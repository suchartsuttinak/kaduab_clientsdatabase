<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniversitySemesterDocument extends Model
{
    protected $fillable = [
        'semester_record_id', 'education_record_id', 'document_type', 'original_name', 'stored_name',
        'file_path', 'mime_type', 'file_size', 'sha256', 'uploaded_by', 'uploaded_at',
    ];

    protected $casts = ['uploaded_at' => 'datetime'];

    public function semesterRecord(): BelongsTo { return $this->belongsTo(UniversitySemesterRecord::class, 'semester_record_id'); }
    public function educationRecord(): BelongsTo { return $this->belongsTo(EducationRecord::class); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
}
