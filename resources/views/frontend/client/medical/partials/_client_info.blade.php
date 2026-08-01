@php
    $medicalClientName = trim((string) ($client->fullname ?? $client->full_name ?? ''));
    if ($medicalClientName === '') {
        $medicalClientName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
    }
    $medicalClientName = $medicalClientName !== '' ? $medicalClientName : '-';
@endphp

@if(($hasMedicalRecords ?? $medicals->isNotEmpty()) || request()->filled('start_date') || request()->filled('end_date') || old('start_date') || old('end_date') || $errors->has('start_date') || $errors->has('end_date'))
<div class="card border-0 shadow-sm mb-3 medical-client-summary-card">
    <div class="card-body p-3 p-lg-4">

        {{-- แถวบน: ข้อมูลผู้รับบริการ --}}
        <div class="medical-client-topbar">
            <div class="medical-client-grid">
                <div class="medical-summary-item">
                    <span class="medical-summary-icon medical-summary-icon--primary">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <div class="medical-summary-content">
                        <div class="medical-summary-label">ชื่อ-สกุล</div>
                        <div class="medical-summary-value">{{ $medicalClientName }}</div>
                    </div>
                </div>

                <div class="medical-summary-item">
                    <span class="medical-summary-icon medical-summary-icon--success">
                        <i class="bi bi-calendar-heart"></i>
                    </span>
                    <div class="medical-summary-content">
                        <div class="medical-summary-label">อายุ</div>
                        <div class="medical-summary-value">{{ filled($client->age ?? null) ? ($client->age . ' ปี') : '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="medical-note-box">
                <i class="bi bi-shield-check me-1"></i>
                ข้อมูลสุขภาพควรบันทึกอย่างถูกต้อง ครบถ้วน และติดตามต่อเนื่อง
            </div>
        </div>

        {{-- แถวล่าง: ตัวกรอง --}}
        <form method="GET"
              action="{{ route('medical.add', ['client_id' => $client->id]) }}"
              class="medical-filter-panel">

            <div class="medical-filter-head">
                <div>
                    <h6 class="medical-filter-title">ตัวกรองข้อมูลตามช่วงวันที่</h6>
                    <p class="medical-filter-desc">
                        เลือกช่วงวันที่เพื่อค้นหาข้อมูลในหน้านี้ หรือเปิดหน้ารายงานตามช่วงวันที่ที่เลือก
                    </p>
                </div>
            </div>

            <div class="medical-filter-grid">
                <div class="medical-filter-group">
                    <label for="medical_start_date">วันที่เริ่มต้น</label>
                    <input
                        type="date"
                        id="medical_start_date"
                        name="start_date"
                        class="form-control @error('start_date') is-invalid @enderror"
                        value="{{ old('start_date', request('start_date')) }}"
                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                    >
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="medical-filter-group">
                    <label for="medical_end_date">วันที่สิ้นสุด</label>
                    <input
                        type="date"
                        id="medical_end_date"
                        name="end_date"
                        class="form-control @error('end_date') is-invalid @enderror"
                        value="{{ old('end_date', request('end_date')) }}"
                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                    >
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="medical-filter-actions">
                    <button type="submit" class="medical-btn medical-btn-search">
                        <i class="bi bi-search"></i>
                        <span>ค้นหา</span>
                    </button>

                    <a href="{{ route('medical.add', ['client_id' => $client->id]) }}"
                       class="medical-btn medical-btn-reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>ล้างค่า</span>
                    </a>

                    <a href="{{ route('medical.report', [
                        'client_id'  => $client->id,
                        'start_date' => request('start_date'),
                        'end_date'   => request('end_date')
                    ]) }}"
                       class="medical-btn medical-btn-report">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>รายงานทั้งหมด</span>
                    </a>
                </div>
            </div>
        </form>

    </div>
</div>
@endif

@push('styles')
<style>
.medical-page .medical-client-summary-card{
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.06);
    overflow: hidden;
    background: #ffffff;
}

.medical-page .medical-client-topbar{
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    padding-bottom: 16px;
    margin-bottom: 16px;
    border-bottom: 1px solid #edf2f7;
}

.medical-page .medical-client-grid{
    display: flex;
    flex-wrap: wrap;
    gap: 14px 22px;
    align-items: center;
}

.medical-page .medical-summary-item{
    display: inline-flex;
    align-items: center;
    gap: 12px;
    min-width: 220px;
    max-width: 100%;
}

.medical-page .medical-summary-icon{
    width: 46px;
    height: 46px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex: 0 0 46px;
}

.medical-page .medical-summary-icon--primary{
    background: #dbeafe;
    color: #1d4ed8;
}

.medical-page .medical-summary-icon--success{
    background: #dcfce7;
    color: #15803d;
}

.medical-page .medical-summary-content{
    min-width: 0;
}

.medical-page .medical-summary-label{
    font-size: .78rem;
    color: #64748b;
    margin-bottom: 2px;
    line-height: 1.2;
}

.medical-page .medical-summary-value{
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.35;
    word-break: break-word;
}

.medical-page .medical-note-box{
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    color: #475569;
    border-radius: 14px;
    padding: 12px 14px;
    font-size: .875rem;
    max-width: 420px;
}

.medical-page .medical-filter-panel{
    padding: 16px;
    border: 1px solid #e6edf5;
    border-radius: 18px;
    background: linear-gradient(180deg, #fbfdff 0%, #f8fafc 100%);
}

.medical-page .medical-filter-head{
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 14px;
}

.medical-page .medical-filter-title{
    margin: 0;
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.4;
}

.medical-page .medical-filter-desc{
    margin: 4px 0 0;
    font-size: .84rem;
    color: #64748b;
    line-height: 1.6;
}

.medical-page .medical-filter-grid{
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto;
    gap: 12px;
    align-items: end;
}

.medical-page .medical-filter-group{
    min-width: 0;
}

.medical-page .medical-filter-group label{
    display: block;
    margin-bottom: 6px;
    font-size: .84rem;
    font-weight: 700;
    color: #334155;
}

.medical-page .medical-filter-group .form-control{
    min-height: 46px;
    border-radius: 12px;
    border-color: #dbe3ec;
    box-shadow: none;
    font-size: .92rem;
}

.medical-page .medical-filter-group .form-control:focus{
    border-color: #93c5fd;
    box-shadow: 0 0 0 0.18rem rgba(37, 99, 235, 0.10);
}

.medical-page .medical-filter-actions{
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.medical-page .medical-btn{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 46px;
    padding: 0.72rem 1rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.92rem;
    white-space: nowrap;
    text-decoration: none;
    border: 1px solid transparent;
    transition: all .2s ease;
}

.medical-page .medical-btn i{
    font-size: 0.95rem;
    line-height: 1;
}

.medical-page .medical-btn-search{
    background: #0f766e;
    border-color: #0f766e;
    color: #ffffff;
}

.medical-page .medical-btn-search:hover{
    background: #0b5f58;
    border-color: #0b5f58;
    color: #ffffff;
}

.medical-page .medical-btn-report{
    background: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
}

.medical-page .medical-btn-report:hover{
    background: #1d4ed8;
    border-color: #1d4ed8;
    color: #ffffff;
}

.medical-page .medical-btn-reset{
    background: #ffffff;
    border-color: #cfd8e3;
    color: #334155;
}

.medical-page .medical-btn-reset:hover{
    background: #f8fafc;
    color: #0f172a;
}

@media (max-width: 1199.98px){
    .medical-page .medical-filter-grid{
        grid-template-columns: 1fr 1fr;
    }

    .medical-page .medical-filter-actions{
        grid-column: 1 / -1;
        justify-content: flex-start;
    }
}

@media (max-width: 767.98px){
    .medical-page .medical-client-summary-card .card-body{
        padding: 14px !important;
    }

    .medical-page .medical-client-topbar{
        flex-direction: column;
        align-items: flex-start;
    }

    .medical-page .medical-client-grid{
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
        width: 100%;
    }

    .medical-page .medical-summary-item{
        width: 100%;
        min-width: 0;
    }

    .medical-page .medical-note-box{
        max-width: 100%;
        width: 100%;
    }

    .medical-page .medical-filter-panel{
        padding: 14px;
    }

    .medical-page .medical-filter-grid{
        grid-template-columns: 1fr;
    }

    .medical-page .medical-filter-actions{
        width: 100%;
    }

    .medical-page .medical-filter-actions .medical-btn{
        width: 100%;
    }
}
</style>
@endpush