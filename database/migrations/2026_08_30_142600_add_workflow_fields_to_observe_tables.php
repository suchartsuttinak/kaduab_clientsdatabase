<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('observes')) {
            Schema::table('observes', function (Blueprint $table) {
                if (!Schema::hasColumn('observes', 'risk_level')) {
                    $table->string('risk_level', 20)->default('none');
                }
                if (!Schema::hasColumn('observes', 'risk_detail')) {
                    $table->text('risk_detail')->nullable();
                }
                if (!Schema::hasColumn('observes', 'status')) {
                    $table->string('status', 30)->default('ongoing');
                }
                if (!Schema::hasColumn('observes', 'next_appointment_date')) {
                    $table->date('next_appointment_date')->nullable();
                }
                if (!Schema::hasColumn('observes', 'followup_focus')) {
                    $table->text('followup_focus')->nullable();
                }
                if (!Schema::hasColumn('observes', 'referral_process')) {
                    $table->string('referral_process', 60)->nullable();
                }
                if (!Schema::hasColumn('observes', 'referral_solution')) {
                    $table->text('referral_solution')->nullable();
                }
                if (!Schema::hasColumn('observes', 'referral_result')) {
                    $table->text('referral_result')->nullable();
                }
            });
        }

        if (Schema::hasTable('observe_followups')) {
            Schema::table('observe_followups', function (Blueprint $table) {
                if (!Schema::hasColumn('observe_followups', 'risk_level')) {
                    $table->string('risk_level', 20)->default('none');
                }
                if (!Schema::hasColumn('observe_followups', 'risk_detail')) {
                    $table->text('risk_detail')->nullable();
                }
                if (!Schema::hasColumn('observe_followups', 'status')) {
                    $table->string('status', 30)->default('ongoing');
                }
                if (!Schema::hasColumn('observe_followups', 'next_appointment_date')) {
                    $table->date('next_appointment_date')->nullable();
                }
                if (!Schema::hasColumn('observe_followups', 'followup_focus')) {
                    $table->text('followup_focus')->nullable();
                }
                if (!Schema::hasColumn('observe_followups', 'referral_process')) {
                    $table->string('referral_process', 60)->nullable();
                }
                if (!Schema::hasColumn('observe_followups', 'referral_solution')) {
                    $table->text('referral_solution')->nullable();
                }
                if (!Schema::hasColumn('observe_followups', 'referral_result')) {
                    $table->text('referral_result')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('observe_followups')) {
            $columns = [
                'risk_level',
                'risk_detail',
                'status',
                'next_appointment_date',
                'followup_focus',
                'referral_process',
                'referral_solution',
                'referral_result',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('observe_followups', $column)) {
                    Schema::table('observe_followups', function (Blueprint $table) use ($column) {
                        $table->dropColumn($column);
                    });
                }
            }
        }

        if (Schema::hasTable('observes')) {
            $columns = [
                'risk_level',
                'risk_detail',
                'status',
                'next_appointment_date',
                'followup_focus',
                'referral_process',
                'referral_solution',
                'referral_result',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('observes', $column)) {
                    Schema::table('observes', function (Blueprint $table) use ($column) {
                        $table->dropColumn($column);
                    });
                }
            }
        }
    }
};
