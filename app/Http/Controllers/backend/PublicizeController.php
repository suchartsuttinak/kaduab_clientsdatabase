<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Publicize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PublicizeController extends Controller
{
    public function index(Request $request)
    {
        $categories = ['all' => 'ทั้งหมด'] + Publicize::CATEGORIES;

        $activeCategory = $request->get('category', 'all');
        $yearBe = $request->get('year_be');

        if (!array_key_exists($activeCategory, $categories)) {
            $activeCategory = 'all';
        }

        $query = Publicize::query();

        if ($activeCategory !== 'all') {
            $query->where('category', $activeCategory);
        }

        if (!empty($yearBe) && is_numeric($yearBe)) {
            $yearAd = (int) $yearBe - 543;
            $query->whereYear('recorded_at', $yearAd);
        }

        $publicizes = $query
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->get();

        $yearOptions = Publicize::selectRaw('YEAR(recorded_at) as year_ad')
            ->distinct()
            ->orderByDesc('year_ad')
            ->pluck('year_ad')
            ->mapWithKeys(function ($yearAd) {
                return [$yearAd + 543 => $yearAd + 543];
            });

        $categoryCounts = [];
        foreach (Publicize::CATEGORIES as $key => $label) {
            $countQuery = Publicize::query()->where('category', $key);

            if (!empty($yearBe) && is_numeric($yearBe)) {
                $countQuery->whereYear('recorded_at', ((int) $yearBe - 543));
            }

            $categoryCounts[$key] = $countQuery->count();
        }

        $allCountQuery = Publicize::query();
        if (!empty($yearBe) && is_numeric($yearBe)) {
            $allCountQuery->whereYear('recorded_at', ((int) $yearBe - 543));
        }
        $categoryCounts['all'] = $allCountQuery->count();

        return view('backend.publicizes.index', compact(
            'categories',
            'activeCategory',
            'publicizes',
            'yearOptions',
            'yearBe',
            'categoryCounts'
        ));
    }

    public function create()
    {
        $categories = Publicize::CATEGORIES;

        return view('backend.publicizes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $categories = array_keys(Publicize::CATEGORIES);

        $validated = $request->validate([
            'recorded_at' => ['required', 'date', 'before_or_equal:' . now('Asia/Bangkok')->toDateString()],
            'category'    => ['required', Rule::in($categories)],
            'title'       => ['required', 'string', 'max:255'],
            'file'        => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'recorded_at.required' => 'กรุณาเลือกวันที่บันทึก',
            'recorded_at.before_or_equal' => 'วันที่บันทึกต้องไม่เกินวันที่ปัจจุบัน',
            'category.required'    => 'กรุณาเลือกประเภท',
            'title.required'       => 'กรุณากรอกชื่อเรื่อง',
            'file.required'        => 'กรุณาอัปโหลดไฟล์ PDF',
            'file.mimes'           => 'รองรับเฉพาะไฟล์ PDF เท่านั้น',
            'file.max'             => 'ขนาดไฟล์ต้องไม่เกิน 10 MB',
        ]);

        $file = $request->file('file');
        $filePath = $this->uploadPublicizeFile($file, $validated['category']);

        Publicize::create([
            'recorded_at' => $validated['recorded_at'],
            'category'    => $validated['category'],
            'title'       => $validated['title'],
            'file_path'   => $filePath,
            'file_name'   => Str::limit(basename((string) $file->getClientOriginalName()), 255, ''),
        ]);

        return redirect()
            ->route('publicizes.index', ['category' => $validated['category']])
            ->with('success', 'บันทึกข้อมูลประชาสัมพันธ์เรียบร้อยแล้ว');
    }

    public function edit(Publicize $publicize)
    {
        $categories = Publicize::CATEGORIES;

        return view('backend.publicizes.edit', compact('publicize', 'categories'));
    }

    public function update(Request $request, Publicize $publicize)
    {
        $categories = array_keys(Publicize::CATEGORIES);

        $validated = $request->validate([
            'recorded_at' => ['required', 'date', 'before_or_equal:' . now('Asia/Bangkok')->toDateString()],
            'category'    => ['required', Rule::in($categories)],
            'title'       => ['required', 'string', 'max:255'],
            'file'        => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'recorded_at.required' => 'กรุณาเลือกวันที่บันทึก',
            'recorded_at.before_or_equal' => 'วันที่บันทึกต้องไม่เกินวันที่ปัจจุบัน',
            'category.required'    => 'กรุณาเลือกประเภท',
            'title.required'       => 'กรุณากรอกชื่อเรื่อง',
            'file.mimes'           => 'รองรับเฉพาะไฟล์ PDF เท่านั้น',
            'file.max'             => 'ขนาดไฟล์ต้องไม่เกิน 10 MB',
        ]);

        $data = [
            'recorded_at' => $validated['recorded_at'],
            'category'    => $validated['category'],
            'title'       => $validated['title'],
        ];

        $oldFilePath = $publicize->file_path;
        $newFilePath = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $newFilePath = $this->uploadPublicizeFile($file, $validated['category']);
            $data['file_path'] = $newFilePath;
            $data['file_name'] = Str::limit(
                basename((string) $file->getClientOriginalName()),
                255,
                ''
            );
        }

        try {
            $publicize->update($data);
        } catch (\Throwable $exception) {
            if ($newFilePath) {
                $this->deletePublicizeFile($newFilePath);
            }

            throw $exception;
        }

        if ($newFilePath && $oldFilePath !== $newFilePath) {
            $this->deletePublicizeFile($oldFilePath);
        }

        return redirect()
            ->route('publicizes.index', ['category' => $validated['category']])
            ->with('success', 'แก้ไขข้อมูลเรียบร้อยแล้ว');
    }

    public function destroy(Publicize $publicize)
    {
        $category = $publicize->category;

        $this->deletePublicizeFile($publicize->file_path);

        $publicize->delete();

        return redirect()
            ->route('publicizes.index', ['category' => $category])
            ->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    public function viewFile(Publicize $publicize)
    {
        $path = $this->resolvePublicizeFile($publicize->file_path);
        abort_unless($path && $this->hasPdfSignature($path), 404);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="publicize-' . $publicize->id . '.pdf"',
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function uploadPublicizeFile($file, string $category): string
    {
        if (!$this->hasPdfSignature($file->getRealPath())) {
            abort(422, 'ไฟล์เอกสารไม่ใช่ PDF ที่ถูกต้อง');
        }

        $safeCategory = array_key_exists($category, Publicize::CATEGORIES)
            ? $category
            : 'other';
        $folder = 'publicizes/' . $safeCategory;
        $destinationPath = storage_path('app/private/' . $folder);
        File::ensureDirectoryExists($destinationPath);

        $filename = now('Asia/Bangkok')->format('YmdHis') . '_' . bin2hex(random_bytes(8)) . '.pdf';
        $file->move($destinationPath, $filename);

        return $folder . '/' . $filename;
    }

    private function resolvePublicizeFile(?string $filePath): ?string
    {
        if (empty($filePath)) {
            return null;
        }

        $relative = ltrim(str_replace('\\', '/', trim($filePath)), '/');
        if ($relative === '' || str_contains($relative, '..')) {
            return null;
        }

        if (str_starts_with($relative, 'publicizes/')) {
            $path = storage_path('app/private/' . $relative);
            return File::isFile($path) ? $path : null;
        }

        // รองรับไฟล์เดิมที่เคยเก็บใต้ public/upload/publicizes
        if (str_starts_with($relative, 'upload/publicizes/')) {
            $path = public_path($relative);
            return File::isFile($path) ? $path : null;
        }

        return null;
    }

    private function hasPdfSignature(?string $path): bool
    {
        if (!$path || !File::isFile($path)) {
            return false;
        }

        $handle = @fopen($path, 'rb');
        if (!$handle) {
            return false;
        }

        try {
            return fread($handle, 5) === '%PDF-';
        } finally {
            fclose($handle);
        }
    }

    private function deletePublicizeFile(?string $filePath): void
    {
        $path = $this->resolvePublicizeFile($filePath);

        if ($path && File::isFile($path)) {
            File::delete($path);
        }

        // รองรับ storage/public รูปแบบเก่ามาก หากยังมีข้อมูลค้างอยู่
        if ($filePath && !str_contains($filePath, '..') && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }

}