<?php

namespace App\Http\Middleware;

use App\Services\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * ยุติ session ของบัญชีที่ถูกปิดใช้งานทันทีที่มี request ครั้งถัดไป
     *
     * ช่วยให้การเปลี่ยนสถานะผู้ใช้มีผลกับ session เดิมด้วย ไม่ใช่เฉพาะการ login ใหม่
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && (string) ($user->status ?? '0') !== '1') {
            $userId = (int) $user->getAuthIdentifier();

            AuditLogger::log(
                action: 'ACCESS_DENIED',
                module: 'account_security',
                result: 'denied',
                statusCode: 401,
                metadata: [
                    'reason' => 'inactive_account',
                ],
                userId: $userId
            );

            Auth::guard('web')->logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'บัญชีนี้ถูกปิดใช้งาน',
                ], 401);
            }

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'บัญชีนี้ถูกปิดใช้งาน กรุณาติดต่อผู้ดูแลระบบ',
                ]);
        }

        return $next($request);
    }
}
