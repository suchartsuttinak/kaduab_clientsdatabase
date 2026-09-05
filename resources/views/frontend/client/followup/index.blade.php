@extends('admin_client.admin_client')

@push('styles')
<style>
        .followup-page {
            --followup-footer-space: 2rem;
            min-height: auto;
            height: auto;
            max-height: none;
            padding-bottom: var(--followup-footer-space);
            overflow: visible;
            scroll-padding-bottom: var(--followup-footer-space);
        }

        .followup-page .followup-card {
            background: #fff;
            border: 1px solid #e8edf4;
            border-radius: 22px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .followup-page .followup-header {
            padding: 1.25rem;
            border-bottom: 1px solid #eef2f7;
            background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
        }

        .followup-page .followup-title-wrap {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .followup-page .followup-title-box h2 {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 700;
            color: #1e293b;
        }

        .followup-page .followup-title-box p {
            margin: .4rem 0 0;
            color: #64748b;
            line-height: 1.7;
        }

        .followup-page .followup-badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .7rem 1rem;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
            white-space: nowrap;
        }

        .followup-page .client-meta {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem .75rem;
            margin-top: .75rem;
        }

        .followup-page .client-chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .45rem .8rem;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: .92rem;
        }

        .followup-page .followup-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
            padding: 1rem 1.25rem;
            flex-wrap: wrap;
        }

        .followup-page .followup-toolbar-left,
        .followup-page .followup-toolbar-right {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .followup-page .followup-btn {
            min-height: 42px;
            padding: .65rem 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            border-radius: 12px;
            border-width: 1px;
            font-weight: 700;
            line-height: 1.25;
            white-space: nowrap;
            box-shadow: 0 5px 14px rgba(15, 23, 42, .07);
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
        }

        .followup-page .followup-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(15, 23, 42, .11);
        }


        .followup-page .followup-btn:disabled,
        .followup-page .followup-btn.disabled {
            opacity: 1;
            transform: none;
            cursor: not-allowed;
            box-shadow: 0 5px 14px rgba(15, 23, 42, .07);
        }

        .followup-page .followup-btn:focus-visible {
            outline: 0;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .16), 0 8px 18px rgba(15, 23, 42, .1);
        }

        .followup-page .followup-btn-primary {
            color: #fff;
            border-color: #1d4ed8;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }

        .followup-page .followup-btn-primary:hover,
        .followup-page .followup-btn-primary:focus,
        .followup-page .followup-btn-primary:active {
            color: #fff;
            border-color: #1e40af;
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        }

        .followup-page .followup-btn-filter {
            color: #1d4ed8;
            border-color: #93c5fd;
            background: #eff6ff;
        }

        .followup-page .followup-btn-filter:hover,
        .followup-page .followup-btn-filter:focus,
        .followup-page .followup-btn-filter[aria-expanded="true"] {
            color: #fff;
            border-color: #2563eb;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .followup-page .followup-btn-filter .followup-filter-chevron {
            font-size: .78rem;
            transition: transform .2s ease;
        }

        .followup-page .followup-btn-filter[aria-expanded="true"] .followup-filter-chevron {
            transform: rotate(180deg);
        }

        .followup-page .followup-btn-report {
            color: #0f766e;
            border-color: #99f6e4;
            background: #f0fdfa;
        }

        .followup-page .followup-btn-report:hover,
        .followup-page .followup-btn-report:focus {
            color: #fff;
            border-color: #0f766e;
            background: #0f766e;
        }

        .followup-page .followup-btn-back,
        .followup-page .followup-btn-secondary {
            color: #475569;
            border-color: #cbd5e1;
            background: #fff;
        }

        .followup-page .followup-btn-back:hover,
        .followup-page .followup-btn-back:focus,
        .followup-page .followup-btn-secondary:hover,
        .followup-page .followup-btn-secondary:focus {
            color: #0f172a;
            border-color: #94a3b8;
            background: #f8fafc;
        }

        .followup-page .followup-search-collapse {
            width: 100%;
        }

        .followup-page .followup-note {
            color: #64748b;
            font-size: .95rem;
        }

        .followup-page .followup-filter-box {
            margin: 0 1.25rem 1.25rem;
            padding: 1rem;
            border-radius: 18px;
            border: 1px solid #e8edf4;
            background: #fbfdff;
        }

        .followup-page .followup-filter-box .form-control {
            min-height: 44px;
            border-radius: 14px;
        }

        .followup-page .followup-filter-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            gap: .5rem;
        }

        /* แยกชั้นของแถบปุ่ม ตัวกรอง และตารางให้ชัดเจน ป้องกันการซ้อนทับจาก CSS ของ layout */
        .followup-page .followup-toolbar,
        .followup-page .followup-filter-box,
        .followup-page .table-wrap {
            position: relative;
            inset: auto;
            clear: both;
            float: none;
            transform: none;
        }

        .followup-page .followup-toolbar {
            z-index: 3;
        }

        .followup-page .followup-filter-box {
            z-index: 2;
            height: auto;
            min-height: 0;
            overflow: visible;
        }

        .followup-page .table-wrap {
            z-index: 1;
            margin-top: 0;
        }

        .followup-page .dataTables_wrapper {
            position: relative;
            clear: both;
            width: 100%;
            z-index: 1;
        }

        .followup-page .table-wrap {
            width: 100%;
            padding: 0 1.25rem 1.25rem;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .followup-page table.dataTable thead th {
            white-space: nowrap;
            vertical-align: middle;
            padding: .8rem .75rem;
        }

        .followup-page .followup-table {
            width: 100% !important;
            min-width: 960px;
            table-layout: fixed;
            margin-bottom: 0 !important;
        }

        .followup-page .followup-table tbody td {
            vertical-align: top !important;
            padding: .8rem .75rem;
        }

        .followup-page .followup-table .date-column {
            width: 145px;
            min-width: 145px;
            white-space: nowrap;
        }

        .followup-page .followup-table .detail-column {
            width: auto;
            min-width: 360px;
        }

        .followup-page .followup-table .note-column {
            width: 250px;
            min-width: 250px;
        }

        .followup-page .followup-table .action-column {
            width: 170px;
            min-width: 170px;
            white-space: nowrap;
        }

        .followup-page .text-preline {
            white-space: pre-line;
            overflow-wrap: anywhere;
            word-break: break-word;
            line-height: 1.7;
        }

        .followup-page .action-cell {
            vertical-align: top !important;
        }

        .followup-page .action-group {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            flex-wrap: nowrap !important;
            white-space: nowrap;
            width: max-content;
            min-width: max-content;
        }

        .followup-page .action-group form {
            display: inline-flex !important;
            flex: 0 0 auto;
            margin: 0;
        }

        .followup-page .btn-action {
            flex: 0 0 40px;
            width: 40px;
            min-width: 40px;
            height: 40px;
            padding: 0;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            border-radius: 12px;
            line-height: 1;
        }

        .followup-page .btn-action i {
            margin: 0;
            line-height: 1;
        }


        .followup-page .btn-action-report {
            color: #1d4ed8;
            border-color: #bfdbfe;
            background: #eff6ff;
        }

        .followup-page .btn-action-report:hover,
        .followup-page .btn-action-report:focus {
            color: #fff;
            border-color: #2563eb;
            background: #2563eb;
        }

        .followup-page .btn-action-edit {
            color: #92400e;
            border-color: #fde68a;
            background: #fffbeb;
        }

        .followup-page .btn-action-edit:hover,
        .followup-page .btn-action-edit:focus {
            color: #fff;
            border-color: #d97706;
            background: #d97706;
        }

        .followup-page .btn-action-delete {
            color: #b91c1c;
            border-color: #fecaca;
            background: #fef2f2;
        }

        .followup-page .btn-action-delete:hover,
        .followup-page .btn-action-delete:focus {
            color: #fff;
            border-color: #dc2626;
            background: #dc2626;
        }

        .followup-page .modal-footer .followup-btn {
            min-width: 120px;
        }

        .followup-page .dataTables_wrapper .dataTables_length,
        .followup-page .dataTables_wrapper .dataTables_filter {
            margin-bottom: .25rem;
        }

        .followup-page .dataTables_wrapper .dataTables_filter input {
            width: 172px;
            min-height: 42px;
            margin-left: .45rem;
            border: 1px solid #cbd5e1;
            border-radius: 13px;
            padding: .45rem .75rem;
        }

        .followup-page .modal .form-label {
            font-weight: 600;
            color: #334155;
        }

        .followup-page .required-star {
            color: #dc2626;
        }

        .followup-page .followup-empty {
            margin: 0 1.25rem 1.25rem;
            padding: 3rem 1.25rem;
            border: 1px dashed #cbd5e1;
            border-radius: 22px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            text-align: center;
        }

        .followup-page .followup-empty-icon {
            width: 82px;
            height: 82px;
            margin: 0 auto 1rem;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 2rem;
            box-shadow: 0 12px 28px rgba(29, 78, 216, .12);
        }

        .followup-page .followup-empty h4 {
            margin: 0 0 .5rem;
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
        }

        .followup-page .followup-empty p {
            max-width: 620px;
            margin: 0 auto 1.25rem;
            color: #64748b;
            line-height: 1.8;
        }

        @media (min-width: 1400px) {
            .followup-page .followup-table {
                min-width: 100%;
            }

            .followup-page .followup-table .date-column {
                width: 140px;
                min-width: 140px;
            }

            .followup-page .followup-table .note-column {
                width: 280px;
                min-width: 280px;
            }

            .followup-page .followup-table .action-column {
                width: 165px;
                min-width: 165px;
            }
        }

        @media (min-width: 992px) and (max-width: 1399.98px) {
            .followup-page .followup-table {
                min-width: 980px;
            }

            .followup-page .followup-table .date-column {
                width: 132px;
                min-width: 132px;
            }

            .followup-page .followup-table .detail-column {
                min-width: 390px;
            }

            .followup-page .followup-table .note-column {
                width: 260px;
                min-width: 260px;
            }

            .followup-page .followup-table .action-column {
                width: 160px;
                min-width: 160px;
            }
        }

        /* จอขนาดกลาง: ให้ปุ่มอยู่ในพื้นที่ของตนเอง ไม่ลอยหรือทับส่วนตาราง */
        @media (min-width: 768px) and (max-width: 1199.98px) {
            .followup-page .followup-toolbar {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                align-items: stretch;
                gap: .65rem;
            }

            .followup-page .followup-toolbar-left,
            .followup-page .followup-toolbar-right {
                display: contents;
            }

            .followup-page .followup-toolbar .btn {
                width: 100%;
                min-width: 0;
                min-height: 42px;
                margin: 0;
                white-space: normal;
                line-height: 1.35;
            }

            .followup-page .followup-filter-box form > .col-md-3 {
                flex: 0 0 50%;
                width: 50%;
                max-width: 50%;
            }

            .followup-page .followup-filter-box form > .col-md-6 {
                flex: 0 0 100%;
                width: 100%;
                max-width: 100%;
            }

            .followup-page .followup-filter-actions {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                width: 100%;
                gap: .65rem;
            }

            .followup-page .followup-filter-actions .btn {
                width: 100%;
                min-width: 0;
                min-height: 42px;
                margin: 0;
                white-space: normal;
                line-height: 1.35;
            }

            .followup-page .followup-filter-box {
                margin-bottom: 1.25rem;
            }

            .followup-page .table-wrap {
                padding-top: 0;
            }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .followup-page .followup-table {
                min-width: 930px;
            }

            .followup-page .followup-table .date-column {
                width: 128px;
                min-width: 128px;
            }

            .followup-page .followup-table .detail-column {
                min-width: 350px;
            }

            .followup-page .followup-table .note-column {
                width: 245px;
                min-width: 245px;
            }

            .followup-page .followup-table .action-column {
                width: 158px;
                min-width: 158px;
            }
        }

        @media (max-width: 767.98px) {
            html,
            body {
                min-height: 100%;
                overflow-x: hidden;
            }

            body {
                overflow-y: auto !important;
            }

            .followup-page {
                --followup-footer-space: calc(7rem + env(safe-area-inset-bottom));
                min-height: auto !important;
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
                padding-bottom: var(--followup-footer-space) !important;
            }

            .followup-page .followup-card {
                overflow: visible;
                margin-bottom: 1.5rem;
            }

            .followup-page .followup-filter-box,
            .followup-page .table-wrap,
            .followup-page .followup-empty {
                scroll-margin-bottom: var(--followup-footer-space);
            }

            .followup-page .followup-header,
            .followup-page .followup-toolbar,
            .followup-page .table-wrap {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .followup-page .followup-filter-box,
            .followup-page .followup-empty {
                margin-left: 1rem;
                margin-right: 1rem;
            }

            .followup-page .followup-title-box h2 {
                font-size: 1.15rem;
            }

            .followup-page .followup-toolbar {
                display: flex;
                grid-template-columns: none;
            }

            .followup-page .followup-toolbar-left,
            .followup-page .followup-toolbar-right {
                display: flex;
                width: 100%;
            }

            .followup-page .followup-filter-box form > .col-md-3,
            .followup-page .followup-filter-box form > .col-md-6 {
                flex: 0 0 100%;
                width: 100%;
                max-width: 100%;
            }

            .followup-page .followup-filter-actions {
                display: flex;
                width: 100%;
            }

            .followup-page .followup-toolbar-left .btn,
            .followup-page .followup-toolbar-right .btn,
            .followup-page .followup-filter-actions .btn {
                flex: 1 1 100%;
            }

            .followup-page .followup-table {
                min-width: 860px;
            }

            .followup-page .followup-table .date-column {
                width: 130px;
                min-width: 130px;
            }

            .followup-page .followup-table .detail-column {
                min-width: 330px;
            }

            .followup-page .followup-table .note-column {
                width: 215px;
                min-width: 215px;
            }

            .followup-page .followup-table .action-column {
                width: 155px;
                min-width: 155px;
            }

            .followup-page .dataTables_wrapper .dataTables_length,
            .followup-page .dataTables_wrapper .dataTables_filter {
                width: 100%;
                text-align: left !important;
            }

            .followup-page .dataTables_wrapper .dataTables_filter {
                margin-top: .75rem;
            }

            .followup-page .dataTables_wrapper .dataTables_filter input {
                width: calc(100% - 55px);
                max-width: 260px;
            }
        }
    </style>
@endpush

@section('content')

@if(session('error'))
    <div class="alert alert-danger mx-3 mx-md-4 mb-3">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger mx-3 mx-md-4 mb-3">
        <div class="fw-bold mb-1">กรุณาตรวจสอบข้อมูล</div>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="followup-page">
    

    @php
        $thaiMonths = [
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

        $hasFollowupRows = isset($followups) && $followups->count() > 0;

        $hasDateFilter = request()->filled('date_from')
            || request()->filled('date_to')
            || !empty($dateFrom)
            || !empty($dateTo);

        $hasFilterErrors = $errors->has('date_from') || $errors->has('date_to');
        $showSearchPanel = $hasDateFilter || $hasFilterErrors;

        $followupPermissionUser = auth()->user();
        $canCreateFollowup = (bool) ($followupPermissionUser?->canCreateForm('welfare_followup') ?? false);
        $canUpdateFollowup = (bool) ($followupPermissionUser?->canUpdateForm('welfare_followup') ?? false);
        $canDeleteFollowup = (bool) ($followupPermissionUser?->canDeleteForm('welfare_followup') ?? false);
        $canPrintFollowup = (bool) ($followupPermissionUser?->canPrintForm('welfare_followup') ?? false);

        // มีข้อมูล หรือกำลังค้นหาตามช่วงวันที่ จึงแสดงส่วนค้นหา/ตาราง
        $showDataSection = $hasFollowupRows || $hasDateFilter || $hasFilterErrors;
    @endphp

    <div class="followup-card">
        <div class="followup-header">
            <div class="followup-title-wrap">
                <div class="followup-title-box">
                    <h2>
                        <i class="bi bi-journal-check me-2"></i>
                        ติดตามผลการช่วยเหลือ
                    </h2>
                    <p>
                        บันทึกข้อมูลการช่วยเหลือและติดตามผลของผู้รับบริการอย่างเป็นระบบ อ่านง่าย ใช้งานสะดวก
                        และรองรับทุกขนาดหน้าจอ
                    </p>

                    <div class="client-meta">
                        <div class="client-chip">
                            <i class="bi bi-person-vcard"></i>
                            รหัสผู้รับบริการ: {{ $client->id }}
                        </div>

                        @if(!empty($client->fullname))
                            <div class="client-chip">
                                <i class="bi bi-person-circle"></i>
                                {{ $client->fullname }}
                            </div>
                        @elseif(!empty($client->name))
                            <div class="client-chip">
                                <i class="bi bi-person-circle"></i>
                                {{ $client->name }}
                            </div>
                        @endif
                    </div>
                </div>

                @if($hasFollowupRows)
                    <div class="followup-badge">
                        <i class="bi bi-clipboard2-pulse"></i>
                        <span>{{ $followups->count() }} รายการ</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="followup-toolbar">
            <div class="followup-toolbar-left">
                <a href="{{ route('client.edit', $client->id) }}"
                   class="btn followup-btn followup-btn-back">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>กลับหน้าแก้ไขผู้รับบริการ</span>
                </a>

                @if($hasFollowupRows)
                    <a href="{{ route('followup.report', $client->id) }}"
                       class="btn followup-btn followup-btn-report">
                        <i class="bi bi-printer"></i>
                        <span>รายงาน</span>
                    </a>
                @endif
            </div>

            <div class="followup-toolbar-right">
                @if($showDataSection)
                    <button type="button"
                            class="btn followup-btn followup-btn-filter"
                            data-bs-toggle="collapse"
                            data-bs-target="#followupSearchPanel"
                            aria-expanded="{{ $showSearchPanel ? 'true' : 'false' }}"
                            aria-controls="followupSearchPanel"
                            data-followup-filter-toggle>
                        <i class="bi bi-search"></i>
                        <span data-followup-filter-label>{{ $showSearchPanel ? 'ซ่อนค้นหา' : 'ค้นหา' }}</span>
                        <i class="bi bi-chevron-down followup-filter-chevron" aria-hidden="true"></i>
                    </button>
                @endif

                @if($canCreateFollowup && $showDataSection)
                    <button type="button"
                            class="btn followup-btn followup-btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#createFollowupModal">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มข้อมูลติดตามผล</span>
                    </button>
                @endif
            </div>
        </div>

        @if($showDataSection)
            <div id="followupSearchPanel"
                 class="collapse followup-search-collapse {{ $showSearchPanel ? 'show' : '' }}">
                <div class="followup-filter-box">
                    <form action="{{ route('followup.index', $client->id) }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">วันที่เริ่มต้น</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? request('date_from') }}" max="{{ now('Asia/Bangkok')->toDateString() }}">
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold">วันที่สิ้นสุด</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? request('date_to') }}" max="{{ now('Asia/Bangkok')->toDateString() }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="followup-filter-actions">
                            <button type="submit" class="btn followup-btn followup-btn-primary">
                                <i class="bi bi-search"></i>
                                <span>ค้นหาตามช่วงวันที่</span>
                            </button>

                            <a href="{{ route('followup.index', $client->id) }}" class="btn followup-btn followup-btn-secondary">
                                <i class="bi bi-arrow-clockwise"></i>
                                <span>ล้างตัวกรอง</span>
                            </a>

                            @if($hasFollowupRows)
                                <a href="{{ route('followup.report', ['client_id' => $client->id, 'date_from' => request('date_from'), 'date_to' => request('date_to')]) }}"
                                   class="btn followup-btn followup-btn-report">
                                    <i class="bi bi-file-earmark-text"></i>
                                    <span>รายงานตามช่วงวันที่</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    </form>
                </div>
            </div>

            @if($hasFollowupRows)
                <div class="table-wrap">
                    <x-stable-table-controls target="followupTable" />
                    <table id="followupTable" class="table table-bordered table-hover followup-table" data-stable-table data-page-length="10">
                        <colgroup>
                            <col class="date-column">
                            <col class="detail-column">
                            <col class="note-column">
                            <col class="action-column">
                        </colgroup>
                        <thead class="table-light">
                            <tr>
                                <th class="date-column">วันเดือนปี</th>
                                <th class="detail-column">การช่วยเหลือและติดตามผล</th>
                                <th class="note-column">หมายเหตุ</th>
                                <th class="action-column text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($followups as $item)
                                @php
                                    $date = \Carbon\Carbon::parse($item->followup_date);
                                    $thaiDate = $date->day . ' ' . $thaiMonths[$date->month] . ' ' . ($date->year + 543);
                                @endphp

                                <tr>
                                    <td class="date-column">{{ $thaiDate }}</td>
                                    <td class="detail-column text-preline">{{ $item->assistance_detail }}</td>
                                    <td class="note-column text-preline">{{ $item->note ?: '-' }}</td>
                                    <td class="action-column action-cell text-center">
                                        <div class="action-group justify-content-center">
                                            @if($canPrintFollowup)
                                                <a href="{{ route('followup.report_item', $item->id) }}"
                                                   class="btn btn-sm btn-action btn-action-report"
                                                   title="เปิดรายงาน"
                                                   aria-label="เปิดรายงาน">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                </a>
                                            @endif

                                            @if($canUpdateFollowup)
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-action btn-action-edit edit-followup-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editFollowupModal"
                                                    data-id="{{ $item->id }}"
                                                    data-date="{{ \Carbon\Carbon::parse($item->followup_date)->format('Y-m-d') }}"
                                                    data-detail="{{ e($item->assistance_detail) }}"
                                                    data-note="{{ e($item->note) }}"
                                                    title="แก้ไข"
                                                >
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                            @endif

                                            @if($canDeleteFollowup)
                                                <form action="{{ route('followup.delete', $item->id) }}" method="POST" class="d-inline delete-followup-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-action btn-action-delete" title="ลบ" aria-label="ลบ">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <x-stable-table-footer target="followupTable" :total="$followups->count()" />
                </div>
            @else
                <div class="followup-empty">
                    <div class="followup-empty-icon">
                        <i class="bi bi-search"></i>
                    </div>

                    <h4>ไม่พบข้อมูลตามช่วงวันที่ที่ค้นหา</h4>
                    <p>
                        กรุณาปรับช่วงวันที่ใหม่ หรือล้างตัวกรองเพื่อกลับไปดูข้อมูลทั้งหมด
                    </p>

                    <a href="{{ route('followup.index', $client->id) }}" class="btn followup-btn followup-btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                        <span>ล้างตัวกรอง</span>
                    </a>
                </div>
            @endif
        @else
            <div class="followup-empty">
                <div class="followup-empty-icon">
                    <i class="bi bi-inbox"></i>
                </div>

                <h4>ยังไม่มีข้อมูลติดตามผล</h4>
                <p>
                    เมื่อยังไม่มีข้อมูล ระบบจะซ่อนช่องค้นหาช่วงวันที่ ปุ่มรายงาน และตารางไว้ก่อน
                    เพื่อให้หน้าจอดูสะอาดและใช้งานง่ายขึ้น
                </p>

                @if($canCreateFollowup)
                    <button type="button"
                            class="btn followup-btn followup-btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#createFollowupModal">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มข้อมูลติดตามผล</span>
                    </button>
                @endif
            </div>
        @endif
    </div>

    {{-- Create Modal --}}
    <div class="modal fade" id="createFollowupModal" tabindex="-1" aria-labelledby="createFollowupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <form action="{{ route('followup.store', $client->id) }}" method="POST" class="modal-content followup-save-form">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="createFollowupModalLabel">
                        <i class="bi bi-plus-circle me-1"></i>
                        เพิ่มข้อมูลติดตามผล
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">
                                วันเดือนปี <span class="required-star">*</span>
                            </label>
                            <input type="date" name="followup_date" class="form-control" value="{{ old('followup_date') }}" max="{{ now('Asia/Bangkok')->toDateString() }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                การช่วยเหลือและติดตามผล <span class="required-star">*</span>
                            </label>
                            <textarea name="assistance_detail" rows="5" class="form-control" required>{{ old('assistance_detail') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea name="note" rows="4" class="form-control">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn followup-btn followup-btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        <span>ปิด</span>
                    </button>

                    <button type="submit" class="btn followup-btn followup-btn-primary">
                        <i class="bi bi-check-circle"></i>
                        <span>บันทึกข้อมูล</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editFollowupModal" tabindex="-1" aria-labelledby="editFollowupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <form id="editFollowupForm" action="" method="POST" class="modal-content followup-save-form">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="editFollowupModalLabel">
                        <i class="bi bi-pencil-square me-1"></i>
                        แก้ไขข้อมูลติดตามผล
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">
                                วันเดือนปี <span class="required-star">*</span>
                            </label>
                            <input type="date" name="followup_date" id="edit_followup_date" class="form-control" max="{{ now('Asia/Bangkok')->toDateString() }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                การช่วยเหลือและติดตามผล <span class="required-star">*</span>
                            </label>
                            <textarea name="assistance_detail" id="edit_assistance_detail" rows="5" class="form-control" required></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea name="note" id="edit_note" rows="4" class="form-control"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn followup-btn followup-btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        <span>ปิด</span>
                    </button>

                    <button type="submit" class="btn followup-btn followup-btn-primary">
                        <i class="bi bi-check-circle"></i>
                        <span>บันทึกการแก้ไข</span>
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
    const followupPage = document.querySelector('.followup-page');
    const successMessage = @json(session('success') ?? session('message') ?? session('status') ?? session('success_message'));
    const filterPanel = document.getElementById('followupSearchPanel');
    const filterToggle = document.querySelector('[data-followup-filter-toggle]');

    if (filterPanel && filterToggle) {
        const filterLabel = filterToggle.querySelector('[data-followup-filter-label]');

        filterPanel.addEventListener('show.bs.collapse', function () {
            filterToggle.setAttribute('aria-expanded', 'true');
            if (filterLabel) {
                filterLabel.textContent = 'ซ่อนค้นหา';
            }
        });

        filterPanel.addEventListener('hide.bs.collapse', function () {
            filterToggle.setAttribute('aria-expanded', 'false');
            if (filterLabel) {
                filterLabel.textContent = 'ค้นหา';
            }
        });
    }

    const editButtons = document.querySelectorAll('.edit-followup-btn');
    const editForm = document.getElementById('editFollowupForm');
    const editDate = document.getElementById('edit_followup_date');
    const editDetail = document.getElementById('edit_assistance_detail');
    const editNote = document.getElementById('edit_note');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id') || '';
            const date = this.getAttribute('data-date') || '';
            const detail = this.getAttribute('data-detail') || '';
            const note = this.getAttribute('data-note') || '';

            editForm.action = "{{ url('/followup/update') }}/" + id;
            editDate.value = date;
            editDetail.value = detail;
            editNote.value = note;
        });
    });

    document.querySelectorAll('.followup-save-form').forEach(form => {
        form.addEventListener('submit', function () {
            const submitButton = form.querySelector('button[type="submit"]');

            if (!submitButton || submitButton.disabled) {
                return;
            }

            submitButton.disabled = true;
            submitButton.setAttribute('aria-disabled', 'true');
        });
    });

    if (successMessage) {
        let successTitle = 'บันทึกข้อมูลสำเร็จ';

        if (/แก้ไข|อัปเดต/.test(successMessage)) {
            successTitle = 'แก้ไขข้อมูลสำเร็จ';
        } else if (/ลบ/.test(successMessage)) {
            successTitle = 'ลบข้อมูลสำเร็จ';
        }

        if (window.Swal) {
            Swal.fire({
                icon: 'success',
                title: successTitle,
                text: successMessage,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#2563eb'
            });
        } else {
            window.alert(successMessage);
        }
    }

    document.querySelectorAll('.delete-followup-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (window.Swal) {
                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    text: 'เมื่อลบแล้วจะไม่สามารถกู้คืนข้อมูลนี้ได้',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่, ลบข้อมูล',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('ยืนยันการลบข้อมูลนี้ใช่หรือไม่?')) {
                    form.submit();
                }
            }
        });
    });

    @if(session('followup_modal') === 'create' && $errors->any())
        const createModal = new bootstrap.Modal(document.getElementById('createFollowupModal'));
        createModal.show();
    @endif

    @if(session('followup_modal') === 'edit' && session('followup_edit_id'))
        @php
            $editItem = $followups->firstWhere('id', session('followup_edit_id'));
        @endphp

        @if($editItem)
            editForm.action = "{{ url('/followup/update/' . $editItem->id) }}";
            editDate.value = "{{ \Carbon\Carbon::parse($editItem->followup_date)->format('Y-m-d') }}";
            editDetail.value = @json($editItem->assistance_detail ?? '');
            editNote.value = @json($editItem->note ?? '');

            const editModal = new bootstrap.Modal(document.getElementById('editFollowupModal'));
            editModal.show();
        @endif
    @endif
});
</script>
@endpush