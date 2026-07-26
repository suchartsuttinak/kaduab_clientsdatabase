<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\SchoolFollowup\StoreSchoolFollowupRequest;
use App\Http\Requests\Client\SchoolFollowup\UpdateSchoolFollowupRequest;
use App\Models\Client;
use App\Models\EducationRecord;
use App\Models\SchoolFollowup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\CaseActivity;

class SchoolFollowupController extends Controller
{
    public function SchoolFollowupAdd(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $educationRecord = $this->getLatestEducationRecord($client->id);

        if (!$educationRecord) {
            return redirect()
                ->route('education_record_add', $client->id)
                ->with('info', 'ต้องบันทึกผลการเรียนก่อนเข้าเมนูติดตามผลการเรียน');
        }

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : null;

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : null;

        $query = SchoolFollowup::with([
                'educationRecord.education',
                'educationRecord.semester'
            ])
            ->where('client_id', $client->id);

        if ($startDate) {
            $query->whereDate('follow_date', '>=', $startDate->toDateString());
        }

        if ($endDate) {
            $query->whereDate('follow_date', '<=', $endDate->toDateString());
        }

        $followups = $query
            ->orderByDesc('follow_date')
            ->orderByDesc('id')
            ->get();

        $client_id = $client->id;

        return view('frontend.client.school_followup.school_followup_create', compact(
            'client',
            'educationRecord',
            'client_id',
            'followups'
        ));
    }
   public function SchoolFollowupStore(StoreSchoolFollowupRequest $request)
    {
        $validated = $request->validated();

        $client = Client::forUser(auth()->user())
            ->where('id', $validated['client_id'])
            ->firstOrFail();

        if (empty($validated['education_record_id'])) {
            $educationRecord = $this->getLatestEducationRecord($client->id);
            $validated['education_record_id'] = $educationRecord?->id;
        }

        $validated['client_id'] = $client->id;

        $schoolFollowup = SchoolFollowup::create($validated);

        CaseActivity::where('client_id', $client->id)
            ->where('module', 'school_followup')
            ->delete();

        CaseActivity::record([
            'client_id'   => $client->id,
            'module'      => 'school_followup',
            'type'        => 'success',
            'title'       => 'บันทึกการติดตามการศึกษา',
            'description' => 'วันที่ติดตาม: ' . ($validated['follow_date'] ?? '-') .
                            ' | ประเภท: ' . ($validated['follow_type'] ?? '-') .
                            ' | ผู้ติดตาม: ' . ($validated['teacher_name'] ?? '-'),
            'occurred_at' => $validated['follow_date'] ?? now('Asia/Bangkok'),
            'icon'        => 'bi-journal-check',
            'url'         => route('school_followup_add', $client->id),
        ]);

        return redirect()
            ->route('school_followup_add', $client->id)
            ->with([
                'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success'
            ]);
    }
    public function SchoolFollowupEdit($id)
    {
        try {
            $followup = SchoolFollowup::with([
                    'educationRecord.education',
                    'educationRecord.semester'
                ])
                ->whereHas('client', function ($q) {
                    $q->forUser(auth()->user());
                })
                ->findOrFail($id);

            $educationRecord = $followup->educationRecord;

            // ✅ ดึงภาคเรียนแบบชัวร์จาก semester_id โดยตรง
            $semesterName = 'ไม่พบข้อมูล';

            if ($educationRecord && $educationRecord->semester_id) {
                $semesterName = \App\Models\Semester::where('id', $educationRecord->semester_id)
                    ->value('semester_name') ?? 'ไม่พบข้อมูล';
            }

            $academicYear = $this->extractAcademicYear($semesterName);

            return response()->json([
                'success' => true,
                'message' => 'โหลดข้อมูลเรียบร้อยแล้ว',
                'data' => [
                    'id' => $followup->id,
                    'client_id' => $followup->client_id,
                    'education_record_id' => $followup->education_record_id,
                    'follow_date' => $followup->follow_date
                        ? Carbon::parse($followup->follow_date)->format('Y-m-d')
                        : null,
                    'teacher_name' => $followup->teacher_name,
                    'tel' => $followup->tel,
                    'follow_type' => $followup->follow_type,
                    'result' => $followup->result,
                    'remark' => $followup->remark,
                    'contact_name' => $followup->contact_name,
                    'school_name' => $educationRecord?->school_name ?? 'ไม่พบข้อมูล',
                    'education_name' => data_get($educationRecord, 'education.education_name', 'ไม่พบข้อมูล'),
                    'semester_name' => $semesterName,
                    'academic_year' => $academicYear,
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลการติดตามที่ต้องการแก้ไข',
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function SchoolFollowupUpdate(UpdateSchoolFollowupRequest $request, $id)
    {
        $followup = SchoolFollowup::where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->firstOrFail();

        $validated = $request->validated();

        $validated['education_record_id'] = $followup->education_record_id;
        $validated['client_id'] = $followup->client_id;

        $followup->update($validated);

        CaseActivity::where('client_id', $followup->client_id)
            ->where('module', 'school_followup')
            ->delete();

        CaseActivity::record([
            'client_id'   => $followup->client_id,
            'module'      => 'school_followup',
            'type'        => 'success',
            'title'       => 'แก้ไขการติดตามการศึกษา',
            'description' => 'วันที่ติดตาม: ' . ($validated['follow_date'] ?? '-') .
                            ' | ประเภท: ' . ($validated['follow_type'] ?? '-') .
                            ' | ผู้ติดตาม: ' . ($validated['teacher_name'] ?? '-'),
            'occurred_at' => $validated['follow_date'] ?? now('Asia/Bangkok'),
            'icon'        => 'bi-journal-check',
            'url'         => route('school_followup_add', $followup->client_id),
        ]);

        if ($request->ajax()) {
            $freshFollowup = $followup->fresh([
                'educationRecord.education',
                'educationRecord.semester'
            ]);

            $semesterName = data_get($freshFollowup, 'educationRecord.semester.semester_name', 'ไม่พบข้อมูล');
            $academicYear = $this->extractAcademicYear($semesterName);

            return response()->json([
                'success' => true,
                'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว',
                'data' => [
                    'id' => $freshFollowup->id,
                    'client_id' => $freshFollowup->client_id,
                    'education_record_id' => $freshFollowup->education_record_id,
                    'follow_date' => $freshFollowup->follow_date
                        ? Carbon::parse($freshFollowup->follow_date)->format('Y-m-d')
                        : null,
                    'teacher_name' => $freshFollowup->teacher_name,
                    'tel' => $freshFollowup->tel,
                    'follow_type' => $freshFollowup->follow_type,
                    'result' => $freshFollowup->result,
                    'remark' => $freshFollowup->remark,
                    'contact_name' => $freshFollowup->contact_name,
                    'school_name' => $freshFollowup->educationRecord?->school_name ?? 'ไม่พบข้อมูล',
                    'education_name' => data_get($freshFollowup, 'educationRecord.education.education_name', 'ไม่พบข้อมูล'),
                    'semester_name' => $semesterName,
                    'academic_year' => $academicYear,
                ]
            ]);
        }

        return redirect()
            ->route('school_followup_add', $followup->client_id)
            ->with([
                'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success'
            ]);
    }

    public function SchoolFollowupDelete($id)
    {
        $followup = SchoolFollowup::where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->firstOrFail();

       $clientId = $followup->client_id;

        CaseActivity::where('client_id', $clientId)
            ->where('module', 'school_followup')
            ->delete();

        $followup->delete();

        return redirect()
            ->route('school_followup_add', $clientId)
            ->with([
                'message' => 'ลบข้อมูลการติดตามเรียบร้อยแล้ว',
                'alert-type' => 'success'
            ]);
    }

    public function SchoolFollowupReport($followup_id)
    {
        $followup = SchoolFollowup::with([
                'educationRecord.education',
                'educationRecord.semester'
            ])
            ->leftJoin('education_records', 'school_followups.education_record_id', '=', 'education_records.id')
            ->leftJoin('semesters', 'education_records.semester_id', '=', 'semesters.id')
            ->select('school_followups.*', 'semesters.semester_name as semester_label')
            ->where('school_followups.id', $followup_id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->firstOrFail();

        $client = Client::forUser(auth()->user())->findOrFail($followup->client_id);
        $educationRecord = $followup->educationRecord;

        $age = $client->birth_date
            ? Carbon::parse($client->birth_date)->age
            : 'ไม่พบข้อมูล';

        $term = $followup->semester_label
            ?? data_get($educationRecord, 'semester.semester_name', 'ไม่พบข้อมูล');

        return view('frontend.client.school_followup.school_followup_report', [
            'client' => $client,
            'educationRecord' => $educationRecord,
            'followup' => $followup,
            'school_name' => $educationRecord?->school_name ?? 'ไม่พบข้อมูล',
            'education_name' => data_get($educationRecord, 'education.education_name', 'ไม่พบข้อมูล'),
            'term' => $term,
            'age' => $age,
        ]);
    }

    private function getLatestEducationRecord($clientId)
    {
        return EducationRecord::with(['education', 'semester'])
            ->where('education_records.client_id', $clientId)
            ->leftJoin('semesters', 'education_records.semester_id', '=', 'semesters.id')
            ->select('education_records.*', 'semesters.semester_name as semester_label')
            ->orderByRaw("
                CAST(SUBSTRING_INDEX(semesters.semester_name, '/', -1) AS UNSIGNED) DESC,
                CAST(SUBSTRING_INDEX(semesters.semester_name, '/', 1) AS UNSIGNED) DESC
            ")
            ->first();
    }

    private function extractAcademicYear($semesterName)
    {
        if (empty($semesterName) || $semesterName === 'ไม่พบข้อมูล') {
            return 'ไม่พบข้อมูล';
        }

        if (str_contains($semesterName, '/')) {
            return explode('/', $semesterName)[1] ?? 'ไม่พบข้อมูล';
        }

        return 'ไม่พบข้อมูล';
    }

    public function SchoolFollowupReportRange(Request $request, $client_id)
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $educationRecord = $this->getLatestEducationRecord($client_id);

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : null;

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : null;

        $query = SchoolFollowup::with([
                'educationRecord.education',
                'educationRecord.semester'
            ])
            ->where('client_id', $client_id);

        if ($startDate) {
            $query->whereDate('follow_date', '>=', $startDate->toDateString());
        }

        if ($endDate) {
            $query->whereDate('follow_date', '<=', $endDate->toDateString());
        }

        $followups = $query
            ->orderByDesc('follow_date')
            ->orderByDesc('id')
            ->get();

        $age = $client->birth_date
            ? Carbon::parse($client->birth_date)->age
            : 'ไม่พบข้อมูล';

        $term = $educationRecord?->semester_label
            ?? data_get($educationRecord, 'semester.semester_name', 'ไม่พบข้อมูล');

        return view('frontend.client.school_followup.school_followup_report_range', [
            'client' => $client,
            'educationRecord' => $educationRecord,
            'followups' => $followups,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'age' => $age,
            'school_name' => $educationRecord?->school_name ?? '-',
            'education_name' => data_get($educationRecord, 'education.education_name', '-'),
            'term' => $term,
        ]);
    }
}

  