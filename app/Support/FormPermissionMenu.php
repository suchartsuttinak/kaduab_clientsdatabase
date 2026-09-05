<?php

namespace App\Support;

use App\Models\User;

final class FormPermissionMenu
{
    /**
     * สิทธิ์ที่ต้องมีผู้รับบริการรายบุคคลเป็นจุดเริ่มต้นจริง
     * ไม่รวมเมนูส่วนกลาง เช่น ศูนย์รับเคส/ศูนย์พัฒนาเด็ก เพื่อไม่ให้ Login
     * ส่งไป client.show ทั้งที่ route นั้นไม่ได้รับอนุญาต
     */
    private const CLIENT_FORM_PERMISSIONS = [
        'registration_client_profile',
        'registration_factfinding',
        'registration_family',
        'registration_family_assessment',
        'registration_family_visit',
        'registration_family_members',
        'registration_client_files',
        'registration_client_reports',
        'education_grade_entry',
        'education_results',
        'education_followup',
        'education_absence',
        'education_university',
        'health_accident',
        'health_body_check',
        'health_treatment_rights',
        'health_medical',
        'health_vaccination',
        'health_psychiatric',
        'health_addictive',
        'health_annual_checkup',
        'screening_behavior_four_diseases',
        'screening_snap_iv',
        'screening_depression',
        'screening_nutrition',
        'individual_development',
        'welfare_counseling',
        'welfare_behavior_problem',
        'welfare_escape',
        'welfare_outside_followup',
        'welfare_discharge',
        'welfare_job_agency',
        'welfare_help_items',
        'welfare_followup',
        'welfare_client_activity',
        'welfare_stateless_person',
    ];

    private const MASTER_DATA_MENU_PERMISSION = 'master_data_menu';


    public static function forUser(?User $user): array
    {
        $groups = config('user_permissions.groups', []);

        // USER_PERMISSION_VISIBILITY_FIX_V2: ใช้เฉพาะ permission ที่ประกาศจริงใน config


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

                $allowed = $user->canViewForm($permissionKey);


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

        foreach (self::CLIENT_FORM_PERMISSIONS as $permissionKey) {
            if (($forms[$permissionKey] ?? false) === true) {
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
     * USER_MULTI_PROJECT_SCOPE_V5
     * Compatibility helper: การมีขอบเขต Project/House ไม่ใช่เงื่อนไขเปิดเมนูอีกต่อไป
     * เพราะ "ไม่เลือก" หมายถึง "ทุกหน่วยงาน/ทุกบ้าน" ตามนโยบายใหม่
     * การเข้าหน้า /client จึงตัดสินจากสิทธิ์รายฟอร์มแทน
     */
    public static function hasClientDataScope(?User $user): bool
    {
        return (bool) $user;
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
        | USER_MULTI_PROJECT_SCOPE_V5 : ทางเข้าแฟ้มผู้รับบริการ
        |--------------------------------------------------------------------------
        | Project / House จำกัด "ข้อมูลที่เห็น" แต่ไม่ใช่สิทธิ์เปิดเมนู
        | หากมีสิทธิ์รายฟอร์มที่ทำงานกับผู้รับบริการอย่างน้อยหนึ่งรายการ
        | จึงให้เข้า client.show เพื่อเลือกผู้รับบริการในขอบเขตของตน
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

            // Communications / Operations
            'communications_publicizes'
                => 'publicizes.index',

            'communications_operations'
                => 'operations.index',


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
            'report_special_children'
                => 'special_children.index',

            'report_discharge_all'
                => 'refers.all',


            // System
            'system_users'
                => 'users.index',
        ];


        foreach ($orderedRoutes as $permissionKey => $routeName) {
            if (str_starts_with($permissionKey, 'master_')
                && $permissionKey !== self::MASTER_DATA_MENU_PERMISSION
                && !$can(self::MASTER_DATA_MENU_PERMISSION)) {
                continue;
            }

            if ($can($permissionKey)) {
                return $routeName;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Executive governance exception
        |--------------------------------------------------------------------------
        | ผู้บริหารยังคงมีหน้าที่จัดการบัญชีและมอบสิทธิ์ให้เจ้าหน้าที่ระดับล่าง
        | แม้ไม่ได้รับสิทธิ์เมนูงานอื่น จึงให้ User Management เป็น fallback
        | เฉพาะผู้บริหารเท่านั้น (Admin มีสิทธิ์ทุกเมนูอยู่แล้ว)
        */
        if (method_exists($user, 'isExecutive') && $user->isExecutive()) {
            return 'users.index';
        }

        // PERMISSION_LANDING_LOGOUT_FIX_V3:
        // ไม่มีหน้าใช้งานที่ได้รับสิทธิ์ -> ไปหน้าสถานะสิทธิ์เฉพาะ
        // ห้ามใช้ profile.edit เป็น fallback เพราะไม่ใช่หน้าทำงานของระบบ
        return 'access.no_permissions';
    }
}