@extends('admin_client.admin_client')

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/assets/css/idstation-responsive-fix.css') }}?v=20260802">
@endpush

@section('content')
@php
    $processingStatuses = ['processing', 'in_progress'];
    $hasIdstationData = $idstations->isNotEmpty();
    $totalCount = $idstations->count();
    $processingCount = $idstations->whereIn('process_status', $processingStatuses)->count();
    $receivedCount = $idstations->where('process_status', 'received_status')->count();
    $today = now('Asia/Bangkok')->startOfDay();

    $over90Count = $idstations->filter(function ($item) use ($processingStatuses, $today) {
        if (!$item->receive_date || !in_array($item->process_status, $processingStatuses, true)) {
            return false;
        }

        return (int) \Carbon\Carbon::parse($item->receive_date)
            ->startOfDay()
            ->diffInDays($today) >= 90;
    })->count();

    $over180Count = $idstations->filter(function ($item) use ($processingStatuses, $today) {
        if (!$item->receive_date || !in_array($item->process_status, $processingStatuses, true)) {
            return false;
        }

        return (int) \Carbon\Carbon::parse($item->receive_date)
            ->startOfDay()
            ->diffInDays($today) >= 180;
    })->count();

    $clientDisplayName = filled($client->fullname ?? null)
        ? $client->fullname
        : trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
@endphp

<div class="idstation-page">
    <section class="idstation-top-card mb-4" aria-labelledby="idstationPageTitle">
        <div class="idstation-top-content">
            <div class="idstation-top-title">
                <div class="idstation-top-icon" aria-hidden="true">
                    <i class="bi bi-person-vcard"></i>
                </div>

                <div class="idstation-top-text">
                    <h5 id="idstationPageTitle">การดำเนินการด้านสถานะบุคคล</h5>
                    <div class="idstation-client-name">
                        ผู้รับบริการ: <span>{{ $clientDisplayName ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="idstation-top-actions">
                <a href="{{ route('client.show', $client->id) }}"
                   class="btn btn-outline-secondary btn-sm idstation-top-btn">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>กลับ</span>
                </a>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show auto-dismiss-alert idstation-alert" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="ปิดข้อความ"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show idstation-alert" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="ปิดข้อความ"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger idstation-alert" role="alert">
            <div class="fw-bold mb-1">
                <i class="bi bi-exclamation-triangle me-1"></i>
                กรุณาตรวจสอบข้อมูล
            </div>
            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($hasIdstationData)
        <section class="idstation-summary-card mb-4" aria-labelledby="idstationSummaryTitle">
            <div class="idstation-summary-header">
                <div>
                    <div class="idstation-summary-kicker">สรุปการดำเนินการ</div>
                    <h5 class="idstation-summary-title mb-0" id="idstationSummaryTitle">
                        กระบวนการช่วยเหลือด้านสถานะบุคคล
                    </h5>
                </div>

                <div class="idstation-summary-year">
                    ปีปัจจุบัน {{ now('Asia/Bangkok')->year + 543 }}
                </div>
            </div>

            <div class="idstation-progress-wrap">
                <div class="idstation-progress-line" aria-hidden="true"></div>

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
        </section>

        @if($over180Count > 0)
            <div class="alert alert-danger idstation-alert" role="alert">
                <i class="bi bi-exclamation-octagon me-1"></i>
                พบรายการที่อยู่ระหว่างดำเนินการเกิน 180 วัน จำนวน {{ $over180Count }} รายการ
            </div>
        @elseif($over90Count > 0)
            <div class="alert alert-warning idstation-alert" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i>
                พบรายการที่อยู่ระหว่างดำเนินการเกิน 90 วัน จำนวน {{ $over90Count }} รายการ
            </div>
        @endif

        <section class="idstation-table-card" aria-labelledby="idstationTableTitle">
            <div class="idstation-table-header">
                <div>
                    <h6 id="idstationTableTitle">รายการดำเนินการด้านสถานะบุคคล</h6>
                    <p>เลื่อนตารางซ้าย–ขวาได้ และจัดการข้อมูลจากคอลัมน์ด้านขวา</p>
                </div>
            </div>

            <div class="idstation-table-body">
                <div class="idstation-table-scroll" tabindex="0" aria-label="ตารางข้อมูลสถานะบุคคลแบบเลื่อนแนวนอน">
                    <table class="table table-hover align-middle mb-0 idstation-table">
                        <thead>
                            <tr>
                                <th style="width:64px">ลำดับ</th>
                                <th style="width:135px">วันที่รับเรื่อง</th>
                                <th style="min-width:230px">รายการทางทะเบียน</th>
                                <th style="width:145px">ระยะเวลาดำเนินการ</th>
                                <th style="width:165px">การดำเนินการ</th>
                                <th style="width:145px">วันที่ได้รับสถานะ</th>
                                <th style="min-width:230px">สถานะที่ได้รับ</th>
                                <th style="width:120px">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($idstations as $index => $item)
                                @php
                                    $days = $item->receive_date
                                        ? (int) \Carbon\Carbon::parse($item->receive_date)
                                            ->startOfDay()
                                            ->diffInDays($today)
                                        : 0;

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
                                    <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                    <td class="text-center text-nowrap">
                                        @if($item->receive_date)
                                            {{ \Carbon\Carbon::parse($item->receive_date)->locale('th')->translatedFormat('d F') }}
                                            {{ \Carbon\Carbon::parse($item->receive_date)->year + 543 }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="idstation-badge-list">
                                            @forelse($item->citizenships as $citizenship)
                                                <span class="badge idstation-soft-badge">
                                                    {{ $citizenship->citizenship_name ?? $citizenship->name ?? '-' }}
                                                </span>
                                            @empty
                                                <span class="text-muted">-</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $dayBadge }} idstation-status-badge">{{ $dayText }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->process_status === 'received_status')
                                            <span class="badge bg-success idstation-status-badge">ได้รับสถานะ</span>
                                        @else
                                            <span class="badge bg-warning text-dark idstation-status-badge">ระหว่างดำเนินการ</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-nowrap">
                                        @if($item->received_status_date)
                                            {{ \Carbon\Carbon::parse($item->received_status_date)->locale('th')->translatedFormat('d F') }}
                                            {{ \Carbon\Carbon::parse($item->received_status_date)->year + 543 }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="idstation-badge-list">
                                            @forelse($item->citizens as $citizen)
                                                <span class="badge idstation-soft-badge">
                                                    {{ $citizen->citizen_name ?? $citizen->name ?? '-' }}
                                                </span>
                                            @empty
                                                <span class="text-muted">-</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="idstation-action-group">
                                            <button type="button"
                                                    class="btn btn-warning btn-sm idstation-action-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editIdstationModal{{ $item->id }}"
                                                    title="แก้ไข"
                                                    aria-label="แก้ไขข้อมูล">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <form action="{{ route('idstation.destroy', $item->id) }}"
                                                  method="POST"
                                                  class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-danger btn-sm idstation-action-btn"
                                                        title="ลบ"
                                                        aria-label="ลบข้อมูล">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="idstation-table-note">
                    <i class="bi bi-info-circle me-1"></i>
                    บนจอขนาดเล็กหรือ iPad สามารถเลื่อนตารางในแนวนอนได้
                </div>
            </div>
        </section>
    @else
        <section class="idstation-empty-card" aria-labelledby="idstationEmptyTitle">
            <div class="idstation-empty-content">
                <div class="idstation-empty-icon" aria-hidden="true">
                    <i class="bi bi-person-vcard"></i>
                </div>

                <h5 class="idstation-empty-title" id="idstationEmptyTitle">
                    ยังไม่มีข้อมูลด้านสถานะบุคคล
                </h5>

                <p class="idstation-empty-description">
                    เริ่มต้นบันทึกวันที่รับเรื่อง รายการทางทะเบียน และรายละเอียดการช่วยเหลือของผู้รับบริการรายนี้
                </p>

                <button type="button"
                        class="btn idstation-empty-add-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#createIdstationModal">
                    <i class="bi bi-plus-circle"></i>
                    <span>เพิ่มข้อมูลด้านสถานะบุคคลครั้งแรก</span>
                </button>
            </div>
        </section>
    @endif
</div>

@foreach($idstations as $item)
    @include('frontend.client.idstation.partials.edit_modal', [
        'item' => $item,
        'citizenships' => $citizenships,
        'citizens' => $citizens,
    ])
@endforeach

@unless($hasIdstationData)
    @include('frontend.client.idstation.partials.create_modal')
@endunless
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const today = @json(now('Asia/Bangkok')->toDateString());
    const formContext = @json(old('_form_context'));
    const hasErrors = @json($errors->any());

    document.querySelectorAll('.idstation-modal').forEach(function (modal) {
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });

    function setReceivedSectionState(modal) {
        const selected = modal.querySelector('input[name="process_status"]:checked');
        const section = modal.querySelector('.received-status-section');

        if (!selected || !section) {
            return;
        }

        const isReceived = selected.value === 'received_status';
        section.hidden = !isReceived;

        section.querySelectorAll('input, textarea, select').forEach(function (field) {
            field.disabled = !isReceived;
        });

        const receivedDate = section.querySelector('input[name="received_status_date"]');
        if (receivedDate) {
            receivedDate.required = isReceived;
        }

        section.querySelectorAll('input[name="citizen_ids[]"]').forEach(function (checkbox) {
            checkbox.required = false;
        });
    }

    function syncReceivedDateMin(modal) {
        const receiveDate = modal.querySelector('input[name="receive_date"]');
        const receivedDate = modal.querySelector('input[name="received_status_date"]');

        if (!receiveDate || !receivedDate) {
            return;
        }

        receivedDate.min = receiveDate.value || '';
        receivedDate.max = today;

        if (receivedDate.value && receiveDate.value && receivedDate.value < receiveDate.value) {
            receivedDate.value = '';
        }
    }

    document.querySelectorAll('.idstation-modal').forEach(function (modal) {
        setReceivedSectionState(modal);
        syncReceivedDateMin(modal);

        modal.querySelectorAll('input[name="process_status"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                setReceivedSectionState(modal);
                syncReceivedDateMin(modal);
            });
        });

        const receiveDate = modal.querySelector('input[name="receive_date"]');
        if (receiveDate) {
            receiveDate.addEventListener('change', function () {
                syncReceivedDateMin(modal);
            });
        }

        const form = modal.querySelector('form');
        if (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    form.classList.add('was-validated');
                    return;
                }

                const submitButton = form.querySelector('button[type="submit"]');
                if (!submitButton || submitButton.disabled) {
                    return;
                }

                submitButton.disabled = true;
                submitButton.dataset.originalHtml = submitButton.innerHTML;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>กำลังบันทึก...';
            });
        }
    });

    document.querySelectorAll('.delete-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const submitDelete = function () {
                const button = form.querySelector('button[type="submit"]');
                if (button) {
                    button.disabled = true;
                    button.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>';
                }
                form.submit();
            };

            if (typeof Swal === 'undefined') {
                if (window.confirm('ยืนยันการลบข้อมูลนี้หรือไม่?')) {
                    submitDelete();
                }
                return;
            }

            Swal.fire({
                title: 'ยืนยันการลบข้อมูล?',
                text: 'เมื่อลบแล้วจะไม่สามารถกู้คืนข้อมูลนี้ได้',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ใช่, ลบข้อมูล',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#dc3545',
                reverseButtons: true,
                focusCancel: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    submitDelete();
                }
            });
        });
    });

    if (hasErrors && typeof bootstrap !== 'undefined') {
        let targetId = null;

        if (formContext === 'create') {
            targetId = 'createIdstationModal';
        } else if (typeof formContext === 'string' && formContext.startsWith('edit-')) {
            targetId = 'editIdstationModal' + formContext.replace('edit-', '');
        }

        const targetModal = targetId ? document.getElementById(targetId) : null;
        if (targetModal) {
            bootstrap.Modal.getOrCreateInstance(targetModal).show();
        }
    }

    window.setTimeout(function () {
        if (typeof bootstrap === 'undefined') {
            return;
        }

        document.querySelectorAll('.auto-dismiss-alert').forEach(function (element) {
            bootstrap.Alert.getOrCreateInstance(element).close();
        });
    }, 3500);
});
</script>
@endpush
