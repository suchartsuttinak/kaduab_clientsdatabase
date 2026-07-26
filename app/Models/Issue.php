<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $fillable = [
    'fullname',
    'phone',
    'subject',
    'is_read',
    'read_at',
];
}