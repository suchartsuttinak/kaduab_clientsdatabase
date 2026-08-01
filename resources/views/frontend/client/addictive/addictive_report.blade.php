@extends('admin_client.admin_client')

@section('content')

@php
    use Carbon\Carbon;

    if (!function_exists('thaiDateAddictiveReport')) {
        function thaiDateAddictiveReport($date)
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

    if (!function_exists('thaiDateTimeAddictiveReport')) {
        function thaiDateTimeAddictiveReport($date)
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

    $clientName = trim(($client->prefix ?? '') . ' ' . ($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
    if ($clientName === '') {
        $clientName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
    }

    $examText = ((int) $addictive->exam === 0) ? 'ไม่พบสารเสพติด' : 'พบสารเสพติด';

    $referText = '-';
    if ((int) $addictive->exam === 1) {
        if ((int) $addictive->refer === 1) {
            $referText = 'ส่งต่อบำบัด';
        } elseif ((int) $addictive->refer === 2) {
            $referText = 'ติดตามดูแลต่อเนื่อง';
        }
    }
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
        grid-template-columns: repeat(4, minmax(0, 1fr));
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
        min-width: 840px;
        border-collapse: collapse;
        background: #fff;
        table-layout: fixed;
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
    }

    .addictive-report-empty{
        padding: 32px 16px;
        text-align: center;
        color: #6b7280;
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

@media print{
    html,
    body{
        width: 297mm !important;
        height: 210mm !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        font-family: "TH Sarabun New","Sarabun",sans-serif !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .navbar-custom,
    .leftside-menu,
    .page-title-box,
    .footer,
    .addictive-report-actions,
    header,
    footer{
        display: none !important;
    }

    .addictive-report-page{
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
        background: #fff !important;
    }

    .addictive-report-box{
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        overflow: visible !important;
        background: #fff !important;
    }

    .addictive-report-header{
        text-align: center !important;
        margin: 0 0 8px !important;
        padding: 0 0 7px !important;
        border-bottom: 1px solid #cbd5e1 !important;
    }

    .addictive-report-title{
        margin: 0 !important;
        font-size: 22px !important;
        font-weight: 900 !important;
        line-height: 1.12 !important;
        color: #0f172a !important;
    }

    .addictive-report-subtitle{
        margin-top: 2px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        color: #64748b !important;
        line-height: 1.15 !important;
    }

    .addictive-report-meta{
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        flex-wrap: wrap !important;
        gap: 6px 24px !important;
        margin: 0 0 8px !important;
        padding: 0 0 6px 6px !important;
        border-bottom: 1px solid #dbe4f0 !important;
    }

    .addictive-report-meta > *{
        border: none !important;
        background: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        min-width: auto !important;
        width: auto !important;
        flex: none !important;
        color: #2563eb !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        line-height: 1.15 !important;
    }

    .addictive-report-table-wrap{
        width: 100% !important;
        overflow: visible !important;
        border: none !important;
        border-radius: 0 !important;
    }

    .addictive-report-table{
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        margin: 0 !important;
        table-layout: fixed !important;
        border-collapse: collapse !important;
        border-spacing: 0 !important;
        background: #fff !important;
    }

    .addictive-report-table thead th{
        background: #eef4ff !important;
        color: #0f172a !important;
        border: 1px solid #111827 !important;
        text-align: center !important;
        vertical-align: middle !important;
        padding: 5px 4px !important;
        font-size: 11px !important;
        font-weight: 900 !important;
        line-height: 1.12 !important;
        white-space: normal !important;
    }

    .addictive-report-table tbody td{
        border: 1px solid #111827 !important;
        padding: 4px 4px !important;
        font-size: 10.8px !important;
        font-weight: 600 !important;
        color: #111827 !important;
        line-height: 1.16 !important;
        vertical-align: middle !important;
        white-space: normal !important;
        word-break: break-word !important;
        overflow-wrap: anywhere !important;
        text-align: center !important;
    }

    .addictive-report-table tbody tr:nth-child(even){
        background: #fcfdff !important;
    }
}
</style>

<div class="container-fluid addictive-report-page">
    <div class="addictive-report-box">
        <div class="addictive-report-header">
            <div>
                <h1 class="addictive-report-title">รายงานการตรวจสารเสพติด</h1>
                <p class="addictive-report-subtitle">
                    แสดงรายละเอียดผลการตรวจสารเสพติดของผู้รับบริการอย่างเป็นระเบียบ
                    อ่านง่าย รองรับการพิมพ์และการใช้งานทุกขนาดหน้าจอ
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
                <div class="addictive-report-meta-label">ครั้งที่</div>
                <div class="addictive-report-meta-value">{{ $addictive->count ?? '-' }}</div>
            </div>

            <div class="addictive-report-meta-item">
                <div class="addictive-report-meta-label">วันที่ตรวจ</div>
                <div class="addictive-report-meta-value">{{ thaiDateAddictiveReport($addictive->date) }}</div>
            </div>

            <div class="addictive-report-meta-item">
                <div class="addictive-report-meta-label">ผู้บันทึก</div>
                <div class="addictive-report-meta-value">{{ $addictive->recorder ?? '-' }}</div>
            </div>
        </div>

        <div class="addictive-report-table-wrap">
            <table class="addictive-report-table">
                <thead>
                    <tr>
                        <th style="width: 14%;">ครั้งที่</th>
                        <th style="width: 16%;">วันที่ตรวจ</th>
                        <th style="width: 18%;">ผลการตรวจ</th>
                        <th style="width: 18%;">การส่งต่อ</th>
                        <th style="width: 34%;">บันทึกผล / รายละเอียด</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="addictive-col-center">{{ $addictive->count ?? '-' }}</td>
                        <td class="addictive-col-center">{{ thaiDateAddictiveReport($addictive->date) }}</td>
                        <td class="addictive-col-center">{{ $examText }}</td>
                        <td class="addictive-col-center">{{ $referText }}</td>
                        <td class="addictive-col-text">{{ $addictive->record ?: '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="addictive-report-sign">
            <div class="addictive-report-sign-box">
                <div>ผู้จัดทำรายงาน</div>
                <div class="addictive-report-sign-line">
                    ({{ $addictive->recorder ?? '................................' }})
                </div>
                <div class="mt-2 small text-muted">
                    วันที่พิมพ์ {{ thaiDateTimeAddictiveReport(now()) }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection