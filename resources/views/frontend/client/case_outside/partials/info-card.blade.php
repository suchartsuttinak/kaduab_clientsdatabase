<section class="co-info-card" aria-label="ข้อมูลผู้รับบริการ">
    <div class="co-info-grid">
        <div class="co-info-item">
            <span class="co-info-item-icon" aria-hidden="true">
                <i class="bi bi-person-fill"></i>
            </span>
            <div>
                <span class="co-info-item-label">ชื่อ-สกุล</span>
                <div class="co-info-item-value">
                    {{ $client->fullname ?? $client->name ?? '-' }}
                </div>
            </div>
        </div>

        <div class="co-info-item">
            <span class="co-info-item-icon" aria-hidden="true">
                <i class="bi bi-calendar-heart"></i>
            </span>
            <div>
                <span class="co-info-item-label">อายุ</span>
                <div class="co-info-item-value">
                    {{ isset($client->age) && $client->age !== '' ? $client->age . ' ปี' : '-' }}
                </div>
            </div>
        </div>
    </div>
</section>
