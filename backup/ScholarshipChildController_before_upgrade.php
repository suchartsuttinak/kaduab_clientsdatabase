<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipChild;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ScholarshipChildController extends Controller
{
   protected function saveChildPhoto($file): string
{
    // =====================================================
    // PATCH:
    // path จริงสำหรับ shared hosting
    // =====================================================
    $destinationPath = public_path('upload/scholarship_children');

    if (!File::exists($destinationPath)) {
        File::makeDirectory($destinationPath, 0755, true);
    }

    $filename = Str::uuid()->toString() . '.jpg';

    $relativePath = 'upload/scholarship_children/' . $filename;

    $fullPath = public_path($relativePath);

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
    $image->scaleDown(width: 1000);

    // =====================================================
    // PATCH:
    // Progressive JPEG
    // โหลดไวขึ้นบนเว็บจริง
    // =====================================================
    $encoded = $image->toJpeg(
        quality: 70,
        progressive: true
    );

    $encoded->save($fullPath);

    return $relativePath;
}

    protected function deleteChildPhoto(?string $path): void
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

    public function index(Request $request)
    {
        $academicYear = $request->academic_year;
        $keyword = $request->keyword;

        $years = ScholarshipChild::select('academic_year')
            ->whereNotNull('academic_year')
            ->distinct()
            ->orderByDesc('academic_year')
            ->pluck('academic_year');

        $children = ScholarshipChild::query()
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('first_name', 'like', '%' . $keyword . '%')
                        ->orWhere('last_name', 'like', '%' . $keyword . '%');
                });
            })
            ->when($academicYear, function ($query) use ($academicYear) {
                $query->where('academic_year', $academicYear);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('landing.scholarship.children.index', compact(
            'children',
            'years',
            'academicYear',
            'keyword'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'gender'          => 'nullable|in:male,female',
            'age'             => 'nullable|integer|min:1|max:120',
            'education_level' => 'nullable|string|max:255',
            'school_name'     => 'nullable|string|max:255',
            'academic_year'   => ['required', 'regex:/^[0-9]{4}$/'],
            'current_address' => 'nullable|string',
            'guardian_name'   => 'nullable|string|max:255',
            'phone'           => 'nullable|string|max:30',
            'reason'          => 'nullable|string',
            'help_needed'     => 'nullable|string',
            'more_detail'     => 'nullable|string',
            'photo'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ], [
            'academic_year.required' => 'กรุณากรอกปีการศึกษาที่ขอรับทุน',
            'academic_year.regex'    => 'ปีการศึกษาต้องเป็นตัวเลข พ.ศ. 4 หลักเท่านั้น เช่น 2568',
        ]);

        $data = $request->except(['photo', '_token', '_method']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->saveChildPhoto($request->file('photo'));
        }

        ScholarshipChild::create($data);

        return redirect()
            ->route('scholarship.children.index')
            ->with('success', 'บันทึกข้อมูลผู้ขอรับทุนเรียบร้อยแล้ว');
    }

    public function update(Request $request, $id)
    {
        $child = ScholarshipChild::findOrFail($id);

        $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'gender'          => 'nullable|in:male,female',
            'age'             => 'nullable|integer|min:1|max:120',
            'education_level' => 'nullable|string|max:255',
            'school_name'     => 'nullable|string|max:255',
            'academic_year'   => ['required', 'regex:/^[0-9]{4}$/'],
            'current_address' => 'nullable|string',
            'guardian_name'   => 'nullable|string|max:255',
            'phone'           => 'nullable|string|max:30',
            'reason'          => 'nullable|string',
            'help_needed'     => 'nullable|string',
            'more_detail'     => 'nullable|string',
            'photo'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ], [
            'academic_year.required' => 'กรุณากรอกปีการศึกษาที่ขอรับทุน',
            'academic_year.regex'    => 'ปีการศึกษาต้องเป็นตัวเลข พ.ศ. 4 หลักเท่านั้น เช่น 2568',
            'gender.in'              => 'กรุณาเลือกเพศให้ถูกต้อง',
        ]);

        $data = $request->except(['photo', '_token', '_method']);

        if ($request->hasFile('photo')) {
            $this->deleteChildPhoto($child->photo);
            $data['photo'] = $this->saveChildPhoto($request->file('photo'));
        }

        $child->update($data);

        return redirect()
            ->route('scholarship.children.index')
            ->with('success', 'แก้ไขข้อมูลเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $child = ScholarshipChild::findOrFail($id);

        $this->deleteChildPhoto($child->photo);

        $child->delete();

        return redirect()
            ->route('scholarship.children.index')
            ->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    public function report(Request $request)
    {
        $academicYear = $request->academic_year;
        $keyword = $request->keyword;

        $years = ScholarshipChild::select('academic_year')
            ->whereNotNull('academic_year')
            ->distinct()
            ->orderByDesc('academic_year')
            ->pluck('academic_year');

        $children = ScholarshipChild::query()
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('first_name', 'like', '%' . $keyword . '%')
                        ->orWhere('last_name', 'like', '%' . $keyword . '%');
                });
            })
            ->when($academicYear, function ($query) use ($academicYear) {
                $query->where('academic_year', $academicYear);
            })
            ->orderBy('academic_year', 'desc')
            ->orderBy('first_name')
            ->get();

        return view('landing.scholarship.children.report', compact(
            'children',
            'years',
            'academicYear',
            'keyword'
        ));
    }

    public function publicReport()
    {
        $latestYear = ScholarshipChild::query()
            ->whereNotNull('academic_year')
            ->max('academic_year');

        $children = ScholarshipChild::query()
            ->when($latestYear, function ($query) use ($latestYear) {
                $query->where('academic_year', $latestYear);
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('landing.scholarship.children.public_report', [
            'children'   => $children,
            'latestYear' => $latestYear,
        ]);
    }
}