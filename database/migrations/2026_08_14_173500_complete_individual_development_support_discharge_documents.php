<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function indexExists(string $table, string $index): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return false;
        }

        $database = DB::connection()->getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }

    public function up(): void
    {
        // 1) Extend the existing plan table without overwriting existing data.
        if (Schema::hasTable('individual_development_plans')) {
            Schema::table('individual_development_plans', function (Blueprint $table): void {
                if (!Schema::hasColumn('individual_development_plans', 'support_network_profile')) {
                    $table->json('support_network_profile')->nullable()->after('support_network_summary');
                }

                if (!Schema::hasColumn('individual_development_plans', 'discharge_plan_profile')) {
                    $table->json('discharge_plan_profile')->nullable()->after('support_network_profile');
                }
            });
        }

        // 2) Coordination history. Explicit short index names avoid MySQL's 64-char limit.
        if (!Schema::hasTable('individual_development_coordinations')) {
            Schema::create('individual_development_coordinations', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('plan_id')->nullable()->constrained('individual_development_plans')->nullOnDelete();
                $table->date('coordination_date');
                $table->string('agency_name', 255);
                $table->string('subject', 500);
                $table->string('coordinator_name', 255)->nullable();
                $table->text('result')->nullable();
                $table->date('next_appointment_date')->nullable();
                $table->string('document_note', 500)->nullable();
                $table->string('status', 30)->default('open');
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['client_id', 'coordination_date'], 'idc_client_date_idx');
                $table->index(['plan_id', 'status'], 'idc_plan_status_idx');
            });
        } else {
            // Recovery path for a previous failed run: the table can already exist while
            // the migration is still Pending. Add only the indexes that were not created.
            if (!$this->indexExists('individual_development_coordinations', 'idc_client_date_idx')) {
                Schema::table('individual_development_coordinations', function (Blueprint $table): void {
                    $table->index(['client_id', 'coordination_date'], 'idc_client_date_idx');
                });
            }

            if (!$this->indexExists('individual_development_coordinations', 'idc_plan_status_idx')) {
                Schema::table('individual_development_coordinations', function (Blueprint $table): void {
                    $table->index(['plan_id', 'status'], 'idc_plan_status_idx');
                });
            }
        }

        // 3) Important-document status. Files themselves continue to use the existing client_files system.
        if (!Schema::hasTable('client_document_statuses')) {
            Schema::create('client_document_statuses', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->string('document_type', 80);
                $table->string('status', 30)->default('missing');
                $table->date('expires_at')->nullable();
                $table->text('note')->nullable();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['client_id', 'document_type'], 'cds_client_doc_uq');
                $table->index(['client_id', 'status'], 'cds_client_status_idx');
            });
        } else {
            if (!$this->indexExists('client_document_statuses', 'cds_client_doc_uq')) {
                Schema::table('client_document_statuses', function (Blueprint $table): void {
                    $table->unique(['client_id', 'document_type'], 'cds_client_doc_uq');
                });
            }

            if (!$this->indexExists('client_document_statuses', 'cds_client_status_idx')) {
                Schema::table('client_document_statuses', function (Blueprint $table): void {
                    $table->index(['client_id', 'status'], 'cds_client_status_idx');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('client_document_statuses');
        Schema::dropIfExists('individual_development_coordinations');

        if (Schema::hasTable('individual_development_plans')) {
            $drop = [];

            foreach (['discharge_plan_profile', 'support_network_profile'] as $column) {
                if (Schema::hasColumn('individual_development_plans', $column)) {
                    $drop[] = $column;
                }
            }

            if ($drop !== []) {
                Schema::table('individual_development_plans', function (Blueprint $table) use ($drop): void {
                    $table->dropColumn($drop);
                });
            }
        }
    }
};
