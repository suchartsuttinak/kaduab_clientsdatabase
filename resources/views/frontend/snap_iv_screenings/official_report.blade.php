@extends('admin_client.admin_client')

@section('content')

<style>

.snap-official-page{
    background:#f1f5f9;
    padding:24px 0;
}

.snap-official-toolbar{
    width:210mm;
    margin:0 auto 12px;
    display:flex;
    justify-content:space-between;
    gap:10px;
}

.snap-official-sheet{
    width:210mm;
    min-height:297mm;
    margin:auto;
    background:#fff;
    padding:12mm;
    font-family:"TH Sarabun New","Sarabun",sans-serif;
    font-size:16px;
    line-height:1.32;
    color:#111827;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.snap-official-head{
    text-align:center;
    margin-bottom:14px;
}

.snap-official-title{
    font-size:22px;
    font-weight:700;
    line-height:1.15;
}

.snap-official-subtitle{
    font-size:16px;
    margin-top:2px;
}

.snap-official-info{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:3px 18px;
    margin-bottom:12px;
    font-size:15px;
    line-height:1.28;
}

.snap-line{
    border-bottom:1px dotted #111;
    display:inline-block;
    min-width:105px;
    padding:0 4px;
    font-size:15px;
    line-height:1.25;
}

.snap-table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}

.snap-table th,
.snap-table td{
    border:1px solid #111;
    padding:5px 7px;
    vertical-align:top;
}

.snap-table th{
    background:#f8fafc;
    text-align:center;
    font-weight:700;
}

.snap-score-table{
    width:100%;
    border-collapse:collapse;
    margin-top:14px;
}

.snap-score-table th,
.snap-score-table td{
    border:1px solid #111;
    padding:6px 8px;
}

.snap-score-table th{
    background:#f8fafc;
    text-align:center;
}

.snap-box{
    border:1px solid #111;
    margin-top:14px;
}

.snap-box-head{
    padding:6px 8px;
    font-weight:700;
    border-bottom:1px solid #111;
    background:#f8fafc;
}

.snap-box-body{
    padding:10px;
    white-space:pre-line;
    min-height:48px;
}

.snap-sign{
    display:block;
    width:100%;
    margin-top:22px;
    padding-top:8px;
    min-height:96px;
    clear:both;
    page-break-inside:avoid;
    break-inside:avoid;
}

.snap-sign-box{
    display:block;
    width:250px;
    margin-left:auto;
    text-align:center;
    font-size:15px;
    line-height:1.65;
}

@media print{

    html,
    body{
        margin:0 !important;
        padding:0 !important;
        background:#fff !important;
    }

    body{
        -webkit-print-color-adjust:exact;
        print-color-adjust:exact;
    }

    .snap-official-toolbar,
    .main-header,
    .left-side-bar,
    .navbar-custom,
    .footer{
        display:none !important;
    }

    .snap-official-page{
        padding:0 !important;
        margin:0 !important;
        background:#fff !important;
    }

    .wrapper,
    .content-wrapper,
    .main-content,
    .container-fluid,
    .snap-official-page,
    .snap-official-sheet{
        height:auto !important;
        min-height:0 !important;
        max-height:none !important;
        overflow:visible !important;
    }

    .snap-official-sheet{
        width:100% !important;
        margin:0 !important;
        padding:0 !important;
        box-shadow:none !important;
        border:none !important;
        font-size:15px !important;
        line-height:1.28 !important;
    }

    .snap-official-title{
        font-size:20px !important;
    }

    .snap-official-subtitle{
        font-size:15px !important;
    }

    .snap-official-info,
    .snap-official-info > div,
    .snap-line{
        font-size:14px !important;
        line-height:1.22 !important;
    }

    .snap-official-info{
        gap:2px 16px !important;
        margin-bottom:10px !important;
    }

    .snap-sign{
        display:block !important;
        width:100% !important;
        margin-top:18px !important;
        padding-top:8px !important;
        min-height:92px !important;
        clear:both !important;
        page-break-inside:avoid !important;
        break-inside:avoid-page !important;
    }

    .snap-sign-box{
        display:block !important;
        width:235px !important;
        margin-left:auto !important;
        text-align:center !important;
        font-size:14px !important;
        line-height:1.6 !important;
    }


    .snap-table th,
    .snap-table td,
    .snap-score-table th,
    .snap-score-table td{
        padding:3px 5px !important;
        font-size:14px !important;
        line-height:1.2 !important;
    }

    .snap-box{
        margin-top:9px !important;
    }

    .snap-box-head{
        padding:4px 6px !important;
        font-size:14px !important;
    }

    .snap-box-body{
        padding:6px !important;
        min-height:36px !important;
        font-size:14px !important;
        line-height:1.25 !important;
    }

    @page{
        size:A4 portrait;
        margin:8mm 10mm;
    }

}

</style>

<div class="snap-official-page">

    <div class="snap-official-toolbar">

        <a href="{{ route('snap-iv.show', $screening->id) }}"
           class="btn btn-light border">

            <i class="bi bi-arrow-left"></i>
            กลับ

        </a>

        <button onclick="window.print()"
                class="btn btn-primary">

            <i class="bi bi-printer"></i>
            พิมพ์รายงาน

        </button>

    </div>

    <div class="snap-official-sheet">

        <div class="snap-official-head">

            <div class="snap-official-title">
                แบบประเมินพฤติกรรม SNAP-IV
            </div>

            <div class="snap-official-subtitle">
                (Short Form)
            </div>

        </div>

        <div class="snap-official-info">

            <div>
                ชื่อ - สกุล
                <span class="snap-line">
                    {{ $client->first_name }} {{ $client->last_name }}
                </span>
            </div>

            <div>
                อายุ
                <span class="snap-line">
                    {{ $screening->age_text ?: '-' }}
                </span>
            </div>

            <div>
                ชั้นเรียน
                <span class="snap-line">
                    {{ $screening->class_level ?: '-' }}
                </span>
            </div>

            <div>
                วันที่ประเมิน
                <span class="snap-line">
                    {{ $screening->screening_date?->format('d/m/Y') }}
                </span>
            </div>

            <div>
                ผู้ประเมิน
                <span class="snap-line">
                    {{ $screening->observer_name ?: '-' }}
                </span>
            </div>

            <div>
                ความสัมพันธ์
                <span class="snap-line">
                    {{ $screening->relationship ?: '-' }}
                </span>
            </div>

            <div>
                ภาคเรียน
                <span class="snap-line">
                    {{ $screening->term ?: (data_get($latestEducationRecord, 'semester_label') ?: data_get($latestEducationRecord, 'semester.semester_name', '-')) }}
                </span>
            </div>

            <div>
                ผลการเรียนเฉลี่ย
                <span class="snap-line">
                    {{ $screening->grade_average !== null && $screening->grade_average !== '' ? $screening->grade_average : (data_get($latestEducationRecord, 'grade_average') ?? '-') }}
                </span>
            </div>

        </div>

        <table class="snap-table">

            <thead>

                <tr>
                    <th width="70">
                        ข้อ
                    </th>

                    <th>
                        รายการประเมิน
                    </th>

                    <th width="80">
                        คะแนน
                    </th>
                </tr>

            </thead>

            <tbody>

                @foreach($screening->items as $item)

                    <tr>

                        <td class="text-center">
                            {{ $item->item_no }}
                        </td>

                        <td>
                            {{ $item->question }}
                        </td>

                        <td class="text-center fw-bold">
                            {{ $item->score }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

        <table class="snap-score-table">

            <thead>

                <tr>
                    <th>
                        ด้านที่ประเมิน
                    </th>

                    <th width="120">
                        คะแนน
                    </th>

                    <th width="220">
                        ผลการแปลผล
                    </th>
                </tr>

            </thead>

            <tbody>

                <tr>

                    <td>
                        อาการขาดสมาธิ
                    </td>

                    <td class="text-center">
                        {{ $screening->inattention_score }}
                    </td>

                    <td class="text-center">
                        {{ $screening->inattention_level }}
                    </td>

                </tr>

                <tr>

                    <td>
                        อาการซน อยู่ไม่นิ่ง และหุนหันพลันแล่น
                    </td>

                    <td class="text-center">
                        {{ $screening->hyperactivity_score }}
                    </td>

                    <td class="text-center">
                        {{ $screening->hyperactivity_level }}
                    </td>

                </tr>

                <tr>

                    <td>
                        อาการดื้อและต่อต้าน
                    </td>

                    <td class="text-center">
                        {{ $screening->oppositional_score }}
                    </td>

                    <td class="text-center">
                        {{ $screening->oppositional_level }}
                    </td>

                </tr>

            </tbody>

        </table>

        <div class="snap-box">

            <div class="snap-box-head">
                สรุปผลการประเมิน
            </div>

            <div class="snap-box-body">
                {{ $screening->summary }}
            </div>

        </div>

        <div class="snap-box">

            <div class="snap-box-head">
                คำแนะนำ
            </div>

            <div class="snap-box-body">
                {{ $screening->recommendation }}
            </div>

        </div>

        @if($screening->remark)

            <div class="snap-box">

                <div class="snap-box-head">
                    หมายเหตุเพิ่มเติม
                </div>

                <div class="snap-box-body">
                    {{ $screening->remark }}
                </div>

            </div>

        @endif

        <div class="snap-sign">

            <div class="snap-sign-box">

                ลงชื่อ........................................
                <br>

                (................................................)

                <br>

                วันที่........../........../..........

            </div>

        </div>

    </div>

</div>

@endsection