@extends('admin_client.admin_client')
@section('content')

<link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">

<style>
    .ff-page {
        --ff-primary: #0f766e;
        --ff-primary-2: #14b8a6;
        --ff-border: #d9e5e7;
        --ff-border-strong: #c9d8da;
        --ff-card: #ffffff;
        --ff-text: #1f2937;
        --ff-muted: #64748b;
        --ff-danger: #dc2626;
        --ff-radius-xl: 20px;
        --ff-radius-lg: 17px;
        --ff-radius-md: 14px;
        --ff-radius-sm: 12px;

        padding: 1rem;
        color: var(--ff-text);
        background: #f5f8f9;
    }

    .ff-shell {
        overflow: hidden;
        border: 1px solid var(--ff-border);
        border-radius: var(--ff-radius-xl);
        background: #ffffff;
        box-shadow: 0 8px 22px rgba(15, 23, 42, .045);
    }

    .ff-alert-wrap {
        padding: 1rem 1rem 0;
    }

    .ff-alert {
        margin-bottom: .75rem;
        border: 1px solid transparent;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .035);
    }

    .ff-body {
        padding: 1rem;
    }

    .ff-hero {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: .25rem .1rem 1rem;
    }

    .ff-hero-title {
        margin: 0;
        color: var(--ff-text);
        font-size: 1.2rem;
        font-weight: 800;
        line-height: 1.25;
    }

    .ff-hero-subtitle {
        margin: .35rem 0 0;
        color: var(--ff-muted);
        font-size: .92rem;
        line-height: 1.55;
    }

    .ff-hero-link {
        color: inherit;
        text-decoration: none;
    }

    .ff-hero-link:hover,
    .ff-hero-link:focus {
        color: inherit;
        text-decoration: none;
    }

    .ff-hero-badge {
        min-height: 42px;
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .58rem .85rem;
        border: 1px solid #dbe4e7;
        border-radius: 12px;
        background: #ffffff;
        color: var(--ff-primary);
        font-size: .88rem;
        font-weight: 700;
        white-space: nowrap;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .035);
        transition:
            transform .16s ease,
            border-color .16s ease,
            background-color .16s ease;
    }

    .ff-hero-link .ff-hero-badge:hover {
        transform: translateY(-1px);
        border-color: #b9c9cc;
        background: #f8fbfb;
    }

    .ff-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
        align-items: start;
    }

    .ff-card {
        min-width: 0;
        overflow: hidden;
        border: 1px solid var(--ff-border);
        border-radius: var(--ff-radius-lg);
        background: var(--ff-card);
        box-shadow: 0 5px 16px rgba(15, 23, 42, .035);
    }

    .ff-card-header {
        display: flex;
        align-items: center;
        gap: .7rem;
        padding: 1rem;
        border-bottom: 1px solid var(--ff-border);
        background: #fbfdfd;
    }

    .ff-card-icon {
        width: 42px;
        height: 42px;
        min-width: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(15, 118, 110, .1);
        border-radius: 12px;
        background: #edf8f6;
        color: var(--ff-primary);
    }

    .ff-card-title {
        margin: 0;
        color: var(--ff-text);
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.3;
    }

    .ff-card-subtitle {
        margin: .22rem 0 0;
        color: var(--ff-muted);
        font-size: .86rem;
        line-height: 1.45;
    }

    .ff-card-body {
        padding: 1rem;
    }

    .ff-form-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: .95rem .9rem;
    }

    .ff-span-12 { grid-column: span 12; }
    .ff-span-8 { grid-column: span 8; }
    .ff-span-6 { grid-column: span 6; }
    .ff-span-4 { grid-column: span 4; }

    .ff-field {
        min-width: 0;
        scroll-margin-top: 90px;
    }

    .ff-label {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        margin-bottom: .45rem;
        color: #334155;
        font-size: .93rem;
        font-weight: 700;
        line-height: 1.35;
    }

    .ff-required {
        color: var(--ff-danger);
        font-weight: 800;
    }

    .ff-label-note {
        color: var(--ff-muted);
        font-size: .8rem;
        font-weight: 500;
    }

    .ff-input,
    .ff-select,
    .ff-textarea {
        min-height: 46px;
        border: 1px solid #d7e3e5;
        border-radius: var(--ff-radius-sm);
        background: #ffffff;
        font-size: .95rem;
        box-shadow: none;
        transition:
            border-color .15s ease,
            box-shadow .15s ease,
            background-color .15s ease;
    }

    .ff-input:focus,
    .ff-select:focus,
    .ff-textarea:focus {
        border-color: rgba(15, 118, 110, .42);
        box-shadow: 0 0 0 .2rem rgba(15, 118, 110, .09);
    }

    .ff-input[readonly] {
        background: #f8fafc;
        color: #475569;
        cursor: default;
    }

    .ff-textarea {
        min-height: 110px;
        resize: vertical;
    }

    .ff-textarea.ff-textarea-sm { min-height: 96px; }
    .ff-textarea.ff-textarea-md { min-height: 124px; }
    .ff-textarea.ff-textarea-lg { min-height: 146px; }

    .ff-inline-radio {
        min-height: 46px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: .75rem 1rem;
        padding: .65rem .9rem;
        border: 1px solid #d7e3e5;
        border-radius: var(--ff-radius-sm);
        background: #ffffff;
    }

    .ff-inline-radio.is-invalid {
        border-color: rgba(220, 38, 38, .5);
    }

    .ff-inline-radio .form-check {
        margin: 0;
    }

    .ff-inline-radio .form-check-label {
        color: var(--ff-text);
        font-size: .93rem;
    }

    .ff-detail-box {
        padding: .9rem;
        border: 1px dashed var(--ff-border-strong);
        border-radius: var(--ff-radius-md);
        background: #f8fbfb;
    }

    .ff-unit {
        display: flex;
        align-items: center;
        gap: .6rem;
    }

    .ff-unit .ff-input {
        min-width: 0;
    }

    .ff-unit .unit-text {
        flex: 0 0 auto;
        color: #4b5563;
        font-size: .88rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .ff-checklist {
        max-height: 360px;
        overflow: auto;
        padding: .9rem;
        border: 1px solid var(--ff-border);
        border-radius: var(--ff-radius-md);
        background: #fafcfc;
        overscroll-behavior: contain;
        scrollbar-gutter: stable;
    }

    .ff-checklist-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .65rem;
    }

    .ff-check-item {
        min-height: 52px;
        display: flex;
        align-items: flex-start;
        gap: .7rem;
        margin: 0;
        padding: .78rem .82rem;
        border: 1px solid #e4ecee;
        border-radius: 11px;
        background: #ffffff;
        cursor: pointer;
        transition:
            border-color .15s ease,
            background-color .15s ease,
            box-shadow .15s ease;
    }

    .ff-check-item:hover {
        border-color: rgba(15, 118, 110, .22);
        background: #fbfefe;
        box-shadow: 0 4px 10px rgba(15, 23, 42, .035);
    }

    .ff-check-item:has(input:checked) {
        border-color: rgba(15, 118, 110, .38);
        background: #f0fdfa;
    }

    .ff-check-item input {
        margin-top: .2rem;
        flex: 0 0 auto;
    }

    .ff-check-item span {
        color: var(--ff-text);
        font-size: .92rem;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    .ff-error {
        display: block;
        margin-top: .38rem;
        color: var(--ff-danger);
        font-size: .82rem;
        line-height: 1.35;
    }

    .ff-input.is-invalid,
    .ff-select.is-invalid,
    .ff-textarea.is-invalid {
        border-color: rgba(220, 38, 38, .5);
        background-image: none;
    }

    .ff-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-top: 1rem;
        padding: 1rem;
        border: 1px solid var(--ff-border);
        border-radius: 15px;
        background: #f8fafc;
    }

    .ff-actions-note {
        color: var(--ff-muted);
        font-size: .88rem;
        line-height: 1.45;
    }

    /* ปุ่มมาตรฐานเดียวกับหน้าอื่นของระบบ */
    .ff-submit {
        min-width: 174px;
        min-height: 46px;
        position: relative;
        overflow: hidden;
        padding: .68rem 1.2rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        border: 1px solid #1d4ed8 !important;
        border-radius: 12px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        font-size: .92rem;
        font-weight: 750;
        line-height: 1.2;
        white-space: nowrap;
        box-shadow: 0 8px 18px rgba(37, 99, 235, .2) !important;
        transition:
            transform .16s ease,
            box-shadow .16s ease,
            background .16s ease;
        -webkit-tap-highlight-color: transparent;
    }

    .ff-submit:hover,
    .ff-submit:focus,
    .ff-submit:focus-visible {
        transform: translateY(-1px);
        border-color: #1e40af !important;
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
        color: #ffffff !important;
        outline: 0;
        box-shadow:
            0 10px 22px rgba(37, 99, 235, .24),
            0 0 0 4px rgba(37, 99, 235, .11) !important;
    }

    .ff-submit:active,
    .ff-submit.active {
        transform: translateY(1px) scale(.995) !important;
        border-color: #1e3a8a !important;
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%) !important;
        box-shadow:
            0 4px 10px rgba(30, 64, 175, .2),
            inset 0 2px 4px rgba(15, 23, 42, .14) !important;
    }

    .ff-submit.is-submitting,
    .ff-submit.is-submitting:hover,
    .ff-submit.is-submitting:focus,
    .ff-submit:disabled {
        transform: none !important;
        border-color: #1d4ed8 !important;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        opacity: 1 !important;
        cursor: progress;
        box-shadow: 0 7px 16px rgba(37, 99, 235, .18) !important;
    }

    .ff-submit.is-submitting::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: inherit;
        background: linear-gradient(
            105deg,
            transparent 28%,
            rgba(255, 255, 255, .14) 48%,
            transparent 68%
        );
        transform: translateX(-110%);
        animation: ffButtonShine 1.15s ease-in-out infinite;
        pointer-events: none;
    }

    .ff-submit .spinner-border {
        width: 1rem;
        height: 1rem;
        flex: 0 0 1rem;
        border-width: .14em;
    }

    @keyframes ffButtonShine {
        to { transform: translateX(110%); }
    }

    @media (max-width: 1199.98px) {
        .ff-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 991.98px) {
        .ff-page {
            padding: .8rem;
        }

        .ff-body {
            padding: .9rem;
        }

        .ff-hero {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media (max-width: 767.98px) {
        .ff-page {
            padding: .55rem;
        }

        .ff-shell,
        .ff-card {
            border-radius: 16px;
        }

        .ff-body,
        .ff-alert-wrap,
        .ff-card-header,
        .ff-card-body {
            padding: .8rem;
        }

        .ff-form-grid {
            grid-template-columns: 1fr;
            gap: .85rem;
        }

        .ff-span-12,
        .ff-span-8,
        .ff-span-6,
        .ff-span-4 {
            grid-column: span 1;
        }

        .ff-checklist {
            max-height: 300px;
            padding: .75rem;
        }

        .ff-checklist-grid {
            grid-template-columns: 1fr;
        }

        .ff-actions {
            align-items: stretch;
            flex-direction: column;
            padding: .8rem;
        }

        .ff-submit {
            width: 100%;
            min-width: 0;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .ff-hero-badge,
        .ff-check-item,
        .ff-input,
        .ff-select,
        .ff-textarea,
        .ff-submit {
            transition: none;
        }

        .ff-submit.is-submitting::after {
            animation: none;
        }
    }
</style>

<div class="container-fluid ff-page">
    <div class="ff-shell">
        <div class="ff-alert-wrap">
            @if(session('info') || !empty($info))
                <div class="alert alert-info ff-alert">
                    {{ session('info', $info ?? '') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger ff-alert">{{ session('error') }}</div>
            @endif

            @if(session('success'))
                <div class="alert alert-success ff-alert">{{ session('success') }}</div>
            @endif
        </div>

        {{-- @include('admin_client.include.tabs') --}}

        <div class="ff-body">
            <div class="ff-hero">
                <div>
                    <h2 class="ff-hero-title">บันทึกการสอบข้อเท็จจริงเบื้องต้น</h2>
                    <p class="ff-hero-subtitle">
                        ออกแบบใหม่ให้ใช้งานง่ายขึ้น ดูทันสมัย รองรับทุกหน้าจอ และช่วยลดความผิดพลาดในการกรอกข้อมูล
                    </p>
                </div>

                <div class="ff-hero-badge">
                    <i class="bi bi-shield-check"></i>
                    <span>โหมดเพิ่มข้อมูลใหม่</span>
                </div>
            </div>

            <form action="{{ route('factfinding.store') }}" method="POST" id="factfindingForm">
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">

                <div class="ff-grid">
                    <section class="ff-card">
                        <div class="ff-card-header">
                            <div class="ff-card-icon">
                                <i class="bi bi-clipboard2-pulse"></i>
                            </div>
                            <div>
                                <h4 class="ff-card-title">ข้อมูลการสอบข้อเท็จจริงเบื้องต้น</h4>
                                <p class="ff-card-subtitle">ข้อมูลเบื้องต้น สุขภาพ เอกสาร และความสัมพันธ์ในครอบครัว</p>
                            </div>
                        </div>

                        <div class="ff-card-body">
                            <div class="ff-form-grid">
                                <div class="ff-field ff-span-4">
                                    <label for="date" class="ff-label">
                                        วันที่นำส่ง <span class="ff-required">*</span>
                                    </label>

                                    <input type="date"
                                        name="date"
                                        id="date"
                                        class="form-control ff-input @error('date') is-invalid @enderror"
                                        value="{{ old('date') }}"
                                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                                        required>

                                    @error('date')
                                        <span class="ff-error" id="date-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-8">
                                    <label for="fact_name" class="ff-label">ผู้นำส่ง <span class="ff-required">*</span></label>
                                    <input type="text" name="fact_name" id="fact_name" class="form-control ff-input @error('fact_name') is-invalid @enderror" value="{{ old('fact_name') }}" required>
                                    @error('fact_name')
                                        <span class="ff-error" id="fact_name-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="appearance" class="ff-label">รูปพรรณสัณฐาน</label>
                                    <input type="text" name="appearance" id="appearance" class="form-control ff-input @error('appearance') is-invalid @enderror" value="{{ old('appearance') }}">
                                    @error('appearance')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="skin" class="ff-label">สีผิว</label>
                                    <input type="text" name="skin" id="skin" class="form-control ff-input @error('skin') is-invalid @enderror" value="{{ old('skin') }}">
                                    @error('skin')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="scar" class="ff-label">ตำหนิ/แผลเป็น</label>
                                    <input type="text" name="scar" id="scar" class="form-control ff-input @error('scar') is-invalid @enderror" value="{{ old('scar') }}">
                                    @error('scar')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="disability" class="ff-label">ลักษณะความพิการ</label>
                                    <input type="text" name="disability" id="disability" class="form-control ff-input @error('disability') is-invalid @enderror" value="{{ old('disability') }}">
                                    @error('disability')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label class="ff-label d-block">ประวัติการเจ็บป่วย <span class="ff-required">*</span></label>
                                    <div class="ff-inline-radio @error('sick') is-invalid @enderror">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="sick" id="sickYes" value="1" {{ old('sick') === '1' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="sickYes">มี</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="sick" id="sickNo" value="0" {{ old('sick') === '0' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sickNo">ไม่มี</label>
                                        </div>
                                    </div>
                                    @error('sick')
                                        <span class="ff-error" id="sick-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12" id="sickDetailGroup" style="{{ old('sick') == 1 ? '' : 'display:none;' }}">
                                    <div class="ff-detail-box">
                                        <label for="sick_detail" class="ff-label">รายละเอียดการเจ็บป่วย <span class="ff-required">*</span></label>
                                        <textarea name="sick_detail" id="sick_detail" rows="4" class="form-control ff-textarea ff-textarea-sm @error('sick_detail') is-invalid @enderror" {{ old('sick') == 1 ? 'required' : '' }}>{{ old('sick_detail') }}</textarea>
                                        @error('sick_detail')
                                            <span class="ff-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="treatment" class="ff-label">การรักษาพยาบาล</label>
                                    <input type="text" name="treatment" id="treatment" class="form-control ff-input @error('treatment') is-invalid @enderror" value="{{ old('treatment') }}">
                                    @error('treatment')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="hospital" class="ff-label">สถานพยาบาล</label>
                                    <input type="text" name="hospital" id="hospital" class="form-control ff-input @error('hospital') is-invalid @enderror" value="{{ old('hospital') }}">
                                    @error('hospital')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-4">
                                    <label for="blood_group" class="ff-label">กรุ๊ปเลือด</label>
                                    <select name="blood_group" id="blood_group" class="form-select ff-select @error('blood_group') is-invalid @enderror">
                                        <option value="">-- กรุณาเลือกกรุ๊ปเลือด --</option>
                                        <option value="A" {{ old('blood_group') == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('blood_group') == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="AB" {{ old('blood_group') == 'AB' ? 'selected' : '' }}>AB</option>
                                        <option value="O" {{ old('blood_group') == 'O' ? 'selected' : '' }}>O</option>
                                        <option value="ไม่ระบุ" {{ old('blood_group') == 'ไม่ระบุ' ? 'selected' : '' }}>ไม่ระบุ</option>
                                    </select>
                                    @error('blood_group')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-4">
                                    <label for="weight" class="ff-label">น้ำหนัก</label>
                                    <div class="ff-unit">
                                        <input type="number" name="weight" id="weight" min="0" max="500" step="0.1" inputmode="decimal" class="form-control ff-input @error('weight') is-invalid @enderror" value="{{ old('weight') }}">
                                        <span class="unit-text">กิโลกรัม</span>
                                    </div>
                                    @error('weight')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-4">
                                    <label for="height" class="ff-label">ส่วนสูง</label>
                                    <div class="ff-unit">
                                        <input type="number" name="height" id="height" min="0" max="300" step="0.1" inputmode="decimal" class="form-control ff-input @error('height') is-invalid @enderror" value="{{ old('height') }}">
                                        <span class="unit-text">เซนติเมตร</span>
                                    </div>
                                    @error('height')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="hygiene" class="ff-label">ความสะอาดร่างกาย</label>
                                    <input type="text" name="hygiene" id="hygiene" class="form-control ff-input @error('hygiene') is-invalid @enderror" value="{{ old('hygiene') }}">
                                    @error('hygiene')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="oral_health" class="ff-label">สุขภาพช่องปาก</label>
                                    <input type="text" name="oral_health" id="oral_health" class="form-control ff-input @error('oral_health') is-invalid @enderror" value="{{ old('oral_health') }}">
                                    @error('oral_health')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="injury" class="ff-label">การบาดเจ็บ/บาดแผล</label>
                                    <input type="text" name="injury" id="injury" class="form-control ff-input @error('injury') is-invalid @enderror" value="{{ old('injury') }}">
                                    @error('injury')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-6">
                                    <label for="marital_id" class="ff-label">สถานะการสมรส <span class="ff-required">*</span></label>
                                    <select name="marital_id" id="marital_id" class="form-select ff-select @error('marital_id') is-invalid @enderror" required>
                                        <option value="">--สถานะการสมรส--</option>
                                        @foreach ($maritals as $item)
                                            <option value="{{ $item->id }}" {{ old('marital_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->marital_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('marital_id')
                                        <span class="ff-error" id="marital_id-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="relation_parent" class="ff-label">ความสัมพันธ์ระหว่างบิดา/มารดา</label>
                                    <textarea name="relation_parent" id="relation_parent" rows="3" class="form-control ff-textarea ff-textarea-sm @error('relation_parent') is-invalid @enderror">{{ old('relation_parent') }}</textarea>
                                    @error('relation_parent')
                                        <span class="ff-error" id="relation_parent-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="relation_family" class="ff-label">ความสัมพันธ์ระหว่างบุคคลในครอบครัว</label>
                                    <textarea name="relation_family" id="relation_family" rows="3" class="form-control ff-textarea ff-textarea-sm @error('relation_family') is-invalid @enderror">{{ old('relation_family') }}</textarea>
                                    @error('relation_family')
                                        <span class="ff-error" id="relation_family-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="relation_child" class="ff-label">ความสัมพันธ์ระหว่างเด็กกับบุคคลในครอบครัว</label>
                                    <textarea name="relation_child" id="relation_child" rows="3" class="form-control ff-textarea ff-textarea-sm @error('relation_child') is-invalid @enderror">{{ old('relation_child') }}</textarea>
                                    @error('relation_child')
                                        <span class="ff-error" id="relation_child-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="evidence" class="ff-label">เอกสารเพิ่มเติม</label>
                                    <input type="text" name="evidence" id="evidence" class="form-control ff-input @error('evidence') is-invalid @enderror" value="{{ old('evidence') }}">
                                    @error('evidence')
                                        <span class="ff-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label class="ff-label d-block">
                                        เอกสารที่เกี่ยวข้อง
                                        <span class="ff-label-note">(เลือกได้มากกว่า 1 รายการ)</span>
                                    </label>

                                    <div class="ff-checklist">
                                        <div class="ff-checklist-grid">
                                            @foreach($documents as $document)
                                                <label for="document{{ $document->id }}" class="ff-check-item">
                                                    <input type="checkbox"
                                                        name="documents[]"
                                                        value="{{ $document->id }}"
                                                        id="document{{ $document->id }}"
                                                        {{ in_array((int) $document->id, array_map('intval', (array) old('documents', [])), true) ? 'checked' : '' }}>
                                                    <span>{{ $document->document_name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="ff-card">
                        <div class="ff-card-header">
                            <div class="ff-card-icon">
                                <i class="bi bi-house-heart"></i>
                            </div>
                            <div>
                                <h4 class="ff-card-title">การประเมินสภาวะเด็กและครอบครัว</h4>
                                <p class="ff-card-subtitle">ประเมินสภาพแวดล้อม ความต้องการ และการวินิจฉัยปัญหา</p>
                            </div>
                        </div>

                        <div class="ff-card-body">
                            <div class="ff-form-grid">
                                <div class="ff-field ff-span-12">
                                    <label for="ex_conditions" class="ff-label">สภาพที่อยู่อาศัยภายนอก</label>
                                    <textarea name="ex_conditions" id="ex_conditions" rows="3" class="form-control ff-textarea ff-textarea-sm @error('ex_conditions') is-invalid @enderror">{{ old('ex_conditions') }}</textarea>
                                    @error('ex_conditions')
                                        <span class="ff-error" id="ex_conditions-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="in_conditions" class="ff-label">สภาพที่อยู่อาศัยภายใน</label>
                                    <textarea name="in_conditions" id="in_conditions" rows="3" class="form-control ff-textarea ff-textarea-sm @error('in_conditions') is-invalid @enderror">{{ old('in_conditions') }}</textarea>
                                    @error('in_conditions')
                                        <span class="ff-error" id="in_conditions-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="environment" class="ff-label">สภาพแวดล้อม</label>
                                    <textarea name="environment" id="environment" rows="4" class="form-control ff-textarea ff-textarea-md @error('environment') is-invalid @enderror">{{ old('environment') }}</textarea>
                                    @error('environment')
                                        <span class="ff-error" id="environment-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="cause_problem" class="ff-label">สาเหตุที่เข้ารับการสงเคราะห์</label>
                                    <textarea name="cause_problem" id="cause_problem" rows="3" class="form-control ff-textarea ff-textarea-sm @error('cause_problem') is-invalid @enderror">{{ old('cause_problem') }}</textarea>
                                    @error('cause_problem')
                                        <span class="ff-error" id="cause_problem-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="need" class="ff-label">ความต้องการความช่วยเหลือ</label>
                                    <textarea name="need" id="need" rows="3" class="form-control ff-textarea ff-textarea-sm @error('need') is-invalid @enderror">{{ old('need') }}</textarea>
                                    @error('need')
                                        <span class="ff-error" id="need-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="case_history" class="ff-label">ประวัติความเป็นมา</label>
                                    <textarea name="case_history" id="case_history" rows="4" class="form-control ff-textarea ff-textarea-md @error('case_history') is-invalid @enderror">{{ old('case_history') }}</textarea>
                                    @error('case_history')
                                        <span class="ff-error" id="case_history-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="information" class="ff-label">ข้อเท็จจริงอื่นๆ</label>
                                    <textarea name="information" id="information" rows="4" class="form-control ff-textarea ff-textarea-md @error('information') is-invalid @enderror">{{ old('information') }}</textarea>
                                    @error('information')
                                        <span class="ff-error" id="information-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="ff-field ff-span-12">
                                    <label for="diagnosis" class="ff-label">การวินิจฉัยปัญหา</label>
                                    <textarea name="diagnosis" id="diagnosis" rows="4" class="form-control ff-textarea ff-textarea-md @error('diagnosis') is-invalid @enderror">{{ old('diagnosis') }}</textarea>
                                    @error('diagnosis')
                                        <span class="ff-error" id="diagnosis-error">{{ $message }}</span>
                                    @enderror
                                </div>

                              <div class="ff-field ff-span-4">
                                <label for="receive_date" class="ff-label">
                                    วันที่บันทึก <span class="ff-required">*</span>
                                </label>

                                <input type="date"
                                    name="receive_date"
                                    id="receive_date"
                                    class="form-control ff-input @error('receive_date') is-invalid @enderror"
                                    value="{{ old('receive_date') }}"
                                    max="{{ now('Asia/Bangkok')->toDateString() }}"
                                    required>

                                @error('receive_date')
                                    <span class="ff-error" id="receive_date-error">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                        <div class="ff-field ff-span-6">
                            <label class="ff-label">ผู้บันทึก</label>

                            <input type="text"
                                class="form-control ff-input"
                                value="{{ auth()->user()->name ?? 'ไม่ระบุผู้บันทึก' }}"
                                readonly>
                        </div>

                        </div> {{-- ff-form-grid --}}
                        </div> {{-- ff-card-body --}}
                        </section> {{-- ff-card --}}
                        </div> {{-- ff-grid --}}

                        <div class="ff-actions">
                            <div class="ff-actions-note">
                                กรุณาตรวจสอบข้อมูลสำคัญให้ครบถ้วนก่อนบันทึก
                            </div>

                            <button type="submit"
                                    class="btn ff-submit"
                                    id="ffSubmitBtn">
                                <i class="bi bi-floppy-fill" aria-hidden="true"></i>
                                <span>บันทึกข้อมูล</span>
                            </button>
                        </div>

                        </form>
                        </div> {{-- ff-body --}}
                        </div> {{-- ff-shell --}}
                        </div> {{-- ff-page --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('factfindingForm');
    const submitBtn = document.getElementById('ffSubmitBtn');

    if (!form || form.dataset.ffBound === '1') return;
    form.dataset.ffBound = '1';

    const sickYes = form.querySelector('#sickYes');
    const sickNo = form.querySelector('#sickNo');
    const sickWrap = form.querySelector('.ff-inline-radio');
    const detailGroup = form.querySelector('#sickDetailGroup');
    const detailField = form.querySelector('#sick_detail');
    const originalButtonHtml = submitBtn ? submitBtn.innerHTML : '';

    function clearFieldError(field) {
        field.classList.remove('is-invalid');

        const fieldWrap = field.closest('.ff-field');
        if (!fieldWrap) return;

        fieldWrap.querySelectorAll('.ff-error').forEach(function (error) {
            error.remove();
        });
    }

    function toggleSickDetail() {
        if (!sickYes || !detailGroup || !detailField) return;

        const isSick = sickYes.checked;
        detailGroup.hidden = !isSick;
        detailGroup.style.display = isSick ? '' : 'none';
        detailField.disabled = !isSick;
        detailField.required = isSick;

        if (!isSick) {
            detailField.value = '';
            clearFieldError(detailField);
        }
    }

    function resetSubmitButton() {
        if (!submitBtn) return;

        form.dataset.submitting = '0';
        submitBtn.disabled = false;
        submitBtn.classList.remove('is-submitting');
        submitBtn.removeAttribute('aria-busy');

        if (originalButtonHtml) {
            submitBtn.innerHTML = originalButtonHtml;
        }
    }

    [sickYes, sickNo].forEach(function (radio) {
        radio?.addEventListener('change', function () {
            sickWrap?.classList.remove('is-invalid');
            sickWrap?.closest('.ff-field')
                ?.querySelectorAll('.ff-error')
                .forEach(function (error) { error.remove(); });

            toggleSickDetail();
        });
    });

    form.addEventListener('input', function (event) {
        const field = event.target;
        if (!field.matches('input, textarea, select')) return;
        clearFieldError(field);
    });

    form.addEventListener('change', function (event) {
        const field = event.target;
        if (!field.matches('input, textarea, select')) return;
        clearFieldError(field);
    });

    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();

            form.classList.add('was-validated');

            const firstInvalid = form.querySelector(':invalid');
            firstInvalid?.focus({ preventScroll: true });
            firstInvalid?.closest('.ff-field')
                ?.scrollIntoView({ behavior: 'smooth', block: 'center' });

            return;
        }

        if (form.dataset.submitting === '1') {
            event.preventDefault();
            return;
        }

        form.dataset.submitting = '1';

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('is-submitting');
            submitBtn.setAttribute('aria-busy', 'true');
            submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span>กำลังบันทึก...</span>
            `;
        }
    });

    window.addEventListener('pageshow', resetSubmitButton);
    toggleSickDetail();
});
</script>

@endsection