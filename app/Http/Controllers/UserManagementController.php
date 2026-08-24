<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\Project;
use App\Models\User;
use App\Models\UserFormPermission;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserManagementController extends Controller
{
    // USER_PERMISSION_GOVERNANCE_V1
    public function index()
    {
        $this->ensureActorCanManageUsers();

        $users = User::query()
            ->with(['houses', 'project', 'formPermissions'])
            ->latest()
            ->get();

        $stats = [
            'total' => User::count(),
            'active' => User::where('status', '1')->count(),
            'admin' => User::where('role', User::ROLE_ADMIN)->count(),
            'executive' => User::where('role', User::ROLE_EXECUTIVE)->count(),
            'social_worker' => User::where('role', User::ROLE_SOCIAL_WORKER)->count(),
            'teacher_caregiver' => User::where('role', User::ROLE_TEACHER_CAREGIVER)->count(),
            'nurse' => User::where('role', User::ROLE_NURSE)->count(),
        ];

        return view('backend.users.index', compact('users', 'stats'));
    }

    public function create()
    {
        $this->ensureActorCanManageUsers();

        $roles = $this->availableRolesForActor();
        $projects = Project::orderBy('project_name')->get();
        $houses = House::orderBy('house_name')->get();
        $permissionGroups = $this->permissionGroupsForActor();

        return view('backend.users.create', compact(
            'roles',
            'projects',
            'houses',
            'permissionGroups'
        ));
    }

    public function store(Request $request)
    {
        $this->ensureActorCanManageUsers();

        $validated = $this->validateUser($request);
        $this->guardDelegatedRole($validated['role']);
        $formPermissionsEnabled = $request->boolean('form_permissions_enabled');

        $user = DB::transaction(function () use ($request, $validated, $formPermissionsEnabled) {
            $photoName = $this->storePhoto($request);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'photo' => $photoName,
                'role' => $validated['role'],
                'status' => $validated['status'],
                'project_id' => $validated['project_id'] ?? null,
                'form_permissions_enabled' => $formPermissionsEnabled,
            ]);

            $user->houses()->sync($validated['house_ids'] ?? []);

            if ($formPermissionsEnabled) {
                $this->syncFormPermissions($user, $request->input('permissions', []));
            }

            return $user;
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'เพิ่มผู้ใช้งานและกำหนดสิทธิ์เรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        $user = User::with(['houses', 'formPermissions'])->findOrFail($id);
        $this->ensureActorCanManageTarget($user);
        $roles = $this->availableRolesForActor();
        $projects = Project::orderBy('project_name')->get();
        $houses = House::orderBy('house_name')->get();
        $permissionGroups = $this->permissionGroupsForActor();

        return view('backend.users.edit', compact(
            'user',
            'roles',
            'projects',
            'houses',
            'permissionGroups'
        ));
    }

    public function update(Request $request, $id)
    {
        $user = User::with('formPermissions')->findOrFail($id);
        $this->ensureActorCanManageTarget($user);
        $validated = $this->validateUser($request, $user);
        $this->guardDelegatedRole($validated['role']);
        $formPermissionsEnabled = $request->boolean('form_permissions_enabled');

        /*
         * Snapshot เฉพาะโครงสร้างสิทธิ์ก่อนแก้ไข
         * ใช้เปรียบเทียบในหน่วยความจำเท่านั้น
         * ไม่บันทึกค่ารายละเอียดลง Audit Log
         */
        $accessBefore = $this->accessSnapshot($user);
        $passwordWasReset = !empty($validated['password']);

        $this->protectLastAdmin(
            $user,
            $validated['role'],
            $validated['status']
        );

        DB::transaction(function () use ($request, $validated, $user, $formPermissionsEnabled) {
            $photoName = $this->storePhoto($request, $user->photo);

            $data = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'photo' => $photoName,
                'role' => $validated['role'],
                'status' => $validated['status'],
                'project_id' => $validated['project_id'] ?? null,
                'form_permissions_enabled' => $formPermissionsEnabled,
            ];

            if (!empty($validated['password'])) {
                $data['password'] = Hash::make($validated['password']);
            }

            $user->update($data);
            $user->houses()->sync($validated['house_ids'] ?? []);

            /*
             * เมื่อปิดสิทธิ์รายฟอร์ม จะเก็บรายการเดิมไว้ก่อน
             * เพื่อให้เปิดกลับมาใช้ได้โดยไม่ต้องกำหนดใหม่ทั้งหมด
             */
            if ($formPermissionsEnabled) {
                $this->syncFormPermissions($user, $request->input('permissions', []));
            }
        });

        /*
         * โหลดค่าจริงหลัง Transaction เพื่อให้ Audit สะท้อนข้อมูลที่บันทึกสำเร็จแล้ว
         */
        $user->refresh();
        $user->load('formPermissions');

        $accessAfter = $this->accessSnapshot($user);
        $accessChangedFields = $this->detectAccessChangedFields(
            $accessBefore,
            $accessAfter
        );

        /*
         * บันทึกเฉพาะ "ชนิดของสิ่งที่เปลี่ยน"
         * ไม่เก็บ role/status/project/house/permission value จริงลง Audit Log
         */
        if ($accessChangedFields !== []) {
            AuditLogger::log(
                action: 'PERMISSION_CHANGE',
                module: 'system_users',
                subject: $user,
                changedFields: $accessChangedFields,
                result: 'success',
                statusCode: 302,
                metadata: [
                    'security_event' => 'user_access_changed',
                ],
                userId: auth()->id() !== null
                    ? (int) auth()->id()
                    : null
            );
        }

        /*
         * Admin รีเซ็ตรหัสผ่านของผู้ใช้อื่น:
         * เก็บเพียงว่า credential ถูกรีเซ็ต ไม่เก็บค่ารหัสผ่าน
         */
        if ($passwordWasReset) {
            AuditLogger::log(
                action: 'UPDATE',
                module: 'account_security',
                subject: $user,
                changedFields: [
                    'credential',
                ],
                result: 'success',
                statusCode: 302,
                metadata: [
                    'security_event' => 'admin_password_reset',
                ],
                userId: auth()->id() !== null
                    ? (int) auth()->id()
                    : null
            );
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'อัปเดตข้อมูลผู้ใช้งานและสิทธิ์เรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->ensureActorCanManageTarget($user);

        if ((int) auth()->id() === (int) $user->id) {
            return back()->with('error', 'ไม่สามารถลบบัญชีของตนเองได้');
        }

        if (
            $user->isAdmin()
            && (string) $user->status === '1'
            && User::where('role', User::ROLE_ADMIN)->where('status', '1')->count() <= 1
        ) {
            return back()->with('error', 'ไม่สามารถลบผู้ดูแลระบบคนสุดท้ายได้');
        }

        /*
         * ตาราง operations ผูก user_id แบบ cascadeOnDelete ใน schema เดิม
         * การลบบัญชีที่เคยมีบันทึกปฏิบัติงานจึงทำให้ประวัติการทำงานหายตามไปด้วย
         * Production จะป้องกันการลบกรณีนี้ และให้ปิดสถานะบัญชีแทน
         */
        if ($user->operations()->exists()) {
            return back()->with(
                'error',
                'ไม่สามารถลบบัญชีนี้ได้ เนื่องจากมีประวัติการปฏิบัติงาน กรุณาปิดสถานะบัญชีแทนเพื่อรักษาประวัติข้อมูล'
            );
        }

        DB::transaction(function () use ($user) {
            $user->houses()->detach();
            $photo = $user->photo;
            $user->delete();
            $this->deletePhoto($photo);
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'ลบผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $this->ensureActorCanManageTarget($user);

        if ((int) auth()->id() === (int) $user->id) {
            return back()->with('error', 'ไม่สามารถเปลี่ยนสถานะบัญชีของตนเองได้');
        }

        if (
            $user->isAdmin()
            && (string) $user->status === '1'
            && User::where('role', User::ROLE_ADMIN)->where('status', '1')->count() <= 1
        ) {
            return back()->with('error', 'ไม่สามารถปิดใช้งานผู้ดูแลระบบที่ใช้งานอยู่คนสุดท้ายได้');
        }

        $user->status = (string) $user->status === '1' ? '0' : '1';
        $user->save();

        /*
         * การเปิด/ปิดบัญชีเป็นเหตุการณ์ด้านสิทธิ์ที่ต้องตรวจสอบย้อนหลังได้
         * เก็บเฉพาะชื่อ field ไม่เก็บค่าเดิม/ค่าใหม่
         */
        AuditLogger::log(
            action: 'PERMISSION_CHANGE',
            module: 'system_users',
            subject: $user,
            changedFields: [
                'account_status',
            ],
            result: 'success',
            statusCode: 302,
            metadata: [
                'security_event' => 'user_status_changed',
            ],
            userId: auth()->id() !== null
                ? (int) auth()->id()
                : null
        );

        return back()->with('success', 'อัปเดตสถานะผู้ใช้งานเรียบร้อยแล้ว');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        $passwordRule = Password::min(10)->letters()->numbers();

        $passwordRules = $user
            ? ['nullable', 'string', 'confirmed', $passwordRule]
            : ['required', 'string', 'confirmed', $passwordRule];

        $emailRule = Rule::unique('users', 'email');

        if ($user) {
            $emailRule->ignore($user->id);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                $emailRule,
            ],
            'password' => $passwordRules,
            'password_confirmation' => $user
                ? ['nullable', 'required_with:password', 'string']
                : ['required', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:2000'],
            'role' => ['required', Rule::in(array_keys($this->availableRolesForActor()))],
            'status' => ['required', Rule::in(['0', '1'])],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'house_ids' => ['nullable', 'array'],
            'house_ids.*' => ['integer', 'distinct', 'exists:houses,id'],
            'form_permissions_enabled' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['nullable', 'array'],
            'permissions.*.view' => ['nullable', 'boolean'],
            'permissions.*.create' => ['nullable', 'boolean'],
            'permissions.*.update' => ['nullable', 'boolean'],
            'permissions.*.delete' => ['nullable', 'boolean'],
            'permissions.*.print' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required' => 'กรุณากรอกชื่อผู้ใช้งาน',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 10 ตัวอักษร',
            'password.letters' => 'รหัสผ่านต้องมีตัวอักษรอย่างน้อย 1 ตัว',
            'password.numbers' => 'รหัสผ่านต้องมีตัวเลขอย่างน้อย 1 ตัว',
            'password.confirmed' => 'ยืนยันรหัสผ่านไม่ตรงกัน',
            'password_confirmation.required' => 'กรุณายืนยันรหัสผ่าน',
            'password_confirmation.required_with' => 'กรุณายืนยันรหัสผ่านใหม่',
            'role.required' => 'กรุณาเลือกบทบาทผู้ใช้งาน',
            'role.in' => 'บทบาทผู้ใช้งานไม่ถูกต้อง',
            'status.required' => 'กรุณาเลือกสถานะ',
            'project_id.exists' => 'หน่วยงาน/โครงการที่เลือกไม่ถูกต้อง',
            'house_ids.array' => 'ข้อมูลบ้านที่เลือกไม่ถูกต้อง',
            'house_ids.*.exists' => 'บ้านที่เลือกไม่ถูกต้อง',
            'house_ids.*.distinct' => 'มีการเลือกบ้านซ้ำกัน',
            'photo.image' => 'ไฟล์รูปประจำตัวต้องเป็นรูปภาพ',
            'photo.mimes' => 'รองรับไฟล์รูปประเภท JPG, JPEG, PNG และ WEBP',
            'photo.max' => 'รูปประจำตัวต้องมีขนาดไม่เกิน 2 MB',
        ]);
    }

    private function syncFormPermissions(User $user, array $submittedPermissions): void
    {
        $groups = config('user_permissions.groups', []);
        $rows = [];
        $now = now();

        foreach ($groups as $group) {
            foreach (($group['items'] ?? []) as $permissionKey => $item) {
                $allowedActions = $item['actions'] ?? [];
                $submitted = $submittedPermissions[$permissionKey] ?? [];

                $values = [
                    'view' => in_array('view', $allowedActions, true) && !empty($submitted['view']),
                    'create' => in_array('create', $allowedActions, true) && !empty($submitted['create']),
                    'update' => in_array('update', $allowedActions, true) && !empty($submitted['update']),
                    'delete' => in_array('delete', $allowedActions, true) && !empty($submitted['delete']),
                    'print' => in_array('print', $allowedActions, true) && !empty($submitted['print']),
                ];

                // Admin/ผู้บริหารที่ผ่านด่าน User Management สามารถกำหนดสิทธิ์
                // จาก matrix กลางได้ครบทุก action โดยไม่ถูกจำกัดด้วยสิทธิ์รายฟอร์มของผู้กำหนดเอง
                // การเข้าถึงหน้าจัดการผู้ใช้ถูกควบคุมด้วย role + controller guard แยกต่างหาก

                // สิทธิ์ทำรายการทุกชนิดต้องดูฟอร์มได้ด้วย
                if ($values['create'] || $values['update'] || $values['delete'] || $values['print']) {
                    $values['view'] = true;
                }

                if (!in_array(true, $values, true)) {
                    continue;
                }

                $rows[] = [
                    'user_id' => $user->id,
                    'permission_key' => $permissionKey,
                    'can_view' => $values['view'],
                    'can_create' => $values['create'],
                    'can_update' => $values['update'],
                    'can_delete' => $values['delete'],
                    'can_print' => $values['print'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        $user->formPermissions()->delete();

        if ($rows !== []) {
            UserFormPermission::insert($rows);
        }

        $user->unsetRelation('formPermissions');
    }

    private function availableRolesForActor(): array
    {
        $roles = User::roleOptions();
        $actor = auth()->user();

        // บทบาท Admin เป็น protected role: ไม่สร้าง/มอบผ่านหน้า User Management
        unset($roles[User::ROLE_ADMIN]);

        // ผู้บริหารจัดการบัญชีระดับเจ้าหน้าที่ลงมาเท่านั้น
        // การแต่งตั้ง/แก้ไขผู้บริหารให้ Admin เป็นผู้ดำเนินการ
        if ($actor && $actor->isExecutive()) {
            unset($roles[User::ROLE_EXECUTIVE]);
        }

        return $roles;
    }

    private function permissionGroupsForActor(): array
    {
        $actor = auth()->user();

        if (!$actor || (!$actor->isAdmin() && !$actor->isExecutive())) {
            return [];
        }

        // Admin และผู้บริหารเห็น matrix กลางครบทุกหมวด/ทุก action ที่ระบบรองรับ
        return config('user_permissions.groups', []);
    }

    private function guardDelegatedRole(string $role): void
    {
        $actor = auth()->user();

        if ($role === User::ROLE_ADMIN) {
            throw ValidationException::withMessages([
                'role' => 'บทบาทผู้ดูแลระบบ (Admin) เป็นบทบาทคุ้มครอง ไม่สามารถสร้างหรือมอบผ่านหน้าจัดการผู้ใช้งานได้',
            ]);
        }

        if ($actor && $actor->isExecutive() && $role === User::ROLE_EXECUTIVE) {
            throw ValidationException::withMessages([
                'role' => 'การแต่งตั้งผู้บริหารต้องดำเนินการโดยผู้ดูแลระบบ (Admin)',
            ]);
        }
    }

    private function ensureActorCanManageUsers(): void
    {
        $actor = auth()->user();

        if (!$actor || (!$actor->isAdmin() && !$actor->isExecutive())) {
            abort(403, 'เฉพาะผู้ดูแลระบบและผู้บริหารเท่านั้นที่สามารถจัดการผู้ใช้งานและกำหนดสิทธิ์ได้');
        }
    }

    private function ensureActorCanManageTarget(User $target): void
    {
        $this->ensureActorCanManageUsers();
        $actor = auth()->user();

        // Admin ทุกบัญชีเป็น protected account ในโมดูลนี้ แม้ผู้กระทำจะเป็น Admin ด้วยกัน
        if ($target->isAdmin()) {
            abort(403, 'บัญชีผู้ดูแลระบบ (Admin) ได้รับการป้องกัน ไม่สามารถแก้ไขสิทธิ์ สถานะ หรือลบผ่านหน้าจัดการผู้ใช้งานได้');
        }

        // ผู้บริหารไม่สามารถแต่งตั้ง/แก้ไขผู้บริหารคนอื่น เพื่อป้องกันการขยายอำนาจต่อกันเอง
        if ($actor && $actor->isExecutive() && $target->isExecutive()) {
            abort(403, 'บัญชีผู้บริหารจัดการได้โดยผู้ดูแลระบบ (Admin) เท่านั้น');
        }

        if ($actor && (int) $actor->id === (int) $target->id) {
            abort(403, 'กรุณาแก้ไขบัญชีของตนเองผ่านหน้าโปรไฟล์');
        }
    }

    private function protectLastAdmin(User $user, string $newRole, string $newStatus): void
    {
        if (!$user->isAdmin() || (string) $user->status !== '1') {
            return;
        }

        $isRemovingAdminAccess = $newRole !== User::ROLE_ADMIN || $newStatus !== '1';

        if (
            $isRemovingAdminAccess
            && User::where('role', User::ROLE_ADMIN)->where('status', '1')->count() <= 1
        ) {
            throw ValidationException::withMessages([
                'role' => 'ไม่สามารถลดสิทธิ์หรือปิดใช้งานผู้ดูแลระบบที่ใช้งานอยู่คนสุดท้ายได้',
            ]);
        }
    }

    /**
     * Snapshot โครงสร้างสิทธิ์ของผู้ใช้งาน
     *
     * ข้อมูลชุดนี้ใช้เปรียบเทียบเฉพาะในหน่วยความจำ
     * และจะไม่ถูกส่งเป็น value เข้า Audit Log
     */
    private function accessSnapshot(User $user): array
    {
        $user->loadMissing('formPermissions');

        $houseIds = $user->houses()
            ->pluck('houses.id')
            ->map(static fn ($id): int => (int) $id)
            ->sort()
            ->values()
            ->all();

        $permissions = $user->formPermissions
            ->map(static function (UserFormPermission $permission): array {
                return [
                    'permission_key' => (string) $permission->permission_key,
                    'can_view' => (bool) $permission->can_view,
                    'can_create' => (bool) $permission->can_create,
                    'can_update' => (bool) $permission->can_update,
                    'can_delete' => (bool) $permission->can_delete,
                    'can_print' => (bool) $permission->can_print,
                ];
            })
            ->sortBy('permission_key')
            ->values()
            ->all();

        return [
            'role' => (string) $user->role,
            'status' => (string) $user->status,
            'project_id' => $user->project_id !== null
                ? (int) $user->project_id
                : null,
            'house_ids' => $houseIds,
            'form_permissions_enabled' => (bool) $user->form_permissions_enabled,
            'permissions' => $permissions,
        ];
    }

    /**
     * คืนเฉพาะชื่อประเภทของสิทธิ์ที่เปลี่ยน
     *
     * ห้ามคืนค่าเดิม/ค่าใหม่ เพื่อไม่ให้ Audit Log เก็บรายละเอียดเกินจำเป็น
     *
     * @return list<string>
     */
    private function detectAccessChangedFields(
        array $before,
        array $after
    ): array {
        $changed = [];

        if (($before['role'] ?? null) !== ($after['role'] ?? null)) {
            $changed[] = 'role';
        }

        if (($before['status'] ?? null) !== ($after['status'] ?? null)) {
            $changed[] = 'account_status';
        }

        if (($before['project_id'] ?? null) !== ($after['project_id'] ?? null)) {
            $changed[] = 'project_assignment';
        }

        if (($before['house_ids'] ?? []) !== ($after['house_ids'] ?? [])) {
            $changed[] = 'house_assignments';
        }

        if (
            ($before['form_permissions_enabled'] ?? false)
            !==
            ($after['form_permissions_enabled'] ?? false)
        ) {
            $changed[] = 'form_permission_mode';
        }

        if (($before['permissions'] ?? []) !== ($after['permissions'] ?? [])) {
            $changed[] = 'form_permissions';
        }

        return $changed;
    }

    private function storePhoto(Request $request, ?string $currentPhoto = null): ?string
    {
        if (!$request->hasFile('photo')) {
            return $currentPhoto;
        }

        $directory = public_path('upload/user_images');
        File::ensureDirectoryExists($directory);

        $extension = strtolower((string) $request->file('photo')->extension());
        $extension = in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)
            ? $extension
            : 'jpg';
        $photoName = 'user_' . now()->format('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;

        $request->file('photo')->move($directory, $photoName);
        $this->deletePhoto($currentPhoto);

        return $photoName;
    }

    private function deletePhoto(?string $photo): void
    {
        if (blank($photo)) {
            return;
        }

        $path = public_path('upload/user_images/' . basename($photo));

        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
