@php
    $routeClient = request()->route('client') ?? (request()->route('client_id') ?? (request()->route('id') ?? null));

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

    /*
    |--------------------------------------------------------------------------
    | บันทึกข้อมูลแรกเข้า
    |--------------------------------------------------------------------------
    */

    $isProfileOpen =
        Request::routeIs('client.show') ||
        Request::routeIs('client.report') ||
        Request::routeIs('client.edit') ||
        Request::routeIs('case-activities.*') ||
        Request::routeIs('issues.index');

    /*
    |--------------------------------------------------------------------------
    | ข้อมูลผู้ใช้
    |--------------------------------------------------------------------------
    */

    $isProcessOpen =
        Request::routeIs('factfinding.*') ||
        Request::routeIs('family.*') ||
        Request::routeIs('visitFamily.*') ||
        Request::routeIs('member.*') ||
        Request::routeIs('estimate.*') ||
        Request::routeIs('client_files.*');

    /*
    |--------------------------------------------------------------------------
    | ข้อมูลการศึกษา
    |--------------------------------------------------------------------------
    */

    $isEducationOpen =
        Request::routeIs('education_record_*') || Request::routeIs('school_followup_*') || Request::routeIs('absent.*');

    /*
    |--------------------------------------------------------------------------
    | ข้อมูลสุขภาพ
    |--------------------------------------------------------------------------
    */

    $isHealthOpen =
        Request::routeIs('accident.*') ||
        Request::routeIs('check_body.*') ||
        Request::routeIs('medical.*') ||
        Request::routeIs('vaccine.*') ||
        Request::routeIs('psychiatric.*') ||
        Request::routeIs('addictive.*') ||
        Request::routeIs('healthc_heckups.*');

    /*
    |--------------------------------------------------------------------------
    | แบบประเมิน / คัดกรอง
    |--------------------------------------------------------------------------
    */

   $isAssessmentOpen =
    Request::routeIs('behavior-screenings.*') ||
    Request::routeIs('snap-iv.*') ||
    Request::routeIs('depression-screenings.*') ||
    Request::routeIs('nutrition_assessments.*');

    /*
    |--------------------------------------------------------------------------
    | ข้อมูลด้านพฤติกรรม
    |--------------------------------------------------------------------------
    */

    $isBehaviorOpen = Request::routeIs('observe.*') || Request::routeIs('escape.*');

    /*
    |--------------------------------------------------------------------------
    | ข้อมูลด้านสังคม
    |--------------------------------------------------------------------------
    */

    $isSocialOpen =
        Request::routeIs('case_outside.*') ||
        Request::routeIs('refers.*') ||
        Request::routeIs('job_agencies.*') ||
        Request::routeIs('help_sessions.*') ||
        Request::routeIs('followup.*') ||
        Request::routeIs('idstation.*');
@endphp

<style>
    .behavior-screening-main-link {
        font-weight: 600;
    }

    .behavior-screening-main-link i {
        color: #2563eb;
        font-size: 17px;
    }

    .behavior-screening-main-link.active i {
        color: inherit;
    }
</style>

<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>
        <div id="sidebar-menu">

            <ul id="side-menu" class="metismenu list-unstyled pt-2">

                <li class="menu-title">ทะเบียนประวัติ</li>

                <li>
                    <a href="#sidebarProfile" data-bs-toggle="collapse"
                        aria-expanded="{{ $isProfileOpen ? 'true' : 'false' }}"
                        class="{{ $isProfileOpen ? 'active' : '' }}">
                        <i data-feather="home"></i>
                        <span> บันทึกข้อมูลแรกเข้า </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="collapse {{ $isProfileOpen ? 'show' : '' }}" id="sidebarProfile">
                        <ul class="nav-second-level">
                            @if ($clientId)
                                <li>
                                    <a href="{{ route('client.edit', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('client.edit') ? 'active' : '' }}">
                                        บันทึกข้อมูลทั่วไป
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('client.report', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('client.report') ? 'active' : '' }}">
                                        รายงานผู้รับบริการ
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('case-activities.index', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('case-activities.*') ? 'active' : '' }}">
                                        ความเคลื่อนไหวผู้รับบริการ
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>

                <li class="menu-title">กระบวนการ</li>

                <li>
                    <a href="#sidebarProcess" data-bs-toggle="collapse"
                        aria-expanded="{{ $isProcessOpen ? 'true' : 'false' }}"
                        class="{{ $isProcessOpen ? 'active' : '' }}">
                        <i data-feather="users"></i>
                        <span> ข้อมูลผู้ใช้ </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="collapse {{ $isProcessOpen ? 'show' : '' }}" id="sidebarProcess">
                        <ul class="nav-second-level">
                            @if ($clientId)
                                <li>
                                    <a href="{{ route('factfinding.add', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('factfinding.*') ? 'active' : '' }}">
                                        สอบข้อเท็จจริง
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('family.add', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('family.*') ? 'active' : '' }}">
                                        บันทึกครอบครัว
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('visitFamily.create', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('visitFamily.*') ? 'active' : '' }}">
                                        เยี่ยมบ้านครอบครัว
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('member.create', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('member.*') ? 'active' : '' }}">
                                        บันทึกสมาชิกครอบครัว
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('estimate.show', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('estimate.*') ? 'active' : '' }}">
                                        ประเมินครอบครัว
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('client_files.index', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('client_files.*') ? 'active' : '' }}">
                                        รายการไฟล์เอกสาร
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#sidebarEducation" data-bs-toggle="collapse"
                        aria-expanded="{{ $isEducationOpen ? 'true' : 'false' }}"
                        class="{{ $isEducationOpen ? 'active' : '' }}">
                        <i class="fas fa-graduation-cap sidebar-fa-icon"></i>
                        <span> ข้อมูลการศึกษา </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="collapse {{ $isEducationOpen ? 'show' : '' }}" id="sidebarEducation">
                        <ul class="nav-second-level">
                            @if ($clientId)
                                <li>
                                    <a href="{{ route('education_record_add', ['client_id' => $clientId]) }}"
                                        class="tp-link {{ Request::routeIs('education_record_add') ? 'active' : '' }}">
                                        บันทึกผลการเรียน
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('education_record_show', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('education_record_show') ? 'active' : '' }}">
                                        แสดงผลการเรียน
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('school_followup_add', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('school_followup_*') ? 'active' : '' }}">
                                        ติดตามการศึกษา
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('absent.add', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('absent.*') ? 'active' : '' }}">
                                        บันทึกการขาดเรียน
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#sidebarHealth" data-bs-toggle="collapse"
                        aria-expanded="{{ $isHealthOpen ? 'true' : 'false' }}"
                        class="{{ $isHealthOpen ? 'active' : '' }}">
                        <i class="fas fa-heartbeat sidebar-fa-icon"></i>
                        <span> ข้อมูลสุขภาพ </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="collapse {{ $isHealthOpen ? 'show' : '' }}" id="sidebarHealth">
                        <ul class="nav-second-level">
                            @if ($clientId)
                                <li>
                                    <a href="{{ route('accident.add', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('accident.*') ? 'active' : '' }}">
                                        บันทึกการบาดเจ็บ
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('check_body.add', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('check_body.*') ? 'active' : '' }}">
                                        ตรวจสุขภาพเบื้องต้น
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('medical.add', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('medical.*') ? 'active' : '' }}">
                                        การรักษาพยาบาล
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('vaccine.index', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('vaccine.*') ? 'active' : '' }}">
                                        ประวัติการรับวัคซีน
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('psychiatric.create', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('psychiatric.*') ? 'active' : '' }}">
                                        การวินิจฉัยทางจิตเวช
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('addictive.create', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('addictive.*') ? 'active' : '' }}">
                                        การตรวจสารเสพติด
                                    </a>
                                </li>
                            @endif

                            <li>
                                <a href="{{ route('healthc_heckups.index') }}"
                                    class="tp-link {{ Request::routeIs('healthc_heckups.*') ? 'active' : '' }}">
                                    ตรวจสุขภาพประจำปี
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-title mt-2">แบบประเมินและคัดกรอง</li>

                <li>
                    <a href="#sidebarAssessment" data-bs-toggle="collapse"
                        aria-expanded="{{ $isAssessmentOpen ? 'true' : 'false' }}"
                        class="{{ $isAssessmentOpen ? 'active' : '' }}">
                        <i class="bi bi-clipboard2-pulse sidebar-fa-icon"></i>
                        <span> แบบคัดกรอง </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="collapse {{ $isAssessmentOpen ? 'show' : '' }}" id="sidebarAssessment">
                        <ul class="nav-second-level">
                            @if ($clientId)
                                <li>
                                    <a href="{{ route('behavior-screenings.index', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('behavior-screenings.*') ? 'active' : '' }}">
                                        แบบประเมินพัฒนาการ
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('snap-iv.index', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('snap-iv.*') ? 'active' : '' }}">
                                        แบบประเมิน SNAP-IV
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('depression-screenings.index', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('depression-screenings.*') ? 'active' : '' }}">
                                        แบบคัดกรองภาวะซึมเศร้า
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('nutrition_assessments.index', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('nutrition_assessments.*') ? 'active' : '' }}">
                                        แบบประเมินภาวะโภชนาการ
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>

                <li class="menu-title mt-2">ด้านพฤติกรรม</li>

                <li>
                    <a href="#sidebarBehavior" data-bs-toggle="collapse"
                        aria-expanded="{{ $isBehaviorOpen ? 'true' : 'false' }}"
                        class="{{ $isBehaviorOpen ? 'active' : '' }}">
                        <i data-feather="package"></i>
                        <span> ด้านพฤติกรรม </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="collapse {{ $isBehaviorOpen ? 'show' : '' }}" id="sidebarBehavior">
                        <ul class="nav-second-level">
                            @if ($clientId)
                                <li>
                                    <a href="{{ route('observe.create', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('observe.*') ? 'active' : '' }}">
                                        บันทึกพฤติกรรม
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('escape.index', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('escape.*') ? 'active' : '' }}">
                                        หนีออกจากบ้าน
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>

                <li class="menu-title mt-2">ข้อมูลด้านสังคม</li>

                <li>
                    <a href="#sidebarSocial" data-bs-toggle="collapse"
                        aria-expanded="{{ $isSocialOpen ? 'true' : 'false' }}"
                        class="{{ $isSocialOpen ? 'active' : '' }}">
                        <i class="fas fa-users sidebar-fa-icon"></i>
                        <span> สังคมสงเคราะห์ </span>
                        <span class="menu-arrow"></span>
                    </a>

                    <div class="collapse {{ $isSocialOpen ? 'show' : '' }}" id="sidebarSocial">
                        <ul class="nav-second-level">
                            @if ($clientId)

                                 @if(
                                        isset($client) &&
                                        $client &&
                                        optional($client->target)->target_name === 'บุคคลไม่มีสถานะทางทะเบียน'
                                    )

                                        <li>
                                            <a href="{{ route('idstation.index', $clientId) }}"
                                                class="tp-link {{ Request::routeIs('idstation.*') ? 'active' : '' }}">
                                                {{-- <i class="bi bi-person-vcard me-1"></i> --}}
                                                ช่วยเหลือด้านสถานะบุคคล
                                            </a>
                                        </li>

                                    @endif

                                <li>
                                    <a href="{{ route('case_outside.show', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('case_outside.*') ? 'active' : '' }}">
                                        ติดตามเด็กที่อยู่ภายนอก
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('refers.index', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('refers.*') ? 'active' : '' }}">
                                        รายการการจำหน่าย
                                    </a>
                                </li>

                               

                                <li>
                                    <a href="{{ route('job_agencies.show', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('job_agencies.*') ? 'active' : '' }}">
                                        หางานให้ผู้รับบริการ
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('help_sessions.show', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('help_sessions.*') ? 'active' : '' }}">
                                        รายการการช่วยเหลือ
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('followup.index', $clientId) }}"
                                        class="tp-link {{ Request::routeIs('followup.*') ? 'active' : '' }}">
                                        การติดตามผล
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</div>
