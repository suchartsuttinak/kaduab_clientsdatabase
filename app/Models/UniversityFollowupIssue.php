<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniversityFollowupIssue extends Model
{
    protected $fillable = ['followup_id', 'category', 'severity', 'detail', 'assistance', 'issue_status'];
    public function followup(): BelongsTo { return $this->belongsTo(UniversityFollowup::class, 'followup_id'); }
}
