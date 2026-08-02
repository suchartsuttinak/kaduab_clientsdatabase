@extends('admin_client.admin_client')

@section('content')
<link rel="stylesheet" href="{{ asset('backend/assets/css/escape.css') }}">
<link rel="stylesheet" href="{{ asset('backend/assets/css/escape-responsive-fix.css') }}">

@php
    $isCopy = ($mode ?? 'create') === 'copy';
    $defaultRetireDate = $isCopy
        ? now('Asia/Bangkok')->toDateString()
        : ($escape->retire_date?->format('Y-m-d') ?? now('Asia/Bangkok')->toDateString());
@endphp

<div class="container-fluid escape-create-page">
    <div class="escape-create-shell">
        <form action="{{ route('escape.store') }}" method="POST" class="escape-create-card escape-submit-form" novalidate>
            @csrf
            <input type="hidden" name="client_id" value="{{ $client->id }}">
            <input type="hidden" name="form_context" value="escape-create-page">

            <div class="escape-create-head">
                <div class="escape-create-head__icon">
                    <i class="bi {{ $isCopy ? 'bi-copy' : 'bi-box-arrow-right' }}"></i>
                </div>
                <div class="min-w-0">
                    <h5 class="escape-create-title">
                        {{ $isCopy ? 'คัดลอกข้อมูลการออก/หลบหนี' : 'เพิ่มข้อมูลการออก/หลบหนี' }}
                    </h5>
                    <div class="escape-create-subtitle text-break">
                        ผู้รับบริการ: {{ $client->fullname ?? '-' }}
                    </div>
                </div>
            </div>

            <div class="escape-create-body">
                @if ($isCopy)
                    <div class="alert alert-info border-0 mb-4">
                        <i class="bi bi-info-circle me-1"></i>
                        ระบบนำประเภทและรายละเอียดจากรายการเดิมมาเป็นต้นแบบ กรุณาตรวจสอบวันที่ก่อนบันทึกรายการใหม่
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold">วันที่ออก/หลบหนี <span class="text-danger">*</span></label>
                        <input type="date"
                               name="retire_date"
                               class="form-control @error('retire_date') is-invalid @enderror"
                               value="{{ old('retire_date', $defaultRetireDate) }}"
                               max="{{ now('Asia/Bangkok')->toDateString() }}"
                               required>
                        @error('retire_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-8">
                        <label class="form-label fw-bold">ประเภทการออก/หลบหนี <span class="text-danger">*</span></label>
                        <select name="retire_id" class="form-select @error('retire_id') is-invalid @enderror" required>
                            <option value="">-- เลือกประเภทการออก/หลบหนี --</option>
                            @foreach ($retires as $ret)
                                <option value="{{ $ret->id }}"
                                    {{ (string) old('retire_id', $escape->retire_id ?? '') === (string) $ret->id ? 'selected' : '' }}>
                                    {{ $ret->retire_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('retire_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">พฤติการณ์ / สาเหตุ</label>
                        <textarea name="stories"
                                  class="form-control @error('stories') is-invalid @enderror"
                                  rows="6"
                                  maxlength="5000"
                                  placeholder="บันทึกรายละเอียดสาเหตุหรือเรื่องราวเพิ่มเติม">{{ old('stories', $escape->stories ?? '') }}</textarea>
                        @error('stories')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="escape-create-footer">
                <a href="{{ route('escape.index', $client->id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>กลับหน้ารายการ</span>
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save"></i>
                    <span>บันทึกข้อมูล</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.escape-submit-form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const button = form.querySelector('button[type="submit"]');
            if (!button || button.disabled) return;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span>กำลังบันทึก...</span>';
        });
    });
});
</script>
@endsection
