<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireExplicitFormPermissions
{
    /**
     * UNIFIED_ACCESS_SCOPE_V5
     * Middleware นี้เหลือหน้าที่บังคับให้ Login เท่านั้น
     * สิทธิ์จริงของ Route ถูกตัดสินโดย EnforceFormPermission ตาม permission matrix
     * เพื่อไม่ให้ flag legacy form_permissions_enabled กลายเป็นช่อง bypass/ช่องบล็อกที่ไม่สอดคล้องกัน
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->guest(route('login'));
        }

        return $next($request);
    }
}
