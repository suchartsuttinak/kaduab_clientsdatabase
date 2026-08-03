@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Request;

    $profileData = Auth::user();

    $routeClient = request()->route('client')
        ?? request()->route('client_id')
        ?? request()->route('id')
        ?? null;

    $clientId = null;

    if (isset($client) && is_object($client)) {
        $clientId = $client->id ?? null;
    } elseif (isset($client) && is_numeric($client)) {
        $clientId = $client;
    } elseif (is_object($routeClient)) {
        $clientId = $routeClient->id ?? null;
    } elseif (is_numeric($routeClient)) {
        $clientId = $routeClient;
    }

    $permissionUser = Auth::user();

    /*
    |--------------------------------------------------------------------------
    | ตัวช่วยตรวจสิทธิ์รายฟอร์ม
    |--------------------------------------------------------------------------
    |
    | หากยังไม่ได้เปิดสิทธิ์รายฟอร์ม User::hasFormPermission() จะคืน true
    | เพื่อให้ผู้ใช้เดิมทำงานตามบทบาท/บ้าน/โครงการเดิมโดยไม่กระทบระบบ
    |
    */
    $canForm = static function (string $permissionKey, string $action = 'view') use ($permissionUser): bool {
        if (!$permissionUser) {
            return false;
        }

        // ป้องกันหน้าเสียหายระหว่างติดตั้งไฟล์ Model ไม่ครบ
        if (!method_exists($permissionUser, 'hasFormPermission')) {
            return true;
        }

        return $permissionUser->hasFormPermission($permissionKey, $action);
    };

    $isStatelessClient = isset($client)
        && is_object($client)
        && optional($client->target)->target_name === 'บุคคลไม่มีสถานะทางทะเบียน';

    $isDashboardActive =
        Request::routeIs('admin.index') ||
        Request::routeIs('dashboard') ||
        Request::routeIs('statistics.index');

    /*
    |--------------------------------------------------------------------------
    | รายการเมนู Topbar ตามสิทธิ์รายฟอร์ม
    |--------------------------------------------------------------------------
    */
    $topbarGroups = [
        'history' => [
            'label' => 'ทะเบียนแรกเข้า',
            'icon' => 'fas fa-book',
            'dropdown_id' => 'historyDropdown',
            'active' =>
                Request::routeIs('client.edit') ||
                Request::routeIs('factfinding.*') ||
                Request::routeIs('family.*') ||
                Request::routeIs('visitFamily.*') ||
                Request::routeIs('vitsitFamily.*') ||
                Request::routeIs('member.*') ||
                Request::routeIs('client_files.*') ||
                Request::routeIs('client.report*'),
            'items' => [
                [
                    'permission' => 'registration_client_profile',
                    'action' => 'view',
                    'label' => 'ประวัติผู้รับบริการ',
                    'url' => $clientId ? route('client.edit', $clientId) : null,
                    'active' => Request::routeIs('client.edit'),
                ],
                [
                    'permission' => 'registration_factfinding',
                    'action' => 'view',
                    'label' => 'สอบข้อเท็จจริงเบื้องต้น',
                    'url' => $clientId ? route('factfinding.add', $clientId) : null,
                    'active' => Request::routeIs('factfinding.*'),
                ],
                [
                    'permission' => 'registration_family',
                    'action' => 'view',
                    'label' => 'บันทึกข้อมูลครอบครัว',
                    'url' => $clientId ? route('family.add', $clientId) : null,
                    'active' => Request::routeIs('family.*') || Request::routeIs('estimate.*'),
                ],
                [
                    'permission' => 'registration_family_visit',
                    'action' => 'view',
                    'label' => 'เยี่ยมบ้านครอบครัว',
                    'url' => $clientId ? route('visitFamily.create', $clientId) : null,
                    'active' => Request::routeIs('visitFamily.*') || Request::routeIs('vitsitFamily.*'),
                ],
                [
                    'permission' => 'registration_family_members',
                    'action' => 'view',
                    'label' => 'บันทึกสมาชิกครอบครัว',
                    'url' => $clientId ? route('member.create', $clientId) : null,
                    'active' => Request::routeIs('member.*'),
                ],
                [
                    'permission' => 'registration_client_files',
                    'action' => 'view',
                    'label' => 'จัดเก็บไฟล์เอกสาร',
                    'url' => $clientId ? route('client_files.index', $clientId) : null,
                    'active' => Request::routeIs('client_files.*'),
                ],
                [
                    'permission' => 'registration_client_reports',
                    'action' => 'print',
                    'label' => 'รายงานผู้รับบริการ',
                    'url' => $clientId ? route('client.report', $clientId) : null,
                    'active' => Request::routeIs('client.report*'),
                ],
            ],
        ],

        'education' => [
            'label' => 'การศึกษา',
            'icon' => 'fas fa-graduation-cap',
            'dropdown_id' => 'educationDropdown',
            'active' =>
                Request::routeIs('education_record*') ||
                Request::routeIs('school_followup*') ||
                Request::routeIs('absent.*'),
            'items' => [
                [
                    'permission' => 'education_grade_entry',
                    'action' => 'view',
                    'label' => 'บันทึกผลการเรียน',
                    'url' => $clientId ? route('education_record_add', ['client_id' => $clientId]) : null,
                    'active' => Request::routeIs('education_record_add') || Request::routeIs('education_record.add'),
                ],
                [
                    'permission' => 'education_results',
                    'action' => 'view',
                    'label' => 'แสดงผลการเรียน',
                    'url' => $clientId ? route('education_record_show', $clientId) : null,
                    'active' => Request::routeIs('education_record_show') || Request::routeIs('education_record.report*'),
                ],
                [
                    'permission' => 'education_followup',
                    'action' => 'view',
                    'label' => 'ติดตามสถานศึกษา',
                    'url' => $clientId ? route('school_followup_add', $clientId) : null,
                    'active' => Request::routeIs('school_followup*'),
                ],
                [
                    'permission' => 'education_absence',
                    'action' => 'view',
                    'label' => 'บันทึกการขาดเรียน',
                    'url' => $clientId ? route('absent.add', $clientId) : null,
                    'active' => Request::routeIs('absent.*'),
                ],
            ],
        ],

        'health' => [
            'label' => 'สุขภาพ',
            'icon' => 'fas fa-heartbeat',
            'dropdown_id' => 'healthDropdown',
            'active' =>
                Request::routeIs('accident.*') ||
                Request::routeIs('check_body.*') ||
                Request::routeIs('medical.*') ||
                Request::routeIs('vaccine.*') ||
                Request::routeIs('psychiatric.*') ||
                Request::routeIs('addictive.*') ||
                Request::routeIs('healthc_heckups.*'),
            'items' => [
                [
                    'permission' => 'health_accident',
                    'action' => 'view',
                    'label' => 'บันทึกการบาดเจ็บ',
                    'url' => $clientId ? route('accident.add', $clientId) : null,
                    'active' => Request::routeIs('accident.*'),
                ],
                [
                    'permission' => 'health_body_check',
                    'action' => 'view',
                    'label' => 'บันทึกการตรวจสุขภาพ',
                    'url' => $clientId ? route('check_body.add', $clientId) : null,
                    'active' => Request::routeIs('check_body.*'),
                ],
                [
                    'permission' => 'health_medical',
                    'action' => 'view',
                    'label' => 'บันทึกการรักษาพยาบาล',
                    'url' => $clientId ? route('medical.add', $clientId) : null,
                    'active' => Request::routeIs('medical.*'),
                ],
                [
                    'permission' => 'health_vaccination',
                    'action' => 'view',
                    'label' => 'ประวัติการรับวัคซีน',
                    'url' => $clientId ? route('vaccine.index', $clientId) : null,
                    'active' => Request::routeIs('vaccine.*'),
                ],
                [
                    'permission' => 'health_psychiatric',
                    'action' => 'view',
                    'label' => 'การวินิจฉัยทางจิตเวช',
                    'url' => $clientId ? route('psychiatric.create', $clientId) : null,
                    'active' => Request::routeIs('psychiatric.*'),
                ],
                [
                    'permission' => 'health_addictive',
                    'action' => 'view',
                    'label' => 'การตรวจสารเสพติด',
                    'url' => $clientId ? route('addictive.create', $clientId) : null,
                    'active' => Request::routeIs('addictive.*'),
                ],
                [
                    'permission' => 'health_annual_checkup',
                    'action' => 'view',
                    'label' => 'ตรวจสุขภาพประจำปี',
                    'url' => $clientId ? route('healthc_heckups.index') : null,
                    'active' => Request::routeIs('healthc_heckups.*'),
                ],
            ],
        ],

        'assessment' => [
            'label' => 'แบบคัดกรอง',
            'icon' => 'fas fa-clipboard-check',
            'dropdown_id' => 'assessmentDropdown',
            'active' =>
                Request::routeIs('behavior-screenings.*') ||
                Request::routeIs('snap-iv.*') ||
                Request::routeIs('depression-screenings.*') ||
                Request::routeIs('nutrition_assessments.*'),
            'items' => [
                [
                    'permission' => 'screening_behavior_four_diseases',
                    'action' => 'view',
                    'label' => 'แบบสังเกตพฤติกรรม 4 โรค',
                    'url' => $clientId ? route('behavior-screenings.index', $clientId) : null,
                    'active' => Request::routeIs('behavior-screenings.*'),
                ],
                [
                    'permission' => 'screening_snap_iv',
                    'action' => 'view',
                    'label' => 'แบบประเมิน SNAP-IV',
                    'url' => $clientId ? route('snap-iv.index', $clientId) : null,
                    'active' => Request::routeIs('snap-iv.*'),
                ],
                [
                    'permission' => 'screening_depression',
                    'action' => 'view',
                    'label' => 'แบบคัดกรองภาวะซึมเศร้า',
                    'url' => $clientId ? route('depression-screenings.index', $clientId) : null,
                    'active' => Request::routeIs('depression-screenings.*'),
                ],
                [
                    'permission' => 'screening_nutrition',
                    'action' => 'view',
                    'label' => 'แบบประเมินภาวะโภชนาการ',
                    'url' => $clientId ? route('nutrition_assessments.index', $clientId) : null,
                    'active' => Request::routeIs('nutrition_assessments.*'),
                ],
            ],
        ],

        'social' => [
            'label' => 'สังคมสงเคราะห์',
            'icon' => 'fas fa-users',
            'dropdown_id' => 'socialDropdown',
            'active' =>
                Request::routeIs('observe.*') ||
                Request::routeIs('escape.*') ||
                Request::routeIs('case_outside.*') ||
                Request::routeIs('refers.*') ||
                Request::routeIs('job_agencies.*') ||
                Request::routeIs('help_sessions.*') ||
                Request::routeIs('followup.*') ||
                Request::routeIs('case-activities.*') ||
                Request::routeIs('idstation.*'),
            'items' => [
                [
                    'permission' => 'welfare_stateless_person',
                    'action' => 'view',
                    'label' => 'ช่วยเหลือด้านสถานะบุคคล',
                    'icon' => 'bi bi-person-vcard me-2',
                    'url' => $clientId ? route('idstation.index', $clientId) : null,
                    'active' => Request::routeIs('idstation.*'),
                    'condition' => $isStatelessClient,
                ],
                [
                    'permission' => 'welfare_behavior_problem',
                    'action' => 'view',
                    'label' => 'บันทึกปัญหาพฤติกรรม',
                    'url' => $clientId ? route('observe.create', $clientId) : null,
                    'active' => Request::routeIs('observe.*'),
                ],
                [
                    'permission' => 'welfare_escape',
                    'action' => 'view',
                    'label' => 'การหลบหนีจากที่พักพิง',
                    'url' => $clientId ? route('escape.index', $clientId) : null,
                    'active' => Request::routeIs('escape.*'),
                ],
                [
                    'permission' => 'welfare_outside_followup',
                    'action' => 'view',
                    'label' => 'การติดตามเด็กที่อยู่ภายนอก',
                    'url' => $clientId ? route('case_outside.show', $clientId) : null,
                    'active' => Request::routeIs('case_outside.*'),
                ],
                [
                    'permission' => 'welfare_discharge',
                    'action' => 'view',
                    'label' => 'บันทึกการจำหน่าย',
                    'url' => $clientId ? route('refers.index', $clientId) : null,
                    'active' => Request::routeIs('refers.*'),
                ],
                [
                    'permission' => 'welfare_job_agency',
                    'action' => 'view',
                    'label' => 'การหางานให้ผู้รับบริการ',
                    'url' => $clientId ? route('job_agencies.show', $clientId) : null,
                    'active' => Request::routeIs('job_agencies.*'),
                ],
                [
                    'permission' => 'welfare_help_items',
                    'action' => 'view',
                    'label' => 'ช่วยเหลือสิ่งของ/เครื่องใช้',
                    'url' => $clientId ? route('help_sessions.show', $clientId) : null,
                    'active' => Request::routeIs('help_sessions.*'),
                ],
                [
                    'permission' => 'welfare_followup',
                    'action' => 'view',
                    'label' => 'บันทึกการติดตามผล',
                    'url' => $clientId ? route('followup.index', $clientId) : null,
                    'active' => Request::routeIs('followup.*'),
                ],
                [
                    'permission' => 'welfare_client_activity',
                    'action' => 'view',
                    'label' => 'ความเคลื่อนไหวผู้รับบริการ',
                    'url' => $clientId ? route('case-activities.index', $clientId) : null,
                    'active' => Request::routeIs('case-activities.*'),
                ],
            ],
        ],
    ];

    /* กรองฟอร์มที่ไม่มีสิทธิ์ และซ่อนทั้งหมวดเมื่อไม่มีรายการเหลือ */
    foreach ($topbarGroups as $groupKey => $group) {
        $topbarGroups[$groupKey]['items'] = collect($group['items'])
            ->filter(function (array $item) use ($canForm): bool {
                return !empty($item['url'])
                    && ($item['condition'] ?? true)
                    && $canForm($item['permission'], $item['action'] ?? 'view');
            })
            ->values()
            ->all();
    }
@endphp

<style>
    /* =========================================================
       Scoped style เฉพาะ topbar นี้เท่านั้น
       ========================================================= */
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

                <button type="button"
                    id="clientSidebarToggle"
                    class="button-toggle-menu client-sidebar-toggle topbar-sidebar-toggle border-0 bg-transparent d-none d-xl-inline-flex"
                    aria-label="เปิดหรือปิดเมนูด้านข้าง"
                    aria-controls="clientSidebarPanel"
                    aria-expanded="true">
                    <i data-feather="menu" class="topbar-icon"></i>
                </button>

                <a href="{{ route('dashboard') }}" class="topbar-brand d-none d-md-flex">
                    <span class="topbar-brand-badge">
                        <i class="fas fa-people-group"></i>
                    </span>
                    <span class="topbar-brand-text">หน้าระบบผู้รับบริการ</span>
                </a>
            </div>

            <div class="collapse navbar-collapse topbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav topbar-menu mb-2 mb-xl-0">
                    <li class="nav-item">
                        <a class="nav-link topbar-link {{ $isDashboardActive ? 'active' : '' }}"
                            href="{{ $clientId ? route('admin.index', $clientId) : route('dashboard') }}">
                            <i class="fas fa-home"></i>
                            <span>หน้าหลัก</span>
                        </a>
                    </li>

                    @foreach ($topbarGroups as $group)
                        @if (!empty($group['items']))
                            <li class="nav-item dropdown">
                                <a class="nav-link topbar-link dropdown-toggle {{ $group['active'] ? 'active' : '' }}"
                                    href="#"
                                    id="{{ $group['dropdown_id'] }}"
                                    role="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="{{ $group['icon'] }}"></i>
                                    <span>{{ $group['label'] }}</span>
                                </a>

                                <ul class="dropdown-menu topbar-dropdown"
                                    aria-labelledby="{{ $group['dropdown_id'] }}">
                                    @foreach ($group['items'] as $item)
                                        <li>
                                            <a class="dropdown-item {{ $item['active'] ? 'active' : '' }}"
                                                href="{{ $item['url'] }}">
                                                @if (!empty($item['icon']))
                                                    <i class="{{ $item['icon'] }}"></i>
                                                @endif
                                                {{ $item['label'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @endforeach
                </ul>

                <ul class="navbar-nav ms-xl-auto align-items-xl-center topbar-profile-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link topbar-user dropdown-toggle" href="#" id="profileDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ !empty($profileData?->photo) ? url('upload/user_images/' . $profileData->photo) : url('upload/no_image.jpg') }}"
                                alt="user-image" class="topbar-user-avatar">
                            <span class="topbar-user-meta d-none d-md-flex">
                                <span class="topbar-user-label">ผู้ใช้งาน</span>
                                <span class="topbar-user-name">{{ $profileData?->name ?? 'ผู้ใช้งาน' }}</span>
                            </span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end topbar-dropdown topbar-user-dropdown"
                            aria-labelledby="profileDropdown">
                            <li><h6 class="dropdown-header">บัญชีผู้ใช้งาน</h6></li>
                            <li>
                                <a href="{{ route('admin.profile') }}" class="dropdown-item">
                                    <i class="fas fa-user-circle me-2"></i>ข้อมูลส่วนตัว
                                </a>
                            </li>
                            <li>
                                <a href="auth-lock-screen.html" class="dropdown-item">
                                    <i class="fas fa-lock me-2"></i>ล็อกหน้าจอ
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a href="{{ route('admin.logout') }}" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
