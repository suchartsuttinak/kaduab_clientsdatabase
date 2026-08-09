<?php

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogger;
use Tests\Support\AuditRegressionHarness;

it('removes sensitive field names and unsafe metadata before writing an audit log', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'status' => '1',
    ]);

    AuditRegressionHarness::namedRequest(
        'POST',
        'audit.regression.privacy',
        $admin
    );

    $log = AuditLogger::log(
        action: 'UPDATE',
        module: 'AUDIT_REGRESSION',
        subject: $admin,
        changedFields: [
            'status',
            'password',
            'email',
            'diagnosis',
            'status',
        ],
        result: 'success',
        statusCode: 200,
        metadata: [
            'reason' => 'regression_test',
            'password' => 'must_not_be_stored',
            'email' => 'must_not_be_stored@example.test',
            'nested_payload' => ['must' => 'not_be_stored'],
        ],
        userId: (int) $admin->id
    );

    expect($log)->not->toBeNull()
        ->and($log->action)->toBe('UPDATE')
        ->and($log->module)->toBe('audit_regression')
        ->and($log->changed_fields)->toBe(['status'])
        ->and($log->metadata)->toBe([
            'reason' => 'regression_test',
        ]);
});

it('keeps an existing audit row immutable through the AuditLog model', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'status' => '1',
    ]);

    AuditRegressionHarness::namedRequest(
        'POST',
        'audit.regression.immutable',
        $admin
    );

    $log = AuditLogger::log(
        action: 'LOGIN',
        module: 'authentication',
        result: 'success',
        statusCode: 200,
        userId: (int) $admin->id
    );

    expect($log)->not->toBeNull();

    $log->action = 'DELETE';

    expect($log->save())->toBeFalse()
        ->and($log->delete())->toBeFalse();

    $fresh = AuditLog::query()->findOrFail($log->id);

    expect($fresh->action)->toBe('LOGIN');
});
