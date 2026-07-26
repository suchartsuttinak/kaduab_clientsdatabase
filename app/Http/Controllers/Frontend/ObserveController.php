<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Misbehavior;
use App\Models\Observe;
use App\Models\ObserveFollowup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\CaseActivity;

class ObserveController extends Controller
{
    // หน้าเพิ่มใหม่
    public function AddObserve($client_id)
    {
        // =========================
        // PATCH: กันเดา URL เข้า client
        // =========================
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $misbehaviors = Misbehavior::all();
        $observes = Observe::with('followups')
            ->where('client_id', $client->id)
            ->orderBy('date', 'desc')
            ->get();
        $observe = null;

        return view('frontend.client.observe.observe_create', compact('client', 'client_id', 'misbehaviors', 'observes', 'observe'));
    }

    // บันทึกข้อมูลใหม่
    public function StoreObserve(Request $request)
        {
            $data = $request->validate([
               'date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),

                Rule::unique('observes')->where(function ($query) use ($request) {
                    return $query->where('client_id', $request->client_id);
                }),
            ],
                'behavior'       => 'required|string',
                'cause'          => 'required|string',
                'solution'       => 'required|string',
                'action'         => 'required|string',
                'obstacles'      => 'nullable|string',
                'result'         => 'required|string',

                'record_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],

                'recorder'       => 'nullable|string|max:100',
                'misbehavior_id' => 'required|integer',
                'client_id'      => 'required|integer',
            ], [
                'date.required'           => 'กรุณาระบุวันที่',
                'date.date'               => 'วันที่ไม่ถูกต้อง',
                'date.unique'             => 'วันที่นี้ถูกบันทึกแล้วสำหรับนักเรียนรายนี้',
                'behavior.required'       => 'กรุณาระบุพฤติกรรม',
                'cause.required'          => 'กรุณาระบุสาเหตุ',
                'solution.required'       => 'กรุณาระบุแนวทางแก้ไข',
                'action.required'         => 'กรุณาระบุการดำเนินการ',
                'result.required'         => 'กรุณาระบุผลการดำเนินการ',
                'record_date.required'    => 'กรุณาระบุวันที่บันทึก',
                'date.before_or_equal'        => 'วันที่เกิดเหตุต้องไม่เกินวันปัจจุบัน',
                'record_date.before_or_equal' => 'วันที่บันทึกต้องไม่เกินวันปัจจุบัน',
                'misbehavior_id.required' => 'กรุณาเลือกประเภทพฤติกรรมไม่เหมาะสม',
                'client_id.required'      => 'กรุณาเลือกนักเรียน',
            ]);

            // =========================
            // PATCH: กันเปลี่ยน client_id
            // =========================
            $client = Client::forUser(auth()->user())->findOrFail($data['client_id']);

            // =========================
            // PATCH: บังคับ client_id จากสิทธิ์ที่ตรวจแล้ว
            // =========================
            $data['client_id'] = $client->id;

            // =========================
            // PATCH: ผู้บันทึกใช้ชื่อ user ที่ login เท่านั้น
            // ไม่รับค่าจาก input เพื่อป้องกันการแก้ชื่อเอง
            // =========================
            $data['recorder'] = auth()->user()->name ?? null;

            $observe = Observe::create($data);

            CaseActivity::where('client_id', $client->id)
                ->where('module', 'observe')
                ->delete();

            CaseActivity::record([
                'client_id'   => $client->id,
                'module'      => 'observe',
                'type'        => 'warning',
                'title'       => 'บันทึกพฤติกรรมไม่เหมาะสม',
                'description' => 'วันที่บันทึก: ' . ($data['date'] ?? '-') .
                                ' | พฤติกรรม: ' . ($data['behavior'] ?? '-') .
                                ' | ผลการดำเนินการ: ' . ($data['result'] ?? '-'),
                'occurred_at' => now(),
                'icon'        => 'bi-exclamation-triangle',
                'url'         => route('observe.create', $client->id),
            ]);

            return redirect()->route('observe.create', $data['client_id'])
                ->with('success', 'บันทึกข้อมูลเรียบร้อย');
        }

    // หน้าแก้ไข
    public function EditObserve($id)
    {
        // =========================
        // PATCH: กันเข้าดู record คนอื่นตั้งแต่ query แรก
        // เดิม: $observe = Observe::with('followups')->findOrFail($id);
        // =========================
        $observe = Observe::with('followups')
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        // =========================
        // PATCH: กันเข้าดู record คนอื่น
        // =========================
        $client = Client::forUser(auth()->user())->findOrFail($observe->client_id);

        $misbehaviors = Misbehavior::all();
        $observes = Observe::with('followups')
            ->where('client_id', $client->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('frontend.client.observe.observe_create', compact('client', 'misbehaviors', 'observes', 'observe'));
    }

    // อัปเดตข้อมูล
   public function UpdateObserve(Request $request, $id)
{
    // =========================
    // PATCH: กัน update record คนอื่นตั้งแต่ query แรก
    // เดิม: $observe = Observe::findOrFail($id);
    // =========================
    $observe = Observe::whereHas('client', function ($q) {
            $q->forUser(auth()->user());
        })
        ->findOrFail($id);

    // =========================
    // PATCH: กัน update record คนอื่น
    // =========================
    Client::forUser(auth()->user())->findOrFail($observe->client_id);

    $data = $request->validate([
        'date' => [
        'required',
        'date',
        'before_or_equal:' . now('Asia/Bangkok')->toDateString(),

        Rule::unique('observes')
            ->where(function ($query) use ($observe) {
                return $query->where('client_id', $observe->client_id);
            })
            ->ignore($id),
    ],
        'behavior'       => 'required|string',
        'cause'          => 'required|string',
        'solution'       => 'required|string',
        'action'         => 'required|string',
        'result'         => 'required|string',

       'record_date' => [
        'required',
        'date',
        'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
    ],

        'recorder'       => 'nullable|string|max:100',
        'misbehavior_id' => 'required|integer',
        'client_id'      => 'required|integer',
    ],[
        'date.required'            => 'กรุณาระบุวันที่เกิดเหตุ',
        'date.date'                => 'วันที่เกิดเหตุไม่ถูกต้อง',
        'date.before_or_equal'     => 'วันที่เกิดเหตุต้องไม่เกินวันปัจจุบัน',
        'date.unique'              => 'วันที่นี้ถูกบันทึกแล้วสำหรับนักเรียนรายนี้',

        'record_date.required'        => 'กรุณาระบุวันที่บันทึก',
        'record_date.date'            => 'วันที่บันทึกไม่ถูกต้อง',
        'record_date.before_or_equal' => 'วันที่บันทึกต้องไม่เกินวันปัจจุบัน',
    ]);

    // =========================
    // PATCH: กันเปลี่ยน client_id
    // บังคับให้ใช้ client_id เดิมของ record นี้เท่านั้น
    // =========================
    $data['client_id'] = $observe->client_id;

    // =========================
    // PATCH: ตรวจสิทธิ์ client_id หลังบังคับค่าแล้ว
    // =========================
    Client::forUser(auth()->user())->findOrFail($data['client_id']);

    // =========================
    // PATCH: ผู้บันทึกใช้ชื่อ user ที่ login เท่านั้น
    // ไม่รับค่าจาก input เพื่อป้องกันการแก้ชื่อเอง
    // =========================
    $data['recorder'] = auth()->user()->name ?? null;

    $observe->update($data);

    CaseActivity::where('client_id', $observe->client_id)
    ->where('module', 'observe')
    ->delete();

    CaseActivity::record([
        'client_id'   => $observe->client_id,
        'module'      => 'observe',
        'type'        => 'warning',
        'title'       => 'แก้ไขพฤติกรรมไม่เหมาะสม',
        'description' => 'วันที่บันทึก: ' . ($data['date'] ?? '-') .
                        ' | พฤติกรรม: ' . ($data['behavior'] ?? '-') .
                        ' | ผลการดำเนินการ: ' . ($data['result'] ?? '-'),
        'occurred_at' => now(),
        'icon'        => 'bi-exclamation-triangle',
        'url'         => route('observe.create', $observe->client_id),
    ]);

    return redirect()->route('observe.create', $data['client_id'])
        ->with('success', 'อัปเดตข้อมูลเรียบร้อย');
}

    // ลบข้อมูล
    public function DeleteObserve($id)
    {
        // =========================
        // PATCH: กันลบ record คนอื่นตั้งแต่ query แรก
        // เดิม: $observe = Observe::findOrFail($id);
        // =========================
        $observe = Observe::whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        // =========================
        // PATCH: กันลบ record คนอื่น
        // =========================
        Client::forUser(auth()->user())->findOrFail($observe->client_id);

        $client_id = $observe->client_id;

        CaseActivity::where('client_id', $client_id)
            ->where('module', 'observe')
            ->delete();

        $observe->delete();

        return redirect()->route('observe.create', $client_id)
            ->with(['message' => 'ลบข้อมูลเรียบร้อย', 'alert-type' => 'success']);
    }

    // บันทึกการติดตามผล
    public function StoreFollowup(Request $request)
    {
        $data = $request->validate([
            'observe_id'      => 'required|integer|exists:observes,id',
            'followup_date'   => 'required|date',
            'followup_action' => 'nullable|string',
            'followup_result' => 'nullable|string',
        ], [
            'observe_id.required'     => 'ไม่พบข้อมูลพฤติกรรมที่ต้องการติดตามผล',
            'observe_id.integer'      => 'ข้อมูลพฤติกรรมไม่ถูกต้อง',
            'observe_id.exists'       => 'ไม่พบข้อมูลพฤติกรรมในระบบ',
            'followup_date.required'  => 'กรุณาระบุวันที่ติดตาม',
            'followup_date.date'      => 'รูปแบบวันที่ติดตามไม่ถูกต้อง',
        ]);

        // =========================
        // PATCH: กันเพิ่ม followup ของ client คนอื่นตั้งแต่ query แรก
        // เดิม: $observe = Observe::findOrFail($data['observe_id']);
        // =========================
        $observe = Observe::whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($data['observe_id']);

        // =========================
        // PATCH: กันเพิ่ม followup ของ client คนอื่น
        // =========================
        Client::forUser(auth()->user())->findOrFail($observe->client_id);

        // =========================
        // AUTO: นับครั้งอัตโนมัติจากจำนวนปัจจุบัน + 1
        // =========================
        $nextFollowupCount = ObserveFollowup::where('observe_id', $observe->id)->count() + 1;

        // =========================
        // VALIDATE: วันที่ของครั้งใหม่ต้อง "มากกว่า" วันที่ของครั้งล่าสุดเท่านั้น
        // ห้ามซ้ำ และห้ามน้อยกว่า
        // =========================
        $lastFollowup = ObserveFollowup::where('observe_id', $observe->id)
            ->orderByDesc('followup_count')
            ->first();

        if ($lastFollowup && $lastFollowup->followup_date) {
            $newFollowupDate  = Carbon::parse($data['followup_date'])->startOfDay();
            $lastFollowupDate = Carbon::parse($lastFollowup->followup_date)->startOfDay();

            if ($newFollowupDate->lte($lastFollowupDate)) {
                return redirect()->back()
                    ->withErrors([
                        'followup_date' => 'วันที่ติดตามของครั้งที่ ' . $nextFollowupCount . ' ต้องมากกว่าวันที่ของครั้งที่ ' . $lastFollowup->followup_count . ' เท่านั้น และห้ามซ้ำ'
                    ])
                    ->withInput();
            }
        }

        $followup = ObserveFollowup::create([
            'observe_id'      => $observe->id,
            'followup_date'   => $data['followup_date'],
            'followup_count'  => $nextFollowupCount,
            'followup_action' => $data['followup_action'] ?? null,
            'followup_result' => $data['followup_result'] ?? null,
        ]);

        CaseActivity::where('client_id', $observe->client_id)
            ->where('module', 'observe')
            ->delete();

        CaseActivity::record([
            'client_id'   => $observe->client_id,
            'module'      => 'observe',
            'type'        => 'warning',
            'title'       => 'บันทึกการติดตามพฤติกรรม',
            'description' => 'ติดตามครั้งที่ ' . ($followup->followup_count ?? '-') .
                            ' | วันที่ติดตาม: ' . ($data['followup_date'] ?? '-') .
                            ' | ผลติดตาม: ' . ($data['followup_result'] ?? '-'),
            'occurred_at' => now(),
            'icon'        => 'bi-clipboard2-check',
            'url'         => route('observe.edit', $observe->id),
        ]);

        return redirect()
            ->route('observe.edit', $observe->id)
            ->with('success', 'บันทึกการติดตามผลเรียบร้อย');
    }

    // ลบการติดตามผล
        public function DeleteFollowup($id)
    {
        $followup = ObserveFollowup::whereHas('observeRelation.client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        $observe = $followup->observeRelation;

        if (!$observe) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลพฤติกรรมที่สัมพันธ์กับการติดตามผลนี้');
        }

        Client::forUser(auth()->user())->findOrFail($observe->client_id);

        $observe_id = $followup->observe_id;
        $clientId = $observe->client_id;

        CaseActivity::where('client_id', $clientId)
            ->where('module', 'observe')
            ->delete();

        $followup->delete();

        $remainingFollowups = ObserveFollowup::where('observe_id', $observe_id)
            ->orderBy('followup_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($remainingFollowups as $index => $item) {
            $item->update([
                'followup_count' => $index + 1
            ]);
        }

        return redirect()->route('observe.edit', $observe_id)
            ->with(['message' => 'ลบข้อมูลเรียบร้อย', 'alert-type' => 'success']);
    }
        // แก้ไขการติดตามผล
    public function EditFollowup($id)
    {
        $followup = ObserveFollowup::whereHas('observeRelation.client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        $observe = $followup->observeRelation;

        if (!$observe) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลพฤติกรรมที่สัมพันธ์กับการติดตามผลนี้');
        }

        $client = Client::forUser(auth()->user())->findOrFail($observe->client_id);

        $misbehaviors = Misbehavior::all();
        $observes = Observe::with('followups')
            ->where('client_id', $client->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('frontend.client.observe.observe_create', compact(
            'client',
            'misbehaviors',
            'observes',
            'observe',
            'followup'
        ));
    }

        // อัปเดตการติดตามผล
        public function UpdateFollowup(Request $request, $id)
    {
        $followup = ObserveFollowup::whereHas('observeRelation.client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        $observe = $followup->observeRelation;

        if (!$observe) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลพฤติกรรมที่สัมพันธ์กับการติดตามผลนี้');
        }

        Client::forUser(auth()->user())->findOrFail($observe->client_id);

        $data = $request->validate([
            'followup_date'   => 'required|date',
            'followup_action' => 'nullable|string',
            'followup_result' => 'nullable|string',
        ], [
            'followup_date.required' => 'กรุณาระบุวันที่ติดตาม',
            'followup_date.date'     => 'รูปแบบวันที่ติดตามไม่ถูกต้อง',
        ]);

        $newFollowupDate = Carbon::parse($data['followup_date'])->startOfDay();

        $prevFollowup = ObserveFollowup::where('observe_id', $followup->observe_id)
            ->where('followup_count', '<', $followup->followup_count)
            ->orderByDesc('followup_count')
            ->first();

        if ($prevFollowup && $prevFollowup->followup_date) {
            $prevFollowupDate = Carbon::parse($prevFollowup->followup_date)->startOfDay();

            if ($newFollowupDate->lte($prevFollowupDate)) {
                return redirect()->back()
                    ->withErrors([
                        'followup_date' => 'วันที่ติดตามของครั้งที่ ' . $followup->followup_count . ' ต้องมากกว่าวันที่ของครั้งที่ ' . $prevFollowup->followup_count . ' เท่านั้น และห้ามซ้ำ'
                    ])
                    ->withInput();
            }
        }

        $nextFollowup = ObserveFollowup::where('observe_id', $followup->observe_id)
            ->where('followup_count', '>', $followup->followup_count)
            ->orderBy('followup_count', 'asc')
            ->first();

        if ($nextFollowup && $nextFollowup->followup_date) {
            $nextFollowupDate = Carbon::parse($nextFollowup->followup_date)->startOfDay();

            if ($newFollowupDate->gte($nextFollowupDate)) {
                return redirect()->back()
                    ->withErrors([
                        'followup_date' => 'วันที่ติดตามของครั้งที่ ' . $followup->followup_count . ' ต้องน้อยกว่าวันที่ของครั้งที่ ' . $nextFollowup->followup_count . ' เท่านั้น และห้ามซ้ำ'
                    ])
                    ->withInput();
            }
        }

        $followup->update([
            'followup_date'   => $data['followup_date'],
            'followup_count'  => $followup->followup_count,
            'followup_action' => $data['followup_action'] ?? null,
            'followup_result' => $data['followup_result'] ?? null,
        ]);

        CaseActivity::where('client_id', $observe->client_id)
            ->where('module', 'observe')
            ->delete();

        CaseActivity::record([
            'client_id'   => $observe->client_id,
            'module'      => 'observe',
            'type'        => 'warning',
            'title'       => 'แก้ไขการติดตามพฤติกรรม',
            'description' => 'ติดตามครั้งที่ ' . ($followup->followup_count ?? '-') .
                            ' | วันที่ติดตาม: ' . ($data['followup_date'] ?? '-') .
                            ' | ผลติดตาม: ' . ($data['followup_result'] ?? '-'),
            'occurred_at' => now(),
            'icon'        => 'bi-clipboard2-check',
            'url'         => route('observe.edit', $observe->id),
        ]);

        return redirect()->route('observe.edit', $observe->id)
            ->with('success', 'อัปเดตการติดตามผลเรียบร้อย');
    }
        public function ReportObserve($id)
        {
            // =========================
            // PATCH: กันเข้าดู report ของ client คนอื่นตั้งแต่ query แรก
            // เดิม: Observe::with([...])->findOrFail($id);
            // =========================
            $observe = Observe::with([
                'client',
                'misbehavior',
                'followups' => function ($query) {
                    $query->orderBy('followup_count', 'asc')
                        ->orderBy('followup_date', 'asc');
                }
            ])
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

            // =========================
            // PATCH: กันเข้าดู report ของ client คนอื่น
            // =========================
            $client = Client::forUser(auth()->user())->findOrFail($observe->client_id);

            return view('frontend.client.observe.observe_report', compact('observe', 'client'));
        }
    }