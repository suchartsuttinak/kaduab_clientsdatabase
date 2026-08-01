@php
    use App\Helpers\ThaiDateHelper;
@endphp
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายงานตรวจสุขภาพเบื้องต้น</title>
    <style>
        :root{
            --text-main:#1f2937;
            --text-soft:#6b7280;
            --line:#d1d5db;
            --line-strong:#9ca3af;
            --bg-head:#f1f5f9;
        }

        body{
            margin:0;
            font-family:"TH Sarabun New","Sarabun",sans-serif;
            font-size:16px;
            line-height:1.4;
            color:var(--text-main);
            background:#f3f4f6;
        }

        .report-page{
            max-width:900px;
            margin:20px auto;
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:14px;
            padding:20px 26px;
        }

        .report-toolbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:10px;
            gap:10px;
            flex-wrap:wrap;
        }

        .toolbar-left,
        .toolbar-right{
            display:flex;
            align-items:center;
            gap:8px;
            flex-wrap:wrap;
        }

        .btn{
            border:0;
            font-size:15px;
            padding:6px 14px;
            border-radius:6px;
            cursor:pointer;
            display:inline-flex;
            align-items:center;
            gap:6px;
        }

        .btn-back{
            background:#e5e7eb;
            color:#111827;
        }

        .btn-back:hover{
            background:#d1d5db;
        }

        .btn-print{
            background:#1d4ed8;
            color:#fff;
        }

        .report-header{
            text-align:center;
            margin-bottom:14px;
        }

        .report-title{
            font-size:22px;
            font-weight:700;
            margin:0;
        }

        .report-subtitle{
            font-size:15px;
            color:var(--text-soft);
            margin-top:4px;
        }

        .client-info{
            display:flex;
            justify-content:space-between;
            flex-wrap:wrap;
            gap:8px 16px;
            border-bottom:1px solid #e5e7eb;
            padding-bottom:8px;
            margin-bottom:12px;
        }

        .client-info span{
            font-size:16px;
        }

        .label{
            font-weight:600;
            margin-right:6px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            font-size:15px;
        }

        th, td{
            border:1px solid var(--line);
            padding:7px 9px;
            vertical-align:top;
        }

        th{
            width:26%;
            background:var(--bg-head);
            text-align:left;
            font-weight:600;
            white-space:nowrap;
        }

        .text-muted{
            color:var(--text-soft);
        }

        @media (max-width:768px){
            .report-page{
                margin:0;
                border:0;
                border-radius:0;
                padding:14px;
            }

            .client-info{
                flex-direction:column;
                gap:4px;
            }

            th{
                width:34%;
            }
        }

        @media print{
            @page{
                size:A4 portrait;
                margin:12mm;
            }

            body{
                background:#fff;
                font-size:15px;
            }

            .report-page{
                border:0;
                margin:0;
                padding:0;
                max-width:none;
            }

            .report-toolbar{
                display:none !important;
            }
        }
    </style>
</head>
<body>

<div class="report-page">

    <div class="report-toolbar">
        <div class="toolbar-left">
            <button class="btn btn-back" onclick="history.back()">
                ← กลับหน้าก่อน
            </button>
        </div>

        <div class="toolbar-right">
            <button class="btn btn-print" onclick="window.print()">
                🖨 พิมพ์รายงาน
            </button>
        </div>
    </div>

    <div class="report-header">
        <div class="report-title">รายงานการตรวจสุขภาพเบื้องต้น</div>
        <div class="report-subtitle">ข้อมูลผู้รับบริการและผลการประเมินสุขภาพ</div>
    </div>

    <div class="client-info">
        <span>
            <span class="label">ชื่อผู้รับบริการ:</span>
            {{ ($client->fullname ?? $client->name ?? trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''))) ?: '-' }}
        </span>

        <span>
            <span class="label">อายุ:</span>
            @if(isset($client->age) && $client->age !== '' && $client->age !== null)
                {{ $client->age }} ปี
            @else
                -
            @endif
        </span>
    </div>

    <table>
        <tr>
            <th>วันที่ตรวจ</th>
            <td>{{ ThaiDateHelper::formatThaiDate($checkbody->assessor_date) }}</td>
        </tr>
        <tr>
            <th>ผู้ตรวจ / ผู้บันทึก</th>
            <td>{{ $checkbody->recorder ?? '-' }}</td>
        </tr>
        <tr>
            <th>พัฒนาการ</th>
            <td>{{ $checkbody->development ?? '-' }}</td>
        </tr>
        <tr>
            <th>รายละเอียด</th>
            <td>{{ $checkbody->detail ?? '-' }}</td>
        </tr>
        <tr>
            <th>การพัฒนา</th>
            <td>{{ $checkbody->development_type ?? '-' }}</td>
        </tr>

        @if(($checkbody->development_type ?? null) === 'เด็กกลุ่มพิเศษ')
            <tr>
                <th>ประเภทการสนับสนุน</th>
                <td>{{ $checkbody->special_support_type ?? '-' }}</td>
            </tr>

            @if(($checkbody->special_support_type ?? null) === 'อื่น ๆ')
                <tr>
                    <th>รายละเอียดอื่น ๆ</th>
                    <td>{{ $checkbody->special_support_other ?? '-' }}</td>
                </tr>
            @endif
        @endif

        <tr>
            <th>น้ำหนัก</th>
            <td>
                @if(!is_null($checkbody->weight) && $checkbody->weight !== '')
                    {{ $checkbody->weight }} กิโลกรัม
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <th>ส่วนสูง</th>
            <td>
                @if(!is_null($checkbody->height) && $checkbody->height !== '')
                    {{ $checkbody->height }} เซนติเมตร
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <th>สุขภาพช่องปาก</th>
            <td>{{ $checkbody->oral ?? '-' }}</td>
        </tr>
        <tr>
            <th>รูปร่าง / ลักษณะ</th>
            <td>{{ $checkbody->appearance ?? '-' }}</td>
        </tr>
        <tr>
            <th>ร่องรอย / บาดแผล</th>
            <td>{{ $checkbody->wound ?? '-' }}</td>
        </tr>
        <tr>
            <th>โรคประจำตัว</th>
            <td>{{ $checkbody->disease ?? '-' }}</td>
        </tr>
        <tr>
            <th>สุขอนามัย</th>
            <td>{{ $checkbody->hygiene ?? '-' }}</td>
        </tr>
        <tr>
            <th>สุขภาพ</th>
            <td>{{ $checkbody->health ?? '-' }}</td>
        </tr>
        <tr>
            <th>การปลูกฝี</th>
            <td>{{ $checkbody->inoculation ?? '-' }}</td>
        </tr>
        <tr>
            <th>การฉีดยา</th>
            <td>{{ $checkbody->injection ?? '-' }}</td>
        </tr>
        <tr>
            <th>การให้วัคซีน</th>
            <td>{{ $checkbody->vaccination ?? '-' }}</td>
        </tr>
        <tr>
            <th>โรคติดต่อ</th>
            <td>{{ $checkbody->contagious ?? '-' }}</td>
        </tr>
        <tr>
            <th>การเจ็บป่วยอื่น ๆ</th>
            <td>{{ $checkbody->other ?? '-' }}</td>
        </tr>
        <tr>
            <th>ประวัติการแพ้ยา</th>
            <td>{{ $checkbody->drug_allergy ?? '-' }}</td>
        </tr>
        <tr>
            <th>หมายเหตุ</th>
            <td>{{ $checkbody->remark ?? '-' }}</td>
        </tr>
    </table>

</div>
</body>
</html>