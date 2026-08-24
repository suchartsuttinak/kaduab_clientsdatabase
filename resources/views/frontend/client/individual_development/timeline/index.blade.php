@extends('admin_client.admin_client')
@section('content')
@php
    $groups = [
        '' => 'ทั้งหมด',
        'development' => 'แผนพัฒนา',
        'education' => 'การศึกษา',
        'health' => 'สุขภาพ',
        'mental' => 'จิตใจ/การให้คำปรึกษา',
        'behavior' => 'พฤติกรรม',
        'family' => 'ครอบครัว',
        'social' => 'สังคมสงเคราะห์/ส่งต่อ',
    ];
    $icons = [
        'development'=>'bi-diagram-3','education'=>'bi-mortarboard','health'=>'bi-heart-pulse','mental'=>'bi-chat-heart',
        'behavior'=>'bi-person-check','family'=>'bi-house-heart','social'=>'bi-people'
    ];
    $thaiDate = static function ($value): string {
        if (!$value) return '-';
        try {
            $date = $value instanceof \Carbon\CarbonInterface ? $value : \Carbon\Carbon::parse($value, 'Asia/Bangkok');
            return $date->format('d/m/') . ($date->year + 543);
        } catch (\Throwable $e) { return '-'; }
    };
@endphp
<style>
.idptl{--b:#e2e8f0;--m:#64748b;padding-bottom:1rem}.idptl *{min-width:0}.idptl-head,.idptl-card{background:#fff;border:1px solid var(--b);border-radius:15px}.idptl-head{padding:1rem 1.1rem;margin-bottom:.8rem}.idptl-title{font-size:1.08rem;font-weight:800;margin:0}.idptl-sub{font-size:.78rem;color:var(--m);margin-top:.25rem}.idptl-filters{display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:1rem}.idptl-filter{display:inline-flex;padding:.35rem .65rem;border:1px solid #dbe3ec;border-radius:999px;color:#475569;text-decoration:none!important;font-size:.73rem;font-weight:700;background:#fff}.idptl-filter.active{background:#eef5ff;color:#245f9f;border-color:#aac7e7}.idptl-line{border-left:2px solid #dbe5ef;margin-left:.72rem;padding-left:1.3rem}.idptl-item{position:relative;background:#fff;border:1px solid var(--b);border-radius:13px;padding:.85rem 1rem;margin-bottom:.75rem}.idptl-item:before{content:"";position:absolute;left:-1.72rem;top:1.05rem;width:10px;height:10px;border-radius:50%;background:#3b82f6;border:2px solid #eaf4ff}.idptl-date{font-size:.72rem;color:var(--m)}.idptl-cat{font-size:.68rem;color:#3b6fa5;font-weight:800;margin-left:.45rem}.idptl-name{font-size:.84rem;font-weight:800;margin-top:.15rem;color:#24344a}.idptl-detail{font-size:.78rem;color:#4b5f75;margin-top:.25rem;line-height:1.55;white-space:pre-line}.idptl-empty{text-align:center;color:var(--m);padding:2.2rem}
</style>
<div class="container-fluid px-2 px-lg-3 idptl">
    <div class="idptl-head d-flex justify-content-between flex-wrap gap-2 align-items-start">
        <div><h4 class="idptl-title"><i class="bi bi-clock-history me-2 text-primary"></i>Timeline การช่วยเหลือและการพัฒนา</h4><div class="idptl-sub">{{ $client->fullname ?? $client->name ?? '-' }} • รวมข้อมูลจากโมดูลเดิมอัตโนมัติ ไม่ต้องกรอกซ้ำ</div></div>
        <a href="{{ route('individual-development.index',$client->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>กลับภาพรวม</a>
    </div>
    <div class="idptl-filters">@foreach($groups as $key=>$label)<a class="idptl-filter {{ ($selectedGroup ?? '')===$key?'active':'' }}" href="{{ request()->fullUrlWithQuery(['group'=>$key ?: null]) }}">{{ $label }}</a>@endforeach</div>
    <div class="idptl-line">
        @forelse($timeline as $item)
            <div class="idptl-item">
                <div><span class="idptl-date">{{ $thaiDate($item['date']) }}</span><span class="idptl-cat"><i class="bi {{ $icons[$item['group']] ?? 'bi-record-circle' }} me-1"></i>{{ $item['category'] ?? '-' }}</span></div>
                <div class="idptl-name">{{ $item['title'] }}</div>
                @if($item['detail'])<div class="idptl-detail">{{ $item['detail'] }}</div>@endif
            </div>
        @empty
            <div class="idptl-card idptl-empty"><i class="bi bi-clock-history fs-3 d-block mb-2"></i>ยังไม่มีเหตุการณ์ใน Timeline ตามตัวกรองนี้</div>
        @endforelse
    </div>
</div>
@endsection
