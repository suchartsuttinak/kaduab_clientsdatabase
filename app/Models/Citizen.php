<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Citizen extends Model
{
    protected $guarded = [];

    /**
     * ความสัมพันธ์กับระบบบุคคลไม่มีสถานะทางทะเบียน
     * (ได้รับสถานะทางทะเบียน)
     */
    public function idstations()
    {
        return $this->belongsToMany(
            Idstation::class,
            'citizen_idstation',
            'citizen_id',
            'idstation_id'
        )->withTimestamps();
    }
}