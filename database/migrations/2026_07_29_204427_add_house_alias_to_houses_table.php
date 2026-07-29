<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('houses', function (Blueprint $table) {
            $table->string('house_alias', 255)
                ->nullable()
                ->after('house_name')
                ->comment('ชื่อเรียกหรือชื่อเฉพาะของบ้าน');
        });
    }

    public function down(): void
    {
        Schema::table('houses', function (Blueprint $table) {
            $table->dropColumn('house_alias');
        });
    }
};