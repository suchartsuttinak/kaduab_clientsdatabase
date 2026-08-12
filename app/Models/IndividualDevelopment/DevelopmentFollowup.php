<?php

namespace App\Models\IndividualDevelopment;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DevelopmentFollowup extends Model
{
    use SoftDeletes;

    public const RESULT_IMPROVED = 'improved';
    public const RESULT_STABLE = 'stable';
    public const RESULT_DECLINED = 'declined';
    public const RESULT_ACHIEVED = 'achieved';

    protected $table = 'individual_development_followups';

    protected $fillable = [
        'plan_id', 'client_id', 'followup_no', 'followup_date', 'followup_type',
        'follower_user_id', 'follower_name', 'current_situation', 'changes',
        'positive_changes', 'actions_taken', 'result', 'problem', 'client_feedback',
        'caregiver_feedback', 'overall_result', 'suggestion', 'next_action',
        'next_followup_date', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'followup_no' => 'integer',
            'followup_date' => 'date',
            'next_followup_date' => 'date',
        ];
    }

    public function plan(): BelongsTo { return $this->belongsTo(DevelopmentPlan::class, 'plan_id'); }
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function follower(): BelongsTo { return $this->belongsTo(User::class, 'follower_user_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function items(): HasMany { return $this->hasMany(DevelopmentFollowupItem::class, 'followup_id'); }
    public function evidences(): MorphMany { return $this->morphMany(DevelopmentEvidence::class, 'evidenceable'); }
}