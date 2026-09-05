<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('project_user')) {
            Schema::create('project_user', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('project_id');
                $table->timestamps();

                $table->unique(['user_id', 'project_id'], 'project_user_unique');
                $table->index('project_id', 'project_user_project_idx');
            });
        }

        // USER_MULTI_PROJECT_SCOPE_V5
        // ย้ายค่า project_id เดิมเข้าสู่ pivot เพื่อคงสิทธิ์ของบัญชีเก่าหลังอัปเกรด
        if (
            Schema::hasTable('users')
            && Schema::hasColumn('users', 'project_id')
            && Schema::hasTable('project_user')
        ) {
            DB::table('users')
                ->select(['id', 'project_id'])
                ->whereNotNull('project_id')
                ->where('project_id', '>', 0)
                ->orderBy('id')
                ->chunkById(500, function ($users): void {
                    $now = now();
                    $rows = [];

                    foreach ($users as $user) {
                        $rows[] = [
                            'user_id' => (int) $user->id,
                            'project_id' => (int) $user->project_id,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    if ($rows !== []) {
                        DB::table('project_user')->insertOrIgnore($rows);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_user');
    }
};
