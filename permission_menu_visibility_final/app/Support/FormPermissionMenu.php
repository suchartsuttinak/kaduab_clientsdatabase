<?php

namespace App\Support;

use App\Models\User;

final class FormPermissionMenu
{
    /**
     * สร้างข้อมูลสิทธิ์สำหรับใช้ในเมนู โดยใช้ permission key ชุดเดียวกับ
     * config/user_permissions.php และตาราง user_form_permissions
     */
    public static function forUser(?User $user): array
    {
        $groups = config('user_permissions.groups', []);
        $forms = [];
        $visibleGroups = [];

        if (!$user) {
            foreach ($groups as $groupKey => $group) {
                $visibleGroups[$groupKey] = false;
                foreach (array_keys($group['items'] ?? []) as $permissionKey) {
                    $forms[$permissionKey] = false;
                }
            }

            return [
                'forms' => $forms,
                'groups' => $visibleGroups,
                'has_any' => false,
            ];
        }

        // โหลดครั้งเดียวต่อ request เพื่อลด query ซ้ำใน Topbar และ Sidebar
        $user->loadMissing('formPermissions');

        foreach ($groups as $groupKey => $group) {
            $groupVisible = false;

            foreach (array_keys($group['items'] ?? []) as $permissionKey) {
                $allowed = $user->canViewForm($permissionKey);
                $forms[$permissionKey] = $allowed;
                $groupVisible = $groupVisible || $allowed;
            }

            $visibleGroups[$groupKey] = $groupVisible;
        }

        return [
            'forms' => $forms,
            'groups' => $visibleGroups,
            'has_any' => in_array(true, $visibleGroups, true),
        ];
    }
}
