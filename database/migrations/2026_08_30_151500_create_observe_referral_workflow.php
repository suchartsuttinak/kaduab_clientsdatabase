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
            });
        }

        if (!Schema::hasTable('observe_referral_rounds')) {
            Schema::create('observe_referral_rounds', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('observe_id');
                $table->unsignedInteger('round_no');
                $table->date('action_date');
                $table->string('assistance_process', 60);
                $table->text('solution');
                $table->text('result');
                $table->string('risk_level', 20)->default('none');
                $table->text('risk_detail')->nullable();
                $table->string('status', 30)->default('ongoing');
                $table->date('next_appointment_date')->nullable();
                $table->text('followup_focus')->nullable();
                $table->unsignedBigInteger('recorder_user_id')->nullable();
                $table->string('recorder_name', 150)->nullable();
                $table->timestamps();

                $table->unique(['observe_id', 'round_no'], 'observe_referral_round_unique');
                $table->unique(['observe_id', 'action_date'], 'observe_referral_date_unique');

                $table->foreign('observe_id')
                    ->references('id')->on('observes')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('observe_referral_rounds');

        // ไม่ลบคอลัมน์ lifecycle จาก observes/observe_followups ใน down()
        // เพื่อหลีกเลี่ยงการทำข้อมูลสถานะเดิมสูญหายโดยไม่ตั้งใจ
    }
};
