<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Client;
use App\Models\Subject;
use App\Models\Education;
use App\Models\Institution;
use App\Models\Semester;
use Illuminate\Http\Request;
use App\Models\EducationRecord;
use App\Http\Controllers\Controller;
use App\Models\CaseActivity;

class EducationRecordController extends Controller
{
    public function EducationRecordAdd($client_id)
{
    $client = Client::forUser(auth()->user())->findOrFail($client_id); // ✅ [แก้ไข]
    $subjects = Subject::all();

    // ✅ เรียง semester_name ตามปีและเทอมจริง ๆ  
        $semesters = Semester::orderByRaw("
        CAST(SUBSTRING_INDEX(semester_name, '/', -1) AS UNSIGNED) DESC,
        CAST(SUBSTRING_INDEX(semester_name, '/', 1) AS UNSIGNED) DESC
    ")->get();

    $educations = Education::all();

    return view('frontend.client.education_record.education_record_create',
        compact('client','subjects', 'semesters', 'educations'));
}

   public function EducationRecordStore(Request $request)
        {
            $validated = $request->validate([
                'client_id'    => 'required|exists:clients,id',
                'education_id' => 'required',
                'semester_id'  => 'required|exists:semesters,id',
                'school_name'  => 'required|string',

               'record_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],

                'grade_average'=> 'nullable|numeric',
                'subjects'     => 'nullable|array',
                'subjects.*.subject_id' => 'nullable|exists:subjects,id',
                'subjects.*.score'      => 'nullable|numeric|min:0|max:100',
                'subjects.*.grade' => 'nullable',
            ], [
                'education_id.required' => 'กรุณาเลือกระดับการศึกษา',
                'semester_id.required'  => 'กรุณาเลือกภาคเรียน',
                'semester_id.exists'    => 'ภาคเรียนที่เลือกไม่ถูกต้อง',
                'school_name.required'  => 'กรุณากรอกชื่อสถานศึกษา',
                'school_name.string'    => 'ชื่อสถานศึกษาต้องเป็นข้อความ',
               'record_date.required'   => 'กรุณาเลือกวันที่บันทึก',
                'record_date.date'      => 'วันที่บันทึกต้องอยู่ในรูปแบบวันที่',
                'record_date.before_or_equal' => 'วันที่บันทึกต้องไม่เกินวันปัจจุบัน',
            ]);

            $client = Client::forUser(auth()->user())
                ->where('id', $validated['client_id'])
                ->firstOrFail();

            // ✅ กันบันทึกซ้ำ
            $existingRecord = EducationRecord::where('client_id', $client->id)
                ->where('education_id', $validated['education_id'])
                ->where('semester_id', $validated['semester_id'])
                ->first();

            if ($existingRecord) {
                return back()->with('error', 'มีการบันทึกผลการเรียนในภาคเรียนนี้แล้ว')->withInput();
            }

            $institution = Institution::firstOrCreate([
                'institution_name' => $validated['school_name']
            ]);

            // ✅ กันเลือกวิชาซ้ำ
            if (!empty($validated['subjects'])) {
                $subjectIds = array_filter(array_column($validated['subjects'], 'subject_id'));

                if (count($subjectIds) !== count(array_unique($subjectIds))) {
                    return back()->with('error', 'ไม่สามารถเลือกวิชาเดิมซ้ำในฟอร์มเดียวกันได้')->withInput();
                }
            }

            $record = EducationRecord::create([
                'client_id'      => $client->id,
                'education_id'   => $validated['education_id'],
                'semester_id'    => $validated['semester_id'],
                'school_name'    => $validated['school_name'],
                'institution_id' => $institution->id,
                'record_date'    => $validated['record_date'],
                'grade_average'  => $validated['grade_average'] ?? null,
            ]);

            if (!empty($validated['subjects'])) {
                foreach ($validated['subjects'] as $data) {
                    if (!empty($data['subject_id'])) {
                        $record->subjects()->attach($data['subject_id'], [
                            'score' => $data['score'] ?? null,
                            'grade' => $this->calculateGradeFromScore($data['score'] ?? null),
                        ]);
                    }
                }
            }

            CaseActivity::record([
                'client_id'   => $client->id,
                'module'      => 'education_record',
                'type'        => 'success',
                'title'       => 'บันทึกผลการเรียน',
                'description' => 'บันทึกผลการเรียน สถานศึกษา: ' . ($validated['school_name'] ?? '-') .
                                ' | เกรดเฉลี่ย: ' . ($validated['grade_average'] ?? '-'),
                'occurred_at' => $validated['record_date'] ?? now(),
                'icon'        => 'bi-mortarboard',
                'url'         => route('education_record_show', ['client_id' => $client->id]),
            ]);

            return redirect()->route('education_record_show', ['client_id' => $record->client_id])
                ->with('success', 'บันทึกผลการเรียนเรียบร้อยแล้ว');
        }

   public function EducationRecordEdit($id)
{
    $record = EducationRecord::with('subjects')
        ->whereHas('client', function ($q) {
            $q->forUser(auth()->user());
        })
        ->findOrFail($id); // ✅ [แก้ไข]

    $client = $record->client;
    $subjects = Subject::all();
    $educations = Education::all();

    // ✅ เรียง semester_name ตามปีและเทอมจริง ๆ
    $semesters = Semester::orderByRaw("
        CAST(SUBSTRING_INDEX(semester_name, '/', -1) AS UNSIGNED) DESC,
        CAST(SUBSTRING_INDEX(semester_name, '/', 1) AS UNSIGNED) DESC
    ")->get();

    return view('frontend.client.education_record.education_record_edit',
        compact('record','client','subjects','educations','semesters'));
}

    public function EducationRecordUpdate(Request $request, $id)
{
    $validated = $request->validate([
        'client_id'    => 'required|exists:clients,id',
        'education_id' => 'required',
        'semester_id'  => 'required|exists:semesters,id',
        'school_name'  => 'required|string',

        'record_date' => [
            'required',
            'date',
            'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
        ],

        'grade_average' => 'nullable|numeric|regex:/^\d{1,3}(\.\d{1,2})?$/',
        'subjects'      => 'nullable|array',

        'subjects.*.subject_id' => 'nullable|exists:subjects,id',
        'subjects.*.score'      => 'nullable|numeric|min:0|max:100',

        // ✅ รับได้ แต่ไม่ใช้ค่าจากฟอร์ม เพื่อความปลอดภัย
        'subjects.*.grade'      => 'nullable',
    ], [
        'education_id.required' => 'กรุณาเลือกระดับการศึกษา',

        'semester_id.required'  => 'กรุณาเลือกภาคเรียน',
        'semester_id.exists'    => 'ภาคเรียนที่เลือกไม่ถูกต้อง',

        'school_name.required'  => 'กรุณากรอกชื่อสถานศึกษา',
        'school_name.string'    => 'ชื่อสถานศึกษาต้องเป็นข้อความ',

        'record_date.required'  => 'กรุณาเลือกวันที่บันทึก',
        'record_date.date'      => 'วันที่บันทึกต้องอยู่ในรูปแบบวันที่',
        'record_date.before_or_equal' => 'วันที่บันทึกต้องไม่เกินวันปัจจุบัน',
    ]);

    $record = EducationRecord::where('id', $id)
        ->whereHas('client', function ($q) {
            $q->forUser(auth()->user());
        })
        ->firstOrFail();

    $client = Client::forUser(auth()->user())
        ->where('id', $validated['client_id'])
        ->firstOrFail();

    // ✅ กันแก้ข้อมูลให้ไปซ้ำกับระดับการศึกษา + ภาคเรียนเดิมของรายการอื่น
    $existingRecord = EducationRecord::where('client_id', $client->id)
        ->where('education_id', $validated['education_id'])
        ->where('semester_id', $validated['semester_id'])
        ->where('id', '!=', $record->id)
        ->first();

    if ($existingRecord) {
        return back()
            ->with('error', 'มีการบันทึกผลการเรียนในภาคเรียนนี้แล้ว')
            ->withInput();
    }

    // ✅ กันเลือกวิชาซ้ำในฟอร์มเดียวกัน โดยไม่นับแถวว่าง
    if (!empty($validated['subjects'])) {
        $subjectIds = array_filter(array_column($validated['subjects'], 'subject_id'));

        if (count($subjectIds) !== count(array_unique($subjectIds))) {
            return back()
                ->with('error', 'ไม่สามารถเลือกวิชาเดิมซ้ำในฟอร์มเดียวกันได้')
                ->withInput();
        }
    }

    // ✅ อัปเดต/สร้างสถานศึกษาให้ตรงกับ school_name ล่าสุด
    $institution = Institution::firstOrCreate([
        'institution_name' => $validated['school_name'],
    ]);

    $record->update([
        'client_id'      => $client->id,
        'education_id'   => $validated['education_id'],
        'semester_id'    => $validated['semester_id'],
        'school_name'    => $validated['school_name'],
        'institution_id' => $institution->id,
        'record_date'    => $validated['record_date'],

       'grade_average' => isset($validated['grade_average']) && $validated['grade_average'] !== null
        ? number_format($validated['grade_average'], 2, '.', '')
        : null,
        ]);

    $syncData = [];

    if (!empty($validated['subjects'])) {
        foreach ($validated['subjects'] as $data) {
            if (!empty($data['subject_id'])) {
                $syncData[$data['subject_id']] = [
                    'score' => $data['score'] ?? null,

                    // ✅ คำนวณจากคะแนนฝั่ง Server เท่านั้น
                    'grade' => $this->calculateGradeFromScore($data['score'] ?? null),
                ];
            }
        }
    }

    $record->subjects()->sync($syncData);

    return redirect()
        ->route('education_record_show', ['client_id' => $record->client_id])
        ->with('success', 'แก้ไขผลการเรียนเรียบร้อยแล้ว');
}

    public function EducationRecordShow($client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id); // ✅ [แก้ไข]

    $educationRecords = EducationRecord::with('subjects','education','semester')
    ->leftJoin('semesters', 'education_records.semester_id', '=', 'semesters.id')
    ->select('education_records.*', 'semesters.semester_name as semester_label')
    ->where('education_records.client_id', $client_id)
    ->orderBy('education_records.record_date', 'desc')
    ->get();



        if ($educationRecords->isEmpty()) {
            return redirect()->route('education_record_add', ['client_id' => $client_id])
                             ->with('info', 'ยังไม่มีข้อมูลผลการเรียน กรุณาบันทึกข้อมูลก่อน');
        }

        return view('frontend.client.education_record.education_record_show',
            compact('client','educationRecords'));
    }

     // 📌 ลบผลการเรียน
    public function EducationRecordDelete($id)
    {
        $record = EducationRecord::where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->firstOrFail(); // ✅ [แก้ไข]

        $client_id = $record->client_id;

        // ลบความสัมพันธ์กับ subjects ก่อน
        $record->subjects()->detach();

        $record->delete();

        return redirect()->route('education_record_show', ['client_id' => $client_id])
                         ->with('success', 'ลบข้อมูลผลการเรียนเรียบร้อยแล้ว');
    }


    public function EducationRecordReport($client_id)
{
    $client = Client::forUser(auth()->user())->findOrFail($client_id); // ✅ จำกัดสิทธิ์เหมือนเดิม

    $educationRecords = EducationRecord::with(['subjects', 'education', 'semester', 'institution'])
        ->where('client_id', $client_id)
        ->orderBy('record_date', 'desc')
        ->get();

    return view('frontend.client.education_record.education_record_report', compact(
        'client',
        'educationRecords'
    ));
}

public function EducationRecordReportById($id)
{
    $record = EducationRecord::with(['subjects', 'education', 'semester', 'institution'])
        ->leftJoin('semesters', 'education_records.semester_id', '=', 'semesters.id')
        ->select('education_records.*', 'semesters.semester_name as semester_label')
        ->where('education_records.id', $id)
        ->whereHas('client', function ($q) {
            $q->forUser(auth()->user());
        })
        ->firstOrFail();

    $client = Client::forUser(auth()->user())
        ->findOrFail($record->client_id);

    $educationRecords = collect([$record]);

    return view('frontend.client.education_record.education_record_report', compact(
        'client',
        'educationRecords'
    ));
}

    private function calculateGradeFromScore($score): ?string
    {
        if ($score === null || $score === '') {
            return null;
        }

        $score = (float) $score;

        if ($score >= 80) return '4.00';
        if ($score >= 75) return '3.50';
        if ($score >= 70) return '3.00';
        if ($score >= 65) return '2.50';
        if ($score >= 60) return '2.00';
        if ($score >= 55) return '1.50';
        if ($score >= 50) return '1.00';

        return '0.00';
    }
}