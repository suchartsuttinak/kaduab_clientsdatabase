@extends('admin_client.admin_client')

@section('content')
@php
    $clientName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
    $thaiDate = static function ($value): string {
        if (!$value) return '-';
        try { $d=$value instanceof \Carbon\CarbonInterface?$value:\Carbon\Carbon::parse($value,'Asia/Bangkok'); return $d->format('d/m/').($d->year+543); } catch(\Throwable $e){ return '-'; }
    };
@endphp
<style>
.idp-close{--border:#e2e8f0;--text:#203249;--muted:#6e7f92;padding-bottom:1.5rem}.idp-close .c-head,.idp-close .c-card{background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:0 6px 20px rgba(32,50,73,.045)}.idp-close .c-head{padding:1rem 1.15rem;margin-bottom:1rem;display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap}.idp-close .c-title{margin:0;font-size:1.12rem;font-weight:800;color:var(--text)}.idp-close .c-sub{margin:.3rem 0 0;color:var(--muted);font-size:.85rem}.idp-close .c-card{overflow:hidden}.idp-close .c-card-head{padding:.85rem 1rem;background:#fbfcfe;border-bottom:1px solid var(--border);font-weight:800}.idp-close .c-card-body{padding:1rem}.idp-close .summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.7rem}.idp-close .box{padding:.75rem;border:1px solid #e4eaf1;border-radius:12px;background:#fafcff}.idp-close .label{font-size:.74rem;color:var(--muted)}.idp-close .value{font-size:.88rem;font-weight:800;color:#31485f;margin-top:.2rem}.idp-close textarea{min-height:115px;border-radius:10px}.idp-close .actions{display:flex;justify-content:flex-end;gap:.55rem;flex-wrap:wrap;margin-top:1rem}.idp-close .btn{min-height:42px;border-radius:10px;font-weight:700}@media(max-width:767.98px){.idp-close .summary{grid-template-columns:1fr 1fr}}@media(max-width:575.98px){.idp-close .summary{grid-template-columns:1fr}.idp-close .actions{display:grid;grid-template-columns:1fr}.idp-close .actions .btn{width:100%}}
</style>
<div class="container-fluid px-2 px-lg-3 idp-close">
    <div class="c-head">
        <div><h4 class="c-title"><i class="bi bi-check2-circle me-2 text-success"></i>สรุปผลและปิดแผนพัฒนารายบุคคล</h4><div class="c-sub">ผู้รับบริการ: <strong>{{ $clientName }}</strong> • อายุ {{ $ageText }} • แผนครั้งที่ {{ $plan->plan_no }}</div></div>
        <a href="{{ route('individual-development.index',$client->id) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>กลับหน้าหลัก</a>
    </div>
    @if($errors->any())<div class="alert alert-danger rounded-3"><div class="fw-bold">กรุณาตรวจสอบข้อมูล</div><ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="c-card mb-3"><div class="c-card-head">ตรวจสอบก่อนปิดแผน</div><div class="c-card-body">
        <div class="summary">
            <div class="box"><div class="label">วันที่เริ่มแผน</div><div class="value">{{ $thaiDate($plan->start_date) }}</div></div>
            <div class="box"><div class="label">ติดตามทั้งหมด</div><div class="value">{{ $plan->followups->count() }} ครั้ง</div></div>
            <div class="box"><div class="label">เป้าหมายทั้งหมด</div><div class="value">{{ $plan->goals->count() }} รายการ</div></div>
            <div class="box"><div class="label">บรรลุเป้าหมาย</div><div class="value">{{ $plan->goals->where('status','achieved')->count() }} รายการ</div></div>
        </div>
        <div class="small text-muted mt-3"><i class="bi bi-shield-check me-1"></i>เมื่อปิดแผนแล้ว Baseline / Goal / Activity / Follow-up จะเป็นประวัติอ่านอย่างเดียว และสามารถเริ่มแผนครั้งใหม่ได้โดยไม่ลบข้อมูลเดิม</div>
    </div></div>
    <form data-idp-th-validation="1" method="POST" action="{{ route('individual-development.close',$client->id) }}" id="closePlanForm">
        @csrf
        <div class="c-card"><div class="c-card-head">สรุปผลสุดท้าย</div><div class="c-card-body">
            <div class="row g-3">
                <div class="col-12"><label class="form-label fw-bold">เหตุผล/เกณฑ์ที่ใช้ในการปิดแผน <span class="text-danger">*</span></label><textarea name="close_reason" class="form-control" required placeholder="เช่น เป้าหมายที่กำหนดได้รับการยืนยันว่าบรรลุแล้ว และกิจกรรมตามแผนสิ้นสุดครบถ้วน">{{ old('close_reason') }}</textarea></div>
                <div class="col-12"><label class="form-label fw-bold">ผลลัพธ์สุดท้ายของแผน <span class="text-danger">*</span></label><textarea name="final_outcome" class="form-control" required placeholder="สรุปพัฒนาการก่อน–หลัง สิ่งที่เปลี่ยนแปลง และผลที่เกิดขึ้นกับผู้รับบริการ">{{ old('final_outcome') }}</textarea></div>
                <div class="col-12"><label class="form-label fw-bold">ข้อเสนอแนะ/แนวทางหลังปิดแผน</label><textarea name="final_recommendation" class="form-control" placeholder="สิ่งที่ควรรักษา ติดตามเป็นระยะ หรือประเด็นที่อาจนำไปสร้างแผนครั้งถัดไป">{{ old('final_recommendation') }}</textarea></div>
            </div>
        </div></div>
        <div class="actions"><a href="{{ route('individual-development.index',$client->id) }}" class="btn btn-outline-secondary">ยกเลิก</a><button type="submit" class="btn btn-success"><i class="bi bi-check2-circle me-1"></i>ยืนยันปิดแผน</button></div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){const f=document.getElementById('closePlanForm');if(!f)return;f.addEventListener('submit',function(e){if(!window.Swal)return;e.preventDefault();Swal.fire({icon:'question',title:'ยืนยันปิดแผน?',text:'หลังปิดแผนข้อมูลทั้งหมดจะเป็นประวัติอ่านอย่างเดียว และสามารถเริ่มแผนครั้งใหม่ได้',showCancelButton:true,confirmButtonText:'ยืนยันปิดแผน',cancelButtonText:'ยกเลิก'}).then(r=>{if(r.isConfirmed)f.submit();});});});
</script>
@include('frontend.client.individual_development.partials._thai_validation')
@endsection
