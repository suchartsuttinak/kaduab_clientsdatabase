<div class="ab-pagebar" data-permission-keep>
    <div class="ab-pagebar-top">
        <div class="ab-pagebar-main">
            <span class="ab-title-icon" aria-hidden="true">
                <i class="bi bi-calendar-x"></i>
            </span>

            <div>
                <h1 class="ab-page-title">บันทึกการขาดเรียน</h1>
                <div class="ab-page-count">{{ $absents->count() }} รายการ</div>
            </div>
        </div>

        @if($absents->isNotEmpty())
            <div class="ab-page-actions">
                @if($canShowAbsentFilter)
                    <button type="button"
                            class="btn btn-outline-primary ab-btn ab-filter-toggle"
                            data-bs-toggle="collapse"
                            data-bs-target="#absentFilterPanel"
                            data-absent-filter-toggle
                            data-permission-keep
                            aria-controls="absentFilterPanel"
                            aria-expanded="{{ $showAbsentFilter ? 'true' : 'false' }}">
                        <i class="bi {{ $showAbsentFilter ? 'bi-chevron-up' : 'bi-funnel' }}"
                           data-filter-toggle-icon
                           aria-hidden="true"></i>

                        <span data-filter-toggle-label>
                            {{ $showAbsentFilter ? 'ซ่อนการค้นหา' : 'ค้นหารายการ' }}
                        </span>
                    </button>
                @endif

                @if($canAbsentPrint)
                    <a href="{{ route('absent.report.range', [
                            'client_id' => $client->id,
                            'start_date' => request('start_date'),
                            'end_date' => request('end_date')
                        ]) }}"
                       class="btn btn-outline-primary ab-btn"
                       data-permission-action="print">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>รายงานรวม</span>
                    </a>
                @endif

                @if($canAbsentCreate)
                    <button type="button"
                            class="btn btn-primary ab-btn"
                            id="btn-open-absent-modal"
                            data-bs-toggle="modal"
                            data-bs-target="#absentModal"
                            data-permission-action="create">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มข้อมูล</span>
                    </button>
                @endif
            </div>
        @endif
    </div>

    <div class="ab-pagebar-details">
        <div class="ab-pagebar-detail">
            <span class="ab-pagebar-detail-label">
                <i class="bi bi-building"></i>
                สถานศึกษา
            </span>
            <span class="ab-pagebar-detail-value">{{ $schoolName }}</span>
        </div>

        <div class="ab-pagebar-detail">
            <span class="ab-pagebar-detail-label">
                <i class="bi bi-book"></i>
                ระดับชั้น
            </span>
            <span class="ab-pagebar-detail-value">{{ $educationName }}</span>
        </div>

        <div class="ab-pagebar-detail">
            <span class="ab-pagebar-detail-label">
                <i class="bi bi-calendar3"></i>
                ภาคเรียน
            </span>
            <span class="ab-pagebar-detail-value">{{ $semesterName }}</span>
        </div>
    </div>
</div>