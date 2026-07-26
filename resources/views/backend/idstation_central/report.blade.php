<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายงานศูนย์กลางทะเบียนบุคคลไม่มีสถานะทางทะเบียน</title>

   <style>
    @page{
        size: A4 landscape;
        margin: 12mm;
    }

    *{
        box-sizing:border-box;
    }

    html{
        width:100%;
        overflow-x:hidden;
    }

    body{
        margin:0;
        padding:24px;
        background:#f8fafc;
        font-family:"Sarabun","TH Sarabun New",sans-serif;
        color:#0f172a;
        font-size:13px;
        line-height:1.55;
        overflow-x:hidden;
    }

    .report-page{
        width:100%;
        max-width:1180px;
        margin:0 auto;
        background:#ffffff;
        padding:26px 28px;
        border-radius:18px;
        box-shadow:0 10px 30px rgba(15,23,42,.06);
        overflow:hidden;
    }

    .print-actions{
        display:flex;
        justify-content:flex-end;
        flex-wrap:wrap;
        gap:8px;
        margin-bottom:18px;
    }

    .btn{
        border:1px solid #dbe3ef;
        background:#ffffff;
        padding:8px 15px;
        border-radius:999px;
        cursor:pointer;
        text-decoration:none;
        color:#334155;
        font-size:13px;
        font-weight:700;
        white-space:nowrap;
    }

    .btn-primary{
        background:linear-gradient(135deg,#2563eb,#1d4ed8);
        border-color:#1d4ed8;
        color:#ffffff;
    }

    .report-header{
        display:grid;
        grid-template-columns:minmax(0,1fr) 300px;
        gap:28px;
        padding-bottom:18px;
        margin-bottom:16px;
        border-bottom:1px solid #e5e7eb;
    }

    .report-title{
        min-width:0;
    }

    .report-title h2{
        margin:0;
        font-size:23px;
        line-height:1.35;
        font-weight:900;
        color:#0f172a;
        letter-spacing:-.2px;
    }

    .report-title p{
        margin:7px 0 0;
        color:#64748b;
        font-size:13px;
    }

    .report-meta{
        min-width:0;
        padding-top:2px;
    }

    .meta-row{
        display:grid;
        grid-template-columns:88px minmax(0,1fr);
        gap:10px;
        padding:4px 0;
        border-bottom:1px dashed #e5e7eb;
        font-size:12px;
    }

    .meta-row:last-child{
        border-bottom:none;
    }

    .meta-label{
        color:#64748b;
        font-weight:700;
        white-space:nowrap;
    }

    .meta-value{
        color:#0f172a;
        font-weight:800;
        text-align:right;
        word-break:break-word;
    }

    .summary-line{
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:0;
        margin:8px 0 22px;
        border-top:1px solid #e5e7eb;
        border-bottom:1px solid #e5e7eb;
    }

    .summary-item{
        min-width:0;
        padding:13px 16px;
        border-right:1px solid #e5e7eb;
    }

    .summary-item:nth-child(4n){
        border-right:none;
    }

    .summary-label{
        color:#64748b;
        font-size:12px;
        font-weight:700;
        margin-bottom:5px;
        white-space:normal;
    }

    .summary-value{
        font-size:25px;
        line-height:1;
        font-weight:900;
        color:#0f172a;
    }

    .summary-value.primary{ color:#2563eb; }
    .summary-value.warning{ color:#d97706; }
    .summary-value.success{ color:#16a34a; }
    .summary-value.danger{ color:#dc2626; }

    .result-summary-report{
        margin:0 0 22px;
        padding:14px 0 12px;
        border-top:1px solid #e5e7eb;
        border-bottom:1px solid #e5e7eb;
    }

    .result-summary-title{
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:14px;
        margin-bottom:8px;
    }

    .result-summary-title strong{
        display:block;
        color:#0f172a;
        font-size:15px;
        font-weight:900;
    }

    .result-summary-title span{
        display:block;
        margin-top:2px;
        color:#64748b;
        font-size:12px;
        font-weight:600;
    }

    .result-summary-total{
        color:#15803d;
        font-size:12px;
        font-weight:900;
        white-space:nowrap;
    }

    .result-summary-list{
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        column-gap:28px;
        row-gap:0;
    }

    .result-summary-row{
        min-width:0;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;
        padding:8px 0;
        border-top:1px dashed #e5e7eb;
    }

    .result-summary-name{
        min-width:0;
        color:#2563eb;
        font-size:13px;
        font-weight:800;
        line-height:1.45;
        white-space:normal;
        overflow-wrap:anywhere;
        word-break:break-word;
    }

   .result-summary-count{
    flex:0 0 auto;
    min-width:38px;
    text-align:right;
    color:#7c3aed;      /* ม่วง */
    font-size:18px;
    font-weight:900;
    white-space:nowrap;
}

    .section-title{
        display:grid;
        grid-template-columns:minmax(0,1fr) auto;
        align-items:start;
        gap:14px;
        margin:4px 0 10px;
    }

    .section-title h3{
        margin:0;
        font-size:15px;
        font-weight:900;
        color:#0f172a;
        line-height:1.4;
    }

    .section-title span{
        color:#64748b;
        font-size:12px;
        line-height:1.4;
    }

    .table-wrap{
        width:100%;
        max-width:100%;
        overflow-x:auto;
        border-top:2px solid #2563eb;
        -webkit-overflow-scrolling:touch;
    }

    table{
        width:100%;
        min-width:920px;
        border-collapse:collapse;
        background:#ffffff;
    }

    th{
        background:#f8fafc;
        color:#334155;
        font-weight:900;
        text-align:center;
        white-space:nowrap;
        border-bottom:1px solid #dbe3ef;
        padding:9px 8px;
        font-size:12px;
    }

    td{
        border-bottom:1px solid #edf2f7;
        padding:8px 9px;
        vertical-align:top;
        color:#1f2937;
    }

    tr:nth-child(even) td{
        background:#fcfcfd;
    }

    .center{
        text-align:center;
    }

    .name{
        font-weight:900;
        color:#0f172a;
        line-height:1.45;
    }

    .muted{
        color:#64748b;
        font-size:12px;
        margin-top:2px;
        line-height:1.45;
    }

    .status{
        display:inline-flex;
        align-items:center;
        gap:5px;
        padding:4px 10px;
        border-radius:999px;
        font-size:11px;
        font-weight:900;
        white-space:nowrap;
    }

    .status-success{
        background:#ecfdf5;
        color:#15803d;
    }

    .status-warning{
        background:#fffbeb;
        color:#b45309;
    }

    .status-danger{
        background:#fef2f2;
        color:#b91c1c;
    }

    .status-secondary{
        background:#f1f5f9;
        color:#475569;
    }

    .empty-text{
        padding:22px 10px;
        color:#64748b;
        font-weight:700;
    }

    .footer-note{
        margin-top:16px;
        display:flex;
        justify-content:space-between;
        flex-wrap:wrap;
        gap:8px 12px;
        color:#64748b;
        font-size:11px;
        border-top:1px solid #e5e7eb;
        padding-top:10px;
    }

    @media (max-width:768px){
        body{
            padding:10px;
            font-size:12px;
        }

        .report-page{
            padding:18px 14px;
            border-radius:16px;
        }

        .print-actions{
            justify-content:flex-start;
        }

        .btn{
            padding:7px 12px;
            font-size:12px;
        }

        .report-header{
            grid-template-columns:1fr;
            gap:12px;
            margin-bottom:14px;
            padding-bottom:14px;
        }

        .report-title h2{
            font-size:18px;
        }

        .report-title p{
            font-size:12px;
        }

        .report-meta{
            width:100%;
        }

        .meta-row{
            grid-template-columns:78px minmax(0,1fr);
            font-size:11.5px;
        }

        .summary-line{
            grid-template-columns:repeat(2,minmax(0,1fr));
            margin-bottom:18px;
        }

        .summary-item{
            padding:11px 10px;
            border-right:1px solid #e5e7eb;
            border-bottom:1px solid #e5e7eb;
        }

        .summary-item:nth-child(2n){
            border-right:none;
        }

        .summary-item:nth-last-child(-n+2){
            border-bottom:none;
        }

        .summary-label{
            font-size:11.5px;
        }

        .summary-value{
            font-size:21px;
        }

        .result-summary-title{
            display:block;
        }

        .result-summary-total{
            margin-top:6px;
            font-size:11.5px;
        }

        .result-summary-list{
            grid-template-columns:1fr;
            column-gap:0;
        }

        .result-summary-row{
            gap:8px;
            padding:8px 0;
        }

        .result-summary-name{
            font-size:12px;
            white-space:normal;
            overflow-wrap:anywhere;
            word-break:break-word;
        }

        .result-summary-count{
            font-size:15px;
        }

        .section-title{
            grid-template-columns:1fr;
            gap:4px;
        }

        .section-title h3{
            font-size:14px;
        }

        .section-title span{
            font-size:11.5px;
        }

        table{
            min-width:880px;
        }

        th,
        td{
            font-size:11.5px;
            padding:7px 8px;
        }

        .footer-note{
            display:block;
            font-size:10.5px;
        }

        .footer-note div + div{
            margin-top:4px;
        }
    }

    @media (max-width:480px){
        body{
            padding:8px;
        }

        .report-page{
            padding:16px 12px;
            border-radius:14px;
        }

        .summary-line{
            grid-template-columns:1fr;
        }

        .summary-item,
        .summary-item:nth-child(2n),
        .summary-item:nth-child(4n){
            border-right:none;
            border-bottom:1px solid #e5e7eb;
        }

        .summary-item:last-child{
            border-bottom:none;
        }

        .result-summary-report{
            padding-top:12px;
        }

        .table-wrap{
            margin-left:-2px;
            margin-right:-2px;
            padding-bottom:4px;
        }
    }

 @media print{
    @page{
        size:A4 landscape;
        margin:8mm;
    }

    html,
    body{
        width:297mm !important;
        height:auto !important;
        margin:0 !important;
        padding:0 !important;
        background:#fff !important;
        overflow:visible !important;
        font-size:10px !important;
        line-height:1.28 !important;
        -webkit-print-color-adjust:exact !important;
        print-color-adjust:exact !important;
    }

    .print-actions{
        display:none !important;
    }

    .report-page{
        width:100% !important;
        max-width:100% !important;
        margin:0 !important;
        padding:0 !important;
        border:none !important;
        border-radius:0 !important;
        box-shadow:none !important;
        overflow:visible !important;
    }

    .report-header{
        display:grid !important;
        grid-template-columns:1fr 240px !important;
        gap:14px !important;
        padding-bottom:6px !important;
        margin-bottom:6px !important;
        border-bottom:1px solid #cbd5e1 !important;
        break-inside:auto !important;
    }

    .report-title h2{
        font-size:15px !important;
        line-height:1.25 !important;
        margin:0 0 2px !important;
    }

    .report-title p{
        font-size:9px !important;
        margin:0 !important;
    }

    .report-meta{
        width:240px !important;
        min-width:240px !important;
    }

    .meta-row{
        grid-template-columns:72px 1fr !important;
        gap:6px !important;
        padding:2px 0 !important;
        font-size:9px !important;
    }

    .summary-line{
        grid-template-columns:repeat(4,1fr) !important;
        margin:5px 0 7px !important;
        break-inside:auto !important;
    }

    .summary-item{
        padding:5px 8px !important;
        border-bottom:none !important;
    }

    .summary-label{
        font-size:9px !important;
        margin-bottom:1px !important;
    }

    .summary-value{
        font-size:15px !important;
    }

    .result-summary-report{
        margin:0 0 7px !important;
        padding:5px 0 6px !important;
        break-inside:auto !important;
    }

    .result-summary-title{
        margin-bottom:3px !important;
    }

    .result-summary-title strong{
        font-size:11px !important;
    }

    .result-summary-title span{
        font-size:8.8px !important;
        margin-top:0 !important;
    }

    .result-summary-total{
        font-size:9px !important;
    }

    .result-summary-list{
        grid-template-columns:repeat(2,minmax(0,1fr)) !important;
        column-gap:20px !important;
    }

    .result-summary-row{
        grid-template-columns:minmax(0,1fr) 26px !important;
        padding:3px 0 !important;
        gap:5px !important;
    }

    .result-summary-name{
        font-size:9px !important;
        line-height:1.2 !important;
    }

    .result-summary-count{
        min-width:22px !important;
        height:17px !important;
        padding:0 5px !important;
        font-size:9px !important;
    }

    .section-title{
        margin:6px 0 3px !important;
        grid-template-columns:1fr auto !important;
        break-inside:auto !important;
    }

    .section-title h3{
        font-size:11px !important;
    }

    .section-title span{
        font-size:8.8px !important;
    }

    .table-wrap{
        overflow:visible !important;
        border-top:1.5px solid #2563eb !important;
    }

    table{
        width:100% !important;
        min-width:0 !important;
        table-layout:fixed !important;
        border-collapse:collapse !important;
        page-break-inside:auto !important;
    }

    th{
        font-size:8.8px !important;
        padding:3px 3px !important;
        line-height:1.2 !important;
        white-space:normal !important;
    }

    td{
        font-size:8.8px !important;
        padding:3px 3px !important;
        line-height:1.22 !important;
        word-break:break-word !important;
    }

    tr{
        page-break-inside:avoid !important;
        page-break-after:auto !important;
    }

    th:nth-child(1),
    td:nth-child(1){ width:32px !important; }

    th:nth-child(2),
    td:nth-child(2){ width:125px !important; }

    th:nth-child(3),
    td:nth-child(3){ width:38px !important; }

    th:nth-child(4),
    td:nth-child(4){ width:82px !important; }

    th:nth-child(5),
    td:nth-child(5){ width:96px !important; }

    th:nth-child(6),
    td:nth-child(6){ width:78px !important; }

    th:nth-child(7),
    td:nth-child(7){ width:98px !important; }

    th:nth-child(8),
    td:nth-child(8){ width:88px !important; }

    th:nth-child(9),
    td:nth-child(9){ width:52px !important; }

    .name{
        font-size:8.8px !important;
        line-height:1.2 !important;
    }

    .muted{
        font-size:8px !important;
        line-height:1.2 !important;
    }

    .status{
        padding:2px 5px !important;
        font-size:8px !important;
        line-height:1 !important;
        gap:2px !important;
    }

    .footer-note{
        margin-top:6px !important;
        padding-top:4px !important;
        font-size:8px !important;
        line-height:1.2 !important;
        break-inside:auto !important;
    }
}
</style>
</head>

<body>

<div class="report-page">

    <div class="print-actions">
        <button type="button" class="btn btn-primary" onclick="window.print()">
            พิมพ์รายงาน
        </button>

        <a href="{{ route('idstation.central.index', request()->query()) }}" class="btn">
            กลับหน้าหลัก
        </a>
    </div>

    <div class="report-header">
        <div class="report-title">
            <h2>รายงานศูนย์กลางทะเบียนบุคคลไม่มีสถานะทางทะเบียน</h2>
            <p>ภาพรวมการรับเรื่อง การติดตาม และผลการดำเนินงานด้านสถานะทางทะเบียน</p>
        </div>

        <div class="report-meta">
            <div class="meta-row">
                <span class="meta-label">ช่วงวันที่</span>
                <span class="meta-value">
                    @if(request('date_from') || request('date_to'))
                        {{ request('date_from') ?: 'เริ่มต้น' }}
                        -
                        {{ request('date_to') ?: 'ปัจจุบัน' }}
                    @else
                        ทั้งหมด
                    @endif
                </span>
            </div>

            <div class="meta-row">
                <span class="meta-label">วันที่พิมพ์</span>
                <span class="meta-value">
                    {{ now('Asia/Bangkok')->locale('th')->translatedFormat('d F') }}
                    {{ now('Asia/Bangkok')->year + 543 }}
                </span>
            </div>

            <div class="meta-row">
                <span class="meta-label">จำนวนรายการ</span>
                <span class="meta-value">{{ number_format($summary['total'] ?? 0) }} ราย</span>
            </div>
        </div>
    </div>

 <div class="result-summary-report">
    <div class="result-summary-title">
        <div>
            <strong>สรุปผลสถานะที่ได้รับ</strong>
            <span>ดึงข้อมูลจริงจากรายการทางทะเบียน</span>
        </div>

        <div class="result-summary-total">
            สำเร็จ {{ number_format($summary['completed'] ?? 0) }} รายการ
        </div>
    </div>

    <div class="result-summary-list">
        @forelse($citizenSummary as $item)
            <div class="result-summary-row">
                <span class="result-summary-name">
                    {{ $item['name'] ?? '-' }}
                </span>

                <span class="result-summary-count">
                    {{ number_format($item['count'] ?? 0) }}
                </span>
            </div>
        @empty
            <div class="result-summary-row">
                <span class="result-summary-name">ยังไม่มีผลสถานะที่ได้รับ</span>
                <span class="result-summary-count">0</span>
            </div>
        @endforelse
    </div>
</div>

    <div class="section-title">
        <h3>รายละเอียดรายการบุคคลไม่มีสถานะทางทะเบียน</h3>
        <span>แสดงตามเงื่อนไขตัวกรองที่เลือก</span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:42px;">ลำดับ</th>
                    <th>ชื่อ - สกุล</th>
                    <th style="width:58px;">อายุ</th>
                    <th>บ้าน</th>
                    <th>โครงการ</th>
                    <th style="width:108px;">วันที่รับเรื่อง</th>
                    <th style="width:120px;">ผลด้านสถานะ</th>
                    <th style="width:125px;">สถานะ</th>
                    <th style="width:82px;">ระยะเวลา</th>
                </tr>
            </thead>

            <tbody>
                @forelse($idstations as $index => $idstation)

                    @php
                        $client = $idstation->client;

                        $days = $idstation->created_at
                            ? \Carbon\Carbon::parse($idstation->created_at)
                                ->startOfDay()
                                ->diffInDays(now('Asia/Bangkok')->startOfDay())
                            : 0;

                        $isCompleted = $idstation->citizenships->count() > 0 || $idstation->citizens->count() > 0;

                        if ($isCompleted) {
                            $statusText = 'ได้รับสถานะแล้ว';
                            $statusClass = 'status-success';
                            $statusDot = '●';
                        } else {
                            $statusText = 'อยู่ระหว่างดำเนินการ';

                            if ($days > 180) {
                                $statusClass = 'status-danger';
                                $statusDot = '●';
                            } elseif ($days > 90) {
                                $statusClass = 'status-warning';
                                $statusDot = '●';
                            } else {
                                $statusClass = 'status-secondary';
                                $statusDot = '●';
                            }
                        }

                        $resultText = '-';

                        if ($idstation->citizenships->count() > 0) {
                            $resultText = 'ได้สัญชาติ';
                        } elseif ($idstation->citizens->count() > 0) {
                            $resultText = 'ได้เลขประจำตัว';
                        }
                    @endphp

                    <tr>
                        <td class="center">{{ $index + 1 }}</td>

                        <td>
                            <div class="name">
                                {{ $client->first_name ?? '-' }} {{ $client->last_name ?? '' }}
                            </div>
                            <div class="muted">
                                {{ $client->target->target_name ?? '' }}
                            </div>
                        </td>

                        <td class="center">{{ $client->age ?? '-' }}</td>

                        <td>{{ $client->house->house_name ?? $client->house->name ?? '-' }}</td>

                        <td>{{ $client->project->project_name ?? $client->project->name ?? '-' }}</td>

                        <td class="center">
                            @if($idstation->created_at)
                                {{ $idstation->created_at->locale('th')->translatedFormat('d F') }}
                                {{ $idstation->created_at->year + 543 }}
                            @else
                                -
                            @endif
                        </td>

                        <td class="center">{{ $resultText }}</td>

                        <td class="center">
                            <span class="status {{ $statusClass }}">
                                <span>{{ $statusDot }}</span>
                                {{ $statusText }}
                            </span>
                        </td>

                        <td class="center">
                            {{ $days == 0 ? 'วันนี้' : $days . ' วัน' }}
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="9" class="center empty-text">
                            ไม่พบข้อมูลตามเงื่อนไขที่เลือก
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer-note">
        <div>ระบบศูนย์กลางทะเบียนบุคคลไม่มีสถานะทางทะเบียน</div>
        <div>
            พิมพ์เมื่อ
            {{ now('Asia/Bangkok')->locale('th')->translatedFormat('d F') }}
            {{ now('Asia/Bangkok')->year + 543 }}
        </div>
    </div>

</div>

</body>
</html>