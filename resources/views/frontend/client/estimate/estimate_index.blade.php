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

        .estimate-page .estimate-title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--estimate-text);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .estimate-page .estimate-client-info {
            padding: 14px 18px;
            border-bottom: 1px solid var(--estimate-border);
            background: #fff;
        }

        .estimate-page .estimate-client-line {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 18px;
            color: var(--estimate-text);
            font-size: .96rem;
        }

        .estimate-page .estimate-client-line strong {
            font-weight: 800;
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
            background: #fff;
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

            .estimate-page .estimate-title {
                font-size: 1rem;
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
                box-shadow: 0 -6px 18px rgba(15, 23, 42, .05);
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
    </style>

    <div class="estimate-page">

        @if ($errors->any() && session('form') === 'add-estimate')
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('add-estimate-modal')).show();

                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        html: `{!! implode('<br>', $errors->all()) !!}`,
                        confirmButtonText: 'ตกลง'
                    });
                });
            </script>
        @endif

        @if ($errors->any() && session('form') === 'edit-estimate')
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('edit-estimate-modal')).show();

                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        html: `{!! implode('<br>', $errors->all()) !!}`,
                        confirmButtonText: 'ตกลง'
                    });
                });
            </script>
        @endif

        <div class="estimate-card mt-3">
            <div class="estimate-header">
                <h5 class="estimate-title">
                    <i class="bi bi-people-fill text-primary"></i>
                    <span>ประวัติการติดตามและประเมินครอบครัวเด็ก</span>
                </h5>

                <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center shadow-sm px-3"
                    data-bs-toggle="modal" data-bs-target="#add-estimate-modal" id="btn-add-estimate">
                    <i class="bi bi-plus-circle me-1"></i>
                    <span>เพิ่มข้อมูล</span>
                </button>
            </div>

            <div class="estimate-client-info">
                <div class="estimate-client-line">
                    <span><strong>ชื่อ:</strong> {{ $client->full_name }}</span>
                    <span><strong>อายุ:</strong> {{ \Carbon\Carbon::parse($client->birth_date)->age }} ปี</span>
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
                                @foreach ($client->estimates->sortByDesc('date')->sortByDesc('id') as $item)
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
                                                    @php
                                                        $imageUrl = str_starts_with($pic->path, 'upload/')
                                                            ? asset($pic->path)
                                                            : asset('storage/' . $pic->path);
                                                    @endphp

                                                    <div class="estimate-photo-thumb">
                                                        <img src="{{ $imageUrl }}"
                                                            class="img-thumbnail estimate-thumb-img" alt="estimate picture">
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

                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#edit-estimate-modal"
                                                onclick="estimateEdit({{ $item->id }})">
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

                                        <input type="date" name="date"
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
                                            <span id="follo_no-error"
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
                                        <div id="preview-area-add" class="d-flex flex-wrap gap-2 mt-3">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div id="preview-area-add" class="d-flex flex-wrap gap-2 mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
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
                                            <span id="follo_no-error" class="text-danger d-block mt-2">
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

                                        {{-- =====================================================
                                            PATCH:
                                            พื้นที่แสดง Preview รูปใหม่ตอนแก้ไข
                                        ====================================================== --}}
                                        <div id="preview-area-edit" class="d-flex flex-wrap gap-2 mt-3">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div id="preview-area-edit" class="d-flex flex-wrap gap-2 mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="btn-update-estimate">อัปเดต</button>
                            <button type="button" class="btn btn-secondary btn-cancel"
                                data-bs-dismiss="modal">ยกเลิก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- =====================================================
     PATCH: Browser Image Compression
     บีบอัดรูปก่อนส่งขึ้น Server
     ช่วยลดเวลาอัปโหลดบน Host จริง
===================================================== --}}
        <script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>

        <script>
            function toggleIncomeReason(section) {
                let radios = document.querySelectorAll(`.estimate-income-sufficiency-${section}`);
                let wrap = document.getElementById(`income-reason-wrap-${section}`);
                let textarea = document.getElementById(section === 'add' ? 'income_reason_add' : 'edit_income_reason');

                if (!wrap) return;

                let selected = '';
                radios.forEach(radio => {
                    if (radio.checked) selected = radio.value;
                });

                if (selected === 'ไม่เพียงพอ') {
                    wrap.classList.remove('estimate-hidden');
                } else {
                    wrap.classList.add('estimate-hidden');
                    if (textarea) textarea.value = '';
                }
            }

            /* =====================================================
               PATCH:
               Preview + Compress รูปก่อน Upload
               ใช้ได้ทั้ง Add และ Edit
            ===================================================== */
            function previewFiles(inputId, previewId) {
                const input = document.getElementById(inputId);
                const previewArea = document.getElementById(previewId);

                if (!input || !previewArea) return;

                input.addEventListener('change', async function(event) {
                    previewArea.innerHTML = "";

                    const originalFiles = Array.from(event.target.files || []);
                    const dt = new DataTransfer();

                    // =====================================================
                    // PATCH:
                    // แสดง preview ทันทีจากไฟล์ต้นฉบับก่อน
                    // ไม่ต้องรอ compression
                    // =====================================================
                    originalFiles.forEach(file => {
                        if (!file.type.startsWith("image/")) return;

                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const wrapper = document.createElement("div");
                            wrapper.className =
                                "position-relative border rounded shadow-sm d-inline-block me-2 mb-2 estimate-preview-item";
                            wrapper.style.width = "90px";

                            wrapper.innerHTML = `
                    <img src="${e.target.result}"
                         class="img-thumbnail"
                         style="width:90px; height:90px; object-fit:cover;"
                         loading="lazy"
                         decoding="async">
                `;

                            previewArea.appendChild(wrapper);
                        };

                        reader.readAsDataURL(file);
                    });

                    // =====================================================
                    // PATCH:
                    // บีบอัดไฟล์สำหรับ submit
                    // =====================================================
                    for (const file of originalFiles) {
                        if (!file.type.startsWith("image/")) continue;

                        try {
                            const compressedFile = await imageCompression(file, {
                                maxSizeMB: 0.7,
                                maxWidthOrHeight: 1600,
                                useWebWorker: true,
                                fileType: 'image/jpeg',
                                initialQuality: 0.75,
                            });

                            dt.items.add(compressedFile);
                        } catch (error) {
                            console.error('Image compression failed:', error);
                            dt.items.add(file);
                        }
                    }

                    input.files = dt.files;
                });
            }

            previewFiles('pictures-input-add', 'preview-area-add');
            previewFiles('pictures-input-edit', 'preview-area-edit');

            function estimateEdit(id) {
                $.ajax({
                    url: "/estimate/edit/" + id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {

                        // ==============================
                        // PATCH: ล้าง Error เดิมก่อนเปิด Modal แก้ไข
                        // ==============================
                        const editForm = $('#edit-estimate-form');

                        editForm.find('.is-invalid').removeClass('is-invalid');
                        editForm.find('.invalid-feedback').hide();

                        editForm.find('#edit-date-error').hide();
                        editForm.find('#follo_no-error').hide();

                        $('#btn-update-estimate').prop('disabled', false);

                        $('#edit-estimate-form').attr('action', '/estimate/update/' + data.id);
                        $('#edit-estimate-form').attr('data-id', data.id);

                        $('#edit_client_id').val(data.client_id ?? '');
                        $('#edit_date').val(data.date ?? '');
                        $('#edit_results').val(data.results ?? '');
                        $('#edit_family_income').val(data.family_income ?? '');
                        $('#edit_guardian_job').val(data.guardian_job ?? '');
                        $('#edit_income_reason').val(data.income_reason ?? '');
                        $('#edit_debt').val(data.debt ?? '');
                        $('#edit_teacher').val(data.teacher ?? '');
                        $('#edit_remark').val(data.remark ?? '');

                        $('#edit-estimate-form input[name="follo_no"]').prop('checked', false);
                        $('#edit-estimate-form input[name="follo_no"]').each(function() {
                            $(this).prop('checked', $(this).val() === data.follo_no);
                        });

                        $('#edit-estimate-form input[name="income_sufficiency"]').prop('checked', false);
                        $('#edit-estimate-form input[name="income_sufficiency"]').each(function() {
                            $(this).prop('checked', $(this).val() === (data.income_sufficiency ??
                                'เพียงพอ'));
                        });

                        $('#edit-estimate-form input[name="housing_condition"]').prop('checked', false);
                        $('#edit-estimate-form input[name="housing_condition"]').each(function() {
                            $(this).prop('checked', $(this).val() === data.housing_condition);
                        });

                        toggleIncomeReason('edit');

                        const preview = $('#preview-area-edit');
                        preview.html('');
                        $('#edit-estimate-form input[name="remove_pictures[]"]').remove();

                        if (Array.isArray(data.pictures)) {
                            data.pictures.forEach(function(pic) {
                                preview.append(`
                        <div class="position-relative d-inline-block me-1 mb-1" style="width:80px;">
                            <img src="${pic.url}" class="img-thumbnail" style="width:80px; height:auto;" loading="lazy">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                    onclick="removeOldPicture(${pic.id}, this)">ลบ</button>
                        </div>
                    `);
                            });
                        }

                        const modalEl = document.getElementById('edit-estimate-modal');
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();

                        setTimeout(() => {
                            const modalBody = modalEl.querySelector('.modal-body');
                            if (modalBody) modalBody.scrollTop = 0;
                        }, 150);
                    }
                });
            }

            function removeOldPicture(picId, btn) {
                $(btn).closest('div').remove();
                $('#edit-estimate-form').append(
                    `<input type="hidden" name="remove_pictures[]" value="${picId}">`
                );
            }

            function resetForm(formId, previewId, setToday = false) {
                const form = document.getElementById(formId);
                if (!form) return;

                form.reset();

                form.querySelectorAll(".is-invalid").forEach(el => {
                    el.classList.remove("is-invalid");
                });

                form.querySelectorAll(".invalid-feedback, .text-danger").forEach(el => {
                    el.style.display = "none";
                });

                const previewArea = document.getElementById(previewId);
                if (previewArea) previewArea.innerHTML = "";

                form.querySelectorAll('input[type="file"]').forEach(fi => fi.value = "");
                form.querySelectorAll('input[name="remove_pictures[]"]').forEach(el => el.remove());

                if (setToday) {
                    const dateInput = form.querySelector('input[name="date"]');
                    if (dateInput) {
                        dateInput.value = new Date().toISOString().split('T')[0];
                    }
                }

                if (formId === 'add-estimate-form') {
                    const defaultRadio = form.querySelector('input[name="income_sufficiency"][value="เพียงพอ"]');
                    if (defaultRadio) defaultRadio.checked = true;
                    toggleIncomeReason('add');
                }

                if (formId === 'edit-estimate-form') {
                    toggleIncomeReason('edit');
                }

                const modalBody = form.closest('.modal-content')?.querySelector('.modal-body');
                if (modalBody) modalBody.scrollTop = 0;
            }

            async function checkDuplicateDate(clientId, date, estimateId) {
                const response = await fetch(
                    `/estimate/check-duplicate?client_id=${clientId}&date=${date}&id=${estimateId}`);
                const result = await response.json();
                return result.duplicate;
            }

            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll('.estimate-income-sufficiency-add').forEach(radio => {
                    radio.addEventListener('change', function() {
                        toggleIncomeReason('add');
                    });
                });

                document.querySelectorAll('.estimate-income-sufficiency-edit').forEach(radio => {
                    radio.addEventListener('change', function() {
                        toggleIncomeReason('edit');
                    });
                });

                toggleIncomeReason('add');
                toggleIncomeReason('edit');

                document.getElementById('btn-add-estimate')
                    ?.addEventListener('click', () => {
                        resetForm('add-estimate-form', 'preview-area-add', true);
                        const modalEl = document.getElementById('add-estimate-modal');
                        setTimeout(() => {
                            const modalBody = modalEl?.querySelector('.modal-body');
                            if (modalBody) modalBody.scrollTop = 0;
                        }, 150);
                    });

                document.querySelector('#add-estimate-modal .btn-secondary[data-bs-dismiss="modal"]')
                    ?.addEventListener('click', () => resetForm('add-estimate-form', 'preview-area-add'));

                document.querySelector('#edit-estimate-modal .btn-secondary[data-bs-dismiss="modal"]')
                    ?.addEventListener('click', () => resetForm('edit-estimate-form', 'preview-area-edit'));

                document.querySelectorAll('input[name="follo_no"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        const errorEl = document.getElementById('follo_no-error');

                        if (errorEl) {
                            errorEl.remove();
                        }
                    });
                });

                document.querySelectorAll('input[name="date"]').forEach(dateInput => {
                    dateInput.addEventListener('change', function() {
                        const wrapper = dateInput.closest('.col-12, .col-md-4, .mb-3');
                        const errorEl = wrapper ? wrapper.querySelector('.invalid-feedback') : null;
                        if (errorEl) errorEl.style.display = 'none';
                        dateInput.classList.remove('is-invalid');
                    });
                });

                const dateInput = document.getElementById('edit_date');
                const updateBtn = document.getElementById('btn-update-estimate');

                if (dateInput) {
                    dateInput.addEventListener('change', async function() {
                        const date = this.value;
                        const clientId = document.getElementById('edit_client_id')?.value;
                        const estimateId = document.querySelector('#edit-estimate-form')?.getAttribute(
                            'data-id');

                        if (!date || !clientId || !estimateId) return;

                        const duplicate = await checkDuplicateDate(clientId, date, estimateId);

                        if (duplicate) {
                            let errorEl = this.parentElement.querySelector('.invalid-feedback');

                            if (!errorEl) {
                                errorEl = document.createElement('div');
                                errorEl.className = 'invalid-feedback';
                                this.parentElement.appendChild(errorEl);
                            }

                            errorEl.textContent = 'วันที่นี้ถูกบันทึกไว้แล้ว กรุณาเลือกวันอื่น';
                            errorEl.style.display = 'block';
                            this.classList.add('is-invalid');

                            if (updateBtn) updateBtn.disabled = true;
                        } else {
                            const errorEl = this.parentElement.querySelector('.invalid-feedback');

                            if (errorEl) errorEl.style.display = 'none';

                            this.classList.remove('is-invalid');

                            if (updateBtn) updateBtn.disabled = false;
                        }
                    });
                }

                ['add-estimate-modal', 'edit-estimate-modal'].forEach(function(id) {
                    const modalEl = document.getElementById(id);
                    if (!modalEl) return;

                    modalEl.addEventListener('shown.bs.modal', function() {
                        const modalBody = modalEl.querySelector('.modal-body');

                        if (modalBody) {
                            modalBody.scrollTop = 0;
                            modalBody.style.overflowY = 'auto';
                        }
                    });
                });
            });
        </script>
    @endpush

@endsection
