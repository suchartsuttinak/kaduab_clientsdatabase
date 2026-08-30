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


    /* ========================================
       Observe Modal scroll hotfix
       ให้ form/fieldset ส่งต่อความสูงไปยัง modal-body
       เพื่อเลื่อนได้จนถึงส่วนท้ายของฟอร์ม
    ======================================== */
    .observe-main-modal .modal-content {
        max-height: calc(100vh - 2rem);
        overflow: hidden;
    }

    .observe-main-modal .observe-modal-form {
        display: flex;
        flex: 1 1 auto;
        flex-direction: column;
        min-height: 0;
        overflow: hidden;
    }

    .observe-main-modal .observe-modal-form > fieldset {
        display: flex;
        flex: 1 1 auto;
        flex-direction: column;
        min-width: 0;
        min-height: 0;
        margin: 0;
        padding: 0;
        border: 0;
        overflow: hidden;
    }

    .observe-main-modal .observe-modal-form > fieldset > .modal-body {
        flex: 1 1 auto;
        min-height: 0;
        overflow-x: hidden;
        overflow-y: auto;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
    }

    .observe-main-modal .observe-modal-fixed-footer {
        flex: 0 0 auto;
    }

    @media (max-width: 767.98px) {
        .observe-main-modal .modal-content {
            max-height: calc(100vh - 1rem);
        }
    }

    /* ========================================
       Workflow / Risk / Referral
       Scope ใช้กับ Observe Modal เท่านั้น
    ======================================== */
    .observe-modal .observe-workflow-section {
        margin-top: 1rem;
        border: 1px solid #dbe5f0;
        border-radius: 16px;
        padding: 16px;
        background: #fbfdff;
    }

    .observe-modal .observe-workflow-readonly {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .observe-modal .observe-workflow-readonly__item {
        min-width: 0;
        padding: 12px 14px;
        border: 1px solid #e5eaf0;
        border-radius: 12px;
        background: #fff;
    }

    .observe-modal .observe-workflow-readonly__item span,
    .observe-modal .observe-workflow-readonly__item strong,
    .observe-modal .observe-workflow-readonly__item small {
        display: block;
    }

    .observe-modal .observe-workflow-readonly__item span {
        margin-bottom: 4px;
        color: #64748b;
        font-size: .82rem;
        font-weight: 700;
    }

    .observe-modal .observe-workflow-readonly__item strong {
        color: #0f172a;
        font-size: .95rem;
        line-height: 1.55;
    }

    .observe-modal .observe-workflow-readonly__item small {
        margin-top: 5px;
        color: #64748b;
        line-height: 1.55;
    }

    .observe-modal .observe-workflow-readonly__wide {
        grid-column: 1 / -1;
    }

    .observe-modal .observe-referral-panel {
        padding: 16px;
        border: 1px solid #f0c36c;
        border-radius: 14px;
        background: #fffbeb;
    }

    .observe-modal .observe-referral-panel__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 4px;
        color: #92400e;
    }

    .observe-modal .observe-referral-panel__header > div {
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .observe-modal .observe-referral-panel__header > span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 9px;
        border-radius: 999px;
        background: #fff7d6;
        border: 1px solid #f5d78e;
        font-size: .78rem;
        font-weight: 800;
    }

    .observe-modal .observe-referral-panel__note {
        color: #9a6b16;
        font-size: .82rem;
        line-height: 1.55;
    }

    .observe-modal .observe-referral-restricted {
        grid-column: 1 / -1;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f8fafc;
        color: #475569;
    }

    .observe-modal .observe-referral-restricted > i {
        margin-top: 2px;
        color: #64748b;
    }

    .observe-modal .observe-referral-restricted strong {
        display: block;
        margin-bottom: 3px;
        color: #334155;
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

    @media (max-width: 767.98px) {
        .observe-modal .observe-workflow-readonly {
            grid-template-columns: 1fr;
        }

        .observe-modal .observe-workflow-readonly__wide {
            grid-column: auto;
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

    if (!$modalToOpen && isset($observe) && $observe && ($canManageObserveReferral ?? false)) {
        $bagName = 'referralStore' . $observe->id;
        if ($errors->getBag($bagName)->any()) {
            $modalToOpen = 'addReferralRoundModal' . $observe->id;
            $activeErrors = collect($errors->getBag($bagName)->all());
        }
    }

    if (!$modalToOpen && isset($observe) && $observe && ($canManageObserveReferral ?? false) && $observe->relationLoaded('referralRounds')) {
        foreach ($observe->referralRounds as $referralItem) {
            $bagName = 'referralUpdate' . $referralItem->id;
            if ($errors->getBag($bagName)->any()) {
                $modalToOpen = 'editReferralRoundModal' . $referralItem->id;
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
                            @php
                                $topLatestFollowup = isset($observe) && $observe ? $observe->followups->last() : null;
                                $topCurrentStatus = isset($observe) && $observe
                                    ? ($topLatestFollowup->status ?? $observe->status ?? 'ongoing')
                                    : 'ongoing';
                            @endphp
                            <i class="bi {{ isset($observe) && $observe ? ($topCurrentStatus === 'referred' ? 'bi-eye' : 'bi-pencil-square') : 'bi-plus-circle' }}"></i>
                            {{ isset($observe) && $observe ? ($topCurrentStatus === 'referred' ? 'ดูข้อมูลต้นทาง' : 'แก้ไขข้อมูล') : 'เพิ่มข้อมูลใหม่' }}
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


    function initObserveWorkflow(form) {
        if (!form) return;

        const riskSelect = form.querySelector('.js-observe-risk-level');
        const riskWrap = form.querySelector('.js-observe-risk-detail-wrap');
        const riskDetail = form.querySelector('.js-observe-risk-detail');
        const riskRequired = form.querySelector('.js-observe-risk-detail-required');

        function syncRisk() {
            if (!riskSelect || !riskWrap) return;

            const hasRisk = riskSelect.value !== 'none';
            const needsDetail = ['moderate', 'high'].includes(riskSelect.value);

            riskWrap.style.display = hasRisk ? '' : 'none';

            if (riskDetail) {
                riskDetail.required = needsDetail;
            }

            if (riskRequired) {
                riskRequired.style.display = needsDetail ? '' : 'none';
            }
        }

        if (riskSelect) {
            riskSelect.addEventListener('change', syncRisk);
            syncRisk();
        }

        const statusSelect = form.querySelector('.js-observe-workflow-status');
        const nextWrap = form.querySelector('.js-observe-next-wrap');
        const nextDate = form.querySelector('.js-observe-next-date');
        const followupFocus = form.querySelector('.js-observe-followup-focus');

        function syncStatus() {
            if (!statusSelect) return;

            const isOngoing = statusSelect.value === 'ongoing';

            if (nextWrap) {
                nextWrap.style.display = isOngoing ? '' : 'none';
            }

            if (nextDate) {
                nextDate.required = isOngoing;
            }

            if (followupFocus) {
                followupFocus.required = isOngoing;
            }
        }

        if (statusSelect) {
            statusSelect.addEventListener('change', syncStatus);
            syncStatus();
        }
    }

    function initReferralWorkflow(form) {
        if (!form) return;

        const riskSelect = form.querySelector('.js-referral-risk-level');
        const riskWrap = form.querySelector('.js-referral-risk-detail-wrap');
        const riskDetail = form.querySelector('.js-referral-risk-detail');
        const riskRequired = form.querySelector('.js-referral-risk-required');

        function syncReferralRisk() {
            if (!riskSelect || !riskWrap) return;
            const hasRisk = riskSelect.value !== 'none';
            const needsDetail = ['moderate', 'high'].includes(riskSelect.value);
            riskWrap.style.display = hasRisk ? '' : 'none';
            if (riskDetail) riskDetail.required = needsDetail;
            if (riskRequired) riskRequired.style.display = needsDetail ? '' : 'none';
        }

        if (riskSelect) {
            riskSelect.addEventListener('change', syncReferralRisk);
            syncReferralRisk();
        }

        const status = form.querySelector('.js-referral-status');
        const nextWrap = form.querySelector('.js-referral-next-wrap');
        const nextDate = form.querySelector('.js-referral-next-date');
        const focus = form.querySelector('.js-referral-followup-focus');

        function syncReferralStatus() {
            if (!status) return;
            const ongoing = status.value === 'ongoing';
            if (nextWrap) nextWrap.style.display = ongoing ? '' : 'none';
            if (nextDate) nextDate.required = ongoing;
            if (focus) focus.required = ongoing;
        }

        if (status) {
            status.addEventListener('change', syncReferralStatus);
            syncReferralStatus();
        }
    }

    document.querySelectorAll('.observe-submit-form').forEach(function (form) {
        initObserveWorkflow(form);
        initReferralWorkflow(form);
    });

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
