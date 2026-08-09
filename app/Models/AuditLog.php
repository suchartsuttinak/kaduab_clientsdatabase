<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    /**
     * Audit Log เป็นข้อมูลประวัติแบบบันทึกครั้งเดียว
     * ไม่มี updated_at
     */
    public const UPDATED_AT = null;

    /**
     * ป้องกันการกำหนดค่าที่ไม่ต้องการ
     */
    protected $fillable = [
        'request_id',
        'user_id',
        'action',
        'module',
        'client_id',
        'subject_type',
        'subject_id',
        'route_name',
        'http_method',
        'ip_address',
        'user_agent_hash',
        'changed_fields',
        'result',
        'status_code',
        'metadata',
    ];

    /**
     * แปลง JSON / วันที่ให้อัตโนมัติ
     */
    protected function casts(): array
    {
        return [
            'changed_fields' => 'array',
            'metadata'       => 'array',
            'created_at'     => 'datetime',
        ];
    }

    /**
     * ผู้ใช้งานที่ทำรายการ
     *
     * ใช้ belongsTo โดยไม่สร้าง Foreign Key
     * ดังนั้นหากบัญชีผู้ใช้ถูกลบ Audit Log จะยังอยู่
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * ผู้รับบริการที่เกี่ยวข้องกับเหตุการณ์
     *
     * ไม่บังคับ Foreign Key เพื่อรักษาประวัติย้อนหลัง
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Audit Log ไม่อนุญาตให้แก้ไขผ่าน Model ปกติ
     */
    public function save(array $options = []): bool
    {
        if ($this->exists) {
            return false;
        }

        return parent::save($options);
    }

    /**
     * Audit Log ไม่อนุญาตให้ลบผ่าน Model ปกติ
     */
    public function delete(): ?bool
    {
        return false;
    }
}