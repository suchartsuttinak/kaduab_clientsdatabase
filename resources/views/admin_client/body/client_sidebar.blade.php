@php
    use Illuminate\Support\Facades\Request;
    use Illuminate\Support\Str;
    use App\Support\FormPermissionMenu;

    $permissionMenu = FormPermissionMenu::forUser(auth()->user());
    $formAccess = $permissionMenu['forms'];
    $groupAccess = $permissionMenu['groups'];
    $canForm = static fn (string $key): bool => (bool) ($formAccess[$key] ?? false);

    $routeClient = request()->route('client') ?? request()->route('client_id') ?? null;
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

    $sidebarClient = isset($client) && is_object($client)
        ? $client
        : (is_object($routeClient) ? $routeClient : null);

    /*
     * แสดงชื่อพร้อมคำนำหน้าตามค่าที่ Model เตรียมไว้
     * หาก fullname/full_name ไม่มีค่า จะประกอบจากความสัมพันธ์ title และชื่อ-สกุล
     */
    $sidebarTitleName = trim((string) (
        data_get($sidebarClient, 'title.title_name') ??
        data_get($sidebarClient, 'title.name') ??
        data_get($sidebarClient, 'title.title') ??
        data_get($sidebarClient, 'prefix.title_name') ??
        data_get($sidebarClient, 'prefix.name') ??
        ''
    ));

    $sidebarFirstName = trim((string) ($sidebarClient->first_name ?? ''));
    $sidebarLastName = trim((string) ($sidebarClient->last_name ?? ''));
    $sidebarNameFromColumns = trim($sidebarFirstName . ' ' . $sidebarLastName);

    $sidebarClientName = trim((string) (
        $sidebarClient->fullname ??
        $sidebarClient->full_name ??
        $sidebarClient->name ??
        ''
    ));

    /*
     * ให้ fullname/full_name จาก Model มีลำดับสูงสุด เพื่อรักษากฎคำนำหน้าแบบไดนามิก
     * เช่น อายุครบ 15 ปีแล้วเปลี่ยนจาก เด็กชาย/เด็กหญิง เป็น นาย/นางสาว
     */
    if ($sidebarClientName === '') {
        $sidebarClientName = trim($sidebarTitleName . ' ' . $sidebarNameFromColumns);
    }

    if ($sidebarClientName === '') {
        $sidebarClientName = 'ผู้รับบริการ';
    }

    /* ดึงเลขทะเบียนจาก clients.register_number โดยตรง */
    $sidebarClientRegisterNumber = trim((string) ($sidebarClient->register_number ?? ''));
    if ($sidebarClientRegisterNumber === '') {
        $sidebarClientRegisterNumber = '-';
    }

    $sidebarClientImage = asset('upload/no_image.jpg');

    if ($sidebarClient && !empty($sidebarClient->image)) {
        $value = trim((string) $sidebarClient->image);

        if (Str::startsWith($value, ['http://', 'https://'])) {
            $sidebarClientImage = $value;
        } elseif ($clientId) {
            $sidebarClientImage = route('client.image', $clientId);
        }
    }

    $isRegistrationOpen =
        Request::routeIs('client.edit') || Request::routeIs('client.report*') ||
        Request::routeIs('factfinding.*') || Request::routeIs('family.*') ||
        Request::routeIs('estimate.*') || Request::routeIs('visitFamily.*') ||
        Request::routeIs('vitsitFamily.*') || Request::routeIs('member.*') ||
        Request::routeIs('client_files.*');

    $isEducationOpen = Request::routeIs('education_record*')
        || Request::routeIs('school_followup*')
        || Request::routeIs('absent.*');

    $isHealthOpen = Request::routeIs('accident.*')
        || Request::routeIs('check_body.*')
        || Request::routeIs('medical.*')
        || Request::routeIs('vaccine.*')
        || Request::routeIs('psychiatric.*')
        || Request::routeIs('addictive.*')
        || Request::routeIs('healthc_heckups.*');

    $isAssessmentOpen = Request::routeIs('behavior-screenings.*')
        || Request::routeIs('snap-iv.*')
        || Request::routeIs('depression-screenings.*')
        || Request::routeIs('nutrition_assessments.*');

    $isSocialOpen = Request::routeIs('observe.*')
        || Request::routeIs('escape.*')
        || Request::routeIs('case_outside.*')
        || Request::routeIs('refers.*')
        || Request::routeIs('job_agencies.*')
        || Request::routeIs('help_sessions.*')
        || Request::routeIs('followup.*')
        || Request::routeIs('case-activities.*')
        || Request::routeIs('idstation.*')
        || Request::routeIs('counseling.*');

    $isStatelessClient = $sidebarClient
        && optional($sidebarClient->target)->target_name === 'บุคคลไม่มีสถานะทางทะเบียน';
@endphp

{{-- FORM_PERMISSION_MENU_V6_NO_COUNT: CLIENT_SIDEBAR --}}
<style>
    .client-sidebar-panel .app-sidebar-menu,
    .client-sidebar-panel .app-sidebar-menu > .sidebar-scroll,
    .client-sidebar-panel .app-sidebar-menu .simplebar-content,
    .client-sidebar-panel #sidebar-menu {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .client-sidebar-panel #side-menu {
        padding-top: 0 !important;
    }

    /* =========================================================
       CLIENT PROFILE — พื้นหลังเดียวกับ Sidebar ไม่มีกรอบการ์ด
       ========================================================= */
    .sidebar-client-card-wrap {
        position: relative;
        list-style: none;
        margin: 0 !important;
        padding: 12px 18px 14px;
    }

    #side-menu > li.sidebar-client-card-wrap > a.sidebar-client-card {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        width: 100%;
        min-height: 142px;
        margin: 0;
        padding: 4px 10px 0 !important;
        gap: 0;
        color: #1f3556;
        text-decoration: none !important;
        background: transparent !important;
        border: 0 !important;
        border-radius: 16px;
        box-shadow: none !important;
        outline: 0;
        transition: background-color .18s ease, transform .18s ease;
    }

    #side-menu > li.sidebar-client-card-wrap > a.sidebar-client-card:hover,
    #side-menu > li.sidebar-client-card-wrap > a.sidebar-client-card:focus-visible {
        color: #1f3556;
        background: rgba(59, 130, 246, .035) !important;
        box-shadow: none !important;
    }

    #side-menu > li.sidebar-client-card-wrap > a.sidebar-client-card:hover {
        transform: translateY(-1px);
    }

    #side-menu > li.sidebar-client-card-wrap > a.sidebar-client-card:focus-visible {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .12) !important;
    }

    .sidebar-client-icon {
        position: absolute;
        top: 0;
        right: 2px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 29px;
        height: 29px;
        color: #4f7df3;
        font-size: 14px;
        line-height: 1;
        background: rgba(79, 125, 243, .09);
        border: 0;
        border-radius: 50%;
        pointer-events: none;
    }

    .sidebar-client-avatar {
        position: relative;
        display: block;
        flex: 0 0 auto;
        width: 74px;
        height: 74px;
        margin-top: 1px;
        padding: 3px;
        background: #ffffff;
        border: 1px solid rgba(191, 208, 229, .95);
        border-radius: 50%;
        box-shadow: 0 5px 13px rgba(15, 23, 42, .10);
    }

    .sidebar-client-avatar::after {
        content: '';
        position: absolute;
        right: 0;
        bottom: 3px;
        width: 12px;
        height: 12px;
        background: #22c55e;
        border: 2px solid #ffffff;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .16);
    }

    .sidebar-client-avatar img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
        border-radius: 50%;
    }

    .sidebar-client-name {
        display: block;
        width: 100%;
        max-width: 175px;
        margin-top: 5px;
        overflow: hidden;
        color: #253a5a;
        font-size: 13.4px;
        font-weight: 700;
        line-height: 1.35;
        text-align: center;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sidebar-client-code {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        max-width: 180px;
        min-height: 23px;
        margin-top: 7px;
        padding: 3px 9px;
        gap: 5px;
        overflow: hidden;
        color: #49617f;
        font-size: 10.5px;
        font-weight: 500;
        line-height: 1.2;
        white-space: nowrap;
        background: rgba(255, 255, 255, .64);
        border: 1px solid rgba(175, 194, 217, .46);
        border-radius: 999px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, .025);
    }

    .sidebar-client-code i {
        flex: 0 0 auto;
        color: #3c5c82;
        font-size: 11px;
        line-height: 1;
    }

    .sidebar-client-code span {
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ให้หัวข้อเมนูแรกอยู่ใกล้ส่วนข้อมูลผู้รับบริการตามตัวอย่าง */
    #side-menu > li.sidebar-client-card-wrap + li.menu-title {
        margin-top: 0 !important;
    }

    @media (max-width: 1199.98px) {
        .sidebar-client-card-wrap {
            padding-right: 15px;
            padding-left: 15px;
        }
    }

    @media (max-width: 991.98px) {
        .sidebar-client-card-wrap {
            padding-top: 10px;
            padding-bottom: 12px;
        }

        #side-menu > li.sidebar-client-card-wrap > a.sidebar-client-card {
            min-height: 138px;
        }

        .sidebar-client-avatar {
            width: 70px;
            height: 70px;
        }

        .sidebar-client-name {
            font-size: 13px;
        }
    }
</style>

<div class="app-sidebar-menu">
    <div class="sidebar-scroll h-100" data-simplebar>
        <div id="sidebar-menu">
            <ul id="side-menu" class="metismenu list-unstyled">
                @if ($sidebarClient && $clientId)
                    <li class="sidebar-client-card-wrap">
                        <a href="{{ route('admin.index', $clientId) }}"
                           class="sidebar-client-card"
                           title="เปิดข้อมูลผู้รับบริการ : {{ $sidebarClientName }}"
                           aria-label="เปิดข้อมูลผู้รับบริการ {{ $sidebarClientName }} เลขทะเบียน {{ $sidebarClientRegisterNumber }}">

                            <span class="sidebar-client-icon" aria-hidden="true">
                                <i class="bi bi-person"></i>
                            </span>

                            <span class="sidebar-client-avatar" aria-hidden="true">
                                <img src="{{ $sidebarClientImage }}"
                                     alt="รูปผู้รับบริการ {{ $sidebarClientName }}"
                                     onerror="this.onerror=null;this.src='{{ asset('upload/no_image.jpg') }}';">
                            </span>

                            <span class="sidebar-client-name">{{ $sidebarClientName }}</span>

                            <span class="sidebar-client-code" aria-label="เลขทะเบียน {{ $sidebarClientRegisterNumber }}">
                                <i class="bi bi-person-vcard" aria-hidden="true"></i>
                                <span>เลขทะเบียน: {{ $sidebarClientRegisterNumber }}</span>
                            </span>
                        </a>
                    </li>
                @endif

                @if ($groupAccess['registration'] ?? false)
                    <li class="menu-title">ทะเบียนแรกเข้า</li>
                    <li>
                        <a href="#sidebarRegistration"
                           data-bs-toggle="collapse"
                           aria-expanded="{{ $isRegistrationOpen ? 'true' : 'false' }}"
                           class="{{ $isRegistrationOpen ? 'active' : '' }}">
                            <i data-feather="home"></i>
                            <span>ทะเบียนแรกเข้า</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div class="collapse {{ $isRegistrationOpen ? 'show' : '' }}" id="sidebarRegistration">
                            <ul class="nav-second-level">
                                @if ($clientId && $canForm('registration_client_profile'))
                                    <li><a href="{{ route('client.edit', $clientId) }}" class="tp-link {{ Request::routeIs('client.edit') ? 'active' : '' }}">ประวัติผู้รับบริการ</a></li>
                                @endif

                                @if ($clientId && $canForm('registration_factfinding'))
                                    <li><a href="{{ route('factfinding.add', $clientId) }}" class="tp-link {{ Request::routeIs('factfinding.*') ? 'active' : '' }}">สอบข้อเท็จจริงเบื้องต้น</a></li>
                                @endif

                                @if ($clientId && $canForm('registration_family'))
                                    {{-- FAMILY_ASSESSMENT_PERMISSION_V2 --}}
                                    <li><a href="{{ route('family.add', $clientId) }}" class="tp-link {{ Request::routeIs('family.*') ? 'active' : '' }}">บันทึกข้อมูลครอบครัว</a></li>
                                @endif

                                @if ($clientId && $canForm('registration_family_assessment'))
                                    <li><a href="{{ route('estimate.show', $clientId) }}" class="tp-link {{ Request::routeIs('estimate.*') ? 'active' : '' }}">ประเมินครอบครัว</a></li>
                                @endif

                                @if ($clientId && $canForm('registration_family_visit'))
                                    <li><a href="{{ route('visitFamily.create', $clientId) }}" class="tp-link {{ Request::routeIs('visitFamily.*') || Request::routeIs('vitsitFamily.*') ? 'active' : '' }}">เยี่ยมครอบครัว</a></li>
                                @endif

                                @if ($clientId && $canForm('registration_family_members'))
                                    <li><a href="{{ route('member.create', $clientId) }}" class="tp-link {{ Request::routeIs('member.*') ? 'active' : '' }}">บันทึกสมาชิกครอบครัว</a></li>
                                @endif

                                @if ($clientId && $canForm('registration_client_files'))
                                    <li><a href="{{ route('client_files.index', $clientId) }}" class="tp-link {{ Request::routeIs('client_files.*') ? 'active' : '' }}">จัดเก็บไฟล์เอกสาร</a></li>
                                @endif

                                @if ($clientId && $canForm('registration_client_reports'))
                                    <li><a href="{{ route('client.report', $clientId) }}" class="tp-link {{ Request::routeIs('client.report*') ? 'active' : '' }}">รายงานผู้รับบริการ</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if ($groupAccess['education'] ?? false)
                    <li class="menu-title mt-2">การศึกษา</li>
                    <li>
                        <a href="#sidebarEducation"
                           data-bs-toggle="collapse"
                           aria-expanded="{{ $isEducationOpen ? 'true' : 'false' }}"
                           class="{{ $isEducationOpen ? 'active' : '' }}">
                            <i class="fas fa-graduation-cap sidebar-fa-icon"></i>
                            <span>ข้อมูลการศึกษา</span>
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

                @if ($groupAccess['health'] ?? false)
                    <li class="menu-title mt-2">สุขภาพ</li>
                    <li>
                        <a href="#sidebarHealth"
                           data-bs-toggle="collapse"
                           aria-expanded="{{ $isHealthOpen ? 'true' : 'false' }}"
                           class="{{ $isHealthOpen ? 'active' : '' }}">
                            <i class="fas fa-heartbeat sidebar-fa-icon"></i>
                            <span>ข้อมูลสุขภาพ</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div class="collapse {{ $isHealthOpen ? 'show' : '' }}" id="sidebarHealth">
                            <ul class="nav-second-level">
                                @if ($clientId && $canForm('health_accident'))
                                    <li><a href="{{ route('accident.add', $clientId) }}" class="tp-link {{ Request::routeIs('accident.*') ? 'active' : '' }}">บันทึกการบาดเจ็บ</a></li>
                                @endif

                                @if ($clientId && $canForm('health_body_check'))
                                    <li><a href="{{ route('check_body.add', $clientId) }}" class="tp-link {{ Request::routeIs('check_body.*') ? 'active' : '' }}">ตรวจสุขภาพเบื้องต้น</a></li>
                                @endif

                                @if ($clientId && $canForm('health_medical'))
                                    <li><a href="{{ route('medical.add', $clientId) }}" class="tp-link {{ Request::routeIs('medical.*') ? 'active' : '' }}">การรักษาพยาบาล</a></li>
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

                @if ($groupAccess['screening'] ?? false)
                    <li class="menu-title mt-2">แบบประเมินและคัดกรอง</li>
                    <li>
                        <a href="#sidebarAssessment"
                           data-bs-toggle="collapse"
                           aria-expanded="{{ $isAssessmentOpen ? 'true' : 'false' }}"
                           class="{{ $isAssessmentOpen ? 'active' : '' }}">
                            <i class="bi bi-clipboard2-pulse sidebar-fa-icon"></i>
                            <span>แบบคัดกรอง</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div class="collapse {{ $isAssessmentOpen ? 'show' : '' }}" id="sidebarAssessment">
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

                @if ($groupAccess['social_welfare'] ?? false)
                    <li class="menu-title mt-2">สังคมสงเคราะห์</li>
                    <li>
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
                                @if ($clientId && $isStatelessClient && $canForm('welfare_stateless_person'))
                                    <li><a href="{{ route('idstation.index', $clientId) }}" class="tp-link {{ Request::routeIs('idstation.*') ? 'active' : '' }}">ช่วยเหลือด้านสถานะบุคคล</a></li>
                                @endif

                                @if ($clientId && $canForm('welfare_counseling'))
                                    <li>
                                        <a href="{{ route('counseling.index', $clientId) }}"
                                           class="tp-link {{ Request::routeIs('counseling.*') ? 'active' : '' }}">
                                            การให้คำปรึกษา
                                        </a>
                                    </li>
                                @endif


                                @if ($clientId && $canForm('welfare_behavior_problem'))
                                    <li><a href="{{ route('observe.create', $clientId) }}" class="tp-link {{ Request::routeIs('observe.*') ? 'active' : '' }}">บันทึกปัญหาพฤติกรรม</a></li>
                                @endif

                                @if ($clientId && $canForm('welfare_escape'))
                                    <li><a href="{{ route('escape.index', $clientId) }}" class="tp-link {{ Request::routeIs('escape.*') ? 'active' : '' }}">การหลบหนีจากที่พักพิง</a></li>
                                @endif

                                @if ($clientId && $canForm('welfare_outside_followup'))
                                    <li><a href="{{ route('case_outside.show', $clientId) }}" class="tp-link {{ Request::routeIs('case_outside.*') ? 'active' : '' }}">ติดตามเด็กที่อยู่ภายนอก</a></li>
                                @endif

                                @if ($clientId && $canForm('welfare_discharge'))
                                    <li><a href="{{ route('refers.index', $clientId) }}" class="tp-link {{ Request::routeIs('refers.*') ? 'active' : '' }}">บันทึกการจำหน่าย</a></li>
                                @endif

                                @if ($clientId && $canForm('welfare_job_agency'))
                                    <li><a href="{{ route('job_agencies.show', $clientId) }}" class="tp-link {{ Request::routeIs('job_agencies.*') ? 'active' : '' }}">การหางานให้ผู้รับบริการ</a></li>
                                @endif

                                @if ($clientId && $canForm('welfare_help_items'))
                                    <li><a href="{{ route('help_sessions.show', $clientId) }}" class="tp-link {{ Request::routeIs('help_sessions.*') ? 'active' : '' }}">ช่วยเหลือสิ่งของ/เครื่องใช้</a></li>
                                @endif

                                @if ($clientId && $canForm('welfare_followup'))
                                    <li><a href="{{ route('followup.index', $clientId) }}" class="tp-link {{ Request::routeIs('followup.*') ? 'active' : '' }}">บันทึกการติดตาม</a></li>
                                @endif

                                @if ($clientId && $canForm('welfare_client_activity'))
                                    <li><a href="{{ route('case-activities.index', $clientId) }}" class="tp-link {{ Request::routeIs('case-activities.*') ? 'active' : '' }}">ความเคลื่อนไหวผู้รับบริการ</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>