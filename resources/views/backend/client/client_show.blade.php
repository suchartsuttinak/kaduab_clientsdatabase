@extends('admin.admin_master')
@section('admin')

<style>
    .client-page {
        padding-top: .5rem;
    }

    .client-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .client-toolbar-left,
    .client-toolbar-right {
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .client-title-box h4 {
        margin: 0;
        font-weight: 600;
        color: #1f2937;
    }

    .client-title-box p {
        margin: .15rem 0 0;
        font-size: 13px;
        color: #6b7280;
    }

    .client-btn {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        border-radius: 12px;
        padding: .55rem .95rem;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .06);
    }

    .client-list-card {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(15, 23, 42, .04);
    }

    .client-list-card .card-header {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #eef2f7;
        padding: 1rem;
    }

    .client-list-card .card-title {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
    }

    .client-list-card .card-body {
        padding: 1rem;
    }

    .client-table-wrap {
        width: 100%;
    }

    .client-table {
        margin-bottom: 0;
        min-width: 980px;
        width: 100% !important;
        vertical-align: middle;
    }

    .client-table thead th {
        background: #eef4ff !important;
        color: #1d4f91;
        font-weight: 600;
        font-size: 13px;
        border-bottom: 1px solid #dbe6f5 !important;
        white-space: nowrap;
        vertical-align: middle;
    }

    .client-table tbody td {
        font-size: 14px;
        vertical-align: middle;
    }

    .client-table tbody tr:hover {
        background: #fafcff;
    }

    .client-link-image,
    .client-link-name {
        text-decoration: none;
        color: inherit;
        display: inline-block;
        transition: all .2s ease;
    }

    .client-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e5e7eb;
        transition: all .2s ease;
    }

    .client-link-image:hover .client-avatar {
        transform: scale(1.04);
        box-shadow: 0 4px 12px rgba(0, 0, 0, .12);
    }

    .client-name {
        font-weight: 600;
        color: #111827;
        margin-bottom: 2px;
        transition: color .2s ease;
    }

    .client-link-name:hover .client-name {
        color: #0d6efd;
    }

    .client-subtext {
        font-size: 12px;
        color: #6b7280;
    }

    .problem-list {
        margin: 0;
        padding-left: 1rem;
        font-size: 13px;
    }

    .status-badge {
        padding: .45rem .7rem;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .action-cell {
        width: 190px;
        min-width: 190px;
        white-space: nowrap;
        text-align: center;
        vertical-align: middle;
    }

    .action-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-wrap: nowrap;
        gap: .35rem;
        white-space: nowrap;
    }

    .action-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        padding: 0;
        flex: 0 0 34px;
        border: none;
    }

    .action-btn span {
        line-height: 1;
        font-size: 18px;
    }

    .client-list-card .dataTables_wrapper {
        width: 100%;
    }

    .client-list-card .dataTables_filter input {
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: .35rem .65rem;
        margin-left: .4rem;
        outline: none;
    }

    .client-list-card .dataTables_filter input:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .12);
    }

    .client-list-card .dataTables_length select {
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: .2rem 1.8rem .2rem .5rem;
        outline: none;
    }

    .client-list-card .dataTables_info,
    .client-list-card .dataTables_length,
    .client-list-card .dataTables_filter {
        font-size: 13px;
        margin-top: .35rem;
    }

    .client-list-card .dataTables_scroll {
        margin-top: .5rem;
        margin-bottom: .75rem;
    }

    .client-list-card .dataTables_scrollBody {
        border-bottom: 1px solid #eef2f7;
    }

    .client-list-card .dataTables_scrollHeadInner,
    .client-list-card .dataTables_scrollHeadInner table {
        width: 100% !important;
    }

    .client-list-card .dataTables_info {
        padding-top: .75rem !important;
        color: #64748b;
    }

    /* ==============================
       DataTables Pagination
       แก้ปุ่มซ้อน / ลดช่องห่าง / จำกัดเฉพาะหน้านี้
       ============================== */
    .client-list-card .dataTables_paginate {
        width: 100%;
        padding-top: .75rem !important;
        margin-top: .25rem !important;
        text-align: center !important;
        float: none !important;
    }

    .client-list-card .dataTables_paginate .pagination {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        margin: 0 !important;
        padding: 0 !important;
        flex-wrap: wrap;
    }

    .client-list-card .dataTables_paginate .page-item {
        margin: 0 !important;
        padding: 0 !important;
    }

    .client-list-card .dataTables_paginate .page-link {
        min-width: 38px;
        height: 36px;
        padding: 0 .72rem !important;
        border-radius: 10px !important;
        border: 1px solid #dbe3ef !important;
        background: #ffffff !important;
        color: #475569 !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        line-height: 1;
        box-shadow: none !important;
    }

    .client-list-card .dataTables_paginate .page-item.active .page-link {
        background: #4f6ef7 !important;
        border-color: #4f6ef7 !important;
        color: #ffffff !important;
    }

    .client-list-card .dataTables_paginate .page-link:hover {
        background: #eef4ff !important;
        border-color: #c7d7fe !important;
        color: #1d4f91 !important;
    }

    .client-list-card .dataTables_paginate .page-item.disabled .page-link {
        background: #f8fafc !important;
        border-color: #e5e7eb !important;
        color: #94a3b8 !important;
        cursor: not-allowed !important;
    }

    .client-list-card .dataTables_paginate .paginate_button {
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .client-list-card .dataTables_paginate .paginate_button:hover,
    .client-list-card .dataTables_paginate .paginate_button.current,
    .client-list-card .dataTables_paginate .paginate_button.current:hover {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    @media (max-width: 767.98px) {
        .client-toolbar {
            align-items: stretch;
        }

        .client-toolbar-left,
        .client-toolbar-right {
            width: 100%;
            justify-content: space-between;
        }

        .client-toolbar-right .client-btn {
            width: 100%;
            justify-content: center;
        }

        .client-list-card .card-header,
        .client-list-card .card-body {
            padding: .85rem;
        }

        .client-table {
            min-width: 960px;
        }

        .action-cell {
            width: 170px;
            min-width: 170px;
        }

        .action-group {
            gap: .3rem;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            flex: 0 0 32px;
            border-radius: 9px;
        }

        .client-list-card .dataTables_length,
        .client-list-card .dataTables_filter,
        .client-list-card .dataTables_info,
        .client-list-card .dataTables_paginate {
            width: 100%;
            text-align: center !important;
        }

        .client-list-card .dataTables_filter {
            margin-top: .65rem;
        }

        .client-list-card .dataTables_filter label {
            width: 100%;
        }

        .client-list-card .dataTables_filter input {
            width: 100%;
            margin-left: 0;
            margin-top: .35rem;
        }

        .client-list-card .dataTables_paginate {
            margin-top: .5rem !important;
        }

        .client-list-card .dataTables_paginate .pagination {
            gap: .28rem;
        }

        .client-list-card .dataTables_paginate .page-link {
            min-width: 36px;
            height: 34px;
            font-size: 12px;
            border-radius: 9px !important;
            padding: 0 .62rem !important;
        }
    }
</style>

<div class="content">
    <div class="container-fluid client-page">

        <div class="client-toolbar">
            <div class="client-toolbar-left">
                <a href="{{ route('dashboard') }}" class="btn btn-primary client-btn">
                    <i data-feather="arrow-left-circle"></i>
                    <span>ย้อนกลับ</span>
                </a>

                <div class="client-title-box">
                    <h4>รายการผู้รับบริการ</h4>
                    <p>จัดการข้อมูลผู้รับบริการทั้งหมดในระบบ</p>
                </div>
            </div>

            @if(auth()->check() && auth()->user()->hasRole(['admin','executive','social_worker']))
                <div class="client-toolbar-right">
                    <a href="{{ route('client.add') }}" class="btn btn-success client-btn">
                        <i data-feather="plus-circle"></i>
                        <span>เพิ่มรายการ</span>
                    </a>
                </div>
            @endif
        </div>

        @if(
            auth()->check()
            && in_array(auth()->user()->role, ['admin', 'executive'], true)
            && isset($clients)
            && $clients->isNotEmpty()
        )
            <div class="card client-list-card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('client.show') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-8 col-lg-6">
                                <label class="form-label fw-semibold">หน่วยงาน / โครงการ</label>
                                <select name="project_id" class="form-select">
                                    <option value="">-- ไม่กำหนดหน่วยงาน --</option>
                                    <option value="all" {{ ($projectId ?? '') === 'all' ? 'selected' : '' }}>
                                        ทั้งหมด
                                    </option>

                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}"
                                            {{ (string)($projectId ?? '') === (string)$project->id ? 'selected' : '' }}>
                                            {{ $project->project_name ?? $project->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 col-lg-3">
                                <button type="submit" class="btn btn-primary client-btn w-100 justify-content-center">
                                    <i data-feather="filter"></i>
                                    <span>กรองข้อมูล</span>
                                </button>
                            </div>

                            <div class="col-md-4 col-lg-3">
                                <a href="{{ route('client.show') }}" class="btn btn-outline-secondary client-btn w-100 justify-content-center">
                                    <i data-feather="refresh-ccw"></i>
                                    <span>ล้างตัวกรอง</span>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <div class="card client-list-card">
            <div class="card-header">
                <h5 class="card-title">รายการผู้รับ</h5>
            </div>

            <div class="card-body">
                @if($clients->isNotEmpty())
                    <div class="client-table-wrap">
                        <table id="datatable" class="table table-hover align-middle client-table nowrap w-100">
                            <thead>
                                <tr>
                                    <th>ลำดับ</th>
                                    <th>ภาพ</th>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>วันที่รับเข้า</th>
                                    <th>วันเกิด</th>
                                    <th>อายุ</th>
                                    <th>ปัญหา</th>
                                    <th>สถานะ</th>
                                    <th class="action-cell">การจัดการ</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($clients as $key => $client)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>

                                        <td>
                                            @php
                                                $imagePath = public_path('upload/client_images/' . $client->image);
                                            @endphp

                                            <a href="{{ route('admin.index', $client->id) }}"
                                               title="ดูข้อมูล"
                                               class="client-link-image">
                                                @if(!empty($client->image) && file_exists($imagePath))
                                                    <img src="{{ asset('upload/client_images/' . $client->image) }}"
                                                         alt="client-image"
                                                         class="client-avatar">
                                                @else
                                                    <img src="{{ asset('upload/no_image.jpg') }}"
                                                         alt="no-image"
                                                         class="client-avatar">
                                                @endif
                                            </a>
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.index', $client->id) }}"
                                               title="ดูข้อมูล"
                                               class="client-link-name">
                                                <div class="client-name">
                                                    {{ $client->full_name }}
                                                </div>

                                                <div class="client-subtext">
                                                    เลขทะเบียน {{ $client->register_number ?? '-' }}
                                                </div>
                                            </a>
                                        </td>

                                        <td>{{ $client->arrival_date }}</td>
                                        <td>{{ $client->birth_date }}</td>
                                        <td>{{ $client->age }}</td>

                                        <td>
                                            @if($client->problems->isNotEmpty())
                                                <ul class="problem-list">
                                                    @foreach($client->problems as $problem)
                                                        <li>{{ $problem->problem_name }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-muted small">ไม่มีข้อมูล</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($client->release_status === 'show')
                                                <span class="badge bg-success-subtle text-success status-badge">
                                                    อยู่ในระบบ
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary status-badge">
                                                    ไม่อยู่ในระบบ
                                                </span>
                                            @endif
                                        </td>

                                        <td class="action-cell">
                                            <div class="action-group">
                                                <a title="ดูข้อมูล"
                                                   href="{{ route('admin.index', $client->id) }}"
                                                   class="btn btn-primary btn-sm action-btn">
                                                    <span class="mdi mdi-eye-circle mdi-18px"></span>
                                                </a>

                                                <a title="แก้ไข"
                                                   href="{{ route('client.edit', $client->id) }}"
                                                   class="btn btn-success btn-sm action-btn">
                                                    <span class="mdi mdi-book-edit-outline mdi-18px"></span>
                                                </a>

                                                @if(in_array(auth()->user()->role, ['admin', 'social_worker']))
                                                    <button type="button"
                                                            title="ลบ"
                                                            class="btn btn-danger btn-sm action-btn client-delete-btn"
                                                            data-url="{{ route('client.delete', $client->id) }}">
                                                        <span class="mdi mdi-trash-can-outline mdi-18px"></span>
                                                    </button>
                                                @endif

                                                <a title="จำหน่าย"
                                                   href="{{ route('refers.index', $client->id) }}"
                                                   class="btn btn-secondary btn-sm action-btn">
                                                    <span class="mdi mdi-file-export-outline mdi-18px"></span>
                                                </a>

                                                @if(auth()->user()->role === 'admin')
                                                    <a title="ย้ายเคส"
                                                       href="{{ route('client.transfer.create', $client->id) }}"
                                                       class="btn btn-warning btn-sm action-btn">
                                                        <span class="mdi mdi-arrow-right-bold mdi-18px"></span>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        ไม่มีข้อมูลผู้รับ
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('#datatable').length && $.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable().destroy();
        }

        if ($('#datatable').length) {
            $('#datatable').DataTable({
                responsive: false,
                autoWidth: false,
                scrollX: true,
                scrollCollapse: true,
                fixedHeader: false,
                pageLength: 10,
                pagingType: 'simple_numbers',
                order: [[0, 'asc']],
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    infoEmpty: "ไม่มีข้อมูลให้แสดง",
                    paginate: {
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    },
                    zeroRecords: "ไม่พบข้อมูลที่ค้นหา"
                },
                drawCallback: function() {
                    $('.client-list-card .dataTables_paginate .paginate_button').addClass('btn-sm');
                }
            });
        }

        if (window.feather) {
            feather.replace();
        }

        $(document)
            .off('click.clientDeleteBtn')
            .on('click.clientDeleteBtn', '.client-delete-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const deleteUrl = $(this).data('url');

                if (!deleteUrl) {
                    return false;
                }

                Swal.fire({
                    title: 'ยืนยันการลบข้อมูล?',
                    text: 'หากลบแล้ว ข้อมูลนี้อาจไม่สามารถกู้คืนได้',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ลบข้อมูล',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                    focusCancel: true,
                    allowOutsideClick: false,
                    allowEscapeKey: true
                }).then(function(result) {
                    if (result.isConfirmed === true) {
                        window.location.href = deleteUrl;
                    }
                });

                return false;
            });
    });
</script>
@endpush