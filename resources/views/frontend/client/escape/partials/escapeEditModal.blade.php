@foreach ($escapes as $escape)
    @php
        $editContext = 'escape-edit-' . $escape->id;
        $isThisEditContext = old('form_context') === $editContext;
    @endphp

    <div class="modal fade escape-module-modal escape-modal"
         id="escapeEditModal{{ $escape->id }}"
         tabindex="-1"
         aria-labelledby="escapeEditModalLabel{{ $escape->id }}"
         aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable escape-modal-dialog">
            <div class="modal-content escape-modal-content">
                <form action="{{ route('escape.update', $escape->id) }}"
                      method="POST"
                      class="escape-submit-form"
                      novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <input type="hidden" name="form_context" value="{{ $editContext }}">

                    <div class="modal-header escape-modal-header bg-warning text-dark">
                        <div class="d-flex align-items-center gap-3 min-w-0">
                            <div class="escape-modal-header__icon flex-shrink-0">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                            <div class="min-w-0">
                                <h5 class="modal-title escape-modal-title mb-1" id="escapeEditModalLabel{{ $escape->id }}">
                                    แก้ไขข้อมูลการออกจากสถานสงเคราะห์
                                </h5>
                                <div class="escape-modal-subtitle text-secondary">
                                    ปรับปรุงรายการนี้ให้ถูกต้องและเป็นปัจจุบัน
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                    </div>

                    <div class="modal-body escape-modal-body">
                        <div class="escape-modal-section">
                            <h6 class="escape-modal-section-title">
                                <i class="bi bi-info-circle text-warning"></i>
                                ข้อมูลการออก/หลบหนี
                            </h6>

                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">วันที่ออก/หลบหนี <span class="text-danger">*</span></label>
                                    <input type="date"
                                           name="retire_date"
                                           class="form-control {{ $isThisEditContext && $errors->has('retire_date') ? 'is-invalid' : '' }}"
                                           value="{{ $isThisEditContext ? old('retire_date', $escape->retire_date?->format('Y-m-d')) : $escape->retire_date?->format('Y-m-d') }}"
                                           max="{{ now('Asia/Bangkok')->toDateString() }}"
                                           required>
                                    @if ($isThisEditContext)
                                        @error('retire_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>

                                <div class="col-12 col-md-8">
                                    <label class="form-label">ประเภทการออก/หลบหนี <span class="text-danger">*</span></label>
                                    <select name="retire_id"
                                            class="form-select {{ $isThisEditContext && $errors->has('retire_id') ? 'is-invalid' : '' }}"
                                            required>
                                        <option value="">-- เลือกประเภทการออก/หลบหนี --</option>
                                        @foreach ($retires as $ret)
                                            @php
                                                $selectedRetire = $isThisEditContext
                                                    ? old('retire_id', $escape->retire_id)
                                                    : $escape->retire_id;
                                            @endphp
                                            <option value="{{ $ret->id }}" {{ (string) $selectedRetire === (string) $ret->id ? 'selected' : '' }}>
                                                {{ $ret->retire_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($isThisEditContext)
                                        @error('retire_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>

                                <div class="col-12">
                                    <label class="form-label">พฤติการณ์ / สาเหตุ</label>
                                    <textarea name="stories"
                                              class="form-control {{ $isThisEditContext && $errors->has('stories') ? 'is-invalid' : '' }}"
                                              rows="5"
                                              maxlength="5000"
                                              placeholder="บันทึกรายละเอียดสาเหตุหรือเรื่องราวเพิ่มเติม">{{ $isThisEditContext ? old('stories', $escape->stories) : $escape->stories }}</textarea>
                                    @if ($isThisEditContext)
                                        @error('stories')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer escape-modal-footer">
                        <div class="escape-modal-actions">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i>
                                <span>ปิด</span>
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check2-circle"></i>
                                <span>อัปเดตข้อมูล</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
