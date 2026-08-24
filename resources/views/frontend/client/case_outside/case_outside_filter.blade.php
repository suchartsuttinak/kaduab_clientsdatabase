@extends('admin_client.admin_client')

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/case_outside.css') }}">
    <style>
        .co-filter-page {
            --co-text: #0f172a;
            --co-muted: #64748b;
            --co-border: #dbe3ef;
        }

        .co-filter-card {
            overflow: hidden;
            border: 1px solid var(--co-border);
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .055);
        }

        .co-filter-head {
            padding: 1.15rem 1.25rem;
            border-bottom: 1px solid var(--co-border);
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        }

        .co-filter-title {
            margin: 0;
            color: var(--co-text);
            font-size: 1.25rem;
            font-weight: 800;
        }

        .co-filter-subtitle {
            margin: .3rem 0 0;
            color: var(--co-muted);
            font-size: .88rem;
            line-height: 1.6;
        }

        .co-filter-body {
            padding: 1.25rem;
        }

        .co-filter-label {
            margin-bottom: .4rem;
            color: #334155;
            font-size: .86rem;
            font-weight: 750;
        }

        .co-filter-card .form-control,
        .co-filter-card .form-select {
            min-height: 45px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            box-shadow: none;
        }

        .co-filter-card .form-control:focus,
        .co-filter-card .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1);
        }

        .co-filter-actions {
            display: flex;
            gap: .6rem;
            flex-wrap: wrap;
        }

        .co-filter-actions .btn {
            display: inline-flex;
            min-height: 44px;
            align-items: center;
            justify-content: center;
            gap: .4rem;
            border-radius: 12px;
            font-weight: 750;
        }

        @media (max-width: 767.98px) {
            .co-filter-head,
            .co-filter-body {
                padding: 1rem;
            }

            .co-filter-actions .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
<div class="container-fluid mt-2 co-filter-page">
    <div class="co-filter-card">
        <div class="co-filter-head">
            <h1 class="co-filter-title">
                ค้นหารายงานติดตามเด็กที่พักอาศัยภายนอก
            </h1>
            <p class="co-filter-subtitle">
                กำหนดช่วงวันที่ สาเหตุ และรูปแบบการดำเนินงาน
                เพื่อแสดงรายงานตามข้อมูลที่ต้องการ
            </p>
        </div>

        <div class="co-filter-body">
            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <div class="fw-bold mb-1">กรุณาตรวจสอบเงื่อนไขรายงาน</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('case_outside.report', $client->id) }}"
                  method="GET">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="case_outside_date_start" class="co-filter-label">
                            วันที่เริ่มต้น
                        </label>
                        <input type="date"
                               id="case_outside_date_start"
                               name="date_start"
                               value="{{ old('date_start', request('date_start')) }}"
                               max="{{ now('Asia/Bangkok')->toDateString() }}"
                               class="form-control @error('date_start') is-invalid @enderror">
                        @error('date_start')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="case_outside_date_end" class="co-filter-label">
                            วันที่สิ้นสุด
                        </label>
                        <input type="date"
                               id="case_outside_date_end"
                               name="date_end"
                               value="{{ old('date_end', request('date_end')) }}"
                               max="{{ now('Asia/Bangkok')->toDateString() }}"
                               class="form-control @error('date_end') is-invalid @enderror">
                        @error('date_end')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="case_outside_outside_id" class="co-filter-label">
                            สาเหตุที่พักอาศัยอยู่ภายนอก
                        </label>
                        <select id="case_outside_outside_id"
                                name="outside_id"
                                class="form-select @error('outside_id') is-invalid @enderror">
                            <option value="">-- ทั้งหมด --</option>
                            @foreach($outside as $item)
                                <option value="{{ $item->id }}"
                                        {{ (string) old('outside_id', request('outside_id')) === (string) $item->id ? 'selected' : '' }}>
                                    {{ $item->outside_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('outside_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="case_outside_follo_no" class="co-filter-label">
                            การดำเนินงาน
                        </label>
                        <select id="case_outside_follo_no"
                                name="follo_no"
                                class="form-select @error('follo_no') is-invalid @enderror">
                            <option value="">-- ทั้งหมด --</option>
                            @foreach(['หน่วยงานไปเอง', 'โทรศัพท์', 'จดหมาย'] as $method)
                                <option value="{{ $method }}"
                                        {{ old('follo_no', request('follo_no')) === $method ? 'selected' : '' }}>
                                    {{ $method }}
                                </option>
                            @endforeach
                        </select>
                        @error('follo_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 pt-1">
                        <div class="co-filter-actions">
                            <button type="submit" class="btn btn-success px-3">
                                <i class="bi bi-printer"></i>
                                <span>แสดงรายงาน</span>
                            </button>

                            <a href="{{ route('case_outside.show', $client->id) }}"
                               class="btn btn-outline-secondary px-3">
                                <i class="bi bi-arrow-left-circle"></i>
                                <span>กลับหน้าหลัก</span>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startInput = document.getElementById('case_outside_date_start');
    const endInput = document.getElementById('case_outside_date_end');

    function syncDateRange() {
        if (!startInput || !endInput) return;

        endInput.min = startInput.value || '';
        if (startInput.value && endInput.value && endInput.value < startInput.value) {
            endInput.value = startInput.value;
        }
    }

    if (startInput && endInput) {
        startInput.addEventListener('change', syncDateRange);
        syncDateRange();
    }
});
</script>
@endpush
