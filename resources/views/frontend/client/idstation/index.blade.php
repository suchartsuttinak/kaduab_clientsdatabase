@extends('admin_client.admin_client')

@section('content')

@php
    $processingStatuses = ['processing', 'in_progress'];

    $totalCount = $idstations->count();

    $processingCount = $idstations
        ->whereIn('process_status', $processingStatuses)
        ->count();

    $receivedCount = $idstations
        ->where('process_status', 'received_status')
        ->count();

    $over90Count = $idstations->filter(function ($item) use ($processingStatuses) {
        $days = $item->receive_date
            ? \Carbon\Carbon::parse($item->receive_date)
                ->startOfDay()
                ->diffInDays(now('Asia/Bangkok')->startOfDay())
            : 0;

        $days = (int) floor($days);

        return in_array($item->process_status, $processingStatuses, true)
            && $days >= 90;
    })->count();

    $over180Count = $idstations->filter(function ($item) use ($processingStatuses) {
        $days = $item->receive_date
            ? \Carbon\Carbon::parse($item->receive_date)
                ->startOfDay()
                ->diffInDays(now('Asia/Bangkok')->startOfDay())
            : 0;

        $days = (int) floor($days);

        return in_array($item->process_status, $processingStatuses, true)
            && $days >= 180;
    })->count();

    $clientDisplayName = $client->fullname
        ?? trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
@endphp

<div class="idstation-page">

    <div class="idstation-top-card mb-4">
        <div class="idstation-top-content">
            <div class="idstation-top-title">
                <div class="idstation-top-icon">
                    <i class="bi bi-person-vcard"></i>
                </div>

                <div class="idstation-top-text">
                    <h5>การดำเนินการด้านสถานะบุคคล</h5>

                    <div class="idstation-client-name">
                        ผู้รับบริการ:
                        <span>{{ $clientDisplayName ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="idstation-top-actions">
                <a href="{{ route('client.show', $client->id) }}"
                   class="btn btn-outline-secondary btn-sm idstation-top-btn">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>กลับ</span>
                </a>

                <button type="button"
                        class="btn btn-primary btn-sm idstation-top-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#createIdstationModal">
                    <i class="bi bi-plus-circle"></i>
                    <span>เพิ่มข้อมูล</span>
                </button>
            </div>
        </div>
    </div>

    <div class="idstation-summary-card mb-4">
        <div class="idstation-summary-header">
            <div>
                <div class="idstation-summary-kicker">
                    สรุปการดำเนินการ
                </div>

                <h5 class="idstation-summary-title mb-0">
                    กระบวนการช่วยเหลือด้านสถานะบุคคล
                </h5>
            </div>

            <div class="idstation-summary-year">
                ปีปัจจุบัน {{ now('Asia/Bangkok')->year + 543 }}
            </div>
        </div>

        <div class="idstation-progress-wrap">
            <div class="idstation-progress-line"></div>

            <div class="idstation-step active">
                <div class="idstation-step-circle">{{ $totalCount }}</div>
                <div class="idstation-step-label">รับเรื่องทั้งหมด</div>
            </div>

            <div class="idstation-step warning">
                <div class="idstation-step-circle">{{ $processingCount }}</div>
                <div class="idstation-step-label">อยู่ระหว่างดำเนินการ</div>
            </div>

            <div class="idstation-step success">
                <div class="idstation-step-circle">{{ $receivedCount }}</div>
                <div class="idstation-step-label">ได้รับสถานะแล้ว</div>
            </div>

            <div class="idstation-step danger">
                <div class="idstation-step-circle">{{ $over90Count }}</div>
                <div class="idstation-step-label">เกิน 90 วัน</div>
            </div>
        </div>
    </div>

    @if($over180Count > 0)
        <div class="alert alert-danger idstation-alert">
            <i class="bi bi-exclamation-octagon me-1"></i>
            พบรายการที่อยู่ระหว่างดำเนินการเกิน 180 วัน จำนวน {{ $over180Count }} รายการ
        </div>
    @elseif($over90Count > 0)
        <div class="alert alert-warning idstation-alert">
            <i class="bi bi-exclamation-triangle me-1"></i>
            พบรายการที่อยู่ระหว่างดำเนินการเกิน 90 วัน จำนวน {{ $over90Count }} รายการ
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show auto-dismiss-alert idstation-alert" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show auto-dismiss-alert idstation-alert" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger idstation-alert">
            <div class="fw-bold mb-1">
                <i class="bi bi-exclamation-triangle me-1"></i>
                กรุณาตรวจสอบข้อมูล
            </div>

            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="idstation-table-card">
        <div class="idstation-table-header">
            <div>
                <h6>รายการดำเนินการด้านสถานะบุคคล</h6>
                <p>เลื่อนตารางซ้าย-ขวาได้ และสามารถจัดการข้อมูลได้จากคอลัมน์ด้านขวา</p>
            </div>
        </div>

        <div class="idstation-table-body">
            <div class="idstation-table-scroll">
                <table class="table table-hover align-middle mb-0 idstation-table">
                    <thead>
                        <tr>
                            <th style="width: 64px;">ลำดับ</th>
                            <th style="width: 135px;">วันที่รับเรื่อง</th>
                            <th style="min-width: 230px;">รายการทางทะเบียน</th>
                            <th style="width: 145px;">ระยะเวลาดำเนินการ</th>
                            <th style="width: 165px;">การดำเนินการ</th>
                            <th style="width: 145px;">วันที่ได้รับสถานะ</th>
                            <th style="min-width: 230px;">สถานะที่ได้รับ</th>
                            <th style="width: 120px;">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($idstations as $index => $item)
                            @php
                                $days = $item->receive_date
                                    ? \Carbon\Carbon::parse($item->receive_date)
                                        ->startOfDay()
                                        ->diffInDays(now('Asia/Bangkok')->startOfDay())
                                    : 0;

                                $days = (int) floor($days);

                                if ($item->process_status === 'received_status') {
                                    $dayBadge = 'bg-success';
                                    $dayText = 'สำเร็จแล้ว';
                                } elseif ($days >= 180) {
                                    $dayBadge = 'bg-danger';
                                    $dayText = $days . ' วัน';
                                } elseif ($days >= 90) {
                                    $dayBadge = 'bg-warning text-dark';
                                    $dayText = $days . ' วัน';
                                } elseif ($days === 0) {
                                    $dayBadge = 'bg-info text-dark';
                                    $dayText = 'วันนี้';
                                } else {
                                    $dayBadge = 'bg-secondary';
                                    $dayText = $days . ' วัน';
                                }
                            @endphp

                            <tr>
                                <td class="text-center fw-semibold">
                                    {{ $index + 1 }}
                                </td>

                                <td class="text-center">
                                    @if($item->receive_date)
                                        {{ $item->receive_date->locale('th')->translatedFormat('d F') }}
                                        {{ $item->receive_date->year + 543 }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @forelse($item->citizenships as $citizenship)
                                        <span class="badge idstation-soft-badge mb-1">
                                            {{ $citizenship->citizenship_name ?? $citizenship->name ?? '-' }}
                                        </span>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>

                                <td class="text-center">
                                    <span class="badge {{ $dayBadge }} idstation-status-badge">
                                        {{ $dayText }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    @if($item->process_status === 'received_status')
                                        <span class="badge bg-success idstation-status-badge">
                                            ได้รับสถานะ
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark idstation-status-badge">
                                            ระหว่างดำเนินการ
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if($item->received_status_date)
                                        {{ $item->received_status_date->locale('th')->translatedFormat('d F') }}
                                        {{ $item->received_status_date->year + 543 }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @forelse($item->citizens as $citizen)
                                        <span class="badge idstation-soft-badge mb-1">
                                            {{ $citizen->citizen_name ?? $citizen->name ?? '-' }}
                                        </span>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>

                                <td class="text-center">
                                    <div class="idstation-action-group">
                                        <button type="button"
                                                class="btn btn-warning btn-sm idstation-action-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editIdstationModal{{ $item->id }}"
                                                title="แก้ไข">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('idstation.destroy', $item->id) }}"
                                              method="POST"
                                              class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-danger btn-sm idstation-action-btn"
                                                    title="ลบ">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    ยังไม่มีข้อมูลการดำเนินการด้านสถานะบุคคล
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="idstation-table-note">
                <i class="bi bi-info-circle me-1"></i>
                หากใช้งานบนจอขนาดเล็กหรือ iPad สามารถเลื่อนตารางในแนวนอนได้
            </div>
        </div>
    </div>

</div>

{{-- Edit Modals ต้องอยู่นอก table / tbody --}}
@foreach($idstations as $item)
    @include('frontend.client.idstation.partials.edit_modal', [
        'item' => $item,
        'citizenships' => $citizenships,
        'citizens' => $citizens,
    ])
@endforeach

@include('frontend.client.idstation.partials.create_modal')

@endsection

@push('styles')
<style>
/* =========================
   PAGE SCROLL FIX
========================= */

html.idstation-scroll-fix,
body.idstation-scroll-fix{
    height: auto !important;
    min-height: 100% !important;
    overflow-y: auto !important;
}

body.idstation-scroll-fix:not(.modal-open){
    overflow-y: auto !important;
}

body.idstation-scroll-fix:not(.modal-open) .page-wrapper,
body.idstation-scroll-fix:not(.modal-open) .main-wrapper,
body.idstation-scroll-fix:not(.modal-open) .content-wrapper,
body.idstation-scroll-fix:not(.modal-open) .main-content,
body.idstation-scroll-fix:not(.modal-open) .admin-content,
body.idstation-scroll-fix:not(.modal-open) .app-content,
body.idstation-scroll-fix:not(.modal-open) .content,
body.idstation-scroll-fix:not(.modal-open) .content-page{
    height: auto !important;
    min-height: auto !important;
    max-height: none !important;
    overflow-y: visible !important;
}

.idstation-page{
    padding-bottom: 140px;
}

/* =========================
   TOP CARD
========================= */

.idstation-top-card{
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
}

.idstation-top-content{
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 18px 20px;
}

.idstation-top-title{
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.idstation-top-icon{
    width: 44px;
    height: 44px;
    border-radius: 15px;
    background: #eff6ff;
    color: #2563eb;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex: 0 0 auto;
}

.idstation-top-text{
    min-width: 0;
}

.idstation-top-title h5{
    margin: 0;
    color: #0f172a;
    font-size: 18px;
    font-weight: 800;
    line-height: 1.35;
}

.idstation-client-name{
    margin-top: 3px;
    color: #64748b;
    font-size: 13px;
}

.idstation-client-name span{
    color: #0f172a;
    font-weight: 800;
}

.idstation-top-actions{
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    flex-wrap: wrap;
}

.idstation-top-btn{
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border-radius: 9px;
    font-weight: 700;
}

/* =========================
   SUMMARY
========================= */

.idstation-summary-card{
    background: linear-gradient(180deg, #f4fbf2 0%, #ffffff 100%);
    border: 1px solid #e1efd9;
    border-radius: 22px;
    padding: 28px 28px 24px;
    box-shadow: 0 12px 30px rgba(72, 113, 58, .07);
}

.idstation-summary-header{
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 30px;
}

.idstation-summary-kicker{
    color: #6ea35f;
    font-size: 13px;
    font-weight: 800;
    margin-bottom: 4px;
}

.idstation-summary-title{
    color: #263238;
    font-size: 19px;
    font-weight: 900;
    line-height: 1.4;
}

.idstation-summary-year{
    color: #5f9852;
    background: #ffffff;
    border: 1px solid #b9ddb0;
    border-radius: 999px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 800;
    white-space: nowrap;
}

.idstation-progress-wrap{
    position: relative;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
    padding-top: 8px;
}

.idstation-progress-line{
    position: absolute;
    top: 31px;
    left: 12.5%;
    right: 12.5%;
    height: 3px;
    background: #dfe8dc;
    border-radius: 99px;
    z-index: 1;
}

.idstation-step{
    position: relative;
    z-index: 2;
    text-align: center;
    min-width: 0;
}

.idstation-step-circle{
    width: 48px;
    height: 48px;
    margin: 0 auto 10px;
    border-radius: 50%;
    background: #ffffff;
    border: 2px solid #dfe8dc;
    color: #8b9788;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 18px;
    box-shadow: 0 8px 18px rgba(15, 23, 42, .06);
}

.idstation-step-label{
    font-size: 13px;
    color: #667085;
    font-weight: 800;
    line-height: 1.45;
}

.idstation-step.active .idstation-step-circle{
    background: #6fa45f;
    border-color: #6fa45f;
    color: #ffffff;
}

.idstation-step.warning .idstation-step-circle{
    background: #fff7e6;
    border-color: #f3bf5f;
    color: #b7791f;
}

.idstation-step.success .idstation-step-circle{
    background: #edf9ee;
    border-color: #70b86b;
    color: #35843a;
}

.idstation-step.danger .idstation-step-circle{
    background: #fff1f1;
    border-color: #e57373;
    color: #c62828;
}

/* =========================
   ALERT
========================= */

.idstation-alert{
    border-radius: 14px;
}

/* =========================
   TABLE CARD
========================= */

.idstation-table-card{
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
    overflow: hidden;
    margin-bottom: 120px;
}

.idstation-table-header{
    padding: 16px 18px;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}

.idstation-table-header h6{
    margin: 0;
    color: #0f172a;
    font-size: 15px;
    font-weight: 900;
}

.idstation-table-header p{
    margin: 4px 0 0;
    color: #64748b;
    font-size: 13px;
    line-height: 1.5;
}

.idstation-table-body{
    padding: 16px;
}

.idstation-table-scroll{
    width: 100%;
    overflow-x: auto;
    overflow-y: visible;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    background: #ffffff;
    -webkit-overflow-scrolling: touch;
}

.idstation-table-scroll::-webkit-scrollbar{
    height: 8px;
}

.idstation-table-scroll::-webkit-scrollbar-track{
    background: #eef2f7;
    border-radius: 999px;
}

.idstation-table-scroll::-webkit-scrollbar-thumb{
    background: #94a3b8;
    border-radius: 999px;
}

.idstation-table{
    width: 100%;
    min-width: 1080px;
    border-collapse: separate;
    border-spacing: 0;
}

.idstation-table thead th{
    background: #f8fafc;
    color: #0f172a;
    font-size: 13px;
    font-weight: 900;
    text-align: center;
    vertical-align: middle;
    padding: 14px 12px;
    border-bottom: 1px solid #e5e7eb;
    border-right: 1px solid #e5e7eb;
    white-space: nowrap;
}

.idstation-table thead th:last-child{
    border-right: 0;
}

.idstation-table tbody td{
    color: #0f172a;
    font-size: 13px;
    vertical-align: middle;
    padding: 14px 12px;
    border-bottom: 1px solid #eef2f7;
    border-right: 1px solid #eef2f7;
}

.idstation-table tbody td:last-child{
    border-right: 0;
}

.idstation-table tbody tr:last-child td{
    border-bottom: 0;
}

.idstation-table tbody tr:hover{
    background: #f8fbff;
}

.idstation-soft-badge{
    background: #f8fafc;
    color: #334155;
    border: 1px solid #dbe3ef;
    font-weight: 800;
    white-space: normal;
    text-align: left;
    line-height: 1.45;
}

.idstation-status-badge{
    font-size: 12px;
    font-weight: 800;
    border-radius: 999px;
    padding: 6px 10px;
}

.idstation-action-group{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    flex-wrap: nowrap;
}

.idstation-action-btn{
    width: 36px;
    height: 36px;
    border-radius: 11px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 0;
}

.idstation-action-btn i{
    font-size: 14px;
}

.idstation-table-note{
    margin-top: 12px;
    color: #64748b;
    font-size: 13px;
    line-height: 1.5;
}

/* =========================
   MODAL
========================= */

body.modal-open{
    overflow: hidden !important;
}

.modal{
    z-index: 1060;
}

.modal-backdrop{
    z-index: 1050;
}

/* =========================
   NOTEBOOK / IPAD
========================= */

@media (max-width: 1199.98px){
    .idstation-page{
        padding-bottom: 170px;
    }

    .idstation-table-card{
        margin-bottom: 160px;
    }

    .idstation-table{
        min-width: 1040px;
    }
}

@media (max-width: 991.98px){
    .idstation-summary-card{
        padding: 24px 22px;
    }

    .idstation-progress-wrap{
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 26px 18px;
    }

    .idstation-progress-line{
        display: none;
    }

    .idstation-table{
        min-width: 1000px;
    }
}

/* =========================
   MOBILE
========================= */

@media (max-width: 767.98px){
    .idstation-page{
        padding-bottom: 220px;
    }

    .idstation-top-content{
        align-items: flex-start;
        flex-direction: column;
        padding: 16px;
    }

    .idstation-top-title{
        align-items: flex-start;
    }

    .idstation-top-actions{
        width: 100%;
        justify-content: stretch;
    }

    .idstation-top-actions .btn{
        flex: 1 1 0;
        justify-content: center;
    }

    .idstation-summary-card{
        padding: 22px 18px;
        border-radius: 18px;
    }

    .idstation-summary-header{
        align-items: flex-start;
        flex-direction: column;
        margin-bottom: 22px;
    }

    .idstation-summary-year{
        width: 100%;
        text-align: center;
    }

    .idstation-step-circle{
        width: 46px;
        height: 46px;
    }

    .idstation-table-card{
        border-radius: 16px;
        margin-bottom: 220px;
    }

    .idstation-table-header{
        padding: 14px;
    }

    .idstation-table-body{
        padding: 12px;
    }

    .idstation-table{
        min-width: 960px;
    }
}

@media (max-width: 480px){
    .idstation-page{
        padding-bottom: 260px;
    }

    .idstation-top-title h5{
        font-size: 16px;
    }

    .idstation-client-name{
        font-size: 12.5px;
    }

    .idstation-summary-title{
        font-size: 17px;
    }

    .idstation-progress-wrap{
        gap: 20px 12px;
    }

    .idstation-step-circle{
        width: 44px;
        height: 44px;
        font-size: 17px;
    }

    .idstation-step-label{
        font-size: 12.5px;
    }

    .idstation-table{
        min-width: 920px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.documentElement.classList.add('idstation-scroll-fix');
    document.body.classList.add('idstation-scroll-fix');

    function toggleReceivedStatus(modal) {
        const checked = modal.querySelector('input[name="process_status"]:checked');
        const section = modal.querySelector('.received-status-section');

        if (!checked || !section) {
            return;
        }

        section.style.display = checked.value === 'received_status' ? 'block' : 'none';
    }

    document.querySelectorAll('.idstation-modal').forEach(function (modal) {
        toggleReceivedStatus(modal);

        modal.querySelectorAll('input[name="process_status"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                toggleReceivedStatus(modal);
            });
        });
    });

    document.querySelectorAll('.delete-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'ยืนยันการลบข้อมูล?',
                text: 'เมื่อลบแล้วจะไม่สามารถกู้คืนข้อมูลนี้ได้',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ใช่, ลบข้อมูล',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    setTimeout(function () {
        document.querySelectorAll('.auto-dismiss-alert').forEach(function (alertElement) {
            const alert = bootstrap.Alert.getOrCreateInstance(alertElement);
            alert.close();
        });
    }, 3000);
});
</script>
@endpush