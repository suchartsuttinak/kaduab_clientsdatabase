<div class="modal fade md-modal" id="add-medical-modal" tabindex="-1" aria-labelledby="addMedicalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form action="{{ route('medical.store') }}" method="POST" id="add-medical-form" novalidate>
                <input type="hidden" name="_form_context" value="medical_add">
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="addMedicalLabel">
                        <i class="bi bi-clipboard2-plus me-2"></i>เพิ่มข้อมูลการรักษาพยาบาล
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด" data-permission-keep></button>
                </div>

                <div class="modal-body">
                    <div class="md-form-section">
                        <h6 class="md-section-title"><i class="bi bi-heart-pulse"></i>ข้อมูลการรักษา</h6>
                        <div class="md-form-grid">
                            <div class="md-col-4">
                                <label class="md-form-label" for="medical_date_new">
                                    <i class="bi bi-calendar-event"></i>วันที่รักษา <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       id="medical_date_new"
                                       name="medical_date"
                                       class="form-control md-form-control @error('medical_date') is-invalid @enderror"
                                       value="{{ old('medical_date') }}"
                                       max="{{ now('Asia/Bangkok')->toDateString() }}"
                                       required>
                                @error('medical_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="md-col-8">
                                <label class="md-form-label" for="disease_name_new">
                                    <i class="bi bi-activity"></i>ชื่อโรค <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       id="disease_name_new"
                                       name="disease_name"
                                       class="form-control md-form-control @error('disease_name') is-invalid @enderror"
                                       value="{{ old('disease_name') }}"
                                       maxlength="255"
                                       placeholder="ระบุชื่อโรคหรืออาการสำคัญ"
                                       required>
                                @error('disease_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="md-col-6">
                                <label class="md-form-label" for="illness_new">
                                    <i class="bi bi-thermometer-half"></i>อาการป่วย <span class="text-danger">*</span>
                                </label>
                                <textarea id="illness_new"
                                          name="illness"
                                          class="form-control md-form-control @error('illness') is-invalid @enderror"
                                          rows="4"
                                          maxlength="3000"
                                          placeholder="ระบุอาการป่วย"
                                          required>{{ old('illness') }}</textarea>
                                @error('illness')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="md-col-6">
                                <label class="md-form-label" for="treatment_new">
                                    <i class="bi bi-bandaid"></i>การรักษา / การปฐมพยาบาล
                                </label>
                                <textarea id="treatment_new"
                                          name="treatment"
                                          class="form-control md-form-control @error('treatment') is-invalid @enderror"
                                          rows="4"
                                          maxlength="3000"
                                          placeholder="ระบุการรักษาหรือการดูแลเบื้องต้น">{{ old('treatment') }}</textarea>
                                @error('treatment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="md-form-section">
                        <h6 class="md-section-title"><i class="bi bi-hospital"></i>การพบแพทย์</h6>
                        <div class="md-radio-group">
                            <label class="md-radio-option" for="refer_yes_new">
                                <input class="form-check-input"
                                       type="radio"
                                       name="refer"
                                       id="refer_yes_new"
                                       value="พบแพทย์"
                                       required
                                       {{ old('refer') === 'พบแพทย์' ? 'checked' : '' }}>
                                <span><i class="bi bi-check-circle"></i>พบแพทย์</span>
                            </label>

                            <label class="md-radio-option" for="refer_no_new">
                                <input class="form-check-input"
                                       type="radio"
                                       name="refer"
                                       id="refer_no_new"
                                       value="ไม่พบแพทย์"
                                       {{ old('refer', 'ไม่พบแพทย์') === 'ไม่พบแพทย์' ? 'checked' : '' }}>
                                <span><i class="bi bi-dash-circle"></i>ไม่พบแพทย์</span>
                            </label>
                        </div>
                        @error('refer')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                        <div id="medical-section-new" class="md-conditional mt-3" style="display:none;">
                            <div class="md-form-grid">
                                <div class="md-col-8">
                                    <label class="md-form-label" for="diagnosis_new">
                                        <i class="bi bi-clipboard2-pulse"></i>การวินิจฉัยของแพทย์ <span class="text-danger">*</span>
                                    </label>
                                    <textarea id="diagnosis_new"
                                              name="diagnosis"
                                              class="form-control md-form-control @error('diagnosis') is-invalid @enderror"
                                              rows="3"
                                              maxlength="3000"
                                              placeholder="ระบุผลการวินิจฉัยของแพทย์">{{ old('diagnosis') }}</textarea>
                                    @error('diagnosis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="md-col-4">
                                    <label class="md-form-label" for="appt_date_new">
                                        <i class="bi bi-calendar2-check"></i>วันที่แพทย์นัด
                                    </label>
                                    <input type="date"
                                           id="appt_date_new"
                                           name="appt_date"
                                           class="form-control md-form-control @error('appt_date') is-invalid @enderror"
                                           value="{{ old('appt_date') }}">
                                    @error('appt_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="md-form-section mb-0">
                        <h6 class="md-section-title"><i class="bi bi-info-circle"></i>ข้อมูลเพิ่มเติม</h6>
                        <div class="md-form-grid">
                            <div class="md-col-5">
                                <label class="md-form-label" for="teacher_new">
                                    <i class="bi bi-person-check"></i>ผู้ดูแล
                                </label>
                                <input type="text"
                                       id="teacher_new"
                                       name="teacher"
                                       class="form-control md-form-control @error('teacher') is-invalid @enderror"
                                       value="{{ old('teacher') }}"
                                       maxlength="255">
                                @error('teacher')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="md-col-7">
                                <label class="md-form-label" for="remark_new">
                                    <i class="bi bi-chat-left-text"></i>หมายเหตุ
                                </label>
                                <textarea id="remark_new"
                                          name="remark"
                                          class="form-control md-form-control @error('remark') is-invalid @enderror"
                                          rows="3"
                                          maxlength="3000">{{ old('remark') }}</textarea>
                                @error('remark')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary md-btn" data-bs-dismiss="modal" data-permission-keep>
                        <i class="bi bi-x-lg"></i>
                        <span>ปิด</span>
                    </button>
                    <button type="submit" class="btn btn-primary md-btn" data-permission-action="create">
                        <i class="bi bi-check-circle"></i>
                        <span>บันทึกข้อมูล</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
