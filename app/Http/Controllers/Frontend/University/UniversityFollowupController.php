<?php

namespace App\Http\Controllers\Frontend\University;

use App\Models\SchoolFollowup;
use App\Models\UniversityFollowup;
use App\Models\UniversitySemesterRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UniversityFollowupController extends UniversityBaseController
{
    public function create(int $semesterRecordId): View
    {
        $this->requireUniversityPermission('create');

        $record = $this->scopedSemesterRecord($semesterRecordId);
        $record->load(['educationRecord.education', 'educationRecord.semester']);

        return view(
            'university.followups.form',
            $this->formData($record, new UniversityFollowup(), false)
        );
    }

    public function store(Request $request, int $semesterRecordId): RedirectResponse
    {
        $this->requireUniversityPermission('create');

        $record = $this->scopedSemesterRecord($semesterRecordId);
        [$validated, $issues] = $this->validatedPayload($request);

        $followup = DB::transaction(function () use ($record, $validated, $issues) {
            UniversitySemesterRecord::query()
                ->whereKey($record->id)
                ->lockForUpdate()
                ->firstOrFail();

            $existing = UniversityFollowup::query()
                ->where('semester_record_id', $record->id)
                ->orderBy('sequence_no')
                ->lockForUpdate()
                ->get();

            $nextSequence = ((int) $existing->max('sequence_no')) + 1;
            $last = $existing->sortByDesc('followup_date')->first();

            if ($last && $validated['followup_date'] <= $last->followup_date->format('Y-m-d')) {
                throw ValidationException::withMessages([
                    'followup_date' => 'ครั้งที่ ' . $nextSequence
                        . ' ต้องมีวันที่มากกว่าครั้งก่อนหน้า ('
                        . $last->followup_date->format('d/m/Y') . ')',
                ]);
            }

            $followup = UniversityFollowup::create(array_merge($validated, [
                'enrollment_id' => $record->enrollment_id,
                'semester_record_id' => $record->id,
                'semester_id' => $record->semester_id,
                'academic_year' => $record->academic_year,
                'sequence_no' => $nextSequence,

                // ข้อมูลการติดต่อเป็นหน้าที่ของ School Followup
                'followup_method' => null,
                'informant' => null,

                'followed_by' => auth()->id(),
            ]));

            $followup->issues()->createMany($issues);

            $record->update([
                'risk_level' => $validated['overall_risk_level'],
                'risk_note' => $validated['continuation_risk_note'] ?? null,
            ]);

            return $followup;
        });

        return redirect()
            ->route('university.semesters.show', $record)
            ->with(
                'success',
                'บันทึกการติดตามภาคเรียน '
                    . $record->term . '/' . $record->academic_year
                    . ' ครั้งที่ ' . $followup->sequence_no
                    . ' เรียบร้อยแล้ว'
            );
    }

    public function edit(int $id): View
    {
        $this->requireUniversityPermission('update');

        $followup = UniversityFollowup::query()
            ->with([
                'enrollment.client',
                'semesterRecord.educationRecord.education',
                'semesterRecord.educationRecord.semester',
                'issues',
            ])
            ->whereHas('enrollment.client', fn ($query) => $query->forUser(auth()->user()))
            ->findOrFail($id);

        abort_unless(
            $followup->semesterRecord,
            422,
            'ข้อมูลติดตามเดิมยังไม่ได้ผูกกับภาคเรียน จึงไม่สามารถใช้ฟอร์มติดตามแบบรายภาคเรียนได้'
        );

        return view(
            'university.followups.form',
            $this->formData($followup->semesterRecord, $followup, true)
        );
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->requireUniversityPermission('update');

        $followup = UniversityFollowup::query()
            ->with(['enrollment.client', 'semesterRecord'])
            ->whereHas('enrollment.client', fn ($query) => $query->forUser(auth()->user()))
            ->findOrFail($id);

        abort_unless(
            $followup->semesterRecord,
            422,
            'ข้อมูลติดตามเดิมยังไม่ได้ผูกกับภาคเรียน'
        );

        [$validated, $issues] = $this->validatedPayload($request);

        DB::transaction(function () use ($followup, $validated, $issues) {
            UniversitySemesterRecord::query()
                ->whereKey($followup->semester_record_id)
                ->lockForUpdate()
                ->firstOrFail();

            $siblings = UniversityFollowup::query()
                ->where('semester_record_id', $followup->semester_record_id)
                ->where('id', '!=', $followup->id)
                ->orderBy('sequence_no')
                ->lockForUpdate()
                ->get();

            if ($siblings->contains(
                fn ($item) => $item->followup_date?->format('Y-m-d') === $validated['followup_date']
            )) {
                throw ValidationException::withMessages([
                    'followup_date' => 'ภาคเรียนนี้มีการติดตามในวันที่ดังกล่าวแล้ว กรุณาเลือกวันที่อื่น',
                ]);
            }

            $previous = $siblings
                ->where('sequence_no', '<', $followup->sequence_no)
                ->sortByDesc('sequence_no')
                ->first();

            $next = $siblings
                ->where('sequence_no', '>', $followup->sequence_no)
                ->sortBy('sequence_no')
                ->first();

            if ($previous && $validated['followup_date'] <= $previous->followup_date->format('Y-m-d')) {
                throw ValidationException::withMessages([
                    'followup_date' => 'ครั้งที่ ' . $followup->sequence_no
                        . ' ต้องมีวันที่มากกว่าครั้งที่ ' . $previous->sequence_no,
                ]);
            }

            if ($next && $validated['followup_date'] >= $next->followup_date->format('Y-m-d')) {
                throw ValidationException::withMessages([
                    'followup_date' => 'ครั้งที่ ' . $followup->sequence_no
                        . ' ต้องมีวันที่น้อยกว่าครั้งที่ ' . $next->sequence_no,
                ]);
            }

            $followup->update($validated);
            $followup->issues()->delete();
            $followup->issues()->createMany($issues);

            $this->syncSemesterRisk((int) $followup->semester_record_id);
        });

        return redirect()
            ->route('university.semesters.show', $followup->semester_record_id)
            ->with('success', 'แก้ไขการติดตามครั้งที่ ' . $followup->sequence_no . ' เรียบร้อยแล้ว');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->requireUniversityPermission('delete');

        $followup = UniversityFollowup::query()
            ->whereHas('enrollment.client', fn ($query) => $query->forUser(auth()->user()))
            ->findOrFail($id);

        $semesterRecordId = $followup->semester_record_id;
        $enrollmentId = $followup->enrollment_id;

        DB::transaction(function () use ($followup, $semesterRecordId) {
            $followup->delete();

            if (!$semesterRecordId) {
                return;
            }

            UniversitySemesterRecord::query()
                ->whereKey($semesterRecordId)
                ->lockForUpdate()
                ->firstOrFail();

            $items = UniversityFollowup::query()
                ->where('semester_record_id', $semesterRecordId)
                ->orderBy('followup_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($items as $index => $item) {
                $expected = $index + 1;

                if ((int) $item->sequence_no !== $expected) {
                    $item->update(['sequence_no' => $expected]);
                }
            }

            $this->syncSemesterRisk((int) $semesterRecordId);
        });

        $redirectRoute = $semesterRecordId
            ? route('university.semesters.show', $semesterRecordId)
            : route('university.enrollments.show', $enrollmentId);

        return redirect($redirectRoute)
            ->with('success', 'ลบการติดตามและจัดลำดับครั้งใหม่เรียบร้อยแล้ว');
    }

    private function validatedPayload(Request $request): array
    {
        $riskLevels = array_keys(config('university_tracking.risk_levels', []));
        $categories = array_keys(config('university_tracking.issue_categories', []));
        $issueStatuses = array_keys(config('university_tracking.issue_statuses', []));
        $today = now('Asia/Bangkok')->toDateString();

        $validated = $request->validate([
            'followup_date' => ['required', 'date', 'before_or_equal:' . $today],
            'overall_risk_level' => ['required', Rule::in($riskLevels)],

            'academic_progress' => ['nullable', 'string', 'max:5000'],
            'adaptation_status' => ['nullable', 'string', 'max:5000'],
            'financial_status' => ['nullable', 'string', 'max:5000'],
            'wellbeing_motivation' => ['nullable', 'string', 'max:5000'],
            'continuation_risk_note' => ['nullable', 'string', 'max:5000'],
            'strengths' => ['nullable', 'string', 'max:5000'],
            'assistance_summary' => ['nullable', 'string', 'max:5000'],
            'next_plan' => ['nullable', 'string', 'max:5000'],

            'issues' => ['nullable', 'array', 'max:' . (int) config('university_tracking.max_followup_issues', 20)],
            'issues.*.category' => [
                'required_with:issues.*.detail,issues.*.assistance',
                'nullable',
                Rule::in($categories),
            ],
            'issues.*.severity' => ['nullable', Rule::in($riskLevels)],
            'issues.*.detail' => ['nullable', 'string', 'max:3000'],
            'issues.*.assistance' => ['nullable', 'string', 'max:3000'],
            'issues.*.issue_status' => ['nullable', Rule::in($issueStatuses)],
        ], [
            'followup_date.required' => 'กรุณาเลือกวันที่ติดตาม',
            'followup_date.before_or_equal' => 'วันที่ติดตามต้องไม่เกินวันปัจจุบัน',
        ]);

        $issues = collect($validated['issues'] ?? [])
            ->filter(
                fn ($row) => filled($row['detail'] ?? null)
                    || filled($row['assistance'] ?? null)
            )
            ->map(fn ($row) => [
                'category' => $row['category'] ?? 'other',
                'severity' => $row['severity'] ?? 'watch',
                'detail' => $row['detail'] ?? null,
                'assistance' => $row['assistance'] ?? null,
                'issue_status' => $row['issue_status'] ?? 'open',
            ])
            ->values()
            ->all();

        unset($validated['issues']);

        return [$validated, $issues];
    }

    private function formData(
        UniversitySemesterRecord $record,
        UniversityFollowup $followup,
        bool $isEdit
    ): array {
        $schoolFollowups = $record->education_record_id
            ? SchoolFollowup::query()
                ->where('client_id', $record->enrollment->client_id)
                ->where('education_record_id', $record->education_record_id)
                ->orderBy('follow_date')
                ->orderBy('id')
                ->get()
            : collect();

        $others = UniversityFollowup::query()
            ->where('semester_record_id', $record->id)
            ->when($isEdit, fn ($query) => $query->where('id', '!=', $followup->id))
            ->orderBy('sequence_no')
            ->get();

        $sequence = $isEdit
            ? (int) $followup->sequence_no
            : ((int) $others->max('sequence_no')) + 1;

        $minimumDate = null;

        if (!$isEdit && $others->isNotEmpty()) {
            $last = $others->sortByDesc('followup_date')->first();

            if ($last?->followup_date) {
                $minimumDate = $last->followup_date
                    ->copy()
                    ->addDay()
                    ->format('Y-m-d');
            }
        }

        return [
            'record' => $record,
            'enrollment' => $record->enrollment,
            'client' => $record->enrollment->client,
            'followup' => $followup,
            'isEdit' => $isEdit,
            'nextSequence' => $sequence,
            'minimumDate' => $minimumDate,
            'schoolFollowups' => $schoolFollowups,
            'existingUniversityFollowups' => $others,
            'universityPermissions' => $this->universityPermissionBag(),
        ];
    }

    private function syncSemesterRisk(int $semesterRecordId): void
    {
        $record = UniversitySemesterRecord::query()->find($semesterRecordId);

        if (!$record) {
            return;
        }

        $latest = UniversityFollowup::query()
            ->where('semester_record_id', $semesterRecordId)
            ->orderByDesc('sequence_no')
            ->first();

        $record->update([
            'risk_level' => $latest?->overall_risk_level ?? 'normal',
            'risk_note' => $latest?->continuation_risk_note,
        ]);
    }
}
