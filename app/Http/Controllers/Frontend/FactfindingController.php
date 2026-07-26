<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Client;
use App\Models\Document;
use App\Models\Factfinding;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FactFindingDocument;
use App\Models\Marital;
use App\Models\CaseActivity;

class FactfindingController extends Controller
{
    public function FactfindingAdd($client_id)
    {
        // ✅ ตรวจสิทธิ์ client ผ่าน Client::forUser(auth()->user())
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        // ✅ ตรวจ factfinding โดยอิง client ที่ user มีสิทธิ์เท่านั้น
        $factFinding = Factfinding::where('client_id', $client->id)->first();

        if ($factFinding) {
            return redirect()->route('factfinding.edit', $factFinding->id)
                             ->with('info', 'มีข้อมูลสอบข้อเท็จจริงอยู่แล้ว จึงเข้าสู่หน้าแก้ไข');
        }

        $maritals = Marital::all();
        $documents = Document::all();

        return view('frontend.client.factfinding.factfinding_add', compact('client', 'documents', 'maritals'))
            ->with('info', 'เพิ่มข้อมูลสอบข้อเท็จจริง');
    }

    public function FactfindingStore(Request $request)
        {
            $validated = $request->validate([
                'client_id'   => 'required|integer',

               'date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],

                'receive_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],

                'fact_name'   => 'required|string|max:255',

                'appearance'  => 'nullable|string',
                'skin'        => 'nullable|string',
                'scar'        => 'nullable|string',
                'disability'  => 'nullable|string',
                'evidence'    => 'nullable|string',
                'sick'        => 'required|in:0,1',
                'sick_detail' => 'required_if:sick,1|nullable|string',
                'treatment'   => 'nullable|string',
                'hospital'    => 'nullable|string',
                'weight'      => 'nullable|numeric|max:500',
                'height'      => 'nullable|numeric|max:300',
                'blood_group' => 'nullable|string',
                'hygiene'     => 'nullable|string',
                'oral_health' => 'nullable|string',
                'injury'      => 'nullable|string',
                'marital_id'  => 'required|integer',
                'relation_parent' => 'nullable|string',
                'relation_family' => 'nullable|string',
                'relation_child'  => 'nullable|string',
                'ex_conditions'   => 'nullable|string',
                'in_conditions'   => 'nullable|string',
                'environment'     => 'nullable|string',
                'cause_problem'   => 'nullable|string',
                'need'            => 'nullable|string',
                'information'     => 'nullable|string',
                'diagnosis'       => 'nullable|string',

                'case_history'    => 'nullable|string',
                'active'          => 'nullable|boolean',

                'documents'   => 'nullable|array',
                'documents.*' => 'integer',
            ], [
                'client_id.required' => 'กรุณาเลือกผู้รับบริการ',
                'client_id.integer'  => 'รหัสผู้รับบริการต้องเป็นตัวเลข',

                'date.required'      => 'กรุณากรอกวันที่นำส่ง',
                'date.date'          => 'รูปแบบวันที่ไม่ถูกต้อง',

                'receive_date.required' => 'กรุณากรอกวันที่บันทึก',
                'receive_date.date' => 'รูปแบบวันที่บันทึกไม่ถูกต้อง',
                'receive_date.before_or_equal' => 'วันที่บันทึกต้องไม่เกินวันที่ปัจจุบัน',

                'fact_name.required' => 'กรุณากรอกชื่อผู้นำส่ง',
                'fact_name.string'   => 'ชื่อข้อเท็จจริงต้องเป็นข้อความ',
                'fact_name.max'      => 'ชื่อข้อเท็จจริงต้องไม่เกิน 255 ตัวอักษร',

                'sick.required'      => 'กรุณาระบุสถานะการเจ็บป่วย',
                'sick.in'            => 'ค่าที่ระบุไม่ถูกต้อง (ต้องเป็น 0 หรือ 1)',
                'sick_detail.required_if' => 'กรุณากรอกรายละเอียดการเจ็บป่วย',
                'sick_detail.string'      => 'รายละเอียดการเจ็บป่วยต้องเป็นข้อความ',

                'marital_id.required' => 'กรุณาเลือกสถานภาพสมรส',
                'marital_id.integer'  => 'สถานภาพสมรสต้องเป็นตัวเลข',

                'weight.numeric' => 'น้ำหนักต้องเป็นตัวเลขเท่านั้น',
                'weight.min'     => 'น้ำหนักต้องไม่น้อยกว่า 1 กิโลกรัม',
                'weight.max'     => 'น้ำหนักต้องไม่เกิน 500 กิโลกรัม',

                'height.numeric' => 'ส่วนสูงต้องเป็นตัวเลขเท่านั้น',
                'height.min'     => 'ส่วนสูงต้องไม่น้อยกว่า 30 เซนติเมตร',
                'height.max'     => 'ส่วนสูงต้องไม่เกิน 300 เซนติเมตร',

                'documents.array'    => 'เอกสารต้องอยู่ในรูปแบบรายการ',
                'documents.*.integer'=> 'รหัสเอกสารต้องเป็นตัวเลข',
            ]);

            $documents = $validated['documents'] ?? [];
            unset($validated['documents']);

            $client = Client::forUser(auth()->user())->findOrFail($validated['client_id']);

            $existing = Factfinding::where('client_id', $client->id)->first();
            if ($existing) {
                return redirect()
                    ->route('factfinding.edit', $existing->id)
                    ->with('error', 'มีข้อมูลของผู้รับรายนี้อยู่แล้ว ท่านสามารถแก้ไขแทนการบันทึกใหม่ได้');
            }

            $validated['client_id'] = $client->id;

            if (($validated['sick'] ?? null) == 0) {
                $validated['sick_detail'] = null;
            }

            $validated['recorder'] = auth()->user()->name ?? 'ไม่ระบุผู้บันทึก';

            $factFinding = Factfinding::create($validated);

           CaseActivity::where('client_id', $client->id)
                ->where('module', 'factfinding')
                ->delete();

            CaseActivity::record([
                'client_id'   => $client->id,
                'module'      => 'factfinding',
                'type'        => 'success',
                'title'       => 'บันทึกสอบข้อเท็จจริงแรกเข้า',
                'description' => 'บันทึกข้อมูลสอบข้อเท็จจริงเบื้องต้น โดยผู้นำส่ง: ' . ($validated['fact_name'] ?? '-'),
                'occurred_at' => now(),
                'icon'        => 'bi-clipboard-check',
                'url'         => route('factfinding.edit', $factFinding->id),
            ]);

            if (!empty($documents)) {
                foreach ($documents as $docId) {
                    FactFindingDocument::create([
                        'factfinding_id' => $factFinding->id,
                        'document_id'    => (int) $docId,
                    ]);
                }
            }

            return redirect()->route('factfinding.edit', $factFinding->id)
                            ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
        }

    public function FactfindingEdit($factfinding_id)
    {
        // ✅ แก้จาก whereHas('client') เป็น whereIn(client_id, subquery)
        // เพราะ Factfinding model ของคุณยังไม่มี relation client()
        $factFinding = Factfinding::where('id', $factfinding_id)
            ->whereIn('client_id', Client::forUser(auth()->user())->select('id'))
            ->firstOrFail();

        $client = Client::forUser(auth()->user())->findOrFail($factFinding->client_id);

        $documents = Document::all();
        $maritals = Marital::all();
        $selectedDocs = $factFinding->documents()->pluck('documents.id')->toArray();

        return view('frontend.client.factfinding.factfinding_edit', compact(
            'client', 'factFinding', 'documents', 'selectedDocs', 'maritals'
        ));
    }

    public function FactfindingUpdate(Request $request, $factfinding_id)
    {
        $validated = $request->validate([
            'client_id'   => 'required|integer',

            'date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],

                'receive_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],


            'fact_name'   => 'required|string|max:255',

            'appearance'  => 'nullable|string',
            'skin'        => 'nullable|string',
            'scar'        => 'nullable|string',
            'disability'  => 'nullable|string',
            'evidence'    => 'nullable|string',
            'sick'        => 'required|in:0,1',
            'sick_detail' => 'required_if:sick,1|nullable|string',
            'treatment'   => 'nullable|string',
            'hospital'    => 'nullable|string',
            'weight'      => 'nullable|numeric|max:500',
            'height'      => 'nullable|numeric|max:300',
            'blood_group' => 'nullable|string',
            'hygiene'     => 'nullable|string',
            'oral_health' => 'nullable|string',
            'injury'      => 'nullable|string',
            'marital_id'  => 'required|integer',
            'relation_parent' => 'nullable|string',
            'relation_family' => 'nullable|string',
            'relation_child'  => 'nullable|string',
            'ex_conditions'   => 'nullable|string',
            'in_conditions'   => 'nullable|string',
            'environment'     => 'nullable|string',
            'cause_problem'   => 'nullable|string',
            'need'            => 'nullable|string',
            'information'     => 'nullable|string',
            'diagnosis'       => 'nullable|string',

            'case_history'    => 'nullable|string',
            // [AUTO RECORDER] ไม่รับชื่อผู้บันทึกจากฟอร์ม ให้ระบบบันทึกจากผู้ใช้งานที่ login
            'active'          => 'nullable|boolean',

            'documents'   => 'nullable|array',
            'documents.*' => 'integer',
        ], [
            'client_id.required' => 'กรุณาเลือกผู้รับบริการ',
            'client_id.integer'  => 'รหัสผู้รับบริการต้องเป็นตัวเลข',

            'date.required'      => 'กรุณากรอกวันที่นำส่ง',
            'date.date'          => 'รูปแบบวันที่ไม่ถูกต้อง',

            'receive_date.required' => 'กรุณากรอกวันที่บันทึก',
            'receive_date.date' => 'รูปแบบวันที่บันทึกไม่ถูกต้อง',
            'receive_date.before_or_equal' => 'วันที่บันทึกต้องไม่เกินวันที่ปัจจุบัน',


            'fact_name.required' => 'กรุณากรอกชื่อผู้นำส่ง',
            'fact_name.string'   => 'ชื่อข้อเท็จจริงต้องเป็นข้อความ',
            'fact_name.max'      => 'ชื่อข้อเท็จจริงต้องไม่เกิน 255 ตัวอักษร',

            'sick.required'      => 'กรุณาระบุสถานะการเจ็บป่วย',
            'sick.in'            => 'ค่าที่ระบุไม่ถูกต้อง (ต้องเป็น 0 หรือ 1)',
            'sick_detail.required_if' => 'กรุณากรอกรายละเอียดการเจ็บป่วย',
            'sick_detail.string'      => 'รายละเอียดการเจ็บป่วยต้องเป็นข้อความ',

            'marital_id.required' => 'กรุณาเลือกสถานภาพสมรส',
            'marital_id.integer'  => 'สถานภาพสมรสต้องเป็นตัวเลข',

            'weight.numeric' => 'น้ำหนักต้องเป็นตัวเลขเท่านั้น',
            'weight.min'     => 'น้ำหนักต้องไม่น้อยกว่า 1 กิโลกรัม',
            'weight.max'     => 'น้ำหนักต้องไม่เกิน 500 กิโลกรัม',

            'height.numeric' => 'ส่วนสูงต้องเป็นตัวเลขเท่านั้น',
            'height.min'     => 'ส่วนสูงต้องไม่น้อยกว่า 30 เซนติเมตร',
            'height.max'     => 'ส่วนสูงต้องไม่เกิน 300 เซนติเมตร',

            // 'recorder.required'  => 'กรุณากรอกชื่อผู้บันทึก',
            // 'recorder.string'    => 'ชื่อผู้บันทึกต้องเป็นข้อความ',

            'documents.array'    => 'เอกสารต้องอยู่ในรูปแบบรายการ',
            'documents.*.integer'=> 'รหัสเอกสารต้องเป็นตัวเลข',
        ]);

        // ✅ แก้จาก whereHas('client') เป็น whereIn(client_id, subquery)
            $factFinding = Factfinding::where('id', $factfinding_id)
                ->whereIn('client_id', Client::forUser(auth()->user())->select('id'))
                ->firstOrFail();

            // =========================
            // PATCH: กันเปลี่ยน client_id จาก form (hidden field)
            // ใช้ client_id จาก record เดิมเท่านั้น
            // =========================
            $validated['client_id'] = $factFinding->client_id;

            $client = Client::forUser(auth()->user())->findOrFail($validated['client_id']);

            $existing = Factfinding::where('client_id', $client->id)
                ->where('id', '!=', $factFinding->id)
                ->first();

            if ($existing) {
                return redirect()
                    ->route('factfinding.edit', $existing->id)
                    ->with('error', 'มีข้อมูลของผู้รับรายนี้อยู่แล้ว ท่านสามารถแก้ไขแทนการบันทึกใหม่ได้');
            }

            if ($validated['sick'] == 0) {
                $validated['sick_detail'] = null;
            }

            $payload = [
            'client_id'   => (int) $client->id,
            'date'        => $validated['date'],
            'receive_date'=> $validated['receive_date'],
            'fact_name'   => $validated['fact_name'],
            'appearance'  => $validated['appearance'] ?? 'ไม่ระบุ',
            'skin'        => $validated['skin'] ?? 'ไม่ระบุ',
            'scar'        => $validated['scar'] ?? 'ไม่ระบุ',
            'disability'  => $validated['disability'] ?? 'ไม่ระบุ',
            'evidence'    => $validated['evidence'] ?? '',
            'sick'        => $validated['sick'] ?? 0,
            'sick_detail' => $validated['sick_detail'] ?? 'ไม่ระบุ',
            'treatment'   => $validated['treatment'] ?? '',
            'hospital'    => $validated['hospital'] ?? '',
            'weight'      => isset($validated['weight']) ? (float) $validated['weight'] : 0,
            'height'      => isset($validated['height']) ? (float) $validated['height'] : 0,
            'blood_group' => $validated['blood_group'] ?? 'ไม่ระบุ',
            'hygiene'     => $validated['hygiene'] ?? 'ไม่ระบุ',
            'oral_health' => $validated['oral_health'] ?? 'ไม่ระบุ',
            'injury'      => $validated['injury'] ?? 'ไม่ระบุ',
            'marital_id'  => $validated['marital_id'] ?? 0,
            'relation_parent' => $validated['relation_parent'] ?? '',
            'relation_family' => $validated['relation_family'] ?? '',
            'relation_child'  => $validated['relation_child'] ?? '',
            'ex_conditions'   => $validated['ex_conditions'] ?? '',
            'in_conditions'   => $validated['in_conditions'] ?? '',
            'environment'     => $validated['environment'] ?? '',
            'cause_problem'   => $validated['cause_problem'] ?? '',
            'need'            => $validated['need'] ?? '',
            'information'     => $validated['information'] ?? '',
            'diagnosis'       => $validated['diagnosis'] ?? '',
            'case_history'    => $validated['case_history'] ?? '',

            // [AUTO RECORDER] บันทึกชื่อผู้แก้ไขล่าสุดจากผู้ใช้งานที่ login
            'recorder'        => auth()->user()->name ?? 'ไม่ระบุผู้บันทึก',

            'active'          => $validated['active'] ?? 1,
        ];

        // ✅ อัปเดตข้อมูลโดยใช้ $payload ที่เตรียมไว้
        $factFinding->update($payload);

                CaseActivity::where('client_id', $client->id)
                ->where('module', 'factfinding')
                ->delete();

            CaseActivity::record([
                'client_id'   => $client->id,
                'module'      => 'factfinding',
                'type'        => 'success',
                'title'       => 'แก้ไขข้อมูลสอบข้อเท็จจริง',
                'description' => 'แก้ไขข้อมูลสอบข้อเท็จจริง โดยผู้นำส่ง: ' . ($validated['fact_name'] ?? '-'),
                'occurred_at' => now(),
                'icon'        => 'bi-pencil-square',
                'url'         => route('factfinding.edit', $factFinding->id),
            ]);

        FactFindingDocument::where('factfinding_id', $factFinding->id)->delete();

        if (!empty($validated['documents'])) {
            foreach ($validated['documents'] as $docId) {
                FactFindingDocument::create([
                    'factfinding_id' => $factFinding->id,
                    'document_id'    => (int) $docId,
                ]);
            }
        }

        $notification = [
            'message'    => 'อัปเดตข้อมูลเรียบร้อยแล้ว',
            'alert-type' => 'success'
        ];

        return redirect()->route('factfinding.edit', $factFinding->id)
                         ->with($notification);
    }

    public function FactfindingDelete($id)
    {
        $factFinding = Factfinding::where('id', $id)
            ->whereIn('client_id', Client::forUser(auth()->user())->select('id'))
            ->firstOrFail();

        // ลบ activity ของ module นี้
            CaseActivity::where('client_id', $factFinding->client_id)
            ->where('module', 'factfinding')
            ->delete();

        FactFindingDocument::where('factfinding_id', $factFinding->id)->delete();

        $factFinding->delete();

        return back()->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }
}