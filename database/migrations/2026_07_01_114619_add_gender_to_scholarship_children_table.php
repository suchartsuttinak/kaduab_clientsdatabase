<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('scholarship_children', function (Blueprint $table) {
        $table->string('gender', 20)->nullable()->after('last_name');
    });
}

public function down(): void
{
    Schema::table('scholarship_children', function (Blueprint $table) {
        $table->dropColumn('gender');
    });
}
};
