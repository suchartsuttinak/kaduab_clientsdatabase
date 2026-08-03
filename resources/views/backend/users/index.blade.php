@extends('admin.admin_master')

@section('admin')
<div class="container-fluid py-4 user-manage-page">
    <div class="ump-header-card">
        <div class="ump-title-wrap">
            <div class="ump-title-icon"><i class="bi bi-people-fill"></i></div>
            <div>
                <h4 class="ump-page-title mb-1">จัดการผู้ใช้งานและสิทธิ์</h4>
                <div class="ump-page-subtitle">กำหนดโครงการ บ้าน บทบาท และสิทธิ์รายฟอร์มอย่างเป็นลำดับ</div>
            </div>
        </div>

        <a href="{{ route('users.create') }}" class="btn ump-btn-primary">
            <i class="bi bi-person-plus-fill"></i>
            เพิ่มผู้ใช้งาน
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success ump-alert shadow-sm border-0">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger ump-alert shadow-sm border-0">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3">
            <div class="ump-stat-card">
                <span class="ump-stat-icon"><i class="bi bi-people"></i></span>
                <div><div class="ump-stat-label">ผู้ใช้ทั้งหมด</div><div class="ump-stat-value">{{ $stats['total'] ?? $users->count() }}</div></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="ump-stat-card">
                <span class="ump-stat-icon ump-stat-icon-green"><i class="bi bi-person-check-fill"></i></span>
                <div><div class="ump-stat-label">กำลังใช้งาน</div><div class="ump-stat-value">{{ $stats['active'] ?? 0 }}</div></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="ump-stat-card">
                <span class="ump-stat-icon ump-stat-icon-purple"><i class="bi bi-shield-check"></i></span>
                <div><div class="ump-stat-label">ผู้ดูแลระบบ</div><div class="ump-stat-value">{{ $stats['admin'] ?? 0 }}</div></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="ump-stat-card">
                <span class="ump-stat-icon ump-stat-icon-orange"><i class="bi bi-ui-checks-grid"></i></span>
                <div><div class="ump-stat-label">เปิดสิทธิ์รายฟอร์ม</div><div class="ump-stat-value">{{ $users->where('form_permissions_enabled', true)->count() }}</div></div>
            </div>
        </div>
    </div>

    <div class="ump-card">
        <div class="ump-card-head">
            <div>
                <div class="ump-card-title"><i class="bi bi-list-ul"></i> รายชื่อผู้ใช้งานทั้งหมด</div>
                <div class="ump-card-subtitle">จำนวน {{ $users->count() }} รายการ</div>
            </div>
            <div class="ump-safe-note">
                <i class="bi bi-shield-lock-fill"></i>
                ผู้ใช้เดิมที่ยังไม่เปิดสิทธิ์รายฟอร์มจะทำงานตามระบบเดิม
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
                            <th>บทบาท</th>
                            <th>โครงการ</th>
                            <th>บ้านที่ดูแล</th>
                            <th>สิทธิ์รายฟอร์ม</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $key => $user)
                            @php
                                $permissionCount = $user->formPermissions->count();
                                $projectName = $user->project?->project_name ?? $user->project?->name;
                            @endphp
                            <tr>
                                <td class="text-center fw-semibold">{{ $key + 1 }}</td>
                                <td>
                                    <div class="ump-user-cell">
                                        <img src="{{ $user->photo_url }}" alt="รูปผู้ใช้" class="ump-user-avatar">
                                        <div>
                                            <div class="ump-user-name">{{ $user->name }}</div>
                                            <div class="ump-user-id">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><div class="ump-email">{{ $user->email }}</div></td>
                                <td><span class="ump-role-badge">{{ $user->role_label }}</span></td>
                                <td>
                                    @if($projectName)
                                        <span class="ump-project-badge"><i class="bi bi-diagram-3-fill"></i>{{ $projectName }}</span>
                                    @else
                                        <span class="ump-empty-text">ไม่กำหนด</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->houses->isNotEmpty())
                                        <div class="ump-house-list">
                                            @foreach($user->houses as $house)
                                                <span class="ump-house-badge"><i class="bi bi-house-door-fill"></i>{{ $house->house_name }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="ump-empty-text">ไม่กำหนดบ้าน</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->isAdmin())
                                        <span class="ump-permission-badge ump-permission-full">
                                            <i class="bi bi-shield-check"></i>เต็มระบบ
                                        </span>
                                    @elseif(!$user->form_permissions_enabled)
                                        <span class="ump-permission-badge ump-permission-legacy">
                                            <i class="bi bi-arrow-repeat"></i>ตามบทบาทเดิม
                                        </span>
                                    @else
                                        <span class="ump-permission-badge ump-permission-custom">
                                            <i class="bi bi-ui-checks-grid"></i>{{ $permissionCount }} ฟอร์ม
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="ump-status-badge {{ (string) $user->status === '1' ? 'ump-status-active' : 'ump-status-inactive' }}">
                                        {{ $user->status_label }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="ump-action-group">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn ump-btn-action ump-btn-edit">
                                            <i class="bi bi-pencil-square"></i><span>แก้ไข</span>
                                        </a>

                                        <form action="{{ route('users.toggle-status', $user->id) }}" method="POST" class="d-inline js-confirm-status" data-user-name="{{ $user->name }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn ump-btn-action ump-btn-status">
                                                <i class="bi bi-arrow-repeat"></i><span>สถานะ</span>
                                            </button>
                                        </form>

                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline js-confirm-delete" data-user-name="{{ $user->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn ump-btn-action ump-btn-delete">
                                                <i class="bi bi-trash3-fill"></i><span>ลบ</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="ump-empty-state">
                                        <i class="bi bi-people"></i>
                                        <div class="ump-empty-title">ยังไม่มีข้อมูลผู้ใช้งาน</div>
                                        <div class="ump-empty-subtitle">กดปุ่มเพิ่มผู้ใช้งานเพื่อเริ่มกำหนดสิทธิ์</div>
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
.user-manage-page{
    --ump-primary:#2563eb;
    --ump-border:#e5edf5;
    --ump-text:#0f172a;
    --ump-muted:#64748b;
}
.user-manage-page .ump-header-card,
.user-manage-page .ump-card,
.user-manage-page .ump-stat-card{
    background:#fff;
    border:1px solid var(--ump-border);
    box-shadow:0 10px 30px rgba(15,23,42,.045);
}
.user-manage-page .ump-header-card{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:1rem;
    flex-wrap:wrap;
    padding:1.25rem;
    border-radius:24px;
    margin-bottom:1rem;
}
.user-manage-page .ump-title-wrap,
.user-manage-page .ump-user-cell,
.user-manage-page .ump-stat-card{
    display:flex;
    align-items:center;
    gap:.85rem;
}
.user-manage-page .ump-title-icon{
    width:56px;
    height:56px;
    border-radius:18px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:1.25rem;
    background:linear-gradient(135deg,#1d4ed8,#3b82f6);
    box-shadow:0 12px 24px rgba(37,99,235,.2);
}
.user-manage-page .ump-page-title{font-weight:800;color:var(--ump-text);font-size:1.35rem}
.user-manage-page .ump-page-subtitle{color:var(--ump-muted);font-size:.92rem}
.user-manage-page .ump-btn-primary{
    display:inline-flex;align-items:center;justify-content:center;gap:.5rem;
    border:0;border-radius:999px;padding:.78rem 1.1rem;color:#fff;font-weight:800;
    background:linear-gradient(135deg,#1d4ed8,#3b82f6);box-shadow:0 10px 24px rgba(37,99,235,.2)
}
.user-manage-page .ump-btn-primary:hover{color:#fff;transform:translateY(-1px)}
.user-manage-page .ump-alert{border-radius:18px;padding:.95rem 1rem}
.user-manage-page .ump-stat-card{min-height:92px;padding:1rem;border-radius:20px}
.user-manage-page .ump-stat-icon{
    width:46px;height:46px;border-radius:15px;display:inline-flex;align-items:center;justify-content:center;
    color:#1d4ed8;background:#dbeafe;font-size:1.1rem;flex:0 0 auto
}
.user-manage-page .ump-stat-icon-green{color:#047857;background:#d1fae5}
.user-manage-page .ump-stat-icon-purple{color:#7e22ce;background:#f3e8ff}
.user-manage-page .ump-stat-icon-orange{color:#c2410c;background:#ffedd5}
.user-manage-page .ump-stat-label{color:var(--ump-muted);font-size:.84rem;font-weight:700}
.user-manage-page .ump-stat-value{color:var(--ump-text);font-size:1.35rem;font-weight:900;line-height:1.15}
.user-manage-page .ump-card{border-radius:24px;overflow:hidden}
.user-manage-page .ump-card-head{display:flex;align-items:center;justify-content:space-between;gap:.8rem;flex-wrap:wrap;padding:1.1rem 1.25rem;border-bottom:1px solid var(--ump-border)}
.user-manage-page .ump-card-title{display:flex;align-items:center;gap:.55rem;font-weight:800;color:var(--ump-text)}
.user-manage-page .ump-card-title i{color:var(--ump-primary)}
.user-manage-page .ump-card-subtitle{color:var(--ump-muted);font-size:.86rem;margin-top:.15rem}
.user-manage-page .ump-safe-note{display:inline-flex;align-items:center;gap:.45rem;padding:.55rem .8rem;border-radius:999px;color:#166534;background:#f0fdf4;border:1px solid #bbf7d0;font-size:.82rem;font-weight:700}
.user-manage-page .ump-card-body{padding:1rem}
.user-manage-page .ump-table-wrap{width:100%;overflow-x:auto;border-radius:18px;-webkit-overflow-scrolling:touch}
.user-manage-page .ump-table{min-width:1450px}
.user-manage-page .ump-table thead th{background:#f8fafc;color:#475569;font-size:.88rem;font-weight:800;white-space:nowrap;padding:.95rem .8rem;border-bottom:1px solid #eaf0f6}
.user-manage-page .ump-table tbody td{padding:.95rem .8rem;border-color:#eef2f7;background:#fff}
.user-manage-page .ump-table tbody tr:hover td{background:#fcfdff}
.user-manage-page .ump-user-cell{min-width:210px}
.user-manage-page .ump-user-avatar{width:46px;height:46px;border-radius:50%;object-fit:cover;border:2px solid #e5e7eb;background:#fff}
.user-manage-page .ump-user-name{font-weight:800;color:var(--ump-text)}
.user-manage-page .ump-user-id{font-size:.8rem;color:var(--ump-muted)}
.user-manage-page .ump-email{min-width:180px;color:#334155;word-break:break-word}
.user-manage-page .ump-role-badge,
.user-manage-page .ump-project-badge,
.user-manage-page .ump-house-badge,
.user-manage-page .ump-permission-badge,
.user-manage-page .ump-status-badge{
    display:inline-flex;align-items:center;gap:.38rem;border-radius:999px;font-size:.82rem;font-weight:800;white-space:nowrap
}
.user-manage-page .ump-role-badge{padding:.48rem .75rem;color:#334155;background:#fff;border:1px solid #dbe3ee}
.user-manage-page .ump-project-badge{padding:.48rem .72rem;color:#1e40af;background:#eff6ff;border:1px solid #bfdbfe}
.user-manage-page .ump-house-list{display:flex;align-items:center;gap:.35rem;flex-wrap:wrap;min-width:210px}
.user-manage-page .ump-house-badge{padding:.42rem .65rem;color:#0f766e;background:#f0fdfa;border:1px solid #99f6e4}
.user-manage-page .ump-permission-badge{padding:.48rem .72rem}
.user-manage-page .ump-permission-full{color:#166534;background:#f0fdf4;border:1px solid #bbf7d0}
.user-manage-page .ump-permission-legacy{color:#92400e;background:#fffbeb;border:1px solid #fde68a}
.user-manage-page .ump-permission-custom{color:#5b21b6;background:#f5f3ff;border:1px solid #ddd6fe}
.user-manage-page .ump-status-badge{min-width:86px;justify-content:center;padding:.48rem .75rem}
.user-manage-page .ump-status-active{color:#15803d;background:#ecfdf3;border:1px solid #bbf7d0}
.user-manage-page .ump-status-inactive{color:#dc2626;background:#fef2f2;border:1px solid #fecaca}
.user-manage-page .ump-empty-text{color:#94a3b8;font-size:.84rem}
.user-manage-page .ump-action-group{display:flex;align-items:center;justify-content:center;gap:.38rem;flex-wrap:wrap;min-width:260px}
.user-manage-page .ump-btn-action{display:inline-flex;align-items:center;justify-content:center;gap:.35rem;min-width:80px;padding:.54rem .72rem;border-radius:999px;font-size:.82rem;font-weight:800;border:1px solid transparent;white-space:nowrap}
.user-manage-page .ump-btn-edit{color:#c2410c;background:#fff7ed;border-color:#fed7aa}
.user-manage-page .ump-btn-status{color:#1d4ed8;background:#eff6ff;border-color:#bfdbfe}
.user-manage-page .ump-btn-delete{color:#b91c1c;background:#fef2f2;border-color:#fecaca}
.user-manage-page .ump-empty-state{min-height:250px;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--ump-muted);text-align:center}
.user-manage-page .ump-empty-state i{font-size:2.2rem;color:#94a3b8;margin-bottom:.6rem}
.user-manage-page .ump-empty-title{font-weight:800;color:var(--ump-text)}
.user-manage-page .ump-empty-subtitle{font-size:.88rem;margin-top:.2rem}
.user-manage-page div.dataTables_wrapper div.dataTables_length,
.user-manage-page div.dataTables_wrapper div.dataTables_filter{margin-bottom:.85rem}
.user-manage-page div.dataTables_wrapper div.dataTables_filter input{border:1px solid #dbe3ee!important;border-radius:999px!important;padding:.48rem .85rem!important;box-shadow:none!important}
.user-manage-page div.dataTables_wrapper div.dataTables_length select{border:1px solid #dbe3ee!important;border-radius:12px!important;box-shadow:none!important}
@media(max-width:767.98px){
    .user-manage-page .ump-header-card{align-items:stretch}
    .user-manage-page .ump-btn-primary{width:100%}
    .user-manage-page .ump-safe-note{border-radius:14px;white-space:normal}
    .user-manage-page div.dataTables_wrapper div.dataTables_filter,
    .user-manage-page div.dataTables_wrapper div.dataTables_length{text-align:left!important}
    .user-manage-page div.dataTables_wrapper div.dataTables_filter input{width:100%!important;margin:.4rem 0 0!important}
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    @if($users->isNotEmpty())
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#usersTable').DataTable({
            pageLength: 10,
            lengthMenu: [[10,25,50,100],[10,25,50,100]],
            responsive: false,
            autoWidth: false,
            scrollX: true,
            language: {
                search: 'ค้นหา:',
                lengthMenu: 'แสดง _MENU_ รายการต่อหน้า',
                info: 'แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ',
                infoEmpty: 'แสดง 0 ถึง 0 จาก 0 รายการ',
                zeroRecords: 'ไม่พบข้อมูลที่ค้นหา',
                paginate: { first:'แรก', last:'สุดท้าย', next:'ถัดไป', previous:'ก่อนหน้า' }
            },
            columnDefs: [{ orderable:false, targets:[5,6,8] }]
        });
    }
    @endif

    function bindConfirmation(selector, options) {
        document.querySelectorAll(selector).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (form.dataset.confirmed === '1') return;
                event.preventDefault();

                const userName = form.dataset.userName || 'ผู้ใช้งานนี้';

                if (typeof Swal === 'undefined') {
                    if (window.confirm(options.fallback.replace(':name', userName))) {
                        form.dataset.confirmed = '1';
                        form.submit();
                    }
                    return;
                }

                Swal.fire({
                    icon: options.icon,
                    title: options.title,
                    html: options.html.replace(':name', '<strong>' + userName + '</strong>'),
                    showCancelButton: true,
                    confirmButtonText: options.confirmText,
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: options.confirmColor,
                    reverseButtons: true,
                    focusCancel: true
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.dataset.confirmed = '1';
                        form.submit();
                    }
                });
            });
        });
    }

    bindConfirmation('.js-confirm-status', {
        icon:'question',
        title:'ยืนยันการเปลี่ยนสถานะ',
        html:'ต้องการเปลี่ยนสถานะของ :name หรือไม่?',
        confirmText:'ยืนยันเปลี่ยนสถานะ',
        confirmColor:'#2563eb',
        fallback:'ยืนยันการเปลี่ยนสถานะของ :name หรือไม่?'
    });

    bindConfirmation('.js-confirm-delete', {
        icon:'warning',
        title:'ยืนยันการลบผู้ใช้งาน',
        html:'ข้อมูลของ :name จะถูกลบออกจากระบบ',
        confirmText:'ยืนยันลบ',
        confirmColor:'#dc2626',
        fallback:'ยืนยันการลบ :name หรือไม่?'
    });
});
</script>
@endsection
