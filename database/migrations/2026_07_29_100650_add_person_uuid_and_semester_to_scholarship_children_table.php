<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scholarship_children', function (Blueprint $table) {
            $table->string('person_uuid', 36)
                ->nullable()
                ->after('id')
                ->comment('รหัสเชื่อมคำขอทุกปี/ภาคเรียนของบุคคลเดียวกัน');

            $table->unsignedTinyInteger('semester')
                ->nullable()
                ->after('academic_year')
                ->comment('ภาคเรียนที่ยื่นขอทุน 1 หรือ 2');
        });

        /*
         * ข้อมูลเก่าแต่ละแถวจะเริ่มด้วย person_uuid ของตนเองก่อน
         * ภาคเรียนจะดึงจากรายการค่าใช้จ่ายล่าสุด ถ้าไม่พบให้ใช้ภาคเรียนที่ 1
         * หลังจากติดตั้ง คำขอรอบใหม่ต้องสร้างผ่านปุ่ม “ยื่นคำขอใหม่”
         * เพื่อให้ใช้ person_uuid เดียวกับบุคคลเดิม
         */
        DB::table('scholarship_children')
            ->orderBy('id')
            ->chunkById(100, function ($children) {
                foreach ($children as $child) {
                    $semester = Schema::hasTable('scholarship_expenses')
                        ? DB::table('scholarship_expenses')
                            ->where('scholarship_child_id', $child->id)
                            ->whereIn('semester', [1, 2])
                            ->orderByDesc('record_date')
                            ->orderByDesc('id')
                            ->value('semester')
                        : null;

                    DB::table('scholarship_children')
                        ->where('id', $child->id)
                        ->update([
                            'person_uuid' => Str::uuid()->toString(),
                            'semester' => (int) ($semester ?: 1),
                        ]);
                }
            });

        Schema::table('scholarship_children', function (Blueprint $table) {
            $table->index(
                'person_uuid',
                'scholarship_children_person_uuid_index'
            );

            $table->index(
                ['academic_year', 'semester'],
                'scholarship_children_period_index'
            );

            $table->unique(
                ['person_uuid', 'academic_year', 'semester'],
                'scholarship_children_person_period_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('scholarship_children', function (Blueprint $table) {
            $table->dropUnique('scholarship_children_person_period_unique');
            $table->dropIndex('scholarship_children_period_index');
            $table->dropIndex('scholarship_children_person_uuid_index');
            $table->dropColumn(['person_uuid', 'semester']);
        });
    }
};