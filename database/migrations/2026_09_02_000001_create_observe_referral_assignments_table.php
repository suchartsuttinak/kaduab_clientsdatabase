<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('observe_referral_assignments')) {
            Schema::create('observe_referral_assignments', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('observe_id')->unique()->constrained('observes')->cascadeOnDelete();
                $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('assigned_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('assigned_at')->nullable();
                $table->dateTime('accepted_at')->nullable();
                $table->timestamps();

                $table->index(['assigned_to_user_id', 'accepted_at'], 'observe_referral_assignee_status_idx');
            });
        }

        // เพิ่มสิทธิ์เฉพาะกลุ่มที่ระบบเดิมอนุญาตให้ดูแลงานหลังส่งต่อ
        if (Schema::hasTable('users') && Schema::hasTable('user_form_permissions')) {
            $userIds = DB::table('users')
                ->whereIn('role', ['admin', 'executive', 'manager', 'social_worker'])
                ->pluck('id');

            foreach ($userIds as $userId) {
                $permissionValues = [
                    'can_view' => true,
                    'can_create' => true,
                    'can_update' => true,
                    'can_delete' => true,
                    'can_print' => true,
                ];

                if (Schema::hasColumn('user_form_permissions', 'updated_at')) {
                    $permissionValues['updated_at'] = now();
                }
                if (Schema::hasColumn('user_form_permissions', 'created_at')) {
                    $permissionValues['created_at'] = now();
                }

                DB::table('user_form_permissions')->updateOrInsert(
                    [
                        'user_id' => $userId,
                        'permission_key' => 'welfare_behavior_referral_center',
                    ],
                    $permissionValues
                );
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_form_permissions')) {
            DB::table('user_form_permissions')
                ->where('permission_key', 'welfare_behavior_referral_center')
                ->delete();
        }

        Schema::dropIfExists('observe_referral_assignments');
    }
};
