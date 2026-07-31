@php
    $isEdit = isset($accident) && $accident;
    $oldTreatNo = old('treat_no', $accident->treat_no ?? '');
    $formPrefix = $isEdit ? 'accident_edit' : 'accident_create';
    $today = now('Asia/Bangkok')->toDateString();
@endphp

<div class="modal fade"
     id="accidentFormModal"
     tabindex="-1"
     aria-labelledby="accidentFormModalLabel"
     aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false">

    <div class="modal-dialog">
        <div class="modal-content">
            <form id="accidentForm"
                  class="acc-modal-form"
                  method="POST"
                  action="{{ $isEdit ? route('accident.update', $accident->id) : route('accident.store') }}"
                  novalidate>

                @csrf

                @if($isEdit)
                    @method('PUT')
                @endif

                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="_form_context" value="accident_form">

                <div class="modal-header">
                    <div class="acc-modal-heading">
                        <div class="acc-modal-heading-icon" aria-hidden="true">
                            <i class="bi {{ $isEdit ? 'bi-pencil-square' : 'bi-shield-plus' }}"></i>
                        </div>

                        <div class="acc-modal-heading-content">
                            <h2 class="acc-modal-title" id="accidentFormModalLabel">
                                {{ $isEdit ? 'แก้ไขข้อมูลการบาดเจ็บ' : 'เพิ่มข้อมูลการบาดเจ็บ' }}
                            </h2>
                            <p class="acc-modal-subtitle">
                                ระบุข้อมูลเหตุการณ์ การรักษา และมาตรการป้องกันให้ครบถ้วน
                            </p>
                        </div>
                    </div>

                    <span class="acc-mode-badge">
                        <i class="bi {{ $isEdit ? 'bi-pencil' : 'bi-plus-lg' }}"></i>
                        {{ $isEdit ? 'โหมดแก้ไข' : 'รายการใหม่' }}
                    </span>

                    @if($isEdit)
                        <a href="{{ route('accident.add', $client->id) }}"
                           class="acc-modal-close"
                           aria-label="ยกเลิกการแก้ไข">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                        </a>
                    @else
                        <button type="button"
                                class="acc-modal-close"
                                data-bs-dismiss="modal"
                                aria-label="ปิดหน้าต่าง">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                        </button>
                    @endif
                </div>

                <div class="modal-body acc-modal-body">
                    <section class="acc-form-section" aria-labelledby="accidentBasicSectionTitle">
                        <div class="acc-section-heading">
                            <div class="acc-section-icon" aria-hidden="true">
                                <i class="bi bi-calendar2-event"></i>
                            </div>
                            <div>
                                <h3 class="acc-section-title" id="accidentBasicSectionTitle">ข้อมูลเหตุการณ์</h3>
                                <p class="acc-section-description">วันเกิดเหตุ สถานที่ ผู้พบเห็น และรายละเอียดการบาดเจ็บ</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-5 col-xl-3">
                                <div class="acc-field">
                                    <label for="{{ $formPrefix }}_incident_date" class="acc-label">
                                        วันที่เกิดเหตุ <span class="acc-required">*</span>
                                    </label>
                                    <input type="date"
                                           id="{{ $formPrefix }}_incident_date"
                                           name="incident_date"
                                           value="{{ old('incident_date', \App\Helpers\ThaiDateHelper::toInputDate($accident->incident_date ?? null)) }}"
                                           max="{{ $today }}"
                                           class="form-control @error('incident_date') is-invalid @enderror"
                                           aria-invalid="{{ $errors->has('incident_date') ? 'true' : 'false' }}"
                                           required>
                                    @error('incident_date')
                                        <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-7 col-xl-5">
                                <div class="acc-field">
                                    <label for="{{ $formPrefix }}_location" class="acc-label">
                                        สถานที่เกิดเหตุ <span class="acc-required">*</span>
                                    </label>
                                    <input type="text"
                                           id="{{ $formPrefix }}_location"
                                           name="location"
                                           value="{{ old('location', $accident->location ?? '') }}"
                                           class="form-control @error('location') is-invalid @enderror"
                                           placeholder="เช่น ห้องเรียน สนามเด็กเล่น หรือหอพัก"
                                           maxlength="255"
                                           autocomplete="off"
                                           aria-invalid="{{ $errors->has('location') ? 'true' : 'false' }}"
                                           required>
                                    @error('location')
                                        <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-xl-4">
                                <div class="acc-field">
                                    <label for="{{ $formPrefix }}_eyewitness" class="acc-label">ผู้พบเห็นเหตุการณ์</label>
                                    <input type="text"
                                           id="{{ $formPrefix }}_eyewitness"
                                           name="eyewitness"
                                           value="{{ old('eyewitness', $accident->eyewitness ?? '') }}"
                                           class="form-control @error('eyewitness') is-invalid @enderror"
                                           placeholder="ระบุชื่อผู้พบเห็นเหตุการณ์"
                                           maxlength="255"
                                           autocomplete="off"
                                           aria-invalid="{{ $errors->has('eyewitness') ? 'true' : 'false' }}">
                                    @error('eyewitness')
                                        <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="acc-field">
                                    <label for="{{ $formPrefix }}_detail" class="acc-label">
                                        รายละเอียดการบาดเจ็บ <span class="acc-required">*</span>
                                    </label>
                                    <textarea id="{{ $formPrefix }}_detail"
                                              name="detail"
                                              rows="4"
                                              class="form-control @error('detail') is-invalid @enderror"
                                              placeholder="อธิบายลักษณะเหตุการณ์ อวัยวะที่บาดเจ็บ และระดับความรุนแรงโดยสังเขป"
                                              maxlength="3000"
                                              aria-invalid="{{ $errors->has('detail') ? 'true' : 'false' }}"
                                              required>{{ old('detail', $accident->detail ?? '') }}</textarea>
                                    @error('detail')
                                        <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="acc-field">
                                    <label for="{{ $formPrefix }}_cause" class="acc-label">
                                        สาเหตุของการบาดเจ็บ <span class="acc-required">*</span>
                                    </label>
                                    <input type="text"
                                           id="{{ $formPrefix }}_cause"
                                           name="cause"
                                           value="{{ old('cause', $accident->cause ?? '') }}"
                                           class="form-control @error('cause') is-invalid @enderror"
                                           placeholder="เช่น ลื่นล้ม ชนของแข็ง หรือถูกของมีคมบาด"
                                           maxlength="500"
                                           autocomplete="off"
                                           aria-invalid="{{ $errors->has('cause') ? 'true' : 'false' }}"
                                           required>
                                    @error('cause')
                                        <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="acc-form-section" aria-labelledby="accidentMedicalSectionTitle">
                        <div class="acc-section-heading">
                            <div class="acc-section-icon" aria-hidden="true">
                                <i class="bi bi-heart-pulse"></i>
                            </div>
                            <div>
                                <h3 class="acc-section-title" id="accidentMedicalSectionTitle">การรักษาและการพบแพทย์</h3>
                                <p class="acc-section-description">เลือกสถานะการพบแพทย์ และบันทึกข้อมูลการรักษาที่เกี่ยวข้อง</p>
                            </div>
                        </div>

                        <div class="acc-radio-wrap @error('treat_no') acc-radio-invalid @enderror" data-treat-wrap>
                            <label class="acc-label d-block">
                                การพบแพทย์ <span class="acc-required">*</span>
                            </label>

                            <div class="acc-radio-grid" role="radiogroup" aria-label="การพบแพทย์">
                                <div class="acc-radio-card">
                                    <input type="radio"
                                           class="acc-radio-input"
                                           name="treat_no"
                                           id="{{ $formPrefix }}_treat_yes"
                                           value="พบแพทย์"
                                           {{ $oldTreatNo === 'พบแพทย์' ? 'checked' : '' }}
                                           required>
                                    <label class="acc-radio-label" for="{{ $formPrefix }}_treat_yes">
                                        <span class="acc-radio-icon"><i class="bi bi-hospital"></i></span>
                                        <span>พบแพทย์</span>
                                        <span class="acc-radio-check"><i class="bi bi-check-lg"></i></span>
                                    </label>
                                </div>

                                <div class="acc-radio-card">
                                    <input type="radio"
                                           class="acc-radio-input"
                                           name="treat_no"
                                           id="{{ $formPrefix }}_treat_no"
                                           value="ไม่พบแพทย์"
                                           {{ $oldTreatNo === 'ไม่พบแพทย์' ? 'checked' : '' }}
                                           required>
                                    <label class="acc-radio-label" for="{{ $formPrefix }}_treat_no">
                                        <span class="acc-radio-icon"><i class="bi bi-house-heart"></i></span>
                                        <span>ไม่พบแพทย์</span>
                                        <span class="acc-radio-check"><i class="bi bi-check-lg"></i></span>
                                    </label>
                                </div>
                            </div>

                            @error('treat_no')
                                <div class="acc-invalid-feedback" data-server-error="true">{{ $message }}</div>
                            @enderror

                            <div class="acc-invalid-feedback d-none" data-treat-client-error>
                                กรุณาเลือกสถานะการพบแพทย์
                            </div>
                        </div>

                        <div class="acc-medical-panel" data-medical-panel aria-hidden="false">
                            <div class="row g-3">
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="acc-field">
                                        <label for="{{ $formPrefix }}_hospital" class="acc-label">สถานพยาบาล</label>
                                        <input type="text"
                                               id="{{ $formPrefix }}_hospital"
                                               name="hospital"
                                               value="{{ old('hospital', $accident->hospital ?? '') }}"
                                               class="form-control @error('hospital') is-invalid @enderror"
                                               placeholder="เช่น โรงพยาบาลหรือคลินิก"
                                               maxlength="255"
                                               autocomplete="off"
                                               data-medical-field
                                               aria-invalid="{{ $errors->has('hospital') ? 'true' : 'false' }}">
                                        @error('hospital')
                                            <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="acc-field">
                                        <label for="{{ $formPrefix }}_diagnosis" class="acc-label">ผลวินิจฉัย</label>
                                        <input type="text"
                                               id="{{ $formPrefix }}_diagnosis"
                                               name="diagnosis"
                                               value="{{ old('diagnosis', $accident->diagnosis ?? '') }}"
                                               class="form-control @error('diagnosis') is-invalid @enderror"
                                               placeholder="ระบุผลการตรวจหรือวินิจฉัย"
                                               maxlength="500"
                                               autocomplete="off"
                                               data-medical-field
                                               aria-invalid="{{ $errors->has('diagnosis') ? 'true' : 'false' }}">
                                        @error('diagnosis')
                                            <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="acc-field">
                                        <label for="{{ $formPrefix }}_appointment" class="acc-label">แพทย์นัดครั้งต่อไป</label>
                                        <input type="date"
                                               id="{{ $formPrefix }}_appointment"
                                               name="appointment"
                                               value="{{ old('appointment', \App\Helpers\ThaiDateHelper::toInputDate($accident->appointment ?? null)) }}"
                                               class="form-control @error('appointment') is-invalid @enderror"
                                               data-medical-field
                                               aria-invalid="{{ $errors->has('appointment') ? 'true' : 'false' }}">
                                        @error('appointment')
                                            <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-0">
                            <div class="col-12 col-md-6">
                                <div class="acc-field">
                                    <label for="{{ $formPrefix }}_treatment" class="acc-label">การรักษา</label>
                                    <textarea id="{{ $formPrefix }}_treatment"
                                              name="treatment"
                                              rows="3"
                                              class="form-control @error('treatment') is-invalid @enderror"
                                              placeholder="เช่น ทำแผล ทายา รับประทานยา หรือพักสังเกตอาการ"
                                              maxlength="2000"
                                              aria-invalid="{{ $errors->has('treatment') ? 'true' : 'false' }}">{{ old('treatment', $accident->treatment ?? '') }}</textarea>
                                    @error('treatment')
                                        <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="acc-field">
                                    <label for="{{ $formPrefix }}_protection" class="acc-label">การป้องกันและการแก้ไข</label>
                                    <textarea id="{{ $formPrefix }}_protection"
                                              name="protection"
                                              rows="3"
                                              class="form-control @error('protection') is-invalid @enderror"
                                              placeholder="ระบุมาตรการป้องกันไม่ให้เกิดเหตุซ้ำ"
                                              maxlength="2000"
                                              aria-invalid="{{ $errors->has('protection') ? 'true' : 'false' }}">{{ old('protection', $accident->protection ?? '') }}</textarea>
                                    @error('protection')
                                        <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="acc-form-section" aria-labelledby="accidentRecordSectionTitle">
                        <div class="acc-section-heading">
                            <div class="acc-section-icon" aria-hidden="true">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div>
                                <h3 class="acc-section-title" id="accidentRecordSectionTitle">ผู้ดูแลและการบันทึก</h3>
                                <p class="acc-section-description">ระบุผู้ดูแลและวันที่จัดทำบันทึกรายการนี้</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-7">
                                <div class="acc-field">
                                    <label for="{{ $formPrefix }}_caretaker" class="acc-label">ผู้ดูแล</label>
                                    <input type="text"
                                           id="{{ $formPrefix }}_caretaker"
                                           name="caretaker"
                                           value="{{ old('caretaker', $accident->caretaker ?? '') }}"
                                           class="form-control @error('caretaker') is-invalid @enderror"
                                           placeholder="ระบุชื่อผู้ดูแลหรือผู้รับผิดชอบ"
                                           maxlength="255"
                                           autocomplete="off"
                                           aria-invalid="{{ $errors->has('caretaker') ? 'true' : 'false' }}">
                                    @error('caretaker')
                                        <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-5">
                                <div class="acc-field">
                                    <label for="{{ $formPrefix }}_record_date" class="acc-label">
                                        วันที่บันทึก <span class="acc-required">*</span>
                                    </label>
                                    <input type="date"
                                           id="{{ $formPrefix }}_record_date"
                                           name="record_date"
                                           value="{{ old('record_date', \App\Helpers\ThaiDateHelper::toInputDate($accident->record_date ?? null) ?: $today) }}"
                                           max="{{ $today }}"
                                           class="form-control @error('record_date') is-invalid @enderror"
                                           aria-invalid="{{ $errors->has('record_date') ? 'true' : 'false' }}"
                                           required>
                                    @error('record_date')
                                        <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="modal-footer acc-modal-footer">
                    @if($isEdit)
                        <a href="{{ route('accident.add', $client->id) }}"
                           class="acc-modal-btn acc-modal-btn-cancel">
                            <i class="bi bi-x-circle"></i>
                            <span>ยกเลิกการแก้ไข</span>
                        </a>
                    @else
                        <button type="button"
                                class="acc-modal-btn acc-modal-btn-cancel"
                                data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i>
                            <span>ยกเลิก</span>
                        </button>
                    @endif

                    <button type="submit" class="acc-modal-btn acc-modal-btn-save">
                        <i class="bi bi-save2"></i>
                        <span>{{ $isEdit ? 'อัปเดตข้อมูล' : 'บันทึกข้อมูล' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div