@extends('admin_client.admin_client')

@section('title', 'รายละเอียดการให้คำปรึกษา')

@section('content')
@php
    $clientName = trim((string) ($client->fullname ?? ''));
    if ($clientName === '') {
        $clientName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
    }
    if ($clientName === '') $clientName = '-';

    $thaiDate = function ($date) {
        if (blank($date)) return '-';
        try {
            $d = \Carbon\Carbon::parse($date);
            return $d->format('d/m/') . ($d->year + 543);
        } catch (\Throwable $e) {
            return '-';
        }
    };

    $roundCount = 1 + $counseling->followups->count();
    $latestFollowup = $counseling->followups->sortByDesc('followup_no')->first();
    $latestDate = $latestFollowup?->followup_date ?: $counseling->session_date;
    $isOpen = in_array($counseling->status, ['ongoing', 'follow_up', 'improved'], true);
    $nextRoundNo = $roundCount + 1;

    $statusClass = match ($counseling->status) {
        'ongoing', 'improved' => 'csl-status--open',
        'follow_up' => 'csl-status--track',
        'goal_met' => 'csl-status--goal',
        'referred' => 'csl-status--refer',
        'closed' => 'csl-status--closed',
        default => 'csl-status--closed',
    };
@endphp

@include('frontend.client.counseling.partials._styles')

<div class="container-fluid csl-page">
    <header class="csl-header">
        <div class="csl-header-main">
            <div class="csl-header-icon"><i class="bi bi-journal-heart"></i></div>
            <div>
                <h1 class="csl-title">
                    การให้คำปรึกษา ครั้งที่ {{ $counseling->session_no }}
                </h1>
                <div class="csl-subtitle">
                    <span>ผู้รับบริการ: <strong>{{ $clientName }}</strong></span>
                    <span class="csl-dot">•</span>
                    <span>ประเด็นหลัก: <strong>{{ \Illuminate\Support\Str::limit($counseling->presenting_problem, 80) }}</strong></span>
                </div>
            </div>
        </div>

        <div class="csl-header-actions">
            @if ($isOpen)
                <a href="{{ route('counseling.followup.create', $counseling->id) }}"
                   class="csl-btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    บันทึกรอบที่ {{ $nextRoundNo }}
                </a>
            @endif

            <a href="{{ route('counseling.report', $counseling->id) }}"
               class="csl-btn-outline">
                <i class="bi bi-printer"></i>
                รายงานรวม
            </a>

            <a href="{{ route('counseling.index', $client->id) }}"
               class="csl-btn-secondary">
                <i class="bi bi-arrow-left"></i>
                กลับ
            </a>
        </div>
    </header>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 small">
            <strong>กรุณาตรวจสอบข้อมูล</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="csl-summary-grid">
        <div class="csl-summary-item">
            <div class="csl-summary-label">วันที่เริ่ม</div>
            <div class="csl-summary-value">{{ $thaiDate($counseling->session_date) }}</div>
        </div>
        <div class="csl-summary-item">
            <div class="csl-summary-label">จำนวนรอบทั้งหมด</div>
            <div class="csl-summary-value">{{ $roundCount }} รอบ</div>
        </div>
        <div class="csl-summary-item">
            <div class="csl-summary-label">รอบล่าสุด</div>
            <div class="csl-summary-value">รอบที่ {{ $roundCount }} • {{ $thaiDate($latestDate) }}</div>
        </div>
        <div class="csl-summary-item">
            <div class="csl-summary-label">สถานะปัจจุบัน</div>
            <div class="csl-summary-value">
                <span class="csl-status {{ $statusClass }}">{{ $counseling->status_label }}</span>
            </div>
        </div>
    </div>

    @if ($isOpen && filled($counseling->next_appointment_date))
        <div class="csl-previous">
            <div class="csl-previous-head">
                <div class="csl-previous-title">
                    <i class="bi bi-calendar-event me-1"></i>
                    ข้อมูลสำหรับรอบถัดไป
                </div>
            </div>
            <div class="csl-previous-grid">
                <div class="csl-previous-item">
                    <div class="csl-previous-label">นัดหมายครั้งต่อไป</div>
                    <div class="csl-previous-value">{{ $thaiDate($counseling->next_appointment_date) }}</div>
                </div>
                <div class="csl-previous-item" style="grid-column: span 2;">
                    <div class="csl-previous-label">ประเด็นที่จะดำเนินการต่อ</div>
                    <div class="csl-previous-value">{{ $counseling->followup_focus ?: '-' }}</div>
                </div>
            </div>
        </div>
    @endif

    <section class="csl-card">
        <div class="csl-card-header">
            <div>
                <div class="csl-card-title">
                    <i class="bi bi-list-ol"></i>
                    ลำดับการให้คำปรึกษาในครั้งนี้
                </div>
                <div class="csl-card-note">
                    อ่านจากรอบที่ 1 ต่อเนื่องตามลำดับ เพื่อเห็นว่าแต่ละรอบทำอะไร ผลเป็นอย่างไร และควรดำเนินการอะไรต่อ
                </div>
            </div>
            <span class="csl-count">{{ $roundCount }} รอบ</span>
        </div>

        <div class="csl-card-body">
            <div class="csl-round-list">
                {{-- รอบที่ 1 --}}
                <article class="csl-round-card">
                    <div class="csl-round-head">
                        <div>
                            <div class="csl-round-title">รอบที่ 1 • เริ่มการให้คำปรึกษา</div>
                            <div class="csl-round-meta">
                                {{ $thaiDate($counseling->session_date) }} • {{ $counseling->channel_label }}
                                @if(filled($counseling->location)) • {{ $counseling->location }} @endif
                            </div>
                        </div>
                        <div class="csl-actions">
                            <a href="{{ route('counseling.followup.report', [$counseling->id, 1]) }}"
                               class="csl-icon-btn csl-icon-print"
                               title="รายงานรอบที่ 1">
                                <i class="bi bi-file-earmark-text"></i>
                            </a>
                            <a href="{{ route('counseling.edit', $counseling->id) }}"
                               class="csl-icon-btn csl-icon-edit"
                               title="แก้ไขรอบที่ 1">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </div>
                    </div>
                    <div class="csl-round-body">
                        <div class="csl-round-field">
                            <div class="csl-round-label">ประเด็น</div>
                            <div class="csl-round-value">{{ $counseling->presenting_problem ?: '-' }}</div>
                        </div>
                        <div class="csl-round-field">
                            <div class="csl-round-label">การดำเนินการ / เทคนิค</div>
                            <div class="csl-round-value">{{ $counseling->interventions ?: $counseling->advice ?: '-' }}</div>
                        </div>
                        <div class="csl-round-field">
                            <div class="csl-round-label">ผล / แนวทางต่อ</div>
                            <div class="csl-round-value">{{ $counseling->outcome ?: $counseling->next_steps ?: '-' }}</div>
                        </div>
                    </div>
                </article>

                {{-- รอบที่ 2+ --}}
                @foreach ($counseling->followups as $round)
                    @php
                        $displayRound = $round->followup_no + 1;
                        $isLatest = $round->followup_no === (int) $counseling->followups->max('followup_no');
                    @endphp
                    <article class="csl-round-card">
                        <div class="csl-round-head">
                            <div>
                                <div class="csl-round-title">รอบที่ {{ $displayRound }}</div>
                                <div class="csl-round-meta">
                                    {{ $thaiDate($round->followup_date) }} • {{ $round->followup_method_label }}
                                    @if(filled($round->location)) • {{ $round->location }} @endif
                                    • ผู้ให้คำปรึกษา: {{ $round->recorder_name ?: '-' }}
                                </div>
                            </div>
                            <div class="csl-actions">
                                <a href="{{ route('counseling.followup.report', [$counseling->id, $displayRound]) }}"
                                   class="csl-icon-btn csl-icon-print"
                                   title="รายงานรอบที่ {{ $displayRound }}">
                                    <i class="bi bi-file-earmark-text"></i>
                                </a>
                                <a href="{{ route('counseling.followup.edit', $round->id) }}"
                                   class="csl-icon-btn csl-icon-edit"
                                   title="แก้ไขรอบที่ {{ $displayRound }}">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @if ($isLatest)
                                    <form action="{{ route('counseling.followup.delete', $round->id) }}"
                                          method="POST"
                                          class="d-inline js-round-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="csl-icon-btn csl-icon-delete"
                                                title="ลบรอบล่าสุด">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="csl-round-body">
                            <div class="csl-round-field">
                                <div class="csl-round-label">หัวข้อ/ประเด็นรอบนี้</div>
                                <div class="csl-round-value">{{ $round->topic ?: $round->progress ?: '-' }}</div>
                            </div>
                            <div class="csl-round-field">
                                <div class="csl-round-label">การดำเนินการ / เทคนิค</div>
                                <div class="csl-round-value">{{ $round->interventions ?: $round->advice ?: $round->additional_support ?: '-' }}</div>
                            </div>
                            <div class="csl-round-field">
                                <div class="csl-round-label">ผล / สถานะ</div>
                                <div class="csl-round-value">
                                    {{ $round->result ?: '-' }}<br>
                                    <span class="csl-status mt-1 {{ in_array($round->status, ['goal_met','referred','closed'], true) ? 'csl-status--closed' : 'csl-status--open' }}">
                                        {{ $round->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
</div>

@include('frontend.client.counseling.partials._scripts')
@endsection
