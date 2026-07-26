@php
    $formChild = $child ?? null;
    $currentYear = date('Y') + 543;

    $selectedEducation = old('education_level', $formChild->education_level ?? '');
    $selectedYear = old('academic_year', $formChild->academic_year ?? $currentYear);

    $uniqueId = $formChild?->id ?? uniqid();
    $yearListId = $yearListId ?? 'academic_year_list_' . $uniqueId;

    $photoInputId = 'scholarshipPhotoInput_' . $uniqueId;
    $photoPreviewId = 'scholarshipPhotoPreview_' . $uniqueId;
    $photoEmptyId = 'scholarshipPhotoEmpty_' . $uniqueId;
    $photoLabelId = 'scholarshipPhotoPreviewLabel_' . $uniqueId;
@endphp

<style>
    .scholarship-child-form .form-label {
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .scholarship-child-form .form-control,
    .scholarship-child-form .form-select {
        min-height: 44px;
        border-radius: 13px;
        border-color: #d1d5db;
        box-shadow: none !important;
    }

    .gender-pill-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
    }

    .gender-pill {
        min-height: 44px;
        border-radius: 13px;
        border: 1px solid #d1d5db;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        white-space: nowrap;
        transition: all .2s ease;
    }

    .gender-pill:hover {
        background: #f8fbff;
        border-color: #60a5fa;
    }

    .gender-pill input {
        width: 15px;
        height: 15px;
        margin: 0;
        accent-color: #2563eb;
        flex-shrink: 0;
    }

    .scholarship-photo-preview-wrap {
        margin-top: 14px;
    }

    .scholarship-photo-preview-box {
        width: 150px;
        height: 150px;
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }

    .scholarship-photo-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .scholarship-photo-preview-empty {
        font-size: 13px;
        color: #94a3b8;
        text-align: center;
        padding: 10px;
    }

    @media (max-width: 991.98px) {
        .gender-pill-group {
            max-width: 260px;
        }
    }

    @media (max-width: 575.98px) {
        .gender-pill-group {
            max-width: none;
        }

        .scholarship-photo-preview-box {
            width: 130px;
            height: 130px;
        }
    }
</style>

<div class="row g-3 scholarship-child-form">

    {{-- แถวที่ 1: ชื่อ / นามสกุล / เพศ --}}
    <div class="col-12 col-lg-5">
        <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
        <input type="text"
               name="first_name"
               class="form-control @error('first_name') is-invalid @enderror"
               value="{{ old('first_name', $formChild->first_name ?? '') }}"
               required>
        @error('first_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-lg-5">
        <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
        <input type="text"
               name="last_name"
               class="form-control @error('last_name') is-invalid @enderror"
               value="{{ old('last_name', $formChild->last_name ?? '') }}"
               required>
        @error('last_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-lg-2">
        <label class="form-label">เพศ</label>

        @php
            $selectedGender = old('gender', $formChild->gender ?? '');
        @endphp

        <div class="gender-pill-group">
            <label class="gender-pill">
                <input type="checkbox"
                       name="gender"
                       value="male"
                       class="gender-checkbox"
                       {{ $selectedGender === 'male' ? 'checked' : '' }}>
                <span>ชาย</span>
            </label>

            <label class="gender-pill">
                <input type="checkbox"
                       name="gender"
                       value="female"
                       class="gender-checkbox"
                       {{ $selectedGender === 'female' ? 'checked' : '' }}>
                <span>หญิง</span>
            </label>
        </div>
    </div>

    {{-- แถวที่ 2: อายุ / ระดับ / ปีการศึกษา / สถานศึกษา --}}
    <div class="col-12 col-md-3">
        <label class="form-label">อายุ</label>
        <input type="number"
               name="age"
               class="form-control @error('age') is-invalid @enderror"
               min="1"
               max="120"
               value="{{ old('age', $formChild->age ?? '') }}">
        @error('age')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-3">
        <label class="form-label">ระดับการศึกษา</label>
        <select name="education_level"
                class="form-select @error('education_level') is-invalid @enderror">
            <option value="">-- เลือกระดับการศึกษา --</option>

            <option value="อนุบาล 1" {{ $selectedEducation == 'อนุบาล 1' ? 'selected' : '' }}>อนุบาล 1</option>
            <option value="อนุบาล 2" {{ $selectedEducation == 'อนุบาล 2' ? 'selected' : '' }}>อนุบาล 2</option>
            <option value="อนุบาล 3" {{ $selectedEducation == 'อนุบาล 3' ? 'selected' : '' }}>อนุบาล 3</option>

            <option value="ประถมศึกษาปีที่ 1" {{ $selectedEducation == 'ประถมศึกษาปีที่ 1' ? 'selected' : '' }}>ประถมศึกษาปีที่ 1</option>
            <option value="ประถมศึกษาปีที่ 2" {{ $selectedEducation == 'ประถมศึกษาปีที่ 2' ? 'selected' : '' }}>ประถมศึกษาปีที่ 2</option>
            <option value="ประถมศึกษาปีที่ 3" {{ $selectedEducation == 'ประถมศึกษาปีที่ 3' ? 'selected' : '' }}>ประถมศึกษาปีที่ 3</option>
            <option value="ประถมศึกษาปีที่ 4" {{ $selectedEducation == 'ประถมศึกษาปีที่ 4' ? 'selected' : '' }}>ประถมศึกษาปีที่ 4</option>
            <option value="ประถมศึกษาปีที่ 5" {{ $selectedEducation == 'ประถมศึกษาปีที่ 5' ? 'selected' : '' }}>ประถมศึกษาปีที่ 5</option>
            <option value="ประถมศึกษาปีที่ 6" {{ $selectedEducation == 'ประถมศึกษาปีที่ 6' ? 'selected' : '' }}>ประถมศึกษาปีที่ 6</option>

            <option value="มัธยมศึกษาปีที่ 1" {{ $selectedEducation == 'มัธยมศึกษาปีที่ 1' ? 'selected' : '' }}>มัธยมศึกษาปีที่ 1</option>
            <option value="มัธยมศึกษาปีที่ 2" {{ $selectedEducation == 'มัธยมศึกษาปีที่ 2' ? 'selected' : '' }}>มัธยมศึกษาปีที่ 2</option>
            <option value="มัธยมศึกษาปีที่ 3" {{ $selectedEducation == 'มัธยมศึกษาปีที่ 3' ? 'selected' : '' }}>มัธยมศึกษาปีที่ 3</option>
            <option value="มัธยมศึกษาปีที่ 4" {{ $selectedEducation == 'มัธยมศึกษาปีที่ 4' ? 'selected' : '' }}>มัธยมศึกษาปีที่ 4</option>
            <option value="มัธยมศึกษาปีที่ 5" {{ $selectedEducation == 'มัธยมศึกษาปีที่ 5' ? 'selected' : '' }}>มัธยมศึกษาปีที่ 5</option>
            <option value="มัธยมศึกษาปีที่ 6" {{ $selectedEducation == 'มัธยมศึกษาปีที่ 6' ? 'selected' : '' }}>มัธยมศึกษาปีที่ 6</option>

            <option value="ประกาศนียบัตรวิชาชีพ 1" {{ $selectedEducation == 'ประกาศนียบัตรวิชาชีพ 1' ? 'selected' : '' }}>ประกาศนียบัตรวิชาชีพ 1</option>
            <option value="ประกาศนียบัตรวิชาชีพ 2" {{ $selectedEducation == 'ประกาศนียบัตรวิชาชีพ 2' ? 'selected' : '' }}>ประกาศนียบัตรวิชาชีพ 2</option>
            <option value="ประกาศนียบัตรวิชาชีพ 3" {{ $selectedEducation == 'ประกาศนียบัตรวิชาชีพ 3' ? 'selected' : '' }}>ประกาศนียบัตรวิชาชีพ 3</option>

            <option value="ประกาศนียบัตรวิชาชีพ (ปวส) 1" {{ $selectedEducation == 'ประกาศนียบัตรวิชาชีพ (ปวส) 1' ? 'selected' : '' }}>ประกาศนียบัตรวิชาชีพ (ปวส) 1</option>
            <option value="ประกาศนียบัตรวิชาชีพ (ปวส) 2" {{ $selectedEducation == 'ประกาศนียบัตรวิชาชีพ (ปวส) 2' ? 'selected' : '' }}>ประกาศนียบัตรวิชาชีพ (ปวส) 2</option>

            <option value="ประกาศนียบัตรวิชาชีพเทคนิค (ปวท.) 1" {{ $selectedEducation == 'ประกาศนียบัตรวิชาชีพเทคนิค (ปวท.) 1' ? 'selected' : '' }}>ประกาศนียบัตรวิชาชีพเทคนิค (ปวท.) 1</option>
            <option value="ประกาศนียบัตรวิชาชีพเทคนิค (ปวท.) 2" {{ $selectedEducation == 'ประกาศนียบัตรวิชาชีพเทคนิค (ปวท.) 2' ? 'selected' : '' }}>ประกาศนียบัตรวิชาชีพเทคนิค (ปวท.) 2</option>

            <option value="ปริญญาตรีชั้นปีที่ 1" {{ $selectedEducation == 'ปริญญาตรีชั้นปีที่ 1' ? 'selected' : '' }}>ปริญญาตรีชั้นปีที่ 1</option>
            <option value="ปริญญาตรีชั้นปีที่ 2" {{ $selectedEducation == 'ปริญญาตรีชั้นปีที่ 2' ? 'selected' : '' }}>ปริญญาตรีชั้นปีที่ 2</option>
            <option value="ปริญญาตรีชั้นปีที่ 3" {{ $selectedEducation == 'ปริญญาตรีชั้นปีที่ 3' ? 'selected' : '' }}>ปริญญาตรีชั้นปีที่ 3</option>
            <option value="ปริญญาตรีชั้นปีที่ 4" {{ $selectedEducation == 'ปริญญาตรีชั้นปีที่ 4' ? 'selected' : '' }}>ปริญญาตรีชั้นปีที่ 4</option>
        </select>
        @error('education_level')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-3">
        <label class="form-label">ปีการศึกษาที่ขอรับทุน <span class="text-danger">*</span></label>
        <input type="text"
               name="academic_year"
               list="{{ $yearListId }}"
               class="form-control @error('academic_year') is-invalid @enderror"
               value="{{ $selectedYear }}"
               placeholder="เช่น 2568"
               inputmode="numeric"
               maxlength="4"
               pattern="[0-9]{4}"
               required>

        <datalist id="{{ $yearListId }}">
            @for($year = $currentYear + 5; $year >= $currentYear - 10; $year--)
                <option value="{{ $year }}">
            @endfor
        </datalist>

        @error('academic_year')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted d-block mt-1">
            กรุณากรอกปี พ.ศ. 4 หลัก เช่น 2568
        </small>
    </div>

    <div class="col-12 col-md-3">
        <label class="form-label">ชื่อสถานศึกษา</label>
        <input type="text"
               name="school_name"
               class="form-control @error('school_name') is-invalid @enderror"
               value="{{ old('school_name', $formChild->school_name ?? '') }}">
        @error('school_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- ที่อยู่ --}}
    <div class="col-12">
        <label class="form-label">ที่อยู่ปัจจุบัน</label>
        <textarea name="current_address"
                  class="form-control @error('current_address') is-invalid @enderror"
                  rows="2">{{ old('current_address', $formChild->current_address ?? '') }}</textarea>
        @error('current_address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- ผู้ปกครอง / เบอร์ --}}
    <div class="col-12 col-md-6">
        <label class="form-label">ชื่อผู้ปกครอง</label>
        <input type="text"
               name="guardian_name"
               class="form-control @error('guardian_name') is-invalid @enderror"
               value="{{ old('guardian_name', $formChild->guardian_name ?? '') }}">
        @error('guardian_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">เบอร์โทรศัพท์</label>
        <input type="text"
               name="phone"
               class="form-control @error('phone') is-invalid @enderror"
               value="{{ old('phone', $formChild->phone ?? '') }}"
               placeholder="เช่น 0812345678">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- รายละเอียด --}}
    <div class="col-12">
        <label class="form-label">สาเหตุที่ขอรับทุน</label>
        <textarea name="reason"
                  class="form-control @error('reason') is-invalid @enderror"
                  rows="3">{{ old('reason', $formChild->reason ?? '') }}</textarea>
        @error('reason')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">ความต้องการความช่วยเหลือ</label>
        <textarea name="help_needed"
                  class="form-control @error('help_needed') is-invalid @enderror"
                  rows="3">{{ old('help_needed', $formChild->help_needed ?? '') }}</textarea>
        @error('help_needed')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">รายละเอียดเพิ่มเติม</label>
        <textarea name="more_detail"
                  class="form-control @error('more_detail') is-invalid @enderror"
                  rows="3">{{ old('more_detail', $formChild->more_detail ?? '') }}</textarea>
        @error('more_detail')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- ภาพถ่าย --}}
    <div class="col-12 col-md-6">
        <label class="form-label">ภาพถ่าย</label>

        <input type="file"
               name="photo"
               id="{{ $photoInputId }}"
               class="form-control @error('photo') is-invalid @enderror scholarship-photo-input"
               accept="image/*">

        @error('photo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <small class="text-muted d-block mt-1">
            รองรับไฟล์ jpg, jpeg, png, webp ขนาดไม่เกิน 10MB
        </small>

        <div class="scholarship-photo-preview-wrap">
            <div class="text-muted small mb-1" id="{{ $photoLabelId }}">
                {{ !empty($formChild?->photo) ? 'ภาพถ่ายปัจจุบัน' : 'ตัวอย่างภาพที่เลือก' }}
            </div>

            <div class="scholarship-photo-preview-box">
                @php
                    $currentPhotoUrl = '';

                    if (!empty($formChild?->photo)) {
                        $currentPhotoUrl = str_starts_with($formChild->photo, 'upload/')
                            ? asset($formChild->photo)
                            : asset('storage/' . $formChild->photo);
                    }
                @endphp

                <img id="{{ $photoPreviewId }}"
                     src="{{ $currentPhotoUrl }}"
                     alt="ตัวอย่างภาพถ่าย"
                     loading="lazy"
                     decoding="async"
                     style="{{ !empty($currentPhotoUrl) ? '' : 'display:none;' }}">

                <div id="{{ $photoEmptyId }}"
                     class="scholarship-photo-preview-empty"
                     style="{{ !empty($currentPhotoUrl) ? 'display:none;' : '' }}">
                    ยังไม่ได้เลือกภาพ
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Preview รูปภาพ
    |--------------------------------------------------------------------------
    */
    const input = document.getElementById(@json($photoInputId));
    const preview = document.getElementById(@json($photoPreviewId));
    const empty = document.getElementById(@json($photoEmptyId));
    const label = document.getElementById(@json($photoLabelId));

    if (input && preview && empty && label) {
        input.addEventListener('change', function () {
            const file = this.files && this.files[0];

            if (!file) {
                return;
            }

            if (!file.type.startsWith('image/')) {
                preview.style.display = 'none';
                empty.style.display = 'block';
                empty.textContent = 'กรุณาเลือกไฟล์รูปภาพเท่านั้น';
                return;
            }

            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                empty.style.display = 'none';
                label.textContent = 'ตัวอย่างภาพที่เลือกใหม่';
            };

            reader.readAsDataURL(file);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | เพศ: checkbox เลือกได้เพียง 1 รายการ
    |--------------------------------------------------------------------------
    */
    document.querySelectorAll('.gender-checkbox').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            if (this.checked) {
                document.querySelectorAll('.gender-checkbox').forEach(function (other) {
                    if (other !== checkbox) {
                        other.checked = false;
                    }
                });
            }
        });
    });

});
</script>