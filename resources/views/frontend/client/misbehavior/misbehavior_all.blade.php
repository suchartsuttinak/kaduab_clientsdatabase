@extends('admin.admin_master')
@section('admin')

<div class="content">
    <div class="container-fluid">

        <!-- Alert Message -->
        @if ($errors->any())
            <div class="alert alert-danger" id="alert-message">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success" id="alert-message">
                {{ session('success') }}
            </div>
        @endif

        <script>
            // ให้ข้อความหายไปเองหลัง 4 วินาที
            setTimeout(function() {
                let alertBox = document.getElementById('alert-message');
                if (alertBox) {
                    alertBox.style.transition = "opacity 0.5s ease";
                    alertBox.style.opacity = 0;
                    setTimeout(() => alertBox.remove(), 500);
                }
            }, 4000);
        </script>
        <!--End Alert Message -->

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">รายการพฤติกรรม</h4>
            </div>

            <!-- Modal Add Subject Button -->
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <button type="button" class="btn btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#add-misbehavior-modal">
                        เพิ่มรายการ
                    </button>
                </ol>
            </div>
            <!--End Modal Add Subject Button -->
        </div>

        <!-- Datatables Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h5 class="card-title mb-0">ตารางรายการพฤติกรรม</h5>
                    </div>

                    <!-- Subject Table -->
                    <div class="card-body">
                        <x-stable-table-controls target="datatable" />
                        <table id="datatable" class="table table-bordered dt-responsive nowrap" data-stable-table data-page-length="10">
                            <thead>
                                <tr>
                                    <th>ลำดับที่</th>
                                    <th>ชื่อพฤติกรรม</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($misbehavior as $key => $item)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $item->misbehavior_name }}</td>
                                        <td>
                                            <!-- Edit Button -->
                                            <button type="button" class="btn btn-success btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#edit-misbehavior-modal"
                                                id="{{ $item->id }}"
                                                onclick="misbehaviorEdit(this.id)">
                                                แก้ไข
                                            </button>

                                            <!-- Delete Button -->
                                            <a href="{{ route('misbehavior.delete', $item->id) }}"
                                               class="btn btn-danger btn-sm" id="delete">ลบ</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <x-stable-table-footer target="datatable" :total="$misbehavior->count()" />
                    </div>
                    <!--End Subject Table -->

                </div>
            </div>
        </div>
    </div> <!-- container-fluid -->
</div> <!-- content -->

<!-- Form Create Subject Modal -->
<div class="modal fade" id="add-misbehavior-modal" tabindex="-1" aria-labelledby="addMisbehaviorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addMisbehaviorLabel">เพิ่มวิชาเรียน</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('misbehavior.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="misbehavior_name" class="form-label">ชื่อวิชาเรียน</label>
                        <input type="text" name="misbehavior_name" id="misbehavior_name"
                            class="form-control @error('misbehavior_name') is-invalid @enderror"
                            value="{{ old('misbehavior_name') }}">
                        @error('misbehavior_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--End Form Create Subject Modal -->

<!-- Edit Subject Modal -->
<div class="modal fade" id="edit-misbehavior-modal" tabindex="-1" aria-labelledby="editMisbehaviorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editMisbehaviorLabel">แก้ไขวิชาเรียน</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('misbehavior.update') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- hidden id -->
                    <input type="hidden" name="mis_id" id="mis_id">

                    <div class="mb-3">
                        <label for="edit_misbehavior_name" class="form-label">ชื่อวิชาเรียน</label>
                        <input type="text" class="form-control" id="edit_misbehavior_name" name="misbehavior_name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">อัปเดต</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--End Edit Subject Modal -->

<script type="text/javascript">
    function misbehaviorEdit(id){
        $.ajax({
            url: "/edit/misbehavior/" + id,
            type:"GET",
            dataType:"json",
            success:function(data){
                $('#edit_misbehavior_name').val(data.misbehavior_name);
                $('#mis_id').val(data.id);
            }
        });
    }
</script>

@endsection