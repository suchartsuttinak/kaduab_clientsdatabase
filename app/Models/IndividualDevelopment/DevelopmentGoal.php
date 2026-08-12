<?php

namespace App\Models\IndividualDevelopment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DevelopmentGoal extends Model
{
    use SoftDeletes;

    public const STATUS_NOT_STARTED = 'not_started';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_ACHIEVED = 'achieved';
    public const STATUS_CANCELLED = 'cancelled';

    protected $table = 'individual_development_goals';

    protected $fillable = [
        'plan_id', 'domain_id', 'indicator_id', 'title', 'description',
        'baseline_level', 'target_level', 'success_indicator', 'measurement_method',
        'target_value', 'target_unit', 'target_date', 'priority', 'status',
        'sort_order', 'responsible_user_id', 'responsible_name', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'baseline_level' => 'integer',
            'target_level' => 'integer',
            'target_value' => 'decimal:2',
            'target_date' => 'date',
            'sort_order' => 'integer',
        ];
    }

    public function plan(): BelongsTo { return $this->belongsTo(DevelopmentPlan::class, 'plan_id'); }
    public function domain(): BelongsTo { return $this->belongsTo(DevelopmentDomain::class, 'domain_id'); }
    public function indicator(): BelongsTo { return $this->belongsTo(DevelopmentIndicator::class, 'indicator_id'); }
    public function responsibleUser(): BelongsTo { return $this->belongsTo(User::class, 'responsible_user_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function activities(): HasMany { return $this->hasMany(DevelopmentActivity::class, 'goal_id')->orderBy('activity_date'); }
}