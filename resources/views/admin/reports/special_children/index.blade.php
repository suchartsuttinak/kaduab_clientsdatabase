@extends('admin.admin_master')

{{-- SPECIAL_CHILDREN_REPORT_V1 --}}
@section('title', 'รายงานเด็กกลุ่มพิเศษ')

@section('admin')
@php
    $hasFilters = filled($filters['search'])
        || filled($filters['house_id'])
        || filled($filters['support_type'])
        || filled($filters['development']);

    $filterOpen = $hasFilters || $errors->any();

    $queryWithoutSupport = request()->except(['page', 'support_type']);
    $allTypesUrl = route('special_children.index', $queryWithoutSupport);
@endphp

<style>
    .special-children-page {
        --sc-ink: #0f172a;
        --sc-text: #334155;
        --sc-muted: #64748b;
        --sc-line: #e2e8f0;
        --sc-line-soft: #edf2f7;
        --sc-soft: #f8fafc;
        --sc-blue: #1d4ed8;
        --sc-blue-soft: #eff6ff;
        --sc-teal: #0f766e;
        --sc-teal-soft: #ecfdf5;
        --sc-amber: #b45309;
        --sc-amber-soft: #fffbeb;
        padding: 18px 0 36px;
        color: var(--sc-ink);
    }

    .special-children-page * { box-sizing: border-box; }

    .sc-shell { max-width: 1480px; margin: 0 auto; }

    .sc-hero,
    .sc-filter,
    .sc-table-card,
    .sc-summary-card,
    .sc-total-card,
    .sc-active-filter,
    .sc-empty {
        border: 1px solid var(--sc-line);
        background: #fff;
        box-shadow: 0 5px 18px rgba(15, 23, 42, .045);
    }

    .sc-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 12px;
        padding: 18px 20px;
        border-radius: 16px;
        background: linear-gradient(135deg, #fff 0%, #f8fbff 100%);
    }

    .sc-hero-main { display: flex; align-items: flex-start; gap: 13px; min-width: 0; }
    .sc-hero-icon {
        width: 46px; height: 46px; flex: 0 0 46px;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid #bfdbfe; border-radius: 13px;
        background: var(--sc-blue-soft); color: var(--sc-blue); font-size: 1.2rem;
    }
    .sc-title { margin: 0; font-size: 1.28rem; font-weight: 800; color: var(--sc-ink); line-height: 1.35; }
    .sc-subtitle { margin: 4px 0 0; color: var(--sc-muted); font-size: .86rem; line-height: 1.65; }
    .sc-actions { display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end; }

    .sc-btn {
        min-height: 38px; padding: .45rem .75rem;
        display: inline-flex; align-items: center; justify-content: center; gap: .42rem;
        border-radius: 9px; font-size: .83rem; font-weight: 700;
        text-decoration: none; white-space: nowrap;
    }
    .sc-btn-outline { border: 1px solid #cbd5e1; background: #fff; color: #334155; }
    .sc-btn-outline:hover { border-color: #94a3b8; background: #f8fafc; color: #0f172a; }
    .sc-btn-primary { border: 1px solid var(--sc-blue); background: var(--sc-blue); color: #fff; }
    .sc-btn-primary:hover { background: #1e40af; color: #fff; }

    .sc-overview {
        display: grid;
        grid-template-columns: 220px minmax(0, 1fr);
        gap: 12px;
        margin-bottom: 12px;
    }

    .sc-total-card {
        border-radius: 14px;
        padding: 15px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }
    .sc-total-label { color: var(--sc-muted); font-size: .78rem; font-weight: 700; }
    .sc-total-value { margin-top: 2px; font-size: 1.55rem; font-weight: 800; line-height: 1; color: var(--sc-ink); }
    .sc-total-unit { margin-left: 4px; font-size: .78rem; font-weight: 700; color: var(--sc-muted); }
    .sc-total-icon {
        width: 40px; height: 40px; flex: 0 0 40px; display: inline-flex;
        align-items: center; justify-content: center; border-radius: 11px;
        background: var(--sc-teal-soft); color: var(--sc-teal); font-size: 1.05rem;
    }

    .sc-context-card {
        border: 1px solid var(--sc-line); border-radius: 14px; background: #fff;
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        padding: 13px 15px;
    }
    .sc-context-title { font-size: .86rem; font-weight: 800; color: var(--sc-text); }
    .sc-context-text { margin-top: 3px; color: var(--sc-muted); font-size: .77rem; line-height: 1.5; }
    .sc-filter-toggle { border: 1px solid #cbd5e1; background: #fff; color: #334155; }
    .sc-filter-toggle[aria-expanded="true"] { border-color: #93c5fd; background: var(--sc-blue-soft); color: var(--sc-blue); }

    .sc-summary-section { margin-bottom: 12px; }
    .sc-section-label { margin: 0 0 7px; color: #475569; font-size: .78rem; font-weight: 800; letter-spacing: .01em; }
    .sc-summary-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 8px;
    }
    .sc-summary-card {
        min-width: 0; padding: 12px; border-radius: 12px; color: inherit;
        text-decoration: none; transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease;
    }
    .sc-summary-card:hover { transform: translateY(-1px); border-color: #bfdbfe; box-shadow: 0 7px 18px rgba(29, 78, 216, .08); color: inherit; }
    .sc-summary-label { min-height: 34px; color: var(--sc-muted); font-size: .72rem; font-weight: 700; line-height: 1.45; }
    .sc-summary-count { margin-top: 7px; color: var(--sc-ink); font-size: 1.24rem; font-weight: 800; line-height: 1; }
    .sc-summary-count span { margin-left: 3px; font-size: .7rem; font-weight: 700; color: var(--sc-muted); }

    .sc-active-filter {
        margin-bottom: 12px; padding: 11px 13px; border-radius: 12px;
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
        background: #f8fbff; border-color: #bfdbfe;
    }
    .sc-active-filter strong { color: #1e40af; }
    .sc-active-filter small { color: var(--sc-muted); }

    .sc-filter { margin-bottom: 12px; padding: 14px; border-radius: 14px; }
    .sc-filter-grid {
        display: grid;
        grid-template-columns: minmax(190px, 1.25fr) minmax(160px, .9fr) minmax(240px, 1.35fr) minmax(150px, .75fr) minmax(110px, .55fr);
        gap: 10px;
        align-items: end;
    }
    .sc-field label { display: block; margin-bottom: 5px; color: #475569; font-size: .75rem; font-weight: 800; }
    .sc-control {
        width: 100%; min-height: 39px; border: 1px solid #cbd5e1; border-radius: 8px;
        background: #fff; color: var(--sc-text); font-size: .83rem; box-shadow: none !important;
    }
    .sc-control:focus { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(37, 99, 235, .08) !important; }
    .sc-filter-actions { grid-column: 1 / -1; display: flex; justify-content: flex-end; flex-wrap: wrap; gap: 7px; padding-top: 2px; }

    .sc-table-card { overflow: hidden; border-radius: 14px; }
    .sc-table-head {
        min-height: 48px; padding: 10px 13px; display: flex; align-items: center;
        justify-content: space-between; gap: 12px; border-bottom: 1px solid var(--sc-line-soft);
    }
    .sc-table-title { margin: 0; color: var(--sc-ink); font-size: .9rem; font-weight: 800; }
    .sc-table-meta { color: var(--sc-muted); font-size: .76rem; white-space: nowrap; }
    .sc-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .sc-table { width: 100%; min-width: 1180px; margin: 0; border-collapse: collapse; }
    .sc-table th {
        padding: 10px 11px; border-bottom: 1px solid var(--sc-line);
        background: #f8fafc; color: #475569; font-size: .74rem; font-weight: 800; white-space: nowrap;
    }
    .sc-table td { padding: 11px; border-bottom: 1px solid var(--sc-line-soft); color: var(--sc-text); font-size: .8rem; vertical-align: top; line-height: 1.55; }
    .sc-table tbody tr:hover { background: #fbfdff; }
    .sc-person-link { color: #1e40af; font-weight: 800; text-decoration: none; }
    .sc-person-link:hover { text-decoration: underline; }
    .sc-register { display: block; margin-top: 2px; color: var(--sc-muted); font-size: .71rem; }
    .sc-badge {
        display: inline-flex; align-items: center; gap: 4px; padding: 3px 7px;
        border-radius: 999px; font-size: .69rem; font-weight: 800; line-height: 1.25;
    }
    .sc-badge-ok { background: #ecfdf5; color: #047857; border: 1px solid #bbf7d0; }
    .sc-badge-watch { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
    .sc-detail { margin-top: 5px; color: #475569; }
    .sc-support-main { font-weight: 800; color: #334155; }
    .sc-support-sub { margin-top: 3px; color: var(--sc-muted); font-size: .72rem; }
    .sc-house { white-space: nowrap; font-weight: 700; color: #334155; }
    .sc-date { white-space: nowrap; color: var(--sc-muted); }
    .sc-open {
        display: inline-flex; align-items: center; gap: 4px; padding: 5px 8px;
        border: 1px solid #bfdbfe; border-radius: 8px; background: #fff; color: #1d4ed8;
        text-decoration: none; font-size: .72rem; font-weight: 800; white-space: nowrap;
    }
    .sc-open:hover { background: var(--sc-blue-soft); color: #1e40af; }

    .sc-empty { margin-top: 0; padding: 36px 18px; border-radius: 14px; text-align: center; }
    .sc-empty-icon {
        width: 52px; height: 52px; margin: 0 auto 10px; display: inline-flex;
        align-items: center; justify-content: center; border-radius: 15px;
        background: #f1f5f9; color: #64748b; font-size: 1.3rem;
    }
    .sc-empty h3 { margin: 0; color: var(--sc-ink); font-size: 1rem; font-weight: 800; }
    .sc-empty p { margin: 5px 0 0; color: var(--sc-muted); font-size: .8rem; }

    .sc-pagination { padding: 10px 13px; }
    .sc-pagination .pagination { margin-bottom: 0; }

    .sc-warning {
        margin: 0 0 12px; padding: 9px 11px; border: 1px solid #fde68a; border-radius: 10px;
        background: var(--sc-amber-soft); color: #92400e; font-size: .76rem;
    }

    @media (max-width: 1199.98px) {
        .sc-summary-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .sc-filter-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 767.98px) {
        .special-children-page { padding-top: 10px; }
        .sc-hero { align-items: flex-start; flex-direction: column; }
        .sc-actions, .sc-actions .sc-btn { width: 100%; }
        .sc-overview { grid-template-columns: 1fr; }
        .sc-summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .sc-filter-grid { grid-template-columns: 1fr; }
        .sc-filter-actions { grid-column: auto; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .sc-filter-actions .sc-btn { width: 100%; }
        .sc-active-filter { align-items: flex-start; flex-direction: column; }
    }

    @media print {
        .app-sidebar-menu,
        .navbar-custom,
        .topbar-menu,
        .sc-no-print { display: none !important; }
        .content-page { margin-left: 0 !important; }
        .special-children-page { padding: 0 !important; }
        .sc-shell { max-width: none !important; }
        .sc-hero,
        .sc-table-card,
        .sc-total-card,
        .sc-summary-card,
        .sc-active-filter { box-shadow: none !important; }
        .sc-table { min-width: 100% !important; font-size: 10px; }
        .sc-table th, .sc-table td { padding: 6px !important; }
        .sc-pagination { display: none !important; }
        a { color: inherit !important; text-decoration: none !important; }
    }
</style>

<div class="container-fluid special-children-page">
    <div class="sc-shell">
        <section class="sc-hero">
            <div class="sc-hero-main">
                <span class="sc-hero-icon" aria-hidden="true"><i class="bi bi-person-hearts"></i></span>
                <div>
                    <h1 class="sc-title">เด็กกลุ่มพิเศษ</h1>
                    <p class="sc-subtitle">
                        ภาพรวมจากผลตรวจสุขภาพล่าสุดของเด็กแต่ละคน เพื่อให้เห็นจำนวน รายชื่อ บ้านพัก
                        พัฒนาการ และประเภทการสนับสนุนที่ต้องติดตามได้ในหน้าเดียว
                    </p>
                </div>
            </div>

            <div class="sc-actions sc-no-print">
                <a href="{{ route('client.show') }}" class="sc-btn sc-btn-outline">
                    <i class="bi bi-people"></i> ผู้รับบริการ
                </a>
                @if($canPrint && $records->total() > 0)
                    <button type="button" class="sc-btn sc-btn-primary" onclick="window.print()">
                        <i class="bi bi-printer"></i> พิมพ์รายงาน
                    </button>
                @endif
            </div>
        </section>

        <section class="sc-overview">
            <div class="sc-total-card">
                <div>
                    <div class="sc-total-label">ผลลัพธ์เด็กกลุ่มพิเศษ</div>
                    <div class="sc-total-value">
                        {{ number_format($records->total()) }}<span class="sc-total-unit">คน</span>
                    </div>
                </div>
                <span class="sc-total-icon"><i class="bi bi-people-fill"></i></span>
            </div>

            <div class="sc-context-card sc-no-print">
                <div>
                    <div class="sc-context-title">ตัวกรองข้อมูล</div>
                    <div class="sc-context-text">ค้นหาชื่อหรือเลขทะเบียน เลือกบ้านพัก ประเภทการสนับสนุน และผลพัฒนาการ</div>
                </div>
                <button class="sc-btn sc-filter-toggle"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#specialChildrenFilter"
                        aria-controls="specialChildrenFilter"
                        aria-expanded="{{ $filterOpen ? 'true' : 'false' }}">
                    <i class="bi bi-funnel"></i>
                    {{ $filterOpen ? 'ซ่อนตัวกรอง' : 'ค้นหา / กรองข้อมูล' }}
                </button>
            </div>
        </section>

        @if(blank($filters['support_type']))
            <section class="sc-summary-section">
                <h2 class="sc-section-label">สรุปแยกตามประเภทการสนับสนุน</h2>
                <div class="sc-summary-grid">
                    @foreach($supportSummary as $item)
                        @php
                            $quickQuery = array_merge($queryWithoutSupport, ['support_type' => $item['type']]);
                        @endphp
                        <a href="{{ route('special_children.index', $quickQuery) }}" class="sc-summary-card sc-no-print" title="กรองเฉพาะ {{ $item['label'] }}">
                            <div class="sc-summary-label">{{ $item['label'] }}</div>
                            <div class="sc-summary-count">{{ number_format($item['count']) }}<span>คน</span></div>
                        </a>
                    @endforeach
                </div>
            </section>
        @else
            <div class="sc-active-filter sc-no-print">
                <div>
                    <strong><i class="bi bi-funnel-fill me-1"></i>{{ $selectedSupportLabel }}</strong>
                    <small class="d-block mt-1">พบ {{ number_format($records->total()) }} คน จากทั้งหมด {{ number_format($summaryTotal) }} คนตามเงื่อนไขอื่น</small>
                </div>
                <a href="{{ $allTypesUrl }}" class="sc-btn sc-btn-outline">
                    <i class="bi bi-grid"></i> ดูทุกประเภท
                </a>
            </div>
        @endif

        @if($unclassifiedCount > 0)
            <div class="sc-warning">
                <i class="bi bi-exclamation-triangle me-1"></i>
                พบข้อมูลเดิม {{ number_format($unclassifiedCount) }} คนที่ถูกระบุเป็นเด็กกลุ่มพิเศษ แต่ยังไม่มีประเภทการสนับสนุน
                ควรเปิดข้อมูลตรวจสุขภาพของเด็กเพื่อปรับปรุงให้ครบถ้วน
            </div>
        @endif

        <div class="collapse {{ $filterOpen ? 'show' : '' }} sc-no-print" id="specialChildrenFilter">
            <form method="GET" action="{{ route('special_children.index') }}" class="sc-filter">
                <div class="sc-filter-grid">
                    <div class="sc-field">
                        <label for="sc_search">ชื่อ / นามสกุล / ชื่อเล่น / เลขทะเบียน</label>
                        <input id="sc_search" type="search" name="search" value="{{ $filters['search'] }}"
                               class="form-control sc-control" placeholder="พิมพ์คำค้นหา">
                    </div>

                    <div class="sc-field">
                        <label for="sc_house">บ้านพัก</label>
                        <select id="sc_house" name="house_id" class="form-select sc-control">
                            <option value="">ทุกบ้าน</option>
                            @foreach($houses as $house)
                                <option value="{{ $house->id }}" @selected((string) $filters['house_id'] === (string) $house->id)>
                                    {{ $house->house_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sc-field">
                        <label for="sc_support">ประเภทการสนับสนุน</label>
                        <select id="sc_support" name="support_type" class="form-select sc-control">
                            <option value="">ทุกประเภท</option>
                            @foreach($supportTypes as $value => $label)
                                <option value="{{ $value }}" @selected($filters['support_type'] === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sc-field">
                        <label for="sc_development">พัฒนาการ</label>
                        <select id="sc_development" name="development" class="form-select sc-control">
                            <option value="">ทุกผลการประเมิน</option>
                            <option value="สมวัย" @selected($filters['development'] === 'สมวัย')>สมวัย</option>
                            <option value="ไม่สมวัย" @selected($filters['development'] === 'ไม่สมวัย')>ไม่สมวัย</option>
                        </select>
                    </div>

                    <div class="sc-field">
                        <label for="sc_per_page">แสดงต่อหน้า</label>
                        <select id="sc_per_page" name="per_page" class="form-select sc-control">
                            @foreach([10, 25, 50, 100] as $size)
                                <option value="{{ $size }}" @selected((int) $filters['per_page'] === $size)>{{ $size }} คน</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sc-filter-actions">
                        <a href="{{ route('special_children.index') }}" class="sc-btn sc-btn-outline">
                            <i class="bi bi-arrow-counterclockwise"></i> ล้างตัวกรอง
                        </a>
                        <button type="submit" class="sc-btn sc-btn-primary">
                            <i class="bi bi-search"></i> ค้นหา
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 px-3 sc-no-print" role="alert">
                <i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}
            </div>
        @endif

        @if($records->isNotEmpty())
            <section class="sc-table-card">
                <div class="sc-table-head">
                    <h2 class="sc-table-title"><i class="bi bi-list-ul me-1"></i>รายชื่อเด็กกลุ่มพิเศษ</h2>
                    <div class="sc-table-meta">{{ number_format($records->total()) }} คน</div>
                </div>

                <div class="sc-table-wrap">
                    <table class="sc-table">
                        <thead>
                            <tr>
                                <th style="width:54px;" class="text-center">ลำดับ</th>
                                <th style="min-width:190px;">ชื่อ-สกุล</th>
                                <th style="width:85px;">อายุ</th>
                                <th style="min-width:250px;">รายละเอียดพัฒนาการ</th>
                                <th style="min-width:270px;">ประเภทการสนับสนุน</th>
                                <th style="min-width:130px;">อยู่ที่บ้าน</th>
                                <th style="width:120px;">ประเมินล่าสุด</th>
                                <th style="width:115px;" class="text-center sc-no-print">เปิดข้อมูล</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                @php
                                    $client = $record->client;
                                    $age = $client?->age;
                                    $isDevelopmentOk = $record->development === 'สมวัย';
                                    $supportLabel = $supportTypes[$record->special_support_type] ?? ($record->special_support_type ?: 'ยังไม่ระบุประเภท');
                                    $assessmentDate = filled($record->assessor_date)
                                        ? \Carbon\Carbon::parse($record->assessor_date)->locale('th')->translatedFormat('d M ') . (\Carbon\Carbon::parse($record->assessor_date)->year + 543)
                                        : '-';
                                @endphp
                                <tr>
                                    <td class="text-center">{{ number_format(($records->firstItem() ?? 1) + $loop->index) }}</td>
                                    <td>
                                        @if($client)
                                            <a href="{{ route('admin.index', $client->id) }}" class="sc-person-link">
                                                {{ $client->full_name ?: trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')) }}
                                            </a>
                                            <span class="sc-register">เลขทะเบียน: {{ $client->register_number ?: '-' }}</span>
                                        @else
                                            <span class="text-muted">ไม่พบข้อมูลผู้รับบริการ</span>
                                        @endif
                                    </td>
                                    <td>{{ $age !== null ? number_format($age) . ' ปี' : '-' }}</td>
                                    <td>
                                        <span class="sc-badge {{ $isDevelopmentOk ? 'sc-badge-ok' : 'sc-badge-watch' }}">
                                            <i class="bi {{ $isDevelopmentOk ? 'bi-check-circle' : 'bi-exclamation-circle' }}"></i>
                                            {{ $record->development ?: 'ไม่ระบุ' }}
                                        </span>
                                        <div class="sc-detail">
                                            {{ filled($record->detail)
                                                ? $record->detail
                                                : ($isDevelopmentOk ? 'พัฒนาการเป็นไปตามวัย' : 'ยังไม่มีรายละเอียดเพิ่มเติม') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="sc-support-main">{{ $supportLabel }}</div>
                                        @if($record->special_support_type === 'อื่น ๆ' && filled($record->special_support_other))
                                            <div class="sc-support-sub">{{ $record->special_support_other }}</div>
                                        @endif
                                    </td>
                                    <td><span class="sc-house">{{ $client?->house?->house_name ?: 'ไม่ระบุบ้าน' }}</span></td>
                                    <td><span class="sc-date">{{ $assessmentDate }}</span></td>
                                    <td class="text-center sc-no-print">
                                        @if($client)
                                            <a href="{{ route('admin.index', $client->id) }}" class="sc-open" title="เปิดข้อมูล {{ $client->full_name }}">
                                                <i class="bi bi-box-arrow-up-right"></i> ดูเด็ก
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($records->hasPages())
                    <div class="sc-pagination sc-no-print">
                        {{ $records->links() }}
                    </div>
                @endif
            </section>
        @else
            <section class="sc-empty">
                <span class="sc-empty-icon"><i class="bi bi-search"></i></span>
                <h3>ไม่พบเด็กกลุ่มพิเศษตามเงื่อนไข</h3>
                <p>ลองล้างตัวกรอง หรือเลือกบ้านพักและประเภทการสนับสนุนใหม่</p>
            </section>
        @endif
    </div>
</div>
@endsection