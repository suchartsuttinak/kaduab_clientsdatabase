<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckHouseAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        // UNIFIED_ACCESS_SCOPE_V5: ใช้กฎบ้านเดียวกับ Client::forUser()
        $houseId = $request->route('house_id');
        if ($houseId !== null && !$user->canAccessHouse($houseId)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงบ้านนี้');
        }

        return $next($request);
    }
}
