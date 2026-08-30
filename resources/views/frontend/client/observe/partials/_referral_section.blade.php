@php
    $selectedLatestRegular = $observe->followups->last();
    $selectedCurrentRegularStatus = $selectedLatestRegular->status ?? $observe->status ?? 'ongoing';

    $referralSourceRound = $observe->followups
        ->filter(fn ($item) => ($item->status ?? 'ongoing') === 'referred')
        ->last();
    $referralSourceDate = $referralSourceRound->followup_date ?? $observe->date;

    $processLabels = [
        'group_therapy' => 'กลุ่มบำบัด',
        'family_therapy' => 'ครอบครัวบำบัด',
        'psychotherapy_counseling' => 'จิตบำบัด/ให้คำปรึกษา',
        'behavior_therapy' => 'พฤติกรรมบำบัด',
        'referred_treatment' => 'ส่งต่อเข้ารับการบำบัด',
    ];
    $riskLabels = [
        'none' => 'ไม่พบความเสี่ยง',
        'low' => 'ความเสี่ยงต่ำ',
        'moderate' => 'ความเสี่ยงปานกลาง',
        'high' => 'ความเสี่ยงสูง',
    ];
    $referralStatusLabels = [
        'ongoing' => 'อยู่ระหว่างดำเนินการ',
        'goal_met' => 'บรรลุเป้าหมาย',
    ];
@endphp

@if($selectedCurrentRegularStatus === 'referred')
    @if(!$canManageObserveReferral)
        <div class="section-card mt-4 observe-modern-card observe-referral-handoff-card">
            <div class="observe-referral-handoff-lock">
                <i class="bi bi-shield-lock-fill"></i>
                <div>
                    <h3>สถานะ: ส่งต่อข้อมูล</h3>
                    <p>การดำเนินงานในส่วนของครู/ผู้ใช้ที่ไม่เกี่ยวข้องสิ้นสุดแล้ว ข้อมูลการช่วยเหลือหลังส่งต่อจำกัดเฉพาะนักสังคมสงเคราะห์ ผู้บริหาร และ Admin</p>
                </div>
            </div>
        </div>
    @else
        @php
            $referralRounds = $observe->relationLoaded('referralRounds') ? $observe->referralRounds : collect();
            $latestReferralRound = $referralRounds->last();
            $referralClosed = $latestReferralRound && ($latestReferralRound->status ?? 'ongoing') === 'goal_met';
            $referralStoreBag = 'referralStore' . $observe->id;
            $hasReferralStoreErrors = $errors->getBag($referralStoreBag)->any();
        @endphp

        <div class="section-card mt-4 observe-modern-card observe-referral-care-card">
            <div class="section-header observe-modern-header">
                <div class="observe-modern-title-wrap">
                    <h2 class="section-title mb-0">
                        <i class="bi bi-person-heart"></i>
                        การช่วยเหลือหลังส่งต่อ
                    </h2>
                    <div class="observe-modern-subtitle">
                        สถานะต้นทางคงเป็น “ส่งต่อข้อมูล” ส่วนนี้เป็นพื้นที่ทำงานเฉพาะนักสังคมสงเคราะห์ ผู้บริหาร และ Admin
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('observe.referral.report', $observe->id) }}"
                       class="observe-referral-report-btn">
                        <i class="bi bi-file-earmark-text"></i> รายงานการช่วยเหลือ
                    </a>

                    <span class="observe-referral-source-chip">
                        <i class="bi bi-send-check"></i> ส่งต่อข้อมูล
                    </span>
                    @if($latestReferralRound)
                        <span class="observe-status-chip observe-status-{{ $latestReferralRound->status }}">
                            {{ $referralStatusLabels[$latestReferralRound->status] ?? '-' }}
                        </span>
                    @else
                        <span class="observe-referral-pending-chip">รอดำเนินการ</span>
                    @endif

                    @if(!$referralClosed)
                        <button type="button"
                                class="btn-modern btn-modern-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#addReferralRoundModal{{ $observe->id }}">
                            <i class="bi bi-plus-circle"></i>
                            {{ $referralRounds->isEmpty() ? 'เริ่มดำเนินการ' : 'เพิ่มรอบถัดไป' }}
                        </button>
                    @endif
                </div>
            </div>

            @if($referralClosed)
                <div class="observe-referral-complete-banner">
                    <i class="bi bi-check-circle-fill"></i>
                    <div><strong>บรรลุเป้าหมายแล้ว</strong><span>สิ้นสุดการช่วยเหลือหลังส่งต่อ ไม่สามารถเพิ่มรอบใหม่ได้</span></div>
                </div>
            @endif

            <div class="table-wrap">
                <table class="table observe-table observe-modern-table" style="min-width: 1050px;">
                    <thead>
                        <tr>
                            <th>รอบ</th>
                            <th>วันที่ดำเนินการ</th>
                            <th>กระบวนการช่วยเหลือ</th>
                            <th>แนวทางแก้ไข / ผลลัพธ์</th>
                            <th>ระดับความเสี่ยง</th>
                            <th>สถานะ</th>
                            <th>ผู้ดำเนินการ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($referralRounds as $rr)
                            @php $isLatestReferral = $latestReferralRound && $latestReferralRound->id === $rr->id; @endphp
                            <tr>
                                <td><span class="observe-follow-count-chip">รอบที่ {{ $rr->round_no }}</span></td>
                                <td>{{ $thaiDate($rr->action_date) }}</td>
                                <td>{{ $processLabels[$rr->assistance_process] ?? '-' }}</td>
                                <td>
                                    <div class="data-main observe-text-strong">{{ $rr->solution ?: '-' }}</div>
                                    <div class="data-sub observe-text-muted">ผลลัพธ์: {{ $rr->result ?: '-' }}</div>
                                </td>
                                <td>
                                    <span class="observe-risk-chip observe-risk-{{ $rr->risk_level ?? 'none' }}">
                                        {{ $riskLabels[$rr->risk_level ?? 'none'] ?? '-' }}
                                    </span>
                                    @if($rr->risk_detail)<div class="data-sub observe-text-muted mt-1">{{ $rr->risk_detail }}</div>@endif
                                </td>
                                <td>
                                    <span class="observe-status-chip observe-status-{{ $rr->status ?? 'ongoing' }}">
                                        {{ $referralStatusLabels[$rr->status ?? 'ongoing'] ?? '-' }}
                                    </span>
                                    @if(($rr->status ?? 'ongoing') === 'ongoing')
                                        <div class="data-sub observe-text-muted mt-1">นัด: {{ $thaiDate($rr->next_appointment_date) }}</div>
                                    @endif
                                </td>
                                <td>{{ $rr->recorder_name ?: '-' }}</td>
                                <td class="text-center">
                                    @if($isLatestReferral)
                                        <button type="button"
                                                class="btn-action btn-action-warning observe-btn-warning"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editReferralRoundModal{{ $rr->id }}">
                                            <i class="bi bi-pencil-square"></i> แก้ไข
                                        </button>
                                    @else
                                        <span class="observe-case-closed-note"><i class="bi bi-lock-fill"></i> ล็อกประวัติ</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">ยังไม่มีการดำเนินการหลังส่งต่อ</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal เพิ่มรอบหลังส่งต่อ --}}
        @if(!$referralClosed)
            <div class="modal fade observe-modal" id="addReferralRoundModal{{ $observe->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-person-heart"></i> {{ $referralRounds->isEmpty() ? 'เริ่มการช่วยเหลือหลังส่งต่อ' : 'เพิ่มรอบการช่วยเหลือหลังส่งต่อ' }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                        </div>
                        <form action="{{ route('observe.referral.store') }}" method="POST" class="observe-submit-form">
                            @csrf
                            <input type="hidden" name="observe_id" value="{{ $observe->id }}">
                            <div class="modal-body">
                                <div class="observe-referral-access-note"><i class="bi bi-shield-lock-fill"></i> ข้อมูลส่วนนี้จำกัดเฉพาะนักสังคมสงเคราะห์ ผู้บริหาร และ Admin</div>
                                @include('frontend.client.observe.partials._referral_round_fields', [
                                    'referralItem' => null,
                                    'referralBag' => $referralStoreBag,
                                    'referralUseOld' => $hasReferralStoreErrors,
                                    'referralSourceDate' => $referralSourceDate,
                                ])
                            </div>
                            <div class="modal-footer-modern observe-referral-create-footer">
                                <button type="submit" class="btn-form-primary" data-submit-button data-loading-text="กำลังบันทึก...">
                                    <i class="bi bi-save"></i><span data-submit-label>บันทึกการช่วยเหลือ</span>
                                </button>
                                <button type="button" class="btn-form-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> ปิด</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- Modal แก้ไขรอบหลังส่งต่อ: เฉพาะรอบล่าสุด --}}
        @foreach($referralRounds as $rr)
            @if($latestReferralRound && $latestReferralRound->id === $rr->id)
                @php
                    $referralUpdateBag = 'referralUpdate' . $rr->id;
                    $hasReferralUpdateErrors = $errors->getBag($referralUpdateBag)->any();
                @endphp
                <div class="modal fade observe-modal" id="editReferralRoundModal{{ $rr->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> แก้ไขการช่วยเหลือหลังส่งต่อ รอบที่ {{ $rr->round_no }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                            </div>
                            <form id="update-referral-round-{{ $rr->id }}"
                                  action="{{ route('observe.referral.update', $rr->id) }}"
                                  method="POST"
                                  class="observe-submit-form">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    @include('frontend.client.observe.partials._referral_round_fields', [
                                        'referralItem' => $rr,
                                        'referralBag' => $referralUpdateBag,
                                        'referralUseOld' => $hasReferralUpdateErrors,
                                        'referralSourceDate' => $referralSourceDate,
                                    ])
                                </div>
                            </form>

                            <div class="modal-footer-modern observe-referral-edit-footer">
                                <form id="delete-referral-round-{{ $rr->id }}"
                                      action="{{ route('observe.referral.delete', $rr->id) }}"
                                      method="POST"
                                      class="observe-referral-delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btn-form-danger"
                                            onclick="confirmReferralRoundDelete('delete-referral-round-{{ $rr->id }}', 'คุณต้องการลบรอบการช่วยเหลือนี้ใช่หรือไม่')">
                                        <i class="bi bi-trash"></i> ลบรอบนี้
                                    </button>
                                </form>

                                <div class="observe-referral-footer-actions">
                                    <button type="submit"
                                            form="update-referral-round-{{ $rr->id }}"
                                            class="btn-form-warning"
                                            data-referral-submit-button
                                            data-loading-text="กำลังอัปเดต...">
                                        <i class="bi bi-check2-circle"></i><span data-submit-label>อัปเดตข้อมูล</span>
                                    </button>
                                    <button type="button" class="btn-form-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle"></i> ปิด
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif
@endif

<style>
/* Referral modal action bar: เว้นขอบล่างและจัดปุ่มให้เป็นแถบเดียว */
.observe-modal .observe-referral-create-footer,
.observe-modal .observe-referral-edit-footer {
    padding: 16px 22px 20px !important;
    gap: 10px;
    border-top: 1px solid #e5e7eb;
    background: #fff;
}

.observe-modal .observe-referral-edit-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
}

.observe-modal .observe-referral-delete-form {
    margin: 0;
}

.observe-modal .observe-referral-footer-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    margin-left: auto;
}

/* SweetAlert ต้องอยู่เหนือ Bootstrap modal/backdrop เสมอ */
.observe-swal-top {
    z-index: 2147483000 !important;
}

@media (max-width: 767.98px) {
    .observe-modal .observe-referral-create-footer,
    .observe-modal .observe-referral-edit-footer {
        padding: 14px 16px 18px !important;
    }

    .observe-modal .observe-referral-edit-footer {
        align-items: stretch;
    }

    .observe-modal .observe-referral-delete-form,
    .observe-modal .observe-referral-footer-actions {
        width: 100%;
    }

    .observe-modal .observe-referral-footer-actions {
        margin-left: 0;
    }
}

.observe-referral-report-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 11px;border:1px solid #cbd5e1;border-radius:10px;background:#fff;color:#334155;font-size:.82rem;font-weight:800;line-height:1.2;text-decoration:none;white-space:nowrap;transition:.18s ease}
.observe-referral-report-btn:hover{border-color:#94a3b8;background:#f8fafc;color:#0f172a;transform:translateY(-1px)}
.observe-referral-handoff-card{border-color:#fed7aa!important;background:#fffdf9!important}
.observe-referral-handoff-lock{display:flex;gap:14px;align-items:flex-start;padding:22px;color:#9a3412}
.observe-referral-handoff-lock>i{font-size:1.7rem}
.observe-referral-handoff-lock h3{font-size:1rem;font-weight:800;margin:0 0 4px}
.observe-referral-handoff-lock p{margin:0;color:#7c2d12;line-height:1.65}
.observe-referral-source-chip,.observe-referral-pending-chip{display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border-radius:999px;font-size:.78rem;font-weight:800;white-space:nowrap}
.observe-referral-source-chip{background:#fff7ed;color:#c2410c;border:1px solid #fed7aa}
.observe-referral-pending-chip{background:#f8fafc;color:#475569;border:1px solid #cbd5e1}
.observe-referral-complete-banner{display:flex;align-items:center;gap:10px;margin:16px 18px 0;padding:13px 15px;border:1px solid #bbf7d0;border-radius:14px;background:#f0fdf4;color:#166534}
.observe-referral-complete-banner>div{display:flex;flex-direction:column;gap:2px}.observe-referral-complete-banner span{font-size:.84rem;color:#15803d}
.observe-referral-access-note{display:flex;align-items:center;gap:8px;margin-bottom:16px;padding:10px 12px;border-radius:12px;background:#eff6ff;color:#1d4ed8;font-size:.86rem;font-weight:700}
</style>

<script>
/**
 * ยืนยันการลบรอบหลังส่งต่อโดยเฉพาะหน้า Observe
 * แยกจาก confirmDelete ส่วนกลาง เพื่อไม่กระทบหน้าอื่น
 */
window.confirmReferralRoundDelete = function (formId, message) {
    const form = document.getElementById(formId);
    if (!form) return;

    const confirmMessage = message || 'คุณต้องการลบข้อมูลนี้ใช่หรือไม่';

    if (!window.Swal) {
        if (window.confirm(confirmMessage)) {
            form.submit();
        }
        return;
    }

    Swal.fire({
        target: document.body,
        icon: 'warning',
        title: 'ยืนยันการลบ',
        text: confirmMessage,
        showCancelButton: true,
        confirmButtonText: 'ลบข้อมูล',
        cancelButtonText: 'ยกเลิก',
        reverseButtons: true,
        focusCancel: true,
        allowOutsideClick: false,
        heightAuto: false,
        customClass: {
            container: 'observe-swal-top'
        }
    }).then(function (result) {
        if (result.isConfirmed) {
            form.submit();
        }
    });
};

/* ปุ่ม submit ที่อยู่นอก form (อ้างด้วย form=...) ให้สถานะ loading เหมือนปุ่มเดิม */
document.addEventListener('submit', function (event) {
    const form = event.target;
    if (!form || !form.id || !form.id.startsWith('update-referral-round-')) return;

    const button = document.querySelector('[data-referral-submit-button][form="' + form.id + '"]');
    if (!button || button.disabled) return;

    button.disabled = true;
    button.setAttribute('aria-disabled', 'true');

    const label = button.querySelector('[data-submit-label]');
    if (label) {
        label.textContent = button.dataset.loadingText || 'กำลังอัปเดต...';
    }

    const icon = button.querySelector('i');
    if (icon) {
        icon.className = 'spinner-border spinner-border-sm';
        icon.setAttribute('aria-hidden', 'true');
    }
});
</script>

