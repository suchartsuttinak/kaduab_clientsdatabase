<?php

namespace App\Http\Controllers\Frontend\IndividualDevelopment;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\IndividualDevelopment\DevelopmentAssessment;
use App\Models\IndividualDevelopment\DevelopmentDomain;
use App\Models\IndividualDevelopment\DevelopmentFollowup;
use App\Models\IndividualDevelopment\DevelopmentGoal;
use App\Models\IndividualDevelopment\DevelopmentPlan;
use App\Services\IndividualDevelopment\IndividualDevelopmentLifecycleService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class IndividualDevelopmentReportController extends Controller
{
    public function __construct(private readonly IndividualDevelopmentLifecycleService $lifecycle)
    {
    }

    public function show(Request $request, int $client): View|RedirectResponse
    {
        $this->authorizePrint();
        $clientModel = $this->findAuthorizedClient($client);
        $data = $this->reportData($clientModel, $request->integer('plan'));
        if (!$data) {
            return redirect()->route('individual-development.index', $clientModel->id)
                ->with('warning', 'ยังไม่มีแผนพัฒนารายบุคคลสำหรับจัดทำรายงาน');
        }
        return view('frontend.client.individual_development.report.show', $data);
    }

    /**
     * IDP_BROWSER_PRINT_FINAL_V1
     * Compatibility endpoint สำหรับ URL/Bookmark เดิม
     * ไม่สร้าง PDF ด้วย DomPDF แล้ว; ส่งกลับหน้ารายงาน A4 ซึ่งพิมพ์หรือ Save as PDF ผ่าน Browser ได้
     */
    public function pdf(Request $request, int $client): RedirectResponse
    {
        $this->authorizePrint();
        $clientModel = $this->findAuthorizedClient($client);

        $params = ['client' => $clientModel->id];
        $planId = $request->integer('plan');
        if ($planId > 0) {
            $params['plan'] = $planId;
        }

        return redirect()
            ->route('individual-development.report.show', $params)
            ->with('info', 'รายงานใช้การพิมพ์ผ่าน Browser กรุณาเลือก “พิมพ์ / บันทึก PDF” จากหน้ารายงาน');
    }

    private function reportData(Client $client, int $planId = 0): ?array
    {
        $query = DevelopmentPlan::query()
            ->where('client_id', $client->id)
            ->with([
                'assessments.items.indicator.domain',
                'goals.domain.indicators', 'goals.indicator', 'goals.activities',
                'followups.items.indicator.domain',
                'reviewer', 'closer',
            ]);

        if ($planId > 0) {
            $query->whereKey($planId);
        } else {
            $query->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")->orderByDesc('plan_no');
        }

        $plan = $query->first();
        if (!$plan) return null;

        $baseline = $plan->assessments
            ->where('assessment_type', DevelopmentAssessment::TYPE_BASELINE)
            ->sortByDesc('assessment_date')->sortByDesc('round_no')->first();
        $latestAssessment = $plan->assessments->sortByDesc(function ($assessment): string {
            $date = optional($assessment->assessment_date)->format('Ymd') ?? '00000000';
            return $date . str_pad((string) $assessment->round_no, 6, '0', STR_PAD_LEFT);
        })->first();
        $latestFollowup = $plan->followups->sortByDesc('followup_no')->first();

        $domains = DevelopmentDomain::query()->where('is_active', true)
            ->with(['indicators' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')->get();

        $domainScores = $this->domainScores($domains, $baseline, $latestAssessment, $latestFollowup);
        $goals = $plan->goals->sortBy('sort_order')->values();
        $goalProgress = $this->lifecycle->goalProgressMap($plan, $goals);
        $goalStats = [
            'total' => $goals->count(),
            'achieved' => $goals->where('status', DevelopmentGoal::STATUS_ACHIEVED)->count(),
            'in_progress' => $goals->whereIn('status', [DevelopmentGoal::STATUS_IN_PROGRESS, DevelopmentGoal::STATUS_PARTIAL, DevelopmentGoal::STATUS_NOT_STARTED])->count(),
            'cancelled' => $goals->where('status', DevelopmentGoal::STATUS_CANCELLED)->count(),
        ];

        $followups = $plan->followups->sortBy('followup_no')->values();
        $followupSummaries = $followups->mapWithKeys(fn ($f) => [$f->id => $this->followupDomainSummaries($domains, $f)]);

        return [
            'client' => $client,
            'plan' => $plan,
            'baseline' => $baseline,
            'latestAssessment' => $latestAssessment,
            'latestFollowup' => $latestFollowup,
            'domainScores' => $domainScores,
            'goals' => $goals,
            'goalProgress' => $goalProgress,
            'goalStats' => $goalStats,
            'followups' => $followups,
            'followupSummaries' => $followupSummaries,
            'ageText' => $this->resolveAgeText($client),
            'statusLabels' => [
                DevelopmentPlan::STATUS_DRAFT => 'ร่างแผน',
                DevelopmentPlan::STATUS_ACTIVE => 'กำลังดำเนินการ',
                DevelopmentPlan::STATUS_REVIEW => 'อยู่ระหว่างทบทวน',
                DevelopmentPlan::STATUS_COMPLETED => 'ปิดแผนแล้ว',
                DevelopmentPlan::STATUS_CANCELLED => 'ยุติแผน',
            ],
            'goalStatusLabels' => [
                DevelopmentGoal::STATUS_NOT_STARTED => 'ยังไม่เริ่ม',
                DevelopmentGoal::STATUS_IN_PROGRESS => 'กำลังดำเนินการ',
                DevelopmentGoal::STATUS_PARTIAL => 'มีความก้าวหน้า',
                DevelopmentGoal::STATUS_ACHIEVED => 'บรรลุแล้ว',
                DevelopmentGoal::STATUS_CANCELLED => 'ยกเลิก',
            ],
            'followupResultLabels' => [
                DevelopmentFollowup::RESULT_IMPROVED => 'ดีขึ้น',
                DevelopmentFollowup::RESULT_STABLE => 'คงเดิม',
                DevelopmentFollowup::RESULT_DECLINED => 'ถดถอย',
                DevelopmentFollowup::RESULT_ACHIEVED => 'บรรลุเป้าหมาย',
            ],
        ];
    }

    private function domainScores(Collection $domains, ?DevelopmentAssessment $baseline, ?DevelopmentAssessment $assessment, ?DevelopmentFollowup $followup): Collection
    {
        return $domains->map(function ($domain) use ($baseline, $assessment, $followup): array {
            $baselineScore = $this->averageDomainFromItems($baseline?->items ?? collect(), (int) $domain->id, 'score');
            $latestScore = null;
            if ($followup) {
                $latestScore = $this->averageDomainFromItems($followup->items, (int) $domain->id, 'score');
            } elseif ($assessment) {
                $latestScore = $this->averageDomainFromItems($assessment->items, (int) $domain->id, 'score');
            }

            $delta = ($baselineScore !== null && $latestScore !== null) ? round($latestScore - $baselineScore, 2) : null;
            $level = $this->scoreLevel($latestScore);
            return [
                'id' => $domain->id,
                'code' => $domain->code,
                'name' => $domain->name,
                'baseline_score' => $baselineScore,
                'score' => $latestScore,
                'delta' => $delta,
                'trend' => $delta === null ? 'none' : ($delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'same')),
                'level' => $level['level'],
                'level_label' => $level['label'],
                'percent' => $latestScore !== null ? max(0, min(100, round(($latestScore / 5) * 100, 1))) : 0,
            ];
        });
    }

    private function averageDomainFromItems(Collection $items, int $domainId, string $scoreField): ?float
    {
        $scores = $items
            ->filter(fn ($i) => (int) optional($i->indicator)->domain_id === $domainId)
            ->pluck($scoreField)
            ->filter(fn ($v) => $v !== null)
            ->map(fn ($v) => (float) $v);

        return $scores->isNotEmpty() ? round((float) $scores->avg(), 2) : null;
    }

    private function followupDomainSummaries(Collection $domains, DevelopmentFollowup $followup): Collection
    {
        return $domains->map(function ($domain) use ($followup): array {
            $items = $followup->items->filter(fn ($i) => (int) optional($i->indicator)->domain_id === (int) $domain->id);
            $cur = $items->pluck('score')->filter(fn ($v) => $v !== null)->map(fn ($v) => (float) $v);
            $prev = $items->pluck('previous_score')->filter(fn ($v) => $v !== null)->map(fn ($v) => (float) $v);
            $current = $cur->isNotEmpty() ? round((float) $cur->avg(), 2) : null;
            $previous = $prev->isNotEmpty() ? round((float) $prev->avg(), 2) : null;
            $delta = ($current !== null && $previous !== null) ? round($current - $previous, 2) : null;
            $level = $this->scoreLevel($current);
            return ['name'=>$domain->name,'previous'=>$previous,'current'=>$current,'delta'=>$delta,'level'=>$level['level'],'label'=>$level['label'],'trend'=>$delta===null?'none':($delta>0?'up':($delta<0?'down':'same'))];
        });
    }

    private function scoreLevel(?float $score): array
    {
        if ($score === null) return ['level'=>null,'label'=>'ยังไม่ประเมิน'];
        return match(true){$score<1.5=>['level'=>1,'label'=>'ต้องส่งเสริมเร่งด่วน'],$score<2.5=>['level'=>2,'label'=>'ควรส่งเสริม'],$score<3.5=>['level'=>3,'label'=>'ตามเกณฑ์'],$score<4.5=>['level'=>4,'label'=>'ดี'],default=>['level'=>5,'label'=>'ดีมาก']};
    }

    private function authorizePrint(): void
    {
        $user = auth()->user();
        $allowed = $user && (
            (method_exists($user, 'isAdmin') && $user->isAdmin())
            || (method_exists($user, 'hasFormPermission') && (bool) $user->hasFormPermission('individual_development', 'print'))
        );
        abort_unless($allowed, 403);
    }

    private function findAuthorizedClient(int $clientId): Client
    {
        return Client::forUser(auth()->user())->with(['house','project','target'])->findOrFail($clientId);
    }

    private function resolveAgeText(Client $client): string
    {
        if (empty($client->birth_date)) return '-';
        $birthDate = Carbon::parse($client->birth_date,'Asia/Bangkok')->startOfDay();
        $today = Carbon::today('Asia/Bangkok');
        if ($birthDate->greaterThan($today)) return '-';
        $diff = $birthDate->diff($today);
        return $diff->y.' ปี '.$diff->m.' เดือน';
    }
}
