<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>รายงานการช่วยเหลือและติดตามผล</title>
    <style>
        @page { size: A4 portrait; margin: 14mm 12mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #111827;
            font-family: "THSarabunNew", "DejaVu Sans", sans-serif;
            font-size: 14px;
            line-height: 1.45;
        }
        h1 { margin: 0 0 6px; text-align: center; font-size: 20px; }
        .sub { text-align: center; margin-bottom: 14px; color: #374151; }
        .info { width: 100%; margin-bottom: 12px; border-collapse: collapse; }
        .info td { padding: 3px 5px; vertical-align: top; }
        .label { font-weight: bold; white-space: nowrap; width: 85px; }
        table.report { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .report th, .report td { border: 1px solid #9ca3af; padding: 6px; vertical-align: top; }
        .report th { background: #f3f4f6; text-align: center; font-weight: bold; }
        .col-no { width: 7%; text-align: center; }
        .col-date { width: 18%; text-align: center; }
        .col-note { width: 25%; }
        .wrap { white-space: pre-wrap; overflow-wrap: anywhere; }
        .empty { padding: 28px 8px !important; text-align: center; color: #6b7280; }
        .footer { margin-top: 10px; text-align: right; font-size: 11px; color: #6b7280; }
    </style>
</head>
<body>
@php
    $thaiMonths = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
        5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
        9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม',
    ];

    $formatThaiDate = static function ($value) use ($thaiMonths) {
        if (!$value) return '-';
        $date = \Carbon\Carbon::parse($value);
        return $date->day . ' ' . $thaiMonths[$date->month] . ' ' . ($date->year + 543);
    };

    $clientName = $client->fullname
        ?? $client->full_name
        ?? trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''))
        ?: '-';

    $rangeText = 'ทั้งหมด';
    if (!empty($dateFrom) || !empty($dateTo)) {
        $rangeText = (!empty($dateFrom) ? $formatThaiDate($dateFrom) : 'ไม่กำหนด')
            . ' ถึง '
            . (!empty($dateTo) ? $formatThaiDate($dateTo) : 'ไม่กำหนด');
    }
@endphp

<h1>รายงานการช่วยเหลือและติดตามผล</h1>
<div class="sub">ข้อมูลผู้รับบริการและรายการติดตามผลตามช่วงวันที่ที่กำหนด</div>

<table class="info">
    <tr>
        <td class="label">ผู้รับบริการ:</td>
        <td>{{ $clientName }}</td>
        <td class="label">เลขทะเบียน:</td>
        <td>{{ $client->register_number ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">ช่วงวันที่:</td>
        <td colspan="3">{{ $rangeText }}</td>
    </tr>
</table>

<table class="report">
    <thead>
        <tr>
            <th class="col-no">ลำดับ</th>
            <th class="col-date">วันที่ติดตาม</th>
            <th>การช่วยเหลือและติดตามผล</th>
            <th class="col-note">หมายเหตุ</th>
        </tr>
    </thead>
    <tbody>
        @forelse($followups as $followup)
            <tr>
                <td class="col-no">{{ $loop->iteration }}</td>
                <td class="col-date">{{ $formatThaiDate($followup->followup_date) }}</td>
                <td class="wrap">{{ $followup->assistance_detail ?: '-' }}</td>
                <td class="wrap">{{ $followup->note ?: '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="empty">ไม่พบข้อมูลการติดตามผลตามเงื่อนไขที่เลือก</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    จัดทำเมื่อ {{ now('Asia/Bangkok')->format('d/m/') }}{{ now('Asia/Bangkok')->year + 543 }} {{ now('Asia/Bangkok')->format('H:i') }} น.
</div>
</body>
</html>
