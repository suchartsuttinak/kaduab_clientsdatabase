<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ScholarshipChild extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $guarded = [];

    protected $casts = [
        'semester' => 'integer',
        'scholarship_status_updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ScholarshipChild $child) {
            if (!$child->person_uuid) {
                $child->person_uuid = Str::uuid()->toString();
            }

            if (!$child->semester) {
                $child->semester = 1;
            }

            if (!$child->scholarship_status) {
                $child->scholarship_status = self::STATUS_PENDING;
            }
        });
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'อยู่ระหว่างรอการพิจารณา',
            self::STATUS_APPROVED => 'อนุมัติทุนการศึกษา',
            self::STATUS_REJECTED => 'ไม่ผ่านการอนุมัติ',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusOptions()[$this->scholarship_status]
            ?? 'อยู่ระหว่างรอการพิจารณา';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->scholarship_status) {
            self::STATUS_APPROVED => 'sc-status-approved',
            self::STATUS_REJECTED => 'sc-status-rejected',
            default => 'sc-status-pending',
        };
    }

    public function getApplicationPeriodLabelAttribute(): string
    {
        return $this->academic_year
            . ' / ภาคเรียนที่ '
            . ($this->semester ?: '-');
    }

    public function isApproved(): bool
    {
        return $this->scholarship_status === self::STATUS_APPROVED;
    }

    public function scopeForPerson($query, ScholarshipChild|string $childOrUuid)
    {
        $personUuid = $childOrUuid instanceof ScholarshipChild
            ? $childOrUuid->person_uuid
            : $childOrUuid;

        return $query->where('person_uuid', $personUuid);
    }

    public function expenses()
    {
        return $this->hasMany(ScholarshipExpense::class);
    }

    /**
     * คำขอทุกปี/ภาคเรียนของบุคคลเดียวกัน รวมแถวปัจจุบันด้วย
     */
    public function personApplications()
    {
        return $this->hasMany(
            self::class,
            'person_uuid',
            'person_uuid'
        );
    }
}