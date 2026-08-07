@php
    $hasAnySessions = $hasAnySessions ?? $sessions->isNotEmpty();
    $hasDateFilter = request()->filled('from') || request()->filled('to');
    $hasFilterErrors = isset($errors) && $errors->getBag('filter')->any();
    $showFilterPanel = $hasDateFilter || $hasFilterErrors;
    $clientName = $client->fullname ?? $client->full_name ?? '-';
@endphp

<style>
    .help-page .hp-main-header,
    .help-page .hp-filter-card,
    .help-page .hp-empty-card {
        background: #fff;
        border: 1px solid #dbe3ef;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .045);
    }

    .help-page .hp-main-header {
        min-height: 90px;
        padding: 1.1rem 1.25rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .help-page .hp-header-title {
        display: flex;
        align-items: center;
        gap: .9rem;
        min-width: 0;
        flex: 1 1 440px;
    }

    .help-page .hp-header-icon {
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 1.15rem;
    }

    .help-page .hp-header-text {
        min-width: 0;
    }

    .help-page .hp-header-text h1 {
        margin: 0;
        color: #0f172a;
        font-size: clamp(1.25rem, 1.6vw, 1.5rem);
        font-weight: 800;
        line-height: 1.35;
        letter-spacing: -0.01em;
    }

    .help-page .hp-header-text p {
        margin: .25rem 0 0;
        color: #64748b;
        font-size: clamp(.92rem, 1vw, 1rem);
        line-height: 1.45;
    }

    .help-page .hp-header-text strong {
        color: #0f172a;
        font-weight: 800;
    }

    .help-page .hp-header-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .65rem;
        flex-wrap: wrap;
    }

    .help-page .hp-btn,
    .help-page .hp-filter-btn {
        min-height: 42px;
        padding: .62rem 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        border-radius: 12px;
        font-weight: 750;
        text-decoration: none;
        white-space: nowrap;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
    }

    .help-page .hp-btn-filter {
        color: #1d4ed8;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
    }

    .help-page .hp-btn-filter:hover,
    .help-page .hp-btn-filter:focus,
    .help-page .hp-btn-filter[aria-expanded="true"] {
        color: #1e40af;
        background: #dbeafe;
        border-color: #93c5fd;
        transform: translateY(-1px);
    }

    .help-page .hp-btn-filter .hp-filter-chevron {
        transition: transform .2s ease;
    }

    .help-page .hp-btn-filter[aria-expanded="true"] .hp-filter-chevron {
        transform: rotate(180deg);
    }

    .help-page .hp-btn-primary {
        color: #fff;
        border: 1px solid #1d4ed8;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        box-shadow: 0 7px 16px rgba(37, 99, 235, .2);
    }

    .help-page .hp-btn-primary:hover,
    .help-page .hp-btn-primary:focus {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(37, 99, 235, .26);
    }

    .help-page .hp-btn-back {
        color: #7c3aed;
        background: #fff;
        border: 1px solid #8b5cf6;
    }

    .help-page .hp-btn-back:hover,
    .help-page .hp-btn-back:focus {
        color: #6d28d9;
        background: #faf5ff;
        transform: translateY(-1px);
    }

    .help-page .hp-filter-collapse {
        margin: 1rem 0;
    }

    .help-page .hp-filter-collapse .hp-filter-card {
        margin: 0;
        padding: 1rem 1.1rem;
    }

    .help-page .hp-filter-head {
        margin-bottom: .85rem;
    }

    .help-page .hp-filter-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
    }

    .help-page .hp-filter-title i {
        color: #2563eb;
    }

    .help-page .hp-filter-subtitle,
    .help-page .hp-filter-note {
        margin: .25rem 0 0;
        color: #64748b;
        font-size: .9rem;
        line-height: 1.55;
    }

    .help-page .hp-filter-form {
        display: grid;
        grid-template-columns: minmax(180px, 220px) minmax(180px, 220px) minmax(280px, 1fr);
        gap: .8rem;
        align-items: end;
    }

    .help-page .hp-filter-group {
        display: flex;
        flex-direction: column;
        gap: .4rem;
    }

    .help-page .hp-filter-label {
        margin: 0;
        color: #334155;
        font-size: .9rem;
        font-weight: 700;
    }

    .help-page .hp-filter-input {
        height: 42px;
        padding: 0 .8rem;
        border: 1px solid #cbd5e1;
        border-radius: 11px;
        color: #0f172a;
        background: #fff;
    }

    .help-page .hp-filter-input:focus {
        border-color: #93b4ff;
        outline: 0;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .1);
    }

    .help-page .hp-filter-actions {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: .55rem;
        flex-wrap: wrap;
    }

    .help-page .hp-filter-btn-search {
        color: #fff;
        border: 1px solid #1d4ed8;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
    }

    .help-page .hp-filter-btn-outline {
        color: #475569;
        background: #fff;
        border: 1px solid #cbd5e1;
    }

    .help-page .hp-filter-btn-outline:hover,
    .help-page .hp-filter-btn-outline:focus {
        color: #0f172a;
        background: #f8fafc;
        border-color: #94a3b8;
    }

    .help-page .hp-filter-error {
        color: #dc2626;
        font-size: .82rem;
    }

    .help-page .hp-empty-card {
        min-height: 320px;
        padding: 2.5rem 1.25rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .help-page .hp-empty-icon {
        width: 82px;
        height: 82px;
        margin-bottom: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #bfdbfe;
        border-radius: 50%;
        background: #eff6ff;
        color: #2563eb;
        font-size: 1.75rem;
    }

    .help-page .hp-empty-card h2 {
        margin: 0;
        color: #0f172a;
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.45;
    }

    .help-page .hp-empty-card p {
        max-width: 660px;
        margin: .55rem auto 1.2rem;
        color: #64748b;
        font-size: .92rem;
        line-height: 1.65;
    }

    @media (max-width: 991.98px) {
        .help-page .hp-filter-form {
            grid-template-columns: 1fr 1fr;
        }

        .help-page .hp-filter-actions {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 767.98px) {
        .help-page .hp-main-header {
            align-items: stretch;
            padding: 1rem;
        }

        .help-page .hp-header-title,
        .help-page .hp-header-actions {
            width: 100%;
        }

        .help-page .hp-header-actions > * {
            flex: 1 1 calc(50% - .35rem);
        }
    }

    @media (max-width: 575.98px) {
        .help-page .hp-header-text h1 { font-size: 1.12rem; }
        .help-page .hp-header-text p { font-size: .9rem; }
        .help-page .hp-header-actions,
        .help-page .hp-filter-actions { flex-direction: column; }
        .help-page .hp-header-actions > *,
        .help-page .hp-filter-actions > * { width: 100%; flex: 1 1 auto; }
        .help-page .hp-filter-form { grid-template-columns: 1fr; }
        .help-page .hp-filter-actions { grid-column: auto; }
        .help-page .hp-empty-card { min-height: 280px; padding: 2rem .9rem; }
    }
</style>

<header class="hp-main-header">
    <div class="hp-header-title">
        <span class="hp-header-icon" aria-hidden="true">
            <i class="bi bi-bag-heart-fill"></i>
        </span>

        <div class="hp-header-text">
            <h1>การช่วยเหลือสิ่งของและเครื่องใช้</h1>
            <p>
                ผู้รับบริการ: <strong>{{ $clientName }}</strong>
                @if(!empty($client->age))
                    <span class="mx-1">•</span> อายุ <strong>{{ $client->age }} ปี</strong>
                @endif
            </p>
        </div>
    </div>

    <div class="hp-header-actions">
        @if($hasAnySessions)
            <button type="button"
                    class="hp-btn hp-btn-filter"
                    data-bs-toggle="collapse"
                    data-bs-target="#helpSearchPanel"
                    aria-expanded="{{ $showFilterPanel ? 'true' : 'false' }}"
                    aria-controls="helpSearchPanel"
                    data-help-filter-toggle>
                <i class="bi bi-search"></i>
                <span data-help-filter-label>{{ $showFilterPanel ? 'ซ่อนค้นหา' : 'ค้นหา' }}</span>
                <i class="bi bi-chevron-down hp-filter-chevron" aria-hidden="true"></i>
            </button>

            <a href="{{ route('help_sessions.create', $client->id) }}"
               class="hp-btn hp-btn-primary">
                <i class="bi bi-plus-circle"></i>
                <span>เพิ่มการช่วยเหลือ</span>
            </a>
        @endif

        <a href="{{ route('admin.index', $client->id) }}"
           class="hp-btn hp-btn-back">
            <i class="bi bi-arrow-left-circle"></i>
            <span>กลับ</span>
        </a>
    </div>
</header>

@if($hasAnySessions)
    @include('frontend.client.helping.partials.profile-card')

    <div id="helpSearchPanel"
         class="collapse hp-filter-collapse {{ $showFilterPanel ? 'show' : '' }}">
        <section class="hp-filter-card">
        <div class="hp-filter-head">
            <div class="hp-filter-title">
                <i class="bi bi-funnel-fill"></i>
                <span>ค้นหาและออกรายงานตามช่วงวันที่</span>
            </div>
            <p class="hp-filter-subtitle">เลือกช่วงวันที่เพื่อแสดงข้อมูลเฉพาะช่วงที่ต้องการ</p>
        </div>

        <form method="GET"
              action="{{ route('help_sessions.show', $client->id) }}"
              class="hp-filter-form">
            <div class="hp-filter-group">
                <label for="from" class="hp-filter-label">ตั้งแต่วันที่</label>
                <input type="date"
                       id="from"
                       name="from"
                       class="hp-filter-input @error('from', 'filter') is-invalid @enderror"
                       value="{{ request('from') }}"
                       max="{{ now('Asia/Bangkok')->toDateString() }}">
                @error('from', 'filter')
                    <div class="hp-filter-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="hp-filter-group">
                <label for="to" class="hp-filter-label">ถึงวันที่</label>
                <input type="date"
                       id="to"
                       name="to"
                       class="hp-filter-input @error('to', 'filter') is-invalid @enderror"
                       value="{{ request('to') }}"
                       max="{{ now('Asia/Bangkok')->toDateString() }}">
                @error('to', 'filter')
                    <div class="hp-filter-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="hp-filter-actions">
                <button type="submit" class="hp-filter-btn hp-filter-btn-search">
                    <i class="bi bi-search"></i>
                    <span>ค้นหา</span>
                </button>

                <a href="{{ route('help_sessions.show', $client->id) }}"
                   class="hp-filter-btn hp-filter-btn-outline">
                    <i class="bi bi-arrow-clockwise"></i>
                    <span>ดูทั้งหมด</span>
                </a>

                @if($sessions->isNotEmpty())
                    <a href="{{ route('help_sessions.report_range', [
                        'client' => $client->id,
                        'from' => request('from'),
                        'to' => request('to')
                    ]) }}"
                       class="hp-filter-btn hp-filter-btn-outline">
                        <i class="bi bi-printer"></i>
                        <span>รายงานตามช่วงวันที่</span>
                    </a>
                @endif
            </div>
        </form>

        <div class="hp-filter-note">
            ไม่เลือกวันที่ ระบบจะแสดงข้อมูลทั้งหมดโดยอัตโนมัติ
        </div>
        </section>
    </div>
@else
    <section class="hp-empty-card" role="status">
        <div class="hp-empty-icon" aria-hidden="true">
            <i class="bi bi-box2-heart"></i>
        </div>

        <h2>ยังไม่มีข้อมูลการช่วยเหลือ</h2>
        <p>
            เริ่มต้นบันทึกรายการสิ่งของ เครื่องใช้ หรือค่าใช้จ่ายที่ให้ความช่วยเหลือแก่ผู้รับบริการรายนี้
        </p>

        <a href="{{ route('help_sessions.create', $client->id) }}"
           class="hp-btn hp-btn-primary">
            <i class="bi bi-plus-circle"></i>
            <span>เพิ่มข้อมูลการช่วยเหลือครั้งแรก</span>
        </a>
    </section>
@endif
