@extends('admin_client.admin_client')

@section('title', 'รายงานการให้คำปรึกษารายรอบ')

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

    $isFirst = $roundNo === 1;
    $date = $isFirst ? $counseling->session_date : $round->followup_date;
    $counselorName = $isFirst ? $counseling->counselor_name : $round->recorder_name;
    $methodLabel = $isFirst ? $counseling->channel_label : $round->followup_method_label;
    $location = $isFirst ? $counseling->location : $round->location;
    $topic = $isFirst ? $counseling->presenting_problem : ($round->topic ?: $round->progress);
    $progress = $isFirst ? null : $round->progress;
    $assessment = $isFirst ? $counseling->assessment : $round->current_assessment;
    $changes = $isFirst ? null : $round->changes;
    $barriers = $isFirst ? null : $round->barriers;
    $goal = $isFirst ? $counseling->goals : $round->session_goal;
    $interventions = $isFirst ? $counseling->interventions : $round->interventions;
    $advice = $isFirst ? $counseling->advice : $round->advice;
    $agreement = $isFirst ? $counseling->agreement : $round->agreement;
    $support = $isFirst ? null : $round->additional_support;
    $result = $isFirst ? $counseling->outcome : $round->result;
    $riskLabel = $isFirst ? $counseling->risk_level_label : $round->risk_level_label;
    $riskDetail = $isFirst ? $counseling->risk_detail : $round->risk_detail;
    $nextAction = $isFirst ? ($counseling->next_steps ?: $counseling->followup_focus) : $round->next_action;
    $statusLabel = $isFirst ? $counseling->status_label : $round->status_label;
    $nextAppointment = $isFirst ? $counseling->next_appointment_date : $round->next_appointment_date;
    $isOpen = $isFirst
        ? in_array($counseling->status, ['ongoing','follow_up','improved'], true)
        : in_array($round->status, ['ongoing','follow_up','improved'], true);
@endphp

@include('frontend.client.counseling.partials._report_styles')

<div class="csr-page">
    <div class="csr-toolbar">
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> พิมพ์รายงาน
        </button>
        <a href="{{ route('counseling.show', $counseling->id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> กลับ
        </a>
    </div>

    <div class="csr-sheet">
        <div class="csr-head">
            <h1>รายงานการให้คำปรึกษา</h1>
            <h2>ครั้งที่ {{ $counseling->session_no }} • รอบที่ {{ $roundNo }}</h2>
            <p>{{ $topic }}</p>
        </div>

        <table class="csr-meta">
            <tr>
                <td><span class="csr-label">ผู้รับบริการ:</span> {{ $clientName }}</td>
                <td><span class="csr-label">เลขทะเบียน:</span> {{ $client->register_number ?? '-' }}</td>
            </tr>
            <tr>
                <td><span class="csr-label">วันที่:</span> {{ $thaiDate($date) }}</td>
                <td><span class="csr-label">ผู้ให้คำปรึกษา:</span> {{ $counselorName ?: '-' }}</td>
            </tr>
            <tr>
                <td><span class="csr-label">ช่องทาง:</span> {{ $methodLabel }}</td>
                <td><span class="csr-label">สถานที่:</span> {{ $location ?: '-' }}</td>
            </tr>
            <tr>
                <td colspan="2"><span class="csr-label">สถานะหลังจบรอบ:</span> {{ $statusLabel }}</td>
            </tr>
        </table>

        <div class="csr-section">
            <div class="csr-section-title">1. ประเด็นและการประเมิน</div>
            <div class="csr-field">
                <div class="csr-field-title">หัวข้อ / ประเด็นที่ดำเนินการ</div>
                <div class="csr-field-value">{{ $topic ?: '-' }}</div>
            </div>
            @if(filled($progress))
                <div class="csr-field">
                    <div class="csr-field-title">สรุปความคืบหน้า / สาระสำคัญ</div>
                    <div class="csr-field-value">{{ $progress }}</div>
                </div>
            @endif
            <div class="csr-field">
                <div class="csr-field-title">สภาพปัจจุบัน / การประเมิน</div>
                <div class="csr-field-value">{{ $assessment ?: '-' }}</div>
            </div>
            @if(filled($changes))
                <div class="csr-field">
                    <div class="csr-field-title">การเปลี่ยนแปลงจากรอบก่อน</div>
                    <div class="csr-field-value">{{ $changes }}</div>
                </div>
            @endif
            @if(filled($barriers))
                <div class="csr-field">
                    <div class="csr-field-title">ปัญหา / อุปสรรค</div>
                    <div class="csr-field-value">{{ $barriers }}</div>
                </div>
            @endif
        </div>

        <div class="csr-section">
            <div class="csr-section-title">2. การดำเนินการให้คำปรึกษา</div>
            <div class="csr-grid-2">
                <div class="csr-field">
                    <div class="csr-field-title">เป้าหมายของรอบนี้</div>
                    <div class="csr-field-value">{{ $goal ?: '-' }}</div>
                </div>
                <div class="csr-field">
                    <div class="csr-field-title">แนวทาง / เทคนิคที่ใช้</div>
                    <div class="csr-field-value">{{ $interventions ?: '-' }}</div>
                </div>
                <div class="csr-field">
                    <div class="csr-field-title">คำแนะนำ / การช่วยเหลือ</div>
                    <div class="csr-field-value">{{ $advice ?: '-' }}</div>
                </div>
                <div class="csr-field">
                    <div class="csr-field-title">ข้อตกลงร่วมกัน</div>
                    <div class="csr-field-value">{{ $agreement ?: '-' }}</div>
                </div>
            </div>
            @if(filled($support))
                <div class="csr-field">
                    <div class="csr-field-title">การช่วยเหลือเพิ่มเติม / การประสานงาน</div>
                    <div class="csr-field-value">{{ $support }}</div>
                </div>
            @endif
        </div>

        <div class="csr-section">
            <div class="csr-section-title">3. ผลการให้คำปรึกษา</div>
            <div class="csr-field">
                <div class="csr-field-title">ผลที่เกิดขึ้น</div>
                <div class="csr-field-value">{{ $result ?: '-' }}</div>
            </div>
            <div class="csr-field">
                <div class="csr-field-title">การประเมินความเสี่ยง</div>
                <div class="csr-field-value">
                    {{ $riskLabel ?: '-' }}
                    @if(filled($riskDetail)) — {{ $riskDetail }} @endif
                </div>
            </div>
            @if(filled($nextAction))
                <div class="csr-field">
                    <div class="csr-field-title">แนวทางต่อ / ข้อเสนอแนะ</div>
                    <div class="csr-field-value">{{ $nextAction }}</div>
                </div>
            @endif
        </div>

        <div class="csr-next">
            <div class="csr-field mb-0">
                <div class="csr-field-title">สถานะหลังจบรอบนี้</div>
                <div class="csr-field-value">{{ $statusLabel }}</div>
            </div>
            @if($isOpen)
                <div class="csr-field mb-0 mt-2">
                    <div class="csr-field-title">นัดหมายครั้งต่อไป</div>
                    <div class="csr-field-value">{{ $thaiDate($nextAppointment) }}</div>
                </div>
            @endif
        </div>

        <div class="csr-signature">
            <div class="csr-signature-box">
                ลงชื่อ ........................................................ ผู้ให้คำปรึกษา<br>
                ({{ $counselorName ?: '........................................................' }})<br>
                วันที่ {{ $thaiDate($date) }}
            </div>
        </div>
    </div>
</div>
@endsection
