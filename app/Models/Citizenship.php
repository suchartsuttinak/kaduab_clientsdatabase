<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Citizenship extends Model
{
    protected $guarded = [];

    /**
     * ความสัมพันธ์กับระบบบุคคลไม่มีสถานะทางทะเบียน
     * (รายการทางทะเบียน)
     */
    public function idstations()
    {
        return $this->belongsToMany(
            Idstation::class,
            'citizenship_idstation',
            'citizenship_id',
            'idstation_id'
        )->withTimestamps();
    }
}