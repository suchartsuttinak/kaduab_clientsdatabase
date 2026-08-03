@extends('admin_client.admin_client')
@section('content')

@php
    $canApproveRefer = auth()->check() && in_array(auth()->user()->role, ['admin', 'executive'], true);
    $hasReferRows = $refers->isNotEmpty();
    $canCreateRefer = $canCreateRefer ?? !in_array($client->release_status, ['pending_refer', 'refer'], true);
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/assets/css/refer.css') }}">
@endpush

<div class="container-fluid mt-2 refer-page">
    <div class="rf-main-card">
        @include('frontend.client.refer.partials._header')

        <div class="rf-body">
            @if($hasReferRows)
                @include('frontend.client.refer.partials.info-card')
            @endif

            @include('frontend.client.refer.partials._table')
        </div>
    </div>

    @include('frontend.client.refer.partials.create_modal')
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const config = {
        today: @json(now('Asia/Bangkok')->toDateString()),
        oldGuardian: @json(old('guardian')),
        serverErrors: @json($errors->all()),
        preserveOldInput: @json($errors->any()),
    };

    const createModalEl = document.getElementById('createReferModal');
    const form = document.getElementById('referForm');
    const guardianFields = document.getElementById('guardianFields');
    const guardianSection = document.getElementById('guardianSection');
    const guardianRadios = form ? form.querySelectorAll('input[name="guardian"]') : [];
    const tableEl = document.getElementById('datatable-refer');

    function setGuardianState(show, clearHiddenFields = true) {
        if (!guardianFields) return;

        guardianFields.classList.toggle('is-active', show);

        const parentName = guardianFields.querySelector('input[name="parent_name"]');
        if (parentName) parentName.required = Boolean(show);

        if (!show && clearHiddenFields) {
            guardianFields.querySelectorAll('input').forEach(function (element) {
                element.value = '';
                element.classList.remove('is-invalid');
            });
        }
    }

    function fieldWrapper(field) {
        if (!field) return null;
        return field.closest('.col-12, .col-md-4, .col-md-6, .col-md-8, .rf-section') || field.parentElement;
    }

    function clearFieldError(field) {
        if (!field) return;
        field.classList.remove('is-invalid');

        const wrapper = fieldWrapper(field);
        const errorElement = wrapper ? wrapper.querySelector(':scope > .rf-invalid-text') : null;
        errorElement?.remove();
    }

    function showFieldError(field, message) {
        if (!field) return;
        field.classList.add('is-invalid');

        const wrapper = fieldWrapper(field);
        if (!wrapper) return;

        let errorElement = wrapper.querySelector(':scope > .rf-invalid-text');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'rf-invalid-text';
            wrapper.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }

    function clearGuardianError() {
        guardianRadios.forEach(function (radio) {
            radio.closest('.rf-option')?.classList.remove('is-invalid-wrap');
        });

        guardianSection?.classList.remove('rf-section-error');
        guardianSection?.querySelector(':scope > .rf-invalid-text')?.remove();
    }

    function showGuardianError(message) {
        guardianRadios.forEach(function (radio) {
            radio.closest('.rf-option')?.classList.add('is-invalid-wrap');
        });

        if (!guardianSection) return;

        guardianSection.classList.add('rf-section-error');
        let errorElement = guardianSection.querySelector(':scope > .rf-invalid-text');

        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'rf-invalid-text';
            guardianSection.appendChild(errorElement);
        }

        errorElement.textContent = message;
    }

    function resetValidationState() {
        if (!form) return;

        form.querySelectorAll('.is-invalid').forEach(function (element) {
            element.classList.remove('is-invalid');
        });

        form.querySelectorAll('.is-invalid-wrap').forEach(function (element) {
            element.classList.remove('is-invalid-wrap');
        });

        form.querySelectorAll('.rf-section-error').forEach(function (element) {
            element.classList.remove('rf-section-error');
        });

        form.querySelectorAll('.rf-invalid-text').forEach(function (element) {
            element.remove();
        });
    }

    function focusInvalid(field) {
        if (!field || !form) return;

        const modalBody = form.querySelector('.rf-modal-body');
        if (modalBody && modalBody.contains(field)) {
            const bodyRect = modalBody.getBoundingClientRect();
            const fieldRect = field.getBoundingClientRect();
            modalBody.scrollTop += (fieldRect.top - bodyRect.top) - 80;
        }

        setTimeout(function () {
            field.focus({ preventScroll: true });
        }, 120);
    }

    function validateForm() {
        if (!form) return true;

        let valid = true;
        let firstInvalid = null;

        const referDate = form.querySelector('input[name="refer_date"]');
        const translateId = form.querySelector('select[name="translate_id"]');
        const destination = form.querySelector('input[name="destination"]');
        const address = form.querySelector('textarea[name="address"]');
        const parentName = form.querySelector('input[name="parent_name"]');
        const teacher = form.querySelector('input[name="teacher"]');
        const committeeResult = form.querySelector('input[name="committee_result"]:checked');
        const meetingFile = form.querySelector('input[name="meeting_report_file"]');
        const guardianValue = form.querySelector('input[name="guardian"]:checked')?.value || '';

        [referDate, translateId, destination, address, parentName, teacher, meetingFile].forEach(clearFieldError);
        clearGuardianError();

        function reject(field, message) {
            valid = false;
            showFieldError(field, message);
            if (!firstInvalid) firstInvalid = field;
        }

        if (!referDate?.value) {
            reject(referDate, 'กรุณาเลือกวันที่นำส่ง');
        } else if (referDate.value > config.today) {
            reject(referDate, 'วันที่นำส่งต้องไม่เกินวันที่ปัจจุบัน');
        }

        if (!translateId?.value) reject(translateId, 'กรุณาเลือกสาเหตุการจำหน่าย');
        if (!destination?.value.trim()) reject(destination, 'กรุณากรอกชื่อสถานที่นำส่ง');
        if (!address?.value.trim()) reject(address, 'กรุณากรอกที่อยู่');

        if (!guardianValue) {
            valid = false;
            showGuardianError('กรุณาเลือกว่ามีผู้ดูแลหรือไม่');
            if (!firstInvalid && guardianRadios.length) firstInvalid = guardianRadios[0];
        } else if (guardianValue === 'มี' && !parentName?.value.trim()) {
            reject(parentName, 'กรณีมีผู้ดูแล กรุณากรอกชื่อผู้รับตัว');
        }

        if (!teacher?.value.trim()) reject(teacher, 'กรุณากรอกชื่อผู้นำส่ง');

        if (committeeResult?.value === 'ผ่าน') {
            const file = meetingFile?.files?.[0];

            if (!file) {
                reject(meetingFile, 'กรุณาแนบรายงานการประชุม PDF');
            } else {
                const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                if (!isPdf) reject(meetingFile, 'แนบได้เฉพาะไฟล์ PDF เท่านั้น');
                if (file.size > 10 * 1024 * 1024) reject(meetingFile, 'ไฟล์ PDF ต้องมีขนาดไม่เกิน 10 MB');
            }
        }

        if (!valid && firstInvalid) focusInvalid(firstInvalid);
        return valid;
    }

    function prepareFreshForm() {
        if (!form) return;

        form.reset();
        form.dataset.submitting = '0';
        resetValidationState();

        const dateInput = form.querySelector('input[name="refer_date"]');
        if (dateInput) dateInput.value = config.today;

        guardianRadios.forEach(function (radio) {
            radio.checked = false;
        });
        setGuardianState(false, true);

        const defaultCommittee = form.querySelector('input[name="committee_result"][value="ไม่ผ่าน"]');
        if (defaultCommittee) {
            defaultCommittee.checked = true;
            defaultCommittee.dispatchEvent(new Event('change', { bubbles: true }));
        }

        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) submitButton.disabled = false;
    }

    if (form) {
        form.querySelectorAll('input[name="refer_date"], select[name="translate_id"], input[name="destination"], textarea[name="address"], input[name="parent_name"], input[name="teacher"], input[name="meeting_report_file"]').forEach(function (field) {
            ['input', 'change'].forEach(function (eventName) {
                field.addEventListener(eventName, function () {
                    if (field.type === 'file' ? field.files.length : field.value.trim()) {
                        clearFieldError(field);
                    }
                });
            });
        });

        guardianRadios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                clearGuardianError();
                setGuardianState(this.value === 'มี', true);
            });
        });

        form.addEventListener('submit', function (event) {
            resetValidationState();

            if (!validateForm()) {
                event.preventDefault();
                event.stopPropagation();

                window.Swal?.fire({
                    icon: 'error',
                    title: 'กรอกข้อมูลไม่ครบ',
                    text: 'กรุณาตรวจสอบช่องที่มีกรอบสีแดง',
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
            if (submitButton) submitButton.disabled = true;
        });
    }

    if (createModalEl && form) {
        createModalEl.addEventListener('show.bs.modal', function () {
            if (form.dataset.preserveOldInput === '1') {
                delete form.dataset.preserveOldInput;
                setGuardianState(config.oldGuardian === 'มี', false);
                return;
            }

            prepareFreshForm();
        });

        createModalEl.addEventListener('shown.bs.modal', function () {
            const modalBody = createModalEl.querySelector('.rf-modal-body');
            if (modalBody) modalBody.scrollTop = 0;
        });

        createModalEl.addEventListener('hidden.bs.modal', function () {
            form.dataset.submitting = '0';
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) submitButton.disabled = false;
        });
    }

    document.querySelectorAll('.js-refer-confirm-form').forEach(function (confirmForm) {
        confirmForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const button = confirmForm.querySelector('button[type="submit"]');
            const submitConfirmed = function () {
                if (button) button.disabled = true;
                HTMLFormElement.prototype.submit.call(confirmForm);
            };

            if (!window.Swal) {
                if (window.confirm(confirmForm.dataset.confirmText || 'ยืนยันการดำเนินการใช่หรือไม่')) {
                    submitConfirmed();
                }
                return;
            }

            Swal.fire({
                icon: 'warning',
                title: confirmForm.dataset.confirmTitle || 'ยืนยันการดำเนินการ',
                text: confirmForm.dataset.confirmText || 'กรุณายืนยันการดำเนินการ',
                showCancelButton: true,
                confirmButtonText: confirmForm.dataset.confirmButton || 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                focusCancel: true
            }).then(function (result) {
                if (result.isConfirmed) submitConfirmed();
            });
        });
    });

    function adjustReferTable() {
        if (!tableEl || !window.jQuery || !jQuery.fn.DataTable) return;
        if (!jQuery.fn.DataTable.isDataTable(tableEl)) return;

        const dataTable = jQuery(tableEl).DataTable();
        dataTable.columns.adjust();

        if (dataTable.responsive && typeof dataTable.responsive.recalc === 'function') {
            dataTable.responsive.recalc();
        }
    }

    if (tableEl) {
        setTimeout(adjustReferTable, 150);
        window.addEventListener('resize', adjustReferTable);

        if (window.jQuery) {
            jQuery(tableEl).on('draw.dt', adjustReferTable);
        }
    }

    if (config.preserveOldInput && createModalEl && form) {
        form.dataset.preserveOldInput = '1';
        bootstrap.Modal.getOrCreateInstance(createModalEl).show();

        if (config.serverErrors.length && window.Swal) {
            const errorContainer = document.createElement('div');
            errorContainer.className = 'text-start';

            config.serverErrors.forEach(function (message) {
                const line = document.createElement('div');
                line.textContent = '• ' + message;
                errorContainer.appendChild(line);
            });

            Swal.fire({
                icon: 'error',
                title: 'กรุณาตรวจสอบข้อมูล',
                html: errorContainer,
                confirmButtonText: 'ตกลง'
            });
        }
    }

    @if (session('message'))
        (function () {
            const alertType = @json(session('alert-type'));
            const icon = ['success', 'warning', 'error', 'info'].includes(alertType) ? alertType : 'info';

            window.Swal?.fire({
                icon: icon,
                title: icon === 'success' ? 'สำเร็จ' : (icon === 'warning' ? 'แจ้งเตือน' : (icon === 'error' ? 'เกิดข้อผิดพลาด' : 'ข้อมูล')),
                text: @json(session('message')),
                timer: icon === 'success' ? 2500 : undefined,
                showConfirmButton: icon !== 'success',
                confirmButtonText: 'ตกลง'
            });
        })();
    @endif
});
</script>
@endpush

@endsection
