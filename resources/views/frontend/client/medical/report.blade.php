@extends('admin_client.admin_client')

@section('content')
@php
    $formatThaiDate = static function ($date): string {
        if (!$date) {
            return '-';
        }

        return \Carbon\Carbon::parse($date)->addYears(543)->format('d/m/Y');
    };

    $clientDisplayName = trim((string) ($client->fullname ?? $client->full_name ?? ''));
    if ($clientDisplayName === '') {
        $clientDisplayName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
    }
    $clientDisplayName = $clientDisplayName !== '' ? $clientDisplayName : '-';

    $ageDisplay = filled($client->age ?? null) ? $client->age . ' ปี' : '-';
    $startDate = request('start_date');
    $endDate = request('end_date');
@endphp

<style>
.medical-report-page{
    padding:16px 12px 28px;
    font-family:"Noto Sans Thai", "Sarabun", Tahoma, sans-serif;
    background:#eef2f7;
}

.report-page{
    max-width:1280px;
    margin:0 auto;
    background:#fff;
    border-radius:16px;
    box-shadow:0 10px 30px rgba(0,0,0,.05);
    overflow:hidden;
    border:1px solid #e5e7eb;
}

.report-toolbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
    padding:14px 18px 0;
}

.report-btn{
    border-radius:10px;
    padding:7px 14px;
    font-size:14px;
    cursor:pointer;
    border:1px solid #d1d5db;
    background:#fff;
    color:#374151;
    display:inline-flex;
    align-items:center;
    gap:6px;
    text-decoration:none;
}

.report-btn-primary{
    background:#2563eb;
    color:#fff;
    border-color:#2563eb;
}

.report-wrap{
    padding:16px 18px 22px;
}

.report-header{
    text-align:center;
    border-bottom:1px solid #e5e7eb;
    padding-bottom:12px;
    margin-bottom:14px;
}

.report-title{
    margin:0;
    font-size:24px;
    font-weight:700;
    color:#111827;
}

.report-subtitle{
    margin:4px 0 0;
    color:#6b7280;
    font-size:14px;
}

.client-info{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:8px 18px;
    margin-bottom:12px;
}

.client-info-item{
    font-size:15px;
    color:#1f2937;
}

.client-info-label{
    font-weight:700;
    color:#111827;
    margin-right:6px;
}

.report-meta{
    display:flex;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:10px;
    background:#f8fafc;
    padding:9px 12px;
    border-radius:12px;
    margin-bottom:12px;
    font-size:14px;
    border:1px solid #e5e7eb;
}

.table-wrap{
    overflow-x:auto;
}

.report-table{
    width:100%;
    border-collapse:collapse;
    min-width:1180px;
}

.report-table th,
.report-table td{
    border:1px solid #e5e7eb;
    padding:7px 8px;
    font-size:14px;
    vertical-align:top;
    line-height:1.35;
}

.report-table th{
    background:#f1f5f9;
    color:#334155;
    font-weight:700;
    text-align:center;
    white-space:nowrap;
}

.report-table td{
    color:#1f2937;
    break-inside:avoid;
    word-break:break-word;
}

.text-center{
    text-align:center;
}

.date-col{
    width:120px;
    min-width:120px;
    white-space:nowrap;
}

.disease-col{
    width:130px;
}

.illness-col{
    width:170px;
}

.treatment-col{
    width:170px;
}

.refer-col{
    width:95px;
}

.diagnosis-col{
    width:145px;
}

.teacher-col{
    width:110px;
}

.remark-col{
    width:120px;
    min-width:120px;
}

.medical-status{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:70px;
    padding:2px 8px;
    border-radius:999px;
    font-weight:700;
    font-size:12px;
}

.medical-status--yes{
    background:#dcfce7;
    color:#166534;
}

.medical-status--no{
    background:#fee2e2;
    color:#991b1b;
}

.empty-state{
    text-align:center;
    padding:32px 16px;
    border:1px dashed #d9e2ef;
    border-radius:16px;
    background:#fff;
    color:#64748b;
}

.signature-wrap{
    margin-top:30px;
    display:flex;
    justify-content:flex-end;
}

.signature-box{
    width:280px;
    text-align:center;
    color:#374151;
    font-size:15px;
}

.signature-line{
    margin-bottom:8px;
    padding-top:24px;
    border-bottom:1px solid #111827;
    height:36px;
}

@media (max-width:768px){
    .medical-report-page{
        padding:0;
    }

    .report-page{
        margin:0;
        border:0;
        border-radius:0;
        box-shadow:none;
    }

    .report-toolbar,
    .report-wrap{
        padding-left:14px;
        padding-right:14px;
    }

    .client-info{
        grid-template-columns:1fr;
    }

    .report-title{
        font-size:22px;
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
        margin:0 !important;
        padding:0 !important;
        background:#fff !important;
        overflow:visible !important;
        font-size:12px !important;
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
        margin:0 !important;
        padding:0 !important;
        width:100% !important;
        max-width:100% !important;
    }

    .medical-report-page{
        width:100% !important;
        padding:0 !important;
        margin:0 !important;
        background:#fff !important;
    }

    .report-page{
        width:100% !important;
        max-width:100% !important;
        margin:0 !important;
        padding:0 !important;
        border:0 !important;
        border-radius:0 !important;
        box-shadow:none !important;
        overflow:visible !important;
    }

    .report-wrap{
        padding:0 !important;
        margin:0 !important;
    }

    .report-header{
        margin-bottom:7px !important;
        padding-bottom:6px !important;
    }

    .report-title{
        font-size:20px !important;
        line-height:1.15 !important;
    }

    .report-subtitle{
        font-size:12px !important;
        margin-top:2px !important;
    }

    .client-info{
        gap:4px 14px !important;
        margin-bottom:8px !important;
    }

    .client-info-item,
    .report-meta{
        font-size:12px !important;
        line-height:1.3 !important;
    }

    .report-meta{
        padding:6px 9px !important;
        margin-bottom:8px !important;
        border-radius:8px !important;
    }

    .table-wrap{
        overflow:visible !important;
    }

    .report-table{
        width:100% !important;
        min-width:unset !important;
        table-layout:fixed !important;
        border-collapse:collapse !important;
    }

    .report-table th,
    .report-table td{
        font-size:10.8px !important;
        padding:4px 4px !important;
        line-height:1.2 !important;
        word-break:break-word !important;
        white-space:normal !important;
    }

    .date-col{
        width:95px !important;
        min-width:95px !important;
        white-space:nowrap !important;
    }

    .disease-col{
        width:105px !important;
    }

    .illness-col{
        width:135px !important;
    }

    .treatment-col{
        width:135px !important;
    }

    .refer-col{
        width:70px !important;
    }

    .diagnosis-col{
        width:115px !important;
    }

    .teacher-col{
        width:80px !important;
    }

  .remark-col{
    width:90px !important;
}

    .medical-status{
        min-width:auto !important;
        padding:1px 4px !important;
        font-size:9.5px !important;
        border-radius:8px !important;
    }

    .signature-wrap{
        margin-top:16px !important;
        page-break-inside:avoid !important;
    }

    .signature-box{
        width:250px !important;
        font-size:12px !important;
    }

    .signature-line{
        height:28px !important;
        padding-top:16px !important;
    }
}
</style>

<div class="medical-report-page">
    <div class="report-page">

        <div class="report-toolbar">
            <div>
                <a href="{{ route('medical.add', $client->id) }}" class="report-btn">
                    ← กลับ
                </a>
            </div>

            <div>
                <button onclick="window.print()" type="button" class="report-btn report-btn-primary">
                    🖨 พิมพ์
                </button>
            </div>
        </div>

        <div class="report-wrap">

            <div class="report-header">
                <h1 class="report-title">รายงานการรักษาพยาบาลในหน่วยงาน</h1>
                <p class="report-subtitle">
                    แสดงข้อมูลสุขภาพ การรักษา การพบแพทย์ และการนัดหมายของผู้รับบริการ
                </p>
            </div>

            <div class="client-info">
                <div class="client-info-item">
                    <span class="client-info-label">ชื่อผู้รับบริการ:</span>
                    {{ $clientDisplayName }}
                </div>

                <div class="client-info-item">
                    <span class="client-info-label">อายุ:</span>
                    {{ $ageDisplay }}
                </div>
            </div>

            <div class="report-meta">
                <div>
                    <strong>ประเภทรายงาน:</strong>
                    รายงานการรักษาพยาบาล
                </div>

                <div>
                    <strong>จำนวน:</strong>
                    {{ number_format($medicals->count()) }} รายการ
                </div>

                @if($startDate || $endDate)
                    <div>
                        <strong>ช่วงวันที่:</strong>
                        {{ $startDate ? $formatThaiDate($startDate) : 'เริ่มต้น' }}
                        ถึง
                        {{ $endDate ? $formatThaiDate($endDate) : 'ปัจจุบัน' }}
                    </div>
                @endif
            </div>

            @if($medicals->isNotEmpty())
                @php
                    $hasDoctorVisit = $medicals->contains(function ($item) {
                        return $item->refer === 'พบแพทย์';
                    });
                @endphp

                <div class="table-wrap">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th class="date-col">วันที่รักษา</th>
                                <th class="disease-col">ชื่อโรค</th>
                                <th class="illness-col">อาการเจ็บป่วย</th>
                                <th class="treatment-col">การรักษา</th>
                                <th class="refer-col">การพบแพทย์</th>

                                @if($hasDoctorVisit)
                                    <th class="diagnosis-col">การวินิจฉัย</th>
                                    <th class="date-col">วันนัด</th>
                                @endif

                                <th class="teacher-col">ครูผู้ดูแล</th>
                                <th class="remark-col">หมายเหตุ</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($medicals as $item)
                                @php
                                    $isDoctorVisit = $item->refer === 'พบแพทย์';
                                @endphp

                                <tr>
                                    <td class="text-center">
                                        {{ $formatThaiDate($item->medical_date) }}
                                    </td>

                                    <td>{{ $item->disease_name ?: '-' }}</td>
                                    <td>{{ $item->illness ?: '-' }}</td>
                                    <td>{{ $item->treatment ?: '-' }}</td>

                                    <td class="text-center">
                                        @if($isDoctorVisit)
                                            <span class="medical-status medical-status--yes">
                                                พบแพทย์
                                            </span>
                                        @else
                                            <span class="medical-status medical-status--no">
                                                {{ $item->refer ?: 'ไม่ระบุ' }}
                                            </span>
                                        @endif
                                    </td>

                                    @if($hasDoctorVisit)
                                        <td>
                                            @if($isDoctorVisit)
                                                {{ $item->diagnosis ?: '-' }}
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if($isDoctorVisit)
                                                {{ $formatThaiDate($item->appt_date) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    @endif

                                    <td>{{ $item->teacher ?: '-' }}</td>
                                    <td>{{ $item->remark ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    ไม่มีข้อมูลรายงานการรักษาพยาบาล
                </div>
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