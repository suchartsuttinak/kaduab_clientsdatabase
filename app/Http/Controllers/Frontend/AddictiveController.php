<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Addictive;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\CaseActivity;

class AddictiveController extends Controller
{
    // โหลดฟอร์ม + ข้อมูลเดิม
    public function AddAddictive($client_id)
    {
        // =========================
        // PATCH: กันเดา URL เข้าถึง client ที่ไม่มีสิทธิ์
        // =========================
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $addictives = Addictive::where('client_id', $client->id)
            ->orderBy('count', 'asc')
            ->get();

        $addictive = null; // สำหรับเพิ่มใหม่

        return view('frontend.client.addictive.addictive_create', compact('client', 'client_id', 'addictives', 'addictive'));
    }

    // บันทึกข้อมูลใหม่
   public function StoreAddictive(Request $request)
        {
            $data = $request->validate([
                'date'       => [
                    'required',
                    'date',
                    Rule::unique('addictives')->where(function ($query) use ($request) {
                        return $query->where('client_id', $request->client_id);
                    }),
                ],
                'exam'       => 'required|in:0,1',
                'refer'      => 'nullable|in:1,2',
                'record'     => 'nullable|string',
                'recorder'   => 'required|string|max:255',
                'client_id'  => 'required|exists:clients,id',
            ], [
                'date.required'      => 'กรุณาระบุวันที่ตรวจ',
                'date.date'          => 'รูปแบบวันที่ไม่ถูกต้อง',
                'date.unique'        => 'วันที่นี้ถูกบันทึกแล้วสำหรับผู้รับบริการรายนี้',
                'exam.required'      => 'กรุณาเลือกผลการตรวจ',
                'exam.in'            => 'ค่าที่เลือกไม่ถูกต้อง',
                'refer.in'           => 'ค่าการส่งต่อไม่ถูกต้อง',
                'record.string'      => 'บันทึกผลต้องเป็นข้อความ',
                'recorder.required'  => 'กรุณาระบุชื่อผู้ตรวจ',
                'recorder.string'    => 'ชื่อผู้ตรวจต้องเป็นข้อความ',
                'recorder.max'       => 'ชื่อผู้ตรวจต้องไม่เกิน 255 ตัวอักษร',
                'client_id.required' => 'ไม่พบรหัสผู้รับบริการ',
                'client_id.exists'   => 'รหัสผู้รับบริการไม่ถูกต้อง',
            ]);

            $client = Client::forUser(auth()->user())->findOrFail($data['client_id']);
            $data['client_id'] = $client->id;

            $latestCount = Addictive::where('client_id', $client->id)->max('count') ?? 0;
            $nextCount = $latestCount + 1;

            $exists = Addictive::where('client_id', $client->id)
                ->where('count', $nextCount)
                ->exists();

            if ($exists) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'ไม่สามารถบันทึกข้อมูลซ้ำได้ (ครั้งที่ ' . $nextCount . ' มีอยู่แล้ว)',
                        'errors' => []
                    ]);
                }

                return redirect()->back()->withInput()->with([
                    'message' => 'ไม่สามารถบันทึกข้อมูลซ้ำได้ (ครั้งที่ ' . $nextCount . ' มีอยู่แล้ว)',
                    'alert-type' => 'error'
                ]);
            }

            $this->validateDateOrderByCount(
                clientId: $client->id,
                currentCount: $nextCount,
                inputDate: $data['date']
            );

            $data['count'] = $nextCount;

            if ($data['exam'] == 0) {
                $data['refer'] = null;
            }

            $addictive = Addictive::create($data);

                CaseActivity::where('client_id', $client->id)
                ->where('module', 'addictive')
                ->delete();

                CaseActivity::record([
                'client_id'   => $client->id,
                'module'      => 'addictive',
                'type'        => ($data['exam'] == 1) ? 'warning' : 'success',
                'title'       => 'บันทึกการตรวจสารเสพติด',
                'description' => 'วันที่ตรวจ: ' . ($data['date'] ?? '-') .
                                ' | ครั้งที่ ' . ($addictive->count ?? '-') .
                                ' | ผลตรวจ: ' . (($data['exam'] == 1) ? 'พบสารเสพติด' : 'ไม่พบสารเสพติด') .
                                ' | ผู้ตรวจ: ' . ($data['recorder'] ?? '-'),
                'occurred_at' => $data['date'] ?? now('Asia/Bangkok'),
                'icon'        => 'bi-clipboard2-pulse',
                'url'         => route('addictive.create', $client->id),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                    'data' => $addictive
                ]);
            }

            return redirect()->back()->with([
                'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success'
            ]);
        }

    // โหลดข้อมูลเดิมสำหรับแก้ไข (JSON)
    public function EditAddictiveJson($id)
    {
        $addictive = Addictive::where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->firstOrFail();

        return response()->json([
            'id'       => $addictive->id,
            'date'     => \Carbon\Carbon::parse($addictive->date)->format('Y-m-d'),
            'count'    => $addictive->count,
            'exam'     => $addictive->exam,
            'refer'    => $addictive->refer,
            'record'   => $addictive->record,
            'recorder' => $addictive->recorder,
        ]);
    }

    // อัปเดตข้อมูล
    public function UpdateAddictive(Request $request, $id)
    {
        $addictive = Addictive::where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->firstOrFail();

        $data = $request->validate([
            'date'       => [
                'required',
                'date',
                Rule::unique('addictives')->where(function ($query) use ($request) {
                    return $query->where('client_id', $request->client_id);
                })->ignore($id),
            ],
            'exam'       => 'required|in:0,1',
            'refer'      => 'nullable|in:1,2',
            'record'     => 'nullable|string',
            'recorder'   => 'required|string|max:255',
            'client_id'  => 'required|exists:clients,id',
        ], [
            'date.required'      => 'กรุณาระบุวันที่ตรวจ',
            'date.date'          => 'รูปแบบวันที่ไม่ถูกต้อง',
            'date.unique'        => 'วันที่นี้ถูกบันทึกแล้วสำหรับผู้รับบริการรายนี้',
            'exam.required'      => 'กรุณาเลือกผลการตรวจ',
            'exam.in'            => 'ค่าที่เลือกไม่ถูกต้อง',
            'refer.in'           => 'ค่าการส่งต่อไม่ถูกต้อง',
            'record.string'      => 'บันทึกผลต้องเป็นข้อความ',
            'recorder.required'  => 'กรุณาระบุชื่อผู้ตรวจ',
            'recorder.string'    => 'ชื่อผู้ตรวจต้องเป็นข้อความ',
            'recorder.max'       => 'ชื่อผู้ตรวจต้องไม่เกิน 255 ตัวอักษร',
            'client_id.required' => 'ไม่พบรหัสผู้รับบริการ',
            'client_id.exists'   => 'รหัสผู้รับบริการไม่ถูกต้อง',
        ]);

        // =========================
        // PATCH: กันเปลี่ยน client_id ไป client อื่น
        // =========================
        Client::forUser(auth()->user())->findOrFail($data['client_id']);

        // =========================
        // PATCH: ตอนแก้ไขก็ต้องคงกฎ count/date เช่นกัน
        // - ต้องมากกว่าครั้งก่อนหน้า
        // - ต้องน้อยกว่าครั้งถัดไป
        // =========================
        $this->validateDateOrderByCount(
            clientId: $data['client_id'],
            currentCount: $addictive->count,
            inputDate: $data['date'],
            ignoreId: $addictive->id
        );

        if ($data['exam'] == 0) {
            $data['refer'] = null;
        }

        $addictive->update($data);

        CaseActivity::where('client_id', $addictive->client_id)
            ->where('module', 'addictive')
            ->delete();

        CaseActivity::record([
            'client_id'   => $addictive->client_id,
            'module'      => 'addictive',
            'type'        => ($data['exam'] == 1) ? 'warning' : 'success',
            'title'       => 'แก้ไขการตรวจสารเสพติด',
            'description' => 'วันที่ตรวจ: ' . ($data['date'] ?? '-') .
                            ' | ครั้งที่ ' . ($addictive->count ?? '-') .
                            ' | ผลตรวจ: ' . (($data['exam'] == 1) ? 'พบสารเสพติด' : 'ไม่พบสารเสพติด') .
                            ' | ผู้ตรวจ: ' . ($data['recorder'] ?? '-'),
            'occurred_at' => $data['date'] ?? now('Asia/Bangkok'),
            'icon'        => 'bi-clipboard2-pulse',
            'url'         => route('addictive.create', $addictive->client_id),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'อัปเดตข้อมูลเรียบร้อยแล้ว',
                'data' => $addictive
            ]);
        }

        return redirect()->route('addictive.create', $addictive->client_id)
            ->with([
                'message' => 'อัปเดตข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success'
            ]);
    }

    // ลบข้อมูล
    public function DeleteAddictive($id)
    {
        $addictive = Addictive::where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->firstOrFail();

        $clientId = $addictive->client_id;

        // เก็บ count เดิมไว้ก่อนลบ
        $deletedCount = $addictive->count;

        CaseActivity::where('client_id', $clientId)
        ->where('module', 'addictive')
        ->delete();

        $addictive->delete();

        // =========================
        // PATCH: เมื่อมีการลบ ให้เลื่อนครั้งถัดไปขึ้นมาแทน
        // เช่น ลบครั้งที่ 1 -> ครั้งที่ 2 กลายเป็น 1, ครั้งที่ 3 กลายเป็น 2
        // =========================
        Addictive::where('client_id', $clientId)
            ->where('count', '>', $deletedCount)
            ->decrement('count');

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'ลบข้อมูลเรียบร้อยแล้ว'
            ]);
        }

        return redirect()->route('addictive.create', $clientId)
            ->with([
                'message' => 'ลบข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success'
            ]);
    }

    // หน้ารายงาน
    public function ReportAddictive($id)
    {
        $addictive = Addictive::with('client')
            ->where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->firstOrFail();

        $client = $addictive->client;

        return view('frontend.client.addictive.addictive_report', compact('client', 'addictive'));
    }

    /**
     * ตรวจสอบลำดับวันที่ตามครั้ง
     * กติกา:
     * - ครั้งที่มากกว่า ต้องมีวันที่มากกว่าครั้งที่น้อยกว่า
     * - ตอนแก้ไข ต้องตรวจทั้งก่อนหน้าและถัดไป
     */
    private function validateDateOrderByCount(int $clientId, int $currentCount, string $inputDate, ?int $ignoreId = null): void
    {
        $input = Carbon::parse($inputDate)->startOfDay();

        // หา record ก่อนหน้า (count น้อยกว่า currentCount ที่มากที่สุด)
        $previous = Addictive::where('client_id', $clientId)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('count', '<', $currentCount)
            ->orderBy('count', 'desc')
            ->first();

        if ($previous && $previous->date) {
            $previousDate = Carbon::parse($previous->date)->startOfDay();

            if ($input->lte($previousDate)) {
                throw ValidationException::withMessages([
                    'date' => 'วันที่ของครั้งที่ ' . $currentCount . ' ต้องมากกว่าวันที่ของครั้งที่ ' . $previous->count . ' (' . $previousDate->format('d/m/') . ($previousDate->year + 543) . ')'
                ]);
            }
        }

        // หา record ถัดไป (count มากกว่า currentCount ที่น้อยที่สุด)
        $next = Addictive::where('client_id', $clientId)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('count', '>', $currentCount)
            ->orderBy('count', 'asc')
            ->first();

        if ($next && $next->date) {
            $nextDate = Carbon::parse($next->date)->startOfDay();

            if ($input->gte($nextDate)) {
                throw ValidationException::withMessages([
                    'date' => 'วันที่ของครั้งที่ ' . $currentCount . ' ต้องน้อยกว่าวันที่ของครั้งที่ ' . $next->count . ' (' . $nextDate->format('d/m/') . ($nextDate->year + 543) . ')'
                ]);
            }
        }
    }

    // รายงานทั้งหมดของผู้รับบริการ + filter ช่วงวันที่
    public function ReportAddictiveAll(Request $request, $client_id)
    {
        // =========================
        // PATCH: กันเดา URL เข้าถึง client ที่ไม่มีสิทธิ์
        // =========================
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $request->validate([
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
        ], [
            'date_from.date'         => 'รูปแบบวันที่เริ่มต้นไม่ถูกต้อง',
            'date_to.date'           => 'รูปแบบวันที่สิ้นสุดไม่ถูกต้อง',
            'date_to.after_or_equal' => 'วันที่สิ้นสุดต้องมากกว่าหรือเท่ากับวันที่เริ่มต้น',
        ]);

        $query = Addictive::where('client_id', $client->id);

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $addictives = $query->orderBy('count', 'asc')->get();

        return view('frontend.client.addictive.addictive_report_all', compact(
            'client',
            'addictives'
        ));
    }
}