@php
    $clientDisplayName = trim(($client->prefix ?? '') . ' ' . ($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
    if ($clientDisplayName === '') {
        $clientDisplayName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
    }

    $clientAgeText = isset($client->age) && $client->age !== null && $client->age !== ''
        ? $client->age . ' ปี'
        : '-';

    $hasAddictiveRows = isset($addictives) && $addictives->isNotEmpty();
@endphp

<style>
    .addictive-client-toolbar-card {
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
    }

    .addictive-client-toolbar-card .card-body {
        padding: 18px 18px 16px;
    }

    .addictive-client-toolbar-card .addictive-client-top {
        margin-bottom: 14px;
    }

    .addictive-client-toolbar-card .addictive-client-title {
        margin: 0 0 4px;
        color: #0f172a;
        font-size: 1.02rem;
        font-weight: 800;
    }

    .addictive-client-toolbar-card .addictive-client-subtitle {
        margin: 0;
        color: #64748b;
        font-size: .88rem;
        line-height: 1.65;
    }

    .addictive-client-toolbar-card .addictive-client-info {
        display: flex;
        align-items: stretch;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .addictive-client-toolbar-card .addictive-client-item {
        display: inline-flex;
        min-width: 220px;
        flex: 1 1 260px;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border: 1px solid #e8eef5;
        border-radius: 12px;
        background: linear-gradient(180deg, #fbfcfe 0%, #f8fafc 100%);
    }

    .addictive-client-toolbar-card .addictive-client-item i {
        display: inline-flex;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #eef4ff;
        color: #2563eb;
    }

    .addictive-client-toolbar-card .addictive-client-text .label {
        display: block;
        margin-bottom: 2px;
        color: #64748b;
        font-size: .78rem;
        font-weight: 700;
    }

    .addictive-client-toolbar-card .addictive-client-text .value {
        display: block;
        color: #0f172a;
        font-size: .95rem;
        font-weight: 750;
        overflow-wrap: anywhere;
    }

    .addictive-client-toolbar-card .addictive-filter-panel {
        padding: 14px;
        border: 1px solid #e6edf5;
        border-radius: 14px;
        background: #f8fafc;
    }

    .addictive-client-toolbar-card .addictive-filter-title {
        margin: 0 0 12px;
        color: #0f172a;
        font-size: .95rem;
        font-weight: 800;
    }

    .addictive-client-toolbar-card .addictive-filter-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto;
        gap: 12px;
        align-items: end;
    }

    .addictive-client-toolbar-card .addictive-filter-group label {
        display: block;
        margin-bottom: 6px;
        color: #334155;
        font-size: .84rem;
        font-weight: 700;
    }

    .addictive-client-toolbar-card .form-control {
        min-height: 44px;
        border: 1px solid #dbe3ec;
        border-radius: 12px;
        box-shadow: none;
    }

    .addictive-client-toolbar-card .addictive-filter-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 9px;
        flex-wrap: wrap;
    }

    .addictive-client-toolbar-card .addictive-btn {
        display: inline-flex;
        min-height: 44px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: .65rem .9rem;
        border-radius: 11px;
        font-size: .86rem;
        font-weight: 750;
        line-height: 1.2;
        text-decoration: none;
        white-space: nowrap;
    }

    .addictive-client-toolbar-card .addictive-btn-report {
        border: 1px solid #2563eb;
        background: #2563eb;
        color: #ffffff;
    }

    .addictive-client-toolbar-card .addictive-btn-search {
        border: 1px solid #0f766e;
        background: #0f766e;
        color: #ffffff;
    }

    .addictive-client-toolbar-card .addictive-btn-reset {
        border: 1px solid #cfd8e3;
        background: #ffffff;
        color: #334155;
    }

    .addictive-client-toolbar-card .addictive-inline-note {
        margin-top: 10px;
        color: #64748b;
        font-size: .8rem;
        line-height: 1.55;
    }

    @media (max-width: 991.98px) {
        .addictive-client-toolbar-card .addictive-filter-grid {
            grid-template-columns: 1fr 1fr;
        }

        .addictive-client-toolbar-card .addictive-filter-actions {
            grid-column: 1 / -1;
            justify-content: flex-start;
        }
    }

    @media (max-width: 767.98px) {
        .addictive-client-toolbar-card .card-body {
            padding: 14px;
        }

        .addictive-client-toolbar-card .addictive-filter-grid {
            grid-template-columns: 1fr;
        }

        .addictive-client-toolbar-card .addictive-filter-actions,
        .addictive-client-toolbar-card .addictive-filter-actions .addictive-btn {
            width: 100%;
        }
    }
</style>

<div class="card border-0 addictive-client-toolbar-card mb-3">
    <div class="card-body">
        <div class="addictive-client-top">
            <h5 class="addictive-client-title">
                {{ $hasAddictiveRows ? 'ข้อมูลผู้รับบริการและเครื่องมือรายงาน' : 'ข้อมูลผู้รับบริการ' }}
            </h5>
            <p class="addictive-client-subtitle">
                {{ $hasAddictiveRows
                    ? 'ตรวจสอบข้อมูลผู้รับบริการและเลือกช่วงวันที่สำหรับเปิดรายงาน'
                    : 'ตรวจสอบข้อมูลผู้รับบริการก่อนบันทึกผลการตรวจสารเสพติดครั้งแรก' }}
            </p>
        </div>

        <div class="addictive-client-info {{ $hasAddictiveRows ? '' : 'mb-0' }}">
            <div class="addictive-client-item">
                <i class="bi bi-person-fill"></i>
                <div class="addictive-client-text">
                    <span class="label">ชื่อ-สกุล</span>
                    <span class="value">{{ $clientDisplayName }}</span>
                </div>
            </div>

            <div class="addictive-client-item">
                <i class="bi bi-calendar-heart"></i>
                <div class="addictive-client-text">
                    <span class="label">อายุ</span>
                    <span class="value">{{ $clientAgeText }}</span>
                </div>
            </div>

            @if($hasAddictiveRows)
                <div class="addictive-client-item">
                    <i class="bi bi-journal-check"></i>
                    <div class="addictive-client-text">
                        <span class="label">จำนวนบันทึก</span>
                        <span class="value">{{ number_format($addictives->count()) }} รายการ</span>
                    </div>
                </div>
            @endif
        </div>

        @if($hasAddictiveRows)
            <form method="GET" action="{{ route('addictive.report.all', $client->id) }}" class="addictive-filter-panel">
                <h6 class="addictive-filter-title">ตัวกรองรายงานตามช่วงวันที่</h6>

                <div class="addictive-filter-grid">
                    <div class="addictive-filter-group">
                        <label for="addictive_date_from">วันที่เริ่มต้น</label>
                        <input type="date"
                               name="date_from"
                               id="addictive_date_from"
                               class="form-control @error('date_from') is-invalid @enderror"
                               value="{{ old('date_from', request('date_from')) }}"
                               max="{{ now('Asia/Bangkok')->toDateString() }}">
                        @error('date_from')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="addictive-filter-group">
                        <label for="addictive_date_to">วันที่สิ้นสุด</label>
                        <input type="date"
                               name="date_to"
                               id="addictive_date_to"
                               class="form-control @error('date_to') is-invalid @enderror"
                               value="{{ old('date_to', request('date_to')) }}"
                               max="{{ now('Asia/Bangkok')->toDateString() }}">
                        @error('date_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="addictive-filter-actions">
                        <a href="{{ route('addictive.report.all', $client->id) }}"
                           class="addictive-btn addictive-btn-report">
                            <i class="bi bi-file-earmark-text"></i><span>รายงานทั้งหมด</span>
                        </a>

                        <button type="submit" class="addictive-btn addictive-btn-search">
                            <i class="bi bi-search"></i><span>ค้นหารายงาน</span>
                        </button>

                        <button type="button"
                                class="addictive-btn addictive-btn-reset"
                                onclick="document.getElementById('addictive_date_from').value=''; document.getElementById('addictive_date_to').value=''; document.getElementById('addictive_date_from').focus();">
                            <i class="bi bi-arrow-counterclockwise"></i><span>ล้างค่า</span>
                        </button>
                    </div>
                </div>

                <div class="addictive-inline-note">
                    วันที่เริ่มต้นและวันที่สิ้นสุดต้องไม่เกินวันปัจจุบัน
                </div>
            </form>
        @endif
    </div>
</div>