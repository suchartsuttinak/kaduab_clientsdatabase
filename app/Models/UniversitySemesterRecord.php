<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\UniversityCreditCalculator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UniversitySemesterRecord extends Model
{
    protected $fillable = [
        'enrollment_id', 'education_record_id', 'semester_id', 'academic_year', 'term', 'year_level',
        'record_date', 'registered_credits', 'earned_credits', 'cumulative_credits', 'semester_gpa',
        'cumulative_gpa', 'academic_status', 'risk_level', 'risk_note', 'semester_summary',
    ];

    protected $casts = [
        'record_date' => 'date',
        'registered_credits' => 'decimal:2',
        'earned_credits' => 'decimal:2',
        'cumulative_credits' => 'decimal:2',
        'semester_gpa' => 'decimal:2',
        'cumulative_gpa' => 'decimal:2',
    ];

    public function enrollment(): BelongsTo { return $this->belongsTo(UniversityEnrollment::class, 'enrollment_id'); }
    public function educationRecord(): BelongsTo { return $this->belongsTo(EducationRecord::class); }
    public function semester(): BelongsTo { return $this->belongsTo(Semester::class); }
    public function subjects(): HasMany { return $this->hasMany(UniversitySubjectResult::class, 'semester_record_id'); }
    public function followups(): HasMany { return $this->hasMany(UniversityFollowup::class, 'semester_record_id'); }
    public function documents(): HasMany { return $this->hasMany(UniversitySemesterDocument::class, 'semester_record_id'); }

    public function getCalculatedGpaAttribute(): ?string
    {
        $subjects = $this->relationLoaded('subjects')
            ? $this->subjects
            : $this->subjects()->get(['credits', 'grade_point']);

        $gradedCredits = 0.0;
        $qualityPoints = 0.0;

        foreach ($subjects as $subject) {
            if ($subject->credits === null || $subject->credits === '' ||
                $subject->grade_point === null || $subject->grade_point === '') {
                continue;
            }

            $credits = (float) $subject->credits;
            $gradePoint = (float) $subject->grade_point;

            if ($credits <= 0) {
                continue;
            }

            $gradedCredits += $credits;
            $qualityPoints += ($credits * $gradePoint);
        }

        if ($gradedCredits <= 0) {
            return null;
        }

        return number_format($qualityPoints / $gradedCredits, 2, '.', '');
    }

    public function getDisplayGpaAttribute(): ?string
    {
        $gpa = $this->semester_gpa;

        if ($gpa === null || $gpa === '') {
            $gpa = $this->educationRecord?->grade_average;
        }

        if ($gpa === null || $gpa === '') {
            $gpa = $this->calculated_gpa;
        }

        return $gpa === null || $gpa === ''
            ? null
            : number_format((float) $gpa, 2, '.', '');
    }

    public function getCalculatedGpaxAttribute(): ?string
    {
        if (!$this->enrollment_id || !$this->academic_year || !$this->term) {
            return null;
        }

        $records = self::query()
            ->where('enrollment_id', $this->enrollment_id)
            ->where(function ($query) {
                $query->where('academic_year', '<', $this->academic_year)
                    ->orWhere(function ($sameYear) {
                        $sameYear->where('academic_year', $this->academic_year)
                            ->where('term', '<=', $this->term);
                    });
            })
            ->with(['subjects:id,semester_record_id,credits,grade_point'])
            ->get(['id', 'enrollment_id', 'academic_year', 'term']);

        $gradedCredits = 0.0;
        $qualityPoints = 0.0;

        foreach ($records as $record) {
            foreach ($record->subjects as $subject) {
                if ($subject->credits === null || $subject->credits === '' ||
                    $subject->grade_point === null || $subject->grade_point === '') {
                    continue;
                }

                $credits = (float) $subject->credits;
                $gradePoint = (float) $subject->grade_point;

                if ($credits <= 0) {
                    continue;
                }

                $gradedCredits += $credits;
                $qualityPoints += ($credits * $gradePoint);
            }
        }

        if ($gradedCredits <= 0) {
            return null;
        }

        return number_format($qualityPoints / $gradedCredits, 2, '.', '');
    }

    public function getDisplayGpaxAttribute(): ?string
    {
        $gpax = $this->cumulative_gpa;

        if ($gpax === null || $gpax === '') {
            $gpax = $this->calculated_gpax;
        }

        return $gpax === null || $gpax === ''
            ? null
            : number_format((float) $gpax, 2, '.', '');
    }

    // UNIVERSITY_CREDIT_AUTO_ACCESSORS_V1
    public function getCalculatedRegisteredCreditsAttribute(): ?string
    {
        $value = UniversityCreditCalculator::semesterSummary($this)['registered_credits'];

        return $value === null
            ? null
            : number_format((float) $value, 2, '.', '');
    }

    public function getCalculatedEarnedCreditsAttribute(): ?string
    {
        $value = UniversityCreditCalculator::semesterSummary($this)['earned_credits'];

        return $value === null
            ? null
            : number_format((float) $value, 2, '.', '');
    }

    public function getCalculatedCumulativeCreditsAttribute(): ?string
    {
        $value = UniversityCreditCalculator::cumulativeEarnedCredits($this);

        return $value === null
            ? null
            : number_format((float) $value, 2, '.', '');
    }

    public function getDisplayRegisteredCreditsAttribute(): ?string
    {
        $value = $this->calculated_registered_credits;

        if ($value === null || $value === '') {
            $value = $this->registered_credits;
        }

        return $value === null || $value === ''
            ? null
            : number_format((float) $value, 2, '.', '');
    }

    public function getDisplayEarnedCreditsAttribute(): ?string
    {
        $value = $this->calculated_earned_credits;

        if ($value === null || $value === '') {
            $value = $this->earned_credits;
        }

        return $value === null || $value === ''
            ? null
            : number_format((float) $value, 2, '.', '');
    }

    public function getDisplayCumulativeCreditsAttribute(): ?string
    {
        $value = $this->calculated_cumulative_credits;

        if ($value === null || $value === '') {
            $value = $this->cumulative_credits;
        }

        return $value === null || $value === ''
            ? null
            : number_format((float) $value, 2, '.', '');
    }

}
