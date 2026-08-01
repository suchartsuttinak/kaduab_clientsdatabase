@php
    $clientDisplayName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
    if ($clientDisplayName === '') {
        $clientDisplayName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
    }

    $clientAgeDisplay = filled($client->age ?? null)
        ? ($client->age . ' ปี')
        : '-';

    /*
     * ซ่อนเครื่องมือตัวกรองเมื่อผู้รับบริการยังไม่มีประวัติวัคซีน
     * แต่หากผู้ใช้กำลังกรองข้อมูลอยู่ แม้ผลลัพธ์เป็นศูนย์
     * ให้คงตัวกรองไว้เพื่อเปลี่ยนช่วงวันที่หรือล้างค่าได้
     */
    $hasVaccineRows = isset($vaccinations) && $vaccinations->isNotEmpty();
    $hasActiveVaccineFilter = request()->filled('start_date')
        || request()->filled('end_date');
    $showVaccineFilter = $hasVaccineRows || $hasActiveVaccineFilter;
@endphp

<div class="card border-0 vaccine-client-toolbar-card mb-3">
    <style>
        .vaccine-client-toolbar-card{
            border-radius: 18px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .vaccine-client-toolbar-card .card-body{
            padding: 18px 18px 16px;
        }

        .vaccine-client-toolbar-card .vaccine-client-top{
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .vaccine-client-toolbar-card .vaccine-client-top-left{
            min-width: 0;
            flex: 1 1 520px;
        }

        .vaccine-client-toolbar-card .vaccine-client-title{
            margin: 0 0 4px;
            font-size: 1.02rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.4;
        }

        .vaccine-client-toolbar-card .vaccine-client-subtitle{
            margin: 0;
            font-size: 0.88rem;
            color: #64748b;
            line-height: 1.65;
        }

        .vaccine-client-toolbar-card .vaccine-client-info-grid{
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(0, 1.7fr);
            gap: 12px;
            margin-bottom: 14px;
        }

        .vaccine-client-toolbar-card .vaccine-client-info{
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            min-width: 0;
        }

        .vaccine-client-toolbar-card .vaccine-client-item{
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            padding: 10px 12px;
            border-radius: 12px;
            background: linear-gradient(180deg, #fbfcfe 0%, #f8fafc 100%);
            border: 1px solid #e8eef5;
        }

        .vaccine-client-toolbar-card .vaccine-client-item i{
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #eef4ff;
            color: #2563eb;
            font-size: 1rem;
            flex: 0 0 auto;
        }

        .vaccine-client-toolbar-card .vaccine-client-item--age i{
            background: #ecfdf5;
            color: #059669;
        }

        .vaccine-client-toolbar-card .vaccine-client-text{
            min-width: 0;
        }

        .vaccine-client-toolbar-card .vaccine-client-text .label{
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 2px;
            line-height: 1.3;
        }

        .vaccine-client-toolbar-card .vaccine-client-text .value{
            display: block;
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.45;
            word-break: break-word;
        }

        .vaccine-client-toolbar-card .vaccine-filter-panel{
            padding: 14px;
            border: 1px solid #e6edf5;
            border-radius: 14px;
            background: #f8fafc;
        }

        .vaccine-client-toolbar-card .vaccine-filter-head{
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .vaccine-client-toolbar-card .vaccine-filter-title{
            margin: 0;
            font-size: 0.95rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.4;
        }

        .vaccine-client-toolbar-card .vaccine-filter-desc{
            margin: 3px 0 0;
            font-size: 0.82rem;
            color: #64748b;
            line-height: 1.6;
        }

        .vaccine-client-toolbar-card .vaccine-filter-grid{
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto;
            gap: 12px;
            align-items: end;
        }

        .vaccine-client-toolbar-card .vaccine-filter-group{
            min-width: 0;
        }

        .vaccine-client-toolbar-card .vaccine-filter-group label{
            display: block;
            margin-bottom: 6px;
            font-size: 0.84rem;
            font-weight: 700;
            color: #334155;
        }

        .vaccine-client-toolbar-card .vaccine-filter-group .form-control{
            min-height: 44px;
            border-radius: 12px;
            border-color: #dbe3ec;
            box-shadow: none;
            font-size: 0.92rem;
        }

        .vaccine-client-toolbar-card .vaccine-filter-group .form-control:focus{
            border-color: #93c5fd;
            box-shadow: 0 0 0 0.18rem rgba(37, 99, 235, 0.10);
        }

        .vaccine-client-toolbar-card .vaccine-filter-actions{
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .vaccine-client-toolbar-card .vaccine-btn{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 0.68rem 1rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.92rem;
            white-space: nowrap;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all .2s ease;
        }

        .vaccine-client-toolbar-card .vaccine-btn i{
            font-size: 0.95rem;
            line-height: 1;
        }

        .vaccine-client-toolbar-card .vaccine-btn-report{
            background: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }

        .vaccine-client-toolbar-card .vaccine-btn-report:hover{
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
        }

        .vaccine-client-toolbar-card .vaccine-btn-search{
            background: #0f766e;
            border-color: #0f766e;
            color: #ffffff;
        }

        .vaccine-client-toolbar-card .vaccine-btn-search:hover{
            background: #0b5f58;
            border-color: #0b5f58;
            color: #ffffff;
        }

        .vaccine-client-toolbar-card .vaccine-btn-reset{
            background: #ffffff;
            border-color: #cfd8e3;
            color: #334155;
        }

        .vaccine-client-toolbar-card .vaccine-btn-reset:hover{
            background: #f8fafc;
            color: #0f172a;
        }

        @media (max-width: 991.98px){
            .vaccine-client-toolbar-card .vaccine-client-info-grid{
                grid-template-columns: 1fr;
            }

            .vaccine-client-toolbar-card .vaccine-filter-grid{
                grid-template-columns: 1fr 1fr;
            }

            .vaccine-client-toolbar-card .vaccine-filter-actions{
                grid-column: 1 / -1;
                justify-content: flex-start;
            }
        }

        @media (max-width: 767.98px){
            .vaccine-client-toolbar-card .card-body{
                padding: 14px;
            }

            .vaccine-client-toolbar-card .vaccine-client-item{
                width: 100%;
                align-items: flex-start;
            }

            .vaccine-client-toolbar-card .vaccine-filter-grid{
                grid-template-columns: 1fr;
            }

            .vaccine-client-toolbar-card .vaccine-filter-actions{
                width: 100%;
            }

            .vaccine-client-toolbar-card .vaccine-filter-actions .vaccine-btn{
                width: 100%;
            }
        }
    </style>

    <div class="card-body">

        <div class="vaccine-client-top">
            <div class="vaccine-client-top-left">
                <h5 class="vaccine-client-title">ข้อมูลผู้รับบริการและเครื่องมือรายงานวัคซีน</h5>
                <p class="vaccine-client-subtitle">
                    @if($showVaccineFilter)
                        ตรวจสอบข้อมูลผู้รับบริการ ค้นหาข้อมูลตามช่วงวันที่รับวัคซีน และเปิดหน้ารายงานตามเงื่อนไขที่เลือกได้จากส่วนเดียว
                    @else
                        ตรวจสอบข้อมูลผู้รับบริการและเริ่มบันทึกประวัติการรับวัคซีนรายการแรก
                    @endif
                </p>
            </div>
        </div>

        <div class="vaccine-client-info-grid">
            <div class="vaccine-client-info">
                <div class="vaccine-client-item">
                    <i class="bi bi-person-fill"></i>
                    <div class="vaccine-client-text">
                        <span class="label">ชื่อ-สกุล</span>
                        <span class="value">{{ $clientDisplayName }}</span>
                    </div>
                </div>

                <div class="vaccine-client-item vaccine-client-item--age">
                    <i class="bi bi-calendar-heart"></i>
                    <div class="vaccine-client-text">
                        <span class="label">อายุ</span>
                        <span class="value">{{ $clientAgeDisplay }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($showVaccineFilter)
        <form method="GET" action="{{ route('vaccine.index', ['client_id' => $client->id]) }}" class="vaccine-filter-panel">
            <div class="vaccine-filter-head">
                <div>
                    <h6 class="vaccine-filter-title">ตัวกรองข้อมูลตามช่วงวันที่รับวัคซีน</h6>
                    <p class="vaccine-filter-desc">
                        เลือกช่วงวันที่เพื่อค้นหาข้อมูลในหน้านี้ หรือเปิดหน้ารายงานตามช่วงวันที่ที่เลือก
                    </p>
                </div>
            </div>

            <div class="vaccine-filter-grid">
                <div class="vaccine-filter-group">
                    <label for="vaccine_date_from">วันที่เริ่มต้น</label>
                    <input
                        type="date"
                        name="start_date"
                        id="vaccine_date_from"
                        class="form-control @error('start_date') is-invalid @enderror"
                        value="{{ request('start_date') }}"
                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                    >
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="vaccine-filter-group">
                    <label for="vaccine_date_to">วันที่สิ้นสุด</label>
                    <input
                        type="date"
                        name="end_date"
                        id="vaccine_date_to"
                        class="form-control @error('end_date') is-invalid @enderror"
                        value="{{ request('end_date') }}"
                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                    >
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="vaccine-filter-actions">
                    <a href="{{ route('vaccine.report', [
                        'client_id'  => $client->id,
                        'start_date' => request('start_date'),
                        'end_date'   => request('end_date')
                    ]) }}"
                       class="vaccine-btn vaccine-btn-report">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>{{ request()->filled('start_date') || request()->filled('end_date') ? 'รายงานตามช่วงวันที่' : 'รายงานทั้งหมด' }}</span>
                    </a>

                    <button type="submit" class="vaccine-btn vaccine-btn-search">
                        <i class="bi bi-search"></i>
                        <span>ค้นหาข้อมูล</span>
                    </button>

                    <a href="{{ route('vaccine.index', ['client_id' => $client->id]) }}"
                       class="vaccine-btn vaccine-btn-reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>ล้างค่า</span>
                    </a>
                </div>
            </div>
        </form>
        @endif

    </div>
</div>