@php
    $formChild = $child ?? null;
    $isEdit = !empty($formChild);
    $instanceKey = $yearListId ?? ('scholarship_form_' . ($formChild->id ?? 'create'));
    $photoInputId = 'scholarship_photo_' . $instanceKey;
    $photoPreviewId = 'scholarship_photo_preview_' . $instanceKey;
    $photoEmptyId = 'scholarship_photo_empty_' . $instanceKey;

    $photoUrl = null;

    if (!empty($formChild?->photo)) {
        $photoUrl = str_starts_with($formChild->photo, 'upload/')
            ? asset($formChild->photo)
            : asset('storage/' . $formChild->photo);
    }
@endphp

@once
    <style>
        .scholarship-form-section{
            padding:16px;
            margin-bottom:14px;
            background:#ffffff;
            border:1px solid #e4e9f2;
            border-radius:15px;
        }

        .scholarship-form-section-title{
            display:flex;
            align-items:center;
            gap:8px;
            margin:0 0 14px;
            color:#243b7d;
            font-size:15px;
            font-weight:800;
        }

        .scholarship-period-lock-note{
            display:flex;
            align-items:flex-start;
            gap:8px;
            padding:11px 13px;
            margin-bottom:14px;
            color:#854d0e;
            background:#fffbeb;
            border:1px solid #fde68a;
            border-radius:11px;
            font-size:12.5px;
            line-height:1.55;
        }

        .scholarship-photo-preview-box{
            width:150px;
            height:150px;
            display:flex;
            align-items:center;
            justify-content:center;
            margin-top:10px;
            overflow:hidden;
            color:#98a2b3;
            background:#f8fafc;
            border:1px dashed #cbd5e1;
            border-radius:14px;
            font-size:12px;
            text-align:center;
        }

        .scholarship-photo-preview-box img{
            width:100%;
            height:100%;
            display:block;
            object-fit:cover;
        }
    </style>
@endonce

@if($isEdit)
    <div class="scholarship-period-lock-note">
        <i class="bi bi-lock-fill mt-1"></i>
        <div>
            คำขอนี้เป็นปีการศึกษา {{ $formChild->academic_year }}
            ภาคเรียนที่ {{ $formChild->semester }}
            ระบบจะไม่เปลี่ยนรอบคำขอเดิม หากต้องการยื่นรอบใหม่ให้ใช้ปุ่ม
            “ยื่นคำขอใหม่” ที่หน้ารายการ
        </div>
    </div>
@endif

<div class="scholarship-form-section">
    <h6 class="scholarship-form-section-title">
        <i class="bi bi-person-vcard"></i>
        ข้อมูลประจำตัวที่ใช้ร่วมกันทุกคำขอ
    </h6>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">
                ชื่อ <span class="text-danger">*</span>
            </label>
            <input type="text"
                   name="first_name"
                   class="form-control @error('first_name') is-invalid @enderror"
                   value="{{ old('first_name', $formChild->first_name ?? '') }}"
                   maxlength="255"
                   required>
            @error('first_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">
                นามสกุล <span class="text-danger">*</span>
            </label>
            <input type="text"
                   name="last_name"
                   class="form-control @error('last_name') is-invalid @enderror"
                   value="{{ old('last_name', $formChild->last_name ?? '') }}"
                   maxlength="255"
                   required>
            @error('last_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">เพศ</label>
            <select name="gender"
                    class="form-select @error('gender') is-invalid @enderror">
                <option value="">เลือกเพศ</option>
                <option value="male" {{ old('gender', $formChild->gender ?? '') === 'male' ? 'selected' : '' }}>
                    ชาย
                </option>
                <option value="female" {{ old('gender', $formChild->gender ?? '') === 'female' ? 'selected' : '' }}>
                    หญิง
                </option>
            </select>
            @error('gender')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">ชื่อผู้ปกครอง</label>
            <input type="text"
                   name="guardian_name"
                   class="form-control @error('guardian_name') is-invalid @enderror"
                   value="{{ old('guardian_name', $formChild->guardian_name ?? '') }}"
                   maxlength="255">
            @error('guardian_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">เบอร์โทรศัพท์</label>
            <input type="text"
                   name="phone"
                   class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone', $formChild->phone ?? '') }}"
                   maxlength="30"
                   placeholder="เช่น 0812345678">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-12">
            <label class="form-label">ที่อยู่ปัจจุบัน</label>
            <textarea name="current_address"
                      class="form-control @error('current_address') is-invalid @enderror"
                      rows="3">{{ old('current_address', $formChild->current_address ?? '') }}</textarea>
            @error('current_address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="scholarship-form-section">
    <h6 class="scholarship-form-section-title">
        <i class="bi bi-calendar2-check"></i>
        ข้อมูลคำขอประจำปีการศึกษาและภาคเรียน
    </h6>

    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">อายุในรอบคำขอนี้</label>
            <input type="number"
                   name="age"
                   class="form-control @error('age') is-invalid @enderror"
                   value="{{ old('age', $formChild->age ?? '') }}"
                   min="1"
                   max="120">
            @error('age')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

       <div class="col-md-5">
    <label class="form-label">ระดับการศึกษา</label>

    @php
        $educationLevels = [
            'เตรียมอนุบาล',
            'อนุบาล 1',
            'อนุบาล 2',
            'อนุบาล 3',
            'ประถมศึกษาปีที่ 1',
            'ประถมศึกษาปีที่ 2',
            'ประถมศึกษาปีที่ 3',
            'ประถมศึกษาปีที่ 4',
            'ประถมศึกษาปีที่ 5',
            'ประถมศึกษาปีที่ 6',
            'มัธยมศึกษาปีที่ 1',
            'มัธยมศึกษาปีที่ 2',
            'มัธยมศึกษาปีที่ 3',
            'มัธยมศึกษาปีที่ 4',
            'มัธยมศึกษาปีที่ 5',
            'มัธยมศึกษาปีที่ 6',
            'ประกาศนียบัตรวิชาชีพ 1 (ปวช.)',
            'ประกาศนียบัตรวิชาชีพ 2 (ปวช.)',
            'ประกาศนียบัตรวิชาชีพ 3 (ปวช.)',
            'ประกาศนียบัตรวิชาชีพชั้นสูง 1 (ปวส.)',
            'ประกาศนียบัตรวิชาชีพชั้นสูง 2 (ปวส.)',
            'อนุปริญญา',
            'ปริญญาตรีชั้นปีที่ 1',
            'ปริญญาตรีชั้นปีที่ 2',
            'ปริญญาตรีชั้นปีที่ 3',
            'ปริญญาตรีชั้นปีที่ 4',
            'สูงกว่าปริญญาตรี',
            'การศึกษานอกระบบระดับประถมศึกษา',
            'การศึกษานอกระบบระดับมัธยมศึกษาตอนต้น',
            'การศึกษานอกระบบระดับมัธยมศึกษาตอนปลาย',
            'อื่น ๆ',
        ];

        $selectedEducationLevel = old(
            'education_level',
            $formChild->education_level ?? ''
        );
    @endphp

    <select name="education_level"
            class="form-select @error('education_level') is-invalid @enderror">

        <option value="">เลือกระดับการศึกษา</option>

        {{-- รักษาค่าเดิมไว้ กรณีข้อมูลเก่าไม่อยู่ในรายการตัวเลือก --}}
        @if(
            $selectedEducationLevel &&
            !in_array($selectedEducationLevel, $educationLevels, true)
        )
            <option value="{{ $selectedEducationLevel }}" selected>
                {{ $selectedEducationLevel }}
            </option>
        @endif

        @foreach($educationLevels as $level)
            <option value="{{ $level }}"
                @selected($selectedEducationLevel === $level)>
                {{ $level }}
            </option>
        @endforeach
    </select>

    @error('education_level')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

        <div class="col-md-4">
            <label class="form-label">สถานศึกษา</label>
            <input type="text"
                   name="school_name"
                   class="form-control @error('school_name') is-invalid @enderror"
                   value="{{ old('school_name', $formChild->school_name ?? '') }}"
                   maxlength="255">
            @error('school_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">
                ปีการศึกษา <span class="text-danger">*</span>
            </label>

            @if($isEdit)
                <input type="text"
                       class="form-control"
                       value="{{ $formChild->academic_year }}"
                       readonly>
                <input type="hidden"
                       name="academic_year"
                       value="{{ $formChild->academic_year }}">
            @else
                <input type="text"
                       name="academic_year"
                       id="{{ $instanceKey }}"
                       class="form-control @error('academic_year') is-invalid @enderror"
                       value="{{ old('academic_year', now()->year + 543) }}"
                       inputmode="numeric"
                       pattern="[0-9]{4}"
                       maxlength="4"
                       placeholder="เช่น 2569"
                       required>
                @error('academic_year')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @endif
        </div>

        <div class="col-md-6">
            <label class="form-label">
                ภาคเรียนที่ยื่นขอทุน <span class="text-danger">*</span>
            </label>

            @if($isEdit)
                <input type="text"
                       class="form-control"
                       value="ภาคเรียนที่ {{ $formChild->semester }}"
                       readonly>
                <input type="hidden"
                       name="semester"
                       value="{{ $formChild->semester }}">
            @else
                <select name="semester"
                        class="form-select @error('semester') is-invalid @enderror"
                        required>
                    <option value="">เลือกภาคเรียน</option>
                    <option value="1" {{ (string) old('semester') === '1' ? 'selected' : '' }}>
                        ภาคเรียนที่ 1
                    </option>
                    <option value="2" {{ (string) old('semester') === '2' ? 'selected' : '' }}>
                        ภาคเรียนที่ 2
                    </option>
                </select>
                @error('semester')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @endif
        </div>

        <div class="col-md-12">
            <label class="form-label">สาเหตุที่ขอรับทุน</label>
            <textarea name="reason"
                      class="form-control @error('reason') is-invalid @enderror"
                      rows="3">{{ old('reason', $formChild->reason ?? '') }}</textarea>
            @error('reason')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-12">
            <label class="form-label">ความต้องการความช่วยเหลือ</label>
            <textarea name="help_needed"
                      class="form-control @error('help_needed') is-invalid @enderror"
                      rows="3">{{ old('help_needed', $formChild->help_needed ?? '') }}</textarea>
            @error('help_needed')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-12">
            <label class="form-label">รายละเอียดเพิ่มเติม</label>
            <textarea name="more_detail"
                      class="form-control @error('more_detail') is-invalid @enderror"
                      rows="3">{{ old('more_detail', $formChild->more_detail ?? '') }}</textarea>
            @error('more_detail')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="scholarship-form-section mb-0">
    <h6 class="scholarship-form-section-title">
        <i class="bi bi-image"></i>
        ภาพถ่ายประจำตัว
    </h6>

    <div class="row g-3 align-items-start">
        <div class="col-md-7">
            <input type="file"
                   name="photo"
                   id="{{ $photoInputId }}"
                   class="form-control @error('photo') is-invalid @enderror"
                   accept="image/jpeg,image/png,image/webp">
            @error('photo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            <small class="text-muted d-block mt-2">
                รองรับ jpg, jpeg, png และ webp ขนาดไม่เกิน 10 MB
                เมื่อแก้ไขภาพ ระบบจะใช้ภาพใหม่กับคำขอทุกปี/ภาคเรียนของบุคคลนี้
            </small>
        </div>

        <div class="col-md-5">
            <div class="scholarship-photo-preview-box">
                <img id="{{ $photoPreviewId }}"
                     src="{{ $photoUrl ?? '' }}"
                     alt="ตัวอย่างภาพถ่าย"
                     style="{{ $photoUrl ? '' : 'display:none;' }}">

                <div id="{{ $photoEmptyId }}"
                     style="{{ $photoUrl ? 'display:none;' : '' }}">
                    ยังไม่ได้เลือกภาพ
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById(@json($photoInputId));
        const preview = document.getElementById(@json($photoPreviewId));
        const empty = document.getElementById(@json($photoEmptyId));

        if (!input || !preview || !empty) {
            return;
        }

        input.addEventListener('change', function () {
            const file = input.files && input.files[0] ? input.files[0] : null;

            if (!file) {
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                preview.src = event.target.result;
                preview.style.display = 'block';
                empty.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    });
</script>