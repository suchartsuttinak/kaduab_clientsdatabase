<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Client;
use App\Models\Education;
use App\Models\EducationRecord;
use App\Models\Institution;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EducationRecordController extends Controller
{
    private const MAX_SUBJECTS_PER_RECORD = 30;

    public function EducationRecordAdd($client_id): View
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        /*
         * ผู้ใช้อ่านอย่างเดียวต้องเห็นข้อมูลเดิม ไม่ใช่ฟอร์มเพิ่มที่ว่างเปล่า
         * จึงใช้หน้ารายการเดิม และส่วนกลางจะเปลี่ยนปุ่มแก้ไขเป็น “ดูข้อมูล”
         */
        if ($this->isGradeEntryReadOnly()) {
            $educationRecords = $this->orderedEducationRecords($client->id);

            return view(
                'frontend.client.education_record.education_record_show',
                compact('client', 'educationRecords')
            );
        }

        return view(
            'frontend.client.education_record.education_record_create',
            array_merge(['client' => $client], $this->formOptions())
        );
    }

    public function EducationRecordStore(Request $request): RedirectResponse
    {
        $this->normalizeRequest($request);

        $validated = $request->validate(
            $this->validationRules(),
            $this->validationMessages()
        );

        $client = Client::forUser(auth()->user())
            ->findOrFail($validated['client_id']);

        if ($this->hasDuplicateRecord(
            $client->id,
            (int) $validated['semester_id'],
            (string) $validated['school_name']
        )) {
            return back()
                ->withInput()
                ->with('error', 'ผู้รับบริการรายนี้มีผลการเรียนของสถานศึกษาและภาคเรียน/ปีการศึกษานี้แล้ว ไม่สามารถบันทึกซ้ำได้');
        }

        $record = DB::transaction(function () use ($validated, $client) {
            // ล็อกผู้รับบริการ ป้องกันคำขอบันทึกพร้อมกันสร้างรายการซ้ำ
            Client::query()->whereKey($client->id)->lockForUpdate()->first();

            $institution = Institution::firstOrCreate([
                'institution_name' => $validated['school_name'],
            ]);

            if ($this->hasDuplicateRecord(
                $client->id,
                (int) $validated['semester_id'],
                (string) $validated['school_name'],
                null,
                (int) $institution->id
            )) {
                throw ValidationException::withMessages([
                    'semester_id' => 'ผู้รับบริการรายนี้มีผลการเรียนของสถานศึกษาและภาคเรียน/ปีการศึกษานี้แล้ว ไม่สามารถบันทึกซ้ำได้',
                ]);
            }

            $record = EducationRecord::create([
                'client_id'      => $client->id,
                'education_id'   => $validated['education_id'],
                'semester_id'    => $validated['semester_id'],
                'school_name'    => $validated['school_name'],
                'institution_id' => $institution->id,
                'record_date'    => $validated['record_date'],
                'grade_average'  => $this->normalizeGpa($validated['grade_average'] ?? null),
            ]);

            $record->subjects()->sync($this->buildSubjectSyncData($validated['subjects'] ?? []));

            $this->replaceCaseActivity(
                $record,
                'บันทึกผลการเรียน'
            );

            return $record;
        });

        return redirect()
            ->route('education_record_show', ['client_id' => $record->client_id])
            ->with('success', 'บันทึกผลการเรียนเรียบร้อยแล้ว');
    }

    public function EducationRecordEdit($id): View
    {
        $record = EducationRecord::with(['subjects', 'client'])
            ->whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->findOrFail($id);

        // EDUCATION_RECORD_UNIVERSITY_HISTORY_GUARD_V1
        $universitySemesterLink = DB::table('university_semester_records')
            ->where('education_record_id', $record->id)
            ->first(['id', 'academic_year', 'term', 'year_level']);

        return view(
            'frontend.client.education_record.education_record_edit',
            array_merge([
                'record' => $record,
                'client' => $record->client,
                'isUniversityEducationLocked' => (bool) $universitySemesterLink,
                'universitySemesterLink' => $universitySemesterLink,
            ], $this->formOptions())
        );
    }

    public function EducationRecordUpdate(Request $request, $id): RedirectResponse
    {
        $record = EducationRecord::whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->findOrFail($id);

        $this->normalizeRequest($request);

        $validated = $request->validate(
            $this->validationRules($record),
            $this->validationMessages()
        );

        // EDUCATION_RECORD_UNIVERSITY_HISTORY_GUARD_UPDATE_V1
        $universitySemesterLink = DB::table('university_semester_records')
            ->where('education_record_id', $record->id)
            ->first(['id', 'academic_year', 'term', 'year_level']);

        if ($universitySemesterLink) {
            $linkedPeriod = (int) $universitySemesterLink->term
                . '/' . (int) $universitySemesterLink->academic_year;

            $submittedSemesterName = DB::table('semesters')
                ->where('id', (int) $validated['semester_id'])
                ->value('semester_name');

            $submittedEducationName = DB::table('education_levels')
                ->where('id', (int) $validated['education_id'])
                ->value('education_name');

            $expectedEducationName = 'ปริญญาตรีชั้นปีที่ '
                . (int) $universitySemesterLink->year_level;

            $schoolChanged = trim((string) $validated['school_name'])
                !== trim((string) $record->school_name);

            $identityMatchesLinkedHistory = trim((string) $submittedSemesterName) === $linkedPeriod
                && trim((string) $submittedEducationName) === $expectedEducationName
                && !$schoolChanged;

            $currentlyMatchesLinkedHistory = (int) $record->semester_id === (int) $validated['semester_id']
                && (int) $record->education_id === (int) $validated['education_id']
                && !$schoolChanged;

            // ถ้าข้อมูลปัจจุบันเคยถูกแก้จน mismatch อนุญาตเฉพาะการ "ซ่อมกลับ"
            // ให้ตรงกับ University Semester เดิมเท่านั้น
            if (!$currentlyMatchesLinkedHistory && !$identityMatchesLinkedHistory) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'รายการนี้ถูกใช้ในเด็กมหาวิทยาลัย ภาคเรียน '
                        . $linkedPeriod
                        . ' แล้ว จึงไม่อนุญาตให้เปลี่ยนระดับการศึกษา ภาคเรียน หรือสถานศึกษา '
                        . 'หากขึ้นภาคเรียนใหม่ กรุณากด “เพิ่มข้อมูลผลการเรียน” เพื่อสร้างรายการใหม่ '
                        . 'สำหรับรายการนี้อนุญาตเฉพาะการแก้กลับให้ตรงกับภาคเรียนเดิม'
                    );
            }

            if ($currentlyMatchesLinkedHistory) {
                $educationChanged = (int) $validated['education_id'] !== (int) $record->education_id;
                $semesterChanged = (int) $validated['semester_id'] !== (int) $record->semester_id;

                if ($educationChanged || $semesterChanged || $schoolChanged) {
                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            'รายการนี้ถูกใช้ในเด็กมหาวิทยาลัย ภาคเรียน '
                            . $linkedPeriod
                            . ' แล้ว จึงล็อกระดับการศึกษา ภาคเรียน และสถานศึกษาไว้ '
                            . 'หากขึ้นภาคเรียนใหม่ กรุณากด “เพิ่มข้อมูลผลการเรียน”'
                        );
                }
            }
        }
        if ($this->hasDuplicateRecord(
            $record->client_id,
            (int) $validated['semester_id'],
            (string) $validated['school_name'],
            $record->id
        )) {
            return back()
                ->withInput()
                ->with('error', 'ผู้รับบริการรายนี้มีผลการเรียนของสถานศึกษาและภาคเรียน/ปีการศึกษานี้แล้ว ไม่สามารถแก้ไขให้ซ้ำกันได้');
        }

        DB::transaction(function () use ($validated, $record) {
            $lockedRecord = EducationRecord::query()
                ->whereKey($record->id)
                ->lockForUpdate()
                ->firstOrFail();

            Client::query()
                ->whereKey($lockedRecord->client_id)
                ->lockForUpdate()
                ->first();

            $institution = Institution::firstOrCreate([
                'institution_name' => $validated['school_name'],
            ]);

            if ($this->hasDuplicateRecord(
                $lockedRecord->client_id,
                (int) $validated['semester_id'],
                (string) $validated['school_name'],
                $lockedRecord->id,
                (int) $institution->id
            )) {
                throw ValidationException::withMessages([
                    'semester_id' => 'ผู้รับบริการรายนี้มีผลการเรียนของสถานศึกษาและภาคเรียน/ปีการศึกษานี้แล้ว ไม่สามารถแก้ไขให้ซ้ำกันได้',
                ]);
            }

            // ไม่อัปเดต client_id เพื่อป้องกันย้ายรายการไปยังผู้รับบริการคนอื่น
            $lockedRecord->update([
                'education_id'   => $validated['education_id'],
                'semester_id'    => $validated['semester_id'],
                'school_name'    => $validated['school_name'],
                'institution_id' => $institution->id,
                'record_date'    => $validated['record_date'],
                'grade_average'  => $this->normalizeGpa($validated['grade_average'] ?? null),
            ]);

            $lockedRecord->subjects()->sync(
                $this->buildSubjectSyncData($validated['subjects'] ?? [])
            );

            $this->replaceCaseActivity(
                $lockedRecord->fresh('education'),
                'แก้ไขผลการเรียน'
            );
        });

        return redirect()
            ->route('education_record_show', ['client_id' => $record->client_id])
            ->with('success', 'แก้ไขผลการเรียนเรียบร้อยแล้ว');
    }

    public function EducationRecordShow($client_id): RedirectResponse|View
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $educationRecords = $this->orderedEducationRecords($client->id);

        if ($educationRecords->isEmpty() && $this->canGradeEntry('create')) {
            return redirect()
                ->route('education_record_add', ['client_id' => $client->id])
                ->with('info', 'ยังไม่มีข้อมูลผลการเรียน กรุณาบันทึกข้อมูลก่อน');
        }

        return view(
            'frontend.client.education_record.education_record_show',
            compact('client', 'educationRecords')
        );
    }

    public function EducationRecordDelete($id): RedirectResponse
    {
        $record = EducationRecord::whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->findOrFail($id);

        // EDUCATION_RECORD_UNIVERSITY_HISTORY_GUARD_DELETE_V1
        $universitySemesterLink = DB::table('university_semester_records')
            ->where('education_record_id', $record->id)
            ->first(['id', 'academic_year', 'term']);

        if ($universitySemesterLink) {
            return back()->with(
                'error',
                'ไม่สามารถลบ Education Record นี้ได้ เนื่องจากถูกใช้ในเด็กมหาวิทยาลัย ภาคเรียน '
                . $universitySemesterLink->term . '/' . $universitySemesterLink->academic_year
                . ' แล้ว หากเป็นข้อมูลทดสอบหรือบันทึกผิด กรุณาจัดการรายการมหาวิทยาลัยที่เชื่อมอยู่ก่อน'
            );
        }
        $clientId = $record->client_id;

        DB::transaction(function () use ($record, $clientId) {
            $lockedRecord = EducationRecord::query()
                ->whereKey($record->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedRecord->subjects()->detach();
            $lockedRecord->delete();

            $this->refreshLatestCaseActivity($clientId);
        });

        $hasRemainingRecords = EducationRecord::query()
            ->where('client_id', $clientId)
            ->exists();

        $routeName = $hasRemainingRecords || !$this->canGradeEntry('create')
            ? 'education_record_show'
            : 'education_record_add';

        return redirect()
            ->route($routeName, ['client_id' => $clientId])
            ->with('success', 'ลบข้อมูลผลการเรียนเรียบร้อยแล้ว');
    }

    public function EducationRecordReport($client_id): View
    {
        $client = Client::forUser(auth()->user())->findOrFail($client_id);

        $educationRecords = $this->educationRecordsWithSemesterQuery($client->id)
            ->orderByRaw("
                CAST(SUBSTRING_INDEX(semesters.semester_name, '/', -1) AS UNSIGNED) DESC,
                CAST(SUBSTRING_INDEX(semesters.semester_name, '/', 1) AS UNSIGNED) DESC
            ")
            ->orderByDesc('education_records.record_date')
            ->orderByDesc('education_records.id')
            ->get();

        // ใช้หลักเดียวกับหน้าติดตามผลการเรียน:
        // ปีการศึกษามากที่สุดก่อน แล้วจึงภาคเรียนมากที่สุด
        $latestEducationRecord = $educationRecords->first();

        return view(
            'frontend.client.education_record.education_record_report',
            compact('client', 'educationRecords', 'latestEducationRecord')
        );
    }

    public function EducationRecordReportById($id): View
    {
        $record = EducationRecord::query()
            ->with([
                'subjects',
                'education',
                'semester',
                'institution',
            ])
            ->leftJoin(
                'semesters',
                'education_records.semester_id',
                '=',
                'semesters.id'
            )
            ->whereHas('client', function ($query) {
                $query->forUser(auth()->user());
            })
            ->where('education_records.id', $id)
            ->select([
                'education_records.*',
                'semesters.semester_name as semester_label',
            ])
            ->firstOrFail();

        $client = Client::forUser(auth()->user())
            ->findOrFail($record->client_id);

        $educationRecords = collect([$record]);
        $latestEducationRecord = $record;

        return view(
            'frontend.client.education_record.education_record_report',
            compact('client', 'educationRecords', 'latestEducationRecord')
        );
    }

    /**
     * Query กลางสำหรับดึงผลการเรียนพร้อมชื่อภาคเรียนโดยตรงจากตาราง semesters
     * ไม่พึ่งความสัมพันธ์เพียงอย่างเดียว และใช้ซ้ำได้ทั้งหน้ารายการ/รายงาน
     */
    private function orderedEducationRecords(int $clientId)
    {
        return $this->educationRecordsWithSemesterQuery($clientId)
            ->orderByRaw("
                CAST(SUBSTRING_INDEX(semesters.semester_name, '/', -1) AS UNSIGNED) DESC,
                CAST(SUBSTRING_INDEX(semesters.semester_name, '/', 1) AS UNSIGNED) DESC
            ")
            ->orderByDesc('education_records.record_date')
            ->orderByDesc('education_records.id')
            ->get();
    }

    private function isGradeEntryReadOnly(): bool
    {
        return $this->canGradeEntry('view') && !$this->canGradeEntry('create');
    }

    private function canGradeEntry(string $action): bool
    {
        $user = auth()->user();

        if (!$user || !method_exists($user, 'hasFormPermission')) {
            return true;
        }

        return (bool) $user->hasFormPermission('education_grade_entry', $action);
    }

    private function educationRecordsWithSemesterQuery(int $clientId)
    {
        return EducationRecord::query()
            ->with([
                'subjects',
                'education',
                'semester',
                'institution',
            ])
            ->leftJoin(
                'semesters',
                'education_records.semester_id',
                '=',
                'semesters.id'
            )
            ->where('education_records.client_id', $clientId)
            ->select([
                'education_records.*',
                'semesters.semester_name as semester_label',
            ]);
    }

    private function formOptions(): array
    {
        return [
            'subjects' => Subject::query()
                ->orderBy('subject_name')
                ->get(['id', 'subject_name']),
            'semesters' => Semester::query()
                ->orderByRaw("CAST(SUBSTRING_INDEX(semester_name, '/', -1) AS UNSIGNED) DESC")
                ->orderByRaw("CAST(SUBSTRING_INDEX(semester_name, '/', 1) AS UNSIGNED) DESC")
                ->get(['id', 'semester_name']),
            'educations' => Education::query()
                ->orderBy('id')
                ->get(['id', 'education_name']),
        ];
    }

    private function validationRules(?EducationRecord $record = null): array
    {
        $clientRule = $record
            ? ['required', 'integer', Rule::in([(int) $record->client_id])]
            : ['required', 'integer', Rule::exists((new Client())->getTable(), 'id')];

        return [
            'client_id' => $clientRule,
            'education_id' => [
                'required',
                'integer',
                Rule::exists((new Education())->getTable(), 'id'),
            ],
            'semester_id' => [
                'required',
                'integer',
                Rule::exists((new Semester())->getTable(), 'id'),
            ],
            'school_name' => ['required', 'string', 'max:255'],
            'record_date' => [
                'required',
                'date',
                'before_or_equal:' . now('Asia/Bangkok')->toDateString(),
            ],
            'grade_average' => ['nullable', 'numeric', 'between:0,4', 'decimal:0,2'],
            'subjects' => ['nullable', 'array', 'max:' . self::MAX_SUBJECTS_PER_RECORD],
            'subjects.*.subject_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists((new Subject())->getTable(), 'id'),
            ],
            'subjects.*.score' => ['nullable', 'numeric', 'between:0,100'],
            // รับค่าเพื่อให้ฟอร์มเดิมทำงาน แต่คำนวณเกรดใหม่จากคะแนนฝั่ง Server เท่านั้น
            'subjects.*.grade' => ['nullable'],
        ];
    }

    private function validationMessages(): array
    {
        return [
            'client_id.required' => 'ไม่พบข้อมูลผู้รับบริการ',
            'client_id.in' => 'ไม่สามารถย้ายผลการเรียนไปยังผู้รับบริการคนอื่นได้',
            'education_id.required' => 'กรุณาเลือกระดับการศึกษา',
            'education_id.exists' => 'ระดับการศึกษาที่เลือกไม่ถูกต้อง',
            'semester_id.required' => 'กรุณาเลือกภาคเรียน',
            'semester_id.exists' => 'ภาคเรียนที่เลือกไม่ถูกต้อง',
            'school_name.required' => 'กรุณากรอกชื่อสถานศึกษา',
            'school_name.string' => 'ชื่อสถานศึกษาต้องเป็นข้อความ',
            'school_name.max' => 'ชื่อสถานศึกษาต้องไม่เกิน 255 ตัวอักษร',
            'record_date.required' => 'กรุณาเลือกวันที่บันทึก',
            'record_date.date' => 'วันที่บันทึกต้องอยู่ในรูปแบบวันที่',
            'record_date.before_or_equal' => 'วันที่บันทึกต้องไม่เกินวันปัจจุบัน',
            'grade_average.numeric' => 'เกรดเฉลี่ยต้องเป็นตัวเลข',
            'grade_average.between' => 'เกรดเฉลี่ยต้องอยู่ระหว่าง 0.00 ถึง 4.00',
            'grade_average.decimal' => 'เกรดเฉลี่ยใส่ทศนิยมได้ไม่เกิน 2 ตำแหน่ง',
            'subjects.array' => 'รูปแบบข้อมูลรายวิชาไม่ถูกต้อง',
            'subjects.max' => 'เพิ่มรายวิชาได้ไม่เกิน ' . self::MAX_SUBJECTS_PER_RECORD . ' รายการ',
            'subjects.*.subject_id.required' => 'กรุณาเลือกรายวิชาในทุกรายการที่เพิ่ม',
            'subjects.*.subject_id.distinct' => 'ไม่สามารถเลือกรายวิชาเดิมซ้ำกันได้',
            'subjects.*.subject_id.exists' => 'รายวิชาที่เลือกไม่ถูกต้อง',
            'subjects.*.score.numeric' => 'คะแนนต้องเป็นตัวเลข',
            'subjects.*.score.between' => 'คะแนนต้องอยู่ระหว่าง 0 ถึง 100',
        ];
    }

    private function normalizeRequest(Request $request): void
    {
        $schoolName = $this->normalizeSchoolName(
            (string) $request->input('school_name')
        );

        $subjects = collect($request->input('subjects', []))
            ->map(function ($row) {
                return [
                    'subject_id' => $row['subject_id'] ?? null,
                    'score' => isset($row['score']) && $row['score'] !== ''
                        ? $row['score']
                        : null,
                    'grade' => $row['grade'] ?? null,
                ];
            })
            ->filter(function ($row) {
                return filled($row['subject_id']) || $row['score'] !== null;
            })
            ->values()
            ->all();

        $request->merge([
            'school_name' => $schoolName,
            'subjects' => $subjects,
            'grade_average' => $request->input('grade_average') === ''
                ? null
                : $request->input('grade_average'),
        ]);
    }

    private function buildSubjectSyncData(array $subjects): array
    {
        $syncData = [];

        foreach ($subjects as $subject) {
            $score = $subject['score'] ?? null;
            $syncData[(int) $subject['subject_id']] = [
                'score' => $score,
                'grade' => $this->calculateGradeFromScore($score),
            ];
        }

        return $syncData;
    }

    /**
     * ป้องกันการบันทึกซ้ำตามเงื่อนไข:
     * ผู้รับบริการคนเดิม + สถานศึกษาเดิม + ภาคเรียน/ปีการศึกษาเดิม
     *
     * ไม่ใช้ระดับการศึกษาเป็นส่วนหนึ่งของเงื่อนไข เพราะการเปลี่ยนระดับชั้น
     * ต้องไม่ทำให้สามารถสร้างรายการซ้ำในสถานศึกษาและภาคเรียนเดิมได้
     */
    private function hasDuplicateRecord(
        int $clientId,
        int $semesterId,
        string $schoolName,
        ?int $ignoreId = null,
        ?int $institutionId = null
    ): bool {
        $schoolKey = $this->schoolNameKey($schoolName);

        return EducationRecord::query()
            ->where('client_id', $clientId)
            ->where('semester_id', $semesterId)
            ->when(
                $ignoreId !== null,
                fn ($query) => $query->where('id', '!=', $ignoreId)
            )
            ->get(['id', 'institution_id', 'school_name'])
            ->contains(function (EducationRecord $candidate) use (
                $institutionId,
                $schoolKey
            ): bool {
                if (
                    $institutionId !== null
                    && $candidate->institution_id !== null
                    && (int) $candidate->institution_id === $institutionId
                ) {
                    return true;
                }

                return $this->schoolNameKey(
                    (string) $candidate->school_name
                ) === $schoolKey;
            });
    }

    private function normalizeSchoolName(string $schoolName): string
    {
        return preg_replace('/\s+/u', ' ', trim($schoolName)) ?? '';
    }

    private function schoolNameKey(string $schoolName): string
    {
        return mb_strtolower(
            $this->normalizeSchoolName($schoolName),
            'UTF-8'
        );
    }

    private function normalizeGpa($gpa): ?string
    {
        if ($gpa === null || $gpa === '') {
            return null;
        }

        return number_format((float) $gpa, 2, '.', '');
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

    private function replaceCaseActivity(EducationRecord $record, string $title): void
    {
        CaseActivity::query()
            ->where('client_id', $record->client_id)
            ->where('module', 'education_record')
            ->delete();

        $record->loadMissing('education');

        $semesterName = Semester::query()
            ->whereKey($record->semester_id)
            ->value('semester_name');

        CaseActivity::record([
            'client_id' => $record->client_id,
            'module' => 'education_record',
            'type' => 'success',
            'title' => $title,
            'description' => 'สถานศึกษา: ' . ($record->school_name ?: '-')
                . ' | ระดับการศึกษา: ' . (data_get($record, 'education.education_name') ?: '-')
                . ' | ภาคเรียน: ' . ($semesterName ?: '-')
                . ' | เกรดเฉลี่ย: ' . ($record->grade_average ?? '-'),
            'occurred_at' => $record->record_date,
            'icon' => 'bi-mortarboard',
            'url' => route('education_record_show', ['client_id' => $record->client_id]),
        ]);
    }

    private function refreshLatestCaseActivity(int $clientId): void
    {
        $latestRecord = $this->educationRecordsWithSemesterQuery($clientId)
            ->orderByRaw("
                CAST(SUBSTRING_INDEX(semesters.semester_name, '/', -1) AS UNSIGNED) DESC,
                CAST(SUBSTRING_INDEX(semesters.semester_name, '/', 1) AS UNSIGNED) DESC
            ")
            ->orderByDesc('education_records.record_date')
            ->orderByDesc('education_records.id')
            ->first();

        if ($latestRecord) {
            $this->replaceCaseActivity($latestRecord, 'ผลการเรียนล่าสุด');
            return;
        }

        CaseActivity::query()
            ->where('client_id', $clientId)
            ->where('module', 'education_record')
            ->delete();
    }
}