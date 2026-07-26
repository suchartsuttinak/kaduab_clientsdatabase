@extends('admin.admin_master')

@section('admin')

<div class="page-content">

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h4 class="mb-1 fw-bold text-primary">
                        <i class="bi bi-house-door me-1"></i> จัดการย้ายบ้านเด็ก
                    </h4>
                    <div class="text-muted small">
                        เปลี่ยนบ้านปัจจุบันของเด็ก โดยระบบจะอัปเดตไปที่ข้อมูลเด็กโดยตรง
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success rounded-3">{{ session('success') }}</div>
            @endif

            @if(session('info'))
                <div class="alert alert-info rounded-3">{{ session('info') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger rounded-3">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger rounded-3">
                    <div class="fw-semibold mb-1">กรุณาตรวจสอบข้อมูล</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="table-responsive">
                <table id="houseTransferTable" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th width="60">#</th>
                            <th>ชื่อ-สกุลเด็ก</th>
                            <th>บ้านปัจจุบัน</th>
                            <th>หน่วยงาน</th>
                            <th>ผู้ดูแลบ้าน</th>
                            <th width="130" class="text-center">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($clients as $index => $client)
                            @php
                                $currentHouseId = $client->house_id;
                                $caregiverName = $caregivers[$currentHouseId] ?? '-';
                                $fullname = $client->fullname ?? trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
                            @endphp

                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    <div class="fw-semibold">{{ $fullname ?: '-' }}</div>
                                    <div class="text-muted small">
                                        เลขทะเบียน: {{ $client->register_number ?? '-' }}
                                    </div>
                                </td>

                                <td>
                                    @if($client->house)
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                                            {{ $client->house->house_name ?? '-' }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2">
                                            ยังไม่ระบุบ้าน
                                        </span>
                                    @endif
                                </td>

                                <td>{{ $client->project?->project_name ?? '-' }}</td>

                                <td>{{ $caregiverName }}</td>

                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-edit-house"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editHouseModal"
                                            data-client-id="{{ $client->id }}"
                                            data-fullname="{{ $fullname ?: '-' }}"
                                            data-project="{{ $client->project?->project_name ?? '-' }}"
                                            data-house-id="{{ $client->house_id ?? '' }}"
                                            data-caregiver="{{ $caregiverName }}"
                                            data-action="{{ route('client-house-transfers.update', $client->id) }}">
                                        <i class="bi bi-pencil-square me-1"></i> แก้ไข
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    ยังไม่มีข้อมูลเด็ก
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

{{-- ใช้ Modal เดียว ไม่สร้างซ้ำในตาราง --}}
<div class="modal fade"
     id="editHouseModal"
     tabindex="-1"
     aria-labelledby="editHouseModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 rounded-4 shadow">

            <form method="POST" action="#" id="houseTransferForm">
                @csrf
                @method('PUT')

                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold text-primary" id="editHouseModalLabel">
                            <i class="bi bi-house-gear me-1"></i> แก้ไขบ้านเด็ก
                        </h5>
                        <div class="text-muted small">
                            ข้อมูลนี้จะอัปเดตไปยังตาราง clients โดยตรง
                        </div>
                    </div>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">ชื่อ-สกุลเด็ก</label>
                        <input type="text"
                               id="modalFullname"
                               class="form-control rounded-3 bg-light"
                               readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">หน่วยงาน</label>
                        <input type="text"
                               id="modalProject"
                               class="form-control rounded-3 bg-light"
                               readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            บ้านที่ <span class="text-danger">*</span>
                        </label>

                        <select name="house_id"
                                id="modalHouseId"
                                class="form-select rounded-3"
                                required>
                            <option value="">-- เลือกบ้าน --</option>

                            @foreach($houses as $house)
                                <option value="{{ $house->id }}">
                                    {{ $house->house_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">ชื่อผู้ดูแลในบ้านนั้น</label>
                        <input type="text"
                               id="modalCaregiver"
                               class="form-control rounded-3 bg-light"
                               readonly>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">หมายเหตุ</label>
                        <textarea name="remark"
                                  rows="3"
                                  class="form-control rounded-3"
                                  placeholder="ระบุหมายเหตุเพิ่มเติม ถ้ามี"></textarea>
                    </div>

                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button"
                            class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">
                        ยกเลิก
                    </button>

                    <button type="submit"
                            class="btn btn-primary rounded-pill px-4"
                            id="btnSaveHouse">
                        <i class="bi bi-check-circle me-1"></i>
                        <span>บันทึกการย้ายบ้าน</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const caregivers = @json($caregivers);

    const form = document.getElementById('houseTransferForm');
    const modalFullname = document.getElementById('modalFullname');
    const modalProject = document.getElementById('modalProject');
    const modalHouseId = document.getElementById('modalHouseId');
    const modalCaregiver = document.getElementById('modalCaregiver');
    const btnSaveHouse = document.getElementById('btnSaveHouse');

    document.querySelectorAll('.btn-edit-house').forEach(function (btn) {
        btn.addEventListener('click', function () {
            form.action = this.dataset.action;

            modalFullname.value = this.dataset.fullname || '-';
            modalProject.value = this.dataset.project || '-';
            modalHouseId.value = this.dataset.houseId || '';
            modalCaregiver.value = this.dataset.caregiver || '-';

            form.querySelector('textarea[name="remark"]').value = '';

            btnSaveHouse.disabled = false;
            btnSaveHouse.querySelector('span').textContent = 'บันทึกการย้ายบ้าน';
        });
    });

    modalHouseId.addEventListener('change', function () {
        modalCaregiver.value = caregivers[this.value] ?? '-';
    });

    form.addEventListener('submit', function () {
        btnSaveHouse.disabled = true;
        btnSaveHouse.querySelector('span').textContent = 'กำลังบันทึก...';
    });

    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#houseTransferTable').DataTable({
            pageLength: 25,
            ordering: true,
            responsive: false,
            scrollX: true,
            autoWidth: false,
            language: {
                search: "ค้นหา:",
                lengthMenu: "แสดง _MENU_ รายการ",
                info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                paginate: {
                    previous: "ก่อนหน้า",
                    next: "ถัดไป"
                },
                zeroRecords: "ไม่พบข้อมูล"
            }
        });
    }
});
</script>
@endpush

@endsection