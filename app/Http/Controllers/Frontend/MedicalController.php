<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\Medical;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MedicalController extends Controller
{
    /**
     * แสดงรายการและตัวกรองข้อมูลการรักษาพยาบาล
     */
    public function MedicalAdd(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $filters = $this->validateDateFilters($request);

        $baseQuery = Medical::query()
            ->where('client_id', $client->id);

        // ใช้แยกกรณี “ยังไม่มีข้อมูลเลย” กับ “กรองแล้วไม่พบข้อมูล”
        $hasMedicalRecords = (clone $baseQuery)->exists();

        $medicals = $baseQuery
            ->when(
                !empty($filters['start_date']),
                fn ($query) => $query->where('medical_date', '>=', $filters['start_date'])
            )
            ->when(
                !empty($filters['end_date']),
                fn ($query) => $query->where('medical_date', '<=', $filters['end_date'])
            )
            ->orderByDesc('medical_date')
            ->orderByDesc('id')
            ->get();

        return view('frontend.client.medical.medical_create', [
            'client'            => $client,
            'client_id'         => $client->id,
            'medicals'          => $medicals,
            'hasMedicalRecords' => $hasMedicalRecords,
        ]);
    }

    /**
     * บันทึกข้อมูลใหม่
     */
    public function MedicalStore(Request $request)
    {
        $clientInput = $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
        ], $this->validationMessages());

        $client = Client::forUser(auth()->user())
            ->findOrFail($clientInput['client_id']);

        $request->merge(['client_id' => $client->id]);
        $this->normalizeMedicalInput($request);

        $data = $request->validate(
            $this->medicalRules($client->id),
            $this->validationMessages()
        );

        $data = $this->cleanConditionalFields($data);

        DB::transaction(function () use ($data, $client): void {
            Medical::create($data);
            $this->syncLatestMedicalActivity($client->id);
        });

        return redirect()
            ->route('medical.add', $client->id)
            ->with([
                'message'    => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * โหลดข้อมูลสำหรับ Modal แก้ไข
     */
    public function editMedicalJson($id)
    {
        $medical = $this->findAuthorizedMedical($id);

        return response()->json([
            'id'           => $medical->id,
            'medical_date' => $this->formatDateForInput($medical->medical_date),
            'disease_name' => $medical->disease_name,
            'illness'      => $medical->illness,
            'treatment'    => $medical->treatment,
            'refer'        => $medical->refer,
            'diagnosis'    => $medical->diagnosis,
            'appt_date'    => $this->formatDateForInput($medical->appt_date),
            'teacher'      => $medical->teacher,
            'remark'       => $medical->remark,
            'client_id'    => $medical->client_id,
        ]);
    }

    /**
     * อัปเดตข้อมูล
     */
    public function MedicalUpdate(Request $request, $id)
    {
        $medical = $this->findAuthorizedMedical($id);

        // ห้ามเปลี่ยนเจ้าของรายการด้วยการแก้ hidden input
        $request->merge(['client_id' => $medical->client_id]);
        $this->normalizeMedicalInput($request);

        $validator = Validator::make(
            $request->all(),
            $this->medicalRules($medical->client_id, $medical->id),
            $this->validationMessages()
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with([
                    'edit_mode' => true,
                    'edit_id'   => $medical->id,
                ]);
        }

        $data = $this->cleanConditionalFields($validator->validated());

        DB::transaction(function () use ($medical, $data): void {
            $medical->update($data);
            $this->syncLatestMedicalActivity($medical->client_id);
        });

        return redirect()
            ->route('medical.add', $medical->client_id)
            ->with([
                'message'    => 'อัปเดตข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * ลบข้อมูล
     */
    public function MedicalDelete($id)
    {
        $medical = $this->findAuthorizedMedical($id);
        $clientId = $medical->client_id;

        DB::transaction(function () use ($medical, $clientId): void {
            $medical->delete();
            $this->syncLatestMedicalActivity($clientId);
        });

        return redirect()
            ->route('medical.add', $clientId)
            ->with([
                'message'    => 'ลบข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * รายงานตามช่วงวันที่
     */
    public function MedicalReport(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $filters = $this->validateDateFilters($request);

        $medicals = Medical::query()
            ->where('client_id', $client->id)
            ->when(
                !empty($filters['start_date']),
                fn ($query) => $query->where('medical_date', '>=', $filters['start_date'])
            )
            ->when(
                !empty($filters['end_date']),
                fn ($query) => $query->where('medical_date', '<=', $filters['end_date'])
            )
            ->orderByDesc('medical_date')
            ->orderByDesc('id')
            ->get();

        return view('frontend.client.medical.report', [
            'client'   => $client,
            'medicals' => $medicals,
        ]);
    }

    private function findAuthorizedMedical($id): Medical
    {
        return Medical::query()
            ->whereKey($id)
            ->whereHas('client', function ($query): void {
                $query->forUser(auth()->user());
            })
            ->firstOrFail();
    }

    private function validateDateFilters(Request $request): array
    {
        $today = now('Asia/Bangkok')->toDateString();

        $endDateRules = [
            'nullable',
            'date',
            'before_or_equal:' . $today,
        ];

        if ($request->filled('start_date')) {
            $endDateRules[] = 'after_or_equal:start_date';
        }

        return $request->validate([
            'start_date' => ['nullable', 'date', 'before_or_equal:' . $today],
            'end_date'   => $endDateRules,
        ], [
            'start_date.date'            => 'วันที่เริ่มต้นไม่ถูกต้อง',
            'start_date.before_or_equal' => 'วันที่เริ่มต้นต้องไม่เกินวันปัจจุบัน',
            'end_date.date'              => 'วันที่สิ้นสุดไม่ถูกต้อง',
            'end_date.before_or_equal'   => 'วันที่สิ้นสุดต้องไม่เกินวันปัจจุบัน',
            'end_date.after_or_equal'    => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
        ]);
    }

    private function medicalRules(int $clientId, ?int $ignoreId = null): array
    {
        $today = now('Asia/Bangkok')->toDateString();

        $uniqueMedicalDate = Rule::unique('medicals', 'medical_date')
            ->where(fn ($query) => $query->where('client_id', $clientId));

        if ($ignoreId !== null) {
            $uniqueMedicalDate->ignore($ignoreId);
        }

        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'medical_date' => [
                'required',
                'date',
                'before_or_equal:' . $today,
                $uniqueMedicalDate,
            ],
            'disease_name' => ['required', 'string', 'max:255'],
            'illness'      => ['required', 'string', 'max:3000'],
            'treatment'    => ['nullable', 'string', 'max:3000'],
            'refer'        => ['required', Rule::in(['พบแพทย์', 'ไม่พบแพทย์'])],
            'diagnosis'    => ['required_if:refer,พบแพทย์', 'nullable', 'string', 'max:3000'],
            'appt_date'    => ['nullable', 'date', 'after_or_equal:medical_date'],
            'teacher'      => ['nullable', 'string', 'max:255'],
            'remark'       => ['nullable', 'string', 'max:3000'],
        ];
    }

    private function validationMessages(): array
    {
        return [
            'client_id.required'              => 'กรุณาเลือกผู้รับบริการ',
            'client_id.integer'               => 'ข้อมูลผู้รับบริการไม่ถูกต้อง',
            'client_id.exists'                => 'ไม่พบข้อมูลผู้รับบริการที่เลือก',
            'medical_date.required'           => 'กรุณาระบุวันที่รักษา',
            'medical_date.date'               => 'วันที่รักษาไม่ถูกต้อง',
            'medical_date.before_or_equal'    => 'วันที่รักษาต้องไม่เกินวันปัจจุบัน',
            'medical_date.unique'             => 'มีการบันทึกวันที่รักษานี้แล้วสำหรับผู้รับบริการรายนี้',
            'disease_name.required'           => 'กรุณาระบุชื่อโรคหรืออาการสำคัญ',
            'disease_name.max'                => 'ชื่อโรคต้องไม่เกิน 255 ตัวอักษร',
            'illness.required'                => 'กรุณาระบุอาการเจ็บป่วย',
            'illness.max'                     => 'อาการเจ็บป่วยต้องไม่เกิน 3,000 ตัวอักษร',
            'treatment.max'                   => 'ข้อมูลการรักษาต้องไม่เกิน 3,000 ตัวอักษร',
            'refer.required'                  => 'กรุณาเลือกสถานะการพบแพทย์',
            'refer.in'                        => 'สถานะการพบแพทย์ไม่ถูกต้อง',
            'diagnosis.required_if'           => 'กรุณาระบุการวินิจฉัยของแพทย์เมื่อเลือกว่าพบแพทย์',
            'diagnosis.max'                   => 'ข้อมูลการวินิจฉัยต้องไม่เกิน 3,000 ตัวอักษร',
            'appt_date.date'                  => 'วันที่แพทย์นัดไม่ถูกต้อง',
            'appt_date.after_or_equal'        => 'วันที่แพทย์นัดต้องไม่น้อยกว่าวันที่รักษา',
            'teacher.max'                     => 'ชื่อผู้ดูแลต้องไม่เกิน 255 ตัวอักษร',
            'remark.max'                      => 'หมายเหตุต้องไม่เกิน 3,000 ตัวอักษร',
        ];
    }

    private function normalizeMedicalInput(Request $request): void
    {
        $fields = [
            'disease_name',
            'illness',
            'treatment',
            'diagnosis',
            'teacher',
            'remark',
        ];

        $normalized = [];

        foreach ($fields as $field) {
            $value = $request->input($field);

            if (!is_string($value)) {
                continue;
            }

            $value = trim($value);
            $normalized[$field] = $value === '' ? null : $value;
        }

        $request->merge($normalized);
    }

    private function cleanConditionalFields(array $data): array
    {
        if (($data['refer'] ?? null) !== 'พบแพทย์') {
            $data['diagnosis'] = null;
            $data['appt_date'] = null;
        }

        return $data;
    }

    /**
     * เก็บกิจกรรมของโมดูลไว้เพียงรายการล่าสุดที่ยังมีอยู่จริง
     */
    private function syncLatestMedicalActivity(int $clientId): void
    {
        CaseActivity::query()
            ->where('client_id', $clientId)
            ->where('module', 'medical')
            ->delete();

        $latest = Medical::query()
            ->where('client_id', $clientId)
            ->orderByDesc('medical_date')
            ->orderByDesc('id')
            ->first();

        if (!$latest) {
            return;
        }

        CaseActivity::record([
            'client_id'   => $clientId,
            'module'      => 'medical',
            'type'        => 'success',
            'title'       => 'บันทึกการรักษาพยาบาล',
            'description' => 'วันที่รักษา: ' . ($latest->medical_date ?: '-') .
                ' | โรค/อาการ: ' . ($latest->disease_name ?: '-') .
                ' | การพบแพทย์: ' . ($latest->refer ?: '-'),
            'occurred_at' => $latest->medical_date ?: now('Asia/Bangkok'),
            'icon'        => 'bi-heart-pulse',
            'url'         => route('medical.add', $clientId),
        ]);
    }

    private function formatDateForInput($date): ?string
    {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->format('Y-m-d');
    }
}
