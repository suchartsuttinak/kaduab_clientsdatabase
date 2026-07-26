<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Absent\StoreAbsentRequest;
use App\Http\Requests\Client\Absent\UpdateAbsentRequest;
use App\Models\Absent;
use App\Models\Client;
use App\Models\EducationRecord;
use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\CaseActivity;

class AbsentController extends Controller
{
    public function AbsentAdd(Request $request, $client_id): View|RedirectResponse
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $educationRecord = $this->getLatestEducationRecord($client->id);

        if (!$educationRecord) {
            return redirect()
                ->route('education_record.add', $client->id)
                ->with([
                    'message' => 'กรุณาเพิ่มข้อมูลการศึกษาเด็กก่อนบันทึกการขาดเรียน',
                    'alert-type' => 'warning',
                ]);
        }

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : null;

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : null;

        $absentsQuery = Absent::with(['educationRecord.education', 'educationRecord.semester'])
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->where('client_id', $client->id);

        if ($startDate) {
            $absentsQuery->whereDate('absent_date', '>=', $startDate->toDateString());
        }

        if ($endDate) {
            $absentsQuery->whereDate('absent_date', '<=', $endDate->toDateString());
        }

        $absents = $absentsQuery
            ->orderByDesc('absent_date')
            ->orderByDesc('id')
            ->get();

        $schoolName = $educationRecord?->school_name ?? 'ไม่พบข้อมูล';
        $educationName = optional($educationRecord?->education)->education_name ?? 'ไม่พบข้อมูล';
        $semesterName = $this->getSemesterNameByEducationRecord($educationRecord);

        return view('frontend.client.absent.absent_create', compact(
            'client',
            'educationRecord',
            'client_id',
            'absents',
            'schoolName',
            'educationName',
            'semesterName'
        ));
    }

  public function AbsentStore(StoreAbsentRequest $request): RedirectResponse
{
    $validated = $request->validated();

    $today = now('Asia/Bangkok')->toDateString();

    if (!empty($validated['absent_date']) && $validated['absent_date'] > $today) {
        return redirect()
            ->back()
            ->withErrors([
                'absent_date' => 'วันที่ขาดเรียนต้องไม่เกินวันปัจจุบัน',
            ])
            ->withInput()
            ->with([
                'message' => 'วันที่ขาดเรียนต้องไม่เกินวันปัจจุบัน',
                'alert-type' => 'error',
                'absent_modal' => 'create',
            ]);
    }

    if (!empty($validated['record_date']) && $validated['record_date'] > $today) {
        return redirect()
            ->back()
            ->withErrors([
                'record_date' => 'วันที่บันทึกต้องไม่เกินวันปัจจุบัน',
            ])
            ->withInput()
            ->with([
                'message' => 'วันที่บันทึกต้องไม่เกินวันปัจจุบัน',
                'alert-type' => 'error',
                'absent_modal' => 'create',
            ]);
    }

    $client = Client::forUser(auth()->user())
        ->where('id', $validated['client_id'])
        ->firstOrFail();

    if (empty($validated['education_record_id'])) {
        $educationRecord = $this->getLatestEducationRecord($client->id);
        $validated['education_record_id'] = $educationRecord?->id;
    }

    $validated['client_id'] = $client->id;

    $duplicate = Absent::where('client_id', $client->id)
        ->whereDate('absent_date', $validated['absent_date'])
        ->exists();

    if ($duplicate) {
        return redirect()
            ->back()
            ->withErrors([
                'absent_date' => 'ไม่สามารถบันทึกซ้ำได้ เด็กคนนี้มีข้อมูลขาดเรียนในวันที่นี้แล้ว',
            ])
            ->withInput()
            ->with([
                'message' => 'ไม่สามารถบันทึกซ้ำได้ เด็กคนนี้มีข้อมูลขาดเรียนในวันที่นี้แล้ว',
                'alert-type' => 'error',
                'absent_modal' => 'create',
            ]);
    }

    Absent::create($validated);

    CaseActivity::where('client_id', $client->id)
        ->where('module', 'absent')
        ->delete();

    CaseActivity::record([
        'client_id'   => $client->id,
        'module'      => 'absent',
        'type'        => 'warning',
        'title'       => 'บันทึกการขาดเรียน',
        'description' => 'วันที่ขาดเรียน: ' . ($validated['absent_date'] ?? '-') .
                        ' | สาเหตุ: ' . ($validated['cause'] ?? '-') .
                        ' | การดำเนินการ: ' . ($validated['operation'] ?? '-'),
        'occurred_at' => $validated['absent_date'] ?? now('Asia/Bangkok'),
        'icon'        => 'bi-calendar-x',
        'url'         => route('absent.add', $client->id),
    ]);

    return redirect()
        ->route('absent.add', $client->id)
        ->with([
            'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
            'alert-type' => 'success',
        ]);
}

    public function AbsentEdit($id): JsonResponse
    {
        try {
            $absent = Absent::with(['educationRecord.education', 'educationRecord.semester'])
                ->whereHas('client', function ($q) {
                    $q->forUser(auth()->user());
                })
                ->findOrFail($id);

            $educationRecord = $absent->educationRecord;
            $semesterName = $this->getSemesterNameByEducationRecord($educationRecord);

            return response()->json([
                'success' => true,
                'message' => 'โหลดข้อมูลเรียบร้อยแล้ว',
                'data' => [
                    'id' => $absent->id,
                    'client_id' => $absent->client_id,
                    'education_record_id' => $absent->education_record_id,
                    'absent_date' => $absent->absent_date
                        ? Carbon::parse($absent->absent_date)->format('Y-m-d')
                        : null,
                    'record_date' => $absent->record_date
                        ? Carbon::parse($absent->record_date)->format('Y-m-d')
                        : null,
                    'cause' => $absent->cause,
                    'operation' => $absent->operation,
                    'remark' => $absent->remark,
                    'teacher' => $absent->teacher,
                    'school_name' => $educationRecord?->school_name ?? 'ไม่พบข้อมูล',
                    'education_name' => optional($educationRecord?->education)->education_name ?? 'ไม่พบข้อมูล',
                    'semester_name' => $semesterName,
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลการขาดเรียนที่ต้องการแก้ไข',
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function AbsentUpdate(UpdateAbsentRequest $request, $id): JsonResponse|RedirectResponse
    {
        $absent = Absent::where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->firstOrFail();

        $validated = $request->validated();

        $validated['education_record_id'] = $absent->education_record_id;
        $validated['client_id'] = $absent->client_id;

        // =========================
        // PATCH: กันแก้ไขวันที่ไปซ้ำกับรายการอื่นของเด็กคนเดิม
        // =========================
        $duplicate = Absent::where('client_id', $absent->client_id)
            ->whereDate('absent_date', $validated['absent_date'])
            ->where('id', '!=', $absent->id)
            ->exists();

        if ($duplicate) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่สามารถแก้ไขได้ เด็กคนนี้มีข้อมูลขาดเรียนในวันที่นี้แล้ว',
                    'errors' => [
                        'absent_date' => ['ไม่สามารถแก้ไขได้ เด็กคนนี้มีข้อมูลขาดเรียนในวันที่นี้แล้ว'],
                    ],
                ], 422);
            }

            return redirect()
                ->back()
                ->withErrors([
                    'absent_date' => 'ไม่สามารถแก้ไขได้ เด็กคนนี้มีข้อมูลขาดเรียนในวันที่นี้แล้ว',
                ])
                ->withInput()
                ->with([
                    'message' => 'ไม่สามารถแก้ไขได้ เด็กคนนี้มีข้อมูลขาดเรียนในวันที่นี้แล้ว',
                    'alert-type' => 'error',
                    'edit_mode' => true,
                    'edit_id' => $absent->id,
                ]);
        }

        $absent->update($validated);

            CaseActivity::where('client_id', $absent->client_id)
            ->where('module', 'absent')
            ->delete();

            CaseActivity::record([
            'client_id'   => $absent->client_id,
            'module'      => 'absent',
            'type'        => 'warning',
            'title'       => 'แก้ไขข้อมูลการขาดเรียน',
            'description' => 'วันที่ขาดเรียน: ' . ($validated['absent_date'] ?? '-') .
                            ' | สาเหตุ: ' . ($validated['cause'] ?? '-') .
                            ' | การดำเนินการ: ' . ($validated['operation'] ?? '-'),
           'occurred_at' => $validated['absent_date'] ?? now('Asia/Bangkok'),
            'icon'        => 'bi-calendar-x',
            'url'         => route('absent.add', $absent->client_id),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว',
                'data' => $absent->fresh(['educationRecord.education', 'educationRecord.semester'])
            ]);
        }

        return redirect()
            ->route('absent.add', $absent->client_id)
            ->with([
                'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว',
                'alert-type' => 'success'
            ]);
    }

    public function AbsentDelete($id): RedirectResponse
    {
        $absent = Absent::where('id', $id)
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->firstOrFail();

       $clientId = $absent->client_id;

        CaseActivity::where('client_id', $clientId)
            ->where('module', 'absent')
            ->delete();

        $absent->delete();

        return redirect()
            ->route('absent.add', $clientId)
            ->with([
                'message' => 'ลบข้อมูลการขาดเรียนเรียบร้อยแล้ว',
                'alert-type' => 'success'
            ]);
    }

    public function AbsentReport($absent_id): View
    {
        $absent = Absent::with(['educationRecord.education', 'educationRecord.semester'])
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->findOrFail($absent_id);

        $client = Client::forUser(auth()->user())->findOrFail($absent->client_id);
        $educationRecord = $absent->educationRecord;

        $age = $client->birth_date
            ? Carbon::parse($client->birth_date)->age
            : 'ไม่พบข้อมูล';

        return view('frontend.client.absent.absent_report', [
            'client' => $client,
            'educationRecord' => $educationRecord,
            'absent' => $absent,
            'school_name' => $educationRecord?->school_name ?? 'ไม่พบข้อมูล',
            'education_name' => optional($educationRecord?->education)->education_name ?? 'ไม่พบข้อมูล',
            'term' => $this->getSemesterNameByEducationRecord($educationRecord),
            'age' => $age,
        ]);
    }

    public function AbsentReportRange(Request $request, $client_id): View
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);
        $educationRecord = $this->getLatestEducationRecord($client->id);

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : null;

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : null;

        $query = Absent::with(['educationRecord.education', 'educationRecord.semester'])
            ->whereHas('client', function ($q) {
                $q->forUser(auth()->user());
            })
            ->where('client_id', $client->id);

        if ($startDate) {
            $query->whereDate('absent_date', '>=', $startDate->toDateString());
        }

        if ($endDate) {
            $query->whereDate('absent_date', '<=', $endDate->toDateString());
        }

        $absents = $query
            ->orderByDesc('absent_date')
            ->orderByDesc('id')
            ->get();

        $age = $client->birth_date
            ? Carbon::parse($client->birth_date)->age
            : 'ไม่พบข้อมูล';

        return view('frontend.client.absent.absent_report_range', [
            'client' => $client,
            'educationRecord' => $educationRecord,
            'absents' => $absents,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'age' => $age,
            'school_name' => $educationRecord?->school_name ?? 'ไม่พบข้อมูล',
            'education_name' => optional($educationRecord?->education)->education_name ?? 'ไม่พบข้อมูล',
            'term' => $this->getSemesterNameByEducationRecord($educationRecord),
        ]);
    }

    private function getLatestEducationRecord($clientId)
    {
        return EducationRecord::with(['education', 'semester'])
            ->where('client_id', $clientId)
            ->join('semesters', 'education_records.semester_id', '=', 'semesters.id')
            ->orderByRaw("
                CAST(SUBSTRING_INDEX(semesters.semester_name, '/', -1) AS UNSIGNED) DESC,
                CAST(SUBSTRING_INDEX(semesters.semester_name, '/', 1) AS UNSIGNED) DESC
            ")
            ->select('education_records.*')
            ->first();
    }

    private function getSemesterNameByEducationRecord($educationRecord): string
    {
        if (!$educationRecord || !$educationRecord->semester_id) {
            return 'ไม่พบข้อมูล';
        }

        return Semester::where('id', $educationRecord->semester_id)
            ->value('semester_name') ?? 'ไม่พบข้อมูล';
    }
}