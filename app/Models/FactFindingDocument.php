<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Factfinding extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'receive_date' => 'date',
        'sick' => 'boolean',
        'active' => 'boolean',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(
            Document::class,
            'factfinding_documents',
            'factfinding_id',
            'document_id'
        )->withTimestamps();
    }

    public function marital(): BelongsTo
    {
        return $this->belongsTo(Marital::class, 'marital_id');
    }
}
