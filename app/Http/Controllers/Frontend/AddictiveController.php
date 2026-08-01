<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Addictive;
use App\Models\CaseActivity;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AddictiveController extends Controller
{
    /**
     * แสดงหน้าบันทึกผลการตรวจสารเสพติด
     */
    public function AddAddictive($client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $addictives = Addictive::where('client_id', $client->id)
            ->orderBy('count')
            ->orderBy('id')
            ->get();

        $addictive = null;

        return view('frontend.client.addictive.addictive_create', compact(
            'client',
            'client_id',
            'addictives',
            'addictive'
        ));
    }

    /**
     * บันทึกผลการตรวจใหม่
     */
    public function StoreAddictive(Request $request)
    {
        $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
        ], [
            'client_id.required' => 'ไม่พบรหัสผู้รับบริการ',
            'client_id.integer'  => 'รหัสผู้รับบริการไม่ถูกต้อง',
            'client_id.exists'   => 'รหัสผู้รับบริการไม่ถูกต้อง',
        ]);

        $client = Client::forUser(auth()->user())
            ->findOrFail((int) $request->input('client_id'));

        $request->merge(['client_id' => $client->id]);
        $this->normalizeRequest($request);

        $data = $this->validateAddictive($request, $client->id);
        $data['client_id'] = $client->id;

        $addictive = DB::transaction(function () use ($data, $client) {
            // ล็อกเจ้าของข้อมูลเพื่อให้คำขอบันทึกของผู้รับบริการรายเดียวกันทำงานทีละรายการ
            Client::whereKey($client->id)->lockForUpdate()->firstOrFail();

            // ล็อกชุดข้อมูลของผู้รับบริการรายนี้ ป้องกันเลขครั้งที่ซ้ำเมื่อบันทึกพร้อมกัน
            $existingRows = Addictive::where('client_id', $client->id)
                ->orderBy('count')
                ->lockForUpdate()
                ->get(['id', 'count', 'date']);

            $nextCount = ((int) ($existingRows->max('count') ?? 0)) + 1;

            $this->validateDateOrderByCount(
                clientId: $client->id,
                currentCount: $nextCount,
                inputDate: $data['date']
            );

            $data['count'] = $nextCount;

            if ((int) $data['exam'] === 0) {
                $data['refer'] = null;
            }

            $row = Addictive::create($data);

            $this->syncLatestCaseActivity(
                clientId: $client->id,
                actionTitle: 'บันทึกการตรวจสารเสพติด',
                focusId: $row->id
            );

            return $row;
        }, 3);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                'data'    => $addictive->fresh(),
            ]);
        }

        return redirect()
            ->route('addictive.create', $client->id)
            ->with([
                'message'    => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * โหลดข้อมูลสำหรับ Modal แก้ไข
     */
    public function EditAddictiveJson($id)
    {
        $addictive = Addictive::whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->findOrFail($id);

        return response()->json([
            'id'        => $addictive->id,
            'date'      => $addictive->date
                ? Carbon::parse($addictive->date)->format('Y-m-d')
                : null,
            'count'     => $addictive->count,
            'exam'      => (string) $addictive->exam,
            'refer'     => $addictive->refer !== null ? (string) $addictive->refer : null,
            'record'    => $addictive->record ?? '',
            'recorder'  => $addictive->recorder ?? '',
            'client_id' => $addictive->client_id,
        ]);
    }

    /**
     * อัปเดตผลการตรวจ
     */
    public function UpdateAddictive(Request $request, $id)
    {
        $addictive = Addictive::whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->findOrFail($id);

        // ห้ามย้ายรายการไปยังผู้รับบริการรายอื่น แม้แก้ค่า hidden input
        $request->merge([
            'client_id' => $addictive->client_id,
            '_edit_id'  => $addictive->id,
        ]);

        $this->normalizeRequest($request);

        $data = $this->validateAddictive(
            request: $request,
            clientId: $addictive->client_id,
            ignoreId: $addictive->id
        );
        $data['client_id'] = $addictive->client_id;

        DB::transaction(function () use ($data, $addictive) {
            $lockedRow = Addictive::whereKey($addictive->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->validateDateOrderByCount(
                clientId: $lockedRow->client_id,
                currentCount: (int) $lockedRow->count,
                inputDate: $data['date'],
                ignoreId: $lockedRow->id
            );

            if ((int) $data['exam'] === 0) {
                $data['refer'] = null;
            }

            $lockedRow->update($data);

            $this->syncLatestCaseActivity(
                clientId: $lockedRow->client_id,
                actionTitle: 'แก้ไขการตรวจสารเสพติด',
                focusId: $lockedRow->id
            );
        }, 3);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'อัปเดตข้อมูลเรียบร้อยแล้ว',
                'data'    => $addictive->fresh(),
            ]);
        }

        return redirect()
            ->route('addictive.create', $addictive->client_id)
            ->with([
                'message'    => 'อัปเดตข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * ลบผลการตรวจ และจัดลำดับครั้งใหม่ให้ต่อเนื่อง
     */
    public function DeleteAddictive($id)
    {
        $addictive = Addictive::whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->findOrFail($id);

        $clientId = $addictive->client_id;

        DB::transaction(function () use ($addictive, $clientId) {
            $lockedRow = Addictive::whereKey($addictive->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedRow->delete();

            // ล็อกและเรียงเลขครั้งใหม่ทีละรายการ ปลอดภัยกว่าการ decrement ทั้งชุด
            $remainingRows = Addictive::where('client_id', $clientId)
                ->orderBy('count')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($remainingRows as $index => $row) {
                $newCount = $index + 1;

                if ((int) $row->count !== $newCount) {
                    $row->count = $newCount;
                    $row->saveQuietly();
                }
            }

            $this->syncLatestCaseActivity($clientId);
        }, 3);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'ลบข้อมูลเรียบร้อยแล้ว',
            ]);
        }

        return redirect()
            ->route('addictive.create', $clientId)
            ->with([
                'message'    => 'ลบข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * รายงานรายครั้ง
     */
    public function ReportAddictive($id)
    {
        $addictive = Addictive::with('client')
            ->whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->findOrFail($id);

        $client = $addictive->client;

        return view('frontend.client.addictive.addictive_report', compact(
            'client',
            'addictive'
        ));
    }

    /**
     * รายงานทั้งหมด พร้อมตัวกรองช่วงวันที่
     */
    public function ReportAddictiveAll(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $today = now('Asia/Bangkok')->toDateString();

        $validated = $request->validate([
            'date_from' => ['nullable', 'date', 'before_or_equal:' . $today],
            'date_to'   => [
                'nullable',
                'date',
                'before_or_equal:' . $today,
                'after_or_equal:date_from',
            ],
        ], [
            'date_from.date'            => 'รูปแบบวันที่เริ่มต้นไม่ถูกต้อง',
            'date_from.before_or_equal' => 'วันที่เริ่มต้นต้องไม่เกินวันปัจจุบัน',
            'date_to.date'              => 'รูปแบบวันที่สิ้นสุดไม่ถูกต้อง',
            'date_to.before_or_equal'   => 'วันที่สิ้นสุดต้องไม่เกินวันปัจจุบัน',
            'date_to.after_or_equal'    => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
        ]);

        $query = Addictive::where('client_id', $client->id);

        if (!empty($validated['date_from'])) {
            $query->whereDate('date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate('date', '<=', $validated['date_to']);
        }

        $addictives = $query
            ->orderBy('count')
            ->orderBy('id')
            ->get();

        $dateFrom = $validated['date_from'] ?? null;
        $dateTo = $validated['date_to'] ?? null;

        return view('frontend.client.addictive.addictive_report_all', compact(
            'client',
            'addictives',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Validation กลางสำหรับเพิ่มและแก้ไข
     */
    private function validateAddictive(
        Request $request,
        int $clientId,
        ?int $ignoreId = null
    ): array {
        $today = now('Asia/Bangkok')->toDateString();

        return $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'date'      => [
                'required',
                'date',
                'before_or_equal:' . $today,
                Rule::unique('addictives', 'date')
                    ->where(fn ($query) => $query->where('client_id', $clientId))
                    ->ignore($ignoreId),
            ],
            'exam'    => ['required', Rule::in(['0', '1', 0, 1])],
            'refer'   => [
                'nullable',
                Rule::requiredIf(fn () => (string) $request->input('exam') === '1'),
                Rule::in(['1', '2', 1, 2]),
            ],
            'record'   => ['nullable', 'string', 'max:3000'],
            'recorder' => ['required', 'string', 'max:255'],
        ], [
            'client_id.required'       => 'ไม่พบรหัสผู้รับบริการ',
            'client_id.integer'        => 'รหัสผู้รับบริการไม่ถูกต้อง',
            'client_id.exists'         => 'รหัสผู้รับบริการไม่ถูกต้อง',
            'date.required'            => 'กรุณาระบุวันที่ตรวจ',
            'date.date'                => 'รูปแบบวันที่ตรวจไม่ถูกต้อง',
            'date.before_or_equal'     => 'วันที่ตรวจต้องไม่เกินวันปัจจุบัน',
            'date.unique'              => 'วันที่นี้ถูกบันทึกแล้วสำหรับผู้รับบริการรายนี้',
            'exam.required'            => 'กรุณาเลือกผลการตรวจ',
            'exam.in'                  => 'ผลการตรวจที่เลือกไม่ถูกต้อง',
            'refer.required'           => 'กรุณาเลือกแนวทางดำเนินการเมื่อพบสารเสพติด',
            'refer.in'                 => 'แนวทางดำเนินการที่เลือกไม่ถูกต้อง',
            'record.string'            => 'บันทึกผลต้องเป็นข้อความ',
            'record.max'               => 'บันทึกผลต้องไม่เกิน 3,000 ตัวอักษร',
            'recorder.required'        => 'กรุณาระบุชื่อผู้ตรวจ',
            'recorder.string'          => 'ชื่อผู้ตรวจต้องเป็นข้อความ',
            'recorder.max'             => 'ชื่อผู้ตรวจต้องไม่เกิน 255 ตัวอักษร',
        ]);
    }

    /**
     * ตัดช่องว่าง และเปลี่ยนช่องไม่บังคับที่ว่างเป็น NULL
     */
    private function normalizeRequest(Request $request): void
    {
        $record = trim((string) $request->input('record', ''));
        $recorder = trim((string) $request->input('recorder', ''));

        $request->merge([
            'record'   => $record !== '' ? $record : null,
            'recorder' => $recorder,
        ]);
    }

    /**
     * ตรวจลำดับวันที่ตามเลขครั้ง
     */
    private function validateDateOrderByCount(
        int $clientId,
        int $currentCount,
        string $inputDate,
        ?int $ignoreId = null
    ): void {
        $input = Carbon::parse($inputDate)->startOfDay();

        $previous = Addictive::where('client_id', $clientId)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('count', '<', $currentCount)
            ->orderByDesc('count')
            ->lockForUpdate()
            ->first();

        if ($previous?->date) {
            $previousDate = Carbon::parse($previous->date)->startOfDay();

            if ($input->lte($previousDate)) {
                throw ValidationException::withMessages([
                    'date' => sprintf(
                        'วันที่ของครั้งที่ %d ต้องมากกว่าวันที่ของครั้งที่ %d (%s)',
                        $currentCount,
                        $previous->count,
                        $this->formatThaiDate($previousDate)
                    ),
                ]);
            }
        }

        $next = Addictive::where('client_id', $clientId)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('count', '>', $currentCount)
            ->orderBy('count')
            ->lockForUpdate()
            ->first();

        if ($next?->date) {
            $nextDate = Carbon::parse($next->date)->startOfDay();

            if ($input->gte($nextDate)) {
                throw ValidationException::withMessages([
                    'date' => sprintf(
                        'วันที่ของครั้งที่ %d ต้องน้อยกว่าวันที่ของครั้งที่ %d (%s)',
                        $currentCount,
                        $next->count,
                        $this->formatThaiDate($nextDate)
                    ),
                ]);
            }
        }
    }

    /**
     * เก็บ CaseActivity เพียงรายการล่าสุดที่ยังมีอยู่จริง
     */
    private function syncLatestCaseActivity(
        int $clientId,
        string $actionTitle = 'ข้อมูลการตรวจสารเสพติดล่าสุด',
        ?int $focusId = null
    ): void {
        CaseActivity::where('client_id', $clientId)
            ->where('module', 'addictive')
            ->delete();

        $latest = Addictive::where('client_id', $clientId)
            ->orderByDesc('date')
            ->orderByDesc('count')
            ->orderByDesc('id')
            ->first();

        if (!$latest) {
            return;
        }

        $title = $focusId !== null && (int) $latest->id === $focusId
            ? $actionTitle
            : 'ข้อมูลการตรวจสารเสพติดล่าสุด';

        CaseActivity::record([
            'client_id'   => $clientId,
            'module'      => 'addictive',
            'type'        => (int) $latest->exam === 1 ? 'warning' : 'success',
            'title'       => $title,
            'description' => 'วันที่ตรวจ: ' . ($latest->date ?? '-') .
                ' | ครั้งที่ ' . ($latest->count ?? '-') .
                ' | ผลตรวจ: ' . ((int) $latest->exam === 1
                    ? 'พบสารเสพติด'
                    : 'ไม่พบสารเสพติด') .
                ' | ผู้ตรวจ: ' . ($latest->recorder ?: '-'),
            'occurred_at' => $latest->date ?? now('Asia/Bangkok'),
            'icon'        => 'bi-clipboard2-pulse',
            'url'         => route('addictive.create', $clientId),
        ]);
    }

    private function formatThaiDate(Carbon $date): string
    {
        return $date->format('d/m/') . ($date->year + 543);
    }
}
