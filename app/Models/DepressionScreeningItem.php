<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepressionScreeningItem extends Model
{
    protected $fillable = [
        'depression_screening_id',
        'item_no',
        'question',
        'score',
        'choice_text',
        'is_reverse',
    ];

    protected $casts = [
        'score' => 'integer',
        'is_reverse' => 'boolean',
    ];

    public function screening()
    {
        return $this->belongsTo(DepressionScreening::class, 'depression_screening_id');
    }
}