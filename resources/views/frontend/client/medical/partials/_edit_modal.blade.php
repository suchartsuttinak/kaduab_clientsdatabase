<div class="modal fade" id="editMedicalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content medical-modal-content">
            <form id="editMedicalForm" method="POST" action="{{ route('medical.update', 0) }}" novalidate>
                @csrf
                @method('PUT')

                <input type="hidden" name="client_id" id="edit_client_id">
                <input type="hidden" name="id" id="edit_medical_id">

                <div class="modal-header medical-modal-header medical-modal-header--warning">
                    <div>
                        <h5 class="modal-title fw-bold mb-1">
                            <i class="bi bi-pencil-square me-2"></i>แก้ไขข้อมูลการรักษาพยาบาล
                        </h5>
                        <div class="small text-dark opacity-75">ปรับปรุงข้อมูลให้เป็นปัจจุบันและถูกต้อง</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="medical-form-body p-3 p-md-4">

                        <div class="medical-form-section">
                            <div class="medical-section-title medical-section-title--warning">ข้อมูลการรักษาเบื้องต้น</div>

                            <div class="row g-3">
                                <div class="col-12 col-md-4 col-lg-3">
                                    <label class="form-label required">วันที่</label>
                                    <input type="date"
                                        name="medical_date"
                                        id="edit_medical_date"
                                        class="form-control"
                                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                                        required>
                                </div>

                                <div class="col-12 col-md-8 col-lg-4">
                                    <label class="form-label">ชื่อโรค</label>
                                    <input type="text" name="disease_name" id="edit_disease_name" class="form-control">
                                </div>

                                <div class="col-12 col-lg-5">
                                    <label class="form-label">อาการป่วย</label>
                                    <textarea name="illness" id="edit_illness" class="form-control" rows="3"></textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">การรักษา / การปฐมพยาบาล</label>
                                    <textarea name="treatment" id="edit_treatment" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="medical-form-section">
                            <div class="medical-section-title medical-section-title--warning">การพบแพทย์</div>

                            <div class="medical-radio-group">
                                <label class="medical-radio-card">
                                    <input type="radio" name="refer" id="edit_refer_yes" value="พบแพทย์">
                                    <span class="medical-radio-content">
                                        <span class="medical-radio-icon text-success"><i class="bi bi-check-circle-fill"></i></span>
                                        <span class="medical-radio-text">
                                            <strong>พบแพทย์</strong>
                                            <small>มีการตรวจ วินิจฉัย หรือรักษาโดยแพทย์</small>
                                        </span>
                                    </span>
                                </label>

                                <label class="medical-radio-card">
                                    <input type="radio" name="refer" id="edit_refer_no" value="ไม่พบแพทย์">
                                    <span class="medical-radio-content">
                                        <span class="medical-radio-icon text-secondary"><i class="bi bi-dash-circle-fill"></i></span>
                                        <span class="medical-radio-text">
                                            <strong>ไม่พบแพทย์</strong>
                                            <small>ดูแลเบื้องต้นโดยยังไม่มีการพบแพทย์</small>
                                        </span>
                                    </span>
                                </label>
                            </div>

                            <div id="edit_medical_section" class="medical-conditional-box mt-3" style="display:none;">
                                <div class="row g-3">
                                    <div class="col-12 col-lg-8">
                                        <label class="form-label">การวินิจฉัย</label>
                                        <textarea name="diagnosis" id="edit_diagnosis" class="form-control" rows="3"></textarea>
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-4">
                                        <label class="form-label">วันที่แพทย์นัด</label>
                                        <input type="date" name="appt_date" id="edit_appt_date" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="medical-form-section mb-0">
                            <div class="medical-section-title medical-section-title--warning">ข้อมูลเพิ่มเติม</div>

                            <div class="row g-3">
                                <div class="col-12 col-md-5">
                                    <label class="form-label">ผู้ดูแล</label>
                                    <input type="text" name="teacher" id="edit_teacher" class="form-control">
                                </div>

                                <div class="col-12 col-md-7">
                                    <label class="form-label">หมายเหตุ</label>
                                    <textarea name="remark" id="edit_remark" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer medical-modal-footer">
                    <button type="submit" class="btn btn-success medical-btn medical-btn-success">
                        <i class="bi bi-save"></i>
                        <span>อัปเดตข้อมูล</span>
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
.medical-page #editMedicalModal .medical-modal-content {
    border: 0;
    border-radius: var(--medical-radius);
    overflow: hidden;
}

.medical-page #editMedicalModal .medical-modal-content > form {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
}

.medical-page #editMedicalModal .medical-modal-header {
    border-bottom: 0;
    padding: 1rem 1.25rem;
    flex: 0 0 auto;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #78350f;
}

.medical-page #editMedicalModal .modal-body {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    background: #fffcf5;
}

.medical-page #editMedicalModal .medical-form-section {
    background: #fff;
    border: 1px solid #f3ead7;
    border-radius: 16px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.medical-page #editMedicalModal .medical-section-title {
    font-size: .95rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}

.medical-page #editMedicalModal .medical-section-title::before {
    content: "";
    width: 6px;
    height: 18px;
    border-radius: 999px;
    background: #f59e0b;
    display: inline-block;
}

.medical-page #editMedicalModal .form-label {
    font-size: .85rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: .4rem;
}

.medical-page #editMedicalModal .form-label.required::after {
    content: " *";
    color: #dc2626;
}

.medical-page #editMedicalModal .form-control,
.medical-page #editMedicalModal .form-select {
    border-radius: 12px;
    border-color: #dbe2ea;
    min-height: 42px;
    padding-top: .55rem;
    padding-bottom: .55rem;
}

.medical-page #editMedicalModal .form-control:focus,
.medical-page #editMedicalModal .form-select:focus {
    border-color: #fcd34d;
    box-shadow: 0 0 0 .2rem rgba(245, 158, 11, .14);
}

.medical-page #editMedicalModal .medical-radio-group {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}

.medical-page #editMedicalModal .medical-radio-card {
    display: block;
    margin: 0;
    cursor: pointer;
}

.medical-page #editMedicalModal .medical-radio-card input[type="radio"] {
    display: none;
}

.medical-page #editMedicalModal .medical-radio-content {
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

.medical-page #editMedicalModal .medical-radio-icon {
    font-size: 1.1rem;
    line-height: 1;
    margin-top: 2px;
}

.medical-page #editMedicalModal .medical-radio-text {
    display: flex;
    flex-direction: column;
    line-height: 1.35;
}

.medical-page #editMedicalModal .medical-radio-text small {
    color: #64748b;
    margin-top: .2rem;
}

.medical-page #editMedicalModal .medical-radio-card input[type="radio"]:checked + .medical-radio-content {
    border-color: #fbbf24;
    background: #fff7d6;
    box-shadow: 0 0 0 .15rem rgba(245, 158, 11, .10);
}

.medical-page #editMedicalModal .medical-conditional-box {
    background: #fffaf0;
    border: 1px dashed #e7d3a7;
    border-radius: 14px;
    padding: 1rem;
}

.medical-page #editMedicalModal .medical-modal-footer {
    flex: 0 0 auto;
    border-top: 1px solid var(--medical-border);
    background: #fff;
    padding: .9rem 1.25rem;
    display: flex;
    justify-content: flex-end;
    gap: .75rem;
    flex-wrap: wrap;
}

.medical-page #editMedicalModal .medical-btn-success,
.medical-page #editMedicalModal .medical-btn-secondary {
    min-width: 148px;
}

@media (max-width: 991.98px) {
    .medical-page #editMedicalModal .medical-radio-group {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 767.98px) {
    .medical-page #editMedicalModal .medical-modal-footer {
        flex-direction: column-reverse;
    }

    .medical-page #editMedicalModal .medical-modal-footer .medical-btn {
        width: 100%;
        min-width: 0;
    }
}

@media (max-width: 575.98px) {
    .medical-page #editMedicalModal.modal.modal-fullscreen-sm-down .modal-content {
        height: 100vh;
        border-radius: 0;
    }

    .medical-page #editMedicalModal.modal.modal-fullscreen-sm-down .medical-modal-content > form {
        height: 100vh;
    }

    .medical-page #editMedicalModal.modal.modal-fullscreen-sm-down .modal-header {
        position: sticky;
        top: 0;
        z-index: 20;
    }

    .medical-page #editMedicalModal.modal.modal-fullscreen-sm-down .modal-footer {
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