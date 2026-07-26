<div class="modal fade" id="editFollowupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-sm-down followup-mobile-dialog">
        <div class="modal-content border-0 shadow-lg custom-modal followup-mobile-content">
            <div class="modal-header modal-header-warning">
                <div>
                    <h5 class="modal-title fw-bold mb-1">
                        <i class="bi bi-pencil-square me-2"></i>แก้ไขข้อมูลการติดตาม
                    </h5>
                    <div class="modal-subtitle text-dark opacity-75">
                        ปรับปรุงข้อมูลการติดตามผลการเรียนให้ถูกต้องและเป็นปัจจุบัน
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="edit-followup-form" method="POST" action="{{ route('school_followup.update', 0) }}">
    @csrf
    @method('PUT')

    <input type="hidden" id="edit_followup_id" name="followup_id" value="">
    <input type="hidden" name="client_id" value="{{ $client->id }}">
    <input type="hidden" name="education_record_id" id="edit_education_record_id" value="">

                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-4">
    <div class="form-side-card h-100">
        <div class="form-side-header">
            <i class="bi bi-person-lines-fill me-2"></i>ข้อมูลผู้รับบริการ
        </div>

        <div class="form-side-body">
            <div class="side-info-item">
                <span class="side-info-label">ชื่อ - นามสกุล</span>
                <span class="side-info-value">{{ $clientName }}</span>
            </div>

            <div class="side-info-item">
                <span class="side-info-label">อายุ</span>
                <span class="side-info-value">{{ $clientAge }} ปี</span>
            </div>

            <div class="side-info-item">
                <span class="side-info-label">สถานศึกษา</span>
                <span class="side-info-value" id="edit_school_name">-</span>
            </div>

            <div class="side-info-item">
                <span class="side-info-label">ระดับชั้น</span>
                <span class="side-info-value" id="edit_education_name">-</span>
            </div>

            <div class="side-info-item">
                <span class="side-info-label">ภาคเรียน</span>
                <span class="side-info-value" id="edit_semester_name">-</span>
            </div>

            <div class="side-info-item">
                <span class="side-info-label">ปีการศึกษา</span>
                <span class="side-info-value" id="edit_academic_year">-</span>
            </div>
        </div>
    </div>
</div>

                        <div class="col-lg-8">
                            <div class="form-main-card h-100">
                                <div class="form-side-header">
                                    <i class="bi bi-pencil-square me-2"></i>ข้อมูลสำหรับแก้ไข
                                </div>

                                <div class="form-side-body">
                                    <div class="row g-3">
                                       <div class="col-md-4">
                                                <label class="form-label form-label-modern">
                                                    วันที่ติดตาม <span class="text-danger">*</span>
                                                </label>

                                                <input type="date"
                                                    id="edit_follow_date"
                                                    name="follow_date"
                                                    class="form-control form-control-modern"
                                                    max="{{ now('Asia/Bangkok')->toDateString() }}"
                                                    required>

                                                <div class="invalid-feedback"></div>
                                            </div>
                                        <div class="col-md-4">
                                            <label class="form-label form-label-modern">ครูประจำชั้น</label>
                                            <input type="text" id="edit_teacher_name" name="teacher_name" class="form-control form-control-modern">
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label form-label-modern">โทรศัพท์</label>
                                            <input type="text" id="edit_tel" name="tel" class="form-control form-control-modern">
                                            <div class="invalid-feedback"></div>
                                        </div>

                                    <div class="col-12">
                                                <label class="form-label form-label-modern d-block">
                                                    การดำเนินงาน <span class="text-danger">*</span>
                                                </label>

                                                <div class="follow-type-group">
                                                    <label class="custom-radio-card" for="edit_follow_self">
                                                        <input class="form-check-input" type="radio" name="follow_type" id="edit_follow_self" value="self">
                                                        <span class="custom-radio-text">ติดตามด้วยตนเอง</span>
                                                    </label>

                                                    <label class="custom-radio-card" for="edit_follow_phone">
                                                        <input class="form-check-input" type="radio" name="follow_type" id="edit_follow_phone" value="phone">
                                                        <span class="custom-radio-text">โทรศัพท์</span>
                                                    </label>

                                                    <label class="custom-radio-card" for="edit_follow_other">
                                                        <input class="form-check-input" type="radio" name="follow_type" id="edit_follow_other" value="other">
                                                        <span class="custom-radio-text">อื่น ๆ</span>
                                                    </label>
                                                </div>

                                                <div class="invalid-feedback d-block"></div>
                                            </div>

                                        <div class="col-md-6">
                                            <label class="form-label form-label-modern">ผลการติดตาม</label>
                                            <textarea id="edit_result" name="result" class="form-control form-control-modern" rows="4"></textarea>
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label form-label-modern">หมายเหตุ</label>
                                            <textarea id="edit_remark" name="remark" class="form-control form-control-modern" rows="4"></textarea>
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label form-label-modern">ชื่อผู้ติดตาม</label>
                                            <input type="text" id="edit_contact_name" name="contact_name" class="form-control form-control-modern">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-4 py-3 border-0">
                    <button type="button" class="btn btn-light btn-cancel" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-warning btn-save text-dark">
                        <i class="bi bi-check2-circle me-2"></i>อัปเดตข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

