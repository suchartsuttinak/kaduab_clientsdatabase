<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        /*
         * Security audit: บันทึกเฉพาะว่าเปลี่ยนรหัสผ่านสำเร็จ
         * ไม่บันทึกรหัสผ่านเดิม/ใหม่, confirmation, token หรือ request payload
         */
        AuditLogger::log(
            action: 'UPDATE',
            module: 'account_security',
            result: 'success',
            statusCode: 302,
            metadata: [
                'security_event' => 'password_change',
                'channel' => 'profile',
            ],
            userId: (int) $user->getAuthIdentifier()
        );

        return back()->with('status', 'password-updated');
    }
}
