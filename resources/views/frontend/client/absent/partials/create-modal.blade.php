<div class="modal fade" id="absentModal" tabindex="-1" aria-labelledby="absentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-sm-down absent-mobile-dialog">
        <div class="modal-content border-0 shadow-lg custom-modal absent-mobile-content">
            <div class="modal-header modal-header-primary">
                <div>
                    <h5 class="modal-title fw-bold mb-1" id="absentModalLabel">
                        <i class="bi bi-journal-plus me-2"></i>แบบฟอร์มบันทึกการขาดเรียน
                    </h5>
                    <div class="modal-subtitle">
                        บันทึกรายละเอียดการขาดเรียนของนักเรียนอย่างครบถ้วน เป็นระบบ และพร้อมใช้งานสำหรับการติดตามผล
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="absent-form" method="POST" action="{{ route('absent.store') }}">
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="education_record_id" value="{{ $educationRecord->id ?? '' }}">

                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <div class="form-side-card h-100">
                                <div class="form-side-header">
                                    <i class="bi bi-person-lines-fill me-2"></i>ข้อมูลเด็ก
                                </div>
                                <div class="form-side-body">
                                    <div class="side-info-item">
                                        <span class="side-info-label">ชื่อ - นามสกุล</span>
                                        <span class="side-info-value">{{ $clientName }}</span>
                                    </div>
                                    <div class="side-info-item">
                                        <span class="side-info-label">อายุ</span>
                                        <span class="side-info-value">{{ $clientAge }} ปี</span>
                                    </div>
                                    <div class="side-info-item">
                                        <span class="side-info-label">สถานศึกษา</span>
                                        <span class="side-info-value">{{ $schoolName }}</span>
                                    </div>
                                    <div class="side-info-item">
                                        <span class="side-info-label">ระดับชั้น</span>
                                        <span class="side-info-value">{{ $educationName }}</span>
                                    </div>
                                    <div class="side-info-item">
                                        <span class="side-info-label">ภาคเรียน</span>
                                        <span class="side-info-value">{{ $semesterName }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="form-main-card h-100">
                                <div class="form-side-header">
                                    <i class="bi bi-journal-check me-2"></i>ข้อมูลการขาดเรียน
                                </div>

                                <div class="form-side-body">
                                    @include('frontend.client.absent.partials.form-fields', [
                                        'prefix' => '',
                                        'absentDate' => old('absent_date', now('Asia/Bangkok')->toDateString()),
                                        'recordDate' => old('record_date', now('Asia/Bangkok')->toDateString()),
                                        'cause' => old('cause'),
                                        'operation' => old('operation'),
                                        'remark' => old('remark'),
                                        'teacher' => old('teacher'),
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-4 py-3 border-0">
                    <button type="button" class="btn btn-light btn-cancel" data-bs-dismiss="modal"
                        id="btn-cancel-absent">
                        <i class="bi bi-x-circle me-2"></i>ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-success btn-save">
                        <i class="bi bi-check-circle-fill me-2"></i>บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
