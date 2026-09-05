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
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class IndividualDevelopmentOutcomeController extends Controller
{
    private const PERMISSION_KEY = 'individual_development';

    private const TYPE_LABELS = [
        DevelopmentAssessment::TYPE_BASELINE => 'แรกเข้า (Baseline)',
        DevelopmentAssessment::TYPE_REVIEW => 'ระหว่างดำเนินการ',
        DevelopmentAssessment::TYPE_FINAL => 'ก่อนจำหน่าย/ก่อนปิดแผน',
        DevelopmentAssessment::TYPE_POST_DISCHARGE => 'หลังจำหน่าย',
    ];

    private const INFORMATION_SOURCES = [
        'client' => 'ผู้รับบริการ', 'caregiver' => 'ผู้ดูแล', 'family' => 'ผู้ปกครอง/ครอบครัว',
        'teacher' => 'ครู/สถานศึกษา', 'social_worker' => 'นักสังคมสงเคราะห์',
        'psychologist' => 'นักจิตวิทยา/ผู้ให้คำปรึกษา', 'medical' => 'บุคลากรทางการแพทย์',
        'records' => 'เอกสาร/ข้อมูลในระบบ', 'observation' => 'การสังเกตตามสภาพจริง', 'other' => 'อื่น ๆ',
    ];

    public function index(Request $request, int $client): View|RedirectResponse
    {
        $this->authorizeAction('view');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->selectedPlan($clientModel->id, $request->integer('plan'));
        if (!$plan) return redirect()->route('individual-development.index', $clientModel->id)->with('warning', 'ยังไม่มีแผนพัฒนารายบุคคล');

        $plan->load(['assessments.items.indicator.domain', 'assessments.assessor']);
        $assessments = $plan->assessments->sortBy(fn($a) => ($a->assessment_date?->format('Ymd') ?? '00000000').str_pad((string)$a->round_no, 5, '0', STR_PAD_LEFT))->values();
        $baseline = $assessments->firstWhere('assessment_type', DevelopmentAssessment::TYPE_BASELINE);
        $rows = $assessments->map(function($assessment) use ($baseline) {
            $avg = $this->average($assessment);
            $base = $baseline ? $this->average($baseline) : null;
            $delta = ($avg !== null && $base !== null) ? round($avg - $base, 2) : null;
            return ['assessment'=>$assessment,'average'=>$avg,'delta'=>$delta,'trend'=>$delta===null?'none':($delta>0?'up':($delta<0?'down':'same'))];
        });

        return view('frontend.client.individual_development.outcomes.index', [
            'client'=>$clientModel, 'plan'=>$plan, 'rows'=>$rows, 'typeLabels'=>self::TYPE_LABELS,
            'canCreate'=>$this->can('create'), 'canUpdate'=>$this->can('update'), 'canDelete'=>$this->can('delete'),
            'readOnly'=>!($this->can('create')||$this->can('update')||$this->can('delete')),
        ]);
    }

    public function create(Request $request, int $client): View|RedirectResponse
    {
        $this->authorizeAction('create');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->selectedPlan($clientModel->id, $request->integer('plan'));
        if (!$plan) return redirect()->route('individual-development.index',$clientModel->id)->with('warning','ยังไม่มีแผนพัฒนารายบุคคล');

        $type = (string)$request->input('type', DevelopmentAssessment::TYPE_REVIEW);
        if (!array_key_exists($type, self::TYPE_LABELS) || $type === DevelopmentAssessment::TYPE_BASELINE) $type = DevelopmentAssessment::TYPE_REVIEW;
        if ($type === DevelopmentAssessment::TYPE_POST_DISCHARGE && $plan->status === DevelopmentPlan::STATUS_ACTIVE) {
            return back()->with('warning','การประเมินหลังจำหน่ายใช้กับแผนที่สิ้นสุดแล้ว');
        }
        if (in_array($type, [DevelopmentAssessment::TYPE_REVIEW, DevelopmentAssessment::TYPE_FINAL], true)
            && $plan->status !== DevelopmentPlan::STATUS_ACTIVE) {
            return back()->with('warning','การประเมินระหว่างดำเนินการ/ก่อนจำหน่ายใช้กับแผนที่กำลังดำเนินการเท่านั้น');
        }
        if ($type === DevelopmentAssessment::TYPE_FINAL) {
            $existingFinal = DevelopmentAssessment::query()
                ->where('plan_id', $plan->id)
                ->where('assessment_type', DevelopmentAssessment::TYPE_FINAL)
                ->orderByDesc('assessment_date')->orderByDesc('round_no')->first();
            if ($existingFinal) {
                return redirect()->route('individual-development.outcomes.show', [$clientModel->id, $existingFinal->id])
                    ->with('warning', 'แผนนี้มี Final Outcome แล้ว กรุณาตรวจสอบหรือแก้ไขรายการเดิมแทนการสร้างซ้ำ');
            }
        }

        return view('frontend.client.individual_development.outcomes.form', $this->formData($clientModel,$plan,null,$type));
    }

    public function store(Request $request, int $client): RedirectResponse
    {
        $this->authorizeAction('create');
        $clientModel = $this->findAuthorizedClient($client);
        $plan = $this->selectedPlan($clientModel->id, $request->integer('plan_id'));
        if (!$plan) abort(422,'ไม่พบแผนพัฒนารายบุคคล');
        $domains = $this->domains();
        $type = (string) $request->input('assessment_type');
        if (!in_array($type, [DevelopmentAssessment::TYPE_REVIEW, DevelopmentAssessment::TYPE_FINAL, DevelopmentAssessment::TYPE_POST_DISCHARGE], true)) {
            throw ValidationException::withMessages(['assessment_type' => 'ประเภทการประเมินไม่ถูกต้อง']);
        }
        $validated = $this->validateOutcome($request, $domains, $plan, $type);
        if ($type === DevelopmentAssessment::TYPE_POST_DISCHARGE && $plan->status === DevelopmentPlan::STATUS_ACTIVE) abort(422,'การประเมินหลังจำหน่ายใช้กับแผนที่สิ้นสุดแล้ว');
        if (in_array($type, [DevelopmentAssessment::TYPE_REVIEW, DevelopmentAssessment::TYPE_FINAL], true)
            && $plan->status !== DevelopmentPlan::STATUS_ACTIVE) abort(422,'การประเมินระหว่างดำเนินการ/ก่อนจำหน่ายใช้กับแผนที่กำลังดำเนินการเท่านั้น');
        if ($type === DevelopmentAssessment::TYPE_FINAL
            && DevelopmentAssessment::query()->where('plan_id', $plan->id)->where('assessment_type', DevelopmentAssessment::TYPE_FINAL)->exists()) {
            return redirect()->route('individual-development.outcomes.index', ['client'=>$clientModel->id,'plan'=>$plan->id])
                ->with('warning', 'แผนนี้มี Final Outcome แล้ว ไม่อนุญาตให้สร้างซ้ำ');
        }

        $assessment = DB::transaction(function() use ($validated,$plan,$clientModel,$domains) {
            $lockedPlan = DevelopmentPlan::query()
                ->whereKey($plan->id)
                ->where('client_id', $clientModel->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($validated['assessment_type'] === DevelopmentAssessment::TYPE_FINAL
                && DevelopmentAssessment::query()->where('plan_id', $lockedPlan->id)->where('assessment_type', DevelopmentAssessment::TYPE_FINAL)->exists()) {
                throw ValidationException::withMessages(['assessment_type' => 'แผนนี้มี Final Outcome แล้ว ไม่อนุญาตให้สร้างซ้ำ']);
            }

            $round = (int) DevelopmentAssessment::query()
                ->where('plan_id', $lockedPlan->id)
                ->where('assessment_type', $validated['assessment_type'])
                ->max('round_no') + 1;
            $assessment = DevelopmentAssessment::create([
                'plan_id'=>$lockedPlan->id,'client_id'=>$clientModel->id,'assessment_type'=>$validated['assessment_type'],
                'round_no'=>$round,'assessment_date'=>$validated['assessment_date'],'assessed_by'=>auth()->id(),
                'information_sources'=>array_values($validated['information_sources'] ?? []),
                'participant_note'=>$this->text($validated['participant_note'] ?? null),'overall_note'=>$this->text($validated['overall_note'] ?? null),
                'created_by'=>auth()->id(),'updated_by'=>auth()->id(),
            ]);
            foreach ($domains->flatMap->indicators as $indicator) {
                $item = $validated['items'][$indicator->id];
                DevelopmentAssessmentItem::create(['assessment_id'=>$assessment->id,'indicator_id'=>$indicator->id,'score'=>(int)$item['score'],'evidence'=>$this->text($item['evidence']??null),'development_note'=>$this->text($item['development_note']??null)]);
            }
            return $assessment;
        });
        return redirect()->route('individual-development.outcomes.show',[$clientModel->id,$assessment->id])->with('success','บันทึกการประเมินผลลัพธ์เรียบร้อยแล้ว');
    }

    public function show(int $client, int $assessment): View
    {
        $this->authorizeAction('view');
        $clientModel = $this->findAuthorizedClient($client);
        $assessmentModel = DevelopmentAssessment::query()->where('client_id',$clientModel->id)->with(['plan','items.indicator.domain','assessor'])->findOrFail($assessment);
        $baseline = DevelopmentAssessment::query()->where('plan_id',$assessmentModel->plan_id)->where('assessment_type',DevelopmentAssessment::TYPE_BASELINE)->with('items.indicator.domain')->orderByDesc('assessment_date')->first();
        return view('frontend.client.individual_development.outcomes.show',[
            'client'=>$clientModel,'plan'=>$assessmentModel->plan,'assessment'=>$assessmentModel,'baseline'=>$baseline,
            'typeLabels'=>self::TYPE_LABELS,'domainRows'=>$this->domainRows($assessmentModel,$baseline),
            'canUpdate'=>$assessmentModel->assessment_type !== DevelopmentAssessment::TYPE_BASELINE && $this->can('update') && ($assessmentModel->plan->status === DevelopmentPlan::STATUS_ACTIVE || $assessmentModel->assessment_type === DevelopmentAssessment::TYPE_POST_DISCHARGE),
        ]);
    }

    public function edit(int $client, int $assessment): View|RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $assessmentModel = DevelopmentAssessment::query()
            ->where('client_id', $clientModel->id)
            ->with(['plan', 'items'])
            ->findOrFail($assessment);

        if ($assessmentModel->assessment_type === DevelopmentAssessment::TYPE_BASELINE) {
            return redirect()->route('individual-development.baseline.edit', $clientModel->id)
                ->with('warning', 'Baseline ใช้หน้าประเมินเริ่มต้นเฉพาะ เพื่อรักษากระบวนการเดิม');
        }
        if ($assessmentModel->plan->status !== DevelopmentPlan::STATUS_ACTIVE
            && $assessmentModel->assessment_type !== DevelopmentAssessment::TYPE_POST_DISCHARGE) {
            return redirect()->route('individual-development.outcomes.show', [$clientModel->id, $assessmentModel->id])
                ->with('warning', 'ผลประเมินของแผนที่สิ้นสุดแล้วเป็นประวัติและไม่อนุญาตให้แก้ไข');
        }

        return view('frontend.client.individual_development.outcomes.form',
            $this->formData($clientModel, $assessmentModel->plan, $assessmentModel, $assessmentModel->assessment_type)
            + ['mode' => 'edit']
        );
    }

    public function update(Request $request, int $client, int $assessment): RedirectResponse
    {
        $this->authorizeAction('update');
        $clientModel = $this->findAuthorizedClient($client);
        $assessmentModel = DevelopmentAssessment::query()
            ->where('client_id', $clientModel->id)
            ->with(['plan', 'items'])
            ->findOrFail($assessment);

        if ($assessmentModel->assessment_type === DevelopmentAssessment::TYPE_BASELINE) {
            return redirect()->route('individual-development.baseline.edit', $clientModel->id)
                ->with('warning', 'Baseline ต้องแก้ไขจากหน้าประเมินเริ่มต้น');
        }
        if ($assessmentModel->plan->status !== DevelopmentPlan::STATUS_ACTIVE
            && $assessmentModel->assessment_type !== DevelopmentAssessment::TYPE_POST_DISCHARGE) {
            return redirect()->route('individual-development.outcomes.show', [$clientModel->id, $assessmentModel->id])
                ->with('warning', 'ผลประเมินของแผนที่สิ้นสุดแล้วเป็นประวัติและไม่อนุญาตให้แก้ไข');
        }

        $domains = $this->domains();
        $validated = $this->validateOutcome($request, $domains, $assessmentModel->plan, $assessmentModel->assessment_type);
        // ไม่อนุญาตให้เปลี่ยนชนิดหรือย้ายแผนของผลประเมินเดิม
        $validated['assessment_type'] = $assessmentModel->assessment_type;

        DB::transaction(function () use ($assessmentModel, $validated, $domains): void {
            $assessmentModel->update([
                'assessment_date' => $validated['assessment_date'],
                'assessed_by' => auth()->id(),
                'information_sources' => array_values($validated['information_sources'] ?? []),
                'participant_note' => $this->text($validated['participant_note'] ?? null),
                'overall_note' => $this->text($validated['overall_note'] ?? null),
                'updated_by' => auth()->id(),
            ]);

            foreach ($domains->flatMap->indicators as $indicator) {
                $item = $validated['items'][$indicator->id];
                DevelopmentAssessmentItem::updateOrCreate(
                    ['assessment_id' => $assessmentModel->id, 'indicator_id' => $indicator->id],
                    [
                        'score' => (int) $item['score'],
                        'evidence' => $this->text($item['evidence'] ?? null),
                        'development_note' => $this->text($item['development_note'] ?? null),
                    ]
                );
            }
        });

        return redirect()->route('individual-development.outcomes.show', [$clientModel->id, $assessmentModel->id])
            ->with('success', 'ปรับปรุงผลการประเมินเรียบร้อยแล้ว');
    }

    private function formData(Client $client, DevelopmentPlan $plan, ?DevelopmentAssessment $assessment, string $type): array
    {
        if ($assessment) $assessment->loadMissing('items');
        $minimumDate = optional($plan->start_date)->format('Y-m-d');
        if ($type === DevelopmentAssessment::TYPE_POST_DISCHARGE && $plan->closed_at) {
            $closedDate = Carbon::parse($plan->closed_at, 'Asia/Bangkok')->format('Y-m-d');
            $minimumDate = !$minimumDate || $closedDate > $minimumDate ? $closedDate : $minimumDate;
        }
        return ['client'=>$client,'plan'=>$plan,'assessment'=>$assessment,'type'=>$type,'typeLabels'=>self::TYPE_LABELS,
            'domains'=>$this->domains(),'informationSourceOptions'=>self::INFORMATION_SOURCES,'today'=>Carbon::today('Asia/Bangkok')->format('Y-m-d'),
            'minimumDate'=>$minimumDate,'assessmentItems'=>$assessment?->items?->keyBy('indicator_id') ?? collect()];
    }

    private function validateOutcome(Request $request, $domains, DevelopmentPlan $plan, string $assessmentType): array
    {
        $minimumDate = optional($plan->start_date)->format('Y-m-d');
        if ($assessmentType === DevelopmentAssessment::TYPE_POST_DISCHARGE && $plan->closed_at) {
            $closedDate = Carbon::parse($plan->closed_at, 'Asia/Bangkok')->format('Y-m-d');
            $minimumDate = !$minimumDate || $closedDate > $minimumDate ? $closedDate : $minimumDate;
        }
        $assessmentDateRules = ['required','date','before_or_equal:today'];
        if ($minimumDate) $assessmentDateRules[] = 'after_or_equal:' . $minimumDate;

        $rules = [
            'plan_id'=>['required','integer',Rule::in([(int) $plan->id])],
            'assessment_type'=>['required',Rule::in([$assessmentType])],
            'assessment_date'=>$assessmentDateRules,'information_sources'=>['required','array','min:1'],'information_sources.*'=>['string',Rule::in(array_keys(self::INFORMATION_SOURCES))],
            'participant_note'=>['nullable','string','max:10000'],'overall_note'=>['nullable','string','max:10000'],'items'=>['required','array'],
        ];
        foreach ($domains->flatMap->indicators as $indicator) {
            $rules['items.'.$indicator->id.'.score']=['required','integer','between:1,5'];
            $rules['items.'.$indicator->id.'.evidence']=['required','string','max:5000'];
            $rules['items.'.$indicator->id.'.development_note']=['nullable','string','max:5000'];
        }
        return $request->validate($rules,[
            'assessment_date.required'=>'กรุณาระบุวันที่ประเมิน',
            'assessment_date.before_or_equal'=>'วันที่ประเมินต้องไม่เกินวันปัจจุบัน',
            'assessment_date.after_or_equal'=>'วันที่ประเมินต้องไม่น้อยกว่าวันเริ่มแผน/วันที่สิ้นสุดแผนตามประเภทการประเมิน',
            'plan_id.in'=>'ไม่อนุญาตให้ย้ายผลประเมินไปยังแผนอื่น',
            'assessment_type.in'=>'ไม่อนุญาตให้เปลี่ยนประเภทของผลประเมินเดิม',
            'information_sources.required'=>'กรุณาเลือกแหล่งข้อมูลอย่างน้อย 1 รายการ',
            'information_sources.min'=>'กรุณาเลือกแหล่งข้อมูลอย่างน้อย 1 รายการ',
            'items.*.score.required'=>'กรุณาประเมินคะแนนทุกตัวชี้วัด',
            'items.*.evidence.required'=>'กรุณาระบุหลักฐาน/เหตุผลประกอบคะแนนทุกตัวชี้วัด',
        ]);
    }

    private function domains() { return DevelopmentDomain::query()->where('is_active',true)->with(['indicators'=>fn($q)=>$q->where('is_active',true)->orderBy('sort_order')])->orderBy('sort_order')->get(); }
    private function selectedPlan(int $clientId, int $planId=0): ?DevelopmentPlan { $q=DevelopmentPlan::query()->where('client_id',$clientId); return $planId>0?$q->find($planId):$q->orderByRaw("CASE WHEN status='active' THEN 0 ELSE 1 END")->orderByDesc('plan_no')->first(); }
    private function findAuthorizedClient(int $id): Client
    {
        $user = auth()->user();
        abort_unless($user, 403);
        $canViewAcrossHouses = (method_exists($user, 'isAdmin') && $user->isAdmin())
            || (method_exists($user, 'hasFormPermission') && $user->hasFormPermission('individual_development_center', 'view'));
        $query = Client::forUser($user);
        return $query->with(['house','project','target'])->findOrFail($id);
    }
    private function authorizeAction(string $action): void { abort_unless($this->can($action),403); }
    private function can(string $action): bool { $u=auth()->user(); return (bool)($u && ((method_exists($u,'isAdmin')&&$u->isAdmin()) || (method_exists($u,'hasFormPermission')&&$u->hasFormPermission(self::PERMISSION_KEY,$action)))); }
    private function text($v): ?string { $t=trim((string)($v??'')); return $t===''?null:$t; }
    private function average(DevelopmentAssessment $a): ?float { $vals=$a->items->pluck('score')->filter(fn($v)=>$v!==null); return $vals->isEmpty()?null:round((float)$vals->avg(),2); }
    private function domainRows(DevelopmentAssessment $a, ?DevelopmentAssessment $baseline): array { $rows=[]; foreach($this->domains() as $d){$cur=$a->items->filter(fn($i)=>(int)optional($i->indicator)->domain_id===(int)$d->id)->pluck('score')->filter();$base=$baseline?->items?->filter(fn($i)=>(int)optional($i->indicator)->domain_id===(int)$d->id)->pluck('score')->filter()??collect();$c=$cur->isEmpty()?null:round((float)$cur->avg(),2);$b=$base->isEmpty()?null:round((float)$base->avg(),2);$delta=($c!==null&&$b!==null)?round($c-$b,2):null;$rows[]=['name'=>$d->name,'baseline'=>$b,'current'=>$c,'delta'=>$delta,'trend'=>$delta===null?'none':($delta>0?'up':($delta<0?'down':'same'))];} return $rows; }
}
