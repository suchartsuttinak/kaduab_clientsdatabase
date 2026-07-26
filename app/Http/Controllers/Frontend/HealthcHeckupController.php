<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\HealthcHeckup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\CaseActivity;

class HealthcHeckupController extends Controller
{
    /**
     * แสดงหน้าหลัก + filter + table
     */
    public function index(Request $request)
    {
        $this->authorizeRole();

        $clients = Client::forUser(auth()->user())
            ->orderByRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) ASC")
            ->get();

        $query = HealthcHeckup::with(['client', 'recorder'])
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->latest('checkup_date')
            ->latest('id');

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('checkup_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('checkup_date', '<=', $request->date_to);
        }

        if ($request->filled('checkup_result')) {
            $query->where('checkup_result', $request->checkup_result);
        }

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);

            $query->where(function ($q) use ($keyword) {
                $q->where('hospital_name', 'like', "%{$keyword}%")
                    ->orWhere('abnormal_detail', 'like', "%{$keyword}%")
                    ->orWhereHas('client', function ($clientQuery) use ($keyword) {
                        $clientQuery->where(function ($nameQuery) use ($keyword) {
                            $nameQuery->where('first_name', 'like', "%{$keyword}%")
                                ->orWhere('last_name', 'like', "%{$keyword}%")
                                ->orWhereRaw(
                                    "CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) LIKE ?",
                                    ["%{$keyword}%"]
                                );
                        });
                    });
            });
        }

        $healthcHeckups = $query->paginate(20)->withQueryString();

        return view('frontend.healthc_heckups.index', compact('clients', 'healthcHeckups'));
    }

    /**
     * บันทึกข้อมูลใหม่
     */
 public function store(Request $request)
        {
            $this->authorizeRole();

            $validated = $request->validate([
                'client_id' => 'required|integer|exists:clients,id',

                'checkup_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],

                'hospital_name' => 'required|string|max:255',
                'checkup_result' => 'required|in:normal,abnormal',
                'abnormal_detail' => 'nullable|string',
                'medical_document' => 'nullable|file|mimes:pdf|max:5120',
            ], [
                'client_id.required' => 'กรุณาเลือกผู้รับบริการ',
                'checkup_date.required' => 'กรุณาระบุวันที่ตรวจ',
                'checkup_date.before_or_equal' => 'วันที่ตรวจต้องไม่เกินวันปัจจุบัน',
                'hospital_name.required' => 'กรุณาระบุสถานพยาบาล',
                'checkup_result.required' => 'กรุณาเลือกผลการตรวจ',
                'medical_document.mimes' => 'ไฟล์เอกสารต้องเป็น PDF เท่านั้น',
                'medical_document.max' => 'ไฟล์เอกสารต้องมีขนาดไม่เกิน 5 MB',
            ]);

            $client = Client::forUser(auth()->user())->findOrFail($validated['client_id']);

            $validated['client_id'] = $client->id;

            $filePath = null;

            if ($request->hasFile('medical_document')) {
                $filePath = $this->uploadMedicalDocument(
                    $request->file('medical_document')
                );
            }

            $healthcHeckup = HealthcHeckup::create([
                'client_id' => $client->id,
                'checkup_date' => $validated['checkup_date'],
                'hospital_name' => $validated['hospital_name'],
                'checkup_result' => $validated['checkup_result'],
                'abnormal_detail' => $validated['checkup_result'] === 'abnormal'
                    ? $validated['abnormal_detail']
                    : null,
                'medical_document' => $filePath,
                'recorded_by' => Auth::id(),
            ]);

           CaseActivity::where('client_id', $client->id)
                ->where('module', 'healthc_heckup')
                ->delete();

            CaseActivity::record([
                'client_id'   => $client->id,
                'module'      => 'healthc_heckup',
                'type'        => $validated['checkup_result'] === 'abnormal' ? 'warning' : 'success',
                'title'       => 'บันทึกการตรวจสุขภาพประจำปี',
                'description' => 'วันที่ตรวจ: ' . ($validated['checkup_date'] ?? '-') .
                                ' | สถานพยาบาล: ' . ($validated['hospital_name'] ?? '-') .
                                ' | ผลตรวจ: ' . ($validated['checkup_result'] === 'abnormal' ? 'ผิดปกติ' : 'ปกติ'),
                'occurred_at' => now(),
                'icon'        => 'bi-clipboard2-heart',
                'url'         => route('healthc_heckups.index'),
            ]);

            return redirect()
                ->route('healthc_heckups.index')
                ->with('success', 'บันทึกข้อมูลการตรวจสุขภาพเรียบร้อยแล้ว');
        }
    /**
     * ดึงข้อมูลสำหรับแก้ไข (JSON)
     */
    public function editJson($id)
    {
        $this->authorizeRole();

        $item = $this->findAuthorizedItem($id);

        return response()->json([
            'id' => $item->id,
            'client_id' => $item->client_id,
            'checkup_date' => optional($item->checkup_date)->format('Y-m-d'),
            'hospital_name' => $item->hospital_name,
            'checkup_result' => $item->checkup_result,
            'abnormal_detail' => $item->abnormal_detail,
            'medical_document_url' => $item->medical_document
                ? asset($item->medical_document)
                : null,
            'medical_document_name' => $item->medical_document
                ? basename($item->medical_document)
                : null,
        ]);
    }

    /**
     * อัปเดตข้อมูล
     */
    public function update(Request $request, $id)
    {
        $this->authorizeRole();

        $item = $this->findAuthorizedItem($id);

        $validated = $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'checkup_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],
            'hospital_name' => 'required|string|max:255',
            'checkup_result' => 'required|in:normal,abnormal',
            'abnormal_detail' => 'nullable|string',
            'medical_document' => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'client_id.required' => 'กรุณาเลือกผู้รับบริการ',
            'checkup_date.required' => 'กรุณาระบุวันที่ตรวจ',
            'checkup_date.before_or_equal' => 'วันที่ตรวจต้องไม่เกินวันปัจจุบัน',
            'hospital_name.required' => 'กรุณาระบุสถานพยาบาล',
            'checkup_result.required' => 'กรุณาเลือกผลการตรวจ',
            'medical_document.mimes' => 'ไฟล์เอกสารต้องเป็น PDF เท่านั้น',
            'medical_document.max' => 'ไฟล์เอกสารต้องมีขนาดไม่เกิน 5 MB',
        ]);

        $client = Client::forUser(auth()->user())->findOrFail($validated['client_id']);

        $validated['client_id'] = $client->id;

        $filePath = $item->medical_document;

        if ($request->hasFile('medical_document')) {

            $this->deleteMedicalDocument($item->medical_document);

            $filePath = $this->uploadMedicalDocument(
                $request->file('medical_document')
            );
        }

        $item->update([
            'client_id' => $client->id,
            'checkup_date' => $validated['checkup_date'],
            'hospital_name' => $validated['hospital_name'],
            'checkup_result' => $validated['checkup_result'],
            'abnormal_detail' => $validated['checkup_result'] === 'abnormal'
                ? $validated['abnormal_detail']
                : null,
            'medical_document' => $filePath,
            'recorded_by' => Auth::id(),
        ]);

        CaseActivity::where('client_id', $client->id)
            ->where('module', 'healthc_heckup')
            ->delete();

        CaseActivity::record([
            'client_id'   => $client->id,
            'module'      => 'healthc_heckup',
            'type'        => $validated['checkup_result'] === 'abnormal' ? 'warning' : 'success',
            'title'       => 'แก้ไขการตรวจสุขภาพประจำปี',
            'description' => 'วันที่ตรวจ: ' . ($validated['checkup_date'] ?? '-') .
                            ' | สถานพยาบาล: ' . ($validated['hospital_name'] ?? '-') .
                            ' | ผลตรวจ: ' . ($validated['checkup_result'] === 'abnormal' ? 'ผิดปกติ' : 'ปกติ'),
            'occurred_at' => now(),
            'icon'        => 'bi-clipboard2-heart',
            'url'         => route('healthc_heckups.index'),
        ]);

        return redirect()
            ->route('healthc_heckups.index')
            ->with('success', 'แก้ไขข้อมูลการตรวจสุขภาพเรียบร้อยแล้ว');
    }

    /**
     * ลบข้อมูล
     */
    public function destroy($id)
    {
        $this->authorizeRole();

        $item = $this->findAuthorizedItem($id);

        $this->deleteMedicalDocument($item->medical_document);

        CaseActivity::where('client_id', $item->client_id)
        ->where('module', 'healthc_heckup')
        ->delete();

        $item->delete();

        return redirect()
            ->route('healthc_heckups.index')
            ->with('success', 'ลบข้อมูลการตรวจสุขภาพเรียบร้อยแล้ว');
    }

    /**
     * รายงานรวม
     */
    public function report(Request $request)
    {
        $this->authorizeRole();

        $query = HealthcHeckup::with(['client', 'recorder'])
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->latest('checkup_date')
            ->latest('id');

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('checkup_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('checkup_date', '<=', $request->date_to);
        }

        if ($request->filled('checkup_result')) {
            $query->where('checkup_result', $request->checkup_result);
        }

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);

            $query->where(function ($q) use ($keyword) {
                $q->where('hospital_name', 'like', "%{$keyword}%")
                    ->orWhere('abnormal_detail', 'like', "%{$keyword}%")
                    ->orWhereHas('client', function ($clientQuery) use ($keyword) {
                        $clientQuery->where(function ($nameQuery) use ($keyword) {
                            $nameQuery->where('first_name', 'like', "%{$keyword}%")
                                ->orWhere('last_name', 'like', "%{$keyword}%")
                                ->orWhereRaw(
                                    "CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) LIKE ?",
                                    ["%{$keyword}%"]
                                );
                        });
                    });
            });
        }

        $items = $query->get();

        return view('frontend.healthc_heckups.report', compact('items'));
    }

    private function uploadMedicalDocument($file): string
    {
        $folder = 'upload/healthc_heckups';

        $destinationPath = public_path($folder);

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $filename = now()->format('YmdHis') . '_' . uniqid() . '.pdf';

        $file->move($destinationPath, $filename);

        return $folder . '/' . $filename;
    }

    private function deleteMedicalDocument(?string $filePath): void
    {
        if (empty($filePath)) {
            return;
        }

        $publicPath = public_path($filePath);

        if (File::exists($publicPath)) {
            File::delete($publicPath);
        }

        // รองรับไฟล์เก่าใน storage/app/public
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }

    private function authorizeRole(): void
    {
        abort_unless(
            auth()->check() && in_array(auth()->user()->role, ['admin', 'executive', 'social_worker']),
            403,
            'คุณไม่มีสิทธิ์เข้าถึงข้อมูลการตรวจสุขภาพ'
        );
    }

    private function findAuthorizedItem($id): HealthcHeckup
    {
        return HealthcHeckup::with(['client', 'recorder'])
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($id);
    }
}