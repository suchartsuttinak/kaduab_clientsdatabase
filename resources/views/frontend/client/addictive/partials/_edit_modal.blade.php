@php
    $editExam = $addictiveEditHasErrors ? (string) old('exam', '') : '';
    $editRefer = $addictiveEditHasErrors ? (string) old('refer', '') : '';
@endphp

<div class="modal fade" id="editAddictiveModal" tabindex="-1" aria-labelledby="editAddictiveLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content addictive-modal">
            <div class="modal-header addictive-modal-header">
                <div class="addictive-modal-title-wrap">
                    <div class="addictive-modal-icon"><i id="editAddictiveIcon" class="bi bi-pencil-square"></i></div>
                    <div>
                        <h5 class="modal-title addictive-modal-title mb-0" id="editAddictiveLabel">
                            แก้ไขข้อมูลการตรวจสารเสพติด
                        </h5>
                        <div class="addictive-modal-subtitle" id="editAddictiveSubtitle">ปรับปรุงผลการตรวจและแนวทางดำเนินการต่อ</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
            </div>

            <form id="addictive-edit-form" method="POST" novalidate data-permission-action="update">
                @csrf
                @method('PUT')
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="_form_context" value="addictive_edit">
                <input type="hidden" name="_edit_id" id="edit_id" value="{{ $addictiveEditHasErrors ? old('_edit_id') : '' }}">

                <div class="modal-body addictive-modal-body">
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
                                       class="form-control form-control-modern {{ $addictiveEditHasErrors && $errors->has('date') ? 'is-invalid' : '' }}"
                                       value="{{ $addictiveEditHasErrors ? old('date') : '' }}"
                                       max="{{ now('Asia/Bangkok')->toDateString() }}"
                                       required>
                                @if($addictiveEditHasErrors && $errors->has('date'))
                                    <div class="invalid-feedback">{{ $errors->first('date') }}</div>
                                @endif
                            </div>

                            <div class="col-12 col-md-4 col-lg-3">
                                <label class="form-label form-label-modern">ครั้งที่</label>
                                <input type="number"
                                       name="count"
                                       id="edit_count"
                                       class="form-control form-control-modern"
                                       value="{{ $addictiveEditHasErrors ? old('count') : '' }}"
                                       readonly>
                            </div>

                            <div class="col-12 col-lg-6">
                                <label class="form-label form-label-modern d-block">
                                    ผลการตรวจ <span class="text-danger">*</span>
                                </label>
                                <div class="radio-card-group">
                                    <label class="radio-card">
                                        <input type="radio" name="exam" value="0" id="edit_exam_no"
                                               {{ $editExam === '0' ? 'checked' : '' }} required>
                                        <span class="radio-card-content">
                                            <span class="radio-card-title">ไม่พบสารเสพติด</span>
                                            <span class="radio-card-desc">ผลการตรวจเป็นปกติ</span>
                                        </span>
                                    </label>
                                    <label class="radio-card">
                                        <input type="radio" name="exam" value="1" id="edit_exam_yes"
                                               {{ $editExam === '1' ? 'checked' : '' }} required>
                                        <span class="radio-card-content">
                                            <span class="radio-card-title">พบสารเสพติด</span>
                                            <span class="radio-card-desc">ต้องเลือกแนวทางดำเนินการต่อ</span>
                                        </span>
                                    </label>
                                </div>
                                @if($addictiveEditHasErrors && $errors->has('exam'))
                                    <div class="invalid-feedback d-block">{{ $errors->first('exam') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-section" id="edit_refer_field" {{ $editExam === '1' ? '' : 'hidden' }}>
                        <div class="form-section-title">
                            <i class="bi bi-arrow-left-right"></i>
                            การดำเนินการต่อ <span class="text-danger">*</span>
                        </div>
                        <p class="text-muted small mb-3">กรุณาเลือกแนวทางดำเนินการเมื่อผลตรวจพบสารเสพติด</p>

                        <div data-refer-wrap class="{{ $addictiveEditHasErrors && $errors->has('refer') ? 'addictive-refer-invalid' : '' }}">
                            <div class="refer-option-grid">
                                <label class="refer-option-card" for="edit_refer_treatment">
                                    <input type="radio" name="refer" value="1" id="edit_refer_treatment"
                                           {{ $editRefer === '1' ? 'checked' : '' }}>
                                    <span class="refer-option-icon"><i class="bi bi-hospital"></i></span>
                                    <span class="refer-option-body">
                                        <span class="refer-option-title">ส่งต่อบำบัด</span>
                                        <span class="refer-option-desc">ส่งต่อเข้าสู่กระบวนการบำบัดรักษาตามแนวทางการดูแล</span>
                                        <span class="refer-option-badge">เข้าสู่การรักษา</span>
                                    </span>
                                </label>

                                <label class="refer-option-card" for="edit_refer_followup">
                                    <input type="radio" name="refer" value="2" id="edit_refer_followup"
                                           {{ $editRefer === '2' ? 'checked' : '' }}>
                                    <span class="refer-option-icon"><i class="bi bi-shield-check"></i></span>
                                    <span class="refer-option-body">
                                        <span class="refer-option-title">ติดตามดูแลต่อเนื่อง</span>
                                        <span class="refer-option-desc">เฝ้าระวัง ประเมินซ้ำ และติดตามผลอย่างต่อเนื่อง</span>
                                        <span class="refer-option-badge">เฝ้าระวังต่อเนื่อง</span>
                                    </span>
                                </label>
                            </div>
                            <div data-refer-client-error class="invalid-feedback d-none">
                                กรุณาเลือกแนวทางดำเนินการเมื่อพบสารเสพติด
                            </div>
                            @if($addictiveEditHasErrors && $errors->has('refer'))
                                <div class="invalid-feedback d-block">{{ $errors->first('refer') }}</div>
                            @endif
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
                                          maxlength="3000"
                                          class="form-control form-control-modern {{ $addictiveEditHasErrors && $errors->has('record') ? 'is-invalid' : '' }}"
                                          placeholder="ระบุผลการตรวจ รายละเอียด หรือข้อสังเกตเพิ่มเติม">{{ $addictiveEditHasErrors ? old('record') : '' }}</textarea>
                                @if($addictiveEditHasErrors && $errors->has('record'))
                                    <div class="invalid-feedback">{{ $errors->first('record') }}</div>
                                @endif
                            </div>

                            <div class="col-12 col-lg-6">
                                <label class="form-label form-label-modern">
                                    ผู้ตรวจ <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="recorder"
                                       id="edit_recorder"
                                       maxlength="255"
                                       class="form-control form-control-modern {{ $addictiveEditHasErrors && $errors->has('recorder') ? 'is-invalid' : '' }}"
                                       value="{{ $addictiveEditHasErrors ? old('recorder') : '' }}"
                                       placeholder="ชื่อผู้ตรวจ / ผู้บันทึกข้อมูล"
                                       required>
                                @if($addictiveEditHasErrors && $errors->has('recorder'))
                                    <div class="invalid-feedback">{{ $errors->first('recorder') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer addictive-modal-footer">
                    <button type="button" class="btn btn-cancel-modern" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> ปิด
                    </button>
                    <button type="submit" id="addictive-edit-submit" class="btn btn-save-modern" data-permission-action="update">
                        <i class="bi bi-save"></i> อัปเดตข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
