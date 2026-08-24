<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('university_subject_results') && Schema::hasColumn('university_subject_results', 'result_status')) {
            DB::statement("ALTER TABLE `university_subject_results` MODIFY `result_status` VARCHAR(30) NULL DEFAULT NULL");
        }
    }

    public function down(): void
    {
        // ไม่บังคับกลับเป็น NOT NULL เพราะระหว่างใช้งาน V3.2 อาจมีรายวิชาที่ยังไม่มีผลและเก็บค่า NULL อย่างถูกต้อง
    }
};
