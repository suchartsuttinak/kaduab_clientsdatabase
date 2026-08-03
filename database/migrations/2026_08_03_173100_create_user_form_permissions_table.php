<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_form_permissions')) {
            Schema::create('user_form_permissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('permission_key', 120);
                $table->boolean('can_view')->default(false);
                $table->boolean('can_create')->default(false);
                $table->boolean('can_update')->default(false);
                $table->boolean('can_delete')->default(false);
                $table->boolean('can_print')->default(false);
                $table->timestamps();

                $table->unique(['user_id', 'permission_key'], 'ufp_user_permission_unique');
                $table->index('permission_key', 'ufp_permission_key_index');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_form_permissions');
    }
};
