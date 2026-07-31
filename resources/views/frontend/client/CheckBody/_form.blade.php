@php
    use App\Helpers\ThaiDateHelper;

    $isEdit = isset($checkbody) && $checkbody;
    $formPrefix = $isEdit ? 'checkbody_edit' : 'checkbody_create';
    $today = now('Asia/Bangkok')->toDateString();

    $assessorDateValue = old(
        'assessor_date',
        $isEdit
            ? ThaiDateHelper::toInputDate($checkbody->assessor_date ?? null)
            : $today
    );

    $oldDevelopment = old('development', $checkbody->development ?? 'สมวัย');
    $oldDevelopmentType = old('development_type', $checkbody->development_type ?? 'เด็กทั่วไป');
    $oldSpecialSupportType = old('special_support_type', $checkbody->special_support_type ?? '');
@endphp

<div class="modal fade"
     id="checkBodyFormModal"
     tabindex="-1"
     aria-labelledby="checkBodyFormModalLabel"
     aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false">

    <div class="modal-dialog">
        <div class="modal-content">
            <form id="checkBodyForm"
                  class="cb-modal-form"
                  method="POST"
                  action="{{ $isEdit ? route('check_body.update', $checkbody->id) : route('check_body.store') }}"
                  novalidate>

                @csrf

                @if($isEdit)
                    @method('PUT')
                @endif

                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="_form_context" value="checkbody_form">

                <div class="modal-header">
                    <div class="cb-modal-heading">
                        <div class="cb-modal-heading-icon" aria-hidden="true">
                            <i class="bi {{ $isEdit ? 'bi-pencil-square' : 'bi-clipboard2-pulse' }}"></i>
                        </div>

                        <div class="cb-modal-heading-content">
                            <h2 class="cb-modal-title" id="checkBodyFormModalLabel">
                                {{ $isEdit ? 'แก้ไขข้อมูลการตรวจสุขภาพ' : 'เพิ่มข้อมูลการตรวจสุขภาพ' }}
                            </h2>
                            <p class="cb-modal-subtitle">
                                บันทึกผลการตรวจร่างกาย พัฒนาการ การส่งเสริม และข้อมูลสุขภาพที่เกี่ยวข้อง
                            </p>
                        </div>
                    </div>

                    <span class="cb-mode-badge">
                        <i class="bi {{ $isEdit ? 'bi-pencil' : 'bi-plus-lg' }}"></i>
                        {{ $isEdit ? 'โหมดแก้ไข' : 'รายการใหม่' }}
                    </span>

                    @if($isEdit)
                        <a href="{{ route('check_body.add', $client->id) }}"
                           class="cb-modal-close"
                           aria-label="ยกเลิกการแก้ไข">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                        </a>
                    @else
                        <button type="button"
                                class="cb-modal-close"
                                data-bs-dismiss="modal"
                                aria-label="ปิดหน้าต่าง">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                        </button>
                    @endif
                </div>

                <div class="modal-body cb-modal-body">
                    <section class="cb-form-section" aria-labelledby="checkBodyBasicSectionTitle">
                        <div class="cb-section-heading">
                            <div class="cb-section-icon" aria-hidden="true">
                                <i class="bi bi-calendar2-check"></i>
                            </div>
                            <div>
                                <h3 class="cb-section-title" id="checkBodyBasicSectionTitle">ข้อมูลการตรวจ</h3>
                                <p class="cb-section-description">ระบุวันที่ตรวจและชื่อผู้ตรวจหรือผู้บันทึกข้อมูล</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <div class="cb-field">
                                    <label for="{{ $formPrefix }}_assessor_date" class="cb-label">
                                        วันที่ตรวจ <span class="cb-required">*</span>
                                    </label>
                                    <input type="date"
                                           id="{{ $formPrefix }}_assessor_date"
                                           name="assessor_date"
                                           value="{{ $assessorDateValue }}"
                                           max="{{ $today }}"
                                           class="form-control @error('assessor_date') is-invalid @enderror"
                                           aria-invalid="{{ $errors->has('assessor_date') ? 'true' : 'false' }}"
                                           required>
                                    @error('assessor_date')
                                        <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-8">
                                <div class="cb-field">
                                    <label for="{{ $formPrefix }}_recorder" class="cb-label">
                                        ผู้ตรวจ / ผู้บันทึก <span class="cb-required">*</span>
                                    </label>
                                    <input type="text"
                                           id="{{ $formPrefix }}_recorder"
                                           name="recorder"
                                           value="{{ old('recorder', $checkbody->recorder ?? '') }}"
                                           class="form-control @error('recorder') is-invalid @enderror"
                                           placeholder="ระบุชื่อผู้ตรวจหรือผู้บันทึก"
                                           maxlength="255"
                                           autocomplete="off"
                                           aria-invalid="{{ $errors->has('recorder') ? 'true' : 'false' }}"
                                           required>
                                    @error('recorder')
                                        <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="cb-form-section" aria-labelledby="checkBodyDevelopmentSectionTitle">
                        <div class="cb-section-heading">
                            <div class="cb-section-icon" aria-hidden="true">
                                <i class="bi bi-emoji-smile"></i>
                            </div>
                            <div>
                                <h3 class="cb-section-title" id="checkBodyDevelopmentSectionTitle">ผลการประเมินพัฒนาการ</h3>
                                <p class="cb-section-description">เลือกผลการประเมินและระบุรายละเอียดเพิ่มเติมเมื่อพบพัฒนาการไม่สมวัย</p>
                            </div>
                        </div>

                        <div class="cb-radio-wrap @error('development') cb-radio-invalid @enderror"
                             data-development-wrap>
                            <label class="cb-label d-block">
                                พัฒนาการ <span class="cb-required">*</span>
                            </label>

                            <div class="cb-radio-grid" role="radiogroup" aria-label="พัฒนาการ">
                                <div class="cb-radio-card">
                                    <input type="radio"
                                           class="cb-radio-input"
                                           name="development"
                                           id="{{ $formPrefix }}_development_normal"
                                           value="สมวัย"
                                           {{ $oldDevelopment === 'สมวัย' ? 'checked' : '' }}
                                           required>
                                    <label class="cb-radio-label" for="{{ $formPrefix }}_development_normal">
                                        <span class="cb-radio-icon"><i class="bi bi-emoji-smile"></i></span>
                                        <span>สมวัย</span>
                                        <span class="cb-radio-check"><i class="bi bi-check-lg"></i></span>
                                    </label>
                                </div>

                                <div class="cb-radio-card">
                                    <input type="radio"
                                           class="cb-radio-input"
                                           name="development"
                                           id="{{ $formPrefix }}_development_delayed"
                                           value="ไม่สมวัย"
                                           {{ $oldDevelopment === 'ไม่สมวัย' ? 'checked' : '' }}
                                           required>
                                    <label class="cb-radio-label" for="{{ $formPrefix }}_development_delayed">
                                        <span class="cb-radio-icon"><i class="bi bi-exclamation-triangle"></i></span>
                                        <span>ไม่สมวัย</span>
                                        <span class="cb-radio-check"><i class="bi bi-check-lg"></i></span>
                                    </label>
                                </div>
                            </div>

                            @error('development')
                                <div class="cb-invalid-feedback" data-server-error="true">{{ $message }}</div>
                            @enderror

                            <div class="cb-invalid-feedback d-none" data-development-client-error>
                                กรุณาเลือกผลการประเมินพัฒนาการ
                            </div>
                        </div>

                        <div class="cb-conditional-panel {{ $oldDevelopment === 'ไม่สมวัย' ? '' : 'is-hidden' }}"
                             data-development-detail-panel
                             aria-hidden="{{ $oldDevelopment === 'ไม่สมวัย' ? 'false' : 'true' }}">
                            <div class="cb-field">
                                <label for="{{ $formPrefix }}_detail" class="cb-label">
                                    รายละเอียดกรณีพัฒนาการไม่สมวัย
                                </label>
                                <textarea id="{{ $formPrefix }}_detail"
                                          name="detail"
                                          rows="3"
                                          class="form-control @error('detail') is-invalid @enderror"
                                          placeholder="ระบุข้อสังเกต ปัญหาที่พบ หรือแนวทางติดตามเพิ่มเติม"
                                          maxlength="3000"
                                          aria-invalid="{{ $errors->has('detail') ? 'true' : 'false' }}">{{ old('detail', $checkbody->detail ?? '') }}</textarea>
                                @error('detail')
                                    <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="cb-form-section" aria-labelledby="checkBodySupportSectionTitle">
                        <div class="cb-section-heading">
                            <div class="cb-section-icon" aria-hidden="true">
                                <i class="bi bi-stars"></i>
                            </div>
                            <div>
                                <h3 class="cb-section-title" id="checkBodySupportSectionTitle">การส่งเสริมและพัฒนา</h3>
                                <p class="cb-section-description">จำแนกกลุ่มเด็กและระบุประเภทการสนับสนุนเมื่อต้องการการดูแลเฉพาะด้าน</p>
                            </div>
                        </div>

                        <div class="cb-radio-wrap @error('development_type') cb-radio-invalid @enderror"
                             data-development-type-wrap>
                            <label class="cb-label d-block">
                                กลุ่มการส่งเสริมและพัฒนา <span class="cb-required">*</span>
                            </label>

                            <div class="cb-radio-grid" role="radiogroup" aria-label="กลุ่มการส่งเสริมและพัฒนา">
                                <div class="cb-radio-card">
                                    <input type="radio"
                                           class="cb-radio-input"
                                           name="development_type"
                                           id="{{ $formPrefix }}_development_type_general"
                                           value="เด็กทั่วไป"
                                           {{ $oldDevelopmentType === 'เด็กทั่วไป' ? 'checked' : '' }}
                                           required>
                                    <label class="cb-radio-label" for="{{ $formPrefix }}_development_type_general">
                                        <span class="cb-radio-icon"><i class="bi bi-person"></i></span>
                                        <span>เด็กทั่วไป</span>
                                        <span class="cb-radio-check"><i class="bi bi-check-lg"></i></span>
                                    </label>
                                </div>

                                <div class="cb-radio-card">
                                    <input type="radio"
                                           class="cb-radio-input"
                                           name="development_type"
                                           id="{{ $formPrefix }}_development_type_special"
                                           value="เด็กกลุ่มพิเศษ"
                                           {{ $oldDevelopmentType === 'เด็กกลุ่มพิเศษ' ? 'checked' : '' }}
                                           required>
                                    <label class="cb-radio-label" for="{{ $formPrefix }}_development_type_special">
                                        <span class="cb-radio-icon"><i class="bi bi-stars"></i></span>
                                        <span>เด็กกลุ่มพิเศษ</span>
                                        <span class="cb-radio-check"><i class="bi bi-check-lg"></i></span>
                                    </label>
                                </div>
                            </div>

                            @error('development_type')
                                <div class="cb-invalid-feedback" data-server-error="true">{{ $message }}</div>
                            @enderror

                            <div class="cb-invalid-feedback d-none" data-development-type-client-error>
                                กรุณาเลือกกลุ่มการส่งเสริมและพัฒนา
                            </div>
                        </div>

                        <div class="cb-conditional-panel {{ $oldDevelopmentType === 'เด็กกลุ่มพิเศษ' ? '' : 'is-hidden' }}"
                             data-special-support-panel
                             aria-hidden="{{ $oldDevelopmentType === 'เด็กกลุ่มพิเศษ' ? 'false' : 'true' }}">
                            <div class="row g-3">
                                <div class="col-12 col-lg-8">
                                    <div class="cb-field">
                                        <label for="{{ $formPrefix }}_special_support_type" class="cb-label">
                                            ประเภทการสนับสนุน
                                        </label>
                                        <select id="{{ $formPrefix }}_special_support_type"
                                                name="special_support_type"
                                                class="form-select @error('special_support_type') is-invalid @enderror"
                                                aria-invalid="{{ $errors->has('special_support_type') ? 'true' : 'false' }}">
                                            <option value="">-- กรุณาเลือก --</option>
                                            <option value="ต้องการการสนับสนุนด้านการเรียนรู้ (อ่าน เขียน คำนวณ)"
                                                {{ $oldSpecialSupportType === 'ต้องการการสนับสนุนด้านการเรียนรู้ (อ่าน เขียน คำนวณ)' ? 'selected' : '' }}>
                                                ต้องการการสนับสนุนด้านการเรียนรู้ (อ่าน เขียน คำนวณ)
                                            </option>
                                            <option value="ต้องการการสนับสนุนด้านพฤติกรรมและอารมณ์ (การควบคุมอารมณ์, สมาธิ)"
                                                {{ $oldSpecialSupportType === 'ต้องการการสนับสนุนด้านพฤติกรรมและอารมณ์ (การควบคุมอารมณ์, สมาธิ)' ? 'selected' : '' }}>
                                                ต้องการการสนับสนุนด้านพฤติกรรมและอารมณ์ (การควบคุมอารมณ์, สมาธิ)
                                            </option>
                                            <option value="ต้องการการสนับสนุนด้านสังคม (การเข้าสังคม, ทำงานร่วมกับเพื่อน)"
                                                {{ $oldSpecialSupportType === 'ต้องการการสนับสนุนด้านสังคม (การเข้าสังคม, ทำงานร่วมกับเพื่อน)' ? 'selected' : '' }}>
                                                ต้องการการสนับสนุนด้านสังคม (การเข้าสังคม, ทำงานร่วมกับเพื่อน)
                                            </option>
                                            <option value="ต้องการการสนับสนุนด้านร่างกาย (การเคลื่อนไหว, สุขภาพ)"
                                                {{ $oldSpecialSupportType === 'ต้องการการสนับสนุนด้านร่างกาย (การเคลื่อนไหว, สุขภาพ)' ? 'selected' : '' }}>
                                                ต้องการการสนับสนุนด้านร่างกาย (การเคลื่อนไหว, สุขภาพ)
                                            </option>
                                            <option value="มีศักยภาพพิเศษที่ควรส่งเสริม (ดนตรี, กีฬา, ศิลปะ)"
                                                {{ $oldSpecialSupportType === 'มีศักยภาพพิเศษที่ควรส่งเสริม (ดนตรี, กีฬา, ศิลปะ)' ? 'selected' : '' }}>
                                                มีศักยภาพพิเศษที่ควรส่งเสริม (ดนตรี, กีฬา, ศิลปะ)
                                            </option>
                                            <option value="อื่น ๆ" {{ $oldSpecialSupportType === 'อื่น ๆ' ? 'selected' : '' }}>
                                                อื่น ๆ
                                            </option>
                                        </select>
                                        @error('special_support_type')
                                            <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-lg-4 {{ $oldSpecialSupportType === 'อื่น ๆ' ? '' : 'd-none' }}"
                                     data-special-support-other-wrap>
                                    <div class="cb-field">
                                        <label for="{{ $formPrefix }}_special_support_other" class="cb-label">
                                            อื่น ๆ (ระบุ)
                                        </label>
                                        <input type="text"
                                               id="{{ $formPrefix }}_special_support_other"
                                               name="special_support_other"
                                               value="{{ old('special_support_other', $checkbody->special_support_other ?? '') }}"
                                               class="form-control @error('special_support_other') is-invalid @enderror"
                                               placeholder="ระบุประเภทการสนับสนุน"
                                               maxlength="255"
                                               autocomplete="off"
                                               aria-invalid="{{ $errors->has('special_support_other') ? 'true' : 'false' }}">
                                        @error('special_support_other')
                                            <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="cb-form-section" aria-labelledby="checkBodyPhysicalSectionTitle">
                        <div class="cb-section-heading">
                            <div class="cb-section-icon" aria-hidden="true">
                                <i class="bi bi-rulers"></i>
                            </div>
                            <div>
                                <h3 class="cb-section-title" id="checkBodyPhysicalSectionTitle">การตรวจร่างกาย</h3>
                                <p class="cb-section-description">บันทึกข้อมูลสัดส่วน สุขภาพช่องปาก รูปร่าง และข้อสังเกตจากการตรวจร่างกาย</p>
                            </div>
                        </div>

                        <div class="cb-metric-panel">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <div class="cb-field">
                                        <label for="{{ $formPrefix }}_weight" class="cb-label">น้ำหนัก (กก.)</label>
                                        <input type="number"
                                               id="{{ $formPrefix }}_weight"
                                               name="weight"
                                               value="{{ old('weight', $checkbody->weight ?? '') }}"
                                               class="form-control @error('weight') is-invalid @enderror"
                                               step="0.01"
                                               min="0"
                                               placeholder="เช่น 25.50"
                                               inputmode="decimal"
                                               aria-invalid="{{ $errors->has('weight') ? 'true' : 'false' }}">
                                        @error('weight')
                                            <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-lg-3">
                                    <div class="cb-field">
                                        <label for="{{ $formPrefix }}_height" class="cb-label">ส่วนสูง (ซม.)</label>
                                        <input type="number"
                                               id="{{ $formPrefix }}_height"
                                               name="height"
                                               value="{{ old('height', $checkbody->height ?? '') }}"
                                               class="form-control @error('height') is-invalid @enderror"
                                               step="0.01"
                                               min="0"
                                               placeholder="เช่น 120.00"
                                               inputmode="decimal"
                                               aria-invalid="{{ $errors->has('height') ? 'true' : 'false' }}">
                                        @error('height')
                                            <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-lg-3">
                                    <div class="cb-field">
                                        <label for="{{ $formPrefix }}_oral" class="cb-label">สุขภาพช่องปาก</label>
                                        <input type="text"
                                               id="{{ $formPrefix }}_oral"
                                               name="oral"
                                               value="{{ old('oral', $checkbody->oral ?? '') }}"
                                               class="form-control @error('oral') is-invalid @enderror"
                                               placeholder="เช่น ปกติ หรือมีฟันผุ"
                                               maxlength="255"
                                               aria-invalid="{{ $errors->has('oral') ? 'true' : 'false' }}">
                                        @error('oral')
                                            <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6 col-lg-3">
                                    <div class="cb-field">
                                        <label for="{{ $formPrefix }}_appearance" class="cb-label">รูปร่าง / ลักษณะ</label>
                                        <input type="text"
                                               id="{{ $formPrefix }}_appearance"
                                               name="appearance"
                                               value="{{ old('appearance', $checkbody->appearance ?? '') }}"
                                               class="form-control @error('appearance') is-invalid @enderror"
                                               placeholder="เช่น สมส่วน ผอม หรือท้วม"
                                               maxlength="255"
                                               aria-invalid="{{ $errors->has('appearance') ? 'true' : 'false' }}">
                                        @error('appearance')
                                            <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-0">
                            <div class="col-12 col-md-6 col-xl-3">
                                <div class="cb-field">
                                    <label for="{{ $formPrefix }}_wound" class="cb-label">ร่องรอย / บาดแผล</label>
                                    <input type="text"
                                           id="{{ $formPrefix }}_wound"
                                           name="wound"
                                           value="{{ old('wound', $checkbody->wound ?? '') }}"
                                           class="form-control @error('wound') is-invalid @enderror"
                                           placeholder="ระบุสิ่งที่ตรวจพบ"
                                           maxlength="255"
                                           aria-invalid="{{ $errors->has('wound') ? 'true' : 'false' }}">
                                    @error('wound')
                                        <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-3">
                                <div class="cb-field">
                                    <label for="{{ $formPrefix }}_disease" class="cb-label">โรคประจำตัว</label>
                                    <input type="text"
                                           id="{{ $formPrefix }}_disease"
                                           name="disease"
                                           value="{{ old('disease', $checkbody->disease ?? '') }}"
                                           class="form-control @error('disease') is-invalid @enderror"
                                           placeholder="ระบุโรคประจำตัว หากมี"
                                           maxlength="255"
                                           aria-invalid="{{ $errors->has('disease') ? 'true' : 'false' }}">
                                    @error('disease')
                                        <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-3">
                                <div class="cb-field">
                                    <label for="{{ $formPrefix }}_hygiene" class="cb-label">สุขอนามัย</label>
                                    <input type="text"
                                           id="{{ $formPrefix }}_hygiene"
                                           name="hygiene"
                                           value="{{ old('hygiene', $checkbody->hygiene ?? '') }}"
                                           class="form-control @error('hygiene') is-invalid @enderror"
                                           placeholder="เช่น สะอาด หรือควรปรับปรุง"
                                           maxlength="255"
                                           aria-invalid="{{ $errors->has('hygiene') ? 'true' : 'false' }}">
                                    @error('hygiene')
                                        <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-3">
                                <div class="cb-field">
                                    <label for="{{ $formPrefix }}_health" class="cb-label">สุขภาพโดยรวม</label>
                                    <input type="text"
                                           id="{{ $formPrefix }}_health"
                                           name="health"
                                           value="{{ old('health', $checkbody->health ?? '') }}"
                                           class="form-control @error('health') is-invalid @enderror"
                                           placeholder="ระบุผลการประเมินสุขภาพโดยรวม"
                                           maxlength="255"
                                           aria-invalid="{{ $errors->has('health') ? 'true' : 'false' }}">
                                    @error('health')
                                        <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="cb-form-section" aria-labelledby="checkBodyPreventionSectionTitle">
                        <div class="cb-section-heading">
                            <div class="cb-section-icon" aria-hidden="true">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div>
                                <h3 class="cb-section-title" id="checkBodyPreventionSectionTitle">การป้องกันโรคและประวัติสุขภาพ</h3>
                                <p class="cb-section-description">บันทึกข้อมูลการปลูกฝี การฉีดยา วัคซีน โรคติดต่อ การเจ็บป่วย และการแพ้ยา</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            @foreach([
                                ['name' => 'inoculation', 'label' => 'การปลูกฝี'],
                                ['name' => 'injection', 'label' => 'การฉีดยา'],
                                ['name' => 'vaccination', 'label' => 'การให้วัคซีน'],
                                ['name' => 'contagious', 'label' => 'โรคติดต่อ'],
                                ['name' => 'other', 'label' => 'การเจ็บป่วยอื่น ๆ'],
                                ['name' => 'drug_allergy', 'label' => 'ประวัติการแพ้ยา'],
                            ] as $healthField)
                                @php
                                    $fieldName = $healthField['name'];
                                    $fieldValue = old($fieldName, $checkbody->{$fieldName} ?? '');
                                @endphp

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="cb-field">
                                        <label for="{{ $formPrefix }}_{{ $fieldName }}" class="cb-label">
                                            {{ $healthField['label'] }}
                                        </label>
                                        <input type="text"
                                               id="{{ $formPrefix }}_{{ $fieldName }}"
                                               name="{{ $fieldName }}"
                                               value="{{ $fieldValue }}"
                                               class="form-control @error($fieldName) is-invalid @enderror"
                                               maxlength="255"
                                               aria-invalid="{{ $errors->has($fieldName) ? 'true' : 'false' }}">
                                        @error($fieldName)
                                            <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="cb-form-section" aria-labelledby="checkBodyRemarkSectionTitle">
                        <div class="cb-section-heading">
                            <div class="cb-section-icon" aria-hidden="true">
                                <i class="bi bi-card-text"></i>
                            </div>
                            <div>
                                <h3 class="cb-section-title" id="checkBodyRemarkSectionTitle">หมายเหตุเพิ่มเติม</h3>
                                <p class="cb-section-description">ระบุข้อเสนอแนะ การติดตาม หรือข้อมูลอื่นที่จำเป็นต่อการดูแล</p>
                            </div>
                        </div>

                        <div class="cb-field">
                            <label for="{{ $formPrefix }}_remark" class="cb-label">หมายเหตุ</label>
                            <textarea id="{{ $formPrefix }}_remark"
                                      name="remark"
                                      rows="3"
                                      class="form-control @error('remark') is-invalid @enderror"
                                      placeholder="ระบุข้อมูลเพิ่มเติม หากมี"
                                      maxlength="3000"
                                      aria-invalid="{{ $errors->has('remark') ? 'true' : 'false' }}">{{ old('remark', $checkbody->remark ?? '') }}</textarea>
                            @error('remark')
                                <div class="invalid-feedback" data-server-error="true">{{ $message }}</div>
                            @enderror
                        </div>
                    </section>
                </div>

                <div class="modal-footer cb-modal-footer">
                    @if($isEdit)
                        <a href="{{ route('check_body.add', $client->id) }}"
                           class="btn cb-modal-btn cb-modal-btn-cancel">
                            <i class="bi bi-x-circle"></i>
                            ยกเลิก
                        </a>
                    @else
                        <button type="button"
                                class="btn cb-modal-btn cb-modal-btn-cancel"
                                data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i>
                            ยกเลิก
                        </button>
                    @endif

                    <button type="submit" class="btn cb-modal-btn cb-modal-btn-save">
                        <i class="bi bi-save2"></i>
                        {{ $isEdit ? 'อัปเดตข้อมูล' : 'บันทึกข้อมูล' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>