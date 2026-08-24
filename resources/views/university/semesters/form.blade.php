@extends(config('university_tracking.client_layout', 'admin_client.admin_client'))
@section('content')
<link rel="stylesheet" href="{{ asset('backend/assets/css/university-tracking.css') }}">
<div class="container-fluid ut-page"><div class="ut-wrap py-3">
<div class="ut-card ut-header">
    <div class="ut-title-row">
        <div class="ut-icon"><i class="bi bi-journal-plus"></i></div>
        <div>
            <h1 class="ut-title">{{ $isEdit ? 'แก้ไขข้อมูลภาคเรียน' : 'เพิ่มข้อมูลภาคเรียนมหาวิทยาลัย' }}</h1>
            <p class="ut-subtitle">@include('university.partials.client_name',['client'=>$client])</p>
        </div>
    </div>
    <div class="ut-actions">
        <a href="{{ $isEdit ? route('university.semesters.show',$record->id) : route('university.enrollments.show',$enrollment->id) }}" class="ut-btn ut-btn-light"><i class="bi bi-x-lg"></i> ปิด</a>
    </div>
</div>
@include('university.partials.validation')

<div class="ut-card ut-content mb-3">
    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-2">
        <h2 class="ut-section-title mb-0"><i class="bi bi-journal-check"></i> ข้อมูลภาคเรียนอ้างอิง</h2>
        <span class="ut-badge ut-badge-gray">{{ $isEdit ? 'คงข้อมูลอ้างอิงเดิม' : 'ดึงจาก Education Record ปัจจุบันอัตโนมัติ' }}</span>
    </div>
    <div class="ut-info-list">
        <div class="ut-info">
            <div class="ut-info-label">มหาวิทยาลัย / สถานศึกษา</div>
            <div class="ut-info-value">{{ data_get($currentEducationRecord,'school_name',$enrollment->university_name ?: '-') }}</div>
        </div>
        <div class="ut-info">
            <div class="ut-info-label">ภาคเรียนปัจจุบัน</div>
            <div class="ut-info-value">{{ $semesterLabel ?: '-' }}</div>
        </div>
        <div class="ut-info">
            <div class="ut-info-label">ระดับการศึกษา</div>
            <div class="ut-info-value">{{ data_get($currentEducationRecord,'education.education_name','-') }}</div>
        </div>
    </div>
    @if(!$isEdit)
        <div class="ut-help mt-2">หากขึ้นภาคเรียนใหม่ ให้บันทึกภาคเรียนใหม่ที่หน้า Education Record ก่อน หน้านี้จะดึงภาคเรียนล่าสุดมาให้เองและไม่เปิดให้เลือกซ้ำ</div>
    @elseif(!$currentEducationRecord)
        <div class="ut-help mt-2">รายการเก่านี้ไม่ได้เชื่อม Education Record ระบบจึงคงปี/ภาคเดิมไว้และไม่เปลี่ยนย้อนหลังอัตโนมัติ</div>
    @endif
</div>

<form method="POST" action="{{ $isEdit ? route('university.semesters.update',$record->id) : route('university.semesters.store',$enrollment->id) }}" class="ut-card ut-content" id="semesterForm">
@csrf
@if($isEdit) @method('PUT') @endif
@if($currentEducationRecord)
    <input type="hidden" name="education_record_id" value="{{ $currentEducationRecord->id }}">
@endif
<div id="deletedSubjectInputs"></div>

<div class="ut-section">
    <h2 class="ut-section-title"><i class="bi bi-graph-up"></i> ผลการเรียนและสถานะ</h2>
    <div class="ut-grid">
        <div class="ut-field ut-col-3">
            <label>ชั้นปี <span class="ut-required">*</span></label>
            <select class="form-select" name="year_level" required>
                @foreach(range(1,8) as $y)
                    <option value="{{ $y }}" @selected((string)old('year_level',$record->year_level ?: 1)===(string)$y)>ปีที่ {{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="ut-field ut-col-3">
            <label>วันที่บันทึก <span class="ut-required">*</span></label>
            <input type="date" max="{{ now('Asia/Bangkok')->toDateString() }}" class="form-control" name="record_date" value="{{ old('record_date',optional($record->record_date)->format('Y-m-d') ?: now('Asia/Bangkok')->toDateString()) }}" required>
        </div>
        <div class="ut-field ut-col-3">
            <label>เกรดเฉลี่ยภาคเรียน (GPA) <span class="ut-muted small">(คำนวณอัตโนมัติหากเว้นว่าง)</span></label>
            <input type="number" step="0.01" min="0" max="4" class="form-control" name="semester_gpa" value="{{ old('semester_gpa', $record->semester_gpa ?? $record->calculated_gpa) }}" placeholder="ยังไม่มีผลให้เว้นว่าง">
        </div>
        <div class="ut-field ut-col-3">
            <label>เกรดเฉลี่ยสะสม (GPAX) <span class="ut-muted small">(คำนวณอัตโนมัติหากเว้นว่าง)</span></label>
            <input type="number" step="0.01" min="0" max="4" class="form-control" name="cumulative_gpa" value="{{ old('cumulative_gpa', $record->cumulative_gpa ?? $record->calculated_gpax) }}" placeholder="ยังไม่มีผลให้เว้นว่าง">
        </div>
        {{-- UNIVERSITY_CREDIT_AUTO_FORM_V1 --}}
        <div class="ut-field ut-col-3">
            <label>หน่วยกิตลงทะเบียน <span class="ut-muted small">(อัตโนมัติ)</span></label>
            <input type="text" class="form-control bg-light" value="{{ $record->display_registered_credits ?? '' }}" readonly tabindex="-1" placeholder="คำนวณเมื่อบันทึก">
            <div class="ut-help">รวมหน่วยกิตของรายวิชาทั้งหมดในภาคเรียนนี้</div>
        </div>
        <div class="ut-field ut-col-3">
            <label>หน่วยกิตที่ผ่าน <span class="ut-muted small">(อัตโนมัติ)</span></label>
            <input type="text" class="form-control bg-light" value="{{ $record->display_earned_credits ?? '' }}" readonly tabindex="-1" placeholder="คำนวณเมื่อบันทึก">
            <div class="ut-help">รวมเฉพาะวิชาที่มีผลผ่าน / S / P</div>
        </div>
        <div class="ut-field ut-col-3">
            <label>หน่วยกิตสะสม <span class="ut-muted small">(อัตโนมัติ)</span></label>
            <input type="text" class="form-control bg-light" value="{{ $record->display_cumulative_credits ?? '' }}" readonly tabindex="-1" placeholder="คำนวณเมื่อบันทึก">
            <div class="ut-help">หน่วยกิตผ่านสะสมทุกภาคเรียน และไม่นับวิชาเรียนซ้ำอีกครั้ง</div>
        </div>
        <div class="ut-field ut-col-3"><label>สถานะทางการศึกษา</label><select class="form-select" name="academic_status">@foreach(config('university_tracking.academic_statuses') as $key=>$label)<option value="{{ $key }}" @selected(old('academic_status',$record->academic_status ?: 'normal')===$key)>{{ $label }}</option>@endforeach</select></div>
        <div class="ut-field ut-col-3"><label>ระดับความเสี่ยง</label><select class="form-select" name="risk_level">@foreach(config('university_tracking.risk_levels') as $key=>$label)<option value="{{ $key }}" @selected(old('risk_level',$record->risk_level ?: 'normal')===$key)>{{ $label }}</option>@endforeach</select></div>
        <div class="ut-field ut-col-9"><label>เหตุผล/ข้อสังเกตความเสี่ยง</label><input class="form-control" name="risk_note" value="{{ old('risk_note',$record->risk_note) }}"></div>
        <div class="ut-field ut-col-12"><label>สรุปผลภาคเรียน</label><textarea class="form-control" name="semester_summary" rows="3">{{ old('semester_summary',$record->semester_summary) }}</textarea></div>
    </div>
</div>

<div class="ut-divider"></div>
<div class="ut-section">
    <div class="d-flex justify-content-between gap-2 align-items-center mb-2">
        <div>
            <h2 class="ut-section-title mb-1"><i class="bi bi-list-check"></i> รายวิชา</h2>
            <div class="ut-help">นักศึกษาที่เพิ่งเริ่มเรียนสามารถกรอกเฉพาะรายวิชาไว้ก่อนได้ ช่องเกรดและผลรายวิชาจะว่างจนกว่าจะมีผลจริง</div>
        </div>
        <button type="button" class="ut-btn ut-btn-light" id="addSubject"><i class="bi bi-plus-circle"></i> เพิ่มวิชา</button>
    </div>
    <div id="subjectRows"></div>
</div>

<div class="ut-actions justify-content-end">
    <button class="ut-btn ut-btn-primary" type="submit"><i class="bi bi-floppy"></i> บันทึกข้อมูลภาคเรียน</button>
</div>
</form>
</div></div>

@php
    $subjectSeed = old(
        'subjects',
        $record->exists
            ? $record->subjects
                ->map(function ($subject) {
                    return $subject->only([
                        'id',
                        'course_code',
                        'course_name',
                        'credits',
                        'grade',
                        'grade_point',
                        'result_status',
                        'note',
                    ]);
                })
                ->values()
                ->all()
            : []
    );

    $subjectStatuses = [
        '' => 'ยังไม่มีผล / รอผล',
        'pass' => 'ผ่าน',
        'fail' => 'ไม่ผ่าน',
        'withdrawn' => 'W ถอนรายวิชา',
        'incomplete' => 'I ยังไม่สมบูรณ์',
        'satisfactory' => 'S',
        'unsatisfactory' => 'U',
        'audit' => 'Audit',
        'other' => 'อื่น ๆ',
    ];
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('subjectRows');
    const addButton = document.getElementById('addSubject');
    const deletedRoot = document.getElementById('deletedSubjectInputs');

    if (!root || !addButton || !deletedRoot) {
        return;
    }

    let rows = @json($subjectSeed);
    const statuses = @json($subjectStatuses);
    const deletedIds = new Set();

    if (!Array.isArray(rows)) {
        rows = [];
    }

    rows = rows.map(function (row) {
        if (String(row.result_status || '') === 'pending') {
            row.result_status = '';
        }
        return row;
    });

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>\"]/g, function (char) {
            const entities = {'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;'};
            return entities[char] ?? char;
        });
    }

    function statusOptions(selectedValue) {
        const selected = String(selectedValue || '');
        return Object.entries(statuses).map(function ([key, label]) {
            return `<option value="${escapeHtml(key)}" ${selected === key ? 'selected' : ''}>${escapeHtml(label)}</option>`;
        }).join('');
    }

    function renderDeletedInputs() {
        deletedRoot.innerHTML = Array.from(deletedIds).map(function (id) {
            return `<input type="hidden" name="deleted_subject_ids[]" value="${Number(id)}">`;
        }).join('');
    }

    // UNIVERSITY_SUBJECT_PRESERVE_TYPED_VALUES_V1_1
    // เก็บค่าที่ผู้ใช้พิมพ์ในแต่ละรายวิชากลับเข้า rows
    // ใช้ name="subjects[index][field]" เป็นตัวอ้างอิง จึงรองรับ field เพิ่มเติมในอนาคต
    function syncSubjectControl(control) {
        if (!control || !control.name) {
            return;
        }

        const match = control.name.match(
            /^subjects\[(\d+)\]\[([^\]]+)\]$/
        );

        if (!match) {
            return;
        }

        const index = Number(match[1]);
        const field = match[2];

        if (!Number.isInteger(index) || index < 0) {
            return;
        }

        if (!rows[index] || typeof rows[index] !== 'object') {
            rows[index] = {};
        }

        if (control.type === 'checkbox') {
            rows[index][field] = control.checked ? control.value : '';
            return;
        }

        if (control.type === 'radio') {
            if (control.checked) {
                rows[index][field] = control.value;
            }
            return;
        }

        rows[index][field] = control.value;
    }

    function syncAllSubjectRowsFromDom() {
        root
            .querySelectorAll(
                'input[name^="subjects["], select[name^="subjects["], textarea[name^="subjects["]'
            )
            .forEach(syncSubjectControl);
    }

    // Event delegation: sync ทันทีที่ผู้ใช้พิมพ์/เลือกค่า
    root.addEventListener('input', function (event) {
        syncSubjectControl(event.target);
    });

    root.addEventListener('change', function (event) {
        syncSubjectControl(event.target);
    });
    function renderSubjects() {
        root.innerHTML = '';

        rows.forEach(function (row, index) {
            const wrapper = document.createElement('div');
            wrapper.className = 'ut-subject-row';
            wrapper.innerHTML = `
                <button type="button" class="ut-row-remove" data-remove="${index}" aria-label="ลบรายวิชา">
                    <i class="bi bi-x"></i>
                </button>
                ${row.id ? `<input type="hidden" name="subjects[${index}][id]" value="${Number(row.id)}">` : ''}
                <div class="ut-grid">
                    <div class="ut-field ut-col-3">
                        <label>รหัสวิชา</label>
                        <input class="form-control" name="subjects[${index}][course_code]" value="${escapeHtml(row.course_code)}">
                    </div>
                    <div class="ut-field ut-col-9">
                        <label>ชื่อวิชา</label>
                        <input class="form-control" name="subjects[${index}][course_name]" value="${escapeHtml(row.course_name)}">
                    </div>
                    <div class="ut-field ut-col-3">
                        <label>หน่วยกิต</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="subjects[${index}][credits]" value="${escapeHtml(row.credits)}">
                    </div>
                    <div class="ut-field ut-col-3">
                        <label>เกรด</label>
                        <input class="form-control" name="subjects[${index}][grade]" value="${escapeHtml(row.grade)}" placeholder="ยังไม่มีผลให้เว้นว่าง">
                    </div>
                    <div class="ut-field ut-col-3">
                        <label>Grade point</label>
                        <input type="number" step="0.01" min="0" max="4" class="form-control" name="subjects[${index}][grade_point]" value="${escapeHtml(row.grade_point)}" placeholder="เว้นว่างได้">
                    </div>
                    <div class="ut-field ut-col-3">
                        <label>ผลรายวิชา</label>
                        <select class="form-select" name="subjects[${index}][result_status]">${statusOptions(row.result_status)}</select>
                    </div>
                    <div class="ut-field ut-col-12">
                        <label>หมายเหตุ</label>
                        <input class="form-control" name="subjects[${index}][note]" value="${escapeHtml(row.note)}">
                    </div>
                </div>
            `;
            root.appendChild(wrapper);
        });

        root.querySelectorAll('[data-remove]').forEach(function (button) {
            button.addEventListener('click', function () {
                const index = Number(button.dataset.remove);
                const row = rows[index];
                if (row && row.id) {
                    deletedIds.add(Number(row.id));
                }
                rows.splice(index, 1);
                renderDeletedInputs();
                renderSubjects();
            });
        });
    }

    addButton.addEventListener('click', function () {
        syncAllSubjectRowsFromDom();
        rows.push({
            course_code: '',
            course_name: '',
            credits: '',
            grade: '',
            grade_point: '',
            result_status: '',
            note: ''
        });
        renderSubjects();
    });

    if (rows.length === 0) {
        rows.push({
            course_code: '',
            course_name: '',
            credits: '',
            grade: '',
            grade_point: '',
            result_status: '',
            note: ''
        });
    }

    renderDeletedInputs();
    renderSubjects();
});
</script>
@endsection
