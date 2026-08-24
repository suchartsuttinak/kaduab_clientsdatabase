<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('individual_development_goals')) {
            Schema::table('individual_development_goals', function (Blueprint $table): void {
                if (!Schema::hasColumn('individual_development_goals', 'achieved_at')) {
                    $table->timestamp('achieved_at')->nullable()->after('status');
                }
                if (!Schema::hasColumn('individual_development_goals', 'achieved_by')) {
                    $table->unsignedBigInteger('achieved_by')->nullable()->index()->after('achieved_at');
                }
                if (!Schema::hasColumn('individual_development_goals', 'cancel_reason')) {
                    $table->text('cancel_reason')->nullable()->after('achieved_by');
                }
                if (!Schema::hasColumn('individual_development_goals', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable()->after('cancel_reason');
                }
                if (!Schema::hasColumn('individual_development_goals', 'cancelled_by')) {
                    $table->unsignedBigInteger('cancelled_by')->nullable()->index()->after('cancelled_at');
                }
                if (!Schema::hasColumn('individual_development_goals', 'status_note')) {
                    $table->text('status_note')->nullable()->after('cancelled_by');
                }
            });
        }

        if (Schema::hasTable('individual_development_activities')) {
            Schema::table('individual_development_activities', function (Blueprint $table): void {
                if (!Schema::hasColumn('individual_development_activities', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()->after('status');
                }
                if (!Schema::hasColumn('individual_development_activities', 'cancel_reason')) {
                    $table->text('cancel_reason')->nullable()->after('completed_at');
                }
                if (!Schema::hasColumn('individual_development_activities', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable()->after('cancel_reason');
                }
                if (!Schema::hasColumn('individual_development_activities', 'cancelled_by')) {
                    $table->unsignedBigInteger('cancelled_by')->nullable()->index()->after('cancelled_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('individual_development_activities')) {
            $columns = array_values(array_filter([
                Schema::hasColumn('individual_development_activities', 'completed_at') ? 'completed_at' : null,
                Schema::hasColumn('individual_development_activities', 'cancel_reason') ? 'cancel_reason' : null,
                Schema::hasColumn('individual_development_activities', 'cancelled_at') ? 'cancelled_at' : null,
                Schema::hasColumn('individual_development_activities', 'cancelled_by') ? 'cancelled_by' : null,
            ]));
            if ($columns !== []) {
                Schema::table('individual_development_activities', fn (Blueprint $table) => $table->dropColumn($columns));
            }
        }

        if (Schema::hasTable('individual_development_goals')) {
            $columns = array_values(array_filter([
                Schema::hasColumn('individual_development_goals', 'achieved_at') ? 'achieved_at' : null,
                Schema::hasColumn('individual_development_goals', 'achieved_by') ? 'achieved_by' : null,
                Schema::hasColumn('individual_development_goals', 'cancel_reason') ? 'cancel_reason' : null,
                Schema::hasColumn('individual_development_goals', 'cancelled_at') ? 'cancelled_at' : null,
                Schema::hasColumn('individual_development_goals', 'cancelled_by') ? 'cancelled_by' : null,
                Schema::hasColumn('individual_development_goals', 'status_note') ? 'status_note' : null,
            ]));
            if ($columns !== []) {
                Schema::table('individual_development_goals', fn (Blueprint $table) => $table->dropColumn($columns));
            }
        }
    }
};
