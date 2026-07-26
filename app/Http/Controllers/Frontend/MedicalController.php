<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Medical;
use Illuminate\Support\Facades\Validator; // ✅ ใช้ namespace ที่ถูกต้อง
use Illuminate\Validation\Rule;
use App\Models\CaseActivity;

class MedicalController extends Controller
{
    // แสดงฟอร์มเพิ่มข้อมูลใหม่
    public function MedicalAdd(Request $request, $client_id)
{
    // =========================
    // PATCH: กันเดา URL เข้าถึง client ที่ไม่มีสิทธิ์
    // =========================
    $client = Client::forUser(auth()->user())->findOrFail($client_id);

    $request->validate([
        'start_date' => ['nullable', 'date'],
        'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
    ], [
        'end_date.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
    ]);

    $query = Medical::where('client_id', $client->id)
        ->orderByDesc('medical_date')
        ->orderByDesc('id');

    if ($request->filled('start_date')) {
        $query->whereDate('medical_date', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('medical_date', '<=', $request->end_date);
    }

    $medicals = $query->get();

    return view('frontend.client.medical.medical_create', compact('client', 'client_id', 'medicals'));
}

    // บันทึกข้อมูลใหม่
  public function MedicalStore(Request $request)
        {
            $data = $request->validate([
                'medical_date' => [
                    'required',
                    'date',
                    Rule::unique('medicals')->where(function ($query) use ($request) {
                        return $query->where('client_id', $request->client_id);
                    }),
                ],
                'disease_name' => 'required|string|max:255',
                'illness'      => 'required|string',
                'treatment'    => 'nullable|string',
                'refer'        => 'required|in:พบแพทย์,ไม่พบแพทย์',
                'diagnosis'    => 'nullable|string',
                'appt_date'    => 'nullable|date',
                'teacher'      => 'nullable|string|max:255',
                'remark'       => 'nullable|string',
                'client_id'    => 'required|exists:clients,id',
            ], [
                'medical_date.required' => 'กรุณาระบุวันที่รักษา',
                'medical_date.date'     => 'วันที่รักษาไม่ถูกต้อง',
                'medical_date.unique'   => 'มีการบันทึกวันที่รักษานี้แล้ว',
                'disease_name.required' => 'กรุณาระบุชื่อโรค',
                'illness.required'      => 'กรุณาระบุอาการเจ็บป่วย',
                'refer.required'        => 'กรุณาเลือกการส่งต่อ',
            ]);

            // =========================
            // PATCH: กันยิง request เปลี่ยน client_id
            // =========================
            $client = Client::forUser(auth()->user())->findOrFail($data['client_id']);

            // =========================
            // PATCH: บังคับ client_id จากสิทธิ์ที่ตรวจแล้ว
            // =========================
            $data['client_id'] = $client->id;

            if ($data['refer'] === 'ไม่พบแพทย์') {
                $data['diagnosis'] = null;
                $data['appt_date'] = null;
            }

            $medical = Medical::create($data);

           CaseActivity::where('client_id', $client->id)
                ->where('module', 'medical')
                ->delete();

            CaseActivity::record([
                'client_id'   => $client->id,
                'module'      => 'medical',
                'type'        => 'success',
                'title'       => 'บันทึกการรักษาพยาบาล',
                'description' => 'วันที่รักษา: ' . ($data['medical_date'] ?? '-') .
                                ' | โรค/อาการ: ' . ($data['disease_name'] ?? '-') .
                                ' | การส่งต่อ: ' . ($data['refer'] ?? '-'),
                'occurred_at' => $data['medical_date'] ?? now('Asia/Bangkok'),
                'icon'        => 'bi-heart-pulse',
                'url'         => route('medical.add', $client->id),
            ]);

            $notification = [
                'message' => 'บันทึกข้อมูลเรียบร้อย',
                'alert-type' => 'success'
            ];

            return redirect()->back()->with($notification);
        }

    // โหลดข้อมูลสำหรับแก้ไข (JSON)
    public function editMedicalJson($id)
{
    // =========================
    // PATCH: กันเดา URL เรียก JSON ของ client คนอื่นตั้งแต่ query แรก
    // เดิม: $medical = Medical::findOrFail($id);
    // =========================
    $medical = Medical::whereHas('client', function ($q) {
            $q->forUser(auth()->user());
        })
        ->findOrFail($id);

    // =========================
    // PATCH: กันเดา URL เรียก JSON ของ client คนอื่น
    // =========================
    Client::forUser(auth()->user())->findOrFail($medical->client_id);

    return response()->json([
        'id'           => $medical->id,
        'medical_date' => $medical->medical_date ? \Carbon\Carbon::parse($medical->medical_date)->format('Y-m-d') : null,
        'disease_name' => $medical->disease_name,
        'illness'      => $medical->illness,
        'treatment'    => $medical->treatment,
        'refer'        => $medical->refer,
        'diagnosis'    => $medical->diagnosis,
        'appt_date'    => $medical->appt_date ? \Carbon\Carbon::parse($medical->appt_date)->format('Y-m-d') : null,
        'teacher'      => $medical->teacher,
        'remark'       => $medical->remark,
        'client_id'    => $medical->client_id,
    ]);
}

// ✅ อัปเดตข้อมูล
public function MedicalUpdate(Request $request, $id)
{
   // =========================
   // PATCH: กันเดา URL มา update record คนอื่นตั้งแต่ query แรก
   // เดิม: $medical = Medical::findOrFail($id);
   // =========================
   $medical = Medical::whereHas('client', function ($q) {
        $q->forUser(auth()->user());
    })
    ->findOrFail($id);

    // =========================
    // PATCH: กันเดา URL มา update record คนอื่น
    // =========================
    Client::forUser(auth()->user())->findOrFail($medical->client_id);

    $validator = Validator::make($request->all(), [
        'medical_date' => [
            'required',
            'date',
            Rule::unique('medicals')
                ->where(function ($query) use ($medical) {
                    return $query->where('client_id', $medical->client_id);
                })
                ->ignore($medical->id), // ✅ ยกเว้น record ที่กำลังแก้ไข
        ],
        'disease_name' => 'required|string|max:255',
        'illness'      => 'required|string',
        'treatment'    => 'nullable|string',
        'refer'        => 'required|in:พบแพทย์,ไม่พบแพทย์',
        'diagnosis'    => 'nullable|string',
        'appt_date'    => 'nullable|date',
        'teacher'      => 'nullable|string|max:255',
        'remark'       => 'nullable|string',
        'client_id'    => 'required|exists:clients,id',
    ], [
        'medical_date.required' => 'กรุณาระบุวันที่รักษา',
        'medical_date.date'     => 'วันที่รักษาไม่ถูกต้อง',
        'medical_date.unique'   => 'มีการบันทึกวันที่รักษานี้แล้ว',
        'disease_name.required' => 'ชื่อโรคต้องไม่เป็นค่าว่าง',
        'illness.required'      => 'อาการเจ็บป่วยต้องไม่เป็นค่าว่าง',
        'refer.required'        => 'กรุณาเลือกการส่งต่อ',
    ]);

    if ($validator->fails()) {
        return back()
            ->withErrors($validator)
            ->withInput()
            ->with([
                'edit_mode' => true,
                'edit_id'   => $id,
            ]);
    }

    $data = $validator->validated();

    // =========================
    // PATCH: กันเปลี่ยน client_id ไป client อื่น
    // บังคับให้ใช้ client_id เดิมของ record นี้เท่านั้น
    // =========================
    $data['client_id'] = $medical->client_id;

    // =========================
    // PATCH: ตรวจสิทธิ์ client_id หลังบังคับค่าแล้ว
    // =========================
    Client::forUser(auth()->user())->findOrFail($data['client_id']);

    if ($data['refer'] === 'ไม่พบแพทย์') {
        $data['diagnosis'] = null;
        $data['appt_date'] = null;
    }

    $medical->update($data);

    CaseActivity::where('client_id', $medical->client_id)
    ->where('module', 'medical')
    ->delete();

    CaseActivity::record([
        'client_id'   => $medical->client_id,
        'module'      => 'medical',
        'type'        => 'success',
        'title'       => 'แก้ไขการรักษาพยาบาล',
        'description' => 'วันที่รักษา: ' . ($data['medical_date'] ?? '-') .
                        ' | โรค/อาการ: ' . ($data['disease_name'] ?? '-') .
                        ' | การส่งต่อ: ' . ($data['refer'] ?? '-'),
        'occurred_at' => $data['medical_date'] ?? now('Asia/Bangkok'),
        'icon'        => 'bi-heart-pulse',
        'url'         => route('medical.add', $medical->client_id),
    ]);

     $notification = [
            'message' => 'อัปเดตข้อมูลเรียบร้อย',
            'alert-type' => 'success'
        ];

    return redirect()
        ->route('medical.add', $medical->client_id)
        ->with($notification);
}
    // ลบข้อมูล
    public function MedicalDelete($id)
    {
      // =========================
      // PATCH: กันเดา URL มาลบข้อมูลของ client คนอื่นตั้งแต่ query แรก
      // เดิม: $medical = Medical::findOrFail($id);
      // =========================
      $medical = Medical::whereHas('client', function ($q) {
        $q->forUser(auth()->user());
    })
    ->findOrFail($id);

        // =========================
        // PATCH: กันเดา URL มาลบข้อมูลของ client คนอื่น
        // =========================
        Client::forUser(auth()->user())->findOrFail($medical->client_id);

        $clientId  = $medical->client_id;

        CaseActivity::where('client_id', $clientId)
        ->where('module', 'medical')
        ->delete();
        
        $medical->delete();

        return redirect()->route('medical.add', $clientId)
                         ->with(['message' => 'ลบข้อมูลเรียบร้อยแล้ว', 'alert-type' => 'success']);
    }

    // ✅ หน้ารายงานการรักษาพยาบาล
        public function MedicalReport(Request $request, $client_id)
        {
            // =========================
            // PATCH: กันเดา URL เข้าถึง client ที่ไม่มีสิทธิ์
            // =========================
            $client = Client::forUser(auth()->user())->findOrFail($client_id);

            $request->validate([
                'start_date' => ['nullable', 'date'],
                'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
            ], [
                'end_date.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
            ]);

            $query = Medical::where('client_id', $client->id)
                ->orderByDesc('medical_date')
                ->orderByDesc('id');

            if ($request->filled('start_date')) {
                $query->whereDate('medical_date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('medical_date', '<=', $request->end_date);
            }

            $medicals = $query->get();

            return view('frontend.client.medical.report', compact(
                'client',
                'medicals'
            ));
        }
}