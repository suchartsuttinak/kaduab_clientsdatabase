@php
    $routeClient = request()->route('client')
        ?? request()->route('client_id')
        ?? request()->route('id')
        ?? null;

    $sidebarClientId = null;

    if (isset($client) && is_object($client)) {
        $sidebarClientId = $client->id;
    } elseif (isset($client) && is_numeric($client)) {
        $sidebarClientId = $client;
    } elseif (is_object($routeClient)) {
        $sidebarClientId = $routeClient->id ?? null;
    } elseif (is_numeric($routeClient)) {
        $sidebarClientId = $routeClient;
    }

    $isProfileOpen =
        Request::routeIs('client.show') ||
        Request::routeIs('client.cases') ||
        Request::routeIs('client.transfers') ||
        Request::routeIs('client.transfer.*') ||
        Request::routeIs('client-house-transfers.*');

  

    $isDashboardOpen =
        Request::routeIs('issues.index') ||
        Request::routeIs('news.create') ||
        Request::routeIs('landing.about.index') ||
        Request::routeIs('scholarship.index') ||
        Request::routeIs('scholarship.children.*');

    $isMasterMenu =
        Request::routeIs('institution.*') ||
        Request::routeIs('subject.*') ||
        Request::routeIs('house.*') ||
        Request::routeIs('education.*') ||
        Request::routeIs('semester.*') ||
        Request::routeIs('psycho.*') ||
        Request::routeIs('misbehavior.*') ||
        Request::routeIs('outside.*') ||
        Request::routeIs('document.*') ||
        Request::routeIs('income.*') ||
        Request::routeIs('help_type.*') ||
        Request::routeIs('citizenship.*') ||
        Request::routeIs('citizen.*') ||
        Request::routeIs('translate.*');

    $isUserMenu =
        Request::routeIs('users.index') ||
        Request::routeIs('users.create') ||
        Request::routeIs('users.edit');

    $isIdstationCentralMenu =
        Request::routeIs('idstation.central.*');
        
@endphp

<style>
    /* ===== Sidebar arrow fix ===== */
    .app-sidebar-menu.sidebar-arrow-fix #side-menu li > a {
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
    }

    /* ปิดลูกศร/ไอคอนแทรกจาก theme เดิมทั้งหมด */
    .app-sidebar-menu.sidebar-arrow-fix #side-menu li > a::after,
    .app-sidebar-menu.sidebar-arrow-fix .metismenu .has-arrow::after,
    .app-sidebar-menu.sidebar-arrow-fix .metismenu .menu-arrow::after,
    .app-sidebar-menu.sidebar-arrow-fix .metismenu .menu-arrow::before {
        content: none !important;
        display: none !important;
    }

    /* สร้างลูกศรใหม่เฉพาะตัวที่ต้องใช้ */
    .app-sidebar-menu.sidebar-arrow-fix .menu-arrow {
        margin-left: auto;
        width: 10px;
        min-width: 10px;
        height: 10px;
        display: inline-block;
        position: relative;
        flex: 0 0 10px;
    }

    .app-sidebar-menu.sidebar-arrow-fix .menu-arrow > span,
    .app-sidebar-menu.sidebar-arrow-fix .menu-arrow i,
    .app-sidebar-menu.sidebar-arrow-fix .menu-arrow svg {
        display: none !important;
    }

    .app-sidebar-menu.sidebar-arrow-fix .menu-arrow-custom::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 7px;
        height: 7px;
        border-right: 2px solid #64748b;
        border-bottom: 2px solid #64748b;
        transform: translate(-50%, -58%) rotate(-45deg);
        transition: transform .2s ease;
        box-sizing: border-box;
    }

    .app-sidebar-menu.sidebar-arrow-fix a[aria-expanded="true"] .menu-arrow-custom::before {
        transform: translate(-50%, -42%) rotate(45deg);
    }

    /* ป้องกัน feather render แล้วกระพริบ/เหลื่อม */
    .app-sidebar-menu.sidebar-arrow-fix i[data-feather] {
        opacity: 0;
    }

    .app-sidebar-menu.sidebar-arrow-fix.sidebar-icons-ready i[data-feather],
    .app-sidebar-menu.sidebar-arrow-fix.sidebar-icons-ready svg.feather {
        opacity: 1;
    }

    /* ลิงก์ที่มี badge + arrow */
    .app-sidebar-menu.sidebar-arrow-fix .menu-link-with-badge {
        display: flex;
        align-items: center;
        width: 100%;
        gap: 10px;
    }

    .app-sidebar-menu.sidebar-arrow-fix .menu-link-with-badge .menu-text {
        min-width: 0;
        white-space: nowrap;
    }

    .sidebar-badge-soft {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-left: 6px;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.4;
        background: rgba(59, 130, 246, .12);
        color: #2563eb;
        vertical-align: middle;
        flex: 0 0 auto;
    }

    .sidebar-user-link-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        min-width: 18px;
        margin-right: 8px;
        font-size: 15px;
        opacity: .92;
    }

    .nav-second-level .tp-link.with-icon {
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all .2s ease;
    }

    .nav-second-level .tp-link.with-icon:hover {
        transform: translateX(2px);
    }

    .menu-title-with-icon {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .menu-title-with-icon i {
        font-size: 14px;
        opacity: .85;
    }

    /* ระยะห่างไอคอนหลัก */
    .app-sidebar-menu.sidebar-arrow-fix #side-menu > li > a > i,
    .app-sidebar-menu.sidebar-arrow-fix #side-menu > li > a > svg,
    .app-sidebar-menu.sidebar-arrow-fix #side-menu > li > a > .bi {
        flex: 0 0 18px;
    }

    /* กันข้อความชน */
    .app-sidebar-menu.sidebar-arrow-fix #side-menu > li > a > span:not(.menu-arrow):not(.sidebar-badge-soft) {
        min-width: 0;
    }
</style>

    

<div class="app-sidebar-menu sidebar-arrow-fix" id="stableMasterSidebar">
    <div class="h-100" data-simplebar>
        <div id="sidebar-menu">

            <div class="logo-box">
                <a href="{{ url('/') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('backend/assets/images/logo-sm.png') }}" height="22" alt="logo-sm">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('backend/assets/images/logo-light.png') }}" height="24" alt="logo-lg">
                    </span>
                </a>
            </div>

            <ul id="side-menu" class="metismenu list-unstyled pt-2">

                {{-- =========================
                    ทะเบียนประวัติ
                ========================== --}}
                <li class="menu-title">ทะเบียนประวัติ</li>
                <li>
                    <a href="#sidebarProfile"
                       data-bs-toggle="collapse"
                       aria-expanded="{{ $isProfileOpen ? 'true' : 'false' }}"
                       class="{{ $isProfileOpen ? 'active' : '' }}">
                        <i data-feather="users"></i>
                        <span>บันทึกข้อมูลแรกเข้า</span>
                        <span class="menu-arrow menu-arrow-custom"></span>
                    </a>

                     <div class="collapse {{ $isProfileOpen ? 'show' : '' }}" id="sidebarProfile">
                        <ul class="nav-second-level">

                           <li>
                                <a href="{{ route('client.show') }}"
                                class="tp-link {{ Request::routeIs('client.show') ? 'active' : '' }}">
                                    <i class="bi bi-people-fill me-2"></i>
                                    ทะเบียนผู้รับบริการ
                                </a>
                            </li>
                          
                            @if(auth()->check() && auth()->user()->role === 'admin')

                                <li>
                                    <a href="{{ route('client.cases') }}"
                                    class="tp-link {{ Request::routeIs('client.cases') ? 'active' : '' }}">
                                        <i class="bi bi-database-fill me-2"></i>
                                        ทะเบียนกลางเคสทั้งหมด
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('client.transfers') }}"
                                    class="tp-link {{ Request::routeIs('client.transfers') || Request::routeIs('client.transfer.*') ? 'active' : '' }}">
                                        <i class="bi bi-arrow-left-right me-2"></i>
                                        ย้ายโปรเจ็คต์
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('client-house-transfers.index') }}"
                                    class="tp-link {{ Request::routeIs('client-house-transfers.*') ? 'active' : '' }}">
                                        <i class="bi bi-house-door-fill me-2"></i>
                                        ย้ายสถานที่พักพิง
                                    </a>
                                </li>

                            @endif

                        </ul>
                    </div>
                </li>



               
                {{-- =========================
                    Dashboard
                ========================== --}}
                <li class="menu-title">Dashboard</li>
               <li>
    <a href="#sidebarDashboard"
       data-bs-toggle="collapse"
       aria-expanded="{{ $isDashboardOpen ? 'true' : 'false' }}"
       class="{{ $isDashboardOpen ? 'active' : '' }}">
        <i data-feather="layout"></i>
        <span>เข้าสู่หน้ายินดีต้อนรับ</span>
        <span class="menu-arrow menu-arrow-custom"></span>
    </a>

    <div class="collapse {{ $isDashboardOpen ? 'show' : '' }}" id="sidebarDashboard">
        <ul class="nav-second-level">

            <li>
                <a href="{{ route('issues.index') }}"
                   class="tp-link {{ Request::routeIs('issues.index') ? 'active' : '' }}">
                    แจ้งเรื่องช่วยเหลือ
                </a>
            </li>

            <li>
                <a href="{{ route('news.create') }}"
                   class="tp-link {{ Request::routeIs('news.create') ? 'active' : '' }}">
                    เพิ่มข่าวสาร
                </a>
            </li>

            <li>
                <a href="{{ route('landing.about.index') }}"
                   class="tp-link {{ Request::routeIs('landing.about.index') ? 'active' : '' }}">
                    ประวัติความเป็นมา
                </a>
            </li>

                @if(auth()->check() && auth()->user()->role === 'admin')
                    <li>
                        <a href="{{ route('scholarship.index') }}"
                        class="tp-link {{ Request::routeIs('scholarship.index') ? 'active' : '' }}">
                            ผู้สนับสนุนทุนการศึกษา
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('scholarship.children.index') }}"
                        class="tp-link {{ Request::routeIs('scholarship.children.*') ? 'active' : '' }}">
                            ทุนการศึกษาเด็ก
                        </a>
                    </li>
                    @endif

                            </ul>
                        </div>
                    </li>


                {{-- =========================
                    ศูนย์กลางทะเบียน
                ========================== --}}
                <li class="menu-title">ศูนย์กลางทะเบียน</li>

                <li>
                    <a href="#sidebarIdstationCentral"
                    data-bs-toggle="collapse"
                    aria-expanded="{{ $isIdstationCentralMenu ? 'true' : 'false' }}"
                    class="{{ $isIdstationCentralMenu ? 'active' : '' }}">

                        <i data-feather="database"></i>

                        <span>บุคคลไรัสัญชาติ</span>

                        <span class="menu-arrow menu-arrow-custom"></span>

                    </a>

                    <div class="collapse {{ $isIdstationCentralMenu ? 'show' : '' }}"
                        id="sidebarIdstationCentral">

                        <ul class="nav-second-level">

                            <li>
                                <a href="{{ route('idstation.central.index') }}"
                                class="tp-link {{ Request::routeIs('idstation.central.index') ? 'active' : '' }}">

                                    <i class="bi bi-speedometer2 me-2"></i>

                                    ศูนย์กลางข้อมูล

                                </a>
                            </li>

                            <li>
                                <a href="{{ route('idstation.central.report') }}"
                                class="tp-link {{ Request::routeIs('idstation.central.report') ? 'active' : '' }}">

                                    <i class="bi bi-file-earmark-text me-2"></i>

                                    รายงานสรุป

                                </a>
                            </li>

                        </ul>

                    </div>
                </li>


                {{-- =========================
                    ข้อมูลอ้างอิง
                ========================== --}}
                @if(auth()->check() && auth()->user()->role === 'admin')
                    <li class="menu-title">ข้อมูลอ้างอิง</li>
                    <li>
                        <a href="#sidebar-master-data"
                           data-bs-toggle="collapse"
                           aria-expanded="{{ $isMasterMenu ? 'true' : 'false' }}"
                           class="{{ $isMasterMenu ? 'active' : '' }}">
                            <i data-feather="grid"></i>
                            <span>ประเภท / หมวดหมู่</span>
                            <span class="menu-arrow menu-arrow-custom"></span>
                        </a>

                        <div class="collapse {{ $isMasterMenu ? 'show' : '' }}" id="sidebar-master-data">
                            <ul class="nav-second-level">
                                <li><a href="{{ route('institution.all') }}" class="tp-link {{ Request::routeIs('institution.*') ? 'active' : '' }}">รายการสถานศึกษา</a></li>
                                <li><a href="{{ route('subject.show') }}" class="tp-link {{ Request::routeIs('subject.*') ? 'active' : '' }}">รายการวิชาเรียน</a></li>
                                <li><a href="{{ route('house.show') }}"class="tp-link {{ Request::routeIs('house.*') ? 'active' : '' }}">รายการบ้านพัก</a></li>
                                <li><a href="{{ route('education.show') }}" class="tp-link {{ Request::routeIs('education.*') ? 'active' : '' }}">รายการระดับการศึกษา</a></li>
                                <li><a href="{{ route('semester.show') }}" class="tp-link {{ Request::routeIs('semester.*') ? 'active' : '' }}">รายการปีการศึกษา</a></li>
                                <li><a href="{{ route('psycho.show') }}" class="tp-link {{ Request::routeIs('psycho.*') ? 'active' : '' }}">รายการโรคทางจิตเวช</a></li>
                                <li><a href="{{ route('misbehavior.show') }}" class="tp-link {{ Request::routeIs('misbehavior.*') ? 'active' : '' }}">รายการพฤติกรรม</a></li>
                                <li><a href="{{ route('outside.show') }}" class="tp-link {{ Request::routeIs('outside.*') ? 'active' : '' }}">รายการเด็กที่อยู่ภายนอก</a></li>
                                <li><a href="{{ route('document.show') }}" class="tp-link {{ Request::routeIs('document.*') ? 'active' : '' }}">รายการเอกสาร</a></li>
                                <li><a href="{{ route('income.show') }}" class="tp-link {{ Request::routeIs('income.*') ? 'active' : '' }}">รายการรายได้</a></li>
                                <li><a href="{{ route('help_type.show') }}" class="tp-link {{ Request::routeIs('help_type.*') ? 'active' : '' }}">ประเภทการช่วยเหลือ</a></li>
                                <li><a href="{{ route('citizenship.show') }}" class="tp-link {{ Request::routeIs('citizenship.*') ? 'active' : '' }}">รายการทางทะเบียน</a></li>
                                <li><a href="{{ route('citizen.show') }}" class="tp-link {{ Request::routeIs('citizen.*') ? 'active' : '' }}">ได้รับสถานะทางทะเบียน</a></li>
                                <li><a href="{{ route('translate.show') }}" class="tp-link {{ Request::routeIs('translate.*') ? 'active' : '' }}">ประเภทการพ้นอุปการะ</a></li>
                            </ul>
                        </div>
                    </li>
                @endif

                {{-- =========================
                    ประชาสัมพันธ์
                ========================== --}}
                <li class="menu-title mt-2">ประชาสัมพันธ์</li>
                <li>
                    <a href="{{ route('publicizes.index') }}"
                       class="tp-link {{ Request::routeIs('publicizes.index') ? 'active' : '' }}">
                        <i class="bi bi-megaphone me-1"></i>
                        ข่าวสาร/กิจกรรม
                    </a>
                </li>

                {{-- =========================
                    บันทึก/รายงานการปฏิบัติงาน
                ========================== --}}
                <li class="nav-item">
                    <a href="{{ route('operations.index') }}"
                       class="nav-link {{ request()->routeIs('operations.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text me-2"></i>
                        <span>บันทึกการปฏิบัติงาน</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('operations.report.daily') }}"
                       class="nav-link {{ request()->routeIs('operations.report.daily') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        <span>รายงานการปฏิบัติงาน</span>
                    </a>
                </li>

                @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'executive']))
    <li class="nav-item">
        <a href="{{ route('refers.all') }}"
           class="nav-link {{ request()->routeIs('refers.all') ? 'active' : '' }}">
            <i class="bi bi-box-arrow-right me-2"></i>
            <span>รายงานการจำหน่าย</span>
        </a>
    </li>
@endif

                {{-- =========================
                    จัดการผู้ใช้งาน
                ========================== --}}
                @if(auth()->check() && auth()->user()->role === 'admin')
                    <li class="menu-title mt-2">
                        <span class="menu-title-with-icon">
                            <i class="bi bi-person-gear"></i>
                            การจัดการระบบ
                        </span>
                    </li>

                    <li>
                        <a href="#sidebarUsers"
                           data-bs-toggle="collapse"
                           aria-expanded="{{ $isUserMenu ? 'true' : 'false' }}"
                           class="{{ $isUserMenu ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i>
                            <span class="menu-text">จัดการผู้ใช้งาน</span>
                            <span class="sidebar-badge-soft">Admin</span>
                            <span class="menu-arrow menu-arrow-custom"></span>
                        </a>

                        <div class="collapse {{ $isUserMenu ? 'show' : '' }}" id="sidebarUsers">
                            <ul class="nav-second-level">
                                <li>
                                    <a href="{{ route('users.index') }}"
                                       class="tp-link with-icon {{ Request::routeIs('users.index') || Request::routeIs('users.edit') ? 'active' : '' }}">
                                        <span class="sidebar-user-link-icon">
                                            <i class="bi bi-list-ul"></i>
                                        </span>
                                        รายชื่อผู้ใช้งาน
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('users.create') }}"
                                       class="tp-link with-icon {{ Request::routeIs('users.create') ? 'active' : '' }}">
                                        <span class="sidebar-user-link-icon">
                                            <i class="bi bi-person-plus-fill"></i>
                                        </span>
                                        เพิ่มผู้ใช้งาน
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                {{-- =========================
                    ระบบ
                ========================== --}}
                <li class="menu-title mt-2">ระบบ</li>
                <li>
                    <a href="{{ route('admin.logout') }}">
                        <i data-feather="log-out"></i>
                        <span>ออกจากระบบ</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('stableMasterSidebar');
        if (!sidebar) return;

        function renderFeather() {
            if (window.feather) {
                try {
                    feather.replace();
                } catch (e) {}
            }
            sidebar.classList.add('sidebar-icons-ready');
        }

        renderFeather();
        setTimeout(renderFeather, 100);
    });
</script>