<?php

namespace App\Models\IndividualDevelopment;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DevelopmentPlan extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_REVIEW = 'review';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $table = 'individual_development_plans';

    protected $fillable = [
        'client_id', 'plan_no', 'start_date', 'end_date', 'overall_goal',
        'strength_summary', 'strength_profile', 'development_need_summary', 'needs_profile', 'client_need_summary',
        'caregiver_need_summary', 'risk_factor_summary', 'protective_factor_summary',
        'support_network_summary', 'support_network_profile', 'discharge_plan_profile', 'status', 'created_by', 'updated_by',
        'reviewed_by', 'reviewed_at', 'closed_by', 'closed_at', 'close_reason',
        'final_outcome', 'final_recommendation',
    ];

    protected function casts(): array
    {
        return [
            'plan_no' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'reviewed_at' => 'datetime',
            'closed_at' => 'datetime',
            'strength_profile' => 'array',
            'needs_profile' => 'array',
            'support_network_profile' => 'array',
            'discharge_plan_profile' => 'array',
        ];
    }

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function closer(): BelongsTo { return $this->belongsTo(User::class, 'closed_by'); }
    public function assessments(): HasMany { return $this->hasMany(DevelopmentAssessment::class, 'plan_id'); }
    public function goals(): HasMany { return $this->hasMany(DevelopmentGoal::class, 'plan_id')->orderBy('sort_order'); }
    public function followups(): HasMany { return $this->hasMany(DevelopmentFollowup::class, 'plan_id')->orderBy('followup_no'); }
    public function evidences(): HasMany { return $this->hasMany(DevelopmentEvidence::class, 'plan_id'); }
}