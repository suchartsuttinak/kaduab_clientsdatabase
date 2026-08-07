<div class="followup-hero mb-4">
    <div class="row align-items-center g-3">
        <div class="col-lg-8">
            <div class="hero-kicker">Education Follow-up</div>
            <h1 class="hero-title mb-2">ติดตามผลการเรียน</h1>
            <p class="hero-subtitle mb-0">
                จัดเก็บข้อมูลการติดตามผลการเรียนอย่างเป็นระบบ รองรับการบันทึก แก้ไข ตรวจสอบ และออกรายงานได้อย่างมีประสิทธิภาพ
            </p>
        </div>
       @if($followups->count())
            <div class="col-lg-4 text-lg-end">
                <button type="button"
                        class="btn btn-primary btn-action shadow-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#followupModal">
                    <i class="bi bi-plus-circle-fill me-2"></i>
                    เพิ่มการติดตามใหม่
                </button>
            </div>
            @endif
    </div>
</div>