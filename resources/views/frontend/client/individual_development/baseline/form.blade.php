@extends('admin_client.admin_client')

@section('content')
@php
    $isEdit = $mode === 'edit';
    $clientName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
    $assessmentItems = ($assessment?->items ?? collect())->keyBy('indicator_id');
    $selectedSources = old('information_sources', $assessment?->information_sources ?? []);
    $domainIcons = [
        'physical' => 'bi-heart-pulse',
        'emotional' => 'bi-emoji-smile',
        'social' => 'bi-people',
        'intellectual' => 'bi-lightbulb',
    ];
@endphp

<style>
.idp-baseline{--idp-border:#e2e9f1;--idp-text:#203049;--idp-muted:#6c7c91;padding-bottom:1.5rem}
.idp-baseline .idp-header,.idp-baseline .idp-card,.idp-baseline .idp-domain{background:#fff;border:1px solid var(--idp-border);border-radius:16px;box-shadow:0 6px 20px rgba(31,47,70,.045)}
.idp-baseline .idp-header{padding:1.05rem 1.2rem;margin-bottom:1rem}
.idp-baseline .idp-title{margin:0;color:var(--idp-text);font-size:1.15rem;font-weight:800}
.idp-baseline .idp-subtitle{margin:.3rem 0 0;color:var(--idp-muted);font-size:.87rem;line-height:1.6}
.idp-baseline .idp-meta{display:flex;flex-wrap:wrap;gap:.45rem;margin-top:.75rem}
.idp-baseline .idp-pill{display:inline-flex;align-items:center;gap:.35rem;padding:.34rem .68rem;border:1px solid #dbe5f0;border-radius:999px;background:#f8fbff;color:#41546d;font-size:.8rem}
.idp-baseline .idp-progress-strip{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.8rem 1rem;margin-bottom:1rem;background:#f8fbff;border:1px solid #dfe9f5;border-radius:13px;color:#40536c;font-size:.82rem}
.idp-baseline .idp-progress-strip strong{color:#245f9f}
.idp-baseline .idp-card{overflow:hidden;margin-bottom:1rem}
.idp-baseline .idp-section-head{padding:.9rem 1rem;border-bottom:1px solid var(--idp-border);background:#fbfcfe;color:var(--idp-text);font-size:.94rem;font-weight:800}
.idp-baseline .idp-body{padding:1rem}
.idp-baseline .form-label{color:#35465c;font-size:.83rem;font-weight:700}
.idp-baseline .form-control{min-height:42px;border-color:#d9e2ec;border-radius:10px;font-size:.86rem}
.idp-baseline textarea.form-control{min-height:92px;resize:vertical}
.idp-baseline .idp-source-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.55rem}
.idp-baseline .idp-check{display:flex;align-items:center;gap:.5rem;padding:.65rem .75rem;border:1px solid #e1e7ef;border-radius:10px;background:#fff;color:#43546a;font-size:.82rem}
.idp-baseline .idp-domain{overflow:hidden;margin-bottom:1rem}
.idp-baseline .idp-domain-head{display:flex;align-items:center;justify-content:space-between;gap:.8rem;padding:.9rem 1rem;background:#f8fbff;border-bottom:1px solid var(--idp-border)}
.idp-baseline .idp-domain-name{display:flex;align-items:center;gap:.55rem;color:var(--idp-text);font-size:.98rem;font-weight:800}
.idp-baseline .idp-domain-icon{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:10px;background:#edf5ff;color:#376fae}
.idp-baseline .idp-domain-score{white-space:nowrap;color:#53657c;font-size:.8rem}
.idp-baseline .idp-domain-score strong{color:#245f9f;font-size:1rem}
.idp-baseline .idp-indicator{padding:1rem;border-bottom:1px solid #edf1f5}
.idp-baseline .idp-indicator:last-child{border-bottom:0}
.idp-baseline .idp-indicator-title{color:#24364f;font-size:.9rem;font-weight:800}
.idp-baseline .idp-indicator-desc{margin:.25rem 0 .7rem;color:var(--idp-muted);font-size:.78rem;line-height:1.55}
.idp-baseline .idp-rubrics{display:grid;gap:.45rem}
.idp-baseline .idp-rubric{position:relative;display:grid;grid-template-columns:38px 1fr;gap:.6rem;padding:.65rem .72rem;border:1px solid #e1e7ef;border-radius:11px;background:#fff;cursor:pointer;transition:.15s ease}
.idp-baseline .idp-rubric:hover{border-color:#b7cce4;background:#fbfdff}
.idp-baseline .idp-rubric input{position:absolute;opacity:0;pointer-events:none}
.idp-baseline .idp-rubric:has(input:checked){border-color:#6fa2d8;background:#f2f8ff;box-shadow:0 0 0 2px rgba(76,132,190,.08)}
.idp-baseline .idp-level{display:flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:9px;background:#eef3f8;color:#345776;font-weight:800}
.idp-baseline .idp-rubric:has(input:checked) .idp-level{background:#3577bd;color:#fff}
.idp-baseline .idp-rubric-title{color:#35475e;font-size:.8rem;font-weight:800}
.idp-baseline .idp-rubric-desc{margin-top:.12rem;color:#66778c;font-size:.76rem;line-height:1.5}
.idp-baseline .idp-evidence{margin-top:.75rem;padding:.75rem;background:#fafbfd;border:1px dashed #dce4ed;border-radius:11px}
.idp-baseline .idp-help{color:#7b899b;font-size:.74rem;line-height:1.5;margin-top:.25rem}
.idp-baseline .idp-actions{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:.55rem;padding:1rem;background:#fbfcfe;border-top:1px solid var(--idp-border)}
.idp-baseline .idp-btn{display:inline-flex;align-items:center;justify-content:center;gap:.4rem;min-height:40px;padding:.5rem .9rem;border-radius:10px;font-size:.83rem;font-weight:700;text-decoration:none!important}
.idp-baseline .idp-btn-primary{border:0;background:linear-gradient(135deg,#3577bd 0%,#245f9f 100%);color:#fff;box-shadow:0 6px 14px rgba(36,95,159,.16)}
.idp-baseline .idp-btn-light{border:1px solid #d7e0e9;background:#fff;color:#4f6075}
.idp-baseline .idp-readonly{display:inline-flex;align-items:center;gap:.35rem;padding:.32rem .65rem;border-radius:999px;background:#f2f4f7;color:#667085;font-size:.76rem;font-weight:700}
@media(max-width:991.98px){.idp-baseline .idp-source-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:575.98px){.idp-baseline .idp-source-grid{grid-template-columns:1fr}.idp-baseline .idp-progress-strip{align-items:flex-start;flex-direction:column}.idp-baseline .idp-domain-head{align-items:flex-start;flex-direction:column}.idp-baseline .idp-actions{flex-direction:column}.idp-baseline .idp-btn{width:100%}.idp-baseline .idp-rubric{grid-template-columns:34px 1fr}}

/* IDP_PHASE5_UI_STABLE_V1 */
.idp-baseline{width:100%;min-width:0}
.idp-baseline *{min-width:0}
.idp-baseline .form-control,.idp-baseline .form-select{max-width:100%}
.idp-baseline .idp-rubric-text,.idp-baseline textarea{overflow-wrap:anywhere}
@media(max-width:575.98px){
  .idp-baseline .idp-actions{display:grid!important;grid-template-columns:1fr!important;width:100%}
  .idp-baseline .idp-actions .btn{width:100%}
}

</style>

<div class="container-fluid px-2 px-lg-3 idp-baseline">
    <div class="idp-header">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-2">
            <div>
                <h4 class="idp-title"><i class="bi bi-clipboard2-pulse me-2 text-primary"></i>{{ $isEdit ? ($readOnly ? 'ดูผลประเมิน Baseline' : 'แก้ไขผลประเมิน Baseline') : 'ประเมินระดับเริ่มต้น (Baseline)' }}</h4>
                <p class="idp-subtitle">ประเมินพัฒนาการ 4 ด้านจาก 20 ตัวชี้วัด โดยเลือกเกณฑ์ Rubric ที่ตรงกับพฤติกรรมตามสภาพจริงมากที่สุด</p>
                <div class="idp-meta">
                    <span class="idp-pill"><i class="bi bi-person"></i>ผู้รับบริการ: <strong>{{ $clientName }}</strong></span>
                    <span class="idp-pill"><i class="bi bi-calendar3"></i>อายุ: <strong>{{ $ageText }}</strong></span>
                    <span class="idp-pill"><i class="bi bi-list-ol"></i>แผนครั้งที่: <strong>{{ $plan->plan_no }}</strong></span>
                </div>
            </div>
            @if($readOnly)<div><span class="idp-readonly"><i class="bi bi-eye"></i>โหมดอ่านอย่างเดียว</span></div>@endif
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3">
            <div class="fw-bold mb-1">กรุณาตรวจสอบข้อมูล</div>
            <ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="idp-progress-strip">
        <span><i class="bi bi-check2-circle me-1"></i>ประเมินแล้ว <strong id="completedIndicatorCount">0</strong> / <strong>{{ $domains->sum(fn($domain) => $domain->indicators->count()) }}</strong> ตัวชี้วัด</span>
        <span>ระบบคำนวณค่าเฉลี่ยรายด้านอัตโนมัติ แต่ผู้ประเมินเป็นผู้ยืนยันระดับของแต่ละตัวชี้วัด</span>
    </div>

    <form method="POST" action="{{ $isEdit ? route('individual-development.baseline.update', $client->id) : route('individual-development.baseline.store', $client->id) }}" id="baselineAssessmentForm">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <fieldset {{ $readOnly ? 'disabled' : '' }}>
            <div class="idp-card">
                <div class="idp-section-head"><i class="bi bi-person-check me-1"></i>ข้อมูลการประเมินและแหล่งข้อมูล</div>
                <div class="idp-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">วันที่ประเมิน <span class="text-danger">*</span></label>
                            <input type="date" name="assessment_date" class="form-control @error('assessment_date') is-invalid @enderror" max="{{ now('Asia/Bangkok')->format('Y-m-d') }}" value="{{ old('assessment_date', $assessment?->assessment_date?->format('Y-m-d') ?? now('Asia/Bangkok')->format('Y-m-d')) }}" required>
                            @error('assessment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">แหล่งข้อมูล/ผู้ร่วมให้ข้อมูล <span class="text-danger">*</span></label>
                            <div class="idp-source-grid">
                                @foreach($informationSourceOptions as $sourceKey => $sourceLabel)
                                    <label class="idp-check">
                                        <input type="checkbox" class="form-check-input mt-0" name="information_sources[]" value="{{ $sourceKey }}" {{ in_array($sourceKey, $selectedSources, true) ? 'checked' : '' }}>
                                        <span>{{ $sourceLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('information_sources')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">หมายเหตุเกี่ยวกับผู้ร่วมประเมิน/แหล่งข้อมูล</label>
                            <textarea name="participant_note" class="form-control" placeholder="เช่น ครูประจำชั้นให้ข้อมูลการเข้าเรียน ผู้ดูแลให้ข้อมูลพฤติกรรมในบ้านพัก">{{ old('participant_note', $assessment?->participant_note) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">สรุปสถานการณ์ปัจจุบัน</label>
                            <textarea name="overall_note" class="form-control" placeholder="สรุปภาพรวมที่สำคัญ ณ วันที่ประเมิน โดยยึดข้อมูลและพฤติกรรมที่ตรวจสอบได้">{{ old('overall_note', $assessment?->overall_note) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="idp-card">
                <div class="idp-section-head"><i class="bi bi-shield-check me-1"></i>บริบทสำหรับวางแผนพัฒนารายบุคคล</div>
                <div class="idp-body">
                    <div class="row g-3">
                        @foreach([
                            'strength_summary' => ['จุดแข็งของผู้รับบริการ','ความสามารถ ความสนใจ พฤติกรรมเชิงบวก หรือทรัพยากรที่เป็นทุนเดิม'],
                            'development_need_summary' => ['ประเด็นที่ควรส่งเสริม/พัฒนา','ประเด็นสำคัญที่ควรนำไปกำหนดเป้าหมายหลังประเมิน Baseline'],
                            'client_need_summary' => ['ความต้องการของผู้รับบริการ','สิ่งที่ผู้รับบริการบอกว่าต้องการหรือเห็นว่าสำคัญ'],
                            'caregiver_need_summary' => ['ความต้องการของผู้ดูแล/ครอบครัว','ข้อเสนอหรือความคาดหวังจากผู้ดูแล/ครอบครัว'],
                            'risk_factor_summary' => ['ปัจจัยเสี่ยง','ปัจจัยที่อาจขัดขวางการพัฒนา หรือควรได้รับการเฝ้าระวัง'],
                            'protective_factor_summary' => ['ปัจจัยคุ้มครอง','บุคคล ความสามารถ สภาพแวดล้อม หรือเงื่อนไขที่ช่วยสนับสนุน'],
                        ] as $field => [$label,$hint])
                            <div class="col-md-6">
                                <label class="form-label">{{ $label }}</label>
                                <textarea name="{{ $field }}" class="form-control" placeholder="{{ $hint }}">{{ old($field, $plan->{$field}) }}</textarea>
                            </div>
                        @endforeach
                        <div class="col-12">
                            <label class="form-label">เครือข่าย/ทรัพยากรสนับสนุน</label>
                            <textarea name="support_network_summary" class="form-control" placeholder="เช่น ครอบครัว โรงเรียน หน่วยบริการสุขภาพ ชุมชน องค์กร หรือบุคคลที่สามารถสนับสนุนแผน">{{ old('support_network_summary', $plan->support_network_summary) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            @foreach($domains as $domain)
                <section class="idp-domain" data-domain-id="{{ $domain->id }}">
                    <div class="idp-domain-head">
                        <div class="idp-domain-name">
                            <span class="idp-domain-icon"><i class="bi {{ $domainIcons[$domain->code] ?? 'bi-clipboard-data' }}"></i></span>
                            <span>{{ $loop->iteration }}. {{ $domain->name }}</span>
                        </div>
                        <div class="idp-domain-score">ค่าเฉลี่ย: <strong class="domain-average">-</strong> / 5 <span class="domain-level ms-1"></span></div>
                    </div>

                    @foreach($domain->indicators as $indicator)
                        @php
                            $existingItem = $assessmentItems->get($indicator->id);
                            $selectedScore = old('items.'.$indicator->id.'.score', $existingItem?->score);
                        @endphp
                        <div class="idp-indicator" data-indicator-id="{{ $indicator->id }}">
                            <div class="idp-indicator-title">{{ $loop->iteration }}. {{ $indicator->name }} <span class="text-danger">*</span></div>
                            @if($indicator->description)<div class="idp-indicator-desc">{{ $indicator->description }}</div>@endif

                            <div class="idp-rubrics">
                                @foreach($indicator->rubrics as $rubric)
                                    <label class="idp-rubric">
                                        <input type="radio" name="items[{{ $indicator->id }}][score]" value="{{ $rubric->level }}" {{ (string)$selectedScore === (string)$rubric->level ? 'checked' : '' }} required>
                                        <span class="idp-level">{{ $rubric->level }}</span>
                                        <span>
                                            <span class="idp-rubric-title">{{ $rubric->title ?: 'ระดับ '.$rubric->level }}</span>
                                            <span class="idp-rubric-desc d-block">{{ $rubric->description }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            @error('items.'.$indicator->id.'.score')<div class="text-danger small mt-2">{{ $message }}</div>@enderror

                            <div class="idp-evidence">
                                <div class="row g-2">
                                    <div class="col-md-7">
                                        <label class="form-label">หลักฐาน/พฤติกรรมที่สังเกตพบ</label>
                                        <textarea name="items[{{ $indicator->id }}][evidence]" class="form-control" placeholder="บันทึกพฤติกรรม เหตุการณ์ หรือข้อมูลที่ใช้ประกอบการเลือกระดับ">{{ old('items.'.$indicator->id.'.evidence', $existingItem?->evidence) }}</textarea>
                                        <div class="idp-help">แนะนำให้บันทึกเมื่อเลือกระดับ 1–2 หรือมีเหตุการณ์สำคัญ เพื่อให้การติดตามครั้งต่อไปเปรียบเทียบได้ชัดเจน</div>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">ข้อสังเกต/ประเด็นที่ควรพัฒนา</label>
                                        <textarea name="items[{{ $indicator->id }}][development_note]" class="form-control" placeholder="สิ่งที่ควรส่งเสริม ติดตาม หรือพัฒนาต่อ">{{ old('items.'.$indicator->id.'.development_note', $existingItem?->development_note) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </section>
            @endforeach
        </fieldset>

        <div class="idp-card">
            <div class="idp-actions">
                <a href="{{ $assessment ? route('individual-development.baseline.show', $client->id) : route('individual-development.index', $client->id) }}" class="idp-btn idp-btn-light"><i class="bi bi-arrow-left"></i>{{ $readOnly ? 'กลับ' : 'ยกเลิก' }}</a>
                @unless($readOnly)
                    <button type="submit" class="idp-btn idp-btn-primary" id="saveBaselineButton"><i class="bi bi-check-circle"></i>{{ $isEdit ? 'บันทึกการแก้ไข' : 'บันทึก Baseline' }}</button>
                @endunless
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('baselineAssessmentForm');
    const saveButton = document.getElementById('saveBaselineButton');
    const countEl = document.getElementById('completedIndicatorCount');

    function scoreLabel(score) {
        if (score < 1.5) return 'ต้องส่งเสริมเร่งด่วน';
        if (score < 2.5) return 'ควรส่งเสริม';
        if (score < 3.5) return 'ตามเกณฑ์';
        if (score < 4.5) return 'ดี';
        return 'ดีมาก';
    }

    function refreshScores() {
        const checked = document.querySelectorAll('.idp-indicator input[type="radio"]:checked');
        if (countEl) countEl.textContent = checked.length;

        document.querySelectorAll('.idp-domain').forEach(function (domain) {
            const values = Array.from(domain.querySelectorAll('.idp-indicator input[type="radio"]:checked')).map(function (input) { return Number(input.value); });
            const averageEl = domain.querySelector('.domain-average');
            const levelEl = domain.querySelector('.domain-level');
            if (!values.length) {
                if (averageEl) averageEl.textContent = '-';
                if (levelEl) levelEl.textContent = '';
                return;
            }
            const avg = values.reduce(function (sum, value) { return sum + value; }, 0) / values.length;
            if (averageEl) averageEl.textContent = avg.toFixed(2);
            if (levelEl) levelEl.textContent = '• ' + scoreLabel(avg);
        });
    }

    document.querySelectorAll('.idp-indicator input[type="radio"]').forEach(function (input) {
        input.addEventListener('change', refreshScores);
    });
    refreshScores();

    if (form && saveButton) {
        form.addEventListener('submit', function () {
            if (saveButton.disabled) return;
            saveButton.disabled = true;
            saveButton.innerHTML = '<i class="bi bi-check-circle"></i>กำลังบันทึก...';
        });
    }
});
</script>
@endsection
