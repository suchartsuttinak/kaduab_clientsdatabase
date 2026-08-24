<?php

namespace App\Http\Controllers\Frontend\IndividualDevelopment;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\IndividualDevelopment\ClientDocumentStatus;
use App\Models\IndividualDevelopment\DevelopmentCoordination;
use App\Models\IndividualDevelopment\DevelopmentPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IndividualDevelopmentSupplementController extends Controller
{
    private const PERMISSION_KEY = 'individual_development';

    public function updateSupportNetwork(Request $request, int $client): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->client($client);
        $plan = $this->activePlan($clientModel->id);
        abort_unless($plan, 422, 'ไม่มีแผนที่กำลังดำเนินการ');

        $validated = $request->validate([
            'support_network_profile' => ['nullable', 'array', 'max:10'],
            'support_network_profile.*.name' => ['nullable', 'string', 'max:255'],
            'support_network_profile.*.type' => ['nullable', Rule::in(['parent','relative','teacher','home_staff','social_worker','agency','other'])],
            'support_network_profile.*.organization' => ['nullable', 'string', 'max:255'],
            'support_network_profile.*.support_level' => ['nullable', Rule::in(['high','medium','low'])],
            'support_network_profile.*.role' => ['nullable', 'string', 'max:1000'],
            'support_network_profile.*.contact_note' => ['nullable', 'string', 'max:500'],
            'support_network_summary' => ['nullable', 'string', 'max:10000'],
        ]);

        $rows = collect($validated['support_network_profile'] ?? [])->map(function ($row) {
            if (!is_array($row)) return null;
            $clean = [
                'name' => $this->text($row['name'] ?? null),
                'type' => $row['type'] ?? null,
                'organization' => $this->text($row['organization'] ?? null),
                'support_level' => $row['support_level'] ?? null,
                'role' => $this->text($row['role'] ?? null),
                'contact_note' => $this->text($row['contact_note'] ?? null),
            ];
            return collect($clean)->filter(fn ($v) => filled($v))->isEmpty() ? null : $clean;
        })->filter()->values()->all();

        $plan->update([
            'support_network_profile' => $rows ?: null,
            'support_network_summary' => $this->text($validated['support_network_summary'] ?? null),
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'บันทึกเครือข่ายสนับสนุนเรียบร้อยแล้ว');
    }

    public function updateDischargePlan(Request $request, int $client): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->client($client);
        $plan = $this->activePlan($clientModel->id);
        abort_unless($plan, 422, 'ไม่มีแผนที่กำลังดำเนินการ');

        $validated = $request->validate([
            'planned_discharge_date' => ['nullable', 'date', 'after_or_equal:' . optional($plan->start_date)->format('Y-m-d')],
            'housing_readiness' => ['nullable', 'string', 'max:3000'],
            'education_readiness' => ['nullable', 'string', 'max:3000'],
            'career_readiness' => ['nullable', 'string', 'max:3000'],
            'income_living_readiness' => ['nullable', 'string', 'max:3000'],
            'caregiver_after_discharge' => ['nullable', 'string', 'max:1000'],
            'receiving_agency' => ['nullable', 'string', 'max:1000'],
            'followup_1m' => ['nullable', 'string', 'max:2000'],
            'followup_3m' => ['nullable', 'string', 'max:2000'],
            'followup_6m' => ['nullable', 'string', 'max:2000'],
            'readiness_summary' => ['nullable', 'string', 'max:5000'],
        ], [
            'planned_discharge_date.after_or_equal' => 'วันที่คาดว่าจะจำหน่ายต้องไม่น้อยกว่าวันที่เริ่มแผน',
        ]);

        $profile = [];
        foreach ($validated as $key => $value) {
            $profile[$key] = $this->text($value);
        }
        $profile = array_filter($profile, fn ($v) => filled($v));

        $plan->update([
            'discharge_plan_profile' => $profile ?: null,
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'บันทึกแผนก่อนจำหน่ายและติดตามหลังจำหน่ายเรียบร้อยแล้ว');
    }

    public function storeCoordination(Request $request, int $client): RedirectResponse
    {
        $this->authorizeAction('create');
        $clientModel = $this->client($client);
        $plan = $this->activePlan($clientModel->id);

        $validated = $request->validate([
            'coordination_date' => ['required', 'date', 'before_or_equal:today'],
            'agency_name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:500'],
            'coordinator_name' => ['nullable', 'string', 'max:255'],
            'result' => ['nullable', 'string', 'max:5000'],
            'next_appointment_date' => ['nullable', 'date', 'after_or_equal:coordination_date'],
            'document_note' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['open','waiting','completed','cancelled'])],
        ], [
            'coordination_date.required' => 'กรุณาระบุวันที่ประสาน',
            'coordination_date.before_or_equal' => 'วันที่ประสานต้องไม่เกินวันปัจจุบัน',
            'agency_name.required' => 'กรุณาระบุหน่วยงาน',
            'subject.required' => 'กรุณาระบุเรื่องที่ประสาน',
            'next_appointment_date.after_or_equal' => 'วันนัดถัดไปต้องไม่น้อยกว่าวันที่ประสาน',
        ]);

        DevelopmentCoordination::create($validated + [
            'client_id' => $clientModel->id,
            'plan_id' => $plan?->id,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'บันทึกการประสานหน่วยงานเรียบร้อยแล้ว');
    }

    public function updateCoordination(Request $request, int $client, int $coordination): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->client($client);
        $record = DevelopmentCoordination::query()->where('client_id', $clientModel->id)->findOrFail($coordination);

        $validated = $request->validate([
            'coordination_date' => ['required', 'date', 'before_or_equal:today'],
            'agency_name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:500'],
            'coordinator_name' => ['nullable', 'string', 'max:255'],
            'result' => ['nullable', 'string', 'max:5000'],
            'next_appointment_date' => ['nullable', 'date', 'after_or_equal:coordination_date'],
            'document_note' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['open','waiting','completed','cancelled'])],
        ], [
            'coordination_date.required' => 'กรุณาระบุวันที่ประสาน',
            'coordination_date.before_or_equal' => 'วันที่ประสานต้องไม่เกินวันปัจจุบัน',
            'agency_name.required' => 'กรุณาระบุหน่วยงาน',
            'subject.required' => 'กรุณาระบุเรื่องที่ประสาน',
            'next_appointment_date.after_or_equal' => 'วันนัดถัดไปต้องไม่น้อยกว่าวันที่ประสาน',
        ]);

        $record->update($validated + ['updated_by' => auth()->id()]);
        return back()->with('success', 'ปรับปรุงผลการประสานเรียบร้อยแล้ว');
    }

    public function destroyCoordination(int $client, int $coordination): RedirectResponse
    {
        $this->authorizeAction('delete');
        $clientModel = $this->client($client);
        $record = DevelopmentCoordination::query()->where('client_id', $clientModel->id)->findOrFail($coordination);
        $record->delete();
        return back()->with('success', 'ลบรายการประสานที่บันทึกผิดเรียบร้อยแล้ว');
    }

    public function updateDocuments(Request $request, int $client): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->client($client);
        $allowedTypes = array_keys($this->documentTypes());

        $validated = $request->validate([
            'documents' => ['nullable', 'array'],
            'documents.*.status' => ['nullable', Rule::in(['missing','in_progress','available','expired','not_applicable'])],
            'documents.*.expires_at' => ['nullable', 'date'],
            'documents.*.note' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($clientModel, $allowedTypes, $validated): void {
            foreach ($allowedTypes as $type) {
                $row = $validated['documents'][$type] ?? [];
                if (!is_array($row)) $row = [];
                $status = $row['status'] ?? null;
                $expires = $row['expires_at'] ?? null;
                $note = $this->text($row['note'] ?? null);

                if (!$status && !$expires && !$note) {
                    ClientDocumentStatus::query()->where('client_id', $clientModel->id)->where('document_type', $type)->delete();
                    continue;
                }

                ClientDocumentStatus::updateOrCreate(
                    ['client_id' => $clientModel->id, 'document_type' => $type],
                    ['status' => $status ?: 'missing', 'expires_at' => $expires ?: null, 'note' => $note, 'updated_by' => auth()->id()]
                );
            }
        });

        return back()->with('success', 'ปรับปรุงสถานะเอกสารสำคัญเรียบร้อยแล้ว');
    }

    private function documentTypes(): array
    {
        return [
            'id_card' => 'บัตรประชาชน',
            'house_registration' => 'ทะเบียนบ้าน',
            'birth_certificate' => 'สูติบัตร',
            'education_document' => 'เอกสารการศึกษา',
            'medical_certificate' => 'ใบรับรองแพทย์',
            'consent_form' => 'หนังสือยินยอม',
            'passport' => 'หนังสือเดินทาง',
            'court_order' => 'คำสั่งศาล',
        ];
    }

    private function client(int $id): Client
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $canViewAcrossHouses = (method_exists($user, 'isAdmin') && $user->isAdmin())
            || (method_exists($user, 'hasFormPermission') && $user->hasFormPermission('individual_development_center', 'view'));

        $query = $canViewAcrossHouses ? Client::query() : Client::forUser($user);
        return $query->findOrFail($id);
    }

    private function activePlan(int $clientId): ?DevelopmentPlan
    {
        return DevelopmentPlan::query()->where('client_id', $clientId)->where('status', DevelopmentPlan::STATUS_ACTIVE)->latest('plan_no')->first();
    }

    private function authorizeAction(string $action): void
    {
        abort_unless($this->can($action), 403);
    }

    private function can(string $action): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) return true;
        return method_exists($user, 'hasFormPermission') && (bool) $user->hasFormPermission(self::PERMISSION_KEY, $action);
    }

    private function text(mixed $value): ?string
    {
        $text = trim((string) ($value ?? ''));
        return $text === '' ? null : $text;
    }
}
