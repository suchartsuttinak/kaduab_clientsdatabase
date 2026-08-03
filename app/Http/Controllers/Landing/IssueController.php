<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class IssueController extends Controller
{
    /** แสดงรายการแจ้งปัญหา */
    public function index(): View
    {
        $user = auth()->user();

        abort_unless(
            $user && in_array(($user->role ?? null), ['admin', 'executive'], true),
            403,
            'คุณไม่มีสิทธิ์เข้าถึงหน้านี้'
        );

        /*
         * อัปเดตเฉพาะรายการที่ยังไม่ได้อ่าน
         * ใช้เฉพาะคอลัมน์ที่มีอยู่ในฐานข้อมูลเดิม
         */
        Issue::query()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now('Asia/Bangkok'),
            ]);

        $issues = Issue::query()
            ->select([
                'id',
                'fullname',
                'phone',
                'subject',
                'is_read',
                'read_at',
                'created_at',
            ])
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('landing.issues.index', compact('issues'));
    }

    /** บันทึกการแจ้งปัญหา */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'fullname' => Str::squish((string) $request->input('fullname')),
            'phone' => trim((string) $request->input('phone')),
            'subject' => trim((string) $request->input('subject')),
        ]);

        $validated = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'subject' => ['required', 'string', 'max:5000'],
        ], [
            'fullname.required' => 'กรุณากรอกชื่อ-สกุลผู้แจ้ง',
            'fullname.string' => 'ชื่อ-สกุลต้องเป็นข้อความ',
            'fullname.max' => 'ชื่อ-สกุลต้องไม่เกิน 255 ตัวอักษร',
            'phone.required' => 'กรุณากรอกเบอร์โทรศัพท์',
            'phone.string' => 'เบอร์โทรศัพท์ต้องเป็นข้อความ',
            'phone.max' => 'เบอร์โทรศัพท์ต้องไม่เกิน 20 ตัวอักษร',
            'subject.required' => 'กรุณากรอกเรื่องที่แจ้ง',
            'subject.string' => 'เรื่องที่แจ้งต้องเป็นข้อความ',
            'subject.max' => 'รายละเอียดที่แจ้งต้องไม่เกิน 5,000 ตัวอักษร',
        ]);

        Issue::query()->create([
            'fullname' => $validated['fullname'],
            'phone' => $validated['phone'],
            'subject' => $validated['subject'],
            'is_read' => false,
        ]);

        return redirect()
            ->back()
            ->with('success', 'แจ้งเรื่องช่วยเหลือสำเร็จแล้ว');
    }
}