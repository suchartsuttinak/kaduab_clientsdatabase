<?php

namespace App\Http\Controllers\Frontend\IndividualDevelopment;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\IndividualDevelopment\DevelopmentActivity;
use App\Models\IndividualDevelopment\DevelopmentAssessment;
use App\Models\IndividualDevelopment\DevelopmentDomain;
use App\Models\IndividualDevelopment\DevelopmentGoal;
use App\Models\IndividualDevelopment\DevelopmentFollowup;
use App\Models\IndividualDevelopment\DevelopmentIndicator;
use App\Models\IndividualDevelopment\DevelopmentPlan;
use App\Services\IndividualDevelopment\IndividualDevelopmentLifecycleService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class IndividualDevelopmentGoalController extends Controller
{
    private const PERMISSION_KEY = 'individual_development';

    private const PRIORITIES = [
        'low' => 'ต่ำ',
        'medium' => 'ปานกลาง',
        'high' => 'สูง',
        'urgent' => 'เร่งด่วน',
    ];

    private const STATUSES = [
        DevelopmentGoal::STATUS_NOT_STARTED => 'ยังไม่เริ่ม',
        DevelopmentGoal::STATUS_IN_PROGRESS => 'กำลังดำเนินการ',
        DevelopmentGoal::STATUS_PARTIAL => 'มีความก้าวหน้า',
        DevelopmentGoal::STATUS_ACHIEVED => 'บรรลุเป้าหมาย',
        DevelopmentGoal::STATUS_CANCELLED => 'ยุติเป้าหมาย',
    ];

    private const EDITABLE_STATUSES = [
        DevelopmentGoal::STATUS_NOT_STARTED,
        DevelopmentGoal::STATUS_IN_PROGRESS,
        DevelopmentGoal::STATUS_PARTIAL,
    ];

    public function __construct(private readonly IndividualDevelopmentLifecycleService $lifecycle)
    {
    }

    public function index(int $client): View|RedirectResponse
    {
        $this->authorizeAction('view');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->currentPlan($clientModel->id);

        if (!$plan) {
            return redirect()->route('individual-development.index', $clientModel->id)
                ->with('warning', 'ยังไม่มีแผนพัฒนารายบุคคล');
        }

        $plan->load(['assessments.items.indicator.domain', 'followups.items.indicator.domain']);
        $goals = DevelopmentGoal::query()
            ->where('plan_id', $plan->id)
            ->with([
                'domain.indicators',
                'indicator',
                'activities' => fn ($query) => $query->orderBy('activity_date')->orderBy('id'),
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $progress = $this->lifecycle->goalProgressMap($plan, $goals);
        $canDeleteGoal = [];
        $canDeleteActivity = [];
        foreach ($goals as $goal) {
            $canDeleteGoal[$goal->id] = $this->lifecycle->canDeleteGoal($goal);
            foreach ($goal->activities as $activity) {
                $canDeleteActivity[$activity->id] = $this->lifecycle->canDeleteActivity($activity);
            }
        }

        $hasWritePermission = $this->can(self::PERMISSION_KEY, 'create') || $this->can(self::PERMISSION_KEY, 'update') || $this->can(self::PERMISSION_KEY, 'delete');
        $readOnly = (bool) request()->attributes->get('form_permission_readonly', false)
            || $plan->status !== DevelopmentPlan::STATUS_ACTIVE
            || !$hasWritePermission;

        return view('frontend.client.individual_development.goals.index', [
            'client' => $clientModel,
            'plan' => $plan,
            'goals' => $goals,
            'goalProgress' => $progress,
            'canDeleteGoalMap' => $canDeleteGoal,
            'canDeleteActivityMap' => $canDeleteActivity,
            'ageText' => $this->resolveAgeText($clientModel),
            'priorityLabels' => self::PRIORITIES,
            'statusLabels' => self::STATUSES,
            'canCreate' => !$readOnly && $this->can(self::PERMISSION_KEY, 'create'),
            'canUpdate' => !$readOnly && $this->can(self::PERMISSION_KEY, 'update'),
            'canDelete' => !$readOnly && $this->can(self::PERMISSION_KEY, 'delete'),
            'readOnly' => $readOnly,
        ]);
    }

    public function create(Request $request, int $client): View|RedirectResponse
    {
        $this->authorizeAction('create');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) {
            return redirect()->route('individual-development.index', $clientModel->id)
                ->with('warning', 'ไม่พบแผนพัฒนาที่กำลังดำเนินการ');
        }

        $baseline = $this->baselineForPlan($plan->id);
        if (!$baseline) {
            return redirect()->route('individual-development.index', $clientModel->id)
                ->with('warning', 'กรุณาประเมิน Baseline ก่อนกำหนดเป้าหมายการพัฒนา');
        }

        return view('frontend.client.individual_development.goals.form', [
            'client' => $clientModel,
            'plan' => $plan,
            'goal' => null,
            'domains' => $this->domains(),
            'baseline' => $baseline,
            'baselineLevels' => $this->baselineLevels($baseline),
            'ageText' => $this->resolveAgeText($clientModel),
            'priorityLabels' => self::PRIORITIES,
            'statusLabels' => collect(self::STATUSES)->only(self::EDITABLE_STATUSES)->all(),
            'prefillNeed' => trim((string)$request->query('need','')),
            'prefillNeedCategory' => trim((string)$request->query('need_category','')),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request, int $client): RedirectResponse
    {
        $this->authorizeAction('create');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) abort(422, 'ไม่พบแผนพัฒนาที่กำลังดำเนินการ');

        $baseline = $this->baselineForPlan($plan->id);
        if (!$baseline) {
            return redirect()->route('individual-development.index', $clientModel->id)
                ->with('warning', 'กรุณาประเมิน Baseline ก่อนกำหนดเป้าหมาย');
        }

        $validated = $this->validateGoal($request, $plan, false);
        $this->assertIndicatorBelongsToDomain($validated['domain_id'], $validated['indicator_id'] ?? null);
        $this->assertNoDuplicateOpenGoal($plan->id, $validated['domain_id'], $validated['indicator_id'] ?? null);
        $baselineLevel = $this->resolveBaselineLevel($baseline, (int) $validated['domain_id'], $validated['indicator_id'] ?? null);
        $this->assertTargetLevelAgainstBaseline((int) $validated['target_level'], $baselineLevel);

        DB::transaction(function () use ($plan, $validated, $baselineLevel): void {
            $sortOrder = ((int) DevelopmentGoal::query()->where('plan_id', $plan->id)->lockForUpdate()->max('sort_order')) + 1;
            DevelopmentGoal::create([
                'plan_id' => $plan->id,
                'domain_id' => (int) $validated['domain_id'],
                'indicator_id' => !empty($validated['indicator_id']) ? (int) $validated['indicator_id'] : null,
                'title' => trim($validated['title']),
                'description' => $this->nullableText($validated['description'] ?? null),
                'baseline_level' => $baselineLevel,
                'target_level' => (int) $validated['target_level'],
                'success_indicator' => trim($validated['success_indicator']),
                'measurement_method' => $this->nullableText($validated['measurement_method'] ?? null),
                'target_value' => $validated['target_value'] ?? null,
                'target_unit' => $this->nullableText($validated['target_unit'] ?? null),
                'target_date' => $validated['target_date'] ?? null,
                'priority' => $validated['priority'],
                'status' => DevelopmentGoal::STATUS_NOT_STARTED,
                'sort_order' => $sortOrder,
                'responsible_user_id' => null,
                'responsible_name' => $this->nullableText($validated['responsible_name'] ?? null),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        });

        return redirect()->route('individual-development.goals.index', $clientModel->id)
            ->with('success', 'เพิ่มเป้าหมายการพัฒนารายบุคคลเรียบร้อยแล้ว');
    }

    public function edit(Request $request, int $client, int $goal): View|RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) return redirect()->route('individual-development.index', $clientModel->id)->with('warning', 'แผนปัจจุบันไม่อยู่ในสถานะที่แก้ไขได้');

        $goalModel = $this->goalForPlan($plan->id, $goal);
        if (in_array($goalModel->status, [DevelopmentGoal::STATUS_ACHIEVED, DevelopmentGoal::STATUS_CANCELLED], true)) {
            return redirect()->route('individual-development.goals.index', $clientModel->id)
                ->with('warning', 'เป้าหมายนี้สิ้นสุดแล้ว หากต้องแก้สถานะให้ใช้ปุ่ม “เปิดเป้าหมายอีกครั้ง” ก่อน');
        }

        $baseline = $this->baselineForPlan($plan->id);
        return view('frontend.client.individual_development.goals.form', [
            'client' => $clientModel,
            'plan' => $plan,
            'goal' => $goalModel,
            'domains' => $this->domains(),
            'baseline' => $baseline,
            'baselineLevels' => $baseline ? $this->baselineLevels($baseline) : [],
            'ageText' => $this->resolveAgeText($clientModel),
            'priorityLabels' => self::PRIORITIES,
            'statusLabels' => collect(self::STATUSES)->only(self::EDITABLE_STATUSES)->all(),
            'prefillNeed' => trim((string)$request->query('need','')),
            'prefillNeedCategory' => trim((string)$request->query('need_category','')),
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, int $client, int $goal): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) abort(422, 'แผนปัจจุบันไม่อยู่ในสถานะที่แก้ไขได้');

        $goalModel = $this->goalForPlan($plan->id, $goal);
        if (in_array($goalModel->status, [DevelopmentGoal::STATUS_ACHIEVED, DevelopmentGoal::STATUS_CANCELLED], true)) {
            abort(422, 'เป้าหมายนี้สิ้นสุดแล้ว ไม่สามารถแก้ไขโดยตรงได้');
        }

        $validated = $this->validateGoal($request, $plan, true);
        $this->assertIndicatorBelongsToDomain($validated['domain_id'], $validated['indicator_id'] ?? null);
        $this->assertNoDuplicateOpenGoal($plan->id, $validated['domain_id'], $validated['indicator_id'] ?? null, $goalModel->id);

        $baseline = $this->baselineForPlan($plan->id);
        $baselineLevel = $baseline
            ? $this->resolveBaselineLevel($baseline, (int) $validated['domain_id'], $validated['indicator_id'] ?? null)
            : $goalModel->baseline_level;
        $this->assertTargetLevelAgainstBaseline((int) $validated['target_level'], $baselineLevel);

        $goalModel->update([
            'domain_id' => (int) $validated['domain_id'],
            'indicator_id' => !empty($validated['indicator_id']) ? (int) $validated['indicator_id'] : null,
            'title' => trim($validated['title']),
            'description' => $this->nullableText($validated['description'] ?? null),
            'baseline_level' => $baselineLevel,
            'target_level' => (int) $validated['target_level'],
            'success_indicator' => trim($validated['success_indicator']),
            'measurement_method' => $this->nullableText($validated['measurement_method'] ?? null),
            'target_value' => $validated['target_value'] ?? null,
            'target_unit' => $this->nullableText($validated['target_unit'] ?? null),
            'target_date' => $validated['target_date'] ?? null,
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'responsible_name' => $this->nullableText($validated['responsible_name'] ?? null),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('individual-development.goals.index', $clientModel->id)
            ->with('success', 'แก้ไขเป้าหมายการพัฒนาเรียบร้อยแล้ว');
    }

    public function achieve(int $client, int $goal): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) abort(422, 'แผนปัจจุบันไม่อยู่ในสถานะที่ยืนยันผลได้');

        $goalModel = $this->goalForPlan($plan->id, $goal, true);

        // ต้องมีการติดตามหลังสร้างเป้าหมายอย่างน้อย 1 ครั้ง เพื่อไม่ใช้คะแนนเก่าหรือ Baseline
        // มายืนยันผลของเป้าหมายที่เพิ่งสร้างขึ้นภายหลัง
        $hasPostGoalFollowup = DevelopmentFollowup::query()
            ->where('plan_id', $plan->id)
            ->where('created_at', '>=', $goalModel->created_at)
            ->exists();
        if (!$hasPostGoalFollowup) {
            return back()->with('warning', 'ยังยืนยันบรรลุเป้าหมายไม่ได้ กรุณาบันทึกการติดตามหลังจากสร้างเป้าหมายนี้อย่างน้อย 1 ครั้ง');
        }

        $plan->load(['assessments.items.indicator.domain', 'followups.items.indicator.domain']);
        $progress = $this->lifecycle->goalProgressMap($plan, collect([$goalModel]))[$goalModel->id] ?? null;
        if (!$progress || !$progress['reached']) {
            return back()->with('warning', 'ยังยืนยันบรรลุเป้าหมายไม่ได้ เพราะคะแนนล่าสุดยังไม่ถึงระดับเป้าหมายที่กำหนด');
        }

        $openActivities = $goalModel->activities->whereIn('status', [DevelopmentActivity::STATUS_PLANNED, DevelopmentActivity::STATUS_IN_PROGRESS])->count();
        if ($openActivities > 0) {
            return back()->with('warning', 'กรุณาปิดกิจกรรมของเป้าหมายนี้ให้เป็น “ดำเนินการแล้ว” หรือ “ยกเลิก” ก่อนยืนยันบรรลุเป้าหมาย');
        }

        $goalModel->update([
            'status' => DevelopmentGoal::STATUS_ACHIEVED,
            'achieved_at' => now('Asia/Bangkok'),
            'achieved_by' => auth()->id(),
            'cancel_reason' => null,
            'cancelled_at' => null,
            'cancelled_by' => null,
            'status_note' => 'ยืนยันบรรลุเป้าหมายโดยผู้ปฏิบัติงานจากคะแนนติดตามล่าสุดระดับ ' . ($progress['current'] ?? '-'),
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'ยืนยันว่าเป้าหมาย “' . $goalModel->title . '” บรรลุแล้วเรียบร้อย');
    }

    public function cancel(Request $request, int $client, int $goal): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) abort(422, 'แผนปัจจุบันไม่อยู่ในสถานะที่ยุติเป้าหมายได้');
        $goalModel = $this->goalForPlan($plan->id, $goal, true);
        if (in_array($goalModel->status, [DevelopmentGoal::STATUS_ACHIEVED, DevelopmentGoal::STATUS_CANCELLED], true)) {
            return back()->with('warning', 'เป้าหมายนี้สิ้นสุดแล้ว หากต้องเปลี่ยนการดำเนินงานให้ใช้ “เปิดเป้าหมายอีกครั้ง” ก่อน');
        }

        $validated = $request->validate(['reason' => ['required', 'string', 'max:5000']], ['reason.required' => 'กรุณาระบุเหตุผลที่ยุติเป้าหมาย']);
        DB::transaction(function () use ($goalModel, $validated): void {
            $goalModel->activities()->whereIn('status', [DevelopmentActivity::STATUS_PLANNED, DevelopmentActivity::STATUS_IN_PROGRESS])->update([
                'status' => DevelopmentActivity::STATUS_CANCELLED,
                'cancel_reason' => 'ยุติตามเป้าหมาย: ' . trim($validated['reason']),
                'cancelled_at' => now('Asia/Bangkok'),
                'cancelled_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
            $goalModel->update([
                'status' => DevelopmentGoal::STATUS_CANCELLED,
                'cancel_reason' => trim($validated['reason']),
                'cancelled_at' => now('Asia/Bangkok'),
                'cancelled_by' => auth()->id(),
                'achieved_at' => null,
                'achieved_by' => null,
                'status_note' => 'ยุติเป้าหมายโดยผู้ปฏิบัติงาน',
                'updated_by' => auth()->id(),
            ]);
        });

        return back()->with('success', 'ยุติเป้าหมายเรียบร้อยแล้ว และเก็บประวัติไว้สำหรับตรวจสอบย้อนหลัง');
    }

    public function reopen(Request $request, int $client, int $goal): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) abort(422, 'แผนปัจจุบันไม่อยู่ในสถานะที่เปิดเป้าหมายใหม่ได้');
        $goalModel = $this->goalForPlan($plan->id, $goal);
        if (!in_array($goalModel->status, [DevelopmentGoal::STATUS_ACHIEVED, DevelopmentGoal::STATUS_CANCELLED], true)) {
            return back()->with('warning', 'เป้าหมายนี้ยังไม่ได้สิ้นสุด จึงไม่จำเป็นต้องเปิดใหม่');
        }
        $validated = $request->validate(['reason' => ['required', 'string', 'max:5000']], ['reason.required' => 'กรุณาระบุเหตุผลที่เปิดเป้าหมายอีกครั้ง']);

        $goalModel->update([
            'status' => DevelopmentGoal::STATUS_IN_PROGRESS,
            'achieved_at' => null,
            'achieved_by' => null,
            'cancel_reason' => null,
            'cancelled_at' => null,
            'cancelled_by' => null,
            'status_note' => 'เปิดเป้าหมายอีกครั้ง: ' . trim($validated['reason']),
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'เปิดเป้าหมายกลับมาเป็น “กำลังดำเนินการ” เรียบร้อยแล้ว');
    }

    public function destroy(int $client, int $goal): RedirectResponse
    {
        $this->authorizeAction('delete');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) abort(422, 'แผนปัจจุบันไม่อยู่ในสถานะที่ลบข้อมูลได้');
        $goalModel = $this->goalForPlan($plan->id, $goal, true);

        if (!$this->lifecycle->canDeleteGoal($goalModel)) {
            return back()->with('warning', 'เป้าหมายนี้มีข้อมูลกิจกรรมหรือถูกใช้ในการติดตามแล้ว จึงไม่อนุญาตให้ลบ กรุณาใช้ “ยุติเป้าหมาย” แทน');
        }

        $goalModel->delete();
        return redirect()->route('individual-development.goals.index', $clientModel->id)
            ->with('success', 'ลบเป้าหมายที่ยังไม่ถูกนำไปใช้งานเรียบร้อยแล้ว');
    }

    private function validateGoal(Request $request, DevelopmentPlan $plan, bool $editing): array
    {
        $today = now('Asia/Bangkok')->format('Y-m-d');
        $startDate = optional($plan->start_date)->format('Y-m-d') ?? $today;
        // เป้าหมายใหม่ห้ามกำหนดย้อนหลัง แต่เป้าหมายเดิมที่เลยกำหนดแล้วต้องยังแก้ไขข้อมูลได้
        // โดยไม่บังคับให้เปลี่ยนวันประวัติเดิมเป็นวันอนาคต
        $minimumTargetDate = $editing ? $startDate : ($startDate > $today ? $startDate : $today);

        return $request->validate([
            'domain_id' => ['required', 'integer', Rule::exists('development_domains', 'id')->where('is_active', true)],
            'indicator_id' => ['nullable', 'integer', Rule::exists('development_indicators', 'id')->where('is_active', true)],
            'title' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'target_level' => ['required', 'integer', 'between:1,5'],
            'success_indicator' => ['required', 'string', 'max:10000'],
            'measurement_method' => ['nullable', 'string', 'max:500'],
            'target_value' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'target_unit' => ['nullable', 'string', 'max:100'],
            'target_date' => ['nullable', 'date', 'after_or_equal:' . $minimumTargetDate],
            'priority' => ['required', Rule::in(array_keys(self::PRIORITIES))],
            'status' => [$editing ? 'required' : 'nullable', Rule::in(self::EDITABLE_STATUSES)],
            'responsible_name' => ['nullable', 'string', 'max:255'],
        ], [
            'domain_id.required' => 'กรุณาเลือกด้านพัฒนาการ',
            'indicator_id.exists' => 'ตัวชี้วัดที่เลือกไม่ถูกต้อง',
            'title.required' => 'กรุณาระบุชื่อเป้าหมาย',
            'target_level.required' => 'กรุณาระบุระดับเป้าหมาย',
            'target_level.between' => 'ระดับเป้าหมายต้องอยู่ระหว่าง 1 ถึง 5',
            'success_indicator.required' => 'กรุณาระบุตัวชี้วัดความสำเร็จ',
            'target_date.after_or_equal' => 'กำหนดสำเร็จต้องไม่เป็นวันที่ย้อนหลัง และต้องไม่น้อยกว่าวันที่เริ่มแผน',
            'priority.required' => 'กรุณาระบุระดับความสำคัญ',
            'status.required' => 'กรุณาระบุสถานะเป้าหมาย',
        ]);
    }

    private function assertNoDuplicateOpenGoal(int $planId, int|string $domainId, int|string|null $indicatorId, ?int $ignoreGoalId = null): void
    {
        $query = DevelopmentGoal::query()->where('plan_id', $planId)
            ->where('domain_id', (int) $domainId)
            ->whereIn('status', self::EDITABLE_STATUSES);
        if (!empty($indicatorId)) $query->where('indicator_id', (int) $indicatorId); else $query->whereNull('indicator_id');
        if ($ignoreGoalId) $query->whereKeyNot($ignoreGoalId);
        if ($query->exists()) {
            throw ValidationException::withMessages(['indicator_id' => 'มีเป้าหมายที่กำลังดำเนินการสำหรับด้าน/ตัวชี้วัดนี้อยู่แล้ว กรุณาแก้ไขหรือสิ้นสุดเป้าหมายเดิมก่อน']);
        }
    }

    private function assertTargetLevelAgainstBaseline(int $targetLevel, ?int $baselineLevel): void
    {
        if ($baselineLevel === null) throw ValidationException::withMessages(['target_level' => 'ไม่พบคะแนน Baseline ของด้านหรือตัวชี้วัดที่เลือก กรุณาตรวจสอบ Baseline ก่อนกำหนดเป้าหมาย']);
        if ($baselineLevel >= 5) {
            if ($targetLevel !== 5) throw ValidationException::withMessages(['target_level' => 'Baseline อยู่ระดับ 5 แล้ว ระดับเป้าหมายต้องเป็นระดับ 5']);
            return;
        }
        if ($targetLevel <= $baselineLevel) throw ValidationException::withMessages(['target_level' => 'ระดับเป้าหมายต้องสูงกว่า Baseline ระดับ ' . $baselineLevel]);
    }

    private function assertIndicatorBelongsToDomain(int|string $domainId, int|string|null $indicatorId): void
    {
        if (empty($indicatorId)) return;
        $valid = DevelopmentIndicator::query()->whereKey((int) $indicatorId)->where('domain_id', (int) $domainId)->where('is_active', true)->exists();
        if (!$valid) throw ValidationException::withMessages(['indicator_id' => 'ตัวชี้วัดที่เลือกไม่อยู่ในด้านพัฒนาการที่กำหนด']);
    }

    private function resolveBaselineLevel(DevelopmentAssessment $baseline, int $domainId, int|string|null $indicatorId): ?int
    {
        if (!empty($indicatorId)) {
            $score = $baseline->items->firstWhere('indicator_id', (int) $indicatorId)?->score;
            return $score !== null ? (int) $score : null;
        }
        $scores = $baseline->items->filter(fn ($item) => (int) optional($item->indicator)->domain_id === $domainId)
            ->pluck('score')->filter(fn ($score) => $score !== null)->map(fn ($score) => (float) $score);
        return $scores->isEmpty() ? null : max(1, min(5, (int) round((float) $scores->avg())));
    }

    private function baselineLevels(DevelopmentAssessment $baseline): array
    {
        $levels = [];
        foreach ($baseline->items as $item) $levels['indicator:' . $item->indicator_id] = $item->score !== null ? (int) $item->score : null;
        foreach ($baseline->items->groupBy(fn ($item) => optional($item->indicator)->domain_id) as $domainId => $items) {
            if (!$domainId) continue;
            $scores = $items->pluck('score')->filter(fn ($score) => $score !== null)->map(fn ($score) => (float) $score);
            $levels['domain:' . $domainId] = $scores->isNotEmpty() ? max(1, min(5, (int) round((float) $scores->avg()))) : null;
        }
        return $levels;
    }

    private function domains()
    {
        return DevelopmentDomain::query()->where('is_active', true)
            ->with(['indicators' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')->get();
    }

    private function baselineForPlan(int $planId): ?DevelopmentAssessment
    {
        return DevelopmentAssessment::query()->where('plan_id', $planId)->where('assessment_type', DevelopmentAssessment::TYPE_BASELINE)
            ->with('items.indicator.domain')->orderByDesc('assessment_date')->orderByDesc('round_no')->first();
    }

    private function goalForPlan(int $planId, int $goalId, bool $withActivities = false): DevelopmentGoal
    {
        $query = DevelopmentGoal::query()->whereKey($goalId)->where('plan_id', $planId)->with(['domain.indicators', 'indicator']);
        if ($withActivities) $query->with('activities');
        return $query->firstOrFail();
    }

    private function currentPlan(int $clientId): ?DevelopmentPlan
    {
        return DevelopmentPlan::query()->where('client_id', $clientId)
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")->orderByDesc('plan_no')->first();
    }

    private function activePlan(int $clientId): ?DevelopmentPlan
    {
        return DevelopmentPlan::query()->where('client_id', $clientId)->where('status', DevelopmentPlan::STATUS_ACTIVE)->orderByDesc('plan_no')->first();
    }

    private function findAuthorizedClient(int $clientId): Client
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $canViewAcrossHouses = (method_exists($user, 'isAdmin') && $user->isAdmin())
            || (method_exists($user, 'hasFormPermission') && $user->hasFormPermission('individual_development_center', 'view'));

        $query = Client::forUser($user);
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

    private function nullableText(mixed $value): ?string
    {
        $text = trim((string) ($value ?? ''));
        return $text === '' ? null : $text;
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
}
