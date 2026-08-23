<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthcareRight extends Model
{
    public const STATUS_GOLD_CARD = 'สิทธิบัตรทอง';
    public const STATUS_DISABLED = 'สิทธิคนพิการ';
    public const STATUS_SOCIAL_SECURITY = 'สิทธิประกันสังคม';
    public const STATUS_CIVIL_SERVANT = 'สิทธิข้าราชการ';
    public const STATUS_UNREGISTERED = 'ยังไม่ได้ขึ้นทะเบียนสิทธิ';

    public const GOVERNMENT_HOSPITAL_TEXT = 'โรงพยาบาลรัฐทุกแห่งทั่วประเทศ';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'record_date' => 'date',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_GOLD_CARD,
            self::STATUS_DISABLED,
            self::STATUS_SOCIAL_SECURITY,
            self::STATUS_CIVIL_SERVANT,
            self::STATUS_UNREGISTERED,
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
