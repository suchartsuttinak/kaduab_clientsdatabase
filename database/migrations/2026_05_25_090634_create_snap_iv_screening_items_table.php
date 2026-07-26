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
        Schema::create('snap_iv_screening_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('snap_iv_screening_id')
                ->constrained('snap_iv_screenings')
                ->cascadeOnDelete();

            $table->string('category');
            $table->unsignedTinyInteger('item_no');
            $table->text('question');
            $table->unsignedTinyInteger('score')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snap_iv_screening_items');
    }
};
