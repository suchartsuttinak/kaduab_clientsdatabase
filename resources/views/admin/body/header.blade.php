<div class="topbar-custom">
    <div class="container-xxl">
        <div class="d-flex justify-content-between">
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <li>
                    <button class="button-toggle-menu nav-link ps-0">
                        <i data-feather="menu" class="noti-icon"></i>
                    </button>
                </li>
            </ul>

            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">

                <li class="d-none d-sm-flex">
                    <button type="button" class="btn nav-link" data-toggle="fullscreen">
                        <i data-feather="maximize" class="align-middle fullscreen noti-icon"></i>
                    </button>
                </li>

                @php
                    $pendingRefers = $pendingRefers ?? 0;
                    $todayAbsents = $todayAbsents ?? 0;
                    $todayIllnesses = $todayIllnesses ?? 0;
                    $upcomingAppointments = $upcomingAppointments ?? 0;

                    $notificationCount =
                        $pendingRefers
                        + $todayAbsents
                        + $todayIllnesses
                        + $upcomingAppointments;

                    $id = Auth::user()->id;
                    $profileData = App\Models\User::find($id);
                @endphp

                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle position-relative"
                       data-bs-toggle="dropdown"
                       href="#"
                       role="button">
                        <i data-feather="bell" class="noti-icon"></i>

                        @if($notificationCount > 0)
                            <span class="badge bg-danger rounded-circle noti-icon-badge">
                                {{ $notificationCount }}
                            </span>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-menu-end dropdown-lg shadow border-0 notification-panel">
                        <div class="dropdown-item noti-title border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0 fw-bold notification-title">
                                        <i class="mdi mdi-bell-ring-outline me-1"></i>
                                        ศูนย์แจ้งเตือนผู้รับบริการ
                                    </h5>
                                    <small class="text-muted">รายการติดตามสำคัญ</small>
                                </div>

                                @if($notificationCount > 0)
                                    <span class="badge bg-danger">
                                        {{ $notificationCount }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="noti-scroll" data-simplebar style="max-height:420px;">
                            @if($notificationCount > 0)

                                @foreach(($upcomingAppointmentItems ?? collect()) as $item)
                                    @php
                                        $client = $item->client ?? null;
                                        $clientName = $client->fullname ?? trim(($client->first_name ?? '').' '.($client->last_name ?? ''));

                                        $appointDate = $item->appointment_date ?? $item->appoin_date ?? null;
                                        $appointDateText = $appointDate
                                            ? \Carbon\Carbon::parse($appointDate)->addYears(543)->format('d/m/Y')
                                            : '-';
                                    @endphp

                                    <a href="{{ $client ? route('admin.index', $client->id) : route('client.show') }}"
                                       class="dropdown-item notify-item">
                                        <div class="notify-icon bg-warning-subtle text-warning">
                                            <i class="mdi mdi-calendar-clock"></i>
                                        </div>

                                        <div class="notify-content">
                                            <div class="fw-semibold">
                                                นัดพบแพทย์: {{ $clientName ?: '-' }}
                                            </div>

                                            <small class="text-muted d-block">
                                                เลขทะเบียน {{ $client->register_number ?? '-' }}
                                            </small>

                                            <small class="text-danger d-block fw-semibold">
                                                นัดวันที่ {{ $appointDateText }}
                                            </small>
                                        </div>
                                    </a>
                                @endforeach

                                @foreach(($pendingReferItems ?? collect()) as $item)
                                    @php
                                        $client = $item->client ?? null;
                                        $clientName = $client->fullname ?? trim(($client->first_name ?? '').' '.($client->last_name ?? ''));
                                    @endphp

                                    <a href="{{ $client ? route('refers.index', $client->id) : route('client.show') }}"
                                       class="dropdown-item notify-item">
                                        <div class="notify-icon bg-primary-subtle text-primary">
                                            <i class="mdi mdi-file-clock-outline"></i>
                                        </div>

                                        <div class="notify-content">
                                            <div class="fw-semibold">
                                                จำหน่ายรออนุมัติ: {{ $clientName ?: '-' }}
                                            </div>

                                            <small class="text-muted d-block">
                                                เลขทะเบียน {{ $client->register_number ?? '-' }}
                                            </small>
                                        </div>
                                    </a>
                                @endforeach

                                @foreach(($todayAbsentItems ?? collect()) as $item)
                                    @php
                                        $client = $item->client ?? null;
                                        $clientName = $client->fullname ?? trim(($client->first_name ?? '').' '.($client->last_name ?? ''));
                                    @endphp

                                    <a href="{{ $client ? route('admin.index', $client->id) : route('client.show') }}"
                                       class="dropdown-item notify-item">
                                        <div class="notify-icon bg-info-subtle text-info">
                                            <i class="mdi mdi-school-outline"></i>
                                        </div>

                                        <div class="notify-content">
                                            <div class="fw-semibold">
                                                ขาดเรียนวันนี้: {{ $clientName ?: '-' }}
                                            </div>

                                            <small class="text-muted d-block">
                                                เลขทะเบียน {{ $client->register_number ?? '-' }}
                                            </small>
                                        </div>
                                    </a>
                                @endforeach

                                @foreach(($todayIllnessItems ?? collect()) as $item)
                                    @php
                                        $client = $item->client ?? null;
                                        $clientName = $client->fullname ?? trim(($client->first_name ?? '').' '.($client->last_name ?? ''));
                                    @endphp

                                    <a href="{{ $client ? route('admin.index', $client->id) : route('client.show') }}"
                                       class="dropdown-item notify-item">
                                        <div class="notify-icon bg-success-subtle text-success">
                                            <i class="mdi mdi-medical-bag"></i>
                                        </div>

                                        <div class="notify-content">
                                            <div class="fw-semibold">
                                                เจ็บป่วยวันนี้: {{ $clientName ?: '-' }}
                                            </div>

                                            <small class="text-muted d-block">
                                                เลขทะเบียน {{ $client->register_number ?? '-' }}
                                            </small>
                                        </div>
                                    </a>
                                @endforeach

                            @else
                                <div class="dropdown-item text-center py-4">
                                    <i class="mdi mdi-bell-outline fs-26 text-muted d-block mb-2"></i>
                                    <div class="fw-semibold text-muted">ไม่มีการแจ้งเตือน</div>
                                    <small class="text-muted">ระบบติดตามเป็นปกติ</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </li>

                {{-- DASHBOARD_USER_MENU_LOGOUT_HOTFIX_V1: account navigation must remain usable in read-only pages --}}
                <li class="dropdown notification-list topbar-dropdown"
                    data-account-navigation="1"
                    data-permission-action="navigation">
                    <a class="nav-link dropdown-toggle nav-user me-0"
                       data-bs-toggle="dropdown"
                       href="#"
                       role="button"
                       aria-haspopup="false"
                       aria-expanded="false">
                        <img src="{{ (!empty($profileData->photo)) ? url('upload/user_images/'.$profileData->photo) : url('upload/no_image.jpg') }}"
                             alt="user-image"
                             class="rounded-circle">

                        <span class="pro-user-name ms-1">
                            {{ $profileData->name }}
                        </span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">บัญชีผู่้ใช้งาน</h6>
                        </div>

                        <a href="{{ route('admin.profile') }}" class="dropdown-item notify-item">
                            <i class="mdi mdi-account-circle-outline fs-16 align-middle"></i>
                            <span>ข้อมูลส่วนตัว</span>
                        </a>

                        <a href="{{ route('admin.profile') }}#change-password"
                           class="dropdown-item notify-item"
                           data-permission-action="navigation">
                            <i class="mdi mdi-lock-reset fs-16 align-middle"></i>
                            <span>เปลี่ยนรหัสผ่าน</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <form method="POST"
{{-- PERMISSION_LANDING_LOGOUT_FIX_V3: ใช้ logout route มาตรฐานเพียงจุดเดียว --}}
                              action="{{ route('logout') }}"
                              class="m-0"
                              data-permission-action="navigation">
                            @csrf
                            <button type="submit" class="dropdown-item notify-item border-0 bg-transparent w-100 text-start">
                                <i class="mdi mdi-location-exit fs-16 align-middle"></i>
                                <span>ออกจากระบบ</span>
                            </button>
                        </form>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</div>

<style>
.notification-panel{
    border-radius:16px;
    min-width:390px;
    overflow:hidden;
}

.notification-title{
    font-size:15px;
}

.notify-item{
    padding:14px 16px;
    display:flex;
    gap:14px;
    align-items:flex-start;
    transition:.2s;
    border-bottom:1px solid #f1f5f9;
}

.notify-item:hover{
    background:#f8fbff;
}

.notify-icon{
    width:42px;
    height:42px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
    flex-shrink:0;
}

.notify-content{
    line-height:1.35;
    min-width:0;
}

.noti-icon-badge{
    font-size:10px;
    min-width:18px;
    height:18px;
    line-height:18px;
    top:8px;
    right:4px;
    position:absolute;
    padding:0 5px;
    box-shadow:0 0 0 2px #fff;
}

@media (max-width:575.98px){
    .notification-panel{
        min-width:300px;
        max-width:92vw;
    }
}
</style>