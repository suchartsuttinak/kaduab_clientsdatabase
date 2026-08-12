<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->ensureDomains();
        $this->ensureIndicators();
        $this->ensureRubrics();
        $this->ensurePlans();
        $this->ensureAssessments();
        $this->ensureAssessmentItems();
        $this->ensureGoals();
        $this->ensureActivities();
        $this->ensureFollowups();
        $this->ensureFollowupItems();
        $this->ensureEvidences();
    }

    public function down(): void
    {
        // Intentionally non-destructive.
        // This phase may be installed on a database that already contains child records.
    }

    private function addIfMissing(string $tableName, string $column, Closure $definition): void
    {
        if (!Schema::hasColumn($tableName, $column)) {
            Schema::table($tableName, function (Blueprint $table) use ($definition): void {
                $definition($table);
            });
        }
    }

    private function ensureDomains(): void
    {
        if (!Schema::hasTable('development_domains')) {
            Schema::create('development_domains', function (Blueprint $table): void {
                $table->id();
                $table->string('code', 50)->unique();
                $table->string('name', 255);
                $table->text('description')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
            return;
        }

        $this->addIfMissing('development_domains', 'code', fn (Blueprint $t) => $t->string('code', 50)->nullable()->index());
        $this->addIfMissing('development_domains', 'name', fn (Blueprint $t) => $t->string('name', 255)->nullable());
        $this->addIfMissing('development_domains', 'description', fn (Blueprint $t) => $t->text('description')->nullable());
        $this->addIfMissing('development_domains', 'sort_order', fn (Blueprint $t) => $t->unsignedInteger('sort_order')->default(0));
        $this->addIfMissing('development_domains', 'is_active', fn (Blueprint $t) => $t->boolean('is_active')->default(true));
        $this->addIfMissing('development_domains', 'created_at', fn (Blueprint $t) => $t->timestamp('created_at')->nullable());
        $this->addIfMissing('development_domains', 'updated_at', fn (Blueprint $t) => $t->timestamp('updated_at')->nullable());
    }

    private function ensureIndicators(): void
    {
        if (!Schema::hasTable('development_indicators')) {
            Schema::create('development_indicators', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('domain_id')->constrained('development_domains')->cascadeOnUpdate()->cascadeOnDelete();
                $table->string('code', 50)->unique();
                $table->string('name', 255);
                $table->text('description')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['domain_id', 'sort_order']);
            });
            return;
        }

        $this->addIfMissing('development_indicators', 'domain_id', fn (Blueprint $t) => $t->unsignedBigInteger('domain_id')->nullable()->index());
        $this->addIfMissing('development_indicators', 'code', fn (Blueprint $t) => $t->string('code', 50)->nullable()->index());
        $this->addIfMissing('development_indicators', 'name', fn (Blueprint $t) => $t->string('name', 255)->nullable());
        $this->addIfMissing('development_indicators', 'description', fn (Blueprint $t) => $t->text('description')->nullable());
        $this->addIfMissing('development_indicators', 'sort_order', fn (Blueprint $t) => $t->unsignedInteger('sort_order')->default(0));
        $this->addIfMissing('development_indicators', 'is_active', fn (Blueprint $t) => $t->boolean('is_active')->default(true));
        $this->addIfMissing('development_indicators', 'created_at', fn (Blueprint $t) => $t->timestamp('created_at')->nullable());
        $this->addIfMissing('development_indicators', 'updated_at', fn (Blueprint $t) => $t->timestamp('updated_at')->nullable());
    }

    private function ensureRubrics(): void
    {
        if (!Schema::hasTable('development_rubrics')) {
            Schema::create('development_rubrics', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('indicator_id')->constrained('development_indicators')->cascadeOnUpdate()->cascadeOnDelete();
                $table->unsignedTinyInteger('level');
                $table->string('title', 255);
                $table->text('description')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->unique(['indicator_id', 'level'], 'dev_rubric_indicator_level_unique');
            });
            return;
        }

        $this->addIfMissing('development_rubrics', 'indicator_id', fn (Blueprint $t) => $t->unsignedBigInteger('indicator_id')->nullable()->index());
        $this->addIfMissing('development_rubrics', 'level', fn (Blueprint $t) => $t->unsignedTinyInteger('level')->nullable());
        $this->addIfMissing('development_rubrics', 'title', fn (Blueprint $t) => $t->string('title', 255)->nullable());
        $this->addIfMissing('development_rubrics', 'description', fn (Blueprint $t) => $t->text('description')->nullable());
        $this->addIfMissing('development_rubrics', 'sort_order', fn (Blueprint $t) => $t->unsignedInteger('sort_order')->default(0));
        $this->addIfMissing('development_rubrics', 'created_at', fn (Blueprint $t) => $t->timestamp('created_at')->nullable());
        $this->addIfMissing('development_rubrics', 'updated_at', fn (Blueprint $t) => $t->timestamp('updated_at')->nullable());
    }

    private function ensurePlans(): void
    {
        if (!Schema::hasTable('individual_development_plans')) {
            Schema::create('individual_development_plans', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnUpdate()->cascadeOnDelete();
                $table->unsignedInteger('plan_no');
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->text('overall_goal');
                $table->text('strength_summary')->nullable();
                $table->text('development_need_summary')->nullable();
                $table->text('client_need_summary')->nullable();
                $table->text('caregiver_need_summary')->nullable();
                $table->text('risk_factor_summary')->nullable();
                $table->text('protective_factor_summary')->nullable();
                $table->text('support_network_summary')->nullable();
                $table->string('status', 30)->default('active');
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('closed_at')->nullable();
                $table->text('close_reason')->nullable();
                $table->text('final_outcome')->nullable();
                $table->text('final_recommendation')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['client_id', 'plan_no'], 'idp_client_plan_no_unique');
                $table->index(['client_id', 'status']);
            });
            return;
        }

        $columns = [
            'client_id' => fn (Blueprint $t) => $t->unsignedBigInteger('client_id')->nullable()->index(),
            'plan_no' => fn (Blueprint $t) => $t->unsignedInteger('plan_no')->default(1),
            'start_date' => fn (Blueprint $t) => $t->date('start_date')->nullable(),
            'end_date' => fn (Blueprint $t) => $t->date('end_date')->nullable(),
            'overall_goal' => fn (Blueprint $t) => $t->text('overall_goal')->nullable(),
            'strength_summary' => fn (Blueprint $t) => $t->text('strength_summary')->nullable(),
            'development_need_summary' => fn (Blueprint $t) => $t->text('development_need_summary')->nullable(),
            'client_need_summary' => fn (Blueprint $t) => $t->text('client_need_summary')->nullable(),
            'caregiver_need_summary' => fn (Blueprint $t) => $t->text('caregiver_need_summary')->nullable(),
            'risk_factor_summary' => fn (Blueprint $t) => $t->text('risk_factor_summary')->nullable(),
            'protective_factor_summary' => fn (Blueprint $t) => $t->text('protective_factor_summary')->nullable(),
            'support_network_summary' => fn (Blueprint $t) => $t->text('support_network_summary')->nullable(),
            'status' => fn (Blueprint $t) => $t->string('status', 30)->default('active'),
            'created_by' => fn (Blueprint $t) => $t->unsignedBigInteger('created_by')->nullable()->index(),
            'updated_by' => fn (Blueprint $t) => $t->unsignedBigInteger('updated_by')->nullable()->index(),
            'reviewed_by' => fn (Blueprint $t) => $t->unsignedBigInteger('reviewed_by')->nullable()->index(),
            'reviewed_at' => fn (Blueprint $t) => $t->timestamp('reviewed_at')->nullable(),
            'closed_by' => fn (Blueprint $t) => $t->unsignedBigInteger('closed_by')->nullable()->index(),
            'closed_at' => fn (Blueprint $t) => $t->timestamp('closed_at')->nullable(),
            'close_reason' => fn (Blueprint $t) => $t->text('close_reason')->nullable(),
            'final_outcome' => fn (Blueprint $t) => $t->text('final_outcome')->nullable(),
            'final_recommendation' => fn (Blueprint $t) => $t->text('final_recommendation')->nullable(),
            'created_at' => fn (Blueprint $t) => $t->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $t) => $t->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $t) => $t->softDeletes(),
        ];
        foreach ($columns as $column => $definition) { $this->addIfMissing('individual_development_plans', $column, $definition); }
    }

    private function ensureAssessments(): void
    {
        if (!Schema::hasTable('individual_development_assessments')) {
            Schema::create('individual_development_assessments', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('plan_id')->constrained('individual_development_plans')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnUpdate()->cascadeOnDelete();
                $table->string('assessment_type', 30)->default('baseline');
                $table->unsignedInteger('round_no')->default(1);
                $table->date('assessment_date');
                $table->foreignId('assessed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->json('information_sources')->nullable();
                $table->text('participant_note')->nullable();
                $table->text('overall_note')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['plan_id', 'assessment_type', 'assessment_date'], 'idp_assess_plan_type_date_idx');
            });
            return;
        }

        $columns = [
            'plan_id' => fn (Blueprint $t) => $t->unsignedBigInteger('plan_id')->nullable()->index(),
            'client_id' => fn (Blueprint $t) => $t->unsignedBigInteger('client_id')->nullable()->index(),
            'assessment_type' => fn (Blueprint $t) => $t->string('assessment_type', 30)->default('baseline'),
            'round_no' => fn (Blueprint $t) => $t->unsignedInteger('round_no')->default(1),
            'assessment_date' => fn (Blueprint $t) => $t->date('assessment_date')->nullable(),
            'assessed_by' => fn (Blueprint $t) => $t->unsignedBigInteger('assessed_by')->nullable()->index(),
            'information_sources' => fn (Blueprint $t) => $t->json('information_sources')->nullable(),
            'participant_note' => fn (Blueprint $t) => $t->text('participant_note')->nullable(),
            'overall_note' => fn (Blueprint $t) => $t->text('overall_note')->nullable(),
            'created_by' => fn (Blueprint $t) => $t->unsignedBigInteger('created_by')->nullable()->index(),
            'updated_by' => fn (Blueprint $t) => $t->unsignedBigInteger('updated_by')->nullable()->index(),
            'created_at' => fn (Blueprint $t) => $t->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $t) => $t->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $t) => $t->softDeletes(),
        ];
        foreach ($columns as $column => $definition) { $this->addIfMissing('individual_development_assessments', $column, $definition); }
    }

    private function ensureAssessmentItems(): void
    {
        if (!Schema::hasTable('individual_development_assessment_items')) {
            Schema::create('individual_development_assessment_items', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('assessment_id')->constrained('individual_development_assessments')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId('indicator_id')->constrained('development_indicators')->cascadeOnUpdate()->restrictOnDelete();
                $table->unsignedTinyInteger('score')->nullable();
                $table->text('evidence')->nullable();
                $table->text('development_note')->nullable();
                $table->timestamps();
                $table->unique(['assessment_id', 'indicator_id'], 'idp_assess_item_unique');
            });
            return;
        }

        $columns = [
            'assessment_id' => fn (Blueprint $t) => $t->unsignedBigInteger('assessment_id')->nullable()->index(),
            'indicator_id' => fn (Blueprint $t) => $t->unsignedBigInteger('indicator_id')->nullable()->index(),
            'score' => fn (Blueprint $t) => $t->unsignedTinyInteger('score')->nullable(),
            'evidence' => fn (Blueprint $t) => $t->text('evidence')->nullable(),
            'development_note' => fn (Blueprint $t) => $t->text('development_note')->nullable(),
            'created_at' => fn (Blueprint $t) => $t->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $t) => $t->timestamp('updated_at')->nullable(),
        ];
        foreach ($columns as $column => $definition) { $this->addIfMissing('individual_development_assessment_items', $column, $definition); }
    }

    private function ensureGoals(): void
    {
        if (!Schema::hasTable('individual_development_goals')) {
            Schema::create('individual_development_goals', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('plan_id')->constrained('individual_development_plans')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId('domain_id')->constrained('development_domains')->cascadeOnUpdate()->restrictOnDelete();
                $table->foreignId('indicator_id')->nullable()->constrained('development_indicators')->nullOnDelete();
                $table->string('title', 500);
                $table->text('description')->nullable();
                $table->unsignedTinyInteger('baseline_level')->nullable();
                $table->unsignedTinyInteger('target_level')->nullable();
                $table->text('success_indicator')->nullable();
                $table->string('measurement_method', 500)->nullable();
                $table->decimal('target_value', 12, 2)->nullable();
                $table->string('target_unit', 100)->nullable();
                $table->date('target_date')->nullable();
                $table->string('priority', 30)->default('medium');
                $table->string('status', 30)->default('not_started');
                $table->unsignedInteger('sort_order')->default(0);
                $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('responsible_name', 255)->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['plan_id', 'status']);
            });
            return;
        }

        $columns = [
            'plan_id' => fn (Blueprint $t) => $t->unsignedBigInteger('plan_id')->nullable()->index(),
            'domain_id' => fn (Blueprint $t) => $t->unsignedBigInteger('domain_id')->nullable()->index(),
            'indicator_id' => fn (Blueprint $t) => $t->unsignedBigInteger('indicator_id')->nullable()->index(),
            'title' => fn (Blueprint $t) => $t->string('title', 500)->nullable(),
            'description' => fn (Blueprint $t) => $t->text('description')->nullable(),
            'baseline_level' => fn (Blueprint $t) => $t->unsignedTinyInteger('baseline_level')->nullable(),
            'target_level' => fn (Blueprint $t) => $t->unsignedTinyInteger('target_level')->nullable(),
            'success_indicator' => fn (Blueprint $t) => $t->text('success_indicator')->nullable(),
            'measurement_method' => fn (Blueprint $t) => $t->string('measurement_method', 500)->nullable(),
            'target_value' => fn (Blueprint $t) => $t->decimal('target_value', 12, 2)->nullable(),
            'target_unit' => fn (Blueprint $t) => $t->string('target_unit', 100)->nullable(),
            'target_date' => fn (Blueprint $t) => $t->date('target_date')->nullable(),
            'priority' => fn (Blueprint $t) => $t->string('priority', 30)->default('medium'),
            'status' => fn (Blueprint $t) => $t->string('status', 30)->default('not_started'),
            'sort_order' => fn (Blueprint $t) => $t->unsignedInteger('sort_order')->default(0),
            'responsible_user_id' => fn (Blueprint $t) => $t->unsignedBigInteger('responsible_user_id')->nullable()->index(),
            'responsible_name' => fn (Blueprint $t) => $t->string('responsible_name', 255)->nullable(),
            'created_by' => fn (Blueprint $t) => $t->unsignedBigInteger('created_by')->nullable()->index(),
            'updated_by' => fn (Blueprint $t) => $t->unsignedBigInteger('updated_by')->nullable()->index(),
            'created_at' => fn (Blueprint $t) => $t->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $t) => $t->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $t) => $t->softDeletes(),
        ];
        foreach ($columns as $column => $definition) { $this->addIfMissing('individual_development_goals', $column, $definition); }
    }

    private function ensureActivities(): void
    {
        if (!Schema::hasTable('individual_development_activities')) {
            Schema::create('individual_development_activities', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('goal_id')->constrained('individual_development_goals')->cascadeOnUpdate()->cascadeOnDelete();
                $table->date('activity_date');
                $table->date('end_date')->nullable();
                $table->string('activity_type', 255)->nullable();
                $table->text('detail');
                $table->string('frequency', 255)->nullable();
                $table->string('status', 30)->default('planned');
                $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('responsible_name', 255)->nullable();
                $table->text('result')->nullable();
                $table->text('problem')->nullable();
                $table->text('next_action')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['goal_id', 'activity_date']);
            });
            return;
        }

        $columns = [
            'goal_id' => fn (Blueprint $t) => $t->unsignedBigInteger('goal_id')->nullable()->index(),
            'activity_date' => fn (Blueprint $t) => $t->date('activity_date')->nullable(),
            'end_date' => fn (Blueprint $t) => $t->date('end_date')->nullable(),
            'activity_type' => fn (Blueprint $t) => $t->string('activity_type', 255)->nullable(),
            'detail' => fn (Blueprint $t) => $t->text('detail')->nullable(),
            'frequency' => fn (Blueprint $t) => $t->string('frequency', 255)->nullable(),
            'status' => fn (Blueprint $t) => $t->string('status', 30)->default('planned'),
            'responsible_user_id' => fn (Blueprint $t) => $t->unsignedBigInteger('responsible_user_id')->nullable()->index(),
            'responsible_name' => fn (Blueprint $t) => $t->string('responsible_name', 255)->nullable(),
            'result' => fn (Blueprint $t) => $t->text('result')->nullable(),
            'problem' => fn (Blueprint $t) => $t->text('problem')->nullable(),
            'next_action' => fn (Blueprint $t) => $t->text('next_action')->nullable(),
            'created_by' => fn (Blueprint $t) => $t->unsignedBigInteger('created_by')->nullable()->index(),
            'updated_by' => fn (Blueprint $t) => $t->unsignedBigInteger('updated_by')->nullable()->index(),
            'created_at' => fn (Blueprint $t) => $t->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $t) => $t->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $t) => $t->softDeletes(),
        ];
        foreach ($columns as $column => $definition) { $this->addIfMissing('individual_development_activities', $column, $definition); }
    }

    private function ensureFollowups(): void
    {
        if (!Schema::hasTable('individual_development_followups')) {
            Schema::create('individual_development_followups', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('plan_id')->constrained('individual_development_plans')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnUpdate()->cascadeOnDelete();
                $table->unsignedInteger('followup_no');
                $table->date('followup_date');
                $table->string('followup_type', 255)->nullable();
                $table->foreignId('follower_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('follower_name', 255)->nullable();
                $table->text('current_situation')->nullable();
                $table->text('changes')->nullable();
                $table->text('positive_changes')->nullable();
                $table->text('actions_taken')->nullable();
                $table->text('result')->nullable();
                $table->text('problem')->nullable();
                $table->text('client_feedback')->nullable();
                $table->text('caregiver_feedback')->nullable();
                $table->string('overall_result', 30)->nullable();
                $table->text('suggestion')->nullable();
                $table->text('next_action')->nullable();
                $table->date('next_followup_date')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['plan_id', 'followup_no'], 'idp_followup_no_unique');
                $table->index(['client_id', 'followup_date']);
            });
            return;
        }

        $columns = [
            'plan_id' => fn (Blueprint $t) => $t->unsignedBigInteger('plan_id')->nullable()->index(),
            'client_id' => fn (Blueprint $t) => $t->unsignedBigInteger('client_id')->nullable()->index(),
            'followup_no' => fn (Blueprint $t) => $t->unsignedInteger('followup_no')->default(1),
            'followup_date' => fn (Blueprint $t) => $t->date('followup_date')->nullable(),
            'followup_type' => fn (Blueprint $t) => $t->string('followup_type', 255)->nullable(),
            'follower_user_id' => fn (Blueprint $t) => $t->unsignedBigInteger('follower_user_id')->nullable()->index(),
            'follower_name' => fn (Blueprint $t) => $t->string('follower_name', 255)->nullable(),
            'current_situation' => fn (Blueprint $t) => $t->text('current_situation')->nullable(),
            'changes' => fn (Blueprint $t) => $t->text('changes')->nullable(),
            'positive_changes' => fn (Blueprint $t) => $t->text('positive_changes')->nullable(),
            'actions_taken' => fn (Blueprint $t) => $t->text('actions_taken')->nullable(),
            'result' => fn (Blueprint $t) => $t->text('result')->nullable(),
            'problem' => fn (Blueprint $t) => $t->text('problem')->nullable(),
            'client_feedback' => fn (Blueprint $t) => $t->text('client_feedback')->nullable(),
            'caregiver_feedback' => fn (Blueprint $t) => $t->text('caregiver_feedback')->nullable(),
            'overall_result' => fn (Blueprint $t) => $t->string('overall_result', 30)->nullable(),
            'suggestion' => fn (Blueprint $t) => $t->text('suggestion')->nullable(),
            'next_action' => fn (Blueprint $t) => $t->text('next_action')->nullable(),
            'next_followup_date' => fn (Blueprint $t) => $t->date('next_followup_date')->nullable(),
            'created_by' => fn (Blueprint $t) => $t->unsignedBigInteger('created_by')->nullable()->index(),
            'updated_by' => fn (Blueprint $t) => $t->unsignedBigInteger('updated_by')->nullable()->index(),
            'created_at' => fn (Blueprint $t) => $t->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $t) => $t->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $t) => $t->softDeletes(),
        ];
        foreach ($columns as $column => $definition) { $this->addIfMissing('individual_development_followups', $column, $definition); }
    }

    private function ensureFollowupItems(): void
    {
        if (!Schema::hasTable('individual_development_followup_items')) {
            Schema::create('individual_development_followup_items', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('followup_id')->constrained('individual_development_followups')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId('indicator_id')->constrained('development_indicators')->cascadeOnUpdate()->restrictOnDelete();
                $table->unsignedTinyInteger('previous_score')->nullable();
                $table->unsignedTinyInteger('score')->nullable();
                $table->text('evidence')->nullable();
                $table->text('development_note')->nullable();
                $table->timestamps();
                $table->unique(['followup_id', 'indicator_id'], 'idp_followup_item_unique');
            });
            return;
        }

        $columns = [
            'followup_id' => fn (Blueprint $t) => $t->unsignedBigInteger('followup_id')->nullable()->index(),
            'indicator_id' => fn (Blueprint $t) => $t->unsignedBigInteger('indicator_id')->nullable()->index(),
            'previous_score' => fn (Blueprint $t) => $t->unsignedTinyInteger('previous_score')->nullable(),
            'score' => fn (Blueprint $t) => $t->unsignedTinyInteger('score')->nullable(),
            'evidence' => fn (Blueprint $t) => $t->text('evidence')->nullable(),
            'development_note' => fn (Blueprint $t) => $t->text('development_note')->nullable(),
            'created_at' => fn (Blueprint $t) => $t->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $t) => $t->timestamp('updated_at')->nullable(),
        ];
        foreach ($columns as $column => $definition) { $this->addIfMissing('individual_development_followup_items', $column, $definition); }
    }

    private function ensureEvidences(): void
    {
        if (!Schema::hasTable('individual_development_evidences')) {
            Schema::create('individual_development_evidences', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId('plan_id')->constrained('individual_development_plans')->cascadeOnUpdate()->cascadeOnDelete();
                $table->nullableMorphs('evidenceable');
                $table->string('category', 100)->nullable();
                $table->string('original_name', 500);
                $table->string('stored_name', 500);
                $table->string('file_path', 1000);
                $table->string('mime_type', 255)->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->text('description')->nullable();
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['client_id', 'plan_id']);
            });
            return;
        }

        $columns = [
            'client_id' => fn (Blueprint $t) => $t->unsignedBigInteger('client_id')->nullable()->index(),
            'plan_id' => fn (Blueprint $t) => $t->unsignedBigInteger('plan_id')->nullable()->index(),
            'evidenceable_type' => fn (Blueprint $t) => $t->string('evidenceable_type')->nullable()->index(),
            'evidenceable_id' => fn (Blueprint $t) => $t->unsignedBigInteger('evidenceable_id')->nullable()->index(),
            'category' => fn (Blueprint $t) => $t->string('category', 100)->nullable(),
            'original_name' => fn (Blueprint $t) => $t->string('original_name', 500)->nullable(),
            'stored_name' => fn (Blueprint $t) => $t->string('stored_name', 500)->nullable(),
            'file_path' => fn (Blueprint $t) => $t->string('file_path', 1000)->nullable(),
            'mime_type' => fn (Blueprint $t) => $t->string('mime_type', 255)->nullable(),
            'file_size' => fn (Blueprint $t) => $t->unsignedBigInteger('file_size')->nullable(),
            'description' => fn (Blueprint $t) => $t->text('description')->nullable(),
            'uploaded_by' => fn (Blueprint $t) => $t->unsignedBigInteger('uploaded_by')->nullable()->index(),
            'created_at' => fn (Blueprint $t) => $t->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $t) => $t->timestamp('updated_at')->nullable(),
            'deleted_at' => fn (Blueprint $t) => $t->softDeletes(),
        ];
        foreach ($columns as $column => $definition) { $this->addIfMissing('individual_development_evidences', $column, $definition); }
    }
};