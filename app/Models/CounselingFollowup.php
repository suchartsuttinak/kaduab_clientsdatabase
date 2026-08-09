<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounselingFollowup extends Model
{
    use HasFactory;

    protected $fillable = [
        'counseling_id',
        'followup_no',
        'followup_date',
        'followup_method',
        'location',
        'topic',
        'progress',
        'changes',
        'barriers',
        'current_assessment',
        'session_goal',
        'interventions',
        'advice',
        'agreement',
        'additional_support',
        'result',
        'risk_level',
        'risk_detail',
        'next_action',
        'next_appointment_date',
        'status',
        'recorder_user_id',
        'recorder_name',
        'updated_by',
    ];

    protected $casts = [
        'followup_date'         => 'date',
        'next_appointment_date' => 'date',
        'followup_no'           => 'integer',
    ];

    public function counseling(): BelongsTo
    {
        return $this->belongsTo(Counseling::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorder_user_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getRoundNoAttribute(): int
    {
        return ((int) $this->followup_no) + 1;
    }

    public function getFollowupMethodLabelAttribute(): string
    {
        return match ($this->followup_method) {
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
}
