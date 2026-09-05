<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\FormPermissionMenu;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // USER_PERMISSION_VISIBILITY_FIX_V2:
        // หลังล็อกอินเลือกหน้าแรกจากสิทธิ์จริง ไม่บังคับครู/ผู้ดูแลไปหน้า client.show
        $user = Auth::user();
        $routeName = FormPermissionMenu::firstAccessibleRouteName($user);

        // PERMISSION_LANDING_LOGOUT_FIX_V3:
        // ไม่ใช้ redirect()->intended() เพราะ URL ค้างใน session (เช่น /profile)
        // สามารถพาผู้ใช้ไปหน้าที่ไม่ได้ตั้งใจหลัง Login ได้
        $request->session()->forget('url.intended');

        return redirect()->route($routeName);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}