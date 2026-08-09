@extends('admin_client.admin_client')
@section('content')

    <style>
        .estimate-page {
            --estimate-border: #d9e0ea;
            --estimate-soft: #f8fafc;
            --estimate-soft-2: #f8fbff;
            --estimate-text: #0f172a;
            --estimate-muted: #64748b;
            --estimate-primary: #2563eb;
        }

        .estimate-page .estimate-card {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
            overflow: hidden;
            background: #fff;
        }

        .estimate-page .estimate-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 16px 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border-bottom: 1px solid var(--estimate-border);
            flex-wrap: wrap;
        }

        .estimate-page .estimate-header-left {
            display: flex;
            align-items: center;
            gap: .85rem;
            min-width: 0;
        }

        .estimate-page .estimate-header-icon {
            width: 44px;
            height: 44px;
            border-radius: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 44px;
            background: #eff6ff;
            color: #2563eb;
        }

        .estimate-page .estimate-header-icon i {
            font-size: 1.05rem;
        }

        .estimate-page .estimate-header-text {
            min-width: 0;
        }

        .estimate-page .estimate-title {
            margin: 0;
            color: var(--estimate-text);
            font-size: clamp(1.25rem, 1.6vw, 1.5rem);
            font-weight: 800;
            line-height: 1.35;
            letter-spacing: -0.01em;
        }

        .estimate-page .estimate-subtitle {
            margin-top: .3rem;
            color: var(--estimate-muted);
            font-size: clamp(.92rem, 1vw, 1rem);
            font-weight: 500;
            line-height: 1.45;
        }

        .estimate-page .estimate-subtitle strong {
            color: var(--estimate-text);
            font-weight: 800;
        }

        .estimate-page .estimate-header-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: .65rem;
            flex-wrap: wrap;
        }

        .estimate-page .estimate-back-btn {
            min-width: 96px;
            color: #7c3aed;
            background: #fff;
            border-color: #8b5cf6;
            box-shadow: none;
        }

        .estimate-page .estimate-back-btn:hover,
        .estimate-page .estimate-back-btn:focus {
            color: #6d28d9;
            background: #faf5ff;
            border-color: #7c3aed;
            box-shadow: 0 6px 14px rgba(124, 58, 237, .12);
        }

        .estimate-page .estimate-table-wrap {
            padding: 16px 18px 20px;
            background: #fff;
        }

        .estimate-page .estimate-empty {
            margin: 0;
            border-radius: 14px;
        }

        .estimate-page .table-responsive {
            border: 1px solid var(--estimate-border);
            border-radius: 14px;
            overflow: auto;
            background: #fff;
        }

        .estimate-page #datatable-estimate {
            margin-bottom: 0 !important;
            min-width: 1200px;
        }

        .estimate-page #datatable-estimate thead th {
            background: #eff6ff;
            color: #1e3a8a;
            font-weight: 800;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
            border-bottom: 1px solid #dbeafe;
        }

        .estimate-page #datatable-estimate td {
            vertical-align: top;
            color: var(--estimate-text);
        }

        .estimate-page .estimate-col-actions {
            white-space: nowrap;
            min-width: 150px;
        }

        .estimate-page .estimate-photo-thumb {
            width: 80px;
        }

        .estimate-page .estimate-photo-thumb img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .estimate-page .estimate-summary-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 6px;
            min-width: 260px;
        }

        .estimate-page .estimate-summary-list .label {
            font-weight: 700;
            color: #334155;
        }

        .estimate-page .estimate-summary-list .value {
            color: #0f172a;
            word-break: break-word;
        }

        .estimate-page .modal {
            overflow-y: auto;
        }

        .estimate-page .modal.fade .modal-dialog {
            transition: transform .2s ease-out;
        }

        .estimate-page .modal-dialog {
            max-width: 920px;
            width: calc(100% - 2rem);
            margin: 1rem auto;
            height: calc(100vh - 2rem);
        }

        .estimate-page .modal-content {
            border: 0;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(15, 23, 42, .18);
            height: 100%;
            max-height: 100%;
            display: flex;
            flex-direction: column;
        }

        .estimate-page .modal-header {
            padding: 14px 18px;
            flex: 0 0 auto;
            border-bottom: 1px solid var(--estimate-border);
        }

        .estimate-page .modal-body {
            padding: 18px;
            background: #fff;
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .estimate-page .modal-footer {
            padding: 14px 18px;
            border-top: 1px solid var(--estimate-border);
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            background: #f8fafc;
        }

        /* =============================================================
           ปุ่มคำสั่งหลัก: เพิ่มข้อมูล / บันทึก / อัปเดต / ยกเลิก
        ============================================================== */
        .estimate-page .estimate-action-btn {
            min-width: 118px;
            min-height: 44px;
            padding: 9px 17px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid transparent;
            border-radius: 12px;
            font-size: .9rem;
            font-weight: 750;
            line-height: 1.2;
            letter-spacing: .01em;
            box-shadow: none;
            transition:
                transform .18s ease,
                box-shadow .18s ease,
                border-color .18s ease,
                background-color .18s ease,
                color .18s ease;
        }

        .estimate-page .estimate-action-btn i {
            font-size: 1rem;
            line-height: 1;
        }

        .estimate-page .estimate-action-btn:hover {
            transform: translateY(-1px);
        }

        .estimate-page .estimate-action-btn:active {
            transform: translateY(0);
        }

        .estimate-page .estimate-action-btn:focus-visible {
            outline: 0;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .16);
        }

        .estimate-page .estimate-add-btn {
            min-width: 132px;
            border-color: #1d4ed8;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            box-shadow: 0 8px 18px rgba(37, 99, 235, .20);
        }

        .estimate-page .estimate-add-btn:hover,
        .estimate-page .estimate-add-btn:focus {
            border-color: #1e40af;
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            color: #fff;
            box-shadow: 0 10px 22px rgba(37, 99, 235, .26);
        }

        .estimate-page .estimate-submit-btn {
            border-color: #1d4ed8;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            box-shadow: 0 7px 16px rgba(37, 99, 235, .18);
        }

        .estimate-page .estimate-submit-btn:hover,
        .estimate-page .estimate-submit-btn:focus {
            border-color: #1e40af;
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            color: #fff;
            box-shadow: 0 9px 20px rgba(37, 99, 235, .24);
        }

        .estimate-page .estimate-cancel-btn {
            border-color: #cbd5e1;
            background: #fff;
            color: #475569;
        }

        .estimate-page .estimate-cancel-btn:hover,
        .estimate-page .estimate-cancel-btn:focus {
            border-color: #94a3b8;
            background: #f1f5f9;
            color: #1e293b;
            box-shadow: 0 6px 14px rgba(15, 23, 42, .08);
        }

        .estimate-page .estimate-action-btn:disabled,
        .estimate-page .estimate-action-btn.disabled {
            transform: none;
            opacity: .62;
            cursor: not-allowed;
            box-shadow: none;
        }

        .estimate-page .estimate-form-section {
            padding: 16px;
            border: 1px solid var(--estimate-border);
            border-radius: 16px;
            background: var(--estimate-soft-2);
            margin-bottom: 14px;
        }

        .estimate-page .estimate-form-section-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--estimate-text);
            margin-bottom: 14px;
        }

        .estimate-page .form-label {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .estimate-page .form-control {
            min-height: 46px;
            border-radius: 14px;
            border: 1px solid #cbd5e1;
        }

        .estimate-page textarea.form-control {
            min-height: 96px;
            resize: vertical;
        }

        .estimate-page .estimate-inline-radio {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .estimate-page .estimate-radio-card {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 42px;
            padding: 8px 14px;
            border: 1px solid var(--estimate-border);
            border-radius: 14px;
            background: #fff;
            color: #111827;
            line-height: 1.3;
            cursor: pointer;
            flex: 0 0 auto;
            max-width: 100%;
        }

        .estimate-page .estimate-radio-card .form-check-input {
            margin: 0;
            flex: 0 0 auto;
        }

        .estimate-page .estimate-hidden {
            display: none !important;
        }

        .estimate-page .estimate-preview-item {
            width: 120px;
        }

        .estimate-page .estimate-preview-item img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .estimate-photo-thumb {
            width: 110px;
            height: 110px;
            overflow: hidden;
            border-radius: 12px;
            flex-shrink: 0;
        }

        .estimate-thumb-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #dee2e6;
            padding: 0;
            display: block;
            background: #f8fafc;
        }

        .estimate-page .estimate-image-status {
            min-height: 20px;
            margin-top: 6px;
            color: var(--estimate-muted);
        }

        .estimate-page .estimate-old-picture-item,
        .estimate-page .estimate-preview-item {
            position: relative;
            width: 92px;
            height: 92px;
            flex: 0 0 92px;
        }

        .estimate-page .estimate-old-picture-item img,
        .estimate-page .estimate-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        .estimate-page .estimate-picture-remove {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 28px;
            height: 28px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        @media(max-width:768px) {
            .estimate-photo-thumb {
                width: 90px;
                height: 90px;
            }
        }

        @media (max-width: 991.98px) {
            .estimate-page .modal-dialog {
                max-width: none;
                width: calc(100% - 1rem);
                margin: .5rem auto;
                height: calc(100vh - 1rem);
            }

            .estimate-page .modal-content {
                border-radius: 18px;
            }

            .estimate-page .modal-body,
            .estimate-page .modal-header,
            .estimate-page .modal-footer {
                padding: 14px;
            }

            .estimate-page .estimate-form-section {
                padding: 14px;
            }
        }

        @media (max-width: 767.98px) {

            .estimate-page .estimate-header,
            .estimate-page .estimate-client-info,
            .estimate-page .estimate-table-wrap {
                padding: 14px;
            }

            .estimate-page .estimate-header {
                align-items: stretch;
            }

            .estimate-page .estimate-header-left,
            .estimate-page .estimate-header-actions {
                width: 100%;
            }

            .estimate-page .estimate-header-actions > * {
                flex: 1 1 calc(50% - .35rem);
            }

            .estimate-page .estimate-title {
                font-size: 1.12rem;
                line-height: 1.35;
            }

            .estimate-page .estimate-subtitle {
                margin-top: .25rem;
                font-size: .9rem;
            }

            .estimate-page .modal {
                padding: 0 !important;
            }

            .estimate-page .modal-dialog {
                width: calc(100% - .5rem);
                margin: .25rem auto;
                height: calc(100vh - .5rem);
            }

            .estimate-page .modal-content {
                border-radius: 16px;
            }

            .estimate-page .modal-header {
                padding: 12px 12px 10px;
            }

            .estimate-page .modal-body {
                padding: 12px;
            }

            .estimate-page .modal-footer {
                padding: 10px 12px 12px;
                position: sticky;
                bottom: 0;
                z-index: 3;
                flex-wrap: nowrap;
                box-shadow: 0 -6px 18px rgba(15, 23, 42, .05);
            }

            .estimate-page .estimate-action-btn {
                min-width: 0;
                min-height: 44px;
                flex: 1 1 0;
                padding: 9px 12px;
            }

            .estimate-page .estimate-form-section {
                padding: 12px;
                border-radius: 14px;
            }

            .estimate-page .form-control {
                min-height: 44px;
                font-size: 16px;
            }

            .estimate-page textarea.form-control {
                min-height: 88px;
            }

            .estimate-page .estimate-inline-radio {
                flex-direction: column;
                gap: 8px;
            }

            .estimate-page .estimate-radio-card {
                width: 100%;
                justify-content: flex-start;
                padding: 10px 12px;
                border-radius: 12px;
            }

            .estimate-page .estimate-summary-list {
                min-width: auto;
            }
        }

        @media (max-width: 575.98px) {
            .estimate-page .estimate-header-actions {
                flex-direction: column;
            }

            .estimate-page .estimate-header-actions > * {
                width: 100%;
                flex: 1 1 auto;
            }
        }

    </style>

    <div class="estimate-page">

        <div class="estimate-card mt-3">
            <div class="estimate-header">
                <div class="estimate-header-left">
                    <div class="estimate-header-icon" aria-hidden="true">
                        <i class="bi bi-people-fill"></i>
                    </div>

                    <div class="estimate-header-text">
                        <h1 class="estimate-title">
                            ประวัติการติดตามและประเมินครอบครัวเด็ก
                        </h1>

                        <div class="estimate-subtitle">
                            ผู้รับบริการ:
                            <strong>{{ $client->full_name ?: '-' }}</strong>
                            <span class="mx-1">•</span>
                            อายุ:
                            <strong>{{ $client->birth_date ? \Carbon\Carbon::parse($client->birth_date)->age . ' ปี' : '-' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="estimate-header-actions">
                    <button type="button"
                        class="btn estimate-action-btn estimate-add-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#add-estimate-modal"
                        id="btn-add-estimate">
                        <i class="bi bi-plus-lg" aria-hidden="true"></i>
                        <span>เพิ่มข้อมูล</span>
                    </button>

                    <a href="{{ route('admin.index', $client->id) }}"
                       class="btn estimate-action-btn estimate-back-btn"
                       aria-label="กลับหน้าหลักผู้รับบริการ">
                        <i class="bi bi-arrow-left-circle" aria-hidden="true"></i>
                        <span>กลับ</span>
                    </a>
                </div>
            </div>

            <div class="estimate-table-wrap">
                @if ($client->estimates->isEmpty())
                    <div class="alert alert-info text-center estimate-empty">
                        ยังไม่มีข้อมูลการติดตามและประเมิน
                    </div>
                @else
                    <div class="table-responsive">
                        <table id="datatable-estimate" class="table table-bordered table-striped align-middle w-100">
                            <thead>
                                <tr>
                                    <th scope="col">ครั้งที่</th>
                                    <th scope="col">วันที่ติดตาม</th>
                                    <th scope="col">การดำเนินงาน</th>
                                    <th scope="col">ผลการติดตาม</th>
                                    <th scope="col">ข้อมูลครอบครัวเพิ่มเติม</th>
                                    <th scope="col">ผู้ประเมิน</th>
                                    <th scope="col">หมายเหตุ</th>
                                    <th scope="col">รูปภาพ</th>
                                    <th scope="col" class="text-center estimate-col-actions">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($client->estimates as $item)
                                    <tr>
                                        <td class="text-center">{{ $item->count }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}
                                        </td>
                                        <td>{{ $item->follo_no }}</td>
                                        <td>{{ $item->results ?: '-' }}</td>
                                        <td>
                                            <div class="estimate-summary-list">
                                                <div>
                                                    <span class="label">รายได้/เดือน:</span>
                                                    <span class="value">
                                                        {{ $item->family_income !== null ? number_format($item->family_income, 2) : '-' }}
                                                    </span>
                                                </div>

                                                <div>
                                                    <span class="label">อาชีพผู้ปกครอง:</span>
                                                    <span class="value">{{ $item->guardian_job ?: '-' }}</span>
                                                </div>

                                                <div>
                                                    <span class="label">รายได้:</span>
                                                    <span class="value">{{ $item->income_sufficiency ?: '-' }}</span>
                                                </div>

                                                <div>
                                                    <span class="label">หนี้สิน:</span>
                                                    <span class="value">{{ $item->debt ?: '-' }}</span>
                                                </div>

                                                <div>
                                                    <span class="label">ที่อยู่อาศัย:</span>
                                                    <span class="value">{{ $item->housing_condition ?: '-' }}</span>
                                                </div>

                                                @if ($item->income_sufficiency === 'ไม่เพียงพอ' && !empty($item->income_reason))
                                                    <div>
                                                        <span class="label">เนื่องจาก:</span>
                                                        <span class="value">{{ $item->income_reason }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $item->teacher ?: '-' }}</td>
                                        <td>{{ $item->remark ?: '-' }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap align-items-start gap-2">
                                                @forelse($item->pictures as $pic)
                                                    <div class="estimate-photo-thumb">
                                                        <img src="{{ route('estimate.image.view', $pic->id) }}"
                                                            class="img-thumbnail estimate-thumb-img" alt="รูปภาพประกอบการประเมิน" loading="lazy" decoding="async">
                                                    </div>

                                                @empty
                                                    <span class="text-muted">-</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="text-center estimate-col-actions">
                                            <a href="{{ route('estimate.report', $item->id) }}"
                                                class="btn btn-info btn-sm d-inline-flex align-items-center text-white mb-1 mb-md-0">
                                                <i class="bi bi-printer-fill me-1"></i> รายงาน
                                            </a>

                                            <button type="button" class="btn btn-success btn-sm js-edit-estimate"
                                                onclick="estimateEdit({{ $item->id }}, this)">
                                                <i class="bi bi-pencil-square"></i> แก้ไข
                                            </button>

                                            <form id="delete-form-item-{{ $item->id }}"
                                                action="{{ route('estimate.delete', $item->id) }}" method="POST"
                                                style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                            <button type="button"
                                                class="btn btn-sm btn-danger d-inline-flex align-items-center ms-1 mt-1 mt-md-0"
                                                onclick="confirmDelete('delete-form-item-{{ $item->id }}', 'คุณต้องการลบข้อมูลนี้ใช่หรือไม่')">
                                                <i class="bi bi-trash-fill me-1"></i> ลบ
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="modal fade" id="add-estimate-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{ route('estimate.store') }}" method="POST" enctype="multipart/form-data"
                        id="add-estimate-form" class="h-100 d-flex flex-column">
                        @csrf
                        <input type="hidden" name="client_id" value="{{ $client->id }}">

                        <div class="modal-header">
                            <h5 class="modal-title">เพิ่มข้อมูลการติดตาม</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="estimate-form-section">
                                <div class="estimate-form-section-title">ข้อมูลพื้นฐาน</div>

                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <label class="form-label">
                                            วันที่ติดตาม <span class="text-danger">*</span>
                                        </label>

                                        <input type="date" name="date" id="add_date"
                                            class="form-control @error('date') is-invalid @enderror"
                                            value="{{ old('date', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                                            max="{{ now('Asia/Bangkok')->toDateString() }}">

                                        @error('date')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label d-block">การดำเนินงาน</label>
                                        <div class="estimate-inline-radio">
                                            <label class="estimate-radio-card">
                                                <input class="form-check-input mt-0" type="radio" name="follo_no"
                                                    value="หน่วยงานไปเอง"
                                                    {{ old('follo_no') == 'หน่วยงานไปเอง' ? 'checked' : '' }}>
                                                <span>หน่วยงานไปเอง</span>
                                            </label>
                                            <label class="estimate-radio-card">
                                                <input class="form-check-input mt-0" type="radio" name="follo_no"
                                                    value="โทรศัพท์" {{ old('follo_no') == 'โทรศัพท์' ? 'checked' : '' }}>
                                                <span>โทรศัพท์</span>
                                            </label>
                                            <label class="estimate-radio-card">
                                                <input class="form-check-input mt-0" type="radio" name="follo_no"
                                                    value="จดหมาย" {{ old('follo_no') == 'จดหมาย' ? 'checked' : '' }}>
                                                <span>จดหมาย</span>
                                            </label>
                                        </div>
                                        @error('follo_no')
                                            <span id="follo_no-error-add"
                                                class="text-danger d-block mt-2">{{ $message }}</span>
                                        @enderror
                                    </div>


                                </div>
                            </div>

                            <div class="estimate-form-section">
                                <div class="estimate-form-section-title">ข้อมูลครอบครัวเพิ่มเติม</div>

                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">รายได้ครอบครัวเฉลี่ย/เดือน</label>
                                        <input type="number" name="family_income"
                                            class="form-control @error('family_income') is-invalid @enderror"
                                            value="{{ old('family_income') }}" min="0" step="0.01"
                                            placeholder="เช่น 12000.00">
                                        @error('family_income')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label">อาชีพผู้ปกครอง</label>
                                        <input type="text" name="guardian_job"
                                            class="form-control @error('guardian_job') is-invalid @enderror"
                                            value="{{ old('guardian_job') }}" placeholder="ระบุอาชีพผู้ปกครอง">
                                        @error('guardian_job')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label d-block">ความเพียงพอของรายได้</label>
                                        <div class="estimate-inline-radio">
                                            <label class="estimate-radio-card">
                                                <input class="form-check-input mt-0 estimate-income-sufficiency-add"
                                                    type="radio" name="income_sufficiency" value="เพียงพอ"
                                                    {{ old('income_sufficiency', 'เพียงพอ') == 'เพียงพอ' ? 'checked' : '' }}>
                                                <span>เพียงพอ</span>
                                            </label>
                                            <label class="estimate-radio-card">
                                                <input class="form-check-input mt-0 estimate-income-sufficiency-add"
                                                    type="radio" name="income_sufficiency" value="ไม่เพียงพอ"
                                                    {{ old('income_sufficiency') == 'ไม่เพียงพอ' ? 'checked' : '' }}>
                                                <span>ไม่เพียงพอ</span>
                                            </label>
                                        </div>
                                        @error('income_sufficiency')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 {{ old('income_sufficiency', 'เพียงพอ') == 'ไม่เพียงพอ' ? '' : 'estimate-hidden' }}"
                                        id="income-reason-wrap-add">
                                        <label class="form-label">เนื่องจาก</label>
                                        <textarea name="income_reason" id="income_reason_add"
                                            class="form-control @error('income_reason') is-invalid @enderror" rows="2"
                                            placeholder="ระบุสาเหตุกรณีรายได้ไม่เพียงพอ">{{ old('income_reason') }}</textarea>
                                        @error('income_reason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label">หนี้สิน (ถ้ามี)</label>
                                        <textarea name="debt" class="form-control @error('debt') is-invalid @enderror" rows="2"
                                            placeholder="ระบุหนี้สิน">{{ old('debt') }}</textarea>
                                        @error('debt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label d-block">สภาพที่อยู่อาศัย</label>
                                        <div class="estimate-inline-radio">
                                            <label class="estimate-radio-card">
                                                <input class="form-check-input mt-0" type="radio"
                                                    name="housing_condition" value="ดี"
                                                    {{ old('housing_condition') == 'ดี' ? 'checked' : '' }}>
                                                <span>ดี</span>
                                            </label>
                                            <label class="estimate-radio-card">
                                                <input class="form-check-input mt-0" type="radio"
                                                    name="housing_condition" value="พอใช้"
                                                    {{ old('housing_condition') == 'พอใช้' ? 'checked' : '' }}>
                                                <span>พอใช้</span>
                                            </label>
                                            <label class="estimate-radio-card">
                                                <input class="form-check-input mt-0" type="radio"
                                                    name="housing_condition" value="ควรปรับปรุง"
                                                    {{ old('housing_condition') == 'ควรปรับปรุง' ? 'checked' : '' }}>
                                                <span>ควรปรับปรุง</span>
                                            </label>
                                        </div>
                                        @error('housing_condition')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="estimate-form-section mb-0">
                                <div class="estimate-form-section-title">ข้อมูลเพิ่มเติม</div>

                                <div class="row g-3">

                                    <div class="col-12">
                                        <label class="form-label">ผลการติดตาม</label>
                                        <textarea name="results" class="form-control @error('results') is-invalid @enderror" rows="3">{{ old('results') }}</textarea>
                                        @error('results')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <div class="col-12">
                                        <label class="form-label">หมายเหตุ</label>
                                        <textarea name="remark" class="form-control @error('remark') is-invalid @enderror" rows="2">{{ old('remark') }}</textarea>
                                        @error('remark')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label">ผู้ติดตาม</label>
                                        <input type="text" name="teacher"
                                            class="form-control @error('teacher') is-invalid @enderror"
                                            value="{{ old('teacher') }}">
                                        @error('teacher')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">เลือกรูปภาพ</label>

                                        {{-- =====================================================
                                            PATCH:
                                            รองรับบีบอัดรูปก่อน upload
                                            ลดเวลาบน shared hosting
                                        ====================================================== --}}
                                        <input type="file" name="pictures[]" multiple accept="image/*"
                                            class="form-control" id="pictures-input-add">

                                        {{-- =====================================================
                                            PATCH:
                                            พื้นที่แสดง Preview รูปตอนเพิ่มข้อมูล
                                        ====================================================== --}}
                                        <div id="preview-area-add" class="d-flex flex-wrap gap-2 mt-3"></div>
                                        <div class="form-text estimate-image-status" aria-live="polite"></div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                class="btn btn-secondary estimate-action-btn estimate-cancel-btn"
                                data-bs-dismiss="modal">
                                <i class="bi bi-x-lg" aria-hidden="true"></i>
                                <span>ยกเลิก</span>
                            </button>

                            <button type="submit"
                                class="btn btn-primary estimate-action-btn estimate-submit-btn">
                                <i class="bi bi-check2-circle" aria-hidden="true"></i>
                                <span>บันทึกข้อมูล</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="edit-estimate-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" id="edit-estimate-form" action="{{ route('estimate.update', 0) }}"
                        enctype="multipart/form-data" data-id="" class="h-100 d-flex flex-column">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="client_id" id="edit_client_id" value="{{ $client->id }}">

                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">แก้ไขข้อมูลการติดตาม</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="estimate-form-section">
                                <div class="estimate-form-section-title">ข้อมูลพื้นฐาน</div>

                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <label class="form-label">
                                            วันที่ติดตาม <span class="text-danger">*</span>
                                        </label>

                                        <input type="date" name="date" id="edit_date" required
                                            class="form-control @error('date') is-invalid @enderror"
                                            value="{{ old('date') }}" max="{{ now('Asia/Bangkok')->toDateString() }}">

                                        @error('date')
                                            <div class="invalid-feedback" id="edit-date-error">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label d-block">
                                            การดำเนินงาน <span class="text-danger">*</span>
                                        </label>

                                        <div class="estimate-inline-radio">
                                            <label class="estimate-radio-card">
                                                <input class="form-check-input mt-0" type="radio" name="follo_no"
                                                    value="หน่วยงานไปเอง">
                                                <span>หน่วยงานไปเอง</span>
                                            </label>

                                            <label class="estimate-radio-card">
                                                <input class="form-check-input mt-0" type="radio" name="follo_no"
                                                    value="โทรศัพท์">
                                                <span>โทรศัพท์</span>
                                            </label>

                                            <label class="estimate-radio-card">
                                                <input class="form-check-input mt-0" type="radio" name="follo_no"
                                                    value="จดหมาย">
                                                <span>จดหมาย</span>
                                            </label>
                                        </div>

                                        @error('follo_no')
                                            <span id="follo_no-error-edit" class="text-danger d-block mt-2">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                </div>
                            </div>

                            <div class="estimate-form-section">
                                <div class="estimate-form-section-title">ข้อมูลครอบครัวเพิ่มเติม</div>

                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">รายได้ครอบครัวเฉลี่ย/เดือน</label>
                                        <input type="number" name="family_income" id="edit_family_income"
                                            class="form-control @error('family_income') is-invalid @enderror"
                                            min="0" step="0.01" value="{{ old('family_income') }}">
                                        @error('family_income')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label">อาชีพผู้ปกครอง</label>
                                        <input type="text" name="guardian_job" id="edit_guardian_job"
                                            class="form-control @error('guardian_job') is-invalid @enderror"
                                            value="{{ old('guardian_job') }}">
                                        @error('guardian_job')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label d-block">ความเพียงพอของรายได้</label>
                                        <div class="estimate-inline-radio">
                                            <label class="estimate-radio-card">
                                                <input class="form-check-input mt-0 estimate-income-sufficiency-edit"
                                                    type="radio" name="income_sufficiency" value="เพียงพอ">
                                                <span>เพียงพอ</span>
                                            </label>
                                            <label class="estimate-radio-card">
                                                <input class="form-check-input mt-0 estimate-income-sufficiency-edit"
                                                    type="radio" name="income_sufficiency" value="ไม่เพียงพอ">
                                                <span>ไม่เพียงพอ</span>
                                            </label>
                                        </div>
                                        @error('income_sufficiency')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 estimate-hidden" id="income-reason-wrap-edit">
                                        <label class="form-label">เนื่องจาก</label>
                                        <textarea name="income_reason" id="edit_income_reason"
                                            class="form-control @error('income_reason') is-invalid @enderror" rows="2">{{ old('income_reason') }}</textarea>
                                        @error('income_reason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label">หนี้สิน (ถ้ามี)</label>
                                        <textarea name="debt" id="edit_debt" class="form-control @error('debt') is-invalid @enderror" rows="2">{{ old('debt') }}</textarea>
                                        @error('debt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label d-block">สภาพที่อยู่อาศัย</label>
                                        <div class="estimate-inline-radio">
                                            <label class="estimate-radio-card">
                                                <input class="form-check-input mt-0" type="radio"
                                                    name="housing_condition" value="ดี">
                                                <span>ดี</span>
                                            </label>
                                            <label class="estimate-radio-card">
                                                <input class="form-check-input mt-0" type="radio"
                                                    name="housing_condition" value="พอใช้">
                                                <span>พอใช้</span>
                                            </label>
                                            <label class="estimate-radio-card">
                                                <input class="form-check-input mt-0" type="radio"
                                                    name="housing_condition" value="ควรปรับปรุง">
                                                <span>ควรปรับปรุง</span>
                                            </label>
                                        </div>
                                        @error('housing_condition')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="estimate-form-section mb-0">
                                <div class="estimate-form-section-title">ข้อมูลเพิ่มเติม</div>

                                <div class="row g-3">

                                    <div class="col-12">
                                        <label class="form-label">ผลการติดตาม</label>
                                        <textarea name="results" id="edit_results" class="form-control @error('results') is-invalid @enderror"
                                            rows="3">{{ old('results') }}</textarea>
                                        @error('results')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">หมายเหตุ</label>
                                        <textarea name="remark" id="edit_remark" class="form-control @error('remark') is-invalid @enderror"
                                            rows="2">{{ old('remark') }}</textarea>
                                        @error('remark')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label">ผู้ติดตาม</label>
                                        <input type="text" name="teacher" id="edit_teacher"
                                            class="form-control @error('teacher') is-invalid @enderror"
                                            value="{{ old('teacher') }}">
                                        @error('teacher')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">เลือกรูปภาพใหม่</label>

                                        {{-- =====================================================
                                            PATCH:
                                            รองรับ Browser Compression ก่อน Upload
                                            ลดเวลาอัปโหลดบน Shared Hosting
                                        ====================================================== --}}
                                        <input type="file" name="pictures[]" multiple accept="image/*"
                                            class="form-control @error('pictures') is-invalid @enderror"
                                            id="pictures-input-edit">

                                        @error('pictures')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <div id="preview-area-edit" class="d-flex flex-wrap gap-2 mt-3"></div>
                                        <div class="form-text estimate-image-status" aria-live="polite"></div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">รูปภาพเดิม</label>
                                        <div id="existing-pictures-edit" class="d-flex flex-wrap gap-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                class="btn btn-secondary btn-cancel estimate-action-btn estimate-cancel-btn"
                                data-bs-dismiss="modal">
                                <i class="bi bi-x-lg" aria-hidden="true"></i>
                                <span>ยกเลิก</span>
                            </button>

                            <button type="submit"
                                class="btn btn-primary estimate-action-btn estimate-submit-btn"
                                id="btn-update-estimate">
                                <i class="bi bi-check2-circle" aria-hidden="true"></i>
                                <span>บันทึกการแก้ไข</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>

        <script>
            (() => {
                'use strict';

                const config = {
                    today: @json(now('Asia/Bangkok')->toDateString()),
                    editUrlTemplate: @json(url('/estimate/edit/__ID__')),
                    updateUrlTemplate: @json(route('estimate.update', '__ID__')),
                    duplicateUrl: @json(url('/estimate/check-duplicate')),
                    formMode: @json(session('form')),
                    editId: @json(session('edit_estimate_id')),
                    oldInput: @json(old()),
                    validationErrors: @json($errors->all()),
                    maxPictures: 8,
                };

                const replaceId = (template, id) => template.replace('__ID__', encodeURIComponent(id));

                function toggleIncomeReason(section) {
                    const form = document.getElementById(`${section}-estimate-form`);
                    const wrap = document.getElementById(`income-reason-wrap-${section}`);
                    const textarea = document.getElementById(section === 'add' ? 'income_reason_add' : 'edit_income_reason');

                    if (!form || !wrap) return;

                    const selected = form.querySelector('input[name="income_sufficiency"]:checked')?.value ?? '';
                    const shouldShow = selected === 'ไม่เพียงพอ';

                    wrap.classList.toggle('estimate-hidden', !shouldShow);

                    if (!shouldShow && textarea) {
                        textarea.value = '';
                    }
                }

                function showValidationAlert(errors) {
                    if (!Array.isArray(errors) || errors.length === 0 || typeof Swal === 'undefined') return;

                    const container = document.createElement('div');
                    container.className = 'text-start';

                    errors.forEach(error => {
                        const line = document.createElement('div');
                        line.textContent = `• ${error}`;
                        container.appendChild(line);
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'กรุณาตรวจสอบข้อมูล',
                        html: container,
                        confirmButtonText: 'ตกลง',
                    });
                }

                function setStatus(input, message = '') {
                    const status = input.closest('.col-12')?.querySelector('.estimate-image-status');
                    if (status) status.textContent = message;
                }

                function renderNewPictures(files, previewArea) {
                    previewArea.innerHTML = '';

                    files.forEach(file => {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'estimate-preview-item';

                        const image = document.createElement('img');
                        image.className = 'img-thumbnail';
                        image.alt = 'ตัวอย่างรูปภาพใหม่';
                        image.decoding = 'async';

                        const objectUrl = URL.createObjectURL(file);
                        image.src = objectUrl;
                        image.addEventListener('load', () => URL.revokeObjectURL(objectUrl), { once: true });

                        wrapper.appendChild(image);
                        previewArea.appendChild(wrapper);
                    });
                }

                function normalizedJpegName(filename) {
                    const base = filename.replace(/\.[^.]+$/, '') || 'estimate-picture';
                    return `${base}.jpg`;
                }

                function bindImageInput(inputId, previewId) {
                    const input = document.getElementById(inputId);
                    const previewArea = document.getElementById(previewId);

                    if (!input || !previewArea) return;

                    input.addEventListener('change', async event => {
                        let files = Array.from(event.target.files || []).filter(file => file.type.startsWith('image/'));

                        if (files.length > config.maxPictures) {
                            files = files.slice(0, config.maxPictures);

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'เลือกรูปภาพเกินจำนวน',
                                    text: `ระบบจะใช้เฉพาะ ${config.maxPictures} รูปแรก`,
                                    confirmButtonText: 'ตกลง',
                                });
                            }
                        }

                        renderNewPictures(files, previewArea);

                        if (files.length === 0) {
                            input.value = '';
                            setStatus(input, '');
                            return;
                        }

                        if (typeof DataTransfer === 'undefined') {
                            setStatus(input, 'เบราว์เซอร์นี้ไม่รองรับการบีบอัดก่อนอัปโหลด ระบบจะบีบอัดที่เซิร์ฟเวอร์');
                            return;
                        }

                        input.dataset.processing = '1';
                        setStatus(input, 'กำลังเตรียมและบีบอัดรูปภาพ...');

                        const transfer = new DataTransfer();

                        try {
                            for (const file of files) {
                                let output = file;

                                if (typeof window.imageCompression === 'function') {
                                    try {
                                        const compressed = await window.imageCompression(file, {
                                            maxSizeMB: 0.8,
                                            maxWidthOrHeight: 1600,
                                            useWebWorker: true,
                                            fileType: 'image/jpeg',
                                            initialQuality: 0.78,
                                        });

                                        output = new File(
                                            [compressed],
                                            normalizedJpegName(file.name),
                                            { type: 'image/jpeg', lastModified: Date.now() }
                                        );
                                    } catch (compressionError) {
                                        console.warn('Image compression failed; original file will be used.', compressionError);
                                    }
                                }

                                transfer.items.add(output);
                            }

                            input.files = transfer.files;
                            setStatus(input, `พร้อมอัปโหลด ${input.files.length} รูป`);
                        } catch (error) {
                            console.error('Unable to prepare image files.', error);
                            setStatus(input, 'ไม่สามารถบีบอัดรูปได้ ระบบจะใช้ไฟล์ต้นฉบับ');
                        } finally {
                            input.dataset.processing = '0';
                        }
                    });
                }

                function setRadioValue(form, name, value) {
                    form.querySelectorAll(`input[name="${name}"]`).forEach(input => {
                        input.checked = input.value === String(value ?? '');
                    });
                }

                function setFieldValue(form, selector, value) {
                    const field = form.querySelector(selector);
                    if (field) field.value = value ?? '';
                }

                function renderExistingPictures(pictures) {
                    const area = document.getElementById('existing-pictures-edit');
                    if (!area) return;

                    area.innerHTML = '';

                    if (!Array.isArray(pictures) || pictures.length === 0) {
                        const empty = document.createElement('span');
                        empty.className = 'text-muted';
                        empty.textContent = 'ไม่มีรูปภาพเดิม';
                        area.appendChild(empty);
                        return;
                    }

                    pictures.forEach(picture => {
                        if (!picture?.id || !picture?.url) return;

                        const wrapper = document.createElement('div');
                        wrapper.className = 'estimate-old-picture-item';

                        const image = document.createElement('img');
                        image.className = 'img-thumbnail';
                        image.src = picture.url;
                        image.alt = 'รูปภาพเดิม';
                        image.loading = 'lazy';
                        image.decoding = 'async';

                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'btn btn-danger btn-sm estimate-picture-remove';
                        button.setAttribute('aria-label', 'ลบรูปภาพนี้');
                        button.innerHTML = '<i class="bi bi-x-lg" aria-hidden="true"></i>';
                        button.addEventListener('click', () => removeOldPicture(picture.id, wrapper));

                        wrapper.append(image, button);
                        area.appendChild(wrapper);
                    });
                }

                function applyEstimateData(data, useOldInput = false) {
                    const form = document.getElementById('edit-estimate-form');
                    if (!form) return;

                    const old = useOldInput ? config.oldInput : {};
                    const value = key => Object.prototype.hasOwnProperty.call(old, key) ? old[key] : data[key];

                    form.action = replaceId(config.updateUrlTemplate, data.id);
                    form.dataset.id = data.id;

                    setFieldValue(form, '#edit_client_id', data.client_id);
                    setFieldValue(form, '#edit_date', value('date'));
                    setFieldValue(form, '#edit_results', value('results'));
                    setFieldValue(form, '#edit_family_income', value('family_income'));
                    setFieldValue(form, '#edit_guardian_job', value('guardian_job'));
                    setFieldValue(form, '#edit_income_reason', value('income_reason'));
                    setFieldValue(form, '#edit_debt', value('debt'));
                    setFieldValue(form, '#edit_teacher', value('teacher'));
                    setFieldValue(form, '#edit_remark', value('remark'));

                    setRadioValue(form, 'follo_no', value('follo_no'));
                    setRadioValue(form, 'income_sufficiency', value('income_sufficiency') ?? 'เพียงพอ');
                    setRadioValue(form, 'housing_condition', value('housing_condition'));

                    form.querySelectorAll('input[name="remove_pictures[]"]').forEach(input => input.remove());
                    document.getElementById('preview-area-edit')?.replaceChildren();
                    const fileInput = document.getElementById('pictures-input-edit');
                    if (fileInput) {
                        fileInput.value = '';
                        fileInput.dataset.processing = '0';
                        setStatus(fileInput, '');
                    }

                    renderExistingPictures(data.pictures);
                    toggleIncomeReason('edit');
                }

                async function estimateEdit(id, trigger = null, useOldInput = false) {
                    const button = trigger instanceof HTMLElement ? trigger : null;
                    const originalButtonHtml = button?.innerHTML;

                    if (button) {
                        button.disabled = true;
                        button.innerHTML = '<span>กำลังโหลด...</span>';
                    }

                    try {
                        const response = await fetch(replaceId(config.editUrlTemplate, id), {
                            headers: { 'Accept': 'application/json' },
                            credentials: 'same-origin',
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const data = await response.json();
                        if (!useOldInput) {
                            clearFormErrors(document.getElementById('edit-estimate-form'));
                        }
                        applyEstimateData(data, useOldInput);

                        const modalElement = document.getElementById('edit-estimate-modal');
                        bootstrap.Modal.getOrCreateInstance(modalElement).show();

                        requestAnimationFrame(() => {
                            const modalBody = modalElement.querySelector('.modal-body');
                            if (modalBody) modalBody.scrollTop = 0;
                        });

                        return true;
                    } catch (error) {
                        console.error('Unable to load estimate.', error);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'ไม่สามารถโหลดข้อมูลได้',
                                text: 'กรุณาลองใหม่อีกครั้ง',
                                confirmButtonText: 'ตกลง',
                            });
                        }

                        return false;
                    } finally {
                        if (button) {
                            button.disabled = false;
                            button.innerHTML = originalButtonHtml;
                        }
                    }
                }

                function removeOldPicture(pictureId, wrapper) {
                    wrapper?.remove();

                    const form = document.getElementById('edit-estimate-form');
                    if (!form || form.querySelector(`input[name="remove_pictures[]"][value="${pictureId}"]`)) return;

                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'remove_pictures[]';
                    hidden.value = pictureId;
                    form.appendChild(hidden);

                    const area = document.getElementById('existing-pictures-edit');
                    if (area && area.children.length === 0) {
                        const empty = document.createElement('span');
                        empty.className = 'text-muted';
                        empty.textContent = 'ไม่มีรูปภาพเดิม';
                        area.appendChild(empty);
                    }
                }

                function clearFormErrors(form) {
                    if (!form) return;

                    form.querySelectorAll('.is-invalid').forEach(element => element.classList.remove('is-invalid'));
                    form.querySelectorAll('.invalid-feedback, .estimate-validation-error').forEach(element => {
                        element.style.display = 'none';
                    });
                }

                function resetForm(formId, previewId, setToday = false) {
                    const form = document.getElementById(formId);
                    if (!form) return;

                    form.reset();
                    form.dataset.submitting = '0';
                    clearFormErrors(form);

                    document.getElementById(previewId)?.replaceChildren();
                    form.querySelectorAll('input[type="file"]').forEach(input => {
                        input.value = '';
                        input.dataset.processing = '0';
                        setStatus(input, '');
                    });
                    form.querySelectorAll('input[name="remove_pictures[]"]').forEach(input => input.remove());

                    if (formId === 'edit-estimate-form') {
                        document.getElementById('existing-pictures-edit')?.replaceChildren();
                        form.dataset.id = '';
                    }

                    if (setToday) {
                        const dateInput = form.querySelector('input[name="date"]');
                        if (dateInput) dateInput.value = config.today;
                    }

                    if (formId === 'add-estimate-form') {
                        const defaultRadio = form.querySelector('input[name="income_sufficiency"][value="เพียงพอ"]');
                        if (defaultRadio) defaultRadio.checked = true;
                    }

                    toggleIncomeReason(formId.startsWith('add') ? 'add' : 'edit');

                    const submitButton = form.querySelector('button[type="submit"]');
                    restoreSubmitButton(submitButton);

                    const modalBody = form.closest('.modal-content')?.querySelector('.modal-body');
                    if (modalBody) modalBody.scrollTop = 0;
                }

                function restoreSubmitButton(button) {
                    if (!button) return;
                    button.disabled = false;
                    if (button.dataset.originalHtml) {
                        button.innerHTML = button.dataset.originalHtml;
                    }
                }

                function bindSafeSubmit(formId, loadingText) {
                    const form = document.getElementById(formId);
                    if (!form) return;

                    form.addEventListener('submit', event => {
                        const processing = Array.from(form.querySelectorAll('input[type="file"]'))
                            .some(input => input.dataset.processing === '1');

                        if (processing) {
                            event.preventDefault();
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'กำลังเตรียมรูปภาพ',
                                    text: 'กรุณารอให้ระบบเตรียมรูปภาพเสร็จก่อนบันทึก',
                                    confirmButtonText: 'ตกลง',
                                });
                            }
                            return;
                        }

                        if (form.querySelector('input[name="date"].is-invalid')) {
                            event.preventDefault();
                            return;
                        }

                        if (form.dataset.submitting === '1') {
                            event.preventDefault();
                            return;
                        }

                        form.dataset.submitting = '1';
                        const button = form.querySelector('button[type="submit"]');

                        if (button) {
                            button.dataset.originalHtml ||= button.innerHTML;
                            button.disabled = true;
                            button.innerHTML = `<span>${loadingText}</span>`;
                        }
                    });
                }

                async function checkDuplicateDate(input, estimateId = null) {
                    const form = input.form;
                    const clientId = form?.querySelector('input[name="client_id"]')?.value;
                    const submitButton = form?.querySelector('button[type="submit"]');

                    if (!input.value || !clientId) return;

                    input._duplicateController?.abort();
                    input._duplicateController = new AbortController();

                    const params = new URLSearchParams({ client_id: clientId, date: input.value });
                    if (estimateId) params.set('id', estimateId);

                    try {
                        const response = await fetch(`${config.duplicateUrl}?${params.toString()}`, {
                            headers: { 'Accept': 'application/json' },
                            credentials: 'same-origin',
                            signal: input._duplicateController.signal,
                        });

                        if (!response.ok) throw new Error(`HTTP ${response.status}`);

                        const result = await response.json();
                        let error = input.parentElement.querySelector('.estimate-duplicate-error');

                        if (result.duplicate) {
                            if (!error) {
                                error = document.createElement('div');
                                error.className = 'invalid-feedback estimate-duplicate-error';
                                input.parentElement.appendChild(error);
                            }

                            error.textContent = 'วันที่นี้ถูกบันทึกไว้แล้ว กรุณาเลือกวันอื่น';
                            error.style.display = 'block';
                            input.classList.add('is-invalid');
                            if (submitButton) submitButton.disabled = true;
                        } else {
                            error?.remove();
                            input.classList.remove('is-invalid');
                            if (submitButton && form.dataset.submitting !== '1') submitButton.disabled = false;
                        }
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            console.warn('Duplicate date check failed; server validation remains active.', error);
                            if (submitButton && form.dataset.submitting !== '1') submitButton.disabled = false;
                        }
                    }
                }

                function bindDuplicateDateCheck(inputId, estimateIdResolver) {
                    const input = document.getElementById(inputId);
                    if (!input) return;

                    input.addEventListener('change', () => {
                        input.parentElement.querySelector('.estimate-duplicate-error')?.remove();
                        input.classList.remove('is-invalid');
                        checkDuplicateDate(input, estimateIdResolver?.() ?? null);
                    });
                }

                document.addEventListener('DOMContentLoaded', async () => {
                    bindImageInput('pictures-input-add', 'preview-area-add');
                    bindImageInput('pictures-input-edit', 'preview-area-edit');
                    bindSafeSubmit('add-estimate-form', 'กำลังบันทึก...');
                    bindSafeSubmit('edit-estimate-form', 'กำลังบันทึก...');

                    document.querySelectorAll('.estimate-income-sufficiency-add').forEach(radio => {
                        radio.addEventListener('change', () => toggleIncomeReason('add'));
                    });

                    document.querySelectorAll('.estimate-income-sufficiency-edit').forEach(radio => {
                        radio.addEventListener('change', () => toggleIncomeReason('edit'));
                    });

                    document.querySelectorAll('input[name="follo_no"]').forEach(radio => {
                        radio.addEventListener('change', () => {
                            radio.form?.querySelectorAll('[id^="follo_no-error-"]').forEach(error => error.remove());
                        });
                    });

                    bindDuplicateDateCheck('add_date', () => null);
                    bindDuplicateDateCheck('edit_date', () => document.getElementById('edit-estimate-form')?.dataset.id);

                    document.getElementById('btn-add-estimate')?.addEventListener('click', () => {
                        resetForm('add-estimate-form', 'preview-area-add', true);
                    });

                    ['add-estimate-modal', 'edit-estimate-modal'].forEach(modalId => {
                        const modal = document.getElementById(modalId);
                        if (!modal) return;

                        modal.addEventListener('shown.bs.modal', () => {
                            const body = modal.querySelector('.modal-body');
                            if (body) body.scrollTop = 0;
                        });

                        modal.addEventListener('hidden.bs.modal', () => {
                            if (modalId === 'add-estimate-modal') {
                                resetForm('add-estimate-form', 'preview-area-add');
                            } else {
                                resetForm('edit-estimate-form', 'preview-area-edit');
                            }
                        });
                    });

                    toggleIncomeReason('add');
                    toggleIncomeReason('edit');

                    if (config.formMode === 'add-estimate' && config.validationErrors.length) {
                        bootstrap.Modal.getOrCreateInstance(document.getElementById('add-estimate-modal')).show();
                        showValidationAlert(config.validationErrors);
                    }

                    if (config.formMode === 'edit-estimate' && config.editId) {
                        const loaded = await estimateEdit(config.editId, null, true);
                        if (loaded) showValidationAlert(config.validationErrors);
                    }
                });

                window.estimateEdit = estimateEdit;
                window.removeOldPicture = removeOldPicture;
            })();
        </script>
    @endpush

@endsection