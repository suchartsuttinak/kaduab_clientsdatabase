@extends('admin.admin_master')
@section('admin')

@php
    /**
     * Audit Log page
     * - ใช้ CSS แบบ scoped เฉพาะ .audit-log-page
     * - Pagination สร้างเองเพื่อไม่พึ่ง Tailwind pagination view / SVG
     * - รองรับ query string ของตัวกรองทุกตัว
     */

    $hasFilters =
        request()->filled('date_from') ||
        request()->filled('date_to') ||
        request()->filled('user_id') ||
        request()->filled('action') ||
        request()->filled('module') ||
        request()->filled('result') ||
        request()->filled('search') ||
        $errors->any();

    $actionLabels = [
        'LOGIN'             => 'เข้าสู่ระบบ',
        'LOGIN_FAILED'      => 'เข้าสู่ระบบไม่สำเร็จ',
        'LOGOUT'            => 'ออกจากระบบ',
        'VIEW'              => 'ดูข้อมูล',
        'CREATE'            => 'เพิ่มข้อมูล',
        'UPDATE'            => 'แก้ไขข้อมูล',
        'DELETE'            => 'ลบข้อมูล',
        'ACCESS_DENIED'     => 'ถูกปฏิเสธการเข้าถึง',
        'PRINT'             => 'พิมพ์รายงาน',
        'DOWNLOAD'          => 'ดาวน์โหลด',
        'EXPORT'            => 'ส่งออกข้อมูล',
        'PERMISSION_CHANGE' => 'เปลี่ยนแปลงสิทธิ์',
        'TEST'              => 'ทดสอบระบบ',
    ];

    $resultLabels = [
        'success' => 'สำเร็จ',
        'failed'  => 'ไม่สำเร็จ',
        'denied'  => 'ปฏิเสธ',
    ];

    // แสดงชื่อเป้าหมายโดยอ้างจากชนิด Model เท่านั้น ไม่เก็บชื่อบุคคลซ้ำใน audit_logs
    $subjectLabels = [
        'Client' => 'ทะเบียนผู้รับบริการ',
        'Factfinding' => 'ข้อมูลสอบข้อเท็จจริง',
        'Accident' => 'บันทึกการบาดเจ็บ',
        'Medical' => 'ข้อมูลการรักษาพยาบาล',
        'Vaccination' => 'ข้อมูลวัคซีน',
        'Psychiatric' => 'ข้อมูลจิตเวช',
        'CheckBody' => 'ผลการตรวจร่างกาย',
        'HealthcHeckup' => 'ผลตรวจสุขภาพเบื้องต้น',
        'NutritionAssessment' => 'ประเมินภาวะโภชนาการ',
        'Observe' => 'บันทึกพฤติกรรม',
        'ObserveFollowup' => 'ติดตามพฤติกรรม',
        'Followup' => 'ติดตามสังคมสงเคราะห์',
        'SchoolFollowup' => 'ติดตามการศึกษา',
        'EducationRecord' => 'ประวัติการศึกษา',
        'Absent' => 'บันทึกการขาดเรียน',
        'Escape' => 'ข้อมูลออก/หนี',
        'EscapeFollow' => 'ติดตามออก/หนี',
        'VisitFamily' => 'เยี่ยมครอบครัว',
        'Estimate' => 'ประเมินสภาพครอบครัว',
        'CaseOutside' => 'ติดตามภายนอก',
        'JobAgency' => 'ส่งเสริมอาชีพ',
        'Addictive' => 'ข้อมูลสารเสพติด',
        'Refer' => 'ข้อมูลการส่งต่อ',
        'HelpSession' => 'การให้ความช่วยเหลือ',
        'ClientFile' => 'เอกสารผู้รับบริการ',
        'BehaviorScreening' => 'แบบคัดกรองพฤติกรรม',
        'SnapIvScreening' => 'แบบประเมิน SNAP-IV',
        'DepressionScreening' => 'แบบคัดกรองภาวะซึมเศร้า',
        'Operation' => 'บันทึกการปฏิบัติงาน',
        'Publicize' => 'ประชาสัมพันธ์',
        'User' => 'บัญชีผู้ใช้งาน',
        'House' => 'บ้านพัก',
        'Institution' => 'สถานศึกษา',
        'Semester' => 'ภาคเรียน',
        'Education' => 'ระดับการศึกษา',
    ];

    $formatAuditDateTime = static function ($date) {
        if (!$date) {
            return '-';
        }

        $date = $date->copy()->timezone('Asia/Bangkok');

        return $date->format('d/m/')
            . ($date->year + 543)
            . ' '
            . $date->format('H:i:s');
    };

    // เก็บ query string เดิมไว้เมื่อเปลี่ยนหน้า
    $auditLogs->appends(request()->except('page'));

    // สร้างช่วงเลขหน้าแบบกะทัดรัด เช่น 1 ... 4 5 6 ... 10
    $currentPage = (int) $auditLogs->currentPage();
    $lastPage    = (int) $auditLogs->lastPage();
    $pageWindow  = 1;

    $pageItems = [];
    if ($lastPage > 1) {
        $candidatePages = array_unique(array_filter([
            1,
            $currentPage - $pageWindow,
            $currentPage,
            $currentPage + $pageWindow,
            $lastPage,
        ], static fn ($page) => $page >= 1 && $page <= $lastPage));

        sort($candidatePages);

        $previous = null;
        foreach ($candidatePages as $page) {
            if ($previous !== null && $page - $previous > 1) {
                $pageItems[] = '...';
            }
            $pageItems[] = $page;
            $previous = $page;
        }
    }
@endphp

<div class="container-fluid audit-log-page">

    {{-- Header --}}
    <div class="alp-header-card">
        <div class="alp-title-wrap">
            <div class="alp-title-icon" aria-hidden="true">
                <i class="bi bi-shield-lock-fill"></i>
            </div>

            <div class="alp-title-text">
                <h4 class="alp-page-title">ประวัติการใช้งานระบบ</h4>
                <div class="alp-page-subtitle">
                    ตรวจสอบการเข้าสู่ระบบ การเข้าถึง และเหตุการณ์สำคัญภายในระบบ
                </div>
            </div>
        </div>

        <div class="alp-header-actions">
            <button
                type="button"
                class="btn alp-btn-filter"
                data-bs-toggle="collapse"
                data-bs-target="#auditFilterPanel"
                aria-expanded="{{ $hasFilters ? 'true' : 'false' }}"
                aria-controls="auditFilterPanel"
            >
                <i class="bi bi-funnel-fill"></i>
                <span>ค้นหา</span>
            </button>
        </div>
    </div>

    {{-- Validation --}}
    @if($errors->any())
        <div class="alert alert-danger alp-alert shadow-sm border-0" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                <div>
                    <div class="fw-bold mb-1">กรุณาตรวจสอบเงื่อนไขการค้นหา</div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Filter --}}
    <div id="auditFilterPanel" class="collapse {{ $hasFilters ? 'show' : '' }}">
        <div class="alp-filter-card">
            <form
                method="GET"
                action="{{ route('audit_logs.index') }}"
                autocomplete="off"
            >
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label" for="audit_date_from">วันที่เริ่มต้น</label>
                        <input
                            id="audit_date_from"
                            type="date"
                            name="date_from"
                            class="form-control"
                            value="{{ request('date_from') }}"
                            max="{{ now()->format('Y-m-d') }}"
                        >
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label" for="audit_date_to">วันที่สิ้นสุด</label>
                        <input
                            id="audit_date_to"
                            type="date"
                            name="date_to"
                            class="form-control"
                            value="{{ request('date_to') }}"
                            max="{{ now()->format('Y-m-d') }}"
                        >
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label" for="audit_user_id">ผู้ใช้งาน</label>
                        <select id="audit_user_id" name="user_id" class="form-select">
                            <option value="">ทั้งหมด</option>
                            @foreach($users as $user)
                                <option
                                    value="{{ $user->id }}"
                                    @selected((string) request('user_id') === (string) $user->id)
                                >
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label" for="audit_action">การดำเนินการ</label>
                        <select id="audit_action" name="action" class="form-select">
                            <option value="">ทั้งหมด</option>
                            @foreach($actionLabels as $value => $label)
                                <option value="{{ $value }}" @selected(request('action') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label" for="audit_module">หมวด / Module</label>
                        <select id="audit_module" name="module" class="form-select">
                            <option value="">ทั้งหมด</option>
                            @foreach($modules as $module)
                                <option value="{{ $module }}" @selected(request('module') === $module)>
                                    {{ $module }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label" for="audit_result">ผลการทำงาน</label>
                        <select id="audit_result" name="result" class="form-select">
                            <option value="">ทั้งหมด</option>
                            @foreach($resultLabels as $value => $label)
                                <option value="{{ $value }}" @selected(request('result') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="form-label" for="audit_search">ค้นหาข้อมูลทางเทคนิค</label>
                        <div class="input-group alp-search-group">
                            <span class="input-group-text" aria-hidden="true">
                                <i class="bi bi-search"></i>
                            </span>
                            <input
                                id="audit_search"
                                type="text"
                                name="search"
                                class="form-control"
                                value="{{ request('search') }}"
                                maxlength="100"
                                placeholder="Action, Module, Route, IP หรือ Request ID"
                            >
                        </div>
                    </div>
                </div>

                <div class="alp-filter-actions">
                    <button type="submit" class="btn alp-btn-search">
                        <i class="bi bi-search"></i>
                        <span>ค้นหา</span>
                    </button>

                    <a href="{{ route('audit_logs.index') }}" class="btn alp-btn-clear">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>ล้างการค้นหา</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Main card --}}
    <div class="alp-card">
        <div class="alp-card-head">
            <div>
                <div class="alp-card-title">
                    <i class="bi bi-clock-history"></i>
                    <span>รายการประวัติการใช้งาน</span>
                </div>
                <div class="alp-card-subtitle">
                    พบทั้งหมด <strong>{{ number_format($auditLogs->total()) }}</strong> รายการ
                </div>
            </div>

            <div class="alp-security-note">
                <i class="bi bi-shield-check"></i>
                <span>ข้อมูลบันทึกของระบบ ไม่สามารถแก้ไขหรือลบจากหน้านี้</span>
            </div>
        </div>

        <div class="alp-card-body">
            <div class="alp-table-wrap">
                <table class="table align-middle mb-0 alp-table">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>วันเวลา</th>
                            <th>ผู้ใช้งาน</th>
                            <th>การดำเนินการ</th>
                            <th>หมวด</th>
                            <th>เป้าหมาย</th>
                            <th class="text-center">ผู้รับบริการ</th>
                            <th>IP Address</th>
                            <th class="text-center">ผล</th>
                            <th>รายละเอียดทางเทคนิค</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($auditLogs as $log)
                            <tr>
                                <td class="text-center alp-row-number">
                                    {{ ($auditLogs->firstItem() ?? 1) + $loop->index }}
                                </td>

                                <td>
                                    <div class="alp-date-cell">
                                        <i class="bi bi-clock"></i>
                                        <span>{{ $formatAuditDateTime($log->created_at) }}</span>
                                    </div>
                                </td>

                                <td>
                                    @if($log->user)
                                        <div class="alp-user-name">{{ $log->user->name }}</div>
                                        <div class="alp-small-text">User ID: {{ $log->user_id }}</div>
                                    @elseif($log->user_id)
                                        <div class="alp-user-name">ผู้ใช้เดิม</div>
                                        <div class="alp-small-text">User ID: {{ $log->user_id }}</div>
                                    @else
                                        <span class="alp-empty-text">ไม่ระบุผู้ใช้</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="alp-action-badge alp-action-{{ strtolower($log->action) }}">
                                        {{ $actionLabels[$log->action] ?? $log->action }}
                                    </span>
                                </td>

                                <td>
                                    @if($log->module)
                                        <span class="alp-module-badge">{{ $log->module }}</span>
                                    @else
                                        <span class="alp-empty-text">-</span>
                                    @endif
                                </td>

                                <td>
                                    @if($log->subject_type === $userMorphClass && $log->subject_id)
                                        @php
                                            $targetUser = $targetUsers->get((int) $log->subject_id);
                                        @endphp

                                        @if($targetUser)
                                            <div class="alp-target-name">{{ $targetUser->name }}</div>
                                            <div class="alp-small-text">User ID: {{ $log->subject_id }}</div>
                                        @else
                                            <div class="alp-target-name">ผู้ใช้เดิม</div>
                                            <div class="alp-small-text">User ID: {{ $log->subject_id }}</div>
                                        @endif
                                    @elseif($log->subject_type && $log->subject_id)
                                        @php
                                            $subjectBase = class_basename($log->subject_type);
                                            $subjectName = $subjectLabels[$subjectBase]
                                                ?? \Illuminate\Support\Str::headline($subjectBase);
                                        @endphp

                                        <div class="alp-target-name">{{ $subjectName }}</div>
                                        <div class="alp-small-text">Record ID: {{ $log->subject_id }}</div>
                                    @else
                                        <span class="alp-empty-text">-</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if($log->client)
                                        <div class="alp-target-name">{{ $log->client->full_name ?: ('ผู้รับบริการ #' . $log->client_id) }}</div>
                                        <div class="alp-small-text">Client ID: #{{ $log->client_id }}</div>
                                    @elseif($log->client_id)
                                        <div class="alp-target-name">ผู้รับบริการเดิม</div>
                                        <div class="alp-small-text">Client ID: #{{ $log->client_id }}</div>
                                    @else
                                        <span class="alp-empty-text">-</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="alp-ip">{{ $log->ip_address ?: '-' }}</span>
                                </td>

                                <td class="text-center">
                                    <span class="alp-result-badge alp-result-{{ $log->result }}">
                                        {{ $resultLabels[$log->result] ?? $log->result }}
                                    </span>
                                </td>

                                <td>
                                    <div class="alp-tech-detail">
                                        <div class="alp-tech-topline">
                                            <span class="alp-tech-label">Method</span>
                                            <span class="alp-method-badge">{{ $log->http_method ?: '-' }}</span>

                                            @if($log->status_code)
                                                <span class="alp-status-code">HTTP {{ $log->status_code }}</span>
                                            @endif
                                        </div>

                                        <div class="alp-route-text" title="{{ $log->route_name ?: '-' }}">
                                            <i class="bi bi-signpost-split"></i>
                                            <span>{{ $log->route_name ?: '-' }}</span>
                                        </div>

                                        @if(!empty($log->changed_fields))
                                            <div class="alp-request-id" title="{{ implode(', ', $log->changed_fields) }}">
                                                Fields: {{ implode(', ', array_slice($log->changed_fields, 0, 5)) }}{{ count($log->changed_fields) > 5 ? ' …' : '' }}
                                            </div>
                                        @endif

                                        @if($log->request_id)
                                            <div class="alp-request-id" title="{{ $log->request_id }}">
                                                Request: {{ $log->request_id }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="p-0">
                                    <div class="alp-empty-state">
                                        <i class="bi bi-clock-history"></i>

                                        <div class="alp-empty-title">
                                            @if($hasFilters)
                                                ไม่พบข้อมูลตามเงื่อนไขที่ค้นหา
                                            @else
                                                ยังไม่มีประวัติการใช้งานระบบ
                                            @endif
                                        </div>

                                        <div class="alp-empty-subtitle">
                                            @if($hasFilters)
                                                ลองเปลี่ยนเงื่อนไขการค้นหา หรือล้างตัวกรอง
                                            @else
                                                เมื่อระบบมีการใช้งาน รายการจะปรากฏที่หน้านี้อัตโนมัติ
                                            @endif
                                        </div>

                                        @if($hasFilters)
                                            <a href="{{ route('audit_logs.index') }}" class="btn alp-btn-clear mt-3">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                                <span>ล้างการค้นหา</span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Custom Pagination: ไม่มี SVG / ไม่มี Tailwind pagination view --}}
            @if($auditLogs->hasPages())
                <div class="alp-pagination-wrap">
                    <div class="alp-pagination-info">
                        แสดง
                        <strong>{{ number_format($auditLogs->firstItem() ?? 0) }}</strong>
                        ถึง
                        <strong>{{ number_format($auditLogs->lastItem() ?? 0) }}</strong>
                        จาก
                        <strong>{{ number_format($auditLogs->total()) }}</strong>
                        รายการ
                    </div>

                    <nav class="alp-pagination-nav" aria-label="การแบ่งหน้าประวัติการใช้งาน">
                        <div class="alp-pagination-list">
                            @if($auditLogs->onFirstPage())
                                <span class="alp-page-btn is-disabled" aria-disabled="true">
                                    <i class="bi bi-chevron-left"></i>
                                    <span class="alp-page-btn-text">ก่อนหน้า</span>
                                </span>
                            @else
                                <a
                                    class="alp-page-btn"
                                    href="{{ $auditLogs->previousPageUrl() }}"
                                    rel="prev"
                                    aria-label="หน้าก่อนหน้า"
                                >
                                    <i class="bi bi-chevron-left"></i>
                                    <span class="alp-page-btn-text">ก่อนหน้า</span>
                                </a>
                            @endif

                            <div class="alp-page-numbers" aria-label="เลขหน้า">
                                @foreach($pageItems as $item)
                                    @if($item === '...')
                                        <span class="alp-page-ellipsis" aria-hidden="true">…</span>
                                    @elseif($item === $currentPage)
                                        <span class="alp-page-number is-active" aria-current="page">
                                            {{ $item }}
                                        </span>
                                    @else
                                        <a
                                            class="alp-page-number"
                                            href="{{ $auditLogs->url($item) }}"
                                            aria-label="ไปหน้าที่ {{ $item }}"
                                        >
                                            {{ $item }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>

                            @if($auditLogs->hasMorePages())
                                <a
                                    class="alp-page-btn"
                                    href="{{ $auditLogs->nextPageUrl() }}"
                                    rel="next"
                                    aria-label="หน้าถัดไป"
                                >
                                    <span class="alp-page-btn-text">ถัดไป</span>
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            @else
                                <span class="alp-page-btn is-disabled" aria-disabled="true">
                                    <span class="alp-page-btn-text">ถัดไป</span>
                                    <i class="bi bi-chevron-right"></i>
                                </span>
                            @endif
                        </div>
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* ======================================================================
   AUDIT LOG PAGE
   Scope ทุก selector ไว้ใต้ .audit-log-page เพื่อไม่กระทบหน้าอื่น
====================================================================== */
.audit-log-page {
    --alp-primary: #2563eb;
    --alp-primary-dark: #1d4ed8;
    --alp-border: #e5edf5;
    --alp-text: #0f172a;
    --alp-muted: #64748b;
    --alp-soft: #f8fafc;
    --alp-shadow: 0 10px 30px rgba(15, 23, 42, .045);

    width: 100%;
    padding: 1.25rem 1rem 1.5rem;
    box-sizing: border-box;
}

.audit-log-page *,
.audit-log-page *::before,
.audit-log-page *::after {
    box-sizing: border-box;
}

/* Header / cards */
.audit-log-page .alp-header-card,
.audit-log-page .alp-filter-card,
.audit-log-page .alp-card {
    background: #fff;
    border: 1px solid var(--alp-border);
    box-shadow: var(--alp-shadow);
}

.audit-log-page .alp-header-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    padding: 1.15rem 1.25rem;
    margin-bottom: 1rem;
    border-radius: 22px;
}

.audit-log-page .alp-title-wrap {
    display: flex;
    align-items: center;
    gap: .85rem;
    min-width: 0;
}

.audit-log-page .alp-title-icon {
    width: 52px;
    height: 52px;
    flex: 0 0 52px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    color: #fff;
    font-size: 1.2rem;
    background: linear-gradient(135deg, #1d4ed8, #3b82f6);
    box-shadow: 0 10px 22px rgba(37, 99, 235, .18);
}

.audit-log-page .alp-title-text {
    min-width: 0;
}

.audit-log-page .alp-page-title {
    margin: 0 0 .2rem;
    color: var(--alp-text);
    font-size: 1.28rem;
    font-weight: 800;
    line-height: 1.3;
}

.audit-log-page .alp-page-subtitle {
    color: var(--alp-muted);
    font-size: .9rem;
    line-height: 1.55;
}

.audit-log-page .alp-header-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .55rem;
    flex-wrap: wrap;
}

/* Buttons */
.audit-log-page .alp-btn-filter,
.audit-log-page .alp-btn-search,
.audit-log-page .alp-btn-clear {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    min-height: 40px;
    border-radius: 12px;
    font-weight: 700;
    line-height: 1.2;
    transition: transform .15s ease, box-shadow .15s ease, background-color .15s ease;
}

.audit-log-page .alp-btn-filter,
.audit-log-page .alp-btn-search {
    border: 0;
    color: #fff;
    background: linear-gradient(135deg, #1d4ed8, #3b82f6);
    box-shadow: 0 7px 16px rgba(37, 99, 235, .16);
}

.audit-log-page .alp-btn-filter {
    padding: .62rem .92rem;
}

.audit-log-page .alp-btn-search {
    padding: .64rem 1rem;
}

.audit-log-page .alp-btn-filter:hover,
.audit-log-page .alp-btn-filter:focus,
.audit-log-page .alp-btn-search:hover,
.audit-log-page .alp-btn-search:focus {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 9px 18px rgba(37, 99, 235, .2);
}

.audit-log-page .alp-btn-clear {
    padding: .62rem .92rem;
    color: #475569;
    background: #fff;
    border: 1px solid #dbe3ee;
}

.audit-log-page .alp-btn-clear:hover,
.audit-log-page .alp-btn-clear:focus {
    color: #1e293b;
    background: #f8fafc;
    border-color: #cbd5e1;
}

/* Alert */
.audit-log-page .alp-alert {
    padding: .95rem 1rem;
    margin-bottom: 1rem;
    border-radius: 16px;
}

/* Filter */
.audit-log-page .alp-filter-card {
    padding: 1.1rem;
    margin-bottom: 1rem;
    border-radius: 20px;
}

.audit-log-page .alp-filter-card .form-label {
    margin-bottom: .4rem;
    color: #334155;
    font-size: .86rem;
    font-weight: 700;
}

.audit-log-page .alp-filter-card .form-control,
.audit-log-page .alp-filter-card .form-select,
.audit-log-page .alp-filter-card .input-group-text {
    min-height: 42px;
    border-color: #dbe3ee;
}

.audit-log-page .alp-filter-card .form-control,
.audit-log-page .alp-filter-card .form-select {
    border-radius: 10px;
}

.audit-log-page .alp-filter-card .form-control:focus,
.audit-log-page .alp-filter-card .form-select:focus {
    border-color: #93c5fd;
    box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .08);
}

.audit-log-page .alp-search-group .input-group-text {
    color: #64748b;
    background: #f8fafc;
    border-radius: 10px 0 0 10px;
}

.audit-log-page .alp-search-group .form-control {
    border-radius: 0 10px 10px 0;
}

.audit-log-page .alp-filter-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .55rem;
    flex-wrap: wrap;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eef2f7;
}

/* Main card */
.audit-log-page .alp-card {
    border-radius: 22px;
    overflow: hidden;
}

.audit-log-page .alp-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .8rem;
    flex-wrap: wrap;
    padding: 1rem 1.15rem;
    border-bottom: 1px solid var(--alp-border);
}

.audit-log-page .alp-card-title {
    display: flex;
    align-items: center;
    gap: .5rem;
    color: var(--alp-text);
    font-weight: 800;
}

.audit-log-page .alp-card-title i {
    color: var(--alp-primary);
}

.audit-log-page .alp-card-subtitle {
    margin-top: .15rem;
    color: var(--alp-muted);
    font-size: .84rem;
}

.audit-log-page .alp-security-note {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .5rem .72rem;
    border: 1px solid #bbf7d0;
    border-radius: 999px;
    color: #166534;
    background: #f0fdf4;
    font-size: .8rem;
    font-weight: 700;
}

.audit-log-page .alp-card-body {
    padding: .9rem;
}

/* Table */
.audit-log-page .alp-table-wrap {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    border: 1px solid #eef2f7;
    border-radius: 15px;
    -webkit-overflow-scrolling: touch;
}

.audit-log-page .alp-table {
    width: 100%;
    min-width: 1380px;
    margin: 0;
}

.audit-log-page .alp-table thead th {
    padding: .82rem .7rem;
    background: #f8fafc;
    color: #475569;
    font-size: .83rem;
    font-weight: 800;
    white-space: nowrap;
    border-bottom: 1px solid #eaf0f6;
}

.audit-log-page .alp-table tbody td {
    padding: .82rem .7rem;
    background: #fff;
    border-color: #eef2f7;
    vertical-align: middle;
}

.audit-log-page .alp-table tbody tr:hover td {
    background: #fcfdff;
}

.audit-log-page .alp-row-number {
    color: #64748b;
    font-weight: 700;
}

/* Date / User / target */
.audit-log-page .alp-date-cell {
    display: flex;
    align-items: center;
    gap: .4rem;
    min-width: 145px;
    color: #334155;
    white-space: nowrap;
}

.audit-log-page .alp-date-cell i {
    color: #94a3b8;
}

.audit-log-page .alp-user-name {
    min-width: 120px;
    color: #0f172a;
    font-weight: 800;
}

.audit-log-page .alp-target-name {
    min-width: 115px;
    color: #334155;
    font-weight: 800;
}

.audit-log-page .alp-small-text,
.audit-log-page .alp-empty-text {
    color: #94a3b8;
    font-size: .78rem;
}

/* Badges */
.audit-log-page .alp-action-badge,
.audit-log-page .alp-module-badge,
.audit-log-page .alp-result-badge,
.audit-log-page .alp-client-id,
.audit-log-page .alp-method-badge,
.audit-log-page .alp-status-code {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    font-size: .77rem;
    font-weight: 800;
    white-space: nowrap;
}

.audit-log-page .alp-action-badge {
    padding: .42rem .62rem;
    color: #334155;
    background: #f8fafc;
    border: 1px solid #dbe3ee;
}

.audit-log-page .alp-action-login {
    color: #166534;
    background: #f0fdf4;
    border-color: #bbf7d0;
}

.audit-log-page .alp-action-login_failed,
.audit-log-page .alp-action-access_denied {
    color: #b91c1c;
    background: #fef2f2;
    border-color: #fecaca;
}

.audit-log-page .alp-action-logout {
    color: #475569;
    background: #f8fafc;
    border-color: #dbe3ee;
}

.audit-log-page .alp-action-create {
    color: #1d4ed8;
    background: #eff6ff;
    border-color: #bfdbfe;
}

.audit-log-page .alp-action-update {
    color: #c2410c;
    background: #fff7ed;
    border-color: #fed7aa;
}

.audit-log-page .alp-action-delete {
    color: #b91c1c;
    background: #fef2f2;
    border-color: #fecaca;
}

.audit-log-page .alp-action-permission_change {
    color: #6d28d9;
    background: #f5f3ff;
    border-color: #ddd6fe;
}

.audit-log-page .alp-module-badge {
    padding: .4rem .6rem;
    color: #5b21b6;
    background: #f5f3ff;
    border: 1px solid #ddd6fe;
}

.audit-log-page .alp-client-id {
    padding: .38rem .58rem;
    color: #1e40af;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
}

.audit-log-page .alp-result-badge {
    min-width: 68px;
    padding: .4rem .6rem;
}

.audit-log-page .alp-result-success {
    color: #15803d;
    background: #ecfdf3;
    border: 1px solid #bbf7d0;
}

.audit-log-page .alp-result-failed {
    color: #b91c1c;
    background: #fef2f2;
    border: 1px solid #fecaca;
}

.audit-log-page .alp-result-denied {
    color: #9a3412;
    background: #fff7ed;
    border: 1px solid #fed7aa;
}

/* Technical */
.audit-log-page .alp-ip {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    color: #475569;
    font-size: .78rem;
    white-space: nowrap;
}

.audit-log-page .alp-tech-detail {
    min-width: 230px;
}

.audit-log-page .alp-tech-detail > div + div {
    margin-top: .28rem;
}

.audit-log-page .alp-tech-topline {
    display: flex;
    align-items: center;
    gap: .28rem;
    flex-wrap: wrap;
}

.audit-log-page .alp-tech-label {
    margin-right: .1rem;
    color: #94a3b8;
    font-size: .74rem;
}

.audit-log-page .alp-method-badge,
.audit-log-page .alp-status-code {
    padding: .28rem .48rem;
}

.audit-log-page .alp-method-badge {
    color: #1e40af;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
}

.audit-log-page .alp-status-code {
    color: #475569;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
}

.audit-log-page .alp-route-text,
.audit-log-page .alp-request-id {
    max-width: 310px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.audit-log-page .alp-route-text {
    color: #334155;
    font-size: .78rem;
}

.audit-log-page .alp-route-text i {
    margin-right: .22rem;
    color: #94a3b8;
}

.audit-log-page .alp-request-id {
    color: #94a3b8;
    font-size: .71rem;
}

/* Empty state */
.audit-log-page .alp-empty-state {
    min-height: 220px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
    color: var(--alp-muted);
    text-align: center;
}

.audit-log-page .alp-empty-state > i {
    margin-bottom: .55rem;
    color: #94a3b8;
    font-size: 2.1rem;
}

.audit-log-page .alp-empty-title {
    color: var(--alp-text);
    font-weight: 800;
}

.audit-log-page .alp-empty-subtitle {
    margin-top: .22rem;
    color: var(--alp-muted);
    font-size: .86rem;
}

/* ======================================================================
   CUSTOM PAGINATION
   สำคัญ: ใช้ pagination markup ของหน้านี้เอง จึงไม่เรียก pagination view ที่มี SVG
====================================================================== */
.audit-log-page .alp-pagination-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .9rem;
    flex-wrap: wrap;
    margin-top: .9rem;
    padding-top: .9rem;
    border-top: 1px solid #eef2f7;
}

.audit-log-page .alp-pagination-info {
    color: #64748b;
    font-size: .82rem;
    white-space: nowrap;
}

.audit-log-page .alp-pagination-nav {
    display: block;
    margin: 0;
    padding: 0;
    width: auto;
    max-width: 100%;
}

.audit-log-page .alp-pagination-list {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .32rem;
    flex-wrap: wrap;
    margin: 0;
    padding: 0;
}

.audit-log-page .alp-page-numbers {
    display: flex;
    align-items: center;
    gap: .28rem;
}

.audit-log-page .alp-page-btn,
.audit-log-page .alp-page-number,
.audit-log-page .alp-page-ellipsis {
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    margin: 0;
    border-radius: 9px;
    line-height: 1;
    text-decoration: none !important;
    vertical-align: middle;
}

.audit-log-page .alp-page-btn {
    gap: .3rem;
    min-width: 84px;
    padding: 0 .72rem;
    border: 1px solid #dbe3ee;
    color: #475569;
    background: #fff;
    font-size: .8rem;
    font-weight: 700;
}

.audit-log-page .alp-page-btn i {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 14px;
    height: 14px;
    font-size: .72rem;
    line-height: 1;
}

.audit-log-page .alp-page-number {
    min-width: 36px;
    padding: 0 .55rem;
    border: 1px solid #dbe3ee;
    color: #475569;
    background: #fff;
    font-size: .8rem;
    font-weight: 700;
}

.audit-log-page .alp-page-ellipsis {
    min-width: 24px;
    color: #94a3b8;
    font-size: .86rem;
}

.audit-log-page a.alp-page-btn:hover,
.audit-log-page a.alp-page-btn:focus,
.audit-log-page a.alp-page-number:hover,
.audit-log-page a.alp-page-number:focus {
    color: #1d4ed8;
    background: #eff6ff;
    border-color: #bfdbfe;
}

.audit-log-page .alp-page-number.is-active {
    color: #fff;
    background: var(--alp-primary);
    border-color: var(--alp-primary);
    box-shadow: 0 5px 12px rgba(37, 99, 235, .16);
}

.audit-log-page .alp-page-btn.is-disabled {
    color: #94a3b8;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    cursor: default;
    pointer-events: none;
}

/* กัน CSS global จาก template มาทำให้ icon / svg ใหญ่ผิดปกติ */
.audit-log-page .alp-pagination-nav svg {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
}

.audit-log-page .alp-pagination-nav i,
.audit-log-page .alp-pagination-nav .bi {
    transform: none !important;
    max-width: 16px !important;
    max-height: 16px !important;
}

/* Responsive */
@media (max-width: 1199.98px) {
    .audit-log-page {
        padding-left: .8rem;
        padding-right: .8rem;
    }

    .audit-log-page .alp-table {
        min-width: 1320px;
    }
}

@media (max-width: 767.98px) {
    .audit-log-page {
        padding: .8rem .65rem 1.15rem;
    }

    .audit-log-page .alp-header-card {
        align-items: stretch;
        padding: .95rem;
        border-radius: 18px;
    }

    .audit-log-page .alp-title-wrap {
        align-items: flex-start;
    }

    .audit-log-page .alp-title-icon {
        width: 46px;
        height: 46px;
        flex-basis: 46px;
        border-radius: 14px;
    }

    .audit-log-page .alp-page-title {
        font-size: 1.08rem;
    }

    .audit-log-page .alp-page-subtitle {
        font-size: .82rem;
    }

    .audit-log-page .alp-header-actions {
        width: 100%;
        justify-content: flex-end;
    }

    .audit-log-page .alp-filter-actions {
        align-items: stretch;
    }

    .audit-log-page .alp-filter-actions .btn {
        flex: 1 1 145px;
    }

    .audit-log-page .alp-card-head {
        align-items: flex-start;
        padding: .9rem;
    }

    .audit-log-page .alp-security-note {
        width: 100%;
        border-radius: 12px;
        white-space: normal;
    }

    .audit-log-page .alp-card-body {
        padding: .7rem;
    }

    .audit-log-page .alp-pagination-wrap {
        align-items: stretch;
        flex-direction: column;
        gap: .65rem;
    }

    .audit-log-page .alp-pagination-info {
        width: 100%;
        white-space: normal;
        text-align: center;
    }

    .audit-log-page .alp-pagination-nav {
        width: 100%;
        overflow-x: auto;
        padding-bottom: .1rem;
        -webkit-overflow-scrolling: touch;
    }

    .audit-log-page .alp-pagination-list {
        justify-content: center;
        flex-wrap: nowrap;
        min-width: max-content;
    }

    .audit-log-page .alp-page-btn {
        min-width: 38px;
        width: 38px;
        padding: 0;
    }

    .audit-log-page .alp-page-btn-text {
        display: none;
    }
}
</style>

@endsection