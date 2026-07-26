@extends('admin_client.admin_client')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                แบบประเมินพฤติกรรม SNAP-IV
            </h4>

            <div class="text-muted">
                รายการแบบประเมินของ {{ $client->first_name }} {{ $client->last_name }}
            </div>
        </div>

        <a href="{{ route('snap-iv.create', $client->id) }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            เพิ่มแบบประเมิน
        </a>

    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($screenings->count())

        <div class="card border-0 shadow-sm rounded-4">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>วันที่ประเมิน</th>
                            <th>ขาดสมาธิ</th>
                            <th>ซน/หุนหันพลันแล่น</th>
                            <th>ดื้อ/ต่อต้าน</th>
                            <th>คะแนนรวม</th>
                            <th>ผู้ประเมิน</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($screenings as $item)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $item->screening_date?->format('d/m/Y') }}
                                </td>

                                <td>
                                    {{ $item->inattention_score }}
                                    <div class="small text-muted">
                                        {{ $item->inattention_level }}
                                    </div>
                                </td>

                                <td>
                                    {{ $item->hyperactivity_score }}
                                    <div class="small text-muted">
                                        {{ $item->hyperactivity_level }}
                                    </div>
                                </td>

                                <td>
                                    {{ $item->oppositional_score }}
                                    <div class="small text-muted">
                                        {{ $item->oppositional_level }}
                                    </div>
                                </td>

                                <td class="fw-bold">
                                    {{ $item->total_score }}
                                </td>

                                <td>
                                    {{ $item->observer_name ?: '-' }}
                                </td>

                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('snap-iv.show', $item->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                      <form action="{{ route('snap-iv.destroy', $item->id) }}"
                                            method="POST"
                                            class="snap-delete-form">

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger">
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
        </div>

        <div class="mt-3">
            {{ $screenings->links() }}
        </div>

    @else

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-clipboard2-pulse fs-1 text-muted"></i>

                <h5 class="mt-3">
                    ยังไม่มีข้อมูลแบบประเมิน SNAP-IV
                </h5>

                <div class="text-muted">
                    กรุณาเพิ่มแบบประเมินใหม่
                </div>
            </div>
        </div>

    @endif

</div>

    <script>

    document.addEventListener('DOMContentLoaded', function () {

        const deleteForms = document.querySelectorAll('.snap-delete-form');

        deleteForms.forEach(function (form) {

            form.addEventListener('submit', function (e) {

                e.preventDefault();

                Swal.fire({
                    title: 'ยืนยันการลบ ?',
                    text: 'เมื่อลบแล้วจะไม่สามารถกู้คืนข้อมูลได้',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ใช่, ลบข้อมูล',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true

                }).then(function (result) {

                    if (result.isConfirmed) {
                        form.submit();
                    }

                });

            });

        });

    });

    </script>

@endsection