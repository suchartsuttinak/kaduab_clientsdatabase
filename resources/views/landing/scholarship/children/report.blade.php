@extends('admin.admin_master')

@section('admin')

<style>
    @page {
        size: A4 landscape;
        margin: 7mm;
    }

    * {
        box-sizing: border-box;
    }

    .report-page {
        padding: 20px;
        background: #f4f6f9;
        color: #111827;
    }

    .report-card {
        max-width: 1600px;
        margin: 0 auto;
        padding: 20px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
    }

    .report-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding-bottom: 14px;
        margin-bottom: 14px;
        border-bottom: 2px solid #e5e7eb;
    }

    .report-title h4 {
        margin: 0;
        color: #111827;
        font-size: 1.3rem;
        font-weight: 800;
    }

    .report-title p {
        margin: 5px 0 0;
        color: #64748b;
        font-size: .92rem;
    }

    .report-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
    }

    .report-filter {
        display: grid;
        grid-template-columns:
            minmax(170px, .75fr)
            minmax(160px, .7fr)
            minmax(250px, 1.4fr)
            auto;
        gap: 12px;
        align-items: end;
        padding: 14px;
        margin-bottom: 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
    }

    .report-filter label {
        display: block;
        margin-bottom: 5px;
        color: #334155;
        font-size: .86rem;
        font-weight: 700;
    }

    .report-filter-actions {
        display: flex;
        gap: 8px;
    }

    .report-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 12px;
    }

    .summary-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-height: 36px;
        padding: 7px 12px;
        color: #334155;
        background: #ffffff;
        border: 1px solid #dbe3ee;
        border-radius: 999px;
        font-size: .87rem;
        font-weight: 700;
    }

    .summary-badge strong {
        color: #0f172a;
        font-weight: 800;
    }

    .report-table-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 10px 12px;
        background: #f8fafc;
        border: 1px solid #dfe6ee;
        border-bottom: none;
        border-radius: 12px 12px 0 0;
    }

    .report-table-heading-title {
        color: #0f172a;
        font-size: .92rem;
        font-weight: 800;
    }

    .report-grand-total {
        display: inline-flex;
        align-items: baseline;
        gap: 7px;
        color: #3730a3;
        font-size: .9rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .report-grand-total strong {
        color: #312e81;
        font-size: 1.08rem;
        font-weight: 900;
        font-variant-numeric: tabular-nums;
    }

    .report-table-wrap {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #dfe6ee;
        border-radius: 0 0 12px 12px;
    }

    .report-table {
        width: 100%;
        min-width: 1580px;
        margin: 0;
        table-layout: fixed;
        font-size: 12px;
    }

    .report-table thead th {
        padding: 9px 5px;
        color: #111827;
        background: #eef2f7;
        border-color: #cfd8e3;
        text-align: center;
        vertical-align: middle;
        white-space: normal;
        line-height: 1.3;
        font-weight: 800;
    }

    .report-table tbody td {
        padding: 8px 6px;
        border-color: #e1e7ef;
        vertical-align: top;
        word-break: break-word;
        line-height: 1.4;
    }

    .report-table tbody tr:nth-child(even) {
        background: #fbfcfe;
    }

    .report-table tbody tr:hover {
        background: #f8fafc;
    }

    .report-photo {
        display: block;
        width: 52px;
        height: 52px;
        margin: 0 auto;
        object-fit: cover;
        border: 1px solid #d1d5db;
        border-radius: 8px;
    }

    .report-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        margin: 0 auto;
        color: #4f46e5;
        background: #eef2ff;
        border-radius: 8px;
        font-size: 20px;
        font-weight: 800;
    }

    .report-name {
        color: #111827;
        font-weight: 800;
    }

    .report-address {
        margin-top: 3px;
        color: #6b7280;
        font-size: 11px;
        line-height: 1.35;
    }

    .report-center {
        text-align: center;
        vertical-align: middle !important;
    }

    .report-academic-period {
        text-align: center;
        vertical-align: middle !important;
    }

    .report-academic-year {
        color: #111827;
        font-weight: 800;
        white-space: nowrap;
    }

    .report-semester {
        margin-top: 2px;
        color: #64748b;
        font-size: 11px;
        white-space: nowrap;
    }

    .report-phone {
        text-align: center;
        vertical-align: middle !important;
        white-space: nowrap;
    }

    .report-amount {
        color: #0f172a;
        text-align: right;
        vertical-align: middle !important;
        white-space: nowrap;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
    }

    .report-no-amount {
        display: block;
        min-height: 18px;
    }

    .empty-box {
        padding: 38px 20px;
        color: #64748b;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        text-align: center;
    }

    @media (max-width: 1199.98px) {
        .report-filter {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .report-filter-actions {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 767.98px) {
        .report-page {
            padding: 10px;
        }

        .report-card {
            padding: 14px;
            border-radius: 12px;
        }

        .report-header {
            flex-direction: column;
        }

        .report-actions,
        .report-actions .btn {
            width: 100%;
        }

        .report-filter {
            grid-template-columns: 1fr;
        }

        .report-filter-actions {
            grid-column: auto;
            flex-direction: column;
        }

        .report-filter-actions .btn {
            width: 100%;
        }

        .report-table-heading {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    @media print {
        html,
        body {
            width: 297mm;
            min-height: 210mm;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print,
        .navbar,
        .sidebar,
        .footer,
        .topbar,
        .page-title,
        .breadcrumb,
        header,
        nav,
        aside {
            display: none !important;
        }

        .content-page,
        .container,
        .container-fluid {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .report-page {
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
        }

        .report-card {
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        .report-header {
            display: block !important;
            padding-bottom: 5px !important;
            margin-bottom: 4px !important;
            border-bottom: 1px solid #475569 !important;
            page-break-after: avoid;
        }

        .report-title {
            width: 100%;
            text-align: center;
        }

        .report-title h4 {
            margin: 0 !important;
            font-size: 17px !important;
        }

        .report-title p {
            margin-top: 2px !important;
            color: #111827 !important;
            font-size: 9.5px !important;
        }

        .report-summary {
            justify-content: center;
            gap: 0;
            margin-bottom: 4px !important;
            page-break-after: avoid;
        }

        .summary-badge {
            min-height: 0 !important;
            padding: 2px 7px !important;
            color: #111827 !important;
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            font-size: 8.5px !important;
        }

        .summary-badge + .summary-badge {
            border-left: 1px solid #94a3b8 !important;
        }

        .report-table-heading {
            padding: 4px 6px !important;
            background: #ffffff !important;
            border: 1px solid #475569 !important;
            border-bottom: none !important;
            border-radius: 0 !important;
            font-size: 8.5px !important;
            page-break-after: avoid;
        }

        .report-table-heading-title,
        .report-grand-total,
        .report-grand-total strong {
            color: #111827 !important;
        }

        .report-grand-total strong {
            font-size: 9.5px !important;
        }

        .report-table-wrap {
            overflow: visible !important;
            border: none !important;
            border-radius: 0 !important;
        }

        .report-table {
            width: 100% !important;
            min-width: 0 !important;
            margin: 0 !important;
            table-layout: fixed !important;
            font-size: 7.4px !important;
        }

        .report-table thead {
            display: table-header-group;
        }

        .report-table th,
        .report-table td {
            padding: 2.5px 3px !important;
            border: 1px solid #475569 !important;
            line-height: 1.15 !important;
        }

        .report-table thead th {
            color: #111827 !important;
            background: #f1f5f9 !important;
        }

        .report-photo,
        .report-avatar {
            width: 31px !important;
            height: 31px !important;
            border-radius: 3px !important;
        }

        .report-avatar {
            font-size: 12px !important;
        }

        .report-address {
            margin-top: 1px !important;
            color: #374151 !important;
            font-size: 6.3px !important;
            line-height: 1.1 !important;
        }

        .report-semester {
            margin-top: 1px !important;
            color: #374151 !important;
            font-size: 6.5px !important;
            line-height: 1.1 !important;
        }

        tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    }
</style>

@php
    $selectedAcademicYear = $academicYear ?? request('academic_year');
    $selectedSemester = $semester ?? request('semester');
    $keyword = $keyword ?? request('keyword');

    $reportYears = isset($years)
        ? collect($years)
        : $children->pluck('academic_year')
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

    /*
    |--------------------------------------------------------------------------
    | คำนวณจำนวนเงินช่วยเหลือของแต่ละคำขอ
    |--------------------------------------------------------------------------
    | ใช้ semester_amount ก่อน หาก Controller เตรียมมาแล้ว
    | หากไม่มี จะรวมยอดจาก scholarship_expenses ของปี/ภาคเรียนนั้น
    */
    $reportChildren = $children->map(function ($child) {
        $amount = $child->getAttribute('semester_amount');

        if ($amount === null) {
            $expenses = $child->expenses ?? collect();

            $matchingExpenses = $expenses->filter(function ($expense) use ($child) {
                return (string) $expense->academic_year === (string) $child->academic_year
                    && (string) $expense->semester === (string) $child->semester;
            });

            $amount = $matchingExpenses->isNotEmpty()
                ? $matchingExpenses->sum('total_amount')
                : $expenses->sum('total_amount');
        }

        $child->setAttribute('report_assistance_amount', (float) $amount);

        return $child;
    });

    $totalAssistance = isset($grandTotal)
        ? (float) $grandTotal
        : (float) $reportChildren->sum('report_assistance_amount');

    $periodText = $selectedAcademicYear
        ? 'ปีการศึกษา ' . $selectedAcademicYear
        : 'ทุกปีการศึกษา';

    $periodText .= $selectedSemester
        ? ' ภาคเรียนที่ ' . $selectedSemester
        : ' ทุกภาคเรียน';
@endphp

<div class="report-page">
    <div class="report-card">

        <div class="report-header">
            <div class="report-title">
                <h4>รายงานผู้ขอรับทุนการศึกษา</h4>
                <p>
                    {{ $periodText }}

                    @if(!empty($keyword))
                        | คำค้นหา “{{ $keyword }}”
                    @endif
                </p>
            </div>

            <div class="report-actions no-print">
                <a href="{{ route('scholarship.children.index', [
                        'academic_year' => $selectedAcademicYear,
                        'semester' => $selectedSemester,
                        'keyword' => $keyword,
                    ]) }}"
                   class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                    กลับหน้าหลัก
                </a>

                <button type="button"
                        class="btn btn-dark"
                        onclick="window.print()">
                    <i class="bi bi-printer"></i>
                    พิมพ์รายงาน
                </button>
            </div>
        </div>

        <form method="GET"
              action="{{ route('scholarship.children.report') }}"
              class="report-filter no-print">

            <div>
                <label for="report_academic_year">ปีการศึกษา</label>
                <select name="academic_year"
                        id="report_academic_year"
                        class="form-select">
                    <option value="">ทุกปีการศึกษา</option>

                    @foreach($reportYears as $year)
                        <option value="{{ $year }}"
                            @selected((string) $selectedAcademicYear === (string) $year)>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="report_semester">ภาคเรียน</label>
                <select name="semester"
                        id="report_semester"
                        class="form-select">
                    <option value="">ทุกภาคเรียน</option>
                    <option value="1" @selected((string) $selectedSemester === '1')>
                        ภาคเรียนที่ 1
                    </option>
                    <option value="2" @selected((string) $selectedSemester === '2')>
                        ภาคเรียนที่ 2
                    </option>
                </select>
            </div>

            <div>
                <label for="report_keyword">ค้นหาชื่อ สถานศึกษา หรือโทรศัพท์</label>
                <input type="text"
                       name="keyword"
                       id="report_keyword"
                       class="form-control"
                       value="{{ $keyword }}"
                       placeholder="กรอกคำค้นหา">
            </div>

            <div class="report-filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i>
                    ค้นหา
                </button>

                <a href="{{ route('scholarship.children.report') }}"
                   class="btn btn-outline-secondary">
                    ล้างตัวกรอง
                </a>
            </div>
        </form>

        <div class="report-summary">
            <div class="summary-badge">
                ผู้ขอรับทุน
                <strong>{{ number_format($reportChildren->count()) }}</strong>
                ราย
            </div>

            <div class="summary-badge">
                {{ $selectedAcademicYear
                    ? 'ปีการศึกษา ' . $selectedAcademicYear
                    : 'ทุกปีการศึกษา' }}
            </div>

            <div class="summary-badge">
                {{ $selectedSemester
                    ? 'ภาคเรียนที่ ' . $selectedSemester
                    : 'ทุกภาคเรียน' }}
            </div>
        </div>

        @if($reportChildren->isNotEmpty())
            <div class="report-table-heading">
                <div class="report-table-heading-title">
                    รายละเอียดผู้ขอรับทุนการศึกษา
                </div>

                @if($totalAssistance > 0)
                    <div class="report-grand-total">
                        ยอดเงินช่วยเหลือรวม
                        <strong>{{ number_format($totalAssistance, 2) }}</strong>
                        บาท
                    </div>
                @endif
            </div>

            <div class="report-table-wrap">
                <table class="table table-bordered align-middle report-table">

                    <colgroup>
                        <col style="width: 5%;">
                        <col style="width: 13%;">
                        <col style="width: 3.5%;">
                        <col style="width: 8%;">
                        <col style="width: 12.5%;">
                        <col style="width: 9%;">
                        <col style="width: 10%;">
                        <col style="width: 8%;">
                        <col style="width: 12%;">
                        <col style="width: 12%;">
                        <col style="width: 7%;">
                    </colgroup>

                    <thead>
                        <tr>
                            <th>ภาพ</th>
                            <th>ชื่อ - นามสกุล</th>
                            <th>อายุ</th>
                            <th>ระดับการศึกษา</th>
                            <th>สถานศึกษา</th>
                            <th>ปีการศึกษา / ภาคเรียน</th>
                            <th>ผู้ปกครอง</th>
                            <th>โทรศัพท์</th>
                            <th>สาเหตุที่ขอรับทุน</th>
                            <th>ความต้องการช่วยเหลือ</th>
                            <th>จำนวนเงิน</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($reportChildren as $child)
                            @php
                                $photoUrl = null;

                                if (!empty($child->photo)) {
                                    $photoUrl = route('scholarship.children.photo', $child);
                                }

                                $rowAmount = (float) $child->report_assistance_amount;
                            @endphp

                            <tr>
                                <td class="report-center">
                                    @if($photoUrl)
                                        <img src="{{ $photoUrl }}"
                                             loading="eager"
                                             decoding="async"
                                             class="report-photo"
                                             alt="{{ $child->first_name }}">
                                    @else
                                        <div class="report-avatar">
                                            {{ mb_substr($child->first_name ?? '-', 0, 1) }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="report-name">
                                        {{ $child->first_name }}
                                        {{ $child->last_name }}
                                    </div>

                                    @if($child->current_address)
                                        <div class="report-address">
                                            {{ $child->current_address }}
                                        </div>
                                    @endif
                                </td>

                                <td class="report-center">
                                    {{ $child->age ?? '-' }}
                                </td>

                                <td>
                                    {{ $child->education_level ?? '-' }}
                                </td>

                                <td>
                                    {{ $child->school_name ?? '-' }}
                                </td>

                                <td class="report-academic-period">
                                    <div class="report-academic-year">
                                        {{ $child->academic_year ?? '-' }}
                                    </div>

                                    @if(!empty($child->semester))
                                        <div class="report-semester">
                                            ภาคเรียนที่ {{ $child->semester }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    {{ $child->guardian_name ?? '-' }}
                                </td>

                                <td class="report-phone">
                                    {{ $child->phone ?? '-' }}
                                </td>

                                <td>
                                    {{ $child->reason ?? '-' }}
                                </td>

                                <td>
                                    {{ $child->help_needed ?? '-' }}
                                </td>

                                <td class="report-amount">
                                    @if($rowAmount > 0)
                                        {{ number_format($rowAmount, 2) }}
                                    @else
                                        <span class="report-no-amount"
                                              aria-label="ยังไม่มีการช่วยเหลือ"></span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-box">
                ไม่พบข้อมูลตามปีการศึกษาและภาคเรียนที่เลือก
            </div>
        @endif

    </div>
</div>

@endsection