@php
    $medicalFilterActive = request()->filled('start_date') || request()->filled('end_date');
    $medicalFilteredEmpty = ($hasMedicalRecords ?? false) && $medicalFilterActive;
@endphp

<div class="md-table-card" data-permission-keep>
    <div class="md-table-head">
        <h2 class="md-table-title">
            <i class="bi bi-list-check" aria-hidden="true"></i>
            รายการการรักษาพยาบาล
        </h2>
        <span class="md-table-meta">ทั้งหมด 0 รายการ</span>
    </div>

    <div class="md-table-body">
        <div class="md-empty">
            <div class="md-empty-icon">
                <i class="bi {{ $medicalFilteredEmpty ? 'bi-search' : 'bi-clipboard2-pulse' }}"></i>
            </div>

            @if($medicalFilteredEmpty)
                <h3 class="md-empty-title">ไม่พบข้อมูลตามช่วงวันที่ที่เลือก</h3>
                <p class="md-empty-text">ปรับช่วงวันที่ใหม่ หรือล้างตัวกรองเพื่อแสดงข้อมูลทั้งหมด</p>
                <a href="{{ route('medical.add', ['client_id' => $client->id]) }}"
                   class="btn btn-outline-secondary md-btn"
                   data-permission-keep>
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>ล้างตัวกรอง</span>
                </a>
            @else
                <h3 class="md-empty-title">ยังไม่มีข้อมูลการรักษาพยาบาล</h3>
                <p class="md-empty-text">เพิ่มข้อมูลครั้งแรกเพื่อเริ่มบันทึกและติดตามการรักษา</p>
                @if($canMedicalCreate)
                    <button type="button"
                            class="btn btn-primary md-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#add-medical-modal"
                            data-permission-action="create">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มข้อมูล</span>
                    </button>
                @endif
            @endif
        </div>
    </div>
</div>
