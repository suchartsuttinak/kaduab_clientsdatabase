@php
    $minCloseDate = optional($counseling->last_activity_date)->format('Y-m-d') ?: optional($counseling->session_date)->format('Y-m-d');
@endphp
<div class="modal fade cs-modal" id="counselingCloseModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"><div class="modal-content">
<form action="{{ route('counseling.close',$counseling->id) }}" method="POST" id="counselingCloseForm">
@csrf
<input type="hidden" name="_form_context" value="close">
<div class="modal-header"><h5 class="modal-title"><i class="bi bi-check2-circle me-1 text-success"></i> จบการให้คำปรึกษา ครั้งที่ {{ $counseling->session_no }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
    <div class="alert alert-success py-2 small"><strong>เมื่อจบครั้งนี้</strong> ระบบจะสรุปกระบวนการตั้งแต่วันเริ่มจนถึงวันนี้ และจึงอนุญาตให้เปิด “ครั้งถัดไป” ได้</div>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">วันที่จบ <span class="cs-required">*</span></label><input type="date" name="closed_date" class="form-control" min="{{ $minCloseDate }}" max="{{ now('Asia/Bangkok')->format('Y-m-d') }}" value="{{ old('closed_date',now('Asia/Bangkok')->format('Y-m-d')) }}" required></div>
        <div class="col-md-6"><label class="form-label">ลักษณะการจบ <span class="cs-required">*</span></label><select name="closure_type" class="form-select" required>
            <option value="completed" @selected(old('closure_type','completed')==='completed')>จบกระบวนการตามแผน</option>
            <option value="goal_met" @selected(old('closure_type')==='goal_met')>บรรลุเป้าหมาย</option>
            <option value="referred" @selected(old('closure_type')==='referred')>ส่งต่อหน่วยงาน/ผู้เชี่ยวชาญ</option>
            <option value="discontinued" @selected(old('closure_type')==='discontinued')>ยุติด้วยเหตุอื่น</option>
        </select></div>
        <div class="col-md-6"><label class="form-label">การบรรลุเป้าหมาย <span class="cs-required">*</span></label><select name="goal_achievement" class="form-select" required>
            <option value="achieved" @selected(old('goal_achievement')==='achieved')>บรรลุเป้าหมาย</option>
            <option value="partial" @selected(old('goal_achievement','partial')==='partial')>บรรลุบางส่วน</option>
            <option value="not_achieved" @selected(old('goal_achievement')==='not_achieved')>ยังไม่บรรลุ</option>
            <option value="not_applicable" @selected(old('goal_achievement')==='not_applicable')>ไม่สามารถประเมินได้/ไม่เกี่ยวข้อง</option>
        </select></div>
        <div class="col-md-6"><label class="form-label">ผู้จบกระบวนการ</label><input type="text" class="form-control cs-readonly" value="{{ auth()->user()->name ?? '-' }}" readonly></div>
        <div class="col-12"><label class="form-label">สรุปผลการให้คำปรึกษาตลอดครั้งนี้ <span class="cs-required">*</span></label><textarea name="closure_summary" class="form-control" rows="4" required placeholder="สรุปสิ่งที่ดำเนินการ การเปลี่ยนแปลง และผลโดยรวม">{{ old('closure_summary') }}</textarea></div>
        <div class="col-12"><label class="form-label">แนวทาง / ข้อเสนอแนะหลังจบ</label><textarea name="final_recommendation" class="form-control" rows="3">{{ old('final_recommendation') }}</textarea></div>
        <div class="col-12"><label class="form-label">หมายเหตุการจบ</label><textarea name="closure_note" class="form-control" rows="2">{{ old('closure_note') }}</textarea></div>
    </div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">ยกเลิก</button><button type="submit" class="cs-btn-success js-submit-once"><i class="bi bi-check-circle"></i> ยืนยันจบครั้งนี้</button></div>
</form>
</div></div></div>
