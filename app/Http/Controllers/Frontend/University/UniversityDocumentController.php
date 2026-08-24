<?php

namespace App\Http\Controllers\Frontend\University;

use App\Models\UniversitySemesterDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class UniversityDocumentController extends UniversityBaseController
{
    public function store(Request $request, int $semesterRecordId): RedirectResponse
    {
        $this->requireUniversityPermission('create');
        $record = $this->scopedSemesterRecord($semesterRecordId);
        $maxKb = (int) config('university_tracking.max_pdf_kb', 10240);

        $validated = $request->validate([
            'document_type' => ['required', 'in:grade_report,transcript,registration,other'],
            'pdf_file' => ['required', 'file', 'mimetypes:application/pdf', 'mimes:pdf', 'max:' . $maxKb],
        ], [
            'pdf_file.required' => 'กรุณาเลือกไฟล์ PDF',
            'pdf_file.mimes' => 'อนุญาตเฉพาะไฟล์ PDF เท่านั้น',
            'pdf_file.max' => 'ไฟล์ PDF มีขนาดใหญ่เกินกำหนด',
        ]);

        $file = $request->file('pdf_file');
        $originalName = $this->safeOriginalName($file->getClientOriginalName());
        $sha256 = hash_file('sha256', $file->getRealPath());

        $duplicate = UniversitySemesterDocument::query()
            ->where('semester_record_id', $record->id)
            ->where('sha256', $sha256)
            ->exists();

        if ($duplicate) {
            return back()->with('error', 'ไฟล์ PDF นี้ถูกอัปโหลดไว้ในภาคเรียนนี้แล้ว');
        }

        $storedName = Str::uuid()->toString() . '.pdf';
        $directory = sprintf(
            'university-results/%d/%d/semester-%d',
            $record->enrollment->client_id,
            $record->academic_year,
            $record->term
        );
        $path = $file->storeAs($directory, $storedName, 'local');
        abort_unless(is_string($path) && $path !== '', 500, 'ไม่สามารถจัดเก็บไฟล์ PDF ได้');

        try {
            UniversitySemesterDocument::create([
                'semester_record_id' => $record->id,
                'education_record_id' => $record->education_record_id,
                'document_type' => $validated['document_type'],
                'original_name' => $originalName,
                'stored_name' => $storedName,
                'file_path' => $path,
                'mime_type' => 'application/pdf',
                'file_size' => $file->getSize(),
                'sha256' => $sha256,
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now('Asia/Bangkok'),
            ]);
        } catch (Throwable $e) {
            Storage::disk('local')->delete($path);
            throw $e;
        }

        return back()->with('success', 'อัปโหลดเอกสารผลการเรียน PDF เรียบร้อยแล้ว');
    }

    public function view(int $id): BinaryFileResponse
    {
        $this->requireUniversityPermission('view');
        $document = $this->scopedDocument($id);
        abort_unless(Storage::disk('local')->exists($document->file_path), 404, 'ไม่พบไฟล์เอกสาร');

        $response = response()->file(Storage::disk('local')->path($document->file_path), [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
        ]);

        $response->setContentDisposition(
            'inline',
            $this->safeOriginalName($document->original_name),
            'university-result.pdf'
        );

        return $response;
    }

    public function download(int $id): BinaryFileResponse
    {
        $this->requireUniversityPermission('print');
        $document = $this->scopedDocument($id);
        abort_unless(Storage::disk('local')->exists($document->file_path), 404, 'ไม่พบไฟล์เอกสาร');

        return response()->download(
            Storage::disk('local')->path($document->file_path),
            $this->safeOriginalName($document->original_name),
            ['Content-Type' => 'application/pdf']
        );
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->requireUniversityPermission('delete');
        $document = $this->scopedDocument($id);
        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'ลบเอกสาร PDF เรียบร้อยแล้ว');
    }


    private function safeOriginalName(?string $name): string
    {
        $name = basename(str_replace(["\r", "\n", "\0"], '', (string) $name));
        $name = trim($name);

        if ($name === '') {
            return 'university-result.pdf';
        }

        $name = preg_replace('/[\x00-\x1F\x7F]/u', '', $name) ?: 'university-result.pdf';
        $name = mb_substr($name, 0, 240, 'UTF-8');

        if (!str_ends_with(mb_strtolower($name, 'UTF-8'), '.pdf')) {
            $name .= '.pdf';
        }

        return $name;
    }

    private function scopedDocument(int $id): UniversitySemesterDocument
    {
        return UniversitySemesterDocument::query()
            ->whereHas('semesterRecord.enrollment.client', fn ($q) => $q->forUser(auth()->user()))
            ->findOrFail($id);
    }
}
