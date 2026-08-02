<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Escape;
use App\Models\EscapeFollow;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EscapeFollowController extends Controller
{
    /**
     * บันทึกการติดตามใหม่
     */
    public function StoreFollow(Request $request, $escape_id)
    {
        $escape = Escape::query()
            ->with('client')
            ->whereHas('client', fn ($query) => $query->forUser(auth()->user()))
            ->findOrFail($escape_id);

        Client::forUser(auth()->user())->findOrFail($escape->client_id);

        $this->normalizeFollowInput($request);
        $data = $this->validateFollow($request, $escape);

        $sequenceError = DB::transaction(function () use ($escape, $data) {
            // ล็อกข้อมูลหลักเพื่อป้องกันเลขครั้งซ้ำเมื่อมีการบันทึกพร้อมกัน
            Escape::query()->whereKey($escape->id)->lockForUpdate()->firstOrFail();

            $lastFollow = EscapeFollow::query()
                ->where('escape_id', $escape->id)
                ->orderByDesc('count')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $nextCount = ((int) ($lastFollow?->count ?? 0)) + 1;

            if ($lastFollow?->trace_date) {
                $newTraceDate = Carbon::parse($data['trace_date'])->startOfDay();
                $lastTraceDate = Carbon::parse($lastFollow->trace_date)->startOfDay();

                if ($newTraceDate->lte($lastTraceDate)) {
                    return 'วันที่ติดตามของครั้งที่ ' . $nextCount
                        . ' ต้องมากกว่าวันที่ของครั้งที่ ' . $lastFollow->count
                        . ' และห้ามซ้ำ';
                }
            }

            $data['escape_id'] = $escape->id;
            $data['count'] = $nextCount;

            EscapeFollow::create($data);

            return null;
        });

        if ($sequenceError) {
            return redirect()->back()
                ->withErrors(['trace_date' => $sequenceError])
                ->withInput();
        }

        return redirect()
            ->route('escape.edit', $escape->id)
            ->with([
                'message' => 'บันทึกข้อมูลการติดตามเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * อัปเดตการติดตาม
     */
    public function UpdateFollow(Request $request, $id)
    {
        $follow = EscapeFollow::query()
            ->with('escape.client')
            ->whereHas('escape.client', fn ($query) => $query->forUser(auth()->user()))
            ->findOrFail($id);

        $escape = $follow->escape;
        Client::forUser(auth()->user())->findOrFail($escape->client_id);

        $this->normalizeFollowInput($request);
        $data = $this->validateFollow($request, $escape);

        $sequenceError = DB::transaction(function () use ($follow, $data) {
            $lockedFollow = EscapeFollow::query()
                ->whereKey($follow->id)
                ->lockForUpdate()
                ->firstOrFail();

            Escape::query()->whereKey($lockedFollow->escape_id)->lockForUpdate()->firstOrFail();

            $newTraceDate = Carbon::parse($data['trace_date'])->startOfDay();

            $prevFollow = EscapeFollow::query()
                ->where('escape_id', $lockedFollow->escape_id)
                ->where('count', '<', $lockedFollow->count)
                ->orderByDesc('count')
                ->orderByDesc('id')
                ->first();

            if ($prevFollow?->trace_date) {
                $prevTraceDate = Carbon::parse($prevFollow->trace_date)->startOfDay();

                if ($newTraceDate->lte($prevTraceDate)) {
                    return 'วันที่ติดตามของครั้งที่ ' . $lockedFollow->count
                        . ' ต้องมากกว่าวันที่ของครั้งที่ ' . $prevFollow->count
                        . ' และห้ามซ้ำ';
                }
            }

            $nextFollow = EscapeFollow::query()
                ->where('escape_id', $lockedFollow->escape_id)
                ->where('count', '>', $lockedFollow->count)
                ->orderBy('count')
                ->orderBy('id')
                ->first();

            if ($nextFollow?->trace_date) {
                $nextTraceDate = Carbon::parse($nextFollow->trace_date)->startOfDay();

                if ($newTraceDate->gte($nextTraceDate)) {
                    return 'วันที่ติดตามของครั้งที่ ' . $lockedFollow->count
                        . ' ต้องน้อยกว่าวันที่ของครั้งที่ ' . $nextFollow->count
                        . ' และห้ามซ้ำ';
                }
            }

            // ถ้าลบวันที่ลงโทษ ระบบต้องล้างข้อความการลงโทษด้วยเสมอ
            if (empty($data['punish_date'])) {
                $data['punish'] = null;
            }

            $data['count'] = $lockedFollow->count;
            $lockedFollow->update($data);

            return null;
        });

        if ($sequenceError) {
            return redirect()->back()
                ->withErrors(['trace_date' => $sequenceError])
                ->withInput();
        }

        return redirect()
            ->route('escape.edit', $follow->escape_id)
            ->with([
                'message' => 'อัปเดตข้อมูลการติดตามเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * ลบการติดตามและจัดลำดับครั้งใหม่
     */
    public function DeleteFollow($id)
    {
        $follow = EscapeFollow::query()
            ->with('escape.client')
            ->whereHas('escape.client', fn ($query) => $query->forUser(auth()->user()))
            ->findOrFail($id);

        $escape = $follow->escape;
        Client::forUser(auth()->user())->findOrFail($escape->client_id);

        $escapeId = $follow->escape_id;

        DB::transaction(function () use ($follow, $escapeId) {
            Escape::query()->whereKey($escapeId)->lockForUpdate()->firstOrFail();

            EscapeFollow::query()
                ->whereKey($follow->id)
                ->lockForUpdate()
                ->firstOrFail()
                ->delete();

            $remainingFollows = EscapeFollow::query()
                ->where('escape_id', $escapeId)
                ->orderBy('trace_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($remainingFollows as $index => $item) {
                $newCount = $index + 1;

                if ((int) $item->count !== $newCount) {
                    $item->update(['count' => $newCount]);
                }
            }
        });

        return redirect()
            ->route('escape.edit', $escapeId)
            ->with([
                'message' => 'ลบข้อมูลการติดตามเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * ทำความสะอาดข้อมูลก่อนตรวจสอบ
     * - ช่องว่างเปลี่ยนเป็น null
     * - ถ้าไม่มีวันที่ลงโทษ จะไม่รับข้อความการลงโทษเข้าระบบ
     */
    private function normalizeFollowInput(Request $request): void
    {
        $normalized = [];

        foreach (['detail', 'punish', 'remark'] as $field) {
            $normalized[$field] = $request->filled($field)
                ? trim((string) $request->input($field))
                : null;
        }

        foreach (['report_date', 'stop_date', 'punish_date'] as $field) {
            $normalized[$field] = $request->filled($field)
                ? $request->input($field)
                : null;
        }

        if (empty($normalized['punish_date'])) {
            $normalized['punish'] = null;
        }

        $request->merge($normalized);
    }

    /**
     * กฎตรวจสอบข้อมูลร่วมกันของฟอร์มเพิ่มและแก้ไข
     */
    private function validateFollow(Request $request, Escape $escape): array
    {
        $today = now('Asia/Bangkok')->toDateString();
        $retireDate = $escape->retire_date
            ? Carbon::parse($escape->retire_date)->toDateString()
            : null;

        $traceDateRules = [
            'required',
            'date',
            'before_or_equal:' . $today,
        ];

        if ($retireDate) {
            $traceDateRules[] = 'after_or_equal:' . $retireDate;
        }

        return $request->validate([
            'trace_date' => $traceDateRules,
            'trac_no' => ['required', Rule::in(['พบ', 'ไม่พบ'])],
            'detail' => ['nullable', 'string', 'max:5000'],
            'report_date' => ['nullable', 'date', 'after_or_equal:trace_date', 'before_or_equal:' . $today],
            'stop_date' => ['nullable', 'date', 'after_or_equal:trace_date', 'before_or_equal:' . $today],
            'punish_date' => ['nullable', 'date', 'after_or_equal:trace_date', 'before_or_equal:' . $today],
            'punish' => ['required_with:punish_date', 'nullable', 'string', 'max:255'],
            'remark' => ['nullable', 'string', 'max:255'],
        ], [
            'trace_date.required' => 'กรุณาระบุวันที่ติดตาม',
            'trace_date.date' => 'รูปแบบวันที่ติดตามไม่ถูกต้อง',
            'trace_date.before_or_equal' => 'วันที่ติดตามต้องไม่เกินวันปัจจุบัน',
            'trace_date.after_or_equal' => 'วันที่ติดตามต้องไม่น้อยกว่าวันที่ออก/หลบหนี',
            'trac_no.required' => 'กรุณาเลือกผลการติดตาม',
            'trac_no.in' => 'ผลการติดตามต้องเป็น “พบ” หรือ “ไม่พบ” เท่านั้น',
            'detail.string' => 'รายละเอียดต้องเป็นข้อความ',
            'detail.max' => 'รายละเอียดต้องไม่เกิน 5,000 ตัวอักษร',
            'report_date.date' => 'รูปแบบวันที่แจ้งความไม่ถูกต้อง',
            'report_date.after_or_equal' => 'วันที่แจ้งความต้องไม่น้อยกว่าวันที่ติดตาม',
            'report_date.before_or_equal' => 'วันที่แจ้งความต้องไม่เกินวันปัจจุบัน',
            'stop_date.date' => 'รูปแบบวันที่ยุติการติดตามไม่ถูกต้อง',
            'stop_date.after_or_equal' => 'วันที่ยุติการติดตามต้องไม่น้อยกว่าวันที่ติดตาม',
            'stop_date.before_or_equal' => 'วันที่ยุติการติดตามต้องไม่เกินวันปัจจุบัน',
            'punish_date.date' => 'รูปแบบวันที่ลงโทษไม่ถูกต้อง',
            'punish_date.after_or_equal' => 'วันที่ลงโทษต้องไม่น้อยกว่าวันที่ติดตาม',
            'punish_date.before_or_equal' => 'วันที่ลงโทษต้องไม่เกินวันปัจจุบัน',
            'punish.required_with' => 'เมื่อกำหนดวันที่ลงโทษ กรุณาระบุการลงโทษด้วย',
            'punish.string' => 'การลงโทษต้องเป็นข้อความ',
            'punish.max' => 'การลงโทษต้องไม่เกิน 255 ตัวอักษร',
            'remark.string' => 'หมายเหตุต้องเป็นข้อความ',
            'remark.max' => 'หมายเหตุต้องไม่เกิน 255 ตัวอักษร',
        ]);
    }
}
