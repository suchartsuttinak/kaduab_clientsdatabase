<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Counseling extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'session_no',
        'session_date',
        'counselor_user_id',
        'counselor_name',
        'channel',
        'location',
        'presenting_problem',
        'assessment',
        'strengths_resources',
        'goals',
        'interventions',
        'advice',
        'agreement',
        'outcome',
        'next_steps',
        'risk_level',
        'risk_detail',
        'needs_followup',
        'next_appointment_date',
        'followup_focus',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'session_date'          => 'date',
        'next_appointment_date' => 'date',
        'needs_followup'        => 'boolean',
        'session_no'            => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function followups(): HasMany
    {
        return $this->hasMany(CounselingFollowup::class)
            ->orderBy('followup_no');
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getChannelLabelAttribute(): string
    {
        return match ($this->channel) {
            'face_to_face' => 'พบโดยตรง',
            'phone'        => 'โทรศัพท์',
            'online'       => 'ออนไลน์',
            'home_visit'   => 'เยี่ยมบ้าน/สถานที่พัก',
            'other'        => 'อื่น ๆ',
            default        => '-',
        };
    }

    public function getRiskLevelLabelAttribute(): string
    {
        return match ($this->risk_level) {
            'none'     => 'ไม่พบความเสี่ยง',
            'low'      => 'ความเสี่ยงต่ำ',
            'moderate' => 'ความเสี่ยงปานกลาง',
            'high'     => 'ความเสี่ยงสูง',
            default    => '-',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'ongoing'   => 'อยู่ระหว่างให้คำปรึกษา',
            'follow_up' => 'อยู่ระหว่างติดตาม',
            'goal_met'  => 'บรรลุเป้าหมาย',
            'referred'  => 'ส่งต่อ',
            'closed'    => 'ยุติการให้คำปรึกษา',
            'improved'  => 'อยู่ระหว่างให้คำปรึกษา',
            default     => '-',
        };
    }

    public function getIsClosedAttribute(): bool
    {
        return in_array(
            $this->status,
            ['goal_met', 'referred', 'closed'],
            true
        );
    }
}
