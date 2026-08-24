<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = [
        'university_enrollments',
        'university_semester_records',
        'university_subject_results',
        'university_followups',
        'university_followup_issues',
        'university_outcomes',
        'university_outcome_reasons',
        'university_semester_documents',
    ];

    public function up(): void
    {
        $preexisting = [];
        foreach (self::TABLES as $table) {
            if (Schema::hasTable($table)) {
                $preexisting[] = $table;
            }
        }

        if (!Schema::hasTable('university_tracking_install_state')) {
            Schema::create('university_tracking_install_state', function (Blueprint $table) {
                $table->id();
                $table->string('migration_key')->unique();
                $table->json('preexisting_tables')->nullable();
                $table->timestamps();
            });
        }

        $migrationKey = '2026_08_15_210000_create_university_tracking_tables';
        if (!DB::table('university_tracking_install_state')->where('migration_key', $migrationKey)->exists()) {
            DB::table('university_tracking_install_state')->insert([
                'migration_key' => $migrationKey,
                'preexisting_tables' => json_encode($preexisting, JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!Schema::hasTable('university_enrollments')) {
            Schema::create('university_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
                $table->string('university_name');
                $table->string('student_code', 100)->nullable();
                $table->string('faculty')->nullable();
                $table->string('major')->nullable();
                $table->string('degree_name')->nullable();
                $table->string('program_type', 100)->nullable();
                $table->unsignedSmallInteger('admission_academic_year');
                $table->unsignedTinyInteger('admission_term')->nullable();
                $table->date('admission_date')->nullable();
                $table->unsignedTinyInteger('curriculum_years')->nullable();
                $table->unsignedSmallInteger('expected_graduation_year')->nullable();
                $table->string('current_status', 40)->default('studying');
                $table->string('funding_type', 120)->nullable();
                $table->string('scholarship_name')->nullable();
                $table->decimal('scholarship_amount', 12, 2)->nullable();
                $table->text('note')->nullable();
                $table->timestamps();

                $table->unique(['client_id', 'institution_id', 'admission_academic_year'], 'uniq_university_enrollment');
                $table->index(['client_id', 'current_status'], 'idx_uni_enroll_client_status');
                $table->index('admission_academic_year', 'idx_uni_enroll_admit_year');
                $table->index('university_name', 'idx_uni_enroll_name');
            });
        }

        if (!Schema::hasTable('university_semester_records')) {
            Schema::create('university_semester_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('enrollment_id')->constrained('university_enrollments')->cascadeOnDelete();
                $table->foreignId('education_record_id')->nullable()->unique()->constrained('education_records')->nullOnDelete();
                $table->foreignId('semester_id')->constrained('semesters')->restrictOnDelete();
                $table->unsignedSmallInteger('academic_year');
                $table->unsignedTinyInteger('term');
                $table->unsignedTinyInteger('year_level');
                $table->date('record_date');
                $table->decimal('registered_credits', 6, 2)->nullable();
                $table->decimal('earned_credits', 6, 2)->nullable();
                $table->decimal('cumulative_credits', 7, 2)->nullable();
                $table->decimal('semester_gpa', 4, 2)->nullable();
                $table->decimal('cumulative_gpa', 4, 2)->nullable();
                $table->string('academic_status', 40)->default('normal');
                $table->string('risk_level', 40)->default('normal');
                $table->text('risk_note')->nullable();
                $table->text('semester_summary')->nullable();
                $table->timestamps();

                $table->unique(['enrollment_id', 'academic_year', 'term'], 'uniq_university_semester');
                $table->index(['academic_year', 'term'], 'idx_uni_sem_year_term');
                $table->index(['year_level', 'risk_level'], 'idx_uni_sem_year_risk');
                $table->index('semester_gpa', 'idx_uni_sem_gpa');
            });
        }

        if (!Schema::hasTable('university_subject_results')) {
            Schema::create('university_subject_results', function (Blueprint $table) {
                $table->id();
                $table->foreignId('semester_record_id')->constrained('university_semester_records')->cascadeOnDelete();
                $table->string('course_code', 100)->nullable();
                $table->string('course_name');
                $table->decimal('credits', 5, 2)->nullable();
                $table->string('grade', 20)->nullable();
                $table->decimal('grade_point', 4, 2)->nullable();
                $table->string('result_status', 30)->default('pass');
                $table->text('note')->nullable();
                $table->timestamps();

                $table->index(['semester_record_id', 'result_status'], 'idx_uni_subject_sem_status');
                $table->index('course_code', 'idx_uni_subject_code');
            });
        }

        if (!Schema::hasTable('university_followups')) {
            Schema::create('university_followups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('enrollment_id')->constrained('university_enrollments')->cascadeOnDelete();
                $table->foreignId('semester_record_id')->nullable()->constrained('university_semester_records')->nullOnDelete();
                $table->foreignId('semester_id')->nullable()->constrained('semesters')->nullOnDelete();
                $table->unsignedSmallInteger('academic_year');
                $table->date('followup_date');
                $table->string('followup_method', 40);
                $table->string('informant')->nullable();
                $table->string('overall_risk_level', 40)->default('normal');
                $table->text('general_condition')->nullable();
                $table->text('strengths')->nullable();
                $table->text('assistance_summary')->nullable();
                $table->text('next_plan')->nullable();
                $table->date('next_followup_date')->nullable();
                $table->foreignId('followed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['academic_year', 'overall_risk_level'], 'idx_uni_follow_year_risk');
                $table->index('followup_date', 'idx_uni_follow_date');
                $table->index('next_followup_date', 'idx_uni_follow_next_date');
            });
        }

        if (!Schema::hasTable('university_followup_issues')) {
            Schema::create('university_followup_issues', function (Blueprint $table) {
                $table->id();
                $table->foreignId('followup_id')->constrained('university_followups')->cascadeOnDelete();
                $table->string('category', 50);
                $table->string('severity', 40)->default('watch');
                $table->text('detail')->nullable();
                $table->text('assistance')->nullable();
                $table->string('issue_status', 40)->default('open');
                $table->timestamps();

                $table->index(['category', 'severity'], 'idx_uni_issue_category_severity');
                $table->index('issue_status', 'idx_uni_issue_status');
            });
        }

        if (!Schema::hasTable('university_outcomes')) {
            Schema::create('university_outcomes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('enrollment_id')->unique()->constrained('university_enrollments')->cascadeOnDelete();
                $table->string('outcome_type', 40);
                $table->date('outcome_date')->nullable();
                $table->unsignedSmallInteger('academic_year');
                $table->foreignId('semester_id')->nullable()->constrained('semesters')->nullOnDelete();
                $table->decimal('final_gpa', 4, 2)->nullable();
                $table->string('degree_name')->nullable();
                $table->string('honors')->nullable();
                $table->string('post_graduation_status', 40)->nullable();
                $table->text('post_graduation_detail')->nullable();
                $table->text('summary')->nullable();
                $table->timestamps();

                $table->index(['academic_year', 'outcome_type'], 'idx_uni_outcome_year_type');
            });
        }

        if (!Schema::hasTable('university_outcome_reasons')) {
            Schema::create('university_outcome_reasons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('outcome_id')->constrained('university_outcomes')->cascadeOnDelete();
                $table->string('reason_code', 50);
                $table->boolean('is_primary')->default(false);
                $table->text('detail')->nullable();
                $table->timestamps();

                $table->index(['reason_code', 'is_primary'], 'idx_uni_reason_code_primary');
            });
        }

        if (!Schema::hasTable('university_semester_documents')) {
            Schema::create('university_semester_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('semester_record_id')->constrained('university_semester_records')->cascadeOnDelete();
                $table->foreignId('education_record_id')->nullable()->constrained('education_records')->nullOnDelete();
                $table->string('document_type', 50)->default('grade_report');
                $table->string('original_name');
                $table->string('stored_name');
                $table->string('file_path');
                $table->string('mime_type', 100)->default('application/pdf');
                $table->unsignedBigInteger('file_size')->nullable();
                $table->char('sha256', 64)->nullable();
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('uploaded_at')->nullable();
                $table->timestamps();

                $table->index(['semester_record_id', 'document_type'], 'idx_uni_doc_sem_type');
                $table->index('sha256', 'idx_uni_doc_sha256');
            });
        }
    }

    public function down(): void
    {
        $key = '2026_08_15_210000_create_university_tracking_tables';
        $preexisting = [];

        if (Schema::hasTable('university_tracking_install_state')) {
            $state = DB::table('university_tracking_install_state')->where('migration_key', $key)->first();
            if ($state && $state->preexisting_tables) {
                $decoded = json_decode((string) $state->preexisting_tables, true);
                if (is_array($decoded)) {
                    $preexisting = $decoded;
                }
            }
        }

        foreach (array_reverse(self::TABLES) as $table) {
            if (!in_array($table, $preexisting, true)) {
                Schema::dropIfExists($table);
            }
        }

        if (Schema::hasTable('university_tracking_install_state')) {
            DB::table('university_tracking_install_state')->where('migration_key', $key)->delete();
            if (!DB::table('university_tracking_install_state')->exists()) {
                Schema::drop('university_tracking_install_state');
            }
        }
    }
};