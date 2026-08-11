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

        {{-- Header --}}
        <div class="py-3 d-flex flex-sm-row flex-column">
            <div class="ms-sm-auto">
                <button type="button" class="btn btn-primary w-100 w-sm-auto"
                    data-bs-toggle="modal"
                    data-bs-target="#add-outside-modal">
                    เพิ่มรายการ
                </button>
            </div>
        </div>

        <!-- Datatables Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h5 class="card-title mb-0">รายชื่อเด็กที่อยู่ภายนอก</h5>
                    </div>

                    <!-- Outside Table -->
                    <div class="card-body">
                        @if($outside->isNotEmpty())
                            <x-stable-table-controls target="datatable" />
                            <table id="datatable" class="table table-bordered dt-responsive nowrap w-100" data-stable-table data-page-length="10">
                                <thead>
                                    <tr>
                                        <th style="width: 10%; text-align: center;">ลำดับที่</th>
                                        <th>เพิ่มรายการ</th>
                                        <th style="width: 10%; text-align: center;">การจัดการ</th>


                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($outside as $key => $item)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $item->outside_name }}</td>
                                            <td>
                                                <!-- Edit Button -->
                                                <button type="button" class="btn btn-success btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#edit-outside-modal"
                                                    id="{{ $item->id }}"
                                                    onclick="outsideEdit(this.id)">
                                                    แก้ไข
                                                </button>

                                                <!-- Delete Button -->
                                                <a href="{{ route('outside.delete', $item->id) }}"
                                                   class="btn btn-danger btn-sm" id="delete">ลบ</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <x-stable-table-footer target="datatable" :total="$outside->count()" />
                        @else
                            <div class="alert alert-info mb-0">
                                ไม่มีข้อมูลในตาราง
                            </div>
                        @endif
                    </div>
                    <!--End Subject Table -->

                </div>
            </div>
        </div>
    </div> <!-- container-fluid -->
</div> <!-- content -->

<!-- Form Create Subject Modal -->
<div class="modal fade" id="add-outside-modal" tabindex="-1" aria-labelledby="addOutsideLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addOutsideLabel">เพิ่มรายการ</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('outside.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="outside_name" class="form-label">สาเหตุที่เด็กอาศัยอยู่ภายนอก</label>
                        <input type="text" name="outside_name" id="outside_name"
                            class="form-control @error('outside_name') is-invalid @enderror"
                            value="{{ old('outside_name') }}">
                        @error('outside_name')
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
<div class="modal fade" id="edit-outside-modal" tabindex="-1" aria-labelledby="editOutsideLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editOutsideLabel">แก้ไขรายการ</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('outside.update') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- hidden id -->
                    <input type="hidden" name="outside_id" id="outside_id">

                    <div class="mb-3">
                        <label for="edit_outside_name" class="form-label">สาเหตุที่เด็กอาศัยอยู่ภายนอก</label>
                        <input type="text" class="form-control" id="edit_outside_name" name="outside_name">
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
    function outsideEdit(id){
        $.ajax({
            url: "/edit/outside/" + id,
            type:"GET",
            dataType:"json",
            success:function(data){
                $('#edit_outside_name').val(data.outside_name);
                $('#outside_id').val(data.id);
            }
        });
    }
</script>

@endsection