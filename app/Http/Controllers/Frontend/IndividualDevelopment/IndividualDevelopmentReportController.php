<?php

namespace App\Http\Controllers\Frontend\IndividualDevelopment;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\IndividualDevelopment\DevelopmentAssessment;
use App\Models\IndividualDevelopment\DevelopmentDomain;
use App\Models\IndividualDevelopment\DevelopmentFollowup;
use App\Models\IndividualDevelopment\DevelopmentGoal;
use App\Models\IndividualDevelopment\DevelopmentPlan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class IndividualDevelopmentReportController extends Controller
{
    public function show(int $client): View|RedirectResponse
    {
        $this->authorizePrint();
        $clientModel = $this->findAuthorizedClient($client);
        $data = $this->reportData($clientModel);
        if (!$data) {
            return redirect()->route('individual-development.index', $clientModel->id)
                ->with('warning', 'ยังไม่มีแผนพัฒนารายบุคคลสำหรับจัดทำรายงาน');
        }
        return view('frontend.client.individual_development.report.show', $data);
    }

    public function pdf(int $client): Response|RedirectResponse
    {
        $this->authorizePrint();
        $clientModel = $this->findAuthorizedClient($client);
        $data = $this->reportData($clientModel);
        if (!$data) {
            return redirect()->route('individual-development.index', $clientModel->id)
                ->with('warning', 'ยังไม่มีแผนพัฒนารายบุคคลสำหรับจัดทำรายงาน');
        }
        $pdf = Pdf::loadView('frontend.client.individual_development.report.pdf', $data)->setPaper('a4', 'portrait');
        $filename = 'individual-development-client-' . $clientModel->id . '-plan-' . $data['plan']->plan_no . '-' . now('Asia/Bangkok')->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    private function reportData(Client $client): ?array
    {
        $plan = DevelopmentPlan::query()
            ->where('client_id', $client->id)
            ->with([
                'assessments.items.indicator.domain',
                'goals.domain', 'goals.indicator', 'goals.activities',
                'followups.items.indicator.domain',
            ])
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderByDesc('plan_no')
            ->first();
        if (!$plan) return null;

        $baseline = $plan->assessments->where('assessment_type', DevelopmentAssessment::TYPE_BASELINE)
            ->sortByDesc('assessment_date')->sortByDesc('round_no')->first();
        $latestAssessment = $plan->assessments->sortByDesc(function ($assessment): string {
            $date = optional($assessment->assessment_date)->format('Ymd') ?? '00000000';
            return $date . str_pad((string) $assessment->round_no, 6, '0', STR_PAD_LEFT);
        })->first();
        $latestFollowup = $plan->followups->sortByDesc('followup_no')->first();

        $domains = DevelopmentDomain::query()->where('is_active', true)
            ->with(['indicators' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')->get();

        $domainScores = $this->latestDomainScores($domains, $latestAssessment, $latestFollowup);
        $goals = $plan->goals->sortBy('sort_order')->values();
        $goalStats = [
            'total' => $goals->count(),
            'achieved' => $goals->where('status', DevelopmentGoal::STATUS_ACHIEVED)->count(),
            'in_progress' => $goals->where('status', DevelopmentGoal::STATUS_IN_PROGRESS)->count() + $goals->where('status', DevelopmentGoal::STATUS_PARTIAL)->count(),
            'not_started' => $goals->where('status', DevelopmentGoal::STATUS_NOT_STARTED)->count(),
        ];

        $followups = $plan->followups->sortBy('followup_no')->values();
        $followupSummaries = $followups->mapWithKeys(fn ($f) => [$f->id => $this->followupDomainSummaries($domains, $f)]);

        return [
            'client' => $client, 'plan' => $plan, 'baseline' => $baseline,
            'latestAssessment' => $latestAssessment, 'latestFollowup' => $latestFollowup,
            'domainScores' => $domainScores, 'goals' => $goals, 'goalStats' => $goalStats,
            'followups' => $followups, 'followupSummaries' => $followupSummaries,
            'ageText' => $this->resolveAgeText($client),
            'statusLabels' => [
                DevelopmentPlan::STATUS_DRAFT => 'ร่างแผน', DevelopmentPlan::STATUS_ACTIVE => 'กำลังดำเนินการ',
                DevelopmentPlan::STATUS_REVIEW => 'อยู่ระหว่างทบทวน', DevelopmentPlan::STATUS_COMPLETED => 'สำเร็จ', DevelopmentPlan::STATUS_CANCELLED => 'ยุติ',
            ],
            'goalStatusLabels' => [
                DevelopmentGoal::STATUS_NOT_STARTED => 'ยังไม่เริ่ม', DevelopmentGoal::STATUS_IN_PROGRESS => 'กำลังดำเนินการ',
                DevelopmentGoal::STATUS_PARTIAL => 'มีความก้าวหน้า', DevelopmentGoal::STATUS_ACHIEVED => 'บรรลุเป้าหมาย', DevelopmentGoal::STATUS_CANCELLED => 'ยุติเป้าหมาย',
            ],
            'followupResultLabels' => [
                DevelopmentFollowup::RESULT_IMPROVED => 'ดีขึ้น', DevelopmentFollowup::RESULT_STABLE => 'คงเดิม',
                DevelopmentFollowup::RESULT_DECLINED => 'ถดถอย', DevelopmentFollowup::RESULT_ACHIEVED => 'บรรลุเป้าหมาย',
            ],
        ];
    }

    private function latestDomainScores(Collection $domains, ?DevelopmentAssessment $assessment, ?DevelopmentFollowup $followup): Collection
    {
        return $domains->map(function ($domain) use ($assessment, $followup): array {
            $score = null;
            if ($followup) {
                $scores = $followup->items->filter(fn ($i) => (int) optional($i->indicator)->domain_id === (int) $domain->id)
                    ->pluck('score')->filter(fn ($v) => $v !== null)->map(fn ($v) => (float) $v);
                if ($scores->isNotEmpty()) $score = round((float) $scores->avg(), 2);
            } elseif ($assessment) {
                $scores = $assessment->items->filter(fn ($i) => (int) optional($i->indicator)->domain_id === (int) $domain->id)
                    ->pluck('score')->filter(fn ($v) => $v !== null)->map(fn ($v) => (float) $v);
                if ($scores->isNotEmpty()) $score = round((float) $scores->avg(), 2);
            }
            $level = $this->scoreLevel($score);
            return ['id'=>$domain->id,'code'=>$domain->code,'name'=>$domain->name,'score'=>$score,'level'=>$level['level'],'level_label'=>$level['label'],'percent'=>$score!==null?max(0,min(100,round(($score/5)*100,1))):0];
        });
    }

    private function followupDomainSummaries(Collection $domains, DevelopmentFollowup $followup): Collection
    {
        return $domains->map(function ($domain) use ($followup): array {
            $items = $followup->items->filter(fn ($i) => (int) optional($i->indicator)->domain_id === (int) $domain->id);
            $cur = $items->pluck('score')->filter(fn ($v)=>$v!==null)->map(fn($v)=>(float)$v);
            $prev = $items->pluck('previous_score')->filter(fn ($v)=>$v!==null)->map(fn($v)=>(float)$v);
            $current = $cur->isNotEmpty()?round((float)$cur->avg(),2):null;
            $previous = $prev->isNotEmpty()?round((float)$prev->avg(),2):null;
            $delta = ($current!==null&&$previous!==null)?round($current-$previous,2):null;
            $level=$this->scoreLevel($current);
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
        $birthDate=Carbon::parse($client->birth_date,'Asia/Bangkok')->startOfDay();$today=Carbon::today('Asia/Bangkok');
        if($birthDate->greaterThan($today))return '-';$diff=$birthDate->diff($today);return $diff->y.' ปี '.$diff->m.' เดือน';
    }
}
