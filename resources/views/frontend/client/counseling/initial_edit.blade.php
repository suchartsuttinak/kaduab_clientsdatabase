@extends('admin_client.admin_client')

@section('title', 'แก้ไขการให้คำปรึกษารอบที่ 1')

@section('content')
@php
    $clientName = trim((string) ($client->fullname ?? ''));
    if ($clientName === '') {
        $clientName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
    }
    if ($clientName === '') $clientName = '-';
    $nextSessionNo = $counseling->session_no;
@endphp

@include('frontend.client.counseling.partials._styles')

<div class="container-fluid csl-page">
    <header class="csl-header">
        <div class="csl-header-main">
            <div class="csl-header-icon"><i class="bi bi-pencil-square"></i></div>
            <div>
                <h1 class="csl-title">
                    แก้ไขการให้คำปรึกษา ครั้งที่ {{ $counseling->session_no }} • รอบที่ 1
                </h1>
                <div class="csl-subtitle">
                    <span>ผู้รับบริการ: <strong>{{ $clientName }}</strong></span>
                </div>
            </div>
        </div>

        <div class="csl-header-actions">
            <a href="{{ route('counseling.followup.report', [$counseling->id, 1]) }}" class="csl-btn-outline">
                <i class="bi bi-file-earmark-text"></i> รายงานรอบที่ 1
            </a>
            <a href="{{ route('counseling.show', $counseling->id) }}" class="csl-btn-secondary">
                <i class="bi bi-arrow-left"></i> กลับ
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

    <form action="{{ route('counseling.update', $counseling->id) }}" method="POST" class="csl-form-card">
        @csrf
        @method('PUT')

        <div class="csl-form-header">
            <div class="csl-card-title">
                <i class="bi bi-chat-heart"></i>
                ข้อมูลรอบที่ 1
            </div>
            <div class="csl-card-note">
                รอบที่ 1 เป็นข้อมูลตั้งต้นของประเด็นหลัก การประเมินเบื้องต้น และเป้าหมายของการให้คำปรึกษาครั้งนี้
            </div>
        </div>

        <div class="csl-form-body">
            @php($formMode = 'edit')
            @include('frontend.client.counseling.partials._initial_fields')
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
