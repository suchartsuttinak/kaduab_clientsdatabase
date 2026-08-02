@extends('admin_client.admin_client')

@section('content')
<link rel="stylesheet" href="{{ asset('backend/assets/css/escape.css') }}">
<link rel="stylesheet" href="{{ asset('backend/assets/css/escape-responsive-fix.css') }}">

@php
    $today = now('Asia/Bangkok')->toDateString();
    $retireDateMin = $escape->retire_date?->format('Y-m-d');
    $retireDateThai = $escape->retire_date
        ? $escape->retire_date->format('d/m/') . ($escape->retire_date->year + 543)
        : '-';
    $formContext = old('form_context');
@endphp

<div class="container-fluid escape-edit-page">
    <div class="escape-edit-shell">
        <div class="card shadow-sm escape-page-card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-person-lines-fill me-2 text-primary"></i>
                    ข้อมูลการออกจากสถานสงเคราะห์
                </h5>
                <a href="{{ route('escape.index', $client->id) }}"
                   class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-arrow-left-circle me-1"></i> กลับหน้ารายการ
                </a>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0 escape-summary-table">
                        <thead class="table-primary">
                            <tr>
                                <th>วันที่ออก/หลบหนี</th>
                                <th>ประเภทการออก/หลบหนี</th>
                                <th>พฤติการณ์ / สาเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-nowrap">{{ $retireDateThai }}</td>
                                <td>{{ $escape->retire->retire_name ?? '-' }}</td>
                                <td class="text-break">{{ $escape->stories ?: '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-4 shadow-sm escape-page-card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-list-check me-2 text-secondary"></i>
                    รายการติดตาม
                </h6>
                <button type="button"
                        class="btn btn-sm btn-primary d-inline-flex align-items-center px-3"
                        data-bs-toggle="modal"
                        data-bs-target="#addFollowModal{{ $escape->id }}">
                    <i class="bi bi-plus-circle me-1"></i> เพิ่มการติดตาม
                </button>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center align-middle mb-0 escape-follow-table">
                        <thead class="table-dark">
                            <tr>
                                <th>วันที่ติดตาม</th>
                                <th>ครั้งที่</th>
                                <th>ผลการติดตาม</th>
                                <th>วันที่แจ้งความ</th>
                                <th>วันที่ยุติ</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($escape->follows as $follow)
                                @php
                                    $traceDateThai = $follow->trace_date
                                        ? $follow->trace_date->format('d/m/') . ($follow->trace_date->year + 543)
                                        : '-';
                                    $reportDateThai = $follow->report_date
                                        ? $follow->report_date->format('d/m/') . ($follow->report_date->year + 543)
                                        : '-';
                                    $stopDateThai = $follow->stop_date
                                        ? $follow->stop_date->format('d/m/') . ($follow->stop_date->year + 543)
                                        : '-';
                                @endphp
                                <tr>
                                    <td>
                                        <span class="escape-date-chip">
                                            <i class="bi bi-calendar-event me-1"></i>{{ $traceDateThai }}
                                        </span>
                                    </td>
                                    <td><span class="escape-count-badge">ครั้งที่ {{ $follow->count ?? '-' }}</span></td>
                                    <td>
                                        @if ($follow->trac_no === 'พบ')
                                            <span class="escape-status-chip escape-status-found">พบ</span>
                                        @elseif ($follow->trac_no === 'ไม่พบ')
                                            <span class="escape-status-chip escape-status-notfound">ไม่พบ</span>
                                        @else
                                            <span class="escape-status-chip escape-status-neutral">{{ $follow->trac_no ?: '-' }}</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">{{ $reportDateThai }}</td>
                                    <td class="text-nowrap">{{ $stopDateThai }}</td>
                                    <td>
                                        <div class="escape-action-wrap">
                                            <button type="button"
                                                    class="btn btn-sm btn-warning"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editFollowModal{{ $follow->id }}">
                                                <i class="bi bi-pencil-square me-1"></i>แก้ไข
                                            </button>

                                            <form id="delete-form-follow-{{ $follow->id }}"
                                                  action="{{ route('escape_follows.delete', $follow->id) }}"
                                                  method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="confirmDelete('delete-form-follow-{{ $follow->id }}','คุณต้องการลบข้อมูลการติดตามนี้ใช่หรือไม่')">
                                                    <i class="bi bi-trash me-1"></i>ลบ
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-muted">
                                        <i class="bi bi-inboxes d-block fs-2 mb-2"></i>
                                        ยังไม่มีข้อมูลการติดตาม
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal เพิ่มการติดตาม: วางนอกตาราง --}}
<div class="modal fade escape-module-modal"
     id="addFollowModal{{ $escape->id }}"
     tabindex="-1"
     aria-labelledby="addFollowModalLabel{{ $escape->id }}"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('escape_follows.store', $escape->id) }}"
                  method="POST"
                  class="escape-submit-form">
                @csrf
                <input type="hidden" name="form_context" value="escape-follow-add">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="addFollowModalLabel{{ $escape->id }}">
                        <i class="bi bi-plus-circle me-1"></i> เพิ่มการติดตาม
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>

                <div class="modal-body">
                    <div class="escape-modal-section">
                        <h6 class="escape-modal-section-title">
                            <i class="bi bi-search text-primary"></i> ข้อมูลการติดตาม
                        </h6>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label class="form-label">วันที่ติดตาม <span class="text-danger">*</span></label>
                                <input type="date"
                                       name="trace_date"
                                       class="form-control {{ $formContext === 'escape-follow-add' && $errors->has('trace_date') ? 'is-invalid' : '' }}"
                                       value="{{ $formContext === 'escape-follow-add' ? old('trace_date', $today) : $today }}"
                                       min="{{ $retireDateMin }}"
                                       max="{{ $today }}"
                                       required>
                                @if ($formContext === 'escape-follow-add')
                                    @error('trace_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">ครั้งที่</label>
                                <div class="form-control escape-auto-count-display">ระบบกำหนดอัตโนมัติ</div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">ผลการติดตาม <span class="text-danger">*</span></label>
                                <select name="trac_no" class="form-select" required>
                                    <option value="พบ" {{ old('trac_no', 'พบ') === 'พบ' ? 'selected' : '' }}>พบ</option>
                                    <option value="ไม่พบ" {{ old('trac_no') === 'ไม่พบ' ? 'selected' : '' }}>ไม่พบ</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-8">
                                <label class="form-label">รายละเอียด</label>
                                <textarea name="detail" class="form-control" rows="3" maxlength="5000">{{ $formContext === 'escape-follow-add' ? old('detail') : '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="escape-modal-section">
                        <h6 class="escape-modal-section-title">
                            <i class="bi bi-calendar-check text-primary"></i> วันที่เกี่ยวข้อง
                        </h6>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <label class="form-label">วันที่แจ้งความ</label>
                                <input type="date"
                                       name="report_date"
                                       class="form-control {{ $formContext === 'escape-follow-add' && $errors->has('report_date') ? 'is-invalid' : '' }}"
                                       value="{{ $formContext === 'escape-follow-add' ? old('report_date') : '' }}"
                                       min="{{ $retireDateMin }}"
                                       max="{{ $today }}">
                                @if ($formContext === 'escape-follow-add')
                                    @error('report_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">วันที่ยุติการติดตาม</label>
                                <input type="date"
                                       name="stop_date"
                                       class="form-control {{ $formContext === 'escape-follow-add' && $errors->has('stop_date') ? 'is-invalid' : '' }}"
                                       value="{{ $formContext === 'escape-follow-add' ? old('stop_date') : '' }}"
                                       min="{{ $retireDateMin }}"
                                       max="{{ $today }}">
                                @if ($formContext === 'escape-follow-add')
                                    @error('stop_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @endif
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label">วันที่ลงโทษ</label>
                                <input type="date"
                                       name="punish_date"
                                       class="form-control {{ $formContext === 'escape-follow-add' && $errors->has('punish_date') ? 'is-invalid' : '' }}"
                                       value="{{ $formContext === 'escape-follow-add' ? old('punish_date') : '' }}"
                                       min="{{ $retireDateMin }}"
                                       max="{{ $today }}"
                                       data-punish-date
                                       aria-controls="addPunishmentSection{{ $escape->id }}"
                                       aria-expanded="{{ $formContext === 'escape-follow-add' && old('punish_date') ? 'true' : 'false' }}">
                                <div class="form-text escape-punishment-help">
                                    เมื่อกำหนดวันที่ ระบบจะแสดงช่องการลงโทษและบังคับให้กรอก
                                </div>
                                @if ($formContext === 'escape-follow-add')
                                    @error('punish_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="escape-modal-section">
                        <h6 class="escape-modal-section-title">
                            <i class="bi bi-journal-text text-primary"></i> ข้อมูลเพิ่มเติม
                        </h6>
                        <div class="row">
                            @php
                                $showAddPunishment = $formContext === 'escape-follow-add' && filled(old('punish_date'));
                            @endphp
                            <div id="addPunishmentSection{{ $escape->id }}"
                                 class="col-12 escape-punishment-field {{ $showAddPunishment ? '' : 'd-none' }}"
                                 data-punishment-section>
                                <label class="form-label">
                                    การลงโทษ <span class="text-danger">*</span>
                                </label>
                                <textarea name="punish"
                                          class="form-control {{ $formContext === 'escape-follow-add' && $errors->has('punish') ? 'is-invalid' : '' }}"
                                          rows="3"
                                          maxlength="255"
                                          data-punish-input
                                          {{ $showAddPunishment ? 'required' : '' }}>{{ $formContext === 'escape-follow-add' ? old('punish') : '' }}</textarea>
                                <div class="form-text">ต้องระบุเมื่อมีวันที่ลงโทษ และไม่เกิน 255 ตัวอักษร</div>
                                @if ($formContext === 'escape-follow-add')
                                    @error('punish')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @endif
                            </div>
                            <div class="col-12">
                                <label class="form-label">หมายเหตุ</label>
                                <textarea name="remark" class="form-control" rows="3" maxlength="255">{{ $formContext === 'escape-follow-add' ? old('remark') : '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="escape-modal-actions">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i><span>ปิด</span>
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i><span>บันทึกการติดตาม</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal แก้ไขการติดตาม: ทุก Modal อยู่นอก tbody --}}
@foreach ($escape->follows as $follow)
    @php
        $editFollowContext = 'escape-follow-edit-' . $follow->id;
        $isThisFollowContext = $formContext === $editFollowContext;
        $editPunishDateValue = $isThisFollowContext
            ? old('punish_date', $follow->punish_date?->format('Y-m-d'))
            : $follow->punish_date?->format('Y-m-d');
        $showEditPunishment = filled($editPunishDateValue);
    @endphp
    <div class="modal fade escape-module-modal"
         id="editFollowModal{{ $follow->id }}"
         tabindex="-1"
         aria-labelledby="editFollowModalLabel{{ $follow->id }}"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form action="{{ route('escape_follows.update', $follow->id) }}"
                      method="POST"
                      class="escape-submit-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="escape_id" value="{{ $escape->id }}">
                    <input type="hidden" name="count" value="{{ $follow->count }}">
                    <input type="hidden" name="form_context" value="{{ $editFollowContext }}">

                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold" id="editFollowModalLabel{{ $follow->id }}">
                            <i class="bi bi-pencil-square me-1"></i>
                            แก้ไขการติดตาม ครั้งที่ {{ $follow->count ?? '-' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                    </div>

                    <div class="modal-body">
                        <div class="escape-modal-section">
                            <h6 class="escape-modal-section-title">
                                <i class="bi bi-search text-warning"></i> ข้อมูลการติดตาม
                            </h6>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">วันที่ติดตาม <span class="text-danger">*</span></label>
                                    <input type="date"
                                           name="trace_date"
                                           class="form-control {{ $isThisFollowContext && $errors->has('trace_date') ? 'is-invalid' : '' }}"
                                           value="{{ $isThisFollowContext ? old('trace_date', $follow->trace_date?->format('Y-m-d')) : $follow->trace_date?->format('Y-m-d') }}"
                                           min="{{ $retireDateMin }}"
                                           max="{{ $today }}"
                                           required>
                                    @if ($isThisFollowContext)
                                        @error('trace_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @endif
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">ครั้งที่</label>
                                    <div class="form-control escape-auto-count-display">ครั้งที่ {{ $follow->count ?? '-' }}</div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label">ผลการติดตาม <span class="text-danger">*</span></label>
                                    @php
                                        $selectedTrace = $isThisFollowContext ? old('trac_no', $follow->trac_no) : $follow->trac_no;
                                    @endphp
                                    <select name="trac_no" class="form-select" required>
                                        <option value="พบ" {{ $selectedTrace === 'พบ' ? 'selected' : '' }}>พบ</option>
                                        <option value="ไม่พบ" {{ $selectedTrace === 'ไม่พบ' ? 'selected' : '' }}>ไม่พบ</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-8">
                                    <label class="form-label">รายละเอียด</label>
                                    <textarea name="detail" class="form-control" rows="3" maxlength="5000">{{ $isThisFollowContext ? old('detail', $follow->detail) : $follow->detail }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="escape-modal-section">
                            <h6 class="escape-modal-section-title">
                                <i class="bi bi-calendar-check text-warning"></i> วันที่เกี่ยวข้อง
                            </h6>
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">วันที่แจ้งความ</label>
                                    <input type="date"
                                           name="report_date"
                                           class="form-control {{ $isThisFollowContext && $errors->has('report_date') ? 'is-invalid' : '' }}"
                                           value="{{ $isThisFollowContext ? old('report_date', $follow->report_date?->format('Y-m-d')) : $follow->report_date?->format('Y-m-d') }}"
                                           min="{{ $retireDateMin }}"
                                           max="{{ $today }}">
                                    @if ($isThisFollowContext)
                                        @error('report_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @endif
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label">วันที่ยุติการติดตาม</label>
                                    <input type="date"
                                           name="stop_date"
                                           class="form-control {{ $isThisFollowContext && $errors->has('stop_date') ? 'is-invalid' : '' }}"
                                           value="{{ $isThisFollowContext ? old('stop_date', $follow->stop_date?->format('Y-m-d')) : $follow->stop_date?->format('Y-m-d') }}"
                                           min="{{ $retireDateMin }}"
                                           max="{{ $today }}">
                                    @if ($isThisFollowContext)
                                        @error('stop_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @endif
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label">วันที่ลงโทษ</label>
                                    <input type="date"
                                           name="punish_date"
                                           class="form-control {{ $isThisFollowContext && $errors->has('punish_date') ? 'is-invalid' : '' }}"
                                           value="{{ $editPunishDateValue }}"
                                           min="{{ $retireDateMin }}"
                                           max="{{ $today }}"
                                           data-punish-date
                                           aria-controls="editPunishmentSection{{ $follow->id }}"
                                           aria-expanded="{{ $showEditPunishment ? 'true' : 'false' }}">
                                    <div class="form-text escape-punishment-help">
                                        เมื่อกำหนดวันที่ ระบบจะแสดงช่องการลงโทษและบังคับให้กรอก
                                    </div>
                                    @if ($isThisFollowContext)
                                        @error('punish_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="escape-modal-section">
                            <h6 class="escape-modal-section-title">
                                <i class="bi bi-journal-text text-warning"></i> ข้อมูลเพิ่มเติม
                            </h6>
                            <div class="row">
                                <div id="editPunishmentSection{{ $follow->id }}"
                                     class="col-12 escape-punishment-field {{ $showEditPunishment ? '' : 'd-none' }}"
                                     data-punishment-section>
                                    <label class="form-label">
                                        การลงโทษ <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="punish"
                                              class="form-control {{ $isThisFollowContext && $errors->has('punish') ? 'is-invalid' : '' }}"
                                              rows="3"
                                              maxlength="255"
                                              data-punish-input
                                              {{ $showEditPunishment ? 'required' : '' }}>{{ $isThisFollowContext ? old('punish', $follow->punish) : $follow->punish }}</textarea>
                                    <div class="form-text">ต้องระบุเมื่อมีวันที่ลงโทษ และไม่เกิน 255 ตัวอักษร</div>
                                    @if ($isThisFollowContext)
                                        @error('punish')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @endif
                                </div>
                                <div class="col-12">
                                    <label class="form-label">หมายเหตุ</label>
                                    <textarea name="remark" class="form-control" rows="3" maxlength="255">{{ $isThisFollowContext ? old('remark', $follow->remark) : $follow->remark }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="escape-modal-actions">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i><span>ปิด</span>
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check2-circle"></i><span>อัปเดตการติดตาม</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<style>
.escape-edit-page .escape-date-chip,
.escape-edit-page .escape-count-badge,
.escape-edit-page .escape-status-chip{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:999px;
    white-space:nowrap;
    font-weight:700;
}
.escape-edit-page .escape-date-chip{
    gap:4px;
    padding:7px 12px;
    background:linear-gradient(135deg,#eef4ff 0%,#dbeafe 100%);
    color:#1d4ed8;
    font-size:.86rem;
}
.escape-edit-page .escape-count-badge{
    padding:7px 12px;
    background:linear-gradient(135deg,#fff7ed 0%,#ffedd5 100%);
    color:#c2410c;
    font-size:.86rem;
    font-weight:800;
}
.escape-edit-page .escape-status-chip{
    min-width:68px;
    padding:6px 12px;
    font-size:.84rem;
}
.escape-edit-page .escape-status-found{background:#ecfdf3;color:#15803d;}
.escape-edit-page .escape-status-notfound{background:#fef2f2;color:#dc2626;}
.escape-edit-page .escape-status-neutral{background:#f8fafc;color:#475569;}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.escape-module-modal').forEach(function (modalElement) {
        if (modalElement.parentElement !== document.body) {
            document.body.appendChild(modalElement);
        }
        modalElement.addEventListener('shown.bs.modal', function () {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops[backdrops.length - 1]?.classList.add('escape-module-backdrop');
        });
    });

    function syncPunishmentField(form, clearWhenHidden = true) {
        const punishDateInput = form.querySelector('[data-punish-date]');
        const punishmentSection = form.querySelector('[data-punishment-section]');
        const punishInput = form.querySelector('[data-punish-input]');

        if (!punishDateInput || !punishmentSection || !punishInput) return;

        const hasPunishDate = punishDateInput.value.trim() !== '';

        punishmentSection.classList.toggle('d-none', !hasPunishDate);
        punishmentSection.hidden = !hasPunishDate;
        punishInput.required = hasPunishDate;
        punishDateInput.setAttribute('aria-expanded', hasPunishDate ? 'true' : 'false');

        if (!hasPunishDate && clearWhenHidden) {
            punishInput.value = '';
            punishInput.classList.remove('is-invalid');
            punishInput.setCustomValidity('');
        }
    }

    function syncRelatedDateMinimums(form) {
        const traceDateInput = form.querySelector('[name="trace_date"]');
        if (!traceDateInput || !traceDateInput.value) return;

        ['report_date', 'stop_date', 'punish_date'].forEach(function (fieldName) {
            const input = form.querySelector('[name="' + fieldName + '"]');
            if (input) input.min = traceDateInput.value;
        });
    }

    document.querySelectorAll('.escape-submit-form').forEach(function (form) {
        const punishDateInput = form.querySelector('[data-punish-date]');
        const traceDateInput = form.querySelector('[name="trace_date"]');

        syncPunishmentField(form, true);
        syncRelatedDateMinimums(form);

        punishDateInput?.addEventListener('change', function () {
            syncPunishmentField(form, true);
        });

        punishDateInput?.addEventListener('input', function () {
            syncPunishmentField(form, true);
        });

        traceDateInput?.addEventListener('change', function () {
            syncRelatedDateMinimums(form);
        });

        form.addEventListener('submit', function () {
            syncPunishmentField(form, true);

            const button = form.querySelector('button[type="submit"]');
            if (!button || button.disabled) return;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span>กำลังบันทึก...</span>';
        });
    });

    @if ($errors->any() && $formContext)
        const formContext = @json($formContext);
        let modalId = null;

        if (formContext === 'escape-follow-add') {
            modalId = 'addFollowModal{{ $escape->id }}';
        } else if (formContext.startsWith('escape-follow-edit-')) {
            modalId = 'editFollowModal' + formContext.replace('escape-follow-edit-', '');
        }

        if (modalId) {
            const targetModal = document.getElementById(modalId);
            if (targetModal && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(targetModal).show();
            }
        }
    @endif
});
</script>
@endsection
