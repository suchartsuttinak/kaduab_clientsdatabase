@extends('admin_client.admin_client')

@section('content')
<div class="container-fluid py-4 nutrition-page">
    <div class="nutrition-card">
        <div class="nutrition-header">
            <div>
                <h4>เพิ่มผลประเมินภาวะโภชนาการ</h4>
                <p>{{ $client->prefix ?? '' }}{{ $client->first_name ?? '' }} {{ $client->last_name ?? '' }}</p>
            </div>

            <a href="{{ route('nutrition_assessments.index', $client->id) }}"
               class="nutrition-btn nutrition-btn-secondary">
                <i class="bi bi-arrow-left"></i>
                <span>กลับหน้ารายการ</span>
            </a>
        </div>

        <hr>

        @if ($errors->any())
            <div class="alert alert-danger border-0 rounded-3">
                <strong><i class="bi bi-exclamation-circle-fill me-1"></i> กรุณาตรวจสอบข้อมูล</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $birthDate = $client->birth_date ? \Carbon\Carbon::parse($client->birth_date) : null;
            $assessmentDate = \Carbon\Carbon::parse(old('assessment_date', now('Asia/Bangkok')->toDateString()));
            $ageText = '-';

            if ($birthDate && $assessmentDate->greaterThanOrEqualTo($birthDate)) {
                $age = $birthDate->diff($assessmentDate);
                $ageText = $age->y . ' ปี ' . $age->m . ' เดือน';
            }

            $birthDateThai = $birthDate
                ? $birthDate->format('d/m/') . ($birthDate->year + 543)
                : '-';

            $genderText = match ($client->gender ?? null) {
                'male' => 'ชาย',
                'female' => 'หญิง',
                default => '-',
            };
        @endphp

        <form id="nutritionForm" method="POST" action="{{ route('nutrition_assessments.store', $client->id) }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">วันที่ชั่งวัด <span class="text-danger">*</span></label>
                    <input type="date"
                           name="assessment_date"
                           id="assessment_date"
                           class="form-control @error('assessment_date') is-invalid @enderror"
                           value="{{ old('assessment_date', now('Asia/Bangkok')->toDateString()) }}"
                           min="{{ $client->birth_date ? \Carbon\Carbon::parse($client->birth_date)->format('Y-m-d') : '' }}"
                           max="{{ now('Asia/Bangkok')->toDateString() }}"
                           required>
                    @error('assessment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">วันเกิด</label>
                    <input type="text" class="form-control bg-light" value="{{ $birthDateThai }}" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">เพศ</label>
                    <input type="text" class="form-control bg-light" value="{{ $genderText }}" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">อายุ ณ วันที่ชั่งวัด</label>
                    <input type="text" id="age_display" class="form-control bg-light" value="{{ $ageText }}" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">ส่วนสูง (เซนติเมตร) <span class="text-danger">*</span></label>
                    <input type="number"
                           step="0.01"
                           min="30"
                           max="250"
                           name="height_cm"
                           class="form-control @error('height_cm') is-invalid @enderror"
                           value="{{ old('height_cm') }}"
                           placeholder="เช่น 135.50"
                           required>
                    @error('height_cm')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">น้ำหนัก (กิโลกรัม) <span class="text-danger">*</span></label>
                    <input type="number"
                           step="0.01"
                           min="1"
                           max="300"
                           name="weight_kg"
                           class="form-control @error('weight_kg') is-invalid @enderror"
                           value="{{ old('weight_kg') }}"
                           placeholder="เช่น 32.40"
                           required>
                    @error('weight_kg')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">หมายเหตุ</label>
                    <textarea name="note"
                              class="form-control @error('note') is-invalid @enderror"
                              rows="3"
                              maxlength="2000"
                              placeholder="ระบุหมายเหตุเพิ่มเติมถ้ามี">{{ old('note') }}</textarea>
                    @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="nutrition-actions">
                <a href="{{ route('nutrition_assessments.index', $client->id) }}"
                   class="nutrition-btn nutrition-btn-secondary">
                    <i class="bi bi-x-circle"></i>
                    <span>ปิด</span>
                </a>

                <button type="reset" class="nutrition-btn nutrition-btn-reset">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>ล้างข้อมูล</span>
                </button>

                <button type="submit" id="submitButton" class="nutrition-btn nutrition-btn-primary">
                    <span class="button-normal"><i class="bi bi-save2-fill"></i> บันทึกผล</span>
                    <span class="button-loading d-none"><span class="spinner-border spinner-border-sm" aria-hidden="true"></span> กำลังบันทึก...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.nutrition-page{background:#f6f8fb;}.nutrition-card{background:#fff;border:1px solid #e8eef5;border-radius:18px;padding:28px;box-shadow:0 8px 25px rgba(15,23,42,.06);}.nutrition-header{display:flex;justify-content:space-between;align-items:center;gap:16px;}.nutrition-header h4{margin:0;font-weight:800;color:#1e293b;}.nutrition-header p{margin:4px 0 0;color:#64748b;font-size:.9rem;}.form-label{font-weight:700;color:#334155;}.form-control,.form-select{border-radius:11px;min-height:44px;border-color:#cbd5e1;}.form-control:focus,.form-select:focus{border-color:#60a5fa;box-shadow:0 0 0 .2rem rgba(37,99,235,.12);}.nutrition-actions{display:flex;gap:12px;justify-content:center;margin-top:30px;flex-wrap:wrap;}.nutrition-btn{min-height:43px;border-radius:12px;padding:10px 20px;border:1px solid transparent;display:inline-flex;align-items:center;justify-content:center;gap:8px;font-weight:700;text-decoration:none;white-space:nowrap;transition:transform .18s ease,box-shadow .18s ease,background .18s ease,border-color .18s ease,color .18s ease;}.nutrition-btn-primary{color:#fff;background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 7px 16px rgba(37,99,235,.22);}.nutrition-btn-primary:hover,.nutrition-btn-primary:focus{color:#fff;transform:translateY(-1px);box-shadow:0 10px 20px rgba(37,99,235,.28);}.nutrition-btn-primary:active{transform:translateY(0);}.nutrition-btn-primary:disabled{opacity:1;color:#fff;background:linear-gradient(135deg,#2563eb,#1d4ed8);cursor:not-allowed;}.nutrition-btn-secondary{color:#475569;background:#fff;border-color:#cbd5e1;box-shadow:0 3px 8px rgba(15,23,42,.05);}.nutrition-btn-secondary:hover{color:#1e293b;background:#f8fafc;border-color:#94a3b8;transform:translateY(-1px);}.nutrition-btn-reset{color:#b45309;background:#fffbeb;border-color:#fde68a;}.nutrition-btn-reset:hover{color:#fff;background:#d97706;border-color:#d97706;transform:translateY(-1px);}.button-normal,.button-loading{display:inline-flex;align-items:center;gap:8px;}@media(max-width:768px){.nutrition-card{padding:18px;}.nutrition-header{flex-direction:column;align-items:flex-start;}.nutrition-header>a{width:100%;}.nutrition-actions{justify-content:stretch;}.nutrition-actions .nutrition-btn{width:100%;}}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('nutritionForm');
    const submitButton = document.getElementById('submitButton');
    const assessmentDate = document.getElementById('assessment_date');
    const ageDisplay = document.getElementById('age_display');
    const birthDate = @json($client->birth_date ? \Carbon\Carbon::parse($client->birth_date)->format('Y-m-d') : null);

    function updateAge() {
        if (!birthDate || !assessmentDate.value) {
            ageDisplay.value = '-';
            return;
        }

        const birth = new Date(birthDate + 'T00:00:00');
        const assessed = new Date(assessmentDate.value + 'T00:00:00');

        if (assessed < birth) {
            ageDisplay.value = '-';
            return;
        }

        let years = assessed.getFullYear() - birth.getFullYear();
        let months = assessed.getMonth() - birth.getMonth();
        if (assessed.getDate() < birth.getDate()) months--;
        if (months < 0) { years--; months += 12; }
        ageDisplay.value = years + ' ปี ' + months + ' เดือน';
    }

    assessmentDate.addEventListener('change', updateAge);
    updateAge();

    form.addEventListener('reset', function () {
        window.setTimeout(updateAge, 0);
    });

    form.addEventListener('submit', function () {
        if (submitButton.disabled) return;
        submitButton.disabled = true;
        submitButton.querySelector('.button-normal').classList.add('d-none');
        submitButton.querySelector('.button-loading').classList.remove('d-none');
    });
});
</script>
@endsection
