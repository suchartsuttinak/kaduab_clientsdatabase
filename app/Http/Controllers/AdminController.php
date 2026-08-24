<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function AdminLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function AdminProfile()
    {
        $profileData = User::findOrFail(Auth::id());

        return view('admin.admin_profile', compact('profileData'));
    }

    public function ProfileStore(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required' => 'กรุณากรอกชื่อผู้ใช้งาน',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'photo.image' => 'ไฟล์รูปประจำตัวต้องเป็นรูปภาพ',
            'photo.mimes' => 'รองรับไฟล์รูปประเภท JPG, JPEG, PNG และ WEBP',
            'photo.max' => 'รูปประจำตัวต้องมีขนาดไม่เกิน 2 MB',
        ]);

        $oldPhoto = $user->photo;

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->address = $validated['address'] ?? null;

        if ($request->hasFile('photo')) {
            $directory = public_path('upload/user_images');
            File::ensureDirectoryExists($directory);

            // extension() ใช้ชนิดไฟล์ที่ Laravel ตรวจแล้ว ไม่เชื่อ extension จากชื่อไฟล์ผู้ใช้โดยตรง
            $extension = strtolower((string) $request->file('photo')->extension());
            $extension = in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)
                ? $extension
                : 'jpg';

            $filename = 'user_' . Str::uuid()->toString() . '.' . $extension;
            $request->file('photo')->move($directory, $filename);
            $user->photo = $filename;
        }

        $user->save();

        if ($request->hasFile('photo') && $oldPhoto && $oldPhoto !== $user->photo) {
            $this->deleteOldImage($oldPhoto);
        }

        return redirect()->back()->with([
            'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
            'alert-type' => 'success',
        ]);
    }

    private function deleteOldImage(string $oldPhotoPath): void
    {
        $filename = basename($oldPhotoPath);

        if ($filename === '' || $filename === '.' || $filename === '..') {
            return;
        }

        $path = public_path('upload/user_images/' . $filename);

        if (File::isFile($path)) {
            File::delete($path);
        }
    }

    public function PasswordUpdate(Request $request)
    {
            $user = Auth::user();

            $request->validate([
            'old_password' => [
                'required',
                'string',
            ],

            'new_password' => [
                'required',
                'string',
                'min:10',
                'confirmed',

                // ต้องมีตัวอักษรอย่างน้อย 1 ตัว
                // และมีตัวเลขอย่างน้อย 1 ตัว
                'regex:/^(?=.*\p{L})(?=.*\d).+$/u',
            ],

            'new_password_confirmation' => [
                'required',
                'string',
            ],
        ], [
            'old_password.required' => 'กรุณากรอกรหัสผ่านปัจจุบัน',

            'new_password.required' => 'กรุณากรอกรหัสผ่านใหม่',
            'new_password.min' => 'รหัสผ่านใหม่ต้องมีอย่างน้อย 10 ตัวอักษร',
            'new_password.regex' => 'รหัสผ่านใหม่ต้องมีทั้งตัวอักษรและตัวเลขอย่างน้อยอย่างละ 1 ตัว',
            'new_password.confirmed' => 'การยืนยันรหัสผ่านใหม่ไม่ตรงกัน',

            'new_password_confirmation.required' => 'กรุณายืนยันรหัสผ่านใหม่',
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
            AuditLogger::log(
                action: 'UPDATE',
                module: 'account_security',
                result: 'failed',
                statusCode: 302,
                metadata: [
                    'security_event' => 'password_change',
                    'reason' => 'current_password_mismatch',
                    'channel' => 'admin_profile',
                ],
                userId: (int) $user->getAuthIdentifier()
            );

            return back()->with([
                'message' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง',
                'alert-type' => 'error',
            ]);
        }

        $userId = (int) $user->getAuthIdentifier();

        User::whereKey($userId)->update([
            'password' => Hash::make($request->new_password),
        ]);

        AuditLogger::log(
            action: 'UPDATE',
            module: 'account_security',
            result: 'success',
            statusCode: 302,
            metadata: [
                'security_event' => 'password_change',
                'channel' => 'admin_profile',
            ],
            userId: $userId
        );

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with([
            'message' => 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว กรุณาเข้าสู่ระบบอีกครั้ง',
            'alert-type' => 'success',
        ]);
    }
}
