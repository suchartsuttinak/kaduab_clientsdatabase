<div class="modal fade ab-modal"
     id="editAbsentModal"
     tabindex="-1"
     aria-labelledby="editAbsentModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable absent-mobile-dialog">
        <div class="modal-content custom-modal absent-mobile-content {{ $canAbsentUpdate ? '' : 'ab-modal-readonly' }}">
            <form id="edit-absent-form"
                  method="POST"
                  data-permission-keep>
                @csrf
                @method('PUT')

                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden"
                       name="education_record_id"
                       id="edit_education_record_id"
                       value="">

                <div class="modal-header">
                    <div class="ab-modal-heading">
                        <span class="ab-modal-heading-icon" aria-hidden="true">
                            <i class="bi {{ $canAbsentUpdate ? 'bi-pencil-square' : 'bi-eye' }}"></i>
                        </span>

                        <div class="ab-modal-heading-text">
                            <h5 class="modal-title" id="editAbsentModalLabel">
                                {{ $canAbsentUpdate
                                    ? 'แก้ไขข้อมูลการขาดเรียน'
                                    : 'ดูข้อมูลการขาดเรียน' }}
                            </h5>
                            <p class="ab-modal-subtitle">
                                {{ $canAbsentUpdate
                                    ? 'ตรวจสอบและปรับปรุงรายละเอียดรายการที่บันทึกไว้'
                                    : 'ตรวจสอบรายละเอียดรายการที่บันทึกไว้' }}
                            </p>
                        </div>
                    </div>

                    <div class="ab-modal-header-actions">
                        @unless($canAbsentUpdate)
                            <span class="ab-view-badge"
                                  data-permission-keep
                                  title="บัญชีนี้เปิดดูข้อมูลได้เท่านั้น">
                                <i class="bi bi-eye"></i>
                                <span>ดูข้อมูล</span>
                            </span>
                        @endunless

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="ปิด"
                                data-permission-keep></button>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="ab-modal-context">
                        <div class="ab-modal-context-item">
                            <span class="ab-modal-context-label">
                                <i class="bi bi-building"></i>
                                สถานศึกษา
                            </span>
                            <span class="ab-modal-context-value"
                                  id="edit_school_name">-</span>
                        </div>

                        <div class="ab-modal-context-item">
                            <span class="ab-modal-context-label">
                                <i class="bi bi-book"></i>
                                ระดับชั้น
                            </span>
                            <span class="ab-modal-context-value"
                                  id="edit_education_name">-</span>
                        </div>

                        <div class="ab-modal-context-item">
                            <span class="ab-modal-context-label">
                                <i class="bi bi-calendar3"></i>
                                ภาคเรียน / ปีการศึกษา
                            </span>
                            <span class="ab-modal-context-value"
                                  id="edit_semester_name">-</span>
                        </div>
                    </div>

                    @include('frontend.client.absent.partials.form-fields', [
                        'prefix' => 'edit_',
                        'absentDate' => '',
                        'recordDate' => '',
                        'cause' => '',
                        'operation' => '',
                        'remark' => '',
                        'teacher' => '',
                    ])
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-outline-secondary ab-btn btn-cancel"
                            data-bs-dismiss="modal"
                            id="btn-cancel-edit-absent"
                            data-permission-keep>
                        <i class="bi bi-x-lg"></i>
                        <span>ปิด</span>
                    </button>

                    @if($canAbsentUpdate)
                        <button type="submit"
                                class="btn btn-warning ab-btn btn-save"
                                data-permission-action="update">
                            <i class="bi bi-check2-circle"></i>
                            <span>บันทึกการแก้ไข</span>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
