<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\CheckBody;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CheckBodyController extends Controller
{
    public function CheckBodyAdd($client_id): View
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $checkbodies = $this->getClientCheckBodies($client->id);
        $checkbody = null;

        return view('frontend.client.checkBody.index', compact(
            'client',
            'client_id',
            'checkbodies',
            'checkbody'
        ));
    }

    public function CheckBodyStore(Request $request): RedirectResponse
    {
        $validated = $this->validateCheckBody($request);

        // ตรวจสิทธิ์จากผู้รับบริการจริงอีกชั้น ไม่เชื่อค่า client_id จาก hidden input เพียงอย่างเดียว
        $client = Client::forUser(auth()->user())
            ->findOrFail($validated['client_id']);

        $validated['client_id'] = $client->id;
        $validated = $this->normalizeCheckBodyData($validated);

        DB::transaction(function () use ($validated, $client): void {
            CheckBody::create($validated);
            $this->syncLatestCaseActivity($client->id);
        });

        return redirect()
            ->route('check_body.add', $client->id)
            ->with([
                'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function CheckBodyEdit($id): View
    {
        $checkbody = $this->findAccessibleCheckBody($id, true);
        $client = $checkbody->client;
        $checkbodies = $this->getClientCheckBodies($client->id);

        return view('frontend.client.checkBody.index', compact(
            'client',
            'checkbodies',
            'checkbody'
        ))->with('client_id', $client->id);
    }

    public function CheckBodyUpdate(Request $request, $id): RedirectResponse
    {
        $checkbody = $this->findAccessibleCheckBody($id);

        // ห้ามย้ายรายการไปยังผู้รับบริการรายอื่นด้วยการแก้ hidden input
        $request->merge([
            'client_id' => $checkbody->client_id,
        ]);

        $validated = $this->validateCheckBody($request, $checkbody->id);
        $validated['client_id'] = $checkbody->client_id;
        $validated = $this->normalizeCheckBodyData($validated);

        DB::transaction(function () use ($checkbody, $validated): void {
            $checkbody->update($validated);
            $this->syncLatestCaseActivity($checkbody->client_id);
        });

        return redirect()
            ->route('check_body.add', $checkbody->client_id)
            ->with([
                'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function CheckBodyDelete($id): RedirectResponse
    {
        $checkbody = $this->findAccessibleCheckBody($id);
        $clientId = $checkbody->client_id;

        DB::transaction(function () use ($checkbody, $clientId): void {
            $checkbody->delete();
            $this->syncLatestCaseActivity($clientId);
        });

        return redirect()
            ->route('check_body.add', $clientId)
            ->with([
                'message' => 'ลบข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function CheckBodyReport($id): View
    {
        $checkbody = $this->findAccessibleCheckBody($id, true);
        $client = $checkbody->client;

        return view('frontend.client.checkBody.report', compact('checkbody', 'client'));
    }

    private function validateCheckBody(Request $request, ?int $ignoreId = null): array
    {
        $today = now('Asia/Bangkok')->toDateString();

        return $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'assessor_date' => [
                'required',
                'date',
                'before_or_equal:' . $today,
                Rule::unique('check_bodies')
                    ->where(fn ($query) => $query->where('client_id', $request->input('client_id')))
                    ->ignore($ignoreId),
            ],
            'development' => ['required', 'in:สมวัย,ไม่สมวัย'],
            'detail' => ['nullable', 'required_if:development,ไม่สมวัย', 'string', 'max:3000'],
            'development_type' => ['required', 'in:เด็กทั่วไป,เด็กกลุ่มพิเศษ'],
            'special_support_type' => [
                'nullable',
                'required_if:development_type,เด็กกลุ่มพิเศษ',
                Rule::in([
                    'ต้องการการสนับสนุนด้านการเรียนรู้ (อ่าน เขียน คำนวณ)',
                    'ต้องการการสนับสนุนด้านพฤติกรรมและอารมณ์ (การควบคุมอารมณ์, สมาธิ)',
                    'ต้องการการสนับสนุนด้านสังคม (การเข้าสังคม, ทำงานร่วมกับเพื่อน)',
                    'ต้องการการสนับสนุนด้านร่างกาย (การเคลื่อนไหว, สุขภาพ)',
                    'มีศักยภาพพิเศษที่ควรส่งเสริม (ดนตรี, กีฬา, ศิลปะ)',
                    'อื่น ๆ',
                ]),
            ],
            'special_support_other' => [
                'nullable',
                'required_if:special_support_type,อื่น ๆ',
                'string',
                'max:255',
            ],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:300'],
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
            'remark' => ['nullable', 'string', 'max:3000'],
        ], [
            'client_id.required' => 'กรุณาเลือกผู้รับบริการ',
            'client_id.integer' => 'ข้อมูลผู้รับบริการไม่ถูกต้อง',
            'client_id.exists' => 'ข้อมูลผู้รับบริการไม่ถูกต้อง',
            'assessor_date.required' => 'กรุณาระบุวันที่ตรวจ',
            'assessor_date.date' => 'รูปแบบวันที่ตรวจไม่ถูกต้อง',
            'assessor_date.before_or_equal' => 'วันที่ตรวจต้องไม่เกินวันที่ปัจจุบัน',
            'assessor_date.unique' => 'มีข้อมูลตรวจสุขภาพวันที่นี้แล้วสำหรับผู้รับบริการคนนี้',
            'development.required' => 'กรุณาเลือกผลการประเมินพัฒนาการ',
            'development.in' => 'ค่าพัฒนาการไม่ถูกต้อง',
            'detail.required_if' => 'กรุณาระบุรายละเอียดกรณีพัฒนาการไม่สมวัย',
            'detail.max' => 'รายละเอียดพัฒนาการต้องไม่เกิน 3,000 ตัวอักษร',
            'development_type.required' => 'กรุณาเลือกกลุ่มการส่งเสริมและพัฒนา',
            'development_type.in' => 'ค่ากลุ่มการส่งเสริมและพัฒนาไม่ถูกต้อง',
            'special_support_type.required_if' => 'กรุณาเลือกประเภทการสนับสนุนสำหรับเด็กกลุ่มพิเศษ',
            'special_support_type.in' => 'ประเภทการสนับสนุนไม่ถูกต้อง',
            'special_support_other.required_if' => 'กรุณาระบุประเภทการสนับสนุนอื่น ๆ',
            'special_support_other.max' => 'รายละเอียดอื่น ๆ ต้องไม่เกิน 255 ตัวอักษร',
            'weight.numeric' => 'น้ำหนักต้องเป็นตัวเลข',
            'weight.min' => 'น้ำหนักต้องไม่น้อยกว่า 0',
            'weight.max' => 'น้ำหนักต้องไม่เกิน 500 กิโลกรัม',
            'height.numeric' => 'ส่วนสูงต้องเป็นตัวเลข',
            'height.min' => 'ส่วนสูงต้องไม่น้อยกว่า 0',
            'height.max' => 'ส่วนสูงต้องไม่เกิน 300 เซนติเมตร',
            'recorder.required' => 'กรุณาระบุชื่อผู้ตรวจหรือผู้บันทึก',
            'recorder.max' => 'ชื่อผู้ตรวจหรือผู้บันทึกต้องไม่เกิน 255 ตัวอักษร',
            'remark.max' => 'หมายเหตุต้องไม่เกิน 3,000 ตัวอักษร',
        ]);
    }

    private function normalizeCheckBodyData(array $validated): array
    {
        $nullableTextFields = [
            'detail',
            'special_support_type',
            'special_support_other',
            'oral',
            'appearance',
            'wound',
            'disease',
            'hygiene',
            'health',
            'inoculation',
            'injection',
            'vaccination',
            'contagious',
            'other',
            'drug_allergy',
            'remark',
        ];

        foreach (['recorder', ...$nullableTextFields] as $field) {
            if (!array_key_exists($field, $validated)) {
                continue;
            }

            $value = trim((string) $validated[$field]);
            $validated[$field] = in_array($field, $nullableTextFields, true) && $value === ''
                ? null
                : $value;
        }

        foreach (['weight', 'height'] as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        if (($validated['development'] ?? null) === 'สมวัย') {
            $validated['detail'] = null;
        }

        if (($validated['development_type'] ?? null) !== 'เด็กกลุ่มพิเศษ') {
            $validated['special_support_type'] = null;
            $validated['special_support_other'] = null;
        } elseif (($validated['special_support_type'] ?? null) !== 'อื่น ๆ') {
            $validated['special_support_other'] = null;
        }

        return $validated;
    }

    private function getClientCheckBodies(int $clientId)
    {
        return CheckBody::where('client_id', $clientId)
            ->orderByDesc('assessor_date')
            ->orderByDesc('id')
            ->get();
    }

    private function findAccessibleCheckBody($id, bool $withClient = false): CheckBody
    {
        $query = CheckBody::query()
            ->whereKey($id)
            ->whereHas('client', function ($query): void {
                $query->forUser(auth()->user());
            });

        if ($withClient) {
            $query->with('client');
        }

        return $query->firstOrFail();
    }

    /**
     * คง CaseActivity ของโมดูลนี้ไว้หนึ่งรายการ โดยอ้างอิงผลตรวจล่าสุดจริง
     * เมื่อลบข้อมูลล่าสุด จะสร้างใหม่จากรายการที่ยังเหลือ หรือไม่สร้างเมื่อไม่มีข้อมูลแล้ว
     */
    private function syncLatestCaseActivity(int $clientId): void
    {
        CaseActivity::where('client_id', $clientId)
            ->where('module', 'check_body')
            ->delete();

        $latestCheckBody = CheckBody::where('client_id', $clientId)
            ->orderByDesc('assessor_date')
            ->orderByDesc('id')
            ->first();

        if (!$latestCheckBody) {
            return;
        }

        CaseActivity::record([
            'client_id' => $clientId,
            'module' => 'check_body',
            'type' => 'success',
            'title' => 'บันทึกการตรวจร่างกาย',
            'description' => 'วันที่ตรวจ: ' . ($latestCheckBody->assessor_date ?? '-') .
                ' | พัฒนาการ: ' . ($latestCheckBody->development ?? '-') .
                ' | ประเภทพัฒนาการ: ' . ($latestCheckBody->development_type ?? '-'),
            'occurred_at' => $latestCheckBody->assessor_date ?? now('Asia/Bangkok'),
            'icon' => 'bi-person-heart',
            'url' => route('check_body.add', $clientId),
        ]);
    }
}
