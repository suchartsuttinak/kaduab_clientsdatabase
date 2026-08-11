<div class="card border-0 shadow-sm vaccine-table-card vaccine-record-card" data-permission-keep>
    <div class="card-header vaccine-record-card__header">
        <div class="vaccine-record-card__title-wrap">
            <div class="vaccine-record-card__icon">
                <i class="bi bi-shield-plus"></i>
            </div>
            <div>
                <h6 class="vaccine-record-card__title mb-0">รายการข้อมูลวัคซีน</h6>
                <div class="vaccine-record-card__subtext">ประวัติการรับวัคซีนของผู้รับบริการ</div>
            </div>
        </div>

        <div class="vaccine-record-card__count">
            จำนวน {{ number_format($vaccinations->count()) }} รายการ
        </div>
    </div>

    <div class="card-body p-0">
        <div class="vaccine-table-wrapper vaccine-record-table-wrapper">
            <x-stable-table-controls target="datatable-vaccine" />
            <table id="datatable-vaccine"
                   class="table table-hover align-middle mb-0 vaccine-table vaccine-record-table" data-stable-table data-page-length="10">
                <thead>
                    <tr>
                        <th class="vaccine-col-date">วันที่รับวัคซีน</th>
                        <th class="vaccine-col-name">ชนิดวัคซีน</th>
                        <th class="vaccine-col-hospital">สถานพยาบาล</th>
                        <th class="vaccine-col-remark">หมายเหตุ</th>
                        <th class="vaccine-col-recorder">ผู้บันทึก</th>
                        <th class="text-center vaccine-col-actions">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vaccinations as $item)
                        <tr>
                            <td data-order="{{ $item->date }}">
                                <span class="vaccine-record-table__date">
                                    {{ \App\Helpers\ThaiDateHelper::formatThaiShort($item->date) }}
                                </span>
                            </td>

                            <td>
                                <div class="vaccine-record-table__primary">
                                    {{ $item->vaccine_name ?: '-' }}
                                </div>
                            </td>

                            <td>{{ $item->hospital ?: '-' }}</td>

                            <td>
                                <div class="vaccine-record-table__remark">
                                    {{ $item->remark ?: '-' }}
                                </div>
                            </td>

                            <td>{{ $item->recorder ?: '-' }}</td>

                            <td class="text-center vaccine-col-actions">
                                <div class="vaccine-action-group">
                                    <button type="button"
                                            class="btn btn-warning vaccine-action-btn vaccine-action-btn--edit"
                                            onclick="vaccineEdit({{ $item->id }})"
                                            data-permission-action="update">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>แก้ไข</span>
                                    </button>

                                    <button type="button"
                                            class="btn btn-danger vaccine-action-btn vaccine-action-btn--delete"
                                            onclick="confirmDelete('delete-form-item-{{ $item->id }}', 'คุณต้องการลบข้อมูลนี้ใช่หรือไม่')"
                                            data-permission-action="delete">
                                        <i class="bi bi-trash"></i>
                                        <span>ลบ</span>
                                    </button>
                                </div>

                                <form id="delete-form-item-{{ $item->id }}"
                                      action="{{ route('vaccine.delete', $item->id) }}"
                                      method="POST"
                                      class="d-none"
                                      data-permission-action="delete">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <x-stable-table-footer target="datatable-vaccine" :total="$vaccinations->count()" />
        </div>
    </div>
</div>

@push('styles')
<style>
/* คงรูปแบบหัวการ์ดและหัวคอลัมน์เดิม แก้เฉพาะโครงสร้าง DataTable/Responsive */
.vaccine-page .vaccine-record-table-wrapper{
    width:100%;
    min-width:0;
    overflow-x:auto;
    overflow-y:hidden;
    -webkit-overflow-scrolling:touch;
}

.vaccine-page .vaccine-record-table{
    width:100% !important;
    min-width:1040px;
    margin:0 !important;
    table-layout:fixed;
}

.vaccine-page .vaccine-record-table .vaccine-col-date{ width:130px !important; }
.vaccine-page .vaccine-record-table .vaccine-col-name{ width:190px !important; }
.vaccine-page .vaccine-record-table .vaccine-col-hospital{ width:190px !important; }
.vaccine-page .vaccine-record-table .vaccine-col-remark{ width:240px !important; }
.vaccine-page .vaccine-record-table .vaccine-col-recorder{ width:150px !important; }

.vaccine-page .vaccine-record-table .vaccine-col-actions{
    position:static !important;
    right:auto !important;
    width:140px !important;
    min-width:140px !important;
    max-width:140px !important;
    box-shadow:none !important;
}

.vaccine-page .vaccine-record-table th,
.vaccine-page .vaccine-record-table td{
    overflow-wrap:anywhere;
    word-break:break-word;
    vertical-align:middle;
}

.vaccine-page .dataTables_wrapper{
    width:100%;
    min-width:0;
}

.vaccine-page .vaccine-dt-top,
.vaccine-page .vaccine-dt-bottom{
    width:100%;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:.75rem;
    flex-wrap:wrap;
}

.vaccine-page .vaccine-dt-top{ margin-bottom:.65rem; }
.vaccine-page .vaccine-dt-bottom{ margin-top:.65rem; }

.vaccine-page .dataTables_length,
.vaccine-page .dataTables_filter,
.vaccine-page .dataTables_info,
.vaccine-page .dataTables_paginate{
    float:none !important;
    margin:0 !important;
    color:#64748b;
    font-size:.8rem;
    text-align:initial !important;
}

.vaccine-page .dataTables_length label,
.vaccine-page .dataTables_filter label{
    margin:0;
    display:inline-flex;
    align-items:center;
    gap:.4rem;
    color:#64748b;
    font-size:.8rem;
    font-weight:400;
    white-space:nowrap;
}

.vaccine-page .dataTables_length select,
.vaccine-page .dataTables_filter input{
    min-height:36px;
    border:1px solid #cbd5e1;
    border-radius:9px;
    background:#fff;
    color:#334155;
    box-shadow:none !important;
    font-size:.82rem;
}

.vaccine-page .dataTables_length select{
    min-width:72px;
    padding:.3rem 1.85rem .3rem .58rem;
}

.vaccine-page .dataTables_filter input{
    width:min(240px, 46vw);
    margin-left:0 !important;
    padding:.38rem .65rem;
}

.vaccine-page .dataTables_length select:focus,
.vaccine-page .dataTables_filter input:focus{
    border-color:#93c5fd;
    outline:0;
    box-shadow:0 0 0 .18rem rgba(37,99,235,.09) !important;
}

.vaccine-page .dataTables_scroll{
    width:100%;
    min-width:0;
}

.vaccine-page .dataTables_scrollHead{
    overflow:hidden !important;
    border-bottom:0 !important;
}

.vaccine-page .dataTables_scrollHeadInner{
    min-width:100% !important;
}

.vaccine-page .dataTables_scrollHead table,
.vaccine-page .dataTables_scrollBody table{
    width:100% !important;
    margin:0 !important;
}

.vaccine-page .dataTables_scrollBody{
    overflow-x:auto !important;
    overflow-y:visible !important;
    border:0 !important;
    -webkit-overflow-scrolling:touch;
}

.vaccine-page .dataTables_paginate .paginate_button{
    min-width:32px;
    min-height:32px;
    margin:0 2px !important;
    padding:.34rem .58rem !important;
    border:1px solid transparent !important;
    border-radius:7px !important;
    background:transparent !important;
    color:#475569 !important;
    box-shadow:none !important;
}

.vaccine-page .dataTables_paginate .paginate_button:hover{
    border-color:#cbd5e1 !important;
    background:#f8fafc !important;
    color:#1e293b !important;
}

.vaccine-page .dataTables_paginate .paginate_button.current,
.vaccine-page .dataTables_paginate .paginate_button.current:hover{
    border-color:#bfdbfe !important;
    background:#eff6ff !important;
    color:#1d4ed8 !important;
}

.vaccine-page .dataTables_paginate .paginate_button.disabled,
.vaccine-page .dataTables_paginate .paginate_button.disabled:hover{
    opacity:.45;
    border-color:transparent !important;
    background:transparent !important;
}

/* ป้องกัน CSS ภายนอกตรึงคอลัมน์จัดการจนหัวกับข้อมูลเหลื่อม */
.vaccine-page .vaccine-record-table th:last-child,
.vaccine-page .vaccine-record-table td:last-child{
    position:static !important;
    right:auto !important;
    box-shadow:none !important;
}

@media (min-width:1400px){
    .vaccine-page .vaccine-record-table{
        min-width:100% !important;
    }
}

@media (max-width:575.98px){
    .vaccine-page .vaccine-dt-top,
    .vaccine-page .vaccine-dt-bottom{
        display:grid;
        grid-template-columns:1fr;
        align-items:stretch;
    }

    .vaccine-page .dataTables_length,
    .vaccine-page .dataTables_filter,
    .vaccine-page .dataTables_info,
    .vaccine-page .dataTables_paginate{
        width:100%;
    }

    .vaccine-page .dataTables_length label,
    .vaccine-page .dataTables_filter label{
        width:100%;
        justify-content:space-between;
    }

    .vaccine-page .dataTables_filter input{
        width:100%;
    }

    .vaccine-page .dataTables_paginate{
        display:flex;
        justify-content:center;
        flex-wrap:wrap;
    }
}
</style>
@endpush
