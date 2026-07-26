@extends('admin_client.admin_client')

@section('content')
    <div class="container-fluid py-4 nutrition-page">

        <div class="nutrition-card">

            <div class="nutrition-header">
                <div>
                    <h4>เพิ่มผลประเมินภาวะโภชนาการ</h4>
                    <p>
                        {{ $client->prefix ?? '' }}{{ $client->first_name ?? '' }}
                        {{ $client->last_name ?? '' }}
                    </p>
                </div>

                <a href="{{ route('nutrition_assessments.index', $client->id) }}" class="btn btn-outline-secondary btn-sm">
                    กลับหน้ารายการ
                </a>
            </div>

            <hr>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>กรุณาตรวจสอบข้อมูล</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('nutrition_assessments.update', [$client->id, $assessment->id]) }}" method="POST">
                @csrf
                @method('PUT')

                @php
                    $birthDate = $client->birth_date ? \Carbon\Carbon::parse($client->birth_date) : null;

                    $assessmentDate = \Carbon\Carbon::parse(old('assessment_date', date('Y-m-d')));

                    $ageText = '-';

                    if ($birthDate) {
                        $age = $birthDate->diff($assessmentDate);
                        $ageText = $age->y . ' ปี ' . $age->m . ' เดือน';
                    }

                    $birthDateThai = $birthDate ? $birthDate->format('d/m/') . ($birthDate->year + 543) : '-';

                    $genderText = match ($client->gender ?? null) {
                        'male' => 'ชาย',
                        'female' => 'หญิง',
                        default => '-',
                    };
                @endphp

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">วันที่ชั่งวัด <span class="text-danger">*</span></label>
                        <input type="date" name="assessment_date"
                            class="form-control @error('assessment_date') is-invalid @enderror"
                            max="{{ now()->format('Y-m-d') }}"
                            value="{{ old('assessment_date', $assessment->assessment_date?->format('Y-m-d')) }}">
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
                        <input type="text" id="age_display" class="form-control bg-light" value="{{ $ageText }}"
                            readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            ส่วนสูง (เซนติเมตร) <span class="text-danger">*</span>
                        </label>

                        <input type="number" step="0.01" name="height_cm"
                            class="form-control @error('height_cm') is-invalid @enderror"
                            value="{{ old('height_cm', $assessment->height_cm) }}" placeholder="เช่น 135.50">

                        @error('height_cm')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            น้ำหนัก (กิโลกรัม) <span class="text-danger">*</span>
                        </label>

                        <input type="number" step="0.01" name="weight_kg"
                            class="form-control @error('weight_kg') is-invalid @enderror"
                            value="{{ old('weight_kg', $assessment->weight_kg) }}" placeholder="เช่น 32.40">

                        @error('weight_kg')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea name="note" class="form-control @error('note') is-invalid @enderror" rows="3"
                            placeholder="ระบุหมายเหตุเพิ่มเติมถ้ามี">{{ old('note', $assessment->note) }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <input type="hidden" name="birth_date" value="{{ $client->birth_date }}">
                    <input type="hidden" name="gender" value="{{ $client->gender }}">

                </div>

                <div class="nutrition-actions">
                    <a href="{{ route('nutrition_assessments.index', $client->id) }}"
                        class="btn btn-outline-secondary px-4">
                        กลับหน้ารายการ
                    </a>

                    <button type="reset" class="btn btn-outline-danger px-4">
                        ล้างข้อมูล
                    </button>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save"></i>
                        บันทึกผล
                    </button>
                </div>
            </form>

        </div>

    </div>

    <style>
        .nutrition-page {
            background: #f6f8fb;
        }

        .nutrition-card {
            background: #fff;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 8px 25px rgba(15, 23, 42, .06);
        }

        .nutrition-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .nutrition-header h4 {
            margin: 0;
            font-weight: 700;
            color: #1e293b;
        }

        .nutrition-header p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: .9rem;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            min-height: 42px;
        }

        .nutrition-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 28px;
            flex-wrap: wrap;
        }

        @media(max-width:768px) {
            .nutrition-card {
                padding: 18px;
            }

            .nutrition-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .nutrition-actions {
                justify-content: flex-start;
            }

            .nutrition-actions .btn {
                width: 100%;
            }
        }
    </style>
@endsection
