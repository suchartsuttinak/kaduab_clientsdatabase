<div class="modal fade observe-modal" id="observeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square"></i>
                    {{ isset($observe) ? 'แก้ไขข้อมูลพฤติกรรม' : 'เพิ่มข้อมูลพฤติกรรม' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form action="{{ isset($observe) ? route('observe.update', $observe->id) : route('observe.store') }}"
                      method="POST"
                      class="needs-validation"
                      novalidate>
                    @csrf
                    @if (isset($observe))
                        @method('PUT')
                    @endif

                    <input type="hidden" name="client_id" value="{{ $client->id }}">

                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-info-circle"></i>
                            ข้อมูลหลัก
                        </h6>

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label for="date" class="form-label-modern">วันที่เกิดเหตุ</label>
                              <input type="date"
                                    name="date"
                                    id="date"
                                    class="form-control form-control-modern @error('date') is-invalid @enderror"
                                    value="{{ old('date', $observe->date ?? now('Asia/Bangkok')->toDateString()) }}"
                                    max="{{ now('Asia/Bangkok')->toDateString() }}"
                                    required>

                                @error('date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-8">
                                <label for="misbehavior_id" class="form-label-modern">สภาพปัญหา</label>
                                <select name="misbehavior_id"
                                        id="misbehavior_id"
                                        class="form-select form-select-modern"
                                        required>
                                    <option value="">-- เลือกสภาพปัญหา --</option>
                                    @foreach ($misbehaviors as $m)
                                        <option value="{{ $m->id }}"
                                            {{ old('misbehavior_id', $observe->misbehavior_id ?? '') == $m->id ? 'selected' : '' }}>
                                            {{ $m->misbehavior_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-search"></i>
                            รายละเอียดพฤติกรรม
                        </h6>

                        <div class="row g-3">
                            <div class="col-12 col-lg-6">
                                <label for="behavior" class="form-label-modern">ความผิดปกติที่พบเห็น</label>
                                <textarea name="behavior"
                                          id="behavior"
                                          class="form-control form-control-modern"
                                          rows="4">{{ old('behavior', $observe->behavior ?? '') }}</textarea>
                            </div>

                            <div class="col-12 col-lg-6">
                                <label for="cause" class="form-label-modern">สาเหตุ</label>
                                <textarea name="cause"
                                          id="cause"
                                          class="form-control form-control-modern"
                                          rows="4">{{ old('cause', $observe->cause ?? '') }}</textarea>
                            </div>

                            <div class="col-12 col-lg-6">
                                <label for="solution" class="form-label-modern">แนวทางแก้ไข</label>
                                <textarea name="solution"
                                          id="solution"
                                          class="form-control form-control-modern"
                                          rows="4">{{ old('solution', $observe->solution ?? '') }}</textarea>
                            </div>

                            <div class="col-12 col-lg-6">
                                <label for="action" class="form-label-modern">การดำเนินการ</label>
                                <textarea name="action"
                                          id="action"
                                          class="form-control form-control-modern"
                                          rows="4">{{ old('action', $observe->action ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-section mb-0">
                        <h6 class="form-section-title">
                            <i class="bi bi-clipboard-data"></i>
                            ผลการดำเนินงาน
                        </h6>

                        <div class="row g-3">
                            <div class="col-12 col-lg-6">
                                <label for="obstacles" class="form-label-modern">ปัญหา / อุปสรรค</label>
                                <textarea name="obstacles"
                                          id="obstacles"
                                          class="form-control form-control-modern"
                                          rows="4">{{ old('obstacles', $observe->obstacles ?? '') }}</textarea>
                            </div>

                            <div class="col-12 col-lg-6">
                                <label for="result" class="form-label-modern">ผลลัพธ์</label>
                                <textarea name="result"
                                          id="result"
                                          class="form-control form-control-modern"
                                          rows="4">{{ old('result', $observe->result ?? '') }}</textarea>
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="record_date" class="form-label-modern">วันที่บันทึก</label>
                                <input type="date"
                                        name="record_date"
                                        id="record_date"
                                        class="form-control form-control-modern @error('record_date') is-invalid @enderror"
                                        value="{{ old('record_date', $observe->record_date ?? now('Asia/Bangkok')->toDateString()) }}"
                                        max="{{ now('Asia/Bangkok')->toDateString() }}">

                                    @error('record_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                            </div>

                          <div class="col-12 col-md-8">
                                <label for="recorder" class="form-label-modern">ผู้บันทึก</label>

                                <input type="text"
                                    name="recorder"
                                    id="recorder"
                                    class="form-control form-control-modern bg-light"
                                    value="{{ auth()->user()->name ?? '-' }}"
                                    readonly>

                                <small class="text-muted">
                                    ระบบดึงชื่อผู้ใช้งานที่เข้าสู่ระบบอัตโนมัติ
                                </small>
                            </div>
                        </div>

                        <div class="modal-footer-modern">
                            <button type="submit" class="btn-form-primary">
                                <i class="bi bi-save"></i>
                                {{ isset($observe) ? 'อัปเดตข้อมูล' : 'บันทึกข้อมูล' }}
                            </button>

                            <button type="button" class="btn-form-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> ปิด
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>