@php
    $referralItem = $referralItem ?? null;
    $referralBag = $referralBag ?? 'default';
    $referralUseOld = (bool) ($referralUseOld ?? false);
    $referralSourceDate = $referralSourceDate ?? ($observe->date ?? now('Asia/Bangkok')->toDateString());
    $bag = $errors->getBag($referralBag);

    $processLabels = [
        'group_therapy' => 'กลุ่มบำบัด',
        'family_therapy' => 'ครอบครัวบำบัด',
        'psychotherapy_counseling' => 'จิตบำบัด/ให้คำปรึกษา',
        'behavior_therapy' => 'พฤติกรรมบำบัด',
        'referred_treatment' => 'ส่งต่อเข้ารับการบำบัด',
    ];
    $riskLabels = [
        'none' => 'ไม่พบความเสี่ยง',
        'low' => 'ความเสี่ยงต่ำ',
        'moderate' => 'ความเสี่ยงปานกลาง',
        'high' => 'ความเสี่ยงสูง',
    ];

    $value = function ($name, $default = null) use ($referralUseOld, $referralItem) {
        if ($referralUseOld) {
            return old($name, $default);
        }

        $stored = $referralItem?->{$name};
        if ($stored instanceof \Carbon\CarbonInterface) {
            return $stored->format('Y-m-d');
        }

        return $stored ?? $default;
    };

    $statusValue = $value('status', 'ongoing');
    $riskValue = $value('risk_level', 'none');
@endphp

<div class="row g-3 js-referral-workflow-form">
    <div class="col-12 col-md-4">
        <label class="form-label-modern">วันที่ดำเนินการในรอบนี้ <span class="text-danger">*</span></label>
        <input type="date"
               name="action_date"
               value="{{ $value('action_date', now('Asia/Bangkok')->toDateString()) }}"
               min="{{ $referralSourceDate }}"
               max="{{ now('Asia/Bangkok')->toDateString() }}"
               class="form-control form-control-modern {{ $bag->has('action_date') ? 'is-invalid' : '' }}"
               required>
        @if($bag->has('action_date'))<div class="invalid-feedback">{{ $bag->first('action_date') }}</div>@endif
    </div>

    <div class="col-12 col-md-8">
        <label class="form-label-modern">กระบวนการช่วยเหลือ <span class="text-danger">*</span></label>
        <select name="assistance_process"
                class="form-select form-select-modern {{ $bag->has('assistance_process') ? 'is-invalid' : '' }}"
                required>
            <option value="">-- เลือกกระบวนการช่วยเหลือ --</option>
            @foreach($processLabels as $processValue => $label)
                <option value="{{ $processValue }}" @selected($value('assistance_process') === $processValue)>{{ $label }}</option>
            @endforeach
        </select>
        @if($bag->has('assistance_process'))<div class="invalid-feedback">{{ $bag->first('assistance_process') }}</div>@endif
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label-modern">แนวทางแก้ไข <span class="text-danger">*</span></label>
        <textarea name="solution" rows="4" maxlength="5000"
                  class="form-control form-control-modern {{ $bag->has('solution') ? 'is-invalid' : '' }}"
                  required>{{ $value('solution') }}</textarea>
        @if($bag->has('solution'))<div class="invalid-feedback">{{ $bag->first('solution') }}</div>@endif
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label-modern">ผลลัพธ์ <span class="text-danger">*</span></label>
        <textarea name="result" rows="4" maxlength="5000"
                  class="form-control form-control-modern {{ $bag->has('result') ? 'is-invalid' : '' }}"
                  required>{{ $value('result') }}</textarea>
        @if($bag->has('result'))<div class="invalid-feedback">{{ $bag->first('result') }}</div>@endif
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label-modern">ระดับความเสี่ยง <span class="text-danger">*</span></label>
        <select name="risk_level"
                class="form-select form-select-modern js-referral-risk-level {{ $bag->has('risk_level') ? 'is-invalid' : '' }}"
                required>
            @foreach($riskLabels as $riskValueOption => $label)
                <option value="{{ $riskValueOption }}" @selected($riskValue === $riskValueOption)>{{ $label }}</option>
            @endforeach
        </select>
        @if($bag->has('risk_level'))<div class="invalid-feedback">{{ $bag->first('risk_level') }}</div>@endif
    </div>

    <div class="col-12 col-md-8 js-referral-risk-detail-wrap" @if($riskValue === 'none') style="display:none;" @endif>
        <label class="form-label-modern">
            รายละเอียดความเสี่ยง
            <span class="text-danger js-referral-risk-required" @if(!in_array($riskValue, ['moderate','high'], true)) style="display:none;" @endif>*</span>
        </label>
        <textarea name="risk_detail" rows="3" maxlength="5000"
                  class="form-control form-control-modern js-referral-risk-detail {{ $bag->has('risk_detail') ? 'is-invalid' : '' }}"
                  placeholder="ระบุปัจจัยเสี่ยง สิ่งที่ต้องเฝ้าระวัง หรือมาตรการความปลอดภัย">{{ $value('risk_detail') }}</textarea>
        @if($bag->has('risk_detail'))<div class="invalid-feedback">{{ $bag->first('risk_detail') }}</div>@endif
    </div>

    <div class="col-12 col-md-5">
        <label class="form-label-modern">สถานะ <span class="text-danger">*</span></label>
        <select name="status"
                class="form-select form-select-modern js-referral-status {{ $bag->has('status') ? 'is-invalid' : '' }}"
                required>
            <option value="ongoing" @selected($statusValue === 'ongoing')>อยู่ระหว่างดำเนินการ</option>
            <option value="goal_met" @selected($statusValue === 'goal_met')>บรรลุเป้าหมาย</option>
        </select>
        @if($bag->has('status'))<div class="invalid-feedback">{{ $bag->first('status') }}</div>@endif
        <small class="text-muted d-block mt-1">เมื่อเลือก “บรรลุเป้าหมาย” จะสิ้นสุดการช่วยเหลือหลังส่งต่อและไม่เปิดรอบใหม่</small>
    </div>

    <div class="col-12 col-md-7 js-referral-next-wrap" @if($statusValue !== 'ongoing') style="display:none;" @endif>
        <div class="row g-3">
            <div class="col-12 col-md-5">
                <label class="form-label-modern">วันนัดหมายครั้งต่อไป <span class="text-danger">*</span></label>
                <input type="date"
                       name="next_appointment_date"
                       value="{{ $value('next_appointment_date') }}"
                       class="form-control form-control-modern js-referral-next-date {{ $bag->has('next_appointment_date') ? 'is-invalid' : '' }}">
                @if($bag->has('next_appointment_date'))<div class="invalid-feedback">{{ $bag->first('next_appointment_date') }}</div>@endif
            </div>
            <div class="col-12 col-md-7">
                <label class="form-label-modern">ประเด็นที่จะดำเนินการต่อในรอบถัดไป <span class="text-danger">*</span></label>
                <textarea name="followup_focus" rows="3" maxlength="5000"
                          class="form-control form-control-modern js-referral-followup-focus {{ $bag->has('followup_focus') ? 'is-invalid' : '' }}"
                          placeholder="ระบุประเด็นหลักที่ผู้รับช่วงจะดำเนินการต่อ">{{ $value('followup_focus') }}</textarea>
                @if($bag->has('followup_focus'))<div class="invalid-feedback">{{ $bag->first('followup_focus') }}</div>@endif
            </div>
        </div>
    </div>
</div>
