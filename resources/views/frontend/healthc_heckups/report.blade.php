<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานการตรวจสุขภาพประจำปี</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #eef3f8;
            color: #1f2937;
            font-family: "TH Sarabun New", "Sarabun", sans-serif;
            font-size: 15px;
            line-height: 1.35;
        }

        .report-page {
            max-width: 1366px;
            margin: 16px auto;
            padding: 20px 22px;
            border: 1px solid #dbe3ec;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
        }

        .report-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .report-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            min-height: 38px;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 7px 13px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: #fff;
            color: #334155;
            font: inherit;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-print {
            border-color: #2563eb;
            background: #2563eb;
            color: #fff;
        }

        .report-heading {
            text-align: center;
            margin-bottom: 12px;
        }

        .report-title {
            margin: 0;
            color: #0f172a;
            font-size: 24px;
            font-weight: 800;
            line-height: 1.25;
        }

        .report-subtitle {
            margin: 3px 0 0;
            color: #64748b;
            font-size: 14px;
        }

        .report-filter-summary {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px 14px;
            margin: 8px 0 14px;
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            background: #f8fafc;
            color: #475569;
            font-size: 13px;
        }

        .summary-item strong {
            color: #1e293b;
        }

        .report-table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        th,
        td {
            padding: 6px 7px;
            border: 1px solid #cfd8e3;
            text-align: left;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        thead th {
            background: #f1f5f9;
            color: #0f172a;
            font-size: 13px;
            font-weight: 800;
            text-align: center;
            vertical-align: middle;
        }

        tbody td {
            color: #1f2937;
            font-size: 13px;
        }

        tbody tr:nth-child(even) {
            background: #fbfdff;
        }

        .col-index {
            width: 46px;
            text-align: center;
        }

        .col-name {
            width: 155px;
        }

        .col-date {
            width: 92px;
            text-align: center;
            white-space: nowrap;
        }

        .col-hospital {
            width: 165px;
        }

        .col-result {
            width: 88px;
            text-align: center;
        }

        .col-recorder {
            width: 115px;
        }

        .badge {
            display: inline-block;
            min-width: 58px;
            padding: 2px 8px;
            border: 1px solid transparent;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.35;
            text-align: center;
            white-space: nowrap;
        }

        .badge-normal {
            border-color: #bbf7d0;
            background: #f0fdf4;
            color: #166534;
        }

        .badge-abnormal {
            border-color: #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }

        .empty-row {
            padding: 18px 8px !important;
            color: #64748b !important;
            text-align: center !important;
        }

        .report-footer {
            margin-top: 9px;
            color: #64748b;
            font-size: 12px;
            text-align: right;
        }

        @media screen and (max-width: 768px) {
            .report-page {
                margin: 8px;
                padding: 14px;
                border-radius: 11px;
            }

            .report-title {
                font-size: 20px;
            }

            .report-actions,
            .report-actions .btn {
                width: 100%;
            }
        }

        @media print {
            body {
                background: #fff;
                font-size: 12px;
            }

            .report-page {
                max-width: none;
                margin: 0;
                padding: 0;
                border: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .report-toolbar {
                display: none !important;
            }

            .report-title {
                font-size: 20px;
            }

            .report-subtitle,
            .report-filter-summary {
                font-size: 11px;
            }

            .report-filter-summary {
                margin-bottom: 9px;
                padding: 5px 7px;
            }

            th,
            td {
                padding: 4px 5px;
                border-color: #9ca3af;
                font-size: 10.5px;
                line-height: 1.25;
            }

            thead th {
                background: #f1f5f9 !important;
                font-size: 10.5px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge {
                padding: 1px 5px;
                font-size: 9.5px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .report-footer {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
@php
    $clientName = static function ($client): string {
        if (!$client) {
            return '-';
        }

        $name = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));

        $preferred = trim((string) ($client->fullname ?? $client->full_name ?? ''));

        return $preferred !== ''
            ? $preferred
            : ($name !== '' ? $name : '-');
    };

    $dateFrom = $filters['date_from'] ?? null;
    $dateTo = $filters['date_to'] ?? null;
    $resultFilter = $filters['checkup_result'] ?? null;
    $keywordFilter = $filters['keyword'] ?? null;

    $thaiDate = static function ($date): string {
        return $date
            ? \App\Helpers\ThaiDateHelper::formatThaiShort($date)
            : '-';
    };
@endphp

<div class="report-page">
    <div class="report-toolbar">
        <a href="{{ route('healthc_heckups.index', request()->query()) }}" class="btn">
            ← กลับหน้าหลัก
        </a>

        <div class="report-actions">
            <button type="button" class="btn btn-print" onclick="window.print()">
                🖨 พิมพ์รายงาน
            </button>
        </div>
    </div>

    <header class="report-heading">
        <h1 class="report-title">รายงานการตรวจสุขภาพประจำปี</h1>
        <p class="report-subtitle">สรุปข้อมูลการตรวจสุขภาพของผู้รับบริการตามสิทธิ์การเข้าถึง</p>
    </header>

    @if($selectedClient || $dateFrom || $dateTo || $resultFilter || $keywordFilter)
        <div class="report-filter-summary">
            @if($selectedClient)
                <span class="summary-item">
                    ผู้รับบริการ: <strong>{{ $clientName($selectedClient) }}</strong>
                </span>
            @endif

            @if($dateFrom || $dateTo)
                <span class="summary-item">
                    ช่วงวันที่:
                    <strong>{{ $dateFrom ? $thaiDate($dateFrom) : 'เริ่มต้น' }}</strong>
                    ถึง
                    <strong>{{ $dateTo ? $thaiDate($dateTo) : 'ปัจจุบัน' }}</strong>
                </span>
            @endif

            @if($resultFilter)
                <span class="summary-item">
                    ผลการตรวจ:
                    <strong>{{ $resultFilter === 'abnormal' ? 'ไม่ปกติ' : 'ปกติ' }}</strong>
                </span>
            @endif

            @if($keywordFilter)
                <span class="summary-item">
                    คำค้นหา: <strong>{{ $keywordFilter }}</strong>
                </span>
            @endif
        </div>
    @endif

    <div class="report-table-wrap">
        <table>
            <thead>
                <tr>
                    <th class="col-index">ลำดับ</th>
                    <th class="col-name">ชื่อ-สกุล</th>
                    <th class="col-date">วันที่ตรวจ</th>
                    <th class="col-hospital">สถานพยาบาล</th>
                    <th class="col-result">ผลการตรวจ</th>
                    <th>รายละเอียดผลตรวจที่ผิดปกติ</th>
                    <th class="col-recorder">ผู้บันทึก</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    <tr>
                        <td class="col-index">{{ $index + 1 }}</td>
                        <td class="col-name">{{ $clientName($item->client) }}</td>
                        <td class="col-date">{{ $thaiDate($item->checkup_date) }}</td>
                        <td class="col-hospital">{{ $item->hospital_name ?: '-' }}</td>
                        <td class="col-result">
                            @if($item->checkup_result === 'normal')
                                <span class="badge badge-normal">ปกติ</span>
                            @else
                                <span class="badge badge-abnormal">ไม่ปกติ</span>
                            @endif
                        </td>
                        <td>{{ $item->abnormal_detail ?: '-' }}</td>
                        <td class="col-recorder">{{ $item->recorder->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-row">ไม่พบข้อมูลตามเงื่อนไขที่เลือก</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="report-footer">
        จำนวนทั้งหมด {{ number_format($items->count()) }} รายการ
        | พิมพ์เมื่อ {{ now('Asia/Bangkok')->format('d/m/') }}{{ now('Asia/Bangkok')->year + 543 }}
    </div>
</div>
</body>
</html>
