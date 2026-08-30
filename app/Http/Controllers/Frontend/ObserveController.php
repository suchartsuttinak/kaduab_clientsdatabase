<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\Misbehavior;
use App\Models\Observe;
use App\Models\ObserveFollowup;
use App\Models\ObserveReferralRound;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ObserveController extends Controller
{
    private const TIMEZONE = 'Asia/Bangkok';
    private const TEXT_MAX = 5000;

    private const RISK_LEVELS = [
        'none',
        'low',
        'moderate',
        'high',
    ];

    private const WORKFLOW_STATUSES = [
        'ongoing',
        'goal_met',
        'referred',
    ];

    private const REFERRAL_PROCESSES = [
        'group_therapy',
        'family_therapy',
        'psychotherapy_counseling',
        'behavior_therapy',
        'referred_treatment',
    ];

    /**
     * เฉพาะกลุ่มนี้เท่านั้นที่สามารถบันทึก/แก้ไขข้อมูลการส่งต่อความช่วยเหลือ
     * รองรับทั้งบทบาทผู้บริหารระดับ executive และ manager ของระบบเดิม
     */
    private const REFERRAL_ROLES = [
        'admin',
        'executive',
        'manager',
        'social_worker',
    ];

    /**
     * หน้าเพิ่มข้อมูลพฤติกรรม
     */
    public function AddObserve($client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        return view('frontend.client.observe.observe_create', array_merge(
            $this->pageData($client),
            ['observe' => null]
        ));
    }

    /**
     * บันทึกข้อมูลพฤติกรรมใหม่
     */
    public function StoreObserve(Request $request)
    {
        $this->trimTextFields($request, [
            'behavior',
            'cause',
            'solution',
            'action',
            'obstacles',
            'result',
            'risk_detail',
            'followup_focus',
        ]);

        $clientInput = $request->validateWithBag('observeForm', [
            'client_id' => ['required', 'integer'],
        ], [
            'client_id.required' => 'ไม่พบข้อมูลผู้รับบริการ',
            'client_id.integer'  => 'ข้อมูลผู้รับบริการไม่ถูกต้อง',
        ]);

        // ตรวจสิทธิ์ก่อนนำ client_id ไปใช้กับ validation/query อื่น
        $client = Client::forUser(auth()->user())->findOrFail($clientInput['client_id']);
        $today = now(self::TIMEZONE)->toDateString();

        $rules = [
            'date' => [
                'required',
                'date',
                'before_or_equal:' . $today,
                Rule::unique('observes', 'date')
                    ->where(fn ($query) => $query->where('client_id', $client->id)),
            ],
            'misbehavior_id' => [
                'required',
                'integer',
                Rule::exists('misbehaviors', 'id'),
            ],
            'behavior'  => ['required', 'string', 'max:' . self::TEXT_MAX],
            'cause'     => ['required', 'string', 'max:' . self::TEXT_MAX],
            'solution'  => ['required', 'string', 'max:' . self::TEXT_MAX],
            'action'    => ['required', 'string', 'max:' . self::TEXT_MAX],
            'obstacles' => ['nullable', 'string', 'max:' . self::TEXT_MAX],
            'result'    => ['required', 'string', 'max:' . self::TEXT_MAX],
            'record_date' => [
                'required',
                'date',
                'after_or_equal:date',
                'before_or_equal:' . $today,
            ],
        ];

        $rules = array_merge(
            $rules,
            $this->workflowRules('date')
        );

        $data = $request->validateWithBag(
            'observeForm',
            $rules,
            array_merge(
                $this->observeValidationMessages(),
                $this->workflowValidationMessages('วันที่เกิดเหตุ')
            )
        );

        $this->validateRiskDetail($data, 'observeForm');
        $data = $this->normalizeWorkflowData($data);

        DB::transaction(function () use ($client, $data): void {
            // ล็อกผู้รับบริการ ป้องกันการบันทึกวันเดียวกันพร้อมกัน
            Client::query()->whereKey($client->id)->lockForUpdate()->firstOrFail();

            if (Observe::query()
                ->where('client_id', $client->id)
                ->whereDate('date', $data['date'])
                ->exists()) {
                $this->throwValidation('observeForm', [
                    'date' => 'วันที่นี้ถูกบันทึกแล้วสำหรับผู้รับบริการรายนี้',
                ]);
            }

            // forceFill ป้องกันปัญหา Model เดิมกำหนด $fillable ไว้ไม่ครบ
            $observe = new Observe();
            $observe->forceFill(array_merge($data, [
                'client_id' => $client->id,
                'recorder'  => auth()->user()->name ?? null,
            ]));
            $observe->save();

            $this->syncCaseActivity($client->id);
        });

        return redirect()
            ->route('observe.create', $client->id)
            ->with('success', 'บันทึกข้อมูลเรียบร้อย');
    }

    /**
     * หน้าแก้ไขข้อมูลพฤติกรรม
     */
    public function EditObserve($id)
    {
        $with = ['followups' => $this->followupOrderCallback()];
        if ($this->canManageReferral()) {
            $with[] = 'referralRounds';
        }

        $observe = $this->authorizedObserveQuery()
            ->with($with)
            ->findOrFail($id);

        $client = Client::forUser(auth()->user())->findOrFail($observe->client_id);

        return view('frontend.client.observe.observe_create', array_merge(
            $this->pageData($client),
            ['observe' => $observe]
        ));
    }

    /**
     * อัปเดตข้อมูลพฤติกรรม
     */
    public function UpdateObserve(Request $request, $id)
    {
        $observe = $this->authorizedObserveQuery()
            ->with(['followups' => $this->followupOrderCallback()])
            ->findOrFail($id);
        $client = Client::forUser(auth()->user())->findOrFail($observe->client_id);

        if ($this->currentObserveStatus($observe) === 'referred') {
            abort(403, 'รายการนี้ส่งต่อข้อมูลแล้ว งานในส่วนเดิมถูกปิด กรุณาดำเนินการต่อในส่วนการช่วยเหลือหลังส่งต่อ');
        }

        $this->trimTextFields($request, [
            'behavior',
            'cause',
            'solution',
            'action',
            'obstacles',
            'result',
            'risk_detail',
            'followup_focus',
        ]);

        $today = now(self::TIMEZONE)->toDateString();

        $rules = [
            'date' => [
                'required',
                'date',
                'before_or_equal:' . $today,
                Rule::unique('observes', 'date')
                    ->where(fn ($query) => $query->where('client_id', $observe->client_id))
                    ->ignore($observe->id),
            ],
            'misbehavior_id' => [
                'required',
                'integer',
                Rule::exists('misbehaviors', 'id'),
            ],
            'behavior'  => ['required', 'string', 'max:' . self::TEXT_MAX],
            'cause'     => ['required', 'string', 'max:' . self::TEXT_MAX],
            'solution'  => ['required', 'string', 'max:' . self::TEXT_MAX],
            'action'    => ['required', 'string', 'max:' . self::TEXT_MAX],
            'obstacles' => ['nullable', 'string', 'max:' . self::TEXT_MAX],
            'result'    => ['required', 'string', 'max:' . self::TEXT_MAX],
            'record_date' => [
                'required',
                'date',
                'after_or_equal:date',
                'before_or_equal:' . $today,
            ],
        ];

        /*
         * เมื่อมีรอบติดตามแล้ว จะไม่อนุญาตให้ย้อนกลับไปเปลี่ยน lifecycle ของรอบแรก
         * เพื่อไม่ให้ประวัติขัดกับรอบถัดไป
         */
        $canEditWorkflow = $observe->followups->isEmpty()
            && ($this->currentObserveStatus($observe) !== 'referred');

        if ($canEditWorkflow) {
            $rules = array_merge(
                $rules,
                $this->workflowRules('date')
            );
        }

        $data = $request->validateWithBag(
            'observeForm',
            $rules,
            array_merge(
                $this->observeValidationMessages(),
                $this->workflowValidationMessages('วันที่เกิดเหตุ')
            )
        );

        if ($canEditWorkflow) {
            $this->validateRiskDetail($data, 'observeForm');
            $data = $this->normalizeWorkflowData($data);
        }

        DB::transaction(function () use ($observe, $client, $data): void {
            $lockedObserve = $this->authorizedObserveQuery()
                ->lockForUpdate()
                ->findOrFail($observe->id);

            if (Observe::query()
                ->where('client_id', $lockedObserve->client_id)
                ->whereDate('date', $data['date'])
                ->where('id', '<>', $lockedObserve->id)
                ->exists()) {
                $this->throwValidation('observeForm', [
                    'date' => 'วันที่นี้ถูกบันทึกแล้วสำหรับผู้รับบริการรายนี้',
                ]);
            }

            $lockedObserve->forceFill(array_merge($data, [
                'client_id' => $lockedObserve->client_id,
                'recorder'  => auth()->user()->name ?? null,
            ]));
            $lockedObserve->save();

            $this->syncCaseActivity($client->id);
        });

        return redirect()
            ->route('observe.create', $client->id)
            ->with('success', 'อัปเดตข้อมูลเรียบร้อย');
    }

    /**
     * ลบข้อมูลพฤติกรรม
     */
    public function DeleteObserve($id)
    {
        $observe = $this->authorizedObserveQuery()->findOrFail($id);

        if (
            !$this->canManageReferral()
            && (
                ($observe->status ?? 'ongoing') === 'referred'
                || $observe->followups()->where('status', 'referred')->exists()
            )
        ) {
            abort(403, 'ข้อมูลการส่งต่อความช่วยเหลือจำกัดสิทธิ์เฉพาะ Admin ผู้บริหาร และนักสังคมสงเคราะห์');
        }

        $clientId = $observe->client_id;

        DB::transaction(function () use ($observe, $clientId): void {
            $lockedObserve = $this->authorizedObserveQuery()
                ->with('followups')
                ->lockForUpdate()
                ->findOrFail($observe->id);

            // รองรับทั้งฐานข้อมูลที่ตั้ง cascade และฐานข้อมูลเดิมที่ยังไม่ได้ตั้ง cascade
            $lockedObserve->followups()->delete();
            $lockedObserve->delete();

            $this->syncCaseActivity($clientId);
        });

        return redirect()
            ->route('observe.create', $clientId)
            ->with('success', 'ลบข้อมูลเรียบร้อย');
    }

    /**
     * บันทึกการติดตามผล
     */
    public function StoreFollowup(Request $request)
    {
        $observeId = (int) $request->input('observe_id');
        $observe = $this->authorizedObserveQuery()
            ->with(['followups' => $this->followupOrderCallback()])
            ->findOrFail($observeId);
        Client::forUser(auth()->user())->findOrFail($observe->client_id);

        $bag = 'followupStore' . $observe->id;

        if ($this->currentObserveStatus($observe) !== 'ongoing') {
            $this->throwValidation($bag, [
                'status' => 'เคสในรอบนี้สิ้นสุดแล้ว ไม่สามารถเพิ่มการติดตามผลรอบถัดไปได้',
            ]);
        }

        $this->trimTextFields($request, [
            'followup_action',
            'followup_result',
            'risk_detail',
            'followup_focus',
        ]);

        $today = now(self::TIMEZONE)->toDateString();

        $rules = [
            'followup_date' => [
                'required',
                'date',
                'after_or_equal:' . $observe->date,
                'before_or_equal:' . $today,
                Rule::unique('observe_followups', 'followup_date')
                    ->where(fn ($query) => $query->where('observe_id', $observe->id)),
            ],
            'followup_action' => ['nullable', 'string', 'max:' . self::TEXT_MAX],
            'followup_result' => ['nullable', 'string', 'max:' . self::TEXT_MAX],
        ];

        $rules = array_merge(
            $rules,
            $this->workflowRules('followup_date')
        );

        $validator = Validator::make(
            $request->all(),
            $rules,
            array_merge(
                $this->followupValidationMessages($observe->date),
                $this->workflowValidationMessages('วันที่ติดตาม')
            )
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, $bag)
                ->withInput();
        }

        $data = $validator->validated();

        $this->validateRiskDetail($data, $bag);
        $data = $this->normalizeWorkflowData($data);

        DB::transaction(function () use ($observe, $data, $bag): void {
            $lockedObserve = $this->authorizedObserveQuery()
                ->with(['followups' => $this->followupOrderCallback()])
                ->lockForUpdate()
                ->findOrFail($observe->id);

            if ($this->currentObserveStatus($lockedObserve) !== 'ongoing') {
                $this->throwValidation($bag, [
                    'status' => 'เคสในรอบนี้สิ้นสุดแล้ว ไม่สามารถเพิ่มการติดตามผลรอบถัดไปได้',
                ]);
            }

            $lastFollowup = ObserveFollowup::query()
                ->where('observe_id', $lockedObserve->id)
                ->orderByDesc('followup_date')
                ->orderByDesc('id')
                ->first();

            if ($lastFollowup) {
                $newDate = Carbon::parse($data['followup_date'], self::TIMEZONE)->startOfDay();
                $lastDate = Carbon::parse($lastFollowup->followup_date, self::TIMEZONE)->startOfDay();

                if ($newDate->lte($lastDate)) {
                    $this->throwValidation($bag, [
                        'followup_date' => 'วันที่ติดตามครั้งใหม่ต้องมากกว่าวันที่ติดตามครั้งล่าสุด ('
                            . $lastDate->format('d/m/Y') . ') และห้ามซ้ำ',
                    ]);
                }
            }

            $nextFollowupCount = ((int) ObserveFollowup::query()
                ->where('observe_id', $lockedObserve->id)
                ->max('followup_count')) + 1;

            $followup = new ObserveFollowup();
            $followup->forceFill(array_merge($data, [
                'observe_id'      => $lockedObserve->id,
                'followup_count'  => $nextFollowupCount,
            ]));
            $followup->save();

            $this->syncCaseActivity($lockedObserve->client_id);
        });

        return redirect()
            ->route('observe.edit', $observe->id)
            ->with('success', 'บันทึกการติดตามผลเรียบร้อย');
    }

    /**
     * ลบการติดตามผลและเรียงเลขครั้งใหม่ตามวันที่
     */
    public function DeleteFollowup($id)
    {
        $followup = $this->authorizedFollowupQuery()->findOrFail($id);

        $observe = $followup->observeRelation;

        if ($observe && $this->currentObserveStatus($observe) === 'referred') {
            abort(403, 'รายการนี้ส่งต่อข้อมูลแล้ว งานติดตามในส่วนเดิมถูกล็อกเพื่อรักษาประวัติการส่งต่อ');
        }

        if (!$observe) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลพฤติกรรมที่สัมพันธ์กับการติดตามผลนี้');
        }

        Client::forUser(auth()->user())->findOrFail($observe->client_id);
        $observeId = $observe->id;
        $clientId = $observe->client_id;

        DB::transaction(function () use ($followup, $observeId, $clientId): void {
            $this->authorizedObserveQuery()->lockForUpdate()->findOrFail($observeId);

            $lockedFollowup = $this->authorizedFollowupQuery()
                ->lockForUpdate()
                ->findOrFail($followup->id);

            $lockedFollowup->delete();
            $this->renumberFollowups($observeId);
            $this->syncCaseActivity($clientId);
        });

        return redirect()
            ->route('observe.edit', $observeId)
            ->with('success', 'ลบการติดตามผลเรียบร้อย');
    }

    /**
     * เปิดหน้าแก้ไขและเปิด Modal ของรายการติดตามที่เลือก
     */
    public function EditFollowup($id)
    {
        $followup = $this->authorizedFollowupQuery()->findOrFail($id);
        $observe = $followup->observeRelation;

        if (!$observe) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลพฤติกรรมที่สัมพันธ์กับการติดตามผลนี้');
        }

        if ($this->currentObserveStatus($observe) === 'referred') {
            abort(403, 'รายการนี้ส่งต่อข้อมูลแล้ว งานติดตามในส่วนเดิมถูกปิด');
        }

        $observe->load(['followups' => $this->followupOrderCallback()]);
        if ($this->canManageReferral()) {
            $observe->load('referralRounds');
        }
        $client = Client::forUser(auth()->user())->findOrFail($observe->client_id);

        return view('frontend.client.observe.observe_create', array_merge(
            $this->pageData($client),
            [
                'observe'   => $observe,
                'followup'  => $followup,
                'openModal' => 'editFollowupModal' . $followup->id,
            ]
        ));
    }

    /**
     * อัปเดตการติดตามผล
     */
    public function UpdateFollowup(Request $request, $id)
    {
        $followup = $this->authorizedFollowupQuery()->findOrFail($id);
        $observe = $followup->observeRelation;

        if (!$observe) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลพฤติกรรมที่สัมพันธ์กับการติดตามผลนี้');
        }

        if (($followup->status ?? 'ongoing') === 'referred' || $this->currentObserveStatus($observe) === 'referred') {
            abort(403, 'รายการนี้ส่งต่อข้อมูลแล้ว งานติดตามในส่วนเดิมถูกปิด');
        }

        Client::forUser(auth()->user())->findOrFail($observe->client_id);

        $this->trimTextFields($request, [
            'followup_action',
            'followup_result',
            'risk_detail',
            'followup_focus',
        ]);

        $bag = 'followupUpdate' . $followup->id;
        $today = now(self::TIMEZONE)->toDateString();

        $hasLaterFollowup = ObserveFollowup::query()
            ->where('observe_id', $followup->observe_id)
            ->where('followup_count', '>', $followup->followup_count)
            ->exists();

        $canEditWorkflow = (($followup->status ?? 'ongoing') !== 'referred');

        $rules = [
            'followup_date' => [
                'required',
                'date',
                'after_or_equal:' . $observe->date,
                'before_or_equal:' . $today,
                Rule::unique('observe_followups', 'followup_date')
                    ->where(fn ($query) => $query->where('observe_id', $observe->id))
                    ->ignore($followup->id),
            ],
            'followup_action' => ['nullable', 'string', 'max:' . self::TEXT_MAX],
            'followup_result' => ['nullable', 'string', 'max:' . self::TEXT_MAX],
        ];

        if ($canEditWorkflow) {
            $allowedStatuses = $hasLaterFollowup ? ['ongoing'] : null;

            $rules = array_merge(
                $rules,
                $this->workflowRules(
                    'followup_date',
                    $allowedStatuses
                )
            );
        }

        $validator = Validator::make(
            $request->all(),
            $rules,
            array_merge(
                $this->followupValidationMessages($observe->date),
                $this->workflowValidationMessages('วันที่ติดตาม')
            )
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, $bag)
                ->withInput();
        }

        $data = $validator->validated();

        if ($canEditWorkflow) {
            $this->validateRiskDetail($data, $bag);
            $data = $this->normalizeWorkflowData($data);
        }

        DB::transaction(function () use (
            $followup,
            $observe,
            $data,
            $bag,
            $hasLaterFollowup,
            $canEditWorkflow
        ): void {
            $this->authorizedObserveQuery()->lockForUpdate()->findOrFail($observe->id);

            $lockedFollowup = $this->authorizedFollowupQuery()
                ->lockForUpdate()
                ->findOrFail($followup->id);

            $newDate = Carbon::parse($data['followup_date'], self::TIMEZONE)->startOfDay();

            $previous = ObserveFollowup::query()
                ->where('observe_id', $lockedFollowup->observe_id)
                ->where('followup_count', '<', $lockedFollowup->followup_count)
                ->orderByDesc('followup_count')
                ->first();

            if ($previous) {
                $previousDate = Carbon::parse($previous->followup_date, self::TIMEZONE)->startOfDay();

                if ($newDate->lte($previousDate)) {
                    $this->throwValidation($bag, [
                        'followup_date' => 'วันที่ติดตามครั้งที่ ' . $lockedFollowup->followup_count
                            . ' ต้องมากกว่าวันที่ของครั้งที่ ' . $previous->followup_count . ' และห้ามซ้ำ',
                    ]);
                }
            }

            $next = ObserveFollowup::query()
                ->where('observe_id', $lockedFollowup->observe_id)
                ->where('followup_count', '>', $lockedFollowup->followup_count)
                ->orderBy('followup_count')
                ->first();

            if ($next) {
                $nextDate = Carbon::parse($next->followup_date, self::TIMEZONE)->startOfDay();

                if ($newDate->gte($nextDate)) {
                    $this->throwValidation($bag, [
                        'followup_date' => 'วันที่ติดตามครั้งที่ ' . $lockedFollowup->followup_count
                            . ' ต้องน้อยกว่าวันที่ของครั้งที่ ' . $next->followup_count . ' และห้ามซ้ำ',
                    ]);
                }
            }

            if (
                $hasLaterFollowup
                && $canEditWorkflow
                && ($data['status'] ?? 'ongoing') !== 'ongoing'
            ) {
                $this->throwValidation($bag, [
                    'status' => 'รอบนี้มีรอบติดตามถัดไปแล้ว จึงไม่สามารถกำหนดเป็นสถานะสิ้นสุดได้',
                ]);
            }

            $lockedFollowup->forceFill($data);
            $lockedFollowup->save();

            $this->syncCaseActivity($observe->client_id);
        });

        return redirect()
            ->route('observe.edit', $observe->id)
            ->with('success', 'อัปเดตการติดตามผลเรียบร้อย');
    }

    /**
     * บันทึกรอบการช่วยเหลือหลังส่งต่อ
     * ใช้งานได้เฉพาะนักสังคมสงเคราะห์ ผู้บริหาร และ Admin
     */
    public function StoreReferralRound(Request $request)
    {
        $this->ensureCanManageReferral();

        $observeId = (int) $request->input('observe_id');
        $observe = $this->authorizedObserveQuery()
            ->with([
                'followups' => $this->followupOrderCallback(),
                'referralRounds',
            ])
            ->findOrFail($observeId);

        Client::forUser(auth()->user())->findOrFail($observe->client_id);

        $bag = 'referralStore' . $observe->id;

        if ($this->currentObserveStatus($observe) !== 'referred') {
            $this->throwValidation($bag, [
                'status' => 'รายการนี้ยังไม่ได้อยู่ในสถานะส่งต่อข้อมูล',
            ]);
        }

        $latestReferral = $observe->referralRounds->last();
        if ($latestReferral && ($latestReferral->status ?? 'ongoing') === 'goal_met') {
            $this->throwValidation($bag, [
                'status' => 'การช่วยเหลือหลังส่งต่อบรรลุเป้าหมายแล้ว ไม่สามารถเพิ่มรอบใหม่ได้',
            ]);
        }

        $this->trimTextFields($request, [
            'solution',
            'result',
            'risk_detail',
            'followup_focus',
        ]);

        $validator = Validator::make(
            $request->all(),
            $this->referralRoundRules($observe, null),
            $this->referralRoundMessages($observe)
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, $bag)
                ->withInput();
        }

        $data = $validator->validated();
        $this->validateRiskDetail($data, $bag);
        $data = $this->normalizeReferralRoundData($data);

        DB::transaction(function () use ($observe, $data, $bag): void {
            $lockedObserve = $this->authorizedObserveQuery()
                ->with([
                    'followups' => $this->followupOrderCallback(),
                    'referralRounds',
                ])
                ->lockForUpdate()
                ->findOrFail($observe->id);

            if ($this->currentObserveStatus($lockedObserve) !== 'referred') {
                $this->throwValidation($bag, [
                    'status' => 'รายการนี้ไม่ได้อยู่ในสถานะส่งต่อข้อมูลแล้ว',
                ]);
            }

            $latestReferral = ObserveReferralRound::query()
                ->where('observe_id', $lockedObserve->id)
                ->orderByDesc('round_no')
                ->orderByDesc('id')
                ->first();

            if ($latestReferral && ($latestReferral->status ?? 'ongoing') === 'goal_met') {
                $this->throwValidation($bag, [
                    'status' => 'การช่วยเหลือหลังส่งต่อบรรลุเป้าหมายแล้ว ไม่สามารถเพิ่มรอบใหม่ได้',
                ]);
            }

            if ($latestReferral) {
                $newDate = Carbon::parse($data['action_date'], self::TIMEZONE)->startOfDay();
                $lastDate = Carbon::parse($latestReferral->action_date, self::TIMEZONE)->startOfDay();

                if ($newDate->lte($lastDate)) {
                    $this->throwValidation($bag, [
                        'action_date' => 'วันที่ดำเนินการรอบใหม่ต้องมากกว่าวันที่ของรอบล่าสุด ('
                            . $lastDate->format('d/m/Y') . ')',
                    ]);
                }
            }

            $nextRound = ((int) ObserveReferralRound::query()
                ->where('observe_id', $lockedObserve->id)
                ->max('round_no')) + 1;

            ObserveReferralRound::create(array_merge($data, [
                'observe_id' => $lockedObserve->id,
                'round_no' => $nextRound,
                'recorder_user_id' => auth()->id(),
                'recorder_name' => auth()->user()->name ?? null,
            ]));

            $this->syncCaseActivity($lockedObserve->client_id);
        });

        return redirect()
            ->route('observe.edit', $observe->id)
            ->with('success', 'บันทึกการช่วยเหลือหลังส่งต่อเรียบร้อย');
    }

    /**
     * อัปเดตรอบการช่วยเหลือหลังส่งต่อ
     */
    public function UpdateReferralRound(Request $request, $id)
    {
        $this->ensureCanManageReferral();

        $round = $this->authorizedReferralRoundQuery()->findOrFail($id);
        $observe = $round->observeRelation;

        if (!$observe) {
            abort(404);
        }

        $observe->load([
            'followups' => $this->followupOrderCallback(),
            'referralRounds',
        ]);

        Client::forUser(auth()->user())->findOrFail($observe->client_id);

        $bag = 'referralUpdate' . $round->id;

        if ($this->currentObserveStatus($observe) !== 'referred') {
            $this->throwValidation($bag, [
                'status' => 'รายการนี้ไม่ได้อยู่ในสถานะส่งต่อข้อมูล',
            ]);
        }

        $hasLaterRound = ObserveReferralRound::query()
            ->where('observe_id', $observe->id)
            ->where('round_no', '>', $round->round_no)
            ->exists();

        if ($hasLaterRound) {
            $this->throwValidation($bag, [
                'status' => 'รอบนี้มีรอบถัดไปแล้ว จึงล็อกการแก้ไขเพื่อรักษาลำดับประวัติ',
            ]);
        }

        $this->trimTextFields($request, [
            'solution',
            'result',
            'risk_detail',
            'followup_focus',
        ]);

        $validator = Validator::make(
            $request->all(),
            $this->referralRoundRules($observe, $round),
            $this->referralRoundMessages($observe)
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, $bag)
                ->withInput();
        }

        $data = $validator->validated();
        $this->validateRiskDetail($data, $bag);
        $data = $this->normalizeReferralRoundData($data);

        $previous = ObserveReferralRound::query()
            ->where('observe_id', $observe->id)
            ->where('round_no', '<', $round->round_no)
            ->orderByDesc('round_no')
            ->first();

        if ($previous) {
            $newDate = Carbon::parse($data['action_date'], self::TIMEZONE)->startOfDay();
            $previousDate = Carbon::parse($previous->action_date, self::TIMEZONE)->startOfDay();

            if ($newDate->lte($previousDate)) {
                $this->throwValidation($bag, [
                    'action_date' => 'วันที่ดำเนินการต้องมากกว่าวันที่ของรอบก่อนหน้า',
                ]);
            }
        }

        DB::transaction(function () use ($round, $observe, $data): void {
            $lockedRound = $this->authorizedReferralRoundQuery()
                ->lockForUpdate()
                ->findOrFail($round->id);

            $lockedRound->update(array_merge($data, [
                'recorder_user_id' => auth()->id(),
                'recorder_name' => auth()->user()->name ?? null,
            ]));

            $this->syncCaseActivity($observe->client_id);
        });

        return redirect()
            ->route('observe.edit', $observe->id)
            ->with('success', 'อัปเดตการช่วยเหลือหลังส่งต่อเรียบร้อย');
    }

    /**
     * ลบรอบการช่วยเหลือหลังส่งต่อ (อนุญาตเฉพาะรอบล่าสุด)
     */
    public function DeleteReferralRound($id)
    {
        $this->ensureCanManageReferral();

        $round = $this->authorizedReferralRoundQuery()->findOrFail($id);
        $observe = $round->observeRelation;

        if (!$observe) {
            abort(404);
        }

        Client::forUser(auth()->user())->findOrFail($observe->client_id);

        $hasLaterRound = ObserveReferralRound::query()
            ->where('observe_id', $observe->id)
            ->where('round_no', '>', $round->round_no)
            ->exists();

        if ($hasLaterRound) {
            return redirect()
                ->route('observe.edit', $observe->id)
                ->with('error', 'ลบไม่ได้ เนื่องจากรอบนี้มีรอบถัดไปแล้ว');
        }

        DB::transaction(function () use ($round, $observe): void {
            $this->authorizedReferralRoundQuery()
                ->lockForUpdate()
                ->findOrFail($round->id)
                ->delete();

            $this->syncCaseActivity($observe->client_id);
        });

        return redirect()
            ->route('observe.edit', $observe->id)
            ->with('success', 'ลบรอบการช่วยเหลือหลังส่งต่อเรียบร้อย');
    }

    /**
     * รายงานข้อมูลพฤติกรรม
     */
    public function ReportObserve($id)
    {
        $observe = $this->authorizedObserveQuery()
            ->with([
                'client',
                'misbehavior',
                'followups' => $this->followupOrderCallback(),
            ])
            ->findOrFail($id);

        $client = Client::forUser(auth()->user())->findOrFail($observe->client_id);
        $canManageObserveReferral = $this->canManageReferral();

        return view(
            'frontend.client.observe.observe_report',
            compact('observe', 'client', 'canManageObserveReferral')
        );
    }


    /**
     * รายงานการช่วยเหลือหลังส่งต่อ
     * จำกัดสิทธิ์เหมือนส่วนการทำงานหลังส่งต่อ เพื่อไม่เปิดเผยข้อมูลให้ผู้ใช้ที่ไม่เกี่ยวข้อง
     */
    public function ReportReferral($id)
    {
        $this->ensureCanManageReferral();

        $observe = $this->authorizedObserveQuery()
            ->with([
                'client',
                'misbehavior',
                'followups' => $this->followupOrderCallback(),
                'referralRounds',
            ])
            ->findOrFail($id);

        $client = Client::forUser(auth()->user())->findOrFail($observe->client_id);

        if ($this->currentObserveStatus($observe) !== 'referred') {
            return redirect()
                ->route('observe.edit', $observe->id)
                ->with('error', 'ยังไม่มีการส่งต่อข้อมูลสำหรับรายการนี้');
        }

        return view(
            'frontend.client.observe.observe_referral_report',
            compact('observe', 'client')
        );
    }

    /**
     * Query ข้อมูลพฤติกรรมที่ผู้ใช้ปัจจุบันมีสิทธิ์เข้าถึง
     */
    private function authorizedObserveQuery()
    {
        return Observe::query()
            ->whereHas('client', fn ($query) => $query->forUser(auth()->user()));
    }

    /**
     * Query การติดตามผลที่ผู้ใช้ปัจจุบันมีสิทธิ์เข้าถึง
     */
    private function authorizedFollowupQuery()
    {
        return ObserveFollowup::query()
            ->with('observeRelation')
            ->whereHas('observeRelation.client', fn ($query) => $query->forUser(auth()->user()));
    }

    /**
     * Query รอบการช่วยเหลือหลังส่งต่อที่ผู้ใช้ปัจจุบันมีสิทธิ์เข้าถึง
     */
    private function authorizedReferralRoundQuery()
    {
        return ObserveReferralRound::query()
            ->with('observeRelation')
            ->whereHas('observeRelation.client', fn ($query) => $query->forUser(auth()->user()));
    }

    /**
     * ข้อมูลที่ใช้ร่วมกันในหน้ารายการ/แก้ไข
     */
    private function pageData(Client $client): array
    {
        $with = ['followups' => $this->followupOrderCallback()];
        if ($this->canManageReferral()) {
            $with[] = 'referralRounds';
        }

        return [
            'client'       => $client,
            'client_id'    => $client->id,
            'canManageObserveReferral' => $this->canManageReferral(),
            'misbehaviors' => Misbehavior::query()
                ->orderBy('misbehavior_name')
                ->get(),
            'observes' => Observe::query()
                ->with($with)
                ->where('client_id', $client->id)
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->get(),
        ];
    }

    /**
     * ลำดับมาตรฐานของการติดตามผล
     */
    private function followupOrderCallback(): callable
    {
        return fn ($query) => $query
            ->orderBy('followup_count')
            ->orderBy('followup_date')
            ->orderBy('id');
    }

    /**
     * เรียงเลขครั้งใหม่หลังลบ โดยยึดวันที่และ id เป็นลำดับคงที่
     */
    private function renumberFollowups(int $observeId): void
    {
        $followups = ObserveFollowup::query()
            ->where('observe_id', $observeId)
            ->orderBy('followup_date')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($followups as $index => $item) {
            $newCount = $index + 1;

            if ((int) $item->followup_count !== $newCount) {
                $item->update(['followup_count' => $newCount]);
            }
        }
    }

    /**
     * ให้ CaseActivity แสดงเหตุการณ์ล่าสุดตามวันที่จริง ไม่ใช่เวลาที่กดแก้ไข
     */
    private function syncCaseActivity(int $clientId): void
    {
        CaseActivity::query()
            ->where('client_id', $clientId)
            ->where('module', 'observe')
            ->delete();

        $latestObserve = Observe::query()
            ->where('client_id', $clientId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->first();

        $latestFollowup = ObserveFollowup::query()
            ->with('observeRelation:id,client_id')
            ->whereHas('observeRelation', fn ($query) => $query->where('client_id', $clientId))
            ->orderByDesc('followup_date')
            ->orderByDesc('id')
            ->first();

        if (!$latestObserve && !$latestFollowup) {
            return;
        }

        $observeDate = $latestObserve
            ? Carbon::parse($latestObserve->date, self::TIMEZONE)->startOfDay()
            : null;

        $followupDate = $latestFollowup
            ? Carbon::parse($latestFollowup->followup_date, self::TIMEZONE)->startOfDay()
            : null;

        $useFollowup = $latestFollowup
            && (!$observeDate || $followupDate->greaterThanOrEqualTo($observeDate));

        if ($useFollowup) {
            CaseActivity::record([
                'client_id'   => $clientId,
                'module'      => 'observe',
                'type'        => 'warning',
                'title'       => 'การติดตามพฤติกรรมล่าสุด',
                'description' => Str::limit(
                    'ติดตามครั้งที่ ' . ($latestFollowup->followup_count ?: '-')
                    . ' | วันที่ติดตาม: ' . ($latestFollowup->followup_date ?: '-')
                    . ' | ผลติดตาม: ' . ($latestFollowup->followup_result ?: '-'),
                    250
                ),
                'occurred_at' => $followupDate,
                'icon'        => 'bi-clipboard2-check',
                'url'         => route('observe.edit', $latestFollowup->observe_id),
            ]);

            return;
        }

        CaseActivity::record([
            'client_id'   => $clientId,
            'module'      => 'observe',
            'type'        => 'warning',
            'title'       => 'พฤติกรรมไม่เหมาะสมล่าสุด',
            'description' => Str::limit(
                'วันที่เกิดเหตุ: ' . ($latestObserve->date ?: '-')
                . ' | พฤติกรรม: ' . ($latestObserve->behavior ?: '-')
                . ' | ผลการดำเนินการ: ' . ($latestObserve->result ?: '-'),
                250
            ),
            'occurred_at' => $observeDate,
            'icon'        => 'bi-exclamation-triangle',
            'url'         => route('observe.edit', $latestObserve->id),
        ]);
    }

    /**
     * ตัดช่องว่างหน้า/หลัง ก่อนตรวจสอบและบันทึก
     */
    private function trimTextFields(Request $request, array $fields): void
    {
        $values = [];

        foreach ($fields as $field) {
            $value = $request->input($field);
            $values[$field] = is_string($value) ? trim($value) : $value;
        }

        $request->merge($values);
    }


    /**
     * Validation lifecycle ใช้ร่วมกันทั้งรอบแรกและรอบติดตาม
     */
    private function workflowRules(
        string $dateField,
        ?array $allowedStatuses = null
    ): array {
        $statuses = $allowedStatuses ?? self::WORKFLOW_STATUSES;

        return [
            'risk_level' => [
                'required',
                Rule::in(self::RISK_LEVELS),
            ],
            'risk_detail' => [
                'nullable',
                'string',
                'max:' . self::TEXT_MAX,
            ],
            'status' => [
                'required',
                Rule::in($statuses),
            ],
            'next_appointment_date' => [
                Rule::requiredIf(fn () => request('status') === 'ongoing'),
                'nullable',
                'date',
                'after:' . $dateField,
            ],
            'followup_focus' => [
                Rule::requiredIf(fn () => request('status') === 'ongoing'),
                'nullable',
                'string',
                'max:' . self::TEXT_MAX,
            ],
        ];
    }

    private function workflowValidationMessages(string $roundDateLabel): array
    {
        return [
            'risk_level.required' => 'กรุณาระบุระดับความเสี่ยง',
            'risk_level.in' => 'ระดับความเสี่ยงไม่ถูกต้อง',
            'risk_detail.max' => 'รายละเอียดความเสี่ยงต้องไม่เกิน ' . self::TEXT_MAX . ' ตัวอักษร',
            'status.required' => 'กรุณาเลือกสถานะในรอบนี้',
            'status.in' => 'สถานะที่เลือกไม่ถูกต้อง',
            'next_appointment_date.required' => 'กรุณาระบุวันนัดหมายครั้งต่อไป เมื่อสถานะอยู่ระหว่างการดำเนินงาน',
            'next_appointment_date.date' => 'วันนัดหมายครั้งต่อไปไม่ถูกต้อง',
            'next_appointment_date.after' => 'วันนัดหมายครั้งต่อไปต้องอยู่หลัง' . $roundDateLabel,
            'followup_focus.required' => 'กรุณาระบุประเด็นที่จะดำเนินการต่อในรอบถัดไป',
            'followup_focus.max' => 'ประเด็นที่จะดำเนินการต่อในรอบถัดไปต้องไม่เกิน ' . self::TEXT_MAX . ' ตัวอักษร',
        ];
    }

    private function validateRiskDetail(array $data, string $errorBag): void
    {
        if (
            in_array($data['risk_level'] ?? null, ['moderate', 'high'], true)
            && blank($data['risk_detail'] ?? null)
        ) {
            $this->throwValidation($errorBag, [
                'risk_detail' => 'กรุณาระบุรายละเอียดความเสี่ยง เมื่อประเมินว่ามีความเสี่ยงระดับปานกลางหรือสูง',
            ]);
        }
    }

    /**
     * ล้างข้อมูลเงื่อนไขที่ไม่สัมพันธ์กับสถานะ เพื่อไม่ให้ข้อมูลเก่าค้างผิดบริบท
     */
    private function normalizeWorkflowData(array $data): array
    {
        if (($data['risk_level'] ?? 'none') === 'none') {
            $data['risk_detail'] = null;
        }

        if (($data['status'] ?? 'ongoing') !== 'ongoing') {
            $data['next_appointment_date'] = null;
            $data['followup_focus'] = null;
        }

        return $data;
    }

    /**
     * สถานะปัจจุบันของเคส = สถานะรอบติดตามล่าสุด ถ้ายังไม่มีให้ใช้รอบแรก
     */
    private function currentObserveStatus(Observe $observe): string
    {
        if ($observe->relationLoaded('followups')) {
            // relation นี้ถูกโหลดด้วย followupOrderCallback() จึงใช้รายการสุดท้ายเป็นรอบล่าสุดได้
            $latest = $observe->followups->last();
        } else {
            $latest = ObserveFollowup::query()
                ->where('observe_id', $observe->id)
                ->orderByDesc('followup_count')
                ->orderByDesc('followup_date')
                ->orderByDesc('id')
                ->first();
        }

        return (string) ($latest?->status ?: $observe->status ?: 'ongoing');
    }

    private function canManageReferral(): bool
    {
        $role = strtolower(trim((string) (auth()->user()->role ?? '')));

        return in_array($role, self::REFERRAL_ROLES, true);
    }

    private function ensureCanManageReferral(): void
    {
        if (!$this->canManageReferral()) {
            abort(403, 'ส่วนการช่วยเหลือหลังส่งต่อจำกัดสิทธิ์เฉพาะนักสังคมสงเคราะห์ ผู้บริหาร และ Admin');
        }
    }

    private function referralSourceDate(Observe $observe): string
    {
        if ($observe->relationLoaded('followups')) {
            $referredFollowup = $observe->followups
                ->filter(fn ($item) => ($item->status ?? 'ongoing') === 'referred')
                ->last();
        } else {
            $referredFollowup = ObserveFollowup::query()
                ->where('observe_id', $observe->id)
                ->where('status', 'referred')
                ->orderByDesc('followup_count')
                ->orderByDesc('followup_date')
                ->orderByDesc('id')
                ->first();
        }

        return (string) ($referredFollowup?->followup_date ?: $observe->date);
    }

    private function referralRoundRules(Observe $observe, ?ObserveReferralRound $ignoreRound): array
    {
        $today = now(self::TIMEZONE)->toDateString();
        $sourceDate = $this->referralSourceDate($observe);

        $dateRule = Rule::unique('observe_referral_rounds', 'action_date')
            ->where(fn ($query) => $query->where('observe_id', $observe->id));

        if ($ignoreRound) {
            $dateRule->ignore($ignoreRound->id);
        }

        return [
            'action_date' => [
                'required',
                'date',
                'after_or_equal:' . $sourceDate,
                'before_or_equal:' . $today,
                $dateRule,
            ],
            'assistance_process' => [
                'required',
                Rule::in(self::REFERRAL_PROCESSES),
            ],
            'solution' => [
                'required',
                'string',
                'max:' . self::TEXT_MAX,
            ],
            'result' => [
                'required',
                'string',
                'max:' . self::TEXT_MAX,
            ],
            'risk_level' => [
                'required',
                Rule::in(self::RISK_LEVELS),
            ],
            'risk_detail' => [
                'nullable',
                'string',
                'max:' . self::TEXT_MAX,
            ],
            'status' => [
                'required',
                Rule::in(['ongoing', 'goal_met']),
            ],
            'next_appointment_date' => [
                Rule::requiredIf(fn () => request('status') === 'ongoing'),
                'nullable',
                'date',
                'after:action_date',
            ],
            'followup_focus' => [
                Rule::requiredIf(fn () => request('status') === 'ongoing'),
                'nullable',
                'string',
                'max:' . self::TEXT_MAX,
            ],
        ];
    }

    private function referralRoundMessages(Observe $observe): array
    {
        return [
            'action_date.required' => 'กรุณาระบุวันที่ดำเนินการในรอบนี้',
            'action_date.date' => 'วันที่ดำเนินการไม่ถูกต้อง',
            'action_date.after_or_equal' => 'วันที่ดำเนินการต้องไม่น้อยกว่าวันที่ส่งต่อข้อมูล (' . $this->referralSourceDate($observe) . ')',
            'action_date.before_or_equal' => 'วันที่ดำเนินการต้องไม่เกินวันปัจจุบัน',
            'action_date.unique' => 'วันที่นี้ถูกใช้ในรอบการช่วยเหลือหลังส่งต่อแล้ว กรุณาเลือกวันอื่น',
            'assistance_process.required' => 'กรุณาเลือกกระบวนการช่วยเหลือ',
            'assistance_process.in' => 'กระบวนการช่วยเหลือที่เลือกไม่ถูกต้อง',
            'solution.required' => 'กรุณาระบุแนวทางแก้ไข',
            'solution.max' => 'แนวทางแก้ไขต้องไม่เกิน ' . self::TEXT_MAX . ' ตัวอักษร',
            'result.required' => 'กรุณาระบุผลลัพธ์',
            'result.max' => 'ผลลัพธ์ต้องไม่เกิน ' . self::TEXT_MAX . ' ตัวอักษร',
            'risk_level.required' => 'กรุณาระบุระดับความเสี่ยง',
            'risk_level.in' => 'ระดับความเสี่ยงไม่ถูกต้อง',
            'risk_detail.max' => 'รายละเอียดความเสี่ยงต้องไม่เกิน ' . self::TEXT_MAX . ' ตัวอักษร',
            'status.required' => 'กรุณาเลือกสถานะการช่วยเหลือในรอบนี้',
            'status.in' => 'สถานะการช่วยเหลือไม่ถูกต้อง',
            'next_appointment_date.required' => 'กรุณาระบุวันนัดหมายครั้งต่อไป เมื่อสถานะอยู่ระหว่างดำเนินการ',
            'next_appointment_date.date' => 'วันนัดหมายครั้งต่อไปไม่ถูกต้อง',
            'next_appointment_date.after' => 'วันนัดหมายครั้งต่อไปต้องอยู่หลังวันที่ดำเนินการในรอบนี้',
            'followup_focus.required' => 'กรุณาระบุประเด็นที่จะดำเนินการต่อในรอบถัดไป',
            'followup_focus.max' => 'ประเด็นที่จะดำเนินการต่อในรอบถัดไปต้องไม่เกิน ' . self::TEXT_MAX . ' ตัวอักษร',
        ];
    }

    private function normalizeReferralRoundData(array $data): array
    {
        if (($data['risk_level'] ?? 'none') === 'none') {
            $data['risk_detail'] = null;
        }

        if (($data['status'] ?? 'ongoing') === 'goal_met') {
            $data['next_appointment_date'] = null;
            $data['followup_focus'] = null;
        }

        return $data;
    }

    private function observeValidationMessages(): array
    {
        return [
            'date.required'                 => 'กรุณาระบุวันที่เกิดเหตุ',
            'date.date'                     => 'วันที่เกิดเหตุไม่ถูกต้อง',
            'date.before_or_equal'          => 'วันที่เกิดเหตุต้องไม่เกินวันปัจจุบัน',
            'date.unique'                   => 'วันที่นี้ถูกบันทึกแล้วสำหรับผู้รับบริการรายนี้',
            'misbehavior_id.required'       => 'กรุณาเลือกสภาพปัญหา',
            'misbehavior_id.integer'        => 'สภาพปัญหาไม่ถูกต้อง',
            'misbehavior_id.exists'         => 'ไม่พบสภาพปัญหาที่เลือกในระบบ',
            'behavior.required'             => 'กรุณาระบุพฤติกรรมที่พบเห็น',
            'behavior.max'                  => 'พฤติกรรมที่พบเห็นต้องไม่เกิน ' . self::TEXT_MAX . ' ตัวอักษร',
            'cause.required'                => 'กรุณาระบุสาเหตุ',
            'cause.max'                     => 'สาเหตุต้องไม่เกิน ' . self::TEXT_MAX . ' ตัวอักษร',
            'solution.required'             => 'กรุณาระบุแนวทางแก้ไข',
            'solution.max'                  => 'แนวทางแก้ไขต้องไม่เกิน ' . self::TEXT_MAX . ' ตัวอักษร',
            'action.required'               => 'กรุณาระบุการดำเนินการ',
            'action.max'                    => 'การดำเนินการต้องไม่เกิน ' . self::TEXT_MAX . ' ตัวอักษร',
            'obstacles.max'                 => 'ปัญหา/อุปสรรคต้องไม่เกิน ' . self::TEXT_MAX . ' ตัวอักษร',
            'result.required'               => 'กรุณาระบุผลลัพธ์',
            'result.max'                    => 'ผลลัพธ์ต้องไม่เกิน ' . self::TEXT_MAX . ' ตัวอักษร',
            'record_date.required'          => 'กรุณาระบุวันที่บันทึก',
            'record_date.date'              => 'วันที่บันทึกไม่ถูกต้อง',
            'record_date.after_or_equal'    => 'วันที่บันทึกต้องไม่น้อยกว่าวันที่เกิดเหตุ',
            'record_date.before_or_equal'   => 'วันที่บันทึกต้องไม่เกินวันปัจจุบัน',
        ];
    }

    private function followupValidationMessages(?string $observeDate): array
    {
        return [
            'followup_date.required'        => 'กรุณาระบุวันที่ติดตาม',
            'followup_date.date'            => 'รูปแบบวันที่ติดตามไม่ถูกต้อง',
            'followup_date.after_or_equal'  => 'วันที่ติดตามต้องไม่น้อยกว่าวันที่เกิดเหตุ' . ($observeDate ? ' (' . $observeDate . ')' : ''),
            'followup_date.before_or_equal' => 'วันที่ติดตามต้องไม่เกินวันปัจจุบัน',
            'followup_date.unique'          => 'วันที่ติดตามนี้ถูกบันทึกแล้ว กรุณาเลือกวันอื่น',
            'followup_action.max'           => 'การดำเนินการต้องไม่เกิน ' . self::TEXT_MAX . ' ตัวอักษร',
            'followup_result.max'           => 'ผลลัพธ์ต้องไม่เกิน ' . self::TEXT_MAX . ' ตัวอักษร',
        ];
    }

    private function throwValidation(string $errorBag, array $messages): never
    {
        $exception = ValidationException::withMessages($messages);
        $exception->errorBag = $errorBag;

        throw $exception;
    }
}
