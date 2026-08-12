@extends('admin_client.admin_client')

@section('content')
@php
    $clientName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
@endphp

<style>
    .idp-form-page{--idp-border:#e4eaf2;--idp-text:#1f2f46;--idp-muted:#6b7a90;padding-bottom:1rem}
    .idp-form-page .idp-header,.idp-form-page .idp-card{background:#fff;border:1px solid var(--idp-border);border-radius:16px;box-shadow:0 6px 20px rgba(31,47,70,.045)}
    .idp-form-page .idp-header{padding:1.05rem 1.2rem;margin-bottom:1rem}
    .idp-form-page .idp-title{margin:0;color:var(--idp-text);font-size:1.15rem;font-weight:800}
    .idp-form-page .idp-subtitle{margin:.28rem 0 0;color:var(--idp-muted);font-size:.88rem;line-height:1.6}
    .idp-form-page .idp-meta{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.75rem}
    .idp-form-page .idp-pill{display:inline-flex;align-items:center;gap:.35rem;padding:.34rem .7rem;border:1px solid #dbe5f0;border-radius:999px;background:#f8fbff;color:#41546d;font-size:.82rem}
    .idp-form-page .idp-card{overflow:hidden}
    .idp-form-page .idp-section-head{padding:.9rem 1rem;border-bottom:1px solid var(--idp-border);background:#fbfcfe;color:var(--idp-text);font-size:.94rem;font-weight:800}
    .idp-form-page .idp-body{padding:1rem}
    .idp-form-page .form-label{color:#35465c;font-size:.84rem;font-weight:700}
    .idp-form-page .form-control{min-height:42px;border-color:#d9e2ec;border-radius:10px;font-size:.88rem}
    .idp-form-page textarea.form-control{min-height:105px;resize:vertical}
    .idp-form-page .idp-hint{color:var(--idp-muted);font-size:.76rem;line-height:1.5;margin-top:.3rem}
    .idp-form-page .idp-actions{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:.6rem;padding:1rem;border-top:1px solid var(--idp-border);background:#fbfcfe}
    .idp-form-page .idp-btn{display:inline-flex;align-items:center;justify-content:center;gap:.4rem;min-height:40px;padding:.5rem .9rem;border-radius:10px;font-size:.84rem;font-weight:700;text-decoration:none!important}
    .idp-form-page .idp-btn-primary{border:0;background:linear-gradient(135deg,#3577bd 0%,#245f9f 100%);color:#fff;box-shadow:0 6px 14px rgba(36,95,159,.16)}
    .idp-form-page .idp-btn-light{border:1px solid #d7e0e9;background:#fff;color:#4f6075}
    @media(max-width:575.98px){.idp-form-page .idp-actions{flex-direction:column}.idp-form-page .idp-btn{width:100%}}

/* IDP_PHASE5_UI_STABLE_V1 */
.idp-form-page{width:100%;min-width:0}
.idp-form-page *{min-width:0}
.idp-form-page .form-control,.idp-form-page .form-select{max-width:100%}
.idp-form-page textarea{overflow-wrap:anywhere}

</style>

<div class="container-fluid px-2 px-lg-3 idp-form-page">
    <div class="idp-header">
        <h4 class="idp-title"><i class="bi bi-person-plus me-2 text-primary"></i>สร้างแผนพัฒนารายบุคคล</h4>
        <p class="idp-subtitle">บันทึกกรอบแผนและบริบทสำคัญของผู้รับบริการ ก่อนเข้าสู่การประเมินระดับเริ่มต้น (Baseline) 4 ด้าน</p>
        <div class="idp-meta">
            <span class="idp-pill"><i class="bi bi-person"></i>ผู้รับบริการ: <strong>{{ $clientName }}</strong></span>
            <span class="idp-pill"><i class="bi bi-calendar3"></i>อายุ: <strong>{{ $ageText }}</strong></span>
            <span class="idp-pill"><i class="bi bi-list-ol"></i>แผนครั้งที่: <strong>{{ $nextPlanNo }}</strong></span>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3">
            <div class="fw-bold mb-1">กรุณาตรวจสอบข้อมูล</div>
            <ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('individual-development.store', $client->id) }}" id="individualDevelopmentPlanForm">
        @csrf
        <div class="idp-card">
            <div class="idp-section-head"><i class="bi bi-clipboard-check me-1"></i>ข้อมูลแผนพัฒนา</div>
            <div class="idp-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">วันที่เริ่มแผน <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" max="{{ now('Asia/Bangkok')->format('Y-m-d') }}" value="{{ old('start_date', now('Asia/Bangkok')->format('Y-m-d')) }}" required>
                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">วันที่คาดว่าจะสิ้นสุดแผน</label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                        @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">เป้าหมายภาพรวมของแผน <span class="text-danger">*</span></label>
                        <textarea name="overall_goal" class="form-control @error('overall_goal') is-invalid @enderror" placeholder="ระบุผลลัพธ์หลักที่ต้องการให้เกิดขึ้นกับผู้รับบริการ" required>{{ old('overall_goal') }}</textarea>
                        @error('overall_goal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">จุดแข็งเบื้องต้น</label>
                        <textarea name="strength_summary" class="form-control">{{ old('strength_summary') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ประเด็นที่ควรพัฒนาเบื้องต้น</label>
                        <textarea name="development_need_summary" class="form-control">{{ old('development_need_summary') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ความต้องการของผู้รับบริการ</label>
                        <textarea name="client_need_summary" class="form-control">{{ old('client_need_summary') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ความต้องการของผู้ดูแล/ครอบครัว</label>
                        <textarea name="caregiver_need_summary" class="form-control">{{ old('caregiver_need_summary') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ปัจจัยเสี่ยง</label>
                        <textarea name="risk_factor_summary" class="form-control">{{ old('risk_factor_summary') }}</textarea>
                        <div class="idp-hint">บันทึกเฉพาะประเด็นที่เกี่ยวข้องกับการวางแผนช่วยเหลือและพัฒนา</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ปัจจัยคุ้มครอง</label>
                        <textarea name="protective_factor_summary" class="form-control">{{ old('protective_factor_summary') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">เครือข่าย/ทรัพยากรสนับสนุน</label>
                        <textarea name="support_network_summary" class="form-control">{{ old('support_network_summary') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="idp-actions">
                <a href="{{ route('individual-development.index', $client->id) }}" class="idp-btn idp-btn-light"><i class="bi bi-x-circle"></i>ยกเลิก</a>
                <button type="submit" class="idp-btn idp-btn-primary" id="savePlanButton"><i class="bi bi-check-circle"></i>บันทึกแผน</button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('individualDevelopmentPlanForm');
    const button = document.getElementById('savePlanButton');
    if (form && button) {
        form.addEventListener('submit', function () {
            if (button.disabled) return;
            button.disabled = true;
            button.innerHTML = '<i class="bi bi-check-circle"></i>กำลังบันทึก...';
        });
    }
});
</script>
@endsection
