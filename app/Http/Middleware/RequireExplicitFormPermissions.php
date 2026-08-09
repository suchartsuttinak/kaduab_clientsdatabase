<?php

namespace App\Http\Middleware;

use App\Services\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireExplicitFormPermissions
{
    /**
     * ใช้กับโมดูลระบบที่มีความอ่อนไหวสูง เช่น จัดการผู้ใช้และ Audit Log
     *
     * Admin ผ่านได้เสมอ ส่วนผู้ใช้อื่นต้องเปิดโหมดสิทธิ์รายฟอร์มก่อน
     * จากนั้น EnforceFormPermission จะตรวจ can_view/create/update/delete ต่ออีกชั้น
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->guest(route('login'));
        }

        if (
            method_exists($user, 'isAdmin')
            && $user->isAdmin()
        ) {
            return $next($request);
        }

        if ((bool) ($user->form_permissions_enabled ?? false)) {
            return $next($request);
        }

        AuditLogger::log(
            action: 'ACCESS_DENIED',
            module: 'authorization',
            result: 'denied',
            statusCode: 403,
            metadata: [
                'reason' => 'explicit_form_permission_required',
            ],
            userId: (int) $user->getAuthIdentifier()
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'บัญชีนี้ไม่ได้เปิดใช้สิทธิ์รายฟอร์มสำหรับส่วนจัดการระบบ',
            ], 403);
        }

        abort(403, 'บัญชีนี้ไม่ได้รับสิทธิ์เข้าส่วนจัดการระบบ');
    }
}
