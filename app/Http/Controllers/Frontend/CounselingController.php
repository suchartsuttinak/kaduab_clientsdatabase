<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\Counseling;
use App\Models\CounselingFollowup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CounselingController extends Controller
{
    private const CHANNELS = [
        'face_to_face',
        'phone',
        'online',
        'home_visit',
        'other',
    ];

    private const RISK_LEVELS = [
        'none',
        'low',
        'moderate',
        'high',
    ];

    private const OPEN_STATUSES = [
        'ongoing',
        'follow_up',
    ];

    private const CLOSED_STATUSES = [
        'goal_met',
        'referred',
        'closed',
    ];

    /*
    |--------------------------------------------------------------------------
    | หน้า Index: รายการ "ครั้งที่" ของผู้รับบริการ
    |--------------------------------------------------------------------------
    */
    public function index($client_id)
    {
        $client = $this->authorizedClient((int) $client_id);

        $counselings = Counseling::with([
                'followups',
                'counselor',
            ])
            ->where('client_id', $client->id)
            ->orderByDesc('session_no')
            ->orderByDesc('session_date')
            ->get();

        return view(
            'frontend.client.counseling.index',
            compact('client', 'client_id', 'counselings')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | เริ่มการให้คำปรึกษาครั้งใหม่ = รอบที่ 1
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $data = $request->validate(
            $this->initialRules(true, true),
            $this->initialMessages()
        );

        $this->validateRiskDetail($data);
        $data = $this->normalizeInitialData($data);

        $counseling = DB::transaction(function () use ($data) {
            $client = Client::forUser(auth()->user())
                ->whereKey($data['client_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $open = Counseling::where('client_id', $client->id)
                ->whereIn('status', array_merge(self::OPEN_STATUSES, ['improved']))
                ->orderByDesc('session_no')
                ->lockForUpdate()
                ->first();

            if ($open) {
                throw ValidationException::withMessages([
                    'client_id' => 'ยังมีการให้คำปรึกษาครั้งที่ '
                        . $open->session_no
                        . ' ที่ยังไม่สิ้นสุด กรุณาดำเนินการครั้งเดิมให้เสร็จก่อนเริ่มครั้งใหม่',
                ]);
            }

            $nextSessionNo = ((int) Counseling::where(
                'client_id',
                $client->id
            )->max('session_no')) + 1;

            return Counseling::create([
                'client_id'             => $client->id,
                'session_no'            => $nextSessionNo,
                'session_date'          => $data['session_date'],
                'counselor_user_id'     => auth()->id(),
                'counselor_name'        => auth()->user()->name ?? null,
                'channel'               => $data['channel'],
                'location'              => $data['location'] ?? null,
                'presenting_problem'    => $data['presenting_problem'],
                'assessment'            => $data['assessment'],
                'strengths_resources'   => $data['strengths_resources'] ?? null,
                'goals'                 => $data['goals'] ?? null,
                'interventions'         => $data['interventions'] ?? null,
                'advice'                => $data['advice'] ?? null,
                'agreement'             => $data['agreement'] ?? null,
                'outcome'               => $data['outcome'] ?? null,
                'next_steps'            => $data['next_steps'] ?? null,
                'risk_level'            => $data['risk_level'],
                'risk_detail'           => $data['risk_detail'] ?? null,
                'needs_followup'        => $this->isOpenStatus($data['status']),
                'next_appointment_date' => $data['next_appointment_date'] ?? null,
                'followup_focus'        => $data['followup_focus'] ?? null,
                'status'                => $data['status'],
                'created_by'            => auth()->id(),
                'updated_by'            => auth()->id(),
            ]);
        }, 3);

        CaseActivity::record([
            'client_id'   => $counseling->client_id,
            'module'      => 'counseling',
            'type'        => 'info',
            'title'       => 'บันทึกการให้คำปรึกษา',
            'description' => 'การให้คำปรึกษาครั้งที่ '
                . $counseling->session_no
                . ' | รอบที่ 1 | วันที่ '
                . $counseling->session_date->format('Y-m-d'),
            'occurred_at' => now(),
            'icon'        => 'bi-chat-heart',
            'url'         => route('counseling.show', $counseling->id),
        ]);

        return redirect()
            ->route('counseling.show', $counseling->id)
            ->with('success', 'บันทึกการให้คำปรึกษา รอบที่ 1 เรียบร้อย');
    }

    /*
    |--------------------------------------------------------------------------
    | หน้าสรุปการให้คำปรึกษา "ครั้งที่" หนึ่ง
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $counseling = $this->authorizedCounseling(
            (int) $id,
            [
                'client',
                'counselor',
                'followups',
            ]
        );

        $client = $this->authorizedClient((int) $counseling->client_id);

        return view(
            'frontend.client.counseling.show',
            compact('client', 'counseling')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | แก้ไขรอบที่ 1
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $counseling = $this->authorizedCounseling(
            (int) $id,
            ['client', 'followups']
        );

        $client = $this->authorizedClient((int) $counseling->client_id);

        return view(
            'frontend.client.counseling.initial_edit',
            compact('client', 'counseling')
        );
    }

    public function update(Request $request, $id)
    {
        $counseling = $this->authorizedCounseling(
            (int) $id,
            ['followups']
        );

        $hasRoundsAfterFirst = $counseling->followups->isNotEmpty();

        $data = $request->validate(
            $this->initialRules(false, !$hasRoundsAfterFirst),
            $this->initialMessages()
        );

        $this->validateRiskDetail($data);

        if (!$hasRoundsAfterFirst) {
            $data = $this->normalizeInitialData($data);
        }

        DB::transaction(function () use (
            $counseling,
            $data,
            $hasRoundsAfterFirst
        ) {
            Client::forUser(auth()->user())
                ->whereKey($counseling->client_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($hasRoundsAfterFirst) {
                $firstRoundAfter = $counseling->followups
                    ->sortBy('followup_no')
                    ->first();

                if ($firstRoundAfter) {
                    $sessionDate = Carbon::parse($data['session_date'])->startOfDay();
                    $nextDate = Carbon::parse($firstRoundAfter->followup_date)->startOfDay();

                    if ($sessionDate->gte($nextDate)) {
                        throw ValidationException::withMessages([
                            'session_date' => 'วันที่รอบที่ 1 ต้องอยู่ก่อนวันที่รอบที่ 2',
                        ]);
                    }
                }
            }

            if (
                !$hasRoundsAfterFirst
                && $this->isClosedStatus($counseling->status)
                && isset($data['status'])
                && $this->isOpenStatus($data['status'])
            ) {
                $hasLaterSession = Counseling::where(
                        'client_id',
                        $counseling->client_id
                    )
                    ->where('session_no', '>', $counseling->session_no)
                    ->exists();

                if ($hasLaterSession) {
                    throw ValidationException::withMessages([
                        'status' => 'ไม่สามารถเปิดการให้คำปรึกษาครั้งนี้กลับมาเป็นต่อเนื่องได้ เนื่องจากมีครั้งถัดไปแล้ว',
                    ]);
                }
            }

            $update = [
                'session_date'        => $data['session_date'],
                'channel'             => $data['channel'],
                'location'            => $data['location'] ?? null,
                'presenting_problem'  => $data['presenting_problem'],
                'assessment'          => $data['assessment'],
                'strengths_resources' => $data['strengths_resources'] ?? null,
                'goals'               => $data['goals'] ?? null,
                'interventions'       => $data['interventions'] ?? null,
                'advice'              => $data['advice'] ?? null,
                'agreement'           => $data['agreement'] ?? null,
                'outcome'             => $data['outcome'] ?? null,
                'next_steps'          => $data['next_steps'] ?? null,
                'risk_level'          => $data['risk_level'],
                'risk_detail'         => $data['risk_detail'] ?? null,
                'updated_by'          => auth()->id(),
            ];

            if (!$hasRoundsAfterFirst) {
                $update['status'] = $data['status'];
                $update['needs_followup'] = $this->isOpenStatus($data['status']);
                $update['next_appointment_date'] = $data['next_appointment_date'] ?? null;
                $update['followup_focus'] = $data['followup_focus'] ?? null;
            }

            $counseling->update($update);
        }, 3);

        return redirect()
            ->route('counseling.show', $counseling->id)
            ->with('success', 'แก้ไขข้อมูลรอบที่ 1 เรียบร้อย');
    }

    public function destroy($id)
    {
        $counseling = $this->authorizedCounseling((int) $id);
        $clientId = $counseling->client_id;

        DB::transaction(function () use ($counseling) {
            Client::forUser(auth()->user())
                ->whereKey($counseling->client_id)
                ->lockForUpdate()
                ->firstOrFail();

            $counseling->delete();
        }, 3);

        return redirect()
            ->route('counseling.index', $clientId)
            ->with('success', 'ลบข้อมูลการให้คำปรึกษาเรียบร้อย');
    }

    /*
    |--------------------------------------------------------------------------
    | รอบที่ 2, 3, 4 ... แยกเป็นหน้าเต็ม
    |--------------------------------------------------------------------------
    */
    public function createRound($id)
    {
        $counseling = $this->authorizedCounseling(
            (int) $id,
            ['client', 'followups']
        );

        if ($this->isClosedStatus($counseling->status)) {
            return redirect()
                ->route('counseling.show', $counseling->id)
                ->with('error', 'การให้คำปรึกษาครั้งนี้สิ้นสุดแล้ว ไม่สามารถเพิ่มรอบใหม่ได้');
        }

        $client = $this->authorizedClient((int) $counseling->client_id);
        $nextRoundNo = 2 + (int) $counseling->followups->max('followup_no');
        $previousRound = $this->buildPreviousRoundSummary($counseling);

        return view(
            'frontend.client.counseling.round_create',
            compact(
                'client',
                'counseling',
                'nextRoundNo',
                'previousRound'
            )
        );
    }

    public function storeRound(Request $request)
    {
        $data = $request->validate(
            $this->roundRules(true),
            $this->roundMessages()
        );

        $this->validateRiskDetail($data);
        $data = $this->normalizeRoundData($data);

        $followup = DB::transaction(function () use ($data) {
            $counseling = Counseling::whereHas(
                    'client',
                    fn ($query) => $query->forUser(auth()->user())
                )
                ->whereKey($data['counseling_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($this->isClosedStatus($counseling->status)) {
                throw ValidationException::withMessages([
                    'counseling_id' => 'การให้คำปรึกษาครั้งนี้สิ้นสุดแล้ว ไม่สามารถเพิ่มรอบใหม่ได้',
                ]);
            }

            $this->validateNewRoundDate(
                $counseling,
                $data['followup_date']
            );

            $nextFollowupNo = ((int) CounselingFollowup::where(
                'counseling_id',
                $counseling->id
            )->max('followup_no')) + 1;

            $followup = CounselingFollowup::create([
                'counseling_id'         => $counseling->id,
                'followup_no'           => $nextFollowupNo,
                'followup_date'         => $data['followup_date'],
                'followup_method'       => $data['followup_method'],
                'location'              => $data['location'] ?? null,
                'topic'                 => $data['topic'],
                'progress'              => $data['progress'],
                'changes'               => $data['changes'] ?? null,
                'barriers'              => $data['barriers'] ?? null,
                'current_assessment'    => $data['current_assessment'],
                'session_goal'          => $data['session_goal'] ?? null,
                'interventions'         => $data['interventions'] ?? null,
                'advice'                => $data['advice'] ?? null,
                'agreement'             => $data['agreement'] ?? null,
                'additional_support'    => $data['additional_support'] ?? null,
                'result'                => $data['result'],
                'risk_level'            => $data['risk_level'],
                'risk_detail'           => $data['risk_detail'] ?? null,
                'next_action'           => $data['next_action'] ?? null,
                'next_appointment_date' => $data['next_appointment_date'] ?? null,
                'status'                => $data['status'],
                'recorder_user_id'      => auth()->id(),
                'recorder_name'         => auth()->user()->name ?? null,
                'updated_by'            => auth()->id(),
            ]);

            $this->syncCounselingFromLatestRound($counseling);

            return $followup;
        }, 3);

        $roundNo = $followup->followup_no + 1;

        CaseActivity::record([
            'client_id'   => $followup->counseling->client_id,
            'module'      => 'counseling',
            'type'        => 'info',
            'title'       => 'บันทึกการให้คำปรึกษาต่อเนื่อง',
            'description' => 'การให้คำปรึกษาครั้งที่ '
                . $followup->counseling->session_no
                . ' | รอบที่ '
                . $roundNo
                . ' | วันที่ '
                . $followup->followup_date->format('Y-m-d'),
            'occurred_at' => now(),
            'icon'        => 'bi-chat-dots',
            'url'         => route('counseling.show', $followup->counseling_id),
        ]);

        return redirect()
            ->route('counseling.show', $followup->counseling_id)
            ->with('success', 'บันทึกการให้คำปรึกษา รอบที่ ' . $roundNo . ' เรียบร้อย');
    }

    public function editRound($id)
    {
        $round = $this->authorizedRound((int) $id);
        $counseling = $this->authorizedCounseling(
            (int) $round->counseling_id,
            ['client', 'followups']
        );
        $client = $this->authorizedClient((int) $counseling->client_id);

        $roundNo = $round->followup_no + 1;
        $previousRound = $this->buildPreviousRoundSummary(
            $counseling,
            $round->followup_no
        );
        $isLatestRound = !$counseling->followups
            ->contains(fn ($item) => $item->followup_no > $round->followup_no);

        return view(
            'frontend.client.counseling.round_edit',
            compact(
                'client',
                'counseling',
                'round',
                'roundNo',
                'previousRound',
                'isLatestRound'
            )
        );
    }

    public function updateRound(Request $request, $id)
    {
        $round = $this->authorizedRound((int) $id);

        $data = $request->validate(
            $this->roundRules(false),
            $this->roundMessages()
        );

        $this->validateRiskDetail($data);
        $data = $this->normalizeRoundData($data);

        DB::transaction(function () use ($round, $data) {
            $lockedRound = CounselingFollowup::whereHas(
                    'counseling.client',
                    fn ($query) => $query->forUser(auth()->user())
                )
                ->whereKey($round->id)
                ->lockForUpdate()
                ->firstOrFail();

            $counseling = Counseling::whereKey($lockedRound->counseling_id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->validateEditedRoundDate(
                $counseling,
                $lockedRound,
                $data['followup_date']
            );

            $hasLaterRound = CounselingFollowup::where(
                    'counseling_id',
                    $counseling->id
                )
                ->where('followup_no', '>', $lockedRound->followup_no)
                ->exists();

            if ($hasLaterRound && $this->isClosedStatus($data['status'])) {
                throw ValidationException::withMessages([
                    'status' => 'รอบนี้มีรอบถัดไปแล้ว จึงไม่สามารถกำหนดให้เป็นสถานะสิ้นสุดได้',
                ]);
            }

            if (
                !$hasLaterRound
                && $this->isClosedStatus($counseling->status)
                && $this->isOpenStatus($data['status'])
            ) {
                $hasLaterSession = Counseling::where(
                        'client_id',
                        $counseling->client_id
                    )
                    ->where('session_no', '>', $counseling->session_no)
                    ->exists();

                if ($hasLaterSession) {
                    throw ValidationException::withMessages([
                        'status' => 'ไม่สามารถเปิดการให้คำปรึกษาครั้งนี้กลับมาเป็นต่อเนื่องได้ เนื่องจากมีการให้คำปรึกษาครั้งถัดไปแล้ว',
                    ]);
                }
            }

            $lockedRound->update([
                'followup_date'         => $data['followup_date'],
                'followup_method'       => $data['followup_method'],
                'location'              => $data['location'] ?? null,
                'topic'                 => $data['topic'],
                'progress'              => $data['progress'],
                'changes'               => $data['changes'] ?? null,
                'barriers'              => $data['barriers'] ?? null,
                'current_assessment'    => $data['current_assessment'],
                'session_goal'          => $data['session_goal'] ?? null,
                'interventions'         => $data['interventions'] ?? null,
                'advice'                => $data['advice'] ?? null,
                'agreement'             => $data['agreement'] ?? null,
                'additional_support'    => $data['additional_support'] ?? null,
                'result'                => $data['result'],
                'risk_level'            => $data['risk_level'],
                'risk_detail'           => $data['risk_detail'] ?? null,
                'next_action'           => $data['next_action'] ?? null,
                'next_appointment_date' => $data['next_appointment_date'] ?? null,
                'status'                => $data['status'],
                'updated_by'            => auth()->id(),
            ]);

            $this->syncCounselingFromLatestRound($counseling);
        }, 3);

        return redirect()
            ->route('counseling.show', $round->counseling_id)
            ->with('success', 'แก้ไขข้อมูลรอบที่ ' . ($round->followup_no + 1) . ' เรียบร้อย');
    }

    public function destroyRound($id)
    {
        $round = $this->authorizedRound((int) $id);
        $counselingId = $round->counseling_id;
        $roundNo = $round->followup_no + 1;

        DB::transaction(function () use ($round) {
            $lockedRound = CounselingFollowup::whereHas(
                    'counseling.client',
                    fn ($query) => $query->forUser(auth()->user())
                )
                ->whereKey($round->id)
                ->lockForUpdate()
                ->firstOrFail();

            $counseling = Counseling::whereKey($lockedRound->counseling_id)
                ->lockForUpdate()
                ->firstOrFail();

            $hasLaterRound = CounselingFollowup::where(
                    'counseling_id',
                    $counseling->id
                )
                ->where('followup_no', '>', $lockedRound->followup_no)
                ->exists();

            if ($hasLaterRound) {
                throw ValidationException::withMessages([
                    'round' => 'ไม่สามารถลบรอบนี้ได้ เนื่องจากมีรอบถัดไปแล้ว กรุณาลบจากรอบล่าสุดย้อนกลับ',
                ]);
            }

            $lockedRound->delete();
            $this->syncCounselingFromLatestRound($counseling, true);
        }, 3);

        return redirect()
            ->route('counseling.show', $counselingId)
            ->with('success', 'ลบรอบที่ ' . $roundNo . ' เรียบร้อย');
    }

    /*
    |--------------------------------------------------------------------------
    | รายงานรายรอบ
    |--------------------------------------------------------------------------
    */
    public function roundReport($id, $roundNo)
    {
        $counseling = $this->authorizedCounseling(
            (int) $id,
            ['client', 'followups']
        );

        $client = $this->authorizedClient((int) $counseling->client_id);
        $roundNo = (int) $roundNo;

        if ($roundNo < 1) {
            abort(404);
        }

        if ($roundNo === 1) {
            $round = null;
        } else {
            $round = $counseling->followups
                ->firstWhere('followup_no', $roundNo - 1);

            abort_if(!$round, 404);
        }

        return view(
            'frontend.client.counseling.round_report',
            compact('client', 'counseling', 'round', 'roundNo')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | รายงานรวมทั้ง "ครั้งที่" เรียงตามรอบ
    |--------------------------------------------------------------------------
    */
    public function report($id)
    {
        $counseling = $this->authorizedCounseling(
            (int) $id,
            [
                'client',
                'counselor',
                'followups' => fn ($query) => $query
                    ->orderBy('followup_no')
                    ->orderBy('followup_date'),
            ]
        );

        $client = $this->authorizedClient((int) $counseling->client_id);

        return view(
            'frontend.client.counseling.report',
            compact('client', 'counseling')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validation - รอบที่ 1
    |--------------------------------------------------------------------------
    */
    private function initialRules(
        bool $includeClient = true,
        bool $includeStatus = true
    ): array {
        $today = now('Asia/Bangkok')->toDateString();

        $rules = [
            'session_date' => ['required', 'date', 'before_or_equal:' . $today],
            'channel' => ['required', Rule::in(self::CHANNELS)],
            'location' => ['nullable', 'string', 'max:255'],
            'presenting_problem' => ['required', 'string', 'max:10000'],
            'assessment' => ['required', 'string', 'max:10000'],
            'strengths_resources' => ['nullable', 'string', 'max:10000'],
            'goals' => ['nullable', 'string', 'max:10000'],
            'interventions' => ['nullable', 'string', 'max:10000'],
            'advice' => ['nullable', 'string', 'max:10000'],
            'agreement' => ['nullable', 'string', 'max:10000'],
            'outcome' => ['nullable', 'string', 'max:10000'],
            'next_steps' => ['nullable', 'string', 'max:10000'],
            'risk_level' => ['required', Rule::in(self::RISK_LEVELS)],
            'risk_detail' => ['nullable', 'string', 'max:10000'],
        ];

        if ($includeClient) {
            $rules['client_id'] = ['required', 'integer'];
        }

        if ($includeStatus) {
            $rules['status'] = [
                'required',
                Rule::in(array_merge(self::OPEN_STATUSES, self::CLOSED_STATUSES)),
            ];
            $rules['next_appointment_date'] = [
                Rule::requiredIf(fn () => in_array(
                    request('status'),
                    self::OPEN_STATUSES,
                    true
                )),
                'nullable',
                'date',
                'after:session_date',
            ];
            $rules['followup_focus'] = [
                Rule::requiredIf(fn () => in_array(
                    request('status'),
                    self::OPEN_STATUSES,
                    true
                )),
                'nullable',
                'string',
                'max:10000',
            ];
        }

        return $rules;
    }

    private function initialMessages(): array
    {
        return [
            'client_id.required' => 'ไม่พบข้อมูลผู้รับบริการ',
            'session_date.required' => 'กรุณาระบุวันที่ให้คำปรึกษา',
            'session_date.before_or_equal' => 'วันที่ให้คำปรึกษาต้องไม่เกินวันนี้',
            'channel.required' => 'กรุณาเลือกช่องทางการให้คำปรึกษา',
            'presenting_problem.required' => 'กรุณาระบุประเด็นหรือสาเหตุที่มารับคำปรึกษา',
            'assessment.required' => 'กรุณาระบุการประเมินสภาพปัญหาเบื้องต้น',
            'risk_level.required' => 'กรุณาระบุระดับความเสี่ยง',
            'status.required' => 'กรุณาเลือกสถานะหลังการให้คำปรึกษา',
            'next_appointment_date.required' => 'กรุณาระบุวันนัดหมายครั้งต่อไป',
            'next_appointment_date.after' => 'วันนัดหมายต้องอยู่หลังวันที่ให้คำปรึกษา',
            'followup_focus.required' => 'กรุณาระบุประเด็นที่จะดำเนินการต่อในรอบถัดไป',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Validation - รอบที่ 2+
    |--------------------------------------------------------------------------
    */
    private function roundRules(bool $includeCounseling = true): array
    {
        $today = now('Asia/Bangkok')->toDateString();

        $rules = [
            'followup_date' => ['required', 'date', 'before_or_equal:' . $today],
            'followup_method' => ['required', Rule::in(self::CHANNELS)],
            'location' => ['nullable', 'string', 'max:255'],
            'topic' => ['required', 'string', 'max:10000'],
            'progress' => ['required', 'string', 'max:10000'],
            'current_assessment' => ['required', 'string', 'max:10000'],
            'changes' => ['nullable', 'string', 'max:10000'],
            'barriers' => ['nullable', 'string', 'max:10000'],
            'session_goal' => ['nullable', 'string', 'max:10000'],
            'interventions' => ['nullable', 'string', 'max:10000'],
            'advice' => ['nullable', 'string', 'max:10000'],
            'agreement' => ['nullable', 'string', 'max:10000'],
            'additional_support' => ['nullable', 'string', 'max:10000'],
            'result' => ['required', 'string', 'max:10000'],
            'risk_level' => ['required', Rule::in(self::RISK_LEVELS)],
            'risk_detail' => ['nullable', 'string', 'max:10000'],
            'next_action' => ['nullable', 'string', 'max:10000'],
            'status' => [
                'required',
                Rule::in(array_merge(self::OPEN_STATUSES, self::CLOSED_STATUSES)),
            ],
            'next_appointment_date' => [
                Rule::requiredIf(fn () => in_array(
                    request('status'),
                    self::OPEN_STATUSES,
                    true
                )),
                'nullable',
                'date',
                'after:followup_date',
            ],
        ];

        if ($includeCounseling) {
            $rules['counseling_id'] = ['required', 'integer'];
        }

        return $rules;
    }

    private function roundMessages(): array
    {
        return [
            'counseling_id.required' => 'ไม่พบข้อมูลการให้คำปรึกษา',
            'followup_date.required' => 'กรุณาระบุวันที่ให้คำปรึกษา',
            'followup_date.before_or_equal' => 'วันที่ให้คำปรึกษาต้องไม่เกินวันนี้',
            'followup_method.required' => 'กรุณาเลือกช่องทางการให้คำปรึกษา',
            'topic.required' => 'กรุณาระบุหัวข้อหรือประเด็นที่ดำเนินการในรอบนี้',
            'progress.required' => 'กรุณาระบุสรุปความคืบหน้าของรอบนี้',
            'current_assessment.required' => 'กรุณาระบุสภาพปัจจุบันหรือการประเมินในรอบนี้',
            'result.required' => 'กรุณาระบุผลที่เกิดขึ้นจากการให้คำปรึกษาในรอบนี้',
            'risk_level.required' => 'กรุณาระบุระดับความเสี่ยง',
            'status.required' => 'กรุณาเลือกสถานะหลังการให้คำปรึกษาในรอบนี้',
            'next_appointment_date.required' => 'กรุณาระบุวันนัดหมายครั้งต่อไป',
            'next_appointment_date.after' => 'วันนัดหมายต้องอยู่หลังวันที่ของรอบนี้',
        ];
    }

    private function normalizeInitialData(array $data): array
    {
        if (($data['risk_level'] ?? 'none') === 'none') {
            $data['risk_detail'] = null;
        }

        if ($this->isClosedStatus($data['status'])) {
            $data['next_appointment_date'] = null;
            $data['followup_focus'] = null;
        }

        return $data;
    }

    private function normalizeRoundData(array $data): array
    {
        if (($data['risk_level'] ?? 'none') === 'none') {
            $data['risk_detail'] = null;
        }

        if ($this->isClosedStatus($data['status'])) {
            $data['next_appointment_date'] = null;
            $data['next_action'] = $data['next_action'] ?? null;
        }

        return $data;
    }

    private function validateRiskDetail(array $data): void
    {
        if (
            in_array($data['risk_level'] ?? null, ['moderate', 'high'], true)
            && blank($data['risk_detail'] ?? null)
        ) {
            throw ValidationException::withMessages([
                'risk_detail' => 'กรุณาระบุรายละเอียดความเสี่ยง เมื่อประเมินว่ามีความเสี่ยงระดับปานกลางหรือสูง',
            ]);
        }
    }

    private function syncCounselingFromLatestRound(
        Counseling $counseling,
        bool $afterDelete = false
    ): void {
        $latest = CounselingFollowup::where(
                'counseling_id',
                $counseling->id
            )
            ->orderByDesc('followup_no')
            ->first();

        if (!$latest) {
            if ($afterDelete) {
                $counseling->update([
                    'status'                => 'follow_up',
                    'needs_followup'        => true,
                    'next_appointment_date' => null,
                    'followup_focus'        => null,
                    'updated_by'            => auth()->id(),
                ]);
            }

            return;
        }

        $isOpen = $this->isOpenStatus($latest->status);

        $counseling->update([
            'status'                => $latest->status,
            'needs_followup'        => $isOpen,
            'next_appointment_date' => $isOpen
                ? $latest->next_appointment_date
                : null,
            'followup_focus'        => $isOpen
                ? $latest->next_action
                : null,
            'updated_by'            => auth()->id(),
        ]);
    }

    private function validateNewRoundDate(
        Counseling $counseling,
        string $date
    ): void {
        $newDate = Carbon::parse($date)->startOfDay();
        $sessionDate = Carbon::parse($counseling->session_date)->startOfDay();

        if ($newDate->lte($sessionDate)) {
            throw ValidationException::withMessages([
                'followup_date' => 'วันที่รอบใหม่ต้องอยู่หลังวันที่รอบที่ 1',
            ]);
        }

        $last = CounselingFollowup::where(
                'counseling_id',
                $counseling->id
            )
            ->orderByDesc('followup_no')
            ->first();

        if ($last) {
            $lastDate = Carbon::parse($last->followup_date)->startOfDay();

            if ($newDate->lte($lastDate)) {
                throw ValidationException::withMessages([
                    'followup_date' => 'วันที่รอบใหม่ต้องอยู่หลังวันที่รอบที่ '
                        . ($last->followup_no + 1)
                        . ' และห้ามใช้วันที่ซ้ำ',
                ]);
            }
        }
    }

    private function validateEditedRoundDate(
        Counseling $counseling,
        CounselingFollowup $round,
        string $date
    ): void {
        $newDate = Carbon::parse($date)->startOfDay();
        $sessionDate = Carbon::parse($counseling->session_date)->startOfDay();

        if ($newDate->lte($sessionDate)) {
            throw ValidationException::withMessages([
                'followup_date' => 'วันที่รอบนี้ต้องอยู่หลังวันที่รอบที่ 1',
            ]);
        }

        $previous = CounselingFollowup::where(
                'counseling_id',
                $counseling->id
            )
            ->where('followup_no', '<', $round->followup_no)
            ->orderByDesc('followup_no')
            ->first();

        if ($previous) {
            $previousDate = Carbon::parse($previous->followup_date)->startOfDay();

            if ($newDate->lte($previousDate)) {
                throw ValidationException::withMessages([
                    'followup_date' => 'วันที่รอบที่ '
                        . ($round->followup_no + 1)
                        . ' ต้องอยู่หลังรอบที่ '
                        . ($previous->followup_no + 1),
                ]);
            }
        }

        $next = CounselingFollowup::where(
                'counseling_id',
                $counseling->id
            )
            ->where('followup_no', '>', $round->followup_no)
            ->orderBy('followup_no')
            ->first();

        if ($next) {
            $nextDate = Carbon::parse($next->followup_date)->startOfDay();

            if ($newDate->gte($nextDate)) {
                throw ValidationException::withMessages([
                    'followup_date' => 'วันที่รอบที่ '
                        . ($round->followup_no + 1)
                        . ' ต้องอยู่ก่อนรอบที่ '
                        . ($next->followup_no + 1),
                ]);
            }
        }
    }

    private function buildPreviousRoundSummary(
        Counseling $counseling,
        ?int $beforeFollowupNo = null
    ): array {
        if ($beforeFollowupNo !== null) {
            $previousFollowup = $counseling->followups
                ->where('followup_no', '<', $beforeFollowupNo)
                ->sortByDesc('followup_no')
                ->first();

            if ($previousFollowup) {
                return [
                    'round_no'    => $previousFollowup->followup_no + 1,
                    'date'        => $previousFollowup->followup_date,
                    'topic'       => $previousFollowup->topic ?: '-',
                    'result'      => $previousFollowup->result ?: '-',
                    'next_action' => $previousFollowup->next_action ?: '-',
                ];
            }
        } else {
            $latestFollowup = $counseling->followups
                ->sortByDesc('followup_no')
                ->first();

            if ($latestFollowup) {
                return [
                    'round_no'    => $latestFollowup->followup_no + 1,
                    'date'        => $latestFollowup->followup_date,
                    'topic'       => $latestFollowup->topic ?: '-',
                    'result'      => $latestFollowup->result ?: '-',
                    'next_action' => $latestFollowup->next_action ?: '-',
                ];
            }
        }

        return [
            'round_no'    => 1,
            'date'        => $counseling->session_date,
            'topic'       => $counseling->presenting_problem ?: '-',
            'result'      => $counseling->outcome ?: '-',
            'next_action' => $counseling->next_steps ?: $counseling->followup_focus ?: '-',
        ];
    }

    private function isOpenStatus(?string $status): bool
    {
        return in_array($status, self::OPEN_STATUSES, true)
            || $status === 'improved';
    }

    private function isClosedStatus(?string $status): bool
    {
        return in_array($status, self::CLOSED_STATUSES, true);
    }

    private function authorizedClient(int $id): Client
    {
        return Client::forUser(auth()->user())->findOrFail($id);
    }

    private function authorizedCounseling(
        int $id,
        array $with = []
    ): Counseling {
        $query = Counseling::query()
            ->whereHas('client', fn ($query) => $query->forUser(auth()->user()));

        if ($with) {
            $query->with($with);
        }

        return $query->findOrFail($id);
    }

    private function authorizedRound(int $id): CounselingFollowup
    {
        return CounselingFollowup::query()
            ->whereHas(
                'counseling.client',
                fn ($query) => $query->forUser(auth()->user())
            )
            ->with('counseling')
            ->findOrFail($id);
    }
}
