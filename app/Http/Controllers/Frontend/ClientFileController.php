<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\ClientFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Throwable;

class ClientFileController extends Controller
{
    private const PRIVATE_DISK = 'local';
    private const MAX_FILE_SIZE_KB = 20480;

    private const FILE_TYPES = [
        'id_card'               => 'บัตรประชาชน',
        'house_registration'    => 'ทะเบียนบ้าน',
        'education_certificate' => 'วุฒิการศึกษา',
        'birth_certificate'     => 'สูติบัตร',
        'police_report'         => 'ใบแจ้งความ',
        'education_document'    => 'เอกสารทางการศึกษา',
        'court_order'           => 'คำสั่งศาล',
        'medical_certificate'   => 'ใบรับรองแพทย์',
        'disability_card'       => 'บัตรประจำตัวคนพิการ',
        'welfare_card'          => 'บัตรสวัสดิการแห่งรัฐ',
        'passport'              => 'หนังสือเดินทาง',
        'photo'                 => 'รูปถ่าย',
        'consent_form'          => 'หนังสือยินยอม',
        'assessment_document'   => 'เอกสารการประเมิน',
        'other'                 => 'อื่น ๆ',
    ];

    public function index($client_id)
    {
        $client = Client::forUser(auth()->user())
            ->with([
                'files' => fn ($query) => $query
                    ->latest('uploaded_at')
                    ->latest('id'),
            ])
            ->findOrFail($client_id);

        return view('frontend.client.client_files.index', [
            'client' => $client,
            'fileTypes' => self::FILE_TYPES,
        ]);
    }

    public function create($client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        return view('frontend.client.client_files.create', [
            'client' => $client,
            'fileTypes' => self::FILE_TYPES,
            'maxFileSizeMb' => (int) (self::MAX_FILE_SIZE_KB / 1024),
        ]);
    }

    public function store(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $validated = $request->validate([
            'file_type' => [
                'required',
                'string',
                Rule::in(array_keys(self::FILE_TYPES)),
            ],
            'file' => [
                'required',
                'file',
                'mimes:pdf',
                'max:' . self::MAX_FILE_SIZE_KB,
            ],
        ], [
            'file_type.required' => 'กรุณาเลือกประเภทเอกสาร',
            'file_type.in'       => 'ประเภทเอกสารที่เลือกไม่ถูกต้อง',
            'file.required'      => 'กรุณาเลือกไฟล์เอกสาร PDF',
            'file.file'          => 'ไฟล์ที่เลือกไม่ถูกต้อง',
            'file.mimes'         => 'อนุญาตให้อัปโหลดเฉพาะไฟล์ PDF เท่านั้น',
            'file.max'           => 'ขนาดไฟล์ต้องไม่เกิน 20 MB',
        ]);

        $uploadedFile = $request->file('file');

        if (!$uploadedFile || !$this->hasPdfSignature($uploadedFile->getRealPath())) {
            return back()
                ->withErrors(['file' => 'ไฟล์ที่เลือกไม่ใช่เอกสาร PDF ที่ถูกต้อง'])
                ->withInput();
        }

        $originalName = $this->sanitizeOriginalName(
            $uploadedFile->getClientOriginalName()
        );

        $directory = sprintf(
            'clients/%d/%s',
            $client->id,
            $validated['file_type']
        );

        $storedName = Str::uuid()->toString() . '.pdf';
        $storedPath = null;

        try {
            $storedPath = $uploadedFile->storeAs(
                $directory,
                $storedName,
                self::PRIVATE_DISK
            );

            if (!$storedPath) {
                throw new \RuntimeException('ไม่สามารถบันทึกไฟล์ลงพื้นที่จัดเก็บได้');
            }

            DB::transaction(function () use (
                $client,
                $validated,
                $originalName,
                $storedPath
            ): void {
                ClientFile::create([
                    'client_id'   => $client->id,
                    'file_type'   => $validated['file_type'],
                    'file_name'   => $originalName,
                    'file_path'   => $storedPath,
                    'uploaded_at' => now('Asia/Bangkok'),
                ]);

                $this->syncLatestActivity($client->id);
            });
        } catch (Throwable $exception) {
            if ($storedPath && Storage::disk(self::PRIVATE_DISK)->exists($storedPath)) {
                Storage::disk(self::PRIVATE_DISK)->delete($storedPath);
            }

            report($exception);

            return back()
                ->withInput()
                ->with('error', 'ไม่สามารถอัปโหลดเอกสารได้ กรุณาลองใหม่อีกครั้ง');
        }

        return redirect()
            ->route('client_files.index', $client->id)
            ->with('success', 'อัปโหลดเอกสารเรียบร้อยแล้ว');
    }

    public function view($client_id, $file)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $clientFile = $this->findClientFile($client->id, $file);
        $location = $this->resolveFileLocation($clientFile);

        abort_unless($location, 404, 'ไม่พบไฟล์เอกสาร');

        $fileName = $this->sanitizeDownloadName(
            $clientFile->file_name ?: 'document.pdf'
        );

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_INLINE,
            $fileName,
            'document.pdf'
        );

        return response()->file($location['absolute_path'], [
            'Content-Type'           => 'application/pdf',
            'Content-Disposition'    => $disposition,
            'Cache-Control'          => 'private, no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'                 => 'no-cache',
            'Expires'                => '0',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options'        => 'SAMEORIGIN',
            'Accept-Ranges'          => 'bytes',
        ]);
    }

    public function download($client_id, $file)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $clientFile = $this->findClientFile($client->id, $file);
        $location = $this->resolveFileLocation($clientFile);

        abort_unless($location, 404, 'ไม่พบไฟล์เอกสาร');

        return response()->download(
            $location['absolute_path'],
            $this->sanitizeDownloadName($clientFile->file_name ?: 'document.pdf'),
            [
                'Content-Type'           => 'application/pdf',
                'Cache-Control'          => 'private, no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'                 => 'no-cache',
                'Expires'                => '0',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    public function destroy($client_id, $file)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $clientFile = $this->findClientFile($client->id, $file);
        $location = $this->resolveFileLocation($clientFile);

        DB::transaction(function () use ($client, $clientFile): void {
            $clientFile->delete();
            $this->syncLatestActivity($client->id);
        });

        if ($location) {
            try {
                Storage::disk($location['disk'])->delete($location['path']);
            } catch (Throwable $exception) {
                Log::warning('ลบไฟล์เอกสารผู้รับบริการไม่สำเร็จ', [
                    'client_id' => $client->id,
                    'client_file_id' => $clientFile->id,
                    'disk' => $location['disk'],
                    'path' => $location['path'],
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('client_files.index', $client->id)
            ->with('success', 'ลบเอกสารเรียบร้อยแล้ว');
    }

    private function findClientFile(int $clientId, int|string $fileId): ClientFile
    {
        return ClientFile::query()
            ->whereKey($fileId)
            ->where('client_id', $clientId)
            ->firstOrFail();
    }

    private function syncLatestActivity(int $clientId): void
    {
        CaseActivity::query()
            ->where('client_id', $clientId)
            ->where('module', 'client_file')
            ->delete();

        $latestFile = ClientFile::query()
            ->where('client_id', $clientId)
            ->latest('uploaded_at')
            ->latest('id')
            ->first();

        if (!$latestFile) {
            return;
        }

        $typeLabel = self::FILE_TYPES[$latestFile->file_type]
            ?? $latestFile->file_type
            ?? 'ไม่ระบุประเภท';

        CaseActivity::record([
            'client_id'   => $clientId,
            'module'      => 'client_file',
            'type'        => 'success',
            'title'       => 'เอกสารผู้รับบริการล่าสุด',
            'description' => 'ประเภท: ' . $typeLabel
                . ' | ชื่อไฟล์: ' . ($latestFile->file_name ?: 'document.pdf'),
            'occurred_at' => $latestFile->uploaded_at ?: now('Asia/Bangkok'),
            'icon'        => 'bi-file-earmark-pdf',
            'url'         => route('client_files.index', $clientId),
        ]);
    }

    private function resolveFileLocation(ClientFile $file): ?array
    {
        $path = $this->normalizeStoredPath($file->file_path);

        if (!$path) {
            return null;
        }

        foreach ([self::PRIVATE_DISK] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return [
                    'disk' => $disk,
                    'path' => $path,
                    'absolute_path' => Storage::disk($disk)->path($path),
                ];
            }
        }

        return null;
    }

    private function normalizeStoredPath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $path = str_replace('\\', '/', trim($path));
        $path = ltrim($path, '/');

        foreach (['storage/', 'public/'] as $prefix) {
            if (Str::startsWith($path, $prefix)) {
                $path = Str::after($path, $prefix);
            }
        }

        if ($path === '' || Str::contains($path, ['../', "\0"])) {
            return null;
        }

        return $path;
    }

    private function hasPdfSignature(string|false $realPath): bool
    {
        if (!$realPath || !is_readable($realPath)) {
            return false;
        }

        $handle = fopen($realPath, 'rb');

        if (!$handle) {
            return false;
        }

        try {
            return str_contains((string) fread($handle, 1024), '%PDF-');
        } finally {
            fclose($handle);
        }
    }

    private function sanitizeOriginalName(string $name): string
    {
        $name = preg_replace('/[\x00-\x1F\x7F]/u', '', $name) ?: 'document.pdf';
        $name = trim(str_replace(['/', '\\'], '-', $name));
        $name = Str::limit($name, 240, '');

        if (!Str::endsWith(Str::lower($name), '.pdf')) {
            $name .= '.pdf';
        }

        return $name ?: 'document.pdf';
    }

    private function sanitizeDownloadName(string $name): string
    {
        return $this->sanitizeOriginalName($name);
    }
}
