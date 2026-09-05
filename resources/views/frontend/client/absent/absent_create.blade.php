@extends('admin_client.admin_client')

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/assets/css/absent.css') }}">
<style>
    .absent-page {
        --ab-border: #e2e8f0;
        --ab-border-soft: #edf2f7;
        --ab-text: #172033;
        --ab-muted: #64748b;
        --ab-primary: #1d4ed8;
        --ab-primary-soft: #eff6ff;
        --ab-surface: #ffffff;
        --ab-surface-soft: #f8fafc;
        padding-top: .75rem !important;
        padding-bottom: 2rem !important;
    }

    .absent-page .ab-pagebar,
    .absent-page .ab-context,
    .absent-page .ab-filter,
    .absent-page .ab-table-card {
        background: var(--ab-surface) !important;
        border: 1px solid var(--ab-border) !important;
        border-radius: 12px !important;
        box-shadow: 0 2px 10px rgba(15, 23, 42, .035) !important;
    }

    /* ส่วนหัวหลัก: แถวเดียว กะทัดรัด และไม่แสดงข้อมูลที่ซ้ำกับ Sidebar */
    .absent-page .ab-pagebar {
        min-height: 58px;
        margin-bottom: .65rem;
        padding: .65rem .8rem .65rem .95rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .absent-page .ab-pagebar-main {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: .7rem;
    }

    .absent-page .ab-title-icon {
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

    .absent-page .ab-page-title {
        margin: 0;
        color: var(--ab-text);
        font-size: 1.08rem;
        font-weight: 700;
        line-height: 1.25;
    }

    .absent-page .ab-page-count {
        margin-top: .12rem;
        color: var(--ab-muted);
        font-size: .8rem;
        font-weight: 400;
        line-height: 1.2;
    }

    .absent-page .ab-page-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .45rem;
        flex-wrap: wrap;
    }

    .absent-page .ab-btn,
    .ab-modal .ab-btn {
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

    /* แสดงเฉพาะบริบทการศึกษาปัจจุบัน */
    .absent-page .ab-context {
        margin-bottom: .65rem;
        padding: .62rem .85rem;
        display: grid;
        grid-template-columns: minmax(0, 1.7fr) minmax(140px, .8fr) minmax(140px, .8fr);
        gap: .45rem 1rem;
    }

    .absent-page .ab-context-item {
        min-width: 0;
        display: flex;
        align-items: baseline;
        gap: .45rem;
    }

    .absent-page .ab-context-label {
        flex: 0 0 auto;
        color: #475569;
        font-size: .78rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: .34rem;
    }

    .absent-page .ab-context-label i,
    .absent-page .ab-filter-label i,
    .ab-modal .ab-form-label i,
    .ab-modal .ab-modal-context-label i {
        color: #64748b;
        font-size: .84em;
        line-height: 1;
    }

    .absent-page .ab-context-value {
        min-width: 0;
        color: var(--ab-text);
        font-size: .88rem;
        font-weight: 400 !important;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    /* หลักการตัวอักษร: หัวข้อหนา ข้อมูลจริงตัวปกติ */
    .absent-page .ab-context-value,
    .absent-page .ab-table tbody td,
    .absent-page .ab-table tbody td *,
    .ab-modal .ab-modal-context-value,
    .ab-modal .ab-modal-context-value *,
    .ab-modal .ab-form-control {
        font-weight: 400 !important;
    }

    /* ตัวกรองแบบแถวเดียว */
    .absent-page .ab-filter {
        margin-bottom: .65rem;
        padding: .68rem .8rem;
    }

    .absent-page .ab-filter-row {
        display: grid;
        grid-template-columns: minmax(155px, 220px) minmax(155px, 220px) minmax(0, 1fr);
        gap: .55rem;
        align-items: end;
    }

    .absent-page .ab-filter-label,
    .ab-modal .ab-form-label {
        margin-bottom: .28rem;
        color: #334155;
        font-size: .79rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: .34rem;
    }

    .absent-page .ab-filter-control,
    .ab-modal .ab-form-control {
        min-height: 38px;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: var(--ab-text);
        font-size: .88rem;
        font-weight: 400 !important;
        box-shadow: none !important;
    }

    .absent-page .ab-filter-control:focus,
    .ab-modal .ab-form-control:focus {
        border-color: #93b4f6 !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .09) !important;
    }

    .absent-page .ab-filter-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: .45rem;
        flex-wrap: wrap;
    }

    /* ตัวกรองแบบซ่อน/แสดง เพื่อประหยัดพื้นที่ */
    .absent-page .ab-filter-collapse {
        margin: 0;
    }

    .absent-page .ab-filter-toggle {
        color: #1d4ed8;
        border-color: #bfdbfe;
        background: #eff6ff;
    }

    .absent-page .ab-filter-toggle:hover,
    .absent-page .ab-filter-toggle:focus,
    .absent-page .ab-filter-toggle[aria-expanded="true"] {
        color: #1e40af;
        border-color: #93c5fd;
        background: #dbeafe;
    }

    .absent-page .ab-filter-error {
        margin-top: .3rem;
        color: #dc2626;
        font-size: .76rem;
        line-height: 1.35;
    }

    /* ตาราง */
    .absent-page .ab-table-card {
        overflow: hidden;
    }

    .absent-page .ab-table-head {
        min-height: 45px;
        padding: .55rem .8rem;
        border-bottom: 1px solid var(--ab-border-soft);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .absent-page .ab-table-title {
        margin: 0;
        color: var(--ab-text);
        font-size: .94rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: .42rem;
    }

    .absent-page .ab-table-title i {
        color: #64748b;
        font-size: .92rem;
        line-height: 1;
    }

    .absent-page .ab-table-meta {
        color: var(--ab-muted);
        font-size: .78rem;
        font-weight: 400;
        white-space: nowrap;
    }

    .absent-page .ab-table-body {
        padding: .7rem .8rem .8rem;
    }

    .absent-page .ab-table-wrap {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    .absent-page .ab-table {
        width: 100% !important;
        min-width: 780px;
        margin: 0 !important;
        border-color: var(--ab-border-soft);
    }

    .absent-page .ab-table thead th {
        padding: .66rem .7rem;
        background: #f7f9fc !important;
        color: #334155;
        border-top: 0;
        border-bottom: 1px solid var(--ab-border);
        font-size: .8rem;
        font-weight: 700;
        white-space: nowrap;
        vertical-align: middle;
    }

    .absent-page .ab-table tbody td {
        padding: .68rem .7rem;
        color: #273449;
        border-color: var(--ab-border-soft);
        font-size: .85rem;
        font-weight: 400 !important;
        vertical-align: middle;
    }

    .absent-page .ab-table tbody tr:hover {
        background: #fbfdff;
    }

    .absent-page .ab-date {
        color: var(--ab-text);
        font-weight: 400 !important;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: .34rem;
    }

    .absent-page .ab-date i {
        color: #94a3b8;
        font-size: .78rem;
        line-height: 1;
    }

    .absent-page .ab-text-wrap {
        min-width: 170px;
        line-height: 1.5;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    .absent-page .ab-action-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        flex-wrap: nowrap;
        white-space: nowrap;
    }

    .absent-page .ab-icon-btn {
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

    .absent-page .ab-empty {
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

    .absent-page .ab-empty-icon {
        width: 42px;
        height: 42px;
        margin-bottom: .65rem;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--ab-primary-soft);
        color: var(--ab-primary);
        font-size: 1.05rem;
    }

    .absent-page .ab-empty-title {
        margin: 0 0 .28rem;
        color: var(--ab-text);
        font-size: .96rem;
        font-weight: 700;
    }

    .absent-page .ab-empty-text {
        margin: 0 0 .8rem;
        color: var(--ab-muted);
        font-size: .84rem;
        font-weight: 400;
    }

    /* DataTables */
    .absent-page .dataTables_wrapper .dataTables_length,
    .absent-page .dataTables_wrapper .dataTables_filter {
        margin-bottom: .55rem;
        color: var(--ab-muted);
        font-size: .8rem;
    }

    .absent-page .dataTables_wrapper .dataTables_length select,
    .absent-page .dataTables_wrapper .dataTables_filter input {
        min-height: 34px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        box-shadow: none;
        font-size: .82rem;
    }

    .absent-page .dataTables_wrapper .dataTables_info,
    .absent-page .dataTables_wrapper .dataTables_paginate {
        margin-top: .6rem;
        color: var(--ab-muted);
        font-size: .8rem;
    }

    /* Modal: เรียบง่าย ไม่มีชื่อ รูป หรืออายุซ้ำกับ Sidebar */
    .ab-modal {
        --ab-border: #e2e8f0;
        --ab-text: #172033;
        --ab-primary-soft: #eff6ff;
        --ab-surface-soft: #f8fafc;
        position: fixed !important;
        inset: 0 !important;
        z-index: 2000 !important;
        overflow: hidden !important;
        padding: 0 !important;
    }

    body.modal-open > .modal-backdrop,
    body.modal-open > .modal-backdrop.show {
        z-index: 1990 !important;
    }

    .ab-modal .modal-dialog.absent-mobile-dialog {
        width: calc(100% - 2rem) !important;
        max-width: 860px !important;
        height: auto !important;
        min-height: 0 !important;
        max-height: calc(100vh - 2rem) !important;
        max-height: calc(100dvh - 2rem) !important;
        margin: 1rem auto !important;
        display: flex !important;
        align-items: center !important;
    }

    .ab-modal .modal-content.absent-mobile-content.custom-modal {
        width: 100% !important;
        height: auto !important;
        min-height: 0 !important;
        max-height: calc(100vh - 2rem) !important;
        max-height: calc(100dvh - 2rem) !important;
        border: 0 !important;
        border-radius: 13px !important;
        overflow: hidden !important;
        box-shadow: 0 18px 55px rgba(15, 23, 42, .18) !important;
    }

    .ab-modal .modal-content > form {
        width: 100% !important;
        height: auto !important;
        min-height: 0 !important;
        max-height: inherit !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
    }

    .ab-modal .modal-header {
        flex: 0 0 auto;
        min-height: 54px;
        padding: .72rem .9rem;
        border-bottom: 1px solid var(--ab-border) !important;
        background: #fff !important;
    }

    .ab-modal .modal-title {
        margin: 0;
        color: var(--ab-text);
        font-size: 1rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
    }

    .ab-modal .modal-title i {
        color: #475569;
    }

    .ab-modal .modal-body {
        flex: 1 1 auto;
        min-height: 0;
        padding: .85rem .9rem 1rem !important;
        padding-bottom: calc(1rem + env(safe-area-inset-bottom)) !important;
        background: #fff !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-gutter: stable;
    }

    .ab-modal .modal-body::-webkit-scrollbar {
        width: 8px;
    }

    .ab-modal .modal-body::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .ab-modal .modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border: 2px solid #f1f5f9;
        border-radius: 999px;
    }

    .ab-modal .ab-modal-context {
        margin-bottom: .8rem;
        padding: .58rem .7rem;
        border: 1px solid var(--ab-border);
        border-radius: 9px;
        background: var(--ab-surface-soft);
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .45rem .8rem;
    }

    .ab-modal .ab-modal-context-item {
        min-width: 0;
    }

    .ab-modal .ab-modal-context-label {
        margin-bottom: .08rem;
        color: #475569;
        font-size: .72rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: .3rem;
    }

    .ab-modal .ab-modal-context-value {
        display: block;
        color: var(--ab-text);
        font-size: .82rem;
        font-weight: 400 !important;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .ab-modal .ab-form-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: .75rem;
    }

    .ab-modal .ab-col-6 { grid-column: span 6; }
    .ab-modal .ab-col-12 { grid-column: span 12; }

    .ab-modal textarea.ab-form-control {
        min-height: 92px;
        resize: vertical;
    }

    .ab-modal .modal-footer {
        position: relative;
        z-index: 2;
        flex: 0 0 auto;
        padding: .65rem .9rem;
        padding-bottom: calc(.65rem + env(safe-area-inset-bottom));
        border-top: 1px solid var(--ab-border) !important;
        background: #fbfcfe !important;
    }

    /* Notebook และจอแนวนอนที่มีความสูงใช้งานน้อย */
    @media (min-width: 768px) and (max-height: 850px) {
        .ab-modal .modal-dialog.absent-mobile-dialog {
            margin: .5rem auto !important;
            max-height: calc(100vh - 1rem) !important;
            max-height: calc(100dvh - 1rem) !important;
            align-items: flex-start !important;
        }

        .ab-modal .modal-content.absent-mobile-content.custom-modal {
            max-height: calc(100vh - 1rem) !important;
            max-height: calc(100dvh - 1rem) !important;
        }

        .ab-modal .modal-header {
            min-height: 46px;
            padding: .55rem .8rem;
        }

        .ab-modal .modal-body {
            padding: .65rem .8rem .75rem !important;
        }

        .ab-modal .ab-modal-context {
            margin-bottom: .6rem;
            padding: .48rem .6rem;
        }

        .ab-modal .ab-form-grid {
            gap: .55rem .7rem;
        }

        .ab-modal textarea.ab-form-control {
            min-height: 76px;
        }

        .ab-modal .modal-footer {
            padding: .5rem .8rem;
        }
    }

    @media (max-width: 991.98px) {
        .absent-page .ab-context {
            grid-template-columns: 1fr 1fr;
        }

        .absent-page .ab-context-item:first-child {
            grid-column: 1 / -1;
        }

        .absent-page .ab-filter-row {
            grid-template-columns: 1fr 1fr;
        }

        .absent-page .ab-filter-actions {
            grid-column: 1 / -1;
            justify-content: flex-start;
        }
    }

    @media (max-width: 767.98px) {
        .absent-page {
            padding-left: .75rem !important;
            padding-right: .75rem !important;
            padding-bottom: calc(5rem + env(safe-area-inset-bottom)) !important;
        }

        .absent-page .ab-pagebar {
            align-items: flex-start;
            flex-direction: column;
        }

        .absent-page .ab-page-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .absent-page .ab-page-actions .ab-btn {
            flex: 1 1 auto;
        }

        .absent-page .ab-context,
        .absent-page .ab-filter-row {
            grid-template-columns: 1fr;
        }

        .absent-page .ab-context-item:first-child,
        .absent-page .ab-filter-actions {
            grid-column: auto;
        }

        .absent-page .ab-context-item {
            align-items: flex-start;
            justify-content: space-between;
            gap: .8rem;
        }

        .absent-page .ab-context-value {
            text-align: right;
        }

        .absent-page .ab-filter-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .absent-page .ab-filter-actions .ab-btn {
            width: 100%;
        }

        .absent-page .dataTables_wrapper .dataTables_length,
        .absent-page .dataTables_wrapper .dataTables_filter {
            width: 100%;
            text-align: left !important;
        }

        .absent-page .dataTables_wrapper .dataTables_filter {
            margin-top: .5rem;
        }

        .absent-page .dataTables_wrapper .dataTables_filter input {
            width: calc(100% - 55px);
            max-width: 260px;
        }

        .ab-modal .modal-dialog.absent-mobile-dialog {
            width: 100% !important;
            max-width: none !important;
            height: 100vh !important;
            height: 100dvh !important;
            min-height: 0 !important;
            max-height: 100vh !important;
            max-height: 100dvh !important;
            margin: 0 !important;
            align-items: stretch !important;
        }

        .ab-modal .modal-content.absent-mobile-content.custom-modal {
            height: 100vh !important;
            height: 100dvh !important;
            min-height: 0 !important;
            max-height: 100vh !important;
            max-height: 100dvh !important;
            border-radius: 0 !important;
        }

        .ab-modal .modal-content > form {
            height: 100% !important;
            max-height: 100% !important;
        }

        .ab-modal .ab-modal-context {
            grid-template-columns: 1fr;
        }

        .ab-modal .ab-modal-context-item {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: .75rem;
        }

        .ab-modal .ab-col-6,
        .ab-modal .ab-col-12 {
            grid-column: span 12;
        }
    }

    /* V7: ส่วนหัวแบบเดียวกับหน้าบันทึกการบาดเจ็บ */
    .absent-page .ab-pagebar {
        min-height: 0;
        padding: .72rem .85rem;
        display: block;
    }

    .absent-page .ab-pagebar-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .absent-page .ab-pagebar-details {
        margin-top: .62rem;
        padding-top: .58rem;
        border-top: 1px solid var(--ab-border-soft);
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) minmax(150px, .8fr) minmax(150px, .8fr);
        gap: .45rem 1rem;
    }

    .absent-page .ab-pagebar-detail {
        min-width: 0;
        display: flex;
        align-items: baseline;
        gap: .42rem;
    }

    .absent-page .ab-pagebar-detail-label {
        flex: 0 0 auto;
        color: #475569;
        font-size: .78rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: .32rem;
    }

    .absent-page .ab-pagebar-detail-label i {
        color: #64748b;
        font-size: .82em;
    }

    .absent-page .ab-pagebar-detail-value {
        min-width: 0;
        color: var(--ab-text);
        font-size: .86rem;
        font-weight: 400 !important;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .absent-page [data-permission-keep],
    .absent-page [data-permission-keep] * { pointer-events: auto; }

    body > .swal2-container,
    .swal2-container { z-index: 25000 !important; }

    .ab-readonly-badge {
        margin-left: .45rem;
        color: #2563eb;
        font-size: .76rem;
        font-weight: 700;
        white-space: nowrap;
    }

    @media (max-width: 991.98px) {
        .absent-page .ab-pagebar-details { grid-template-columns: 1fr 1fr; }
        .absent-page .ab-pagebar-detail:first-child { grid-column: 1 / -1; }
    }

    @media (max-width: 575.98px) {
        .absent-page .ab-pagebar-top {
            align-items: flex-start;
            flex-direction: column;
        }
        .absent-page .ab-page-actions {
            width: 100%;
            justify-content: flex-start;
        }
        .absent-page .ab-page-actions .ab-btn { flex: 1 1 auto; }
        .absent-page .ab-pagebar-details { grid-template-columns: 1fr; }
        .absent-page .ab-pagebar-detail:first-child { grid-column: auto; }
        .absent-page .ab-pagebar-detail {
            justify-content: space-between;
            gap: .75rem;
        }
        .absent-page .ab-pagebar-detail-value { text-align: right; }
    }

</style>
@endpush

@section('content')
@php
    use Carbon\Carbon;

    $schoolName = optional($educationRecord)->school_name ?? 'ไม่พบข้อมูล';
    $educationName = optional(optional($educationRecord)->education)->education_name ?? 'ไม่พบข้อมูล';
    $semesterName = $semesterName
        ?? data_get($educationRecord, 'semester.semester_name')
        ?? 'ไม่พบข้อมูล';

    $hasAbsents = isset($absents) && $absents->isNotEmpty();

    $absentFilterErrorBag = $errors->getBag('filters');
    $hasAbsentFilterErrors = $absentFilterErrorBag->has('start_date')
        || $absentFilterErrorBag->has('end_date')
        || $errors->has('start_date')
        || $errors->has('end_date');

    $showAbsentFilter = filled(request('start_date'))
        || filled(request('end_date'))
        || filled(old('start_date'))
        || filled(old('end_date'))
        || $hasAbsentFilterErrors;

    // เมื่อค้นหาแล้วไม่พบข้อมูล ยังต้องแสดงปุ่มและแผงค้นหาเพื่อให้ล้างค่าได้
    $canShowAbsentFilter = $hasAbsents || $showAbsentFilter;

    $absentFormErrorFields = [
        'absent_date', 'record_date', 'cause', 'operation',
        'remark', 'teacher', 'education_record_id', 'client_id',
    ];
    $hasAbsentFormErrors = collect($absentFormErrorFields)
        ->contains(fn ($field) => $errors->has($field));

    $permissionUser = auth()->user();
    $canAbsentCreate = (bool) ($permissionUser?->canCreateForm('education_absence') ?? false);
    $canAbsentUpdate = (bool) ($permissionUser?->canUpdateForm('education_absence') ?? false);
    $canAbsentDelete = (bool) ($permissionUser?->canDeleteForm('education_absence') ?? false);
    $canAbsentPrint = (bool) ($permissionUser?->canPrintForm('education_absence') ?? false);

    $absentReadonlyRecords = $absents->mapWithKeys(function ($item) {
        return [$item->id => [
            'id' => $item->id,
            'absent_date' => filled($item->absent_date) ? Carbon::parse($item->absent_date)->format('Y-m-d') : '',
            'record_date' => filled($item->record_date) ? Carbon::parse($item->record_date)->format('Y-m-d') : '',
            'cause' => $item->cause ?? '',
            'operation' => $item->operation ?? '',
            'remark' => $item->remark ?? '',
            'teacher' => $item->teacher ?? '',
            'education_record_id' => $item->education_record_id ?? '',
            'school_name' => data_get($item, 'educationRecord.school_name', '-'),
            'education_name' => data_get($item, 'educationRecord.education.education_name', '-'),
            'semester_name' => data_get($item, 'educationRecord.semester.semester_name', '-'),
        ]];
    });
@endphp

<div class="container-fluid absent-page">
    @include('frontend.client.absent.partials.header')
    @include('frontend.client.absent.partials._report_filter')
    @include('frontend.client.absent.partials.table')
</div>

@include('frontend.client.absent.partials.create-modal')
@include('frontend.client.absent.partials.edit-modal')

@if ($hasAbsentFormErrors)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const createModalEl = document.getElementById('absentModal');
    if (createModalEl && window.bootstrap) {
        const modal = new bootstrap.Modal(createModalEl);
        modal.show();
    }
});
</script>
@endif
@endsection

@push('scripts')
<script>
    window.absentConfig = {
        editBaseUrl: "{{ url('/absent/edit') }}",
        updateBaseUrl: "{{ url('/absent/update') }}",
        canUpdate: @json($canAbsentUpdate),
        readonlyRecords: @json($absentReadonlyRecords)
    };
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const filterPanel = document.getElementById('absentFilterPanel');
    const filterToggle = document.querySelector('[data-absent-filter-toggle]');

    function syncAbsentFilterToggle(isOpen) {
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

    if (!filterPanel) return;

    syncAbsentFilterToggle(filterPanel.classList.contains('show'));

    filterPanel.addEventListener('shown.bs.collapse', function () {
        syncAbsentFilterToggle(true);

        const firstFilter = filterPanel.querySelector('input:not([disabled])');
        if (firstFilter) {
            window.setTimeout(function () {
                firstFilter.focus({ preventScroll: true });
            }, 100);
        }
    });

    filterPanel.addEventListener('hidden.bs.collapse', function () {
        syncAbsentFilterToggle(false);
    });
});
</script>
<script src="{{ asset('backend/assets/js/absent.js') }}"></script>
<script>
(function () {
    'use strict';

    const config = window.absentConfig || {};
    const canUpdate = Boolean(config.canUpdate);
    const records = config.readonlyRecords || {};

    function setValue(id, value) {
        const element = document.getElementById(id);
        if (element) element.value = value ?? '';
    }

    function setText(id, value) {
        const element = document.getElementById(id);
        if (element) element.textContent = (value === null || value === undefined || value === '') ? '-' : value;
    }

    function lockReadonlyForm(form) {
        if (!form) return;

        form.querySelectorAll('input, textarea, select').forEach(function (field) {
            if (field.type === 'hidden') return;

            if (field.matches('input[type="text"], input[type="email"], input[type="number"], textarea')) {
                field.readOnly = true;
            } else {
                field.disabled = true;
            }

            field.setAttribute('aria-readonly', 'true');
            field.classList.add('bg-light');
        });

        form.querySelectorAll('button[type="submit"]').forEach(function (button) {
            button.remove();
        });
    }

    window.openAbsentReadonly = function (id) {
        const record = records[String(id)] || records[id];
        const modalElement = document.getElementById('editAbsentModal');
        const form = document.getElementById('edit-absent-form');

        if (!record || !modalElement || !form) {
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่พบข้อมูล',
                    text: 'ไม่สามารถเปิดข้อมูลรายการนี้ได้',
                    confirmButtonText: 'ตกลง'
                });
            }
            return;
        }

        setValue('edit_education_record_id', record.education_record_id);
        setValue('edit_absent_date', record.absent_date);
        setValue('edit_record_date', record.record_date);
        setValue('edit_cause', record.cause);
        setValue('edit_operation', record.operation);
        setValue('edit_remark', record.remark);
        setValue('edit_teacher', record.teacher);

        setText('edit_school_name', record.school_name);
        setText('edit_education_name', record.education_name);
        setText('edit_semester_name', record.semester_name);

        lockReadonlyForm(form);

        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalElement).show();
        }
    };

    function clearValidation(form) {
        form.querySelectorAll('.is-invalid').forEach(function (field) {
            field.classList.remove('is-invalid');
        });
        form.querySelectorAll('.invalid-feedback').forEach(function (feedback) {
            feedback.textContent = '';
        });
    }

    function showFieldErrors(form, errors) {
        Object.entries(errors || {}).forEach(function ([name, messages]) {
            const field = form.querySelector('[name="' + name + '"]');
            const message = Array.isArray(messages) ? messages[0] : messages;
            if (!field) return;

            field.classList.add('is-invalid');
            const column = field.closest('.ab-col-6, .ab-col-12');
            const feedback = column ? column.querySelector('.invalid-feedback') : null;
            if (feedback) feedback.textContent = message || 'ข้อมูลไม่ถูกต้อง';
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('edit-absent-form');
        const modalElement = document.getElementById('editAbsentModal');

        if (!canUpdate) {
            lockReadonlyForm(form);
            return;
        }

        if (!form) return;

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();

            clearValidation(form);

            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton?.disabled) return;
            if (submitButton) submitButton.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const payload = await response.json().catch(function () { return {}; });

                if (!response.ok || payload.success === false) {
                    showFieldErrors(form, payload.errors || {});
                    throw new Error(payload.message || 'ไม่สามารถแก้ไขข้อมูลได้');
                }

                if (modalElement && window.bootstrap) {
                    bootstrap.Modal.getInstance(modalElement)?.hide();
                }

                if (window.Swal) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'บันทึกสำเร็จ',
                        text: payload.message || 'แก้ไขข้อมูลเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง',
                        allowOutsideClick: false
                    });
                }

                window.location.reload();
            } catch (error) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'ไม่สามารถบันทึกข้อมูลได้',
                        text: error.message || 'กรุณาตรวจสอบข้อมูลแล้วลองใหม่',
                        confirmButtonText: 'ตกลง'
                    });
                }
            } finally {
                if (submitButton) submitButton.disabled = false;
            }
        }, true);
    });
})();
</script>

@endpush