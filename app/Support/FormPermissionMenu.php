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
                'has_any_client_form' => false,
            ];
        }

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

    public static function firstAccessibleRouteName(?User $user): string
    {
        if (!$user) {
            return 'landing.index';
        }

        $menu = self::forUser($user);
        $forms = $menu['forms'];
        $can = static fn (string $key): bool => (bool) ($forms[$key] ?? false);

        if ($can('dashboard_overview')) {
            return 'dashboard';
        }

        if ($menu['has_any_client_form']) {
            return 'client.show';
        }

        $orderedRoutes = [
            'registration_central_cases' => 'client.cases',
            'registration_project_transfer' => 'client.transfers',
            'registration_house_transfer' => 'client-house-transfers.index',
            'dashboard_issues' => 'issues.index',
            'dashboard_news' => 'news.create',
            'dashboard_about' => 'landing.about.index',
            'dashboard_scholarship_sponsors' => 'scholarship.index',
            'dashboard_scholarship_children' => 'scholarship.children.index',
            'dashboard_child_analytics' => 'child.analytics.report.index',
            'master_institutions' => 'institution.all',
            'master_subjects' => 'subject.show',
            'master_houses' => 'house.show',
            'master_education_levels' => 'education.show',
            'master_semesters' => 'semester.show',
            'master_psychiatric_diseases' => 'psycho.show',
            'master_behaviors' => 'misbehavior.show',
            'master_outside_types' => 'outside.show',
            'master_documents' => 'document.show',
            'master_incomes' => 'income.show',
            'master_help_types' => 'help_type.show',
            'master_citizenships' => 'citizenship.show',
            'master_citizen_statuses' => 'citizen.show',
            'master_release_types' => 'translate.show',
            'report_discharge_all' => 'refers.all',
            'system_users' => 'users.index',
        ];

        foreach ($orderedRoutes as $permissionKey => $routeName) {
            if ($can($permissionKey)) {
                return $routeName;
            }
        }

        return 'client.show';
    }
}
