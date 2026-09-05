@extends('admin.admin_master')

@section('admin')

    <!-- ทำงานที่หน้า StatisticsController -->


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

   <style>
    .dashboard-page {
        padding: 1.25rem 0 1.5rem;
    }

    /* =========================
       HERO
    ========================= */
    .dashboard-hero {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        padding: 1.5rem 1.6rem;
        margin-bottom: 1.25rem;
        background:
            radial-gradient(circle at top right, rgba(255, 255, 255, .22), transparent 28%),
            linear-gradient(135deg, #0f4c81 0%, #1368aa 48%, #1e88e5 100%);
        color: #fff;
        box-shadow: 0 18px 40px rgba(15, 76, 129, .18);
    }

    .dashboard-hero::after {
        content: "";
        position: absolute;
        right: -60px;
        top: -60px;
        width: 220px;
        height: 220px;
        background: rgba(255, 255, 255, .08);
        border-radius: 50%;
    }

    .dashboard-hero::before {
        content: "";
        position: absolute;
        left: -40px;
        bottom: -60px;
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, .06);
        border-radius: 50%;
    }

    .dashboard-hero-content,
    .dashboard-hero-action-col {
        position: relative;
        z-index: 2;
    }

    .dashboard-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        background: rgba(255, 255, 255, .14);
        border: 1px solid rgba(255, 255, 255, .18);
        border-radius: 999px;
        padding: .45rem .8rem;
        font-size: .9rem;
        font-weight: 600;
        margin-bottom: .8rem;
        white-space: nowrap;
    }

    .dashboard-hero-title {
        margin: 0;
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.02em;
        color: #ffffff !important;
        text-shadow: 0 2px 12px rgba(0, 0, 0, .15);
    }

    .dashboard-hero-subtitle {
        margin: .55rem 0 0;
        color: rgba(255, 255, 255, .88);
        max-width: 780px;
        font-size: .98rem;
        line-height: 1.65;
    }

    .dashboard-hero-action-col {
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .dashboard-hero-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: .85rem;
        flex-wrap: nowrap;
        width: 100%;
    }

    .dashboard-btn-pill {
        width: 255px;
        min-height: 56px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .65rem;
        border-radius: 999px;
        padding: 0 1rem;
        font-size: .92rem;
        font-weight: 800;
        line-height: 1.2;
        white-space: nowrap;
        box-shadow: 0 10px 20px rgba(0, 0, 0, .12);
        border: 1px solid rgba(255, 255, 255, .25);
        transition: all .18s ease;
        flex: 0 0 255px;
    }

    .dashboard-btn-pill i,
    .dashboard-btn-pill svg {
        width: 23px;
        height: 23px;
        font-size: 1.25rem;
        flex: 0 0 23px;
    }

    .dashboard-btn-pill span {
        display: inline-block;
        line-height: 1.2;
    }

    .dashboard-btn-report {
        background: linear-gradient(135deg, #0f766e, #14b8a6);
        color: #ffffff !important;
    }

    .dashboard-btn-report:hover {
        background: linear-gradient(135deg, #0f5f59, #0d9488);
        color: #ffffff !important;
        transform: translateY(-1px);
    }

    .dashboard-btn-pill.btn-light {
        color: #111827 !important;
        background: #ffffff;
        border-color: rgba(255, 255, 255, .9);
    }

    .dashboard-btn-pill.btn-light:hover {
        background: #f8fafc;
        color: #111827 !important;
        transform: translateY(-1px);
    }

    /* =========================
       CARD / SECTION
    ========================= */
    .dashboard-card {
        border: 1px solid #e9eef5;
        border-radius: 20px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .05);
        overflow: hidden;
        background: #fff;
    }

    .dashboard-card .card-header {
        border-bottom: 1px solid #edf1f5;
        background: #fff;
        padding: 1rem 1.15rem;
    }

    .dashboard-card .card-body {
        padding: 1.1rem 1.15rem;
    }

    .section-title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #172b4d;
    }

    .section-subtitle {
        margin: .15rem 0 0;
        color: #6b7280;
        font-size: .88rem;
    }

    .alert-appointment-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .alert-chip {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        padding: .35rem .7rem;
        font-size: .84rem;
        font-weight: 600;
    }

    /* =========================
       TABLE
    ========================= */
    .appointment-table {
        margin-bottom: 0;
    }

    .appointment-table thead th {
        background: #f8fafc;
        color: #334155;
        font-weight: 700;
        border-bottom-color: #e5e7eb;
        white-space: nowrap;
    }

    .appointment-table tbody td {
        vertical-align: middle;
    }

    .appointment-date {
        color: #b91c1c;
        font-weight: 700;
    }

    .table-card .table {
        margin-bottom: 0;
    }

    .table-card .table thead th {
        background: #f8fafc;
        color: #334155;
        font-weight: 700;
        border-bottom-color: #e5e7eb;
        white-space: nowrap;
    }

    .table-card .table tbody td {
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-student-name {
        font-weight: 600;
        color: #111827;
        min-width: 180px;
    }

    .table-student-name a {
        transition: all .2s ease;
        text-decoration: none !important;
    }

    .table-student-name a:hover {
        color: #0d6efd !important;
        text-decoration: none !important;
    }

    .table-muted {
        color: #6b7280;
    }

    .dashboard-empty {
        padding: 1rem;
        text-align: center;
        color: #6b7280;
    }

    .dashboard-table-scroll {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        border-radius: 14px;
        position: relative;
    }

    .dashboard-table-scroll table {
        min-width: 860px;
    }

    .dashboard-table-scroll .dataTables_wrapper {
        width: 100%;
    }

    .dashboard-table-scroll .dataTables_scroll {
        width: 100%;
    }

    .dashboard-table-scroll .dataTables_scrollHead {
        overflow: hidden !important;
        border-top-left-radius: 14px;
        border-top-right-radius: 14px;
    }

    .dashboard-table-scroll .dataTables_scrollBody {
        overflow-x: auto !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch;
        border-bottom-left-radius: 14px;
        border-bottom-right-radius: 14px;
    }

    .dashboard-table-scroll .dataTables_scrollBody table {
        min-width: 860px !important;
        width: 100% !important;
    }

    .dashboard-table-scroll::-webkit-scrollbar,
    .dashboard-table-scroll .dataTables_scrollBody::-webkit-scrollbar {
        height: 10px;
        width: 10px;
    }

    .dashboard-table-scroll::-webkit-scrollbar-thumb,
    .dashboard-table-scroll .dataTables_scrollBody::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, .75);
        border-radius: 999px;
    }

    .dashboard-table-scroll::-webkit-scrollbar-track,
    .dashboard-table-scroll .dataTables_scrollBody::-webkit-scrollbar-track {
        background: rgba(241, 245, 249, .95);
        border-radius: 999px;
    }

    .dashboard-table-fade {
        position: relative;
    }

    .dashboard-table-fade::before,
    .dashboard-table-fade::after {
        content: "";
        position: absolute;
        top: 0;
        width: 24px;
        height: 100%;
        pointer-events: none;
        z-index: 3;
    }

    .dashboard-table-fade::before {
        left: 0;
        background: linear-gradient(to right, rgba(255, 255, 255, .95), rgba(255, 255, 255, 0));
    }

    .dashboard-table-fade::after {
        right: 0;
        background: linear-gradient(to left, rgba(255, 255, 255, .95), rgba(255, 255, 255, 0));
    }

    #clientsTable_wrapper .dataTables_length,
    #clientsTable_wrapper .dataTables_filter {
        margin-bottom: .85rem;
    }

    #clientsTable_wrapper .dataTables_length select,
    #clientsTable_wrapper .dataTables_filter input {
        border-radius: 10px;
        border: 1px solid #dbe3ec;
        min-height: 38px;
        padding: .35rem .65rem;
        box-shadow: none !important;
        background: #fff;
    }

    #clientsTable_wrapper .dataTables_info,
    #clientsTable_wrapper .dataTables_paginate,
    #clientsTable_wrapper .dataTables_filter,
    #clientsTable_wrapper .dataTables_length {
        font-size: .88rem;
        color: #475569;
        margin-top: .55rem;
    }

    #clientsTable_wrapper .dataTables_paginate .paginate_button {
        border-radius: 10px !important;
    }

    /* =========================
       MINI STAT
    ========================= */
    .mini-stat-card {
        height: 100%;
        border: 1px solid #e8edf3;
        border-radius: 20px;
        padding: 1rem;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 8px 22px rgba(15, 23, 42, .04);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .mini-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(15, 23, 42, .08);
    }

    .mini-stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        margin-bottom: .85rem;
    }

    .mini-stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #fff;
        flex: 0 0 52px;
    }

    .mini-stat-icon.absent {
        background: linear-gradient(135deg, #ef4444, #f97316);
    }

    .mini-stat-icon.accident {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
    }

    .mini-stat-icon.escape {
        background: linear-gradient(135deg, #2563eb, #38bdf8);
    }

    .mini-stat-label {
        margin: 0;
        color: #111827;
        font-size: .98rem;
        font-weight: 700;
    }

    .mini-stat-date {
        margin: .15rem 0 0;
        color: #6b7280;
        font-size: .82rem;
    }

    .mini-stat-number {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: .7rem;
    }

    .mini-stat-number.absent {
        color: #dc2626;
    }

    .mini-stat-number.accident {
        color: #d97706;
    }

    .mini-stat-number.escape {
        color: #1d4ed8;
    }

    .name-list {
        display: flex;
        flex-wrap: wrap;
        gap: .45rem;
    }

    .name-pill {
        display: inline-flex;
        align-items: center;
        padding: .32rem .65rem;
        border-radius: 999px;
        background: #f3f4f6;
        color: #374151;
        font-size: .84rem;
        font-weight: 500;
    }

    /* =========================
       FILTER
    ========================= */
    .filter-card {
        border: 1px solid #e8edf3;
        border-radius: 22px;
        background: linear-gradient(180deg, #ffffff 0%, #fcfdff 100%);
        box-shadow: 0 12px 26px rgba(15, 23, 42, .05);
        margin-top: 1.25rem;
        margin-bottom: 1.25rem;
    }

    .filter-card .card-body {
        padding: 1.2rem;
    }

    .filter-title {
        font-size: 1.02rem;
        font-weight: 700;
        color: #172b4d;
        margin-bottom: .2rem;
    }

    .filter-subtitle {
        color: #6b7280;
        font-size: .88rem;
        margin-bottom: 1rem;
    }

    .filter-section-label {
        font-size: .92rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: .55rem;
    }

    .filter-card .form-label {
        font-weight: 600;
        color: #334155;
        margin-bottom: .4rem;
    }

    .filter-card .form-control,
    .filter-card .form-select {
        min-height: 44px;
        border-radius: 12px;
        border-color: #dbe3ec;
        box-shadow: none !important;
    }

    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        border-color: #60a5fa;
    }

    .status-radio-group {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem 1.5rem;
        padding: .25rem 0;
    }

    .status-radio-group .form-check {
        margin: 0;
    }

    .status-radio-group .form-check-input {
        box-shadow: none;
    }

    .status-radio-group .form-check-label {
        font-weight: 500;
        color: #334155;
    }

    .filter-divider {
        border-top: 1px dashed #dbe3ec;
        margin: 1rem 0 1.1rem;
    }

    .filter-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        align-items: center;
    }

    .filter-submit-btn {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .8rem 1.15rem;
        border-radius: 14px;
        font-weight: 700;
    }

    /* =========================
       METRIC / CHART
    ========================= */
    .metric-card {
        position: relative;
        overflow: hidden;
        border-radius: 22px;
        padding: 1.15rem 1rem;
        color: #fff;
        box-shadow: 0 16px 30px rgba(15, 23, 42, .08);
        height: 100%;
    }

    .metric-card::after {
        content: "";
        position: absolute;
        top: -30px;
        right: -30px;
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, .12);
        border-radius: 50%;
    }

    .metric-card .metric-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        background: rgba(255, 255, 255, .18);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .metric-card .metric-label {
        margin: 0 0 .3rem;
        font-size: .95rem;
        font-weight: 600;
        opacity: .95;
    }

    .metric-card .metric-value {
        margin: 0;
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
    }

    .metric-card.metric-total {
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
    }

    .metric-card.metric-male {
        background: linear-gradient(135deg, #16a34a, #4ade80);
    }

    .metric-card.metric-female {
        background: linear-gradient(135deg, #ec4899, #f472b6);
    }

    .chart-card .card-header,
    .table-card .card-header {
        padding: 1rem 1.15rem;
    }

    .chart-card .card-body,
    .table-card .card-body {
        padding: 1.15rem;
    }

    .widget-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #dbeafe;
        flex: 0 0 42px;
    }

    .chart-title-wrap {
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .chart-panel {
        min-height: 360px;
    }

    /* =========================
       กิจกรรมล่าสุดของเคส
    ========================= */
    .case-feed-card {
        border: 1px solid #e8edf3;
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
        overflow: hidden;
        margin-bottom: 1.25rem;
    }

    .case-feed-header {
        padding: 1rem 1.15rem;
        border-bottom: 1px solid #edf1f5;
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .case-feed-title-wrap {
        display: flex;
        gap: .75rem;
        align-items: center;
    }

    .case-feed-icon {
        width: 44px;
        height: 44px;
        border-radius: 15px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex: 0 0 44px;
    }

    .case-feed-title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        color: #172b4d;
    }

    .case-feed-subtitle {
        margin: .12rem 0 0;
        font-size: .86rem;
        color: #64748b;
    }

    .case-feed-chip {
        border-radius: 999px;
        background: #f1f5f9;
        color: #334155;
        padding: .38rem .75rem;
        font-size: .82rem;
        font-weight: 700;
    }

    .case-feed-body {
        padding: .9rem 1.15rem 1.05rem;
    }

    .case-feed-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 14px;
    }

    .case-feed-item {
        position: relative;
        display: grid;
        grid-template-columns: 46px minmax(0, 1fr);
        gap: .85rem;
        padding: 1rem;
        border: 1px solid #e9eef5;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 8px 22px rgba(15, 23, 42, .04);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        text-decoration: none !important;
        color: inherit !important;
    }

    .case-feed-item::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        border-radius: 18px 18px 0 0;
        background: linear-gradient(90deg, #2563eb, #38bdf8);
    }

    .case-feed-item:hover {
        transform: translateY(-2px);
        border-color: #cfe0ff;
        box-shadow: 0 16px 30px rgba(37, 99, 235, .08);
        color: inherit !important;
    }

    .case-feed-avatar {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        background: linear-gradient(135deg, #2563eb 0%, #38bdf8 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        box-shadow: 0 10px 20px rgba(37, 99, 235, .18);
    }

    .case-feed-name {
        font-size: .96rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: .15rem;
        line-height: 1.25;
    }

    .case-feed-desc {
        font-size: .88rem;
        color: #475569;
        line-height: 1.35;
        word-break: break-word;
    }

    .case-feed-meta {
        margin-top: .45rem;
        display: flex;
        flex-wrap: wrap;
        gap: .45rem .7rem;
        font-size: .8rem;
        color: #64748b;
    }

    .case-feed-meta span {
        display: inline-flex;
        align-items: center;
        min-width: 0;
    }

    .case-feed-empty {
        text-align: center;
        color: #64748b;
        padding: 1.15rem;
        background: #f8fafc;
        border: 1px dashed #dbe3ec;
        border-radius: 16px;
    }

    /* =========================
       RESPONSIVE
    ========================= */
    @media (min-width: 1400px) {
        .dashboard-btn-pill {
            width: 270px;
            flex-basis: 270px;
            min-height: 58px;
            font-size: .96rem;
        }
    }

    @media (max-width: 1399.98px) and (min-width: 1200px) {
        .dashboard-hero-title {
            font-size: 1.85rem;
        }

        .dashboard-hero-subtitle {
            font-size: .94rem;
        }

        .dashboard-hero-actions {
            gap: .65rem;
        }

        .dashboard-btn-pill {
            width: 245px;
            flex-basis: 245px;
            min-height: 54px;
            font-size: .86rem;
            padding: 0 .75rem;
            gap: .5rem;
        }

        .dashboard-btn-pill i,
        .dashboard-btn-pill svg {
            width: 21px;
            height: 21px;
            font-size: 1.12rem;
            flex-basis: 21px;
        }
    }

    @media (max-width: 1199.98px) and (min-width: 992px) {
        .dashboard-hero {
            padding: 1.35rem 1.2rem;
        }

        .dashboard-hero-title {
            font-size: 1.6rem;
        }

        .dashboard-hero-subtitle {
            font-size: .88rem;
            line-height: 1.55;
        }

        .dashboard-hero-badge {
            font-size: .78rem;
            padding: .38rem .65rem;
        }

        .dashboard-hero-actions {
            gap: .5rem;
        }

        .dashboard-btn-pill {
            width: 220px;
            flex-basis: 220px;
            min-height: 52px;
            font-size: .78rem;
            padding: 0 .55rem;
            gap: .45rem;
        }

        .dashboard-btn-pill i,
        .dashboard-btn-pill svg {
            width: 19px;
            height: 19px;
            font-size: 1.05rem;
            flex-basis: 19px;
        }
    }

    @media (max-width: 991.98px) {
        .dashboard-hero {
            padding: 1.25rem;
        }

        .dashboard-hero-title {
            font-size: 1.6rem;
        }

        .dashboard-hero-action-col {
            justify-content: flex-start;
        }

        .dashboard-hero-actions {
            justify-content: flex-start;
            flex-wrap: wrap;
            margin-top: .5rem;
        }

        .dashboard-btn-pill {
            width: 245px;
            flex-basis: 245px;
        }

        .case-feed-list {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .dashboard-page {
            padding-top: .85rem;
        }

        .dashboard-hero {
            border-radius: 20px;
            padding: 1rem;
        }

        .dashboard-hero-title {
            font-size: 1.35rem;
        }

        .dashboard-hero-subtitle {
            font-size: .92rem;
        }

        .dashboard-hero-badge {
            white-space: normal;
        }

        .dashboard-hero-actions {
            gap: .7rem;
        }

        .dashboard-btn-pill {
            width: 100%;
            flex-basis: 100%;
            min-height: 52px;
            justify-content: center;
            font-size: .92rem;
        }

        .case-feed-header,
        .case-feed-body {
            padding: 1rem;
        }

        .case-feed-list {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .case-feed-item {
            grid-template-columns: 40px minmax(0, 1fr);
            padding: .9rem;
            border-radius: 16px;
        }

        .case-feed-item::before {
            border-radius: 16px 16px 0 0;
        }

        .case-feed-avatar {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            font-size: 1rem;
        }

        .case-feed-meta {
            gap: .38rem .6rem;
            font-size: .78rem;
        }

        .filter-card .card-body,
        .dashboard-card .card-body,
        .chart-card .card-body,
        .table-card .card-body {
            padding: 1rem;
        }

        .status-radio-group {
            gap: .85rem 1rem;
        }

        .metric-card .metric-value {
            font-size: 1.7rem;
        }

        .chart-panel {
            min-height: 300px;
        }

        .dashboard-table-scroll {
            margin: 0 -2px;
            padding-bottom: .15rem;
        }

        .dashboard-table-scroll table,
        .dashboard-table-scroll .dataTables_scrollBody table {
            min-width: 820px !important;
        }

        #clientsTable_wrapper .dataTables_length,
        #clientsTable_wrapper .dataTables_filter {
            width: 100%;
            text-align: left;
        }

        #clientsTable_wrapper .dataTables_filter input {
            width: 100%;
            margin-left: 0 !important;
        }
    }
    .behavior-referral-alert {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 18px 20px;
        border: 1px solid rgba(255,255,255,.32);
        border-radius: 18px;
        color: #fff;
        background: linear-gradient(135deg, #9a3412, #ea580c 62%, #f59e0b);
        box-shadow: 0 14px 30px rgba(194, 65, 12, .18);
    }

    .behavior-referral-alert-icon {
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        background: rgba(255,255,255,.18);
        font-size: 1.35rem;
    }

    .behavior-referral-alert-content { flex: 1; min-width: 0; }
    .behavior-referral-alert-kicker { display:block; color:rgba(255,255,255,.78); font-size:.78rem; font-weight:700; }
    .behavior-referral-alert h2 { margin:2px 0 7px; color:#fff; font-size:1.15rem; font-weight:800; }
    .behavior-referral-alert-meta { display:flex; gap:7px; flex-wrap:wrap; }
    .behavior-referral-alert-meta span { padding:4px 8px; border-radius:999px; background:rgba(255,255,255,.13); color:#fff; font-size:.73rem; font-weight:700; }
    .behavior-referral-alert-meta span.urgent { background:#fff; color:#b42318; }
    .behavior-referral-alert-action { flex:0 0 auto; border:0; border-radius:11px; color:#9a3412; font-weight:800; }

    @media (max-width: 767.98px) {
        .behavior-referral-alert { align-items:flex-start; flex-wrap:wrap; padding:16px; }
        .behavior-referral-alert-action { width:100%; }
    }
</style>

    <div class="content">
        <div class="container-fluid dashboard-page">

            @php
                $thaiDate = \Carbon\Carbon::parse($today)->locale('th');
                $day = $thaiDate->translatedFormat('j');
                $month = $thaiDate->translatedFormat('F');
                $year = $thaiDate->year + 543;
            @endphp
<div class="dashboard-hero">
    <div class="row align-items-center g-3">
        <div class="col-12 col-lg-6">
            <div class="dashboard-hero-content">
                <div class="dashboard-hero-badge">
                    <i class="bi bi-shield-check"></i>
                    <span>Social Welfare Intelligence Dashboard</span>
                </div>

                <h1 class="dashboard-hero-title">
                    ระบบฐานข้อมูลเด็กและสวัสดิการสังคม
                </h1>

                <p class="dashboard-hero-subtitle">
                    ศูนย์กลางสำหรับติดตามสถานะผู้รับบริการ การนัดหมายทางการแพทย์ สถิติการศึกษา
                    และข้อมูลเชิงวิเคราะห์เพื่อการดูแลอย่างเป็นระบบ
                </p>
            </div>
        </div>

        <div class="col-12 col-lg-6 dashboard-hero-action-col">
            <div class="dashboard-hero-actions">
                @if((\App\Support\FormPermissionMenu::forUser(auth()->user())['has_any_client_form'] ?? false))
                    <a href="{{ route('client.show') }}" class="btn btn-light dashboard-btn-pill">
                        <i data-feather="arrow-right-circle"></i>
                        <span>แสดงรายชื่อผู้รับบริการ</span>
                    </a>
                @endif

                @if(auth()->user()?->canViewForm('dashboard_child_analytics'))
                    <a href="{{ route('child.analytics.report.index') }}"
                       class="btn dashboard-btn-pill dashboard-btn-report">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>รายงานวิเคราะห์เด็ก</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@php
    $canViewBehaviorReferralCenter = \Illuminate\Support\Facades\Route::has('observe.referrals.index')
        && \App\Support\BehaviorReferralCenter::canAccess(auth()->user());
    $behaviorReferralSummary = $canViewBehaviorReferralCenter
        ? \App\Support\BehaviorReferralCenter::summary()
        : ['actionable' => 0, 'waiting' => 0, 'assigned' => 0, 'overdue' => 0, 'high_risk' => 0];
@endphp

@if($canViewBehaviorReferralCenter && ($behaviorReferralSummary['actionable'] ?? 0) > 0)
    <div class="behavior-referral-alert mb-4">
        <div class="behavior-referral-alert-icon"><i class="bi bi-inbox-fill"></i></div>
        <div class="behavior-referral-alert-content">
            <span class="behavior-referral-alert-kicker">มีเคสที่ต้องดำเนินการ</span>
            <h2>{{ number_format($behaviorReferralSummary['actionable']) }} เคสพฤติกรรมที่ส่งต่อ</h2>
            <div class="behavior-referral-alert-meta">
                <span>รอมอบหมาย {{ number_format($behaviorReferralSummary['waiting']) }}</span>
                <span>รอรับเคส {{ number_format($behaviorReferralSummary['assigned']) }}</span>
                @if($behaviorReferralSummary['high_risk'] > 0)<span class="urgent">ความเสี่ยงสูง {{ number_format($behaviorReferralSummary['high_risk']) }}</span>@endif
                @if($behaviorReferralSummary['overdue'] > 0)<span class="urgent">เลยนัด {{ number_format($behaviorReferralSummary['overdue']) }}</span>@endif
            </div>
        </div>
        <a href="{{ route('observe.referrals.index') }}" class="btn btn-light behavior-referral-alert-action">เปิดศูนย์รับเคส <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
@endif

           <div class="row g-4 mb-4">
                {{-- แจ้งเตือนเรื่องช่วยเหลือ --}}
                @if (
                    auth()->user()?->canViewForm('dashboard_issues') &&
                    isset($pendingIssues) &&
                    $pendingIssues->count() > 0
                )
                <div class="col-lg-3">
                    <div class="card dashboard-card h-100">
                        <div class="card-header">
                            <div class="alert-appointment-header">
                                <div>
                                    <h5 class="section-title mb-1">
                                        🚨 แจ้งเรื่องช่วยเหลือ
                                    </h5>

                                    <p class="section-subtitle mb-0">
                                        รายการที่ยังไม่ได้เปิดดู
                                    </p>
                                </div>

                                <div class="alert-chip"
                                    style="background:#fff1f2;color:#be123c;">
                                    <i class="bi bi-bell-fill"></i>
                                    <span>{{ $pendingIssues->count() }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="card-body d-flex align-items-end">
                            <a href="{{ route('issues.index') }}"
                            class="btn btn-danger w-100">
                                <i class="bi bi-eye"></i>
                                เปิดดูรายการ
                            </a>
                        </div>
                    </div>
                </div>
                @endif


                {{-- แจ้งเตือนผู้สนับสนุนทุน --}}
                @if (
                    auth()->check() &&
                    auth()->user()->canViewForm('dashboard_scholarship_sponsors') &&
                    isset($pendingScholarships) &&
                    $pendingScholarships->count() > 0
                )
                <div class="col-lg-3">
                    <div class="card dashboard-card h-100">

                        <div class="card-header">
                            <div class="alert-appointment-header">

                                <div>
                                    <h5 class="section-title mb-1">
                                        🎓 ผู้สนใจสนับสนุนทุน
                                    </h5>

                                    <p class="section-subtitle mb-0">
                                        รายการที่ยังไม่ได้เปิดดู
                                    </p>
                                </div>

                                <div class="alert-chip"
                                    style="background:#ecfdf5;color:#047857;">
                                    <i class="bi bi-mortarboard-fill"></i>
                                    <span>{{ $pendingScholarships->count() }}</span>
                                </div>

                            </div>
                        </div>

                        <div class="card-body d-flex align-items-end">
                            <a href="{{ route('scholarship.index') }}"
                            class="btn btn-success w-100">
                                <i class="bi bi-eye"></i>
                                เปิดดูรายการ
                            </a>
                        </div>

                    </div>
                </div>
                @endif
            </div>
                    


        <!-- กิจกรรมผู้รับบริการล่าสุด -->
        <div class="case-feed-card">
            <div class="case-feed-header">
                <div class="case-feed-title-wrap">
                    <div class="case-feed-icon">
                        <i class="bi bi-activity"></i>
                    </div>

                    <div>
                        <h5 class="case-feed-title">
                            กิจกรรมผู้รับบริการล่าสุด
                        </h5>

                        <p class="case-feed-subtitle">
                            ติดตามความเคลื่อนไหวและการดำเนินงานล่าสุดของผู้รับบริการในระบบ
                        </p>
                    </div>
                </div>

                <div class="case-feed-chip">
                    {{ isset($latestCaseActivities) ? $latestCaseActivities->count() : 0 }} รายการล่าสุด
                </div>
            </div>

        <div class="case-feed-body">
            @php
                $safeActivities = collect($latestCaseActivities ?? [])->filter(function ($activity) {
                    return $activity
                        && $activity->client
                        && ($activity->client->release_status ?? null) === 'show';
                });
            @endphp

            @if($safeActivities->isNotEmpty())
                <div class="case-feed-list">
                    @foreach($safeActivities as $activity)
                        @php
                            $client = $activity->client;

                            $activityDate = $activity->occurred_at
                                ? \Carbon\Carbon::parse($activity->occurred_at)->timezone('Asia/Bangkok')
                                : null;

                            $cardUrl = route('admin.index', $client->id);
                        @endphp

                        <a href="{{ $cardUrl }}" class="case-feed-item" title="ดูข้อมูลผู้รับบริการ">
                            <div class="case-feed-avatar">
                                <i class="bi bi-person-lines-fill"></i>
                            </div>

                            <div class="case-feed-content">
                                <div class="case-feed-name">
                                    {{ $client->fullname ?? '-' }}
                                </div>

                                <div class="case-feed-desc">
                                    {{ $activity->title ?: ($activity->type ?: 'บันทึกกิจกรรมผู้รับบริการ') }}
                                </div>

                                <div class="case-feed-meta">
                                    <span>
                                        <i class="bi bi-person-circle me-1"></i>
                                        {{ $activity->user->name ?? 'ระบบ' }}
                                    </span>

                                    <span title="{{ $activityDate ? $activityDate->format('d/m/Y H:i') : '-' }}">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $activityDate ? $activityDate->locale('th')->diffForHumans() : '-' }}
                                    </span>

                                    <span class="case-feed-status active">
                                        <i class="bi bi-person-check me-1"></i>
                                        อยู่ในความดูแล
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="case-feed-empty">
                    ยังไม่มีกิจกรรมผู้รับบริการล่าสุด
                </div>
            @endif
        </div>
     </div>

      
            @if (auth()->user()?->canUpdateForm('welfare_discharge') &&
                    isset($pendingReferApprovals) &&
                    $pendingReferApprovals->count() > 0)
                <div class="card dashboard-card mb-4">
                    <div class="card-header">
                        <div class="alert-appointment-header">
                            <div>
                                <h5 class="section-title mb-1">แจ้งเตือนการจำหน่ายรออนุมัติ</h5>
                                <p class="section-subtitle mb-0">รายการเหล่านี้ยังไม่ออกจากระบบ จนกว่าจะได้รับการอนุมัติ</p>
                            </div>
                            <div class="alert-chip" style="background:#fff7ed;color:#c2410c;">
                                <i class="bi bi-bell-fill"></i>
                                <span>{{ $pendingReferApprovals->count() }} รายการ</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="dashboard-table-scroll dashboard-table-fade">
                            <table class="table appointment-table align-middle">
                                <thead>
                                    <tr>
                                        <th class="text-center">ชื่อ - สกุล</th>
                                        <th class="text-center">วันที่นำส่ง</th>
                                        <th class="text-center">สาเหตุ</th>
                                        <th class="text-center">สถานที่นำส่ง</th>
                                        <th class="text-center">ดำเนินการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendingReferApprovals as $item)
                                        <tr class="table-warning">
                                            <td>{{ $item->client->fullname ?? ($item->client->name ?? '-') }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->refer_date)->format('d/m/Y') }}
                                            </td>
                                            <td class="text-center">{{ $item->translate->translate_name ?? '-' }}</td>
                                            <td>{{ $item->destination ?? '-' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('refers.index', $item->client_id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                    <span>เปิดหน้าอนุมัติ</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card dashboard-card mb-4">
                <div class="card-header">
                    <div class="alert-appointment-header">
                        <div>
                            <h5 class="section-title mb-1">การแจ้งเตือนการพบแพทย์</h5>
                            <p class="section-subtitle mb-0">แสดงนัดหมายล่วงหน้า 5 วัน
                                เพื่อเตรียมการดูแลและติดตามอย่างต่อเนื่อง</p>
                        </div>
                        <div class="alert-chip">
                            <i class="bi bi-calendar2-check"></i>
                            <span>{{ $appointmentCount }} รายการ</span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if ($appointmentCount > 0)
                        <div class="dashboard-table-scroll dashboard-table-fade">
                            <table class="table appointment-table align-middle">
                                <thead>
                                    <tr>
                                        <th class="text-center">ชื่อ - สกุล</th>
                                        <th class="text-center">อายุ</th>
                                        <th class="text-center">ประเภทการนัด</th>
                                        <th class="text-center">วันที่นัด</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($appointments as $record)
                                        @php
                                            $dateObj = \Carbon\Carbon::parse($record['date']);
                                            $daysDiff = $dateObj->diffInDays(\Carbon\Carbon::today());

                                            if ($daysDiff === 0) {
                                                $rowClass = 'table-danger fw-bold';
                                            } elseif ($daysDiff === 1) {
                                                $rowClass = 'table-warning fw-semibold';
                                            } else {
                                                $rowClass = 'table-success';
                                            }
                                        @endphp
                                        <tr class="{{ $rowClass }}">
                                            <td>{{ $record['fullname'] }}</td>
                                            <td class="text-center">{{ $record['age'] }} ปี</td>
                                            <td class="text-center">{{ $record['type'] }}</td>
                                            <td class="text-center appointment-date
                                                {{ $dateObj->isBetween(now(), now()->copy()->addDays(3)) ? 'text-danger fw-bold' : '' }}">
                                                
                                                {{ $dateObj->locale('th')->translatedFormat('d F') }}
                                                {{ $dateObj->year + 543 }}

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="dashboard-empty">
                            ไม่มีนัดหมายใน 5 วันถัดไป
                        </div>
                    @endif
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-lg-4">
                    <div class="mini-stat-card">
                        <div class="mini-stat-top">
                            <div>
                                <h6 class="mini-stat-label">การขาดเรียน</h6>
                                <p class="mini-stat-date">วันที่ {{ $day }} {{ $month }}
                                    {{ $year }}</p>
                            </div>
                            <div class="mini-stat-icon absent">
                                <i class="bi bi-journal-x"></i>
                            </div>
                        </div>

                        <div class="mini-stat-number absent">{{ $absentCount }} คน</div>

                        @if ($absentCount > 0)
                            <div class="name-list">
                                @foreach ($absentNames as $name)
                                    <span class="name-pill">{{ $name }}</span>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted small">ไม่มีรายการในวันนี้</div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="mini-stat-card">
                        <div class="mini-stat-top">
                            <div>
                                <h6 class="mini-stat-label">การเจ็บป่วย</h6>
                                <p class="mini-stat-date">วันที่ {{ $day }} {{ $month }}
                                    {{ $year }}</p>
                            </div>
                            <div class="mini-stat-icon accident">
                                <i class="bi bi-heart-pulse"></i>
                            </div>
                        </div>

                        <div class="mini-stat-number accident">{{ $accidentCount }} คน</div>

                        @if ($accidentCount > 0)
                            <div class="name-list">
                                @foreach ($accidentNames as $name)
                                    <span class="name-pill">{{ $name }}</span>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted small">ไม่มีรายการในวันนี้</div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="mini-stat-card">
                        <div class="mini-stat-top">
                            <div>
                                <h6 class="mini-stat-label">การออกโดยไม่ได้รับอนุญาต</h6>
                                <p class="mini-stat-date">วันที่ {{ $day }} {{ $month }}
                                    {{ $year }}</p>
                            </div>
                            <div class="mini-stat-icon escape">
                                <i class="bi bi-door-open"></i>
                            </div>
                        </div>

                        <div class="mini-stat-number escape">{{ $escapeCount }} คน</div>

                        @if ($escapeCount > 0)
                            <div class="name-list">
                                @foreach ($escapeNames as $name)
                                    <span class="name-pill">{{ $name }}</span>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted small">ไม่มีรายการในวันนี้</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card filter-card">
                <div class="card-body">
                    <div class="filter-title">แผงตัวกรองข้อมูลเชิงสถิติ</div>
                    <div class="filter-subtitle">เลือกช่วงข้อมูลที่ต้องการ เพื่อประมวลผลผลลัพธ์ให้ตรงตามวัตถุประสงค์</div>

                    <form method="GET" action="{{ route('statistics.index') }}">
                        <div class="filter-section-label">สถานะผู้รับบริการ</div>

                        <div class="status-radio-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="release_status" id="statusAll"
                                    value="all" {{ ($releaseStatus ?? '') == 'all' ? 'checked' : '' }}>
                                <label class="form-check-label" for="statusAll">ทั้งหมด</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="release_status" id="statusShow"
                                    value="show" {{ ($releaseStatus ?? '') == 'show' ? 'checked' : '' }}>
                                <label class="form-check-label" for="statusShow">อยู่อาศัย</label>
                            </div>

                            {{-- =========================
                             PATCH: เพิ่มสถานะรออนุมัติจำหน่าย
                        ========================= --}}
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="release_status"
                                    id="statusPendingRefer" value="pending_refer"
                                    {{ ($releaseStatus ?? '') == 'pending_refer' ? 'checked' : '' }}>
                                <label class="form-check-label" for="statusPendingRefer">รออนุมัติจำหน่าย</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="release_status" id="statusRefer"
                                    value="refer" {{ ($releaseStatus ?? '') == 'refer' ? 'checked' : '' }}>
                                <label class="form-check-label" for="statusRefer">ถูกจำหน่าย</label>
                            </div>
                        </div>

                        <div class="filter-divider"></div>

                       <div class="row g-3">
                
                         {{-- บ้าน --}}
                                <div class="col-12 col-md-3 col-lg-3">
                                    <label class="form-label">บ้าน</label>

                                    <select name="house_id" class="form-select">
                                        <option value="">ทั้งหมด</option>

                                        @foreach ($houses as $house)
                                            <option value="{{ $house->id }}"
                                                {{ (string)($houseId ?? '') === (string)$house->id ? 'selected' : '' }}>
                                                {{ $house->house_name ?? '-' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- เพศ --}}
                                <div class="col-12 col-md-3 col-lg-3">
                                    <label class="form-label">เพศ</label>

                                    <select name="gender" class="form-select">
                                        <option value="">ทั้งหมด</option>

                                        <option value="male" {{ ($gender ?? '') == 'male' ? 'selected' : '' }}>
                                            ชาย
                                        </option>

                                        <option value="female" {{ ($gender ?? '') == 'female' ? 'selected' : '' }}>
                                            หญิง
                                        </option>
                                    </select>
                                </div>

                                {{-- อายุต่ำสุด --}}
                                <div class="col-6 col-md-3 col-lg-3">
                                    <label class="form-label">อายุต่ำสุด</label>

                                    <input type="number"
                                        name="age_min"
                                        class="form-control"
                                        value="{{ $ageMin ?? 0 }}"
                                        min="0"
                                        max="99">
                                </div>

                                {{-- อายุสูงสุด --}}
                                <div class="col-6 col-md-3 col-lg-3">
                                    <label class="form-label">อายุสูงสุด</label>

                                    <input type="number"
                                        name="age_max"
                                        class="form-control"
                                        value="{{ $ageMax ?? 99 }}"
                                        min="0"
                                        max="99">
                                </div>

                      
                           {{-- สถานศึกษา --}}
                            <div class="col-12 col-md-6 col-lg-3">
                                <label class="form-label">สถานศึกษา</label>

                                <select name="institution_id" class="form-select">
                                    <option value="">ทั้งหมด</option>

                                    @foreach (\App\Models\Institution::all() as $inst)
                                        <option value="{{ $inst->id }}"
                                            {{ ($institution_id ?? '') == $inst->id ? 'selected' : '' }}>

                                            {{ $inst->institution_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- ระดับการศึกษาเริ่มต้น --}}
                            <div class="col-12 col-md-6 col-lg-3">
                                <label class="form-label">
                                    ระดับการศึกษา
                                </label>

                                <select name="education_start" class="form-select">
                                    <option value="">ทั้งหมด</option>

                                    @foreach ($educations as $edu)
                                        <option value="{{ $edu->id }}"
                                            {{ ($educationStart ?? '') == $edu->id ? 'selected' : '' }}>

                                            {{ $edu->education_name }}
                                        </option>
                                    @endforeach
                                </select>

                                <small class="text-muted d-block mt-1">
                                    เลือกเพียงช่องนี้ = ค้นหาระดับเดียว
                                </small>
                            </div>

                            {{-- ระดับการศึกษาสิ้นสุด --}}
                            <div class="col-12 col-md-6 col-lg-3">
                                <label class="form-label">
                                    ถึงระดับการศึกษา
                                </label>

                                <select name="education_end" class="form-select">
                                    <option value="">ไม่กำหนด</option>

                                    @foreach ($educations as $edu)
                                        <option value="{{ $edu->id }}"
                                            {{ ($educationEnd ?? '') == $edu->id ? 'selected' : '' }}>

                                            {{ $edu->education_name }}
                                        </option>
                                    @endforeach
                                </select>

                                <small class="text-muted d-block mt-1">
                                    เลือกช่วงระดับชั้น เช่น ป.1 - ม.3
                                </small>
                            </div>


                            {{-- กลุ่มเป้าหมาย --}}
                                <div class="col-12 col-md-6 col-lg-3">
                                    <label class="form-label">
                                        กลุ่มเป้าหมาย
                                    </label>

                                    <select name="target_id" class="form-select">
                                        <option value="">ทั้งหมด</option>

                                        @foreach (\App\Models\Target::orderBy('target_name')->get() as $target)
                                            <option value="{{ $target->id }}"
                                                {{ ($targetId ?? '') == $target->id ? 'selected' : '' }}>

                                                {{ $target->target_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <small class="text-muted d-block mt-1">
                                        เลือกกลุ่มเป้าหมายที่ต้องการประมวลผล
                                    </small>
                                </div>

                            </div>

                        <div class="filter-divider"></div>

                        @php
                            $months = [
                                1 => 'มกราคม',
                                2 => 'กุมภาพันธ์',
                                3 => 'มีนาคม',
                                4 => 'เมษายน',
                                5 => 'พฤษภาคม',
                                6 => 'มิถุนายน',
                                7 => 'กรกฎาคม',
                                8 => 'สิงหาคม',
                                9 => 'กันยายน',
                                10 => 'ตุลาคม',
                                11 => 'พฤศจิกายน',
                                12 => 'ธันวาคม',
                            ];
                        @endphp

                        <div class="row g-3">
                            <div class="col-md-6 col-lg-2">
                                <label class="form-label">สภาพปัญหา</label>
                                <select name="problem" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    @foreach ($problems as $prob)
                                        <option value="{{ $prob->id }}"
                                            {{ request('problem') == $prob->id ? 'selected' : '' }}>
                                            {{ $prob->problem_name ?? ($prob->name ?? '-') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-6 col-lg-2">
                                <label class="form-label">หน่วยงาน / โครงการ</label>
                                <select name="project_id" class="form-select">
                                    <option value="">-- ไม่กำหนดหน่วยงาน --</option>
                                    <option value="all" {{ ($projectId ?? '') == 'all' ? 'selected' : '' }}>
                                        ทั้งหมด
                                    </option>

                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}"
                                            {{ (string) ($projectId ?? '') === (string) $project->id ? 'selected' : '' }}>
                                            {{ $project->project_name ?? ($project->name ?? '-') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>




                            <div class="col-md-3 col-lg-2">
                                <label class="form-label">เดือนเริ่มต้น</label>
                                <select name="start_month" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    @foreach ($months as $num => $name)
                                        <option value="{{ $num }}"
                                            {{ ($startMonth ?? '') == $num ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 col-lg-2">
                                <label class="form-label">ปี พ.ศ. เริ่มต้น</label>
                                <select name="start_year" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    @for ($y = date('Y') + 543; $y >= 2550; $y--)
                                        <option value="{{ $y }}"
                                            {{ ($startYear ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-3 col-lg-2">
                                <label class="form-label">เดือนสิ้นสุด</label>
                                <select name="end_month" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    @foreach ($months as $num => $name)
                                        <option value="{{ $num }}"
                                            {{ ($endMonth ?? '') == $num ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 col-lg-2">
                                <label class="form-label">ปี พ.ศ. สิ้นสุด</label>
                                <select name="end_year" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    @for ($y = date('Y') + 543; $y >= 2550; $y--)
                                        <option value="{{ $y }}" {{ ($endYear ?? '') == $y ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                       <div class="filter-actions mt-4">
                            <button type="submit" class="btn btn-primary filter-submit-btn shadow-sm">
                                <i data-feather="search"></i>
                                <span>ประมวลผลข้อมูล</span>
                            </button>

                          <a href="{{ route('statistics.report', request()->query()) }}"
                                    class="btn filter-submit-btn shadow-sm"
                                    style="
                                        background:linear-gradient(135deg,#0f766e,#14b8a6);
                                        color:#fff;
                                        border:0;
                                    ">
                                        <i class="bi bi-printer"></i>
                                        <span>พิมพ์รายงาน</span>
                                </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="metric-card metric-total">
                        <div class="metric-icon">
                            <i data-feather="users" class="feather-36"></i>
                        </div>
                        <p class="metric-label">จำนวนทั้งหมด</p>
                        <p class="metric-value">{{ $clients->count() ?? 0 }}</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="metric-card metric-male">
                        <div class="metric-icon">
                            <i data-feather="user" class="feather-36"></i>
                        </div>
                        <p class="metric-label">ชาย</p>
                        <p class="metric-value">{{ $maleCount ?? 0 }}</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="metric-card metric-female">
                        <div class="metric-icon">
                            <i data-feather="user-check" class="feather-36"></i>
                        </div>
                        <p class="metric-label">หญิง</p>
                        <p class="metric-value">{{ $femaleCount ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-1">
                <div class="col-xl-6">
                    <div class="card dashboard-card chart-card h-100">
                        <div class="card-header">
                            <div class="chart-title-wrap">
                                <div class="widget-icon-box">
                                    <i data-feather="pie-chart"></i>
                                </div>
                                <div>
                                    <h5 class="section-title mb-1">กราฟสัดส่วนเพศ</h5>
                                    <p class="section-subtitle mb-0">แสดงสัดส่วนผู้รับบริการชายและหญิง</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body chart-panel">
                            <div id="chartGender" class="apex-charts"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card dashboard-card chart-card h-100">
                        <div class="card-header">
                            <div class="chart-title-wrap">
                                <div class="widget-icon-box">
                                    <i data-feather="bar-chart"></i>
                                </div>
                                <div>
                                    <h5 class="section-title mb-1">กราฟระดับการศึกษา</h5>
                                    <p class="section-subtitle mb-0">สรุปจำนวนผู้รับบริการตามระดับการศึกษา</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body chart-panel">
                            <div id="chartEducation" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card dashboard-card table-card">
                        <div class="card-header">
                            <div>
                                <h5 class="section-title mb-1">ตารางข้อมูลผู้รับบริการ</h5>
                                <p class="section-subtitle mb-0">ข้อมูลเชิงรายละเอียดสำหรับการตรวจสอบและวิเคราะห์</p>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="dashboard-table-scroll dashboard-table-fade">
                                <table id="clientsTable" class="table table-hover align-middle w-100">
                                    <thead>
                                        <tr>
                                            <th style="width:18%">ชื่อ</th>
                                            <th>เพศ</th>
                                            <th>อายุ</th>
                                            <th>ระดับการศึกษา</th>
                                            <th>ภาคเรียน</th>
                                            <th>สถานศึกษา</th>
                                            {{-- PATCH: เพิ่มสถานะเพื่อให้ตรวจสอบการรออนุมัติได้ง่าย --}}
                                            <th>สถานะ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($clients as $c)
                                            <tr>
                                                <td>
                                                    <div class="table-student-name">
                                                        <a href="{{ route('admin.index', $c->id) }}" title="ดูข้อมูล"
                                                            class="text-decoration-none text-dark fw-semibold">
                                                            {{ $c->fullname }}
                                                        </a>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($c->gender === 'male')
                                                        ชาย
                                                    @elseif($c->gender === 'female')
                                                        หญิง
                                                    @else
                                                        <span class="table-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($c->birth_date)->age }}</td>

                                                @if ($c->educationRecords->isNotEmpty())
                                                    <td>{{ $c->educationRecords->first()->education->education_name ?? '-' }}
                                                    </td>
                                                    <td>
                                                        {{ $c->educationRecords->first()->semester_label
                                                            ?? $c->educationRecords->first()->semester->semester_name
                                                            ?? '-' }}
                                                    </td>
                                                                                                          
                                                    <td>{{ $c->educationRecords->first()->school_name ?? '-' }}</td>
                                                @else
                                                    <td class="table-muted">-</td>
                                                    <td class="table-muted">-</td>
                                                    <td class="table-muted">-</td>
                                                @endif

                                                <td>
                                                    {{-- =========================
                                                     PATCH: แสดงสถานะผู้รับบริการ
                                                ========================= --}}
                                                    @if ($c->release_status === 'show')
                                                        <span class="badge bg-primary">อยู่อาศัย</span>
                                                    @elseif($c->release_status === 'pending_refer')
                                                        <span class="badge bg-warning text-dark">รออนุมัติจำหน่าย</span>
                                                    @elseif($c->release_status === 'refer')
                                                        <span class="badge bg-success">ถูกจำหน่าย</span>
                                                    @else
                                                        <span class="badge bg-secondary">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">ไม่มีข้อมูล</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (document.querySelector("#chartGender")) {
                var optionsGender = {
                    chart: {
                        type: 'donut',
                        height: 360,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{{ $maleCount ?? 0 }}, {{ $femaleCount ?? 0 }}],
                    labels: ['ชาย', 'หญิง'],
                    colors: ['#16a34a', '#ec4899'],
                    legend: {
                        position: 'bottom',
                        fontFamily: 'Kanit, sans-serif'
                    },
                    dataLabels: {
                        enabled: true
                    },
                    stroke: {
                        width: 0
                    }
                };
                new ApexCharts(document.querySelector("#chartGender"), optionsGender).render();
            }

            if (document.querySelector("#chartEducation")) {
                var optionsEducation = {
                    chart: {
                        type: 'bar',
                        height: 360,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'จำนวน',
                        data: [
                            @foreach ($educationCounts as $eduName => $count)
                                {{ $count }},
                            @endforeach
                        ]
                    }],
                    xaxis: {
                        categories: [
                            @foreach ($educationCounts as $eduName => $count)
                                '{{ $eduName }}',
                            @endforeach
                        ],
                        labels: {
                            style: {
                                fontFamily: 'Kanit, sans-serif'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                fontFamily: 'Kanit, sans-serif'
                            }
                        }
                    },
                    colors: ['#2563eb'],
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '48%',
                            borderRadius: 8
                        }
                    },
                    dataLabels: {
                        enabled: true
                    },
                    grid: {
                        borderColor: '#e5e7eb'
                    }
                };
                new ApexCharts(document.querySelector("#chartEducation"), optionsEducation).render();
            }

            if ($.fn.DataTable && $('#clientsTable').length) {
                $('#clientsTable').DataTable({
                    destroy: true,
                    responsive: false,
                    autoWidth: false,
                    scrollX: true,
                    scrollCollapse: false,
                    pageLength: 10,
                    order: [
                        [0, 'asc']
                    ],
                    language: {
                        search: "ค้นหา:",
                        lengthMenu: "แสดง _MENU_ รายการต่อหน้า",
                        info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                        infoEmpty: "ไม่มีข้อมูลให้แสดง",
                        infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                        zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                        paginate: {
                            first: "หน้าแรก",
                            last: "หน้าสุดท้าย",
                            next: "ถัดไป",
                            previous: "ก่อนหน้า"
                        }
                    },
                    columnDefs: [{
                        targets: 0,
                        width: "18%"
                    }],
                    initComplete: function() {
                        if (window.feather) {
                            feather.replace();
                        }
                    }
                });
            }

            if ($.fn.datepicker && $('.datepicker-th').length) {
                $('.datepicker-th').datepicker({
                    format: 'dd/mm/yyyy',
                    language: 'th',
                    thaiyear: true,
                    autoclose: true,
                    todayHighlight: true
                });
            }

            if (window.feather) {
                feather.replace();
            }
        });
    </script>

    <script>
        @if (Session::has('message'))
            var type = "{{ Session::get('alert-type', 'info') }}";
            switch (type) {
                case 'info':
                    toastr.info("{{ Session::get('message') }}");
                    break;
                case 'success':
                    toastr.success("{{ Session::get('message') }}");
                    break;
                case 'warning':
                    toastr.warning("{{ Session::get('message') }}");
                    break;
                case 'error':
                    toastr.error("{{ Session::get('message') }}");
                    break;
            }
        @endif
    </script>
@endpush
