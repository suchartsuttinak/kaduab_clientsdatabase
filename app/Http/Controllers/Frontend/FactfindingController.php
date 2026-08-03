<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\Document;
use App\Models\Factfinding;
use App\Models\Marital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class FactfindingController extends Controller
{
    /**
     * แสดงฟอร์มเพิ่มข้อมูลสอบข้อเท็จจริง
     */
    public function FactfindingAdd($client_id)
    {
        $client = $this->findAuthorizedClient($client_id);

        $factFinding = Factfinding::query()
            ->select(['id', 'client_id'])
            ->where('client_id', $client->id)
            ->first();

        if ($factFinding) {
            return redirect()
                ->route('factfinding.edit', $factFinding->id)
                ->with('info', 'มีข้อมูลสอบข้อเท็จจริงอยู่แล้ว จึงเข้าสู่หน้าแก้ไข');
        }

        [$documents, $maritals] = $this->loadFormOptions();

        return view('frontend.client.factfinding.factfinding_add', [
            'client' => $client,
            'documents' => $documents,
            'maritals' => $maritals,
            'info' => 'เพิ่มข้อมูลสอบข้อเท็จจริง',
        ]);
    }

    /**
     * บันทึกข้อมูลสอบข้อเท็จจริง
     */
    public function FactfindingStore(Request $request)
    {
        $validated = $request->validate(
            $this->validationRules(),
            $this->validationMessages()
        );

        $documentIds = $this->normalizeDocumentIds($validated['documents'] ?? []);
        unset($validated['documents']);

        try {
            $result = DB::transaction(function () use ($validated, $documentIds) {
                /*
                 * ล็อกแถวผู้รับบริการระหว่างตรวจและสร้างข้อมูล
                 * ป้องกันการกดบันทึกซ้ำหรือการส่งคำขอพร้อมกันจนเกิดข้อมูลซ้ำ
                 */
                $client = Client::forUser(auth()->user())
                    ->whereKey($validated['client_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $existing = Factfinding::query()
                    ->select(['id', 'client_id'])
                    ->where('client_id', $client->id)
                    ->first();

                if ($existing) {
                    return [
                        'created' => false,
                        'factfinding_id' => $existing->id,
                    ];
                }

                $payload = $this->preparePayload($validated, $client->id);
                $factFinding = Factfinding::create($payload);

                /*
                 * ใช้ sync เพียงครั้งเดียวแทนการ insert เอกสารทีละรายการ
                 * ลดจำนวน Query และทำให้การบันทึกเอกสารเป็นชุดเดียวกัน
                 */
                $factFinding->documents()->sync($documentIds);

                $this->recordActivity(
                    $client->id,
                    $factFinding->id,
                    'บันทึกสอบข้อเท็จจริงแรกเข้า',
                    'บันทึกข้อมูลสอบข้อเท็จจริงเบื้องต้น โดยผู้นำส่ง: ' . ($payload['fact_name'] ?? '-'),
                    'bi-clipboard-check'
                );

                return [
                    'created' => true,
                    'factfinding_id' => $factFinding->id,
                ];
            }, 3);
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'ไม่สามารถบันทึกข้อมูลได้ในขณะนี้ กรุณาลองใหม่อีกครั้ง');
        }

        if (!$result['created']) {
            return redirect()
                ->route('factfinding.edit', $result['factfinding_id'])
                ->with('error', 'มีข้อมูลของผู้รับรายนี้อยู่แล้ว ท่านสามารถแก้ไขแทนการบันทึกใหม่ได้');
        }

        return redirect()
            ->route('factfinding.edit', $result['factfinding_id'])
            ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }

    /**
     * แสดงฟอร์มแก้ไขข้อมูลสอบข้อเท็จจริง
     */
    public function FactfindingEdit($factfinding_id)
    {
        $factFinding = $this->findAuthorizedFactFinding($factfinding_id);

        $client = $this->findAuthorizedClient($factFinding->client_id);

        [$documents, $maritals] = $this->loadFormOptions();

        $selectedDocs = $factFinding->documents()
            ->pluck('documents.id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return view('frontend.client.factfinding.factfinding_edit', compact(
            'client',
            'factFinding',
            'documents',
            'selectedDocs',
            'maritals'
        ));
    }

    /**
     * อัปเดตข้อมูลสอบข้อเท็จจริง
     */
    public function FactfindingUpdate(Request $request, $factfinding_id)
    {
        $validated = $request->validate(
            $this->validationRules(),
            $this->validationMessages()
        );

        $documentIds = $this->normalizeDocumentIds($validated['documents'] ?? []);
        unset($validated['documents']);

        /*
         * ตรวจสิทธิ์ก่อนเริ่ม Transaction
         * client_id จาก hidden field จะไม่ถูกนำมาใช้เปลี่ยนเจ้าของข้อมูล
         */
        $authorizedFactFinding = $this->findAuthorizedFactFinding($factfinding_id);

        try {
            DB::transaction(function () use (
                $authorizedFactFinding,
                $validated,
                $documentIds
            ) {
                $factFinding = Factfinding::query()
                    ->whereKey($authorizedFactFinding->id)
                    ->where('client_id', $authorizedFactFinding->client_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $client = Client::forUser(auth()->user())
                    ->whereKey($factFinding->client_id)
                    ->firstOrFail();

                $payload = $this->preparePayload(
                    $validated,
                    $client->id,
                    $factFinding
                );

                $factFinding->update($payload);
                $factFinding->documents()->sync($documentIds);

                $this->recordActivity(
                    $client->id,
                    $factFinding->id,
                    'แก้ไขข้อมูลสอบข้อเท็จจริง',
                    'แก้ไขข้อมูลสอบข้อเท็จจริง โดยผู้นำส่ง: ' . ($payload['fact_name'] ?? '-'),
                    'bi-pencil-square'
                );
            }, 3);
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'ไม่สามารถอัปเดตข้อมูลได้ในขณะนี้ กรุณาลองใหม่อีกครั้ง');
        }

        return redirect()
            ->route('factfinding.edit', $authorizedFactFinding->id)
            ->with([
                'message' => 'อัปเดตข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * ลบข้อมูลสอบข้อเท็จจริง
     */
    public function FactfindingDelete($id)
    {
        $authorizedFactFinding = $this->findAuthorizedFactFinding($id);

        try {
            DB::transaction(function () use ($authorizedFactFinding) {
                $factFinding = Factfinding::query()
                    ->whereKey($authorizedFactFinding->id)
                    ->where('client_id', $authorizedFactFinding->client_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                CaseActivity::query()
                    ->where('client_id', $factFinding->client_id)
                    ->where('module', 'factfinding')
                    ->delete();

                $factFinding->documents()->detach();
                $factFinding->delete();
            }, 3);
        } catch (Throwable $e) {
            report($e);

            return back()->with(
                'error',
                'ไม่สามารถลบข้อมูลได้ในขณะนี้ กรุณาลองใหม่อีกครั้ง'
            );
        }

        return back()->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    /**
     * โหลดผู้รับบริการที่ผู้ใช้งานมีสิทธิ์เข้าถึง
     *
     * ไม่ select เฉพาะ clients.id เพราะ Layout, header และเมนูผู้รับบริการ
     * อาจต้องใช้ชื่อ วันเกิด บ้าน โครงการ และคำนำหน้า
     */
    private function findAuthorizedClient($clientId): Client
    {
        return Client::forUser(auth()->user())
            ->with([
                'title:id,title_name',
                'house:id,house_name',
                'project:id,project_name',
            ])
            ->findOrFail($clientId);
    }

    /**
     * ค้นหาข้อมูลสอบข้อเท็จจริงที่ผู้ใช้งานมีสิทธิ์เข้าถึง
     */
    private function findAuthorizedFactFinding($factfindingId): Factfinding
    {
        return Factfinding::query()
            ->whereKey($factfindingId)
            ->whereIn(
                'client_id',
                Client::forUser(auth()->user())->select('clients.id')
            )
            ->firstOrFail();
    }

    /**
     * โหลดข้อมูลตัวเลือกเฉพาะคอลัมน์ที่หน้าแบบฟอร์มใช้งาน
     */
    private function loadFormOptions(): array
    {
        $documents = Document::query()
            ->select(['id', 'document_name'])
            ->orderBy('document_name')
            ->get();

        $maritals = Marital::query()
            ->select(['id', 'marital_name'])
            ->orderBy('marital_name')
            ->get();

        return [$documents, $maritals];
    }

    /**
     * กฎ Validation ใช้ร่วมกันระหว่างเพิ่มและแก้ไข
     */
    private function validationRules(): array
    {
        $today = now('Asia/Bangkok')->toDateString();

        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],

            'date' => [
                'required',
                'date',
                'before_or_equal:' . $today,
            ],

            'receive_date' => [
                'required',
                'date',
                'before_or_equal:' . $today,
            ],

            'fact_name' => ['required', 'string', 'max:255'],

            'appearance' => ['nullable', 'string', 'max:255'],
            'skin' => ['nullable', 'string', 'max:255'],
            'scar' => ['nullable', 'string', 'max:255'],
            'disability' => ['nullable', 'string', 'max:255'],
            'evidence' => ['nullable', 'string', 'max:2000'],

            'sick' => ['required', 'in:0,1'],
            'sick_detail' => ['nullable', 'required_if:sick,1', 'string', 'max:5000'],
            'treatment' => ['nullable', 'string', 'max:255'],
            'hospital' => ['nullable', 'string', 'max:255'],

            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'blood_group' => ['nullable', 'string', 'max:20'],

            'hygiene' => ['nullable', 'string', 'max:255'],
            'oral_health' => ['nullable', 'string', 'max:255'],
            'injury' => ['nullable', 'string', 'max:1000'],

            'marital_id' => ['required', 'integer', 'exists:maritals,id'],

            'relation_parent' => ['nullable', 'string', 'max:10000'],
            'relation_family' => ['nullable', 'string', 'max:10000'],
            'relation_child' => ['nullable', 'string', 'max:10000'],
            'ex_conditions' => ['nullable', 'string', 'max:10000'],
            'in_conditions' => ['nullable', 'string', 'max:10000'],
            'environment' => ['nullable', 'string', 'max:10000'],
            'cause_problem' => ['nullable', 'string', 'max:10000'],
            'need' => ['nullable', 'string', 'max:10000'],
            'information' => ['nullable', 'string', 'max:10000'],
            'diagnosis' => ['nullable', 'string', 'max:10000'],
            'case_history' => ['nullable', 'string', 'max:10000'],

            'active' => ['nullable', 'boolean'],

            'documents' => ['nullable', 'array'],
            'documents.*' => [
                'integer',
                'distinct',
                'exists:documents,id',
            ],
        ];
    }

    /**
     * ข้อความ Validation ภาษาไทย
     */
    private function validationMessages(): array
    {
        return [
            'client_id.required' => 'กรุณาเลือกผู้รับบริการ',
            'client_id.integer' => 'รหัสผู้รับบริการต้องเป็นตัวเลข',
            'client_id.exists' => 'ไม่พบข้อมูลผู้รับบริการ',

            'date.required' => 'กรุณากรอกวันที่นำส่ง',
            'date.date' => 'รูปแบบวันที่นำส่งไม่ถูกต้อง',
            'date.before_or_equal' => 'วันที่นำส่งต้องไม่เกินวันที่ปัจจุบัน',

            'receive_date.required' => 'กรุณากรอกวันที่บันทึก',
            'receive_date.date' => 'รูปแบบวันที่บันทึกไม่ถูกต้อง',
            'receive_date.before_or_equal' => 'วันที่บันทึกต้องไม่เกินวันที่ปัจจุบัน',

            'fact_name.required' => 'กรุณากรอกชื่อผู้นำส่ง',
            'fact_name.string' => 'ชื่อผู้นำส่งต้องเป็นข้อความ',
            'fact_name.max' => 'ชื่อผู้นำส่งต้องไม่เกิน 255 ตัวอักษร',

            '*.string' => 'ข้อมูลต้องเป็นข้อความ',
            '*.max' => 'ข้อมูลมีความยาวเกินกว่าที่ระบบกำหนด',

            'sick.required' => 'กรุณาระบุสถานะการเจ็บป่วย',
            'sick.in' => 'สถานะการเจ็บป่วยไม่ถูกต้อง',
            'sick_detail.required_if' => 'กรุณากรอกรายละเอียดการเจ็บป่วย',

            'weight.numeric' => 'น้ำหนักต้องเป็นตัวเลขเท่านั้น',
            'weight.min' => 'น้ำหนักต้องไม่น้อยกว่า 0 กิโลกรัม',
            'weight.max' => 'น้ำหนักต้องไม่เกิน 500 กิโลกรัม',

            'height.numeric' => 'ส่วนสูงต้องเป็นตัวเลขเท่านั้น',
            'height.min' => 'ส่วนสูงต้องไม่น้อยกว่า 0 เซนติเมตร',
            'height.max' => 'ส่วนสูงต้องไม่เกิน 300 เซนติเมตร',


            'marital_id.required' => 'กรุณาเลือกสถานภาพสมรส',
            'marital_id.integer' => 'สถานภาพสมรสไม่ถูกต้อง',
            'marital_id.exists' => 'ไม่พบสถานภาพสมรสที่เลือก',

            'documents.array' => 'เอกสารต้องอยู่ในรูปแบบรายการ',
            'documents.*.integer' => 'รหัสเอกสารต้องเป็นตัวเลข',
            'documents.*.distinct' => 'พบรายการเอกสารซ้ำ',
            'documents.*.exists' => 'ไม่พบเอกสารที่เลือก',
        ];
    }

    /**
     * เตรียมข้อมูลก่อนสร้างหรืออัปเดต
     */
    private function preparePayload(
        array $validated,
        int $clientId,
        ?Factfinding $current = null
    ): array {
        unset($validated['client_id'], $validated['documents']);

        $validated['client_id'] = $clientId;
        $validated['sick'] = (int) $validated['sick'];

        if ($validated['sick'] === 0) {
            $validated['sick_detail'] = null;
        }

        $validated['recorder'] = auth()->user()->name ?? 'ไม่ระบุผู้บันทึก';

        if (!array_key_exists('active', $validated)) {
            $validated['active'] = $current?->active ?? true;
        }

        return $validated;
    }

    /**
     * ทำความสะอาดรหัสเอกสารก่อน sync
     */
    private function normalizeDocumentIds(array $documentIds): array
    {
        return collect($documentIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * บันทึกกิจกรรมล่าสุดของโมดูลสอบข้อเท็จจริง
     */
    private function recordActivity(
        int $clientId,
        int $factFindingId,
        string $title,
        string $description,
        string $icon
    ): void {
        CaseActivity::query()
            ->where('client_id', $clientId)
            ->where('module', 'factfinding')
            ->delete();

        CaseActivity::record([
            'client_id' => $clientId,
            'module' => 'factfinding',
            'type' => 'success',
            'title' => $title,
            'description' => $description,
            'occurred_at' => now('Asia/Bangkok'),
            'icon' => $icon,
            'url' => route('factfinding.edit', $factFindingId),
        ]);
    }
}