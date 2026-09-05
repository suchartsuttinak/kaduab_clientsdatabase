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

        $actor = auth()->user();

        $users = User::query()
            ->with(['houses', 'projects', 'project', 'formPermissions'])
            ->latest()
            ->get();

        // USER_DELEGATION_SCOPE_GUARD_V5:
        // ผู้บริหารที่ถูกจำกัดหน่วยงาน/บ้าน จะเห็นและจัดการเฉพาะบัญชีระดับเจ้าหน้าที่
        // ที่มีขอบเขตอยู่ภายในขอบเขตของตนเองเท่านั้น ป้องกันการมอบสิทธิ์ข้ามหน่วยงาน
        if ($actor && $actor->isExecutive()) {
            $users = $users
                ->filter(fn (User $candidate): bool => $this->actorCanManageTargetByScope($actor, $candidate))
                ->values();
        }

        $stats = [
            'total' => $users->count(),
            'active' => $users->filter(fn (User $u): bool => (string) $u->status === '1')->count(),
            'admin' => $users->filter(fn (User $u): bool => $u->isAdmin())->count(),
            'executive' => $users->filter(fn (User $u): bool => $u->isExecutive())->count(),
            'social_worker' => $users->filter(fn (User $u): bool => $u->isSocialWorker())->count(),
            'teacher_caregiver' => $users->filter(fn (User $u): bool => $u->isTeacherCaregiver())->count(),
            'nurse' => $users->filter(fn (User $u): bool => $u->isNurse())->count(),
        ];

        return view('backend.users.index', compact('users', 'stats'));
    }

    public function create()
    {
        $this->ensureActorCanManageUsers();

        $roles = $this->availableRolesForActor();
        $projects = $this->projectsForActor();
        $houses = $this->housesForActor();
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
        // UNIFIED_ACCESS_SCOPE_V5: ผู้ใช้ทุกบทบาทยกเว้น Admin ใช้ permission matrix เป็นระบบหลัก
        $formPermissionsEnabled = true;
        $projectIds = $this->normalizedProjectIds($validated);
        $houseIds = $this->normalizedHouseIds($validated);
        $this->guardDelegatedScope($projectIds, $houseIds);

        $user = DB::transaction(function () use ($request, $validated, $formPermissionsEnabled, $projectIds, $houseIds) {
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
                'project_id' => $projectIds[0] ?? null,
                'form_permissions_enabled' => $formPermissionsEnabled,
            ]);

            $user->houses()->sync($houseIds);
            $user->projects()->sync($projectIds);

            $this->syncFormPermissions($user, $request->input('permissions', []));

            return $user;
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'เพิ่มผู้ใช้งานและกำหนดสิทธิ์เรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        $user = User::with(['houses', 'projects', 'formPermissions'])->findOrFail($id);
        $this->ensureActorCanManageTarget($user);
        $roles = $this->availableRolesForActor();
        $projects = $this->projectsForActor();
        $houses = $this->housesForActor();
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
        // UNIFIED_ACCESS_SCOPE_V5: ผู้ใช้ทุกบทบาทยกเว้น Admin ใช้ permission matrix เป็นระบบหลัก
        $formPermissionsEnabled = true;
        $projectIds = $this->normalizedProjectIds($validated);
        $houseIds = $this->normalizedHouseIds($validated);
        $this->guardDelegatedScope($projectIds, $houseIds);

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

        DB::transaction(function () use ($request, $validated, $user, $formPermissionsEnabled, $projectIds, $houseIds) {
            $photoName = $this->storePhoto($request, $user->photo);

            $data = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'photo' => $photoName,
                'role' => $validated['role'],
                'status' => $validated['status'],
                'project_id' => $projectIds[0] ?? null,
                'form_permissions_enabled' => $formPermissionsEnabled,
            ];

            if (!empty($validated['password'])) {
                $data['password'] = Hash::make($validated['password']);
            }

            $user->update($data);
            $user->houses()->sync($houseIds);
            $user->projects()->sync($projectIds);

            /* สิทธิ์ที่บันทึกจาก matrix คือสิทธิ์ใช้งานจริงของบัญชี */
            $this->syncFormPermissions($user, $request->input('permissions', []));
        });

        /*
         * โหลดค่าจริงหลัง Transaction เพื่อให้ Audit สะท้อนข้อมูลที่บันทึกสำเร็จแล้ว
         */
        $user->refresh();
        $user->load(['formPermissions', 'projects', 'houses']);

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
            $user->projects()->detach();
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
            // USER_MULTI_PROJECT_SCOPE_V5: ไม่เลือก = ทุกหน่วยงาน, เลือกได้หลายหน่วยงาน
            'project_ids' => ['nullable', 'array'],
            'project_ids.*' => ['integer', 'distinct', 'exists:projects,id'],
            // รับ field เดิมไว้เพื่อ compatibility ระหว่าง deploy/cache เก่า
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
            'project_ids.array' => 'ข้อมูลหน่วยงาน/โครงการที่เลือกไม่ถูกต้อง',
            'project_ids.*.exists' => 'หน่วยงาน/โครงการที่เลือกไม่ถูกต้อง',
            'project_ids.*.distinct' => 'มีการเลือกหน่วยงาน/โครงการซ้ำกัน',
            'project_id.exists' => 'หน่วยงาน/โครงการที่เลือกไม่ถูกต้อง',
            'house_ids.array' => 'ข้อมูลบ้านที่เลือกไม่ถูกต้อง',
            'house_ids.*.exists' => 'บ้านที่เลือกไม่ถูกต้อง',
            'house_ids.*.distinct' => 'มีการเลือกบ้านซ้ำกัน',
            'photo.image' => 'ไฟล์รูปประจำตัวต้องเป็นรูปภาพ',
            'photo.mimes' => 'รองรับไฟล์รูปประเภท JPG, JPEG, PNG และ WEBP',
            'photo.max' => 'รูปประจำตัวต้องมีขนาดไม่เกิน 2 MB',
        ]);
    }

    /**
     * USER_MULTI_PROJECT_SCOPE_V5
     * คืนรายการ Project ที่เลือกจริง
     * [] = ทุกหน่วยงาน (ไม่จำกัด Project)
     */
    private function normalizedProjectIds(array $validated): array
    {
        $ids = array_values(array_unique(array_filter(
            array_map(
                static fn ($id): int => (int) $id,
                (array) ($validated['project_ids'] ?? [])
            ),
            static fn (int $id): bool => $id > 0
        )));

        // รองรับ request จากหน้าเก่าที่ยังส่ง project_id เดี่ยวระหว่าง deploy
        if ($ids === [] && !empty($validated['project_id'])) {
            $ids = [(int) $validated['project_id']];
        }

        sort($ids);

        return $ids;
    }

    /**
     * คืนรายการ House ที่เลือกจริง
     * [] = ทุกบ้าน (ไม่จำกัด House)
     */
    private function normalizedHouseIds(array $validated): array
    {
        $ids = array_values(array_unique(array_filter(
            array_map(
                static fn ($id): int => (int) $id,
                (array) ($validated['house_ids'] ?? [])
            ),
            static fn (int $id): bool => $id > 0
        )));

        sort($ids);

        return $ids;
    }

    /**
     * USER_DELEGATION_SCOPE_GUARD_V5
     * ผู้บริหารมอบขอบเขตให้เจ้าหน้าที่ได้ไม่เกินขอบเขตของตนเอง
     * - ผู้บริหารไม่ถูกจำกัด Project/House -> มอบได้ทุกค่าในส่วนนั้น
     * - ผู้บริหารถูกจำกัด -> ห้ามเลือก "ทุกหน่วยงาน/ทุกบ้าน" ให้ผู้ใต้บังคับบัญชา
     *   และห้ามเลือกค่าที่อยู่นอกขอบเขตของผู้บริหาร
     * - Admin ไม่ถูกจำกัด
     */
    private function guardDelegatedScope(array $projectIds, array $houseIds): void
    {
        $actor = auth()->user();

        if (!$actor || $actor->isAdmin()) {
            return;
        }

        if (!$actor->isExecutive()) {
            abort(403, 'บัญชีนี้ไม่มีสิทธิ์กำหนดขอบเขตผู้ใช้งาน');
        }

        $actorProjectIds = $actor->assignedProjectIds();
        if ($actorProjectIds !== []) {
            if ($projectIds === []) {
                throw ValidationException::withMessages([
                    'project_ids' => 'ผู้บริหารบัญชีนี้ถูกจำกัดหน่วยงาน จึงไม่สามารถกำหนด “ทุกหน่วยงาน” ให้ผู้ใช้งานได้',
                ]);
            }

            if (array_diff($projectIds, $actorProjectIds) !== []) {
                throw ValidationException::withMessages([
                    'project_ids' => 'มีหน่วยงาน/โครงการที่อยู่นอกขอบเขตของผู้บริหาร กรุณาเลือกเฉพาะหน่วยงานที่ได้รับมอบหมาย',
                ]);
            }
        }

        $actorHouseIds = $actor->assignedHouseIds();
        if ($actorHouseIds !== []) {
            if ($houseIds === []) {
                throw ValidationException::withMessages([
                    'house_ids' => 'ผู้บริหารบัญชีนี้ถูกจำกัดบ้าน จึงไม่สามารถกำหนด “ทุกบ้าน” ให้ผู้ใช้งานได้',
                ]);
            }

            if (array_diff($houseIds, $actorHouseIds) !== []) {
                throw ValidationException::withMessages([
                    'house_ids' => 'มีบ้านที่อยู่นอกขอบเขตของผู้บริหาร กรุณาเลือกเฉพาะบ้านที่ได้รับมอบหมาย',
                ]);
            }
        }
    }

    private function projectsForActor()
    {
        $actor = auth()->user();
        $query = Project::query()->orderBy('project_name');

        if ($actor && $actor->isExecutive() && $actor->hasProjectRestriction()) {
            $query->whereIn('id', $actor->assignedProjectIds());
        }

        return $query->get();
    }

    private function housesForActor()
    {
        $actor = auth()->user();
        $query = House::query()->orderBy('house_name');

        if ($actor && $actor->isExecutive() && $actor->hasHouseRestriction()) {
            $query->whereIn('id', $actor->assignedHouseIds());
        }

        return $query->get();
    }

    /**
     * ตรวจว่าบัญชีเป้าหมายอยู่ภายในขอบเขตที่ผู้บริหารสามารถกำกับได้หรือไม่
     * "ไม่เลือก" ของบัญชีเป้าหมายหมายถึงทุกหน่วยงาน/ทุกบ้าน จึงถือว่ากว้างกว่า
     * ผู้บริหารที่ถูกจำกัดในส่วนนั้นและไม่อนุญาตให้จัดการ
     */
    private function actorCanManageTargetByScope(User $actor, User $target): bool
    {
        if ($actor->isAdmin()) {
            return true;
        }

        if (!$actor->isExecutive()) {
            return false;
        }

        if ($target->isAdmin() || $target->isExecutive() || (int) $actor->id === (int) $target->id) {
            return false;
        }

        $actorProjectIds = $actor->assignedProjectIds();
        if ($actorProjectIds !== []) {
            $targetProjectIds = $target->assignedProjectIds();
            if ($targetProjectIds === [] || array_diff($targetProjectIds, $actorProjectIds) !== []) {
                return false;
            }
        }

        $actorHouseIds = $actor->assignedHouseIds();
        if ($actorHouseIds !== []) {
            $targetHouseIds = $target->assignedHouseIds();
            if ($targetHouseIds === [] || array_diff($targetHouseIds, $actorHouseIds) !== []) {
                return false;
            }
        }

        return true;
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

        if ($actor && $actor->isExecutive() && !$this->actorCanManageTargetByScope($actor, $target)) {
            abort(403, 'บัญชีผู้ใช้งานนี้อยู่นอกขอบเขตหน่วยงาน/บ้านที่ผู้บริหารได้รับมอบหมาย');
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
        $user->loadMissing(['formPermissions', 'projects']);

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
            // project_id เดิมเก็บไว้เป็นค่า compatibility เท่านั้น
            'project_id' => $user->project_id !== null
                ? (int) $user->project_id
                : null,
            'project_ids' => $user->assignedProjectIds(),
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

        if (($before['project_ids'] ?? []) !== ($after['project_ids'] ?? [])) {
            $changed[] = 'project_assignments';
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
