@extends('admin_client.admin_client')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

@php
    use Carbon\Carbon;

    if (!$client) {
        abort(404);
    }

    $profileImage = !empty($client->image)
        ? route('client.image', $client->id)
        : asset('upload/no_image.jpg');

    $birthDate = !empty($client->birth_date) ? Carbon::parse($client->birth_date) : null;
    $arrivalDate = !empty($client->arrival_date) ? Carbon::parse($client->arrival_date) : null;

    $clientFullName = $client->full_name
        ?? $client->fullname
        ?? trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));

    $educationName = optional(optional($educationRecord)->education)->education_name ?? 'ไม่ระบุ';
    $institutionName = optional(optional($educationRecord)->institution)->institution_name ?? 'ไม่ระบุ';

    $summaryItems = $overviewSummary['items'] ?? [];
    $summaryTotal = $overviewSummary['total_count'] ?? 0;
    $rangeStart = !empty($overviewSummary['start_date']) ? $overviewSummary['start_date'] : null;
    $rangeEnd = !empty($overviewSummary['end_date']) ? $overviewSummary['end_date'] : null;
    $summaryRows = $overviewSummary['rows'] ?? [];
@endphp

<div class="container-fluid overview-page">

    <div class="overview-toolbar mb-4">
        <div class="overview-toolbar-left">
            <a href="{{ route('admin.index', $client->id) }}" class="btn overview-btn overview-btn-primary">
                <i class="bi bi-arrow-left-short"></i>
                <span>ย้อนกลับ</span>
            </a>

            <a href="{{ route('client.report', $client->id) }}" class="btn overview-btn overview-btn-outline">
                <i class="bi bi-file-earmark-text"></i>
                <span>รายงานผู้รับบริการ</span>
            </a>
        </div>

        <div class="overview-toolbar-right">
            <div class="overview-badge">
                <i class="bi bi-grid-1x2-fill me-2"></i>
                <span>ข้อมูลภาพรวม</span>
            </div>
        </div>
    </div>

    <div class="overview-hero mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <div class="hero-kicker">ACADEMIC YEAR OVERVIEW</div>
                <h1 class="hero-title mb-2">สรุปข้อมูลตามปีการศึกษา</h1>
                <p class="hero-subtitle mb-0">
                    แสดงจำนวนครั้งของการขาดเรียน การติดตามที่โรงเรียน การติดตามเด็กที่อยู่ภายนอก และการเจ็บป่วย
                    โดยรองรับทั้งการดูปีการศึกษาเดียวและดูแบบช่วงปีการศึกษา
                </p>
            </div>

            <div class="col-lg-4">
                <div class="hero-summary-box">
                    <div class="hero-summary-label">
                        @if(($overviewSummary['mode'] ?? 'single') === 'range')
                            รวมทั้งหมดในช่วงปีการศึกษา {{ $startAcademicYear }} - {{ $endAcademicYear }}
                        @else
                            รวมทั้งหมดในปีการศึกษา {{ $selectedAcademicYear }}
                        @endif
                    </div>
                    <div class="hero-summary-value">{{ number_format($summaryTotal) }}</div>
                    <div class="hero-summary-subtext">ครั้ง</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card overview-profile-card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-xl-4 profile-panel-left text-center">
                    <div class="profile-image-wrap">
                        <img src="{{ $profileImage }}" alt="รูปผู้รับบริการ" class="profile-image">
                    </div>

                    <h3 class="profile-name">{{ $clientFullName ?: 'ไม่ระบุชื่อ' }}</h3>

                    <div class="profile-meta">
                        <div class="meta-pill">
                            <i class="bi bi-person-badge me-1"></i>
                            เลขทะเบียน: {{ $client->register_number ?? '-' }}
                        </div>

                        <div class="meta-pill">
                            <i class="bi bi-calendar-check me-1"></i>
                            วันที่รับเข้า:
                            @if($arrivalDate)
                                {{ $arrivalDate->locale('th')->translatedFormat('d F') }} {{ $arrivalDate->year + 543 }}
                            @else
                                ไม่ระบุ
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    <div class="profile-panel-right p-4 p-lg-5">
                        <div class="section-title-wrap mb-4">
                            <h4 class="section-title mb-1">รายละเอียดพื้นฐาน</h4>
                            <p class="section-subtitle mb-0">ข้อมูลตั้งต้นสำหรับอ้างอิงก่อนดูสรุปตามปีการศึกษา</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">ชื่อ - สกุล</div>
                                    <div class="info-value">{{ $clientFullName ?: 'ไม่ระบุ' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">วันเกิด</div>
                                    <div class="info-value">
                                        @if($birthDate)
                                            {{ $birthDate->locale('th')->translatedFormat('d F') }} {{ $birthDate->year + 543 }}
                                        @else
                                            ไม่ระบุ
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">อายุ</div>
                                    <div class="info-value">
                                        @if($birthDate)
                                            {{ $birthDate->age }} ปี
                                        @else
                                            ไม่ระบุ
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">บ้าน / หน่วยงาน</div>
                                    <div class="info-value">{{ optional($client->house)->name ?? optional($client->house)->house_name ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">ระดับการศึกษา</div>
                                    <div class="info-value">{{ $educationName }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">สถานศึกษา</div>
                                    <div class="info-value">{{ $institutionName }}</div>
                                </div>
                            </div>
                        </div>

                        @if($rangeStart && $rangeEnd)
                            <div class="overview-date-range mt-4">
                                <i class="bi bi-calendar-range me-2"></i>
                                ช่วงวันที่ที่ระบบใช้คำนวณ:
                                {{ $rangeStart->locale('th')->translatedFormat('d F') }} {{ $rangeStart->year + 543 }}
                                -
                                {{ $rangeEnd->locale('th')->translatedFormat('d F') }} {{ $rangeEnd->year + 543 }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="filter-head mb-3">
                <div>
                    <h4 class="section-title mb-1">เลือกช่วงปีการศึกษา</h4>
                    <p class="section-subtitle mb-0">
                        @if(($overviewSummary['mode'] ?? 'single') === 'range')
                            ระบบกำลังสรุปข้อมูลตั้งแต่ปีการศึกษา {{ $startAcademicYear }} ถึง {{ $endAcademicYear }}
                        @else
                            ระบบกำลังสรุปข้อมูลปีการศึกษา {{ $selectedAcademicYear }}
                        @endif
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.client.overview', $client->id) }}" class="year-filter-form">
                <div class="row g-3">
                    <div class="col-12 col-lg-4">
                        <label class="service-label">รูปแบบการดูข้อมูล</label>
                        <select name="filter_mode" id="filterModeSelect" class="form-select service-control">
                            <option value="single" {{ ($filterMode ?? 'single') === 'single' ? 'selected' : '' }}>ปีการศึกษาเดียว</option>
                            <option value="range" {{ ($filterMode ?? 'single') === 'range' ? 'selected' : '' }}>ช่วงปีการศึกษา</option>
                        </select>
                    </div>

                    <div class="col-12 col-lg-4 single-year-wrap {{ ($filterMode ?? 'single') === 'range' ? 'd-none' : '' }}">
                        <label class="service-label">ปีการศึกษา (พ.ศ.)</label>
                        <select name="academic_year" class="form-select service-control">
                            @foreach($academicYearOptions as $yearOption)
                                <option value="{{ $yearOption }}" {{ (int) $selectedAcademicYear === (int) $yearOption ? 'selected' : '' }}>
                                    ปีการศึกษา {{ $yearOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-lg-4 range-year-wrap {{ ($filterMode ?? 'single') === 'range' ? '' : 'd-none' }}">
                        <label class="service-label">ปีการศึกษาเริ่มต้น</label>
                        <select name="start_academic_year" class="form-select service-control">
                            @foreach($academicYearOptions as $yearOption)
                                <option value="{{ $yearOption }}" {{ (int) $startAcademicYear === (int) $yearOption ? 'selected' : '' }}>
                                    ปีการศึกษา {{ $yearOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-lg-4 range-year-wrap {{ ($filterMode ?? 'single') === 'range' ? '' : 'd-none' }}">
                        <label class="service-label">ปีการศึกษาสิ้นสุด</label>
                        <select name="end_academic_year" class="form-select service-control">
                            @foreach($academicYearOptions as $yearOption)
                                <option value="{{ $yearOption }}" {{ (int) $endAcademicYear === (int) $yearOption ? 'selected' : '' }}>
                                    ปีการศึกษา {{ $yearOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 d-flex flex-wrap gap-2 pt-1">
                        <button type="submit" class="btn overview-btn overview-btn-primary year-filter-btn">
                            <i class="bi bi-search"></i>
                            <span>แสดงข้อมูล</span>
                        </button>

                        <a href="{{ route('admin.client.overview', $client->id) }}" class="btn overview-btn overview-btn-outline year-filter-btn">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            <span>รีเซ็ต</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="overview-section-head mb-3">
        <div>
            <h4 class="section-title mb-1">
                @if(($overviewSummary['mode'] ?? 'single') === 'range')
                    สรุปจำนวนตั้งแต่ปีการศึกษา {{ $startAcademicYear }} ถึง {{ $endAcademicYear }}
                @else
                    สรุปจำนวนตามปีการศึกษา {{ $selectedAcademicYear }}
                @endif
            </h4>
            <p class="section-subtitle mb-0">จำนวนครั้งของแต่ละหมวดในช่วงปีการศึกษาที่เลือก</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @foreach($summaryItems as $item)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card mini-stat-card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mini-stat-icon {{ $item['icon_class'] }}">
                            <i class="bi {{ $item['icon'] }}"></i>
                        </div>

                        <div class="mini-stat-label">{{ $item['title'] }}</div>
                        <div class="mini-stat-value">{{ number_format($item['count']) }}</div>
                        <div class="mini-stat-text">{{ $item['description'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if(!empty($summaryRows))
        <div class="card summary-table-card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="summary-table-head mb-3">
                    <div>
                        <h4 class="section-title mb-1">ตารางสรุปรายปี</h4>
                        <p class="section-subtitle mb-0">ใช้ดูแนวโน้มย้อนหลังและเปรียบเทียบแต่ละปีการศึกษา</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table overview-summary-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ปีการศึกษา</th>
                                <th class="text-center">ขาดเรียน</th>
                                <th class="text-center">ติดตามที่โรงเรียน</th>
                                <th class="text-center">ติดตามภายนอก</th>
                                <th class="text-center">การเจ็บป่วย</th>
                                <th class="text-center">รวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summaryRows as $row)
                                <tr>
                                    <td class="fw-semibold">ปีการศึกษา {{ $row['academic_year'] ?? '-' }}</td>
                                    <td class="text-center">{{ number_format($row['absent_count'] ?? 0) }}</td>
                                    <td class="text-center">{{ number_format($row['school_followup_count'] ?? 0) }}</td>
                                    <td class="text-center">{{ number_format($row['case_outside_count'] ?? 0) }}</td>
                                    <td class="text-center">{{ number_format($row['medical_count'] ?? 0) }}</td>
                                    <td class="text-center fw-bold">{{ number_format($row['total_count'] ?? 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>รวมทั้งหมด</th>
                                <th class="text-center">{{ number_format(collect($summaryRows)->sum('absent_count')) }}</th>
                                <th class="text-center">{{ number_format(collect($summaryRows)->sum('school_followup_count')) }}</th>
                                <th class="text-center">{{ number_format(collect($summaryRows)->sum('case_outside_count')) }}</th>
                                <th class="text-center">{{ number_format(collect($summaryRows)->sum('medical_count')) }}</th>
                                <th class="text-center">{{ number_format(collect($summaryRows)->sum('total_count')) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

</div>

<style>
    .overview-page{
        background: linear-gradient(180deg, #f8fbff 0%, #f4f7fb 100%);
        min-height: 100vh;
        padding: 2rem 1.25rem 2rem;
        overflow-x: hidden;
        position: relative;
        z-index: 1;
    }

    .overview-page .overview-toolbar{
        margin-top: .25rem;
        position: relative;
        z-index: 2;
    }

    .overview-toolbar{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:1rem;
        flex-wrap:wrap;
    }

    .overview-toolbar-left{
        display:flex;
        align-items:center;
        gap:.75rem;
        flex-wrap:wrap;
    }

    .overview-toolbar-right{
        display:flex;
        align-items:center;
        justify-content:flex-end;
    }

    .overview-btn{
        border-radius:999px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:.5rem;
        font-weight:600;
        padding:.7rem 1rem;
        text-decoration:none;
        white-space:nowrap;
    }

    .overview-btn-primary{
        background:#0d6efd;
        border:1px solid #0d6efd;
        color:#fff;
    }

    .overview-btn-primary:hover{
        background:#0b5ed7;
        border-color:#0b5ed7;
        color:#fff;
    }

    .overview-btn-outline{
        background:#fff;
        border:1px solid rgba(13,110,253,.2);
        color:#0d6efd;
    }

    .overview-btn-outline:hover{
        background:#f8fbff;
        border-color:#0d6efd;
        color:#0a58ca;
    }

    .overview-badge{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:.65rem 1rem;
        border-radius:999px;
        background:#fff;
        border:1px solid rgba(13,110,253,.12);
        color:#0d6efd;
        font-weight:700;
        box-shadow:0 10px 30px rgba(15, 23, 42, 0.05);
        max-width:100%;
    }

    .overview-hero{
        background: linear-gradient(135deg, #ffffff 0%, #f7faff 100%);
        border: 1px solid rgba(13,110,253,.08);
        border-radius: 24px;
        padding: 2rem;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.06);
    }

    .hero-kicker{
        display:inline-block;
        font-size:.8rem;
        font-weight:700;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:#0d6efd;
        background:rgba(13,110,253,.08);
        padding:.45rem .8rem;
        border-radius:999px;
        margin-bottom:1rem;
    }

    .hero-title{
        font-size:clamp(1.7rem, 2vw, 2.4rem);
        font-weight:800;
        color:#1e293b;
        line-height:1.25;
    }

    .hero-subtitle{
        color:#64748b;
        font-size:1rem;
        line-height:1.7;
        max-width:720px;
    }

    .hero-summary-box{
        background:#fff;
        border:1px solid rgba(13,110,253,.10);
        border-radius:22px;
        padding:1.25rem;
        text-align:center;
        box-shadow:0 15px 35px rgba(15,23,42,.06);
        height:100%;
    }

    .hero-summary-label{
        color:#64748b;
        font-size:.92rem;
        margin-bottom:.4rem;
    }

    .hero-summary-value{
        font-size:2.4rem;
        font-weight:800;
        color:#0f172a;
        line-height:1.1;
    }

    .hero-summary-subtext{
        color:#475569;
        margin-top:.25rem;
        font-weight:600;
    }

    .overview-profile-card,
    .filter-card,
    .mini-stat-card,
    .summary-table-card{
        border-radius:24px;
    }

    .profile-panel-left{
        background: linear-gradient(180deg, #0d6efd 0%, #0b5ed7 100%);
        color:#fff;
        padding:2.25rem 1.25rem;
        display:flex;
        flex-direction:column;
        justify-content:center;
    }

    .profile-panel-right{
        height:100%;
    }

    .profile-image-wrap{
        margin-bottom:1.25rem;
    }

    .profile-image{
        width:160px;
        height:160px;
        object-fit:cover;
        border-radius:50%;
        border:5px solid rgba(255,255,255,.2);
        box-shadow:0 20px 40px rgba(0,0,0,.18);
        max-width:100%;
    }

    .profile-name{
        font-weight:800;
        font-size:1.5rem;
        line-height:1.35;
        margin-bottom:1rem;
        word-break:break-word;
    }

    .profile-meta{
        display:flex;
        flex-wrap:wrap;
        justify-content:center;
        gap:.65rem;
    }

    .meta-pill{
        background:rgba(255,255,255,.14);
        border:1px solid rgba(255,255,255,.18);
        color:#fff;
        border-radius:999px;
        padding:.55rem .9rem;
        font-size:.88rem;
        max-width:100%;
        text-align:center;
        word-break:break-word;
    }

    .section-title{
        font-weight:800;
        color:#0f172a;
    }

    .section-subtitle{
        color:#64748b;
    }

    .info-card{
        background:#fff;
        border:1px solid rgba(15,23,42,.06);
        border-radius:18px;
        padding:1rem 1.1rem;
        height:100%;
    }

    .info-label{
        color:#64748b;
        font-size:.88rem;
        margin-bottom:.35rem;
    }

    .info-value{
        color:#0f172a;
        font-weight:700;
        line-height:1.6;
        word-break:break-word;
    }

    .overview-date-range{
        background:#f8fafc;
        border:1px dashed #cbd5e1;
        color:#475569;
        border-radius:16px;
        padding:.9rem 1rem;
        line-height:1.6;
    }

    .filter-head,
    .summary-table-head{
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:1rem;
        flex-wrap:wrap;
    }

    .year-filter-form .service-label{
        display:inline-block;
        margin-bottom:.45rem;
        color:#475569;
        font-size:.9rem;
        font-weight:700;
    }

    .service-control{
        min-height:48px;
        border-radius:14px;
        border-color:rgba(15,23,42,.12);
        box-shadow:none;
    }

    .service-control:focus{
        border-color:#86b7fe;
        box-shadow:0 0 0 .2rem rgba(13,110,253,.12);
    }

    .year-filter-btn{
        min-width:150px;
    }

    .mini-stat-card{
        transition:transform .25s ease, box-shadow .25s ease;
    }

    .mini-stat-card:hover{
        transform:translateY(-6px);
        box-shadow:0 20px 40px rgba(15,23,42,.08) !important;
    }

    .mini-stat-icon{
        width:58px;
        height:58px;
        border-radius:18px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        font-size:1.35rem;
        margin-bottom:1rem;
    }

    .mini-stat-label{
        color:#334155;
        font-size:.95rem;
        font-weight:700;
        margin-bottom:.4rem;
    }

    .mini-stat-value{
        font-size:2rem;
        font-weight:800;
        color:#0f172a;
        line-height:1.1;
    }

    .mini-stat-text{
        color:#64748b;
        font-size:.92rem;
        margin-top:.4rem;
        line-height:1.6;
    }

    .overview-summary-table thead th{
        background:#f8fafc;
        color:#334155;
        font-weight:700;
        border-bottom:1px solid #e2e8f0;
        white-space:nowrap;
        padding:.95rem .9rem;
    }

    .overview-summary-table tbody td,
    .overview-summary-table tfoot th{
        border-color:#eef2f7;
        vertical-align:middle;
        padding:.95rem .9rem;
    }

    .overview-summary-table tbody tr:hover{
        background:#f8fbff;
    }

    .overview-summary-table tfoot th{
        background:#f8fafc;
    }

    @media (max-width: 1199.98px){
        .overview-page{
            padding:1.5rem 1rem 2rem;
        }

        .overview-toolbar{
            align-items:stretch;
        }

        .overview-toolbar-left,
        .overview-toolbar-right{
            width:100%;
        }

        .overview-toolbar-right{
            justify-content:flex-start;
        }
    }

    @media (max-width: 767.98px){
        .overview-page{
            padding:1rem .75rem 1.25rem;
        }

        .overview-toolbar-left{
            width:100%;
            flex-direction:column;
            align-items:stretch;
        }

        .overview-btn{
            width:100%;
            border-radius:18px;
        }

        .overview-badge{
            width:100%;
            border-radius:18px;
            justify-content:center;
            text-align:center;
        }

        .overview-hero{
            padding:1.2rem 1rem;
        }

        .hero-title{
            font-size:1.45rem;
        }

        .profile-image{
            width:128px;
            height:128px;
        }

        .profile-name{
            font-size:1.25rem;
        }

        .meta-pill{
            width:100%;
        }

        .year-filter-btn{
            width:100%;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modeSelect = document.getElementById('filterModeSelect');
    const singleWrap = document.querySelector('.single-year-wrap');
    const rangeWraps = document.querySelectorAll('.range-year-wrap');

    function toggleYearMode() {
        const isRange = modeSelect && modeSelect.value === 'range';

        if (singleWrap) {
            singleWrap.classList.toggle('d-none', isRange);
        }

        rangeWraps.forEach(function (el) {
            el.classList.toggle('d-none', !isRange);
        });
    }

    if (modeSelect) {
        modeSelect.addEventListener('change', toggleYearMode);
        toggleYearMode();
    }
});
</script>

@endsection