@extends('admin_client.admin_client')

@section('content')
@php
    use Carbon\Carbon;

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

    $dateRangeThai = 'ทั้งหมด';

    if (!empty($dateFrom) || !empty($dateTo)) {
        $fromText = !empty($dateFrom)
            ? Carbon::parse($dateFrom)->day . ' ' . $thaiMonths[Carbon::parse($dateFrom)->month] . ' ' . (Carbon::parse($dateFrom)->year + 543)
            : 'ไม่กำหนด';

        $toText = !empty($dateTo)
            ? Carbon::parse($dateTo)->day . ' ' . $thaiMonths[Carbon::parse($dateTo)->month] . ' ' . (Carbon::parse($dateTo)->year + 543)
            : 'ไม่กำหนด';

        $dateRangeThai = $fromText . ' ถึง ' . $toText;
    }
@endphp

<style>
    @page {
        size: A4 portrait;
        margin: 12mm;
    }

    .followup-report-page {
        --report-border: #dbe3ec;
        --report-text: #1f2937;
        --report-muted: #64748b;
        width: 100%;
        min-width: 0;
        padding: .25rem .75rem 2.5rem;
    }

    .followup-report-page,
    .followup-report-page * {
        box-sizing: border-box;
    }

    .followup-report-card {
        width: 100%;
        min-width: 0;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .045);
    }

    .followup-report-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .7rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #edf2f7;
    }

    .followup-report-toolbar-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: .6rem;
    }

    .followup-report-toolbar .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        min-height: 42px;
        padding: .65rem 1rem;
        border-radius: 12px;
        font-weight: 700;
        white-space: nowrap;
        box-shadow: 0 5px 14px rgba(15, 23, 42, .07);
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease;
    }

    .followup-report-toolbar .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(15, 23, 42, .11);
    }

    .followup-report-btn-back {
        color: #475569;
        border: 1px solid #cbd5e1;
        background: #fff;
    }

    .followup-report-btn-back:hover,
    .followup-report-btn-back:focus {
        color: #0f172a;
        border-color: #94a3b8;
        background: #f8fafc;
    }

    .followup-report-btn-print {
        color: #fff;
        border: 1px solid #1d4ed8;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    }

    .followup-report-btn-print:hover,
    .followup-report-btn-print:focus,
    .followup-report-btn-print:active {
        color: #fff;
        border-color: #1e40af;
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    }

    .followup-report-header {
        padding: 1.25rem;
        border-bottom: 1px solid #edf2f7;
    }

    .followup-report-title {
        margin: 0;
        color: var(--report-text);
        font-size: 1.3rem;
        font-weight: 700;
        line-height: 1.35;
    }

    .followup-report-subtitle {
        margin: .4rem 0 0;
        color: var(--report-muted);
        font-size: .93rem;
        line-height: 1.6;
    }

    .followup-report-info {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .7rem 1rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #edf2f7;
    }

    .followup-report-info-item {
        display: grid;
        grid-template-columns: max-content minmax(0, 1fr);
        gap: .35rem;
        min-width: 0;
        color: var(--report-text);
        font-size: .92rem;
    }

    .followup-report-info-label {
        font-weight: 700;
    }

    .followup-report-info-value {
        min-width: 0;
        overflow-wrap: anywhere;
    }

    .followup-report-body {
        padding: 1.25rem;
    }

    .followup-report-section-title {
        margin: 0 0 .75rem;
        color: var(--report-text);
        font-size: 1rem;
        font-weight: 700;
    }

    .followup-report-table-wrap {
        width: 100%;
        min-width: 0;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }

    .followup-report-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .followup-report-table th,
    .followup-report-table td {
        border: 1px solid var(--report-border);
    }

    .followup-report-table th {
        padding: .72rem;
        background: #f1f5f9;
        color: var(--report-text);
        font-size: .9rem;
        font-weight: 700;
        line-height: 1.35;
        text-align: center;
    }

    .followup-report-table td {
        padding: .75rem;
        background: #fff;
        color: var(--report-text);
        font-size: .9rem;
        line-height: 1.65;
        vertical-align: top;
        overflow-wrap: anywhere;
    }

    .followup-col-date {
        width: 132px;
    }

    .followup-col-note {
        width: 30%;
    }

    .followup-date-cell {
        font-weight: 600;
        text-align: center;
        white-space: nowrap;
    }

    .text-preline {
        white-space: pre-line;
    }

    .followup-report-empty {
        padding: 2.5rem 1rem;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        background: #fbfdff;
        text-align: center;
    }

    .followup-report-empty h4 {
        margin: 0 0 .4rem;
        color: var(--report-text);
        font-size: 1.1rem;
        font-weight: 700;
    }

    .followup-report-empty p {
        margin: 0;
        color: var(--report-muted);
    }

    @media (max-width: 991.98px) {
        .followup-report-info {
            grid-template-columns: 1fr 1fr;
        }

        .followup-report-info-item:last-child {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 767.98px) {
        .followup-report-page {
            padding: 0 .5rem 2rem;
        }

        .followup-report-card {
            border-radius: 13px;
        }

        .followup-report-toolbar,
        .followup-report-header,
        .followup-report-body {
            padding-left: .9rem;
            padding-right: .9rem;
        }

        .followup-report-toolbar,
        .followup-report-toolbar-group {
            display: grid;
            grid-template-columns: 1fr;
            width: 100%;
        }

        .followup-report-toolbar .btn {
            width: 100%;
        }

        .followup-report-title {
            font-size: 1.12rem;
        }

        .followup-report-info {
            grid-template-columns: 1fr;
        }

        .followup-report-info-item:last-child {
            grid-column: auto;
        }

        .followup-report-table-wrap {
            overflow: visible;
        }

        .followup-report-table {
            min-width: 0;
            border: 0;
        }

        .followup-report-table thead {
            display: none;
        }

        .followup-report-table tbody,
        .followup-report-table tr,
        .followup-report-table td {
            display: block;
            width: 100% !important;
        }

        .followup-report-table tr {
            margin-bottom: .85rem;
            border: 1px solid var(--report-border);
            border-radius: 12px;
            overflow: hidden;
        }

        .followup-report-table tr:last-child {
            margin-bottom: 0;
        }

        .followup-report-table td {
            display: grid;
            grid-template-columns: 112px minmax(0, 1fr);
            gap: .7rem;
            padding: .7rem .8rem;
            border: 0;
            border-bottom: 1px solid #edf2f7;
            text-align: left;
        }

        .followup-report-table td:last-child {
            border-bottom: 0;
        }

        .followup-report-table td::before {
            content: attr(data-label);
            color: #475569;
            font-weight: 700;
        }

        .followup-date-cell {
            white-space: normal;
        }
    }

    @media (max-width: 479.98px) {
        .followup-report-info-item,
        .followup-report-table td {
            grid-template-columns: 1fr;
            gap: .25rem;
        }
    }

    @media print {
        .navbar-custom,
        .leftside-menu,
        .footer,
        .topbar,
        .page-title-box,
        .followup-report-toolbar {
            display: none !important;
        }

        html,
        body {
            background: #fff !important;
        }

        .content-page,
        .content,
        .container-fluid,
        .followup-report-page {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .followup-report-card {
            border: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            overflow: visible !important;
        }

        .followup-report-header {
            padding: 0 0 10px !important;
            border-bottom: 1px solid #000 !important;
        }

        .followup-report-title {
            font-size: 18pt !important;
            text-align: center;
        }

        .followup-report-subtitle {
            display: none !important;
        }

        .followup-report-info {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 4px 10px !important;
            margin-top: 8px !important;
            padding-top: 8px !important;
            border-top: 0 !important;
        }

        .followup-report-info-item {
            display: block !important;
            font-size: 11pt !important;
        }

        .followup-report-info-label,
        .followup-report-info-value {
            display: inline !important;
        }

        .followup-report-body {
            padding: 10px 0 0 !important;
        }

        .followup-report-section-title {
            margin-bottom: 6px !important;
            font-size: 12pt !important;
        }

        .followup-report-table-wrap {
            overflow: visible !important;
        }

        .followup-report-table {
            display: table !important;
            width: 100% !important;
            min-width: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
        }

        .followup-report-table thead {
            display: table-header-group !important;
        }

        .followup-report-table tbody {
            display: table-row-group !important;
        }

        .followup-report-table tr {
            display: table-row !important;
            page-break-inside: avoid;
            border: 0 !important;
        }

        .followup-report-table th,
        .followup-report-table td {
            display: table-cell !important;
            padding: 5px 6px !important;
            border: 1px solid #000 !important;
            background: #fff !important;
            color: #000 !important;
            font-size: 10.5pt !important;
            line-height: 1.35 !important;
        }

        .followup-report-table td::before {
            display: none !important;
            content: none !important;
        }

        .followup-col-date {
            width: 16% !important;
        }

        .followup-col-detail {
            width: 54% !important;
        }

        .followup-col-note {
            width: 30% !important;
        }
    }
</style>

<div class="followup-report-page">
    <section class="followup-report-card">
        <div class="followup-report-toolbar">
            <div class="followup-report-toolbar-group">
                <a href="{{ route('followup.index', $client->id) }}" class="btn followup-report-btn-back">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>กลับหน้ารายการ</span>
                </a>
            </div>

            <div class="followup-report-toolbar-group">
                <button type="button" class="btn followup-report-btn-print" onclick="window.print();">
                    <i class="bi bi-printer"></i>
                    <span>พิมพ์รายงาน</span>
                </button>
            </div>
        </div>

        <header class="followup-report-header">
            <h1 class="followup-report-title">รายงานการช่วยเหลือและติดตามผล</h1>
            <p class="followup-report-subtitle">
                เอกสารสรุปข้อมูลการช่วยเหลือและติดตามผลของผู้รับบริการ
            </p>

            <div class="followup-report-info">
                <div class="followup-report-info-item">
                    <span class="followup-report-info-label">รหัสผู้รับบริการ:</span>
                    <span class="followup-report-info-value">{{ $client->id }}</span>
                </div>

                <div class="followup-report-info-item">
                    <span class="followup-report-info-label">ชื่อผู้รับบริการ:</span>
                    <span class="followup-report-info-value">{{ $client->fullname ?? $client->name ?? '-' }}</span>
                </div>

                <div class="followup-report-info-item">
                    <span class="followup-report-info-label">ช่วงวันที่:</span>
                    <span class="followup-report-info-value">{{ $dateRangeThai }}</span>
                </div>
            </div>
        </header>

        <div class="followup-report-body">
            <h2 class="followup-report-section-title">รายละเอียดรายการ</h2>

            @if($followups->count() > 0)
                <div class="followup-report-table-wrap">
                    <table class="followup-report-table">
                        <thead>
                            <tr>
                                <th class="followup-col-date">วันเดือนปี</th>
                                <th class="followup-col-detail">การช่วยเหลือและติดตามผล</th>
                                <th class="followup-col-note">หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($followups as $item)
                                @php
                                    $date = Carbon::parse($item->followup_date);
                                    $thaiDate = $date->day . ' ' . $thaiMonths[$date->month] . ' ' . ($date->year + 543);
                                @endphp

                                <tr>
                                    <td class="followup-col-date followup-date-cell" data-label="วันเดือนปี">{{ $thaiDate }}</td>
                                    <td class="followup-col-detail text-preline" data-label="การช่วยเหลือและติดตามผล">{{ $item->assistance_detail }}</td>
                                    <td class="followup-col-note text-preline" data-label="หมายเหตุ">{{ $item->note ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="followup-report-empty">
                    <h4>ยังไม่มีข้อมูลติดตามผล</h4>
                    <p>ไม่พบรายการสำหรับช่วงวันที่ที่เลือก</p>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
