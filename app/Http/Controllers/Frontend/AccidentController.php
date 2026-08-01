<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\CaseActivity;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AccidentController extends Controller
{
    public function AccidentAdd($client_id): View
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $accidents = $this->getClientAccidents($client->id);
        $accident = null;

        return view('frontend.client.accident.index', compact(
            'client',
            'client_id',
            'accidents',
            'accident'
        ));
    }

    public function AccidentStore(Request $request): RedirectResponse
    {
        $validated = $this->validateAccident($request);

        // ตรวจสิทธิ์จากผู้รับบริการจริงอีกชั้น ไม่เชื่อค่า client_id จาก hidden input เพียงอย่างเดียว
        $client = Client::forUser(auth()->user())
            ->findOrFail($validated['client_id']);

        $validated['client_id'] = $client->id;
        $validated = $this->normalizeAccidentData($validated);

        DB::transaction(function () use ($validated, $client): void {
            Accident::create($validated);
            $this->syncLatestCaseActivity($client->id);
        });

        return redirect()
            ->route('accident.add', $client->id)
            ->with([
                'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function AccidentEdit($id): View
    {
        $accident = $this->findAccessibleAccident($id, true);
        $client = $accident->client;
        $accidents = $this->getClientAccidents($client->id);

        return view('frontend.client.accident.index', compact(
            'client',
            'accidents',
            'accident'
        ))->with('client_id', $client->id);
    }

    public function AccidentUpdate(Request $request, $id): RedirectResponse
    {
        $accident = $this->findAccessibleAccident($id);
        $validated = $this->validateAccident($request);

        // ห้ามย้ายรายการไปยังผู้รับบริการรายอื่นด้วยการแก้ hidden input
        $validated['client_id'] = $accident->client_id;
        $validated = $this->normalizeAccidentData($validated);

        DB::transaction(function () use ($accident, $validated): void {
            $accident->update($validated);
            $this->syncLatestCaseActivity($accident->client_id);
        });

        return redirect()
            ->route('accident.add', $accident->client_id)
            ->with([
                'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function AccidentDelete($id): RedirectResponse
    {
        $accident = $this->findAccessibleAccident($id);
        $clientId = $accident->client_id;

        DB::transaction(function () use ($accident, $clientId): void {
            $accident->delete();
            $this->syncLatestCaseActivity($clientId);
        });

        return redirect()
            ->route('accident.add', $clientId)
            ->with([
                'message' => 'ลบข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function AccidentReport($id): View
    {
        $accident = $this->findAccessibleAccident($id, true);
        $client = $accident->client;

        return view('frontend.client.accident.report', compact('accident', 'client'));
    }

    private function validateAccident(Request $request): array
    {
        $today = now('Asia/Bangkok')->toDateString();

        return $request->validate([
            'client_id'     => ['required', 'integer', 'exists:clients,id'],
            'incident_date' => ['required', 'date', 'before_or_equal:' . $today],
            'location'      => ['required', 'string', 'max:255'],
            'eyewitness'    => ['nullable', 'string', 'max:255'],
            'detail'        => ['required', 'string', 'max:3000'],
            'cause'         => ['required', 'string', 'max:255'],
            'treat_no'      => ['required', 'in:พบแพทย์,ไม่พบแพทย์'],
            'hospital'      => ['nullable', 'string', 'max:255'],
            'diagnosis'     => ['nullable', 'string', 'max:255'],
            'appointment'   => ['nullable', 'date', 'after_or_equal:incident_date'],
            'protection'    => ['nullable', 'string', 'max:1000'],
            'treatment'     => ['nullable', 'string', 'max:1000'],
            'caretaker'     => ['nullable', 'string', 'max:255'],
            'record_date'   => [
                'required',
                'date',
                'after_or_equal:incident_date',
                'before_or_equal:' . $today,
            ],
        ], [
            'client_id.required'              => 'กรุณาเลือกผู้รับบริการ',
            'client_id.integer'               => 'ข้อมูลผู้รับบริการไม่ถูกต้อง',
            'client_id.exists'                => 'ผู้รับบริการที่เลือกไม่ถูกต้อง',
            'incident_date.required'          => 'กรุณาระบุวันที่เกิดเหตุ',
            'incident_date.date'              => 'วันที่เกิดเหตุต้องเป็นรูปแบบวันที่',
            'incident_date.before_or_equal'   => 'วันที่เกิดเหตุต้องไม่เกินวันที่ปัจจุบัน',
            'location.required'               => 'กรุณาระบุสถานที่เกิดเหตุ',
            'location.max'                    => 'สถานที่เกิดเหตุต้องไม่เกิน 255 ตัวอักษร',
            'eyewitness.max'                  => 'ชื่อผู้พบเห็นเหตุการณ์ต้องไม่เกิน 255 ตัวอักษร',
            'detail.required'                 => 'กรุณาระบุรายละเอียดการบาดเจ็บ',
            'detail.max'                      => 'รายละเอียดการบาดเจ็บต้องไม่เกิน 3,000 ตัวอักษร',
            'cause.required'                  => 'กรุณาระบุสาเหตุของการบาดเจ็บ',
            'cause.max'                       => 'สาเหตุต้องไม่เกิน 255 ตัวอักษร',
            'treat_no.required'               => 'กรุณาเลือกการพบแพทย์',
            'treat_no.in'                     => 'ค่าการพบแพทย์ไม่ถูกต้อง',
            'hospital.max'                    => 'ชื่อสถานพยาบาลต้องไม่เกิน 255 ตัวอักษร',
            'diagnosis.max'                   => 'ผลวินิจฉัยต้องไม่เกิน 255 ตัวอักษร',
            'appointment.date'                => 'วันที่นัดครั้งต่อไปต้องเป็นรูปแบบวันที่',
            'appointment.after_or_equal'      => 'วันที่นัดครั้งต่อไปต้องไม่น้อยกว่าวันที่เกิดเหตุ',
            'protection.max'                  => 'ข้อมูลการป้องกัน/การแก้ไขต้องไม่เกิน 1,000 ตัวอักษร',
            'treatment.max'                   => 'ข้อมูลการรักษาต้องไม่เกิน 1,000 ตัวอักษร',
            'caretaker.max'                   => 'ชื่อผู้ดูแลต้องไม่เกิน 255 ตัวอักษร',
            'record_date.required'            => 'กรุณาระบุวันที่บันทึก',
            'record_date.date'                => 'วันที่บันทึกต้องเป็นรูปแบบวันที่',
            'record_date.after_or_equal'      => 'วันที่บันทึกต้องไม่น้อยกว่าวันที่เกิดเหตุ',
            'record_date.before_or_equal'     => 'วันที่บันทึกต้องไม่เกินวันที่ปัจจุบัน',
        ]);
    }

    private function normalizeAccidentData(array $validated): array
    {
        $nullableTextFields = [
            'eyewitness',
            'hospital',
            'diagnosis',
            'protection',
            'treatment',
            'caretaker',
        ];

        foreach (['location', 'detail', 'cause', ...$nullableTextFields] as $field) {
            if (!array_key_exists($field, $validated)) {
                continue;
            }

            $value = trim((string) $validated[$field]);
            $validated[$field] = in_array($field, $nullableTextFields, true) && $value === ''
                ? null
                : $value;
        }

        if (($validated['treat_no'] ?? null) === 'ไม่พบแพทย์') {
            $validated['hospital'] = null;
            $validated['diagnosis'] = null;
            $validated['appointment'] = null;
        }

        return $validated;
    }

    private function getClientAccidents(int $clientId)
    {
        return Accident::where('client_id', $clientId)
            ->orderByDesc('incident_date')
            ->orderByDesc('id')
            ->get();
    }

    private function findAccessibleAccident($id, bool $withClient = false): Accident
    {
        $query = Accident::query()
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
     * คง CaseActivity ของโมดูลนี้ไว้หนึ่งรายการ โดยอ้างอิงเหตุการณ์ล่าสุดจริง
     * เมื่อมีการลบ จะสร้างใหม่จากรายการที่ยังเหลืออยู่ หรือไม่สร้างเมื่อไม่มีข้อมูลแล้ว
     */
    private function syncLatestCaseActivity(int $clientId): void
    {
        CaseActivity::where('client_id', $clientId)
            ->where('module', 'accident')
            ->delete();

        $latestAccident = Accident::where('client_id', $clientId)
            ->orderByDesc('incident_date')
            ->orderByDesc('id')
            ->first();

        if (!$latestAccident) {
            return;
        }

        CaseActivity::record([
            'client_id'   => $clientId,
            'module'      => 'accident',
            'type'        => 'warning',
            'title'       => 'บันทึกการบาดเจ็บ',
            'description' => 'วันที่เกิดเหตุ: ' . ($latestAccident->incident_date ?? '-') .
                ' | สถานที่: ' . ($latestAccident->location ?? '-') .
                ' | การรักษา: ' . ($latestAccident->treat_no ?? '-'),
            'occurred_at' => $latestAccident->incident_date ?? now('Asia/Bangkok'),
            'icon'        => 'bi-bandaid',
            'url'         => route('accident.add', $clientId),
        ]);
    }
}
