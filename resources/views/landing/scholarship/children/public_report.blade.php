<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายชื่อผู้ขอรับทุนภาคเรียนนี้</title>

    <style>

    body{
        font-family:"Sarabun","TH Sarabun New",sans-serif;
        background:#f8fafc;
        color:#1f2937;
        margin:0;
        padding:24px;
        font-size:15px;
        line-height:1.6;
    }

    .report-page{
        max-width:1100px;
        margin:0 auto;
        background:#ffffff;
        border-radius:18px;
        padding:28px;
        box-shadow:0 10px 30px rgba(15,23,42,.07);
        border:1px solid #eef2f7;
    }

    /* =========================
       HEADER
    ========================= */

    .report-header{
        text-align:center;
        margin-bottom:22px;
    }

    .report-header h1{
        margin:0;
        font-size:23px;
        font-weight:700;
        letter-spacing:.2px;
        color:#1e3a8a;
        line-height:1.4;
    }

    .report-header p{
        margin-top:8px;
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:5px 14px;
        border-radius:999px;
        background:#eff6ff;
        border:1px solid #dbeafe;
        color:#475569;
        font-size:13px;
        font-weight:600;
    }

    /* =========================
       TOOLBAR
    ========================= */

    .toolbar{
        display:flex;
        justify-content:flex-end;
        gap:10px;
        margin-bottom:18px;
        flex-wrap:wrap;
    }

    .btn{
        border:0;
        border-radius:10px;
        padding:8px 16px;
        text-decoration:none;
        cursor:pointer;
        font-size:14px;
        font-weight:600;
        transition:.25s ease;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:6px;
    }

    .btn:hover{
        transform:translateY(-1px);
    }

    .btn-print{
        background:#2563eb;
        color:#fff;
        box-shadow:0 4px 14px rgba(37,99,235,.18);
    }

    .btn-print:hover{
        background:#1d4ed8;
    }

    .btn-back{
        background:#f1f5f9;
        color:#334155;
        border:1px solid #e2e8f0;
    }

    .btn-back:hover{
        background:#e2e8f0;
    }

    /* =========================
       TABLE
    ========================= */

    table{
        width:100%;
        border-collapse:collapse;
        margin-top:14px;
        overflow:hidden;
        border-radius:14px;
        background:#fff;
        font-size:14px;
    }

    th{
        background:#f1f5f9;
        color:#334155;
        font-weight:700;
        text-align:center;
        padding:10px 12px;
        border:1px solid #e2e8f0;
        font-size:13px;
        letter-spacing:.2px;
        white-space:nowrap;
    }

    td{
        border:1px solid #e2e8f0;
        padding:8px 10px;
        vertical-align:top;
        color:#374151;
        line-height:1.55;
        font-size:13px;
        font-weight:400;
    }

    tbody tr:nth-child(even){
        background:#fafafa;
    }

    tbody tr:hover{
        background:#f8fbff;
        transition:.2s ease;
    }

    td.center{
        text-align:center;
        white-space:nowrap;
    }

    /* =========================
       EMPTY
    ========================= */

    .empty{
        text-align:center;
        padding:32px 20px;
        background:#f8fafc;
        border:1px dashed #cbd5e1;
        border-radius:14px;
        color:#64748b;
        font-size:14px;
        margin-top:12px;
    }

    /* =========================
       MOBILE
    ========================= */

    @media(max-width:768px){

        body{
            padding:14px;
        }

        .report-page{
            padding:18px;
            border-radius:14px;
        }

        .report-header h1{
            font-size:19px;
        }

        .report-header p{
            font-size:12px;
        }

        .toolbar{
            justify-content:center;
        }

        .btn{
            width:100%;
            font-size:13px;
        }

        table{
            font-size:12px;
        }

        th{
            font-size:11px;
            padding:8px;
        }

        td{
            font-size:11px;
            padding:7px 8px;
        }
    }

    /* =========================
       PRINT
    ========================= */

    @media print{

        @page{
            size:A4 landscape;
            margin:10mm;
        }

        body{
            background:#fff;
            padding:0;
            font-size:12px;
        }

        .report-page{
            box-shadow:none;
            border:none;
            border-radius:0;
            padding:0;
            max-width:100%;
        }

        .toolbar{
            display:none;
        }

        table{
            font-size:11px;
        }

        th{
            font-size:10px;
            background:#f3f4f6 !important;
            color:#111827 !important;
        }

        td{
            font-size:10px;
            color:#111827;
        }

        .report-header h1{
            font-size:18px;
        }

        .report-header p{
            font-size:10px;
            border:1px solid #d1d5db;
            background:#f9fafb;
            color:#374151;
        }
    }

</style>
</head>
<body>

<div class="report-page">

    <div class="toolbar">
        <a href="{{ url('/') }}" class="btn btn-back">กลับหน้าแรก</a>
        {{-- <button onclick="window.print()" class="btn btn-print">พิมพ์รายงาน</button> --}}
    </div>

    <div class="report-header">
        <h1>รายชื่อผู้ขอรับทุนภาคเรียนนี้</h1>
        <p class="mt-2 text-px text-slate-600">
            ปีการศึกษา {{ $latestYear ?? 'ไม่พบข้อมูล' }}
        </p>
    </div>

    @if($children->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width:60px;">ลำดับ</th>

                    @auth
                        <th>ชื่อ - นามสกุล</th>
                    @else
                        <th style="width:90px;">เพศ</th>
                    @endauth

                    <th style="width:90px;">อายุ</th>
                    <th>ระดับการศึกษา</th>
                    <th>สถานศึกษา</th>
                    <th>เหตุผล / ความจำเป็น</th>
                </tr>
            </thead>

            <tbody>
                @foreach($children as $index => $child)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>

                        <td class="center">
                            @auth
                                {{ trim(($child->first_name ?? '') . ' ' . ($child->last_name ?? '')) ?: '-' }}
                            @else
                                @php
                                    $gender = strtolower($child->gender ?? '');
                                @endphp

                                @if($gender === 'male')
                                    ชาย
                                @elseif($gender === 'female')
                                    หญิง
                                @elseif($gender)
                                    {{ $child->gender }}
                                @else
                                    -
                                @endif
                            @endauth
                        </td>

                        <td class="center">{{ $child->age ?? '-' }}</td>
                        <td>{{ $child->education_level ?? '-' }}</td>
                        <td>{{ $child->school_name ?? '-' }}</td>
                        <td>{{ $child->reason ?? $child->need_detail ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">
            ไม่พบข้อมูลผู้ขอรับทุนในปีการศึกษาล่าสุด
        </div>
    @endif

</div>

</body>
</html>