<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObserveReferralAssignment extends Model
{
    use HasFactory;

    protected $table = 'observe_referral_assignments';

    protected $fillable = [
        'observe_id',
        'assigned_to_user_id',
        'assigned_by_user_id',
        'assigned_at',
        'accepted_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /**
     * ห้ามใช้ชื่อ observe() เพราะชนกับ Model::observe() ของ Laravel 12
     */
    public function observeRelation()
    {
        return $this->belongsTo(Observe::class, 'observe_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }
}
