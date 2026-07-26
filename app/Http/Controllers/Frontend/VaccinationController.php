<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Client;
use App\Models\Vaccination;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\CaseActivity;

class VaccinationController extends Controller
{
    public function VaccineShow(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
        ], [
            'end_date.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
        ]);

        $query = $client->vaccinations()->orderByDesc('date')->orderByDesc('id');

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $vaccinations = $query->get();

        return view('frontend.client.vaccine.vaccine_show', compact('client', 'vaccinations'));
    }

  public function VaccineStore(Request $request)
        {
            $validated = $request->validate([
                'client_id'    => ['required', 'integer', 'exists:clients,id'],
                'date'         => [
                    'required',
                    'date',
                    Rule::unique('vaccinations')->where(function ($query) use ($request) {
                        return $query->where('client_id', $request->client_id);
                    }),
                ],
                'vaccine_name' => 'required|string|max:255',
                'hospital'     => 'nullable|string|max:255',
                'recorder'     => 'nullable|string|max:255',
                'remark'       => 'nullable|string|max:500',
            ], [
                'client_id.required'    => 'กรุณาระบุรหัสผู้รับบริการ',
                'client_id.exists'      => 'รหัสผู้รับบริการไม่ถูกต้อง',
                'date.required'         => 'กรุณากรอกวันที่รับวัคซีน',
                'date.date'             => 'วันที่รับวัคซีนไม่ถูกต้อง',
                'date.unique'           => 'เด็กคนนี้มีการบันทึกวันที่รับวัคซีนนี้แล้ว',
                'vaccine_name.required' => 'กรุณากรอกชนิดวัคซีน',
                'vaccine_name.max'      => 'ชนิดวัคซีนต้องไม่เกิน 255 ตัวอักษร',
                'hospital.max'          => 'ชื่อสถานพยาบาลต้องไม่เกิน 255 ตัวอักษร',
                'recorder.max'          => 'ชื่อเจ้าหน้าที่ต้องไม่เกิน 255 ตัวอักษร',
                'remark.max'            => 'หมายเหตุต้องไม่เกิน 500 ตัวอักษร',
            ]);

            $client = Client::forUser(auth()->user())->findOrFail($validated['client_id']);
            $validated['client_id'] = $client->id;

            $vaccination = Vaccination::create($validated);

            CaseActivity::where('client_id', $client->id)
                ->where('module', 'vaccine')
                ->delete();

            CaseActivity::record([
                'client_id'   => $client->id,
                'module'      => 'vaccine',
                'type'        => 'success',
                'title'       => 'บันทึกประวัติการรับวัคซีน',
                'description' =>
                    'วันที่รับวัคซีน: ' . ($validated['date'] ?? '-') .
                    ' | ชนิดวัคซีน: ' . ($validated['vaccine_name'] ?? '-') .
                    ' | สถานพยาบาล: ' . ($validated['hospital'] ?? '-'),

                    'occurred_at' => $validated['date'] ?? now('Asia/Bangkok'),'occurred_at' => now(),

                'icon'        => 'bi-shield-plus',

                'url'         => route('vaccine.index', [
                    'client_id' => $client->id
                ]),
            ]);

            return redirect()->route('vaccine.index', ['client_id' => $validated['client_id']])
                            ->with(['message' => 'บันทึกข้อมูลเรียบร้อยแล้ว', 'alert-type' => 'success']);
        }

    public function VaccineEdit($id)
    {
        $vaccination = Vaccination::whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        return response()->json([
            'id'           => $vaccination->id,
            'date'         => $vaccination->date,
            'vaccine_name' => $vaccination->vaccine_name,
            'hospital'     => $vaccination->hospital ?? 'ไม่ระบุ',
            'remark'       => $vaccination->remark ?? '',
            'recorder'     => $vaccination->recorder ?? '',
            'client_id'    => $vaccination->client_id,
        ]);
    }

    public function VaccineUpdate(Request $request, $id)
    {
        $vaccination = Vaccination::whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'client_id'    => ['required', 'integer', 'exists:clients,id'],
            'date'         => [
                'required',
                'date',
                Rule::unique('vaccinations')
                    ->where(function ($query) use ($vaccination) {
                        return $query->where('client_id', $vaccination->client_id);
                    })
                    ->ignore($vaccination->id, 'id'),
            ],
            'vaccine_name' => 'required|string|max:255',
            'hospital'     => 'nullable|string|max:255',
            'recorder'     => 'nullable|string|max:255',
            'remark'       => 'nullable|string|max:500',
        ], [
            'client_id.required'    => 'กรุณาระบุรหัสผู้รับบริการ',
            'client_id.exists'      => 'รหัสผู้รับบริการไม่ถูกต้อง',
            'date.required'         => 'กรุณากรอกวันที่รับวัคซีน',
            'date.date'             => 'วันที่รับวัคซีนไม่ถูกต้อง',
            'date.unique'           => 'เด็กคนนี้มีการบันทึกวันที่รับวัคซีนนี้แล้ว',
            'vaccine_name.required' => 'กรุณากรอกชนิดวัคซีน',
            'vaccine_name.max'      => 'ชนิดวัคซีนต้องไม่เกิน 255 ตัวอักษร',
            'hospital.max'          => 'ชื่อสถานพยาบาลต้องไม่เกิน 255 ตัวอักษร',
            'recorder.max'          => 'ชื่อเจ้าหน้าที่ต้องไม่เกิน 255 ตัวอักษร',
            'remark.max'            => 'หมายเหตุต้องไม่เกิน 500 ตัวอักษร',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with(['edit_mode' => true, 'edit_id' => $id]);
        }

        $data = $validator->validated();

        $data['client_id'] = $vaccination->client_id;

        Client::forUser(auth()->user())->findOrFail($data['client_id']);

        $vaccination->update($data);

        CaseActivity::where('client_id', $vaccination->client_id)
            ->where('module', 'vaccine')
            ->delete();

        CaseActivity::record([
            'client_id'   => $vaccination->client_id,
            'module'      => 'vaccine',
            'type'        => 'success',
            'title'       => 'แก้ไขประวัติการรับวัคซีน',
            'description' =>
                'วันที่รับวัคซีน: ' . ($data['date'] ?? '-') .
                ' | ชนิดวัคซีน: ' . ($data['vaccine_name'] ?? '-') .
                ' | สถานพยาบาล: ' . ($data['hospital'] ?? '-'),

            'occurred_at' => $validated['date'] ?? now('Asia/Bangkok'),

            'icon'        => 'bi-shield-plus',

            'url'         => route('vaccine.index', [
                'client_id' => $vaccination->client_id
            ]),
        ]);

        return redirect()->route('vaccine.index', ['client_id' => $vaccination->client_id])
                         ->with(['message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว', 'alert-type' => 'success']);
    }

    public function VaccineDelete($id)
    {
        $vaccination = Vaccination::whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        $client_id = $vaccination->client_id;

        CaseActivity::where('client_id', $vaccination->client_id)
            ->where('module', 'vaccine')
            ->delete();

            $vaccination->delete();

            return redirect()->route('vaccine.index', ['client_id' => $client_id])
                ->with(['message' => 'ลบข้อมูลเรียบร้อยแล้ว', 'alert-type' => 'success']);
        }

    public function VaccineReport(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
        ], [
            'end_date.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
        ]);

        $query = $client->vaccinations()->orderByDesc('date')->orderByDesc('id');

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $vaccinations = $query->get();

        return view('frontend.client.vaccine.report', compact(
            'client',
            'vaccinations'
        ));
    }
}