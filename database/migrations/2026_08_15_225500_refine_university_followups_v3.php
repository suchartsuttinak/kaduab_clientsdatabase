<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function hasIndex(string $table, string $name): bool
    {
        return collect(DB::select("SHOW INDEX FROM `{$table}`"))
            ->contains(fn ($row) => (string) ($row->Key_name ?? '') === $name);
    }

    public function up(): void
    {
        if (!Schema::hasTable('university_followups')) {
            return;
        }

        if (!Schema::hasColumn('university_followups', 'sequence_no')) {
            Schema::table('university_followups', function (Blueprint $table) {
                $table->unsignedSmallInteger('sequence_no')->nullable()->after('academic_year');
            });
        }

        $assessmentColumns = [
            'academic_progress' => 'overall_risk_level',
            'adaptation_status' => 'academic_progress',
            'financial_status' => 'adaptation_status',
            'wellbeing_motivation' => 'financial_status',
            'continuation_risk_note' => 'wellbeing_motivation',
        ];

        foreach ($assessmentColumns as $column => $after) {
            if (!Schema::hasColumn('university_followups', $column)) {
                Schema::table('university_followups', function (Blueprint $table) use ($column, $after) {
                    $table->text($column)->nullable()->after($after);
                });
            }
        }

        // หน้าเด็กมหาวิทยาลัยไม่เก็บวิธีติดต่อ/ผู้ติดต่อซ้ำกับ School Followup
        // จึงอนุญาต followup_method เป็น NULL สำหรับรายการรูปแบบใหม่
        DB::statement(
            "ALTER TABLE `university_followups`
             MODIFY `followup_method` VARCHAR(40) NULL"
        );

        $groups = DB::table('university_followups')
            ->whereNotNull('semester_record_id')
            ->distinct()
            ->pluck('semester_record_id');

        foreach ($groups as $semesterRecordId) {
            $items = DB::table('university_followups')
                ->where('semester_record_id', $semesterRecordId)
                ->orderBy('followup_date')
                ->orderBy('id')
                ->get(['id', 'sequence_no']);

            foreach ($items as $index => $item) {
                $expected = $index + 1;

                if ((int) ($item->sequence_no ?? 0) !== $expected) {
                    DB::table('university_followups')
                        ->where('id', $item->id)
                        ->update(['sequence_no' => $expected]);
                }
            }
        }

        if (!$this->hasIndex('university_followups', 'uniq_uni_follow_sem_seq')) {
            Schema::table('university_followups', function (Blueprint $table) {
                $table->unique(
                    ['semester_record_id', 'sequence_no'],
                    'uniq_uni_follow_sem_seq'
                );
            });
        }

        // V2 เคยบังคับ unique วันที่ติดตามต่อภาคเรียนในระดับฐานข้อมูล
        // รุ่นนี้ให้ Controller ตรวจแทน เพื่อรองรับข้อมูลเดิมได้ปลอดภัยกว่า
        if ($this->hasIndex('university_followups', 'uniq_uni_follow_sem_date')) {
            Schema::table('university_followups', function (Blueprint $table) {
                $table->dropUnique('uniq_uni_follow_sem_date');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('university_followups')) {
            return;
        }

        if ($this->hasIndex('university_followups', 'uniq_uni_follow_sem_seq')) {
            Schema::table('university_followups', function (Blueprint $table) {
                $table->dropUnique('uniq_uni_follow_sem_seq');
            });
        }

        foreach ([
            'sequence_no',
            'academic_progress',
            'adaptation_status',
            'financial_status',
            'wellbeing_motivation',
            'continuation_risk_note',
        ] as $column) {
            if (Schema::hasColumn('university_followups', $column)) {
                Schema::table('university_followups', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        // ไม่บังคับ followup_method กลับเป็น NOT NULL ใน down()
        // เพราะอาจมีข้อมูลรูปแบบใหม่ที่ค่าเป็น NULL อยู่แล้ว
    }
};
