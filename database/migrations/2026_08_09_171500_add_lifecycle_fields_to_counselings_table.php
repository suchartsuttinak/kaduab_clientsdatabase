<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counselings', function (Blueprint $table) {
            $table->string('issue_relation', 30)
                ->nullable()
                ->after('session_no')
                ->comment('new_problem,same_problem,related_problem,unspecified');

            $table->date('closed_date')
                ->nullable()
                ->after('status');

            $table->string('closure_type', 30)
                ->nullable()
                ->after('closed_date')
                ->comment('completed,goal_met,referred,discontinued');

            $table->text('closure_summary')
                ->nullable()
                ->after('closure_type');

            $table->string('goal_achievement', 30)
                ->nullable()
                ->after('closure_summary')
                ->comment('achieved,partial,not_achieved,not_applicable');

            $table->text('final_recommendation')
                ->nullable()
                ->after('goal_achievement');

            $table->text('closure_note')
                ->nullable()
                ->after('final_recommendation');

            $table->foreignId('closed_by_user_id')
                ->nullable()
                ->after('closure_note')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('closed_by_name', 150)
                ->nullable()
                ->after('closed_by_user_id');

            $table->index(['client_id', 'closed_date']);
        });
    }

    public function down(): void
    {
        Schema::table('counselings', function (Blueprint $table) {
            $table->dropIndex(['client_id', 'closed_date']);
            $table->dropForeign(['closed_by_user_id']);

            $table->dropColumn([
                'issue_relation',
                'closed_date',
                'closure_type',
                'closure_summary',
                'goal_achievement',
                'final_recommendation',
                'closure_note',
                'closed_by_user_id',
                'closed_by_name',
            ]);
        });
    }
};
