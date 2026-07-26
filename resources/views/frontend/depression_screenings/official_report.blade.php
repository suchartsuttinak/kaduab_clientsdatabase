@extends('admin_client.admin_client')

@section('content')

<style>
    .official-page{
        background:#f1f5f9;
        padding:24px 0;
    }

    .official-toolbar{
        width:210mm;
        margin:0 auto 12px;
        display:flex;
        justify-content:space-between;
        gap:10px;
    }

    .official-sheet{
        width:210mm;
        margin:auto;
        background:#fff;
        padding:8mm 9mm 9mm;
        color:#111827;
        font-family:"TH Sarabun New","Sarabun",sans-serif;
        font-size:16px;
        line-height:1.15;
        box-shadow:0 10px 30px rgba(15,23,42,.12);
    }

    .official-title{
        text-align:center;
        font-size:21px;
        font-weight:800;
        margin:0 0 2px;
        line-height:1.05;
    }

    .official-subtitle{
        text-align:center;
        font-size:16px;
        font-weight:600;
        margin:0 0 4px;
        line-height:1.1;
    }

    .official-desc{
        text-align:center;
        font-size:14px;
        margin-bottom:6px;
        color:#374151;
    }

    .official-info{
        display:grid;
        grid-template-columns:repeat(3, 1fr);
        gap:2px 10px;
        margin:5px 0;
        line-height:1.05;
        font-size:15px;
    }

    .official-line{
        border-bottom:1px dotted #111;
        display:inline-block;
        min-width:80px;
        padding:0 4px 1px;
        line-height:1.05;
        font-weight:700;
    }

    .official-note{
        border:1px solid #111;
        padding:4px 6px;
        margin:5px 0;
        font-size:15px;
        line-height:1.16;
    }

    .official-table{
        width:100%;
        border-collapse:collapse;
        margin-top:4px;
    }

    .official-table th,
    .official-table td{
        border:1px solid #111;
        padding:2px 4px;
        vertical-align:middle;
    }

    .official-table th{
        text-align:center;
        font-weight:700;
        background:#f8fafc;
        line-height:1.08;
        font-size:14px;
    }

    .question-no{
        width:32px;
        text-align:center;
        font-weight:700;
    }

    .question-text{
        line-height:1.12;
        font-size:14.5px;
    }

    .choice-cell{
        width:48px;
        text-align:center;
        font-size:16px;
        font-weight:800;
        line-height:1;
    }

    .score-table{
        width:100%;
        border-collapse:collapse;
        margin-top:6px;
        font-size:15px;
    }

    .score-table th,
    .score-table td{
        border:1px solid #111;
        padding:3px 6px;
    }

    .score-table th{
        background:#f8fafc;
        text-align:center;
    }

    .official-summary{
        border:1px solid #111;
        margin-top:5px;
        page-break-inside:avoid;
    }

    .official-summary-title{
        border-bottom:1px solid #111;
        background:#f8fafc;
        padding:3px 6px;
        font-weight:700;
        font-size:15px;
    }

    .official-summary-body{
        padding:4px 6px;
        min-height:22px;
        white-space:pre-line;
        line-height:1.22;
        font-size:14.5px;
    }

    .official-sign{
        margin-top:10px;
        display:flex;
        justify-content:flex-end;
        page-break-inside:avoid;
    }

    .official-sign-box{
        width:240px;
        text-align:center;
        line-height:1.25;
        font-size:15px;
    }

    .text-center{
        text-align:center;
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
            padding:0 !important;
            box-shadow:none !important;
            border:none !important;
            font-size:15px !important;
            line-height:1.08 !important;
        }

        .official-title{
            font-size:20px !important;
            margin:0 0 1px !important;
            line-height:1 !important;
        }

        .official-subtitle{
            font-size:15px !important;
            margin:0 0 3px !important;
            line-height:1.02 !important;
        }

        .official-desc{
            font-size:13px !important;
            margin-bottom:4px !important;
        }

        .official-info{
            gap:1px 10px !important;
            margin:4px 0 !important;
            line-height:1.02 !important;
            font-size:14px !important;
        }

        .official-line{
            min-width:70px !important;
            padding:0 3px !important;
        }

        .official-note{
            padding:3px 5px !important;
            margin:4px 0 !important;
            font-size:14px !important;
            line-height:1.1 !important;
        }

        .official-table{
            margin-top:3px !important;
        }

        .official-table th,
        .official-table td{
            padding:1.5px 3px !important;
        }

        .official-table th{
            font-size:13px !important;
            line-height:1.02 !important;
        }

        .question-no{
            width:30px !important;
        }

        .question-text{
            font-size:13.5px !important;
            line-height:1.08 !important;
        }

        .choice-cell{
            width:44px !important;
            font-size:15px !important;
        }

        .score-table{
            margin-top:4px !important;
            font-size:14px !important;
        }

        .score-table th,
        .score-table td{
            padding:2px 5px !important;
        }

        .official-summary{
            margin-top:4px !important;
        }

        .official-summary-title{
            padding:2px 5px !important;
            font-size:14px !important;
        }

        .official-summary-body{
            padding:3px 5px !important;
            min-height:18px !important;
            line-height:1.12 !important;
            font-size:13.5px !important;
        }

        .official-sign{
            margin-top:8px !important;
        }

        .official-sign-box{
            font-size:14px !important;
            line-height:1.18 !important;
        }

        @page{
            size:A4 portrait;
            margin:5mm;
        }
    }
</style>

<div class="official-page">

    <div class="official-toolbar">
        <a href="{{ route('depression-screenings.show', $screening->id) }}"
           class="btn btn-light border">
            <i class="bi bi-arrow-left"></i>
            กลับ
        </a>

        <button onclick="window.print()"
                class="btn btn-danger">
            <i class="bi bi-printer"></i>
            พิมพ์รายงาน
        </button>
    </div>

    <div class="official-sheet">

        <div class="official-title">
            แบบคัดกรองภาวะซึมเศร้าในวัยรุ่น
        </div>

        <div class="official-subtitle">
            Center for Epidemiologic Studies-Depression Scale (CES-D) ฉบับภาษาไทย
        </div>

        <div class="official-desc">
            ผู้พัฒนา: ศ.พญ.อุมาพร ตรังคสมบัติ ภาควิชาจิตเวชศาสตร์ จุฬาลงกรณ์มหาวิทยาลัย
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
                วันที่คัดกรอง
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

        <div class="official-note">
            <strong>คำชี้แจง:</strong>
            ผู้รับบริการมีความรู้สึกดังต่อไปนี้บ่อยเพียงใดใน 1 สัปดาห์ที่ผ่านมา
            กรุณาเลือกคำตอบที่ตรงกับความรู้สึกมากที่สุด
        </div>

        <table class="official-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width:32px;">ข้อ</th>
                    <th rowspan="2">ข้อคำถาม</th>
                    <th colspan="4">ระดับความรู้สึก</th>
                </tr>
                <tr>
                    <th style="width:48px;">ไม่เลย<br>0</th>
                    <th style="width:48px;">นานๆ ครั้ง<br>1</th>
                    <th style="width:48px;">บ่อยๆ<br>2</th>
                    <th style="width:48px;">ตลอดเวลา<br>3</th>
                </tr>
            </thead>

            <tbody>
                @foreach($screening->items as $item)
                    @php
                        $rawScore = $item->is_reverse
                            ? 3 - (int) $item->score
                            : (int) $item->score;
                    @endphp

                    <tr>
                        <td class="question-no">
                            {{ $item->item_no }}
                        </td>

                        <td class="question-text">
                            {{ $item->question }}
                        </td>

                        @for($score = 0; $score <= 3; $score++)
                            <td class="choice-cell">
                                {{ $rawScore === $score ? '✓' : '' }}
                            </td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="score-table">
            <tr>
                <th style="width:130px;">คะแนนรวม</th>
                <td class="text-center" style="width:70px;">
                    <strong>{{ $screening->total_score }}</strong>
                </td>
                <th style="width:150px;">ผลการคัดกรอง</th>
                <td>
                    {{ $screening->result_level }}
                </td>
            </tr>
        </table>

        <div class="official-summary">
            <div class="official-summary-title">
                การแปลผล
            </div>

            <div class="official-summary-body">
                แบบคัดกรอง CES-D ให้คะแนน 0-3 คะแนนต่อข้อ คะแนนรวมตั้งแต่ 22 คะแนนขึ้นไป อยู่ในเกณฑ์ควรเฝ้าระวังภาวะซึมเศร้า
            </div>
        </div>

        <div class="official-summary">
            <div class="official-summary-title">
                สรุปผลการคัดกรอง
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
                ลงชื่อ........................................ผู้ประเมิน<br>
                (................................................)<br>
                วันที่........../........../..........
            </div>
        </div>

    </div>

</div>

@endsection