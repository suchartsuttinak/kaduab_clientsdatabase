<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipExpenseItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * หัวรายการค่าใช้จ่าย
     */
    public function expense()
    {
        return $this->belongsTo(ScholarshipExpense::class);
    }
}