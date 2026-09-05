<?php

namespace App\Support;

use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Throwable;

final class FormPermissionUi
{
    /** @var array<string, array<string, mixed>|null> */
    private static array $resolvedRuleCache = [];

    /**
     * สร้างสถานะสิทธิ์สำหรับส่วนติดต่อผู้ใช้จาก Route ปัจจุบัน
     *
     * โครงสร้างนี้รองรับ config/user_permissions.php รูปแบบเดิมของระบบ:
     * - route_permissions[*].routes
     * - route_permissions[*].permissions หรือ permission
     * - route_permissions[*].action
     */
    public static function forUser(mixed $user, ?string $routeName): array
    {
        if (!$user || !method_exists($user, 'hasFormPermission')) {
            return self::disabledState($routeName);
        }

        // UNIFIED_ACCESS_SCOPE_V5:
        // สิทธิ์รายฟอร์มเป็นระบบหลักสำหรับผู้ใช้ทุกบทบาท (Admin bypass ใน User model)
        // จึงไม่ปิด Permission UI ตาม flag legacy form_permissions_enabled อีกต่อไป
        $currentRule = $routeName ? self::findRule($routeName) : null;

        if ($currentRule === null) {
            return self::disabledState($routeName);
        }

        $permissionKeys = self::permissionKeys($currentRule);
        // EPC_CAPABILITY_AWARE_READONLY_V1
        $availableActions = self::availableActions($permissionKeys);
        $current = [
            'view'   => self::isAllowed($user, $permissionKeys, 'view'),
            'create' => self::isAllowed($user, $permissionKeys, 'create'),
            'update' => self::isAllowed($user, $permissionKeys, 'update'),
            'delete' => self::isAllowed($user, $permissionKeys, 'delete'),
            'print'  => self::isAllowed($user, $permissionKeys, 'print'),
        ];

        // หน้า report/view-only โดยธรรมชาติ (เช่น Dashboard/Analytics/Center)
        // ไม่ควรถูกตีความว่าเป็น "โหมดอ่านอย่างเดียว" เพราะไม่มี write action ให้มอบตั้งแต่แรก
        $writeActions = array_values(array_intersect(
            ['create', 'update', 'delete'],
            $availableActions
        ));

        $hasGrantedWrite = false;
        foreach ($writeActions as $writeAction) {
            if (($current[$writeAction] ?? false) === true) {
                $hasGrantedWrite = true;
                break;
            }
        }

        $current['readonly'] = $current['view']
            && $writeActions !== []
            && !$hasGrantedWrite;

        return [
            'enabled'         => true,
            'route_name'      => $routeName,
            'route_action'    => (string) ($currentRule['action'] ?? 'view'),
            'permission_keys' => $permissionKeys,
            'available_actions' => $availableActions,
            'current'         => $current,
            'denied_routes'   => self::deniedRoutes($user),
        ];
    }

    private static function disabledState(?string $routeName): array
    {
        return [
            'enabled'         => false,
            'route_name'      => $routeName,
            'route_action'    => null,
            'permission_keys' => [],
            'available_actions' => [],
            'current'         => null,
            'denied_routes'   => [],
        ];
    }

    /**
     * เลือกกฎที่เฉพาะเจาะจงที่สุด ไม่ใช้กฎแรกที่พบเพียงอย่างเดียว
     * เพื่อป้องกัน wildcard กว้าง เช่น module.* ไปทับ module.edit/module.destroy
     */
    public static function findRule(string $routeName): ?array
    {
        if (array_key_exists($routeName, self::$resolvedRuleCache)) {
            return self::$resolvedRuleCache[$routeName];
        }

        $bestRule = null;
        $bestScore = PHP_INT_MIN;

        foreach ((array) config('user_permissions.route_permissions', []) as $rule) {
            if (!is_array($rule)) {
                continue;
            }

            foreach ((array) ($rule['routes'] ?? []) as $pattern) {
                $pattern = trim((string) $pattern);

                if ($pattern === '' || !Str::is($pattern, $routeName)) {
                    continue;
                }

                $wildcards = substr_count($pattern, '*') + substr_count($pattern, '?');
                $exactBonus = $pattern === $routeName ? 100000 : 0;
                $score = $exactBonus + (strlen($pattern) * 10) - ($wildcards * 1000);

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestRule = $rule;
                }
            }
        }

        return self::$resolvedRuleCache[$routeName] = $bestRule;
    }

    /** @return list<string> */
    private static function permissionKeys(array $rule): array
    {
        $keys = $rule['permissions'] ?? $rule['permission'] ?? [];

        return array_values(array_unique(array_filter(
            array_map(static fn ($key): string => trim((string) $key), (array) $keys),
            static fn (string $key): bool => $key !== ''
        )));
    }

    /** @return list<string> */
    private static function availableActions(array $permissionKeys): array
    {
        if ($permissionKeys === []) {
            return ['view', 'create', 'update', 'delete', 'print'];
        }

        $found = [];
        foreach ((array) config('user_permissions.groups', []) as $group) {
            foreach (($group['items'] ?? []) as $permissionKey => $item) {
                if (!in_array((string) $permissionKey, $permissionKeys, true)) {
                    continue;
                }

                foreach ((array) ($item['actions'] ?? []) as $action) {
                    $action = strtolower(trim((string) $action));
                    if ($action !== '') {
                        $found[$action] = true;
                    }
                }
            }
        }

        return array_values(array_keys($found));
    }
    private static function isAllowed(mixed $user, array $permissionKeys, string $action): bool
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
            // หาก helper ของระบบเดิมผิดพลาด ให้ฝั่ง Middleware เป็นผู้ตัดสินขั้นสุดท้าย
            return false;
        }

        return false;
    }

    /** @return list<array<string, mixed>> */
    private static function deniedRoutes(mixed $user): array
    {
        $denied = [];

        foreach (Route::getRoutes() as $route) {
            $routeName = $route->getName();

            if (!$routeName) {
                continue;
            }

            $rule = self::findRule($routeName);

            if ($rule === null) {
                continue;
            }

            $permissionKeys = self::permissionKeys($rule);
            $action = (string) ($rule['action'] ?? 'view');
            $methods = array_values(array_filter(
                array_map('strtoupper', $route->methods()),
                static fn (string $method): bool => $method !== 'HEAD'
            ));

            $allowed = self::isAllowed($user, $permissionKeys, $action);
            $usesMasterChildPermission = collect($permissionKeys)->contains(
                static fn (string $key): bool => str_starts_with($key, 'master_')
                    && $key !== 'master_data_menu'
            );
            $masterParentAllowed = !$usesMasterChildPermission
                || (bool) $user->canViewForm('master_data_menu');

            if (!$masterParentAllowed) {
                $allowed = false;
            }

            /*
             * GET ของหน้าแก้ไขยังเปิดได้เมื่อมีสิทธิ์ดู เพื่อแสดงข้อมูลแบบอ่านอย่างเดียว
             * แต่ต้องผ่านสิทธิ์เมนูหลัก “ประเภท / หมวดหมู่” ด้วยเช่นเดียวกับ Middleware
             */
            if (
                !$allowed
                && $masterParentAllowed
                && $action === 'update'
                && self::containsSafeReadMethod($methods)
                && self::isAllowed($user, $permissionKeys, 'view')
            ) {
                $allowed = true;
            }

            if ($allowed) {
                continue;
            }

            $denied[] = [
                'name'        => $routeName,
                'methods'     => $methods,
                'pattern'     => self::routePathPattern($route),
                'action'      => $action,
                'permissions' => $permissionKeys,
            ];
        }

        return $denied;
    }

    /** @param list<string> $methods */
    private static function containsSafeReadMethod(array $methods): bool
    {
        return in_array('GET', $methods, true) || in_array('HEAD', $methods, true);
    }

    private static function routePathPattern(LaravelRoute $route): string
    {
        $uri = trim((string) $route->uri(), '/');

        if ($uri === '') {
            return '^/?$';
        }

        $segments = explode('/', $uri);
        $pattern = '^';

        foreach ($segments as $segment) {
            if (preg_match('/^\{[^}]+\?\}$/', $segment) === 1) {
                $pattern .= '(?:/[^/?#]+)?';
                continue;
            }

            if (preg_match('/^\{[^}]+\}$/', $segment) === 1) {
                $pattern .= '/[^/?#]+';
                continue;
            }

            $literal = preg_quote($segment, '/');
            $literal = preg_replace('/\\\{[^}]+\\\}/', '[^/?#]+', $literal) ?? $literal;
            $pattern .= '/' . $literal;
        }

        return $pattern . '/?$';
    }
}
