<?php

namespace App\Http\Controllers\Frontend\IndividualDevelopment;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\IndividualDevelopment\DevelopmentAssessment;
use App\Models\IndividualDevelopment\DevelopmentAssessmentItem;
use App\Models\IndividualDevelopment\DevelopmentDomain;
use App\Models\IndividualDevelopment\DevelopmentPlan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IndividualDevelopmentAssessmentController extends Controller
{
    private const PERMISSION_KEY = 'individual_development';

    private const INFORMATION_SOURCES = [
        'client' => 'ผู้รับบริการ',
        'caregiver' => 'ผู้ดูแล',
        'family' => 'ผู้ปกครอง/ครอบครัว',
        'teacher' => 'ครู/สถานศึกษา',
        'social_worker' => 'นักสังคมสงเคราะห์',
        'psychologist' => 'นักจิตวิทยา/ผู้ให้คำปรึกษา',
        'medical' => 'บุคลากรทางการแพทย์',
        'records' => 'เอกสาร/ข้อมูลในระบบ',
        'observation' => 'การสังเกตตามสภาพจริง',
        'other' => 'อื่น ๆ',
    ];

    public function create(int $client): View|RedirectResponse
    {
        $this->authorizeAction('create');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);

        if (!$plan) {
            return redirect()
                ->route('individual-development.index', $clientModel->id)
                ->with('warning', 'กรุณาสร้างแผนพัฒนารายบุคคลก่อนเริ่มประเมิน Baseline');
        }

        $existing = $this->baselineForPlan($plan->id);
        if ($existing) {
            return redirect()
                ->route('individual-development.baseline.show', $clientModel->id)
                ->with('warning', 'แผนปัจจุบันมีผลประเมิน Baseline แล้ว');
        }

        return view('frontend.client.individual_development.baseline.form', [
            'client' => $clientModel,
            'plan' => $plan,
            'assessment' => null,
            'domains' => $this->domains(),
            'ageText' => $this->resolveAgeText($clientModel),
            'informationSourceOptions' => self::INFORMATION_SOURCES,
            'readOnly' => false,
            'mode' => 'create',
        ]);
    }

    public function store(Request $request, int $client): RedirectResponse
    {
        $this->authorizeAction('create');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->activePlan($clientModel->id);

        if (!$plan) {
            return redirect()
                ->route('individual-development.index', $clientModel->id)
                ->with('warning', 'ไม่พบแผนพัฒนาที่กำลังดำเนินการ');
        }

        $domains = $this->domains();
        $validated = $this->validateAssessment($request, $domains);

        DB::transaction(function () use ($clientModel, $plan, $validated, $domains): void {
            $lockedPlan = DevelopmentPlan::query()
                ->whereKey($plan->id)
                ->where('client_id', $clientModel->id)
                ->lockForUpdate()
                ->firstOrFail();

            $exists = DevelopmentAssessment::query()
                ->where('plan_id', $lockedPlan->id)
                ->where('assessment_type', DevelopmentAssessment::TYPE_BASELINE)
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                abort(422, 'แผนนี้มีผลประเมิน Baseline แล้ว');
            }

            $assessment = DevelopmentAssessment::create([
                'plan_id' => $lockedPlan->id,
                'client_id' => $clientModel->id,
                'assessment_type' => DevelopmentAssessment::TYPE_BASELINE,
                'round_no' => 1,
                'assessment_date' => $validated['assessment_date'],
                'assessed_by' => auth()->id(),
                'information_sources' => array_values($validated['information_sources']),
                'participant_note' => $this->nullableText($validated['participant_note'] ?? null),
                'overall_note' => $this->nullableText($validated['overall_note'] ?? null),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            foreach ($this->indicatorIds($domains) as $indicatorId) {
                $item = $validated['items'][$indicatorId];

                DevelopmentAssessmentItem::create([
                    'assessment_id' => $assessment->id,
                    'indicator_id' => $indicatorId,
                    'score' => (int) $item['score'],
                    'evidence' => $this->nullableText($item['evidence'] ?? null),
                    'development_note' => $this->nullableText($item['development_note'] ?? null),
                ]);
            }

            $this->updatePlanContext($lockedPlan, $validated);
        });

        return redirect()
            ->route('individual-development.baseline.show', $clientModel->id)
            ->with('success', 'บันทึกการประเมิน Baseline 4 ด้านเรียบร้อยแล้ว');
    }

    public function show(int $client): View|RedirectResponse
    {
        $this->authorizeAction('view');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->currentPlan($clientModel->id);

        if (!$plan) {
            return redirect()
                ->route('individual-development.index', $clientModel->id)
                ->with('warning', 'ยังไม่มีแผนพัฒนารายบุคคล');
        }

        $assessment = $this->baselineForPlan($plan->id, true);
        if (!$assessment) {
            return redirect()
                ->route('individual-development.index', $clientModel->id)
                ->with('warning', 'ยังไม่มีผลประเมิน Baseline สำหรับแผนนี้');
        }

        $domains = $this->domains();

        return view('frontend.client.individual_development.baseline.show', [
            'client' => $clientModel,
            'plan' => $plan,
            'assessment' => $assessment,
            'domains' => $domains,
            'domainSummaries' => $this->domainSummaries($domains, $assessment),
            'ageText' => $this->resolveAgeText($clientModel),
            'informationSourceOptions' => self::INFORMATION_SOURCES,
            'canUpdateBaseline' => $plan->status === DevelopmentPlan::STATUS_ACTIVE
                && $this->can(self::PERMISSION_KEY, 'update'),
        ]);
    }

    public function edit(Request $request, int $client): View|RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->currentPlan($clientModel->id);

        if (!$plan) {
            return redirect()
                ->route('individual-development.index', $clientModel->id)
                ->with('warning', 'ยังไม่มีแผนพัฒนารายบุคคล');
        }

        $assessment = $this->baselineForPlan($plan->id, true);
        if (!$assessment) {
            return redirect()
                ->route('individual-development.baseline.create', $clientModel->id)
                ->with('warning', 'ยังไม่มีผลประเมิน Baseline');
        }

        return view('frontend.client.individual_development.baseline.form', [
            'client' => $clientModel,
            'plan' => $plan,
            'assessment' => $assessment,
            'domains' => $this->domains(),
            'ageText' => $this->resolveAgeText($clientModel),
            'informationSourceOptions' => self::INFORMATION_SOURCES,
            'readOnly' => (bool) $request->attributes->get('form_permission_readonly', false)
                || $plan->status !== DevelopmentPlan::STATUS_ACTIVE
                || !$this->can(self::PERMISSION_KEY, 'update'),
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, int $client): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->currentPlan($clientModel->id);

        if (!$plan) {
            abort(404);
        }

        $assessment = $this->baselineForPlan($plan->id, true);
        if (!$assessment) {
            abort(404);
        }

        if ($plan->status !== DevelopmentPlan::STATUS_ACTIVE) {
            abort(422, 'ไม่สามารถแก้ไข Baseline ของแผนที่ปิดหรือยุติแล้ว');
        }

        $domains = $this->domains();
        $validated = $this->validateAssessment($request, $domains);

        DB::transaction(function () use ($plan, $assessment, $validated, $domains): void {
            $lockedAssessment = DevelopmentAssessment::query()
                ->whereKey($assessment->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedAssessment->update([
                'assessment_date' => $validated['assessment_date'],
                'assessed_by' => auth()->id(),
                'information_sources' => array_values($validated['information_sources']),
                'participant_note' => $this->nullableText($validated['participant_note'] ?? null),
                'overall_note' => $this->nullableText($validated['overall_note'] ?? null),
                'updated_by' => auth()->id(),
            ]);

            foreach ($this->indicatorIds($domains) as $indicatorId) {
                $item = $validated['items'][$indicatorId];

                DevelopmentAssessmentItem::query()->updateOrCreate(
                    [
                        'assessment_id' => $lockedAssessment->id,
                        'indicator_id' => $indicatorId,
                    ],
                    [
                        'score' => (int) $item['score'],
                        'evidence' => $this->nullableText($item['evidence'] ?? null),
                        'development_note' => $this->nullableText($item['development_note'] ?? null),
                    ]
                );
            }

            $this->updatePlanContext($plan->fresh(), $validated);
        });

        return redirect()
            ->route('individual-development.baseline.show', $clientModel->id)
            ->with('success', 'ปรับปรุงผลประเมิน Baseline เรียบร้อยแล้ว');
    }

    private function validateAssessment(Request $request, Collection $domains): array
    {
        $rules = [
            'assessment_date' => ['required', 'date', 'before_or_equal:today'],
            'information_sources' => ['required', 'array', 'min:1'],
            'information_sources.*' => ['required', 'string', Rule::in(array_keys(self::INFORMATION_SOURCES))],
            'participant_note' => ['nullable', 'string', 'max:10000'],
            'overall_note' => ['nullable', 'string', 'max:10000'],
            'strength_summary' => ['nullable', 'string', 'max:10000'],
            'development_need_summary' => ['nullable', 'string', 'max:10000'],
            'client_need_summary' => ['nullable', 'string', 'max:10000'],
            'caregiver_need_summary' => ['nullable', 'string', 'max:10000'],
            'risk_factor_summary' => ['nullable', 'string', 'max:10000'],
            'protective_factor_summary' => ['nullable', 'string', 'max:10000'],
            'support_network_summary' => ['nullable', 'string', 'max:10000'],
            'items' => ['required', 'array'],
        ];

        $attributes = [
            'assessment_date' => 'วันที่ประเมิน',
            'information_sources' => 'แหล่งข้อมูล/ผู้ร่วมให้ข้อมูล',
            'participant_note' => 'หมายเหตุผู้ร่วมประเมิน',
            'overall_note' => 'สรุปสถานการณ์ปัจจุบัน',
        ];

        foreach ($domains as $domain) {
            foreach ($domain->indicators as $indicator) {
                $id = (int) $indicator->id;
                $rules["items.$id.score"] = ['required', 'integer', 'between:1,5'];
                $rules["items.$id.evidence"] = ['nullable', 'string', 'max:5000'];
                $rules["items.$id.development_note"] = ['nullable', 'string', 'max:5000'];
                $attributes["items.$id.score"] = 'ระดับของตัวชี้วัด “' . $indicator->name . '”';
                $attributes["items.$id.evidence"] = 'หลักฐาน/พฤติกรรมของ “' . $indicator->name . '”';
                $attributes["items.$id.development_note"] = 'ข้อสังเกตของ “' . $indicator->name . '”';
            }
        }

        return $request->validate($rules, [
            'assessment_date.required' => 'กรุณาระบุวันที่ประเมิน',
            'assessment_date.date' => 'วันที่ประเมินไม่ถูกต้อง',
            'assessment_date.before_or_equal' => 'วันที่ประเมินต้องไม่เกินวันปัจจุบัน',
            'information_sources.required' => 'กรุณาเลือกแหล่งข้อมูลหรือผู้ร่วมให้ข้อมูลอย่างน้อย 1 รายการ',
            'information_sources.min' => 'กรุณาเลือกแหล่งข้อมูลหรือผู้ร่วมให้ข้อมูลอย่างน้อย 1 รายการ',
            'items.*.score.required' => 'กรุณาประเมิน :attribute',
            'items.*.score.between' => ':attribute ต้องอยู่ระหว่างระดับ 1 ถึง 5',
        ], $attributes);
    }

    private function updatePlanContext(DevelopmentPlan $plan, array $validated): void
    {
        $plan->update([
            'strength_summary' => $this->nullableText($validated['strength_summary'] ?? null),
            'development_need_summary' => $this->nullableText($validated['development_need_summary'] ?? null),
            'client_need_summary' => $this->nullableText($validated['client_need_summary'] ?? null),
            'caregiver_need_summary' => $this->nullableText($validated['caregiver_need_summary'] ?? null),
            'risk_factor_summary' => $this->nullableText($validated['risk_factor_summary'] ?? null),
            'protective_factor_summary' => $this->nullableText($validated['protective_factor_summary'] ?? null),
            'support_network_summary' => $this->nullableText($validated['support_network_summary'] ?? null),
            'updated_by' => auth()->id(),
        ]);
    }

    private function domains(): Collection
    {
        return DevelopmentDomain::query()
            ->where('is_active', true)
            ->with([
                'indicators' => fn ($query) => $query
                    ->where('is_active', true)
                    ->with('rubrics')
                    ->orderBy('sort_order'),
            ])
            ->orderBy('sort_order')
            ->get();
    }

    private function indicatorIds(Collection $domains): array
    {
        return $domains
            ->flatMap(fn ($domain) => $domain->indicators)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    private function domainSummaries(Collection $domains, DevelopmentAssessment $assessment): Collection
    {
        $itemsByIndicator = $assessment->items->keyBy('indicator_id');

        return $domains->map(function ($domain) use ($itemsByIndicator): array {
            $scores = $domain->indicators
                ->map(fn ($indicator) => optional($itemsByIndicator->get($indicator->id))->score)
                ->filter(fn ($score) => $score !== null)
                ->map(fn ($score) => (float) $score);

            $average = $scores->isNotEmpty() ? round((float) $scores->avg(), 2) : null;
            $meta = $this->scoreLevel($average);

            return [
                'domain_id' => $domain->id,
                'code' => $domain->code,
                'name' => $domain->name,
                'average' => $average,
                'level' => $meta['level'],
                'label' => $meta['label'],
                'completed' => $scores->count(),
                'total' => $domain->indicators->count(),
            ];
        });
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

    private function activePlan(int $clientId): ?DevelopmentPlan
    {
        return DevelopmentPlan::query()
            ->where('client_id', $clientId)
            ->where('status', DevelopmentPlan::STATUS_ACTIVE)
            ->orderByDesc('plan_no')
            ->first();
    }

    private function currentPlan(int $clientId): ?DevelopmentPlan
    {
        return $this->activePlan($clientId)
            ?? DevelopmentPlan::query()
                ->where('client_id', $clientId)
                ->orderByDesc('start_date')
                ->orderByDesc('plan_no')
                ->first();
    }

    private function baselineForPlan(int $planId, bool $withItems = false): ?DevelopmentAssessment
    {
        $query = DevelopmentAssessment::query()
            ->where('plan_id', $planId)
            ->where('assessment_type', DevelopmentAssessment::TYPE_BASELINE)
            ->orderByDesc('round_no');

        if ($withItems) {
            $query->with(['items.indicator.domain', 'items.indicator.rubrics', 'assessor']);
        }

        return $query->first();
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
