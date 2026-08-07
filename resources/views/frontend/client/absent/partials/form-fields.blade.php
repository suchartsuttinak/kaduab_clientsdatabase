<div class="ab-form-section">
    <div class="ab-form-section-head">
        <span class="ab-form-section-icon" aria-hidden="true">
            <i class="bi bi-calendar-range"></i>
        </span>
        <div>
            <h6 class="ab-form-section-title">ข้อมูลวันที่</h6>
            <p class="ab-form-section-desc">ระบุวันที่ขาดเรียนและวันที่เจ้าหน้าที่บันทึกข้อมูล</p>
        </div>
    </div>

    <div class="ab-form-grid">
        <div class="ab-col-6">
            <label class="ab-form-label" for="{{ $prefix }}absent_date">
                <i class="bi bi-calendar-x"></i>
                วันที่ขาดเรียน <span class="text-danger">*</span>
            </label>
            <input type="date"
                   id="{{ $prefix }}absent_date"
                   name="absent_date"
                   class="form-control ab-form-control @if(empty($prefix)) @error('absent_date') is-invalid @enderror @endif"
                   value="{{ $absentDate ?? '' }}"
                   max="{{ now('Asia/Bangkok')->toDateString() }}"
                   required>
            @if(empty($prefix))
                @error('absent_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @else
                <div class="invalid-feedback"></div>
            @endif
        </div>

        <div class="ab-col-6">
            <label class="ab-form-label" for="{{ $prefix }}record_date">
                <i class="bi bi-calendar-check"></i>
                วันที่บันทึก <span class="text-danger">*</span>
            </label>
            <input type="date"
                   id="{{ $prefix }}record_date"
                   name="record_date"
                   class="form-control ab-form-control @if(empty($prefix)) @error('record_date') is-invalid @enderror @endif"
                   value="{{ $recordDate ?? '' }}"
                   max="{{ now('Asia/Bangkok')->toDateString() }}"
                   required>
            @if(empty($prefix))
                @error('record_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @else
                <div class="invalid-feedback"></div>
            @endif
        </div>
    </div>
</div>

<div class="ab-form-section">
    <div class="ab-form-section-head">
        <span class="ab-form-section-icon" aria-hidden="true">
            <i class="bi bi-journal-text"></i>
        </span>
        <div>
            <h6 class="ab-form-section-title">รายละเอียดการขาดเรียน</h6>
            <p class="ab-form-section-desc">บันทึกสาเหตุและแนวทางดำเนินงานที่เกี่ยวข้อง</p>
        </div>
    </div>

    <div class="ab-form-grid">
        <div class="ab-col-6">
            <label class="ab-form-label" for="{{ $prefix }}cause">
                <i class="bi bi-question-circle"></i>
                สาเหตุที่ขาดเรียน
            </label>
            <textarea id="{{ $prefix }}cause"
                      name="cause"
                      class="form-control ab-form-control @if(empty($prefix)) @error('cause') is-invalid @enderror @endif"
                      rows="4"
                      placeholder="ระบุสาเหตุที่ขาดเรียน">{{ $cause ?? '' }}</textarea>
            @if(empty($prefix))
                @error('cause')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @else
                <div class="invalid-feedback"></div>
            @endif
        </div>

        <div class="ab-col-6">
            <label class="ab-form-label" for="{{ $prefix }}operation">
                <i class="bi bi-clipboard-check"></i>
                การดำเนินงาน
            </label>
            <textarea id="{{ $prefix }}operation"
                      name="operation"
                      class="form-control ab-form-control @if(empty($prefix)) @error('operation') is-invalid @enderror @endif"
                      rows="4"
                      placeholder="ระบุการดำเนินงาน">{{ $operation ?? '' }}</textarea>
            @if(empty($prefix))
                @error('operation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @else
                <div class="invalid-feedback"></div>
            @endif
        </div>
    </div>
</div>

<div class="ab-form-section">
    <div class="ab-form-section-head">
        <span class="ab-form-section-icon" aria-hidden="true">
            <i class="bi bi-person-check"></i>
        </span>
        <div>
            <h6 class="ab-form-section-title">ข้อมูลเพิ่มเติม</h6>
            <p class="ab-form-section-desc">ระบุหมายเหตุและชื่อผู้ดูแลเด็กตามความเหมาะสม</p>
        </div>
    </div>

    <div class="ab-form-grid">
        <div class="ab-col-6">
            <label class="ab-form-label" for="{{ $prefix }}remark">
                <i class="bi bi-chat-left-text"></i>
                หมายเหตุ
            </label>
            <textarea id="{{ $prefix }}remark"
                      name="remark"
                      class="form-control ab-form-control @if(empty($prefix)) @error('remark') is-invalid @enderror @endif"
                      rows="4"
                      placeholder="ระบุข้อมูลเพิ่มเติม">{{ $remark ?? '' }}</textarea>
            @if(empty($prefix))
                @error('remark')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @else
                <div class="invalid-feedback"></div>
            @endif
        </div>

        <div class="ab-col-6">
            <label class="ab-form-label" for="{{ $prefix }}teacher">
                <i class="bi bi-person-vcard"></i>
                ชื่อ-สกุล ผู้ดูแลเด็ก
            </label>
            <input type="text"
                   id="{{ $prefix }}teacher"
                   name="teacher"
                   class="form-control ab-form-control @if(empty($prefix)) @error('teacher') is-invalid @enderror @endif"
                   value="{{ $teacher ?? '' }}"
                   maxlength="255"
                   placeholder="ระบุชื่อผู้ดูแลเด็ก">
            @if(empty($prefix))
                @error('teacher')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @else
                <div class="invalid-feedback"></div>
            @endif
        </div>
    </div>
</div>
