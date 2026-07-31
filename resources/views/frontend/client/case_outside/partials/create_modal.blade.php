<style>
    /*
    |--------------------------------------------------------------------------
    | Create Case Outside Modal
    |--------------------------------------------------------------------------
    | CSS ทุกส่วนถูกจำกัดอยู่ภายใน #createCaseOutsideModal
    | จึงไม่กระทบฟอร์ม Modal หรือหน้าอื่น
    */

    #createCaseOutsideModal {
        --co-primary: #166534;
        --co-primary-dark: #14532d;
        --co-primary-soft: #ecfdf3;
        --co-primary-border: #bbf7d0;

        --co-text: #172033;
        --co-text-muted: #64748b;
        --co-border: #dbe3ec;
        --co-border-soft: #e9eef5;
        --co-surface: #ffffff;
        --co-surface-soft: #f8fafc;

        --co-danger: #dc3545;
        --co-danger-soft: #fff7f7;

        --co-radius-lg: 22px;
        --co-radius-md: 15px;
        --co-radius-sm: 11px;

        --co-shadow:
            0 24px 60px rgba(15, 23, 42, 0.16),
            0 6px 18px rgba(15, 23, 42, 0.08);

        color: var(--co-text);
    }

    /*
    |--------------------------------------------------------------------------
    | Modal structure
    |--------------------------------------------------------------------------
    */

    #createCaseOutsideModal .modal-dialog {
        width: calc(100% - 2rem);
        max-width: 920px;
        margin-right: auto;
        margin-left: auto;
    }

    #createCaseOutsideModal .modal-content {
        overflow: hidden;
        border: 0;
        border-radius: var(--co-radius-lg);
        background: var(--co-surface);
        box-shadow: var(--co-shadow);
    }

    #createCaseOutsideModal .co-modal-form {
        display: flex;
        min-height: 0;
        flex: 1 1 auto;
        flex-direction: column;
    }

    /*
    |--------------------------------------------------------------------------
    | Header
    |--------------------------------------------------------------------------
    */

    #createCaseOutsideModal .modal-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--co-border-soft);
        background:
            linear-gradient(
                135deg,
                rgba(236, 253, 243, 0.95) 0%,
                rgba(255, 255, 255, 1) 72%
            );
    }

    #createCaseOutsideModal .co-modal-heading {
        display: flex;
        min-width: 0;
        flex: 1 1 auto;
        align-items: center;
        gap: .9rem;
    }

    #createCaseOutsideModal .co-modal-heading-icon {
        display: inline-flex;
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--co-primary-border);
        border-radius: 14px;
        background: var(--co-primary-soft);
        color: var(--co-primary);
        font-size: 1.25rem;
    }

    #createCaseOutsideModal .co-modal-heading-content {
        min-width: 0;
    }

    #createCaseOutsideModal .co-modal-title {
        display: block;
        margin: 0;
        color: var(--co-text);
        font-size: 1.08rem;
        font-weight: 750;
        line-height: 1.35;
    }

    #createCaseOutsideModal .co-modal-subtitle {
        margin: .22rem 0 0;
        color: var(--co-text-muted);
        font-size: .84rem;
        line-height: 1.5;
    }

    #createCaseOutsideModal .btn-close {
        flex: 0 0 auto;
        margin: 0;
        padding: .65rem;
        border-radius: 10px;
        box-shadow: none;
        opacity: .65;
        transition:
            background-color .18s ease,
            opacity .18s ease;
    }

    #createCaseOutsideModal .btn-close:hover {
        background-color: rgba(15, 23, 42, 0.07);
        opacity: 1;
    }

    #createCaseOutsideModal .btn-close:focus {
        box-shadow: 0 0 0 .2rem rgba(22, 101, 52, 0.14);
    }

    /*
    |--------------------------------------------------------------------------
    | Body and sections
    |--------------------------------------------------------------------------
    */

    #createCaseOutsideModal .co-modal-body {
        overflow-y: auto;
        padding: 1.4rem 1.5rem;
        background: var(--co-surface-soft);
        overscroll-behavior: contain;
    }

    #createCaseOutsideModal .co-section {
        padding: 1.25rem;
        border: 1px solid var(--co-border-soft);
        border-radius: var(--co-radius-md);
        background: var(--co-surface);
    }

    #createCaseOutsideModal .co-section + .co-section {
        margin-top: 1rem;
    }

    #createCaseOutsideModal .co-section-header {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        margin-bottom: 1.05rem;
    }

    #createCaseOutsideModal .co-section-icon {
        display: inline-flex;
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: var(--co-primary-soft);
        color: var(--co-primary);
        font-size: 1rem;
    }

    #createCaseOutsideModal .co-section-heading {
        min-width: 0;
        padding-top: .05rem;
    }

    #createCaseOutsideModal .co-section-title {
        margin: 0;
        color: var(--co-text);
        font-size: .98rem;
        font-weight: 750;
        line-height: 1.4;
    }

    #createCaseOutsideModal .co-section-description {
        margin: .15rem 0 0;
        color: var(--co-text-muted);
        font-size: .8rem;
        line-height: 1.5;
    }

    /*
    |--------------------------------------------------------------------------
    | Form controls
    |--------------------------------------------------------------------------
    */

    #createCaseOutsideModal .co-form-group {
        position: relative;
    }

    #createCaseOutsideModal .co-label {
        display: inline-flex;
        align-items: center;
        gap: .22rem;
        margin-bottom: .45rem;
        color: #334155;
        font-size: .86rem;
        font-weight: 700;
        line-height: 1.4;
    }

    #createCaseOutsideModal .co-required {
        color: var(--co-danger);
        font-size: .8rem;
    }

    #createCaseOutsideModal .co-control,
    #createCaseOutsideModal .co-select,
    #createCaseOutsideModal .co-textarea {
        width: 100%;
        border: 1px solid var(--co-border);
        border-radius: var(--co-radius-sm);
        background-color: #ffffff;
        color: var(--co-text);
        font-size: .9rem;
        box-shadow: none;
        transition:
            border-color .18s ease,
            box-shadow .18s ease,
            background-color .18s ease;
    }

    #createCaseOutsideModal .co-control,
    #createCaseOutsideModal .co-select {
        min-height: 46px;
        padding: .68rem .85rem;
    }

    #createCaseOutsideModal .co-textarea {
        min-height: 104px;
        padding: .75rem .85rem;
        line-height: 1.65;
        resize: vertical;
    }

    #createCaseOutsideModal .co-control:hover,
    #createCaseOutsideModal .co-select:hover,
    #createCaseOutsideModal .co-textarea:hover {
        border-color: #b7c3d1;
    }

    #createCaseOutsideModal .co-control:focus,
    #createCaseOutsideModal .co-select:focus,
    #createCaseOutsideModal .co-textarea:focus {
        border-color: var(--co-primary);
        background-color: #ffffff;
        box-shadow: 0 0 0 .2rem rgba(22, 101, 52, 0.12);
        outline: 0;
    }

    #createCaseOutsideModal .co-control::placeholder,
    #createCaseOutsideModal .co-textarea::placeholder {
        color: #94a3b8;
    }

    #createCaseOutsideModal .co-field-help {
        display: block;
        margin-top: .4rem;
        color: var(--co-text-muted);
        font-size: .77rem;
        line-height: 1.45;
    }

    /*
    |--------------------------------------------------------------------------
    | Radio option cards
    |--------------------------------------------------------------------------
    */

    #createCaseOutsideModal .co-option-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .75rem;
    }

    #createCaseOutsideModal .co-option {
        position: relative;
        min-width: 0;
    }

    #createCaseOutsideModal .co-option-input {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        opacity: 0;
        pointer-events: none;
    }

    #createCaseOutsideModal .co-option-label {
        display: flex;
        min-height: 76px;
        margin: 0;
        padding: .85rem .9rem;
        border: 1px solid var(--co-border);
        border-radius: 13px;
        background: #ffffff;
        cursor: pointer;
        align-items: center;
        gap: .7rem;
        color: #334155;
        font-size: .86rem;
        font-weight: 700;
        line-height: 1.35;
        transition:
            border-color .18s ease,
            background-color .18s ease,
            box-shadow .18s ease,
            transform .18s ease;
    }

    #createCaseOutsideModal .co-option-label:hover {
        border-color: #86b99a;
        background: #fbfffc;
        transform: translateY(-1px);
    }

    #createCaseOutsideModal .co-option-icon {
        display: inline-flex;
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #f1f5f9;
        color: #64748b;
        font-size: 1rem;
        transition:
            background-color .18s ease,
            color .18s ease;
    }

    #createCaseOutsideModal .co-option-text {
        min-width: 0;
        flex: 1 1 auto;
    }

    #createCaseOutsideModal .co-option-check {
        display: inline-flex;
        width: 22px;
        height: 22px;
        flex: 0 0 22px;
        align-items: center;
        justify-content: center;
        border: 1.5px solid #cbd5e1;
        border-radius: 50%;
        background: #ffffff;
        color: transparent;
        font-size: .75rem;
        transition:
            border-color .18s ease,
            background-color .18s ease,
            color .18s ease;
    }

    #createCaseOutsideModal .co-option-input:checked + .co-option-label {
        border-color: var(--co-primary);
        background: var(--co-primary-soft);
        color: var(--co-primary-dark);
        box-shadow: 0 0 0 .18rem rgba(22, 101, 52, 0.09);
    }

    #createCaseOutsideModal
        .co-option-input:checked
        + .co-option-label
        .co-option-icon {
        background: var(--co-primary);
        color: #ffffff;
    }

    #createCaseOutsideModal
        .co-option-input:checked
        + .co-option-label
        .co-option-check {
        border-color: var(--co-primary);
        background: var(--co-primary);
        color: #ffffff;
    }

    #createCaseOutsideModal
        .co-option-input:focus-visible
        + .co-option-label {
        border-color: var(--co-primary);
        box-shadow: 0 0 0 .22rem rgba(22, 101, 52, 0.14);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    #createCaseOutsideModal .is-invalid,
    #createCaseOutsideModal .was-validated .form-control:invalid,
    #createCaseOutsideModal .was-validated .form-select:invalid {
        border-color: var(--co-danger) !important;
        background-color: var(--co-danger-soft);
        box-shadow: 0 0 0 .18rem rgba(220, 53, 69, 0.09) !important;
    }

    #createCaseOutsideModal .co-invalid-text {
        display: block;
        width: 100%;
        margin-top: .42rem;
        color: var(--co-danger);
        font-size: .78rem;
        font-weight: 650;
        line-height: 1.45;
    }

    #createCaseOutsideModal .co-section.co-section-error {
        border-color: rgba(220, 53, 69, 0.55);
        box-shadow: 0 0 0 .18rem rgba(220, 53, 69, 0.06);
    }

    #createCaseOutsideModal
        .co-section-error
        .co-option-label {
        border-color: rgba(220, 53, 69, 0.48);
        background: var(--co-danger-soft);
    }

    /*
    |--------------------------------------------------------------------------
    | Footer and buttons
    |--------------------------------------------------------------------------
    */

    #createCaseOutsideModal .co-modal-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .7rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--co-border-soft);
        background: #ffffff;
    }

    #createCaseOutsideModal .co-btn {
        display: inline-flex;
        min-height: 43px;
        min-width: 128px;
        padding: .62rem 1rem;
        border-radius: 11px;
        align-items: center;
        justify-content: center;
        gap: .48rem;
        font-size: .86rem;
        font-weight: 700;
        line-height: 1.2;
        box-shadow: none;
        transition:
            transform .18s ease,
            border-color .18s ease,
            background-color .18s ease,
            box-shadow .18s ease;
    }

    #createCaseOutsideModal .co-btn:hover {
        transform: translateY(-1px);
    }

    #createCaseOutsideModal .co-btn:focus {
        box-shadow: 0 0 0 .2rem rgba(22, 101, 52, 0.13);
    }

    #createCaseOutsideModal .co-btn-cancel {
        border-color: #cbd5e1;
        background: #ffffff;
        color: #475569;
    }

    #createCaseOutsideModal .co-btn-cancel:hover {
        border-color: #94a3b8;
        background: #f8fafc;
        color: #1e293b;
    }

    #createCaseOutsideModal .co-btn-save {
        border-color: var(--co-primary);
        background: var(--co-primary);
        color: #ffffff;
    }

    #createCaseOutsideModal .co-btn-save:hover {
        border-color: var(--co-primary-dark);
        background: var(--co-primary-dark);
        color: #ffffff;
        box-shadow: 0 7px 18px rgba(22, 101, 52, 0.2);
    }

    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 767.98px) {
        #createCaseOutsideModal .modal-dialog {
            width: 100%;
            max-width: none;
            margin: 0;
        }

        #createCaseOutsideModal .modal-content {
            border-radius: 0;
        }

        #createCaseOutsideModal .modal-header {
            padding: 1rem;
        }

        #createCaseOutsideModal .co-modal-body {
            padding: 1rem;
        }

        #createCaseOutsideModal .co-section {
            padding: 1rem;
        }

        #createCaseOutsideModal .co-modal-footer {
            padding: .85rem 1rem;
        }

        #createCaseOutsideModal .co-option-grid {
            grid-template-columns: 1fr;
        }

        #createCaseOutsideModal .co-option-label {
            min-height: 62px;
        }
    }

    @media (max-width: 575.98px) {
        #createCaseOutsideModal .co-modal-heading-icon {
            width: 42px;
            height: 42px;
            flex-basis: 42px;
        }

        #createCaseOutsideModal .co-modal-subtitle {
            display: none;
        }

        #createCaseOutsideModal .co-section-header {
            margin-bottom: .9rem;
        }

        #createCaseOutsideModal .co-modal-footer {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        #createCaseOutsideModal .co-btn {
            width: 100%;
            min-width: 0;
        }
    }

    @media (max-width: 380px) {
        #createCaseOutsideModal .co-modal-footer {
            grid-template-columns: 1fr;
        }

        #createCaseOutsideModal .co-btn-save {
            order: -1;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        #createCaseOutsideModal *,
        #createCaseOutsideModal *::before,
        #createCaseOutsideModal *::after {
            scroll-behavior: auto !important;
            transition-duration: .01ms !important;
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
        }
    }
</style>


<div class="modal fade co-modal co-page co-modal-create"
     id="createCaseOutsideModal"
     tabindex="-1"
     aria-labelledby="createCaseOutsideModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">

            <form id="createCaseOutsideForm"
                  action="{{ route('case_outside.store') }}"
                  method="POST"
                  class="co-modal-form"
                  novalidate>

                @csrf

                <input type="hidden"
                       name="client_id"
                       value="{{ old('client_id', $client->id) }}">

                <input type="hidden"
                       name="_form_context"
                       value="create_case_outside">

                {{-- Modal Header --}}
                <div class="modal-header">
                    <div class="co-modal-heading">
                        <div class="co-modal-heading-icon" aria-hidden="true">
                            <i class="bi bi-person-check"></i>
                        </div>

                        <div class="co-modal-heading-content">
                            <h5 class="co-modal-title"
                                id="createCaseOutsideModalLabel">
                                เพิ่มข้อมูลการติดตาม
                            </h5>

                            <p class="co-modal-subtitle">
                                บันทึกข้อมูลการติดตามผู้รับบริการที่พักอาศัยอยู่ภายนอก
                            </p>
                        </div>
                    </div>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="ปิดหน้าต่าง">
                    </button>
                </div>


                {{-- Modal Body --}}
                <div class="modal-body co-modal-body">

                    {{-- ข้อมูลพื้นฐาน --}}
                    <section class="co-section"
                             aria-labelledby="createBasicInformationTitle">

                        <div class="co-section-header">
                            <div class="co-section-icon" aria-hidden="true">
                                <i class="bi bi-ui-checks-grid"></i>
                            </div>

                            <div class="co-section-heading">
                                <h6 class="co-section-title"
                                    id="createBasicInformationTitle">
                                    ข้อมูลพื้นฐาน
                                </h6>

                                <p class="co-section-description">
                                    ระบุวันที่ สาเหตุ และสถานที่พักของผู้รับบริการ
                                </p>
                            </div>
                        </div>

                        <div class="row g-3">

                            {{-- วันที่ติดตาม --}}
                            <div class="col-12 col-md-4">
                                <div class="co-form-group">
                                    <label for="create_case_outside_date"
                                           class="co-label">
                                        วันที่ติดตาม
                                        <span class="co-required"
                                              aria-hidden="true">*</span>
                                    </label>

                                    <input type="date"
                                           id="create_case_outside_date"
                                           name="date"
                                           value="{{ old('date', now('Asia/Bangkok')->toDateString()) }}"
                                           max="{{ now('Asia/Bangkok')->toDateString() }}"
                                           class="form-control co-control @error('date') is-invalid @enderror"
                                           aria-describedby="create_case_outside_date_error"
                                           aria-invalid="{{ $errors->has('date') ? 'true' : 'false' }}"
                                           required>

                                    @error('date')
                                        <div id="create_case_outside_date_error"
                                             class="co-invalid-text"
                                             role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>


                            {{-- สาเหตุ --}}
                            <div class="col-12 col-md-8">
                                <div class="co-form-group">
                                    <label for="create_case_outside_outside_id"
                                           class="co-label">
                                        สาเหตุที่พักอาศัยอยู่ภายนอก
                                        <span class="co-required"
                                              aria-hidden="true">*</span>
                                    </label>

                                    <select id="create_case_outside_outside_id"
                                            name="outside_id"
                                            class="form-select co-select @error('outside_id') is-invalid @enderror"
                                            aria-describedby="create_case_outside_outside_id_error"
                                            aria-invalid="{{ $errors->has('outside_id') ? 'true' : 'false' }}"
                                            required>

                                        <option value="">
                                            -- เลือกสาเหตุ --
                                        </option>

                                        @forelse($outside as $o)
                                            <option value="{{ $o->id }}"
                                                {{ (string) old('outside_id') === (string) $o->id ? 'selected' : '' }}>
                                                {{ $o->outside_name }}
                                            </option>
                                        @empty
                                            <option value="" disabled>
                                                ไม่พบข้อมูลสาเหตุ
                                            </option>
                                        @endforelse
                                    </select>

                                    @error('outside_id')
                                        <div id="create_case_outside_outside_id_error"
                                             class="co-invalid-text"
                                             role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>


                            {{-- สถานที่พัก --}}
                            <div class="col-12">
                                <div class="co-form-group">
                                    <label for="create_case_outside_dormitory"
                                           class="co-label">
                                        สถานที่พัก
                                        <span class="co-required"
                                              aria-hidden="true">*</span>
                                    </label>

                                    <input type="text"
                                           id="create_case_outside_dormitory"
                                           name="dormitory"
                                           value="{{ old('dormitory') }}"
                                           class="form-control co-control @error('dormitory') is-invalid @enderror"
                                           placeholder="ระบุชื่อสถานที่พัก บ้านพัก หรือที่อยู่โดยสังเขป"
                                           maxlength="255"
                                           autocomplete="off"
                                           aria-describedby="create_case_outside_dormitory_error"
                                           aria-invalid="{{ $errors->has('dormitory') ? 'true' : 'false' }}"
                                           required>

                                    @error('dormitory')
                                        <div id="create_case_outside_dormitory_error"
                                             class="co-invalid-text"
                                             role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </section>


                    {{-- การดำเนินงาน --}}
                    <section id="createCaseOutsideFollowSection"
                             class="co-section @error('follo_no') co-section-error @enderror"
                             aria-labelledby="createFollowMethodTitle">

                        <div class="co-section-header">
                            <div class="co-section-icon" aria-hidden="true">
                                <i class="bi bi-check2-square"></i>
                            </div>

                            <div class="co-section-heading">
                                <h6 class="co-section-title"
                                    id="createFollowMethodTitle">
                                    การดำเนินงาน
                                </h6>

                                <p class="co-section-description">
                                    เลือกวิธีที่ใช้ในการติดตามผู้รับบริการ
                                </p>
                            </div>
                        </div>

                        @php
                            $followOptions = [
                                [
                                    'value' => 'หน่วยงานไปเอง',
                                    'icon'  => 'bi-building-check',
                                ],
                                [
                                    'value' => 'โทรศัพท์',
                                    'icon'  => 'bi-telephone-outbound',
                                ],
                                [
                                    'value' => 'จดหมาย',
                                    'icon'  => 'bi-envelope-paper',
                                ],
                            ];
                        @endphp

                        <div class="co-option-grid"
                             role="radiogroup"
                             aria-labelledby="createFollowMethodTitle">

                            @foreach($followOptions as $followOption)
                                @php
                                    $createOptionId = 'create_follo_no_' . $loop->index;
                                @endphp

                                <div class="co-option">
                                    <input type="radio"
                                           class="co-option-input"
                                           name="follo_no"
                                           id="{{ $createOptionId }}"
                                           value="{{ $followOption['value'] }}"
                                           {{ old('follo_no') === $followOption['value'] ? 'checked' : '' }}
                                           required>

                                    <label class="co-option-label"
                                           for="{{ $createOptionId }}">

                                        <span class="co-option-icon"
                                              aria-hidden="true">
                                            <i class="bi {{ $followOption['icon'] }}"></i>
                                        </span>

                                        <span class="co-option-text">
                                            {{ $followOption['value'] }}
                                        </span>

                                        <span class="co-option-check"
                                              aria-hidden="true">
                                            <i class="bi bi-check-lg"></i>
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        @error('follo_no')
                            <div class="co-invalid-text"
                                 role="alert">
                                {{ $message }}
                            </div>
                        @enderror

                        <div id="createCaseOutsideFollowClientError"
                             class="co-invalid-text d-none"
                             role="alert">
                            กรุณาเลือกวิธีการดำเนินงาน
                        </div>
                    </section>


                    {{-- ผลการติดตาม --}}
                    <section class="co-section"
                             aria-labelledby="createFollowResultTitle">

                        <div class="co-section-header">
                            <div class="co-section-icon" aria-hidden="true">
                                <i class="bi bi-card-text"></i>
                            </div>

                            <div class="co-section-heading">
                                <h6 class="co-section-title"
                                    id="createFollowResultTitle">
                                    ผลการติดตามและรายละเอียดเพิ่มเติม
                                </h6>

                                <p class="co-section-description">
                                    บันทึกผลการดำเนินงาน ผู้ติดตาม และหมายเหตุที่เกี่ยวข้อง
                                </p>
                            </div>
                        </div>

                        <div class="row g-3">

                            {{-- ผลการติดตาม --}}
                            <div class="col-12">
                                <div class="co-form-group">
                                    <label for="create_case_outside_results"
                                           class="co-label">
                                        ผลการติดตาม
                                        <span class="co-required"
                                              aria-hidden="true">*</span>
                                    </label>

                                    <textarea id="create_case_outside_results"
                                              name="results"
                                              class="form-control co-textarea @error('results') is-invalid @enderror"
                                              rows="4"
                                              maxlength="2000"
                                              placeholder="ระบุผลที่ได้รับจากการติดตามและการดำเนินงาน"
                                              aria-describedby="create_case_outside_results_error"
                                              aria-invalid="{{ $errors->has('results') ? 'true' : 'false' }}"
                                              required>{{ old('results') }}</textarea>

                                    @error('results')
                                        <div id="create_case_outside_results_error"
                                             class="co-invalid-text"
                                             role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>


                            {{-- ผู้ติดตาม --}}
                            <div class="col-12 col-md-6">
                                <div class="co-form-group">
                                    <label for="create_case_outside_teacher"
                                           class="co-label">
                                        ผู้ติดตาม
                                    </label>

                                    <input type="text"
                                           id="create_case_outside_teacher"
                                           name="teacher"
                                           value="{{ old('teacher') }}"
                                           class="form-control co-control @error('teacher') is-invalid @enderror"
                                           placeholder="ชื่อผู้ดำเนินการติดตาม"
                                           maxlength="255"
                                           autocomplete="off"
                                           aria-describedby="create_case_outside_teacher_error"
                                           aria-invalid="{{ $errors->has('teacher') ? 'true' : 'false' }}">

                                    @error('teacher')
                                        <div id="create_case_outside_teacher_error"
                                             class="co-invalid-text"
                                             role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>


                            {{-- หมายเหตุ --}}
                            <div class="col-12">
                                <div class="co-form-group">
                                    <label for="create_case_outside_remerk"
                                           class="co-label">
                                        หมายเหตุ
                                    </label>

                                    <textarea id="create_case_outside_remerk"
                                              name="remerk"
                                              class="form-control co-textarea @error('remerk') is-invalid @enderror"
                                              rows="3"
                                              maxlength="2000"
                                              placeholder="ระบุข้อมูลเพิ่มเติม หากมี"
                                              aria-describedby="create_case_outside_remerk_error"
                                              aria-invalid="{{ $errors->has('remerk') ? 'true' : 'false' }}">{{ old('remerk') }}</textarea>

                                    @error('remerk')
                                        <div id="create_case_outside_remerk_error"
                                             class="co-invalid-text"
                                             role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </section>

                </div>


                {{-- Modal Footer --}}
                <div class="modal-footer co-modal-footer">
                    <button type="button"
                            class="btn co-btn co-btn-cancel"
                            data-bs-dismiss="modal">
                        <i class="bi bi-x-circle" aria-hidden="true"></i>
                        <span>ยกเลิก</span>
                    </button>

                    <button type="submit"
                            class="btn co-btn co-btn-save">
                        <i class="bi bi-save2" aria-hidden="true"></i>
                        <span>บันทึกข้อมูล</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalElement = document.getElementById('createCaseOutsideModal');
        const form = document.getElementById('createCaseOutsideForm');
        const followSection = document.getElementById('createCaseOutsideFollowSection');
        const followClientError = document.getElementById('createCaseOutsideFollowClientError');

        if (!modalElement || !form) {
            return;
        }

        const followInputs = form.querySelectorAll('input[name="follo_no"]');

        /**
         * ตรวจสอบว่ามีการเลือกวิธีติดตามหรือไม่
         */
        function validateFollowMethod() {
            const isSelected = Array.from(followInputs).some(function (input) {
                return input.checked;
            });

            if (followSection) {
                followSection.classList.toggle('co-section-error', !isSelected);
            }

            if (followClientError) {
                followClientError.classList.toggle('d-none', isSelected);
            }

            return isSelected;
        }

        /**
         * ล้างสถานะ error เมื่อผู้ใช้แก้ไขข้อมูล
         */
        form.querySelectorAll('.form-control, .form-select').forEach(function (field) {
            field.addEventListener('input', function () {
                if (field.checkValidity()) {
                    field.classList.remove('is-invalid');
                    field.setAttribute('aria-invalid', 'false');
                }
            });

            field.addEventListener('change', function () {
                if (field.checkValidity()) {
                    field.classList.remove('is-invalid');
                    field.setAttribute('aria-invalid', 'false');
                }
            });
        });

        followInputs.forEach(function (input) {
            input.addEventListener('change', function () {
                validateFollowMethod();
            });
        });

        /**
         * ตรวจสอบฟอร์มก่อนส่ง
         */
        form.addEventListener('submit', function (event) {
            const followIsValid = validateFollowMethod();

            form.classList.add('was-validated');

            if (!form.checkValidity() || !followIsValid) {
                event.preventDefault();
                event.stopPropagation();

                const firstInvalidField =
                    form.querySelector('.form-control:invalid, .form-select:invalid') ||
                    form.querySelector('input[name="follo_no"]');

                if (firstInvalidField) {
                    firstInvalidField.focus({
                        preventScroll: true
                    });

                    firstInvalidField.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }

                return;
            }

            const submitButton = form.querySelector('button[type="submit"]');

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm"
                          aria-hidden="true"></span>
                    <span>กำลังบันทึก...</span>
                `;
            }
        });

        /**
         * เมื่อเปิด Modal ให้โฟกัสช่องวันที่
         */
        modalElement.addEventListener('shown.bs.modal', function () {
            const firstField = document.getElementById('create_case_outside_date');

            if (firstField) {
                firstField.focus();
            }
        });

        /**
         * เปิด Modal กลับมาอัตโนมัติเมื่อ validation จาก Controller ไม่ผ่าน
         */
        @if (
            $errors->any() &&
            old('_form_context') === 'create_case_outside'
        )
            if (window.bootstrap && bootstrap.Modal) {
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
                modalInstance.show();
            }
        @endif
    });
</script>