@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Request;
    use App\Support\FormPermissionMenu;

    $profileData = Auth::user();
    $permissionMenu = FormPermissionMenu::forUser($profileData);
    $formAccess = $permissionMenu['forms'];
    $groupAccess = $permissionMenu['groups'];

    $canForm = static fn (string $key): bool => (bool) ($formAccess[$key] ?? false);

    $routeClient = request()->route('client')
        ?? request()->route('client_id')
        ?? null;

    $clientId = null;

    if (isset($client) && is_object($client)) {
        $clientId = $client->id ?? null;
    } elseif (isset($client) && is_numeric($client)) {
        $clientId = (int) $client;
    } elseif (is_object($routeClient)) {
        $clientId = $routeClient->id ?? null;
    } elseif (is_numeric($routeClient)) {
        $clientId = (int) $routeClient;
    }

    $isDashboardActive =
        Request::routeIs('admin.index') ||
        Request::routeIs('dashboard') ||
        Request::routeIs('statistics.index');

    $isHistoryActive =
        Request::routeIs('client.edit') ||
        Request::routeIs('factfinding.*') ||
        Request::routeIs('family.*') ||
        Request::routeIs('visitFamily.*') ||
        Request::routeIs('vitsitFamily.*') ||
        Request::routeIs('member.*') ||
        Request::routeIs('estimate.*') ||
        Request::routeIs('client_files.*') ||
        Request::routeIs('client.report*');

    $isEducationActive =
        Request::routeIs('education_record*') ||
        Request::routeIs('school_followup*') ||
        Request::routeIs('absent.*');

    $isHealthActive =
        Request::routeIs('accident.*') ||
        Request::routeIs('check_body.*') ||
        Request::routeIs('medical.*') ||
        Request::routeIs('vaccine.*') ||
        Request::routeIs('psychiatric.*') ||
        Request::routeIs('addictive.*') ||
        Request::routeIs('healthc_heckups.*');

    $isAssessmentActive =
        Request::routeIs('behavior-screenings.*') ||
        Request::routeIs('snap-iv.*') ||
        Request::routeIs('depression-screenings.*') ||
        Request::routeIs('nutrition_assessments.*');

    $isSocialActive =
        Request::routeIs('observe.*') ||
        Request::routeIs('escape.*') ||
        Request::routeIs('case_outside.*') ||
        Request::routeIs('refers.*') ||
        Request::routeIs('job_agencies.*') ||
        Request::routeIs('help_sessions.*') ||
        Request::routeIs('followup.*') ||
        Request::routeIs('case-activities.*') ||
        Request::routeIs('idstation.*');

    $isStatelessClient = isset($client)
        && is_object($client)
        && optional($client->target)->target_name === 'บุคคลไม่มีสถานะทางทะเบียน';
@endphp

{{-- FORM_PERMISSION_MENU_V3: TOPBAR --}}
<style>
    #appTopbar .topbar-menu>.nav-item>.topbar-link>span,
    #appTopbar .topbar-menu>.nav-item>.topbar-link.dropdown-toggle>span {
        font-family: 'Kanit', sans-serif !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        line-height: 1.2 !important;
        letter-spacing: .01em !important;
        color: #1e3a5f !important;
        display: inline-block !important;
        vertical-align: middle !important;
        text-rendering: geometricPrecision;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    #appTopbar .topbar-menu>.nav-item>.topbar-link.active>span,
    #appTopbar .topbar-menu>.nav-item>.topbar-link:hover>span,
    #appTopbar .topbar-menu>.nav-item>.topbar-link:focus>span,
    #appTopbar .topbar-menu>.nav-item>.topbar-link.dropdown-toggle.active>span,
    #appTopbar .topbar-menu>.nav-item>.topbar-link.dropdown-toggle:hover>span,
    #appTopbar .topbar-menu>.nav-item>.topbar-link.dropdown-toggle:focus>span {
        font-family: 'Kanit', sans-serif !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        line-height: 1.2 !important;
        letter-spacing: .01em !important;
    }

    #appTopbar .topbar-menu>.nav-item>.topbar-link {
        display: inline-flex !important;
        align-items: center !important;
        gap: .55rem !important;
    }

    #appTopbar .topbar-menu>.nav-item>.topbar-link>i {
        flex: 0 0 auto;
    }

    @media (max-width: 767.98px) {
        #appTopbar .topbar-menu>.nav-item>.topbar-link>span,
        #appTopbar .topbar-menu>.nav-item>.topbar-link.dropdown-toggle>span,
        #appTopbar .topbar-menu>.nav-item>.topbar-link.active>span,
        #appTopbar .topbar-menu>.nav-item>.topbar-link:hover>span,
        #appTopbar .topbar-menu>.nav-item>.topbar-link:focus>span {
            font-family: 'Kanit', sans-serif !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            line-height: 1.2 !important;
            letter-spacing: .01em !important;
            color: #1e3a5f !important;
        }
    }
</style>

<div class="topbar-custom app-topbar" id="appTopbar">
    <div class="container-fluid px-2 px-lg-3">
        <nav class="navbar navbar-expand-xl navbar-light topbar-navbar">
            <div class="d-flex align-items-center topbar-left-group">
                <button class="navbar-toggler topbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <button type="button" id="clientSidebarToggle"
                    class="button-toggle-menu client-sidebar-toggle topbar-sidebar-toggle border-0 bg-transparent d-none d-xl-inline-flex"
                    aria-label="เปิดหรือปิดเมนูด้านข้าง" aria-controls="clientSidebarPanel" aria-expanded="true">
                    <i data-feather="menu" class="topbar-icon"></i>
                </button>

                <a href="{{ (auth()->user()?->canViewForm('dashboard_overview') ? route('dashboard') : route('client.show')) }}" class="topbar-brand d-none d-md-flex">
                    <span class="topbar-brand-badge"><i class="fas fa-people-group"></i></span>
                    <span class="topbar-brand-text">หน้าระบบผู้รับบริการ</span>
                </a>
            </div>

            <div class="collapse navbar-collapse topbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav topbar-menu mb-2 mb-xl-0">
                    <li class="nav-item">
                        <a class="nav-link topbar-link {{ $isDashboardActive ? 'active' : '' }}"
                           href="{{ $clientId ? route('admin.index', $clientId) : (auth()->user()?->canViewForm('dashboard_overview') ? route('dashboard') : route('client.show')) }}">
                            <i class="fas fa-home"></i><span>หน้าหลัก</span>
                        </a>
                    </li>

                    @if ($groupAccess['registration'] ?? false)
                        <li class="nav-item dropdown">
                            <a class="nav-link topbar-link dropdown-toggle {{ $isHistoryActive ? 'active' : '' }}"
                               href="#" id="historyDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-book"></i><span>ทะเบียนแรกเข้า</span>
                            </a>
                            <ul class="dropdown-menu topbar-dropdown" aria-labelledby="historyDropdown">
                                @if ($clientId && $canForm('registration_client_profile'))
                                    <li><a class="dropdown-item {{ Request::routeIs('client.edit') ? 'active' : '' }}"
                                           href="{{ route('client.edit', $clientId) }}">ประวัติผู้รับบริการ</a></li>
                                @endif
                                @if ($clientId && $canForm('registration_factfinding'))
                                    <li><a class="dropdown-item {{ Request::routeIs('factfinding.*') ? 'active' : '' }}"
                                           href="{{ route('factfinding.add', $clientId) }}">สอบข้อเท็จจริงเบื้องต้น</a></li>
                                @endif
                                @if ($clientId && $canForm('registration_family'))
                                    <li><a class="dropdown-item {{ Request::routeIs('family.*') || Request::routeIs('estimate.*') ? 'active' : '' }}"
                                           href="{{ route('family.add', $clientId) }}">บันทึกข้อมูลครอบครัว</a></li>
                                @endif
                                @if ($clientId && $canForm('registration_family_visit'))
                                    <li><a class="dropdown-item {{ Request::routeIs('visitFamily.*') || Request::routeIs('vitsitFamily.*') ? 'active' : '' }}"
                                           href="{{ route('visitFamily.create', $clientId) }}">เยี่ยมครอบครัว</a></li>
                                @endif
                                @if ($clientId && $canForm('registration_family_members'))
                                    <li><a class="dropdown-item {{ Request::routeIs('member.*') ? 'active' : '' }}"
                                           href="{{ route('member.create', $clientId) }}">บันทึกสมาชิกครอบครัว</a></li>
                                @endif
                                @if ($clientId && $canForm('registration_client_files'))
                                    <li><a class="dropdown-item {{ Request::routeIs('client_files.*') ? 'active' : '' }}"
                                           href="{{ route('client_files.index', $clientId) }}">จัดเก็บไฟล์เอกสาร</a></li>
                                @endif
                                @if ($clientId && $canForm('registration_client_reports'))
                                    <li><a class="dropdown-item {{ Request::routeIs('client.report*') ? 'active' : '' }}"
                                           href="{{ route('client.report', $clientId) }}">รายงานผู้รับบริการ</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if ($groupAccess['education'] ?? false)
                        <li class="nav-item dropdown">
                            <a class="nav-link topbar-link dropdown-toggle {{ $isEducationActive ? 'active' : '' }}"
                               href="#" id="educationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-graduation-cap"></i><span>การศึกษา</span>
                            </a>
                            <ul class="dropdown-menu topbar-dropdown" aria-labelledby="educationDropdown">
                                @if ($clientId && $canForm('education_grade_entry'))
                                    <li><a class="dropdown-item {{ Request::routeIs('education_record_add') ? 'active' : '' }}"
                                           href="{{ route('education_record_add', ['client_id' => $clientId]) }}">บันทึกผลการเรียน</a></li>
                                @endif
                                @if ($clientId && $canForm('education_results'))
                                    <li><a class="dropdown-item {{ Request::routeIs('education_record_show') ? 'active' : '' }}"
                                           href="{{ route('education_record_show', $clientId) }}">แสดงผลการเรียน</a></li>
                                @endif
                                @if ($clientId && $canForm('education_followup'))
                                    <li><a class="dropdown-item {{ Request::routeIs('school_followup*') ? 'active' : '' }}"
                                           href="{{ route('school_followup_add', $clientId) }}">ติดตามการศึกษา</a></li>
                                @endif
                                @if ($clientId && $canForm('education_absence'))
                                    <li><a class="dropdown-item {{ Request::routeIs('absent.*') ? 'active' : '' }}"
                                           href="{{ route('absent.add', $clientId) }}">บันทึกการขาดเรียน</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if ($groupAccess['health'] ?? false)
                        <li class="nav-item dropdown">
                            <a class="nav-link topbar-link dropdown-toggle {{ $isHealthActive ? 'active' : '' }}"
                               href="#" id="healthDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-heartbeat"></i><span>สุขภาพ</span>
                            </a>
                            <ul class="dropdown-menu topbar-dropdown" aria-labelledby="healthDropdown">
                                @if ($clientId && $canForm('health_accident'))
                                    <li><a class="dropdown-item {{ Request::routeIs('accident.*') ? 'active' : '' }}" href="{{ route('accident.add', $clientId) }}">บันทึกการบาดเจ็บ</a></li>
                                @endif
                                @if ($clientId && $canForm('health_body_check'))
                                    <li><a class="dropdown-item {{ Request::routeIs('check_body.*') ? 'active' : '' }}" href="{{ route('check_body.add', $clientId) }}">บันทึกการตรวจสุขภาพ</a></li>
                                @endif
                                @if ($clientId && $canForm('health_medical'))
                                    <li><a class="dropdown-item {{ Request::routeIs('medical.*') ? 'active' : '' }}" href="{{ route('medical.add', $clientId) }}">บันทึกการรักษาพยาบาล</a></li>
                                @endif
                                @if ($clientId && $canForm('health_vaccination'))
                                    <li><a class="dropdown-item {{ Request::routeIs('vaccine.*') ? 'active' : '' }}" href="{{ route('vaccine.index', $clientId) }}">ประวัติการรับวัคซีน</a></li>
                                @endif
                                @if ($clientId && $canForm('health_psychiatric'))
                                    <li><a class="dropdown-item {{ Request::routeIs('psychiatric.*') ? 'active' : '' }}" href="{{ route('psychiatric.create', $clientId) }}">การวินิจฉัยทางจิตเวช</a></li>
                                @endif
                                @if ($clientId && $canForm('health_addictive'))
                                    <li><a class="dropdown-item {{ Request::routeIs('addictive.*') ? 'active' : '' }}" href="{{ route('addictive.create', $clientId) }}">การตรวจสารเสพติด</a></li>
                                @endif
                                @if ($canForm('health_annual_checkup'))
                                    <li><a class="dropdown-item {{ Request::routeIs('healthc_heckups.*') ? 'active' : '' }}" href="{{ route('healthc_heckups.index') }}">ตรวจสุขภาพประจำปี</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if ($groupAccess['screening'] ?? false)
                        <li class="nav-item dropdown">
                            <a class="nav-link topbar-link dropdown-toggle {{ $isAssessmentActive ? 'active' : '' }}"
                               href="#" id="assessmentDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-clipboard-check"></i><span>แบบคัดกรอง</span>
                            </a>
                            <ul class="dropdown-menu topbar-dropdown" aria-labelledby="assessmentDropdown">
                                @if ($clientId && $canForm('screening_behavior_four_diseases'))
                                    <li><a class="dropdown-item {{ Request::routeIs('behavior-screenings.*') ? 'active' : '' }}" href="{{ route('behavior-screenings.index', $clientId) }}">แบบสังเกตพฤติกรรม 4 โรค</a></li>
                                @endif
                                @if ($clientId && $canForm('screening_snap_iv'))
                                    <li><a class="dropdown-item {{ Request::routeIs('snap-iv.*') ? 'active' : '' }}" href="{{ route('snap-iv.index', $clientId) }}">แบบประเมิน SNAP-IV</a></li>
                                @endif
                                @if ($clientId && $canForm('screening_depression'))
                                    <li><a class="dropdown-item {{ Request::routeIs('depression-screenings.*') ? 'active' : '' }}" href="{{ route('depression-screenings.index', $clientId) }}">แบบคัดกรองภาวะซึมเศร้า</a></li>
                                @endif
                                @if ($clientId && $canForm('screening_nutrition'))
                                    <li><a class="dropdown-item {{ Request::routeIs('nutrition_assessments.*') ? 'active' : '' }}" href="{{ route('nutrition_assessments.index', $clientId) }}">แบบประเมินภาวะโภชนาการ</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if ($groupAccess['social_welfare'] ?? false)
                        <li class="nav-item dropdown">
                            <a class="nav-link topbar-link dropdown-toggle {{ $isSocialActive ? 'active' : '' }}"
                               href="#" id="socialDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-users"></i><span>สังคมสงเคราะห์</span>
                            </a>
                            <ul class="dropdown-menu topbar-dropdown" aria-labelledby="socialDropdown">
                                @if ($clientId && $isStatelessClient && $canForm('welfare_stateless_person'))
                                    <li><a class="dropdown-item {{ Request::routeIs('idstation.*') ? 'active' : '' }}" href="{{ route('idstation.index', $clientId) }}">ช่วยเหลือด้านสถานะบุคคล</a></li>
                                @endif
                                @if ($clientId && $canForm('welfare_behavior_problem'))
                                    <li><a class="dropdown-item {{ Request::routeIs('observe.*') ? 'active' : '' }}" href="{{ route('observe.create', $clientId) }}">บันทึกปัญหาพฤติกรรม</a></li>
                                @endif
                                @if ($clientId && $canForm('welfare_escape'))
                                    <li><a class="dropdown-item {{ Request::routeIs('escape.*') ? 'active' : '' }}" href="{{ route('escape.index', $clientId) }}">การหลบหนีจากที่พักพิง</a></li>
                                @endif
                                @if ($clientId && $canForm('welfare_outside_followup'))
                                    <li><a class="dropdown-item {{ Request::routeIs('case_outside.*') ? 'active' : '' }}" href="{{ route('case_outside.show', $clientId) }}">การติดตามเด็กที่อยู่ภายนอก</a></li>
                                @endif
                                @if ($clientId && $canForm('welfare_discharge'))
                                    <li><a class="dropdown-item {{ Request::routeIs('refers.*') ? 'active' : '' }}" href="{{ route('refers.index', $clientId) }}">บันทึกการจำหน่าย</a></li>
                                @endif
                                @if ($clientId && $canForm('welfare_job_agency'))
                                    <li><a class="dropdown-item {{ Request::routeIs('job_agencies.*') ? 'active' : '' }}" href="{{ route('job_agencies.show', $clientId) }}">การหางานให้ผู้รับบริการ</a></li>
                                @endif
                                @if ($clientId && $canForm('welfare_help_items'))
                                    <li><a class="dropdown-item {{ Request::routeIs('help_sessions.*') ? 'active' : '' }}" href="{{ route('help_sessions.show', $clientId) }}">ช่วยเหลือสิ่งของ/เครื่องใช้</a></li>
                                @endif
                                @if ($clientId && $canForm('welfare_followup'))
                                    <li><a class="dropdown-item {{ Request::routeIs('followup.*') ? 'active' : '' }}" href="{{ route('followup.index', $clientId) }}">บันทึกการติดตาม</a></li>
                                @endif
                                @if ($clientId && $canForm('welfare_client_activity'))
                                    <li><a class="dropdown-item {{ Request::routeIs('case-activities.*') ? 'active' : '' }}" href="{{ route('case-activities.index', $clientId) }}">ความเคลื่อนไหวผู้รับบริการ</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                </ul>

                <ul class="navbar-nav ms-xl-auto align-items-xl-center topbar-profile-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link topbar-user dropdown-toggle" href="#" id="profileDropdown"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ !empty($profileData?->photo) ? url('upload/user_images/' . $profileData->photo) : url('upload/no_image.jpg') }}"
                                 alt="user-image" class="topbar-user-avatar">
                            <span class="topbar-user-meta d-none d-md-flex">
                                <span class="topbar-user-label">ผู้ใช้งาน</span>
                                <span class="topbar-user-name">{{ $profileData?->name ?? '-' }}</span>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end topbar-dropdown topbar-user-dropdown" aria-labelledby="profileDropdown">
                            <li><h6 class="dropdown-header">บัญชีผู้ใช้งาน</h6></li>
                            <li><a href="{{ route('admin.profile') }}" class="dropdown-item"><i class="fas fa-user-circle me-2"></i>ข้อมูลส่วนตัว</a></li>
                            <li><a href="auth-lock-screen.html" class="dropdown-item"><i class="fas fa-lock me-2"></i>ล็อกหน้าจอ</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a href="{{ route('admin.logout') }}" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>

{{-- FORM_PERMISSION_UI_V6 --}}
@include('components.form_permission_ui')
