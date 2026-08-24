@extends(config('university_tracking.client_layout', 'admin_client.admin_client'))
@section('content')
<link rel="stylesheet" href="{{ asset('backend/assets/css/university-tracking.css') }}">
@php
$issueSeed = old(
    'issues',
    $followup->exists
        ? $followup->issues
            ->map(fn($item) => $item->only(['category','severity','detail','assistance','issue_status']))
            ->values()
            ->all()
        : []
);

$cats = config('university_tracking.issue_categories');
$risks = config('university_tracking.risk_levels');
$issueStatuses = config('university_tracking.issue_statuses');

$schoolFollowTypeLabels = [
    'self' => 'ติดตามด้วยตนเอง',
    'phone' => 'โทรศัพท์',
    'other' => 'อื่น ๆ',
];
@endphp

<div class="container-fluid ut-page">
<div class="ut-wrap py-3">

<div class="ut-card ut-header">
    <div class="ut-title-row">
        <div class="ut-icon"><i class="bi bi-person-check"></i></div>
        <div>
            <h1 class="ut-title">{{ $isEdit ? 'แก้ไขการติดตามเด็กมหาวิทยาลัย' : 'ติดตามเด็กมหาวิทยาลัย' }} · ครั้งที่ {{ $nextSequence }}</h1>
            <p class="ut-subtitle">
                @include('university.partials.client_name',['client'=>$client])
                · ภาคเรียน {{ $record->term }}/{{ $record->academic_year }}
                · ปีที่ {{ $record->year_level }}
            </p>
        </div>
    </div>
    <div class="ut-actions">
        <a class="ut-btn ut-btn-light" href="{{ route('university.semesters.show',$record->id) }}">
            <i class="bi bi-x-lg"></i> ปิด
        </a>
    </div>
</div>

@include('university.partials.validation')

<div class="ut-card ut-content mb-3">
    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-2">
        <h2 class="ut-section-title mb-0">
            <i class="bi bi-journal-check"></i> ข้อมูลภาคเรียน
        </h2>
        <span class="ut-badge ut-badge-gray">อ้างอิงข้อมูลเดิม · ไม่กรอกซ้ำ</span>
    </div>

    <div class="ut-info-list">
        <div class="ut-info">
            <div class="ut-info-label">ภาคเรียน</div>
            <div class="ut-info-value">{{ data_get($record,'educationRecord.semester.semester_name') ?: ($record->term.'/'.$record->academic_year) }}</div>
        </div>
        <div class="ut-info">
            <div class="ut-info-label">ระดับการศึกษา</div>
            <div class="ut-info-value">{{ data_get($record,'educationRecord.education.education_name','-') }}</div>
        </div>
        <div class="ut-info">
            <div class="ut-info-label">สถานศึกษา</div>
            <div class="ut-info-value">{{ data_get($record,'educationRecord.school_name') ?: $enrollment->university_name }}</div>
        </div>
    </div>
</div>

<div class="ut-card ut-content mb-3">
    <div class="d-flex justify-content-between gap-2 flex-wrap align-items-center mb-2">
        <h2 class="ut-section-title mb-0">
            <i class="bi bi-telephone"></i> ข้อมูลจาก School Followup
        </h2>
        <span class="ut-badge ut-badge-gray">แสดงอย่างเดียว · ไม่กรอกซ้ำ</span>
    </div>

    <div class="ut-help mb-2">
        ผู้ติดต่อ โทรศัพท์ วิธีติดตาม ผลการติดต่อ และหมายเหตุ บันทึกที่หน้า School Followup เพียงแห่งเดียว
        หน้านี้ใช้ติดตามเฉพาะสถานการณ์ของเด็กมหาวิทยาลัย
    </div>

    <div class="ut-timeline">
        @forelse($schoolFollowups as $schoolFollowup)
            <div class="ut-timeline-item">
                <div class="ut-timeline-title">
                    {{ $schoolFollowup->follow_date ? \Carbon\Carbon::parse($schoolFollowup->follow_date)->format('d/m/Y') : '-' }}
                    · {{ $schoolFollowTypeLabels[$schoolFollowup->follow_type] ?? ($schoolFollowup->follow_type ?: '-') }}
                </div>
                <div class="ut-timeline-text">
                    {{ $schoolFollowup->result ?: '-' }}
                    @if($schoolFollowup->teacher_name)
                        · ผู้ติดต่อ {{ $schoolFollowup->teacher_name }}
                    @endif
                </div>
            </div>
        @empty
            <div class="ut-empty"><p>ยังไม่มี School Followup ในภาคเรียนนี้</p></div>
        @endforelse
    </div>
</div>

<form method="POST"
      action="{{ $isEdit ? route('university.followups.update',$followup->id) : route('university.followups.store',$record->id) }}"
      class="ut-card ut-content">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="ut-section">
        <h2 class="ut-section-title">
            <i class="bi bi-calendar-check"></i> ข้อมูลการติดตาม
        </h2>

        <div class="ut-grid">
            <div class="ut-field ut-col-3">
                <label>ครั้งที่</label>
                <input class="form-control" value="{{ $nextSequence }}" readonly>
                <div class="ut-help">ระบบกำหนดอัตโนมัติแยกตามภาคเรียน</div>
            </div>

            <div class="ut-field ut-col-3">
                <label>วันที่ติดตาม <span class="ut-required">*</span></label>
                <input type="date"
                       class="form-control"
                       name="followup_date"
                       required
                       @if($minimumDate) min="{{ $minimumDate }}" @endif
                       max="{{ now('Asia/Bangkok')->toDateString() }}"
                       value="{{ old('followup_date', optional($followup->followup_date)->format('Y-m-d') ?: now('Asia/Bangkok')->toDateString()) }}">
                <div class="ut-help">ภาคเรียนเดียวกัน ครั้งถัดไปต้องมีวันที่มากกว่าครั้งก่อน</div>
            </div>

            <div class="ut-field ut-col-6">
                <label>ระดับความเสี่ยงโดยรวม <span class="ut-required">*</span></label>
                <select class="form-select" name="overall_risk_level" required>
                    @foreach($risks as $key => $label)
                        <option value="{{ $key }}"
                            @selected(old('overall_risk_level',$followup->overall_risk_level ?: 'normal') === $key)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="ut-divider"></div>

    <div class="ut-section">
        <h2 class="ut-section-title">
            <i class="bi bi-graph-up-arrow"></i> ประเด็นติดตามเฉพาะเด็กมหาวิทยาลัย
        </h2>
        <div class="ut-help mb-3">
            ส่วนนี้ไม่เก็บข้อมูลผู้ติดต่อหรือวิธีติดต่อซ้ำกับ School Followup
        </div>

        <div class="ut-grid">
            <div class="ut-field ut-col-6">
                <label>การเรียนและความก้าวหน้า</label>
                <textarea class="form-control"
                          name="academic_progress"
                          placeholder="เช่น การเข้าเรียน ความเข้าใจรายวิชา งานค้าง ผลการเรียน แนวโน้มเรียนตามแผน">{{ old('academic_progress',$followup->academic_progress) }}</textarea>
            </div>

            <div class="ut-field ut-col-6">
                <label>การปรับตัว</label>
                <textarea class="form-control"
                          name="adaptation_status"
                          placeholder="เช่น การปรับตัวกับมหาวิทยาลัย เพื่อน อาจารย์ การใช้ชีวิต และสภาพแวดล้อม">{{ old('adaptation_status',$followup->adaptation_status) }}</textarea>
            </div>

            <div class="ut-field ut-col-6">
                <label>การเงิน / ทุน / ภาระงาน</label>
                <textarea class="form-control"
                          name="financial_status"
                          placeholder="เช่น ค่าเทอม ค่าใช้จ่าย ทุนการศึกษา การทำงานระหว่างเรียน และภาระที่กระทบการเรียน">{{ old('financial_status',$followup->financial_status) }}</textarea>
            </div>

            <div class="ut-field ut-col-6">
                <label>สุขภาวะและแรงจูงใจ</label>
                <textarea class="form-control"
                          name="wellbeing_motivation"
                          placeholder="เช่น สุขภาพ ความเครียด กำลังใจ แรงจูงใจ และเป้าหมายเรียนให้จบ">{{ old('wellbeing_motivation',$followup->wellbeing_motivation) }}</textarea>
            </div>

            <div class="ut-field ut-col-12">
                <label>ความเสี่ยงต่อการเรียนต่อ / ออกกลางคัน</label>
                <textarea class="form-control"
                          name="continuation_risk_note"
                          placeholder="เช่น มีแนวคิดพักเรียน ลาออก เปลี่ยนสาขา ผลการเรียนต่ำต่อเนื่อง หรือมีปัจจัยที่อาจทำให้เรียนไม่ต่อเนื่อง">{{ old('continuation_risk_note',$followup->continuation_risk_note) }}</textarea>
            </div>

            <div class="ut-field ut-col-6">
                <label>จุดแข็ง / ปัจจัยคุ้มครอง</label>
                <textarea class="form-control"
                          name="strengths"
                          placeholder="สิ่งที่เด็กทำได้ดี แหล่งสนับสนุน บุคคลสำคัญ หรือปัจจัยที่ช่วยให้เรียนต่อได้">{{ old('strengths',$followup->strengths) }}</textarea>
            </div>

            <div class="ut-field ut-col-6">
                <label>การช่วยเหลือ / ข้อเสนอแนะ</label>
                <textarea class="form-control"
                          name="assistance_summary"
                          placeholder="การช่วยเหลือที่ดำเนินการหรือข้อเสนอแนะในครั้งนี้">{{ old('assistance_summary',$followup->assistance_summary) }}</textarea>
            </div>

            <div class="ut-field ut-col-12">
                <label>สิ่งที่ต้องดำเนินการต่อ</label>
                <textarea class="form-control"
                          name="next_plan"
                          placeholder="งานที่ต้องทำต่อ ผู้รับผิดชอบ หรือประเด็นที่ต้องกลับมาติดตามครั้งหน้า">{{ old('next_plan',$followup->next_plan) }}</textarea>
            </div>
        </div>
    </div>

    <div class="ut-divider"></div>

    <div class="ut-section">
        <div class="d-flex justify-content-between gap-2 flex-wrap align-items-center mb-2">
            <div>
                <h2 class="ut-section-title mb-1">
                    <i class="bi bi-exclamation-diamond"></i> ประเด็นสำคัญที่ต้องติดตาม
                </h2>
                <div class="ut-help">
                    เพิ่มเฉพาะประเด็นที่ต้องติดตามเป็นพิเศษเพื่อใช้ทำสถิติ ไม่จำเป็นต้องเพิ่มทุกครั้ง
                </div>
            </div>

            <button type="button" class="ut-btn ut-btn-light" id="addIssue">
                <i class="bi bi-plus-circle"></i> เพิ่มประเด็น
            </button>
        </div>

        <div id="issueRows" class="mt-3"></div>
    </div>

    <div class="ut-actions justify-content-end">
        <button class="ut-btn ut-btn-primary">
            <i class="bi bi-floppy"></i>
            บันทึกการติดตามครั้งที่ {{ $nextSequence }}
        </button>
    </div>
</form>

@if($isEdit && $universityPermissions['delete'])
<form method="POST"
      action="{{ route('university.followups.destroy',$followup->id) }}"
      class="ut-delete-form mt-3"
      data-message="ลบการติดตามครั้งที่ {{ $nextSequence }} หรือไม่? ระบบจะจัดลำดับครั้งที่เหลือใหม่อัตโนมัติ">
    @csrf
    @method('DELETE')
    <button class="ut-btn ut-btn-danger">
        <i class="bi bi-trash"></i> ลบการติดตามครั้งนี้
    </button>
</form>
@endif

</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('issueRows');
    const add = document.getElementById('addIssue');

    let rows = @json($issueSeed);
    const cats = @json($cats);
    const risks = @json($risks);
    const statuses = @json($issueStatuses);

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];
        });
    }

    function options(items, selected) {
        return Object.entries(items).map(function ([key, label]) {
            return `<option value="${escapeHtml(key)}" ${String(selected || '') === key ? 'selected' : ''}>${escapeHtml(label)}</option>`;
        }).join('');
    }

    function collectRows() {
        const output = [];

        root.querySelectorAll('.ut-issue-row').forEach(function (row) {
            const getValue = function (field) {
                return row.querySelector(`[data-field="${field}"]`)?.value ?? '';
            };

            output.push({
                category: getValue('category'),
                severity: getValue('severity'),
                issue_status: getValue('issue_status'),
                detail: getValue('detail'),
                assistance: getValue('assistance')
            });
        });

        rows = output;
    }

    function renderRows() {
        root.innerHTML = '';

        rows.forEach(function (row, index) {
            const wrapper = document.createElement('div');
            wrapper.className = 'ut-issue-row';

            wrapper.innerHTML = `
                <button type="button" class="ut-row-remove" data-remove="${index}">
                    <i class="bi bi-x"></i>
                </button>

                <div class="ut-grid">
                    <div class="ut-field ut-col-4">
                        <label>หมวดประเด็น</label>
                        <select class="form-select"
                                data-field="category"
                                name="issues[${index}][category]">
                            ${options(cats, row.category || 'academic')}
                        </select>
                    </div>

                    <div class="ut-field ut-col-4">
                        <label>ระดับ</label>
                        <select class="form-select"
                                data-field="severity"
                                name="issues[${index}][severity]">
                            ${options(risks, row.severity || 'watch')}
                        </select>
                    </div>

                    <div class="ut-field ut-col-4">
                        <label>สถานะ</label>
                        <select class="form-select"
                                data-field="issue_status"
                                name="issues[${index}][issue_status]">
                            ${options(statuses, row.issue_status || 'open')}
                        </select>
                    </div>

                    <div class="ut-field ut-col-6">
                        <label>รายละเอียด</label>
                        <textarea class="form-control"
                                  data-field="detail"
                                  name="issues[${index}][detail]">${escapeHtml(row.detail)}</textarea>
                    </div>

                    <div class="ut-field ut-col-6">
                        <label>การช่วยเหลือเฉพาะประเด็น</label>
                        <textarea class="form-control"
                                  data-field="assistance"
                                  name="issues[${index}][assistance]">${escapeHtml(row.assistance)}</textarea>
                    </div>
                </div>
            `;

            root.appendChild(wrapper);
        });

        root.querySelectorAll('[data-remove]').forEach(function (button) {
            button.addEventListener('click', function () {
                collectRows();
                rows.splice(Number(button.dataset.remove), 1);
                renderRows();
            });
        });
    }

    if (!Array.isArray(rows)) {
        rows = [];
    }

    renderRows();

    add?.addEventListener('click', function () {
        collectRows();
        rows.push({
            category: 'academic',
            severity: 'watch',
            issue_status: 'open',
            detail: '',
            assistance: ''
        });
        renderRows();
    });
});
</script>

@include('university.partials.delete_confirm')
@endsection
