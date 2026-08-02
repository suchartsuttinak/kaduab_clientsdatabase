@extends('admin_client.admin_client')

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/case_outside.css') }}">
@endpush

@section('content')
<div class="container-fluid mt-2 co-page">
    @if($caseoutsides->isEmpty())
        <section class="co-empty-header" aria-labelledby="caseOutsidePageTitle">
            <div class="co-empty-header-main">
                <span class="co-empty-header-icon" aria-hidden="true">
                    <i class="bi bi-geo-alt-fill"></i>
                </span>

                <div class="co-empty-header-copy">
                    <h1 class="co-empty-header-title" id="caseOutsidePageTitle">
                        การติดตามเด็กที่พักอาศัยภายนอก
                    </h1>
                    <p class="co-empty-header-subtitle">
                        ผู้รับบริการ:
                        <strong>{{ $client->fullname ?? $client->name ?? '-' }}</strong>
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.index', $client->id) }}"
               class="btn co-btn co-btn-back">
                <i class="bi bi-arrow-left-circle"></i>
                <span>กลับ</span>
            </a>
        </section>

        <section class="co-empty-card" aria-labelledby="caseOutsideEmptyTitle">
            <div class="co-empty-content">
                <span class="co-empty-icon" aria-hidden="true">
                    <i class="bi bi-geo-alt"></i>
                </span>

                <h2 class="co-empty-title" id="caseOutsideEmptyTitle">
                    ยังไม่มีข้อมูลการติดตามเด็กที่อยู่ภายนอก
                </h2>

                <p class="co-empty-description">
                    เริ่มต้นบันทึกวันที่ติดตาม สาเหตุที่พักอาศัยภายนอก สถานที่พัก
                    การดำเนินงาน และผลการติดตามของผู้รับบริการรายนี้
                </p>

                <button type="button"
                        class="btn co-empty-action"
                        data-bs-toggle="modal"
                        data-bs-target="#createCaseOutsideModal">
                    <i class="bi bi-plus-circle"></i>
                    <span>เพิ่มข้อมูลการติดตามครั้งแรก</span>
                </button>
            </div>
        </section>
    @else
        <div class="co-card">
            @include('frontend.client.case_outside.partials._header')

            <div class="co-body">
                @include('frontend.client.case_outside.partials.info-card')
                @include('frontend.client.case_outside.partials._table')
            </div>
        </div>
    @endif
</div>

{{-- Modal ต้องอยู่นอกตาราง เพื่อให้เลื่อนและปิดได้ถูกต้องทุกขนาดหน้าจอ --}}
@include('frontend.client.case_outside.partials.edit_modal')
@include('frontend.client.case_outside.partials.create_modal')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const validationErrors = @json($errors->all());
    const formContext = @json(old('_form_context'));
    const editCaseId = @json(old('case_id'));
    const successMessage = @json(session('success'));
    const today = @json(now('Asia/Bangkok')->toDateString());

    function showAlert(options) {
        if (window.Swal && typeof Swal.fire === 'function') {
            return Swal.fire(options);
        }

        window.alert(options.text || options.title || 'เกิดข้อผิดพลาด');
        return Promise.resolve({ isConfirmed: true });
    }

    // ป้องกัน parent ที่มี overflow/transform ตัด Modal
    document.querySelectorAll('.co-modal').forEach(function (modalElement) {
        if (modalElement.parentElement !== document.body) {
            document.body.appendChild(modalElement);
        }

        modalElement.addEventListener('shown.bs.modal', function () {
            const modalBody = modalElement.querySelector('.co-modal-body, .modal-body');
            if (modalBody) {
                modalBody.scrollTop = 0;
            }
        });
    });

    function clearValidation(form) {
        form.classList.remove('was-validated');
        form.querySelectorAll('.is-invalid').forEach(function (field) {
            field.classList.remove('is-invalid');
            field.setAttribute('aria-invalid', 'false');
        });
        form.querySelectorAll('.co-section-error').forEach(function (section) {
            section.classList.remove('co-section-error');
        });
        form.querySelectorAll('[data-client-error]').forEach(function (error) {
            error.classList.add('d-none');
        });
    }

    function validateRadioGroup(form) {
        const checked = form.querySelector('input[name="follo_no"]:checked');
        const section = form.querySelector('[data-follow-section]');
        const error = form.querySelector('[data-client-error]');

        if (section) {
            section.classList.toggle('co-section-error', !checked);
        }

        if (error) {
            error.classList.toggle('d-none', Boolean(checked));
        }

        return Boolean(checked);
    }

    document.querySelectorAll('[data-case-outside-form]').forEach(function (form) {
        const submitButton = form.querySelector('button[type="submit"]');

        form.querySelectorAll('input[name="follo_no"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                validateRadioGroup(form);
            });
        });

        form.addEventListener('submit', function (event) {
            const radioValid = validateRadioGroup(form);
            form.classList.add('was-validated');

            if (!form.checkValidity() || !radioValid) {
                event.preventDefault();
                event.stopPropagation();

                const firstInvalid = form.querySelector(
                    '.form-control:invalid, .form-select:invalid, input[name="follo_no"]'
                );

                if (firstInvalid) {
                    firstInvalid.focus({ preventScroll: true });
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }

                return;
            }

            if (submitButton && !submitButton.disabled) {
                submitButton.disabled = true;
                submitButton.dataset.originalHtml = submitButton.innerHTML;
                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    <span>กำลังบันทึก...</span>
                `;
            }
        });
    });

    // เปิดฟอร์มเพิ่มใหม่: รีเซ็ตเฉพาะเมื่อไม่ได้กลับมาจาก Validation
    document.querySelectorAll('[data-bs-target="#createCaseOutsideModal"]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (formContext === 'create_case_outside' && validationErrors.length) {
                return;
            }

            const form = document.getElementById('createCaseOutsideForm');
            if (!form) return;

            form.reset();
            clearValidation(form);

            const dateInput = form.querySelector('input[name="date"]');
            if (dateInput) {
                dateInput.value = today;
            }
        });
    });

    document.querySelectorAll('[data-delete-case-outside]').forEach(function (button) {
        button.addEventListener('click', function () {
            const formId = button.getAttribute('data-delete-case-outside');
            const form = document.getElementById(formId);

            if (!form) return;

            if (window.Swal && typeof Swal.fire === 'function') {
                Swal.fire({
                    icon: 'warning',
                    title: 'ยืนยันการลบข้อมูล',
                    text: 'คุณต้องการลบข้อมูลการติดตามรายการนี้ใช่หรือไม่',
                    showCancelButton: true,
                    confirmButtonText: 'ลบข้อมูล',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#dc3545',
                    reverseButtons: true
                }).then(function (result) {
                    if (result.isConfirmed) {
                        button.disabled = true;
                        form.submit();
                    }
                });
            } else if (window.confirm('คุณต้องการลบข้อมูลการติดตามรายการนี้ใช่หรือไม่')) {
                button.disabled = true;
                form.submit();
            }
        });
    });

    if (validationErrors.length) {
        let modalElement = null;

        if (formContext === 'edit_case_outside' && editCaseId) {
            modalElement = document.getElementById('editCaseOutsideModal' + editCaseId);
        } else {
            modalElement = document.getElementById('createCaseOutsideModal');
        }

        if (modalElement && window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalElement).show();
        }

        showAlert({
            icon: 'error',
            title: 'กรุณาตรวจสอบข้อมูล',
            html: validationErrors.map(function (message) {
                const div = document.createElement('div');
                div.textContent = message;
                return div.innerHTML;
            }).join('<br>'),
            confirmButtonText: 'ตกลง'
        });
    }

    if (successMessage) {
        showAlert({
            icon: 'success',
            title: 'สำเร็จ',
            text: successMessage,
            timer: 2600,
            showConfirmButton: false
        });
    }

    function adjustTable() {
        if (
            window.jQuery &&
            jQuery.fn.DataTable &&
            jQuery.fn.DataTable.isDataTable('#datatable-caseoutside')
        ) {
            const table = jQuery('#datatable-caseoutside').DataTable();
            table.columns.adjust();

            if (table.responsive && typeof table.responsive.recalc === 'function') {
                table.responsive.recalc();
            }
        }
    }

    setTimeout(adjustTable, 150);
    window.addEventListener('resize', adjustTable);
});
</script>
@endpush
