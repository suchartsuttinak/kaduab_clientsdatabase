@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตารางการจำหน่ายรวม</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


    <style>
        .refer-all-page,
        .refer-all-page *{
            box-sizing:border-box;
        }

        :root{
            --refer-all-text:#0f172a;
            --refer-all-text-soft:#334155;
            --refer-all-text-muted:#64748b;
            --refer-all-line:#e2e8f0;
            --refer-all-line-soft:#edf2f7;
            --refer-all-bg:#f8fafc;
            --refer-all-white:#ffffff;
            --refer-all-primary:#2563eb;
            --refer-all-primary-dark:#1d4ed8;
            --refer-all-teal:#0f766e;
            --refer-all-teal-dark:#115e59;
            --refer-all-success:#166534;
            --refer-all-warning:#b45309;
            --refer-all-danger:#b91c1c;
            --refer-all-shadow:0 10px 30px rgba(15,23,42,.05);
            --refer-all-radius-lg:18px;
            --refer-all-radius-md:14px;
            --refer-all-radius-sm:12px;
        }

        html{
            font-size:16px;
        }

        body{
            margin:0;
            background:var(--refer-all-bg);
            font-family:"Sarabun","TH Sarabun New","Noto Sans Thai","Segoe UI",Tahoma,sans-serif;
            color:var(--refer-all-text);
            text-rendering:optimizeLegibility;
            -webkit-font-smoothing:antialiased;
            -moz-osx-font-smoothing:grayscale;
        }

        .refer-all-page{
            max-width:1400px;
            margin:0 auto;
            padding:clamp(18px, 2vw, 28px) clamp(12px, 1.8vw, 18px) 42px;
        }

        .refer-all-header{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:18px;
            flex-wrap:wrap;
            margin-bottom:20px;
            padding-bottom:16px;
            border-bottom:1px solid var(--refer-all-line);
        }

        .refer-all-title-wrap{
            display:flex;
            gap:14px;
            align-items:flex-start;
        }

        .refer-all-icon{
            width:50px;
            height:50px;
            border-radius:15px;
            display:flex;
            align-items:center;
            justify-content:center;
            background:linear-gradient(135deg,#eff6ff 0%,#eef2ff 100%);
            color:var(--refer-all-primary);
            box-shadow:inset 0 0 0 1px #dbeafe;
            flex-shrink:0;
        }

        .refer-all-icon i{
            font-size:21px;
        }

        .refer-all-title h1{
            margin:0;
            font-size:clamp(1.45rem, 1.2rem + .9vw, 1.9rem);
            font-weight:800;
            line-height:1.18;
            letter-spacing:-.02em;
            color:var(--refer-all-text);
        }

        .refer-all-title p{
            margin:6px 0 0;
            font-size:clamp(.92rem, .88rem + .15vw, 1rem);
            color:var(--refer-all-text-muted);
            line-height:1.65;
            max-width:920px;
        }

        .refer-all-actions{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
        }

        .refer-all-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            min-height:42px;
            padding:10px 16px;
            border-radius:12px;
            border:1px solid #d6e0ea;
            background:#fff;
            color:var(--refer-all-text);
            text-decoration:none;
            font-size:clamp(.88rem, .86rem + .06vw, .95rem);
            font-weight:700;
            line-height:1;
            cursor:pointer;
            transition:all .2s ease;
            white-space:nowrap;
            box-shadow:0 4px 14px rgba(15,23,42,.03);
        }

        .refer-all-btn:hover{
            background:#f8fbff;
            border-color:#bfd0e3;
            color:var(--refer-all-text);
            transform:translateY(-1px);
        }

        .refer-all-btn-primary{
            background:linear-gradient(135deg,var(--refer-all-primary) 0%,var(--refer-all-primary-dark) 100%);
            border-color:var(--refer-all-primary-dark);
            color:#fff;
            box-shadow:0 10px 24px rgba(37,99,235,.18);
        }

        .refer-all-btn-primary:hover{
            color:#fff;
            background:linear-gradient(135deg,var(--refer-all-primary-dark) 0%,#1e40af 100%);
        }

        .refer-all-btn-report{
            min-height:36px;
            padding:8px 12px;
            border-radius:10px;
            font-size:.84rem;
            background:linear-gradient(135deg,var(--refer-all-teal) 0%,#0d9488 100%);
            border-color:var(--refer-all-teal);
            color:#fff;
            box-shadow:0 8px 18px rgba(15,118,110,.14);
        }

        .refer-all-btn-report:hover{
            color:#fff;
            background:linear-gradient(135deg,var(--refer-all-teal-dark) 0%,var(--refer-all-teal) 100%);
            border-color:var(--refer-all-teal-dark);
        }

        .refer-all-filter{
            background:var(--refer-all-white);
            border:1px solid var(--refer-all-line);
            border-radius:var(--refer-all-radius-lg);
            padding:18px;
            margin-bottom:18px;
            box-shadow:var(--refer-all-shadow);
        }

        .refer-all-filter-title{
            font-size:clamp(1.06rem, 1rem + .2vw, 1.18rem);
            font-weight:800;
            color:var(--refer-all-text);
            margin-bottom:14px;
            letter-spacing:-.01em;
        }

        .refer-all-grid{
            display:grid;
            grid-template-columns:repeat(12, minmax(0, 1fr));
            gap:12px;
        }

        .refer-all-col-2{ grid-column:span 2; }
        .refer-all-col-3{ grid-column:span 3; }
        .refer-all-col-4{ grid-column:span 4; }
        .refer-all-col-6{ grid-column:span 6; }
        .refer-all-col-12{ grid-column:span 12; }

        .refer-all-label{
            display:block;
            margin-bottom:6px;
            font-size:.92rem;
            font-weight:700;
            color:var(--refer-all-text-soft);
            line-height:1.45;
        }

        .refer-all-input,
        .refer-all-select{
            width:100%;
            min-height:44px;
            border:1px solid #d7dfeb;
            border-radius:12px;
            padding:10px 12px;
            font-size:.95rem;
            font-family:inherit;
            color:var(--refer-all-text);
            background:#fff;
            outline:none;
            transition:border-color .2s ease, box-shadow .2s ease;
        }

        .refer-all-input::placeholder{
            color:#94a3b8;
        }

        .refer-all-input:focus,
        .refer-all-select:focus{
            border-color:#93c5fd;
            box-shadow:0 0 0 3px rgba(59,130,246,.10);
        }

        .refer-all-filter-actions{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            align-items:center;
            margin-top:16px;
        }

        .refer-all-summary{
            display:grid;
            grid-template-columns:repeat(4, minmax(0, 1fr));
            gap:12px;
            margin-bottom:18px;
        }

        .refer-all-stat{
            background:#fff;
            border:1px solid var(--refer-all-line);
            border-radius:16px;
            padding:15px 16px;
            box-shadow:var(--refer-all-shadow);
        }

        .refer-all-stat-label{
            font-size:.88rem;
            color:var(--refer-all-text-muted);
            margin-bottom:7px;
            line-height:1.4;
        }

        .refer-all-stat-value{
            font-size:clamp(1.35rem, 1.15rem + .7vw, 1.72rem);
            font-weight:800;
            color:var(--refer-all-text);
            line-height:1.05;
            letter-spacing:-.02em;
        }

        .refer-all-table-wrap{
            background:#fff;
            border:1px solid var(--refer-all-line);
            border-radius:var(--refer-all-radius-lg);
            overflow:hidden;
            box-shadow:var(--refer-all-shadow);
        }

        .refer-all-table-head{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
            padding:15px 16px;
            border-bottom:1px solid var(--refer-all-line);
        }

        .refer-all-table-title{
            font-size:clamp(1.06rem, 1rem + .22vw, 1.18rem);
            font-weight:800;
            color:var(--refer-all-text);
            letter-spacing:-.01em;
        }

        .refer-all-table-meta{
            font-size:.92rem;
            color:var(--refer-all-text-muted);
        }

        .refer-all-scroll{
            width:100%;
            overflow-x:auto;
            -webkit-overflow-scrolling:touch;
        }

        .refer-all-table{
            width:100%;
            min-width:980px;
            border-collapse:collapse;
        }

        .refer-all-table thead th{
            padding:13px 10px;
            background:#f8fafc;
            color:var(--refer-all-text);
            font-size:.95rem;
            font-weight:800;
            text-align:left;
            border-bottom:1px solid var(--refer-all-line);
            white-space:nowrap;
            letter-spacing:-.01em;
        }

        .refer-all-table tbody td{
            padding:13px 10px;
            font-size:.95rem;
            color:#1f2937;
            border-bottom:1px solid var(--refer-all-line-soft);
            vertical-align:top;
            background:#fff;
            line-height:1.65;
        }

        .refer-all-table tbody tr:nth-child(even) td{
            background:#fcfdff;
        }

        .refer-all-badge{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:6px;
            min-height:28px;
            padding:4px 10px;
            border-radius:999px;
            font-size:.82rem;
            font-weight:700;
            white-space:nowrap;
            border:1px solid transparent;
            line-height:1.15;
        }

        .refer-all-badge-approved{
            background:#ecfdf3;
            color:var(--refer-all-success);
            border-color:#bbf7d0;
        }

        .refer-all-badge-pending{
            background:#fff7ed;
            color:var(--refer-all-warning);
            border-color:#fed7aa;
        }

        .refer-all-badge-cancelled{
            background:#fef2f2;
            color:var(--refer-all-danger);
            border-color:#fecaca;
        }

        .refer-all-badge-committee-pass{
            background:#eff6ff;
            color:#1d4ed8;
            border-color:#bfdbfe;
        }

        .refer-all-badge-committee-fail{
            background:#f1f5f9;
            color:#475569;
            border-color:#cbd5e1;
        }

        .refer-all-link{
            color:var(--refer-all-primary);
            text-decoration:none;
            font-weight:700;
            font-size:.94rem;
        }

        .refer-all-link:hover{
            text-decoration:underline;
        }

        .refer-all-empty{
            padding:30px 16px;
            text-align:center;
            color:var(--refer-all-text-muted);
            font-size:.98rem;
        }

        .refer-all-pagination{
            padding:16px;
            background:#fff;
        }

        .refer-all-name{
            font-weight:700;
            color:var(--refer-all-text);
        }

        .refer-all-note{
            color:#475569;
            line-height:1.7;
            min-width:180px;
        }

        .text-center{
            text-align:center;
        }

        .refer-all-empty-card{
            min-height:320px;
            padding:2.5rem 1.25rem;
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            text-align:center;
            background:#fff;
            border:1px solid var(--refer-all-line);
            border-radius:var(--refer-all-radius-lg);
            box-shadow:var(--refer-all-shadow);
        }

        .refer-all-empty-icon{
            width:82px;
            height:82px;
            margin-bottom:1rem;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border:1px solid #c7d2fe;
            border-radius:50%;
            background:#eef2ff;
            color:#4f46e5;
            font-size:1.7rem;
        }

        .refer-all-empty-title{
            margin:0;
            color:var(--refer-all-text);
            font-size:1.15rem;
            font-weight:800;
            line-height:1.45;
        }

        .refer-all-empty-description{
            max-width:720px;
            margin:.55rem auto 0;
            color:var(--refer-all-text-muted);
            font-size:.92rem;
            line-height:1.65;
        }

        .refer-all-validation{
            margin-bottom:16px;
            padding:14px 16px;
            border:1px solid #fecaca;
            border-radius:14px;
            background:#fef2f2;
            color:#991b1b;
        }

        @media (max-width: 1200px){
            .refer-all-col-2,
            .refer-all-col-3,
            .refer-all-col-4{
                grid-column:span 6;
            }

            .refer-all-col-6{
                grid-column:span 12;
            }

            .refer-all-summary{
                grid-template-columns:repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px){
            html{
                font-size:15px;
            }

            .refer-all-page{
                padding:18px 12px 28px;
            }

            .refer-all-col-2,
            .refer-all-col-3,
            .refer-all-col-4,
            .refer-all-col-6,
            .refer-all-col-12{
                grid-column:span 12;
            }

            .refer-all-summary{
                grid-template-columns:1fr;
            }

            .refer-all-btn{
                width:100%;
            }

            .refer-all-actions,
            .refer-all-filter-actions{
                width:100%;
            }

            .refer-all-table{
                min-width:880px;
            }

            .refer-all-filter,
            .refer-all-stat,
            .refer-all-table-wrap{
                border-radius:16px;
            }
        }

        @media (max-width: 576px){
            html{
                font-size:14px;
            }

            .refer-all-title-wrap{
                gap:12px;
            }

            .refer-all-icon{
                width:44px;
                height:44px;
                border-radius:12px;
            }

            .refer-all-icon i{
                font-size:18px;
            }

            .refer-all-filter{
                padding:14px;
            }

            .refer-all-table-head,
            .refer-all-pagination{
                padding:14px;
            }
        }
    </style>
</head>
<body>
    <div class="refer-all-page">
        <div class="refer-all-header">
            <div class="refer-all-title-wrap">
                <div class="refer-all-icon">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <div class="refer-all-title">
                    <h1>ตารางการจำหน่ายรวม</h1>
                    <p>สำหรับผู้ดูแลระบบและผู้บริหาร ใช้ตรวจสอบข้อมูลย้อนหลัง ค้นหาตามวัน เดือน ปี และติดตามสถานะการจำหน่ายได้ในหน้าเดียว</p>
                </div>
            </div>

            <div class="refer-all-actions">
                <a href="{{ url('/dashboard') }}" class="refer-all-btn">
                    <i class="bi bi-arrow-left"></i>
                    <span>กลับหน้าหลัก</span>
                </a>
            </div>
        </div>

        @php
            $hasAnyRefers = $hasAnyRefers ?? true;
        @endphp

        @if($errors->any())
            <div class="refer-all-validation" role="alert">
                <div class="fw-bold mb-1">กรุณาตรวจสอบเงื่อนไขการค้นหา</div>
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if($hasAnyRefers)
        <form method="GET" action="{{ route('refers.all') }}" class="refer-all-filter">
            <div class="refer-all-filter-title">ค้นหาและกรองข้อมูล</div>

            <div class="refer-all-grid">
                <div class="refer-all-col-2">
                    <label class="refer-all-label">วันที่เริ่มต้น</label>
                    <input type="date" name="date_from" class="refer-all-input" value="{{ old('date_from', request('date_from')) }}" max="{{ now('Asia/Bangkok')->toDateString() }}">
                </div>

                <div class="refer-all-col-2">
                    <label class="refer-all-label">วันที่สิ้นสุด</label>
                    <input type="date" name="date_to" class="refer-all-input" value="{{ old('date_to', request('date_to')) }}" max="{{ now('Asia/Bangkok')->toDateString() }}">
                </div>

                <div class="refer-all-col-2">
                    <label class="refer-all-label">ปี</label>
                    <select name="year" class="refer-all-select">
                        <option value="">ทั้งหมด</option>
                        @for($y = now()->year; $y >= now()->year - 10; $y--)
                            <option value="{{ $y }}" {{ old('year', request('year')) == $y ? 'selected' : '' }}>
                                {{ $y + 543 }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="refer-all-col-2">
                    <label class="refer-all-label">เดือน</label>
                    <select name="month" class="refer-all-select">
                        <option value="">ทั้งหมด</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ old('month', request('month')) == $m ? 'selected' : '' }}>
                                {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="refer-all-col-2">
                    <label class="refer-all-label">สถานะอนุมัติ</label>
                    <select name="approve_status" class="refer-all-select">
                        <option value="">ทั้งหมด</option>
                        <option value="approved" {{ old('approve_status', request('approve_status')) === 'approved' ? 'selected' : '' }}>อนุมัติแล้ว</option>
                        <option value="pending" {{ old('approve_status', request('approve_status')) === 'pending' ? 'selected' : '' }}>รออนุมัติ</option>
                        <option value="cancelled" {{ old('approve_status', request('approve_status')) === 'cancelled' ? 'selected' : '' }}>ยกเลิกแล้ว</option>
                    </select>
                </div>

                <div class="refer-all-col-2">
                    <label class="refer-all-label">ผลคณะกรรมการฯ</label>
                    <select name="committee_result" class="refer-all-select">
                        <option value="">ทั้งหมด</option>
                        <option value="ผ่าน" {{ old('committee_result', request('committee_result')) === 'ผ่าน' ? 'selected' : '' }}>ผ่าน</option>
                        <option value="ไม่ผ่าน" {{ old('committee_result', request('committee_result')) === 'ไม่ผ่าน' ? 'selected' : '' }}>ไม่ผ่าน</option>
                    </select>
                </div>

                <div class="refer-all-col-12">
                    <label class="refer-all-label">ค้นหาชื่อผู้รับ / สาเหตุการจำหน่าย / หมายเหตุ</label>
                    <input type="text"
                           name="keyword"
                           class="refer-all-input"
                           value="{{ old('keyword', request('keyword')) }}"
                           placeholder="พิมพ์คำค้นหา...">
                </div>
            </div>

            <div class="refer-all-filter-actions">
                <button type="submit" class="refer-all-btn refer-all-btn-primary">
                    <i class="bi bi-search"></i>
                    <span>ค้นหา</span>
                </button>

                <a href="{{ route('refers.all') }}" class="refer-all-btn">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>ล้างค่า</span>
                </a>
            </div>
        </form>

        <div class="refer-all-summary">
            <div class="refer-all-stat">
                <div class="refer-all-stat-label">รายการทั้งหมด</div>
                <div class="refer-all-stat-value">{{ number_format($summary['total']) }}</div>
            </div>

            <div class="refer-all-stat">
                <div class="refer-all-stat-label">อนุมัติแล้ว</div>
                <div class="refer-all-stat-value">{{ number_format($summary['approved']) }}</div>
            </div>

            <div class="refer-all-stat">
                <div class="refer-all-stat-label">รออนุมัติ</div>
                <div class="refer-all-stat-value">{{ number_format($summary['pending']) }}</div>
            </div>

            <div class="refer-all-stat">
                <div class="refer-all-stat-label">ยกเลิกแล้ว</div>
                <div class="refer-all-stat-value">{{ number_format($summary['cancelled']) }}</div>
            </div>
        </div>

        <div class="refer-all-table-wrap">
            <div class="refer-all-table-head">
                <div class="refer-all-table-title">รายการจำหน่ายย้อนหลังทั้งหมด</div>
                <div class="refer-all-table-meta">แสดง {{ $refers->count() }} รายการในหน้านี้</div>
            </div>

            <div class="refer-all-scroll">
                <table class="refer-all-table">
                    <thead>
                        <tr>
                            <th>วันที่จำหน่าย</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>สาเหตุการจำหน่าย</th>
                            <th>คณะกรรมการ</th>
                            <th>รายงานประชุม</th>
                            <th>สถานะอนุมัติ</th>
                            <th>หมายเหตุ</th>
                            <th class="text-center">รายงาน</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($refers as $item)
                            <tr>
                                <td>
                                    {{ !empty($item->refer_date) ? Carbon::parse($item->refer_date)->addYears(543)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="refer-all-name">{{ $item->client->fullname ?? '-' }}</td>
                                <td>{{ $item->translate->translate_name ?? $item->translate->name ?? '-' }}</td>
                                <td>
                                    @if(($item->committee_result ?? 'ไม่ผ่าน') === 'ผ่าน')
                                        <span class="refer-all-badge refer-all-badge-committee-pass">ผ่าน</span>
                                    @else
                                        <span class="refer-all-badge refer-all-badge-committee-fail">ไม่ผ่าน</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($item->meeting_report_file))
                                        <a href="{{ asset('uploads/refer_meeting_reports/' . $item->meeting_report_file) }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="refer-all-link">
                                            เปิด PDF
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if(($item->approve_status ?? '') === 'approved')
                                        <span class="refer-all-badge refer-all-badge-approved">อนุมัติแล้ว</span>
                                    @elseif(($item->approve_status ?? '') === 'pending')
                                        <span class="refer-all-badge refer-all-badge-pending">รออนุมัติ</span>
                                    @elseif(($item->approve_status ?? '') === 'cancelled')
                                        <span class="refer-all-badge refer-all-badge-cancelled">ยกเลิกแล้ว</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="refer-all-note">{{ $item->remark ?? '-' }}</td>
                                <td class="text-center">
                                    @if(!empty($item->client_id))
                                        <a href="{{ route('refers.report', $item->client_id) }}" class="refer-all-btn refer-all-btn-report">
                                            <i class="bi bi-file-earmark-text"></i>
                                            <span>รายงาน</span>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="refer-all-empty">
                                        ยังไม่พบข้อมูลการจำหน่ายตามเงื่อนไขที่เลือก
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="refer-all-pagination">
                {{ $refers->links() }}
            </div>
        </div>
        @else
            <div class="refer-all-empty-card" role="status">
                <div class="refer-all-empty-icon" aria-hidden="true">
                    <i class="bi bi-box-arrow-right"></i>
                </div>

                <h2 class="refer-all-empty-title">ยังไม่มีข้อมูลการจำหน่ายผู้รับบริการ</h2>

                <p class="refer-all-empty-description">
                    เมื่อมีการบันทึกข้อมูลการจำหน่าย รายการ สถานะการอนุมัติ
                    และรายงานที่เกี่ยวข้องจะแสดงในหน้านี้
                </p>
            </div>
        @endif
    </div>
</body>
</html>