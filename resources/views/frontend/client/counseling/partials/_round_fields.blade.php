@php
    $isEditRound = ($formMode ?? 'create') === 'edit';
    $item = $isEditRound ? $round : null;
    $currentStatus = old('status', $item?->status ?? '');
    $isCurrentOpen = in_array($currentStatus, ['ongoing', 'follow_up', 'improved'], true);
    $canChooseClosed = !$isEditRound || ($isLatestRound ?? true);
@endphp

<div class="csl-section">
    <div class="csl-section-title">
        <i class="bi bi-calendar-check"></i>
        ข้อมูลรอบนี้
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">
                วันที่ให้คำปรึกษา <span class="csl-required">*</span>
            </label>
            <input type="date"
                   name="followup_date"
                   class="form-control"
                   max="{{ now('Asia/Bangkok')->format('Y-m-d') }}"
                   value="{{ old('followup_date', $item?->followup_date?->format('Y-m-d')) }}"
                   required>
        </div>

        <div class="col-md-3">
            <label class="form-label">ครั้งที่</label>
            <input type="text"
                   class="form-control csl-readonly"
                   value="{{ $counseling->session_no }}"
                   readonly>
        </div>

        <div class="col-md-3">
            <label class="form-label">รอบที่</label>
            <input type="text"
                   class="form-control csl-readonly"
                   value="{{ $roundNo }}"
                   readonly>
        </div>

        <div class="col-md-3">
            <label class="form-label">ผู้ให้คำปรึกษา</label>
            <input type="text"
                   class="form-control csl-readonly"
                   value="{{ $isEditRound ? ($item->recorder_name ?: '-') : (auth()->user()->name ?? '-') }}"
                   readonly>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                ช่องทางการให้คำปรึกษา <span class="csl-required">*</span>
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
                            @selected(old('followup_method', $item?->followup_method ?? 'face_to_face') === $value)>
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
                   value="{{ old('location', $item?->location) }}"
                   placeholder="เช่น ห้องให้คำปรึกษา / บ้านพัก / โรงเรียน">
        </div>
    </div>
</div>

<div class="csl-section">
    <div class="csl-section-title">
        <i class="bi bi-chat-left-text"></i>
        ประเด็นและสถานการณ์ในรอบนี้
    </div>

    <div class="row g-3">
        <div class="col-12">
            <label class="form-label">
                หัวข้อ / ประเด็นที่ดำเนินการในรอบนี้ <span class="csl-required">*</span>
            </label>
            <textarea name="topic"
                      class="form-control"
                      required
                      placeholder="เช่น ติดตามการจัดการความเครียดและการวางแผนการเรียน">{{ old('topic', $item?->topic) }}</textarea>
            <div class="csl-help">
                ช่องนี้จะใช้เป็นหัวข้อหลักของรายงานรอบนี้ เพื่อให้ผู้อ่านทราบทันทีว่ารอบนี้ดำเนินการเรื่องใด
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                สรุปความคืบหน้า / สาระสำคัญ <span class="csl-required">*</span>
            </label>
            <textarea name="progress"
                      class="form-control"
                      required>{{ old('progress', $item?->progress) }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                สภาพปัจจุบัน / การประเมินในรอบนี้ <span class="csl-required">*</span>
            </label>
            <textarea name="current_assessment"
                      class="form-control"
                      required>{{ old('current_assessment', $item?->current_assessment) }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">การเปลี่ยนแปลงจากรอบก่อน</label>
            <textarea name="changes" class="form-control">{{ old('changes', $item?->changes) }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">ปัญหา / อุปสรรคที่พบ</label>
            <textarea name="barriers" class="form-control">{{ old('barriers', $item?->barriers) }}</textarea>
        </div>
    </div>
</div>

<div class="csl-section">
    <div class="csl-section-title">
        <i class="bi bi-lightbulb"></i>
        การดำเนินการให้คำปรึกษาในรอบนี้
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">เป้าหมายของรอบนี้</label>
            <textarea name="session_goal" class="form-control">{{ old('session_goal', $item?->session_goal) }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">แนวทาง / เทคนิคที่ใช้</label>
            <textarea name="interventions" class="form-control">{{ old('interventions', $item?->interventions) }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">คำแนะนำ / การช่วยเหลือ</label>
            <textarea name="advice" class="form-control">{{ old('advice', $item?->advice) }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">ข้อตกลงร่วมกัน</label>
            <textarea name="agreement" class="form-control">{{ old('agreement', $item?->agreement) }}</textarea>
        </div>

        <div class="col-12">
            <label class="form-label">การช่วยเหลือเพิ่มเติม / การประสานงาน</label>
            <textarea name="additional_support" class="form-control">{{ old('additional_support', $item?->additional_support) }}</textarea>
        </div>
    </div>
</div>

<div class="csl-section">
    <div class="csl-section-title">
        <i class="bi bi-clipboard2-check"></i>
        ผลการให้คำปรึกษาและความเสี่ยง
    </div>

    <div class="row g-3">
        <div class="col-12">
            <label class="form-label">
                ผลที่เกิดขึ้นจากการให้คำปรึกษาในรอบนี้ <span class="csl-required">*</span>
            </label>
            <textarea name="result"
                      class="form-control"
                      required>{{ old('result', $item?->result) }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">แนวทางต่อ / ข้อเสนอแนะ</label>
            <textarea name="next_action"
                      class="form-control"
                      placeholder="หากยังต่อเนื่อง ให้ระบุสิ่งที่ควรทำในรอบถัดไป; หากจบ ให้ระบุข้อเสนอแนะหลังจบ">{{ old('next_action', $item?->next_action) }}</textarea>
        </div>

        <div class="col-md-3">
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
                            @selected(old('risk_level', $item?->risk_level ?? 'none') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 js-risk-detail-wrap">
            <label class="form-label">รายละเอียดความเสี่ยง</label>
            <textarea name="risk_detail" class="form-control">{{ old('risk_detail', $item?->risk_detail) }}</textarea>
        </div>
    </div>
</div>

<div class="csl-section">
    <div class="csl-section-title">
        <i class="bi bi-signpost-split"></i>
        สถานะหลังจบรอบนี้
    </div>

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

                @if ($canChooseClosed)
                    <option value="goal_met" @selected($currentStatus === 'goal_met')>
                        บรรลุเป้าหมาย
                    </option>
                    <option value="referred" @selected($currentStatus === 'referred')>
                        ส่งต่อ
                    </option>
                    <option value="closed" @selected($currentStatus === 'closed')>
                        ยุติการให้คำปรึกษา
                    </option>
                @endif
            </select>
            <div class="csl-help">
                บรรลุเป้าหมาย / ส่งต่อ / ยุติการให้คำปรึกษา = สิ้นสุด “ครั้งที่ {{ $counseling->session_no }}” และไม่สามารถเพิ่มรอบใหม่ได้
            </div>
        </div>

        <div class="col-md-6 js-next-appointment-wrap"
             @if(!$isCurrentOpen) style="display:none;" @endif>
            <label class="form-label">
                วันนัดหมายครั้งต่อไป <span class="csl-required">*</span>
            </label>
            <input type="date"
                   name="next_appointment_date"
                   class="form-control js-next-appointment-date"
                   value="{{ old('next_appointment_date', $item?->next_appointment_date?->format('Y-m-d')) }}">
            <div class="csl-help">
                ช่องนี้จะแสดงเฉพาะเมื่อสถานะยังอยู่ระหว่างให้คำปรึกษาหรืออยู่ระหว่างติดตาม
            </div>
        </div>
    </div>
</div>
