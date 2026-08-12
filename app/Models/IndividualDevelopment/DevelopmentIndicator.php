<?php

namespace App\Models\IndividualDevelopment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DevelopmentIndicator extends Model
{
    protected $fillable = ['domain_id', 'code', 'name', 'description', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'is_active' => 'boolean'];
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(DevelopmentDomain::class, 'domain_id');
    }

    public function rubrics(): HasMany
    {
        return $this->hasMany(DevelopmentRubric::class, 'indicator_id')->orderBy('level');
    }

    public function assessmentItems(): HasMany
    {
        return $this->hasMany(DevelopmentAssessmentItem::class, 'indicator_id');
    }

    public function followupItems(): HasMany
    {
        return $this->hasMany(DevelopmentFollowupItem::class, 'indicator_id');
    }
}
