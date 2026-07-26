@extends('admin_client.admin_client')

@section('content')

@php
    use Carbon\Carbon;

    if (!function_exists('thaiDateAddictiveAllReport')) {
        function thaiDateAddictiveAllReport($date)
        {
            if (!$date) return '-';

            try {
                $d = $date instanceof \Carbon\Carbon ? $date : Carbon::parse($date);
                return $d->format('d/m/') . ($d->year + 543);
            } catch (\Exception $e) {
                return '-';
            }
        }
    }

    if (!function_exists('thaiDateTimeAddictiveAllReport')) {
        function thaiDateTimeAddictiveAllReport($date)
        {
            if (!$date) return '-';

            try {
                $d = $date instanceof \Carbon\Carbon ? $date : Carbon::parse($date);
                return $d->format('d/m/') . ($d->year + 543) . ' ' . $d->format('H:i') . ' น.';
            } catch (\Exception $e) {
                return '-';
            }
        }
    }

    $clientName = trim(($client->prefix ?? '') . ($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
    if ($clientName === '') {
        $clientName = $client->fullname ?? $client->full_name ?? '-';
    }

    $totalRows = $addictives->count();
@endphp

<style>
    .addictive-report-page{
        padding: 14px 0 24px;
    }

    .addictive-report-box{
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        padding: 20px;
    }

    .addictive-report-header{
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .addictive-report-title{
        margin: 0;
        font-size: 1.35rem;
        font-weight: 700;
        color: #111827;
        line-height: 1.4;
    }

    .addictive-report-subtitle{
        margin: 6px 0 0;
        font-size: .95rem;
        color: #6b7280;
        line-height: 1.6;
    }

    .addictive-report-actions{
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .addictive-report-actions .btn{
        border-radius: 10px;
        font-weight: 600;
        min-height: 42px;
        padding: .6rem 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        line-height: 1.2;
        white-space: nowrap;
    }

    .addictive-report-meta{
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin: 20px 0;
    }

    .addictive-report-meta-item{
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 14px;
        background: #fafafa;
    }

    .addictive-report-meta-label{
        font-size: .85rem;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .addictive-report-meta-value{
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
        line-height: 1.5;
        word-break: break-word;
    }

    .addictive-report-table-wrap{
        overflow-x: auto;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
    }

    .addictive-report-table{
        width: 100%;
        min-width: 1100px;
        border-collapse: collapse;
        background: #fff;
    }

    .addictive-report-table thead th{
        background: #f9fafb;
        color: #374151;
        font-size: .93rem;
        font-weight: 700;
        text-align: center;
        vertical-align: middle;
        padding: 14px 12px;
        border-bottom: 1px solid #e5e7eb;
        line-height: 1.45;
        white-space: nowrap;
    }

    .addictive-report-table tbody td{
        padding: 13px 12px;
        border-bottom: 1px solid #edf0f2;
        color: #111827;
        vertical-align: top;
        font-size: .94rem;
        line-height: 1.55;
    }

    .addictive-col-center{
        text-align: center;
        white-space: nowrap;
    }

    .addictive-col-text{
        text-align: left;
        word-break: break-word;
        min-width: 220px;
    }

    .addictive-report-empty{
        padding: 32px 16px;
        text-align: center;
        color: #6b7280;
        border: 1px dashed #d1d5db;
        border-radius: 12px;
        background: #fcfcfc;
    }

    .addictive-report-sign{
        margin-top: 28px;
        display: flex;
        justify-content: flex-end;
    }

    .addictive-report-sign-box{
        width: 280px;
        max-width: 100%;
        text-align: center;
        color: #374151;
    }

    .addictive-report-sign-line{
        margin-top: 56px;
        border-top: 1px solid #9ca3af;
        padding-top: 8px;
    }

   @page{
    size: A4 landscape;
    margin: 8mm 10mm;
}

@page{
    size: A4 landscape;
    margin: 10mm 14mm;
}

@media print{
    @page{
        size:A4 landscape;
        margin:6mm;
    }

    html,
    body{
        width:297mm !important;
        height:auto !important;
        min-height:auto !important;
        margin:0 !important;
        padding:0 !important;
        background:#fff !important;
        overflow:visible !important;
        font-size:12px !important;
        -webkit-print-color-adjust:exact !important;
        print-color-adjust:exact !important;
    }

    nav,
    header,
    footer,
    aside,
    .navbar,
    .navbar-custom,
    .sidebar,
    .leftside-menu,
    .main-footer,
    .footer,
    .app-footer,
    .content-footer,
    .page-footer,
    .page-title-box,
    .addictive-report-actions{
        display:none !important;
    }

    .content-wrapper,
    .main-content,
    .page-content,
    .container,
    .container-fluid{
        width:100% !important;
        max-width:100% !important;
        min-height:auto !important;
        height:auto !important;
        margin:0 !important;
        padding:0 !important;
        background:#fff !important;
        overflow:visible !important;
    }

    .addictive-report-page{
        width:100% !important;
        max-width:100% !important;
        min-height:auto !important;
        height:auto !important;
        margin:0 !important;
        padding:0 !important;
        background:#fff !important;
        overflow:visible !important;
    }

    .addictive-report-box{
        width:100% !important;
        max-width:100% !important;
        min-height:auto !important;
        height:auto !important;
        margin:0 !important;
        padding:0 !important;
        border:0 !important;
        border-radius:0 !important;
        box-shadow:none !important;
        background:#fff !important;
        overflow:visible !important;
        page-break-after:auto !important;
        break-after:auto !important;
    }

    .addictive-report-header{
        display:block !important;
        text-align:center !important;
        margin:0 0 7px !important;
        padding:0 0 6px !important;
        border-bottom:1px solid #d1d5db !important;
    }

    .addictive-report-title{
        margin:0 !important;
        font-size:20px !important;
        font-weight:900 !important;
        line-height:1.15 !important;
        color:#111827 !important;
    }

    .addictive-report-subtitle{
        margin:2px 0 0 !important;
        font-size:11px !important;
        line-height:1.2 !important;
        color:#64748b !important;
    }

    .addictive-report-meta{
        display:flex !important;
        flex-wrap:wrap !important;
        gap:4px 20px !important;
        margin:0 0 7px !important;
        padding:0 0 6px !important;
        border-bottom:1px solid #e5e7eb !important;
    }

    .addictive-report-meta-item{
        border:0 !important;
        background:none !important;
        box-shadow:none !important;
        padding:0 !important;
        margin:0 !important;
        min-width:auto !important;
        width:auto !important;
    }

    .addictive-report-meta-label{
        display:inline !important;
        font-size:12px !important;
        font-weight:800 !important;
        color:#111827 !important;
        margin:0 4px 0 0 !important;
    }

    .addictive-report-meta-value{
        display:inline !important;
        font-size:12px !important;
        font-weight:800 !important;
        color:#111827 !important;
        margin:0 !important;
    }

    .addictive-report-table-wrap{
        width:100% !important;
        overflow:visible !important;
        border:0 !important;
        border-radius:0 !important;
        margin:0 !important;
        padding:0 !important;
    }

    .addictive-report-table{
        width:100% !important;
        min-width:0 !important;
        max-width:100% !important;
        table-layout:fixed !important;
        border-collapse:collapse !important;
        margin:0 !important;
        page-break-inside:auto !important;
    }

    .addictive-report-table thead{
        display:table-header-group !important;
    }

    .addictive-report-table tr{
        page-break-inside:avoid !important;
        break-inside:avoid !important;
    }

    .addictive-report-table th,
    .addictive-report-table td{
        border:1px solid #111827 !important;
        padding:3px 4px !important;
        font-size:10.5px !important;
        line-height:1.15 !important;
        vertical-align:middle !important;
        text-align:center !important;
        white-space:normal !important;
        word-break:break-word !important;
        overflow-wrap:anywhere !important;
    }

    .addictive-report-table thead th{
        background:#f1f5f9 !important;
        font-weight:900 !important;
        color:#111827 !important;
    }

    .addictive-col-text{
        text-align:left !important;
    }

    .addictive-report-sign{
        margin-top:14px !important;
        display:flex !important;
        justify-content:flex-end !important;
        page-break-inside:avoid !important;
        break-inside:avoid !important;
    }

    .addictive-report-sign-box{
        width:240px !important;
        max-width:240px !important;
        text-align:center !important;
        font-size:11.5px !important;
        color:#111827 !important;
    }

    .addictive-report-sign-line{
        margin-top:24px !important;
        padding-top:4px !important;
        border-top:1px solid #111827 !important;
        font-size:11px !important;
        line-height:1.1 !important;
    }

    .addictive-report-empty{
        padding:14px !important;
        font-size:12px !important;
        border-radius:0 !important;
    }
}
</style>

<div class="container-fluid addictive-report-page">
    <div class="addictive-report-box">

        <div class="addictive-report-header">
            <div>
                <h1 class="addictive-report-title">รายงานการตรวจสารเสพติดทั้งหมด</h1>
                <p class="addictive-report-subtitle">
                    แสดงข้อมูลการตรวจสารเสพติดทั้งหมดของผู้รับบริการในรูปแบบที่เรียบง่าย อ่านง่าย
                    และเหมาะสำหรับตรวจสอบย้อนหลังหรือพิมพ์รายงาน
                </p>
            </div>

            <div class="addictive-report-actions">
                <a href="{{ route('addictive.create', $client->id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>กลับหน้ารายการ</span>
                </a>

                <button type="button" onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer"></i>
                    <span>พิมพ์รายงาน</span>
                </button>
            </div>
        </div>

        <div class="addictive-report-meta">
            <div class="addictive-report-meta-item">
                <div class="addictive-report-meta-label">ชื่อผู้รับบริการ</div>
                <div class="addictive-report-meta-value">{{ $clientName ?: '-' }}</div>
            </div>

            <div class="addictive-report-meta-item">
                <div class="addictive-report-meta-label">จำนวนรายการ</div>
                <div class="addictive-report-meta-value">{{ $totalRows }} รายการ</div>
            </div>

            <div class="addictive-report-meta-item">
                <div class="addictive-report-meta-label">วันที่พิมพ์รายงาน</div>
                <div class="addictive-report-meta-value">{{ thaiDateTimeAddictiveAllReport(now()) }}</div>
            </div>
        </div>

        @if($addictives->isNotEmpty())
            <div class="addictive-report-table-wrap">
                <table class="addictive-report-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">ครั้งที่</th>
                            <th style="width: 12%;">วันที่ตรวจ</th>
                            <th style="width: 16%;">ผลการตรวจ</th>
                            <th style="width: 16%;">การส่งต่อ</th>
                            <th style="width: 28%;">บันทึกผล</th>
                            <th style="width: 20%;">ผู้ตรวจ / ผู้บันทึก</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($addictives as $row)
                            <tr>
                                <td class="addictive-col-center">{{ $row->count ?? '-' }}</td>
                                <td class="addictive-col-center">{{ thaiDateAddictiveAllReport($row->date) }}</td>
                                <td class="addictive-col-center">
                                    @if((int) $row->exam === 0)
                                        ไม่พบสารเสพติด
                                    @else
                                        พบสารเสพติด
                                    @endif
                                </td>
                                <td class="addictive-col-center">
                                    @if((int) $row->exam === 1)
                                        @if((int) $row->refer === 1)
                                            ส่งต่อบำบัด
                                        @elseif((int) $row->refer === 2)
                                            ติดตามดูแลต่อเนื่อง
                                        @else
                                            -
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="addictive-col-text">{{ $row->record ?: '-' }}</td>
                                <td class="addictive-col-text">{{ $row->recorder ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="addictive-report-empty">
                <i class="bi bi-info-circle me-1"></i>
                ยังไม่มีข้อมูลการตรวจสารเสพติด
            </div>
        @endif

        <div class="addictive-report-sign">
            <div class="addictive-report-sign-box">
                <div>ผู้จัดทำรายงาน</div>
                <div class="addictive-report-sign-line">
                    (....................................................)
                </div>
            </div>
        </div>

    </div>
</div>
@endsection