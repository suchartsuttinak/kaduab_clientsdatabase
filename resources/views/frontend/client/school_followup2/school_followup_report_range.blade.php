@php
    use App\Helpers\ThaiDateHelper;
@endphp
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>รายงานการติดตามในโรงเรียน</title>

<style>
body{
    font-family:"TH Sarabun New","Sarabun",sans-serif;
    font-size:15px;
    background:#eef2f7;
    margin:0;
    color:#1f2937;
    line-height:1.45;
}

.report-page{
    max-width:1280px;
    margin:20px auto;
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

.btn{
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
    transition:all .2s ease;
}

.btn:hover{
    background:#f9fafb;
}

.btn-primary{
    background:#2563eb;
    color:#fff;
    border-color:#2563eb;
}

.btn-primary:hover{
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

.empty-state{
    text-align:center;
    padding:32px 16px;
    border:1px dashed #d9e2ef;
    border-radius:16px;
    background:linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
}

.empty-title{
    font-size:18px;
    font-weight:700;
    color:#1f2937;
    margin-bottom:6px;
}

.empty-subtitle{
    font-size:14px;
    color:#6b7280;
    margin:0;
}

@media (max-width: 768px){
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

    html, body{
        width:297mm;
        height:210mm;
        background:#fff;
        font-size:13px;
    }

    body{
        margin:0;
    }

    .report-page{
        width:100%;
        max-width:100%;
        margin:0;
        border:0;
        border-radius:0;
        box-shadow:none;
    }

    .report-toolbar{
        display:none !important;
    }

    .report-wrap{
        padding:0;
    }

    .report-header{
        margin-bottom:10px;
        padding-bottom:8px;
    }

    .report-title{
        font-size:22px;
    }

    .report-subtitle,
    .client-info-item,
    .report-meta{
        font-size:13px;
    }

    .report-table{
        min-width:unset;
    }

    .report-table th,
    .report-table td{
        font-size:12.5px;
        padding:5px 6px;
    }
}
</style>
</head>

<body>
<div class="report-page">

    <div class="report-toolbar">
        <div class="toolbar-left">
            <button onclick="history.back()" class="btn">← กลับ</button>
        </div>

        <div class="toolbar-right">
            <button onclick="window.print()" class="btn btn-primary">🖨 พิมพ์</button>
        </div>
    </div>

    <div class="report-wrap">

        <div class="report-header">
            <h1 class="report-title">รายงานการติดตามในโรงเรียน</h1>
            <p class="report-subtitle">สรุปข้อมูลการติดตามผลการเรียนของผู้รับบริการ</p>
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
                <strong>ช่วงวันที่:</strong>
                {{ $start_date ? ThaiDateHelper::formatThaiDate($start_date) : 'ทั้งหมด' }}
                -
                {{ $end_date ? ThaiDateHelper::formatThaiDate($end_date) : 'ทั้งหมด' }}
            </div>
            <div>
                <strong>จำนวน:</strong> {{ $followups->count() }} รายการ
            </div>
        </div>

        @if($followups->isEmpty())
            <div class="empty-state">
                <div class="empty-title">ไม่พบข้อมูลการติดตาม</div>
                <p class="empty-subtitle">ยังไม่มีข้อมูลสำหรับจัดทำรายงานในช่วงวันที่ที่เลือก</p>
            </div>
        @else
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
                            <th>ผลการติดตาม</th>
                            <th>หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($followups as $f)
                        <tr>
                            <td class="text-center date-col">
                                {{ $f->follow_date ? ThaiDateHelper::formatThaiDate($f->follow_date) : '-' }}
                            </td>
                            <td>
                                {{ optional($f->educationRecord)->school_name ?? '-' }}
                            </td>
                            <td>
                                {{ optional(optional($f->educationRecord)->education)->education_name ?? '-' }}
                            </td>
                            <td>
                                {{ $f->teacher_name ?? '-' }}
                            </td>
                            <td class="text-center tel-col">
                                {{ $f->tel ?? '-' }}
                            </td>
                            <td class="text-center type-col">
                                @switch($f->follow_type)
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
                                        {{ $f->follow_type ?? '-' }}
                                @endswitch
                            </td>
                            <td>
                                {{ $f->contact_name ?? '-' }}
                            </td>
                            <td>
                                {{ $f->result ?? '-' }}
                            </td>
                            <td>
                                {{ $f->remark ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>
</body>
</html>