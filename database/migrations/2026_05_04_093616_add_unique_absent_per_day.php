<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('absents')) {
            return;
        }

        // ป้องกันการสร้าง UNIQUE ซ้ำ
        // รองรับทั้ง MySQL/MariaDB และ SQLite ที่ใช้ใน PHPUnit Test
        if (!Schema::hasIndex('absents', ['client_id', 'absent_date'], 'unique')) {
            Schema::table('absents', function (Blueprint $table) {
                $table->unique(
                    ['client_id', 'absent_date'],
                    'absents_client_absent_date_unique'
                );
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('absents')) {
            return;
        }

        if (Schema::hasIndex(
            'absents',
            'absents_client_absent_date_unique',
            'unique'
        )) {
            Schema::table('absents', function (Blueprint $table) {
                $table->dropUnique('absents_client_absent_date_unique');
            });
        }
    }
};