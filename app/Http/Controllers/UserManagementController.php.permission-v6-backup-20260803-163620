<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\Project;
use App\Models\User;
use App\Models\UserFormPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserManagementController extends Controller
{
    public function index()
    {
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
        $roles = User::roleOptions();
        $projects = Project::orderBy('project_name')->get();
        $houses = House::orderBy('house_name')->get();
        $permissionGroups = config('user_permissions.groups', []);

        return view('backend.users.create', compact(
            'roles',
            'projects',
            'houses',
            'permissionGroups'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateUser($request);
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
        $roles = User::roleOptions();
        $projects = Project::orderBy('project_name')->get();
        $houses = House::orderBy('house_name')->get();
        $permissionGroups = config('user_permissions.groups', []);

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
        $validated = $this->validateUser($request, $user);
        $formPermissionsEnabled = $request->boolean('form_permissions_enabled');

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

        return redirect()
            ->route('users.index')
            ->with('success', 'อัปเดตข้อมูลผู้ใช้งานและสิทธิ์เรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ((int) auth()->id() === (int) $user->id) {
            return back()->with('error', 'ไม่สามารถลบบัญชีของตนเองได้');
        }

        if ($user->isAdmin() && User::where('role', User::ROLE_ADMIN)->count() <= 1) {
            return back()->with('error', 'ไม่สามารถลบผู้ดูแลระบบคนสุดท้ายได้');
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

        return back()->with('success', 'อัปเดตสถานะผู้ใช้งานเรียบร้อยแล้ว');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        $passwordRules = $user
            ? ['nullable', 'confirmed', 'min:8']
            : ['required', 'confirmed', 'min:8'];

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
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:2000'],
            'role' => ['required', Rule::in(array_keys(User::roleOptions()))],
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
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
            'password.confirmed' => 'ยืนยันรหัสผ่านไม่ตรงกัน',
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

    private function storePhoto(Request $request, ?string $currentPhoto = null): ?string
    {
        if (!$request->hasFile('photo')) {
            return $currentPhoto;
        }

        $directory = public_path('upload/user_images');
        File::ensureDirectoryExists($directory);

        $extension = strtolower($request->file('photo')->getClientOriginalExtension());
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

        $path = public_path('upload/user_images/' . $photo);

        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
