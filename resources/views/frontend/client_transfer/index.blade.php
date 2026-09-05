@extends('admin.admin_master')

@section('admin')
@php
    $canApproveTransfer = (bool) (auth()->user()?->canUpdateForm('registration_project_transfer') ?? false);
@endphp
<div class="container-fluid py-4">

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 fw-bold">รายการย้ายเคส</h4>
                <div class="text-muted small">แสดงประวัติการย้ายเคสระหว่างโปรเจ็ค</div>
            </div>
        </div>

        <div class="card-body">
            @if($transfers->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>เด็ก/เยาวชน</th>
                                <th>ทะเบียน</th>
                                <th>จากโปรเจ็ค</th>
                                <th>ไปโปรเจ็ค</th>
                                <th>สถานะ</th>
                                <th>วันที่ย้าย</th>
                                <th>ผู้ขอ</th>
                                <th>ผู้อนุมัติ</th>
                                @if($canApproveTransfer)<th class="text-center">จัดการ</th>@endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transfers as $transfer)
                                <tr>
                                    <td>
                                        {{ optional($transfer->client)->first_name }}
                                        {{ optional($transfer->client)->last_name }}
                                    </td>
                                    <td>{{ optional($transfer->client)->register_number ?? '-' }}</td>
                                    <td>{{ optional($transfer->fromProject)->project_name ?? '-' }}</td>
                                    <td>{{ optional($transfer->toProject)->project_name ?? '-' }}</td>
                                    <td>
                                        @if($transfer->status === 'approved')
                                            <span class="badge bg-success">อนุมัติแล้ว</span>
                                        @elseif($transfer->status === 'rejected')
                                            <span class="badge bg-danger">ไม่อนุมัติ</span>
                                        @elseif($transfer->status === 'cancelled')
                                            <span class="badge bg-secondary">ยกเลิก</span>
                                        @else
                                            <span class="badge bg-warning text-dark">รออนุมัติ</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $transfer->transfer_date ? $transfer->transfer_date->format('d/m/Y') : '-' }}
                                    </td>
                                    <td>{{ optional($transfer->requestedBy)->name ?? '-' }}</td>
                                    <td>{{ optional($transfer->approvedBy)->name ?? '-' }}</td>
                                    @if($canApproveTransfer)
                                        <td class="text-center text-nowrap">
                                            @if($transfer->status === 'pending')
                                                <form method="POST" action="{{ route('client.transfer.approve', $transfer->id) }}" class="d-inline" onsubmit="return confirm('ยืนยันอนุมัติการย้ายผู้รับบริการไปยังหน่วยงาน/โครงการปลายทาง?')">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-circle me-1"></i>อนุมัติ
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('client.transfer.reject', $transfer->id) }}" class="d-inline ms-1" onsubmit="return confirm('ยืนยันไม่อนุมัติคำขอย้ายรายการนี้?')">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-x-circle me-1"></i>ไม่อนุมัติ
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $transfers->links() }}
                </div>
            @else
                <div class="alert alert-info mb-0">
                    ยังไม่มีรายการย้ายเคส
                </div>
            @endif
        </div>
    </div>

</div>
@endsection