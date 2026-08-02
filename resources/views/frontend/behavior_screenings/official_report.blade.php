@extends('admin_client.admin_client')

@section('content')

@php
    $groups = [
        'learning' => [
            'title' => 'ภาวะเรียนรู้ช้า',
            'score' => $screening->learning_score,
            'risk' => $screening->learning_risk,
        ],
        'ld' => [
            'title' => 'ภาวะแอลดี',
            'score' => $screening->ld_score,
            'risk' => $screening->ld_risk,
        ],
        'adhd' => [
            'title' => 'ภาวะสมาธิสั้น',
            'score' => $screening->adhd_score,
            'risk' => $screening->adhd_risk,
        ],
        'autism' => [
            'title' => 'ภาวะออทิสติก',
            'score' => $screening->autism_score,
            'risk' => $screening->autism_risk,
        ],
    ];

    $itemsByCategory = $screening->items->groupBy('category');
@endphp

<style>
    /* Behavior screening official report medium typography v2026-08-01 */
    .official-page{
        background:#f1f5f9;
        padding:24px 0;
    }

    .official-sheet{
        width:210mm;
        min-height:297mm;
        margin:auto;
        background:#fff;
        padding:16mm 12mm 14mm;
        color:#111827;
        font-family:"TH Sarabun New","Sarabun",sans-serif;
        font-size:16px;
        line-height:1.18;
        box-shadow:0 10px 30px rgba(15,23,42,.12);
        overflow:visible;
    }

    .official-toolbar{
        width:210mm;
        margin:0 auto 12px;
        display:flex;
        justify-content:space-between;
        gap:10px;
    }

    .official-report-head{
        margin:0 0 6px;
        text-align:center;
    }

    .official-title{
        text-align:center;
        font-size:21px;
        font-weight:800;
        margin:0 0 1px;
        line-height:1.08;
    }

    .official-subtitle{
        text-align:center;
        font-size:17px;
        font-weight:600;
        margin:0;
        line-height:1.08;
    }

    .official-info{
        display:grid;
        grid-template-columns:minmax(0, 1fr) minmax(0, 1fr);
        column-gap:18px;
        row-gap:0;
        margin-top:12px !important;
        margin-bottom:5px !important;
        font-size:14px !important;
        line-height:1.08 !important;
    }

    .official-info > div{
        min-height:22px;
        white-space:nowrap;
    }

    .official-line{
        border-bottom:1px dotted #111;
        display:inline-block;
        min-width:105px;
        padding:0 5px 1px;
        line-height:1.02;
        font-weight:500;
    }

    .official-table{
        width:100%;
        border-collapse:collapse;
        margin-top:4px;
        font-size:13.5px;
        line-height:1.15;
    }

    .official-table th,
    .official-table td{
        border:1px solid #111;
        padding:3px 5px;
        vertical-align:top;
    }

    .official-table th{
        text-align:center;
        font-weight:700;
        background:#f8fafc;
    }

    .official-section-row td{
        background:#e5e7eb;
        font-weight:700;
    }

    .text-center{
        text-align:center;
    }

    .check-cell{
        font-size:16px;
        font-weight:700;
        text-align:center;
        width:40px;
        line-height:1;
    }

    .official-summary{
        margin-top:8px;
        border:1px solid #111;
        font-size:13.5px;
        line-height:1.18;
        break-inside:avoid;
        page-break-inside:avoid;
    }

    .official-summary-title{
        padding:4px 7px;
        font-weight:700;
        border-bottom:1px solid #111;
        background:#f8fafc;
    }

    .official-summary-body{
        padding:5px 8px;
        white-space:pre-line;
        min-height:30px;
    }

    .official-score-table{
        width:100%;
        border-collapse:collapse;
        margin-top:7px;
        font-size:13.5px;
        line-height:1.15;
    }

    .official-score-table th,
    .official-score-table td{
        border:1px solid #111;
        padding:3px 6px;
    }

    .official-sign{
        margin-top:16px;
        display:block;
        width:100%;
        text-align:right;
        font-size:13.5px;
        line-height:1.4;
        break-inside:avoid;
        page-break-inside:avoid;
    }

    .official-sign-box{
        width:245px;
        margin-left:auto;
        text-align:center;
        break-inside:avoid;
        page-break-inside:avoid;
    }

    @media (max-width: 900px){
        .official-toolbar,
        .official-sheet{
            width:calc(100% - 24px);
        }

        .official-sheet{
            min-height:auto;
            padding:18px;
        }
    }

    @media (max-width: 576px){
        .official-info{
            grid-template-columns:1fr;
        }

        .official-info > div{
            white-space:normal;
        }
    }

    @media print{
        html,
        body{
            background:#fff !important;
            margin:0 !important;
            padding:0 !important;
        }

        body{
            -webkit-print-color-adjust:exact;
            print-color-adjust:exact;
        }

        .official-page{
            padding:0 !important;
            margin:0 !important;
            background:#fff !important;
            overflow:visible !important;
        }

        .official-toolbar,
        .main-header,
        .left-side-bar,
        .navbar-custom,
        .footer{
            display:none !important;
        }

        .official-sheet{
            width:100% !important;
            min-height:auto !important;
            margin:0 !important;
            padding:2mm 0 0 !important;
            box-shadow:none !important;
            border:none !important;
            overflow:visible !important;
            position:static !important;
            font-size:15px !important;
            line-height:1.08 !important;
        }

        .official-report-head{
            margin:0 0 5px !important;
            padding:0 !important;
        }

        .official-title{
            font-size:19px !important;
            line-height:1.02 !important;
            margin:0 !important;
        }

        .official-subtitle{
            font-size:15px !important;
            line-height:1.02 !important;
            margin:0 !important;
        }

        .official-info{
            display:grid !important;
            grid-template-columns:minmax(0, 1fr) minmax(0, 1fr) !important;
            column-gap:16px !important;
            row-gap:0 !important;
            margin-top:8px !important;
            margin-bottom:4px !important;
            font-size:12px !important;
            line-height:1.02 !important;
        }

        .official-info > div{
            min-height:18px !important;
            white-space:nowrap !important;
        }

        .official-line{
            min-width:95px !important;
            padding:0 4px 1px !important;
            line-height:1 !important;
        }

        .official-table{
            margin-top:3px !important;
            font-size:12px !important;
            line-height:1.04 !important;
        }

        .official-table th,
        .official-table td{
            padding:2px 4px !important;
        }

        .check-cell{
            font-size:14px !important;
            width:34px !important;
        }

        .official-score-table{
            margin-top:8px !important;
            font-size:12px !important;
            line-height:1.04 !important;
        }

        .official-score-table th,
        .official-score-table td{
            padding:2px 4px !important;
        }

        .official-summary{
            margin-top:8px !important;
            font-size:12px !important;
            line-height:1.06 !important;
        }

        .official-summary-title{
            padding:2px 5px !important;
        }

        .official-summary-body{
            min-height:22px !important;
            padding:3px 6px !important;
        }

        .official-sign{
            display:block !important;
            margin-top:10px !important;
            font-size:12px !important;
            line-height:1.25 !important;
            break-inside:avoid !important;
            page-break-inside:avoid !important;
        }

        .official-sign-box{
            width:220px !important;
            margin-left:auto !important;
            break-inside:avoid !important;
            page-break-inside:avoid !important;
        }

        .official-table thead{
            display:table-header-group;
        }

        .official-table tr,
        .official-score-table tr{
            break-inside:avoid;
            page-break-inside:avoid;
        }

        @page{
            size:A4 portrait;
            margin:12mm 9mm 8mm;
        }
    }
</style>

<div class="official-page">

    <div class="official-toolbar">
        <a href="{{ route('behavior-screenings.show', $screening->id) }}"
           class="btn btn-light border">
            <i class="bi bi-arrow-left"></i>
            กลับ
        </a>

        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer"></i>
            พิมพ์รายงาน
        </button>
    </div>

    <div class="official-sheet">

        <div class="official-title">
            แบบสังเกตพฤติกรรม 4 โรค
        </div>

        <div class="official-subtitle">
            สำหรับคัดกรองพฤติกรรมเบื้องต้น
        </div>

        <div class="official-info">
            <div>
                ชื่อ - สกุล
                <span class="official-line">
                    {{ $client->first_name }} {{ $client->last_name }}
                </span>
            </div>

            <div>
                อายุ
                <span class="official-line">
                    {{ $screening->age_text ?: '-' }}
                </span>
            </div>

            <div>
                ชั้นเรียน
                <span class="official-line">
                    {{ $screening->class_level ?: '-' }}
                </span>
            </div>

            <div>
                เลขทะเบียน
                <span class="official-line">
                    {{ $client->register_number ?? '-' }}
                </span>
            </div>

            <div>
                วันที่ประเมิน
                <span class="official-line">
                    {{ $screening->screening_date?->format('d/m/Y') }}
                </span>
            </div>

            <div>
                ผู้ประเมิน
                <span class="official-line">
                    {{ $screening->observer_name ?: '-' }}
                </span>
            </div>
        </div>

        <table class="official-table">
            <thead>
                <tr>
                    <th style="width:55px;">ข้อ</th>
                    <th>รายการสังเกตพฤติกรรม</th>
                    <th style="width:55px;">ใช่</th>
                    <th style="width:65px;">ไม่ใช่</th>
                </tr>
            </thead>

            <tbody>
                @foreach($groups as $category => $group)
                    <tr class="official-section-row">
                        <td colspan="4">
                            {{ $group['title'] }}
                        </td>
                    </tr>

                    @foreach(($itemsByCategory[$category] ?? collect()) as $item)
                        <tr>
                            <td class="text-center">{{ $item->item_no }}</td>
                            <td>{{ $item->question }}</td>
                            <td class="check-cell">{{ $item->answer ? '✓' : '' }}</td>
                            <td class="check-cell">{{ ! $item->answer ? '✓' : '' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <table class="official-score-table">
            <thead>
                <tr>
                    <th>ด้านที่ประเมิน</th>
                    <th style="width:100px;">คะแนนรวม</th>
                    <th style="width:180px;">ผลการประเมิน</th>
                </tr>
            </thead>

            <tbody>
                @foreach($groups as $group)
                    <tr>
                        <td>{{ $group['title'] }}</td>
                        <td class="text-center">{{ $group['score'] }}</td>
                        <td class="text-center">
                            {{ $group['risk'] ? 'มีความเสี่ยง' : 'ไม่พบความเสี่ยง' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="official-summary">
            <div class="official-summary-title">
                สรุปผลการประเมิน
            </div>
            <div class="official-summary-body">
                {{ $screening->summary }}
            </div>
        </div>

        <div class="official-summary">
            <div class="official-summary-title">
                คำแนะนำ
            </div>
            <div class="official-summary-body">
                {{ $screening->recommendation }}
            </div>
        </div>

        @if($screening->remark)
            <div class="official-summary">
                <div class="official-summary-title">
                    หมายเหตุ
                </div>
                <div class="official-summary-body">
                    {{ $screening->remark }}
                </div>
            </div>
        @endif

        <div class="official-sign">
            <div class="official-sign-box">
                ลงชื่อ........................................<br>
                (................................................)<br>
                วันที่........../........../..........
            </div>
        </div>

    </div>
</div>

@endsection