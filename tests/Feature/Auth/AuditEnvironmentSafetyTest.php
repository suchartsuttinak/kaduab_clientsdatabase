<?php

use Illuminate\Support\Facades\DB;

it('uses only the isolated sqlite in-memory database for audit regression tests', function () {
    expect(app()->environment('testing'))->toBeTrue()
        ->and(config('database.default'))->toBe('sqlite')
        ->and((string) config('database.connections.sqlite.database'))->toBe(':memory:')
        ->and(DB::connection()->getDriverName())->toBe('sqlite')
        ->and((string) DB::connection()->getDatabaseName())->toBe(':memory:');
});

it('keeps the audit capture hook and the user-update duplicate guard installed', function () {
    $provider = file_get_contents(app_path('Providers/AppServiceProvider.php'));
    $middleware = file_get_contents(app_path('Http/Middleware/EnforceFormPermission.php'));

    expect($provider)->toBeString()
        ->and($provider)->toContain("'eloquent.created: *' => 'create'")
        ->and($provider)->toContain("'eloquent.updated: *' => 'update'")
        ->and($provider)->toContain("'eloquent.deleted: *' => 'delete'")
        ->and($provider)->toContain('AuditMutationContext::capture($auditAction, $model);')
        ->and($middleware)->toBeString()
        ->and($middleware)->toContain('AUDIT_USER_UPDATE_EMPTY_CONTEXT_V1')
        ->and($middleware)->toContain("\$request->route()?->getName() === 'users.update'");
});
