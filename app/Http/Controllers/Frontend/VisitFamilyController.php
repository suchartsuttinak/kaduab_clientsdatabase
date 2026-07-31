<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\District;
use App\Models\Image;
use App\Models\Income;
use App\Models\Province;
use App\Models\SubDistrict;
use App\Models\VisitFamily;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

class VisitFamilyController extends Controller
{
    private const MAX_IMAGES_PER_REQUEST = 10;
    private const MAX_IMAGE_SIZE_KB = 10240;

    /**
     * บันทึกและลดขนาดรูปภาพให้เหมาะกับ Shared Hosting
     */
    protected function saveVisitImage(UploadedFile $file, bool $cover = false): array
    {
        $destinationPath = public_path('upload/visit_images');

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true, true);
        }

        $filename = Str::uuid()->toString() . '.jpg';
        $relativePath = 'upload/visit_images/' . $filename;
        $absolutePath = public_path($relativePath);

        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath())->orient();

        if ($cover) {
            $image->cover(1000, 700);
        } else {
            $image->scaleDown(width: 1200, height: 1200);
        }

        $image->toJpeg(
            quality: 72,
            progressive: true
        )->save($absolutePath);

        return [
            'path' => $relativePath,
            'name' => mb_substr(basename($file->getClientOriginalName()), 0, 255),
            'mime' => 'image/jpeg',
            'size' => File::size($absolutePath),
        ];
    }

    /**
     * คืน absolute path เฉพาะตำแหน่งที่ระบบอนุญาต
     */
    protected function resolveVisitImagePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', trim($path)), '/');

        if (
            $normalized === ''
            || str_contains($normalized, '../')
            || str_contains($normalized, "\0")
        ) {
            return null;
        }

        if (Str::startsWith($normalized, 'upload/visit_images/')) {
            return public_path($normalized);
        }

        if (Str::startsWith($normalized, 'storage/')) {
            return public_path($normalized);
        }

        // รองรับข้อมูลเก่าที่เก็บ path ใน public disk โดยไม่มีคำว่า storage/
        return public_path('storage/' . $normalized);
    }

    protected function deleteVisitImage(?string $path): void
    {
        $fullPath = $this->resolveVisitImagePath($path);

        if ($fullPath && File::isFile($fullPath)) {
            File::delete($fullPath);
        }
    }

    protected function visitImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', trim($path)), '/');

        if (
            $normalized === ''
            || str_contains($normalized, '../')
            || str_contains($normalized, "\0")
        ) {
            return null;
        }

        if (
            Str::startsWith($normalized, 'upload/')
            || Str::startsWith($normalized, 'storage/')
        ) {
            return asset($normalized);
        }

        return asset('storage/' . $normalized);
    }

    public function AddvisitFamily($client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $visitFamily = VisitFamily::where('client_id', $client->id)
            ->latest('id')
            ->first();

        if ($visitFamily) {
            return redirect()
                ->route('vitsitFamily.edit', $visitFamily->id)
                ->with('warning', 'มีการบันทึกข้อมูลรายนี้แล้ว กรุณาแก้ไขข้อมูลเดิม');
        }

        $incomes = Income::query()->orderBy('id')->get();
        $provinces = Province::query()->orderBy('id')->get();
        $districts = collect();
        $sub_districts = collect();

        return view('frontend.client.visitFamily.visitFamily_add', compact(
            'provinces',
            'districts',
            'sub_districts',
            'client_id',
            'client',
            'incomes',
            'visitFamily'
        ));
    }

    public function getDistricts($province_id)
    {
        $province = Province::findOrFail($province_id);

        $districts = District::where('province_id', $province->id)
            ->orderBy('id')
            ->get();

        return response()->json($districts);
    }

    public function getSubdistricts($district_id)
    {
        $district = District::findOrFail($district_id);

        $subdistricts = SubDistrict::where('district_id', $district->id)
            ->orderBy('id')
            ->get();

        return response()->json($subdistricts);
    }

    public function getZipcode($subdistrict_id)
    {
        $subdistrict = SubDistrict::findOrFail($subdistrict_id);

        return response()->json([
            'zipcode' => $subdistrict->zipcode,
        ]);
    }

    public function StoreVisitFamily(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $validated = $this->validateVisitFamily($request);
        $validated = $this->normalizeVisitData($validated);

        $savedPaths = [];

        try {
            $visitFamily = DB::transaction(function () use (
                $request,
                $client,
                $validated,
                &$savedPaths
            ) {
                // ล็อกผู้รับบริการเพื่อป้องกันการกดบันทึกพร้อมกันจนเกิดข้อมูลซ้ำ
                Client::whereKey($client->id)->lockForUpdate()->firstOrFail();

                if (VisitFamily::where('client_id', $client->id)->exists()) {
                    throw ValidationException::withMessages([
                        'client_id' => 'มีการบันทึกข้อมูลเยี่ยมบ้านของผู้รับบริการรายนี้แล้ว',
                    ]);
                }

                $data = $validated;
                unset($data['images']);

                $data['client_id'] = $client->id;
                $data['count'] = 1;

                $visitFamily = VisitFamily::create($data);

                foreach ($request->file('images', []) as $file) {
                    $saved = $this->saveVisitImage($file);
                    $savedPaths[] = $saved['path'];

                    $visitFamily->images()->create([
                        'file_path' => $saved['path'],
                        'file_name' => $saved['name'],
                        'mime_type' => $saved['mime'],
                        'size' => $saved['size'],
                        'client_id' => $client->id,
                    ]);
                }

                $this->recordVisitActivity(
                    $client->id,
                    $visitFamily->id,
                    $data,
                    'บันทึกการเยี่ยมบ้านครอบครัว'
                );

                return $visitFamily;
            });
        } catch (Throwable $e) {
            foreach ($savedPaths as $path) {
                $this->deleteVisitImage($path);
            }

            throw $e;
        }

        return redirect()
            ->route('vitsitFamily.edit', $visitFamily->id)
            ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }

    public function EditVisitFamily($id)
    {
        $visitFamily = VisitFamily::whereKey($id)
            ->whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->with(['images' => function ($query) {
                $query->orderBy('id');
            }])
            ->firstOrFail();

        $client = Client::forUser(auth()->user())
            ->findOrFail($visitFamily->client_id);

        $incomes = Income::query()->orderBy('id')->get();
        $provinces = Province::query()->orderBy('id')->get();

        $districts = District::where('province_id', $visitFamily->province_id)
            ->orderBy('id')
            ->get();

        $sub_districts = SubDistrict::where('district_id', $visitFamily->district_id)
            ->orderBy('id')
            ->get();

        $images = $visitFamily->images;

        return view('frontend.client.visitFamily.visitFamily_add', compact(
            'provinces',
            'districts',
            'sub_districts',
            'client',
            'incomes',
            'visitFamily',
            'images'
        ));
    }

    public function UpdateVisitFamily(Request $request, $id)
    {
        $visitFamily = VisitFamily::whereKey($id)
            ->whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->firstOrFail();

        $validated = $this->validateVisitFamily($request);
        $validated = $this->normalizeVisitData($validated);

        $savedPaths = [];

        try {
            DB::transaction(function () use (
                $request,
                $visitFamily,
                $validated,
                &$savedPaths
            ) {
                $lockedVisit = VisitFamily::whereKey($visitFamily->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $data = $validated;
                unset($data['images'], $data['client_id']);

                $lockedVisit->update($data);

                // รูปที่เพิ่มในหน้าแก้ไขควรรักษาสัดส่วนเดิม ไม่ครอบตัดรูป
                foreach ($request->file('images', []) as $file) {
                    $saved = $this->saveVisitImage($file);
                    $savedPaths[] = $saved['path'];

                    $lockedVisit->images()->create([
                        'file_path' => $saved['path'],
                        'file_name' => $saved['name'],
                        'mime_type' => $saved['mime'],
                        'size' => $saved['size'],
                        'client_id' => $lockedVisit->client_id,
                    ]);
                }

                $this->recordVisitActivity(
                    $lockedVisit->client_id,
                    $lockedVisit->id,
                    $data,
                    'แก้ไขข้อมูลการเยี่ยมบ้านครอบครัว'
                );
            });
        } catch (Throwable $e) {
            foreach ($savedPaths as $path) {
                $this->deleteVisitImage($path);
            }

            throw $e;
        }

        return redirect()
            ->route('vitsitFamily.edit', $visitFamily->id)
            ->with('success', 'แก้ไขข้อมูลเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $image = Image::whereKey($id)
            ->whereHas('visitFamily.client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->firstOrFail();

        $oldPath = $image->file_path;

        DB::transaction(function () use ($image, $oldPath) {
            $image->delete();

            DB::afterCommit(function () use ($oldPath) {
                $this->deleteVisitImage($oldPath);
            });
        });

        return response()->json([
            'success' => true,
            'message' => 'ลบรูปภาพเรียบร้อยแล้ว',
        ]);
    }

    public function replaceImage(Request $request, $id)
    {
        $validated = $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:' . self::MAX_IMAGE_SIZE_KB,
            ],
        ], [
            'image.required' => 'กรุณาเลือกรูปภาพ',
            'image.image' => 'ไฟล์ที่เลือกต้องเป็นรูปภาพ',
            'image.mimes' => 'รูปภาพต้องเป็นไฟล์ชนิด jpg, jpeg, png หรือ webp',
            'image.max' => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 10MB',
        ]);

        $image = Image::whereKey($id)
            ->whereHas('visitFamily.client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->firstOrFail();

        // บันทึกรูปใหม่ให้สำเร็จก่อน จึงค่อยเปลี่ยนข้อมูลและลบรูปเดิม
        $saved = $this->saveVisitImage($validated['image'], true);
        $oldPath = $image->file_path;

        try {
            DB::transaction(function () use ($image, $saved, $oldPath) {
                $image->update([
                    'file_path' => $saved['path'],
                    'file_name' => $saved['name'],
                    'mime_type' => $saved['mime'],
                    'size' => $saved['size'],
                ]);

                DB::afterCommit(function () use ($oldPath) {
                    $this->deleteVisitImage($oldPath);
                });
            });
        } catch (Throwable $e) {
            $this->deleteVisitImage($saved['path']);
            throw $e;
        }

        return response()->json([
            'success' => true,
            'id' => $image->id,
            'url' => $this->visitImageUrl($saved['path']),
        ]);
    }

    public function ReportVisitFamily($id)
    {
        $visitFamily = VisitFamily::with(['client', 'income', 'images'])
            ->whereKey($id)
            ->whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->firstOrFail();

        $client = Client::forUser(auth()->user())
            ->findOrFail($visitFamily->client_id);

        $province = Province::find($visitFamily->province_id);
        $district = District::find($visitFamily->district_id);
        $subDistrict = SubDistrict::find($visitFamily->sub_district_id);

        $provinceName = $province->prov_name
            ?? $province->province_name
            ?? $province->name
            ?? '-';

        $districtName = $district->dist_name
            ?? $district->district_name
            ?? $district->name
            ?? '-';

        $subDistrictName = $subDistrict->subd_name
            ?? $subDistrict->sub_district_name
            ?? $subDistrict->subdistrict_name
            ?? $subDistrict->name
            ?? '-';

        return view('frontend.client.visitFamily.visitFamily_report', compact(
            'visitFamily',
            'client',
            'provinceName',
            'districtName',
            'subDistrictName'
        ));
    }

    /**
     * Validation ใช้ร่วมกันทั้งบันทึกและแก้ไข
     */
    protected function validateVisitFamily(Request $request): array
    {
        $this->trimRequestStrings($request);

        return $request->validate([
            'visit_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],
            'family_fname' => ['required', 'string', 'max:255'],
            'family_age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'member' => ['nullable', 'string', 'max:255'],
            'residence_status' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'moo' => ['nullable', 'string', 'max:50'],
            'soi' => ['nullable', 'string', 'max:50'],
            'road' => ['nullable', 'string', 'max:255'],
            'village' => ['nullable', 'string', 'max:255'],
            'province_id' => [
                'required',
                'integer',
                Rule::exists('provinces', 'id'),
            ],
            'district_id' => [
                'required',
                'integer',
                Rule::exists('districts', 'id')->where(
                    fn ($query) => $query->where(
                        'province_id',
                        $request->input('province_id')
                    )
                ),
            ],
            'sub_district_id' => [
                'required',
                'integer',
                Rule::exists('sub_districts', 'id')->where(
                    fn ($query) => $query->where(
                        'district_id',
                        $request->input('district_id')
                    )
                ),
            ],
            'zipcode' => ['required', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:20'],
            'outside_address' => ['nullable', 'string', 'max:5000'],
            'inside_address' => ['nullable', 'string', 'max:5000'],
            'environment' => ['nullable', 'string', 'max:5000'],
            'neighbor' => ['nullable', 'string', 'max:5000'],
            'member_relation' => ['nullable', 'string', 'max:5000'],
            'income_id' => [
                'nullable',
                'integer',
                Rule::exists('incomes', 'id'),
            ],
            'problem' => ['nullable', 'string', 'max:5000'],
            'need' => ['nullable', 'string', 'max:5000'],
            'diagnose' => ['nullable', 'string', 'max:5000'],
            'assistance' => ['nullable', 'string', 'max:5000'],
            'comment' => ['nullable', 'string', 'max:5000'],
            'modify' => ['nullable', 'string', 'max:5000'],
            'teacher' => ['required', 'string', 'max:255'],
            'remark' => ['nullable', 'string', 'max:5000'],
            'images' => [
                'nullable',
                'array',
                'max:' . self::MAX_IMAGES_PER_REQUEST,
            ],
            'images.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:' . self::MAX_IMAGE_SIZE_KB,
            ],
        ], [
            'visit_date.required' => 'กรุณาระบุวันที่เยี่ยมบ้าน',
            'visit_date.date' => 'รูปแบบวันที่เยี่ยมบ้านไม่ถูกต้อง',
            'visit_date.before_or_equal' => 'วันที่เยี่ยมบ้านต้องไม่เกินวันที่ปัจจุบัน',
            'family_fname.required' => 'กรุณากรอกชื่อผู้ให้ข้อมูล',
            'family_fname.max' => 'ชื่อผู้ให้ข้อมูลต้องไม่เกิน 255 ตัวอักษร',
            'family_age.integer' => 'อายุต้องเป็นตัวเลขจำนวนเต็ม',
            'family_age.min' => 'อายุต้องไม่น้อยกว่า 0 ปี',
            'family_age.max' => 'อายุต้องไม่เกิน 120 ปี',
            'province_id.required' => 'กรุณาเลือกจังหวัด',
            'province_id.exists' => 'จังหวัดที่เลือกไม่ถูกต้อง',
            'district_id.required' => 'กรุณาเลือกอำเภอ',
            'district_id.exists' => 'อำเภอที่เลือกไม่สอดคล้องกับจังหวัด',
            'sub_district_id.required' => 'กรุณาเลือกตำบล',
            'sub_district_id.exists' => 'ตำบลที่เลือกไม่สอดคล้องกับอำเภอ',
            'zipcode.required' => 'กรุณากรอกรหัสไปรษณีย์',
            'zipcode.max' => 'รหัสไปรษณีย์ต้องไม่เกิน 10 หลัก',
            'income_id.exists' => 'ช่วงรายได้ที่เลือกไม่ถูกต้อง',
            'teacher.required' => 'กรุณาระบุผู้ที่เยี่ยมบ้าน',
            'teacher.max' => 'ชื่อผู้เยี่ยมบ้านต้องไม่เกิน 255 ตัวอักษร',
            'images.array' => 'ข้อมูลรูปภาพไม่ถูกต้อง',
            'images.max' => 'อัปโหลดรูปภาพได้ไม่เกิน '
                . self::MAX_IMAGES_PER_REQUEST
                . ' รูปต่อครั้ง',
            'images.*.required' => 'ไฟล์รูปภาพไม่ถูกต้อง',
            'images.*.image' => 'ไฟล์ต้องเป็นรูปภาพ',
            'images.*.mimes' => 'รูปภาพต้องเป็นไฟล์ชนิด jpg, jpeg, png หรือ webp',
            'images.*.max' => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 10MB',
        ]);
    }

    /**
     * ตัดช่องว่างก่อน Validation เพื่อให้ required ตรวจข้อความว่างได้ถูกต้อง
     */
    protected function trimRequestStrings(Request $request): void
    {
        $fields = [
            'family_fname',
            'member',
            'residence_status',
            'address',
            'moo',
            'soi',
            'road',
            'village',
            'zipcode',
            'phone',
            'outside_address',
            'inside_address',
            'environment',
            'neighbor',
            'member_relation',
            'problem',
            'need',
            'diagnose',
            'assistance',
            'comment',
            'modify',
            'teacher',
            'remark',
        ];

        $trimmed = [];

        foreach ($fields as $field) {
            if (!$request->exists($field)) {
                continue;
            }

            $value = $request->input($field);
            $trimmed[$field] = is_string($value) ? trim($value) : $value;
        }

        if ($trimmed !== []) {
            $request->merge($trimmed);
        }
    }

    /**
     * ตัดช่องว่างและเปลี่ยนข้อความว่างเป็น null
     */
    protected function normalizeVisitData(array $data): array
    {
        $stringFields = [
            'family_fname',
            'member',
            'residence_status',
            'address',
            'moo',
            'soi',
            'road',
            'village',
            'zipcode',
            'phone',
            'outside_address',
            'inside_address',
            'environment',
            'neighbor',
            'member_relation',
            'problem',
            'need',
            'diagnose',
            'assistance',
            'comment',
            'modify',
            'teacher',
            'remark',
        ];

        foreach ($stringFields as $field) {
            if (!array_key_exists($field, $data)) {
                continue;
            }

            $value = trim((string) $data[$field]);
            $data[$field] = $value === '' ? null : $value;
        }

        // ใช้รหัสไปรษณีย์จากฐานข้อมูลตำบลเป็นค่าหลักเมื่อมีข้อมูล
        if (!empty($data['sub_district_id'])) {
            $subDistrict = SubDistrict::find($data['sub_district_id']);

            if ($subDistrict && !empty($subDistrict->zipcode)) {
                $data['zipcode'] = (string) $subDistrict->zipcode;
            }
        }

        return $data;
    }

    protected function recordVisitActivity(
        int $clientId,
        int $visitFamilyId,
        array $data,
        string $title
    ): void {
        CaseActivity::where('client_id', $clientId)
            ->where('module', 'visit_family')
            ->delete();

        CaseActivity::record([
            'client_id' => $clientId,
            'module' => 'visit_family',
            'type' => 'success',
            'title' => $title,
            'description' => 'วันที่เยี่ยม: ' . ($data['visit_date'] ?? '-')
                . ' / ผู้ให้ข้อมูล: ' . ($data['family_fname'] ?? '-')
                . ' / ผู้เยี่ยม: ' . ($data['teacher'] ?? '-'),
            'occurred_at' => $data['visit_date'] ?? now('Asia/Bangkok'),
            'icon' => 'bi-house-heart',
            'url' => route('vitsitFamily.edit', $visitFamilyId),
        ]);
    }
}