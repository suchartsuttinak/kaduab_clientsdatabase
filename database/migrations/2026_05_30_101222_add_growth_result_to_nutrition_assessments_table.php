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
        Schema::table('nutrition_assessments', function (Blueprint $table) {

            $table->string('height_for_age_result')
                ->nullable()
                ->after('nutrition_status');

            $table->string('weight_for_height_result')
                ->nullable()
                ->after('height_for_age_result');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nutrition_assessments', function (Blueprint $table) {
            //
        });
    }
};
