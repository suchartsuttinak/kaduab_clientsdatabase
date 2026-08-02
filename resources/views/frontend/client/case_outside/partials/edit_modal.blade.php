@php
    $caseOutsideFollowOptions = [
        ['value' => 'หน่วยงานไปเอง', 'icon' => 'bi-building-check'],
        ['value' => 'โทรศัพท์', 'icon' => 'bi-telephone-outbound'],
        ['value' => 'จดหมาย', 'icon' => 'bi-envelope-paper'],
    ];
@endphp

@foreach($caseoutsides as $case)
    @php
        $isEditError = old('_form_context') === 'edit_case_outside'
            && (int) old('case_id') === (int) $case->id;

        $editDate = $isEditError
            ? old('date')
            : \Carbon\Carbon::parse($case->date)->format('Y-m-d');

        $editOutsideId = $isEditError ? old('outside_id') : $case->outside_id;
        $editDormitory = $isEditError ? old('dormitory') : $case->dormitory;
        $editFollowMethod = $isEditError ? old('follo_no') : $case->follo_no;
        $editResults = $isEditError ? old('results') : $case->results;
        $editTeacher = $isEditError ? old('teacher') : $case->teacher;
        $editRemark = $isEditError ? old('remerk') : $case->remerk;
    @endphp

    <div class="modal fade co-modal"
         id="editCaseOutsideModal{{ $case->id }}"
         tabindex="-1"
         aria-labelledby="editCaseOutsideModalLabel{{ $case->id }}"
         aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <form action="{{ route('case_outside.update', $case->id) }}"
                      method="POST"
                      class="co-modal-form"
                      data-case-outside-form
                      novalidate>
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <input type="hidden" name="case_id" value="{{ $case->id }}">
                    <input type="hidden" name="_form_context" value="edit_case_outside">

                    <div class="modal-header">
                        <div class="co-modal-heading">
                            <span class="co-modal-heading-icon" aria-hidden="true">
                                <i class="bi bi-pencil-square"></i>
                            </span>
                            <div>
                                <h2 class="co-modal-title"
                                    id="editCaseOutsideModalLabel{{ $case->id }}">
                                    แก้ไขข้อมูลการติดตาม ครั้งที่ {{ $case->count ?? $loop->iteration }}
                                </h2>
                                <p class="co-modal-subtitle">
                                    แก้ไขข้อมูลให้ถูกต้องและครบถ้วนก่อนบันทึก
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
                                    <label for="edit_case_outside_date_{{ $case->id }}" class="co-label">
                                        วันที่ติดตาม <span class="co-required">*</span>
                                    </label>
                                    <input type="date"
                                           id="edit_case_outside_date_{{ $case->id }}"
                                           name="date"
                                           value="{{ $editDate }}"
                                           max="{{ now('Asia/Bangkok')->toDateString() }}"
                                           class="form-control co-control {{ $isEditError && $errors->has('date') ? 'is-invalid' : '' }}"
                                           required>
                                    @if($isEditError)
                                        @error('date')
                                            <div class="co-invalid-text">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>

                                <div class="col-12 col-md-8">
                                    <label for="edit_case_outside_outside_id_{{ $case->id }}" class="co-label">
                                        สาเหตุที่พักอาศัยอยู่ภายนอก <span class="co-required">*</span>
                                    </label>
                                    <select id="edit_case_outside_outside_id_{{ $case->id }}"
                                            name="outside_id"
                                            class="form-select co-select {{ $isEditError && $errors->has('outside_id') ? 'is-invalid' : '' }}"
                                            required>
                                        <option value="">-- เลือกสาเหตุ --</option>
                                        @foreach($outside as $item)
                                            <option value="{{ $item->id }}"
                                                    {{ (string) $editOutsideId === (string) $item->id ? 'selected' : '' }}>
                                                {{ $item->outside_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($isEditError)
                                        @error('outside_id')
                                            <div class="co-invalid-text">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>

                                <div class="col-12">
                                    <label for="edit_case_outside_dormitory_{{ $case->id }}" class="co-label">
                                        สถานที่พัก <span class="co-required">*</span>
                                    </label>
                                    <input type="text"
                                           id="edit_case_outside_dormitory_{{ $case->id }}"
                                           name="dormitory"
                                           value="{{ $editDormitory }}"
                                           maxlength="255"
                                           class="form-control co-control {{ $isEditError && $errors->has('dormitory') ? 'is-invalid' : '' }}"
                                           required>
                                    @if($isEditError)
                                        @error('dormitory')
                                            <div class="co-invalid-text">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>
                            </div>
                        </section>

                        <section class="co-section {{ $isEditError && $errors->has('follo_no') ? 'co-section-error' : '' }}"
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
                                    @php($optionId = 'edit_follo_no_' . $case->id . '_' . $loop->index)
                                    <div class="co-option">
                                        <input type="radio"
                                               class="co-option-input"
                                               name="follo_no"
                                               id="{{ $optionId }}"
                                               value="{{ $option['value'] }}"
                                               {{ $editFollowMethod === $option['value'] ? 'checked' : '' }}
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

                            @if($isEditError)
                                @error('follo_no')
                                    <div class="co-invalid-text">{{ $message }}</div>
                                @enderror
                            @endif

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
                                    <label for="edit_case_outside_results_{{ $case->id }}" class="co-label">
                                        ผลการติดตาม <span class="co-required">*</span>
                                    </label>
                                    <textarea id="edit_case_outside_results_{{ $case->id }}"
                                              name="results"
                                              rows="4"
                                              maxlength="5000"
                                              class="form-control co-textarea {{ $isEditError && $errors->has('results') ? 'is-invalid' : '' }}"
                                              required>{{ $editResults }}</textarea>
                                    @if($isEditError)
                                        @error('results')
                                            <div class="co-invalid-text">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="edit_case_outside_teacher_{{ $case->id }}" class="co-label">
                                        ผู้ติดตาม
                                    </label>
                                    <input type="text"
                                           id="edit_case_outside_teacher_{{ $case->id }}"
                                           name="teacher"
                                           value="{{ $editTeacher }}"
                                           maxlength="255"
                                           class="form-control co-control {{ $isEditError && $errors->has('teacher') ? 'is-invalid' : '' }}">
                                    @if($isEditError)
                                        @error('teacher')
                                            <div class="co-invalid-text">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>

                                <div class="col-12">
                                    <label for="edit_case_outside_remerk_{{ $case->id }}" class="co-label">
                                        หมายเหตุ
                                    </label>
                                    <textarea id="edit_case_outside_remerk_{{ $case->id }}"
                                              name="remerk"
                                              rows="3"
                                              maxlength="5000"
                                              class="form-control co-textarea {{ $isEditError && $errors->has('remerk') ? 'is-invalid' : '' }}">{{ $editRemark }}</textarea>
                                    @if($isEditError)
                                        @error('remerk')
                                            <div class="co-invalid-text">{{ $message }}</div>
                                        @enderror
                                    @endif
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
                            <span>บันทึกการแก้ไข</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
