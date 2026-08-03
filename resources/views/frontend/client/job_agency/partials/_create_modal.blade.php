<div class="modal fade ja-modal"
     id="createJobAgencyModal"
     tabindex="-1"
     aria-labelledby="createJobAgencyModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form id="createJobAgencyForm"
                  action="{{ route('job_agencies.store') }}"
                  method="POST"
                  class="ja-modal-form jobagency-validate-form"
                  novalidate>
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">

                <div class="modal-header">
                    <h5 class="modal-title ja-modal-title" id="createJobAgencyModalLabel">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มข้อมูลการจัดหางาน</span>
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
                                <label class="ja-label" for="create_job_date">
                                    วันที่เริ่มงาน <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       id="create_job_date"
                                       name="job_date"
                                       value="{{ old('job_date', now('Asia/Bangkok')->toDateString()) }}"
                                       max="{{ now('Asia/Bangkok')->toDateString() }}"
                                       class="form-control ja-control @error('job_date') is-invalid @enderror"
                                       required>
                                @error('job_date')
                                    <div class="ja-invalid-text" data-server-error>{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-8">
                                <label class="ja-label" for="create_occupation_id">
                                    อาชีพ <span class="text-danger">*</span>
                                </label>
                                <select id="create_occupation_id"
                                        name="occupation_id"
                                        class="form-select ja-select @error('occupation_id') is-invalid @enderror"
                                        required>
                                    <option value="">-- เลือกอาชีพ --</option>
                                    @foreach($occupations as $occupation)
                                        <option value="{{ $occupation->id }}"
                                            @selected((string) old('occupation_id') === (string) $occupation->id)>
                                            {{ $occupation->occupation_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('occupation_id')
                                    <div class="ja-invalid-text" data-server-error>{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="ja-label" for="create_position">
                                    ตำแหน่งงาน <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       id="create_position"
                                       name="position"
                                       value="{{ old('position') }}"
                                       maxlength="255"
                                       class="form-control ja-control @error('position') is-invalid @enderror"
                                       required>
                                @error('position')
                                    <div class="ja-invalid-text" data-server-error>{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="ja-label" for="create_income">
                                    รายได้ (บาท/เดือน) <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       id="create_income"
                                       name="income"
                                       value="{{ old('income') }}"
                                       min="0"
                                       step="0.01"
                                       inputmode="decimal"
                                       class="form-control ja-control @error('income') is-invalid @enderror"
                                       required>
                                @error('income')
                                    <div class="ja-invalid-text" data-server-error>{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="ja-label" for="create_company">
                                    บริษัท/หน่วยงาน <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       id="create_company"
                                       name="company"
                                       value="{{ old('company') }}"
                                       maxlength="255"
                                       class="form-control ja-control @error('company') is-invalid @enderror"
                                       required>
                                @error('company')
                                    <div class="ja-invalid-text" data-server-error>{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="ja-label" for="create_coordinator">
                                    ผู้ประสานงาน <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       id="create_coordinator"
                                       name="coordinator"
                                       value="{{ old('coordinator') }}"
                                       maxlength="255"
                                       class="form-control ja-control @error('coordinator') is-invalid @enderror"
                                       required>
                                @error('coordinator')
                                    <div class="ja-invalid-text" data-server-error>{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="ja-label" for="create_remark">หมายเหตุ</label>
                                <textarea id="create_remark"
                                          name="remark"
                                          class="form-control ja-textarea @error('remark') is-invalid @enderror"
                                          maxlength="2000"
                                          rows="3">{{ old('remark') }}</textarea>
                                @error('remark')
                                    <div class="ja-invalid-text" data-server-error>{{ $message }}</div>
                                @enderror
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
                        <span>บันทึกข้อมูล</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
