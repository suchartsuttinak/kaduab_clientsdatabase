<?php

namespace App\Http\Controllers\Frontend\IndividualDevelopment;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\House;
use App\Models\Project;
use App\Models\IndividualDevelopment\ClientDocumentStatus;
use App\Models\IndividualDevelopment\DevelopmentGoal;
use App\Models\IndividualDevelopment\DevelopmentPlan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IndividualDevelopmentCenterController extends Controller
{
    private const PERMISSION_KEY = 'individual_development_center';

    public function index(Request $request): View
    {
        abort_unless($this->can('view'), 403);

        $today = Carbon::today('Asia/Bangkok');
        $soon = $today->copy()->addDays(7);
        $staleCutoff = $today->copy()->subDays(30);

        // UNIFIED_ACCESS_SCOPE_V5: แม้เป็นศูนย์กลางก็ต้องอยู่ภายใน Project + House ของผู้ใช้
        $user = auth()->user();
        $scopedClientIds = Client::forUser($user)->select('clients.id');

        $query = Client::forUser($user)
            ->with(['house', 'project'])
            ->with([
                'individualDevelopmentPlans' => fn ($q) => $q
                    ->with([
                        'goals' => fn ($g) => $g->orderBy('sort_order')->orderBy('id'),
                        'followups' => fn ($f) => $f->orderByDesc('followup_no')->orderByDesc('id'),
                        'assessments' => fn ($a) => $a->with('items')->orderByDesc('assessment_date')->orderByDesc('round_no'),
                    ])
                    ->orderByDesc('plan_no')
                    ->limit(1),
            ]);

        if ($request->filled('house_id')) {
            abort_unless($user->canAccessHouse($request->integer('house_id')), 403, 'คุณไม่มีสิทธิ์ดูบ้านนี้');
        }
        if ($request->filled('project_id')) {
            abort_unless($user->canAccessProject($request->integer('project_id')), 403, 'คุณไม่มีสิทธิ์ดูหน่วยงาน/โครงการนี้');
        }

        $this->applyFilters($query, $request, $today, $soon, $staleCutoff);

        $clients = $query
            ->orderBy('house_id')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(30)
            ->withQueryString();

        $stats = [
            'clients' => Client::forUser($user)->count(),
            'active_plans' => DevelopmentPlan::query()
                ->whereIn('client_id', clone $scopedClientIds)
                ->where('status', DevelopmentPlan::STATUS_ACTIVE)
                ->distinct()->count('client_id'),
            'without_plan' => Client::forUser($user)->whereDoesntHave('individualDevelopmentPlans')->count(),
            'overdue' => DevelopmentPlan::query()
                ->whereIn('client_id', clone $scopedClientIds)
                ->where('status', DevelopmentPlan::STATUS_ACTIVE)
                ->whereHas('goals', fn ($q) => $this->openGoals($q)->whereDate('target_date', '<', $today->toDateString()))
                ->distinct()->count('client_id'),
            'due_soon' => DevelopmentPlan::query()
                ->whereIn('client_id', clone $scopedClientIds)
                ->where('status', DevelopmentPlan::STATUS_ACTIVE)
                ->whereHas('goals', fn ($q) => $this->openGoals($q)->whereBetween('target_date', [$today->toDateString(), $soon->toDateString()]))
                ->distinct()->count('client_id'),
            'stale' => DevelopmentPlan::query()
                ->whereIn('client_id', clone $scopedClientIds)
                ->where('status', DevelopmentPlan::STATUS_ACTIVE)
                ->whereDoesntHave('followups', fn ($q) => $q->whereDate('followup_date', '>=', $staleCutoff->toDateString()))
                ->distinct()->count('client_id'),
            'completed' => DevelopmentPlan::query()
                ->whereIn('client_id', clone $scopedClientIds)
                ->where('status', DevelopmentPlan::STATUS_COMPLETED)
                ->distinct()->count('client_id'),
            'documents_attention' => ClientDocumentStatus::query()
                ->whereIn('client_id', clone $scopedClientIds)
                ->where(function ($q) use ($today): void {
                    $q->whereIn('status', ['missing', 'in_progress', 'expired'])
                        ->orWhereDate('expires_at', '<', $today->toDateString());
                })
                ->distinct()->count('client_id'),
        ];

        return view('frontend.client.individual_development.center.index', [
            'clients' => $clients,
            'houses' => House::query()->whereIn('id', $user->accessibleHouseIds())->orderBy('house_name')->get(),
            'projects' => Project::query()->whereIn('id', $user->accessibleProjectIds())->orderBy('project_name')->get(),
            'stats' => $stats,
            'today' => $today,
            'soon' => $soon,
            'staleCutoff' => $staleCutoff,
        ]);
    }

    private function applyFilters(Builder $query, Request $request, Carbon $today, Carbon $soon, Carbon $staleCutoff): void
    {
        if ($request->filled('house_id')) {
            $query->where('house_id', $request->integer('house_id'));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            $query->where(function (Builder $q) use ($term): void {
                $q->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('nick_name', 'like', "%{$term}%")
                    ->orWhere('register_number', 'like', "%{$term}%");
            });
        }

        $status = (string) $request->input('status');
        if ($status === 'no_plan') {
            $query->whereDoesntHave('individualDevelopmentPlans');
        } elseif ($status === 'active') {
            $query->whereHas('individualDevelopmentPlans', fn ($q) => $q->where('status', DevelopmentPlan::STATUS_ACTIVE));
        } elseif ($status === 'completed') {
            $query->whereHas('individualDevelopmentPlans', fn ($q) => $q->where('status', DevelopmentPlan::STATUS_COMPLETED));
        } elseif ($status === 'overdue') {
            $query->whereHas('individualDevelopmentPlans', function ($q) use ($today): void {
                $q->where('status', DevelopmentPlan::STATUS_ACTIVE)
                    ->whereHas('goals', fn ($g) => $this->openGoals($g)->whereDate('target_date', '<', $today->toDateString()));
            });
        } elseif ($status === 'due_soon') {
            $query->whereHas('individualDevelopmentPlans', function ($q) use ($today, $soon): void {
                $q->where('status', DevelopmentPlan::STATUS_ACTIVE)
                    ->whereHas('goals', fn ($g) => $this->openGoals($g)->whereBetween('target_date', [$today->toDateString(), $soon->toDateString()]));
            });
        } elseif ($status === 'stale') {
            $query->whereHas('individualDevelopmentPlans', function ($q) use ($staleCutoff): void {
                $q->where('status', DevelopmentPlan::STATUS_ACTIVE)
                    ->whereDoesntHave('followups', fn ($f) => $f->whereDate('followup_date', '>=', $staleCutoff->toDateString()));
            });
        } elseif ($status === 'documents_attention') {
            $query->whereIn('id', ClientDocumentStatus::query()
                ->select('client_id')
                ->where(function ($q) use ($today): void {
                    $q->whereIn('status', ['missing', 'in_progress', 'expired'])
                        ->orWhereDate('expires_at', '<', $today->toDateString());
                }));
        }
    }

    private function openGoals(Builder $query): Builder
    {
        return $query->whereNotIn('status', [DevelopmentGoal::STATUS_ACHIEVED, DevelopmentGoal::STATUS_CANCELLED]);
    }

    private function can(string $action): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) return true;
        return method_exists($user, 'hasFormPermission')
            && (bool) $user->hasFormPermission(self::PERMISSION_KEY, $action);
    }
}
