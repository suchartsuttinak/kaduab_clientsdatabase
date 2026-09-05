@extends('admin_client.admin_client')

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/assets/css/school-followup.css') }}">
<style>
    .school-followup-page {
        --sf-border: #e2e8f0;
        --sf-border-soft: #edf2f7;
        --sf-text: #172033;
        --sf-muted: #64748b;
        --sf-primary: #1d4ed8;
        --sf-primary-soft: #eff6ff;
        --sf-surface: #ffffff;
        --sf-surface-soft: #f8fafc;
        padding-top: .75rem !important;
        padding-bottom: 2rem !important;
    }

    .school-followup-page .sf-pagebar,
    .school-followup-page .sf-context,
    .school-followup-page .sf-filter,
    .school-followup-page .sf-table-card {
        background: var(--sf-surface);
        border: 1px solid var(--sf-border);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, .035);
    }

    /* ส่วนหัวหลัก: แถวเดียว กะทัดรัด ไม่มีข้อมูลซ้ำ */
    .school-followup-page .sf-pagebar {
        min-height: 58px;
        margin-bottom: .65rem;
        padding: .65rem .8rem .65rem .95rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .school-followup-page .sf-pagebar-main {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: .7rem;
    }

    .school-followup-page .sf-title-icon {
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

    .school-followup-page .sf-page-title {
        margin: 0;
        color: var(--sf-text);
        font-size: 1.08rem;
        font-weight: 700;
        line-height: 1.25;
    }

    .school-followup-page .sf-page-count {
        margin-top: .12rem;
        color: var(--sf-muted);
        font-size: .8rem;
        line-height: 1.2;
    }

    .school-followup-page .sf-page-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .45rem;
        flex-wrap: wrap;
    }

    .school-followup-page .sf-btn,
    .sf-modal .sf-btn {
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

    /* แสดงเฉพาะบริบทการศึกษา ไม่แสดงชื่อหรือรูปซ้ำกับ Sidebar */
    .school-followup-page .sf-context {
        margin-bottom: .65rem;
        padding: .62rem .85rem;
        display: grid;
        grid-template-columns: minmax(0, 1.7fr) minmax(140px, .8fr) minmax(140px, .8fr);
        gap: .45rem 1rem;
    }

    .school-followup-page .sf-context-item {
        min-width: 0;
        display: flex;
        align-items: baseline;
        gap: .45rem;
    }

    .school-followup-page .sf-context-label {
        flex: 0 0 auto;
        color: #475569;
        font-size: .78rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: .34rem;
    }

    .school-followup-page .sf-context-label i,
    .school-followup-page .sf-filter-label i,
    .sf-modal .sf-form-label i,
    .sf-modal .sf-modal-context-label i {
        color: #64748b;
        font-size: .84em;
        line-height: 1;
    }

    .school-followup-page .sf-context-value {
        min-width: 0;
        color: var(--sf-text);
        font-size: .88rem;
        font-weight: 400 !important;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }


    /* หลักการตัวอักษร: หัวข้อหนา ข้อมูลจริงตัวปกติ */
    .school-followup-page .sf-context-value,
    .school-followup-page .sf-table tbody td,
    .school-followup-page .sf-table tbody td *,
    .sf-modal .sf-modal-context-value,
    .sf-modal .sf-modal-context-value *,
    .sf-modal .sf-form-control {
        font-weight: 400 !important;
    }

    /* ตัวกรองแบบแถวเดียว */
    .school-followup-page .sf-filter {
        margin-bottom: .65rem;
        padding: .68rem .8rem;
    }

    .school-followup-page .sf-filter-row {
        display: grid;
        grid-template-columns: minmax(155px, 220px) minmax(155px, 220px) minmax(0, 1fr);
        gap: .55rem;
        align-items: end;
    }

    .school-followup-page .sf-filter-label,
    .sf-modal .sf-form-label {
        margin-bottom: .28rem;
        color: #334155;
        font-size: .79rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: .34rem;
    }

    .school-followup-page .sf-filter-control,
    .sf-modal .sf-form-control {
        min-height: 38px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #fff;
        color: var(--sf-text);
        font-size: .88rem;
        font-weight: 400 !important;
        box-shadow: none !important;
    }

    .school-followup-page .sf-filter-control:focus,
    .sf-modal .sf-form-control:focus {
        border-color: #93b4f6;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .09) !important;
    }

    .school-followup-page .sf-filter-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: .45rem;
        flex-wrap: wrap;
    }

    /* ตัวกรองแบบซ่อน/แสดง เพื่อประหยัดพื้นที่ */
    .school-followup-page .sf-filter-collapse {
        margin: 0;
    }

    .school-followup-page .sf-filter-toggle {
        color: #1d4ed8;
        border-color: #bfdbfe;
        background: #eff6ff;
    }

    .school-followup-page .sf-filter-toggle:hover,
    .school-followup-page .sf-filter-toggle:focus,
    .school-followup-page .sf-filter-toggle[aria-expanded="true"] {
        color: #1e40af;
        border-color: #93c5fd;
        background: #dbeafe;
    }

    .school-followup-page .sf-filter-error {
        margin-top: .3rem;
        color: #dc2626;
        font-size: .76rem;
        line-height: 1.35;
    }

    /* ตาราง */
    .school-followup-page .sf-table-card {
        overflow: hidden;
    }

    .school-followup-page .sf-table-head {
        min-height: 45px;
        padding: .55rem .8rem;
        border-bottom: 1px solid var(--sf-border-soft);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .school-followup-page .sf-table-title {
        margin: 0;
        color: var(--sf-text);
        font-size: .94rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: .42rem;
    }

    .school-followup-page .sf-table-title i {
        color: #64748b;
        font-size: .92rem;
        line-height: 1;
    }

    .school-followup-page .sf-table-meta {
        color: var(--sf-muted);
        font-size: .78rem;
        white-space: nowrap;
    }

    .school-followup-page .sf-table-body {
        padding: .7rem .8rem .8rem;
    }

    .school-followup-page .sf-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .school-followup-page .sf-table {
        min-width: 850px;
        margin: 0 !important;
        border-color: var(--sf-border-soft);
    }

    .school-followup-page .sf-table thead th {
        padding: .66rem .7rem;
        background: #f7f9fc;
        color: #334155;
        border-top: 0;
        border-bottom: 1px solid var(--sf-border);
        font-size: .8rem;
        font-weight: 700;
        white-space: nowrap;
        vertical-align: middle;
    }

    .school-followup-page .sf-table tbody td {
        padding: .68rem .7rem;
        color: #273449;
        border-color: var(--sf-border-soft);
        font-size: .85rem;
        font-weight: 400 !important;
        vertical-align: middle;
    }

    .school-followup-page .sf-table tbody tr:hover {
        background: #fbfdff;
    }

    .school-followup-page .sf-date {
        color: var(--sf-text);
        font-weight: 400 !important;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: .34rem;
    }

    .school-followup-page .sf-date i {
        color: #94a3b8;
        font-size: .78rem;
        line-height: 1;
    }

    .school-followup-page .sf-action-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        flex-wrap: nowrap;
        white-space: nowrap;
    }

    .school-followup-page .sf-icon-btn {
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

    .school-followup-page .sf-empty {
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

    .school-followup-page .sf-empty-icon {
        width: 42px;
        height: 42px;
        margin-bottom: .65rem;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--sf-primary-soft);
        color: var(--sf-primary);
        font-size: 1.05rem;
    }

    .school-followup-page .sf-empty-title {
        margin: 0 0 .28rem;
        color: var(--sf-text);
        font-size: .96rem;
        font-weight: 700;
    }

    .school-followup-page .sf-empty-text {
        margin: 0 0 .8rem;
        color: var(--sf-muted);
        font-size: .84rem;
    }

    /* DataTables ให้กลมกลืนกับหน้าต้นแบบ */
    .school-followup-page .dataTables_wrapper .dataTables_length,
    .school-followup-page .dataTables_wrapper .dataTables_filter {
        margin-bottom: .55rem;
        color: var(--sf-muted);
        font-size: .8rem;
    }

    .school-followup-page .dataTables_wrapper .dataTables_length select,
    .school-followup-page .dataTables_wrapper .dataTables_filter input {
        min-height: 34px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        box-shadow: none;
        font-size: .82rem;
    }

    .school-followup-page .dataTables_wrapper .dataTables_info,
    .school-followup-page .dataTables_wrapper .dataTables_paginate {
        margin-top: .6rem;
        color: var(--sf-muted);
        font-size: .8rem;
    }

    /* Modal แบบเรียบง่าย ไม่ซ้ำชื่อและรูปผู้รับบริการ */
    .sf-modal {
        --sf-border: #e2e8f0;
        --sf-border-soft: #edf2f7;
        --sf-text: #172033;
        --sf-muted: #64748b;
        --sf-primary: #1d4ed8;
        --sf-primary-soft: #eff6ff;
        --sf-surface-soft: #f8fafc;
    }

    /*
     * โครงสร้าง Modal ต้องรองรับจอ Notebook ที่ความสูงจำกัด
     * เนื่องจากภายใน .modal-content มี <form> ครอบ modal-header/body/footer
     * จึงกำหนดให้ form เป็น flex-column โดยตรง เพื่อให้ modal-body เลื่อนได้จริง
     */
    .sf-modal {
        position: fixed !important;
        inset: 0 !important;
        z-index: 2000 !important;
        overflow: hidden !important;
        padding: 0 !important;
    }

    body:has(.sf-modal.show) .modal-backdrop.show,
    body.modal-open > .modal-backdrop.show {
        z-index: 1990 !important;
    }

    /* ใช้ specificity และ !important เฉพาะโครงสร้าง เพื่อไม่ให้ CSS เดิมของ layout ทับ */
    .sf-modal .modal-dialog.followup-mobile-dialog {
        width: calc(100% - 2rem) !important;
        max-width: 860px !important;
        height: auto !important;
        min-height: 0 !important;
        max-height: calc(100vh - 2rem) !important;
        max-height: calc(100dvh - 2rem) !important;
        margin: 1rem auto !important;
        align-items: center !important;
    }

    .sf-modal .modal-content.followup-mobile-content.custom-modal {
        width: 100% !important;
        height: auto !important;
        min-height: 0 !important;
        max-height: calc(100vh - 2rem) !important;
        max-height: calc(100dvh - 2rem) !important;
        border: 0;
        border-radius: 13px;
        overflow: hidden !important;
        box-shadow: 0 18px 55px rgba(15, 23, 42, .18);
    }

    .sf-modal .modal-content > form {
        width: 100% !important;
        height: auto !important;
        min-height: 0 !important;
        max-height: inherit !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
    }

    .sf-modal .modal-header {
        flex: 0 0 auto;
        min-height: 54px;
        padding: .72rem .9rem;
        border-bottom: 1px solid var(--sf-border);
        background: #fff;
    }

    .sf-modal .modal-title {
        margin: 0;
        color: var(--sf-text);
        font-size: 1rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
    }

    .sf-modal .modal-title i {
        color: #475569;
    }

    .sf-modal .modal-body {
        flex: 1 1 auto;
        min-height: 0;
        padding: .85rem .9rem 1rem !important;
        padding-bottom: calc(1rem + env(safe-area-inset-bottom)) !important;
        background: #fff;
        overflow-x: hidden !important;
        overflow-y: auto !important;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-gutter: stable;
    }

    .sf-modal .modal-body::-webkit-scrollbar {
        width: 8px;
    }

    .sf-modal .modal-body::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .sf-modal .modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border: 2px solid #f1f5f9;
        border-radius: 999px;
    }

    .sf-modal .sf-modal-context {
        margin-bottom: .8rem;
        padding: .58rem .7rem;
        border: 1px solid var(--sf-border);
        border-radius: 9px;
        background: var(--sf-surface-soft);
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .45rem .8rem;
    }

    .sf-modal .sf-modal-context-item {
        min-width: 0;
    }

    .sf-modal .sf-modal-context-label {
        margin-bottom: .08rem;
        color: #475569;
        font-size: .72rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: .3rem;
    }

    .sf-modal .sf-modal-context-value {
        display: block;
        color: var(--sf-text);
        font-size: .82rem;
        font-weight: 400 !important;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .sf-modal .sf-form-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: .75rem;
    }

    .sf-modal .sf-col-4 { grid-column: span 4; }
    .sf-modal .sf-col-6 { grid-column: span 6; }
    .sf-modal .sf-col-12 { grid-column: span 12; }

    .sf-modal .sf-radio-group {
        display: flex;
        align-items: center;
        gap: .45rem;
        flex-wrap: wrap;
    }

    .sf-modal .sf-radio-option {
        min-height: 36px;
        margin: 0;
        padding: .38rem .65rem;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #fff;
        display: inline-flex;
        align-items: center;
        gap: .42rem;
        color: #334155;
        font-size: .82rem;
        font-weight: 600;
        cursor: pointer;
    }

    .sf-modal .sf-radio-option:has(input:checked) {
        border-color: #93b4f6;
        background: var(--sf-primary-soft);
        color: #1e40af;
    }

    .sf-modal .sf-radio-option .form-check-input {
        margin: 0;
    }

    .sf-modal .custom-radio-text {
        display: inline-flex;
        align-items: center;
        gap: .34rem;
    }

    .sf-modal .custom-radio-text i {
        color: #64748b;
        font-size: .82rem;
        line-height: 1;
    }

    .sf-modal textarea.sf-form-control {
        min-height: 92px;
        resize: vertical;
    }

    .sf-modal .modal-footer {
        position: relative;
        z-index: 2;
        flex: 0 0 auto;
        padding: .65rem .9rem;
        padding-bottom: calc(.65rem + env(safe-area-inset-bottom));
        border-top: 1px solid var(--sf-border) !important;
        background: #fbfcfe;
    }

    /* Notebook และจอแนวนอนที่มีความสูงใช้งานน้อย */
    @media (min-width: 768px) and (max-height: 850px) {
        .sf-modal .modal-dialog.followup-mobile-dialog {
            margin: .5rem auto !important;
            max-height: calc(100vh - 1rem) !important;
            max-height: calc(100dvh - 1rem) !important;
            align-items: flex-start !important;
        }

        .sf-modal .modal-content.followup-mobile-content.custom-modal {
            max-height: calc(100vh - 1rem) !important;
            max-height: calc(100dvh - 1rem) !important;
        }

        .sf-modal .modal-header {
            min-height: 46px;
            padding: .55rem .8rem;
        }

        .sf-modal .modal-body {
            padding: .65rem .8rem .75rem !important;
        }

        .sf-modal .sf-modal-context {
            margin-bottom: .6rem;
            padding: .48rem .6rem;
        }

        .sf-modal .sf-form-grid {
            gap: .55rem .7rem;
        }

        .sf-modal textarea.sf-form-control {
            min-height: 76px;
        }

        .sf-modal .modal-footer {
            padding: .5rem .8rem;
        }
    }

    @media (max-width: 991.98px) {
        .school-followup-page .sf-context {
            grid-template-columns: 1fr 1fr;
        }

        .school-followup-page .sf-context-item:first-child {
            grid-column: 1 / -1;
        }

        .school-followup-page .sf-filter-row {
            grid-template-columns: 1fr 1fr;
        }

        .school-followup-page .sf-filter-actions {
            grid-column: 1 / -1;
            justify-content: flex-start;
        }
    }

    @media (max-width: 767.98px) {
        .school-followup-page {
            padding-left: .75rem !important;
            padding-right: .75rem !important;
        }

        .school-followup-page .sf-pagebar {
            align-items: flex-start;
            flex-direction: column;
        }

        .school-followup-page .sf-page-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .school-followup-page .sf-page-actions .sf-btn {
            flex: 1 1 auto;
        }

        .school-followup-page .sf-context,
        .school-followup-page .sf-filter-row {
            grid-template-columns: 1fr;
        }

        .school-followup-page .sf-context-item:first-child,
        .school-followup-page .sf-filter-actions {
            grid-column: auto;
        }

        .school-followup-page .sf-context-item {
            align-items: flex-start;
            justify-content: space-between;
            gap: .8rem;
        }

        .school-followup-page .sf-context-value {
            text-align: right;
        }

        .school-followup-page .sf-filter-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .school-followup-page .sf-filter-actions .sf-btn {
            width: 100%;
        }

        .sf-modal .modal-dialog.followup-mobile-dialog {
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

        .sf-modal .modal-content.followup-mobile-content.custom-modal {
            height: 100vh !important;
            height: 100dvh !important;
            min-height: 0 !important;
            max-height: 100vh !important;
            max-height: 100dvh !important;
            border-radius: 0;
        }

        .sf-modal .modal-content > form {
            height: 100% !important;
            max-height: 100% !important;
        }

        .sf-modal .sf-modal-context {
            grid-template-columns: 1fr;
        }

        .sf-modal .sf-modal-context-item {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: .75rem;
        }

        .sf-modal .sf-col-4,
        .sf-modal .sf-col-6,
        .sf-modal .sf-col-12 {
            grid-column: span 12;
        }
    }

    /* V7: ส่วนหัวแบบเดียวกับหน้าบันทึกการบาดเจ็บ — สะอาดและประหยัดพื้นที่ */
    .school-followup-page .sf-pagebar {
        min-height: 0;
        padding: .72rem .85rem;
        display: block;
    }

    .school-followup-page .sf-pagebar-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .school-followup-page .sf-pagebar-details {
        margin-top: .62rem;
        padding-top: .58rem;
        border-top: 1px solid var(--sf-border-soft);
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) minmax(150px, .8fr) minmax(150px, .8fr);
        gap: .45rem 1rem;
    }

    .school-followup-page .sf-pagebar-detail {
        min-width: 0;
        display: flex;
        align-items: baseline;
        gap: .42rem;
    }

    .school-followup-page .sf-pagebar-detail-label {
        flex: 0 0 auto;
        color: #475569;
        font-size: .78rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: .32rem;
    }

    .school-followup-page .sf-pagebar-detail-label i {
        color: #64748b;
        font-size: .82em;
    }

    .school-followup-page .sf-pagebar-detail-value {
        min-width: 0;
        color: var(--sf-text);
        font-size: .86rem;
        font-weight: 400 !important;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .school-followup-page .sf-operation {
        display: inline-flex;
        align-items: center;
        gap: .34rem;
        color: #334155;
        font-size: .84rem;
        font-weight: 400 !important;
        white-space: nowrap;
    }

    .school-followup-page .sf-operation i {
        color: #94a3b8;
        font-size: .8rem;
    }

    .school-followup-page .sf-table { min-width: 980px; }

    .school-followup-page [data-permission-keep],
    .school-followup-page [data-permission-keep] * {
        pointer-events: auto;
    }

    body > .swal2-container,
    .swal2-container { z-index: 25000 !important; }

    .sf-readonly-badge {
        margin-left: .45rem;
        color: #2563eb;
        font-size: .76rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .sf-readonly-operation {
        min-height: 40px;
        padding: .55rem .7rem;
        border: 1px solid #d7dee8;
        border-radius: 8px;
        background: #f8fafc;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: .45rem;
        font-size: .88rem;
        font-weight: 400;
    }

    .sf-readonly-operation i { color: #64748b; }

    @media (max-width: 991.98px) {
        .school-followup-page .sf-pagebar-details {
            grid-template-columns: 1fr 1fr;
        }
        .school-followup-page .sf-pagebar-detail:first-child { grid-column: 1 / -1; }
    }

    @media (max-width: 575.98px) {
        .school-followup-page .sf-pagebar-top {
            align-items: flex-start;
            flex-direction: column;
        }
        .school-followup-page .sf-page-actions {
            width: 100%;
            justify-content: flex-start;
        }
        .school-followup-page .sf-page-actions .sf-btn { flex: 1 1 auto; }
        .school-followup-page .sf-pagebar-details { grid-template-columns: 1fr; }
        .school-followup-page .sf-pagebar-detail:first-child { grid-column: auto; }
        .school-followup-page .sf-pagebar-detail {
            justify-content: space-between;
            gap: .75rem;
        }
        .school-followup-page .sf-pagebar-detail-value { text-align: right; }
    }

</style>
@endpush

@section('content')
@php
    use Carbon\Carbon;

    $schoolName = optional($educationRecord)->school_name ?? 'ไม่พบข้อมูล';
    $educationName = optional(optional($educationRecord)->education)->education_name ?? 'ไม่พบข้อมูล';
    $semesterName = data_get($educationRecord, 'semester_label')
        ?? data_get($educationRecord, 'semester.semester_name')
        ?? 'ไม่พบข้อมูล';

    $permissionUser = auth()->user();
    $canSchoolCreate = (bool) ($permissionUser?->canCreateForm('education_followup') ?? false);
    $canSchoolUpdate = (bool) ($permissionUser?->canUpdateForm('education_followup') ?? false);
    $canSchoolDelete = (bool) ($permissionUser?->canDeleteForm('education_followup') ?? false);
    $canSchoolPrint = (bool) ($permissionUser?->canPrintForm('education_followup') ?? false);

    /*
     * ตัวกรองวันที่แบบซ่อน/แสดง
     * - พารามิเตอร์ว่าง เช่น ?start_date=&end_date= ไม่นับว่าใช้งานตัวกรอง
     * - เปิดอัตโนมัติเมื่อมีค่าค้นหาหรือ Validation ของตัวกรองผิดพลาด
     */
    $hasDateFilter = request()->filled('start_date')
        || request()->filled('end_date')
        || filled(old('start_date'))
        || filled(old('end_date'));

    $filterErrors = $errors->getBag('filters');
    $hasFilterErrors = $filterErrors->has('start_date')
        || $filterErrors->has('end_date')
        || $errors->has('start_date')
        || $errors->has('end_date');

    $showSchoolFollowupFilter = $hasDateFilter || $hasFilterErrors;

    /*
     * ให้ปุ่มค้นหาและปุ่มล้างค่ายังคงอยู่ เมื่อค้นหาแล้วไม่พบข้อมูล
     * แต่ไม่แสดงตัวกรองในหน้าเริ่มต้นที่ยังไม่มีบันทึกเลย
     */
    $hasFollowups = $followups->isNotEmpty()
        || $hasDateFilter
        || $hasFilterErrors;

    $schoolReadonlyRecords = $followups->mapWithKeys(function ($item) {
        $semester = data_get($item, 'educationRecord.semester.semester_name')
            ?? data_get($item, 'educationRecord.semester_label')
            ?? '-';
        preg_match('/(25\d{2})/', (string) $semester, $yearMatch);

        return [$item->id => [
            'id' => $item->id,
            'follow_date' => filled($item->follow_date) ? Carbon::parse($item->follow_date)->format('Y-m-d') : '',
            'teacher_name' => $item->teacher_name ?? '',
            'tel' => $item->tel ?? '',
            'follow_type' => $item->follow_type ?? '',
            'result' => $item->result ?? '',
            'remark' => $item->remark ?? '',
            'contact_name' => $item->contact_name ?? '',
            'education_record_id' => $item->education_record_id ?? '',
            'school_name' => data_get($item, 'educationRecord.school_name', '-'),
            'education_name' => data_get($item, 'educationRecord.education.education_name', '-'),
            'semester_name' => $semester,
            'academic_year' => $yearMatch[1] ?? '-',
        ]];
    });
@endphp

<div class="container-fluid school-followup-page">
    @include('frontend.client.school_followup.partials.header')
    @include('frontend.client.school_followup.partials.school_followup_fillter')
    @include('frontend.client.school_followup.partials.table')
</div>

@include('frontend.client.school_followup.partials.create-modal')
@include('frontend.client.school_followup.partials.edit-modal')

@php
    /* เปิด Modal เพิ่มข้อมูลเฉพาะข้อผิดพลาดของฟอร์มเพิ่ม ไม่เปิดจากข้อผิดพลาดตัวกรอง */
    $createErrorFields = [
        'follow_date',
        'teacher_name',
        'tel',
        'follow_type',
        'result',
        'remark',
        'contact_name',
        'education_record_id',
        'client_id',
    ];

    $hasCreateFormErrors = false;
    foreach ($createErrorFields as $createErrorField) {
        if ($errors->has($createErrorField)) {
            $hasCreateFormErrors = true;
            break;
        }
    }
@endphp

@if ($hasCreateFormErrors)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const createModalEl = document.getElementById('followupModal');
    if (createModalEl) {
        const modal = new bootstrap.Modal(createModalEl);
        modal.show();
    }
});
</script>
@endif
@endsection

@push('scripts')
<script>
    window.schoolFollowupConfig = {
        editUrlTemplate: "{{ route('school_followup.edit', ':id') }}",
        updateUrlTemplate: "{{ route('school_followup.update', ':id') }}",
        canUpdate: @json($canSchoolUpdate),
        readonlyRecords: @json($schoolReadonlyRecords)
    };
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const filterPanel = document.getElementById('schoolFollowupFilterPanel');
    const filterToggle = document.querySelector('[data-school-followup-filter-toggle]');

    function syncSchoolFollowupFilterToggle(isOpen) {
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

    syncSchoolFollowupFilterToggle(filterPanel.classList.contains('show'));

    filterPanel.addEventListener('shown.bs.collapse', function () {
        syncSchoolFollowupFilterToggle(true);

        const firstFilter = filterPanel.querySelector('input:not([disabled])');
        if (firstFilter) {
            window.setTimeout(function () {
                firstFilter.focus({ preventScroll: true });
            }, 100);
        }
    });

    filterPanel.addEventListener('hidden.bs.collapse', function () {
        syncSchoolFollowupFilterToggle(false);
    });
});
</script>
<script src="{{ asset('backend/assets/js/school-followup.js') }}"></script>
<script>
(function () {
    'use strict';

    const config = window.schoolFollowupConfig || {};
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

    function followTypeMeta(value) {
        if (value === 'self') return { label: 'ติดตามด้วยตนเอง', icon: 'bi-person-walking' };
        if (value === 'phone') return { label: 'โทรศัพท์', icon: 'bi-telephone' };
        if (value === 'other') return { label: 'อื่น ๆ', icon: 'bi-three-dots' };
        return { label: '-', icon: 'bi-dash' };
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

    window.openSchoolFollowupReadonly = function (id) {
        const record = records[String(id)] || records[id];
        const modalElement = document.getElementById('editFollowupModal');
        const form = document.getElementById('edit-followup-form');

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

        setValue('edit_followup_id', record.id);
        setValue('edit_education_record_id', record.education_record_id);
        setValue('edit_follow_date', record.follow_date);
        setValue('edit_teacher_name', record.teacher_name);
        setValue('edit_tel', record.tel);
        setValue('edit_result', record.result);
        setValue('edit_remark', record.remark);
        setValue('edit_contact_name', record.contact_name);

        setText('edit_school_name', record.school_name);
        setText('edit_education_name', record.education_name);
        setText('edit_semester_name', record.semester_name);
        setText('edit_academic_year', record.academic_year);

        form.querySelectorAll('input[name="follow_type"]').forEach(function (radio) {
            radio.checked = radio.value === record.follow_type;
        });

        const readonlyBox = document.getElementById('edit_follow_type_readonly');
        if (readonlyBox) {
            const meta = followTypeMeta(record.follow_type);
            readonlyBox.innerHTML = '<i class="bi ' + meta.icon + '"></i><span>' + meta.label + '</span>';
        }

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
            const column = field.closest('.sf-col-4, .sf-col-6, .sf-col-12');
            const feedback = column ? column.querySelector('.invalid-feedback') : null;
            if (feedback) feedback.textContent = message || 'ข้อมูลไม่ถูกต้อง';
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('edit-followup-form');
        const modalElement = document.getElementById('editFollowupModal');

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