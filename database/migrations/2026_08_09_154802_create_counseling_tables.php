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
        /*
        |--------------------------------------------------------------------------
        | 1. ตารางการให้คำปรึกษาหลัก
        |--------------------------------------------------------------------------
        */
        Schema::create('counselings', function (Blueprint $table) {
            $table->id();

            /*
             * ผูกกับผู้รับบริการ
             * หาก client ถูกลบ counseling จะถูกลบตามอัตโนมัติ
             */
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            /*
             * ครั้งที่ให้คำปรึกษาของผู้รับบริการรายนั้น
             */
            $table->unsignedInteger('session_no');

            /*
             * วันที่ให้คำปรึกษา
             */
            $table->date('session_date');

            /*
             * ผู้ให้คำปรึกษา
             *
             * เก็บทั้ง user_id และชื่อ ณ วันที่บันทึก
             * เพื่อให้ประวัติเดิมยังอ่านได้แม้ภายหลังผู้ใช้ถูกลบ
             */
            $table->foreignId('counselor_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('counselor_name', 150)->nullable();

            /*
             * วิธี/ช่องทางการให้คำปรึกษา
             *
             * face_to_face = พบโดยตรง
             * phone        = โทรศัพท์
             * online       = ออนไลน์
             * home_visit   = เยี่ยมบ้าน/สถานที่พัก
             * other        = อื่น ๆ
             */
            $table->string('channel', 30)->default('face_to_face');

            /*
             * สถานที่ให้คำปรึกษา
             */
            $table->string('location', 255)->nullable();

            /*
             * ประเด็นหลักที่มารับคำปรึกษา
             */
            $table->text('presenting_problem');

            /*
             * การประเมินสภาพปัญหาเบื้องต้น
             */
            $table->text('assessment');

            /*
             * จุดแข็ง / ทรัพยากร / บุคคลสนับสนุน
             */
            $table->text('strengths_resources')->nullable();

            /*
             * เป้าหมายของการให้คำปรึกษา
             */
            $table->text('goals')->nullable();

            /*
             * วิธีการ / เทคนิค / การดำเนินการให้คำปรึกษา
             */
            $table->text('interventions')->nullable();

            /*
             * คำแนะนำที่ให้
             */
            $table->text('advice')->nullable();

            /*
             * ข้อตกลงร่วมกัน
             */
            $table->text('agreement')->nullable();

            /*
             * ผลของการให้คำปรึกษาครั้งนี้
             */
            $table->text('outcome')->nullable();

            /*
             * สิ่งที่ผู้รับคำปรึกษาต้องดำเนินการต่อ
             */
            $table->text('next_steps')->nullable();

            /*
             * ระดับความเสี่ยง
             *
             * none     = ไม่พบ
             * low      = ต่ำ
             * moderate = ปานกลาง
             * high     = สูง
             */
            $table->string('risk_level', 20)->default('none');

            /*
             * รายละเอียดความเสี่ยง
             */
            $table->text('risk_detail')->nullable();

            /*
             * ต้องติดตามผลหรือไม่
             */
            $table->boolean('needs_followup')->default(false);

            /*
             * นัดหมายครั้งต่อไป
             * สามารถเป็นอนาคตได้
             */
            $table->date('next_appointment_date')->nullable();

            /*
             * ประเด็นที่ต้องติดตามครั้งต่อไป
             */
            $table->text('followup_focus')->nullable();

            /*
             * สถานะของกระบวนการให้คำปรึกษา
             *
             * ongoing   = อยู่ระหว่างดำเนินการ
             * follow_up = อยู่ระหว่างติดตาม
             * improved  = มีพัฒนาการดีขึ้น
             * goal_met  = บรรลุเป้าหมาย
             * referred  = ส่งต่อ
             * closed    = ยุติการให้คำปรึกษา
             */
            $table->string('status', 30)->default('ongoing');

            /*
             * Audit เบื้องต้น
             */
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            /*
             * ป้องกันเลขครั้งซ้ำในผู้รับบริการรายเดียวกัน
             */
            $table->unique(
                ['client_id', 'session_no'],
                'counselings_client_session_unique'
            );

            /*
             * Index สำหรับหน้า List / Report / Filter
             */
            $table->index(
                ['client_id', 'session_date'],
                'counselings_client_date_index'
            );

            $table->index(
                ['client_id', 'status'],
                'counselings_client_status_index'
            );
        });


        /*
        |--------------------------------------------------------------------------
        | 2. ตารางการติดตามผลการให้คำปรึกษา
        |--------------------------------------------------------------------------
        */
        Schema::create('counseling_followups', function (Blueprint $table) {
            $table->id();

            /*
             * ผูกกับการให้คำปรึกษาหลัก
             *
             * หาก counseling ถูกลบ
             * followup ทุกครั้งจะถูกลบตาม
             */
            $table->foreignId('counseling_id')
                ->constrained('counselings')
                ->cascadeOnDelete();

            /*
             * ครั้งที่ติดตาม
             */
            $table->unsignedInteger('followup_no');

            /*
             * วันที่ติดตาม
             */
            $table->date('followup_date');

            /*
             * วิธีติดตาม
             */
            $table->string('followup_method', 30)
                ->default('face_to_face');

            /*
             * สถานที่ติดตาม
             */
            $table->string('location', 255)->nullable();

            /*
             * ความคืบหน้าหลังจากการให้คำปรึกษาครั้งก่อน
             */
            $table->text('progress');

            /*
             * การเปลี่ยนแปลงที่พบ
             */
            $table->text('changes')->nullable();

            /*
             * ปัญหา / อุปสรรค
             */
            $table->text('barriers')->nullable();

            /*
             * การประเมินสภาพปัจจุบัน
             */
            $table->text('current_assessment')->nullable();

            /*
             * การช่วยเหลือหรือคำแนะนำเพิ่มเติม
             */
            $table->text('additional_support')->nullable();

            /*
             * ผลการติดตาม
             */
            $table->text('result')->nullable();

            /*
             * แนวทางดำเนินการต่อ
             */
            $table->text('next_action')->nullable();

            /*
             * นัดหมายครั้งต่อไป
             */
            $table->date('next_appointment_date')->nullable();

            /*
             * สถานะหลังติดตาม
             */
            $table->string('status', 30)->default('follow_up');

            /*
             * ผู้บันทึกการติดตาม
             */
            $table->foreignId('recorder_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * Snapshot ชื่อผู้บันทึก
             */
            $table->string('recorder_name', 150)->nullable();

            /*
             * ผู้แก้ไขล่าสุด
             */
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            /*
             * ป้องกันเลขครั้งติดตามซ้ำ
             */
            $table->unique(
                ['counseling_id', 'followup_no'],
                'counseling_followups_no_unique'
            );

            /*
             * ช่วยให้เรียก Timeline ได้เร็ว
             */
            $table->index(
                ['counseling_id', 'followup_date'],
                'counseling_followups_date_index'
            );
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
         * ต้องลบตารางลูกก่อนตารางแม่
         */
        Schema::dropIfExists('counseling_followups');
        Schema::dropIfExists('counselings');
    }
};