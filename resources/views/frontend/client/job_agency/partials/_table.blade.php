<style>
.ja-table-card {
    overflow: hidden;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, .045);
}

.ja-table-head {
    padding: 1rem 1.1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    flex-wrap: wrap;
    border-bottom: 1px solid #eef2f7;
    background: #f8fafc;
}

.ja-table-title {
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    color: #0f172a;
    font-weight: 800;
}

.ja-table-title i {
    color: #2563eb;
}

.ja-table-meta {
    color: #64748b;
    font-size: .9rem;
    font-weight: 700;
}

.ja-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.ja-table {
    min-width: 940px;
    margin-bottom: 0;
}

.ja-table thead th {
    padding: .9rem .8rem;
    color: #334155;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    font-size: .88rem;
    font-weight: 800;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
}

.ja-table tbody td {
    padding: .9rem .8rem;
    color: #334155;
    border-color: #eef2f7;
    font-size: .9rem;
    vertical-align: middle;
}

.ja-table tbody tr:hover {
    background: #fbfdff;
}

.ja-cell {
    min-width: 120px;
    word-break: break-word;
}

.ja-income-badge {
    min-width: 96px;
    padding: .42rem .72rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    color: #1d4ed8;
    background: #eff6ff;
    font-size: .88rem;
    font-weight: 800;
}

.ja-actions {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    flex-wrap: nowrap;
    white-space: nowrap;
}

.ja-actions form {
    margin: 0;
}

.ja-btn-sm {
    min-height: 36px;
    padding: .48rem .72rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    border-radius: 10px;
    font-size: .82rem;
    font-weight: 700;
}

.ja-filter-empty {
    min-height: 230px;
    padding: 2rem 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: #64748b;
}

.ja-filter-empty-icon {
    width: 64px;
    height: 64px;
    margin-bottom: .8rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: #2563eb;
    background: #eff6ff;
}

.ja-filter-empty-title {
    margin-bottom: .35rem;
    color: #0f172a;
    font-weight: 800;
}

@media (max-width: 575.98px) {
    .ja-table-head {
        padding: .9rem;
    }

    .ja-btn-sm {
        min-width: 70px;
        padding: .44rem .56rem;
        font-size: .78rem;
    }
}
</style>

<div class="ja-table-card">
    <div class="ja-table-head">
        <div class="ja-table-title">
            <i class="bi bi-table"></i>
            <span>รายการจัดหางาน</span>
        </div>

        <div class="ja-table-meta">
            @if($jobAgencies->isNotEmpty())
                จำนวน {{ number_format($jobAgencies->count()) }} รายการ
            @else
                ไม่พบข้อมูลตามเงื่อนไข
            @endif
        </div>
    </div>

    @if($jobAgencies->isNotEmpty())
        <div class="ja-table-wrap">
            <table id="datatable-jobagency" class="table table-hover align-middle ja-table">
                <thead>
                    <tr>
                        <th style="width: 125px;">วันที่เริ่มงาน</th>
                        <th>อาชีพ</th>
                        <th>ตำแหน่ง</th>
                        <th style="width: 130px;">รายได้/เดือน</th>
                        <th>บริษัท/หน่วยงาน</th>
                        <th>ผู้ประสานงาน</th>
                        <th style="width: 180px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jobAgencies as $job)
                        @php
                            $jobDate = $job->job_date ? \Carbon\Carbon::parse($job->job_date) : null;
                        @endphp
                        <tr>
                            <td class="text-center fw-semibold">
                                {{ $jobDate ? $jobDate->format('d/m/') . ($jobDate->year + 543) : '-' }}
                            </td>

                            <td>
                                <div class="ja-cell">{{ $job->occupation->occupation_name ?? '-' }}</div>
                            </td>

                            <td>
                                <div class="ja-cell">{{ $job->position ?: '-' }}</div>
                            </td>

                            <td class="text-center">
                                <span class="ja-income-badge">
                                    {{ number_format((float) ($job->income ?? 0), 2) }}
                                </span>
                            </td>

                            <td>
                                <div class="ja-cell">{{ $job->company ?: '-' }}</div>
                            </td>

                            <td>
                                <div class="ja-cell">{{ $job->coordinator ?: '-' }}</div>
                            </td>

                            <td class="text-center">
                                <div class="ja-actions">
                                    <button type="button"
                                            class="btn btn-outline-warning ja-btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editJobAgencyModal{{ $job->id }}"
                                            aria-label="แก้ไขข้อมูลการจัดหางาน">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>แก้ไข</span>
                                    </button>

                                    <form id="delete-form-job-{{ $job->id }}"
                                          action="{{ route('job_agencies.delete', $job->id) }}"
                                          method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="button"
                                                class="btn btn-outline-danger ja-btn-sm"
                                                data-delete-job-agency="delete-form-job-{{ $job->id }}"
                                                aria-label="ลบข้อมูลการจัดหางาน">
                                            <i class="bi bi-trash"></i>
                                            <span>ลบ</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="ja-filter-empty" role="status">
            <div class="ja-filter-empty-icon" aria-hidden="true">
                <i class="bi bi-search"></i>
            </div>
            <div class="ja-filter-empty-title">ไม่พบข้อมูลตามช่วงวันที่ที่เลือก</div>
            <div>ลองเปลี่ยนช่วงวันที่ หรือกดปุ่มรีเซ็ตเพื่อแสดงข้อมูลทั้งหมด</div>
        </div>
    @endif
</div>
