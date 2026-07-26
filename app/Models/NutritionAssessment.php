<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionAssessment extends Model
{
    protected $fillable = [
        'client_id',
        'assessment_date',
        'birth_date',
        'age_year',
        'age_month',
        'gender',
        'height_cm',
        'weight_kg',
        'ibw',
        'ibw_percent',
        'ha_median',
        'ha_percent',
        'height_result',
        'weight_result',
        'summary_result',
        'note',
        'created_by',
        'updated_by',
        'bmi',
        'bmi_result',
        'nutrition_status',
        'height_for_age_result',
        'weight_for_height_result',
    ];

        protected $casts = [
        'assessment_date' => 'date',
        'birth_date' => 'date',
        'height_cm' => 'decimal:2',
        'weight_kg' => 'decimal:2',
        'bmi' => 'decimal:2',
        'ibw' => 'decimal:2',
        'ibw_percent' => 'decimal:2',
        'ha_median' => 'decimal:2',
        'ha_percent' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}