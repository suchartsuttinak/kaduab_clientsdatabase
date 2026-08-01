<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\Psycho;
use App\Models\Psychiatric;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PsychiatricController extends Controller
{
    /**
     * แสดงรายการและแบบฟอร์มบันทึกข้อมูลจิตเวช
     */
    public function AddPsychiatric(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $filters = $this->validateDateFilters($request);

        $psycho = Psycho::query()
            ->orderBy('psycho_name')
            ->get(['id', 'psycho_name']);

        $psychiatrics = $this->psychiatricQuery($client->id, $filters)
            ->get();

        $psychiatric = null;
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        return view('frontend.client.psychiatric.psychiatric_create', compact(
            'client',
            'client_id',
            'psychiatrics',
            'psychiatric',
            'psycho',
            'startDate',
            'endDate'
        ));
    }

    /**
     * บันทึกข้อมูลใหม่
     */
    public function StorePsychiatric(Request $request)
    {
        $this->normalizeRequest($request);

        $clientId = Validator::make($request->all(), [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
        ], [
            'client_id.required' => 'กรุณาเลือกผู้รับบริการ',
            'client_id.integer' => 'รหัสผู้รับบริการไม่ถูกต้อง',
            'client_id.exists' => 'ผู้รับบริการที่เลือกไม่ถูกต้อง',
        ])->validate()['client_id'];

        $client = Client::forUser(auth()->user())->findOrFail($clientId);
        $data = $this->validatePsychiatric($request, $client->id);
        $data['client_id'] = $client->id;
        unset($data['_form_context'], $data['_edit_id']);

        DB::transaction(function () use ($data, $client): void {
            Psychiatric::create($data);
            $this->refreshCaseActivity($client->id);
        });

        return redirect()
            ->route('psychiatric.create', $client->id)
            ->with([
                'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * โหลดข้อมูลสำหรับ Modal แก้ไข
     */
    public function EditPsychiatricJson($id)
    {
        $psychiatric = $this->authorizedPsychiatric($id);

        return response()->json([
            'id' => $psychiatric->id,
            'sent_date' => $psychiatric->sent_date ? Carbon::parse($psychiatric->sent_date)->format('Y-m-d') : null,
            'hotpital' => $psychiatric->hotpital,
            'psycho_id' => $psychiatric->psycho_id,
            'diagnose' => $psychiatric->diagnose,
            'appoin_date' => $psychiatric->appoin_date ? Carbon::parse($psychiatric->appoin_date)->format('Y-m-d') : null,
            'drug_no' => $psychiatric->drug_no,
            'drug_name' => $psychiatric->drug_name,
            'disa_no' => $psychiatric->disa_no,
            'client_id' => $psychiatric->client_id,
        ]);
    }

    /**
     * อัปเดตข้อมูล
     */
    public function UpdatePsychiatric(Request $request, $id)
    {
        $psychiatric = $this->authorizedPsychiatric($id);

        // ห้ามย้ายรายการไปยังผู้รับบริการรายอื่นจาก hidden input
        $request->merge([
            'client_id' => $psychiatric->client_id,
        ]);

        $this->normalizeRequest($request);
        $data = $this->validatePsychiatric(
            $request,
            $psychiatric->client_id,
            $psychiatric->id
        );
        $data['client_id'] = $psychiatric->client_id;
        unset($data['_form_context'], $data['_edit_id']);

        DB::transaction(function () use ($psychiatric, $data): void {
            $psychiatric->update($data);
            $this->refreshCaseActivity($psychiatric->client_id);
        });

        return redirect()
            ->route('psychiatric.create', $psychiatric->client_id)
            ->with([
                'message' => 'อัปเดตข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * ลบข้อมูล
     */
    public function DeletePsychiatric($id)
    {
        $psychiatric = $this->authorizedPsychiatric($id);
        $clientId = $psychiatric->client_id;

        DB::transaction(function () use ($psychiatric, $clientId): void {
            $psychiatric->delete();
            $this->refreshCaseActivity($clientId);
        });

        return redirect()
            ->route('psychiatric.create', $clientId)
            ->with([
                'message' => 'ลบข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * รายงานข้อมูลจิตเวช
     */
    public function ReportPsychiatric(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $filters = $this->validateDateFilters($request);

        $psychiatrics = $this->psychiatricQuery($client->id, $filters)
            ->get();

        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        return view('frontend.client.psychiatric.psychiatric_report', compact(
            'client',
            'client_id',
            'psychiatrics',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Query กลางสำหรับหน้ารายการและรายงาน
     */
    private function psychiatricQuery(int $clientId, array $filters = []): Builder
    {
        return Psychiatric::query()
            ->where('client_id', $clientId)
            ->with(['psycho:id,psycho_name'])
            ->when(
                !empty($filters['start_date']),
                fn (Builder $query) => $query->whereDate('sent_date', '>=', $filters['start_date'])
            )
            ->when(
                !empty($filters['end_date']),
                fn (Builder $query) => $query->whereDate('sent_date', '<=', $filters['end_date'])
            )
            ->orderByDesc('sent_date')
            ->orderByDesc('id');
    }

    /**
     * ตรวจสอบวันที่ค้นหา
     */
    private function validateDateFilters(Request $request): array
    {
        $today = now('Asia/Bangkok')->toDateString();

        return $request->validate([
            'start_date' => ['nullable', 'date', 'before_or_equal:' . $today],
            'end_date' => [
                'nullable',
                'date',
                'before_or_equal:' . $today,
                'after_or_equal:start_date',
            ],
        ], [
            'start_date.date' => 'วันที่เริ่มต้นไม่ถูกต้อง',
            'start_date.before_or_equal' => 'วันที่เริ่มต้นต้องไม่เกินวันปัจจุบัน',
            'end_date.date' => 'วันที่สิ้นสุดไม่ถูกต้อง',
            'end_date.before_or_equal' => 'วันที่สิ้นสุดต้องไม่เกินวันปัจจุบัน',
            'end_date.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
        ]);
    }

    /**
     * Validation กลางของหน้าเพิ่มและแก้ไข
     */
    private function validatePsychiatric(
        Request $request,
        int $clientId,
        ?int $ignoreId = null
    ): array {
        $today = now('Asia/Bangkok')->toDateString();
        $uniqueSentDate = Rule::unique('psychiatrics', 'sent_date')
            ->where(fn ($query) => $query->where('client_id', $clientId));

        if ($ignoreId !== null) {
            $uniqueSentDate->ignore($ignoreId);
        }

        return $request->validate([
            'client_id' => ['required', 'integer', Rule::in([$clientId])],
            'sent_date' => [
                'required',
                'date',
                'before_or_equal:' . $today,
                $uniqueSentDate,
            ],
            'hotpital' => ['required', 'string', 'max:255'],
            'psycho_id' => ['required', 'integer', 'exists:psychos,id'],
            'diagnose' => ['nullable', 'string', 'max:3000'],
            'appoin_date' => ['nullable', 'date', 'after_or_equal:sent_date'],
            'drug_no' => ['required', Rule::in(['yes', 'no'])],
            'drug_name' => [
                Rule::requiredIf(fn () => $request->input('drug_no') === 'yes'),
                'nullable',
                'string',
                'max:255',
            ],
            'disa_no' => ['required', Rule::in(['yes', 'no'])],
            '_form_context' => ['nullable', Rule::in(['psychiatric_create', 'psychiatric_edit'])],
            '_edit_id' => ['nullable', 'integer'],
        ], [
            'client_id.required' => 'กรุณาเลือกผู้รับบริการ',
            'client_id.integer' => 'รหัสผู้รับบริการไม่ถูกต้อง',
            'client_id.in' => 'ไม่สามารถเปลี่ยนผู้รับบริการของรายการนี้ได้',
            'sent_date.required' => 'กรุณาระบุวันที่ส่งตรวจ',
            'sent_date.date' => 'รูปแบบวันที่ส่งตรวจไม่ถูกต้อง',
            'sent_date.before_or_equal' => 'วันที่ส่งตรวจต้องไม่เกินวันปัจจุบัน',
            'sent_date.unique' => 'วันที่ส่งตรวจนี้ถูกบันทึกแล้วสำหรับผู้รับบริการคนนี้',
            'hotpital.required' => 'กรุณาระบุชื่อสถานพยาบาล',
            'hotpital.string' => 'ชื่อสถานพยาบาลต้องเป็นข้อความ',
            'hotpital.max' => 'ชื่อสถานพยาบาลต้องไม่เกิน 255 ตัวอักษร',
            'psycho_id.required' => 'กรุณาเลือกผลการตรวจวินิจฉัย',
            'psycho_id.integer' => 'ผลการตรวจวินิจฉัยไม่ถูกต้อง',
            'psycho_id.exists' => 'ผลการตรวจวินิจฉัยที่เลือกไม่ถูกต้อง',
            'diagnose.string' => 'สรุปผลการตรวจต้องเป็นข้อความ',
            'diagnose.max' => 'สรุปผลการตรวจต้องไม่เกิน 3,000 ตัวอักษร',
            'appoin_date.date' => 'รูปแบบวันที่นัดครั้งต่อไปไม่ถูกต้อง',
            'appoin_date.after_or_equal' => 'วันที่นัดครั้งต่อไปต้องไม่น้อยกว่าวันที่ส่งตรวจ',
            'drug_no.required' => 'กรุณาระบุการรับยา',
            'drug_no.in' => 'ข้อมูลการรับยาไม่ถูกต้อง',
            'drug_name.required' => 'กรุณาระบุชื่อยาเมื่อเลือกว่ารับยา',
            'drug_name.string' => 'ชื่อยาต้องเป็นข้อความ',
            'drug_name.max' => 'ชื่อยาต้องไม่เกิน 255 ตัวอักษร',
            'disa_no.required' => 'กรุณาระบุการขึ้นทะเบียนคนพิการ',
            'disa_no.in' => 'ข้อมูลการขึ้นทะเบียนคนพิการไม่ถูกต้อง',
        ]);
    }

    /**
     * จัดรูปแบบข้อมูลก่อน Validation และบันทึก
     */
    private function normalizeRequest(Request $request): void
    {
        $nullableTextFields = ['diagnose', 'drug_name'];
        $normalized = [
            'hotpital' => trim((string) $request->input('hotpital', '')),
        ];

        foreach ($nullableTextFields as $field) {
            $value = trim((string) $request->input($field, ''));
            $normalized[$field] = $value === '' ? null : $value;
        }

        if ($request->input('drug_no') !== 'yes') {
            $normalized['drug_name'] = null;
        }

        if ($request->input('appoin_date') === '') {
            $normalized['appoin_date'] = null;
        }

        $request->merge($normalized);
    }

    /**
     * ดึงรายการโดยตรวจสิทธิ์เจ้าของผู้รับบริการตั้งแต่ Query แรก
     */
    private function authorizedPsychiatric($id): Psychiatric
    {
        return Psychiatric::query()
            ->whereKey($id)
            ->whereHas('client', function ($query): void {
                $query->forUser(auth()->user());
            })
            ->firstOrFail();
    }

    /**
     * ให้ CaseActivity อ้างอิงรายการล่าสุดที่ยังมีอยู่จริงเสมอ
     */
    private function refreshCaseActivity(int $clientId): void
    {
        CaseActivity::query()
            ->where('client_id', $clientId)
            ->where('module', 'psychiatric')
            ->delete();

        $latest = Psychiatric::query()
            ->where('client_id', $clientId)
            ->orderByDesc('sent_date')
            ->orderByDesc('id')
            ->first();

        if (!$latest) {
            return;
        }

        CaseActivity::record([
            'client_id' => $clientId,
            'module' => 'psychiatric',
            'type' => 'success',
            'title' => 'ข้อมูลการวินิจฉัยทางจิตเวชล่าสุด',
            'description' =>
                'วันที่ส่งตรวจ: ' . ($latest->sent_date ? Carbon::parse($latest->sent_date)->format('Y-m-d') : '-') .
                ' | ส่งตรวจที่: ' . ($latest->hotpital ?: '-') .
                ' | การใช้ยา: ' . ($latest->drug_no === 'yes' ? 'รับยา' : 'ไม่รับยา'),
            'occurred_at' => $latest->sent_date ?? now('Asia/Bangkok'),
            'icon' => 'bi-hospital',
            'url' => route('psychiatric.create', $clientId),
        ]);
    }
}
