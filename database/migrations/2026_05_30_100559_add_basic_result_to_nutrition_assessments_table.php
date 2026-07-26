<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nutrition_assessments', function (Blueprint $table) {
            $table->decimal('bmi', 6, 2)->nullable()->after('weight_kg');
            $table->string('bmi_result')->nullable()->after('bmi');
            $table->string('nutrition_status')->nullable()->after('summary_result');
        });
    }

    public function down(): void
    {
        Schema::table('nutrition_assessments', function (Blueprint $table) {
            $table->dropColumn([
                'bmi',
                'bmi_result',
                'nutrition_status',
            ]);
        });
    }
};