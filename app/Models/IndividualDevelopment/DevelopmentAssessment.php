<?php

namespace App\Models\IndividualDevelopment;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DevelopmentAssessment extends Model
{
    use SoftDeletes;

    public const TYPE_BASELINE = 'baseline';
    public const TYPE_REVIEW = 'review';
    public const TYPE_FINAL = 'final';

    protected $table = 'individual_development_assessments';

    protected $fillable = [
        'plan_id', 'client_id', 'assessment_type', 'round_no', 'assessment_date',
        'assessed_by', 'information_sources', 'participant_note', 'overall_note',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'round_no' => 'integer',
            'assessment_date' => 'date',
            'information_sources' => 'array',
        ];
    }

    public function plan(): BelongsTo { return $this->belongsTo(DevelopmentPlan::class, 'plan_id'); }
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function assessor(): BelongsTo { return $this->belongsTo(User::class, 'assessed_by'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function items(): HasMany { return $this->hasMany(DevelopmentAssessmentItem::class, 'assessment_id'); }
    public function evidences(): MorphMany { return $this->morphMany(DevelopmentEvidence::class, 'evidenceable'); }
}