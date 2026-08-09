@php
    $thaiDateShort = function ($date) {
        if (blank($date)) return '-';
        try {
            $d = \Carbon\Carbon::parse($date);
            return $d->format('d/m/') . ($d->year + 543);
        } catch (\Throwable $e) {
            return '-';
        }
    };
@endphp

<div class="csl-previous">
    <div class="csl-previous-head">
        <div class="csl-previous-title">
            <i class="bi bi-journal-check me-1"></i>
            สรุปจากรอบก่อนหน้า — รอบที่ {{ $previousRound['round_no'] }}
        </div>

        <a href="{{ route('counseling.followup.report', [$counseling->id, $previousRound['round_no']]) }}"
           class="csl-btn-outline py-1 px-2"
           style="min-height:32px;">
            <i class="bi bi-file-earmark-text"></i>
            เปิดรายงานรอบก่อน
        </a>
    </div>

    <div class="csl-previous-grid">
        <div class="csl-previous-item">
            <div class="csl-previous-label">วันที่</div>
            <div class="csl-previous-value">{{ $thaiDateShort($previousRound['date']) }}</div>
        </div>

        <div class="csl-previous-item">
            <div class="csl-previous-label">ประเด็นที่ดำเนินการ</div>
            <div class="csl-previous-value">{{ $previousRound['topic'] ?: '-' }}</div>
        </div>

        <div class="csl-previous-item">
            <div class="csl-previous-label">ผล / แนวทางต่อ</div>
            <div class="csl-previous-value">
                {{ $previousRound['result'] ?: '-' }}
                @if(filled($previousRound['next_action']) && $previousRound['next_action'] !== '-')
                    <div class="mt-1"><strong>ดำเนินการต่อ:</strong> {{ $previousRound['next_action'] }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
