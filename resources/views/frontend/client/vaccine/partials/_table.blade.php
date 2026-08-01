<div class="card border-0 shadow-sm vaccine-table-card vaccine-record-card">
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
            <table id="datatable-vaccine"
                   class="table table-hover align-middle mb-0 vaccine-table vaccine-record-table">
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
                                            onclick="vaccineEdit({{ $item->id }})">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>แก้ไข</span>
                                    </button>

                                    <button type="button"
                                            class="btn btn-danger vaccine-action-btn vaccine-action-btn--delete"
                                            onclick="confirmDelete('delete-form-item-{{ $item->id }}', 'คุณต้องการลบข้อมูลนี้ใช่หรือไม่')">
                                        <i class="bi bi-trash"></i>
                                        <span>ลบ</span>
                                    </button>
                                </div>

                                <form id="delete-form-item-{{ $item->id }}"
                                      action="{{ route('vaccine.delete', $item->id) }}"
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
.vaccine-page .vaccine-record-table-wrapper{
    width:100%;
    overflow-x:auto;
    overflow-y:hidden;
    -webkit-overflow-scrolling:touch;
}

.vaccine-page .vaccine-record-table-wrapper.is-datatable-ready{
    overflow:visible;
}

.vaccine-page .vaccine-record-table{
    width:100% !important;
    min-width:1040px;
    margin:0 !important;
    table-layout:fixed;
}

.vaccine-page .vaccine-record-table .vaccine-col-date{
    width:130px !important;
}

.vaccine-page .vaccine-record-table .vaccine-col-name{
    width:190px !important;
}

.vaccine-page .vaccine-record-table .vaccine-col-hospital{
    width:190px !important;
}

.vaccine-page .vaccine-record-table .vaccine-col-remark{
    width:240px !important;
}

.vaccine-page .vaccine-record-table .vaccine-col-recorder{
    width:150px !important;
}

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
}

.vaccine-page .dataTables_wrapper,
.vaccine-page .dataTables_scroll{
    width:100%;
    min-width:0;
}

.vaccine-page .dataTables_scrollHead{
    overflow:hidden !important;
}

.vaccine-page .dataTables_scrollBody{
    overflow-x:auto !important;
    overflow-y:visible !important;
    -webkit-overflow-scrolling:touch;
}

.vaccine-page .dataTables_scrollBody.vaccine-scroll-fit{
    overflow-x:hidden !important;
}

@media (min-width:1400px){
    .vaccine-page .vaccine-record-table{
        min-width:100% !important;
    }
}
</style>
@endpush
