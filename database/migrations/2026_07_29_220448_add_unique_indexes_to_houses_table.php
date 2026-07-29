<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('houses', function (Blueprint $table) {
            $table->unique(
                'house_name',
                'houses_house_name_unique'
            );

            $table->unique(
                'house_alias',
                'houses_house_alias_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('houses', function (Blueprint $table) {
            $table->dropUnique('houses_house_name_unique');
            $table->dropUnique('houses_house_alias_unique');
        });
    }
};