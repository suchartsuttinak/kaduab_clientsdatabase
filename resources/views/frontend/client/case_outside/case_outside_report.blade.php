<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานติดตามเด็กที่พักอาศัยภายนอก</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 11mm 12mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            background: #f4f7fb;
            color: #111827;
            font-family: "TH Sarabun New", "Sarabun", sans-serif;
            font-size: 17px;
            line-height: 1.35;
        }

        .report-page {
            width: min(96vw, 1450px);
            margin: 22px auto;
            padding: 24px 26px;
            border: 1px solid #dbe3ef;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 12px 34px rgba(15, 23, 42, .08);
        }

        .report-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .report-toolbar-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .report-button {
            display: inline-flex;
            min-height: 42px;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 8px 14px;
            border: 1px solid transparent;
            border-radius: 12px;
            text-decoration: none;
            font-family: inherit;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }

        .report-button-back {
            border-color: #cbd5e1;
            background: #fff;
            color: #334155;
        }

        .report-button-print {
            border-color: #15803d;
            background: #16a34a;
            color: #fff;
        }

        .report-header {
            margin-bottom: 13px;
            padding-bottom: 10px;
            border-bottom: 1px solid #cbd5e1;
            text-align: center;
        }

        .report-title {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            line-height: 1.2;
        }

        .report-subtitle {
            margin: 4px 0 0;
            color: #334155;
            font-size: 19px;
            font-weight: 700;
        }

        .report-meta {
            display: flex;
            justify-content: center;
            gap: 8px 18px;
            margin-top: 5px;
            color: #475569;
            font-size: 15px;
            flex-wrap: wrap;
        }

        .report-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #475569;
            padding: 6px 7px;
            vertical-align: top;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .report-table th {
            background: #f1f5f9;
            color: #0f172a;
            font-size: 15px;
            font-weight: 800;
            line-height: 1.25;
            text-align: center;
        }

        .report-table td {
            color: #1f2937;
            font-size: 15px;
            line-height: 1.3;
        }

        .text-center {
            text-align: center;
        }

        .report-empty {
            padding: 28px 18px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            color: #475569;
            text-align: center;
            font-size: 18px;
        }

        .report-footer {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 10px;
            color: #475569;
            font-size: 14px;
        }

        .report-footer-right {
            text-align: right;
        }

        @media (max-width: 900px) {
            .report-page {
                width: calc(100vw - 20px);
                margin: 10px auto;
                padding: 16px;
                border-radius: 14px;
            }

            .report-button {
                flex: 1 1 auto;
            }

            .report-table {
                min-width: 1150px;
            }
        }

        @media print {
            html,
            body {
                width: 100%;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            body {
                font-size: 15px;
                line-height: 1.25;
            }

            .report-page {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
                border: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .report-toolbar {
                display: none !important;
            }

            .report-header {
                margin-bottom: 9px;
                padding-bottom: 7px;
            }

            .report-title {
                font-size: 23px;
            }

            .report-subtitle {
                font-size: 17px;
            }

            .report-meta {
                font-size: 13px;
            }

            .report-table-wrap {
                overflow: visible !important;
            }

            .report-table {
                width: 100% !important;
                min-width: 0 !important;
            }

            .report-table thead {
                display: table-header-group;
            }

            .report-table tfoot {
                display: table-footer-group;
            }

            .report-table tr {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .report-table th,
            .report-table td {
                padding: 4.5px 5px;
            }

            .report-table th {
                font-size: 13.5px;
            }

            .report-table td {
                font-size: 13.5px;
                line-height: 1.2;
            }

            .report-footer {
                font-size: 12px;
            }

            .report-empty {
                border-radius: 0;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
@php
    $now = now('Asia/Bangkok');
    $printDateThai = $now->format('d/m/') . ($now->year + 543);

    $formatThaiDate = static function ($value) {
        if (blank($value)) {
            return '-';
        }

        $date = \Carbon\Carbon::parse($value);

        return $date->format('d/m/') . ($date->year + 543);
    };

    $filterParts = [];

    if (!empty($filters['date_start'])) {
        $filterParts[] = 'ตั้งแต่ ' . $formatThaiDate($filters['date_start']);
    }

    if (!empty($filters['date_end'])) {
        $filterParts[] = 'ถึง ' . $formatThaiDate($filters['date_end']);
    }

    if (!empty($filters['outside_id']) && filled($filterOutsideName)) {
        $filterParts[] = 'สาเหตุ: ' . $filterOutsideName;
    }

    if (!empty($filters['follo_no'])) {
        $filterParts[] = 'การดำเนินงาน: ' . $filters['follo_no'];
    }
@endphp

<div class="report-page">
    <div class="report-toolbar">
        <div class="report-toolbar-group">
            <a href="{{ route('case_outside.show', $client->id) }}"
               class="report-button report-button-back">
                <span aria-hidden="true">←</span>
                <span>กลับหน้าหลัก</span>
            </a>
        </div>

        <div class="report-toolbar-group">
            <button type="button"
                    onclick="window.print()"
                    class="report-button report-button-print">
                <span aria-hidden="true">🖨️</span>
                <span>พิมพ์รายงาน</span>
            </button>
        </div>
    </div>

    <header class="report-header">
        <h1 class="report-title">
            รายงานติดตามเด็กที่พักอาศัยภายนอก
        </h1>

        <p class="report-subtitle">
            ผู้รับบริการ: {{ $client->fullname ?? $client->name ?? ('ID ' . $client->id) }}
        </p>

        <div class="report-meta">
            <span>จำนวน {{ number_format($caseoutsides->count()) }} รายการ</span>

            @if($filterParts)
                <span>{{ implode(' | ', $filterParts) }}</span>
            @endif
        </div>
    </header>

    @if($caseoutsides->isNotEmpty())
        <div class="report-table-wrap">
            <table class="report-table">
                <colgroup>
                    <col style="width: 5%;">
                    <col style="width: 9%;">
                    <col style="width: 14%;">
                    <col style="width: 13%;">
                    <col style="width: 10%;">
                    <col style="width: 22%;">
                    <col style="width: 10%;">
                    <col style="width: 17%;">
                </colgroup>

                <thead>
                    <tr>
                        <th>ครั้งที่</th>
                        <th>วันที่ติดตาม</th>
                        <th>สาเหตุที่พักภายนอก</th>
                        <th>สถานที่พัก</th>
                        <th>การดำเนินงาน</th>
                        <th>ผลการติดตาม</th>
                        <th>ผู้ติดตาม</th>
                        <th>หมายเหตุ</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($caseoutsides as $case)
                        <tr>
                            <td class="text-center">
                                {{ $case->count ?? $loop->iteration }}
                            </td>
                            <td class="text-center">
                                {{ $formatThaiDate($case->date) }}
                            </td>
                            <td>
                                {{ $case->outside->outside_name ?? '-' }}
                            </td>
                            <td>
                                {{ $case->dormitory ?: '-' }}
                            </td>
                            <td class="text-center">
                                {{ $case->follo_no ?: '-' }}
                            </td>
                            <td>
                                {{ $case->results ?: '-' }}
                            </td>
                            <td>
                                {{ $case->teacher ?: '-' }}
                            </td>
                            <td>
                                {{ $case->remerk ?: '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="report-empty">
            ไม่พบข้อมูลตามเงื่อนไขที่กำหนด
        </div>
    @endif

    <footer class="report-footer">
        <div>
            รายงานติดตามเด็กที่พักอาศัยภายนอก
        </div>
        <div class="report-footer-right">
            วันที่พิมพ์ {{ $printDateThai }}
        </div>
    </footer>
</div>
</body>
</html>
