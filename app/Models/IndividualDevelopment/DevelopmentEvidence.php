<?php

namespace App\Models\IndividualDevelopment;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DevelopmentEvidence extends Model
{
    use SoftDeletes;

    protected $table = 'individual_development_evidences';

    protected $fillable = [
        'client_id', 'plan_id', 'evidenceable_type', 'evidenceable_id', 'category',
        'original_name', 'stored_name', 'file_path', 'mime_type', 'file_size',
        'description', 'uploaded_by',
    ];

    protected function casts(): array
    {
        return ['file_size' => 'integer'];
    }

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function plan(): BelongsTo { return $this->belongsTo(DevelopmentPlan::class, 'plan_id'); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function evidenceable(): MorphTo { return $this->morphTo(); }
}