@extends('admin_client.admin_client')

@section('content')
@php
    use App\Helpers\ThaiDateHelper;

    $isEdit = isset($checkbody) && $checkbody;
    $hasRows = isset($checkbodies) && $checkbodies->isNotEmpty();
    $totalRows = $hasRows ? $checkbodies->count() : 0;
    $normalCount = $hasRows ? $checkbodies->where('development', 'สมวัย')->count() : 0;
    $delayedCount = $hasRows ? $checkbodies->where('development', 'ไม่สมวัย')->count() : 0;
    $specialCount = $hasRows ? $checkbodies->where('development_type', 'เด็กกลุ่มพิเศษ')->count() : 0;
@endphp

<style>
    .checkbody-page {
        --cb-primary: #0f6f61;
        --cb-primary-dark: #0b5148;
        --cb-primary-soft: #ecfdf7;
        --cb-primary-border: #b7ead9;
        --cb-blue: #1d4ed8;
        --cb-blue-soft: #eff6ff;
        --cb-success: #15803d;
        --cb-success-soft: #f0fdf4;
        --cb-warning: #b45309;
        --cb-warning-soft: #fffbeb;
        --cb-danger: #b91c1c;
        --cb-danger-soft: #fef2f2;
        --cb-text: #172033;
        --cb-muted: #64748b;
        --cb-border: #dbe4ee;
        --cb-border-soft: #e8eef5;
        --cb-surface: #fff;
        --cb-surface-soft: #f8fafc;
        --cb-radius-xl: 22px;
        --cb-radius-lg: 17px;
        --cb-radius-md: 12px;
        color: var(--cb-text);
    }

    .checkbody-page *,
    #checkBodyFormModal *,
    #checkBodyFormModal *::before,
    #checkBodyFormModal *::after {
        box-sizing: border-box;
    }

    .checkbody-page .cb-shell {
        width: 100%;
        max-width: 1500px;
        margin: 0 auto;
    }

    .checkbody-page .cb-hero,
    .checkbody-page .cb-stat,
    .checkbody-page .cb-table-card,
    .checkbody-page .cb-empty-state {
        border: 1px solid var(--cb-border-soft);
        background: var(--cb-surface);
        box-shadow: 0 6px 18px rgba(15, 23, 42, .055);
    }

    .checkbody-page .cb-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1rem;
        padding: 1.35rem 1.5rem;
        border-radius: var(--cb-radius-xl);
        background:
            radial-gradient(circle at top right, rgba(20, 184, 166, .14), transparent 35%),
            linear-gradient(135deg, #fff 0%, #f7fffc 100%);
    }

    .checkbody-page .cb-hero::after {
        content: "";
        position: absolute;
        right: -78px;
        bottom: -100px;
        width: 225px;
        height: 225px;
        border: 38px solid rgba(15, 111, 97, .055);
        border-radius: 50%;
        pointer-events: none;
    }

    .checkbody-page .cb-hero-grid {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 1rem 1.5rem;
        align-items: center;
    }

    .checkbody-page .cb-heading-row {
        display: flex;
        min-width: 0;
        align-items: flex-start;
        gap: .9rem;
    }

    .checkbody-page .cb-heading-icon {
        display: inline-flex;
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--cb-primary-border);
        border-radius: 16px;
        background: var(--cb-primary-soft);
        color: var(--cb-primary);
        font-size: 1.35rem;
        box-shadow: 0 8px 20px rgba(15, 111, 97, .1);
    }

    .checkbody-page .cb-page-title {
        margin: 0;
        color: #0f172a;
        font-size: clamp(1.22rem, 2vw, 1.58rem);
        font-weight: 800;
        line-height: 1.35;
    }

    .checkbody-page .cb-page-subtitle {
        max-width: 850px;
        margin: .28rem 0 0;
        color: var(--cb-muted);
        font-size: .9rem;
        line-height: 1.7;
    }

    .checkbody-page .cb-client-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .55rem;
        margin-top: .9rem;
    }

    .checkbody-page .cb-meta-chip {
        display: inline-flex;
        max-width: 100%;
        align-items: center;
        gap: .42rem;
        padding: .48rem .75rem;
        border: 1px solid var(--cb-border);
        border-radius: 999px;
        background: rgba(255, 255, 255, .9);
        color: #334155;
        font-size: .8rem;
        line-height: 1.35;
    }

    .checkbody-page .cb-meta-chip i { color: var(--cb-primary); }
    .checkbody-page .cb-meta-chip strong {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .checkbody-page .cb-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: .65rem;
    }

    .checkbody-page .cb-btn {
        display: inline-flex;
        min-height: 44px;
        align-items: center;
        justify-content: center;
        gap: .48rem;
        padding: .65rem 1rem;
        border-radius: 12px;
        font-size: .86rem;
        font-weight: 700;
        line-height: 1.2;
        text-decoration: none;
        box-shadow: none;
        transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease, border-color .18s ease;
    }

    .checkbody-page .cb-btn:hover { transform: translateY(-1px); }

    .checkbody-page .cb-btn-primary {
        border: 1px solid var(--cb-primary);
        background: var(--cb-primary);
        color: #fff;
    }

    .checkbody-page .cb-btn-primary:hover {
        border-color: var(--cb-primary-dark);
        background: var(--cb-primary-dark);
        color: #fff;
        box-shadow: 0 9px 20px rgba(15, 111, 97, .2);
    }

    .checkbody-page .cb-btn-secondary {
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #475569;
    }

    .checkbody-page .cb-btn-secondary:hover {
        border-color: #94a3b8;
        background: #f8fafc;
        color: #1e293b;
    }

    .checkbody-page .cb-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .85rem;
        margin-bottom: 1rem;
    }

    .checkbody-page .cb-stat {
        min-width: 0;
        padding: 1rem 1.05rem;
        border-radius: var(--cb-radius-lg);
    }

    .checkbody-page .cb-stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .checkbody-page .cb-stat-label {
        color: var(--cb-muted);
        font-size: .78rem;
        font-weight: 650;
        line-height: 1.45;
    }

    .checkbody-page .cb-stat-icon {
        display: inline-flex;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: var(--cb-primary-soft);
        color: var(--cb-primary);
    }

    .checkbody-page .cb-stat-value {
        margin-top: .5rem;
        overflow: hidden;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.45;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .checkbody-page .cb-value-success { color: var(--cb-success); }
    .checkbody-page .cb-value-warning { color: var(--cb-warning); }
    .checkbody-page .cb-value-blue { color: var(--cb-blue); }

    .checkbody-page .cb-table-card {
        overflow: hidden;
        border-radius: var(--cb-radius-xl);
    }

    .checkbody-page .cb-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.15rem;
        border-bottom: 1px solid var(--cb-border-soft);
        background: #fff;
    }

    .checkbody-page .cb-card-heading {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: .7rem;
    }

    .checkbody-page .cb-card-heading-icon {
        display: inline-flex;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: var(--cb-primary-soft);
        color: var(--cb-primary);
    }

    .checkbody-page .cb-card-title {
        margin: 0;
        color: #0f172a;
        font-size: .98rem;
        font-weight: 800;
        line-height: 1.4;
    }

    .checkbody-page .cb-card-subtitle {
        margin: .12rem 0 0;
        color: var(--cb-muted);
        font-size: .78rem;
        line-height: 1.55;
    }

    .checkbody-page .cb-count-badge {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        gap: .35rem;
        padding: .42rem .7rem;
        border: 1px solid var(--cb-border);
        border-radius: 999px;
        background: var(--cb-surface-soft);
        color: #475569;
        font-size: .78rem;
        font-weight: 700;
    }

    .checkbody-page .cb-card-body { padding: 1.05rem 1.15rem 1.2rem; }

    .checkbody-page .cb-table-wrap {
        overflow: hidden;
        border: 1px solid var(--cb-border-soft);
        border-radius: 14px;
        background: #fff;
    }

    .checkbody-page .cb-table-wrap .dataTables_wrapper,
    .checkbody-page .cb-table-wrap .dataTables_scroll {
        width: 100%;
        min-width: 0;
    }

    .checkbody-page .cb-table-wrap .dataTables_scrollHead {
        overflow: hidden !important;
        background: #f8fafc;
    }

    .checkbody-page .cb-table-wrap .dataTables_scrollBody {
        overflow-x: auto !important;
        overflow-y: visible !important;
        scrollbar-gutter: stable;
        -webkit-overflow-scrolling: touch;
    }

    .checkbody-page .cb-table {
        width: 100% !important;
        min-width: 1280px;
        margin: 0 !important;
        table-layout: fixed;
    }

    .checkbody-page .cb-col-date { width: 125px !important; min-width: 125px !important; }
    .checkbody-page .cb-col-development { width: 125px !important; min-width: 125px !important; }
    .checkbody-page .cb-col-support { width: 200px !important; min-width: 200px !important; }
    .checkbody-page .cb-col-metric { width: 175px !important; min-width: 175px !important; }
    .checkbody-page .cb-col-health { width: 205px !important; min-width: 205px !important; }
    .checkbody-page .cb-col-recorder { width: 155px !important; min-width: 155px !important; }
    .checkbody-page .cb-col-remark { width: 220px !important; min-width: 220px !important; }

    .checkbody-page .cb-col-actions {
        position: sticky;
        right: 0;
        width: 150px !important;
        min-width: 150px !important;
        max-width: 150px !important;
        border-left: 1px solid var(--cb-border-soft) !important;
    }

    .checkbody-page .cb-table thead .cb-col-actions {
        z-index: 5;
        background: #f8fafc;
        box-shadow: -10px 0 18px -18px rgba(15, 23, 42, .75);
    }

    .checkbody-page .cb-table tbody .cb-col-actions {
        z-index: 3;
        background: #fff;
        box-shadow: -10px 0 18px -18px rgba(15, 23, 42, .55);
    }

    .checkbody-page .cb-table tbody tr:hover .cb-col-actions { background: #fbfefd; }

    .checkbody-page .cb-table thead th {
        padding: .78rem .75rem;
        border-bottom: 1px solid var(--cb-border);
        background: #f8fafc;
        color: #334155;
        font-size: .78rem;
        font-weight: 800;
        vertical-align: middle;
        white-space: nowrap;
    }

    .checkbody-page .cb-table tbody td {
        padding: .78rem .75rem;
        border-color: #edf1f6;
        color: #334155;
        font-size: .83rem;
        line-height: 1.55;
        vertical-align: middle;
    }

    .checkbody-page .cb-table tbody tr:hover { background: #fbfefd; }
    .checkbody-page .cb-cell-main { color: #1e293b; font-weight: 700; }
    .checkbody-page .cb-cell-muted { color: var(--cb-muted); }

    .checkbody-page .cb-text-block {
        display: block;
        min-width: 0;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .checkbody-page .cb-text-clamp {
        display: -webkit-box;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }

    .checkbody-page .cb-status {
        display: inline-flex;
        align-items: center;
        gap: .32rem;
        padding: .35rem .58rem;
        border: 1px solid transparent;
        border-radius: 999px;
        font-size: .74rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .checkbody-page .cb-status-normal {
        border-color: #bbf7d0;
        background: var(--cb-success-soft);
        color: var(--cb-success);
    }

    .checkbody-page .cb-status-delayed {
        border-color: #fde68a;
        background: var(--cb-warning-soft);
        color: var(--cb-warning);
    }

    .checkbody-page .cb-status-general {
        border-color: #dbeafe;
        background: var(--cb-blue-soft);
        color: var(--cb-blue);
    }

    .checkbody-page .cb-status-special {
        border-color: #ddd6fe;
        background: #f5f3ff;
        color: #6d28d9;
    }

    .checkbody-page .cb-row-actions {
        display: grid;
        width: 120px;
        margin: 0 auto;
        grid-template-columns: repeat(3, 36px);
        justify-content: center;
        align-items: center;
        gap: .38rem;
        white-space: nowrap;
    }

    .checkbody-page .cb-icon-btn {
        display: inline-flex;
        width: 36px;
        height: 36px;
        padding: 0;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: .88rem;
        box-shadow: none;
        transition: transform .16s ease, box-shadow .16s ease;
    }

    .checkbody-page .cb-icon-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(15, 23, 42, .12);
    }

    .checkbody-page .cb-empty-state {
        padding: 3.2rem 1.25rem;
        border-radius: var(--cb-radius-xl);
        text-align: center;
        background: radial-gradient(circle at top, rgba(20, 184, 166, .11), transparent 44%), #fff;
    }

    .checkbody-page .cb-empty-icon {
        display: inline-flex;
        width: 82px;
        height: 82px;
        margin-bottom: 1rem;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--cb-primary-border);
        border-radius: 50%;
        background: var(--cb-primary-soft);
        color: var(--cb-primary);
        font-size: 2rem;
    }

    .checkbody-page .cb-empty-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.15rem;
        font-weight: 800;
    }

    .checkbody-page .cb-empty-text {
        max-width: 650px;
        margin: .45rem auto 1.15rem;
        color: var(--cb-muted);
        font-size: .88rem;
        line-height: 1.75;
    }

    .checkbody-page .dataTables_wrapper .dataTables_length,
    .checkbody-page .dataTables_wrapper .dataTables_filter {
        margin-bottom: .8rem;
        color: #475569;
        font-size: .82rem;
    }

    .checkbody-page .dataTables_wrapper .dataTables_length select,
    .checkbody-page .dataTables_wrapper .dataTables_filter input {
        min-height: 38px;
        border: 1px solid var(--cb-border);
        border-radius: 10px;
        background: #fff;
        box-shadow: none;
    }

    .checkbody-page .dataTables_wrapper .dataTables_info,
    .checkbody-page .dataTables_wrapper .dataTables_paginate {
        margin-top: .8rem;
        color: var(--cb-muted);
        font-size: .8rem;
    }

    .checkbody-page .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
    }



    /* =========================================================
       HEADER สำหรับกรณียังไม่มีข้อมูล
       รูปแบบเดียวกับหน้า idstation / addictive
    ========================================================= */
    .checkbody-page .cb-empty-header {
        position: relative;
        overflow: hidden;
        min-height: 142px;
        margin-bottom: 1.75rem;
        padding: 1.45rem 1.5rem;
        border: 1px solid var(--cb-primary-border);
        border-radius: 18px;
        background:
            linear-gradient(135deg, #f0fdf9 0%, #f8fffc 58%, #ffffff 100%);
        box-shadow: 0 10px 28px rgba(15, 111, 97, .08);
    }

    .checkbody-page .cb-empty-header::after {
        content: "";
        position: absolute;
        right: -58px;
        top: -72px;
        width: 190px;
        height: 190px;
        border: 26px solid rgba(15, 111, 97, .045);
        border-radius: 50%;
        pointer-events: none;
    }

    .checkbody-page .cb-empty-header-inner {
        position: relative;
        z-index: 1;
        min-height: 92px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .checkbody-page .cb-empty-header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
        min-width: 0;
    }

    .checkbody-page .cb-empty-header-icon {
        display: inline-flex;
        width: 60px;
        height: 60px;
        flex: 0 0 60px;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--cb-primary-border);
        border-radius: 18px;
        background: linear-gradient(145deg, #dcfdf5, #f0fdf9);
        color: var(--cb-primary);
        font-size: 1.45rem;
        box-shadow: 0 8px 18px rgba(15, 111, 97, .12);
    }

    .checkbody-page .cb-empty-header-text {
        min-width: 0;
    }

    .checkbody-page .cb-empty-header-title {
        margin: 0;
        color: #143b38;
        font-size: 1.18rem;
        font-weight: 800;
        line-height: 1.35;
        letter-spacing: -.01em;
    }

    .checkbody-page .cb-empty-header-client {
        margin-top: .28rem;
        color: var(--cb-muted);
        font-size: .84rem;
        line-height: 1.5;
    }

    .checkbody-page .cb-empty-header-client strong {
        color: #0f172a;
        font-weight: 800;
    }

    .checkbody-page .cb-empty-back-btn {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: .42rem;
        padding: .55rem .95rem;
        border: 1px solid #8b5cf6;
        border-radius: 12px;
        background: rgba(255, 255, 255, .92);
        color: #7c3aed;
        font-size: .86rem;
        font-weight: 700;
        line-height: 1.2;
        text-decoration: none;
        white-space: nowrap;
        box-shadow: 0 5px 12px rgba(124, 58, 237, .08);
        transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    .checkbody-page .cb-empty-back-btn:hover,
    .checkbody-page .cb-empty-back-btn:focus {
        color: #6d28d9;
        background: #faf5ff;
        transform: translateY(-1px);
        box-shadow: 0 8px 16px rgba(124, 58, 237, .12);
    }

    .checkbody-page .cb-empty-state {
        min-height: 320px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 767.98px) {
        .checkbody-page .cb-empty-header {
            min-height: 168px;
            margin-bottom: 1rem;
            padding: 1.35rem 1rem !important;
            border-radius: 16px;
        }

        .checkbody-page .cb-empty-header-inner {
            min-height: 124px;
            align-content: center;
            row-gap: 1rem;
        }

        .checkbody-page .cb-empty-header-inner,
        .checkbody-page .cb-empty-header-left {
            width: 100%;
        }

        .checkbody-page .cb-empty-back-btn {
            width: 100%;
        }
    }

    @media (max-width: 575.98px) {
        .checkbody-page .cb-empty-header {
            min-height: 176px;
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }

        .checkbody-page .cb-empty-header-left {
            align-items: center;
            gap: .8rem;
        }

        .checkbody-page .cb-empty-header-icon {
            width: 52px;
            height: 52px;
            flex-basis: 52px;
            border-radius: 15px;
            font-size: 1.25rem;
        }

        .checkbody-page .cb-empty-header-title {
            font-size: 1.02rem;
        }

        .checkbody-page .cb-empty-header-client {
            font-size: .78rem;
        }
    }

    body.checkbody-modal-open #checkBodyFormModal { z-index: 20000 !important; }
    body.checkbody-modal-open .modal-backdrop { z-index: 19990 !important; }

    #checkBodyFormModal {
        --cbm-primary: #0f6f61;
        --cbm-primary-dark: #0b5148;
        --cbm-primary-soft: #ecfdf7;
        --cbm-primary-border: #b7ead9;
        --cbm-text: #172033;
        --cbm-muted: #64748b;
        --cbm-border: #dbe4ee;
        --cbm-border-soft: #e8eef5;
        --cbm-danger: #dc2626;
        --cbm-danger-soft: #fff7f7;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 0 !important;
        color: var(--cbm-text);
    }

    #checkBodyFormModal .modal-dialog {
        width: calc(100% - 2rem);
        max-width: 1040px;
        height: calc(100dvh - 2rem);
        min-height: 0;
        margin: 1rem auto;
    }

    #checkBodyFormModal .modal-content {
        display: flex;
        width: 100%;
        height: 100%;
        max-height: 100%;
        min-height: 0;
        overflow: hidden;
        border: 0;
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 28px 70px rgba(15, 23, 42, .22), 0 8px 24px rgba(15, 23, 42, .1);
    }

    #checkBodyFormModal .cb-modal-form {
        display: flex;
        width: 100%;
        height: 100%;
        min-height: 0;
        flex: 1 1 auto;
        flex-direction: column;
    }

    #checkBodyFormModal .modal-header {
        position: relative;
        z-index: 5;
        display: flex;
        flex: 0 0 auto;
        align-items: center;
        gap: .85rem;
        padding: 1.05rem 1.25rem;
        border-bottom: 1px solid var(--cbm-border-soft);
        background: radial-gradient(circle at top right, rgba(20, 184, 166, .13), transparent 38%), linear-gradient(135deg, #f0fdf9 0%, #fff 72%);
    }

    #checkBodyFormModal .cb-modal-heading {
        display: flex;
        min-width: 0;
        flex: 1 1 auto;
        align-items: center;
        gap: .8rem;
    }

    #checkBodyFormModal .cb-modal-heading-icon {
        display: inline-flex;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--cbm-primary-border);
        border-radius: 13px;
        background: var(--cbm-primary-soft);
        color: var(--cbm-primary);
        font-size: 1.18rem;
    }

    #checkBodyFormModal .cb-modal-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.06rem;
        font-weight: 800;
        line-height: 1.35;
    }

    #checkBodyFormModal .cb-modal-subtitle {
        margin: .18rem 0 0;
        color: var(--cbm-muted);
        font-size: .79rem;
        line-height: 1.5;
    }

    #checkBodyFormModal .cb-mode-badge {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        gap: .35rem;
        padding: .38rem .62rem;
        border: 1px solid var(--cbm-border);
        border-radius: 999px;
        background: rgba(255, 255, 255, .9);
        color: #475569;
        font-size: .72rem;
        font-weight: 750;
        white-space: nowrap;
    }

    #checkBodyFormModal .cb-modal-close {
        display: inline-flex;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 10px;
        background: transparent;
        color: #64748b;
        text-decoration: none;
        cursor: pointer;
    }

    #checkBodyFormModal .cb-modal-close:hover { background: rgba(15, 23, 42, .07); color: #0f172a; }

    #checkBodyFormModal .cb-modal-body {
        position: relative;
        min-height: 0;
        flex: 1 1 auto;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 1.05rem 1.2rem 1.2rem;
        background: #f8fafc;
        overscroll-behavior: contain;
        scrollbar-gutter: stable;
        -webkit-overflow-scrolling: touch;
    }

    #checkBodyFormModal .cb-form-section {
        padding: 1rem;
        border: 1px solid var(--cbm-border-soft);
        border-radius: 15px;
        background: #fff;
    }

    #checkBodyFormModal .cb-form-section + .cb-form-section { margin-top: .85rem; }

    #checkBodyFormModal .cb-section-heading {
        display: flex;
        align-items: flex-start;
        gap: .68rem;
        margin-bottom: .9rem;
    }

    #checkBodyFormModal .cb-section-icon {
        display: inline-flex;
        width: 35px;
        height: 35px;
        flex: 0 0 35px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: var(--cbm-primary-soft);
        color: var(--cbm-primary);
        font-size: .96rem;
    }

    #checkBodyFormModal .cb-section-title {
        margin: 0;
        color: #172033;
        font-size: .93rem;
        font-weight: 800;
        line-height: 1.4;
    }

    #checkBodyFormModal .cb-section-description {
        margin: .1rem 0 0;
        color: var(--cbm-muted);
        font-size: .75rem;
        line-height: 1.5;
    }

    #checkBodyFormModal .cb-field { position: relative; }

    #checkBodyFormModal .cb-label {
        display: inline-flex;
        align-items: center;
        gap: .2rem;
        margin-bottom: .4rem;
        color: #334155;
        font-size: .82rem;
        font-weight: 700;
        line-height: 1.4;
    }

    #checkBodyFormModal .cb-required { color: var(--cbm-danger); }

    #checkBodyFormModal .form-control,
    #checkBodyFormModal .form-select {
        min-height: 44px;
        border: 1px solid var(--cbm-border);
        border-radius: 11px;
        background-color: #fff;
        color: var(--cbm-text);
        font-size: .86rem;
        box-shadow: none;
        transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    #checkBodyFormModal .form-control { padding: .64rem .78rem; }
    #checkBodyFormModal .form-select { padding-top: .64rem; padding-bottom: .64rem; }
    #checkBodyFormModal textarea.form-control { min-height: 94px; line-height: 1.6; resize: vertical; }
    #checkBodyFormModal .form-control::placeholder { color: #94a3b8; }
    #checkBodyFormModal .form-control:hover,
    #checkBodyFormModal .form-select:hover { border-color: #b9c7d6; }

    #checkBodyFormModal .form-control:focus,
    #checkBodyFormModal .form-select:focus {
        border-color: var(--cbm-primary);
        box-shadow: 0 0 0 .19rem rgba(15, 111, 97, .11);
    }

    #checkBodyFormModal .form-control.is-invalid,
    #checkBodyFormModal .form-select.is-invalid,
    #checkBodyFormModal .was-validated .form-control:invalid,
    #checkBodyFormModal .was-validated .form-select:invalid {
        border-color: var(--cbm-danger) !important;
        background-color: var(--cbm-danger-soft);
        box-shadow: 0 0 0 .17rem rgba(220, 38, 38, .08) !important;
    }

    #checkBodyFormModal .invalid-feedback,
    #checkBodyFormModal .cb-invalid-feedback {
        display: block;
        width: 100%;
        margin-top: .38rem;
        color: var(--cbm-danger);
        font-size: .74rem;
        font-weight: 650;
        line-height: 1.45;
    }

    #checkBodyFormModal .cb-radio-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .65rem;
    }

    #checkBodyFormModal .cb-radio-card { position: relative; min-width: 0; }

    #checkBodyFormModal .cb-radio-input {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        opacity: 0;
        pointer-events: none;
    }

    #checkBodyFormModal .cb-radio-label {
        display: flex;
        min-height: 62px;
        margin: 0;
        padding: .72rem .8rem;
        align-items: center;
        gap: .62rem;
        border: 1px solid var(--cbm-border);
        border-radius: 12px;
        background: #fff;
        color: #334155;
        cursor: pointer;
        font-size: .82rem;
        font-weight: 750;
        line-height: 1.35;
        transition: border-color .18s ease, background-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }

    #checkBodyFormModal .cb-radio-label:hover {
        border-color: #7dc4b7;
        background: #fbfffd;
        transform: translateY(-1px);
    }

    #checkBodyFormModal .cb-radio-icon {
        display: inline-flex;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #f1f5f9;
        color: #64748b;
    }

    #checkBodyFormModal .cb-radio-check {
        display: inline-flex;
        width: 21px;
        height: 21px;
        flex: 0 0 21px;
        margin-left: auto;
        align-items: center;
        justify-content: center;
        border: 1.5px solid #cbd5e1;
        border-radius: 50%;
        background: #fff;
        color: transparent;
        font-size: .7rem;
    }

    #checkBodyFormModal .cb-radio-input:checked + .cb-radio-label {
        border-color: var(--cbm-primary);
        background: var(--cbm-primary-soft);
        color: var(--cbm-primary-dark);
        box-shadow: 0 0 0 .16rem rgba(15, 111, 97, .08);
    }

    #checkBodyFormModal .cb-radio-input:checked + .cb-radio-label .cb-radio-icon {
        background: var(--cbm-primary);
        color: #fff;
    }

    #checkBodyFormModal .cb-radio-input:checked + .cb-radio-label .cb-radio-check {
        border-color: var(--cbm-primary);
        background: var(--cbm-primary);
        color: #fff;
    }

    #checkBodyFormModal .cb-radio-input:focus-visible + .cb-radio-label {
        box-shadow: 0 0 0 .2rem rgba(15, 111, 97, .14);
    }

    #checkBodyFormModal .cb-radio-wrap.cb-radio-invalid .cb-radio-label {
        border-color: rgba(220, 38, 38, .52);
        background: var(--cbm-danger-soft);
    }

    #checkBodyFormModal .cb-conditional-panel {
        margin-top: .85rem;
        padding: .9rem;
        border: 1px dashed #c8d8d4;
        border-radius: 13px;
        background: #fbfffd;
    }

    #checkBodyFormModal .cb-conditional-panel.is-hidden { display: none; }

    #checkBodyFormModal .cb-metric-panel {
        padding: .9rem;
        border: 1px dashed #cfdbe6;
        border-radius: 13px;
        background: #fbfdff;
    }

    #checkBodyFormModal .cb-modal-footer {
        position: relative;
        z-index: 5;
        display: flex;
        flex: 0 0 auto;
        align-items: center;
        justify-content: flex-end;
        gap: .65rem;
        padding: .85rem 1.2rem;
        border-top: 1px solid var(--cbm-border-soft);
        background: #fff;
    }

    #checkBodyFormModal .cb-modal-btn {
        display: inline-flex;
        min-width: 130px;
        min-height: 42px;
        padding: .58rem .95rem;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        border-radius: 11px;
        font-size: .83rem;
        font-weight: 750;
        line-height: 1.2;
        text-decoration: none;
        box-shadow: none;
        transition: transform .18s ease, background-color .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    #checkBodyFormModal .cb-modal-btn:hover { transform: translateY(-1px); }

    #checkBodyFormModal .cb-modal-btn-cancel {
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #475569;
    }

    #checkBodyFormModal .cb-modal-btn-cancel:hover {
        border-color: #94a3b8;
        background: #f8fafc;
        color: #1e293b;
    }

    #checkBodyFormModal .cb-modal-btn-save {
        border: 1px solid var(--cbm-primary);
        background: var(--cbm-primary);
        color: #fff;
    }

    #checkBodyFormModal .cb-modal-btn-save:hover {
        border-color: var(--cbm-primary-dark);
        background: var(--cbm-primary-dark);
        color: #fff;
        box-shadow: 0 8px 18px rgba(15, 111, 97, .2);
    }

    #checkBodyFormModal .cb-modal-btn:disabled {
        transform: none;
        cursor: default;
        opacity: 1;
        pointer-events: none;
    }

    @media (max-width: 1199.98px) {
        .checkbody-page .cb-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 991.98px) {
        .checkbody-page .cb-hero-grid { grid-template-columns: 1fr; }
        .checkbody-page .cb-actions { justify-content: flex-start; }
    }

    @media (max-width: 767.98px) {
        .checkbody-page { padding-right: .65rem !important; padding-left: .65rem !important; }
        .checkbody-page .cb-hero { padding: 1.1rem; border-radius: 18px; }
        .checkbody-page .cb-stats { grid-template-columns: 1fr 1fr; gap: .65rem; }
        .checkbody-page .cb-card-header { align-items: flex-start; flex-direction: column; }
        .checkbody-page .cb-card-body { padding: .9rem; }

        #checkBodyFormModal .modal-dialog {
            width: calc(100% - 1rem);
            height: calc(100dvh - 1rem);
            margin: .5rem auto;
        }

        #checkBodyFormModal .modal-content { border-radius: 17px; }
        #checkBodyFormModal .modal-header { padding: .9rem 1rem; }
        #checkBodyFormModal .cb-modal-body { padding: .85rem; }
        #checkBodyFormModal .cb-form-section { padding: .85rem; }
        #checkBodyFormModal .cb-modal-footer { padding: .75rem .85rem; }
    }

    @media (max-width: 575.98px) {
        .checkbody-page .cb-heading-icon { width: 46px; height: 46px; flex-basis: 46px; }
        .checkbody-page .cb-page-subtitle { font-size: .84rem; }
        .checkbody-page .cb-meta-chip { width: 100%; }
        .checkbody-page .cb-meta-chip strong { white-space: normal; }
        .checkbody-page .cb-actions,
        .checkbody-page .cb-actions .cb-btn { width: 100%; }
        .checkbody-page .cb-stats { grid-template-columns: 1fr; }

        #checkBodyFormModal .modal-dialog { width: 100%; height: 100dvh; margin: 0; }
        #checkBodyFormModal .modal-content { border-radius: 0; }
        #checkBodyFormModal .modal-header {
            padding: calc(.85rem + env(safe-area-inset-top)) .85rem .85rem;
        }
        #checkBodyFormModal .cb-modal-heading { gap: .62rem; }
        #checkBodyFormModal .cb-modal-heading-icon { width: 40px; height: 40px; flex-basis: 40px; }
        #checkBodyFormModal .cb-modal-subtitle,
        #checkBodyFormModal .cb-mode-badge { display: none; }
        #checkBodyFormModal .cb-radio-grid { grid-template-columns: 1fr; }
        #checkBodyFormModal .cb-modal-footer {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            padding: .72rem .85rem calc(.72rem + env(safe-area-inset-bottom));
        }
        #checkBodyFormModal .cb-modal-btn { width: 100%; min-width: 0; }
    }

    @media (max-width: 380px) {
        #checkBodyFormModal .cb-modal-footer { grid-template-columns: 1fr; }
        #checkBodyFormModal .cb-modal-btn-save { order: -1; }
    }

    @media (prefers-reduced-motion: reduce) {
        .checkbody-page *,
        .checkbody-page *::before,
        .checkbody-page *::after,
        #checkBodyFormModal *,
        #checkBodyFormModal *::before,
        #checkBodyFormModal *::after {
            scroll-behavior: auto !important;
            transition-duration: .01ms !important;
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
        }
    }
</style>

<div class="container-fluid px-2 px-lg-3 checkbody-page">
    <div class="cb-shell">
        @if($hasRows)
            <section class="cb-hero" aria-labelledby="checkBodyPageTitle">
                <div class="cb-hero-grid">
                    <div class="cb-heading-row">
                        <div class="cb-heading-icon" aria-hidden="true">
                            <i class="bi bi-heart-pulse"></i>
                        </div>

                        <div class="cb-heading-content">
                            <h1 class="cb-page-title" id="checkBodyPageTitle">
                                บันทึกการตรวจสุขภาพเบื้องต้น
                            </h1>

                            <p class="cb-page-subtitle">
                                จัดเก็บผลการตรวจร่างกาย พัฒนาการ การส่งเสริม และข้อมูลสุขภาพของผู้รับบริการอย่างเป็นระบบ
                            </p>

                            <div class="cb-client-meta">
                                <span class="cb-meta-chip">
                                    <i class="bi bi-person"></i>
                                    ผู้รับบริการ:
                                    <strong>{{ $client->fullname ?? $client->name ?? '-' }}</strong>
                                </span>

                                @if(!empty($client->age))
                                    <span class="cb-meta-chip">
                                        <i class="bi bi-calendar-heart"></i>
                                        อายุ:
                                        <strong>{{ $client->age }} ปี</strong>
                                    </span>
                                @endif

                                <span class="cb-meta-chip">
                                    <i class="bi bi-clipboard2-pulse"></i>
                                    จำนวนบันทึก:
                                    <strong>{{ number_format($totalRows) }} รายการ</strong>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="cb-actions">
                        @if($isEdit)
                            <a href="{{ route('check_body.add', $client->id) }}"
                               class="btn cb-btn cb-btn-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                ยกเลิกการแก้ไข
                            </a>
                        @endif

                        <button type="button"
                                class="btn cb-btn cb-btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#checkBodyFormModal">
                            <i class="bi {{ $isEdit ? 'bi-pencil-square' : 'bi-plus-circle' }}"></i>
                            {{ $isEdit ? 'เปิดฟอร์มแก้ไข' : 'เพิ่มผลการตรวจ' }}
                        </button>
                    </div>
                </div>
            </section>
        @else
            <section class="cb-empty-header" aria-labelledby="checkBodyPageTitle">
                <div class="cb-empty-header-inner">
                    <div class="cb-empty-header-left">
                        <div class="cb-empty-header-icon" aria-hidden="true">
                            <i class="bi bi-heart-pulse"></i>
                        </div>

                        <div class="cb-empty-header-text">
                            <h1 class="cb-empty-header-title" id="checkBodyPageTitle">
                                บันทึกการตรวจสุขภาพเบื้องต้น
                            </h1>

                            <div class="cb-empty-header-client">
                                ผู้รับบริการ:
                                <strong>{{ $client->fullname ?? $client->name ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('admin.index', $client->id) }}"
                       class="cb-empty-back-btn"
                       aria-label="กลับหน้าหลักผู้รับบริการ">
                        <i class="bi bi-arrow-left-circle"></i>
                        <span>กลับ</span>
                    </a>
                </div>
            </section>
        @endif

        @if($hasRows)
            <section class="cb-stats" aria-label="สรุปข้อมูลการตรวจสุขภาพ">
                <article class="cb-stat">
                    <div class="cb-stat-top">
                        <span class="cb-stat-label">วันที่ตรวจล่าสุด</span>
                        <span class="cb-stat-icon"><i class="bi bi-calendar2-check"></i></span>
                    </div>
                    <div class="cb-stat-value">
                        {{ ThaiDateHelper::formatThaiShort(optional($checkbodies->first())->assessor_date) }}
                    </div>
                </article>

                <article class="cb-stat">
                    <div class="cb-stat-top">
                        <span class="cb-stat-label">พัฒนาการสมวัย</span>
                        <span class="cb-stat-icon"><i class="bi bi-emoji-smile"></i></span>
                    </div>
                    <div class="cb-stat-value cb-value-success">
                        {{ number_format($normalCount) }} รายการ
                    </div>
                </article>

                <article class="cb-stat">
                    <div class="cb-stat-top">
                        <span class="cb-stat-label">พัฒนาการไม่สมวัย</span>
                        <span class="cb-stat-icon"><i class="bi bi-exclamation-triangle"></i></span>
                    </div>
                    <div class="cb-stat-value cb-value-warning">
                        {{ number_format($delayedCount) }} รายการ
                    </div>
                </article>

                <article class="cb-stat">
                    <div class="cb-stat-top">
                        <span class="cb-stat-label">เด็กกลุ่มพิเศษ</span>
                        <span class="cb-stat-icon"><i class="bi bi-stars"></i></span>
                    </div>
                    <div class="cb-stat-value cb-value-blue">
                        {{ number_format($specialCount) }} รายการ
                    </div>
                </article>
            </section>

            @include('frontend.client.checkBody._table')
        @else
            <section class="cb-empty-state">
                <div class="cb-empty-icon" aria-hidden="true">
                    <i class="bi bi-clipboard2-heart"></i>
                </div>

                <h2 class="cb-empty-title">ยังไม่มีข้อมูลการตรวจสุขภาพเบื้องต้น</h2>

                <p class="cb-empty-text">
                    เริ่มบันทึกผลการตรวจร่างกาย พัฒนาการ และสุขภาพพื้นฐาน
                    เพื่อใช้ประกอบการติดตามและวางแผนดูแลผู้รับบริการ
                </p>

                <button type="button"
                        class="btn cb-btn cb-btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#checkBodyFormModal">
                    <i class="bi bi-plus-circle"></i>
                    เพิ่มผลการตรวจครั้งแรก
                </button>
            </section>
        @endif
    </div>
</div>

@include('frontend.client.checkBody._form')
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalElement = document.getElementById('checkBodyFormModal');
        const form = document.getElementById('checkBodyForm');
        const shouldAutoOpen = @json(
            $isEdit || ($errors->any() && old('_form_context') === 'checkbody_form')
        );

        if (!modalElement || !form) {
            return;
        }

        if (modalElement.parentElement !== document.body) {
            document.body.appendChild(modalElement);
        }

        const modalBody = modalElement.querySelector('.cb-modal-body');
        const submitButton = form.querySelector('button[type="submit"]');

        const developmentInputs = form.querySelectorAll('input[name="development"]');
        const developmentWrap = form.querySelector('[data-development-wrap]');
        const developmentClientError = form.querySelector('[data-development-client-error]');
        const developmentDetailPanel = form.querySelector('[data-development-detail-panel]');
        const developmentDetail = form.querySelector('[name="detail"]');

        const developmentTypeInputs = form.querySelectorAll('input[name="development_type"]');
        const developmentTypeWrap = form.querySelector('[data-development-type-wrap]');
        const developmentTypeClientError = form.querySelector('[data-development-type-client-error]');
        const specialSupportPanel = form.querySelector('[data-special-support-panel]');
        const specialSupportType = form.querySelector('[name="special_support_type"]');
        const specialSupportOtherWrap = form.querySelector('[data-special-support-other-wrap]');
        const specialSupportOther = form.querySelector('[name="special_support_other"]');

        function selectedValue(name) {
            const checked = form.querySelector('input[name="' + name + '"]:checked');
            return checked ? checked.value : '';
        }

        function setPanelVisibility(panel, show) {
            if (!panel) return;
            panel.classList.toggle('is-hidden', !show);
            panel.setAttribute('aria-hidden', show ? 'false' : 'true');
        }

        function toggleDevelopmentDetail(clearHidden) {
            const showDetail = selectedValue('development') === 'ไม่สมวัย';
            setPanelVisibility(developmentDetailPanel, showDetail);

            if (developmentDetail) {
                developmentDetail.required = showDetail;

                if (!showDetail && clearHidden) {
                    developmentDetail.value = '';
                }
            }
        }

        function toggleSpecialSupportOther(clearHidden) {
            const showOther = specialSupportType && specialSupportType.value === 'อื่น ๆ';

            if (specialSupportOtherWrap) {
                specialSupportOtherWrap.classList.toggle('d-none', !showOther);
            }

            if (specialSupportOther) {
                specialSupportOther.required = showOther;
                if (!showOther && clearHidden) specialSupportOther.value = '';
            }
        }

        function toggleSpecialSupport(clearHidden) {
            const showSpecial = selectedValue('development_type') === 'เด็กกลุ่มพิเศษ';
            setPanelVisibility(specialSupportPanel, showSpecial);

            if (specialSupportType) {
                specialSupportType.required = showSpecial;
                if (!showSpecial && clearHidden) specialSupportType.value = '';
            }

            if (!showSpecial && specialSupportOther) {
                specialSupportOther.required = false;
                if (clearHidden) specialSupportOther.value = '';
            }

            toggleSpecialSupportOther(clearHidden);
        }

        function validateRadioGroup(name, wrap, errorElement, showMessage) {
            const valid = selectedValue(name) !== '';
            if (wrap) wrap.classList.toggle('cb-radio-invalid', showMessage && !valid);
            if (errorElement) errorElement.classList.toggle('d-none', !showMessage || valid);
            return valid;
        }

        function clearFieldError(field) {
            field.classList.remove('is-invalid');
            field.removeAttribute('aria-invalid');
            const feedback = field.closest('.cb-field')?.querySelector('.invalid-feedback');
            if (feedback && feedback.dataset.serverError === 'true') {
                feedback.style.display = 'none';
            }
        }

        developmentInputs.forEach(function (input) {
            input.addEventListener('change', function () {
                validateRadioGroup('development', developmentWrap, developmentClientError, false);
                toggleDevelopmentDetail(true);
            });
        });

        developmentTypeInputs.forEach(function (input) {
            input.addEventListener('change', function () {
                validateRadioGroup('development_type', developmentTypeWrap, developmentTypeClientError, false);
                toggleSpecialSupport(true);
            });
        });

        if (specialSupportType) {
            specialSupportType.addEventListener('change', function () {
                clearFieldError(specialSupportType);
                toggleSpecialSupportOther(true);
            });
        }

        form.querySelectorAll('.form-control, .form-select').forEach(function (field) {
            field.addEventListener('input', function () { clearFieldError(field); });
            field.addEventListener('change', function () { clearFieldError(field); });
        });

        form.addEventListener('submit', function (event) {
            if (form.dataset.submitting === 'true') {
                event.preventDefault();
                event.stopPropagation();
                return;
            }

            const developmentValid = validateRadioGroup(
                'development', developmentWrap, developmentClientError, true
            );
            const developmentTypeValid = validateRadioGroup(
                'development_type', developmentTypeWrap, developmentTypeClientError, true
            );

            toggleDevelopmentDetail(false);
            toggleSpecialSupport(false);
            form.classList.add('was-validated');

            if (!form.checkValidity() || !developmentValid || !developmentTypeValid) {
                event.preventDefault();
                event.stopPropagation();

                const firstInvalid = form.querySelector(
                    '.form-control:invalid, .form-select:invalid, input[name="development"]:invalid, input[name="development_type"]:invalid'
                );

                const focusTarget = firstInvalid || form.querySelector('input[name="assessor_date"]');

                if (focusTarget) {
                    try { focusTarget.focus({ preventScroll: true }); }
                    catch (error) { focusTarget.focus(); }

                    (focusTarget.closest('.cb-field, [data-development-wrap], [data-development-type-wrap]') || focusTarget)
                        .scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            form.dataset.submitting = 'true';

            if (submitButton) {
                submitButton.disabled = true;
            }
        });

        modalElement.addEventListener('show.bs.modal', function () {
            document.body.classList.add('checkbody-modal-open');
            toggleDevelopmentDetail(false);
            toggleSpecialSupport(false);
        });

        modalElement.addEventListener('shown.bs.modal', function () {
            if (modalBody) modalBody.scrollTop = 0;

            window.requestAnimationFrame(function () {
                const firstField = form.querySelector('.is-invalid') || form.querySelector('input[name="assessor_date"]');
                if (firstField) {
                    try { firstField.focus({ preventScroll: true }); }
                    catch (error) { firstField.focus(); }
                }
            });
        });

        modalElement.addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('checkbody-modal-open');
            if (modalBody) modalBody.scrollTop = 0;
            form.dataset.submitting = 'false';
            if (submitButton) submitButton.disabled = false;
        });

        toggleDevelopmentDetail(false);
        toggleSpecialSupport(false);

        if (shouldAutoOpen && window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalElement, {
                backdrop: 'static',
                keyboard: false
            }).show();
        }

        if (window.jQuery && $.fn.DataTable && document.getElementById('datatable-checkbody')) {
            const table = $('#datatable-checkbody');

            if (!$.fn.DataTable.isDataTable(table)) {
                const dataTable = table.DataTable({
                    autoWidth: false,
                    scrollX: true,
                    scrollCollapse: true,
                    order: [[0, 'desc']],
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    columnDefs: [{
                        orderable: false,
                        searchable: false,
                        width: '150px',
                        className: 'cb-col-actions',
                        targets: -1
                    }],
                    initComplete: function () {
                        const api = this.api();
                        window.requestAnimationFrame(function () { api.columns.adjust(); });
                    },
                    language: {
                        emptyTable: 'ไม่พบข้อมูล',
                        info: 'แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ',
                        infoEmpty: 'แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ',
                        infoFiltered: '(กรองจากทั้งหมด _MAX_ รายการ)',
                        lengthMenu: 'แสดง _MENU_ รายการ',
                        loadingRecords: 'กำลังโหลด...',
                        processing: 'กำลังประมวลผล...',
                        search: 'ค้นหา:',
                        zeroRecords: 'ไม่พบข้อมูลที่ตรงกับการค้นหา',
                        paginate: {
                            first: 'หน้าแรก', last: 'หน้าสุดท้าย', next: 'ถัดไป', previous: 'ก่อนหน้า'
                        }
                    }
                });

                let resizeTimer = null;
                window.addEventListener('resize', function () {
                    window.clearTimeout(resizeTimer);
                    resizeTimer = window.setTimeout(function () { dataTable.columns.adjust(); }, 120);
                });
            }
        }
    });

    function confirmDelete(id) {
        const form = document.getElementById('delete-form-' + id);
        if (!form) return;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'ยืนยันการลบข้อมูล',
                text: 'ข้อมูลที่ลบแล้วจะไม่สามารถกู้คืนได้',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'ลบข้อมูล',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                focusCancel: true
            }).then(function (result) {
                if (result.isConfirmed) form.submit();
            });
            return;
        }

        if (window.confirm('ยืนยันการลบข้อมูลนี้ใช่หรือไม่?')) form.submit();
    }
</script>

@if($errors->any() && old('_form_context') === 'checkbody_form')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'กรุณาตรวจสอบข้อมูล',
                html: @json(implode('<br>', $errors->all())),
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        }
    });
</script>
@endif
@endpush