@extends('admin_client.admin_client')

@section('content')
<link rel="stylesheet" href="{{ asset('backend/assets/css/escape.css') }}">
<link rel="stylesheet" href="{{ asset('backend/assets/css/escape-responsive-fix.css') }}">

<div class="container-fluid escape-page">
    <div class="escape-shell">
        @include('frontend.client.escape.partials._header')

        @if ($escapes->isNotEmpty())
            @include('frontend.client.escape.partials.client_info')
            @include('frontend.client.escape.partials._table')
        @else
            <div class="escape-first-empty-card" role="status" aria-live="polite">
                <div class="escape-first-empty-card__icon" aria-hidden="true">
                    <i class="bi bi-box-arrow-right"></i>
                </div>

                <h5 class="escape-first-empty-card__title">
                    ยังไม่มีข้อมูลการออกจากสถานสงเคราะห์
                </h5>

                <p class="escape-first-empty-card__description">
                    กรุณาเพิ่มข้อมูลการออกจากสถานสงเคราะห์ครั้งแรก
                </p>

                <button type="button"
                        class="btn escape-btn escape-btn--primary escape-first-empty-card__button"
                        data-bs-toggle="modal"
                        data-bs-target="#escapeCreateModal">
                    <i class="bi bi-plus-circle"></i>
                    <span>เพิ่มข้อมูลการออกครั้งแรก</span>
                </button>
            </div>
        @endif
    </div>
</div>

{{-- Modal ต้องอยู่นอกตารางและนอกส่วนที่อาจมี overflow/transform --}}
@include('frontend.client.escape.partials.escapeCreateModal')
@include('frontend.client.escape.partials.escapeEditModal')

<style>
.escape-page .escape-first-empty-card {
    min-height: 360px;
    padding: 3.5rem 1.25rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
}

.escape-page .escape-first-empty-card__icon {
    width: 82px;
    height: 82px;
    margin-bottom: 1.25rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #eef4ff;
    color: #2563eb;
    font-size: 2rem;
}

.escape-page .escape-first-empty-card__title {
    margin: 0 0 .45rem;
    color: #0f172a;
    font-size: 1.15rem;
    font-weight: 800;
}

.escape-page .escape-first-empty-card__description {
    margin: 0 0 1.25rem;
    color: #64748b;
    font-size: .95rem;
}

.escape-page .escape-first-empty-card__button {
    min-width: 230px;
    justify-content: center;
}

@media (max-width: 767.98px) {
    .escape-page .escape-first-empty-card {
        min-height: 320px;
        padding: 2.5rem 1rem;
        border-radius: 16px;
    }

    .escape-page .escape-first-empty-card__button {
        width: 100%;
        min-width: 0;
        max-width: 360px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalElements = document.querySelectorAll('.escape-module-modal, .escape-modal');

    modalElements.forEach(function (modalElement) {
        if (modalElement.parentElement !== document.body) {
            document.body.appendChild(modalElement);
        }

        modalElement.addEventListener('shown.bs.modal', function () {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            const backdrop = backdrops[backdrops.length - 1];
            backdrop?.classList.add('escape-module-backdrop');
        });
    });

    document.querySelectorAll('.escape-submit-form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const button = form.querySelector('button[type="submit"]');
            if (!button || button.disabled) return;

            button.disabled = true;
            button.dataset.originalHtml = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span>กำลังบันทึก...</span>';
        });
    });

    @if ($errors->any() && old('form_context'))
        const formContext = @json(old('form_context'));
        let targetId = null;

        if (formContext === 'escape-create') {
            targetId = 'escapeCreateModal';
        } else if (formContext.startsWith('escape-edit-')) {
            targetId = 'escapeEditModal' + formContext.replace('escape-edit-', '');
        }

        if (targetId) {
            const targetModal = document.getElementById(targetId);
            if (targetModal && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(targetModal).show();
            }
        }
    @endif
});
</script>
@endsection