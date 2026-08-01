<div class="modal fade medical-page" id="add-medical-modal" tabindex="-1" aria-labelledby="addMedicalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content medical-modal-content">
            <form action="{{ route('medical.store') }}" method="POST" id="add-medical-form" novalidate>
                <input type="hidden" name="_form_context" value="medical_add">
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">

                <div class="modal-header medical-modal-header medical-modal-header--primary">
                    <div>
                        <h5 class="modal-title fw-bold mb-1" id="addMedicalLabel">
                            <i class="bi bi-hospital me-2"></i>เพิ่มข้อมูลการรักษาพยาบาล
                        </h5>
                        <div class="small opacity-75">กรอกข้อมูลให้ครบถ้วนเพื่อการติดตามผลที่ถูกต้อง</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>

                <div class="modal-body">
                    <div class="medical-form-body p-3 p-md-4">

                        <div class="medical-form-section">
                            <div class="medical-section-title">ข้อมูลการรักษาเบื้องต้น</div>

                            <div class="row g-3">
                                <div class="col-12 col-md-4 col-lg-3">
                                    <label class="form-label required">วันที่รักษา</label>
                                   <input type="date" name="medical_date"
                                    class="form-control @error('medical_date') is-invalid @enderror"
                                    value="{{ old('medical_date') }}"
                                    max="{{ now('Asia/Bangkok')->toDateString() }}"
                                    required>
                                    @error('medical_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 col-md-8 col-lg-4">
                                    <label class="form-label required">ชื่อโรค</label>
                                    <input type="text" name="disease_name"
                                           class="form-control @error('disease_name') is-invalid @enderror"
                                           value="{{ old('disease_name') }}"
                                           placeholder="ระบุชื่อโรค" maxlength="255" required>
                                    @error('disease_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 col-lg-5">
                                    <label class="form-label required">อาการป่วย</label>
                                    <textarea name="illness"
                                              class="form-control @error('illness') is-invalid @enderror"
                                              rows="3"
                                              maxlength="3000"
                                              required
                                              placeholder="ระบุอาการป่วย">{{ old('illness') }}</textarea>
                                    @error('illness') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">การรักษา / การปฐมพยาบาล</label>
                                    <textarea name="treatment"
                                              class="form-control @error('treatment') is-invalid @enderror"
                                              rows="4"
                                              maxlength="3000"
                                              placeholder="ระบุการรักษา การปฐมพยาบาล หรือการดูแลเบื้องต้น">{{ old('treatment') }}</textarea>
                                    @error('treatment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="medical-form-section">
                            <div class="medical-section-title">การพบแพทย์</div>

                            <div class="medical-radio-group">
                                <label class="medical-radio-card">
                                    <input class="form-check-input" type="radio" name="refer" id="refer_yes_new" value="พบแพทย์" required {{ old('refer') == 'พบแพทย์' ? 'checked' : '' }}>
                                    <span class="medical-radio-content">
                                        <span class="medical-radio-icon text-success"><i class="bi bi-check-circle-fill"></i></span>
                                        <span class="medical-radio-text">
                                            <strong>พบแพทย์</strong>
                                            <small>มีการตรวจ วินิจฉัย หรือรักษาโดยแพทย์</small>
                                        </span>
                                    </span>
                                </label>

                                <label class="medical-radio-card">
                                    <input class="form-check-input" type="radio" name="refer" id="refer_no_new" value="ไม่พบแพทย์" {{ old('refer', 'ไม่พบแพทย์') == 'ไม่พบแพทย์' ? 'checked' : '' }}>
                                    <span class="medical-radio-content">
                                        <span class="medical-radio-icon text-secondary"><i class="bi bi-dash-circle-fill"></i></span>
                                        <span class="medical-radio-text">
                                            <strong>ไม่พบแพทย์</strong>
                                            <small>ดูแลเบื้องต้นโดยยังไม่มีการพบแพทย์</small>
                                        </span>
                                    </span>
                                </label>
                            </div>

                            @error('refer') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                            <div id="medical-section-new" class="medical-conditional-box mt-3" style="display:none;">
                                <div class="row g-3">
                                    <div class="col-12 col-lg-8">
                                        <label class="form-label required">การวินิจฉัยของแพทย์</label>
                                        <textarea name="diagnosis"
                                                  class="form-control @error('diagnosis') is-invalid @enderror"
                                                  rows="3" maxlength="3000"
                                                  placeholder="ระบุผลการวินิจฉัยของแพทย์">{{ old('diagnosis') }}</textarea>
                                        @error('diagnosis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-4">
                                        <label class="form-label">วันที่แพทย์นัด</label>
                                        <input type="date" name="appt_date"
                                               class="form-control @error('appt_date') is-invalid @enderror"
                                               value="{{ old('appt_date') }}">
                                        @error('appt_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="medical-form-section mb-0">
                            <div class="medical-section-title">ข้อมูลเพิ่มเติม</div>

                            <div class="row g-3">
                                <div class="col-12 col-md-5">
                                    <label class="form-label">ผู้ดูแล</label>
                                    <input type="text" name="teacher"
                                           class="form-control @error('teacher') is-invalid @enderror"
                                           value="{{ old('teacher') }}" maxlength="255">
                                    @error('teacher') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 col-md-7">
                                    <label class="form-label">หมายเหตุ</label>
                                    <textarea name="remark"
                                              class="form-control @error('remark') is-invalid @enderror"
                                              rows="3" maxlength="3000">{{ old('remark') }}</textarea>
                                    @error('remark') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer medical-modal-footer">
                    <button type="submit" class="btn btn-primary medical-btn medical-btn-primary">
                        <i class="bi bi-save"></i>
                        <span>บันทึกข้อมูล</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary medical-btn medical-btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        <span>ปิด</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
#add-medical-modal .medical-modal-content {
    border: 0;
    border-radius: var(--medical-radius);
    overflow: hidden;
}

#add-medical-modal .medical-modal-content > form {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
}

#add-medical-modal .medical-modal-header {
    border-bottom: 0;
    padding: 1rem 1.25rem;
    flex: 0 0 auto;
    background: linear-gradient(135deg, var(--medical-primary) 0%, var(--medical-primary-dark) 100%);
    color: #fff;
}

#add-medical-modal .modal-body {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    background: #fbfdff;
}

#add-medical-modal .medical-form-section {
    background: #fff;
    border: 1px solid #eef2f7;
    border-radius: 16px;
    padding: 1rem;
    margin-bottom: 1rem;
}

#add-medical-modal .medical-section-title {
    font-size: .95rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}

#add-medical-modal .medical-section-title::before {
    content: "";
    width: 6px;
    height: 18px;
    border-radius: 999px;
    background: var(--medical-primary);
    display: inline-block;
}

#add-medical-modal .form-label {
    font-size: .85rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: .4rem;
}

#add-medical-modal .form-label.required::after {
    content: " *";
    color: #dc2626;
}

#add-medical-modal .form-control,
#add-medical-modal .form-select {
    border-radius: 12px;
    border-color: #dbe2ea;
    min-height: 42px;
    padding-top: .55rem;
    padding-bottom: .55rem;
}

#add-medical-modal .form-control:focus,
#add-medical-modal .form-select:focus {
    border-color: #93c5fd;
    box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .12);
}

#add-medical-modal .medical-radio-group {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}

#add-medical-modal .medical-radio-card {
    display: block;
    margin: 0;
    cursor: pointer;
}

#add-medical-modal .medical-radio-card input[type="radio"] {
    display: none;
}

#add-medical-modal .medical-radio-content {
    display: flex;
    align-items: flex-start;
    gap: .85rem;
    border: 1px solid #dbe2ea;
    border-radius: 14px;
    padding: .95rem 1rem;
    background: #fff;
    transition: all .2s ease;
    min-height: 100%;
}

#add-medical-modal .medical-radio-icon {
    font-size: 1.1rem;
    line-height: 1;
    margin-top: 2px;
}

#add-medical-modal .medical-radio-text {
    display: flex;
    flex-direction: column;
    line-height: 1.35;
}

#add-medical-modal .medical-radio-text small {
    color: #64748b;
    margin-top: .2rem;
}

#add-medical-modal .medical-radio-card input[type="radio"]:checked + .medical-radio-content {
    border-color: #60a5fa;
    background: #eff6ff;
    box-shadow: 0 0 0 .15rem rgba(37, 99, 235, .08);
}

#add-medical-modal .medical-conditional-box {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 14px;
    padding: 1rem;
}

#add-medical-modal .medical-modal-footer {
    flex: 0 0 auto;
    border-top: 1px solid var(--medical-border);
    background: #fff;
    padding: .9rem 1.25rem;
    display: flex;
    justify-content: flex-end;
    gap: .75rem;
    flex-wrap: wrap;
}

#add-medical-modal .medical-btn-secondary {
    min-width: 120px;
}

@media (max-width: 991.98px) {
    #add-medical-modal .medical-radio-group {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 767.98px) {
    #add-medical-modal .medical-modal-footer {
        flex-direction: column-reverse;
    }

    #add-medical-modal .medical-modal-footer .medical-btn {
        width: 100%;
        min-width: 0;
    }
}

@media (max-width: 575.98px) {
    #add-medical-modal.modal.modal-fullscreen-sm-down .modal-content {
        height: 100vh;
        border-radius: 0;
    }

    #add-medical-modal.modal.modal-fullscreen-sm-down .medical-modal-content > form {
        height: 100vh;
    }

    #add-medical-modal.modal.modal-fullscreen-sm-down .modal-header {
        position: sticky;
        top: 0;
        z-index: 20;
    }

    #add-medical-modal.modal.modal-fullscreen-sm-down .modal-footer {
        position: sticky;
        bottom: 0;
        z-index: 20;
        border-top: 1px solid #dee2e6;
        background: #fff;
        padding: .75rem;
    }
}
</style>
@endpush