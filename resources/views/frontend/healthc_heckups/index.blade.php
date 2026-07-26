@extends('admin_client.admin_client')

@section('title', 'ข้อมูลการตรวจสุขภาพ')

@section('content')
<div class="container-fluid py-3 healthc-page">

    @php
        $hasRows = isset($healthcHeckups) && $healthcHeckups->count() > 0;

        $hasFilter = request()->filled('keyword')
            || request()->filled('client_id')
            || request()->filled('date_from')
            || request()->filled('date_to')
            || request()->filled('checkup_result');

        $showSection = $hasRows || $hasFilter;
    @endphp

    <div class="healthc-header mb-3">
        <div class="healthc-header-left">
            <div class="healthc-header-icon">
                <i class="bi bi-heart-pulse"></i>
            </div>

            <div>
                <h3 class="healthc-title mb-1">ข้อมูลการตรวจสุขภาพประจำปี</h3>
                <div class="healthc-subtitle">
                    บันทึก / ค้นหา / แก้ไข / ลบ / ออกรายงาน
                </div>
            </div>
        </div>

          @if($hasRows)
        <div class="healthc-header-right">
            <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                <i class="bi bi-plus-circle"></i> เพิ่มข้อมูล
            </button>

          
                <a href="{{ route('healthc_heckups.report', request()->query()) }}"
                   target="_blank"
                   class="btn btn-outline-secondary">
                    <i class="bi bi-printer"></i> รายงาน
                </a>
        </div>
         @endif
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show healthc-alert">
            <strong>พบข้อผิดพลาด</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($showSection)
        <div class="card border-0 shadow-sm mb-3 healthc-filter-card">
            <div class="card-body">
                <form method="GET" action="{{ route('healthc_heckups.index') }}">
                    <div class="row g-3">
                        <div class="col-12 col-md-6 col-xl-3">
                            <label class="form-label">ค้นหาชื่อเด็ก / สถานพยาบาล / รายละเอียด</label>
                            <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}" placeholder="พิมพ์คำค้นหา">
                        </div>

                        <div class="col-12 col-md-6 col-xl-3">
                            <label class="form-label">ผู้รับบริการ</label>
                            <select name="client_id" class="form-select">
                                <option value="">-- ทั้งหมด --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->fullname ?? (($client->first_name ?? '') . ' ' . ($client->last_name ?? '')) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6 col-xl-2">
                            <label class="form-label">วันที่เริ่มต้น</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>

                        <div class="col-12 col-md-6 col-xl-2">
                            <label class="form-label">วันที่สิ้นสุด</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>

                        <div class="col-12 col-md-6 col-xl-2">
                            <label class="form-label">ผลการตรวจ</label>
                            <select name="checkup_result" class="form-select">
                                <option value="">-- ทั้งหมด --</option>
                                <option value="normal" {{ request('checkup_result') === 'normal' ? 'selected' : '' }}>ปกติ</option>
                                <option value="abnormal" {{ request('checkup_result') === 'abnormal' ? 'selected' : '' }}>ไม่ปกติ</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> ค้นหา
                                </button>

                                <a href="{{ route('healthc_heckups.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-counterclockwise"></i> ล้างค่า
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm healthc-table-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width:70px;">ลำดับ</th>
                                <th style="min-width:180px;">ชื่อ-สกุล</th>
                                <th style="min-width:130px;">วันที่ตรวจ</th>
                                <th style="min-width:180px;">สถานพยาบาล</th>
                                <th style="min-width:120px;">ผลการตรวจ</th>
                                <th style="min-width:220px;">รายละเอียด</th>
                                <th style="min-width:120px;">เอกสาร</th>
                                <th style="min-width:150px;">ผู้บันทึก</th>
                                <th class="text-center" style="min-width:220px;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($healthcHeckups as $index => $item)
                                <tr>
                                    <td class="text-center">
                                        {{ $healthcHeckups->firstItem() + $index }}
                                    </td>
                                    <td>
                                        {{ $item->client->fullname ?? (($item->client->first_name ?? '') . ' ' . ($item->client->last_name ?? '')) }}
                                    </td>
                                    <td>
                                        {{ optional($item->checkup_date)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        {{ $item->hospital_name }}
                                    </td>
                                    <td>
                                        @if($item->checkup_result === 'normal')
                                            <span class="badge bg-success">ปกติ</span>
                                        @else
                                            <span class="badge bg-danger">ไม่ปกติ</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $item->abnormal_detail ?: '-' }}
                                    </td>
                                    <td>
                                      @if($item->medical_document)
                                            <a href="{{ asset($item->medical_document) }}"
                                            target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-earmark-pdf"></i> เปิดไฟล์
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $item->recorder->name ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-wrap justify-content-center gap-1">
                                            <button type="button"
                                                    class="btn btn-sm btn-warning"
                                                    onclick="openEditModal({{ $item->id }})">
                                                <i class="bi bi-pencil-square"></i> แก้ไข
                                            </button>

                                            <form action="{{ route('healthc_heckups.delete', $item->id) }}"
                                                  method="POST"
                                                  class="d-inline js-delete-healthc-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i> ลบ
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">ไม่พบข้อมูล</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($healthcHeckups->hasPages())
                    <div class="mt-3">
                        {{ $healthcHeckups->links() }}
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="healthc-empty-card">
            <div class="healthc-empty-body">
                <div class="healthc-empty-icon">
                    <i class="bi bi-heart-pulse"></i>
                </div>

                <h4>ยังไม่มีข้อมูลการตรวจสุขภาพ</h4>

                <p>
                    ระบบจะซ่อนปุ่มรายงาน ช่องค้นหา และตารางไว้ก่อน
                    เพื่อให้หน้าจอดูสะอาด ใช้งานง่าย และไม่แสดงตารางว่างโดยไม่จำเป็น
                </p>

                <button type="button" class="btn btn-primary px-4" onclick="openCreateModal()">
                    <i class="bi bi-plus-circle"></i> เพิ่มข้อมูลการตรวจสุขภาพ
                </button>
            </div>
        </div>
    @endif
</div>

{{-- Modal --}}
<div class="modal fade healthc-modal" id="healthcHeckupModal" tabindex="-1" aria-labelledby="healthcHeckupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered healthc-modal-dialog">
        <div class="modal-content healthc-modal-content">
            <form id="healthcHeckupForm" method="POST" enctype="multipart/form-data" class="healthc-modal-form">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header healthc-modal-header">
                    <h5 class="modal-title" id="healthcHeckupModalLabel">เพิ่มข้อมูลการตรวจสุขภาพ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body healthc-modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">ผู้รับบริการ <span class="text-danger">*</span></label>

                            <input type="text"
                                   id="client_search"
                                   class="form-control"
                                   list="client_list"
                                   placeholder="พิมพ์ค้นหาชื่อผู้รับบริการ..."
                                   autocomplete="off"
                                   required>

                            <datalist id="client_list">
                                @foreach($clients as $client)
                                    <option
                                        value="{{ $client->fullname ?? (($client->first_name ?? '') . ' ' . ($client->last_name ?? '')) }}"
                                        data-id="{{ $client->id }}">
                                    </option>
                                @endforeach
                            </datalist>

                            <input type="hidden" name="client_id" id="client_id" required>
                            <div class="form-text">พิมพ์ชื่อ แล้วเลือกชื่อจากรายการที่แสดง</div>
                        </div>

                      <div class="col-12 col-md-6">
                            <label class="form-label">วันที่ตรวจ <span class="text-danger">*</span></label>
                            <input type="date"
                                name="checkup_date"
                                id="checkup_date"
                                class="form-control"
                                max="{{ now('Asia/Bangkok')->toDateString() }}"
                                required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">สถานพยาบาล <span class="text-danger">*</span></label>
                            <input type="text" name="hospital_name" id="hospital_name" class="form-control" maxlength="255" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label d-block">ผลการตรวจ <span class="text-danger">*</span></label>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="checkup_result" id="result_normal" value="normal" checked>
                                <label class="form-check-label" for="result_normal">ปกติ</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="checkup_result" id="result_abnormal" value="abnormal">
                                <label class="form-check-label" for="result_abnormal">ไม่ปกติ</label>
                            </div>
                        </div>

                        <div class="col-12 d-none" id="abnormalDetailWrap">
                            <label class="form-label">ระบุรายละเอียดกรณีไม่ปกติ</label>
                            <textarea name="abnormal_detail" id="abnormal_detail" rows="4" class="form-control"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">เอกสารทางการแพทย์ (PDF)</label>
                            <input type="file" name="medical_document" id="medical_document" class="form-control" accept="application/pdf">
                            <div class="form-text">อัปโหลดได้เฉพาะไฟล์ PDF ขนาดไม่เกิน 5 MB</div>
                            <div id="currentFileBlock" class="mt-2 d-none">
                                <a href="#" target="_blank" id="currentFileLink" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-pdf"></i> เปิดไฟล์ปัจจุบัน
                                </a>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">ผู้บันทึก</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->name ?? '-' }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="modal-footer healthc-modal-footer d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> ปิด
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.healthc-header{
    display:flex;
    flex-wrap:wrap;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    padding:16px 18px;
    border-radius:18px;
    background:linear-gradient(135deg,#ffffff 0%,#f7fbff 100%);
    box-shadow:0 10px 30px rgba(0,0,0,0.06);
}

.healthc-header-left{
    display:flex;
    align-items:center;
    gap:14px;
}

.healthc-header-icon{
    width:54px;
    height:54px;
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#e8f2ff;
    color:#0d6efd;
    font-size:26px;
    box-shadow:0 6px 18px rgba(13,110,253,.18);
    flex:0 0 auto;
}

.healthc-title{
    font-size:20px;
    font-weight:700;
    color:#1f2937;
}

.healthc-subtitle{
    font-size:13px;
    color:#6b7280;
}

.healthc-header-right{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
}

.healthc-alert{
    border-radius:14px;
}

.healthc-filter-card,
.healthc-table-card{
    border-radius:18px;
    overflow:hidden;
}

.healthc-empty-card{
    border-radius:22px;
    background:linear-gradient(135deg,#ffffff 0%,#f8fbff 100%);
    box-shadow:0 12px 32px rgba(15,23,42,.08);
    border:0;
}

.healthc-empty-body{
    text-align:center;
    padding:56px 20px;
}

.healthc-empty-icon{
    width:86px;
    height:86px;
    border-radius:50%;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    background:#eef6ff;
    color:#0d6efd;
    font-size:42px;
    box-shadow:0 12px 28px rgba(13,110,253,.14);
    margin-bottom:18px;
}

.healthc-empty-card h4{
    font-size:22px;
    font-weight:700;
    color:#1f2937;
    margin-bottom:10px;
}

.healthc-empty-card p{
    max-width:600px;
    margin:0 auto 24px;
    color:#6b7280;
    line-height:1.8;
}

.healthc-page .table td,
.healthc-page .table th{
    font-size:14px;
}

.healthc-page .modal .form-label,
.healthc-page .form-label{
    font-weight:600;
    margin-bottom:6px;
}

.healthc-modal{
    z-index: 2000;
}

.healthc-modal-dialog{
    width:min(100% - 20px, 760px);
    max-width:none;
    margin:10px auto;
}

.healthc-modal-content{
    border:0;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 18px 46px rgba(15, 23, 42, 0.20);
    background:#fff;
}

.healthc-modal-form{
    display:flex;
    flex-direction:column;
    max-height:calc(100vh - 20px);
}

.healthc-modal-header{
    padding:16px 18px;
    border-bottom:1px solid #e9eef5;
    background:#fff;
    flex:0 0 auto;
}

.healthc-modal-body{
    padding:18px;
    overflow-y:auto;
    overflow-x:hidden;
    flex:1 1 auto;
    background:#fff;
}

.healthc-modal-footer{
    padding:14px 18px;
    border-top:1px solid #e9eef5;
    background:#fff;
    flex:0 0 auto;
}

.healthc-modal-footer .btn{
    min-width:120px;
}

.healthc-page .form-control,
.healthc-page .form-select{
    min-height:44px;
    font-size:14px;
    border-radius:14px;
}

.healthc-page textarea.form-control{
    min-height:110px;
    resize:vertical;
}

.healthc-page .form-text{
    font-size:12px;
    color:#7b8794;
}

.healthc-page .form-check-inline{
    margin-right:16px;
}

.healthc-page .badge{
    font-size:12px;
}

body.healthc-modal-active{
    overflow:hidden !important;
    padding-right:0 !important;
}

body.healthc-modal-active .app-sidebar-menu,
body.healthc-modal-active #sidebar-menu,
body.healthc-modal-active .leftside-menu,
body.healthc-modal-active .sidebar,
body.healthc-modal-active .app-menu,
body.healthc-modal-active #layout-menu{
    z-index:1 !important;
    pointer-events:none;
}

body.healthc-modal-active .modal{
    z-index:2000 !important;
}

@media (max-width:768px){
    .healthc-header{
        flex-direction:column;
        align-items:flex-start;
    }

    .healthc-header-right{
        width:100%;
    }

    .healthc-header-right .btn{
        flex:1;
    }
}

@media (max-width: 991.98px){
    .healthc-modal-dialog{
        width:calc(100% - 18px);
        margin:9px auto;
    }

    .healthc-modal-form{
        max-height:calc(100vh - 18px);
    }

    .healthc-modal-body{
        padding:16px;
    }

    .healthc-modal-footer{
        padding:12px 16px;
    }
}

@media (max-width: 767.98px){
    .healthc-modal-dialog{
        width:calc(100% - 12px);
        margin:6px auto;
    }

    .healthc-modal-content{
        border-radius:14px;
    }

    .healthc-modal-form{
        max-height:calc(100vh - 12px);
    }

    .healthc-modal-header{
        padding:14px 14px 12px;
    }

    .healthc-modal-body{
        padding:14px;
    }

    .healthc-modal-footer{
        padding:12px 14px 14px;
    }

    .healthc-modal-footer .btn{
        flex:1 1 calc(50% - 4px);
        min-width:0;
    }

    .healthc-page .form-control,
    .healthc-page .form-select{
        min-height:42px;
        font-size:14px;
    }

    .healthc-page textarea.form-control{
        min-height:96px;
    }
}

@media (max-width: 575.98px){
    .healthc-header{
        padding:14px;
        border-radius:16px;
    }

    .healthc-header-left{
        align-items:flex-start;
    }

    .healthc-header-icon{
        width:46px;
        height:46px;
        border-radius:14px;
        font-size:22px;
    }

    .healthc-title{
        font-size:18px;
    }

    .healthc-header-right .btn{
        width:100%;
        flex:1 1 100%;
    }

    .healthc-empty-body{
        padding:42px 16px;
    }

    .healthc-empty-icon{
        width:74px;
        height:74px;
        font-size:36px;
    }

    .healthc-empty-card h4{
        font-size:19px;
    }

    .healthc-modal-dialog{
        width:calc(100% - 8px);
        margin:4px auto;
    }

    .healthc-modal-content{
        border-radius:12px;
    }

    .healthc-modal-form{
        max-height:calc(100vh - 8px);
    }

    .healthc-modal-header{
        padding:12px 12px 10px;
    }

    .healthc-modal-body{
        padding:12px;
    }

    .healthc-modal-footer{
        padding:10px 12px 12px;
    }

    .healthc-modal-footer .btn{
        width:100%;
        flex:1 1 100%;
    }

    .healthc-page .modal-title{
        font-size:16px;
        line-height:1.35;
    }

    .healthc-page .form-label{
        font-size:14px;
    }

    .healthc-page .form-control,
    .healthc-page .form-select{
        min-height:40px;
        font-size:13px;
        padding:.5rem .75rem;
        border-radius:12px;
    }

    .healthc-page textarea.form-control{
        min-height:90px;
    }

    .healthc-page .form-check-inline{
        display:block;
        margin-right:0;
        margin-bottom:8px;
    }

    .healthc-page .form-text{
        font-size:11px;
    }
}
</style>

<script>
function toggleAbnormalDetail(forceValue = null) {
    const abnormal = document.getElementById('result_abnormal');
    const wrap = document.getElementById('abnormalDetailWrap');
    const textarea = document.getElementById('abnormal_detail');

    const abnormalSelected = forceValue !== null
        ? forceValue === 'abnormal'
        : abnormal.checked;

    if (abnormalSelected) {
        wrap.classList.remove('d-none');
    } else {
        wrap.classList.add('d-none');
        textarea.value = '';
    }
}

function syncClientIdFromName() {
    const searchInput = document.getElementById('client_search');
    const hiddenClientId = document.getElementById('client_id');
    const options = document.querySelectorAll('#client_list option');

    const inputValue = (searchInput.value || '').trim();
    hiddenClientId.value = '';

    options.forEach(function(option) {
        if (option.value.trim() === inputValue) {
            hiddenClientId.value = option.dataset.id;
        }
    });
}

function openCreateModal() {
    const form = document.getElementById('healthcHeckupForm');
    const method = document.getElementById('formMethod');

    document.getElementById('healthcHeckupModalLabel').innerText = 'เพิ่มข้อมูลการตรวจสุขภาพ';
    form.action = "{{ route('healthc_heckups.store') }}";
    method.value = 'POST';

    form.reset();
    document.getElementById('client_search').value = '';
    document.getElementById('client_id').value = '';
    document.getElementById('result_normal').checked = true;
    document.getElementById('medical_document').value = '';

    document.getElementById('currentFileBlock').classList.add('d-none');
    document.getElementById('currentFileLink').href = '#';

    toggleAbnormalDetail('normal');

    const modal = new bootstrap.Modal(document.getElementById('healthcHeckupModal'));
    modal.show();
}

function openEditModal(id) {
    fetch("{{ url('healthc-checkups/edit-json') }}/" + id)
        .then(response => response.json())
        .then(data => {
            const form = document.getElementById('healthcHeckupForm');
            const method = document.getElementById('formMethod');
            const clientOptions = document.querySelectorAll('#client_list option');

            document.getElementById('healthcHeckupModalLabel').innerText = 'แก้ไขข้อมูลการตรวจสุขภาพ';
            form.action = "{{ url('healthc-checkups/update') }}/" + id;
            method.value = 'PUT';

            document.getElementById('client_id').value = data.client_id ?? '';
            document.getElementById('checkup_date').value = data.checkup_date ?? '';
            document.getElementById('hospital_name').value = data.hospital_name ?? '';
            document.getElementById('abnormal_detail').value = data.abnormal_detail ?? '';
            document.getElementById('medical_document').value = '';

            let selectedClientName = '';
            clientOptions.forEach(function(option) {
                if (String(option.dataset.id) === String(data.client_id)) {
                    selectedClientName = option.value;
                }
            });
            document.getElementById('client_search').value = selectedClientName;

            if (data.checkup_result === 'abnormal') {
                document.getElementById('result_abnormal').checked = true;
            } else {
                document.getElementById('result_normal').checked = true;
            }

            if (data.medical_document_url) {
                document.getElementById('currentFileBlock').classList.remove('d-none');
                document.getElementById('currentFileLink').href = data.medical_document_url;
            } else {
                document.getElementById('currentFileBlock').classList.add('d-none');
                document.getElementById('currentFileLink').href = '#';
            }

            toggleAbnormalDetail(data.checkup_result);

            const modal = new bootstrap.Modal(document.getElementById('healthcHeckupModal'));
            modal.show();
        });
}

document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('healthcHeckupModal');

    modalElement.addEventListener('show.bs.modal', function () {
        document.body.classList.add('healthc-modal-active');
    });

    modalElement.addEventListener('hidden.bs.modal', function () {
        document.body.classList.remove('healthc-modal-active');
    });

    document.getElementById('result_normal').addEventListener('change', function () {
        toggleAbnormalDetail();
    });

    document.getElementById('result_abnormal').addEventListener('change', function () {
        toggleAbnormalDetail();
    });

    document.getElementById('client_search').addEventListener('input', function () {
        syncClientIdFromName();
    });

    document.getElementById('client_search').addEventListener('change', function () {
        syncClientIdFromName();
    });

    document.getElementById('healthcHeckupForm').addEventListener('submit', function(e) {
        syncClientIdFromName();

        if (!document.getElementById('client_id').value) {
            e.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือกผู้รับบริการ',
                text: 'โปรดพิมพ์ชื่อและเลือกจากรายการที่ระบบแสดง',
                confirmButtonText: 'ตกลง'
            });

            document.getElementById('client_search').focus();
            return false;
        }
    });

    toggleAbnormalDetail();

    document.querySelectorAll('.js-delete-healthc-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: 'เมื่อลบแล้วจะไม่สามารถกู้คืนข้อมูลนี้ได้',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'บันทึกสำเร็จ',
            text: @json(session('success')),
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            position: 'top-end',
            toast: true
        });
    @endif
});
</script>
@endsection