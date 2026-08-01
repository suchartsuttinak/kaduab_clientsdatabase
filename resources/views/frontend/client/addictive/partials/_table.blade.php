<style>
    .addictive-record-section .addictive-record-card {
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        background: #ffffff;
        box-shadow: 0 12px 32px rgba(15, 23, 42, .07);
    }

    .addictive-record-section .addictive-record-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.15rem;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%);
    }

    .addictive-record-section .addictive-record-head-left {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: .8rem;
    }

    .addictive-record-section .addictive-record-head-icon {
        display: inline-flex;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: #eaf2ff;
        color: #2563eb;
    }

    .addictive-record-section .addictive-record-head-text h6 {
        margin: 0;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
    }

    .addictive-record-section .addictive-record-head-text small {
        display: block;
        margin-top: .18rem;
        color: #64748b;
        font-size: .82rem;
        line-height: 1.5;
    }

    .addictive-record-section .addictive-record-body {
        padding: .9rem;
    }

    /* ใช้ wrapper นี้เลื่อนเพียงชั้นเดียว ไม่เปิด scrollX ของ DataTable */
    .addictive-record-section .addictive-table-wrap {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        border: 1px solid #e8eef6;
        border-radius: 15px;
        background: #ffffff;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }

    .addictive-record-section .dataTables_wrapper {
        width: 100%;
        min-width: 0;
    }

    .addictive-record-section .addictive-table {
        width: 100% !important;
        min-width: 1160px;
        margin: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
    }

    .addictive-record-section .addictive-table thead th {
        padding: .78rem .7rem;
        border-bottom: 1px solid #dbe4f0;
        background: #f8fafc;
        color: #334155;
        font-size: .78rem;
        font-weight: 800;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    .addictive-record-section .addictive-table tbody td {
        padding: .78rem .7rem;
        border-color: #edf1f6;
        background: #ffffff;
        color: #334155;
        font-size: .83rem;
        line-height: 1.55;
        vertical-align: middle;
    }

    .addictive-record-section .addictive-table tbody tr:hover td {
        background: #fbfdff;
    }

    .addictive-record-section .col-date { width: 125px; }
    .addictive-record-section .col-count { width: 75px; }
    .addictive-record-section .col-exam { width: 155px; }
    .addictive-record-section .col-refer { width: 175px; }
    .addictive-record-section .col-record { width: 250px; }
    .addictive-record-section .col-recorder { width: 145px; }
    .addictive-record-section .col-actions { width: 235px; }

    .addictive-record-section .addictive-text-clamp {
        display: -webkit-box;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        overflow-wrap: anywhere;
    }

    .addictive-record-section .badge {
        padding: .42rem .62rem;
        font-size: .74rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .addictive-record-section .addictive-action-group {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .42rem;
        flex-wrap: nowrap;
        white-space: nowrap;
    }

    .addictive-record-section .addictive-btn-action {
        display: inline-flex;
        min-height: 36px;
        align-items: center;
        justify-content: center;
        gap: .34rem;
        padding: .5rem .72rem;
        border-radius: 10px;
        font-size: .78rem;
        font-weight: 750;
        line-height: 1;
        white-space: nowrap;
        box-shadow: none !important;
    }

    .addictive-record-section .addictive-empty-state {
        padding: 2.8rem 1.2rem;
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        background: linear-gradient(180deg, #fbfdff 0%, #f8fbff 100%);
        color: #64748b;
        text-align: center;
    }

    .addictive-record-section .dataTables_length,
    .addictive-record-section .dataTables_filter {
        margin-bottom: .75rem;
        color: #475569;
        font-size: .82rem;
    }

    .addictive-record-section .dataTables_length select,
    .addictive-record-section .dataTables_filter input {
        min-height: 38px;
        border: 1px solid #dbe4f0;
        border-radius: 10px;
        background: #ffffff;
        box-shadow: none;
    }

    .addictive-record-section .dataTables_info,
    .addictive-record-section .dataTables_paginate {
        margin-top: .75rem;
        color: #64748b;
        font-size: .8rem;
    }

    @media (min-width: 1400px) {
        .addictive-record-section .addictive-table {
            min-width: 100%;
        }
    }

    @media (max-width: 767.98px) {
        .addictive-record-section .addictive-record-head {
            padding: .9rem;
        }

        .addictive-record-section .addictive-record-body {
            padding: .65rem;
        }
    }
</style>

<div class="addictive-record-section">
    @if($addictives->isNotEmpty())
        <div class="card border-0 shadow-sm addictive-record-card">
            <div class="addictive-record-head">
                <div class="addictive-record-head-left">
                    <div class="addictive-record-head-icon"><i class="bi bi-table"></i></div>
                    <div class="addictive-record-head-text">
                        <h6>รายการข้อมูลการตรวจสารเสพติด</h6>
                        <small>แสดงผลการตรวจ แนวทางดำเนินการ บันทึกผล และผู้ตรวจทั้งหมด</small>
                    </div>
                </div>
            </div>

            <div class="card-body addictive-record-body">
                <div class="addictive-table-wrap">
                    <table id="datatable-addictive" class="table table-hover align-middle addictive-table">
                        <thead>
                            <tr>
                                <th class="col-date">วันที่ตรวจ</th>
                                <th class="col-count">ครั้งที่</th>
                                <th class="col-exam">ผลการตรวจ</th>
                                <th class="col-refer">การดำเนินการต่อ</th>
                                <th class="col-record">บันทึกผล</th>
                                <th class="col-recorder">ผู้ตรวจ</th>
                                <th class="col-actions">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($addictives as $addictive)
                                <tr id="row-addictive-{{ $addictive->id }}">
                                    <td class="col-date text-center" data-order="{{ $addictive->date }}">
                                        {{ $addictive->date ? \Carbon\Carbon::parse($addictive->date)->format('d/m/') . (\Carbon\Carbon::parse($addictive->date)->year + 543) : '-' }}
                                    </td>
                                    <td class="col-count text-center">{{ $addictive->count ?? '-' }}</td>
                                    <td class="col-exam text-center">
                                        @if((int) $addictive->exam === 0)
                                            <span class="badge rounded-pill text-bg-success">ไม่พบสารเสพติด</span>
                                        @else
                                            <span class="badge rounded-pill text-bg-danger">พบสารเสพติด</span>
                                        @endif
                                    </td>
                                    <td class="col-refer text-center">
                                        @if((int) $addictive->exam === 1 && (int) $addictive->refer === 1)
                                            <span class="badge rounded-pill text-bg-warning text-dark">ส่งต่อบำบัด</span>
                                        @elseif((int) $addictive->exam === 1 && (int) $addictive->refer === 2)
                                            <span class="badge rounded-pill text-bg-info">ติดตามดูแลต่อเนื่อง</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="col-record">
                                        <span class="addictive-text-clamp" title="{{ $addictive->record ?: '-' }}">
                                            {{ $addictive->record ?: '-' }}
                                        </span>
                                    </td>
                                    <td class="col-recorder">
                                        <span class="addictive-text-clamp" title="{{ $addictive->recorder ?: '-' }}">
                                            {{ $addictive->recorder ?: '-' }}
                                        </span>
                                    </td>
                                    <td class="col-actions text-center">
                                        <div class="addictive-action-group">
                                            <button type="button"
                                                    class="btn btn-warning btn-sm addictive-btn-action"
                                                    onclick="openEditAddictive({{ $addictive->id }})">
                                                <i class="bi bi-pencil-square"></i><span>แก้ไข</span>
                                            </button>

                                            <form id="delete-form-addictive-{{ $addictive->id }}"
                                                  action="{{ route('addictive.delete', $addictive->id) }}"
                                                  method="POST"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="btn btn-danger btn-sm addictive-btn-action"
                                                        onclick="confirmDelete('delete-form-addictive-{{ $addictive->id }}', 'คุณต้องการลบข้อมูลการตรวจสารเสพติดนี้ใช่หรือไม่')">
                                                    <i class="bi bi-trash"></i><span>ลบ</span>
                                                </button>
                                            </form>

                                            <a href="{{ route('addictive.report.all', ['client_id' => $client->id, 'date_from' => $addictive->date, 'date_to' => $addictive->date]) }}"
                                               class="btn btn-outline-primary btn-sm addictive-btn-action">
                                                <i class="bi bi-printer"></i><span>รายงาน</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="addictive-empty-state">
            <div class="mb-2"><i class="bi bi-clipboard2-pulse fs-2"></i></div>
            <strong class="d-block text-dark mb-1">ยังไม่มีข้อมูลการตรวจสารเสพติด</strong>
            <span>กดปุ่ม “เพิ่มข้อมูล” เพื่อบันทึกผลการตรวจครั้งแรก</span>
        </div>
    @endif
</div>
