@extends('admin_client.admin_client')

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/assets/css/addictive.css') }}">
<style>
    .addictive-page-v2 {
        --ad-border: #e2e8f0;
        --ad-border-soft: #edf2f7;
        --ad-text: #172033;
        --ad-muted: #64748b;
        --ad-primary: #1d4ed8;
        --ad-primary-soft: #eff6ff;
        --ad-surface: #ffffff;
        --ad-surface-soft: #f8fafc;
        padding-top: .75rem !important;
        padding-bottom: 2rem !important;
    }

    .addictive-page-v2 .ad-pagebar,
    .addictive-page-v2 .ad-filter,
    .addictive-page-v2 .ad-table-card {
        border: 1px solid var(--ad-border);
        border-radius: 12px;
        background: var(--ad-surface);
        box-shadow: 0 2px 10px rgba(15, 23, 42, .035);
    }

    .addictive-page-v2 .ad-pagebar {
        min-height: 58px;
        margin-bottom: .65rem;
        padding: .65rem .8rem .65rem .95rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .addictive-page-v2 .ad-pagebar-main,
    .addictive-page-v2 .ad-page-actions,
    .addictive-page-v2 .ad-table-head-main {
        display: flex;
        align-items: center;
    }

    .addictive-page-v2 .ad-pagebar-main {
        min-width: 0;
        gap: .7rem;
    }

    .addictive-page-v2 .ad-title-icon,
    .addictive-page-v2 .ad-table-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        border: 1px solid #d8dee8;
        border-radius: 9px;
        background: #f8fafc;
        color: #475569;
        line-height: 1;
    }

    .addictive-page-v2 .ad-title-icon {
        width: 34px;
        height: 34px;
        flex-basis: 34px;
        font-size: 1rem;
    }

    .addictive-page-v2 .ad-page-title {
        margin: 0;
        color: var(--ad-text);
        font-size: 1.08rem;
        font-weight: 700;
        line-height: 1.25;
    }

    .addictive-page-v2 .ad-page-count {
        margin-top: .12rem;
        color: var(--ad-muted);
        font-size: .8rem;
        line-height: 1.2;
    }

    .addictive-page-v2 .ad-page-actions {
        justify-content: flex-end;
        gap: .45rem;
        flex-wrap: wrap;
    }

    .addictive-page-v2 .ad-btn {
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
        text-decoration: none;
        box-shadow: none !important;
    }


    .addictive-page-v2 .ad-filter {
        margin-bottom: .65rem;
        padding: .68rem .8rem;
    }

    .addictive-page-v2 .ad-filter-row {
        display: grid;
        grid-template-columns: minmax(155px, 220px) minmax(155px, 220px) minmax(0, 1fr);
        gap: .55rem;
        align-items: end;
    }

    .addictive-page-v2 .ad-filter-label {
        margin-bottom: .28rem;
        display: flex;
        align-items: center;
        gap: .34rem;
        color: #334155;
        font-size: .79rem;
        font-weight: 700;
    }

    .addictive-page-v2 .ad-filter-label i {
        color: #64748b;
        font-size: .84em;
    }

    .addictive-page-v2 .ad-filter-control {
        min-height: 38px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #fff;
        color: var(--ad-text);
        font-size: .88rem;
        font-weight: 400 !important;
        box-shadow: none !important;
    }

    .addictive-page-v2 .ad-filter-control:focus {
        border-color: #93b4f6;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .09) !important;
    }

    .addictive-page-v2 .ad-filter-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: .45rem;
        flex-wrap: wrap;
    }

    .addictive-page-v2 .ad-table-card {
        overflow: hidden;
    }

    .addictive-page-v2 .ad-table-head {
        min-height: 45px;
        padding: .55rem .8rem;
        border-bottom: 1px solid var(--ad-border-soft);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .addictive-page-v2 .ad-table-head-main {
        min-width: 0;
        gap: .55rem;
    }

    .addictive-page-v2 .ad-table-icon {
        width: 30px;
        height: 30px;
        flex-basis: 30px;
        font-size: .88rem;
    }

    .addictive-page-v2 .ad-table-title {
        margin: 0;
        color: var(--ad-text);
        font-size: .95rem;
        font-weight: 700;
        line-height: 1.25;
    }

    .addictive-page-v2 .ad-table-meta {
        color: var(--ad-muted);
        font-size: .78rem;
        font-weight: 400;
        white-space: nowrap;
    }

    .addictive-page-v2 .ad-table-body {
        padding: .72rem .8rem .8rem;
    }

    .addictive-page-v2 .ad-table-wrap {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        border: 1px solid #e7edf4;
        border-radius: 9px;
        background: #fff;
        -webkit-overflow-scrolling: touch;
    }

    .addictive-page-v2 .ad-table {
        width: 100% !important;
        min-width: 960px;
        margin: 0 !important;
        border-collapse: collapse !important;
    }

    .addictive-page-v2 .ad-table thead th {
        padding: .62rem .62rem;
        border-bottom: 1px solid #dce4ed !important;
        background: #f8fafc;
        color: #334155;
        font-size: .76rem;
        font-weight: 700;
        text-align: left;
        vertical-align: middle;
        white-space: nowrap;
    }

    .addictive-page-v2 .ad-table tbody td,
    .addictive-page-v2 .ad-table tbody td * {
        font-weight: 400 !important;
    }

    .addictive-page-v2 .ad-table tbody td {
        padding: .64rem .62rem;
        border-bottom: 1px solid #edf2f7;
        color: #334155;
        font-size: .82rem;
        line-height: 1.45;
        vertical-align: middle;
    }

    .addictive-page-v2 .ad-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .addictive-page-v2 .ad-table tbody tr:hover td {
        background: #fbfdff;
    }

    .addictive-page-v2 .ad-date {
        display: inline-flex;
        align-items: center;
        gap: .36rem;
        color: #334155;
        white-space: nowrap;
    }

    .addictive-page-v2 .ad-date i {
        color: #64748b;
        font-size: .78rem;
    }

    .addictive-page-v2 .ad-status {
        display: inline-flex;
        align-items: center;
        gap: .32rem;
        padding: .28rem .48rem;
        border: 1px solid transparent;
        border-radius: 999px;
        font-size: .73rem;
        line-height: 1.1;
        white-space: nowrap;
    }

    .addictive-page-v2 .ad-status--negative {
        border-color: #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .addictive-page-v2 .ad-status--positive {
        border-color: #fecaca;
        background: #fef2f2;
        color: #b91c1c;
    }

    .addictive-page-v2 .ad-status--refer {
        border-color: #fde68a;
        background: #fffbeb;
        color: #92400e;
    }

    .addictive-page-v2 .ad-status--follow {
        border-color: #bae6fd;
        background: #f0f9ff;
        color: #0369a1;
    }

    .addictive-page-v2 .ad-note {
        max-width: 310px;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    .addictive-page-v2 .ad-action-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .32rem;
        flex-wrap: nowrap;
    }

    .addictive-page-v2 .ad-icon-btn {
        width: 34px;
        height: 34px;
        padding: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .86rem;
        line-height: 1;
        box-shadow: none !important;
    }

    .addictive-page-v2 .ad-empty {
        min-height: 235px;
        padding: 2rem 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .addictive-page-v2 .ad-empty-icon {
        width: 52px;
        height: 52px;
        margin-bottom: .75rem;
        border: 1px solid #d8e3ef;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        color: #64748b;
        font-size: 1.15rem;
    }

    .addictive-page-v2 .ad-empty-title {
        margin: 0;
        color: var(--ad-text);
        font-size: .98rem;
        font-weight: 700;
    }

    .addictive-page-v2 .ad-empty-text {
        margin: .35rem 0 .8rem;
        color: var(--ad-muted);
        font-size: .82rem;
        line-height: 1.5;
    }

    .addictive-page-v2 .ad-dt-top,
    .addictive-page-v2 .ad-dt-bottom {
        padding: .55rem .2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        flex-wrap: wrap;
    }

    .addictive-page-v2 .ad-dt-top {
        padding-top: 0;
    }

    .addictive-page-v2 .ad-dt-bottom {
        padding-bottom: 0;
    }

    .addictive-page-v2 .dataTables_length,
    .addictive-page-v2 .dataTables_filter,
    .addictive-page-v2 .dataTables_info,
    .addictive-page-v2 .dataTables_paginate {
        margin: 0 !important;
        color: #64748b;
        font-size: .78rem;
    }

    .addictive-page-v2 .dataTables_length select,
    .addictive-page-v2 .dataTables_filter input {
        min-height: 34px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #fff;
        box-shadow: none !important;
    }

    .addictive-page-v2 .dataTables_filter input {
        width: 190px;
        margin-left: .35rem;
    }

    .addictive-page-v2 .dataTables_paginate .paginate_button {
        min-width: 32px;
        min-height: 32px;
        margin: 0 2px !important;
        padding: .35rem .55rem !important;
        border: 1px solid #dbe3ec !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: #475569 !important;
        box-shadow: none !important;
    }

    .addictive-page-v2 .dataTables_paginate .paginate_button.current {
        border-color: #93b4f6 !important;
        background: #eff6ff !important;
        color: #1d4ed8 !important;
    }

    /* คงค่าที่เลือกใน Radio ให้เห็นชัดเมื่อ Modal เป็นโหมดอ่านอย่างเดียว */
    #editAddictiveModal[data-addictive-readonly="1"] input[type="radio"]:disabled {
        opacity: 1 !important;
        cursor: default;
    }

    #editAddictiveModal[data-addictive-readonly="1"] .radio-card,
    #editAddictiveModal[data-addictive-readonly="1"] .refer-option-card {
        cursor: default;
        opacity: 1 !important;
    }

    #editAddictiveModal[data-addictive-readonly="1"] .radio-card:has(input:checked),
    #editAddictiveModal[data-addictive-readonly="1"] .refer-option-card:has(input:checked) {
        border-color: #60a5fa;
        background: #eff6ff;
        box-shadow: 0 0 0 .16rem rgba(37, 99, 235, .08);
    }

    @media (max-width: 991.98px) {
        .addictive-page-v2 .ad-filter-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .addictive-page-v2 .ad-filter-actions {
            grid-column: 1 / -1;
            justify-content: flex-start;
        }
    }

    @media (max-width: 767.98px) {
        .addictive-page-v2 .ad-pagebar {
            align-items: stretch;
            flex-direction: column;
        }

        .addictive-page-v2 .ad-page-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .addictive-page-v2 .ad-page-actions .ad-btn {
            flex: 1 1 auto;
        }

        .addictive-page-v2 .ad-filter-row {
            grid-template-columns: 1fr;
        }

        .addictive-page-v2 .ad-filter-actions {
            grid-column: auto;
        }

        .addictive-page-v2 .ad-table-body {
            padding: .6rem;
        }

        .addictive-page-v2 .ad-dt-top,
        .addictive-page-v2 .ad-dt-bottom {
            align-items: stretch;
            flex-direction: column;
        }

        .addictive-page-v2 .dataTables_filter,
        .addictive-page-v2 .dataTables_filter input,
        .addictive-page-v2 .dataTables_length,
        .addictive-page-v2 .dataTables_paginate {
            width: 100%;
        }

        .addictive-page-v2 .dataTables_filter input {
            margin-top: .3rem;
            margin-left: 0;
        }
    }

    @media (max-width: 575.98px) {
        .addictive-page-v2 .ad-page-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }


        .addictive-page-v2 .ad-filter-actions {
            display: grid;
            grid-template-columns: 1fr;
            width: 100%;
        }

        .addictive-page-v2 .ad-filter-actions .ad-btn {
            width: 100%;
        }
    }


    /* =========================================================
       Polished table + DataTable controls (รูปแบบเดียวกับ Psychiatric)
    ========================================================= */
    .addictive-page-v2 .ad-table-card {
        border-radius: 22px;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06),
                    0 2px 8px rgba(15, 23, 42, 0.03);
    }

    .addictive-page-v2 .ad-table-head {
        min-height: 68px;
        padding: 1rem 1.15rem;
        border-bottom: 1px solid #e2e8f0;
        background:
            radial-gradient(circle at top right, rgba(59, 130, 246, 0.10), transparent 26%),
            linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%);
    }

    .addictive-page-v2 .ad-table-icon {
        width: 46px;
        height: 46px;
        flex-basis: 46px;
        border-color: #bfdbfe;
        border-radius: 14px;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1d4ed8;
    }

    .addictive-page-v2 .ad-table-title {
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
    }

    .addictive-page-v2 .ad-table-meta {
        font-size: 0.82rem;
    }

    .addictive-page-v2 .ad-table-body {
        padding: 0;
    }

    .addictive-page-v2 .ad-table-wrap {
        overflow-x: auto;
        border: 0;
        border-radius: 0;
        scrollbar-width: thin;
    }

    .addictive-page-v2 .ad-table-wrap::-webkit-scrollbar {
        height: 8px;
    }

    .addictive-page-v2 .ad-table-wrap::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: #cbd5e1;
    }

    .addictive-page-v2 .dataTables_wrapper {
        width: 100%;
        padding: 0;
    }

    .addictive-page-v2 .ad-dt-top,
    .addictive-page-v2 .ad-dt-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin: 0;
        padding: 0.95rem 1.15rem 0.65rem;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .addictive-page-v2 .ad-dt-top {
        border-bottom: 1px solid #eef2f7;
    }

    .addictive-page-v2 .ad-dt-bottom {
        padding-top: 0.85rem;
        padding-bottom: 1rem;
        border-top: 1px solid #eef2f7;
        background: #ffffff;
    }

    .addictive-page-v2 .ad-dt-length,
    .addictive-page-v2 .ad-dt-search,
    .addictive-page-v2 .ad-dt-info,
    .addictive-page-v2 .ad-dt-page {
        min-width: 0;
    }

    .addictive-page-v2 .dataTables_length,
    .addictive-page-v2 .dataTables_filter,
    .addictive-page-v2 .dataTables_info,
    .addictive-page-v2 .dataTables_paginate {
        margin: 0 !important;
        padding: 0 !important;
        color: #64748b;
        font-size: 0.86rem;
    }

    .addictive-page-v2 .dataTables_length label,
    .addictive-page-v2 .dataTables_filter label {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        margin: 0 !important;
        color: #475569;
        font-size: 0.88rem;
        font-weight: 600;
        line-height: 1.45;
        white-space: nowrap;
    }

    .addictive-page-v2 .dataTables_filter label {
        justify-content: flex-end;
    }

    .addictive-page-v2 .dataTables_length select,
    .addictive-page-v2 .dataTables_filter input {
        height: 40px;
        border: 1px solid #dbe3ee;
        border-radius: 12px;
        background: #ffffff;
        color: #0f172a;
        font-size: 0.9rem;
        box-shadow: none !important;
    }

    .addictive-page-v2 .dataTables_length select {
        min-width: 72px;
        margin: 0 !important;
        padding: 0.38rem 2rem 0.38rem 0.75rem;
    }

    .addictive-page-v2 .dataTables_filter input {
        width: 190px;
        min-width: 190px;
        margin: 0 0 0 0.4rem !important;
        padding: 0.45rem 0.85rem;
    }

    .addictive-page-v2 .dataTables_length select:focus,
    .addictive-page-v2 .dataTables_filter input:focus {
        border-color: #93c5fd;
        outline: none;
        box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.14) !important;
    }

    .addictive-page-v2 .dataTables_paginate {
        margin-left: auto !important;
        text-align: right;
    }

    .addictive-page-v2 .dataTables_paginate .paginate_button {
        min-width: 34px;
        min-height: 34px;
        margin: 0 2px !important;
        padding: 0.38rem 0.58rem !important;
        border: 1px solid #dbe3ec !important;
        border-radius: 10px !important;
        background: #ffffff !important;
        color: #475569 !important;
    }

    .addictive-page-v2 .dataTables_paginate .paginate_button:hover {
        border-color: #bfdbfe !important;
        background: #eff6ff !important;
        color: #1d4ed8 !important;
    }

    .addictive-page-v2 .dataTables_paginate .paginate_button.current,
    .addictive-page-v2 .dataTables_paginate .paginate_button.current:hover {
        border-color: #93c5fd !important;
        background: #dbeafe !important;
        color: #1d4ed8 !important;
    }

    .addictive-page-v2 .ad-table thead th {
        padding: 0.75rem 0.68rem;
        background: #f8fafc;
        color: #334155;
        font-size: 0.78rem;
        font-weight: 800;
    }

    .addictive-page-v2 .ad-table tbody td {
        padding: 0.72rem 0.68rem;
        font-size: 0.84rem;
    }

    @media (min-width: 1400px) {
        .addictive-page-v2 .ad-table {
            min-width: 100%;
            table-layout: fixed;
        }
    }

    @media (max-width: 767.98px) {
        .addictive-page-v2 .ad-dt-top,
        .addictive-page-v2 .ad-dt-bottom {
            align-items: stretch;
            flex-direction: column;
            padding-right: 0.8rem;
            padding-left: 0.8rem;
        }

        .addictive-page-v2 .ad-dt-length,
        .addictive-page-v2 .ad-dt-search,
        .addictive-page-v2 .ad-dt-info,
        .addictive-page-v2 .ad-dt-page,
        .addictive-page-v2 .dataTables_length,
        .addictive-page-v2 .dataTables_filter,
        .addictive-page-v2 .dataTables_info,
        .addictive-page-v2 .dataTables_paginate {
            width: 100%;
            text-align: left !important;
        }

        .addictive-page-v2 .dataTables_length label,
        .addictive-page-v2 .dataTables_filter label {
            width: 100%;
            justify-content: flex-start;
        }

        .addictive-page-v2 .dataTables_filter input {
            width: 100%;
            min-width: 0;
            margin-left: 0 !important;
        }

        .addictive-page-v2 .dataTables_paginate {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 3px;
            margin-left: 0 !important;
        }
    }

</style>
@endpush

@section('content')
@php
    $addictiveAddHasErrors = $errors->any() && old('_form_context') === 'addictive_add';
    $addictiveEditHasErrors = $errors->any() && old('_form_context') === 'addictive_edit';


    /*
     * สถานะตัวกรองรายงาน ใช้ร่วมกันระหว่าง Header และแผงค้นหา
     * Query string ที่มี date_from/date_to เป็นค่าว่างจะไม่ถือว่าเปิดตัวกรอง
     */
    $hasAddictiveRows = isset($addictives) && $addictives->isNotEmpty();

    $hasActiveAddictiveFilter = request()->filled('date_from')
        || request()->filled('date_to')
        || filled(old('date_from'))
        || filled(old('date_to'));

    $addictiveFilterErrorBag = $errors->getBag('filters');
    $hasAddictiveFilterErrors = $addictiveFilterErrorBag->has('date_from')
        || $addictiveFilterErrorBag->has('date_to')
        || (blank(old('_form_context')) && (
            $errors->has('date_from') || $errors->has('date_to')
        ));

    $showAddictiveFilter = $hasActiveAddictiveFilter || $hasAddictiveFilterErrors;
    $canShowAddictiveFilter = $hasAddictiveRows
        || $hasActiveAddictiveFilter
        || $hasAddictiveFilterErrors;

    $showAddictiveFirstEmptyState = !$hasAddictiveRows
        && !$hasActiveAddictiveFilter
        && !$hasAddictiveFilterErrors;

    $addictiveEditOldValues = [
        'id' => old('_edit_id'),
        'date' => old('date'),
        'count' => old('count'),
        'exam' => old('exam'),
        'refer' => old('refer'),
        'record' => old('record'),
        'recorder' => old('recorder'),
    ];

    $permissionUser = auth()->user();
    $fallbackWriteRoles = ['admin', 'executive', 'social_worker'];

    $canAddictiveCreate = $permissionUser && method_exists($permissionUser, 'canCreateForm')
        ? $permissionUser->canCreateForm('health_addictive')
        : in_array($permissionUser?->role, $fallbackWriteRoles, true);

    $canAddictiveUpdate = $permissionUser && method_exists($permissionUser, 'canUpdateForm')
        ? $permissionUser->canUpdateForm('health_addictive')
        : in_array($permissionUser?->role, $fallbackWriteRoles, true);

    $canAddictiveDelete = $permissionUser && method_exists($permissionUser, 'canDeleteForm')
        ? $permissionUser->canDeleteForm('health_addictive')
        : $permissionUser?->role === 'admin';

    $canAddictivePrint = $permissionUser && method_exists($permissionUser, 'canPrintForm')
        ? $permissionUser->canPrintForm('health_addictive')
        : true;

    $isAddictiveReadOnly = !$canAddictiveCreate && !$canAddictiveUpdate && !$canAddictiveDelete;
    $hasAddictiveData = $hasAddictiveRows;

    $addictiveReadonlyRecords = $addictives->mapWithKeys(function ($item) {
        return [$item->id => [
            'id' => $item->id,
            'date' => filled($item->date) ? \Carbon\Carbon::parse($item->date)->format('Y-m-d') : '',
            'count' => $item->count ?? '',
            'exam' => (string) ($item->exam ?? ''),
            'refer' => filled($item->refer) ? (string) $item->refer : '',
            'record' => $item->record ?? '',
            'recorder' => $item->recorder ?? '',
        ]];
    });

    $addictiveFlash = [
        'message' => session('message'),
        'type' => session('alert-type', 'success'),
    ];
@endphp

<div class="container-fluid addictive-page-v2">
    @include('frontend.client.addictive.partials.header')
    @include('frontend.client.addictive.partials._client_info')
    @include('frontend.client.addictive.partials._table')
</div>

@include('frontend.client.addictive.partials._create_modal')
@include('frontend.client.addictive.partials._edit_modal')
@endsection

@push('scripts')
<script>
    window.addictiveConfig = {
        jsonUrl: @json(url('/addictive/json')),
        updateBaseUrl: @json(url('/addictive/update')),
        addHasErrors: @json($addictiveAddHasErrors),
        editHasErrors: @json($addictiveEditHasErrors),
        editOldValues: @json($addictiveEditOldValues),
        canUpdate: @json($canAddictiveUpdate),
        readonlyRecords: @json($addictiveReadonlyRecords),
        flash: @json($addictiveFlash)
    };
</script>
@include('frontend.client.addictive.partials._script_init')
@endpush
