<?php

namespace App\Support;

use App\Models\User;

final class FormPermissionMenu
{
    private const CLIENT_GROUPS = [
        'registration',
        'education',
        'health',
        'screening',
        'social_welfare',
    ];

    /**
     * Permission ของ Project
     */
    private const PROJECT_PERMISSION = 'master_projects';

    /**
     * สิทธิ์สำรองชั่วคราว
     * ใช้จนกว่าจะเพิ่ม master_projects ลงใน config/user_permissions.php
     */
    private const PROJECT_FALLBACK_PERMISSION = 'master_semesters';


    public static function forUser(?User $user): array
    {
        $groups = config('user_permissions.groups', []);

        /*
        |--------------------------------------------------------------------------
        | Compatibility: Project
        |--------------------------------------------------------------------------
        |
        | ถ้า config/user_permissions.php ยังไม่มี master_projects
        | ให้เพิ่มเข้า master_data ชั่วคราว เพื่อให้ FormPermissionMenu
        | รู้จัก permission key นี้
        |
        */
        if (!isset($groups['master_data'])) {
            $groups['master_data'] = [
                'label' => 'ข้อมูลอ้างอิง',
                'items' => [],
            ];
        }

        if (!isset($groups['master_data']['items'])) {
            $groups['master_data']['items'] = [];
        }

        if (!array_key_exists(
            self::PROJECT_PERMISSION,
            $groups['master_data']['items']
        )) {
            $groups['master_data']['items'][self::PROJECT_PERMISSION]
                = 'รายการโครงการ';
        }


        $forms = [];
        $visibleGroups = [];


        /*
        |--------------------------------------------------------------------------
        | Guest
        |--------------------------------------------------------------------------
        */
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
                'has_any_client_form' => false,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | โหลดสิทธิ์ของ User
        |--------------------------------------------------------------------------
        */
        $user->loadMissing('formPermissions');


        /*
        |--------------------------------------------------------------------------
        | ตรวจ Permission
        |--------------------------------------------------------------------------
        */
        foreach ($groups as $groupKey => $group) {

            $groupVisible = false;

            foreach (array_keys($group['items'] ?? []) as $permissionKey) {

                /*
                |--------------------------------------------------------------------------
                | Project Permission
                |--------------------------------------------------------------------------
                |
                | ตรวจ master_projects ก่อน
                |
                | ถ้ายังไม่มี record สิทธิ์ master_projects
                | ให้ใช้ master_semesters เป็น fallback ชั่วคราว
                |
                */
                if ($permissionKey === self::PROJECT_PERMISSION) {

                    $allowed = $user->canViewForm(
                        self::PROJECT_PERMISSION
                    );

                    if (!$allowed) {
                        $allowed = $user->canViewForm(
                            self::PROJECT_FALLBACK_PERMISSION
                        );
                    }

                } else {

                    $allowed = $user->canViewForm($permissionKey);
                }


                $forms[$permissionKey] = (bool) $allowed;

                $groupVisible = $groupVisible || (bool) $allowed;
            }

            $visibleGroups[$groupKey] = $groupVisible;
        }


        /*
        |--------------------------------------------------------------------------
        | Client Group
        |--------------------------------------------------------------------------
        */
        $hasAnyClientForm = false;

        foreach (self::CLIENT_GROUPS as $groupKey) {

            if (($visibleGroups[$groupKey] ?? false) === true) {

                $hasAnyClientForm = true;

                break;
            }
        }


        return [
            'forms' => $forms,
            'groups' => $visibleGroups,
            'has_any' => in_array(true, $visibleGroups, true),
            'has_any_client_form' => $hasAnyClientForm,
        ];
    }


    /**
     * Route แรกที่ User สามารถเข้าได้
     */
    public static function firstAccessibleRouteName(?User $user): string
    {
        if (!$user) {
            return 'landing.index';
        }

        $menu = self::forUser($user);

        $forms = $menu['forms'];

        $can = static fn (string $key): bool =>
            (bool) ($forms[$key] ?? false);


        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
        if ($can('dashboard_overview')) {
            return 'dashboard';
        }


        /*
        |--------------------------------------------------------------------------
        | Client
        |--------------------------------------------------------------------------
        */
        if ($menu['has_any_client_form']) {
            return 'client.show';
        }


        /*
        |--------------------------------------------------------------------------
        | Ordered Routes
        |--------------------------------------------------------------------------
        */
        $orderedRoutes = [

            // Registration
            'registration_central_cases'
                => 'client.cases',

            'registration_project_transfer'
                => 'client.transfers',

            'registration_house_transfer'
                => 'client-house-transfers.index',


            // Dashboard
            'dashboard_issues'
                => 'issues.index',

            'dashboard_news'
                => 'news.create',

            'dashboard_about'
                => 'landing.about.index',

            'dashboard_scholarship_sponsors'
                => 'scholarship.index',

            'dashboard_scholarship_children'
                => 'scholarship.children.index',

            'dashboard_child_analytics'
                => 'child.analytics.report.index',


            // Master Data
            'master_institutions'
                => 'institution.all',

            'master_subjects'
                => 'subject.show',

            'master_houses'
                => 'house.show',

            'master_education_levels'
                => 'education.show',

            'master_semesters'
                => 'semester.show',

            /*
            |--------------------------------------------------------------------------
            | Project
            |--------------------------------------------------------------------------
            */
            'master_projects'
                => 'project.show',

            'master_psychiatric_diseases'
                => 'psycho.show',

            'master_behaviors'
                => 'misbehavior.show',

            'master_outside_types'
                => 'outside.show',

            'master_documents'
                => 'document.show',

            'master_incomes'
                => 'income.show',

            'master_help_types'
                => 'help_type.show',

            'master_citizenships'
                => 'citizenship.show',

            'master_citizen_statuses'
                => 'citizen.show',

            'master_release_types'
                => 'translate.show',


            // Reports
            'report_discharge_all'
                => 'refers.all',


            // System
            'system_users'
                => 'users.index',
        ];


        foreach ($orderedRoutes as $permissionKey => $routeName) {

            if ($can($permissionKey)) {
                return $routeName;
            }
        }


        return 'client.show';
    }
}