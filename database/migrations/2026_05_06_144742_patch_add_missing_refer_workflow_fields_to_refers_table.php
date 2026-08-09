<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('refers')) {
            return;
        }

        Schema::table('refers', function (Blueprint $table) {
            if (!Schema::hasColumn('refers', 'meeting_report_file')) {
                $table->string('meeting_report_file')->nullable()->after('client_id');
            }

            if (!Schema::hasColumn('refers', 'approve_status')) {
                $table->enum('approve_status', [
                    'pending',
                    'approved',
                    'rejected',
                    'cancelled',
                ])->default('pending')->after('meeting_report_file');
            }

            if (!Schema::hasColumn('refers', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('approve_status');
            }

            if (!Schema::hasColumn('refers', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('created_by');
            }

            if (!Schema::hasColumn('refers', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });

        /*
         * MySQL/MariaDB only: extend an existing ENUM definition.
         * SQLite does not support ALTER TABLE ... MODIFY, and a freshly
         * created SQLite test column above already includes "cancelled".
         */
        if (
            Schema::hasColumn('refers', 'approve_status')
            && DB::connection()->getDriverName() !== 'sqlite'
        ) {
            DB::statement("
                ALTER TABLE refers
                MODIFY approve_status ENUM('pending','approved','rejected','cancelled')
                NOT NULL DEFAULT 'pending'
            ");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('refers')) {
            return;
        }

        Schema::table('refers', function (Blueprint $table) {
            $columns = [];

            foreach ([
                'meeting_report_file',
                'approve_status',
                'created_by',
                'approved_by',
                'approved_at',
            ] as $column) {
                if (Schema::hasColumn('refers', $column)) {
                    $columns[] = $column;
                }
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
