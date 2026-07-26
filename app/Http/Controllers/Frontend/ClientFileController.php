<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ClientFile;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\CaseActivity;

class ClientFileController extends Controller
{
    public function index($client_id)
    {
        $client = Client::forUser(auth()->user())
            ->with(['files' => function ($query) {
                $query->latest('uploaded_at')->latest('id');
            }])
            ->findOrFail($client_id);

        return view('frontend.client.client_files.index', compact('client'));
    }

    public function create($client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

         $fileTypes = [
        'id_card'                 => 'บัตรประชาชน',
        'house_registration'      => 'ทะเบียนบ้าน',
        'education_certificate'   => 'วุฒิการศึกษา',
        'birth_certificate'       => 'สูติบัตร',

        // เพิ่มได้ตรงนี้
        'police_report'           => 'ใบแจ้งความ',
        'education_document'      => 'เอกสารทางการศึกษา',
        'court_order'             => 'คำสั่งศาล',
        'medical_certificate'     => 'ใบรับรองแพทย์',
        'disability_card'         => 'บัตรประจำตัวคนพิการ',
        'welfare_card'            => 'บัตรสวัสดิการแห่งรัฐ',
        'passport'                => 'หนังสือเดินทาง',
        'photo'                   => 'รูปถ่าย',
        'consent_form'            => 'หนังสือยินยอม',
        'assessment_document'     => 'เอกสารการประเมิน',
        'other'                   => 'อื่น ๆ',
    ];

        return view('frontend.client.client_files.create', compact('client', 'fileTypes'));
    }

   public function store(Request $request, $client_id)
        {
            $client = Client::forUser(auth()->user())->findOrFail($client_id);

           $request->validate([
                'file_type' => 'required|string',
                'file' => 'required|mimes:pdf|max:20480',
            ], [
                'file_type.required' => 'กรุณาเลือกประเภทเอกสาร',
                'file.required'      => 'กรุณาเลือกไฟล์เอกสาร PDF',
                'file.mimes'         => 'อนุญาตให้อัปโหลดเฉพาะไฟล์ PDF เท่านั้น',
                'file.max'           => 'ขนาดไฟล์ต้องไม่เกิน 20 MB',
            ]);

            $uploadedFile = $request->file('file');
            $originalName = $uploadedFile->getClientOriginalName();

            $path = $uploadedFile->store("clients/{$client->id}/{$request->file_type}", 'public');

            ClientFile::create([
                'client_id'   => $client->id,
                'file_type'   => $request->file_type,
                'file_name'   => $originalName,
                'file_path'   => $path,
                'uploaded_at' => now(),
            ]);

                CaseActivity::where('client_id', $client->id)
                    ->where('module', 'client_file')
                    ->delete();

                CaseActivity::record([
                    'client_id'   => $client->id,
                    'module'      => 'client_file',
                    'type'        => 'success',
                    'title'       => 'อัปโหลดเอกสารผู้รับบริการ',
                    'description' => 'อัปโหลดเอกสารประเภท: ' . $request->file_type . ' | ชื่อไฟล์: ' . $originalName,
                    'occurred_at' => now(),
                    'icon'        => 'bi-file-earmark-pdf',
                    'url'         => route('client_files.index', $client->id),
                ]);

            return redirect()
                ->route('client_files.index', $client->id)
                ->with('success', 'อัปโหลดเอกสารเรียบร้อยแล้ว');
        }

    public function view($client_id, $fileId)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $file = ClientFile::where('id', $fileId)
            ->where('client_id', $client->id)
            ->firstOrFail();

        abort_unless(
            $file->file_path && Storage::disk('public')->exists($file->file_path),
            404,
            'ไม่พบไฟล์เอกสาร'
        );

        $absolutePath = Storage::disk('public')->path($file->file_path);
        $safeFileName = rawurlencode($file->file_name ?: 'document.pdf');

        $response = response()->file($absolutePath, [
            'Content-Type'              => 'application/pdf',
            'Content-Disposition'       => "inline; filename=\"document.pdf\"; filename*=UTF-8''{$safeFileName}",
            'Cache-Control'             => 'private, max-age=86400',
            'X-Content-Type-Options'    => 'nosniff',
            'Accept-Ranges'             => 'bytes',
        ]);

        $response->setAutoEtag();
        $response->setAutoLastModified();

        return $response;
    }

    public function download($client_id, $fileId)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $file = ClientFile::where('id', $fileId)
            ->where('client_id', $client->id)
            ->firstOrFail();

        abort_unless(
            $file->file_path && Storage::disk('public')->exists($file->file_path),
            404,
            'ไม่พบไฟล์เอกสาร'
        );

        return Storage::disk('public')->download(
            $file->file_path,
            $file->file_name ?: 'document.pdf',
            [
                'Content-Type'           => 'application/pdf',
                'Cache-Control'          => 'private, max-age=86400',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    public function destroy($client_id, $fileId)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $file = ClientFile::where('id', $fileId)
            ->where('client_id', $client->id)
            ->firstOrFail();

        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        CaseActivity::where('client_id', $client->id)
        ->where('module', 'client_file')
        ->delete();

        $file->delete();

        return redirect()
            ->route('client_files.index', $client->id)
            ->with('success', 'ลบเอกสารเรียบร้อยแล้ว');
    }
}