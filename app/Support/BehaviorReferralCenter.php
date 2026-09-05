<?php

namespace App\Support;

use App\Models\Observe;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

final class BehaviorReferralCenter
{
    /**
     * UNIFIED_ACCESS_SCOPE_V5
     * บทบาทไม่ใช่ด่านสิทธิ์ของศูนย์รับเคสอีกต่อไป
     * ผู้ใช้ทุกบทบาท (ยกเว้น Admin ที่ผ่านเสมอ) ต้องได้รับ permission โดยตรง
     */
    public const PERMISSION_KEY = 'welfare_behavior_referral_center';

    private static ?array $summaryCache = null;

    public static function canAccess(?User $user): bool
    {
        return (bool) ($user && $user->canViewForm(self::PERMISSION_KEY));
    }

    public static function canAssign(?User $user): bool
    {
        return (bool) ($user && $user->canUpdateForm(self::PERMISSION_KEY));
    }

    /**
     * จำกัดขอบเขตเฉพาะรายการที่มีสถานะส่งต่อแล้ว ไม่กระทบ scope ของโมดูลอื่น
     */
    public static function query(): Builder
    {
        $query = Observe::query()->where(function (Builder $query): void {
            $query->where('observes.status', 'referred')
                ->orWhereHas('followups', fn (Builder $followup) => $followup->where('status', 'referred'));
        });

        // ใช้ขอบเขต Project + House เดียวกับผู้รับบริการทุกโมดูล
        if (auth()->check()) {
            $user = auth()->user();
            $query->whereHas('client', fn (Builder $client) => $client->forUser($user));
        }

        return $query;
    }

    public static function summary(): array
    {
        if (self::$summaryCache !== null) {
            return self::$summaryCache;
        }

        $empty = [
            'actionable' => 0,
            'waiting' => 0,
            'assigned' => 0,
            'ongoing' => 0,
            'overdue' => 0,
            'high_risk' => 0,
            'closed' => 0,
        ];

        if (!Schema::hasTable('observes') || !Schema::hasTable('observe_referral_rounds')) {
            return self::$summaryCache = $empty;
        }

        $base = self::query();
        $hasAssignments = Schema::hasTable('observe_referral_assignments');

        $actionable = (clone $base)
            ->whereDoesntHave('latestReferralRound', fn (Builder $q) => $q->where('status', 'goal_met'));

        $waiting = $hasAssignments
            ? (clone $actionable)->where(function (Builder $q): void {
                $q->whereDoesntHave('referralAssignment')
                    ->orWhereHas('referralAssignment', fn (Builder $a) => $a->whereNull('assigned_to_user_id'));
            })->count()
            : (clone $actionable)->count();

        $assigned = $hasAssignments
            ? (clone $actionable)->whereHas('referralAssignment', fn (Builder $q) => $q
                ->whereNotNull('assigned_to_user_id')
                ->whereNull('accepted_at'))
                ->count()
            : 0;

        $ongoing = (clone $actionable)
            ->whereHas('latestReferralRound', fn (Builder $q) => $q->where('status', 'ongoing'))
            ->count();

        $overdue = (clone $actionable)
            ->whereHas('latestReferralRound', fn (Builder $q) => $q
                ->where('status', 'ongoing')
                ->whereDate('next_appointment_date', '<', now('Asia/Bangkok')->toDateString()))
            ->count();

        $highRisk = (clone $actionable)->where(function (Builder $q): void {
            $q->whereHas('latestReferralRound', fn (Builder $round) => $round->where('risk_level', 'high'))
                ->orWhere(function (Builder $fallback): void {
                    $fallback->whereDoesntHave('latestReferralRound')
                        ->where(function (Builder $source): void {
                            $source->whereHas('latestFollowup', fn (Builder $followup) => $followup->where('risk_level', 'high'))
                                ->orWhere(function (Builder $initial): void {
                                    $initial->whereDoesntHave('latestFollowup')
                                        ->where('observes.risk_level', 'high');
                                });
                        });
                });
        })->count();

        return self::$summaryCache = [
            'actionable' => (clone $actionable)->count(),
            'waiting' => $waiting,
            'assigned' => $assigned,
            'ongoing' => $ongoing,
            'overdue' => $overdue,
            'high_risk' => $highRisk,
            'closed' => (clone $base)
                ->whereHas('latestReferralRound', fn (Builder $q) => $q->where('status', 'goal_met'))
                ->count(),
        ];
    }
}
