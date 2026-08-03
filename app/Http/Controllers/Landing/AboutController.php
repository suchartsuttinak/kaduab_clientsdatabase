<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\AboutData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        /*
         * ดึงข้อมูลล่าสุดของทั้ง 3 ประเภทด้วย Query เดียว
         * ลดการเรียกฐานข้อมูลซ้ำจากเดิม 3 ครั้ง
         */
        $latestIds = AboutData::query()
            ->selectRaw('MAX(id)')
            ->whereIn('type', ['history', 'objective', 'mission'])
            ->groupBy('type');

        $latestByType = AboutData::query()
            ->whereIn('id', $latestIds)
            ->get()
            ->keyBy('type');

        $history = $latestByType->get('history');
        $objective = $latestByType->get('objective');
        $mission = $latestByType->get('mission');

        /*
         * ใช้ Pagination แทนการโหลดข้อมูลทั้งหมด
         * เพื่อให้หน้าโหลดเร็วแม้มีประวัติจำนวนมาก
         */
        $aboutData = AboutData::query()
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('landing.about.index', compact(
            'history',
            'objective',
            'mission',
            'aboutData'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'content' => trim((string) $request->input('content')),
        ]);

        $validated = $request->validate([
            'type' => ['required', 'in:history,objective,mission'],
            'content' => ['required', 'string', 'max:10000'],
        ], [
            'type.required' => 'กรุณาเลือกประเภทข้อมูล',
            'type.in' => 'ประเภทข้อมูลไม่ถูกต้อง',
            'content.required' => 'กรุณากรอกรายละเอียด',
            'content.max' => 'รายละเอียดต้องไม่เกิน 10,000 ตัวอักษร',
        ]);

        AboutData::query()->create($validated);

        return redirect()
            ->route('landing.about.index')
            ->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }

    public function destroy($id): RedirectResponse
    {
        $data = AboutData::query()->findOrFail($id);
        $data->delete();

        return redirect()
            ->back()
            ->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }
}
