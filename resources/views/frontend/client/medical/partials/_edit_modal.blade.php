<div class="modal fade md-modal" id="editMedicalModal" tabindex="-1" aria-labelledby="editMedicalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="editMedicalForm"
                  method="POST"
                  action="{{ route('medical.update', 0) }}"
                  novalidate
                  data-permission-keep>
                <input type="hidden" name="_form_context" value="medical_edit">
                @csrf
                @method('PUT')
                <input type="hidden" name="client_id" id="edit_client_id">
                <input type="hidden" name="id" id="edit_medical_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="editMedicalModalLabel">
                        <i class="bi {{ $canMedicalUpdate ? 'bi-pencil-square' : 'bi-eye' }} me-2"></i>
                        {{ $canMedicalUpdate ? 'แก้ไขข้อมูลการรักษาพยาบาล' : 'ดูข้อมูลการรักษาพยาบาล' }}
                        @unless($canMedicalUpdate)
                            <span class="md-readonly-badge">อ่านอย่างเดียว</span>
                        @endunless
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด" data-permission-keep></button>
                </div>

                <div class="modal-body">
                    <div class="md-form-section">
                        <h6 class="md-section-title"><i class="bi bi-heart-pulse"></i>ข้อมูลการรักษา</h6>
                        <div class="md-form-grid">
                            <div class="md-col-4">
                                <label class="md-form-label" for="edit_medical_date">
                                    <i class="bi bi-calendar-event"></i>วันที่รักษา <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       name="medical_date"
                                       id="edit_medical_date"
                                       class="form-control md-form-control"
                                       max="{{ now('Asia/Bangkok')->toDateString() }}"
                                       required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="md-col-8">
                                <label class="md-form-label" for="edit_disease_name">
                                    <i class="bi bi-activity"></i>ชื่อโรค <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="disease_name"
                                       id="edit_disease_name"
                                       class="form-control md-form-control"
                                       maxlength="255"
                                       required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="md-col-6">
                                <label class="md-form-label" for="edit_illness">
                                    <i class="bi bi-thermometer-half"></i>อาการป่วย <span class="text-danger">*</span>
                                </label>
                                <textarea name="illness"
                                          id="edit_illness"
                                          class="form-control md-form-control"
                                          rows="4"
                                          maxlength="3000"
                                          required></textarea>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="md-col-6">
                                <label class="md-form-label" for="edit_treatment">
                                    <i class="bi bi-bandaid"></i>การรักษา / การปฐมพยาบาล
                                </label>
                                <textarea name="treatment"
                                          id="edit_treatment"
                                          class="form-control md-form-control"
                                          rows="4"
                                          maxlength="3000"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="md-form-section">
                        <h6 class="md-section-title"><i class="bi bi-hospital"></i>การพบแพทย์</h6>
                        <div class="md-radio-group">
                            <label class="md-radio-option" for="edit_refer_yes">
                                <input class="form-check-input"
                                       type="radio"
                                       name="refer"
                                       id="edit_refer_yes"
                                       value="พบแพทย์"
                                       required>
                                <span><i class="bi bi-check-circle"></i>พบแพทย์</span>
                            </label>

                            <label class="md-radio-option" for="edit_refer_no">
                                <input class="form-check-input"
                                       type="radio"
                                       name="refer"
                                       id="edit_refer_no"
                                       value="ไม่พบแพทย์">
                                <span><i class="bi bi-dash-circle"></i>ไม่พบแพทย์</span>
                            </label>
                        </div>
                        <div class="invalid-feedback d-block"></div>

                        <div id="edit_medical_section" class="md-conditional mt-3" style="display:none;">
                            <div class="md-form-grid">
                                <div class="md-col-8">
                                    <label class="md-form-label" for="edit_diagnosis">
                                        <i class="bi bi-clipboard2-pulse"></i>การวินิจฉัยของแพทย์ <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="diagnosis"
                                              id="edit_diagnosis"
                                              class="form-control md-form-control"
                                              rows="3"
                                              maxlength="3000"></textarea>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="md-col-4">
                                    <label class="md-form-label" for="edit_appt_date">
                                        <i class="bi bi-calendar2-check"></i>วันที่แพทย์นัด
                                    </label>
                                    <input type="date"
                                           name="appt_date"
                                           id="edit_appt_date"
                                           class="form-control md-form-control">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="md-form-section mb-0">
                        <h6 class="md-section-title"><i class="bi bi-info-circle"></i>ข้อมูลเพิ่มเติม</h6>
                        <div class="md-form-grid">
                            <div class="md-col-5">
                                <label class="md-form-label" for="edit_teacher">
                                    <i class="bi bi-person-check"></i>ผู้ดูแล
                                </label>
                                <input type="text"
                                       name="teacher"
                                       id="edit_teacher"
                                       class="form-control md-form-control"
                                       maxlength="255">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="md-col-7">
                                <label class="md-form-label" for="edit_remark">
                                    <i class="bi bi-chat-left-text"></i>หมายเหตุ
                                </label>
                                <textarea name="remark"
                                          id="edit_remark"
                                          class="form-control md-form-control"
                                          rows="3"
                                          maxlength="3000"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary md-btn" data-bs-dismiss="modal" data-permission-keep>
                        <i class="bi bi-x-lg"></i>
                        <span>ปิด</span>
                    </button>
                    @if($canMedicalUpdate)
                        <button type="submit" class="btn btn-warning md-btn" data-permission-action="update">
                            <i class="bi bi-check2-circle"></i>
                            <span>บันทึกการแก้ไข</span>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
