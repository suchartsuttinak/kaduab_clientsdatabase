@extends('admin_client.admin_client')

@section('content')

@php
    use Carbon\Carbon;

    if (!function_exists('thaiDateHelpingReport')) {
        function thaiDateHelpingReport($date) {
            if (!$date) return '-';

            try {
                $d = Carbon::parse($date);
                $months = [
                    1 => 'ม.ค.',
                    2 => 'ก.พ.',
                    3 => 'มี.ค.',
                    4 => 'เม.ย.',
                    5 => 'พ.ค.',
                    6 => 'มิ.ย.',
                    7 => 'ก.ค.',
                    8 => 'ส.ค.',
                    9 => 'ก.ย.',
                    10 => 'ต.ค.',
                    11 => 'พ.ย.',
                    12 => 'ธ.ค.',
                ];

                return $d->day . ' ' . $months[(int) $d->month] . ' ' . ($d->year + 543);
            } catch (\Exception $e) {
                return $date;
            }
        }
    }
@endphp

<style>
    .help-report-page{
        padding: 12px 0 24px;
    }

    .help-report-box{
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 24px;
    }

    .help-report-header{
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .help-report-title{
        margin: 0;
        font-size: 1.35rem;
        font-weight: 700;
        color: #111827;
        line-height: 1.4;
    }

    .help-report-client-name{
        margin: 5px 0 0;
        font-size: .96rem;
        color: #475569;
        line-height: 1.55;
        font-weight: 400;
    }

    .help-report-actions{
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .help-report-actions .btn{
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

    .help-report-table-wrap{
        overflow-x: auto;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
    }

    .help-report-table{
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
        background: #fff;
        table-layout: fixed;
    }

    .help-report-table thead th{
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

    .help-report-table tbody td,
    .help-report-table tfoot th{
        padding: 13px 12px;
        border-bottom: 1px solid #edf0f2;
        color: #111827;
        vertical-align: middle;
        font-size: .94rem;
        line-height: 1.55;
    }

    .help-col-date{
        text-align: center;
        white-space: nowrap;
    }

    .help-col-item{
        text-align: left;
        word-break: break-word;
    }

    .help-col-qty{
        text-align: center;
        white-space: nowrap;
    }

    .help-col-unit,
    .help-col-total{
        text-align: right;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    .help-report-table thead th.help-col-qty {
        text-align: center;
    }

    .help-report-table thead th.help-col-unit,
    .help-report-table thead th.help-col-total {
        text-align: right;
    }

    .help-report-sign{
        margin-top: 28px;
        display: flex;
        justify-content: flex-end;
    }

    .help-report-sign-box{
        width: 280px;
        max-width: 100%;
        text-align: center;
        color: #374151;
    }

    .help-report-sign-line{
        margin-top: 56px;
        border-top: 1px solid #9ca3af;
        padding-top: 8px;
    }

   @page{
    size:A4 landscape;
    margin:10mm 14mm;
}

@media print{

    html,
    body{
        width:297mm !important;
        min-height:210mm !important;
        margin:0 !important;
        padding:0 !important;
        background:#fff !important;
        font-family:"TH Sarabun New","Sarabun",sans-serif !important;
        overflow:visible !important;
        -webkit-print-color-adjust:exact !important;
        print-color-adjust:exact !important;
    }

    .navbar-custom,
    .leftside-menu,
    .page-title-box,
    .footer,
    .help-report-actions,
    header,
    footer{
        display:none !important;
    }

    .help-report-page{
        width:100% !important;
        max-width:100% !important;
        margin:0 auto !important;
        padding:0 !important;
        background:#fff !important;
        overflow:visible !important;
    }

    .help-report-box{
        width:100% !important;
        max-width:100% !important;
        min-height:auto !important;
        margin:0 auto !important;
        padding:0 !important;
        border:none !important;
        border-radius:0 !important;
        box-shadow:none !important;
        background:#fff !important;
        overflow:visible !important;
        page-break-after:avoid !important;
        break-after:avoid !important;
    }

    .help-report-header{
        text-align:center !important;
        border-bottom:1px solid #cbd5e1 !important;
        padding:0 0 5px !important;
        margin:0 0 6px !important;
    }

    .help-report-title{
        font-size:20px !important;
        font-weight:900 !important;
        line-height:1.1 !important;
        color:#0f172a !important;
        margin:0 !important;
    }

    .help-report-client-name{
        font-size:11.5px !important;
        color:#475569 !important;
        margin-top:2px !important;
        line-height:1.15 !important;
        font-weight:400 !important;
    }

    .help-report-table-wrap{
        width:100% !important;
        max-width:100% !important;
        overflow:visible !important;
        border:none !important;
        border-radius:0 !important;
        margin:0 !important;
        padding:0 !important;
    }

    .help-report-table{
        width:100% !important;
        min-width:0 !important;
        max-width:100% !important;
        margin:0 !important;
        table-layout:fixed !important;
        border-collapse:collapse !important;
        border-spacing:0 !important;
        background:#fff !important;
        page-break-inside:auto !important;
    }

    .help-report-table thead{
        display:table-header-group !important;
    }

    .help-report-table tr{
        page-break-inside:avoid !important;
        break-inside:avoid !important;
    }

    .help-report-table thead th{
        background:#eef4ff !important;
        color:#0f172a !important;
        border:1px solid #111827 !important;
        text-align:center !important;
        vertical-align:middle !important;
        padding:3px 3px !important;
        font-size:10px !important;
        font-weight:900 !important;
        line-height:1.08 !important;
        white-space:normal !important;
    }

    .help-report-table tbody td,
    .help-report-table tfoot td{
        border:1px solid #111827 !important;
        padding:3px 3px !important;
        font-size:9.8px !important;
        font-weight:600 !important;
        color:#111827 !important;
        line-height:1.08 !important;
        vertical-align:middle !important;
        white-space:normal !important;
        word-break:break-word !important;
        overflow-wrap:anywhere !important;
        text-align:center !important;
    }

    .help-report-table tbody tr:nth-child(even){
        background:#fcfdff !important;
    }

    .help-report-table tfoot td{
        background:#f8fafc !important;
        font-weight:900 !important;
    }

    .help-report-empty{
        border:1px dashed #94a3b8 !important;
        border-radius:0 !important;
        padding:14px !important;
        text-align:center !important;
        color:#475569 !important;
        font-size:12px !important;
    }
}
</style>

<div class="container-fluid help-report-page">
    <div class="help-report-box">
        <div class="help-report-header">
            <div>
                <h1 class="help-report-title">รายงานการช่วยเหลือผู้รับบริการ</h1>
                <p class="help-report-client-name">
                    ผู้รับบริการ: {{ $client->fullname ?? $client->full_name ?? '-' }}
                </p>
            </div>

            <div class="help-report-actions">
                <a href="{{ route('help_sessions.show', $client->id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>กลับหน้ารายการ</span>
                </a>

                <button type="button" onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer"></i>
                    <span>พิมพ์รายงาน</span>
                </button>
            </div>
        </div>

        <div class="help-report-table-wrap">
            <table class="help-report-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">วันที่</th>
                        <th style="width: 32%;">รายการ</th>
                        <th class="help-col-qty" style="width: 12%;">จำนวน</th>
                        <th class="help-col-unit" style="width: 18%;">ราคา/หน่วย (บาท)</th>
                        <th class="help-col-total" style="width: 18%;">ราคารวม (บาท)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($session->items as $item)
                        @php
                            $rowTotal = (float) $item->quantity * (float) $item->unit_price;
                        @endphp
                        <tr>
                            <td class="help-col-date">{{ thaiDateHelpingReport($session->help_date) }}</td>
                            <td class="help-col-item">{{ $item->item_name ?? '-' }}</td>
                            <td class="help-col-qty">{{ number_format($item->quantity ?? 0) }}</td>
                            <td class="help-col-unit">{{ number_format($item->unit_price ?? 0, 2) }}</td>
                            <td class="help-col-total">{{ number_format($rowTotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">ยังไม่พบข้อมูลการช่วยเหลือ</td>
                        </tr>
                    @endforelse
                </tbody>

                @if($session->items->count())
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">รวมทั้งสิ้น</th>
                            <th class="help-col-total">{{ number_format($grandTotal, 2) }} บาท</th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <div class="help-report-sign">
            <div class="help-report-sign-box">
                <div>ผู้จัดทำรายงาน</div>
                <div class="help-report-sign-line">
                    (.............................................)
                </div>
            </div>
        </div>
    </div>
</div>
@endsection