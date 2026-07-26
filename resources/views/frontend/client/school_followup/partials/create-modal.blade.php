<div class="modal fade" id="followupModal" tabindex="-1" aria-labelledby="followupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-sm-down followup-mobile-dialog">
        <div class="modal-content border-0 shadow-lg custom-modal followup-mobile-content">
            <div class="modal-header modal-header-primary">
                <div>
                    <h5 class="modal-title fw-bold mb-1" id="followupModalLabel">
                        <i class="bi bi-clipboard-check me-2"></i>แบบฟอร์มการติดตามผลการเรียน
                    </h5>
                    <div class="modal-subtitle">บันทึกรายละเอียดการติดตามผลการเรียนอย่างครบถ้วนและเป็นระบบ</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="create-followup-form" method="POST" action="{{ route('school_followup_store') }}">
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="education_record_id" value="{{ $educationRecord->id ?? '' }}">

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
                                        <span class="side-info-value">{{ $schoolName }}</span>
                                    </div>
                                    <div class="side-info-item">
                                        <span class="side-info-label">ระดับชั้น</span>
                                        <span class="side-info-value">{{ $educationName }}</span>
                                    </div>
                                    <div class="side-info-item">
                                        <span class="side-info-label">ภาคเรียน</span>
                                        <span class="side-info-value">{{ $semesterName }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="form-main-card h-100">
                                <div class="form-side-header">
                                    <i class="bi bi-journal-check me-2"></i>ข้อมูลการติดตาม
                                </div>

                                <div class="form-side-body">
                                    <div class="row g-3">
                                       <div class="col-md-4">
                                            <label class="form-label form-label-modern">
                                                วันที่ติดตาม <span class="text-danger">*</span>
                                            </label>
                                            <input type="date"
                                                name="follow_date"
                                                class="form-control form-control-modern @error('follow_date') is-invalid @enderror"
                                                value="{{ old('follow_date', now('Asia/Bangkok')->toDateString()) }}"
                                                max="{{ now('Asia/Bangkok')->toDateString() }}"
                                                required>
                                            @error('follow_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label form-label-modern">ครูประจำชั้น</label>
                                            <input type="text"
                                                   name="teacher_name"
                                                   class="form-control form-control-modern @error('teacher_name') is-invalid @enderror"
                                                   value="{{ old('teacher_name') }}"
                                                   placeholder="ระบุชื่อครูประจำชั้น">
                                            @error('teacher_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label form-label-modern">โทรศัพท์</label>
                                            <input type="text"
                                                   name="tel"
                                                   class="form-control form-control-modern @error('tel') is-invalid @enderror"
                                                   value="{{ old('tel') }}"
                                                   placeholder="ระบุหมายเลขโทรศัพท์">
                                            @error('tel')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                   <div class="col-12">
                                        <label class="form-label form-label-modern d-block">
                                            การดำเนินงาน <span class="text-danger">*</span>
                                        </label>

                                        <div class="follow-type-group">
                                            <label class="custom-radio-card" for="follow_type_self">
                                                <input class="form-check-input" type="radio" name="follow_type" id="follow_type_self" value="self" {{ old('follow_type') == 'self' ? 'checked' : '' }}>
                                                <span class="custom-radio-text">ติดตามด้วยตนเอง</span>
                                            </label>

                                            <label class="custom-radio-card" for="follow_type_phone">
                                                <input class="form-check-input" type="radio" name="follow_type" id="follow_type_phone" value="phone" {{ old('follow_type') == 'phone' ? 'checked' : '' }}>
                                                <span class="custom-radio-text">โทรศัพท์</span>
                                            </label>

                                            <label class="custom-radio-card" for="follow_type_other">
                                                <input class="form-check-input" type="radio" name="follow_type" id="follow_type_other" value="other" {{ old('follow_type') == 'other' ? 'checked' : '' }}>
                                                <span class="custom-radio-text">อื่น ๆ</span>
                                            </label>
                                        </div>

                                        @error('follow_type')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                        <div class="col-md-6">
                                            <label class="form-label form-label-modern">ผลการติดตาม</label>
                                            <textarea name="result"
                                                      class="form-control form-control-modern @error('result') is-invalid @enderror"
                                                      rows="4"
                                                      placeholder="สรุปผลการติดตาม">{{ old('result') }}</textarea>
                                            @error('result')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label form-label-modern">หมายเหตุ</label>
                                            <textarea name="remark"
                                                      class="form-control form-control-modern @error('remark') is-invalid @enderror"
                                                      rows="4"
                                                      placeholder="ระบุข้อมูลเพิ่มเติม">{{ old('remark') }}</textarea>
                                            @error('remark')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label form-label-modern">ชื่อผู้ติดตาม</label>
                                            <input type="text"
                                                   name="contact_name"
                                                   class="form-control form-control-modern @error('contact_name') is-invalid @enderror"
                                                   value="{{ old('contact_name') }}"
                                                   placeholder="ระบุชื่อผู้ติดตาม">
                                            @error('contact_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                    <button type="submit" class="btn btn-success btn-save">
                        <i class="bi bi-check-circle-fill me-2"></i>บันทึกผล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
