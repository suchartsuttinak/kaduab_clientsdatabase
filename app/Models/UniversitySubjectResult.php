<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniversitySubjectResult extends Model
{
    protected $fillable = [
        'semester_record_id', 'course_code', 'course_name', 'credits', 'grade', 'grade_point',
        'result_status', 'note',
    ];

    protected $casts = [
        'credits' => 'decimal:2',
        'grade_point' => 'decimal:2',
    ];

    public function semesterRecord(): BelongsTo { return $this->belongsTo(UniversitySemesterRecord::class, 'semester_record_id'); }
}
