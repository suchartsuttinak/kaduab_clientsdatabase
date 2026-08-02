<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\Misbehavior;
use App\Models\Observe;
use App\Models\ObserveFollowup;
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

        $data = $request->validateWithBag('observeForm', [
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
        ], $this->observeValidationMessages());

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

            Observe::create(array_merge($data, [
                'client_id' => $client->id,
                'recorder'  => auth()->user()->name ?? null,
            ]));

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
        $observe = $this->authorizedObserveQuery()
            ->with(['followups' => $this->followupOrderCallback()])
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
        $observe = $this->authorizedObserveQuery()->findOrFail($id);
        $client = Client::forUser(auth()->user())->findOrFail($observe->client_id);

        $this->trimTextFields($request, [
            'behavior',
            'cause',
            'solution',
            'action',
            'obstacles',
            'result',
        ]);

        $today = now(self::TIMEZONE)->toDateString();

        $data = $request->validateWithBag('observeForm', [
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
        ], $this->observeValidationMessages());

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

            $lockedObserve->update(array_merge($data, [
                'client_id' => $lockedObserve->client_id,
                'recorder'  => auth()->user()->name ?? null,
            ]));

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
        $observe = $this->authorizedObserveQuery()->findOrFail($observeId);
        Client::forUser(auth()->user())->findOrFail($observe->client_id);

        $this->trimTextFields($request, ['followup_action', 'followup_result']);

        $bag = 'followupStore' . $observe->id;
        $today = now(self::TIMEZONE)->toDateString();

        $validator = Validator::make($request->all(), [
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
        ], $this->followupValidationMessages($observe->date));

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, $bag)
                ->withInput();
        }

        $data = $validator->validated();

        DB::transaction(function () use ($observe, $data, $bag): void {
            $lockedObserve = $this->authorizedObserveQuery()
                ->lockForUpdate()
                ->findOrFail($observe->id);

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

            ObserveFollowup::create([
                'observe_id'      => $lockedObserve->id,
                'followup_date'   => $data['followup_date'],
                'followup_count'  => $nextFollowupCount,
                'followup_action' => $data['followup_action'] ?? null,
                'followup_result' => $data['followup_result'] ?? null,
            ]);

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

        $observe->load(['followups' => $this->followupOrderCallback()]);
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

        Client::forUser(auth()->user())->findOrFail($observe->client_id);
        $this->trimTextFields($request, ['followup_action', 'followup_result']);

        $bag = 'followupUpdate' . $followup->id;
        $today = now(self::TIMEZONE)->toDateString();

        $validator = Validator::make($request->all(), [
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
        ], $this->followupValidationMessages($observe->date));

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, $bag)
                ->withInput();
        }

        $data = $validator->validated();

        DB::transaction(function () use ($followup, $observe, $data, $bag): void {
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

            $lockedFollowup->update([
                'followup_date'   => $data['followup_date'],
                'followup_action' => $data['followup_action'] ?? null,
                'followup_result' => $data['followup_result'] ?? null,
            ]);

            $this->syncCaseActivity($observe->client_id);
        });

        return redirect()
            ->route('observe.edit', $observe->id)
            ->with('success', 'อัปเดตการติดตามผลเรียบร้อย');
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

        return view('frontend.client.observe.observe_report', compact('observe', 'client'));
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
     * ข้อมูลที่ใช้ร่วมกันในหน้ารายการ/แก้ไข
     */
    private function pageData(Client $client): array
    {
        return [
            'client'       => $client,
            'client_id'    => $client->id,
            'misbehaviors' => Misbehavior::query()
                ->orderBy('misbehavior_name')
                ->get(),
            'observes' => Observe::query()
                ->with(['followups' => $this->followupOrderCallback()])
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
