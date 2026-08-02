<div class="modal fade idstation-modal"
     id="createIdstationModal"
     tabindex="-1"
     aria-labelledby="createIdstationModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form action="{{ route('idstation.store', $client->id) }}"
              method="POST"
              class="modal-content idstation-modal-content"
              novalidate>
            @csrf
            <input type="hidden" name="_form_context" value="create">

            <div class="modal-header idstation-modal-header idstation-modal-header-primary">
                <div>
                    <div class="idstation-modal-kicker">รับเรื่องใหม่</div>
                    <h5 class="modal-title" id="createIdstationModalLabel">
                        <i class="bi bi-person-plus me-1"></i>
                        เพิ่มข้อมูลบุคคลไม่มีสถานะทางทะเบียน
                    </h5>
                </div>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="ปิดฟอร์ม"></button>
            </div>

            <div class="modal-body idstation-modal-body">
                <div class="idstation-form-section">
                    <div class="idstation-section-heading">
                        <span class="idstation-section-icon"><i class="bi bi-calendar-check"></i></span>
                        <div>
                            <h6>ข้อมูลรับเรื่อง</h6>
                            <p>ระบุวันที่และรายการทางทะเบียนที่ต้องดำเนินการ</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold" for="create_receive_date">
                                วันที่รับเรื่อง <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   id="create_receive_date"
                                   name="receive_date"
                                   class="form-control @error('receive_date') is-invalid @enderror"
                                   value="{{ old('receive_date', now('Asia/Bangkok')->toDateString()) }}"
                                   max="{{ now('Asia/Bangkok')->toDateString() }}"
                                   required>
                            @error('receive_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                รายการทางทะเบียน <span class="text-danger">*</span>
                            </label>

                            <div class="idstation-option-grid @error('citizenship_ids') idstation-option-grid-invalid @enderror">
                                @foreach($citizenships as $citizenship)
                                    <label class="idstation-option-item" for="create_citizenship_{{ $citizenship->id }}">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="citizenship_ids[]"
                                               value="{{ $citizenship->id }}"
                                               id="create_citizenship_{{ $citizenship->id }}"
                                               {{ in_array($citizenship->id, old('citizenship_ids', [])) ? 'checked' : '' }}>
                                        <span>{{ $citizenship->citizenship_name ?? $citizenship->name ?? '-' }}</span>
                                    </label>
                                @endforeach
                            </div>

                            @error('citizenship_ids')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                            @error('citizenship_ids.*')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="idstation-form-section">
                    <div class="idstation-section-heading">
                        <span class="idstation-section-icon"><i class="bi bi-journal-text"></i></span>
                        <div>
                            <h6>รายละเอียดเพิ่มเติม</h6>
                            <p>บันทึกข้อเท็จจริงหรือแนวทางการช่วยเหลือเบื้องต้น</p>
                        </div>
                    </div>

                    <label class="form-label fw-semibold" for="create_detail">บันทึกรายละเอียด</label>
                    <textarea name="detail"
                              id="create_detail"
                              rows="4"
                              class="form-control @error('detail') is-invalid @enderror"
                              placeholder="ระบุรายละเอียดเพิ่มเติม">{{ old('detail') }}</textarea>
                    @error('detail')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info idstation-form-info mb-0" role="note">
                    <i class="bi bi-info-circle"></i>
                    <span>
                        หลังบันทึก ระบบจะตั้งสถานะเป็น <strong>อยู่ระหว่างดำเนินการ</strong>
                        และสามารถเปลี่ยนเป็น <strong>ได้รับสถานะทางทะเบียน</strong> ในภายหลัง
                    </span>
                </div>
            </div>

            <div class="modal-footer idstation-modal-footer">
                <button type="button" class="btn btn-outline-secondary idstation-modal-btn" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    ปิดฟอร์ม
                </button>
                <button type="submit" class="btn btn-primary idstation-modal-btn idstation-save-btn">
                    <i class="bi bi-check-circle me-1"></i>
                    บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</div>
