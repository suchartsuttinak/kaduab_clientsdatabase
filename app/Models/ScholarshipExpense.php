<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipExpense extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'record_date'  => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * เด็กผู้ได้รับทุน
     */
    public function child()
    {
        return $this->belongsTo(
            ScholarshipChild::class,
            'scholarship_child_id'
        );
    }

    /**
     * รายการค่าใช้จ่ายย่อย
     */
    public function items()
    {
        return $this->hasMany(ScholarshipExpenseItem::class);
    }

    /**
     * ไฟล์เอกสารประกอบ
     */
    public function attachments()
    {
        return $this->hasMany(ScholarshipExpenseAttachment::class);
    }

    /**
     * ผู้สร้างข้อมูล
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * ผู้แก้ไขข้อมูลล่าสุด
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}