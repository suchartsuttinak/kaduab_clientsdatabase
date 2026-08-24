<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UniversityEnrollment extends Model
{
    protected $fillable = [
        'client_id', 'institution_id', 'university_name', 'student_code', 'faculty', 'major',
        'degree_name', 'program_type', 'admission_academic_year', 'admission_term', 'admission_date',
        'curriculum_years', 'expected_graduation_year', 'current_status', 'funding_type',
        'scholarship_name', 'scholarship_amount', 'note',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'scholarship_amount' => 'decimal:2',
    ];

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function institution(): BelongsTo { return $this->belongsTo(Institution::class); }
    public function semesterRecords(): HasMany { return $this->hasMany(UniversitySemesterRecord::class, 'enrollment_id'); }
    public function followups(): HasMany { return $this->hasMany(UniversityFollowup::class, 'enrollment_id'); }
    public function outcome(): HasOne { return $this->hasOne(UniversityOutcome::class, 'enrollment_id'); }
}
