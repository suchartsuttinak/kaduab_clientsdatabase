@extends('admin_client.admin_client')

@section('content')

@php
    use Carbon\Carbon;

    $thaiDate = function ($date) {
        if (!$date) {
            return '-';
        }

        try {
            $d = Carbon::parse($date);

            $months = [
                1  => 'ม.ค.',
                2  => 'ก.พ.',
                3  => 'มี.ค.',
                4  => 'เม.ย.',
                5  => 'พ.ค.',
                6  => 'มิ.ย.',
                7  => 'ก.ค.',
                8  => 'ส.ค.',
                9  => 'ก.ย.',
                10 => 'ต.ค.',
                11 => 'พ.ย.',
                12 => 'ธ.ค.',
            ];

            return $d->day . ' ' . $months[(int) $d->month] . ' ' . ($d->year + 543);
        } catch (\Throwable $e) {
            return $date;
        }
    };

    $fromDate = request('from');
    $toDate   = request('to');

    $clientName  = $client->fullname ?? $client->full_name ?? '-';
    $sessionCount = $sessions->count();
    $itemCount    = $sessions->sum(fn ($session) => $session->items->count());
@endphp

<style>
    @page {
        size: A4 landscape;
        margin: 6mm 10mm 8mm;
    }

    .help-report-page {
        padding: 12px 0 24px;
        color: #0f172a;
        background: #fff;
    }

    .help-report-box {
        width: 100%;
        margin: 0;
        padding: 0;
        background: #fff;
        border: 0;
        border-radius: 0;
        box-shadow: none;
    }

    .help-report-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin: 0 0 10px;
        padding: 0 0 7px;
        border-bottom: 1px solid #cbd5e1;
    }

    .help-report-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.25rem;
        font-weight: 800;
        line-height: 1.3;
    }

    .help-report-subtitle {
        margin: 2px 0 0;
        color: #64748b;
        font-size: .9rem;
        line-height: 1.4;
    }

    .help-report-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    .help-report-actions .btn {
        min-height: 40px;
        padding: .55rem .9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border-radius: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    /*
     * แสดงข้อมูลเป็นข้อความธรรมดา ไม่ใช้บ็อกซ์
     */
    .help-report-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px 24px;
        margin: 0 0 9px;
        padding: 0 2px 7px;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
        font-size: .94rem;
        line-height: 1.5;
    }

    .help-report-meta-item {
        display: inline-flex;
        align-items: baseline;
        gap: 5px;
    }

    .help-report-meta-label {
        color: #64748b;
        font-weight: 600;
    }

    .help-report-meta-value {
        color: #0f172a;
        font-weight: 800;
    }

    .help-report-table-wrap {
        width: 100%;
        overflow-x: auto;
        background: #fff;
    }

    .help-report-table {
        width: 100%;
        min-width: 760px;
        margin: 0;
        border-collapse: collapse;
        table-layout: fixed;
        background: #fff;
    }

    .help-report-table th,
    .help-report-table td {
        border: 1px solid #111827;
        padding: 7px;
        color: #111827;
        font-size: .9rem;
        line-height: 1.4;
        vertical-align: middle;
    }

    .help-report-table thead th {
        background: #f1f5f9;
        font-weight: 800;
        text-align: center;
    }

    .help-col-date {
        width: 18%;
        text-align: center;
        white-space: nowrap;
    }

    .help-col-item {
        width: 34%;
        text-align: left;
        overflow-wrap: anywhere;
    }

    .help-col-qty {
        width: 12%;
        text-align: center;
        white-space: nowrap;
    }

    .help-col-unit,
    .help-col-total {
        width: 18%;
        text-align: right;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    .help-report-table tfoot th {
        background: #f8fafc;
        font-weight: 800;
    }

    .help-report-empty {
        padding: 18px 12px;
        color: #64748b;
        text-align: center;
    }

    .help-report-sign {
        display: flex;
        justify-content: flex-end;
        margin-top: 18px;
        page-break-inside: avoid;
        break-inside: avoid;
    }

    .help-report-sign-box {
        width: 240px;
        max-width: 100%;
        color: #334155;
        text-align: center;
        font-size: .9rem;
    }

    .help-report-sign-line {
        margin-top: 38px;
        padding-top: 4px;
        border-top: 1px solid #111827;
    }

    @media (max-width: 767.98px) {
        .help-report-header {
            flex-direction: column;
        }

        .help-report-actions {
            width: 100%;
        }

        .help-report-actions > * {
            flex: 1 1 calc(50% - 4px);
        }
    }

    @media print {
        html,
        body {
            width: 297mm !important;
            min-height: 0 !important;
            height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
            background: #fff !important;
            font-family: "TH Sarabun New", "Sarabun", sans-serif !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .navbar-custom,
        .leftside-menu,
        .page-title-box,
        .footer,
        .help-report-actions,
        header,
        footer {
            display: none !important;
        }

        /*
         * ล้างระยะและ min-height จาก Layout หลัก
         * เพื่อยกหัวรายงานขึ้นและป้องกันหน้าว่างแผ่นที่สอง
         */
        .wrapper,
        .content-page,
        .content,
        main,
        .container,
        .container-fluid,
        .help-report-page,
        .help-report-box {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            min-height: 0 !important;
            height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
            background: #fff !important;
            border: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        .content-page {
            margin-left: 0 !important;
        }

        .help-report-header {
            margin: 0 0 5px !important;
            padding: 0 0 4px !important;
            border-bottom: 1px solid #94a3b8 !important;
        }

        .help-report-title {
            margin: 0 !important;
            font-size: 20px !important;
            font-weight: 900 !important;
            line-height: 1.08 !important;
        }

        .help-report-subtitle {
            margin: 1px 0 0 !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            line-height: 1.1 !important;
        }

        .help-report-meta {
            gap: 2px 20px !important;
            margin: 0 0 5px !important;
            padding: 0 2px 4px !important;
            font-size: 12px !important;
            line-height: 1.15 !important;
            border-bottom: 1px solid #dbe4f0 !important;
        }

        .help-report-meta-label {
            color: #475569 !important;
            font-weight: 700 !important;
        }

        .help-report-meta-value {
            color: #0f172a !important;
            font-weight: 900 !important;
        }

        .help-report-table-wrap {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
        }

        .help-report-table {
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            margin: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            page-break-after: auto !important;
        }

        .help-report-table thead {
            display: table-header-group !important;
        }

        .help-report-table tfoot {
            display: table-row-group !important;
        }

        .help-report-table tr {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .help-report-table th,
        .help-report-table td {
            padding: 3px 4px !important;
            border: 1px solid #111827 !important;
            color: #111827 !important;
            font-size: 10px !important;
            line-height: 1.12 !important;
        }

        .help-report-table thead th {
            background: #f1f5f9 !important;
            font-weight: 900 !important;
            text-align: center !important;
        }

        .help-report-table tfoot th {
            background: #f8fafc !important;
            font-weight: 900 !important;
        }

        .help-report-sign {
            margin-top: 10px !important;
            page-break-before: avoid !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .help-report-sign-box {
            width: 220px !important;
            font-size: 11px !important;
        }

        .help-report-sign-line {
            margin-top: 30px !important;
            padding-top: 3px !important;
        }
    }
</style>

<div class="container-fluid help-report-page">
    <div class="help-report-box">

        <div class="help-report-header">
            <div>
                <h1 class="help-report-title">
                    รายงานการช่วยเหลือผู้รับบริการตามช่วงวันที่
                </h1>

                <p class="help-report-subtitle">
                    @if($fromDate && $toDate)
                        ช่วงวันที่ {{ $thaiDate($fromDate) }} ถึง {{ $thaiDate($toDate) }}
                    @elseif($fromDate)
                        ตั้งแต่วันที่ {{ $thaiDate($fromDate) }}
                    @elseif($toDate)
                        ถึงวันที่ {{ $thaiDate($toDate) }}
                    @else
                        แสดงข้อมูลทั้งหมด
                    @endif
                </p>
            </div>

            <div class="help-report-actions">
                <a href="{{ route('help_sessions.show', [
                    'client' => $client->id,
                    'from'   => $fromDate,
                    'to'     => $toDate,
                ]) }}"
                   class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>กลับหน้ารายการ</span>
                </a>

                <button type="button"
                        onclick="window.print()"
                        class="btn btn-primary">
                    <i class="bi bi-printer"></i>
                    <span>พิมพ์รายงาน</span>
                </button>
            </div>
        </div>

        <div class="help-report-meta">
            <div class="help-report-meta-item">
                <span class="help-report-meta-label">ชื่อผู้รับบริการ:</span>
                <span class="help-report-meta-value">{{ $clientName }}</span>
            </div>

            <div class="help-report-meta-item">
                <span class="help-report-meta-label">จำนวนครั้งที่ช่วยเหลือ:</span>
                <span class="help-report-meta-value">
                    {{ number_format($sessionCount) }} ครั้ง
                </span>
            </div>

            <div class="help-report-meta-item">
                <span class="help-report-meta-label">จำนวนรายการทั้งหมด:</span>
                <span class="help-report-meta-value">
                    {{ number_format($itemCount) }} รายการ
                </span>
            </div>
        </div>

        <div class="help-report-table-wrap">
            <table class="help-report-table">
                <thead>
                    <tr>
                        <th class="help-col-date">วันที่</th>
                        <th class="help-col-item">รายการ</th>
                        <th class="help-col-qty">จำนวน</th>
                        <th class="help-col-unit">ราคา/หน่วย (บาท)</th>
                        <th class="help-col-total">ราคารวม (บาท)</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $hasRows = false;
                    @endphp

                    @foreach($sessions as $session)
                        @foreach($session->items as $item)
                            @php
                                $hasRows = true;
                                $rowTotal = (float) $item->quantity * (float) $item->unit_price;
                            @endphp

                            <tr>
                                <td class="help-col-date">
                                    {{ $thaiDate($session->help_date) }}
                                </td>

                                <td class="help-col-item">
                                    {{ $item->item_name ?? '-' }}
                                </td>

                                <td class="help-col-qty">
                                    {{ number_format($item->quantity ?? 0) }}
                                </td>

                                <td class="help-col-unit">
                                    {{ number_format($item->unit_price ?? 0, 2) }}
                                </td>

                                <td class="help-col-total">
                                    {{ number_format($rowTotal, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach

                    @if(!$hasRows)
                        <tr>
                            <td colspan="5" class="help-report-empty">
                                ยังไม่พบข้อมูลการช่วยเหลือในช่วงวันที่ที่เลือก
                            </td>
                        </tr>
                    @endif
                </tbody>

                @if($hasRows)
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">
                                รวมทั้งสิ้น
                            </th>

                            <th class="help-col-total">
                                {{ number_format($grandTotal, 2) }} บาท
                            </th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <div class="help-report-sign">
            <div class="help-report-sign-box">
                <div>ผู้จัดทำรายงาน</div>

                <div class="help-report-sign-line">
                    (.............................................)
                </div>
            </div>
        </div>

    </div>
</div>

@endsection