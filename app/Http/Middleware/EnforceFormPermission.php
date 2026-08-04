<?php

namespace App\Http\Middleware;

use App\Support\FormPermissionUi;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EnforceFormPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return $next($request);
        }

        $rule = FormPermissionUi::findRule($routeName);

        if ($rule === null) {
            return $next($request);
        }

        $user = $request->user();

        if (!$user || !method_exists($user, 'hasFormPermission')) {
            return $next($request);
        }

        $permissionKeys = $this->permissionKeys($rule);
        $action = (string) ($rule['action'] ?? 'view');
        $allowed = $this->isAllowed($user, $permissionKeys, $action);

        if ($allowed) {
            return $next($request);
        }

        /*
         * มาตรฐานโหมดอ่านอย่างเดียว:
         * หน้า GET/HEAD ที่ใช้ฟอร์มแก้ไขสามารถเปิดดูข้อมูลเดิมได้
         * แต่คำขอ POST/PUT/PATCH/DELETE ยังถูกปฏิเสธจากฝั่งเซิร์ฟเวอร์เสมอ
         */
        if (
            $action === 'update'
            && $request->isMethodSafe()
            && $this->isAllowed($user, $permissionKeys, 'view')
        ) {
            $request->attributes->set('form_permission_readonly', true);
            $request->attributes->set('form_permission_keys', $permissionKeys);

            return $next($request);
        }

        /* ผู้ไม่มีสิทธิ์ Dashboard ให้กลับไปทะเบียนผู้รับบริการแทนหน้า 403 */
        if ($routeName === 'dashboard' && $request->isMethodSafe()) {
            return redirect()
                ->route('client.show')
                ->with('info', 'บัญชีนี้ไม่ได้รับสิทธิ์หน้า Dashboard ระบบจึงนำไปยังทะเบียนผู้รับบริการ');
        }

        $message = $this->deniedMessage($action);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'action' => $action,
            ], 403);
        }

        abort(403, $message);
    }

    /** @return list<string> */
    private function permissionKeys(array $rule): array
    {
        $keys = $rule['permissions'] ?? $rule['permission'] ?? [];

        return array_values(array_unique(array_filter(
            array_map(static fn ($key): string => trim((string) $key), (array) $keys),
            static fn (string $key): bool => $key !== ''
        )));
    }

    private function isAllowed(mixed $user, array $permissionKeys, string $action): bool
    {
        if ($permissionKeys === []) {
            return true;
        }

        try {
            if (count($permissionKeys) === 1) {
                return (bool) $user->hasFormPermission($permissionKeys[0], $action);
            }

            if (method_exists($user, 'hasAnyFormPermission')) {
                return (bool) $user->hasAnyFormPermission($permissionKeys, $action);
            }

            foreach ($permissionKeys as $permissionKey) {
                if ($user->hasFormPermission($permissionKey, $action)) {
                    return true;
                }
            }
        } catch (Throwable) {
            return false;
        }

        return false;
    }

    private function deniedMessage(string $action): string
    {
        return match ($action) {
            'create' => 'บัญชีนี้เป็นโหมดอ่านอย่างเดียวและไม่มีสิทธิ์เพิ่มข้อมูล',
            'update' => 'บัญชีนี้เป็นโหมดอ่านอย่างเดียวและไม่มีสิทธิ์แก้ไขข้อมูล',
            'delete' => 'บัญชีนี้เป็นโหมดอ่านอย่างเดียวและไม่มีสิทธิ์ลบข้อมูล',
            'print'  => 'บัญชีนี้ไม่มีสิทธิ์พิมพ์หรือเปิดรายงาน',
            default  => 'คุณไม่มีสิทธิ์ใช้งานหน้าหรือดำเนินการรายการนี้',
        };
    }
}
