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

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="createPsychiatricLabel">
                        <i class="bi bi-clipboard-heart"></i>
                        เพิ่มข้อมูลการตรวจวินิจฉัยทางจิตเวช
                    </h5>
                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="client_id" value="{{ $client->id }}">

                    <div class="psy-modal-section">
                        <div class="psy-form-grid">
                            <div class="psy-field psy-col-3">
                                <label class="psy-label" for="create_sent_date">
                                    วันที่ส่งตรวจ <span class="psy-required">*</span>
                                </label>
                                <input type="date"
                                id="create_sent_date"
                                name="sent_date"
                                class="form-control @error('sent_date') is-invalid @enderror"
                                value="{{ old('sent_date') }}"
                                max="{{ now('Asia/Bangkok')->toDateString() }}"
                                required>
                                @error('sent_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="psy-field psy-col-4">
                                <label class="psy-label" for="create_hotpital">
                                    สถานพยาบาล <span class="psy-required">*</span>
                                </label>
                                <input type="text"
                                       id="create_hotpital"
                                       name="hotpital"
                                       class="form-control @error('hotpital') is-invalid @enderror"
                                       value="{{ old('hotpital') }}"
                                       placeholder="ระบุสถานพยาบาล">
                                @error('hotpital')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="psy-field psy-col-5">
                                <label class="psy-label" for="create_psycho_id">
                                    ผลการตรวจวินิจฉัย <span class="psy-required">*</span>
                                </label>
                                <select id="create_psycho_id"
                                        name="psycho_id"
                                        class="form-select @error('psycho_id') is-invalid @enderror">
                                    <option value="">-- เลือกผลการตรวจ --</option>
                                    @foreach($psycho as $p)
                                        <option value="{{ $p->id }}" {{ old('psycho_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->psycho_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('psycho_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="psy-field psy-col-12">
                                <label class="psy-label" for="create_diagnose">
                                    สรุปผลการตรวจ / การวินิจฉัย
                                </label>
                                <textarea id="create_diagnose"
                                          name="diagnose"
                                          rows="4"
                                          class="form-control @error('diagnose') is-invalid @enderror"
                                          placeholder="ระบุรายละเอียดเพิ่มเติม">{{ old('diagnose') }}</textarea>
                                @error('diagnose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                       class="form-control @error('appoin_date') is-invalid @enderror"
                                       value="{{ old('appoin_date') }}">
                                @error('appoin_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="psy-field psy-col-4">
                                <label class="psy-label d-block">การรักษา</label>
                                <div class="psy-option-group" data-option-group="drug_no">
                                    <label class="psy-option-card" for="drug_yes_new">
                                        <input class="form-check-input @error('drug_no') is-invalid @enderror"
                                               type="radio"
                                               name="drug_no"
                                               id="drug_yes_new"
                                               value="yes"
                                               {{ old('drug_no') == 'yes' ? 'checked' : '' }}>
                                        <span>รับยา</span>
                                    </label>

                                    <label class="psy-option-card" for="drug_no_new">
                                        <input class="form-check-input @error('drug_no') is-invalid @enderror"
                                               type="radio"
                                               name="drug_no"
                                               id="drug_no_new"
                                               value="no"
                                               {{ old('drug_no', 'no') == 'no' ? 'checked' : '' }}>
                                        <span>ไม่รับยา</span>
                                    </label>
                                </div>
                                @error('drug_no')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="psy-field psy-col-5" id="drug_name_field_new">
                                <label class="psy-label" for="create_drug_name">ชื่อยา</label>
                                <input type="text"
                                       id="create_drug_name"
                                       name="drug_name"
                                       class="form-control @error('drug_name') is-invalid @enderror"
                                       value="{{ old('drug_name') }}"
                                       placeholder="ระบุชื่อยา">
                                @error('drug_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="psy-field psy-col-6">
                                <label class="psy-label d-block">การขึ้นทะเบียนคนพิการ</label>
                                <div class="psy-option-group" data-option-group="disa_no">
                                    <label class="psy-option-card" for="create_disa_yes">
                                        <input class="form-check-input @error('disa_no') is-invalid @enderror"
                                               type="radio"
                                               name="disa_no"
                                               id="create_disa_yes"
                                               value="yes"
                                               {{ old('disa_no') == 'yes' ? 'checked' : '' }}>
                                        <span>ขึ้นทะเบียน</span>
                                    </label>

                                    <label class="psy-option-card" for="create_disa_no">
                                        <input class="form-check-input @error('disa_no') is-invalid @enderror"
                                               type="radio"
                                               name="disa_no"
                                               id="create_disa_no"
                                               value="no"
                                               {{ old('disa_no', 'no') == 'no' ? 'checked' : '' }}>
                                        <span>ไม่ขึ้นทะเบียน</span>
                                    </label>
                                </div>
                                @error('disa_no')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
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