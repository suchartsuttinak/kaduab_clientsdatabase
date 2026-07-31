<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\Estimate;
use App\Models\EstimatePicture;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use RuntimeException;
use Throwable;

class EstimateController extends Controller
{
    private ?ImageManager $imageManager = null;

    private function imageManager(): ImageManager
    {
        return $this->imageManager ??= new ImageManager(new Driver());
    }

    /**
     * บันทึกรูปเป็น JPEG พร้อมหมุนภาพและย่อขนาดเพื่อลดภาระ Shared Hosting
     */
    protected function saveEstimateImage(UploadedFile $file): string
    {
        $destinationPath = public_path('upload/estimate_pictures');

        if (! File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true, true);
        }

        if (! File::isDirectory($destinationPath) || ! is_writable($destinationPath)) {
            throw new RuntimeException('ไม่สามารถเขียนไฟล์ลงโฟลเดอร์รูปภาพการประเมินได้');
        }

        $filename = Str::uuid()->toString() . '.jpg';
        $fullPath = $destinationPath . DIRECTORY_SEPARATOR . $filename;

        try {
            $image = $this->imageManager()
                ->read($file->getRealPath())
                ->orient()
                ->scaleDown(width: 1200, height: 1200);

            $image->toJpeg(quality: 72, progressive: true)->save($fullPath);
        } catch (Throwable $exception) {
            File::delete($fullPath);
            throw $exception;
        }

        return 'upload/estimate_pictures/' . $filename;
    }

    /**
     * รองรับทั้ง path รุ่นใหม่ใน public/upload และ path รุ่นเดิมใน public/storage
     */
    protected function deleteEstimateImage(?string $path): void
    {
        $fullPath = $this->resolveEstimateImagePath($path);

        if ($fullPath && File::isFile($fullPath)) {
            File::delete($fullPath);
        }
    }

    protected function estimateImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', $path), '/');

        if (str_contains($normalized, '..')) {
            return null;
        }

        if (Str::startsWith($normalized, ['upload/', 'storage/'])) {
            return asset($normalized);
        }

        return asset('storage/' . $normalized);
    }

    private function resolveEstimateImagePath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', $path), '/');

        if (str_contains($normalized, '..')) {
            return null;
        }

        if (Str::startsWith($normalized, 'upload/estimate_pictures/')) {
            return public_path($normalized);
        }

        if (Str::startsWith($normalized, 'storage/estimate_pictures/')) {
            return public_path($normalized);
        }

        if (Str::startsWith($normalized, 'estimate_pictures/')) {
            return storage_path('app/public/' . $normalized);
        }

        return null;
    }

    private function findClientWithEstimates(int|string $clientId): Client
    {
        return Client::forUser(auth()->user())
            ->with([
                'estimates' => fn ($query) => $query
                    ->orderByDesc('date')
                    ->orderByDesc('id'),
                'estimates.pictures' => fn ($query) => $query->orderBy('id'),
            ])
            ->findOrFail($clientId);
    }

    private function findAccessibleEstimate(int|string $id, bool $withPictures = false): Estimate
    {
        $query = Estimate::query()
            ->whereKey($id)
            ->whereHas('client', function ($clientQuery) {
                $clientQuery->forUser(auth()->user());
            });

        if ($withPictures) {
            $query->with('pictures');
        }

        return $query->firstOrFail();
    }

    private function estimateRules(int $clientId, ?int $ignoreId = null): array
    {
        $uniqueDate = Rule::unique('estimates', 'date')
            ->where(fn ($query) => $query->where('client_id', $clientId));

        if ($ignoreId !== null) {
            $uniqueDate->ignore($ignoreId);
        }

        return [
            'date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
                $uniqueDate,
            ],
            'follo_no' => ['required', Rule::in(['หน่วยงานไปเอง', 'โทรศัพท์', 'จดหมาย'])],
            'results' => ['nullable', 'string', 'max:5000'],
            'family_income' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'guardian_job' => ['nullable', 'string', 'max:255'],
            'income_sufficiency' => ['required', Rule::in(['เพียงพอ', 'ไม่เพียงพอ'])],
            'income_reason' => ['nullable', 'string', 'max:3000'],
            'debt' => ['nullable', 'string', 'max:3000'],
            'housing_condition' => ['nullable', Rule::in(['ดี', 'พอใช้', 'ควรปรับปรุง'])],
            'teacher' => ['nullable', 'string', 'max:255'],
            'remark' => ['nullable', 'string', 'max:3000'],
            'pictures' => ['nullable', 'array', 'max:8'],
            'pictures.*' => [
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:10240',
                'dimensions:max_width=10000,max_height=10000',
            ],
            'remove_pictures' => ['nullable', 'array'],
            'remove_pictures.*' => ['integer', 'distinct'],
        ];
    }

    private function validationMessages(): array
    {
        return [
            'date.unique' => 'วันที่นี้ถูกบันทึกไว้แล้ว กรุณาเลือกวันอื่น',
            'date.required' => 'กรุณาเลือกวันที่',
            'date.date' => 'รูปแบบวันที่ไม่ถูกต้อง',
            'date.before_or_equal' => 'วันที่ติดตามต้องไม่เกินวันที่ปัจจุบัน',
            'follo_no.required' => 'กรุณาเลือกการดำเนินงาน',
            'follo_no.in' => 'ค่าการดำเนินงานไม่ถูกต้อง',
            'income_sufficiency.required' => 'กรุณาเลือกความเพียงพอของรายได้',
            'income_sufficiency.in' => 'ค่าความเพียงพอของรายได้ไม่ถูกต้อง',
            'housing_condition.in' => 'ค่าสภาพที่อยู่อาศัยไม่ถูกต้อง',
            'family_income.numeric' => 'รายได้ครอบครัวเฉลี่ย/เดือนต้องเป็นตัวเลข',
            'family_income.min' => 'รายได้ครอบครัวเฉลี่ย/เดือนต้องไม่น้อยกว่า 0',
            'family_income.max' => 'รายได้ครอบครัวเฉลี่ย/เดือนมีค่ามากเกินไป',
            'pictures.array' => 'ข้อมูลรูปภาพไม่ถูกต้อง',
            'pictures.max' => 'อัปโหลดรูปภาพได้ไม่เกิน 8 รูปต่อครั้ง',
            'pictures.*.image' => 'ไฟล์ต้องเป็นรูปภาพ',
            'pictures.*.mimes' => 'รูปภาพต้องเป็นไฟล์ jpeg, png, jpg หรือ webp',
            'pictures.*.max' => 'รูปภาพแต่ละไฟล์ต้องมีขนาดไม่เกิน 10MB',
            'pictures.*.dimensions' => 'รูปภาพมีความละเอียดสูงเกินกว่าที่ระบบรองรับ',
        ];
    }

    private function prepareEstimateData(array $validated): array
    {
        unset($validated['pictures'], $validated['remove_pictures']);

        if (($validated['income_sufficiency'] ?? 'เพียงพอ') === 'เพียงพอ') {
            $validated['income_reason'] = null;
        }

        return $validated;
    }

    /**
     * จัดลำดับครั้งที่ใหม่ด้วยคำสั่ง UPDATE เดียว ลด N+1 update queries
     */
    private function renumberEstimates(int $clientId): void
    {
        $ids = Estimate::query()
            ->where('client_id', $clientId)
            ->orderBy('date')
            ->orderBy('id')
            ->pluck('id')
            ->all();

        if ($ids === []) {
            return;
        }

        $caseParts = [];
        $bindings = [];

        foreach ($ids as $index => $id) {
            $caseParts[] = 'WHEN ? THEN ?';
            $bindings[] = $id;
            $bindings[] = $index + 1;
        }

        $inPlaceholders = implode(',', array_fill(0, count($ids), '?'));
        array_push($bindings, ...$ids);

        DB::update(
            'UPDATE estimates SET `count` = CASE id ' . implode(' ', $caseParts) .
            ' END WHERE id IN (' . $inPlaceholders . ')',
            $bindings
        );
    }

    /**
     * ให้ activity ของโมดูลสะท้อนรายการล่าสุดเสมอ รวมถึงหลังลบข้อมูล
     */
    private function syncEstimateActivity(
        int $clientId,
        string $title,
        ?Estimate $sourceEstimate = null
    ): void {
        CaseActivity::query()
            ->where('client_id', $clientId)
            ->where('module', 'estimate')
            ->delete();

        $activityEstimate = $sourceEstimate ?: Estimate::query()
            ->where('client_id', $clientId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->first();

        if (! $activityEstimate) {
            return;
        }

        CaseActivity::record([
            'client_id' => $clientId,
            'module' => 'estimate',
            'type' => 'success',
            'title' => $title,
            'description' => 'วันที่ประเมิน: ' . ($activityEstimate->date ?? '-') .
                ' / ครั้งที่ ' . ($activityEstimate->count ?? '-') .
                ' / ผลการประเมิน: ' . ($activityEstimate->results ?: '-'),
            'occurred_at' => Carbon::parse($activityEstimate->date, 'Asia/Bangkok')->startOfDay(),
            'icon' => 'bi-clipboard-data',
            'url' => route('estimate.show', $clientId),
        ]);
    }

    public function IndexEstimate($client_id)
    {
        return $this->ShowEstimate($client_id);
    }

    public function ShowEstimate($client_id)
    {
        $client = $this->findClientWithEstimates($client_id);

        return view('frontend.client.estimate.estimate_index', compact('client'));
    }

    public function AddEstimate($id)
    {
        return $this->EditEstimate($id);
    }

    public function StoreEstimate(Request $request)
    {
        $clientId = (int) $request->input('client_id');

        $client = Client::forUser(auth()->user())
            ->whereKey($clientId)
            ->firstOrFail();

        $validator = Validator::make(
            $request->all(),
            $this->estimateRules($client->id),
            $this->validationMessages()
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('form', 'add-estimate');
        }

        $data = $this->prepareEstimateData($validator->validated());
        $data['client_id'] = $client->id;
        $newImagePaths = [];

        try {
            // ประมวลผลรูปก่อนเปิด transaction เพื่อลดเวลาล็อกฐานข้อมูล
            foreach ($request->file('pictures', []) as $file) {
                $newImagePaths[] = $this->saveEstimateImage($file);
            }

            DB::beginTransaction();

            $estimate = Estimate::create($data);

            foreach ($newImagePaths as $path) {
                $estimate->pictures()->create(['path' => $path]);
            }

            $this->renumberEstimates($client->id);
            $estimate->refresh();
            $this->syncEstimateActivity($client->id, 'บันทึกการประเมินครอบครัว', $estimate);

            DB::commit();
        } catch (Throwable $exception) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            foreach ($newImagePaths as $path) {
                $this->deleteEstimateImage($path);
            }

            report($exception);

            return back()
                ->withInput()
                ->with('form', 'add-estimate')
                ->with([
                    'message' => 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
                    'alert-type' => 'error',
                ]);
        }

        return redirect()->route('estimate.show', $client->id)
            ->with([
                'message' => 'บันทึกข้อมูลเรียบร้อย',
                'alert-type' => 'success',
            ]);
    }

    public function EditEstimate($id)
    {
        $estimate = $this->findAccessibleEstimate($id, true);

        return response()->json([
            'id' => $estimate->id,
            'client_id' => $estimate->client_id,
            'date' => $estimate->date ? Carbon::parse($estimate->date)->format('Y-m-d') : null,
            'follo_no' => $estimate->follo_no,
            'results' => $estimate->results,
            'family_income' => $estimate->family_income,
            'guardian_job' => $estimate->guardian_job,
            'income_sufficiency' => $estimate->income_sufficiency ?? 'เพียงพอ',
            'income_reason' => $estimate->income_reason,
            'debt' => $estimate->debt,
            'housing_condition' => $estimate->housing_condition,
            'teacher' => $estimate->teacher,
            'remark' => $estimate->remark,
            'pictures' => $estimate->pictures->map(fn (EstimatePicture $picture) => [
                'id' => $picture->id,
                'url' => $this->estimateImageUrl($picture->path),
            ])->values(),
        ]);
    }

    public function UpdateEstimate(Request $request, $id)
    {
        $estimate = $this->findAccessibleEstimate($id, true);

        $validator = Validator::make(
            $request->all(),
            $this->estimateRules($estimate->client_id, $estimate->id),
            $this->validationMessages()
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with([
                    'form' => 'edit-estimate',
                    'edit_estimate_id' => $estimate->id,
                ]);
        }

        $data = $this->prepareEstimateData($validator->validated());
        $newImagePaths = [];
        $oldImagePaths = [];

        try {
            // ประมวลผลรูปใหม่ก่อนเปิด transaction เพื่อลดเวลาล็อกฐานข้อมูล
            foreach ($request->file('pictures', []) as $file) {
                $newImagePaths[] = $this->saveEstimateImage($file);
            }

            DB::beginTransaction();

            $estimate->update($data);

            $removeIds = collect($request->input('remove_pictures', []))
                ->filter(fn ($value) => is_numeric($value))
                ->map(fn ($value) => (int) $value)
                ->unique()
                ->values();

            if ($removeIds->isNotEmpty()) {
                $picturesToRemove = EstimatePicture::query()
                    ->where('estimate_id', $estimate->id)
                    ->whereIn('id', $removeIds)
                    ->get();

                $oldImagePaths = $picturesToRemove->pluck('path')->filter()->all();

                EstimatePicture::query()
                    ->where('estimate_id', $estimate->id)
                    ->whereIn('id', $picturesToRemove->pluck('id'))
                    ->delete();
            }

            foreach ($newImagePaths as $path) {
                $estimate->pictures()->create(['path' => $path]);
            }

            $this->renumberEstimates($estimate->client_id);
            $estimate->refresh();
            $this->syncEstimateActivity($estimate->client_id, 'แก้ไขการประเมินครอบครัว', $estimate);

            DB::commit();
        } catch (Throwable $exception) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            foreach ($newImagePaths as $path) {
                $this->deleteEstimateImage($path);
            }

            report($exception);

            return back()
                ->withInput()
                ->with([
                    'form' => 'edit-estimate',
                    'edit_estimate_id' => $estimate->id,
                    'message' => 'ไม่สามารถแก้ไขข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
                    'alert-type' => 'error',
                ]);
        }

        foreach ($oldImagePaths as $path) {
            $this->deleteEstimateImage($path);
        }

        return redirect()->route('estimate.show', $estimate->client_id)
            ->with([
                'message' => 'แก้ไขข้อมูลเรียบร้อย',
                'alert-type' => 'success',
            ]);
    }

    public function DeleteEstimate($id)
    {
        $estimate = $this->findAccessibleEstimate($id, true);
        $clientId = $estimate->client_id;
        $imagePaths = $estimate->pictures->pluck('path')->filter()->all();

        try {
            DB::transaction(function () use ($estimate, $clientId) {
                $estimate->pictures()->delete();
                $estimate->delete();
                $this->renumberEstimates($clientId);
                $this->syncEstimateActivity($clientId, 'อัปเดตการประเมินครอบครัว');
            });
        } catch (Throwable $exception) {
            report($exception);

            return back()->with([
                'message' => 'ไม่สามารถลบข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
                'alert-type' => 'error',
            ]);
        }

        foreach ($imagePaths as $path) {
            $this->deleteEstimateImage($path);
        }

        return redirect()->route('estimate.show', $clientId)
            ->with([
                'message' => 'ลบข้อมูลเรียบร้อย',
                'alert-type' => 'success',
            ]);
    }

    public function CheckDuplicate(Request $request)
    {
        $validated = $request->validate([
            'client_id' => ['required', 'integer'],
            'date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],
            'id' => ['nullable', 'integer'],
        ]);

        $client = Client::forUser(auth()->user())
            ->whereKey($validated['client_id'])
            ->firstOrFail();

        $query = Estimate::query()
            ->where('client_id', $client->id)
            ->whereDate('date', $validated['date']);

        if (! empty($validated['id'])) {
            $query->where('id', '!=', $validated['id']);
        }

        return response()->json(['duplicate' => $query->exists()]);
    }

    public function ReportEstimate($id)
    {
        $estimate = Estimate::query()
            ->whereKey($id)
            ->whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->with([
                'client',
                'pictures' => fn ($query) => $query->orderBy('id'),
            ])
            ->firstOrFail();

        return view('frontend.client.estimate.estimate_report', compact('estimate'));
    }
}