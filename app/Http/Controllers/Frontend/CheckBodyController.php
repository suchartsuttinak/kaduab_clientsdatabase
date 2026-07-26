<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CheckBody;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\CaseActivity;

class CheckBodyController extends Controller
{
    public function CheckBodyAdd($client_id)
    {
        // PATCH: กันเดา URL เข้าถึง client ที่ไม่มีสิทธิ์
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $checkbodies = CheckBody::where('client_id', $client->id)
            ->orderByDesc('assessor_date')
            ->orderByDesc('id')
            ->get();

        $checkbody = null;

        return view('frontend.client.checkBody.index', compact(
            'client',
            'client_id',
            'checkbodies',
            'checkbody'
        ));
    }

  public function CheckBodyStore(Request $request)
        {
            $validated = $this->validateCheckBody($request);

            // PATCH: กันแก้ client_id จาก request เพื่อบันทึกให้ client ที่ไม่มีสิทธิ์
            Client::forUser(auth()->user())->findOrFail($validated['client_id']);

            if (($validated['development'] ?? null) === 'สมวัย') {
                $validated['detail'] = null;
            }

            if (($validated['development_type'] ?? null) !== 'เด็กกลุ่มพิเศษ') {
                $validated['special_support_type'] = null;
                $validated['special_support_other'] = null;
            } elseif (($validated['special_support_type'] ?? null) !== 'อื่น ๆ') {
                $validated['special_support_other'] = null;
            }

            $checkBody = CheckBody::create($validated);

                CaseActivity::where('client_id', $validated['client_id'])
                ->where('module', 'check_body')
                ->delete();

                CaseActivity::record([
                'client_id'   => $validated['client_id'],
                'module'      => 'check_body',
                'type'        => 'success',
                'title'       => 'บันทึกการตรวจร่างกาย',
                'description' => 'วันที่ตรวจ: ' . ($validated['assessor_date'] ?? '-') .
                                ' | พัฒนาการ: ' . ($validated['development'] ?? '-') .
                                ' | ประเภทพัฒนาการ: ' . ($validated['development_type'] ?? '-'),
                'occurred_at' => $validated['assessor_date'] ?? now('Asia/Bangkok'),
                'icon'        => 'bi-person-heart',
                'url'         => route('check_body.add', $validated['client_id']),
            ]);

            return redirect()
                ->route('check_body.add', $validated['client_id'])
                ->with([
                    'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                    'alert-type' => 'success',
                ]);
        }

    public function CheckBodyEdit($id)
    {
        // PATCH: กันเดา URL เข้ามาแก้ record ของ client ที่ไม่มีสิทธิ์ตั้งแต่ query แรก
        $checkbody = CheckBody::with('client')
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        $client = $checkbody->client;

        $checkbodies = CheckBody::where('client_id', $client->id)
            ->orderByDesc('assessor_date')
            ->orderByDesc('id')
            ->get();

        return view('frontend.client.checkBody.index', compact(
            'client',
            'checkbodies',
            'checkbody'
        ))->with('client_id', $client->id);
    }

    public function CheckBodyUpdate(Request $request, $id)
    {
        // PATCH: กันเดา URL เข้ามาอัปเดต record ของ client ที่ไม่มีสิทธิ์ตั้งแต่ query แรก
        $checkbody = CheckBody::whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        // PATCH: บังคับ client_id ให้เป็นเจ้าของ record เดิม ป้องกันแก้ hidden field แล้วย้าย record
        $request->merge([
            'client_id' => $checkbody->client_id,
        ]);

        $validated = $this->validateCheckBody($request, $checkbody->id);

        // PATCH: ยืนยันสิทธิ์ client เจ้าของ record เดิมอีกชั้น
        Client::forUser(auth()->user())->findOrFail($checkbody->client_id);

        if (($validated['development'] ?? null) === 'สมวัย') {
            $validated['detail'] = null;
        }

        if (($validated['development_type'] ?? null) !== 'เด็กกลุ่มพิเศษ') {
            $validated['special_support_type'] = null;
            $validated['special_support_other'] = null;
        } elseif (($validated['special_support_type'] ?? null) !== 'อื่น ๆ') {
            $validated['special_support_other'] = null;
        }

        $checkbody->update($validated);

        CaseActivity::where('client_id', $checkbody->client_id)
            ->where('module', 'check_body')
            ->delete();

        CaseActivity::record([
            'client_id'   => $checkbody->client_id,
            'module'      => 'check_body',
            'type'        => 'success',
            'title'       => 'แก้ไขการตรวจร่างกาย',
            'description' => 'วันที่ตรวจ: ' . ($validated['assessor_date'] ?? '-') .
                            ' | พัฒนาการ: ' . ($validated['development'] ?? '-') .
                            ' | ประเภทพัฒนาการ: ' . ($validated['development_type'] ?? '-'),
            'occurred_at' => now(),
            'icon'        => 'bi-person-heart',
            'url'         => route('check_body.add', $checkbody->client_id),
        ]);

        return redirect()
            ->route('check_body.add', $checkbody->client_id)
            ->with([
                'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function CheckBodyDelete($id)
    {
        // PATCH: กันเดา URL เข้ามาลบ record ของ client ที่ไม่มีสิทธิ์ตั้งแต่ query แรก
        $checkbody = CheckBody::whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        $clientId = $checkbody->client_id;

        CaseActivity::where('client_id', $clientId)
            ->where('module', 'check_body')
            ->delete();

        $checkbody->delete();

        return redirect()
            ->route('check_body.add', $clientId)
            ->with([
                'message' => 'ลบข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function CheckBodyReport($id)
    {
        // PATCH: กันเดา URL เข้าดูรายงานของ client ที่ไม่มีสิทธิ์ตั้งแต่ query แรก
        $checkbody = CheckBody::with('client')
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        $client = $checkbody->client;

        return view('frontend.client.checkBody.report', compact('checkbody', 'client'));
    }

    private function validateCheckBody(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],

            'assessor_date' => [
            'required',
            'date',
            'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            Rule::unique('check_bodies')
                ->where(fn ($query) => $query->where('client_id', $request->client_id))
                ->ignore($ignoreId),
        ],

            'development' => ['required', 'in:สมวัย,ไม่สมวัย'],
            'detail' => ['nullable', 'string'],

            'development_type' => ['nullable', 'in:เด็กทั่วไป,เด็กกลุ่มพิเศษ'],
            'special_support_type' => [
                'nullable',
                Rule::in([
                    'ต้องการการสนับสนุนด้านการเรียนรู้ (อ่าน เขียน คำนวณ)',
                    'ต้องการการสนับสนุนด้านพฤติกรรมและอารมณ์ (การควบคุมอารมณ์, สมาธิ)',
                    'ต้องการการสนับสนุนด้านสังคม (การเข้าสังคม, ทำงานร่วมกับเพื่อน)',
                    'ต้องการการสนับสนุนด้านร่างกาย (การเคลื่อนไหว, สุขภาพ)',
                    'มีศักยภาพพิเศษที่ควรส่งเสริม (ดนตรี, กีฬา, ศิลปะ)',
                    'อื่น ๆ',
                ]),
            ],
            'special_support_other' => ['nullable', 'string', 'max:255'],

            'weight' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],

            'oral' => ['nullable', 'string', 'max:255'],
            'appearance' => ['nullable', 'string', 'max:255'],
            'wound' => ['nullable', 'string', 'max:255'],
            'disease' => ['nullable', 'string', 'max:255'],
            'hygiene' => ['nullable', 'string', 'max:255'],
            'health' => ['nullable', 'string', 'max:255'],
            'inoculation' => ['nullable', 'string', 'max:255'],
            'injection' => ['nullable', 'string', 'max:255'],
            'vaccination' => ['nullable', 'string', 'max:255'],
            'contagious' => ['nullable', 'string', 'max:255'],
            'other' => ['nullable', 'string', 'max:255'],
            'drug_allergy' => ['nullable', 'string', 'max:255'],

            'recorder' => ['required', 'string', 'max:255'],
            'remark' => ['nullable', 'string'],
        ], [
            'client_id.required' => 'กรุณาเลือกผู้รับบริการ',
            'client_id.exists' => 'ข้อมูลผู้รับบริการไม่ถูกต้อง',

            'assessor_date.required' => 'กรุณาระบุวันที่ตรวจ',
            'assessor_date.date' => 'รูปแบบวันที่ตรวจไม่ถูกต้อง',
            'assessor_date.unique' => 'มีข้อมูลตรวจสุขภาพวันที่นี้แล้วสำหรับผู้รับบริการคนนี้',

            'development.required' => 'กรุณาเลือกผลการประเมินพัฒนาการ',
            'development.in' => 'ค่าพัฒนาการไม่ถูกต้อง',

            'development_type.in' => 'ค่าการพัฒนาไม่ถูกต้อง',
            'special_support_type.in' => 'ประเภทการสนับสนุนไม่ถูกต้อง',
            'special_support_other.max' => 'รายละเอียดอื่น ๆ ต้องไม่เกิน 255 ตัวอักษร',

            'weight.numeric' => 'น้ำหนักต้องเป็นตัวเลข',
            'weight.min' => 'น้ำหนักต้องไม่น้อยกว่า 0',

            'height.numeric' => 'ส่วนสูงต้องเป็นตัวเลข',
            'height.min' => 'ส่วนสูงต้องไม่น้อยกว่า 0',

            'recorder.required' => 'กรุณาระบุชื่อผู้ตรวจหรือผู้บันทึก',
            'recorder.max' => 'ชื่อผู้ตรวจหรือผู้บันทึกต้องไม่เกิน 255 ตัวอักษร',
        ]);

        // PATCH: ตรวจสิทธิ์ client_id ที่ถูกส่งมาจากฟอร์ม
        Client::forUser(auth()->user())->findOrFail($validated['client_id']);

        return $validated;
    }
}