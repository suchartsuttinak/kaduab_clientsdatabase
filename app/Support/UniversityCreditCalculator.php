<?php

namespace App\Support;

use App\Models\UniversitySemesterRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class UniversityCreditCalculator
{
    private const EARNED_STATUSES = [
        'pass',
        'satisfactory',
    ];

    private const NOT_EARNED_STATUSES = [
        'fail',
        'withdrawn',
        'incomplete',
        'unsatisfactory',
        'audit',
        'pending',
        'other',
    ];

    private const EARNED_GRADES = [
        'A+', 'A', 'A-',
        'B+', 'B', 'B-',
        'C+', 'C', 'C-',
        'D+', 'D', 'D-',
        'S', 'P', 'PASS',
    ];

    private const NOT_EARNED_GRADES = [
        'F', 'W', 'I', 'U', 'NP', 'N', 'AUDIT',
    ];

    /**
     * เครดิตของภาคเรียนปัจจุบัน
     * registered = วิชาที่มีจำนวนหน่วยกิตทั้งหมด
     * earned     = วิชาที่ผ่านแล้วเท่านั้น
     */
    public static function semesterSummary(UniversitySemesterRecord $record): array
    {
        $subjects = self::subjectsFor($record);

        $registeredCourses = [];
        $earnedCourses = [];
        $hasCreditRows = false;

        foreach ($subjects as $subject) {
            $credits = self::creditsOf($subject);

            if ($credits === null) {
                continue;
            }

            $hasCreditRows = true;
            $key = self::courseKey($subject);

            // ป้องกันรายการวิชาเดียวกันซ้ำในภาคเรียนเดียวกัน
            $registeredCourses[$key] = max($registeredCourses[$key] ?? 0.0, $credits);

            if (self::isEarned($subject)) {
                $earnedCourses[$key] = max($earnedCourses[$key] ?? 0.0, $credits);
            }
        }

        return [
            'has_credit_rows' => $hasCreditRows,
            'registered_credits' => $hasCreditRows ? array_sum($registeredCourses) : null,
            'earned_credits' => $hasCreditRows ? array_sum($earnedCourses) : null,
        ];
    }

    /**
     * หน่วยกิตที่ผ่านสะสมถึงภาคเรียนของ record
     * - ใช้เฉพาะวิชาที่ผ่าน
     * - ถ้าเรียนวิชาเดิมซ้ำ จะนับหน่วยกิตสะสมเพียงครั้งเดียว
     */
    public static function cumulativeEarnedCredits(UniversitySemesterRecord $record): ?float
    {
        if (!$record->enrollment_id || !$record->academic_year || !$record->term) {
            return null;
        }

        $records = self::recordsThroughPeriod(
            (int) $record->enrollment_id,
            (int) $record->academic_year,
            (int) $record->term
        );

        $passedCourses = [];
        $hasCreditHistory = false;

        foreach ($records as $semester) {
            foreach ($semester->subjects as $subject) {
                $credits = self::creditsOf($subject);

                if ($credits === null) {
                    continue;
                }

                $hasCreditHistory = true;

                if (!self::isEarned($subject)) {
                    continue;
                }

                $key = self::courseKey($subject);
                $passedCourses[$key] = max($passedCourses[$key] ?? 0.0, $credits);
            }
        }

        return $hasCreditHistory ? array_sum($passedCourses) : null;
    }

    /**
     * Sync ค่าที่คำนวณได้กลับลงคอลัมน์เดิมหลังผู้ใช้กดบันทึกภาคเรียน
     * ไม่แตะ GPA/GPAX และไม่ลบข้อมูลใด ๆ
     */
    public static function syncEnrollment(int $enrollmentId): void
    {
        $records = UniversitySemesterRecord::query()
            ->where('enrollment_id', $enrollmentId)
            ->with(['subjects:id,semester_record_id,course_code,course_name,credits,grade,grade_point,result_status'])
            ->orderBy('academic_year')
            ->orderBy('term')
            ->orderBy('id')
            ->get();

        $passedCourses = [];
        $hasCreditHistory = false;

        foreach ($records as $record) {
            $summary = self::semesterSummary($record);
            $updates = [];

            if ($summary['has_credit_rows']) {
                $updates['registered_credits'] = self::roundCredit($summary['registered_credits']);
                $updates['earned_credits'] = self::roundCredit($summary['earned_credits']);
                $hasCreditHistory = true;
            }

            foreach ($record->subjects as $subject) {
                $credits = self::creditsOf($subject);

                if ($credits === null) {
                    continue;
                }

                $hasCreditHistory = true;

                if (!self::isEarned($subject)) {
                    continue;
                }

                $key = self::courseKey($subject);
                $passedCourses[$key] = max($passedCourses[$key] ?? 0.0, $credits);
            }

            if ($hasCreditHistory) {
                $updates['cumulative_credits'] = self::roundCredit(array_sum($passedCourses));
            }

            if ($updates !== []) {
                DB::table('university_semester_records')
                    ->where('id', $record->id)
                    ->update($updates);
            }
        }
    }

    public static function isEarned(object $subject): bool
    {
        $status = strtolower(trim((string) ($subject->result_status ?? '')));

        if (in_array($status, self::EARNED_STATUSES, true)) {
            return true;
        }

        if (in_array($status, self::NOT_EARNED_STATUSES, true)) {
            return false;
        }

        $grade = strtoupper(trim((string) ($subject->grade ?? '')));

        if ($grade !== '' && in_array($grade, self::EARNED_GRADES, true)) {
            return true;
        }

        if ($grade !== '' && in_array($grade, self::NOT_EARNED_GRADES, true)) {
            return false;
        }

        $gradePoint = $subject->grade_point ?? null;

        // Legacy data บางรายการไม่มี result_status แต่มี grade point
        return $gradePoint !== null
            && $gradePoint !== ''
            && (float) $gradePoint > 0;
    }

    private static function recordsThroughPeriod(
        int $enrollmentId,
        int $academicYear,
        int $term
    ): Collection {
        return UniversitySemesterRecord::query()
            ->where('enrollment_id', $enrollmentId)
            ->where(function ($query) use ($academicYear, $term) {
                $query->where('academic_year', '<', $academicYear)
                    ->orWhere(function ($sameYear) use ($academicYear, $term) {
                        $sameYear->where('academic_year', $academicYear)
                            ->where('term', '<=', $term);
                    });
            })
            ->with(['subjects:id,semester_record_id,course_code,course_name,credits,grade,grade_point,result_status'])
            ->orderBy('academic_year')
            ->orderBy('term')
            ->orderBy('id')
            ->get();
    }

    private static function subjectsFor(UniversitySemesterRecord $record): Collection
    {
        return $record->relationLoaded('subjects')
            ? $record->subjects
            : $record->subjects()
                ->get(['id', 'semester_record_id', 'course_code', 'course_name', 'credits', 'grade', 'grade_point', 'result_status']);
    }

    private static function creditsOf(object $subject): ?float
    {
        $value = $subject->credits ?? null;

        if ($value === null || $value === '' || !is_numeric($value)) {
            return null;
        }

        $credits = (float) $value;

        return $credits > 0 ? $credits : null;
    }

    private static function courseKey(object $subject): string
    {
        $code = trim((string) ($subject->course_code ?? ''));

        if ($code !== '') {
            $code = strtoupper((string) preg_replace('/\s+/u', '', $code));
            return 'code:' . $code;
        }

        $name = trim((string) ($subject->course_name ?? ''));

        if ($name !== '') {
            $name = (string) preg_replace('/\s+/u', ' ', $name);
            $name = function_exists('mb_strtolower')
                ? mb_strtolower($name, 'UTF-8')
                : strtolower($name);

            $creditKey = self::creditsOf($subject);
            $creditText = $creditKey === null
                ? 'na'
                : number_format($creditKey, 2, '.', '');

            return 'name:' . $name . '|credits:' . $creditText;
        }

        return 'subject:' . (string) ($subject->id ?? spl_object_id($subject));
    }

    private static function roundCredit(?float $value): ?float
    {
        return $value === null ? null : round($value, 2);
    }
}