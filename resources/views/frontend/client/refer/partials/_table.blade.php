@php
    $canApproveRefer = auth()->check() && in_array(auth()->user()->role, ['admin', 'executive'], true);
    $hasReferRows = isset($refers) && $refers->isNotEmpty();
    $canCreateRefer = $canCreateRefer ?? !in_array($client->release_status, ['pending_refer', 'refer'], true);
@endphp

<style>
.rf-table-card{
    margin-top:16px;
    border:1px solid #e7edf5;
    border-radius:18px;
    background:#fff;
    box-shadow:0 10px 30px rgba(15,23,42,.05);
    overflow:hidden;
}

.rf-table-head{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
    padding:16px 18px;
    border-bottom:1px solid #eef2f7;
    background:#f8fafc;
}

.rf-table-title{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:16px;
    font-weight:800;
    color:#0f172a;
}

.rf-table-title i{
    width:38px;
    height:38px;
    border-radius:12px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    background:#eef2ff;
    color:#4f46e5;
}

.rf-table-meta{
    padding:7px 12px;
    border-radius:999px;
    background:#fff;
    border:1px solid #e2e8f0;
    color:#475569;
    font-size:13px;
    font-weight:700;
}

.rf-table-wrap{padding:16px;overflow-x:auto;}
.rf-table{min-width:1500px;margin-bottom:0;}

.rf-table thead th{
    white-space:nowrap;
    font-size:13px;
    color:#334155;
    background:#f8fafc;
    border-bottom:1px solid #e2e8f0;
    vertical-align:middle;
}

.rf-table tbody td{font-size:13px;color:#334155;vertical-align:middle;}
.rf-cell{min-width:130px;max-width:180px;white-space:normal;line-height:1.6;}
.rf-cell-sm{min-width:90px;max-width:130px;white-space:normal;line-height:1.6;}
.rf-cell-lg{min-width:180px;max-width:260px;white-space:normal;line-height:1.6;}

.rf-btn-sm{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    min-height:34px;
    padding:7px 10px;
    border-radius:11px;
    font-size:12px;
    font-weight:700;
    white-space:nowrap;
}

.rf-actions{min-width:170px;}

.rf-empty-card{
    min-height:320px;
    margin-top:16px;
    padding:2.5rem 1.25rem;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    text-align:center;
    border:1px solid #dbe3ef;
    border-radius:18px;
    background:#fff;
    box-shadow:0 10px 30px rgba(15,23,42,.04);
}

.rf-empty-icon{
    width:82px;
    height:82px;
    margin-bottom:1rem;
    border:1px solid #c7d2fe;
    border-radius:50%;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    background:#eef2ff;
    color:#4f46e5;
    font-size:1.7rem;
}

.rf-empty-title{
    margin:0;
    color:#0f172a;
    font-size:1.15rem;
    font-weight:800;
    line-height:1.45;
}

.rf-empty-description{
    max-width:720px;
    margin:.55rem auto 1.2rem;
    color:#64748b;
    font-size:.92rem;
    line-height:1.65;
}

.rf-empty-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    min-height:44px;
    padding:.65rem 1.15rem;
    border:0;
    border-radius:12px;
    color:#fff;
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    box-shadow:0 7px 16px rgba(37,99,235,.22);
    font-weight:700;
}

.rf-empty-btn:hover,
.rf-empty-btn:focus{
    color:#fff;
    transform:translateY(-1px);
    box-shadow:0 10px 20px rgba(37,99,235,.28);
}

.rf-empty-status{
    display:inline-flex;
    align-items:center;
    gap:7px;
    min-height:42px;
    padding:9px 14px;
    border:1px solid #fde68a;
    border-radius:12px;
    background:#fffbeb;
    color:#92400e;
    font-weight:700;
}

@media (max-width:575.98px){
    .rf-table-head{padding:14px;}
    .rf-table-wrap{padding:12px;}
    .rf-empty-card{min-height:280px;padding:1.75rem .9rem;}
    .rf-empty-icon{width:72px;height:72px;}
    .rf-empty-title{font-size:1rem;}
    .rf-empty-description{font-size:.84rem;}
    .rf-empty-btn{width:100%;}
}
</style>

@if($hasReferRows)
    <div class="rf-table-card">
        <div class="rf-table-head">
            <div class="rf-table-title">
                <i class="bi bi-table"></i>
                <span>รายการจำหน่าย</span>
            </div>

            <div class="rf-table-meta">
                จำนวน {{ $refers->count() }} รายการ
            </div>
        </div>

        <div class="rf-table-wrap">
            <table id="datatable-refer" class="table table-hover align-middle rf-table">
                <thead>
                    <tr>
                        <th>วันที่นำส่ง</th>
                        <th>ชื่อผู้รับ</th>
                        <th>สาเหตุ</th>
                        <th>สถานที่นำส่ง</th>
                        <th>ผู้ดูแล</th>
                        <th>ผู้รับตัว</th>
                        <th>โทรศัพท์</th>
                        <th>ความสัมพันธ์</th>
                        <th>ผู้นำส่ง</th>
                        <th>ผลคณะกรรมการฯ</th>
                        <th>รายงานประชุม</th>
                        <th>หมายเหตุ</th>
                        <th>สถานะ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($refers as $refer)
                        @php
                            $approveStatus = $refer->approve_status ?? 'pending';
                            $committeeResult = $refer->committee_result ?? 'ไม่ผ่าน';
                        @endphp

                        <tr>
                            <td class="text-center">
                                {{ $refer->refer_date
                                    ? \Carbon\Carbon::parse($refer->refer_date)->addYears(543)->format('d/m/Y')
                                    : '-' }}
                            </td>

                            <td><div class="rf-cell">{{ $refer->client->fullname ?? $client->fullname ?? '-' }}</div></td>
                            <td><div class="rf-cell">{{ $refer->translate->translate_name ?? '-' }}</div></td>
                            <td><div class="rf-cell-lg">{{ $refer->destination ?: '-' }}</div></td>
                            <td class="text-center"><div class="rf-cell-sm">{{ $refer->guardian ?: '-' }}</div></td>
                            <td><div class="rf-cell">{{ $refer->parent_name ?: '-' }}</div></td>
                            <td><div class="rf-cell">{{ $refer->parent_tel ?: '-' }}</div></td>
                            <td><div class="rf-cell">{{ $refer->member ?: '-' }}</div></td>
                            <td><div class="rf-cell">{{ $refer->teacher ?: '-' }}</div></td>

                            <td class="text-center">
                                @if($committeeResult === 'ผ่าน')
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                        <i class="bi bi-check-circle me-1"></i> ผ่าน
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2">
                                        <i class="bi bi-x-circle me-1"></i> ไม่ผ่าน
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if(!empty($refer->meeting_report_file))
                                    <a href="{{ route('refers.meeting_report.view', $refer->id) }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="btn btn-outline-primary rf-btn-sm">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        <span>เปิดไฟล์</span>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td><div class="rf-cell-lg">{{ $refer->remark ?: '-' }}</div></td>

                            <td class="text-center">
                                @if($approveStatus === 'pending')
                                    <span class="badge rounded-pill bg-warning text-dark px-3 py-2">
                                        <i class="bi bi-hourglass-split me-1"></i> รออนุมัติ
                                    </span>
                                @elseif($approveStatus === 'approved')
                                    <span class="badge rounded-pill bg-success px-3 py-2">
                                        <i class="bi bi-check-circle me-1"></i> อนุมัติแล้ว
                                    </span>
                                @elseif($approveStatus === 'cancelled')
                                    <span class="badge rounded-pill bg-secondary px-3 py-2">
                                        <i class="bi bi-x-circle me-1"></i> ยกเลิกแล้ว
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                                        <i class="bi bi-question-circle me-1"></i> ไม่ระบุ
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="rf-actions d-flex flex-wrap justify-content-center gap-2">
                                    @if($approveStatus === 'pending')
                                        @if($canApproveRefer)
                                            <form action="{{ route('refers.approve', $refer->id) }}"
                                                  method="POST"
                                                  class="d-inline js-refer-confirm-form"
                                                  data-confirm-title="ยืนยันการอนุมัติ"
                                                  data-confirm-text="คุณต้องการอนุมัติการจำหน่ายรายการนี้ใช่หรือไม่"
                                                  data-confirm-button="อนุมัติรายการ">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-success rf-btn-sm">
                                                    <i class="bi bi-check-circle"></i>
                                                    <span>อนุมัติ</span>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-warning rf-btn-sm" disabled>
                                                <i class="bi bi-hourglass-split"></i>
                                                <span>รออนุมัติ</span>
                                            </button>
                                        @endif
                                    @endif

                                    @if($approveStatus === 'approved')
                                        @if($canApproveRefer)
                                            <form action="{{ route('refers.restore', $refer->id) }}"
                                                  method="POST"
                                                  class="d-inline js-refer-confirm-form"
                                                  data-confirm-title="ยืนยันการคืนสถานะ"
                                                  data-confirm-text="คุณต้องการยกเลิกรายการจำหน่ายและคืนสถานะผู้รับบริการใช่หรือไม่"
                                                  data-confirm-button="คืนสถานะ">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-outline-success rf-btn-sm">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                    <span>คืนสถานะ</span>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-outline-secondary rf-btn-sm" disabled>
                                                <i class="bi bi-lock"></i>
                                                <span>ไม่มีสิทธิ์คืนสถานะ</span>
                                            </button>
                                        @endif
                                    @endif

                                    @if($approveStatus === 'cancelled')
                                        <button type="button" class="btn btn-secondary rf-btn-sm" disabled>
                                            <i class="bi bi-slash-circle"></i>
                                            <span>ยกเลิกแล้ว</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="rf-empty-card" role="status">
        <div class="rf-empty-icon" aria-hidden="true">
            <i class="bi bi-box-arrow-right"></i>
        </div>

        <h2 class="rf-empty-title">ยังไม่มีข้อมูลการจำหน่ายผู้รับบริการ</h2>

        <p class="rf-empty-description">
            เริ่มต้นบันทึกวันที่นำส่ง สาเหตุการจำหน่าย สถานที่นำส่ง ผู้ดูแล
            ผลคณะกรรมการ และข้อมูลผู้นำส่งของผู้รับบริการรายนี้
        </p>

        @if($canCreateRefer)
            <button type="button"
                    class="rf-empty-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#createReferModal">
                <i class="bi bi-plus-circle"></i>
                <span>เพิ่มข้อมูลจำหน่ายครั้งแรก</span>
            </button>
        @else
            <div class="rf-empty-status">
                <i class="bi bi-hourglass-split"></i>
                <span>ผู้รับบริการมีรายการจำหน่ายที่กำลังดำเนินการอยู่</span>
            </div>
        @endif
    </div>
@endif
