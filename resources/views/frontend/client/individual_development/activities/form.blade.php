@extends('admin_client.admin_client')

@section('content')
@php
    $clientName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
    $isEdit = $mode === 'edit' && $activity;
    $selectedStatus = old('status', $activity?->status ?? 'planned');
@endphp
<style>
.idp-activity-form{--border:#e1e8f0;--text:#203249;--muted:#6d7f92;padding-bottom:1.5rem}.idp-activity-form .af-head,.idp-activity-form .af-card{background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:0 6px 20px rgba(32,50,73,.045)}.idp-activity-form .af-head{padding:1rem 1.15rem;margin-bottom:1rem;display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;flex-wrap:wrap}.idp-activity-form .af-title{margin:0;font-size:1.12rem;font-weight:800;color:var(--text)}.idp-activity-form .af-sub{margin:.3rem 0 0;color:var(--muted);font-size:.85rem;line-height:1.55}.idp-activity-form .af-goal{margin-top:.7rem;padding:.72rem .8rem;border:1px solid #dbe8f5;border-radius:11px;background:#f8fbff;color:#365675;font-size:.82rem}.idp-activity-form .af-card{overflow:hidden}.idp-activity-form .af-card-head{padding:.85rem 1rem;background:#fbfcfe;border-bottom:1px solid var(--border);font-weight:800;color:var(--text)}.idp-activity-form .af-card-body{padding:1rem}.idp-activity-form .form-label{font-size:.82rem;font-weight:700;color:#42556c}.idp-activity-form .form-control,.idp-activity-form .form-select{border-radius:10px;border-color:#d9e2ec;min-height:42px}.idp-activity-form textarea.form-control{min-height:95px}.idp-activity-form .af-actions{display:flex;justify-content:flex-end;gap:.55rem;flex-wrap:wrap;margin-top:1rem}.idp-activity-form .btn{border-radius:10px;min-height:42px;font-weight:700;padding:.58rem .95rem}.idp-activity-form .btn-save{background:linear-gradient(135deg,#3577bd,#245f9f);border:0;color:#fff}.idp-activity-form .btn-save:hover{color:#fff}@media(max-width:575.98px){.idp-activity-form .af-actions{display:grid;grid-template-columns:1fr}.idp-activity-form .af-actions .btn{width:100%}}

/* IDP_PHASE5_UI_STABLE_V1 */
.idp-activity-form{width:100%;min-width:0}
.idp-activity-form *{min-width:0}
.idp-activity-form .form-control,.idp-activity-form .form-select{max-width:100%}
.idp-activity-form textarea,.idp-activity-form .af-goal{overflow-wrap:anywhere}

</style>
<div class="container-fluid px-2 px-lg-3 idp-activity-form">
    <div class="af-head">
        <div>
            <h4 class="af-title"><i class="bi bi-list-check me-2 text-primary"></i>{{ $isEdit ? 'แก้ไขกิจกรรมตามแผน' : 'เพิ่มกิจกรรมตามแผน' }}</h4>
            <p class="af-sub">ผู้รับบริการ: <strong>{{ $clientName }}</strong> • อายุ {{ $ageText }} • แผนครั้งที่ {{ $plan->plan_no }}</p>
            <div class="af-goal"><strong>เป้าหมาย:</strong> {{ $goal->title }}<br><span class="text-muted">{{ $goal->domain?->name ?? '-' }} @if($goal->indicator) • {{ $goal->indicator->name }} @endif</span></div>
        </div>
        <a href="{{ route('individual-development.goals.index', $client->id) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>กลับเป้าหมาย</a>
    </div>

    @if($errors->any())<div class="alert alert-danger rounded-3"><div class="fw-bold mb-1">กรุณาตรวจสอบข้อมูล</div><ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <form data-idp-th-validation="1" method="POST" action="{{ $isEdit ? route('individual-development.activities.update', [$client->id,$activity->id]) : route('individual-development.activities.store', [$client->id,$goal->id]) }}">
        @csrf
        @if($isEdit) @method('PATCH') @endif
        <div class="af-card">
            <div class="af-card-head">รายละเอียดกิจกรรม <span class="fw-normal text-muted small ms-1">วางแผนก่อน → ทำจริง → บันทึกผล → จึงนำไปใช้ประกอบ Follow-up</span></div>
            <div class="af-card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <label class="form-label">วันที่เริ่มกิจกรรม <span class="text-danger">*</span></label>
                        <input type="date" name="activity_date" class="form-control" min="{{ optional($plan->start_date)->format('Y-m-d') }}" value="{{ old('activity_date', optional($activity?->activity_date)->format('Y-m-d') ?? now('Asia/Bangkok')->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">วันที่สิ้นสุด</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date', optional($activity?->end_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">ประเภทกิจกรรม</label>
                        <input type="text" name="activity_type" class="form-control" maxlength="255" value="{{ old('activity_type', $activity?->activity_type) }}" placeholder="เช่น ให้คำปรึกษา / กิจกรรมกลุ่ม">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">สถานะ <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>@foreach($statusLabels as $value=>$label)<option value="{{ $value }}" @selected($selectedStatus===$value)>{{ $label }}</option>@endforeach</select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">รายละเอียดกิจกรรม <span class="text-danger">*</span></label>
                        <textarea name="detail" class="form-control" maxlength="10000" placeholder="อธิบายวิธีดำเนินกิจกรรม สิ่งที่จะฝึก หรือการช่วยเหลือที่จัดให้" required>{{ old('detail', $activity?->detail) }}</textarea>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">ความถี่</label>
                        <input type="text" name="frequency" class="form-control" maxlength="255" value="{{ old('frequency', $activity?->frequency) }}" placeholder="เช่น สัปดาห์ละ 1 ครั้ง / ทุกวัน">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">ผู้รับผิดชอบ</label>
                        <input type="text" name="responsible_name" class="form-control" maxlength="255" value="{{ old('responsible_name', $activity?->responsible_name ?? $goal->responsible_name) }}" placeholder="ชื่อเจ้าหน้าที่/ครู/ผู้ดูแล">
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="form-label">ผลการดำเนินงาน <span class="text-danger" id="activityResultRequired" style="display:none">*</span></label>
                        <textarea name="result" id="activityResult" class="form-control" maxlength="10000" placeholder="ตอนวางแผนปล่อยว่างได้ เมื่อเสร็จสิ้นต้องบันทึกผล">{{ old('result', $activity?->result) }}</textarea>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="form-label">ปัญหา/อุปสรรค</label>
                        <textarea name="problem" class="form-control" maxlength="10000" placeholder="ปัญหาหรือข้อจำกัดที่พบ">{{ old('problem', $activity?->problem) }}</textarea>
                    </div>
                    <div class="col-12 col-lg-4">
                        <label class="form-label">สิ่งที่ต้องทำต่อ</label>
                        <textarea name="next_action" class="form-control" maxlength="10000" placeholder="สิ่งที่ควรดำเนินการต่อจากกิจกรรมนี้">{{ old('next_action', $activity?->next_action) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="af-actions">
            <a href="{{ route('individual-development.goals.index', $client->id) }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle me-1"></i>ยกเลิก</a>
            <button type="submit" class="btn btn-save"><i class="bi bi-check-circle me-1"></i>{{ $isEdit ? 'บันทึกการแก้ไข' : 'บันทึกกิจกรรม' }}</button>
        </div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){
 const status=document.querySelector('select[name="status"]'); const result=document.getElementById('activityResult'); const mark=document.getElementById('activityResultRequired');
 function sync(){const value=status?status.value:''; const done=value==='completed'; const started=value==='in_progress'||done; const activityDate=document.querySelector('input[name="activity_date"]'); const endDate=document.querySelector('input[name="end_date"]'); const today=@json(now('Asia/Bangkok')->format('Y-m-d')); if(result) result.required=done; if(mark) mark.style.display=done?'inline':'none'; if(activityDate){if(started)activityDate.max=today;else activityDate.removeAttribute('max');} if(endDate){if(done)endDate.max=today;else endDate.removeAttribute('max');}}
 if(status){status.addEventListener('change',sync);sync();}
});
</script>
@include('frontend.client.individual_development.partials._thai_validation')
@endsection
