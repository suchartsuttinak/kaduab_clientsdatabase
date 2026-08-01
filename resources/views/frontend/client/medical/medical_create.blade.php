@extends('admin_client.admin_client')

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/assets/css/medical.css') }}">
@endpush

@section('content')
<div class="container-fluid py-3 medical-page">

    @include('frontend.client.medical.partials._header')
    @include('frontend.client.medical.partials._client_info')

    @if($medicals->isNotEmpty())
        @include('frontend.client.medical.partials._table')
    @else
        @include('frontend.client.medical.partials._empty')
    @endif

</div>

@include('frontend.client.medical.partials._add_modal')
@include('frontend.client.medical.partials._edit_modal')
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/medical.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const today = @json(now('Asia/Bangkok')->toDateString());
    const addModal = document.getElementById('add-medical-modal');
    const editModal = document.getElementById('editMedicalModal');

    /* วาง Modal ใต้ body โดยตรง ป้องกัน topbar/sidebar และ stacking context บัง Modal */
    [addModal, editModal].forEach(function (modal) {
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });

    document.querySelectorAll('#medical_start_date, #medical_end_date').forEach(function (input) {
        input.max = today;
    });

    function setupForm(formId, sectionId) {
        const form = document.getElementById(formId);
        if (!form || form.dataset.medicalGuardReady === 'true') {
            return;
        }

        form.dataset.medicalGuardReady = 'true';

        const medicalDate = form.querySelector('input[name="medical_date"]');
        const appointmentDate = form.querySelector('input[name="appt_date"]');
        const diagnosis = form.querySelector('[name="diagnosis"]');
        const conditionalSection = document.getElementById(sectionId);
        const referInputs = form.querySelectorAll('input[name="refer"]');
        const submitButton = form.querySelector('button[type="submit"]');

        function selectedRefer() {
            const checked = form.querySelector('input[name="refer"]:checked');
            return checked ? checked.value : '';
        }

        function syncAppointment() {
            const isDoctorVisit = selectedRefer() === 'พบแพทย์';

            if (conditionalSection) {
                conditionalSection.style.display = isDoctorVisit ? '' : 'none';
                conditionalSection.setAttribute('aria-hidden', isDoctorVisit ? 'false' : 'true');
            }

            if (appointmentDate) {
                if (medicalDate && medicalDate.value) {
                    appointmentDate.min = medicalDate.value;
                } else {
                    appointmentDate.removeAttribute('min');
                }

                if (!isDoctorVisit) {
                    appointmentDate.value = '';
                }
            }

            if (diagnosis) {
                diagnosis.required = isDoctorVisit;
                diagnosis.setAttribute('aria-required', isDoctorVisit ? 'true' : 'false');

                if (!isDoctorVisit) {
                    diagnosis.value = '';
                    diagnosis.classList.remove('is-invalid');
                    diagnosis.removeAttribute('aria-invalid');
                }
            }
        }

        referInputs.forEach(function (input) {
            input.addEventListener('change', syncAppointment);
        });

        if (medicalDate) {
            medicalDate.max = today;
            medicalDate.addEventListener('input', syncAppointment);
            medicalDate.addEventListener('change', syncAppointment);
        }

        form.addEventListener('submit', function (event) {
            form.classList.add('was-validated');

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();

                const invalid = form.querySelector(':invalid');
                if (invalid) {
                    try {
                        invalid.focus({ preventScroll: true });
                    } catch (error) {
                        invalid.focus();
                    }
                    invalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            /* ป้องกันการกดซ้ำ โดยไม่เพิ่ม spinner และไม่เปลี่ยนข้อความ */
            if (submitButton) {
                submitButton.disabled = true;
            }
        });

        const modal = form.closest('.modal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function () {
                if (submitButton) {
                    submitButton.disabled = false;
                }
            });
        }

        syncAppointment();
    }

    setupForm('add-medical-form', 'medical-section-new');
    setupForm('editMedicalForm', 'edit_medical_section');

    function fixMedicalTableOverflow() {
        const scrollBody = document.querySelector('.medical-page .dataTables_scrollBody');
        if (!scrollBody) {
            return;
        }

        const overflow = scrollBody.scrollWidth - scrollBody.clientWidth;
        const isLargeScreen = window.matchMedia('(min-width: 1400px)').matches;
        scrollBody.classList.toggle(
            'medical-scroll-fit',
            isLargeScreen && overflow > 0 && overflow <= 12
        );
    }

    window.requestAnimationFrame(function () {
        window.requestAnimationFrame(fixMedicalTableOverflow);
    });

    let resizeTimer = null;
    window.addEventListener('resize', function () {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(fixMedicalTableOverflow, 140);
    });

    function renumberMedicalRows() {
        if (!window.jQuery || !$.fn.DataTable || !$.fn.DataTable.isDataTable('#datatable-medical')) {
            return;
        }

        const api = $('#datatable-medical').DataTable();
        const pageInfo = api.page.info();

        api.rows({ page: 'current', order: 'applied', search: 'applied' })
            .nodes()
            .each(function (row, index) {
                const numberCell = row.querySelector('.medical-row-number');
                if (numberCell) {
                    numberCell.textContent = pageInfo.start + index + 1;
                }
            });
    }

    if (window.jQuery && $.fn.DataTable) {
        $('#datatable-medical').on('draw.dt column-sizing.dt', function () {
            renumberMedicalRows();
            window.requestAnimationFrame(fixMedicalTableOverflow);
        });

        window.requestAnimationFrame(renumberMedicalRows);
    }
});
</script>

@if ($errors->any() && old('_form_context') === 'medical_add' && !session('edit_mode'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('add-medical-modal');
    if (modal && window.bootstrap && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(modal).show();
    }
});
</script>
@endif

@if ($errors->any() && session('edit_mode') && session('edit_id'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.openEditMedical === 'function') {
        window.openEditMedical({{ session('edit_id') }});
    }

    if (typeof window.showEditErrors === 'function') {
        window.showEditErrors(@json($errors->toArray()));
    }
});
</script>
@endif
@endpush
