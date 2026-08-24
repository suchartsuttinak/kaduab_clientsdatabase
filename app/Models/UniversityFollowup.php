<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UniversityFollowup extends Model
{
    protected $fillable = [
        'enrollment_id',
        'semester_record_id',
        'semester_id',
        'academic_year',
        'sequence_no',
        'followup_date',
        'followup_method',
        'informant',
        'overall_risk_level',
        'academic_progress',
        'adaptation_status',
        'financial_status',
        'wellbeing_motivation',
        'continuation_risk_note',
        'general_condition',
        'strengths',
        'assistance_summary',
        'next_plan',
        'next_followup_date',
        'followed_by',
    ];

    protected $casts = [
        'followup_date' => 'date',
        'next_followup_date' => 'date',
        'sequence_no' => 'integer',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(UniversityEnrollment::class, 'enrollment_id');
    }

    public function semesterRecord(): BelongsTo
    {
        return $this->belongsTo(UniversitySemesterRecord::class, 'semester_record_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'followed_by');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(UniversityFollowupIssue::class, 'followup_id');
    }
}
