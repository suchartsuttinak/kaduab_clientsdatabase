<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>รายงานสถิติผู้รับบริการ</title>

  <style>
    body {
        font-family: "TH Sarabun New", "Sarabun", sans-serif;
        background: linear-gradient(180deg, #edf3f8 0%, #e2e8f0 100%);
        color: #111827;
        margin: 0;
        font-size: 14px;
        line-height: 1.3;
    }

    .toolbar {
        width: 297mm;
        margin: 16px auto 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn {
        border: 0;
        border-radius: 999px;
        padding: 9px 18px;
        cursor: pointer;
        text-decoration: none;
        font-size: 14px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: .22s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-print {
        background: linear-gradient(135deg, #0f766e, #14b8a6);
        color: #fff;
        box-shadow: 0 10px 22px rgba(15, 118, 110, .25);
    }

    .btn-print:hover {
        box-shadow: 0 14px 28px rgba(15, 118, 110, .35);
    }

    .btn-back {
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: #fff;
        box-shadow: 0 10px 22px rgba(37, 99, 235, .24);
    }

    .btn-back:hover {
        box-shadow: 0 14px 28px rgba(37, 99, 235, .34);
    }

    .sheet {
        width: 297mm;
        min-height: 210mm;
        margin: 0 auto 20px;
        background: #fff;
        padding: 9mm 10mm;
        box-sizing: border-box;
        border-radius: 10px;
        box-shadow: 0 16px 40px rgba(15, 23, 42, .10);
    }

    .report-head {
        border-bottom: 2px solid #0f766e;
        padding-bottom: 8px;
        margin-bottom: 10px;
        text-align: right;
    }

    .report-kicker {
        font-size: 13px;
        color: #0f766e;
        font-weight: 800;
        letter-spacing: .04em;
    }

    .report-title {
        font-size: 23px;
        font-weight: 900;
        margin: 2px 0 0;
        color: #0f172a;
        text-align: right !important;
        line-height: 1.1;
    }

    .report-subtitle {
        font-size: 13.5px;
        margin-top: 3px;
        color: #64748b;
        text-align: right;
    }

    .filter-box {
        margin-top: 9px;
        padding: 8px 10px;
        border: 1px solid #dbe3ec;
        background: #f8fafc;
        border-radius: 10px;
        font-size: 13.5px;
        color: #334155;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-top: 10px;
    }

    .summary-card {
        border: 1px solid #dbe3ec;
        border-radius: 12px;
        padding: 8px 9px;
        text-align: center;
        background: linear-gradient(180deg, #ffffff, #f8fafc);
        box-shadow: 0 4px 12px rgba(15, 23, 42, .04);
    }

    .summary-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 700;
    }

    .summary-value {
        font-size: 24px;
        font-weight: 900;
        margin-top: 2px;
        color: #0f766e;
        line-height: 1;
    }

    .summary-unit {
        font-size: 12px;
        color: #64748b;
        font-weight: 700;
        margin-left: 2px;
    }

    .section-title {
        margin: 12px 0 5px;
        font-size: 15px;
        font-weight: 900;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .section-title::before {
        content: "";
        width: 4px;
        height: 15px;
        border-radius: 999px;
        background: #0f766e;
        display: inline-block;
    }

    .summary-tables {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-top: 4px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 4px;
        table-layout: fixed;
        overflow: hidden;
        border-radius: 8px;
    }

    th,
    td {
        border: 1px solid #d7dee8;
        padding: 4px 6px;
        vertical-align: top;
        word-break: break-word;
    }

    th {
        background: #ecfdf5;
        color: #065f46;
        font-weight: 900;
        text-align: center;
        font-size: 13.2px;
    }

    td {
        font-size: 13.2px;
    }

    tbody tr:nth-child(even) {
        background: #fafafa;
    }

    tbody tr:hover {
        background: #f0fdf4;
    }

    td.center {
        text-align: center;
    }

    .muted {
        color: #64748b;
    }

    .main-table th,
    .main-table td {
        font-size: 12.8px;
        padding: 3px 4px;
    }

    .footer-note {
        margin-top: 10px;
        font-size: 12px;
        color: #64748b;
        text-align: right;
    }

    .report-head{
    border-bottom: 2px solid #0f766e;
    padding-bottom: 8px;
    margin-bottom: 10px;
    text-align: right;
}

.report-title{
    font-size: 23px;
    font-weight: 900;
    margin: 2px 0 0;
    color: #0f172a;
    line-height: 1.1;

    text-align: center !important;
}

.report-subtitle{
    font-size: 13.5px;
    margin-top: 3px;
    color: #64748b;

    text-align: right;
}

    @media print {
        @page {
            size: A4 landscape;
            margin: 7mm;
        }

        body {
            background: #fff;
            font-size: 13px;
        }

        .toolbar {
            display: none;
        }

        .sheet {
            width: auto;
            min-height: auto;
            margin: 0;
            padding: 0;
            border-radius: 0;
            box-shadow: none;
        }

        .summary-card,
        .filter-box,
        th {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }
</style>
</head>

<body>

    <div class="toolbar">
        <a href="{{ route('statistics.index', request()->query()) }}" class="btn btn-back">
            ← กลับหน้าสถิติ
        </a>

        <button onclick="window.print()" class="btn btn-print">
            พิมพ์รายงาน
        </button>
    </div>

    <div class="sheet">

        <div class="report-head text-end">
            <h1 class="report-title">รายงานสถิติผู้รับบริการ</h1>

            <div class="report-subtitle">
                วันที่ออกรายงาน {{ now()->locale('th')->translatedFormat('j F') }}
                {{ now()->year + 543 }}
            </div>
        </div>

        <div class="filter-box">
            <strong>เงื่อนไขการกรอง:</strong>

            สถานะ
            {{
                $releaseStatus === 'all'
                    ? 'ทั้งหมด'
                    : ($releaseStatus === 'show'
                        ? 'แสดง'
                        : $releaseStatus)
            }},

            บ้าน {{ !empty($houseId) ? 'ตามที่เลือก' : 'ทั้งหมด' }},

            เพศ {{ $gender ? ($gender === 'male' ? 'ชาย' : 'หญิง') : 'ทั้งหมด' }},

            อายุ {{ $ageMin }} - {{ $ageMax }} ปี,

            กลุ่มเป้าหมาย {{ $targetName ?? 'ทั้งหมด' }}

            @if ($startMonth && $startYear && $endMonth && $endYear),
                ช่วงเวลา
                {{ str_pad($startMonth, 2, '0', STR_PAD_LEFT) }}/{{ $startYear }}
                ถึง
                {{ str_pad($endMonth, 2, '0', STR_PAD_LEFT) }}/{{ $endYear }}
            @endif
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-label">จำนวนทั้งหมด</div>
                <div class="summary-value">
                    {{ $clients->count() }}<span class="summary-unit">คน</span>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-label">ชาย</div>
                <div class="summary-value">
                    {{ $maleCount }}<span class="summary-unit">คน</span>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-label">หญิง</div>
                <div class="summary-value">
                    {{ $femaleCount }}<span class="summary-unit">คน</span>
                </div>
            </div>
        </div>

        <div class="summary-tables">

            @if (isset($houseSummary) && count($houseSummary) > 0)
                <div>
                    <div class="section-title">สรุปตามบ้าน</div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:75%">บ้าน</th>
                                <th style="width:25%">จำนวน</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($houseSummary as $name => $count)
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td class="center">{{ $count }} คน</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (isset($targetSummary) && count($targetSummary) > 0)
                <div>
                    <div class="section-title">สรุปตามกลุ่มเป้าหมาย</div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:75%">กลุ่มเป้าหมาย</th>
                                <th style="width:25%">จำนวน</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($targetSummary as $name => $count)
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td class="center">{{ $count }} คน</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (count($problemSummary) > 0)
                <div>
                    <div class="section-title">สรุปตามสภาพปัญหา</div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:75%">สภาพปัญหา</th>
                                <th style="width:25%">จำนวน</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($problemSummary as $name => $count)
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td class="center">{{ $count }} คน</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (count($educationSummary) > 0)
                <div>
                    <div class="section-title">สรุปตามระดับการศึกษา</div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:75%">ระดับการศึกษา</th>
                                <th style="width:25%">จำนวน</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($educationSummary as $name => $count)
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td class="center">{{ $count }} คน</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (count($institutionSummary) > 0)
                <div>
                    <div class="section-title">สรุปตามสถานศึกษา</div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:75%">สถานศึกษา</th>
                                <th style="width:25%">จำนวน</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($institutionSummary as $name => $count)
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td class="center">{{ $count }} คน</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (count($projectSummary) > 0)
                <div>
                    <div class="section-title">สรุปตามหน่วยงาน / โครงการ</div>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:75%">หน่วยงาน / โครงการ</th>
                                <th style="width:25%">จำนวน</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projectSummary as $name => $count)
                                <tr>
                                    <td>{{ $name }}</td>
                                    <td class="center">{{ $count }} คน</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="section-title">รายชื่อผู้รับบริการตามเงื่อนไข</div>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width:5%">ลำดับ</th>
                    <th style="width:16%">ชื่อ - สกุล</th>
                    <th style="width:6%">เพศ</th>
                    <th style="width:5%">อายุ</th>
                    <th style="width:10%">บ้าน</th>
                    <th style="width:13%">ระดับการศึกษา</th>
                    <th style="width:17%">สถานศึกษา</th>
                    <th style="width:13%">กลุ่มเป้าหมาย</th>
                    <th style="width:15%">สภาพปัญหา</th>
                </tr>
            </thead>

            <tbody>
                @forelse($clients as $index => $client)
                    @php
                        $latestEducation = $client->educationRecords->first();

                        $problemNames = $client->problems
                            ->map(fn($p) => $p->problem_name ?? ($p->name ?? '-'))
                            ->filter()
                            ->implode(', ');
                    @endphp

                    <tr>
                        <td class="center">{{ $index + 1 }}</td>

                        <td>{{ $client->fullname ?? '-' }}</td>

                        <td class="center">
                            @if ($client->gender === 'male')
                                ชาย
                            @elseif($client->gender === 'female')
                                หญิง
                            @else
                                -
                            @endif
                        </td>

                        <td class="center">
                            {{ $client->birth_date ? \Carbon\Carbon::parse($client->birth_date)->age : '-' }}
                        </td>

                        <td>
                            {{ $client->house->house_name ?? '-' }}
                        </td>

                        <td>
                            {{ $latestEducation?->education?->education_name ?? '-' }}
                        </td>

                        <td>
                            {{ $latestEducation?->institution?->institution_name ?? ($latestEducation?->school_name ?? '-') }}
                        </td>

                        <td>
                            {{ $client->target->target_name ?? '-' }}
                        </td>

                        <td>
                            {{ $problemNames ?: '-' }}
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="9" class="center muted">
                            ไม่พบข้อมูลตามเงื่อนไขที่เลือก
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer-note">
            รวม {{ $clients->count() }} รายการ
        </div>

    </div>

</body>

</html>
