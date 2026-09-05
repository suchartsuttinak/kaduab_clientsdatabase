@php
    $workflowItem = $workflowItem ?? null;
    $workflowBag = $workflowBag ?? 'default';
    $workflowUseOld = (bool) ($workflowUseOld ?? false);
    $workflowCanEdit = (bool) ($workflowCanEdit ?? true);
    $workflowCanClose = (bool) ($workflowCanClose ?? true);

    $statusValue = $workflowUseOld
        ? old('status', 'ongoing')
        : ($workflowItem->status ?? 'ongoing');
    $riskValue = $workflowUseOld
        ? old('risk_level', 'none')
        : ($workflowItem->risk_level ?? 'none');
    $riskDetailValue = $workflowUseOld
        ? old('risk_detail')
        : ($workflowItem->risk_detail ?? null);
    $nextAppointmentValue = $workflowUseOld
        ? old('next_appointment_date')
        : ($workflowItem->next_appointment_date ?? null);
    $followupFocusValue = $workflowUseOld
        ? old('followup_focus')
        : ($workflowItem->followup_focus ?? null);

    $statusLabels = [
        'ongoing' => 'อยู่ระหว่างการดำเนินงาน',
        'goal_met' => 'บรรลุเป้าหมาย',
        'referred' => 'ส่งต่อข้อมูล',
    ];
    $riskLabels = [
        'none' => 'ไม่พบความเสี่ยง',
        'low' => 'ความเสี่ยงต่ำ',
        'moderate' => 'ความเสี่ยงปานกลาง',
        'high' => 'ความเสี่ยงสูง',
    ];

    $bag = $errors->getBag($workflowBag);
@endphp

<div class="form-section observe-workflow-section">
    <h6 class="form-section-title">
        <i class="bi bi-shield-check"></i>
        การประเมินความเสี่ยงและสถานะในรอบนี้
    </h6>

    @if (!$workflowCanEdit)
        <div class="observe-workflow-readonly">
            <div class="observe-workflow-readonly__item">
                <span>สถานะ</span>
                <strong>{{ $statusLabels[$statusValue] ?? '-' }}</strong>
            </div>

            <div class="observe-workflow-readonly__item">
                <span>ระดับความเสี่ยง</span>
                <strong>{{ $riskLabels[$riskValue] ?? '-' }}</strong>
                @if ($riskDetailValue)
                    <small>{{ $riskDetailValue }}</small>
                @endif
            </div>

            @if ($statusValue === 'ongoing')
                <div class="observe-workflow-readonly__item">
                    <span>วันนัดหมายครั้งต่อไป</span>
                    <strong>{{ $nextAppointmentValue ?: '-' }}</strong>
                </div>
                <div class="observe-workflow-readonly__item observe-workflow-readonly__wide">
                    <span>ประเด็นที่จะดำเนินการต่อในรอบถัดไป</span>
                    <strong>{{ $followupFocusValue ?: '-' }}</strong>
                </div>
            @elseif ($statusValue === 'referred')
                <div class="observe-referral-restricted observe-workflow-readonly__wide">
                    <i class="bi bi-send-check-fill"></i>
                    <div>
                        <strong>ส่งต่อข้อมูลแล้ว</strong>
                        <div>งานในส่วนผู้บันทึกเดิมสิ้นสุดในรอบนี้ และส่งต่อไปยังผู้ใช้งานที่ได้รับสิทธิ์ศูนย์รับเคสพฤติกรรมเพื่อดำเนินการช่วยเหลือต่อ</div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="row g-3 observe-workflow-fields">
            <div class="col-12 col-md-4">
                <label class="form-label-modern">
                    ระดับความเสี่ยง <span class="text-danger">*</span>
                </label>
                <select name="risk_level"
                        class="form-select form-select-modern js-observe-risk-level {{ $bag->has('risk_level') ? 'is-invalid' : '' }}"
                        required>
                    @foreach ($riskLabels as $value => $label)
                        <option value="{{ $value }}" @selected($riskValue === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @if ($bag->has('risk_level'))
                    <div class="invalid-feedback">{{ $bag->first('risk_level') }}</div>
                @endif
            </div>

            <div class="col-12 col-md-8 js-observe-risk-detail-wrap"
                 @if($riskValue === 'none') style="display:none;" @endif>
                <label class="form-label-modern">
                    รายละเอียดความเสี่ยง
                    <span class="text-danger js-observe-risk-detail-required"
                          @if(!in_array($riskValue, ['moderate', 'high'], true)) style="display:none;" @endif>*</span>
                </label>
                <textarea name="risk_detail"
                          rows="3"
                          maxlength="5000"
                          class="form-control form-control-modern js-observe-risk-detail {{ $bag->has('risk_detail') ? 'is-invalid' : '' }}"
                          placeholder="ระบุปัจจัยเสี่ยง สัญญาณเตือน หรือสิ่งที่ต้องเฝ้าระวัง">{{ $riskDetailValue }}</textarea>
                @if ($bag->has('risk_detail'))
                    <div class="invalid-feedback">{{ $bag->first('risk_detail') }}</div>
                @endif
            </div>

            <div class="col-12 col-md-5">
                <label class="form-label-modern">
                    สถานะในรอบนี้ <span class="text-danger">*</span>
                </label>
                <select name="status"
                        class="form-select form-select-modern js-observe-workflow-status {{ $bag->has('status') ? 'is-invalid' : '' }}"
                        required>
                    <option value="ongoing" @selected($statusValue === 'ongoing')>อยู่ระหว่างการดำเนินงาน</option>
                    @if ($workflowCanClose)
                        <option value="goal_met" @selected($statusValue === 'goal_met')>บรรลุเป้าหมาย</option>
                        <option value="referred" @selected($statusValue === 'referred')>ส่งต่อความช่วยเหลือ</option>
                    @endif
                </select>
                @if ($bag->has('status'))
                    <div class="invalid-feedback">{{ $bag->first('status') }}</div>
                @endif
                <small class="text-muted d-block mt-1">
                    เมื่อเลือก “ส่งต่อความช่วยเหลือ” ระบบจะเปลี่ยนสถานะเป็น “ส่งต่อข้อมูล” และปิดการติดตามในส่วนเดิม
                </small>
            </div>

            <div class="col-12 col-md-7 js-observe-next-wrap"
                 @if($statusValue !== 'ongoing') style="display:none;" @endif>
                <div class="row g-3">
                    <div class="col-12 col-md-5">
                        <label class="form-label-modern">
                            วันนัดหมายครั้งต่อไป <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="next_appointment_date"
                               value="{{ $nextAppointmentValue }}"
                               class="form-control form-control-modern js-observe-next-date {{ $bag->has('next_appointment_date') ? 'is-invalid' : '' }}">
                        @if ($bag->has('next_appointment_date'))
                            <div class="invalid-feedback">{{ $bag->first('next_appointment_date') }}</div>
                        @endif
                    </div>
                    <div class="col-12 col-md-7">
                        <label class="form-label-modern">
                            ประเด็นที่จะดำเนินการต่อในรอบถัดไป <span class="text-danger">*</span>
                        </label>
                        <textarea name="followup_focus"
                                  rows="3"
                                  maxlength="5000"
                                  class="form-control form-control-modern js-observe-followup-focus {{ $bag->has('followup_focus') ? 'is-invalid' : '' }}"
                                  placeholder="ระบุเป้าหมายหรือประเด็นสำคัญสำหรับรอบถัดไป">{{ $followupFocusValue }}</textarea>
                        @if ($bag->has('followup_focus'))
                            <div class="invalid-feedback">{{ $bag->first('followup_focus') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
