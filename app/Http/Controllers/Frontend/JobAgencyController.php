<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\JobAgency;
use App\Models\Occupation;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class JobAgencyController extends Controller
{
    /**
     * แสดงข้อมูลการจัดหางานของผู้รับบริการ
     */
    public function showJobAgency(Request $request, $client_id): View|RedirectResponse
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $filterValidator = $this->filterValidator($request);

        if ($filterValidator->fails()) {
            return redirect()
                ->route('job_agencies.show', $client->id)
                ->withErrors($filterValidator, 'filters')
                ->withInput();
        }

        $filters = $filterValidator->validated();

        $hasAnyJobAgency = JobAgency::query()
            ->where('client_id', $client->id)
            ->exists();

        $jobAgencies = JobAgency::query()
            ->with('occupation:id,occupation_name')
            ->where('client_id', $client->id)
            ->when(
                !empty($filters['start_date']),
                fn ($query) => $query->where('job_date', '>=', $filters['start_date'])
            )
            ->when(
                !empty($filters['end_date']),
                fn ($query) => $query->where('job_date', '<=', $filters['end_date'])
            )
            ->orderByDesc('job_date')
            ->orderByDesc('id')
            ->get();

        $occupations = Occupation::query()
            ->orderBy('occupation_name')
            ->get(['id', 'occupation_name']);

        return view('frontend.client.job_agency.job_agency', [
            'client'           => $client,
            'client_id'        => $client->id,
            'jobAgencies'      => $jobAgencies,
            'occupations'      => $occupations,
            'hasAnyJobAgency'  => $hasAnyJobAgency,
        ]);
    }

    /**
     * บันทึกข้อมูลการจัดหางาน
     */
    public function storeJobAgency(Request $request): RedirectResponse
    {
        $this->normalizeInput($request);

        $clientValidator = Validator::make($request->only('client_id'), [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
        ], [
            'client_id.required' => 'ไม่พบข้อมูลผู้รับบริการ',
            'client_id.integer'  => 'ข้อมูลผู้รับบริการไม่ถูกต้อง',
            'client_id.exists'   => 'ไม่พบผู้รับบริการที่เลือก',
        ]);

        if ($clientValidator->fails()) {
            return back()->withErrors($clientValidator)->withInput();
        }

        $client = Client::forUser(auth()->user())
            ->findOrFail((int) $request->input('client_id'));

        $validated = Validator::make(
            $request->all(),
            $this->rules($client->id),
            $this->messages()
        )->validate();

        $validated['client_id'] = $client->id;
        unset($validated['job_id']);

        try {
            $jobAgency = DB::transaction(function () use ($client, $validated) {
                Client::forUser(auth()->user())
                    ->whereKey($client->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $duplicate = JobAgency::query()
                    ->where('client_id', $client->id)
                    ->where('job_date', $validated['job_date'])
                    ->exists();

                if ($duplicate) {
                    throw ValidationException::withMessages([
                        'job_date' => 'วันที่เริ่มงานนี้มีอยู่แล้ว กรุณาเลือกวันที่อื่น',
                    ]);
                }

                $jobAgency = JobAgency::create($validated);

                $this->syncCaseActivity(
                    $client->id,
                    $jobAgency,
                    'บันทึกการจัดหางาน'
                );

                return $jobAgency;
            }, 3);
        } catch (QueryException $exception) {
            if ($this->isIntegrityConstraintViolation($exception)) {
                return back()
                    ->withErrors(['job_date' => 'วันที่เริ่มงานนี้มีอยู่แล้ว กรุณาเลือกวันที่อื่น'])
                    ->withInput();
            }

            throw $exception;
        }

        return redirect()
            ->route('job_agencies.show', $jobAgency->client_id)
            ->with('success', 'บันทึกข้อมูลการจัดหางานเรียบร้อยแล้ว');
    }

    /**
     * อัปเดตข้อมูลการจัดหางาน
     */
    public function updateJobAgency(Request $request, $id): RedirectResponse
    {
        $jobAgency = JobAgency::query()
            ->whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->findOrFail($id);

        $client = Client::forUser(auth()->user())
            ->findOrFail($jobAgency->client_id);

        $this->normalizeInput($request);
        $request->merge(['job_id' => $jobAgency->id]);

        $validated = Validator::make(
            $request->all(),
            $this->rules($client->id, $jobAgency->id),
            $this->messages()
        )->validate();

        $validated['client_id'] = $client->id;
        unset($validated['job_id']);

        try {
            DB::transaction(function () use ($jobAgency, $client, $validated) {
                Client::forUser(auth()->user())
                    ->whereKey($client->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $lockedJobAgency = JobAgency::query()
                    ->whereKey($jobAgency->id)
                    ->where('client_id', $client->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $duplicate = JobAgency::query()
                    ->where('client_id', $client->id)
                    ->where('job_date', $validated['job_date'])
                    ->where('id', '!=', $lockedJobAgency->id)
                    ->exists();

                if ($duplicate) {
                    throw ValidationException::withMessages([
                        'job_date' => 'วันที่เริ่มงานนี้มีอยู่แล้ว กรุณาเลือกวันที่อื่น',
                    ]);
                }

                $lockedJobAgency->update($validated);

                $this->syncCaseActivity(
                    $client->id,
                    $lockedJobAgency->fresh(),
                    'แก้ไขการจัดหางาน'
                );
            }, 3);
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput($request->all());
        } catch (QueryException $exception) {
            if ($this->isIntegrityConstraintViolation($exception)) {
                return back()
                    ->withErrors(['job_date' => 'วันที่เริ่มงานนี้มีอยู่แล้ว กรุณาเลือกวันที่อื่น'])
                    ->withInput($request->all());
            }

            throw $exception;
        }

        return redirect()
            ->route('job_agencies.show', $client->id)
            ->with('success', 'แก้ไขข้อมูลการจัดหางานเรียบร้อยแล้ว');
    }

    /**
     * ลบข้อมูลการจัดหางาน
     */
    public function deleteJobAgency($id): RedirectResponse
    {
        $jobAgency = JobAgency::query()
            ->whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->findOrFail($id);

        $client = Client::forUser(auth()->user())
            ->findOrFail($jobAgency->client_id);

        DB::transaction(function () use ($jobAgency, $client) {
            Client::forUser(auth()->user())
                ->whereKey($client->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedJobAgency = JobAgency::query()
                ->whereKey($jobAgency->id)
                ->where('client_id', $client->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedJobAgency->delete();

            $latestJobAgency = JobAgency::query()
                ->where('client_id', $client->id)
                ->orderByDesc('job_date')
                ->orderByDesc('id')
                ->first();

            $this->syncCaseActivity(
                $client->id,
                $latestJobAgency,
                'ข้อมูลการจัดหางานล่าสุด'
            );
        }, 3);

        return redirect()
            ->route('job_agencies.show', $client->id)
            ->with('success', 'ลบข้อมูลการจัดหางานเรียบร้อยแล้ว');
    }

    /**
     * แสดงรายงานการจัดหางาน
     */
    public function reportJobAgency(Request $request, $client_id): View|RedirectResponse
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $filterValidator = $this->filterValidator($request);

        if ($filterValidator->fails()) {
            return redirect()
                ->route('job_agencies.show', $client->id)
                ->withErrors($filterValidator, 'filters')
                ->withInput();
        }

        $filters = $filterValidator->validated();

        $jobAgencies = JobAgency::query()
            ->with('occupation:id,occupation_name')
            ->where('client_id', $client->id)
            ->when(
                !empty($filters['start_date']),
                fn ($query) => $query->where('job_date', '>=', $filters['start_date'])
            )
            ->when(
                !empty($filters['end_date']),
                fn ($query) => $query->where('job_date', '<=', $filters['end_date'])
            )
            ->orderByDesc('job_date')
            ->orderByDesc('id')
            ->get();

        return view('frontend.client.job_agency.report', compact(
            'client',
            'jobAgencies'
        ));
    }

    /**
     * กฎ Validation สำหรับเพิ่มและแก้ไข
     */
    private function rules(int $clientId, ?int $ignoreId = null): array
    {
        $uniqueDate = Rule::unique('job_agencies', 'job_date')
            ->where(fn ($query) => $query->where('client_id', $clientId));

        if ($ignoreId !== null) {
            $uniqueDate->ignore($ignoreId);
        }

        return [
            'job_date' => [
                'required',
                'date',
                'before_or_equal:today',
                $uniqueDate,
            ],
            'occupation_id' => ['required', 'integer', 'exists:occupations,id'],
            'position'      => ['required', 'string', 'max:255'],
            'income'        => ['required', 'numeric', 'min:0'],
            'company'       => ['required', 'string', 'max:255'],
            'coordinator'   => ['required', 'string', 'max:255'],
            'remark'        => ['nullable', 'string', 'max:2000'],
            'client_id'     => ['required', 'integer', Rule::in([$clientId])],
            'job_id'        => ['nullable', 'integer'],
        ];
    }

    /**
     * ข้อความ Validation ภาษาไทย
     */
    private function messages(): array
    {
        return [
            'job_date.required'         => 'กรุณาระบุวันที่เริ่มงาน',
            'job_date.date'             => 'รูปแบบวันที่เริ่มงานไม่ถูกต้อง',
            'job_date.before_or_equal'  => 'วันที่เริ่มงานต้องไม่เกินวันที่ปัจจุบัน',
            'job_date.unique'           => 'วันที่เริ่มงานนี้มีอยู่แล้ว กรุณาเลือกวันที่อื่น',
            'occupation_id.required'    => 'กรุณาเลือกอาชีพ',
            'occupation_id.integer'     => 'ข้อมูลอาชีพไม่ถูกต้อง',
            'occupation_id.exists'      => 'ไม่พบอาชีพที่เลือก',
            'position.required'         => 'กรุณาระบุตำแหน่งงาน',
            'position.string'           => 'ตำแหน่งงานต้องเป็นข้อความ',
            'position.max'              => 'ตำแหน่งงานต้องมีความยาวไม่เกิน 255 ตัวอักษร',
            'income.required'           => 'กรุณาระบุรายได้',
            'income.numeric'            => 'รายได้ต้องเป็นตัวเลข',
            'income.min'                => 'รายได้ต้องมีค่าไม่น้อยกว่า 0',
            'company.required'          => 'กรุณาระบุชื่อบริษัทหรือหน่วยงาน',
            'company.string'            => 'ชื่อบริษัทหรือหน่วยงานต้องเป็นข้อความ',
            'company.max'               => 'ชื่อบริษัทหรือหน่วยงานต้องมีความยาวไม่เกิน 255 ตัวอักษร',
            'coordinator.required'      => 'กรุณาระบุชื่อผู้ประสานงาน',
            'coordinator.string'        => 'ชื่อผู้ประสานงานต้องเป็นข้อความ',
            'coordinator.max'           => 'ชื่อผู้ประสานงานต้องมีความยาวไม่เกิน 255 ตัวอักษร',
            'remark.string'             => 'หมายเหตุต้องเป็นข้อความ',
            'remark.max'                => 'หมายเหตุต้องมีความยาวไม่เกิน 2,000 ตัวอักษร',
            'client_id.required'        => 'ไม่พบข้อมูลผู้รับบริการ',
            'client_id.integer'         => 'ข้อมูลผู้รับบริการไม่ถูกต้อง',
            'client_id.in'              => 'ไม่สามารถเปลี่ยนผู้รับบริการของรายการนี้ได้',
        ];
    }

    /**
     * Validation ตัวกรองวันที่ โดยใช้ Error Bag แยกจาก Modal
     */
    private function filterValidator(Request $request)
    {
        return Validator::make($request->only('start_date', 'end_date'), [
            'start_date' => ['nullable', 'date', 'before_or_equal:today'],
            'end_date'   => ['nullable', 'date', 'before_or_equal:today', 'after_or_equal:start_date'],
        ], [
            'start_date.date'            => 'รูปแบบวันที่เริ่มต้นไม่ถูกต้อง',
            'start_date.before_or_equal' => 'วันที่เริ่มต้นต้องไม่เกินวันที่ปัจจุบัน',
            'end_date.date'              => 'รูปแบบวันที่สิ้นสุดไม่ถูกต้อง',
            'end_date.before_or_equal'   => 'วันที่สิ้นสุดต้องไม่เกินวันที่ปัจจุบัน',
            'end_date.after_or_equal'    => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
        ]);
    }

    /**
     * ตัดช่องว่างก่อน Validation และบันทึก
     */
    private function normalizeInput(Request $request): void
    {
        $request->merge([
            'position'    => $this->trimNullable($request->input('position')),
            'company'     => $this->trimNullable($request->input('company')),
            'coordinator' => $this->trimNullable($request->input('coordinator')),
            'remark'      => $this->trimNullable($request->input('remark')),
        ]);
    }

    private function trimNullable(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * ทำให้ CaseActivity สอดคล้องกับข้อมูลล่าสุดของโมดูล
     */
    private function syncCaseActivity(
        int $clientId,
        ?JobAgency $jobAgency,
        string $title
    ): void {
        CaseActivity::query()
            ->where('client_id', $clientId)
            ->where('module', 'job_agency')
            ->delete();

        if (!$jobAgency) {
            return;
        }

        CaseActivity::record([
            'client_id'   => $clientId,
            'module'      => 'job_agency',
            'type'        => 'success',
            'title'       => $title,
            'description' => 'วันที่เริ่มงาน: ' . $this->thaiDate($jobAgency->job_date) .
                ' | ตำแหน่งงาน: ' . ($jobAgency->position ?: '-') .
                ' | บริษัท/หน่วยงาน: ' . ($jobAgency->company ?: '-'),
            'occurred_at' => Carbon::parse($jobAgency->job_date, 'Asia/Bangkok')->startOfDay(),
            'icon'        => 'bi-briefcase',
            'url'         => route('job_agencies.show', $clientId),
        ]);
    }

    private function thaiDate(mixed $date): string
    {
        if (!$date) {
            return '-';
        }

        $parsed = Carbon::parse($date);

        return $parsed->format('d/m/') . ($parsed->year + 543);
    }

    private function isIntegrityConstraintViolation(QueryException $exception): bool
    {
        $sqlState = (string) ($exception->errorInfo[0] ?? $exception->getCode());
        $driverCode = (int) ($exception->errorInfo[1] ?? 0);

        return $sqlState === '23505'
            || in_array($driverCode, [1062, 2601, 2627], true);
    }
}
