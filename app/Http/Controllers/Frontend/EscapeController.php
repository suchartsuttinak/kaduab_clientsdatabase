<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Escape;
use App\Models\Retire;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\CaseActivity;

class EscapeController extends Controller
{
    // หน้า IndexEscape (แสดง Escape ทั้งหมด)
    public function IndexEscape($client_id)
{
    // =========================
    // PATCH: กันเดา URL เข้า client
    // =========================
    $client = Client::forUser(auth()->user())->findOrFail($client_id);

    $escapes = Escape::with(['retire','follows'])
                     ->where('client_id', $client->id) // PATCH: ใช้ client ที่ผ่านสิทธิ์แล้ว
                     ->get();
    $retires = Retire::all();

    return view('frontend.client.escape.escape_index', compact('client','escapes','retires'));
}

    // หน้า AddEscape (ฟอร์มเพิ่มใหม่)
    public function AddEscape($client_id)
    {
        // =========================
        // PATCH: กันเดา URL เข้า client
        // =========================
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $retires = Retire::all();
        $mode = 'create';

        return view('frontend.client.escape.escape_create', compact('client','retires','mode'));
    }

    // บันทึก Escape ใหม่
   public function StoreEscape(Request $request)
        {
            $data = $request->validate([
                'client_id'   => 'required|exists:clients,id',
               'retire_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),

                Rule::unique('escapes')->where(function ($query) use ($request) {
                    return $query->where('client_id', $request->client_id);
                }),
            ],
                'retire_id'   => 'required|exists:retires,id',
                'stories'     => 'nullable|string',
            ], [
                'client_id.required'   => 'กรุณาเลือกผู้รับบริการ',
                'client_id.exists'     => 'รหัสผู้รับบริการไม่ถูกต้อง',
                 'retire_date.before_or_equal' => 'วันที่ออกต้องไม่เกินวันปัจจุบัน',
                'retire_date.required' => 'กรุณาระบุวันที่ออก',
                'retire_date.date'     => 'รูปแบบวันที่ไม่ถูกต้อง',
                'retire_date.unique'   => 'วันที่ออกนี้ถูกบันทึกแล้วสำหรับผู้รับบริการรายนี้',
                'retire_id.required'   => 'กรุณาเลือกประเภทการออก/หลบหนี',
                'retire_id.exists'     => 'รหัสการออก/หลบหนีไม่ถูกต้อง',
                'stories.string'       => 'เรื่องราวต้องเป็นข้อความ',
            ]);

            $client = Client::forUser(auth()->user())->findOrFail($data['client_id']);
            $data['client_id'] = $client->id;

            $escape = Escape::create($data);

            CaseActivity::where('client_id', $client->id)
                ->where('module', 'escape')
                ->delete();

            CaseActivity::record([
                'client_id'   => $client->id,
                'module'      => 'escape',
                'type'        => 'danger',
                'title'       => 'บันทึกการออก/หลบหนีจากที่พักพิง',
                'description' => 'วันที่ออก/หลบหนี: ' . ($data['retire_date'] ?? '-') .
                                ' | รายละเอียด: ' . ($data['stories'] ?? '-'),
                'occurred_at' => now(),
                'icon'        => 'bi-box-arrow-right',
                'url'         => route('escape.edit', $escape->id),
            ]);

            return redirect()
                ->route('escape.edit', $escape->id)
                ->with(['message' => 'บันทึกข้อมูลการออกเรียบร้อย', 'alert-type' => 'success']);
        }

    // หน้า EditEscape (ฟอร์มแก้ไข)
    public function EditEscape($id)
    {
        // =========================
        // PATCH: กันเข้าดู record คนอื่นตั้งแต่ query แรก
        // เดิม: $escape = Escape::with(['client','retire','follows'])->findOrFail($id);
        // =========================
        $escape = Escape::with(['client','retire','follows'])
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        // =========================
        // PATCH: ใช้ client จาก record ที่ผ่านสิทธิ์แล้ว
        // เดิม: $client = Client::forUser(auth()->user())->findOrFail($escape->client_id);
        // =========================
        $client = $escape->client;

        $retires = Retire::all();

        return view('frontend.client.escape.escape_edit', compact('escape','retires','client'));
    }

    // อัปเดต Escape ที่มีอยู่แล้ว
    public function UpdateEscape(Request $request, $id)
    {
      // =========================
      // PATCH: กัน update record คนอื่นตั้งแต่ query แรก
      // เดิม: $escape = Escape::findOrFail($id);
      // =========================
      $escape = Escape::whereHas('client', function ($q) {
            $q->forUser(auth()->user());
        })
        ->findOrFail($id);

        // =========================
        // PATCH: กันเปลี่ยน client_id จาก hidden field
        // บังคับให้ client_id เป็นของ record เดิมเสมอ
        // =========================
        $request->merge([
            'client_id' => $escape->client_id,
        ]);

        // =========================
        // PATCH: กัน update record คนอื่น
        // =========================
        Client::forUser(auth()->user())->findOrFail($escape->client_id);

        $data = $request->validate([
            'retire_date' => [
                'required',
                'date',
                Rule::unique('escapes')->where(function ($query) use ($request) {
                    return $query->where('client_id', $request->client_id);
                })->ignore($id),
            ],
            'retire_id'   => 'required|exists:retires,id',
            'stories'     => 'nullable|string',
            'client_id'   => 'required|exists:clients,id',
        ], [
            'retire_date.required' => 'กรุณาระบุวันที่เกษียณ',
            'retire_date.date'     => 'รูปแบบวันที่ไม่ถูกต้อง',
            'retire_date.unique'   => 'วันที่เกษียณนี้ถูกบันทึกแล้วสำหรับผู้รับบริการรายนี้',
            'retire_id.required'   => 'กรุณาเลือกประเภทการเกษียณ',
            'retire_id.exists'     => 'รหัสการเกษียณไม่ถูกต้อง',
            'stories.string'       => 'เรื่องราวต้องเป็นข้อความ',
            'client_id.required'   => 'กรุณาเลือกผู้รับบริการ',
            'client_id.exists'     => 'รหัสผู้รับบริการไม่ถูกต้อง',
        ]);

        // =========================
        // PATCH: กันเปลี่ยน client_id
        // =========================
        Client::forUser(auth()->user())->findOrFail($data['client_id']);

        $escape->update($data);

        CaseActivity::where('client_id', $escape->client_id)
            ->where('module', 'escape')
            ->delete();

        CaseActivity::record([
            'client_id'   => $escape->client_id,
            'module'      => 'escape',
            'type'        => 'danger',
            'title'       => 'แก้ไขการออก/หลบหนีจากที่พักพิง',
            'description' => 'วันที่ออก/หลบหนี: ' . ($data['retire_date'] ?? '-') .
                            ' | รายละเอียด: ' . ($data['stories'] ?? '-'),
            'occurred_at' => now(),
            'icon'        => 'bi-box-arrow-right',
            'url'         => route('escape.edit', $escape->id),
        ]);

        return redirect()
            ->route('escape.edit', $escape->id)
            ->with(['message' => 'แก้ไขข้อมูลการออกเรียบร้อย', 'alert-type' => 'success']);
    }

    public function DeleteEscape($id)
{
        // =========================
        // PATCH: กันลบ record คนอื่นตั้งแต่ query แรก
        // เดิม: $escape = Escape::findOrFail($id);
        // =========================
        $escape = Escape::whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        // =========================
        // PATCH: กันลบ record คนอื่น
        // =========================
        Client::forUser(auth()->user())->findOrFail($escape->client_id);

        $client_id = $escape->client_id;

        CaseActivity::where('client_id', $client_id)
            ->where('module', 'escape')
            ->delete();

        $escape->delete();

        return redirect()
            ->route('escape.index', $client_id)
            ->with(['message' => 'ลบข้อมูลการออกเรียบร้อย', 'alert-type' => 'success']);
}

    public function CopyEscape($id)
    {
        // =========================
        // PATCH: กัน copy ข้อมูลของ client คนอื่นตั้งแต่ query แรก
        // เดิม: $escape = Escape::with(['client','retire'])->findOrFail($id);
        // =========================
        $escape = Escape::with(['client','retire'])
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        // =========================
        // PATCH: ใช้ client จาก record ที่ผ่านสิทธิ์แล้ว
        // เดิม: $client = Client::forUser(auth()->user())->findOrFail($escape->client_id);
        // =========================
        $client = $escape->client;

        $retires = Retire::all();
        $mode = 'copy';

        return view('frontend.client.escape.escape_create', compact('client','retires','escape','mode'));
    }

    public function ReportEscape($id)
{
    // =========================
    // PATCH: กันเข้าดู report ของ client คนอื่นตั้งแต่ query แรก
    // เดิม: Escape::with([...])->findOrFail($id);
    // =========================
    $escape = Escape::with([
        'client',
        'retire',
        'follows' => function ($query) {
            $query->orderBy('count', 'asc')->orderBy('trace_date', 'asc');
        }
    ])
    ->whereHas('client', function ($q) {
        $q->forUser(auth()->user());
    })
    ->findOrFail($id);

    // =========================
    // PATCH: ใช้ client จาก record ที่ผ่านสิทธิ์แล้ว
    // เดิม: $client = Client::forUser(auth()->user())->findOrFail($escape->client_id);
    // =========================
    $client = $escape->client;

    return view('frontend.client.escape.escape_report', compact('escape', 'client'));
}
}