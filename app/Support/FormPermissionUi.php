<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

final class FormPermissionUi
{
    /**
     * สร้างข้อมูลสำหรับซ่อนปุ่ม/ลิงก์ที่ผู้ใช้ไม่มีสิทธิ์
     * โดยใช้ route_permissions ชุดเดียวกับ Middleware
     */
    public static function forUser(?User $user, ?string $currentRouteName = null): array
    {
        if (!$user || $user->isAdmin() || !$user->form_permissions_enabled) {
            return [
                'enabled' => false,
                'denied_routes' => [],
                'current' => null,
            ];
        }

        $user->loadMissing('formPermissions');
        $rules = config('user_permissions.route_permissions', []);
        $denied = [];
        $current = null;

        foreach ($rules as $rule) {
            $action = (string) ($rule['action'] ?? 'view');
            $permissionKeys = array_values(array_filter((array) ($rule['permissions'] ?? [])));
            $allowed = self::isAllowed($user, $permissionKeys, $action);

            if ($currentRouteName && self::matchesAnyPattern($currentRouteName, (array) ($rule['routes'] ?? []))) {
                $current ??= self::currentPermissionState($user, $permissionKeys);
            }

            if ($allowed) {
                continue;
            }

            foreach (Route::getRoutes() as $route) {
                $routeName = $route->getName();

                if (!$routeName || !self::matchesAnyPattern($routeName, (array) ($rule['routes'] ?? []))) {
                    continue;
                }

                $denied[$routeName . '|' . $action] = [
                    'name' => $routeName,
                    'action' => $action,
                    'methods' => array_values(array_diff($route->methods(), ['HEAD'])),
                    'pattern' => self::uriRegex($route),
                ];
            }
        }

        return [
            'enabled' => true,
            'denied_routes' => array_values($denied),
            'current' => $current,
        ];
    }

    private static function currentPermissionState(User $user, array $permissionKeys): ?array
    {
        if (count($permissionKeys) !== 1) {
            return null;
        }

        $key = $permissionKeys[0];

        return [
            'permission_key' => $key,
            'view' => $user->canViewForm($key),
            'create' => $user->canCreateForm($key),
            'update' => $user->canUpdateForm($key),
            'delete' => $user->canDeleteForm($key),
            'print' => $user->canPrintForm($key),
        ];
    }

    private static function isAllowed(User $user, array $permissionKeys, string $action): bool
    {
        if ($permissionKeys === []) {
            return true;
        }

        return count($permissionKeys) === 1
            ? $user->hasFormPermission($permissionKeys[0], $action)
            : $user->hasAnyFormPermission($permissionKeys, $action);
    }

    private static function matchesAnyPattern(string $routeName, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (Str::is((string) $pattern, $routeName)) {
                return true;
            }
        }

        return false;
    }

    private static function uriRegex(LaravelRoute $route): string
    {
        $uri = trim($route->uri(), '/');

        if ($uri === '') {
            return '^/$';
        }

        $regex = '^';

        foreach (explode('/', $uri) as $segment) {
            if (preg_match('/^\{[^}]+\?\}$/', $segment)) {
                $regex .= '(?:/[^/]+)?';
            } elseif (preg_match('/^\{[^}]+\}$/', $segment)) {
                $regex .= '/[^/]+';
            } else {
                $regex .= '/' . preg_quote($segment, '/');
            }
        }

        return $regex . '/?$';
    }
}
