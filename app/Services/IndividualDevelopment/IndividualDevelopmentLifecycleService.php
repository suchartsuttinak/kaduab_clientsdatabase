<?php

namespace App\Services\IndividualDevelopment;

use App\Models\IndividualDevelopment\DevelopmentActivity;
use App\Models\IndividualDevelopment\DevelopmentAssessment;
use App\Models\IndividualDevelopment\DevelopmentFollowup;
use App\Models\IndividualDevelopment\DevelopmentGoal;
use App\Models\IndividualDevelopment\DevelopmentPlan;
use Illuminate\Support\Collection;

class IndividualDevelopmentLifecycleService
{
    public function baseline(DevelopmentPlan $plan): ?DevelopmentAssessment
    {
        if ($plan->relationLoaded('assessments')) {
            return $plan->assessments
                ->where('assessment_type', DevelopmentAssessment::TYPE_BASELINE)
                ->sortByDesc('assessment_date')
                ->sortByDesc('round_no')
                ->first();
        }

        return DevelopmentAssessment::query()
            ->where('plan_id', $plan->id)
            ->where('assessment_type', DevelopmentAssessment::TYPE_BASELINE)
            ->with('items.indicator.domain')
            ->orderByDesc('assessment_date')
            ->orderByDesc('round_no')
            ->first();
    }

    public function latestFollowup(DevelopmentPlan $plan): ?DevelopmentFollowup
    {
        if ($plan->relationLoaded('followups')) {
            return $plan->followups->sortByDesc('followup_no')->first();
        }

        return DevelopmentFollowup::query()
            ->where('plan_id', $plan->id)
            ->with('items.indicator.domain')
            ->orderByDesc('followup_no')
            ->first();
    }

    /** @return array<int,int|null> */
    public function latestScores(DevelopmentPlan $plan): array
    {
        $latest = $this->latestFollowup($plan);
        if ($latest) {
            if (!$latest->relationLoaded('items')) {
                $latest->load('items.indicator.domain');
            }

            return $latest->items->mapWithKeys(fn ($item) => [
                (int) $item->indicator_id => $item->score !== null ? (int) $item->score : null,
            ])->all();
        }

        $baseline = $this->baseline($plan);
        if (!$baseline) {
            return [];
        }

        if (!$baseline->relationLoaded('items')) {
            $baseline->load('items.indicator.domain');
        }

        return $baseline->items->mapWithKeys(fn ($item) => [
            (int) $item->indicator_id => $item->score !== null ? (int) $item->score : null,
        ])->all();
    }

    /**
     * @param Collection<int,DevelopmentGoal> $goals
     * @return array<int,array<string,mixed>>
     */
    public function goalProgressMap(DevelopmentPlan $plan, Collection $goals): array
    {
        $scores = $this->latestScores($plan);
        $map = [];

        foreach ($goals as $goal) {
            $currentRaw = $this->currentLevelForGoal($goal, $scores);
            $currentLevel = $currentRaw !== null ? max(1, min(5, (int) round($currentRaw))) : null;
            $baseline = $goal->baseline_level !== null ? (int) $goal->baseline_level : null;
            $target = $goal->target_level !== null ? (int) $goal->target_level : null;
            $reached = $currentLevel !== null && $target !== null && $currentLevel >= $target;

            $denominator = ($baseline !== null && $target !== null) ? max(1, $target - $baseline) : null;
            $progressPercent = 0;
            if ($denominator !== null && $currentLevel !== null && $baseline !== null) {
                $progressPercent = (int) round(max(0, min(100, (($currentLevel - $baseline) / $denominator) * 100)));
            }

            $map[(int) $goal->id] = [
                'baseline' => $baseline,
                'current' => $currentLevel,
                'current_raw' => $currentRaw,
                'target' => $target,
                'reached' => $reached,
                'delta_from_baseline' => ($currentLevel !== null && $baseline !== null) ? $currentLevel - $baseline : null,
                'progress_percent' => $progressPercent,
                'needs_confirmation' => $reached && !in_array($goal->status, [DevelopmentGoal::STATUS_ACHIEVED, DevelopmentGoal::STATUS_CANCELLED], true),
            ];
        }

        return $map;
    }

    /** @param array<int,int|null> $scores */
    public function currentLevelForGoal(DevelopmentGoal $goal, array $scores): ?float
    {
        if ($goal->indicator_id) {
            $value = $scores[(int) $goal->indicator_id] ?? null;
            return $value !== null ? (float) $value : null;
        }

        $indicatorIds = $goal->domain?->indicators?->pluck('id')->map(fn ($id) => (int) $id)->all() ?? [];
        if ($indicatorIds === []) {
            return null;
        }

        $values = collect($indicatorIds)
            ->map(fn ($id) => $scores[$id] ?? null)
            ->filter(fn ($value) => $value !== null)
            ->map(fn ($value) => (float) $value);

        return $values->isNotEmpty() ? round((float) $values->avg(), 2) : null;
    }

    public function canDeleteGoal(DevelopmentGoal $goal): bool
    {
        // “ลบ” ใช้เฉพาะข้อมูลที่สร้างผิดและยังไม่เริ่มใช้งานจริงเท่านั้น
        if ($goal->status !== DevelopmentGoal::STATUS_NOT_STARTED) {
            return false;
        }

        $hasActivities = DevelopmentActivity::withTrashed()
            ->where('goal_id', $goal->id)
            ->exists();

        if ($hasActivities) {
            return false;
        }

        return !$this->hasFollowupAfter($goal->plan_id, $goal->created_at);
    }

    public function canDeleteActivity(DevelopmentActivity $activity): bool
    {
        if ($activity->status !== DevelopmentActivity::STATUS_PLANNED) {
            return false;
        }

        if (filled($activity->result) || filled($activity->problem) || filled($activity->next_action)) {
            return false;
        }

        $planId = (int) $activity->goal?->plan_id;
        if ($planId <= 0) {
            $planId = (int) DevelopmentGoal::query()->whereKey($activity->goal_id)->value('plan_id');
        }

        return !$this->hasFollowupAfter($planId, $activity->created_at);
    }

    /** @return array<int,string> */
    public function closeBlockers(DevelopmentPlan $plan): array
    {
        $blockers = [];

        if (!$this->baseline($plan)) {
            $blockers[] = 'ยังไม่มีผลประเมินระดับเริ่มต้น (Baseline)';
        }

        $goals = $plan->relationLoaded('goals') ? $plan->goals : $plan->goals()->with('activities')->get();
        if ($goals->isEmpty()) {
            $blockers[] = 'ยังไม่มีเป้าหมายการพัฒนา';
        }

        $activeGoalCount = $goals->whereIn('status', [
            DevelopmentGoal::STATUS_NOT_STARTED,
            DevelopmentGoal::STATUS_IN_PROGRESS,
            DevelopmentGoal::STATUS_PARTIAL,
        ])->count();
        if ($activeGoalCount > 0) {
            $blockers[] = 'ยังมีเป้าหมายที่ยังไม่สิ้นสุด ' . $activeGoalCount . ' รายการ';
        }

        $activeActivityCount = $goals->flatMap(fn ($goal) => $goal->activities)
            ->whereIn('status', [DevelopmentActivity::STATUS_PLANNED, DevelopmentActivity::STATUS_IN_PROGRESS])
            ->count();
        if ($activeActivityCount > 0) {
            $blockers[] = 'ยังมีกิจกรรมที่ยังไม่สิ้นสุด ' . $activeActivityCount . ' รายการ';
        }

        $followupCount = $plan->relationLoaded('followups') ? $plan->followups->count() : $plan->followups()->count();
        if ($followupCount === 0) {
            $blockers[] = 'ยังไม่มีการติดตามผล';
        }

        $hasFinalOutcome = $plan->relationLoaded('assessments')
            ? $plan->assessments->contains('assessment_type', DevelopmentAssessment::TYPE_FINAL)
            : $plan->assessments()->where('assessment_type', DevelopmentAssessment::TYPE_FINAL)->exists();
        if (!$hasFinalOutcome) {
            $blockers[] = 'ยังไม่มีการประเมินผลก่อนจำหน่าย/ก่อนปิดแผน (Final Outcome)';
        }

        return $blockers;
    }

    public function canClose(DevelopmentPlan $plan): bool
    {
        return $plan->status === DevelopmentPlan::STATUS_ACTIVE && $this->closeBlockers($plan) === [];
    }

    public function baselineLocked(DevelopmentPlan $plan): bool
    {
        return $plan->goals()->exists() || $plan->followups()->exists();
    }

    public function hasAchievedGoals(DevelopmentPlan $plan): bool
    {
        return $plan->goals()->where('status', DevelopmentGoal::STATUS_ACHIEVED)->exists();
    }

    private function hasFollowupAfter(int $planId, mixed $createdAt): bool
    {
        $query = DevelopmentFollowup::query()->where('plan_id', $planId);

        if ($createdAt) {
            $query->where('created_at', '>=', $createdAt);
        }

        return $query->exists();
    }
}
