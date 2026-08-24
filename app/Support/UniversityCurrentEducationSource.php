<?php

namespace App\Support;

use App\Models\EducationRecord;

class UniversityCurrentEducationSource
{
    public static function allowedBachelorLevels(): array
    {
        return [
            'ปริญญาตรีชั้นปีที่ 1',
            'ปริญญาตรีชั้นปีที่ 2',
            'ปริญญาตรีชั้นปีที่ 3',
            'ปริญญาตรีชั้นปีที่ 4',
        ];
    }

    public static function latestForClient(int $clientId): ?EducationRecord
    {
        return EducationRecord::query()
            ->with(['education', 'semester', 'institution'])
            ->leftJoin('semesters', 'education_records.semester_id', '=', 'semesters.id')
            ->where('education_records.client_id', $clientId)
            ->whereHas('education', function ($query) {
                $query->whereIn(
                    'education_name',
                    self::allowedBachelorLevels()
                );
            })
            ->select('education_records.*', 'semesters.semester_name as semester_label')
            ->orderByRaw("
                CAST(SUBSTRING_INDEX(semesters.semester_name, '/', -1) AS UNSIGNED) DESC,
                CAST(SUBSTRING_INDEX(semesters.semester_name, '/', 1) AS UNSIGNED) DESC
            ")
            ->orderByDesc('education_records.record_date')
            ->orderByDesc('education_records.id')
            ->first();
    }

    /**
     * คืนชื่อภาคเรียนจาก Education Record
     * เช่น 1/2569
     *
     * รองรับทั้ง relation semester และ semester_label
     * เพื่อ compatibility กับ Controller/Report เดิม
     */
    public static function semesterName(?EducationRecord $record): ?string
    {
        if (!$record) {
            return null;
        }

        // 1) Relation ปกติ
        try {
            $record->loadMissing('semester');

            $relationName = data_get($record, 'semester.semester_name');

            if (is_string($relationName) && trim($relationName) !== '') {
                return trim($relationName);
            }
        } catch (\Throwable $e) {
            // fallback ด้านล่าง
        }

        // 2) Alias จาก query ที่ join semesters
        $semesterLabel = data_get($record, 'semester_label');

        if (is_string($semesterLabel) && trim($semesterLabel) !== '') {
            return trim($semesterLabel);
        }

        // 3) อ่านจากตาราง semesters โดยตรง
        // ใช้ semester_id เป็น source ที่แน่นอนสำหรับ Education Record เดิม
        $semesterId = (int) ($record->semester_id ?? 0);

        if ($semesterId <= 0) {
            return null;
        }

        $directName = \Illuminate\Support\Facades\DB::table('semesters')
            ->where('id', $semesterId)
            ->value('semester_name');

        if (!is_string($directName)) {
            return null;
        }

        $directName = trim($directName);

        return $directName !== '' ? $directName : null;
    }
    /**
     * แปลงระดับปริญญาตรีเป็นเลขชั้นปี 1-4
     * ใช้เป็น compatibility helper สำหรับหน้าภาคเรียน
     */
    public static function yearLevel(?EducationRecord $record): ?int
    {
        if (!$record) {
            return null;
        }

        $record->loadMissing('education');

        $educationName = trim((string) data_get($record, 'education.education_name'));

        if (preg_match('/ปริญญาตรีชั้นปีที่\s*([1-4])/u', $educationName, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * คืนปีการศึกษา พ.ศ. จากชื่อภาคเรียน เช่น 1/2569 -> 2569
     */
    public static function academicYear(?EducationRecord $record): ?int
    {
        $semesterName = self::semesterName($record);

        if (!$semesterName) {
            return null;
        }

        if (preg_match('/^\s*\d+\s*\/\s*(\d{4})\s*$/u', $semesterName, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * คืนเลขภาคเรียนจากชื่อ เช่น 1/2569 -> 1
     */
    public static function term(?EducationRecord $record): ?int
    {
        $semesterName = self::semesterName($record);

        if (!$semesterName) {
            return null;
        }

        if (preg_match('/^\s*(\d+)\s*\/\s*\d{4}\s*$/u', $semesterName, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
    public static function isEligible(?EducationRecord $record): bool
    {
        if (!$record) {
            return false;
        }

        $record->loadMissing('education');

        return in_array(
            (string) data_get($record, 'education.education_name'),
            self::allowedBachelorLevels(),
            true
        );
    }
}
