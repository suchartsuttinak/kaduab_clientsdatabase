@php
    $canApproveRefer = auth()->check() && in_array(auth()->user()->role, ['admin', 'executive'], true);
    $canCreateRefer = $canCreateRefer ?? !in_array($client->release_status, ['pending_refer', 'refer'], true);
@endphp

<div class="modal fade rf-modal" id="createReferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form action="{{ route('refers.store') }}"
                  method="POST"
                  id="referForm"
                  class="rf-modal-form refer-validate-form"
                  enctype="multipart/form-data"
                  novalidate>
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">

                <div class="modal-header">
                    <h5 class="modal-title rf-modal-title">
                        <i class="bi bi-file-earmark-plus"></i>
                        <span>แบบฟอร์มบันทึกข้อมูลการจำหน่าย</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>

                <div class="rf-modal-body">
                    @if($canApproveRefer)
                        <div class="alert alert-info border-0 mb-3" style="border-radius:14px;">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-shield-check mt-1"></i>
                                <div>
                                    <div class="fw-bold">บัญชีของคุณมีสิทธิ์อนุมัติการจำหน่าย</div>
                                    <div class="small mb-0">
                                        เมื่อบันทึกสำเร็จ ระบบจะอนุมัติรายการและปรับสถานะผู้รับบริการเป็น
                                        <strong>จำหน่ายแล้ว</strong> ทันที
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning border-0 mb-3" style="border-radius:14px;">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                                <div>
                                    <div class="fw-bold">รายการจะถูกส่งเข้าสู่ขั้นตอนรออนุมัติ</div>
                                    <div class="small mb-0">
                                        เมื่อบันทึกแล้ว ต้องให้ <strong>ผู้ดูแลระบบหรือผู้บริหาร</strong>
                                        เป็นผู้อนุมัติการจำหน่าย
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="rf-section">
                        <div class="rf-section-title">
                            <i class="bi bi-ui-checks-grid"></i>
                            <span>ข้อมูลการนำส่ง</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="rf-label">วันที่นำส่ง <span class="text-danger">*</span></label>
                                <input type="date"
                                       name="refer_date"
                                       value="{{ old('refer_date', now('Asia/Bangkok')->toDateString()) }}"
                                       max="{{ now('Asia/Bangkok')->toDateString() }}"
                                       class="form-control rf-control @error('refer_date') is-invalid @enderror"
                                       required>
                                @error('refer_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-8">
                                <label class="rf-label">สาเหตุการจำหน่าย <span class="text-danger">*</span></label>
                                <select name="translate_id"
                                        class="form-select rf-select @error('translate_id') is-invalid @enderror"
                                        required>
                                    <option value="">-- เลือก --</option>
                                    @foreach($translates as $t)
                                        <option value="{{ $t->id }}" {{ old('translate_id') == $t->id ? 'selected' : '' }}>
                                            {{ $t->translate_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('translate_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="rf-label">ชื่อสถานที่นำส่ง <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="destination"
                                       value="{{ old('destination') }}"
                                       maxlength="255"
                                       class="form-control rf-control @error('destination') is-invalid @enderror"
                                       required>
                                @error('destination')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="rf-label">ที่อยู่ <span class="text-danger">*</span></label>
                                <textarea name="address"
                                          maxlength="2000"
                                          class="form-control rf-textarea @error('address') is-invalid @enderror"
                                          rows="2"
                                          required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="rf-section" id="guardianSection">
                        <div class="rf-section-title">
                            <i class="bi bi-people"></i>
                            <span>ข้อมูลผู้ดูแล</span>
                        </div>

                        <label class="rf-label d-block mb-2">มีผู้ดูแลหรือไม่ <span class="text-danger">*</span></label>
                        <div class="rf-option-grid">
                            <div class="rf-option">
                                <input class="rf-option-input"
                                       type="radio"
                                       name="guardian"
                                       id="guardian_yes"
                                       value="มี"
                                       {{ old('guardian') === 'มี' ? 'checked' : '' }}
                                       required>
                                <label class="rf-option-label" for="guardian_yes">
                                    <span>มีผู้ดูแล / มีผู้มารับตัว</span>
                                </label>
                            </div>

                            <div class="rf-option">
                                <input class="rf-option-input"
                                       type="radio"
                                       name="guardian"
                                       id="guardian_no"
                                       value="ไม่มี"
                                       {{ old('guardian') === 'ไม่มี' ? 'checked' : '' }}
                                       required>
                                <label class="rf-option-label" for="guardian_no">
                                    <span>ไม่มีผู้ดูแล</span>
                                </label>
                            </div>
                        </div>
                        @error('guardian')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror

                        <div id="guardianFields" class="rf-guardian-panel {{ old('guardian') === 'มี' ? 'is-active' : '' }}">
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="rf-label">ชื่อผู้รับตัว <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="parent_name"
                                           value="{{ old('parent_name') }}"
                                           maxlength="255"
                                           class="form-control rf-control @error('parent_name') is-invalid @enderror">
                                    @error('parent_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="rf-label">โทรศัพท์</label>
                                    <input type="text"
                                           name="parent_tel"
                                           value="{{ old('parent_tel') }}"
                                           maxlength="50"
                                           class="form-control rf-control @error('parent_tel') is-invalid @enderror">
                                    @error('parent_tel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="rf-label">ความสัมพันธ์</label>
                                    <input type="text"
                                           name="member"
                                           value="{{ old('member') }}"
                                           maxlength="255"
                                           class="form-control rf-control @error('member') is-invalid @enderror">
                                    @error('member')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rf-section">
                        <div class="rf-section-title">
                            <i class="bi bi-person-workspace"></i>
                            <span>ข้อมูลเพิ่มเติม</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="rf-label">ชื่อผู้นำส่ง <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="teacher"
                                       value="{{ old('teacher') }}"
                                       maxlength="255"
                                       class="form-control rf-control @error('teacher') is-invalid @enderror"
                                       required>
                                @error('teacher')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="rf-label d-block mb-2">ผลคณะกรรมการฯ <span class="text-danger">*</span></label>

                                <div class="rf-committee-group">
                                    <label class="rf-committee-option">
                                        <input type="radio"
                                               name="committee_result"
                                               value="ไม่ผ่าน"
                                               {{ old('committee_result', 'ไม่ผ่าน') === 'ไม่ผ่าน' ? 'checked' : '' }}
                                               required>
                                        <span>ไม่ผ่าน</span>
                                    </label>

                                    <label class="rf-committee-option">
                                        <input type="radio"
                                               name="committee_result"
                                               value="ผ่าน"
                                               {{ old('committee_result') === 'ผ่าน' ? 'checked' : '' }}
                                               required>
                                        <span>ผ่าน</span>
                                    </label>
                                </div>

                                @error('committee_result')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12"
                                 id="meeting-report-upload-wrapper"
                                 style="{{ old('committee_result', 'ไม่ผ่าน') === 'ผ่าน' ? '' : 'display:none;' }}">
                                <label class="rf-label">แนบรายงานการประชุม <span class="text-danger">*</span></label>
                                <input type="file"
                                       name="meeting_report_file"
                                       class="form-control rf-control @error('meeting_report_file') is-invalid @enderror"
                                       accept="application/pdf,.pdf">
                                <div class="form-text">รองรับเฉพาะไฟล์ PDF ขนาดไม่เกิน 10 MB</div>

                                @error('meeting_report_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="rf-label">หมายเหตุ</label>
                                <textarea name="remark"
                                          maxlength="3000"
                                          class="form-control rf-textarea @error('remark') is-invalid @enderror"
                                          rows="2">{{ old('remark') }}</textarea>
                                @error('remark')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rf-modal-footer">
                    <button type="button" class="btn btn-outline-secondary rf-btn" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        <span>ปิด</span>
                    </button>

                    @if(!$canCreateRefer)
                        <button type="button" class="btn btn-secondary rf-btn" disabled>
                            <i class="bi bi-hourglass-split"></i>
                            <span>มีรายการที่กำลังดำเนินการ</span>
                        </button>
                    @else
                        <button type="submit" class="btn btn-success rf-btn">
                            <i class="bi bi-send-check"></i>
                            <span>{{ $canApproveRefer ? 'บันทึกและอนุมัติ' : 'บันทึกและส่งรออนุมัติ' }}</span>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.rf-committee-group{display:flex;flex-wrap:wrap;gap:10px;margin-top:2px;}
.rf-committee-option{
    display:inline-flex;align-items:center;gap:8px;min-height:42px;padding:9px 14px;
    border:1px solid #d8e0ea;border-radius:12px;background:#fff;color:#0f172a;
    font-size:14px;font-weight:600;cursor:pointer;transition:all .2s ease;user-select:none;
}
.rf-committee-option:hover{border-color:#b8c7d9;background:#f8fbff;}
.rf-committee-option input[type="radio"]{width:16px;height:16px;margin:0;accent-color:#2563eb;}
#meeting-report-upload-wrapper{margin-top:2px;}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('referForm');
    const radios = form ? form.querySelectorAll('input[name="committee_result"]') : [];
    const uploadWrapper = document.getElementById('meeting-report-upload-wrapper');
    const uploadInput = form ? form.querySelector('input[name="meeting_report_file"]') : null;

    function toggleMeetingReportUpload() {
        if (!uploadWrapper) return;

        const checked = form ? form.querySelector('input[name="committee_result"]:checked') : null;
        const mustUpload = checked && checked.value === 'ผ่าน';

        uploadWrapper.style.display = mustUpload ? '' : 'none';

        if (uploadInput) {
            uploadInput.required = Boolean(mustUpload);

            if (!mustUpload) {
                uploadInput.value = '';
                uploadInput.classList.remove('is-invalid');
            }
        }
    }

    radios.forEach(function (radio) {
        radio.addEventListener('change', toggleMeetingReportUpload);
    });

    toggleMeetingReportUpload();
});
</script>
