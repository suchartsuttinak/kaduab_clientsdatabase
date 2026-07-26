@extends('admin_client.admin_client')

@section('content')

    <style>
        .ds-page {
            padding: 24px 0;
        }

        .ds-card {
            border: none;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 14px 36px rgba(15, 23, 42, .08);
            margin-bottom: 24px;
            background: #fff;
        }

        .ds-header {
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, .22), transparent 34%),
                linear-gradient(135deg, #dc2626, #991b1b);
            color: #fff;
            padding: 26px 30px;
            position: relative;
        }

        .ds-title {
            font-size: 1.48rem;
            font-weight: 800;
            margin-bottom: 6px;
            line-height: 1.35;
        }

        .ds-subtitle {
            opacity: .93;
            font-size: .95rem;
            line-height: 1.7;
        }

        .ds-client-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            padding: 18px 0 22px;
            margin-bottom: 18px;
            border-bottom: 1px solid #eef2f7;
        }

        .ds-client-main {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .ds-client-avatar {
            width: 50px;
            height: 50px;
            border-radius: 18px;
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
            flex: 0 0 auto;
        }

        .ds-client-name {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
            line-height: 1.35;
        }

        .ds-client-meta {
            color: #64748b;
            font-size: .92rem;
            line-height: 1.8;
        }

        .ds-client-meta span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-right: 16px;
            white-space: nowrap;
        }

        .ds-form-panel {
            background: #fafafa;
            border: 1px solid #eef2f7;
            border-radius: 18px;
            padding: 18px 18px 2px;
            margin-bottom: 22px;
        }

        .ds-question-table {
            min-width: 980px;
            margin: 0;
        }

        .ds-question-table th {
            background: #f8fafc;
            color: #0f172a;
            font-weight: 800;
            text-align: center;
            vertical-align: middle;
            border-bottom: 1px solid #e5e7eb;
            padding: 12px 10px;
            white-space: nowrap;
        }

        .ds-question-table td {
            vertical-align: middle;
            padding: 13px 10px;
            border-bottom: 1px solid #eef2f7;
        }

        .ds-question-no {
            width: 60px;
            text-align: center;
            font-weight: 700;
            color: #334155;
        }

        .ds-question-text {
            min-width: 360px;
            line-height: 1.7;
            color: #0f172a;
            font-weight: 600;
        }

        .ds-radio-cell {
            width: 120px;
            text-align: center;
        }

        .ds-radio-cell .form-check {
            display: flex;
            justify-content: center;
            margin: 0;
            min-height: auto;
        }

        .ds-radio-cell input {
            cursor: pointer;
        }

        .ds-note {
            font-size: .92rem;
            color: #64748b;
            line-height: 1.7;
            border-radius: 16px;
        }

        .ds-footer {
            position: sticky;
            bottom: 0;
            background: #fff;
            padding: 18px;
            border-top: 1px solid #e5e7eb;
            z-index: 100;
        }

        .ds-save-btn {
            border-radius: 12px;
            padding: 12px 26px;
            font-weight: 700;
        }

        @media (max-width:768px) {
            .ds-header {
                padding: 22px;
            }

            .ds-title {
                font-size: 1.25rem;
            }

            .ds-client-strip {
                align-items: flex-start;
            }

            .ds-client-main {
                align-items: flex-start;
            }

            .ds-client-meta span {
                display: flex;
                margin: 4px 0;
                white-space: normal;
            }

            .ds-footer {
                align-items: stretch !important;
            }

            .ds-footer .btn {
                width: 100%;
            }
        }
    </style>

    <div class="container-fluid ds-page">

        <div class="ds-card">

            <div class="ds-header">
                <div class="ds-title">
                    แบบคัดกรองภาวะซึมเศร้าในวัยรุ่น
                </div>

                <div class="ds-subtitle">
                    Center for Epidemiologic Studies-Depression Scale (CES-D) ฉบับภาษาไทย<br>
                    กรุณาประเมินความรู้สึกในช่วง 1 สัปดาห์ที่ผ่านมา
                </div>
            </div>

            <div class="p-4">

                <div class="ds-client-strip">
                    <div class="ds-client-main">
                        <div class="ds-client-avatar">
                            <i class="bi bi-person-heart"></i>
                        </div>

                        <div>
                            <div class="ds-client-name">
                                {{ $client->first_name }} {{ $client->last_name }}
                            </div>

                            <div class="ds-client-meta">
                                <span>
                                    <i class="bi bi-card-text"></i>
                                    เลขทะเบียน {{ $client->register_number ?? '-' }}
                                </span>

                                <span>
                                    <i class="bi bi-calendar-heart"></i>
                                    อายุ
                                    {{ $client->birth_date ? \Carbon\Carbon::parse($client->birth_date)->age . ' ปี' : '-' }}
                                </span>

                                <span>
                                    <i class="bi bi-mortarboard"></i>
                                    ชั้นเรียน
                                    {{ optional($client->educationRecords->sortByDesc('record_date')->first()?->education)->education_name ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <div class="fw-bold mb-1">
                            กรุณาตรวจสอบข้อมูล
                        </div>

                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('depression-screenings.store', $client->id) }}" method="POST">

                    @csrf

                    <div class="ds-form-panel">
                        <div class="row mb-2">

                            <div class="col-lg-3 mb-3">

                                <label class="form-label fw-bold">
                                    วันที่คัดกรอง <span class="text-danger">*</span>
                                </label>

                                <input type="date" name="screening_date"
                                    value="{{ old('screening_date', now('Asia/Bangkok')->toDateString()) }}"
                                    max="{{ now('Asia/Bangkok')->toDateString() }}"
                                    class="form-control @error('screening_date') is-invalid @enderror">

                                @error('screening_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <div class="col-lg-4 mb-3">
                                <label class="form-label fw-bold">
                                    ผู้ประเมิน
                                </label>

                                <input type="text" name="observer_name"
                                    value="{{ old('observer_name', auth()->user()->name ?? '') }}" class="form-control">
                            </div>

                            <input type="hidden" name="age_text"
                                value="{{ $client->birth_date ? \Carbon\Carbon::parse($client->birth_date)->age . ' ปี' : '-' }}">

                            <input type="hidden" name="class_level"
                                value="{{ optional($client->educationRecords->sortByDesc('record_date')->first()?->education)->education_name ?? '-' }}">

                        </div>
                    </div>

                    <div class="alert alert-light border ds-note mb-4">
                        <strong>คำชี้แจง:</strong>
                        ผู้รับบริการมีความรู้สึกดังต่อไปนี้บ่อยเพียงใดใน 1 สัปดาห์ที่ผ่านมา
                        กรุณาเลือกคำตอบที่ตรงกับความรู้สึกมากที่สุด
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="table-responsive">
                            <table class="table ds-question-table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width:60px;">ข้อ</th>
                                        <th>ข้อคำถาม</th>
                                        <th>ไม่เลย<br><small>(น้อยกว่า 1 วัน)</small></th>
                                        <th>นานๆ ครั้ง<br><small>(1-2 วัน)</small></th>
                                        <th>บ่อยๆ<br><small>(3-4 วัน)</small></th>
                                        <th>ตลอดเวลา<br><small>(5-7 วัน)</small></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($questions as $itemNo => $question)
                                        <tr>
                                            <td class="ds-question-no">
                                                {{ $itemNo }}
                                            </td>

                                            <td class="ds-question-text">
                                                {{ $question }}

                                                @if (in_array($itemNo, [4, 8, 12, 16]))
                                                    <span class="badge bg-light text-muted border ms-1">
                                                        ข้อกลับคะแนน
                                                    </span>
                                                @endif
                                            </td>

                                            @for ($score = 0; $score <= 3; $score++)
                                                <td class="ds-radio-cell">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="answers[{{ $itemNo }}]"
                                                            value="{{ $score }}"
                                                            id="answer_{{ $itemNo }}_{{ $score }}"
                                                            {{ (string) old("answers.$itemNo") === (string) $score ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold">
                            หมายเหตุเพิ่มเติม
                        </label>

                        <textarea name="remark" rows="4" class="form-control">{{ old('remark') }}</textarea>
                    </div>

                    <div class="ds-footer d-flex justify-content-between align-items-center flex-wrap gap-2">

                        <a href="{{ route('depression-screenings.index', $client->id) }}" class="btn btn-light border">
                            <i class="bi bi-arrow-left"></i>
                            กลับ
                        </a>

                        <button type="submit" class="btn btn-danger ds-save-btn">
                            <i class="bi bi-save"></i>
                            บันทึกแบบคัดกรอง
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection
