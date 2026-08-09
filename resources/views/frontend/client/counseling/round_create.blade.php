@extends('admin_client.admin_client')

@section('title', 'บันทึกการให้คำปรึกษารอบต่อเนื่อง')

@section('content')
@php
    $clientName = trim((string) ($client->fullname ?? ''));
    if ($clientName === '') {
        $clientName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
    }
    if ($clientName === '') $clientName = '-';
@endphp

@include('frontend.client.counseling.partials._styles')

<div class="container-fluid csl-page">
    <header class="csl-header">
        <div class="csl-header-main">
            <div class="csl-header-icon"><i class="bi bi-chat-dots"></i></div>
            <div>
                <h1 class="csl-title">
                    การให้คำปรึกษา ครั้งที่ {{ $counseling->session_no }} • รอบที่ {{ $nextRoundNo }}
                </h1>
                <div class="csl-subtitle">
                    <span>ผู้รับบริการ: <strong>{{ $clientName }}</strong></span>
                    <span class="csl-dot">•</span>
                    <span>ประเด็นหลัก: <strong>{{ \Illuminate\Support\Str::limit($counseling->presenting_problem, 80) }}</strong></span>
                </div>
            </div>
        </div>

        <div class="csl-header-actions">
            <a href="{{ route('counseling.show', $counseling->id) }}" class="csl-btn-secondary">
                <i class="bi bi-arrow-left"></i> กลับ
            </a>
        </div>
    </header>

    @include('frontend.client.counseling.partials._previous_round')

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

    <form action="{{ route('counseling.followup.store') }}" method="POST" class="csl-form-card">
        @csrf
        <input type="hidden" name="counseling_id" value="{{ $counseling->id }}">

        <div class="csl-form-header">
            <div class="csl-card-title">
                <i class="bi bi-pencil-square"></i>
                บันทึกข้อมูลรอบที่ {{ $nextRoundNo }}
            </div>
            <div class="csl-card-note">
                บันทึกให้ครบว่ารอบนี้ดำเนินการเรื่องใด ใช้วิธีใด เกิดผลอย่างไร และรอบถัดไปควรทำอะไรต่อ
            </div>
        </div>

        <div class="csl-form-body">
            @php
                $formMode = 'create';
                $roundNo = $nextRoundNo;
                $round = null;
                $isLatestRound = true;
            @endphp
            @include('frontend.client.counseling.partials._round_fields')
        </div>

        <div class="csl-form-footer">
            <a href="{{ route('counseling.show', $counseling->id) }}" class="csl-btn-secondary">
                <i class="bi bi-x-circle"></i> ยกเลิก
            </a>
            <button type="submit" class="csl-btn-primary js-submit-once">
                <i class="bi bi-check-circle"></i> บันทึกรอบที่ {{ $nextRoundNo }}
            </button>
        </div>
    </form>
</div>

@include('frontend.client.counseling.partials._scripts')
@endsection
