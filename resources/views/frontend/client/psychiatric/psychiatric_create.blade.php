@extends('admin_client.admin_client')

@section('content')
@php
    /*
     * สถานะข้อมูลและตัวกรอง ใช้ร่วมกันทั้ง Header, Filter และ Empty state
     * URL ที่มี start_date/end_date เป็นค่าว่าง จะไม่ถือว่าเปิดตัวกรอง
     */
    $hasPsychiatricRows = isset($psychiatrics) && $psychiatrics->isNotEmpty();

    $hasActivePsychiatricFilter = request()->filled('start_date')
        || request()->filled('end_date')
        || filled(old('start_date'))
        || filled(old('end_date'));

    $psychiatricFilterErrorBag = $errors->getBag('filters');
    $hasPsychiatricFilterErrors = $psychiatricFilterErrorBag->has('start_date')
        || $psychiatricFilterErrorBag->has('end_date')
        || (blank(old('_form_context')) && (
            $errors->has('start_date') || $errors->has('end_date')
        ));

    /* เปิดตัวกรองอัตโนมัติเฉพาะเมื่อมีค่าค้นหา หรือ Validation ตัวกรองผิดพลาด */
    $showPsychiatricFilter = $hasActivePsychiatricFilter || $hasPsychiatricFilterErrors;
    $canShowPsychiatricFilter = $hasPsychiatricRows
        || $hasActivePsychiatricFilter
        || $hasPsychiatricFilterErrors;

    $showPsychiatricFirstEmptyState = !$hasPsychiatricRows
        && !$hasActivePsychiatricFilter
        && !$hasPsychiatricFilterErrors;

    /* สิทธิ์รายฟอร์ม: ตัวกรองยังใช้งานได้ในโหมดอ่านอย่างเดียว */
    $permissionUser = auth()->user();

    $canPsychiatricCreate = (bool) ($permissionUser?->canCreateForm('health_psychiatric') ?? false);

    $canPsychiatricUpdate = (bool) ($permissionUser?->canUpdateForm('health_psychiatric') ?? false);

    $canPsychiatricDelete = (bool) ($permissionUser?->canDeleteForm('health_psychiatric') ?? false);

    $canPsychiatricPrint = (bool) ($permissionUser?->canPrintForm('health_psychiatric') ?? false);
@endphp

<div class="container-fluid mt-2 psychiatric-page">
    <div class="card shadow-sm border-0 psychiatric-card psychiatric-header-card mb-3">
        @include('frontend.client.psychiatric.partials.header')
    </div>

    @if($showPsychiatricFirstEmptyState)
        <section class="psychiatric-empty-card" aria-labelledby="psychiatricEmptyTitle">
            <div class="psychiatric-empty-content">
                <div class="psychiatric-empty-icon" aria-hidden="true">
                    <i class="bi bi-clipboard2-pulse"></i>
                </div>

                <h5 class="psychiatric-empty-title" id="psychiatricEmptyTitle">
                    ยังไม่มีข้อมูลการตรวจวินิจฉัยทางจิตเวช
                </h5>

                <p class="psychiatric-empty-description">
                    เริ่มต้นบันทึกผลการส่งตรวจ การวินิจฉัย การรักษา และการติดตามของผู้รับบริการรายนี้
                </p>

                @if($canPsychiatricCreate)
                    <button type="button"
                            class="btn psychiatric-empty-add-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#createPsychiatricModal"
                            data-permission-action="create"
                            id="btn-create-psychiatric">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มข้อมูลการตรวจจิตเวชครั้งแรก</span>
                    </button>
                @endif
            </div>
        </section>
    @else
        <div class="card shadow-sm border-0 psychiatric-card">
            <div class="card-body p-2 p-md-3">
                @include('frontend.client.psychiatric.partials._client_info')
                @include('frontend.client.psychiatric.partials._table')
            </div>
        </div>
    @endif
</div>

@include('frontend.client.psychiatric.partials._create_modal')
@include('frontend.client.psychiatric.partials._edit_modal')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/assets/css/psychiatric.css') }}">
<style>
    .psychiatric-page {
        padding-bottom: 2rem;
    }

    .psychiatric-page .psychiatric-header-card,
    .psychiatric-page .psychiatric-empty-card {
        background: #ffffff;
        border: 1px solid #e2e8f0 !important;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.045) !important;
        overflow: hidden;
    }

    .psychiatric-page .psychiatric-empty-card {
        min-height: 320px;
        display: grid;
        place-items: center;
        padding: 42px 24px;
    }

    .psychiatric-page .psychiatric-empty-content {
        width: min(680px, 100%);
        text-align: center;
    }

    .psychiatric-page .psychiatric-empty-icon {
        width: 82px;
        height: 82px;
        margin: 0 auto 18px;
        display: grid;
        place-items: center;
        color: #2563eb;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 50%;
        font-size: 34px;
    }

    .psychiatric-page .psychiatric-empty-title {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: 19px;
        font-weight: 900;
        line-height: 1.45;
    }

    .psychiatric-page .psychiatric-empty-description {
        max-width: 650px;
        margin: 0 auto 22px;
        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
    }

    .psychiatric-page .psychiatric-empty-add-btn {
        min-height: 44px;
        padding: 10px 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: #ffffff;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        border: 0;
        border-radius: 12px;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
        font-weight: 800;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .psychiatric-page .psychiatric-empty-add-btn:hover,
    .psychiatric-page .psychiatric-empty-add-btn:focus {
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 11px 22px rgba(37, 99, 235, 0.28);
    }

    .psychiatric-page .psychiatric-empty-add-btn:active {
        transform: translateY(0);
    }

    /* Modal ต้องอยู่เหนือ header/sidebar และเลื่อนภายในได้ */
    #createPsychiatricModal,
    #editPsychiatricModal {
        z-index: 2147483000 !important;
    }

    body.psychiatric-modal-open .modal-backdrop {
        z-index: 2147482990 !important;
    }

    #createPsychiatricModal .modal-dialog,
    #editPsychiatricModal .modal-dialog {
        width: calc(100% - 2rem);
        max-width: 1080px;
        height: calc(100dvh - 2rem);
        margin: 1rem auto;
    }

    #createPsychiatricModal .modal-content,
    #editPsychiatricModal .modal-content,
    #createPsychiatricModal .psy-modal-form,
    #editPsychiatricModal .psy-modal-form {
        display: flex;
        width: 100%;
        height: 100%;
        min-height: 0;
        flex-direction: column;
        overflow: hidden;
    }

    #createPsychiatricModal .modal-body,
    #editPsychiatricModal .modal-body {
        min-height: 0;
        flex: 1 1 auto;
        overflow-x: hidden;
        overflow-y: auto;
        overscroll-behavior: contain;
    }

    .psy-drug-field.is-hidden {
        display: none !important;
    }

    /* ใช้ตัวครอบตารางเป็นจุดเลื่อนแนวนอนเพียงจุดเดียว */
    .psychiatric-page .psy-inline-table-wrap {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    .psychiatric-page .psy-inline-table {
        width: 100% !important;
        min-width: 1180px;
        margin: 0 !important;
    }

    .psychiatric-page .psy-inline-action-group {
        overflow: visible !important;
    }

    .psychiatric-page .psy-col-actions {
        position: static !important;
        right: auto !important;
        box-shadow: none !important;
    }

    /* ป้องกัน DataTable สร้าง scrollbar ซ้อน */
    .psychiatric-page .dataTables_scrollBody {
        overflow-x: visible !important;
        overflow-y: visible !important;
    }

    @media (min-width: 1400px) {
        .psychiatric-page .psy-inline-table {
            min-width: 100%;
            table-layout: fixed;
        }
    }

    @media (max-width: 767.98px) {
        .psychiatric-page .psychiatric-empty-card {
            min-height: 290px;
            padding: 34px 18px;
        }

        .psychiatric-page .psychiatric-empty-icon {
            width: 72px;
            height: 72px;
            font-size: 30px;
        }

        .psychiatric-page .psychiatric-empty-title {
            font-size: 17px;
        }

        .psychiatric-page .psychiatric-empty-add-btn {
            width: 100%;
            max-width: 340px;
        }

        #createPsychiatricModal,
        #editPsychiatricModal {
            padding: 0 !important;
        }

        #createPsychiatricModal .modal-dialog,
        #editPsychiatricModal .modal-dialog {
            width: 100%;
            max-width: none;
            height: 100dvh;
            margin: 0;
        }

        #createPsychiatricModal .modal-content,
        #editPsychiatricModal .modal-content {
            height: 100dvh;
            border-radius: 0;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    window.psychiatricConfig = {
        editJsonUrl: @json(url('/psychiatric/edit-json')),
        updateBaseUrl: @json(url('/psychiatric')),
        today: @json(now('Asia/Bangkok')->toDateString())
    };
</script>
@include('frontend.client.psychiatric.partials._script_init')
@endpush