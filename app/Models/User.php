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

    /**
     * โครงการเดิมแบบหนึ่งต่อหนึ่ง เก็บไว้เพื่อรองรับข้อมูล/โค้ดเก่า
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * USER_MULTI_PROJECT_SCOPE_V5
     * ผู้ใช้งานหนึ่งบัญชีสามารถรับผิดชอบได้หลายหน่วยงาน/โครงการ
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id')
            ->withTimestamps();
    }

    /**
     * คืน project id ที่ "กำหนดไว้จริง" ให้บัญชี
     * [] หมายถึงไม่ได้จำกัดหน่วยงาน = ทุกหน่วยงาน
     *
     * รองรับ project_id เดิมในช่วงเปลี่ยนผ่านเพื่อไม่ให้สิทธิ์เก่าหาย
     */
    public function assignedProjectIds(): array
    {
        if ($this->isAdmin()) {
            return [];
        }

        $ids = [];

        try {
            $this->loadMissing('projects');
            $ids = $this->projects
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->unique()
                ->values()
                ->toArray();
        } catch (\Throwable) {
            // ระหว่าง deploy ก่อน migrate ให้ fallback project_id เดิมได้
            $ids = [];
        }

        if ($ids === [] && !empty($this->project_id)) {
            $ids = [(int) $this->project_id];
        }

        return $ids;
    }

    public function hasProjectRestriction(): bool
    {
        return !$this->isAdmin() && $this->assignedProjectIds() !== [];
    }

    /**
     * project ที่บัญชีเข้าถึงได้จริง
     * - Admin: ทุกโครงการ
     * - ไม่ได้เลือก project: ทุกโครงการ
     * - เลือก 1+ project: เฉพาะที่เลือก
     */
    public function accessibleProjectIds(): array
    {
        if ($this->isAdmin() || !$this->hasProjectRestriction()) {
            return Project::query()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        }

        return $this->assignedProjectIds();
    }

    public function canAccessProject(int|string|null $projectId): bool
    {
        if ($this->isAdmin() || !$this->hasProjectRestriction()) {
            return true;
        }

        if (empty($projectId)) {
            return false;
        }

        return in_array((int) $projectId, $this->assignedProjectIds(), true);
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
     * USER_MULTI_PROJECT_SCOPE_V5
     * คืน house id ที่กำหนดไว้จริง
     * [] หมายถึงไม่ได้จำกัดบ้าน = ทุกบ้าน
     */
    public function assignedHouseIds(): array
    {
        if ($this->isAdmin()) {
            return [];
        }

        $this->loadMissing('houses');

        $ids = $this->houses
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->toArray();

        // รองรับ house_id เดิม เมื่อระบบเก่ายังไม่ได้ย้ายเข้าตาราง house_user
        if ($ids === [] && !empty($this->house_id)) {
            $ids = [(int) $this->house_id];
        }

        return $ids;
    }

    public function hasHouseRestriction(): bool
    {
        return !$this->isAdmin() && $this->assignedHouseIds() !== [];
    }

    /**
     * คืนบ้านทั้งหมดที่ผู้ใช้เข้าถึงได้จริง
     * - Admin: ทุกบ้าน
     * - ไม่ได้เลือกบ้าน: ทุกบ้าน
     * - เลือกบ้าน 1+ หลัง: เฉพาะบ้านที่เลือก
     */
    public function accessibleHouseIds(): array
    {
        if ($this->isAdmin() || !$this->hasHouseRestriction()) {
            return House::query()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        }

        return $this->assignedHouseIds();
    }

    public function canAccessHouse(int|string|null $houseId): bool
    {
        if ($this->isAdmin() || !$this->hasHouseRestriction()) {
            return true;
        }

        if (empty($houseId)) {
            return false;
        }

        return in_array((int) $houseId, $this->assignedHouseIds(), true);
    }

    /**
     * คงชื่อ method เดิมไว้สำหรับ compatibility
     * หมายถึงมีการ "เลือกจำกัดบ้าน" จริง ไม่ใช่การเข้าถึงบ้านได้หรือไม่
     */
    public function hasAssignedHouses(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->assignedHouseIds() !== [];
    }

    /**
     * UNIFIED_ACCESS_SCOPE_V5
     * ตรวจสิทธิ์รายฟอร์มแบบ Default Deny สำหรับผู้ใช้ทุกบทบาท ยกเว้น Admin
     *
     * - Admin: ผ่านทุกสิทธิ์เสมอ
     * - ผู้บริหาร / นักสังคม / ครู / พยาบาล / เจ้าหน้าที่ / ผู้ใช้ทั่วไป:
     *   ต้องได้รับสิทธิ์ใน user_form_permissions โดยตรง
     * - form_permissions_enabled คงไว้เพื่อ compatibility กับฐานข้อมูลเดิม
     *   แต่ไม่ใช้เป็นช่องทาง bypass สิทธิ์อีกต่อไป
     */
    public function hasFormPermission(string $permissionKey, string $action = 'view'): bool
    {
        if ($this->isAdmin()) {
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
