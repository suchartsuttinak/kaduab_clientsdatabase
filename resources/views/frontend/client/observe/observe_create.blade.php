@extends('admin_client.admin_client')

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/assets/css/observe.css') }}">
<link rel="stylesheet" href="{{ asset('backend/assets/css/observe-responsive-fix.css') }}">

<style>
    /* ========================================
       Empty state รูปแบบเดียวกับ /idstation
    ======================================== */
    .observe-page .observe-empty-card {
        min-height: 320px;
        margin-top: 1rem;
        padding: 2rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background: #ffffff;
        border: 1px solid #dbe3ef;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }

    .observe-page .observe-empty-content {
        width: 100%;
        max-width: 760px;
        margin: 0 auto;
    }

    .observe-page .observe-empty-icon {
        width: 82px;
        height: 82px;
        margin: 0 auto 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #bfdbfe;
        border-radius: 50%;
        background: #eff6ff;
        color: #2563eb;
        font-size: 2rem;
    }

    .observe-page .observe-empty-title {
        margin: 0 0 .55rem;
        color: #0f172a;
        font-size: 1.2rem;
        font-weight: 800;
    }

    .observe-page .observe-empty-description {
        margin: 0 auto 1.25rem;
        color: #64748b;
        font-size: .95rem;
        line-height: 1.7;
    }

    .observe-page .observe-empty-add-btn {
        min-height: 44px;
        padding: .7rem 1.2rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        border: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        font-weight: 800;
        box-shadow: 0 9px 20px rgba(37, 99, 235, .22);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .observe-page .observe-empty-add-btn:hover,
    .observe-page .observe-empty-add-btn:focus {
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 12px 24px rgba(37, 99, 235, .28);
    }

    @media (max-width: 767.98px) {
        .observe-page .observe-empty-card {
            min-height: 285px;
            padding: 1.5rem 1rem;
        }

        .observe-page .observe-empty-icon {
            width: 72px;
            height: 72px;
            font-size: 1.75rem;
        }

        .observe-page .observe-empty-title {
            font-size: 1.08rem;
        }

        .observe-page .observe-empty-add-btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
@php
    $modalToOpen = $openModal ?? null;
    $activeErrors = collect();
    $hasObserveData = $observes->isNotEmpty();

    if ($errors->getBag('observeForm')->any()) {
        $modalToOpen = 'observeModal';
        $activeErrors = collect($errors->getBag('observeForm')->all());
    }

    if (!$modalToOpen) {
        foreach ($observes as $obsItem) {
            $bagName = 'followupStore' . $obsItem->id;

            if ($errors->getBag($bagName)->any()) {
                $modalToOpen = 'addFollowupModal' . $obsItem->id;
                $activeErrors = collect($errors->getBag($bagName)->all());
                break;
            }
        }
    }

    if (!$modalToOpen && isset($observe) && $observe) {
        foreach ($observe->followups as $followupItem) {
            $bagName = 'followupUpdate' . $followupItem->id;

            if ($errors->getBag($bagName)->any()) {
                $modalToOpen = 'editFollowupModal' . $followupItem->id;
                $activeErrors = collect($errors->getBag($bagName)->all());
                break;
            }
        }
    }
@endphp

<div class="observe-page">
    <div class="observe-shell">

        <div class="observe-topbar">
            <div class="observe-title-wrap">
                <div class="observe-title-box">
                    <div class="observe-title-icon">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>
                    <div>
                        <h1 class="observe-title">ข้อมูลการบันทึกและติดตามพฤติกรรม</h1>
                        <p class="observe-subtitle">
                           <p class="co-empty-header-subtitle">
                        ผู้รับบริการ:
                        <strong>{{ $client->fullname ?? $client->name ?? '-' }}</strong>
                         <span class="mx-1">•</span>
                            อายุ: <strong>{{ is_numeric($client->age ?? null) ? $client->age . ' ปี' : '-' }}</strong>
                    </p>
                        </p>
                    </div>
                </div>

                <div class="observe-actions">
                    {{-- เมื่อมีข้อมูลแล้ว จึงแสดงปุ่มเพิ่ม/แก้ไขด้านบน --}}
                    @if($hasObserveData)
                        <button type="button"
                                class="btn-modern {{ isset($observe) && $observe ? 'btn-modern-warning' : 'btn-modern-primary' }}"
                                data-bs-toggle="modal"
                                data-bs-target="#observeModal">
                            <i class="bi {{ isset($observe) && $observe ? 'bi-pencil-square' : 'bi-plus-circle' }}"></i>
                            {{ isset($observe) && $observe ? 'แก้ไขข้อมูล' : 'เพิ่มข้อมูลใหม่' }}
                        </button>
                    @endif

                    <a href="{{ route('admin.index', $client->id) }}"
                       class="btn-modern btn-modern-danger text-decoration-none"
                       aria-label="ปิดฟอร์มและกลับหน้าหลักผู้รับบริการ">
                        <i class="bi bi-x-circle"></i>
                        ปิดฟอร์ม
                    </a>
                </div>
            </div>
        </div>

        @if($hasObserveData)
            {{-- @include('frontend.client.observe.partials.summary') --}}
            @include('frontend.client.observe.partials._table')
        @else
            <section class="observe-empty-card" aria-labelledby="observeEmptyTitle">
                <div class="observe-empty-content">
                    <div class="observe-empty-icon" aria-hidden="true">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>

                    <h2 id="observeEmptyTitle" class="observe-empty-title">
                        ยังไม่มีข้อมูลการบันทึกและติดตามพฤติกรรม
                    </h2>

                    <p class="observe-empty-description">
                        เริ่มต้นบันทึกข้อมูลพฤติกรรมครั้งแรก โดยระบุวันที่เกิดเหตุ สภาพปัญหา
                        รายละเอียดพฤติกรรม แนวทางแก้ไข การดำเนินการ และผลลัพธ์ให้ครบถ้วน
                    </p>

                    <button type="button"
                            class="observe-empty-add-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#observeModal">
                        <i class="bi bi-plus-circle"></i>
                        เพิ่มข้อมูลพฤติกรรมครั้งแรก
                    </button>
                </div>
            </section>
        @endif
    </div>

    {{-- ต้องคง Modal ไว้เสมอ เพื่อให้ปุ่มใน Empty state เปิดฟอร์มได้ --}}
    @include('frontend.client.observe.partials.observeModal')
    @include('frontend.client.observe.partials.editFollowupModal')
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /*
     * ย้าย Modal ไปไว้ใต้ <body> โดยตรง
     * ป้องกัน fixed header / sidebar / parent overflow หรือ transform บัง Modal
     */
    const observeModals = Array.from(document.querySelectorAll('.observe-modal'));

    observeModals.forEach(function (modalElement) {
        if (modalElement.parentElement !== document.body) {
            document.body.appendChild(modalElement);
        }

        modalElement.addEventListener('show.bs.modal', function () {
            document.body.classList.add('observe-modal-active');
        });

        modalElement.addEventListener('hidden.bs.modal', function () {
            const hasOpenObserveModal = document.querySelector('.observe-modal.show');

            if (!hasOpenObserveModal) {
                document.body.classList.remove('observe-modal-active');
                document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
                    backdrop.remove();
                });
            }
        });
    });

    const modalId = @json($modalToOpen);
    const errors = @json($activeErrors->values());

    if (modalId && window.bootstrap) {
        const modalElement = document.getElementById(modalId);

        if (modalElement) {
            bootstrap.Modal.getOrCreateInstance(modalElement).show();
        }
    }

    if (errors.length > 0 && window.Swal) {
        const list = document.createElement('ul');
        list.style.textAlign = 'left';
        list.style.paddingLeft = '1.2rem';
        list.style.marginBottom = '0';

        errors.forEach(function (message) {
            const item = document.createElement('li');
            item.textContent = message;
            list.appendChild(item);
        });

        Swal.fire({
            icon: 'error',
            title: 'กรุณาตรวจสอบข้อมูล',
            html: list,
            confirmButtonText: 'ตกลง'
        });
    }

    document.querySelectorAll('.observe-submit-form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const button = form.querySelector('[data-submit-button]');

            if (!button || button.disabled) {
                return;
            }

            button.disabled = true;
            button.setAttribute('aria-disabled', 'true');

            const label = button.querySelector('[data-submit-label]');
            const loadingText = button.dataset.loadingText || 'กำลังบันทึก...';

            if (label) {
                label.textContent = loadingText;
            }

            const icon = button.querySelector('i');
            if (icon) {
                icon.className = 'spinner-border spinner-border-sm';
                icon.setAttribute('aria-hidden', 'true');
            }
        });
    });
});
</script>

@if (session('success') || session('message'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.Swal) {
        Swal.fire({
            icon: 'success',
            title: @json(session('success') ?? session('message')),
            showConfirmButton: true,
            timer: 3000,
            timerProgressBar: true
        });
    }
});
</script>
@endif

@if (session('error'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.Swal) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: @json(session('error')),
            confirmButtonText: 'ตกลง'
        });
    }
});
</script>
@endif
@endsection
