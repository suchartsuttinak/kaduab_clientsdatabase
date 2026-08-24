@extends('admin_client.admin_client')

@section('content')
@php
    $clientName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
    $thaiDate = static function ($value): string {
        if (!$value) return '-';
        try { $d=$value instanceof \Carbon\CarbonInterface?$value:\Carbon\Carbon::parse($value,'Asia/Bangkok'); return $d->format('d/m/').($d->year+543); } catch(\Throwable $e){ return '-'; }
    };
    $statusClass=['not_started'=>'neutral','in_progress'=>'primary','partial'=>'warning','achieved'=>'success','cancelled'=>'muted'];
    $activityStatus=['planned'=>'วางแผน','in_progress'=>'กำลังดำเนินการ','completed'=>'เสร็จสิ้น','cancelled'=>'ยกเลิก'];
@endphp
<style>
.idpg{--b:#e2e8f0;--text:#1f2f46;--muted:#66768a;padding-bottom:1rem}.idpg *{min-width:0}.idpg .head,.idpg .card{background:#fff;border:1px solid var(--b);border-radius:16px;box-shadow:0 5px 18px rgba(31,47,70,.045)}.idpg .head{padding:1rem 1.1rem;margin-bottom:1rem;display:flex;justify-content:space-between;align-items:flex-start;gap:1rem}.idpg h4{margin:0;font-size:1.12rem;font-weight:800;color:var(--text)}.idpg .sub{font-size:.8rem;color:var(--muted);margin-top:.25rem}.idpg .actions,.idpg .card-actions,.idpg .act-actions{display:flex;gap:.4rem;flex-wrap:wrap}.idpg .btnx{display:inline-flex;align-items:center;justify-content:center;gap:.3rem;min-height:36px;padding:.4rem .68rem;border-radius:9px;font-size:.75rem;font-weight:700;text-decoration:none!important;white-space:nowrap}.idpg .primary{background:#2868a9;color:#fff!important;border:1px solid #2868a9}.idpg .light{background:#fff;color:#486077!important;border:1px solid #d4dee8}.idpg .danger{background:#fff7f7;color:#b91c1c!important;border:1px solid #fecaca}.idpg .warning{background:#fffaf0;color:#935b0a!important;border:1px solid #f5d7a1}.idpg .success{background:#f0fbf5;color:#167447!important;border:1px solid #a9dfc3}.idpg .card{margin-bottom:1rem;overflow:hidden}.idpg .card-head{padding:.82rem 1rem;border-bottom:1px solid var(--b);background:#fbfcfe;display:flex;justify-content:space-between;gap:.8rem;align-items:flex-start}.idpg .domain{font-size:.73rem;font-weight:800;color:#27629e}.idpg .name{margin-top:.15rem;font-size:.9rem;font-weight:800;color:var(--text);overflow-wrap:anywhere}.idpg .badge2{display:inline-flex;padding:.24rem .52rem;border-radius:999px;font-size:.7rem;font-weight:800}.idpg .badge2.primary{background:#eaf4ff;color:#2367a8;border:0}.idpg .badge2.warning{background:#fff7df;color:#8b6205;border:0}.idpg .badge2.success{background:#eafaf1;color:#17764c;border:0}.idpg .badge2.neutral{background:#f2f4f7;color:#5d6876;border:0}.idpg .badge2.muted{background:#f0f0f0;color:#777;border:0}.idpg .body{padding:1rem}.idpg .grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.7rem}.idpg .info{padding:.72rem;border:1px solid #e5ebf2;border-radius:11px;background:#fafcff}.idpg .wide{grid-column:1/-1}.idpg .label{font-size:.7rem;color:var(--muted);font-weight:700}.idpg .value{margin-top:.12rem;font-size:.79rem;color:#31465c;line-height:1.5;white-space:pre-line;overflow-wrap:anywhere}.idpg .progressbox{padding:.8rem;border-radius:12px;background:#f7fbff;border:1px solid #d8e7f5;margin-top:.8rem;display:flex;align-items:center;justify-content:space-between;gap:.8rem}.idpg .score{font-size:1rem;font-weight:800;color:#245f9f}.idpg .bar{height:7px;border-radius:99px;background:#e1eaf3;overflow:hidden;min-width:160px}.idpg .bar span{height:100%;display:block;background:#4385c0}.idpg .reach{font-size:.72rem;color:#167447;font-weight:800}.idpg .await{font-size:.72rem;color:#93610b;font-weight:800}.idpg .activity{margin-top:1rem;padding-top:1rem;border-top:1px solid #e7edf3}.idpg .activity-head{display:flex;justify-content:space-between;align-items:center;gap:.5rem;flex-wrap:wrap;margin-bottom:.55rem}.idpg .activity-title{font-size:.8rem;font-weight:800;color:#40556c}.idpg .act{display:grid;grid-template-columns:100px minmax(0,1fr) 130px auto;gap:.65rem;align-items:center;padding:.68rem .72rem;border:1px solid #e6ebf1;border-radius:10px;margin-bottom:.5rem}.idpg .act-date,.idpg .act-status{font-size:.73rem;color:#637489}.idpg .act-detail{font-size:.78rem;color:#34485e;line-height:1.45;overflow-wrap:anywhere}.idpg .act-result{font-size:.72rem;color:#607184;margin-top:.25rem}.idpg .readonly{display:inline-flex;padding:.3rem .6rem;border-radius:999px;background:#f1f5f9;color:#64748b;font-size:.72rem;font-weight:800}.idpg .empty{text-align:center;padding:2rem;color:#66788e}.idpg .terminal-note{margin-top:.6rem;padding:.55rem .65rem;border-radius:9px;background:#f8fafc;border:1px solid #e2e8f0;font-size:.72rem;color:#5c6b7c;white-space:pre-line}
@media(max-width:1199px){.idpg .grid{grid-template-columns:repeat(2,minmax(0,1fr))}.idpg .act{grid-template-columns:90px minmax(0,1fr) 130px}.idpg .act-actions{grid-column:1/-1;justify-content:flex-end}}@media(max-width:767px){.idpg .head,.idpg .card-head,.idpg .progressbox{flex-direction:column}.idpg .actions{width:100%}.idpg .act{grid-template-columns:1fr}}@media(max-width:575px){.idpg .grid{grid-template-columns:1fr}.idpg .actions,.idpg .card-actions,.idpg .act-actions{display:grid;grid-template-columns:1fr;width:100%}.idpg .btnx{width:100%}}
</style>

<div class="container-fluid px-2 px-lg-3 idpg">
    <div class="head">
        <div><h4><i class="bi bi-bullseye me-2 text-primary"></i>เป้าหมายและแผนกิจกรรมรายบุคคล</h4><div class="sub">ผู้รับบริการ: <strong>{{ $clientName }}</strong> • อายุ {{ $ageText }} • แผนครั้งที่ {{ $plan->plan_no }} • คะแนนล่าสุดใช้เพื่อช่วยติดตาม ไม่ตัดสินแทนผู้ปฏิบัติงาน</div></div>
        <div class="actions">@if($readOnly)<span class="readonly"><i class="bi bi-eye"></i>อ่านอย่างเดียว</span>@endif<a class="btnx light" href="{{ route('individual-development.index',$client->id) }}"><i class="bi bi-arrow-left"></i>กลับหน้าหลัก</a>@if($canCreate && !$readOnly)<a class="btnx light" href="{{ route('individual-development.followups.create',$client->id) }}"><i class="bi bi-activity"></i>บันทึกติดตาม</a><a class="btnx primary" href="{{ route('individual-development.goals.create',$client->id) }}"><i class="bi bi-plus-circle"></i>เพิ่มเป้าหมาย</a>@endif</div>
    </div>

    @forelse($goals as $goal)
        @php
            $gp=$goalProgress[$goal->id] ?? [];
            $terminal=in_array($goal->status,['achieved','cancelled'],true);
            $openActs=$goal->activities->whereIn('status',['planned','in_progress'])->count();
        @endphp
        <div class="card">
            <div class="card-head">
                <div><div class="domain">{{ $goal->domain?->name ?? '-' }} @if($goal->indicator) • {{ $goal->indicator->name }}@endif</div><div class="name">{{ $loop->iteration }}. {{ $goal->title }}</div></div>
                <div class="card-actions"><span class="badge2 {{ $statusClass[$goal->status] ?? 'neutral' }}">{{ $statusLabels[$goal->status] ?? $goal->status }}</span>
                    @if($canUpdate && !$readOnly && !$terminal)<a class="btnx light" href="{{ route('individual-development.goals.edit',[$client->id,$goal->id]) }}"><i class="bi bi-pencil"></i>แก้ไข</a>@endif
                    @if($canUpdate && !$readOnly && ($gp['needs_confirmation'] ?? false))<form method="POST" action="{{ route('individual-development.goals.achieve',[$client->id,$goal->id]) }}" class="achieve-form">@csrf<button class="btnx success" type="submit"><i class="bi bi-check2-circle"></i>ยืนยันบรรลุ</button></form>@endif
                    @if($canUpdate && !$readOnly && !$terminal)<button type="button" class="btnx warning" data-bs-toggle="modal" data-bs-target="#cancelGoal{{ $goal->id }}"><i class="bi bi-slash-circle"></i>ยกเลิก</button>@endif
                    @if($canUpdate && !$readOnly && $terminal)<button type="button" class="btnx light" data-bs-toggle="modal" data-bs-target="#reopenGoal{{ $goal->id }}"><i class="bi bi-arrow-counterclockwise"></i>เปิดอีกครั้ง</button>@endif
                    @if($canDelete && !$readOnly && ($canDeleteGoalMap[$goal->id] ?? false))<form method="POST" action="{{ route('individual-development.goals.destroy',[$client->id,$goal->id]) }}" class="safe-delete">@csrf @method('DELETE')<button class="btnx danger" type="submit"><i class="bi bi-trash"></i>ลบ</button></form>@endif
                </div>
            </div>
            <div class="body">
                <div class="grid">
                    <div class="info"><div class="label">Baseline → เป้าหมาย</div><div class="value fw-bold">{{ $goal->baseline_level ?? '-' }} → {{ $goal->target_level ?? '-' }}</div></div>
                    <div class="info"><div class="label">ความสำคัญ</div><div class="value fw-bold">{{ $priorityLabels[$goal->priority] ?? $goal->priority }}</div></div>
                    <div class="info"><div class="label">กำหนดสำเร็จ</div><div class="value fw-bold">{{ $thaiDate($goal->target_date) }}</div></div>
                    <div class="info"><div class="label">ผู้รับผิดชอบ</div><div class="value fw-bold">{{ $goal->responsible_name ?: '-' }}</div></div>
                    <div class="info wide"><div class="label">ตัวชี้วัดความสำเร็จ</div><div class="value">{{ $goal->success_indicator ?: '-' }}</div></div>
                    @if($goal->measurement_method || $goal->target_value !== null)<div class="info wide"><div class="label">วิธีการวัด</div><div class="value">{{ $goal->measurement_method ?: '-' }}@if($goal->target_value !== null) • ค่าเป้าหมาย {{ rtrim(rtrim(number_format((float)$goal->target_value,2,'.',','),'0'),'.') }} {{ $goal->target_unit }}@endif</div></div>@endif
                </div>
                <div class="progressbox"><div><div class="label">ความก้าวหน้าจากคะแนนล่าสุด</div><div class="score">{{ $gp['baseline'] ?? '-' }} → {{ $gp['current'] ?? '-' }} → Target {{ $gp['target'] ?? '-' }}</div>@if($gp['needs_confirmation'] ?? false)<div class="await">ถึงระดับเป้าหมายแล้ว • รอผู้ปฏิบัติงานยืนยัน</div>@elseif($goal->status==='achieved')<div class="reach">ยืนยันบรรลุแล้ว {{ $goal->achieved_at ? '• '.$thaiDate($goal->achieved_at) : '' }}</div>@endif</div><div><div class="small text-muted text-end">{{ $gp['progress_percent'] ?? 0 }}%</div><div class="bar"><span style="width:{{ $gp['progress_percent'] ?? 0 }}%"></span></div></div></div>
                @if($goal->status==='cancelled' && $goal->cancel_reason)<div class="terminal-note"><strong>เหตุผลที่ยกเลิก:</strong> {{ $goal->cancel_reason }}</div>@elseif($goal->status==='achieved' && $goal->status_note)<div class="terminal-note">{{ $goal->status_note }}</div>@endif

                <div class="activity">
                    <div class="activity-head"><div class="activity-title"><i class="bi bi-list-check me-1"></i>กิจกรรมตามแผน ({{ $goal->activities->count() }})</div>@if($canCreate && !$readOnly && !$terminal)<a class="btnx light" href="{{ route('individual-development.activities.create',[$client->id,$goal->id]) }}"><i class="bi bi-plus-circle"></i>เพิ่มกิจกรรม</a>@endif</div>
                    @forelse($goal->activities as $activity)
                        <div class="act"><div class="act-date">{{ $thaiDate($activity->activity_date) }}</div><div class="act-detail"><strong>{{ $activity->activity_type ?: 'กิจกรรม' }}</strong><br>{{ $activity->detail }}@if($activity->result)<div class="act-result"><strong>ผล:</strong> {{ $activity->result }}</div>@endif @if($activity->cancel_reason)<div class="act-result"><strong>เหตุผลยกเลิก:</strong> {{ $activity->cancel_reason }}</div>@endif</div><div class="act-status">{{ $activityStatus[$activity->status] ?? $activity->status }}@if($activity->responsible_name)<br>{{ $activity->responsible_name }}@endif</div><div class="act-actions">
                            @if($canUpdate && !$readOnly && !$terminal && $activity->status!=='cancelled')<a class="btnx light" href="{{ route('individual-development.activities.edit',[$client->id,$activity->id]) }}"><i class="bi bi-pencil"></i>แก้ไข</a>@endif
                            @if($canUpdate && !$readOnly && !$terminal && !in_array($activity->status,['completed','cancelled'],true))<button type="button" class="btnx warning" data-bs-toggle="modal" data-bs-target="#cancelActivity{{ $activity->id }}">ยกเลิก</button>@endif
                            @if($canDelete && !$readOnly && ($canDeleteActivityMap[$activity->id] ?? false))<form method="POST" action="{{ route('individual-development.activities.destroy',[$client->id,$activity->id]) }}" class="safe-delete">@csrf @method('DELETE')<button class="btnx danger" type="submit">ลบ</button></form>@endif
                        </div></div>
                    @empty<div class="small text-muted py-2">ยังไม่มีกิจกรรมสำหรับเป้าหมายนี้</div>@endforelse
                </div>
            </div>
        </div>

        @if($canUpdate && !$readOnly && !$terminal)
        <div class="modal fade" id="cancelGoal{{ $goal->id }}" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><form method="POST" action="{{ route('individual-development.goals.cancel',[$client->id,$goal->id]) }}">@csrf<div class="modal-header"><h5 class="modal-title">ยกเลิกเป้าหมาย</h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div><div class="modal-body"><p class="small text-muted">ใช้เมื่อเป้าหมายไม่ดำเนินการต่อ ระบบจะเก็บประวัติและยกเลิกกิจกรรมที่ยังเปิดอยู่</p><label class="form-label fw-bold">เหตุผล <span class="text-danger">*</span></label><textarea name="reason" class="form-control" rows="3" required></textarea></div><div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">ปิด</button><button class="btn btn-warning" type="submit">ยืนยันยกเลิก</button></div></form></div></div></div>
        @endif
        @if($canUpdate && !$readOnly && $terminal)
        <div class="modal fade" id="reopenGoal{{ $goal->id }}" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><form method="POST" action="{{ route('individual-development.goals.reopen',[$client->id,$goal->id]) }}">@csrf<div class="modal-header"><h5 class="modal-title">เปิดเป้าหมายอีกครั้ง</h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div><div class="modal-body"><p class="small text-muted">ใช้เมื่อต้องแก้ไข/ดำเนินงานต่อหลังจากเคยปิดสถานะแล้ว พร้อมเก็บเหตุผลในประวัติ</p><label class="form-label fw-bold">เหตุผล <span class="text-danger">*</span></label><textarea name="reason" class="form-control" rows="3" required></textarea></div><div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">ปิด</button><button class="btn btn-primary" type="submit">เปิดเป้าหมาย</button></div></form></div></div></div>
        @endif
        @foreach($goal->activities as $activity)
            @if($canUpdate && !$readOnly && !$terminal && !in_array($activity->status,['completed','cancelled'],true))
            <div class="modal fade" id="cancelActivity{{ $activity->id }}" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><form method="POST" action="{{ route('individual-development.activities.cancel',[$client->id,$activity->id]) }}">@csrf<div class="modal-header"><h5 class="modal-title">ยกเลิกกิจกรรม</h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div><div class="modal-body"><label class="form-label fw-bold">เหตุผล <span class="text-danger">*</span></label><textarea name="cancel_reason" class="form-control" rows="3" required></textarea></div><div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">ปิด</button><button class="btn btn-warning" type="submit">ยืนยันยกเลิก</button></div></form></div></div></div>
            @endif
        @endforeach
    @empty
        <div class="card empty"><div class="fs-2 text-primary mb-2"><i class="bi bi-bullseye"></i></div><div class="fw-bold text-dark">ยังไม่มีเป้าหมายการพัฒนา</div><div class="small mt-1">เลือกประเด็นจาก Baseline แล้วกำหนดเป้าหมายที่วัดผลได้</div>@if($canCreate && !$readOnly)<a class="btnx primary mt-3" href="{{ route('individual-development.goals.create',$client->id) }}">สร้างเป้าหมายแรก</a>@endif</div>
    @endforelse
</div>

<script>
document.addEventListener('DOMContentLoaded',function(){
 document.querySelectorAll('.safe-delete').forEach(function(f){f.addEventListener('submit',function(e){if(!window.Swal){if(!confirm('ยืนยันลบรายการที่ยังไม่ถูกใช้งาน?'))e.preventDefault();return;}e.preventDefault();Swal.fire({icon:'warning',title:'ยืนยันการลบ?',text:'การลบใช้เฉพาะข้อมูลที่สร้างผิดและยังไม่ถูกนำไปใช้งาน หากมีประวัติควรใช้ “ยกเลิก”',showCancelButton:true,confirmButtonText:'ลบ',cancelButtonText:'ยกเลิก',confirmButtonColor:'#b91c1c'}).then(r=>{if(r.isConfirmed)f.submit();});});});
 document.querySelectorAll('.achieve-form').forEach(function(f){f.addEventListener('submit',function(e){if(!window.Swal)return;e.preventDefault();Swal.fire({icon:'question',title:'ยืนยันว่าบรรลุเป้าหมาย?',text:'ระบบตรวจเพียงว่าคะแนนล่าสุดถึง Target การยืนยันนี้เป็นการตัดสินใจของผู้ปฏิบัติงาน',showCancelButton:true,confirmButtonText:'ยืนยันบรรลุ',cancelButtonText:'กลับไปตรวจ'}).then(r=>{if(r.isConfirmed)f.submit();});});});
 @if(session('success') || session('warning')) if(window.Swal){Swal.fire({icon:@json(session('success')?'success':'warning'),title:@json(session('success')?'สำเร็จ':'แจ้งเตือน'),text:@json(session('success') ?? session('warning')),confirmButtonText:'OK',timer:3000,timerProgressBar:true});}@endif
});
</script>

{{-- IDP_VALIDATION_COMPLETE_V1_INCLUDE --}}
@include('frontend.client.individual_development._validation')
@endsection
