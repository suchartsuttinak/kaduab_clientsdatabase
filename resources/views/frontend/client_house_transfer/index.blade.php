@extends('admin.admin_master')

@section('admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/assets/css/kaduab-stable-table.css') }}">
<style>
/* CLIENT_HOUSE_TRANSFER_UI_FIX_V1
   Scoped only to /client-house-transfers */
.house-transfer-page {
    --ht-text: #0f172a;
    --ht-muted: #64748b;
    --ht-border: #e2e8f0;
    --ht-soft: #f8fafc;
    --ht-primary: #2563eb;
    --ht-primary-soft: #eff6ff;
    --ht-radius: 16px;
}

.house-transfer-page .ht-shell {
    width: 100%;
    max-width: 100%;
}

.house-transfer-page .ht-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    padding: .9rem 1rem;
    margin-bottom: .85rem;
    background: #fff;
    border: 1px solid var(--ht-border);
    border-radius: var(--ht-radius);
    box-shadow: 0 4px 16px rgba(15, 23, 42, .05);
}

.house-transfer-page .ht-title-wrap {
    display: flex;
    align-items: center;
    gap: .75rem;
    min-width: 0;
}

.house-transfer-page .ht-title-icon {
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    color: #fff;
    background: linear-gradient(135deg, #2563eb, #4f7cff);
    box-shadow: 0 8px 18px rgba(37, 99, 235, .18);
    font-size: 1rem;
}

.house-transfer-page .ht-title {
    margin: 0;
    color: var(--ht-text);
    font-size: 1.05rem;
    font-weight: 800;
    line-height: 1.35;
}

.house-transfer-page .ht-subtitle {
    margin: .16rem 0 0;
    color: var(--ht-muted);
    font-size: .78rem;
    line-height: 1.45;
}

.house-transfer-page .ht-count {
    display: inline-flex;
    align-items: center;
    gap: .38rem;
    min-height: 34px;
    padding: .42rem .72rem;
    border: 1px solid #dbeafe;
    border-radius: 999px;
    background: #f8fbff;
    color: #1d4ed8;
    font-size: .78rem;
    font-weight: 700;
    white-space: nowrap;
}

.house-transfer-page .ht-card {
    overflow: hidden;
    background: #fff;
    border: 1px solid var(--ht-border);
    border-radius: var(--ht-radius);
    box-shadow: 0 4px 16px rgba(15, 23, 42, .04);
}

.house-transfer-page .ht-card-body {
    padding: .85rem 1rem 1rem;
}

.house-transfer-page .ht-alert {
    margin-bottom: .75rem;
    padding: .7rem .85rem;
    border-radius: 11px;
    font-size: .82rem;
}

.house-transfer-page .ht-toolbar-wrap {
    width: 100%;
    min-width: 0;
}

/* Important: toolbar/footer stay OUTSIDE the horizontal-scroll container. */
.house-transfer-page .ht-table-wrap {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    border: 1px solid var(--ht-border);
    border-radius: 12px;
    background: #fff;
}

.house-transfer-page .ht-table {
    width: 100% !important;
    min-width: 920px;
    margin: 0 !important;
    border-collapse: separate;
    border-spacing: 0;
}

.house-transfer-page .ht-table thead th {
    padding: .68rem .72rem;
    background: #f7f9fc;
    color: #334155;
    border-top: 0;
    border-bottom: 1px solid var(--ht-border);
    font-size: .79rem;
    font-weight: 700;
    line-height: 1.35;
    white-space: nowrap;
    vertical-align: middle;
}

.house-transfer-page .ht-table tbody td {
    padding: .72rem;
    color: #273449;
    border-color: #eef2f7;
    font-size: .84rem;
    font-weight: 400;
    line-height: 1.45;
    vertical-align: middle;
}

.house-transfer-page .ht-table tbody tr:hover {
    background: #fbfdff;
}

.house-transfer-page .ht-name {
    color: var(--ht-text);
    font-weight: 700;
    line-height: 1.35;
}

.house-transfer-page .ht-register {
    margin-top: .15rem;
    color: #94a3b8;
    font-size: .74rem;
}

.house-transfer-page .ht-house-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .38rem .68rem;
    border-radius: 999px;
    border: 1px solid #dbeafe;
    background: var(--ht-primary-soft);
    color: #1d4ed8;
    font-size: .75rem;
    font-weight: 700;
    white-space: nowrap;
}

.house-transfer-page .ht-house-badge.is-empty {
    color: #64748b;
    background: #f8fafc;
    border-color: #e2e8f0;
}

.house-transfer-page .ht-edit-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    min-height: 34px;
    padding: .4rem .72rem;
    border: 1px solid #bfdbfe;
    border-radius: 10px;
    background: #fff;
    color: #2563eb;
    font-size: .78rem;
    font-weight: 700;
    white-space: nowrap;
    transition: .15s ease;
}

.house-transfer-page .ht-edit-btn:hover,
.house-transfer-page .ht-edit-btn:focus {
    color: #1d4ed8;
    background: #eff6ff;
    border-color: #93c5fd;
    transform: translateY(-1px);
}

.house-transfer-page .kst-toolbar {
    margin: 0 0 .7rem !important;
}

.house-transfer-page .kst-footer {
    margin: .7rem 0 0 !important;
}

.house-transfer-page .kst-search input {
    width: min(270px, 38vw);
}

.house-transfer-page .ht-empty {
    padding: 2rem !important;
    color: #94a3b8 !important;
    text-align: center !important;
    background: #fff !important;
}

/* Modal */
#editHouseModal .modal-dialog {
    max-width: 620px;
}

#editHouseModal .modal-content {
    overflow: hidden;
    border: 0;
    border-radius: 16px;
    box-shadow: 0 24px 70px rgba(15, 23, 42, .22);
}

#editHouseModal .modal-header {
    padding: .95rem 1rem .65rem;
    border-bottom: 1px solid #eef2f7;
}

#editHouseModal .modal-title {
    color: #0f172a;
    font-size: 1rem;
    font-weight: 800;
}

#editHouseModal .ht-modal-subtitle {
    margin-top: .15rem;
    color: #64748b;
    font-size: .76rem;
}

#editHouseModal .modal-body {
    max-height: min(66vh, 620px);
    overflow-y: auto;
    padding: 1rem;
}

#editHouseModal .modal-footer {
    padding: .75rem 1rem 1rem;
    border-top: 1px solid #eef2f7;
}

#editHouseModal .form-label {
    margin-bottom: .35rem;
    color: #334155;
    font-size: .8rem;
    font-weight: 700;
}

#editHouseModal .form-control,
#editHouseModal .form-select {
    min-height: 40px;
    border-color: #dbe3ee;
    border-radius: 10px;
    box-shadow: none;
    font-size: .84rem;
}

#editHouseModal textarea.form-control {
    min-height: 90px;
    resize: vertical;
}

#editHouseModal .form-control:focus,
#editHouseModal .form-select:focus {
    border-color: #93c5fd;
    box-shadow: 0 0 0 .18rem rgba(37, 99, 235, .10);
}

#editHouseModal .ht-modal-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    min-height: 38px;
    padding: .5rem .9rem;
    border-radius: 10px;
    font-size: .8rem;
    font-weight: 700;
}

@media (max-width: 767.98px) {
    .house-transfer-page .ht-header {
        align-items: flex-start;
        padding: .8rem;
    }

    .house-transfer-page .ht-count {
        width: 100%;
        justify-content: center;
    }

    .house-transfer-page .ht-card-body {
        padding: .75rem;
    }

    .house-transfer-page .ht-table {
        min-width: 820px;
    }

    .house-transfer-page .kst-search input {
        width: 100%;
        max-width: none;
    }

    #editHouseModal .modal-dialog {
        max-width: none;
    }
}
</style>
@endpush

<div class="page-content house-transfer-page">
    <div class="ht-shell">

        <header class="ht-header">
            <div class="ht-title-wrap">
                <div class="ht-title-icon" aria-hidden="true">
                    <i class="bi bi-house-door"></i>
                </div>

                <div>
                    <h1 class="ht-title">จัดการย้ายบ้านเด็ก</h1>
                    <p class="ht-subtitle">
                        เปลี่ยนบ้านปัจจุบันของเด็ก โดยระบบจะอัปเดตข้อมูลเด็กและบันทึกประวัติการย้ายบ้านตามกระบวนการเดิม
                    </p>
                </div>
            </div>

            <div class="ht-count">
                <i class="bi bi-people"></i>
                <span>ทั้งหมด {{ number_format($clients->count()) }} รายการ</span>
            </div>
        </header>

        <section class="ht-card">
            <div class="ht-card-body">

                @if(session('success'))
                    <div class="alert alert-success ht-alert">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info ht-alert">
                        <i class="bi bi-info-circle me-1"></i>
                        {{ session('info') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger ht-alert">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger ht-alert">
                        <div class="fw-semibold mb-1">กรุณาตรวจสอบข้อมูล</div>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="ht-toolbar-wrap">
                    <x-stable-table-controls target="houseTransferTable" :page-length="25" />
                </div>

                <div class="ht-table-wrap">
                    <table
                        id="houseTransferTable"
                        class="table align-middle ht-table"
                        data-stable-table
                        data-page-length="25"
                    >
                        <thead>
                            <tr>
                                <th width="60">#</th>
                                <th>ชื่อ-สกุลเด็ก</th>
                                <th>บ้านปัจจุบัน</th>
                                <th>หน่วยงาน</th>
                                <th>ผู้ดูแลบ้าน</th>
                                <th width="130" class="text-center" data-kst-nosort data-kst-nosearch>จัดการ</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($clients as $index => $client)
                                @php
                                    $currentHouseId = $client->house_id;
                                    $caregiverName = $caregivers[$currentHouseId] ?? '-';
                                    $fullname = $client->fullname
                                        ?? trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
                                @endphp

                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>

                                    <td>
                                        <div class="ht-name">{{ $fullname ?: '-' }}</div>
                                        <div class="ht-register">
                                            เลขทะเบียน: {{ $client->register_number ?? '-' }}
                                        </div>
                                    </td>

                                    <td>
                                        @if($client->house)
                                            <span class="ht-house-badge">
                                                {{ $client->house->house_name ?? '-' }}
                                            </span>
                                        @else
                                            <span class="ht-house-badge is-empty">
                                                ยังไม่ระบุบ้าน
                                            </span>
                                        @endif
                                    </td>

                                    <td>{{ $client->project?->project_name ?? '-' }}</td>

                                    <td>{{ $caregiverName }}</td>

                                    <td class="text-center">
                                        <button
                                            type="button"
                                            class="ht-edit-btn btn-edit-house"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editHouseModal"
                                            data-client-id="{{ $client->id }}"
                                            data-fullname="{{ $fullname ?: '-' }}"
                                            data-project="{{ $client->project?->project_name ?? '-' }}"
                                            data-house-id="{{ $client->house_id ?? '' }}"
                                            data-caregiver="{{ $caregiverName }}"
                                            data-action="{{ route('client-house-transfers.update', $client->id) }}"
                                            data-permission-action="update"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                            <span>แก้ไข</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="ht-empty">
                                        ยังไม่มีข้อมูลเด็ก
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-stable-table-footer
                    target="houseTransferTable"
                    :total="$clients->count()"
                    :page-length="25"
                />
            </div>
        </section>
    </div>
</div>

{{-- ใช้ Modal เดียว ไม่สร้างซ้ำในตาราง --}}
<div
    class="modal fade"
    id="editHouseModal"
    tabindex="-1"
    aria-labelledby="editHouseModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <form
                method="POST"
                action="#"
                id="houseTransferForm"
                data-permission-action="update"
            >
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="editHouseModalLabel">
                            <i class="bi bi-house-gear me-1 text-primary"></i>
                            แก้ไขบ้านเด็ก
                        </h5>
                        <div class="ht-modal-subtitle">
                            เลือกบ้านใหม่แล้วระบบจะบันทึกตามกระบวนการย้ายบ้านเดิม
                        </div>
                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="ปิด"
                    ></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ชื่อ-สกุลเด็ก</label>
                        <input
                            type="text"
                            id="modalFullname"
                            class="form-control bg-light"
                            readonly
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">หน่วยงาน</label>
                        <input
                            type="text"
                            id="modalProject"
                            class="form-control bg-light"
                            readonly
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            บ้านที่ <span class="text-danger">*</span>
                        </label>

                        <select
                            name="house_id"
                            id="modalHouseId"
                            class="form-select"
                            required
                        >
                            <option value="">-- เลือกบ้าน --</option>

                            @foreach($houses as $house)
                                <option value="{{ $house->id }}">
                                    {{ $house->house_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ชื่อผู้ดูแลในบ้านนั้น</label>
                        <input
                            type="text"
                            id="modalCaregiver"
                            class="form-control bg-light"
                            readonly
                        >
                    </div>

                    <div class="mb-0">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea
                            name="remark"
                            rows="3"
                            class="form-control"
                            placeholder="ระบุหมายเหตุเพิ่มเติม ถ้ามี"
                        ></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-light ht-modal-btn"
                        data-bs-dismiss="modal"
                    >
                        <i class="bi bi-x-circle"></i>
                        <span>ยกเลิก</span>
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary ht-modal-btn"
                        id="btnSaveHouse"
                        data-permission-action="update"
                    >
                        <i class="bi bi-check-circle"></i>
                        <span>บันทึกการย้ายบ้าน</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('backend/assets/js/kaduab-stable-table.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const caregivers = @json($caregivers);

    const form = document.getElementById('houseTransferForm');
    const modalFullname = document.getElementById('modalFullname');
    const modalProject = document.getElementById('modalProject');
    const modalHouseId = document.getElementById('modalHouseId');
    const modalCaregiver = document.getElementById('modalCaregiver');
    const btnSaveHouse = document.getElementById('btnSaveHouse');

    if (window.KaduabStableTable) {
        window.KaduabStableTable.initAll(document);
    }

    document.querySelectorAll('.btn-edit-house').forEach(function (btn) {
        btn.addEventListener('click', function () {
            form.action = this.dataset.action;
            modalFullname.value = this.dataset.fullname || '-';
            modalProject.value = this.dataset.project || '-';
            modalHouseId.value = this.dataset.houseId || '';
            modalCaregiver.value = this.dataset.caregiver || '-';

            const remark = form.querySelector('textarea[name="remark"]');
            if (remark) {
                remark.value = '';
            }

            btnSaveHouse.disabled = false;

            const label = btnSaveHouse.querySelector('span');
            if (label) {
                label.textContent = 'บันทึกการย้ายบ้าน';
            }
        });
    });

    modalHouseId?.addEventListener('change', function () {
        modalCaregiver.value = caregivers[this.value] ?? '-';
    });

    form?.addEventListener('submit', function () {
        btnSaveHouse.disabled = true;

        const label = btnSaveHouse.querySelector('span');
        if (label) {
            label.textContent = 'กำลังบันทึก...';
        }
    });
});
</script>
@endpush

@endsection