@php
    $clientAge = $client->age ?? null;
@endphp

<div class="ja-info-card">
    <div class="ja-info-grid">
        <div class="ja-info-item">
            <span class="ja-info-item-icon" aria-hidden="true">
                <i class="bi bi-person-fill"></i>
            </span>
            <div>
                <span class="ja-info-label">ชื่อ-สกุล</span>
                <div class="ja-info-value">
                    {{ $client->fullname ?? $client->full_name ?? $client->name ?? '-' }}
                </div>
            </div>
        </div>

        <div class="ja-info-item">
            <span class="ja-info-item-icon" aria-hidden="true">
                <i class="bi bi-calendar-heart"></i>
            </span>
            <div>
                <span class="ja-info-label">อายุ</span>
                <div class="ja-info-value">
                    {{ $clientAge !== null && $clientAge !== '' ? $clientAge . ' ปี' : '-' }}
                </div>
            </div>
        </div>
    </div>
</div>
