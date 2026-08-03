@foreach ($jobAgencies as $job)
    @php
        $isOldEdit = (string) old('job_id') === (string) $job->id;
        $editDate = $isOldEdit
            ? old('job_date')
            : ($job->job_date ? \Carbon\Carbon::parse($job->job_date)->format('Y-m-d') : '');
        $editOccupationId = $isOldEdit ? old('occupation_id') : $job->occupation_id;
    @endphp

    <div class="modal fade ja-modal ja-modal-edit"
         id="editJobAgencyModal{{ $job->id }}"
         tabindex="-1"
         aria-labelledby="editJobAgencyModalLabel{{ $job->id }}"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <form id="editJobAgencyForm{{ $job->id }}"
                      action="{{ route('job_agencies.update', $job->id) }}"
                      method="POST"
                      class="ja-modal-form jobagency-validate-form"
                      novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <input type="hidden" name="job_id" value="{{ $job->id }}">

                    <div class="modal-header">
                        <h5 class="modal-title ja-modal-title" id="editJobAgencyModalLabel{{ $job->id }}">
                            <i class="bi bi-pencil-square"></i>
                            <span>แก้ไขข้อมูลการจัดหางาน</span>
                        </h5>
                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="ปิด"></button>
                    </div>

                    <div class="ja-modal-body">
                        <div class="ja-section">
                            <div class="ja-section-title">
                                <i class="bi bi-ui-checks-grid"></i>
                                <span>ข้อมูลการทำงาน</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="ja-label" for="edit_job_date_{{ $job->id }}">
                                        วันที่เริ่มงาน <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           id="edit_job_date_{{ $job->id }}"
                                           name="job_date"
                                           value="{{ $editDate }}"
                                           max="{{ now('Asia/Bangkok')->toDateString() }}"
                                           class="form-control ja-control {{ $isOldEdit && $errors->has('job_date') ? 'is-invalid' : '' }}"
                                           required>
                                    @if($isOldEdit && $errors->has('job_date'))
                                        <div class="ja-invalid-text" data-server-error>{{ $errors->first('job_date') }}</div>
                                    @endif
                                </div>

                                <div class="col-12 col-md-8">
                                    <label class="ja-label" for="edit_occupation_id_{{ $job->id }}">
                                        อาชีพ <span class="text-danger">*</span>
                                    </label>
                                    <select id="edit_occupation_id_{{ $job->id }}"
                                            name="occupation_id"
                                            class="form-select ja-select {{ $isOldEdit && $errors->has('occupation_id') ? 'is-invalid' : '' }}"
                                            required>
                                        <option value="">-- เลือกอาชีพ --</option>
                                        @foreach($occupations as $occupation)
                                            <option value="{{ $occupation->id }}"
                                                @selected((string) $editOccupationId === (string) $occupation->id)>
                                                {{ $occupation->occupation_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($isOldEdit && $errors->has('occupation_id'))
                                        <div class="ja-invalid-text" data-server-error>{{ $errors->first('occupation_id') }}</div>
                                    @endif
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="ja-label" for="edit_position_{{ $job->id }}">
                                        ตำแหน่งงาน <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="edit_position_{{ $job->id }}"
                                           name="position"
                                           value="{{ $isOldEdit ? old('position') : $job->position }}"
                                           maxlength="255"
                                           class="form-control ja-control {{ $isOldEdit && $errors->has('position') ? 'is-invalid' : '' }}"
                                           required>
                                    @if($isOldEdit && $errors->has('position'))
                                        <div class="ja-invalid-text" data-server-error>{{ $errors->first('position') }}</div>
                                    @endif
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="ja-label" for="edit_income_{{ $job->id }}">
                                        รายได้ (บาท/เดือน) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           id="edit_income_{{ $job->id }}"
                                           name="income"
                                           value="{{ $isOldEdit ? old('income') : $job->income }}"
                                           min="0"
                                           step="0.01"
                                           inputmode="decimal"
                                           class="form-control ja-control {{ $isOldEdit && $errors->has('income') ? 'is-invalid' : '' }}"
                                           required>
                                    @if($isOldEdit && $errors->has('income'))
                                        <div class="ja-invalid-text" data-server-error>{{ $errors->first('income') }}</div>
                                    @endif
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="ja-label" for="edit_company_{{ $job->id }}">
                                        บริษัท/หน่วยงาน <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="edit_company_{{ $job->id }}"
                                           name="company"
                                           value="{{ $isOldEdit ? old('company') : $job->company }}"
                                           maxlength="255"
                                           class="form-control ja-control {{ $isOldEdit && $errors->has('company') ? 'is-invalid' : '' }}"
                                           required>
                                    @if($isOldEdit && $errors->has('company'))
                                        <div class="ja-invalid-text" data-server-error>{{ $errors->first('company') }}</div>
                                    @endif
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="ja-label" for="edit_coordinator_{{ $job->id }}">
                                        ผู้ประสานงาน <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="edit_coordinator_{{ $job->id }}"
                                           name="coordinator"
                                           value="{{ $isOldEdit ? old('coordinator') : $job->coordinator }}"
                                           maxlength="255"
                                           class="form-control ja-control {{ $isOldEdit && $errors->has('coordinator') ? 'is-invalid' : '' }}"
                                           required>
                                    @if($isOldEdit && $errors->has('coordinator'))
                                        <div class="ja-invalid-text" data-server-error>{{ $errors->first('coordinator') }}</div>
                                    @endif
                                </div>

                                <div class="col-12">
                                    <label class="ja-label" for="edit_remark_{{ $job->id }}">หมายเหตุ</label>
                                    <textarea id="edit_remark_{{ $job->id }}"
                                              name="remark"
                                              class="form-control ja-textarea {{ $isOldEdit && $errors->has('remark') ? 'is-invalid' : '' }}"
                                              maxlength="2000"
                                              rows="3">{{ $isOldEdit ? old('remark') : $job->remark }}</textarea>
                                    @if($isOldEdit && $errors->has('remark'))
                                        <div class="ja-invalid-text" data-server-error>{{ $errors->first('remark') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ja-modal-footer">
                        <button type="button"
                                class="btn btn-outline-secondary ja-btn"
                                data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i>
                            <span>ปิด</span>
                        </button>

                        <button type="submit" class="btn ja-btn ja-btn-primary">
                            <i class="bi bi-save"></i>
                            <span>บันทึกการแก้ไข</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
