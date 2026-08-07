@extends('admin_client.admin_client')
@section('content')

<link rel="stylesheet" href="{{ asset('backend/assets/css/education-record-edit.css') }}">

<style>
    /* มาตรฐานปุ่มของระบบและคงสีเดิมขณะประมวลผล */
    .edurec-create-page .btn-edurec-add,
    .edurec-create-page .btn-edurec-home,
    .edurec-create-page .btn-edurec-save,
    .edurec-edit-page .btn-edurec-add,
    .edurec-edit-page .btn-edurec-home,
    .edurec-edit-page .btn-edurec-save {
        min-height: 44px;
        padding: 9px 17px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 12px;
        font-size: .92rem;
        font-weight: 750;
        line-height: 1.2;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
    }

    .edurec-create-page .btn-edurec-add,
    .edurec-edit-page .btn-edurec-add,
    .edurec-create-page .btn-edurec-save,
    .edurec-edit-page .btn-edurec-save {
        border: 1px solid #1d4ed8;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #fff;
        box-shadow: 0 7px 16px rgba(37, 99, 235, .18);
    }

    .edurec-create-page .btn-edurec-add:hover,
    .edurec-edit-page .btn-edurec-add:hover,
    .edurec-create-page .btn-edurec-save:hover,
    .edurec-edit-page .btn-edurec-save:hover {
        transform: translateY(-1px);
        border-color: #1e40af;
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        color: #fff;
        box-shadow: 0 9px 20px rgba(37, 99, 235, .24);
    }

    .edurec-create-page .btn-edurec-home,
    .edurec-edit-page .btn-edurec-home {
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #475569;
    }

    .edurec-create-page .btn-edurec-home:hover,
    .edurec-edit-page .btn-edurec-home:hover {
        transform: translateY(-1px);
        border-color: #94a3b8;
        background: #f1f5f9;
        color: #1e293b;
    }

    .edurec-create-page .btn-edurec-remove,
    .edurec-edit-page .btn-edurec-remove {
        min-width: 44px;
        min-height: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dc2626;
        background: #dc2626;
        color: #fff;
    }

    .edurec-create-page .btn-edurec-remove:hover,
    .edurec-edit-page .btn-edurec-remove:hover {
        background: #b91c1c;
        border-color: #b91c1c;
        color: #fff;
    }

    .edurec-create-page button:disabled,
    .edurec-edit-page button:disabled {
        opacity: 1 !important;
        cursor: not-allowed;
        transform: none !important;
    }

    .edurec-create-page .is-processing,
    .edurec-edit-page .is-processing {
        pointer-events: none;
    }

    .edurec-subject-item .invalid-feedback.subject-duplicate-error {
        display: block;
    }

    @media (max-width: 575.98px) {
        .edurec-footer-actions,
        .edurec-footer-actions-left,
        .edurec-footer-actions-right {
            width: 100%;
        }

        .edurec-footer-actions-left,
        .edurec-footer-actions-right {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .edurec-create-page .btn-edurec-add,
        .edurec-create-page .btn-edurec-home,
        .edurec-create-page .btn-edurec-save,
        .edurec-edit-page .btn-edurec-add,
        .edurec-edit-page .btn-edurec-home,
        .edurec-edit-page .btn-edurec-save {
            width: 100%;
        }
    }
</style>


<div class="container-fluid edurec-edit-page">

    <div class="edurec-wrap">

        {{-- ✅ แสดงข้อความแจ้งเตือน --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="edurec-card">
            <div class="edurec-header">
                <div class="edurec-header-inner">
                    <div class="edurec-title-wrap">
                        <div class="edurec-icon">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <div class="edurec-title-group">
                            <div class="edurec-badge">
                                <i class="bi bi-mortarboard-fill"></i>
                                <span>Education Record Edit</span>
                            </div>
                            <h1 class="edurec-title">แก้ไขผลการเรียน</h1>
                            <p class="edurec-subtitle">ปรับปรุงข้อมูลผลการเรียนให้ครบถ้วน</p>
                        </div>
                    </div>

                    <a href="{{ route('education_record_show', ['client_id' => $record->client_id]) }}"
                       class="edurec-close-btn"
                       aria-label="ปิดฟอร์ม"
                       title="กลับหน้าหลัก">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>

            <div class="edurec-body">
                <form action="{{ route('education_record_update', $record->id) }}" method="POST" id="educationRecordEditForm">
                    @csrf

                    <input type="hidden" name="client_id" value="{{ $client->id }}">

                    {{-- ข้อมูลพื้นฐาน --}}
                    <div class="edurec-section">
                        <h2 class="edurec-section-title">
                            <i class="bi bi-person-vcard"></i>
                            <span>ข้อมูลพื้นฐาน</span>
                        </h2>

                        <div class="edurec-info-grid">
                            <div class="edurec-col-6">
                                <div class="edurec-field">
                                    <label class="edurec-label">นักเรียน</label>
                                    <input type="text"
                                           class="form-control"
                                           value="{{ $client->full_name }}"
                                           readonly>
                                </div>
                            </div>

                            <div class="edurec-col-6">
                                <div class="edurec-field">
                                    <label for="education_id" class="edurec-label">
                                        ระดับการศึกษา <span class="text-danger">*</span>
                                    </label>
                                    <select name="education_id"
                                            id="education_id"
                                            class="form-select @error('education_id') is-invalid @enderror"
                                            required>
                                        <option value="">-- เลือกการศึกษา --</option>
                                        @foreach ($educations as $item)
                                            <option value="{{ $item->id }}"
                                                {{ old('education_id', $record->education_id ?? '') == $item->id ? 'selected' : '' }}>
                                                {{ $item->education_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('education_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="edurec-col-4">
                                <div class="edurec-field">
                                    <label for="semester_id" class="edurec-label">
                                        ภาคเรียน <span class="text-danger">*</span>
                                    </label>
                                    <select name="semester_id"
                                            id="semester_id"
                                            class="form-select @error('semester_id') is-invalid @enderror"
                                            required>
                                        <option value="">-- เลือกภาคเรียน --</option>
                                        @foreach($semesters as $sem)
                                            <option value="{{ $sem->id }}"
                                                {{ old('semester_id', $record->semester_id ?? '') == $sem->id ? 'selected' : '' }}>
                                                {{ $sem->semester_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('semester_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="edurec-col-8">
                                <div class="edurec-field">
                                    <label for="school_name" class="edurec-label">
                                        สถานศึกษา <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="school_name"
                                           id="school_name"
                                           class="form-control @error('school_name') is-invalid @enderror"
                                           value="{{ old('school_name', $record->school_name ?? '') }}"
                                           placeholder="กรอกชื่อสถานศึกษา"
                                           required>
                                    @error('school_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="edurec-col-3">
                                <div class="edurec-field">
                                    <label for="record_date" class="edurec-label">
                                        วันที่บันทึก <span class="text-danger">*</span>
                                    </label>
                                   <input type="date"
                                        name="record_date"
                                        id="record_date"
                                        class="form-control @error('record_date') is-invalid @enderror"
                                        value="{{ old('record_date', filled($record->record_date) ? \Carbon\Carbon::parse($record->record_date)->format('Y-m-d') : '') }}"
                                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                                        required>
                                    @error('record_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- รายวิชา --}}
                    <div class="edurec-section">
                        <div class="edurec-subject-toolbar">
                            <div>
                                <h2 class="edurec-section-title mb-2">
                                    <i class="bi bi-list-check"></i>
                                    <span>รายละเอียดวิชา</span>
                                </h2>
                                <p class="edurec-subject-note">
                                    สามารถเพิ่ม ลบ และแก้ไขข้อมูลรายวิชาได้ พร้อมคำนวณเกรดอัตโนมัติจากคะแนน
                                </p>
                            </div>

                            <button type="button" class="btn btn-edurec-add" id="add-subject">
                                <i class="bi bi-plus-circle"></i>
                                <span>เพิ่มวิชา</span>
                            </button>
                        </div>

                        @php
                            $formSubjects = old('subjects');
                            if (!is_array($formSubjects)) {
                                $formSubjects = $record->subjects->map(function ($subject) {
                                    return [
                                        'subject_id' => $subject->id,
                                        'score' => $subject->pivot->score,
                                        'grade' => $subject->pivot->grade,
                                    ];
                                })->values()->all();
                            }
                            if (count($formSubjects) === 0) {
                                $formSubjects = [['subject_id' => '', 'score' => '', 'grade' => '']];
                            }
                        @endphp

                        <div id="subject-container" class="edurec-subject-list">
                            @foreach($formSubjects as $index => $formSubject)
                                <div class="edurec-subject-item subject-item">
                                    <div class="edurec-subject-head">
                                        <div class="edurec-subject-badge">
                                            <i class="bi bi-book-half"></i>
                                            <span>วิชาที่ {{ $index + 1 }}</span>
                                        </div>
                                    </div>

                                    <div class="edurec-subject-grid">
                                        <div class="edurec-field">
                                            <label class="edurec-label">วิชา</label>
                                            <select name="subjects[{{ $index }}][subject_id]"
                                                    class="form-select @error('subjects.' . $index . '.subject_id') is-invalid @enderror">
                                                <option value="">-- เลือกวิชา --</option>
                                                @foreach($subjects as $subject)
                                                    <option value="{{ $subject->id }}"
                                                        {{ (string)($formSubject['subject_id'] ?? '') === (string)$subject->id ? 'selected' : '' }}>
                                                        {{ $subject->subject_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('subjects.' . $index . '.subject_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="edurec-field">
                                            <label class="edurec-label">คะแนน</label>
                                            <input type="number"
                                                   name="subjects[{{ $index }}][score]"
                                                   class="form-control subject-score @error('subjects.' . $index . '.score') is-invalid @enderror"
                                                   min="0"
                                                   max="100"
                                                   step="0.01"
                                                   inputmode="decimal"
                                                   value="{{ $formSubject['score'] ?? '' }}"
                                                   placeholder="0 - 100">
                                            <div class="edurec-helper">คะแนนเต็ม 100</div>
                                            @error('subjects.' . $index . '.score')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="edurec-field">
                                            <label class="edurec-label">เกรด</label>
                                            <input type="text"
                                                   name="subjects[{{ $index }}][grade]"
                                                   class="form-control subject-grade"
                                                   value="{{ $formSubject['grade'] ?? '' }}"
                                                   placeholder="คำนวณอัตโนมัติ"
                                                   readonly>
                                        </div>

                                        <div class="edurec-field">
                                            <label class="edurec-label d-none d-md-block">&nbsp;</label>
                                            <button type="button"
                                                    class="btn btn-edurec-remove remove-subject"
                                                    aria-label="ลบวิชา">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>


                        <div id="subject-empty-state" class="edurec-empty-state">
                            <i class="bi bi-journal-x"></i>
                            <div>ยังไม่มีรายการวิชา</div>
                            <small>กด “เพิ่มวิชา” เพื่อเริ่มเพิ่มข้อมูลผลการเรียน</small>
                        </div>
                    </div>

                    {{-- เกรดเฉลี่ย --}}
                    <div class="edurec-section">
                        <h2 class="edurec-section-title">
                            <i class="bi bi-bar-chart-line"></i>
                            <span>สรุปผลการเรียน</span>
                        </h2>

                        <div class="edurec-summary-box">
                            <div class="edurec-summary-grid">
                                <div class="edurec-summary-meta">
                                    จำนวนวิชาที่กรอกแล้ว:
                                    <strong><span id="subject-count">{{ max($record->subjects->count(), 1) }}</span></strong> รายการ
                                </div>

                                <div class="edurec-gpa-wrap">
                                    <label for="grade_average" class="edurec-label">เกรดเฉลี่ย</label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           max="4"
                                           inputmode="decimal"
                                           name="grade_average"
                                           id="grade_average"
                                           class="form-control @error('grade_average') is-invalid @enderror"
                                           value="{{ old('grade_average', $record->grade_average) }}">
                                    @error('grade_average')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ปุ่ม --}}
                    <div class="edurec-footer-actions">
                        <div class="edurec-footer-actions-left">
                            <button type="button" class="btn btn-edurec-add" id="add-subject-bottom">
                                <i class="bi bi-plus-circle"></i>
                                <span>เพิ่มวิชา</span>
                            </button>

                            <a href="{{ route('education_record_show', ['client_id' => $record->client_id]) }}"
                               class="btn btn-edurec-home">
                                <i class="bi bi-arrow-left-circle"></i>
                                <span>กลับหน้าหลัก</span>
                            </a>
                        </div>

                        <div class="edurec-footer-actions-right">
                            <button type="submit" class="btn btn-edurec-save">
                                <i class="bi bi-save"></i>
                                <span>แก้ไขข้อมูล</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const form = document.getElementById('educationRecordEditForm');
    const container = document.getElementById('subject-container');
    const emptyState = document.getElementById('subject-empty-state');
    const addButtons = [
        document.getElementById('add-subject'),
        document.getElementById('add-subject-bottom')
    ].filter(Boolean);
    const subjectCountEl = document.getElementById('subject-count');
    const maxSubjects = 30;

    if (!form || !container) return;

    function calculateGrade(score) {
        if (score === '' || score === null || typeof score === 'undefined') return '';

        const numericScore = Number(score);
        if (!Number.isFinite(numericScore)) return '';
        if (numericScore >= 80) return '4.00';
        if (numericScore >= 75) return '3.50';
        if (numericScore >= 70) return '3.00';
        if (numericScore >= 65) return '2.50';
        if (numericScore >= 60) return '2.00';
        if (numericScore >= 55) return '1.50';
        if (numericScore >= 50) return '1.00';
        return '0.00';
    }

    function updateGrade(scoreInput) {
        const item = scoreInput.closest('.subject-item');
        const gradeInput = item?.querySelector('.subject-grade');
        if (gradeInput) gradeInput.value = calculateGrade(scoreInput.value);
    }

    function clearRowErrors(item) {
        item.querySelectorAll('.is-invalid').forEach(element => element.classList.remove('is-invalid'));
        item.querySelectorAll('.invalid-feedback, .subject-duplicate-error').forEach(element => element.remove());
    }

    function renumberRows() {
        const items = Array.from(container.querySelectorAll('.subject-item'));

        items.forEach((item, index) => {
            const badge = item.querySelector('.edurec-subject-badge span');
            const subjectSelect = item.querySelector('select[name*="[subject_id]"]');
            const scoreInput = item.querySelector('input[name*="[score]"]');
            const gradeInput = item.querySelector('input[name*="[grade]"]');

            if (badge) badge.textContent = `วิชาที่ ${index + 1}`;
            if (subjectSelect) subjectSelect.name = `subjects[${index}][subject_id]`;
            if (scoreInput) scoreInput.name = `subjects[${index}][score]`;
            if (gradeInput) gradeInput.name = `subjects[${index}][grade]`;
        });

        const selectedCount = items.filter(item => {
            return Boolean(item.querySelector('select[name*="[subject_id]"]')?.value);
        }).length;

        if (subjectCountEl) subjectCountEl.textContent = selectedCount;
        if (emptyState) emptyState.style.display = items.length ? 'none' : 'block';
    }

    function showMaxSubjectAlert() {
        const message = `เพิ่มรายวิชาได้ไม่เกิน ${maxSubjects} รายการ`;
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'เพิ่มรายวิชาไม่ได้',
                text: message,
                confirmButtonText: 'ตกลง'
            });
        } else {
            window.alert(message);
        }
    }

    function addSubject() {
        const items = container.querySelectorAll('.subject-item');
        if (items.length >= maxSubjects) {
            showMaxSubjectAlert();
            return;
        }

        const template = items[0];
        if (!template) return;

        const item = template.cloneNode(true);
        clearRowErrors(item);

        item.querySelectorAll('select').forEach(select => select.value = '');
        item.querySelectorAll('input').forEach(input => input.value = '');

        container.appendChild(item);
        renumberRows();
        item.querySelector('select')?.focus();
    }

    function clearSingleItem(item) {
        clearRowErrors(item);
        item.querySelectorAll('select').forEach(select => select.value = '');
        item.querySelectorAll('input').forEach(input => input.value = '');
        renumberRows();
    }

    function validateDuplicateSubjects() {
        const seen = new Map();
        let valid = true;

        container.querySelectorAll('.subject-duplicate-error').forEach(error => error.remove());
        container.querySelectorAll('select[name*="[subject_id]"]').forEach(select => {
            if (select.classList.contains('subject-duplicate-invalid')) {
                select.classList.remove('subject-duplicate-invalid');
                const hasServerError = Boolean(
                    select.parentElement.querySelector('.invalid-feedback:not(.subject-duplicate-error)')
                );
                if (!hasServerError) select.classList.remove('is-invalid');
            }

            if (!select.value) return;

            if (seen.has(select.value)) {
                valid = false;
                select.classList.add('is-invalid', 'subject-duplicate-invalid');
                seen.get(select.value).classList.add('is-invalid', 'subject-duplicate-invalid');

                if (!select.parentElement.querySelector('.subject-duplicate-error')) {
                    const error = document.createElement('div');
                    error.className = 'invalid-feedback subject-duplicate-error';
                    error.textContent = 'ไม่สามารถเลือกรายวิชาเดิมซ้ำกันได้';
                    select.parentElement.appendChild(error);
                }
            } else {
                seen.set(select.value, select);
            }
        });

        return valid;
    }

    addButtons.forEach(button => button.addEventListener('click', addSubject));

    container.addEventListener('click', function (event) {
        const removeButton = event.target.closest('.remove-subject');
        if (!removeButton) return;

        const item = removeButton.closest('.subject-item');
        if (!item) return;

        if (container.querySelectorAll('.subject-item').length <= 1) {
            clearSingleItem(item);
            return;
        }

        item.remove();
        renumberRows();
        validateDuplicateSubjects();
    });

    container.addEventListener('input', function (event) {
        if (!event.target.matches('.subject-score')) return;

        const value = Number(event.target.value);
        if (event.target.value !== '' && Number.isFinite(value)) {
            if (value < 0) event.target.value = 0;
            if (value > 100) event.target.value = 100;
        }

        updateGrade(event.target);
    });

    container.addEventListener('change', function (event) {
        if (event.target.matches('select[name*="[subject_id]"]')) {
            renumberRows();
            validateDuplicateSubjects();
        }
    });

    form.addEventListener('submit', function (event) {
        if (!validateDuplicateSubjects()) {
            event.preventDefault();
            container.querySelector('.is-invalid')?.focus();
            return;
        }

        if (form.dataset.submitting === '1') {
            event.preventDefault();
            return;
        }

        form.dataset.submitting = '1';
        const submitButton = form.querySelector('button[type="submit"]');

        if (submitButton) {
            submitButton.dataset.originalHtml = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.classList.add('is-processing');
            submitButton.innerHTML = '<i class="bi bi-save" aria-hidden="true"></i><span>กำลังบันทึก...</span>';
        }
    });

    container.querySelectorAll('.subject-score').forEach(updateGrade);
    renumberRows();
    validateDuplicateSubjects();
});
</script>
@endsection