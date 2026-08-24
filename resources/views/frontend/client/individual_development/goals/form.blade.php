@extends('admin_client.admin_client')

@section('content')
@php
    $clientName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
    $isEdit = $mode === 'edit' && $goal;
    $selectedDomain = old('domain_id', $goal?->domain_id);
    $selectedIndicator = old('indicator_id', $goal?->indicator_id);
    $selectedPriority = old('priority', $goal?->priority ?? 'medium');
    $selectedStatus = old('status', $goal?->status ?? 'not_started');
    $selectedTarget = old('target_level', $goal?->target_level);
    $todayDate = now('Asia/Bangkok')->format('Y-m-d');
    $planStartDate = optional($plan->start_date)->format('Y-m-d') ?: $todayDate;
    $targetDateMin = $planStartDate > $todayDate ? $planStartDate : $todayDate;
@endphp

<style>
.idp-goal-form{--border:#e1e8f0;--text:#203249;--muted:#697b91;padding-bottom:1.5rem}
.idp-goal-form .gf-head,.idp-goal-form .gf-card{background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:0 6px 20px rgba(32,50,73,.045)}
.idp-goal-form .gf-head{padding:1rem 1.15rem;margin-bottom:1rem;display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;flex-wrap:wrap}
.idp-goal-form .gf-title{margin:0;font-size:1.12rem;font-weight:800;color:var(--text)}
.idp-goal-form .gf-sub{margin:.3rem 0 0;color:var(--muted);font-size:.86rem}
.idp-goal-form .gf-meta{display:flex;gap:.45rem;flex-wrap:wrap;margin-top:.65rem}.idp-goal-form .gf-pill{border:1px solid #dce6ef;border-radius:999px;background:#f8fbff;padding:.3rem .65rem;font-size:.78rem;color:#455a73}
.idp-goal-form .gf-card{overflow:hidden}.idp-goal-form .gf-card-head{padding:.85rem 1rem;background:#fbfcfe;border-bottom:1px solid var(--border);font-weight:800;color:var(--text)}.idp-goal-form .gf-card-body{padding:1rem}
.idp-goal-form .form-label{font-size:.82rem;font-weight:700;color:#42556c}.idp-goal-form .form-control,.idp-goal-form .form-select{border-radius:10px;border-color:#d9e2ec;min-height:42px}.idp-goal-form textarea.form-control{min-height:96px}
.idp-goal-form .gf-help{font-size:.74rem;color:#7a899b;margin-top:.25rem}.idp-goal-form .gf-baseline{height:100%;padding:.9rem;border-radius:12px;border:1px solid #d7e7f8;background:#f7fbff}.idp-goal-form .gf-baseline-label{font-size:.76rem;color:#66809a}.idp-goal-form .gf-baseline-value{font-size:1.18rem;font-weight:800;color:#245f9f;margin-top:.15rem;white-space:nowrap}.idp-goal-form .gf-baseline-text{font-size:.72rem;color:#60758d;margin-top:.2rem;line-height:1.25}
.idp-goal-form .gf-actions{display:flex;justify-content:flex-end;gap:.55rem;flex-wrap:wrap;margin-top:1rem}.idp-goal-form .btn{border-radius:10px;min-height:42px;font-weight:700;padding:.58rem .95rem}.idp-goal-form .btn-save{background:linear-gradient(135deg,#3577bd,#245f9f);border:0;color:#fff}.idp-goal-form .btn-save:hover{color:#fff}
@media(max-width:575.98px){.idp-goal-form .gf-actions{display:grid;grid-template-columns:1fr}.idp-goal-form .gf-actions .btn{width:100%}}

/* IDP_PHASE5_UI_STABLE_V1 */
.idp-goal-form{width:100%;min-width:0}
.idp-goal-form *{min-width:0}
.idp-goal-form .form-control,.idp-goal-form .form-select{max-width:100%}
.idp-goal-form textarea{overflow-wrap:anywhere}

</style>

<div class="container-fluid px-2 px-lg-3 idp-goal-form" data-idp-goal-guard="v1">
    <div class="gf-head">
        <div>
            <h4 class="gf-title"><i class="bi bi-bullseye me-2 text-primary"></i>{{ $isEdit ? 'แก้ไขเป้าหมายการพัฒนา' : 'กำหนดเป้าหมายการพัฒนา' }}</h4>
            <p class="gf-sub">กำหนดเป้าหมายที่วัดผลได้ เชื่อมกับ Baseline และระบุผู้รับผิดชอบอย่างชัดเจน</p>
            <div class="gf-meta">
                <span class="gf-pill">ผู้รับบริการ: <strong>{{ $clientName }}</strong></span>
                <span class="gf-pill">อายุ: <strong>{{ $ageText }}</strong></span>
                <span class="gf-pill">แผนครั้งที่: <strong>{{ $plan->plan_no }}</strong></span>
            </div>
        </div>
        <a href="{{ route('individual-development.goals.index', $client->id) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>กลับรายการเป้าหมาย</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <div class="fw-bold mb-1">กรุณาตรวจสอบข้อมูล</div>
            <ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form data-idp-th-validation="1" method="POST" action="{{ $isEdit ? route('individual-development.goals.update', [$client->id, $goal->id]) : route('individual-development.goals.store', $client->id) }}" id="goalForm">
        @csrf
        @if($isEdit) @method('PATCH') @endif

        <div class="gf-card mb-3">
            <div class="gf-card-head">1. ด้านพัฒนาการและระดับเป้าหมาย</div>
            <div class="gf-card-body">
                <div class="row g-3">
                    <div class="col-12 col-lg-4">
                        <label class="form-label">ด้านพัฒนาการ <span class="text-danger">*</span></label>
                        <select name="domain_id" id="domain_id" class="form-select" required>
                            <option value="">-- เลือกด้านพัฒนาการ --</option>
                            @foreach($domains as $domain)
                                <option value="{{ $domain->id }}" @selected((string)$selectedDomain === (string)$domain->id)>{{ $domain->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-5">
                        <label class="form-label">ตัวชี้วัดที่เกี่ยวข้อง</label>
                        <select name="indicator_id" id="indicator_id" class="form-select">
                            <option value="">-- เป้าหมายระดับด้าน / ไม่ระบุตัวชี้วัด --</option>
                            @foreach($domains as $domain)
                                @foreach($domain->indicators as $indicator)
                                    <option value="{{ $indicator->id }}" data-domain="{{ $domain->id }}" @selected((string)$selectedIndicator === (string)$indicator->id)>{{ $indicator->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                        <div class="gf-help">หากเป้าหมายเฉพาะเจาะจง แนะนำให้เลือกตัวชี้วัดเพื่อเชื่อมคะแนน Baseline ได้แม่นยำ</div>
                    </div>
                    <div class="col-6 col-lg-1">
                        <div class="gf-baseline">
                            <div class="gf-baseline-label">Baseline</div>
                            <div class="gf-baseline-value" id="baselineLevel">-</div>
                            <div class="gf-baseline-text" id="baselineText">เลือกด้าน/ตัวชี้วัด</div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <label class="form-label">ระดับเป้าหมาย <span class="text-danger">*</span></label>
                        <select name="target_level" id="target_level" class="form-select" required>
                            <option value="">-- เลือกระดับเป้าหมาย --</option>
                            @for($level=1;$level<=5;$level++)<option value="{{ $level }}" @selected((string)$selectedTarget === (string)$level)>ระดับ {{ $level }}</option>@endfor
                        </select>
                        <div class="gf-help" id="targetLevelHelp">เลือกระดับให้สูงกว่า Baseline</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="gf-card mb-3">
            <div class="gf-card-head">2. เป้าหมายและตัวชี้วัดความสำเร็จ</div>
            <div class="gf-card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">ชื่อเป้าหมาย <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" maxlength="500" value="{{ old('title', $goal?->title ?? ($prefillNeed ?? '')) }}" placeholder="เช่น สามารถควบคุมอารมณ์เมื่อเกิดความขัดแย้งได้อย่างเหมาะสม" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">รายละเอียด/ผลลัพธ์ที่คาดหวัง</label>
                        <textarea name="description" class="form-control" maxlength="10000" placeholder="อธิบายพฤติกรรมหรือผลลัพธ์ที่ต้องการให้เกิดขึ้น">{{ old('description', $goal?->description ?? ($prefillNeed ?? '')) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">ตัวชี้วัดความสำเร็จ <span class="text-danger">*</span></label>
                        <textarea name="success_indicator" class="form-control" maxlength="10000" placeholder="เช่น สามารถจัดการสถานการณ์โดยไม่ใช้ความรุนแรงอย่างน้อย 4 ใน 5 ครั้ง" required>{{ old('success_indicator', $goal?->success_indicator) }}</textarea>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label">วิธีวัดผล</label>
                        <input type="text" name="measurement_method" class="form-control" maxlength="500" value="{{ old('measurement_method', $goal?->measurement_method) }}" placeholder="เช่น การสังเกตพฤติกรรม / แบบประเมิน / บันทึกครู">
                    </div>
                    <div class="col-6 col-lg-3">
                        <label class="form-label">ค่าเป้าหมายเชิงปริมาณ</label>
                        <input type="number" step="0.01" min="0" name="target_value" class="form-control" value="{{ old('target_value', $goal?->target_value) }}" placeholder="เช่น 80">
                    </div>
                    <div class="col-6 col-lg-3">
                        <label class="form-label">หน่วย</label>
                        <input type="text" name="target_unit" maxlength="100" class="form-control" value="{{ old('target_unit', $goal?->target_unit) }}" placeholder="%, ครั้ง, คะแนน">
                    </div>
                </div>
            </div>
        </div>

        <div class="gf-card">
            <div class="gf-card-head">3. ระยะเวลา ผู้รับผิดชอบ และสถานะ</div>
            <div class="gf-card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label">กำหนดสำเร็จ</label>
                        <input type="date" name="target_date" class="form-control" min="{{ $targetDateMin }}" value="{{ old('target_date', optional($goal?->target_date)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">ระดับความสำคัญ <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select" required>
                            @foreach($priorityLabels as $value=>$label)<option value="{{ $value }}" @selected($selectedPriority===$value)>{{ $label }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">ผู้รับผิดชอบ</label>
                        <input type="text" name="responsible_name" maxlength="255" class="form-control" value="{{ old('responsible_name', $goal?->responsible_name) }}" placeholder="ชื่อเจ้าหน้าที่/ผู้ดูแล/ครู">
                    </div>
                    @if($isEdit)
                        <div class="col-12 col-md-4">
                            <label class="form-label">สถานะเป้าหมาย <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                @foreach($statusLabels as $value=>$label)<option value="{{ $value }}" @selected($selectedStatus===$value)>{{ $label }}</option>@endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="gf-actions">
            <a href="{{ route('individual-development.goals.index', $client->id) }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle me-1"></i>ยกเลิก</a>
            <button type="submit" class="btn btn-save"><i class="bi bi-check-circle me-1"></i>{{ $isEdit ? 'บันทึกการแก้ไข' : 'บันทึกเป้าหมาย' }}</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const domain = document.getElementById('domain_id');
    const indicator = document.getElementById('indicator_id');
    const baselineOutput = document.getElementById('baselineLevel');
    const baselineText = document.getElementById('baselineText');
    const target = document.getElementById('target_level');
    const targetHelp = document.getElementById('targetLevelHelp');
    const baselineLevels = @json($baselineLevels);
    const selectedIndicator = @json((string)$selectedIndicator);
    const selectedTarget = @json((string)$selectedTarget);
    const rubricLabels = {
        1: 'ต้องส่งเสริมเร่งด่วน',
        2: 'ควรส่งเสริม',
        3: 'ตามเกณฑ์',
        4: 'ดี',
        5: 'ดีมาก'
    };

    function targetAllowed(level, baseline) {
        if (!baseline) return true;
        if (baseline >= 5) return level === 5;
        return level > baseline;
    }

    function refreshTargetOptions(baseline) {
        Array.from(target.options).forEach(function (option) {
            if (!option.value) { option.disabled = false; return; }
            option.disabled = !targetAllowed(Number(option.value), baseline);
        });

        const current = Number(target.value || 0);
        if (baseline) {
            const recommended = baseline >= 5 ? 5 : Math.min(5, baseline + 1);
            if (!current || !targetAllowed(current, baseline)) {
                target.value = String(recommended);
            }
            targetHelp.textContent = baseline >= 5
                ? 'Baseline อยู่ระดับ 5 แล้ว ระบบคงระดับเป้าหมายไว้ที่ระดับ 5'
                : 'ระดับเป้าหมายต้องสูงกว่า Baseline ระดับ ' + baseline + ' (แนะนำเริ่มที่ระดับ ' + recommended + ')';
        } else {
            if (!selectedTarget) target.value = '';
            targetHelp.textContent = 'เลือกระดับให้สูงกว่า Baseline';
        }
    }

    function refreshIndicators() {
        const domainId = String(domain.value || '');
        Array.from(indicator.options).forEach(function (option, index) {
            if (index === 0) { option.hidden = false; return; }
            option.hidden = String(option.dataset.domain || '') !== domainId;
        });
        const selected = indicator.selectedOptions[0];
        if (selected && selected.hidden) indicator.value = '';
        refreshBaseline();
    }

    function refreshBaseline() {
        const indicatorId = String(indicator.value || '');
        const domainId = String(domain.value || '');
        const key = indicatorId ? 'indicator:' + indicatorId : 'domain:' + domainId;
        const rawValue = baselineLevels[key];
        const value = rawValue === null || rawValue === undefined || rawValue === '' ? null : Number(rawValue);

        if (value) {
            baselineOutput.textContent = 'ระดับ ' + value + ' / 5';
            baselineText.textContent = rubricLabels[value] || '';
        } else {
            baselineOutput.textContent = '-';
            baselineText.textContent = domainId ? 'ไม่พบคะแนน Baseline' : 'เลือกด้าน/ตัวชี้วัด';
        }

        refreshTargetOptions(value);
    }

    domain.addEventListener('change', refreshIndicators);
    indicator.addEventListener('change', refreshBaseline);
    refreshIndicators();
    if (selectedIndicator) {
        indicator.value = selectedIndicator;
        refreshBaseline();
    }
});
</script>
@include('frontend.client.individual_development.partials._thai_validation')
@endsection
