<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipExpenseAttachment extends Model
{
    use HasFactory;

    public const CATEGORY_EXPENSE_DOCUMENT = 'expense_document';

    public const CATEGORY_GRADE_REPORT = 'grade_report';

    protected $guarded = [];

    /**
     * หัวรายการค่าใช้จ่าย
     */
    public function expense()
    {
        return $this->belongsTo(ScholarshipExpense::class);
    }

    /**
     * ผู้ที่อัปโหลดไฟล์
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * ชื่อประเภทเอกสารภาษาไทย
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            self::CATEGORY_GRADE_REPORT => 'ผลการเรียน',
            default => 'เอกสารรายการค่าใช้จ่าย',
        };
    }
}