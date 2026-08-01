<div class="card border-0 psych-client-toolbar-card mb-3">
@php
    /*
     * แสดงตัวกรองเมื่อมีข้อมูล หรือเมื่อผู้ใช้กำลังใช้เงื่อนไขค้นหาอยู่
     * เพื่อไม่ให้ตัวกรองหายหลังค้นหาแล้วไม่พบรายการในช่วงวันที่นั้น
     */
    $hasPsychiatricRows = isset($psychiatrics) && $psychiatrics->isNotEmpty();
    $hasActivePsychiatricFilter = request()->filled('start_date') || request()->filled('end_date');
    $showPsychiatricFilter = $hasPsychiatricRows || $hasActivePsychiatricFilter;
@endphp
    <style>
        /* =========================
           Scoped CSS: psych-client-toolbar
           ใช้เฉพาะบล็อกนี้ ไม่กระทบส่วนอื่น
        ========================= */
        .psych-client-toolbar-card{
            border-radius: 18px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .psych-client-toolbar-card .card-body{
            padding: 18px 18px 16px;
        }

        .psych-client-toolbar-card .psych-client-top{
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .psych-client-toolbar-card .psych-client-top-left{
            min-width: 0;
            flex: 1 1 520px;
        }

        .psych-client-toolbar-card .psych-client-title{
            margin: 0 0 4px;
            font-size: 1.02rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.4;
        }

        .psych-client-toolbar-card .psych-client-subtitle{
            margin: 0;
            font-size: 0.88rem;
            color: #64748b;
            line-height: 1.65;
        }

        .psych-client-toolbar-card .psych-client-info-grid{
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(0, 1.7fr);
            gap: 12px;
            margin-bottom: 14px;
        }

        .psych-client-toolbar-card .psych-client-info{
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            min-width: 0;
        }

        .psych-client-toolbar-card .psych-client-item{
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            padding: 10px 12px;
            border-radius: 12px;
            background: linear-gradient(180deg, #fbfcfe 0%, #f8fafc 100%);
            border: 1px solid #e8eef5;
        }

        .psych-client-toolbar-card .psych-client-item i{
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

        .psych-client-toolbar-card .psych-client-item--age i{
            background: #ecfdf5;
            color: #059669;
        }

        .psych-client-toolbar-card .psych-client-text{
            min-width: 0;
        }

        .psych-client-toolbar-card .psych-client-text .label{
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 2px;
            line-height: 1.3;
        }

        .psych-client-toolbar-card .psych-client-text .value{
            display: block;
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.45;
            word-break: break-word;
        }

        .psych-client-toolbar-card .psych-filter-panel{
            padding: 14px;
            border: 1px solid #e6edf5;
            border-radius: 14px;
            background: #f8fafc;
        }

        .psych-client-toolbar-card .psych-filter-head{
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .psych-client-toolbar-card .psych-filter-title{
            margin: 0;
            font-size: 0.95rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.4;
        }

        .psych-client-toolbar-card .psych-filter-desc{
            margin: 3px 0 0;
            font-size: 0.82rem;
            color: #64748b;
            line-height: 1.6;
        }

        .psych-client-toolbar-card .psych-filter-grid{
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto;
            gap: 12px;
            align-items: end;
        }

        .psych-client-toolbar-card .psych-filter-group{
            min-width: 0;
        }

        .psych-client-toolbar-card .psych-filter-group label{
            display: block;
            margin-bottom: 6px;
            font-size: 0.84rem;
            font-weight: 700;
            color: #334155;
        }

        .psych-client-toolbar-card .psych-filter-group .form-control{
            min-height: 44px;
            border-radius: 12px;
            border-color: #dbe3ec;
            box-shadow: none;
            font-size: 0.92rem;
        }

        .psych-client-toolbar-card .psych-filter-group .form-control:focus{
            border-color: #93c5fd;
            box-shadow: 0 0 0 0.18rem rgba(37, 99, 235, 0.10);
        }

        .psych-client-toolbar-card .psych-filter-actions{
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .psych-client-toolbar-card .psych-btn{
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
        }

        .psych-client-toolbar-card .psych-btn i{
            font-size: 0.95rem;
            line-height: 1;
        }

        .psych-client-toolbar-card .psych-btn-report{
            background: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }

        .psych-client-toolbar-card .psych-btn-report:hover{
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
        }

        .psych-client-toolbar-card .psych-btn-search{
            background: #0f766e;
            border-color: #0f766e;
            color: #ffffff;
        }

        .psych-client-toolbar-card .psych-btn-search:hover{
            background: #0b5f58;
            border-color: #0b5f58;
            color: #ffffff;
        }

        .psych-client-toolbar-card .psych-btn-reset{
            background: #ffffff;
            border-color: #cfd8e3;
            color: #334155;
        }

        .psych-client-toolbar-card .psych-btn-reset:hover{
            background: #f8fafc;
            color: #0f172a;
        }

        .psych-client-toolbar-card .psych-inline-note{
            margin-top: 10px;
            font-size: 0.8rem;
            color: #64748b;
            line-height: 1.6;
        }

        @media (max-width: 991.98px){
            .psych-client-toolbar-card .psych-client-info-grid{
                grid-template-columns: 1fr;
            }

            .psych-client-toolbar-card .psych-filter-grid{
                grid-template-columns: 1fr 1fr;
            }

            .psych-client-toolbar-card .psych-filter-actions{
                grid-column: 1 / -1;
                justify-content: flex-start;
            }
        }

        @media (max-width: 767.98px){
            .psych-client-toolbar-card .card-body{
                padding: 14px;
            }

            .psych-client-toolbar-card .psych-client-item{
                width: 100%;
                align-items: flex-start;
            }

            .psych-client-toolbar-card .psych-filter-grid{
                grid-template-columns: 1fr;
            }

            .psych-client-toolbar-card .psych-filter-actions{
                width: 100%;
            }

            .psych-client-toolbar-card .psych-filter-actions .psych-btn{
                width: 100%;
            }
        }
    </style>

    <div class="card-body">

        <div class="psych-client-top">
            <div class="psych-client-top-left">
                <h5 class="psych-client-title">ข้อมูลผู้รับบริการและเครื่องมือรายงานจิตเวช</h5>
                <p class="psych-client-subtitle">
                    ตรวจสอบข้อมูลผู้รับบริการ ค้นหาข้อมูลตามช่วงวันที่ส่งพบจิตเวช และเปิดรายงานตามเงื่อนไขที่เลือกได้จากส่วนเดียว
                </p>
            </div>
        </div>

        <div class="psych-client-info-grid">
            <div class="psych-client-info">
                <div class="psych-client-item">
                    <i class="bi bi-person-fill"></i>
                    <div class="psych-client-text">
                        <span class="label">ชื่อ-สกุล</span>
                        <span class="value">{{ trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')) ?: ($client->fullname ?? $client->name ?? '-') }}</span>
                    </div>
                </div>

                <div class="psych-client-item psych-client-item--age">
                    <i class="bi bi-calendar-heart"></i>
                    <div class="psych-client-text">
                        <span class="label">อายุ</span>
                        <span class="value">{{ filled($client->age) ? $client->age . ' ปี' : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($showPsychiatricFilter)
        <form method="GET" action="{{ route('psychiatric.create', $client->id) }}" class="psych-filter-panel">
            <div class="psych-filter-head">
                <div>
                    <h6 class="psych-filter-title">ตัวกรองข้อมูลตามช่วงวันที่ส่งพบจิตเวช</h6>
                    <p class="psych-filter-desc">
                        เลือกช่วงวันที่เพื่อค้นหาข้อมูลในหน้านี้ หรือเปิดหน้ารายงานตามช่วงวันที่ที่เลือก
                    </p>
                </div>
            </div>

            <div class="psych-filter-grid">
                <div class="psych-filter-group">
                    <label for="psych_date_from">วันที่เริ่มต้น</label>
                    <input
                        type="date"
                        name="start_date"
                        id="psych_date_from"
                        class="form-control @error('start_date') is-invalid @enderror"
                        value="{{ $startDate }}"
                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                    >
                    @error('start_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="psych-filter-group">
                    <label for="psych_date_to">วันที่สิ้นสุด</label>
                    <input
                        type="date"
                        name="end_date"
                        id="psych_date_to"
                        class="form-control @error('end_date') is-invalid @enderror"
                        value="{{ $endDate }}"
                        min="{{ $startDate }}"
                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                    >
                    @error('end_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="psych-filter-actions">
                    <a href="{{ route('psychiatric.report', ['client_id' => $client->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                       class="psych-btn psych-btn-report">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>รายงานทั้งหมด</span>
                    </a>

                    <button type="submit" class="psych-btn psych-btn-search">
                        <i class="bi bi-search"></i>
                        <span>ค้นหาข้อมูล</span>
                    </button>

                    <a href="{{ route('psychiatric.create', $client->id) }}"
                       class="psych-btn psych-btn-reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>ล้างค่า</span>
                    </a>
                </div>
            </div>

            
        </form>
        @endif

    </div>
</div>