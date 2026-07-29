<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * สร้างตารางรายการค่าใช้จ่ายทุนการศึกษา
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ตารางหัวรายการค่าใช้จ่าย
        |--------------------------------------------------------------------------
        */
        Schema::create('scholarship_expenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('scholarship_child_id')
                ->constrained('scholarship_children')
                ->cascadeOnDelete();

            $table->date('record_date')->index();

            // เก็บปีการศึกษาเดิมของเด็กในวันที่บันทึกรายการ
            $table->string('academic_year', 4)->index();

            // ภาคเรียนที่ 1 หรือ 2
            $table->unsignedTinyInteger('semester');

            // ยอดรวมของรายการค่าใช้จ่ายทั้งหมด
            $table->decimal('total_amount', 12, 2)->default(0);

            $table->text('note')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(
                [
                    'scholarship_child_id',
                    'academic_year',
                    'semester',
                ],
                'scholarship_expenses_child_year_semester_index'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | ตารางรายการค่าใช้จ่ายย่อย
        |--------------------------------------------------------------------------
        | การบันทึกหนึ่งครั้งสามารถมีหลายรายการ เช่น
        | ค่าเทอม + ค่าอาหาร + ค่าอุปกรณ์การเรียน
        */
        Schema::create('scholarship_expense_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('scholarship_expense_id')
                ->constrained('scholarship_expenses')
                ->cascadeOnDelete();

            $table->string('expense_type', 100);

            $table->decimal('amount', 12, 2);

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | ตารางไฟล์ PDF
        |--------------------------------------------------------------------------
        | รองรับเอกสารรายการค่าใช้จ่ายและผลการเรียนหลายไฟล์
        */
        Schema::create('scholarship_expense_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('scholarship_expense_id')
                ->constrained('scholarship_expenses')
                ->cascadeOnDelete();

            /*
             * expense_document = เอกสารรายการค่าใช้จ่าย
             * grade_report     = ผลการเรียน
             */
            $table->string('category', 30)->index();

            $table->string('file_path');

            $table->string('original_name');

            $table->string('mime_type', 100)->nullable();

            $table->unsignedBigInteger('file_size')->nullable();

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * ลบตารางเมื่อ Rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarship_expense_attachments');

        Schema::dropIfExists('scholarship_expense_items');

        Schema::dropIfExists('scholarship_expenses');
    }
};