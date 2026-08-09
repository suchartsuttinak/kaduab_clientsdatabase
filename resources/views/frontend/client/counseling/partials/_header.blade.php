<header class="csl-header">
    <div class="csl-header-main">
        <div class="csl-header-icon" aria-hidden="true">
            <i class="bi bi-chat-heart"></i>
        </div>

        <div class="min-w-0">
            <h1 class="csl-title">การให้คำปรึกษา</h1>
            <div class="csl-subtitle">
                <span>ผู้รับบริการ: <strong>{{ $clientDisplayName }}</strong></span>
                <span class="csl-dot">•</span>
                <span>อายุ: <strong>{{ $clientAgeText }}</strong></span>
            </div>
        </div>
    </div>

    @if ($counselingCount > 0 && $canStartNewCounseling)
        <div class="csl-header-actions">
            <button type="button"
                    class="csl-btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#counselingCreateModal">
                <i class="bi bi-plus-circle"></i>
                เริ่มการให้คำปรึกษาครั้งใหม่
            </button>
        </div>
    @elseif ($counselingCount > 0 && $hasOpenCounseling)
        <div class="csl-header-actions">
            <a href="{{ route('counseling.show', $latestCounseling->id) }}"
               class="csl-btn-outline">
                <i class="bi bi-arrow-repeat"></i>
                ครั้งที่ {{ $latestCounseling->session_no }} ยังดำเนินการอยู่
            </a>
        </div>
    @endif
</header>
