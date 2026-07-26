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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                    <form action="{{ route('client.update') }}" method="POST" enctype="multipart/form-data">
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
                                                    $authUser = auth()->user();

                                                    $availableHouses =
                                                        $authUser->isAdmin() || $authUser->isExecutive()
                                                            ? $houses
                                                            : $houses->whereIn('id', $authUser->accessibleHouseIds());
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
                                            src="{{ !empty($client->image) ? asset('upload/client_images/' . $client->image) : asset('upload/no_image.jpg') }}"
                                            alt="image profile" class="photo-preview d-block mx-auto">

                                        <input type="file" name="image" id="image" class="d-none"
                                            accept=".jpg,.jpeg,.png,.webp">

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
                                <div class="panel-title">ภูมิลำเนาเดิม</div>

                                <div class="form-check copy-check">
                                    <input class="form-check-input" type="checkbox" id="sameAsCurrentAddress">
                                    <label class="form-check-label" for="sameAsCurrentAddress">
                                        ที่อยู่ปัจจุบันตรงกับภูมิลำเนาเดิม
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
                            <button type="submit" class="btn btn-success btn-registry">
                                <i class="bi bi-check-circle me-1"></i> แก้ไขข้อมูล
                            </button>

                            <a href="{{ route('client.show') }}" class="btn btn-danger btn-registry">
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
        $(function() {
            $('#province').on('change', function() {
                let province_id = $(this).val();

                if (province_id) {
                    $.get('/get-districts/' + province_id, function(data) {
                        $('#district').empty().append('<option value="">--เลือกอำเภอ--</option>');
                        $.each(data, function(key, value) {
                            $('#district').append('<option value="' + value.id + '">' +
                                value.dist_name + '</option>');
                        });

                        $('#subdistrict').empty().append('<option value="">--เลือกตำบล--</option>');
                        $('#zipcode').val('');
                    });
                } else {
                    $('#district').empty().append('<option value="">--เลือกอำเภอ--</option>');
                    $('#subdistrict').empty().append('<option value="">--เลือกตำบล--</option>');
                    $('#zipcode').val('');
                }
            });

            $('#district').on('change', function() {
                let district_id = $(this).val();

                if (district_id) {
                    $.get('/get-subdistricts/' + district_id, function(data) {
                        $('#subdistrict').empty().append('<option value="">--เลือกตำบล--</option>');
                        $.each(data, function(key, value) {
                            $('#subdistrict').append('<option value="' + value.id + '">' +
                                value.subd_name + '</option>');
                        });
                        $('#zipcode').val('');
                    });
                } else {
                    $('#subdistrict').empty().append('<option value="">--เลือกตำบล--</option>');
                    $('#zipcode').val('');
                }
            });

            $('#subdistrict').on('change', function() {
                let subdistrict_id = $(this).val();

                if (subdistrict_id) {
                    $.get('/get-zipcode/' + subdistrict_id, function(data) {
                        $('#zipcode').val(data.zipcode);
                    });
                } else {
                    $('#zipcode').val('');
                }
            });

            $('#origin_province').on('change', function() {
                let province_id = $(this).val();
                if (province_id) {
                    $.get('/get-origin-districts/' + province_id, function(data) {
                        $('#origin_district').empty().append(
                            '<option value="">--เลือกอำเภอ--</option>');
                        $.each(data, function(key, value) {
                            $('#origin_district').append('<option value="' + value.id +
                                '">' + value.dist_name + '</option>');
                        });
                        $('#origin_subdistrict').empty().append(
                            '<option value="">--เลือกตำบล--</option>');
                        $('#origin_zipcode').val('');
                    });
                } else {
                    $('#origin_district').empty().append('<option value="">--เลือกอำเภอ--</option>');
                    $('#origin_subdistrict').empty().append('<option value="">--เลือกตำบล--</option>');
                    $('#origin_zipcode').val('');
                }
            });

            $('#origin_district').on('change', function() {
                let district_id = $(this).val();
                if (district_id) {
                    $.get('/get-origin-subdistricts/' + district_id, function(data) {
                        $('#origin_subdistrict').empty().append(
                            '<option value="">--เลือกตำบล--</option>');
                        $.each(data, function(key, value) {
                            $('#origin_subdistrict').append('<option value="' + value.id +
                                '">' + value.subd_name + '</option>');
                        });
                        $('#origin_zipcode').val('');
                    });
                } else {
                    $('#origin_subdistrict').empty().append('<option value="">--เลือกตำบล--</option>');
                    $('#origin_zipcode').val('');
                }
            });

            $('#origin_subdistrict').on('change', function() {
                let subdistrict_id = $(this).val();
                if (subdistrict_id) {
                    $.get('/get-origin-zipcode/' + subdistrict_id, function(data) {
                        $('#origin_zipcode').val(data.origin_zipcode ?? data.zipcode ?? '');
                    });
                } else {
                    $('#origin_zipcode').val('');
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#image').change(function(e) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#showImage').attr('src', e.target.result);
                }
                reader.readAsDataURL(e.target.files['0']);
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input, select, textarea');

            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    const errorElement = document.getElementById('error-' + input.name);
                    if (errorElement) {
                        errorElement.remove();
                    }

                    if (input.type === 'radio') {
                        document.querySelectorAll('input[name="' + input.name + '"]').forEach(
                            radio => {
                                radio.classList.remove('is-invalid');
                            });
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                input.addEventListener('change', function() {
                    const errorElement = document.getElementById('error-' + input.name);
                    if (errorElement) {
                        errorElement.remove();
                    }

                    if (input.type === 'radio') {
                        document.querySelectorAll('input[name="' + input.name + '"]').forEach(
                            radio => {
                                radio.classList.remove('is-invalid');
                            });
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
            });
        });
    </script>

    <script>
        $(function() {
            $('#sameAsCurrentAddress').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#origin_address').val($('#address').val());
                    $('#origin_moo').val($('#moo').val());
                    $('#origin_soi').val($('#soi').val());
                    $('#origin_road').val($('#road').val());
                    $('#origin_village').val($('#village').val());
                    $('#origin_zipcode').val($('#zipcode').val());
                    $('#origin_phone').val($('#phone').val());

                    let province_id = $('#province').val();
                    $('#origin_province').val(province_id).trigger('change');

                    setTimeout(function() {
                        let district_id = $('#district').val();
                        $('#origin_district').val(district_id).trigger('change');
                    }, 500);

                    setTimeout(function() {
                        let subdistrict_id = $('#subdistrict').val();
                        $('#origin_subdistrict').val(subdistrict_id).trigger('change');
                    }, 1000);
                } else {
                    $('#origin_address').val('');
                    $('#origin_moo').val('');
                    $('#origin_soi').val('');
                    $('#origin_road').val('');
                    $('#origin_village').val('');
                    $('#origin_zipcode').val('');
                    $('#origin_phone').val('');
                    $('#origin_province').val('').trigger('change');
                    $('#origin_district').val('').trigger('change');
                    $('#origin_subdistrict').val('').trigger('change');
                }
            });
        });
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '{{ session('success') }}',
                showConfirmButton: true,
                timer: 2000
            });
        </script>
    @endif

    <!-- JavaScript สำหรับจัดรูปแบบเลขบัตรประชาชน -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const idCard = document.getElementById('id_card');

            function formatThaiId(value) {
                value = value.replace(/\D/g, '').substring(0, 13);

                if (value.length > 1) {
                    value = value.substring(0, 1) + '-' + value.substring(1);
                }
                if (value.length > 6) {
                    value = value.substring(0, 6) + '-' + value.substring(6);
                }
                if (value.length > 12) {
                    value = value.substring(0, 12) + '-' + value.substring(12);
                }
                if (value.length > 15) {
                    value = value.substring(0, 15) + '-' + value.substring(15);
                }

                return value;
            }

            if (idCard) {

                // โหลดข้อมูลเดิมตอน edit ให้จัด format อัตโนมัติ
                idCard.value = formatThaiId(idCard.value);

                // ตอนพิมพ์ให้ใส่ขีดอัตโนมัติ
                idCard.addEventListener('input', function() {
                    this.value = formatThaiId(this.value);
                });

            }

        });
    </script>

@endsection
