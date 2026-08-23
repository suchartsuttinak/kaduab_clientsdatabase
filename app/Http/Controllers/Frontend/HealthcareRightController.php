<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\HealthcareRight;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HealthcareRightController extends Controller
{
    public function index(int $client_id): View
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $rights = $this->rightsForClient($client->id);
        $editing = null;

        return view('frontend.client.healthcare_rights.index', compact(
            'client',
            'rights',
            'editing'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $clientInput = $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
        ], [
            'client_id.required' => 'ไม่พบข้อมูลผู้รับบริการ',
            'client_id.integer' => 'ข้อมูลผู้รับบริการไม่ถูกต้อง',
            'client_id.exists' => 'ไม่พบข้อมูลผู้รับบริการในระบบ',
        ]);

        $client = Client::forUser(auth()->user())
            ->findOrFail((int) $clientInput['client_id']);

        $validated = $this->validateRight($request, $client->id);
        $validated['client_id'] = $client->id;
        $validated['recorder_name'] = $this->currentRecorderName();
        $validated = $this->normalizeHospital($validated);

        HealthcareRight::create($validated);

        return redirect()
            ->route('healthcare_rights.index', $client->id)
            ->with([
                'message' => 'บันทึกสิทธิรักษาพยาบาลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function edit(int $id): View
    {
        $editing = $this->findAccessibleRight($id);
        $client = $editing->client;
        $rights = $this->rightsForClient($client->id);

        return view('frontend.client.healthcare_rights.index', compact(
            'client',
            'rights',
            'editing'
        ));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $right = $this->findAccessibleRight($id);

        $request->merge([
            'client_id' => $right->client_id,
        ]);

        $validated = $this->validateRight($request, $right->client_id, $right->id);
        $validated['client_id'] = $right->client_id;
        $validated = $this->normalizeHospital($validated);

        // เก็บชื่อผู้บันทึกครั้งแรกไว้ ไม่เปลี่ยนเมื่อมีการแก้ไข
        unset($validated['recorder_name']);

        $right->update($validated);

        return redirect()
            ->route('healthcare_rights.index', $right->client_id)
            ->with([
                'message' => 'แก้ไขสิทธิรักษาพยาบาลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $right = $this->findAccessibleRight($id);
        $clientId = $right->client_id;
        $right->delete();

        return redirect()
            ->route('healthcare_rights.index', $clientId)
            ->with([
                'message' => 'ลบข้อมูลสิทธิรักษาพยาบาลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function report(int $client_id): View
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $rights = $this->rightsForClient($client->id);

        return view('frontend.client.healthcare_rights.report', compact(
            'client',
            'rights'
        ));
    }

    private function validateRight(
        Request $request,
        int $clientId,
        ?int $ignoreId = null
    ): array {
        $today = now('Asia/Bangkok')->toDateString();

        $uniqueDate = Rule::unique('healthcare_rights', 'record_date')
            ->where(fn ($query) => $query->where('client_id', $clientId));

        if ($ignoreId !== null) {
            $uniqueDate->ignore($ignoreId);
        }

        $manualHospitalStatuses = [
            HealthcareRight::STATUS_GOLD_CARD,
            HealthcareRight::STATUS_SOCIAL_SECURITY,
        ];

        return $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'record_date' => [
                'bail',
                'required',
                'date',
                'before_or_equal:' . $today,
                $uniqueDate,
            ],
            'coverage_status' => [
                'required',
                Rule::in(HealthcareRight::statusOptions()),
            ],
            'primary_hospital' => [
                Rule::requiredIf(
                    fn () => in_array(
                        (string) $request->input('coverage_status'),
                        $manualHospitalStatuses,
                        true
                    )
                ),
                'nullable',
                'string',
                'max:255',
            ],
        ], [
            'record_date.required' => 'กรุณาระบุวันที่บันทึก',
            'record_date.date' => 'รูปแบบวันที่บันทึกไม่ถูกต้อง',
            'record_date.before_or_equal' => 'วันที่บันทึกต้องไม่เกินวันที่ปัจจุบัน',
            'record_date.unique' => 'ผู้รับบริการรายนี้มีการบันทึกสิทธิในวันที่นี้แล้ว',
            'coverage_status.required' => 'กรุณาเลือกสถานะสิทธิรักษาพยาบาล',
            'coverage_status.in' => 'สถานะสิทธิรักษาพยาบาลไม่ถูกต้อง',
            'primary_hospital.required' => 'กรุณาระบุสถานพยาบาลที่เข้ารับการรักษาเบื้องต้น',
            'primary_hospital.string' => 'ชื่อสถานพยาบาลต้องเป็นข้อความ',
            'primary_hospital.max' => 'ชื่อสถานพยาบาลต้องไม่เกิน 255 ตัวอักษร',
        ]);
    }

    private function normalizeHospital(array $validated): array
    {
        $status = (string) ($validated['coverage_status'] ?? '');

        if (in_array($status, [
            HealthcareRight::STATUS_DISABLED,
            HealthcareRight::STATUS_CIVIL_SERVANT,
        ], true)) {
            $validated['primary_hospital'] = HealthcareRight::GOVERNMENT_HOSPITAL_TEXT;

            return $validated;
        }

        if ($status === HealthcareRight::STATUS_UNREGISTERED) {
            $validated['primary_hospital'] = null;

            return $validated;
        }

        $hospital = trim((string) ($validated['primary_hospital'] ?? ''));
        $validated['primary_hospital'] = $hospital !== '' ? $hospital : null;

        return $validated;
    }

    private function currentRecorderName(): string
    {
        $user = auth()->user();

        $name = trim((string) ($user?->name ?? ''));

        return $name !== '' ? $name : 'ผู้ใช้งานระบบ';
    }

    private function findAccessibleRight(int $id): HealthcareRight
    {
        return HealthcareRight::query()
            ->with('client')
            ->whereHas('client', function ($query): void {
                $query->forUser(auth()->user());
            })
            ->findOrFail($id);
    }

    private function rightsForClient(int $clientId)
    {
        return HealthcareRight::query()
            ->where('client_id', $clientId)
            ->orderByDesc('record_date')
            ->orderByDesc('id')
            ->get();
    }
}
