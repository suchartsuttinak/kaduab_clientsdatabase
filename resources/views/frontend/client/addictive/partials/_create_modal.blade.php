<div class="modal fade" id="createAddictiveModal" tabindex="-1" aria-labelledby="createAddictiveLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content addictive-modal">
            <div class="modal-header addictive-modal-header">
                <div class="addictive-modal-title-wrap">
                    <div class="addictive-modal-icon">
                        <i class="bi bi-eyedropper"></i>
                    </div>
                    <div>
                        <h5 class="modal-title addictive-modal-title mb-0" id="createAddictiveLabel">
                            เพิ่มข้อมูลการตรวจสารเสพติด
                        </h5>
                        <div class="addictive-modal-subtitle">
                            บันทึกผลการตรวจและข้อมูลการติดตามอย่างเป็นระบบ
                        </div>
                    </div>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <style>
                /* ======================================
                   Create Addictive Modal Only
                   Scoped styles - safe
                   ====================================== */
                #createAddictiveModal .create-section-note {
                    margin-top: -0.15rem;
                    margin-bottom: 0.85rem;
                    font-size: 0.84rem;
                    color: #64748b;
                    line-height: 1.45;
                }

                #createAddictiveModal #refer_field_new .refer-option-grid {
                    display: grid;
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 0.9rem;
                }

                #createAddictiveModal #refer_field_new .refer-option-card {
                    position: relative;
                    display: flex;
                    align-items: stretch;
                    gap: 0.85rem;
                    min-height: 120px;
                    padding: 1rem 1rem 0.95rem;
                    border: 1px solid #dbe4f0;
                    border-radius: 18px;
                    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
                    cursor: pointer;
                    transition: all 0.2s ease;
                    overflow: hidden;
                }

                #createAddictiveModal #refer_field_new .refer-option-card:hover {
                    border-color: #93c5fd;
                    box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.08);
                    transform: translateY(-1px);
                }

                #createAddictiveModal #refer_field_new .refer-option-card input[type="radio"] {
                    margin: 0.15rem 0 0;
                    flex: 0 0 auto;
                }

                #createAddictiveModal #refer_field_new .refer-option-icon {
                    width: 44px;
                    height: 44px;
                    border-radius: 14px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    flex: 0 0 auto;
                    background: #eff6ff;
                    color: #2563eb;
                }

                #createAddictiveModal #refer_field_new .refer-option-icon i {
                    font-size: 1rem;
                    line-height: 1;
                }

                #createAddictiveModal #refer_field_new .refer-option-body {
                    min-width: 0;
                    display: flex;
                    flex-direction: column;
                    gap: 0.3rem;
                }

                #createAddictiveModal #refer_field_new .refer-option-title {
                    display: block;
                    font-size: 0.98rem;
                    font-weight: 700;
                    color: #0f172a;
                    line-height: 1.35;
                }

                #createAddictiveModal #refer_field_new .refer-option-desc {
                    display: block;
                    font-size: 0.86rem;
                    color: #64748b;
                    line-height: 1.5;
                    word-break: break-word;
                }

                #createAddictiveModal #refer_field_new .refer-option-badge {
                    display: inline-flex;
                    align-items: center;
                    width: fit-content;
                    margin-top: 0.15rem;
                    padding: 0.28rem 0.55rem;
                    border-radius: 999px;
                    font-size: 0.74rem;
                    font-weight: 700;
                    line-height: 1;
                    background: #f1f5f9;
                    color: #475569;
                }

                #createAddictiveModal #refer_field_new .refer-option-card:has(input:checked) {
                    border-color: #60a5fa;
                    background: linear-gradient(180deg, #f8fbff 0%, #eef6ff 100%);
                    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.12);
                }

                #createAddictiveModal #refer_field_new .refer-option-card:has(input:checked) .refer-option-icon {
                    background: #dbeafe;
                    color: #1d4ed8;
                }

                #createAddictiveModal #refer_field_new .refer-option-card:has(input:checked) .refer-option-badge {
                    background: #dbeafe;
                    color: #1d4ed8;
                }

                @media (max-width: 991.98px) {
                    #createAddictiveModal #refer_field_new .refer-option-card {
                        min-height: 112px;
                    }
                }

                @media (max-width: 767.98px) {
                    #createAddictiveModal #refer_field_new .refer-option-grid {
                        grid-template-columns: 1fr;
                    }

                    #createAddictiveModal #refer_field_new .refer-option-card {
                        min-height: unset;
                    }
                }

                @media (max-width: 575.98px) {
                    #createAddictiveModal .create-section-note {
                        font-size: 0.8rem;
                        margin-bottom: 0.75rem;
                    }

                    #createAddictiveModal #refer_field_new .refer-option-card {
                        gap: 0.75rem;
                        padding: 0.9rem;
                        border-radius: 16px;
                    }

                    #createAddictiveModal #refer_field_new .refer-option-icon {
                        width: 40px;
                        height: 40px;
                        border-radius: 12px;
                    }

                    #createAddictiveModal #refer_field_new .refer-option-title {
                        font-size: 0.93rem;
                    }

                    #createAddictiveModal #refer_field_new .refer-option-desc {
                        font-size: 0.82rem;
                    }
                }
            </style>

            <form id="addictive-form" action="{{ route('addictive.store') }}" method="POST" novalidate>
                @csrf

                <div class="modal-body addictive-modal-body">
                    <input type="hidden" name="client_id" value="{{ $client->id }}">

                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-clipboard2-pulse"></i>
                            ข้อมูลการตรวจ
                        </div>

                        <div class="row g-3">
                           <div class="col-12 col-md-4 col-lg-3">
                                    <label class="form-label form-label-modern">
                                        วันที่ตรวจ <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                        name="date"
                                        class="form-control form-control-modern @error('date') is-invalid @enderror"
                                        value="{{ old('date', now('Asia/Bangkok')->toDateString()) }}"
                                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                                        required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            <div class="col-12 col-md-4 col-lg-3">
                                <label class="form-label form-label-modern">ครั้งที่</label>
                                <input type="number"
                                       name="count"
                                       class="form-control form-control-modern"
                                       value="{{ $addictives->count() + 1 }}"
                                       readonly>
                            </div>

                            <div class="col-12 col-lg-6">
                                <label class="form-label form-label-modern d-block">
                                    ผลการตรวจ <span class="text-danger">*</span>
                                </label>

                                <div class="radio-card-group">
                                    <label class="radio-card">
                                        <input class="@error('exam') is-invalid @enderror"
                                               type="radio"
                                               name="exam"
                                               value="0"
                                               id="exam_no_new"
                                               {{ old('exam', '0') == '0' ? 'checked' : '' }}>
                                        <span class="radio-card-content">
                                            <span class="radio-card-title">ไม่พบสารเสพติด</span>
                                            <span class="radio-card-desc">ผลการตรวจเป็นปกติ</span>
                                        </span>
                                    </label>

                                    <label class="radio-card">
                                        <input class="@error('exam') is-invalid @enderror"
                                               type="radio"
                                               name="exam"
                                               value="1"
                                               id="exam_yes_new"
                                               {{ old('exam') == '1' ? 'checked' : '' }}>
                                        <span class="radio-card-content">
                                            <span class="radio-card-title">พบสารเสพติด</span>
                                            <span class="radio-card-desc">ต้องระบุแนวทางดำเนินการต่อ</span>
                                        </span>
                                    </label>
                                </div>

                                @error('exam')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-section" id="refer_field_new" style="display: {{ old('exam') == '1' ? 'block' : 'none' }};">
                        <div class="form-section-title">
                            <i class="bi bi-arrow-left-right"></i>
                            การดำเนินการต่อ
                        </div>

                        <div class="create-section-note">
                            เลือกแนวทางดำเนินการที่เหมาะสมเมื่อผลตรวจพบสารเสพติด
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label form-label-modern d-block">การส่งต่อ</label>

                                <div class="refer-option-grid">
                                    <label class="refer-option-card" for="refer_treatment_new">
                                        <input class="@error('refer') is-invalid @enderror"
                                               type="radio"
                                               name="refer"
                                               value="1"
                                               id="refer_treatment_new"
                                               {{ old('refer') == '1' ? 'checked' : '' }}>

                                        <span class="refer-option-icon">
                                            <i class="bi bi-hospital"></i>
                                        </span>

                                        <span class="refer-option-body">
                                            <span class="refer-option-title">ส่งต่อบำบัด</span>
                                            <span class="refer-option-desc">ส่งต่อเข้ารับการบำบัดรักษาอย่างเหมาะสมตามแนวทางการดูแล</span>
                                            <span class="refer-option-badge">เข้าสู่การรักษา</span>
                                        </span>
                                    </label>

                                    <label class="refer-option-card" for="refer_followup_new">
                                        <input class="@error('refer') is-invalid @enderror"
                                               type="radio"
                                               name="refer"
                                               value="2"
                                               id="refer_followup_new"
                                               {{ old('refer') == '2' ? 'checked' : '' }}>

                                        <span class="refer-option-icon">
                                            <i class="bi bi-shield-check"></i>
                                        </span>

                                        <span class="refer-option-body">
                                            <span class="refer-option-title">ติดตามดูแลต่อเนื่อง</span>
                                            <span class="refer-option-desc">เฝ้าระวัง ประเมินซ้ำ และติดตามผลอย่างต่อเนื่องเพื่อลดความเสี่ยง</span>
                                            <span class="refer-option-badge">เฝ้าระวังต่อเนื่อง</span>
                                        </span>
                                    </label>
                                </div>

                                @error('refer')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-journal-text"></i>
                            รายละเอียดเพิ่มเติม
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label form-label-modern">บันทึกผล</label>
                                <textarea name="record"
                                          rows="4"
                                          class="form-control form-control-modern @error('record') is-invalid @enderror"
                                          placeholder="ระบุผลการตรวจ รายละเอียด หรือข้อสังเกตเพิ่มเติม">{{ old('record') }}</textarea>
                                @error('record')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-lg-6">
                                <label class="form-label form-label-modern">
                                    ผู้ตรวจ <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="recorder"
                                       class="form-control form-control-modern @error('recorder') is-invalid @enderror"
                                       value="{{ old('recorder') }}"
                                       placeholder="ชื่อผู้ตรวจ / ผู้บันทึกข้อมูล">
                                @error('recorder')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer addictive-modal-footer">
                    <button type="button" class="btn btn-cancel-modern" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        ปิด
                    </button>

                    <button type="submit" class="btn btn-save-modern">
                        <i class="bi bi-save"></i>
                        บันทึกผล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>