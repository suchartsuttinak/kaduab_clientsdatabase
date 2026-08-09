<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $addTopic = !Schema::hasColumn('counseling_followups', 'topic');
        $addSessionGoal = !Schema::hasColumn('counseling_followups', 'session_goal');
        $addInterventions = !Schema::hasColumn('counseling_followups', 'interventions');
        $addAdvice = !Schema::hasColumn('counseling_followups', 'advice');
        $addAgreement = !Schema::hasColumn('counseling_followups', 'agreement');
        $addRiskLevel = !Schema::hasColumn('counseling_followups', 'risk_level');
        $addRiskDetail = !Schema::hasColumn('counseling_followups', 'risk_detail');

        Schema::table(
            'counseling_followups',
            function (Blueprint $table) use (
                $addTopic,
                $addSessionGoal,
                $addInterventions,
                $addAdvice,
                $addAgreement,
                $addRiskLevel,
                $addRiskDetail
            ) {
                if ($addTopic) {
                    $table->text('topic')->nullable()->after('location');
                }

                if ($addSessionGoal) {
                    $table->text('session_goal')->nullable()->after('current_assessment');
                }

                if ($addInterventions) {
                    $table->text('interventions')->nullable()->after('session_goal');
                }

                if ($addAdvice) {
                    $table->text('advice')->nullable()->after('interventions');
                }

                if ($addAgreement) {
                    $table->text('agreement')->nullable()->after('advice');
                }

                if ($addRiskLevel) {
                    $table->string('risk_level', 20)->default('none')->after('result');
                }

                if ($addRiskDetail) {
                    $table->text('risk_detail')->nullable()->after('risk_level');
                }
            }
        );
    }

    public function down(): void
    {
        $existing = array_values(array_filter([
            Schema::hasColumn('counseling_followups', 'topic') ? 'topic' : null,
            Schema::hasColumn('counseling_followups', 'session_goal') ? 'session_goal' : null,
            Schema::hasColumn('counseling_followups', 'interventions') ? 'interventions' : null,
            Schema::hasColumn('counseling_followups', 'advice') ? 'advice' : null,
            Schema::hasColumn('counseling_followups', 'agreement') ? 'agreement' : null,
            Schema::hasColumn('counseling_followups', 'risk_level') ? 'risk_level' : null,
            Schema::hasColumn('counseling_followups', 'risk_detail') ? 'risk_detail' : null,
        ]));

        if ($existing === []) {
            return;
        }

        Schema::table('counseling_followups', function (Blueprint $table) use ($existing) {
            $table->dropColumn($existing);
        });
    }
};
