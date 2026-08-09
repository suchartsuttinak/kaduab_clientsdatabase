@php
    $clientInitial = mb_substr(trim($client->fullname ?? $client->full_name ?? 'U'), 0, 1);

    $clientImageUrl = !empty($client->image)
        ? route('client.image', $client->id)
        : asset('upload/no_image.jpg');
@endphp

<div class="hp-top-grid">
    <div class="hp-profile-card">
        <div class="hp-profile-inner hp-profile-inner--enhanced">
            <div class="hp-profile-photo-col">
                <div class="summary-avatar">
                    <img
                        src="{{ $clientImageUrl }}"
                        alt="client-image"
                        onerror="this.style.display='none'; this.closest('.summary-avatar').nextElementSibling.style.display='inline-flex';"
                    >
                </div>

                <div class="hp-avatar-fallback hp-avatar-fallback--large" style="display:none;">
                    {{ $clientInitial }}
                </div>
            </div>

            <div class="hp-profile-content">
                <div class="hp-profile-label">
                    <i class="bi bi-person-badge"></i>
                    <span>ข้อมูลผู้รับบริการ</span>
                </div>

                <div class="hp-profile-name">{{ $client->fullname ?? $client->full_name ?? '-' }}</div>

                <div class="hp-profile-meta">
                    <span class="hp-meta-chip">
                        <i class="bi bi-calendar-heart"></i>
                        อายุ {{ $client->age ?? '-' }} ปี
                    </span>
                    <span class="hp-meta-chip">
                        <i class="bi bi-clipboard2-heart"></i>
                        จำนวนทั้งหมด {{ number_format($totalSessionCount ?? $sessions->count()) }} ครั้ง
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="hp-summary-card">
        <div class="hp-summary-label">ยอดรวมตามรายการที่แสดง</div>
        <div class="hp-summary-value">
            {{ number_format($grandTotal ?? 0, 2) }} บาท
        </div>
        <div class="hp-summary-sub">
            สรุปจากข้อมูลที่แสดงตามช่วงวันที่ที่เลือก
        </div>
    </div>
</div>