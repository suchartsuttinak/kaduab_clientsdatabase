{{-- Edit Modals --}}
    @foreach($escapes as $escape)
        <div class="modal fade escape-modal" id="escapeEditModal{{ $escape->id }}" tabindex="-1" aria-labelledby="escapeEditModalLabel{{ $escape->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-sm-down escape-modal-dialog">
                <div class="modal-content escape-modal-content">
                    <form action="{{ route('escape.update', $escape->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="client_id" value="{{ $client->id }}">

                        <div class="modal-header escape-modal-header">
                            <div class="escape-modal-header__inner">
                                <div class="escape-modal-header__icon">
                                    <i class="bi bi-pencil-square"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title escape-modal-title mb-1" id="escapeEditModalLabel{{ $escape->id }}">
                                        แก้ไขข้อมูลการออกจากสถานสงเคราะห์
                                    </h5>
                                    <div class="escape-modal-subtitle">
                                        ปรับปรุงข้อมูลรายการนี้ให้ถูกต้องและเป็นปัจจุบัน
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="ปิด"></button>
                        </div>

                        <div class="modal-body escape-modal-body">
                            <div class="escape-form-card">
                                <div class="escape-form-card__head">
                                    <div class="escape-form-card__eyebrow">EDIT RECORD</div>
                                    <h6 class="escape-form-card__title">รายละเอียดการออกจากสถานสงเคราะห์</h6>
                                    <p class="escape-form-card__desc mb-0">
                                        ใช้รูปแบบเดียวกับฟอร์มเพิ่มข้อมูล เพื่อให้ create และ edit มีมาตรฐานเดียวกัน
                                    </p>
                                </div>

                                <div class="row g-4">
                                  <div class="col-12 col-md-4">
                                        <label class="form-label escape-label escape-label--required">
                                            วันที่ออก
                                        </label>

                                        <div class="escape-field">
                                            <span class="escape-field__icon">
                                                <i class="bi bi-calendar-event"></i>
                                            </span>

                                            <input type="date"
                                                name="retire_date"
                                                class="form-control escape-input escape-input--with-icon @error('retire_date') is-invalid @enderror"
                                                required
                                                value="{{ old('retire_date', $escape->retire_date?->format('Y-m-d')) }}"
                                                max="{{ now('Asia/Bangkok')->toDateString() }}">

                                            @error('retire_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-8">
                                        <label class="form-label escape-label escape-label--required">ประเภทการออกจากหน่วยงาน</label>
                                        <div class="escape-field">
                                            <span class="escape-field__icon">
                                                <i class="bi bi-diagram-3"></i>
                                            </span>
                                            <select name="retire_id"
                                                    class="form-select escape-input escape-input--with-icon"
                                                    required>
                                                <option value="">-- เลือกประเภทการออก --</option>
                                                @foreach ($retires as $ret)
                                                    <option value="{{ $ret->id }}" {{ old('retire_id', $escape->retire_id) == $ret->id ? 'selected' : '' }}>
                                                        {{ $ret->retire_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label escape-label">พฤติการณ์ / สาเหตุ</label>
                                        <div class="escape-field escape-field--textarea">
                                            <span class="escape-field__icon escape-field__icon--textarea">
                                                <i class="bi bi-journal-text"></i>
                                            </span>
                                            <textarea name="stories"
                                                      class="form-control escape-input escape-input--with-icon escape-textarea"
                                                      rows="5"
                                                      placeholder="บันทึกรายละเอียดสาเหตุหรือเรื่องราวเพิ่มเติม">{{ old('stories', $escape->stories) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer escape-modal-footer">
                            <button type="button" class="btn escape-btn escape-btn--light" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i>
                                <span>ปิด</span>
                            </button>

                            <button type="submit" class="btn escape-btn escape-btn--success">
                                <i class="bi bi-check2-circle"></i>
                                <span>อัปเดตข้อมูล</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>