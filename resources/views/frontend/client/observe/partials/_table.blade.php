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

    $observeStatusLabels = [
        'ongoing' => 'อยู่ระหว่างการดำเนินงาน',
        'goal_met' => 'บรรลุเป้าหมาย',
        'referred' => 'ส่งต่อข้อมูล',
    ];

    $observeRiskLabels = [
        'none' => 'ไม่พบความเสี่ยง',
        'low' => 'ความเสี่ยงต่ำ',
        'moderate' => 'ความเสี่ยงปานกลาง',
        'high' => 'ความเสี่ยงสูง',
    ];
@endphp

<div class="observe-body observe-modern-page">
    @if ($observes->isNotEmpty())
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
                            <th style="min-width: 190px;">สถานะ / ความเสี่ยง</th>
                            <th style="min-width: 160px;">ผู้บันทึก</th>
                            <th style="min-width: 320px;">การติดตามผล</th>
                            <th class="text-center" style="min-width: 250px;">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($observes as $obs)
                            @php
                                $latestFollowup = $obs->followups->last();
                                $currentStatus = $latestFollowup->status ?? $obs->status ?? 'ongoing';
                                $currentRisk = $latestFollowup->risk_level ?? $obs->risk_level ?? 'none';
                                $currentStatusLabel = $observeStatusLabels[$currentStatus] ?? '-';
                                $currentRiskLabel = $observeRiskLabels[$currentRisk] ?? '-';
                                $hasRestrictedReferral = !$canManageObserveReferral && $currentStatus === 'referred';
                            @endphp

                            <tr>
                                <td>
                                    <div class="observe-date-block">
                                        <div class="observe-date-main">{{ $thaiDate($obs->date) }}</div>

                                        @if (!empty($obs->record_date))
                                            <div class="observe-date-sub">
                                                บันทึก: {{ $thaiDate($obs->record_date) }}
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
                                    <div class="observe-workflow-chip-stack">
                                        <span class="observe-status-chip observe-status-{{ $currentStatus }}">
                                            {{ $currentStatusLabel }}
                                        </span>
                                        <span class="observe-risk-chip observe-risk-{{ $currentRisk }}">
                                            {{ $currentRiskLabel }}
                                        </span>
                                    </div>
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
                                                    {{ $thaiDate($latestFollowup->followup_date) }}
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
                                            <i class="bi {{ $currentStatus === 'referred' ? 'bi-eye' : 'bi-pencil-square' }}"></i>
                                            {{ $currentStatus === 'referred' ? ($canManageObserveReferral ? 'ดำเนินการต่อ' : 'ดูข้อมูล') : 'แก้ไข' }}
                                        </a>

                                        @if (!$hasRestrictedReferral)
                                            <form id="delete-form-observe-{{ $obs->id }}"
                                                  action="{{ route('observe.delete', $obs->id) }}"
                                                  method="POST"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="btn-action btn-action-danger observe-btn-danger"
                                                        onclick="confirmDelete('delete-form-observe-{{ $obs->id }}')">
                                                    <i class="bi bi-trash"></i> ลบ
                                                </button>
                                            </form>
                                        @endif

                                        @if ($currentStatus === 'ongoing')
                                            <button type="button"
                                                    class="btn-action btn-action-info observe-btn-info"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#addFollowupModal{{ $obs->id }}">
                                                <i class="bi bi-arrow-repeat"></i> ติดตามผล
                                            </button>
                                        @elseif ($currentStatus === 'referred')
                                            <span class="observe-case-closed-note">
                                                <i class="bi bi-send-check-fill"></i> ส่งต่อข้อมูลแล้ว
                                            </span>
                                        @else
                                            <span class="observe-case-closed-note">
                                                <i class="bi bi-check-circle-fill"></i> บรรลุเป้าหมายแล้ว
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
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

    @if (isset($observe) && $observe)
        @php
            $selectedLatestFollowup = $observe->followups->last();
            $selectedCurrentStatus = $selectedLatestFollowup->status ?? $observe->status ?? 'ongoing';
        @endphp

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

                @if ($selectedCurrentStatus === 'ongoing')
                    <button type="button"
                            class="btn-modern btn-modern-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#addFollowupModal{{ $observe->id }}">
                        <i class="bi bi-plus-circle"></i>
                        เพิ่มการติดตามผล
                    </button>
                @else
                    <span class="observe-case-closed-banner">
                        <i class="bi {{ $selectedCurrentStatus === 'referred' ? 'bi-send-check-fill' : 'bi-check-circle-fill' }}"></i>
                        @if($selectedCurrentStatus === 'referred')
                            ส่งต่อข้อมูล — ปิดการติดตามในส่วนเดิม
                        @else
                            {{ $observeStatusLabels[$selectedCurrentStatus] ?? 'สิ้นสุดรอบนี้' }} — ไม่สามารถเพิ่มรอบใหม่
                        @endif
                    </span>
                @endif
            </div>

            <div class="table-wrap">
                <table class="table observe-table observe-modern-table" style="min-width: 780px;">
                    <thead>
                        <tr>
                            <th>วันที่ติดตาม</th>
                            <th>ครั้งที่</th>
                            <th>การดำเนินการ</th>
                            <th>ผลลัพธ์</th>
                            <th>สถานะ / ความเสี่ยง</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($observe->followups as $f)
                            <tr>
                                <td>
                                    <span class="observe-follow-date-chip">
                                        <i class="bi bi-calendar-event"></i>
                                        {{ $thaiDate($f->followup_date) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="observe-follow-count-chip">
                                        ครั้งที่ {{ $f->followup_count }}
                                    </span>
                                </td>
                                <td>{{ $f->followup_action ?: '-' }}</td>
                                <td>{{ $f->followup_result ?: '-' }}</td>
                                <td>
                                    <div class="observe-workflow-chip-stack">
                                        <span class="observe-status-chip observe-status-{{ $f->status ?? 'ongoing' }}">
                                            {{ $observeStatusLabels[$f->status ?? 'ongoing'] ?? '-' }}
                                        </span>
                                        <span class="observe-risk-chip observe-risk-{{ $f->risk_level ?? 'none' }}">
                                            {{ $observeRiskLabels[$f->risk_level ?? 'none'] ?? '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($selectedCurrentStatus === 'referred')
                                        <span class="observe-case-closed-note"><i class="bi bi-lock-fill"></i> ล็อกประวัติ</span>
                                    @else
                                        <button type="button"
                                                class="btn-action btn-action-warning observe-btn-warning"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editFollowupModal{{ $f->id }}">
                                            <i class="bi bi-pencil-square"></i> แก้ไข
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    ยังไม่มีการติดตามผล
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @include('frontend.client.observe.partials._referral_section', [
            'observe' => $observe,
            'canManageObserveReferral' => $canManageObserveReferral,
            'thaiDate' => $thaiDate,
        ])
    @endif
</div>

{{-- วาง Modal ไว้นอกตาราง ป้องกัน overflow/backdrop และปุ่มปิดทำงานผิดปกติ --}}
@foreach ($observes as $obs)
    @php
        $followupBag = 'followupStore' . $obs->id;
        $hasFollowupErrors = $errors->getBag($followupBag)->any();
        $obsLatestFollowup = $obs->followups->last();
        $obsCurrentStatus = $obsLatestFollowup->status ?? $obs->status ?? 'ongoing';
    @endphp

    @if ($obsCurrentStatus === 'ongoing')
    <div class="modal fade observe-modal"
         id="addFollowupModal{{ $obs->id }}"
         tabindex="-1"
         aria-labelledby="addFollowupModalLabel{{ $obs->id }}"
         aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content observe-modern-modal">
                <div class="modal-header observe-modern-modal-header">
                    <h5 class="modal-title" id="addFollowupModalLabel{{ $obs->id }}">
                        <i class="bi bi-plus-circle"></i>
                        เพิ่มการติดตามผล (พฤติกรรมวันที่ {{ $thaiDate($obs->date) }})
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('observe.followup.store') }}"
                          method="POST"
                          class="observe-submit-form">
                        @csrf
                        <input type="hidden" name="observe_id" value="{{ $obs->id }}">

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
                                    <input type="date"
                                           name="followup_date"
                                           class="form-control form-control-modern @error('followup_date', $followupBag) is-invalid @enderror"
                                           value="{{ $hasFollowupErrors ? old('followup_date') : now('Asia/Bangkok')->toDateString() }}"
                                           min="{{ $obs->date }}"
                                           max="{{ now('Asia/Bangkok')->toDateString() }}"
                                           required>
                                    @error('followup_date', $followupBag)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label-modern text-start d-block">ครั้งที่</label>
                                    <div class="form-control form-control-modern observe-auto-count-box text-start">
                                        ระบบกำหนดอัตโนมัติ
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label-modern text-start d-block">การดำเนินการ</label>
                                    <textarea name="followup_action"
                                              class="form-control form-control-modern text-start @error('followup_action', $followupBag) is-invalid @enderror"
                                              rows="3"
                                              maxlength="5000">{{ $hasFollowupErrors ? old('followup_action') : '' }}</textarea>
                                    @error('followup_action', $followupBag)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label-modern text-start d-block">ผลลัพธ์</label>
                                    <textarea name="followup_result"
                                              class="form-control form-control-modern text-start @error('followup_result', $followupBag) is-invalid @enderror"
                                              rows="3"
                                              maxlength="5000">{{ $hasFollowupErrors ? old('followup_result') : '' }}</textarea>
                                    @error('followup_result', $followupBag)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @include('frontend.client.observe.partials._workflow_fields', [
                            'workflowItem' => null,
                            'workflowBag' => $followupBag,
                            'workflowUseOld' => $hasFollowupErrors,
                            'workflowCanEdit' => true,
                            'workflowCanClose' => true,
                        ])

                        <div class="modal-footer-modern">
                            <button type="submit"
                                    class="btn-form-primary"
                                    data-submit-button
                                    data-loading-text="กำลังบันทึก...">
                                <i class="bi bi-save"></i>
                                <span data-submit-label>บันทึกการติดตามผล</span>
                            </button>
                            <button type="button" class="btn-form-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> ปิด
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

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


    .observe-modern-page .observe-workflow-chip-stack {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }

    .observe-modern-page .observe-status-chip,
    .observe-modern-page .observe-risk-chip {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .observe-modern-page .observe-status-ongoing {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .observe-modern-page .observe-status-goal_met {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .observe-modern-page .observe-status-referred {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }

    .observe-modern-page .observe-risk-none {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .observe-modern-page .observe-risk-low {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .observe-modern-page .observe-risk-moderate {
        background: #fffbeb;
        color: #a16207;
        border: 1px solid #fde68a;
    }

    .observe-modern-page .observe-risk-high {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .observe-modern-page .observe-case-closed-note,
    .observe-modern-page .observe-case-closed-banner {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        background: #f8fafc;
        color: #64748b;
        font-size: .8rem;
        font-weight: 800;
        padding: 7px 10px;
    }

    .observe-modern-page .observe-case-closed-banner {
        padding: 9px 12px;
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
