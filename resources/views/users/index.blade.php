@extends('admin.admin_master')

@section('admin')
<div class="container-fluid py-4 user-manage-page">
    <div class="ump-header-card">
        <div class="ump-header-left">
            <div class="ump-title-wrap">
                <div class="ump-title-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h4 class="ump-page-title mb-1">จัดการผู้ใช้งาน</h4>
                    <div class="ump-page-subtitle">
                        จัดการข้อมูลผู้ใช้ สิทธิ์การใช้งาน และบ้านที่รับผิดชอบ
                    </div>
                </div>
            </div>
        </div>

        <div class="ump-header-right">
            <a href="{{ route('users.create') }}" class="btn ump-btn-primary">
                <i class="bi bi-person-plus-fill"></i>
                <span>เพิ่มผู้ใช้งาน</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success ump-alert shadow-sm border-0">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger ump-alert shadow-sm border-0">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="ump-card">
        <div class="ump-card-head">
            <div class="ump-card-head-left">
                <div class="ump-card-title">
                    <i class="bi bi-list-ul"></i>
                    <span>รายชื่อผู้ใช้งานทั้งหมด</span>
                </div>
                <div class="ump-card-subtitle">
                    จำนวน {{ $users->count() }} รายการ
                </div>
            </div>
        </div>

        <div class="ump-card-body">
            <div class="ump-table-wrap">
                <table id="usersTable" class="table align-middle mb-0 ump-table nowrap w-100">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>ผู้ใช้งาน</th>
                            <th>อีเมล</th>
                            <th>สิทธิ์</th>
                            <th>บ้านที่ดูแล</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $key => $user)
                            <tr>
                                <td class="text-center fw-semibold">{{ $key + 1 }}</td>

                                <td>
                                    <div class="ump-user-cell">
                                        <img src="{{ $user->photo_url }}"
                                             alt="user-photo"
                                             class="ump-user-avatar">
                                        <div class="ump-user-meta">
                                            <div class="ump-user-name">{{ $user->name }}</div>
                                            <div class="ump-user-id">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="ump-email">
                                        {{ $user->email }}
                                    </div>
                                </td>

                                <td>
                                    <span class="ump-role-badge">
                                        {{ $user->role_label ?? $user->role }}
                                    </span>
                                </td>

                                <td>
                                    @if($user->houses->count())
                                        <div class="ump-house-list">
                                            @foreach($user->houses as $house)
                                                <span class="ump-house-badge">
                                                    <i class="bi bi-house-door-fill"></i>
                                                    <span>{{ $house->house_name }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="ump-empty-text">- ไม่ได้กำหนดบ้าน -</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if((string) $user->status === '1')
                                        <span class="ump-status-badge ump-status-active">
                                            ใช้งาน
                                        </span>
                                    @else
                                        <span class="ump-status-badge ump-status-inactive">
                                            ปิดใช้งาน
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="ump-action-group">
                                        <a href="{{ route('users.edit', $user->id) }}"
                                           class="btn ump-btn-action ump-btn-edit">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>แก้ไข</span>
                                        </a>

                                        <form action="{{ route('users.toggle-status', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn ump-btn-action ump-btn-status"
                                                    onclick="return confirm('ยืนยันการเปลี่ยนสถานะผู้ใช้งานนี้?')">
                                                <i class="bi bi-arrow-repeat"></i>
                                                <span>สถานะ</span>
                                            </button>
                                        </form>

                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn ump-btn-action ump-btn-delete"
                                                    onclick="return confirm('ยืนยันการลบผู้ใช้งานนี้?')">
                                                <i class="bi bi-trash3-fill"></i>
                                                <span>ลบ</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="ump-empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <div class="ump-empty-title">ไม่พบข้อมูลผู้ใช้งาน</div>
                                        <div class="ump-empty-subtitle">เริ่มต้นโดยการเพิ่มผู้ใช้งานใหม่เข้าสู่ระบบ</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* =========================
   Users Management Page Only
   Scoped CSS: .user-manage-page / .ump-*
========================= */
.user-manage-page {
    --ump-primary: #2563eb;
    --ump-primary-soft: #eff6ff;
    --ump-border: #e6edf5;
    --ump-border-strong: #d9e2ec;
    --ump-text: #0f172a;
    --ump-muted: #64748b;
    --ump-bg: #f8fbff;
    --ump-white: #ffffff;
    --ump-success-bg: #ecfdf3;
    --ump-success-text: #15803d;
    --ump-danger-bg: #fef2f2;
    --ump-danger-text: #dc2626;
    --ump-warning-bg: #fff7ed;
    --ump-warning-text: #ea580c;
    --ump-info-bg: #eff6ff;
    --ump-info-text: #1d4ed8;
}

.user-manage-page .ump-header-card,
.user-manage-page .ump-card {
    background: var(--ump-white);
    border: 1px solid var(--ump-border);
    border-radius: 24px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
}

.user-manage-page .ump-header-card {
    padding: 1.25rem 1.25rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.user-manage-page .ump-title-wrap {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-manage-page .ump-title-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    color: #fff;
    font-size: 1.35rem;
    box-shadow: 0 12px 24px rgba(37, 99, 235, 0.20);
    flex: 0 0 auto;
}

.user-manage-page .ump-page-title {
    color: var(--ump-text);
    font-weight: 800;
    font-size: 1.35rem;
    margin: 0;
}

.user-manage-page .ump-page-subtitle {
    color: var(--ump-muted);
    font-size: 0.95rem;
}

.user-manage-page .ump-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    border: 0;
    border-radius: 999px;
    padding: .82rem 1.2rem;
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    color: #fff;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 10px 24px rgba(37, 99, 235, 0.20);
    transition: transform .18s ease, box-shadow .18s ease;
}

.user-manage-page .ump-btn-primary:hover {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 14px 30px rgba(37, 99, 235, 0.24);
}

.user-manage-page .ump-alert {
    border-radius: 18px;
    padding: .95rem 1rem;
    margin-bottom: 1rem;
}

.user-manage-page .ump-card-head {
    padding: 1.1rem 1.25rem;
    border-bottom: 1px solid var(--ump-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    flex-wrap: wrap;
}

.user-manage-page .ump-card-title {
    display: flex;
    align-items: center;
    gap: .6rem;
    font-weight: 800;
    color: var(--ump-text);
}

.user-manage-page .ump-card-title i {
    color: var(--ump-primary);
}

.user-manage-page .ump-card-subtitle {
    font-size: .88rem;
    color: var(--ump-muted);
    margin-top: .15rem;
}

.user-manage-page .ump-card-body {
    padding: 1rem;
}

.user-manage-page .ump-table-wrap {
    width: 100%;
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
    border-radius: 18px;
}

.user-manage-page .ump-table {
    min-width: 1100px;
    margin-bottom: 0;
}

.user-manage-page .ump-table thead th {
    background: #f8fafc;
    color: #475569;
    font-size: .92rem;
    font-weight: 800;
    border-bottom: 1px solid #eaf0f6;
    white-space: nowrap;
    padding: 1rem .9rem;
    vertical-align: middle;
}

.user-manage-page .ump-table tbody td {
    border-color: #eef2f7;
    padding: 1rem .9rem;
    vertical-align: middle;
    background: #fff;
}

.user-manage-page .ump-table tbody tr:hover td {
    background: #fcfdff;
}

.user-manage-page .ump-user-cell {
    display: flex;
    align-items: center;
    gap: .85rem;
    min-width: 220px;
}

.user-manage-page .ump-user-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e5e7eb;
    background: #fff;
    flex: 0 0 auto;
}

.user-manage-page .ump-user-name {
    font-weight: 700;
    color: var(--ump-text);
    line-height: 1.2;
}

.user-manage-page .ump-user-id {
    font-size: .82rem;
    color: var(--ump-muted);
    margin-top: .18rem;
}

.user-manage-page .ump-email {
    color: #334155;
    min-width: 180px;
    word-break: break-word;
}

.user-manage-page .ump-role-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .5rem .85rem;
    border-radius: 999px;
    border: 1px solid var(--ump-border-strong);
    background: #fff;
    color: #334155;
    font-weight: 700;
    font-size: .85rem;
    white-space: nowrap;
}

.user-manage-page .ump-house-list {
    display: flex;
    flex-wrap: wrap;
    gap: .45rem;
    min-width: 210px;
}

.user-manage-page .ump-house-badge {
    display: inline-flex;
    align-items: center;
    gap: .38rem;
    padding: .46rem .72rem;
    border-radius: 999px;
    background: var(--ump-primary-soft);
    color: var(--ump-primary);
    border: 1px solid #dbeafe;
    font-weight: 700;
    font-size: .83rem;
    white-space: nowrap;
}

.user-manage-page .ump-empty-text {
    color: var(--ump-muted);
    font-size: .92rem;
}

.user-manage-page .ump-status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 92px;
    padding: .52rem .9rem;
    border-radius: 999px;
    font-weight: 800;
    font-size: .84rem;
    white-space: nowrap;
}

.user-manage-page .ump-status-active {
    background: var(--ump-success-bg);
    color: var(--ump-success-text);
    border: 1px solid #bbf7d0;
}

.user-manage-page .ump-status-inactive {
    background: var(--ump-danger-bg);
    color: var(--ump-danger-text);
    border: 1px solid #fecaca;
}

.user-manage-page .ump-action-group {
    display: flex;
    flex-wrap: wrap;
    gap: .45rem;
    justify-content: center;
    min-width: 265px;
}

.user-manage-page .ump-btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    min-width: 84px;
    padding: .58rem .85rem;
    border-radius: 999px;
    font-size: .84rem;
    font-weight: 700;
    border: 1px solid transparent;
    transition: all .18s ease;
    white-space: nowrap;
}

.user-manage-page .ump-btn-edit {
    background: var(--ump-warning-bg);
    color: var(--ump-warning-text);
    border-color: #fed7aa;
}

.user-manage-page .ump-btn-edit:hover {
    background: #ffedd5;
    color: #c2410c;
}

.user-manage-page .ump-btn-status {
    background: var(--ump-info-bg);
    color: var(--ump-info-text);
    border-color: #bfdbfe;
}

.user-manage-page .ump-btn-status:hover {
    background: #dbeafe;
    color: #1e40af;
}

.user-manage-page .ump-btn-delete {
    background: var(--ump-danger-bg);
    color: var(--ump-danger-text);
    border-color: #fecaca;
}

.user-manage-page .ump-btn-delete:hover {
    background: #fee2e2;
    color: #b91c1c;
}

.user-manage-page .ump-empty-state {
    min-height: 260px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
    text-align: center;
    color: var(--ump-muted);
}

.user-manage-page .ump-empty-state i {
    font-size: 2.4rem;
    margin-bottom: .75rem;
    color: #94a3b8;
}

.user-manage-page .ump-empty-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--ump-text);
}

.user-manage-page .ump-empty-subtitle {
    font-size: .9rem;
    margin-top: .25rem;
}

/* DataTables scoped */
.user-manage-page div.dataTables_wrapper div.dataTables_length,
.user-manage-page div.dataTables_wrapper div.dataTables_filter {
    margin-bottom: .85rem;
}

.user-manage-page div.dataTables_wrapper div.dataTables_filter {
    text-align: right;
}

.user-manage-page div.dataTables_wrapper div.dataTables_filter label,
.user-manage-page div.dataTables_wrapper div.dataTables_length label {
    font-weight: 700;
    color: #475569;
    font-size: .9rem;
}

.user-manage-page div.dataTables_wrapper div.dataTables_filter input {
    border-radius: 999px !important;
    border: 1px solid #dbe3ee !important;
    padding: .5rem .9rem !important;
    margin-left: .5rem !important;
    min-height: 40px;
    box-shadow: none !important;
}

.user-manage-page div.dataTables_wrapper div.dataTables_length select {
    border-radius: 12px !important;
    border: 1px solid #dbe3ee !important;
    padding: .42rem 2rem .42rem .8rem !important;
    min-height: 40px;
    box-shadow: none !important;
}

.user-manage-page div.dataTables_wrapper div.dataTables_info {
    color: #64748b;
    font-size: .9rem;
    padding-top: 1rem;
}

.user-manage-page div.dataTables_wrapper div.dataTables_paginate {
    padding-top: .75rem;
}

.user-manage-page div.dataTables_wrapper div.dataTables_paginate .paginate_button {
    border-radius: 999px !important;
    margin: 0 .12rem;
    padding: .38rem .8rem !important;
}

.user-manage-page .dataTables_scrollHeadInner,
.user-manage-page .dataTables_scrollHeadInner table {
    width: 100% !important;
}

/* Responsive */
@media (max-width: 991.98px) {
    .user-manage-page .ump-header-card {
        padding: 1rem;
    }

    .user-manage-page .ump-card-head,
    .user-manage-page .ump-card-body {
        padding-left: .9rem;
        padding-right: .9rem;
    }

    .user-manage-page .ump-page-title {
        font-size: 1.18rem;
    }

    .user-manage-page .ump-title-icon {
        width: 50px;
        height: 50px;
        font-size: 1.15rem;
        border-radius: 16px;
    }
}

@media (max-width: 767.98px) {
    .user-manage-page {
        padding-left: .15rem;
        padding-right: .15rem;
    }

    .user-manage-page .ump-header-card {
        flex-direction: column;
        align-items: stretch;
    }

    .user-manage-page .ump-title-wrap {
        align-items: flex-start;
    }

    .user-manage-page .ump-header-right {
        width: 100%;
    }

    .user-manage-page .ump-btn-primary {
        width: 100%;
        justify-content: center;
    }

    .user-manage-page .ump-card-head {
        flex-direction: column;
        align-items: flex-start;
    }

    .user-manage-page div.dataTables_wrapper div.dataTables_filter,
    .user-manage-page div.dataTables_wrapper div.dataTables_length,
    .user-manage-page div.dataTables_wrapper div.dataTables_info,
    .user-manage-page div.dataTables_wrapper div.dataTables_paginate {
        text-align: left !important;
    }

    .user-manage-page div.dataTables_wrapper div.dataTables_filter input {
        width: 100% !important;
        margin-left: 0 !important;
        margin-top: .45rem !important;
    }

    .user-manage-page div.dataTables_wrapper div.dataTables_length select {
        width: 100% !important;
        margin-top: .45rem;
    }

    .user-manage-page .ump-table {
        min-width: 980px;
    }

    .user-manage-page .ump-action-group {
        justify-content: flex-start;
    }
}

@media (max-width: 575.98px) {
    .user-manage-page .ump-page-title {
        font-size: 1.05rem;
    }

    .user-manage-page .ump-page-subtitle,
    .user-manage-page .ump-card-subtitle {
        font-size: .84rem;
    }

    .user-manage-page .ump-title-icon {
        width: 44px;
        height: 44px;
        font-size: 1rem;
        border-radius: 14px;
    }

    .user-manage-page .ump-btn-primary {
        padding: .78rem 1rem;
        font-size: .92rem;
    }

    .user-manage-page .ump-card-head,
    .user-manage-page .ump-card-body {
        padding: .8rem;
    }

    .user-manage-page .ump-table thead th,
    .user-manage-page .ump-table tbody td {
        padding: .85rem .75rem;
    }
}

/* ADMIN_USERS_SCROLL_HOTFIX_V1
   Keep exactly one horizontal scrollbar on /admin/users.
   DataTables owns horizontal scrolling; the outer .ump-table-wrap must not scroll. */
.user-manage-page .ump-table-wrap {
    overflow-x: visible !important;
    overflow-y: visible !important;
}

.user-manage-page div.dataTables_wrapper,
.user-manage-page div.dt-container {
    width: 100% !important;
    max-width: 100% !important;
}

.user-manage-page .dataTables_scroll,
.user-manage-page .dt-scroll {
    width: 100% !important;
    max-width: 100% !important;
}

.user-manage-page .dataTables_scrollBody,
.user-manage-page .dt-scroll-body {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
}

/* Keep summary and pagination clear of the remaining scrollbar. */
.user-manage-page div.dataTables_wrapper div.dataTables_info,
.user-manage-page div.dataTables_wrapper div.dataTables_paginate,
.user-manage-page div.dt-container div.dt-info,
.user-manage-page div.dt-container div.dt-paging {
    position: relative;
    z-index: 1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#usersTable').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            responsive: false,
            autoWidth: false,
            scrollX: true,
            language: {
                search: "ค้นหา:",
                lengthMenu: "แสดง _MENU_ รายการต่อหน้า",
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
                { orderable: false, targets: [4, 6] }
            ]
        });
    }
});
</script>
@endsection