@extends('admin_client.admin_client')

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/job_agency.css') }}">
@endpush

@section('content')
@php
    $hasAnyJobAgency = $hasAnyJobAgency ?? $jobAgencies->isNotEmpty();
    $hasDateFilter = request()->filled('start_date')
        || request()->filled('end_date')
        || filled(old('start_date'))
        || filled(old('end_date'));

    $hasFilterErrors = $errors->getBag('filters')->any();
    $showDateFilter = $hasDateFilter || $hasFilterErrors;
@endphp

<div class="container-fluid mt-2 jobagency-page">
    <div class="ja-main-card">
        @include('frontend.client.job_agency.partials._header')

        <div class="ja-body">
            @if(!$hasAnyJobAgency && !$hasDateFilter)
                <section class="ja-first-empty" aria-labelledby="jobAgencyEmptyTitle">
                    <div class="ja-first-empty-icon" aria-hidden="true">
                        <i class="bi bi-briefcase"></i>
                    </div>

                    <h2 class="ja-first-empty-title" id="jobAgencyEmptyTitle">
                        ยังไม่มีข้อมูลการจัดหางาน
                    </h2>

                    <p class="ja-first-empty-description">
                        เริ่มต้นบันทึกวันที่เริ่มงาน อาชีพ ตำแหน่ง รายได้ บริษัทหรือหน่วยงาน
                        และผู้ประสานงานของผู้รับบริการรายนี้
                    </p>

                    <button type="button"
                            class="btn btn-primary ja-first-empty-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#createJobAgencyModal">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มข้อมูลการจัดหางานครั้งแรก</span>
                    </button>
                </section>
            @else
                @include('frontend.client.job_agency.partials._table')
            @endif
        </div>
    </div>

    @include('frontend.client.job_agency.partials._edit_modal')
    @include('frontend.client.job_agency.partials._create_modal')
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const validationErrors = @json($errors->all());
    const oldJobId = @json(old('job_id'));
    const successMessage = @json(session('success'));
    const today = @json(now('Asia/Bangkok')->toDateString());
    const formSelector = '.jobagency-validate-form';
    const createModalEl = document.getElementById('createJobAgencyModal');
    const createForm = document.getElementById('createJobAgencyForm');
    const filterPanel = document.getElementById('jobAgencyFilterPanel');
    const filterToggle = document.querySelector('[data-job-agency-filter-toggle]');

    function syncFilterToggle(isOpen) {
        if (!filterToggle) return;

        filterToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        const icon = filterToggle.querySelector('[data-filter-toggle-icon]');
        const label = filterToggle.querySelector('[data-filter-toggle-label]');

        if (icon) {
            icon.className = isOpen
                ? 'bi bi-chevron-up'
                : 'bi bi-funnel';
        }

        if (label) {
            label.textContent = isOpen
                ? 'ซ่อนการค้นหา'
                : 'ค้นหารายการ';
        }
    }

    if (filterPanel) {
        syncFilterToggle(filterPanel.classList.contains('show'));
        filterPanel.addEventListener('shown.bs.collapse', function () {
            syncFilterToggle(true);

            const firstFilter = filterPanel.querySelector('input:not([disabled])');
            if (firstFilter) {
                setTimeout(function () {
                    firstFilter.focus({ preventScroll: true });
                }, 100);
            }
        });
        filterPanel.addEventListener('hidden.bs.collapse', function () {
            syncFilterToggle(false);
        });
    }

    function showAlert(options) {
        if (window.Swal && typeof Swal.fire === 'function') {
            return Swal.fire(options);
        }

        window.alert(options.text || options.title || 'เกิดข้อผิดพลาด');
        return Promise.resolve({ isConfirmed: true });
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = String(value ?? '');
        return div.innerHTML;
    }

    // Modal ต้องคงอยู่ภายใน .jobagency-page เพื่อให้ CSS แบบ scoped ทำงานครบถ้วน
    // ห้ามย้าย Modal ไปไว้ใต้ document.body เพราะจะทำให้รูปแบบฟอร์มและปุ่มเสีย
    document.querySelectorAll('.ja-modal').forEach(function (modalElement) {
        modalElement.addEventListener('shown.bs.modal', function () {
            const modalBody = modalElement.querySelector('.ja-modal-body, .modal-body');
            if (modalBody) modalBody.scrollTop = 0;
        });
    });

    function clearFieldError(field) {
        if (!field) return;

        field.classList.remove('is-invalid');
        field.setAttribute('aria-invalid', 'false');

        const wrapper = field.closest('.col-12, .col-md-4, .col-md-6, .col-md-8, .ja-section') || field.parentElement;
        if (!wrapper) return;

        wrapper.querySelectorAll(':scope > .ja-invalid-text').forEach(function (errorElement) {
            errorElement.remove();
        });
    }

    function showFieldError(field, message) {
        if (!field) return;

        field.classList.add('is-invalid');
        field.setAttribute('aria-invalid', 'true');

        const wrapper = field.closest('.col-12, .col-md-4, .col-md-6, .col-md-8, .ja-section') || field.parentElement;
        if (!wrapper) return;

        let errorElement = wrapper.querySelector(':scope > .ja-invalid-text');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'ja-invalid-text';
            wrapper.appendChild(errorElement);
        }

        errorElement.textContent = message;
    }

    function resetValidationState(form) {
        if (!form) return;

        form.querySelectorAll('.is-invalid').forEach(function (element) {
            element.classList.remove('is-invalid');
            element.setAttribute('aria-invalid', 'false');
        });

        form.querySelectorAll('.ja-invalid-text').forEach(function (element) {
            if (!element.hasAttribute('data-server-error')) {
                element.remove();
            }
        });
    }

    function validateJobAgencyForm(form) {
        let isValid = true;
        let firstInvalid = null;

        const fields = {
            jobDate: form.querySelector('input[name="job_date"]'),
            occupationId: form.querySelector('select[name="occupation_id"]'),
            position: form.querySelector('input[name="position"]'),
            income: form.querySelector('input[name="income"]'),
            company: form.querySelector('input[name="company"]'),
            coordinator: form.querySelector('input[name="coordinator"]')
        };

        Object.values(fields).forEach(clearFieldError);

        function invalidate(field, message) {
            isValid = false;
            showFieldError(field, message);
            if (!firstInvalid) firstInvalid = field;
        }

        if (!fields.jobDate?.value) {
            invalidate(fields.jobDate, 'กรุณาเลือกวันที่เริ่มงาน');
        } else if (fields.jobDate.value > today) {
            invalidate(fields.jobDate, 'วันที่เริ่มงานต้องไม่เกินวันที่ปัจจุบัน');
        }

        if (!fields.occupationId?.value) {
            invalidate(fields.occupationId, 'กรุณาเลือกอาชีพ');
        }

        if (!fields.position?.value.trim()) {
            invalidate(fields.position, 'กรุณากรอกตำแหน่งงาน');
        }

        if (!fields.income?.value.trim()) {
            invalidate(fields.income, 'กรุณากรอกรายได้');
        } else if (!Number.isFinite(Number(fields.income.value)) || Number(fields.income.value) < 0) {
            invalidate(fields.income, 'รายได้ต้องเป็นตัวเลขที่มีค่าไม่น้อยกว่า 0');
        }

        if (!fields.company?.value.trim()) {
            invalidate(fields.company, 'กรุณากรอกชื่อบริษัทหรือหน่วยงาน');
        }

        if (!fields.coordinator?.value.trim()) {
            invalidate(fields.coordinator, 'กรุณากรอกชื่อผู้ประสานงาน');
        }

        if (!isValid && firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(function () {
                firstInvalid.focus({ preventScroll: true });
            }, 150);
        }

        return isValid;
    }

    function restoreSubmitButton(button) {
        if (!button) return;

        button.disabled = false;
        if (button.dataset.originalHtml) {
            button.innerHTML = button.dataset.originalHtml;
        }
    }

    document.querySelectorAll(formSelector).forEach(function (form) {
        form.querySelectorAll('input, select, textarea').forEach(function (field) {
            ['input', 'change'].forEach(function (eventName) {
                field.addEventListener(eventName, function () {
                    if (String(field.value ?? '').trim()) clearFieldError(field);
                });
            });
        });

        form.addEventListener('submit', function (event) {
            resetValidationState(form);

            if (!validateJobAgencyForm(form)) {
                event.preventDefault();
                event.stopPropagation();

                showAlert({
                    icon: 'error',
                    title: 'กรุณาตรวจสอบข้อมูล',
                    text: 'กรุณาตรวจสอบช่องที่มีกรอบสีแดงให้ครบถ้วน',
                    confirmButtonText: 'ตกลง'
                });

                return;
            }

            if (form.dataset.submitting === '1') {
                event.preventDefault();
                return;
            }

            form.dataset.submitting = '1';

            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.dataset.originalHtml ||= submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    <span>กำลังบันทึก...</span>
                `;
            }
        });
    });

    if (createModalEl && createForm) {
        createModalEl.addEventListener('show.bs.modal', function () {
            // เมื่อกลับมาจาก Validation ฝั่ง Server ต้องรักษาค่าที่ผู้ใช้กรอกไว้
            if (validationErrors.length && !oldJobId) return;

            createForm.reset();
            createForm.dataset.submitting = '0';
            resetValidationState(createForm);

            const dateInput = createForm.querySelector('input[name="job_date"]');
            if (dateInput) dateInput.value = today;

            restoreSubmitButton(createForm.querySelector('button[type="submit"]'));
        });
    }

    document.querySelectorAll('.ja-modal-edit').forEach(function (modalElement) {
        modalElement.addEventListener('show.bs.modal', function () {
            const form = modalElement.querySelector('form.jobagency-validate-form');
            if (!form) return;

            form.dataset.submitting = '0';
            resetValidationState(form);
            restoreSubmitButton(form.querySelector('button[type="submit"]'));
        });
    });

    document.querySelectorAll('[data-delete-job-agency]').forEach(function (button) {
        button.addEventListener('click', function () {
            const formId = button.getAttribute('data-delete-job-agency');
            const form = document.getElementById(formId);
            if (!form) return;

            showAlert({
                icon: 'warning',
                title: 'ยืนยันการลบข้อมูล',
                text: 'เมื่อลบแล้วจะไม่สามารถกู้คืนรายการนี้ได้',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'ใช่, ลบข้อมูล',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                focusCancel: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    button.disabled = true;
                    form.submit();
                }
            });
        });
    });

    if (validationErrors.length) {
        const modalId = oldJobId
            ? 'editJobAgencyModal' + oldJobId
            : 'createJobAgencyModal';
        const modalElement = document.getElementById(modalId);

        if (modalElement && window.bootstrap?.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalElement).show();
        }

        showAlert({
            icon: 'error',
            title: 'กรุณาตรวจสอบข้อมูล',
            html: validationErrors.map(escapeHtml).join('<br>'),
            confirmButtonText: 'ตกลง'
        });
    }

    if (successMessage) {
        showAlert({
            icon: 'success',
            title: 'สำเร็จ',
            text: successMessage,
            timer: 2600,
            timerProgressBar: true,
            showConfirmButton: false
        });
    }


});
</script>
@endpush