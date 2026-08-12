<?php

namespace App\Models\IndividualDevelopment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DevelopmentDomain extends Model
{
    protected $fillable = ['code', 'name', 'description', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'is_active' => 'boolean'];
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(DevelopmentIndicator::class, 'domain_id')->orderBy('sort_order');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(DevelopmentGoal::class, 'domain_id');
    }
}
