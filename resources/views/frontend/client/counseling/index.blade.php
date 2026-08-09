@extends('admin_client.admin_client')

@section('title', 'การให้คำปรึกษา')

@section('content')
@php
    $clientDisplayName = trim((string) ($client->fullname ?? ''));
    if ($clientDisplayName === '') {
        $clientDisplayName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
    }
    if ($clientDisplayName === '') $clientDisplayName = '-';

    $clientAgeText = '-';
    if (!empty($client->birth_date)) {
        try {
            $birth = \Carbon\Carbon::parse($client->birth_date)->startOfDay();
            $diff = $birth->diff(\Carbon\Carbon::today('Asia/Bangkok'));
            $parts = [];
            if ($diff->y > 0) $parts[] = $diff->y . ' ปี';
            if ($diff->m > 0) $parts[] = $diff->m . ' เดือน';
            if ($diff->y === 0 && $diff->m === 0) $parts[] = $diff->d . ' วัน';
            $clientAgeText = implode(' ', $parts) ?: '0 วัน';
        } catch (\Throwable $e) {
            $clientAgeText = '-';
        }
    }

    $counselingCount = $counselings->count();
    $nextSessionNo = ((int) $counselings->max('session_no')) + 1;
    $latestCounseling = $counselings->sortByDesc('session_no')->first();
    $hasOpenCounseling = $latestCounseling
        && in_array($latestCounseling->status, ['ongoing', 'follow_up', 'improved'], true);
    $canStartNewCounseling = !$latestCounseling || !$hasOpenCounseling;
@endphp

@include('frontend.client.counseling.partials._styles')

<div class="container-fluid csl-page">
    @include('frontend.client.counseling.partials._header')

    @if ($errors->any() && old('_form_context') !== 'create')
        <div class="alert alert-danger rounded-3 small">
            <strong>กรุณาตรวจสอบข้อมูล</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="csl-card">
        <div class="csl-card-header">
            <div>
                <div class="csl-card-title">
                    <i class="bi bi-journal-text"></i>
                    ประวัติการให้คำปรึกษา
                </div>
                <div class="csl-card-note">
                    “ครั้งที่” คือกระบวนการหนึ่งเรื่องตั้งแต่เริ่มจนจบ ส่วน “รอบที่” คือการให้คำปรึกษาแต่ละครั้งภายในกระบวนการนั้น
                </div>
            </div>

            @if ($counselingCount > 0)
                <span class="csl-count">{{ $counselingCount }} ครั้ง</span>
            @endif
        </div>

        <div class="csl-card-body">
            @if ($counselingCount > 0)
                @include('frontend.client.counseling.partials._table')
            @else
                @include('frontend.client.counseling.partials._empty')
            @endif
        </div>
    </section>
</div>

@include('frontend.client.counseling.partials._create_modal')
@include('frontend.client.counseling.partials._scripts')
@endsection
