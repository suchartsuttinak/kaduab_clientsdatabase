@extends('admin.admin_master')
@section('admin')

<div class="content">
    <div class="container-fluid">

        {{-- Alert Message --}}
        @if ($errors->any())
            <div class="alert alert-danger" id="alert-message">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('message'))
            <div class="alert alert-success" id="alert-message">
                {{ session('message') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success" id="alert-message">
                {{ session('success') }}
            </div>
        @endif

        <script>
            setTimeout(function() {
                let alertBox = document.getElementById('alert-message');
                if (alertBox) {
                    alertBox.style.transition = "opacity 0.5s ease";
                    alertBox.style.opacity = 0;
                    setTimeout(() => alertBox.remove(), 500);
                }
            }, 4000);
        </script>

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">รายการสถานะทางทะเบียน</h4>
            </div>

            <div class="text-end">
                <button type="button" class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#add-citizen-modal">
                    เพิ่มรายการ
                </button>
            </div>
        </div>

        {{-- Datatables Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h5 class="card-title mb-0">ตารางสถานะทางทะเบียน</h5>
                    </div>

                    <div class="card-body">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>ลำดับที่</th>
                                    <th>ชื่อสถานะทางทะเบียน</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($citizens as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->citizen_name }}</td>
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#edit-citizen-modal"
                                                onclick="citizenEdit({{ $item->id }})">
                                                แก้ไข
                                            </button>

                                            <a href="{{ route('citizen.delete', $item->id) }}"
                                               class="btn btn-danger btn-sm"
                                               id="delete">
                                                ลบ
                                            </a>
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
</div>

{{-- Form Create Citizen Modal --}}
<div class="modal fade" id="add-citizen-modal" tabindex="-1" aria-labelledby="addCitizenLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addCitizenLabel">เพิ่มสถานะทางทะเบียน</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('citizen.store') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="citizen_name" class="form-label">ชื่อสถานะทางทะเบียน</label>
                        <input type="text"
                            name="citizen_name"
                            id="citizen_name"
                            class="form-control @error('citizen_name') is-invalid @enderror"
                            value="{{ old('citizen_name') }}">

                        @error('citizen_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- Edit Citizen Modal --}}
<div class="modal fade" id="edit-citizen-modal" tabindex="-1" aria-labelledby="editCitizenLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editCitizenLabel">แก้ไขสถานะทางทะเบียน</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('citizen.update') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <input type="hidden" name="citizen_id" id="citizen_id">

                    <div class="mb-3">
                        <label for="edit_citizen_name" class="form-label">ชื่อสถานะทางทะเบียน</label>
                        <input type="text"
                            class="form-control @error('citizen_name') is-invalid @enderror"
                            id="edit_citizen_name"
                            name="citizen_name">

                        @error('citizen_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">อัปเดต</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script type="text/javascript">
    function citizenEdit(id) {
        $.ajax({
            url: "/edit/citizen/" + id,
            type: "GET",
            dataType: "json",
            success: function(data) {
                $('#edit_citizen_name').val(data.citizen_name);
                $('#citizen_id').val(data.id);
            },
            error: function() {
                alert('ไม่สามารถดึงข้อมูลได้ กรุณาลองใหม่อีกครั้ง');
            }
        });
    }
</script>

@endsection