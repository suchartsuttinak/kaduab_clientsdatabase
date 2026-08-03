<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFormPermission
{
    public function handle(
        Request $request,
        Closure $next,
        string $permissionKey,
        string $action = 'view'
    ): Response {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        if (!$user->hasFormPermission($permissionKey, $action)) {
            abort(403, 'คุณไม่มีสิทธิ์ใช้งานส่วนนี้');
        }

        return $next($request);
    }
}
