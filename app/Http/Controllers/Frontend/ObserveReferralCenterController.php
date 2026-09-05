<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\House;
use App\Models\Observe;
use App\Models\ObserveReferralAssignment;
use App\Models\User;
use App\Support\BehaviorReferralCenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ObserveReferralCenterController extends Controller
{
    private const TIMEZONE = 'Asia/Bangkok';

    public function index(Request $request)
    {
        $this->ensureAccess();

        $stages = ['actionable', 'waiting', 'assigned', 'accepted', 'ongoing', 'overdue', 'closed', 'all'];
        $riskLevels = ['none', 'low', 'moderate', 'high'];
        $perPageOptions = [10, 20, 30, 50];

        $requestedStage = (string) $request->input('stage', 'actionable');
        $requestedRisk = (string) $request->input('risk_level', '');
        $stage = in_array($requestedStage, $stages, true)
            ? $requestedStage
            : 'actionable';
        $riskLevel = in_array($requestedRisk, $riskLevels, true)
            ? $requestedRisk
            : null;
        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 20;
        }

        $query = BehaviorReferralCenter::query()->with([
            'client.title',
            'client.house',
            'misbehavior',
            'latestFollowup',
            'latestReferralRound',
            'referralAssignment.assignee',
        ]);

        $this->applyStage($query, $stage);

        if ($riskLevel !== null) {
            $this->applyRisk($query, $riskLevel);
        }

        if ($request->filled('house_id')) {
            $query->whereHas('client', fn (Builder $client) => $client
                ->where('house_id', (int) $request->input('house_id')));
        }

        if ($request->filled('assigned_to')) {
            if ($request->input('assigned_to') === 'unassigned') {
                $query->where(function (Builder $assignment): void {
                    $assignment->whereDoesntHave('referralAssignment')
                        ->orWhereHas('referralAssignment', fn (Builder $q) => $q->whereNull('assigned_to_user_id'));
                });
            } elseif (is_numeric($request->input('assigned_to'))) {
                $query->whereHas('referralAssignment', fn (Builder $assignment) => $assignment
                    ->where('assigned_to_user_id', (int) request('assigned_to')));
            }
        }

        $keyword = trim((string) $request->input('keyword'));
        if ($keyword !== '') {
            $keyword = mb_substr($keyword, 0, 100);
            $query->where(function (Builder $search) use ($keyword): void {
                $like = '%' . addcslashes($keyword, '%_\\') . '%';
                $search->where('observes.behavior', 'like', $like)
                    ->orWhereHas('misbehavior', fn (Builder $misbehavior) => $misbehavior
                        ->where('misbehavior_name', 'like', $like))
                    ->orWhereHas('client', function (Builder $client) use ($like): void {
                        $client->where('register_number', 'like', $like)
                            ->orWhere('first_name', 'like', $like)
                            ->orWhere('last_name', 'like', $like)
                            ->orWhere('nick_name', 'like', $like)
                            ->orWhereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) LIKE ?", [$like]);
                    });
            });
        }

        $referrals = $query
            ->orderByDesc('observes.updated_at')
            ->orderByDesc('observes.id')
            ->paginate($perPage)
            ->withQueryString();

        $eligibleUsers = $this->eligibleUsers();
        $houses = House::query()
            ->whereIn('id', auth()->user()->accessibleHouseIds())
            ->orderBy('house_name')
            ->get(['id', 'house_name']);
        $summary = BehaviorReferralCenter::summary();

        return view('admin.observe_referrals.index', compact(
            'referrals',
            'eligibleUsers',
            'houses',
            'summary',
            'stage',
            'riskLevel',
            'perPage',
            'perPageOptions'
        ));
    }

    public function show(int $id)
    {
        $this->ensureAccess();

        $observe = $this->findReferral($id, [
            'client.title',
            'client.house',
            'misbehavior',
            'followups' => fn ($query) => $query
                ->orderBy('followup_count')
                ->orderBy('followup_date')
                ->orderBy('id'),
            'referralRounds',
            'latestReferralRound',
            'referralAssignment.assignee',
            'referralAssignment.assignedBy',
        ]);

        $assignment = $observe->referralAssignment;
        $user = auth()->user();
        $isLeader = BehaviorReferralCenter::canAssign($user);
        $closed = ($observe->latestReferralRound?->status ?? null) === 'goal_met';
        $assignedToCurrentUser = (int) ($assignment?->assigned_to_user_id ?? 0) === (int) $user->id;
        $canAccept = !$closed && (!$assignment?->assigned_to_user_id || $assignedToCurrentUser);
        $canManageRounds = !$closed && (
            $isLeader
            || ($assignedToCurrentUser && $assignment?->accepted_at)
        );

        return view('admin.observe_referrals.show', [
            'observe' => $observe,
            'assignment' => $assignment,
            'eligibleUsers' => $this->eligibleUsers(),
            'canAssign' => $isLeader,
            'canAccept' => $canAccept,
            'canManageRounds' => $canManageRounds,
            'closed' => $closed,
        ]);
    }

    public function accept(int $id)
    {
        $this->ensureAccess('update');
        $observe = $this->findReferral($id, ['client', 'latestReferralRound', 'referralAssignment']);

        if (($observe->latestReferralRound?->status ?? null) === 'goal_met') {
            return back()->with('error', 'เคสนี้บรรลุเป้าหมายและปิดการช่วยเหลือแล้ว');
        }

        $assignment = $observe->referralAssignment;
        if ($assignment?->assigned_to_user_id && (int) $assignment->assigned_to_user_id !== (int) auth()->id()) {
            abort(403, 'เคสนี้มีผู้รับผิดชอบแล้ว กรุณาให้ผู้มีสิทธิ์มอบหมายใหม่ก่อน');
        }

        ObserveReferralAssignment::query()->updateOrCreate(
            ['observe_id' => $observe->id],
            [
                'assigned_to_user_id' => auth()->id(),
                'assigned_by_user_id' => $assignment?->assigned_by_user_id ?: auth()->id(),
                'assigned_at' => $assignment?->assigned_at ?: now(self::TIMEZONE),
                'accepted_at' => now(self::TIMEZONE),
            ]
        );

        return back()->with('success', 'รับเคสเพื่อดำเนินการเรียบร้อยแล้ว');
    }

    public function assign(Request $request, int $id)
    {
        $this->ensureAccess('update');
        if (!BehaviorReferralCenter::canAssign(auth()->user())) {
            abort(403, 'บัญชีนี้ไม่ได้รับสิทธิ์มอบหมายเคส');
        }

        $observe = $this->findReferral($id, ['client', 'latestReferralRound', 'referralAssignment']);
        if (($observe->latestReferralRound?->status ?? null) === 'goal_met') {
            return back()->with('error', 'เคสนี้บรรลุเป้าหมายและปิดการช่วยเหลือแล้ว');
        }

        $validated = $request->validate([
            'assigned_to_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ], [
            'assigned_to_user_id.exists' => 'ไม่พบผู้รับผิดชอบที่เลือก',
        ]);

        $assigneeId = $validated['assigned_to_user_id'] ?? null;

        if ($assigneeId) {
            $assignee = User::query()->findOrFail($assigneeId);
            abort_unless(
                (string) $assignee->status === '1'
                    && $assignee->canViewForm(BehaviorReferralCenter::PERMISSION_KEY)
                    && $assignee->canAccessProject($observe->client?->project_id)
                    && $assignee->canAccessHouse($observe->client?->house_id),
                422,
                'ผู้รับผิดชอบที่เลือกไม่มีสิทธิ์เข้าศูนย์รับเคส หรือขอบเขตหน่วยงาน/บ้านไม่ครอบคลุมผู้รับบริการรายนี้'
            );
        }
        $current = $observe->referralAssignment;
        $assigneeChanged = (int) ($current?->assigned_to_user_id ?? 0) !== (int) ($assigneeId ?? 0);

        ObserveReferralAssignment::query()->updateOrCreate(
            ['observe_id' => $observe->id],
            [
                'assigned_to_user_id' => $assigneeId,
                'assigned_by_user_id' => auth()->id(),
                'assigned_at' => $assigneeId ? now(self::TIMEZONE) : null,
                'accepted_at' => $assigneeChanged ? null : $current?->accepted_at,
            ]
        );

        return back()->with('success', $assigneeId
            ? 'มอบหมายผู้รับผิดชอบเรียบร้อยแล้ว'
            : 'นำการมอบหมายออกเรียบร้อยแล้ว');
    }

    private function ensureAccess(string $action = 'view'): void
    {
        $user = auth()->user();

        abort_unless($user, 403);
        abort_unless(
            $user->hasFormPermission(BehaviorReferralCenter::PERMISSION_KEY, $action),
            403,
            'คุณไม่มีสิทธิ์ดำเนินการในศูนย์รับเคสนี้'
        );
    }

    private function findReferral(int $id, array $with = []): Observe
    {
        return BehaviorReferralCenter::query()->with($with)->findOrFail($id);
    }

    private function eligibleUsers()
    {
        return User::query()
            ->where('status', '1')
            ->where(function (Builder $query): void {
                $query->where('role', User::ROLE_ADMIN)
                    ->orWhereHas('formPermissions', fn (Builder $permission) => $permission
                        ->where('permission_key', BehaviorReferralCenter::PERMISSION_KEY)
                        ->where('can_view', true));
            })
            ->orderBy('name')
            ->get(['id', 'name', 'role']);
    }

    private function applyStage(Builder $query, string $stage): void
    {
        if ($stage === 'all') {
            return;
        }

        if ($stage === 'closed') {
            $query->whereHas('latestReferralRound', fn (Builder $q) => $q->where('status', 'goal_met'));
            return;
        }

        $query->whereDoesntHave('latestReferralRound', fn (Builder $q) => $q->where('status', 'goal_met'));

        if ($stage === 'waiting') {
            $query->where(function (Builder $q): void {
                $q->whereDoesntHave('referralAssignment')
                    ->orWhereHas('referralAssignment', fn (Builder $assignment) => $assignment->whereNull('assigned_to_user_id'));
            });
        } elseif ($stage === 'assigned') {
            $query->whereHas('referralAssignment', fn (Builder $q) => $q
                ->whereNotNull('assigned_to_user_id')
                ->whereNull('accepted_at'));
        } elseif ($stage === 'accepted') {
            $query->whereHas('referralAssignment', fn (Builder $q) => $q->whereNotNull('accepted_at'))
                ->whereDoesntHave('latestReferralRound');
        } elseif ($stage === 'ongoing') {
            $query->whereHas('latestReferralRound', fn (Builder $q) => $q->where('status', 'ongoing'));
        } elseif ($stage === 'overdue') {
            $query->whereHas('latestReferralRound', fn (Builder $q) => $q
                ->where('status', 'ongoing')
                ->whereDate('next_appointment_date', '<', now(self::TIMEZONE)->toDateString()));
        }
    }

    private function applyRisk(Builder $query, string $riskLevel): void
    {
        $query->where(function (Builder $q) use ($riskLevel): void {
            $q->whereHas('latestReferralRound', fn (Builder $round) => $round->where('risk_level', $riskLevel))
                ->orWhere(function (Builder $fallback) use ($riskLevel): void {
                    $fallback->whereDoesntHave('latestReferralRound')
                        ->where(function (Builder $source) use ($riskLevel): void {
                            $source->whereHas('latestFollowup', fn (Builder $followup) => $followup->where('risk_level', $riskLevel))
                                ->orWhere(function (Builder $initial) use ($riskLevel): void {
                                    $initial->whereDoesntHave('latestFollowup')
                                        ->where('observes.risk_level', $riskLevel);
                                });
                        });
                });
        });
    }
}
