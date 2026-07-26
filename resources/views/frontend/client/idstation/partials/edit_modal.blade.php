<div class="modal fade idstation-modal" id="editIdstationModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form action="{{ route('idstation.update', $item->id) }}" method="POST" class="modal-content">
            @csrf
            @method('PUT')

            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-1"></i>
                    แก้ไขข้อมูลบุคคลไม่มีสถานะทางทะเบียน
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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

                        @php
                            $selectedCitizenships = old(
                                'citizenship_ids',
                                $item->citizenships->pluck('id')->toArray()
                            );
                        @endphp

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
                                                               id="edit_{{ $item->id }}_citizenship_{{ $citizenship->id }}"
                                                               {{ in_array($citizenship->id, $selectedCitizenships) ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                               for="edit_{{ $item->id }}_citizenship_{{ $citizenship->id }}">
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
                                  placeholder="ระบุรายละเอียดเพิ่มเติม">{{ old('detail', $item->detail) }}</textarea>
                        @error('detail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            การดำเนินการ <span class="text-danger">*</span>
                        </label>

                        @php
                            $processStatus = old('process_status', $item->process_status ?? 'processing');
                        @endphp

                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="radio"
                                       name="process_status"
                                       id="edit_{{ $item->id }}_processing"
                                       value="processing"
                                       onclick="toggleIdstationReceivedSection({{ $item->id }}, 'processing')"
                                       {{ $processStatus === 'processing' ? 'checked' : '' }}>
                                <label class="form-check-label" for="edit_{{ $item->id }}_processing">
                                    อยู่ระหว่างดำเนินการ
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input"
                                       type="radio"
                                       name="process_status"
                                       id="edit_{{ $item->id }}_received_status"
                                       value="received_status"
                                       onclick="toggleIdstationReceivedSection({{ $item->id }}, 'received_status')"
                                       {{ $processStatus === 'received_status' ? 'checked' : '' }}>
                                <label class="form-check-label" for="edit_{{ $item->id }}_received_status">
                                    ได้รับสถานะทางทะเบียน
                                </label>
                            </div>
                        </div>

                        @error('process_status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 received-status-section"
                         id="receivedStatusSection{{ $item->id }}"
                         style="{{ $processStatus === 'received_status' ? '' : 'display: none;' }}">
                        <div class="border rounded p-3 bg-light">
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        วันที่รับสถานะทางทะเบียน <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           name="received_status_date"
                                           class="form-control @error('received_status_date') is-invalid @enderror"
                                           value="{{ old('received_status_date', optional($item->received_status_date)->format('Y-m-d')) }}"
                                           max="{{ now('Asia/Bangkok')->toDateString() }}">
                                    @error('received_status_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        รายการสถานะทางทะเบียนที่ได้รับ <span class="text-danger">*</span>
                                    </label>

                                    @php
                                        $selectedCitizens = old(
                                            'citizen_ids',
                                            $item->citizens->pluck('id')->toArray()
                                        );
                                    @endphp

                                    <div class="table-responsive border rounded bg-white">
                                        <table class="table table-sm table-bordered mb-0 align-middle">
                                            <tbody>
                                                @foreach($citizens->chunk(3) as $chunk)
                                                    <tr>
                                                        @foreach($chunk as $citizen)
                                                            <td style="width: 33.33%;">
                                                                <div class="form-check">
                                                                    <input class="form-check-input"
                                                                           type="checkbox"
                                                                           name="citizen_ids[]"
                                                                           value="{{ $citizen->id }}"
                                                                           id="edit_{{ $item->id }}_citizen_{{ $citizen->id }}"
                                                                           {{ in_array($citizen->id, $selectedCitizens) ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                           for="edit_{{ $item->id }}_citizen_{{ $citizen->id }}">
                                                                        {{ $citizen->citizen_name ?? $citizen->name ?? '-' }}
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

                                    @error('citizen_ids')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">รายละเอียดเพิ่มเติม</label>
                                    <textarea name="remark"
                                              rows="3"
                                              class="form-control @error('remark') is-invalid @enderror"
                                              placeholder="ระบุรายละเอียดเพิ่มเติมหลังได้รับสถานะ">{{ old('remark', $item->remark) }}</textarea>
                                    @error('remark')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    ยกเลิก
                </button>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save me-1"></i>
                    บันทึกการแก้ไข
                </button>
            </div>
        </form>
    </div>
</div>

@once
<script>
function toggleIdstationReceivedSection(itemId, status) {
    const section = document.getElementById('receivedStatusSection' + itemId);

    if (!section) {
        return;
    }

    section.style.display = status === 'received_status' ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.idstation-modal').forEach(function (modal) {
        const checked = modal.querySelector('input[name="process_status"]:checked');

        if (!checked) {
            return;
        }

        const itemIdMatch = checked.id.match(/^edit_(\d+)_/);

        if (!itemIdMatch) {
            return;
        }

        toggleIdstationReceivedSection(itemIdMatch[1], checked.value);
    });
});
</script>
@endonce