@php
    $isObserveEdit = isset($observe) && $observe;
@endphp

<div class="modal fade observe-modal observe-main-modal" id="observeModal" tabindex="-1"
     aria-labelledby="observeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="observeModalLabel">
                    <i class="bi bi-pencil-square"></i>
                    {{ $isObserveEdit ? 'แก้ไขข้อมูลพฤติกรรม' : 'เพิ่มข้อมูลพฤติกรรม' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
            </div>

            <form action="{{ $isObserveEdit ? route('observe.update', $observe->id) : route('observe.store') }}"
                  method="POST"
                  class="needs-validation observe-submit-form observe-modal-form"
                  novalidate>
                @csrf
                @if ($isObserveEdit)
                    @method('PUT')
                @endif

                <input type="hidden" name="client_id" value="{{ $client->id }}">

                <div class="modal-body">
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-info-circle"></i>
                            ข้อมูลหลัก
                        </h6>

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="date" class="form-label-modern">
                                    วันที่เกิดเหตุ <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       name="date"
                                       id="date"
                                       class="form-control form-control-modern @error('date', 'observeForm') is-invalid @enderror"
                                       value="{{ old('date', $observe->date ?? now('Asia/Bangkok')->toDateString()) }}"
                                       max="{{ now('Asia/Bangkok')->toDateString() }}"
                                       required>
                                @error('date', 'observeForm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-8">
                                <label for="misbehavior_id" class="form-label-modern">
                                    สภาพปัญหา <span class="text-danger">*</span>
                                </label>
                                <select name="misbehavior_id"
                                        id="misbehavior_id"
                                        class="form-select form-select-modern @error('misbehavior_id', 'observeForm') is-invalid @enderror"
                                        required>
                                    <option value="">-- เลือกสภาพปัญหา --</option>
                                    @foreach ($misbehaviors as $m)
                                        <option value="{{ $m->id }}"
                                            {{ (string) old('misbehavior_id', $observe->misbehavior_id ?? '') === (string) $m->id ? 'selected' : '' }}>
                                            {{ $m->misbehavior_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('misbehavior_id', 'observeForm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-search"></i>
                            รายละเอียดพฤติกรรม
                        </h6>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="behavior" class="form-label-modern">
                                    ความผิดปกติที่พบเห็น <span class="text-danger">*</span>
                                </label>
                                <textarea name="behavior" id="behavior" rows="4" maxlength="5000" required
                                          class="form-control form-control-modern @error('behavior', 'observeForm') is-invalid @enderror">{{ old('behavior', $observe->behavior ?? '') }}</textarea>
                                @error('behavior', 'observeForm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="cause" class="form-label-modern">
                                    สาเหตุ <span class="text-danger">*</span>
                                </label>
                                <textarea name="cause" id="cause" rows="4" maxlength="5000" required
                                          class="form-control form-control-modern @error('cause', 'observeForm') is-invalid @enderror">{{ old('cause', $observe->cause ?? '') }}</textarea>
                                @error('cause', 'observeForm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="solution" class="form-label-modern">
                                    แนวทางแก้ไข <span class="text-danger">*</span>
                                </label>
                                <textarea name="solution" id="solution" rows="4" maxlength="5000" required
                                          class="form-control form-control-modern @error('solution', 'observeForm') is-invalid @enderror">{{ old('solution', $observe->solution ?? '') }}</textarea>
                                @error('solution', 'observeForm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="action" class="form-label-modern">
                                    การดำเนินการ <span class="text-danger">*</span>
                                </label>
                                <textarea name="action" id="action" rows="4" maxlength="5000" required
                                          class="form-control form-control-modern @error('action', 'observeForm') is-invalid @enderror">{{ old('action', $observe->action ?? '') }}</textarea>
                                @error('action', 'observeForm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-section mb-0">
                        <h6 class="form-section-title">
                            <i class="bi bi-clipboard-data"></i>
                            ผลการดำเนินงาน
                        </h6>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="obstacles" class="form-label-modern">ปัญหา / อุปสรรค</label>
                                <textarea name="obstacles" id="obstacles" rows="4" maxlength="5000"
                                          class="form-control form-control-modern @error('obstacles', 'observeForm') is-invalid @enderror">{{ old('obstacles', $observe->obstacles ?? '') }}</textarea>
                                @error('obstacles', 'observeForm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="result" class="form-label-modern">
                                    ผลลัพธ์ <span class="text-danger">*</span>
                                </label>
                                <textarea name="result" id="result" rows="4" maxlength="5000" required
                                          class="form-control form-control-modern @error('result', 'observeForm') is-invalid @enderror">{{ old('result', $observe->result ?? '') }}</textarea>
                                @error('result', 'observeForm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="record_date" class="form-label-modern">
                                    วันที่บันทึก <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       name="record_date"
                                       id="record_date"
                                       class="form-control form-control-modern @error('record_date', 'observeForm') is-invalid @enderror"
                                       value="{{ old('record_date', $observe->record_date ?? now('Asia/Bangkok')->toDateString()) }}"
                                       max="{{ now('Asia/Bangkok')->toDateString() }}"
                                       required>
                                @error('record_date', 'observeForm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-8">
                                <label for="recorder" class="form-label-modern">ผู้บันทึก</label>
                                <input type="text"
                                       id="recorder"
                                       class="form-control form-control-modern bg-light"
                                       value="{{ auth()->user()->name ?? '-' }}"
                                       readonly>
                                <small class="text-muted">ระบบดึงชื่อผู้ใช้งานที่เข้าสู่ระบบอัตโนมัติ</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer-modern observe-modal-fixed-footer">
                    <button type="submit"
                            class="btn-form-primary"
                            data-submit-button
                            data-loading-text="{{ $isObserveEdit ? 'กำลังอัปเดต...' : 'กำลังบันทึก...' }}">
                        <i class="bi bi-save" aria-hidden="true"></i>
                        <span data-submit-label>{{ $isObserveEdit ? 'อัปเดตข้อมูล' : 'บันทึกข้อมูล' }}</span>
                    </button>

                    <button type="button" class="btn-form-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> ปิด
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
