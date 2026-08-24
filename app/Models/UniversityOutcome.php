<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UniversityOutcome extends Model
{
    protected $fillable = [
        'enrollment_id', 'outcome_type', 'outcome_date', 'academic_year', 'semester_id', 'final_gpa',
        'degree_name', 'honors', 'post_graduation_status', 'post_graduation_detail', 'summary',
    ];

    protected $casts = [
        'outcome_date' => 'date',
        'final_gpa' => 'decimal:2',
    ];

    public function enrollment(): BelongsTo { return $this->belongsTo(UniversityEnrollment::class, 'enrollment_id'); }
    public function semester(): BelongsTo { return $this->belongsTo(Semester::class); }
    public function reasons(): HasMany { return $this->hasMany(UniversityOutcomeReason::class, 'outcome_id'); }
}
