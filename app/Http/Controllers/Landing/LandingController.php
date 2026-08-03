<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\AboutData;
use App\Models\News;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        /* ลด Query ข้อมูลเกี่ยวกับเราจาก 3 ครั้งเหลือ 1 ครั้ง */
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

        /* เลือกเฉพาะคอลัมน์ที่หน้า Landing ใช้งานจริง */
        $news = News::query()
            ->select(['id', 'title', 'description', 'image', 'created_at'])
            ->latest('id')
            ->limit(6)
            ->get();

        return view('landing.index', compact(
            'history',
            'objective',
            'mission',
            'news'
        ));
    }
}
