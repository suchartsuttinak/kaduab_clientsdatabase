<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานบันทึกการบาดเจ็บ</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 14mm 14mm 15mm;
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
            background: #eef2f7;
            color: #111827;
            font-family: "TH Sarabun New", "Sarabun", Arial, sans-serif;
            font-size: 17px;
            line-height: 1.45;
        }

        .acc-report-shell {
            width: min(100% - 32px, 900px);
            margin: 24px auto;
        }

        .acc-report-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .acc-report-toolbar-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .acc-report-btn {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 8px 14px;
            border: 1px solid transparent;
            border-radius: 10px;
            font: inherit;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.2;
            text-decoration: none;
            cursor: pointer;
            transition: background-color .18s ease, border-color .18s ease, transform .18s ease;
        }

        .acc-report-btn:hover {
            transform: translateY(-1px);
        }

        .acc-report-btn-secondary {
            border-color: #cbd5e1;
            background: #ffffff;
            color: #334155;
        }

        .acc-report-btn-secondary:hover {
            border-color: #94a3b8;
            background: #f8fafc;
            color: #0f172a;
        }

        .acc-report-btn-primary {
            border-color: #1d4ed8;
            background: #1d4ed8;
            color: #ffffff;
        }

        .acc-report-btn-primary:hover {
            border-color: #1e3a8a;
            background: #1e3a8a;
            color: #ffffff;
        }

        .acc-report-page {
            overflow: hidden;
            padding: 28px 30px 30px;
            border: 1px solid #dbe3ec;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 16px 40px rgba(15, 23, 42, .08);
        }

        .acc-report-header {
            padding-bottom: 14px;
            border-bottom: 2px solid #1f2937;
            text-align: center;
        }

        .acc-report-kicker {
            margin: 0 0 2px;
            color: #475569;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: .02em;
        }

        .acc-report-title {
            margin: 0;
            color: #111827;
            font-size: 25px;
            font-weight: 800;
            line-height: 1.25;
        }

        .acc-report-subtitle {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 16px;
        }

        .acc-report-client {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 10px 24px;
            padding: 14px 0;
            border-bottom: 1px solid #cbd5e1;
        }

        .acc-report-client-item {
            min-width: 0;
        }

        .acc-report-label {
            color: #111827;
            font-weight: 700;
        }

        .acc-report-section {
            margin-top: 16px;
        }

        .acc-report-section-title {
            margin: 0 0 7px;
            color: #111827;
            font-size: 18px;
            font-weight: 800;
        }

        .acc-report-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .acc-report-table th,
        .acc-report-table td {
            padding: 8px 10px;
            border: 1px solid #9ca3af;
            vertical-align: top;
        }

        .acc-report-table th {
            width: 27%;
            background: #f8fafc;
            color: #111827;
            font-weight: 700;
            text-align: left;
        }

        .acc-report-table td {
            color: #1f2937;
            overflow-wrap: anywhere;
            white-space: pre-line;
        }

        .acc-report-status {
            display: inline-block;
            padding: 2px 9px;
            border: 1px solid #9ca3af;
            border-radius: 999px;
            font-size: 15px;
            font-weight: 700;
        }

        .acc-report-status-doctor {
            border-color: #86efac;
            background: #f0fdf4;
            color: #166534;
        }

        .acc-report-status-home {
            border-color: #cbd5e1;
            background: #f8fafc;
            color: #475569;
        }

        .acc-report-footer-note {
            margin-top: 12px;
            color: #64748b;
            font-size: 14px;
            text-align: right;
        }

        @media (max-width: 767.98px) {
            body {
                background: #ffffff;
            }

            .acc-report-shell {
                width: 100%;
                margin: 0;
            }

            .acc-report-toolbar {
                padding: 10px;
                border-bottom: 1px solid #e5e7eb;
                background: #ffffff;
            }

            .acc-report-toolbar-group {
                flex: 1 1 auto;
            }

            .acc-report-btn {
                flex: 1 1 auto;
            }

            .acc-report-page {
                padding: 18px 14px 24px;
                border: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .acc-report-client {
                grid-template-columns: 1fr;
                gap: 5px;
            }

            .acc-report-table {
                table-layout: auto;
            }

            .acc-report-table th {
                width: 36%;
            }
        }

        @media print {
            body {
                background: #ffffff;
                color: #000000;
                font-size: 16px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .acc-report-shell {
                width: 100%;
                margin: 0;
            }

            .acc-report-toolbar {
                display: none !important;
            }

            .acc-report-page {
                overflow: visible;
                padding: 0;
                border: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .acc-report-header {
                border-bottom-color: #000000;
            }

            .acc-report-client {
                border-bottom-color: #000000;
            }

            .acc-report-table th,
            .acc-report-table td {
                border-color: #000000;
                padding: 6px 8px;
            }

            .acc-report-table th {
                background: #ffffff;
            }

            .acc-report-status {
                border-color: #000000;
                background: #ffffff;
                color: #000000;
            }

            .acc-report-section,
            .acc-report-table tr {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
@php
    $clientName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));

    if ($clientName === '') {
        $clientName = $client->name ?? $client->fullname ?? '-';
    }

    $isDoctorVisit = ($accident->treat_no ?? '') === 'พบแพทย์';
    $clientAge = filled($client->age ?? null) ? $client->age . ' ปี' : '-';
@endphp

<div class="acc-report-shell">
    <div class="acc-report-toolbar">
        <div class="acc-report-toolbar-group">
            <button type="button"
                    class="acc-report-btn acc-report-btn-secondary"
                    onclick="history.length > 1 ? history.back() : window.location.href='{{ route('accident.add', $client->id) }}'">
                <span aria-hidden="true">←</span>
                <span>กลับหน้าก่อน</span>
            </button>
        </div>

        <div class="acc-report-toolbar-group">
            <button type="button"
                    class="acc-report-btn acc-report-btn-primary"
                    onclick="window.print()">
                <span aria-hidden="true">▣</span>
                <span>พิมพ์รายงาน</span>
            </button>
        </div>
    </div>

    <main class="acc-report-page">
        <header class="acc-report-header">
            <p class="acc-report-kicker">แบบบันทึกข้อมูลผู้รับบริการ</p>
            <h1 class="acc-report-title">รายงานบันทึกการบาดเจ็บ</h1>
            <p class="acc-report-subtitle">รายละเอียดเหตุการณ์ การรักษา และการป้องกัน</p>
        </header>

        <section class="acc-report-client" aria-label="ข้อมูลผู้รับบริการ">
            <div class="acc-report-client-item">
                <span class="acc-report-label">ชื่อผู้รับบริการ:</span>
                <span>{{ $clientName }}</span>
            </div>

            <div class="acc-report-client-item">
                <span class="acc-report-label">อายุ:</span>
                <span>{{ $clientAge }}</span>
            </div>

            @if(!empty($client->cid))
                <div class="acc-report-client-item">
                    <span class="acc-report-label">เลขประจำตัวประชาชน:</span>
                    <span>{{ $client->cid }}</span>
                </div>
            @endif
        </section>

        <section class="acc-report-section" aria-labelledby="accidentEventReportTitle">
            <h2 class="acc-report-section-title" id="accidentEventReportTitle">1. ข้อมูลเหตุการณ์</h2>

            <table class="acc-report-table">
                <tbody>
                    <tr>
                        <th>วันที่เกิดเหตุ</th>
                        <td>{{ \App\Helpers\ThaiDateHelper::formatThaiDate($accident->incident_date) }}</td>
                    </tr>
                    <tr>
                        <th>สถานที่เกิดเหตุ</th>
                        <td>{{ $accident->location ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>ผู้พบเห็นเหตุการณ์</th>
                        <td>{{ $accident->eyewitness ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>รายละเอียดการบาดเจ็บ</th>
                        <td>{{ $accident->detail ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>สาเหตุของการบาดเจ็บ</th>
                        <td>{{ $accident->cause ?: '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="acc-report-section" aria-labelledby="accidentTreatmentReportTitle">
            <h2 class="acc-report-section-title" id="accidentTreatmentReportTitle">2. การรักษาและการพบแพทย์</h2>

            <table class="acc-report-table">
                <tbody>
                    <tr>
                        <th>การพบแพทย์</th>
                        <td>
                            <span class="acc-report-status {{ $isDoctorVisit ? 'acc-report-status-doctor' : 'acc-report-status-home' }}">
                                {{ $accident->treat_no ?: '-' }}
                            </span>
                        </td>
                    </tr>

                    @if($isDoctorVisit)
                        <tr>
                            <th>สถานพยาบาล</th>
                            <td>{{ $accident->hospital ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>ผลวินิจฉัย</th>
                            <td>{{ $accident->diagnosis ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>แพทย์นัดครั้งต่อไป</th>
                            <td>
                                {{ !empty($accident->appointment)
                                    ? \App\Helpers\ThaiDateHelper::formatThaiDate($accident->appointment)
                                    : '-' }}
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <th>การรักษา</th>
                        <td>{{ $accident->treatment ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>การป้องกันและการแก้ไข</th>
                        <td>{{ $accident->protection ?: '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="acc-report-section" aria-labelledby="accidentRecordReportTitle">
            <h2 class="acc-report-section-title" id="accidentRecordReportTitle">3. ผู้ดูแลและการบันทึก</h2>

            <table class="acc-report-table">
                <tbody>
                    <tr>
                        <th>ผู้ดูแล</th>
                        <td>{{ $accident->caretaker ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>วันที่บันทึก</th>
                        <td>{{ \App\Helpers\ThaiDateHelper::formatThaiDate($accident->record_date) }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <div class="acc-report-footer-note">
            เอกสารจัดทำจากระบบบันทึกข้อมูลผู้รับบริการ
        </div>
    </main>
</div>
</body>
</html>