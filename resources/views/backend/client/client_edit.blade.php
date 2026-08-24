@extends('admin_client.admin_client')
@section('content')

    @php
        use Illuminate\Support\Carbon;

        $districts = $districts ?? collect();
        $sub_districts = $sub_districts ?? collect();
        $origin_districts = $origin_districts ?? collect();
        $origin_sub_districts = $origin_sub_districts ?? collect();
        $tab = $tab ?? request('tab', 'profile');

        $birthDateValue = old(
            'birth_date',
            !empty($client?->birth_date) ? Carbon::parse($client->birth_date)->format('Y-m-d') : '',
        );

        $arrivalDateValue = old(
            'arrival_date',
            !empty($client?->arrival_date) ? Carbon::parse($client->arrival_date)->format('Y-m-d') : '',
        );
    @endphp

    <link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">

    <style>
        .client-form-action {
            min-width: 148px;
            min-height: 46px;
            padding: .65rem 1.15rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            border: 1px solid transparent;
            border-radius: 12px;
            font-size: .92rem;
            font-weight: 750;
            line-height: 1.2;
            white-space: nowrap;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
            transition: transform .16s ease, box-shadow .16s ease, background .16s ease,
                border-color .16s ease, color .16s ease;
        }

        .client-form-submit,
        .client-form-submit:visited {
            border-color: #1d4ed8 !important;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            color: #fff !important;
            box-shadow: 0 8px 18px rgba(37, 99, 235, .20) !important;
        }

        .client-form-submit:hover,
        .client-form-submit:focus,
        .client-form-submit:focus-visible {
            border-color: #1e40af !important;
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
            color: #fff !important;
            box-shadow: 0 10px 22px rgba(37, 99, 235, .25),
                0 0 0 4px rgba(37, 99, 235, .12) !important;
        }

        .client-form-submit:active,
        .client-form-submit.active {
            transform: translateY(1px) scale(.995) !important;
            border-color: #1e3a8a !important;
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%) !important;
            box-shadow: 0 4px 10px rgba(30, 64, 175, .23),
                inset 0 2px 4px rgba(15, 23, 42, .16) !important;
        }

        .client-form-cancel,
        .client-form-cancel:visited {
            border-color: #cbd5e1 !important;
            background: #fff !important;
            color: #475569 !important;
            box-shadow: none !important;
        }

        .client-form-cancel:hover,
        .client-form-cancel:focus,
        .client-form-cancel:focus-visible {
            border-color: #94a3b8 !important;
            background: #f1f5f9 !important;
            color: #1e293b !important;
            box-shadow: 0 6px 14px rgba(15, 23, 42, .08),
                0 0 0 4px rgba(100, 116, 139, .10) !important;
        }

        .client-form-submit.is-submitting,
        .client-form-submit.is-submitting:hover,
        .client-form-submit:disabled {
            transform: none !important;
            border-color: #1d4ed8 !important;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            color: #fff !important;
            opacity: 1 !important;
            cursor: progress;
            box-shadow: 0 7px 16px rgba(37, 99, 235, .18) !important;
        }

        .client-form-submit .spinner-border {
            width: 1rem;
            height: 1rem;
            border-width: .14em;
        }

        @media (max-width: 575.98px) {
            .action-bar {
                display: grid !important;
                grid-template-columns: 1fr;
            }

            .client-form-action {
                width: 100%;
                min-width: 0;
            }
        }
    </style>

<div class="container-fluid registry-page">
        <div class="registry-wrapper">
            <div class="registry-header">
                แก้ไขทะเบียนประวัติผู้รับฯ
            </div>

            <div class="registry-subtabs">
                {{-- รายละเอียดผู้รับ --}}
                <a href="{{ route('client.edit', $client->id) }}"
                    class="subtab-link {{ request()->routeIs('client.edit') ? 'active' : '' }}">
                    รายละเอียดผู้รับ
                </a>

                {{-- ครอบครัว --}}
                <a href="{{ route('family.add', $client->id) }}"
                    class="subtab-link {{ request()->routeIs('family.add') ? 'active' : '' }}">
                    ครอบครัว
                </a>

                {{-- สมาชิกครอบครัว --}}
                <a href="{{ route('member.create', $client->id) }}"
                    class="subtab-link {{ request()->routeIs('member.create') ? 'active' : '' }}">
                    สมาชิกครอบครัว
                </a>

                {{-- สอบข้อเท็จจริง --}}
                <a href="{{ route('factfinding.add', $client->id) }}"
                    class="subtab-link {{ request()->routeIs('factfinding.add') ? 'active' : '' }}">
                    สอบข้อเท็จจริง
                </a>
            </div>

            <div class="registry-body">
                @if ($tab === 'profile')
                    <form action="{{ route('client.update') }}" method="POST" enctype="multipart/form-data" id="client-edit-form">
                        @csrf
                        <input type="hidden" name="id" value="{{ $client->id }}">

                        <div class="panel-box">
                            <div class="row client-main-row compact-row">
                                <div class="col-12 col-lg-10">
                                    <div class="row compact-row">
                                        <div class="col-12 col-md-4 col-lg-3">
                                            <label for="register_number" class="form-label">เลขทะเบียน</label>
                                            <input type="text" name="register_number" id="register_number"
                                                class="form-control @error('register_number') is-invalid @enderror"
                                                value="{{ old('register_number', $client->register_number) }}">
                                            @error('register_number')
                                                <small class="text-danger error-message"
                                                    id="error-register_number">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-8 col-lg-5">
                                            <label for="id_card" class="form-label">
                                                เลขประจำตัวประชาชน <span class="required-star"></span>
                                            </label>

                                            <input type="text" name="id_card" id="id_card" maxlength="17"
                                                placeholder="0-0000-00000-00-0" inputmode="numeric"
                                                class="form-control @error('id_card') is-invalid @enderror"
                                                value="{{ old('id_card', $client?->id_card) }}">

                                            @error('id_card')
                                                <small class="text-danger error-message" id="error-id_card">
                                                    {{ $message }}
                                                </small>
                                            @enderror
                                        </div>
                                        <div class="col-6 col-md-3 col-lg-2">
                                            <label for="title_id" class="form-label">คำนำหน้า <span
                                                    class="required-star">*</span></label>
                                            <select name="title_id" id="title_id"
                                                class="form-select @error('title_id') is-invalid @enderror">
                                                <option value="">--เลือก--</option>
                                                @foreach ($titles as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('title_id', $client->title_id ?? '') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->title_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('title_id')
                                                <small class="text-danger error-message"
                                                    id="error-title_id">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-6 col-md-3 col-lg-2">
                                            <label for="nick_name" class="form-label">ชื่อเล่น</label>
                                            <input type="text" name="nick_name" id="nick_name" class="form-control"
                                                value="{{ old('nick_name', $client?->nick_name) }}">
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-3">
                                            <label for="first_name" class="form-label">ชื่อ <span
                                                    class="required-star">*</span></label>
                                            <input type="text" name="first_name" id="first_name"
                                                class="form-control @error('first_name') is-invalid @enderror"
                                                value="{{ old('first_name', $client?->first_name) }}">
                                            @error('first_name')
                                                <small class="text-danger error-message"
                                                    id="error-first_name">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-3">
                                            <label for="last_name" class="form-label">นามสกุล <span
                                                    class="required-star">*</span></label>
                                            <input type="text" name="last_name" id="last_name"
                                                class="form-control @error('last_name') is-invalid @enderror"
                                                value="{{ old('last_name', $client?->last_name) }}">
                                            @error('last_name')
                                                <small class="text-danger error-message"
                                                    id="error-last_name">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-3">
                                            <label class="form-label">เพศ <span class="required-star">*</span></label>
                                            <div class="inline-radio-group @error('gender') is-invalid @enderror">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="gender"
                                                        id="genderMale" value="male"
                                                        {{ old('gender', $client->gender ?? '') == 'male' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="genderMale">ชาย</label>
                                                </div>

                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="gender"
                                                        id="genderFemale" value="female"
                                                        {{ old('gender', $client->gender ?? '') == 'female' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="genderFemale">หญิง</label>
                                                </div>
                                            </div>
                                            @error('gender')
                                                <small class="text-danger d-block error-message"
                                                    id="error-gender">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-3">
                                            <label for="birth_date" class="form-label">
                                                วันเกิด <span class="required-star">*</span>
                                            </label>

                                            <input type="date" name="birth_date" id="birth_date"
                                                class="form-control @error('birth_date') is-invalid @enderror"
                                                value="{{ old('birth_date', $birthDateValue) }}">

                                            @error('birth_date')
                                                <small class="text-danger d-block error-message" id="error-birth_date">
                                                    {{ $message }}
                                                </small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-2">
                                            <label for="national_id" class="form-label">สัญชาติ <span
                                                    class="required-star">*</span></label>
                                            <select name="national_id" id="national_id"
                                                class="form-select @error('national_id') is-invalid @enderror">
                                                <option value="">--เลือก--</option>
                                                @foreach ($nations as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('national_id', $client?->national_id) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->national_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('national_id')
                                                <small class="text-danger d-block error-message" id="error-national_id">
                                                    {{ $message }}
                                                </small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-2">
                                            <label for="religion_id" class="form-label">ศาสนา <span
                                                    class="required-star">*</span></label>
                                            <select name="religion_id" id="religion_id"
                                                class="form-select @error('religion_id') is-invalid @enderror">
                                                <option value="">--เลือก--</option>
                                                @foreach ($religions as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('religion_id', $client?->religion_id) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->religion_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('religion_id')
                                                <small class="text-danger d-block error-message" id="error-religion_id">
                                                    {{ $message }}
                                                </small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-3">
                                            <label for="marital_id" class="form-label">สถานะการสมรส <span
                                                    class="required-star">*</span></label>
                                            <select name="marital_id" id="marital_id"
                                                class="form-select @error('marital_id') is-invalid @enderror">
                                                <option value="">--เลือก--</option>
                                                @foreach ($maritals as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('marital_id', $client?->marital_id) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->marital_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('marital_id')
                                                <small class="text-danger d-block error-message" id="error-marital_id">
                                                    {{ $message }}
                                                </small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-3">
                                            <label for="occupation_id" class="form-label">อาชีพ <span
                                                    class="required-star">*</span></label>
                                            <select name="occupation_id" id="occupation_id"
                                                class="form-select @error('occupation_id') is-invalid @enderror">
                                                <option value="">--เลือก--</option>
                                                @foreach ($occupations as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('occupation_id', $client?->occupation_id) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->occupation_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('occupation_id')
                                                <small class="text-danger d-block error-message" id="error-occupation_id">
                                                    {{ $message }}
                                                </small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-3">
                                            <label for="income_id" class="form-label">รายได้เฉลี่ย/เดือน <span
                                                    class="required-star">*</span></label>
                                            <select name="income_id" id="income_id"
                                                class="form-select @error('income_id') is-invalid @enderror">
                                                <option value="">--เลือก--</option>
                                                @foreach ($incomes as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('income_id', $client?->income_id) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->income_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('income_id')
                                                <small class="text-danger d-block error-message" id="error-income_id">
                                                    {{ $message }}
                                                </small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-3">
                                            <label for="education_id" class="form-label">การศึกษา <span
                                                    class="required-star">*</span></label>
                                            <select name="education_id" id="education_id"
                                                class="form-select @error('education_id') is-invalid @enderror">
                                                <option value="">--เลือก--</option>
                                                @foreach ($educations as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('education_id', $client?->education_id) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->education_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('education_id')
                                                <small class="text-danger d-block error-message" id="error-education_id">
                                                    {{ $message }}
                                                </small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-4">
                                            <label for="scholl" class="form-label">ชื่อโรงเรียน/สถาบัน</label>
                                            <input type="text" name="scholl" id="scholl" class="form-control"
                                                value="{{ old('scholl', $client?->scholl) }}">
                                            @error('scholl')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-4">
                                            <label for="target_id" class="form-label">กลุ่มเป้าหมาย <span
                                                    class="required-star">*</span></label>
                                            <select name="target_id" id="target_id"
                                                class="form-select @error('target_id') is-invalid @enderror">
                                                <option value="">--เลือก--</option>
                                                @foreach ($targets as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('target_id', $client?->target_id) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->target_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('target_id')
                                                <small class="text-danger error-message"
                                                    id="error-target_id">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-4">
                                            <label for="contact_id" class="form-label">วิธีการติดต่อ <span
                                                    class="required-star">*</span></label>
                                            <select name="contact_id" id="contact_id"
                                                class="form-select @error('contact_id') is-invalid @enderror">
                                                <option value="">--เลือก--</option>
                                                @foreach ($contacts as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('contact_id', $client?->contact_id) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->contact_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('contact_id')
                                                <small class="text-danger error-message"
                                                    id="error-contact_id">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-4">
                                            <label for="project_id" class="form-label">
                                                หน่วยงาน <span class="required-star">*</span>
                                            </label>

                                            @if (auth()->user()->isAdmin() || auth()->user()->isExecutive())
                                                <select name="project_id" id="project_id"
                                                    class="form-select @error('project_id') is-invalid @enderror">

                                                    <option value="">--เลือก--</option>

                                                    @foreach ($projects as $item)
                                                        <option value="{{ $item->id }}"
                                                            {{ old('project_id', $client?->project_id) == $item->id ? 'selected' : '' }}>
                                                            {{ $item->project_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="hidden" name="project_id"
                                                    value="{{ auth()->user()->project_id }}">

                                                <input type="text" class="form-control"
                                                    value="{{ auth()->user()->project->project_name ?? '-' }}" readonly>
                                            @endif

                                            @error('project_id')
                                                <small class="text-danger error-message" id="error-project_id">
                                                    {{ $message }}
                                                </small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-4">
                                            <label for="house_id" class="form-label">
                                                สถานที่พักอาศัย <span class="required-star">*</span>
                                            </label>

                                            <select name="house_id" id="house_id"
                                                class="form-select @error('house_id') is-invalid @enderror">

                                                <option value="">--เลือก--</option>

                                                @php
                                                    // Controller กรองบ้านตามสิทธิ์เรียบร้อยแล้ว
                                                    $availableHouses = $houses;
                                                @endphp

                                                @foreach ($availableHouses as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('house_id', $client?->house_id) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->house_name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error('house_id')
                                                <small class="text-danger error-message" id="error-house_id">
                                                    {{ $message }}
                                                </small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-4">
                                            <label for="status_id" class="form-label">สถานะผู้เข้ารับ <span
                                                    class="required-star">*</span></label>
                                            <select name="status_id" id="status_id"
                                                class="form-select @error('status_id') is-invalid @enderror">
                                                <option value="">--เลือก--</option>
                                                @foreach ($statuses as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('status_id', $client?->status_id) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->status_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('status_id')
                                                <small class="text-danger error-message"
                                                    id="error-status_id">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-3">
                                            <label for="arrival_date" class="form-label">
                                                วันที่รับเข้า <span class="required-star">*</span>
                                            </label>

                                            <input type="date" name="arrival_date" id="arrival_date"
                                                class="form-control @error('arrival_date') is-invalid @enderror"
                                                value="{{ $arrivalDateValue }}">

                                            @error('arrival_date')
                                                <small class="text-danger d-block error-message" id="error-arrival_date">
                                                    {{ $message }}
                                                </small>
                                            @enderror
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-3">
                                            <label for="case_resident" class="form-label">สถานะอยู่อาศัย <span
                                                    class="required-star">*</span></label>
                                            <select name="case_resident" id="case_resident"
                                                class="form-select @error('case_resident') is-invalid @enderror" required>
                                                <option value="">--เลือกสถานะ--</option>
                                                <option value="Active"
                                                    {{ old('case_resident', $client->case_resident ?? '') === 'Active' ? 'selected' : '' }}>
                                                    อยู่อาศัย</option>
                                                <option value="Inactive"
                                                    {{ old('case_resident', $client->case_resident ?? '') === 'Inactive' ? 'selected' : '' }}>
                                                    ไม่อยู่อาศัย</option>
                                            </select>
                                            @error('case_resident')
                                                <small class="text-danger error-message"
                                                    id="error-case_resident">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-2">
                                    <div class="photo-box">
                                        <label class="form-label d-block text-center mb-2">ภาพถ่าย</label>

                                        <img id="showImage"
                                            src="{{ !empty($client->image)
                                                ? (route('client.image', $client->id) . '?v=' . substr(sha1((string) $client->image), 0, 12))
                                                : asset('upload/no_image.jpg') }}"
                                            alt="image profile"
                                            class="photo-preview d-block mx-auto"
                                            width="160"
                                            height="160"
                                            loading="eager"
                                            decoding="async"
                                            fetchpriority="high">

                                        <input type="file" name="image" id="image" class="d-none"
                                            accept=".jpg,.jpeg,.png,.gif,.webp">

                                        <button type="button" class="btn btn-light btn-sm photo-btn"
                                            onclick="document.getElementById('image').click()">
                                            เลือกรูปภาพ
                                        </button>
                                    </div>
                                </div>

                                <div class="col-12 mt-2">
                                    <label class="form-label">
                                        ปัญหาที่พบ <span class="required-star">* (เลือกได้มากกว่า 1 รายการ)</span>
                                    </label>

                                    <div class="problem-box">
                                        <div class="problem-grid">
                                            @foreach ($problems as $problem)
                                                <label class="problem-item" for="problem{{ $problem->id }}">
                                                    <input class="form-check-input" type="checkbox" name="problems[]"
                                                        value="{{ $problem->id }}" id="problem{{ $problem->id }}"
                                                        {{ in_array($problem->id, old('problems', $client?->problems->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
                                                    <span>{{ $problem->problem_name }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                        @error('problems')
                                            <div class="invalid-feedback d-block small-text">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="address-grid">
                            <div class="panel-box">
                                <div class="panel-title">ที่อยู่ปัจจุบัน</div>

                                <div class="row compact-row">
                                    <div class="col-md-6">
                                        <label for="address" class="form-label">ที่อยู่เลขที่</label>
                                        <input type="text" name="address" id="address" class="form-control"
                                            value="{{ old('address', $client?->address) }}">
                                        @error('address')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="moo" class="form-label">หมู่ที่</label>
                                        <input type="text" name="moo" id="moo" class="form-control"
                                            value="{{ old('moo', $client?->moo) }}">
                                        @error('moo')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="soi" class="form-label">ตรอก/ซอย</label>
                                        <input type="text" name="soi" id="soi" class="form-control"
                                            value="{{ old('soi', $client?->soi) }}">
                                        @error('soi')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="road" class="form-label">ถนน</label>
                                        <input type="text" name="road" id="road" class="form-control"
                                            value="{{ old('road', $client?->road) }}">
                                        @error('road')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label for="village" class="form-label">หมู่บ้าน</label>
                                        <input type="text" name="village" id="village" class="form-control"
                                            value="{{ old('village', $client?->village) }}">
                                        @error('village')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label for="province" class="form-label">จังหวัด</label>
                                        <select name="province_id" id="province" class="form-select">
                                            <option value="">--เลือกจังหวัด--</option>
                                            @foreach ($provinces as $province)
                                                <option value="{{ $province->id }}"
                                                    {{ old('province_id', $client?->province_id) == $province->id ? 'selected' : '' }}>
                                                    {{ $province->prov_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="district" class="form-label">เขต/อำเภอ</label>
                                        <select name="district_id" id="district" class="form-select">
                                            <option value="">--เลือกอำเภอ--</option>
                                            @foreach ($districts as $district)
                                                <option value="{{ $district->id }}"
                                                    {{ old('district_id', $client?->district_id) == $district->id ? 'selected' : '' }}>
                                                    {{ $district->dist_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="subdistrict" class="form-label">แขวง/ตำบล</label>
                                        <select name="sub_district_id" id="subdistrict" class="form-select">
                                            <option value="">--เลือกตำบล--</option>
                                            @foreach ($sub_districts as $subdistrict)
                                                <option value="{{ $subdistrict->id }}"
                                                    {{ old('sub_district_id', $client?->sub_district_id) == $subdistrict->id ? 'selected' : '' }}>
                                                    {{ $subdistrict->subd_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('sub_district_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="zipcode" class="form-label">รหัสไปรษณีย์</label>
                                        <input type="text" name="zipcode" id="zipcode" class="form-control"
                                            value="{{ old('zipcode', $client?->zipcode) }}">
                                        @error('zipcode')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-8">
                                        <label for="phone" class="form-label">โทรศัพท์</label>
                                        <input type="text" name="phone" id="phone" class="form-control"
                                            value="{{ old('phone', $client?->phone) }}">
                                        @error('phone')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="panel-box">
                                <div class="panel-title">ที่อยู่ตามทะเบียนบ้าน</div>

                                <div class="form-check copy-check">
                                    <input class="form-check-input" type="checkbox" id="sameAsCurrentAddress">
                                    <label class="form-check-label" for="sameAsCurrentAddress">
                                        ที่อยู่ปัจจุบันตรงกับที่อยู่ตามทะเบียนบ้าน
                                    </label>
                                </div>

                                <div class="row compact-row">
                                    <div class="col-md-6">
                                        <label for="origin_address" class="form-label">ที่อยู่เลขที่</label>
                                        <input type="text" name="origin_address" id="origin_address"
                                            class="form-control"
                                            value="{{ old('origin_address', $client?->origin_address) }}">
                                        @error('origin_address')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="origin_moo" class="form-label">หมู่ที่</label>
                                        <input type="text" name="origin_moo" id="origin_moo" class="form-control"
                                            value="{{ old('origin_moo', $client?->origin_moo) }}">
                                        @error('origin_moo')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="origin_soi" class="form-label">ตรอก/ซอย</label>
                                        <input type="text" name="origin_soi" id="origin_soi" class="form-control"
                                            value="{{ old('origin_soi', $client?->origin_soi) }}">
                                        @error('origin_soi')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="origin_road" class="form-label">ถนน</label>
                                        <input type="text" name="origin_road" id="origin_road" class="form-control"
                                            value="{{ old('origin_road', $client?->origin_road) }}">
                                        @error('origin_road')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label for="origin_village" class="form-label">หมู่บ้าน</label>
                                        <input type="text" name="origin_village" id="origin_village"
                                            class="form-control"
                                            value="{{ old('origin_village', $client?->origin_village) }}">
                                        @error('origin_village')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label" for="origin_province">จังหวัด</label>
                                        <select name="origin_province_id" id="origin_province" class="form-select">
                                            <option value="">--เลือกจังหวัด--</option>
                                            @foreach ($origin_provinces as $province)
                                                <option value="{{ $province->id }}"
                                                    {{ old('origin_province_id', $client?->origin_province_id) == $province->id ? 'selected' : '' }}>
                                                    {{ $province->prov_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label" for="origin_district">เขต/อำเภอ</label>
                                        <select name="origin_district_id" id="origin_district" class="form-select">
                                            <option value="">--เลือกอำเภอ--</option>
                                            @foreach ($origin_districts as $district)
                                                <option value="{{ $district->id }}"
                                                    {{ old('origin_district_id', $client?->origin_district_id) == $district->id ? 'selected' : '' }}>
                                                    {{ $district->dist_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label" for="origin_subdistrict">แขวง/ตำบล</label>
                                        <select name="origin_sub_district_id" id="origin_subdistrict"
                                            class="form-select">
                                            <option value="">--เลือกตำบล--</option>
                                            @foreach ($origin_sub_districts as $subdistrict)
                                                <option value="{{ $subdistrict->id }}"
                                                    {{ old('origin_sub_district_id', $client?->origin_sub_district_id) == $subdistrict->id ? 'selected' : '' }}>
                                                    {{ $subdistrict->subd_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('origin_sub_district_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="origin_zipcode" class="form-label">รหัสไปรษณีย์</label>
                                        <input type="text" name="origin_zipcode" id="origin_zipcode"
                                            class="form-control"
                                            value="{{ old('origin_zipcode', $client?->origin_zipcode) }}">
                                        @error('origin_zipcode')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-8">
                                        <label for="origin_phone" class="form-label">โทรศัพท์</label>
                                        <input type="text" name="origin_phone" id="origin_phone" class="form-control"
                                            value="{{ old('origin_phone', $client?->origin_phone) }}">
                                        @error('origin_phone')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="action-bar">
                            <button type="submit" class="btn client-form-action client-form-submit" id="client-edit-submit">
                                <i class="bi bi-check2-circle" aria-hidden="true"></i>
                                <span>บันทึกการแก้ไข</span>
                            </button>

                            <a href="{{ route('client.show') }}" class="btn client-form-action client-form-cancel">
                                <i class="bi bi-x-circle me-1"></i> ยกเลิก
                            </a>
                        </div>
                    </form>
                @elseif ($tab === 'family')
                    <div class="panel-box">
                        <div class="panel-title">รายละเอียดบิดา มารดา</div>
                        <div class="p-3">
                            ตอนนี้แท็บนี้พร้อมใช้งานในหน้าเดียวแล้ว<br>
                            ขั้นถัดไปค่อยย้ายฟอร์มจริงจากหน้า <strong>family.add</strong> เข้ามาวางในส่วนนี้
                        </div>
                    </div>
                @elseif ($tab === 'guardian')
                    <div class="panel-box">
                        <div class="panel-title">รายละเอียดผู้ปกครอง/ญาติ</div>
                        <div class="p-3">
                            ตอนนี้แท็บนี้พร้อมใช้งานในหน้าเดียวแล้ว<br>
                            ขั้นถัดไปค่อยย้ายฟอร์มจริงจากหน้า <strong>guardian.add</strong> เข้ามาวางในส่วนนี้
                        </div>
                    </div>
                @elseif ($tab === 'member')
                    <div class="panel-box">
                        <div class="panel-title">รายละเอียดครอบครัว</div>
                        <div class="p-3">
                            ตอนนี้แท็บนี้พร้อมใช้งานในหน้าเดียวแล้ว<br>
                            ขั้นถัดไปค่อยย้ายฟอร์มจริงจากหน้า <strong>member.create</strong> เข้ามาวางในส่วนนี้
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            'use strict';

            const form = document.getElementById('client-edit-form');
            if (!form) return;

            const endpoints = {
                districts: @json(url('/get-districts')),
                subdistricts: @json(url('/get-subdistricts')),
                zipcode: @json(url('/get-zipcode')),
                originDistricts: @json(url('/get-origin-districts')),
                originSubdistricts: @json(url('/get-origin-subdistricts')),
                originZipcode: @json(url('/get-origin-zipcode'))
            };

            const initialValues = {
                district: String(@json(old('district_id', $client->district_id ?? '')) ?? ''),
                subdistrict: String(@json(old('sub_district_id', $client->sub_district_id ?? '')) ?? ''),
                originDistrict: String(@json(old('origin_district_id', $client->origin_district_id ?? '')) ?? ''),
                originSubdistrict: String(@json(old('origin_sub_district_id', $client->origin_sub_district_id ?? '')) ?? '')
            };

            const cache = new Map();
            const requestVersions = new WeakMap();

            const current = {
                province: document.getElementById('province'),
                district: document.getElementById('district'),
                subdistrict: document.getElementById('subdistrict'),
                zipcode: document.getElementById('zipcode')
            };

            const origin = {
                province: document.getElementById('origin_province'),
                district: document.getElementById('origin_district'),
                subdistrict: document.getElementById('origin_subdistrict'),
                zipcode: document.getElementById('origin_zipcode')
            };

            async function fetchCached(baseUrl, id) {
                const key = `${baseUrl}:${id}`;

                if (!cache.has(key)) {
                    const request = fetch(`${baseUrl}/${encodeURIComponent(id)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(function (response) {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }
                        return response.json();
                    }).catch(function (error) {
                        cache.delete(key);
                        throw error;
                    });

                    cache.set(key, request);
                }

                return cache.get(key);
            }

            function resetSelect(select, placeholder) {
                if (!select) return;
                select.replaceChildren(new Option(placeholder, ''));
                select.value = '';
            }

            function hasOption(select, value) {
                if (!select || !value) return false;
                return Array.from(select.options).some(function (option) {
                    return String(option.value) === String(value);
                });
            }

            function fillSelect(select, items, labelKey, placeholder, selectedValue) {
                if (!select) return;

                const fragment = document.createDocumentFragment();
                fragment.appendChild(new Option(placeholder, ''));

                (Array.isArray(items) ? items : []).forEach(function (item) {
                    const option = new Option(item[labelKey] ?? '-', item.id);
                    fragment.appendChild(option);
                });

                select.replaceChildren(fragment);
                select.value = selectedValue ? String(selectedValue) : '';
            }

            async function loadDistricts(group, endpoint, selectedValue = '') {
                if (!group.province || !group.district) return;

                const provinceId = group.province.value;
                const version = (requestVersions.get(group.district) ?? 0) + 1;
                requestVersions.set(group.district, version);

                resetSelect(group.district, '--เลือกอำเภอ--');
                resetSelect(group.subdistrict, '--เลือกตำบล--');
                if (group.zipcode) group.zipcode.value = '';

                if (!provinceId) return;

                group.district.disabled = true;
                group.district.options[0].textContent = 'กำลังโหลดอำเภอ...';

                try {
                    const items = await fetchCached(endpoint, provinceId);
                    if (requestVersions.get(group.district) !== version) return;
                    fillSelect(group.district, items, 'dist_name', '--เลือกอำเภอ--', selectedValue);
                } catch (error) {
                    if (requestVersions.get(group.district) === version) {
                        resetSelect(group.district, '--โหลดอำเภอไม่สำเร็จ--');
                    }
                    console.error('Load districts failed:', error);
                } finally {
                    if (requestVersions.get(group.district) === version) {
                        group.district.disabled = false;
                    }
                }
            }

            async function loadSubdistricts(group, endpoint, selectedValue = '') {
                if (!group.district || !group.subdistrict) return;

                const districtId = group.district.value;
                const version = (requestVersions.get(group.subdistrict) ?? 0) + 1;
                requestVersions.set(group.subdistrict, version);

                resetSelect(group.subdistrict, '--เลือกตำบล--');
                if (group.zipcode) group.zipcode.value = '';

                if (!districtId) return;

                group.subdistrict.disabled = true;
                group.subdistrict.options[0].textContent = 'กำลังโหลดตำบล...';

                try {
                    const items = await fetchCached(endpoint, districtId);
                    if (requestVersions.get(group.subdistrict) !== version) return;
                    fillSelect(group.subdistrict, items, 'subd_name', '--เลือกตำบล--', selectedValue);
                } catch (error) {
                    if (requestVersions.get(group.subdistrict) === version) {
                        resetSelect(group.subdistrict, '--โหลดตำบลไม่สำเร็จ--');
                    }
                    console.error('Load subdistricts failed:', error);
                } finally {
                    if (requestVersions.get(group.subdistrict) === version) {
                        group.subdistrict.disabled = false;
                    }
                }
            }

            async function loadZipcode(group, endpoint, responseKey) {
                if (!group.subdistrict || !group.zipcode) return;

                const subdistrictId = group.subdistrict.value;
                group.zipcode.value = '';

                if (!subdistrictId) return;

                try {
                    const data = await fetchCached(endpoint, subdistrictId);
                    group.zipcode.value = data[responseKey] ?? data.zipcode ?? '';
                } catch (error) {
                    console.error('Load zipcode failed:', error);
                }
            }

            if (current.province) {
                current.province.addEventListener('change', function () {
                    loadDistricts(current, endpoints.districts);
                });
            }

            if (current.district) {
                current.district.addEventListener('change', function () {
                    loadSubdistricts(current, endpoints.subdistricts);
                });
            }

            if (current.subdistrict) {
                current.subdistrict.addEventListener('change', function () {
                    loadZipcode(current, endpoints.zipcode, 'zipcode');
                });
            }

            if (origin.province) {
                origin.province.addEventListener('change', function () {
                    loadDistricts(origin, endpoints.originDistricts);
                });
            }

            if (origin.district) {
                origin.district.addEventListener('change', function () {
                    loadSubdistricts(origin, endpoints.originSubdistricts);
                });
            }

            if (origin.subdistrict) {
                origin.subdistrict.addEventListener('change', function () {
                    loadZipcode(origin, endpoints.originZipcode, 'origin_zipcode');
                });
            }

            // รองรับ Controller รุ่นเดิม: โหลดเฉพาะเมื่อมีค่าที่เลือกไว้แต่ option ยังไม่ถูกส่งมา
            (async function restoreLocationOptions() {
                if (current.province?.value && initialValues.district && !hasOption(current.district, initialValues.district)) {
                    await loadDistricts(current, endpoints.districts, initialValues.district);
                }
                if (current.district?.value && initialValues.subdistrict && !hasOption(current.subdistrict, initialValues.subdistrict)) {
                    await loadSubdistricts(current, endpoints.subdistricts, initialValues.subdistrict);
                }
                if (origin.province?.value && initialValues.originDistrict && !hasOption(origin.district, initialValues.originDistrict)) {
                    await loadDistricts(origin, endpoints.originDistricts, initialValues.originDistrict);
                }
                if (origin.district?.value && initialValues.originSubdistrict && !hasOption(origin.subdistrict, initialValues.originSubdistrict)) {
                    await loadSubdistricts(origin, endpoints.originSubdistricts, initialValues.originSubdistrict);
                }
            })();

            const sameAddress = document.getElementById('sameAsCurrentAddress');
            if (sameAddress) {
                sameAddress.addEventListener('change', async function () {
                    const pairs = [
                        ['address', 'origin_address'],
                        ['moo', 'origin_moo'],
                        ['soi', 'origin_soi'],
                        ['road', 'origin_road'],
                        ['village', 'origin_village'],
                        ['phone', 'origin_phone']
                    ];

                    if (!this.checked) {
                        pairs.forEach(function (pair) {
                            const target = document.getElementById(pair[1]);
                            if (target) target.value = '';
                        });
                        if (origin.province) origin.province.value = '';
                        resetSelect(origin.district, '--เลือกอำเภอ--');
                        resetSelect(origin.subdistrict, '--เลือกตำบล--');
                        if (origin.zipcode) origin.zipcode.value = '';
                        return;
                    }

                    pairs.forEach(function (pair) {
                        const source = document.getElementById(pair[0]);
                        const target = document.getElementById(pair[1]);
                        if (source && target) target.value = source.value;
                    });

                    if (origin.province && current.province) {
                        origin.province.value = current.province.value;
                        await loadDistricts(origin, endpoints.originDistricts, current.district?.value ?? '');
                        await loadSubdistricts(origin, endpoints.originSubdistricts, current.subdistrict?.value ?? '');
                    }

                    if (origin.zipcode && current.zipcode) {
                        origin.zipcode.value = current.zipcode.value;
                    }
                });
            }

            // ใช้ event delegation เพียงชุดเดียว ลด listener จำนวนมาก
            function clearValidation(event) {
                const input = event.target.closest('input, select, textarea');
                if (!input || !form.contains(input)) return;

                if (input.type === 'radio' && input.name) {
                    form.querySelectorAll(`input[name="${CSS.escape(input.name)}"]`).forEach(function (radio) {
                        radio.classList.remove('is-invalid');
                    });
                } else {
                    input.classList.remove('is-invalid');
                }

                if (input.name) {
                    const errorById = document.getElementById('error-' + input.name);
                    if (errorById) errorById.remove();
                }

                const wrapper = input.closest('.col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-12, [class*="col-"]');
                wrapper?.querySelectorAll('.invalid-feedback, .error-message').forEach(function (feedback) {
                    feedback.style.display = 'none';
                });
            }

            form.addEventListener('input', clearValidation);
            form.addEventListener('change', clearValidation);

            const idCard = document.getElementById('id_card');
            function formatThaiId(value) {
                const digits = String(value ?? '').replace(/\D/g, '').slice(0, 13);
                return [
                    digits.slice(0, 1),
                    digits.slice(1, 5),
                    digits.slice(5, 10),
                    digits.slice(10, 12),
                    digits.slice(12, 13)
                ].filter(Boolean).join('-');
            }

            if (idCard) {
                idCard.value = formatThaiId(idCard.value);
                idCard.addEventListener('input', function () {
                    this.value = formatThaiId(this.value);
                });
            }

            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('showImage');
            const sidebarImagePreview = document.getElementById('sidebarClientAvatarImage');
            let previewUrl = null;

            if (imageInput && imagePreview) {
                const allowedImageTypes = new Set([
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'image/webp'
                ]);
                const maxImageBytes = 2 * 1024 * 1024;

                imageInput.addEventListener('change', function () {
                    const file = this.files?.[0];
                    if (!file) return;

                    if (!allowedImageTypes.has(file.type) || file.size > maxImageBytes) {
                        this.value = '';

                        if (window.Swal) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'ไม่สามารถใช้รูปนี้ได้',
                                text: file.size > maxImageBytes
                                    ? 'รูปภาพต้องมีขนาดไม่เกิน 2MB'
                                    : 'รองรับเฉพาะไฟล์ JPG, JPEG, PNG, GIF และ WEBP',
                                confirmButtonText: 'ตกลง'
                            });
                        }

                        return;
                    }

                    if (previewUrl) URL.revokeObjectURL(previewUrl);
                    previewUrl = URL.createObjectURL(file);
                    imagePreview.src = previewUrl;

                    // CLIENT_IMAGE_PREVIEW_SYNC_V8: ให้ภาพใน Sidebar เปลี่ยนพร้อม Preview ก่อนบันทึก
                    if (sidebarImagePreview) {
                        sidebarImagePreview.src = previewUrl;
                    }
                });

                window.addEventListener('pagehide', function () {
                    if (previewUrl) URL.revokeObjectURL(previewUrl);
                }, { once: true });
            }

            const submitButton = document.getElementById('client-edit-submit');
            form.addEventListener('submit', function () {
                if (!submitButton) return;

                submitButton.classList.add('is-submitting');
                submitButton.disabled = true;
                submitButton.setAttribute('aria-busy', 'true');
                submitButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    <span>กำลังบันทึก...</span>
                `;
            });

            const successMessage = @json(session('success'));
            const errorMessage = @json(session('error'));

            if (successMessage && window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: successMessage,
                    timer: 1800,
                    showConfirmButton: false
                });
            } else if (errorMessage && window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: errorMessage,
                    confirmButtonText: 'ตกลง'
                });
            }
        });
    </script>

@endsection