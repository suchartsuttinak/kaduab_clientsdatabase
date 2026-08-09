@php
$thaiDate = function ($date) {
    if (empty($date)) return '-';
    try { $d = \Carbon\Carbon::parse($date); return $d->format('d/m/') . ($d->year + 543); }
    catch (\Throwable $e) { return '-'; }
};
@endphp
<section class="cs-process-panel" id="counseling-process">
    <div class="cs-process-head">
        <div>
            <h2 class="cs-process-title">การให้คำปรึกษา ครั้งที่ {{ $counseling->session_no }}</h2>
            <div class="cs-process-meta">เริ่ม {{ $thaiDate($counseling->session_date) }} • ให้บริการรวม {{ $counseling->service_count }} ครั้ง • {{ $counseling->process_label }}</div>
        </div>
        <div class="cs-process-actions">
            <button type="button" class="cs-btn-light" data-bs-toggle="modal" data-bs-target="#counselingEditModal"><i class="bi bi-pencil-square"></i> แก้ไขข้อมูลเริ่มต้น</button>
            @if(!$counseling->is_closed)
                <button type="button" class="cs-btn-primary" data-bs-toggle="modal" data-bs-target="#counselingFollowupCreateModal"><i class="bi bi-plus-circle"></i> บันทึกการให้คำปรึกษาต่อเนื่อง</button>
                <button type="button" class="cs-btn-success" data-bs-toggle="modal" data-bs-target="#counselingCloseModal"><i class="bi bi-check2-circle"></i> จบการให้คำปรึกษาครั้งนี้</button>
            @endif
            <a href="{{ route('counseling.report', $counseling->id) }}" class="cs-btn-light"><i class="bi bi-printer"></i> รายงานครั้งนี้</a>
        </div>
    </div>
    <div class="cs-process-body">
        <div class="cs-detail-grid">
            <div class="cs-detail-item"><div class="cs-detail-label">สถานะ</div><div class="cs-detail-value">{{ $counseling->process_label }}</div></div>
            <div class="cs-detail-item"><div class="cs-detail-label">วันที่เริ่ม</div><div class="cs-detail-value">{{ $thaiDate($counseling->session_date) }}</div></div>
            <div class="cs-detail-item"><div class="cs-detail-label">วันที่ล่าสุด</div><div class="cs-detail-value">{{ $thaiDate($counseling->last_activity_date) }}</div></div>
            <div class="cs-detail-item"><div class="cs-detail-label">จำนวนการให้บริการ</div><div class="cs-detail-value">{{ $counseling->service_count }} ครั้ง</div></div>
        </div>

        <div class="cs-section">
            <div class="cs-section-title"><i class="bi bi-chat-left-text"></i> ประเด็นหลักของครั้งนี้</div>
            <div class="cs-text-block"><strong>ประเด็น:</strong> {{ $counseling->presenting_problem ?: '-' }}
@if($counseling->session_no > 1)
<strong>ความสัมพันธ์กับครั้งก่อน:</strong> {{ $counseling->issue_relation_label }}
@endif
<strong>เป้าหมาย:</strong> {{ $counseling->goals ?: '-' }}</div>
        </div>

        <div class="cs-section">
            <div class="cs-section-title"><i class="bi bi-diagram-3"></i> ลำดับการดำเนินการ</div>
            <div class="cs-timeline">
                <div class="cs-timeline-item">
                    <span class="cs-timeline-dot"></span>
                    <div class="cs-timeline-title">เริ่มการให้คำปรึกษา</div>
                    <div class="cs-timeline-meta">{{ $thaiDate($counseling->session_date) }} • {{ $counseling->channel_label }} • {{ $counseling->counselor_name ?: '-' }}</div>
                    <div class="cs-timeline-content">
                        <strong>การประเมิน:</strong> {{ $counseling->assessment ?: '-' }}<br>
                        @if(filled($counseling->interventions))<strong>วิธีการ/เทคนิค:</strong> {{ $counseling->interventions }}<br>@endif
                        @if(filled($counseling->advice))<strong>คำแนะนำ/การช่วยเหลือ:</strong> {{ $counseling->advice }}<br>@endif
                        @if(filled($counseling->outcome))<strong>ผล:</strong> {{ $counseling->outcome }}@endif
                    </div>
                </div>

                @foreach($counseling->followups as $followup)
                <div class="cs-timeline-item">
                    <span class="cs-timeline-dot"></span>
                    <div class="cs-timeline-title">การให้คำปรึกษาต่อเนื่อง ครั้งที่ {{ $followup->followup_no }}</div>
                    <div class="cs-timeline-meta">{{ $thaiDate($followup->followup_date) }} • {{ $followup->followup_method_label }} • {{ $followup->recorder_name ?: '-' }}</div>
                    <div class="cs-timeline-content">
                        <strong>ความคืบหน้า:</strong> {{ $followup->progress ?: '-' }}
                        @if(filled($followup->changes))<br><strong>การเปลี่ยนแปลง:</strong> {{ $followup->changes }}@endif
                        @if(filled($followup->result))<br><strong>ผล:</strong> {{ $followup->result }}@endif
                        @if(filled($followup->next_action))<br><strong>แนวทางต่อ:</strong> {{ $followup->next_action }}@endif
                        <div class="mt-2 d-flex gap-1 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#followupEditModal{{ $followup->id }}"><i class="bi bi-pencil me-1"></i>แก้ไข</button>
                            <form action="{{ route('counseling.followup.delete', $followup->id) }}" method="POST" class="d-inline js-followup-delete">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger rounded-pill"><i class="bi bi-trash3 me-1"></i>ลบ</button></form>
                        </div>
                    </div>
                </div>
                @endforeach

                @if($counseling->is_closed)
                <div class="cs-timeline-item is-closed">
                    <span class="cs-timeline-dot"></span>
                    <div class="cs-timeline-title">จบการให้คำปรึกษา ครั้งที่ {{ $counseling->session_no }}</div>
                    <div class="cs-timeline-meta">{{ $thaiDate($counseling->closed_date ?: $counseling->last_activity_date) }} • {{ $counseling->closure_type_label }}</div>
                </div>
                @endif
            </div>
        </div>

        @if($counseling->is_closed)
        <div class="cs-closure-box">
            <div class="cs-closure-title"><i class="bi bi-check-circle-fill me-1"></i> สรุปเมื่อจบกระบวนการ</div>
            <div class="cs-closure-grid">
                <div class="cs-detail-item"><div class="cs-detail-label">วันที่จบ</div><div class="cs-detail-value">{{ $thaiDate($counseling->closed_date ?: $counseling->last_activity_date) }}</div></div>
                <div class="cs-detail-item"><div class="cs-detail-label">ลักษณะการจบ</div><div class="cs-detail-value">{{ $counseling->closure_type_label }}</div></div>
                <div class="cs-detail-item"><div class="cs-detail-label">การบรรลุเป้าหมาย</div><div class="cs-detail-value">{{ $counseling->goal_achievement_label }}</div></div>
            </div>
            <div class="cs-text-block mt-3"><strong>สรุปผล:</strong> {{ $counseling->closure_summary ?: '-' }}@if(filled($counseling->final_recommendation))
<strong>แนวทาง/ข้อเสนอแนะ:</strong> {{ $counseling->final_recommendation }}@endif</div>
        </div>
        @endif
    </div>
</section>
