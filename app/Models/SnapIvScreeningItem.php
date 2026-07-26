<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnapIvScreeningItem extends Model
{
    protected $fillable = [
        'snap_iv_screening_id',
        'category',
        'item_no',
        'question',
        'score',
    ];

    public function screening()
    {
        return $this->belongsTo(SnapIvScreening::class, 'snap_iv_screening_id');
    }
}