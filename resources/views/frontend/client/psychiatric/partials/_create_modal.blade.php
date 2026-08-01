@php
    $isCreateError = old('_form_context') === 'psychiatric_create';
@endphp

<div class="modal fade psychiatric-page psy-modal"
     id="createPsychiatricModal"
     tabindex="-1"
     aria-labelledby="createPsychiatricLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="psychiatric-form"
                  class="psy-modal-form"
                  action="{{ route('psychiatric.store') }}"
                  method="POST"
                  novalidate>
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="_form_context" value="psychiatric_create">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="createPsychiatricLabel">
                        <i class="bi bi-clipboard-heart"></i>
                        เพิ่มข้อมูลการตรวจวินิจฉัยทางจิตเวช
                    </h5>
                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                            aria-label="ปิด"></button>
                </div>

                <div class="modal-body">
                    <div class="psy-modal-section">
                        <div class="psy-form-grid">
                            <div class="psy-field psy-col-3">
                                <label class="psy-label" for="create_sent_date">
                                    วันที่ส่งตรวจ <span class="psy-required">*</span>
                                </label>
                                <input type="date"
                                       id="create_sent_date"
                                       name="sent_date"
                                       class="form-control {{ $isCreateError && $errors->has('sent_date') ? 'is-invalid' : '' }}"
                                       value="{{ $isCreateError ? old('sent_date') : '' }}"
                                       max="{{ now('Asia/Bangkok')->toDateString() }}"
                                       required>
                                @if($isCreateError && $errors->has('sent_date'))
                                    <div class="invalid-feedback">{{ $errors->first('sent_date') }}</div>
                                @endif
                            </div>

                            <div class="psy-field psy-col-4">
                                <label class="psy-label" for="create_hotpital">
                                    สถานพยาบาล <span class="psy-required">*</span>
                                </label>
                                <input type="text"
                                       id="create_hotpital"
                                       name="hotpital"
                                       class="form-control {{ $isCreateError && $errors->has('hotpital') ? 'is-invalid' : '' }}"
                                       value="{{ $isCreateError ? old('hotpital') : '' }}"
                                       maxlength="255"
                                       placeholder="ระบุสถานพยาบาล"
                                       required>
                                @if($isCreateError && $errors->has('hotpital'))
                                    <div class="invalid-feedback">{{ $errors->first('hotpital') }}</div>
                                @endif
                            </div>

                            <div class="psy-field psy-col-5">
                                <label class="psy-label" for="create_psycho_id">
                                    ผลการตรวจวินิจฉัย <span class="psy-required">*</span>
                                </label>
                                <select id="create_psycho_id"
                                        name="psycho_id"
                                        class="form-select {{ $isCreateError && $errors->has('psycho_id') ? 'is-invalid' : '' }}"
                                        required>
                                    <option value="">-- เลือกผลการตรวจ --</option>
                                    @foreach($psycho as $p)
                                        <option value="{{ $p->id }}"
                                            {{ $isCreateError && (string) old('psycho_id') === (string) $p->id ? 'selected' : '' }}>
                                            {{ $p->psycho_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($isCreateError && $errors->has('psycho_id'))
                                    <div class="invalid-feedback">{{ $errors->first('psycho_id') }}</div>
                                @endif
                            </div>

                            <div class="psy-field psy-col-12">
                                <label class="psy-label" for="create_diagnose">
                                    สรุปผลการตรวจ / การวินิจฉัย
                                </label>
                                <textarea id="create_diagnose"
                                          name="diagnose"
                                          rows="4"
                                          maxlength="3000"
                                          class="form-control {{ $isCreateError && $errors->has('diagnose') ? 'is-invalid' : '' }}"
                                          placeholder="ระบุรายละเอียดเพิ่มเติม">{{ $isCreateError ? old('diagnose') : '' }}</textarea>
                                @if($isCreateError && $errors->has('diagnose'))
                                    <div class="invalid-feedback">{{ $errors->first('diagnose') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="psy-modal-section">
                        <div class="psy-form-grid">
                            <div class="psy-field psy-col-3">
                                <label class="psy-label" for="create_appoin_date">นัดครั้งต่อไป</label>
                                <input type="date"
                                       id="create_appoin_date"
                                       name="appoin_date"
                                       class="form-control {{ $isCreateError && $errors->has('appoin_date') ? 'is-invalid' : '' }}"
                                       value="{{ $isCreateError ? old('appoin_date') : '' }}">
                                @if($isCreateError && $errors->has('appoin_date'))
                                    <div class="invalid-feedback">{{ $errors->first('appoin_date') }}</div>
                                @endif
                            </div>

                            <div class="psy-field psy-col-4">
                                <label class="psy-label d-block">
                                    การรักษา <span class="psy-required">*</span>
                                </label>
                                <div class="psy-option-group" data-option-group="drug_no">
                                    <label class="psy-option-card" for="drug_yes_new">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="drug_no"
                                               id="drug_yes_new"
                                               value="yes"
                                               {{ $isCreateError && old('drug_no') === 'yes' ? 'checked' : '' }}
                                               required>
                                        <span>รับยา</span>
                                    </label>

                                    <label class="psy-option-card" for="drug_no_new">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="drug_no"
                                               id="drug_no_new"
                                               value="no"
                                               {{ !$isCreateError || old('drug_no', 'no') === 'no' ? 'checked' : '' }}
                                               required>
                                        <span>ไม่รับยา</span>
                                    </label>
                                </div>
                                @if($isCreateError && $errors->has('drug_no'))
                                    <div class="invalid-feedback d-block">{{ $errors->first('drug_no') }}</div>
                                @endif
                            </div>

                            <div class="psy-field psy-col-5 psy-drug-field" id="drug_name_field_new">
                                <label class="psy-label" for="create_drug_name">
                                    ชื่อยา <span class="psy-required" data-drug-required>*</span>
                                </label>
                                <input type="text"
                                       id="create_drug_name"
                                       name="drug_name"
                                       class="form-control {{ $isCreateError && $errors->has('drug_name') ? 'is-invalid' : '' }}"
                                       value="{{ $isCreateError ? old('drug_name') : '' }}"
                                       maxlength="255"
                                       placeholder="ระบุชื่อยา">
                                @if($isCreateError && $errors->has('drug_name'))
                                    <div class="invalid-feedback">{{ $errors->first('drug_name') }}</div>
                                @endif
                            </div>

                            <div class="psy-field psy-col-6">
                                <label class="psy-label d-block">
                                    การขึ้นทะเบียนคนพิการ <span class="psy-required">*</span>
                                </label>
                                <div class="psy-option-group" data-option-group="disa_no">
                                    <label class="psy-option-card" for="create_disa_yes">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="disa_no"
                                               id="create_disa_yes"
                                               value="yes"
                                               {{ $isCreateError && old('disa_no') === 'yes' ? 'checked' : '' }}
                                               required>
                                        <span>ขึ้นทะเบียน</span>
                                    </label>

                                    <label class="psy-option-card" for="create_disa_no">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="disa_no"
                                               id="create_disa_no"
                                               value="no"
                                               {{ !$isCreateError || old('disa_no', 'no') === 'no' ? 'checked' : '' }}
                                               required>
                                        <span>ไม่ขึ้นทะเบียน</span>
                                    </label>
                                </div>
                                @if($isCreateError && $errors->has('disa_no'))
                                    <div class="invalid-feedback d-block">{{ $errors->first('disa_no') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success psy-btn">
                        <i class="bi bi-save"></i>
                        <span>บันทึกผล</span>
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
