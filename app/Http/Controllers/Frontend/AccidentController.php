<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Accident;
use App\Models\CaseActivity;

class AccidentController extends Controller
{
    public function AccidentAdd($client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $accidents = Accident::where('client_id', $client->id)
            ->orderByDesc('incident_date')
            ->orderByDesc('id')
            ->get();

        $accident = null;

        return view('frontend.client.accident.index', compact(
            'client',
            'client_id',
            'accidents',
            'accident'
        ));
    }

    public function AccidentStore(Request $request)
    {
        $validated = $this->validateAccident($request);

        if (($validated['treat_no'] ?? null) === 'ไม่พบแพทย์') {
            $validated['hospital'] = null;
            $validated['diagnosis'] = null;
            $validated['appointment'] = null;
        }

        Accident::create($validated);

            CaseActivity::where('client_id', $validated['client_id'])
            ->where('module', 'accident')
            ->delete();

            CaseActivity::record([
            'client_id'   => $validated['client_id'],
            'module'      => 'accident',
            'type'        => 'warning',
            'title'       => 'บันทึกการบาดเจ็บ',
            'description' => 'วันที่เกิดเหตุ: ' . ($validated['incident_date'] ?? '-') .
                            ' | สถานที่: ' . ($validated['location'] ?? '-') .
                            ' | การรักษา: ' . ($validated['treat_no'] ?? '-'),
             'occurred_at' => $validated['incident_date'] ?? now('Asia/Bangkok'),
            'icon'        => 'bi-bandaid',
            'url'         => route('accident.add', $validated['client_id']),
        ]);

        return redirect()
            ->route('accident.add', $validated['client_id'])
            ->with([
                'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function AccidentEdit($id)
    {
        $accident = Accident::with('client')
            ->where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->firstOrFail();

        $client = $accident->client;

        $accidents = Accident::where('client_id', $client->id)
            ->orderByDesc('incident_date')
            ->orderByDesc('id')
            ->get();

        return view('frontend.client.accident.index', compact(
            'client',
            'accidents',
            'accident'
        ))->with('client_id', $client->id);
    }

   public function AccidentUpdate(Request $request, $id)
{
    $accident = Accident::where('id', $id)
        ->whereHas('client', function ($q) {
            $q->forUser(auth()->user());
        })
        ->firstOrFail();

    $validated = $this->validateAccident($request);

    if (($validated['treat_no'] ?? null) === 'ไม่พบแพทย์') {
        $validated['hospital'] = null;
        $validated['diagnosis'] = null;
        $validated['appointment'] = null;
    }

    $accident->update($validated);

    CaseActivity::where('client_id', $accident->client_id)
        ->where('module', 'accident')
        ->delete();

    CaseActivity::record([
        'client_id'   => $accident->client_id,
        'module'      => 'accident',
        'type'        => 'warning',
        'title'       => 'แก้ไขการบาดเจ็บ',
        'description' => 'วันที่เกิดเหตุ: ' . ($validated['incident_date'] ?? '-') .
                        ' | สถานที่: ' . ($validated['location'] ?? '-') .
                        ' | การรักษา: ' . ($validated['treat_no'] ?? '-'),
        'occurred_at' => $validated['incident_date'] ?? now('Asia/Bangkok'),
        'icon'        => 'bi-bandaid',
        'url'         => route('accident.add', $accident->client_id),
    ]);

    return redirect()
        ->route('accident.add', $accident->client_id)
        ->with([
            'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว',
            'alert-type' => 'success',
        ]);
}

    public function AccidentDelete($id)
    {
        $accident = Accident::where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->firstOrFail();

        $clientId = $accident->client_id;

        CaseActivity::where('client_id', $clientId)
            ->where('module', 'accident')
            ->delete();

        $accident->delete();

        return redirect()
            ->route('accident.add', $clientId)
            ->with([
                'message' => 'ลบข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    public function AccidentReport($id)
    {
        $accident = Accident::with('client')
            ->where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->firstOrFail();

        $client = $accident->client;

        return view('frontend.client.accident.report', compact('accident', 'client'));
    }

    private function validateAccident(Request $request): array
    {
        $validated = $request->validate([
            'client_id'     => 'required|exists:clients,id',
            'incident_date' => 'required|date',
            'location'      => 'required|string|max:255',
            'eyewitness'    => 'nullable|string|max:255',
            'detail'        => 'required|string',
            'cause'         => 'required|string|max:255',
            'treat_no'      => 'required|in:พบแพทย์,ไม่พบแพทย์',
            'hospital'      => 'nullable|string|max:255',
            'diagnosis'     => 'nullable|string|max:255',
            'appointment'   => 'nullable|date',
            'protection'    => 'nullable|string|max:1000',
            'treatment'     => 'nullable|string|max:1000',
            'caretaker'     => 'nullable|string|max:255',
            'record_date'   => 'required|date',
        ], [
            'client_id.required'     => 'กรุณาเลือกผู้รับบริการ',
            'client_id.exists'       => 'ผู้รับบริการที่เลือกไม่ถูกต้อง',
            'incident_date.required' => 'กรุณาระบุวันที่เกิดเหตุ',
            'incident_date.date'     => 'วันที่เกิดเหตุต้องเป็นรูปแบบวันที่',
            'location.required'      => 'กรุณาระบุสถานที่เกิดเหตุ',
            'location.max'           => 'สถานที่เกิดเหตุต้องไม่เกิน 255 ตัวอักษร',
            'detail.required'        => 'กรุณาระบุรายละเอียดการบาดเจ็บ',
            'cause.required'         => 'กรุณาระบุสาเหตุของการบาดเจ็บ',
            'cause.max'              => 'สาเหตุต้องไม่เกิน 255 ตัวอักษร',
            'treat_no.required'      => 'กรุณาเลือกการพบแพทย์',
            'treat_no.in'            => 'ค่าการพบแพทย์ไม่ถูกต้อง',
            'hospital.max'           => 'ชื่อสถานพยาบาลต้องไม่เกิน 255 ตัวอักษร',
            'diagnosis.max'          => 'ผลวินิจฉัยต้องไม่เกิน 255 ตัวอักษร',
            'appointment.date'       => 'วันที่นัดครั้งต่อไปต้องเป็นรูปแบบวันที่',
            'protection.max'         => 'ข้อมูลการป้องกัน/การแก้ไขยาวเกินไป',
            'treatment.max'          => 'ข้อมูลการรักษายาวเกินไป',
            'caretaker.max'          => 'ชื่อผู้ดูแลต้องไม่เกิน 255 ตัวอักษร',
            'record_date.required'   => 'กรุณาระบุวันที่บันทึก',
            'record_date.date'       => 'วันที่บันทึกต้องเป็นรูปแบบวันที่',
        ]);

        Client::forUser(auth()->user())->findOrFail($validated['client_id']);

        return $validated;
    }
}