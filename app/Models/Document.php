<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Document extends Model
{
    protected $guarded = [];

    public function factfindings(): BelongsToMany
    {
        return $this->belongsToMany(
            Factfinding::class,
            'factfinding_documents',
            'document_id',
            'factfinding_id'
        )->withTimestamps();
    }
}
