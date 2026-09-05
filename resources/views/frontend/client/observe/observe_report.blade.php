@extends('admin_client.admin_client')

@section('content')

@php

    $thaiDate = function ($value) {

        if (!$value) {

            return '-';

        }

        try {

            $date = $value instanceof \Carbon\Carbon

                ? $value

                : \Carbon\Carbon::parse($value);

            return $date->format('d/m/') . ($date->year + 543);

        } catch (\Throwable $e) {

            return '-';

        }

    };

    $followCount = $observe->followups->count();

    $clientFullName = trim(

        ($client->prefix ?? '')

        . ($client->first_name ?? '')

        . ' '

        . ($client->last_name ?? '')

    );

    if ($clientFullName === '') {

        $clientFullName = $client->fullname ?? $client->name ?? '-';

    }

@endphp

<div class="observe-report-page" id="observeReport">

    <div class="observe-report-toolbar no-print">

        <div>

            <h1>รายงานพฤติกรรมและการติดตามผล</h1>

            <p>ตรวจสอบข้อมูลก่อนสั่งพิมพ์ รายงานจัดหน้าเป็นกระดาษ A4 แนวนอน</p>

        </div>

        <div class="observe-report-actions">

            <button type="button" class="btn btn-primary" onclick="window.print()">

                <i class="bi bi-printer"></i> พิมพ์รายงาน

            </button>

            <a href="{{ route('observe.create', $client->id) }}" class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left"></i> กลับหน้ารายการ

            </a>

        </div>

    </div>

    <article class="observe-report-paper">

       <header class="observe-report-document-header">
    <h2>รายงานพฤติกรรมและการติดตามผล</h2>

    @php
        $displayPrefix = trim((string) ($client->prefix ?? ''));

        try {
            $age = !empty($client->birth_date)
                ? \Carbon\Carbon::parse($client->birth_date)->age
                : null;
        } catch (\Throwable $e) {
            $age = null;
        }

        $gender = strtolower(trim((string) ($client->gender ?? '')));

        $isMale = in_array($gender, [
            'ชาย', 'male', 'm', '1'
        ], true);

        $isFemale = in_array($gender, [
            'หญิง', 'female', 'f', '2'
        ], true);

        if ($age !== null) {
            if ($age < 15) {
                if ($isMale) {
                    $displayPrefix = 'เด็กชาย';
                } elseif ($isFemale) {
                    $displayPrefix = 'เด็กหญิง';
                }
            } else {
                if ($isMale) {
                    $displayPrefix = 'นาย';
                } elseif ($isFemale) {
                    $displayPrefix = 'นางสาว';
                }
            }
        }

        $displayFullName = trim(
            $displayPrefix . ' ' .
            ($client->first_name ?? '') . ' ' .
            ($client->last_name ?? '')
        );

        if ($displayFullName === '') {
            $displayFullName = $clientFullName ?? '-';
        }
    @endphp

    <div class="observe-report-client-name">
        <span class="client-label">ชื่อ–สกุล</span>
        <span class="client-name">{{ $displayFullName }}</span>
    </div>
</header>

        <section class="observe-report-summary" aria-label="สรุปข้อมูล">

            <div class="observe-report-summary-item">

                <span>วันที่เกิดเหตุ</span>

                <strong>{{ $thaiDate($observe->date) }}</strong>

            </div>

            <div class="observe-report-summary-item">

                <span>สภาพปัญหา</span>

                <strong>{{ $observe->misbehavior->misbehavior_name ?? '-' }}</strong>

            </div>

            <div class="observe-report-summary-item">

                <span>วันที่บันทึก</span>

                <strong>{{ $thaiDate($observe->record_date) }}</strong>

            </div>

            <div class="observe-report-summary-item">

                <span>จำนวนการติดตาม</span>

                <strong>{{ $followCount }} ครั้ง</strong>

            </div>

        </section>

        <section class="observe-report-section">

            <h3>1. ข้อมูลเหตุการณ์</h3>

            <table class="observe-report-detail-table">

                <colgroup>

                    <col class="label-col">

                    <col>

                    <col class="label-col">

                    <col>

                </colgroup>

                <tbody>

                    <tr>

                        <th>ผู้รับบริการ</th>

                        <td>{{ $clientFullName }}</td>

                        <th>ผู้บันทึก</th>

                        <td>{{ $observe->recorder ?: '-' }}</td>

                    </tr>

                    <tr>

                        <th>พฤติกรรมที่พบเห็น</th>

                        <td colspan="3">{!! nl2br(e($observe->behavior ?: '-')) !!}</td>

                    </tr>

                    <tr>

                        <th>สาเหตุ</th>

                        <td colspan="3">{!! nl2br(e($observe->cause ?: '-')) !!}</td>

                    </tr>

                </tbody>

            </table>

        </section>

        <section class="observe-report-section">

            <h3>2. การดำเนินการและผลลัพธ์</h3>

            <table class="observe-report-detail-table">

                <colgroup>

                    <col class="label-col">

                    <col>

                </colgroup>

                <tbody>

                    <tr>

                        <th>แนวทางแก้ไข</th>

                        <td>{!! nl2br(e($observe->solution ?: '-')) !!}</td>

                    </tr>

                    <tr>

                        <th>การดำเนินการ</th>

                        <td>{!! nl2br(e($observe->action ?: '-')) !!}</td>

                    </tr>

                    <tr>

                        <th>ปัญหา / อุปสรรค</th>

                        <td>{!! nl2br(e($observe->obstacles ?: '-')) !!}</td>

                    </tr>

                    <tr>

                        <th>ผลการดำเนินการ</th>

                        <td>{!! nl2br(e($observe->result ?: '-')) !!}</td>

                    </tr>

                </tbody>

            </table>

        </section>

        <section class="observe-report-section observe-report-followups">

            <h3>3. ประวัติการติดตามผล</h3>

            <table class="observe-report-followup-table">

                <thead>

                    <tr>

                        <th class="number-col">ครั้งที่</th>

                        <th class="date-col">วันที่ติดตาม</th>

                        <th>การดำเนินการ</th>

                        <th>ผลลัพธ์</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($observe->followups as $followup)

                        <tr>

                            <td class="text-center">{{ $followup->followup_count ?: $loop->iteration }}</td>

                            <td class="text-center">{{ $thaiDate($followup->followup_date) }}</td>

                            <td>{!! nl2br(e($followup->followup_action ?: '-')) !!}</td>

                            <td>{!! nl2br(e($followup->followup_result ?: '-')) !!}</td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" class="observe-report-empty">ไม่มีข้อมูลการติดตามผล</td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </section>

        <footer class="observe-report-footer">

            <span>พิมพ์จากระบบเมื่อ {{ $thaiDate(now('Asia/Bangkok')) }}</span>

            <span>ข้อมูลผู้รับบริการ: {{ $clientFullName }}</span>

        </footer>

    </article>

</div>

<style>
.observe-report-page {
    padding: 24px 16px 40px;
    background: #f1f2f4;
    color: #111;
    font-family: "Noto Sans Thai", "Leelawadee UI", Tahoma, sans-serif;
}

.observe-report-toolbar {
    width: min(1120px, 100%);
    margin: 0 auto 14px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}

.observe-report-toolbar h1 {
    margin: 0 0 3px;
    color: #111;
    font-size: 1.28rem;
    font-weight: 700;
}

.observe-report-toolbar p {
    margin: 0;
    color: #666;
    font-size: .86rem;
}

.observe-report-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.observe-report-paper {
    width: min(1120px, 100%);
    margin: 0 auto;
    padding: 38px 46px 32px;
    background: #fff;
    border: 0;
    border-radius: 0;
    box-shadow: none;
}

.observe-report-document-header {
    padding-bottom: 12px;
    border-bottom: 1.5px solid #222;
}

.observe-report-document-header h2 {
    margin: 0;
    text-align: center;
    font-size: 1.45rem;
    font-weight: 700;
    color: #111;
}

.observe-report-client-name {
    margin-top: 14px;
    text-align: left;
    font-size: .95rem;
    line-height: 1.6;
    color: #111;
    font-weight: 400;
}

.observe-report-client-name .client-label {
    margin-right: 8px;
    font-weight: 600;
}

.observe-report-client-name .client-name {
    font-weight: 400;
}

.observe-report-kicker {
    margin-bottom: 2px;
    color: #333;
    font-size: .82rem;
    font-weight: 400;
    letter-spacing: 0;
}

.observe-report-document-header h2 {
    margin: 0;
    color: #000;
    font-size: 1.48rem;
    font-weight: 700;
    line-height: 1.35;
}

.observe-report-client-name {
    margin-top: 4px;
    color: #111;
    font-size: .96rem;
    font-weight: 400;
}

.observe-report-summary {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    column-gap: 38px;
    row-gap: 4px;
    margin: 15px 0 4px;
    padding: 8px 0;
    border: 0;
    border-top: 1px solid #777;
    border-bottom: 1px solid #777;
    border-radius: 0;
    overflow: visible;
}

.observe-report-summary-item {
    display: grid;
    grid-template-columns: 130px minmax(0, 1fr);
    gap: 8px;
    min-width: 0;
    padding: 2px 0;
    border: 0;
}

.observe-report-summary-item span,
.observe-report-summary-item strong {
    display: block;
    margin: 0;
    font-size: .84rem;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.observe-report-summary-item span {
    color: #333;
    font-weight: 600;
}

.observe-report-summary-item span::after {
    content: ":";
}

.observe-report-summary-item strong {
    color: #111;
    font-weight: 400;
}

.observe-report-section {
    margin-top: 22px;
}

.observe-report-section h3 {
    margin: 0 0 6px;
    padding: 0 0 4px;
    border: 0;
    border-bottom: 1px solid #222;
    background: transparent;
    color: #000;
    font-size: .98rem;
    font-weight: 700;
}

.observe-report-detail-table,
.observe-report-followup-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    background: transparent;
}

.observe-report-detail-table th,
.observe-report-detail-table td {
    border: 0;
    border-bottom: 1px dotted #b7b7b7;
    padding: 7px 4px;
    vertical-align: top;
    color: #111;
    font-size: .86rem;
    line-height: 1.65;
    overflow-wrap: anywhere;
}

.observe-report-detail-table th {
    padding-left: 0;
    background: transparent;
    text-align: left;
    font-weight: 600;
}

.observe-report-detail-table th::after {
    content: ":";
}

.observe-report-detail-table td {
    padding-right: 12px;
    font-weight: 400;
}

.observe-report-detail-table .label-col {
    width: 150px;
}

.observe-report-followup-table {
    margin-top: 2px;
}

.observe-report-followup-table th,
.observe-report-followup-table td {
    border: 1px solid #777;
    padding: 7px 8px;
    vertical-align: top;
    color: #111;
    font-size: .84rem;
    line-height: 1.55;
    overflow-wrap: anywhere;
}

.observe-report-followup-table thead th {
    background: transparent;
    text-align: center;
    font-weight: 600;
    border-top: 1.5px solid #222;
    border-bottom: 1.5px solid #222;
}

.observe-report-followup-table .number-col {
    width: 70px;
}

.observe-report-followup-table .date-col {
    width: 120px;
}

.observe-report-empty {
    padding: 16px !important;
    text-align: center;
    color: #555 !important;
}

.observe-report-footer {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    margin-top: 24px;
    padding-top: 7px;
    border-top: 1px solid #777;
    color: #555;
    font-size: .7rem;
}

@media (max-width: 767.98px) {
    .observe-report-page {
        padding: 10px 6px 24px;
    }

    .observe-report-paper {
        padding: 24px 16px;
    }

    .observe-report-summary {
        grid-template-columns: 1fr;
    }

    .observe-report-summary-item {
        grid-template-columns: 120px minmax(0, 1fr);
    }

    .observe-report-section {
        overflow-x: auto;
    }

    .observe-report-detail-table,
    .observe-report-followup-table {
        min-width: 720px;
    }

    .observe-report-footer {
        flex-direction: column;
        align-items: flex-start;
    }
}

@page {
    size: A4 landscape;
    margin: 12mm 14mm;
}

@media print {
    html,
    body {
        width: 100% !important;
        min-width: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .content-page,
    .main-content,
    .page-content,
    .content,
    .wrapper,
    .container,
    .container-fluid {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
        position: static !important;
        transform: none !important;
    }

    body * {
        visibility: hidden !important;
    }

    #observeReport,
    #observeReport * {
        visibility: visible !important;
    }

    #observeReport {
        position: absolute !important;
        inset: 0 auto auto 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        color: #000 !important;
        font-family: "Noto Sans Thai", "Leelawadee UI", Tahoma, sans-serif !important;
    }

    .no-print {
        display: none !important;
    }

    .observe-report-paper {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    .observe-report-document-header {
        padding-bottom: 6px !important;
        border-bottom: 1.2pt solid #000 !important;
    }

    .observe-report-kicker {
        font-size: 8.5pt !important;
        color: #000 !important;
    }

    .observe-report-document-header h2 {
        font-size: 15pt !important;
        color: #000 !important;
    }

    .observe-report-client-name {
        margin-top: 2px !important;
        font-size: 10pt !important;
        color: #000 !important;
    }

    .observe-report-summary {
        margin: 7px 0 2px !important;
        padding: 5px 0 !important;
        column-gap: 26px !important;
        border-color: #000 !important;
    }

    .observe-report-summary-item {
        grid-template-columns: 112px minmax(0, 1fr) !important;
        gap: 4px !important;
        padding: 1px 0 !important;
    }

    .observe-report-summary-item span,
    .observe-report-summary-item strong {
        color: #000 !important;
        font-size: 8.5pt !important;
        line-height: 1.35 !important;
    }

    .observe-report-section {
        margin-top: 8px !important;
        overflow: visible !important;
        break-inside: auto;
        page-break-inside: auto;
    }

    .observe-report-section h3 {
        margin-bottom: 3px !important;
        padding-bottom: 2px !important;
        border-color: #000 !important;
        font-size: 10pt !important;
        break-after: avoid;
        page-break-after: avoid;
    }

    .observe-report-detail-table,
    .observe-report-followup-table {
        width: 100% !important;
        min-width: 0 !important;
        table-layout: fixed !important;
    }

    .observe-report-detail-table th,
    .observe-report-detail-table td {
        border-bottom-color: #777 !important;
        padding: 3px 3px !important;
        color: #000 !important;
        font-size: 8.3pt !important;
        line-height: 1.4 !important;
    }

    .observe-report-detail-table .label-col {
        width: 125px !important;
    }

    .observe-report-followup-table th,
    .observe-report-followup-table td {
        border-color: #000 !important;
        padding: 4px 5px !important;
        color: #000 !important;
        font-size: 8pt !important;
        line-height: 1.35 !important;
    }

    .observe-report-followup-table thead th {
        background: transparent !important;
        border-top-width: 1.2pt !important;
        border-bottom-width: 1.2pt !important;
    }

    .observe-report-followup-table .number-col {
        width: 52px !important;
    }

    .observe-report-followup-table .date-col {
        width: 92px !important;
    }

    .observe-report-followup-table thead {
        display: table-header-group;
    }

    .observe-report-followup-table tr,
    .observe-report-detail-table tr {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .observe-report-footer {
        margin-top: 8px !important;
        padding-top: 4px !important;
        border-color: #000 !important;
        color: #000 !important;
        font-size: 7pt !important;
    }
}
</style>

@endsection