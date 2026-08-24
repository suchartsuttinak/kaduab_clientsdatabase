<?php

namespace App\Http\Controllers\Concerns;

trait AuthorizesUniversityTracking
{
    protected function universityCan(string $action): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if (method_exists($user, 'hasFormPermission')) {
            return (bool) $user->hasFormPermission(
                config('university_tracking.permission_key', 'education_university'),
                $action
            );
        }

        // รองรับโครงการที่ยังไม่ได้เปิดระบบ permission รายฟอร์ม
        return true;
    }

    protected function requireUniversityPermission(string $action): void
    {
        abort_unless($this->universityCan($action), 403, 'คุณไม่มีสิทธิ์ดำเนินการในโมดูลติดตามการศึกษาระดับอุดมศึกษา');
    }

    protected function universityPermissionBag(): array
    {
        return [
            'view' => $this->universityCan('view'),
            'create' => $this->universityCan('create'),
            'update' => $this->universityCan('update'),
            'delete' => $this->universityCan('delete'),
            'print' => $this->universityCan('print'),
        ];
    }
}
