@extends('admin_client.admin_client')

@php
    use App\Helpers\ThaiDateHelper;
    use App\Models\HealthcareRight;

    $isEditing = isset($editing) && $editing;
    $formAction = $isEditing
        ? route('healthcare_rights.update', $editing->id)
        : route('healthcare_rights.store');
    $formPermissionAction = $isEditing ? 'update' : 'create';
    $latestRight = $rights->first();
    $today = now('Asia/Bangkok')->toDateString();

    $selectedStatus = old(
        'coverage_status',
        $isEditing ? $editing->coverage_status : ''
    );

    $hospitalValue = old(
        'primary_hospital',
        $isEditing ? $editing->primary_hospital : ''
    );

    $recordDateValue = old(
        'record_date',
        $isEditing
            ? optional($editing->record_date)->format('Y-m-d')
            : $today
    );

    $clientFullName = trim((string) (
        $client->fullname
        ?? $client->full_name
        ?? (($client->first_name ?? '') . ' ' . ($client->last_name ?? ''))
    ));

    $recorderName = $isEditing
        ? (string) $editing->recorder_name
        : trim((string) (auth()->user()->name ?? ''));

    $canCreateRight = auth()->user()
        && method_exists(auth()->user(), 'hasFormPermission')
        && auth()->user()->hasFormPermission('health_treatment_rights', 'create');
@endphp

@section('content')
<div class="container-fluid py-3 healthcare-rights-page">
    <div class="hr-hero mb-3">
        <div>
            <div class="d-flex align-items-center gap-3">
                <div class="hr-icon"><i class="bi bi-shield-check"></i></div>
                <div>
                    <h4 class="mb-1 fw-bold">สิทธิรักษาพยาบาล</h4>
                    <div class="text-muted small">
                        บันทึกและติดตามสิทธิการรักษาพยาบาลของผู้รับบริการ
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <span class="hr-chip">
                    <i class="bi bi-person"></i>
                    {{ $clientFullName !== '' ? $clientFullName : 'ผู้รับบริการ' }}
                </span>
                <span class="hr-chip">
                    <i class="bi bi-card-list"></i>
                    เลขทะเบียน {{ $client->register_number ?: '-' }}
                </span>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 justify-content-end">
            @if($rights->isNotEmpty())
                <a href="{{ route('healthcare_rights.report', $client->id) }}"
                   target="_blank"
                   rel="noopener"
                   class="btn btn-outline-primary hr-action-btn"
                   data-permission-action="print">
                    <i class="bi bi-printer"></i> รายงาน
                </a>
            @endif

            <a href="{{ route('admin.index', $client->id) }}"
               class="btn btn-outline-secondary hr-action-btn"
               data-permission-action="navigation">
                <i class="bi bi-arrow-left"></i> กลับข้อมูลผู้รับบริการ
            </a>
        </div>
    </div>

    @if($latestRight)
        <div class="hr-current mb-3">
            <div class="hr-current-label">สิทธิปัจจุบันล่าสุด</div>
            <div class="hr-current-status">{{ $latestRight->coverage_status }}</div>
            <div class="hr-current-meta">
                บันทึกเมื่อ {{ ThaiDateHelper::formatThaiShort($latestRight->record_date) }}
                @if($latestRight->primary_hospital)
                    · {{ $latestRight->primary_hospital }}
                @endif
            </div>
        </div>
    @endif

    @if($isEditing || $canCreateRight)
    <div class="card border-0 shadow-sm mb-3 hr-card">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="mb-1 fw-bold">
                        <i class="bi {{ $isEditing ? 'bi-pencil-square' : 'bi-plus-circle' }} me-1"></i>
                        {{ $isEditing ? 'แก้ไขข้อมูลสิทธิรักษาพยาบาล' : 'เพิ่มข้อมูลสิทธิรักษาพยาบาล' }}
                    </h5>
                    <div class="text-muted small">
                        ช่องที่มีเครื่องหมาย <span class="text-danger">*</span> จำเป็นต้องกรอก
                    </div>
                </div>

                @if($isEditing)
                    <a href="{{ route('healthcare_rights.index', $client->id) }}"
                       class="btn btn-light border"
                       data-permission-action="navigation">
                        <i class="bi bi-x-lg"></i> ยกเลิกการแก้ไข
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body p-4">
            <form id="healthcareRightForm"
                  action="{{ $formAction }}"
                  method="POST"
                  data-permission-action="{{ $formPermissionAction }}"
                  novalidate>
                @csrf
                @if($isEditing)
                    @method('PUT')
                @endif

                <input type="hidden" name="client_id" value="{{ $client->id }}">

                <div class="row g-3">
                    <div class="col-lg-4">
                        <label class="form-label fw-semibold">
                            วันที่บันทึก <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="record_date"
                               value="{{ $recordDateValue }}"
                               max="{{ $today }}"
                               class="form-control @error('record_date') is-invalid @enderror"
                               required>
                        @error('record_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-8">
                        <label class="form-label fw-semibold d-block">
                            สถานะสิทธิ <span class="text-danger">*</span>
                        </label>

                        <div class="hr-radio-grid @error('coverage_status') hr-invalid @enderror"
                             id="coverageStatusGroup">
                            @foreach(HealthcareRight::statusOptions() as $status)
                                <label class="hr-radio-option">
                                    <input type="radio"
                                           name="coverage_status"
                                           value="{{ $status }}"
                                           @checked($selectedStatus === $status)
                                           required>
                                    <span>{{ $status }}</span>
                                </label>
                            @endforeach
                        </div>

                        @error('coverage_status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12" id="hospitalSection">
                        <div class="hr-section-title">
                            <i class="bi bi-hospital"></i>
                            ข้อมูลสถานพยาบาล
                        </div>

                        <label class="form-label fw-semibold mt-2">
                            สถานพยาบาลที่เข้ารับการรักษาเบื้องต้น
                            <span class="text-danger" id="hospitalRequiredMark">*</span>
                        </label>

                        <input type="text"
                               id="primaryHospital"
                               name="primary_hospital"
                               value="{{ $hospitalValue }}"
                               maxlength="255"
                               class="form-control @error('primary_hospital') is-invalid @enderror"
                               placeholder="กรอกชื่อสถานพยาบาล">

                        <div class="form-text" id="hospitalHelp">
                            กรุณาเลือกสถานะสิทธิเพื่อกำหนดข้อมูลสถานพยาบาล
                        </div>

                        @error('primary_hospital')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">ชื่อ - สกุล ผู้บันทึกข้อมูล</label>
                        <input type="text"
                               class="form-control bg-light"
                               value="{{ $recorderName !== '' ? $recorderName : 'ผู้ใช้งานระบบ' }}"
                               readonly
                               aria-readonly="true">
                        <div class="form-text">
                            ระบบระบุจากบัญชีผู้ใช้งานที่บันทึกข้อมูล
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">
                    @if($isEditing)
                        <a href="{{ route('healthcare_rights.index', $client->id) }}"
                           class="btn btn-light border"
                           data-permission-action="navigation">
                            ยกเลิก
                        </a>
                    @endif

                    <button type="submit"
                            class="btn btn-primary px-4"
                            id="healthcareRightSubmit">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ $isEditing ? 'บันทึกการแก้ไข' : 'บันทึกข้อมูล' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="card border-0 shadow-sm hr-card">
        <div class="card-header bg-white border-0 p-4 pb-2">
            <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                <div>
                    <h5 class="mb-1 fw-bold">
                        <i class="bi bi-clock-history me-1"></i>
                        ประวัติสิทธิรักษาพยาบาล
                    </h5>
                    <div class="text-muted small">
                        เรียงจากวันที่บันทึกล่าสุด
                    </div>
                </div>
                <span class="badge text-bg-light border px-3 py-2">
                    {{ $rights->count() }} รายการ
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            @if($rights->isEmpty())
                <div class="hr-empty">
                    <div class="hr-empty-icon"><i class="bi bi-shield-plus"></i></div>
                    <div class="fw-bold mb-1">ยังไม่มีข้อมูลสิทธิรักษาพยาบาล</div>
                    <div class="text-muted small">เมื่อบันทึกข้อมูลแล้ว ประวัติจะมาแสดงในส่วนนี้</div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0 hr-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">ลำดับ</th>
                                <th style="width: 145px;">วันที่บันทึก</th>
                                <th style="width: 220px;">สถานะสิทธิ</th>
                                <th>สถานพยาบาลเบื้องต้น</th>
                                <th style="width: 190px;">ผู้บันทึก</th>
                                <th style="width: 120px;" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rights as $index => $right)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-nowrap">
                                        {{ ThaiDateHelper::formatThaiShort($right->record_date) }}
                                    </td>
                                    <td>
                                        <span class="hr-status-badge">
                                            {{ $right->coverage_status }}
                                        </span>
                                    </td>
                                    <td>{{ $right->primary_hospital ?: '-' }}</td>
                                    <td>{{ $right->recorder_name ?: '-' }}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('healthcare_rights.edit', $right->id) }}"
                                               class="btn btn-sm btn-warning"
                                               title="แก้ไข"
                                               data-permission-action="update">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <form action="{{ route('healthcare_rights.destroy', $right->id) }}"
                                                  method="POST"
                                                  class="d-inline healthcare-right-delete-form"
                                                  data-permission-action="delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        title="ลบ">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .healthcare-rights-page {
        --hr-primary: #1d4ed8;
        --hr-primary-soft: #eff6ff;
        --hr-border: #e2e8f0;
        --hr-text: #0f172a;
        --hr-muted: #64748b;
        color: var(--hr-text);
    }

    .healthcare-rights-page .hr-hero,
    .healthcare-rights-page .hr-current,
    .healthcare-rights-page .hr-card {
        border: 1px solid var(--hr-border) !important;
        border-radius: 18px;
    }

    .healthcare-rights-page .hr-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.25rem 1.35rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 6px 18px rgba(15, 23, 42, .05);
    }

    .healthcare-rights-page .hr-icon {
        display: grid;
        width: 50px;
        height: 50px;
        place-items: center;
        border-radius: 14px;
        background: var(--hr-primary-soft);
        color: var(--hr-primary);
        font-size: 1.35rem;
    }

    .healthcare-rights-page .hr-chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .42rem .7rem;
        border: 1px solid #dbe4f0;
        border-radius: 999px;
        background: #fff;
        color: #334155;
        font-size: .8rem;
    }

    .healthcare-rights-page .hr-action-btn {
        min-height: 40px;
        border-radius: 10px;
        font-weight: 600;
    }

    .healthcare-rights-page .hr-current {
        padding: 1rem 1.15rem;
        background: #f8fafc;
    }

    .healthcare-rights-page .hr-current-label {
        color: var(--hr-muted);
        font-size: .78rem;
        font-weight: 600;
    }

    .healthcare-rights-page .hr-current-status {
        margin-top: .18rem;
        color: #0f3f8c;
        font-size: 1.05rem;
        font-weight: 800;
    }

    .healthcare-rights-page .hr-current-meta {
        margin-top: .15rem;
        color: var(--hr-muted);
        font-size: .82rem;
    }

    .healthcare-rights-page .hr-card {
        overflow: hidden;
    }

    .healthcare-rights-page .form-control {
        min-height: 44px;
        border-radius: 10px;
    }

    .healthcare-rights-page .form-control:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .10);
    }

    .healthcare-rights-page .hr-radio-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .65rem;
    }

    .healthcare-rights-page .hr-radio-option {
        display: flex;
        min-height: 46px;
        align-items: center;
        gap: .55rem;
        padding: .7rem .8rem;
        border: 1px solid #dbe4ee;
        border-radius: 11px;
        background: #fff;
        cursor: pointer;
        transition: .16s ease;
    }

    .healthcare-rights-page .hr-radio-option:hover {
        border-color: #93c5fd;
        background: #f8fbff;
    }

    .healthcare-rights-page .hr-radio-option input {
        width: 17px;
        height: 17px;
        accent-color: var(--hr-primary);
    }

    .healthcare-rights-page .hr-radio-option:has(input:checked) {
        border-color: #60a5fa;
        background: #eff6ff;
        color: #1e3a8a;
        font-weight: 700;
    }

    .healthcare-rights-page .hr-invalid .hr-radio-option {
        border-color: #dc3545;
    }

    .healthcare-rights-page .hr-section-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-top: .25rem;
        padding-bottom: .55rem;
        border-bottom: 1px solid #edf2f7;
        color: #1e3a8a;
        font-weight: 800;
    }

    .healthcare-rights-page .hr-table thead th {
        border-top: 0;
        border-bottom: 1px solid #dfe7f0;
        background: #f8fafc;
        color: #475569;
        font-size: .8rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .healthcare-rights-page .hr-table td {
        border-color: #edf2f7;
        color: #334155;
        font-size: .86rem;
    }

    .healthcare-rights-page .hr-status-badge {
        display: inline-flex;
        padding: .38rem .65rem;
        border-radius: 999px;
        background: #eff6ff;
        color: #1e40af;
        font-size: .78rem;
        font-weight: 700;
    }

    .healthcare-rights-page .hr-empty {
        padding: 3rem 1rem;
        text-align: center;
    }

    .healthcare-rights-page .hr-empty-icon {
        display: grid;
        width: 58px;
        height: 58px;
        margin: 0 auto .75rem;
        place-items: center;
        border-radius: 16px;
        background: #f1f5f9;
        color: #64748b;
        font-size: 1.55rem;
    }

    @media (max-width: 991.98px) {
        .healthcare-rights-page .hr-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .healthcare-rights-page .hr-radio-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .healthcare-rights-page .hr-radio-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('healthcareRightForm');
    const hospitalSection = document.getElementById('hospitalSection');
    const hospitalInput = document.getElementById('primaryHospital');
    const hospitalHelp = document.getElementById('hospitalHelp');
    const requiredMark = document.getElementById('hospitalRequiredMark');

    const AUTO_TEXT = @json(HealthcareRight::GOVERNMENT_HOSPITAL_TEXT);
    const STATUS_DISABLED = @json(HealthcareRight::STATUS_DISABLED);
    const STATUS_CIVIL = @json(HealthcareRight::STATUS_CIVIL_SERVANT);
    const STATUS_GOLD = @json(HealthcareRight::STATUS_GOLD_CARD);
    const STATUS_SOCIAL = @json(HealthcareRight::STATUS_SOCIAL_SECURITY);
    const STATUS_NONE = @json(HealthcareRight::STATUS_UNREGISTERED);

    function selectedStatus() {
        return document.querySelector('input[name="coverage_status"]:checked')?.value || '';
    }

    function syncHospital(clearWhenNeeded) {
        if (!hospitalSection || !hospitalInput) return;

        const status = selectedStatus();
        const isAuto = status === STATUS_DISABLED || status === STATUS_CIVIL;
        const isManual = status === STATUS_GOLD || status === STATUS_SOCIAL;
        const isNone = status === STATUS_NONE;

        hospitalSection.classList.toggle('d-none', isNone);

        if (isAuto) {
            hospitalInput.value = AUTO_TEXT;
            hospitalInput.readOnly = true;
            hospitalInput.required = false;
            hospitalInput.classList.add('bg-light');
            requiredMark?.classList.add('d-none');
            if (hospitalHelp) {
                hospitalHelp.textContent = 'ระบบกำหนดให้อัตโนมัติ และไม่สามารถแก้ไขข้อความนี้ได้';
            }
            return;
        }

        hospitalInput.readOnly = false;
        hospitalInput.classList.remove('bg-light');

        if (isManual) {
            hospitalInput.required = true;
            requiredMark?.classList.remove('d-none');
            if (clearWhenNeeded && hospitalInput.value === AUTO_TEXT) {
                hospitalInput.value = '';
            }
            if (hospitalHelp) {
                hospitalHelp.textContent = 'กรุณาระบุสถานพยาบาลตามสิทธิของผู้รับบริการ';
            }
            return;
        }

        hospitalInput.required = false;
        requiredMark?.classList.add('d-none');

        if (clearWhenNeeded && (isNone || status === '')) {
            hospitalInput.value = '';
        }

        if (hospitalHelp) {
            hospitalHelp.textContent = 'กรุณาเลือกสถานะสิทธิเพื่อกำหนดข้อมูลสถานพยาบาล';
        }
    }

    document.querySelectorAll('input[name="coverage_status"]').forEach(function (input) {
        input.addEventListener('change', function () {
            syncHospital(true);
        });
    });

    syncHospital(false);

    if (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            if (form.dataset.confirmed === 'true') {
                const submitButton = document.getElementById('healthcareRightSubmit');
                if (submitButton) submitButton.disabled = true;
                return;
            }

            if (typeof Swal === 'undefined') {
                return;
            }

            event.preventDefault();

            Swal.fire({
                title: @json($isEditing ? 'ยืนยันการแก้ไขข้อมูล' : 'ยืนยันการบันทึกข้อมูล'),
                text: 'กรุณาตรวจสอบข้อมูลก่อนยืนยัน',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.dataset.confirmed = 'true';
                    form.requestSubmit();
                }
            });
        });
    }

    document.querySelectorAll('.healthcare-right-delete-form').forEach(function (deleteForm) {
        deleteForm.addEventListener('submit', function (event) {
            if (deleteForm.dataset.confirmed === 'true' || typeof Swal === 'undefined') {
                return;
            }

            event.preventDefault();

            Swal.fire({
                title: 'ยืนยันการลบข้อมูล',
                text: 'ข้อมูลที่ลบแล้วจะไม่สามารถกู้คืนได้',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonText: 'ยกเลิก',
                confirmButtonText: 'ลบข้อมูล',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    deleteForm.dataset.confirmed = 'true';
                    deleteForm.submit();
                }
            });
        });
    });
});
</script>

@if($errors->any())
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'กรุณาตรวจสอบข้อมูล',
            html: @json(implode('<br>', $errors->all())),
            icon: 'error',
            confirmButtonText: 'ตกลง'
        });
    }
});
</script>
@endif
@endpush
