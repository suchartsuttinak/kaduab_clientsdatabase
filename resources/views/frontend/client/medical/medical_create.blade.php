@extends('admin_client.admin_client')

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/assets/css/medical.css') }}">
<style>
    .medical-page {
        --md-border: #e2e8f0;
        --md-border-soft: #edf2f7;
        --md-text: #172033;
        --md-muted: #64748b;
        --md-primary: #1d4ed8;
        --md-primary-soft: #eff6ff;
        --md-surface: #ffffff;
        --md-surface-soft: #f8fafc;
        min-height: auto !important;
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
        padding-top: .75rem !important;
        padding-bottom: calc(2rem + env(safe-area-inset-bottom)) !important;
    }

    .medical-page .md-pagebar,
    .medical-page .md-filter,
    .medical-page .md-table-card {
        background: var(--md-surface);
        border: 1px solid var(--md-border);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, .035);
    }

    .medical-page .md-pagebar {
        min-height: 58px;
        margin-bottom: .65rem;
        padding: .65rem .8rem .65rem .95rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .medical-page .md-pagebar-main {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: .7rem;
    }

    .medical-page .md-title-icon {
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        border: 1px solid #d8dee8;
        border-radius: 9px;
        background: #f8fafc;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        line-height: 1;
    }

    .medical-page .md-title-group {
        min-width: 0;
    }

    .medical-page .md-page-title {
        margin: 0;
        color: var(--md-text);
        font-size: 1.08rem;
        font-weight: 700;
        line-height: 1.25;
    }

    .medical-page .md-page-count {
        margin-top: .12rem;
        color: var(--md-muted);
        font-size: .8rem;
        font-weight: 400;
        line-height: 1.2;
    }

    .medical-page .md-page-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .5rem;
        flex-wrap: wrap;
    }

    .medical-page .md-btn,
    .md-modal .md-btn {
        min-height: 36px;
        padding: .42rem .72rem;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .38rem;
        font-size: .84rem;
        font-weight: 600;
        line-height: 1.15;
        white-space: nowrap;
        box-shadow: none !important;
    }

    /* ปุ่มเฉพาะหัวหน้า: เรียบง่าย สมดุล และแยกหน้าที่ชัดเจน */
    .medical-page .md-page-actions .md-btn {
        min-height: 39px;
        padding: .42rem .78rem .42rem .48rem;
        border-width: 1px;
        border-style: solid;
        border-radius: 10px;
        gap: .46rem;
        font-size: .84rem;
        font-weight: 700;
        letter-spacing: .005em;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .07) !important;
        transition:
            transform .18s ease,
            border-color .18s ease,
            background-color .18s ease,
            color .18s ease,
            box-shadow .18s ease;
    }

    .medical-page .md-page-actions .md-btn-icon {
        width: 26px;
        height: 26px;
        flex: 0 0 26px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        transition: background-color .18s ease, transform .18s ease;
    }

    .medical-page .md-page-actions .md-btn-icon i {
        font-size: .9rem;
        line-height: 1;
    }

    .medical-page .md-page-actions .md-btn:hover {
        transform: translateY(-1px);
    }

    .medical-page .md-page-actions .md-btn:hover .md-btn-icon {
        transform: scale(1.04);
    }

    .medical-page .md-page-actions .md-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 7px rgba(15, 23, 42, .08) !important;
    }

    .medical-page .md-page-actions .md-btn:focus-visible {
        outline: 0;
        box-shadow:
            0 0 0 3px rgba(37, 99, 235, .16),
            0 4px 12px rgba(15, 23, 42, .08) !important;
    }

    .medical-page .md-page-actions .md-btn-filter {
        border-color: #cbd5e1;
        background: #fff;
        color: #334155;
    }

    .medical-page .md-page-actions .md-btn-filter .md-btn-icon {
        background: #f1f5f9;
        color: #475569;
    }

    .medical-page .md-page-actions .md-btn-filter:hover,
    .medical-page .md-page-actions .md-btn-filter:focus,
    .medical-page .md-page-actions .md-btn-filter[aria-expanded="true"] {
        border-color: #93c5fd;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .medical-page .md-page-actions .md-btn-filter:hover .md-btn-icon,
    .medical-page .md-page-actions .md-btn-filter:focus .md-btn-icon,
    .medical-page .md-page-actions .md-btn-filter[aria-expanded="true"] .md-btn-icon {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .medical-page .md-page-actions .md-btn-report {
        border-color: #bfdbfe;
        background: #fff;
        color: #1e40af;
    }

    .medical-page .md-page-actions .md-btn-report .md-btn-icon {
        background: #eff6ff;
        color: #2563eb;
    }

    .medical-page .md-page-actions .md-btn-report:hover,
    .medical-page .md-page-actions .md-btn-report:focus {
        border-color: #60a5fa;
        background: #f8fbff;
        color: #1d4ed8;
        box-shadow: 0 6px 16px rgba(37, 99, 235, .12) !important;
    }

    .medical-page .md-page-actions .md-btn-create {
        border-color: #1d4ed8;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #fff;
        box-shadow: 0 6px 16px rgba(37, 99, 235, .22) !important;
    }

    .medical-page .md-page-actions .md-btn-create .md-btn-icon {
        background: rgba(255, 255, 255, .16);
        color: #fff;
    }

    .medical-page .md-page-actions .md-btn-create:hover,
    .medical-page .md-page-actions .md-btn-create:focus,
    .medical-page .md-page-actions .md-btn-create:active {
        border-color: #1e40af;
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        color: #fff;
        box-shadow: 0 8px 20px rgba(37, 99, 235, .27) !important;
    }

    .medical-page .md-page-actions .md-btn:disabled,
    .medical-page .md-page-actions .md-btn.disabled,
    .medical-page .md-page-actions .md-btn[aria-disabled="true"] {
        transform: none !important;
        box-shadow: none !important;
        opacity: .62;
    }

    .medical-page .md-filter {
        margin-bottom: .65rem;
        padding: .68rem .8rem;
    }

    .medical-page .md-filter-row {
        display: grid;
        grid-template-columns: minmax(155px, 220px) minmax(155px, 220px) minmax(0, 1fr);
        gap: .55rem;
        align-items: end;
    }

    .medical-page .md-filter-label,
    .md-modal .md-form-label {
        margin-bottom: .28rem;
        color: #334155;
        font-size: .79rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: .34rem;
    }

    .medical-page .md-filter-label i,
    .md-modal .md-form-label i,
    .md-modal .md-section-title i {
        color: #64748b;
        font-size: .88em;
        line-height: 1;
    }

    .medical-page .md-filter-control,
    .md-modal .md-form-control {
        min-height: 38px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #fff;
        color: var(--md-text, #172033);
        font-size: .88rem;
        font-weight: 400 !important;
        box-shadow: none !important;
    }

    .medical-page .md-filter-control:focus,
    .md-modal .md-form-control:focus {
        border-color: #93b4f6;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .09) !important;
    }

    .medical-page .md-filter-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: .45rem;
        flex-wrap: wrap;
    }

    .medical-page .md-filter-collapse {
        margin-bottom: .65rem;
    }

    .medical-page .md-filter-collapse:not(.show) {
        margin-bottom: 0;
    }

    .medical-page .md-filter-collapse .md-filter {
        margin-bottom: 0;
    }

    .medical-page .md-filter-toggle {
        border-color: #cbd5e1;
        background: #fff;
        color: #334155;
    }

    .medical-page .md-table-card {
        overflow: hidden;
    }

    .medical-page .md-table-head {
        min-height: 45px;
        padding: .55rem .8rem;
        border-bottom: 1px solid var(--md-border-soft);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .medical-page .md-table-title {
        margin: 0;
        color: var(--md-text);
        font-size: .94rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: .42rem;
    }

    .medical-page .md-table-title i {
        color: #64748b;
        font-size: .92rem;
        line-height: 1;
    }

    .medical-page .md-table-meta {
        color: var(--md-muted);
        font-size: .78rem;
        font-weight: 400;
        white-space: nowrap;
    }

    .medical-page .md-table-body {
        padding: .7rem .8rem .8rem;
    }

    .medical-page .md-table-wrap,
    .medical-page .dataTables_scrollBody {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }

    .medical-page .dataTables_scrollHead {
        overflow: hidden !important;
    }

    .medical-page .md-table {
        width: 100% !important;
        min-width: 980px;
        margin: 0 !important;
        border-color: var(--md-border-soft);
    }

    .medical-page .md-table thead th {
        padding: .66rem .7rem;
        background: #f7f9fc;
        color: #334155;
        border-top: 0;
        border-bottom: 1px solid var(--md-border);
        font-size: .8rem;
        font-weight: 700;
        white-space: nowrap;
        vertical-align: middle;
    }

    .medical-page .md-table tbody td,
    .medical-page .md-table tbody td * {
        font-weight: 400 !important;
    }

    .medical-page .md-table tbody td {
        padding: .68rem .7rem;
        color: #273449;
        border-color: var(--md-border-soft);
        font-size: .85rem;
        line-height: 1.5;
        vertical-align: middle;
    }

    .medical-page .md-table tbody tr:hover {
        background: #fbfdff;
    }

    .medical-page .md-date,
    .medical-page .md-refer {
        color: var(--md-text);
        font-weight: 400 !important;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: .34rem;
    }

    .medical-page .md-date i,
    .medical-page .md-refer i {
        color: #94a3b8;
        font-size: .78rem;
        line-height: 1;
    }

    .medical-page .md-cell-text {
        display: block;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .medical-page .md-cell-clamp {
        display: -webkit-box;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }

    .medical-page .md-action-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        flex-wrap: nowrap;
        white-space: nowrap;
    }

    .medical-page .md-icon-btn {
        width: 34px;
        min-width: 34px;
        height: 34px;
        padding: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        box-shadow: none !important;
    }

    .medical-page .md-col-seq { width: 64px; min-width: 64px; }
    .medical-page .md-col-date { width: 120px; min-width: 120px; }
    .medical-page .md-col-disease { width: 150px; min-width: 150px; }
    .medical-page .md-col-detail { min-width: 190px; }
    .medical-page .md-col-refer { width: 128px; min-width: 128px; }
    .medical-page .md-col-appt { width: 130px; min-width: 130px; }
    .medical-page .md-col-actions { width: 100px; min-width: 100px; }

    .medical-page .md-empty {
        min-height: 170px;
        padding: 1.35rem 1rem;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        background: #fbfcfe;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .medical-page .md-empty-icon {
        width: 42px;
        height: 42px;
        margin-bottom: .65rem;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--md-primary-soft);
        color: var(--md-primary);
        font-size: 1.05rem;
    }

    .medical-page .md-empty-title {
        margin: 0 0 .28rem;
        color: var(--md-text);
        font-size: .96rem;
        font-weight: 700;
    }

    .medical-page .md-empty-text {
        margin: 0 0 .8rem;
        color: var(--md-muted);
        font-size: .84rem;
        font-weight: 400;
    }

    .medical-page .dataTables_wrapper .dataTables_length,
    .medical-page .dataTables_wrapper .dataTables_filter {
        margin-bottom: .55rem;
        color: var(--md-muted);
        font-size: .8rem;
    }

    .medical-page .dataTables_wrapper .dataTables_length select,
    .medical-page .dataTables_wrapper .dataTables_filter input {
        min-height: 34px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        box-shadow: none;
        font-size: .82rem;
    }

    .medical-page .dataTables_wrapper .dataTables_info,
    .medical-page .dataTables_wrapper .dataTables_paginate {
        margin-top: .55rem;
        color: var(--md-muted);
        font-size: .78rem;
    }

    .md-modal {
        --md-border: #e2e8f0;
        --md-border-soft: #edf2f7;
        --md-text: #172033;
        --md-muted: #64748b;
    }

    .md-modal .modal-dialog {
        width: min(920px, calc(100% - 1.5rem));
        max-width: 920px;
        margin-top: .75rem;
        margin-bottom: .75rem;
    }

    .md-modal .modal-content {
        max-height: calc(100vh - 1.5rem);
        max-height: calc(100dvh - 1.5rem);
        border: 1px solid var(--md-border);
        border-radius: 12px;
        box-shadow: 0 14px 40px rgba(15, 23, 42, .14);
        overflow: hidden;
    }

    .md-modal .modal-content > form {
        display: flex;
        flex-direction: column;
        min-height: 0;
        max-height: inherit;
    }

    .md-modal .modal-header {
        min-height: 52px;
        padding: .72rem .9rem;
        border-bottom: 1px solid var(--md-border-soft);
        background: #fff;
        flex: 0 0 auto;
    }

    .md-modal .modal-title {
        margin: 0;
        color: var(--md-text);
        font-size: 1rem;
        font-weight: 700;
        line-height: 1.35;
    }

    .md-modal .modal-body {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: .85rem;
        background: #f8fafc;
    }

    .md-modal .modal-footer {
        min-height: 54px;
        padding: .65rem .85rem;
        border-top: 1px solid var(--md-border-soft);
        background: #fff;
        flex: 0 0 auto;
        gap: .45rem;
    }

    .md-modal .md-form-section {
        margin-bottom: .7rem;
        padding: .75rem;
        background: #fff;
        border: 1px solid var(--md-border);
        border-radius: 10px;
    }

    .md-modal .md-section-title {
        margin: 0 0 .65rem;
        color: var(--md-text);
        font-size: .86rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: .38rem;
    }

    .md-modal .md-form-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: .65rem;
    }

    .md-modal .md-col-4 { grid-column: span 4; }
    .md-modal .md-col-5 { grid-column: span 5; }
    .md-modal .md-col-6 { grid-column: span 6; }
    .md-modal .md-col-7 { grid-column: span 7; }
    .md-modal .md-col-8 { grid-column: span 8; }
    .md-modal .md-col-12 { grid-column: span 12; }

    .md-modal textarea.md-form-control {
        min-height: auto;
        resize: vertical;
    }

    .md-modal .md-radio-group {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .55rem;
    }

    .md-modal .md-radio-option {
        min-height: 40px;
        margin: 0;
        padding: .55rem .65rem;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #fff;
        display: flex;
        align-items: center;
        gap: .5rem;
        cursor: pointer;
    }

    .md-modal .md-radio-option:has(input:checked) {
        border-color: #93b4f6;
        background: #f8fbff;
    }

    .md-modal .md-radio-option .form-check-input {
        margin: 0;
        flex: 0 0 auto;
    }

    .md-modal .md-radio-option > span {
        color: #334155;
        font-size: .84rem;
        font-weight: 400;
        display: inline-flex;
        align-items: center;
        gap: .38rem;
    }

    .md-modal .md-radio-option > span i {
        color: #64748b;
    }

    .md-modal .md-conditional {
        padding: .7rem;
        border: 1px dashed #cbd5e1;
        border-radius: 9px;
        background: #fbfcfe;
    }

    .md-modal .md-readonly-badge {
        margin-left: .35rem;
        padding: .18rem .42rem;
        border: 1px solid #bfdbfe;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: .7rem;
        font-weight: 600;
        vertical-align: middle;
    }

    .md-modal .permission-readonly-form .md-form-control,
    .md-modal .md-readonly-local .md-form-control {
        color: #334155 !important;
        background: #f8fafc !important;
        opacity: 1 !important;
        -webkit-text-fill-color: #334155 !important;
    }

    .swal2-container {
        z-index: 20000 !important;
    }

    @media (min-width: 1400px) {
        .medical-page .md-table {
            min-width: 100%;
            table-layout: fixed;
        }
    }

    @media (max-width: 991.98px) {
        .medical-page .md-filter-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .medical-page .md-filter-actions {
            grid-column: 1 / -1;
            justify-content: flex-start;
        }

        .md-modal .md-col-4,
        .md-modal .md-col-5,
        .md-modal .md-col-7,
        .md-modal .md-col-8 {
            grid-column: span 6;
        }
    }

    @media (max-height: 850px) and (min-width: 576px) {
        .md-modal .modal-dialog {
            margin-top: .35rem;
            margin-bottom: .35rem;
        }

        .md-modal .modal-content {
            max-height: calc(100vh - .7rem);
            max-height: calc(100dvh - .7rem);
        }

        .md-modal .modal-body {
            padding: .65rem;
        }

        .md-modal .md-form-section {
            padding: .65rem;
            margin-bottom: .55rem;
        }
    }

    @media (max-width: 767.98px) {
        .medical-page {
            padding-left: .5rem !important;
            padding-right: .5rem !important;
            padding-bottom: calc(5.5rem + env(safe-area-inset-bottom)) !important;
        }

        .medical-page .md-pagebar {
            align-items: flex-start;
            flex-direction: column;
        }

        .medical-page .md-page-actions,
        .medical-page .md-page-actions .md-btn {
            width: 100%;
        }

        .medical-page .md-filter-row {
            grid-template-columns: 1fr;
        }

        .medical-page .md-filter-actions {
            grid-column: auto;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .medical-page .md-filter-actions .md-btn {
            width: 100%;
        }

        .medical-page .md-table-head {
            align-items: flex-start;
        }

        .medical-page .md-table-body {
            padding: .6rem;
        }

        .md-modal .md-col-4,
        .md-modal .md-col-5,
        .md-modal .md-col-6,
        .md-modal .md-col-7,
        .md-modal .md-col-8,
        .md-modal .md-col-12 {
            grid-column: 1 / -1;
        }

        .md-modal .md-radio-group {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575.98px) {
        .md-modal .modal-dialog {
            width: 100%;
            max-width: none;
            margin: 0;
        }

        .md-modal .modal-content,
        .md-modal .modal-content > form {
            height: 100vh;
            height: 100dvh;
            max-height: 100vh;
            max-height: 100dvh;
            border: 0;
            border-radius: 0;
        }

        .md-modal .modal-footer {
            display: grid;
            grid-template-columns: 1fr;
        }

        .md-modal .modal-footer .md-btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
@php
    $permissionUser = auth()->user();
    $permissionKey = 'health_medical';

    $canMedicalCreate = $permissionUser && method_exists($permissionUser, 'canCreateForm')
        ? $permissionUser->canCreateForm($permissionKey)
        : true;
    $canMedicalUpdate = $permissionUser && method_exists($permissionUser, 'canUpdateForm')
        ? $permissionUser->canUpdateForm($permissionKey)
        : true;
    $canMedicalDelete = $permissionUser && method_exists($permissionUser, 'canDeleteForm')
        ? $permissionUser->canDeleteForm($permissionKey)
        : true;
    $canMedicalPrint = $permissionUser && method_exists($permissionUser, 'canPrintForm')
        ? $permissionUser->canPrintForm($permissionKey)
        : true;

    /*
     * ตัวกรองวันที่แบบซ่อน/แสดง
     * - พารามิเตอร์ว่าง เช่น ?start_date=&end_date= ไม่นับว่าใช้งานตัวกรอง
     * - เปิดอัตโนมัติเมื่อมีค่าค้นหาหรือ Validation ของตัวกรองผิดพลาด
     * - ปุ่มและช่องค้นหาคงใช้งานได้ในสิทธิ์อ่านอย่างเดียวผ่าน data-permission-keep
     */
    $hasMedicalRecords = $hasMedicalRecords ?? $medicals->isNotEmpty();
    $hasMedicalDateFilter = request()->filled('start_date')
        || request()->filled('end_date')
        || filled(old('start_date'))
        || filled(old('end_date'));

    $medicalFilterErrors = $errors->getBag('filters');
    $hasMedicalFilterErrors = $medicalFilterErrors->has('start_date')
        || $medicalFilterErrors->has('end_date')
        || $errors->has('start_date')
        || $errors->has('end_date');

    $showMedicalFilter = $hasMedicalDateFilter || $hasMedicalFilterErrors;
    $canShowMedicalFilter = $hasMedicalRecords
        || $hasMedicalDateFilter
        || $hasMedicalFilterErrors;

    $medicalReadonlyRecords = $medicals->mapWithKeys(function ($item) {
        return [$item->id => [
            'id' => $item->id,
            'client_id' => $item->client_id,
            'medical_date' => filled($item->medical_date)
                ? \Carbon\Carbon::parse($item->medical_date)->format('Y-m-d')
                : '',
            'disease_name' => $item->disease_name ?? '',
            'illness' => $item->illness ?? '',
            'treatment' => $item->treatment ?? '',
            'refer' => $item->refer ?? '',
            'diagnosis' => $item->diagnosis ?? '',
            'appt_date' => filled($item->appt_date)
                ? \Carbon\Carbon::parse($item->appt_date)->format('Y-m-d')
                : '',
            'teacher' => $item->teacher ?? '',
            'remark' => $item->remark ?? '',
        ]];
    });

    $medicalFlashMessage = session('message') ?: session('success') ?: session('error');
    $medicalFlashType = session('error')
        ? 'error'
        : (session('alert-type') === 'error' ? 'error' : 'success');
    $medicalFlashTitle = $medicalFlashType === 'success' ? 'ดำเนินการสำเร็จ' : 'เกิดข้อผิดพลาด';
    $medicalFlashTimer = $medicalFlashType === 'success' ? 2000 : null;
    $medicalFlashHasTimer = $medicalFlashType === 'success';
    $medicalJsonUrlTemplate = url('/medical/json') . '/:id';
    $medicalUpdateUrlTemplate = url('/medical/update') . '/:id';
@endphp

<div class="container-fluid medical-page">
    @include('frontend.client.medical.partials._header')
    @include('frontend.client.medical.partials._client_info')

    @if($medicals->isNotEmpty())
        @include('frontend.client.medical.partials._table')
    @else
        @include('frontend.client.medical.partials._empty')
    @endif
</div>

@if($canMedicalCreate)
    @include('frontend.client.medical.partials._add_modal')
@endif
@include('frontend.client.medical.partials._edit_modal')
@endsection

@push('scripts')
<script>
    window.medicalPageConfig = {
        canUpdate: @json($canMedicalUpdate),
        jsonUrlTemplate: @json($medicalJsonUrlTemplate),
        updateUrlTemplate: @json($medicalUpdateUrlTemplate),
        readonlyRecords: @json($medicalReadonlyRecords)
    };
</script>
<script src="{{ asset('backend/assets/js/medical.js') }}"></script>
<script>
(function () {
    'use strict';

    const config = window.medicalPageConfig || {};
    const today = @json(now('Asia/Bangkok')->toDateString());

    function urlFor(template, id) {
        return String(template || '').replace(':id', String(id));
    }

    function setValue(id, value) {
        const element = document.getElementById(id);
        if (element) element.value = value ?? '';
    }

    function clearValidation(form) {
        if (!form) return;
        form.classList.remove('was-validated');
        form.querySelectorAll('.is-invalid').forEach(function (field) {
            field.classList.remove('is-invalid');
            field.removeAttribute('aria-invalid');
        });
        form.querySelectorAll('.invalid-feedback').forEach(function (feedback) {
            feedback.textContent = '';
        });
    }

    function syncMedicalConditional(form, sectionId) {
        if (!form) return;

        const section = document.getElementById(sectionId);
        const medicalDate = form.querySelector('input[name="medical_date"]');
        const appointmentDate = form.querySelector('input[name="appt_date"]');
        const diagnosis = form.querySelector('[name="diagnosis"]');
        const checked = form.querySelector('input[name="refer"]:checked');
        const hasDoctor = checked && checked.value === 'พบแพทย์';

        if (section) {
            section.style.display = hasDoctor ? '' : 'none';
            section.setAttribute('aria-hidden', hasDoctor ? 'false' : 'true');
        }

        if (appointmentDate) {
            if (medicalDate?.value) {
                appointmentDate.min = medicalDate.value;
            } else {
                appointmentDate.removeAttribute('min');
            }

            if (!hasDoctor && !form.classList.contains('md-readonly-local')) {
                appointmentDate.value = '';
            }
        }

        if (diagnosis) {
            diagnosis.required = Boolean(hasDoctor && !form.classList.contains('md-readonly-local'));
            diagnosis.setAttribute('aria-required', diagnosis.required ? 'true' : 'false');

            if (!hasDoctor && !form.classList.contains('md-readonly-local')) {
                diagnosis.value = '';
                diagnosis.classList.remove('is-invalid');
            }
        }
    }

    function populateEditForm(data) {
        const form = document.getElementById('editMedicalForm');
        if (!form || !data) return form;

        clearValidation(form);
        form.action = urlFor(config.updateUrlTemplate, data.id);

        setValue('edit_medical_id', data.id);
        setValue('edit_client_id', data.client_id);
        setValue('edit_medical_date', data.medical_date);
        setValue('edit_disease_name', data.disease_name);
        setValue('edit_illness', data.illness);
        setValue('edit_treatment', data.treatment);
        setValue('edit_diagnosis', data.diagnosis);
        setValue('edit_appt_date', data.appt_date);
        setValue('edit_teacher', data.teacher);
        setValue('edit_remark', data.remark);

        form.querySelectorAll('input[name="refer"]').forEach(function (radio) {
            radio.checked = radio.value === (data.refer ?? '');
        });

        syncMedicalConditional(form, 'edit_medical_section');
        return form;
    }

    function lockReadonlyEditForm(form) {
        if (!form) return;

        form.classList.add('md-readonly-local', 'permission-readonly-form');
        form.setAttribute('aria-readonly', 'true');

        form.querySelectorAll('input, textarea, select').forEach(function (field) {
            if (field.type === 'hidden') return;

            const type = String(field.type || '').toLowerCase();
            if (field.tagName === 'TEXTAREA' || ['text', 'date', 'number', 'email', 'tel'].includes(type)) {
                field.readOnly = true;
                field.setAttribute('readonly', 'readonly');
                field.setAttribute('aria-readonly', 'true');
            } else {
                field.disabled = true;
                field.setAttribute('disabled', 'disabled');
                field.setAttribute('aria-disabled', 'true');
            }
        });

        form.querySelectorAll('button[type="submit"]').forEach(function (button) {
            button.remove();
        });
    }

    window.openMedicalReadonly = function (id) {
        const records = config.readonlyRecords || {};
        const record = records[String(id)] || records[id];
        const modal = document.getElementById('editMedicalModal');
        const form = populateEditForm(record);

        if (!record || !modal || !form) {
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่พบข้อมูล',
                    text: 'ไม่สามารถเปิดข้อมูลรายการนี้ได้',
                    confirmButtonText: 'OK'
                });
            }
            return;
        }

        lockReadonlyEditForm(form);
        bootstrap.Modal.getOrCreateInstance(modal).show();
    };

    window.openEditMedical = function (id) {
        if (!config.canUpdate) {
            window.openMedicalReadonly(id);
            return Promise.resolve();
        }

        const modal = document.getElementById('editMedicalModal');
        const form = document.getElementById('editMedicalForm');
        if (!id || !modal || !form) return Promise.resolve();

        clearValidation(form);

        return fetch(urlFor(config.jsonUrlTemplate, id), {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (response) {
                if (!response.ok) throw new Error('ไม่สามารถโหลดข้อมูลได้');
                return response.json();
            })
            .then(function (data) {
                populateEditForm(data);
                bootstrap.Modal.getOrCreateInstance(modal).show();
                return data;
            })
            .catch(function (error) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'ไม่สามารถเปิดข้อมูลได้',
                        text: error.message || 'กรุณาลองใหม่อีกครั้ง',
                        confirmButtonText: 'OK'
                    });
                }
                throw error;
            });
    };

    window.showEditErrors = function (errors) {
        const form = document.getElementById('editMedicalForm');
        if (!form) return;

        Object.entries(errors || {}).forEach(function ([name, messages]) {
            const field = form.querySelector('[name="' + name + '"]');
            const message = Array.isArray(messages) ? messages[0] : messages;
            if (!field) return;

            field.classList.add('is-invalid');
            field.setAttribute('aria-invalid', 'true');

            const container = field.closest('.md-col-4, .md-col-5, .md-col-6, .md-col-7, .md-col-8, .md-col-12, .md-form-section');
            const feedback = container?.querySelector('.invalid-feedback');
            if (feedback) feedback.textContent = message || 'ข้อมูลไม่ถูกต้อง';
        });
    };

    window.confirmMedicalDelete = function (id) {
        const form = document.getElementById('delete-form-medical-' + id);
        if (!form) return;

        if (!window.Swal) {
            if (window.confirm('ยืนยันการลบข้อมูลรายการนี้ใช่หรือไม่?')) form.submit();
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'ยืนยันการลบข้อมูล',
            text: 'เมื่อลบแล้วจะไม่สามารถกู้คืนข้อมูลรายการนี้ได้',
            showCancelButton: true,
            confirmButtonText: 'ลบข้อมูล',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#dc3545',
            allowOutsideClick: false
        }).then(function (result) {
            if (result.isConfirmed) form.submit();
        });
    };

    function setupForm(formId, sectionId) {
        const form = document.getElementById(formId);
        if (!form || form.dataset.mdReady === '1') return;
        form.dataset.mdReady = '1';

        const medicalDate = form.querySelector('input[name="medical_date"]');
        const submitButton = form.querySelector('button[type="submit"]');

        form.querySelectorAll('input[name="refer"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                syncMedicalConditional(form, sectionId);
            });
        });

        if (medicalDate) {
            medicalDate.max = today;
            medicalDate.addEventListener('input', function () {
                syncMedicalConditional(form, sectionId);
            });
            medicalDate.addEventListener('change', function () {
                syncMedicalConditional(form, sectionId);
            });
        }

        form.querySelectorAll('input, textarea, select').forEach(function (field) {
            ['input', 'change'].forEach(function (eventName) {
                field.addEventListener(eventName, function () {
                    field.classList.remove('is-invalid');
                    field.removeAttribute('aria-invalid');
                });
            });
        });

        form.addEventListener('submit', function (event) {
            form.classList.add('was-validated');

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                const invalid = form.querySelector(':invalid');
                invalid?.focus();
                invalid?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            if (form.dataset.submitting === '1') {
                event.preventDefault();
                return;
            }

            form.dataset.submitting = '1';
            if (submitButton) submitButton.disabled = true;
        });

        const modal = form.closest('.modal');
        modal?.addEventListener('hidden.bs.modal', function () {
            form.dataset.submitting = '0';
            if (submitButton) submitButton.disabled = false;
        });

        syncMedicalConditional(form, sectionId);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const addModal = document.getElementById('add-medical-modal');
        const editModal = document.getElementById('editMedicalModal');

        const filterPanel = document.getElementById('medicalFilterPanel');
        const filterToggle = document.querySelector('[data-medical-filter-toggle]');

        function syncMedicalFilterToggle(isOpen) {
            if (!filterToggle) return;

            filterToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

            const icon = filterToggle.querySelector('[data-filter-toggle-icon]');
            const label = filterToggle.querySelector('[data-filter-toggle-label]');

            if (icon) {
                icon.className = isOpen
                    ? 'bi bi-chevron-up'
                    : 'bi bi-funnel';
            }

            if (label) {
                label.textContent = isOpen
                    ? 'ซ่อนการค้นหา'
                    : 'ค้นหารายการ';
            }
        }

        if (filterPanel) {
            syncMedicalFilterToggle(filterPanel.classList.contains('show'));

            filterPanel.addEventListener('shown.bs.collapse', function () {
                syncMedicalFilterToggle(true);

                const firstFilter = filterPanel.querySelector('input:not([disabled])');
                if (firstFilter) {
                    window.setTimeout(function () {
                        firstFilter.focus({ preventScroll: true });
                    }, 100);
                }
            });

            filterPanel.addEventListener('hidden.bs.collapse', function () {
                syncMedicalFilterToggle(false);
            });
        }

        [addModal, editModal].forEach(function (modal) {
            if (modal && modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
        });

        setupForm('add-medical-form', 'medical-section-new');
        setupForm('editMedicalForm', 'edit_medical_section');

        document.querySelectorAll('#medical_start_date, #medical_end_date').forEach(function (input) {
            input.max = today;
        });

        function renumberRows() {
            if (!window.jQuery || !$.fn.DataTable || !$.fn.DataTable.isDataTable('#datatable-medical')) return;

            const api = $('#datatable-medical').DataTable();
            const pageInfo = api.page.info();
            api.rows({ page: 'current', order: 'applied', search: 'applied' })
                .nodes()
                .each(function (row, index) {
                    const cell = row.querySelector('.medical-row-number');
                    if (cell) cell.textContent = pageInfo.start + index + 1;
                });
        }

        if (window.jQuery && $.fn.DataTable) {
            $('#datatable-medical').on('draw.dt column-sizing.dt', renumberRows);
            window.requestAnimationFrame(renumberRows);
        }

        @if($medicalFlashMessage)
            if (window.Swal) {
                Swal.fire({
                    icon: @json($medicalFlashType),
                    title: @json($medicalFlashTitle),
                    text: @json($medicalFlashMessage),
                    timer: @json($medicalFlashTimer),
                    timerProgressBar: @json($medicalFlashHasTimer),
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    allowOutsideClick: false
                });
            }
        @endif
    });
})();
</script>

@if($errors->any() && old('_form_context') === 'medical_add' && !session('edit_mode'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('add-medical-modal');
    if (modal && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(modal).show();
    }
});
</script>
@endif

@if($errors->any() && session('edit_mode') && session('edit_id'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Promise.resolve(window.openEditMedical?.({{ session('edit_id') }}))
        .then(function () {
            window.showEditErrors?.(@json($errors->toArray()));
        })
        .catch(function () {});
});
</script>
@endif
@endpush