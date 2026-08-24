<?php

namespace App\Http\Controllers\Frontend\University;

use App\Models\Semester;
use App\Models\UniversityOutcome;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UniversityOutcomeController extends UniversityBaseController
{
    public function form(int $enrollmentId): View
    {
        $enrollment = $this->scopedEnrollment($enrollmentId);
        $outcome = UniversityOutcome::query()->with('reasons')->where('enrollment_id', $enrollment->id)->first();
        $this->requireUniversityPermission($outcome ? 'update' : 'create');

        $semesters = Semester::query()
            ->orderByRaw("CAST(SUBSTRING_INDEX(semester_name, '/', -1) AS UNSIGNED) DESC")
            ->orderByRaw("CAST(SUBSTRING_INDEX(semester_name, '/', 1) AS UNSIGNED) DESC")
            ->get(['id', 'semester_name']);

        return view('university.outcomes.form', [
            'enrollment' => $enrollment,
            'client' => $enrollment->client,
            'outcome' => $outcome ?? new UniversityOutcome(),
            'semesters' => $semesters,
            'isEdit' => (bool) $outcome,
            'universityPermissions' => $this->universityPermissionBag(),
        ]);
    }

    public function save(Request $request, int $enrollmentId): RedirectResponse
    {
        $enrollment = $this->scopedEnrollment($enrollmentId);
        $existing = UniversityOutcome::query()->where('enrollment_id', $enrollment->id)->first();
        $this->requireUniversityPermission($existing ? 'update' : 'create');

        $outcomeTypes = array_keys(config('university_tracking.outcome_types', []));
        $reasonCodes = array_keys(config('university_tracking.outcome_reasons', []));
        $postStatuses = array_keys(config('university_tracking.post_graduation_statuses', []));

        $validated = $request->validate([
            'outcome_type' => ['required', Rule::in($outcomeTypes)],
            'outcome_date' => ['nullable', 'date', 'before_or_equal:' . now('Asia/Bangkok')->toDateString()],
            'academic_year' => ['required', 'integer', 'between:2400,2800'],
            'semester_id' => ['nullable', 'integer', Rule::exists('semesters', 'id')],
            'final_gpa' => ['nullable', 'numeric', 'between:0,4', 'decimal:0,2'],
            'degree_name' => ['nullable', 'string', 'max:255'],
            'honors' => ['nullable', 'string', 'max:255'],
            'post_graduation_status' => ['nullable', Rule::in($postStatuses)],
            'post_graduation_detail' => ['nullable', 'string', 'max:5000'],
            'summary' => ['nullable', 'string', 'max:5000'],
            'reasons' => ['nullable', 'array', 'max:10'],
            'reasons.*.reason_code' => ['required', Rule::in($reasonCodes)],
            'reasons.*.is_primary' => ['nullable', 'boolean'],
            'reasons.*.detail' => ['nullable', 'string', 'max:3000'],
        ]);

        $reasons = $validated['reasons'] ?? [];
        if ($validated['outcome_type'] !== 'graduated' && count($reasons) === 0) {
            throw ValidationException::withMessages([
                'reasons' => 'กรุณาระบุสาเหตุอย่างน้อย 1 ข้อ เพื่อใช้ในการติดตามและสรุปสถิติ',
            ]);
        }
        unset($validated['reasons']);

        DB::transaction(function () use ($existing, $validated, $reasons, $enrollment) {
            $outcome = $existing ?: new UniversityOutcome(['enrollment_id' => $enrollment->id]);
            $outcome->fill($validated);
            $outcome->save();
            $outcome->reasons()->delete();

            if (in_array($validated['outcome_type'], ['dropout', 'dismissed', 'transferred', 'other'], true)) {
                $primaryAssigned = false;
                foreach ($reasons as $row) {
                    $isPrimary = !$primaryAssigned && !empty($row['is_primary']);
                    $primaryAssigned = $primaryAssigned || $isPrimary;
                    $outcome->reasons()->create([
                        'reason_code' => $row['reason_code'],
                        'is_primary' => $isPrimary,
                        'detail' => $row['detail'] ?? null,
                    ]);
                }
                if (!$primaryAssigned && $outcome->reasons()->exists()) {
                    $first = $outcome->reasons()->oldest('id')->first();
                    $first?->update(['is_primary' => true]);
                }
            }

            $statusMap = [
                'graduated' => 'graduated',
                'dropout' => 'dropout',
                'dismissed' => 'dismissed',
                'transferred' => 'transferred',
                'other' => 'other',
            ];
            $enrollment->update(['current_status' => $statusMap[$validated['outcome_type']] ?? $enrollment->current_status]);
        });

        return redirect()->route('university.enrollments.show', $enrollment)
            ->with('success', 'บันทึกผลสิ้นสุด/สถานะการศึกษาเรียบร้อยแล้ว');
    }

    public function destroy(int $enrollmentId): RedirectResponse
    {
        $this->requireUniversityPermission('delete');
        $enrollment = $this->scopedEnrollment($enrollmentId);
        UniversityOutcome::query()->where('enrollment_id', $enrollment->id)->delete();
        $enrollment->update(['current_status' => 'studying']);

        return redirect()->route('university.enrollments.show', $enrollment)
            ->with('success', 'ลบผลสิ้นสุดการศึกษาเรียบร้อยแล้ว');
    }
}
