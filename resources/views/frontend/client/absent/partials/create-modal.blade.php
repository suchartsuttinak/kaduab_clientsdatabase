<div class="modal fade ab-modal"
     id="absentModal"
     tabindex="-1"
     aria-labelledby="absentModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable absent-mobile-dialog">
        <div class="modal-content custom-modal absent-mobile-content">
            <form id="absent-form"
                  method="POST"
                  action="{{ route('absent.store') }}">
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden"
                       name="education_record_id"
                       value="{{ $educationRecord->id ?? '' }}">

                <div class="modal-header">
                    <div class="ab-modal-heading">
                        <span class="ab-modal-heading-icon" aria-hidden="true">
                            <i class="bi bi-calendar-plus"></i>
                        </span>

                        <div class="ab-modal-heading-text">
                            <h5 class="modal-title" id="absentModalLabel">
                                เพิ่มข้อมูลการขาดเรียน
                            </h5>
                            <p class="ab-modal-subtitle">
                                บันทึกวันที่ สาเหตุ และการดำเนินงานอย่างเป็นระบบ
                            </p>
                        </div>
                    </div>

                    <div class="ab-modal-header-actions">
                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="ปิด"></button>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="ab-modal-context">
                        <div class="ab-modal-context-item">
                            <span class="ab-modal-context-label">
                                <i class="bi bi-building"></i>
                                สถานศึกษา
                            </span>
                            <span class="ab-modal-context-value">
                                {{ $schoolName }}
                            </span>
                        </div>

                        <div class="ab-modal-context-item">
                            <span class="ab-modal-context-label">
                                <i class="bi bi-book"></i>
                                ระดับชั้น
                            </span>
                            <span class="ab-modal-context-value">
                                {{ $educationName }}
                            </span>
                        </div>

                        <div class="ab-modal-context-item">
                            <span class="ab-modal-context-label">
                                <i class="bi bi-calendar3"></i>
                                ภาคเรียน / ปีการศึกษา
                            </span>
                            <span class="ab-modal-context-value">
                                {{ $semesterName }}
                            </span>
                        </div>
                    </div>

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

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-outline-secondary ab-btn btn-cancel"
                            data-bs-dismiss="modal"
                            id="btn-cancel-absent">
                        <i class="bi bi-x-lg"></i>
                        <span>ปิด</span>
                    </button>

                    <button type="submit"
                            class="btn btn-primary ab-btn btn-save"
                            data-permission-action="create">
                        <i class="bi bi-check-circle"></i>
                        <span>บันทึกข้อมูล</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
