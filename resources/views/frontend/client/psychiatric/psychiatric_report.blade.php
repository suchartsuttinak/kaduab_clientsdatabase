@extends('admin_client.admin_client')

@section('content')
<div class="container-fluid psychiatric-report-page">

    <style>
        .psychiatric-report-page{
            font-family:"TH Sarabun New","Sarabun",sans-serif;
            color:#0f172a;
            background:#f6f8fb;
            padding-top:24px;
            padding-bottom:24px;
        }

        .report-page{
            max-width:1480px;
            margin:0 auto;
            background:#ffffff;
            border:1px solid #e2e8f0;
            border-radius:22px;
            box-shadow:0 16px 40px rgba(15,23,42,.08);
            overflow:hidden;
        }

        .report-body{
            padding:26px;
        }

        .report-toolbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            margin-bottom:18px;
            flex-wrap:wrap;
        }

        .report-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            padding:.65rem 1.1rem;
            border-radius:12px;
            font-weight:800;
            border:1px solid #cbd5e1;
            background:#ffffff;
            color:#1e293b;
            text-decoration:none;
            box-shadow:0 4px 12px rgba(15,23,42,.04);
            transition:all .2s ease;
        }

        .report-btn:hover{
            background:#f8fafc;
            color:#0f172a;
            border-color:#94a3b8;
            text-decoration:none;
        }

        .report-btn-print{
            background:linear-gradient(135deg,#2563eb,#1d4ed8);
            border-color:#1d4ed8;
            color:#ffffff !important;
        }

        .report-btn-print:hover,
        .report-btn-print:focus,
        .report-btn-print:active{
            background:linear-gradient(135deg,#1d4ed8,#1e40af) !important;
            border-color:#1e40af !important;
            color:#ffffff !important;
            text-decoration:none !important;
            box-shadow:0 8px 18px rgba(37,99,235,.24);
            transform:translateY(-1px);
        }

        .report-header{
            text-align:center;
            padding:24px 18px;
            margin-bottom:18px;
            border-radius:18px;
            border:1px solid #bfdbfe;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.55), transparent 35%),
                linear-gradient(135deg,#e0f2fe,#eff6ff);
        }

        .report-title{
            font-size:2rem;
            font-weight:900;
            margin:0;
            color:#0f172a;
            letter-spacing:.2px;
        }

        .report-subtitle{
            font-size:1rem;
            color:#475569;
            margin-top:5px;
            font-weight:700;
        }

        .report-info{
            display:grid;
            grid-template-columns:repeat(2,minmax(0,1fr));
            gap:14px;
            margin-bottom:18px;
        }

        .report-info-box{
            border:1px solid #dbeafe;
            border-radius:16px;
            padding:14px 16px 14px 18px;
            background:#f8fafc;
            position:relative;
            overflow:hidden;
        }

        .report-info-box::before{
            content:"";
            position:absolute;
            left:0;
            top:0;
            width:5px;
            height:100%;
            background:#2563eb;
        }

        .report-info-label{
            font-size:.82rem;
            color:#64748b;
            font-weight:800;
            margin-bottom:3px;
        }

        .report-info-value{
            font-size:1.05rem;
            font-weight:900;
            color:#0f172a;
        }

        .report-table-wrap{
            overflow-x:auto;
            border:1px solid #e2e8f0;
            border-radius:16px;
            background:#ffffff;
        }

        .report-table{
            width:100%;
            min-width:1200px;
            margin:0;
            border-collapse:collapse;
        }

        .report-table thead th{
            background:#f1f5f9;
            color:#0f172a;
            font-weight:900;
            font-size:.88rem;
            text-align:center;
            vertical-align:middle;
            padding:12px 10px;
            border-bottom:1px solid #e2e8f0;
            white-space:nowrap;
        }

        .report-table tbody td{
            font-size:.92rem;
            color:#1f2937;
            vertical-align:middle;
            padding:11px 10px;
            border-bottom:1px solid #edf2f7;
        }

        .report-table tbody tr:nth-child(even){
            background:#fbfdff;
        }

        .report-table tbody tr:hover{
            background:#f8fafc;
        }

        .status-yes,
        .status-no{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-width:46px;
            padding:4px 10px;
            border-radius:999px;
            font-weight:900;
            font-size:.82rem;
            line-height:1.1;
            white-space:nowrap;
        }

        .status-yes{
            background:#dcfce7;
            color:#166534;
        }

        .status-no{
            background:#fee2e2;
            color:#991b1b;
        }

        .empty-report-box{
            padding:32px 18px;
            border:1px dashed #cbd5e1;
            border-radius:16px;
            background:#f8fafc;
            color:#64748b;
            font-weight:800;
            text-align:center;
        }

        @media (max-width:768px){
            .psychiatric-report-page{
                padding-top:12px;
                padding-bottom:12px;
            }

            .report-page{
                border-radius:16px;
            }

            .report-body{
                padding:16px;
            }

            .report-toolbar{
                align-items:stretch;
            }

            .report-btn{
                width:100%;
            }

            .report-header{
                padding:18px 12px;
            }

            .report-title{
                font-size:1.5rem;
            }

            .report-info{
                grid-template-columns:1fr;
            }
        }

        @page{
            size:A4 landscape;
            margin:8mm 10mm;
        }

        @media print{
            *{
                box-sizing:border-box !important;
            }

            html,
            body{
                width:297mm !important;
                height:auto !important;
                min-height:0 !important;
                margin:0 !important;
                padding:0 !important;
                background:#ffffff !important;
                font-family:"TH Sarabun New","Sarabun",sans-serif !important;
                overflow:visible !important;
                -webkit-print-color-adjust:exact !important;
                print-color-adjust:exact !important;
            }

            .navbar,
            .navbar-custom,
            .leftside-menu,
            .sidebar,
            .footer,
            .page-title-box,
            .report-toolbar,
            header,
            footer{
                display:none !important;
            }

            .wrapper,
            .content-page,
            .content,
            .page-content,
            .main-content,
            .container-fluid,
            .psychiatric-report-page{
                width:100% !important;
                max-width:100% !important;
                height:auto !important;
                min-height:0 !important;
                margin:0 !important;
                padding:0 !important;
                background:#ffffff !important;
                overflow:visible !important;
            }

            .report-page{
                display:block !important;
                width:100% !important;
                max-width:100% !important;
                height:auto !important;
                min-height:0 !important;
                margin:0 !important;
                padding:0 !important;
                border:none !important;
                border-radius:0 !important;
                box-shadow:none !important;
                background:#ffffff !important;
                overflow:visible !important;
                page-break-after:auto !important;
                break-after:auto !important;
            }

            .report-body{
                height:auto !important;
                min-height:0 !important;
                padding:0 !important;
                margin:0 !important;
            }

            .report-header{
                text-align:center !important;
                background:#ffffff !important;
                border:none !important;
                border-bottom:1px solid #cbd5e1 !important;
                border-radius:0 !important;
                padding:0 0 4px !important;
                margin:0 0 5px !important;
            }

            .report-title{
                font-size:20px !important;
                font-weight:900 !important;
                line-height:1.05 !important;
                color:#0f172a !important;
                margin:0 !important;
            }

            .report-subtitle{
                font-size:11px !important;
                color:#64748b !important;
                margin-top:2px !important;
                line-height:1.05 !important;
                font-weight:700 !important;
            }

            .report-info{
                display:flex !important;
                align-items:center !important;
                justify-content:flex-start !important;
                flex-wrap:wrap !important;
                gap:4px 22px !important;
                margin:0 0 5px !important;
                padding:0 0 4px 4px !important;
                border-bottom:1px solid #dbe4f0 !important;
            }

            .report-info-box{
                border:none !important;
                background:none !important;
                padding:0 !important;
                margin:0 !important;
                min-width:auto !important;
                flex:none !important;
                border-radius:0 !important;
                position:relative !important;
                overflow:visible !important;
            }

            .report-info-box::before{
                display:none !important;
            }

            .report-info-box::after{
                content:"";
                position:absolute;
                right:-12px;
                top:50%;
                transform:translateY(-50%);
                width:1px;
                height:12px;
                background:#cbd5e1;
            }

            .report-info-box:last-child::after{
                display:none !important;
            }

            .report-info-label,
            .report-info-value{
                display:inline !important;
                font-size:12.5px !important;
                font-weight:900 !important;
                color:#2563eb !important;
                line-height:1.05 !important;
            }

            .report-info-label{
                margin-right:3px !important;
            }

            .report-table-wrap{
                width:100% !important;
                max-width:100% !important;
                height:auto !important;
                min-height:0 !important;
                overflow:visible !important;
                border:none !important;
                border-radius:0 !important;
                margin:0 !important;
                padding:0 !important;
                background:#ffffff !important;
            }

            .report-table{
                width:100% !important;
                min-width:0 !important;
                max-width:100% !important;
                height:auto !important;
                min-height:0 !important;
                margin:0 !important;
                table-layout:fixed !important;
                border-collapse:collapse !important;
                border-spacing:0 !important;
                background:#ffffff !important;
                page-break-inside:auto !important;
            }

            .report-table thead{
                display:table-header-group !important;
            }

            .report-table tr{
                page-break-inside:avoid !important;
                break-inside:avoid !important;
            }

            .report-table thead th{
                background:#eef4ff !important;
                color:#0f172a !important;
                border:1px solid #111827 !important;
                text-align:center !important;
                vertical-align:middle !important;
                padding:3px !important;
                font-size:10px !important;
                font-weight:900 !important;
                line-height:1.05 !important;
                white-space:normal !important;
            }

            .report-table tbody td{
                border:1px solid #111827 !important;
                padding:3px !important;
                font-size:9.8px !important;
                font-weight:600 !important;
                color:#111827 !important;
                line-height:1.05 !important;
                vertical-align:middle !important;
                white-space:normal !important;
                word-break:break-word !important;
                overflow-wrap:anywhere !important;
                text-align:center !important;
                background:#ffffff !important;
            }

            .report-table tbody tr:nth-child(even) td{
                background:#fcfdff !important;
            }

            .report-table th:nth-child(1),
            .report-table td:nth-child(1){
                width:9% !important;
            }

            .report-table th:nth-child(2),
            .report-table td:nth-child(2){
                width:16% !important;
            }

            .report-table th:nth-child(3),
            .report-table td:nth-child(3){
                width:14% !important;
            }

            .report-table th:nth-child(4),
            .report-table td:nth-child(4){
                width:25% !important;
            }

            .report-table th:nth-child(5),
            .report-table td:nth-child(5){
                width:9% !important;
            }

            .report-table th:nth-child(6),
            .report-table td:nth-child(6){
                width:7% !important;
            }

            .report-table th:nth-child(7),
            .report-table td:nth-child(7){
                width:13% !important;
            }

            .report-table th:nth-child(8),
            .report-table td:nth-child(8){
                width:7% !important;
            }

            .status-yes,
            .status-no{
                display:inline-flex !important;
                align-items:center !important;
                justify-content:center !important;
                min-width:28px !important;
                padding:1px 5px !important;
                border-radius:999px !important;
                font-size:9.5px !important;
                font-weight:900 !important;
                line-height:1.05 !important;
            }

            .status-yes{
                background:#dcfce7 !important;
                color:#166534 !important;
            }

            .status-no{
                background:#fee2e2 !important;
                color:#991b1b !important;
            }

            .empty-report-box{
                padding:14px !important;
                font-size:12px !important;
                border:1px dashed #94a3b8 !important;
                border-radius:0 !important;
                color:#475569 !important;
                background:#ffffff !important;
            }
        }
    </style>

    <div class="report-page">
        <div class="report-body">

            <div class="report-toolbar">
                <a href="{{ route('psychiatric.create', $client->id) }}" class="report-btn">
                    ← กลับหน้าบันทึก
                </a>

                <button type="button" onclick="window.print()" class="report-btn report-btn-print">
                    🖨 พิมพ์รายงาน
                </button>
            </div>

            <div class="report-header">
                <h1 class="report-title">รายงานการตรวจวินิจฉัยทางจิตเวช</h1>
                <div class="report-subtitle">
                    แสดงข้อมูลการส่งพบจิตเวช การวินิจฉัย และการติดตามผล
                </div>
            </div>

            <div class="report-info">
                <div class="report-info-box">
                    <div class="report-info-label">ชื่อ-สกุล</div>
                    <div class="report-info-value">{{ trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')) ?: ($client->fullname ?? $client->name ?? '-') }}</div>
                </div>

                <div class="report-info-box">
                    <div class="report-info-label">อายุ</div>
                    <div class="report-info-value">{{ filled($client->age) ? $client->age . ' ปี' : '-' }}</div>
                </div>

                @if(filled($startDate) || filled($endDate))
                    <div class="report-info-box" style="grid-column:1 / -1;">
                        <div class="report-info-label">ช่วงวันที่รายงาน</div>
                        <div class="report-info-value">
                            {{ filled($startDate) ? \Carbon\Carbon::parse($startDate)->addYears(543)->format('d/m/Y') : 'วันแรก' }}
                            ถึง
                            {{ filled($endDate) ? \Carbon\Carbon::parse($endDate)->addYears(543)->format('d/m/Y') : 'วันปัจจุบัน' }}
                        </div>
                    </div>
                @endif
            </div>

            @if($psychiatrics->isNotEmpty())
                <div class="report-table-wrap">
                    <table class="table report-table">
                        <thead>
                            <tr>
                                <th>วันที่ส่ง</th>
                                <th>สถานพยาบาล</th>
                                <th>ผลการตรวจ</th>
                                <th>การวินิจฉัย</th>
                                <th>วันที่นัด</th>
                                <th>ใช้ยา</th>
                                <th>ชื่อยา</th>
                                <th>ความพิการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($psychiatrics as $item)
                                <tr>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($item->sent_date)->addYears(543)->format('d/m/Y') }}
                                    </td>

                                    <td>{{ $item->hotpital ?? '-' }}</td>

                                    <td>{{ optional($item->psycho)->psycho_name ?? '-' }}</td>

                                    <td>{{ $item->diagnose ?? '-' }}</td>

                                    <td class="text-center">
                                        @if($item->appoin_date)
                                            {{ \Carbon\Carbon::parse($item->appoin_date)->addYears(543)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <span class="{{ $item->drug_no === 'yes' ? 'status-yes' : 'status-no' }}">
                                            {{ $item->drug_no === 'yes' ? 'รับยา' : 'ไม่รับยา' }}
                                        </span>
                                    </td>

                                    <td>{{ $item->drug_name ?? '-' }}</td>

                                    <td class="text-center">
                                        <span class="{{ $item->disa_no === 'yes' ? 'status-yes' : 'status-no' }}">
                                            {{ $item->disa_no === 'yes' ? 'ขึ้นทะเบียน' : 'ไม่ขึ้นทะเบียน' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-report-box">
                    ไม่มีข้อมูลรายงาน
                </div>
            @endif

        </div>
    </div>
</div>
@endsection