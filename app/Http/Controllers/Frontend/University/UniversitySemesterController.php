<?php

namespace App\Http\Controllers\Frontend\University;

use App\Models\EducationRecord;
use App\Models\SchoolFollowup;
use App\Models\UniversitySemesterRecord;
use App\Support\UniversityCurrentEducationSource;
use App\Support\UniversityCreditCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UniversitySemesterController extends UniversityBaseController
{
    public function create(int $enrollmentId): RedirectResponse|View
    {
        $this->requireUniversityPermission('create');
        $enrollment = $this->scopedEnrollment($enrollmentId);
        $currentEducationRecord = UniversityCurrentEducationSource::latestForClient($enrollment->client_id);

        if (!$currentEducationRecord) {
            return redirect()
                ->route('education_record_add', $enrollment->client_id)
                ->with('info', 'กรุณาบันทึกภาคเรียน ระดับการศึกษา และสถานศึกษาปัจจุบันใน Education Record ก่อนเพิ่มภาคเรียนมหาวิทยาลัย');
        }

        $alreadyLinked = UniversitySemesterRecord::query()
            ->where('enrollment_id', $enrollment->id)
            ->where('education_record_id', $currentEducationRecord->id)
            ->first();

        if ($alreadyLinked) {
            return redirect()
                ->route('university.semesters.show', $alreadyLinked->id)
                ->with('info', 'ภาคเรียนปัจจุบันจาก Education Record ถูกเพิ่มในหน้าเด็กมหาวิทยาลัยแล้ว กรุณาแก้ไขรายการเดิม');
        }

        return view('university.semesters.form', $this->formData(
            $enrollment,
            new UniversitySemesterRecord(),
            false,
            $currentEducationRecord
        ));
    }

    public function store(Request $request, int $enrollmentId): RedirectResponse
    {
        $this->requireUniversityPermission('create');
        $enrollment = $this->scopedEnrollment($enrollmentId);
        $currentEducationRecord = UniversityCurrentEducationSource::latestForClient($enrollment->client_id);

        if (!$currentEducationRecord) {
            return redirect()
                ->route('education_record_add', $enrollment->client_id)
                ->with('info', 'กรุณาบันทึกภาคเรียน ระดับการศึกษา และสถานศึกษาปัจจุบันใน Education Record ก่อนเพิ่มภาคเรียนมหาวิทยาลัย');
        }

        $submittedEducationRecordId = $request->integer('education_record_id');
        if ($submittedEducationRecordId && $submittedEducationRecordId !== (int) $currentEducationRecord->id) {
            return redirect()
                ->route('university.semesters.create', $enrollment->id)
                ->with('info', 'Education Record ปัจจุบันมีการเปลี่ยนแปลงแล้ว ระบบโหลดภาคเรียนล่าสุดให้ใหม่ กรุณาตรวจสอบก่อนบันทึก');
        }

        [$validated, $subjectRows] = $this->validatedPayload($request);
        [$semesterId, $academicYear, $term] = $this->periodFromEducationRecord($currentEducationRecord);

        $exists = UniversitySemesterRecord::query()
            ->where('enrollment_id', $enrollment->id)
            ->where(function ($query) use ($currentEducationRecord, $academicYear, $term) {
                $query->where('education_record_id', $currentEducationRecord->id)
                    ->orWhere(function ($period) use ($academicYear, $term) {
                        $period->where('academic_year', $academicYear)
                            ->where('term', $term);
                    });
            })
            ->first();

        if ($exists) {
            return redirect()
                ->route('university.semesters.show', $exists->id)
                ->with('info', 'มีข้อมูลภาคเรียนปัจจุบันนี้แล้ว กรุณาแก้ไขรายการเดิมแทนการเพิ่มซ้ำ');
        }

        $record = DB::transaction(function () use (
            $validated,
            $subjectRows,
            $enrollment,
            $currentEducationRecord,
            $semesterId,
            $academicYear,
            $term
        ) {
            UniversitySemesterRecord::query()
                ->where('enrollment_id', $enrollment->id)
                ->lockForUpdate()
                ->get(['id']);

            $record = UniversitySemesterRecord::create([
                'enrollment_id' => $enrollment->id,
                'education_record_id' => $currentEducationRecord->id,
                'semester_id' => $semesterId,
                'academic_year' => $academicYear,
                'term' => $term,
                'year_level' => $validated['year_level'],
                'record_date' => $validated['record_date'],
                'registered_credits' => $validated['registered_credits'] ?? null,
                'earned_credits' => $validated['earned_credits'] ?? null,
                'cumulative_credits' => $validated['cumulative_credits'] ?? null,
                'semester_gpa' => $validated['semester_gpa'] ?? null,
                'cumulative_gpa' => $validated['cumulative_gpa'] ?? null,
                'academic_status' => $validated['academic_status'],
                'risk_level' => $validated['risk_level'],
                'risk_note' => $validated['risk_note'] ?? null,
                'semester_summary' => $validated['semester_summary'] ?? null,
            ]);

            foreach ($subjectRows as $row) {
                unset($row['id']);
                $record->subjects()->create($row);
            }

            // UNIVERSITY_CREDIT_AUTO_SYNC_STORE_V1
            // คำนวณเครดิตจากรายวิชาจริงหลังบันทึกรายวิชาเรียบร้อยแล้ว
            UniversityCreditCalculator::syncEnrollment((int) $record->enrollment_id);
            $record->refresh();

            return $record;
        });

        return redirect()->route('university.semesters.show', $record)
            ->with('success', 'บันทึกข้อมูลภาคเรียนและรายวิชาเรียบร้อยแล้ว');
    }

    public function show(int $id): View
    {
        $this->requireUniversityPermission('view');
        $record = $this->scopedSemesterRecord($id);
        $record->load([
            'educationRecord.education',
            'educationRecord.semester',
            'subjects',
            'documents.uploader',
            'followups' => fn ($query) => $query
                ->with(['issues', 'follower'])
                ->orderBy('sequence_no')
                ->orderBy('followup_date')
                ->orderBy('id'),
        ]);

        $schoolFollowups = collect();

        if ($record->education_record_id) {
            $schoolFollowups = SchoolFollowup::query()
                ->where('client_id', $record->enrollment->client_id)
                ->where('education_record_id', $record->education_record_id)
                ->orderBy('follow_date')
                ->orderBy('id')
                ->get();
        }

        return view('university.semesters.show', [
            'record' => $record,
            'enrollment' => $record->enrollment,
            'client' => $record->enrollment->client,
            'schoolFollowups' => $schoolFollowups,
            'universityPermissions' => $this->universityPermissionBag(),
        ]);
    }

    public function edit(int $id): View
    {
        $this->requireUniversityPermission('update');
        $record = $this->scopedSemesterRecord($id);
        $record->load(['subjects', 'educationRecord.education', 'educationRecord.semester']);

        return view('university.semesters.form', $this->formData(
            $record->enrollment,
            $record,
            true,
            $record->educationRecord
        ));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->requireUniversityPermission('update');
        $record = $this->scopedSemesterRecord($id);
        [$validated, $subjectRows, $deletedSubjectIds] = $this->validatedPayload($request, true);

        DB::transaction(function () use ($record, $validated, $subjectRows, $deletedSubjectIds) {
            UniversitySemesterRecord::query()->whereKey($record->id)->lockForUpdate()->firstOrFail();

            // ภาคเรียน/ปีการศึกษา/education_record_id เป็นข้อมูลอ้างอิง ไม่เปิดให้เปลี่ยนจากหน้า University
            $record->update([
                'year_level' => $validated['year_level'],
                'record_date' => $validated['record_date'],
                'semester_gpa' => $validated['semester_gpa'] ?? null,
                'cumulative_gpa' => $validated['cumulative_gpa'] ?? null,
                'academic_status' => $validated['academic_status'],
                'risk_level' => $validated['risk_level'],
                'risk_note' => $validated['risk_note'] ?? null,
                'semester_summary' => $validated['semester_summary'] ?? null,
            ]);

            $this->syncSubjectsSafely($record, $subjectRows, $deletedSubjectIds);

            // UNIVERSITY_CREDIT_AUTO_SYNC_UPDATE_V1
            UniversityCreditCalculator::syncEnrollment((int) $record->enrollment_id);
            $record->refresh();
        });

        return redirect()->route('university.semesters.show', $record)
            ->with('success', 'แก้ไขข้อมูลภาคเรียนเรียบร้อยแล้ว');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->requireUniversityPermission('delete');
        $record = $this->scopedSemesterRecord($id);
        $enrollmentId = $record->enrollment_id;
        $record->load('documents');
        foreach ($record->documents as $document) {
            if ($document->file_path) {
                Storage::disk('local')->delete($document->file_path);
            }
        }
        $record->delete();

        return redirect()->route('university.enrollments.show', $enrollmentId)
            ->with('success', 'ลบข้อมูลภาคเรียนเรียบร้อยแล้ว');
    }

    private function formData(
        $enrollment,
        UniversitySemesterRecord $record,
        bool $isEdit,
        ?EducationRecord $educationRecord
    ): array {
        $semesterLabel = $educationRecord
            ? UniversityCurrentEducationSource::semesterName($educationRecord)
            : ($record->exists ? $record->term . '/' . $record->academic_year : null);

        return [
            'enrollment' => $enrollment,
            'client' => $enrollment->client,
            'record' => $record,
            'currentEducationRecord' => $educationRecord,
            'semesterLabel' => $semesterLabel,
            'isEdit' => $isEdit,
            'universityPermissions' => $this->universityPermissionBag(),
        ];
    }

    private function validatedPayload(Request $request, bool $isEdit = false): array
    {
        $maxSubjects = (int) config('university_tracking.max_subjects_per_semester', 40);
        $academicStatuses = array_keys(config('university_tracking.academic_statuses', []));
        $riskLevels = array_keys(config('university_tracking.risk_levels', []));

        $validated = $request->validate([
            'education_record_id' => [$isEdit ? 'nullable' : 'required', 'integer', Rule::exists('education_records', 'id')],
            'year_level' => ['required', 'integer', 'between:1,8'],
            'record_date' => ['required', 'date', 'before_or_equal:' . now('Asia/Bangkok')->toDateString()],
            'registered_credits' => ['nullable', 'numeric', 'between:0,99.99'],
            'earned_credits' => ['nullable', 'numeric', 'between:0,99.99'],
            'cumulative_credits' => ['nullable', 'numeric', 'between:0,999.99'],
            'semester_gpa' => ['nullable', 'numeric', 'between:0,4', 'decimal:0,2'],
            'cumulative_gpa' => ['nullable', 'numeric', 'between:0,4', 'decimal:0,2'],
            'academic_status' => ['required', Rule::in($academicStatuses)],
            'risk_level' => ['required', Rule::in($riskLevels)],
            'risk_note' => ['nullable', 'string', 'max:5000'],
            'semester_summary' => ['nullable', 'string', 'max:5000'],
            'subjects' => ['nullable', 'array', 'max:' . $maxSubjects],
            'subjects.*.id' => ['nullable', 'integer'],
            'subjects.*.course_code' => ['nullable', 'string', 'max:100'],
            'subjects.*.course_name' => ['required_with:subjects.*.course_code,subjects.*.credits,subjects.*.grade', 'nullable', 'string', 'max:255'],
            'subjects.*.credits' => ['nullable', 'numeric', 'between:0,30'],
            'subjects.*.grade' => ['nullable', 'string', 'max:20'],
            'subjects.*.grade_point' => ['nullable', 'numeric', 'between:0,4'],
            'subjects.*.result_status' => ['nullable', Rule::in(['pass', 'fail', 'withdrawn', 'incomplete', 'satisfactory', 'unsatisfactory', 'audit', 'other', 'pending'])],
            'subjects.*.note' => ['nullable', 'string', 'max:1000'],
            'deleted_subject_ids' => ['nullable', 'array'],
            'deleted_subject_ids.*' => ['integer'],
        ], [
            'year_level.required' => 'กรุณาเลือกชั้นปี',
            'year_level.between' => 'ชั้นปีต้องอยู่ระหว่างปี 1 ถึงปี 8',
            'record_date.before_or_equal' => 'วันที่บันทึกต้องไม่เกินวันปัจจุบัน',
            'semester_gpa.between' => 'เกรดเฉลี่ยภาคเรียนต้องอยู่ระหว่าง 0.00 ถึง 4.00',
            'cumulative_gpa.between' => 'เกรดเฉลี่ยสะสมต้องอยู่ระหว่าง 0.00 ถึง 4.00',
        ]);

        $subjectRows = collect($validated['subjects'] ?? [])
            ->map(function ($row) {
                return [
                    'id' => !empty($row['id']) ? (int) $row['id'] : null,
                    'course_code' => filled($row['course_code'] ?? null) ? trim($row['course_code']) : null,
                    'course_name' => filled($row['course_name'] ?? null) ? trim($row['course_name']) : null,
                    'credits' => ($row['credits'] ?? '') === '' ? null : $row['credits'],
                    'grade' => filled($row['grade'] ?? null) ? trim($row['grade']) : null,
                    'grade_point' => ($row['grade_point'] ?? '') === '' ? null : $row['grade_point'],
                    'result_status' => filled($row['result_status'] ?? null) ? $row['result_status'] : null,
                    'note' => filled($row['note'] ?? null) ? trim($row['note']) : null,
                ];
            })
            ->filter(fn ($row) => filled($row['course_name']) || filled($row['course_code']) || !empty($row['id']))
            ->values()
            ->all();

        $deletedSubjectIds = collect($validated['deleted_subject_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        unset($validated['subjects'], $validated['deleted_subject_ids']);

        return $isEdit
            ? [$validated, $subjectRows, $deletedSubjectIds]
            : [$validated, $subjectRows];
    }

    private function periodFromEducationRecord(EducationRecord $educationRecord): array
    {
        $semesterName = UniversityCurrentEducationSource::semesterName($educationRecord);

        if (!$educationRecord->semester_id || !$semesterName) {
            throw ValidationException::withMessages([
                'education_record_id' => 'Education Record ปัจจุบันไม่มีข้อมูลภาคเรียนที่สมบูรณ์ กรุณาตรวจสอบหน้า Education Record ก่อน',
            ]);
        }

        [$term, $academicYear] = $this->parseSemesterName($semesterName);

        return [(int) $educationRecord->semester_id, $academicYear, $term];
    }

    private function parseSemesterName(string $name): array
    {
        if (preg_match('/(\d+)\s*\/\s*(\d{4})/u', $name, $matches)) {
            return [(int) $matches[1], (int) $matches[2]];
        }

        throw ValidationException::withMessages([
            'education_record_id' => 'รูปแบบภาคเรียนไม่ถูกต้อง ระบบคาดหวังรูปแบบ เช่น 1/2569 หรือ 2/2569',
        ]);
    }

    private function syncSubjectsSafely(
        UniversitySemesterRecord $record,
        array $subjectRows,
        array $deletedSubjectIds
    ): void {
        if ($deletedSubjectIds) {
            $record->subjects()->whereIn('id', $deletedSubjectIds)->delete();
        }

        $deletedLookup = array_fill_keys($deletedSubjectIds, true);

        foreach ($subjectRows as $row) {
            $subjectId = $row['id'] ?? null;
            unset($row['id']);

            if ($subjectId) {
                if (isset($deletedLookup[$subjectId])) {
                    continue;
                }

                $subject = $record->subjects()->whereKey($subjectId)->first();
                if (!$subject) {
                    throw ValidationException::withMessages([
                        'subjects' => 'พบรายการรายวิชาที่ไม่ตรงกับภาคเรียนนี้ ระบบหยุดการบันทึกเพื่อป้องกันข้อมูลเดิมสูญหาย',
                    ]);
                }

                $subject->update($row);
                continue;
            }

            if (filled($row['course_name'] ?? null) || filled($row['course_code'] ?? null)) {
                $record->subjects()->create($row);
            }
        }
    }
}
