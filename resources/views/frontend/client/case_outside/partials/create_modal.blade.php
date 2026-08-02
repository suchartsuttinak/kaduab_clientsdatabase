@php
    $caseOutsideFollowOptions = [
        ['value' => 'หน่วยงานไปเอง', 'icon' => 'bi-building-check'],
        ['value' => 'โทรศัพท์', 'icon' => 'bi-telephone-outbound'],
        ['value' => 'จดหมาย', 'icon' => 'bi-envelope-paper'],
    ];
@endphp

<div class="modal fade co-modal"
     id="createCaseOutsideModal"
     tabindex="-1"
     aria-labelledby="createCaseOutsideModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="createCaseOutsideForm"
                  action="{{ route('case_outside.store') }}"
                  method="POST"
                  class="co-modal-form"
                  data-case-outside-form
                  novalidate>
                @csrf

                <input type="hidden" name="client_id" value="{{ old('client_id', $client->id) }}">
                <input type="hidden" name="_form_context" value="create_case_outside">

                <div class="modal-header">
                    <div class="co-modal-heading">
                        <span class="co-modal-heading-icon" aria-hidden="true">
                            <i class="bi bi-geo-alt"></i>
                        </span>

                        <div>
                            <h2 class="co-modal-title" id="createCaseOutsideModalLabel">
                                เพิ่มข้อมูลการติดตาม
                            </h2>
                            <p class="co-modal-subtitle">
                                บันทึกข้อมูลผู้รับบริการที่พักอาศัยอยู่ภายนอก
                            </p>
                        </div>
                    </div>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="ปิดหน้าต่าง">
                    </button>
                </div>

                <div class="modal-body co-modal-body">
                    <section class="co-section">
                        <div class="co-section-header">
                            <span class="co-section-icon" aria-hidden="true">
                                <i class="bi bi-ui-checks-grid"></i>
                            </span>
                            <div>
                                <h3 class="co-section-title">ข้อมูลพื้นฐาน</h3>
                                <p class="co-section-description">
                                    ระบุวันที่ สาเหตุ และสถานที่พักของผู้รับบริการ
                                </p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="create_case_outside_date" class="co-label">
                                    วันที่ติดตาม <span class="co-required">*</span>
                                </label>
                                <input type="date"
                                       id="create_case_outside_date"
                                       name="date"
                                       value="{{ old('date', now('Asia/Bangkok')->toDateString()) }}"
                                       max="{{ now('Asia/Bangkok')->toDateString() }}"
                                       class="form-control co-control @error('date') is-invalid @enderror"
                                       required>
                                @error('date')
                                    <div class="co-invalid-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-8">
                                <label for="create_case_outside_outside_id" class="co-label">
                                    สาเหตุที่พักอาศัยอยู่ภายนอก <span class="co-required">*</span>
                                </label>
                                <select id="create_case_outside_outside_id"
                                        name="outside_id"
                                        class="form-select co-select @error('outside_id') is-invalid @enderror"
                                        required>
                                    <option value="">-- เลือกสาเหตุ --</option>
                                    @foreach($outside as $item)
                                        <option value="{{ $item->id }}"
                                                {{ (string) old('outside_id') === (string) $item->id ? 'selected' : '' }}>
                                            {{ $item->outside_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('outside_id')
                                    <div class="co-invalid-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="create_case_outside_dormitory" class="co-label">
                                    สถานที่พัก <span class="co-required">*</span>
                                </label>
                                <input type="text"
                                       id="create_case_outside_dormitory"
                                       name="dormitory"
                                       value="{{ old('dormitory') }}"
                                       maxlength="255"
                                       class="form-control co-control @error('dormitory') is-invalid @enderror"
                                       placeholder="ระบุชื่อสถานที่พัก บ้านพัก หรือที่อยู่โดยสังเขป"
                                       required>
                                @error('dormitory')
                                    <div class="co-invalid-text">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="co-section @error('follo_no') co-section-error @enderror"
                             data-follow-section>
                        <div class="co-section-header">
                            <span class="co-section-icon" aria-hidden="true">
                                <i class="bi bi-check2-square"></i>
                            </span>
                            <div>
                                <h3 class="co-section-title">การดำเนินงาน</h3>
                                <p class="co-section-description">
                                    เลือกวิธีที่ใช้ในการติดตามผู้รับบริการ
                                </p>
                            </div>
                        </div>

                        <div class="co-option-grid" role="radiogroup" aria-label="การดำเนินงาน">
                            @foreach($caseOutsideFollowOptions as $option)
                                @php($optionId = 'create_follo_no_' . $loop->index)
                                <div class="co-option">
                                    <input type="radio"
                                           class="co-option-input"
                                           name="follo_no"
                                           id="{{ $optionId }}"
                                           value="{{ $option['value'] }}"
                                           {{ old('follo_no') === $option['value'] ? 'checked' : '' }}
                                           required>
                                    <label class="co-option-label" for="{{ $optionId }}">
                                        <span class="co-option-icon" aria-hidden="true">
                                            <i class="bi {{ $option['icon'] }}"></i>
                                        </span>
                                        <span>{{ $option['value'] }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        @error('follo_no')
                            <div class="co-invalid-text">{{ $message }}</div>
                        @enderror

                        <div class="co-invalid-text d-none" data-client-error>
                            กรุณาเลือกการดำเนินงาน
                        </div>
                    </section>

                    <section class="co-section">
                        <div class="co-section-header">
                            <span class="co-section-icon" aria-hidden="true">
                                <i class="bi bi-card-text"></i>
                            </span>
                            <div>
                                <h3 class="co-section-title">ผลการติดตามและรายละเอียดเพิ่มเติม</h3>
                                <p class="co-section-description">
                                    บันทึกผล ผู้ติดตาม และหมายเหตุที่เกี่ยวข้อง
                                </p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="create_case_outside_results" class="co-label">
                                    ผลการติดตาม <span class="co-required">*</span>
                                </label>
                                <textarea id="create_case_outside_results"
                                          name="results"
                                          rows="4"
                                          maxlength="5000"
                                          class="form-control co-textarea @error('results') is-invalid @enderror"
                                          placeholder="ระบุผลที่ได้รับจากการติดตามและการดำเนินงาน"
                                          required>{{ old('results') }}</textarea>
                                @error('results')
                                    <div class="co-invalid-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="create_case_outside_teacher" class="co-label">
                                    ผู้ติดตาม
                                </label>
                                <input type="text"
                                       id="create_case_outside_teacher"
                                       name="teacher"
                                       value="{{ old('teacher') }}"
                                       maxlength="255"
                                       class="form-control co-control @error('teacher') is-invalid @enderror"
                                       placeholder="ชื่อผู้ดำเนินการติดตาม">
                                @error('teacher')
                                    <div class="co-invalid-text">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="create_case_outside_remerk" class="co-label">
                                    หมายเหตุ
                                </label>
                                <textarea id="create_case_outside_remerk"
                                          name="remerk"
                                          rows="3"
                                          maxlength="5000"
                                          class="form-control co-textarea @error('remerk') is-invalid @enderror"
                                          placeholder="ระบุข้อมูลเพิ่มเติม หากมี">{{ old('remerk') }}</textarea>
                                @error('remerk')
                                    <div class="co-invalid-text">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </section>
                </div>

                <div class="modal-footer co-modal-footer">
                    <button type="button"
                            class="btn co-btn-cancel"
                            data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        <span>ยกเลิก</span>
                    </button>

                    <button type="submit" class="btn co-btn-save">
                        <i class="bi bi-save2"></i>
                        <span>บันทึกข้อมูล</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
