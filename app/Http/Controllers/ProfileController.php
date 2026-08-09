<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        /*
         * บัญชีเจ้าหน้าที่ต้องจัดการผ่านเมนูผู้ใช้งานส่วนกลาง
         * เพื่อรักษา Audit Trail และป้องกันข้อมูลที่อ้างอิง user_id ถูกลบตาม cascade
         */
        return Redirect::route('profile.edit')->withErrors([
            'account' => 'ไม่อนุญาตให้ลบบัญชีเจ้าหน้าที่ด้วยตนเอง กรุณาติดต่อผู้ดูแลระบบ',
        ]);
    }
}
