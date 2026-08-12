<?php

namespace App\Models\IndividualDevelopment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevelopmentAssessmentItem extends Model
{
    protected $table = 'individual_development_assessment_items';

    protected $fillable = ['assessment_id', 'indicator_id', 'score', 'evidence', 'development_note'];

    protected function casts(): array { return ['score' => 'integer']; }

    public function assessment(): BelongsTo { return $this->belongsTo(DevelopmentAssessment::class, 'assessment_id'); }
    public function indicator(): BelongsTo { return $this->belongsTo(DevelopmentIndicator::class, 'indicator_id'); }
}
