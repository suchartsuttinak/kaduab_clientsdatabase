<header class="co-header">
    <div class="co-header-main">
        <span class="co-header-icon" aria-hidden="true">
            <i class="bi bi-geo-alt-fill"></i>
        </span>

        <div class="co-header-copy">
            <h1 class="co-header-title">
                การติดตามเด็กที่พักอาศัยภายนอก
            </h1>
             <p class="co-empty-header-subtitle">
                        ผู้รับบริการ:
                        <strong>{{ $client->fullname ?? $client->name ?? '-' }}</strong>
                         <span class="mx-1">•</span>
                            อายุ: <strong>{{ is_numeric($client->age ?? null) ? $client->age . ' ปี' : '-' }}</strong>
                    </p>
        </div>
    </div>

    <div class="co-header-actions">
        <button type="button"
                class="btn btn-primary co-btn"
                data-bs-toggle="modal"
                data-bs-target="#createCaseOutsideModal">
            <i class="bi bi-plus-circle"></i>
            <span>เพิ่มข้อมูล</span>
        </button>

        <a href="{{ route('case_outside.filter', $client->id) }}"
           class="btn btn-outline-primary co-btn">
            <i class="bi bi-funnel"></i>
            <span>ค้นหารายงาน</span>
        </a>

        <a href="{{ route('case_outside.report', $client->id) }}"
           target="_blank"
           rel="noopener"
           class="btn btn-success co-btn">
            <i class="bi bi-printer"></i>
            <span>รายงานทั้งหมด</span>
        </a>

        <a href="{{ route('admin.index', $client->id) }}"
           class="btn co-btn co-btn-back">
            <i class="bi bi-arrow-left-circle"></i>
            <span>กลับ</span>
        </a>
    </div>
</header>
