@php
    $editThaiDate = function ($date) {
        if (empty($date)) return '-';
        try {
            $d = \Carbon\Carbon::parse($date);
            return $d->format('d/m/') . ($d->year + 543);
        } catch (\Throwable $e) {
            return '-';
        }
    };

    $editHasFollowups = $counseling->followups->count() > 0;
    $editCurrentClosed = in_array(
        $counseling->status,
        ['closed', 'goal_met', 'referred'],
        true
    );
@endphp

<div class="modal fade cs-modal"
     id="counselingEditModal"
     tabindex="-1"
     aria-labelledby="counselingEditModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="counselingEditModalLabel">
                            <i class="bi bi-pencil-square me-1 text-primary"></i>
                            การให้คำปรึกษาครั้งที่ {{ $counseling->session_no }}
                        </h5>
                        <div class="cs-help mt-1">วันที่ {{ $editThaiDate($counseling->session_date) }} • ผู้ให้คำปรึกษา {{ $counseling->counselor_name ?: '-' }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('counseling.update', $counseling->id) }}" method="POST" id="counselingEditForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_form_context" value="edit">

                    <div class="cs-section">
                        <div class="cs-section-title"><i class="bi bi-calendar-check"></i> ข้อมูลการให้คำปรึกษา</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">วันที่ให้คำปรึกษา <span class="cs-required">*</span></label>
                                <input type="date" name="session_date" class="form-control" max="{{ now('Asia/Bangkok')->format('Y-m-d') }}" value="{{ old('session_date', optional($counseling->session_date)->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ครั้งที่</label>
                                <input type="text" class="form-control cs-readonly" value="{{ $counseling->session_no }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ผู้ให้คำปรึกษา</label>
                                <input type="text" class="form-control cs-readonly" value="{{ $counseling->counselor_name ?: '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ช่องทางการให้คำปรึกษา <span class="cs-required">*</span></label>
                                <select name="channel" class="form-select" required>
                                    <option value="face_to_face" @selected(old('channel', $counseling->channel) === 'face_to_face')>พบโดยตรง</option>
                                    <option value="phone" @selected(old('channel', $counseling->channel) === 'phone')>โทรศัพท์</option>
                                    <option value="online" @selected(old('channel', $counseling->channel) === 'online')>ออนไลน์</option>
                                    <option value="home_visit" @selected(old('channel', $counseling->channel) === 'home_visit')>เยี่ยมบ้าน/สถานที่พัก</option>
                                    <option value="other" @selected(old('channel', $counseling->channel) === 'other')>อื่น ๆ</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">สถานที่</label>
                                <input type="text" name="location" class="form-control" maxlength="255" value="{{ old('location', $counseling->location) }}">
                            </div>
                        </div>
                    </div>

                    <div class="cs-section">
                        <div class="cs-section-title"><i class="bi bi-search-heart"></i> ประเด็นและการประเมิน</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">ประเด็นหรือสาเหตุที่มารับคำปรึกษา <span class="cs-required">*</span></label>
                                <textarea name="presenting_problem" class="form-control" required>{{ old('presenting_problem', $counseling->presenting_problem) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">การประเมินสภาพปัญหาเบื้องต้น <span class="cs-required">*</span></label>
                                <textarea name="assessment" class="form-control" required>{{ old('assessment', $counseling->assessment) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">จุดแข็ง / ทรัพยากรสนับสนุน</label>
                                <textarea name="strengths_resources" class="form-control">{{ old('strengths_resources', $counseling->strengths_resources) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="cs-section">
                        <div class="cs-section-title"><i class="bi bi-lightbulb"></i> การดำเนินการให้คำปรึกษา</div>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">เป้าหมายการให้คำปรึกษา</label><textarea name="goals" class="form-control">{{ old('goals', $counseling->goals) }}</textarea></div>
                            <div class="col-md-6"><label class="form-label">วิธีการ / เทคนิคที่ใช้</label><textarea name="interventions" class="form-control">{{ old('interventions', $counseling->interventions) }}</textarea></div>
                            <div class="col-md-6"><label class="form-label">คำแนะนำ / การช่วยเหลือ</label><textarea name="advice" class="form-control">{{ old('advice', $counseling->advice) }}</textarea></div>
                            <div class="col-md-6"><label class="form-label">ข้อตกลงร่วมกัน</label><textarea name="agreement" class="form-control">{{ old('agreement', $counseling->agreement) }}</textarea></div>
                        </div>
                    </div>

                    <div class="cs-section">
                        <div class="cs-section-title"><i class="bi bi-clipboard2-check"></i> ผลและแผนดำเนินการ</div>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">ผลการให้คำปรึกษา</label><textarea name="outcome" class="form-control">{{ old('outcome', $counseling->outcome) }}</textarea></div>
                            <div class="col-md-6"><label class="form-label">สิ่งที่ต้องดำเนินการต่อ</label><textarea name="next_steps" class="form-control">{{ old('next_steps', $counseling->next_steps) }}</textarea></div>

                            <div class="col-md-4">
                                <label class="form-label">ระดับความเสี่ยง <span class="cs-required">*</span></label>
                                <select name="risk_level" class="form-select js-risk-level" required>
                                    @foreach (['none' => 'ไม่พบความเสี่ยง', 'low' => 'ความเสี่ยงต่ำ', 'moderate' => 'ความเสี่ยงปานกลาง', 'high' => 'ความเสี่ยงสูง'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('risk_level', $counseling->risk_level) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8 js-risk-detail-wrap">
                                <label class="form-label">รายละเอียดความเสี่ยง</label>
                                <textarea name="risk_detail" class="form-control">{{ old('risk_detail', $counseling->risk_detail) }}</textarea>
                            </div>

                            @if (!$editHasFollowups)
                                <div class="col-md-6">
                                    <label class="form-label">
                                        สถานะหลังการให้คำปรึกษาครั้งนี้
                                        <span class="cs-required">*</span>
                                    </label>

                                    <select name="status"
                                            class="form-select js-workflow-status"
                                            required>
                                        <option value="follow_up"
                                                @selected(old('status', $editCurrentClosed ? 'closed' : 'follow_up') === 'follow_up')>
                                            ให้คำปรึกษาต่อเนื่อง
                                        </option>
                                        <option value="closed"
                                                @selected(old('status', $editCurrentClosed ? 'closed' : 'follow_up') === 'closed')>
                                            ยุติการให้คำปรึกษา
                                        </option>
                                    </select>
                                </div>

                                <div class="col-12 js-next-appointment-wrap"
                                     @if($editCurrentClosed) style="display:none;" @endif>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">
                                                วันนัดหมายครั้งต่อไป
                                                <span class="cs-required">*</span>
                                            </label>
                                            <input type="date"
                                                   name="next_appointment_date"
                                                   class="form-control js-next-appointment-date"
                                                   value="{{ old('next_appointment_date', optional($counseling->next_appointment_date)->format('Y-m-d')) }}">
                                        </div>

                                        <div class="col-md-8">
                                            <label class="form-label">
                                                ประเด็นที่จะให้คำปรึกษาต่อเนื่อง
                                                <span class="cs-required">*</span>
                                            </label>
                                            <textarea name="followup_focus"
                                                      class="form-control js-followup-focus">{{ old('followup_focus', $counseling->followup_focus) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-12">
                                    <label class="form-label">สถานะปัจจุบันของกระบวนการ</label>
                                    <input type="text"
                                           class="form-control cs-readonly"
                                           value="{{ $editCurrentClosed ? 'ยุติการให้คำปรึกษา' : 'ให้คำปรึกษาต่อเนื่อง' }}"
                                           readonly>
                                    <div class="cs-help">
                                        มีบันทึกการให้คำปรึกษาต่อเนื่องแล้ว สถานะปัจจุบันจึงยึดจากบันทึกต่อเนื่องล่าสุด
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    </form>

                    <div class="cs-section">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div class="cs-section-title mb-0"><i class="bi bi-clock-history"></i> การให้คำปรึกษาต่อเนื่อง</div>
                            @if (!$editCurrentClosed)
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-3" data-bs-toggle="modal" data-bs-target="#counselingFollowupCreateModal">
                                    <i class="bi bi-plus-circle me-1"></i> บันทึกการให้คำปรึกษาต่อเนื่อง
                                </button>
                            @endif
                        </div>

                        @if ($editCurrentClosed)
                            <div class="alert alert-light border mb-3 small text-muted">
                                <i class="bi bi-check-circle me-1 text-success"></i>
                                การให้คำปรึกษาครั้งนี้ยุติแล้ว จึงไม่สามารถเพิ่มบันทึกต่อเนื่องใหม่ได้
                            </div>
                        @endif

                        @if ($counseling->followups->count() > 0)
                            <div class="cs-followup-list">
                                @foreach ($counseling->followups as $followup)
                                    <div class="cs-followup-item">
                                        <div class="cs-followup-top">
                                            <div>
                                                <div class="cs-followup-title">ต่อเนื่องครั้งที่ {{ $followup->followup_no }}</div>
                                                <div class="cs-followup-meta">
                                                    {{ $editThaiDate($followup->followup_date) }} • {{ $followup->followup_method_label }} • ผู้บันทึก {{ $followup->recorder_name ?: '-' }}
                                                </div>
                                            </div>
                                            <div class="cs-actions">
                                                <button type="button" class="cs-action-btn cs-action-edit" data-bs-toggle="modal" data-bs-target="#followupEditModal{{ $followup->id }}" title="แก้ไขข้อมูลต่อเนื่อง"><i class="bi bi-pencil"></i></button>
                                                <form action="{{ route('counseling.followup.delete', $followup->id) }}" method="POST" class="d-inline js-followup-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="cs-action-btn cs-action-delete" title="ลบข้อมูลต่อเนื่อง"><i class="bi bi-trash3"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="cs-followup-progress">{{ $followup->progress ?: '-' }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-3 small">ยังไม่มีบันทึกการให้คำปรึกษาต่อเนื่องสำหรับครั้งนี้</div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('counseling.index', $client->id) }}" class="btn btn-light rounded-3 px-3">
                        <i class="bi bi-x-circle me-1"></i> ปิด
                    </a>
                    <button type="submit" form="counselingEditForm" class="cs-btn-primary js-submit-once"><i class="bi bi-check-circle"></i> บันทึกการแก้ไข</button>
                </div>
        </div>
    </div>
</div>
