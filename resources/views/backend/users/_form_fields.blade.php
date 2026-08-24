@php
    $editingUser = $user ?? null;
    $selectedHouses = old(
        'house_ids',
        $editingUser ? $editingUser->houses->pluck('id')->map(fn ($id) => (int) $id)->toArray() : []
    );
    $photoPreview = $editingUser?->photo_url ?? asset('upload/no_image.jpg');
@endphp

@if($errors->any())
    <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
        <div class="fw-bold mb-1">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            กรุณาตรวจสอบข้อมูลที่กรอก
        </div>
        <div class="small">พบข้อมูลบางส่วนไม่ถูกต้อง โปรดแก้ไขช่องที่มีข้อความแจ้งเตือน</div>
    </div>
@endif

<div class="row g-4">
    <div class="col-12">
        <section class="user-form-section">
            <div class="user-form-section-title">
                <span class="user-form-section-icon"><i class="bi bi-person-badge-fill"></i></span>
                <div>
                    <h5 class="mb-1">ข้อมูลบัญชีผู้ใช้งาน</h5>
                    <div class="text-muted small">ชื่อ อีเมล บทบาท สถานะ และข้อมูลติดต่อ</div>
                </div>
            </div>

            <div class="row g-4 mt-0">
                <div class="col-xl-3 col-lg-4">
                    <div class="user-photo-panel">
                        <img
                            src="{{ $photoPreview }}"
                            alt="รูปผู้ใช้งาน"
                            class="user-photo-preview"
                            id="userPhotoPreview"
                        >
                        <label class="btn user-photo-button" for="photoInput">
                            <i class="bi bi-camera-fill"></i>
                            เลือกรูปประจำตัว
                        </label>
                        <input
                            type="file"
                            name="photo"
                            id="photoInput"
                            class="d-none"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        >
                        <div class="text-muted small text-center mt-2">JPG, PNG หรือ WEBP ไม่เกิน 2 MB</div>
                        @error('photo')
                            <div class="text-danger small text-center mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-xl-9 col-lg-8">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <label class="form-label fw-semibold">ชื่อผู้ใช้งาน <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                name="name"
                                class="form-control form-control-modern @error('name') is-invalid @enderror"
                                value="{{ old('name', $editingUser?->name) }}"
                                maxlength="255"
                                autocomplete="name"
                            >
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label fw-semibold">อีเมล <span class="text-danger">*</span></label>
                            <input
                                type="email"
                                name="email"
                                class="form-control form-control-modern @error('email') is-invalid @enderror"
                                value="{{ old('email', $editingUser?->email) }}"
                                maxlength="255"
                                autocomplete="email"
                            >
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label fw-semibold">โทรศัพท์</label>
                            <input
                                type="text"
                                name="phone"
                                class="form-control form-control-modern @error('phone') is-invalid @enderror"
                                value="{{ old('phone', $editingUser?->phone) }}"
                                maxlength="50"
                            >
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label fw-semibold">บทบาทผู้ใช้งาน <span class="text-danger">*</span></label>
                            <select name="role" class="form-select form-control-modern @error('role') is-invalid @enderror">
                                <option value="">-- เลือกบทบาทผู้ใช้งาน --</option>
                                @foreach($roles as $roleValue => $roleLabel)
                                    <option
                                        value="{{ $roleValue }}"
                                        {{ old('role', $editingUser?->role) === $roleValue ? 'selected' : '' }}
                                    >
                                        {{ $roleLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">บทบาท Admin ไม่สามารถสร้างหรือมอบจากหน้านี้ และผู้บริหารไม่สามารถแต่งตั้งผู้บริหารเพิ่มเอง</div>
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label fw-semibold">
                                {{ $editingUser ? 'รหัสผ่านใหม่' : 'รหัสผ่าน' }}
                                @unless($editingUser)<span class="text-danger">*</span>@endunless
                            </label>
                            <input
                                type="password"
                                name="password"
                                class="form-control form-control-modern @error('password') is-invalid @enderror"
                                autocomplete="new-password"
                            >
                            <div class="form-text">
                                {{ $editingUser ? 'เว้นว่างไว้เมื่อต้องการใช้รหัสผ่านเดิม • ' : '' }}
                                รหัสผ่านต้องมีอย่างน้อย 10 ตัวอักษร และมีทั้งตัวอักษรกับตัวเลข
                            </div>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label fw-semibold">
                                ยืนยันรหัสผ่าน{{ $editingUser ? 'ใหม่' : '' }}
                                @unless($editingUser)<span class="text-danger">*</span>@endunless
                            </label>
                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control form-control-modern @error('password_confirmation') is-invalid @enderror"
                                autocomplete="new-password"
                            >
                            @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label fw-semibold">สถานะ <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-control-modern @error('status') is-invalid @enderror">
                                <option value="1" {{ (string) old('status', $editingUser?->status ?? '1') === '1' ? 'selected' : '' }}>ใช้งาน</option>
                                <option value="0" {{ (string) old('status', $editingUser?->status ?? '1') === '0' ? 'selected' : '' }}>ปิดใช้งาน</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label fw-semibold">ที่อยู่/รายละเอียดติดต่อ</label>
                            <textarea
                                name="address"
                                rows="2"
                                class="form-control form-control-modern @error('address') is-invalid @enderror"
                                maxlength="2000"
                            >{{ old('address', $editingUser?->address) }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-12">
        <section class="user-form-section">
            <div class="user-form-section-title">
                <span class="user-form-section-icon user-form-section-icon-green"><i class="bi bi-diagram-3-fill"></i></span>
                <div>
                    <h5 class="mb-1">ขอบเขตข้อมูลที่รับผิดชอบ</h5>
                    <div class="text-muted small">กำหนดโครงการก่อน แล้วจำกัดเพิ่มเติมด้วยบ้านที่ผู้ใช้งานดูแล</div>
                </div>
            </div>

            <div class="row g-4 mt-0">
                <div class="col-lg-6">
                    <label class="form-label fw-semibold">หน่วยงาน / โครงการ</label>
                    <select name="project_id" class="form-select form-control-modern @error('project_id') is-invalid @enderror">
                        <option value="">-- ไม่กำหนดหน่วยงาน/โครงการ --</option>
                        @foreach($projects as $project)
                            <option
                                value="{{ $project->id }}"
                                {{ (string) old('project_id', $editingUser?->project_id) === (string) $project->id ? 'selected' : '' }}
                            >
                                {{ $project->project_name ?? $project->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">ระบบเดิมรองรับหนึ่งโครงการต่อผู้ใช้ เพื่อไม่กระทบ Client::forUser()</div>
                </div>

                <div class="col-12">
                    <div class="house-box">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <div>
                                <div class="fw-bold text-dark">
                                    <i class="bi bi-house-check-fill text-primary me-2"></i>
                                    เลือกบ้านที่ดูแล
                                </div>
                                <div class="text-muted small mt-1">
                                    หากเลือกบ้าน ระบบจะจำกัดผู้รับบริการเฉพาะบ้านเหล่านี้ก่อนใช้สิทธิ์รายฟอร์ม
                                </div>
                            </div>

                            <div class="form-check m-0">
                                <input type="checkbox" class="form-check-input" id="checkAllHouses">
                                <label class="form-check-label fw-semibold" for="checkAllHouses">เลือกทั้งหมด</label>
                            </div>
                        </div>

                        @if($houses->isNotEmpty())
                            <div class="row g-3">
                                @foreach($houses as $house)
                                    <div class="col-md-6 col-xl-4">
                                        <label class="house-option" for="house_{{ $house->id }}">
                                            <input
                                                type="checkbox"
                                                class="form-check-input house-checkbox"
                                                name="house_ids[]"
                                                value="{{ $house->id }}"
                                                id="house_{{ $house->id }}"
                                                {{ in_array((int) $house->id, array_map('intval', $selectedHouses), true) ? 'checked' : '' }}
                                            >
                                            <span class="house-option-text">
                                                <i class="bi bi-house-door-fill text-primary me-1"></i>
                                                {{ $house->house_name }}
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="house-empty-state">
                                <i class="bi bi-house-exclamation"></i>
                                ยังไม่มีข้อมูลบ้าน กรุณาเพิ่มบ้านก่อนกำหนดให้ผู้ใช้งาน
                            </div>
                        @endif

                        @error('house_ids')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                        @error('house_ids.*')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('backend.users._form_permissions', [
        'permissionGroups' => $permissionGroups,
        'user' => $editingUser,
    ])

    <div class="col-12">
        <div class="user-form-actions">
            <a href="{{ route('users.index') }}" class="btn user-btn-cancel">
                <i class="bi bi-x-circle"></i>
                ยกเลิก
            </a>
            <button type="submit" class="btn user-btn-save" data-user-submit>
                <i class="bi bi-check-circle-fill"></i>
                <span data-submit-label>{{ $editingUser ? 'บันทึกการแก้ไข' : 'บันทึกข้อมูล' }}</span>
            </button>
        </div>
    </div>
</div>

<style>
.user-form-page{
    --user-primary:#2563eb;
    --user-border:#e4ebf3;
    --user-text:#0f172a;
    --user-muted:#64748b;
}
.user-form-page .user-form-card{
    border:1px solid var(--user-border);
    border-radius:26px;
    background:#fff;
    box-shadow:0 14px 40px rgba(15,23,42,.055);
    overflow:hidden;
}
.user-form-page .user-form-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:1rem;
    flex-wrap:wrap;
    padding:1.25rem 1.4rem;
    border-bottom:1px solid #edf2f7;
    background:#fff;
}
.user-form-page .user-form-header-left{
    display:flex;
    align-items:center;
    gap:1rem;
}
.user-form-page .user-form-header-icon{
    width:54px;
    height:54px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:17px;
    color:#fff;
    background:linear-gradient(135deg,#1d4ed8,#3b82f6);
    box-shadow:0 12px 26px rgba(37,99,235,.2);
    flex:0 0 auto;
}
.user-form-page .user-form-page-title{
    color:var(--user-text);
    font-weight:800;
    font-size:1.3rem;
}
.user-form-page .user-form-back{
    display:inline-flex;
    align-items:center;
    gap:.45rem;
    border:1px solid #dbe3ee;
    border-radius:999px;
    padding:.68rem 1rem;
    background:#fff;
    color:#334155;
    font-weight:700;
}
.user-form-page .user-form-back:hover{
    color:#1d4ed8;
    border-color:#93c5fd;
    background:#eff6ff;
}
.user-form-page .user-form-body{
    padding:1.25rem;
}
.user-form-page .user-form-section{
    border:1px solid var(--user-border);
    border-radius:22px;
    padding:1.25rem;
    background:#fff;
}
.user-form-page .user-form-section-title{
    display:flex;
    align-items:center;
    gap:.8rem;
    padding-bottom:1rem;
    border-bottom:1px solid #edf2f7;
}
.user-form-page .user-form-section-title h5{
    color:var(--user-text);
    font-weight:800;
}
.user-form-page .user-form-section-icon{
    width:44px;
    height:44px;
    border-radius:14px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    color:#1d4ed8;
    background:#dbeafe;
    flex:0 0 auto;
}
.user-form-page .user-form-section-icon-green{
    color:#047857;
    background:#d1fae5;
}
.user-form-page .form-control-modern{
    min-height:48px;
    border-radius:14px;
    border:1px solid #dbe3ee;
    box-shadow:none;
}
.user-form-page textarea.form-control-modern{
    min-height:78px;
}
.user-form-page .form-control-modern:focus{
    border-color:#60a5fa;
    box-shadow:0 0 0 .2rem rgba(37,99,235,.10);
}
.user-form-page .user-photo-panel{
    height:100%;
    min-height:270px;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    padding:1rem;
    border:1px dashed #bfdbfe;
    border-radius:20px;
    background:#f8fbff;
}
.user-form-page .user-photo-preview{
    width:132px;
    height:132px;
    object-fit:cover;
    border-radius:50%;
    border:5px solid #fff;
    box-shadow:0 10px 28px rgba(15,23,42,.12);
    background:#fff;
}
.user-form-page .user-photo-button{
    display:inline-flex;
    align-items:center;
    gap:.45rem;
    margin-top:1rem;
    border:1px solid #bfdbfe;
    border-radius:999px;
    background:#eff6ff;
    color:#1d4ed8;
    font-weight:700;
}
.user-form-page .user-photo-button:hover{
    color:#1e40af;
    background:#dbeafe;
}
.user-form-page .house-box{
    border:1px solid #e1e9f2;
    border-radius:20px;
    padding:1.1rem;
    background:#f9fbfd;
}
.user-form-page .house-option{
    display:flex;
    align-items:center;
    gap:.75rem;
    width:100%;
    min-height:58px;
    border:1px solid #e2e8f0;
    border-radius:16px;
    padding:.85rem 1rem;
    background:#fff;
    cursor:pointer;
    transition:all .18s ease;
}
.user-form-page .house-option:hover{
    border-color:#93c5fd;
    transform:translateY(-1px);
    box-shadow:0 8px 22px rgba(15,23,42,.05);
}
.user-form-page .house-option-text{
    font-weight:700;
    color:#263247;
}
.user-form-page .house-empty-state{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:.6rem;
    min-height:100px;
    color:#64748b;
    background:#fff;
    border:1px dashed #cbd5e1;
    border-radius:16px;
}
.user-form-page .user-form-actions{
    display:flex;
    justify-content:flex-end;
    gap:.7rem;
    flex-wrap:wrap;
    padding-top:.25rem;
}
.user-form-page .user-btn-cancel,
.user-form-page .user-btn-save{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:.5rem;
    min-width:150px;
    min-height:46px;
    border-radius:999px;
    font-weight:800;
    padding:.7rem 1.15rem;
}
.user-form-page .user-btn-cancel{
    color:#475569;
    border:1px solid #dbe3ee;
    background:#fff;
}
.user-form-page .user-btn-save{
    color:#fff;
    border:0;
    background:linear-gradient(135deg,#1d4ed8,#3b82f6);
    box-shadow:0 10px 24px rgba(37,99,235,.22);
}
.user-form-page .user-btn-save:hover,
.user-form-page .user-btn-save:focus,
.user-form-page .user-btn-save:active,
.user-form-page .user-btn-save:disabled{
    color:#fff;
    background:linear-gradient(135deg,#1e40af,#2563eb);
}
@media(max-width:767.98px){
    .user-form-page .user-form-header{
        align-items:flex-start;
    }
    .user-form-page .user-form-header-left{
        align-items:flex-start;
    }
    .user-form-page .user-form-back{
        width:100%;
        justify-content:center;
    }
    .user-form-page .user-form-body,
    .user-form-page .user-form-section{
        padding:1rem;
    }
    .user-form-page .user-btn-cancel,
    .user-form-page .user-btn-save{
        flex:1 1 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkAllHouses');
    const houseCheckboxes = [...document.querySelectorAll('.house-checkbox')];

    function syncCheckAll() {
        if (!checkAll) return;
        checkAll.checked = houseCheckboxes.length > 0 && houseCheckboxes.every(function (item) {
            return item.checked;
        });
        checkAll.indeterminate = houseCheckboxes.some(function (item) {
            return item.checked;
        }) && !checkAll.checked;
    }

    checkAll?.addEventListener('change', function () {
        houseCheckboxes.forEach(function (checkbox) {
            checkbox.checked = checkAll.checked;
        });
        syncCheckAll();
    });

    houseCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', syncCheckAll);
    });

    syncCheckAll();

    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('userPhotoPreview');

    photoInput?.addEventListener('change', function () {
        const file = photoInput.files && photoInput.files[0];
        if (!file || !photoPreview) return;

        const objectUrl = URL.createObjectURL(file);
        photoPreview.src = objectUrl;
        photoPreview.onload = function () {
            URL.revokeObjectURL(objectUrl);
        };
    });
});
</script>
