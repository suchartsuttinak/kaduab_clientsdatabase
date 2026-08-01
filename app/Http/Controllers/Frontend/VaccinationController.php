<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\Vaccination;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VaccinationController extends Controller
{
    /**
     * แสดงรายการประวัติการรับวัคซีน พร้อมตัวกรองช่วงวันที่
     */
    public function VaccineShow(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $filters = $this->validateDateRange($request);

        $vaccinations = $client->vaccinations()
            ->when(
                !empty($filters['start_date']),
                fn ($query) => $query->where('date', '>=', $filters['start_date'])
            )
            ->when(
                !empty($filters['end_date']),
                fn ($query) => $query->where('date', '<=', $filters['end_date'])
            )
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        return view('frontend.client.vaccine.vaccine_show', compact(
            'client',
            'vaccinations'
        ));
    }

    /**
     * บันทึกประวัติการรับวัคซีน
     */
    public function VaccineStore(Request $request)
    {
        $this->normalizeVaccinationInput($request);

        // ตรวจรูปแบบรหัสก่อน แล้วตรวจสิทธิ์จาก scope ของระบบ
        $clientInput = $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
        ], [
            'client_id.required' => 'กรุณาระบุรหัสผู้รับบริการ',
            'client_id.integer'  => 'รหัสผู้รับบริการไม่ถูกต้อง',
            'client_id.exists'   => 'รหัสผู้รับบริการไม่ถูกต้อง',
        ]);

        $client = Client::forUser(auth()->user())
            ->findOrFail($clientInput['client_id']);

        $request->merge(['client_id' => $client->id]);
        $validated = $this->validateVaccination($request, $client->id);

        DB::transaction(function () use ($validated, $client): void {
            Vaccination::create($validated);
            $this->syncLatestVaccineActivity($client->id);
        });

        return redirect()
            ->route('vaccine.index', ['client_id' => $client->id])
            ->with([
                'message'    => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * ส่งข้อมูลรายการที่ต้องการแก้ไขเป็น JSON
     */
    public function VaccineEdit($id)
    {
        $vaccination = Vaccination::whereHas('client', function ($query): void {
                $query->forUser(auth()->user());
            })
            ->findOrFail($id);

        return response()->json([
            'id'           => $vaccination->id,
            'date'         => $vaccination->date
                ? Carbon::parse($vaccination->date)->format('Y-m-d')
                : null,
            'vaccine_name' => $vaccination->vaccine_name ?? '',
            'hospital'     => $vaccination->hospital ?? '',
            'remark'       => $vaccination->remark ?? '',
            'recorder'     => $vaccination->recorder ?? '',
            'client_id'    => $vaccination->client_id,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    /**
     * แก้ไขประวัติการรับวัคซีน
     */
    public function VaccineUpdate(Request $request, $id)
    {
        $vaccination = Vaccination::whereHas('client', function ($query): void {
                $query->forUser(auth()->user());
            })
            ->findOrFail($id);

        // ไม่เชื่อถือ hidden client_id จากหน้าเว็บ ให้ใช้เจ้าของรายการเดิมเสมอ
        $request->merge(['client_id' => $vaccination->client_id]);
        $this->normalizeVaccinationInput($request);

        $validated = $this->validateVaccination(
            $request,
            $vaccination->client_id,
            $vaccination->id
        );

        DB::transaction(function () use ($vaccination, $validated): void {
            $vaccination->update($validated);
            $this->syncLatestVaccineActivity($vaccination->client_id);
        });

        return redirect()
            ->route('vaccine.index', ['client_id' => $vaccination->client_id])
            ->with([
                'message'    => 'แก้ไขข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * ลบประวัติการรับวัคซีน
     */
    public function VaccineDelete($id)
    {
        $vaccination = Vaccination::whereHas('client', function ($query): void {
                $query->forUser(auth()->user());
            })
            ->findOrFail($id);

        $clientId = $vaccination->client_id;

        DB::transaction(function () use ($vaccination, $clientId): void {
            $vaccination->delete();
            $this->syncLatestVaccineActivity($clientId);
        });

        return redirect()
            ->route('vaccine.index', ['client_id' => $clientId])
            ->with([
                'message'    => 'ลบข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success',
            ]);
    }

    /**
     * หน้ารายงานประวัติการรับวัคซีน
     */
    public function VaccineReport(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $filters = $this->validateDateRange($request);

        $vaccinations = $client->vaccinations()
            ->when(
                !empty($filters['start_date']),
                fn ($query) => $query->where('date', '>=', $filters['start_date'])
            )
            ->when(
                !empty($filters['end_date']),
                fn ($query) => $query->where('date', '<=', $filters['end_date'])
            )
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        return view('frontend.client.vaccine.report', compact(
            'client',
            'vaccinations'
        ));
    }

    /**
     * ตรวจสอบข้อมูลวัคซีน ใช้ร่วมกันทั้งเพิ่มและแก้ไข
     */
    private function validateVaccination(
        Request $request,
        int $clientId,
        ?int $ignoreId = null
    ): array {
        $today = now('Asia/Bangkok')->toDateString();

        $uniqueDateRule = Rule::unique('vaccinations', 'date')
            ->where(fn ($query) => $query->where('client_id', $clientId));

        if ($ignoreId !== null) {
            $uniqueDateRule->ignore($ignoreId);
        }

        return $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'date' => [
                'bail',
                'required',
                'date',
                'before_or_equal:' . $today,
                $uniqueDateRule,
            ],
            'vaccine_name' => ['bail', 'required', 'string', 'max:255'],
            'hospital'     => ['nullable', 'string', 'max:255'],
            'recorder'     => ['nullable', 'string', 'max:255'],
            'remark'       => ['nullable', 'string', 'max:500'],
        ], [
            'client_id.required'    => 'กรุณาระบุรหัสผู้รับบริการ',
            'client_id.integer'     => 'รหัสผู้รับบริการไม่ถูกต้อง',
            'client_id.exists'      => 'รหัสผู้รับบริการไม่ถูกต้อง',
            'date.required'         => 'กรุณากรอกวันที่รับวัคซีน',
            'date.date'             => 'วันที่รับวัคซีนไม่ถูกต้อง',
            'date.before_or_equal'  => 'วันที่รับวัคซีนต้องไม่เกินวันปัจจุบัน',
            'date.unique'           => 'ผู้รับบริการรายนี้มีการบันทึกวันที่รับวัคซีนนี้แล้ว',
            'vaccine_name.required' => 'กรุณากรอกชนิดวัคซีน',
            'vaccine_name.string'   => 'ชนิดวัคซีนต้องเป็นข้อความ',
            'vaccine_name.max'      => 'ชนิดวัคซีนต้องไม่เกิน 255 ตัวอักษร',
            'hospital.max'          => 'ชื่อสถานพยาบาลต้องไม่เกิน 255 ตัวอักษร',
            'recorder.max'          => 'ชื่อผู้บันทึกต้องไม่เกิน 255 ตัวอักษร',
            'remark.max'            => 'หมายเหตุต้องไม่เกิน 500 ตัวอักษร',
        ]);
    }

    /**
     * ตรวจสอบช่วงวันที่ โดยไม่อนุญาตวันที่ในอนาคต
     */
    private function validateDateRange(Request $request): array
    {
        $today = now('Asia/Bangkok')->toDateString();

        $endDateRules = [
            'nullable',
            'date',
            'before_or_equal:' . $today,
        ];

        if ($request->filled('start_date')) {
            $endDateRules[] = 'after_or_equal:start_date';
        }

        return $request->validate([
            'start_date' => ['nullable', 'date', 'before_or_equal:' . $today],
            'end_date'   => $endDateRules,
        ], [
            'start_date.date'            => 'วันที่เริ่มต้นไม่ถูกต้อง',
            'start_date.before_or_equal' => 'วันที่เริ่มต้นต้องไม่เกินวันปัจจุบัน',
            'end_date.date'              => 'วันที่สิ้นสุดไม่ถูกต้อง',
            'end_date.before_or_equal'   => 'วันที่สิ้นสุดต้องไม่เกินวันปัจจุบัน',
            'end_date.after_or_equal'    => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
        ]);
    }

    /**
     * ตัดช่องว่าง และเปลี่ยนข้อความว่างของช่องไม่บังคับเป็น null
     */
    private function normalizeVaccinationInput(Request $request): void
    {
        $normalized = [];

        foreach (['vaccine_name', 'hospital', 'recorder', 'remark'] as $field) {
            if (!$request->exists($field)) {
                continue;
            }

            $value = trim((string) $request->input($field));
            $normalized[$field] = $value === '' ? null : $value;
        }

        $request->merge($normalized);
    }

    /**
     * ให้ Dashboard แสดงข้อมูลวัคซีนล่าสุดที่ยังมีอยู่จริงเสมอ
     */
    private function syncLatestVaccineActivity(int $clientId): void
    {
        CaseActivity::where('client_id', $clientId)
            ->where('module', 'vaccine')
            ->delete();

        $latest = Vaccination::where('client_id', $clientId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->first();

        if (!$latest) {
            return;
        }

        CaseActivity::record([
            'client_id' => $clientId,
            'module'    => 'vaccine',
            'type'      => 'success',
            'title'     => 'ประวัติการรับวัคซีนล่าสุด',
            'description' =>
                'วันที่รับวัคซีน: ' . ($latest->date ?? '-') .
                ' | ชนิดวัคซีน: ' . ($latest->vaccine_name ?: '-') .
                ' | สถานพยาบาล: ' . ($latest->hospital ?: '-'),
            'occurred_at' => $latest->date ?: now('Asia/Bangkok'),
            'icon'        => 'bi-shield-plus',
            'url'         => route('vaccine.index', ['client_id' => $clientId]),
        ]);
    }
}
