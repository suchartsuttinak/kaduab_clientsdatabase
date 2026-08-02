@php
    $isCreateContext = old('form_context') === 'escape-create';
@endphp

<div class="modal fade escape-module-modal escape-modal"
     id="escapeCreateModal"
     tabindex="-1"
     aria-labelledby="escapeCreateModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable escape-modal-dialog">
        <div class="modal-content escape-modal-content">
            <form action="{{ route('escape.store') }}" method="POST" class="escape-submit-form" novalidate>
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="form_context" value="escape-create">

                <div class="modal-header escape-modal-header bg-primary text-white">
                    <div class="d-flex align-items-center gap-3 min-w-0">
                        <div class="escape-modal-header__icon flex-shrink-0">
                            <i class="bi bi-plus-circle"></i>
                        </div>
                        <div class="min-w-0">
                            <h5 class="modal-title escape-modal-title mb-1" id="escapeCreateModalLabel">
                                เพิ่มข้อมูลการออกจากสถานสงเคราะห์
                            </h5>
                            <div class="escape-modal-subtitle text-white-50">
                                กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>

                <div class="modal-body escape-modal-body">
                    <div class="escape-modal-section">
                        <h6 class="escape-modal-section-title">
                            <i class="bi bi-info-circle text-primary"></i>
                            ข้อมูลการออก/หลบหนี
                        </h6>

                        <div class="row">
                            <div class="col-12 col-md-4">
                                <label class="form-label">วันที่ออก/หลบหนี <span class="text-danger">*</span></label>
                                <input type="date"
                                       name="retire_date"
                                       class="form-control {{ $isCreateContext && $errors->has('retire_date') ? 'is-invalid' : '' }}"
                                       value="{{ $isCreateContext ? old('retire_date', now('Asia/Bangkok')->toDateString()) : now('Asia/Bangkok')->toDateString() }}"
                                       max="{{ now('Asia/Bangkok')->toDateString() }}"
                                       required>
                                @if ($isCreateContext)
                                    @error('retire_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-8">
                                <label class="form-label">ประเภทการออก/หลบหนี <span class="text-danger">*</span></label>
                                <select name="retire_id"
                                        class="form-select {{ $isCreateContext && $errors->has('retire_id') ? 'is-invalid' : '' }}"
                                        required>
                                    <option value="">-- เลือกประเภทการออก/หลบหนี --</option>
                                    @foreach ($retires as $ret)
                                        <option value="{{ $ret->id }}"
                                            {{ ($isCreateContext ? old('retire_id') : null) == $ret->id ? 'selected' : '' }}>
                                            {{ $ret->retire_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($isCreateContext)
                                    @error('retire_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-12">
                                <label class="form-label">พฤติการณ์ / สาเหตุ</label>
                                <textarea name="stories"
                                          class="form-control {{ $isCreateContext && $errors->has('stories') ? 'is-invalid' : '' }}"
                                          rows="5"
                                          maxlength="5000"
                                          placeholder="บันทึกรายละเอียดสาเหตุหรือเรื่องราวเพิ่มเติม">{{ $isCreateContext ? old('stories') : '' }}</textarea>
                                @if ($isCreateContext)
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
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i>
                            <span>บันทึกข้อมูล</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
