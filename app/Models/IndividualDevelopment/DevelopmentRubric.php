<?php

namespace App\Models\IndividualDevelopment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevelopmentRubric extends Model
{
    protected $fillable = ['indicator_id', 'level', 'title', 'description', 'sort_order'];

    protected function casts(): array
    {
        return ['level' => 'integer', 'sort_order' => 'integer'];
    }

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(DevelopmentIndicator::class, 'indicator_id');
    }
}
