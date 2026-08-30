<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Observe extends Model
{
    use HasFactory;

    protected $table = 'observes';

    protected $fillable = [
        'date',
        'behavior',
        'cause',
        'solution',
        'action',
        'obstacles',
        'result',
        'record_date',
        'recorder',
        'misbehavior_id',
        'client_id',
        'risk_level',
        'risk_detail',
        'status',
        'next_appointment_date',
        'followup_focus',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function misbehavior()
    {
        return $this->belongsTo(Misbehavior::class, 'misbehavior_id');
    }

    public function followups()
    {
        return $this->hasMany(ObserveFollowup::class, 'observe_id');
    }

    public function referralRounds()
    {
        return $this->hasMany(ObserveReferralRound::class, 'observe_id')
            ->orderBy('round_no')
            ->orderBy('action_date')
            ->orderBy('id');
    }
}
