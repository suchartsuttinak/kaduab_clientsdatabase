@extends('admin_client.admin_client')
@section('content')

@php
    $tabConfigs = [
        'father' => [
            'tab_id' => 'father-tab',
            'pane_id' => 'father-pane',
            'tab_label' => 'บิดา',
            'personal_title' => 'ข้อมูลส่วนตัวบิดา',
            'contact_title' => 'ข้อมูลการติดต่อบิดา',
            'name_label' => 'ชื่อบิดา',
            'name_placeholder' => 'กรอกคำนำหน้าชื่อ เช่น นายสุชาติ',
            'model' => $father ?? null,
        ],
        'mother' => [
            'tab_id' => 'mother-tab',
            'pane_id' => 'mother-pane',
            'tab_label' => 'มารดา',
            'personal_title' => 'ข้อมูลส่วนตัวมารดา',
            'contact_title' => 'ข้อมูลการติดต่อมารดา',
            'name_label' => 'ชื่อมารดา',
            'name_placeholder' => 'กรอกคำนำหน้าชื่อ เช่น นางสาวสมหญิง',
            'model' => $mother ?? null,
        ],
        'spouse' => [
            'tab_id' => 'spouse-tab',
            'pane_id' => 'spouse-pane',
            'tab_label' => 'ผู้ปกครอง',
            'personal_title' => 'ข้อมูลส่วนตัวผู้ปกครอง',
            'contact_title' => 'ข้อมูลการติดต่อผู้ปกครอง',
            'name_label' => 'ผู้ปกครองชื่อ',
            'name_placeholder' => 'กรอกคำนำหน้าชื่อ เช่น นายสมชาย',
            'model' => $spouse ?? null,
        ],
        'relative' => [
            'tab_id' => 'relative-tab',
            'pane_id' => 'relative-pane',
            'tab_label' => 'ญาติที่อยู่ที่อื่น',
            'personal_title' => 'ข้อมูลส่วนตัวญาติที่อยู่ที่อื่น',
            'contact_title' => 'ข้อมูลการติดต่อญาติที่อยู่ที่อื่น',
            'name_label' => 'ญาติที่อยู่ที่อื่นชื่อ',
            'name_placeholder' => 'กรอกคำนำหน้าชื่อ เช่น นางสาวสมหญิง',
            'model' => $relative ?? null,
        ],
    ];

    $savedActiveTab = session('active_tab', 'father-tab');
    $hasExistingData = isset($father) || isset($mother) || isset($spouse) || isset($relative);
    $submitLabel = $hasExistingData ? 'แก้ไขข้อมูล' : 'บันทึกข้อมูล';
@endphp

<style>
    .family-page {
        --fp-primary: #0f766e;
        --fp-primary-2: #14b8a6;
        --fp-primary-soft: rgba(15, 118, 110, 0.10);
        --fp-primary-soft-2: rgba(15, 118, 110, 0.16);
        --fp-text: #1f2937;
        --fp-muted: #6b7280;
        --fp-border: #dbe7e7;
        --fp-border-strong: #cfe0e2;
        --fp-bg: #f6fbfb;
        --fp-card: #ffffff;
        --fp-card-soft: #fbfefe;
        --fp-danger: #dc2626;
        --fp-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
        --fp-shadow-lg: 0 16px 36px rgba(15, 23, 42, 0.08);
        --fp-radius-xl: 22px;
        --fp-radius-lg: 18px;
        --fp-radius-md: 14px;
        --fp-radius-sm: 12px;
    }

    .family-page {
        color: var(--fp-text);
    }

    .family-page .family-shell {
        background: linear-gradient(180deg, #fcfefe 0%, #f7fbfb 100%);
        border: 1px solid var(--fp-border);
        border-radius: var(--fp-radius-xl);
        padding: 1rem;
        box-shadow: var(--fp-shadow);
    }

    .family-page .family-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: .2rem .1rem 0;
    }

    .family-page .family-title-wrap {
        min-width: 0;
    }

    .family-page .family-title {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.2;
        color: var(--fp-text);
    }

    .family-page .family-subtitle {
        margin: .3rem 0 0;
        color: var(--fp-muted);
        font-size: .92rem;
        line-height: 1.4;
    }

    .family-page .family-status-badge {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .6rem .85rem;
        border-radius: 999px;
        border: 1px solid var(--fp-border);
        background: #fff;
        font-weight: 700;
        font-size: .88rem;
        color: var(--fp-primary);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.04);
        white-space: nowrap;
    }

    .family-page .family-status-badge i {
        font-size: .95rem;
    }

    .family-page .family-tabs-wrap {
        margin-bottom: 1rem;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }

    .family-page .family-tabs-wrap::-webkit-scrollbar {
        height: 8px;
    }

    .family-page .family-tabs-wrap::-webkit-scrollbar-thumb {
        background: rgba(15, 118, 110, 0.18);
        border-radius: 999px;
    }

    .family-page .family-tabs {
        display: flex;
        flex-wrap: nowrap;
        gap: .55rem;
        min-width: max-content;
        border-bottom: 0;
        padding-bottom: .1rem;
    }

    .family-page .family-tabs .nav-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        min-height: 48px;
        padding: .82rem 1rem;
        border-radius: var(--fp-radius-md);
        border: 1px solid var(--fp-border);
        background: #fff;
        color: var(--fp-text);
        font-weight: 700;
        white-space: nowrap;
        transition: all .2s ease;
        box-shadow: 0 3px 10px rgba(15, 23, 42, 0.02);
    }

    .family-page .family-tabs .nav-link:hover {
        background: #f9fcfc;
        border-color: rgba(15, 118, 110, 0.20);
        color: var(--fp-primary);
        transform: translateY(-1px);
    }

    .family-page .family-tabs .nav-link.active {
        background: linear-gradient(180deg, rgba(15, 118, 110, 0.14), rgba(20, 184, 166, 0.10));
        color: #0b5f59;
        border-color: rgba(15, 118, 110, 0.22);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.75), 0 8px 18px rgba(15, 23, 42, 0.05);
    }

    .family-page .family-tabs .nav-link.active::after {
        content: "";
        position: absolute;
        left: 14px;
        right: 14px;
        bottom: 6px;
        height: 3px;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--fp-primary), var(--fp-primary-2));
    }

    .family-page .family-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .family-page .family-card {
        border: 1px solid var(--fp-border);
        border-radius: var(--fp-radius-lg);
        background: var(--fp-card);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        overflow: hidden;
        height: 100%;
    }

    .family-page .family-card-header {
        display: flex;
        align-items: center;
        gap: .65rem;
        padding: 1rem 1rem .95rem;
        background: linear-gradient(180deg, #ffffff, #f8fbfb);
        border-bottom: 1px solid var(--fp-border);
    }

    .family-page .family-card-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(15, 118, 110, 0.12), rgba(20, 184, 166, 0.10));
        color: var(--fp-primary);
        border: 1px solid rgba(15, 118, 110, 0.10);
    }

    .family-page .family-card-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.25;
        color: var(--fp-text);
    }

    .family-page .family-card-subtitle {
        margin: .2rem 0 0;
        color: var(--fp-muted);
        font-size: .86rem;
    }

    .family-page .family-card-body {
        padding: 1rem;
    }

    .family-page .family-form-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: .95rem .9rem;
    }

    .family-page .span-12 { grid-column: span 12; }
    .family-page .span-8  { grid-column: span 8; }
    .family-page .span-6  { grid-column: span 6; }
    .family-page .span-4  { grid-column: span 4; }

    .family-page .field-group {
        min-width: 0;
    }

    .family-page .form-label {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        margin-bottom: .45rem;
        font-weight: 700;
        color: #334155;
        line-height: 1.3;
    }

    .family-page .label-required {
        color: var(--fp-danger);
        font-weight: 800;
    }

    .family-page .form-control,
    .family-page .form-select,
    .family-page .input-group-text {
        min-height: 46px;
        border-radius: var(--fp-radius-sm);
        border-color: #d7e3e5;
        font-size: .95rem;
    }

    .family-page .form-control,
    .family-page .form-select {
        background: #fff;
    }

    .family-page .form-control::placeholder {
        color: #94a3b8;
    }

    .family-page .form-control:focus,
    .family-page .form-select:focus {
        border-color: rgba(15, 118, 110, 0.34);
        box-shadow: 0 0 0 .22rem rgba(15, 118, 110, 0.10);
    }

    .family-page .input-group-text {
        background: #f8fbfb;
        color: #475569;
        font-weight: 700;
    }

    .family-page .form-control.is-invalid,
    .family-page .form-select.is-invalid {
        border-color: rgba(220, 38, 38, 0.45);
        background-image: none;
    }

    .family-page .field-error {
        margin-top: .38rem;
        display: block;
        font-size: .82rem;
        color: var(--fp-danger);
        line-height: 1.35;
    }

    .family-page .family-actionbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-top: 1.15rem;
        padding: 1rem 0 0;
        border-top: 1px solid var(--fp-border);
    }

    .family-page .family-note {
        flex: 1 1 auto;
        display: flex;
        align-items: center;
        gap: .7rem;
        min-height: 54px;
        padding: .9rem 1rem;
        border-radius: 16px;
        background: linear-gradient(180deg, #ffffff, #f9fcfc);
        border: 1px solid var(--fp-border);
        color: #52606d;
        font-size: .9rem;
        line-height: 1.45;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.03);
    }

    .family-page .family-note::before {
        content: "\F431";
        font-family: "bootstrap-icons";
        font-size: 1rem;
        color: var(--fp-primary);
        line-height: 1;
        flex: 0 0 auto;
    }

    .family-page .family-action-buttons {
        display: flex;
        align-items: center;
        gap: .75rem;
        flex: 0 0 auto;
    }

    .family-page .btn-family-back,
    .family-page .btn-family-submit {
        min-height: 50px;
        padding: .85rem 1.2rem;
        border-radius: 14px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        transition: all .18s ease;
        min-width: 150px;
    }

    .family-page .btn-family-back {
        color: #475569;
        background: #ffffff;
        border: 1px solid #d8e2e8;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
        text-decoration: none;
    }

    .family-page .btn-family-back:hover {
        color: #0f766e;
        border-color: rgba(15, 118, 110, 0.24);
        background: #f8fcfc;
        transform: translateY(-1px);
    }

    .family-page .btn-family-submit {
        border: 0;
        color: #fff;
        background: linear-gradient(135deg, var(--fp-primary), var(--fp-primary-2));
        box-shadow: 0 12px 24px rgba(15, 118, 110, 0.18);
    }

    .family-page .btn-family-submit:hover {
        filter: brightness(.98);
        transform: translateY(-1px);
    }

    .family-page .btn-family-submit:disabled {
        opacity: .84;
        transform: none;
    }

    .family-page .tab-pane {
        animation: familyFade .18s ease;
    }

    .family-page .ajax-banner {
        display: none;
        margin-bottom: 1rem;
        padding: .9rem 1rem;
        border-radius: 14px;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .family-page .ajax-banner.show {
        display: block;
    }

    .family-page .ajax-banner.success {
        background: #ecfdf5;
        color: #065f46;
        border-color: #a7f3d0;
    }

    .family-page .ajax-banner.error {
        background: #fef2f2;
        color: #991b1b;
        border-color: #fecaca;
    }

    @keyframes familyFade {
        from { opacity: 0; transform: translateY(4px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 1199.98px) {
        .family-page .family-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 991.98px) {
        .family-page .family-shell {
            padding: .9rem;
            border-radius: 18px;
        }

        .family-page .family-topbar {
            flex-direction: column;
            align-items: flex-start;
        }

        .family-page .family-status-badge {
            white-space: normal;
        }

        .family-page .family-card-body {
            padding: .95rem;
        }

        .family-page .family-tabs .nav-link {
            padding: .74rem .95rem;
            min-height: 46px;
            border-radius: 12px;
        }

        .family-page .family-actionbar {
            flex-direction: column;
            align-items: stretch;
        }

        .family-page .family-note {
            width: 100%;
        }

        .family-page .family-action-buttons {
            width: 100%;
            justify-content: flex-end;
        }
    }

    @media (max-width: 767.98px) {
        .family-page .family-shell {
            padding: .78rem;
            border-radius: 16px;
        }

        .family-page .family-title {
            font-size: 1.04rem;
        }

        .family-page .family-subtitle {
            font-size: .88rem;
        }

        .family-page .family-card {
            border-radius: 16px;
        }

        .family-page .family-card-header {
            padding: .92rem .92rem .88rem;
        }

        .family-page .family-card-body {
            padding: .9rem;
        }

        .family-page .family-form-grid {
            grid-template-columns: 1fr;
            gap: .88rem;
        }

        .family-page .span-12,
        .family-page .span-8,
        .family-page .span-6,
        .family-page .span-4 {
            grid-column: span 1;
        }

        .family-page .family-action-buttons {
            width: 100%;
            flex-direction: column-reverse;
        }

        .family-page .family-actionbar .btn,
        .family-page .family-actionbar a {
            width: 100%;
        }

        .family-page .btn-family-back,
        .family-page .btn-family-submit {
            min-width: 100%;
        }
    }
</style>

<link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">

<div class="family-page">
    <div class="family-shell">
        <div id="familyAjaxBanner" class="ajax-banner"></div>

        <div class="family-topbar">
            <div class="family-title-wrap">
                <h2 class="family-title">ข้อมูลครอบครัวและผู้เกี่ยวข้อง</h2>
                <p class="family-subtitle">
                    บันทึกข้อมูลบิดา มารดา ผู้ปกครอง และญาติที่อยู่ที่อื่น โดยออกแบบให้รองรับทุกหน้าจอ ใช้งานง่าย และลดความผิดพลาดจากโค้ดซ้ำ
                </p>
            </div>

            <div class="family-status-badge" id="familyStatusBadge">
                <i class="bi bi-shield-check"></i>
                <span>{{ $hasExistingData ? 'โหมดแก้ไขข้อมูล' : 'โหมดบันทึกข้อมูลใหม่' }}</span>
            </div>
        </div>

        <form id="familyForm" action="{{ route('family.store') }}" method="POST" novalidate>
            @csrf
            <input type="hidden" name="client_id" value="{{ $client->id }}">
            <input type="hidden" name="active_tab" id="active_tab" value="{{ $savedActiveTab }}">

            <div class="family-tabs-wrap">
                <ul class="nav family-tabs" id="familyTabs" role="tablist" aria-label="แท็บข้อมูลครอบครัว">
                    @foreach ($tabConfigs as $prefix => $config)
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link {{ $savedActiveTab === $config['tab_id'] ? 'active' : ($loop->first && !$savedActiveTab ? 'active' : '') }}"
                                id="{{ $config['tab_id'] }}"
                                data-bs-toggle="tab"
                                data-bs-target="#{{ $config['pane_id'] }}"
                                type="button"
                                role="tab"
                                aria-controls="{{ $config['pane_id'] }}"
                                aria-selected="{{ $savedActiveTab === $config['tab_id'] ? 'true' : 'false' }}"
                            >
                                <i class="bi bi-person-vcard"></i>
                                <span>{{ $config['tab_label'] }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="tab-content" id="familyTabsContent">
                @foreach ($tabConfigs as $prefix => $config)
                    @php
                        $person = $config['model'];
                        $isActivePane = $savedActiveTab === $config['tab_id'] || ($loop->first && !$savedActiveTab);
                    @endphp

                    <div
                        class="tab-pane fade {{ $isActivePane ? 'show active' : '' }}"
                        id="{{ $config['pane_id'] }}"
                        role="tabpanel"
                        aria-labelledby="{{ $config['tab_id'] }}"
                        tabindex="0"
                    >
                        <div class="family-grid">
                            <section class="family-card">
                                <div class="family-card-header">
                                    <div class="family-card-icon">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                    <div>
                                        <h5 class="family-card-title">{{ $config['personal_title'] }}</h5>
                                        <p class="family-card-subtitle">ข้อมูลประจำตัวและข้อมูลพื้นฐาน</p>
                                    </div>
                                </div>

                                <div class="family-card-body">
                                    <div class="family-form-grid">
                                        @php
                                            $nameField = "{$prefix}.fname";
                                            $lnameField = "{$prefix}.lname";
                                            $idcardField = "{$prefix}.idcard";
                                            $ageField = "{$prefix}.age";
                                            $occupationField = "{$prefix}.occupation";
                                            $incomeField = "{$prefix}.income";
                                        @endphp

                                        <div class="field-group span-12">
                                            <label for="{{ $prefix }}_fname" class="form-label">
                                                {{ $config['name_label'] }}
                                            </label>
                                            <input
                                                type="text"
                                                name="{{ $prefix }}[fname]"
                                                id="{{ $prefix }}_fname"
                                                class="form-control {{ $errors->has($nameField) ? 'is-invalid' : '' }}"
                                                value="{{ old($nameField, $person->fname ?? '') }}"
                                                placeholder="{{ $config['name_placeholder'] }}"
                                                autocomplete="off"
                                            >
                                            @if ($errors->has($nameField))
                                                <span class="field-error">{{ $errors->first($nameField) }}</span>
                                            @endif
                                        </div>

                                        <div class="field-group span-12">
                                            <label for="{{ $prefix }}_lname" class="form-label">นามสกุล</label>
                                            <input
                                                type="text"
                                                name="{{ $prefix }}[lname]"
                                                id="{{ $prefix }}_lname"
                                                class="form-control {{ $errors->has($lnameField) ? 'is-invalid' : '' }}"
                                                value="{{ old($lnameField, $person->lname ?? '') }}"
                                                autocomplete="off"
                                            >
                                            @if ($errors->has($lnameField))
                                                <span class="field-error">{{ $errors->first($lnameField) }}</span>
                                            @endif
                                        </div>

                                        <div class="field-group span-12">
                                            <label for="{{ $prefix }}_idcard" class="form-label">เลขประจำตัวประชาชน</label>
                                            <input
                                                type="text"
                                                name="{{ $prefix }}[idcard]"
                                                id="{{ $prefix }}_idcard"
                                                class="form-control js-thai-idcard {{ $errors->has($idcardField) ? 'is-invalid' : '' }}"
                                                value="{{ old($idcardField, $person->idcard ?? '') }}"
                                                maxlength="17"
                                                placeholder="0-0000-00000-00-0"
                                                inputmode="numeric"
                                                autocomplete="off"
                                            >
                                            @if ($errors->has($idcardField))
                                                <span class="field-error">{{ $errors->first($idcardField) }}</span>
                                            @endif
                                        </div>

                                        <div class="field-group span-4">
                                            <label for="{{ $prefix }}_age" class="form-label">อายุ</label>
                                            <div class="input-group">
                                                <input
                                                    type="number"
                                                    name="{{ $prefix }}[age]"
                                                    id="{{ $prefix }}_age"
                                                    class="form-control {{ $errors->has($ageField) ? 'is-invalid' : '' }}"
                                                    value="{{ old($ageField, $person->age ?? '') }}"
                                                    min="0"
                                                >
                                                <span class="input-group-text">ปี</span>
                                            </div>
                                            @if ($errors->has($ageField))
                                                <span class="field-error">{{ $errors->first($ageField) }}</span>
                                            @endif
                                        </div>

                                        <div class="field-group span-8">
                                            <label for="{{ $prefix }}_occupation" class="form-label">อาชีพ</label>
                                            <input
                                                type="text"
                                                name="{{ $prefix }}[occupation]"
                                                id="{{ $prefix }}_occupation"
                                                class="form-control {{ $errors->has($occupationField) ? 'is-invalid' : '' }}"
                                                value="{{ old($occupationField, $person->occupation ?? '') }}"
                                                autocomplete="off"
                                            >
                                            @if ($errors->has($occupationField))
                                                <span class="field-error">{{ $errors->first($occupationField) }}</span>
                                            @endif
                                        </div>

                                     <div class="field-group span-12">
                                            <label for="{{ $prefix }}_income" class="form-label">รายได้</label>

                                            <div class="input-group">
                                                <input
                                                    type="number"
                                                    name="{{ $prefix }}[income]"
                                                    id="{{ $prefix }}_income"
                                                    class="form-control {{ $errors->has($incomeField) ? 'is-invalid' : '' }}"
                                                    value="{{ old($incomeField, $person->income ?? '') }}"
                                                    min="0"
                                                    step="0.01"
                                                    placeholder="เช่น 12000"
                                                    autocomplete="off"
                                                >
                                                <span class="input-group-text">บาท/เดือน</span>
                                            </div>

                                            @if ($errors->has($incomeField))
                                                <span class="field-error">{{ $errors->first($incomeField) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="family-card">
                                <div class="family-card-header">
                                    <div class="family-card-icon">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="family-card-title">{{ $config['contact_title'] }}</h5>
                                        <p class="family-card-subtitle">ที่อยู่และช่องทางการติดต่อ</p>
                                    </div>
                                </div>

                                <div class="family-card-body">
                                    <div class="family-form-grid">
                                        @php
                                            $addressNoField = "{$prefix}.address_no";
                                            $mooField = "{$prefix}.moo";
                                            $soiField = "{$prefix}.soi";
                                            $roadField = "{$prefix}.road";
                                            $villageField = "{$prefix}.village";
                                            $provinceField = "{$prefix}.province_id";
                                            $districtField = "{$prefix}.district_id";
                                            $subDistrictField = "{$prefix}.sub_district_id";
                                            $zipcodeField = "{$prefix}.zipcode";
                                            $phoneField = "{$prefix}.phone";
                                        @endphp

                                        <div class="field-group span-6">
                                            <label for="{{ $prefix }}_address_no" class="form-label">ที่อยู่เลขที่</label>
                                            <input
                                                type="text"
                                                name="{{ $prefix }}[address_no]"
                                                id="{{ $prefix }}_address_no"
                                                class="form-control {{ $errors->has($addressNoField) ? 'is-invalid' : '' }}"
                                                value="{{ old($addressNoField, $person->address_no ?? '') }}"
                                                autocomplete="off"
                                            >
                                            @if ($errors->has($addressNoField))
                                                <span class="field-error">{{ $errors->first($addressNoField) }}</span>
                                            @endif
                                        </div>

                                        <div class="field-group span-6">
                                            <label for="{{ $prefix }}_moo" class="form-label">หมู่ที่</label>
                                            <input
                                                type="text"
                                                name="{{ $prefix }}[moo]"
                                                id="{{ $prefix }}_moo"
                                                class="form-control {{ $errors->has($mooField) ? 'is-invalid' : '' }}"
                                                value="{{ old($mooField, $person->moo ?? '') }}"
                                                autocomplete="off"
                                            >
                                            @if ($errors->has($mooField))
                                                <span class="field-error">{{ $errors->first($mooField) }}</span>
                                            @endif
                                        </div>

                                        <div class="field-group span-6">
                                            <label for="{{ $prefix }}_soi" class="form-label">ตรอก/ซอย</label>
                                            <input
                                                type="text"
                                                name="{{ $prefix }}[soi]"
                                                id="{{ $prefix }}_soi"
                                                class="form-control {{ $errors->has($soiField) ? 'is-invalid' : '' }}"
                                                value="{{ old($soiField, $person->soi ?? '') }}"
                                                autocomplete="off"
                                            >
                                            @if ($errors->has($soiField))
                                                <span class="field-error">{{ $errors->first($soiField) }}</span>
                                            @endif
                                        </div>

                                        <div class="field-group span-6">
                                            <label for="{{ $prefix }}_road" class="form-label">ถนน</label>
                                            <input
                                                type="text"
                                                name="{{ $prefix }}[road]"
                                                id="{{ $prefix }}_road"
                                                class="form-control {{ $errors->has($roadField) ? 'is-invalid' : '' }}"
                                                value="{{ old($roadField, $person->road ?? '') }}"
                                                autocomplete="off"
                                            >
                                            @if ($errors->has($roadField))
                                                <span class="field-error">{{ $errors->first($roadField) }}</span>
                                            @endif
                                        </div>

                                        <div class="field-group span-12">
                                            <label for="{{ $prefix }}_village" class="form-label">หมู่บ้าน</label>
                                            <input
                                                type="text"
                                                name="{{ $prefix }}[village]"
                                                id="{{ $prefix }}_village"
                                                class="form-control {{ $errors->has($villageField) ? 'is-invalid' : '' }}"
                                                value="{{ old($villageField, $person->village ?? '') }}"
                                                autocomplete="off"
                                            >
                                            @if ($errors->has($villageField))
                                                <span class="field-error">{{ $errors->first($villageField) }}</span>
                                            @endif
                                        </div>

                                        <div class="field-group span-6">
                                            <label for="{{ $prefix }}_province" class="form-label">จังหวัด</label>
                                            <select
                                                name="{{ $prefix }}[province_id]"
                                                id="{{ $prefix }}_province"
                                                class="form-select {{ $errors->has($provinceField) ? 'is-invalid' : '' }}"
                                                data-selected="{{ old($provinceField, $person->province_id ?? '') }}"
                                            >
                                                <option value="">--เลือกจังหวัด--</option>
                                                @foreach ($provinces as $province)
                                                    <option
                                                        value="{{ $province->id }}"
                                                        {{ old($provinceField, $person->province_id ?? '') == $province->id ? 'selected' : '' }}
                                                    >
                                                        {{ $province->prov_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has($provinceField))
                                                <span class="field-error">{{ $errors->first($provinceField) }}</span>
                                            @endif
                                        </div>

                                        <div class="field-group span-6">
                                            <label for="{{ $prefix }}_district" class="form-label">เขต/อำเภอ</label>
                                            <select
                                                name="{{ $prefix }}[district_id]"
                                                id="{{ $prefix }}_district"
                                                class="form-select {{ $errors->has($districtField) ? 'is-invalid' : '' }}"
                                                data-selected="{{ old($districtField, $person->district_id ?? '') }}"
                                            >
                                                <option value="">--เลือกอำเภอ--</option>
                                                @foreach ($districts as $district)
                                                    <option
                                                        value="{{ $district->id }}"
                                                        {{ old($districtField, $person->district_id ?? '') == $district->id ? 'selected' : '' }}
                                                    >
                                                        {{ $district->dist_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has($districtField))
                                                <span class="field-error">{{ $errors->first($districtField) }}</span>
                                            @endif
                                        </div>

                                        <div class="field-group span-12">
                                            <label for="{{ $prefix }}_subdistrict" class="form-label">แขวง/ตำบล</label>
                                            <select
                                                name="{{ $prefix }}[sub_district_id]"
                                                id="{{ $prefix }}_subdistrict"
                                                class="form-select {{ $errors->has($subDistrictField) ? 'is-invalid' : '' }}"
                                                data-selected="{{ old($subDistrictField, $person->sub_district_id ?? '') }}"
                                            >
                                                <option value="">--เลือกตำบล--</option>
                                                @foreach ($sub_districts as $subdistrict)
                                                    <option
                                                        value="{{ $subdistrict->id }}"
                                                        {{ old($subDistrictField, $person->sub_district_id ?? '') == $subdistrict->id ? 'selected' : '' }}
                                                    >
                                                        {{ $subdistrict->subd_name ?? $subdistrict->subdist_name ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has($subDistrictField))
                                                <span class="field-error">{{ $errors->first($subDistrictField) }}</span>
                                            @endif
                                        </div>

                                        <div class="field-group span-4">
                                            <label for="{{ $prefix }}_zipcode" class="form-label">รหัสไปรษณีย์</label>
                                            <input
                                                type="text"
                                                name="{{ $prefix }}[zipcode]"
                                                id="{{ $prefix }}_zipcode"
                                                class="form-control {{ $errors->has($zipcodeField) ? 'is-invalid' : '' }}"
                                                value="{{ old($zipcodeField, $person->zipcode ?? '') }}"
                                                autocomplete="off"
                                            >
                                            @if ($errors->has($zipcodeField))
                                                <span class="field-error">{{ $errors->first($zipcodeField) }}</span>
                                            @endif
                                        </div>

                                        <div class="field-group span-8">
                                            <label for="{{ $prefix }}_phone" class="form-label">โทรศัพท์</label>
                                            <input
                                                type="text"
                                                name="{{ $prefix }}[phone]"
                                                id="{{ $prefix }}_phone"
                                                class="form-control {{ $errors->has($phoneField) ? 'is-invalid' : '' }}"
                                                value="{{ old($phoneField, $person->phone ?? '') }}"
                                                autocomplete="off"
                                            >
                                            @if ($errors->has($phoneField))
                                                <span class="field-error">{{ $errors->first($phoneField) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="family-actionbar">
                <div class="family-note">
                    ระบบจะบันทึกแท็บล่าสุดไว้ เพื่อให้กลับมาที่ส่วนเดิมหลังตรวจสอบหรือบันทึกข้อมูล
                </div>

                <div class="family-action-buttons">
                    <a href="{{ route('client.edit', $client->id) }}"
                       class="btn btn-family-back">
                        <i class="bi bi-arrow-left-circle"></i>
                        <span>กลับหน้าก่อน</span>
                    </a>

                    <button type="submit"
                            id="familySubmitBtn"
                            class="btn btn-family-submit"
                            data-default-label="{{ $submitLabel }}">
                        <i class="bi bi-check-circle me-1"></i>
                        <span>{{ $submitLabel }}</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const familyForm = document.getElementById('familyForm');
    const familySubmitBtn = document.getElementById('familySubmitBtn');
    const activeTabInput = document.getElementById('active_tab');
    const familyAjaxBanner = document.getElementById('familyAjaxBanner');
    const familyStatusBadge = document.getElementById('familyStatusBadge');
    const familyTabs = document.getElementById('familyTabs');

    const tabMap = {
        father: 'father-tab',
        mother: 'mother-tab',
        spouse: 'spouse-tab',
        relative: 'relative-tab'
    };

    /*
    |--------------------------------------------------------------------------
    | จัดรูปแบบเลขประจำตัวประชาชน
    |--------------------------------------------------------------------------
    | ให้เหมือนหน้า client:
    | 1-2345-67890-12-3
    | รองรับทั้งกรณีพิมพ์ใหม่ และกรณีข้อมูลเดิมในฐานข้อมูลเป็นเลขติดกัน 13 หลัก
    */
    const formatThaiIdCard = (value) => {
        let digits = String(value || '').replace(/\D/g, '').substring(0, 13);

        if (digits.length <= 1) {
            return digits;
        }

        let formatted = digits.substring(0, 1);

        if (digits.length > 1) {
            formatted += '-' + digits.substring(1, 5);
        }

        if (digits.length > 5) {
            formatted += '-' + digits.substring(5, 10);
        }

        if (digits.length > 10) {
            formatted += '-' + digits.substring(10, 12);
        }

        if (digits.length > 12) {
            formatted += '-' + digits.substring(12, 13);
        }

        return formatted;
    };

    const bindThaiIdCards = () => {
        document.querySelectorAll('.family-page .js-thai-idcard').forEach(input => {
            input.value = formatThaiIdCard(input.value);

            input.addEventListener('input', function () {
                this.value = formatThaiIdCard(this.value);

                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                }
            });

            input.addEventListener('blur', function () {
                this.value = formatThaiIdCard(this.value);
            });
        });
    };

    const fetchJSON = async (url) => {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`Request failed: ${response.status}`);
        }

        return await response.json();
    };

    const setOptions = (selectEl, items, placeholder, textKey, selectedValue = '') => {
        if (!selectEl) return;

        let html = `<option value="">${placeholder}</option>`;
        items.forEach(item => {
            const selected = String(item.id) === String(selectedValue) ? 'selected' : '';
            const text = item[textKey] ?? '';
            html += `<option value="${item.id}" ${selected}>${text}</option>`;
        });
        selectEl.innerHTML = html;
    };

    const showBanner = (message, type = 'success') => {
        if (!familyAjaxBanner) return;
        familyAjaxBanner.className = `ajax-banner show ${type}`;
        familyAjaxBanner.innerHTML = message;
        window.scrollTo({ top: familyAjaxBanner.offsetTop - 80, behavior: 'smooth' });
    };

    const clearBanner = () => {
        if (!familyAjaxBanner) return;
        familyAjaxBanner.className = 'ajax-banner';
        familyAjaxBanner.innerHTML = '';
    };

    const clearValidationErrors = () => {
        document.querySelectorAll('.family-page .is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });

        document.querySelectorAll('.family-page .field-error.is-ajax').forEach(el => {
            el.remove();
        });
    };

    const appendFieldError = (input, message) => {
        if (!input) return;
        input.classList.add('is-invalid');

        const oldAjaxError = input.closest('.field-group')?.querySelector('.field-error.is-ajax');
        if (oldAjaxError) {
            oldAjaxError.textContent = message;
            return;
        }

        const span = document.createElement('span');
        span.className = 'field-error is-ajax';
        span.textContent = message;

        const wrapper = input.closest('.field-group');
        if (wrapper) {
            wrapper.appendChild(span);
        }
    };

    const activateTabByField = (fieldName) => {
        if (!fieldName) return;

        const prefix = fieldName.split('.')[0];
        const tabId = tabMap[prefix];

        if (!tabId) return;

        const trigger = document.getElementById(tabId);
        if (trigger && window.bootstrap) {
            new bootstrap.Tab(trigger).show();
            if (activeTabInput) {
                activeTabInput.value = tabId;
            }
        }
    };

    const setSubmitButtonState = (isLoading = false, forceEditMode = false) => {
        if (!familySubmitBtn) return;

        const defaultLabel = forceEditMode
            ? 'แก้ไขข้อมูล'
            : (familySubmitBtn.dataset.defaultLabel || 'บันทึกข้อมูล');

        if (isLoading) {
            familySubmitBtn.disabled = true;
            familySubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><span>กำลังบันทึกข้อมูล...</span>';
            return;
        }

        familySubmitBtn.disabled = false;
        familySubmitBtn.innerHTML = `<i class="bi bi-check-circle me-1"></i><span>${defaultLabel}</span>`;
        familySubmitBtn.dataset.defaultLabel = defaultLabel;
    };

    const bindLocationGroup = (prefix) => {
        const province = document.getElementById(`${prefix}_province`);
        const district = document.getElementById(`${prefix}_district`);
        const subdistrict = document.getElementById(`${prefix}_subdistrict`);
        const zipcode = document.getElementById(`${prefix}_zipcode`);

        if (!province || !district || !subdistrict || !zipcode) return;

        const selectedProvince = province.dataset.selected || '';
        const selectedDistrict = district.dataset.selected || '';
        const selectedSubdistrict = subdistrict.dataset.selected || '';

        province.addEventListener('change', async function () {
            const provinceId = this.value;

            setOptions(district, [], '--เลือกอำเภอ--', 'dist_name');
            setOptions(subdistrict, [], '--เลือกตำบล--', 'subd_name');
            zipcode.value = '';

            if (!provinceId) return;

            try {
                const districts = await fetchJSON(`/get-districts/${provinceId}`);
                setOptions(district, districts, '--เลือกอำเภอ--', 'dist_name');
            } catch (error) {
                console.error('Load districts failed:', error);
            }
        });

        district.addEventListener('change', async function () {
            const districtId = this.value;

            setOptions(subdistrict, [], '--เลือกตำบล--', 'subd_name');
            zipcode.value = '';

            if (!districtId) return;

            try {
                const subdistricts = await fetchJSON(`/get-subdistricts/${districtId}`);
                setOptions(subdistrict, subdistricts, '--เลือกตำบล--', 'subd_name');
            } catch (error) {
                console.error('Load subdistricts failed:', error);
            }
        });

        subdistrict.addEventListener('change', async function () {
            const subdistrictId = this.value;
            zipcode.value = '';

            if (!subdistrictId) return;

            try {
                const zip = await fetchJSON(`/get-zipcode/${subdistrictId}`);
                zipcode.value = zip.zipcode || '';
            } catch (error) {
                console.error('Load zipcode failed:', error);
            }
        });

        const preload = async () => {
            try {
                if (selectedProvince) {
                    province.value = selectedProvince;

                    const districts = await fetchJSON(`/get-districts/${selectedProvince}`);
                    setOptions(district, districts, '--เลือกอำเภอ--', 'dist_name', selectedDistrict);
                }

                if (selectedDistrict) {
                    const subdistricts = await fetchJSON(`/get-subdistricts/${selectedDistrict}`);
                    setOptions(subdistrict, subdistricts, '--เลือกตำบล--', 'subd_name', selectedSubdistrict);
                }

                if (selectedSubdistrict) {
                    const zip = await fetchJSON(`/get-zipcode/${selectedSubdistrict}`);
                    zipcode.value = zip.zipcode || '';
                }
            } catch (error) {
                console.error(`Preload failed for ${prefix}:`, error);
            }
        };

        preload();
    };

    bindThaiIdCards();

    ['father', 'mother', 'spouse', 'relative'].forEach(bindLocationGroup);

    const savedTab = activeTabInput?.value || '';
    if (savedTab) {
        const trigger = document.getElementById(savedTab);
        if (trigger && window.bootstrap) {
            const tab = new bootstrap.Tab(trigger);
            tab.show();
        }
    }

    document.querySelectorAll('#familyTabs .nav-link').forEach(tabBtn => {
        tabBtn.addEventListener('shown.bs.tab', function () {
            if (activeTabInput) {
                activeTabInput.value = this.id;
            }
        });
    });

    if (familyTabs) {
        familyTabs.addEventListener('input', function (e) {
            const target = e.target;
            if (target.classList.contains('is-invalid')) {
                target.classList.remove('is-invalid');
            }
        });

        familyTabs.addEventListener('change', function (e) {
            const target = e.target;
            if (target.classList.contains('is-invalid')) {
                target.classList.remove('is-invalid');
            }
        });
    }

    if (familyForm) {
        familyForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            clearBanner();
            clearValidationErrors();

            bindThaiIdCards();

            const activeTab = document.querySelector('#familyTabs .nav-link.active');
            if (activeTabInput && activeTab) {
                activeTabInput.value = activeTab.id;
            }

            setSubmitButtonState(true);

            try {
                const formData = new FormData(familyForm);

                const response = await fetch(familyForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.status === 422) {
                    const errors = data.errors || {};
                    const firstField = Object.keys(errors)[0] || null;

                    if (firstField) {
                        activateTabByField(firstField);
                    }

                    Object.entries(errors).forEach(([field, messages]) => {
                        const inputName = field.replace(/\.(\w+)/g, '[$1]');
                        const input = familyForm.querySelector(`[name="${inputName}"]`);
                        appendFieldError(input, Array.isArray(messages) ? messages[0] : messages);
                    });

                    showBanner(data.message || 'กรุณาตรวจสอบข้อมูลที่กรอกอีกครั้ง', 'error');
                    return;
                }

                if (!response.ok || !data.success) {
                    showBanner(data.message || 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                    return;
                }

                if (familyStatusBadge) {
                    familyStatusBadge.innerHTML = '<i class="bi bi-shield-check"></i><span>โหมดแก้ไขข้อมูล</span>';
                }

                setSubmitButtonState(false, true);
                showBanner(data.message || 'บันทึกข้อมูลสำเร็จ', 'success');

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: data.message || 'บันทึกข้อมูลสำเร็จ',
                        timer: 1600,
                        showConfirmButton: false
                    });
                }
            } catch (error) {
                console.error('Family submit failed:', error);
                showBanner('เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง', 'error');
            } finally {
                setSubmitButtonState(false, familySubmitBtn?.dataset.defaultLabel === 'แก้ไขข้อมูล');
            }
        });
    }
});
</script>

@endsection

