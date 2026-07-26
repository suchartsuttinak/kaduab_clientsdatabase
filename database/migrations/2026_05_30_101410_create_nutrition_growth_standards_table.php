<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nutrition_growth_standards', function (Blueprint $table) {
            $table->id();

            $table->enum('gender', ['male', 'female']);
            $table->unsignedSmallInteger('age_month')->nullable();
            $table->decimal('height_cm', 5, 2)->nullable();

            $table->enum('standard_type', [
                'height_for_age',
                'weight_for_height',
            ]);

            $table->decimal('sd_minus_3', 6, 2)->nullable();
            $table->decimal('sd_minus_2', 6, 2)->nullable();
            $table->decimal('sd_minus_1_5', 6, 2)->nullable();
            $table->decimal('median', 6, 2)->nullable();
            $table->decimal('sd_plus_1_5', 6, 2)->nullable();
            $table->decimal('sd_plus_2', 6, 2)->nullable();
            $table->decimal('sd_plus_3', 6, 2)->nullable();

            $table->timestamps();

            $table->index(['gender', 'age_month', 'standard_type']);
            $table->index(['gender', 'height_cm', 'standard_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutrition_growth_standards');
    }
};