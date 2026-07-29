@extends('admin.admin_master')

@section('admin')

<div class="content">
    <div class="container-fluid">

        {{-- ===================== Alert Message ===================== --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show auto-dismiss-alert"
                 role="alert">

                <div class="fw-semibold mb-1">
                    กรุณาตรวจสอบข้อมูล
                </div>

                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="ปิด">
                </button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show auto-dismiss-alert"
                 role="alert">

                {{ session('success') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="ปิด">
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show auto-dismiss-alert"
                 role="alert">

                {{ session('error') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="ปิด">
                </button>
            </div>
        @endif
        {{-- =================== End Alert Message =================== --}}

        {{-- ======================== Header ======================== --}}
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-3">

            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">
                    รายการบ้านพัก
                </h4>
            </div>

            <div class="text-end">
                <button type="button"
                        class="btn btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#add-house-modal">

                    <i class="bi bi-plus-circle me-1"></i>
                    เพิ่มรายการ
                </button>
            </div>

        </div>
        {{-- ====================== End Header ====================== --}}

        {{-- ======================== Table ======================== --}}
        <div class="row">
            <div class="col-12">

                <div class="card">

                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            ตารางบ้านพัก
                        </h5>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table id="datatable"
                                   class="table table-bordered table-striped align-middle dt-responsive nowrap w-100">

                                <thead>
                                    <tr>
                                        <th style="width: 90px;">ลำดับที่</th>
                                        <th>ชื่อบ้านพัก</th>
                                        <th>ชื่อเรียกบ้านพัก</th>
                                        <th style="width: 180px;">จัดการ</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($house as $key => $item)
                                        <tr>
                                            <td class="text-center">
                                                {{ $key + 1 }}
                                            </td>

                                            <td>
                                                {{ $item->house_name }}
                                            </td>

                                            <td>
                                                @if (!empty($item->house_alias))
                                                    {{ $item->house_alias }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="d-flex flex-wrap gap-2">

                                                    {{-- Edit Button --}}
                                                    <button type="button"
                                                            class="btn btn-success btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#edit-house-modal"
                                                            data-edit-url="{{ route('house.edit', $item->id) }}"
                                                            onclick="houseEdit(this.dataset.editUrl)">

                                                        <i class="bi bi-pencil-square me-1"></i>
                                                        แก้ไข
                                                    </button>

                                                    {{-- Delete Form --}}
                                                    <form action="{{ route('house.delete', $item->id) }}"
                                                          method="POST"
                                                          class="d-inline delete-house-form"
                                                          data-house-name="{{ $item->house_name }}">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="button"
                                                                class="btn btn-danger btn-sm btn-delete-house">

                                                            <i class="bi bi-trash me-1"></i>
                                                            ลบ
                                                        </button>
                                                    </form>

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        {{-- ====================== End Table ====================== --}}

    </div>
</div>

{{-- =========================================================
     Add House Modal
========================================================= --}}
<div class="modal fade"
     id="add-house-modal"
     tabindex="-1"
     aria-labelledby="addHouseLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('house.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addHouseLabel">
                        เพิ่มบ้านพัก
                    </h1>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="ปิด">
                    </button>
                </div>

                <div class="modal-body">

                    {{-- ชื่อบ้านพัก --}}
                    <div class="mb-3">
                        <label for="house_name" class="form-label">
                            ชื่อบ้านพัก
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="house_name"
                               id="house_name"
                               class="form-control
                                    @if (session('open_house_modal') === 'add')
                                        @error('house_name') is-invalid @enderror
                                    @endif"
                               value="{{ session('open_house_modal') === 'add'
                                    ? old('house_name')
                                    : '' }}"
                               maxlength="255"
                               required
                               autocomplete="off"
                               placeholder="กรอกชื่อบ้านพัก">

                        @if (session('open_house_modal') === 'add')
                            @error('house_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        @endif
                    </div>

                    {{-- ชื่อเรียกบ้านพัก --}}
                    <div class="mb-3">
                        <label for="house_alias" class="form-label">
                            ชื่อเรียกบ้านพัก
                            <span class="text-muted fw-normal">(ถ้ามี)</span>
                        </label>

                        <input type="text"
                               name="house_alias"
                               id="house_alias"
                               class="form-control
                                    @if (session('open_house_modal') === 'add')
                                        @error('house_alias') is-invalid @enderror
                                    @endif"
                               value="{{ session('open_house_modal') === 'add'
                                    ? old('house_alias')
                                    : '' }}"
                               maxlength="255"
                               autocomplete="off"
                               placeholder="เช่น บ้านมะปราง">

                        @if (session('open_house_modal') === 'add')
                            @error('house_alias')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        @endif

                        <div class="form-text">
                            เว้นว่างได้ แต่หากกรอกแล้วชื่อเรียกบ้านพักต้องไม่ซ้ำกัน
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        ยกเลิก
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>
                        บันทึก
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
{{-- ===================== End Add House Modal ===================== --}}

{{-- =========================================================
     Edit House Modal
========================================================= --}}
<div class="modal fade"
     id="edit-house-modal"
     tabindex="-1"
     aria-labelledby="editHouseLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('house.update') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editHouseLabel">
                        แก้ไขบ้านพัก
                    </h1>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="ปิด">
                    </button>
                </div>

                <div class="modal-body">

                    {{-- House ID --}}
                    <input type="hidden"
                           name="house_id"
                           id="house_id"
                           value="{{ old('house_id') }}">

                    {{-- ชื่อบ้านพัก --}}
                    <div class="mb-3">
                        <label for="edit_house_name" class="form-label">
                            ชื่อบ้านพัก
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="house_name"
                               id="edit_house_name"
                               class="form-control
                                    @if (session('open_house_modal') === 'edit')
                                        @error('house_name') is-invalid @enderror
                                    @endif"
                               value="{{ session('open_house_modal') === 'edit'
                                    ? old('house_name')
                                    : '' }}"
                               maxlength="255"
                               required
                               autocomplete="off"
                               placeholder="กรอกชื่อบ้านพัก">

                        @if (session('open_house_modal') === 'edit')
                            @error('house_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        @endif
                    </div>

                    {{-- ชื่อเรียกบ้านพัก --}}
                    <div class="mb-3">
                        <label for="edit_house_alias" class="form-label">
                            ชื่อเรียกบ้านพัก
                            <span class="text-muted fw-normal">(ถ้ามี)</span>
                        </label>

                        <input type="text"
                               name="house_alias"
                               id="edit_house_alias"
                               class="form-control
                                    @if (session('open_house_modal') === 'edit')
                                        @error('house_alias') is-invalid @enderror
                                    @endif"
                               value="{{ session('open_house_modal') === 'edit'
                                    ? old('house_alias')
                                    : '' }}"
                               maxlength="255"
                               autocomplete="off"
                               placeholder="เช่น บ้านมะปราง">

                        @if (session('open_house_modal') === 'edit')
                            @error('house_alias')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        @endif

                        <div class="form-text">
                            เว้นว่างได้ แต่หากกรอกแล้วชื่อเรียกบ้านพักต้องไม่ซ้ำกัน
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        ยกเลิก
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>
                        อัปเดต
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
{{-- ==================== End Edit House Modal ==================== --}}

<script>
    /**
     * เรียกข้อมูลบ้านพักมาแสดงใน Modal แก้ไข
     */
    function houseEdit(editUrl) {
        const houseIdInput = document.getElementById('house_id');
        const houseNameInput = document.getElementById('edit_house_name');
        const houseAliasInput = document.getElementById('edit_house_alias');

        houseIdInput.value = '';
        houseNameInput.value = '';
        houseAliasInput.value = '';

        fetch(editUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('ไม่สามารถเรียกข้อมูลบ้านพักได้');
            }

            return response.json();
        })
        .then(function (data) {
            houseIdInput.value = data.id ?? '';
            houseNameInput.value = data.house_name ?? '';
            houseAliasInput.value = data.house_alias ?? '';

            setTimeout(function () {
                houseNameInput.focus();
            }, 300);
        })
        .catch(function (error) {
            console.error(error);

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถเรียกข้อมูลบ้านพักได้ กรุณาลองใหม่อีกครั้ง',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                window.alert(
                    'ไม่สามารถเรียกข้อมูลบ้านพักได้ กรุณาลองใหม่อีกครั้ง'
                );
            }
        });
    }

    /**
     * ใช้ Event Delegation เพื่อรองรับ DataTables
     * แม้ตารางจะมีการค้นหา เปลี่ยนหน้า หรือวาดตารางใหม่
     */
    document.addEventListener('click', function (event) {
        const deleteButton = event.target.closest('.btn-delete-house');

        if (!deleteButton) {
            return;
        }

        const deleteForm = deleteButton.closest('.delete-house-form');

        if (!deleteForm) {
            return;
        }

        /*
         * ป้องกันการกดปุ่มซ้ำระหว่างรอผล
         */
        if (deleteButton.disabled) {
            return;
        }

        /*
         * กรณี SweetAlert2 ยังไม่ถูกโหลด
         */
        if (typeof Swal === 'undefined') {
            const confirmed = window.confirm(
                'ลบข้อมูลนี้ใช่หรือไม่ ?'
            );

            if (confirmed) {
                deleteButton.disabled = true;
                deleteForm.submit();
            }

            return;
        }

        /*
         * SweetAlert2 รูปแบบเดียวกับหน้าตัวอย่าง
         */
        Swal.fire({
            title: 'ท่านแน่ใจ ?',
            text: 'ลบข้อมูลนี้ใช่หรือไม่ ?',
            icon: 'warning',
            showCancelButton: true,

            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',

            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',

            reverseButtons: false,
            focusConfirm: true,
            focusCancel: false,

            allowOutsideClick: false,
            allowEscapeKey: true
        }).then(function (result) {
            if (result.isConfirmed) {
                deleteButton.disabled = true;
                deleteForm.submit();
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {

        /**
         * ซ่อน Alert อัตโนมัติภายใน 4 วินาที
         */
        setTimeout(function () {
            document
                .querySelectorAll('.auto-dismiss-alert')
                .forEach(function (alertBox) {

                    alertBox.style.transition = 'opacity 0.5s ease';
                    alertBox.style.opacity = '0';

                    setTimeout(function () {
                        alertBox.remove();
                    }, 500);
                });
        }, 4000);

        /**
         * เปิด Modal เดิมอัตโนมัติเมื่อ Validation ไม่ผ่าน
         */
        @if (session('open_house_modal'))
            const modalType = @json(session('open_house_modal'));

            const modalId = modalType === 'edit'
                ? 'edit-house-modal'
                : 'add-house-modal';

            const modalElement = document.getElementById(modalId);

            if (
                modalElement &&
                typeof bootstrap !== 'undefined'
            ) {
                const houseModal =
                    bootstrap.Modal.getOrCreateInstance(modalElement);

                houseModal.show();
            }
        @endif
    });
</script>

@endsection