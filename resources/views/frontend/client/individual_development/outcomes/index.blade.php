@extends('admin_client.admin_client')
@section('content')
@php
$thaiDate=function($v){if(!$v)return '-';$d=$v instanceof \Carbon\CarbonInterface?$v:\Carbon\Carbon::parse($v);return $d->format('d/m/').($d->year+543);};
@endphp
<style>
.idpo{max-width:1500px;margin:auto}.idpo-card{background:#fff;border:1px solid #dfe7f1;border-radius:16px;padding:18px}.idpo-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start;flex-wrap:wrap}.idpo-table{overflow:auto}.idpo table{width:100%;border-collapse:collapse}.idpo th,.idpo td{padding:11px;border-bottom:1px solid #edf1f5;vertical-align:middle}.idpo th{font-size:.82rem;color:#53657a}.idpo .trend-up{color:#15803d}.idpo .trend-down{color:#b91c1c}.idpo .trend-same{color:#64748b}@media(max-width:768px){.idpo-card{padding:12px}}
</style>
<div class="container-fluid py-3 idpo">
 <div class="idpo-card mb-3">
  <div class="idpo-head"><div><h4 class="fw-bold mb-1"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>ประเมินผลลัพธ์รายบุคคล</h4><div class="text-muted small">เปรียบเทียบตั้งแต่แรกเข้า ระหว่างดำเนินการ ก่อนจำหน่าย และหลังจำหน่าย • แผนครั้งที่ {{ $plan->plan_no }}</div></div>
  <div class="d-flex gap-2 flex-wrap"><a class="btn btn-outline-secondary" href="{{ route('individual-development.index',['client'=>$client->id,'plan'=>$plan->id]) }}"><i class="bi bi-arrow-left me-1"></i>กลับ</a>@if($canCreate)@if($plan->status==='active')<a class="btn btn-primary" href="{{ route('individual-development.outcomes.create',['client'=>$client->id,'plan'=>$plan->id,'type'=>'review']) }}"><i class="bi bi-plus-lg me-1"></i>ประเมินระหว่างดำเนินการ</a>@else<a class="btn btn-primary" href="{{ route('individual-development.outcomes.create',['client'=>$client->id,'plan'=>$plan->id,'type'=>'post_discharge']) }}"><i class="bi bi-plus-lg me-1"></i>ประเมินหลังจำหน่าย</a>@endif @endif</div></div>
 </div>
 <div class="idpo-card">
  <div class="idpo-table"><table><thead><tr><th>รอบประเมิน</th><th>วันที่</th><th>ผู้ประเมิน</th><th>คะแนนเฉลี่ย</th><th>เทียบ Baseline</th><th class="text-end">จัดการ</th></tr></thead><tbody>
  @forelse($rows as $row) @php($a=$row['assessment'])
  <tr><td><strong>{{ $typeLabels[$a->assessment_type] ?? $a->assessment_type }}</strong><div class="small text-muted">ครั้งที่ {{ $a->round_no }}</div></td><td>{{ $thaiDate($a->assessment_date) }}</td><td>{{ optional($a->assessor)->name ?? '-' }}</td><td>{{ $row['average'] !== null ? number_format($row['average'],2) : '-' }} / 5</td><td>@if($row['delta']===null)-@elseif($row['delta']>0)<span class="trend-up">↑ +{{ number_format($row['delta'],2) }} ดีขึ้น</span>@elseif($row['delta']<0)<span class="trend-down">↓ {{ number_format($row['delta'],2) }} ต้องติดตาม</span>@else<span class="trend-same">→ คงเดิม</span>@endif</td><td class="text-end"><div class="d-inline-flex gap-1"><a class="btn btn-sm btn-outline-primary" href="{{ route('individual-development.outcomes.show',[$client->id,$a->id]) }}">ดูผล</a>@if($canUpdate && $a->assessment_type !== 'baseline' && ($plan->status === 'active' || $a->assessment_type === 'post_discharge'))<a class="btn btn-sm btn-outline-secondary" href="{{ route('individual-development.outcomes.edit',[$client->id,$a->id]) }}">แก้ไข</a>@endif</div></td></tr>
  @empty<tr><td colspan="6" class="text-center text-muted py-5">ยังไม่มีการประเมินผลลัพธ์</td></tr>@endforelse
  </tbody></table></div>
  @if($canCreate)<div class="border-top pt-3 mt-2 d-flex gap-2 flex-wrap"><span class="small text-muted align-self-center me-2">เพิ่มตามช่วง:</span><a class="btn btn-sm btn-outline-primary" href="{{ route('individual-development.outcomes.create',['client'=>$client->id,'plan'=>$plan->id,'type'=>'review']) }}">ระหว่างดำเนินการ</a>@if($plan->status==='active')<a class="btn btn-sm btn-outline-primary" href="{{ route('individual-development.outcomes.create',['client'=>$client->id,'plan'=>$plan->id,'type'=>'final']) }}">ก่อนจำหน่าย</a>@else<a class="btn btn-sm btn-outline-primary" href="{{ route('individual-development.outcomes.create',['client'=>$client->id,'plan'=>$plan->id,'type'=>'post_discharge']) }}">หลังจำหน่าย</a>@endif</div>@endif
 </div>
</div>
@if(session('success') || session('warning'))
<script>document.addEventListener('DOMContentLoaded',function(){if(window.Swal){Swal.fire({icon:@json(session('success')?'success':'warning'),title:@json(session('success')?'สำเร็จ':'แจ้งเตือน'),text:@json(session('success')??session('warning')),confirmButtonText:'OK',timer:3000,timerProgressBar:true});}});</script>
@endif
@endsection
