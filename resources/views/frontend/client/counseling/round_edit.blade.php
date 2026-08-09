@extends('admin_client.admin_client')

@section('title', 'แก้ไขการให้คำปรึกษารอบต่อเนื่อง')

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
            <div class="csl-header-icon"><i class="bi bi-pencil-square"></i></div>
            <div>
                <h1 class="csl-title">
                    แก้ไขการให้คำปรึกษา ครั้งที่ {{ $counseling->session_no }} • รอบที่ {{ $roundNo }}
                </h1>
                <div class="csl-subtitle">
                    <span>ผู้รับบริการ: <strong>{{ $clientName }}</strong></span>
                    <span class="csl-dot">•</span>
                    <span>ประเด็นหลัก: <strong>{{ \Illuminate\Support\Str::limit($counseling->presenting_problem, 80) }}</strong></span>
                </div>
            </div>
        </div>

        <div class="csl-header-actions">
            <a href="{{ route('counseling.followup.report', [$counseling->id, $roundNo]) }}" class="csl-btn-outline">
                <i class="bi bi-file-earmark-text"></i> รายงานรอบนี้
            </a>
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

    <form action="{{ route('counseling.followup.update', $round->id) }}" method="POST" class="csl-form-card">
        @csrf
        @method('PUT')

        <div class="csl-form-header">
            <div class="csl-card-title">
                <i class="bi bi-pencil-square"></i>
                แก้ไขข้อมูลรอบที่ {{ $roundNo }}
            </div>
            <div class="csl-card-note">
                @if(!$isLatestRound)
                    รอบนี้มีรอบถัดไปแล้ว จึงไม่สามารถเปลี่ยนสถานะเป็นสถานะสิ้นสุดได้
                @else
                    รอบล่าสุดสามารถกำหนดสถานะต่อเนื่องหรือตัดสินใจสิ้นสุดการให้คำปรึกษาครั้งนี้ได้
                @endif
            </div>
        </div>

        <div class="csl-form-body">
            @php($formMode = 'edit')
            @include('frontend.client.counseling.partials._round_fields')
        </div>

        <div class="csl-form-footer">
            <a href="{{ route('counseling.show', $counseling->id) }}" class="csl-btn-secondary">
                <i class="bi bi-x-circle"></i> ยกเลิก
            </a>
            <button type="submit" class="csl-btn-primary js-submit-once">
                <i class="bi bi-check-circle"></i> บันทึกการแก้ไข
            </button>
        </div>
    </form>
</div>

@include('frontend.client.counseling.partials._scripts')
@endsection
