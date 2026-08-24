@php
    use Illuminate\Support\Facades\Request;
    use App\Support\FormPermissionMenu;

    $currentUser = auth()->user();
    $permissionMenu = FormPermissionMenu::forUser($currentUser);
    $formAccess = $permissionMenu['forms'];
    $groupAccess = $permissionMenu['groups'];

    $canForm = static fn (string $key): bool => (bool) ($formAccess[$key] ?? false);
    $canCreate = static fn (string $key): bool => (bool) ($currentUser?->canCreateForm($key) ?? false);
    $canManageSystemUsers = (bool) ($currentUser && ($currentUser->isAdmin() || $currentUser->isExecutive()));

    $showRegistrationMenu = (bool) ($permissionMenu['has_any_client_form'] ?? false)
        || ($groupAccess['registration_central'] ?? false);
    $showDashboardMenu = (bool) ($groupAccess['dashboard'] ?? false);
    $showMasterMenu = (bool) ($groupAccess['master_data'] ?? false);
    $showSystemMenu = $canManageSystemUsers
    || $canForm('system_audit_logs');

    $isProfileOpen = Request::routeIs('client.show')
        || Request::routeIs('client.cases')
        || Request::routeIs('client.transfers')
        || Request::routeIs('client.transfer.*')
        || Request::routeIs('client-house-transfers.*');

    $isDashboardOpen = Request::routeIs('dashboard')
        || Request::routeIs('statistics.*')
        || Request::routeIs('issues.index')
        || Request::routeIs('news.create')
        || Request::routeIs('landing.about.index')
        || Request::routeIs('scholarship.index')
        || Request::routeIs('scholarship.children.*')
        || Request::routeIs('child.analytics.report.index');

    $isMasterMenuOpen = Request::routeIs('institution.*')
        || Request::routeIs('subject.*')
        || Request::routeIs('house.*')
        || Request::routeIs('education.*')
        || Request::routeIs('semester.*')
         || Request::routeIs('project.*')
        || Request::routeIs('psycho.*')
        || Request::routeIs('misbehavior.*')
        || Request::routeIs('outside.*')
        || Request::routeIs('document.*')
        || Request::routeIs('income.*')
        || Request::routeIs('help_type.*')
        || Request::routeIs('citizenship.*')
        || Request::routeIs('citizen.*')
        || Request::routeIs('translate.*');

   $isUserMenu = Request::routeIs('users.*')
    || Request::routeIs('audit_logs.*');
    $isIdstationCentralMenu = Request::routeIs('idstation.central.*');
    $isIndividualDevelopmentCentral = Request::routeIs('individual-development.center');
@endphp

{{-- FORM_PERMISSION_MENU_V6: MAIN_SIDEBAR --}}
<style>
    .app-sidebar-menu.sidebar-arrow-fix #side-menu li > a { display:flex; align-items:center; gap:10px; position:relative; }
    .app-sidebar-menu.sidebar-arrow-fix #side-menu li > a::after,
    .app-sidebar-menu.sidebar-arrow-fix .metismenu .has-arrow::after,
    .app-sidebar-menu.sidebar-arrow-fix .metismenu .menu-arrow::after,
    .app-sidebar-menu.sidebar-arrow-fix .metismenu .menu-arrow::before { content:none !important; display:none !important; }
    .app-sidebar-menu.sidebar-arrow-fix .menu-arrow { margin-left:auto; width:10px; min-width:10px; height:10px; position:relative; }
    .app-sidebar-menu.sidebar-arrow-fix .menu-arrow-custom::before { content:""; position:absolute; top:50%; left:50%; width:7px; height:7px; border-right:2px solid #64748b; border-bottom:2px solid #64748b; transform:translate(-50%,-58%) rotate(-45deg); transition:transform .2s ease; }
    .app-sidebar-menu.sidebar-arrow-fix a[aria-expanded="true"] .menu-arrow-custom::before { transform:translate(-50%,-42%) rotate(45deg); }
    .sidebar-badge-soft { display:inline-flex; align-items:center; justify-content:center; margin-left:6px; padding:2px 8px; border-radius:999px; font-size:11px; font-weight:600; background:rgba(59,130,246,.12); color:#2563eb; }
</style>

<div class="app-sidebar-menu sidebar-arrow-fix" id="stableMasterSidebar">
    <div class="h-100" data-simplebar>
        <div id="sidebar-menu">
            <div class="logo-box">
                <a href="{{ url('/') }}" class="logo logo-light">
                    <span class="logo-sm"><img src="{{ asset('backend/assets/images/logo-sm.png') }}" height="22" alt="logo-sm"></span>
                    <span class="logo-lg"><img src="{{ asset('backend/assets/images/logo-light.png') }}" height="24" alt="logo-lg"></span>
                </a>
            </div>

            <ul id="side-menu" class="metismenu list-unstyled pt-2">
                @if($showRegistrationMenu)
                    <li class="menu-title">ทะเบียนประวัติ</li>
                    <li>
                        <a href="#sidebarProfile" data-bs-toggle="collapse" aria-expanded="{{ $isProfileOpen ? 'true' : 'false' }}" class="{{ $isProfileOpen ? 'active' : '' }}">
                            <i data-feather="users"></i>
                            <span>บันทึกข้อมูลแรกเข้า</span>
                            <span class="menu-arrow menu-arrow-custom"></span>
                        </a>
                        <div class="collapse {{ $isProfileOpen ? 'show' : '' }}" id="sidebarProfile">
                            <ul class="nav-second-level">
                                @if($permissionMenu['has_any_client_form'] ?? false)
                                    <li>
                                        <a href="{{ route('client.show') }}" class="tp-link {{ Request::routeIs('client.show') ? 'active' : '' }}">
                                            <i class="bi bi-people-fill me-2"></i>ทะเบียนผู้รับบริการ
                                        </a>
                                    </li>
                                @endif
                                @if($canForm('registration_central_cases'))
                                    <li><a href="{{ route('client.cases') }}" class="tp-link {{ Request::routeIs('client.cases') ? 'active' : '' }}">ทะเบียนกลางเคสทั้งหมด</a></li>
                                @endif
                                @if($canForm('registration_project_transfer'))
                                    <li><a href="{{ route('client.transfers') }}" class="tp-link {{ Request::routeIs('client.transfers') || Request::routeIs('client.transfer.*') ? 'active' : '' }}">ย้ายโครงการ</a></li>
                                @endif
                                @if($canForm('registration_house_transfer'))
                                    <li><a href="{{ route('client-house-transfers.index') }}" class="tp-link {{ Request::routeIs('client-house-transfers.*') ? 'active' : '' }}">ย้ายสถานที่พักพิง</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if($showDashboardMenu)
                    <li class="menu-title">Dashboard</li>
                    <li>
                        <a href="#sidebarDashboard" data-bs-toggle="collapse" aria-expanded="{{ $isDashboardOpen ? 'true' : 'false' }}" class="{{ $isDashboardOpen ? 'active' : '' }}">
                            <i data-feather="layout"></i>
                            <span>เข้าสู่หน้ายินดีต้อนรับ</span>
                            <span class="menu-arrow menu-arrow-custom"></span>
                        </a>
                        <div class="collapse {{ $isDashboardOpen ? 'show' : '' }}" id="sidebarDashboard">
                            <ul class="nav-second-level">
                                @if($canForm('dashboard_overview'))
                                    <li><a href="{{ route('dashboard') }}" class="tp-link {{ Request::routeIs('dashboard') || Request::routeIs('statistics.*') ? 'active' : '' }}">ระบบผู้รับบริการ</a></li>
                                @endif
                                @if($canForm('dashboard_issues'))
                                    <li><a href="{{ route('issues.index') }}" class="tp-link {{ Request::routeIs('issues.index') ? 'active' : '' }}">แจ้งเรื่องช่วยเหลือ</a></li>
                                @endif
                                @if($canForm('dashboard_news'))
                                    <li><a href="{{ route('news.create') }}" class="tp-link {{ Request::routeIs('news.create') ? 'active' : '' }}">เพิ่มข่าวสาร</a></li>
                                @endif
                                @if($canForm('dashboard_about'))
                                    <li><a href="{{ route('landing.about.index') }}" class="tp-link {{ Request::routeIs('landing.about.index') ? 'active' : '' }}">ประวัติความเป็นมา</a></li>
                                @endif
                                @if($canForm('dashboard_scholarship_sponsors'))
                                    <li><a href="{{ route('scholarship.index') }}" class="tp-link {{ Request::routeIs('scholarship.index') ? 'active' : '' }}">ผู้สนับสนุนทุนการศึกษา</a></li>
                                @endif
                                @if($canForm('dashboard_scholarship_children'))
                                    <li><a href="{{ route('scholarship.children.index') }}" class="tp-link {{ Request::routeIs('scholarship.children.*') ? 'active' : '' }}">ทุนการศึกษาเด็ก</a></li>
                                @endif
                                @if($canForm('dashboard_child_analytics'))
                                    <li><a href="{{ route('child.analytics.report.index') }}#overview" class="tp-link {{ Request::routeIs('child.analytics.report.index') ? 'active' : '' }}">รายงานวิเคราะห์ข้อมูลเด็ก</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if($canForm('individual_development_center') && Route::has('individual-development.center'))
                    <li class="menu-title mt-2">แผนพัฒนาเด็ก</li>
                    <li><a href="{{ route('individual-development.center') }}" class="{{ $isIndividualDevelopmentCentral ? 'active' : '' }}"><i class="bi bi-diagram-3 sidebar-fa-icon"></i><span>ศูนย์กลางการพัฒนาเด็ก</span></a></li>
                @endif

                @if($canForm('welfare_stateless_person'))
                    <li class="menu-title">ศูนย์กลางทะเบียน</li>
                    <li>
                        <a href="#sidebarIdstationCentral" data-bs-toggle="collapse" aria-expanded="{{ $isIdstationCentralMenu ? 'true' : 'false' }}" class="{{ $isIdstationCentralMenu ? 'active' : '' }}">
                            <i data-feather="database"></i><span>บุคคลไร้สัญชาติ</span><span class="menu-arrow menu-arrow-custom"></span>
                        </a>
                        <div class="collapse {{ $isIdstationCentralMenu ? 'show' : '' }}" id="sidebarIdstationCentral">
                            <ul class="nav-second-level">
                                <li><a href="{{ route('idstation.central.index') }}" class="tp-link {{ Request::routeIs('idstation.central.index') ? 'active' : '' }}">ศูนย์กลางข้อมูล</a></li>
                                @if($currentUser?->canPrintForm('welfare_stateless_person'))
                                    <li><a href="{{ route('idstation.central.report') }}" class="tp-link {{ Request::routeIs('idstation.central.report') ? 'active' : '' }}">รายงานสรุป</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if($showMasterMenu)
                    <li class="menu-title">ข้อมูลอ้างอิง</li>
                    <li>
                        <a href="#sidebar-master-data" data-bs-toggle="collapse" aria-expanded="{{ $isMasterMenuOpen ? 'true' : 'false' }}" class="{{ $isMasterMenuOpen ? 'active' : '' }}">
                            <i data-feather="grid"></i><span>ประเภท / หมวดหมู่</span><span class="menu-arrow menu-arrow-custom"></span>
                        </a>
                        <div class="collapse {{ $isMasterMenuOpen ? 'show' : '' }}" id="sidebar-master-data">
                            <ul class="nav-second-level">
                                @if($canForm('master_institutions'))<li><a href="{{ route('institution.all') }}" class="tp-link">รายการสถานศึกษา</a></li>@endif
                                @if($canForm('master_subjects'))<li><a href="{{ route('subject.show') }}" class="tp-link">รายการวิชาเรียน</a></li>@endif
                                @if($canForm('master_houses'))<li><a href="{{ route('house.show') }}" class="tp-link">รายการบ้านพัก</a></li>@endif
                                @if($canForm('master_education_levels'))<li><a href="{{ route('education.show') }}" class="tp-link">รายการระดับการศึกษา</a></li>@endif
                                @if($canForm('master_semesters'))<li><a href="{{ route('semester.show') }}" class="tp-link">รายการปีการศึกษา</a></li>@endif
                                @if($canForm('master_projects'))<li><a href="{{ route('project.show') }}" class="tp-link">รายการโครงการ</a></li>@endif
                                @if($canForm('master_psychiatric_diseases'))<li><a href="{{ route('psycho.show') }}" class="tp-link">รายการโรคทางจิตเวช</a></li>@endif
                                @if($canForm('master_behaviors'))<li><a href="{{ route('misbehavior.show') }}" class="tp-link">รายการพฤติกรรม</a></li>@endif
                                @if($canForm('master_outside_types'))<li><a href="{{ route('outside.show') }}" class="tp-link">รายการเด็กที่อยู่ภายนอก</a></li>@endif
                                @if($canForm('master_documents'))<li><a href="{{ route('document.show') }}" class="tp-link">รายการเอกสาร</a></li>@endif
                                @if($canForm('master_incomes'))<li><a href="{{ route('income.show') }}" class="tp-link">รายการรายได้</a></li>@endif
                                @if($canForm('master_help_types'))<li><a href="{{ route('help_type.show') }}" class="tp-link">ประเภทการช่วยเหลือ</a></li>@endif
                                @if($canForm('master_citizenships'))<li><a href="{{ route('citizenship.show') }}" class="tp-link">รายการทางทะเบียน</a></li>@endif
                                @if($canForm('master_citizen_statuses'))<li><a href="{{ route('citizen.show') }}" class="tp-link">ได้รับสถานะทางทะเบียน</a></li>@endif
                                @if($canForm('master_release_types'))<li><a href="{{ route('translate.show') }}" class="tp-link">ประเภทการพ้นอุปการะ</a></li>@endif
                            </ul>
                        </div>
                    </li>
                @endif

                {{-- SPECIAL_CHILDREN_REPORT_V1 --}}
                @if($canForm('health_body_check') && Route::has('special_children.index'))
                    <li>
                        <a href="{{ route('special_children.index') }}"
                           class="{{ Request::routeIs('special_children.*') ? 'active' : '' }}">
                            <i class="bi bi-person-hearts"></i>
                            <span>เด็กกลุ่มพิเศษ</span>
                        </a>
                    </li>
                @endif
                <li class="menu-title mt-2">ประชาสัมพันธ์</li>
                <li><a href="{{ route('publicizes.index') }}" class="tp-link {{ Request::routeIs('publicizes.*') ? 'active' : '' }}"><i class="bi bi-megaphone me-1"></i>ข่าวสาร/กิจกรรม</a></li>
                <li><a href="{{ route('operations.index') }}" class="nav-link {{ Request::routeIs('operations.index') ? 'active' : '' }}"><i class="bi bi-journal-text me-2"></i><span>บันทึกการปฏิบัติงาน</span></a></li>
                <li><a href="{{ route('operations.report.daily') }}" class="nav-link {{ Request::routeIs('operations.report.daily') ? 'active' : '' }}"><i class="bi bi-file-earmark-text me-2"></i><span>รายงานการปฏิบัติงาน</span></a></li>

                @if($canForm('report_discharge_all'))
                    <li><a href="{{ route('refers.all') }}" class="nav-link {{ Request::routeIs('refers.all') ? 'active' : '' }}"><i class="bi bi-box-arrow-right me-2"></i><span>รายงานการจำหน่าย</span></a></li>
                @endif

              @if($showSystemMenu)
    <li class="menu-title mt-2">การจัดการระบบ</li>

    <li>
        <a href="#sidebarUsers"
           data-bs-toggle="collapse"
           aria-expanded="{{ $isUserMenu ? 'true' : 'false' }}"
           class="{{ $isUserMenu ? 'active' : '' }}">

            <i class="bi bi-people-fill"></i>
            <span>จัดการผู้ใช้งาน</span>
            <span class="menu-arrow menu-arrow-custom"></span>
        </a>

        <div class="collapse {{ $isUserMenu ? 'show' : '' }}"
             id="sidebarUsers">

            <ul class="nav-second-level">

                {{-- รายชื่อผู้ใช้งาน --}}
                @if($canManageSystemUsers)
                    <li>
                        <a href="{{ route('users.index') }}"
                           class="tp-link {{ Request::routeIs('users.index') || Request::routeIs('users.edit') ? 'active' : '' }}">
                            รายชื่อผู้ใช้งาน
                        </a>
                    </li>
                @endif

                {{-- เพิ่มผู้ใช้งาน --}}
                @if($canManageSystemUsers)
                    <li>
                        <a href="{{ route('users.create') }}"
                           class="tp-link {{ Request::routeIs('users.create') ? 'active' : '' }}">
                            เพิ่มผู้ใช้งาน
                        </a>
                    </li>
                @endif

                {{-- ประวัติการใช้งานระบบ --}}
                @if($canForm('system_audit_logs'))
                    <li>
                        <a href="{{ route('audit_logs.index') }}"
                           class="tp-link {{ Request::routeIs('audit_logs.*') ? 'active' : '' }}">
                            ประวัติการใช้งานระบบ
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </li>
@endif

                <li class="menu-title mt-2">ระบบ</li>
                <li>
                    {{-- DASHBOARD_USER_MENU_LOGOUT_HOTFIX_V1: session action, not a form write permission --}}
                    <form method="POST"
                          action="{{ route('admin.logout') }}"
                          class="m-0"
                          data-permission-action="navigation">
                        @csrf
                        <button type="submit" class="sidebar-logout-btn">
                            <i data-feather="log-out"></i><span>ออกจากระบบ</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>



<style>
.sidebar-logout-btn{
    display:flex;
    align-items:center;
    width:100%;
    gap:10px;
    padding:10px 20px;
    border:0;
    background:transparent;
    color:inherit;
    text-align:left;
    cursor:pointer;
}
.sidebar-logout-btn:hover,
.sidebar-logout-btn:focus{
    color:inherit;
    background:rgba(255,255,255,.04);
}
.sidebar-logout-btn svg{
    width:18px;
    height:18px;
}
</style>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('stableMasterSidebar');
        if (!sidebar) return;
        if (window.feather) { try { feather.replace(); } catch (e) {} }
        sidebar.classList.add('sidebar-icons-ready');
    });
</script>
