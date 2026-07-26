<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Followup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class FollowupController extends Controller
{
    public function index(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $validator = Validator::make($request->all(), [
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
        ], [
            'date_to.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
        ]);

        if ($validator->fails()) {
            return redirect()->route('followup.index', $client->id)
                ->withErrors($validator)
                ->withInput();
        }

        $query = Followup::where('client_id', $client->id);

        if ($request->filled('date_from')) {
            $query->whereDate('followup_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('followup_date', '<=', $request->date_to);
        }

        $followups = $query->latest('followup_date')
            ->latest('id')
            ->get();

        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;

        return view('frontend.client.followup.index', compact(
            'client',
            'followups',
            'dateFrom',
            'dateTo'
        ));
    }

    public function store(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $this->authorizeManage();

        $validator = Validator::make($request->all(), [
          'followup_date' => [
            'required',
            'date',
            'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
        ],

            'assistance_detail'  => 'required|string',
            'note'               => 'nullable|string',
        ], [
            'followup_date.required'        => 'กรุณาเลือกวันเดือนปี',
            'followup_date.date'            => 'รูปแบบวันเดือนไม่ถูกต้อง',
            'followup_date.before_or_equal' => 'วันที่ติดตามต้องไม่เกินวันปัจจุบัน',

            'assistance_detail.required'    => 'กรุณากรอกการช่วยเหลือและติดตามผล',
        ]);

        if ($validator->fails()) {
            return redirect()->route('followup.index', $client->id)
                ->withErrors($validator)
                ->withInput()
                ->with('followup_modal', 'create');
        }

        $data = $validator->validated();

        try {
            Followup::create([
                'client_id'         => $client->id,
                'followup_date'     => $data['followup_date'],
                'assistance_detail' => $data['assistance_detail'],
                'note'              => $data['note'] ?? null,
            ]);
        } catch (\Throwable $e) {
            return redirect()->route('followup.index', $client->id)
                ->withInput()
                ->with('followup_modal', 'create')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('followup.index', $client->id)
            ->with('success', 'บันทึกข้อมูลติดตามผลเรียบร้อยแล้ว');
    }

    public function update(Request $request, $id)
    {
        // PATCH: กันเดา URL ตั้งแต่ query แรก
        $followup = Followup::whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        $client = Client::forUser(auth()->user())->findOrFail($followup->client_id);

        $this->authorizeManage();

        $validator = Validator::make($request->all(), [
          'followup_date' => [
            'required',
            'date',
            'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
        ],

            'assistance_detail'  => 'required|string',
            'note'               => 'nullable|string',
        ], [
            'followup_date.required'        => 'กรุณาเลือกวันเดือนปี',
            'followup_date.date'            => 'รูปแบบวันเดือนไม่ถูกต้อง',
            'followup_date.before_or_equal' => 'วันที่ติดตามต้องไม่เกินวันปัจจุบัน',
            'assistance_detail.required'    => 'กรุณากรอกการช่วยเหลือและติดตามผล',
        ]);

        if ($validator->fails()) {
            return redirect()->route('followup.index', $client->id)
                ->withErrors($validator)
                ->withInput()
                ->with('followup_modal', 'edit')
                ->with('followup_edit_id', $followup->id);
        }

        $followup->update([
            'followup_date'     => $request->followup_date,
            'assistance_detail' => $request->assistance_detail,
            'note'              => $request->note,
        ]);

        return redirect()->route('followup.index', $client->id)
            ->with('success', 'แก้ไขข้อมูลติดตามผลเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        // PATCH: กันเดา URL ตั้งแต่ query แรก
        $followup = Followup::whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        $client = Client::forUser(auth()->user())->findOrFail($followup->client_id);

        $this->authorizeDelete();

        $followup->delete();

        return redirect()->route('followup.index', $client->id)
            ->with('success', 'ลบข้อมูลติดตามผลเรียบร้อยแล้ว');
    }

    public function report(Request $request, $client_id)
    {
        // PATCH: report แบบช่วงวันที่ ต้องอิง client_id เดิม ไม่ใช้ $id
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $validator = Validator::make($request->all(), [
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
        ], [
            'date_to.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
        ]);

        if ($validator->fails()) {
            return redirect()->route('followup.index', $client->id)
                ->withErrors($validator)
                ->withInput();
        }

        $query = Followup::where('client_id', $client->id);

        if ($request->filled('date_from')) {
            $query->whereDate('followup_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('followup_date', '<=', $request->date_to);
        }

        $followups = $query->orderBy('followup_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;

        return view('frontend.client.followup.report', compact(
            'client',
            'followups',
            'dateFrom',
            'dateTo'
        ));
    }

    public function exportPdf(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $validator = Validator::make($request->all(), [
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date|after_or_equal:date_from',
        ], [
            'date_to.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
        ]);

        if ($validator->fails()) {
            return redirect()->route('followup.index', $client->id)
                ->withErrors($validator)
                ->withInput();
        }

        $query = Followup::where('client_id', $client->id);

        if ($request->filled('date_from')) {
            $query->whereDate('followup_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('followup_date', '<=', $request->date_to);
        }

        $followups = $query->orderBy('followup_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;

        $pdf = Pdf::loadView('frontend.client.followup.pdf', compact(
            'client',
            'followups',
            'dateFrom',
            'dateTo'
        ))->setPaper('a4', 'portrait');

        $filename = 'followup-client-' . $client->id . '-' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    public function reportItem($id)
    {
        // PATCH: กันเดา URL ตั้งแต่ query แรก
        $followup = Followup::whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);

        $client = Client::forUser(auth()->user())->findOrFail($followup->client_id);

        $followups = collect([$followup]);

        $dateFrom = null;
        $dateTo   = null;

        return view('frontend.client.followup.report', compact(
            'client',
            'followups',
            'dateFrom',
            'dateTo'
        ));
    }

    private function authorizeManage(): void
    {
        $allowedRoles = ['admin', 'executive', 'social_worker'];

        if (!auth()->check() || !in_array(auth()->user()->role, $allowedRoles)) {
            abort(403, 'คุณไม่มีสิทธิ์ดำเนินการนี้');
        }
    }

    private function authorizeDelete(): void
    {
        $allowedRoles = ['admin'];

        if (!auth()->check() || !in_array(auth()->user()->role, $allowedRoles)) {
            abort(403, 'คุณไม่มีสิทธิ์ลบข้อมูลนี้');
        }
    }
}