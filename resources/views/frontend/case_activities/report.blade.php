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

    $typeLabels = [
        'info'    => 'ทั่วไป',
        'success' => 'สำเร็จ',
        'warning' => 'เฝ้าระวัง',
        'danger'  => 'เร่งด่วน',
    ];

    $allowedTypes = array_keys($typeLabels);

    $clientDisplayName = trim((string) (
        $client->fullname
        ?? $client->full_name
        ?? (($client->first_name ?? '') . ' ' . ($client->last_name ?? ''))
    ));

    if ($clientDisplayName === '') {
        $clientDisplayName = '-';
    }

    $printedAt = now();
    $hasFilters = request()->filled('module')
        || request()->filled('type')
        || request()->filled('date_from')
        || request()->filled('date_to');
@endphp

@push('styles')
<style>
    .ca-report-page {
        min-height: 100vh;
        padding: .5rem 0 1.5rem;
        background: #eef2f7;
        font-family: "TH Sarabun New", "Sarabun", "Kanit", sans-serif;
    }

    .ca-report-toolbar {
        width: min(210mm, 100%);
        margin: 0 auto .75rem;
        display: flex;
        justify-content: space-between;
        gap: .6rem;
    }

    .ca-report-btn {
        min-height: 40px;
        padding: .55rem .9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        border: 1px solid transparent;
        border-radius: 10px;
        font-size: .86rem;
        font-weight: 700;
        text-decoration: none;
    }

    .ca-report-btn-primary {
        color: #fff;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        box-shadow: 0 7px 16px rgba(37, 99, 235, .2);
    }

    .ca-report-btn-primary:hover,
    .ca-report-btn-primary:focus {
        color: #fff;
    }

    .ca-report-btn-light {
        color: #334155;
        background: #fff;
        border-color: #cbd5e1;
    }

    .ca-report-sheet {
        width: min(210mm, 100%);
        min-height: 297mm;
        margin: 0 auto;
        padding: 11mm 12mm;
        color: #111827;
        background: #fff;
        box-shadow: 0 10px 26px rgba(15, 23, 42, .13);
        font-size: 15px;
        line-height: 1.3;
    }

    .ca-report-head {
        margin-bottom: 8px;
        padding-bottom: 7px;
        text-align: center;
        border-bottom: 2px solid #1f2937;
    }

    .ca-report-title {
        margin: 0;
        font-size: 21px;
        font-weight: 800;
        line-height: 1.2;
    }

    .ca-report-subtitle {
        margin-top: 3px;
        color: #475569;
        font-size: 14px;
    }

    .ca-client-info {
        margin-top: 8px;
        padding: 7px 9px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 4px 16px;
        background: #f8fafc;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        font-size: 15px;
    }

    .ca-client-info strong {
        color: #111827;
        font-weight: 800;
    }

    .ca-filter-summary {
        margin-top: 7px;
        padding: 6px 9px;
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        background: #f8fafc;
        border: 1px solid #dbe3ec;
        border-radius: 7px;
        font-size: 13.5px;
    }

    .ca-filter-chip {
        padding: 2px 7px;
        color: #334155;
        background: #fff;
        border: 1px solid #dbe3ec;
        border-radius: 999px;
        font-weight: 700;
    }

    .ca-section-title {
        margin: 10px 0 7px;
        padding-bottom: 4px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        color: #0f172a;
        border-bottom: 1px solid #cbd5e1;
        font-size: 16px;
        font-weight: 800;
    }

    .ca-section-count {
        color: #64748b;
        font-size: 13px;
        font-weight: 700;
    }

    .ca-report-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .ca-report-item {
        padding: 6px 8px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-left: 4px solid #2563eb;
        border-radius: 7px;
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .ca-report-item.type-success { border-left-color: #16a34a; }
    .ca-report-item.type-warning { border-left-color: #f59e0b; }
    .ca-report-item.type-danger { border-left-color: #dc2626; }
    .ca-report-item.type-info { border-left-color: #2563eb; }

    .ca-report-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 8px;
        align-items: start;
    }

    .ca-report-item-title {
        margin: 0;
        overflow-wrap: anywhere;
        color: #111827;
        font-size: 15.5px;
        font-weight: 800;
        line-height: 1.25;
    }

    .ca-report-date {
        color: #475569;
        font-size: 13.5px;
        font-weight: 700;
        text-align: right;
        white-space: nowrap;
    }

    .ca-report-desc {
        margin-top: 3px;
        overflow-wrap: anywhere;
        color: #1f2937;
        font-size: 14.5px;
        line-height: 1.3;
    }

    .ca-report-meta {
        margin-top: 4px;
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
        color: #475569;
        font-size: 12.8px;
    }

    .ca-chip {
        padding: 2px 7px;
        color: #3730a3;
        background: #eef2ff;
        border-radius: 999px;
        font-weight: 700;
    }

    .ca-empty {
        padding: 30px 10px;
        color: #6b7280;
        text-align: center;
        font-size: 15px;
    }

    .ca-empty i {
        margin-bottom: 6px;
        display: block;
        color: #94a3b8;
        font-size: 30px;
    }

    .ca-signature {
        margin-top: 22px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 32px;
        text-align: center;
        font-size: 15px;
    }

    .ca-sign-line {
        margin-top: 28px;
        padding-top: 4px;
        border-top: 1px solid #111827;
    }

    @media (max-width: 900px) {
        .ca-report-toolbar,
        .ca-report-sheet {
            width: 100%;
        }

        .ca-report-sheet {
            min-height: auto;
            padding: 16px;
        }

        .ca-client-info,
        .ca-signature,
        .ca-report-row {
            grid-template-columns: 1fr;
        }

        .ca-report-date {
            text-align: left;
            white-space: normal;
        }
    }

    @media print {
        @page {
            size: A4 portrait;
            /* ลดขอบบนจากเดิม 8 มม. เพื่อดันหัวรายงานขึ้น */
            margin: 4mm 8mm 8mm;
        }

        /*
         * ใช้ selector ที่เฉพาะเจาะจงกว่า CSS หลักของ Layout
         * เพื่อยกเลิกพื้นที่ Topbar ที่ body เว้นไว้ในหน้าจอปกติ
         */
        html body,
        body[data-menu-color],
        body[data-sidebar] {
            width: 100% !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            padding-top: 0 !important;
            overflow: visible !important;
            background: #fff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        #appTopbar,
        .app-topbar,
        .topbar-custom,
        .client-sidebar-panel,
        .sidebar-overlay,
        .app-footer,
        .ca-report-toolbar {
            display: none !important;
        }

        #app-layout,
        #app-layout .app-body,
        #app-layout .content-page.main-content,
        #app-layout .content-shell,
        #app-layout .content-shell > .content-scroll-x,
        #app-layout .ca-report-page {
            width: 100% !important;
            min-width: 0 !important;
            max-width: none !important;
            min-height: 0 !important;
            height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            top: auto !important;
            display: block !important;
            overflow: visible !important;
            background: #fff !important;
        }

        #app-layout .ca-report-sheet {
            width: auto !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        #app-layout .ca-report-head {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        .ca-report-item {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .ca-signature {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    }
</style>
@endpush

<div class="ca-report-page">
    <div class="ca-report-toolbar">
        <a href="{{ route(
                'case-activities.index',
                array_merge(
                    ['client' => $client->id],
                    request()->except('page')
                )
            ) }}"
           class="ca-report-btn ca-report-btn-light">
            <i class="bi bi-arrow-left"></i>
            ย้อนกลับ
        </a>

        @if($activities->isNotEmpty())
            <button type="button"
                    onclick="window.print()"
                    class="ca-report-btn ca-report-btn-primary">
                <i class="bi bi-printer"></i>
                พิมพ์รายงาน
            </button>
        @endif
    </div>

    <main class="ca-report-sheet">
        <header class="ca-report-head">
            <h1 class="ca-report-title">
                รายงานประวัติความเคลื่อนไหวของผู้รับบริการ
            </h1>

            <div class="ca-report-subtitle">
                ระบบฐานข้อมูลผู้รับบริการ
            </div>
        </header>

        <section class="ca-client-info">
            <div>
                <strong>ชื่อ - นามสกุล:</strong>
                {{ $clientDisplayName }}
            </div>

            <div>
                <strong>เลขทะเบียน:</strong>
                {{ $client->register_number ?? '-' }}
            </div>

            <div>
                <strong>ชื่อเล่น:</strong>
                {{ $client->nick_name ?? $client->nickname ?? '-' }}
            </div>

            <div>
                <strong>วันที่พิมพ์รายงาน:</strong>
                {{ $printedAt->format('d/m/') }}{{ $printedAt->year + 543 }}
                เวลา {{ $printedAt->format('H:i') }} น.
            </div>
        </section>

        @if($hasFilters)
            <section class="ca-filter-summary" aria-label="เงื่อนไขรายงาน">
                <strong>เงื่อนไข:</strong>

                @if(request()->filled('module'))
                    <span class="ca-filter-chip">
                        ประเภท:
                        {{ $moduleLabels[request('module')] ?? request('module') }}
                    </span>
                @endif

                @if(request()->filled('type'))
                    <span class="ca-filter-chip">
                        ระดับ:
                        {{ $typeLabels[request('type')] ?? request('type') }}
                    </span>
                @endif

                @if(request()->filled('date_from'))
                    <span class="ca-filter-chip">
                        ตั้งแต่:
                        {{ \Carbon\Carbon::parse(request('date_from'))->format('d/m/') }}{{ \Carbon\Carbon::parse(request('date_from'))->year + 543 }}
                    </span>
                @endif

                @if(request()->filled('date_to'))
                    <span class="ca-filter-chip">
                        ถึง:
                        {{ \Carbon\Carbon::parse(request('date_to'))->format('d/m/') }}{{ \Carbon\Carbon::parse(request('date_to'))->year + 543 }}
                    </span>
                @endif
            </section>
        @endif

        <div class="ca-section-title">
            <span>รายการความเคลื่อนไหว</span>
            <span class="ca-section-count">
                ทั้งหมด {{ number_format($activities->count()) }} รายการ
            </span>
        </div>

        @if($activities->isNotEmpty())
            <section class="ca-report-list">
                @foreach($activities as $activity)
                    @php
                        $type = in_array($activity->type, $allowedTypes, true)
                            ? $activity->type
                            : 'info';

                        $moduleName = $moduleLabels[$activity->module]
                            ?? ($activity->module ?: 'ไม่ระบุหมวด');

                        $typeName = $typeLabels[$activity->type]
                            ?? 'ทั่วไป';
                    @endphp

                    <article class="ca-report-item type-{{ $type }}">
                        <div class="ca-report-row">
                            <h2 class="ca-report-item-title">
                                {{ $activity->title ?: 'ไม่ระบุรายการ' }}
                            </h2>

                            <div class="ca-report-date">
                                @if($activity->occurred_at)
                                    {{ $activity->occurred_at->format('d/m/') }}{{ $activity->occurred_at->year + 543 }}
                                    {{ $activity->occurred_at->format('H:i') }} น.
                                @else
                                    ไม่ระบุวันที่
                                @endif
                            </div>
                        </div>

                        @if(filled($activity->description))
                            <div class="ca-report-desc">
                                {{ $activity->description }}
                            </div>
                        @endif

                        <div class="ca-report-meta">
                            <span class="ca-chip">
                                หมวด: {{ $moduleName }}
                            </span>

                            @if($activity->user)
                                <span class="ca-chip">
                                    ผู้บันทึก: {{ $activity->user->name }}
                                </span>
                            @endif

                            <span class="ca-chip">
                                ระดับ: {{ $typeName }}
                            </span>
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="ca-signature">
                <div>
                    <div class="ca-sign-line">ผู้จัดทำรายงาน</div>
                </div>

                <div>
                    <div class="ca-sign-line">ผู้ตรวจสอบ</div>
                </div>
            </section>
        @else
            <div class="ca-empty">
                <i class="bi bi-inbox"></i>
                ไม่พบข้อมูลความเคลื่อนไหวตามเงื่อนไขที่เลือก
            </div>
        @endif
    </main>
</div>
@endsection