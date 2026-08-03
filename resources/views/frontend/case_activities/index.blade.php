@extends('admin_client.admin_client')

@section('content')
@php
    $moduleLabels = [
        'client'           => 'ผู้รับบริการ',
        'education_record' => 'บันทึกการศึกษา',
        'psychiatric'      => 'พบจิตแพทย์',
        'medical'          => 'พบแพทย์',
        'vaccine'          => 'วัคซีน',
        'observe'          => 'สังเกตพฤติกรรม',
        'addictive'        => 'พฤติกรรมเสพติด',
        'escape'           => 'หนีออกจากสถานสงเคราะห์',
        'school_followup'  => 'ติดตามการเรียน',
        'help_session'     => 'การช่วยเหลือ',
        'job_agency'       => 'จัดหางาน',
        'refer'            => 'จำหน่าย / ส่งต่อ',
        'absent'           => 'ขาดเรียน',
        'operation'        => 'กิจกรรมประจำวัน',
        'estimate'         => 'แบบประเมิน',
        'health_checkup'   => 'ตรวจสุขภาพ',
        'accident'         => 'อุบัติเหตุ',
    ];

    $allowedTypes = ['info', 'success', 'warning', 'danger'];
    $today = now()->toDateString();
    $hasFilters = request()->filled('module')
        || request()->filled('type')
        || request()->filled('date_from')
        || request()->filled('date_to');

    $clientDisplayName = trim((string) (
        $client->fullname
        ?? $client->full_name
        ?? (($client->first_name ?? '') . ' ' . ($client->last_name ?? ''))
    ));

    if ($clientDisplayName === '') {
        $clientDisplayName = 'ผู้รับบริการ';
    }
@endphp

@push('styles')
<style>
    .ca-page {
        --ca-primary: #2563eb;
        --ca-primary-dark: #1d4ed8;
        --ca-success: #16a34a;
        --ca-warning: #f59e0b;
        --ca-danger: #dc2626;
        --ca-text: #0f172a;
        --ca-muted: #64748b;
        --ca-line: #e2e8f0;
        padding: .5rem 0 1.5rem;
        overflow-x: hidden;
    }

    .ca-container {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
    }

    .ca-header {
        margin-bottom: 1rem;
        padding: 1.25rem 1.4rem;
        color: #fff;
        background: linear-gradient(135deg, #0f766e 0%, #2563eb 100%);
        border-radius: 20px;
        box-shadow: 0 14px 32px rgba(15, 23, 42, .14);
    }

    .ca-header-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .ca-title {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 800;
        line-height: 1.35;
    }

    .ca-subtitle {
        margin-top: .35rem;
        color: rgba(255, 255, 255, .9);
        font-size: .9rem;
    }

    .ca-actions {
        display: flex;
        align-items: center;
        gap: .55rem;
        flex-wrap: wrap;
    }

    .ca-btn {
        min-height: 40px;
        padding: .55rem .9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        border: 1px solid transparent;
        border-radius: 11px;
        font-size: .86rem;
        font-weight: 700;
        line-height: 1;
        text-decoration: none;
        white-space: nowrap;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
    }

    .ca-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .ca-btn-light {
        color: #1e3a8a;
        background: #fff;
        box-shadow: 0 6px 16px rgba(15, 23, 42, .12);
    }

    .ca-btn-light:hover,
    .ca-btn-light:focus {
        color: #1d4ed8;
        background: #f8fbff;
    }

    .ca-btn-outline {
        color: #fff;
        background: rgba(255, 255, 255, .12);
        border-color: rgba(255, 255, 255, .4);
    }

    .ca-btn-outline:hover,
    .ca-btn-outline:focus {
        color: #fff;
        background: rgba(255, 255, 255, .2);
    }

    .ca-filter-card,
    .ca-timeline-card,
    .ca-empty-card {
        margin-bottom: 1rem;
        padding: 1rem;
        background: #fff;
        border: 1px solid var(--ca-line);
        border-radius: 17px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, .055);
    }

    .ca-filter-head {
        margin-bottom: .8rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        flex-wrap: wrap;
    }

    .ca-filter-title {
        margin: 0;
        color: var(--ca-text);
        font-size: 1rem;
        font-weight: 800;
    }

    .ca-filter-summary {
        color: var(--ca-muted);
        font-size: .78rem;
        font-weight: 600;
    }

    .ca-label {
        display: block;
        margin-bottom: .35rem;
        color: #334155;
        font-size: .82rem;
        font-weight: 700;
    }

    .ca-filter-card .form-control,
    .ca-filter-card .form-select {
        min-height: 42px;
        border-color: #cbd5e1;
        border-radius: 10px;
        font-size: .84rem;
    }

    .ca-filter-card .form-control:focus,
    .ca-filter-card .form-select:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
    }

    .ca-filter-actions {
        display: flex;
        gap: .5rem;
    }

    .ca-filter-actions .btn {
        min-height: 42px;
        flex: 1 1 0;
        border-radius: 10px;
        font-size: .84rem;
        font-weight: 700;
    }

    .ca-validation-alert {
        margin-bottom: 1rem;
        padding: .8rem 1rem;
        color: #991b1b;
        background: #fff7f7;
        border: 1px solid #fecaca;
        border-radius: 12px;
        font-size: .85rem;
    }

    .ca-validation-alert ul {
        margin: .35rem 0 0;
        padding-left: 1.2rem;
    }

    .ca-timeline {
        position: relative;
        padding-left: 2.2rem;
    }

    .ca-timeline::before {
        content: "";
        position: absolute;
        top: .3rem;
        bottom: .3rem;
        left: .82rem;
        width: 3px;
        background: #dbeafe;
        border-radius: 999px;
    }

    .ca-item {
        position: relative;
        padding-bottom: 1rem;
    }

    .ca-item:last-child {
        padding-bottom: 0;
    }

    .ca-dot {
        position: absolute;
        z-index: 1;
        top: 0;
        left: -2.15rem;
        width: 1.9rem;
        height: 1.9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        border: 3px solid #fff;
        border-radius: 50%;
        box-shadow: 0 5px 13px rgba(15, 23, 42, .18);
    }

    .ca-type-info .ca-dot { background: var(--ca-primary); }
    .ca-type-success .ca-dot { background: var(--ca-success); }
    .ca-type-warning .ca-dot { background: var(--ca-warning); }
    .ca-type-danger .ca-dot { background: var(--ca-danger); }

    .ca-content {
        max-width: 100%;
        padding: .85rem 1rem;
        overflow: hidden;
        background: #fbfdff;
        border: 1px solid var(--ca-line);
        border-radius: 14px;
    }

    .ca-item-head {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: .65rem;
        align-items: start;
    }

    .ca-item-title {
        margin: 0;
        overflow-wrap: anywhere;
        color: var(--ca-text);
        font-size: .98rem;
        font-weight: 800;
        line-height: 1.4;
    }

    .ca-date {
        color: var(--ca-muted);
        font-size: .8rem;
        font-weight: 700;
        text-align: right;
        white-space: nowrap;
    }

    .ca-desc {
        margin: .4rem 0 0;
        overflow-wrap: anywhere;
        color: #334155;
        font-size: .87rem;
        line-height: 1.6;
    }

    .ca-meta {
        margin-top: .65rem;
        display: flex;
        gap: .4rem;
        flex-wrap: wrap;
    }

    .ca-badge {
        max-width: 100%;
        padding: .25rem .55rem;
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        overflow: hidden;
        color: #3730a3;
        background: #eef2ff;
        border-radius: 999px;
        font-size: .74rem;
        font-weight: 700;
        line-height: 1.35;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    a.ca-badge:hover,
    a.ca-badge:focus {
        color: #1d4ed8;
        background: #dbeafe;
    }

    .ca-empty-card {
        padding: 2.25rem 1rem;
        text-align: center;
        color: var(--ca-muted);
    }

    .ca-empty-icon {
        width: 3.4rem;
        height: 3.4rem;
        margin: 0 auto .7rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        background: #f1f5f9;
        border-radius: 50%;
        font-size: 1.6rem;
    }

    .ca-empty-title {
        margin: 0;
        color: #334155;
        font-size: 1rem;
        font-weight: 800;
    }

    .ca-empty-text {
        margin: .25rem 0 0;
        font-size: .82rem;
    }

    .ca-pagination-wrap {
        margin-top: 1rem;
        padding-top: .9rem;
        overflow-x: auto;
        border-top: 1px solid #e8edf3;
    }

    .ca-pagination-wrap .pagination {
        margin: 0;
        gap: .25rem;
        flex-wrap: wrap;
    }

    .ca-pagination-wrap .page-link {
        padding: .35rem .6rem;
        border-radius: 9px !important;
        font-size: .8rem;
    }

    @media (max-width: 767.98px) {
        .ca-page {
            padding-top: .25rem;
        }

        .ca-header {
            padding: 1rem;
            border-radius: 16px;
        }

        .ca-title {
            font-size: 1.15rem;
        }

        .ca-actions,
        .ca-actions .ca-btn {
            width: 100%;
        }

        .ca-item-head {
            grid-template-columns: 1fr;
        }

        .ca-date {
            text-align: left;
            white-space: normal;
        }

        .ca-timeline {
            padding-left: 1.9rem;
        }

        .ca-dot {
            left: -1.88rem;
        }

        .ca-timeline::before {
            left: .68rem;
        }

        .ca-filter-actions {
            flex-direction: column;
        }
    }
</style>
@endpush

<div class="ca-page">
    <div class="ca-container">

        <header class="ca-header">
            <div class="ca-header-top">
                <div>
                    <h1 class="ca-title">
                        <i class="bi bi-clock-history me-1"></i>
                        ความเคลื่อนไหวผู้รับบริการ
                    </h1>

                    <div class="ca-subtitle">
                        {{ $clientDisplayName }}

                        @if(!empty($client->register_number))
                            <span class="mx-1">•</span>
                            เลขทะเบียน {{ $client->register_number }}
                        @endif
                    </div>
                </div>

                <div class="ca-actions">
                    @if($activities->total() > 0)
                        <a href="{{ route(
                                'case-activities.report',
                                array_merge(
                                    ['client' => $client->id],
                                    request()->except('page')
                                )
                            ) }}"
                           target="_blank"
                           rel="noopener"
                           class="ca-btn ca-btn-light">
                            <i class="bi bi-printer"></i>
                            พิมพ์รายงาน
                        </a>
                    @endif

                    <a href="{{ route('client.show', $client->id) }}"
                       class="ca-btn ca-btn-outline">
                        <i class="bi bi-arrow-left"></i>
                        กลับหน้าข้อมูล
                    </a>
                </div>
            </div>
        </header>

        @if($errors->any())
            <div class="ca-validation-alert" role="alert">
                <strong>
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    กรุณาตรวจสอบตัวกรอง
                </strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="ca-filter-card" aria-labelledby="ca-filter-title">
            <div class="ca-filter-head">
                <h2 class="ca-filter-title" id="ca-filter-title">
                    <i class="bi bi-funnel me-1"></i>
                    ตัวกรองรายการ
                </h2>

                <span class="ca-filter-summary">
                    พบทั้งหมด {{ number_format($activities->total()) }} รายการ
                </span>
            </div>

            <form method="GET"
                  action="{{ route('case-activities.index', $client->id) }}"
                  id="case-activity-filter-form">
                <div class="row g-3 align-items-end">

                    <div class="col-lg-3 col-md-6">
                        <label for="module" class="ca-label">ประเภทข้อมูล</label>
                        <select name="module"
                                id="module"
                                class="form-select">
                            <option value="">ทั้งหมด</option>

                            @foreach($modules as $module)
                                <option value="{{ $module }}"
                                    @selected(request('module') === $module)>
                                    {{ $moduleLabels[$module] ?? $module }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label for="type" class="ca-label">ระดับเหตุการณ์</label>
                        <select name="type"
                                id="type"
                                class="form-select">
                            <option value="">ทั้งหมด</option>
                            <option value="info" @selected(request('type') === 'info')>ทั่วไป</option>
                            <option value="success" @selected(request('type') === 'success')>สำเร็จ</option>
                            <option value="warning" @selected(request('type') === 'warning')>เฝ้าระวัง</option>
                            <option value="danger" @selected(request('type') === 'danger')>เร่งด่วน</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label for="date_from" class="ca-label">จากวันที่</label>
                        <input type="date"
                               name="date_from"
                               id="date_from"
                               value="{{ request('date_from') }}"
                               max="{{ $today }}"
                               class="form-control">
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label for="date_to" class="ca-label">ถึงวันที่</label>
                        <input type="date"
                               name="date_to"
                               id="date_to"
                               value="{{ request('date_to') }}"
                               max="{{ $today }}"
                               class="form-control">
                    </div>

                    <div class="col-lg-3">
                        <div class="ca-filter-actions">
                            <button type="submit"
                                    class="btn btn-primary"
                                    id="case-activity-search-btn">
                                <i class="bi bi-search"></i>
                                <span>ค้นหา</span>
                            </button>

                            @if($hasFilters)
                                <a href="{{ route('case-activities.index', $client->id) }}"
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                    ล้างตัวกรอง
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            </form>
        </section>

        @if($activities->count() > 0)
            <section class="ca-timeline-card" aria-label="รายการความเคลื่อนไหว">
                <div class="ca-timeline">
                    @foreach($activities as $activity)
                        @php
                            $activityType = in_array($activity->type, $allowedTypes, true)
                                ? $activity->type
                                : 'info';

                            $icon = trim((string) ($activity->icon ?: 'bi-clock-history'));
                            $moduleName = $moduleLabels[$activity->module]
                                ?? ($activity->module ?: 'ไม่ระบุหมวด');
                        @endphp

                        <article class="ca-item ca-type-{{ $activityType }}">
                            <div class="ca-dot" aria-hidden="true">
                                <i class="bi {{ $icon }}"></i>
                            </div>

                            <div class="ca-content">
                                <div class="ca-item-head">
                                    <h3 class="ca-item-title">
                                        {{ $activity->title ?: 'ไม่ระบุรายการ' }}
                                    </h3>

                                    <time class="ca-date"
                                          @if($activity->occurred_at)
                                              datetime="{{ $activity->occurred_at->toIso8601String() }}"
                                          @endif>
                                        @if($activity->occurred_at)
                                            {{ $activity->occurred_at->format('d/m/') }}{{ $activity->occurred_at->year + 543 }}
                                            เวลา {{ $activity->occurred_at->format('H:i') }} น.
                                        @else
                                            ไม่ระบุวันที่
                                        @endif
                                    </time>
                                </div>

                                @if(filled($activity->description))
                                    <p class="ca-desc">
                                        {{ $activity->description }}
                                    </p>
                                @endif

                                <div class="ca-meta">
                                    <span class="ca-badge">
                                        <i class="bi bi-folder2-open"></i>
                                        {{ $moduleName }}
                                    </span>

                                    @if($activity->user)
                                        <span class="ca-badge">
                                            <i class="bi bi-person"></i>
                                            {{ $activity->user->name }}
                                        </span>
                                    @endif

                                    @if(filled($activity->url))
                                        <a href="{{ $activity->url }}"
                                           class="ca-badge text-decoration-none">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                            เปิดรายการ
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if($activities->hasPages())
                    <div class="ca-pagination-wrap">
                        {{ $activities->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </section>
        @else
            <section class="ca-empty-card" aria-live="polite">
                <div class="ca-empty-icon">
                    <i class="bi bi-inbox"></i>
                </div>

                <h2 class="ca-empty-title">
                    {{ $hasFilters ? 'ไม่พบข้อมูลตามตัวกรอง' : 'ยังไม่มีประวัติความเคลื่อนไหว' }}
                </h2>

                <p class="ca-empty-text">
                    @if($hasFilters)
                        กรุณาปรับเงื่อนไขการค้นหา หรือล้างตัวกรองเพื่อดูข้อมูลทั้งหมด
                    @else
                        เมื่อมีการบันทึกข้อมูล ระบบจะแสดงรายการในหน้านี้โดยอัตโนมัติ
                    @endif
                </p>
            </section>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('case-activity-filter-form');
    const submitButton = document.getElementById('case-activity-search-btn');
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');

    function syncDateRange() {
        if (!dateFrom || !dateTo) {
            return;
        }

        if (dateFrom.value) {
            dateTo.min = dateFrom.value;

            if (dateTo.value && dateTo.value < dateFrom.value) {
                dateTo.value = dateFrom.value;
            }
        } else {
            dateTo.removeAttribute('min');
        }
    }

    dateFrom?.addEventListener('change', syncDateRange);
    syncDateRange();

    form?.addEventListener('submit', function () {
        if (!submitButton) {
            return;
        }

        submitButton.disabled = true;
        submitButton.innerHTML = `
            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
            <span>กำลังค้นหา...</span>
        `;
    });
});
</script>
@endpush