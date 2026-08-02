@extends('admin_client.admin_client')

@section('content')

@php
    $isEdit = isset($screening);

    $formAction = $isEdit
        ? route('snap-iv.update', $screening->id)
        : route('snap-iv.store', $client->id);

    $screeningDateValue = old(
        'screening_date',
        $isEdit
            ? \Carbon\Carbon::parse($screening->screening_date)->toDateString()
            : now('Asia/Bangkok')->toDateString()
    );

    $ageTextValue = $isEdit
        ? ($screening->age_text ?: '-')
        : ($client->birth_date
            ? \Carbon\Carbon::parse($client->birth_date)->age . ' ปี'
            : '-');

    $latestClassLevel = data_get($latestEducationRecord, 'education.education_name');

    // Controller ส่ง semester_label มาจาก JOIN โดยตรง
    // และยังมี relation semester เป็น fallback เพื่อรองรับข้อมูลเดิม
    $latestTerm = data_get($latestEducationRecord, 'semester_label')
        ?: data_get($latestEducationRecord, 'semester.semester_name');

    $latestGradeAverage = data_get($latestEducationRecord, 'grade_average');

    $classLevelValue = $isEdit
        ? ($screening->class_level ?: ($latestClassLevel ?: '-'))
        : ($latestClassLevel ?: '-');

    $termValue = old(
        'term',
        $isEdit
            ? ($screening->term ?: $latestTerm)
            : $latestTerm
    );

    $gradeAverageValue = old(
        'grade_average',
        $isEdit
            ? (($screening->grade_average !== null && $screening->grade_average !== '')
                ? $screening->grade_average
                : $latestGradeAverage)
            : $latestGradeAverage
    );
@endphp


    <style>
        .snap-page {
            padding: 24px 0;
        }

        .snap-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
        }

        .snap-header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            padding: 24px;
        }

        .snap-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .snap-subtitle {
            opacity: .92;
        }

        .snap-body {
            padding: 24px;
            background: #fff;
        }

        .snap-section {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .snap-section-head {
            padding: 16px 20px;
            font-weight: 700;
            color: #fff;
        }

        .snap-inattention {
            background: #2563eb;
        }

        .snap-hyperactivity {
            background: #d97706;
        }

        .snap-oppositional {
            background: #dc2626;
        }

        .snap-question {
            padding: 18px 20px;
            border-bottom: 1px solid #eef2f7;
        }

        .snap-question:last-child {
            border-bottom: none;
        }

        .snap-question-title {
            font-weight: 600;
            line-height: 1.7;
            margin-bottom: 14px;
        }

        .snap-score-group {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
        }

        .snap-footer {
            position: sticky;
            bottom: 0;
            background: #fff;
            border-top: 1px solid #e5e7eb;
            padding: 18px;
            z-index: 100;
        }

        .snap-desc {
            font-size: .82rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, .78);
            margin: 0;
        }

        @media(max-width:768px) {

            .snap-score-group {
                flex-direction: column;
                gap: 10px;
            }

        }
    </style>

    <div class="container-fluid snap-page">

        <div class="snap-card">

            <div class="snap-header">

                <div class="snap-title">
                    {{ $isEdit ? 'แก้ไขแบบประเมินพฤติกรรม SNAP-IV' : 'แบบประเมินพฤติกรรม SNAP-IV' }}
                </div>

                <div class="snap-subtitle">
                    แบบประเมินคัดกรองพฤติกรรมเด็กและวัยรุ่น
                </div>
                <p class="snap-desc">
                    ใช้สำหรับคัดกรองและประเมินความรุนแรงของโรคสมาธิสั้น (ADHD)
                    ในเด็กอายุ 6–18 ปี
                </p>

            </div>

            <div class="snap-body">

                @if ($errors->any())
                    <div class="alert alert-danger">

                        <ul class="mb-0">

                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach

                        </ul>

                    </div>
                @endif

                <form id="snapScreeningForm" action="{{ $formAction }}" method="POST">

                    @csrf

                    @if ($isEdit)
                        @method('PUT')
                    @endif

                    <div class="row mb-4">
                        <div class="col-lg-3 mb-3">

                            <label class="form-label fw-bold">
                                วันที่ประเมิน <span class="text-danger">*</span>
                            </label>

                            <input type="date" name="screening_date"
                                value="{{ $screeningDateValue }}"
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

                            <input type="text" name="observer_name"
                                value="{{ old('observer_name', $isEdit ? $screening->observer_name : (auth()->user()->name ?? '')) }}" class="form-control">

                        </div>

                        <div class="col-lg-3 mb-3">

                            <label class="form-label fw-bold">
                                ความสัมพันธ์กับเด็ก
                            </label>

                            <input type="text" name="relationship" value="{{ old('relationship', $isEdit ? $screening->relationship : 'ครูประจำชั้น') }}"
                                class="form-control">

                        </div>

                        <div class="col-lg-3 mb-3">

                            <label class="form-label fw-bold">
                                อายุ
                            </label>

                            <input type="text" class="form-control bg-light"
                                value="{{ $ageTextValue }}"
                                readonly>

                            <input type="hidden" name="age_text"
                                value="{{ $ageTextValue }}">

                        </div>

                    </div>

                    <div class="row mb-4">

                        <div class="col-lg-4 mb-3">

                            <label class="form-label fw-bold">
                                ชั้นเรียน
                            </label>

                            <input type="text" class="form-control bg-light"
                                value="{{ $classLevelValue }}"
                                readonly>

                            <input type="hidden" name="class_level"
                                value="{{ $classLevelValue }}">

                        </div>

                        <div class="col-lg-4 mb-3">

                            <label class="form-label fw-bold">
                                ภาคเรียน
                            </label>

                            <input type="text" name="term" value="{{ $termValue }}" class="form-control">

                        </div>

                        <div class="col-lg-4 mb-3">

                            <label class="form-label fw-bold">
                                ผลการเรียนเฉลี่ย
                            </label>

                            <input type="number" name="grade_average" value="{{ $gradeAverageValue }}"
                                min="0" max="4" step="0.01" inputmode="decimal" class="form-control">

                        </div>

                    </div>

                    @php

                        $sectionTitles = [
                            'inattention' => 'อาการขาดสมาธิ',
                            'hyperactivity' => 'อาการซน อยู่ไม่นิ่ง และหุนหันพลันแล่น',
                            'oppositional' => 'อาการดื้อและต่อต้าน',
                        ];

                        $sectionClasses = [
                            'inattention' => 'snap-inattention',
                            'hyperactivity' => 'snap-hyperactivity',
                            'oppositional' => 'snap-oppositional',
                        ];

                        $scoreLabels = [
                            0 => 'ไม่พบ',
                            1 => 'เล็กน้อย',
                            2 => 'ปานกลาง',
                            3 => 'บ่อยมาก',
                        ];

                    @endphp

                    @foreach ($questions as $category => $items)
                        <div class="snap-section">

                            <div class="snap-section-head {{ $sectionClasses[$category] }}">
                                {{ $sectionTitles[$category] }}
                            </div>

                            @foreach ($items as $itemNo => $question)
                                <div class="snap-question">

                                    <div class="snap-question-title">
                                        {{ $itemNo }}. {{ $question }}
                                    </div>

                                    <div class="snap-score-group">

                                        @foreach ($scoreLabels as $score => $label)
                                            @php
                                                $selectedScore = old(
                                                    "answers.$category.$itemNo",
                                                    $isEdit ? data_get($answers ?? [], "$category.$itemNo") : null
                                                );
                                            @endphp

                                            <div class="form-check">

                                                <input class="form-check-input" type="radio"
                                                    name="answers[{{ $category }}][{{ $itemNo }}]"
                                                    value="{{ $score }}"
                                                    id="q_{{ $category }}_{{ $itemNo }}_{{ $score }}"
                                                    {{ (string) $selectedScore === (string) $score ? 'checked' : '' }}
                                                    {{ $score === 0 ? 'required' : '' }}>

                                                <label class="form-check-label"
                                                    for="q_{{ $category }}_{{ $itemNo }}_{{ $score }}">

                                                    {{ $score }}
                                                    คะแนน

                                                    <span class="text-muted">
                                                        ({{ $label }})
                                                    </span>

                                                </label>

                                            </div>
                                        @endforeach

                                    </div>

                                </div>
                            @endforeach

                        </div>
                    @endforeach

                    <div class="mb-5">

                        <label class="form-label fw-bold">
                            หมายเหตุเพิ่มเติม
                        </label>

                        <textarea name="remark" rows="4" class="form-control">{{ old('remark', $isEdit ? $screening->remark : '') }}</textarea>

                    </div>

                    <div class="snap-footer d-flex justify-content-between align-items-center flex-wrap gap-2">

                        <a href="{{ route('snap-iv.index', $client->id) }}" class="btn btn-light border">

                            <i class="bi bi-arrow-left"></i>
                            กลับ

                        </a>

                        <button type="submit" id="snapSubmitButton" class="btn btn-primary px-4">

                            <i class="bi bi-save"></i>
                            <span id="snapSubmitText">
                                {{ $isEdit ? 'บันทึกการแก้ไข' : 'บันทึกแบบประเมิน' }}
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('snapScreeningForm');
    const submitButton = document.getElementById('snapSubmitButton');
    const submitText = document.getElementById('snapSubmitText');

    if (!form || !submitButton) {
        return;
    }

    let isSubmitting = false;

    form.addEventListener('submit', function (event) {
        if (isSubmitting) {
            event.preventDefault();
            return;
        }

        isSubmitting = true;
        submitButton.disabled = true;
        submitButton.setAttribute('aria-disabled', 'true');

        if (submitText) {
            submitText.textContent = 'กำลังบันทึก...';
        }
    });
});
</script>

@endsection
