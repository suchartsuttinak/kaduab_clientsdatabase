@extends('admin_client.admin_client')
@section('content')
    <div class="escape-edit-page">

        <div class="card shadow-sm mt-4 escape-page-card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-person-lines-fill me-2 text-primary"></i>
                    ข้อมูลการออกจากสถานสงเคราะห์
                </h5>
                <div>
                    <a href="{{ route('escape.index', $client->id) }}"
                        class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm">
                        <i class="bi bi-arrow-left-circle me-1"></i> ย้อนกลับ
                    </a>
                </div>
            </div>

            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-primary text-center">
                            <tr>
                                <th class="text-start">วันที่ออก</th>
                                <th class="text-start">ประเภทการออก</th>
                                <th class="text-start">พฤติการณ์/สาเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $escape->retire_date ? $escape->retire_date->format('d/m/Y') : '-' }}</td>
                                <td>{{ $escape->retire->retire_name ?? '-' }}</td>
                                <td>{{ $escape->stories ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Follow list -->
        <div class="card mt-4 shadow-sm escape-page-card">
            <div
                class="card-header bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-list-check me-2 text-secondary"></i> รายการติดตาม
                </h6>
                <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center px-3"
                    data-bs-toggle="modal" data-bs-target="#addFollowModal{{ $escape->id }}">
                    <i class="bi bi-plus-circle me-1"></i> เพิ่มการติดตาม
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center align-middle escape-follow-table">
                        <thead class="table-dark">
                            <tr>
                                <th>วันที่ติดตาม</th>
                                <th>ครั้งที่</th>
                                <th>พบ/ไม่พบ</th>
                                <th>แจ้งความ</th>
                                <th>ยุติการติดตาม</th>
                                <th class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($escape->follows as $f)
                                <tr>
                                    <td>
                                        <span class="escape-date-chip">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            {{ $f->trace_date ? $f->trace_date->format('d/m/Y') : '-' }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="escape-count-badge">
                                            ครั้งที่ {{ $f->count }}
                                        </span>
                                    </td>

                                    <td>
                                        @if ($f->trac_no === 'พบ')
                                            <span class="escape-status-chip escape-status-found">พบ</span>
                                        @elseif($f->trac_no === 'ไม่พบ')
                                            <span class="escape-status-chip escape-status-notfound">ไม่พบ</span>
                                        @else
                                            <span
                                                class="escape-status-chip escape-status-neutral">{{ $f->trac_no ?: '-' }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $f->report_date ? $f->report_date->format('d/m/Y') : '-' }}
                                    </td>

                                    <td>
                                        {{ $f->stop_date ? $f->stop_date->format('d/m/Y') : '-' }}
                                    </td>

                                    <td class="text-center">
                                        <div class="escape-action-wrap">
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#editFollowModal{{ $f->id }}">
                                                แก้ไข
                                            </button>

                                            <form id="delete-form-follow-{{ $f->id }}"
                                                action="{{ route('escape_follows.delete', $f->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete('delete-form-follow-{{ $f->id }}','คุณต้องการลบข้อมูลการติดตามนี้ใช่หรือไม่')">
                                                    ลบ
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit follow modal -->
                                <div class="modal fade" id="editFollowModal{{ $f->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow-lg">
                                            <div class="modal-header bg-warning text-dark">
                                                <h5 class="modal-title fw-bold">
                                                    <i class="bi bi-pencil-square me-1"></i> แก้ไขการติดตาม (ครั้งที่
                                                    {{ $f->count }})
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('escape_follows.update', $f->id) }}" method="POST"
                                                    class="needs-validation" novalidate>
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="escape_id" value="{{ $escape->id }}">

                                                    <div class="row mb-3 g-3">
                                                        <div class="col-12 col-sm-6 col-md-3">
                                                            <label class="form-label fw-bold">วันที่ติดตาม</label>

                                                            <input type="date" name="trace_date"
                                                                class="form-control form-control-sm @error('trace_date') is-invalid @enderror"
                                                                value="{{ old('trace_date', $f->trace_date?->format('Y-m-d')) }}"
                                                                max="{{ now('Asia/Bangkok')->toDateString() }}" required>

                                                            @error('trace_date')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>

                                                        <div class="col-12 col-md-3">
                                                            <label class="form-label fw-bold">ครั้งที่</label>

                                                            <div
                                                                class="form-control form-control-sm escape-auto-count-display">
                                                                ครั้งที่ {{ old('count', $f->count) }}
                                                            </div>

                                                            <input type="hidden" name="count"
                                                                value="{{ old('count', $f->count) }}">
                                                        </div>

                                                        <div class="col-12 col-md-3">
                                                            <label class="form-label fw-bold">พบ/ไม่พบ</label>

                                                            <select name="trac_no" class="form-select form-select-sm"
                                                                required>
                                                                <option value="พบ"
                                                                    {{ old('trac_no', $f->trac_no) == 'พบ' ? 'selected' : '' }}>
                                                                    พบ
                                                                </option>
                                                                <option value="ไม่พบ"
                                                                    {{ old('trac_no', $f->trac_no) == 'ไม่พบ' ? 'selected' : '' }}>
                                                                    ไม่พบ
                                                                </option>
                                                            </select>
                                                        </div>

                                                        <div class="col-12 mt-2">
                                                            <label class="form-label fw-bold">รายละเอียด</label>

                                                            <input type="text" name="detail"
                                                                class="form-control form-control-sm"
                                                                value="{{ old('detail', $f->detail) }}">
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3 g-3">
                                                        <div class="col-12 col-md-3">
                                                            <label class="form-label fw-bold">วันที่แจ้งความ</label>

                                                            <input type="date" name="report_date"
                                                                class="form-control form-control-sm @error('report_date') is-invalid @enderror"
                                                                value="{{ old('report_date') }}"
                                                                max="{{ now('Asia/Bangkok')->toDateString() }}">

                                                            @error('report_date')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>

                                                        <div class="col-12 col-md-3">
                                                            <label class="form-label fw-bold">วันที่ยุติการติดตาม</label>

                                                            <input type="date" name="stop_date"
                                                                class="form-control form-control-sm @error('stop_date') is-invalid @enderror"
                                                                value="{{ old('stop_date') }}"
                                                                max="{{ now('Asia/Bangkok')->toDateString() }}">

                                                            @error('stop_date')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>

                                                        <div class="col-12 mt-2">
                                                            <label class="form-label fw-bold">การลงโทษ</label>

                                                            <input type="text" name="punish"
                                                                class="form-control form-control-sm"
                                                                value="{{ old('punish', $f->punish) }}">
                                                        </div>

                                                        <div class="col-12 col-md-3 mt-2">
                                                            <label class="form-label fw-bold">วันที่ลงโทษ</label>

                                                            <input type="date" name="punish_date"
                                                                class="form-control form-control-sm @error('punish_date') is-invalid @enderror"
                                                                value="{{ old('punish_date') }}"
                                                                max="{{ now('Asia/Bangkok')->toDateString() }}">

                                                            @error('punish_date')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-12">
                                                            <label class="form-label fw-bold">หมายเหตุ</label>
                                                            <input type="text" name="remark"
                                                                class="form-control form-control-sm"
                                                                value="{{ old('remark', $f->remark) }}">
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="modal-footer d-flex justify-content-between flex-wrap gap-2">
                                                        <button type="submit" class="btn btn-warning btn-sm">
                                                            <i class="bi bi-pencil-square me-1"></i> อัปเดตการติดตาม
                                                        </button>
                                                        <button type="button" class="btn btn-secondary btn-sm"
                                                            data-bs-dismiss="modal">
                                                            <i class="bi bi-x-circle me-1"></i> ปิด
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">ยังไม่มีข้อมูลการติดตาม</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addFollowModal{{ $escape->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content border-0 shadow-sm">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-plus-circle me-1"></i> เพิ่มการติดตาม
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <form action="{{ route('escape_follows.store', $escape->id) }}" method="POST"
                            class="needs-validation" novalidate>
                            @csrf

                            <div class="row mb-3 g-3">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label class="form-label fw-bold">วันที่ติดตาม</label>
                                    <input type="date" name="trace_date"
                                        class="form-control form-control-sm @error('trace_date') is-invalid @enderror"
                                        value="{{ old('trace_date', now('Asia/Bangkok')->toDateString()) }}"
                                        max="{{ now('Asia/Bangkok')->toDateString() }}" required>
                                    @error('trace_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-sm-6 col-md-3">
                                    <label class="form-label fw-bold">ครั้งที่</label>
                                    <div class="form-control form-control-sm escape-auto-count-display">
                                        ระบบกำหนดอัตโนมัติ
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3 g-3">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label class="form-label fw-bold">พบ/ไม่พบ</label>
                                    <select name="trac_no" class="form-select form-select-sm" required>
                                        <option value="พบ">พบ</option>
                                        <option value="ไม่พบ">ไม่พบ</option>
                                    </select>
                                </div>

                                <div class="col-12 col-sm-6 col-md-9">
                                    <label class="form-label fw-bold">รายละเอียด</label>
                                    <input type="text" name="detail" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row mb-3 g-3">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label class="form-label fw-bold">วันที่แจ้งความ</label>
                                    <input type="date" name="report_date"
                                        class="form-control form-control-sm @error('report_date') is-invalid @enderror"
                                        value="{{ old('report_date') }}"
                                        max="{{ now('Asia/Bangkok')->toDateString() }}">
                                    @error('report_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-sm-6 col-md-3">
                                    <label class="form-label fw-bold">วันที่ยุติการติดตาม</label>
                                    <input type="date" name="stop_date"
                                        class="form-control form-control-sm @error('stop_date') is-invalid @enderror"
                                        value="{{ old('stop_date') }}" max="{{ now('Asia/Bangkok')->toDateString() }}">
                                    @error('stop_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-12 mt-2">
                                    <label class="form-label fw-bold">การลงโทษ</label>
                                    <input type="text" name="punish" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="row mb-3 g-3">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label class="form-label fw-bold">วันที่ลงโทษ</label>
                                    <input type="date" name="punish_date"
                                        class="form-control form-control-sm @error('punish_date') is-invalid @enderror"
                                        value="{{ old('punish_date') }}"
                                        max="{{ now('Asia/Bangkok')->toDateString() }}">
                                    @error('punish_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-sm-6 col-md-9">
                                    <label class="form-label fw-bold">หมายเหตุ</label>
                                    <input type="text" name="remark" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="d-flex flex-wrap justify-content-end gap-2 mt-3">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bi bi-save me-1"></i> บันทึก
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle me-1"></i> ปิด
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success text-center fw-bold py-2 mb-3 mt-3">
                {{ session('success') }}
            </div>
        @endif

    </div>

    <style>
        .escape-edit-page .escape-page-card {
            border: 1px solid #e9eef5;
        }

        .escape-edit-page .escape-follow-table th,
        .escape-edit-page .escape-follow-table td {
            vertical-align: middle;
        }

        .escape-edit-page .escape-date-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 7px 12px;
            border-radius: 999px;
            background: linear-gradient(135deg, #eef4ff 0%, #dbeafe 100%);
            color: #1d4ed8;
            font-weight: 700;
            font-size: 0.86rem;
            white-space: nowrap;
        }

        .escape-edit-page .escape-count-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 12px;
            border-radius: 999px;
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            color: #c2410c;
            font-weight: 800;
            font-size: 0.86rem;
            white-space: nowrap;
        }

        .escape-edit-page .escape-status-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 68px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.84rem;
            font-weight: 700;
        }

        .escape-edit-page .escape-status-found {
            background: #ecfdf3;
            color: #15803d;
        }

        .escape-edit-page .escape-status-notfound {
            background: #fef2f2;
            color: #dc2626;
        }

        .escape-edit-page .escape-status-neutral {
            background: #f8fafc;
            color: #475569;
        }

        .escape-edit-page .escape-auto-count-display {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #dee2e6;
            color: #64748b;
            font-weight: 600;
            min-height: 31px;
        }

        .escape-edit-page .escape-action-wrap {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
        }

        @media (max-width: 767.98px) {
            .escape-edit-page .card-header {
                align-items: flex-start !important;
            }

            .escape-edit-page .escape-action-wrap {
                min-width: max-content;
                flex-wrap: nowrap;
            }

            .escape-edit-page .escape-date-chip,
            .escape-edit-page .escape-count-badge {
                font-size: 0.8rem;
                padding: 6px 10px;
            }
        }
    </style>
@endsection
