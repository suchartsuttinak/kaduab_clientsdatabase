@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Request;

    $id = Auth::id();
    $profileData = App\Models\User::find($id);

    // กัน error กรณีบางหน้าไม่มี $client
    $clientId =
        $client->id ??
        (request()->route('client') ?? (request()->route('client_id') ?? (request()->route('id') ?? null)));

    if (is_object($clientId)) {
        $clientId = $clientId->id ?? null;
    }

    // หน้าหลัก
    $isDashboardActive =
        Request::routeIs('admin.index') || Request::routeIs('dashboard') || Request::routeIs('statistics.index');

    // ทะเบียนแรกเข้า
    $isHistoryActive =
        Request::routeIs('client.edit') ||
        Request::routeIs('factfinding.add') ||
        Request::routeIs('family.add') ||
        Request::routeIs('visitFamily.create') ||
        Request::routeIs('member.create') ||
        Request::routeIs('client_files.index') ||
        Request::routeIs('client.report') ||
        Request::routeIs('client.report.*');

    // การศึกษา
    $isEducationActive =
        Request::routeIs('education_record.add') ||
        Request::routeIs('education_record.store') ||
        Request::routeIs('education_record.edit') ||
        Request::routeIs('education_record.report') ||
        Request::routeIs('education_record.report_by_id') ||
        Request::routeIs('education_record_add') ||
        Request::routeIs('education_record_store') ||
        Request::routeIs('education_record_show') ||
        Request::routeIs('education_record_edit') ||
        Request::routeIs('education_record_update') ||
        Request::routeIs('education_record_delete') ||
        Request::routeIs('education_record_report') ||
        Request::routeIs('education_record_report_by_id') ||
        Request::routeIs('school_followup_add') ||
        Request::routeIs('school_followup_*') ||
        Request::routeIs('absent.add') ||
        Request::routeIs('absent.*');

    // สุขภาพ
    $isHealthActive =
        Request::routeIs('accident.add') ||
        Request::routeIs('accident.*') ||
        Request::routeIs('check_body.add') ||
        Request::routeIs('check_body.*') ||
        Request::routeIs('medical.add') ||
        Request::routeIs('medical.*') ||
        Request::routeIs('vaccine.index') ||
        Request::routeIs('vaccine.*') ||
        Request::routeIs('psychiatric.create') ||
        Request::routeIs('psychiatric.*') ||
        Request::routeIs('addictive.create') ||
        Request::routeIs('addictive.*') ||
        Request::routeIs('healthc_heckups.index') ||
        Request::routeIs('healthc_heckups.*');

    // แบบประเมิน / คัดกรอง
   $isAssessmentActive =
    Request::routeIs('behavior-screenings.*') ||
    Request::routeIs('snap-iv.*') ||
    Request::routeIs('depression-screenings.*') ||
    Request::routeIs('nutrition_assessments.*');

    // สังคมสงเคราะห์ / พฤติกรรม / ความเคลื่อนไหว
    $isSocialActive =
        Request::routeIs('observe.create') ||
        Request::routeIs('observe.*') ||
        Request::routeIs('escape.index') ||
        Request::routeIs('escape.*') ||
        Request::routeIs('case_outside.show') ||
        Request::routeIs('case_outside.*') ||
        Request::routeIs('refers.index') ||
        Request::routeIs('refers.*') ||
        Request::routeIs('job_agencies.show') ||
        Request::routeIs('job_agencies.*') ||
        Request::routeIs('help_sessions.show') ||
        Request::routeIs('help_sessions.*') ||
        Request::routeIs('followup.index') ||
        Request::routeIs('followup.*') ||
        Request::routeIs('case-activities.index') ||
        Request::routeIs('case-activities.*') ||
        Request::routeIs('idstation.*');
@endphp

<style>
    /* =========================================================
       Scoped style เฉพาะ topbar นี้เท่านั้น
       ปรับเฉพาะข้อความเมนูหลักให้เหมือน "ระบบข้อมูลผู้รับบริการ"
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

    /* คงรูปแบบเดิมตอน active / hover โดยไม่ไปยุ่งส่วนอื่น */
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

    /* จัดไอคอนกับข้อความให้บาลานซ์ขึ้น แต่ไม่กระทบเมนูย่อย */
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

            <!-- Left -->
            <div class="d-flex align-items-center topbar-left-group">
                <button class="navbar-toggler topbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
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

            <!-- Main menu -->
            <div class="collapse navbar-collapse topbar-collapse" id="navbarSupportedContent">

                <ul class="navbar-nav topbar-menu mb-2 mb-xl-0">
                    <li class="nav-item">
                        <a class="nav-link topbar-link {{ $isDashboardActive ? 'active' : '' }}"
                            href="{{ $clientId ? route('admin.index', $clientId) : route('dashboard') }}">
                                <i class="fas fa-home"></i>
                                <span>หน้าหลัก</span>
                            </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link topbar-link dropdown-toggle {{ $isHistoryActive ? 'active' : '' }}"
                            href="#" id="historyDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-book"></i>
                            <span>ทะเบียนแรกเข้า</span>
                        </a>
                        <ul class="dropdown-menu topbar-dropdown" aria-labelledby="historyDropdown">
                            @if ($clientId)
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('client.edit') ? 'active' : '' }}"
                                        href="{{ route('client.edit', $clientId) }}">
                                        ประวัติผู้รับบริการ
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('factfinding.add') ? 'active' : '' }}"
                                        href="{{ route('factfinding.add', $clientId) }}">
                                        สอบข้อเท็จจริงเบื้องต้น
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('family.add') ? 'active' : '' }}"
                                        href="{{ route('family.add', $clientId) }}">
                                        บันทึกข้อมูลครอบครัว
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('visitFamily.create') ? 'active' : '' }}"
                                        href="{{ route('visitFamily.create', $clientId) }}">
                                        เยี่ยมบ้านครอบครัว
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('member.create') ? 'active' : '' }}"
                                        href="{{ route('member.create', $clientId) }}">
                                        บันทึกสมาชิกครอบครัว
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('client_files.index') ? 'active' : '' }}"
                                        href="{{ route('client_files.index', $clientId) }}">
                                        จัดเก็บไฟล์เอกสาร
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('client.report') ? 'active' : '' }}"
                                        href="{{ route('client.report', $clientId) }}">
                                        รายงานผู้รับบริการ
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link topbar-link dropdown-toggle {{ $isEducationActive ? 'active' : '' }}"
                            href="#" id="educationDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-graduation-cap"></i>
                            <span>การศึกษา</span>
                        </a>
                        <ul class="dropdown-menu topbar-dropdown" aria-labelledby="educationDropdown">
                            @if ($clientId)
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('education_record_add') ? 'active' : '' }}"
                                        href="{{ route('education_record_add', ['client_id' => $clientId]) }}">
                                        บันทึกผลการเรียน
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('education_record_show') ? 'active' : '' }}"
                                        href="{{ route('education_record_show', $clientId) }}">
                                        แสดงผลการเรียน
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('school_followup_add') ? 'active' : '' }}"
                                        href="{{ route('school_followup_add', $clientId) }}">
                                        ติดตามสถานศึกษา
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('absent.add') ? 'active' : '' }}"
                                        href="{{ route('absent.add', $clientId) }}">
                                        บันทึกการขาดเรียน
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link topbar-link dropdown-toggle {{ $isHealthActive ? 'active' : '' }}"
                            href="#" id="healthDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-heartbeat"></i>
                            <span>สุขภาพ</span>
                        </a>

                        <ul class="dropdown-menu topbar-dropdown" aria-labelledby="healthDropdown">

                            @if ($clientId)
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('accident.*') ? 'active' : '' }}"
                                        href="{{ route('accident.add', $clientId) }}">
                                        บันทึกการบาดเจ็บ
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('check_body.*') ? 'active' : '' }}"
                                        href="{{ route('check_body.add', $clientId) }}">
                                        บันทึกการตรวจสุขภาพ
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('medical.*') ? 'active' : '' }}"
                                        href="{{ route('medical.add', $clientId) }}">
                                        บันทึกการรักษาพยาบาล
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('vaccine.*') ? 'active' : '' }}"
                                        href="{{ route('vaccine.index', $clientId) }}">
                                        ประวัติการรับวัคซีน
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('psychiatric.*') ? 'active' : '' }}"
                                        href="{{ route('psychiatric.create', $clientId) }}">
                                        การวินิจฉัยทางจิตเวช
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('addictive.*') ? 'active' : '' }}"
                                        href="{{ route('addictive.create', $clientId) }}">
                                        การตรวจสารเสพติด
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('healthc_heckups.*') ? 'active' : '' }}"
                                        href="{{ route('healthc_heckups.index') }}">
                                        ตรวจสุขภาพประจำปี
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link topbar-link dropdown-toggle {{ $isAssessmentActive ? 'active' : '' }}"
                            href="#" id="assessmentDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-clipboard-check"></i>
                            <span>แบบคัดกรอง</span>
                        </a>

                        <ul class="dropdown-menu topbar-dropdown" aria-labelledby="assessmentDropdown">

                            @if ($clientId)
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('behavior-screenings.*') ? 'active' : '' }}"
                                        href="{{ route('behavior-screenings.index', $clientId) }}">
                                        แบบสังเกตพฤติกรรม 4 โรค
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('snap-iv.*') ? 'active' : '' }}"
                                        href="{{ route('snap-iv.index', $clientId) }}">
                                        แบบประเมิน SNAP-IV
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('depression-screenings.*') ? 'active' : '' }}"
                                        href="{{ route('depression-screenings.index', $clientId) }}">
                                        แบบคัดกรองภาวะซึมเศร้า
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('nutrition_assessments.*') ? 'active' : '' }}"
                                        href="{{ route('nutrition_assessments.index', $clientId) }}">
                                        แบบประเมินภาวะโภชนาการ
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </li>


                    <li class="nav-item dropdown">
                        <a class="nav-link topbar-link dropdown-toggle {{ $isSocialActive ? 'active' : '' }}"
                            href="#" id="socialDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-users"></i>
                            <span>สังคมสงเคราะห์</span>
                        </a>
                        <ul class="dropdown-menu topbar-dropdown" aria-labelledby="socialDropdown">
                            @if ($clientId)

                            @if(
                                        isset($client) &&
                                        $client &&
                                        optional($client->target)->target_name === 'บุคคลไม่มีสถานะทางทะเบียน'
                                    )

                                        <li>
                                            <a class="dropdown-item {{ Request::routeIs('idstation.*') ? 'active' : '' }}"
                                                href="{{ route('idstation.index', $clientId) }}">
                                                <i class="bi bi-person-vcard me-2"></i>
                                                ช่วยเหลือด้านสถานะบุคคล
                                            </a>
                                        </li>

                                    @endif

                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('observe.create') ? 'active' : '' }}"
                                        href="{{ route('observe.create', $clientId) }}">
                                        บันทึกปัญหาพฤติกรรม
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('escape.index') ? 'active' : '' }}"
                                        href="{{ route('escape.index', $clientId) }}">
                                        การหลบหนีจากที่พักพิง
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('case_outside.show') ? 'active' : '' }}"
                                        href="{{ route('case_outside.show', $clientId) }}">
                                        การติดตามเด็กที่อยู่ภายนอก
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('refers.index') ? 'active' : '' }}"
                                        href="{{ route('refers.index', $clientId) }}">
                                        บันทึกการจำหน่าย
                                    </a>
                                </li>

                                    
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('job_agencies.show') ? 'active' : '' }}"
                                        href="{{ route('job_agencies.show', $clientId) }}">
                                        การหางานให้ผู้รับบริการ
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('help_sessions.show') ? 'active' : '' }}"
                                        href="{{ route('help_sessions.show', $clientId) }}">
                                        ช่วยเหลือสิ่งของ/เครื่องใช้
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ Request::routeIs('followup.index') ? 'active' : '' }}"
                                        href="{{ route('followup.index', $clientId) }}">
                                        บันทึกการติดตามผล
                                    </a>
                                </li>

                               <li>
                                <a class="dropdown-item {{ Request::routeIs('case-activities.*') ? 'active' : '' }}"
                                href="{{ route('case-activities.index', $clientId) }}">
                                    ความเคลื่อนไหวผู้รับบริการ
                                </a>
                            </li>
                 
                
                            @endif
                        </ul>
                    </li>
                </ul>

                <!-- Right profile -->
                <ul class="navbar-nav ms-xl-auto align-items-xl-center topbar-profile-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link topbar-user dropdown-toggle" href="#" id="profileDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ !empty($profileData->photo) ? url('upload/user_images/' . $profileData->photo) : url('upload/no_image.jpg') }}"
                                alt="user-image" class="topbar-user-avatar">
                            <span class="topbar-user-meta d-none d-md-flex">
                                <span class="topbar-user-label">ผู้ใช้งาน</span>
                                <span class="topbar-user-name">{{ $profileData->name }}</span>
                            </span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end topbar-dropdown topbar-user-dropdown"
                            aria-labelledby="profileDropdown">
                            <li>
                                <h6 class="dropdown-header">บัญชีผู้ใช้งาน</h6>
                            </li>
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
                            <li>
                                <hr class="dropdown-divider">
                            </li>
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
