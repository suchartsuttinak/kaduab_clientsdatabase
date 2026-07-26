@extends('admin.admin_master')

@section('admin')

<div class="container-fluid idstation-central-page">

    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>
                    <h3 class="fw-bold text-primary mb-1">
                        <i class="bi bi-person-vcard me-2"></i>
                        ศูนย์กลางทะเบียนบุคคลไม่มีสถานะทางทะเบียน
                    </h3>

                    <p class="text-muted mb-0">
                        ภาพรวมการรับเรื่อง การติดตาม และผลการช่วยเหลือด้านสถานะทางทะเบียน
                    </p>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('idstation.central.report', request()->query()) }}"
                        class="btn btn-outline-primary"
                        target="_blank">
                        <i class="bi bi-printer me-1"></i>
                        พิมพ์รายงาน
                    </a>

                   <a href="{{ route('dashboard') }}" class="btn btn-primary client-btn">
                    <i data-feather="arrow-left-circle"></i>
                    <span>ย้อนกลับ</span>
                </a>

                </div>

            </div>

        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="summary-card border-start border-primary">
                <div>
                    <p>รับเรื่องทั้งหมด</p>
                    <h3>{{ number_format($summary['total']) }}</h3>
                </div>
                <i class="bi bi-folder2-open text-primary"></i>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="summary-card border-start border-warning">
                <div>
                    <p>อยู่ระหว่างดำเนินการ</p>
                    <h3>{{ number_format($summary['pending']) }}</h3>
                </div>
                <i class="bi bi-hourglass-split text-warning"></i>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="summary-card border-start border-success">
                <div>
                    <p>ได้รับสถานะแล้ว</p>
                    <h3>{{ number_format($summary['completed']) }}</h3>
                </div>
                <i class="bi bi-check-circle text-success"></i>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="summary-card border-start border-danger">
                <div>
                    <p>เกิน 180 วัน</p>
                    <h3>{{ number_format($summary['over_180']) }}</h3>
                </div>
                <i class="bi bi-exclamation-triangle text-danger"></i>
            </div>
        </div>

    </div>

   

{{-- Result Summary --}}
<div class="card border-0 shadow-sm mb-4 result-summary-card">
    <div class="card-header bg-white border-0 pb-0">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1">
                    <i class="bi bi-check2-circle text-success me-2"></i>
                    สรุปผลสถานะที่ได้รับ
                </h5>
                <div class="text-muted small">
                    แสดงผลตามข้อมูลจริงจากตารางสถานะทางทะเบียนที่ได้รับ
                </div>
            </div>

            <span class="badge bg-light text-dark border">
                {{ number_format($summary['completed']) }} รายการสำเร็จ
            </span>
        </div>
    </div>

    <div class="card-body">
        <div class="result-summary-list">

            <div class="result-summary-row warning">
                <div class="result-summary-name">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    เกิน 90 วัน
                </div>
                <div class="result-summary-count">
                    {{ number_format($summary['over_90']) }}
                </div>
            </div>

            @foreach($citizenSummary as $item)
                <div class="result-summary-row">
                    <div class="result-summary-name">
                        {{ $item['name'] }}
                    </div>
                    <div class="result-summary-count">
                        {{ number_format($item['count']) }}
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>



  {{-- Filter --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0">
        <h5 class="fw-bold mb-0">
            <i class="bi bi-funnel me-2 text-primary"></i>
            ตัวกรองข้อมูล
        </h5>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('idstation.central.index') }}">

            <div class="row g-3">

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">วันที่เริ่มต้น</label>
                    <input type="date"
                           name="date_from"
                           class="form-control"
                           value="{{ request('date_from') }}"
                           max="{{ now('Asia/Bangkok')->toDateString() }}">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">วันที่สิ้นสุด</label>
                    <input type="date"
                           name="date_to"
                           class="form-control"
                           value="{{ request('date_to') }}"
                           max="{{ now('Asia/Bangkok')->toDateString() }}">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">บ้าน</label>
                    <select name="house_id" class="form-select">
                        <option value="">ทั้งหมด</option>
                        @foreach($houses as $house)
                            <option value="{{ $house->id }}" {{ request('house_id') == $house->id ? 'selected' : '' }}>
                                {{ $house->house_name ?? $house->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">โครงการ</label>
                    <select name="project_id" class="form-select">
                        <option value="">ทั้งหมด</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->project_name ?? $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">สถานะการดำเนินงาน</label>
                    <select name="process_status" class="form-select">
                        <option value="">ทั้งหมด</option>
                        <option value="pending" {{ request('process_status') == 'pending' ? 'selected' : '' }}>
                            อยู่ระหว่างดำเนินการ
                        </option>
                        <option value="completed" {{ request('process_status') == 'completed' ? 'selected' : '' }}>
                            ได้รับสถานะแล้ว
                        </option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">ระยะเวลาดำเนินการ</label>
                    <select name="duration_status" class="form-select">
                        <option value="">ทั้งหมด</option>
                        <option value="over_90" {{ request('duration_status') == 'over_90' ? 'selected' : '' }}>
                            เกิน 90 วัน
                        </option>
                        <option value="over_180" {{ request('duration_status') == 'over_180' ? 'selected' : '' }}>
                            เกิน 180 วัน
                        </option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">สถานะที่ได้รับ</label>
                  <select name="result_status" class="form-select">
                        <option value="">ทั้งหมด</option>
                        @foreach($citizens as $citizen)
                            <option value="{{ $citizen->id }}" {{ request('result_status') == $citizen->id ? 'selected' : '' }}>
                                {{ $citizen->citizen_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">ค้นหา</label>
                    <input type="text"
                           name="keyword"
                           class="form-control"
                           value="{{ request('keyword') }}"
                           placeholder="ค้นหาชื่อ - สกุล">
                </div>

            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('idstation.central.index') }}"
                   class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    ล้างตัวกรอง
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>
                    ค้นหา
                </button>
            </div>

        </form>
    </div>
</div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-table me-2 text-primary"></i>
                รายการบุคคลไม่มีสถานะทางทะเบียน
            </h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">ลำดับ</th>
                            <th>ชื่อ - สกุล</th>
                            <th class="text-center">อายุ</th>
                            <th>บ้าน</th>
                            <th>โครงการ</th>
                            <th class="text-center">วันที่รับเรื่อง</th>
                            <th>รายการทางทะเบียน</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center">ระยะเวลา</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
            <tbody>
    @forelse($idstations as $index => $idstation)

        @php
            $client = $idstation->client;

          $days = $idstation->receive_date
                ? \Carbon\Carbon::parse($idstation->receive_date)
                    ->startOfDay()
                    ->diffInDays(now('Asia/Bangkok')->startOfDay())
                : 0;

            $days = (int) floor($days);

            $isCompleted = $idstation->citizens->count() > 0;
            if ($isCompleted) {
                $statusText = 'ได้รับสถานะแล้ว';
                $statusClass = 'bg-success';
            } else {
                $statusText = 'อยู่ระหว่างดำเนินการ';
                $statusClass = $days > 180 ? 'bg-danger' : ($days > 90 ? 'bg-warning text-dark' : 'bg-secondary');
            }

            $resultText = '-';

            if ($idstation->citizenships->count() > 0) {
                $resultText = 'ได้สัญชาติ';
            } elseif ($idstation->citizens->count() > 0) {
                $resultText = 'ได้เลขประจำตัว';
            }
        @endphp

        <tr>
            <td class="text-center">
                {{ $idstations->firstItem() + $index }}
            </td>

            <td>
                <div class="fw-semibold">
                    {{ $client->first_name ?? '-' }} {{ $client->last_name ?? '' }}
                </div>

               <small class="text-muted">
                    {{ $client->target->target_name ?? '' }}
                </small>
            </td>

            <td class="text-center">
                {{ $client->age ?? '-' }}
            </td>

            <td>
                {{ $client->house->house_name ?? $client->house->name ?? '-' }}
            </td>

            <td>
                {{ $client->project->project_name ?? $client->project->name ?? '-' }}
            </td>

            <td class="text-center">
               @if($idstation->receive_date)
                        {{ \Carbon\Carbon::parse($idstation->receive_date)->locale('th')->translatedFormat('d F') }}
                        {{ \Carbon\Carbon::parse($idstation->receive_date)->year + 543 }}
                    @else
                        -
                    @endif
                   
            </td>

            <td>
                {{ $resultText }}
            </td>

            <td class="text-center">
                <span class="badge {{ $statusClass }}">
                    {{ $statusText }}
                </span>
            </td>

            <td class="text-center">
                {{ $days }} วัน
            </td>

            <td class="text-center">
                <a href="{{ route('idstation.index', $client->id) }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i>
                    ดูเคส
                </a>
            </td>
        </tr>

    @empty
        <tr>
            <td colspan="10" class="text-center text-muted py-5">
                <i class="bi bi-info-circle me-1"></i>
                ไม่พบข้อมูลบุคคลไม่มีสถานะทางทะเบียน
            </td>
        </tr>
    @endforelse
</tbody>
                </table>

                <div class="mt-3">
    {{ $idstations->links() }}
</div>
            </div>

        </div>
    </div>

</div>

<style>
.idstation-central-page{
    padding-bottom:40px;
}

/* ===========================
   Summary Card
=========================== */
.summary-card{
    background:#ffffff;
    border-radius:16px;
    padding:18px 20px;
    min-height:105px;
    border:1px solid #e9eef5;
    box-shadow:0 3px 12px rgba(15,23,42,.04);
    display:flex;
    align-items:center;
    justify-content:space-between;
    transition:.25s;
}

.summary-card:hover{
    transform:translateY(-2px);
    border-color:#cfe0ff;
    box-shadow:0 8px 20px rgba(37,99,235,.08);
}

.summary-card p{
    margin:0 0 5px;
    color:#2563eb;
    font-size:13px;
    font-weight:600;
}

.summary-card h3,
.summary-card h4{
    margin:0;
    color:#0f172a;
    font-size:27px;
    font-weight:800;
    line-height:1;
}

.summary-card > i{
    color:#2563eb;
    font-size:30px;
    opacity:.85;
}

/* ===========================
   Result Summary
=========================== */
.result-summary-card{
    border-radius:18px;
    border:1px solid #e6edf5;
    background:#ffffff;
    box-shadow:0 3px 14px rgba(15,23,42,.04);
    overflow:hidden;
}

.result-summary-card .card-header{
    padding:18px 22px 12px;
    background:#ffffff;
    border-bottom:1px solid #edf2f7;
}

.result-summary-card h5{
    color:#0f172a;
    font-size:15px;
    font-weight:800;
}

.result-summary-card .small{
    color:#64748b;
    font-size:12px;
}

.result-summary-card .badge{
    background:#eff6ff !important;
    color:#1d4ed8 !important;
    border:1px solid #bfdbfe;
    border-radius:999px;
    padding:6px 12px;
    font-size:11px;
    font-weight:700;
}

.result-summary-list{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    column-gap:34px;
    row-gap:0;
}

.result-summary-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:18px;
    padding:10px 2px;
    border-bottom:1px dashed #e5eaf2;
}

.result-summary-row:nth-last-child(-n+2){
    border-bottom:none;
}

.result-summary-name{
    display:flex;
    align-items:center;
    gap:8px;
    min-width:0;
    color:#2563eb;
    font-size:13px;
    font-weight:600;
    line-height:1.45;
}

.result-summary-name i{
    color:#94a3b8;
    font-size:13px;
}

.result-summary-count{
    min-width:42px;
    text-align:right;
    color:#1e293b;
    font-size:16px;
    font-weight:800;
    line-height:1;
}

.result-summary-row.warning{
    padding:10px 2px;
    background:transparent;
}

.result-summary-row.warning .result-summary-name{
    color:#b45309;
}

.result-summary-row.warning .result-summary-name i{
    color:#f59e0b;
}

.result-summary-row.warning .result-summary-count{
    color:#b45309;
}

/* ===========================
   Table
=========================== */
.table thead th{
    white-space:nowrap;
    font-size:13px;
    font-weight:700;
    color:#1e3a8a;
    background:#f8fafc;
    border-bottom:2px solid #e2e8f0;
}

.table tbody td{
    font-size:13px;
    color:#334155;
    vertical-align:middle;
}

.table tbody tr:hover{
    background:#f8fbff;
}

/* ===========================
   Responsive
=========================== */
@media (max-width:992px){
    .result-summary-list{
        grid-template-columns:1fr;
    }

    .result-summary-row,
    .result-summary-row:nth-last-child(-n+2){
        border-bottom:1px dashed #e5eaf2;
    }

    .result-summary-row:last-child{
        border-bottom:none;
    }
}

@media (max-width:768px){
    .summary-card{
        min-height:95px;
        padding:16px;
    }

    .summary-card h3,
    .summary-card h4{
        font-size:22px;
    }

    .summary-card > i{
        font-size:24px;
    }

    .result-summary-card .card-header{
        padding:16px;
    }

    .result-summary-name{
        font-size:12.5px;
    }

    .result-summary-count{
        font-size:15px;
    }
}
</style>

@endsection