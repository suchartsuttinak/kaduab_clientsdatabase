@php
    $isEditError = old('_form_context') === 'psychiatric_edit';
@endphp

<div class="modal fade psychiatric-page psy-modal"
     id="editPsychiatricModal"
     tabindex="-1"
     aria-labelledby="editPsychiatricLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="psychiatric-edit-form"
                  class="psy-modal-form"
                  method="POST"
                  novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="_form_context" value="psychiatric_edit">
                <input type="hidden" name="_edit_id" id="edit_record_id" value="{{ $isEditError ? old('_edit_id') : '' }}">

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold" id="editPsychiatricLabel">
                        <i class="bi bi-pencil-square"></i>
                        แก้ไขข้อมูลการตรวจวินิจฉัยทางจิตเวช
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>

                <div class="modal-body">
                    <div class="psy-modal-section">
                        <div class="psy-form-grid">
                            <div class="psy-field psy-col-3">
                                <label class="psy-label" for="edit_sent_date">
                                    วันที่ส่งตรวจ <span class="psy-required">*</span>
                                </label>
                                <input type="date"
                                       id="edit_sent_date"
                                       name="sent_date"
                                       class="form-control {{ $isEditError && $errors->has('sent_date') ? 'is-invalid' : '' }}"
                                       max="{{ now('Asia/Bangkok')->toDateString() }}"
                                       required>
                                @if($isEditError && $errors->has('sent_date'))
                                    <div class="invalid-feedback">{{ $errors->first('sent_date') }}</div>
                                @endif
                            </div>

                            <div class="psy-field psy-col-4">
                                <label class="psy-label" for="edit_hotpital">
                                    สถานพยาบาล <span class="psy-required">*</span>
                                </label>
                                <input type="text"
                                       id="edit_hotpital"
                                       name="hotpital"
                                       class="form-control {{ $isEditError && $errors->has('hotpital') ? 'is-invalid' : '' }}"
                                       maxlength="255"
                                       placeholder="ระบุสถานพยาบาล"
                                       required>
                                @if($isEditError && $errors->has('hotpital'))
                                    <div class="invalid-feedback">{{ $errors->first('hotpital') }}</div>
                                @endif
                            </div>

                            <div class="psy-field psy-col-5">
                                <label class="psy-label" for="edit_psycho_id">
                                    ผลการตรวจวินิจฉัย <span class="psy-required">*</span>
                                </label>
                                <select id="edit_psycho_id"
                                        name="psycho_id"
                                        class="form-select {{ $isEditError && $errors->has('psycho_id') ? 'is-invalid' : '' }}"
                                        required>
                                    <option value="">-- เลือกผลการตรวจ --</option>
                                    @foreach($psycho as $p)
                                        <option value="{{ $p->id }}">{{ $p->psycho_name }}</option>
                                    @endforeach
                                </select>
                                @if($isEditError && $errors->has('psycho_id'))
                                    <div class="invalid-feedback">{{ $errors->first('psycho_id') }}</div>
                                @endif
                            </div>

                            <div class="psy-field psy-col-12">
                                <label class="psy-label" for="edit_diagnose">สรุปผลการตรวจ / การวินิจฉัย</label>
                                <textarea id="edit_diagnose"
                                          name="diagnose"
                                          rows="4"
                                          maxlength="3000"
                                          class="form-control {{ $isEditError && $errors->has('diagnose') ? 'is-invalid' : '' }}"
                                          placeholder="ระบุรายละเอียดเพิ่มเติม"></textarea>
                                @if($isEditError && $errors->has('diagnose'))
                                    <div class="invalid-feedback">{{ $errors->first('diagnose') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="psy-modal-section">
                        <div class="psy-form-grid">
                            <div class="psy-field psy-col-3">
                                <label class="psy-label" for="edit_appoin_date">นัดครั้งต่อไป</label>
                                <input type="date"
                                       id="edit_appoin_date"
                                       name="appoin_date"
                                       class="form-control {{ $isEditError && $errors->has('appoin_date') ? 'is-invalid' : '' }}">
                                @if($isEditError && $errors->has('appoin_date'))
                                    <div class="invalid-feedback">{{ $errors->first('appoin_date') }}</div>
                                @endif
                            </div>

                            <div class="psy-field psy-col-4">
                                <label class="psy-label d-block">
                                    การรักษา <span class="psy-required">*</span>
                                </label>
                                <div class="psy-option-group" data-option-group="edit_drug_no">
                                    <label class="psy-option-card" for="edit_drug_yes">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="drug_no"
                                               value="yes"
                                               id="edit_drug_yes"
                                               required>
                                        <span>รับยา</span>
                                    </label>

                                    <label class="psy-option-card" for="edit_drug_no">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="drug_no"
                                               value="no"
                                               id="edit_drug_no"
                                               required>
                                        <span>ไม่รับยา</span>
                                    </label>
                                </div>
                                @if($isEditError && $errors->has('drug_no'))
                                    <div class="invalid-feedback d-block">{{ $errors->first('drug_no') }}</div>
                                @endif
                            </div>

                            <div class="psy-field psy-col-5 psy-drug-field" id="edit_drug_name_field">
                                <label class="psy-label" for="edit_drug_name">
                                    ชื่อยา <span class="psy-required" data-drug-required>*</span>
                                </label>
                                <input type="text"
                                       id="edit_drug_name"
                                       name="drug_name"
                                       class="form-control {{ $isEditError && $errors->has('drug_name') ? 'is-invalid' : '' }}"
                                       maxlength="255"
                                       placeholder="ระบุชื่อยา">
                                @if($isEditError && $errors->has('drug_name'))
                                    <div class="invalid-feedback">{{ $errors->first('drug_name') }}</div>
                                @endif
                            </div>

                            <div class="psy-field psy-col-6">
                                <label class="psy-label d-block">
                                    การขึ้นทะเบียนคนพิการ <span class="psy-required">*</span>
                                </label>
                                <div class="psy-option-group" data-option-group="edit_disa_no">
                                    <label class="psy-option-card" for="edit_disa_yes">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="disa_no"
                                               value="yes"
                                               id="edit_disa_yes"
                                               required>
                                        <span>ขึ้นทะเบียน</span>
                                    </label>

                                    <label class="psy-option-card" for="edit_disa_no">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="disa_no"
                                               value="no"
                                               id="edit_disa_no"
                                               required>
                                        <span>ไม่ขึ้นทะเบียน</span>
                                    </label>
                                </div>
                                @if($isEditError && $errors->has('disa_no'))
                                    <div class="invalid-feedback d-block">{{ $errors->first('disa_no') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success psy-btn">
                        <i class="bi bi-save"></i>
                        <span>อัปเดตข้อมูล</span>
                    </button>
                    <button type="button" class="btn btn-secondary psy-btn" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        <span>ปิด</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
