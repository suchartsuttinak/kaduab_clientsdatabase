@php
    $routeClient = request()->route('client')
        ?? request()->route('client_id')
        ?? request()->route('id')
        ?? null;

    $clientId = null;

    if (isset($client) && is_object($client)) {
        $clientId = $client->id;
    } elseif (isset($client) && is_numeric($client)) {
        $clientId = $client;
    } elseif (is_object($routeClient)) {
        $clientId = $routeClient->id ?? null;
    } elseif (is_numeric($routeClient)) {
        $clientId = $routeClient;
    }

    $permissionUser = auth()->user();

    $canForm = static function (string $permissionKey, string $action = 'view') use ($permissionUser): bool {
        return $permissionUser
            ? $permissionUser->hasFormPermission($permissionKey, $action)
            : false;
    };

    $canAnyForm = static function (array $permissionKeys, string $action = 'view') use ($permissionUser): bool {
        return $permissionUser
            ? $permissionUser->hasAnyFormPermission($permissionKeys, $action)
            : false;
    };

    /*
    |--------------------------------------------------------------------------
    | กลุ่มสิทธิ์ตามโครงสร้างหน้ากำหนดสิทธิ์
    |--------------------------------------------------------------------------
    */
    $registrationPermissionKeys = [
        'registration_client_profile',
        'registration_factfinding',
        'registration_family',
        'registration_family_visit',
        'registration_family_members',
        'registration_client_files',
        'registration_client_reports',
    ];

    $educationPermissionKeys = [
        'education_grade_entry',
        'education_results',
        'education_followup',
        'education_absence',
    ];

    $healthPermissionKeys = [
        'health_accident',
        'health_body_check',
        'health_medical',
        'health_vaccination',
        'health_psychiatric',
        'health_addictive',
        'health_annual_checkup',
    ];

    $screeningPermissionKeys = [
        'screening_behavior_four_diseases',
        'screening_snap_iv',
        'screening_depression',
        'screening_nutrition',
    ];

    $socialPermissionKeys = [
        'welfare_behavior_problem',
        'welfare_escape',
        'welfare_outside_followup',
        'welfare_discharge',
        'welfare_job_agency',
        'welfare_help_items',
        'welfare_followup',
        'welfare_client_activity',
        'welfare_stateless_person',
    ];

    $showRegistrationMenu = $canAnyForm($registrationPermissionKeys);
    $showEducationMenu = $canAnyForm($educationPermissionKeys);
    $showHealthMenu = $canAnyForm($healthPermissionKeys);
    $showScreeningMenu = $canAnyForm($screeningPermissionKeys);
    $showSocialMenu = $canAnyForm($socialPermissionKeys);

    $showAnyClientMenu =
        $showRegistrationMenu ||
        $showEducationMenu ||
        $showHealthMenu ||
        $showScreeningMenu ||
        $showSocialMenu;

    /*
    |--------------------------------------------------------------------------
    | เปิดหมวดอัตโนมัติตาม Route ปัจจุบัน
    |--------------------------------------------------------------------------
    */
    $isRegistrationOpen =
        Request::routeIs('client.edit') ||
        Request::routeIs('client.report') ||
        Request::routeIs('factfinding.*') ||
        Request::routeIs('family.*') ||
        Request::routeIs('visitFamily.*') ||
        Request::routeIs('vitsitFamily.*') ||
        Request::routeIs('member.*') ||
        Request::routeIs('estimate.*') ||
        Request::routeIs('client_files.*');

    $isEducationOpen =
        Request::routeIs('education_record*') ||
        Request::routeIs('school_followup*') ||
        Request::routeIs('absent.*');

    $isHealthOpen =
        Request::routeIs('accident.*') ||
        Request::routeIs('check_body.*') ||
        Request::routeIs('medical.*') ||
        Request::routeIs('vaccine.*') ||
        Request::routeIs('psychiatric.*') ||
        Request::routeIs('addictive.*') ||
        Request::routeIs('healthc_heckups.*');

    $isScreeningOpen =
        Request::routeIs('behavior-screenings.*') ||
        Request::routeIs('snap-iv.*') ||
        Request::routeIs('depression-screenings.*') ||
        Request::routeIs('nutrition_assessments.*');

    $isSocialOpen =
        Request::routeIs('observe.*') ||
        Request::routeIs('escape.*') ||
        Request::routeIs('case_outside.*') ||
        Request::routeIs('refers.*') ||
        Request::routeIs('job_agencies.*') ||
        Request::routeIs('help_sessions.*') ||
        Request::routeIs('followup.*') ||
        Request::routeIs('case-activities.*') ||
        Request::routeIs('idstation.*');

    /*
    |--------------------------------------------------------------------------
    | ข้อมูลผู้รับบริการสำหรับการ์ดด้านบน
    |--------------------------------------------------------------------------
    */
    $sidebarClient = null;

    if (isset($client) && is_object($client)) {
        $sidebarClient = $client;
    } elseif (is_object($routeClient)) {
        $sidebarClient = $routeClient;
    }

    $sidebarClientName = trim((string) (
        $sidebarClient->fullname ??
        $sidebarClient->full_name ??
        $sidebarClient->name ??
        'ผู้รับบริการ'
    ));

    if ($sidebarClientName === '') {
        $sidebarClientName = 'ผู้รับบริการ';
    }

    $sidebarClientImage = asset('upload/no_image.jpg');

    if ($sidebarClient && !empty($sidebarClient->image)) {
        $sidebarImageValue = trim((string) $sidebarClient->image);

        if (\Illuminate\Support\Str::startsWith($sidebarImageValue, ['http://', 'https://'])) {
            $sidebarClientImage = $sidebarImageValue;
        } elseif (\Illuminate\Support\Str::startsWith($sidebarImageValue, ['/'])) {
            $sidebarClientImage = url($sidebarImageValue);
        } elseif (\Illuminate\Support\Str::startsWith($sidebarImageValue, ['upload/', 'storage/'])) {
            $sidebarClientImage = asset($sidebarImageValue);
        } else {
            $sidebarClientImage = asset('upload/client_images/' . ltrim($sidebarImageValue, '/'));
        }
    }
@endphp

<style>
    .client-sidebar-panel .app-sidebar-menu {
        top: 0 !important;
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .client-sidebar-panel .app-sidebar-menu > .sidebar-scroll,
    .client-sidebar-panel .app-sidebar-menu .simplebar-wrapper,
    .client-sidebar-panel .app-sidebar-menu .simplebar-mask,
    .client-sidebar-panel .app-sidebar-menu .simplebar-offset,
    .client-sidebar-panel .app-sidebar-menu .simplebar-content-wrapper,
    .client-sidebar-panel .app-sidebar-menu .simplebar-content,
    .client-sidebar-panel #sidebar-menu,
    .client-sidebar-panel #side-menu {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .client-sidebar-panel .sidebar-scroll {
        min-height: 100%;
    }

    .client-sidebar-panel #side-menu {
        padding-top: 6px !important;
    }

    .sidebar-client-card-wrap {
        list-style: none;
        margin: 0 !important;
        padding: 6px 13px 12px;
    }

    #side-menu > li.sidebar-client-card-wrap > a.sidebar-client-card {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 138px;
        margin: 0 !important;
        padding: 14px 12px !important;
        color: #0f172a;
        text-decoration: none;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border: 1px solid rgba(148, 163, 184, .18);
        border-radius: 18px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, .06);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
    }

    #side-menu > li.sidebar-client-card-wrap > a.sidebar-client-card:hover {
        color: #0f172a;
        background: linear-gradient(180deg, #ffffff 0%, #f4f8ff 100%);
        border-color: rgba(59, 130, 246, .22);
        box-shadow: 0 12px 24px rgba(37, 99, 235, .10);
        transform: translateY(-1px);
    }

    .sidebar-client-avatar {
        position: relative;
        z-index: 1;
        flex: 0 0 72px;
        width: 72px;
        height: 72px;
        padding: 3px;
        background: #ffffff;
        border: 1px solid rgba(203, 213, 225, .9);
        border-radius: 50%;
        box-shadow: 0 6px 16px rgba(15, 23, 42, .10);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .sidebar-client-avatar::after {
        content: '';
        position: absolute;
        right: 1px;
        bottom: 2px;
        width: 13px;
        height: 13px;
        background: #22c55e;
        border: 2px solid #ffffff;
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(15, 23, 42, .16);
    }

    .sidebar-client-avatar img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
        background: #f1f5f9;
        border-radius: 50%;
    }

    #side-menu > li.sidebar-client-card-wrap > a.sidebar-client-card:hover .sidebar-client-avatar {
        transform: scale(1.03);
        border-color: rgba(59, 130, 246, .35);
        box-shadow: 0 8px 18px rgba(37, 99, 235, .14);
    }

    .sidebar-client-name {
        display: -webkit-box;
        max-width: 100%;
        overflow: hidden;
        color: #1e293b;
        font-size: 13.25px;
        font-weight: 700;
        line-height: 1.38;
        text-align: center;
        text-overflow: ellipsis;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        word-break: break-word;
    }

    .client-permission-menu > a {
        display: flex;
        align-items: center;
    }

    .client-permission-menu .menu-arrow {
        margin-left: auto;
    }

    .client-permission-empty {
        margin: 12px 13px;
        padding: 14px 12px;
        color: #64748b;
        font-size: 12.5px;
        line-height: 1.55;
        text-align: center;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
    }

    @media (max-width: 575.98px) {
        .sidebar-client-card-wrap {
            padding: 8px 12px 11px;
        }

        #side-menu > li.sidebar-client-card-wrap > a.sidebar-client-card {
            min-height: 128px;
            gap: 8px;
            border-radius: 16px;
            padding: 12px 10px !important;
        }

        .sidebar-client-avatar {
            flex-basis: 66px;
            width: 66px;
            height: 66px;
        }

        .sidebar-client-avatar::after {
            width: 12px;
            height: 12px;
        }
    }
</style>

<div class="app-sidebar-menu">
    <div class="sidebar-scroll h-100" data-simplebar>
        <div id="sidebar-menu">
            <ul id="side-menu" class="metismenu list-unstyled">

                @if ($sidebarClient && $clientId && $showAnyClientMenu)
                    <li class="sidebar-client-card-wrap">
                        <a href="{{ route('admin.index', $clientId) }}"
                           class="sidebar-client-card"
                           title="เปิดข้อมูลผู้รับบริการ : {{ $sidebarClientName }}">
                            <span class="sidebar-client-avatar" aria-hidden="true">
                                <img src="{{ $sidebarClientImage }}"
                                     alt="รูปผู้รับบริการ {{ $sidebarClientName }}"
                                     loading="eager"
                                     onerror="this.onerror=null;this.src='{{ asset('upload/no_image.jpg') }}';">
                            </span>
                            <span class="sidebar-client-name">{{ $sidebarClientName }}</span>
                        </a>
                    </li>
                @endif

                @if ($showAnyClientMenu)
                    <li class="menu-title">ข้อมูลผู้รับบริการ</li>
                @endif

                {{-- 1. ทะเบียนแรกเข้า --}}
                @if ($showRegistrationMenu)
                    <li class="client-permission-menu">
                        <a href="#sidebarRegistration"
                           data-bs-toggle="collapse"
                           aria-expanded="{{ $isRegistrationOpen ? 'true' : 'false' }}"
                           class="{{ $isRegistrationOpen ? 'active' : '' }}">
                            <i data-feather="user-check"></i>
                            <span>ทะเบียนแรกเข้า</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div class="collapse {{ $isRegistrationOpen ? 'show' : '' }}" id="sidebarRegistration">
                            <ul class="nav-second-level">
                                @if ($clientId && $canForm('registration_client_profile'))
                                    <li>
                                        <a href="{{ route('client.edit', $clientId) }}"
                                           class="tp-link {{ Request::routeIs('client.edit') ? 'active' : '' }}">
                                            ประวัติผู้รับบริการ
                                        </a>
                                    </li>
                                @endif

                                @if ($clientId && $canForm('registration_factfinding'))
                                    <li>
                                        <a href="{{ route('factfinding.add', $clientId) }}"
                                           class="tp-link {{ Request::routeIs('factfinding.*') ? 'active' : '' }}">
                                            สอบข้อเท็จจริงเบื้องต้น
                                        </a>
                                    </li>
                                @endif

                                @if ($clientId && $canForm('registration_family'))
                                    <li>
                                        <a href="{{ route('family.add', $clientId) }}"
                                           class="tp-link {{ Request::routeIs('family.*') ? 'active' : '' }}">
                                            บันทึกข้อมูลครอบครัว
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('estimate.show', $clientId) }}"
                                           class="tp-link {{ Request::routeIs('estimate.*') ? 'active' : '' }}">
                                            ประเมินครอบครัว
                                        </a>
                                    </li>
                                @endif

                                @if ($clientId && $canForm('registration_family_visit'))
                                    <li>
                                        <a href="{{ route('visitFamily.create', $clientId) }}"
                                           class="tp-link {{ Request::routeIs('visitFamily.*') || Request::routeIs('vitsitFamily.*') ? 'active' : '' }}">
                                            เยี่ยมครอบครัว
                                        </a>
                                    </li>
                                @endif

                                @if ($clientId && $canForm('registration_family_members'))
                                    <li>
                                        <a href="{{ route('member.create', $clientId) }}"
                                           class="tp-link {{ Request::routeIs('member.*') ? 'active' : '' }}">
                                            บันทึกสมาชิกครอบครัว
                                        </a>
                                    </li>
                                @endif

                                @if ($clientId && $canForm('registration_client_files'))
                                    <li>
                                        <a href="{{ route('client_files.index', $clientId) }}"
                                           class="tp-link {{ Request::routeIs('client_files.*') ? 'active' : '' }}">
                                            จัดเก็บไฟล์เอกสาร
                                        </a>
                                    </li>
                                @endif

                                @if ($clientId && $canForm('registration_client_reports', 'print'))
                                    <li>
                                        <a href="{{ route('client.report', $clientId) }}"
                                           class="tp-link {{ Request::routeIs('client.report') ? 'active' : '' }}">
                                            รายงานผู้รับบริการ
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                {{-- 2. การศึกษา --}}
                @if ($showEducationMenu)
                    <li class="client-permission-menu">
                        <a href="#sidebarEducation"
                           data-bs-toggle="collapse"
                           aria-expanded="{{ $isEducationOpen ? 'true' : 'false' }}"
                           class="{{ $isEducationOpen ? 'active' : '' }}">
                            <i class="fas fa-graduation-cap sidebar-fa-icon"></i>
                            <span>การศึกษา</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div class="collapse {{ $isEducationOpen ? 'show' : '' }}" id="sidebarEducation">
                            <ul class="nav-second-level">
                                @if ($clientId && $canForm('education_grade_entry'))
                                    <li><a href="{{ route('education_record_add', ['client_id' => $clientId]) }}" class="tp-link {{ Request::routeIs('education_record_add') ? 'active' : '' }}">บันทึกผลการเรียน</a></li>
                                @endif
                                @if ($clientId && $canForm('education_results'))
                                    <li><a href="{{ route('education_record_show', $clientId) }}" class="tp-link {{ Request::routeIs('education_record_show') ? 'active' : '' }}">แสดงผลการเรียน</a></li>
                                @endif
                                @if ($clientId && $canForm('education_followup'))
                                    <li><a href="{{ route('school_followup_add', $clientId) }}" class="tp-link {{ Request::routeIs('school_followup*') ? 'active' : '' }}">ติดตามการศึกษา</a></li>
                                @endif
                                @if ($clientId && $canForm('education_absence'))
                                    <li><a href="{{ route('absent.add', $clientId) }}" class="tp-link {{ Request::routeIs('absent.*') ? 'active' : '' }}">บันทึกการขาดเรียน</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                {{-- 3. สุขภาพ --}}
                @if ($showHealthMenu)
                    <li class="client-permission-menu">
                        <a href="#sidebarHealth"
                           data-bs-toggle="collapse"
                           aria-expanded="{{ $isHealthOpen ? 'true' : 'false' }}"
                           class="{{ $isHealthOpen ? 'active' : '' }}">
                            <i class="fas fa-heartbeat sidebar-fa-icon"></i>
                            <span>สุขภาพ</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div class="collapse {{ $isHealthOpen ? 'show' : '' }}" id="sidebarHealth">
                            <ul class="nav-second-level">
                                @if ($clientId && $canForm('health_accident'))
                                    <li><a href="{{ route('accident.add', $clientId) }}" class="tp-link {{ Request::routeIs('accident.*') ? 'active' : '' }}">บันทึกการบาดเจ็บ</a></li>
                                @endif
                                @if ($clientId && $canForm('health_body_check'))
                                    <li><a href="{{ route('check_body.add', $clientId) }}" class="tp-link {{ Request::routeIs('check_body.*') ? 'active' : '' }}">บันทึกการตรวจร่างกาย</a></li>
                                @endif
                                @if ($clientId && $canForm('health_medical'))
                                    <li><a href="{{ route('medical.add', $clientId) }}" class="tp-link {{ Request::routeIs('medical.*') ? 'active' : '' }}">บันทึกการรักษาพยาบาล</a></li>
                                @endif
                                @if ($clientId && $canForm('health_vaccination'))
                                    <li><a href="{{ route('vaccine.index', $clientId) }}" class="tp-link {{ Request::routeIs('vaccine.*') ? 'active' : '' }}">ประวัติการรับวัคซีน</a></li>
                                @endif
                                @if ($clientId && $canForm('health_psychiatric'))
                                    <li><a href="{{ route('psychiatric.create', $clientId) }}" class="tp-link {{ Request::routeIs('psychiatric.*') ? 'active' : '' }}">การวินิจฉัยทางจิตเวช</a></li>
                                @endif
                                @if ($clientId && $canForm('health_addictive'))
                                    <li><a href="{{ route('addictive.create', $clientId) }}" class="tp-link {{ Request::routeIs('addictive.*') ? 'active' : '' }}">การตรวจสารเสพติด</a></li>
                                @endif
                                @if ($canForm('health_annual_checkup'))
                                    <li><a href="{{ route('healthc_heckups.index') }}" class="tp-link {{ Request::routeIs('healthc_heckups.*') ? 'active' : '' }}">ตรวจสุขภาพประจำปี</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                {{-- 4. แบบคัดกรอง --}}
                @if ($showScreeningMenu)
                    <li class="client-permission-menu">
                        <a href="#sidebarScreening"
                           data-bs-toggle="collapse"
                           aria-expanded="{{ $isScreeningOpen ? 'true' : 'false' }}"
                           class="{{ $isScreeningOpen ? 'active' : '' }}">
                            <i class="bi bi-clipboard2-pulse sidebar-fa-icon"></i>
                            <span>แบบคัดกรอง</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div class="collapse {{ $isScreeningOpen ? 'show' : '' }}" id="sidebarScreening">
                            <ul class="nav-second-level">
                                @if ($clientId && $canForm('screening_behavior_four_diseases'))
                                    <li><a href="{{ route('behavior-screenings.index', $clientId) }}" class="tp-link {{ Request::routeIs('behavior-screenings.*') ? 'active' : '' }}">แบบสังเกตพฤติกรรม 4 โรค</a></li>
                                @endif
                                @if ($clientId && $canForm('screening_snap_iv'))
                                    <li><a href="{{ route('snap-iv.index', $clientId) }}" class="tp-link {{ Request::routeIs('snap-iv.*') ? 'active' : '' }}">แบบประเมิน SNAP-IV</a></li>
                                @endif
                                @if ($clientId && $canForm('screening_depression'))
                                    <li><a href="{{ route('depression-screenings.index', $clientId) }}" class="tp-link {{ Request::routeIs('depression-screenings.*') ? 'active' : '' }}">แบบคัดกรองภาวะซึมเศร้า</a></li>
                                @endif
                                @if ($clientId && $canForm('screening_nutrition'))
                                    <li><a href="{{ route('nutrition_assessments.index', $clientId) }}" class="tp-link {{ Request::routeIs('nutrition_assessments.*') ? 'active' : '' }}">แบบประเมินภาวะโภชนาการ</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                {{-- 5. สังคมสงเคราะห์ --}}
                @if ($showSocialMenu)
                    <li class="client-permission-menu">
                        <a href="#sidebarSocial"
                           data-bs-toggle="collapse"
                           aria-expanded="{{ $isSocialOpen ? 'true' : 'false' }}"
                           class="{{ $isSocialOpen ? 'active' : '' }}">
                            <i class="fas fa-users sidebar-fa-icon"></i>
                            <span>สังคมสงเคราะห์</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div class="collapse {{ $isSocialOpen ? 'show' : '' }}" id="sidebarSocial">
                            <ul class="nav-second-level">
                                @if ($clientId && $canForm('welfare_behavior_problem'))
                                    <li><a href="{{ route('observe.create', $clientId) }}" class="tp-link {{ Request::routeIs('observe.*') ? 'active' : '' }}">บันทึกปัญหาพฤติกรรม</a></li>
                                @endif
                                @if ($clientId && $canForm('welfare_escape'))
                                    <li><a href="{{ route('escape.index', $clientId) }}" class="tp-link {{ Request::routeIs('escape.*') ? 'active' : '' }}">การหลบหนีจากที่พักพิง</a></li>
                                @endif
                                @if ($clientId && $canForm('welfare_outside_followup'))
                                    <li><a href="{{ route('case_outside.show', $clientId) }}" class="tp-link {{ Request::routeIs('case_outside.*') ? 'active' : '' }}">การติดตามเด็กที่อยู่ภายนอก</a></li>
                                @endif
                                @if ($clientId && $canForm('welfare_discharge'))
                                    <li><a href="{{ route('refers.index', $clientId) }}" class="tp-link {{ Request::routeIs('refers.*') ? 'active' : '' }}">บันทึกการจำหน่าย</a></li>
                                @endif
                                @if ($clientId && $canForm('welfare_job_agency'))
                                    <li><a href="{{ route('job_agencies.show', $clientId) }}" class="tp-link {{ Request::routeIs('job_agencies.*') ? 'active' : '' }}">การหางานให้ผู้รับบริการ</a></li>
                                @endif
                                @if ($clientId && $canForm('welfare_help_items'))
                                    <li><a href="{{ route('help_sessions.show', $clientId) }}" class="tp-link {{ Request::routeIs('help_sessions.*') ? 'active' : '' }}">ช่วยเหลือสิ่งของเครื่องใช้</a></li>
                                @endif
                                @if ($clientId && $canForm('welfare_followup'))
                                    <li><a href="{{ route('followup.index', $clientId) }}" class="tp-link {{ Request::routeIs('followup.*') ? 'active' : '' }}">บันทึกการติดตาม</a></li>
                                @endif
                                @if ($clientId && $canForm('welfare_client_activity'))
                                    <li><a href="{{ route('case-activities.index', $clientId) }}" class="tp-link {{ Request::routeIs('case-activities.*') ? 'active' : '' }}">ความเคลื่อนไหวผู้รับบริการ</a></li>
                                @endif
                                @if (
                                    $clientId &&
                                    $canForm('welfare_stateless_person') &&
                                    $sidebarClient &&
                                    optional($sidebarClient->target)->target_name === 'บุคคลไม่มีสถานะทางทะเบียน'
                                )
                                    <li><a href="{{ route('idstation.index', $clientId) }}" class="tp-link {{ Request::routeIs('idstation.*') ? 'active' : '' }}">บุคคลไร้สัญชาติ</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if (!$showAnyClientMenu && $permissionUser && $permissionUser->form_permissions_enabled)
                    <li class="client-permission-empty">
                        บัญชีนี้ยังไม่ได้รับสิทธิ์ใช้งานฟอร์มผู้รับบริการ
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
