@extends('admin.admin_master')

@section('title', 'ดำเนินการเคสพฤติกรรมที่ส่งต่อ')

@section('admin')
@php
    $thaiDate = static function ($value): string {
        if (!$value) return '-';
        try {
            $date = $value instanceof \Carbon\CarbonInterface ? $value : \Carbon\Carbon::parse($value);
            return $date->format('d/m/') . ($date->year + 543);
        } catch (\Throwable $e) { return '-'; }
    };
    $client = $observe->client;
    $clientName = $client?->full_name ?: trim(($client?->first_name ?? '') . ' ' . ($client?->last_name ?? ''));
    $referralSource = $observe->followups->filter(fn ($item) => ($item->status ?? 'ongoing') === 'referred')->last();
    $sourceDate = $referralSource?->followup_date ?? $observe->date;
    $rounds = $observe->referralRounds;
    $latestRound = $rounds->last();
    $riskLabels = ['none' => 'ไม่พบความเสี่ยง', 'low' => 'ความเสี่ยงต่ำ', 'moderate' => 'ความเสี่ยงปานกลาง', 'high' => 'ความเสี่ยงสูง'];
    $processLabels = ['group_therapy' => 'กลุ่มบำบัด', 'family_therapy' => 'ครอบครัวบำบัด', 'psychotherapy_counseling' => 'จิตบำบัด/ให้คำปรึกษา', 'behavior_therapy' => 'พฤติกรรมบำบัด', 'referred_treatment' => 'ส่งต่อเข้ารับการบำบัด'];
    $roleLabels = \App\Models\User::roleOptions();
    $referralStoreBag = 'referralStore' . $observe->id;
    $openModal = $errors->getBag($referralStoreBag)->any() ? 'addReferralRoundModal' : null;
    foreach ($rounds as $roundForError) {
        if ($errors->getBag('referralUpdate' . $roundForError->id)->any()) $openModal = 'editReferralRoundModal' . $roundForError->id;
    }
@endphp

<div class="content referral-case-page">
    <div class="container-fluid py-3 py-lg-4">
        <div class="case-toolbar">
            <div>
                <a href="{{ route('observe.referrals.index') }}" class="back-link"><i class="bi bi-arrow-left"></i> กลับศูนย์รับเคส</a>
                <h1>เคส #{{ $observe->id }} · {{ $clientName ?: '-' }}</h1>
                <p>{{ $client?->house?->house_name ?: 'ไม่ระบุบ้าน' }} @if($client?->register_number)<span>· เลขทะเบียน {{ $client->register_number }}</span>@endif</p>
            </div>
            <div class="case-toolbar-actions">
                <a href="{{ route('observe.referral.report', ['id' => $observe->id, 'from' => 'center']) }}" class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i>รายงาน</a>
                @if($closed)<span class="status-pill closed"><i class="bi bi-check-circle"></i> บรรลุเป้าหมาย</span>@else<span class="status-pill active"><i class="bi bi-activity"></i> เปิดดำเนินการ</span>@endif
            </div>
        </div>

        @if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}</div>@endif

        <div class="row g-3 mb-3">
            <div class="col-12 col-xl-8">
                <section class="case-card source-card h-100">
                    <div class="case-card-head"><div><span class="section-kicker">01 · ข้อมูลต้นทาง</span><h2>ปัญหาพฤติกรรมที่ส่งต่อ</h2></div><span class="source-date"><i class="bi bi-send-check"></i> {{ $thaiDate($sourceDate) }}</span></div>
                    <div class="source-grid">
                        <div><span>สภาพปัญหา</span><strong>{{ $observe->misbehavior?->misbehavior_name ?: '-' }}</strong></div>
                        <div><span>วันที่เกิดเหตุ</span><strong>{{ $thaiDate($observe->date) }}</strong></div>
                        <div><span>ผู้บันทึกต้นทาง</span><strong>{{ $observe->recorder ?: '-' }}</strong></div>
                    </div>
                    <div class="narrative"><span>พฤติกรรมที่พบเห็น</span><p>{!! nl2br(e($observe->behavior ?: '-')) !!}</p></div>
                    <div class="narrative"><span>สาเหตุ / ปัจจัยที่เกี่ยวข้อง</span><p>{!! nl2br(e($observe->cause ?: '-')) !!}</p></div>
                    @if($referralSource)<div class="narrative highlighted"><span>ผลการติดตามก่อนส่งต่อ · ครั้งที่ {{ $referralSource->followup_count }}</span><p>{!! nl2br(e($referralSource->followup_result ?: '-')) !!}</p></div>@endif
                </section>
            </div>
            <div class="col-12 col-xl-4">
                <section class="case-card assignment-card h-100">
                    <div class="case-card-head"><div><span class="section-kicker">02 · การรับเคส</span><h2>ผู้รับผิดชอบ</h2></div></div>
                    @if($assignment?->assignee)
                        <div class="assignee-box"><span class="assignee-avatar"><i class="bi bi-person-check"></i></span><div><strong>{{ $assignment->assignee->name }}</strong><span>{{ $roleLabels[$assignment->assignee->role] ?? $assignment->assignee->role }}</span></div></div>
                        <div class="assignment-meta"><span><i class="bi bi-send"></i> มอบหมาย {{ $thaiDate($assignment->assigned_at) }}</span><span class="{{ $assignment->accepted_at ? 'accepted' : 'pending' }}"><i class="bi {{ $assignment->accepted_at ? 'bi-check-circle' : 'bi-hourglass-split' }}"></i> {{ $assignment->accepted_at ? 'รับเคสแล้ว' : 'รอรับเคส' }}</span></div>
                    @else
                        <div class="unassigned-box"><i class="bi bi-inbox"></i><strong>ยังไม่มีผู้รับผิดชอบ</strong><span>ผู้มีสิทธิ์สามารถรับเคส หรือให้ผู้บริหารมอบหมาย</span></div>
                    @endif

                    @if(!$closed)
                        @if($canAssign)
                            <form method="POST" action="{{ route('observe.referrals.assign', $observe->id) }}" class="assignment-form">@csrf @method('PUT')
                                <label class="form-label">มอบหมาย / เปลี่ยนผู้รับผิดชอบ</label>
                                <select name="assigned_to_user_id" class="form-select @error('assigned_to_user_id') is-invalid @enderror"><option value="">— ยังไม่มอบหมาย —</option>@foreach($eligibleUsers as $person)<option value="{{ $person->id }}" @selected((int) $assignment?->assigned_to_user_id === (int) $person->id)>{{ $person->name }} ({{ $roleLabels[$person->role] ?? $person->role }})</option>@endforeach</select>
                                @error('assigned_to_user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <button type="submit" class="btn btn-outline-primary w-100 mt-2"><i class="bi bi-person-plus me-1"></i>บันทึกการมอบหมาย</button>
                            </form>
                        @endif
                        @if($canAccept && !$assignment?->accepted_at)
                            <form method="POST" action="{{ route('observe.referrals.accept', $observe->id) }}" class="mt-2">@csrf<button type="submit" class="btn btn-primary w-100"><i class="bi bi-check2-circle me-1"></i>รับเคสเพื่อดำเนินการ</button></form>
                        @endif
                    @endif
                    @if(!$canManageRounds && !$closed)<div class="read-only-note"><i class="bi bi-info-circle"></i><span>{{ $assignment?->assigned_to_user_id ? 'คุณดูข้อมูลได้ แต่เคสนี้มอบหมายให้ผู้รับผิดชอบรายอื่น' : 'รับเคสก่อนเริ่มบันทึกการช่วยเหลือ' }}</span></div>@endif
                </section>
            </div>
        </div>

        <section class="case-card rounds-card">
            <div class="case-card-head rounds-head"><div><span class="section-kicker">03 · การช่วยเหลือ</span><h2>ลำดับการดำเนินงานหลังส่งต่อ</h2><p>ประวัติเก่าจะถูกล็อก แก้ไขได้เฉพาะรอบล่าสุด</p></div>@if($canManageRounds)<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReferralRoundModal"><i class="bi bi-plus-circle me-1"></i>{{ $rounds->isEmpty() ? 'เริ่มดำเนินการ' : 'เพิ่มรอบถัดไป' }}</button>@endif</div>
            <div class="table-responsive"><table class="table rounds-table align-middle mb-0"><thead><tr><th>รอบ / วันที่</th><th>กระบวนการ</th><th>แนวทางแก้ไข / ผลลัพธ์</th><th>ความเสี่ยง</th><th>สถานะ / นัดหมาย</th><th>ผู้ดำเนินการ</th><th class="text-end">จัดการ</th></tr></thead><tbody>
                @forelse($rounds as $round)
                    @php $isLatest = $latestRound && $latestRound->id === $round->id; @endphp
                    <tr><td><span class="round-number">รอบที่ {{ $round->round_no }}</span><small>{{ $thaiDate($round->action_date) }}</small></td><td><strong>{{ $processLabels[$round->assistance_process] ?? '-' }}</strong></td><td><div class="round-text"><span>แนวทาง</span>{{ $round->solution ?: '-' }}</div><div class="round-text result"><span>ผลลัพธ์</span>{{ $round->result ?: '-' }}</div></td><td><span class="risk-chip risk-{{ $round->risk_level ?? 'none' }}">{{ $riskLabels[$round->risk_level ?? 'none'] ?? '-' }}</span>@if($round->risk_detail)<small class="d-block mt-1">{{ $round->risk_detail }}</small>@endif</td><td>@if(($round->status ?? 'ongoing') === 'goal_met')<span class="status-chip complete"><i class="bi bi-check-circle"></i> บรรลุเป้าหมาย</span>@else<span class="status-chip ongoing"><i class="bi bi-arrow-repeat"></i> ดำเนินการ</span><small class="d-block mt-1">นัด {{ $thaiDate($round->next_appointment_date) }}</small>@endif</td><td>{{ $round->recorder_name ?: '-' }}</td><td class="text-end">@if($isLatest && $canManageRounds)<button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editReferralRoundModal{{ $round->id }}"><i class="bi bi-pencil-square"></i> แก้ไข</button>@else<span class="locked-history"><i class="bi bi-lock"></i> {{ $isLatest ? 'ดูได้อย่างเดียว' : 'ล็อกประวัติ' }}</span>@endif</td></tr>
                @empty<tr><td colspan="7"><div class="rounds-empty"><i class="bi bi-journal-plus"></i><strong>ยังไม่มีการดำเนินการหลังส่งต่อ</strong><span>{{ $canManageRounds ? 'กดเริ่มดำเนินการเพื่อบันทึกรอบการช่วยเหลือ' : 'รอผู้รับผิดชอบรับเคสและเริ่มดำเนินการ' }}</span></div></td></tr>@endforelse
            </tbody></table></div>
        </section>
    </div>
</div>

@if($canManageRounds)
<div class="modal fade referral-modal" id="addReferralRoundModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><div><h5 class="modal-title">{{ $rounds->isEmpty() ? 'เริ่มการช่วยเหลือหลังส่งต่อ' : 'เพิ่มรอบการช่วยเหลือ' }}</h5><small>เคส #{{ $observe->id }} · {{ $clientName }}</small></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form method="POST" action="{{ route('observe.referral.store') }}" class="referral-workflow-form">@csrf<input type="hidden" name="observe_id" value="{{ $observe->id }}"><input type="hidden" name="return_to" value="referral_center"><div class="modal-body">@include('frontend.client.observe.partials._referral_round_fields', ['referralItem' => null, 'referralBag' => $referralStoreBag, 'referralUseOld' => $errors->getBag($referralStoreBag)->any(), 'referralSourceDate' => $sourceDate])</div><div class="modal-footer"><button type="button" class="btn btn-light border" data-bs-dismiss="modal">ยกเลิก</button><button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>บันทึกการช่วยเหลือ</button></div></form></div></div></div>

@if($latestRound)
<div class="modal fade referral-modal" id="editReferralRoundModal{{ $latestRound->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><div><h5 class="modal-title">แก้ไขการช่วยเหลือ รอบที่ {{ $latestRound->round_no }}</h5><small>แก้ไขได้เฉพาะรอบล่าสุด</small></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form id="updateReferralRoundForm" method="POST" action="{{ route('observe.referral.update', $latestRound->id) }}" class="referral-workflow-form">@csrf @method('PUT')<input type="hidden" name="return_to" value="referral_center"><div class="modal-body">@include('frontend.client.observe.partials._referral_round_fields', ['referralItem' => $latestRound, 'referralBag' => 'referralUpdate' . $latestRound->id, 'referralUseOld' => $errors->getBag('referralUpdate' . $latestRound->id)->any(), 'referralSourceDate' => $sourceDate])</div></form><div class="modal-footer justify-content-between"><form id="deleteReferralRoundForm" method="POST" action="{{ route('observe.referral.delete', $latestRound->id) }}">@csrf @method('DELETE')<input type="hidden" name="return_to" value="referral_center"><button type="button" class="btn btn-outline-danger" onclick="confirmReferralDelete()"><i class="bi bi-trash me-1"></i>ลบรอบนี้</button></form><div class="d-flex gap-2"><button type="button" class="btn btn-light border" data-bs-dismiss="modal">ยกเลิก</button><button type="submit" form="updateReferralRoundForm" class="btn btn-warning"><i class="bi bi-check2-circle me-1"></i>อัปเดตข้อมูล</button></div></div></div></div></div>
@endif
@endif
@endsection

@push('styles')
<style>
.referral-case-page{min-height:calc(100vh - 70px);background:#f4f7fb;color:#172033}.case-toolbar{display:flex;align-items:center;justify-content:space-between;gap:18px;margin-bottom:17px}.back-link{display:inline-flex;align-items:center;gap:5px;margin-bottom:7px;color:#4b6588;font-size:.84rem;font-weight:700}.case-toolbar h1{margin:0;font-size:1.55rem;font-weight:800}.case-toolbar p{margin:4px 0 0;color:#6d7b91}.case-toolbar-actions{display:flex;align-items:center;gap:9px;flex-wrap:wrap}.status-pill{display:inline-flex;align-items:center;gap:6px;padding:9px 12px;border-radius:10px;font-size:.82rem;font-weight:800}.status-pill.active{background:#e9f8ef;color:#15803d}.status-pill.closed{background:#eef2f6;color:#475569}.case-card{padding:20px;border:1px solid #e0e7f0;border-radius:18px;background:#fff;box-shadow:0 7px 22px rgba(26,45,75,.05)}.case-card-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:16px}.section-kicker{display:block;margin-bottom:3px;color:#2867ad;font-size:.75rem;font-weight:800}.case-card-head h2{margin:0;color:#172033;font-size:1.08rem;font-weight:800}.case-card-head p{margin:4px 0 0;color:#728097;font-size:.82rem}.source-date{display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border-radius:999px;background:#fff3e8;color:#c2510c;font-size:.78rem;font-weight:800}.source-grid{display:grid;grid-template-columns:2fr 1fr 1fr;gap:10px;margin-bottom:13px}.source-grid>div{padding:12px;border:1px solid #e7ecf3;border-radius:12px;background:#fafbfd}.source-grid span,.narrative>span{display:block;margin-bottom:4px;color:#718096;font-size:.75rem;font-weight:700}.source-grid strong{font-size:.9rem}.narrative{padding:12px 0;border-top:1px solid #edf1f6}.narrative p{margin:0;color:#334155;line-height:1.65}.narrative.highlighted{padding:13px;border:1px solid #d7e8ff;border-radius:12px;background:#f5f9ff}.assignee-box{display:flex;align-items:center;gap:12px;padding:14px;border-radius:14px;background:#f2f7ff}.assignee-avatar{display:flex;width:44px;height:44px;align-items:center;justify-content:center;border-radius:12px;background:#dceaff;color:#2364b7;font-size:1.2rem}.assignee-box strong,.assignee-box span{display:block}.assignee-box span{margin-top:2px;color:#718096;font-size:.78rem}.assignment-meta{display:grid;gap:7px;padding:12px 2px;color:#64748b;font-size:.8rem}.assignment-meta span{display:flex;align-items:center;gap:6px}.assignment-meta .accepted{color:#15803d}.assignment-meta .pending{color:#b45309}.unassigned-box{display:flex;align-items:center;justify-content:center;min-height:135px;flex-direction:column;gap:5px;padding:16px;border:1px dashed #bdc9d9;border-radius:14px;color:#738197;text-align:center}.unassigned-box i{font-size:1.8rem;color:#4178bd}.unassigned-box strong{color:#334155}.unassigned-box span{font-size:.78rem}.assignment-form{padding-top:12px;border-top:1px solid #edf1f6}.assignment-form .form-label{font-size:.78rem;font-weight:700}.read-only-note{display:flex;gap:8px;margin-top:12px;padding:10px;border-radius:10px;background:#f5f7fa;color:#627086;font-size:.78rem}.rounds-card{padding:0;overflow:hidden}.rounds-head{align-items:center;margin:0;padding:18px 20px}.rounds-table{min-width:1140px}.rounds-table thead th{padding:12px 14px;background:#f7f9fc;color:#526077;border-color:#e3e9f1;font-size:.77rem;font-weight:800}.rounds-table td{padding:14px;border-color:#edf1f6}.round-number{display:block;color:#225da3;font-weight:800}.rounds-table small{color:#718096}.round-text{max-width:280px;color:#334155;font-size:.82rem;line-height:1.45}.round-text span{margin-right:5px;color:#245f9f;font-weight:800}.round-text.result{margin-top:5px}.risk-chip,.status-chip{display:inline-flex;align-items:center;gap:5px;padding:5px 9px;border-radius:999px;font-size:.74rem;font-weight:800}.risk-none{background:#f1f5f9;color:#64748b}.risk-low{background:#ecfdf3;color:#15803d}.risk-moderate{background:#fff7df;color:#b45309}.risk-high{background:#feecec;color:#c81e1e}.status-chip.ongoing{background:#e9f8ef;color:#15803d}.status-chip.complete{background:#eef2f6;color:#475569}.locked-history{color:#8793a5;font-size:.75rem}.rounds-empty{display:flex;min-height:230px;align-items:center;justify-content:center;flex-direction:column;gap:6px;color:#78869a;text-align:center}.rounds-empty i{font-size:2rem;color:#9aabc0}.rounds-empty strong{color:#3d4b60}.referral-modal .modal-content{border:0;border-radius:18px}.referral-modal .modal-header{padding:17px 20px;background:#f7faff}.referral-modal .modal-title{font-weight:800}.referral-modal .modal-header small{color:#718096}.referral-modal .modal-body{padding:20px}.referral-modal .modal-footer{padding:14px 20px}.referral-workflow-form .form-label-modern{display:block;margin-bottom:6px;color:#445167;font-size:.82rem;font-weight:700}.referral-workflow-form .form-control-modern,.referral-workflow-form .form-select-modern{min-height:44px;border-color:#d9e1ec;border-radius:10px}.referral-workflow-form textarea.form-control-modern{min-height:auto}.referral-workflow-form .text-muted{font-size:.76rem}
@media(max-width:991.98px){.case-toolbar{align-items:flex-start;flex-direction:column}.source-grid{grid-template-columns:1fr 1fr}}@media(max-width:575.98px){.case-toolbar h1{font-size:1.3rem}.case-toolbar-actions,.case-toolbar-actions .btn{width:100%}.source-grid{grid-template-columns:1fr}.case-card{padding:16px}.case-card-head{align-items:flex-start;flex-direction:column}.rounds-head .btn{width:100%}}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.referral-workflow-form').forEach(function (form) {
        const risk = form.querySelector('.js-referral-risk-level');
        const riskWrap = form.querySelector('.js-referral-risk-detail-wrap');
        const riskDetail = form.querySelector('.js-referral-risk-detail');
        const riskRequired = form.querySelector('.js-referral-risk-required');
        const status = form.querySelector('.js-referral-status');
        const nextWrap = form.querySelector('.js-referral-next-wrap');
        const nextDate = form.querySelector('.js-referral-next-date');
        const focus = form.querySelector('.js-referral-followup-focus');

        function syncRisk() {
            if (!risk || !riskWrap) return;
            const visible = risk.value !== 'none';
            riskWrap.style.display = visible ? '' : 'none';
            if (riskRequired) riskRequired.style.display = ['moderate', 'high'].includes(risk.value) ? '' : 'none';
            if (riskDetail) riskDetail.required = ['moderate', 'high'].includes(risk.value);
        }
        function syncStatus() {
            if (!status || !nextWrap) return;
            const ongoing = status.value === 'ongoing';
            nextWrap.style.display = ongoing ? '' : 'none';
            if (nextDate) nextDate.required = ongoing;
            if (focus) focus.required = ongoing;
        }
        if (risk) risk.addEventListener('change', syncRisk);
        if (status) status.addEventListener('change', syncStatus);
        syncRisk(); syncStatus();
    });

    @if($openModal)
    const modalElement = document.getElementById(@json($openModal));
    if (modalElement && window.bootstrap) bootstrap.Modal.getOrCreateInstance(modalElement).show();
    @endif
});

window.confirmReferralDelete = function () {
    const form = document.getElementById('deleteReferralRoundForm');
    if (!form) return;
    if (!window.Swal) {
        if (window.confirm('ยืนยันลบรอบการช่วยเหลือล่าสุด?')) form.submit();
        return;
    }
    Swal.fire({icon:'warning',title:'ยืนยันการลบ',text:'ลบได้เฉพาะรอบล่าสุด ข้อมูลที่ลบไม่สามารถเรียกคืนได้',showCancelButton:true,confirmButtonText:'ลบข้อมูล',cancelButtonText:'ยกเลิก',reverseButtons:true}).then(function(result){if(result.isConfirmed) form.submit();});
};
</script>
@endpush
