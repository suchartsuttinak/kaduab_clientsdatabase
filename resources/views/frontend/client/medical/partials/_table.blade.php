<div class="card border-0 shadow-sm medical-table-card">
    <div class="card-header bg-white border-0 px-3 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h6 class="mb-0 fw-bold">
                <i class="bi bi-clipboard2-pulse me-2 text-primary"></i>รายการข้อมูลการรักษาพยาบาล
            </h6>
        </div>

        <div class="small text-muted">
            จำนวน {{ number_format($medicals->count()) }} รายการ
        </div>
    </div>

    <div class="card-body p-0">
        @php
            $hasDoctorVisit = $medicals->contains(function ($medical) {
                return ($medical->refer ?? '') === 'พบแพทย์';
            });
        @endphp

        <div class="medical-table-wrapper">
            <table id="datatable-medical" class="table table-hover align-middle mb-0 medical-table">
                <thead>
                    <tr>
                        <th class="text-center medical-col-seq">ลำดับ</th>
                        <th class="medical-col-date">วันที่รักษา</th>
                        <th class="medical-col-disease">ชื่อโรค</th>
                        <th class="medical-col-illness">อาการป่วย</th>
                        <th class="medical-col-treatment">การรักษา</th>
                        <th class="medical-col-refer">การพบแพทย์</th>

                        @if($hasDoctorVisit)
                            <th class="medical-col-appt">วันที่แพทย์นัด</th>
                        @endif

                        <th class="text-center medical-col-actions">จัดการ</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($medicals as $index => $medical)
                        @php
                            $refer = $medical->refer ?? '';
                            $isDoctorVisit = $refer === 'พบแพทย์';
                        @endphp

                        <tr>
                            <td class="text-center fw-semibold medical-row-number">
                                {{ $index + 1 }}
                            </td>

                            <td data-order="{{ $medical->medical_date ?: '' }}">
                                {{ $medical->medical_date ? \Carbon\Carbon::parse($medical->medical_date)->format('d/m/Y') : '-' }}
                            </td>

                            <td>
                                <span class="medical-cell-text" title="{{ $medical->disease_name }}">
                                    {{ $medical->disease_name ?: '-' }}
                                </span>
                            </td>

                            <td>
                                <span class="medical-cell-text medical-cell-clamp" title="{{ $medical->illness }}">
                                    {{ $medical->illness ?: '-' }}
                                </span>
                            </td>

                            <td>
                                <span class="medical-cell-text medical-cell-clamp" title="{{ $medical->treatment }}">
                                    {{ $medical->treatment ?: '-' }}
                                </span>
                            </td>

                            <td>
                                @if($refer === 'พบแพทย์')
                                    <span class="badge rounded-pill bg-success-subtle text-success-emphasis px-3 py-2">
                                        พบแพทย์
                                    </span>
                                @elseif($refer === 'ไม่พบแพทย์')
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis px-3 py-2">
                                        ไม่พบแพทย์
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-light text-secondary border px-3 py-2">
                                        ไม่ระบุ
                                    </span>
                                @endif
                            </td>

                            @if($hasDoctorVisit)
                                <td data-order="{{ $isDoctorVisit ? ($medical->appt_date ?: '') : '' }}">
                                    @if($isDoctorVisit && !empty($medical->appt_date))
                                        {{ \Carbon\Carbon::parse($medical->appt_date)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endif

                            <td class="text-center medical-col-actions">
                                <div class="action-group">
                                    <button type="button"
                                            class="btn btn-warning btn-sm action-btn"
                                            onclick="openEditMedical({{ $medical->id }})"
                                            aria-label="แก้ไขข้อมูลวันที่ {{ $medical->medical_date }}">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>แก้ไข</span>
                                    </button>

                                    <button type="button"
                                        class="btn btn-danger btn-sm action-btn"
                                        onclick="confirmDelete('delete-form-medical-{{ $medical->id }}', 'คุณต้องการลบข้อมูลทางการแพทย์นี้ใช่หรือไม่')"
                                        aria-label="ลบข้อมูลวันที่ {{ $medical->medical_date }}">
                                        <i class="bi bi-trash"></i>
                                        <span>ลบ</span>
                                    </button>
                                </div>

                                <form id="delete-form-medical-{{ $medical->id }}"
                                    action="{{ route('medical.delete', $medical->id) }}"
                                    method="POST"
                                    class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
.medical-page .medical-table-card{
    overflow: hidden;
    border: 1px solid var(--medical-border-soft, #eef2f7);
}

.medical-page .medical-table-wrapper{
    width: 100%;
    min-width: 0;
    overflow: hidden;
    border-top: 1px solid #eef2f7;
}

.medical-page .medical-table-wrapper .dataTables_wrapper,
.medical-page .medical-table-wrapper .dataTables_scroll{
    width: 100%;
    min-width: 0;
}

.medical-page .medical-table-wrapper .dataTables_scrollHead{
    overflow: hidden !important;
    background: #f8fafc;
}

.medical-page .medical-table-wrapper .dataTables_scrollBody{
    overflow-x: auto !important;
    overflow-y: visible !important;
    -webkit-overflow-scrolling: touch;
}

.medical-page .medical-table-wrapper .dataTables_scrollBody.medical-scroll-fit{
    overflow-x: hidden !important;
}

.medical-page .medical-table{
    width: 100% !important;
    min-width: 1050px;
    margin: 0 !important;
}

.medical-page .medical-table th,
.medical-page .medical-table td{
    vertical-align: middle;
}

.medical-page .medical-table thead th{
    padding: .78rem .72rem;
    border-bottom: 1px solid #dbe4f0;
    background: #f8fafc;
    color: #334155;
    font-size: .78rem;
    font-weight: 800;
    white-space: nowrap;
}

.medical-page .medical-table tbody td{
    padding: .78rem .72rem;
    border-color: #edf1f6;
    color: #334155;
    font-size: .83rem;
    line-height: 1.55;
}

.medical-page .medical-table tbody tr:hover{
    background: #fbfdff;
}

.medical-page .medical-col-seq{
    width: 70px !important;
    min-width: 70px !important;
}

.medical-page .medical-col-date{
    width: 118px !important;
    min-width: 118px !important;
}

.medical-page .medical-col-disease{
    width: 160px !important;
    min-width: 160px !important;
}

.medical-page .medical-col-illness,
.medical-page .medical-col-treatment{
    width: 210px !important;
    min-width: 210px !important;
}

.medical-page .medical-col-refer{
    width: 130px !important;
    min-width: 130px !important;
}

.medical-page .medical-col-appt{
    width: 135px !important;
    min-width: 135px !important;
}

.medical-page .medical-col-actions{
    position: static !important;
    right: auto !important;
    width: 150px !important;
    min-width: 150px !important;
    max-width: 150px !important;
    box-shadow: none !important;
}

.medical-page .medical-cell-text{
    display: block;
    min-width: 0;
    overflow-wrap: anywhere;
    word-break: break-word;
}

.medical-page .medical-cell-clamp{
    display: -webkit-box;
    overflow: hidden;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

.medical-page .action-group{
    display: grid;
    grid-template-columns: repeat(2, 56px);
    justify-content: center;
    align-items: center;
    gap: .4rem;
    width: 118px;
    margin: 0 auto;
    white-space: nowrap;
}

.medical-page .action-group .action-btn{
    display: inline-flex;
    min-height: 36px;
    align-items: center;
    justify-content: center;
    gap: .32rem;
    border-radius: 10px;
    padding: .42rem .48rem;
    font-size: .76rem;
    line-height: 1;
}

.medical-page .dataTables_wrapper .dataTables_length,
.medical-page .dataTables_wrapper .dataTables_filter{
    margin: .85rem 1rem .6rem;
    color: #475569;
    font-size: .82rem;
}

.medical-page .dataTables_wrapper .dataTables_info,
.medical-page .dataTables_wrapper .dataTables_paginate{
    margin: .75rem 1rem .9rem;
    color: #64748b;
    font-size: .8rem;
}

@media (min-width: 1400px){
    .medical-page .medical-table{
        min-width: 100% !important;
        table-layout: fixed;
    }
}

@media (max-width: 767.98px){
    .medical-page .medical-table-card .card-header{
        align-items: flex-start !important;
    }

    .medical-page .dataTables_wrapper .dataTables_length,
    .medical-page .dataTables_wrapper .dataTables_filter,
    .medical-page .dataTables_wrapper .dataTables_info,
    .medical-page .dataTables_wrapper .dataTables_paginate{
        margin-left: .75rem;
        margin-right: .75rem;
    }
}
</style>
@endpush
