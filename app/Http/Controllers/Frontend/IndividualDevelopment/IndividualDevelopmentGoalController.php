<?php

namespace App\Http\Controllers\Frontend\IndividualDevelopment;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\IndividualDevelopment\DevelopmentAssessment;
use App\Models\IndividualDevelopment\DevelopmentDomain;
use App\Models\IndividualDevelopment\DevelopmentGoal;
use App\Models\IndividualDevelopment\DevelopmentIndicator;
use App\Models\IndividualDevelopment\DevelopmentPlan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class IndividualDevelopmentGoalController extends Controller
{
    private const PERMISSION_KEY = 'individual_development';

    private const PRIORITIES = [
        'low' => 'ต่ำ',
        'medium' => 'ปานกลาง',
        'high' => 'สูง',
        'urgent' => 'เร่งด่วน',
    ];

    private const STATUSES = [
        DevelopmentGoal::STATUS_NOT_STARTED => 'ยังไม่เริ่ม',
        DevelopmentGoal::STATUS_IN_PROGRESS => 'กำลังดำเนินการ',
        DevelopmentGoal::STATUS_PARTIAL => 'มีความก้าวหน้า',
        DevelopmentGoal::STATUS_ACHIEVED => 'บรรลุเป้าหมาย',
        DevelopmentGoal::STATUS_CANCELLED => 'ยุติเป้าหมาย',
    ];

    public function index(int $client): View|RedirectResponse
    {
        $this->authorizeAction('view');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->currentPlan($clientModel->id);

        if (!$plan) {
            return redirect()
                ->route('individual-development.index', $clientModel->id)
                ->with('warning', 'ยังไม่มีแผนพัฒนารายบุคคล');
        }

        $goals = DevelopmentGoal::query()
            ->where('plan_id', $plan->id)
            ->with(['domain', 'indicator', 'activities' => fn ($query) => $query->orderBy('activity_date')->orderBy('id')])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('frontend.client.individual_development.goals.index', [
            'client' => $clientModel,
            'plan' => $plan,
            'goals' => $goals,
            'ageText' => $this->resolveAgeText($clientModel),
            'priorityLabels' => self::PRIORITIES,
            'statusLabels' => self::STATUSES,
            'canCreate' => $plan->status === DevelopmentPlan::STATUS_ACTIVE && $this->can(self::PERMISSION_KEY, 'create'),
            'canUpdate' => $plan->status === DevelopmentPlan::STATUS_ACTIVE && $this->can(self::PERMISSION_KEY, 'update'),
            'canDelete' => $plan->status === DevelopmentPlan::STATUS_ACTIVE && $this->can(self::PERMISSION_KEY, 'delete'),
            'readOnly' => (bool) request()->attributes->get('form_permission_readonly', false),
        ]);
    }

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
                ->with('warning', 'กรุณาประเมิน Baseline ก่อนกำหนดเป้าหมายการพัฒนา');
        }

        return view('frontend.client.individual_development.goals.form', [
            'client' => $clientModel,
            'plan' => $plan,
            'goal' => null,
            'domains' => $this->domains(),
            'baseline' => $baseline,
            'baselineLevels' => $this->baselineLevels($baseline),
            'ageText' => $this->resolveAgeText($clientModel),
            'priorityLabels' => self::PRIORITIES,
            'statusLabels' => self::STATUSES,
            'mode' => 'create',
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
                ->with('warning', 'กรุณาประเมิน Baseline ก่อนกำหนดเป้าหมาย');
        }

        $validated = $this->validateGoal($request, $plan, false);
        $this->assertIndicatorBelongsToDomain($validated['domain_id'], $validated['indicator_id'] ?? null);
        $baselineLevel = $this->resolveBaselineLevel($baseline, (int) $validated['domain_id'], $validated['indicator_id'] ?? null);

        DB::transaction(function () use ($plan, $validated, $baselineLevel): void {
            $sortOrder = ((int) DevelopmentGoal::query()
                ->where('plan_id', $plan->id)
                ->lockForUpdate()
                ->max('sort_order')) + 1;

            DevelopmentGoal::create([
                'plan_id' => $plan->id,
                'domain_id' => (int) $validated['domain_id'],
                'indicator_id' => !empty($validated['indicator_id']) ? (int) $validated['indicator_id'] : null,
                'title' => trim($validated['title']),
                'description' => $this->nullableText($validated['description'] ?? null),
                'baseline_level' => $baselineLevel,
                'target_level' => (int) $validated['target_level'],
                'success_indicator' => trim($validated['success_indicator']),
                'measurement_method' => $this->nullableText($validated['measurement_method'] ?? null),
                'target_value' => $validated['target_value'] ?? null,
                'target_unit' => $this->nullableText($validated['target_unit'] ?? null),
                'target_date' => $validated['target_date'] ?? null,
                'priority' => $validated['priority'],
                'status' => DevelopmentGoal::STATUS_NOT_STARTED,
                'sort_order' => $sortOrder,
                'responsible_user_id' => null,
                'responsible_name' => $this->nullableText($validated['responsible_name'] ?? null),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        });

        return redirect()
            ->route('individual-development.goals.index', $clientModel->id)
            ->with('success', 'เพิ่มเป้าหมายการพัฒนารายบุคคลเรียบร้อยแล้ว');
    }

    public function edit(int $client, int $goal): View|RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);

        if (!$plan) {
            return redirect()->route('individual-development.index', $clientModel->id)
                ->with('warning', 'แผนปัจจุบันไม่อยู่ในสถานะที่แก้ไขได้');
        }

        $goalModel = DevelopmentGoal::query()
            ->whereKey($goal)
            ->where('plan_id', $plan->id)
            ->firstOrFail();

        $baseline = $this->baselineForPlan($plan->id);

        return view('frontend.client.individual_development.goals.form', [
            'client' => $clientModel,
            'plan' => $plan,
            'goal' => $goalModel,
            'domains' => $this->domains(),
            'baseline' => $baseline,
            'baselineLevels' => $baseline ? $this->baselineLevels($baseline) : [],
            'ageText' => $this->resolveAgeText($clientModel),
            'priorityLabels' => self::PRIORITIES,
            'statusLabels' => self::STATUSES,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, int $client, int $goal): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) {
            abort(422, 'แผนปัจจุบันไม่อยู่ในสถานะที่แก้ไขได้');
        }

        $goalModel = DevelopmentGoal::query()
            ->whereKey($goal)
            ->where('plan_id', $plan->id)
            ->firstOrFail();

        $validated = $this->validateGoal($request, $plan, true);
        $this->assertIndicatorBelongsToDomain($validated['domain_id'], $validated['indicator_id'] ?? null);

        $baseline = $this->baselineForPlan($plan->id);
        $baselineLevel = $baseline
            ? $this->resolveBaselineLevel($baseline, (int) $validated['domain_id'], $validated['indicator_id'] ?? null)
            : $goalModel->baseline_level;

        $goalModel->update([
            'domain_id' => (int) $validated['domain_id'],
            'indicator_id' => !empty($validated['indicator_id']) ? (int) $validated['indicator_id'] : null,
            'title' => trim($validated['title']),
            'description' => $this->nullableText($validated['description'] ?? null),
            'baseline_level' => $baselineLevel,
            'target_level' => (int) $validated['target_level'],
            'success_indicator' => trim($validated['success_indicator']),
            'measurement_method' => $this->nullableText($validated['measurement_method'] ?? null),
            'target_value' => $validated['target_value'] ?? null,
            'target_unit' => $this->nullableText($validated['target_unit'] ?? null),
            'target_date' => $validated['target_date'] ?? null,
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'responsible_name' => $this->nullableText($validated['responsible_name'] ?? null),
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('individual-development.goals.index', $clientModel->id)
            ->with('success', 'แก้ไขเป้าหมายการพัฒนาเรียบร้อยแล้ว');
    }

    public function destroy(int $client, int $goal): RedirectResponse
    {
        $this->authorizeAction('delete');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);
        if (!$plan) {
            abort(422, 'แผนปัจจุบันไม่อยู่ในสถานะที่ลบข้อมูลได้');
        }

        $goalModel = DevelopmentGoal::query()
            ->whereKey($goal)
            ->where('plan_id', $plan->id)
            ->firstOrFail();

        DB::transaction(function () use ($goalModel): void {
            $goalModel->activities()->delete();
            $goalModel->delete();
        });

        return redirect()
            ->route('individual-development.goals.index', $clientModel->id)
            ->with('success', 'ลบเป้าหมายการพัฒนาเรียบร้อยแล้ว');
    }

    private function validateGoal(Request $request, DevelopmentPlan $plan, bool $editing): array
    {
        $startDate = optional($plan->start_date)->format('Y-m-d') ?? now('Asia/Bangkok')->format('Y-m-d');

        return $request->validate([
            'domain_id' => ['required', 'integer', Rule::exists('development_domains', 'id')->where('is_active', true)],
            'indicator_id' => ['nullable', 'integer', Rule::exists('development_indicators', 'id')->where('is_active', true)],
            'title' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'target_level' => ['required', 'integer', 'between:1,5'],
            'success_indicator' => ['required', 'string', 'max:10000'],
            'measurement_method' => ['nullable', 'string', 'max:500'],
            'target_value' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'target_unit' => ['nullable', 'string', 'max:100'],
            'target_date' => ['nullable', 'date', 'after_or_equal:' . $startDate],
            'priority' => ['required', Rule::in(array_keys(self::PRIORITIES))],
            'status' => [$editing ? 'required' : 'nullable', Rule::in(array_keys(self::STATUSES))],
            'responsible_name' => ['nullable', 'string', 'max:255'],
        ], [
            'domain_id.required' => 'กรุณาเลือกด้านพัฒนาการ',
            'domain_id.exists' => 'ด้านพัฒนาการที่เลือกไม่ถูกต้อง',
            'indicator_id.exists' => 'ตัวชี้วัดที่เลือกไม่ถูกต้อง',
            'title.required' => 'กรุณาระบุชื่อเป้าหมาย',
            'title.max' => 'ชื่อเป้าหมายต้องไม่เกิน 500 ตัวอักษร',
            'target_level.required' => 'กรุณาระบุระดับเป้าหมาย',
            'target_level.between' => 'ระดับเป้าหมายต้องอยู่ระหว่าง 1 ถึง 5',
            'success_indicator.required' => 'กรุณาระบุตัวชี้วัดความสำเร็จ',
            'target_date.after_or_equal' => 'กำหนดสำเร็จต้องไม่น้อยกว่าวันที่เริ่มแผน',
            'priority.required' => 'กรุณาระบุระดับความสำคัญ',
            'status.required' => 'กรุณาระบุสถานะเป้าหมาย',
        ]);
    }

    private function assertIndicatorBelongsToDomain(int|string $domainId, int|string|null $indicatorId): void
    {
        if (empty($indicatorId)) {
            return;
        }

        $valid = DevelopmentIndicator::query()
            ->whereKey((int) $indicatorId)
            ->where('domain_id', (int) $domainId)
            ->where('is_active', true)
            ->exists();

        if (!$valid) {
            throw ValidationException::withMessages([
                'indicator_id' => 'ตัวชี้วัดที่เลือกไม่อยู่ในด้านพัฒนาการที่กำหนด',
            ]);
        }
    }

    private function resolveBaselineLevel(DevelopmentAssessment $baseline, int $domainId, int|string|null $indicatorId): ?int
    {
        if (!empty($indicatorId)) {
            $score = $baseline->items->firstWhere('indicator_id', (int) $indicatorId)?->score;
            return $score !== null ? (int) $score : null;
        }

        $scores = $baseline->items
            ->filter(fn ($item) => (int) optional($item->indicator)->domain_id === $domainId)
            ->pluck('score')
            ->filter(fn ($score) => $score !== null)
            ->map(fn ($score) => (float) $score);

        if ($scores->isEmpty()) {
            return null;
        }

        return max(1, min(5, (int) round((float) $scores->avg())));
    }

    private function baselineLevels(DevelopmentAssessment $baseline): array
    {
        $levels = [];
        foreach ($baseline->items as $item) {
            $levels['indicator:' . $item->indicator_id] = $item->score !== null ? (int) $item->score : null;
        }

        $domainGroups = $baseline->items->groupBy(fn ($item) => optional($item->indicator)->domain_id);
        foreach ($domainGroups as $domainId => $items) {
            if (!$domainId) continue;
            $scores = $items->pluck('score')->filter(fn ($score) => $score !== null)->map(fn ($score) => (float) $score);
            $levels['domain:' . $domainId] = $scores->isNotEmpty()
                ? max(1, min(5, (int) round((float) $scores->avg())))
                : null;
        }

        return $levels;
    }

    private function domains()
    {
        return DevelopmentDomain::query()
            ->where('is_active', true)
            ->with(['indicators' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }

    private function baselineForPlan(int $planId): ?DevelopmentAssessment
    {
        return DevelopmentAssessment::query()
            ->where('plan_id', $planId)
            ->where('assessment_type', DevelopmentAssessment::TYPE_BASELINE)
            ->with('items.indicator.domain')
            ->orderByDesc('assessment_date')
            ->orderByDesc('round_no')
            ->first();
    }

    private function currentPlan(int $clientId): ?DevelopmentPlan
    {
        return DevelopmentPlan::query()
            ->where('client_id', $clientId)
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderByDesc('plan_no')
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

    private function findAuthorizedClient(int $clientId): Client
    {
        return Client::forUser(auth()->user())
            ->with(['house', 'project', 'target'])
            ->findOrFail($clientId);
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
