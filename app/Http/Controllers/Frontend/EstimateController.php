<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Client;
use App\Models\Estimate;
use App\Models\EstimatePicture;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\CaseActivity;

class EstimateController extends Controller
{
   protected function saveEstimateImage($file): string
{
    $destinationPath = public_path('upload/estimate_pictures');

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
    // ป้องกัน shared hosting ทำงานหนักเกิน
    // =====================================================
    $image->scaleDown(width: 1000);

    // =====================================================
    // PATCH:
    // บันทึกแบบ progressive JPEG
    // โหลดเร็วขึ้นบนเว็บ
    // =====================================================
    $encoded = $image->toJpeg(
        quality: 70,
        progressive: true
    );

    $encoded->save($destinationPath . '/' . $filename);

    // =====================================================
    // PATCH:
    // คืน path
    // =====================================================
    return 'upload/estimate_pictures/' . $filename;
}

    protected function deleteEstimateImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    public function IndexEstimate($client_id)
    {
        $client = Client::forUser(auth()->user())
            ->with('estimates.pictures')
            ->findOrFail($client_id);

        return view('frontend.client.estimate.estimate_index', compact('client'));
    }

    public function ShowEstimate($client_id)
    {
        $client = Client::forUser(auth()->user())
            ->with('estimates.pictures')
            ->findOrFail($client_id);

        return view('frontend.client.estimate.estimate_index', compact('client'));
    }

    public function AddEstimate($id)
    {
        $estimate = Estimate::where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->with('pictures')
            ->firstOrFail();

        return response()->json($estimate);
    }

   public function StoreEstimate(Request $request)
        {
            $client = Client::forUser(auth()->user())
                ->where('id', $request->client_id)
                ->firstOrFail();

            $validator = Validator::make($request->all(), [
                'date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),

                Rule::unique('estimates')->where(function ($query) use ($request) {
                    return $query->where('client_id', $request->client_id);
                }),
            ],
                'follo_no' => 'required',
                'results'  => 'nullable|string',
                'family_income' => 'nullable|numeric|min:0',
                'guardian_job' => 'nullable|string|max:255',
                'income_sufficiency' => 'required|in:เพียงพอ,ไม่เพียงพอ',
                'income_reason' => 'nullable|string',
                'debt' => 'nullable|string',
                'housing_condition' => 'nullable|in:ดี,พอใช้,ควรปรับปรุง',
                'teacher'  => 'nullable|string|max:255',
                'remark'   => 'nullable|string',
                'client_id'=> 'required|exists:clients,id',

                // PATCH: รองรับรูปใหญ่จากมือถือ แล้วค่อยบีบอัดเอง
                'pictures'   => 'nullable|array',
                'pictures.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240',
            ], [
                'date.unique' => 'วันที่นี้ถูกบันทึกไว้แล้ว กรุณาเลือกวันอื่น',
                'date.required' => 'กรุณาเลือกวันที่',
                'date.date' => 'รูปแบบวันที่ไม่ถูกต้อง',
                'date.before_or_equal' => 'วันที่ติดตามต้องไม่เกินวันที่ปัจจุบัน',
                'follo_no.required' => 'กรุณาเลือกการดำเนินงาน',
                'income_sufficiency.required' => 'กรุณาเลือกความเพียงพอของรายได้',
                'income_sufficiency.in' => 'ค่าความเพียงพอของรายได้ไม่ถูกต้อง',
                'housing_condition.in' => 'ค่าสภาพที่อยู่อาศัยไม่ถูกต้อง',
                'family_income.numeric' => 'รายได้ครอบครัวเฉลี่ย/เดือนต้องเป็นตัวเลข',
                'family_income.min' => 'รายได้ครอบครัวเฉลี่ย/เดือนต้องไม่น้อยกว่า 0',
                'pictures.*.image' => 'ไฟล์ต้องเป็นรูปภาพ',
                'pictures.*.mimes' => 'รูปภาพต้องเป็นไฟล์ jpeg, png, jpg หรือ webp',
                'pictures.*.max' => 'รูปภาพต้องมีขนาดไม่เกิน 10MB',
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('form', 'add-estimate');
            }

            $data = $validator->validated();
            $data['client_id'] = $client->id;

            unset($data['pictures']);

            if (($data['income_sufficiency'] ?? 'เพียงพอ') === 'เพียงพอ') {
                $data['income_reason'] = null;
            }

            $estimate = Estimate::create($data);

            $estimates = Estimate::where('client_id', $estimate->client_id)
                ->orderBy('date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $counter = 1;
            foreach ($estimates as $item) {
                $item->update(['count' => $counter]);
                $counter++;
            }

            if ($request->hasFile('pictures')) {
                foreach ($request->file('pictures') as $file) {
                    $path = $this->saveEstimateImage($file);
                    $estimate->pictures()->create(['path' => $path]);
                }
            }

              CaseActivity::where('client_id', $client->id)
                ->where('module', 'estimate')
                ->delete();

            CaseActivity::record([
                'client_id'   => $client->id,
                'module'      => 'estimate',
                'type'        => 'success',
                'title'       => 'บันทึกการประเมินครอบครัว',
                'description' => 'วันที่ประเมิน: ' . ($data['date'] ?? '-') .
                                ' / ครั้งที่ ' . ($estimate->count ?? '-') .
                                ' / ผลการประเมิน: ' . ($data['results'] ?? '-'),
                'occurred_at' => now(),
                'icon'        => 'bi-clipboard-data',
                'url'         => route('estimate.show', $client->id),
            ]);

            return redirect()->route('estimate.show', $estimate->client_id)
                ->with([
                    'message'    => 'บันทึกข้อมูลเรียบร้อย',
                    'alert-type' => 'success'
                ]);
        }

    public function EditEstimate($id)
    {
        $estimate = Estimate::where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->with('pictures')
            ->firstOrFail();

        return response()->json([
            'id' => $estimate->id,
            'client_id' => $estimate->client_id,
            'date' => \Carbon\Carbon::parse($estimate->date)->format('Y-m-d'),
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
            'pictures' => $estimate->pictures->map(fn($pic) => [
                'id'  => $pic->id,
                'url' => asset($pic->path),
            ]),
        ]);
    }

    public function UpdateEstimate(Request $request, $id)
    {
        $estimate = Estimate::where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->firstOrFail();

        $validated = $request->validate([
           'date' => [
            'required',
            'date',
            'before_or_equal:' . now('Asia/Bangkok')->toDateString(),

            Rule::unique('estimates')
                ->where(fn($q) => $q->where('client_id', $estimate->client_id))
                ->ignore($estimate->id),
        ],
            'follo_no' => 'required',
            'results' => 'nullable|string',
            'family_income' => 'nullable|numeric|min:0',
            'guardian_job' => 'nullable|string|max:255',
            'income_sufficiency' => 'required|in:เพียงพอ,ไม่เพียงพอ',
            'income_reason' => 'nullable|string',
            'debt' => 'nullable|string',
            'housing_condition' => 'nullable|in:ดี,พอใช้,ควรปรับปรุง',
            'teacher' => 'nullable|string|max:255',
            'remark' => 'nullable|string',

            // PATCH: รองรับรูปใหญ่จากมือถือ แล้วค่อยบีบอัดเอง
            'pictures'   => 'nullable|array',
            'pictures.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240',
            'remove_pictures' => 'nullable|array',
            'remove_pictures.*' => 'integer',
        ], [
            'date.unique' => 'วันที่นี้ถูกบันทึกไว้แล้ว กรุณาเลือกวันอื่น',
            'date.required' => 'กรุณาเลือกวันที่',
            'date.date' => 'รูปแบบวันที่ไม่ถูกต้อง',
            'date.before_or_equal' => 'วันที่ติดตามต้องไม่เกินวันที่ปัจจุบัน',
            'follo_no.required' => 'กรุณาเลือกการดำเนินงาน',
            'income_sufficiency.required' => 'กรุณาเลือกความเพียงพอของรายได้',
            'income_sufficiency.in' => 'ค่าความเพียงพอของรายได้ไม่ถูกต้อง',
            'housing_condition.in' => 'ค่าสภาพที่อยู่อาศัยไม่ถูกต้อง',
            'family_income.numeric' => 'รายได้ครอบครัวเฉลี่ย/เดือนต้องเป็นตัวเลข',
            'family_income.min' => 'รายได้ครอบครัวเฉลี่ย/เดือนต้องไม่น้อยกว่า 0',
            'pictures.*.image' => 'ไฟล์ต้องเป็นรูปภาพ',
            'pictures.*.mimes' => 'รูปภาพต้องเป็นไฟล์ jpeg, png, jpg หรือ webp',
            'pictures.*.max' => 'รูปภาพต้องมีขนาดไม่เกิน 10MB',
        ]);

        unset($validated['pictures'], $validated['remove_pictures']);

        if (($validated['income_sufficiency'] ?? 'เพียงพอ') === 'เพียงพอ') {
            $validated['income_reason'] = null;
        }

        $estimate->update($validated);

        if ($request->has('remove_pictures')) {
            foreach ($request->remove_pictures as $picId) {
                $pic = EstimatePicture::where('id', $picId)
                    ->where('estimate_id', $estimate->id)
                    ->first();

                if ($pic) {
                    $this->deleteEstimateImage($pic->path);
                    $pic->delete();
                }
            }
        }

        if ($request->hasFile('pictures')) {
            foreach ($request->file('pictures') as $file) {
                $path = $this->saveEstimateImage($file);
                $estimate->pictures()->create(['path' => $path]);
            }
        }

        $estimates = Estimate::where('client_id', $estimate->client_id)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $counter = 1;
        foreach ($estimates as $item) {
            $item->update(['count' => $counter]);
            $counter++;
        }

              CaseActivity::where('client_id', $estimate->client_id)
                ->where('module', 'estimate')
                ->delete();

            CaseActivity::record([
                'client_id'   => $estimate->client_id,
                'module'      => 'estimate',
                'type'        => 'success',
                'title'       => 'แก้ไขการประเมินครอบครัว',
                'description' => 'วันที่ประเมิน: ' . ($validated['date'] ?? '-') .
                                ' / ครั้งที่ ' . ($estimate->count ?? '-') .
                                ' / ผลการประเมิน: ' . ($validated['results'] ?? '-'),
                'occurred_at' => now(),
                'icon'        => 'bi-clipboard-data',
                'url'         => route('estimate.show', $estimate->client_id),
            ]);

        return redirect()->route('estimate.show', $estimate->client_id)
            ->with([
                'message' => 'แก้ไขข้อมูลเรียบร้อย',
                'alert-type' => 'success'
            ]);
    }

  public function DeleteEstimate($id)
{
    $estimate = Estimate::where('id', $id)
        ->whereHas('client', function ($q) {
            $q->forUser(auth()->user());
        })
        ->with('pictures')
        ->firstOrFail();

    $client_id = $estimate->client_id;

    // ✅ ลบ activity ของ estimate ออกจาก feed ก่อน
    CaseActivity::where('client_id', $client_id)
        ->where('module', 'estimate')
        ->delete();

    foreach ($estimate->pictures as $pic) {
        $this->deleteEstimateImage($pic->path);
        $pic->delete();
    }

    $estimate->delete();

    $estimates = Estimate::where('client_id', $client_id)
        ->orderBy('date', 'asc')
        ->orderBy('id', 'asc')
        ->get();

    $counter = 1;
    foreach ($estimates as $item) {
        $item->update(['count' => $counter]);
        $counter++;
    }

    return redirect()->route('estimate.show', $client_id)
        ->with([
            'message' => 'ลบข้อมูลเรียบร้อย',
            'alert-type' => 'success'
        ]);
}

    public function CheckDuplicate(Request $request)
    {
        $client = Client::forUser(auth()->user())
            ->where('id', $request->client_id)
            ->firstOrFail();

        $exists = Estimate::where('client_id', $client->id)
            ->where('date', $request->date)
            ->where('id', '!=', $request->id)
            ->exists();

        return response()->json(['duplicate' => $exists]);
    }

    public function ReportEstimate($id)
    {
        $estimate = Estimate::where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->with(['client', 'pictures'])
            ->firstOrFail();

        return view('frontend.client.estimate.estimate_report', compact('estimate'));
    }
}