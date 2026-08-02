@extends('admin_client.admin_client')

@section('content')

<style>
    .ds-show-page{
        padding:24px 0;
    }

    .ds-show-shell{
        max-width:1200px;
        margin:auto;
    }

    .ds-toolbar{
        display:flex;
        justify-content:space-between;
        gap:12px;
        flex-wrap:wrap;
        margin-bottom:20px;
    }

    .ds-card{
        border:none;
        border-radius:20px;
        overflow:hidden;
        box-shadow:0 10px 30px rgba(15,23,42,.07);
        background:#fff;
    }

    .ds-header{
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.22), transparent 34%),
            linear-gradient(135deg,#dc2626,#991b1b);
        color:#fff;
        padding:28px;
    }

    .ds-title{
        font-size:1.6rem;
        font-weight:800;
        margin-bottom:6px;
    }

    .ds-subtitle{
        opacity:.92;
        line-height:1.7;
    }

    .ds-body{
        padding:28px;
    }

    .ds-client-strip{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:18px;
        flex-wrap:wrap;
        padding:0 0 18px;
        margin-bottom:20px;
        border-bottom:1px solid #eef2f7;
    }

    .ds-client-main{
        display:flex;
        align-items:flex-start;
        gap:16px;
    }

    .ds-client-avatar{
        width:56px;
        height:56px;
        border-radius:18px;
        background:linear-gradient(135deg,#fee2e2,#fecaca);
        color:#991b1b;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:24px;
        flex:0 0 auto;
    }

    .ds-client-name{
        font-size:1.28rem;
        font-weight:800;
        color:#0f172a;
        margin-bottom:6px;
        line-height:1.35;
    }

    .ds-client-meta{
        display:flex;
        flex-wrap:wrap;
        gap:10px 18px;
        color:#64748b;
        font-size:.93rem;
        line-height:1.7;
    }

    .ds-client-meta span{
        display:inline-flex;
        align-items:center;
        gap:6px;
        white-space:nowrap;
    }

    .ds-client-meta strong{
        color:#0f172a;
        font-weight:700;
    }

    .ds-score-panel{
        border-radius:20px;
        padding:24px;
        color:#fff;
        margin-bottom:24px;
        background:linear-gradient(135deg,#16a34a,#15803d);
    }

    .ds-score-panel.risk{
        background:linear-gradient(135deg,#dc2626,#991b1b);
    }

    .ds-score-label{
        font-size:.95rem;
        opacity:.9;
        margin-bottom:8px;
    }

    .ds-score-value{
        font-size:3rem;
        font-weight:900;
        line-height:1;
    }

    .ds-score-result{
        margin-top:12px;
        font-size:1.05rem;
        font-weight:700;
    }

    .ds-section{
        border:1px solid #e5e7eb;
        border-radius:18px;
        overflow:hidden;
        margin-bottom:22px;
    }

    .ds-section-head{
        background:#f8fafc;
        padding:14px 18px;
        font-weight:800;
        color:#0f172a;
        border-bottom:1px solid #e5e7eb;
    }

    .ds-section-body{
        padding:18px;
        line-height:1.8;
        white-space:pre-line;
    }

    .ds-table{
        margin:0;
        min-width:860px;
    }

    .ds-table th{
        background:#f8fafc;
        color:#0f172a;
        font-weight:800;
        text-align:center;
        vertical-align:middle;
        white-space:nowrap;
    }

    .ds-table td{
        vertical-align:middle;
    }

    .ds-choice{
        font-weight:700;
        color:#334155;
    }

    .ds-badge-score{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-width:34px;
        height:26px;
        padding:0 10px;
        border-radius:999px;
        background:#334155;
        color:#fff;
        font-weight:800;
        font-size:.85rem;
    }

    @media (max-width:768px){
        .ds-client-main{
            width:100%;
        }

        .ds-client-meta{
            flex-direction:column;
            gap:4px;
        }

        .ds-client-meta span{
            white-space:normal;
        }
    }

    @media print{
        .ds-toolbar,
        .main-header,
        .left-side-bar,
        .navbar-custom,
        .footer{
            display:none !important;
        }

        body{
            background:#fff !important;
            -webkit-print-color-adjust:exact;
            print-color-adjust:exact;
        }

        .container-fluid,
        .ds-show-page{
            padding:0 !important;
            margin:0 !important;
        }

        .ds-show-shell{
            max-width:100% !important;
        }

        .ds-card{
            box-shadow:none !important;
            border-radius:0 !important;
        }

        .ds-header{
            background:#fff !important;
            color:#111827 !important;
            padding:0 0 10px !important;
            border-bottom:2px solid #111827;
            text-align:center;
        }

        .ds-title{
            font-size:22px !important;
            margin:0 0 4px !important;
        }

        .ds-subtitle{
            font-size:15px !important;
            color:#374151 !important;
            opacity:1 !important;
        }

        .ds-body{
            padding:12px 0 0 !important;
        }

        .ds-client-strip{
            display:block !important;
            border-bottom:1px solid #d1d5db !important;
            padding:0 0 8px !important;
            margin-bottom:10px !important;
        }

        .ds-client-avatar{
            display:none !important;
        }

        .ds-client-main{
            display:block !important;
        }

        .ds-client-name{
            font-size:16px !important;
            margin-bottom:4px !important;
        }

        .ds-client-meta{
            display:grid !important;
            grid-template-columns:repeat(3,1fr) !important;
            gap:3px 14px !important;
            font-size:14px !important;
            line-height:1.25 !important;
            color:#111827 !important;
        }

        .ds-client-meta span{
            display:block !important;
            white-space:normal !important;
        }

        .ds-client-meta i{
            display:none !important;
        }

        .ds-client-meta strong{
            color:#111827 !important;
            font-weight:700 !important;
        }

        .ds-score-panel,
        .ds-score-panel.risk{
            background:#fff !important;
            color:#111827 !important;
            border:1px solid #111827;
            border-radius:0 !important;
            padding:8px !important;
        }

        .ds-score-value{
            font-size:24px !important;
        }

        .ds-section{
            border:1px solid #111827 !important;
            border-radius:0 !important;
            margin-bottom:10px !important;
            page-break-inside:avoid;
        }

        .ds-section-head{
            background:#f3f4f6 !important;
            border-bottom:1px solid #111827 !important;
            padding:6px 8px !important;
            font-size:15px !important;
        }

        .ds-section-body{
            padding:8px !important;
            font-size:15px !important;
            line-height:1.55 !important;
        }

        table{
            font-size:14px !important;
        }

        @page{
            size:A4 portrait;
            margin:10mm;
        }
    }
</style>

<div class="container-fluid ds-show-page">
    <div class="ds-show-shell">
        <div class="ds-toolbar">
            <a href="{{ route('depression-screenings.index', $client->id) }}"
               class="btn btn-light border">
                <i class="bi bi-arrow-left"></i>
                กลับ
            </a>

        <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('depression-screenings.official-report', $screening->id) }}"
                    class="btn btn-outline-danger">
                        <i class="bi bi-file-earmark-text"></i>
                       รูปแบบรายงาน
                    </a>

                    <a href="{{ route('depression-screenings.official-report', $screening->id) . '?print=1' }}"
                    class="btn btn-danger">
                        <i class="bi bi-printer"></i>
                        พิมพ์รายงาน
                    </a>
                </div>
        </div>

        <div class="ds-card">

            <div class="ds-header">
                <div class="ds-title">
                    รายงานแบบคัดกรองภาวะซึมเศร้าในวัยรุ่น
                </div>

                <div class="ds-subtitle">
                    Center for Epidemiologic Studies-Depression Scale (CES-D) ฉบับภาษาไทย
                </div>
            </div>

            <div class="ds-body">

              <div class="ds-client-strip">

    <div class="ds-client-main">

        <div class="ds-client-avatar">
            <i class="bi bi-person-vcard"></i>
        </div>

        <div>

            <div class="ds-client-name">
                {{ $client->first_name }} {{ $client->last_name }}
            </div>

            <div class="ds-client-meta">

                <span>
                    <i class="bi bi-upc-scan"></i>
                    เลขทะเบียน
                    <strong>{{ $client->register_number ?? '-' }}</strong>
                </span>

                <span>
                    <i class="bi bi-balloon-heart"></i>
                    อายุ
                    <strong>
                        {{ $screening->age_text ?: '-' }}
                    </strong>
                </span>

                <span>
                    <i class="bi bi-mortarboard"></i>
                    ชั้นเรียน
                    <strong>
                        {{ $screening->class_level ?: '-' }}
                    </strong>
                </span>

                  <span>
                    <i class="bi bi-calendar2-check"></i>
                    วันที่คัดกรอง
                    <strong>
                        {{ $screening->screening_date?->format('d/m/Y') }}
                    </strong>
                </span>

                <span>
                    <i class="bi bi-person-heart"></i>
                    ผู้ประเมิน
                    <strong>
                        {{ $screening->observer_name ?: '-' }}
                    </strong>
                </span>

            </div>

        </div>

    </div>

</div>

                <div class="ds-score-panel {{ $screening->total_score >= 22 ? 'risk' : '' }}">
                    <div class="ds-score-label">
                        คะแนนรวม CES-D
                    </div>

                    <div class="ds-score-value">
                        {{ $screening->total_score }}
                    </div>

                    <div class="ds-score-result">
                        {{ $screening->result_level }}
                    </div>
                </div>

                <div class="ds-section">
                    <div class="ds-section-head">
                        สรุปผลการคัดกรอง
                    </div>

                    <div class="ds-section-body">
                        {{ $screening->summary }}
                    </div>
                </div>

                <div class="ds-section">
                    <div class="ds-section-head">
                        คำแนะนำ
                    </div>

                    <div class="ds-section-body">
                        {{ $screening->recommendation }}
                    </div>
                </div>

                @if($screening->remark)
                    <div class="ds-section">
                        <div class="ds-section-head">
                            หมายเหตุเพิ่มเติม
                        </div>

                        <div class="ds-section-body">
                            {{ $screening->remark }}
                        </div>
                    </div>
                @endif

                <div class="ds-section">
                    <div class="ds-section-head">
                        รายละเอียดคำตอบรายข้อ
                    </div>

                    <div class="table-responsive">
                        <table class="table ds-table align-middle">
                            <thead>
                                <tr>
                                    <th style="width:70px;">ข้อ</th>
                                    <th>ข้อคำถาม</th>
                                    <th style="width:190px;">คำตอบ</th>
                                    <th style="width:90px;">คะแนน</th>
                                    <th style="width:110px;">หมายเหตุ</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($screening->items as $item)
                                    <tr>
                                        <td class="text-center fw-bold">
                                            {{ $item->item_no }}
                                        </td>

                                        <td>
                                            {{ $item->question }}
                                        </td>

                                        <td class="ds-choice text-center">
                                            {{ $item->choice_text }}
                                        </td>

                                        <td class="text-center">
                                            <span class="ds-badge-score">
                                                {{ $item->score }}
                                            </span>
                                        </td>

                                        <td class="text-center">
                                            @if($item->is_reverse)
                                                <span class="badge bg-light text-muted border">
                                                    ข้อกลับคะแนน
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection