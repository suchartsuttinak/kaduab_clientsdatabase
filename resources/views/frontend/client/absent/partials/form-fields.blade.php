<div class="row g-3">
    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold">วันที่ขาดเรียน</label>
        <input type="date"
               id="{{ $prefix }}absent_date"
               name="absent_date"
               class="form-control form-control-sm @if(empty($prefix)) @error('absent_date') is-invalid @enderror @endif"
               value="{{ $absentDate ?? '' }}"
               max="{{ now('Asia/Bangkok')->toDateString() }}"
               required>
        @if(empty($prefix))
            @error('absent_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold">วันที่บันทึก</label>
        <input type="date"
               id="{{ $prefix }}record_date"
               name="record_date"
               class="form-control form-control-sm @if(empty($prefix)) @error('record_date') is-invalid @enderror @endif"
               value="{{ $recordDate ?? '' }}"
               max="{{ now('Asia/Bangkok')->toDateString() }}"
               required>
        @if(empty($prefix))
            @error('record_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold">สาเหตุที่ขาดเรียน</label>
        <textarea id="{{ $prefix }}cause"
                  name="cause"
                  class="form-control form-control-sm @if(empty($prefix)) @error('cause') is-invalid @enderror @endif"
                  rows="3">{{ $cause ?? '' }}</textarea>
        @if(empty($prefix))
            @error('cause')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold">การดำเนินงาน</label>
        <textarea id="{{ $prefix }}operation"
                  name="operation"
                  class="form-control form-control-sm @if(empty($prefix)) @error('operation') is-invalid @enderror @endif"
                  rows="3">{{ $operation ?? '' }}</textarea>
        @if(empty($prefix))
            @error('operation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold">หมายเหตุ</label>
        <textarea id="{{ $prefix }}remark"
                  name="remark"
                  class="form-control form-control-sm @if(empty($prefix)) @error('remark') is-invalid @enderror @endif"
                  rows="3">{{ $remark ?? '' }}</textarea>
        @if(empty($prefix))
            @error('remark')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold">ชื่อ-สกุล ผู้ดูแลเด็ก</label>
        <input type="text"
               id="{{ $prefix }}teacher"
               name="teacher"
               class="form-control form-control-sm @if(empty($prefix)) @error('teacher') is-invalid @enderror @endif"
               value="{{ $teacher ?? '' }}">
        @if(empty($prefix))
            @error('teacher')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>
</div>