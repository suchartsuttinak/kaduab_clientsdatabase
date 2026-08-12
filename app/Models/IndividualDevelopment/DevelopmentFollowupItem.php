<?php

namespace App\Models\IndividualDevelopment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevelopmentFollowupItem extends Model
{
    protected $table = 'individual_development_followup_items';

    protected $fillable = [
        'followup_id', 'indicator_id', 'previous_score', 'score', 'evidence', 'development_note',
    ];

    protected function casts(): array
    {
        return ['previous_score' => 'integer', 'score' => 'integer'];
    }

    public function followup(): BelongsTo { return $this->belongsTo(DevelopmentFollowup::class, 'followup_id'); }
    public function indicator(): BelongsTo { return $this->belongsTo(DevelopmentIndicator::class, 'indicator_id'); }
}