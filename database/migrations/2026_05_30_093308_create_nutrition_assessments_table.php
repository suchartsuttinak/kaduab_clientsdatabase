<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nutrition_assessments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            $table->date('assessment_date')->comment('วันที่ชั่งวัด');
            $table->date('birth_date')->nullable()->comment('วันเกิด');

            $table->unsignedTinyInteger('age_year')->nullable();
            $table->unsignedTinyInteger('age_month')->nullable();

            $table->enum('gender', ['male', 'female']);

            $table->decimal('height_cm', 5, 2)->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();

            $table->decimal('ibw', 6, 2)->nullable();
            $table->decimal('ibw_percent', 6, 2)->nullable();

            $table->decimal('ha_median', 6, 2)->nullable();
            $table->decimal('ha_percent', 6, 2)->nullable();

            $table->string('height_result')->nullable();
            $table->string('weight_result')->nullable();
            $table->string('summary_result')->nullable();

            $table->text('note')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['client_id', 'assessment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutrition_assessments');
    }
};