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
       Schema::create('citizen_idstation', function (Blueprint $table) {
    $table->id();

    $table->foreignId('idstation_id')
        ->constrained('idstations')
        ->cascadeOnDelete();

   $table->foreignId('citizen_id')
    ->constrained('citizens')
    ->cascadeOnDelete();

    $table->timestamps();

    $table->unique(['idstation_id', 'citizen_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citizen_idstation');
    }
};
