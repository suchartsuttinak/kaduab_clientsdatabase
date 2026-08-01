@extends('admin_client.admin_client')

@section('title', 'ข้อมูลการตรวจสุขภาพ')

@section('content')
@php
    $hasRows = isset($healthcHeckups) && $healthcHeckups->count() > 0;
    $hasAnyRows = isset($hasAnyHealthcHeckups)
        ? (bool) $hasAnyHealthcHeckups
        : $hasRows;

    $hasFilter = request()->filled('keyword')
        || request()->filled('client_id')
        || request()->filled('date_from')
        || request()->filled('date_to')
        || request()->filled('checkup_result');

    // เมื่อระบบยังไม่มีข้อมูลเลย ให้ซ่อนตัวกรองและตาราง
    // แต่หากกำลังกรองแล้วไม่พบข้อมูล ให้คงตัวกรองไว้เพื่อแก้ไขหรือล้างค่า
    $showListingSection = $hasAnyRows || $hasFilter;
    $today = now('Asia/Bangkok')->toDateString();
    $healthcFormErrors = $errors->getBag('healthcForm');
    $healthcFilterErrors = $errors->getBag('healthcFilter');
    $healthcFormHasErrors = $healthcFormErrors->any();

    $healthcOldValues = [
        'client_id' => (string) old('client_id', ''),
        'checkup_date' => old('checkup_date', ''),
        'hospital_name' => old('hospital_name', ''),
        'checkup_result' => old('checkup_result', 'normal'),
        'abnormal_detail' => old('abnormal_detail', ''),
    ];

    $clientName = static function ($client): string {
        $name = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));

        $preferred = trim((string) ($client->fullname ?? $client->full_name ?? ''));

        return $preferred !== ''
            ? $preferred
            : ($name !== '' ? $name : '-');
    };

    $documentUrl = static function (?string $path): ?string {
        if (!$path) {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', $path), '/');

        if (
            str_starts_with($normalized, 'upload/')
            || str_starts_with($normalized, 'storage/')
        ) {
            return asset($normalized);
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($normalized);
    };
@endphp

<style>
    .healthc-page {
        --hc-primary: #2563eb;
        --hc-primary-dark: #1d4ed8;
        --hc-primary-soft: #eff6ff;
        --hc-success: #15803d;
        --hc-success-soft: #f0fdf4;
        --hc-danger: #b91c1c;
        --hc-danger-soft: #fef2f2;
        --hc-warning: #b45309;
        --hc-warning-soft: #fffbeb;
        --hc-text: #172033;
        --hc-muted: #64748b;
        --hc-border: #dbe4ee;
        --hc-border-soft: #e8eef5;
        --hc-surface: #ffffff;
        --hc-surface-soft: #f8fafc;
        color: var(--hc-text);
    }

    .healthc-page *,
    #healthcHeckupModal *,
    #healthcHeckupModal *::before,
    #healthcHeckupModal *::after {
        box-sizing: border-box;
    }

    .healthc-page .healthc-shell {
        width: 100%;
        max-width: 1500px;
        margin: 0 auto;
    }

    .healthc-page .healthc-header,
    .healthc-page .healthc-filter-card,
    .healthc-page .healthc-table-card,
    .healthc-page .healthc-empty-card {
        border: 1px solid var(--hc-border-soft);
        background: var(--hc-surface);
        box-shadow: 0 7px 22px rgba(15, 23, 42, .06);
    }

    .healthc-page .healthc-header {
        position: relative;
        display: flex;
        overflow: hidden;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.25rem 1.4rem;
        border-radius: 22px;
        background:
            radial-gradient(circle at top right, rgba(59, 130, 246, .14), transparent 36%),
            linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    }

    .healthc-page .healthc-header::after {
        content: "";
        position: absolute;
        right: -72px;
        bottom: -98px;
        width: 210px;
        height: 210px;
        border: 36px solid rgba(37, 99, 235, .05);
        border-radius: 50%;
        pointer-events: none;
    }

    .healthc-page .healthc-header-left,
    .healthc-page .healthc-header-right {
        position: relative;
        z-index: 1;
    }

    .healthc-page .healthc-header-left {
        display: flex;
        min-width: 0;
        align-items: flex-start;
        gap: .85rem;
    }

    .healthc-page .healthc-header-icon {
        display: inline-flex;
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        align-items: center;
        justify-content: center;
        border: 1px solid #bfdbfe;
        border-radius: 16px;
        background: var(--hc-primary-soft);
        color: var(--hc-primary);
        font-size: 1.35rem;
    }

    .healthc-page .healthc-title {
        margin: 0;
        color: #0f172a;
        font-size: clamp(1.2rem, 2vw, 1.55rem);
        font-weight: 800;
        line-height: 1.35;
    }

    .healthc-page .healthc-subtitle {
        max-width: 780px;
        margin-top: .28rem;
        color: var(--hc-muted);
        font-size: .88rem;
        line-height: 1.7;
    }

    .healthc-page .healthc-header-right {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: .6rem;
    }

    .healthc-page .healthc-btn {
        display: inline-flex;
        min-height: 43px;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        padding: .62rem .95rem;
        border-radius: 11px;
        font-size: .84rem;
        font-weight: 750;
        line-height: 1.2;
        text-decoration: none;
        box-shadow: none;
        transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease, border-color .18s ease;
    }

    .healthc-page .healthc-btn:hover {
        transform: translateY(-1px);
    }

    .healthc-page .healthc-btn-primary {
        border: 1px solid var(--hc-primary);
        background: var(--hc-primary);
        color: #fff;
    }

    .healthc-page .healthc-btn-primary:hover {
        border-color: var(--hc-primary-dark);
        background: var(--hc-primary-dark);
        color: #fff;
        box-shadow: 0 8px 18px rgba(37, 99, 235, .2);
    }

    .healthc-page .healthc-btn-secondary {
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #475569;
    }

    .healthc-page .healthc-btn-secondary:hover {
        border-color: #94a3b8;
        background: #f8fafc;
        color: #1e293b;
    }

    .healthc-page .healthc-alert {
        border-radius: 14px;
    }

    .healthc-page .healthc-filter-card,
    .healthc-page .healthc-table-card {
        overflow: hidden;
        border-radius: 18px;
    }

    .healthc-page .healthc-filter-card .card-body,
    .healthc-page .healthc-table-card .card-body {
        padding: 1rem 1.1rem;
    }

    .healthc-page .healthc-filter-title {
        margin: 0 0 .85rem;
        color: #0f172a;
        font-size: .95rem;
        font-weight: 800;
    }

    .healthc-page .form-label {
        margin-bottom: .38rem;
        color: #334155;
        font-size: .81rem;
        font-weight: 700;
    }

    .healthc-page .form-control,
    .healthc-page .form-select {
        min-height: 43px;
        border: 1px solid var(--hc-border);
        border-radius: 11px;
        background-color: #fff;
        color: var(--hc-text);
        font-size: .84rem;
        box-shadow: none;
    }

    .healthc-page .form-control:focus,
    .healthc-page .form-select:focus {
        border-color: var(--hc-primary);
        box-shadow: 0 0 0 .18rem rgba(37, 99, 235, .1);
    }

    .healthc-page .invalid-feedback {
        color: #dc2626;
        font-size: .74rem;
        font-weight: 650;
    }

    .healthc-page .healthc-table-wrap {
        overflow-x: auto;
        border: 1px solid var(--hc-border-soft);
        border-radius: 13px;
        -webkit-overflow-scrolling: touch;
    }

    .healthc-page .healthc-table {
        min-width: 1180px;
        margin: 0;
        table-layout: fixed;
    }

    .healthc-page .healthc-table thead th {
        padding: .72rem .68rem;
        border-color: var(--hc-border);
        background: #f8fafc;
        color: #334155;
        font-size: .77rem;
        font-weight: 800;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    .healthc-page .healthc-table tbody td {
        padding: .72rem .68rem;
        border-color: #edf1f6;
        color: #334155;
        font-size: .81rem;
        line-height: 1.55;
        vertical-align: middle;
        overflow-wrap: anywhere;
    }

    .healthc-page .healthc-table tbody tr:hover {
        background: #fbfdff;
    }

    .healthc-page .healthc-status {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .34rem .58rem;
        border: 1px solid transparent;
        border-radius: 999px;
        font-size: .73rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .healthc-page .healthc-status-normal {
        border-color: #bbf7d0;
        background: var(--hc-success-soft);
        color: var(--hc-success);
    }

    .healthc-page .healthc-status-abnormal {
        border-color: #fecaca;
        background: var(--hc-danger-soft);
        color: var(--hc-danger);
    }

    .healthc-page .healthc-row-actions {
        display: flex;
        flex-wrap: nowrap;
        justify-content: center;
        gap: .4rem;
        white-space: nowrap;
    }

    .healthc-page .healthc-icon-btn {
        display: inline-flex;
        width: 37px;
        height: 37px;
        padding: 0;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    .healthc-page .healthc-empty-card {
        border-radius: 22px;
        background:
            radial-gradient(circle at top, rgba(59, 130, 246, .1), transparent 44%),
            #fff;
    }

    .healthc-page .healthc-empty-body {
        padding: 3.2rem 1.25rem;
        text-align: center;
    }

    .healthc-page .healthc-empty-icon {
        display: inline-flex;
        width: 82px;
        height: 82px;
        margin-bottom: 1rem;
        align-items: center;
        justify-content: center;
        border: 1px solid #bfdbfe;
        border-radius: 50%;
        background: var(--hc-primary-soft);
        color: var(--hc-primary);
        font-size: 2rem;
    }

    .healthc-page .healthc-empty-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.13rem;
        font-weight: 800;
    }

    .healthc-page .healthc-empty-text {
        max-width: 650px;
        margin: .45rem auto 1.15rem;
        color: var(--hc-muted);
        font-size: .86rem;
        line-height: 1.75;
    }

    #healthcHeckupModal {
        --hcm-primary: #2563eb;
        --hcm-primary-dark: #1d4ed8;
        --hcm-primary-soft: #eff6ff;
        --hcm-danger: #dc2626;
        --hcm-danger-soft: #fff7f7;
        --hcm-text: #172033;
        --hcm-muted: #64748b;
        --hcm-border: #dbe4ee;
        --hcm-border-soft: #e8eef5;
        z-index: 2147483000 !important;
        padding-right: 0 !important;
        color: var(--hcm-text);
    }

    body.healthc-modal-active {
        overflow: hidden;
    }

    body.healthc-modal-active .modal-backdrop {
        z-index: 2147482990 !important;
    }

    #healthcHeckupModal .modal-dialog {
        width: calc(100% - 2rem);
        max-width: 820px;
        height: calc(100dvh - 2rem);
        min-height: 0;
        margin: 1rem auto;
    }

    #healthcHeckupModal .modal-content {
        display: flex;
        width: 100%;
        height: 100%;
        min-height: 0;
        overflow: hidden;
        border: 0;
        border-radius: 21px;
        background: #fff;
        box-shadow: 0 30px 80px rgba(15, 23, 42, .24), 0 8px 24px rgba(15, 23, 42, .12);
    }

    #healthcHeckupModal .healthc-modal-form {
        display: flex;
        width: 100%;
        min-height: 0;
        flex: 1 1 auto;
        flex-direction: column;
    }

    #healthcHeckupModal .modal-header {
        flex: 0 0 auto;
        padding: 1rem 1.2rem;
        border-bottom: 1px solid var(--hcm-border-soft);
        background:
            radial-gradient(circle at top right, rgba(59, 130, 246, .13), transparent 42%),
            linear-gradient(135deg, #ffffff 0%, #f7fbff 100%);
    }

    #healthcHeckupModal .healthc-modal-heading {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: .72rem;
    }

    #healthcHeckupModal .healthc-modal-icon {
        display: inline-flex;
        width: 43px;
        height: 43px;
        flex: 0 0 43px;
        align-items: center;
        justify-content: center;
        border: 1px solid #bfdbfe;
        border-radius: 13px;
        background: var(--hcm-primary-soft);
        color: var(--hcm-primary);
        font-size: 1.1rem;
    }

    #healthcHeckupModal .modal-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.03rem;
        font-weight: 800;
    }

    #healthcHeckupModal .healthc-modal-subtitle {
        margin: .14rem 0 0;
        color: var(--hcm-muted);
        font-size: .76rem;
        line-height: 1.5;
    }

    #healthcHeckupModal .healthc-modal-body {
        min-height: 0;
        flex: 1 1 auto;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 1rem 1.15rem 1.2rem;
        background: #f8fafc;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
    }

    #healthcHeckupModal .healthc-form-section {
        padding: 1rem;
        border: 1px solid var(--hcm-border-soft);
        border-radius: 15px;
        background: #fff;
    }

    #healthcHeckupModal .form-label {
        display: inline-flex;
        align-items: center;
        gap: .2rem;
        margin-bottom: .4rem;
        color: #334155;
        font-size: .81rem;
        font-weight: 700;
    }

    #healthcHeckupModal .form-control,
    #healthcHeckupModal .form-select {
        min-height: 44px;
        border: 1px solid var(--hcm-border);
        border-radius: 11px;
        color: var(--hcm-text);
        font-size: .84rem;
        box-shadow: none;
    }

    #healthcHeckupModal textarea.form-control {
        min-height: 105px;
        line-height: 1.6;
        resize: vertical;
    }

    #healthcHeckupModal .form-control:focus,
    #healthcHeckupModal .form-select:focus {
        border-color: var(--hcm-primary);
        box-shadow: 0 0 0 .18rem rgba(37, 99, 235, .1);
    }

    #healthcHeckupModal .form-control.is-invalid,
    #healthcHeckupModal .was-validated .form-control:invalid,
    #healthcHeckupModal .was-validated .form-select:invalid {
        border-color: var(--hcm-danger) !important;
        background-color: var(--hcm-danger-soft);
        box-shadow: 0 0 0 .16rem rgba(220, 38, 38, .07) !important;
    }

    #healthcHeckupModal .invalid-feedback {
        display: block;
        color: var(--hcm-danger);
        font-size: .73rem;
        font-weight: 650;
        line-height: 1.45;
    }

    #healthcHeckupModal .healthc-result-box {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .65rem;
    }

    #healthcHeckupModal .healthc-result-option {
        position: relative;
    }

    #healthcHeckupModal .healthc-result-option input {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        opacity: 0;
    }

    #healthcHeckupModal .healthc-result-label {
        display: flex;
        min-height: 58px;
        margin: 0;
        padding: .72rem .8rem;
        align-items: center;
        gap: .6rem;
        border: 1px solid var(--hcm-border);
        border-radius: 12px;
        background: #fff;
        color: #334155;
        cursor: pointer;
        font-size: .82rem;
        font-weight: 750;
    }

    #healthcHeckupModal .healthc-result-option input:checked + .healthc-result-label {
        border-color: var(--hcm-primary);
        background: var(--hcm-primary-soft);
        color: var(--hcm-primary-dark);
        box-shadow: 0 0 0 .15rem rgba(37, 99, 235, .07);
    }

    #healthcHeckupModal .healthc-result-option input:focus-visible + .healthc-result-label {
        box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .12);
    }

    #healthcHeckupModal .healthc-conditional-panel {
        margin-top: .8rem;
        padding: .85rem;
        border: 1px dashed #cbd5e1;
        border-radius: 13px;
        background: #fbfdff;
    }

    #healthcHeckupModal .healthc-conditional-panel.d-none {
        display: none !important;
    }

    #healthcHeckupModal .healthc-modal-footer {
        display: flex;
        flex: 0 0 auto;
        align-items: center;
        justify-content: flex-end;
        gap: .6rem;
        padding: .82rem 1.15rem;
        border-top: 1px solid var(--hcm-border-soft);
        background: #fff;
    }

    /*
    |--------------------------------------------------------------------------
    | ปุ่มด้านล่าง Modal
    |--------------------------------------------------------------------------
    */
    #healthcHeckupModal .healthc-modal-footer .healthc-btn {
        position: relative;
        display: inline-flex;
        min-width: 142px;
        min-height: 46px;
        padding: .7rem 1.05rem;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        border-radius: 13px;
        font-size: .86rem;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: .01em;
        text-decoration: none;
        box-shadow: none;
        transition:
            transform .18s ease,
            box-shadow .18s ease,
            background-color .18s ease,
            border-color .18s ease,
            color .18s ease;
    }

    #healthcHeckupModal .healthc-modal-footer .healthc-btn i {
        display: inline-flex;
        width: 25px;
        height: 25px;
        flex: 0 0 25px;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: .9rem;
        line-height: 1;
    }

    #healthcHeckupModal .healthc-modal-btn-close {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        box-shadow: 0 3px 10px rgba(15, 23, 42, .05);
    }

    #healthcHeckupModal .healthc-modal-btn-close i {
        background: #f1f5f9;
        color: #64748b;
    }

    #healthcHeckupModal .healthc-modal-btn-close:hover {
        border-color: #94a3b8;
        background: #f8fafc;
        color: #0f172a;
        transform: translateY(-1px);
        box-shadow: 0 7px 16px rgba(15, 23, 42, .09);
    }

    #healthcHeckupModal .healthc-modal-btn-close:hover i {
        background: #e2e8f0;
        color: #334155;
    }

    #healthcHeckupModal .healthc-modal-btn-save {
        border: 1px solid #1d4ed8;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        box-shadow: 0 7px 17px rgba(37, 99, 235, .22);
    }

    #healthcHeckupModal .healthc-modal-btn-save i {
        background: rgba(255, 255, 255, .16);
        color: #ffffff;
    }

    #healthcHeckupModal .healthc-modal-btn-save:hover {
        border-color: #1e40af;
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(37, 99, 235, .28);
    }

    #healthcHeckupModal .healthc-modal-btn-close:active,
    #healthcHeckupModal .healthc-modal-btn-save:active {
        transform: translateY(0);
        box-shadow: none;
    }

    #healthcHeckupModal .healthc-modal-btn-close:focus-visible,
    #healthcHeckupModal .healthc-modal-btn-save:focus-visible {
        outline: none;
        box-shadow: 0 0 0 .22rem rgba(37, 99, 235, .16);
    }

    #healthcHeckupModal .healthc-submit-btn:disabled {
        cursor: default;
        opacity: 1;
        transform: none;
        pointer-events: none;
        border-color: #1d4ed8;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        box-shadow: 0 7px 17px rgba(37, 99, 235, .18);
    }

    @media (max-width: 991.98px) {
        .healthc-page .healthc-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .healthc-page .healthc-header-right {
            justify-content: flex-start;
        }
    }

    @media (max-width: 767.98px) {
        .healthc-page {
            padding-right: .65rem !important;
            padding-left: .65rem !important;
        }

        .healthc-page .healthc-header {
            padding: 1.05rem;
            border-radius: 18px;
        }

        .healthc-page .healthc-header-right,
        .healthc-page .healthc-header-right .healthc-btn {
            width: 100%;
        }

        #healthcHeckupModal .modal-dialog {
            width: 100%;
            max-width: none;
            height: 100dvh;
            margin: 0;
        }

        #healthcHeckupModal .modal-content {
            height: 100dvh;
            border-radius: 0;
        }

        #healthcHeckupModal .modal-header {
            padding: calc(.9rem + env(safe-area-inset-top)) .9rem .9rem;
        }

        #healthcHeckupModal .healthc-modal-body {
            padding: .85rem;
        }

        #healthcHeckupModal .healthc-modal-footer {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            padding: .75rem .85rem calc(.75rem + env(safe-area-inset-bottom));
        }

        #healthcHeckupModal .healthc-modal-footer .healthc-btn {
            width: 100%;
            min-width: 0;
        }
    }

    @media (max-width: 575.98px) {
        .healthc-page .healthc-header-icon {
            width: 46px;
            height: 46px;
            flex-basis: 46px;
        }

        #healthcHeckupModal .healthc-result-box {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container-fluid py-3 healthc-page">
    <div class="healthc-shell">
        <header class="healthc-header mb-3">
            <div class="healthc-header-left">
                <div class="healthc-header-icon" aria-hidden="true">
                    <i class="bi bi-heart-pulse"></i>
                </div>

                <div>
                    <h1 class="healthc-title">ข้อมูลการตรวจสุขภาพประจำปี</h1>
                    <div class="healthc-subtitle">
                        จัดเก็บผลตรวจสุขภาพ เอกสารทางการแพทย์ และติดตามผลที่ผิดปกติอย่างเป็นระบบ
                    </div>
                </div>
            </div>

            @if($hasAnyRows)
                <div class="healthc-header-right">
                    <button type="button"
                            class="btn healthc-btn healthc-btn-primary"
                            onclick="openCreateModal()">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มข้อมูล</span>
                    </button>

                    <a href="{{ route('healthc_heckups.report', request()->query()) }}"
                       target="_blank"
                       class="healthc-btn healthc-btn-secondary">
                        <i class="bi bi-printer"></i>
                        <span>รายงาน</span>
                    </a>
                </div>
            @endif
        </header>

        @if($healthcFormHasErrors)
            <div class="alert alert-danger alert-dismissible fade show healthc-alert mb-3">
                <strong>กรุณาตรวจสอบข้อมูลในแบบฟอร์ม</strong>
                <ul class="mb-0 mt-2 ps-3">
                    @foreach($healthcFormErrors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="ปิด"></button>
            </div>
        @endif

        @if($showListingSection)
            <section class="card border-0 healthc-filter-card mb-3">
                <div class="card-body">
                    <h2 class="healthc-filter-title">ค้นหาและกรองข้อมูล</h2>

                    <form method="GET" action="{{ route('healthc_heckups.index') }}">
                        <div class="row g-3">
                            <div class="col-12 col-md-6 col-xl-3">
                                <label class="form-label" for="healthc_keyword">ชื่อผู้รับบริการ / สถานพยาบาล / รายละเอียด</label>
                                <input type="text"
                                       name="keyword"
                                       id="healthc_keyword"
                                       class="form-control @error('keyword', 'healthcFilter') is-invalid @enderror"
                                       value="{{ request('keyword') }}"
                                       maxlength="255"
                                       placeholder="พิมพ์คำค้นหา">
                                @error('keyword', 'healthcFilter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 col-xl-3">
                                <label class="form-label" for="healthc_client_filter">ผู้รับบริการ</label>
                                <select name="client_id"
                                        id="healthc_client_filter"
                                        class="form-select @error('client_id', 'healthcFilter') is-invalid @enderror">
                                    <option value="">-- ทั้งหมด --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}"
                                                {{ (string) request('client_id') === (string) $client->id ? 'selected' : '' }}>
                                            {{ $clientName($client) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id', 'healthcFilter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 col-xl-2">
                                <label class="form-label" for="healthc_date_from">วันที่เริ่มต้น</label>
                                <input type="date"
                                       name="date_from"
                                       id="healthc_date_from"
                                       class="form-control @error('date_from', 'healthcFilter') is-invalid @enderror"
                                       value="{{ request('date_from') }}"
                                       max="{{ $today }}">
                                @error('date_from', 'healthcFilter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 col-xl-2">
                                <label class="form-label" for="healthc_date_to">วันที่สิ้นสุด</label>
                                <input type="date"
                                       name="date_to"
                                       id="healthc_date_to"
                                       class="form-control @error('date_to', 'healthcFilter') is-invalid @enderror"
                                       value="{{ request('date_to') }}"
                                       min="{{ request('date_from') }}"
                                       max="{{ $today }}">
                                @error('date_to', 'healthcFilter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 col-xl-2">
                                <label class="form-label" for="healthc_result_filter">ผลการตรวจ</label>
                                <select name="checkup_result"
                                        id="healthc_result_filter"
                                        class="form-select @error('checkup_result', 'healthcFilter') is-invalid @enderror">
                                    <option value="">-- ทั้งหมด --</option>
                                    <option value="normal" {{ request('checkup_result') === 'normal' ? 'selected' : '' }}>ปกติ</option>
                                    <option value="abnormal" {{ request('checkup_result') === 'abnormal' ? 'selected' : '' }}>ไม่ปกติ</option>
                                </select>
                                @error('checkup_result', 'healthcFilter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="submit" class="btn healthc-btn healthc-btn-primary">
                                        <i class="bi bi-search"></i>
                                        <span>ค้นหา</span>
                                    </button>

                                    <a href="{{ route('healthc_heckups.index') }}"
                                       class="healthc-btn healthc-btn-secondary">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                        <span>ล้างค่า</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

            <section class="card border-0 healthc-table-card">
                <div class="card-body">
                    <div class="healthc-table-wrap">
                        <table class="table table-bordered align-middle healthc-table">
                            <thead>
                                <tr>
                                    <th style="width:65px;">ลำดับ</th>
                                    <th style="width:175px;">ชื่อ-สกุล</th>
                                    <th style="width:115px;">วันที่ตรวจ</th>
                                    <th style="width:175px;">สถานพยาบาล</th>
                                    <th style="width:105px;">ผลการตรวจ</th>
                                    <th style="width:235px;">รายละเอียด</th>
                                    <th style="width:110px;">เอกสาร</th>
                                    <th style="width:145px;">ผู้บันทึก</th>
                                    <th style="width:130px;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($healthcHeckups as $index => $item)
                                    @php($itemDocumentUrl = $documentUrl($item->medical_document))
                                    <tr>
                                        <td class="text-center">
                                            {{ ($healthcHeckups->firstItem() ?? 1) + $index }}
                                        </td>
                                        <td>{{ $clientName($item->client) }}</td>
                                        <td class="text-center">
                                            {{ \App\Helpers\ThaiDateHelper::formatThaiShort($item->checkup_date) }}
                                        </td>
                                        <td>{{ $item->hospital_name }}</td>
                                        <td class="text-center">
                                            @if($item->checkup_result === 'normal')
                                                <span class="healthc-status healthc-status-normal">
                                                    <i class="bi bi-check-circle"></i> ปกติ
                                                </span>
                                            @else
                                                <span class="healthc-status healthc-status-abnormal">
                                                    <i class="bi bi-exclamation-circle"></i> ไม่ปกติ
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $item->abnormal_detail ?: '-' }}</td>
                                        <td class="text-center">
                                            @if($itemDocumentUrl)
                                                <a href="{{ $itemDocumentUrl }}"
                                                   target="_blank"
                                                   rel="noopener noreferrer"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-file-earmark-pdf"></i> เปิดไฟล์
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->recorder->name ?? '-' }}</td>
                                        <td class="text-center">
                                            <div class="healthc-row-actions">
                                                <button type="button"
                                                        class="btn btn-warning healthc-icon-btn"
                                                        onclick="openEditModal({{ $item->id }})"
                                                        aria-label="แก้ไขข้อมูล"
                                                        title="แก้ไขข้อมูล">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <form action="{{ route('healthc_heckups.delete', $item->id) }}"
                                                      method="POST"
                                                      class="js-delete-healthc-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-danger healthc-icon-btn"
                                                            aria-label="ลบข้อมูล"
                                                            title="ลบข้อมูล">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            ไม่พบข้อมูลที่ตรงกับเงื่อนไขการค้นหา
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($healthcHeckups->hasPages())
                        <div class="mt-3">
                            {{ $healthcHeckups->links() }}
                        </div>
                    @endif
                </div>
            </section>
        @else
            <section class="healthc-empty-card">
                <div class="healthc-empty-body">
                    <div class="healthc-empty-icon" aria-hidden="true">
                        <i class="bi bi-heart-pulse"></i>
                    </div>

                    <h2 class="healthc-empty-title">ยังไม่มีข้อมูลการตรวจสุขภาพ</h2>
                    <p class="healthc-empty-text">
                        เริ่มบันทึกผลการตรวจสุขภาพประจำปี สถานพยาบาล ผลการตรวจ
                        และเอกสารทางการแพทย์ เพื่อใช้ประกอบการติดตามดูแลผู้รับบริการ
                    </p>

                    <button type="button"
                            class="btn healthc-btn healthc-btn-primary"
                            onclick="openCreateModal()">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มข้อมูลการตรวจสุขภาพครั้งแรก</span>
                    </button>
                </div>
            </section>
        @endif
    </div>
</div>

<div class="modal fade"
     id="healthcHeckupModal"
     tabindex="-1"
     aria-labelledby="healthcHeckupModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="healthcHeckupForm"
                  method="POST"
                  enctype="multipart/form-data"
                  class="healthc-modal-form"
                  novalidate>
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="_form_context" id="formContext" value="healthc_create">
                <input type="hidden" name="_edit_id" id="editId" value="">

                <div class="modal-header">
                    <div class="healthc-modal-heading">
                        <div class="healthc-modal-icon" aria-hidden="true">
                            <i class="bi bi-clipboard2-heart"></i>
                        </div>
                        <div>
                            <h2 class="modal-title" id="healthcHeckupModalLabel">เพิ่มข้อมูลการตรวจสุขภาพ</h2>
                            <p class="healthc-modal-subtitle">กรอกข้อมูลที่มีเครื่องหมาย * ให้ครบถ้วนก่อนบันทึก</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>

                <div class="healthc-modal-body">
                    <div class="healthc-form-section">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="client_search">
                                    ผู้รับบริการ <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       id="client_search"
                                       class="form-control {{ $healthcFormErrors->has('client_id') ? 'is-invalid' : '' }}"
                                       list="client_list"
                                       placeholder="พิมพ์และเลือกชื่อผู้รับบริการ"
                                       autocomplete="off"
                                       required>

                                <datalist id="client_list">
                                    @foreach($clients as $client)
                                        <option value="{{ $clientName($client) }}" data-id="{{ $client->id }}"></option>
                                    @endforeach
                                </datalist>

                                <input type="hidden" name="client_id" id="client_id" value="{{ old('client_id') }}">
                                <div class="form-text">พิมพ์ชื่อ แล้วเลือกชื่อจากรายการที่ระบบแสดง</div>
                                @error('client_id', 'healthcForm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="checkup_date">
                                    วันที่ตรวจสุขภาพ <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       name="checkup_date"
                                       id="checkup_date"
                                       class="form-control @error('checkup_date', 'healthcForm') is-invalid @enderror"
                                       value="{{ old('checkup_date') }}"
                                       max="{{ $today }}"
                                       required>
                                @error('checkup_date', 'healthcForm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="hospital_name">
                                    สถานพยาบาล <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="hospital_name"
                                       id="hospital_name"
                                       class="form-control @error('hospital_name', 'healthcForm') is-invalid @enderror"
                                       value="{{ old('hospital_name') }}"
                                       maxlength="255"
                                       placeholder="ระบุชื่อโรงพยาบาลหรือสถานพยาบาล"
                                       required>
                                @error('hospital_name', 'healthcForm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label d-block">
                                    ผลการตรวจสุขภาพ <span class="text-danger">*</span>
                                </label>

                                <div class="healthc-result-box">
                                    <div class="healthc-result-option">
                                        <input type="radio"
                                               name="checkup_result"
                                               id="result_normal"
                                               value="normal"
                                               {{ old('checkup_result', 'normal') === 'normal' ? 'checked' : '' }}
                                               required>
                                        <label class="healthc-result-label" for="result_normal">
                                            <i class="bi bi-check-circle"></i>
                                            <span>ผลตรวจปกติ</span>
                                        </label>
                                    </div>

                                    <div class="healthc-result-option">
                                        <input type="radio"
                                               name="checkup_result"
                                               id="result_abnormal"
                                               value="abnormal"
                                               {{ old('checkup_result') === 'abnormal' ? 'checked' : '' }}
                                               required>
                                        <label class="healthc-result-label" for="result_abnormal">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            <span>ผลตรวจไม่ปกติ</span>
                                        </label>
                                    </div>
                                </div>

                                @error('checkup_result', 'healthcForm')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 healthc-conditional-panel d-none" id="abnormalDetailWrap">
                                <label class="form-label" for="abnormal_detail">
                                    รายละเอียดผลตรวจที่ผิดปกติ <span class="text-danger">*</span>
                                </label>
                                <textarea name="abnormal_detail"
                                          id="abnormal_detail"
                                          rows="4"
                                          class="form-control @error('abnormal_detail', 'healthcForm') is-invalid @enderror"
                                          maxlength="3000"
                                          placeholder="ระบุความผิดปกติ คำแนะนำ หรือแนวทางติดตาม">{{ old('abnormal_detail') }}</textarea>
                                @error('abnormal_detail', 'healthcForm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="medical_document">เอกสารทางการแพทย์ (PDF)</label>
                                <input type="file"
                                       name="medical_document"
                                       id="medical_document"
                                       class="form-control @error('medical_document', 'healthcForm') is-invalid @enderror"
                                       accept=".pdf,application/pdf">
                                <div class="form-text">อัปโหลดได้เฉพาะไฟล์ PDF ขนาดไม่เกิน 5 MB</div>
                                @error('medical_document', 'healthcForm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div id="currentFileBlock" class="mt-2 d-none">
                                    <a href="#"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       id="currentFileLink"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        <span id="currentFileText">เปิดไฟล์ปัจจุบัน</span>
                                    </a>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">ผู้บันทึก</label>
                                <input type="text"
                                       class="form-control"
                                       value="{{ auth()->user()->name ?? '-' }}"
                                       readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="healthc-modal-footer">
                    <button type="button"
                            class="healthc-btn healthc-modal-btn-close"
                            data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i>
                        <span>ปิด</span>
                    </button>

                    <button type="submit"
                            class="healthc-btn healthc-modal-btn-save healthc-submit-btn">
                        <i class="bi bi-check2"></i>
                        <span id="submitButtonText">บันทึกข้อมูล</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalElement = document.getElementById('healthcHeckupModal');
        const form = document.getElementById('healthcHeckupForm');

        if (!modalElement || !form) {
            return;
        }

        if (modalElement.parentElement !== document.body) {
            document.body.appendChild(modalElement);
        }

        const modal = bootstrap.Modal.getOrCreateInstance(modalElement, {
            backdrop: 'static',
            keyboard: false
        });

        const clientSearch = document.getElementById('client_search');
        const clientId = document.getElementById('client_id');
        const checkupDate = document.getElementById('checkup_date');
        const hospitalName = document.getElementById('hospital_name');
        const resultNormal = document.getElementById('result_normal');
        const resultAbnormal = document.getElementById('result_abnormal');
        const abnormalWrap = document.getElementById('abnormalDetailWrap');
        const abnormalDetail = document.getElementById('abnormal_detail');
        const medicalDocument = document.getElementById('medical_document');
        const currentFileBlock = document.getElementById('currentFileBlock');
        const currentFileLink = document.getElementById('currentFileLink');
        const currentFileText = document.getElementById('currentFileText');
        const formMethod = document.getElementById('formMethod');
        const formContext = document.getElementById('formContext');
        const editId = document.getElementById('editId');
        const submitButton = form.querySelector('button[type="submit"]');
        const submitButtonText = document.getElementById('submitButtonText');
        const modalBody = modalElement.querySelector('.healthc-modal-body');
        const clientOptions = Array.from(document.querySelectorAll('#client_list option'));
        const oldValues = @json($healthcOldValues);
        const hasFormErrors = @json($healthcFormHasErrors);
        const oldFormContext = @json(old('_form_context'));
        const oldEditId = @json(old('_edit_id'));

        function selectedResult() {
            const selected = form.querySelector('input[name="checkup_result"]:checked');
            return selected ? selected.value : '';
        }

        function toggleAbnormalDetail(clearHidden) {
            const show = selectedResult() === 'abnormal';

            abnormalWrap.classList.toggle('d-none', !show);
            abnormalWrap.setAttribute('aria-hidden', show ? 'false' : 'true');
            abnormalDetail.required = show;

            if (!show && clearHidden) {
                abnormalDetail.value = '';
                abnormalDetail.classList.remove('is-invalid');
            }
        }

        function findClientOptionById(id) {
            return clientOptions.find(function (option) {
                return String(option.dataset.id) === String(id);
            });
        }

        function syncClientIdFromName() {
            const value = (clientSearch.value || '').trim();
            const option = clientOptions.find(function (item) {
                return item.value.trim() === value;
            });

            clientId.value = option ? option.dataset.id : '';
            return Boolean(clientId.value);
        }

        function clearFormValidation() {
            form.classList.remove('was-validated');
            form.querySelectorAll('.is-invalid').forEach(function (field) {
                field.classList.remove('is-invalid');
            });
        }

        function resetCurrentFile() {
            currentFileBlock.classList.add('d-none');
            currentFileLink.href = '#';
            currentFileText.textContent = 'เปิดไฟล์ปัจจุบัน';
        }

        function showCurrentFile(url, name) {
            if (!url) {
                resetCurrentFile();
                return;
            }

            currentFileBlock.classList.remove('d-none');
            currentFileLink.href = url;
            currentFileText.textContent = name ? 'เปิดไฟล์ปัจจุบัน: ' + name : 'เปิดไฟล์ปัจจุบัน';
        }

        function applyValues(values) {
            const clientOption = findClientOptionById(values.client_id);

            clientId.value = values.client_id || '';
            clientSearch.value = clientOption ? clientOption.value : '';
            checkupDate.value = values.checkup_date || '';
            hospitalName.value = values.hospital_name || '';
            abnormalDetail.value = values.abnormal_detail || '';

            if (values.checkup_result === 'abnormal') {
                resultAbnormal.checked = true;
            } else {
                resultNormal.checked = true;
            }

            toggleAbnormalDetail(false);
        }

        function setCreateMode(values, preserveValidation) {
            form.reset();

            if (!preserveValidation) {
                clearFormValidation();
            }
            form.dataset.submitting = 'false';
            submitButton.disabled = false;
            medicalDocument.value = '';
            resetCurrentFile();

            document.getElementById('healthcHeckupModalLabel').textContent = 'เพิ่มข้อมูลการตรวจสุขภาพ';
            submitButtonText.textContent = 'บันทึกข้อมูล';
            form.action = @json(route('healthc_heckups.store'));
            formMethod.value = 'POST';
            formContext.value = 'healthc_create';
            editId.value = '';
            clientSearch.readOnly = false;
            clientSearch.removeAttribute('aria-readonly');

            resultNormal.checked = true;
            toggleAbnormalDetail(true);

            if (values) {
                applyValues(values);
            }
        }

        function setEditMode(data, overrideValues, preserveValidation) {
            if (!preserveValidation) {
                clearFormValidation();
            }
            form.dataset.submitting = 'false';
            submitButton.disabled = false;
            medicalDocument.value = '';

            document.getElementById('healthcHeckupModalLabel').textContent = 'แก้ไขข้อมูลการตรวจสุขภาพ';
            submitButtonText.textContent = 'อัปเดตข้อมูล';
            form.action = @json(url('healthc-checkups/update')) + '/' + data.id;
            formMethod.value = 'PUT';
            formContext.value = 'healthc_edit';
            editId.value = data.id;
            clientSearch.readOnly = true;
            clientSearch.setAttribute('aria-readonly', 'true');

            applyValues(overrideValues || data);
            showCurrentFile(data.medical_document_url, data.medical_document_name);
        }

        window.openCreateModal = function (values, preserveValidation) {
            setCreateMode(values || null, Boolean(preserveValidation));
            modal.show();
        };

        window.openEditModal = function (id, overrideValues, preserveValidation) {
            fetch(@json(url('healthc-checkups/edit-json')) + '/' + id, {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('ไม่สามารถโหลดข้อมูลสำหรับแก้ไขได้');
                    }
                    return response.json();
                })
                .then(function (data) {
                    setEditMode(data, overrideValues || null, Boolean(preserveValidation));
                    modal.show();
                })
                .catch(function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'ไม่สามารถเปิดข้อมูลได้',
                        text: 'กรุณารีเฟรชหน้าแล้วลองใหม่อีกครั้ง',
                        confirmButtonText: 'ตกลง'
                    });
                });
        };

        function validatePdfFile() {
            const file = medicalDocument.files && medicalDocument.files[0];

            if (!file) {
                medicalDocument.classList.remove('is-invalid');
                return true;
            }

            const extension = (file.name.split('.').pop() || '').toLowerCase();
            const isPdf = extension === 'pdf'
                && (!file.type || file.type === 'application/pdf' || file.type === 'application/x-pdf');
            const isWithinSize = file.size <= 5 * 1024 * 1024;

            if (!isPdf) {
                medicalDocument.value = '';
                medicalDocument.classList.add('is-invalid');
                Swal.fire({
                    icon: 'warning',
                    title: 'ชนิดไฟล์ไม่ถูกต้อง',
                    text: 'กรุณาเลือกเอกสารทางการแพทย์เป็นไฟล์ PDF เท่านั้น',
                    confirmButtonText: 'ตกลง'
                });
                return false;
            }

            if (!isWithinSize) {
                medicalDocument.value = '';
                medicalDocument.classList.add('is-invalid');
                Swal.fire({
                    icon: 'warning',
                    title: 'ไฟล์มีขนาดใหญ่เกินไป',
                    text: 'เอกสารทางการแพทย์ต้องมีขนาดไม่เกิน 5 MB',
                    confirmButtonText: 'ตกลง'
                });
                return false;
            }

            medicalDocument.classList.remove('is-invalid');
            return true;
        }

        function firstInvalidMessage(field) {
            if (field === clientSearch) {
                return 'กรุณาพิมพ์และเลือกชื่อผู้รับบริการจากรายการที่ระบบแสดง';
            }
            if (field === checkupDate) {
                return 'กรุณาระบุวันที่ตรวจสุขภาพที่ไม่เกินวันปัจจุบัน';
            }
            if (field === hospitalName) {
                return 'กรุณาระบุชื่อสถานพยาบาล';
            }
            if (field === abnormalDetail) {
                return 'กรุณาระบุรายละเอียดผลตรวจที่ผิดปกติ';
            }
            return 'กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน';
        }

        resultNormal.addEventListener('change', function () {
            toggleAbnormalDetail(true);
        });

        resultAbnormal.addEventListener('change', function () {
            toggleAbnormalDetail(false);
        });

        clientSearch.addEventListener('input', function () {
            if (!clientSearch.readOnly) {
                syncClientIdFromName();
            }
            clientSearch.classList.remove('is-invalid');
        });

        clientSearch.addEventListener('change', function () {
            if (!clientSearch.readOnly) {
                syncClientIdFromName();
            }
        });

        medicalDocument.addEventListener('change', validatePdfFile);

        form.querySelectorAll('.form-control, .form-select').forEach(function (field) {
            field.addEventListener('input', function () {
                field.classList.remove('is-invalid');
            });
            field.addEventListener('change', function () {
                field.classList.remove('is-invalid');
            });
        });

        form.addEventListener('submit', function (event) {
            if (form.dataset.submitting === 'true') {
                event.preventDefault();
                return;
            }

            if (!clientSearch.readOnly) {
                syncClientIdFromName();
            }

            toggleAbnormalDetail(false);
            form.classList.add('was-validated');

            let firstInvalid = null;

            if (!clientId.value) {
                clientSearch.classList.add('is-invalid');
                firstInvalid = clientSearch;
            } else if (!checkupDate.value || checkupDate.value > @json($today)) {
                checkupDate.classList.add('is-invalid');
                firstInvalid = checkupDate;
            } else if (!hospitalName.value.trim()) {
                hospitalName.classList.add('is-invalid');
                firstInvalid = hospitalName;
            } else if (selectedResult() === 'abnormal' && !abnormalDetail.value.trim()) {
                abnormalDetail.classList.add('is-invalid');
                firstInvalid = abnormalDetail;
            } else if (!validatePdfFile()) {
                firstInvalid = medicalDocument;
            } else if (!form.checkValidity()) {
                firstInvalid = form.querySelector(':invalid');
            }

            if (firstInvalid) {
                event.preventDefault();
                event.stopPropagation();

                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณาตรวจสอบข้อมูล',
                    text: firstInvalidMessage(firstInvalid),
                    confirmButtonText: 'ตกลง'
                }).then(function () {
                    try {
                        firstInvalid.focus({ preventScroll: true });
                    } catch (error) {
                        firstInvalid.focus();
                    }

                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                });

                return;
            }

            form.dataset.submitting = 'true';
            submitButton.disabled = true;
        });

        modalElement.addEventListener('show.bs.modal', function () {
            document.body.classList.add('healthc-modal-active');
        });

        modalElement.addEventListener('shown.bs.modal', function () {
            if (modalBody) {
                modalBody.scrollTop = 0;
            }

            const firstInvalid = form.querySelector('.is-invalid');
            const firstField = firstInvalid || clientSearch;

            if (firstField) {
                try {
                    firstField.focus({ preventScroll: true });
                } catch (error) {
                    firstField.focus();
                }
            }
        });

        modalElement.addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('healthc-modal-active');
            form.dataset.submitting = 'false';
            submitButton.disabled = false;
            form.classList.remove('was-validated');
        });

        const filterDateFrom = document.getElementById('healthc_date_from');
        const filterDateTo = document.getElementById('healthc_date_to');

        if (filterDateFrom && filterDateTo) {
            filterDateFrom.addEventListener('change', function () {
                filterDateTo.min = filterDateFrom.value || '';

                if (filterDateFrom.value && filterDateTo.value && filterDateTo.value < filterDateFrom.value) {
                    filterDateTo.value = filterDateFrom.value;
                }
            });
        }

        document.querySelectorAll('.js-delete-healthc-form').forEach(function (deleteForm) {
            deleteForm.addEventListener('submit', function (event) {
                event.preventDefault();

                Swal.fire({
                    title: 'ยืนยันการลบข้อมูล',
                    text: 'ข้อมูลและเอกสารที่แนบจะถูกลบ และไม่สามารถกู้คืนได้',
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
                        deleteForm.submit();
                    }
                });
            });
        });

        toggleAbnormalDetail(false);

        if (hasFormErrors) {
            if (oldFormContext === 'healthc_edit' && oldEditId) {
                window.openEditModal(oldEditId, oldValues, true);
            } else {
                window.openCreateModal(oldValues, true);
            }
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'ดำเนินการสำเร็จ',
                text: @json(session('success')),
                timer: 2800,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        @endif
    });
</script>
@endpush