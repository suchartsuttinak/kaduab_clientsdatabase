<div class="modal fade" id="editAddictiveModal" tabindex="-1" aria-labelledby="editAddictiveLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content addictive-modal">
            <div class="modal-header addictive-modal-header">
                <div class="addictive-modal-title-wrap">
                    <div class="addictive-modal-icon">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h5 class="modal-title addictive-modal-title mb-0" id="editAddictiveLabel">
                            แก้ไขข้อมูลการตรวจสารเสพติด
                        </h5>
                        <div class="addictive-modal-subtitle">
                            ปรับปรุงข้อมูลผลการตรวจและการดำเนินการต่ออย่างเป็นระบบ
                        </div>
                    </div>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <style>
                /* =========================
                   Edit Addictive Modal Only
                   Scoped styles - safe
                   ========================= */
                #editAddictiveModal #edit_refer_field .refer-section-note {
                    margin-top: -0.15rem;
                    margin-bottom: 0.85rem;
                    font-size: 0.84rem;
                    color: #64748b;
                    line-height: 1.45;
                }

                #editAddictiveModal #edit_refer_field .refer-option-grid {
                    display: grid;
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 0.9rem;
                }

                #editAddictiveModal #edit_refer_field .refer-option-card {
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

                #editAddictiveModal #edit_refer_field .refer-option-card:hover {
                    border-color: #93c5fd;
                    box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.08);
                    transform: translateY(-1px);
                }

                #editAddictiveModal #edit_refer_field .refer-option-card input[type="radio"] {
                    margin: 0.15rem 0 0;
                    flex: 0 0 auto;
                }

                #editAddictiveModal #edit_refer_field .refer-option-icon {
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

                #editAddictiveModal #edit_refer_field .refer-option-icon i {
                    font-size: 1rem;
                    line-height: 1;
                }

                #editAddictiveModal #edit_refer_field .refer-option-body {
                    min-width: 0;
                    display: flex;
                    flex-direction: column;
                    gap: 0.3rem;
                }

                #editAddictiveModal #edit_refer_field .refer-option-title {
                    display: block;
                    font-size: 0.98rem;
                    font-weight: 700;
                    color: #0f172a;
                    line-height: 1.35;
                }

                #editAddictiveModal #edit_refer_field .refer-option-desc {
                    display: block;
                    font-size: 0.86rem;
                    color: #64748b;
                    line-height: 1.5;
                    word-break: break-word;
                }

                #editAddictiveModal #edit_refer_field .refer-option-badge {
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

                #editAddictiveModal #edit_refer_field .refer-option-card:has(input:checked) {
                    border-color: #60a5fa;
                    background: linear-gradient(180deg, #f8fbff 0%, #eef6ff 100%);
                    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.12);
                }

                #editAddictiveModal #edit_refer_field .refer-option-card:has(input:checked) .refer-option-icon {
                    background: #dbeafe;
                    color: #1d4ed8;
                }

                #editAddictiveModal #edit_refer_field .refer-option-card:has(input:checked) .refer-option-badge {
                    background: #dbeafe;
                    color: #1d4ed8;
                }

                @media (max-width: 991.98px) {
                    #editAddictiveModal #edit_refer_field .refer-option-card {
                        min-height: 112px;
                    }
                }

                @media (max-width: 767.98px) {
                    #editAddictiveModal #edit_refer_field .refer-option-grid {
                        grid-template-columns: 1fr;
                    }

                    #editAddictiveModal #edit_refer_field .refer-option-card {
                        min-height: unset;
                    }
                }

                @media (max-width: 575.98px) {
                    #editAddictiveModal #edit_refer_field .refer-section-note {
                        font-size: 0.8rem;
                        margin-bottom: 0.75rem;
                    }

                    #editAddictiveModal #edit_refer_field .refer-option-card {
                        gap: 0.75rem;
                        padding: 0.9rem;
                        border-radius: 16px;
                    }

                    #editAddictiveModal #edit_refer_field .refer-option-icon {
                        width: 40px;
                        height: 40px;
                        border-radius: 12px;
                    }

                    #editAddictiveModal #edit_refer_field .refer-option-title {
                        font-size: 0.93rem;
                    }

                    #editAddictiveModal #edit_refer_field .refer-option-desc {
                        font-size: 0.82rem;
                    }
                }
            </style>

            <form id="addictive-edit-form" method="POST" novalidate>
                @csrf
                @method('PUT')

                <div class="modal-body addictive-modal-body">
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <input type="hidden" name="id" id="edit_id">

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
                                    id="edit_date"
                                    class="form-control form-control-modern"
                                    max="{{ now('Asia/Bangkok')->toDateString() }}"
                                    required>
                            </div>

                            <div class="col-12 col-md-4 col-lg-3">
                                <label class="form-label form-label-modern">ครั้งที่</label>
                                <input type="number"
                                       name="count"
                                       id="edit_count"
                                       class="form-control form-control-modern"
                                       readonly>
                            </div>

                            <div class="col-12 col-lg-6">
                                <label class="form-label form-label-modern d-block">
                                    ผลการตรวจ <span class="text-danger">*</span>
                                </label>

                                <div class="radio-card-group">
                                    <label class="radio-card">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="exam"
                                               value="0"
                                               id="edit_exam_no">
                                        <span class="radio-card-content">
                                            <span class="radio-card-title">ไม่พบสารเสพติด</span>
                                            <span class="radio-card-desc">ผลการตรวจเป็นปกติ</span>
                                        </span>
                                    </label>

                                    <label class="radio-card">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="exam"
                                               value="1"
                                               id="edit_exam_yes">
                                        <span class="radio-card-content">
                                            <span class="radio-card-title">พบสารเสพติด</span>
                                            <span class="radio-card-desc">ต้องระบุแนวทางดำเนินการต่อ</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section" id="edit_refer_field" style="display:none;">
                        <div class="form-section-title">
                            <i class="bi bi-arrow-left-right"></i>
                            การดำเนินการต่อ
                        </div>

                        <div class="refer-section-note">
                            เลือกแนวทางดำเนินการที่เหมาะสมเมื่อผลตรวจพบสารเสพติด
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label form-label-modern d-block">การส่งต่อ</label>

                                <div class="refer-option-grid">
                                    <label class="refer-option-card" for="edit_refer_treatment">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="refer"
                                               value="1"
                                               id="edit_refer_treatment">

                                        <span class="refer-option-icon">
                                            <i class="bi bi-hospital"></i>
                                        </span>

                                        <span class="refer-option-body">
                                            <span class="refer-option-title">ส่งต่อบำบัด</span>
                                            <span class="refer-option-desc">ส่งต่อเข้าสู่กระบวนการบำบัดรักษาอย่างเหมาะสมตามแนวทางดูแล</span>
                                            <span class="refer-option-badge">เข้าสู่การรักษา</span>
                                        </span>
                                    </label>

                                    <label class="refer-option-card" for="edit_refer_followup">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="refer"
                                               value="2"
                                               id="edit_refer_followup">

                                        <span class="refer-option-icon">
                                            <i class="bi bi-shield-check"></i>
                                        </span>

                                        <span class="refer-option-body">
                                            <span class="refer-option-title">ติดตามดูแลต่อเนื่อง</span>
                                            <span class="refer-option-desc">เฝ้าระวัง ประเมินซ้ำ และติดตามผลอย่างต่อเนื่องเพื่อป้องกันความเสี่ยง</span>
                                            <span class="refer-option-badge">เฝ้าระวังต่อเนื่อง</span>
                                        </span>
                                    </label>
                                </div>
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
                                          id="edit_record"
                                          rows="4"
                                          class="form-control form-control-modern"
                                          placeholder="ระบุผลการตรวจ รายละเอียด หรือข้อสังเกตเพิ่มเติม"></textarea>
                            </div>

                            <div class="col-12 col-lg-6">
                                <label class="form-label form-label-modern">
                                    ผู้ตรวจ <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="recorder"
                                       id="edit_recorder"
                                       class="form-control form-control-modern"
                                       placeholder="ชื่อผู้ตรวจ / ผู้บันทึกข้อมูล">
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
                        อัปเดตข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>