<div class="sf-pagebar" data-permission-keep>
    <div class="sf-pagebar-top">
        <div class="sf-pagebar-main">
            <span class="sf-title-icon" aria-hidden="true">
                <i class="bi bi-mortarboard"></i>
            </span>

            <div>
                <h1 class="sf-page-title">ติดตามผลการเรียน</h1>
                <div class="sf-page-count">{{ $followups->count() }} รายการ</div>
            </div>
        </div>

        @if($hasFollowups)
            <div class="sf-page-actions">
                <button type="button"
                        class="btn btn-outline-primary sf-btn sf-filter-toggle"
                        data-bs-toggle="collapse"
                        data-bs-target="#schoolFollowupFilterPanel"
                        data-school-followup-filter-toggle
                        data-permission-keep
                        aria-controls="schoolFollowupFilterPanel"
                        aria-expanded="{{ $showSchoolFollowupFilter ? 'true' : 'false' }}">
                    <i class="bi {{ $showSchoolFollowupFilter ? 'bi-chevron-up' : 'bi-funnel' }}"
                       data-filter-toggle-icon
                       aria-hidden="true"></i>
                    <span data-filter-toggle-label>
                        {{ $showSchoolFollowupFilter ? 'ซ่อนการค้นหา' : 'ค้นหารายการ' }}
                    </span>
                </button>

                @if($canSchoolPrint)
                    <a href="{{ route('school_followup.report.range', [
                            'client_id' => $client->id,
                            'start_date' => request('start_date'),
                            'end_date' => request('end_date')
                        ]) }}"
                       class="btn btn-outline-primary sf-btn"
                       data-permission-action="print">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>รายงานรวม</span>
                    </a>
                @endif

                @if($canSchoolCreate)
                    <button type="button"
                            class="btn btn-primary sf-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#followupModal"
                            data-permission-action="create">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มข้อมูล</span>
                    </button>
                @endif
            </div>
        @endif
    </div>

    <div class="sf-pagebar-details">
        <div class="sf-pagebar-detail">
            <span class="sf-pagebar-detail-label">
                <i class="bi bi-building"></i>
                สถานศึกษา
            </span>
            <span class="sf-pagebar-detail-value">{{ $schoolName }}</span>
        </div>

        <div class="sf-pagebar-detail">
            <span class="sf-pagebar-detail-label">
                <i class="bi bi-book"></i>
                ระดับชั้น
            </span>
            <span class="sf-pagebar-detail-value">{{ $educationName }}</span>
        </div>

        <div class="sf-pagebar-detail">
            <span class="sf-pagebar-detail-label">
                <i class="bi bi-calendar3"></i>
                ภาคเรียน
            </span>
            <span class="sf-pagebar-detail-value">{{ $semesterName }}</span>
        </div>
    </div>
</div>