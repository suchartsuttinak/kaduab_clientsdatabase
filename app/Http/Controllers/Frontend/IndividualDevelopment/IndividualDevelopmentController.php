<?php

namespace App\Http\Controllers\Frontend\IndividualDevelopment;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\IndividualDevelopment\DevelopmentAssessment;
use App\Models\IndividualDevelopment\DevelopmentDomain;
use App\Models\IndividualDevelopment\DevelopmentPlan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IndividualDevelopmentController extends Controller
{
    private const PERMISSION_KEY = 'individual_development';

    public function index(int $client): View
    {
        $this->authorizeAction('view');
        $clientModel = $this->findAuthorizedClient($client);

        $plans = DevelopmentPlan::query()
            ->where('client_id', $clientModel->id)
            ->with([
                'assessments.items.indicator.domain',
                'goals.domain',
                'goals.indicator',
                'goals.activities',
                'followups.items.indicator.domain',
            ])
            ->orderByDesc('start_date')
            ->orderByDesc('plan_no')
            ->get();

        $currentPlan = $plans->firstWhere('status', DevelopmentPlan::STATUS_ACTIVE)
            ?? $plans->first();

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
        ];

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

            $latestFollowup = $currentPlan->followups
                ->sortByDesc('followup_no')
                ->first();

            $goals = $currentPlan->goals;
            $goalStats = [
                'total' => $goals->count(),
                'achieved' => $goals->where('status', 'achieved')->count(),
                'in_progress' => $goals->where('status', 'in_progress')->count(),
                'partial' => $goals->where('status', 'partial')->count(),
                'not_started' => $goals->where('status', 'not_started')->count(),
            ];
        }

        $domains = DevelopmentDomain::query()
            ->where('is_active', true)
            ->with(['indicators' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        foreach ($domains as $domain) {
            $score = null;

            if ($latestFollowup) {
                $scores = $latestFollowup->items
                    ->filter(fn ($item) => (int) optional($item->indicator)->domain_id === (int) $domain->id)
                    ->pluck('score')
                    ->filter(fn ($value) => $value !== null)
                    ->map(fn ($value) => (float) $value);

                if ($scores->isNotEmpty()) {
                    $score = round((float) $scores->avg(), 2);
                }
            } elseif ($latestAssessment) {
                $scores = $latestAssessment->items
                    ->filter(fn ($item) => (int) optional($item->indicator)->domain_id === (int) $domain->id)
                    ->pluck('score')
                    ->filter(fn ($value) => $value !== null)
                    ->map(fn ($value) => (float) $value);

                if ($scores->isNotEmpty()) {
                    $score = round((float) $scores->avg(), 2);
                }
            }

            $level = $this->scoreLevel($score);

            $domainScores->push([
                'id' => $domain->id,
                'code' => $domain->code,
                'name' => $domain->name,
                'description' => $domain->description,
                'score' => $score,
                'level' => $level['level'],
                'level_label' => $level['label'],
                'indicator_count' => $domain->indicators->count(),
            ]);
        }

        return view('frontend.client.individual_development.index', [
            'client' => $clientModel,
            'plans' => $plans,
            'currentPlan' => $currentPlan,
            'baselineAssessment' => $baselineAssessment,
            'latestAssessment' => $latestAssessment,
            'latestFollowup' => $latestFollowup,
            'domainScores' => $domainScores,
            'goalStats' => $goalStats,
            'ageText' => $this->resolveAgeText($clientModel),
            'canCreatePlan' => $this->can(self::PERMISSION_KEY, 'create')
                && !$plans->contains('status', DevelopmentPlan::STATUS_ACTIVE),
            'canCreateBaseline' => (bool) $currentPlan
                && !$baselineAssessment
                && $currentPlan->status === DevelopmentPlan::STATUS_ACTIVE
                && $this->can(self::PERMISSION_KEY, 'create'),
            'canUpdateBaseline' => (bool) $baselineAssessment
                && $currentPlan?->status === DevelopmentPlan::STATUS_ACTIVE
                && $this->can(self::PERMISSION_KEY, 'update'),
            'canCreateGoal' => (bool) $baselineAssessment
                && $currentPlan?->status === DevelopmentPlan::STATUS_ACTIVE
                && $this->can(self::PERMISSION_KEY, 'create'),
            'canCreateFollowup' => (bool) $baselineAssessment
                && $currentPlan?->status === DevelopmentPlan::STATUS_ACTIVE
                && $this->can(self::PERMISSION_KEY, 'create'),
            'latestEvaluationLabel' => $latestFollowup
                ? 'อ้างอิงการติดตามล่าสุด ครั้งที่ ' . $latestFollowup->followup_no . ' วันที่ ' . $this->thaiDateText($latestFollowup->followup_date)
                : ($latestAssessment ? 'อ้างอิง Baseline วันที่ ' . $this->thaiDateText($latestAssessment->assessment_date) : 'ยังไม่มีผลประเมิน'),
            'canPrintReport' => (bool) $currentPlan
                && $this->can(self::PERMISSION_KEY, 'print'),
        ]);
    }

    public function create(int $client): View|RedirectResponse
    {
        $this->authorizeAction('create');
        $clientModel = $this->findAuthorizedClient($client);

        $activePlanExists = DevelopmentPlan::query()
            ->where('client_id', $clientModel->id)
            ->where('status', DevelopmentPlan::STATUS_ACTIVE)
            ->exists();

        if ($activePlanExists) {
            return redirect()
                ->route('individual-development.index', $clientModel->id)
                ->with('warning', 'ผู้รับบริการรายนี้มีแผนพัฒนาที่กำลังดำเนินการอยู่แล้ว กรุณาดำเนินการแผนปัจจุบันให้เสร็จก่อนสร้างแผนใหม่');
        }

        $nextPlanNo = ((int) DevelopmentPlan::query()
            ->where('client_id', $clientModel->id)
            ->max('plan_no')) + 1;

        return view('frontend.client.individual_development.create', [
            'client' => $clientModel,
            'nextPlanNo' => $nextPlanNo,
            'ageText' => $this->resolveAgeText($clientModel),
        ]);
    }

    public function store(Request $request, int $client): RedirectResponse
    {
        $this->authorizeAction('create');
        $clientModel = $this->findAuthorizedClient($client);

        $validated = $request->validate([
            'start_date' => ['required', 'date', 'before_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'overall_goal' => ['required', 'string', 'max:10000'],
            'strength_summary' => ['nullable', 'string', 'max:10000'],
            'development_need_summary' => ['nullable', 'string', 'max:10000'],
            'client_need_summary' => ['nullable', 'string', 'max:10000'],
            'caregiver_need_summary' => ['nullable', 'string', 'max:10000'],
            'risk_factor_summary' => ['nullable', 'string', 'max:10000'],
            'protective_factor_summary' => ['nullable', 'string', 'max:10000'],
            'support_network_summary' => ['nullable', 'string', 'max:10000'],
        ], [
            'start_date.required' => 'กรุณาระบุวันที่เริ่มแผน',
            'start_date.date' => 'วันที่เริ่มแผนไม่ถูกต้อง',
            'start_date.before_or_equal' => 'วันที่เริ่มแผนต้องไม่เกินวันปัจจุบัน',
            'end_date.date' => 'วันที่สิ้นสุดแผนไม่ถูกต้อง',
            'end_date.after_or_equal' => 'วันที่สิ้นสุดแผนต้องไม่น้อยกว่าวันที่เริ่มแผน',
            'overall_goal.required' => 'กรุณาระบุเป้าหมายภาพรวมของแผน',
            'overall_goal.max' => 'เป้าหมายภาพรวมต้องไม่เกิน 10,000 ตัวอักษร',
        ], [
            'strength_summary' => 'สรุปจุดแข็ง',
            'development_need_summary' => 'สรุปประเด็นที่ควรพัฒนา',
            'client_need_summary' => 'ความต้องการของผู้รับบริการ',
            'caregiver_need_summary' => 'ความต้องการของผู้ดูแล/ครอบครัว',
            'risk_factor_summary' => 'ปัจจัยเสี่ยง',
            'protective_factor_summary' => 'ปัจจัยคุ้มครอง',
            'support_network_summary' => 'เครือข่ายสนับสนุน',
        ]);

        DB::transaction(function () use ($clientModel, $validated): void {
            $activePlanExists = DevelopmentPlan::query()
                ->where('client_id', $clientModel->id)
                ->where('status', DevelopmentPlan::STATUS_ACTIVE)
                ->lockForUpdate()
                ->exists();

            if ($activePlanExists) {
                abort(422, 'มีแผนพัฒนาที่กำลังดำเนินการอยู่แล้ว');
            }

            $nextPlanNo = ((int) DevelopmentPlan::query()
                ->where('client_id', $clientModel->id)
                ->lockForUpdate()
                ->max('plan_no')) + 1;

            DevelopmentPlan::create([
                'client_id' => $clientModel->id,
                'plan_no' => $nextPlanNo,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'overall_goal' => trim($validated['overall_goal']),
                'strength_summary' => $this->nullableText($validated['strength_summary'] ?? null),
                'development_need_summary' => $this->nullableText($validated['development_need_summary'] ?? null),
                'client_need_summary' => $this->nullableText($validated['client_need_summary'] ?? null),
                'caregiver_need_summary' => $this->nullableText($validated['caregiver_need_summary'] ?? null),
                'risk_factor_summary' => $this->nullableText($validated['risk_factor_summary'] ?? null),
                'protective_factor_summary' => $this->nullableText($validated['protective_factor_summary'] ?? null),
                'support_network_summary' => $this->nullableText($validated['support_network_summary'] ?? null),
                'status' => DevelopmentPlan::STATUS_ACTIVE,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        });

        return redirect()
            ->route('individual-development.index', $clientModel->id)
            ->with('success', 'สร้างแผนพัฒนารายบุคคลเรียบร้อยแล้ว ขั้นตอนถัดไปคือการประเมินระดับเริ่มต้น (Baseline)');
    }

    private function findAuthorizedClient(int $clientId): Client
    {
        return Client::forUser(auth()->user())
            ->with(['house', 'project', 'target'])
            ->findOrFail($clientId);
    }

    private function resolveAgeText(Client $client): string
    {
        if (empty($client->birth_date)) {
            return '-';
        }

        $birthDate = Carbon::parse($client->birth_date, 'Asia/Bangkok')->startOfDay();
        $today = Carbon::today('Asia/Bangkok');

        if ($birthDate->greaterThan($today)) {
            return '-';
        }

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
        if ($score === null) {
            return ['level' => null, 'label' => 'ยังไม่ประเมิน'];
        }

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

        if (!$user) {
            return false;
        }

        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }

        if (!method_exists($user, 'hasFormPermission')) {
            return false;
        }

        return (bool) $user->hasFormPermission($permissionKey, $action);
    }
}
