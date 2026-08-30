<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObserveReferralRound extends Model
{
    use HasFactory;

    protected $table = 'observe_referral_rounds';

    protected $fillable = [
        'observe_id',
        'round_no',
        'action_date',
        'assistance_process',
        'solution',
        'result',
        'risk_level',
        'risk_detail',
        'status',
        'next_appointment_date',
        'followup_focus',
        'recorder_user_id',
        'recorder_name',
    ];

    protected $casts = [
        'action_date' => 'date',
        'next_appointment_date' => 'date',
    ];

    public function observeRelation()
    {
        return $this->belongsTo(Observe::class, 'observe_id');
    }
}
