@extends('admin_client.admin_client')

@section('title', 'รายงานสรุปการให้คำปรึกษา')

@section('content')
@php
    $thaiMonths = [1=>'มกราคม',2=>'กุมภาพันธ์',3=>'มีนาคม',4=>'เมษายน',5=>'พฤษภาคม',6=>'มิถุนายน',7=>'กรกฎาคม',8=>'สิงหาคม',9=>'กันยายน',10=>'ตุลาคม',11=>'พฤศจิกายน',12=>'ธันวาคม'];
    $thaiDate = function ($date) use ($thaiMonths) {
        if (blank($date)) return '-';
        try {
            $d = \Carbon\Carbon::parse($date);
            return $d->day . ' ' . ($thaiMonths[$d->month] ?? '') . ' ' . ($d->year + 543);
        } catch (\Throwable $e) {
            return '-';
        }
    };

    $clientName = trim((string) ($client->fullname ?? ''));
    if ($clientName === '') $clientName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
    if ($clientName === '') $clientName = '-';

    $followups = $counseling->followups ?? collect();
    $roundCount = 1 + $followups->count();
    $lastRound = $followups->sortByDesc('followup_no')->first();
    $latestDate = $lastRound?->followup_date ?: $counseling->session_date;
    $isClosed = in_array($counseling->status, ['goal_met','referred','closed'], true);
    $closedDate = $isClosed ? $latestDate : null;
@endphp

@include('frontend.client.counseling.partials._report_styles')

<div class="csr-page">
    <div class="csr-toolbar">
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> พิมพ์รายงานรวม
        </button>
        <a href="{{ route('counseling.show', $counseling->id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> กลับ
        </a>
    </div>

    <div class="csr-sheet">
        <div class="csr-head">
            <h1>รายงานสรุปการให้คำปรึกษา</h1>
            <h2>ครั้งที่ {{ $counseling->session_no }}</h2>
            <p>{{ $counseling->presenting_problem }}</p>
        </div>

        <table class="csr-meta">
            <tr>
                <td><span class="csr-label">ผู้รับบริการ:</span> {{ $clientName }}</td>
                <td><span class="csr-label">เลขทะเบียน:</span> {{ $client->register_number ?? '-' }}</td>
            </tr>
            <tr>
                <td><span class="csr-label">วันที่เริ่ม:</span> {{ $thaiDate($counseling->session_date) }}</td>
                <td><span class="csr-label">วันที่สิ้นสุด:</span> {{ $isClosed ? $thaiDate($closedDate) : '-' }}</td>
            </tr>
            <tr>
                <td><span class="csr-label">ผู้ให้คำปรึกษาหลัก:</span> {{ $counseling->counselor_name ?: '-' }}</td>
                <td><span class="csr-label">สถานะปัจจุบัน:</span> {{ $counseling->status_label }}</td>
            </tr>
        </table>

        <div class="csr-summary">
            <div class="csr-summary-item">
                <div class="csr-summary-label">จำนวนรอบ</div>
                <div class="csr-summary-value">{{ $roundCount }} รอบ</div>
            </div>
            <div class="csr-summary-item">
                <div class="csr-summary-label">วันที่เริ่ม</div>
                <div class="csr-summary-value">{{ $thaiDate($counseling->session_date) }}</div>
            </div>
            <div class="csr-summary-item">
                <div class="csr-summary-label">รอบล่าสุด</div>
                <div class="csr-summary-value">รอบที่ {{ $roundCount }} • {{ $thaiDate($latestDate) }}</div>
            </div>
            <div class="csr-summary-item">
                <div class="csr-summary-label">ผลสุดท้าย / สถานะ</div>
                <div class="csr-summary-value">{{ $counseling->status_label }}</div>
            </div>
        </div>

        <div class="csr-section">
            <div class="csr-section-title">สารบัญรอบการให้คำปรึกษา</div>
            <table class="csr-index-table">
                <thead>
                    <tr>
                        <th style="width:9%;">รอบที่</th>
                        <th style="width:17%;">วันที่</th>
                        <th>ประเด็นที่ดำเนินการ</th>
                        <th style="width:20%;">สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">1</td>
                        <td>{{ $thaiDate($counseling->session_date) }}</td>
                        <td>{{ $counseling->presenting_problem ?: '-' }}</td>
                        <td>{{ $followups->isEmpty() ? $counseling->status_label : 'เริ่มต้นกระบวนการ' }}</td>
                    </tr>
                    @foreach ($followups as $round)
                        <tr>
                            <td class="text-center">{{ $round->followup_no + 1 }}</td>
                            <td>{{ $thaiDate($round->followup_date) }}</td>
                            <td>{{ $round->topic ?: $round->progress ?: '-' }}</td>
                            <td>{{ $round->status_label }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="csr-section">
            <div class="csr-section-title">ภาพรวมเมื่อเริ่มการให้คำปรึกษา</div>
            <div class="csr-field">
                <div class="csr-field-title">ประเด็นหลัก</div>
                <div class="csr-field-value">{{ $counseling->presenting_problem ?: '-' }}</div>
            </div>
            <div class="csr-field">
                <div class="csr-field-title">การประเมินเบื้องต้น</div>
                <div class="csr-field-value">{{ $counseling->assessment ?: '-' }}</div>
            </div>
            @if(filled($counseling->strengths_resources))
                <div class="csr-field">
                    <div class="csr-field-title">จุดแข็ง / ทรัพยากรสนับสนุน</div>
                    <div class="csr-field-value">{{ $counseling->strengths_resources }}</div>
                </div>
            @endif
            @if(filled($counseling->goals))
                <div class="csr-field">
                    <div class="csr-field-title">เป้าหมายของการให้คำปรึกษา</div>
                    <div class="csr-field-value">{{ $counseling->goals }}</div>
                </div>
            @endif
        </div>

        {{-- รอบที่ 1 --}}
        <article class="csr-round">
            <div class="csr-round-head">
                <div>
                    <div class="csr-round-title">รอบที่ 1 • เริ่มการให้คำปรึกษา</div>
                    <div class="csr-round-meta">
                        {{ $thaiDate($counseling->session_date) }} • {{ $counseling->channel_label }}
                        @if(filled($counseling->location)) • {{ $counseling->location }} @endif
                        • ผู้ให้คำปรึกษา: {{ $counseling->counselor_name ?: '-' }}
                    </div>
                </div>
                <span class="csr-round-status">{{ $followups->isEmpty() ? $counseling->status_label : 'เริ่มต้นกระบวนการ' }}</span>
            </div>
            <div class="csr-round-body">
                <div class="csr-grid-2">
                    <div class="csr-field">
                        <div class="csr-field-title">ประเด็นที่ดำเนินการ</div>
                        <div class="csr-field-value">{{ $counseling->presenting_problem ?: '-' }}</div>
                    </div>
                    <div class="csr-field">
                        <div class="csr-field-title">การประเมิน</div>
                        <div class="csr-field-value">{{ $counseling->assessment ?: '-' }}</div>
                    </div>
                    <div class="csr-field">
                        <div class="csr-field-title">แนวทาง / เทคนิคที่ใช้</div>
                        <div class="csr-field-value">{{ $counseling->interventions ?: '-' }}</div>
                    </div>
                    <div class="csr-field">
                        <div class="csr-field-title">คำแนะนำ / การช่วยเหลือ</div>
                        <div class="csr-field-value">{{ $counseling->advice ?: '-' }}</div>
                    </div>
                    <div class="csr-field">
                        <div class="csr-field-title">ผลที่เกิดขึ้น</div>
                        <div class="csr-field-value">{{ $counseling->outcome ?: '-' }}</div>
                    </div>
                    <div class="csr-field">
                        <div class="csr-field-title">แนวทางต่อ</div>
                        <div class="csr-field-value">{{ $counseling->next_steps ?: $counseling->followup_focus ?: '-' }}</div>
                    </div>
                </div>
                <div class="csr-field mb-0">
                    <div class="csr-field-title">การประเมินความเสี่ยง</div>
                    <div class="csr-field-value">
                        {{ $counseling->risk_level_label }}
                        @if(filled($counseling->risk_detail)) — {{ $counseling->risk_detail }} @endif
                    </div>
                </div>
            </div>
        </article>

        {{-- รอบที่ 2+ --}}
        @foreach ($followups as $round)
            <article class="csr-round">
                <div class="csr-round-head">
                    <div>
                        <div class="csr-round-title">
                            รอบที่ {{ $round->followup_no + 1 }} • {{ $round->topic ?: 'การให้คำปรึกษาต่อเนื่อง' }}
                        </div>
                        <div class="csr-round-meta">
                            {{ $thaiDate($round->followup_date) }} • {{ $round->followup_method_label }}
                            @if(filled($round->location)) • {{ $round->location }} @endif
                            • ผู้ให้คำปรึกษา: {{ $round->recorder_name ?: '-' }}
                        </div>
                    </div>
                    <span class="csr-round-status">{{ $round->status_label }}</span>
                </div>

                <div class="csr-round-body">
                    <div class="csr-grid-2">
                        <div class="csr-field">
                            <div class="csr-field-title">หัวข้อ / ประเด็นที่ดำเนินการ</div>
                            <div class="csr-field-value">{{ $round->topic ?: $round->progress ?: '-' }}</div>
                        </div>
                        <div class="csr-field">
                            <div class="csr-field-title">สรุปความคืบหน้า / สาระสำคัญ</div>
                            <div class="csr-field-value">{{ $round->progress ?: '-' }}</div>
                        </div>
                        <div class="csr-field">
                            <div class="csr-field-title">สภาพปัจจุบัน / การประเมิน</div>
                            <div class="csr-field-value">{{ $round->current_assessment ?: '-' }}</div>
                        </div>
                        @if(filled($round->changes))
                            <div class="csr-field">
                                <div class="csr-field-title">การเปลี่ยนแปลงจากรอบก่อน</div>
                                <div class="csr-field-value">{{ $round->changes }}</div>
                            </div>
                        @endif
                        @if(filled($round->barriers))
                            <div class="csr-field">
                                <div class="csr-field-title">ปัญหา / อุปสรรค</div>
                                <div class="csr-field-value">{{ $round->barriers }}</div>
                            </div>
                        @endif
                        <div class="csr-field">
                            <div class="csr-field-title">เป้าหมายของรอบนี้</div>
                            <div class="csr-field-value">{{ $round->session_goal ?: '-' }}</div>
                        </div>
                        <div class="csr-field">
                            <div class="csr-field-title">แนวทาง / เทคนิคที่ใช้</div>
                            <div class="csr-field-value">{{ $round->interventions ?: '-' }}</div>
                        </div>
                        <div class="csr-field">
                            <div class="csr-field-title">คำแนะนำ / การช่วยเหลือ</div>
                            <div class="csr-field-value">{{ $round->advice ?: $round->additional_support ?: '-' }}</div>
                        </div>
                        <div class="csr-field">
                            <div class="csr-field-title">ผลที่เกิดขึ้น</div>
                            <div class="csr-field-value">{{ $round->result ?: '-' }}</div>
                        </div>
                    </div>

                    @if(filled($round->agreement))
                        <div class="csr-field">
                            <div class="csr-field-title">ข้อตกลงร่วมกัน</div>
                            <div class="csr-field-value">{{ $round->agreement }}</div>
                        </div>
                    @endif

                    <div class="csr-field">
                        <div class="csr-field-title">การประเมินความเสี่ยง</div>
                        <div class="csr-field-value">
                            {{ $round->risk_level_label }}
                            @if(filled($round->risk_detail)) — {{ $round->risk_detail }} @endif
                        </div>
                    </div>

                    @if(filled($round->next_action))
                        <div class="csr-field mb-0">
                            <div class="csr-field-title">แนวทางต่อ / ข้อเสนอแนะ</div>
                            <div class="csr-field-value">{{ $round->next_action }}</div>
                        </div>
                    @endif
                </div>
            </article>
        @endforeach

        <div class="csr-section">
            <div class="csr-section-title">สรุปสถานะของการให้คำปรึกษาครั้งนี้</div>
            @if($isClosed)
                <div class="csr-next">
                    <div class="csr-field">
                        <div class="csr-field-title">สถานะสิ้นสุด</div>
                        <div class="csr-field-value">{{ $counseling->status_label }}</div>
                    </div>
                    <div class="csr-field">
                        <div class="csr-field-title">วันที่สิ้นสุด</div>
                        <div class="csr-field-value">{{ $thaiDate($closedDate) }}</div>
                    </div>
                    <div class="csr-field mb-0">
                        <div class="csr-field-title">ผล / ข้อเสนอแนะจากรอบสุดท้าย</div>
                        <div class="csr-field-value">
                            {{ $lastRound?->result ?: $counseling->outcome ?: '-' }}
                            @if(filled($lastRound?->next_action ?: $counseling->next_steps))
                                <br><strong>ข้อเสนอแนะ:</strong> {{ $lastRound?->next_action ?: $counseling->next_steps }}
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="csr-next">
                    <div class="csr-field">
                        <div class="csr-field-title">สถานะปัจจุบัน</div>
                        <div class="csr-field-value">{{ $counseling->status_label }}</div>
                    </div>
                    <div class="csr-field">
                        <div class="csr-field-title">นัดหมายรอบถัดไป</div>
                        <div class="csr-field-value">{{ $thaiDate($counseling->next_appointment_date) }}</div>
                    </div>
                    <div class="csr-field mb-0">
                        <div class="csr-field-title">สิ่งที่ควรดำเนินการต่อ</div>
                        <div class="csr-field-value">{{ $counseling->followup_focus ?: $lastRound?->next_action ?: '-' }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
