<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Idstation extends Model
{
    protected $fillable = [
        'client_id',
        'receive_date',
        'detail',
        'process_status',
        'received_status_date',
        'remark',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'receive_date' => 'date',
        'received_status_date' => 'date',
    ];

    /**
     * ผู้รับบริการ
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * รายการทางทะเบียน
     */
    public function citizenships()
    {
        return $this->belongsToMany(
            Citizenship::class,
            'citizenship_idstation',
            'idstation_id',
            'citizenship_id'
        )->withTimestamps();
    }

    /**
     * สถานะทางทะเบียนที่ได้รับ
     */
    public function citizens()
    {
        return $this->belongsToMany(
            Citizen::class,
            'citizen_idstation',
            'idstation_id',
            'citizen_id'
        )->withTimestamps();
    }

    /**
     * ผู้สร้างข้อมูล
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * ผู้แก้ไขข้อมูล
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}