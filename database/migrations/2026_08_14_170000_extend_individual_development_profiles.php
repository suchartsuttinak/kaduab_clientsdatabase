<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('individual_development_plans')) {
            Schema::table('individual_development_plans', function (Blueprint $table): void {
                if (!Schema::hasColumn('individual_development_plans', 'strength_profile')) {
                    $table->json('strength_profile')->nullable()->after('strength_summary');
                }
                if (!Schema::hasColumn('individual_development_plans', 'needs_profile')) {
                    $table->json('needs_profile')->nullable()->after('development_need_summary');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('individual_development_plans')) {
            Schema::table('individual_development_plans', function (Blueprint $table): void {
                foreach (['needs_profile', 'strength_profile'] as $column) {
                    if (Schema::hasColumn('individual_development_plans', $column)) $table->dropColumn($column);
                }
            });
        }
    }
};
