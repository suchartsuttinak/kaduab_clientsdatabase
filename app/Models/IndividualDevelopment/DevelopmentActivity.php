<?php

namespace App\Models\IndividualDevelopment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DevelopmentActivity extends Model
{
    use SoftDeletes;

    public const STATUS_PLANNED = 'planned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $table = 'individual_development_activities';

    protected $fillable = [
        'goal_id', 'activity_date', 'end_date', 'activity_type', 'detail', 'frequency',
        'status', 'completed_at', 'cancel_reason', 'cancelled_at', 'cancelled_by',
        'responsible_user_id', 'responsible_name', 'result', 'problem',
        'next_action', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'end_date' => 'date',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function goal(): BelongsTo { return $this->belongsTo(DevelopmentGoal::class, 'goal_id'); }
    public function responsibleUser(): BelongsTo { return $this->belongsTo(User::class, 'responsible_user_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function evidences(): MorphMany { return $this->morphMany(DevelopmentEvidence::class, 'evidenceable'); }
}