<?php

namespace App\Http\Controllers\Frontend\IndividualDevelopment;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\IndividualDevelopment\DevelopmentAssessment;
use App\Models\IndividualDevelopment\DevelopmentDomain;
use App\Models\IndividualDevelopment\DevelopmentFollowup;
use App\Models\IndividualDevelopment\DevelopmentFollowupItem;
use App\Models\IndividualDevelopment\DevelopmentGoal;
use App\Models\IndividualDevelopment\DevelopmentPlan;
use App\Services\IndividualDevelopment\IndividualDevelopmentLifecycleService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class IndividualDevelopmentFollowupController extends Controller
{
    private const PERMISSION_KEY = 'individual_development';

    public function __construct(private readonly IndividualDevelopmentLifecycleService $lifecycle)
    {
    }

    private const FOLLOWUP_TYPES = [
        'routine' => 'ติดตามตามแผน',
        'case_review' => 'ทบทวนแผนรายกรณี',
        'school' => 'ติดตามด้านการศึกษา',
        'family' => 'ติดตามครอบครัว/ผู้ดูแล',
        'health' => 'ติดตามสุขภาพ',
        'other' => 'อื่น ๆ',
    ];

    private const RESULTS = [
        DevelopmentFollowup::RESULT_IMPROVED => 'ดีขึ้น',
        DevelopmentFollowup::RESULT_STABLE => 'คงเดิม',
        DevelopmentFollowup::RESULT_DECLINED => 'ถดถอย',
        DevelopmentFollowup::RESULT_ACHIEVED => 'บรรลุเป้าหมาย',
    ];

    public function create(int $client): View|RedirectResponse
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
                ->with('warning', 'กรุณาประเมิน Baseline ก่อนบันทึกการติดตาม');
        }

        $goals = DevelopmentGoal::query()
            ->where('plan_id', $plan->id)
            ->with(['domain.indicators', 'indicator', 'activities'])
            ->orderBy('sort_order')->orderBy('id')->get();
        if ($goals->isEmpty()) {
            return redirect()->route('individual-development.goals.create', $clientModel->id)
                ->with('warning', 'กรุณากำหนดเป้าหมายการพัฒนาก่อนเริ่มบันทึกการติดตาม');
        }

        $domains = $this->domains();
        $previousScores = $this->previousScores($plan->id, $baseline);
        $previousFollowup = $this->latestFollowup($plan->id);
        $nextNo = ((int) DevelopmentFollowup::withTrashed()->where('plan_id', $plan->id)->max('followup_no')) + 1;

        return view('frontend.client.individual_development.followups.form', [
            'client' => $clientModel,
            'plan' => $plan,
            'followup' => null,
            'previousFollowup' => $previousFollowup,
            'followupNo' => $nextNo,
            'minimumFollowupDate' => $this->minimumFollowupDate($plan),
            'domains' => $domains,
            'previousScores' => $previousScores,
            'currentScores' => $previousScores,
            'ageText' => $this->resolveAgeText($clientModel),
            'followupTypes' => self::FOLLOWUP_TYPES,
            'resultLabels' => self::RESULTS,
            'goals' => $goals,
            'goalProgress' => $this->lifecycle->goalProgressMap($plan, $goals),
            'mode' => 'create',
            'readOnly' => false,
        ]);
    }

    public function store(Request $request, int $client): RedirectResponse
    {
        $this->authorizeAction('create');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);

        if (!$plan) {
            abort(422, 'ไม่พบแผนพัฒนาที่กำลังดำเนินการ');
        }

        $baseline = $this->baselineForPlan($plan->id);
        if (!$baseline) {
            return redirect()->route('individual-development.index', $clientModel->id)
                ->with('warning', 'กรุณาประเมิน Baseline ก่อนบันทึกการติดตาม');
        }

        if (!DevelopmentGoal::query()->where('plan_id', $plan->id)->exists()) {
            return redirect()->route('individual-development.goals.create', $clientModel->id)
                ->with('warning', 'กรุณากำหนดเป้าหมายการพัฒนาก่อนเริ่มบันทึกการติดตาม');
        }

        $domains = $this->domains();
        $previousScores = $this->previousScores($plan->id, $baseline);
        $validated = $this->validateFollowup($request, $domains, $plan, null, $previousScores);

        $followup = DB::transaction(function () use ($clientModel, $plan, $baseline, $domains, $validated): DevelopmentFollowup {
            $lockedPlan = DevelopmentPlan::query()
                ->whereKey($plan->id)
                ->where('client_id', $clientModel->id)
                ->where('status', DevelopmentPlan::STATUS_ACTIVE)
                ->lockForUpdate()
                ->firstOrFail();

            $latestFollowup = DevelopmentFollowup::query()
                ->where('plan_id', $lockedPlan->id)
                ->orderByDesc('followup_no')
                ->lockForUpdate()
                ->first();

            if ($latestFollowup?->followup_date) {
                $latestDate = Carbon::parse($latestFollowup->followup_date, 'Asia/Bangkok')->startOfDay();
                $requestedDate = Carbon::parse($validated['followup_date'], 'Asia/Bangkok')->startOfDay();
                if ($requestedDate->lessThanOrEqualTo($latestDate)) {
                    throw ValidationException::withMessages([
                        'followup_date' => 'มีการติดตามรอบล่าสุดในวันที่ ' . $latestDate->format('d/m/Y') . ' แล้ว วันที่ของรอบใหม่ต้องมากกว่าวันดังกล่าว',
                    ]);
                }
            }

            $nextNo = ((int) DevelopmentFollowup::withTrashed()
                ->where('plan_id', $lockedPlan->id)
                ->lockForUpdate()
                ->max('followup_no')) + 1;

            $previousScores = $this->previousScores($lockedPlan->id, $baseline);

            $followup = DevelopmentFollowup::create([
                'plan_id' => $lockedPlan->id,
                'client_id' => $clientModel->id,
                'followup_no' => $nextNo,
                'followup_date' => $validated['followup_date'],
                'followup_type' => $this->nullableText($validated['followup_type'] ?? null),
                'follower_user_id' => auth()->id(),
                'follower_name' => $this->nullableText($validated['follower_name'] ?? null) ?: (auth()->user()->name ?? null),
                'current_situation' => $this->nullableText($validated['current_situation'] ?? null),
                'changes' => $this->nullableText($validated['changes'] ?? null),
                'positive_changes' => $this->nullableText($validated['positive_changes'] ?? null),
                'actions_taken' => $this->nullableText($validated['actions_taken'] ?? null),
                'result' => $this->nullableText($validated['result'] ?? null),
                'problem' => $this->nullableText($validated['problem'] ?? null),
                'client_feedback' => $this->nullableText($validated['client_feedback'] ?? null),
                'caregiver_feedback' => $this->nullableText($validated['caregiver_feedback'] ?? null),
                'overall_result' => $validated['overall_result'],
                'suggestion' => $this->nullableText($validated['suggestion'] ?? null),
                'next_action' => $this->nullableText($validated['next_action'] ?? null),
                'next_followup_date' => $validated['next_followup_date'] ?? null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            foreach ($this->indicatorIds($domains) as $indicatorId) {
                DevelopmentFollowupItem::create([
                    'followup_id' => $followup->id,
                    'indicator_id' => $indicatorId,
                    'previous_score' => $previousScores[$indicatorId] ?? null,
                    'score' => (int) $validated['items'][$indicatorId]['score'],
                    'evidence' => $this->nullableText($validated['items'][$indicatorId]['evidence'] ?? null),
                    'development_note' => $this->nullableText($validated['items'][$indicatorId]['development_note'] ?? null),
                ]);
            }

            return $followup;
        });

        return redirect()
            ->route('individual-development.followups.show', [$clientModel->id, $followup->id])
            ->with('success', 'บันทึกการติดตามครั้งที่ ' . $followup->followup_no . ' เรียบร้อยแล้ว');
    }

    public function show(int $client, int $followup): View
    {
        $this->authorizeAction('view');
        $clientModel = $this->findAuthorizedClient($client);
        $followupModel = $this->followupForClient($clientModel->id, $followup);
        $domains = $this->domains();
        $summaries = $this->domainSummaries($domains, $followupModel->items);
        $latestId = DevelopmentFollowup::query()
            ->where('plan_id', $followupModel->plan_id)
            ->orderByDesc('followup_no')
            ->value('id');

        $canUpdate = $followupModel->plan?->status === DevelopmentPlan::STATUS_ACTIVE
            && (int) $latestId === (int) $followupModel->id
            && $this->can(self::PERMISSION_KEY, 'update');
        $canDelete = $followupModel->plan?->status === DevelopmentPlan::STATUS_ACTIVE
            && (int) $latestId === (int) $followupModel->id
            && !$this->lifecycle->hasAchievedGoals($followupModel->plan)
            && $this->can(self::PERMISSION_KEY, 'delete');

        return view('frontend.client.individual_development.followups.show', [
            'client' => $clientModel,
            'plan' => $followupModel->plan,
            'followup' => $followupModel,
            'domains' => $domains,
            'domainSummaries' => $summaries,
            'ageText' => $this->resolveAgeText($clientModel),
            'followupTypes' => self::FOLLOWUP_TYPES,
            'resultLabels' => self::RESULTS,
            'canUpdate' => $canUpdate,
            'canDelete' => $canDelete,
            'readOnly' => (bool) request()->attributes->get('form_permission_readonly', false)
                || !($this->can(self::PERMISSION_KEY, 'create') || $this->can(self::PERMISSION_KEY, 'update') || $this->can(self::PERMISSION_KEY, 'delete')),
        ]);
    }

    public function edit(Request $request, int $client, int $followup): View|RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $followupModel = $this->followupForClient($clientModel->id, $followup);

        if ($followupModel->plan?->status !== DevelopmentPlan::STATUS_ACTIVE || !$this->isLatest($followupModel)) {
            return redirect()->route('individual-development.followups.show', [$clientModel->id, $followupModel->id])
                ->with('warning', 'เพื่อรักษาลำดับประวัติ สามารถแก้ไขได้เฉพาะการติดตามล่าสุดของแผนที่กำลังดำเนินการ');
        }

        $domains = $this->domains();
        $items = $followupModel->items->keyBy('indicator_id');
        $previousScores = [];
        $currentScores = [];
        foreach ($this->indicatorIds($domains) as $indicatorId) {
            $previousScores[$indicatorId] = $items->get($indicatorId)?->previous_score;
            $currentScores[$indicatorId] = $items->get($indicatorId)?->score;
        }

        $previousFollowup = DevelopmentFollowup::query()
            ->where('plan_id', $followupModel->plan_id)
            ->where('followup_no', '<', $followupModel->followup_no)
            ->orderByDesc('followup_no')
            ->first();

        $goals = DevelopmentGoal::query()
            ->where('plan_id', $followupModel->plan_id)
            ->with(['domain.indicators', 'indicator', 'activities'])
            ->orderBy('sort_order')->orderBy('id')->get();

        return view('frontend.client.individual_development.followups.form', [
            'client' => $clientModel,
            'plan' => $followupModel->plan,
            'followup' => $followupModel,
            'previousFollowup' => $previousFollowup,
            'followupNo' => $followupModel->followup_no,
            'minimumFollowupDate' => $this->minimumFollowupDate($followupModel->plan, $followupModel),
            'domains' => $domains,
            'previousScores' => $previousScores,
            'currentScores' => $currentScores,
            'ageText' => $this->resolveAgeText($clientModel),
            'followupTypes' => self::FOLLOWUP_TYPES,
            'resultLabels' => self::RESULTS,
            'goals' => $goals,
            'goalProgress' => $this->lifecycle->goalProgressMap($followupModel->plan, $goals),
            'mode' => 'edit',
            'readOnly' => (bool) $request->attributes->get('form_permission_readonly', false)
                || !$this->can(self::PERMISSION_KEY, 'update'),
        ]);
    }

    public function update(Request $request, int $client, int $followup): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $followupModel = $this->followupForClient($clientModel->id, $followup);

        if ($followupModel->plan?->status !== DevelopmentPlan::STATUS_ACTIVE || !$this->isLatest($followupModel)) {
            abort(422, 'สามารถแก้ไขได้เฉพาะการติดตามล่าสุดของแผนที่กำลังดำเนินการ');
        }

        $domains = $this->domains();
        $previousScores = $followupModel->items->mapWithKeys(fn ($item) => [
            (int) $item->indicator_id => $item->previous_score !== null ? (int) $item->previous_score : null,
        ])->all();
        $validated = $this->validateFollowup($request, $domains, $followupModel->plan, $followupModel, $previousScores);
        $this->assertAchievedGoalsRemainSatisfied($followupModel->plan, $validated['items']);

        DB::transaction(function () use ($followupModel, $domains, $validated): void {
            $locked = DevelopmentFollowup::query()->whereKey($followupModel->id)->lockForUpdate()->firstOrFail();

            $latestId = DevelopmentFollowup::query()
                ->where('plan_id', $locked->plan_id)
                ->orderByDesc('followup_no')
                ->value('id');
            if ((int) $latestId !== (int) $locked->id) {
                abort(422, 'มีการติดตามรอบใหม่กว่าแล้ว จึงไม่สามารถแก้ไขรอบนี้ได้');
            }

            $locked->update([
                'followup_date' => $validated['followup_date'],
                'followup_type' => $this->nullableText($validated['followup_type'] ?? null),
                'follower_user_id' => auth()->id(),
                'follower_name' => $this->nullableText($validated['follower_name'] ?? null) ?: (auth()->user()->name ?? null),
                'current_situation' => $this->nullableText($validated['current_situation'] ?? null),
                'changes' => $this->nullableText($validated['changes'] ?? null),
                'positive_changes' => $this->nullableText($validated['positive_changes'] ?? null),
                'actions_taken' => $this->nullableText($validated['actions_taken'] ?? null),
                'result' => $this->nullableText($validated['result'] ?? null),
                'problem' => $this->nullableText($validated['problem'] ?? null),
                'client_feedback' => $this->nullableText($validated['client_feedback'] ?? null),
                'caregiver_feedback' => $this->nullableText($validated['caregiver_feedback'] ?? null),
                'overall_result' => $validated['overall_result'],
                'suggestion' => $this->nullableText($validated['suggestion'] ?? null),
                'next_action' => $this->nullableText($validated['next_action'] ?? null),
                'next_followup_date' => $validated['next_followup_date'] ?? null,
                'updated_by' => auth()->id(),
            ]);

            $existing = $locked->items()->get()->keyBy('indicator_id');
            foreach ($this->indicatorIds($domains) as $indicatorId) {
                $oldItem = $existing->get($indicatorId);
                DevelopmentFollowupItem::query()->updateOrCreate(
                    ['followup_id' => $locked->id, 'indicator_id' => $indicatorId],
                    [
                        'previous_score' => $oldItem?->previous_score,
                        'score' => (int) $validated['items'][$indicatorId]['score'],
                        'evidence' => $this->nullableText($validated['items'][$indicatorId]['evidence'] ?? null),
                        'development_note' => $this->nullableText($validated['items'][$indicatorId]['development_note'] ?? null),
                    ]
                );
            }
        });

        return redirect()
            ->route('individual-development.followups.show', [$clientModel->id, $followupModel->id])
            ->with('success', 'ปรับปรุงการติดตามครั้งที่ ' . $followupModel->followup_no . ' เรียบร้อยแล้ว');
    }

    public function destroy(int $client, int $followup): RedirectResponse
    {
        $this->authorizeAction('delete');
        $clientModel = $this->findAuthorizedClient($client);
        $followupModel = $this->followupForClient($clientModel->id, $followup);

        if ($followupModel->plan?->status !== DevelopmentPlan::STATUS_ACTIVE || !$this->isLatest($followupModel)) {
            abort(422, 'เพื่อรักษาลำดับประวัติ สามารถลบได้เฉพาะการติดตามล่าสุดของแผนที่กำลังดำเนินการ');
        }

        if ($this->lifecycle->hasAchievedGoals($followupModel->plan)) {
            return redirect()->route('individual-development.followups.show', [$clientModel->id, $followupModel->id])
                ->with('warning', 'มีเป้าหมายที่ยืนยัน “บรรลุแล้ว” อ้างอิงประวัติการติดตามอยู่ กรุณาเปิดเป้าหมายนั้นอีกครั้งก่อนจึงจะแก้ไขลำดับประวัติได้');
        }

        $followupNo = $followupModel->followup_no;
        $followupModel->delete();

        return redirect()->route('individual-development.index', $clientModel->id)
            ->with('success', 'ลบการติดตามครั้งที่ ' . $followupNo . ' เรียบร้อยแล้ว');
    }

    private function validateFollowup(Request $request, Collection $domains, DevelopmentPlan $plan, ?DevelopmentFollowup $editingFollowup = null, array $previousScores = []): array
    {
        $minimumDate = $this->minimumFollowupDate($plan, $editingFollowup);

        $followupDateRule = Rule::unique('individual_development_followups', 'followup_date')
            ->where(fn ($query) => $query
                ->where('plan_id', $plan->id)
                ->whereNull('deleted_at'));
        if ($editingFollowup) {
            $followupDateRule->ignore($editingFollowup->id);
        }

        $rules = [
            'followup_date' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:' . $minimumDate, $followupDateRule],
            'followup_type' => ['nullable', 'string', Rule::in(array_keys(self::FOLLOWUP_TYPES))],
            'follower_name' => ['nullable', 'string', 'max:255'],
            'current_situation' => ['nullable', 'string', 'max:10000'],
            'changes' => ['nullable', 'string', 'max:10000'],
            'positive_changes' => ['nullable', 'string', 'max:10000'],
            'actions_taken' => ['nullable', 'string', 'max:10000'],
            'result' => ['nullable', 'string', 'max:10000'],
            'problem' => ['nullable', 'string', 'max:10000'],
            'client_feedback' => ['nullable', 'string', 'max:10000'],
            'caregiver_feedback' => ['nullable', 'string', 'max:10000'],
            'overall_result' => ['required', Rule::in(array_keys(self::RESULTS))],
            'suggestion' => ['nullable', 'string', 'max:10000'],
            'next_action' => ['nullable', 'string', 'max:10000'],
            'next_followup_date' => ['nullable', 'date', 'after_or_equal:followup_date'],
            'items' => ['required', 'array'],
        ];

        $attributes = [
            'followup_date' => 'วันที่ติดตาม',
            'overall_result' => 'ผลการติดตามโดยรวม',
            'next_action' => 'สิ่งที่ต้องดำเนินการต่อ',
            'next_followup_date' => 'วันที่ติดตามครั้งถัดไป',
        ];

        foreach ($domains as $domain) {
            foreach ($domain->indicators as $indicator) {
                $id = (int) $indicator->id;
                $rules["items.$id.score"] = ['required', 'integer', 'between:1,5'];
                $rules["items.$id.evidence"] = ['nullable', 'string', 'max:5000'];
                $rules["items.$id.development_note"] = ['nullable', 'string', 'max:5000'];
                $attributes["items.$id.score"] = 'ระดับของตัวชี้วัด “' . $indicator->name . '”';
            }
        }

        $validated = $request->validate($rules, [
            'followup_date.required' => 'กรุณาระบุวันที่ติดตาม',
            'followup_date.before_or_equal' => 'วันที่ติดตามต้องไม่เกินวันปัจจุบัน',
            'followup_date.after_or_equal' => 'วันที่ติดตามต้องไม่น้อยกว่าวันเริ่มแผน และต้องเรียงต่อจากครั้งก่อน',
            'followup_date.unique' => 'วันที่ติดตามนี้ถูกใช้ในแผนปัจจุบันแล้ว กรุณาเลือกวันอื่นเพื่อรักษาลำดับประวัติ',
            'overall_result.required' => 'กรุณาระบุผลการติดตามโดยรวม',
            'next_followup_date.after_or_equal' => 'วันที่ติดตามครั้งถัดไปต้องไม่น้อยกว่าวันที่ติดตามครั้งนี้',
            'items.*.score.required' => 'กรุณาประเมิน :attribute',
            'items.*.score.between' => ':attribute ต้องอยู่ระหว่างระดับ 1 ถึง 5',
        ], $attributes);

        if (($validated['overall_result'] ?? null) !== DevelopmentFollowup::RESULT_ACHIEVED
            && blank($validated['next_action'] ?? null)) {
            throw ValidationException::withMessages([
                'next_action' => 'กรุณาระบุสิ่งที่ต้องดำเนินการต่อ เพื่อให้ผู้รับผิดชอบครั้งถัดไปทำงานต่อได้',
            ]);
        }

        foreach ($this->indicatorIds($domains) as $indicatorId) {
            $previous = $previousScores[$indicatorId] ?? null;
            $current = (int) ($validated['items'][$indicatorId]['score'] ?? 0);
            $evidence = trim((string) ($validated['items'][$indicatorId]['evidence'] ?? ''));

            if ($previous !== null && (int) $previous !== $current && $evidence === '') {
                throw ValidationException::withMessages([
                    "items.$indicatorId.evidence" => 'คะแนนมีการเปลี่ยนแปลงจากครั้งก่อน กรุณาระบุหลักฐาน/พฤติกรรมที่พบเพื่อรองรับการเปลี่ยนคะแนน',
                ]);
            }
        }

        return $validated;
    }

    private function assertAchievedGoalsRemainSatisfied(DevelopmentPlan $plan, array $items): void
    {
        $achievedGoals = DevelopmentGoal::query()
            ->where('plan_id', $plan->id)
            ->where('status', DevelopmentGoal::STATUS_ACHIEVED)
            ->with(['domain.indicators', 'indicator'])
            ->get();

        if ($achievedGoals->isEmpty()) {
            return;
        }

        $scores = collect($items)->mapWithKeys(fn ($item, $indicatorId) => [
            (int) $indicatorId => isset($item['score']) ? (int) $item['score'] : null,
        ])->all();

        foreach ($achievedGoals as $goal) {
            $current = $this->lifecycle->currentLevelForGoal($goal, $scores);
            if ($current !== null && $goal->target_level !== null && $current < (int) $goal->target_level) {
                throw ValidationException::withMessages([
                    'items' => 'การแก้ไขนี้จะทำให้เป้าหมาย “' . $goal->title . '” ที่ยืนยันบรรลุแล้วต่ำกว่าระดับเป้าหมาย กรุณาเปิดเป้าหมายนั้นอีกครั้งก่อนแก้ไขประวัติการติดตาม',
                ]);
            }
        }
    }

    private function minimumFollowupDate(DevelopmentPlan $plan, ?DevelopmentFollowup $editingFollowup = null): string
    {
        $query = DevelopmentFollowup::query()->where('plan_id', $plan->id);

        if ($editingFollowup) {
            $query->where('followup_no', '<', $editingFollowup->followup_no);
        }

        $previousDate = $query->orderByDesc('followup_no')->value('followup_date');

        if ($previousDate) {
            try {
                return Carbon::parse($previousDate, 'Asia/Bangkok')->addDay()->format('Y-m-d');
            } catch (\Throwable $e) {
                // ใช้วันเริ่มแผนเป็น fallback
            }
        }

        return optional($plan->start_date)->format('Y-m-d')
            ?? now('Asia/Bangkok')->format('Y-m-d');
    }

    private function previousScores(int $planId, DevelopmentAssessment $baseline): array
    {
        $latest = DevelopmentFollowup::query()
            ->where('plan_id', $planId)
            ->with('items')
            ->orderByDesc('followup_no')
            ->first();

        $items = $latest?->items ?? $baseline->items;

        return $items->mapWithKeys(fn ($item) => [(int) $item->indicator_id => $item->score !== null ? (int) $item->score : null])->all();
    }

    private function domainSummaries(Collection $domains, Collection $items): Collection
    {
        $itemsByIndicator = $items->keyBy('indicator_id');

        return $domains->map(function ($domain) use ($itemsByIndicator): array {
            $current = collect();
            $previous = collect();
            foreach ($domain->indicators as $indicator) {
                $item = $itemsByIndicator->get($indicator->id);
                if ($item?->score !== null) $current->push((float) $item->score);
                if ($item?->previous_score !== null) $previous->push((float) $item->previous_score);
            }

            $currentAvg = $current->isNotEmpty() ? round((float) $current->avg(), 2) : null;
            $previousAvg = $previous->isNotEmpty() ? round((float) $previous->avg(), 2) : null;
            $delta = ($currentAvg !== null && $previousAvg !== null) ? round($currentAvg - $previousAvg, 2) : null;
            $meta = $this->scoreLevel($currentAvg);

            return [
                'id' => $domain->id,
                'code' => $domain->code,
                'name' => $domain->name,
                'previous' => $previousAvg,
                'current' => $currentAvg,
                'delta' => $delta,
                'trend' => $delta === null ? 'none' : ($delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'same')),
                'level' => $meta['level'],
                'label' => $meta['label'],
            ];
        });
    }

    private function domains(): Collection
    {
        return DevelopmentDomain::query()
            ->where('is_active', true)
            ->with(['indicators' => fn ($query) => $query
                ->where('is_active', true)
                ->with('rubrics')
                ->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }

    private function indicatorIds(Collection $domains): array
    {
        return $domains->flatMap(fn ($domain) => $domain->indicators)
            ->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
    }

    private function baselineForPlan(int $planId): ?DevelopmentAssessment
    {
        return DevelopmentAssessment::query()
            ->where('plan_id', $planId)
            ->where('assessment_type', DevelopmentAssessment::TYPE_BASELINE)
            ->with('items.indicator.domain')
            ->orderByDesc('round_no')
            ->first();
    }

    private function latestFollowup(int $planId): ?DevelopmentFollowup
    {
        return DevelopmentFollowup::query()
            ->where('plan_id', $planId)
            ->orderByDesc('followup_no')
            ->first();
    }

    private function activePlan(int $clientId): ?DevelopmentPlan
    {
        return DevelopmentPlan::query()
            ->where('client_id', $clientId)
            ->where('status', DevelopmentPlan::STATUS_ACTIVE)
            ->orderByDesc('plan_no')
            ->first();
    }

    private function followupForClient(int $clientId, int $followupId): DevelopmentFollowup
    {
        return DevelopmentFollowup::query()
            ->whereKey($followupId)
            ->where('client_id', $clientId)
            ->with(['plan', 'items.indicator.domain', 'items.indicator.rubrics', 'follower'])
            ->firstOrFail();
    }

    private function isLatest(DevelopmentFollowup $followup): bool
    {
        $latestId = DevelopmentFollowup::query()
            ->where('plan_id', $followup->plan_id)
            ->orderByDesc('followup_no')
            ->value('id');

        return (int) $latestId === (int) $followup->id;
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

    private function scoreLevel(?float $score): array
    {
        if ($score === null) return ['level' => null, 'label' => 'ยังไม่ประเมิน'];
        return match (true) {
            $score < 1.5 => ['level' => 1, 'label' => 'ต้องส่งเสริมเร่งด่วน'],
            $score < 2.5 => ['level' => 2, 'label' => 'ควรส่งเสริม'],
            $score < 3.5 => ['level' => 3, 'label' => 'ตามเกณฑ์'],
            $score < 4.5 => ['level' => 4, 'label' => 'ดี'],
            default => ['level' => 5, 'label' => 'ดีมาก'],
        };
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
