<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Escape;
use App\Models\EscapeFollow;
use Carbon\Carbon;
use App\Models\CaseActivity;

class EscapeFollowController extends Controller
{
    // เพิ่มการติดตามใหม่
    public function StoreFollow(Request $request, $escape_id)
    {
        // =========================
        // PATCH: กันเดา URL ตั้งแต่ query แรก
        // เดิม: $escape = Escape::findOrFail($escape_id);
        // =========================
        $escape = Escape::whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($escape_id);

        // =========================
        // PATCH: กันเข้าถึง client คนอื่น
        // =========================
        Client::forUser(auth()->user())->findOrFail($escape->client_id);

        $data = $request->validate([
                'trace_date' => [
                    'required',
                    'date',
                    'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
                ],
                'trac_no' => 'required|string',
                'detail' => 'nullable|string',

                'report_date' => [
                    'nullable',
                    'date',
                    'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
                ],
                'stop_date' => [
                    'nullable',
                    'date',
                    'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
                ],
                'punish' => 'nullable|string',
                'punish_date' => [
                    'nullable',
                    'date',
                    'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
                ],
                'remark' => 'nullable|string',
            ], [
                'trace_date.required'        => 'กรุณาระบุวันที่ติดตาม',
                'trace_date.date'            => 'รูปแบบวันที่ติดตามไม่ถูกต้อง',
                'trace_date.before_or_equal' => 'วันที่ติดตามต้องไม่เกินวันปัจจุบัน',

                'report_date.date'            => 'รูปแบบวันที่แจ้งความไม่ถูกต้อง',
                'report_date.before_or_equal' => 'วันที่แจ้งความต้องไม่เกินวันปัจจุบัน',

                'stop_date.date'            => 'รูปแบบวันที่ยุติการติดตามไม่ถูกต้อง',
                'stop_date.before_or_equal' => 'วันที่ยุติการติดตามต้องไม่เกินวันปัจจุบัน',

                'punish_date.date'            => 'รูปแบบวันที่ลงโทษไม่ถูกต้อง',
                'punish_date.before_or_equal' => 'วันที่ลงโทษต้องไม่เกินวันปัจจุบัน',
            ]);

        $nextCount = EscapeFollow::where('escape_id', $escape_id)->count() + 1;

        $lastFollow = EscapeFollow::where('escape_id', $escape_id)
            ->orderByDesc('count')
            ->first();

        if ($lastFollow && $lastFollow->trace_date) {
            $newTraceDate  = Carbon::parse($data['trace_date'])->startOfDay();
            $lastTraceDate = Carbon::parse($lastFollow->trace_date)->startOfDay();

            if ($newTraceDate->lte($lastTraceDate)) {
                return redirect()->back()
                    ->withErrors([
                        'trace_date' => 'วันที่ติดตามของครั้งที่ ' . $nextCount . ' ต้องมากกว่าวันที่ของครั้งที่ ' . $lastFollow->count . ' เท่านั้น'
                    ])
                    ->withInput();
            }
        }

        $data['escape_id'] = $escape->id;
        $data['count']     = $nextCount;

       $follow = EscapeFollow::create($data);

                CaseActivity::where('client_id', $escape->client_id)
                    ->where('module', 'escape')
                    ->delete();

                CaseActivity::record([
                    'client_id'   => $escape->client_id,
                    'module'      => 'escape',
                    'type'        => 'warning',
                    'title'       => 'บันทึกการติดตามการออก/หลบหนี',
                    'description' => 'ติดตามครั้งที่ ' . ($follow->count ?? '-') .
                                    ' | วันที่ติดตาม: ' . ($data['trace_date'] ?? '-') .
                                    ' | รายละเอียด: ' . ($data['detail'] ?? '-'),
                    'occurred_at' => now(),
                    'icon'        => 'bi-person-check',
                    'url'         => route('escape.edit', $escape->id),
                ]);

        return redirect()
            ->route('escape.edit', $escape->id)
            ->with([
                'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success'
            ]);
    }

    // อัปเดตการติดตาม
    public function UpdateFollow(Request $request, $id)
    {
        // =========================
        // PATCH: กันเดา URL ตั้งแต่ query แรก
        // เดิม: $follow = EscapeFollow::findOrFail($id);
        // =========================
        $follow = EscapeFollow::whereHas('escape.client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        // =========================
        // PATCH: ใช้ escape จาก relation ที่ผ่านสิทธิ์แล้ว
        // เดิม:
        // $escape = Escape::findOrFail($follow->escape_id);
        // Client::forUser(...)->findOrFail(...)
        // =========================
        $escape = $follow->escape;

        Client::forUser(auth()->user())->findOrFail($escape->client_id);

       $data = $request->validate([
            'trace_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],
            'trac_no' => 'required|string',
            'detail' => 'nullable|string',

            'report_date' => [
                'nullable',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],
            'stop_date' => [
                'nullable',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],
            'punish' => 'nullable|string',
            'punish_date' => [
                'nullable',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],
            'remark' => 'nullable|string',
        ], [
            'trace_date.required'        => 'กรุณาระบุวันที่ติดตาม',
            'trace_date.date'            => 'รูปแบบวันที่ติดตามไม่ถูกต้อง',
            'trace_date.before_or_equal' => 'วันที่ติดตามต้องไม่เกินวันปัจจุบัน',

            'report_date.date'            => 'รูปแบบวันที่แจ้งความไม่ถูกต้อง',
            'report_date.before_or_equal' => 'วันที่แจ้งความต้องไม่เกินวันปัจจุบัน',

            'stop_date.date'            => 'รูปแบบวันที่ยุติการติดตามไม่ถูกต้อง',
            'stop_date.before_or_equal' => 'วันที่ยุติการติดตามต้องไม่เกินวันปัจจุบัน',

            'punish_date.date'            => 'รูปแบบวันที่ลงโทษไม่ถูกต้อง',
            'punish_date.before_or_equal' => 'วันที่ลงโทษต้องไม่เกินวันปัจจุบัน',
        ]);

        $newTraceDate = Carbon::parse($data['trace_date'])->startOfDay();

        $prevFollow = EscapeFollow::where('escape_id', $follow->escape_id)
            ->where('count', '<', $follow->count)
            ->orderByDesc('count')
            ->first();

        if ($prevFollow && $prevFollow->trace_date) {
            $prevTraceDate = Carbon::parse($prevFollow->trace_date)->startOfDay();

            if ($newTraceDate->lte($prevTraceDate)) {
                return redirect()->back()
                    ->withErrors([
                        'trace_date' => 'วันที่ติดตามของครั้งที่ ' . $follow->count . ' ต้องมากกว่าวันที่ของครั้งที่ ' . $prevFollow->count . ' เท่านั้น'
                    ])
                    ->withInput();
            }
        }

        $nextFollow = EscapeFollow::where('escape_id', $follow->escape_id)
            ->where('count', '>', $follow->count)
            ->orderBy('count', 'asc')
            ->first();

        if ($nextFollow && $nextFollow->trace_date) {
            $nextTraceDate = Carbon::parse($nextFollow->trace_date)->startOfDay();

            if ($newTraceDate->gte($nextTraceDate)) {
                return redirect()->back()
                    ->withErrors([
                        'trace_date' => 'วันที่ติดตามของครั้งที่ ' . $follow->count . ' ต้องน้อยกว่าวันที่ของครั้งที่ ' . $nextFollow->count . ' เท่านั้น'
                    ])
                    ->withInput();
            }
        }

        $data['count'] = $follow->count;

        $follow->update($data);

        CaseActivity::where('client_id', $escape->client_id)
            ->where('module', 'escape')
            ->delete();

        CaseActivity::record([
            'client_id'   => $escape->client_id,
            'module'      => 'escape',
            'type'        => 'warning',
            'title'       => 'แก้ไขการติดตามการออก/หลบหนี',
            'description' => 'ติดตามครั้งที่ ' . ($follow->count ?? '-') .
                            ' | วันที่ติดตาม: ' . ($data['trace_date'] ?? '-') .
                            ' | รายละเอียด: ' . ($data['detail'] ?? '-'),
            'occurred_at' => now(),
            'icon'        => 'bi-person-check',
            'url'         => route('escape.edit', $escape->id),
        ]);

        return redirect()
            ->route('escape.edit', $follow->escape_id)
            ->with([
                'message' => 'อัปเดตข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success'
            ]);
    }

    // ลบการติดตาม
        public function DeleteFollow($id)
        {
            $follow = EscapeFollow::whereHas('escape.client', function ($q) {
                    $q->forUser(auth()->user());
                })
                ->findOrFail($id);

            $escape = $follow->escape;

            Client::forUser(auth()->user())->findOrFail($escape->client_id);

            $escape_id = $follow->escape_id;

            CaseActivity::where('client_id', $escape->client_id)
                ->where('module', 'escape')
                ->delete();

            $follow->delete();

            $remainingFollows = EscapeFollow::where('escape_id', $escape_id)
                ->orderBy('count', 'asc')
                ->orderBy('trace_date', 'asc')
                ->get();

            foreach ($remainingFollows as $index => $item) {
                $item->update([
                    'count' => $index + 1
                ]);
            }

            return redirect()
                ->route('escape.edit', $escape_id)
                ->with([
                    'message' => 'ลบข้อมูลเรียบร้อยแล้ว',
                    'alert-type' => 'success'
                ]);
        }
    }