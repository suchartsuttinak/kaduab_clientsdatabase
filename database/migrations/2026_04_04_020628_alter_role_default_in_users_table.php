<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        /*
         * This migration originally uses MySQL/MariaDB-specific
         * "ALTER TABLE ... MODIFY" syntax. Laravel's test suite uses
         * SQLite in-memory, where MODIFY is not valid SQL.
         *
         * The SQLite test schema already receives explicit role/status
         * values from UserFactory, so no data/application behavior is
         * changed by skipping only this default-alter operation on SQLite.
         */
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY role VARCHAR(255) NOT NULL DEFAULT 'general_user'");
        DB::statement("ALTER TABLE users MODIFY status VARCHAR(255) NOT NULL DEFAULT '1'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY role VARCHAR(255) NOT NULL DEFAULT 'user'");
        DB::statement("ALTER TABLE users MODIFY status VARCHAR(255) NOT NULL DEFAULT '1'");
    }
};
