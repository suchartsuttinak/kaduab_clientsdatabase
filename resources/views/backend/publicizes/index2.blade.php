@extends('admin.admin_master')
@section('admin')

<div class="container-fluid py-4 publicize-page">

    {{-- =========================
        Header
    ========================== --}}
    <div class="publicize-hero mb-4">
        <div class="publicize-hero__left">
            <div class="publicize-hero__top">
                <a href="{{ url()->previous() }}" class="btn btn-light publicize-icon-btn" title="ย้อนกลับ">
                    <i class="bi bi-arrow-left"></i>
                </a>

                <div class="publicize-hero__title-wrap">
                    <h2 class="mb-1 fw-bold publicize-hero__title">
                        <i class="bi bi-megaphone-fill me-2 text-primary"></i>
                        ระบบประชาสัมพันธ์
                    </h2>
                    <div class="text-muted publicize-hero__subtitle">
                        จัดการเอกสารประชาสัมพันธ์ แยกตามหมวดหมู่ ค้นหาได้ตามปี และแสดงผลในรูปแบบตาราง
                    </div>
                </div>
            </div>
        </div>

        <div class="publicize-hero__actions">
            <a href="{{ url('/') }}" class="btn btn-outline-secondary publicize-head-btn">
                <i class="bi bi-house-door me-1"></i>
                <span>หน้าหลัก</span>
            </a>

            <a href="{{ route('publicizes.create') }}" class="btn btn-primary publicize-head-btn">
                <i class="bi bi-plus-circle me-1"></i>
                <span>เพิ่มข้อมูลประชาสัมพันธ์</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm publicize-alert d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="row g-4">

        {{-- =========================
            Sidebar
        ========================== --}}
        <div class="col-xl-3 col-lg-4">
            <div class="card border-0 shadow-sm publicize-sidebar">
                <div class="card-header publicize-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-folder2-open text-primary"></i>
                        <span class="fw-bold">หมวดหมู่ประกาศ</span>
                    </div>
                </div>

                <div class="publicize-filter-box">
                    <form method="GET" action="{{ route('publicizes.index') }}" class="publicize-filter-form">
                        <input type="hidden" name="category" value="{{ $activeCategory }}">

                        <label class="form-label small fw-semibold mb-2">
                            <i class="bi bi-calendar3 me-1 text-primary"></i>
                            ค้นหาตามปี พ.ศ.
                        </label>

                        <div class="publicize-filter-row">
                            <select name="year_be" class="form-select form-select-sm">
                                <option value="">ทุกปี</option>
                                @foreach($yearOptions as $year)
                                    <option value="{{ $year }}" {{ (string)$yearBe === (string)$year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit" class="btn btn-primary btn-sm px-3">
                                <i class="bi bi-search me-1"></i> ค้นหา
                            </button>
                        </div>

                        @if($yearBe)
                            <div class="mt-2">
                                <a href="{{ route('publicizes.index', ['category' => $activeCategory]) }}"
                                   class="btn btn-light btn-sm border w-100 publicize-reset-btn">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                                    ล้างตัวกรองปี
                                </a>
                            </div>
                        @endif
                    </form>
                </div>

                <div class="list-group list-group-flush publicize-category-list">
                    @foreach($categories as $key => $label)
                        <a href="{{ route('publicizes.index', array_filter([
                                'category' => $key,
                                'year_be' => $yearBe
                           ])) }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $activeCategory === $key ? 'active' : '' }}">
                            <span class="d-flex align-items-center gap-2">
                                @if($key === 'all')
                                    <i class="bi bi-collection"></i>
                                @elseif($key === 'news')
                                    <i class="bi bi-newspaper"></i>
                                @elseif($key === 'activity')
                                    <i class="bi bi-calendar-event"></i>
                                @elseif($key === 'law')
                                    <i class="bi bi-bank"></i>
                                @elseif($key === 'book')
                                    <i class="bi bi-journal-text"></i>
                                @elseif($key === 'policy')
                                    <i class="bi bi-shield-check"></i>
                                @elseif($key === 'mou')
                                    <i class="bi bi-file-earmark-text"></i>
                                @elseif($key === 'form')
                                    <i class="bi bi-ui-checks-grid"></i>
                                @elseif($key === 'manual')
                                    <i class="bi bi-book"></i>
                                @else
                                    <i class="bi bi-folder2"></i>
                                @endif
                                <span>{{ $label }}</span>
                            </span>

                            <span class="badge {{ $activeCategory === $key ? 'bg-white text-primary' : 'bg-light text-dark' }}">
                                {{ $categoryCounts[$key] ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- =========================
            Content
        ========================== --}}
        <div class="col-xl-9 col-lg-8">
            <div class="card border-0 shadow-sm publicize-content">
                <div class="card-header publicize-card-header publicize-content-head">
                    <div>
                        <h5 class="mb-1 fw-bold d-flex align-items-center gap-2">
                            <i class="bi bi-table text-primary"></i>
                            <span>{{ $categories[$activeCategory] ?? 'รายการทั้งหมด' }}</span>
                        </h5>

                        <div class="text-muted small">
                            @if($yearBe)
                                กรองข้อมูลตามปี พ.ศ. {{ $yearBe }}
                            @else
                                แสดงข้อมูลทั้งหมดตามหมวดหมู่ที่เลือก
                            @endif
                        </div>
                    </div>

                    <div class="publicize-count-badge">
                        จำนวน {{ $publicizes->count() }} รายการ
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($publicizes->count())
                        <div class="publicize-table-shell">
                            <div class="table-responsive">
                                <table id="publicizeTable" class="table align-middle mb-0 publicize-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 70px;">ลำดับ</th>
                                            <th style="width: 140px;">หมวดหมู่</th>
                                            <th>ชื่อเรื่อง / ไฟล์</th>
                                            <th style="width: 135px;">วันที่บันทึก</th>
                                            <th style="width: 220px;" class="text-center">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($publicizes as $index => $item)
                                            <tr>
                                                <td class="text-muted fw-semibold">
                                                    {{ $index + 1 }}
                                                </td>

                                                <td>
                                                    <span class="badge publicize-badge-table">
                                                        {{ $item->category_label }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <div class="publicize-table-title">
                                                        {{ $item->title }}
                                                    </div>

                                                    <div class="publicize-table-file">
                                                        <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                                        <span class="publicize-file-name">
                                                            {{ $item->file_name ?? basename($item->file_path) }}
                                                        </span>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="publicize-date">
                                                        {{ optional($item->recorded_at)->format('d/m/') }}{{ optional($item->recorded_at)->year + 543 }}
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="publicize-table-actions">
                                                        <a href="{{ route('publicizes.file', $item) }}"
                                                           target="_blank"
                                                           class="btn btn-outline-primary btn-sm">
                                                            <i class="bi bi-eye me-1"></i> เปิด
                                                        </a>

                                                        <a href="{{ route('publicizes.edit', $item->id) }}"
                                                           class="btn btn-outline-warning btn-sm">
                                                            <i class="bi bi-pencil-square me-1"></i> แก้ไข
                                                        </a>

                                                        <form action="{{ route('publicizes.destroy', $item->id) }}"
                                                              method="POST"
                                                              class="delete-publicize-form d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-outline-danger btn-sm delete-publicize-btn"
                                                                    data-title="{{ $item->title }}">
                                                                <i class="bi bi-trash me-1"></i> ลบ
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
                    @else
                        <div class="publicize-empty-state">
                            <div class="publicize-empty-state__icon">
                                <i class="bi bi-folder-x"></i>
                            </div>
                            <div class="fw-bold mb-2">ยังไม่มีข้อมูลในเงื่อนไขที่เลือก</div>
                            <div class="text-muted mb-3">
                                สามารถเพิ่มข้อมูลประชาสัมพันธ์ใหม่ได้ทันที
                            </div>
                            <div class="d-flex justify-content-center flex-wrap gap-2">
                                <a href="{{ route('publicizes.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i> เพิ่มข้อมูล
                                </a>
                                <a href="{{ route('publicizes.index') }}" class="btn btn-light border">
                                    <i class="bi bi-arrow-repeat me-1"></i> รีเซ็ตการแสดงผล
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- DataTables CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

<style>
.publicize-page{
    --publicize-primary: #0d6efd;
    --publicize-primary-soft: rgba(13, 110, 253, 0.10);
    --publicize-border: #e9eef5;
    --publicize-text-soft: #6c757d;
    --publicize-bg-soft: #f8fafc;
    --publicize-radius: 18px;
    --publicize-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
}

.publicize-page .publicize-hero{
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    padding: 18px 20px;
    background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    border: 1px solid var(--publicize-border);
    border-radius: 20px;
    box-shadow: var(--publicize-shadow);
}

.publicize-page .publicize-hero__top{
    display: flex;
    align-items: flex-start;
    gap: 14px;
}

.publicize-page .publicize-hero__title{
    font-size: 1.45rem;
    line-height: 1.2;
}

.publicize-page .publicize-hero__subtitle{
    font-size: 0.95rem;
}

.publicize-page .publicize-hero__actions{
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.publicize-page .publicize-icon-btn{
    width: 42px;
    height: 42px;
    border-radius: 12px;
    border: 1px solid var(--publicize-border);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
}

.publicize-page .publicize-icon-btn:hover{
    background: #f3f6fa;
}

.publicize-page .publicize-head-btn{
    min-height: 42px;
    padding: 0.6rem 1rem;
    border-radius: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
}

.publicize-page .publicize-sidebar,
.publicize-page .publicize-content{
    border-radius: var(--publicize-radius);
    overflow: hidden;
    border: 1px solid var(--publicize-border);
    box-shadow: var(--publicize-shadow);
    background: #fff;
}

.publicize-page .publicize-card-header{
    padding: 1rem 1.2rem;
    background: #fff;
    border-bottom: 1px solid var(--publicize-border);
}

.publicize-page .publicize-filter-box{
    padding: 1rem 1rem 0.9rem;
    border-bottom: 1px solid var(--publicize-border);
    background: #fcfdff;
}

.publicize-page .publicize-filter-row{
    display: flex;
    gap: 8px;
}

.publicize-page .publicize-filter-form .form-select,
.publicize-page .publicize-filter-form .btn{
    min-height: 40px;
}

.publicize-page .publicize-reset-btn{
    font-weight: 500;
}

.publicize-page .publicize-category-list .list-group-item{
    border: 0;
    border-radius: 0;
    padding: 14px 18px;
    font-weight: 500;
    transition: all .2s ease;
}

.publicize-page .publicize-category-list .list-group-item:hover{
    background: var(--publicize-bg-soft);
}

.publicize-page .publicize-category-list .list-group-item.active{
    background: var(--publicize-primary);
    color: #fff;
}

.publicize-page .publicize-content-head{
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}

.publicize-page .publicize-count-badge{
    background: var(--publicize-bg-soft);
    color: #334155;
    padding: 8px 14px;
    border-radius: 999px;
    font-size: 0.9rem;
    font-weight: 600;
}

.publicize-page .publicize-alert{
    border-radius: 14px;
    padding: 0.9rem 1rem;
}

.publicize-page .publicize-empty-state{
    padding: 3.5rem 1rem;
    text-align: center;
}

.publicize-page .publicize-empty-state__icon{
    width: 76px;
    height: 76px;
    margin: 0 auto 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--publicize-primary-soft);
    color: var(--publicize-primary);
    font-size: 2rem;
}

.publicize-page .publicize-table-shell{
    padding: 0;
}

.publicize-page .publicize-table{
    --bs-table-bg: transparent;
    margin: 0;
}

.publicize-page .publicize-table thead th{
    background: #f8fafc;
    color: #334155;
    font-size: 0.87rem;
    font-weight: 700;
    border-bottom: 1px solid #e9eef5 !important;
    padding: 14px 16px;
    vertical-align: middle;
    white-space: nowrap;
}

.publicize-page .publicize-table tbody td{
    padding: 14px 16px;
    vertical-align: middle;
    border-color: #eef2f7 !important;
}

.publicize-page .publicize-table tbody tr{
    transition: background .2s ease;
}

.publicize-page .publicize-table tbody tr:hover{
    background: #fbfdff;
}

.publicize-page .publicize-badge-table{
    background: rgba(13, 110, 253, 0.10);
    color: #0d6efd;
    font-weight: 700;
    padding: 7px 12px;
    border-radius: 999px;
    font-size: 0.82rem;
    white-space: nowrap;
}

.publicize-page .publicize-table-title{
    font-weight: 700;
    color: #0f172a;
    line-height: 1.45;
    margin-bottom: 4px;
}

.publicize-page .publicize-table-file{
    font-size: 0.88rem;
    color: #64748b;
    display: flex;
    align-items: center;
    min-width: 0;
    line-height: 1.4;
}

.publicize-page .publicize-file-name{
    display: inline-block;
    max-width: 420px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    vertical-align: middle;
}

.publicize-page .publicize-date{
    font-weight: 600;
    color: #334155;
    white-space: nowrap;
}

.publicize-page .publicize-table-actions{
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.publicize-page .publicize-table-actions .btn{
    min-height: 34px;
    border-radius: 10px;
    font-weight: 600;
    white-space: nowrap;
}

.publicize-page .dataTables_wrapper{
    padding: 16px;
}

.publicize-page .dataTables_length,
.publicize-page .dataTables_filter{
    margin-bottom: 14px;
}

.publicize-page .dataTables_length label,
.publicize-page .dataTables_filter label{
    font-weight: 600;
    color: #475569;
    font-size: 0.92rem;
}

.publicize-page .dataTables_length select{
    min-width: 82px;
    border-radius: 10px;
    border: 1px solid #dbe3ee;
    padding: 6px 28px 6px 10px;
    margin: 0 6px;
}

.publicize-page .dataTables_filter input{
    border: 1px solid #dbe3ee;
    border-radius: 10px;
    min-height: 38px;
    padding: 6px 12px;
    margin-left: 8px;
    outline: none;
}

.publicize-page .dataTables_info{
    color: #64748b;
    font-size: 0.9rem;
    padding-top: 14px !important;
}

.publicize-page .dataTables_paginate{
    padding-top: 10px !important;
}

.publicize-page .dataTables_paginate .paginate_button{
    border-radius: 10px !important;
    margin: 0 2px !important;
}

.publicize-page .dataTables_paginate .paginate_button.current,
.publicize-page .dataTables_paginate .paginate_button.current:hover{
    background: #0d6efd !important;
    border-color: #0d6efd !important;
    color: #fff !important;
}

.publicize-page .dataTables_paginate .paginate_button:hover{
    background: #eff6ff !important;
    border-color: #cfe2ff !important;
    color: #0d6efd !important;
}

@media (max-width: 1199.98px){
    .publicize-page .publicize-hero{
        padding: 16px;
    }
}

@media (max-width: 991.98px){
    .publicize-page .row{
        --bs-gutter-y: 1rem;
    }
}

@media (max-width: 767.98px){
    .publicize-page .publicize-hero__title{
        font-size: 1.2rem;
    }

    .publicize-page .publicize-filter-row{
        flex-direction: column;
    }

    .publicize-page .publicize-filter-row .btn,
    .publicize-page .publicize-filter-row .form-select{
        width: 100%;
    }

    .publicize-page .dataTables_wrapper{
        padding: 12px;
    }

    .publicize-page .publicize-file-name{
        max-width: 220px;
    }
}

@media (max-width: 575.98px){
    .publicize-page .publicize-hero{
        padding: 14px;
        border-radius: 16px;
    }

    .publicize-page .publicize-hero__actions{
        width: 100%;
    }

    .publicize-page .publicize-hero__actions .btn{
        flex: 1 1 100%;
        justify-content: center;
    }

    .publicize-page .publicize-table-actions{
        flex-direction: column;
        align-items: stretch;
    }

    .publicize-page .publicize-table-actions .btn,
    .publicize-page .publicize-table-actions form{
        width: 100%;
    }

    .publicize-page .publicize-table-actions .btn{
        justify-content: center;
    }
}
</style>

{{-- jQuery + DataTables + SweetAlert --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && $('#publicizeTable').length) {
        $('#publicizeTable').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            ordering: true,
            responsive: false,
            autoWidth: false,
            language: {
                search: "ค้นหา:",
                lengthMenu: "แสดง _MENU_ แถว",
                info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                infoEmpty: "แสดง 0 ถึง 0 จาก 0 รายการ",
                zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                paginate: {
                    first: "แรก",
                    last: "สุดท้าย",
                    next: "ถัดไป",
                    previous: "ก่อนหน้า"
                }
            },
            columnDefs: [
                { orderable: false, targets: [4] }
            ]
        });
    }

    const deleteForms = document.querySelectorAll('.delete-publicize-form');

    deleteForms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const button = form.querySelector('.delete-publicize-btn');
            const title = button?.getAttribute('data-title') || 'รายการนี้';

            Swal.fire({
                title: 'ยืนยันการลบ?',
                html: `คุณต้องการลบ <b>${title}</b> ใช่หรือไม่`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                focusCancel: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
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