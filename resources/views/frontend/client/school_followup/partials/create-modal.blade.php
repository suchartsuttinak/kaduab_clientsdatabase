<div class="modal fade sf-modal" id="followupModal" tabindex="-1" aria-labelledby="followupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable followup-mobile-dialog">
        <div class="modal-content custom-modal followup-mobile-content">
            <form id="create-followup-form" method="POST" action="{{ route('school_followup_store') }}">
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="education_record_id" value="{{ $educationRecord->id ?? '' }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="followupModalLabel">
                        <i class="bi bi-journal-plus me-2"></i>เพิ่มข้อมูลติดตามผลการเรียน
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>

                <div class="modal-body">
                    <div class="sf-modal-context">
                        <div class="sf-modal-context-item">
                            <span class="sf-modal-context-label"><i class="bi bi-building"></i>สถานศึกษา</span>
                            <span class="sf-modal-context-value">{{ $schoolName }}</span>
                        </div>
                        <div class="sf-modal-context-item">
                            <span class="sf-modal-context-label"><i class="bi bi-book"></i>ระดับชั้น</span>
                            <span class="sf-modal-context-value">{{ $educationName }}</span>
                        </div>
                        <div class="sf-modal-context-item">
                            <span class="sf-modal-context-label"><i class="bi bi-calendar3"></i>ภาคเรียน</span>
                            <span class="sf-modal-context-value">{{ $semesterName }}</span>
                        </div>
                    </div>

                    <div class="sf-form-grid">
                        <div class="sf-col-4">
                            <label class="form-label form-label-modern sf-form-label" for="follow_date">
                                <i class="bi bi-calendar-event"></i>วันที่ติดตาม <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   id="follow_date"
                                   name="follow_date"
                                   class="form-control form-control-modern sf-form-control @error('follow_date') is-invalid @enderror"
                                   value="{{ old('follow_date', now('Asia/Bangkok')->toDateString()) }}"
                                   max="{{ now('Asia/Bangkok')->toDateString() }}"
                                   required>
                            @error('follow_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sf-col-4">
                            <label class="form-label form-label-modern sf-form-label" for="teacher_name"><i class="bi bi-person-badge"></i>ครูประจำชั้น</label>
                            <input type="text"
                                   id="teacher_name"
                                   name="teacher_name"
                                   class="form-control form-control-modern sf-form-control @error('teacher_name') is-invalid @enderror"
                                   value="{{ old('teacher_name') }}"
                                   placeholder="ระบุชื่อครูประจำชั้น">
                            @error('teacher_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sf-col-4">
                            <label class="form-label form-label-modern sf-form-label" for="tel"><i class="bi bi-telephone"></i>โทรศัพท์</label>
                            <input type="text"
                                   id="tel"
                                   name="tel"
                                   class="form-control form-control-modern sf-form-control @error('tel') is-invalid @enderror"
                                   value="{{ old('tel') }}"
                                   placeholder="ระบุหมายเลขโทรศัพท์">
                            @error('tel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sf-col-12">
                            <label class="form-label form-label-modern sf-form-label d-block">
                                <i class="bi bi-diagram-3"></i>การดำเนินงาน <span class="text-danger">*</span>
                            </label>
                            <div class="follow-type-group sf-radio-group">
                                <label class="custom-radio-card sf-radio-option" for="follow_type_self">
                                    <input class="form-check-input" type="radio" name="follow_type" id="follow_type_self" value="self" {{ old('follow_type') === 'self' ? 'checked' : '' }}>
                                    <span class="custom-radio-text"><i class="bi bi-person-walking"></i>ติดตามด้วยตนเอง</span>
                                </label>
                                <label class="custom-radio-card sf-radio-option" for="follow_type_phone">
                                    <input class="form-check-input" type="radio" name="follow_type" id="follow_type_phone" value="phone" {{ old('follow_type') === 'phone' ? 'checked' : '' }}>
                                    <span class="custom-radio-text"><i class="bi bi-telephone"></i>โทรศัพท์</span>
                                </label>
                                <label class="custom-radio-card sf-radio-option" for="follow_type_other">
                                    <input class="form-check-input" type="radio" name="follow_type" id="follow_type_other" value="other" {{ old('follow_type') === 'other' ? 'checked' : '' }}>
                                    <span class="custom-radio-text"><i class="bi bi-three-dots"></i>อื่น ๆ</span>
                                </label>
                            </div>
                            @error('follow_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sf-col-6">
                            <label class="form-label form-label-modern sf-form-label" for="result"><i class="bi bi-clipboard-check"></i>ผลการติดตาม</label>
                            <textarea id="result"
                                      name="result"
                                      class="form-control form-control-modern sf-form-control @error('result') is-invalid @enderror"
                                      rows="4"
                                      placeholder="สรุปผลการติดตาม">{{ old('result') }}</textarea>
                            @error('result')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sf-col-6">
                            <label class="form-label form-label-modern sf-form-label" for="remark"><i class="bi bi-chat-left-text"></i>หมายเหตุ</label>
                            <textarea id="remark"
                                      name="remark"
                                      class="form-control form-control-modern sf-form-control @error('remark') is-invalid @enderror"
                                      rows="4"
                                      placeholder="ระบุข้อมูลเพิ่มเติม">{{ old('remark') }}</textarea>
                            @error('remark')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sf-col-6">
                            <label class="form-label form-label-modern sf-form-label" for="contact_name"><i class="bi bi-person-check"></i>ชื่อผู้ติดตาม</label>
                            <input type="text"
                                   id="contact_name"
                                   name="contact_name"
                                   class="form-control form-control-modern sf-form-control @error('contact_name') is-invalid @enderror"
                                   value="{{ old('contact_name') }}"
                                   placeholder="ระบุชื่อผู้ติดตาม">
                            @error('contact_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary sf-btn btn-cancel" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i>
                        <span>ปิด</span>
                    </button>
                    <button type="submit" class="btn btn-primary sf-btn btn-save">
                        <i class="bi bi-check-circle"></i>
                        <span>บันทึกข้อมูล</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
