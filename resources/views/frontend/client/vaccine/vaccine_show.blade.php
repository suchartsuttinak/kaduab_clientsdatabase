@extends('admin_client.admin_client')

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/assets/css/vaccine.css') }}">
<style>
body.vaccine-modal-open{
    overflow:hidden;
}

/* ป้องกัน CSS เดิมตรึงคอลัมน์จัดการหรือสร้าง scrollbar ซ้อน */
.vaccine-page .vaccine-record-table th:last-child,
.vaccine-page .vaccine-record-table td:last-child{
    position:static !important;
    right:auto !important;
    box-shadow:none !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-3 vaccine-page">
    @include('frontend.client.vaccine.partials._header')
    @include('frontend.client.vaccine.partials._client_info')

    @if($vaccinations->isNotEmpty())
        @include('frontend.client.vaccine.partials._table')
    @else
        @include('frontend.client.vaccine.partials._empty')
    @endif
</div>

@include('frontend.client.vaccine.partials._add_modal')
@include('frontend.client.vaccine.partials._edit_modal')
@endsection

@push('scripts')
@php
    $vaccineAddHasErrors = $errors->any() && old('_form_context') === 'vaccine_add';
    $vaccineEditHasErrors = $errors->any() && old('_form_context') === 'vaccine_edit';
    $vaccineEditOldId = old('_edit_id');
    $vaccineEditOldValues = [
        'date' => old('date'),
        'vaccine_name' => old('vaccine_name'),
        'hospital' => old('hospital'),
        'recorder' => old('recorder'),
        'remark' => old('remark'),
    ];
@endphp
<script src="{{ asset('backend/assets/js/vaccine.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const today = @json(now('Asia/Bangkok')->toDateString());
    const addModal = document.getElementById('add-vaccine-modal');
    const editModal = document.getElementById('edit-vaccine-modal');
    const addForm = document.getElementById('add-vaccine-form');
    const editForm = document.getElementById('edit-vaccine-form');
    const startDate = document.getElementById('vaccine_date_from');
    const endDate = document.getElementById('vaccine_date_to');
    const editIdInput = document.getElementById('edit_vaccine_id');

    [addModal, editModal].forEach(function (modal) {
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        if (!modal) {
            return;
        }

        modal.addEventListener('show.bs.modal', function () {
            document.body.classList.add('vaccine-modal-open');
        });

        modal.addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('vaccine-modal-open');

            const form = modal.querySelector('form');
            const submitButton = form ? form.querySelector('button[type="submit"]') : null;
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = submitButton.dataset.originalHtml || submitButton.innerHTML;
            }
        });
    });

    function protectSubmitWithoutSpinner(form) {
        if (!form) {
            return;
        }

        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton && !submitButton.dataset.originalHtml) {
            submitButton.dataset.originalHtml = submitButton.innerHTML;
        }

        form.addEventListener('submit', function (event) {
            form.classList.add('was-validated');

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();

                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            if (submitButton) {
                // ป้องกันการกดซ้ำ แต่คงไอคอน สี และข้อความเดิมไว้ โดยไม่มี spinner
                window.setTimeout(function () {
                    submitButton.querySelectorAll('.spinner-border, .spinner-grow').forEach(function (spinner) {
                        spinner.remove();
                    });
                    submitButton.innerHTML = submitButton.dataset.originalHtml;
                    submitButton.disabled = true;
                }, 0);
            }
        });
    }

    protectSubmitWithoutSpinner(addForm);
    protectSubmitWithoutSpinner(editForm);

    function syncFilterDateLimits() {
        if (startDate) {
            startDate.max = today;
        }

        if (endDate) {
            endDate.max = today;
            if (startDate && startDate.value) {
                endDate.min = startDate.value;
            } else {
                endDate.removeAttribute('min');
            }
        }
    }

    if (startDate) {
        startDate.addEventListener('input', syncFilterDateLimits);
        startDate.addEventListener('change', syncFilterDateLimits);
    }
    syncFilterDateLimits();

    /* เก็บ id ของรายการแก้ไข เพื่อเปิด Modal เดิมได้ถูกต้องเมื่อ Validation ไม่ผ่าน */
    if (typeof window.vaccineEdit === 'function') {
        const originalVaccineEdit = window.vaccineEdit;
        window.vaccineEdit = function (id) {
            if (editIdInput) {
                editIdInput.value = id;
            }
            return originalVaccineEdit.apply(this, arguments);
        };
    }

    function setupVaccineDataTable() {
        const tableElement = document.getElementById('datatable-vaccine');
        if (!tableElement || !window.jQuery || !jQuery.fn.DataTable) {
            return;
        }

        const $table = jQuery(tableElement);
        let dataTable;

        if (jQuery.fn.DataTable.isDataTable(tableElement)) {
            dataTable = $table.DataTable();
        } else {
            dataTable = $table.DataTable({
                autoWidth: false,
                scrollX: true,
                scrollCollapse: true,
                order: [[0, 'desc']],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                        targets: -1
                    }
                ],
                language: {
                    emptyTable: 'ไม่พบข้อมูล',
                    info: 'แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ',
                    infoEmpty: 'แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ',
                    infoFiltered: '(กรองจากทั้งหมด _MAX_ รายการ)',
                    lengthMenu: 'แสดง _MENU_ รายการ',
                    loadingRecords: 'กำลังโหลด...',
                    processing: 'กำลังประมวลผล...',
                    search: 'ค้นหา:',
                    zeroRecords: 'ไม่พบข้อมูลที่ตรงกับการค้นหา',
                    paginate: {
                        first: 'หน้าแรก',
                        last: 'หน้าสุดท้าย',
                        next: 'ถัดไป',
                        previous: 'ก่อนหน้า'
                    }
                }
            });
        }

        const wrapper = tableElement.closest('.vaccine-record-table-wrapper');
        if (wrapper) {
            wrapper.classList.add('is-datatable-ready');
        }

        function adjustTableWidth() {
            dataTable.columns.adjust();

            window.requestAnimationFrame(function () {
                const scrollBody = wrapper ? wrapper.querySelector('.dataTables_scrollBody') : null;
                if (!scrollBody) {
                    return;
                }

                const overflow = scrollBody.scrollWidth - scrollBody.clientWidth;
                const isLargeScreen = window.matchMedia('(min-width: 1400px)').matches;

                // ซ่อนเฉพาะเศษความกว้าง 1–12px จากการปัดเศษของ DataTables บนจอใหญ่
                scrollBody.classList.toggle(
                    'vaccine-scroll-fit',
                    isLargeScreen && overflow >= 0 && overflow <= 12
                );
            });
        }

        adjustTableWidth();

        let resizeTimer = null;
        window.addEventListener('resize', function () {
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(adjustTableWidth, 120);
        });
    }

    // ให้สคริปต์เดิมมีโอกาส initialize ก่อน แล้วจึงตรวจและปรับซ้ำอย่างปลอดภัย
    window.setTimeout(setupVaccineDataTable, 0);

    const addHasErrors = @json($vaccineAddHasErrors);
    const editHasErrors = @json($vaccineEditHasErrors);
    const editOldId = @json($vaccineEditOldId);
    const editOldValues = @json($vaccineEditOldValues);

    if (addHasErrors && addModal && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(addModal).show();
    }

    if (editHasErrors && editOldId && typeof window.vaccineEdit === 'function') {
        if (editModal) {
            editModal.addEventListener('shown.bs.modal', function restoreOldEditValues() {
                Object.entries(editOldValues).forEach(function ([name, value]) {
                    const field = editForm ? editForm.querySelector('[name="' + name + '"]') : null;
                    if (field && value !== null) {
                        field.value = value;
                    }
                });
            }, { once: true });
        }

        window.vaccineEdit(editOldId);
    }

    @if($errors->any() && in_array(old('_form_context'), ['vaccine_add', 'vaccine_edit'], true))
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'กรุณาตรวจสอบข้อมูล',
                html: @json(implode('<br>', $errors->all())),
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        }
    @endif
});

if (typeof window.confirmDelete !== 'function') {
    window.confirmDelete = function (formId, message) {
        const form = document.getElementById(formId);
        if (!form) {
            return;
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'ยืนยันการลบข้อมูล',
                text: message || 'ข้อมูลที่ลบแล้วจะไม่สามารถกู้คืนได้',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'ลบข้อมูล',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                focusCancel: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return;
        }

        if (window.confirm(message || 'ยืนยันการลบข้อมูลนี้ใช่หรือไม่?')) {
            form.submit();
        }
    };
}
</script>
@endpush