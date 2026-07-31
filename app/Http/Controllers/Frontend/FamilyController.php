<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\District;
use App\Models\Father;
use App\Models\Mother;
use App\Models\Province;
use App\Models\Relative;
use App\Models\Spouse;
use App\Models\SubDistrict;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class FamilyController extends Controller
{
    private const FAMILY_GROUPS = ['father', 'mother', 'spouse', 'relative'];

    public function FamilyAdd($client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $father = Father::where('client_id', $client->id)->first();
        $mother = Mother::where('client_id', $client->id)->first();
        $spouse = Spouse::where('client_id', $client->id)->first();
        $relative = Relative::where('client_id', $client->id)->first();

        $familyModels = compact('father', 'mother', 'spouse', 'relative');
        $oldInput = session()->get('_old_input', []);

        /*
        |------------------------------------------------------------------
        | PERFORMANCE PATCH
        |------------------------------------------------------------------
        | เดิมโหลด District::all() และ SubDistrict::all() แล้วนำไปสร้าง
        | <option> ซ้ำ 4 แท็บ ทำให้ HTML ใหญ่มากและหน้าเรนเดอร์ช้า
        |
        | ชุดนี้โหลดเฉพาะอำเภอของจังหวัดที่ถูกเลือก และตำบลของอำเภอ
        | ที่ถูกเลือกเท่านั้น โดยรวม query ให้เหลืออย่างละไม่เกิน 1 ครั้ง
        */
        $selectedLocations = collect(self::FAMILY_GROUPS)->mapWithKeys(function (string $group) use ($familyModels, $oldInput) {
            $model = $familyModels[$group];

            return [$group => [
                'province_id' => data_get($oldInput, "$group.province_id", $model?->province_id),
                'district_id' => data_get($oldInput, "$group.district_id", $model?->district_id),
                'sub_district_id' => data_get($oldInput, "$group.sub_district_id", $model?->sub_district_id),
            ]];
        });

        $provinceIds = $selectedLocations
            ->pluck('province_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $districtIds = $selectedLocations
            ->pluck('district_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $districtsByProvince = $provinceIds->isEmpty()
            ? collect()
            : District::query()
                ->select(['id', 'province_id', 'dist_name'])
                ->whereIn('province_id', $provinceIds)
                ->orderBy('dist_name')
                ->get()
                ->groupBy('province_id');

        $subDistrictsByDistrict = $districtIds->isEmpty()
            ? collect()
            : SubDistrict::query()
                ->select(['id', 'district_id', 'subd_name', 'zipcode'])
                ->whereIn('district_id', $districtIds)
                ->orderBy('subd_name')
                ->get()
                ->groupBy('district_id');

        $locationOptions = $selectedLocations->map(function (array $selected) use ($districtsByProvince, $subDistrictsByDistrict) {
            return [
                'districts' => $selected['province_id']
                    ? $districtsByProvince->get((int) $selected['province_id'], collect())
                    : collect(),
                'sub_districts' => $selected['district_id']
                    ? $subDistrictsByDistrict->get((int) $selected['district_id'], collect())
                    : collect(),
            ];
        })->all();

        /*
        |------------------------------------------------------------------
        | BACKWARD-COMPATIBILITY PATCH
        |------------------------------------------------------------------
        | รองรับ Blade รุ่นเดิมที่ยังเรียก $districts และ $sub_districts
        | โดยไม่ย้อนกลับไปโหลดข้อมูลทั้งประเทศ แค่รวมรายการที่จำเป็น
        | สำหรับข้อมูลเดิมของทั้ง 4 แท็บเท่านั้น
        */
        $districts = $districtsByProvince
            ->flatten(1)
            ->unique('id')
            ->values();

        $sub_districts = $subDistrictsByDistrict
            ->flatten(1)
            ->unique('id')
            ->values();

        $provinces = Province::query()
            ->select(['id', 'prov_name'])
            ->orderBy('prov_name')
            ->get();

        return view('frontend.client.family.family_add', compact(
            'client',
            'father',
            'mother',
            'spouse',
            'relative',
            'provinces',
            'districts',
            'sub_districts',
            'locationOptions'
        ));
    }

    public function getDistricts($province_id): JsonResponse
    {
        $districts = District::query()
            ->where('province_id', (int) $province_id)
            ->orderBy('dist_name')
            ->get(['id', 'dist_name']);

        return response()
            ->json($districts)
            ->header('Cache-Control', 'private, max-age=3600');
    }

    public function getSubdistricts($district_id): JsonResponse
    {
        $subDistricts = SubDistrict::query()
            ->where('district_id', (int) $district_id)
            ->orderBy('subd_name')
            ->get(['id', 'subd_name']);

        return response()
            ->json($subDistricts)
            ->header('Cache-Control', 'private, max-age=3600');
    }

    public function getZipcode($subdistrict_id): JsonResponse
    {
        $zipcode = SubDistrict::query()
            ->whereKey((int) $subdistrict_id)
            ->value('zipcode');

        return response()
            ->json(['zipcode' => $zipcode])
            ->header('Cache-Control', 'private, max-age=3600');
    }

    public function FamilyStore(Request $request)
    {
        try {
            $validated = $request->validate($this->familyRules(), $this->validationMessages());

            $client = Client::forUser(auth()->user())
                ->where('id', $validated['client_id'])
                ->firstOrFail();

            $activeTab = $validated['active_tab'] ?? 'father-tab';

            DB::transaction(function () use ($client, $validated): void {
                $clientId = $client->id;

                $this->saveFamilyGroup(Father::class, $clientId, $validated['father'] ?? []);
                $this->saveFamilyGroup(Mother::class, $clientId, $validated['mother'] ?? []);
                $this->saveFamilyGroup(Spouse::class, $clientId, $validated['spouse'] ?? []);
                $this->saveFamilyGroup(Relative::class, $clientId, $validated['relative'] ?? []);

                CaseActivity::where('client_id', $clientId)
                    ->where('module', 'family')
                    ->delete();

                CaseActivity::record([
                    'client_id' => $clientId,
                    'module' => 'family',
                    'type' => 'success',
                    'title' => 'บันทึกข้อมูลครอบครัว',
                    'description' => 'มีการบันทึกหรือปรับปรุงข้อมูลครอบครัวของผู้รับบริการ',
                    'occurred_at' => now('Asia/Bangkok'),
                    'icon' => 'bi-people',
                    'url' => url('/client/family/add/' . $clientId),
                ]);
            }, 3);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'บันทึกข้อมูลครอบครัวเรียบร้อยแล้ว',
                    'active_tab' => $activeTab,
                ]);
            }

            return redirect()
                ->back()
                ->with('success', 'บันทึกข้อมูลครอบครัวเรียบร้อยแล้ว')
                ->with('active_tab', $activeTab);
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'กรุณาตรวจสอบข้อมูลที่กรอกอีกครั้ง',
                    'errors' => $e->errors(),
                    'active_tab' => $request->input('active_tab', 'father-tab'),
                ], 422);
            }

            throw $e;
        } catch (Throwable $e) {
            report($e);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง',
                    'active_tab' => $request->input('active_tab', 'father-tab'),
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง')
                ->with('active_tab', $request->input('active_tab', 'father-tab'));
        }
    }

    private function familyRules(): array
    {
        $rules = [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'active_tab' => ['nullable', 'string', 'max:50'],
        ];

        foreach (self::FAMILY_GROUPS as $group) {
            $rules["$group.fname"] = ['nullable', 'string', 'max:255'];
            $rules["$group.lname"] = ['nullable', 'string', 'max:255'];
            $rules["$group.idcard"] = ['nullable', 'regex:/^[0-9]{1}-[0-9]{4}-[0-9]{5}-[0-9]{2}-[0-9]{1}$/'];
            $rules["$group.age"] = ['nullable', 'integer', 'min:0', 'max:150'];
            $rules["$group.occupation"] = ['nullable', 'string', 'max:255'];
            $rules["$group.income"] = ['nullable', 'string', 'max:50'];
            $rules["$group.address_no"] = ['nullable', 'string', 'max:255'];
            $rules["$group.moo"] = ['nullable', 'string', 'max:255'];
            $rules["$group.soi"] = ['nullable', 'string', 'max:255'];
            $rules["$group.road"] = ['nullable', 'string', 'max:255'];
            $rules["$group.village"] = ['nullable', 'string', 'max:255'];
            $rules["$group.province_id"] = ['nullable', 'integer'];
            $rules["$group.district_id"] = ['nullable', 'integer'];
            $rules["$group.sub_district_id"] = ['nullable', 'integer'];
            $rules["$group.zipcode"] = ['nullable', 'string', 'max:20'];
            $rules["$group.phone"] = ['nullable', 'string', 'max:20'];
        }

        return $rules;
    }

    private function validationMessages(): array
    {
        return [
            '*.idcard.regex' => 'รูปแบบเลขประจำตัวประชาชนต้องเป็น 0-0000-00000-00-0',
            '*.age.integer' => 'อายุต้องเป็นจำนวนเต็ม',
            '*.age.min' => 'อายุต้องไม่น้อยกว่า 0 ปี',
            '*.age.max' => 'อายุต้องไม่เกิน 150 ปี',
            '*.max' => 'ข้อมูลมีความยาวเกินกว่าที่กำหนด',
        ];
    }

    private function saveFamilyGroup(string $modelClass, int $clientId, array $data): void
    {
        $payload = $this->normalizeFamilyPayload($data);

        foreach ($payload as $value) {
            if ($value !== null && $value !== '') {
                $modelClass::updateOrCreate(
                    ['client_id' => $clientId],
                    $payload
                );

                return;
            }
        }
    }

    private function normalizeFamilyPayload(array $data): array
    {
        $stringFields = [
            'fname',
            'lname',
            'idcard',
            'occupation',
            'address_no',
            'moo',
            'soi',
            'road',
            'village',
            'zipcode',
            'phone',
        ];

        $payload = [];

        foreach ($stringFields as $field) {
            $value = isset($data[$field]) ? trim((string) $data[$field]) : null;
            $payload[$field] = $value === '' ? null : $value;
        }

        $payload['age'] = isset($data['age']) && $data['age'] !== ''
            ? (int) $data['age']
            : null;

        $payload['income'] = isset($data['income']) && $data['income'] !== ''
            ? preg_replace('/[^0-9.]/', '', (string) $data['income'])
            : null;

        foreach (['province_id', 'district_id', 'sub_district_id'] as $field) {
            $payload[$field] = isset($data[$field]) && $data[$field] !== ''
                ? (int) $data[$field]
                : null;
        }

        return $payload;
    }
}