<?php

use App\Models\Accident;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Vaccination;
use App\Services\AuditMutationContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tests\Support\AuditRegressionHarness;

it('resolves client_id only from explicit model relationships and attributes', function () {
    $accident = new Accident([
        'client_id' => 207,
    ]);
    $accident->setAttribute('id', 55);

    $vaccination = new Vaccination([
        'client_id' => 206,
    ]);
    $vaccination->setAttribute('id', 31);

    expect(AuditRegressionHarness::resolveClientId($accident))->toBe(207)
        ->and(AuditRegressionHarness::resolveClientId($vaccination))->toBe(206);
});

it('selects the route-matching mutation as the primary target', function () {
    $request = AuditRegressionHarness::namedRequest(
        'PUT',
        'accident.update'
    );

    AuditRegressionHarness::putMutationContext($request, [
        [
            'action' => 'update',
            'subject_type' => (new User())->getMorphClass(),
            'subject_class' => User::class,
            'subject_id' => 7,
            'client_id' => null,
            'changed_fields' => ['status'],
        ],
        [
            'action' => 'update',
            'subject_type' => (new Accident())->getMorphClass(),
            'subject_class' => Accident::class,
            'subject_id' => 55,
            'client_id' => 207,
            'changed_fields' => ['location'],
        ],
    ]);

    $context = AuditMutationContext::primary($request, 'update');

    expect($context)->toBeArray()
        ->and($context['subject_type'] ?? null)->toBe((new Accident())->getMorphClass())
        ->and((int) ($context['subject_id'] ?? 0))->toBe(55)
        ->and((int) ($context['client_id'] ?? 0))->toBe(207)
        ->and($context['changed_fields'] ?? [])->toBe(['location']);
});

it('writes target and client columns from a verified mutation context', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'status' => '1',
    ]);

    $request = AuditRegressionHarness::namedRequest(
        'PUT',
        'accident.update',
        $admin
    );

    AuditRegressionHarness::putMutationContext($request, [
        [
            'action' => 'update',
            'subject_type' => (new Accident())->getMorphClass(),
            'subject_class' => Accident::class,
            'subject_id' => 55,
            'client_id' => 207,
            'changed_fields' => ['location'],
        ],
    ]);

    AuditRegressionHarness::invokeAuditMutation(
        request: $request,
        response: new RedirectResponse('/accident', 302),
        user: $admin,
        permissionKeys: ['health_accident'],
        action: 'update'
    );

    $log = AuditLog::query()
        ->where('route_name', 'accident.update')
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->action)->toBe('UPDATE')
        ->and($log->module)->toBe('health_accident')
        ->and($log->subject_type)->toBe((new Accident())->getMorphClass())
        ->and((int) $log->subject_id)->toBe(55)
        ->and((int) $log->client_id)->toBe(207)
        ->and($log->changed_fields)->toBe(['location'])
        ->and($log->result)->toBe('success')
        ->and((int) $log->status_code)->toBe(302);
});
