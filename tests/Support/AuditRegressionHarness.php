<?php

namespace Tests\Support;

use App\Http\Middleware\EnforceFormPermission;
use App\Services\AuditMutationContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class AuditRegressionHarness
{
    /**
     * สร้าง Request จำลองเฉพาะในหน่วยความจำสำหรับทดสอบ Audit
     * โดยไม่เรียก URL จริงและไม่แตะฐานข้อมูล Production
     */
    public static function namedRequest(
        string $method,
        string $routeName,
        mixed $user = null
    ): Request {
        $method = strtoupper($method);

        $request = Request::create('/_audit-regression-test', $method);

        $route = new Route(
            [$method],
            '/_audit-regression-test',
            static fn () => response()->noContent()
        );
        $route->name($routeName);

        $request->setRouteResolver(static fn () => $route);

        if ($user !== null) {
            $request->setUserResolver(static fn () => $user);
        }

        // AuditLogger / AuditMutationContext ใช้ request() จาก container
        app()->instance('request', $request);

        return $request;
    }

    /**
     * ใส่ Mutation Context จำลองใน Request โดยอ่านชื่อ attribute
     * จาก class จริงผ่าน Reflection เพื่อไม่ hard-code ซ้ำหลายจุด
     */
    public static function putMutationContext(Request $request, array $entries): void
    {
        $reflection = new ReflectionClass(AuditMutationContext::class);
        $attribute = $reflection->getConstant('REQUEST_ATTRIBUTE');

        if (!is_string($attribute) || $attribute === '') {
            throw new RuntimeException('Unable to resolve AuditMutationContext request attribute.');
        }

        $request->attributes->set($attribute, $entries);
    }

    /**
     * เรียก auditMutation โดยตรงเพื่อทดสอบกฎ Audit กลาง
     * โดยไม่ต้องยิง Controller จริงในทุกกรณี
     */
    public static function invokeAuditMutation(
        Request $request,
        Response $response,
        mixed $user,
        array $permissionKeys,
        string $action
    ): void {
        $method = new ReflectionMethod(
            EnforceFormPermission::class,
            'auditMutation'
        );

        $method->invoke(
            new EnforceFormPermission(),
            $request,
            $response,
            $user,
            $permissionKeys,
            $action,
            false,
            null
        );
    }

    /**
     * ตรวจ logic หา client_id ของ Mutation Context โดยตรง
     */
    public static function resolveClientId(Model $model): ?int
    {
        $method = new ReflectionMethod(
            AuditMutationContext::class,
            'resolveClientId'
        );

        $resolved = $method->invoke(null, $model, 0);

        return is_numeric($resolved)
            ? (int) $resolved
            : null;
    }
}
