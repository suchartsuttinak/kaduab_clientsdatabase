<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnforceFormPermission
{
    /**
     * ตรวจสิทธิ์จากชื่อ Route โดยใช้ config/user_permissions.php
     *
     * Middleware นี้ปล่อย Route ที่ไม่ได้อยู่ใน route_permissions ผ่านตามเดิม
     * จึงไม่กระทบ Dashboard, Login และเมนูส่วนกลางอื่นของระบบ
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return $next($request);
        }

        $rule = $this->findRule($routeName);

        if ($rule === null) {
            return $next($request);
        }

        $user = $request->user();

        // ปล่อยให้ auth middleware เดิมจัดการกรณียังไม่ได้เข้าสู่ระบบ
        if (!$user) {
            return $next($request);
        }

        $action = (string) ($rule['action'] ?? 'view');
        $permissionKeys = array_values(array_filter((array) ($rule['permissions'] ?? [])));

        $allowed = count($permissionKeys) === 1
            ? $user->hasFormPermission($permissionKeys[0], $action)
            : $user->hasAnyFormPermission($permissionKeys, $action);

        if (!$allowed) {
            $message = 'คุณไม่มีสิทธิ์ใช้งานฟอร์มหรือดำเนินการรายการนี้';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], 403);
            }

            abort(403, $message);
        }

        return $next($request);
    }

    private function findRule(string $routeName): ?array
    {
        foreach (config('user_permissions.route_permissions', []) as $rule) {
            foreach ((array) ($rule['routes'] ?? []) as $pattern) {
                if (Str::is($pattern, $routeName)) {
                    return $rule;
                }
            }
        }

        return null;
    }
}
