<div class="escape-page-header">
    <div class="escape-page-header__left">
        <div class="escape-page-header__icon">
            <i class="bi bi-box-arrow-right"></i>
        </div>
        <div>
            <h4 class="escape-page-header__title mb-1">ข้อมูลการออกจากสถานสงเคราะห์</h4>
            <div class="escape-page-header__subtitle">
                  <p class="co-empty-header-subtitle">
                        ผู้รับบริการ:
                        <strong>{{ $client->fullname ?? $client->name ?? '-' }}</strong>
                         <span class="mx-1">•</span>
                            อายุ: <strong>{{ is_numeric($client->age ?? null) ? $client->age . ' ปี' : '-' }}</strong>
                    </p>
            </div>
            
        </div>
    </div>

    {{-- เมื่อมีข้อมูลแล้วจึงแสดงปุ่มเพิ่มด้านบน
         กรณียังไม่มีข้อมูล จะใช้ปุ่มเพิ่มครั้งแรกภายในกล่องสถานะว่าง --}}
    @if ($escapes->isNotEmpty())
        <div class="escape-page-header__right">
            <button type="button"
                    class="btn escape-btn escape-btn--primary"
                    data-bs-toggle="modal"
                    data-bs-target="#escapeCreateModal">
                <i class="bi bi-plus-circle"></i>
                <span>เพิ่มข้อมูล</span>
            </button>
        </div>
    @endif
</div>