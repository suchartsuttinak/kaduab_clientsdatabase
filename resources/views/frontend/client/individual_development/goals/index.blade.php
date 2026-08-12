@extends('admin_client.admin_client')

@section('content')
@php
    $clientName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
    $thaiDate = static function ($value): string {
        if (!$value) return '-';
        try { $d = $value instanceof \Carbon\CarbonInterface ? $value : \Carbon\Carbon::parse($value); return $d->format('d/m/') . ($d->year + 543); } catch (\Throwable $e) { return '-'; }
    };
    $priorityClass = ['low'=>'low','medium'=>'medium','high'=>'high','urgent'=>'urgent'];
    $statusClass = ['not_started'=>'neutral','in_progress'=>'primary','partial'=>'warning','achieved'=>'success','cancelled'=>'muted'];
    $activityStatus = ['planned'=>'วางแผน','in_progress'=>'กำลังดำเนินการ','completed'=>'ดำเนินการแล้ว','cancelled'=>'ยกเลิก'];
@endphp
<style>
.idp-goals{--border:#e2e8f0;--text:#203249;--muted:#718096;padding-bottom:1.5rem}.idp-goals .g-head,.idp-goals .g-card{background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:0 6px 20px rgba(32,50,73,.045)}.idp-goals .g-head{padding:1rem 1.15rem;margin-bottom:1rem;display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap}.idp-goals .g-title{margin:0;font-size:1.12rem;font-weight:800;color:var(--text)}.idp-goals .g-sub{margin:.3rem 0 0;color:var(--muted);font-size:.85rem}.idp-goals .g-actions{display:flex;gap:.5rem;flex-wrap:wrap}.idp-goals .btn{border-radius:10px;font-weight:700;min-height:40px}.idp-goals .g-card-actions{display:flex;gap:.55rem;align-items:center;flex-wrap:wrap;justify-content:flex-end}.idp-goals .row-action-group{display:flex;gap:.45rem;align-items:center}.idp-goals .row-action-btn{display:inline-flex;align-items:center;justify-content:center;gap:.34rem;min-height:38px;padding:.42rem .68rem;border-radius:10px;font-size:.76rem;font-weight:800;text-decoration:none;transition:.15s;white-space:nowrap}.idp-goals .row-action-btn.edit{border:1px solid #9fc2e7;background:#f7fbff;color:#2b6ead}.idp-goals .row-action-btn.edit:hover{background:#eaf4ff;color:#205b94}.idp-goals .row-action-btn.delete{border:1px solid #f0b0b7;background:#fff8f8;color:#c8424d}.idp-goals .row-action-btn.delete:hover{background:#fff0f1;color:#ad303c}.idp-goals .row-action-btn i{font-size:.92rem}.idp-goals .btn-primary2{background:linear-gradient(135deg,#3577bd,#245f9f);border:0;color:#fff}.idp-goals .btn-primary2:hover{color:#fff}
.idp-goals .g-card{overflow:hidden;margin-bottom:1rem}.idp-goals .g-card-head{padding:.9rem 1rem;background:#fbfcfe;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;gap:.75rem;align-items:flex-start;flex-wrap:wrap}.idp-goals .g-domain{font-size:.76rem;color:#2d6ba8;font-weight:800}.idp-goals .g-name{font-size:1rem;font-weight:800;color:var(--text);margin-top:.15rem}.idp-goals .g-card-body{padding:1rem}.idp-goals .g-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.7rem}.idp-goals .g-info{border:1px solid #e7edf3;border-radius:12px;background:#fafcff;padding:.72rem}.idp-goals .g-label{font-size:.72rem;color:var(--muted)}.idp-goals .g-value{font-size:.84rem;color:#34485e;font-weight:700;margin-top:.15rem;white-space:pre-line}.idp-goals .g-wide{grid-column:1/-1}.idp-goals .badge2{display:inline-flex;align-items:center;padding:.28rem .58rem;border-radius:999px;font-size:.72rem;font-weight:800}.idp-goals .badge2.primary{background:#eaf4ff;color:#2367a8}.idp-goals .badge2.warning{background:#fff7df;color:#8b6205}.idp-goals .badge2.success{background:#eafaf1;color:#17764c}.idp-goals .badge2.neutral{background:#f2f4f7;color:#5d6876}.idp-goals .badge2.muted{background:#f0f0f0;color:#7a7a7a}.idp-goals .activity-box{margin-top:1rem;border-top:1px solid #e7edf3;padding-top:1rem}.idp-goals .activity-head{display:flex;justify-content:space-between;gap:.5rem;align-items:center;flex-wrap:wrap;margin-bottom:.6rem}.idp-goals .activity-title{font-size:.83rem;font-weight:800;color:#40556c}.idp-goals .activity-item{padding:.7rem .8rem;border:1px solid #e8edf3;border-radius:11px;margin-bottom:.5rem;background:#fff;display:grid;grid-template-columns:105px minmax(0,1fr) 140px auto;gap:.65rem;align-items:center}.idp-goals .act-date{font-size:.77rem;color:#5e7186}.idp-goals .act-detail{font-size:.82rem;color:#32475c}.idp-goals .act-status{font-size:.74rem;color:#5d6f82}.idp-goals .act-actions{display:flex;gap:.42rem;justify-content:flex-end}.idp-goals .act-actions .row-action-btn{min-height:36px;padding:.38rem .58rem}.idp-goals .empty{padding:2rem;text-align:center;background:#fff;border:1px dashed #cfd9e4;border-radius:16px;color:#66788e}.idp-goals .readonly{display:inline-flex;padding:.3rem .62rem;border-radius:999px;background:#f2f4f7;color:#697586;font-size:.75rem;font-weight:700}
@media(max-width:1199.98px){.idp-goals .activity-item{grid-template-columns:100px minmax(0,1fr) 130px}.idp-goals .activity-item .act-actions{grid-column:1/-1;justify-content:flex-end}}@media(max-width:991.98px){.idp-goals .g-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.idp-goals .g-card-actions{width:100%;justify-content:space-between}.idp-goals .row-action-btn{min-width:86px}.idp-goals .activity-item{grid-template-columns:100px minmax(0,1fr)}}@media(max-width:575.98px){.idp-goals .g-grid{grid-template-columns:1fr}.idp-goals .g-actions{display:grid;width:100%;grid-template-columns:1fr}.idp-goals .g-actions .btn{width:100%}.idp-goals .g-card-actions{display:grid;grid-template-columns:1fr;width:100%}.idp-goals .row-action-group{display:grid;grid-template-columns:1fr 1fr;width:100%}.idp-goals .row-action-btn{width:100%;min-width:0}.idp-goals .activity-item{grid-template-columns:1fr}.idp-goals .activity-item .act-actions{grid-column:auto;display:grid;grid-template-columns:1fr 1fr}.idp-goals .act-actions .row-action-btn{width:100%}}

/* IDP_PHASE6_SAFE_SCOPE */
.idp-goals{width:100%;min-width:0}
.idp-goals *{min-width:0}
.idp-goals .g-name,.idp-goals .g-value,.idp-goals .act-detail{overflow-wrap:anywhere;word-break:break-word}
.idp-goals .g-card-actions form,.idp-goals .act-actions form{margin:0}
</style>
<div class="container-fluid px-2 px-lg-3 idp-goals">
    <div class="g-head">
        <div>
            <h4 class="g-title"><i class="bi bi-bullseye me-2 text-primary"></i>เป้าหมายและแผนกิจกรรมรายบุคคล</h4>
            <p class="g-sub">ผู้รับบริการ: <strong>{{ $clientName }}</strong> • อายุ {{ $ageText }} • แผนครั้งที่ {{ $plan->plan_no }}</p>
        </div>
        <div class="g-actions">
            @if($readOnly)<span class="readonly"><i class="bi bi-eye me-1"></i>อ่านอย่างเดียว</span>@endif
            <a href="{{ route('individual-development.index', $client->id) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>กลับหน้าหลัก</a>
            @if($canCreate && !$readOnly)<a href="{{ route('individual-development.followups.create', $client->id) }}" class="btn btn-outline-primary"><i class="bi bi-activity me-1"></i>บันทึกติดตาม</a>@endif
            @if($canCreate && !$readOnly)<a href="{{ route('individual-development.goals.create', $client->id) }}" class="btn btn-primary2"><i class="bi bi-plus-circle me-1"></i>เพิ่มเป้าหมาย</a>@endif
        </div>
    </div>

    @forelse($goals as $goal)
        <div class="g-card">
            <div class="g-card-head">
                <div>
                    <div class="g-domain">{{ $goal->domain?->name ?? '-' }} @if($goal->indicator) • {{ $goal->indicator->name }} @endif</div>
                    <div class="g-name">{{ $loop->iteration }}. {{ $goal->title }}</div>
                </div>
                <div class="g-card-actions">
                    <span class="badge2 {{ $statusClass[$goal->status] ?? 'neutral' }}">{{ $statusLabels[$goal->status] ?? $goal->status }}</span>
                    <div class="row-action-group">
                        @if($canUpdate && !$readOnly)<a href="{{ route('individual-development.goals.edit', [$client->id,$goal->id]) }}" class="row-action-btn edit" title="แก้ไขเป้าหมาย"><i class="bi bi-pencil-square"></i><span>แก้ไข</span></a>@endif
                        @if($canDelete && !$readOnly)
                            <form method="POST" action="{{ route('individual-development.goals.destroy', [$client->id,$goal->id]) }}" class="goal-delete-form m-0">@csrf @method('DELETE')<button type="submit" class="row-action-btn delete" title="ลบเป้าหมาย"><i class="bi bi-trash3"></i><span>ลบ</span></button></form>
                        @endif
                    </div>
                </div>
            </div>
            <div class="g-card-body">
                <div class="g-grid">
                    <div class="g-info"><div class="g-label">ระดับ Baseline → เป้าหมาย</div><div class="g-value">{{ $goal->baseline_level ?? '-' }} → {{ $goal->target_level ?? '-' }}</div></div>
                    <div class="g-info"><div class="g-label">ความสำคัญ</div><div class="g-value">{{ $priorityLabels[$goal->priority] ?? $goal->priority }}</div></div>
                    <div class="g-info"><div class="g-label">กำหนดสำเร็จ</div><div class="g-value">{{ $thaiDate($goal->target_date) }}</div></div>
                    <div class="g-info"><div class="g-label">ผู้รับผิดชอบ</div><div class="g-value">{{ $goal->responsible_name ?: '-' }}</div></div>
                    <div class="g-info g-wide"><div class="g-label">ตัวชี้วัดความสำเร็จ</div><div class="g-value">{{ $goal->success_indicator ?: '-' }}</div></div>
                    @if($goal->measurement_method || $goal->target_value !== null)
                        <div class="g-info g-wide"><div class="g-label">การวัดผล</div><div class="g-value">{{ $goal->measurement_method ?: '-' }}@if($goal->target_value !== null) • ค่าเป้าหมาย {{ rtrim(rtrim(number_format((float)$goal->target_value,2,'.',','),'0'),'.') }} {{ $goal->target_unit }}@endif</div></div>
                    @endif
                </div>

                <div class="activity-box">
                    <div class="activity-head">
                        <div class="activity-title"><i class="bi bi-list-check me-1"></i>กิจกรรมตามแผน ({{ $goal->activities->count() }})</div>
                        @if($canCreate && !$readOnly)<a href="{{ route('individual-development.activities.create', [$client->id,$goal->id]) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus-circle me-1"></i>เพิ่มกิจกรรม</a>@endif
                    </div>
                    @forelse($goal->activities as $activity)
                        <div class="activity-item">
                            <div class="act-date">{{ $thaiDate($activity->activity_date) }}</div>
                            <div class="act-detail"><strong>{{ $activity->activity_type ?: 'กิจกรรม' }}</strong><br>{{ $activity->detail }}</div>
                            <div class="act-status">{{ $activityStatus[$activity->status] ?? $activity->status }}@if($activity->responsible_name)<br>{{ $activity->responsible_name }}@endif</div>
                            <div class="act-actions">
                                @if($canUpdate && !$readOnly)<a href="{{ route('individual-development.activities.edit', [$client->id,$activity->id]) }}" class="row-action-btn edit" title="แก้ไขกิจกรรม"><i class="bi bi-pencil-square"></i><span>แก้ไข</span></a>@endif
                                @if($canDelete && !$readOnly)<form method="POST" action="{{ route('individual-development.activities.destroy', [$client->id,$activity->id]) }}" class="activity-delete-form m-0">@csrf @method('DELETE')<button class="row-action-btn delete" type="submit" title="ลบกิจกรรม"><i class="bi bi-trash3"></i><span>ลบ</span></button></form>@endif
                            </div>
                        </div>
                    @empty
                        <div class="small text-muted py-2">ยังไม่มีกิจกรรมสำหรับเป้าหมายนี้</div>
                    @endforelse
                </div>
            </div>
        </div>
    @empty
        <div class="empty">
            <div class="fs-2 text-primary mb-2"><i class="bi bi-bullseye"></i></div>
            <div class="fw-bold text-dark">ยังไม่มีเป้าหมายการพัฒนา</div>
            <div class="small mt-1">นำผล Baseline มาใช้กำหนดเป้าหมายที่ชัดเจน วัดผลได้ และมีผู้รับผิดชอบ</div>
            @if($canCreate && !$readOnly)<a href="{{ route('individual-development.goals.create', $client->id) }}" class="btn btn-primary2 mt-3"><i class="bi bi-plus-circle me-1"></i>สร้างเป้าหมายแรก</a>@endif
        </div>
    @endforelse
</div>

@if(session('success') || session('warning'))
<script>document.addEventListener('DOMContentLoaded',function(){if(!window.Swal)return;Swal.fire({icon:@json(session('success')?'success':'warning'),title:@json(session('success')?'สำเร็จ':'แจ้งเตือน'),text:@json(session('success')??session('warning')),confirmButtonText:'OK',timer:3000,timerProgressBar:true});});</script>
@endif
<script>
document.addEventListener('DOMContentLoaded',function(){document.querySelectorAll('.goal-delete-form,.activity-delete-form').forEach(function(form){form.addEventListener('submit',function(e){if(!window.Swal){if(!confirm('ยืนยันการลบข้อมูลนี้?'))e.preventDefault();return;}e.preventDefault();Swal.fire({icon:'warning',title:'ยืนยันการลบ?',text:'ข้อมูลที่ลบจะไม่แสดงในรายการใช้งาน',showCancelButton:true,confirmButtonText:'ลบข้อมูล',cancelButtonText:'ยกเลิก'}).then(function(r){if(r.isConfirmed)form.submit();});});});});
</script>
@endsection
