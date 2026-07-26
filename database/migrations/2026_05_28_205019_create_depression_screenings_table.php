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
        Schema::create('depression_screenings', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | ความสัมพันธ์
            |--------------------------------------------------------------------------
            */

            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | ข้อมูลการประเมิน
            |--------------------------------------------------------------------------
            */

            $table->date('screening_date');

            $table->string('observer_name')
                ->nullable();

            $table->string('age_text')
                ->nullable();

            $table->string('class_level')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | คะแนนรวม
            |--------------------------------------------------------------------------
            */

            $table->unsignedTinyInteger('total_score')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | ผลการประเมิน
            |--------------------------------------------------------------------------
            */

            $table->string('result_level')
                ->nullable();

            $table->text('summary')
                ->nullable();

            $table->text('recommendation')
                ->nullable();

            $table->text('remark')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | ป้องกันบันทึกซ้ำวันเดียวกัน
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'client_id',
                'screening_date'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depression_screenings');
    }
};