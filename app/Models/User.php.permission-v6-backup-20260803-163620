<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public const ROLE_ADMIN = 'admin';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_EXECUTIVE = 'executive';
    public const ROLE_SOCIAL_WORKER = 'social_worker';
    public const ROLE_TEACHER_CAREGIVER = 'teacher_caregiver';
    public const ROLE_NURSE = 'nurse';
    public const ROLE_GENERAL_USER = 'general_user';

    public const FORM_PERMISSION_ACTIONS = [
        'view' => 'can_view',
        'create' => 'can_create',
        'update' => 'can_update',
        'delete' => 'can_delete',
        'print' => 'can_print',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'form_permissions_enabled' => 'boolean',
        ];
    }

    public static function roleOptions(): array
    {
        return [
            self::ROLE_ADMIN => 'ผู้ดูแลระบบ',
            self::ROLE_MANAGER => 'ผู้ใช้ / เจ้าหน้าที่',
            self::ROLE_EXECUTIVE => 'ผู้บริหาร',
            self::ROLE_SOCIAL_WORKER => 'นักสังคมสงเคราะห์',
            self::ROLE_TEACHER_CAREGIVER => 'ครู/ผู้ดูแล',
            self::ROLE_NURSE => 'พยาบาล',
            self::ROLE_GENERAL_USER => 'ผู้ใช้ทั่วไป',
        ];
    }

    public function getRoleLabelAttribute(): string
    {
        return self::roleOptions()[$this->role] ?? 'ไม่ระบุ';
    }

    public function getStatusLabelAttribute(): string
    {
        return (string) $this->status === '1' ? 'ใช้งาน' : 'ปิดใช้งาน';
    }

    public function getPhotoUrlAttribute(): string
    {
        $path = public_path('upload/user_images/' . $this->photo);

        if (!empty($this->photo) && file_exists($path)) {
            return asset('upload/user_images/' . $this->photo);
        }

        return asset('upload/no_image.jpg');
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isExecutive(): bool
    {
        return $this->role === self::ROLE_EXECUTIVE;
    }

    public function isSocialWorker(): bool
    {
        return $this->role === self::ROLE_SOCIAL_WORKER;
    }

    public function isTeacherCaregiver(): bool
    {
        return $this->role === self::ROLE_TEACHER_CAREGIVER;
    }

    public function isNurse(): bool
    {
        return $this->role === self::ROLE_NURSE;
    }

    public function isGeneralUser(): bool
    {
        return $this->role === self::ROLE_GENERAL_USER;
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles, true);
        }

        return $this->role === $roles;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /**
     * ผู้ใช้งานสามารถดูแลได้หลายบ้าน
     */
    public function houses(): BelongsToMany
    {
        return $this->belongsToMany(House::class, 'house_user', 'user_id', 'house_id')
            ->withTimestamps();
    }

    /**
     * เผื่อใช้กรณีต้องการเฉพาะบ้านที่เปิดใช้งาน
     */
    public function activeHouses(): BelongsToMany
    {
        return $this->belongsToMany(House::class, 'house_user', 'user_id', 'house_id')
            ->withTimestamps()
            ->where(function ($query) {
                $query->whereNull('houses.status')
                    ->orWhere('houses.status', 1)
                    ->orWhere('houses.status', '1');
            });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }

    public function healthcHeckups(): HasMany
    {
        return $this->hasMany(HealthcHeckup::class, 'recorded_by');
    }

    /**
     * สิทธิ์ระดับฟอร์มที่กำหนดให้ผู้ใช้รายนี้
     */
    public function formPermissions(): HasMany
    {
        return $this->hasMany(UserFormPermission::class);
    }

    /**
     * คืนค่า id บ้านทั้งหมดที่ user มีสิทธิ์
     * admin = เห็นทุกบ้าน
     */
    public function accessibleHouseIds(): array
    {
        if ($this->isAdmin()) {
            return House::query()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        }

        $this->loadMissing('houses');

        return $this->houses
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * ตรวจว่าผู้ใช้มีสิทธิ์เข้าถึงบ้านนี้หรือไม่
     */
    public function canAccessHouse(int|string|null $houseId): bool
    {
        if (empty($houseId)) {
            return false;
        }

        if ($this->isAdmin()) {
            return true;
        }

        return in_array((int) $houseId, $this->accessibleHouseIds(), true);
    }

    /**
     * ตรวจว่าผู้ใช้มีบ้านในความดูแลหรือไม่
     */
    public function hasAssignedHouses(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $this->loadMissing('houses');

        return $this->houses->isNotEmpty();
    }

    /**
     * ตรวจสิทธิ์รายฟอร์ม
     *
     * หลักการเพื่อไม่ให้กระทบระบบเดิม:
     * - admin ผ่านทุกสิทธิ์เสมอ
     * - หาก form_permissions_enabled = false ให้ใช้ระบบ role/route เดิมต่อไป
     * - เมื่อเปิดใช้งานแล้ว สิทธิ์ที่ไม่ได้กำหนดจะถือว่าไม่อนุญาต
     */
    public function hasFormPermission(string $permissionKey, string $action = 'view'): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (!$this->form_permissions_enabled) {
            return true;
        }

        $column = self::FORM_PERMISSION_ACTIONS[$action] ?? null;

        if ($column === null) {
            return false;
        }

        $this->loadMissing('formPermissions');

        $permission = $this->formPermissions->firstWhere('permission_key', $permissionKey);

        return (bool) ($permission?->{$column} ?? false);
    }

    public function hasAnyFormPermission(array $permissionKeys, string $action = 'view'): bool
    {
        foreach ($permissionKeys as $permissionKey) {
            if ($this->hasFormPermission($permissionKey, $action)) {
                return true;
            }
        }

        return false;
    }

    public function canViewForm(string $permissionKey): bool
    {
        return $this->hasFormPermission($permissionKey, 'view');
    }

    public function canCreateForm(string $permissionKey): bool
    {
        return $this->hasFormPermission($permissionKey, 'create');
    }

    public function canUpdateForm(string $permissionKey): bool
    {
        return $this->hasFormPermission($permissionKey, 'update');
    }

    public function canDeleteForm(string $permissionKey): bool
    {
        return $this->hasFormPermission($permissionKey, 'delete');
    }

    public function canPrintForm(string $permissionKey): bool
    {
        return $this->hasFormPermission($permissionKey, 'print');
    }
}
