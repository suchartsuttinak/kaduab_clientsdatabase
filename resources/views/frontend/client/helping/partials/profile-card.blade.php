@php
    $clientInitial = mb_substr(trim($client->fullname ?? $client->full_name ?? 'U'), 0, 1);

    $clientImageUrl = !empty($client->image)
        ? route('client.image', $client->id)
        : asset('upload/no_image.jpg');
@endphp

{{--
    HOST-SAFE PROFILE CARD
    เก็บ CSS ที่จำเป็นไว้กับ partial นี้โดยตรง เพื่อไม่ให้ layout เพี้ยนเมื่อไฟล์
    public/backend/assets/css/help-sessions.css บน Linux host หาย/เป็น cache เก่า/ชื่อไฟล์ต่างตัวพิมพ์เล็ก-ใหญ่
--}}
<style>
    .help-page .hp-top-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(300px, 1fr);
        gap: 1rem;
        align-items: stretch;
        margin-bottom: 1rem;
    }

    .help-page .hp-profile-card,
    .help-page .hp-summary-card {
        min-width: 0;
        background: #fff;
        border: 1px solid #dbe3ef;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .045);
        overflow: hidden;
    }

    .help-page .hp-profile-inner {
        min-height: 162px;
        padding: .9rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .help-page .hp-profile-photo-col {
        width: 136px;
        flex: 0 0 136px;
    }

    .help-page .summary-avatar,
    .help-page .hp-avatar-fallback--large {
        width: 136px !important;
        height: 136px !important;
        max-width: 136px !important;
        max-height: 136px !important;
        border-radius: 14px;
    }

    .help-page .summary-avatar {
        display: block;
        overflow: hidden;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
    }

    .help-page .summary-avatar img {
        display: block !important;
        width: 136px !important;
        height: 136px !important;
        max-width: 136px !important;
        max-height: 136px !important;
        min-width: 0 !important;
        min-height: 0 !important;
        margin: 0 !important;
        object-fit: cover !important;
        object-position: center !important;
    }

    .help-page .hp-avatar-fallback--large {
        align-items: center;
        justify-content: center;
        color: #1d4ed8;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        font-size: 2rem;
        font-weight: 800;
    }

    .help-page .hp-profile-content {
        min-width: 0;
        flex: 1 1 auto;
    }

    .help-page .hp-profile-label {
        width: fit-content;
        max-width: 100%;
        margin-bottom: .55rem;
        padding: .28rem .62rem;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        color: #1d4ed8;
        background: #eff6ff;
        border-radius: 999px;
        font-size: .82rem;
        font-weight: 800;
        line-height: 1.25;
    }

    .help-page .hp-profile-name {
        color: #0f172a;
        font-size: clamp(1.08rem, 1.25vw, 1.28rem);
        font-weight: 800;
        line-height: 1.4;
        word-break: break-word;
    }

    .help-page .hp-profile-meta {
        margin-top: .65rem;
        display: flex;
        align-items: center;
        gap: .5rem;
        flex-wrap: wrap;
    }

    .help-page .hp-meta-chip {
        min-height: 30px;
        padding: .3rem .62rem;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        color: #334155;
        background: #fff;
        border: 1px solid #dbe3ef;
        border-radius: 999px;
        font-size: .84rem;
        font-weight: 750;
        line-height: 1.25;
        white-space: nowrap;
    }

    .help-page .hp-meta-chip i {
        color: #2563eb;
    }

    .help-page .hp-summary-card {
        min-height: 162px;
        padding: 1.2rem;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
    }

    .help-page .hp-summary-label {
        color: #64748b;
        font-size: .9rem;
        font-weight: 700;
        line-height: 1.4;
    }

    .help-page .hp-summary-value {
        margin-top: .35rem;
        color: #1d4ed8;
        font-size: clamp(1.35rem, 1.8vw, 1.65rem);
        font-weight: 850;
        line-height: 1.25;
        font-variant-numeric: tabular-nums;
    }

    .help-page .hp-summary-sub {
        margin-top: .4rem;
        color: #64748b;
        font-size: .84rem;
        line-height: 1.45;
    }

    @media (max-width: 991.98px) {
        .help-page .hp-top-grid {
            grid-template-columns: 1fr;
        }

        .help-page .hp-summary-card {
            min-height: auto;
        }
    }

    @media (max-width: 575.98px) {
        .help-page .hp-profile-inner {
            min-height: 0;
            padding: .85rem;
            align-items: flex-start;
        }

        .help-page .hp-profile-photo-col {
            width: 92px;
            flex-basis: 92px;
        }

        .help-page .summary-avatar,
        .help-page .summary-avatar img,
        .help-page .hp-avatar-fallback--large {
            width: 92px !important;
            height: 92px !important;
            max-width: 92px !important;
            max-height: 92px !important;
        }

        .help-page .hp-profile-meta {
            align-items: flex-start;
        }

        .help-page .hp-meta-chip {
            white-space: normal;
        }
    }
</style>

<div class="hp-top-grid">
    <div class="hp-profile-card">
        <div class="hp-profile-inner hp-profile-inner--enhanced">
            <div class="hp-profile-photo-col">
                <div class="summary-avatar">
                    <img
                        src="{{ $clientImageUrl }}"
                        alt="client-image"
                        width="136"
                        height="136"
                        loading="lazy"
                        decoding="async"
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
