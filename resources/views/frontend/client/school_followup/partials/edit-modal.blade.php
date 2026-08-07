<div class="modal fade sf-modal" id="editFollowupModal" tabindex="-1" aria-labelledby="editFollowupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable followup-mobile-dialog">
        <div class="modal-content custom-modal followup-mobile-content {{ $canSchoolUpdate ? '' : 'sf-modal-readonly' }}">
            <form id="edit-followup-form" method="POST" action="{{ route('school_followup.update', 0) }}" data-permission-keep>
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_followup_id" name="followup_id" value="">
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="education_record_id" id="edit_education_record_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title" id="editFollowupModalLabel">
                        <i class="bi {{ $canSchoolUpdate ? 'bi-pencil-square' : 'bi-eye' }} me-2"></i>
                        {{ $canSchoolUpdate ? 'แก้ไขข้อมูลติดตามผลการเรียน' : 'ดูข้อมูลติดตามผลการเรียน' }}
                        @unless($canSchoolUpdate)
                            <span class="sf-readonly-badge">อ่านอย่างเดียว</span>
                        @endunless
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด" data-permission-keep></button>
                </div>

                <div class="modal-body">
                    <div class="sf-modal-context">
                        <div class="sf-modal-context-item">
                            <span class="sf-modal-context-label"><i class="bi bi-building"></i>สถานศึกษา</span>
                            <span class="sf-modal-context-value" id="edit_school_name">-</span>
                        </div>
                        <div class="sf-modal-context-item">
                            <span class="sf-modal-context-label"><i class="bi bi-book"></i>ระดับชั้น</span>
                            <span class="sf-modal-context-value" id="edit_education_name">-</span>
                        </div>
                        <div class="sf-modal-context-item">
                            <span class="sf-modal-context-label"><i class="bi bi-calendar3"></i>ภาคเรียน</span>
                            <span class="sf-modal-context-value" id="edit_semester_name">-</span>
                        </div>
                    </div>

                    <div class="sf-form-grid">
                        <div class="sf-col-4">
                            <label class="form-label form-label-modern sf-form-label" for="edit_follow_date">
                                <i class="bi bi-calendar-event"></i>วันที่ติดตาม <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   id="edit_follow_date"
                                   name="follow_date"
                                   class="form-control form-control-modern sf-form-control"
                                   max="{{ now('Asia/Bangkok')->toDateString() }}"
                                   required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="sf-col-4">
                            <label class="form-label form-label-modern sf-form-label" for="edit_teacher_name"><i class="bi bi-person-badge"></i>ครูประจำชั้น</label>
                            <input type="text" id="edit_teacher_name" name="teacher_name" class="form-control form-control-modern sf-form-control">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="sf-col-4">
                            <label class="form-label form-label-modern sf-form-label" for="edit_tel"><i class="bi bi-telephone"></i>โทรศัพท์</label>
                            <input type="text" id="edit_tel" name="tel" class="form-control form-control-modern sf-form-control">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="sf-col-12">
                            <label class="form-label form-label-modern sf-form-label d-block">
                                <i class="bi bi-diagram-3"></i>การดำเนินงาน <span class="text-danger">*</span>
                            </label>
                            <div class="follow-type-group sf-radio-group"
                                 data-permission-keep>
                                <label class="custom-radio-card sf-radio-option" for="edit_follow_self">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="follow_type"
                                           id="edit_follow_self"
                                           value="self">
                                    <span class="custom-radio-text">
                                        <i class="bi bi-person-walking"></i>
                                        ติดตามด้วยตนเอง
                                    </span>
                                </label>

                                <label class="custom-radio-card sf-radio-option" for="edit_follow_phone">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="follow_type"
                                           id="edit_follow_phone"
                                           value="phone">
                                    <span class="custom-radio-text">
                                        <i class="bi bi-telephone"></i>
                                        โทรศัพท์
                                    </span>
                                </label>

                                <label class="custom-radio-card sf-radio-option" for="edit_follow_other">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="follow_type"
                                           id="edit_follow_other"
                                           value="other">
                                    <span class="custom-radio-text">
                                        <i class="bi bi-three-dots"></i>
                                        อื่น ๆ
                                    </span>
                                </label>
                            </div>
                            <div class="invalid-feedback d-block"></div>
                        </div>

                        <div class="sf-col-6">
                            <label class="form-label form-label-modern sf-form-label" for="edit_result"><i class="bi bi-clipboard-check"></i>ผลการติดตาม</label>
                            <textarea id="edit_result" name="result" class="form-control form-control-modern sf-form-control" rows="4"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="sf-col-6">
                            <label class="form-label form-label-modern sf-form-label" for="edit_remark"><i class="bi bi-chat-left-text"></i>หมายเหตุ</label>
                            <textarea id="edit_remark" name="remark" class="form-control form-control-modern sf-form-control" rows="4"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="sf-col-6">
                            <label class="form-label form-label-modern sf-form-label" for="edit_contact_name"><i class="bi bi-person-check"></i>ชื่อผู้ติดตาม</label>
                            <input type="text" id="edit_contact_name" name="contact_name" class="form-control form-control-modern sf-form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary sf-btn btn-cancel" data-bs-dismiss="modal" data-permission-keep>
                        <i class="bi bi-x-lg"></i>
                        <span>ปิด</span>
                    </button>
                    @if($canSchoolUpdate)
                        <button type="submit" class="btn btn-warning sf-btn btn-save" data-permission-action="update">
                            <i class="bi bi-check2-circle"></i>
                            <span>บันทึกการแก้ไข</span>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
