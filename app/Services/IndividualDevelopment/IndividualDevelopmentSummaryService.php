<?php

namespace App\Services\IndividualDevelopment;

use App\Models\IndividualDevelopment\DevelopmentAssessment;
use App\Models\IndividualDevelopment\DevelopmentGoal;
use App\Models\IndividualDevelopment\DevelopmentPlan;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class IndividualDevelopmentSummaryService
{
    public function build(?DevelopmentPlan $plan): array
    {
        if (!$plan) {
            return ['problem'=>'ยังไม่มีแผนพัฒนารายบุคคล','actions'=>'ยังไม่มีการดำเนินงานตามแผน','result'=>'ยังไม่มีผลการประเมิน','next'=>'ควรเริ่มจากการประเมินจุดแข็ง ความต้องการ และจัดทำแผนพัฒนา'];
        }

        if (!$plan->relationLoaded('goals')) $plan->load(['goals.activities','followups','assessments.items']);
        $goals = $plan->goals;
        $followups = $plan->followups;

        $problemParts = collect([
            $plan->development_need_summary,
            $plan->client_need_summary ? 'ความต้องการของผู้รับบริการ: '.$plan->client_need_summary : null,
        ])->filter()->map(fn($v)=>$this->short($v,280));

        if ($problemParts->isEmpty() && is_array($plan->needs_profile)) {
            $problemParts = collect($plan->needs_profile)->pluck('detail')->filter()->take(3)->map(fn($v)=>$this->short($v,180));
        }

        $activities = $goals->flatMap->activities;
        $completedActivities = $activities->where('status','completed');
        $actions = $completedActivities->pluck('detail')->filter()->take(4)->map(fn($v)=>$this->short($v,140));
        if ($actions->isEmpty()) {
            $actions = $followups->pluck('actions_taken')->filter()->take(3)->map(fn($v)=>$this->short($v,160));
        }

        $achieved = $goals->where('status', DevelopmentGoal::STATUS_ACHIEVED)->count();
        $total = $goals->where('status','!=',DevelopmentGoal::STATUS_CANCELLED)->count();
        $latest = $followups->sortByDesc('followup_no')->first();
        $latestResult = $latest?->positive_changes ?: $latest?->result ?: $plan->final_outcome;
        $resultText = $total > 0 ? "บรรลุเป้าหมาย {$achieved} จาก {$total} เป้าหมาย" : 'ยังไม่มีเป้าหมายที่ใช้ประเมินผล';
        if ($latestResult) $resultText .= ' • '.$this->short($latestResult,260);

        $next = collect();
        foreach ($goals as $goal) {
            if (in_array($goal->status,[DevelopmentGoal::STATUS_ACHIEVED,DevelopmentGoal::STATUS_CANCELLED],true)) continue;
            if ($goal->target_date) $next->push('ติดตามเป้าหมาย “'.$this->short($goal->title,90).'” ภายใน '.$goal->target_date->format('d/m/').($goal->target_date->year + 543));
        }
        if ($latest?->next_action) $next->prepend($this->short($latest->next_action,220));
        if ($plan->final_recommendation) $next->push($this->short($plan->final_recommendation,220));
        if (is_array($plan->discharge_plan_profile) && filled($plan->discharge_plan_profile['readiness_summary'] ?? null)) {
            $next->push('แผนก่อนจำหน่าย: '.$this->short($plan->discharge_plan_profile['readiness_summary'],220));
        }

        return [
            'problem'=>$problemParts->isNotEmpty() ? $problemParts->implode(' • ') : 'ยังไม่ได้ระบุประเด็นความต้องการสำคัญ',
            'actions'=>$actions->isNotEmpty() ? $actions->implode(' • ') : 'ยังไม่มีการดำเนินกิจกรรมที่บันทึกผลแล้ว',
            'result'=>$resultText,
            'next'=>$next->isNotEmpty() ? $next->take(4)->implode(' • ') : ($plan->status===DevelopmentPlan::STATUS_ACTIVE ? 'ยังไม่ได้กำหนดงานถัดไป' : 'แผนสิ้นสุดแล้ว ไม่มีงานค้างจากแผนนี้'),
        ];
    }

    private function short(mixed $value, int $limit): string
    {
        return Str::limit(trim((string)$value), $limit, '…');
    }
}
