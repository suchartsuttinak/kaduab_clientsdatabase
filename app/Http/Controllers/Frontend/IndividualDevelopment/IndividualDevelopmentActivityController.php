<?php

namespace App\Http\Controllers\Frontend\IndividualDevelopment;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\IndividualDevelopment\DevelopmentActivity;
use App\Models\IndividualDevelopment\DevelopmentGoal;
use App\Models\IndividualDevelopment\DevelopmentPlan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IndividualDevelopmentActivityController extends Controller
{
    private const PERMISSION_KEY = 'individual_development';

    private const STATUSES = [
        DevelopmentActivity::STATUS_PLANNED => 'วางแผน',
        DevelopmentActivity::STATUS_IN_PROGRESS => 'กำลังดำเนินการ',
        DevelopmentActivity::STATUS_COMPLETED => 'ดำเนินการแล้ว',
        DevelopmentActivity::STATUS_CANCELLED => 'ยกเลิก',
    ];

    public function create(int $client, int $goal): View|RedirectResponse
    {
        $this->authorizeAction('create');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) {
            return redirect()->route('individual-development.goals.index', $clientModel->id)
                ->with('warning', 'แผนปัจจุบันไม่อยู่ในสถานะที่เพิ่มกิจกรรมได้');
        }

        $goalModel = $this->goalForPlan($plan->id, $goal);

        return view('frontend.client.individual_development.activities.form', [
            'client' => $clientModel,
            'plan' => $plan,
            'goal' => $goalModel,
            'activity' => null,
            'ageText' => $this->resolveAgeText($clientModel),
            'statusLabels' => self::STATUSES,
            'mode' => 'create',
        ]);
    }

    public function store(Request $request, int $client, int $goal): RedirectResponse
    {
        $this->authorizeAction('create');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) abort(422, 'แผนปัจจุบันไม่อยู่ในสถานะที่เพิ่มกิจกรรมได้');

        $goalModel = $this->goalForPlan($plan->id, $goal);
        $validated = $this->validateActivity($request, $plan);

        DevelopmentActivity::create([
            'goal_id' => $goalModel->id,
            'activity_date' => $validated['activity_date'],
            'end_date' => $validated['end_date'] ?? null,
            'activity_type' => $this->nullableText($validated['activity_type'] ?? null),
            'detail' => trim($validated['detail']),
            'frequency' => $this->nullableText($validated['frequency'] ?? null),
            'status' => $validated['status'],
            'responsible_user_id' => null,
            'responsible_name' => $this->nullableText($validated['responsible_name'] ?? null),
            'result' => $this->nullableText($validated['result'] ?? null),
            'problem' => $this->nullableText($validated['problem'] ?? null),
            'next_action' => $this->nullableText($validated['next_action'] ?? null),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        if ($goalModel->status === DevelopmentGoal::STATUS_NOT_STARTED
            && in_array($validated['status'], [DevelopmentActivity::STATUS_IN_PROGRESS, DevelopmentActivity::STATUS_COMPLETED], true)) {
            $goalModel->update([
                'status' => DevelopmentGoal::STATUS_IN_PROGRESS,
                'updated_by' => auth()->id(),
            ]);
        }

        return redirect()->route('individual-development.goals.index', $clientModel->id)
            ->with('success', 'เพิ่มกิจกรรมตามแผนเรียบร้อยแล้ว');
    }

    public function edit(int $client, int $activity): View|RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) {
            return redirect()->route('individual-development.goals.index', $clientModel->id)
                ->with('warning', 'แผนปัจจุบันไม่อยู่ในสถานะที่แก้ไขกิจกรรมได้');
        }

        $activityModel = $this->activityForPlan($plan->id, $activity);

        return view('frontend.client.individual_development.activities.form', [
            'client' => $clientModel,
            'plan' => $plan,
            'goal' => $activityModel->goal,
            'activity' => $activityModel,
            'ageText' => $this->resolveAgeText($clientModel),
            'statusLabels' => self::STATUSES,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, int $client, int $activity): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) abort(422, 'แผนปัจจุบันไม่อยู่ในสถานะที่แก้ไขกิจกรรมได้');

        $activityModel = $this->activityForPlan($plan->id, $activity);
        $validated = $this->validateActivity($request, $plan);

        $activityModel->update([
            'activity_date' => $validated['activity_date'],
            'end_date' => $validated['end_date'] ?? null,
            'activity_type' => $this->nullableText($validated['activity_type'] ?? null),
            'detail' => trim($validated['detail']),
            'frequency' => $this->nullableText($validated['frequency'] ?? null),
            'status' => $validated['status'],
            'responsible_name' => $this->nullableText($validated['responsible_name'] ?? null),
            'result' => $this->nullableText($validated['result'] ?? null),
            'problem' => $this->nullableText($validated['problem'] ?? null),
            'next_action' => $this->nullableText($validated['next_action'] ?? null),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('individual-development.goals.index', $clientModel->id)
            ->with('success', 'แก้ไขกิจกรรมตามแผนเรียบร้อยแล้ว');
    }

    public function destroy(int $client, int $activity): RedirectResponse
    {
        $this->authorizeAction('delete');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) abort(422, 'แผนปัจจุบันไม่อยู่ในสถานะที่ลบกิจกรรมได้');

        $activityModel = $this->activityForPlan($plan->id, $activity);
        $activityModel->delete();

        return redirect()->route('individual-development.goals.index', $clientModel->id)
            ->with('success', 'ลบกิจกรรมตามแผนเรียบร้อยแล้ว');
    }

    private function validateActivity(Request $request, DevelopmentPlan $plan): array
    {
        $startDate = optional($plan->start_date)->format('Y-m-d') ?? now('Asia/Bangkok')->format('Y-m-d');

        return $request->validate([
            'activity_date' => ['required', 'date', 'after_or_equal:' . $startDate],
            'end_date' => ['nullable', 'date', 'after_or_equal:activity_date'],
            'activity_type' => ['nullable', 'string', 'max:255'],
            'detail' => ['required', 'string', 'max:10000'],
            'frequency' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_keys(self::STATUSES))],
            'responsible_name' => ['nullable', 'string', 'max:255'],
            'result' => ['nullable', 'string', 'max:10000'],
            'problem' => ['nullable', 'string', 'max:10000'],
            'next_action' => ['nullable', 'string', 'max:10000'],
        ], [
            'activity_date.required' => 'กรุณาระบุวันที่เริ่มกิจกรรม',
            'activity_date.after_or_equal' => 'วันที่เริ่มกิจกรรมต้องไม่น้อยกว่าวันที่เริ่มแผน',
            'end_date.after_or_equal' => 'วันที่สิ้นสุดกิจกรรมต้องไม่น้อยกว่าวันที่เริ่มกิจกรรม',
            'detail.required' => 'กรุณาระบุรายละเอียดกิจกรรม',
            'status.required' => 'กรุณาระบุสถานะกิจกรรม',
        ]);
    }

    private function goalForPlan(int $planId, int $goalId): DevelopmentGoal
    {
        return DevelopmentGoal::query()
            ->whereKey($goalId)
            ->where('plan_id', $planId)
            ->with(['domain', 'indicator'])
            ->firstOrFail();
    }

    private function activityForPlan(int $planId, int $activityId): DevelopmentActivity
    {
        return DevelopmentActivity::query()
            ->whereKey($activityId)
            ->whereHas('goal', fn ($query) => $query->where('plan_id', $planId))
            ->with(['goal.domain', 'goal.indicator'])
            ->firstOrFail();
    }

    private function activePlan(int $clientId): ?DevelopmentPlan
    {
        return DevelopmentPlan::query()
            ->where('client_id', $clientId)
            ->where('status', DevelopmentPlan::STATUS_ACTIVE)
            ->orderByDesc('plan_no')
            ->first();
    }

    private function findAuthorizedClient(int $clientId): Client
    {
        return Client::forUser(auth()->user())
            ->with(['house', 'project', 'target'])
            ->findOrFail($clientId);
    }

    private function resolveAgeText(Client $client): string
    {
        if (empty($client->birth_date)) return '-';
        $birthDate = Carbon::parse($client->birth_date, 'Asia/Bangkok')->startOfDay();
        $today = Carbon::today('Asia/Bangkok');
        if ($birthDate->greaterThan($today)) return '-';
        $diff = $birthDate->diff($today);
        return $diff->y . ' ปี ' . $diff->m . ' เดือน';
    }

    private function authorizeAction(string $action): void
    {
        abort_unless($this->can(self::PERMISSION_KEY, $action), 403);
    }

    private function can(string $permissionKey, string $action): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) return true;
        if (!method_exists($user, 'hasFormPermission')) return false;
        return (bool) $user->hasFormPermission($permissionKey, $action);
    }

    private function nullableText(mixed $value): ?string
    {
        $text = trim((string) ($value ?? ''));
        return $text === '' ? null : $text;
    }
}
