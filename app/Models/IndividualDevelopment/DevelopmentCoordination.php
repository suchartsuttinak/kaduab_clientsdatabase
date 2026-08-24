<?php

namespace App\Models\IndividualDevelopment;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevelopmentCoordination extends Model
{
    protected $table = 'individual_development_coordinations';

    protected $fillable = [
        'client_id', 'plan_id', 'coordination_date', 'agency_name', 'subject',
        'coordinator_name', 'result', 'next_appointment_date', 'document_note',
        'status', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'coordination_date' => 'date',
            'next_appointment_date' => 'date',
        ];
    }

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function plan(): BelongsTo { return $this->belongsTo(DevelopmentPlan::class, 'plan_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
}
