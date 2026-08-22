<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\HealthcHeckup;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class HealthcHeckupController extends Controller
{
    private const PRIVATE_DOCUMENT_DIRECTORY = 'healthc_heckups';

    /**
     * แสดงหน้าหลัก พร้อมตัวกรองและรายการตรวจสุขภาพ
     */
    public function index(Request $request)
    {
        $this->authorizeRole();

        $filters = $request->validateWithBag(
            'healthcFilter',
            $this->filterRules(),
            $this->filterMessages(),
            $this->filterAttributes()
        );

        if (!empty($filters['client_id'])) {
            $this->findAuthorizedClient((int) $filters['client_id'], 'healthcFilter');
        }

        $clients = Client::forUser(auth()->user())
            ->orderByRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) ASC")
            ->get();

        $hasAnyHealthcHeckups = HealthcHeckup::whereHas('client', function ($query) {
            $query->forUser(auth()->user());
        })->exists();

        $query = $this->authorizedQuery();
        $this->applyFilters($query, $filters);

        $healthcHeckups = $query
            ->paginate(20)
            ->withQueryString();

        return view('frontend.healthc_heckups.index', compact(
            'clients',
            'healthcHeckups',
            'hasAnyHealthcHeckups'
        ));
    }

    /**
     * บันทึกข้อมูลใหม่
     */
    public function store(Request $request)
    {
        $this->authorizeRole();
        $this->normalizeFormInput($request);

        $validated = $request->validateWithBag(
            'healthcForm',
            $this->formRules((int) $request->input('client_id')),
            $this->formMessages(),
            $this->formAttributes()
        );

        $client = $this->findAuthorizedClient((int) $validated['client_id'], 'healthcForm');
        $validated['client_id'] = $client->id;

        $newFilePath = null;

        try {
            if ($request->hasFile('medical_document')) {
                $newFilePath = $this->uploadMedicalDocument(
                    $request->file('medical_document')
                );
            }

            DB::transaction(function () use ($validated, $client, $newFilePath): void {
                HealthcHeckup::create([
                    'client_id'        => $client->id,
                    'checkup_date'     => $validated['checkup_date'],
                    'hospital_name'    => $validated['hospital_name'],
                    'checkup_result'   => $validated['checkup_result'],
                    'abnormal_detail'  => $validated['checkup_result'] === 'abnormal'
                        ? $validated['abnormal_detail']
                        : null,
                    'medical_document' => $newFilePath,
                    'recorded_by'      => Auth::id(),
                ]);

                $this->syncLatestActivity($client->id);
            });
        } catch (Throwable $exception) {
            if ($newFilePath) {
                $this->deleteMedicalDocument($newFilePath);
            }

            throw $exception;
        }

        return redirect()
            ->route('healthc_heckups.index')
            ->with('success', 'บันทึกข้อมูลการตรวจสุขภาพเรียบร้อยแล้ว');
    }

    /**
     * ดึงข้อมูลสำหรับแก้ไขใน Modal
     */
    public function editJson($id)
    {
        $this->authorizeRole();

        $item = $this->findAuthorizedItem($id);

        return response()->json([
            'id'                    => $item->id,
            'client_id'             => $item->client_id,
            'checkup_date'          => optional($item->checkup_date)->format('Y-m-d'),
            'hospital_name'         => $item->hospital_name,
            'checkup_result'        => $item->checkup_result,
            'abnormal_detail'       => $item->abnormal_detail,
            'medical_document_url'  => $this->medicalDocumentUrl($item),
            'medical_document_name' => $item->medical_document
                ? basename($item->medical_document)
                : null,
        ]);
    }

    /**
     * อัปเดตข้อมูล
     */
    public function update(Request $request, $id)
    {
        $this->authorizeRole();

        $item = $this->findAuthorizedItem($id);

        // ป้องกันการแก้ hidden field เพื่อย้ายรายการไปยังผู้รับบริการรายอื่น
        $request->merge([
            'client_id' => $item->client_id,
        ]);

        $this->normalizeFormInput($request);

        $validated = $request->validateWithBag(
            'healthcForm',
            $this->formRules($item->client_id, $item->id),
            $this->formMessages(),
            $this->formAttributes()
        );

        $client = $this->findAuthorizedClient($item->client_id, 'healthcForm');
        $oldFilePath = $item->medical_document;
        $newFilePath = null;

        try {
            if ($request->hasFile('medical_document')) {
                // อัปโหลดไฟล์ใหม่ก่อน และยังไม่ลบไฟล์เดิมจนกว่าฐานข้อมูลจะบันทึกสำเร็จ
                $newFilePath = $this->uploadMedicalDocument(
                    $request->file('medical_document')
                );
            }

            DB::transaction(function () use (
                $item,
                $validated,
                $client,
                $oldFilePath,
                $newFilePath
            ): void {
                $item->update([
                    'client_id'        => $client->id,
                    'checkup_date'     => $validated['checkup_date'],
                    'hospital_name'    => $validated['hospital_name'],
                    'checkup_result'   => $validated['checkup_result'],
                    'abnormal_detail'  => $validated['checkup_result'] === 'abnormal'
                        ? $validated['abnormal_detail']
                        : null,
                    'medical_document' => $newFilePath ?: $oldFilePath,
                    'recorded_by'      => Auth::id(),
                ]);

                $this->syncLatestActivity($client->id);
            });
        } catch (Throwable $exception) {
            if ($newFilePath) {
                $this->deleteMedicalDocument($newFilePath);
            }

            throw $exception;
        }

        if ($newFilePath && $oldFilePath && $oldFilePath !== $newFilePath) {
            $this->deleteMedicalDocument($oldFilePath);
        }

        return redirect()
            ->route('healthc_heckups.index')
            ->with('success', 'แก้ไขข้อมูลการตรวจสุขภาพเรียบร้อยแล้ว');
    }

    /**
     * ลบข้อมูล
     */
    public function destroy($id)
    {
        $this->authorizeRole();

        $item = $this->findAuthorizedItem($id);
        $clientId = $item->client_id;
        $filePath = $item->medical_document;

        DB::transaction(function () use ($item, $clientId): void {
            $item->delete();
            $this->syncLatestActivity($clientId);
        });

        // ลบไฟล์หลังฐานข้อมูลลบสำเร็จ เพื่อไม่ให้ข้อมูลอ้างถึงไฟล์ที่ถูกลบก่อนเวลา
        $this->deleteMedicalDocument($filePath);

        return redirect()
            ->route('healthc_heckups.index')
            ->with('success', 'ลบข้อมูลการตรวจสุขภาพเรียบร้อยแล้ว');
    }

    /**
     * รายงานรวม
     */
    public function report(Request $request)
    {
        $this->authorizeRole();

        $filters = $request->validateWithBag(
            'healthcFilter',
            $this->filterRules(),
            $this->filterMessages(),
            $this->filterAttributes()
        );

        $selectedClient = null;

        if (!empty($filters['client_id'])) {
            $selectedClient = $this->findAuthorizedClient(
                (int) $filters['client_id'],
                'healthcFilter'
            );
        }

        $query = $this->authorizedQuery();
        $this->applyFilters($query, $filters);

        $items = $query->get();

        return view('frontend.healthc_heckups.report', compact(
            'items',
            'filters',
            'selectedClient'
        ));
    }

    /**
     * Query หลักที่จำกัดเฉพาะผู้รับบริการที่ผู้ใช้มีสิทธิ์
     */
    private function authorizedQuery()
    {
        return HealthcHeckup::with(['client', 'recorder'])
            ->whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->latest('checkup_date')
            ->latest('id');
    }

    /**
     * ใช้ตัวกรองกับ Query โดยไม่ทำซ้ำระหว่างหน้าหลักและหน้ารายงาน
     */
    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['client_id'])) {
            $query->where('client_id', (int) $filters['client_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('checkup_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('checkup_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['checkup_result'])) {
            $query->where('checkup_result', $filters['checkup_result']);
        }

        if (!empty($filters['keyword'])) {
            $keyword = trim((string) $filters['keyword']);

            $query->where(function ($subQuery) use ($keyword) {
                $subQuery
                    ->where('hospital_name', 'like', "%{$keyword}%")
                    ->orWhere('abnormal_detail', 'like', "%{$keyword}%")
                    ->orWhereHas('client', function ($clientQuery) use ($keyword) {
                        $clientQuery->where(function ($nameQuery) use ($keyword) {
                            $nameQuery
                                ->where('first_name', 'like', "%{$keyword}%")
                                ->orWhere('last_name', 'like', "%{$keyword}%")
                                ->orWhereRaw(
                                    "CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) LIKE ?",
                                    ["%{$keyword}%"]
                                );
                        });
                    });
            });
        }
    }

    private function filterRules(): array
    {
        $today = now('Asia/Bangkok')->toDateString();

        return [
            'keyword'        => ['nullable', 'string', 'max:255'],
            'client_id'      => ['nullable', 'integer', 'exists:clients,id'],
            'date_from'      => ['nullable', 'date', 'before_or_equal:' . $today],
            'date_to'        => [
                'nullable',
                'date',
                'before_or_equal:' . $today,
                Rule::when(
                    request()->filled('date_from'),
                    ['after_or_equal:date_from']
                ),
            ],
            'checkup_result' => ['nullable', Rule::in(['normal', 'abnormal'])],
        ];
    }

    private function filterMessages(): array
    {
        return [
            'keyword.string'                  => 'คำค้นหาต้องเป็นข้อความ',
            'keyword.max'                     => 'คำค้นหาต้องไม่เกิน 255 ตัวอักษร',
            'client_id.integer'               => 'รหัสผู้รับบริการไม่ถูกต้อง',
            'client_id.exists'                => 'ไม่พบผู้รับบริการที่เลือก',
            'date_from.date'                  => 'รูปแบบวันที่เริ่มต้นไม่ถูกต้อง',
            'date_from.before_or_equal'       => 'วันที่เริ่มต้นต้องไม่เกินวันปัจจุบัน',
            'date_to.date'                    => 'รูปแบบวันที่สิ้นสุดไม่ถูกต้อง',
            'date_to.before_or_equal'         => 'วันที่สิ้นสุดต้องไม่เกินวันปัจจุบัน',
            'date_to.after_or_equal'          => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
            'checkup_result.in'               => 'ผลการตรวจที่เลือกไม่ถูกต้อง',
        ];
    }

    private function filterAttributes(): array
    {
        return [
            'keyword'        => 'คำค้นหา',
            'client_id'      => 'ผู้รับบริการ',
            'date_from'      => 'วันที่เริ่มต้น',
            'date_to'        => 'วันที่สิ้นสุด',
            'checkup_result' => 'ผลการตรวจ',
        ];
    }

    private function formRules(int $clientId, ?int $ignoreId = null): array
    {
        $today = now('Asia/Bangkok')->toDateString();
        $table = (new HealthcHeckup())->getTable();

        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'checkup_date' => [
                'required',
                'date',
                'before_or_equal:' . $today,
                Rule::unique($table, 'checkup_date')
                    ->where(fn ($query) => $query->where('client_id', $clientId))
                    ->ignore($ignoreId),
            ],
            'hospital_name' => ['required', 'string', 'max:255'],
            'checkup_result' => ['required', Rule::in(['normal', 'abnormal'])],
            'abnormal_detail' => [
                Rule::requiredIf(fn () => request('checkup_result') === 'abnormal'),
                'nullable',
                'string',
                'max:3000',
            ],
            'medical_document' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            '_form_context' => ['nullable', Rule::in(['healthc_create', 'healthc_edit'])],
            '_edit_id' => ['nullable', 'integer'],
        ];
    }

    private function formMessages(): array
    {
        return [
            'client_id.required'              => 'กรุณาเลือกผู้รับบริการ',
            'client_id.integer'               => 'รหัสผู้รับบริการไม่ถูกต้อง',
            'client_id.exists'                => 'ไม่พบผู้รับบริการที่เลือก',
            'checkup_date.required'           => 'กรุณาระบุวันที่ตรวจสุขภาพ',
            'checkup_date.date'               => 'รูปแบบวันที่ตรวจสุขภาพไม่ถูกต้อง',
            'checkup_date.before_or_equal'    => 'วันที่ตรวจสุขภาพต้องไม่เกินวันปัจจุบัน',
            'checkup_date.unique'             => 'ผู้รับบริการรายนี้มีข้อมูลการตรวจสุขภาพในวันที่ดังกล่าวแล้ว',
            'hospital_name.required'          => 'กรุณาระบุชื่อสถานพยาบาล',
            'hospital_name.string'            => 'ชื่อสถานพยาบาลต้องเป็นข้อความ',
            'hospital_name.max'               => 'ชื่อสถานพยาบาลต้องไม่เกิน 255 ตัวอักษร',
            'checkup_result.required'         => 'กรุณาเลือกผลการตรวจสุขภาพ',
            'checkup_result.in'               => 'ผลการตรวจสุขภาพที่เลือกไม่ถูกต้อง',
            'abnormal_detail.required'        => 'กรุณาระบุรายละเอียดผลตรวจที่ผิดปกติ',
            'abnormal_detail.string'          => 'รายละเอียดผลตรวจที่ผิดปกติต้องเป็นข้อความ',
            'abnormal_detail.max'             => 'รายละเอียดผลตรวจที่ผิดปกติต้องไม่เกิน 3,000 ตัวอักษร',
            'medical_document.file'           => 'เอกสารทางการแพทย์ต้องเป็นไฟล์',
            'medical_document.uploaded'       => 'อัปโหลดเอกสารทางการแพทย์ไม่สำเร็จ กรุณาตรวจสอบขนาดไฟล์แล้วลองใหม่',
            'medical_document.mimes'          => 'เอกสารทางการแพทย์ต้องเป็นไฟล์ PDF เท่านั้น',
            'medical_document.max'            => 'เอกสารทางการแพทย์ต้องมีขนาดไม่เกิน 5 MB',
            '_form_context.in'                => 'ข้อมูลบริบทของแบบฟอร์มไม่ถูกต้อง',
            '_edit_id.integer'                => 'รหัสรายการที่แก้ไขไม่ถูกต้อง',
        ];
    }

    private function formAttributes(): array
    {
        return [
            'client_id'         => 'ผู้รับบริการ',
            'checkup_date'      => 'วันที่ตรวจสุขภาพ',
            'hospital_name'     => 'สถานพยาบาล',
            'checkup_result'    => 'ผลการตรวจสุขภาพ',
            'abnormal_detail'   => 'รายละเอียดผลตรวจที่ผิดปกติ',
            'medical_document'  => 'เอกสารทางการแพทย์',
        ];
    }

    private function normalizeFormInput(Request $request): void
    {
        $request->merge([
            'hospital_name' => trim((string) $request->input('hospital_name')),
            'abnormal_detail' => $this->nullableTrim($request->input('abnormal_detail')),
        ]);
    }

    private function nullableTrim(mixed $value): ?string
    {
        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function uploadMedicalDocument(UploadedFile $file): string
    {
        if (!$this->hasPdfSignature($file->getRealPath())) {
            throw ValidationException::withMessages([
                'medical_document' => 'ไฟล์เอกสารทางการแพทย์ไม่ใช่เอกสาร PDF ที่ถูกต้อง',
            ])->errorBag('healthcForm');
        }

        $filename = now('Asia/Bangkok')->format('YmdHis')
            . '_'
            . Str::uuid()->toString()
            . '.pdf';

        $relativePath = self::PRIVATE_DOCUMENT_DIRECTORY . '/' . $filename;
        $absolutePath = storage_path('app/private/' . $relativePath);

        File::ensureDirectoryExists(dirname($absolutePath), 0755, true);
        $file->move(dirname($absolutePath), basename($absolutePath));

        return $relativePath;
    }

    private function normalizeMedicalDocumentPrivatePath(?string $filePath): ?string
    {
        if (!$filePath) {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', trim($filePath)), '/');

        if ($normalized === '' || str_contains($normalized, '../') || str_contains($normalized, "\0")) {
            return null;
        }

        foreach ([
            'upload/healthc_heckups/',
            'storage/healthc_heckups/',
            'healthc_heckups/',
        ] as $prefix) {
            if (!str_starts_with($normalized, $prefix)) {
                continue;
            }

            $relative = ltrim(substr($normalized, strlen($prefix)), '/');

            if ($relative === '' || str_contains($relative, '../') || str_contains($relative, "\0")) {
                return null;
            }

            return self::PRIVATE_DOCUMENT_DIRECTORY . '/' . $relative;
        }

        return null;
    }

    private function resolveMedicalDocumentPath(?string $filePath): ?string
    {
        $privatePath = $this->normalizeMedicalDocumentPrivatePath($filePath);

        if (!$privatePath) {
            return null;
        }

        $absolutePath = storage_path('app/private/' . $privatePath);

        return File::isFile($absolutePath) ? $absolutePath : null;
    }

    private function deleteMedicalDocument(?string $filePath): void
    {
        $absolutePath = $this->resolveMedicalDocumentPath($filePath);

        if ($absolutePath) {
            File::delete($absolutePath);
        }
    }

    private function medicalDocumentUrl(HealthcHeckup $item): ?string
    {
        if (!$item->medical_document) {
            return null;
        }

        return route('healthc_heckups.document.view', $item->id);
    }

    /**
     * เปิดเอกสารตรวจสุขภาพผ่าน Laravel เท่านั้น
     */
    public function viewMedicalDocument($id)
    {
        $this->authorizeRole();

        $user = auth()->user();
        abort_unless($user && $user->hasFormPermission('health_annual_checkup', 'view'), 403);

        $item = $this->findAuthorizedItem($id);
        $absolutePath = $this->resolveMedicalDocumentPath($item->medical_document);

        abort_unless($absolutePath, 404, 'ไม่พบเอกสารทางการแพทย์');
        abort_unless($this->hasPdfSignature($absolutePath), 404);

        return response()->file($absolutePath, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
        ]);
    }

    private function hasPdfSignature(string|false $path): bool
    {
        if (!$path || !is_readable($path)) {
            return false;
        }

        $handle = fopen($path, 'rb');

        if (!$handle) {
            return false;
        }

        try {
            return str_contains((string) fread($handle, 1024), '%PDF-');
        } finally {
            fclose($handle);
        }
    }

    /**
     * ให้ CaseActivity สะท้อนรายการล่าสุดที่ยังมีอยู่จริงของผู้รับบริการ
     */
    private function syncLatestActivity(int $clientId): void
    {
        CaseActivity::where('client_id', $clientId)
            ->where('module', 'healthc_heckup')
            ->delete();

        $latest = HealthcHeckup::where('client_id', $clientId)
            ->orderByDesc('checkup_date')
            ->orderByDesc('id')
            ->first();

        if (!$latest) {
            return;
        }

        CaseActivity::record([
            'client_id'   => $clientId,
            'module'      => 'healthc_heckup',
            'type'        => $latest->checkup_result === 'abnormal' ? 'warning' : 'success',
            'title'       => 'การตรวจสุขภาพประจำปีล่าสุด',
            'description' => 'วันที่ตรวจ: ' . ($latest->checkup_date?->format('Y-m-d') ?? '-')
                . ' | สถานพยาบาล: ' . ($latest->hospital_name ?: '-')
                . ' | ผลตรวจ: ' . ($latest->checkup_result === 'abnormal' ? 'ผิดปกติ' : 'ปกติ'),
            'occurred_at' => $latest->checkup_date ?? now('Asia/Bangkok'),
            'icon'        => 'bi-clipboard2-heart',
            'url'         => route('healthc_heckups.index', ['client_id' => $clientId]),
        ]);
    }

    private function authorizeRole(): void
    {
        // สิทธิ์รายฟอร์ม health_annual_checkup ถูกตรวจโดย EnforceFormPermission
        // Route นี้มี auth middleware อยู่แล้ว จึงตรวจเพียงสถานะการเข้าสู่ระบบซ้ำแบบ defensive
        abort_unless(auth()->check(), 401, 'Unauthenticated.');
    }

    private function findAuthorizedClient(int $clientId, string $errorBag): Client
    {
        $client = Client::forUser(auth()->user())->find($clientId);

        if (!$client) {
            throw ValidationException::withMessages([
                'client_id' => 'ผู้รับบริการที่เลือกไม่อยู่ในขอบเขตสิทธิ์ของคุณ',
            ])->errorBag($errorBag);
        }

        return $client;
    }

    private function findAuthorizedItem($id): HealthcHeckup
    {
        return HealthcHeckup::with(['client', 'recorder'])
            ->whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->findOrFail($id);
    }
}
