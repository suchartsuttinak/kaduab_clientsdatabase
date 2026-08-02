@if (isset($observe) && $observe)
    @foreach ($observe->followups as $f)
        @php
            $followupUpdateBag = 'followupUpdate' . $f->id;
            $hasFollowupUpdateErrors = $errors->getBag($followupUpdateBag)->any();
        @endphp

        <div class="modal fade observe-modal"
             id="editFollowupModal{{ $f->id }}"
             tabindex="-1"
             aria-labelledby="editFollowupModalLabel{{ $f->id }}"
             aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFollowupModalLabel{{ $f->id }}">
                            <i class="bi bi-pencil-square"></i>
                            แก้ไขการติดตามผล ครั้งที่ {{ $f->followup_count }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                    </div>

                    <div class="modal-body">
                        <form action="{{ route('observe.followup.update', $f->id) }}"
                              method="POST"
                              class="needs-validation observe-submit-form"
                              novalidate>
                            @csrf
                            @method('PUT')

                            <div class="form-section">
                                <h6 class="form-section-title">
                                    <i class="bi bi-arrow-repeat"></i>
                                    ข้อมูลการติดตามผล
                                </h6>

                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label-modern">
                                            วันที่ติดตาม <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                               name="followup_date"
                                               class="form-control form-control-modern @error('followup_date', $followupUpdateBag) is-invalid @enderror"
                                               value="{{ $hasFollowupUpdateErrors ? old('followup_date') : $f->followup_date }}"
                                               min="{{ $observe->date }}"
                                               max="{{ now('Asia/Bangkok')->toDateString() }}"
                                               required>
                                        @error('followup_date', $followupUpdateBag)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label-modern">ครั้งที่</label>
                                        <div class="form-control form-control-modern d-flex align-items-center text-start"
                                             style="background-color:#f8fafc;">
                                            {{ $f->followup_count }}
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label-modern">การดำเนินการ</label>
                                        <textarea name="followup_action"
                                                  class="form-control form-control-modern text-start @error('followup_action', $followupUpdateBag) is-invalid @enderror"
                                                  rows="3"
                                                  maxlength="5000">{{ $hasFollowupUpdateErrors ? old('followup_action') : $f->followup_action }}</textarea>
                                        @error('followup_action', $followupUpdateBag)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label-modern">ผลลัพธ์</label>
                                        <textarea name="followup_result"
                                                  class="form-control form-control-modern text-start @error('followup_result', $followupUpdateBag) is-invalid @enderror"
                                                  rows="3"
                                                  maxlength="5000">{{ $hasFollowupUpdateErrors ? old('followup_result') : $f->followup_result }}</textarea>
                                        @error('followup_result', $followupUpdateBag)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer-modern">
                                <button type="submit"
                                        class="btn-form-warning"
                                        data-submit-button
                                        data-loading-text="กำลังอัปเดต...">
                                    <i class="bi bi-check2-circle"></i>
                                    <span data-submit-label>อัปเดตการติดตามผล</span>
                                </button>

                                <button type="button" class="btn-form-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle"></i> ปิด
                                </button>
                            </div>
                        </form>

                        <div class="modal-footer-modern pt-0 border-0">
                            <form id="delete-form-followup-{{ $f->id }}"
                                  action="{{ route('observe.followup.delete', $f->id) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        class="btn-form-danger"
                                        onclick="confirmDelete('delete-form-followup-{{ $f->id }}', 'คุณต้องการลบการติดตามผลนี้ใช่หรือไม่')">
                                    <i class="bi bi-trash"></i> ลบการติดตามผล
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
