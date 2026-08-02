<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\CaseOutside;
use App\Models\Client;
use App\Models\Outside;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CaseOutsideController extends Controller
{
    /**
     * แสดงข้อมูลการติดตามเด็กที่พักอาศัยภายนอก
     */
    public function ShowCaseOutside($client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $caseoutsides = CaseOutside::with('outside')
            ->where('client_id', $client->id)
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $outside = $this->outsideOptions();

        return view('frontend.client.case_outside.case_outside_create', compact(
            'client',
            'client_id',
            'caseoutsides',
            'outside'
        ));
    }

    /**
     * บันทึกข้อมูลการติดตามใหม่
     */
    public function StoreCaseOutside(Request $request)
    {
        $request->validate([
            'client_id' => ['required', 'integer'],
        ], [
            'client_id.required' => 'ไม่พบข้อมูลผู้รับบริการ',
            'client_id.integer' => 'ข้อมูลผู้รับบริการไม่ถูกต้อง',
        ]);

        $client = Client::forUser(auth()->user())
            ->findOrFail((int) $request->input('client_id'));

        $this->normalizeTextInputs($request);

        $validated = $this->validateCaseOutside($request);

        DB::transaction(function () use ($client, $validated) {
            // ใช้แถว client เป็นจุดล็อกกลาง ป้องกันการบันทึกวันเดียวกันพร้อมกัน
            Client::whereKey($client->id)->lockForUpdate()->firstOrFail();

            $duplicate = CaseOutside::where('client_id', $client->id)
                ->whereDate('date', $validated['date'])
                ->exists();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'date' => 'วันที่ติดตามนี้มีอยู่แล้วสำหรับผู้รับบริการรายนี้',
                ]);
            }

            CaseOutside::create([
                'client_id' => $client->id,
                'date' => $validated['date'],
                'outside_id' => $validated['outside_id'],
                'follo_no' => $validated['follo_no'],
                'results' => $validated['results'],
                'teacher' => $validated['teacher'] ?? null,
                'remerk' => $validated['remerk'] ?? null,
                'dormitory' => $validated['dormitory'],
            ]);

            $this->reindexCounts($client->id);
            $this->syncLatestActivity($client->id);
        });

        return redirect()
            ->route('case_outside.show', $client->id)
            ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }

    /**
     * แก้ไขข้อมูลการติดตาม
     */
    public function UpdateCaseOutside(Request $request, $id)
    {
        $case = CaseOutside::whereKey($id)
            ->whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->firstOrFail();

        // ไม่รับ client_id จากฟอร์ม เพื่อป้องกันการย้ายข้อมูลไปยังผู้รับบริการรายอื่น
        $this->normalizeTextInputs($request);

        $validated = $this->validateCaseOutside($request, $case->id, $case->client_id);

        DB::transaction(function () use ($case, $validated) {
            Client::whereKey($case->client_id)->lockForUpdate()->firstOrFail();

            $lockedCase = CaseOutside::whereKey($case->id)
                ->lockForUpdate()
                ->firstOrFail();

            $duplicate = CaseOutside::where('client_id', $lockedCase->client_id)
                ->whereDate('date', $validated['date'])
                ->where('id', '<>', $lockedCase->id)
                ->exists();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'date' => 'วันที่ติดตามนี้มีอยู่แล้วสำหรับผู้รับบริการรายนี้',
                ]);
            }

            $lockedCase->update([
                'date' => $validated['date'],
                'outside_id' => $validated['outside_id'],
                'follo_no' => $validated['follo_no'],
                'results' => $validated['results'],
                'teacher' => $validated['teacher'] ?? null,
                'remerk' => $validated['remerk'] ?? null,
                'dormitory' => $validated['dormitory'],
            ]);

            $this->reindexCounts($lockedCase->client_id);
            $this->syncLatestActivity($lockedCase->client_id);
        });

        return redirect()
            ->route('case_outside.show', $case->client_id)
            ->with('success', 'แก้ไขข้อมูลเรียบร้อยแล้ว');
    }

    /**
     * ลบข้อมูลการติดตาม
     */
    public function DeleteCaseOutside($id)
    {
        $case = CaseOutside::whereKey($id)
            ->whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->firstOrFail();

        $clientId = $case->client_id;

        DB::transaction(function () use ($case, $clientId) {
            Client::whereKey($clientId)->lockForUpdate()->firstOrFail();

            $lockedCase = CaseOutside::whereKey($case->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedCase->delete();

            $this->reindexCounts($clientId);
            $this->syncLatestActivity($clientId);
        });

        return redirect()
            ->route('case_outside.show', $clientId)
            ->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    /**
     * หน้ากำหนดเงื่อนไขรายงาน
     */
    public function FilterCaseOutside($client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $outside = $this->outsideOptions();

        return view('frontend.client.case_outside.case_outside_filter', compact(
            'client',
            'outside'
        ));
    }

    /**
     * รายงานข้อมูลการติดตาม
     */
    public function ReportCaseOutside(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $today = now('Asia/Bangkok')->toDateString();

        $filters = $request->validate([
            'date_start' => ['nullable', 'date', 'before_or_equal:' . $today],
            'date_end' => [
                'nullable',
                'date',
                'after_or_equal:date_start',
                'before_or_equal:' . $today,
            ],
            'outside_id' => ['nullable', 'integer', 'exists:outsides,id'],
            'follo_no' => ['nullable', Rule::in($this->followMethods())],
        ], [
            'date_start.date' => 'วันที่เริ่มต้นไม่ถูกต้อง',
            'date_start.before_or_equal' => 'วันที่เริ่มต้นต้องไม่เกินวันที่ปัจจุบัน',
            'date_end.date' => 'วันที่สิ้นสุดไม่ถูกต้อง',
            'date_end.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
            'date_end.before_or_equal' => 'วันที่สิ้นสุดต้องไม่เกินวันที่ปัจจุบัน',
            'outside_id.exists' => 'สาเหตุที่เลือกไม่ถูกต้อง',
            'follo_no.in' => 'รูปแบบการดำเนินงานไม่ถูกต้อง',
        ]);

        $query = CaseOutside::with('outside')
            ->where('client_id', $client->id);

        if (!empty($filters['date_start'])) {
            $query->whereDate('date', '>=', $filters['date_start']);
        }

        if (!empty($filters['date_end'])) {
            $query->whereDate('date', '<=', $filters['date_end']);
        }

        if (!empty($filters['outside_id'])) {
            $query->where('outside_id', $filters['outside_id']);
        }

        if (!empty($filters['follo_no'])) {
            $query->where('follo_no', $filters['follo_no']);
        }

        $caseoutsides = $query
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $filterOutsideName = !empty($filters['outside_id'])
            ? Outside::whereKey($filters['outside_id'])->value('outside_name')
            : null;

        return view('frontend.client.case_outside.case_outside_report', compact(
            'client',
            'caseoutsides',
            'filters',
            'filterOutsideName'
        ));
    }

    /**
     * กฎตรวจสอบข้อมูล เพิ่ม/แก้ไข
     */
    private function validateCaseOutside(
        Request $request,
        ?int $ignoreId = null,
        ?int $clientId = null
    ): array {
        $today = now('Asia/Bangkok')->toDateString();

        $dateRule = Rule::unique('case_outsides', 'date')
            ->where(function ($query) use ($request, $clientId) {
                return $query->where(
                    'client_id',
                    $clientId ?? (int) $request->input('client_id')
                );
            });

        if ($ignoreId !== null) {
            $dateRule->ignore($ignoreId);
        }

        return $request->validate([
            'date' => [
                'required',
                'date',
                'before_or_equal:' . $today,
                $dateRule,
            ],
            'outside_id' => ['required', 'integer', 'exists:outsides,id'],
            'follo_no' => ['required', Rule::in($this->followMethods())],
            'results' => ['required', 'string', 'max:5000'],
            'teacher' => ['nullable', 'string', 'max:255'],
            'remerk' => ['nullable', 'string', 'max:5000'],
            'dormitory' => ['required', 'string', 'max:255'],
        ], [
            'date.required' => 'กรุณาระบุวันที่ติดตาม',
            'date.date' => 'รูปแบบวันที่ติดตามไม่ถูกต้อง',
            'date.before_or_equal' => 'วันที่ติดตามต้องไม่เกินวันที่ปัจจุบัน',
            'date.unique' => 'วันที่ติดตามนี้มีอยู่แล้วสำหรับผู้รับบริการรายนี้',

            'outside_id.required' => 'กรุณาเลือกสาเหตุที่พักอาศัยอยู่ภายนอก',
            'outside_id.integer' => 'ข้อมูลสาเหตุไม่ถูกต้อง',
            'outside_id.exists' => 'ไม่พบข้อมูลสาเหตุที่เลือก',

            'follo_no.required' => 'กรุณาเลือกการดำเนินงาน',
            'follo_no.in' => 'รูปแบบการดำเนินงานไม่ถูกต้อง',

            'results.required' => 'กรุณาระบุผลการติดตาม',
            'results.string' => 'ผลการติดตามต้องเป็นข้อความ',
            'results.max' => 'ผลการติดตามต้องไม่เกิน 5,000 ตัวอักษร',

            'teacher.string' => 'ชื่อผู้ติดตามต้องเป็นข้อความ',
            'teacher.max' => 'ชื่อผู้ติดตามต้องไม่เกิน 255 ตัวอักษร',

            'remerk.string' => 'หมายเหตุต้องเป็นข้อความ',
            'remerk.max' => 'หมายเหตุต้องไม่เกิน 5,000 ตัวอักษร',

            'dormitory.required' => 'กรุณาระบุสถานที่พัก',
            'dormitory.string' => 'สถานที่พักต้องเป็นข้อความ',
            'dormitory.max' => 'สถานที่พักต้องไม่เกิน 255 ตัวอักษร',
        ]);
    }

    /**
     * ตัดช่องว่าง และแปลงข้อความว่างเป็น null
     */
    private function normalizeTextInputs(Request $request): void
    {
        $request->merge([
            'dormitory' => trim((string) $request->input('dormitory')),
            'results' => trim((string) $request->input('results')),
            'teacher' => $this->nullableTrim($request->input('teacher')),
            'remerk' => $this->nullableTrim($request->input('remerk')),
        ]);
    }

    private function nullableTrim($value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * เรียงเลขครั้งใหม่ตามวันที่ โดยอัปเดตเฉพาะรายการที่เลขเปลี่ยน
     */
    private function reindexCounts(int $clientId): void
    {
        $items = CaseOutside::where('client_id', $clientId)
            ->orderBy('date')
            ->orderBy('id')
            ->get(['id', 'count']);

        foreach ($items as $index => $item) {
            $newCount = $index + 1;

            if ((int) $item->count !== $newCount) {
                DB::table('case_outsides')
                    ->where('id', $item->id)
                    ->update(['count' => $newCount]);
            }
        }
    }

    /**
     * ให้ CaseActivity สะท้อนรายการล่าสุดเสมอ แม้มีการแก้ไขหรือลบรายการ
     */
    private function syncLatestActivity(int $clientId): void
    {
        CaseActivity::where('client_id', $clientId)
            ->where('module', 'case_outside')
            ->delete();

        $latest = CaseOutside::with('outside')
            ->where('client_id', $clientId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->first();

        if (!$latest) {
            return;
        }

        $occurredAt = Carbon::parse(
            $latest->date,
            'Asia/Bangkok'
        )->startOfDay();

        CaseActivity::record([
            'client_id' => $clientId,
            'module' => 'case_outside',
            'type' => 'success',
            'title' => 'ติดตามเด็กที่พักอาศัยภายนอก',
            'description' => 'ครั้งที่ ' . ($latest->count ?? '-') .
                ' | วันที่ติดตาม: ' . ($latest->date ?? '-') .
                ' | สถานที่พัก: ' . Str::limit($latest->dormitory ?: '-', 80) .
                ' | ผลการติดตาม: ' . Str::limit($latest->results ?: '-', 160),
            'occurred_at' => $occurredAt,
            'icon' => 'bi-geo-alt',
            'url' => route('case_outside.show', $clientId),
        ]);
    }

    private function outsideOptions()
    {
        return Outside::query()
            ->orderBy('outside_name')
            ->orderBy('id')
            ->get(['id', 'outside_name']);
    }

    private function followMethods(): array
    {
        return [
            'หน่วยงานไปเอง',
            'โทรศัพท์',
            'จดหมาย',
        ];
    }
}
