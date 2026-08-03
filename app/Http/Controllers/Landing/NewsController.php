<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

class NewsController extends Controller
{
    private const IMAGE_DIRECTORY = 'upload/news';

    /**
     * ใช้ public_path() เพื่อให้ทำงานตรงกันทั้ง Local, Queue, CLI และ Shared Hosting
     * ไม่พึ่ง $_SERVER['DOCUMENT_ROOT'] ซึ่งอาจไม่มีค่าหรือชี้ผิดโฟลเดอร์
     */
    protected function uploadRootPath(): string
    {
        return public_path();
    }

    protected function saveNewsImage($file): string
    {
        $destinationPath = public_path(self::IMAGE_DIRECTORY);

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true, true);
        }

        $filename = Str::uuid()->toString() . '.jpg';
        $relativePath = self::IMAGE_DIRECTORY . '/' . $filename;
        $fullPath = public_path($relativePath);

        $manager = new ImageManager(new Driver());
        $image = $manager
            ->read($file->getRealPath())
            ->orient()
            ->scaleDown(width: 1200);

        $image
            ->toJpeg(quality: 72, progressive: true)
            ->save($fullPath);

        return $relativePath;
    }

    protected function deleteNewsImage(?string $relativePath): void
    {
        if (!$relativePath || !Str::startsWith($relativePath, self::IMAGE_DIRECTORY . '/')) {
            return;
        }

        $fullPath = public_path($relativePath);

        if (File::isFile($fullPath)) {
            File::delete($fullPath);
        }
    }

    public function index(): View
    {
        $news = News::query()
            ->select(['id', 'title', 'description', 'image', 'created_at'])
            ->latest('id')
            ->paginate(9)
            ->withQueryString();

        return view('landing.news.index', compact('news'));
    }

    public function create(): View
    {
        return view('landing.news.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'title' => Str::squish((string) $request->input('title')),
            'description' => trim((string) $request->input('description')),
        ]);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:20000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'title.required' => 'กรุณากรอกหัวข้อข่าว',
            'title.max' => 'หัวข้อข่าวต้องไม่เกิน 255 ตัวอักษร',
            'description.required' => 'กรุณากรอกรายละเอียดข่าว',
            'description.max' => 'รายละเอียดข่าวต้องไม่เกิน 20,000 ตัวอักษร',
            'image.image' => 'ไฟล์ต้องเป็นรูปภาพ',
            'image.mimes' => 'รูปภาพต้องเป็นไฟล์ jpg, jpeg, png หรือ webp',
            'image.max' => 'รูปภาพต้องมีขนาดไม่เกิน 10 MB',
        ]);

        $imagePath = null;

        try {
            if ($request->hasFile('image')) {
                $imagePath = $this->saveNewsImage($request->file('image'));
            }

            News::query()->create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'image' => $imagePath,
            ]);
        } catch (Throwable $exception) {
            $this->deleteNewsImage($imagePath);
            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'news' => 'ไม่สามารถบันทึกข่าวได้ กรุณาลองใหม่อีกครั้ง',
                ]);
        }

        return redirect()
            ->route('news.index')
            ->with('success', 'เพิ่มข่าวเรียบร้อยแล้ว');
    }

    public function show($id): View
    {
        $news = News::query()
            ->select(['id', 'title', 'description', 'image', 'created_at'])
            ->findOrFail($id);

        return view('landing.news.show', compact('news'));
    }
}
