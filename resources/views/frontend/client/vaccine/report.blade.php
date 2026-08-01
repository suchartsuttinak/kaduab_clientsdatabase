@extends('admin_client.admin_client')

@section('content')
@php
    $formatThaiDate = static function ($date): string {
        if (!$date) {
            return '-';
        }

        return \Carbon\Carbon::parse($date)
            ->locale('th')
            ->translatedFormat('d/m/')
            . (\Carbon\Carbon::parse($date)->year + 543);
    };

    $clientDisplayName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
    if ($clientDisplayName === '') {
        $clientDisplayName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
    }

    $clientAgeDisplay = filled($client->age ?? null)
        ? ($client->age . ' ปี')
        : '-';

    $startDateDisplay = request()->filled('start_date')
        ? $formatThaiDate(request('start_date'))
        : 'ทั้งหมด';

    $endDateDisplay = request()->filled('end_date')
        ? $formatThaiDate(request('end_date'))
        : 'ทั้งหมด';
@endphp

<style>
.vaccine-report-page{
    padding:16px 12px 28px;
    background:#eef2f7;
    color:#1f2937;
}

.vaccine-report-page *{
    box-sizing:border-box;
}

.vaccine-report-page .report-page{
    max-width:1280px;
    margin:0 auto;
    overflow:hidden;
    border:1px solid #e5e7eb;
    border-radius:16px;
    background:#fff;
    box-shadow:0 10px 30px rgba(15,23,42,.06);
}

.vaccine-report-page .report-toolbar{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    padding:14px 18px 0;
}

.vaccine-report-page .report-btn{
    display:inline-flex;
    min-height:40px;
    align-items:center;
    justify-content:center;
    gap:7px;
    padding:7px 14px;
    border:1px solid #d1d5db;
    border-radius:10px;
    background:#fff;
    color:#374151;
    font-size:14px;
    font-weight:700;
    text-decoration:none;
    cursor:pointer;
}

.vaccine-report-page .report-btn-primary{
    border-color:#2563eb;
    background:#2563eb;
    color:#fff;
}

.vaccine-report-page .report-wrap{
    padding:16px 18px 22px;
}

.vaccine-report-page .report-header{
    margin-bottom:14px;
    padding-bottom:12px;
    border-bottom:1px solid #e5e7eb;
    text-align:center;
}

.vaccine-report-page .report-title{
    margin:0;
    color:#111827;
    font-size:24px;
    font-weight:800;
    line-height:1.35;
}

.vaccine-report-page .report-subtitle{
    margin:4px 0 0;
    color:#6b7280;
    font-size:14px;
}

.vaccine-report-page .client-info{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:8px 18px;
    margin-bottom:12px;
}

.vaccine-report-page .client-info-item{
    color:#1f2937;
    font-size:15px;
}

.vaccine-report-page .client-info-label{
    margin-right:6px;
    color:#111827;
    font-weight:700;
}

.vaccine-report-page .report-meta{
    display:flex;
    justify-content:space-between;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:12px;
    padding:9px 12px;
    border:1px solid #e5e7eb;
    border-radius:12px;
    background:#f8fafc;
    font-size:14px;
}

.vaccine-report-page .table-wrap{
    overflow-x:auto;
}

.vaccine-report-page .report-table{
    width:100%;
    min-width:980px;
    border-collapse:collapse;
    table-layout:fixed;
}

.vaccine-report-page .report-table th,
.vaccine-report-page .report-table td{
    padding:7px 8px;
    border:1px solid #dfe5ec;
    font-size:14px;
    line-height:1.4;
    vertical-align:top;
    overflow-wrap:anywhere;
}

.vaccine-report-page .report-table th{
    background:#f1f5f9;
    color:#334155;
    font-weight:800;
    text-align:center;
    white-space:nowrap;
}

.vaccine-report-page .text-center{
    text-align:center;
}

.vaccine-report-page .no-col{width:55px;}
.vaccine-report-page .date-col{width:125px;}
.vaccine-report-page .vaccine-col{width:220px;}
.vaccine-report-page .hospital-col{width:220px;}
.vaccine-report-page .recorder-col{width:160px;}
.vaccine-report-page .remark-col{width:auto;}

.vaccine-report-page .empty-state{
    padding:32px 16px;
    border:1px dashed #d9e2ef;
    border-radius:14px;
    background:#fff;
    color:#64748b;
    text-align:center;
}

.vaccine-report-page .signature-wrap{
    display:flex;
    justify-content:flex-end;
    margin-top:30px;
}

.vaccine-report-page .signature-box{
    width:280px;
    color:#374151;
    font-size:15px;
    text-align:center;
}

.vaccine-report-page .signature-line{
    height:36px;
    margin-bottom:8px;
    padding-top:24px;
    border-bottom:1px solid #111827;
}

@media(max-width:768px){
    .vaccine-report-page{padding:0;}
    .vaccine-report-page .report-page{
        margin:0;
        border:0;
        border-radius:0;
        box-shadow:none;
    }
    .vaccine-report-page .report-toolbar,
    .vaccine-report-page .report-wrap{
        padding-left:14px;
        padding-right:14px;
    }
    .vaccine-report-page .client-info{grid-template-columns:1fr;}
    .vaccine-report-page .report-title{font-size:22px;}
}

@media print{
    @page{
        size:A4 landscape;
        margin:8mm;
    }

    html,
    body{
        width:auto !important;
        margin:0 !important;
        padding:0 !important;
        overflow:visible !important;
        background:#fff !important;
    }

    body{
        -webkit-print-color-adjust:exact !important;
        print-color-adjust:exact !important;
    }

    nav,
    header,
    footer,
    aside,
    .navbar,
    .sidebar,
    .main-footer,
    .footer,
    .app-footer,
    .content-footer,
    .page-footer,
    .report-toolbar{
        display:none !important;
    }

    .content-wrapper,
    .main-content,
    .page-content,
    .container,
    .container-fluid{
        width:100% !important;
        max-width:100% !important;
        margin:0 !important;
        padding:0 !important;
    }

    .vaccine-report-page,
    .vaccine-report-page .report-page{
        width:100% !important;
        max-width:100% !important;
        margin:0 !important;
        padding:0 !important;
        overflow:visible !important;
        border:0 !important;
        border-radius:0 !important;
        background:#fff !important;
        box-shadow:none !important;
    }

    .vaccine-report-page .report-wrap{padding:0 !important;}
    .vaccine-report-page .report-header{
        margin-bottom:8px !important;
        padding-bottom:6px !important;
    }
    .vaccine-report-page .report-title{
        font-size:20px !important;
        line-height:1.2 !important;
    }
    .vaccine-report-page .report-subtitle{
        margin-top:2px !important;
        font-size:12px !important;
    }
    .vaccine-report-page .client-info{
        gap:4px 12px !important;
        margin-bottom:8px !important;
    }
    .vaccine-report-page .client-info-item,
    .vaccine-report-page .report-meta{
        font-size:12px !important;
        line-height:1.3 !important;
    }
    .vaccine-report-page .report-meta{
        margin-bottom:8px !important;
        padding:6px 8px !important;
    }
    .vaccine-report-page .table-wrap{overflow:visible !important;}
    .vaccine-report-page .report-table{
        width:100% !important;
        min-width:0 !important;
        table-layout:fixed !important;
    }
    .vaccine-report-page .report-table thead{display:table-header-group;}
    .vaccine-report-page .report-table tr{page-break-inside:avoid;}
    .vaccine-report-page .report-table th,
    .vaccine-report-page .report-table td{
        padding:4px !important;
        font-size:11px !important;
        line-height:1.25 !important;
        white-space:normal !important;
    }
    .vaccine-report-page .no-col{width:38px !important;}
    .vaccine-report-page .date-col{width:90px !important;}
    .vaccine-report-page .vaccine-col{width:190px !important;}
    .vaccine-report-page .hospital-col{width:190px !important;}
    .vaccine-report-page .recorder-col{width:115px !important;}
    .vaccine-report-page .remark-col{width:auto !important;}
    .vaccine-report-page .signature-wrap{
        margin-top:18px !important;
        page-break-inside:avoid !important;
    }
    .vaccine-report-page .signature-box{
        width:250px !important;
        font-size:12px !important;
    }
    .vaccine-report-page .signature-line{
        height:28px !important;
        padding-top:16px !important;
    }
}
</style>

<div class="vaccine-report-page">
    <div class="report-page">
        <div class="report-toolbar">
            <a href="{{ route('vaccine.index', [
                    'client_id' => $client->id,
                    'start_date' => request('start_date'),
                    'end_date' => request('end_date'),
                ]) }}"
               class="report-btn">
                <i class="bi bi-arrow-left"></i>
                <span>กลับหน้ารายการ</span>
            </a>

            <button onclick="window.print()" type="button" class="report-btn report-btn-primary">
                <i class="bi bi-printer"></i>
                <span>พิมพ์รายงาน</span>
            </button>
        </div>

        <div class="report-wrap">
            <div class="report-header">
                <h1 class="report-title">รายงานประวัติการรับวัคซีน</h1>
                <p class="report-subtitle">สรุปข้อมูลการรับวัคซีนของผู้รับบริการ</p>
            </div>

            <div class="client-info">
                <div class="client-info-item">
                    <span class="client-info-label">ชื่อผู้รับบริการ:</span>
                    {{ $clientDisplayName }}
                </div>
                <div class="client-info-item">
                    <span class="client-info-label">อายุ:</span>
                    {{ $clientAgeDisplay }}
                </div>
            </div>

            <div class="report-meta">
                <div>
                    <strong>ช่วงวันที่:</strong>
                    {{ $startDateDisplay }} - {{ $endDateDisplay }}
                </div>
                <div>
                    <strong>จำนวน:</strong>
                    {{ number_format($vaccinations->count()) }} รายการ
                </div>
            </div>

            @if($vaccinations->isNotEmpty())
                <div class="table-wrap">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th class="no-col">ลำดับ</th>
                                <th class="date-col">วันที่รับวัคซีน</th>
                                <th class="vaccine-col">ชนิดวัคซีน</th>
                                <th class="hospital-col">สถานพยาบาล</th>
                                <th class="recorder-col">ผู้บันทึก</th>
                                <th class="remark-col">หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vaccinations as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">{{ $formatThaiDate($item->date) }}</td>
                                    <td>{{ $item->vaccine_name ?: '-' }}</td>
                                    <td>{{ $item->hospital ?: '-' }}</td>
                                    <td>{{ $item->recorder ?: '-' }}</td>
                                    <td>{{ $item->remark ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">ไม่พบข้อมูลวัคซีนตามเงื่อนไขที่เลือก</div>
            @endif

            <div class="signature-wrap">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>ผู้จัดทำรายงาน</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
