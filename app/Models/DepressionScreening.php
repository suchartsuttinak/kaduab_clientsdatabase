<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepressionScreening extends Model
{
    protected $fillable = [
        'client_id',
        'created_by',
        'screening_date',
        'observer_name',
        'age_text',
        'class_level',
        'total_score',
        'result_level',
        'summary',
        'recommendation',
        'remark',
    ];

    protected $casts = [
        'screening_date' => 'date',
        'total_score' => 'integer',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(DepressionScreeningItem::class);
    }
}