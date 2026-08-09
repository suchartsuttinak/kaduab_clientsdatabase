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

<div class="csl-table-wrap">
    <table class="csl-table">
        <thead>
            <tr>
                <th class="text-center">ครั้งที่</th>
                <th>วันที่เริ่ม</th>
                <th>ประเด็นหลัก</th>
                <th class="text-center">จำนวนรอบ</th>
                <th>รอบล่าสุด</th>
                <th>สถานะ</th>
                <th class="text-center">จัดการ</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($counselings as $item)
                @php
                    $roundCount = 1 + $item->followups->count();
                    $latestRound = $item->followups->sortByDesc('followup_no')->first();
                    $latestDate = $latestRound?->followup_date ?: $item->session_date;
                    $statusClass = match ($item->status) {
                        'ongoing', 'improved' => 'csl-status--open',
                        'follow_up' => 'csl-status--track',
                        'goal_met' => 'csl-status--goal',
                        'referred' => 'csl-status--refer',
                        'closed' => 'csl-status--closed',
                        default => 'csl-status--closed',
                    };
                @endphp

                <tr>
                    <td class="text-center fw-semibold text-primary">
                        {{ $item->session_no }}
                    </td>
                    <td>{{ $thaiDateShort($item->session_date) }}</td>
                    <td style="min-width:260px; max-width:360px;">
                        <div class="csl-clamp-2" title="{{ $item->presenting_problem }}">
                            {{ $item->presenting_problem ?: '-' }}
                        </div>
                    </td>
                    <td class="text-center">{{ $roundCount }}</td>
                    <td>
                        รอบที่ {{ $roundCount }}<br>
                        <span class="text-muted small">{{ $thaiDateShort($latestDate) }}</span>
                    </td>
                    <td>
                        <span class="csl-status {{ $statusClass }}">
                            {{ $item->status_label }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="csl-actions justify-content-center">
                            <a href="{{ route('counseling.show', $item->id) }}"
                               class="csl-icon-btn csl-icon-view"
                               title="ดูรายละเอียดและรอบทั้งหมด">
                                <i class="bi bi-eye"></i>
                            </a>

                            <a href="{{ route('counseling.report', $item->id) }}"
                               class="csl-icon-btn csl-icon-print"
                               title="รายงานรวมครั้งนี้">
                                <i class="bi bi-printer"></i>
                            </a>

                            <form action="{{ route('counseling.delete', $item->id) }}"
                                  method="POST"
                                  class="d-inline js-counseling-delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="csl-icon-btn csl-icon-delete"
                                        title="ลบข้อมูลครั้งนี้">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
