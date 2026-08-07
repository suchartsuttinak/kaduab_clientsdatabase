@php
    $showMedicalActions = $canShowMedicalFilter
        || ($medicals->isNotEmpty() && $canMedicalPrint)
        || ($hasMedicalRecords && $canMedicalCreate);
@endphp

<div class="md-pagebar" data-permission-keep>
    <div class="md-pagebar-main">
        <span class="md-title-icon" aria-hidden="true">
            <i class="bi bi-heart-pulse"></i>
        </span>

        <div class="md-title-group">
            <h1 class="md-page-title">การรักษาพยาบาล</h1>
            <div class="md-page-count">
                {{ number_format($medicals->count()) }} รายการ
            </div>
        </div>
    </div>

    @if($showMedicalActions)
        <div class="md-page-actions">
            @if($canShowMedicalFilter)
                <button type="button"
                        class="btn md-btn md-filter-toggle md-btn-filter"
                        data-bs-toggle="collapse"
                        data-bs-target="#medicalFilterPanel"
                        data-medical-filter-toggle
                        data-permission-keep
                        aria-controls="medicalFilterPanel"
                        aria-expanded="{{ $showMedicalFilter ? 'true' : 'false' }}">
                    <span class="md-btn-icon" aria-hidden="true">
                        <i class="bi {{ $showMedicalFilter ? 'bi-chevron-up' : 'bi-funnel' }}"
                           data-filter-toggle-icon></i>
                    </span>

                    <span data-filter-toggle-label>
                        {{ $showMedicalFilter ? 'ซ่อนการค้นหา' : 'ค้นหารายการ' }}
                    </span>
                </button>
            @endif

            @if($medicals->isNotEmpty() && $canMedicalPrint)
                <a href="{{ route('medical.report', [
                        'client_id' => $client->id,
                        'start_date' => request('start_date'),
                        'end_date' => request('end_date')
                    ]) }}"
                   class="btn md-btn md-btn-report"
                   data-permission-action="print">
                    <span class="md-btn-icon" aria-hidden="true">
                        <i class="bi bi-file-earmark-text"></i>
                    </span>
                    <span>รายงานรวม</span>
                </a>
            @endif

            @if($hasMedicalRecords && $canMedicalCreate)
                <button type="button"
                        class="btn md-btn md-btn-create"
                        data-bs-toggle="modal"
                        data-bs-target="#add-medical-modal"
                        data-permission-action="create">
                    <span class="md-btn-icon" aria-hidden="true">
                        <i class="bi bi-plus-circle"></i>
                    </span>
                    <span>เพิ่มข้อมูล</span>
                </button>
            @endif
        </div>
    @endif
</div>