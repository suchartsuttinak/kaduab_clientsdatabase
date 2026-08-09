@php
    $isEditInitial = ($formMode ?? 'create') === 'edit';
    $initial = $isEditInitial ? $counseling : null;
    $hasLaterRounds = $isEditInitial
        ? ($counseling->followups->isNotEmpty())
        : false;

    $currentStatus = old(
        'status',
        $initial?->status ?? ''
    );

    $isCurrentOpen = in_array(
        $currentStatus,
        ['ongoing', 'follow_up', 'improved'],
        true
    );
@endphp

<div class="csl-section">
    <div class="csl-section-title">
        <i class="bi bi-calendar-check"></i>
        ข้อมูลการให้คำปรึกษา
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">
                วันที่ให้คำปรึกษา <span class="csl-required">*</span>
            </label>
            <input type="date"
                   name="session_date"
                   class="form-control"
                   max="{{ now('Asia/Bangkok')->format('Y-m-d') }}"
                   value="{{ old('session_date', $initial?->session_date?->format('Y-m-d') ?? now('Asia/Bangkok')->format('Y-m-d')) }}"
                   required>
        </div>

        <div class="col-md-3">
            <label class="form-label">ครั้งที่</label>
            <input type="text"
                   class="form-control csl-readonly"
                   value="{{ $isEditInitial ? $initial->session_no : $nextSessionNo }}"
                   readonly>
        </div>

        <div class="col-md-3">
            <label class="form-label">รอบที่</label>
            <input type="text"
                   class="form-control csl-readonly"
                   value="1"
                   readonly>
        </div>

        <div class="col-md-3">
            <label class="form-label">ผู้ให้คำปรึกษา</label>
            <input type="text"
                   class="form-control csl-readonly"
                   value="{{ $isEditInitial ? ($initial->counselor_name ?: '-') : (auth()->user()->name ?? '-') }}"
                   readonly>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                ช่องทางการให้คำปรึกษา <span class="csl-required">*</span>
            </label>
            <select name="channel" class="form-select" required>
                @foreach ([
                    'face_to_face' => 'พบโดยตรง',
                    'phone' => 'โทรศัพท์',
                    'online' => 'ออนไลน์',
                    'home_visit' => 'เยี่ยมบ้าน/สถานที่พัก',
                    'other' => 'อื่น ๆ',
                ] as $value => $label)
                    <option value="{{ $value }}"
                            @selected(old('channel', $initial?->channel ?? 'face_to_face') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">สถานที่</label>
            <input type="text"
                   name="location"
                   class="form-control"
                   maxlength="255"
                   value="{{ old('location', $initial?->location) }}"
                   placeholder="เช่น ห้องให้คำปรึกษา / บ้านพัก / โรงเรียน">
        </div>
    </div>
</div>

<div class="csl-section">
    <div class="csl-section-title">
        <i class="bi bi-search-heart"></i>
        ประเด็นและการประเมิน
    </div>

    <div class="row g-3">
        <div class="col-12">
            <label class="form-label">
                ประเด็นหรือสาเหตุที่มารับคำปรึกษา <span class="csl-required">*</span>
            </label>
            <textarea name="presenting_problem"
                      class="form-control"
                      required
                      placeholder="ระบุประเด็นหลักให้ชัด เพื่อใช้เป็นหัวข้อของการให้คำปรึกษาครั้งนี้">{{ old('presenting_problem', $initial?->presenting_problem) }}</textarea>
        </div>

        <div class="col-12">
            <label class="form-label">
                สภาพปัญหา / การประเมินเบื้องต้น <span class="csl-required">*</span>
            </label>
            <textarea name="assessment"
                      class="form-control"
                      required
                      placeholder="สรุปข้อเท็จจริง สภาพอารมณ์ ความคิด พฤติกรรม และปัจจัยที่เกี่ยวข้อง">{{ old('assessment', $initial?->assessment) }}</textarea>
        </div>

        <div class="col-12">
            <label class="form-label">จุดแข็ง / ทรัพยากรสนับสนุน</label>
            <textarea name="strengths_resources"
                      class="form-control"
                      placeholder="เช่น แรงจูงใจ ครอบครัว บุคคลสนับสนุน ความสามารถ หรือทรัพยากรที่มี">{{ old('strengths_resources', $initial?->strengths_resources) }}</textarea>
        </div>
    </div>
</div>

<div class="csl-section">
    <div class="csl-section-title">
        <i class="bi bi-lightbulb"></i>
        การดำเนินการให้คำปรึกษา
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">เป้าหมายของรอบนี้</label>
            <textarea name="goals" class="form-control">{{ old('goals', $initial?->goals) }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">แนวทาง / เทคนิคที่ใช้</label>
            <textarea name="interventions" class="form-control">{{ old('interventions', $initial?->interventions) }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">คำแนะนำ / การช่วยเหลือ</label>
            <textarea name="advice" class="form-control">{{ old('advice', $initial?->advice) }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">ข้อตกลงร่วมกัน</label>
            <textarea name="agreement" class="form-control">{{ old('agreement', $initial?->agreement) }}</textarea>
        </div>
    </div>
</div>

<div class="csl-section">
    <div class="csl-section-title">
        <i class="bi bi-clipboard2-check"></i>
        ผลการให้คำปรึกษาและความเสี่ยง
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">ผลที่เกิดขึ้น / การเปลี่ยนแปลง</label>
            <textarea name="outcome" class="form-control">{{ old('outcome', $initial?->outcome) }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">แนวทางหรือสิ่งที่ต้องดำเนินการต่อ</label>
            <textarea name="next_steps" class="form-control">{{ old('next_steps', $initial?->next_steps) }}</textarea>
        </div>

        <div class="col-md-4">
            <label class="form-label">
                ระดับความเสี่ยง <span class="csl-required">*</span>
            </label>
            <select name="risk_level"
                    class="form-select js-risk-level"
                    required>
                @foreach ([
                    'none' => 'ไม่พบความเสี่ยง',
                    'low' => 'ความเสี่ยงต่ำ',
                    'moderate' => 'ความเสี่ยงปานกลาง',
                    'high' => 'ความเสี่ยงสูง',
                ] as $value => $label)
                    <option value="{{ $value }}"
                            @selected(old('risk_level', $initial?->risk_level ?? 'none') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-8 js-risk-detail-wrap">
            <label class="form-label">รายละเอียดความเสี่ยง</label>
            <textarea name="risk_detail"
                      class="form-control"
                      placeholder="ระบุปัจจัยเสี่ยง การเฝ้าระวัง หรือการช่วยเหลือที่ดำเนินการ">{{ old('risk_detail', $initial?->risk_detail) }}</textarea>
        </div>
    </div>
</div>

<div class="csl-section">
    <div class="csl-section-title">
        <i class="bi bi-signpost-split"></i>
        สถานะหลังจบรอบนี้
    </div>

    @if ($hasLaterRounds)
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">สถานะปัจจุบันของการให้คำปรึกษาครั้งนี้</label>
                <input type="text"
                       class="form-control csl-readonly"
                       value="{{ $initial->status_label }}"
                       readonly>
                <div class="csl-help">
                    มีรอบที่ 2 ขึ้นไปแล้ว สถานะปัจจุบันจึงยึดจากรอบล่าสุด และไม่แก้จากรอบที่ 1
                </div>
            </div>
        </div>
    @else
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">
                    สถานะ <span class="csl-required">*</span>
                </label>
                <select name="status"
                        class="form-select js-workflow-status"
                        required>
                    <option value="">-- เลือกสถานะ --</option>
                    <option value="ongoing" @selected($currentStatus === 'ongoing')>
                        อยู่ระหว่างให้คำปรึกษา
                    </option>
                    <option value="follow_up" @selected($currentStatus === 'follow_up')>
                        อยู่ระหว่างติดตาม
                    </option>
                    <option value="goal_met" @selected($currentStatus === 'goal_met')>
                        บรรลุเป้าหมาย
                    </option>
                    <option value="referred" @selected($currentStatus === 'referred')>
                        ส่งต่อ
                    </option>
                    <option value="closed" @selected($currentStatus === 'closed')>
                        ยุติการให้คำปรึกษา
                    </option>
                </select>
                <div class="csl-help">
                    บรรลุเป้าหมาย / ส่งต่อ / ยุติการให้คำปรึกษา ถือว่าสิ้นสุดการให้คำปรึกษาครั้งนี้
                </div>
            </div>

            <div class="col-12 js-next-appointment-wrap"
                 @if(!$isCurrentOpen) style="display:none;" @endif>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">
                            วันนัดหมายครั้งต่อไป <span class="csl-required">*</span>
                        </label>
                        <input type="date"
                               name="next_appointment_date"
                               class="form-control js-next-appointment-date"
                               value="{{ old('next_appointment_date', $initial?->next_appointment_date?->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">
                            ประเด็นที่จะดำเนินการต่อในรอบถัดไป <span class="csl-required">*</span>
                        </label>
                        <textarea name="followup_focus"
                                  class="form-control js-followup-focus"
                                  placeholder="ระบุหัวข้อที่ผู้ให้คำปรึกษาควรดำเนินการต่อในรอบหน้า">{{ old('followup_focus', $initial?->followup_focus) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
