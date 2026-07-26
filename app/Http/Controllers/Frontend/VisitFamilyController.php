<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\VisitFamily;
use App\Models\Province;
use App\Models\District;
use App\Models\SubDistrict;
use App\Models\Income;
use App\Models\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\CaseActivity;

class VisitFamilyController extends Controller
{
    // =====================================================
// PATCH:
// Save + Optimize Visit Family Images
// สำหรับ Shared Hosting / Production
// =====================================================
protected function saveVisitImage($file, bool $cover = false): array
{
    $destinationPath = public_path('upload/visit_images');

    // =====================================================
    // PATCH:
    // สร้างโฟลเดอร์อัตโนมัติ
    // =====================================================
    if (!File::exists($destinationPath)) {
        File::makeDirectory($destinationPath, 0755, true);
    }

    // =====================================================
    // PATCH:
    // ตั้งชื่อไฟล์ใหม่
    // =====================================================
    $filename = Str::uuid()->toString() . '.jpg';

    $relativePath = 'upload/visit_images/' . $filename;

    // =====================================================
    // PATCH:
    // ใช้ Intervention Image
    // =====================================================
    $manager = new ImageManager(new Driver());

    $image = $manager->read($file->getRealPath());

    // =====================================================
    // PATCH:
    // หมุนภาพอัตโนมัติจากมือถือ
    // =====================================================
    $image = $image->orient();

    // =====================================================
    // PATCH:
    // ลดขนาดภาพ
    // =====================================================
    if ($cover) {

        // ================================================
        // PATCH:
        // รูปปก / replace image
        // ================================================
        $image->cover(1000, 700);

    } else {

        // ================================================
        // PATCH:
        // รูปทั่วไป
        // ================================================
        $image->scaleDown(width: 1000);
    }

    // =====================================================
    // PATCH:
    // บันทึกแบบ Progressive JPEG
    // โหลดเร็วขึ้นบนเว็บ
    // =====================================================
    $encoded = $image->toJpeg(
        quality: 70,
        progressive: true
    );

    $encoded->save(public_path($relativePath));

    // =====================================================
    // PATCH:
    // คืนค่าข้อมูลไฟล์
    // =====================================================
    return [
        'path' => $relativePath,
        'name' => $file->getClientOriginalName(),
        'mime' => 'image/jpeg',
        'size' => File::size(public_path($relativePath)),
    ];
}

    protected function deleteVisitImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        $fullPath = str_starts_with($path, 'upload/')
            ? public_path($path)
            : public_path('storage/' . $path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    protected function visitImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return str_starts_with($path, 'upload/')
            ? asset($path)
            : asset('storage/' . $path);
    }

    public function AddvisitFamily($client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $incomes = Income::all();
        $provinces = Province::all();

        $districts = [];
        $sub_districts = [];

        $visitFamily = VisitFamily::where('client_id', $client->id)->first();

        if ($visitFamily) {
            return redirect()
                ->route('vitsitFamily.edit', $visitFamily->id)
                ->with('warning', 'มีการบันทึกข้อมูลรายนี้แล้ว กรุณาแก้ไขข้อมูล');
        }

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
        $districts = District::where('province_id', $province_id)->get();

        return response()->json($districts);
    }

    public function getSubdistricts($district_id)
    {
        $subdistricts = SubDistrict::where('district_id', $district_id)->get();

        return response()->json($subdistricts);
    }

    public function getZipcode($subdistrict_id)
    {
        $subdistrict = SubDistrict::find($subdistrict_id);

        return response()->json([
            'zipcode' => $subdistrict ? $subdistrict->zipcode : null
        ]);
    }

   public function StoreVisitFamily(Request $request, $client_id)
        {
            $client = Client::forUser(auth()->user())->findOrFail($client_id);

            $validated = $request->validate([
                'visit_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],

                'family_fname'    => 'required|string|max:255',
                'family_age'      => 'nullable|integer',
                'member'          => 'nullable|string|max:255',
                'residence_status'=> 'nullable|string|max:100',
                'address'         => 'nullable|string|max:255',
                'moo'             => 'nullable|string|max:50',
                'soi'             => 'nullable|string|max:50',
                'road'            => 'nullable|string|max:255',
                'village'         => 'nullable|string|max:255',
                'province_id'     => 'required|integer',
                'district_id'     => 'required|integer',
                'sub_district_id' => 'required|integer',
                'zipcode'         => 'required|string|max:10',
                'phone'           => 'nullable|string|max:20',
                'outside_address' => 'nullable|string',
                'inside_address'  => 'nullable|string',
                'environment'     => 'nullable|string',
                'neighbor'        => 'nullable|string',
                'member_relation' => 'nullable|string',
                'income_id'       => 'nullable|integer',
                'problem'         => 'nullable|string',
                'need'            => 'nullable|string',
                'diagnose'        => 'nullable|string',
                'assistance'      => 'nullable|string',
                'comment'         => 'nullable|string',
                'modify'          => 'nullable|string',
                'teacher'         => 'required|string',
                'remark'          => 'nullable|string',
                'images'          => 'nullable|array',
                'images.*'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            ], [
                'visit_date.required' => 'กรุณาระบุวันที่เยี่ยมบ้าน',
                'visit_date.date' => 'รูปแบบวันที่เยี่ยมบ้านไม่ถูกต้อง',
                'visit_date.before_or_equal' => 'วันที่เยี่ยมบ้านต้องไม่เกินวันที่ปัจจุบัน',

                'family_fname.required'    => 'กรุณากรอกชื่อผู้ให้ข้อมูล',
                'family_fname.max'         => 'ชื่อผู้ให้ข้อมูลต้องไม่เกิน 255 ตัวอักษร',
                'family_age.integer'       => 'อายุต้องเป็นตัวเลข',
                'province_id.required'     => 'กรุณาเลือกจังหวัด',
                'district_id.required'     => 'กรุณาเลือกอำเภอ',
                'sub_district_id.required' => 'กรุณาเลือกตำบล',
                'zipcode.required'         => 'กรุณากรอกรหัสไปรษณีย์',
                'zipcode.max'              => 'รหัสไปรษณีย์ต้องไม่เกิน 10 หลัก',
                'teacher.required'         => 'กรุณาระบุผู้ที่เยี่ยมบ้าน',
                'images.*.image'           => 'ไฟล์ต้องเป็นรูปภาพ',
                'images.*.mimes'           => 'รูปภาพต้องเป็นไฟล์ชนิด jpg, jpeg, png หรือ webp',
                'images.*.max'             => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 10MB',
            ]);

            $validated['client_id'] = $client->id;
            $validated['count'] = 1;

            unset($validated['images']);

            $visitFamily = VisitFamily::create($validated);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $saved = $this->saveVisitImage($file, false);

                    Image::create([
                        'file_path'       => $saved['path'],
                        'file_name'       => $saved['name'],
                        'mime_type'       => $saved['mime'],
                        'size'            => $saved['size'],
                        'visit_family_id' => $visitFamily->id,
                        'client_id'       => $visitFamily->client_id,
                    ]);
                }
            }

                CaseActivity::where('client_id', $client->id)
                ->where('module', 'visit_family')
                ->delete();

                CaseActivity::record([
                'client_id'   => $client->id,
                'module'      => 'visit_family',
                'type'        => 'success',
                'title'       => 'บันทึกการเยี่ยมบ้านครอบครัว',
                'description' => 'วันที่เยี่ยม: ' . ($validated['visit_date'] ?? '-') .
                                ' / ผู้ให้ข้อมูล: ' . ($validated['family_fname'] ?? '-') .
                                ' / ผู้เยี่ยม: ' . ($validated['teacher'] ?? '-'),
                'occurred_at' => now(),
                'icon'        => 'bi-house-heart',
                'url'         => route('vitsitFamily.edit', $visitFamily->id),
            ]);

            return redirect()->route('visitFamily.create', $client->id)
                ->with('success', 'บันทึกข้อมูลเรียบร้อย');
        }
  public function EditVisitFamily($id)
{
    $visitFamily = VisitFamily::where('id', $id)
        ->whereHas('client', function ($q) {
            $q->forUser(auth()->user());
        })

        // =====================================================
        // PATCH:
        // โหลดรูปภาพเดิมมาพร้อมข้อมูล visitFamily
        // แก้ปัญหาหน้าแก้ไขไม่แสดงรูปที่เคยอัปโหลด
        // =====================================================
        ->with('images')
        ->firstOrFail();

    $client = Client::forUser(auth()->user())
        ->findOrFail($visitFamily->client_id);

    $incomes = Income::all();
    $provinces = Province::all();

    $districts = District::where('province_id', $visitFamily->province_id)->get();

    $sub_districts = SubDistrict::where('district_id', $visitFamily->district_id)->get();

    // =====================================================
    // PATCH:
    // ใช้ collection จาก relation ที่โหลดมาแล้ว
    // =====================================================
    $images = $visitFamily->images ?? collect();

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
        $validated = $request->validate([
              'visit_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],

            'family_fname'    => 'required|string|max:255',
            'family_age'      => 'nullable|integer',
            'member'          => 'nullable|string|max:255',
            'residence_status'=> 'nullable|string|max:100',
            'address'         => 'nullable|string|max:255',
            'moo'             => 'nullable|string|max:50',
            'soi'             => 'nullable|string|max:50',
            'road'            => 'nullable|string|max:255',
            'village'         => 'nullable|string|max:255',
            'province_id'     => 'required|integer',
            'district_id'     => 'required|integer',
            'sub_district_id' => 'required|integer',
            'zipcode'         => 'required|string|max:10',
            'phone'           => 'nullable|string|max:20',
            'outside_address' => 'nullable|string',
            'inside_address'  => 'nullable|string',
            'environment'     => 'nullable|string',
            'neighbor'        => 'nullable|string',
            'member_relation' => 'nullable|string',
            'income_id'       => 'nullable|integer',
            'problem'         => 'nullable|string',
            'need'            => 'nullable|string',
            'diagnose'        => 'nullable|string',
            'assistance'      => 'nullable|string',
            'comment'         => 'nullable|string',
            'modify'          => 'nullable|string',
            'teacher'         => 'required|string',
            'remark'          => 'nullable|string',
            'images'          => 'nullable|array',
            'images.*'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ],[
               'visit_date.required' => 'กรุณาระบุวันที่เยี่ยมบ้าน',
                'visit_date.date' => 'รูปแบบวันที่เยี่ยมบ้านไม่ถูกต้อง',
                'visit_date.before_or_equal' => 'วันที่เยี่ยมบ้านต้องไม่เกินวันที่ปัจจุบัน',

                'family_fname.required'    => 'กรุณากรอกชื่อผู้ให้ข้อมูล',
                'family_fname.max'         => 'ชื่อผู้ให้ข้อมูลต้องไม่เกิน 255 ตัวอักษร',
                'family_age.integer'       => 'อายุต้องเป็นตัวเลข',
                'province_id.required'     => 'กรุณาเลือกจังหวัด',
                'district_id.required'     => 'กรุณาเลือกอำเภอ',
                'sub_district_id.required' => 'กรุณาเลือกตำบล',
                'zipcode.required'         => 'กรุณากรอกรหัสไปรษณีย์',
                'zipcode.max'              => 'รหัสไปรษณีย์ต้องไม่เกิน 10 หลัก',
                'teacher.required'         => 'กรุณาระบุผู้ที่เยี่ยมบ้าน',
                'images.*.image'           => 'ไฟล์ต้องเป็นรูปภาพ',
                'images.*.mimes'           => 'รูปภาพต้องเป็นไฟล์ชนิด jpg, jpeg, png หรือ webp',
                'images.*.max'             => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 10MB',
            ]);

        $visitFamily = VisitFamily::where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->firstOrFail();

        $validated['client_id'] = $visitFamily->client_id;

        Client::forUser(auth()->user())->findOrFail($validated['client_id']);

        unset($validated['images']);

        $visitFamily->update($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $saved = $this->saveVisitImage($file, true);

                Image::create([
                    'file_path'       => $saved['path'],
                    'file_name'       => $saved['name'],
                    'mime_type'       => $saved['mime'],
                    'size'            => $saved['size'],
                    'visit_family_id' => $visitFamily->id,
                    'client_id'       => $visitFamily->client_id,
                ]);
            }
        }

                CaseActivity::where('client_id', $visitFamily->client_id)
                    ->where('module', 'visit_family')
                    ->delete();

                CaseActivity::record([
                    'client_id'   => $visitFamily->client_id,
                    'module'      => 'visit_family',
                    'type'        => 'success',
                    'title'       => 'แก้ไขข้อมูลการเยี่ยมบ้านครอบครัว',
                    'description' => 'วันที่เยี่ยม: ' . ($validated['visit_date'] ?? '-') .
                                    ' / ผู้ให้ข้อมูล: ' . ($validated['family_fname'] ?? '-') .
                                    ' / ผู้เยี่ยม: ' . ($validated['teacher'] ?? '-'),
                    'occurred_at' => now(),
                    'icon'        => 'bi-house-heart',
                    'url'         => route('vitsitFamily.edit', $visitFamily->id),
                ]);

        return redirect()->route('vitsitFamily.edit', $id)
            ->with('success', 'แก้ไขข้อมูลเรียบร้อย');
    }

    public function destroy($id)
    {
        $image = Image::where('id', $id)
            ->whereHas('visitFamily.client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->first();

        if (!$image) {
            return response()->json(['error' => 'ไม่พบรูปภาพ'], 404);
        }

        $this->deleteVisitImage($image->file_path);

        $image->delete();

        return response()->json(['success' => true]);
    }

    public function replaceImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $image = Image::where('id', $id)
            ->whereHas('visitFamily.client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->first();

        if (!$image) {
            return response()->json(['error' => 'ไม่พบรูปภาพ'], 404);
        }

        $this->deleteVisitImage($image->file_path);

        $file = $request->file('image');
        $saved = $this->saveVisitImage($file, true);

        $image->update([
            'file_path' => $saved['path'],
            'file_name' => $saved['name'],
            'mime_type' => $saved['mime'],
            'size'      => $saved['size'],
        ]);

        return response()->json([
            'success' => true,
            'id'      => $image->id,
            'url'     => $this->visitImageUrl($image->file_path),
        ]);
    }

    public function ReportVisitFamily($id)
    {
        $visitFamily = VisitFamily::with(['client', 'income', 'images'])
            ->where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->firstOrFail();

        $client = Client::forUser(auth()->user())
            ->findOrFail($visitFamily->client_id);

        return view('frontend.client.visitFamily.visitFamily_report', compact(
            'visitFamily',
            'client'
        ));
    }
}