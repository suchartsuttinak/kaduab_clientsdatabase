<style>
    .school-followup-table-card{
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #e8edf4;
        background: #ffffff;
    }

    .school-followup-table-card .school-followup-card-header{
        background: #ffffff;
        border-bottom: 1px solid #eef2f7;
        padding: 1.2rem 1.25rem .95rem;
    }

    .school-followup-table-card .school-followup-header-row{
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .school-followup-table-card .school-followup-title-wrap{
        min-width: 0;
    }

    .school-followup-table-card .school-followup-section-title{
        font-size: 1.05rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: .2rem;
        line-height: 1.35;
    }

    .school-followup-table-card .school-followup-section-subtitle{
        font-size: .9rem;
        color: #64748b;
        margin-bottom: 0;
        line-height: 1.55;
    }

    .school-followup-table-card .school-followup-badge{
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        background: #f8fafc;
        color: #1f2937;
        border: 1px solid #e5e7eb;
        border-radius: 999px;
        padding: .58rem .9rem;
        font-size: .88rem;
        font-weight: 700;
        white-space: nowrap;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    }

    .school-followup-table-card .school-followup-card-body{
        padding: 1rem 1.25rem 1.25rem;
    }

    .school-followup-table-card .school-followup-table-wrap{
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .school-followup-table-card .school-followup-table{
        margin-bottom: 0;
        min-width: 980px;
    }

    .school-followup-table-card .school-followup-table thead th{
        background: #f8fafc;
        color: #334155;
        font-size: .84rem;
        font-weight: 700;
        border-bottom: 1px solid #e5e7eb;
        border-top: 0;
        padding: .9rem .85rem;
        white-space: nowrap;
        vertical-align: middle;
    }

    .school-followup-table-card .school-followup-table tbody td{
        font-size: .9rem;
        color: #1f2937;
        padding: .9rem .85rem;
        vertical-align: middle;
        border-color: #eef2f7;
    }

    .school-followup-table-card .school-followup-table tbody tr:hover{
        background: #fbfdff;
    }

    .school-followup-table-card .school-followup-date{
        font-weight: 700;
        color: #111827;
        line-height: 1.35;
        white-space: nowrap;
    }

    .school-followup-table-card .school-followup-action-group{
        display: flex;
        flex-wrap: nowrap;
        justify-content: center;
        align-items: center;
        gap: .5rem;
        min-width: 290px;
    }

    .school-followup-table-card .school-followup-action-btn{
        min-height: 34px;
        padding: .42rem .75rem;
        border-radius: 10px;
        font-size: .84rem;
        font-weight: 600;
        line-height: 1.2;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        white-space: nowrap;
        box-shadow: none !important;
    }

    .school-followup-table-card .school-followup-empty{
        border: 1px dashed #d8e1ec;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        text-align: center;
        padding: 2rem 1rem;
    }

    .school-followup-table-card .school-followup-empty-icon{
        width: 62px;
        height: 62px;
        border-radius: 18px;
        margin: 0 auto 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef4ff;
        color: #2563eb;
        font-size: 1.5rem;
    }

    .school-followup-table-card .school-followup-empty-title{
        font-size: 1.02rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: .35rem;
    }

    .school-followup-table-card .school-followup-empty-text{
        color: #64748b;
        font-size: .92rem;
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    .school-followup-table-card .school-followup-empty-btn{
        border-radius: 12px;
        padding: .65rem 1rem;
        font-size: .9rem;
        font-weight: 600;
    }

    @media (max-width: 991.98px){
        .school-followup-table-card .school-followup-card-header{
            padding: 1rem 1rem .85rem;
        }

        .school-followup-table-card .school-followup-card-body{
            padding: .9rem 1rem 1rem;
        }

        .school-followup-table-card .school-followup-header-row{
            align-items: flex-start;
        }

        .school-followup-table-card .school-followup-badge{
            font-size: .84rem;
        }

        .school-followup-table-card .school-followup-table{
            min-width: 920px;
        }
    }

    @media (max-width: 767.98px){
        .school-followup-table-card{
            border-radius: 16px;
        }

        .school-followup-table-card .school-followup-section-title{
            font-size: 1rem;
        }

        .school-followup-table-card .school-followup-section-subtitle{
            font-size: .86rem;
        }

        .school-followup-table-card .school-followup-badge{
            width: 100%;
            justify-content: center;
        }

        .school-followup-table-card .school-followup-table thead th,
        .school-followup-table-card .school-followup-table tbody td{
            font-size: .86rem;
            padding: .8rem .75rem;
        }

        .school-followup-table-card .school-followup-action-group{
            gap: .45rem;
        }

        .school-followup-table-card .school-followup-action-btn{
            font-size: .82rem;
            padding: .4rem .65rem;
        }
    }

    @media (max-width: 575.98px){
        .school-followup-table-card .school-followup-card-header{
            padding: .95rem .9rem .8rem;
        }

        .school-followup-table-card .school-followup-card-body{
            padding: .85rem .9rem .95rem;
        }

        .school-followup-table-card .school-followup-empty{
            padding: 1.5rem .85rem;
        }

        .school-followup-table-card .school-followup-empty-icon{
            width: 56px;
            height: 56px;
            font-size: 1.35rem;
        }
    }
</style>

<div class="card border-0 shadow-sm school-followup-table-card">
    <div class="school-followup-card-header">
        <div class="school-followup-header-row">
            <div class="school-followup-title-wrap">
                <h5 class="school-followup-section-title mb-1">รายการติดตามผลการเรียน</h5>
                <p class="school-followup-section-subtitle">
                    แสดงข้อมูลการติดตามทั้งหมด พร้อมเครื่องมือจัดการแต่ละรายการ
                </p>
            </div>

            <div class="school-followup-badge">
                <i class="bi bi-table"></i>
                <span>{{ $followups->count() }} รายการ</span>
            </div>
        </div>
    </div>

    <div class="school-followup-card-body">
        @if($followups->isNotEmpty())
            <div class="table-responsive school-followup-table-wrap">
                <table id="datatable-followup" class="table align-middle w-100 school-followup-table">
                    <thead>
                        <tr>
                            <th>วันที่ติดตาม</th>
                            <th>สถานศึกษา</th>
                            <th>ระดับชั้น</th>
                            <th>ครูประจำชั้น</th>
                            <th>โทรศัพท์</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($followups as $item)
                            <tr>
                                <td>
                                    <div class="school-followup-date">
                                        {{ Carbon\Carbon::parse($item->follow_date)->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td>{{ optional($item->educationRecord)->school_name ?? 'ไม่พบข้อมูล' }}</td>
                                <td>{{ optional(optional($item->educationRecord)->education)->education_name ?? 'ไม่พบข้อมูล' }}</td>
                                <td>{{ $item->teacher_name ?? '-' }}</td>
                                <td>{{ $item->tel ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="school-followup-action-group">
                                        <button type="button"
                                                class="btn btn-warning btn-sm school-followup-action-btn"
                                                onclick="openEditFollowup({{ $item->id }})">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>แก้ไข</span>
                                        </button>

                                        <button type="button"
                                                class="btn btn-danger btn-sm school-followup-action-btn"
                                                onclick="confirmDelete({{ $item->id }})">
                                            <i class="bi bi-trash"></i>
                                            <span>ลบ</span>
                                        </button>

                                        <a href="{{ route('school_followup.report', $item->id) }}"
                                           class="btn btn-info btn-sm text-white school-followup-action-btn">
                                            <i class="bi bi-file-earmark-text"></i>
                                            <span>รายงาน</span>
                                        </a>
                                    </div>

                                    <form id="delete-form-{{ $item->id }}"
                                          action="{{ route('school_followup.delete', $item->id) }}"
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
        @else
            <div class="school-followup-empty">
                <div class="school-followup-empty-icon">
                    <i class="bi bi-journal-x"></i>
                </div>
                <h5 class="school-followup-empty-title">ยังไม่มีข้อมูลการติดตามผลการเรียน</h5>
                <p class="school-followup-empty-text">
                    เริ่มต้นเพิ่มข้อมูลการติดตามใหม่เพื่อให้ระบบสามารถจัดเก็บและออกรายงานได้
                </p>
                <button type="button"
                        class="btn btn-primary school-followup-empty-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#followupModal">
                    <i class="bi bi-plus-circle-fill me-2"></i>เพิ่มการติดตามใหม่
                </button>
            </div>
        @endif
    </div>
</div>