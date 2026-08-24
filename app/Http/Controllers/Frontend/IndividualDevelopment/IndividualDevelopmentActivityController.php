<?php

namespace App\Http\Controllers\Frontend\IndividualDevelopment;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\IndividualDevelopment\DevelopmentActivity;
use App\Models\IndividualDevelopment\DevelopmentGoal;
use App\Models\IndividualDevelopment\DevelopmentPlan;
use App\Services\IndividualDevelopment\IndividualDevelopmentLifecycleService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IndividualDevelopmentActivityController extends Controller
{
    private const PERMISSION_KEY = 'individual_development';

    private const EDITABLE_STATUSES = [
        DevelopmentActivity::STATUS_PLANNED => 'วางแผน',
        DevelopmentActivity::STATUS_IN_PROGRESS => 'กำลังดำเนินการ',
        DevelopmentActivity::STATUS_COMPLETED => 'เสร็จสิ้น',
    ];

    public function __construct(private readonly IndividualDevelopmentLifecycleService $lifecycle)
    {
    }

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
        if ($this->goalIsTerminal($goalModel)) {
            return redirect()->route('individual-development.goals.index', $clientModel->id)
                ->with('warning', 'เป้าหมายนี้สิ้นสุดแล้ว ไม่สามารถเพิ่มกิจกรรมใหม่ได้');
        }

        return view('frontend.client.individual_development.activities.form', [
            'client' => $clientModel,
            'plan' => $plan,
            'goal' => $goalModel,
            'activity' => null,
            'ageText' => $this->resolveAgeText($clientModel),
            'statusLabels' => self::EDITABLE_STATUSES,
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
        if ($this->goalIsTerminal($goalModel)) {
            return redirect()->route('individual-development.goals.index', $clientModel->id)
                ->with('warning', 'เป้าหมายนี้สิ้นสุดแล้ว ไม่สามารถเพิ่มกิจกรรมใหม่ได้');
        }

        $validated = $this->validateActivity($request, $plan);

        DB::transaction(function () use ($goalModel, $validated): void {
            DevelopmentActivity::create([
                'goal_id' => $goalModel->id,
                'activity_date' => $validated['activity_date'],
                'end_date' => $validated['end_date'] ?? null,
                'activity_type' => $this->nullableText($validated['activity_type'] ?? null),
                'detail' => trim($validated['detail']),
                'frequency' => $this->nullableText($validated['frequency'] ?? null),
                'status' => $validated['status'],
                'completed_at' => $validated['status'] === DevelopmentActivity::STATUS_COMPLETED ? now('Asia/Bangkok') : null,
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
        });

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
        if ($activityModel->status === DevelopmentActivity::STATUS_CANCELLED || $this->goalIsTerminal($activityModel->goal)) {
            return redirect()->route('individual-development.goals.index', $clientModel->id)
                ->with('warning', 'กิจกรรมหรือเป้าหมายนี้สิ้นสุดแล้ว หากต้องแก้ไขประวัติให้เปิดเป้าหมายอีกครั้งก่อน');
        }

        return view('frontend.client.individual_development.activities.form', [
            'client' => $clientModel,
            'plan' => $plan,
            'goal' => $activityModel->goal,
            'activity' => $activityModel,
            'ageText' => $this->resolveAgeText($clientModel),
            'statusLabels' => self::EDITABLE_STATUSES,
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
        if ($activityModel->status === DevelopmentActivity::STATUS_CANCELLED || $this->goalIsTerminal($activityModel->goal)) {
            return redirect()->route('individual-development.goals.index', $clientModel->id)
                ->with('warning', 'กิจกรรมหรือเป้าหมายนี้สิ้นสุดแล้ว ไม่สามารถแก้ไขโดยตรงได้');
        }

        $validated = $this->validateActivity($request, $plan);
        $wasCompleted = $activityModel->status === DevelopmentActivity::STATUS_COMPLETED;

        DB::transaction(function () use ($activityModel, $validated, $wasCompleted): void {
            $activityModel->update([
                'activity_date' => $validated['activity_date'],
                'end_date' => $validated['end_date'] ?? null,
                'activity_type' => $this->nullableText($validated['activity_type'] ?? null),
                'detail' => trim($validated['detail']),
                'frequency' => $this->nullableText($validated['frequency'] ?? null),
                'status' => $validated['status'],
                'completed_at' => $validated['status'] === DevelopmentActivity::STATUS_COMPLETED
                    ? ($activityModel->completed_at ?: now('Asia/Bangkok'))
                    : null,
                'responsible_name' => $this->nullableText($validated['responsible_name'] ?? null),
                'result' => $this->nullableText($validated['result'] ?? null),
                'problem' => $this->nullableText($validated['problem'] ?? null),
                'next_action' => $this->nullableText($validated['next_action'] ?? null),
                'updated_by' => auth()->id(),
            ]);

            $goal = $activityModel->goal;
            if ($goal->status === DevelopmentGoal::STATUS_NOT_STARTED
                && in_array($validated['status'], [DevelopmentActivity::STATUS_IN_PROGRESS, DevelopmentActivity::STATUS_COMPLETED], true)) {
                $goal->update([
                    'status' => DevelopmentGoal::STATUS_IN_PROGRESS,
                    'updated_by' => auth()->id(),
                ]);
            }

            if ($wasCompleted && $validated['status'] !== DevelopmentActivity::STATUS_COMPLETED) {
                $activityModel->forceFill(['completed_at' => null])->save();
            }
        });

        return redirect()->route('individual-development.goals.index', $clientModel->id)
            ->with('success', 'แก้ไขกิจกรรมตามแผนเรียบร้อยแล้ว');
    }

    public function cancel(Request $request, int $client, int $activity): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) abort(422, 'แผนปัจจุบันไม่อยู่ในสถานะที่ยกเลิกกิจกรรมได้');

        $activityModel = $this->activityForPlan($plan->id, $activity);
        if ($activityModel->status === DevelopmentActivity::STATUS_CANCELLED) {
            return back()->with('warning', 'กิจกรรมนี้ถูกยกเลิกแล้ว');
        }
        if ($activityModel->status === DevelopmentActivity::STATUS_COMPLETED) {
            return back()->with('warning', 'กิจกรรมนี้เสร็จสิ้นแล้ว หากบันทึกผิดให้แก้ไขกิจกรรมก่อน ไม่อนุญาตให้เปลี่ยนเป็นยกเลิกผ่านคำสั่งโดยตรง');
        }
        if ($activityModel->goal->status === DevelopmentGoal::STATUS_ACHIEVED) {
            return back()->with('warning', 'เป้าหมายนี้ได้รับการยืนยันว่าบรรลุแล้ว หากต้องแก้ประวัติให้เปิดเป้าหมายอีกครั้งก่อน');
        }

        $validated = $request->validate([
            'cancel_reason' => ['required', 'string', 'max:5000'],
        ], [
            'cancel_reason.required' => 'กรุณาระบุเหตุผลที่ยกเลิกกิจกรรม',
        ]);

        $activityModel->update([
            'status' => DevelopmentActivity::STATUS_CANCELLED,
            'cancel_reason' => trim($validated['cancel_reason']),
            'cancelled_at' => now('Asia/Bangkok'),
            'cancelled_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('individual-development.goals.index', $clientModel->id)
            ->with('success', 'ยกเลิกกิจกรรมแล้ว โดยเก็บประวัติไว้');
    }

    public function destroy(int $client, int $activity): RedirectResponse
    {
        $this->authorizeAction('delete');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) abort(422, 'แผนปัจจุบันไม่อยู่ในสถานะที่ลบกิจกรรมได้');

        $activityModel = $this->activityForPlan($plan->id, $activity);
        if (!$this->lifecycle->canDeleteActivity($activityModel)) {
            return redirect()->route('individual-development.goals.index', $clientModel->id)
                ->with('warning', 'กิจกรรมนี้ถูกนำไปใช้งานแล้ว จึงไม่ควรลบ กรุณาใช้ “ยกเลิกกิจกรรม” เพื่อเก็บประวัติ');
        }

        $activityModel->delete();

        return redirect()->route('individual-development.goals.index', $clientModel->id)
            ->with('success', 'ลบกิจกรรมที่ยังไม่ถูกใช้งานเรียบร้อยแล้ว');
    }

    private function validateActivity(Request $request, DevelopmentPlan $plan): array
    {
        $startDate = optional($plan->start_date)->format('Y-m-d') ?? now('Asia/Bangkok')->format('Y-m-d');

        $validated = $request->validate([
            'activity_date' => ['required', 'date', 'after_or_equal:' . $startDate],
            'end_date' => ['nullable', 'date', 'after_or_equal:activity_date'],
            'activity_type' => ['nullable', 'string', 'max:255'],
            'detail' => ['required', 'string', 'max:10000'],
            'frequency' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_keys(self::EDITABLE_STATUSES))],
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

        if (in_array($validated['status'], [DevelopmentActivity::STATUS_IN_PROGRESS, DevelopmentActivity::STATUS_COMPLETED], true)) {
            $today = Carbon::today('Asia/Bangkok');
            $activityDate = Carbon::parse($validated['activity_date'], 'Asia/Bangkok')->startOfDay();
            if ($activityDate->greaterThan($today)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'activity_date' => 'เมื่อสถานะเป็น “กำลังดำเนินการ” หรือ “เสร็จสิ้น” วันที่เริ่มกิจกรรมต้องไม่เกินวันปัจจุบัน',
                ]);
            }

            if (!empty($validated['end_date'])) {
                $endDate = Carbon::parse($validated['end_date'], 'Asia/Bangkok')->startOfDay();
                if ($validated['status'] === DevelopmentActivity::STATUS_COMPLETED && $endDate->greaterThan($today)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'end_date' => 'กิจกรรมที่เสร็จสิ้นแล้วต้องมีวันที่สิ้นสุดไม่เกินวันปัจจุบัน',
                    ]);
                }
            }
        }

        if ($validated['status'] === DevelopmentActivity::STATUS_COMPLETED && blank($validated['result'] ?? null)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'result' => 'เมื่อเลือกสถานะ “เสร็จสิ้น” กรุณาบันทึกผลการดำเนินงานก่อน',
            ]);
        }

        return $validated;
    }

    private function goalForPlan(int $planId, int $goalId): DevelopmentGoal
    {
        return DevelopmentGoal::query()
            ->whereKey($goalId)
            ->where('plan_id', $planId)
            ->with(['domain', 'indicator', 'activities'])
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

    private function goalIsTerminal(DevelopmentGoal $goal): bool
    {
        return in_array($goal->status, [DevelopmentGoal::STATUS_ACHIEVED, DevelopmentGoal::STATUS_CANCELLED], true);
    }

    private function findAuthorizedClient(int $clientId): Client
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $canViewAcrossHouses = (method_exists($user, 'isAdmin') && $user->isAdmin())
            || (method_exists($user, 'hasFormPermission') && $user->hasFormPermission('individual_development_center', 'view'));

        $query = $canViewAcrossHouses ? Client::query() : Client::forUser($user);
        return $query->with(['house', 'project', 'target'])->findOrFail($clientId);
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
        if ($value === null) return null;
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }
}
