<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Psychiatric;
use App\Models\Psycho;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\CaseActivity;

class PsychiatricController extends Controller
{
    // แสดงฟอร์มเพิ่มข้อมูลใหม่
    public function AddPsychiatric(Request $request, $client_id)
    {
        $psycho = Psycho::all();

        // =========================
        // PATCH: กันเดา URL เข้าถึง client ที่ไม่มีสิทธิ์
        // =========================
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        // =========================
        // PATCH: ถ้าเลือกวันที่สลับกัน ให้สลับกลับให้อัตโนมัติ
        // =========================
        if (!empty($startDate) && !empty($endDate) && $startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $query = Psychiatric::where('client_id', $client->id)
            ->with('psycho');

        // =========================
        // PATCH: filter วันที่ให้ตรงตามช่วงจริง
        // - ถ้ามีทั้งสองวัน ใช้ whereDate ครอบสองด้าน
        // - ถ้ามีวันเดียว ให้ตรงวันนั้นเท่านั้น
        // =========================
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereDate('sent_date', '>=', $startDate)
                  ->whereDate('sent_date', '<=', $endDate);
        } elseif (!empty($startDate)) {
            $query->whereDate('sent_date', '=', $startDate);
        } elseif (!empty($endDate)) {
            $query->whereDate('sent_date', '=', $endDate);
        }

        $psychiatrics = $query->orderBy('sent_date', 'desc')->get();
        $psychiatric = null; // สำหรับเพิ่มใหม่

        return view('frontend.client.psychiatric.psychiatric_create', compact(
            'client',
            'client_id',
            'psychiatrics',
            'psychiatric',
            'psycho',
            'startDate',
            'endDate'
        ));
    }

    // บันทึกข้อมูลใหม่
    public function StorePsychiatric(Request $request)
        {
            $data = $request->validate([
                'sent_date'   => [
                    'required',
                    'date',
                    Rule::unique('psychiatrics')->where(function ($query) use ($request) {
                        return $query->where('client_id', $request->client_id);
                    }),
                ],
                'hotpital'    => 'required|string|max:255',
                'psycho_id'   => 'required|exists:psychos,id',
                'diagnose'    => 'nullable|string',
                'appoin_date' => 'nullable|date',
                'drug_no'     => 'required|in:yes,no',
                'drug_name'   => 'nullable|string|max:255',
                'disa_no'     => 'required|in:yes,no',
                'client_id'   => 'required|exists:clients,id',
            ], [
                'sent_date.required'   => 'กรุณาระบุวันที่ส่ง',
                'sent_date.date'       => 'รูปแบบวันที่ส่งไม่ถูกต้อง',
                'sent_date.unique'     => 'วันที่ส่งนี้ถูกใช้แล้วสำหรับผู้รับบริการคนนี้',
                'hotpital.required'    => 'กรุณาระบุชื่อโรงพยาบาล',
                'hotpital.string'      => 'ชื่อโรงพยาบาลต้องเป็นข้อความ',
                'hotpital.max'         => 'ชื่อโรงพยาบาลต้องไม่เกิน 255 ตัวอักษร',
                'psycho_id.required'   => 'กรุณาเลือกนักจิตวิทยา',
                'psycho_id.exists'     => 'นักจิตวิทยาที่เลือกไม่ถูกต้อง',
                'diagnose.string'      => 'การวินิจฉัยต้องเป็นข้อความ',
                'appoin_date.date'     => 'รูปแบบวันที่นัดหมายไม่ถูกต้อง',
                'drug_no.required'     => 'กรุณาระบุการใช้ยา',
                'drug_no.in'           => 'การใช้ยาต้องเป็น yes หรือ no เท่านั้น',
                'drug_name.string'     => 'ชื่อยาต้องเป็นข้อความ',
                'drug_name.max'        => 'ชื่อยาต้องไม่เกิน 255 ตัวอักษร',
                'disa_no.required'     => 'กรุณาระบุความพิการ',
                'disa_no.in'           => 'ความพิการต้องเป็น yes หรือ no เท่านั้น',
                'client_id.required'   => 'กรุณาเลือกผู้รับบริการ',
                'client_id.exists'     => 'ผู้รับบริการที่เลือกไม่ถูกต้อง',
            ]);

            // =========================
            // PATCH: กันยิง request เปลี่ยน client_id
            // =========================
            $client = Client::forUser(auth()->user())->findOrFail($data['client_id']);
            $data['client_id'] = $client->id;

            if ($data['drug_no'] === 'no') {
                $data['drug_name'] = null;
            }

            $psychiatric = Psychiatric::create($data);

           CaseActivity::where('client_id', $client->id)
            ->where('module', 'psychiatric')
            ->delete();

            CaseActivity::record([
            'client_id'   => $client->id,
            'module'      => 'psychiatric',
            'type'        => 'success',
            'title'       => 'บันทึกการวินิจฉัยทางจิตเวช',
            'description' => 'วันที่ส่งตรวจ: ' . ($data['sent_date'] ?? '-') .
                            ' | ส่งตรวจที่: ' . ($data['hotpital'] ?? '-') .
                            ' | การใช้ยา: ' . (($data['drug_no'] ?? 'no') === 'yes' ? 'มี' : 'ไม่มี'),
            'occurred_at' => $data['sent_date'] ?? now('Asia/Bangkok'),
            'icon'        => 'bi-hospital',
            'url'         => route('psychiatric.create', $client->id),
        ]);

            $notification = [
                'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success'
            ];

            return redirect()->back()->with($notification);
        }

    // แก้ไขข้อมูล (โหลดฟอร์มพร้อมข้อมูลเดิม)
    public function EditPsychiatricJson($id)
    {
      $psychiatric = Psychiatric::whereHas('client', function ($q) {
        $q->forUser(auth()->user());
    })
    ->findOrFail($id);

        // =========================
        // PATCH: กันเดา URL เรียก JSON ของ client คนอื่น
        // =========================
        Client::forUser(auth()->user())->findOrFail($psychiatric->client_id);

        return response()->json([
            'id'          => $psychiatric->id,
            'sent_date'   => \Carbon\Carbon::parse($psychiatric->sent_date)->format('Y-m-d'),
            'hotpital'    => $psychiatric->hotpital,
            'psycho_id'   => $psychiatric->psycho_id,
            'diagnose'    => $psychiatric->diagnose,
            'appoin_date' => $psychiatric->appoin_date ? \Carbon\Carbon::parse($psychiatric->appoin_date)->format('Y-m-d') : null,
            'drug_no'     => $psychiatric->drug_no,
            'drug_name'   => $psychiatric->drug_name,
            'disa_no'     => $psychiatric->disa_no,
            'client_id'   => $psychiatric->client_id,
        ]);
    }

    // อัปเดตข้อมูล
    public function UpdatePsychiatric(Request $request, $id)
    {
       $psychiatric = Psychiatric::whereHas('client', function ($q) {
        $q->forUser(auth()->user());
    })
    ->findOrFail($id);

        // =========================
        // PATCH: กันเดา URL มา update record คนอื่น
        // =========================
        Client::forUser(auth()->user())->findOrFail($psychiatric->client_id);

        $data = $request->validate([
            'sent_date'   => [
                'required',
                'date',
                Rule::unique('psychiatrics')
                    ->where(function ($query) use ($request) {
                        return $query->where('client_id', $request->client_id);
                    })
                    ->ignore($id),
            ],
            'hotpital'    => 'required|string|max:255',
            'psycho_id'   => 'required|exists:psychos,id',
            'diagnose'    => 'nullable|string',
            'appoin_date' => 'nullable|date',
            'drug_no'     => 'required|in:yes,no',
            'drug_name'   => 'nullable|string|max:255',
            'disa_no'     => 'required|in:yes,no',
            'client_id'   => 'required|exists:clients,id',
        ], [
            'sent_date.required'   => 'กรุณาระบุวันที่ส่ง',
            'sent_date.date'       => 'รูปแบบวันที่ส่งไม่ถูกต้อง',
            'sent_date.unique'     => 'วันที่ส่งนี้ถูกใช้แล้วสำหรับผู้รับบริการคนนี้',
            'hotpital.required'    => 'กรุณาระบุชื่อโรงพยาบาล',
            'hotpital.string'      => 'ชื่อโรงพยาบาลต้องเป็นข้อความ',
            'hotpital.max'         => 'ชื่อโรงพยาบาลต้องไม่เกิน 255 ตัวอักษร',
            'psycho_id.required'   => 'กรุณาเลือกนักจิตวิทยา',
            'psycho_id.exists'     => 'นักจิตวิทยาที่เลือกไม่ถูกต้อง',
            'diagnose.string'      => 'การวินิจฉัยต้องเป็นข้อความ',
            'appoin_date.date'     => 'รูปแบบวันที่นัดหมายไม่ถูกต้อง',
            'drug_no.required'     => 'กรุณาระบุการใช้ยา',
            'drug_no.in'           => 'การใช้ยาต้องเป็น yes หรือ no เท่านั้น',
            'drug_name.string'     => 'ชื่อยาต้องเป็นข้อความ',
            'drug_name.max'        => 'ชื่อยาต้องไม่เกิน 255 ตัวอักษร',
            'disa_no.required'     => 'กรุณาระบุความพิการ',
            'disa_no.in'           => 'ความพิการต้องเป็น yes หรือ no เท่านั้น',
            'client_id.required'   => 'กรุณาเลือกผู้รับบริการ',
            'client_id.exists'     => 'ผู้รับบริการที่เลือกไม่ถูกต้อง',
        ]);

        // =========================
        // PATCH: กันเปลี่ยน client_id ไป client อื่น
        // =========================
        Client::forUser(auth()->user())->findOrFail($data['client_id']);

        if ($data['drug_no'] === 'no') {
            $data['drug_name'] = null;
        }

        if (empty($data['hotpital'])) {
            $data['hotpital'] = 'ไม่ระบุ';
        }

        $psychiatric->update($data);

        CaseActivity::where('client_id', $psychiatric->client_id)
            ->where('module', 'psychiatric')
            ->delete();

        CaseActivity::record([
            'client_id'   => $psychiatric->client_id,
            'module'      => 'psychiatric',
            'type'        => 'success',
            'title'       => 'แก้ไขการวินิจฉัยทางจิตเวช',
            'description' => 'วันที่ส่งตรวจ: ' . ($data['sent_date'] ?? '-') .
                            ' | ส่งตรวจที่: ' . ($data['hotpital'] ?? '-') .
                            ' | การใช้ยา: ' . (($data['drug_no'] ?? 'no') === 'yes' ? 'มี' : 'ไม่มี'),
            'occurred_at' => $data['sent_date'] ?? now('Asia/Bangkok'),
            'icon'        => 'bi-hospital',
            'url'         => route('psychiatric.create', $psychiatric->client_id),
        ]);

        $notification = [
            'message' => 'อัปเดตข้อมูลเรียบร้อยแล้ว',
            'alert-type' => 'success'
        ];

        return redirect()->route('psychiatric.create', $psychiatric->client_id)
                         ->with($notification);
    }

    // ลบข้อมูล
    public function DeletePsychiatric($id)
    {
       $psychiatric = Psychiatric::whereHas('client', function ($q) {
        $q->forUser(auth()->user());
    })
    ->findOrFail($id);  

        // =========================
        // PATCH: กันเดา URL มาลบข้อมูลของ client คนอื่น
        // =========================
        Client::forUser(auth()->user())->findOrFail($psychiatric->client_id);

        $clientId = $psychiatric->client_id;

        CaseActivity::where('client_id', $clientId)
            ->where('module', 'psychiatric')
            ->delete();

        $psychiatric->delete();

        $notification = [
            'message' => 'ลบข้อมูลเรียบร้อยแล้ว',
            'alert-type' => 'success'
        ];

        return redirect()
            ->route('psychiatric.create', $clientId)
            ->with($notification);
    }

    public function ReportPsychiatric(Request $request, $client_id)
    {
        $psycho = Psycho::all();

        // =========================
        // PATCH: กันเดา URL เข้าถึง client ที่ไม่มีสิทธิ์
        // =========================
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        // =========================
        // PATCH: ถ้าเลือกวันที่สลับกัน ให้สลับกลับให้อัตโนมัติ
        // =========================
        if (!empty($startDate) && !empty($endDate) && $startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $query = Psychiatric::where('client_id', $client->id)
            ->with('psycho');

        // =========================
        // PATCH: filter วันที่ให้ตรงตามช่วงจริง
        // - ถ้ามีทั้งสองวัน ใช้ whereDate ครอบสองด้าน
        // - ถ้ามีวันเดียว ให้ตรงวันนั้นเท่านั้น
        // =========================
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereDate('sent_date', '>=', $startDate)
                  ->whereDate('sent_date', '<=', $endDate);
        } elseif (!empty($startDate)) {
            $query->whereDate('sent_date', '=', $startDate);
        } elseif (!empty($endDate)) {
            $query->whereDate('sent_date', '=', $endDate);
        }

        $psychiatrics = $query->orderBy('sent_date', 'desc')->get();

        return view('frontend.client.psychiatric.psychiatric_report', compact(
            'client',
            'client_id',
            'psychiatrics',
            'psycho',
            'startDate',
            'endDate'
        ));
    }
}