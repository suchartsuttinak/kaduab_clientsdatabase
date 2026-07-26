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
       Schema::create('idstations', function (Blueprint $table) {
    $table->id();

    $table->foreignId('client_id')
        ->constrained('clients')
        ->cascadeOnDelete();

    $table->date('receive_date');

    $table->text('detail')->nullable();

    $table->enum('process_status', [
        'processing',
        'received_status'
    ])->default('processing');

    $table->date('received_status_date')->nullable();

    $table->text('remark')->nullable();

    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idstations');
    }
};
