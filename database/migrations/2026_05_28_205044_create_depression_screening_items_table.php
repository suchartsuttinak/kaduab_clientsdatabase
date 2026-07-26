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
        Schema::create('depression_screening_items', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | ความสัมพันธ์
            |--------------------------------------------------------------------------
            */

            $table->foreignId('depression_screening_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | รายการคำถาม
            |--------------------------------------------------------------------------
            */

            $table->unsignedTinyInteger('item_no');

            $table->text('question');

            /*
            |--------------------------------------------------------------------------
            | คำตอบ
            |--------------------------------------------------------------------------
            | 0 = ไม่เลย
            | 1 = นานๆ ครั้ง
            | 2 = บ่อยๆ
            | 3 = ตลอดเวลา
            |--------------------------------------------------------------------------
            */

            $table->unsignedTinyInteger('score')
                ->default(0);

            $table->string('choice_text')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | ข้อกลับคะแนน
            |--------------------------------------------------------------------------
            | ใช้สำหรับข้อเชิงบวก เช่น
            | ฉันมีความสุข
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_reverse')
                ->default(false);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depression_screening_items');
    }
};