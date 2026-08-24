@extends('admin_client.admin_client')

@section('content')
@php
    $clientName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
    $isEdit = ($mode ?? 'create') === 'edit' && $plan;
    $action = $isEdit ? route('individual-development.update', $client->id) : route('individual-development.store', $client->id);
    $today = now('Asia/Bangkok')->format('Y-m-d');
@endphp
<style>
.idp-start{--b:#e2e8f0;--text:#1e293b;--muted:#64748b;max-width:1180px;margin:0 auto;padding-bottom:1.2rem}.idp-start *{min-width:0}.idp-start .box{background:#fff;border:1px solid var(--b);border-radius:16px;box-shadow:0 5px 20px rgba(30,41,59,.045)}.idp-start .head{padding:1rem 1.15rem;margin-bottom:1rem}.idp-start h4{margin:0;font-size:1.15rem;font-weight:800;color:var(--text)}.idp-start .sub{margin:.3rem 0 0;color:var(--muted);font-size:.83rem;line-height:1.6}.idp-start .meta{display:flex;flex-wrap:wrap;gap:.45rem;margin-top:.7rem}.idp-start .pill{display:inline-flex;align-items:center;gap:.35rem;padding:.32rem .65rem;border:1px solid #dbe4ee;border-radius:999px;background:#f8fbff;color:#475569;font-size:.78rem}.idp-start .body{padding:1.05rem}.idp-start .section-title{font-size:.9rem;font-weight:800;color:#334155}.idp-start .form-label{font-size:.82rem;font-weight:700;color:#405269}.idp-start .form-control{border-radius:10px;border-color:#d9e2ec;min-height:42px}.idp-start textarea.form-control{min-height:120px;resize:vertical}.idp-start .hint{font-size:.76rem;color:var(--muted);line-height:1.55;margin-top:.3rem}.idp-start .workflow{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:.5rem;margin-top:.9rem}.idp-start .step{padding:.7rem;border:1px solid #e2e8f0;border-radius:12px;background:#fbfdff;text-align:center;font-size:.74rem;color:#55657a}.idp-start .step b{display:block;color:#2d5f91;margin-bottom:.15rem}.idp-start .lock{padding:.7rem .8rem;border:1px solid #d8e6f3;border-radius:11px;background:#f7fbff;color:#4f6780;font-size:.78rem;margin-bottom:1rem}.idp-start .note{padding:.85rem;border:1px solid #dbeafe;border-radius:12px;background:#f8fbff;color:#405b75;font-size:.79rem;line-height:1.6}.idp-start .actions{display:flex;justify-content:flex-end;gap:.6rem;flex-wrap:wrap;padding:1rem;border-top:1px solid var(--b);background:#fbfcfe;border-radius:0 0 16px 16px}.idp-start .btnx{display:inline-flex;align-items:center;justify-content:center;gap:.4rem;min-height:40px;padding:.5rem .9rem;border-radius:10px;font-size:.83rem;font-weight:700;text-decoration:none!important}.idp-start .primary{border:0;background:linear-gradient(135deg,#3b82c4,#2563a9);color:#fff}.idp-start .light{border:1px solid #d7e0e9;background:#fff;color:#4f6075}@media(max-width:767.98px){.idp-start .workflow{grid-template-columns:1fr 1fr}.idp-start .actions{flex-direction:column}.idp-start .btnx{width:100%}}
</style>

<div class="container-fluid px-2 px-lg-3 idp-start">
    <div class="box head">
        <h4><i class="bi {{ $isEdit ? 'bi-pencil-square' : 'bi-clipboard2-plus' }} me-2 text-primary"></i>{{ $isEdit ? 'แก้ไขกรอบแผนพัฒนารายบุคคล' : 'เริ่มแผนพัฒนารายบุคคลรอบใหม่' }}</h4>
        <p class="sub">หน้านี้เก็บเฉพาะกรอบแผนที่จำเป็น ส่วนจุดแข็ง ความต้องการ Baseline เป้าหมาย กิจกรรม การติดตาม และ Outcome จัดการต่อจาก Workspace รายบุคคล เพื่อไม่ให้กรอกข้อมูลซ้ำ</p>
        <div class="meta">
            <span class="pill"><i class="bi bi-person"></i>ผู้รับบริการ: <strong>{{ $clientName }}</strong></span>
            <span class="pill"><i class="bi bi-calendar3"></i>อายุ: <strong>{{ $ageText }}</strong></span>
            <span class="pill"><i class="bi bi-list-ol"></i>แผนครั้งที่: <strong>{{ $nextPlanNo }}</strong></span>
        </div>
        <div class="workflow">
            <div class="step"><b>1. เริ่มแผน</b>กรอบแผน</div>
            <div class="step"><b>2. เข้าใจเด็ก</b>จุดแข็ง/ความต้องการ</div>
            <div class="step"><b>3. Baseline</b>ค่าตั้งต้น</div>
            <div class="step"><b>4. ลงมือ</b>Goal/Activity/ติดตาม</div>
            <div class="step"><b>5. วัดผล</b>Outcome/รายงาน</div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3"><div class="fw-bold mb-1">กรุณาตรวจสอบข้อมูล</div><ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form data-idp-th-validation="1" method="POST" action="{{ $action }}" id="individualDevelopmentPlanForm">
        @csrf
        @if($isEdit) @method('PATCH') @endif
        <div class="box overflow-hidden">
            <div class="body">
                <div class="section-title mb-3"><i class="bi bi-journal-check me-1"></i>กรอบแผน</div>
                @if($isEdit && $lockStartDate)
                    <div class="lock"><i class="bi bi-lock me-1"></i>วันที่เริ่มแผนถูกล็อกหลังมี Baseline เพื่อรักษาลำดับประวัติ คุณยังแก้ไขวันสิ้นสุดและเป้าหมายภาพรวมได้</div>
                @endif
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">วันที่เริ่มแผน <span class="text-danger">*</span></label>
                        <input type="date" id="planStartDate" name="start_date" class="form-control @error('start_date') is-invalid @enderror" max="{{ $today }}" value="{{ old('start_date', optional($plan?->start_date)->format('Y-m-d') ?? $today) }}" {{ $lockStartDate ? 'disabled' : '' }} required>
                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">วันที่คาดว่าจะสิ้นสุดแผน</label>
                        <input type="date" id="planEndDate" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', optional($plan?->end_date)->format('Y-m-d')) }}">
                        <div class="hint">เป็นกำหนดการล่วงหน้า จึงสามารถเลือกวันในอนาคตได้</div>
                        @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">เป้าหมายภาพรวมของแผน <span class="text-danger">*</span></label>
                        <textarea id="planOverallGoal" name="overall_goal" class="form-control @error('overall_goal') is-invalid @enderror" placeholder="ระบุภาพรวมว่าในรอบแผนนี้ต้องการให้เกิดการเปลี่ยนแปลงสำคัญอะไรกับผู้รับบริการ" required>{{ old('overall_goal', $plan?->overall_goal) }}</textarea>
                        <div class="hint">เขียนเป็นผลลัพธ์ภาพรวม ไม่จำเป็นต้องใส่รายละเอียดกิจกรรม เพราะจะกำหนดใน Goal/Activity ภายหลัง</div>
                        @error('overall_goal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <div class="note"><i class="bi bi-info-circle me-1"></i><strong>หลังบันทึก:</strong> ระบบจะกลับไป Workspace ของเด็ก เพื่อบันทึกจุดแข็ง/ศักยภาพ/ความต้องการ → Baseline → สร้างเป้าหมาย → กิจกรรม/ติดตาม → Outcome → รายงาน ตามลำดับ โดยข้อมูลเดิมของแต่ละส่วนไม่ถูกล้างจากการแก้กรอบแผนหน้านี้</div>
                    </div>
                </div>
            </div>
            <div class="actions">
                <a href="{{ route('individual-development.index', $client->id) }}" class="btnx light"><i class="bi bi-x-circle"></i>ยกเลิก</a>
                <button type="submit" class="btnx primary" id="savePlanButton"><i class="bi bi-check-circle"></i>{{ $isEdit ? 'บันทึกกรอบแผน' : 'เริ่มแผน' }}</button>
            </div>
        </div>
    </form>
</div>
@include('frontend.client.individual_development.partials._thai_validation')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('individualDevelopmentPlanForm');
    const button = document.getElementById('savePlanButton');
    const startDate = document.getElementById('planStartDate');
    const endDate = document.getElementById('planEndDate');
    const overallGoal = document.getElementById('planOverallGoal');
    if (!form || !button) return;

    function syncDates() {
        if (!startDate || !endDate) return;
        const min = startDate.value || '';
        endDate.min = min;
        endDate.setCustomValidity('');
        if (min && endDate.value && endDate.value < min) {
            endDate.setCustomValidity('วันที่คาดว่าจะสิ้นสุดแผนต้องไม่น้อยกว่าวันที่เริ่มแผน');
        }
    }
    if (startDate) startDate.addEventListener('change', syncDates);
    if (endDate) endDate.addEventListener('change', syncDates);
    syncDates();

    function lockButton() {
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        button.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> กำลังบันทึก...';
    }

    form.addEventListener('submit', function (event) {
        syncDates();
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            const invalid = form.querySelector(':invalid');
            if (invalid) {
                invalid.focus({preventScroll:true});
                invalid.scrollIntoView({behavior:'smooth', block:'center'});
                invalid.reportValidity();
            }
            if (window.Swal) {
                Swal.fire({icon:'warning', title:'กรุณาตรวจสอบข้อมูล', text:'กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วนและตรวจสอบวันที่ให้ถูกต้อง', confirmButtonText:'OK'});
            }
            return;
        }

        if (form.dataset.confirmed === '1') {
            if (!button.disabled) lockButton();
            return;
        }

        if (window.Swal) {
            event.preventDefault();
            Swal.fire({
                icon:'question',
                title:@json($isEdit ? 'ยืนยันการบันทึกกรอบแผน?' : 'ยืนยันการเริ่มแผนพัฒนารอบใหม่?'),
                html:@json($isEdit ? 'ระบบจะปรับเฉพาะกรอบแผน โดยไม่ล้างข้อมูลจุดแข็ง ความต้องการ Baseline เป้าหมาย หรือประวัติเดิม' : 'หลังเริ่มแผน ระบบจะพากลับ Workspace เพื่อดำเนินงานตามลำดับ จุดแข็ง/ความต้องการ → Baseline → เป้าหมาย → กิจกรรม/ติดตาม → Outcome'),
                showCancelButton:true,
                confirmButtonText:@json($isEdit ? 'บันทึกการแก้ไข' : 'เริ่มแผน'),
                cancelButtonText:'ยกเลิก',
                reverseButtons:true
            }).then(function (result) {
                if (!result.isConfirmed) return;
                form.dataset.confirmed = '1';
                lockButton();
                form.requestSubmit();
            });
            return;
        }
        lockButton();
    });
});
</script>
@endsection
