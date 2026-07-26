<div class="modal fade idstation-modal" id="createIdstationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form action="{{ route('idstation.store', $client->id) }}" method="POST" class="modal-content">
            @csrf

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-1"></i>
                    เพิ่มข้อมูลบุคคลไม่มีสถานะทางทะเบียน
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            วันที่รับเรื่อง <span class="text-danger">*</span>
                        </label>
                        <input type="date"
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

                        <div class="table-responsive border rounded">
                            <table class="table table-sm table-bordered mb-0 align-middle">
                                <tbody>
                                    @foreach($citizenships->chunk(3) as $chunk)
                                        <tr>
                                            @foreach($chunk as $citizenship)
                                                <td style="width: 33.33%;">
                                                    <div class="form-check">
                                                        <input class="form-check-input"
                                                               type="checkbox"
                                                               name="citizenship_ids[]"
                                                               value="{{ $citizenship->id }}"
                                                               id="create_citizenship_{{ $citizenship->id }}"
                                                               {{ in_array($citizenship->id, old('citizenship_ids', [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                               for="create_citizenship_{{ $citizenship->id }}">
                                                            {{ $citizenship->citizenship_name ?? $citizenship->name ?? '-' }}
                                                        </label>
                                                    </div>
                                                </td>
                                            @endforeach

                                            @for($i = $chunk->count(); $i < 3; $i++)
                                                <td style="width: 33.33%;"></td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @error('citizenship_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">บันทึกรายละเอียด</label>
                        <textarea name="detail"
                                  rows="4"
                                  class="form-control @error('detail') is-invalid @enderror"
                                  placeholder="ระบุรายละเอียดเพิ่มเติม">{{ old('detail') }}</textarea>
                        @error('detail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            เมื่อบันทึกข้อมูล ระบบจะตั้งสถานะเป็น
                            <strong>อยู่ระหว่างดำเนินการ</strong>
                            โดยอัตโนมัติ และสามารถกลับมาเลือก
                            <strong>ได้รับสถานะทางทะเบียน</strong>
                            ได้ภายหลังจากปุ่มแก้ไข
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    ยกเลิก
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</div>