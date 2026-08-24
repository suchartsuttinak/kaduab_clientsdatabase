<?php

namespace App\Models\IndividualDevelopment;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientDocumentStatus extends Model
{
    protected $table = 'client_document_statuses';

    protected $fillable = ['client_id', 'document_type', 'status', 'expires_at', 'note', 'updated_by'];

    protected function casts(): array
    {
        return ['expires_at' => 'date'];
    }

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
}
