@php
    use Illuminate\Support\Facades\Request;
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

    $sidebarClientName = trim((string) (
        $sidebarClient->fullname ??
        $sidebarClient->full_name ??
        $sidebarClient->name ??
        'ผู้รับบริการ'
    )) ?: 'ผู้รับบริการ';

    $sidebarClientImage = asset('upload/no_image.jpg');

    if ($sidebarClient && !empty($sidebarClient->image)) {
        $value = trim((string) $sidebarClient->image);
        if (\Illuminate\Support\Str::startsWith($value, ['http://', 'https://'])) {
            $sidebarClientImage = $value;
        } elseif (\Illuminate\Support\Str::startsWith($value, ['/'])) {
            $sidebarClientImage = url($value);
        } elseif (\Illuminate\Support\Str::startsWith($value, ['upload/', 'storage/'])) {
            $sidebarClientImage = asset($value);
        } else {
            $sidebarClientImage = asset('upload/client_images/' . ltrim($value, '/'));
        }
    }

    $isRegistrationOpen =
        Request::routeIs('client.edit') || Request::routeIs('client.report*') ||
        Request::routeIs('factfinding.*') || Request::routeIs('family.*') ||
        Request::routeIs('estimate.*') || Request::routeIs('visitFamily.*') ||
        Request::routeIs('vitsitFamily.*') || Request::routeIs('member.*') ||
        Request::routeIs('client_files.*');

    $isEducationOpen = Request::routeIs('education_record*') || Request::routeIs('school_followup*') || Request::routeIs('absent.*');
    $isHealthOpen = Request::routeIs('accident.*') || Request::routeIs('check_body.*') || Request::routeIs('medical.*') || Request::routeIs('vaccine.*') || Request::routeIs('psychiatric.*') || Request::routeIs('addictive.*') || Request::routeIs('healthc_heckups.*');
    $isAssessmentOpen = Request::routeIs('behavior-screenings.*') || Request::routeIs('snap-iv.*') || Request::routeIs('depression-screenings.*') || Request::routeIs('nutrition_assessments.*');
    $isSocialOpen = Request::routeIs('observe.*') || Request::routeIs('escape.*') || Request::routeIs('case_outside.*') || Request::routeIs('refers.*') || Request::routeIs('job_agencies.*') || Request::routeIs('help_sessions.*') || Request::routeIs('followup.*') || Request::routeIs('case-activities.*') || Request::routeIs('idstation.*');

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

    .client-sidebar-panel #side-menu { padding-top: 6px !important; }

    .sidebar-client-card-wrap { list-style: none; margin: 0 !important; padding: 6px 13px 12px; }

    #side-menu > li.sidebar-client-card-wrap > a.sidebar-client-card {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 10px; min-height: 138px; padding: 14px 12px !important; color: #0f172a;
        text-decoration: none; background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
        border: 1px solid rgba(148,163,184,.18); border-radius: 18px;
        box-shadow: 0 8px 20px rgba(15,23,42,.06); transition: .18s ease;
    }

    #side-menu > li.sidebar-client-card-wrap > a.sidebar-client-card:hover {
        border-color: rgba(59,130,246,.22); box-shadow: 0 12px 24px rgba(37,99,235,.10);
        transform: translateY(-1px);
    }

    .sidebar-client-avatar {
        position: relative; width: 72px; height: 72px; padding: 3px; background: #fff;
        border: 1px solid rgba(203,213,225,.9); border-radius: 50%;
        box-shadow: 0 6px 16px rgba(15,23,42,.10);
    }

    .sidebar-client-avatar::after {
        content: ''; position: absolute; right: 1px; bottom: 2px; width: 13px; height: 13px;
        background: #22c55e; border: 2px solid #fff; border-radius: 50%;
    }

    .sidebar-client-avatar img { width: 100%; height: 100%; object-fit: cover; object-position: center top; border-radius: 50%; }
    .sidebar-client-name { max-width: 100%; overflow: hidden; color: #1e293b; font-size: 13.25px; font-weight: 700; line-height: 1.38; text-align: center; }
</style>

<div class="app-sidebar-menu">
    <div class="sidebar-scroll h-100" data-simplebar>
        <div id="sidebar-menu">
            <ul id="side-menu" class="metismenu list-unstyled">
                @if ($sidebarClient && $clientId)
                    <li class="sidebar-client-card-wrap">
                        <a href="{{ route('admin.index', $clientId) }}" class="sidebar-client-card" title="เปิดข้อมูลผู้รับบริการ : {{ $sidebarClientName }}">
                            <span class="sidebar-client-avatar" aria-hidden="true">
                                <img src="{{ $sidebarClientImage }}" alt="รูปผู้รับบริการ {{ $sidebarClientName }}"
                                     onerror="this.onerror=null;this.src='{{ asset('upload/no_image.jpg') }}';">
                            </span>
                            <span class="sidebar-client-name">{{ $sidebarClientName }}</span>
                        </a>
                    </li>
                @endif

                @if ($groupAccess['registration'] ?? false)
                    <li class="menu-title">ทะเบียนแรกเข้า</li>
                    <li>
                        <a href="#sidebarRegistration" data-bs-toggle="collapse" aria-expanded="{{ $isRegistrationOpen ? 'true' : 'false' }}" class="{{ $isRegistrationOpen ? 'active' : '' }}">
                            <i data-feather="home"></i><span>ทะเบียนแรกเข้า</span><span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isRegistrationOpen ? 'show' : '' }}" id="sidebarRegistration">
                            <ul class="nav-second-level">
                                @if ($clientId && $canForm('registration_client_profile'))<li><a href="{{ route('client.edit', $clientId) }}" class="tp-link {{ Request::routeIs('client.edit') ? 'active' : '' }}">ประวัติผู้รับบริการ</a></li>@endif
                                @if ($clientId && $canForm('registration_factfinding'))<li><a href="{{ route('factfinding.add', $clientId) }}" class="tp-link {{ Request::routeIs('factfinding.*') ? 'active' : '' }}">สอบข้อเท็จจริงเบื้องต้น</a></li>@endif
                                @if ($clientId && $canForm('registration_family'))<li><a href="{{ route('family.add', $clientId) }}" class="tp-link {{ Request::routeIs('family.*') || Request::routeIs('estimate.*') ? 'active' : '' }}">บันทึกข้อมูลครอบครัว</a></li>@endif
                                @if ($clientId && $canForm('registration_family_visit'))<li><a href="{{ route('visitFamily.create', $clientId) }}" class="tp-link {{ Request::routeIs('visitFamily.*') || Request::routeIs('vitsitFamily.*') ? 'active' : '' }}">เยี่ยมครอบครัว</a></li>@endif
                                @if ($clientId && $canForm('registration_family_members'))<li><a href="{{ route('member.create', $clientId) }}" class="tp-link {{ Request::routeIs('member.*') ? 'active' : '' }}">บันทึกสมาชิกครอบครัว</a></li>@endif
                                @if ($clientId && $canForm('registration_client_files'))<li><a href="{{ route('client_files.index', $clientId) }}" class="tp-link {{ Request::routeIs('client_files.*') ? 'active' : '' }}">จัดเก็บไฟล์เอกสาร</a></li>@endif
                                @if ($clientId && $canForm('registration_client_reports'))<li><a href="{{ route('client.report', $clientId) }}" class="tp-link {{ Request::routeIs('client.report*') ? 'active' : '' }}">รายงานผู้รับบริการ</a></li>@endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if ($groupAccess['education'] ?? false)
                    <li class="menu-title mt-2">การศึกษา</li>
                    <li>
                        <a href="#sidebarEducation" data-bs-toggle="collapse" aria-expanded="{{ $isEducationOpen ? 'true' : 'false' }}" class="{{ $isEducationOpen ? 'active' : '' }}">
                            <i class="fas fa-graduation-cap sidebar-fa-icon"></i><span>ข้อมูลการศึกษา</span><span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isEducationOpen ? 'show' : '' }}" id="sidebarEducation"><ul class="nav-second-level">
                            @if ($clientId && $canForm('education_grade_entry'))<li><a href="{{ route('education_record_add', ['client_id' => $clientId]) }}" class="tp-link {{ Request::routeIs('education_record_add') ? 'active' : '' }}">บันทึกผลการเรียน</a></li>@endif
                            @if ($clientId && $canForm('education_results'))<li><a href="{{ route('education_record_show', $clientId) }}" class="tp-link {{ Request::routeIs('education_record_show') ? 'active' : '' }}">แสดงผลการเรียน</a></li>@endif
                            @if ($clientId && $canForm('education_followup'))<li><a href="{{ route('school_followup_add', $clientId) }}" class="tp-link {{ Request::routeIs('school_followup*') ? 'active' : '' }}">ติดตามการศึกษา</a></li>@endif
                            @if ($clientId && $canForm('education_absence'))<li><a href="{{ route('absent.add', $clientId) }}" class="tp-link {{ Request::routeIs('absent.*') ? 'active' : '' }}">บันทึกการขาดเรียน</a></li>@endif
                        </ul></div>
                    </li>
                @endif

                @if ($groupAccess['health'] ?? false)
                    <li class="menu-title mt-2">สุขภาพ</li>
                    <li>
                        <a href="#sidebarHealth" data-bs-toggle="collapse" aria-expanded="{{ $isHealthOpen ? 'true' : 'false' }}" class="{{ $isHealthOpen ? 'active' : '' }}">
                            <i class="fas fa-heartbeat sidebar-fa-icon"></i><span>ข้อมูลสุขภาพ</span><span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isHealthOpen ? 'show' : '' }}" id="sidebarHealth"><ul class="nav-second-level">
                            @if ($clientId && $canForm('health_accident'))<li><a href="{{ route('accident.add', $clientId) }}" class="tp-link {{ Request::routeIs('accident.*') ? 'active' : '' }}">บันทึกการบาดเจ็บ</a></li>@endif
                            @if ($clientId && $canForm('health_body_check'))<li><a href="{{ route('check_body.add', $clientId) }}" class="tp-link {{ Request::routeIs('check_body.*') ? 'active' : '' }}">ตรวจสุขภาพเบื้องต้น</a></li>@endif
                            @if ($clientId && $canForm('health_medical'))<li><a href="{{ route('medical.add', $clientId) }}" class="tp-link {{ Request::routeIs('medical.*') ? 'active' : '' }}">การรักษาพยาบาล</a></li>@endif
                            @if ($clientId && $canForm('health_vaccination'))<li><a href="{{ route('vaccine.index', $clientId) }}" class="tp-link {{ Request::routeIs('vaccine.*') ? 'active' : '' }}">ประวัติการรับวัคซีน</a></li>@endif
                            @if ($clientId && $canForm('health_psychiatric'))<li><a href="{{ route('psychiatric.create', $clientId) }}" class="tp-link {{ Request::routeIs('psychiatric.*') ? 'active' : '' }}">การวินิจฉัยทางจิตเวช</a></li>@endif
                            @if ($clientId && $canForm('health_addictive'))<li><a href="{{ route('addictive.create', $clientId) }}" class="tp-link {{ Request::routeIs('addictive.*') ? 'active' : '' }}">การตรวจสารเสพติด</a></li>@endif
                            @if ($canForm('health_annual_checkup'))<li><a href="{{ route('healthc_heckups.index') }}" class="tp-link {{ Request::routeIs('healthc_heckups.*') ? 'active' : '' }}">ตรวจสุขภาพประจำปี</a></li>@endif
                        </ul></div>
                    </li>
                @endif

                @if ($groupAccess['screening'] ?? false)
                    <li class="menu-title mt-2">แบบประเมินและคัดกรอง</li>
                    <li>
                        <a href="#sidebarAssessment" data-bs-toggle="collapse" aria-expanded="{{ $isAssessmentOpen ? 'true' : 'false' }}" class="{{ $isAssessmentOpen ? 'active' : '' }}">
                            <i class="bi bi-clipboard2-pulse sidebar-fa-icon"></i><span>แบบคัดกรอง</span><span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isAssessmentOpen ? 'show' : '' }}" id="sidebarAssessment"><ul class="nav-second-level">
                            @if ($clientId && $canForm('screening_behavior_four_diseases'))<li><a href="{{ route('behavior-screenings.index', $clientId) }}" class="tp-link {{ Request::routeIs('behavior-screenings.*') ? 'active' : '' }}">แบบสังเกตพฤติกรรม 4 โรค</a></li>@endif
                            @if ($clientId && $canForm('screening_snap_iv'))<li><a href="{{ route('snap-iv.index', $clientId) }}" class="tp-link {{ Request::routeIs('snap-iv.*') ? 'active' : '' }}">แบบประเมิน SNAP-IV</a></li>@endif
                            @if ($clientId && $canForm('screening_depression'))<li><a href="{{ route('depression-screenings.index', $clientId) }}" class="tp-link {{ Request::routeIs('depression-screenings.*') ? 'active' : '' }}">แบบคัดกรองภาวะซึมเศร้า</a></li>@endif
                            @if ($clientId && $canForm('screening_nutrition'))<li><a href="{{ route('nutrition_assessments.index', $clientId) }}" class="tp-link {{ Request::routeIs('nutrition_assessments.*') ? 'active' : '' }}">แบบประเมินภาวะโภชนาการ</a></li>@endif
                        </ul></div>
                    </li>
                @endif

                @if ($groupAccess['social_welfare'] ?? false)
                    <li class="menu-title mt-2">สังคมสงเคราะห์</li>
                    <li>
                        <a href="#sidebarSocial" data-bs-toggle="collapse" aria-expanded="{{ $isSocialOpen ? 'true' : 'false' }}" class="{{ $isSocialOpen ? 'active' : '' }}">
                            <i class="fas fa-users sidebar-fa-icon"></i><span>สังคมสงเคราะห์</span><span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isSocialOpen ? 'show' : '' }}" id="sidebarSocial"><ul class="nav-second-level">
                            @if ($clientId && $isStatelessClient && $canForm('welfare_stateless_person'))<li><a href="{{ route('idstation.index', $clientId) }}" class="tp-link {{ Request::routeIs('idstation.*') ? 'active' : '' }}">ช่วยเหลือด้านสถานะบุคคล</a></li>@endif
                            @if ($clientId && $canForm('welfare_behavior_problem'))<li><a href="{{ route('observe.create', $clientId) }}" class="tp-link {{ Request::routeIs('observe.*') ? 'active' : '' }}">บันทึกปัญหาพฤติกรรม</a></li>@endif
                            @if ($clientId && $canForm('welfare_escape'))<li><a href="{{ route('escape.index', $clientId) }}" class="tp-link {{ Request::routeIs('escape.*') ? 'active' : '' }}">การหลบหนีจากที่พักพิง</a></li>@endif
                            @if ($clientId && $canForm('welfare_outside_followup'))<li><a href="{{ route('case_outside.show', $clientId) }}" class="tp-link {{ Request::routeIs('case_outside.*') ? 'active' : '' }}">ติดตามเด็กที่อยู่ภายนอก</a></li>@endif
                            @if ($clientId && $canForm('welfare_discharge'))<li><a href="{{ route('refers.index', $clientId) }}" class="tp-link {{ Request::routeIs('refers.*') ? 'active' : '' }}">บันทึกการจำหน่าย</a></li>@endif
                            @if ($clientId && $canForm('welfare_job_agency'))<li><a href="{{ route('job_agencies.show', $clientId) }}" class="tp-link {{ Request::routeIs('job_agencies.*') ? 'active' : '' }}">การหางานให้ผู้รับบริการ</a></li>@endif
                            @if ($clientId && $canForm('welfare_help_items'))<li><a href="{{ route('help_sessions.show', $clientId) }}" class="tp-link {{ Request::routeIs('help_sessions.*') ? 'active' : '' }}">ช่วยเหลือสิ่งของ/เครื่องใช้</a></li>@endif
                            @if ($clientId && $canForm('welfare_followup'))<li><a href="{{ route('followup.index', $clientId) }}" class="tp-link {{ Request::routeIs('followup.*') ? 'active' : '' }}">บันทึกการติดตาม</a></li>@endif
                            @if ($clientId && $canForm('welfare_client_activity'))<li><a href="{{ route('case-activities.index', $clientId) }}" class="tp-link {{ Request::routeIs('case-activities.*') ? 'active' : '' }}">ความเคลื่อนไหวผู้รับบริการ</a></li>@endif
                        </ul></div>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>

{{-- FORM_PERMISSION_UI_V6 --}}
@include('components.form_permission_ui')