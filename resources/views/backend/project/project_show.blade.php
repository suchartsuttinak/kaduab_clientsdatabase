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


        @if (session('success'))

            <div class="alert alert-success" id="alert-message">

                {{ session('success') }}

            </div>

        @endif


        <script>

            // ให้ข้อความหายไปเองหลัง 4 วินาที
            setTimeout(function () {

                let alertBox =
                    document.getElementById('alert-message');

                if (alertBox) {

                    alertBox.style.transition =
                        "opacity 0.5s ease";

                    alertBox.style.opacity = 0;

                    setTimeout(() => alertBox.remove(), 500);
                }

            }, 4000);

        </script>
        {{-- End Alert Message --}}



        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">

            <div class="flex-grow-1">

                <h4 class="fs-18 fw-semibold m-0">
                    รายการโครงการ
                </h4>

            </div>


            {{-- Modal Add Project Button --}}
            <div class="text-end">

                <ol class="breadcrumb m-0 py-0">

                    <button type="button"
                            class="btn btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#add-project-modal">

                        เพิ่มรายการ

                    </button>

                </ol>

            </div>
            {{-- End Modal Add Project Button --}}

        </div>



        {{-- Datatables Table --}}
        <div class="row">

            <div class="col-12">

                <div class="card">

                    <div class="card-header">

                        <h5 class="card-title mb-0">
                            ตารางรายการโครงการ
                        </h5>

                    </div>


                    {{-- Project Table --}}
                    <div class="card-body">

                        <table id="datatable"
                               class="table table-bordered dt-responsive nowrap">

                            <thead>

                                <tr>

                                    <th>ลำดับที่</th>

                                    <th>ชื่อโครงการ</th>

                                    <th>Action</th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach ($project as $key => $item)

                                    <tr>

                                        <td>
                                            {{ $key + 1 }}
                                        </td>

                                        <td>
                                            {{ $item->project_name }}
                                        </td>

                                        <td>

                                            {{-- Edit Button --}}
                                            <button type="button"
                                                    class="btn btn-success btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#edit-project-modal"
                                                    id="{{ $item->id }}"
                                                    onclick="projectEdit(this.id)">

                                                แก้ไข

                                            </button>


                                            {{-- Delete Button --}}
                                         <form action="{{ route('project.delete', $item->id) }}"
                                                method="POST"
                                                class="d-inline delete-project-form">

                                                @csrf
                                                @method('DELETE')

                                                <button type="button"
                                                        class="btn btn-danger btn-sm delete-project"
                                                        data-project-name="{{ $item->project_name }}">
                                                    ลบ
                                                </button>

                                            </form>
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>
                    {{-- End Project Table --}}

                </div>

            </div>

        </div>

    </div>
    {{-- container-fluid --}}

</div>
{{-- content --}}



{{-- ================================================================
    Form Create Project Modal
================================================================= --}}

<div class="modal fade"
     id="add-project-modal"
     tabindex="-1"
     aria-labelledby="addProjectLabel"
     aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h1 class="modal-title fs-5"
                    id="addProjectLabel">

                    เพิ่มโครงการ

                </h1>


                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>


            <form action="{{ route('project.store') }}"
                  method="POST">

                @csrf


                <div class="modal-body">

                    <div class="mb-3">

                        <label for="project_name"
                               class="form-label">

                            ชื่อโครงการ

                        </label>


                        <input type="text"
                               name="project_name"
                               id="project_name"
                               class="form-control @error('project_name') is-invalid @enderror"
                               value="{{ old('project_name') }}">


                        @error('project_name')

                            <div class="invalid-feedback">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="submit"
                            class="btn btn-primary">

                        บันทึก

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

{{-- End Form Create Project Modal --}}



{{-- ================================================================
    Edit Project Modal
================================================================= --}}

<div class="modal fade"
     id="edit-project-modal"
     tabindex="-1"
     aria-labelledby="editProjectLabel"
     aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h1 class="modal-title fs-5"
                    id="editProjectLabel">

                    แก้ไขโครงการ

                </h1>


                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>


            <form action="{{ route('project.update') }}"
                  method="POST">

                @csrf


                <div class="modal-body">

                    {{-- hidden id --}}
                    <input type="hidden"
                           name="project_id"
                           id="project_id">


                    <div class="mb-3">

                        <label for="edit_project_name"
                               class="form-label">

                            ชื่อโครงการ

                        </label>


                        <input type="text"
                               class="form-control"
                               id="edit_project_name"
                               name="project_name">

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="submit"
                            class="btn btn-primary">

                        อัปเดต

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

{{-- End Edit Project Modal --}}



<script type="text/javascript">

    function projectEdit(id) {

        $.ajax({

            url: "/edit/project/" + id,

            type: "GET",

            dataType: "json",

            success: function (data) {

                $('#edit_project_name')
                    .val(data.project_name);

                $('#project_id')
                    .val(data.id);

            }

        });

    }

</script>


<script type="text/javascript">

    function projectEdit(id) {

        $.ajax({

            url: "/edit/project/" + id,
            type: "GET",
            dataType: "json",

            success: function(data) {

                $('#edit_project_name').val(data.project_name);
                $('#project_id').val(data.id);

            }

        });

    }


    // ============================================================
    // SweetAlert ยืนยันการลบโครงการ
    // ============================================================
    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.delete-project').forEach(function (button) {

            button.addEventListener('click', function () {

                const form = this.closest('.delete-project-form');
                const projectName = this.dataset.projectName || '';

                Swal.fire({

                    title: 'คุณแน่ใจหรือไม่?',

                    text: projectName
                        ? 'ต้องการลบโครงการ "' + projectName + '" หรือไม่?'
                        : 'ต้องการลบข้อมูลนี้หรือไม่?',

                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonText: 'ใช่, ลบข้อมูล',

                    cancelButtonText: 'ยกเลิก',

                    reverseButtons: true

                }).then((result) => {

                    if (result.isConfirmed) {

                        form.submit();

                    }

                });

            });

        });

    });

</script>
@endsection