@extends('admin_client.admin_client')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('backend/assets/css/help-sessions.css') }}">
<style>
    .help-page {
        padding: 1rem 0 2.5rem;
    }

    .help-page .hp-body {
        margin-top: 1rem;
    }

    @media (max-width: 767.98px) {
        .help-page {
            padding-top: .75rem;
        }
    }
</style>
@endpush

<div class="container-fluid help-page">
    @include('frontend.client.helping.partials._header')

    @if($hasAnySessions)
        <div class="hp-body">
            @include('frontend.client.helping.partials._table')
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const successMessage = @json(session('message'));

    document.querySelectorAll('.help-page .collapse').forEach(function (collapseElement) {
        collapseElement.addEventListener('show.bs.collapse', function () {
            const button = document.querySelector('[data-bs-target="#' + collapseElement.id + '"]');
            if (!button) return;

            button.classList.add('is-open');
            button.setAttribute('aria-expanded', 'true');
            button.innerHTML = '<i class="bi bi-eye-slash"></i><span>ซ่อนรายการ</span>';
        });

        collapseElement.addEventListener('hide.bs.collapse', function () {
            const button = document.querySelector('[data-bs-target="#' + collapseElement.id + '"]');
            if (!button) return;

            button.classList.remove('is-open');
            button.setAttribute('aria-expanded', 'false');
            button.innerHTML = '<i class="bi bi-list-ul"></i><span>แสดงรายการ</span>';
        });
    });

    document.querySelectorAll('.help-delete-form').forEach(function (form) {
        const button = form.querySelector('.help-delete-button');
        if (!button) return;

        button.addEventListener('click', function () {
            const submitDelete = function () {
                button.disabled = true;
                form.submit();
            };

            if (!window.Swal) {
                if (window.confirm('คุณต้องการลบข้อมูลการช่วยเหลือรายการนี้ใช่หรือไม่')) {
                    submitDelete();
                }
                return;
            }

            Swal.fire({
                icon: 'warning',
                title: 'ยืนยันการลบข้อมูล',
                text: 'เมื่อลบแล้วจะไม่สามารถกู้คืนข้อมูลรายการนี้ได้',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'ลบข้อมูล',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                focusCancel: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    submitDelete();
                }
            });
        });
    });

    if (successMessage) {
        if (window.Swal) {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: successMessage,
                timer: 2600,
                showConfirmButton: false
            });
        }
    }
});
</script>
@endpush
@endsection
