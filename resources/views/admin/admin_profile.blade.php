@extends('admin.admin_master')
@section('admin')

@php
    $profilePhoto = !empty($profileData->photo)
        ? url('upload/user_images/' . $profileData->photo)
        : url('upload/no_image.jpg');
@endphp

<div class="content">
    <div class="container-fluid py-4 admin-profile-page">

        {{-- ============================================================
            Header
        ============================================================ --}}
        <div class="app-profile-header">
            <div class="app-profile-header__main">
                <div class="app-profile-header__icon">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div>
                    <h4 class="app-profile-header__title mb-1">ข้อมูลส่วนตัว</h4>
                    <div class="app-profile-header__subtitle">
                        จัดการข้อมูลบัญชี รูปประจำตัว และรหัสผ่านของคุณ
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
            Profile summary
        ============================================================ --}}
        <div class="app-profile-summary">
            <img
                src="{{ $profilePhoto }}"
                class="app-profile-summary__avatar"
                alt="รูปประจำตัวของ {{ $profileData->name }}"
            >

            <div class="app-profile-summary__info">
                <div class="app-profile-summary__name">
                    {{ $profileData->name }}
                </div>
                <div class="app-profile-summary__email">
                    {{ $profileData->email }}
                </div>
            </div>
        </div>

        <div class="row g-4 align-items-stretch">

            {{-- ========================================================
                Personal information
            ======================================================== --}}
            <div class="col-12 col-xl-7">
                <div class="app-profile-card h-100">
                    <div class="app-profile-card__header">
                        <div class="app-profile-card__header-icon app-profile-card__header-icon--blue">
                            <i class="bi bi-person-vcard"></i>
                        </div>
                        <div>
                            <h5 class="app-profile-card__title mb-1">ข้อมูลบัญชีผู้ใช้งาน</h5>
                            <div class="app-profile-card__subtitle">
                                แก้ไขชื่อ อีเมล ข้อมูลติดต่อ และรูปประจำตัว
                            </div>
                        </div>
                    </div>

                    <form
                        action="{{ route('profile.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="app-profile-form js-safe-submit"
                    >
                        @csrf

                        <div class="app-profile-card__body">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="profile_name" class="form-label">
                                        ชื่อผู้ใช้งาน <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="profile_name"
                                        name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $profileData->name) }}"
                                        autocomplete="name"
                                        required
                                    >
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="profile_email" class="form-label">
                                        อีเมล <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="email"
                                        id="profile_email"
                                        name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $profileData->email) }}"
                                        autocomplete="email"
                                        required
                                    >
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="profile_phone" class="form-label">โทรศัพท์</label>
                                    <input
                                        type="text"
                                        id="profile_phone"
                                        name="phone"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone', $profileData->phone) }}"
                                        autocomplete="tel"
                                    >
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="profile_address" class="form-label">ที่อยู่ / รายละเอียดติดต่อ</label>
                                    <textarea
                                        id="profile_address"
                                        name="address"
                                        class="form-control app-profile-textarea @error('address') is-invalid @enderror"
                                        rows="4"
                                        autocomplete="street-address"
                                    >{{ old('address', $profileData->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="image" class="form-label">รูปประจำตัว</label>

                                    <div class="app-profile-photo-box">
                                        <img
                                            id="showImage"
                                            src="{{ $profilePhoto }}"
                                            class="app-profile-photo-box__preview"
                                            alt="ตัวอย่างรูปประจำตัว"
                                        >

                                        <div class="app-profile-photo-box__control">
                                            <input
                                                type="file"
                                                id="image"
                                                name="photo"
                                                class="form-control @error('photo') is-invalid @enderror"
                                                accept="image/jpeg,image/png,image/webp"
                                            >
                                            <div class="app-profile-help-text">
                                                รองรับไฟล์ JPG, PNG หรือ WEBP ขนาดไม่เกิน 2 MB
                                            </div>
                                            @error('photo')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="app-profile-card__footer">
                            <button
                                type="submit"
                                class="btn app-profile-btn-primary js-submit-button"
                                data-loading-text="กำลังบันทึก..."
                            >
                                <i class="bi bi-check-circle-fill"></i>
                                <span>บันทึกข้อมูล</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ========================================================
                Change password
            ======================================================== --}}
         <div class="col-12 col-xl-5" id="change-password">
    {{-- DASHBOARD_USER_MENU_LOGOUT_HOTFIX_V1: anchor used by topbar account menu --}}
    <div class="app-profile-card h-100">

        {{-- Header --}}
        <div class="app-profile-card__header">
            <div class="app-profile-card__header-icon app-profile-card__header-icon--green">
                <i class="bi bi-shield-lock"></i>
            </div>

            <div>
                <h5 class="app-profile-card__title mb-1">
                    เปลี่ยนรหัสผ่าน
                </h5>

                <div class="app-profile-card__subtitle">
                    ยืนยันรหัสผ่านปัจจุบันก่อนกำหนดรหัสผ่านใหม่
                </div>
            </div>
        </div>

        {{-- Password Form --}}
        <form
            action="{{ route('admin.password.update') }}"
            method="POST"
            class="app-profile-form js-safe-submit"
            autocomplete="off"
        >
            @csrf

            <div class="app-profile-card__body">

                {{-- Security Notice --}}
                <div class="app-profile-security-note">
                    <i class="bi bi-info-circle-fill"></i>

                    <span>
                        หลังเปลี่ยนรหัสผ่านสำเร็จ ระบบจะออกจากระบบอัตโนมัติ
                        และให้เข้าสู่ระบบใหม่ด้วยรหัสผ่านใหม่
                    </span>
                </div>

                {{-- รหัสผ่านปัจจุบัน --}}
                <div class="mb-3">
                    <label for="old_password" class="form-label">
                        รหัสผ่านปัจจุบัน
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="password"
                        id="old_password"
                        name="old_password"
                        class="form-control @error('old_password') is-invalid @enderror"
                        placeholder="กรอกรหัสผ่านปัจจุบัน"
                        autocomplete="current-password"
                        required
                    >

                    @error('old_password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- รหัสผ่านใหม่ --}}
                <div class="mb-3">
                    <label for="new_password" class="form-label">
                        รหัสผ่านใหม่
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        class="form-control @error('new_password') is-invalid @enderror"
                        placeholder="กรอกรหัสผ่านใหม่"
                        autocomplete="new-password"
                        minlength="10"
                        aria-describedby="newPasswordHelp"
                        required
                    >

                    @error('new_password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div
                        id="newPasswordHelp"
                        class="form-text mt-2"
                    >
                        <i class="bi bi-info-circle me-1"></i>
                        รหัสผ่านต้องมีอย่างน้อย
                        <strong>10 ตัวอักษร</strong>
                        และต้องประกอบด้วย
                        <strong>ตัวอักษรและตัวเลขอย่างน้อยอย่างละ 1 ตัว</strong>
                    </div>
                </div>

                {{-- ยืนยันรหัสผ่านใหม่ --}}
                <div class="mb-0">
                    <label
                        for="new_password_confirmation"
                        class="form-label"
                    >
                        ยืนยันรหัสผ่านใหม่
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="password"
                        id="new_password_confirmation"
                        name="new_password_confirmation"
                        class="form-control @error('new_password_confirmation') is-invalid @enderror"
                        placeholder="กรอกรหัสผ่านใหม่อีกครั้ง"
                        autocomplete="new-password"
                        minlength="10"
                        required
                    >

                    @error('new_password_confirmation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="form-text mt-2">
                        กรุณากรอกรหัสผ่านใหม่อีกครั้งให้ตรงกัน
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="app-profile-card__footer">
                <button
                    type="submit"
                    class="btn app-profile-btn-primary js-submit-button"
                    data-loading-text="กำลังเปลี่ยนรหัสผ่าน..."
                >
                    <i class="bi bi-key-fill"></i>
                    <span>เปลี่ยนรหัสผ่าน</span>
                </button>
            </div>

        </form>
    </div>
</div>

<style>
    .admin-profile-page{
        --profile-border:#e4ebf3;
        --profile-text:#0f172a;
        --profile-muted:#64748b;
        --profile-primary:#2563eb;
        --profile-primary-dark:#1d4ed8;
    }

    .admin-profile-page .app-profile-header,
    .admin-profile-page .app-profile-summary,
    .admin-profile-page .app-profile-card{
        background:#fff;
        border:1px solid var(--profile-border);
        box-shadow:0 10px 30px rgba(15,23,42,.045);
    }

    .admin-profile-page .app-profile-header{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:1rem;
        padding:1.15rem 1.25rem;
        margin-bottom:1rem;
        border-radius:24px;
    }

    .admin-profile-page .app-profile-header__main{
        display:flex;
        align-items:center;
        gap:.85rem;
    }

    .admin-profile-page .app-profile-header__icon{
        width:54px;
        height:54px;
        flex:0 0 54px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:17px;
        color:#fff;
        background:linear-gradient(135deg,#1d4ed8,#3b82f6);
        box-shadow:0 12px 24px rgba(37,99,235,.20);
        font-size:1.25rem;
    }

    .admin-profile-page .app-profile-header__title{
        color:var(--profile-text);
        font-size:1.32rem;
        font-weight:800;
    }

    .admin-profile-page .app-profile-header__subtitle{
        color:var(--profile-muted);
        font-size:.9rem;
    }

    .admin-profile-page .app-profile-summary{
        display:flex;
        align-items:center;
        gap:1rem;
        padding:1rem 1.2rem;
        margin-bottom:1rem;
        border-radius:22px;
    }

    .admin-profile-page .app-profile-summary__avatar{
        width:76px;
        height:76px;
        flex:0 0 76px;
        object-fit:cover;
        border-radius:50%;
        border:4px solid #fff;
        box-shadow:0 0 0 1px #dbe3ee,0 8px 20px rgba(15,23,42,.10);
    }

    .admin-profile-page .app-profile-summary__name{
        color:var(--profile-text);
        font-size:1.05rem;
        font-weight:800;
    }

    .admin-profile-page .app-profile-summary__email{
        margin-top:.2rem;
        color:var(--profile-muted);
        font-size:.88rem;
        overflow-wrap:anywhere;
    }

    .admin-profile-page .app-profile-card{
        overflow:hidden;
        border-radius:24px;
    }

    .admin-profile-page .app-profile-card__header{
        display:flex;
        align-items:center;
        gap:.8rem;
        min-height:82px;
        padding:1rem 1.15rem;
        border-bottom:1px solid var(--profile-border);
    }

    .admin-profile-page .app-profile-card__header-icon{
        width:44px;
        height:44px;
        flex:0 0 44px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:14px;
        font-size:1.05rem;
    }

    .admin-profile-page .app-profile-card__header-icon--blue{
        color:#1d4ed8;
        background:#dbeafe;
    }

    .admin-profile-page .app-profile-card__header-icon--green{
        color:#047857;
        background:#d1fae5;
    }

    .admin-profile-page .app-profile-card__title{
        color:var(--profile-text);
        font-size:1rem;
        font-weight:800;
    }

    .admin-profile-page .app-profile-card__subtitle{
        color:var(--profile-muted);
        font-size:.84rem;
    }

    .admin-profile-page .app-profile-card__body{
        padding:1.15rem;
    }

    .admin-profile-page .app-profile-card__footer{
        display:flex;
        justify-content:flex-end;
        padding:1rem 1.15rem;
        border-top:1px solid #eef2f7;
        background:#fbfdff;
    }

    .admin-profile-page .form-label{
        margin-bottom:.42rem;
        color:#334155;
        font-size:.86rem;
        font-weight:800;
    }

    .admin-profile-page .form-control{
        min-height:44px;
        border-color:#dbe3ee;
        border-radius:13px;
        box-shadow:none;
    }

    .admin-profile-page .form-control:focus{
        border-color:#93c5fd;
        box-shadow:0 0 0 .2rem rgba(37,99,235,.08);
    }

    .admin-profile-page .app-profile-textarea{
        min-height:108px;
        resize:vertical;
    }

    .admin-profile-page .app-profile-photo-box{
        display:flex;
        align-items:center;
        gap:1rem;
        padding:1rem;
        border:1px dashed #bfd3ee;
        border-radius:18px;
        background:#f8fbff;
    }

    .admin-profile-page .app-profile-photo-box__preview{
        width:82px;
        height:82px;
        flex:0 0 82px;
        object-fit:cover;
        border-radius:50%;
        border:4px solid #fff;
        box-shadow:0 0 0 1px #dbe3ee,0 6px 16px rgba(15,23,42,.08);
    }

    .admin-profile-page .app-profile-photo-box__control{
        flex:1 1 auto;
        min-width:0;
    }

    .admin-profile-page .app-profile-help-text{
        margin-top:.45rem;
        color:var(--profile-muted);
        font-size:.78rem;
    }

    .admin-profile-page .app-profile-security-note{
        display:flex;
        align-items:flex-start;
        gap:.55rem;
        margin-bottom:1rem;
        padding:.8rem .9rem;
        color:#1e40af;
        background:#eff6ff;
        border:1px solid #bfdbfe;
        border-radius:14px;
        font-size:.82rem;
        line-height:1.55;
    }

    .admin-profile-page .app-profile-security-note i{
        margin-top:.08rem;
        flex:0 0 auto;
    }

    .admin-profile-page .app-profile-btn-primary{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:.45rem;
        min-height:42px;
        padding:.65rem 1rem;
        border:0;
        border-radius:999px;
        color:#fff;
        background:linear-gradient(135deg,var(--profile-primary-dark),#3b82f6);
        box-shadow:0 8px 20px rgba(37,99,235,.18);
        font-weight:800;
    }

    .admin-profile-page .app-profile-btn-primary:hover,
    .admin-profile-page .app-profile-btn-primary:focus,
    .admin-profile-page .app-profile-btn-primary:active,
    .admin-profile-page .app-profile-btn-primary:disabled{
        color:#fff;
        background:linear-gradient(135deg,var(--profile-primary-dark),#3b82f6);
    }

    .admin-profile-page .app-profile-btn-primary:hover{
        transform:translateY(-1px);
    }

    @media (max-width:767.98px){
        .admin-profile-page{
            padding-left:.65rem !important;
            padding-right:.65rem !important;
        }

        .admin-profile-page .app-profile-header{
            padding:1rem;
            border-radius:20px;
        }

        .admin-profile-page .app-profile-header__icon{
            width:48px;
            height:48px;
            flex-basis:48px;
            border-radius:15px;
        }

        .admin-profile-page .app-profile-header__title{
            font-size:1.12rem;
        }

        .admin-profile-page .app-profile-header__subtitle{
            font-size:.82rem;
        }

        .admin-profile-page .app-profile-summary{
            align-items:flex-start;
            padding:1rem;
        }

        .admin-profile-page .app-profile-summary__avatar{
            width:64px;
            height:64px;
            flex-basis:64px;
        }

        .admin-profile-page .app-profile-card{
            border-radius:20px;
        }

        .admin-profile-page .app-profile-photo-box{
            align-items:flex-start;
            flex-direction:column;
        }

        .admin-profile-page .app-profile-card__footer .btn{
            width:100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('showImage');

        if (imageInput && imagePreview) {
            imageInput.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function (event) {
                    imagePreview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            });
        }

        document.querySelectorAll('.js-safe-submit').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (form.dataset.submitting === '1') {
                    event.preventDefault();
                    return;
                }

                if (!form.checkValidity()) {
                    return;
                }

                form.dataset.submitting = '1';

                const button = form.querySelector('.js-submit-button');
                if (!button) return;

                button.disabled = true;
                const text = button.querySelector('span');
                if (text && button.dataset.loadingText) {
                    text.textContent = button.dataset.loadingText;
                }
            });
        });
    });
</script>

@endsection
