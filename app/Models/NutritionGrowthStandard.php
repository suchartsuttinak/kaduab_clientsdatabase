<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionGrowthStandard extends Model
{
    protected $fillable = [
        'gender',
        'age_month',
        'height_cm',
        'standard_type',
        'sd_minus_3',
        'sd_minus_2',
        'sd_minus_1_5',
        'median',
        'sd_plus_1_5',
        'sd_plus_2',
        'sd_plus_3',
    ];

    protected $casts = [
        'age_month'     => 'integer',
        'height_cm'     => 'decimal:2',
        'sd_minus_3'    => 'decimal:2',
        'sd_minus_2'    => 'decimal:2',
        'sd_minus_1_5'  => 'decimal:2',
        'median'        => 'decimal:2',
        'sd_plus_1_5'   => 'decimal:2',
        'sd_plus_2'     => 'decimal:2',
        'sd_plus_3'     => 'decimal:2',
    ];
}