@extends('admin_client.admin_client')

@section('content')

<style>

    .bs-card{
        border:none;
        border-radius:18px;
        overflow:hidden;
        box-shadow:0 10px 30px rgba(0,0,0,.06);
        margin-bottom:24px;
    }

    .bs-header{
        background:linear-gradient(135deg,#2563eb,#1d4ed8);
        color:#fff;
        padding:24px;
    }

    .bs-title{
        font-size:1.5rem;
        font-weight:700;
        margin-bottom:6px;
    }

    .bs-subtitle{
        opacity:.9;
    }

    .bs-section{
        border-radius:16px;
        border:1px solid #e5e7eb;
        overflow:hidden;
        margin-bottom:24px;
        background:#fff;
    }

    .bs-section-head{
        padding:16px 20px;
        font-weight:700;
        color:#fff;
        font-size:1rem;
    }

    .bs-learning{
        background:#2563eb;
    }

    .bs-ld{
        background:#0891b2;
    }

    .bs-adhd{
        background:#d97706;
    }

    .bs-autism{
        background:#dc2626;
    }

    .bs-question{
        padding:18px 20px;
        border-bottom:1px solid #f1f5f9;
    }

    .bs-question:last-child{
        border-bottom:none;
    }

    .bs-question-title{
        font-weight:600;
        margin-bottom:12px;
        line-height:1.7;
    }

    .bs-radio-group{
        display:flex;
        gap:20px;
        flex-wrap:wrap;
    }

    .bs-footer{
        position:sticky;
        bottom:0;
        background:#fff;
        padding:18px;
        border-top:1px solid #e5e7eb;
        z-index:100;
    }

    .bs-save-btn{
        border-radius:12px;
        padding:12px 26px;
        font-weight:700;
        font-size:1rem;
    }

</style>

<div class="container-fluid py-4">

    <div class="bs-card">

        <div class="bs-header">

            <div class="bs-title">
                แบบสังเกตพฤติกรรม 4 โรค
            </div>

            <div class="bs-subtitle">
                ระบบคัดกรองพฤติกรรมเบื้องต้น
                 ( สถาบันราชานุกูล กรมสุขภาพจิต )
            </div>
           

        </div>

        <div class="p-4">

            <div class="row mb-4">

                <div class="col-md-6 mb-3">
                    <div class="fw-bold">ชื่อผู้รับบริการ</div>

                    <div class="text-muted">
                        {{ $client->first_name }} {{ $client->last_name }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="fw-bold">เลขทะเบียน</div>

                    <div class="text-muted">
                        {{ $client->register_number ?? '-' }}
                    </div>
                </div>

            </div>

            @if ($errors->any())

                <div class="alert alert-danger">

                    <ul class="mb-0">

                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </div>

            @endif

            <form action="{{ route('behavior-screenings.store', $client->id) }}"
                  method="POST">

                @csrf

           <div class="row mb-4">

            <div class="col-lg-3 mb-3">

                <label class="form-label fw-bold">
                    วันที่ประเมิน <span class="text-danger">*</span>
                </label>

                <input type="date"
                    name="screening_date"
                    value="{{ old('screening_date', now('Asia/Bangkok')->toDateString()) }}"
                    max="{{ now('Asia/Bangkok')->toDateString() }}"
                    class="form-control @error('screening_date') is-invalid @enderror">

                @error('screening_date')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

    <div class="col-lg-3 mb-3">

        <label class="form-label fw-bold">
            ผู้ประเมิน
        </label>

        <input type="text"
               name="observer_name"
               value="{{ old('observer_name', auth()->user()->name ?? '') }}"
               class="form-control">

    </div>

    <div class="col-lg-3 mb-3">

        <label class="form-label fw-bold">
            อายุ
        </label>

        <input type="text"
               class="form-control bg-light"
               value="{{ $client->birth_date ? \Carbon\Carbon::parse($client->birth_date)->age . ' ปี' : '-' }}"
               readonly>

        <input type="hidden"
               name="age_text"
               value="{{ $client->birth_date ? \Carbon\Carbon::parse($client->birth_date)->age . ' ปี' : '-' }}">

         </div>

                    <div class="col-lg-3 mb-3">

                        <label class="form-label fw-bold">
                            ชั้นเรียน
                        </label>

                        <input type="text"
                            class="form-control bg-light"
                            value="{{ $classLevel ?: '-' }}"
                            readonly>

                    </div>

                    </div>

                @php
                    $sectionClasses = [
                        'learning' => 'bs-learning',
                        'ld' => 'bs-ld',
                        'adhd' => 'bs-adhd',
                        'autism' => 'bs-autism',
                    ];

                    $sectionTitles = [
                        'learning' => 'ภาวะเรียนรู้ช้า',
                        'ld' => 'ภาวะแอลดี (LD)',
                        'adhd' => 'ภาวะสมาธิสั้น',
                        'autism' => 'ภาวะออทิสติก',
                    ];
                @endphp

                @foreach($questions as $category => $items)

                    <div class="bs-section">

                        <div class="bs-section-head {{ $sectionClasses[$category] }}">
                            {{ $sectionTitles[$category] }}
                        </div>

                        @foreach($items as $itemNo => $question)

                            <div class="bs-question">

                                <div class="bs-question-title">
                                    {{ $itemNo }}. {{ $question }}
                                </div>

                                <div class="bs-radio-group">

                                    <div class="form-check">

                                        <input class="form-check-input"
                                               type="radio"
                                               name="answers[{{ $category }}][{{ $itemNo }}]"
                                               value="1"
                                               id="yes_{{ $category }}_{{ $itemNo }}"
                                               {{ old("answers.$category.$itemNo") == 1 ? 'checked' : '' }}>

                                        <label class="form-check-label"
                                               for="yes_{{ $category }}_{{ $itemNo }}">
                                            ใช่
                                        </label>

                                    </div>

                                    <div class="form-check">

                                        <input class="form-check-input"
                                               type="radio"
                                               name="answers[{{ $category }}][{{ $itemNo }}]"
                                               value="0"
                                               id="no_{{ $category }}_{{ $itemNo }}"
                                               {{ old("answers.$category.$itemNo") === "0" ? 'checked' : '' }}>

                                        <label class="form-check-label"
                                               for="no_{{ $category }}_{{ $itemNo }}">
                                            ไม่ใช่
                                        </label>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @endforeach

                <div class="mb-5">

                    <label class="form-label fw-bold">
                        หมายเหตุเพิ่มเติม
                    </label>

                    <textarea name="remark"
                              rows="4"
                              class="form-control">{{ old('remark') }}</textarea>

                </div>

                <div class="bs-footer d-flex justify-content-between align-items-center flex-wrap gap-2">

                    <a href="{{ route('behavior-screenings.index', $client->id) }}"
                       class="btn btn-light border">
                        <i class="bi bi-arrow-left"></i>
                        กลับ
                    </a>

                    <button type="submit"
                            class="btn btn-primary bs-save-btn">
                        <i class="bi bi-save"></i>
                        บันทึกแบบประเมิน
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection