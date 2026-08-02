@php
    $processStatus = old('process_status', $item->process_status ?? 'processing');
    $processStatus = $processStatus === 'in_progress' ? 'processing' : $processStatus;

    $selectedCitizenships = old(
        'citizenship_ids',
        $item->citizenships->pluck('id')->toArray()
    );

    $selectedCitizens = old(
        'citizen_ids',
        $item->citizens->pluck('id')->toArray()
    );
@endphp

<div class="modal fade idstation-modal"
     id="editIdstationModal{{ $item->id }}"
     tabindex="-1"
     aria-labelledby="editIdstationModalLabel{{ $item->id }}"
     aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form action="{{ route('idstation.update', $item->id) }}"
              method="POST"
              class="modal-content idstation-modal-content"
              novalidate>
            @csrf
            @method('PUT')
            <input type="hidden" name="_form_context" value="edit-{{ $item->id }}">

            <div class="modal-header idstation-modal-header idstation-modal-header-warning">
                <div>
                    <div class="idstation-modal-kicker">ปรับปรุงข้อมูล</div>
                    <h5 class="modal-title" id="editIdstationModalLabel{{ $item->id }}">
                        <i class="bi bi-pencil-square me-1"></i>
                        แก้ไขข้อมูลบุคคลไม่มีสถานะทางทะเบียน
                    </h5>
                </div>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="ปิดฟอร์ม"></button>
            </div>

            <div class="modal-body idstation-modal-body">
                <div class="idstation-form-section">
                    <div class="idstation-section-heading">
                        <span class="idstation-section-icon"><i class="bi bi-calendar-check"></i></span>
                        <div>
                            <h6>ข้อมูลรับเรื่อง</h6>
                            <p>ตรวจสอบวันที่และรายการทางทะเบียนที่อยู่ระหว่างดำเนินการ</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold" for="edit_{{ $item->id }}_receive_date">
                                วันที่รับเรื่อง <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   id="edit_{{ $item->id }}_receive_date"
                                   name="receive_date"
                                   class="form-control @error('receive_date') is-invalid @enderror"
                                   value="{{ old('receive_date', optional($item->receive_date)->format('Y-m-d')) }}"
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
                                    <label class="idstation-option-item" for="edit_{{ $item->id }}_citizenship_{{ $citizenship->id }}">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="citizenship_ids[]"
                                               value="{{ $citizenship->id }}"
                                               id="edit_{{ $item->id }}_citizenship_{{ $citizenship->id }}"
                                               {{ in_array($citizenship->id, $selectedCitizenships) ? 'checked' : '' }}>
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

                        <div class="col-12">
                            <label class="form-label fw-semibold" for="edit_{{ $item->id }}_detail">บันทึกรายละเอียด</label>
                            <textarea name="detail"
                                      id="edit_{{ $item->id }}_detail"
                                      rows="4"
                                      class="form-control @error('detail') is-invalid @enderror"
                                      placeholder="ระบุรายละเอียดเพิ่มเติม">{{ old('detail', $item->detail) }}</textarea>
                            @error('detail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="idstation-form-section">
                    <div class="idstation-section-heading">
                        <span class="idstation-section-icon"><i class="bi bi-diagram-3"></i></span>
                        <div>
                            <h6>ผลการดำเนินการ</h6>
                            <p>เลือกสถานะปัจจุบันของกระบวนการช่วยเหลือ</p>
                        </div>
                    </div>

                    <div class="idstation-status-options" role="radiogroup" aria-label="สถานะการดำเนินการ">
                        <label class="idstation-status-option" for="edit_{{ $item->id }}_processing">
                            <input class="form-check-input"
                                   type="radio"
                                   name="process_status"
                                   id="edit_{{ $item->id }}_processing"
                                   value="processing"
                                   {{ $processStatus === 'processing' ? 'checked' : '' }}
                                   required>
                            <span class="idstation-status-option-icon warning"><i class="bi bi-hourglass-split"></i></span>
                            <span>
                                <strong>อยู่ระหว่างดำเนินการ</strong>
                                <small>ยังอยู่ในกระบวนการช่วยเหลือ</small>
                            </span>
                        </label>

                        <label class="idstation-status-option" for="edit_{{ $item->id }}_received_status">
                            <input class="form-check-input"
                                   type="radio"
                                   name="process_status"
                                   id="edit_{{ $item->id }}_received_status"
                                   value="received_status"
                                   {{ $processStatus === 'received_status' ? 'checked' : '' }}
                                   required>
                            <span class="idstation-status-option-icon success"><i class="bi bi-person-check"></i></span>
                            <span>
                                <strong>ได้รับสถานะทางทะเบียน</strong>
                                <small>ดำเนินการสำเร็จและจำหน่ายออกจากระบบ</small>
                            </span>
                        </label>
                    </div>

                    @error('process_status')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="idstation-form-section received-status-section"
                     {{ $processStatus === 'received_status' ? '' : 'hidden' }}>
                    <div class="idstation-section-heading">
                        <span class="idstation-section-icon success"><i class="bi bi-patch-check"></i></span>
                        <div>
                            <h6>ข้อมูลสถานะทางทะเบียนที่ได้รับ</h6>
                            <p>ระบุวันที่และรายการสถานะที่ได้รับให้ครบถ้วน</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold" for="edit_{{ $item->id }}_received_status_date">
                                วันที่รับสถานะทางทะเบียน <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   id="edit_{{ $item->id }}_received_status_date"
                                   name="received_status_date"
                                   class="form-control @error('received_status_date') is-invalid @enderror"
                                   value="{{ old('received_status_date', optional($item->received_status_date)->format('Y-m-d')) }}"
                                   min="{{ old('receive_date', optional($item->receive_date)->format('Y-m-d')) }}"
                                   max="{{ now('Asia/Bangkok')->toDateString() }}">
                            @error('received_status_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                รายการสถานะทางทะเบียนที่ได้รับ <span class="text-danger">*</span>
                            </label>

                            <div class="idstation-option-grid @error('citizen_ids') idstation-option-grid-invalid @enderror">
                                @foreach($citizens as $citizen)
                                    <label class="idstation-option-item" for="edit_{{ $item->id }}_citizen_{{ $citizen->id }}">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="citizen_ids[]"
                                               value="{{ $citizen->id }}"
                                               id="edit_{{ $item->id }}_citizen_{{ $citizen->id }}"
                                               {{ in_array($citizen->id, $selectedCitizens) ? 'checked' : '' }}>
                                        <span>{{ $citizen->citizen_name ?? $citizen->name ?? '-' }}</span>
                                    </label>
                                @endforeach
                            </div>

                            @error('citizen_ids')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                            @error('citizen_ids.*')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" for="edit_{{ $item->id }}_remark">รายละเอียดเพิ่มเติมหลังได้รับสถานะ</label>
                            <textarea name="remark"
                                      id="edit_{{ $item->id }}_remark"
                                      rows="3"
                                      class="form-control @error('remark') is-invalid @enderror"
                                      placeholder="ระบุรายละเอียดเพิ่มเติม">{{ old('remark', $item->remark) }}</textarea>
                            @error('remark')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer idstation-modal-footer">
                <button type="button" class="btn btn-outline-secondary idstation-modal-btn" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    ปิดฟอร์ม
                </button>
                <button type="submit" class="btn btn-primary idstation-modal-btn idstation-save-btn">
                    <i class="bi bi-check-circle me-1"></i>
                    บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>
