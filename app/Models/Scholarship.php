<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    protected $fillable = [
        'fullname',
        'phone',
        'email',
        'support_types',
        'detail',

        // สำหรับระบบแจ้งเตือน
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'support_types' => 'array',
        'is_read'       => 'boolean',
        'read_at'       => 'datetime',
    ];

    // 1 ผู้สนับสนุน มีหลายการบริจาค
    public function donations()
    {
        return $this->hasMany(ScholarshipDonation::class);
    }
}