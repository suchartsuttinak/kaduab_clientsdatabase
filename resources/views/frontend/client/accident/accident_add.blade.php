@extends('admin_client.admin_client')

{{-- ACCIDENT_EMPTY_STATE_FULL_FIX_V4 --}}

@section('content')
@php
    $isEdit = isset($accident) && $accident;
    $hasAccidentRows = isset($accidents) && $accidents->isNotEmpty();
    $doctorVisitCount = $hasAccidentRows ? $accidents->where('treat_no', 'พบแพทย์')->count() : 0;
    $nonDoctorVisitCount = $hasAccidentRows ? $accidents->where('treat_no', 'ไม่พบแพทย์')->count() : 0;
    $clientDisplayName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));

    if ($clientDisplayName === '') {
        $clientDisplayName = $client->name ?? $client->fullname ?? '-';
    }

    $clientAgeDisplay = filled($client->age ?? null)
        ? ($client->age . ' ปี')
        : '-';
@endphp

<style>
    /*
    |--------------------------------------------------------------------------
    | Accident Page — scoped styles only
    |--------------------------------------------------------------------------
    */
    .accident-page {
        --acc-primary: #1d4ed8;
        --acc-primary-dark: #1e3a8a;
        --acc-primary-soft: #eff6ff;
        --acc-success: #15803d;
        --acc-success-soft: #f0fdf4;
        --acc-warning: #b45309;
        --acc-warning-soft: #fffbeb;
        --acc-danger: #b91c1c;
        --acc-danger-soft: #fef2f2;
        --acc-text: #172033;
        --acc-muted: #64748b;
        --acc-border: #dbe4f0;
        --acc-border-soft: #e8eef6;
        --acc-surface: #ffffff;
        --acc-surface-soft: #f8fafc;
        --acc-radius-xl: 22px;
        --acc-radius-lg: 17px;
        --acc-radius-md: 12px;
        --acc-shadow: 0 18px 44px rgba(15, 23, 42, .08);
        --acc-shadow-sm: 0 6px 18px rgba(15, 23, 42, .06);

        color: var(--acc-text);
    }

    .accident-page * {
        box-sizing: border-box;
    }

    .accident-page .acc-shell {
        width: 100%;
        max-width: none;
        margin: 0;
    }

    .accident-page .acc-hero,
    .accident-page .acc-stat,
    .accident-page .acc-table-card,
    .accident-page .acc-empty-state {
        border: 1px solid var(--acc-border-soft);
        background: var(--acc-surface);
        box-shadow: var(--acc-shadow-sm);
    }

    .accident-page .acc-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 1rem;
        padding: 1.35rem 1.5rem;
        border-radius: var(--acc-radius-xl);
        background:
            radial-gradient(circle at top right, rgba(59, 130, 246, .14), transparent 34%),
            linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    }

    .accident-page .acc-hero::after {
        content: "";
        position: absolute;
        right: -80px;
        bottom: -95px;
        width: 220px;
        height: 220px;
        border: 38px solid rgba(37, 99, 235, .055);
        border-radius: 50%;
        pointer-events: none;
    }

    .accident-page .acc-hero-grid {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 1rem 1.5rem;
        align-items: center;
    }

    .accident-page .acc-heading-row {
        display: flex;
        min-width: 0;
        align-items: flex-start;
        gap: .9rem;
    }

    .accident-page .acc-heading-icon {
        display: inline-flex;
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        align-items: center;
        justify-content: center;
        border: 1px solid #bfdbfe;
        border-radius: 16px;
        background: var(--acc-primary-soft);
        color: var(--acc-primary);
        font-size: 1.35rem;
        box-shadow: 0 8px 20px rgba(37, 99, 235, .10);
    }

    .accident-page .acc-heading-content {
        min-width: 0;
    }

    .accident-page .acc-page-title {
        margin: 0;
        color: #0f172a;
        font-size: clamp(1.2rem, 2vw, 1.55rem);
        font-weight: 800;
        line-height: 1.35;
        letter-spacing: -.01em;
    }

    .accident-page .acc-page-subtitle {
        max-width: 820px;
        margin: .28rem 0 0;
        color: var(--acc-muted);
        font-size: .9rem;
        line-height: 1.7;
    }

    .accident-page .acc-client-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .55rem;
        margin-top: .9rem;
    }

    .accident-page .acc-meta-chip {
        display: inline-flex;
        max-width: 100%;
        align-items: center;
        gap: .42rem;
        padding: .48rem .75rem;
        border: 1px solid var(--acc-border);
        border-radius: 999px;
        background: rgba(255, 255, 255, .88);
        color: #334155;
        font-size: .8rem;
        line-height: 1.35;
        box-shadow: 0 3px 10px rgba(15, 23, 42, .035);
    }

    .accident-page .acc-meta-chip i {
        color: var(--acc-primary);
    }

    .accident-page .acc-meta-chip strong {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .accident-page .acc-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: .65rem;
    }

    .accident-page .acc-btn {
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

    .accident-page .acc-btn:hover {
        transform: translateY(-1px);
    }

    .accident-page .acc-btn-primary {
        border: 1px solid var(--acc-primary);
        background: var(--acc-primary);
        color: #ffffff;
    }

    .accident-page .acc-btn-primary:hover {
        border-color: var(--acc-primary-dark);
        background: var(--acc-primary-dark);
        color: #ffffff;
        box-shadow: 0 9px 20px rgba(29, 78, 216, .20);
    }

    .accident-page .acc-btn-secondary {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
    }

    .accident-page .acc-btn-secondary:hover {
        border-color: #94a3b8;
        background: #f8fafc;
        color: #1e293b;
    }


    /**
     |--------------------------------------------------------------------------
     | Accident Empty State
     |--------------------------------------------------------------------------
     | จัดรูปแบบกรณียังไม่มีข้อมูลให้เป็นการ์ดกะทัดรัดและอยู่กึ่งกลาง
     */
    .accident-page .acc-empty-state {
        position: relative;
        overflow: hidden;
        display: flex;
        min-height: 270px;
        margin-bottom: 1rem;
        padding: 2.35rem 1.5rem;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        border-radius: var(--acc-radius-xl);
        text-align: center;
        isolation: isolate;
    }

    .accident-page .acc-empty-state::before {
        content: "";
        position: absolute;
        z-index: -1;
        top: -76px;
        right: -64px;
        width: 190px;
        height: 190px;
        border: 32px solid rgba(37, 99, 235, .045);
        border-radius: 50%;
        pointer-events: none;
    }

    .accident-page .acc-empty-state::after {
        content: "";
        position: absolute;
        z-index: -1;
        left: -52px;
        bottom: -78px;
        width: 170px;
        height: 170px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(59, 130, 246, .065) 0%, rgba(59, 130, 246, 0) 70%);
        pointer-events: none;
    }

    .accident-page .acc-empty-icon {
        display: inline-flex;
        width: 62px;
        height: 62px;
        flex: 0 0 62px;
        margin-bottom: 1rem;
        align-items: center;
        justify-content: center;
        border: 1px solid #bfdbfe;
        border-radius: 18px;
        background: var(--acc-primary-soft);
        color: var(--acc-primary);
        font-size: 1.55rem;
        line-height: 1;
        box-shadow: 0 10px 24px rgba(37, 99, 235, .11);
    }

    .accident-page .acc-empty-title {
        margin: 0;
        color: #0f172a;
        font-size: clamp(1.12rem, 2vw, 1.38rem);
        font-weight: 800;
        line-height: 1.4;
    }

    .accident-page .acc-empty-text {
        max-width: 650px;
        margin: .48rem auto 0;
        color: var(--acc-muted);
        font-size: .88rem;
        line-height: 1.75;
    }

    .accident-page .acc-empty-state .acc-btn {
        margin-top: 1.15rem;
    }

    .accident-page .acc-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .85rem;
        margin-bottom: 1rem;
    }

    .accident-page .acc-stat {
        min-width: 0;
        padding: 1rem 1.05rem;
        border-radius: var(--acc-radius-lg);
    }

    .accident-page .acc-stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .accident-page .acc-stat-label {
        color: var(--acc-muted);
        font-size: .78rem;
        font-weight: 650;
        line-height: 1.45;
    }

    .accident-page .acc-stat-icon {
        display: inline-flex;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: var(--acc-primary-soft);
        color: var(--acc-primary);
    }

    .accident-page .acc-stat-value {
        margin-top: .5rem;
        overflow: hidden;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.4;
        text-overflow: ellipsis;
    }

    .accident-page .acc-table-card {
        overflow: hidden;
        border-radius: var(--acc-radius-xl);
    }

    .accident-page .acc-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.05rem 1.25rem;
        border-bottom: 1px solid var(--acc-border-soft);
        background: #ffffff;
    }

    .accident-page .acc-card-heading {
        display: flex;
        min-width: 0;
        align-items: flex-start;
        gap: .72rem;
    }

    .accident-page .acc-card-heading-icon {
        display: inline-flex;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: var(--acc-primary-soft);
        color: var(--acc-primary);
    }

    .accident-page .acc-card-title {
        margin: 0;
        color: #172033;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.4;
    }

    .accident-page .acc-card-subtitle {
        margin: .15rem 0 0;
        color: var(--acc-muted);
        font-size: .79rem;
        line-height: 1.55;
    }

    .accident-page .acc-count-badge {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        gap: .35rem;
        padding: .42rem .7rem;
        border: 1px solid var(--acc-border);
        border-radius: 999px;
        background: var(--acc-surface-soft);
        color: #475569;
        font-size: .78rem;
        font-weight: 700;
    }

    .accident-page .acc-card-body {
        padding: 1.05rem 1.15rem 1.2rem;
    }

    .accident-page .acc-table-wrap {
        width: 100%;
        min-width: 0;
        overflow: hidden;
        border: 1px solid var(--acc-border-soft);
        border-radius: 14px;
        background: #ffffff;
    }

    /*
    |--------------------------------------------------------------------------
    | Accident DataTable — single, stable layout
    |--------------------------------------------------------------------------
    | DataTables ดูแลการเลื่อนแนวนอนเพียงจุดเดียว ป้องกันหัวตารางเหลื่อม
    | ช่องค้นหา/จำนวนรายการซ้ำ และ scrollbar ซ้อนจาก wrapper หลายชั้น
    */
    .accident-page .dataTables_wrapper {
        width: 100%;
        min-width: 0;
        margin: 0;
        color: var(--acc-text);
    }

    .accident-page .acc-dt-top,
    .accident-page .acc-dt-bottom {
        display: flex;
        width: 100%;
        min-width: 0;
        align-items: center;
        justify-content: space-between;
        gap: .85rem 1rem;
        margin: 0;
    }

    .accident-page .acc-dt-top {
        padding: .9rem 1rem .75rem;
        border-bottom: 1px solid #eef2f7;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .accident-page .acc-dt-bottom {
        padding: .8rem 1rem .95rem;
        border-top: 1px solid #eef2f7;
        background: #ffffff;
    }

    .accident-page .acc-dt-length,
    .accident-page .acc-dt-search,
    .accident-page .acc-dt-info,
    .accident-page .acc-dt-paging {
        min-width: 0;
    }

    .accident-page .acc-dt-length {
        flex: 1 1 auto;
    }

    .accident-page .acc-dt-search,
    .accident-page .acc-dt-paging {
        flex: 0 0 auto;
        margin-left: auto;
    }

    .accident-page .dataTables_wrapper .dataTables_length,
    .accident-page .dataTables_wrapper .dataTables_filter,
    .accident-page .dataTables_wrapper .dataTables_info,
    .accident-page .dataTables_wrapper .dataTables_paginate {
        float: none !important;
        width: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        color: var(--acc-muted);
        font-size: .82rem;
    }

    .accident-page .dataTables_wrapper .dataTables_length label,
    .accident-page .dataTables_wrapper .dataTables_filter label {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin: 0 !important;
        color: #475569;
        font-size: .84rem;
        font-weight: 650;
        line-height: 1.4;
        white-space: nowrap;
    }

    .accident-page .dataTables_wrapper .dataTables_filter label {
        justify-content: flex-end;
    }

    .accident-page .dataTables_wrapper .dataTables_length select,
    .accident-page .dataTables_wrapper .dataTables_filter input {
        height: 40px;
        min-height: 40px;
        margin: 0 !important;
        border: 1px solid var(--acc-border);
        border-radius: 11px;
        background-color: #ffffff;
        color: #0f172a;
        font-size: .86rem;
        box-shadow: none;
        outline: none;
        transition: border-color .16s ease, box-shadow .16s ease;
    }

    .accident-page .dataTables_wrapper .dataTables_length select {
        min-width: 76px;
        padding: .38rem 2rem .38rem .72rem;
    }

    .accident-page .dataTables_wrapper .dataTables_filter input {
        width: 210px !important;
        min-width: 210px;
        padding: .42rem .75rem;
    }

    .accident-page .dataTables_wrapper .dataTables_length select:focus,
    .accident-page .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 .18rem rgba(59, 130, 246, .13);
    }

    .accident-page .dataTables_wrapper .dataTables_scroll,
    .accident-page .dataTables_wrapper .dataTables_scrollHead,
    .accident-page .dataTables_wrapper .dataTables_scrollBody {
        width: 100%;
        min-width: 0;
    }

    .accident-page .dataTables_wrapper .dataTables_scrollHead {
        overflow: hidden !important;
        border: 0 !important;
        background: #f8fafc;
    }

    .accident-page .dataTables_wrapper .dataTables_scrollBody {
        overflow-x: auto !important;
        overflow-y: hidden !important;
        border: 0 !important;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
        -webkit-overflow-scrolling: touch;
    }

    .accident-page .dataTables_wrapper .dataTables_scrollBody::-webkit-scrollbar {
        height: 8px;
    }

    .accident-page .dataTables_wrapper .dataTables_scrollBody::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: #cbd5e1;
    }

    .accident-page .dataTables_wrapper .dataTables_scrollHeadInner,
    .accident-page .dataTables_wrapper .dataTables_scrollHeadInner > table,
    .accident-page .dataTables_wrapper .dataTables_scrollBody > table {
        box-sizing: border-box !important;
        margin: 0 !important;
    }

    .accident-page .acc-table {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0;
        table-layout: fixed;
    }

    .accident-page .acc-table.acc-table-compact {
        min-width: 1150px;
    }

    .accident-page .acc-table.acc-table-expanded {
        min-width: 1450px;
    }

    .accident-page .acc-col-date {
        width: 120px !important;
        min-width: 120px !important;
    }

    .accident-page .acc-col-location {
        width: 155px !important;
        min-width: 155px !important;
    }

    .accident-page .acc-col-detail {
        width: 280px !important;
        min-width: 280px !important;
    }

    .accident-page .acc-col-cause {
        width: 160px !important;
        min-width: 160px !important;
    }

    .accident-page .acc-col-treatment {
        width: 145px !important;
        min-width: 145px !important;
    }

    .accident-page .acc-col-hospital {
        width: 165px !important;
        min-width: 165px !important;
    }

    .accident-page .acc-col-appointment {
        width: 135px !important;
        min-width: 135px !important;
    }

    .accident-page .acc-col-caretaker {
        width: 140px !important;
        min-width: 140px !important;
    }

    .accident-page .acc-col-actions {
        position: static !important;
        right: auto !important;
        width: 150px !important;
        min-width: 150px !important;
        max-width: 150px !important;
        border-left: 1px solid var(--acc-border-soft) !important;
        box-shadow: none !important;
    }

    .accident-page .acc-table thead .acc-col-actions {
        background: #f8fafc;
    }

    .accident-page .acc-table tbody .acc-col-actions {
        background: #ffffff;
        text-align: center !important;
    }

    .accident-page .acc-table tbody tr:hover .acc-col-actions {
        background: #fbfdff;
    }

    .accident-page .acc-text-block {
        display: block;
        min-width: 0;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .accident-page .acc-text-clamp {
        display: -webkit-box;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }

    .accident-page .acc-table thead th {
        padding: .82rem .75rem;
        border-top: 0;
        border-bottom: 1px solid var(--acc-border);
        background: #f8fafc;
        color: #334155;
        font-size: .78rem;
        font-weight: 800;
        line-height: 1.35;
        vertical-align: middle;
        white-space: nowrap;
    }

    .accident-page .acc-table tbody td {
        padding: .82rem .75rem;
        border-color: #edf1f6;
        background: #ffffff;
        color: #334155;
        font-size: .83rem;
        line-height: 1.55;
        vertical-align: middle;
    }

    .accident-page .acc-table tbody tr:nth-child(even) td {
        background: #fcfdff;
    }

    .accident-page .acc-table tbody tr:hover td {
        background: #f8fbff;
    }

    .accident-page .acc-table .acc-cell-main {
        color: #1e293b;
        font-weight: 700;
    }

    .accident-page .acc-table .acc-cell-muted {
        color: var(--acc-muted);
    }

    .accident-page .acc-status {
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

    .accident-page .acc-status-doctor {
        border-color: #bbf7d0;
        background: var(--acc-success-soft);
        color: var(--acc-success);
    }

    .accident-page .acc-status-home {
        border-color: #e2e8f0;
        background: #f8fafc;
        color: #64748b;
    }

    .accident-page .acc-row-actions {
        display: inline-flex;
        width: auto;
        min-width: 36px;
        margin: 0 auto;
        align-items: center;
        justify-content: center;
        gap: .38rem;
        white-space: nowrap;
        vertical-align: middle;
    }

    .accident-page .acc-icon-btn {
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

    .accident-page .acc-icon-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(15, 23, 42, .12);
    }

    .accident-page .dataTables_wrapper .dataTables_info {
        line-height: 1.45;
    }

    .accident-page .dataTables_wrapper .dataTables_paginate {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .15rem;
        white-space: nowrap;
    }

    .accident-page .dataTables_wrapper .dataTables_paginate .paginate_button {
        min-width: 34px;
        min-height: 34px;
        margin: 0 1px !important;
        padding: .38rem .62rem !important;
        border-radius: 9px !important;
    }

    @media (max-width: 767.98px) {
        .accident-page .acc-empty-state {
            min-height: 235px;
            padding: 1.85rem 1rem;
            border-radius: 18px;
        }

        .accident-page .acc-empty-icon {
            width: 56px;
            height: 56px;
            flex-basis: 56px;
            border-radius: 16px;
            font-size: 1.38rem;
        }

        .accident-page .acc-empty-state .acc-btn {
            width: auto;
            max-width: 100%;
        }

        .accident-page .acc-dt-top,
        .accident-page .acc-dt-bottom {
            align-items: stretch;
            flex-direction: column;
        }

        .accident-page .acc-dt-search,
        .accident-page .acc-dt-paging {
            width: 100%;
            margin-left: 0;
        }

        .accident-page .dataTables_wrapper .dataTables_filter,
        .accident-page .dataTables_wrapper .dataTables_filter label {
            width: 100% !important;
            justify-content: flex-start;
        }

        .accident-page .dataTables_wrapper .dataTables_filter input {
            width: 100% !important;
            min-width: 0;
            flex: 1 1 auto;
        }

        .accident-page .dataTables_wrapper .dataTables_paginate {
            justify-content: flex-start;
            overflow-x: auto;
            padding-bottom: .15rem !important;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Accident Modal — scoped by ID and moved to body by JavaScript
    |--------------------------------------------------------------------------
    */
    #accidentFormModal {
        --accm-primary: #1d4ed8;
        --accm-primary-dark: #1e3a8a;
        --accm-primary-soft: #eff6ff;
        --accm-success: #15803d;
        --accm-success-dark: #166534;
        --accm-success-soft: #f0fdf4;
        --accm-danger: #dc2626;
        --accm-danger-soft: #fef2f2;
        --accm-text: #172033;
        --accm-muted: #64748b;
        --accm-border: #dbe4f0;
        --accm-border-soft: #e8eef6;
        --accm-surface: #ffffff;
        --accm-surface-soft: #f8fafc;
        --accm-radius-xl: 22px;
        --accm-radius-lg: 16px;
        --accm-radius-md: 12px;

        z-index: 2147483000 !important;
        padding-right: 0 !important;
        color: var(--accm-text);
    }

    body.accident-modal-open .modal-backdrop {
        z-index: 2147482990 !important;
    }

    body.accident-modal-open {
        overflow: hidden;
    }

    #accidentFormModal .modal-dialog {
        width: calc(100% - 2rem);
        max-width: 1040px;
        height: calc(100dvh - 2rem);
        min-height: 0;
        margin: 1rem auto;
    }

    #accidentFormModal .modal-content {
        display: flex;
        width: 100%;
        height: 100%;
        min-height: 0;
        overflow: hidden;
        border: 0;
        border-radius: var(--accm-radius-xl);
        background: var(--accm-surface);
        box-shadow: 0 30px 80px rgba(15, 23, 42, .24), 0 8px 24px rgba(15, 23, 42, .12);
    }

    #accidentFormModal .acc-modal-form {
        display: flex;
        width: 100%;
        min-height: 0;
        flex: 1 1 auto;
        flex-direction: column;
    }

    #accidentFormModal .modal-header {
        position: relative;
        z-index: 2;
        display: flex;
        flex: 0 0 auto;
        align-items: center;
        gap: 1rem;
        padding: 1.15rem 1.35rem;
        border-bottom: 1px solid var(--accm-border-soft);
        background:
            radial-gradient(circle at right top, rgba(59, 130, 246, .13), transparent 42%),
            linear-gradient(135deg, #ffffff 0%, #f7fbff 100%);
    }

    #accidentFormModal .acc-modal-heading {
        display: flex;
        min-width: 0;
        flex: 1 1 auto;
        align-items: center;
        gap: .8rem;
    }

    #accidentFormModal .acc-modal-heading-icon {
        display: inline-flex;
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        align-items: center;
        justify-content: center;
        border: 1px solid #bfdbfe;
        border-radius: 14px;
        background: var(--accm-primary-soft);
        color: var(--accm-primary);
        font-size: 1.2rem;
    }

    #accidentFormModal .acc-modal-heading-content {
        min-width: 0;
    }

    #accidentFormModal .acc-modal-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.08rem;
        font-weight: 800;
        line-height: 1.4;
    }

    #accidentFormModal .acc-modal-subtitle {
        margin: .18rem 0 0;
        color: var(--accm-muted);
        font-size: .8rem;
        line-height: 1.5;
    }

    #accidentFormModal .acc-mode-badge {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        gap: .35rem;
        padding: .38rem .62rem;
        border: 1px solid #bfdbfe;
        border-radius: 999px;
        background: #ffffff;
        color: var(--accm-primary-dark);
        font-size: .72rem;
        font-weight: 800;
        white-space: nowrap;
    }

    #accidentFormModal .acc-modal-close {
        display: inline-flex;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        align-items: center;
        justify-content: center;
        margin: 0;
        padding: 0;
        border: 1px solid var(--accm-border);
        border-radius: 11px;
        background: #ffffff;
        color: #64748b;
        text-decoration: none;
        box-shadow: none;
        opacity: 1;
        transition: background-color .18s ease, border-color .18s ease, color .18s ease;
    }

    #accidentFormModal .acc-modal-close:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        color: #0f172a;
    }

    #accidentFormModal .acc-modal-body {
        min-height: 0;
        flex: 1 1 auto;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 1.15rem 1.25rem 1.35rem;
        background: var(--accm-surface-soft);
        overscroll-behavior: contain;
        scrollbar-gutter: stable;
        -webkit-overflow-scrolling: touch;
    }

    #accidentFormModal .acc-form-section {
        padding: 1.1rem;
        border: 1px solid var(--accm-border-soft);
        border-radius: var(--accm-radius-lg);
        background: #ffffff;
    }

    #accidentFormModal .acc-form-section + .acc-form-section {
        margin-top: .9rem;
    }

    #accidentFormModal .acc-section-heading {
        display: flex;
        align-items: flex-start;
        gap: .7rem;
        margin-bottom: 1rem;
    }

    #accidentFormModal .acc-section-icon {
        display: inline-flex;
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: var(--accm-primary-soft);
        color: var(--accm-primary);
    }

    #accidentFormModal .acc-section-title {
        margin: 0;
        color: #172033;
        font-size: .95rem;
        font-weight: 800;
        line-height: 1.4;
    }

    #accidentFormModal .acc-section-description {
        margin: .14rem 0 0;
        color: var(--accm-muted);
        font-size: .76rem;
        line-height: 1.5;
    }

    #accidentFormModal .acc-field {
        position: relative;
    }

    #accidentFormModal .acc-label {
        display: inline-flex;
        align-items: center;
        gap: .2rem;
        margin-bottom: .42rem;
        color: #334155;
        font-size: .82rem;
        font-weight: 750;
        line-height: 1.4;
    }

    #accidentFormModal .acc-required {
        color: var(--accm-danger);
        font-size: .78rem;
    }

    #accidentFormModal .form-control,
    #accidentFormModal .form-select {
        width: 100%;
        min-height: 45px;
        padding: .64rem .8rem;
        border: 1px solid var(--accm-border);
        border-radius: var(--accm-radius-md);
        background: #ffffff;
        color: var(--accm-text);
        font-size: .86rem;
        box-shadow: none;
        transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    #accidentFormModal textarea.form-control {
        min-height: 100px;
        line-height: 1.65;
        resize: vertical;
    }

    #accidentFormModal .form-control::placeholder {
        color: #94a3b8;
    }

    #accidentFormModal .form-control:hover,
    #accidentFormModal .form-select:hover {
        border-color: #b8c5d6;
    }

    #accidentFormModal .form-control:focus,
    #accidentFormModal .form-select:focus {
        border-color: var(--accm-primary);
        background: #ffffff;
        box-shadow: 0 0 0 .2rem rgba(29, 78, 216, .11);
    }

    #accidentFormModal .form-control.is-invalid,
    #accidentFormModal .was-validated .form-control:invalid,
    #accidentFormModal .was-validated .form-select:invalid {
        border-color: var(--accm-danger) !important;
        background-color: var(--accm-danger-soft);
        box-shadow: 0 0 0 .18rem rgba(220, 38, 38, .08) !important;
    }

    #accidentFormModal .invalid-feedback,
    #accidentFormModal .acc-invalid-feedback {
        display: block;
        margin-top: .38rem;
        color: var(--accm-danger);
        font-size: .74rem;
        font-weight: 650;
        line-height: 1.45;
    }

    #accidentFormModal .acc-radio-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .65rem;
    }

    #accidentFormModal .acc-radio-card {
        position: relative;
        min-width: 0;
    }

    #accidentFormModal .acc-radio-input {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        opacity: 0;
        pointer-events: none;
    }

    #accidentFormModal .acc-radio-label {
        display: flex;
        min-height: 62px;
        margin: 0;
        padding: .75rem .82rem;
        align-items: center;
        gap: .68rem;
        border: 1px solid var(--accm-border);
        border-radius: 13px;
        background: #ffffff;
        color: #334155;
        cursor: pointer;
        font-size: .82rem;
        font-weight: 750;
        line-height: 1.35;
        transition: border-color .18s ease, background-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }

    #accidentFormModal .acc-radio-label:hover {
        border-color: #93b4f8;
        background: #fbfdff;
        transform: translateY(-1px);
    }

    #accidentFormModal .acc-radio-icon {
        display: inline-flex;
        width: 35px;
        height: 35px;
        flex: 0 0 35px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #f1f5f9;
        color: #64748b;
    }

    #accidentFormModal .acc-radio-check {
        display: inline-flex;
        width: 21px;
        height: 21px;
        flex: 0 0 21px;
        align-items: center;
        justify-content: center;
        margin-left: auto;
        border: 1.5px solid #cbd5e1;
        border-radius: 50%;
        background: #ffffff;
        color: transparent;
        font-size: .7rem;
    }

    #accidentFormModal .acc-radio-input:checked + .acc-radio-label {
        border-color: var(--accm-primary);
        background: var(--accm-primary-soft);
        color: var(--accm-primary-dark);
        box-shadow: 0 0 0 .16rem rgba(29, 78, 216, .08);
    }

    #accidentFormModal .acc-radio-input:checked + .acc-radio-label .acc-radio-icon {
        background: var(--accm-primary);
        color: #ffffff;
    }

    #accidentFormModal .acc-radio-input:checked + .acc-radio-label .acc-radio-check {
        border-color: var(--accm-primary);
        background: var(--accm-primary);
        color: #ffffff;
    }

    #accidentFormModal .acc-radio-input:focus-visible + .acc-radio-label {
        border-color: var(--accm-primary);
        box-shadow: 0 0 0 .2rem rgba(29, 78, 216, .13);
    }

    #accidentFormModal .acc-radio-wrap.acc-radio-invalid .acc-radio-label {
        border-color: rgba(220, 38, 38, .55);
        background: var(--accm-danger-soft);
    }

    #accidentFormModal .acc-medical-panel {
        overflow: hidden;
        max-height: 520px;
        margin-top: .85rem;
        padding: .95rem;
        border: 1px dashed #bfdbfe;
        border-radius: 14px;
        background: #f8fbff;
        opacity: 1;
        transition: max-height .24s ease, margin .24s ease, padding .24s ease, opacity .2s ease, border-width .2s ease;
    }

    #accidentFormModal .acc-medical-panel.is-hidden {
        max-height: 0;
        margin-top: 0;
        padding-top: 0;
        padding-bottom: 0;
        border-width: 0;
        opacity: 0;
        pointer-events: none;
    }

    #accidentFormModal .acc-modal-footer {
        position: relative;
        z-index: 2;
        display: flex;
        flex: 0 0 auto;
        align-items: center;
        justify-content: flex-end;
        gap: .65rem;
        padding: .9rem 1.25rem;
        border-top: 1px solid var(--accm-border-soft);
        background: #ffffff;
    }

    #accidentFormModal .acc-modal-btn {
        display: inline-flex;
        min-width: 126px;
        min-height: 43px;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        padding: .62rem .95rem;
        border-radius: 11px;
        font-size: .82rem;
        font-weight: 750;
        line-height: 1.2;
        text-decoration: none;
        box-shadow: none;
        transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease, border-color .18s ease;
    }

    #accidentFormModal .acc-modal-btn:hover {
        transform: translateY(-1px);
    }

    #accidentFormModal .acc-modal-btn-cancel {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
    }

    #accidentFormModal .acc-modal-btn-cancel:hover {
        border-color: #94a3b8;
        background: #f8fafc;
        color: #1e293b;
    }

    #accidentFormModal .acc-modal-btn-save {
        border: 1px solid var(--accm-success);
        background: var(--accm-success);
        color: #ffffff;
    }

    #accidentFormModal .acc-modal-btn-save:hover {
        border-color: var(--accm-success-dark);
        background: var(--accm-success-dark);
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(21, 128, 61, .20);
    }

    #accidentFormModal .acc-modal-btn:disabled {
        cursor: not-allowed;
        opacity: 1;
        transform: none;
    }

    @media (max-width: 1199.98px) {
        .accident-page .acc-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .accident-page .acc-hero-grid {
            grid-template-columns: 1fr;
        }

        .accident-page .acc-actions {
            justify-content: flex-start;
        }
    }

    @media (max-width: 767.98px) {
        .accident-page .acc-hero {
            padding: 1.1rem;
            border-radius: 18px;
        }

        .accident-page .acc-heading-icon {
            width: 46px;
            height: 46px;
            flex-basis: 46px;
        }

        .accident-page .acc-actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        .accident-page .acc-btn {
            width: 100%;
        }

        .accident-page .acc-card-header {
            align-items: flex-start;
            padding: .95rem 1rem;
        }

        .accident-page .acc-card-body {
            padding: .9rem;
        }

        #accidentFormModal {
            padding: 0 !important;
        }

        #accidentFormModal .modal-dialog {
            width: 100%;
            max-width: none;
            height: 100dvh;
            margin: 0;
        }

        #accidentFormModal .modal-content {
            height: 100dvh;
            border-radius: 0;
        }

        #accidentFormModal .modal-header {
            padding: calc(.95rem + env(safe-area-inset-top)) 1rem .95rem;
        }

        #accidentFormModal .acc-modal-body {
            padding: .9rem;
        }

        #accidentFormModal .acc-form-section {
            padding: .95rem;
        }

        #accidentFormModal .acc-modal-footer {
            padding: .8rem 1rem calc(.8rem + env(safe-area-inset-bottom));
        }
    }

    @media (max-width: 575.98px) {
        .accident-page .acc-stats {
            grid-template-columns: 1fr;
        }

        .accident-page .acc-heading-row {
            gap: .72rem;
        }

        .accident-page .acc-meta-chip strong {
            white-space: normal;
        }

        .accident-page .acc-card-header {
            flex-direction: column;
        }

        .accident-page .acc-count-badge {
            align-self: flex-start;
        }

        #accidentFormModal .acc-modal-heading {
            gap: .68rem;
        }

        #accidentFormModal .acc-modal-heading-icon {
            width: 42px;
            height: 42px;
            flex-basis: 42px;
        }

        #accidentFormModal .acc-modal-subtitle,
        #accidentFormModal .acc-mode-badge {
            display: none;
        }

        #accidentFormModal .acc-radio-grid {
            grid-template-columns: 1fr;
        }

        #accidentFormModal .acc-modal-footer {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        #accidentFormModal .acc-modal-btn {
            width: 100%;
            min-width: 0;
        }
    }

    @media (max-width: 380px) {
        #accidentFormModal .acc-modal-footer {
            grid-template-columns: 1fr;
        }

        #accidentFormModal .acc-modal-btn-save {
            order: -1;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .accident-page *,
        #accidentFormModal *,
        .accident-page *::before,
        .accident-page *::after,
        #accidentFormModal *::before,
        #accidentFormModal *::after {
            scroll-behavior: auto !important;
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: .01ms !important;
        }
    }

    /*
     * Final Empty State override V4
     * ใช้ ID เพื่อป้องกัน CSS จาก layout/theme เขียนทับ
     */
    .accident-page #accidentEmptyState {
        position: relative !important;
        overflow: hidden !important;
        display: flex !important;
        width: 100% !important;
        min-height: 290px !important;
        margin: 0 0 1rem !important;
        padding: 2.4rem 1.5rem !important;
        align-items: center !important;
        justify-content: center !important;
        flex-direction: column !important;
        border: 1px solid #e8eef6 !important;
        border-radius: 22px !important;
        background: #ffffff !important;
        box-shadow: 0 6px 18px rgba(15, 23, 42, .06) !important;
        text-align: center !important;
    }

    .accident-page #accidentEmptyState > .acc-empty-icon {
        display: inline-flex !important;
        width: 62px !important;
        height: 62px !important;
        flex: 0 0 62px !important;
        margin: 0 0 1rem !important;
        align-items: center !important;
        justify-content: center !important;
        border: 1px solid #bfdbfe !important;
        border-radius: 18px !important;
        background: #eff6ff !important;
        color: #1d4ed8 !important;
        font-size: 1.55rem !important;
        line-height: 1 !important;
        box-shadow: 0 10px 24px rgba(37, 99, 235, .11) !important;
    }

    .accident-page #accidentEmptyState > .acc-empty-title {
        margin: 0 !important;
        color: #0f172a !important;
        font-size: clamp(1.12rem, 2vw, 1.38rem) !important;
        font-weight: 800 !important;
        line-height: 1.4 !important;
        text-align: center !important;
    }

    .accident-page #accidentEmptyState > .acc-empty-text {
        width: 100% !important;
        max-width: 650px !important;
        margin: .48rem auto 0 !important;
        color: #64748b !important;
        font-size: .88rem !important;
        line-height: 1.75 !important;
        text-align: center !important;
    }

    .accident-page #accidentEmptyState > .acc-btn {
        margin-top: 1.15rem !important;
    }

    @media (max-width: 575.98px) {
        .accident-page #accidentEmptyState {
            min-height: 250px !important;
            padding: 2rem 1rem !important;
            border-radius: 18px !important;
        }

        .accident-page #accidentEmptyState > .acc-empty-icon {
            width: 56px !important;
            height: 56px !important;
            flex-basis: 56px !important;
            border-radius: 16px !important;
            font-size: 1.38rem !important;
        }

        .accident-page #accidentEmptyState > .acc-btn {
            width: 100% !important;
            max-width: 310px !important;
        }
    }
</style>

<div class="container-fluid px-2 px-lg-3 accident-page">
    <div class="acc-shell">
        <header class="acc-hero">
            <div class="acc-hero-grid">
                <div class="acc-heading-row">
                    <div class="acc-heading-icon" aria-hidden="true">
                        <i class="bi bi-shield-plus"></i>
                    </div>

                    <div class="acc-heading-content">
                        <h1 class="acc-page-title">บันทึกข้อมูลการบาดเจ็บ</h1>
                        <p class="acc-page-subtitle">
                            จัดเก็บประวัติการเกิดเหตุ การรักษา การพบแพทย์ และมาตรการป้องกันอย่างเป็นระบบ
                        </p>

                        <div class="acc-client-meta">
                            <span class="acc-meta-chip">
                                <i class="bi bi-person"></i>
                                <span>ผู้รับบริการ:</span>
                                <strong>{{ $clientDisplayName }}</strong>
                            </span>

                            @if(!empty($client->cid))
                                <span class="acc-meta-chip">
                                    <i class="bi bi-card-text"></i>
                                    <span>เลขประจำตัว:</span>
                                    <strong>{{ $client->cid }}</strong>
                                </span>
                            @endif

                            <span class="acc-meta-chip">
                                <i class="bi bi-calendar-heart"></i>
                                <span>อายุ:</span>
                                <strong>{{ $clientAgeDisplay }}</strong>
                            </span>
                        </div>
                    </div>
                </div>

                @if($hasAccidentRows)
                    <div class="acc-actions">
                        @if($isEdit)
                            <a href="{{ route('accident.add', $client->id) }}"
                               class="acc-btn acc-btn-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                <span>ยกเลิกการแก้ไข</span>
                            </a>
                        @endif

                        <button type="button"
                                class="btn acc-btn acc-btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#accidentFormModal">
                            <i class="bi {{ $isEdit ? 'bi-pencil-square' : 'bi-plus-circle' }}"></i>
                            <span>{{ $isEdit ? 'เปิดฟอร์มแก้ไข' : 'เพิ่มข้อมูลการบาดเจ็บ' }}</span>
                        </button>
                    </div>
                @endif
            </div>
        </header>

        @if($hasAccidentRows)
            <section class="acc-stats" aria-label="สรุปข้อมูลการบาดเจ็บ">
                <article class="acc-stat">
                    <div class="acc-stat-top">
                        <span class="acc-stat-label">รายการล่าสุด</span>
                        <span class="acc-stat-icon"><i class="bi bi-calendar-event"></i></span>
                    </div>
                    <div class="acc-stat-value">
                        {{ \App\Helpers\*ThaiDateHelper::formatThaiShort(optional($accidents->first())->incident_date) }}
                    </div>
                </article>

                <article class="acc-stat">
                    <div class="acc-stat-top">
                        <span class="acc-stat-label">พบแพทย์</span>
                        <span class="acc-stat-icon"><i class="bi bi-hospital"></i></span>
                    </div>
                    <div class="acc-stat-value">{{ number_format($doctorVisitCount) }} รายการ</div>
                </article>

                <article class="acc-stat">
                    <div class="acc-stat-top">
                        <span class="acc-stat-label">ไม่พบแพทย์</span>
                        <span class="acc-stat-icon"><i class="bi bi-house-heart"></i></span>
                    </div>
                    <div class="acc-stat-value">{{ number_format($nonDoctorVisitCount) }} รายการ</div>
                </article>

                <article class="acc-stat">
                    <div class="acc-stat-top">
                        <span class="acc-stat-label">สถานะหน้าปัจจุบัน</span>
                        <span class="acc-stat-icon"><i class="bi bi-window-stack"></i></span>
                    </div>
                    <div class="acc-stat-value">{{ $isEdit ? 'โหมดแก้ไขข้อมูล' : 'พร้อมเพิ่มรายการใหม่' }}</div>
                </article>
            </section>
        @endif

        @if($hasAccidentRows)
            @include('frontend.client.accident._table')
        @else
            {{--
                ใช้ inline critical styles เฉพาะ Empty State เพื่อป้องกัน CSS จาก Layout/Theme
                เขียนทับจนรูปแบบเพี้ยน โดยยังคง class เดิมสำหรับ responsive และมาตรฐานหน้า Accident
            --}}
            <section id="accidentEmptyState" class="acc-empty-state"
                     style="position:relative !important;
                            overflow:hidden !important;
                            display:flex !important;
                            width:100% !important;
                            min-height:290px !important;
                            margin:0 0 1rem !important;
                            padding:2.4rem 1.5rem !important;
                            align-items:center !important;
                            justify-content:center !important;
                            flex-direction:column !important;
                            border:1px solid #e8eef6 !important;
                            border-radius:22px !important;
                            background:#ffffff !important;
                            box-shadow:0 6px 18px rgba(15,23,42,.06) !important;
                            text-align:center !important;">

                <div class="acc-empty-icon"
                     aria-hidden="true"
                     style="display:inline-flex !important;
                            width:62px !important;
                            height:62px !important;
                            flex:0 0 62px !important;
                            margin:0 0 1rem !important;
                            align-items:center !important;
                            justify-content:center !important;
                            border:1px solid #bfdbfe !important;
                            border-radius:18px !important;
                            background:#eff6ff !important;
                            color:#1d4ed8 !important;
                            font-size:1.55rem !important;
                            line-height:1 !important;
                            box-shadow:0 10px 24px rgba(37,99,235,.11) !important;">
                    <i class="bi bi-clipboard2-pulse" aria-hidden="true"></i>
                </div>

                <h2 class="acc-empty-title"
                    style="margin:0 !important;
                           color:#0f172a !important;
                           font-size:clamp(1.12rem,2vw,1.38rem) !important;
                           font-weight:800 !important;
                           line-height:1.4 !important;
                           text-align:center !important;">
                    ยังไม่มีข้อมูลการบาดเจ็บ
                </h2>

                <p class="acc-empty-text"
                   style="width:100% !important;
                          max-width:650px !important;
                          margin:.48rem auto 0 !important;
                          color:#64748b !important;
                          font-size:.88rem !important;
                          line-height:1.75 !important;
                          text-align:center !important;">
                    เริ่มต้นบันทึกเหตุการณ์ครั้งแรก โดยระบุวันเกิดเหตุ สถานที่ รายละเอียด การรักษา และผู้ดูแลให้ครบถ้วน
                </p>

                <button type="button"
                        class="btn acc-btn acc-btn-primary"
                        style="margin-top:1.15rem !important;"
                        data-bs-toggle="modal"
                        data-bs-target="#accidentFormModal">
                    <i class="bi bi-plus-circle" aria-hidden="true"></i>
                    <span>เพิ่มข้อมูลการบาดเจ็บครั้งแรก</span>
                </button>
            </section>
        @endif
    </div>
</div>

@include('frontend.client.accident._form')
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalElement = document.getElementById('accidentFormModal');
        const form = document.getElementById('accidentForm');

        if (!modalElement || !form) {
            return;
        }

        /* ย้ายเฉพาะ Modal นี้ไปใต้ body โดยตรง เพื่อไม่ให้ถูก topbar/sidebar บัง */
        if (modalElement.parentElement !== document.body) {
            document.body.appendChild(modalElement);
        }

        const modalBody = modalElement.querySelector('.acc-modal-body');
        const medicalPanel = modalElement.querySelector('[data-medical-panel]');
        const medicalFields = modalElement.querySelectorAll('[data-medical-field]');
        const treatInputs = modalElement.querySelectorAll('input[name="treat_no"]');
        const treatWrap = modalElement.querySelector('[data-treat-wrap]');
        const treatClientError = modalElement.querySelector('[data-treat-client-error]');
        const submitButton = form.querySelector('button[type="submit"]');
        const incidentDateInput = form.querySelector('input[name="incident_date"]');
        const appointmentDateInput = form.querySelector('input[name="appointment"]');
        const recordDateInput = form.querySelector('input[name="record_date"]');
        const shouldAutoOpen = @json(
            $isEdit || ($errors->any() && old('_form_context') === 'accident_form')
        );

        function selectedTreatValue() {
            const checked = form.querySelector('input[name="treat_no"]:checked');
            return checked ? checked.value : '';
        }

        function toggleMedicalPanel() {
            const showMedical = selectedTreatValue() === 'พบแพทย์';

            if (medicalPanel) {
                medicalPanel.classList.toggle('is-hidden', !showMedical);
                medicalPanel.setAttribute('aria-hidden', showMedical ? 'false' : 'true');
            }

            medicalFields.forEach(function (field) {
                field.disabled = !showMedical;
            });
        }

        function syncRelatedDateLimits() {
            const incidentDate = incidentDateInput ? incidentDateInput.value : '';

            [appointmentDateInput, recordDateInput].forEach(function (field) {
                if (!field) {
                    return;
                }

                if (incidentDate) {
                    field.min = incidentDate;
                } else {
                    field.removeAttribute('min');
                }
            });
        }

        function validateTreatChoice(showMessage) {
            const valid = selectedTreatValue() !== '';

            if (treatWrap) {
                treatWrap.classList.toggle('acc-radio-invalid', showMessage && !valid);
            }

            if (treatClientError) {
                treatClientError.classList.toggle('d-none', !showMessage || valid);
            }

            return valid;
        }

        function clearFieldError(field) {
            field.classList.remove('is-invalid');
            field.removeAttribute('aria-invalid');

            const feedback = field.closest('.acc-field')?.querySelector('.invalid-feedback');
            if (feedback && feedback.dataset.serverError === 'true') {
                feedback.style.display = 'none';
            }
        }

        if (incidentDateInput) {
            incidentDateInput.addEventListener('input', syncRelatedDateLimits);
            incidentDateInput.addEventListener('change', syncRelatedDateLimits);
        }

        treatInputs.forEach(function (input) {
            input.addEventListener('change', function () {
                validateTreatChoice(false);
                toggleMedicalPanel();
            });
        });

        form.querySelectorAll('.form-control, .form-select').forEach(function (field) {
            field.addEventListener('input', function () {
                clearFieldError(field);
            });

            field.addEventListener('change', function () {
                clearFieldError(field);
            });
        });

        form.addEventListener('submit', function (event) {
            const treatValid = validateTreatChoice(true);
            form.classList.add('was-validated');

            if (!form.checkValidity() || !treatValid) {
                event.preventDefault();
                event.stopPropagation();

                const firstInvalid = form.querySelector(
                    '.form-control:invalid, .form-select:invalid, input[name="treat_no"]:invalid'
                );

                const focusTarget = firstInvalid || form.querySelector('input[name="treat_no"]');

                if (focusTarget) {
                    try {
                        focusTarget.focus({ preventScroll: true });
                    } catch (error) {
                        focusTarget.focus();
                    }

                    (focusTarget.closest('.acc-field, [data-treat-wrap]') || focusTarget).scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }

                return;
            }

            if (submitButton) {
                // ป้องกันการกดซ้ำ โดยคงไอคอนและข้อความเดิมของปุ่มไว้
                submitButton.disabled = true;
            }
        });

        modalElement.addEventListener('show.bs.modal', function () {
            document.body.classList.add('accident-modal-open');
            toggleMedicalPanel();
        });

        modalElement.addEventListener('shown.bs.modal', function () {
            if (modalBody) {
                modalBody.scrollTop = 0;
            }

            window.requestAnimationFrame(function () {
                const firstInvalid = form.querySelector('.is-invalid');
                const firstField = firstInvalid || form.querySelector('input[name="incident_date"]');

                if (firstField) {
                    try {
                        firstField.focus({ preventScroll: true });
                    } catch (error) {
                        firstField.focus();
                    }
                }
            });
        });

        modalElement.addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('accident-modal-open');

            if (modalBody) {
                modalBody.scrollTop = 0;
            }

            if (submitButton) {
                submitButton.disabled = false;
            }
        });

        syncRelatedDateLimits();
        toggleMedicalPanel();

        if (shouldAutoOpen && window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalElement, {
                backdrop: 'static',
                keyboard: false
            }).show();
        }

        function setupAccidentDataTable() {
            const tableElement = document.getElementById('datatable-accident');

            if (!tableElement || !window.jQuery || !jQuery.fn.DataTable) {
                return;
            }

            const $table = jQuery(tableElement);

            /*
             * Layout หรือสคริปต์ส่วนกลางอาจสร้าง DataTable ไว้ก่อน
             * ทำลาย instance เดิมแล้วสร้างใหม่เพียงครั้งเดียว เพื่อไม่ให้
             * ช่องค้นหา/จำนวนรายการซ้ำ หัวตารางเหลื่อม หรือ scrollbar ซ้อน
             */
            if (jQuery.fn.DataTable.isDataTable(tableElement)) {
                $table.DataTable().destroy();
            }

            const accidentDataTable = $table.DataTable({
                destroy: true,
                autoWidth: false,
                scrollX: true,
                scrollCollapse: true,
                order: [[0, 'desc']],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                dom: '<"acc-dt-top"<"acc-dt-length"l><"acc-dt-search"f>>rt<"acc-dt-bottom"<"acc-dt-info"i><"acc-dt-paging"p>>',
                columnDefs: [
                    {
                        orderable: false,
                        searchable: false,
                        width: '150px',
                        className: 'acc-col-actions',
                        targets: -1
                    }
                ],
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
                        first: 'หน้าแรก',
                        last: 'หน้าสุดท้าย',
                        next: 'ถัดไป',
                        previous: 'ก่อนหน้า'
                    }
                },
                initComplete: function () {
                    const api = this.api();
                    const wrapper = tableElement.closest('.dataTables_wrapper');

                    wrapper?.setAttribute('data-permission-keep', '');
                    wrapper?.querySelectorAll('input, select, button, a').forEach(function (element) {
                        element.setAttribute('data-permission-keep', '');
                    });

                    window.requestAnimationFrame(function () {
                        api.columns.adjust().draw(false);
                    });
                }
            });

            function adjustAccidentDataTable() {
                window.requestAnimationFrame(function () {
                    accidentDataTable.columns.adjust();
                });
            }

            window.setTimeout(adjustAccidentDataTable, 100);
            window.addEventListener('load', adjustAccidentDataTable, { once: true });

            let resizeTimer = null;
            window.addEventListener('resize', function () {
                window.clearTimeout(resizeTimer);
                resizeTimer = window.setTimeout(adjustAccidentDataTable, 120);
            });

            const tableCard = tableElement.closest('.acc-table-card');
            if (tableCard && window.ResizeObserver) {
                const observer = new ResizeObserver(function () {
                    adjustAccidentDataTable();
                });
                observer.observe(tableCard);
            }
        }

        /* รอ Layout และสคริปต์ส่วนกลางทำงานก่อน แล้ว normalize ตารางหนึ่งครั้ง */
        window.setTimeout(setupAccidentDataTable, 60);
    });

    function confirmDelete(id) {
        const form = document.getElementById('delete-form-' + id);

        if (!form) {
            return;
        }

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
                if (result.isConfirmed) {
                    form.submit();
                }
            });

            return;
        }

        if (window.confirm('ยืนยันการลบข้อมูลนี้ใช่หรือไม่?')) {
            form.submit();
        }
    }
</script>

@if($errors->any() && old('_form_context') === 'accident_form')
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