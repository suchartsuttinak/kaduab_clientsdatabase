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

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">รายการดำเนินการทางทะเบียน</h4>
            </div>

            <div class="text-end">
                <button type="button" class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#add-citizenship-modal">
                    เพิ่มรายการ
                </button>
            </div>
        </div>

        {{-- Datatables Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h5 class="card-title mb-0">รายการสถานะทางทะเบียน</h5>
                    </div>

                    <div class="card-body">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>ลำดับที่</th>
                                    <th>ชื่อรายการขอมีสถานะทางทะเบียน</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($citizenships as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->citizenship_name }}</td>
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#edit-citizenship-modal"
                                                onclick="citizenshipEdit({{ $item->id }})">
                                                แก้ไข
                                            </button>

                                            <a href="{{ route('citizenship.delete', $item->id) }}"
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

{{-- Form Create Citizenship Modal --}}
<div class="modal fade" id="add-citizenship-modal" tabindex="-1" aria-labelledby="addCitizenshipLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addCitizenshipLabel">เพิ่มรายการ</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('citizenship.store') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="citizenship_name" class="form-label">ชื่อรายการทางทะเบียน</label>
                        <input type="text"
                            name="citizenship_name"
                            id="citizenship_name"
                            class="form-control @error('citizenship_name') is-invalid @enderror"
                            value="{{ old('citizenship_name') }}">

                        @error('citizenship_name')
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

{{-- Edit Citizenship Modal --}}
<div class="modal fade" id="edit-citizenship-modal" tabindex="-1" aria-labelledby="editCitizenshipLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editCitizenshipLabel">แก้ไขสถานะทางทะเบียน</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('citizenship.update') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <input type="hidden" name="citizenship_id" id="citizenship_id">

                    <div class="mb-3">
                        <label for="edit_citizenship_name" class="form-label">ชื่อรายการ</label>
                        <input type="text"
                            class="form-control @error('citizenship_name') is-invalid @enderror"
                            id="edit_citizenship_name"
                            name="citizenship_name">

                        @error('citizenship_name')
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
    setTimeout(function() {
        let alertBox = document.getElementById('alert-message');

        if (alertBox) {
            alertBox.style.transition = "opacity 0.5s ease";
            alertBox.style.opacity = 0;

            setTimeout(() => alertBox.remove(), 500);
        }
    }, 4000);

    function citizenshipEdit(id) {
        $.ajax({
            url: "/edit/citizenship/" + id,
            type: "GET",
            dataType: "json",
            success: function(data) {
                $('#edit_citizenship_name').val(data.citizenship_name);
                $('#citizenship_id').val(data.id);
            },
            error: function() {
                alert('ไม่สามารถดึงข้อมูลได้ กรุณาลองใหม่อีกครั้ง');
            }
        });
    }
</script>

@endsection