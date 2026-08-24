@extends('admin_client.admin_client')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

@php
    use Carbon\Carbon;

    if (!$client) {
        abort(404);
    }

    // CLIENT_IMAGE_VERSION_V8: Browser cache ได้ตามปกติ แต่ชื่อไฟล์เปลี่ยน = URL เปลี่ยนทันที
    $profileImage = !empty($client->image)
        ? (route('client.image', $client->id) . '?v=' . substr(sha1((string) $client->image), 0, 12))
        : asset('upload/no_image.jpg');

    $birthDate = !empty($client->birth_date) ? Carbon::parse($client->birth_date) : null;
    $arrivalDate = !empty($client->arrival_date) ? Carbon::parse($client->arrival_date) : null;

    // Controller เลือกข้อมูลภาคเรียนที่มีค่ามากที่สุดมาให้แล้ว
    // เรียงปีการศึกษาและเลขภาคเรียน ไม่ใช้วันที่บันทึก
    $educationRecord = $currentEducationRecord ?? null;

    $educationName = data_get($educationRecord, 'education.education_name')
        ?: 'ยังไม่มีข้อมูลการศึกษา';

    $institutionName = data_get($educationRecord, 'institution.institution_name')
        ?: ($educationRecord->school_name ?? null)
        ?: 'ยังไม่มีข้อมูลการศึกษา';

    $educationPeriodText = $educationRecord && $currentSemester && $currentAcademicYear
        ? 'ภาคเรียนที่ ' . $currentSemester . ' ปีการศึกษา ' . $currentAcademicYear
        : 'ยังไม่มีข้อมูลภาคเรียน';

    $appointments = collect($appointments ?? []);
    $appointmentCount = $appointmentCount ?? $appointments->count();

    $accidents = collect($accidents ?? []);
    $accidentCount = $accidentCount ?? $accidents->count();

    $observeLatestCount = !empty($observeLatest) ? 1 : 0;
    $observeDateText = !empty($observeDate)
        ? Carbon::parse($observeDate)->locale('th')->translatedFormat('d F') . ' ' . (Carbon::parse($observeDate)->year + 543)
        : 'ไม่ระบุ';

    $todayText = ($day ?? '-') . ' ' . ($month ?? '-') . ' ' . ($year ?? '-');

    $clientFullName = $client->full_name ?? $client->fullname ?? trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
@endphp

<div class="container-fluid client-page">

    <div class="client-topbar d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div class="topbar-left">
            <a href="{{ route('client.show') }}" class="btn btn-primary btn-back shadow-sm">
                <span class="btn-back-icon">
                    <i class="bi bi-arrow-left-short"></i>
                </span>
                <span>กลับหน้าหลัก</span>
            </a>
        </div>

        <div class="topbar-right text-md-end">
            <div class="page-badge">
                <i class="bi bi-person-vcard me-2"></i>ข้อมูลผู้รับบริการ
            </div>
        </div>
    </div>

    <div class="page-header-card mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="section-kicker">Client Profile</div>
                <h1 class="page-title mb-2">ข้อมูลผู้รับบริการ</h1>
                <p class="page-subtitle mb-0">
                    แสดงรายละเอียดเฉพาะราย ข้อมูลพื้นฐาน และเมนูบริการที่เกี่ยวข้องอย่างเป็นระบบ
                </p>
            </div>
           
        </div>
    </div>

    <div class="card profile-card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-xl-4 profile-left-panel text-center">
                    <div class="profile-avatar-wrap">
                        <img src="{{ $profileImage }}"
                             alt="โปรไฟล์ผู้รับบริการ"
                             class="profile-avatar">
                    </div>

                    <h3 class="profile-name mb-2">{{ $clientFullName ?: 'ไม่ระบุชื่อ' }}</h3>

                    <div class="profile-meta">
                        @if($birthDate)
                            อายุ {{ $birthDate->age }} ปี
                        @else
                            ไม่ระบุอายุ
                        @endif
                    </div>

                    <div class="profile-quick-tags mt-3">
                        <span class="quick-tag">
                            <i class="bi bi-person-badge me-1"></i>
                            เลขทะเบียน: {{ $client->register_number ?? '-' }}
                        </span>

                        <span class="quick-tag">
                            <i class="bi bi-calendar-check me-1"></i>ระยะเวลาอยู่อาศัย:
                            {{ $client->residence_duration ?? 'ไม่มีข้อมูลวันที่เข้าระบบ' }}
                        </span>
                    </div>

                    {{-- HEALTHCARE_RIGHTS_PROFILE_SUMMARY_V1_VIEW --}}
                    @if($canViewHealthcareRightSummary ?? false)
                        <div class="healthcare-right-summary mt-3">
                            <i class="bi bi-heart-pulse-fill" aria-hidden="true"></i>
                            @if($latestHealthcareRight)
                                <span>
                                    <strong>สิทธิรักษาพยาบาล</strong>
                                    ({{ $latestHealthcareRight->coverage_status }})
                                    @if(!empty($latestHealthcareRight->primary_hospital))
                                        {{ $latestHealthcareRight->primary_hospital }}
                                    @endif
                                </span>
                            @else
                                <span><strong>สิทธิรักษาพยาบาล</strong> ยังไม่มีข้อมูล</span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="col-xl-8">
                    <div class="profile-right-panel p-4 p-lg-5">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 detail-head">
                            <h5 class="detail-title mb-0">รายละเอียดข้อมูลพื้นฐาน</h5>
                            <span class="detail-status">
                                <i class="bi bi-check-circle-fill me-1"></i> อัปเดตข้อมูลแล้ว
                            </span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">ชื่อ - สกุล</div>
                                    <div class="info-value">{{ $clientFullName ?: 'ไม่ระบุ' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">วันเกิด</div>
                                    <div class="info-value">
                                        @if($birthDate)
                                            {{ $birthDate->locale('th')->translatedFormat('d F') }} {{ $birthDate->year + 543 }}
                                        @else
                                            ไม่ระบุ
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">อายุ</div>
                                    <div class="info-value">
                                        @if($birthDate)
                                            {{ $birthDate->age }} ปี
                                        @else
                                            ไม่ระบุ
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">วันที่รับเข้า</div>
                                    <div class="info-value">
                                        @if($arrivalDate)
                                            {{ $arrivalDate->locale('th')->translatedFormat('d F') }} {{ $arrivalDate->year + 543 }}
                                        @else
                                            ไม่ระบุ
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">ระดับการศึกษา</div>
                                    <div class="info-value">{{ $educationName }}</div>
                                    <div class="education-period {{ $educationRecord ? '' : 'education-period-missing' }}">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $educationPeriodText }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">สถานศึกษา</div>
                                    <div class="info-value">{{ $institutionName }}</div>
                                    <div class="education-period {{ $educationRecord ? '' : 'education-period-missing' }}">
                                        @if($educationRecord)
                                            ข้อมูลตามภาคเรียนล่าสุด
                                        @else
                                            ยังไม่มีข้อมูลประวัติการศึกษา
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="profile-note mt-4">
                            <i class="bi bi-info-circle me-2"></i>
                            ข้อมูลส่วนนี้ใช้สำหรับสรุปภาพรวมผู้รับบริการก่อนเข้าสู่เมนูรายละเอียดเชิงลึก
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section-head mb-3">
        <div>
            <h4 class="section-title mb-1">สรุปข้อมูลล่าสุด</h4>
            <p class="section-subtitle mb-0">ภาพรวมกิจกรรมสำคัญของผู้รับบริการในระบบ</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-4">
            <div class="card stat-card stat-card-success border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="stat-icon">
                        <i class="bi bi-calendar2-check"></i>
                    </div>

                    <div class="stat-label">การพบแพทย์</div>
                    <div class="stat-number">{{ $appointmentCount }}</div>
                    <div class="stat-desc">จำนวนรายการนัดหมายที่เกี่ยวข้อง</div>

                    <div class="stat-list mt-4">
                        @if($appointmentCount > 0)
                            <ul class="list-unstyled mb-0">
                                @foreach($appointments as $record)
                                    <li>
                                        <i class="bi bi-dot"></i>
                                        {{ is_array($record) ? ($record['type'] ?? 'นัดหมาย') : ($record->type ?? 'นัดหมาย') }}
                                        วันที่
                                        @php
                                            $recordDate = is_array($record) ? ($record['date'] ?? null) : ($record->date ?? null);
                                        @endphp
                                        @if($recordDate)
                                            {{ Carbon::parse($recordDate)->locale('th')->translatedFormat('d F') }}
                                            {{ Carbon::parse($recordDate)->year + 543 }}
                                        @else
                                            ไม่ระบุ
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="empty-text">ไม่มีนัดหมาย</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card stat-card stat-card-warning border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="stat-icon">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>

                    <div class="stat-label">พฤติกรรม</div>
                    <div class="stat-number">{{ $observeLatestCount }}</div>
                    <div class="stat-desc">บันทึกพฤติกรรมล่าสุดในระบบ</div>

                    <div class="stat-list mt-4">
                        <div class="single-line-info">
                            <strong>บันทึกล่าสุด:</strong> {{ $observeDateText }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card stat-card stat-card-danger border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="stat-icon">
                        <i class="bi bi-heart-pulse"></i>
                    </div>

                    <div class="stat-label">การบาดเจ็บ</div>
                    <div class="stat-number">{{ $accidentCount }}</div>
                    <div class="stat-desc">ข้อมูลการบาดเจ็บที่บันทึกในระบบ</div>

                    <div class="stat-list mt-4">
                        <div class="single-line-info mb-2">
                            <strong>อ้างอิงวันที่:</strong> {{ $todayText }}
                        </div>

                        @if($accidentCount > 0)
                            <ul class="list-unstyled mb-0">
                                @foreach($accidents as $accident)
                                    @php
                                        $incidentDate = is_array($accident) ? ($accident['incident_date'] ?? null) : ($accident->incident_date ?? null);
                                    @endphp
                                    <li>
                                        <i class="bi bi-dot"></i>
                                        บันทึกการบาดเจ็บวันที่
                                        @if($incidentDate)
                                            {{ Carbon::parse($incidentDate)->locale('th')->translatedFormat('d F') }}
                                            {{ Carbon::parse($incidentDate)->year + 543 }}
                                        @else
                                            ไม่ระบุ
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="empty-text">ไม่มีข้อมูลการบาดเจ็บ</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section-head mb-3">
        <div>
            <h4 class="section-title mb-1">เลือกบริการ</h4>
            <p class="section-subtitle mb-0">เมนูหลักสำหรับเข้าถึงบริการและข้อมูลที่เกี่ยวข้องกับผู้รับบริการ</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4 col-md-6">
            <div class="card service-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="service-icon bg-primary-subtle text-primary">
                        <i class="bi bi-grid-1x2-fill"></i>
                    </div>
                    <h5 class="service-title">ข้อมูลภาพรวม</h5>
                    <p class="service-text">ดูรายละเอียดเบื้องต้นของผู้รับบริการ ประวัติ และข้อมูลสรุปในระบบ</p>
                   <a href="{{ route('admin.client.overview', $client->id) }}" class="service-link">
                เข้าสู่เมนู <i class="bi bi-arrow-right-short"></i>
            </a>
                            </div>
                        </div>
                    </div>
                    

<div class="col-xl-4 col-md-6">
    <div class="card service-card border-0 shadow-sm h-100">
        <div class="card-body p-4">
            <div class="service-icon bg-warning-subtle text-warning">
                <i class="bi bi-clipboard2-pulse"></i>
            </div>

            <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap mb-2">
                <h5 class="service-title mb-0">ข้อมูลเบื้องต้นที่ควรบันทึก</h5>

                @if(($serviceLogCount ?? 0) > 0)
                    <span class="service-mini-badge">
                        ต้องตรวจสอบ
                    </span>
                @else
                    <span class="service-mini-badge">
                        ครบถ้วน
                    </span>
                @endif
            </div>

            <p class="service-text mb-2">
                ตรวจสอบข้อมูลสำคัญที่ยังไม่มีการบันทึก เช่น สอบข้อเท็จจริง ครอบครัว สมาชิก เอกสาร ตรวจร่างกาย การศึกษา และติดตามผล
            </p>

            <div class="service-meta-list mb-3">
                <div class="service-meta-item">
                    <span class="service-meta-label">จำนวนที่ยังขาด</span>
                    <span class="service-meta-value">{{ $serviceLogCount ?? 0 }}</span>
                </div>

                <div class="service-meta-item">
                    <span class="service-meta-label">รายการแรกที่ยังขาด</span>
                    <span class="service-meta-value">{{ $serviceLogLatestType ?? 'ข้อมูลเบื้องต้นครบถ้วน' }}</span>
                </div>

                <div class="service-meta-item">
                    <span class="service-meta-label">สถานะ</span>
                    <span class="service-meta-value">
                        @if(($serviceLogCount ?? 0) > 0)
                            ยังมีข้อมูลที่ควรบันทึก
                        @else
                            ข้อมูลเบื้องต้นครบถ้วน
                        @endif
                    </span>
                </div>
            </div>

            <a href="{{ route('admin.client.service_logs', $client->id) }}" class="service-link">
                เข้าสู่เมนู <i class="bi bi-arrow-right-short"></i>
            </a>
        </div>
    </div>
</div>

      <div class="col-xl-4 col-md-6">
    <div class="card service-card border-0 shadow-sm h-100">
        <div class="card-body p-4">
            <div class="service-icon bg-danger-subtle text-danger">
                <i class="bi bi-heart-pulse-fill"></i>
            </div>

            <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap mb-2">
                <h5 class="service-title mb-0">สุขภาพและการรักษา</h5>

                @if(!empty($healthLatestTreatmentDate))
                    <span class="service-mini-badge service-mini-badge-danger">
                        อัปเดตล่าสุด
                    </span>
                @endif
            </div>

            <p class="service-text mb-2">
                ตรวจสอบข้อมูลด้านสุขภาพ การนัดหมาย และประวัติการรักษาที่เกี่ยวข้อง
            </p>

            <div class="service-meta-list mb-3">
                <div class="service-meta-item">
                    <span class="service-meta-label">นัดหมายถัดไป</span>
                    <span class="service-meta-value">
                        @if(!empty($healthNextAppointment['date']))
                            {{ $healthNextAppointment['type'] ?? 'นัดหมาย' }}
                        @else
                            ไม่มีนัด
                        @endif
                    </span>
                </div>

                <div class="service-meta-item">
                    <span class="service-meta-label">จำนวนรอนัด</span>
                    <span class="service-meta-value">{{ $healthAppointmentCount ?? 0 }}</span>
                </div>

                <div class="service-meta-item">
                    <span class="service-meta-label">วันที่รักษาล่าสุด</span>
                    <span class="service-meta-value">
                        @if(!empty($healthLatestTreatmentDate))
                            {{ \Carbon\Carbon::parse($healthLatestTreatmentDate)->locale('th')->translatedFormat('d F') }}
                            {{ \Carbon\Carbon::parse($healthLatestTreatmentDate)->year + 543 }}
                        @else
                            ไม่มีข้อมูล
                        @endif
                    </span>
                </div>
            </div>

            <a href="{{ route('admin.client.health', $client->id) }}" class="service-link">
                ดูทั้งหมด <i class="bi bi-arrow-right-short"></i>
            </a>
        </div>
    </div>
</div>

    <div class="col-xl-4 col-md-6">
    <a href="{{ route('publicizes.index') }}" class="service-card-link">

        <div class="card service-card border-0 shadow-sm h-100">
            <div class="card-body p-4">

                <div class="service-icon bg-warning-subtle text-warning">
                    <i class="bi bi-megaphone-fill"></i>
                </div>

                <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap mb-2">
                    <h5 class="service-title mb-0">ข่าวสารและกิจกรรม</h5>

                    @if(!empty($publicizeLatestDate))
                        <span class="service-mini-badge">
                            ล่าสุด
                        </span>
                    @endif
                </div>

                <p class="service-text mb-2">
                    แสดงข้อมูลข่าวสาร เอกสาร และกิจกรรมประชาสัมพันธ์ล่าสุดของระบบ
                </p>

                <div class="service-meta-list mb-3">

                    <div class="service-meta-item">
                        <span class="service-meta-label">จำนวนทั้งหมด</span>
                        <span class="service-meta-value">
                            {{ $publicizeCount ?? 0 }}
                        </span>
                    </div>

                    <div class="service-meta-item">
                        <span class="service-meta-label">หมวดล่าสุด</span>
                        <span class="service-meta-value">
                            {{ $publicizeLatestCategory ?? '-' }}
                        </span>
                    </div>

                    <div class="service-meta-item">
                        <span class="service-meta-label">วันที่ล่าสุด</span>
                        <span class="service-meta-value">
                            @if(!empty($publicizeLatestDate))
                                {{ \Carbon\Carbon::parse($publicizeLatestDate)->locale('th')->translatedFormat('d F') }}
                                {{ \Carbon\Carbon::parse($publicizeLatestDate)->year + 543 }}
                            @else
                                -
                            @endif
                        </span>
                    </div>

                </div>

                <div class="service-link">
                    เปิดดูข้อมูล <i class="bi bi-arrow-right-short"></i>
                </div>

            </div>
        </div>

    </a>
</div>

       <div class="col-xl-4 col-md-6">
    <a href="{{ route('client_files.index', $client->id) }}" class="service-card-link">

        <div class="card service-card border-0 shadow-sm h-100">
            <div class="card-body p-4">

                <div class="service-icon bg-info-subtle text-info">
                    <i class="bi bi-folder-fill"></i>
                </div>

                <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap mb-2">
                    <h5 class="service-title mb-0">เอกสารและไฟล์</h5>

                    @if(!empty($fileLatestDate))
                        <span class="service-mini-badge">
                            ล่าสุด
                        </span>
                    @endif
                </div>

                <p class="service-text mb-2">
                    จัดเก็บเอกสารสำคัญ เช่น บัตรประชาชน ทะเบียนบ้าน และเอกสารอื่น ๆ ของผู้รับบริการ
                </p>

                <div class="service-meta-list mb-3">

                    <div class="service-meta-item">
                        <span class="service-meta-label">จำนวนไฟล์</span>
                        <span class="service-meta-value">
                            {{ $filesCount ?? 0 }}
                        </span>
                    </div>

                    <div class="service-meta-item">
                        <span class="service-meta-label">ประเภทล่าสุด</span>
                        <span class="service-meta-value">
                            {{ $fileLatestType }}
                        </span>
                    </div>

                    <div class="service-meta-item">
                        <span class="service-meta-label">อัปโหลดล่าสุด</span>
                        <span class="service-meta-value">
                            @if(!empty($fileLatestDate))
                                {{ \Carbon\Carbon::parse($fileLatestDate)->locale('th')->translatedFormat('d F') }}
                                {{ \Carbon\Carbon::parse($fileLatestDate)->year + 543 }}
                            @else
                                -
                            @endif
                        </span>
                    </div>

                </div>

                <div class="service-link">
                    เปิดดูไฟล์ <i class="bi bi-arrow-right-short"></i>
                </div>

            </div>
        </div>

    </a>
</div>

       <div class="col-xl-4 col-md-6">
    <a href="{{ route('case-activities.index', $client->id) }}"
       class="service-card-link">

        <div class="card service-card border-0 shadow-sm h-100">
            <div class="card-body p-4">

                <div class="service-icon bg-secondary-subtle text-secondary">
                    <i class="bi bi-clock-history"></i>
                </div>

                <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap mb-2">

                    <h5 class="service-title mb-0">
                        ความเคลื่อนไหวผู้รับบริการ
                    </h5>

                    @if(($activitiesCount ?? 0) > 0)
                        <span class="service-mini-badge">
                            ล่าสุด
                        </span>
                    @endif

                </div>

                <p class="service-text mb-2">
                    ติดตามกิจกรรม การบันทึกข้อมูล และความเคลื่อนไหวต่าง ๆ ของผู้รับบริการแบบ Timeline
                </p>

                <div class="service-meta-list mb-3">

                    <div class="service-meta-item">
                        <span class="service-meta-label">
                            จำนวนกิจกรรม
                        </span>

                        <span class="service-meta-value">
                            {{ $activitiesCount ?? 0 }}
                        </span>
                    </div>

                    <div class="service-meta-item">
                        <span class="service-meta-label">
                            ประเภทล่าสุด
                        </span>

                        <span class="service-meta-value">
                            {{ $latestActivityType ?? '-' }}
                        </span>
                    </div>

                    <div class="service-meta-item">
                        <span class="service-meta-label">
                            วันที่ล่าสุด
                        </span>

                        <span class="service-meta-value">
                            @if(!empty($latestActivityDate))
                                {{ \Carbon\Carbon::parse($latestActivityDate)->locale('th')->translatedFormat('d F') }}
                                {{ \Carbon\Carbon::parse($latestActivityDate)->year + 543 }}
                            @else
                                -
                            @endif
                        </span>
                    </div>

                </div>

                <div class="service-link">
                    เปิดดูข้อมูล
                    <i class="bi bi-arrow-right-short"></i>
                </div>

            </div>
        </div>

    </a>
</div>
    </div>
</div>

<style>
    .client-page {
        background: linear-gradient(180deg, #f8fbff 0%, #f4f7fb 100%);
        min-height: 100vh;
        padding: 1.25rem 1.25rem 2rem;
        overflow-x: hidden;
        position: relative;
        z-index: 1;
    }

    .client-topbar {
        position: relative;
        z-index: 3;
    }

    .topbar-left,
    .topbar-right {
        min-width: 0;
    }

    .btn-back {
        border-radius: 999px;
        padding: 0.65rem 1.15rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .55rem;
        max-width: 100%;
        white-space: normal;
        text-align: center;
        line-height: 1.3;
    }

    .btn-back-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(255,255,255,.18);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex: 0 0 auto;
    }

    .page-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: .55rem 1rem;
        border-radius: 999px;
        background: #ffffff;
        border: 1px solid rgba(13,110,253,.12);
        color: #0d6efd;
        font-weight: 600;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        max-width: 100%;
        text-align: center;
        line-height: 1.35;
        white-space: normal;
    }

    .page-header-card {
        background: linear-gradient(135deg, #ffffff 0%, #f7faff 100%);
        border: 1px solid rgba(13,110,253,.08);
        border-radius: 24px;
        padding: 2rem;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .section-kicker {
        display: inline-block;
        font-size: .8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #0d6efd;
        background: rgba(13,110,253,.08);
        padding: .45rem .8rem;
        border-radius: 999px;
        margin-bottom: 1rem;
    }

    .page-title {
        font-size: clamp(1.7rem, 2vw, 2.4rem);
        font-weight: 800;
        color: #1e293b;
        line-height: 1.25;
        word-break: break-word;
    }

    .page-subtitle {
        color: #64748b;
        font-size: 1rem;
        line-height: 1.75;
        max-width: 700px;
    }

    .header-mini-stat {
        background: #fff;
        border-radius: 18px;
        padding: 1rem 1.25rem;
        border: 1px solid rgba(15, 23, 42, .06);
        display: inline-block;
        min-width: 220px;
        max-width: 100%;
    }

    .mini-stat-label {
        color: #64748b;
        font-size: .85rem;
        margin-bottom: .3rem;
    }

    .mini-stat-value {
        color: #0f172a;
        font-weight: 700;
        word-break: break-word;
    }

    .profile-card {
        border-radius: 26px;
    }

    .profile-left-panel {
        background: linear-gradient(180deg, #0d6efd 0%, #0b5ed7 100%);
        color: #fff;
        padding: 2.5rem 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .profile-right-panel {
        height: 100%;
    }

    .profile-avatar-wrap {
        margin-bottom: 1.25rem;
    }

    .profile-avatar {
        width: 160px;
        height: 160px;
        object-fit: cover;
        border-radius: 50%;
        border: 5px solid rgba(255,255,255,.25);
        box-shadow: 0 20px 40px rgba(0,0,0,.18);
        max-width: 100%;
    }

    .profile-name {
        font-weight: 800;
        font-size: 1.5rem;
        line-height: 1.35;
        word-break: break-word;
    }

    .profile-meta {
        color: rgba(255,255,255,.88);
        font-size: .98rem;
    }

    .profile-quick-tags {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: .6rem;
    }

    .quick-tag {
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.18);
        color: #fff;
        border-radius: 999px;
        padding: .5rem .85rem;
        font-size: .85rem;
        max-width: 100%;
        white-space: normal;
        word-break: break-word;
        text-align: center;
    }

    /* HEALTHCARE_RIGHTS_PROFILE_SUMMARY_V1_CSS */
    .healthcare-right-summary {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        gap: .45rem;
        width: 100%;
        padding: .7rem .85rem 0;
        border-top: 1px solid rgba(255,255,255,.18);
        color: rgba(255,255,255,.96);
        font-size: .9rem;
        line-height: 1.55;
        text-align: center;
        white-space: normal;
        word-break: break-word;
    }

    .healthcare-right-summary i {
        flex: 0 0 auto;
        margin-top: .15rem;
        font-size: 1rem;
    }

    .healthcare-right-summary strong {
        font-weight: 800;
    }

    .detail-title {
        font-weight: 800;
        color: #0f172a;
    }

    .detail-status {
        background: #ecfdf3;
        color: #198754;
        border: 1px solid #cdebd8;
        padding: .45rem .75rem;
        border-radius: 999px;
        font-size: .85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        max-width: 100%;
        white-space: normal;
    }

    .info-item {
        background: #fff;
        border: 1px solid rgba(15, 23, 42, .06);
        border-radius: 18px;
        padding: 1rem 1.1rem;
        height: 100%;
        transition: all .25s ease;
        overflow-wrap: anywhere;
    }

    .info-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 30px rgba(15, 23, 42, .06);
    }

    .info-label {
        color: #64748b;
        font-size: .88rem;
        margin-bottom: .35rem;
    }

    .info-value {
        color: #0f172a;
        font-weight: 700;
        line-height: 1.6;
        word-break: break-word;
    }

    .education-period {
        display: flex;
        align-items: center;
        gap: .15rem;
        margin-top: .35rem;
        color: #0d6efd;
        font-size: .8rem;
        font-weight: 600;
        line-height: 1.45;
    }

    .education-period-missing {
        color: #b45309;
    }

    .profile-note {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        color: #475569;
        border-radius: 16px;
        padding: .95rem 1rem;
        word-break: break-word;
    }

    .section-head {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 1rem;
    }

    .section-title {
        font-weight: 800;
        color: #0f172a;
    }

    .section-subtitle {
        color: #64748b;
    }

    .stat-card {
        border-radius: 24px;
        overflow: hidden;
        transition: transform .25s ease, box-shadow .25s ease;
    }

    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, .08) !important;
    }

    .stat-card-success {
        background: linear-gradient(180deg, #ffffff 0%, #f3fcf7 100%);
    }

    .stat-card-warning {
        background: linear-gradient(180deg, #ffffff 0%, #fffaf0 100%);
    }

    .stat-card-danger {
        background: linear-gradient(180deg, #ffffff 0%, #fff5f5 100%);
    }

    .stat-icon {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        background: rgba(13,110,253,.08);
        color: #0d6efd;
        margin-bottom: 1rem;
    }

    .stat-label {
        color: #334155;
        font-size: .95rem;
        font-weight: 700;
        margin-bottom: .35rem;
    }

    .stat-number {
        font-size: 2.2rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
        word-break: break-word;
    }

    .stat-desc {
        color: #64748b;
        font-size: .92rem;
        margin-top: .35rem;
    }

    .stat-list {
        border-top: 1px solid rgba(15, 23, 42, .06);
        padding-top: 1rem;
        color: #475569;
        font-size: .92rem;
        line-height: 1.75;
        word-break: break-word;
    }

    .stat-list li {
        position: relative;
        padding-left: 0;
        margin-bottom: .35rem;
    }

    .stat-list li i {
        color: #0d6efd;
        font-size: 1rem;
        vertical-align: middle;
    }

    .empty-text {
        color: #94a3b8;
    }

    .single-line-info {
        color: #475569;
        word-break: break-word;
    }

    .service-card {
        border-radius: 24px;
        transition: transform .25s ease, box-shadow .25s ease;
        background: #fff;
    }

    .service-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 22px 44px rgba(15, 23, 42, .08) !important;
    }

    .service-icon {
        width: 66px;
        height: 66px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .service-title {
        font-weight: 800;
        color: #0f172a;
        margin-bottom: .6rem;
    }

    .service-text {
        color: #64748b;
        line-height: 1.7;
        min-height: 72px;
        word-break: break-word;
    }

    .service-link {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        font-weight: 700;
        color: #0d6efd;
        text-decoration: none;
        flex-wrap: wrap;
    }

    .service-link:hover {
        color: #0a58ca;
    }

    .service-mini-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:.35rem .7rem;
    border-radius:999px;
    background:#ecfdf3;
    border:1px solid #cdebd8;
    color:#198754;
    font-size:.78rem;
    font-weight:700;
    white-space:nowrap;
}

.service-meta-list{
    display:flex;
    flex-direction:column;
    gap:.45rem;
    padding:.8rem .9rem;
    border-radius:16px;
    background:#f8fafc;
    border:1px solid rgba(15,23,42,.06);
}

.service-meta-item{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:.75rem;
}

.service-meta-label{
    color:#64748b;
    font-size:.86rem;
    line-height:1.5;
}

.service-meta-value{
    color:#0f172a;
    font-weight:700;
    font-size:.9rem;
    text-align:right;
    line-height:1.5;
    word-break:break-word;
}
.service-mini-badge-danger{
    background:#fff1f2;
    border:1px solid #fecdd3;
    color:#dc2626;
}

/* ทำให้ทั้งการ์ดคลิกได้ */
.service-card-link{
    display:block;
    text-decoration:none;
    color:inherit;
}

.service-card-link:hover{
    text-decoration:none;
    color:inherit;
}

/* hover effect */
.service-card-link .service-card{
    transition:all .25s ease;
}

.service-card-link:hover .service-card{
    transform:translateY(-4px);
    box-shadow:0 20px 40px rgba(0,0,0,.08);
}
.service-card-link{
    display:block;
    text-decoration:none;
    color:inherit;
}

.service-card-link:hover{
    color:inherit;
}

.service-card-link .service-card{
    transition:all .25s ease;
}

.service-card-link:hover .service-card{
    transform:translateY(-4px);
    box-shadow:0 20px 40px rgba(0,0,0,.08);
}

    @media (max-width: 1399.98px) {
        .client-page {
            padding: 1.1rem 1rem 2rem;
        }
    }

    @media (max-width: 1199.98px) {
        .client-topbar {
            align-items: stretch !important;
        }

        .topbar-left,
        .topbar-right {
            width: 100%;
        }

        .topbar-right {
            text-align: left !important;
        }

        .btn-back,
        .page-badge {
            width: 100%;
        }

        .page-header-card {
            padding: 1.5rem;
        }

        .header-mini-stat {
            display: block;
            width: 100%;
            min-width: 0;
        }

        .service-text {
            min-height: auto;
        }
    }

    @media (max-width: 991.98px) {
        .client-page {
            padding: 1rem .9rem 1.5rem;
        }

        .page-header-card,
        .profile-card,
        .stat-card,
        .service-card {
            border-radius: 20px;
        }

        .profile-left-panel {
            padding: 2rem 1.25rem;
        }

        .profile-right-panel {
            padding: 1.5rem !important;
        }

        .detail-head {
            align-items: stretch !important;
        }

        .detail-status {
            width: 100%;
        }

        .section-head {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media (max-width: 767.98px) {
        .client-page {
            padding: .85rem .75rem 1.25rem;
        }

        .btn-back {
            padding: .7rem .95rem;
            font-size: .95rem;
        }

        .page-badge {
            padding: .7rem .95rem;
            font-size: .95rem;
        }

        .page-header-card {
            padding: 1.2rem 1rem;
        }

        .section-kicker {
            font-size: .75rem;
            padding: .38rem .7rem;
        }

        .page-title {
            font-size: 1.45rem;
        }

        .page-subtitle {
            font-size: .94rem;
            line-height: 1.65;
        }

        .profile-avatar {
            width: 128px;
            height: 128px;
        }

        .profile-name {
            font-size: 1.25rem;
        }

        .quick-tag {
            width: 100%;
        }

        .info-item {
            padding: .95rem 1rem;
        }

        .stat-number {
            font-size: 1.9rem;
        }

        .service-icon {
            width: 58px;
            height: 58px;
            font-size: 1.35rem;
        }
    }

    @media (max-width: 575.98px) {
        .client-topbar {
            gap: .85rem !important;
        }

        .btn-back {
            border-radius: 18px;
        }

        .page-badge {
            border-radius: 18px;
        }

        .profile-left-panel {
            padding: 1.5rem 1rem;
        }

        .profile-right-panel {
            padding: 1rem !important;
        }

        .stat-card .card-body,
        .service-card .card-body {
            padding: 1rem !important;
        }

        .service-meta-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .service-meta-value {
            text-align: left;
        }
    }
</style>

@endsection