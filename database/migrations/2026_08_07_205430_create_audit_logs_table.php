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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // ใช้เชื่อมเหตุการณ์ที่เกิดจาก Request เดียวกัน
            $table->string('request_id', 36)
                ->nullable()
                ->index();

            // ผู้ใช้งานที่ทำรายการ
            // ไม่ทำ Foreign Key เพื่อให้ประวัติยังคงอยู่
            // แม้ภายหลังบัญชีผู้ใช้งานจะถูกลบ
            $table->unsignedBigInteger('user_id')
                ->nullable()
                ->index();

            // LOGIN, VIEW, CREATE, UPDATE, DELETE ฯลฯ
            $table->string('action', 50)
                ->index();

            // เช่น accident, medical, client, permission
            $table->string('module', 100)
                ->nullable()
                ->index();

            // ผู้รับบริการที่เกี่ยวข้อง
            // ไม่ผูก Foreign Key เพื่อรักษาประวัติย้อนหลัง
            $table->unsignedBigInteger('client_id')
                ->nullable()
                ->index();

            // Model/ประเภทข้อมูล เช่น App\Models\Accident
            $table->string('subject_type', 150)
                ->nullable();

            // ID ของรายการที่ถูกกระทำ
            $table->unsignedBigInteger('subject_id')
                ->nullable();

            // Route ที่ใช้งาน
            $table->string('route_name', 255)
                ->nullable();

            // GET / POST / PUT / PATCH / DELETE
            $table->string('http_method', 10)
                ->nullable();

            // รองรับ IPv4 และ IPv6
            $table->string('ip_address', 45)
                ->nullable();

            // ไม่เก็บ User-Agent เต็ม ๆ
            // เก็บ SHA-256 แทน
            $table->char('user_agent_hash', 64)
                ->nullable();

            // บันทึกเฉพาะ "ชื่อ field ที่เปลี่ยน"
            // ไม่เก็บค่าข้อมูลส่วนบุคคล
            $table->json('changed_fields')
                ->nullable();

            // success / failed / denied
            $table->string('result', 20)
                ->default('success')
                ->index();

            // เช่น 200, 302, 403, 422, 500
            $table->unsignedSmallInteger('status_code')
                ->nullable();

            // ข้อมูลประกอบที่ไม่ใช่ข้อมูลส่วนบุคคล
            $table->json('metadata')
                ->nullable();

            // Audit Log ไม่มี updated_at
            // เพราะเมื่อบันทึกแล้วไม่ควรแก้ไข
            $table->timestamp('created_at')
                ->useCurrent()
                ->index();

            // ช่วยให้ค้นหาประวัติได้เร็วขึ้น
            $table->index(
                ['module', 'client_id', 'created_at'],
                'audit_logs_module_client_created_idx'
            );

            $table->index(
                ['user_id', 'created_at'],
                'audit_logs_user_created_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};