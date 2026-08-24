<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniversityOutcomeReason extends Model
{
    protected $fillable = ['outcome_id', 'reason_code', 'is_primary', 'detail'];
    protected $casts = ['is_primary' => 'boolean'];
    public function outcome(): BelongsTo { return $this->belongsTo(UniversityOutcome::class, 'outcome_id'); }
}
