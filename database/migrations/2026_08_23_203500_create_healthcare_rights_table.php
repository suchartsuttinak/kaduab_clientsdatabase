<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('healthcare_rights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            $table->date('record_date');
            $table->string('coverage_status', 80);
            $table->string('primary_hospital')->nullable();
            $table->string('recorder_name');
            $table->timestamps();

            $table->unique(
                ['client_id', 'record_date'],
                'healthcare_rights_client_date_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('healthcare_rights');
    }
};
