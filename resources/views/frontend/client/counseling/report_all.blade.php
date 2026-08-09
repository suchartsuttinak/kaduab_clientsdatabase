@extends('admin_client.admin_client')
@section('title','รายงานประวัติการให้คำปรึกษาทั้งหมด')
@section('content')
@php
$name = trim((string)($client->fullname ?? '')) ?: trim(($client->first_name ?? '').' '.($client->last_name ?? '')) ?: '-';
$thaiDate = function($date){ if(empty($date)) return '-'; try{$d=\Carbon\Carbon::parse($date);$m=[1=>'มกราคม',2=>'กุมภาพันธ์',3=>'มีนาคม',4=>'เมษายน',5=>'พฤษภาคม',6=>'มิถุนายน',7=>'กรกฎาคม',8=>'สิงหาคม',9=>'กันยายน',10=>'ตุลาคม',11=>'พฤศจิกายน',12=>'ธันวาคม'];return $d->day.' '.$m[$d->month].' '.($d->year+543);}catch(\Throwable $e){return '-';}};
$closedCount = $counselings->filter(fn($x)=>$x->is_closed)->count();
@endphp
<style>
.csa{max-width:1040px;margin:0 auto;padding:1rem 0 2rem;color:#1f2937}.csa-toolbar{display:flex;justify-content:flex-end;gap:.5rem;margin-bottom:.8rem}.csa-sheet{background:#fff;border:1px solid #dfe5ec;border-radius:14px;padding:1.3rem 1.5rem;box-shadow:0 8px 24px rgba(15,23,42,.05)}.csa-head{text-align:center;padding-bottom:.65rem;margin-bottom:.8rem;border-bottom:2px solid #334155}.csa-head h1{margin:0;font-size:1.3rem;font-weight:700}.csa-head p{margin:.18rem 0 0;color:#64748b;font-size:.82rem}.csa-meta{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.45rem;margin-bottom:.8rem}.csa-meta>div{padding:.5rem .6rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;font-size:.8rem}.csa-table{width:100%;border-collapse:collapse;font-size:.76rem}.csa-table th,.csa-table td{border:1px solid #d9e0e8;padding:.38rem .42rem;vertical-align:top}.csa-table th{background:#f8fafc;font-weight:700}.csa-episode{margin-top:1rem;padding-top:.7rem;border-top:2px solid #cbd5e1}.csa-episode h2{margin:0;font-size:.95rem;font-weight:700}.csa-episode-meta{margin:.18rem 0 .45rem;color:#64748b;font-size:.76rem}.csa-text{white-space:pre-line;font-size:.79rem;line-height:1.48}.csa-close{margin-top:.45rem;padding:.5rem .6rem;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px;font-size:.78rem;line-height:1.45}
@media print{@page{size:A4 portrait;margin:9mm 10mm 10mm}html,body{margin:0!important;padding:0!important;background:#fff!important;font-family:"Sarabun","TH Sarabun New",Tahoma,Arial,sans-serif!important;font-size:11pt!important;-webkit-print-color-adjust:exact;print-color-adjust:exact}.app-topbar,.app-sidebar-menu,.sidebar-overlay,.app-footer,footer,.csa-toolbar{display:none!important}.app-body,.content-page,.main-content,.content-shell,.content-scroll-x{display:block!important;width:100%!important;max-width:100%!important;min-width:0!important;height:auto!important;min-height:0!important;margin:0!important;padding:0!important;overflow:visible!important;transform:none!important}.csa{width:100%!important;max-width:none!important;margin:0!important;padding:0!important}.csa-sheet{border:0!important;border-radius:0!important;box-shadow:none!important;padding:0!important}.csa-head h1{font-size:16pt!important}.csa-meta{grid-template-columns:repeat(4,1fr)!important}.csa-meta>div{font-size:9.5pt!important;background:#fff!important}.csa-table{font-size:8.8pt!important}.csa-table th,.csa-table td{padding:1.4mm 1.5mm!important}.csa-table thead{display:table-header-group}.csa-table tr{break-inside:avoid}.csa-episode{break-before:auto}.csa-episode h2{break-after:avoid}.csa-text,.csa-close{font-size:9.3pt!important}.csa-close{background:#fff!important;break-inside:avoid}}
</style>
<div class="csa"><div class="csa-toolbar"><a href="{{ route('counseling.index',$client->id) }}" class="btn btn-light">กลับหน้าหลัก</a><button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>พิมพ์รายงานรวม</button></div>
<article class="csa-sheet">
<header class="csa-head"><h1>รายงานประวัติการให้คำปรึกษาทั้งหมด</h1><p>แสดงแต่ละกระบวนการเป็น “ครั้งที่” ตั้งแต่เริ่ม ดำเนินการต่อเนื่อง และจบ</p></header>
<div class="csa-meta"><div><b>ผู้รับบริการ</b><br>{{ $name }}</div><div><b>เลขทะเบียน</b><br>{{ $client->register_number ?? '-' }}</div><div><b>ทั้งหมด</b><br>{{ $counselings->count() }} ครั้ง</div><div><b>จบแล้ว</b><br>{{ $closedCount }} ครั้ง</div></div>

<table class="csa-table"><thead><tr><th>ครั้งที่</th><th>เริ่ม</th><th>จบ</th><th>ประเด็น</th><th>จำนวนบริการ</th><th>ผล/สถานะ</th></tr></thead><tbody>
@forelse($counselings as $c)
<tr><td class="text-center"><b>{{ $c->session_no }}</b></td><td>{{ $thaiDate($c->session_date) }}</td><td>{{ $c->is_closed ? $thaiDate($c->closed_date ?: $c->last_activity_date) : 'ยังไม่จบ' }}</td><td>{{ $c->presenting_problem ?: '-' }}</td><td class="text-center">{{ $c->service_count }}</td><td>{{ $c->is_closed ? $c->closure_type_label : $c->status_label }}</td></tr>
@empty<tr><td colspan="6" class="text-center">ไม่มีข้อมูล</td></tr>@endforelse
</tbody></table>

@foreach($counselings as $c)
<section class="csa-episode">
    <h2>การให้คำปรึกษา ครั้งที่ {{ $c->session_no }} — {{ $c->is_closed ? 'จบแล้ว' : 'กำลังดำเนินการ' }}</h2>
    <div class="csa-episode-meta">{{ $thaiDate($c->session_date) }} ถึง {{ $c->is_closed ? $thaiDate($c->closed_date ?: $c->last_activity_date) : 'ปัจจุบัน' }} • ให้บริการ {{ $c->service_count }} ครั้ง • {{ $c->issue_relation_label }}</div>
    <div class="csa-text"><b>ประเด็น:</b> {{ $c->presenting_problem ?: '-' }}
<b>เป้าหมาย:</b> {{ $c->goals ?: '-' }}</div>
    <table class="csa-table" style="margin-top:.4rem"><thead><tr><th style="width:15%">วัน/ลำดับ</th><th style="width:18%">ช่องทาง</th><th style="width:39%">การดำเนินการ</th><th style="width:28%">ผล/แนวทางต่อ</th></tr></thead><tbody>
        <tr><td><b>เริ่มต้น</b><br>{{ $thaiDate($c->session_date) }}</td><td>{{ $c->channel_label }}</td><td>{{ $c->interventions ?: ($c->advice ?: '-') }}</td><td>{{ $c->outcome ?: ($c->next_steps ?: '-') }}</td></tr>
        @foreach($c->followups as $f)
        <tr><td><b>ต่อเนื่อง {{ $f->followup_no }}</b><br>{{ $thaiDate($f->followup_date) }}</td><td>{{ $f->followup_method_label }}</td><td>{{ $f->progress ?: '-' }}@if(filled($f->additional_support))<br>{{ $f->additional_support }}@endif</td><td>{{ $f->result ?: '-' }}@if(filled($f->next_action))<br>{{ $f->next_action }}@endif</td></tr>
        @endforeach
    </tbody></table>
    @if($c->is_closed)
    <div class="csa-close"><b>สรุปการจบ:</b> {{ $c->closure_summary ?: '-' }}<br><b>ลักษณะ:</b> {{ $c->closure_type_label }} • <b>เป้าหมาย:</b> {{ $c->goal_achievement_label }}@if(filled($c->final_recommendation))<br><b>ข้อเสนอแนะ:</b> {{ $c->final_recommendation }}@endif</div>
    @endif
</section>
@endforeach
</article></div>
@endsection
