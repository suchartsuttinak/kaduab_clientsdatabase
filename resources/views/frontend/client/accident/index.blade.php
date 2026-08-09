@extends('admin_client.admin_client')

{{-- ACCIDENT_DATATABLE_CHECKBODY_V5_FINAL --}}
@section('content')
@php
    $isEdit = isset($accident) && $accident;
    $hasAccidentRows = isset($accidents) && $accidents->isNotEmpty();
    $totalAccidents = $hasAccidentRows ? $accidents->count() : 0;
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
        max-width: 1500px;
        margin: 0 auto;
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
        overflow: hidden;
        border: 1px solid var(--acc-border-soft);
        border-radius: 14px;
        background: #ffffff;
    }

    /*
    | ใช้ scrollX ของ DataTables เพียงจุดเดียว
    | ป้องกัน scrollbar ซ้อนและหัวตารางไม่ตรงกับข้อมูล
    */
    .accident-page .acc-table-wrap .dataTables_wrapper,
    .accident-page .acc-table-wrap .dataTables_scroll {
        width: 100%;
        min-width: 0;
    }

    .accident-page .acc-table-wrap .dataTables_scrollHead {
        overflow: hidden !important;
        background: #f8fafc;
    }

    .accident-page .acc-table-wrap .dataTables_scrollBody {
        overflow-x: auto !important;
        overflow-y: visible !important;
        /* ไม่จองพื้นที่ scrollbar โดยไม่จำเป็น เพราะทำให้เกิดส่วนเกินเล็กน้อย */
        scrollbar-gutter: auto;
        -webkit-overflow-scrolling: touch;
    }

    .accident-page .acc-table {
        width: 100% !important;
        margin: 0 !important;
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

    /*
    | คอลัมน์จัดการเลื่อนไปพร้อมกับตาราง
    | ไม่ใช้ sticky เพื่อป้องกันช่องว่าง/คอลัมน์ซ้อนบนจอขนาดใหญ่
    */
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
        z-index: auto;
        background: #f8fafc;
    }

    .accident-page .acc-table tbody .acc-col-actions {
        z-index: auto;
        background: #ffffff;
    }

    .accident-page .acc-table tbody tr:hover .acc-col-actions {
        background: #fbfdff;
    }

    /*
    | DataTables บางครั้งปัดเศษความกว้างเกิน 1-12px บนจอใหญ่
    | JavaScript จะเพิ่มคลาสนี้เฉพาะเมื่อเป็นส่วนเกินเล็กน้อยจริง
    */
    .accident-page .dataTables_wrapper.acc-no-trivial-overflow .dataTables_scrollBody {
        overflow-x: hidden !important;
    }

    .accident-page .dataTables_wrapper.acc-no-trivial-overflow .dataTables_scrollHeadInner {
        width: 100% !important;
        padding-right: 0 !important;
    }

    .accident-page .dataTables_wrapper.acc-no-trivial-overflow .dataTables_scrollHeadInner > table,
    .accident-page .dataTables_wrapper.acc-no-trivial-overflow .dataTables_scrollBody > table {
        width: 100% !important;
        min-width: 100% !important;
    }

    .accident-page .dataTables_wrapper.acc-no-trivial-overflow .acc-table th,
    .accident-page .dataTables_wrapper.acc-no-trivial-overflow .acc-table td {
        min-width: 0 !important;
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
        padding: .78rem .75rem;
        border-bottom: 1px solid var(--acc-border);
        background: #f8fafc;
        color: #334155;
        font-size: .78rem;
        font-weight: 800;
        vertical-align: middle;
        white-space: nowrap;
    }

    .accident-page .acc-table tbody td {
        padding: .78rem .75rem;
        border-color: #edf1f6;
        color: #334155;
        font-size: .83rem;
        line-height: 1.55;
        vertical-align: middle;
    }

    .accident-page .acc-table tbody tr:hover {
        background: #fbfdff;
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

    /* จัดปุ่มในคอลัมน์ “จัดการ” ให้อยู่กึ่งกลางเสมอ
       แม้ระบบสิทธิ์อ่านอย่างเดียวจะเหลือปุ่มดูเพียงปุ่มเดียว */
    .accident-page .acc-table tbody .acc-col-actions {
        text-align: center !important;
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

    .accident-page .acc-empty-state {
        padding: 3.2rem 1.25rem;
        border-radius: var(--acc-radius-xl);
        text-align: center;
        background:
            radial-gradient(circle at top, rgba(59, 130, 246, .10), transparent 44%),
            #ffffff;
    }

    .accident-page .acc-empty-icon {
        display: inline-flex;
        width: 82px;
        height: 82px;
        margin-bottom: 1rem;
        align-items: center;
        justify-content: center;
        border: 1px solid #bfdbfe;
        border-radius: 50%;
        background: var(--acc-primary-soft);
        color: var(--acc-primary);
        font-size: 2rem;
    }

    .accident-page .acc-empty-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.15rem;
        font-weight: 800;
    }

    .accident-page .acc-empty-text {
        max-width: 650px;
        margin: .45rem auto 1.15rem;
        color: var(--acc-muted);
        font-size: .88rem;
        line-height: 1.75;
    }

    .accident-page .dataTables_wrapper .dataTables_length,
    .accident-page .dataTables_wrapper .dataTables_filter {
        margin-bottom: .8rem;
        color: #475569;
        font-size: .82rem;
    }

    .accident-page .dataTables_wrapper .dataTables_length select,
    .accident-page .dataTables_wrapper .dataTables_filter input {
        min-height: 38px;
        border: 1px solid var(--acc-border);
        border-radius: 10px;
        background: #ffffff;
        box-shadow: none;
    }

    .accident-page .dataTables_wrapper .dataTables_info,
    .accident-page .dataTables_wrapper .dataTables_paginate {
        margin-top: .8rem;
        color: var(--acc-muted);
        font-size: .8rem;
    }

    .accident-page .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
    }



    /* ACCIDENT_DATATABLE_CHECKBODY_V5_FINAL
       DataTable layout ใช้โครงสร้างเดียวกับหน้า check_body */
    .accident-page .acc-table-wrap {
        width: 100%;
        min-width: 0;
        overflow: visible;
        border: 0;
        border-radius: 0;
        background: transparent;
    }

    .accident-page .acc-table-wrap .dataTables_wrapper {
        width: 100%;
        min-width: 0;
    }

    .accident-page .acc-dt-toolbar,
    .accident-page .acc-dt-footer {
        display: flex;
        width: 100%;
        align-items: center;
        justify-content: space-between;
        gap: .75rem 1rem;
        flex-wrap: wrap;
    }

    .accident-page .acc-dt-toolbar { margin-bottom: .8rem; }
    .accident-page .acc-dt-footer { margin-top: .85rem; }

    .accident-page .acc-dt-length,
    .accident-page .acc-dt-search,
    .accident-page .acc-dt-info,
    .accident-page .acc-dt-paging { min-width: 0; }

    .accident-page .acc-dt-search { margin-left: auto; }

    .accident-page .acc-dt-scroll {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        border: 1px solid var(--acc-border-soft);
        border-radius: 14px;
        background: #fff;
        scrollbar-gutter: stable;
        -webkit-overflow-scrolling: touch;
    }

    .accident-page .acc-table {
        width: 100% !important;
        margin: 0 !important;
        table-layout: fixed;
        border-collapse: collapse !important;
    }

    .accident-page .acc-table.acc-table-compact { min-width: 1150px; }
    .accident-page .acc-table.acc-table-expanded { min-width: 1450px; }

    .accident-page .acc-col-date { width: 120px !important; min-width: 120px !important; }
    .accident-page .acc-col-location { width: 155px !important; min-width: 155px !important; }
    .accident-page .acc-col-detail { width: 280px !important; min-width: 280px !important; }
    .accident-page .acc-col-cause { width: 160px !important; min-width: 160px !important; }
    .accident-page .acc-col-treatment { width: 145px !important; min-width: 145px !important; }
    .accident-page .acc-col-hospital { width: 165px !important; min-width: 165px !important; }
    .accident-page .acc-col-appointment { width: 135px !important; min-width: 135px !important; }
    .accident-page .acc-col-caretaker { width: 140px !important; min-width: 140px !important; }

    .accident-page .acc-col-actions {
        position: static !important;
        right: auto !important;
        width: 150px !important;
        min-width: 150px !important;
        max-width: 150px !important;
        border-left: 1px solid var(--acc-border-soft) !important;
        box-shadow: none !important;
    }

    .accident-page .acc-table thead .acc-col-actions { background: #f8fafc; }
    .accident-page .acc-table tbody .acc-col-actions { background: #fff; }
    .accident-page .acc-table tbody tr:hover .acc-col-actions { background: #fbfdff; }

    .accident-page .dataTables_wrapper .dataTables_length,
    .accident-page .dataTables_wrapper .dataTables_filter,
    .accident-page .dataTables_wrapper .dataTables_info,
    .accident-page .dataTables_wrapper .dataTables_paginate {
        float: none !important;
        margin: 0 !important;
        color: #475569;
        font-size: .82rem;
        text-align: left !important;
    }

    .accident-page .dataTables_wrapper .dataTables_length label,
    .accident-page .dataTables_wrapper .dataTables_filter label {
        display: inline-flex;
        align-items: center;
        gap: .42rem;
        margin: 0;
        color: #475569;
        font-weight: 650;
        white-space: nowrap;
    }

    .accident-page .dataTables_wrapper .dataTables_length select,
    .accident-page .dataTables_wrapper .dataTables_filter input {
        min-height: 38px;
        margin: 0 !important;
        border: 1px solid var(--acc-border);
        border-radius: 10px;
        background: #fff;
        color: #334155;
        box-shadow: none;
        outline: none;
    }

    .accident-page .dataTables_wrapper .dataTables_length select {
        min-width: 76px;
        padding: .35rem 1.9rem .35rem .65rem;
    }

    .accident-page .dataTables_wrapper .dataTables_filter input {
        width: 230px !important;
        max-width: 42vw;
        padding: .45rem .7rem;
    }

    .accident-page .dataTables_wrapper .dataTables_length select:focus,
    .accident-page .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--acc-primary);
        box-shadow: 0 0 0 .18rem rgba(29, 78, 216, .10);
    }

    .accident-page .dataTables_wrapper .dataTables_info {
        padding-top: 0 !important;
        color: var(--acc-muted);
        line-height: 1.5;
    }

    .accident-page .dataTables_wrapper .dataTables_paginate {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .2rem;
    }

    .accident-page .dataTables_wrapper .dataTables_paginate .paginate_button {
        min-width: 36px;
        min-height: 36px;
        margin: 0 1px !important;
        padding: .42rem .62rem !important;
        border: 1px solid transparent !important;
        border-radius: 9px !important;
        background: transparent !important;
        color: #475569 !important;
        box-shadow: none !important;
    }

    .accident-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        border-color: #cbd5e1 !important;
        background: #f8fafc !important;
        color: #0f172a !important;
    }

    .accident-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .accident-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        border-color: var(--acc-primary) !important;
        background: var(--acc-primary) !important;
        color: #fff !important;
    }

    .accident-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .accident-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        border-color: transparent !important;
        background: transparent !important;
        color: #94a3b8 !important;
        cursor: default !important;
    }

    @media (max-width: 767.98px) {
        .accident-page .acc-dt-toolbar,
        .accident-page .acc-dt-footer {
            align-items: stretch;
            flex-direction: column;
        }

        .accident-page .acc-dt-length,
        .accident-page .acc-dt-search,
        .accident-page .acc-dt-info,
        .accident-page .acc-dt-paging {
            width: 100%;
            margin-left: 0;
        }

        .accident-page .dataTables_wrapper .dataTables_length label,
        .accident-page .dataTables_wrapper .dataTables_filter label {
            width: 100%;
        }

        .accident-page .dataTables_wrapper .dataTables_filter input {
            width: min(100%, 280px) !important;
            max-width: none;
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


    /* =========================================================
       HEADER สำหรับกรณียังไม่มีข้อมูล
       ยึดโครงสร้างเดียวกับหน้า Check Body
    ========================================================= */
    .accident-page .acc-empty-header {
        position: relative;
        overflow: hidden;
        min-height: 142px;
        margin-bottom: 1.75rem;
        padding: 1.45rem 1.5rem;
        border: 1px solid #bfdbfe;
        border-radius: 18px;
        background:
            linear-gradient(135deg, #eff6ff 0%, #f8fbff 58%, #ffffff 100%);
        box-shadow: 0 10px 28px rgba(37, 99, 235, .08);
    }

    .accident-page .acc-empty-header::after {
        content: "";
        position: absolute;
        right: -58px;
        top: -72px;
        width: 190px;
        height: 190px;
        border: 26px solid rgba(37, 99, 235, .045);
        border-radius: 50%;
        pointer-events: none;
    }

    .accident-page .acc-empty-header-inner {
        position: relative;
        z-index: 1;
        min-height: 92px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .accident-page .acc-empty-header-left {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 1rem;
    }

    .accident-page .acc-empty-header-icon {
        display: inline-flex;
        width: 60px;
        height: 60px;
        flex: 0 0 60px;
        align-items: center;
        justify-content: center;
        border: 1px solid #bfdbfe;
        border-radius: 18px;
        background: linear-gradient(145deg, #dbeafe, #eff6ff);
        color: var(--acc-primary);
        font-size: 1.45rem;
        box-shadow: 0 8px 18px rgba(37, 99, 235, .12);
    }

    .accident-page .acc-empty-header-text {
        min-width: 0;
    }

    .accident-page .acc-empty-header-title {
        margin: 0;
        color: #172554;
        font-size: 1.18rem;
        font-weight: 800;
        line-height: 1.35;
        letter-spacing: -.01em;
    }

    .accident-page .acc-empty-header-client {
        margin-top: .28rem;
        color: var(--acc-muted);
        font-size: .84rem;
        line-height: 1.5;
    }

    .accident-page .acc-empty-header-client strong {
        color: #0f172a;
        font-weight: 800;
    }

    .accident-page .acc-empty-back-btn {
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

    .accident-page .acc-empty-back-btn:hover,
    .accident-page .acc-empty-back-btn:focus {
        color: #6d28d9;
        background: #faf5ff;
        transform: translateY(-1px);
        box-shadow: 0 8px 16px rgba(124, 58, 237, .12);
    }

    .accident-page .acc-empty-state {
        min-height: 320px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
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

        .accident-page .acc-empty-header {
            min-height: 168px;
            margin-bottom: 1rem;
            padding: 1.35rem 1rem !important;
            border-radius: 16px;
        }

        .accident-page .acc-empty-header-inner {
            min-height: 124px;
            align-content: center;
            row-gap: 1rem;
        }

        .accident-page .acc-empty-header-inner,
        .accident-page .acc-empty-header-left {
            width: 100%;
        }

        .accident-page .acc-empty-back-btn {
            width: 100%;
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

        .accident-page .acc-empty-header {
            min-height: 176px;
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }

        .accident-page .acc-empty-header-left {
            align-items: center;
            gap: .8rem;
        }

        .accident-page .acc-empty-header-icon {
            width: 52px;
            height: 52px;
            flex-basis: 52px;
            border-radius: 15px;
            font-size: 1.25rem;
        }

        .accident-page .acc-empty-header-title {
            font-size: 1.02rem;
        }

        .accident-page .acc-empty-header-client {
            font-size: .78rem;
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
</style>

<div class="container-fluid px-2 px-lg-3 accident-page">
    <div class="acc-shell">
        @if($hasAccidentRows)
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

                            @if($hasAccidentRows)
                                <span class="acc-meta-chip">
                                    <i class="bi bi-journal-medical"></i>
                                    <span>จำนวนบันทึก:</span>
                                    <strong>{{ number_format($totalAccidents) }} รายการ</strong>
                                </span>
                            @endif
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
        @else
            <section class="acc-empty-header" aria-labelledby="accidentPageTitle">
                <div class="acc-empty-header-inner">
                    <div class="acc-empty-header-left">
                        <div class="acc-empty-header-icon" aria-hidden="true">
                            <i class="bi bi-shield-plus"></i>
                        </div>

                        <div class="acc-empty-header-text">
                            <h1 class="acc-empty-header-title" id="accidentPageTitle">
                                บันทึกข้อมูลการบาดเจ็บ
                            </h1>

                            <div class="acc-empty-header-client">
                                ผู้รับบริการ:
                                <strong>{{ $clientDisplayName }}</strong>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('admin.index', $client->id) }}"
                       class="acc-empty-back-btn"
                       aria-label="กลับหน้าหลักผู้รับบริการ">
                        <i class="bi bi-arrow-left-circle"></i>
                        <span>กลับ</span>
                    </a>
                </div>
            </section>
        @endif

        @if($hasAccidentRows)
            <section class="acc-stats" aria-label="สรุปข้อมูลการบาดเจ็บ">
                <article class="acc-stat">
                    <div class="acc-stat-top">
                        <span class="acc-stat-label">รายการล่าสุด</span>
                        <span class="acc-stat-icon"><i class="bi bi-calendar-event"></i></span>
                    </div>
                    <div class="acc-stat-value">
                        {{ \App\Helpers\ThaiDateHelper::formatThaiShort(optional($accidents->first())->incident_date) }}
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
            <section class="acc-empty-state">
                <div class="acc-empty-icon" aria-hidden="true">
                    <i class="bi bi-clipboard2-pulse"></i>
                </div>
                <h2 class="acc-empty-title">ยังไม่มีข้อมูลการบาดเจ็บ</h2>
                <p class="acc-empty-text">
                    เริ่มต้นบันทึกเหตุการณ์ครั้งแรก โดยระบุวันเกิดเหตุ สถานที่ รายละเอียด การรักษา และผู้ดูแลให้ครบถ้วน
                </p>
                <button type="button"
                        class="btn acc-btn acc-btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#accidentFormModal">
                    <i class="bi bi-plus-circle"></i>
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

        function setupAccidentDataTable() {
            const tableElement = document.getElementById('datatable-accident');

            if (!tableElement || !window.jQuery || !jQuery.fn.DataTable) {
                return;
            }

            const $table = jQuery(tableElement);

            /* เหมือนหน้า Check Body: ทำลายตัวที่ Layout สร้างก่อน แล้วสร้างของหน้านี้ใหม่ */
            if (jQuery.fn.DataTable.isDataTable(tableElement)) {
                $table.DataTable().destroy();
            }

            tableElement.removeAttribute('style');

            const dataTable = $table.DataTable({
                destroy: true,
                autoWidth: false,
                responsive: false,
                scrollX: false,
                order: [[0, 'desc']],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                dom: '<"acc-dt-toolbar"<"acc-dt-length"l><"acc-dt-search"f>><"acc-dt-scroll"t><"acc-dt-footer"<"acc-dt-info"i><"acc-dt-paging"p>>',
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

                    wrapper?.setAttribute('data-accident-datatable', 'checkbody-v5');
                    wrapper?.querySelectorAll('.acc-dt-toolbar, .acc-dt-footer').forEach(function (element) {
                        element.setAttribute('data-permission-keep', '');
                    });

                    wrapper?.querySelectorAll(
                        '.acc-dt-toolbar input, .acc-dt-toolbar select, .acc-dt-toolbar button, .acc-dt-toolbar a, ' +
                        '.acc-dt-footer input, .acc-dt-footer select, .acc-dt-footer button, .acc-dt-footer a'
                    ).forEach(function (element) {
                        element.setAttribute('data-permission-keep', '');
                    });

                    window.requestAnimationFrame(function () {
                        api.columns.adjust();
                    });
                }
            });

            let resizeTimer = null;
            window.addEventListener('resize', function () {
                window.clearTimeout(resizeTimer);
                resizeTimer = window.setTimeout(function () {
                    dataTable.columns.adjust();
                }, 140);
            }, { passive: true });
        }

        /* ใช้ timing เดียวกับหน้า Check Body */
        window.setTimeout(setupAccidentDataTable, 80);

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
