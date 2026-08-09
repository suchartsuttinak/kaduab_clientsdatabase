<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Production safety: ห้ามลบข้อมูลเดิมเมื่อ migration นี้ยังไม่เคยถูกบันทึก
        if (Schema::hasTable('school_followups')) {
            return;
        }

        Schema::create('school_followups', function (Blueprint $table) {
            $table->id();
            $table->date('follow_date');
            $table->string('teacher_name')->nullable();
            $table->string('tel')->nullable();
            $table->string('follow_type'); // self, phone, other
            $table->tinyInteger('follo_no')->nullable();
            $table->text('result')->nullable();
            $table->string('contact_name')->nullable();
            $table->text('remark')->nullable();

            // ✅ Foreign keys
            $table->foreignId('client_id')
                  ->constrained('clients')
                  ->onDelete('cascade');

            $table->foreignId('education_record_id')
                  ->nullable()
                  ->constrained('education_records')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_followups');
    }
};

