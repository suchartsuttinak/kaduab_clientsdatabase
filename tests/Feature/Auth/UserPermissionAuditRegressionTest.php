<?php

use App\Models\AuditLog;
use App\Models\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tests\Support\AuditRegressionHarness;

it('does not create an empty generic UPDATE row for a successful users.update without mutation context', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'status' => '1',
        'form_permissions_enabled' => false,
    ]);

    $request = AuditRegressionHarness::namedRequest(
        'PUT',
        'users.update',
        $admin
    );

    AuditRegressionHarness::invokeAuditMutation(
        request: $request,
        response: new RedirectResponse('/admin/users', 302),
        user: $admin,
        permissionKeys: ['system_users'],
        action: 'update'
    );

    expect(
        AuditLog::query()
            ->where('route_name', 'users.update')
            ->where('action', 'UPDATE')
            ->count()
    )->toBe(0);
});

it('records one permission change with the target user and no empty duplicate UPDATE', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'status' => '1',
        'form_permissions_enabled' => false,
    ]);

    $target = User::factory()->create([
        'role' => User::ROLE_SOCIAL_WORKER,
        'status' => '1',
        'form_permissions_enabled' => true,
        'project_id' => null,
    ]);

    $response = $this
        ->actingAs($admin)
        ->put(route('users.update', ['id' => $target->id]), [
            // ข้อมูล User หลักคงเดิมทั้งหมด
            'name' => $target->name,
            'email' => $target->email,
            'phone' => $target->phone,
            'address' => $target->address,
            'role' => $target->role,
            'status' => (string) $target->status,
            'project_id' => null,
            'form_permissions_enabled' => '1',

            // เปลี่ยนเฉพาะ Permission relation
            'permissions' => [
                'system_users' => [
                    'view' => '1',
                ],
            ],
        ]);

    $response->assertRedirect(route('users.index'));

    $permissionLogs = AuditLog::query()
        ->where('route_name', 'users.update')
        ->where('action', 'PERMISSION_CHANGE')
        ->get();

    expect($permissionLogs)->toHaveCount(1);

    $log = $permissionLogs->first();

    expect($log)->not->toBeNull()
        ->and($log->module)->toBe('system_users')
        ->and($log->subject_type)->toBe((new User())->getMorphClass())
        ->and((int) $log->subject_id)->toBe((int) $target->id)
        ->and($log->client_id)->toBeNull()
        ->and($log->result)->toBe('success')
        ->and($log->changed_fields)->toContain('form_permissions');

    $emptyGenericUpdateCount = AuditLog::query()
        ->where('route_name', 'users.update')
        ->where('action', 'UPDATE')
        ->whereNull('subject_type')
        ->whereNull('subject_id')
        ->count();

    expect($emptyGenericUpdateCount)->toBe(0);
});
