<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFormPermission
{
    public function handle(
        Request $request,
        Closure $next,
        string $permissionKey,
        string $action = 'view'
    ): Response {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        if ($user->hasFormPermission($permissionKey, $action)) {
            return $next($request);
        }

        /* เปิด GET ของหน้าแก้ไขเพื่อดูข้อมูลเดิมแบบอ่านอย่างเดียว */
        if (
            $action === 'update'
            && $request->isMethodSafe()
            && $user->hasFormPermission($permissionKey, 'view')
        ) {
            $request->attributes->set('form_permission_readonly', true);
            $request->attributes->set('form_permission_keys', [$permissionKey]);

            return $next($request);
        }

        $message = match ($action) {
            'create' => 'บัญชีนี้ไม่มีสิทธิ์เพิ่มข้อมูล',
            'update' => 'บัญชีนี้ไม่มีสิทธิ์แก้ไขข้อมูล',
            'delete' => 'บัญชีนี้ไม่มีสิทธิ์ลบข้อมูล',
            'print' => 'บัญชีนี้ไม่มีสิทธิ์พิมพ์หรือเปิดรายงาน',
            default => 'คุณไม่มีสิทธิ์ใช้งานส่วนนี้',
        };

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }

        abort(403, $message);
    }
}
