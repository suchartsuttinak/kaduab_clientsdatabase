@extends('admin_client.admin_client')

@section('content')

<style>
    .ds-index-page{
        padding:24px 0 40px;
    }

    .ds-index-header{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:16px;
        flex-wrap:wrap;
        margin-bottom:22px;
    }

    .ds-index-header-left{
        display:flex;
        align-items:flex-start;
        gap:14px;
        flex-wrap:wrap;
    }

    .ds-index-title{
        font-size:1.35rem;
        font-weight:800;
        color:#0f172a;
        margin:0 0 4px;
    }

    .ds-index-subtitle{
        color:#64748b;
        font-size:.95rem;
    }

    .ds-back-btn{
        border-radius:12px;
        padding:8px 14px;
        font-weight:600;
        border:1px solid #dbe3ef;
        background:#fff;
        white-space:nowrap;
    }

    .ds-add-btn{
        border-radius:12px;
        padding:10px 18px;
        font-weight:600;
        box-shadow:0 4px 14px rgba(220,38,38,.15);
    }

    .ds-table-card{
        border:0;
        border-radius:18px;
        overflow:hidden;
        box-shadow:0 10px 30px rgba(15,23,42,.06);
        background:#fff;
    }

    .ds-table{
        margin:0;
        min-width:880px;
    }

    .ds-table thead th{
        background:#f8fafc;
        color:#0f172a;
        font-weight:800;
        border-bottom:1px solid #e2e8f0;
        padding:14px 12px;
        white-space:nowrap;
        vertical-align:middle;
    }

    .ds-table tbody td{
        padding:16px 12px;
        vertical-align:middle;
        border-bottom:1px solid #eef2f7;
        color:#0f172a;
    }

    .ds-table tbody tr:last-child td{
        border-bottom:0;
    }

    .ds-score{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-width:44px;
        height:28px;
        padding:0 10px;
        border-radius:999px;
        font-size:.85rem;
        font-weight:800;
        color:#fff;
        background:#dc2626;
    }

    .ds-score-normal{
        background:#16a34a;
    }

    .ds-score-risk{
        background:#dc2626;
    }

    .ds-summary{
        white-space:pre-line;
        line-height:1.7;
        font-weight:600;
        min-width:260px;
    }

    .ds-action-group{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        flex-wrap:nowrap;
    }

    .ds-action-btn{
        width:34px;
        height:34px;
        padding:0;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:10px;
        line-height:1;
    }

    .ds-empty-card{
        border:0;
        border-radius:18px;
        box-shadow:0 10px 30px rgba(15,23,42,.06);
    }

    @media (max-width:768px){
        .ds-index-header{
            align-items:stretch;
        }

        .ds-index-header-left{
            width:100%;
            flex-direction:column;
            align-items:flex-start;
        }

        .ds-back-btn,
        .ds-add-btn{
            width:100%;
            justify-content:center;
        }
    }
</style>

<div class="container-fluid ds-index-page">

    <div class="ds-index-header">

        <div class="ds-index-header-left">

            <a href="{{ route('client.show') }}"
               class="btn btn-light ds-back-btn">
                <i class="bi bi-arrow-left-circle me-1"></i>
                กลับไปหน้าเคส
            </a>

            <div>
                <h4 class="ds-index-title">
                    แบบคัดกรองภาวะซึมเศร้าในวัยรุ่น
                </h4>

                <div class="ds-index-subtitle">
                    Center for Epidemiologic Studies-Depression Scale (CES-D) ฉบับภาษาไทย
                </div>
            </div>

        </div>

        <a href="{{ route('depression-screenings.create', $client->id) }}"
           class="btn btn-danger ds-add-btn">
            <i class="bi bi-plus-circle me-1"></i>
            เพิ่มแบบคัดกรอง
        </a>

    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($screenings->count())

        <div class="card ds-table-card">
            <div class="table-responsive">
                <table class="table ds-table align-middle">
                    <thead>
                        <tr>
                            <th style="width:120px;">วันที่</th>
                            <th style="width:110px;" class="text-center">คะแนนรวม</th>
                            <th>ผลการคัดกรอง</th>
                            <th style="width:160px;">ผู้ประเมิน</th>
                            <th style="width:120px;" class="text-center">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($screenings as $item)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $item->screening_date?->format('d/m/Y') }}
                                </td>

                                <td class="text-center">
                                    <span class="ds-score {{ $item->total_score >= 22 ? 'ds-score-risk' : 'ds-score-normal' }}">
                                        {{ $item->total_score }}
                                    </span>
                                </td>

                                <td>
                                    <div class="ds-summary">
                                        {{ $item->summary }}
                                    </div>
                                </td>

                                <td>
                                    {{ $item->observer_name ?: '-' }}
                                </td>

                                <td class="text-center">
                                    <div class="ds-action-group">

                                        <a href="{{ route('depression-screenings.show', $item->id) }}"
                                           class="btn btn-outline-primary ds-action-btn"
                                           title="ดูข้อมูล">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <form action="{{ route('depression-screenings.destroy', $item->id) }}"
                                              method="POST"
                                              class="depression-delete-form m-0 p-0">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-outline-danger ds-action-btn"
                                                    title="ลบข้อมูล">
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

        <div class="card ds-empty-card">
            <div class="card-body text-center py-5">
                <i class="bi bi-emoji-frown fs-1 text-muted"></i>

                <h5 class="mt-3">
                    ยังไม่มีข้อมูลแบบคัดกรอง
                </h5>

                <div class="text-muted mb-3">
                    กรุณาเพิ่มแบบคัดกรองภาวะซึมเศร้าในวัยรุ่น
                </div>
            </div>
        </div>

    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('.depression-delete-form');

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