@extends('layouts.landing_pages')

@section('content')
<section class="scholarship-admin-page">
    <style>
        .scholarship-admin-page {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, .12), transparent 34%),
                radial-gradient(circle at top right, rgba(16, 185, 129, .10), transparent 30%),
                linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
            padding: 42px 16px;
        }

        .scholarship-container {
            max-width: 1280px;
            margin: 0 auto;
        }

        .scholarship-hero,
        .filter-card,
        .year-summary-card,
        .scholarship-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 30px;
            box-shadow: 0 22px 55px rgba(15, 23, 42, .09);
        }

        .scholarship-hero {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 58%, #eff6ff 100%);
            padding: 30px;
            margin-bottom: 22px;
        }

        .scholarship-hero-top {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .scholarship-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 14px;
        }

        .scholarship-title {
            margin: 0;
            color: #0f172a;
            font-size: clamp(26px, 3vw, 40px);
            font-weight: 900;
            letter-spacing: -.03em;
            line-height: 1.2;
        }

        .scholarship-desc {
            max-width: 760px;
            margin: 12px 0 0;
            color: #64748b;
            line-height: 1.8;
            font-size: 15px;
        }

        .scholarship-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .scholarship-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 14px;
            padding: 11px 16px;
            font-size: 14px;
            font-weight: 800;
            text-decoration: none;
            border: 1px solid transparent;
            transition: .2s ease;
            white-space: nowrap;
            line-height: 1.2;
            cursor: pointer;
        }

        .scholarship-btn-outline {
            color: #334155;
            background: #ffffff;
            border-color: #cbd5e1;
        }

        .scholarship-btn-outline:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #94a3b8;
            transform: translateY(-1px);
        }

        .scholarship-btn-primary {
            color: #ffffff;
            background: #2563eb;
            box-shadow: 0 10px 24px rgba(37, 99, 235, .25);
        }

        .scholarship-btn-primary:hover {
            background: #1d4ed8;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .scholarship-btn-soft {
            color: #047857;
            background: #ecfdf5;
            border-color: #bbf7d0;
        }

        .scholarship-btn-soft:hover {
            color: #065f46;
            background: #d1fae5;
            border-color: #86efac;
            transform: translateY(-1px);
        }

        .scholarship-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 22px;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            padding: 20px;
            box-shadow: 0 14px 34px rgba(15, 23, 42, .07);
        }

        .stat-card::after {
            content: "";
            position: absolute;
            right: -26px;
            top: -26px;
            width: 86px;
            height: 86px;
            border-radius: 999px;
            background: rgba(37, 99, 235, .08);
        }

        .stat-label {
            color: #64748b;
            font-size: 13px;
            font-weight: 800;
        }

        .stat-value {
            margin-top: 8px;
            color: #0f172a;
            font-size: 30px;
            font-weight: 900;
            line-height: 1;
        }

        .filter-card {
            padding: 24px;
            margin-bottom: 22px;
        }

        .filter-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .filter-title {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 900;
        }

        .filter-desc {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.7;
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr)) auto auto;
            gap: 14px;
            align-items: end;
        }

        .filter-group label {
            display: block;
            color: #475569;
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 7px;
        }

        .filter-control {
            width: 100%;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            border-radius: 16px;
            padding: 12px 14px;
            font-size: 14px;
            outline: none;
            transition: .2s ease;
        }

        .filter-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
        }

        .filter-result {
            margin-top: 16px;
            border-radius: 18px;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 12px 14px;
            font-size: 14px;
            font-weight: 800;
        }

        .year-summary-card {
            padding: 24px;
            margin-bottom: 22px;
        }

        .year-summary-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .year-summary-head h2 {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 900;
        }

        .year-summary-head p {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 14px;
        }

        .total-donation-box {
            min-width: 240px;
            border-radius: 24px;
            padding: 18px 20px;
            background: linear-gradient(135deg, #059669, #047857);
            color: #ffffff;
            box-shadow: 0 14px 30px rgba(5, 150, 105, .24);
        }

        .total-donation-box span,
        .total-donation-box small {
            display: block;
            font-size: 13px;
            opacity: .9;
        }

        .total-donation-box strong {
            display: block;
            margin-top: 7px;
            font-size: 30px;
            font-weight: 900;
            line-height: 1;
        }

        .year-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .year-card {
            border-radius: 24px;
            padding: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .year-card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .year-card-top span {
            color: #64748b;
            font-size: 13px;
            font-weight: 800;
        }

        .year-card-top strong {
            color: #2563eb;
            font-size: 26px;
            font-weight: 900;
        }

        .year-card-amount {
            margin-top: 16px;
            color: #047857;
            font-size: 28px;
            font-weight: 900;
        }

        .year-card-amount span {
            color: #64748b;
            font-size: 13px;
            font-weight: 800;
        }

        .year-card-footer {
            margin-top: 8px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .year-empty {
            border: 1px dashed #cbd5e1;
            border-radius: 22px;
            background: #f8fafc;
            padding: 28px;
            text-align: center;
            color: #64748b;
        }

        .scholarship-card {
            overflow: hidden;
        }

        .scholarship-card-head {
            padding: 22px 24px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border-bottom: 1px solid #e5e7eb;
        }

        .scholarship-card-title {
            margin: 0;
            color: #0f172a;
            font-size: 21px;
            font-weight: 900;
        }

        .scholarship-card-subtitle {
            margin-top: 5px;
            color: #64748b;
            font-size: 13px;
        }

        .scholarship-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .scholarship-table {
            width: 100%;
            min-width: 1160px;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .scholarship-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 900;
            padding: 15px 16px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
            text-align: left;
        }

        .scholarship-table td {
            color: #334155;
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
            font-size: 14px;
        }

        .scholarship-table tbody tr:hover {
            background: #f8fafc;
        }

        .number-badge {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            background: #eff6ff;
            color: #1d4ed8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
        }

        .person-name {
            color: #0f172a;
            font-weight: 900;
            white-space: nowrap;
        }

        .muted-text {
            color: #94a3b8;
        }

        .support-pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 11px;
            border-radius: 999px;
            background: #ecfdf5;
            color: #047857;
            font-size: 12px;
            font-weight: 900;
            margin: 2px;
            white-space: nowrap;
            border: 1px solid #bbf7d0;
        }

        .detail-text {
            max-width: 320px;
            line-height: 1.65;
            color: #475569;
        }

        .table-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 160px;
        }

        .table-actions .scholarship-btn {
            width: 100%;
            padding: 9px 12px;
            font-size: 13px;
            border-radius: 12px;
        }

        .empty-box {
            padding: 64px 20px;
            text-align: center;
            color: #64748b;
        }

        .empty-icon {
            width: 62px;
            height: 62px;
            border-radius: 22px;
            background: #eff6ff;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 14px;
        }

        .empty-title {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 900;
        }

        .empty-desc {
            margin-top: 8px;
            color: #64748b;
        }

        .pagination-wrap {
            padding: 16px 20px;
            border-top: 1px solid #e5e7eb;
            background: #ffffff;
        }

        @media (max-width: 992px) {
            .filter-form {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .filter-form .scholarship-btn {
                width: 100%;
            }

            .year-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .scholarship-admin-page {
                padding: 24px 12px;
            }

            .scholarship-hero,
            .filter-card,
            .year-summary-card {
                padding: 22px;
                border-radius: 24px;
            }

            .scholarship-actions,
            .scholarship-btn {
                width: 100%;
            }

            .scholarship-stats,
            .filter-form,
            .year-summary-grid {
                grid-template-columns: 1fr;
            }

            .total-donation-box {
                width: 100%;
            }

            .scholarship-card {
                border-radius: 24px;
            }

            .scholarship-card-head {
                padding: 18px;
            }

            .scholarship-table {
                min-width: 1120px;
            }
        }
    </style>

    <div class="scholarship-container">

        <div class="scholarship-hero">
            <div class="scholarship-hero-top">
                <div>
                    <div class="scholarship-badge">
                        🎓 Scholarship Support
                    </div>

                    <h1 class="scholarship-title">
                        รายการผู้สนใจสนับสนุนทุนการศึกษาเด็ก
                    </h1>

                    <p class="scholarship-desc">
                        หน้านี้สำหรับผู้ดูแลระบบตรวจสอบข้อมูลผู้มีจิตศรัทธาที่ส่งมาจากหน้าบ้าน
                        สามารถค้นหาการบริจาคตามช่วงวันที่ และบันทึกข้อมูลการบริจาคจริงต่อได้จากแต่ละรายการ
                    </p>
                </div>

                <div class="scholarship-actions">
                    <a href="{{ url('/dashboard') }}" class="scholarship-btn scholarship-btn-outline">
                        ← กลับหน้า Dashboard
                    </a>

                    {{-- EPC_SCHOLARSHIP_VIEW_ACTIONS_V1 --}}
                    @if(auth()->user()?->canCreateForm('dashboard_scholarship_sponsors'))
                        <a href="{{ route('scholarship.create') }}" class="scholarship-btn scholarship-btn-primary">
                            + เพิ่มข้อมูลผู้สนับสนุน
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="filter-card">
            <div class="filter-head">
                <div>
                    <h2 class="filter-title">ค้นหาการบริจาคตามช่วงวันที่</h2>
                    <p class="filter-desc">
                        เลือกวันที่เริ่มต้นและวันที่สิ้นสุด เพื่อดูยอดบริจาคและรายชื่อผู้สนับสนุนในช่วงเวลาที่ต้องการ
                    </p>
                </div>
            </div>

            @if($errors->any())
                <div class="filter-result" style="border-color:#fecaca;background:#fef2f2;color:#b91c1c;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="GET" action="{{ route('scholarship.index') }}" class="filter-form">
                <div class="filter-group">
                    <label for="start_date">วันที่เริ่มต้น</label>
                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        value="{{ request('start_date', $startDate ?? '') }}"
                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                        class="filter-control"
                    >
                </div>

                <div class="filter-group">
                    <label for="end_date">วันที่สิ้นสุด</label>
                    <input
                        type="date"
                        id="end_date"
                        name="end_date"
                        value="{{ request('end_date', $endDate ?? '') }}"
                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                        class="filter-control"
                    >
                </div>

                <button type="submit" class="scholarship-btn scholarship-btn-primary">
                    ค้นหา
                </button>

                <a href="{{ route('scholarship.index') }}" class="scholarship-btn scholarship-btn-outline">
                    ล้างค่า
                </a>
            </form>

            @if($startDate || $endDate)
                <div class="filter-result">
                    @if($startDate && $endDate)
                        กำลังแสดงข้อมูลตั้งแต่
                        {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
                        ถึง {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                    @elseif($startDate)
                        กำลังแสดงข้อมูลตั้งแต่วันที่
                        {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} เป็นต้นไป
                    @else
                        กำลังแสดงข้อมูลถึงวันที่
                        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                    @endif
                </div>
            @endif
        </div>

        <div class="scholarship-stats">
            <div class="stat-card">
                <div class="stat-label">รายการในหน้านี้</div>
                <div class="stat-value">{{ $scholarships->count() }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">รายการทั้งหมดที่พบ</div>
                <div class="stat-value">{{ $scholarships->total() }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">หน้าปัจจุบัน</div>
                <div class="stat-value">{{ $scholarships->currentPage() }}</div>
            </div>
        </div>

        <div class="year-summary-card">
            <div class="year-summary-head">
                <div>
                    <h2>สรุปยอดบริจาค</h2>
                    <p>
                        @if($startDate || $endDate)
                            สรุปยอดเงินบริจาคตามช่วงวันที่ที่เลือก
                        @else
                            สรุปยอดเงินบริจาครวมจากผู้สนับสนุนทั้งหมด
                        @endif
                    </p>
                </div>

                <div class="total-donation-box">
                    <span>
                        @if($startDate || $endDate)
                            รวมเงินบริจาคตามช่วงวันที่
                        @else
                            รวมเงินบริจาคทั้งหมด
                        @endif
                    </span>
                    <strong>{{ number_format($totalDonationAmount ?? 0, 2) }}</strong>
                    <small>บาท</small>
                </div>
            </div>

            @if(isset($donationYearSummary) && $donationYearSummary->count())
                <div class="year-summary-grid">
                    @foreach($donationYearSummary as $summary)
                        <div class="year-card">
                            <div class="year-card-top">
                                <span>ปีบริจาค</span>
                                <strong>{{ $summary->year + 543 }}</strong>
                            </div>

                            <div class="year-card-amount">
                                {{ number_format($summary->total_amount, 2) }}
                                <span>บาท</span>
                            </div>

                            <div class="year-card-footer">
                                {{ number_format($summary->total_items ?? 0) }} รายการบริจาค
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="year-empty">
                    ยังไม่มีข้อมูลการบริจาคในช่วงวันที่ที่เลือก
                </div>
            @endif
        </div>

        <div class="scholarship-card">
            <div class="scholarship-card-head">
                <div>
                    <h2 class="scholarship-card-title">
                        ข้อมูลการสนับสนุน
                    </h2>
                    <div class="scholarship-card-subtitle">
                        เรียงจากรายการล่าสุด สามารถเลื่อนตารางซ้าย-ขวาได้บนมือถือ
                    </div>
                </div>
            </div>

            @if($scholarships->count())
                <div class="scholarship-table-wrap">
                    <table class="scholarship-table">
                        <thead>
                            <tr>
                                <th style="width: 70px;">ลำดับ</th>
                                <th>ชื่อ-สกุล</th>
                                <th>ประสงค์สนับสนุน</th>
                                <th>เบอร์โทร</th>
                                <th>อีเมล</th>
                                <th>รายละเอียดเพิ่มเติม</th>
                                <th>วันที่ส่งข้อมูล</th>
                                <th style="width: 180px;">จัดการ</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($scholarships as $index => $item)
                                <tr>
                                    <td>
                                        <span class="number-badge">
                                            {{ $scholarships->firstItem() + $index }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="person-name">
                                            {{ $item->fullname }}
                                        </div>
                                    </td>

                                    <td>
                                        @forelse(($item->support_types ?? []) as $type)
                                            <span class="support-pill">
                                                {{ $type }}
                                            </span>
                                        @empty
                                            <span class="muted-text">-</span>
                                        @endforelse
                                    </td>

                                    <td>
                                        {{ $item->phone ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $item->email ?? '-' }}
                                    </td>

                                    <td>
                                        <div class="detail-text">
                                            {{ $item->detail ?? '-' }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ optional($item->created_at)->format('d/m/Y H:i') }}
                                    </td>

                                    <td>
                                        <div class="table-actions">
                                            @if(auth()->user()?->canCreateForm('dashboard_scholarship_sponsors'))
                                                <a href="{{ route('scholarship.donation.create', $item->id) }}"
                                                   class="scholarship-btn scholarship-btn-primary">
                                                    บันทึกการบริจาค
                                                </a>
                                            @endif

                                            <a href="{{ route('scholarship.donation.index', $item->id) }}"
                                               class="scholarship-btn scholarship-btn-soft">
                                                ดูประวัติ
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrap">
                    {{ $scholarships->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-box">
                    <div class="empty-icon">🎓</div>
                    <h3 class="empty-title">
                        ยังไม่มีข้อมูลผู้สนใจสนับสนุนทุนการศึกษาเด็ก
                    </h3>
                    <p class="empty-desc">
                        เมื่อมีผู้กรอกแบบฟอร์มจากหน้าบ้าน หรือมีข้อมูลตรงตามช่วงวันที่ที่เลือก รายการจะแสดงที่หน้านี้
                    </p>
                </div>
            @endif
        </div>

    </div>
</section>
@endsection