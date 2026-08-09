@php
    $followupOldContext = old('_form_context');
    $latestFollowupNo = (int) $counseling->followups->max('followup_no');
@endphp

<div class="modal fade cs-modal"
     id="counselingFollowupCreateModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <form action="{{ route('counseling.followup.store') }}"
                  method="POST"
                  class="js-workflow-form">
                @csrf

                <input type="hidden" name="counseling_id" value="{{ $counseling->id }}">
                <input type="hidden" name="_form_context" value="followup_create">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-chat-dots me-1 text-primary"></i>
                        การให้คำปรึกษาต่อเนื่อง ครั้งที่ {{ $latestFollowupNo + 1 }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">
                                วันที่ให้คำปรึกษาต่อเนื่อง <span class="cs-required">*</span>
                            </label>
                            <input type="date"
                                   name="followup_date"
                                   class="form-control"
                                   max="{{ now('Asia/Bangkok')->format('Y-m-d') }}"
                                   value="{{ $followupOldContext === 'followup_create' ? old('followup_date') : '' }}"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                ช่องทางการให้คำปรึกษา <span class="cs-required">*</span>
                            </label>
                            <select name="followup_method" class="form-select" required>
                                @foreach ([
                                    'face_to_face' => 'พบโดยตรง',
                                    'phone' => 'โทรศัพท์',
                                    'online' => 'ออนไลน์',
                                    'home_visit' => 'เยี่ยมบ้าน/สถานที่พัก',
                                    'other' => 'อื่น ๆ',
                                ] as $value => $label)
                                    <option value="{{ $value }}"
                                            @selected($followupOldContext === 'followup_create' && old('followup_method', 'face_to_face') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">สถานที่</label>
                            <input type="text"
                                   name="location"
                                   class="form-control"
                                   maxlength="255"
                                   value="{{ $followupOldContext === 'followup_create' ? old('location') : '' }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                ความคืบหน้า / สาระสำคัญของการให้คำปรึกษา <span class="cs-required">*</span>
                            </label>
                            <textarea name="progress" class="form-control" required>{{ $followupOldContext === 'followup_create' ? old('progress') : '' }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">การเปลี่ยนแปลงที่พบ</label>
                            <textarea name="changes" class="form-control">{{ $followupOldContext === 'followup_create' ? old('changes') : '' }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">ปัญหา / อุปสรรค</label>
                            <textarea name="barriers" class="form-control">{{ $followupOldContext === 'followup_create' ? old('barriers') : '' }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">การประเมินปัจจุบัน</label>
                            <textarea name="current_assessment" class="form-control">{{ $followupOldContext === 'followup_create' ? old('current_assessment') : '' }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">การช่วยเหลือเพิ่มเติม</label>
                            <textarea name="additional_support" class="form-control">{{ $followupOldContext === 'followup_create' ? old('additional_support') : '' }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">ผลการให้คำปรึกษาครั้งนี้</label>
                            <textarea name="result" class="form-control">{{ $followupOldContext === 'followup_create' ? old('result') : '' }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">แนวทางดำเนินการต่อ</label>
                            <textarea name="next_action" class="form-control">{{ $followupOldContext === 'followup_create' ? old('next_action') : '' }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                สถานะหลังการให้คำปรึกษาครั้งนี้ <span class="cs-required">*</span>
                            </label>
                            <select name="status" class="form-select js-workflow-status" required>
                                <option value="">-- เลือกสถานะ --</option>
                                <option value="follow_up"
                                        @selected($followupOldContext === 'followup_create' && old('status') === 'follow_up')>
                                    ให้คำปรึกษาต่อเนื่อง
                                </option>
                                <option value="closed"
                                        @selected($followupOldContext === 'followup_create' && old('status') === 'closed')>
                                    ยุติการให้คำปรึกษา
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6 js-next-appointment-wrap" style="display:none;">
                            <label class="form-label">
                                วันนัดหมายครั้งต่อไป <span class="cs-required">*</span>
                            </label>
                            <input type="date"
                                   name="next_appointment_date"
                                   class="form-control js-next-appointment-date"
                                   value="{{ $followupOldContext === 'followup_create' ? old('next_appointment_date') : '' }}">
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> ปิด
                    </button>
                    <button type="submit" class="cs-btn-primary js-submit-once">
                        <i class="bi bi-check-circle"></i> บันทึกข้อมูล
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>


@foreach ($counseling->followups as $followup)
    @php
        $hasLaterFollowup = $counseling->followups
            ->where('followup_no', '>', $followup->followup_no)
            ->isNotEmpty();

        $followupIsClosed = in_array(
            $followup->status,
            ['closed', 'goal_met', 'referred'],
            true
        );
    @endphp

    <div class="modal fade cs-modal"
         id="followupEditModal{{ $followup->id }}"
         tabindex="-1"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <form action="{{ route('counseling.followup.update', $followup->id) }}"
                      method="POST"
                      class="js-workflow-form">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="_form_context" value="followup_edit_{{ $followup->id }}">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square me-1 text-primary"></i>
                            แก้ไขการให้คำปรึกษาต่อเนื่อง ครั้งที่ {{ $followup->followup_no }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">
                                    วันที่ให้คำปรึกษาต่อเนื่อง <span class="cs-required">*</span>
                                </label>
                                <input type="date"
                                       name="followup_date"
                                       class="form-control"
                                       max="{{ now('Asia/Bangkok')->format('Y-m-d') }}"
                                       value="{{ optional($followup->followup_date)->format('Y-m-d') }}"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    ช่องทางการให้คำปรึกษา <span class="cs-required">*</span>
                                </label>
                                <select name="followup_method" class="form-select" required>
                                    @foreach ([
                                        'face_to_face' => 'พบโดยตรง',
                                        'phone' => 'โทรศัพท์',
                                        'online' => 'ออนไลน์',
                                        'home_visit' => 'เยี่ยมบ้าน/สถานที่พัก',
                                        'other' => 'อื่น ๆ',
                                    ] as $value => $label)
                                        <option value="{{ $value }}" @selected($followup->followup_method === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">สถานที่</label>
                                <input type="text" name="location" class="form-control" maxlength="255" value="{{ $followup->location }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    ความคืบหน้า / สาระสำคัญของการให้คำปรึกษา <span class="cs-required">*</span>
                                </label>
                                <textarea name="progress" class="form-control" required>{{ $followup->progress }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">การเปลี่ยนแปลงที่พบ</label>
                                <textarea name="changes" class="form-control">{{ $followup->changes }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">ปัญหา / อุปสรรค</label>
                                <textarea name="barriers" class="form-control">{{ $followup->barriers }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">การประเมินปัจจุบัน</label>
                                <textarea name="current_assessment" class="form-control">{{ $followup->current_assessment }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">การช่วยเหลือเพิ่มเติม</label>
                                <textarea name="additional_support" class="form-control">{{ $followup->additional_support }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">ผลการให้คำปรึกษาครั้งนี้</label>
                                <textarea name="result" class="form-control">{{ $followup->result }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">แนวทางดำเนินการต่อ</label>
                                <textarea name="next_action" class="form-control">{{ $followup->next_action }}</textarea>
                            </div>

                            @if ($hasLaterFollowup)
                                <div class="col-12">
                                    <label class="form-label">สถานะ</label>
                                    <input type="hidden" name="status" value="follow_up">
                                    <input type="text" class="form-control cs-readonly" value="ให้คำปรึกษาต่อเนื่อง" readonly>
                                    <div class="cs-help">
                                        มีบันทึกต่อเนื่องครั้งถัดไปแล้ว รายการนี้จึงไม่สามารถเปลี่ยนเป็น “ยุติการให้คำปรึกษา” ได้
                                    </div>
                                </div>
                            @else
                                <div class="col-md-6">
                                    <label class="form-label">
                                        สถานะหลังการให้คำปรึกษาครั้งนี้ <span class="cs-required">*</span>
                                    </label>
                                    <select name="status" class="form-select js-workflow-status" required>
                                        <option value="follow_up" @selected(!$followupIsClosed)>
                                            ให้คำปรึกษาต่อเนื่อง
                                        </option>
                                        <option value="closed" @selected($followupIsClosed)>
                                            ยุติการให้คำปรึกษา
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-6 js-next-appointment-wrap"
                                     @if($followupIsClosed) style="display:none;" @endif>
                                    <label class="form-label">
                                        วันนัดหมายครั้งต่อไป <span class="cs-required">*</span>
                                    </label>
                                    <input type="date"
                                           name="next_appointment_date"
                                           class="form-control js-next-appointment-date"
                                           value="{{ optional($followup->next_appointment_date)->format('Y-m-d') }}">
                                </div>
                            @endif

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> ปิด
                        </button>
                        <button type="submit" class="cs-btn-primary js-submit-once">
                            <i class="bi bi-check-circle"></i> บันทึกการแก้ไข
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endforeach
