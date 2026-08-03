<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'form_permissions_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('form_permissions_enabled')
                    ->default(false)
                    ->after('status')
                    ->comment('เปิดใช้สิทธิ์รายฟอร์ม; false = ใช้ระบบบทบาทเดิม');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'form_permissions_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('form_permissions_enabled');
            });
        }
    }
};
