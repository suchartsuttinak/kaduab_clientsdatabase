<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * เพิ่มสถานะการพิจารณาทุนในตารางผู้ขอรับทุน
     */
    public function up(): void
    {
        Schema::table('scholarship_children', function (Blueprint $table) {
            $table->string('scholarship_status', 20)
                ->default('pending')
                ->after('academic_year')
                ->index();

            $table->timestamp('scholarship_status_updated_at')
                ->nullable()
                ->after('scholarship_status');
        });
    }

    /**
     * ย้อนกลับการเปลี่ยนแปลง
     */
    public function down(): void
    {
        Schema::table('scholarship_children', function (Blueprint $table) {
            $table->dropIndex(['scholarship_status']);

            $table->dropColumn([
                'scholarship_status',
                'scholarship_status_updated_at',
            ]);
        });
    }
};