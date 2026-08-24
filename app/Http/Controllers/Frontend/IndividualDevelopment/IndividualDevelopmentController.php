<?php

namespace App\Http\Controllers\Frontend\IndividualDevelopment;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\IndividualDevelopment\ClientDocumentStatus;
use App\Models\IndividualDevelopment\DevelopmentActivity;
use App\Models\IndividualDevelopment\DevelopmentCoordination;
use App\Models\IndividualDevelopment\DevelopmentAssessment;
use App\Models\IndividualDevelopment\DevelopmentDomain;
use App\Models\IndividualDevelopment\DevelopmentGoal;
use App\Models\IndividualDevelopment\DevelopmentPlan;
use App\Services\IndividualDevelopment\IndividualDevelopmentLifecycleService;
use App\Services\IndividualDevelopment\IndividualDevelopmentSummaryService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class IndividualDevelopmentController extends Controller
{
    private const PERMISSION_KEY = 'individual_development';

    public function __construct(
        private readonly IndividualDevelopmentLifecycleService $lifecycle,
        private readonly IndividualDevelopmentSummaryService $summaryService
    ) {
    }

    public function index(Request $request, int $client): View
    {
        $this->authorizeAction('view');
        $clientModel = $this->findAuthorizedClient($client);

        $plans = DevelopmentPlan::query()
            ->where('client_id', $clientModel->id)
            ->with([
                'assessments.items.indicator.domain',
                'goals.domain.indicators',
                'goals.indicator',
                'goals.activities',
                'followups.items.indicator.domain',
            ])
            ->orderByDesc('plan_no')
            ->get();

        $requestedPlanId = $request->integer('plan');
        $activePlan = $plans->firstWhere('status', DevelopmentPlan::STATUS_ACTIVE);
        $currentPlan = $requestedPlanId > 0
            ? $plans->firstWhere('id', $requestedPlanId)
            : ($activePlan ?? $plans->first());

        // หากมี plan id ที่ไม่ใช่ของผู้รับบริการ ให้กลับมาเลือกแผนปัจจุบันอย่างปลอดภัย
        if (!$currentPlan && $plans->isNotEmpty()) {
            $currentPlan = $activePlan ?? $plans->first();
        }

        $baselineAssessment = null;
        $latestAssessment = null;
        $latestFollowup = null;
        $domainScores = collect();
        $goalStats = [
            'total' => 0,
            'achieved' => 0,
            'in_progress' => 0,
            'partial' => 0,
            'not_started' => 0,
            'cancelled' => 0,
        ];
        $goalProgress = [];
        $closeBlockers = [];

        if ($currentPlan) {
            $baselineAssessment = $currentPlan->assessments
                ->where('assessment_type', DevelopmentAssessment::TYPE_BASELINE)
                ->sortByDesc('assessment_date')
                ->sortByDesc('round_no')
                ->first();

            $latestAssessment = $currentPlan->assessments
                ->sortByDesc(function ($assessment): string {
                    $date = optional($assessment->assessment_date)->format('Ymd') ?? '00000000';
                    $round = str_pad((string) $assessment->round_no, 6, '0', STR_PAD_LEFT);
                    return $date . $round;
                })
                ->first();

            $latestFollowup = $currentPlan->followups->sortByDesc('followup_no')->first();

            $goals = $currentPlan->goals;
            $goalStats = [
                'total' => $goals->count(),
                'achieved' => $goals->where('status', DevelopmentGoal::STATUS_ACHIEVED)->count(),
                'in_progress' => $goals->where('status', DevelopmentGoal::STATUS_IN_PROGRESS)->count(),
                'partial' => $goals->where('status', DevelopmentGoal::STATUS_PARTIAL)->count(),
                'not_started' => $goals->where('status', DevelopmentGoal::STATUS_NOT_STARTED)->count(),
                'cancelled' => $goals->where('status', DevelopmentGoal::STATUS_CANCELLED)->count(),
            ];
            $goalProgress = $this->lifecycle->goalProgressMap($currentPlan, $goals);
            $closeBlockers = $this->lifecycle->closeBlockers($currentPlan);
        }

        $domains = DevelopmentDomain::query()
            ->where('is_active', true)
            ->with(['indicators' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $baselineScores = $baselineAssessment?->items?->keyBy('indicator_id') ?? collect();
        $followupDate = $latestFollowup?->followup_date;
        $assessmentDate = $latestAssessment?->assessment_date;
        $useFollowupAsLatest = $latestFollowup && (!$assessmentDate || ($followupDate && $followupDate->gte($assessmentDate)));
        $latestItems = $useFollowupAsLatest
            ? ($latestFollowup?->items?->keyBy('indicator_id') ?? collect())
            : ($latestAssessment?->items?->keyBy('indicator_id') ?? collect());

        foreach ($domains as $domain) {
            $baselineValues = collect();
            $latestValues = collect();

            foreach ($domain->indicators as $indicator) {
                $baselineItem = $baselineScores->get($indicator->id);
                $latestItem = $latestItems->get($indicator->id);
                if ($baselineItem?->score !== null) $baselineValues->push((float) $baselineItem->score);
                if ($latestItem?->score !== null) $latestValues->push((float) $latestItem->score);
            }

            $baselineScore = $baselineValues->isNotEmpty() ? round((float) $baselineValues->avg(), 2) : null;
            $latestScore = $latestValues->isNotEmpty() ? round((float) $latestValues->avg(), 2) : null;
            $delta = ($baselineScore !== null && $latestScore !== null) ? round($latestScore - $baselineScore, 2) : null;
            $level = $this->scoreLevel($latestScore);

            $domainScores->push([
                'id' => $domain->id,
                'code' => $domain->code,
                'name' => $domain->name,
                'description' => $domain->description,
                'baseline_score' => $baselineScore,
                'score' => $latestScore,
                'delta' => $delta,
                'trend' => $delta === null ? 'none' : ($delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'same')),
                'level' => $level['level'],
                'level_label' => $level['label'],
                'indicator_count' => $domain->indicators->count(),
            ]);
        }

        $hasActivePlan = $plans->contains('status', DevelopmentPlan::STATUS_ACTIVE);
        $selectedIsActive = $currentPlan?->status === DevelopmentPlan::STATUS_ACTIVE;
        $hasWritePermission = $this->can(self::PERMISSION_KEY, 'create')
            || $this->can(self::PERMISSION_KEY, 'update')
            || $this->can(self::PERMISSION_KEY, 'delete');
        $readOnly = (bool) $request->attributes->get('form_permission_readonly', false) || !$hasWritePermission;
        $canUpdate = $this->can(self::PERMISSION_KEY, 'update') && !$readOnly;
        $canDelete = $this->can(self::PERMISSION_KEY, 'delete') && !$readOnly;
        $canCreate = $this->can(self::PERMISSION_KEY, 'create') && !$readOnly;

        $nextTasks = collect();
        if ($currentPlan && $selectedIsActive) {
            $today = Carbon::today('Asia/Bangkok');
            foreach ($currentPlan->goals as $goal) {
                if (in_array($goal->status, [DevelopmentGoal::STATUS_ACHIEVED, DevelopmentGoal::STATUS_CANCELLED], true)) continue;
                if ($goal->target_date) {
                    $date = Carbon::parse($goal->target_date, 'Asia/Bangkok')->startOfDay();
                    $nextTasks->push([
                        'date' => $date,
                        'type' => 'goal',
                        'title' => 'กำหนดเป้าหมาย: ' . $goal->title,
                        'detail' => $goal->responsible_name ? 'ผู้รับผิดชอบ: ' . $goal->responsible_name : null,
                        'urgency' => $date->lt($today) ? 'overdue' : ($date->lte($today->copy()->addDays(7)) ? 'soon' : 'normal'),
                    ]);
                }
            }
            $latestFu = $currentPlan->followups->sortByDesc('followup_no')->first();
            if ($latestFu && ($latestFu->next_action || $latestFu->next_followup_date)) {
                $date = $latestFu->next_followup_date ? Carbon::parse($latestFu->next_followup_date, 'Asia/Bangkok')->startOfDay() : null;
                $nextTasks->push([
                    'date' => $date,
                    'type' => 'followup',
                    'title' => $latestFu->next_action ?: 'ติดตามผลครั้งถัดไป',
                    'detail' => $latestFu->next_followup_date ? 'นัดติดตามครั้งถัดไป' : 'ยังไม่ได้กำหนดวัน',
                    'urgency' => !$date ? 'normal' : ($date->lt($today) ? 'overdue' : ($date->lte($today->copy()->addDays(7)) ? 'soon' : 'normal')),
                ]);
            }
            $nextTasks = $nextTasks->sortBy(fn ($task) => $task['date']?->timestamp ?? PHP_INT_MAX)->values();
        }

        $caseSummary = $this->summaryService->build($currentPlan);

        $referrals = $clientModel->refers()->with('translate')->orderByDesc('refer_date')->orderByDesc('id')->limit(10)->get();
        $coordinations = DevelopmentCoordination::query()->where('client_id', $clientModel->id)
            ->orderByDesc('coordination_date')->orderByDesc('id')->limit(20)->get();
        $documentTypes = [
            'id_card' => 'บัตรประชาชน',
            'house_registration' => 'ทะเบียนบ้าน',
            'birth_certificate' => 'สูติบัตร',
            'education_document' => 'เอกสารการศึกษา',
            'medical_certificate' => 'ใบรับรองแพทย์',
            'consent_form' => 'หนังสือยินยอม',
            'passport' => 'หนังสือเดินทาง',
            'court_order' => 'คำสั่งศาล',
        ];
        $documentStatuses = ClientDocumentStatus::query()->where('client_id', $clientModel->id)->get()->keyBy('document_type');
        $uploadedFileTypes = $clientModel->files()->select('file_type')->distinct()->pluck('file_type')->all();

        return view('frontend.client.individual_development.index', [
            'client' => $clientModel,
            'plans' => $plans,
            'currentPlan' => $currentPlan,
            'activePlan' => $activePlan,
            'baselineAssessment' => $baselineAssessment,
            'latestAssessment' => $latestAssessment,
            'latestFollowup' => $latestFollowup,
            'domainScores' => $domainScores,
            'goalStats' => $goalStats,
            'goalProgress' => $goalProgress,
            'closeBlockers' => $closeBlockers,
            'ageText' => $this->resolveAgeText($clientModel),
            'readOnly' => $readOnly,
            'canCreatePlan' => $canCreate && !$hasActivePlan,
            'canEditPlan' => (bool) $currentPlan && $selectedIsActive && $canUpdate,
            'canEditProfile' => (bool) $currentPlan && $selectedIsActive && $canUpdate,
            'canDeletePlan' => (bool) $currentPlan && $selectedIsActive && $canDelete
                && !$baselineAssessment
                && $currentPlan->goals->isEmpty()
                && $currentPlan->followups->isEmpty(),
            'canCancelPlan' => (bool) $currentPlan && $selectedIsActive && $canUpdate,
            'canClosePlan' => (bool) $currentPlan && $selectedIsActive && $canUpdate && $closeBlockers === [],
            'canCreateBaseline' => (bool) $currentPlan
                && !$baselineAssessment
                && $selectedIsActive
                && $canCreate,
            'canUpdateBaseline' => (bool) $baselineAssessment
                && $selectedIsActive
                && $canUpdate
                && !$this->lifecycle->baselineLocked($currentPlan),
            'canCreateGoal' => (bool) $baselineAssessment && $selectedIsActive && $canCreate,
            'canCreateFollowup' => (bool) $baselineAssessment
                && $currentPlan?->goals->isNotEmpty()
                && $selectedIsActive
                && $canCreate,
            'canCreateOutcome' => (bool) $baselineAssessment && $selectedIsActive && $canCreate,
            'latestEvaluationLabel' => $latestFollowup
                ? 'อ้างอิงการติดตามล่าสุด ครั้งที่ ' . $latestFollowup->followup_no . ' วันที่ ' . $this->thaiDateText($latestFollowup->followup_date)
                : ($latestAssessment ? 'อ้างอิง Baseline วันที่ ' . $this->thaiDateText($latestAssessment->assessment_date) : 'ยังไม่มีผลประเมิน'),
            'canPrintReport' => (bool) $currentPlan && $this->can(self::PERMISSION_KEY, 'print'),
            'nextTasks' => $nextTasks,
            'caseSummary' => $caseSummary,
            'referrals' => $referrals,
            'coordinations' => $coordinations,
            'documentTypes' => $documentTypes,
            'documentStatuses' => $documentStatuses,
            'uploadedFileTypes' => $uploadedFileTypes,
            'canUpdateSupplement' => (bool) $currentPlan && $selectedIsActive && $canUpdate,
            'canUpdateDocuments' => $canUpdate,
            'canCreateCoordination' => $canCreate,
            'canUpdateCoordination' => $canUpdate,
            'canDeleteCoordination' => $canDelete,
        ]);
    }

    public function create(int $client): View|RedirectResponse
    {
        $this->authorizeAction('create');
        $clientModel = $this->findAuthorizedClient($client);

        if (DevelopmentPlan::query()->where('client_id', $clientModel->id)->where('status', DevelopmentPlan::STATUS_ACTIVE)->exists()) {
            return redirect()->route('individual-development.index', $clientModel->id)
                ->with('warning', 'ผู้รับบริการรายนี้มีแผนพัฒนาที่กำลังดำเนินการอยู่แล้ว กรุณาดำเนินการแผนปัจจุบันให้เสร็จก่อนสร้างแผนใหม่');
        }

        $nextPlanNo = ((int) DevelopmentPlan::query()->where('client_id', $clientModel->id)->max('plan_no')) + 1;

        return view('frontend.client.individual_development.create', [
            'client' => $clientModel,
            'plan' => null,
            'nextPlanNo' => $nextPlanNo,
            'ageText' => $this->resolveAgeText($clientModel),
            'mode' => 'create',
            'lockStartDate' => false,
        ]);
    }

    public function store(Request $request, int $client): RedirectResponse
    {
        $this->authorizeAction('create');
        $clientModel = $this->findAuthorizedClient($client);
        $validated = $this->validatePlan($request);

        DB::transaction(function () use ($clientModel, $validated): void {
            $activePlanExists = DevelopmentPlan::query()
                ->where('client_id', $clientModel->id)
                ->where('status', DevelopmentPlan::STATUS_ACTIVE)
                ->lockForUpdate()
                ->exists();
            if ($activePlanExists) abort(422, 'มีแผนพัฒนาที่กำลังดำเนินการอยู่แล้ว');

            $nextPlanNo = ((int) DevelopmentPlan::query()
                ->where('client_id', $clientModel->id)
                ->lockForUpdate()
                ->max('plan_no')) + 1;

            DevelopmentPlan::create($this->planPayload($validated) + [
                'client_id' => $clientModel->id,
                'plan_no' => $nextPlanNo,
                'status' => DevelopmentPlan::STATUS_ACTIVE,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        });

        return redirect()->route('individual-development.index', $clientModel->id)
            ->with('success', 'สร้างแผนพัฒนารายบุคคลเรียบร้อยแล้ว ขั้นตอนถัดไปคือบันทึกจุดแข็ง/ความต้องการ แล้วจึงประเมิน Baseline');
    }

    public function edit(int $client): View|RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) {
            return redirect()->route('individual-development.index', $clientModel->id)
                ->with('warning', 'ไม่มีแผนที่กำลังดำเนินการสำหรับแก้ไข');
        }

        return view('frontend.client.individual_development.create', [
            'client' => $clientModel,
            'plan' => $plan,
            'nextPlanNo' => $plan->plan_no,
            'ageText' => $this->resolveAgeText($clientModel),
            'mode' => 'edit',
            'lockStartDate' => $plan->assessments()->where('assessment_type', DevelopmentAssessment::TYPE_BASELINE)->exists(),
        ]);
    }

    public function update(Request $request, int $client): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) abort(422, 'ไม่มีแผนที่กำลังดำเนินการสำหรับแก้ไข');

        $lockStartDate = $plan->assessments()->where('assessment_type', DevelopmentAssessment::TYPE_BASELINE)->exists();
        $validated = $this->validatePlan($request, $plan, $lockStartDate);
        $payload = $this->planPayload($validated);
        if ($lockStartDate) {
            $payload['start_date'] = optional($plan->start_date)->format('Y-m-d');
        }
        $payload['updated_by'] = auth()->id();
        $plan->update($payload);

        return redirect()->route('individual-development.index', $clientModel->id)
            ->with('success', 'ปรับปรุงข้อมูลแผนพัฒนารายบุคคลเรียบร้อยแล้ว');
    }

    public function updateProfile(Request $request, int $client): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) {
            return redirect()->route('individual-development.index', $clientModel->id)
                ->with('warning', 'ไม่มีแผนที่กำลังดำเนินการ จึงไม่สามารถแก้ไขจุดแข็งและความต้องการได้');
        }

        $validated = $request->validate([
            'strength_profile' => ['nullable', 'array'],
            'strength_profile.*' => ['nullable', 'string', 'max:2000'],
            'strength_summary' => ['nullable', 'string', 'max:10000'],
            'needs_profile' => ['nullable', 'array'],
            'needs_profile.*.detail' => ['nullable', 'string', 'max:5000'],
            'needs_profile.*.priority' => ['nullable', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'needs_profile.*.client_view' => ['nullable', 'string', 'max:5000'],
            'needs_profile.*.staff_view' => ['nullable', 'string', 'max:5000'],
            'development_need_summary' => ['nullable', 'string', 'max:10000'],
            'client_need_summary' => ['nullable', 'string', 'max:10000'],
            'caregiver_need_summary' => ['nullable', 'string', 'max:10000'],
            'risk_factor_summary' => ['nullable', 'string', 'max:10000'],
            'protective_factor_summary' => ['nullable', 'string', 'max:10000'],
            'support_network_summary' => ['nullable', 'string', 'max:10000'],
        ]);

        $plan->update([
            'strength_profile' => $this->cleanStrengthProfile($validated['strength_profile'] ?? []),
            'strength_summary' => $this->nullableText($validated['strength_summary'] ?? null),
            'needs_profile' => $this->cleanNeedsProfile($validated['needs_profile'] ?? []),
            'development_need_summary' => $this->nullableText($validated['development_need_summary'] ?? null),
            'client_need_summary' => $this->nullableText($validated['client_need_summary'] ?? null),
            'caregiver_need_summary' => $this->nullableText($validated['caregiver_need_summary'] ?? null),
            'risk_factor_summary' => $this->nullableText($validated['risk_factor_summary'] ?? null),
            'protective_factor_summary' => $this->nullableText($validated['protective_factor_summary'] ?? null),
            'support_network_summary' => $this->nullableText($validated['support_network_summary'] ?? null),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('individual-development.index', $clientModel->id)
            ->with('success', 'บันทึกจุดแข็ง ศักยภาพ และความต้องการเรียบร้อยแล้ว');
    }

    public function destroy(int $client): RedirectResponse
    {
        $this->authorizeAction('delete');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) abort(422, 'ไม่มีแผนที่กำลังดำเนินการ');

        $hasData = $plan->assessments()->exists() || $plan->goals()->exists() || $plan->followups()->exists();
        if ($hasData) {
            return back()->with('warning', 'แผนนี้มี Baseline/เป้าหมาย/การติดตามแล้ว จึงไม่อนุญาตให้ลบ กรุณาใช้ “ยุติแผน” เพื่อรักษาประวัติ');
        }

        $plan->delete();
        return redirect()->route('individual-development.index', $clientModel->id)
            ->with('success', 'ลบแผนที่ยังไม่มีข้อมูลต่อเนื่องเรียบร้อยแล้ว');
    }

    public function closeForm(int $client): View|RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) {
            return redirect()->route('individual-development.index', $clientModel->id)
                ->with('warning', 'ไม่มีแผนที่กำลังดำเนินการ');
        }
        $plan->load(['assessments.items.indicator.domain', 'goals.domain.indicators', 'goals.indicator', 'goals.activities', 'followups.items.indicator.domain']);
        $blockers = $this->lifecycle->closeBlockers($plan);
        if ($blockers !== []) {
            return redirect()->route('individual-development.index', $clientModel->id)
                ->with('warning', 'ยังปิดแผนไม่ได้: ' . implode(' / ', $blockers));
        }

        return view('frontend.client.individual_development.plan.close', [
            'client' => $clientModel,
            'plan' => $plan,
            'ageText' => $this->resolveAgeText($clientModel),
            'goalProgress' => $this->lifecycle->goalProgressMap($plan, $plan->goals),
        ]);
    }

    public function close(Request $request, int $client): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) abort(422, 'ไม่มีแผนที่กำลังดำเนินการ');
        $plan->load(['assessments.items.indicator.domain', 'goals.activities', 'followups.items.indicator.domain']);

        $blockers = $this->lifecycle->closeBlockers($plan);
        if ($blockers !== []) {
            return back()->with('warning', 'ยังปิดแผนไม่ได้: ' . implode(' / ', $blockers));
        }

        $validated = $request->validate([
            'close_reason' => ['required', 'string', 'max:10000'],
            'final_outcome' => ['required', 'string', 'max:10000'],
            'final_recommendation' => ['nullable', 'string', 'max:10000'],
        ], [
            'close_reason.required' => 'กรุณาระบุเหตุผล/เกณฑ์ที่ใช้ในการปิดแผน',
            'final_outcome.required' => 'กรุณาสรุปผลลัพธ์สุดท้ายของแผน',
        ]);

        $plan->update([
            'status' => DevelopmentPlan::STATUS_COMPLETED,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now('Asia/Bangkok'),
            'closed_by' => auth()->id(),
            'closed_at' => now('Asia/Bangkok'),
            'close_reason' => trim($validated['close_reason']),
            'final_outcome' => trim($validated['final_outcome']),
            'final_recommendation' => $this->nullableText($validated['final_recommendation'] ?? null),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('individual-development.index', ['client' => $clientModel->id, 'plan' => $plan->id])
            ->with('success', 'ปิดแผนพัฒนารายบุคคลเรียบร้อยแล้ว ข้อมูลถูกเก็บเป็นประวัติและพร้อมออกรายงานสรุป');
    }

    public function cancel(Request $request, int $client): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) abort(422, 'ไม่มีแผนที่กำลังดำเนินการ');

        $validated = $request->validate([
            'close_reason' => ['required', 'string', 'max:10000'],
            'final_outcome' => ['nullable', 'string', 'max:10000'],
            'final_recommendation' => ['nullable', 'string', 'max:10000'],
        ], ['close_reason.required' => 'กรุณาระบุเหตุผลที่ยุติแผน']);

        DB::transaction(function () use ($plan, $validated): void {
            $reason = trim($validated['close_reason']);
            $plan->goals()->whereIn('status', [
                DevelopmentGoal::STATUS_NOT_STARTED,
                DevelopmentGoal::STATUS_IN_PROGRESS,
                DevelopmentGoal::STATUS_PARTIAL,
            ])->update([
                'status' => DevelopmentGoal::STATUS_CANCELLED,
                'cancel_reason' => 'ยุติตามแผน: ' . $reason,
                'cancelled_at' => now('Asia/Bangkok'),
                'cancelled_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            DevelopmentActivity::query()
                ->whereHas('goal', fn ($query) => $query->where('plan_id', $plan->id))
                ->whereIn('status', [DevelopmentActivity::STATUS_PLANNED, DevelopmentActivity::STATUS_IN_PROGRESS])
                ->update([
                    'status' => DevelopmentActivity::STATUS_CANCELLED,
                    'cancel_reason' => 'ยุติตามแผน: ' . $reason,
                    'cancelled_at' => now('Asia/Bangkok'),
                    'cancelled_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

            $plan->update([
                'status' => DevelopmentPlan::STATUS_CANCELLED,
                'closed_by' => auth()->id(),
                'closed_at' => now('Asia/Bangkok'),
                'close_reason' => $reason,
                'final_outcome' => $this->nullableText($validated['final_outcome'] ?? null),
                'final_recommendation' => $this->nullableText($validated['final_recommendation'] ?? null),
                'updated_by' => auth()->id(),
            ]);
        });

        return redirect()->route('individual-development.index', ['client' => $clientModel->id, 'plan' => $plan->id])
            ->with('success', 'ยุติแผนเรียบร้อยแล้ว โดยเก็บประวัติทั้งหมดไว้สำหรับตรวจสอบย้อนหลัง');
    }

    private function validatePlan(Request $request, ?DevelopmentPlan $plan = null, bool $lockStartDate = false): array
    {
        $startDate = $lockStartDate && $plan?->start_date
            ? $plan->start_date->format('Y-m-d')
            : (string) $request->input('start_date');

        $rules = [
            'overall_goal' => ['required', 'string', 'max:10000'],
        ];
        if (!$lockStartDate) {
            $rules['start_date'] = ['required', 'date', 'before_or_equal:today'];
        }
        $rules['end_date'] = ['nullable', 'date', 'after_or_equal:' . ($startDate ?: now('Asia/Bangkok')->format('Y-m-d'))];

        return $request->validate($rules, [
            'start_date.required' => 'กรุณาระบุวันที่เริ่มแผน',
            'start_date.date' => 'วันที่เริ่มแผนไม่ถูกต้อง',
            'start_date.before_or_equal' => 'วันที่เริ่มแผนต้องไม่เกินวันปัจจุบัน',
            'end_date.date' => 'วันที่สิ้นสุดแผนไม่ถูกต้อง',
            'end_date.after_or_equal' => 'วันที่สิ้นสุดแผนต้องไม่น้อยกว่าวันที่เริ่มแผน',
            'overall_goal.required' => 'กรุณาระบุเป้าหมายภาพรวมของแผน',
        ]);
    }

    /**
     * ข้อมูลกรอบแผนเท่านั้น
     * จุดแข็ง/ความต้องการแก้ไขผ่าน updateProfile() เพื่อไม่ให้หน้าแก้แผน
     * ล้างข้อมูล Profile เดิมโดยไม่ตั้งใจ
     */
    private function planPayload(array $validated): array
    {
        return [
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'overall_goal' => trim($validated['overall_goal']),
        ];
    }

    private function activePlan(int $clientId): ?DevelopmentPlan
    {
        return DevelopmentPlan::query()->where('client_id', $clientId)
            ->where('status', DevelopmentPlan::STATUS_ACTIVE)
            ->orderByDesc('plan_no')->first();
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

    private function thaiDateText(mixed $value): string
    {
        if (!$value) return '-';
        try {
            $date = $value instanceof \Carbon\CarbonInterface ? $value : Carbon::parse($value, 'Asia/Bangkok');
            return $date->format('d/m/') . ($date->year + 543);
        } catch (\Throwable $e) {
            return '-';
        }
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

    private function cleanStrengthProfile(array $profile): ?array
    {
        $clean=[];
        foreach($profile as $key=>$value){ $text=$this->nullableText($value); if($text!==null)$clean[$key]=$text; }
        return $clean ?: null;
    }

    private function cleanNeedsProfile(array $profile): ?array
    {
        $clean=[];
        foreach($profile as $key=>$item){
            if(!is_array($item)) continue;
            $row=[
                'detail'=>$this->nullableText($item['detail']??null),
                'priority'=>$item['priority']??null,
                'client_view'=>$this->nullableText($item['client_view']??null),
                'staff_view'=>$this->nullableText($item['staff_view']??null),
            ];
            if(array_filter($row,fn($v)=>$v!==null && $v!=='')) $clean[$key]=$row;
        }
        return $clean ?: null;
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
