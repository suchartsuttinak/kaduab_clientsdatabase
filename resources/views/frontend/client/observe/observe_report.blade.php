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
            <div class="observe-report-kicker">แบบรายงานข้อมูลผู้รับบริการ</div>
            <h2>รายงานพฤติกรรมและการติดตามผล</h2>
            <div class="observe-report-client-name">{{ $clientFullName }}</div>
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
            <h3>ข้อมูลเหตุการณ์</h3>
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
            <h3>การดำเนินการและผลลัพธ์</h3>
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
            <h3>ประวัติการติดตามผล</h3>

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
    padding: 22px 14px 34px;
    background: #f4f7fb;
    font-family: inherit;
    color: #111827;
}

.observe-report-toolbar {
    width: min(1180px, 100%);
    margin: 0 auto 16px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}

.observe-report-toolbar h1 {
    margin: 0 0 4px;
    font-size: 1.45rem;
    font-weight: 800;
}

.observe-report-toolbar p {
    margin: 0;
    color: #64748b;
    font-size: .92rem;
}

.observe-report-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.observe-report-paper {
    width: min(1180px, 100%);
    margin: 0 auto;
    padding: 30px;
    background: #fff;
    border: 1px solid #dfe6ee;
    border-radius: 16px;
    box-shadow: 0 14px 38px rgba(15, 23, 42, .07);
}

.observe-report-document-header {
    text-align: center;
    padding-bottom: 16px;
    border-bottom: 2px solid #1f2937;
}

.observe-report-kicker {
    margin-bottom: 4px;
    color: #64748b;
    font-size: .84rem;
    font-weight: 700;
    letter-spacing: .04em;
}

.observe-report-document-header h2 {
    margin: 0;
    font-size: 1.65rem;
    font-weight: 800;
}

.observe-report-client-name {
    margin-top: 6px;
    color: #334155;
    font-size: 1rem;
    font-weight: 700;
}

.observe-report-summary {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    margin: 18px 0;
    border: 1px solid #cfd8e3;
    border-radius: 10px;
    overflow: hidden;
}

.observe-report-summary-item {
    min-width: 0;
    padding: 11px 13px;
    border-right: 1px solid #dbe3ec;
}

.observe-report-summary-item:last-child {
    border-right: 0;
}

.observe-report-summary-item span,
.observe-report-summary-item strong {
    display: block;
}

.observe-report-summary-item span {
    margin-bottom: 3px;
    color: #64748b;
    font-size: .78rem;
    font-weight: 700;
}

.observe-report-summary-item strong {
    color: #111827;
    font-size: .92rem;
    line-height: 1.45;
    overflow-wrap: anywhere;
}

.observe-report-section {
    margin-top: 18px;
}

.observe-report-section h3 {
    margin: 0;
    padding: 8px 11px;
    border: 1px solid #94a3b8;
    border-bottom: 0;
    background: #eef2f7;
    color: #111827;
    font-size: .95rem;
    font-weight: 800;
}

.observe-report-detail-table,
.observe-report-followup-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    background: #fff;
}

.observe-report-detail-table th,
.observe-report-detail-table td,
.observe-report-followup-table th,
.observe-report-followup-table td {
    border: 1px solid #94a3b8;
    padding: 9px 10px;
    vertical-align: top;
    font-size: .9rem;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

.observe-report-detail-table th {
    background: #f8fafc;
    text-align: left;
    font-weight: 800;
}

.observe-report-detail-table .label-col {
    width: 155px;
}

.observe-report-followup-table thead th {
    background: #e9eef5;
    text-align: center;
    font-weight: 800;
}

.observe-report-followup-table .number-col {
    width: 78px;
}

.observe-report-followup-table .date-col {
    width: 130px;
}

.observe-report-empty {
    padding: 20px !important;
    text-align: center;
    color: #64748b;
}

.observe-report-footer {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    margin-top: 16px;
    padding-top: 9px;
    border-top: 1px solid #cbd5e1;
    color: #64748b;
    font-size: .75rem;
}

@media (max-width: 767.98px) {
    .observe-report-page {
        padding: 14px 8px 24px;
    }

    .observe-report-paper {
        padding: 18px 12px;
        border-radius: 12px;
    }

    .observe-report-summary {
        grid-template-columns: 1fr 1fr;
    }

    .observe-report-summary-item:nth-child(2) {
        border-right: 0;
    }

    .observe-report-summary-item:nth-child(-n+2) {
        border-bottom: 1px solid #dbe3ec;
    }

    .observe-report-section {
        overflow-x: auto;
    }

    .observe-report-detail-table,
    .observe-report-followup-table {
        min-width: 720px;
    }
}

@page {
    size: A4 landscape;
    margin: 12mm;
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
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .observe-report-document-header {
        padding-bottom: 8px !important;
        border-bottom: 1.5px solid #000 !important;
    }

    .observe-report-kicker {
        font-size: 8pt !important;
        color: #000 !important;
    }

    .observe-report-document-header h2 {
        font-size: 16pt !important;
        color: #000 !important;
    }

    .observe-report-client-name {
        margin-top: 3px !important;
        font-size: 10pt !important;
        color: #000 !important;
    }

    .observe-report-summary {
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        margin: 9px 0 !important;
        border: 1px solid #000 !important;
        border-radius: 0 !important;
    }

    .observe-report-summary-item {
        padding: 5px 7px !important;
        border-color: #000 !important;
    }

    .observe-report-summary-item span {
        margin-bottom: 1px !important;
        color: #000 !important;
        font-size: 7.5pt !important;
    }

    .observe-report-summary-item strong {
        color: #000 !important;
        font-size: 8.5pt !important;
        line-height: 1.25 !important;
    }

    .observe-report-section {
        margin-top: 8px !important;
        overflow: visible !important;
        break-inside: auto;
        page-break-inside: auto;
    }

    .observe-report-section h3 {
        padding: 4px 6px !important;
        border-color: #000 !important;
        background: #e9e9e9 !important;
        color: #000 !important;
        font-size: 9pt !important;
    }

    .observe-report-detail-table,
    .observe-report-followup-table {
        width: 100% !important;
        min-width: 0 !important;
        table-layout: fixed !important;
    }

    .observe-report-detail-table th,
    .observe-report-detail-table td,
    .observe-report-followup-table th,
    .observe-report-followup-table td {
        border-color: #000 !important;
        padding: 4px 5px !important;
        color: #000 !important;
        font-size: 8pt !important;
        line-height: 1.35 !important;
    }

    .observe-report-detail-table th,
    .observe-report-followup-table thead th {
        background: #f0f0f0 !important;
    }

    .observe-report-detail-table .label-col {
        width: 125px !important;
    }

    .observe-report-followup-table .number-col {
        width: 55px !important;
    }

    .observe-report-followup-table .date-col {
        width: 95px !important;
    }

    .observe-report-followup-table thead {
        display: table-header-group;
    }

    .observe-report-followup-table tr,
    .observe-report-detail-table tr,
    .observe-report-summary-item {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .observe-report-footer {
        margin-top: 8px !important;
        padding-top: 4px !important;
        border-color: #000 !important;
        color: #000 !important;
        font-size: 6.8pt !important;
    }
}
</style>
@endsection
