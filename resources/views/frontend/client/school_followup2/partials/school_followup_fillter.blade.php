@php
    $hasFollowups = isset($followups) && $followups->count() > 0;
@endphp

@if($hasFollowups)

<style>
    .sf-filter-card{
        border-radius:16px;
        border:1px solid #eef2f7;
        background:#fff;
    }

    .sf-filter-label{
        font-size:.85rem;
        font-weight:600;
        color:#64748b;
        margin-bottom:4px;
    }

    .sf-filter-control{
        border-radius:10px;
        font-size:.9rem;
        padding:6px 10px;
    }

    .sf-filter-actions{
        display:flex;
        flex-wrap:wrap;
        gap:8px;
        justify-content:flex-end;
    }

    .sf-filter-actions .btn{
        border-radius:10px;
        font-size:.85rem;
        padding:6px 12px;
        display:flex;
        align-items:center;
        gap:4px;
    }

    @media(max-width:768px){
        .sf-filter-actions{
            justify-content:flex-start;
        }
    }
</style>

<div class="sf-filter-card card shadow-sm mb-3">
    <div class="card-body p-3">

        <form method="GET" action="{{ route('school_followup_add', $client->id) }}">
            <div class="row g-3 align-items-end">

                <div class="col-12 col-md-6 col-xl-3">
                    <label class="sf-filter-label">ตั้งแต่วันที่</label>
                    <input type="date"
                           name="start_date"
                           class="form-control sf-filter-control"
                           value="{{ request('start_date') }}">
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <label class="sf-filter-label">ถึงวันที่</label>
                    <input type="date"
                           name="end_date"
                           class="form-control sf-filter-control"
                           value="{{ request('end_date') }}">
                </div>

                <div class="col-12 col-xl-6">
                    <div class="sf-filter-actions">

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                            ค้นหา
                        </button>

                        <a href="{{ route('school_followup.report.range', [
                                'client_id' => $client->id,
                                'start_date' => request('start_date'),
                                'end_date' => request('end_date')
                            ]) }}"
                           class="btn btn-outline-primary">
                            <i class="bi bi-file-earmark-text"></i>
                            รายงานรวม
                        </a>

                        <a href="{{ route('school_followup_add', $client->id) }}"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            ล้างค่า
                        </a>

                    </div>
                </div>

            </div>
        </form>

    </div>
</div>

@endif