@php
    $hasDateFilter = request()->filled('from') || request()->filled('to');
@endphp

<style>
    .help-page .hp-table-card,
    .help-page .hp-no-results {
        background: #fff;
        border: 1px solid #dbe3ef;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .04);
    }

    .help-page .hp-table-card {
        overflow: hidden;
    }

    .help-page .hp-table-head {
        min-height: 56px;
        padding: .85rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        flex-wrap: wrap;
        background: #f8fafc;
        border-bottom: 1px solid #dbe3ef;
    }

    .help-page .hp-table-title {
        display: flex;
        align-items: center;
        gap: .55rem;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
    }

    .help-page .hp-table-title i {
        color: #2563eb;
    }

    .help-page .hp-table-meta {
        color: #64748b;
        font-size: .88rem;
        font-weight: 700;
    }

    .help-page .hp-table-wrap {
        overflow-x: auto;
    }

    .help-page .hp-table {
        min-width: 980px;
        margin: 0;
        table-layout: fixed;
    }

    .help-page .hp-table thead th {
        padding: .85rem .75rem;
        color: #334155;
        background: #eff6ff;
        border-bottom: 1px solid #bfdbfe;
        font-size: .88rem;
        font-weight: 800;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    .help-page .hp-table tbody > tr:not(.hp-detail-row) > td {
        padding: .9rem .75rem;
        color: #334155;
        border-bottom: 1px solid #edf2f7;
        vertical-align: middle;
    }

    .help-page .hp-table tbody > tr:not(.hp-detail-row):hover > td {
        background: #fbfdff;
    }

    .help-page .hp-date-badge,
    .help-page .hp-total-badge,
    .help-page .hp-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 30px;
        padding: .3rem .65rem;
        border-radius: 999px;
        font-size: .84rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .help-page .hp-date-badge {
        color: #1e40af;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
    }

    .help-page .hp-count-badge {
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .help-page .hp-total-badge {
        color: #166534;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        font-variant-numeric: tabular-nums;
    }

    .help-page .hp-toggle-btn,
    .help-page .hp-action-btn {
        min-height: 36px;
        padding: .43rem .72rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        border-radius: 10px;
        font-size: .82rem;
        font-weight: 750;
        line-height: 1.2;
        white-space: nowrap;
        text-decoration: none;
    }

    .help-page .hp-toggle-btn {
        color: #1d4ed8;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
    }

    .help-page .hp-toggle-btn:hover,
    .help-page .hp-toggle-btn:focus,
    .help-page .hp-toggle-btn.is-open {
        color: #fff;
        background: #2563eb;
        border-color: #2563eb;
    }

    .help-page .hp-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        flex-wrap: nowrap;
    }

    .help-page .hp-action-edit {
        color: #a16207;
        background: #fffbeb;
        border: 1px solid #fde68a;
    }

    .help-page .hp-action-edit:hover,
    .help-page .hp-action-edit:focus {
        color: #fff;
        background: #d97706;
        border-color: #d97706;
    }

    .help-page .hp-action-report {
        color: #1d4ed8;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
    }

    .help-page .hp-action-report:hover,
    .help-page .hp-action-report:focus {
        color: #fff;
        background: #2563eb;
        border-color: #2563eb;
    }

    .help-page .hp-action-delete {
        color: #b91c1c;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    .help-page .hp-action-delete:hover,
    .help-page .hp-action-delete:focus {
        color: #fff;
        background: #dc2626;
        border-color: #dc2626;
    }

    .help-page .hp-detail-cell {
        padding: 0 !important;
        border-bottom: 1px solid #dbe3ef !important;
        background: #f8fafc;
    }

    .help-page .hp-detail-shell {
        padding: .8rem;
    }

    .help-page .hp-detail-card {
        overflow: hidden;
        border: 1px solid #dbe3ef;
        border-radius: 14px;
        background: #fff;
    }

    .help-page .hp-detail-head {
        padding: .75rem .9rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        flex-wrap: wrap;
        background: #f8fafc;
        border-bottom: 1px solid #dbe3ef;
    }

    .help-page .hp-detail-title {
        display: flex;
        align-items: center;
        gap: .45rem;
        color: #0f172a;
        font-size: .9rem;
        font-weight: 800;
    }

    .help-page .hp-detail-title i {
        color: #2563eb;
    }

    .help-page .hp-detail-wrap {
        overflow-x: auto;
    }

    .help-page .hp-detail-table {
        width: 100%;
        min-width: 700px;
        margin: 0;
        table-layout: fixed;
    }

    .help-page .hp-detail-table th {
        padding: .7rem .75rem;
        color: #475569;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        font-size: .84rem;
        font-weight: 800;
        vertical-align: middle;
    }

    .help-page .hp-detail-table td {
        padding: .75rem;
        color: #334155;
        border-bottom: 1px solid #edf2f7;
        font-size: .88rem;
        vertical-align: middle;
    }

    .help-page .hp-detail-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .help-page .hp-col-item { text-align: left; }
    .help-page .hp-col-quantity { text-align: center; }
    .help-page .hp-col-money { text-align: right; font-variant-numeric: tabular-nums; }
    .help-page .hp-item-name { font-weight: 700; }
    .help-page .hp-item-money { color: #166534; font-weight: 800; }

    .help-page .hp-no-results {
        min-height: 220px;
        padding: 2rem 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #64748b;
    }

    .help-page .hp-no-results i {
        margin-bottom: .65rem;
        color: #94a3b8;
        font-size: 2rem;
    }

    .help-page .hp-no-results strong {
        color: #0f172a;
        font-size: 1rem;
    }

    @media (max-width: 575.98px) {
        .help-page .hp-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .help-page .hp-actions > *,
        .help-page .hp-actions form,
        .help-page .hp-actions form button {
            width: 100%;
        }
    }
</style>

@if($sessions->isNotEmpty())
    <section class="hp-table-card">
        <div class="hp-table-head">
            <div class="hp-table-title">
                <i class="bi bi-table"></i>
                <span>ประวัติการให้ความช่วยเหลือ</span>
            </div>
            <div class="hp-table-meta">
                แสดง {{ number_format($sessions->count()) }} ครั้ง
                • {{ number_format($totalItemCount ?? $sessions->sum(fn($session) => $session->items->count())) }} รายการ
            </div>
        </div>

        <div class="hp-table-wrap">
            <table class="table hp-table align-middle mb-0">
                <colgroup>
                    <col style="width: 17%;">
                    <col style="width: 15%;">
                    <col style="width: 20%;">
                    <col style="width: 18%;">
                    <col style="width: 30%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>วันที่ให้ความช่วยเหลือ</th>
                        <th>จำนวนรายการ</th>
                        <th>มูลค่ารวม</th>
                        <th>รายละเอียด</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessions as $session)
                        <tr>
                            <td class="text-center">
                                <span class="hp-date-badge">
                                    {{ \Carbon\Carbon::parse($session->help_date)->addYears(543)->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="hp-count-badge">
                                    {{ number_format($session->items->count()) }} รายการ
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="hp-total-badge">
                                    {{ number_format((float) $session->total_amount, 2) }} บาท
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="hp-toggle-btn"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#session-{{ $session->id }}"
                                        aria-expanded="false"
                                        aria-controls="session-{{ $session->id }}">
                                    <i class="bi bi-list-ul"></i>
                                    <span>แสดงรายการ</span>
                                </button>
                            </td>
                            <td>
                                <div class="hp-actions">
                                    <a href="{{ route('help_sessions.edit', [
                                            'client' => $client->id,
                                            'session' => $session->id
                                        ]) }}"
                                       class="hp-action-btn hp-action-edit">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>แก้ไข</span>
                                    </a>

                                    <a href="{{ route('help_sessions.report', [
                                            'client' => $client->id,
                                            'session' => $session->id
                                        ]) }}"
                                       class="hp-action-btn hp-action-report">
                                        <i class="bi bi-printer"></i>
                                        <span>รายงาน</span>
                                    </a>

                                    <form action="{{ route('help_sessions.destroy', [
                                                'client' => $client->id,
                                                'session' => $session->id
                                            ]) }}"
                                          method="POST"
                                          class="help-delete-form m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="hp-action-btn hp-action-delete help-delete-button">
                                            <i class="bi bi-trash3"></i>
                                            <span>ลบ</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <tr class="hp-detail-row">
                            <td colspan="5" class="hp-detail-cell">
                                <div id="session-{{ $session->id }}" class="collapse">
                                    <div class="hp-detail-shell">
                                        <div class="hp-detail-card">
                                            <div class="hp-detail-head">
                                                <div class="hp-detail-title">
                                                    <i class="bi bi-bag-heart-fill"></i>
                                                    <span>
                                                        รายละเอียดการช่วยเหลือ วันที่
                                                        {{ \Carbon\Carbon::parse($session->help_date)->addYears(543)->format('d/m/Y') }}
                                                    </span>
                                                </div>
                                                <div class="small fw-bold text-secondary">
                                                    รวม {{ number_format($session->items->count()) }} รายการ
                                                </div>
                                            </div>

                                            <div class="hp-detail-wrap">
                                                <table class="table hp-detail-table mb-0">
                                                    <colgroup>
                                                        <col style="width: 50%;">
                                                        <col style="width: 14%;">
                                                        <col style="width: 18%;">
                                                        <col style="width: 18%;">
                                                    </colgroup>
                                                    <thead>
                                                        <tr>
                                                            <th class="hp-col-item">รายการ</th>
                                                            <th class="hp-col-quantity">จำนวน</th>
                                                            <th class="hp-col-money">ราคา/หน่วย (บาท)</th>
                                                            <th class="hp-col-money">ราคารวม (บาท)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($session->items as $item)
                                                            <tr>
                                                                <td class="hp-col-item">
                                                                    <div class="hp-item-name">{{ $item->item_name }}</div>
                                                                </td>
                                                                <td class="hp-col-quantity">
                                                                    {{ number_format((int) $item->quantity) }}
                                                                </td>
                                                                <td class="hp-col-money">
                                                                    {{ number_format((float) $item->unit_price, 2) }}
                                                                </td>
                                                                <td class="hp-col-money">
                                                                    <span class="hp-item-money">
                                                                        {{ number_format((float) $item->total_price, 2) }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@elseif($hasDateFilter)
    <section class="hp-no-results" role="status">
        <i class="bi bi-search"></i>
        <strong>ไม่พบข้อมูลตามช่วงวันที่ที่เลือก</strong>
        <div class="mt-1">ลองเปลี่ยนช่วงวันที่ หรือกด “ดูทั้งหมด” เพื่อแสดงข้อมูลทุกวัน</div>
    </section>
@endif
