@php
    use App\Helpers\ThaiDateHelper;
@endphp

@extends('admin_client.admin_client')

@section('content')
<style>
.school-followup-report-page{
    padding:16px 12px 28px;
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

.toolbar-left,
.toolbar-right{
    display:flex;
    align-items:center;
    gap:8px;
    flex-wrap:wrap;
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
    transition:all .2s ease;
}

.report-btn:hover{
    background:#f9fafb;
    color:#111827;
}

.report-btn-primary{
    background:#2563eb;
    color:#fff;
    border-color:#2563eb;
}

.report-btn-primary:hover{
    background:#1d4ed8;
    border-color:#1d4ed8;
    color:#fff;
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
    line-height:1.2;
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
    align-items:center;
    flex-wrap:wrap;
    gap:10px;
    background:#f8fafc;
    padding:9px 12px;
    border-radius:12px;
    margin-bottom:12px;
    font-size:14px;
    border:1px solid #e5e7eb;
}

.report-meta strong{
    color:#111827;
}

.table-wrap{
    overflow-x:auto;
    -webkit-overflow-scrolling:touch;
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
}

.text-center{
    text-align:center;
}

.date-col{
    width:95px;
    white-space:nowrap;
}

.type-col{
    width:110px;
    white-space:nowrap;
}

.tel-col{
    width:110px;
    white-space:nowrap;
}

.contact-col{
    width:130px;
}

.teacher-col{
    width:130px;
}

.school-col{
    width:170px;
}

.education-col{
    width:130px;
}

.result-col,
.remark-col{
    min-width:180px;
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
    .school-followup-report-page{
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
        margin:6mm;
    }

    html,
    body{
        width:297mm !important;
        min-height:auto !important;
        height:auto !important;
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

    /* ซ่อนส่วน layout หลักที่ติดมาตอนพิมพ์ */
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

    .school-followup-report-page{
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
        page-break-after:auto !important;
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
        font-size:11.5px !important;
        padding:4px 5px !important;
        line-height:1.25 !important;
        word-break:break-word !important;
        white-space:normal !important;
    }

    .date-col{
        width:78px !important;
    }

    .school-col{
        width:105px !important;
    }

    .education-col{
        width:75px !important;
    }

    .teacher-col{
        width:75px !important;
    }

    .tel-col{
        width:82px !important;
    }

    .type-col{
        width:85px !important;
    }

    .contact-col{
        width:85px !important;
    }

    .result-col,
    .remark-col{
        width:auto !important;
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

<div class="school-followup-report-page">
    <div class="report-page">

        <div class="report-toolbar">
            <div class="toolbar-left">
                <a href="{{ route('school_followup_add', $client->id) }}" class="report-btn">
                    ← กลับ
                </a>
            </div>

            <div class="toolbar-right">
                <button type="button" onclick="window.print()" class="report-btn report-btn-primary">
                    🖨 พิมพ์
                </button>
            </div>
        </div>

        <div class="report-wrap">

            <div class="report-header">
                <h1 class="report-title">รายงานการติดตามในโรงเรียน</h1>
                <p class="report-subtitle">รายละเอียดการติดตามผลการเรียนของผู้รับบริการ</p>
            </div>

            <div class="client-info">
                <div class="client-info-item">
                    <span class="client-info-label">ชื่อผู้รับบริการ:</span>
                    {{ $client->fullname ?? $client->full_name ?? '-' }}
                </div>

                <div class="client-info-item">
                    <span class="client-info-label">อายุ:</span>
                    {{ $age ?? '-' }} ปี
                </div>

                <div class="client-info-item">
                    <span class="client-info-label">โรงเรียน:</span>
                    {{ $school_name ?? '-' }}
                </div>

                <div class="client-info-item">
                    <span class="client-info-label">ระดับ / ภาคเรียน:</span>
                    {{ $education_name ?? '-' }} / {{ $term ?? '-' }}
                </div>
            </div>

            <div class="report-meta">
                <div>
                    <strong>วันที่ติดตาม:</strong>
                    {{ !empty($followup->follow_date) ? ThaiDateHelper::formatThaiDate($followup->follow_date) : '-' }}
                </div>

                <div>
                    <strong>จำนวน:</strong> 1 รายการ
                </div>
            </div>

            <div class="table-wrap">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th class="date-col">วันที่ติดตาม</th>
                            <th class="school-col">สถานศึกษา</th>
                            <th class="education-col">ระดับชั้น</th>
                            <th class="teacher-col">ครูผู้สอน</th>
                            <th class="tel-col">เบอร์โทร</th>
                            <th class="type-col">ประเภทการติดตาม</th>
                            <th class="contact-col">ผู้ติดต่อ</th>
                            <th class="result-col">ผลการติดตาม</th>
                            <th class="remark-col">หมายเหตุ</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="text-center date-col">
                                {{ !empty($followup->follow_date) ? ThaiDateHelper::formatThaiDate($followup->follow_date) : '-' }}
                            </td>

                            <td>
                                {{ $school_name ?? optional($followup->educationRecord)->school_name ?? '-' }}
                            </td>

                            <td>
                                {{ $education_name ?? optional(optional($followup->educationRecord)->education)->education_name ?? '-' }}
                            </td>

                            <td>
                                {{ $followup->teacher_name ?? '-' }}
                            </td>

                            <td class="text-center tel-col">
                                {{ $followup->tel ?? '-' }}
                            </td>

                            <td class="text-center type-col">
                                @switch($followup->follow_type)
                                    @case('self')
                                        ติดตามด้วยตนเอง
                                        @break
                                    @case('phone')
                                        โทรศัพท์
                                        @break
                                    @case('other')
                                        อื่นๆ
                                        @break
                                    @default
                                        {{ $followup->follow_type ?? '-' }}
                                @endswitch
                            </td>

                            <td>
                                {{ $followup->contact_name ?? '-' }}
                            </td>

                            <td>
                                {{ $followup->result ?? '-' }}
                            </td>

                            <td>
                                {{ $followup->remark ?? '-' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

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