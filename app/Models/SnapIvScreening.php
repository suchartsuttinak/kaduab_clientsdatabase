<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnapIvScreening extends Model
{
    protected $fillable = [
        'client_id',
        'created_by',
        'screening_date',
        'observer_name',
        'relationship',
        'age_text',
        'class_level',
        'term',
        'grade_average',
        'inattention_score',
        'hyperactivity_score',
        'oppositional_score',
        'total_score',
        'inattention_level',
        'hyperactivity_level',
        'oppositional_level',
        'summary',
        'recommendation',
        'remark',
    ];

    protected $casts = [
        'screening_date' => 'date',
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
        return $this->hasMany(SnapIvScreeningItem::class);
    }
}