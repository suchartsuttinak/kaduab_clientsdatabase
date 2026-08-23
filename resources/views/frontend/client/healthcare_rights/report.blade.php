@php
    use App\Helpers\ThaiDateHelper;

    /* HEALTHCARE_RIGHTS_REPORT_DESIGN_V1 */
    $clientFullName = trim((string) (
        $client->fullname
        ?? $client->full_name
        ?? (($client->first_name ?? '') . ' ' . ($client->last_name ?? ''))
    ));

    $clientFullName = $clientFullName !== '' ? $clientFullName : 'ผู้รับบริการ';
    $registerNumber = trim((string) ($client->register_number ?? ''));
    $ageDisplay = filled($client->age ?? null) ? ($client->age . ' ปี') : '-';
    $latestRight = $rights->first();
    $reportGeneratedAt = now('Asia/Bangkok');

    $statusClass = static function (?string $status): string {
        return match ($status) {
            'สิทธิบัตรทอง' => 'status-gold',
            'สิทธิคนพิการ' => 'status-disabled',
            'สิทธิประกันสังคม' => 'status-social',
            'สิทธิข้าราชการ' => 'status-civil',
            'ยังไม่ได้ขึ้นทะเบียนสิทธิ' => 'status-none',
            default => 'status-default',
        };
    };
@endphp
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานสิทธิรักษาพยาบาล - {{ $clientFullName }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 11mm 14mm;
        }

        * { box-sizing: border-box; }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            color: #1f2937;
            background: #eef2f7;
            font-family: "TH Sarabun New", "Sarabun", Tahoma, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .screen-shell {
            padding: 18px 14px 32px;
        }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            max-width: 960px;
            margin: 0 auto 12px;
        }

        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 8px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #fff;
            color: #334155;
            font: inherit;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(15, 23, 42, .04);
        }

        .btn:hover {
            border-color: #94a3b8;
            background: #f8fafc;
        }

        .btn-primary {
            border-color: #2563eb;
            background: #2563eb;
            color: #fff;
        }

        .btn-primary:hover {
            border-color: #1d4ed8;
            background: #1d4ed8;
        }

        .btn-icon {
            width: 17px;
            height: 17px;
            display: inline-block;
        }

        .paper {
            width: 100%;
            max-width: 960px;
            min-height: 270mm;
            margin: 0 auto;
            padding: 22px 26px 26px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 14px 36px rgba(15, 23, 42, .08);
        }

        .document-head {
            position: relative;
            padding: 3px 0 15px;
            border-bottom: 2px solid #1e3a5f;
            text-align: center;
        }

        .document-kicker {
            margin: 0 0 2px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .02em;
        }

        .document-title {
            margin: 0;
            color: #0f2744;
            font-size: 27px;
            font-weight: 800;
            line-height: 1.25;
        }

        .document-subtitle {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 14px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1.55fr .8fr .8fr;
            gap: 10px;
            margin-top: 15px;
        }

        .info-item {
            min-width: 0;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
        }

        .info-label {
            display: block;
            margin-bottom: 2px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .info-value {
            color: #172033;
            font-size: 15px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .current-section {
            margin-top: 12px;
            padding: 12px 14px;
            border: 1px solid #dbe4ef;
            border-left: 4px solid #315f91;
            border-radius: 10px;
            background: #fbfdff;
        }

        .current-title {
            margin: 0 0 7px;
            color: #334155;
            font-size: 13px;
            font-weight: 800;
        }

        .current-grid {
            display: grid;
            grid-template-columns: 1fr 1.7fr .8fr;
            gap: 14px;
        }

        .current-label {
            display: block;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .current-value {
            display: block;
            margin-top: 1px;
            color: #1e293b;
            font-size: 14px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .section-heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            margin: 18px 0 8px;
        }

        .section-title {
            margin: 0;
            color: #1e293b;
            font-size: 17px;
            font-weight: 800;
        }

        .record-count {
            color: #64748b;
            font-size: 13px;
            white-space: nowrap;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            padding: 8px 7px;
            border-right: 1px solid #d6dee8;
            border-bottom: 1px solid #d6dee8;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        th:last-child,
        td:last-child {
            border-right: 0;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        th {
            background: #eef3f8;
            color: #263a52;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.35;
            text-align: center;
        }

        td {
            color: #334155;
            font-size: 13.5px;
            line-height: 1.45;
        }

        tbody tr:nth-child(even) td {
            background: #fbfdff;
        }

        .col-no { width: 7%; }
        .col-date { width: 16%; }
        .col-status { width: 24%; }
        .col-hospital { width: 33%; }
        .col-recorder { width: 20%; }

        .center { text-align: center; }

        .status-badge {
            display: inline-block;
            max-width: 100%;
            padding: 3px 8px;
            border: 1px solid transparent;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.3;
            text-align: center;
        }

        .status-gold {
            border-color: #f4d57d;
            background: #fff8df;
            color: #7a5812;
        }

        .status-disabled {
            border-color: #b7d5f1;
            background: #edf6ff;
            color: #28577d;
        }

        .status-social {
            border-color: #b9dfcf;
            background: #eef9f4;
            color: #28624c;
        }

        .status-civil {
            border-color: #c9c6eb;
            background: #f4f2ff;
            color: #4f4a87;
        }

        .status-none {
            border-color: #d7dce3;
            background: #f4f5f7;
            color: #5f6875;
        }

        .status-default {
            border-color: #d7dce3;
            background: #f8fafc;
            color: #475569;
        }

        .empty-state {
            padding: 34px 16px;
            color: #64748b;
            font-size: 14px;
            text-align: center;
        }

        .report-footer {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
            margin-top: 26px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
        }

        .generated-info {
            color: #7c8798;
            font-size: 11.5px;
            line-height: 1.45;
        }

        .signature-box {
            width: 245px;
            text-align: center;
        }

        .signature-space {
            height: 38px;
            border-bottom: 1px solid #64748b;
        }

        .signature-label {
            margin-top: 5px;
            color: #475569;
            font-size: 13px;
        }

        @media (max-width: 760px) {
            .screen-shell { padding: 10px 0 24px; }
            .toolbar { padding: 0 10px; }
            .paper {
                min-height: 0;
                padding: 18px 14px 22px;
                border-right: 0;
                border-left: 0;
                border-radius: 0;
                box-shadow: none;
            }
            .info-grid,
            .current-grid {
                grid-template-columns: 1fr;
            }
            .document-title { font-size: 23px; }
            .table-wrap { border-radius: 0; }
            table { min-width: 760px; }
        }

        @media print {
            html,
            body {
                width: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                overflow: visible !important;
            }

            body {
                font-size: 14px;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .screen-shell {
                padding: 0 !important;
            }

            .toolbar {
                display: none !important;
            }

            .paper {
                width: 100% !important;
                max-width: none !important;
                min-height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                border: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .document-head {
                padding-bottom: 8px;
            }

            .document-kicker { font-size: 11px; }
            .document-title { font-size: 22px; }
            .document-subtitle { font-size: 11px; }

            .info-grid {
                gap: 6px;
                margin-top: 9px;
            }

            .info-item {
                padding: 5px 7px;
                border-radius: 0;
                background: #fff;
            }

            .info-label { font-size: 10px; }
            .info-value { font-size: 12px; }

            .current-section {
                margin-top: 7px;
                padding: 6px 8px;
                border-radius: 0;
                background: #fff;
            }

            .current-title { margin-bottom: 3px; font-size: 11px; }
            .current-grid { gap: 8px; }
            .current-label { font-size: 10px; }
            .current-value { font-size: 11px; }

            .section-heading {
                margin: 10px 0 4px;
            }

            .section-title { font-size: 13px; }
            .record-count { font-size: 10.5px; }

            .table-wrap {
                overflow: visible !important;
                border-radius: 0;
            }

            table {
                min-width: 0 !important;
                table-layout: fixed !important;
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
                padding: 4px 4px;
                font-size: 10.5px;
                line-height: 1.25;
            }

            th {
                background: #eef1f4 !important;
            }

            tbody tr:nth-child(even) td {
                background: #fff !important;
            }

            .status-badge {
                padding: 0;
                border: 0;
                border-radius: 0;
                background: transparent !important;
                color: #111827 !important;
                font-size: 10.5px;
                font-weight: 700;
            }

            .report-footer {
                margin-top: 16px;
                padding-top: 7px;
                page-break-inside: avoid;
            }

            .generated-info { font-size: 9.5px; }
            .signature-box { width: 210px; }
            .signature-space { height: 28px; }
            .signature-label { font-size: 10.5px; }
        }
    </style>
</head>
<body>
<div class="screen-shell">
    <div class="toolbar">
        <div class="toolbar-group">
            <button type="button" class="btn" onclick="window.close()" title="ปิดหน้ารายงาน">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                ปิดรายงาน
            </button>
        </div>

        <div class="toolbar-group">
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v7H6z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                พิมพ์รายงาน
            </button>
        </div>
    </div>

    <main class="paper">
        <header class="document-head">
            <p class="document-kicker">ข้อมูลด้านสุขภาพผู้รับบริการ</p>
            <h1 class="document-title">รายงานสิทธิรักษาพยาบาล</h1>
            <p class="document-subtitle">สรุปประวัติสิทธิและสถานพยาบาลที่เข้ารับการรักษาเบื้องต้น</p>
        </header>

        <section class="info-grid" aria-label="ข้อมูลผู้รับบริการ">
            <div class="info-item">
                <span class="info-label">ชื่อ - สกุล ผู้รับบริการ</span>
                <span class="info-value">{{ $clientFullName }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">เลขทะเบียน</span>
                <span class="info-value">{{ $registerNumber !== '' ? $registerNumber : '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">อายุ</span>
                <span class="info-value">{{ $ageDisplay }}</span>
            </div>
        </section>

        @if($latestRight)
            <section class="current-section" aria-label="สิทธิรักษาพยาบาลล่าสุด">
                <p class="current-title">สิทธิรักษาพยาบาลล่าสุด</p>
                <div class="current-grid">
                    <div>
                        <span class="current-label">สถานะสิทธิ</span>
                        <span class="current-value">{{ $latestRight->coverage_status ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="current-label">สถานพยาบาลที่เข้ารับการรักษาเบื้องต้น</span>
                        <span class="current-value">{{ $latestRight->primary_hospital ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="current-label">วันที่บันทึกล่าสุด</span>
                        <span class="current-value">{{ ThaiDateHelper::formatThaiShort($latestRight->record_date) }}</span>
                    </div>
                </div>
            </section>
        @endif

        <div class="section-heading">
            <h2 class="section-title">ประวัติการบันทึกสิทธิรักษาพยาบาล</h2>
            <div class="record-count">จำนวน {{ number_format($rights->count()) }} รายการ</div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="col-no">ลำดับ</th>
                        <th class="col-date">วันที่บันทึก</th>
                        <th class="col-status">สถานะสิทธิ</th>
                        <th class="col-hospital">สถานพยาบาลที่เข้ารับการรักษาเบื้องต้น</th>
                        <th class="col-recorder">ผู้บันทึกข้อมูล</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rights as $index => $right)
                        <tr>
                            <td class="center">{{ $index + 1 }}</td>
                            <td class="center">{{ ThaiDateHelper::formatThaiShort($right->record_date) }}</td>
                            <td class="center">
                                <span class="status-badge {{ $statusClass($right->coverage_status) }}">
                                    {{ $right->coverage_status ?: '-' }}
                                </span>
                            </td>
                            <td>{{ $right->primary_hospital ?: '-' }}</td>
                            <td>{{ $right->recorder_name ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">ยังไม่มีข้อมูลสิทธิรักษาพยาบาลของผู้รับบริการรายนี้</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <footer class="report-footer">
            <div class="generated-info">
                จัดทำรายงานจากระบบข้อมูลผู้รับบริการ<br>
                วันที่จัดทำ {{ $reportGeneratedAt->locale('th')->translatedFormat('j F') }} {{ $reportGeneratedAt->year + 543 }}
                เวลา {{ $reportGeneratedAt->format('H:i') }} น.
            </div>

            <div class="signature-box">
                <div class="signature-space"></div>
                <div class="signature-label">ผู้จัดทำรายงาน</div>
            </div>
        </footer>
    </main>
</div>
</body>
</html>