<div class="rf-info-card">
    <div class="rf-info-grid">
        <div class="rf-info-item">
            <span class="rf-info-item-icon" aria-hidden="true">
                <i class="bi bi-person-fill"></i>
            </span>
            <div>
                <span class="rf-info-label">ชื่อ-สกุล</span>
                <div class="rf-info-value">
                    {{ $client->fullname ?? $client->full_name ?? $client->name ?? '-' }}
                </div>
            </div>
        </div>

        <div class="rf-info-item">
            <span class="rf-info-item-icon" aria-hidden="true">
                <i class="bi bi-calendar-heart"></i>
            </span>
            <div>
                <span class="rf-info-label">อายุ</span>
                <div class="rf-info-value">
                    {{ filled($client->age ?? null) ? $client->age . ' ปี' : '-' }}
                </div>
            </div>
        </div>
    </div>
</div>
