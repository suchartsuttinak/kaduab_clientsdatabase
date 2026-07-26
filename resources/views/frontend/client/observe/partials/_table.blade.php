<div class="observe-body observe-modern-page">

    @if ($observes->count() > 0)
        <div class="section-card observe-modern-card">
            <div class="section-header observe-modern-header">
                <div class="observe-modern-title-wrap">
                    <h2 class="section-title mb-0">
                        <i class="bi bi-table"></i>
                        รายการบันทึกพฤติกรรม
                    </h2>
                    <div class="observe-modern-subtitle">
                        แสดงข้อมูลพฤติกรรมที่บันทึกไว้ทั้งหมด พร้อมสถานะการติดตามผลล่าสุด
                    </div>
                </div>

                <span class="section-badge observe-modern-badge">
                    {{ $observes->count() }} รายการ
                </span>
            </div>

            <div class="table-wrap">
                <table class="table observe-table observe-modern-table">
                    <thead>
                        <tr>
                            <th style="min-width: 140px;">วันที่</th>
                            <th style="min-width: 240px;">พฤติกรรมที่พบเห็น</th>
                            <th style="min-width: 220px;">ผลลัพธ์</th>
                            <th style="min-width: 160px;">ผู้บันทึก</th>
                            <th style="min-width: 320px;">การติดตามผล</th>
                            <th class="text-center" style="min-width: 250px;">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($observes as $obs)
                            @php
                                $latestFollowup = $obs->followups
                                    ->sortByDesc(function ($item) {
                                        return strtotime($item->followup_date ?? '1970-01-01');
                                    })
                                    ->first();
                            @endphp

                            <tr>
                                <td>
                                    <div class="observe-date-block">
                                        <div class="observe-date-main">
                                            {{ $obs->date ?: '-' }}
                                        </div>

                                        @if (!empty($obs->record_date))
                                            <div class="observe-date-sub">
                                                บันทึก: {{ $obs->record_date }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <div class="data-main observe-text-strong">
                                        {{ $obs->behavior ?: '-' }}
                                    </div>

                                    @if (!empty($obs->cause))
                                        <div class="data-sub observe-text-muted">
                                            สาเหตุ: {{ $obs->cause }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="data-main observe-text-strong">
                                        {{ $obs->result ?: '-' }}
                                    </div>

                                    @if (!empty($obs->solution))
                                        <div class="data-sub observe-text-muted">
                                            แนวทาง: {{ $obs->solution }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="data-main observe-recorder-chip">
                                        {{ $obs->recorder ?: '-' }}
                                    </div>
                                </td>

                                <td>
                                    @if ($latestFollowup)
                                        <div class="observe-follow-summary">
                                            <div class="observe-follow-summary__top">
                                                <span class="observe-follow-date-chip">
                                                    <i class="bi bi-calendar-event"></i>
                                                    {{ $latestFollowup->followup_date }}
                                                </span>

                                                <span class="observe-follow-count-chip">
                                                    ครั้งที่ {{ $latestFollowup->followup_count }}
                                                </span>
                                            </div>

                                            <div class="observe-follow-summary__body">
                                                {{ $latestFollowup->followup_result ?: 'ยังไม่ได้ระบุผลลัพธ์' }}
                                            </div>
                                        </div>

                                        @if ($obs->followups->count() > 1)
                                            <div class="observe-follow-summary__more">
                                                ทั้งหมด {{ $obs->followups->count() }} รายการติดตาม
                                            </div>
                                        @endif
                                    @else
                                        <div class="followup-empty observe-empty-chip">
                                            ยังไม่มีการติดตามผล
                                        </div>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="action-stack observe-action-stack">

                                        <a href="{{ route('observe.report', $obs->id) }}"
                                            class="btn-action btn-action-primary text-decoration-none observe-btn-primary">
                                            <i class="bi bi-file-earmark-text"></i> รายงาน
                                        </a>

                                        <a href="{{ route('observe.edit', $obs->id) }}"
                                            class="btn-action btn-action-warning text-decoration-none observe-btn-warning">
                                            <i class="bi bi-pencil-square"></i> แก้ไข
                                        </a>

                                        <form id="delete-form-observe-{{ $obs->id }}"
                                            action="{{ route('observe.delete', $obs->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn-action btn-action-danger observe-btn-danger"
                                                onclick="confirmDelete('delete-form-observe-{{ $obs->id }}')">
                                                <i class="bi bi-trash"></i> ลบ
                                            </button>
                                        </form>

                                        <button type="button" class="btn-action btn-action-info observe-btn-info"
                                            data-bs-toggle="modal"
                                            data-bs-target="#addFollowupModal{{ $obs->id }}">
                                            <i class="bi bi-arrow-repeat"></i> ติดตามผล
                                        </button>
                                    </div>

                                    {{-- Modal เพิ่มการติดตามผล --}}
                                    <div class="modal fade observe-modal" id="addFollowupModal{{ $obs->id }}"
                                        tabindex="-1" aria-hidden="true">
                                        <div
                                            class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content observe-modern-modal">
                                                <div class="modal-header observe-modern-modal-header">
                                                    <h5 class="modal-title">
                                                        <i class="bi bi-plus-circle"></i>
                                                        เพิ่มการติดตามผล (พฤติกรรมวันที่ {{ $obs->date }})
                                                    </h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <form action="{{ route('observe.followup.store') }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="hidden" name="observe_id"
                                                            value="{{ $obs->id }}">

                                                        <div class="form-section observe-modern-form-section">
                                                            <h6 class="form-section-title">
                                                                <i class="bi bi-calendar-check"></i>
                                                                ข้อมูลการติดตาม
                                                            </h6>

                                                            <div class="row g-3">
                                                                <div class="col-12 col-md-6">
                                                                    <label class="form-label-modern text-start d-block">
                                                                        วันที่ติดตาม <span class="text-danger">*</span>
                                                                    </label>

                                                                    <input type="date" name="followup_date"
                                                                        class="form-control form-control-modern @error('followup_date') is-invalid @enderror"
                                                                        value="{{ old('followup_date', now('Asia/Bangkok')->toDateString()) }}"
                                                                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                                                                        required>

                                                                    @error('followup_date')
                                                                        <div class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>

                                                                <div class="col-12 col-md-6">
                                                                    <label class="form-label-modern text-start d-block">
                                                                        ครั้งที่
                                                                    </label>

                                                                    <div
                                                                        class="form-control form-control-modern observe-auto-count-box text-start">
                                                                        ระบบกำหนดอัตโนมัติ
                                                                    </div>
                                                                </div>

                                                                <div class="col-12">
                                                                    <label class="form-label-modern text-start d-block">
                                                                        การดำเนินการ
                                                                    </label>

                                                                    <textarea name="followup_action" class="form-control form-control-modern text-start" rows="3"></textarea>
                                                                </div>

                                                                <div class="col-12">
                                                                    <label class="form-label-modern text-start d-block">
                                                                        ผลลัพธ์
                                                                    </label>

                                                                    <textarea name="followup_result" class="form-control form-control-modern text-start" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer-modern">
                                                            <button type="submit" class="btn-form-primary">
                                                                <i class="bi bi-save"></i> บันทึกการติดตามผล
                                                            </button>
                                                            <button type="button" class="btn-form-secondary"
                                                                data-bs-dismiss="modal">
                                                                <i class="bi bi-x-circle"></i> ปิด
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    ยังไม่มีบันทึกพฤติกรรม
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="empty-state observe-modern-empty-state">
            <i class="bi bi-info-circle"></i>
            <div class="fw-bold mb-1">ยังไม่มีบันทึกพฤติกรรม</div>
            <div>เริ่มต้นโดยกดปุ่ม “เพิ่มข้อมูลใหม่” เพื่อบันทึกข้อมูลการสังเกตพฤติกรรม</div>
        </div>
    @endif

    {{-- รายการติดตามผลของ observe เดียว --}}
    @if (isset($observe))
        <div class="section-card mt-4 observe-modern-card">
            <div class="section-header observe-modern-header">
                <div class="observe-modern-title-wrap">
                    <h2 class="section-title mb-0">
                        <i class="bi bi-list-check"></i>
                        รายการติดตามผล
                    </h2>
                    <div class="observe-modern-subtitle">
                        แสดงลำดับการติดตามผลของพฤติกรรมรายการที่กำลังแก้ไข
                    </div>
                </div>

                <button type="button" class="btn-modern btn-modern-primary" data-bs-toggle="modal"
                    data-bs-target="#addFollowupModal{{ $observe->id }}">
                    <i class="bi bi-plus-circle"></i>
                    เพิ่มการติดตามผล
                </button>
            </div>

            <div class="table-wrap">
                <table class="table observe-table observe-modern-table" style="min-width: 780px;">
                    <thead>
                        <tr>
                            <th>วันที่ติดตาม</th>
                            <th>ครั้งที่</th>
                            <th>การดำเนินการ</th>
                            <th>ผลลัพธ์</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($observe->followups as $f)
                            <tr>
                                <td>
                                    <span class="observe-follow-date-chip">
                                        <i class="bi bi-calendar-event"></i>
                                        {{ $f->followup_date }}
                                    </span>
                                </td>

                                <td>
                                    <span class="observe-follow-count-chip">
                                        ครั้งที่ {{ $f->followup_count }}
                                    </span>
                                </td>

                                <td>{{ $f->followup_action ?: '-' }}</td>
                                <td>{{ $f->followup_result ?: '-' }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn-action btn-action-warning observe-btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editFollowupModal{{ $f->id }}">
                                        <i class="bi bi-pencil-square"></i> แก้ไข
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    ยังไม่มีการติดตามผล
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<style>
    /* =========================================================
   Observe Modern Page
   Scope เฉพาะส่วนนี้ ไม่กระทบส่วนอื่น
========================================================= */
    .observe-modern-page .observe-modern-card {
        border: 1px solid #e7edf4;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.04);
        background: #fff;
    }

    .observe-modern-page .observe-modern-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding: 18px 20px;
        border-bottom: 1px solid #eef2f6;
        background: #fff;
    }

    .observe-modern-page .observe-modern-title-wrap {
        min-width: 0;
    }

    .observe-modern-page .observe-modern-subtitle {
        margin-top: 6px;
        color: #64748b;
        font-size: .92rem;
        line-height: 1.6;
    }

    .observe-modern-page .observe-modern-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 92px;
        padding: 8px 14px;
        border-radius: 999px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
        font-weight: 800;
    }

    .observe-modern-page .observe-modern-table {
        margin: 0;
    }

    .observe-modern-page .observe-modern-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: .9rem;
        font-weight: 800;
        padding: 15px 14px;
        border-bottom: 1px solid #e8edf3;
        vertical-align: middle;
    }

    .observe-modern-page .observe-modern-table tbody td {
        padding: 16px 14px;
        vertical-align: middle;
        border-bottom: 1px solid #eef2f6;
    }

    .observe-modern-page .observe-modern-table tbody tr:last-child td {
        border-bottom: none;
    }

    .observe-modern-page .observe-modern-table tbody tr:hover td {
        background: #fcfdff;
    }

    .observe-modern-page .observe-date-block {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .observe-modern-page .observe-date-main {
        font-size: .95rem;
        font-weight: 800;
        color: #0f172a;
    }

    .observe-modern-page .observe-date-sub {
        font-size: .83rem;
        color: #94a3b8;
    }

    .observe-modern-page .observe-text-strong {
        color: #0f172a;
        font-weight: 700;
        line-height: 1.65;
    }

    .observe-modern-page .observe-text-muted {
        color: #64748b;
        line-height: 1.65;
    }

    .observe-modern-page .observe-recorder-chip {
        display: inline-flex;
        align-items: center;
        min-height: 36px;
        padding: 7px 12px;
        border-radius: 12px;
        background: #f8fafc;
        color: #334155;
        font-weight: 700;
    }

    .observe-modern-page .observe-follow-summary {
        display: grid;
        gap: 8px;
    }

    .observe-modern-page .observe-follow-summary__top {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .observe-modern-page .observe-follow-summary__body {
        color: #334155;
        font-size: .9rem;
        line-height: 1.7;
    }

    .observe-modern-page .observe-follow-summary__more {
        margin-top: 6px;
        color: #94a3b8;
        font-size: .82rem;
    }

    .observe-modern-page .observe-follow-date-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        border-radius: 999px;
        background: linear-gradient(135deg, #eef4ff 0%, #dbeafe 100%);
        color: #1d4ed8;
        font-weight: 700;
        font-size: .84rem;
        white-space: nowrap;
    }

    .observe-modern-page .observe-follow-count-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 12px;
        border-radius: 999px;
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
        color: #c2410c;
        font-weight: 800;
        font-size: .84rem;
        white-space: nowrap;
    }

    .observe-modern-page .observe-empty-chip {
        display: inline-flex;
        align-items: center;
        padding: 7px 12px;
        border-radius: 999px;
        background: #f8fafc;
        color: #94a3b8;
        font-size: .85rem;
    }

    .observe-modern-page .observe-action-stack {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px;
    }

    .observe-modern-page .observe-action-stack .btn-action {
        white-space: nowrap;
    }

    .observe-modern-page .observe-btn-primary {
        background: #eef4ff;
        color: #1d4ed8;
        border: 1px solid #dbeafe;
    }

    .observe-modern-page .observe-btn-primary:hover {
        background: #dbeafe;
        color: #1e40af;
    }

    .observe-modern-page .observe-btn-warning {
        border-color: #fed7aa;
    }

    .observe-modern-page .observe-btn-danger {
        border-color: #fecaca;
    }

    .observe-modern-page .observe-btn-info {
        border-color: #bfdbfe;
    }

    .observe-modern-page .observe-auto-count-box {
        display: flex;
        align-items: center;
        background: #f8fafc;
        color: #64748b;
        font-weight: 700;
    }

    .observe-modern-page .observe-modern-modal {
        border-radius: 20px;
        overflow: hidden;
    }

    .observe-modern-page .observe-modern-modal-header {
        background: #f8fafc;
        border-bottom: 1px solid #e9eef5;
    }

    .observe-modern-page .observe-modern-form-section {
        border: 1px solid #eef2f6;
        border-radius: 18px;
        padding: 16px;
        background: #fff;
    }

    .observe-modern-page .observe-modern-empty-state {
        border: 1px solid #e7edf4;
        border-radius: 22px;
        padding: 34px 20px;
        background: #fff;
        text-align: center;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.04);
    }

    @media (max-width: 767.98px) {
        .observe-modern-page .observe-modern-header {
            padding: 16px 14px;
        }

        .observe-modern-page .observe-modern-table thead th {
            padding: 13px 12px;
            font-size: .86rem;
        }

        .observe-modern-page .observe-modern-table tbody td {
            padding: 14px 12px;
        }

        .observe-modern-page .observe-action-stack {
            min-width: max-content;
            flex-wrap: nowrap;
        }

        .observe-modern-page .observe-follow-summary {
            min-width: 220px;
        }

        .observe-modern-page .observe-follow-date-chip,
        .observe-modern-page .observe-follow-count-chip {
            font-size: .8rem;
            padding: 6px 10px;
        }
    }
</style>
