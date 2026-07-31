@extends('admin_client.admin_client')

@section('content')
<style>
    .education-report-page{
        background:#f4f7fb;
        min-height:100%;
        color:#1f2937;
    }

    .education-report-wrap{
        max-width:1120px;
        margin:0 auto;
    }

    .education-report-toolbar{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
        margin-bottom:14px;
    }

    .education-report-toolbar-left,
    .education-report-toolbar-right{
        display:flex;
        align-items:center;
        gap:10px;
        flex-wrap:wrap;
    }

    .education-report-btn{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:.5rem;
        min-height:42px;
        padding:.68rem .95rem;
        border-radius:10px;
        text-decoration:none;
        font-weight:700;
        font-size:.9rem;
        border:1px solid #d6deea;
        background:#fff;
        color:#0f172a;
        transition:.18s ease;
        white-space:nowrap;
    }

    .education-report-btn:hover{
        transform:translateY(-1px);
        color:#0f172a;
        border-color:#bfd0e2;
    }

    .education-report-btn-print{
        background:#1d4ed8;
        border-color:#1d4ed8;
        color:#fff;
    }

    .education-report-btn-print:hover{
        color:#fff;
        background:#1e40af;
        border-color:#1e40af;
    }

    .education-report-sheet{
        background:#fff;
        border:1px solid #dde5ef;
        border-radius:18px;
        box-shadow:0 12px 28px rgba(15, 23, 42, 0.05);
        padding:22px 24px 20px;
    }

    .report-header{
        margin-bottom:16px;
        border-bottom:2px solid #1e3a8a;
        padding-bottom:12px;
    }

    .report-header-top{
        text-align:center;
        margin-bottom:12px;
    }

    .report-header-title{
        font-size:1.34rem;
        font-weight:800;
        color:#0f172a;
        margin-bottom:4px;
        letter-spacing:.2px;
    }

    .report-header-subtitle{
        font-size:.94rem;
        color:#475569;
        margin-bottom:0;
    }

    .report-meta{
        display:grid;
        grid-template-columns:repeat(2, minmax(0, 1fr));
        gap:4px 20px;
        font-size:.96rem;
        line-height:1.6;
    }

    .report-meta-item{
        display:flex;
        align-items:flex-start;
        gap:8px;
        min-width:0;
    }

    .report-meta-label{
        min-width:130px;
        font-weight:800;
        color:#0f172a;
        white-space:nowrap;
    }

    .report-meta-value{
        flex:1;
        color:#334155;
        word-break:break-word;
    }

    .report-section-title{
        font-size:1rem;
        font-weight:800;
        color:#0f172a;
        margin:14px 0 10px;
        padding-left:10px;
        border-left:4px solid #2563eb;
        line-height:1.35;
    }

    .record-block{
        margin-bottom:12px;
        border:1px solid #dde5ef;
        border-radius:14px;
        overflow:hidden;
        background:#fff;
    }

    .record-block:last-child{
        margin-bottom:0;
    }

    .record-body{
        padding:0;
    }

    .subject-table-wrap{
        overflow-x:auto;
        -webkit-overflow-scrolling:touch;
    }

    .subject-table{
        width:100%;
        border-collapse:collapse;
        margin:0;
        table-layout:fixed;
    }

    .subject-table thead th{
        background:#f8fafc;
        color:#0f172a;
        font-size:.88rem;
        font-weight:800;
        text-align:center;
        padding:9px 8px;
        border:1px solid #dbe4ee;
        vertical-align:middle;
        line-height:1.35;
    }

    .subject-table tbody td{
        font-size:.9rem;
        color:#1f2937;
        padding:8px 10px;
        border:1px solid #e2e8f0;
        vertical-align:middle;
        line-height:1.45;
    }

    .subject-table tbody tr:nth-child(even){
        background:#fcfdff;
    }

    .subject-col-name{
        font-weight:700;
        width:auto;
        word-break:break-word;
    }

    .subject-col-center{
        text-align:center;
        white-space:nowrap;
    }

    .subject-summary-row td{
        background:#f1f5f9;
        font-weight:800;
        color:#0f172a;
    }

    .summary-label{
        text-align:right;
    }

    .report-empty{
        border:1px dashed #cbd5e1;
        background:#fafcff;
        color:#64748b;
        text-align:center;
        padding:20px 14px;
        font-size:.95rem;
    }

    .subject-mobile-list{
        display:none;
        padding:4px 12px 8px;
    }

    .subject-mobile-row{
        display:grid;
        grid-template-columns:minmax(0,1fr) 78px 64px;
        gap:10px;
        align-items:center;
        padding:9px 0;
        border-bottom:1px solid #e5edf5;
    }

    .subject-mobile-row:last-child{
        border-bottom:none;
    }

    .subject-mobile-name{
        font-weight:700;
        color:#0f172a;
        line-height:1.45;
        word-break:break-word;
    }

    .subject-mobile-score,
    .subject-mobile-grade{
        text-align:center;
        color:#334155;
        white-space:nowrap;
        font-weight:600;
    }

    .subject-mobile-head{
        display:grid;
        grid-template-columns:minmax(0,1fr) 78px 64px;
        gap:10px;
        padding:8px 0 9px;
        border-bottom:2px solid #dbe4ee;
        margin-bottom:2px;
        font-size:.8rem;
        font-weight:800;
        color:#475569;
    }

    .subject-mobile-summary{
        display:grid;
        grid-template-columns:minmax(0,1fr) 78px 64px;
        gap:10px;
        align-items:center;
        padding:10px 0 4px;
        border-top:1.5px solid #dbe4ee;
        margin-top:2px;
        font-weight:800;
        color:#0f172a;
    }

    .subject-mobile-summary-label{
        text-align:right;
    }

    .subject-mobile-summary-value{
        grid-column:3;
        text-align:center;
        white-space:nowrap;
    }

    @media (max-width: 991.98px){
        .report-meta{
            grid-template-columns:1fr;
        }
    }

    @media (max-width: 767.98px){
        .education-report-page{
            background:#fff;
        }

        .education-report-sheet{
            padding:18px 14px 16px;
            border-radius:14px;
        }

        .education-report-toolbar-left,
        .education-report-toolbar-right{
            width:100%;
        }

        .education-report-btn{
            width:100%;
        }

        .report-header-title{
            font-size:1.16rem;
        }

        .report-meta-item{
            display:grid;
            grid-template-columns:120px minmax(0,1fr);
            gap:6px;
            align-items:start;
        }

        .report-meta-label{
            min-width:auto;
        }

        .subject-table-wrap{
            display:none;
        }

        .subject-mobile-list{
            display:block;
        }

        .subject-mobile-summary-label{
            text-align:left;
        }
    }

    @page{
        size:A4 landscape;
        margin:8mm;
    }

    @media print{
        html, body{
            width:297mm;
            min-height:210mm;
            background:#fff !important;
            font-size:12px !important;
        }

        .education-report-page{
            background:#fff !important;
            color:#000 !important;
        }

        .education-report-toolbar,
        .main-nav,
        .navbar,
        .topbar,
        .leftside-menu,
        .footer{
            display:none !important;
        }

        .container-fluid,
        .content-page{
            padding:0 !important;
            margin:0 !important;
        }

        .education-report-wrap{
            max-width:none !important;
            margin:0 !important;
        }

        .education-report-sheet{
            box-shadow:none !important;
            border:none !important;
            border-radius:0 !important;
            padding:0 !important;
        }

        .report-header{
            margin-bottom:10px !important;
            padding-bottom:8px !important;
        }

        .report-header-top{
            margin-bottom:8px !important;
        }

        .report-header-title{
            font-size:16px !important;
            margin-bottom:2px !important;
        }

        .report-header-subtitle{
            font-size:12px !important;
        }

        .report-meta{
            gap:2px 14px !important;
            font-size:12px !important;
            line-height:1.35 !important;
        }

        .report-section-title{
            margin:8px 0 6px !important;
            font-size:13px !important;
        }

        .record-block{
            border:1px solid #dbe4ee !important;
            margin-bottom:8px !important;
            break-inside:avoid;
            page-break-inside:avoid;
        }

        .subject-table thead th{
            font-size:11px !important;
            padding:6px 6px !important;
            -webkit-print-color-adjust:exact;
            print-color-adjust:exact;
        }

        .subject-table tbody td{
            font-size:11px !important;
            padding:5px 7px !important;
            line-height:1.3 !important;
        }

        .subject-summary-row td{
            font-size:11px !important;
            padding:6px 7px !important;
            -webkit-print-color-adjust:exact;
            print-color-adjust:exact;
        }

        .subject-table-wrap{
            display:block !important;
            overflow:visible !important;
        }

        .subject-mobile-list{
            display:none !important;
        }
    }


    .record-meta-strip{
        display:grid;
        grid-template-columns:repeat(4, minmax(0, 1fr));
        gap:8px 14px;
        padding:10px 12px;
        background:#f8fafc;
        border-bottom:1px solid #dde5ef;
        font-size:.9rem;
    }

    .record-meta-strip-item{
        min-width:0;
        color:#334155;
        word-break:break-word;
    }

    .record-meta-strip-label{
        font-weight:800;
        color:#0f172a;
    }

    @media (max-width: 767.98px){
        .record-meta-strip{
            grid-template-columns:1fr 1fr;
        }
    }

    @media print{
        .record-block{
            break-inside:avoid;
            page-break-inside:avoid;
        }

        .record-meta-strip{
            grid-template-columns:repeat(4, minmax(0, 1fr));
            padding:7px 9px;
            font-size:12px;
        }
    }

</style>

@php
    /*
     * Controller จัดเรียงตามปีการศึกษาและภาคเรียนแล้ว
     * จึงใช้รายการแรกเป็นภาคเรียนล่าสุดเหมือนหน้าติดตามผลการเรียน
     */
    $latestRecord = $latestEducationRecord ?? $educationRecords->first();

    $latestSemesterName = data_get($latestRecord, 'semester_label')
        ?: data_get($latestRecord, 'semester.semester_name')
        ?: '-';

    $hasGpa = static fn ($record): bool => $record
        && $record->grade_average !== null
        && $record->grade_average !== '';
@endphp

<div class="container-fluid education-report-page py-3 py-md-4">
    <div class="education-report-wrap">

        <div class="education-report-toolbar">
            <div class="education-report-toolbar-left">
                <a href="{{ route('education_record_show', $client->id) }}" class="education-report-btn">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>กลับหน้ารายการ</span>
                </a>
            </div>

            <div class="education-report-toolbar-right">
                <button type="button" class="education-report-btn education-report-btn-print" onclick="window.print()">
                    <i class="bi bi-printer"></i>
                    <span>พิมพ์รายงาน</span>
                </button>
            </div>
        </div>

        <div class="education-report-sheet">
            <div class="report-header">
                <div class="report-header-top">
                    <div class="report-header-title">รายงานผลการเรียน</div>
                    <br>
                    
                </div>

                <div class="report-meta">
                    <div class="report-meta-item">
                        <div class="report-meta-label">ชื่อ-สกุล</div>
                        <div class="report-meta-value">: {{ $client->fullname ?? $client->full_name ?? '-' }}</div>
                    </div>

                    <div class="report-meta-item">
                        <div class="report-meta-label">วันที่ออกรายงาน</div>
                        <div class="report-meta-value">: {{ now('Asia/Bangkok')->format('d/m') }}/{{ now('Asia/Bangkok')->year + 543 }}</div>
                    </div>

                    <div class="report-meta-item">
                        <div class="report-meta-label">ระดับการศึกษาล่าสุด</div>
                        <div class="report-meta-value">: {{ data_get($latestRecord, 'education.education_name', '-') }}</div>
                    </div>

                  <div class="report-meta-item">
    <div class="report-meta-label">ภาคเรียนล่าสุด</div>
    <div class="report-meta-value">
        : {{ $latestSemesterName }}
    </div>
</div>

                    <div class="report-meta-item">
                        <div class="report-meta-label">สถานศึกษาล่าสุด</div>
                        <div class="report-meta-value">: {{ data_get($latestRecord, 'school_name', '-') }}</div>
                    </div>

                    <div class="report-meta-item">
                        <div class="report-meta-label">เกรดเฉลี่ย</div>
                        <div class="report-meta-value">
                            :
                            @if($hasGpa($latestRecord))
                                {{ number_format($latestRecord->grade_average, 2) }}
                            @else
                                รอผล
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="report-section-title">ผลการเรียน</div>

            @if($educationRecords->isNotEmpty())
                @foreach($educationRecords as $record)
                    <div class="record-block">
                        <div class="record-body">
                            <div class="record-meta-strip">
                                <div class="record-meta-strip-item">
                                    <span class="record-meta-strip-label">วันที่บันทึก:</span>
                                    {{ $record->record_date ? \Carbon\Carbon::parse($record->record_date)->format('d/m') . '/' . (\Carbon\Carbon::parse($record->record_date)->year + 543) : '-' }}
                                </div>
                                <div class="record-meta-strip-item">
                                    <span class="record-meta-strip-label">ระดับการศึกษา:</span>
                                    {{ data_get($record, 'education.education_name', '-') }}
                                </div>
                                <div class="record-meta-strip-item">
                                    <span class="record-meta-strip-label">ภาคเรียน:</span>
                                    {{ $record->semester_label ?? data_get($record, 'semester.semester_name', '-') }}
                                </div>
                                <div class="record-meta-strip-item">
                                    <span class="record-meta-strip-label">สถานศึกษา:</span>
                                    {{ $record->school_name ?: '-' }}
                                </div>
                            </div>
                            @if($record->subjects && $record->subjects->count())

                                {{-- Desktop / Print --}}
                                <div class="subject-table-wrap">
                                    <table class="subject-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 62%;">รายวิชา</th>
                                                <th style="width: 20%;">คะแนน</th>
                                                <th style="width: 18%;">เกรด</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($record->subjects as $subject)
                                                <tr>
                                                    <td class="subject-col-name">{{ $subject->subject_name ?? '-' }}</td>
                                                    <td class="subject-col-center">{{ $subject->pivot->score ?? '-' }}</td>
                                                    <td class="subject-col-center">{{ $subject->pivot->grade ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="subject-summary-row">
                                                <td colspan="2" class="summary-label">เกรดเฉลี่ย (GPA)</td>
                                                <td class="subject-col-center">
                                                    @if($hasGpa($record))
                                                        {{ number_format($record->grade_average, 2) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Mobile --}}
                                <div class="subject-mobile-list">
                                    <div class="subject-mobile-head">
                                        <div>รายวิชา</div>
                                        <div class="text-center">คะแนน</div>
                                        <div class="text-center">เกรด</div>
                                    </div>

                                    @foreach($record->subjects as $subject)
                                        <div class="subject-mobile-row">
                                            <div class="subject-mobile-name">{{ $subject->subject_name ?? '-' }}</div>
                                            <div class="subject-mobile-score">{{ $subject->pivot->score ?? '-' }}</div>
                                            <div class="subject-mobile-grade">{{ $subject->pivot->grade ?? '-' }}</div>
                                        </div>
                                    @endforeach

                                    <div class="subject-mobile-summary">
                                        <div class="subject-mobile-summary-label">เกรดเฉลี่ย (GPA)</div>
                                        <div></div>
                                        <div class="subject-mobile-summary-value">
                                            @if($hasGpa($record))
                                                {{ number_format($record->grade_average, 2) }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            @else
                                <div class="report-empty">
                                    ยังไม่มีข้อมูลรายวิชา
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="report-empty">
                    ยังไม่มีข้อมูลผลการเรียนสำหรับจัดทำรายงาน
                </div>
            @endif
        </div>
    </div>
</div>
@endsection