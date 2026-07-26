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
        Schema::create('snap_iv_screenings', function (Blueprint $table) {
        $table->id();

        $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
        $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

        $table->date('screening_date');
        $table->string('observer_name')->nullable();
        $table->string('relationship')->nullable();
        $table->string('age_text')->nullable();
        $table->string('class_level')->nullable();
        $table->string('term')->nullable();
        $table->string('grade_average')->nullable();

        $table->unsignedTinyInteger('inattention_score')->default(0);
        $table->unsignedTinyInteger('hyperactivity_score')->default(0);
        $table->unsignedTinyInteger('oppositional_score')->default(0);
        $table->unsignedTinyInteger('total_score')->default(0);

        $table->string('inattention_level')->nullable();
        $table->string('hyperactivity_level')->nullable();
        $table->string('oppositional_level')->nullable();

        $table->text('summary')->nullable();
        $table->text('recommendation')->nullable();
        $table->text('remark')->nullable();

        $table->timestamps();

        $table->unique(['client_id', 'screening_date']);
    });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snap_iv_screenings');
    }
};
