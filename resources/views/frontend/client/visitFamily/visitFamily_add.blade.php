@extends('admin_client.admin_client')
@section('content')

<style>
    /* ใช้ตัวอักษรเดียวกับ Sidebar / Header จาก Layout หลัก */
    .visit-family-page {
        font-family: inherit;
        font-size: inherit;
        color: inherit;
    }

    .visit-family-page button,
    .visit-family-page input,
    .visit-family-page select,
    .visit-family-page textarea,
    .visit-family-page .form-control,
    .visit-family-page .form-select,
    .visit-family-page .btn,
    .visit-family-page .card,
    .visit-family-page .alert {
        font-family: inherit !important;
    }

    .visit-family-page .card-title {
        font-family: inherit;
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.35;
    }

    .visit-family-page .form-label {
        font-family: inherit;
        font-size: .95rem;
        font-weight: 600;
        line-height: 1.45;
    }

    .visit-family-page .form-control,
    .visit-family-page .form-select {
        font-size: .95rem;
        font-weight: 400;
        line-height: 1.5;
    }

    .official-form {
        border: 2px solid #0d6efd;
        padding: 20px;
        background-color: #fdfdfd;
        border-radius: 8px;
    }

    .official-checkbox {
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        padding: 10px 14px;
        border-radius: 6px;
        transition: all 0.3s ease-in-out;
    }

    .official-checkbox:hover {
        background-color: #e9f2ff;
        border-color: #0d6efd;
    }

    .styled-checkbox {
        width: 20px;
        height: 20px;
        border: 2px solid #0d6efd;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    .styled-checkbox:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
        box-shadow: 0 0 4px rgba(13, 110, 253, 0.6);
    }

    .form-check-label {
        margin-left: 0.6em;
        font-size: 1rem;
    }

    /* ✅ เฉพาะรูปเยี่ยมบ้าน */
    .visit-family-gallery .visit-family-image-card,
    .visit-family-preview .visit-family-image-card {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 3;
        background: #f8fafc;
        border: 1px solid #dbe3ec;
        border-radius: 10px;
        overflow: hidden;
    }

    .visit-family-gallery .visit-family-image-card img,
    .visit-family-preview .visit-family-image-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center center;
        display: block;
    }

    .visit-family-delete-btn {
        position: absolute;
        right: 8px;
        bottom: 8px;
        z-index: 5;
        font-size: 13px;
        font-weight: 700;
        padding: 5px 10px;
        border-radius: 6px;
    }

    @media (max-width: 575.98px) {
        .visit-family-gallery .visit-family-image-card,
        .visit-family-preview .visit-family-image-card {
            aspect-ratio: 1 / 1;
        }
    }

    .visit-family-form-card {
        border: 1px solid #dbe3ec;
        border-radius: 18px;
        overflow: hidden;
        background: #fff;
    }

    .visit-family-action-btn {
        min-width: 132px;
        min-height: 44px;
        padding: 9px 17px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 12px;
        font-size: .95rem;
        font-weight: 600;
        line-height: 1.2;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease,
                    background-color .18s ease, color .18s ease;
    }

    .visit-family-action-btn:hover {
        transform: translateY(-1px);
    }

    .visit-family-action-btn:active {
        transform: translateY(0);
    }

    .visit-family-action-btn:focus-visible {
        outline: 0;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .16);
    }

    .visit-family-save-btn {
        border: 1px solid #1d4ed8;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #fff;
        box-shadow: 0 7px 16px rgba(37, 99, 235, .18);
    }

    .visit-family-save-btn:hover,
    .visit-family-save-btn:focus {
        border-color: #1e40af;
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        color: #fff;
        box-shadow: 0 9px 20px rgba(37, 99, 235, .24);
    }

    .visit-family-report-btn {
        border: 1px solid #0f766e;
        background: #fff;
        color: #0f766e;
    }

    .visit-family-report-btn:hover,
    .visit-family-report-btn:focus {
        background: #f0fdfa;
        border-color: #115e59;
        color: #115e59;
        box-shadow: 0 7px 16px rgba(15, 118, 110, .12);
    }

    .visit-family-action-btn:disabled {
        opacity: 1;
        transform: none;
        cursor: not-allowed;
    }

    .visit-family-upload-status {
        min-height: 20px;
        margin-top: 6px;
        color: #64748b;
    }

    @media (max-width: 575.98px) {
        .visit-family-actions {
            display: grid !important;
            grid-template-columns: 1fr;
            width: 100%;
        }

        .visit-family-action-btn {
            width: 100%;
        }
    }

</style>

<div class="container-fluid py-4 visit-family-page">
    <form method="POST"
          id="visit-family-form"
          action="{{ isset($visitFamily) ? route('vitsitFamily.update', $visitFamily->id) : route('vitsitFamily.store', $client_id) }}"
          enctype="multipart/form-data">
        @csrf

        @if(isset($visitFamily))
            @method('PUT')
        @endif

        @if (session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            {{-- Card ฝั่งซ้าย --}}
            <div class="col-lg-6 col-xl-6 mb-4">
                <div class="card border shadow-sm">
                    <div class="card-header">
                        <h4 class="card-title mb-0">ข้อมูลการเยี่ยมบ้าน</h4>
                    </div>

                    <div class="card-body">
                        <div class="row pt-4">
                           <div class="form-group col-md-3 mb-3">
                                <label for="visit_date" class="form-label">
                                    วันที่เยี่ยมบ้าน: <span class="text-danger">*</span>
                                </label>

                                <input type="date"
                                    name="visit_date"
                                    id="visit_date"
                                    class="form-control @error('visit_date') is-invalid @enderror"
                                    value="{{ old('visit_date', $visitFamily->visit_date ?? '') }}"
                                    max="{{ now('Asia/Bangkok')->toDateString() }}">

                                @error('visit_date')
                                    <small class="text-danger" id="visit_date-error">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 mb-3">
                                <label for="family_fname" class="form-label">
                                    ชื่อ-สกุล ผู้ให้ข้อมูล: <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="family_fname"
                                       id="family_fname"
                                       class="form-control @error('family_fname') is-invalid @enderror"
                                       value="{{ old('family_fname', $visitFamily->family_fname ?? '') }}">
                                @error('family_fname')
                                    <div class="invalid-feedback" id="family_fname-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-3 mb-3">
                                <label for="family_age" class="form-label">อายุ:</label>
                                <div class="d-flex align-items-center">
                                    <input type="number"
                                           name="family_age"
                                           id="family_age"
                                           class="form-control @error('family_age') is-invalid @enderror"
                                           value="{{ old('family_age', $visitFamily->family_age ?? '') }}"
                                           min="0"
                                           max="120"
                                           inputmode="numeric">
                                    <span class="ms-2">ปี</span>
                                </div>
                                @error('family_age')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 mb-3">
                                <label for="member" class="form-label">ความสัมพันธ์กับผู้รับ</label>
                                <input type="text"
                                       name="member"
                                       id="member"
                                       class="form-control"
                                       value="{{ old('member', $visitFamily->member ?? '') }}">
                                @error('member')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label" for="income_id">รายได้เฉลี่ยครอบครัว</label>
                                <select name="income_id"
                                        id="income_id"
                                        class="form-control form-select @error('income_id') is-invalid @enderror">
                                    <option value="">--รายได้เฉลี่ย--</option>
                                    @foreach ($incomes as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('income_id', $visitFamily->income_id ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->income_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('income_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-12 mb-3">
                                <label for="residence_status" class="form-label">สถานะการอยู่อาศัย:</label>
                                <input type="text"
                                       id="residence_status"
                                       name="residence_status"
                                       class="form-control"
                                       value="{{ old('residence_status', $visitFamily->residence_status ?? '') }}"
                                       placeholder="เช่น บ้านของตนเอง, บ้านเช่า, อยู่กับครอบครัว, บ้านพักคนงาน">
                            </div>

                            <div class="form-group col-md-12 mb-3">
                                <label for="outside_address" class="form-label">สภาพที่อยู่ภายนอก</label>
                                <textarea name="outside_address"
                                          id="outside_address"
                                          class="form-control bg-white border rounded shadow-sm @error('outside_address') is-invalid @enderror"
                                          rows="3">{{ old('outside_address', $visitFamily->outside_address ?? '') }}</textarea>
                                @error('outside_address')
                                    <div class="invalid-feedback d-block" id="outside_address-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-12 mb-3">
                                <label for="inside_address" class="form-label">สภาพที่อยู่ภายใน</label>
                                <textarea name="inside_address"
                                          id="inside_address"
                                          class="form-control bg-white border rounded shadow-sm @error('inside_address') is-invalid @enderror"
                                          rows="3">{{ old('inside_address', $visitFamily->inside_address ?? '') }}</textarea>
                                @error('inside_address')
                                    <div class="invalid-feedback d-block" id="inside_address-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-12 mb-3">
                                <label for="environment" class="form-label">สภาพแวดล้อม</label>
                                <textarea name="environment"
                                          id="environment"
                                          class="form-control bg-white border rounded shadow-sm @error('environment') is-invalid @enderror"
                                          rows="3">{{ old('environment', $visitFamily->environment ?? '') }}</textarea>
                                @error('environment')
                                    <div class="invalid-feedback d-block" id="environment-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-12 mb-3">
                                <label for="neighbor" class="form-label">ความสัมพันธ์กับเพื่อนบ้าน</label>
                                <textarea name="neighbor"
                                          id="neighbor"
                                          class="form-control bg-white border rounded shadow-sm @error('neighbor') is-invalid @enderror"
                                          rows="3">{{ old('neighbor', $visitFamily->neighbor ?? '') }}</textarea>
                                @error('neighbor')
                                    <div class="invalid-feedback d-block" id="neighbor-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-12 mb-3">
                                <label for="member_relation" class="form-label">ความสัมพันธ์ของสมาชิกในบ้าน</label>
                                <textarea name="member_relation"
                                          id="member_relation"
                                          class="form-control bg-white border rounded shadow-sm @error('member_relation') is-invalid @enderror"
                                          rows="3">{{ old('member_relation', $visitFamily->member_relation ?? '') }}</textarea>
                                @error('member_relation')
                                    <div class="invalid-feedback d-block" id="member_relation-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-12 mb-3 mt-3">
                                <label for="problem" class="form-label">ปัญหาที่พบ</label>
                                <textarea name="problem"
                                          id="problem"
                                          class="form-control bg-white border rounded shadow-sm @error('problem') is-invalid @enderror"
                                          rows="3">{{ old('problem', $visitFamily->problem ?? '') }}</textarea>
                                @error('problem')
                                    <div class="invalid-feedback d-block" id="problem-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-12 mb-3">
                                <label for="need" class="form-label">ความต้องการ</label>
                                <textarea name="need"
                                          id="need"
                                          class="form-control bg-white border rounded shadow-sm @error('need') is-invalid @enderror"
                                          rows="3">{{ old('need', $visitFamily->need ?? '') }}</textarea>
                                @error('need')
                                    <div class="invalid-feedback d-block" id="need-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card ฝั่งขวา --}}
            <div class="col-lg-6 col-xl-6 mb-4">
                <div class="card border shadow-sm">
                    <div class="card-header">
                        <h4 class="card-title mb-0">การประเมินสภาวะเด็กและครอบครัว</h4>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">ที่อยู่เลขที่</label>
                                <input type="text"
                                       name="address"
                                       id="address"
                                       class="form-control"
                                       value="{{ old('address', $visitFamily->address ?? '') }}">
                                @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="moo" class="form-label">หมู่ที่</label>
                                <input type="text"
                                       name="moo"
                                       id="moo"
                                       class="form-control"
                                       value="{{ old('moo', $visitFamily->moo ?? '') }}">
                                @error('moo')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="soi" class="form-label">ตรอก/ซอย</label>
                                <input type="text"
                                       name="soi"
                                       id="soi"
                                       class="form-control"
                                       value="{{ old('soi', $visitFamily->soi ?? '') }}">
                                @error('soi')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="road" class="form-label">ถนน</label>
                                <input type="text"
                                       name="road"
                                       id="road"
                                       class="form-control"
                                       value="{{ old('road', $visitFamily->road ?? '') }}">
                                @error('road')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="village" class="form-label">หมู่บ้าน</label>
                            <input type="text"
                                   name="village"
                                   id="village"
                                   class="form-control"
                                   value="{{ old('village', $visitFamily->village ?? '') }}">
                            @error('village')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="province" class="form-label">
                                    จังหวัด : <span class="text-danger">*</span>
                                </label>
                                <select name="province_id"
                                        id="province"
                                        class="form-select @error('province_id') is-invalid @enderror"
                                        data-selected="{{ old('province_id', $visitFamily->province_id ?? '') }}">
                                    <option value="">--เลือกจังหวัด--</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->id }}"
                                            {{ old('province_id', $visitFamily->province_id ?? '') == $province->id ? 'selected' : '' }}>
                                            {{ $province->prov_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('province_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="district" class="form-label">
                                    เขต/อำเภอ : <span class="text-danger">*</span>
                                </label>
                                <select name="district_id"
                                        id="district"
                                        class="form-select @error('district_id') is-invalid @enderror"
                                        data-selected="{{ old('district_id', $visitFamily->district_id ?? '') }}">
                                    <option value="">--เลือกอำเภอ--</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}"
                                            {{ old('district_id', $visitFamily->district_id ?? '') == $district->id ? 'selected' : '' }}>
                                            {{ $district->dist_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('district_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subdistrict" class="form-label">
                                แขวง/ตำบล : <span class="text-danger">*</span>
                            </label>
                            <select name="sub_district_id"
                                    id="subdistrict"
                                    class="form-select @error('sub_district_id') is-invalid @enderror"
                                    data-selected="{{ old('sub_district_id', $visitFamily->sub_district_id ?? '') }}">
                                <option value="">-- เลือกตำบล --</option>
                                @foreach ($sub_districts as $subdistrict)
                                    <option value="{{ $subdistrict->id }}"
                                        {{ old('sub_district_id', $visitFamily->sub_district_id ?? '') == $subdistrict->id ? 'selected' : '' }}>
                                        {{ $subdistrict->subd_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sub_district_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="form-group col-md-3 mb-3">
                                <label for="zipcode" class="form-label">
                                    รหัสไปรษณีย์ <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="zipcode"
                                       id="zipcode"
                                       class="form-control @error('zipcode') is-invalid @enderror"
                                       value="{{ old('zipcode', $visitFamily->zipcode ?? '') }}"
                                       maxlength="10"
                                       readonly>
                                @error('zipcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-8">
                                <label for="phone" class="form-label">โทรศัพท์</label>
                                <input type="text"
                                       name="phone"
                                       id="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $visitFamily->phone ?? '') }}"
                                       maxlength="20"
                                       inputmode="tel">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-md-12 mb-3">
                            <label for="diagnose" class="form-label">การวินิจฉัยปัญหา</label>
                            <textarea name="diagnose"
                                      id="diagnose"
                                      class="form-control bg-white border rounded shadow-sm @error('diagnose') is-invalid @enderror"
                                      rows="3">{{ old('diagnose', $visitFamily->diagnose ?? '') }}</textarea>
                            @error('diagnose')
                                <div class="invalid-feedback d-block" id="diagnose-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-12 mb-3">
                            <label for="assistance" class="form-label">การให้ความช่วยเหลือ</label>
                            <textarea name="assistance"
                                      id="assistance"
                                      class="form-control bg-white border rounded shadow-sm @error('assistance') is-invalid @enderror"
                                      rows="3">{{ old('assistance', $visitFamily->assistance ?? '') }}</textarea>
                            @error('assistance')
                                <div class="invalid-feedback d-block" id="assistance-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-12 mb-3">
                            <label for="comment" class="form-label">ข้อคิดเห็น</label>
                            <textarea name="comment"
                                      id="comment"
                                      class="form-control bg-white border rounded shadow-sm @error('comment') is-invalid @enderror"
                                      rows="3">{{ old('comment', $visitFamily->comment ?? '') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback d-block" id="comment-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-12 mb-3">
                            <label for="modify" class="form-label">สิ่งที่ควรแก้ไข</label>
                            <textarea name="modify"
                                      id="modify"
                                      class="form-control bg-white border rounded shadow-sm @error('modify') is-invalid @enderror"
                                      rows="3">{{ old('modify', $visitFamily->modify ?? '') }}</textarea>
                            @error('modify')
                                <div class="invalid-feedback d-block" id="modify-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-12 mb-3">
                            <label for="remark" class="form-label">หมายเหตุ</label>
                            <textarea name="remark"
                                      id="remark"
                                      class="form-control bg-white border rounded shadow-sm @error('remark') is-invalid @enderror"
                                      rows="3">{{ old('remark', $visitFamily->remark ?? '') }}</textarea>
                            @error('remark')
                                <div class="invalid-feedback d-block" id="remark-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            <label for="teacher" class="form-label">
                                ชื่อ-สกุล ผู้ที่ติดตามเยี่ยมบ้าน : <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="teacher"
                                   id="teacher"
                                   class="form-control @error('teacher') is-invalid @enderror"
                                   value="{{ old('teacher', $visitFamily->teacher ?? '') }}">
                            @error('teacher')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                     {{-- =====================================================
     PATCH:
     Upload Visit Family Images
===================================================== --}}
<div class="mb-3">
    <label for="images" class="form-label">เลือกรูปภาพ</label>

    <input type="file"
           name="images[]"
           id="images"
           class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
           multiple
           accept="image/jpeg,image/png,image/webp"
           data-max-files="10">

    @error('images')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    @error('images.*')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    <div class="form-text">อัปโหลดได้ไม่เกิน 10 รูปต่อครั้ง รูปละไม่เกิน 10 MB</div>
    <div id="visit-family-upload-status" class="visit-family-upload-status" aria-live="polite"></div>
    <div id="preview" class="row mt-3 visit-family-preview"></div>
</div>

{{-- =====================================================
     PATCH:
     รูปที่เคยอัปโหลด
     ใช้ relation จาก $visitFamily->images โดยตรง
===================================================== --}}
@php
    $oldImages = isset($visitFamily) && $visitFamily->relationLoaded('images')
        ? $visitFamily->images
        : (isset($images) ? $images : collect());
@endphp

@if($oldImages->count() > 0)
    <div class="mb-3">
        <label class="form-label">รูปเยี่ยมบ้านที่เคยอัปโหลด</label>

        <div class="row visit-family-gallery" id="image-gallery">
            @foreach($oldImages as $img)
                <div class="col-6 col-md-4 col-xl-3 mb-3" id="image-{{ $img->id }}">
                    <div class="visit-family-image-card">
                        @php
                            $imagePath = ltrim((string) $img->file_path, '/');
                            $imageUrl = str_starts_with($imagePath, 'upload/')
                                || str_starts_with($imagePath, 'storage/')
                                    ? asset($imagePath)
                                    : asset('storage/' . $imagePath);
                        @endphp

                        <img src="{{ $imageUrl }}"
                             alt="รูปเยี่ยมบ้าน"
                             loading="lazy"
                             decoding="async">

                        <button type="button"
                                class="btn btn-danger btn-sm visit-family-delete-btn delete-image"
                                data-url="{{ route('image.destroy', $img->id) }}"
                                data-id="{{ $img->id }}">
                            ลบภาพ
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
                {{-- =====================================================
                    PATCH:
                    Browser Image Compression
                    บีบอัดรูปก่อน Upload
                ===================================================== --}}

                        <div class="d-flex flex-wrap gap-2 mt-3 visit-family-actions">
                            <button type="submit"
                                    class="btn visit-family-action-btn visit-family-save-btn"
                                    id="visit-family-submit">
                                <i class="bi bi-check2-circle" aria-hidden="true"></i>
                                <span>{{ isset($visitFamily) ? 'บันทึกการแก้ไข' : 'บันทึกข้อมูล' }}</span>
                            </button>

                            @if(isset($visitFamily) && !empty($visitFamily->id))
                                <a href="{{ route('vitsitFamily.report', $visitFamily->id) }}"
                                   class="btn visit-family-action-btn visit-family-report-btn"
                                   target="_blank"
                                   rel="noopener">
                                    <i class="bi bi-printer" aria-hidden="true"></i>
                                    <span>รายงาน</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


{{-- ใช้ browser-image-compression เมื่อ CDN พร้อมใช้งาน หากโหลดไม่ได้จะส่งไฟล์ต้นฉบับ --}}
<script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const form = document.getElementById('visit-family-form');
    const province = document.getElementById('province');
    const district = document.getElementById('district');
    const subdistrict = document.getElementById('subdistrict');
    const zipcode = document.getElementById('zipcode');
    const imageInput = document.getElementById('images');
    const preview = document.getElementById('preview');
    const uploadStatus = document.getElementById('visit-family-upload-status');
    const submitButton = document.getElementById('visit-family-submit');

    const urls = {
        districts: @json(url('/vitsitFamily/get-districts')),
        subdistricts: @json(url('/vitsitFamily/get-subdistricts')),
        zipcode: @json(url('/vitsitFamily/get-zipcode')),
    };

    let districtRequest = null;
    let subdistrictRequest = null;
    let zipcodeRequest = null;

    function clearOptions(select, placeholder) {
        if (!select) return;
        select.replaceChildren(new Option(placeholder, ''));
    }

    function setSelectLoading(select, placeholder) {
        clearOptions(select, placeholder);
        select.disabled = true;
    }

    function addOptions(select, items, valueKey, labelKey, selectedValue = '') {
        clearOptions(
            select,
            select === district ? '--เลือกอำเภอ--' : '--เลือกตำบล--'
        );

        items.forEach(item => {
            const option = new Option(item[labelKey] ?? '-', item[valueKey]);
            option.selected = String(item[valueKey]) === String(selectedValue || '');
            select.add(option);
        });

        select.disabled = false;
    }

    async function fetchJson(url, controller) {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            signal: controller.signal,
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        return response.json();
    }

    async function loadDistricts(provinceId, selectedDistrict = '') {
        districtRequest?.abort();
        districtRequest = new AbortController();

        setSelectLoading(district, 'กำลังโหลดอำเภอ...');
        clearOptions(subdistrict, '--เลือกตำบล--');
        subdistrict.disabled = true;
        zipcode.value = '';

        if (!provinceId) {
            clearOptions(district, '--เลือกอำเภอ--');
            district.disabled = false;
            subdistrict.disabled = false;
            return;
        }

        try {
            const data = await fetchJson(
                `${urls.districts}/${encodeURIComponent(provinceId)}`,
                districtRequest
            );

            addOptions(district, Array.isArray(data) ? data : [], 'id', 'dist_name', selectedDistrict);
        } catch (error) {
            if (error.name === 'AbortError') return;
            console.error('Unable to load districts.', error);
            clearOptions(district, '--ไม่สามารถโหลดอำเภอ--');
            district.disabled = false;
        }
    }

    async function loadSubdistricts(districtId, selectedSubdistrict = '') {
        subdistrictRequest?.abort();
        subdistrictRequest = new AbortController();

        setSelectLoading(subdistrict, 'กำลังโหลดตำบล...');
        zipcode.value = '';

        if (!districtId) {
            clearOptions(subdistrict, '--เลือกตำบล--');
            subdistrict.disabled = false;
            return;
        }

        try {
            const data = await fetchJson(
                `${urls.subdistricts}/${encodeURIComponent(districtId)}`,
                subdistrictRequest
            );

            addOptions(
                subdistrict,
                Array.isArray(data) ? data : [],
                'id',
                'subd_name',
                selectedSubdistrict
            );
        } catch (error) {
            if (error.name === 'AbortError') return;
            console.error('Unable to load subdistricts.', error);
            clearOptions(subdistrict, '--ไม่สามารถโหลดตำบล--');
            subdistrict.disabled = false;
        }
    }

    async function loadZipcode(subdistrictId) {
        zipcodeRequest?.abort();
        zipcodeRequest = new AbortController();
        zipcode.value = '';

        if (!subdistrictId) return;

        try {
            const data = await fetchJson(
                `${urls.zipcode}/${encodeURIComponent(subdistrictId)}`,
                zipcodeRequest
            );

            zipcode.value = data.zipcode ?? '';
            clearFieldError(zipcode);
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Unable to load zipcode.', error);
            }
        }
    }

    province?.addEventListener('change', async function () {
        await loadDistricts(this.value);
    });

    district?.addEventListener('change', async function () {
        await loadSubdistricts(this.value);
    });

    subdistrict?.addEventListener('change', function () {
        loadZipcode(this.value);
    });

    async function restoreAddressSelections() {
        if (!province || !district || !subdistrict) return;

        const selectedProvince = province.dataset.selected || province.value;
        const selectedDistrict = district.dataset.selected || district.value;
        const selectedSubdistrict = subdistrict.dataset.selected || subdistrict.value;

        if (!selectedProvince) return;

        await loadDistricts(selectedProvince, selectedDistrict);

        if (selectedDistrict) {
            await loadSubdistricts(selectedDistrict, selectedSubdistrict);
        }

        if (selectedSubdistrict) {
            await loadZipcode(selectedSubdistrict);
        }
    }

    function clearFieldError(field) {
        if (!field) return;

        field.classList.remove('is-invalid');

        const parent = field.closest('.mb-3, .form-group, .col-md-3, .col-md-6, .col-md-8, .col-12');
        parent?.querySelectorAll('.invalid-feedback').forEach(feedback => {
            feedback.remove();
        });
    }

    document.querySelectorAll(
        '#visit-family-form input, #visit-family-form select, #visit-family-form textarea'
    ).forEach(field => {
        ['input', 'change'].forEach(eventName => {
            field.addEventListener(eventName, () => clearFieldError(field));
        });
    });

    function renderPreview(files) {
        if (!preview) return;
        preview.replaceChildren();

        files.forEach(file => {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-xl-3 mb-3';

            const card = document.createElement('div');
            card.className = 'visit-family-image-card';

            const image = document.createElement('img');
            image.alt = 'ตัวอย่างรูปเยี่ยมบ้าน';
            image.decoding = 'async';

            const objectUrl = URL.createObjectURL(file);
            image.src = objectUrl;
            image.addEventListener('load', () => URL.revokeObjectURL(objectUrl), { once: true });

            card.appendChild(image);
            col.appendChild(card);
            preview.appendChild(col);
        });
    }

    function normalizedJpegName(filename) {
        const base = filename.replace(/\.[^.]+$/, '') || 'visit-family-image';
        return `${base}.jpg`;
    }

    imageInput?.addEventListener('change', async function (event) {
        let files = Array.from(event.target.files || [])
            .filter(file => file.type.startsWith('image/'));

        const maxFiles = Number(imageInput.dataset.maxFiles || 10);

        if (files.length > maxFiles) {
            files = files.slice(0, maxFiles);

            if (window.Swal) {
                await Swal.fire({
                    icon: 'warning',
                    title: 'เลือกรูปภาพเกินจำนวน',
                    text: `ระบบจะใช้เฉพาะ ${maxFiles} รูปแรก`,
                    confirmButtonText: 'ตกลง',
                });
            }
        }

        renderPreview(files);

        if (files.length === 0) {
            imageInput.value = '';
            imageInput.dataset.processing = '0';
            if (uploadStatus) uploadStatus.textContent = '';
            return;
        }

        if (typeof DataTransfer === 'undefined') {
            imageInput.dataset.processing = '0';
            if (uploadStatus) {
                uploadStatus.textContent = 'เบราว์เซอร์นี้จะส่งไฟล์ต้นฉบับ และให้เซิร์ฟเวอร์ลดขนาดรูป';
            }
            return;
        }

        imageInput.dataset.processing = '1';
        if (uploadStatus) uploadStatus.textContent = 'กำลังเตรียมและบีบอัดรูปภาพ...';

        const transfer = new DataTransfer();

        try {
            for (const file of files) {
                let output = file;

                if (typeof window.imageCompression === 'function') {
                    try {
                        const compressed = await window.imageCompression(file, {
                            maxSizeMB: 0.7,
                            maxWidthOrHeight: 1600,
                            useWebWorker: true,
                            fileType: 'image/jpeg',
                            initialQuality: 0.75,
                        });

                        output = new File(
                            [compressed],
                            normalizedJpegName(file.name),
                            { type: 'image/jpeg', lastModified: Date.now() }
                        );
                    } catch (compressionError) {
                        console.warn('Image compression failed; original file will be used.', compressionError);
                    }
                }

                transfer.items.add(output);
            }

            imageInput.files = transfer.files;

            if (uploadStatus) {
                uploadStatus.textContent = `พร้อมอัปโหลด ${imageInput.files.length} รูป`;
            }
        } catch (error) {
            console.error('Unable to prepare image files.', error);

            if (uploadStatus) {
                uploadStatus.textContent = 'ไม่สามารถบีบอัดรูปได้ ระบบจะใช้ไฟล์ต้นฉบับ';
            }
        } finally {
            imageInput.dataset.processing = '0';
        }
    });

    document.querySelectorAll('.delete-image').forEach(button => {
        button.addEventListener('click', async function () {
            if (button.dataset.deleting === '1') return;

            const url = button.dataset.url;
            const id = button.dataset.id;
            const confirmed = window.Swal
                ? (await Swal.fire({
                    title: 'ยืนยันการลบ',
                    text: 'คุณต้องการลบรูปภาพนี้ใช่หรือไม่',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ลบรูปภาพ',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true,
                    allowOutsideClick: false,
                })).isConfirmed
                : window.confirm('คุณต้องการลบรูปภาพนี้ใช่หรือไม่');

            if (!confirmed) return;

            button.dataset.deleting = '1';
            button.disabled = true;
            const originalText = button.textContent;
            button.textContent = 'กำลังลบ...';

            try {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': @json(csrf_token()),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok || !data.success) {
                    throw new Error(data.error || data.message || 'ไม่สามารถลบรูปภาพได้');
                }

                document.getElementById(`image-${id}`)?.remove();

                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'ลบรูปภาพแล้ว',
                        timer: 1200,
                        showConfirmButton: false,
                    });
                }
            } catch (error) {
                console.error('Unable to delete image.', error);
                button.disabled = false;
                button.dataset.deleting = '0';
                button.textContent = originalText;

                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'ไม่สามารถลบรูปภาพได้',
                        text: error.message || 'กรุณาลองใหม่อีกครั้ง',
                        confirmButtonText: 'ตกลง',
                    });
                } else {
                    alert(error.message || 'ไม่สามารถลบรูปภาพได้');
                }
            }
        });
    });

    form?.addEventListener('submit', function (event) {
        if (imageInput?.dataset.processing === '1') {
            event.preventDefault();

            if (window.Swal) {
                Swal.fire({
                    icon: 'info',
                    title: 'กำลังเตรียมรูปภาพ',
                    text: 'กรุณารอให้ระบบเตรียมรูปภาพเสร็จก่อนบันทึก',
                    confirmButtonText: 'ตกลง',
                });
            }

            return;
        }

        if (form.dataset.submitting === '1') {
            event.preventDefault();
            return;
        }

        form.dataset.submitting = '1';

        if (submitButton) {
            submitButton.dataset.originalHtml ||= submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.querySelector('span').textContent = 'กำลังบันทึก...';
        }
    });

    restoreAddressSelections();
});
</script>

@endsection