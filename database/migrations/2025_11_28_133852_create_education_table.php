
<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * ไม่ดำเนินการ เนื่องจากระบบใช้ตาราง education_levels แทน
     */
    public function up(): void
    {
        // ไม่ต้องสร้างตาราง education
    }

    /**
     * ไม่มีสิ่งที่ต้องย้อนกลับ
     */
    public function down(): void
    {
        // ไม่ต้องดำเนินการ
    }
};