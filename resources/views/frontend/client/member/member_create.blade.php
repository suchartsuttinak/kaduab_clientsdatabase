@extends('admin_client.admin_client')
@section('content')

<link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

@php
    $formMembers = old('members', []);

    if (empty($formMembers)) {
        $formMembers = [[
            'fullname' => '',
            'member_age' => '',
            'education_id' => '',
            'relationship' => '',
            'occupation_id' => '',
            'income_id' => '',
            'remark' => '',
        ]];
    }
@endphp


<style>
    .member-form-page {
        --mf-primary: #245b91;
        --mf-primary-dark: #1b466f;
        --mf-text: #1e293b;
        --mf-muted: #64748b;
        --mf-line: #dce5ee;
        --mf-soft: #f7fafc;
        padding: 1rem;
        color: var(--mf-text);
    }

    .member-form-shell {
        overflow: hidden;
        border: 1px solid var(--mf-line);
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 12px 32px rgba(15, 23, 42, .06);
    }

    .member-form-header {
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        border-bottom: 1px solid var(--mf-line);
        background: linear-gradient(135deg, #fff 0%, #f4f8fc 100%);
    }

    .member-form-kicker {
        display: block;
        margin-bottom: .22rem;
        color: #52749a;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .08em;
    }

    .member-form-title {
        margin: 0;
        color: #172033;
        font-size: 1.2rem;
        font-weight: 800;
    }

    .member-required-note {
        padding: .48rem .72rem;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        border: 1px solid #fde2e2;
        border-radius: 999px;
        background: #fff7f7;
        color: #b42318;
        font-size: .78rem;
        font-weight: 700;
    }

    .member-form-body {
        padding: 1.15rem 1.25rem 1.3rem;
    }

    .member-client-summary {
        margin-bottom: 1rem;
        padding: .9rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .8rem 1.5rem;
        flex-wrap: wrap;
        border: 1px solid #e4ebf2;
        border-radius: 14px;
        background: var(--mf-soft);
    }

    .member-client-summary-item span {
        display: block;
        margin-bottom: .15rem;
        color: var(--mf-muted);
        font-size: .74rem;
    }

    .member-client-summary-item strong {
        color: #263548;
        font-size: .92rem;
        font-weight: 750;
    }

    .member-validation-alert {
        margin-bottom: 1rem;
        padding: .85rem 1rem;
        border: 1px solid #fecaca;
        border-radius: 13px;
        background: #fff7f7;
        color: #991b1b;
    }

    .member-validation-alert strong {
        display: flex;
        align-items: center;
        gap: .45rem;
        margin-bottom: .35rem;
        font-size: .9rem;
    }

    .member-validation-alert ul {
        margin: 0;
        padding-left: 1.3rem;
        font-size: .82rem;
    }

    .member-section-head {
        margin-bottom: .7rem;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .member-section-head h2 {
        margin: 0;
        color: #263548;
        font-size: 1rem;
        font-weight: 800;
    }

    .member-section-head p {
        margin: .2rem 0 0;
        color: var(--mf-muted);
        font-size: .78rem;
    }

    .member-table-wrap {
        overflow-x: auto;
        border: 1px solid var(--mf-line);
        border-radius: 15px;
        background: #fff;
        -webkit-overflow-scrolling: touch;
    }

    .member-entry-table {
        min-width: 1180px;
        margin: 0;
    }

    .member-entry-table thead th {
        padding: .75rem .65rem;
        border-color: #dce6f0;
        background: #edf4fb;
        color: #294d73;
        font-size: .78rem;
        font-weight: 800;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    .member-entry-table tbody td {
        padding: .65rem;
        border-color: #e6ebf1;
        background: #fff;
        vertical-align: top;
    }

    .member-entry-table tbody tr:nth-child(even) td {
        background: #fbfcfe;
    }

    .member-entry-table .form-control,
    .member-entry-table .form-select {
        min-height: 42px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background-color: #fff;
        color: #1e293b;
        font-size: .84rem;
    }

    .member-entry-table .form-control:focus,
    .member-entry-table .form-select:focus {
        border-color: #6d94ba;
        box-shadow: 0 0 0 3px rgba(36, 91, 145, .12);
    }

    .member-entry-table .form-control.is-invalid,
    .member-entry-table .form-select.is-invalid {
        border-color: #dc3545;
        background-image: none;
        padding-right: .65rem;
    }

    .member-entry-table .invalid-feedback {
        margin-top: .3rem;
        font-size: .72rem;
        line-height: 1.35;
    }

    .member-required-mark {
        color: #dc2626;
    }

    .member-remove-btn {
        width: 38px;
        height: 38px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #fecaca;
        border-radius: 10px;
        background: #fff;
        color: #dc2626;
        transition: .18s ease;
    }

    .member-remove-btn:hover,
    .member-remove-btn:focus {
        border-color: #ef4444;
        background: #fef2f2;
        color: #b91c1c;
        box-shadow: 0 5px 12px rgba(220, 38, 38, .12);
        transform: translateY(-1px);
    }

    .member-form-actions {
        margin-top: 1rem;
        padding-top: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        flex-wrap: wrap;
        border-top: 1px solid #e8edf2;
    }

    .member-form-actions-left,
    .member-form-actions-right {
        display: flex;
        align-items: center;
        gap: .65rem;
        flex-wrap: wrap;
    }

    .member-action-btn {
        min-width: 132px;
        min-height: 44px;
        padding: .65rem 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .48rem;
        border-radius: 12px;
        font-size: .88rem;
        font-weight: 750;
        text-decoration: none;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
    }

    .member-action-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .member-add-btn {
        border: 1px solid #aac1d8;
        background: #f7fbff;
        color: #245b91;
    }

    .member-add-btn:hover,
    .member-add-btn:focus {
        border-color: #7399bd;
        background: #edf5fc;
        color: #1b466f;
        box-shadow: 0 7px 16px rgba(36, 91, 145, .12);
    }

    .member-cancel-btn {
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #475569;
    }

    .member-cancel-btn:hover,
    .member-cancel-btn:focus {
        border-color: #94a3b8;
        background: #f1f5f9;
        color: #1e293b;
        box-shadow: 0 6px 14px rgba(15, 23, 42, .08);
    }

    .member-submit-btn {
        border: 1px solid #1d4f7f;
        background: linear-gradient(135deg, #2d6ca8 0%, #245b91 100%);
        color: #fff;
        box-shadow: 0 8px 18px rgba(36, 91, 145, .2);
    }

    .member-submit-btn:hover,
    .member-submit-btn:focus {
        border-color: #163f66;
        background: linear-gradient(135deg, #245b91 0%, #1b466f 100%);
        color: #fff;
        box-shadow: 0 10px 22px rgba(36, 91, 145, .26);
    }

    .member-action-btn:focus-visible,
    .member-remove-btn:focus-visible {
        outline: 0;
        box-shadow: 0 0 0 4px rgba(36, 91, 145, .15);
    }

    @media (max-width: 767.98px) {
        .member-form-page {
            padding: .75rem;
        }

        .member-form-header,
        .member-form-body {
            padding: 1rem;
        }

        .member-form-actions,
        .member-form-actions-left,
        .member-form-actions-right {
            width: 100%;
        }

        .member-form-actions-right {
            flex-direction: row;
        }

        .member-action-btn {
            min-width: 0;
            flex: 1 1 0;
        }

        .member-form-actions-left .member-action-btn {
            width: 100%;
            flex-basis: 100%;
        }
    }

    @media (max-width: 480px) {
        .member-form-actions-right {
            flex-direction: column-reverse;
        }

        .member-form-actions-right .member-action-btn {
            width: 100%;
            flex-basis: auto;
        }
    }
</style>


<div class="member-form-page">
    <div class="member-form-shell">
        <div class="member-form-header">
            <div>
                <span class="member-form-kicker">ADD FAMILY MEMBER</span>
                <h1 class="member-form-title">เพิ่มข้อมูลสมาชิกในครอบครัว</h1>
            </div>

            <span class="member-required-note">
                <i class="bi bi-asterisk"></i>
                ทุกช่องต้องกรอก ยกเว้นหมายเหตุ
            </span>
        </div>

        <form action="{{ route('member.store') }}"
              method="POST"
              id="member-form"
              novalidate>
            @csrf
            <input type="hidden" name="client_id" value="{{ $client->id }}">

            <div class="member-form-body">
                <div class="member-client-summary">
                    <div class="member-client-summary-item">
                        <span>ชื่อ - นามสกุลผู้รับบริการ</span>
                        <strong>{{ $client->fullname }}</strong>
                    </div>

                    <div class="member-client-summary-item">
                        <span>อายุ</span>
                        <strong>{{ $client->age }} ปี</strong>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="member-validation-alert" role="alert">
                        <strong>
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            กรุณาตรวจสอบข้อมูลที่กรอก
                        </strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="member-section-head">
                    <div>
                        <h2>รายละเอียดสมาชิกในครอบครัว</h2>
                        <p>สามารถเพิ่มสมาชิกได้มากกว่า 1 คน และต้องเหลืออย่างน้อย 1 รายการ</p>
                    </div>
                </div>

                <div class="member-table-wrap">
                    <table class="table table-bordered align-middle member-entry-table">
                        <thead>
                            <tr>
                                <th style="width: 16%;">ชื่อ - นามสกุล <span class="member-required-mark">*</span></th>
                                <th style="width: 8%;">อายุ <span class="member-required-mark">*</span></th>
                                <th style="width: 15%;">การศึกษา <span class="member-required-mark">*</span></th>
                                <th style="width: 16%;">ความสัมพันธ์ <span class="member-required-mark">*</span></th>
                                <th style="width: 15%;">อาชีพ <span class="member-required-mark">*</span></th>
                                <th style="width: 15%;">รายได้/เดือน <span class="member-required-mark">*</span></th>
                                <th style="width: 12%;">หมายเหตุ</th>
                                <th style="width: 6%;">ลบ</th>
                            </tr>
                        </thead>
                        <tbody id="member-container">
                            @foreach ($formMembers as $i => $member)
                                <tr class="member-item" data-index="{{ $i }}">
                                    <td>
                                        <input type="text"
                                               name="members[{{ $i }}][fullname]"
                                               value="{{ old("members.$i.fullname", $member['fullname'] ?? '') }}"
                                               class="form-control form-control-sm @error("members.$i.fullname") is-invalid @enderror"
                                               maxlength="255"
                                               placeholder="ชื่อ - นามสกุล"
                                               required>
                                        @error("members.$i.fullname")
                                            <div class="invalid-feedback d-block member-server-error">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="members[{{ $i }}][member_age]"
                                               value="{{ old("members.$i.member_age", $member['member_age'] ?? '') }}"
                                               class="form-control form-control-sm @error("members.$i.member_age") is-invalid @enderror"
                                               min="0"
                                               max="150"
                                               step="1"
                                               placeholder="อายุ"
                                               required>
                                        @error("members.$i.member_age")
                                            <div class="invalid-feedback d-block member-server-error">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    <td>
                                        <select name="members[{{ $i }}][education_id]"
                                                class="form-select form-select-sm @error("members.$i.education_id") is-invalid @enderror"
                                                required>
                                            <option value="">-- เลือกการศึกษา --</option>
                                            @foreach ($educations as $education)
                                                <option value="{{ $education->id }}"
                                                    @selected((string) old("members.$i.education_id", $member['education_id'] ?? '') === (string) $education->id)>
                                                    {{ $education->education_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("members.$i.education_id")
                                            <div class="invalid-feedback d-block member-server-error">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    <td>
                                        <input type="text"
                                               name="members[{{ $i }}][relationship]"
                                               value="{{ old("members.$i.relationship", $member['relationship'] ?? '') }}"
                                               class="form-control form-control-sm @error("members.$i.relationship") is-invalid @enderror"
                                               maxlength="100"
                                               placeholder="เช่น บิดา มารดา"
                                               required>
                                        @error("members.$i.relationship")
                                            <div class="invalid-feedback d-block member-server-error">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    <td>
                                        <select name="members[{{ $i }}][occupation_id]"
                                                class="form-select form-select-sm @error("members.$i.occupation_id") is-invalid @enderror"
                                                required>
                                            <option value="">-- เลือกอาชีพ --</option>
                                            @foreach ($occupations as $occupation)
                                                <option value="{{ $occupation->id }}"
                                                    @selected((string) old("members.$i.occupation_id", $member['occupation_id'] ?? '') === (string) $occupation->id)>
                                                    {{ $occupation->occupation_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("members.$i.occupation_id")
                                            <div class="invalid-feedback d-block member-server-error">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    <td>
                                        <select name="members[{{ $i }}][income_id]"
                                                class="form-select form-select-sm @error("members.$i.income_id") is-invalid @enderror"
                                                required>
                                            <option value="">-- เลือกรายได้ --</option>
                                            @foreach ($incomes as $income)
                                                <option value="{{ $income->id }}"
                                                    @selected((string) old("members.$i.income_id", $member['income_id'] ?? '') === (string) $income->id)>
                                                    {{ $income->income_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("members.$i.income_id")
                                            <div class="invalid-feedback d-block member-server-error">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    <td>
                                        <input type="text"
                                               name="members[{{ $i }}][remark]"
                                               value="{{ old("members.$i.remark", $member['remark'] ?? '') }}"
                                               class="form-control form-control-sm @error("members.$i.remark") is-invalid @enderror"
                                               maxlength="255"
                                               placeholder="ไม่บังคับ">
                                        @error("members.$i.remark")
                                            <div class="invalid-feedback d-block member-server-error">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    <td class="text-center">
                                        <button type="button"
                                                class="member-remove-btn remove-member"
                                                aria-label="ลบสมาชิก">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="member-form-actions">
                    <div class="member-form-actions-left">
                        <button type="button"
                                class="member-action-btn member-add-btn"
                                id="add-member">
                            <i class="bi bi-person-plus"></i>
                            <span>เพิ่มสมาชิก</span>
                        </button>
                    </div>

                    <div class="member-form-actions-right">
                        <a href="{{ url()->previous() }}"
                           class="member-action-btn member-cancel-btn">
                            <i class="bi bi-x-lg"></i>
                            <span>ยกเลิก</span>
                        </a>

                        <button type="submit"
                                class="member-action-btn member-submit-btn"
                                data-loading-text="กำลังบันทึก...">
                            <i class="bi bi-check2-circle"></i>
                            <span>บันทึกข้อมูล</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('member-form');
    const container = document.getElementById('member-container');
    const addButton = document.getElementById('add-member');

    const educationItems = @json($educations->map(fn ($item) => ['id' => $item->id, 'name' => $item->education_name])->values());
    const occupationItems = @json($occupations->map(fn ($item) => ['id' => $item->id, 'name' => $item->occupation_name])->values());
    const incomeItems = @json($incomes->map(fn ($item) => ['id' => $item->id, 'name' => $item->income_name])->values());

    let nextIndex = {{ count($formMembers) }};

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function buildOptions(items, placeholder) {
        return `<option value="">${escapeHtml(placeholder)}</option>` + items.map(item =>
            `<option value="${escapeHtml(item.id)}">${escapeHtml(item.name)}</option>`
        ).join('');
    }

    function showNotice(title, text, icon = 'warning') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon,
                title,
                text,
                confirmButtonText: 'ตกลง',
                confirmButtonColor: '#245b91'
            });
            return;
        }

        window.alert(text);
    }

    function memberRow(index) {
        return `
            <tr class="member-item" data-index="${index}">
                <td>
                    <input type="text"
                           name="members[${index}][fullname]"
                           class="form-control form-control-sm"
                           maxlength="255"
                           placeholder="ชื่อ - นามสกุล"
                           required>
                </td>
                <td>
                    <input type="number"
                           name="members[${index}][member_age]"
                           class="form-control form-control-sm"
                           min="0"
                           max="150"
                           step="1"
                           placeholder="อายุ"
                           required>
                </td>
                <td>
                    <select name="members[${index}][education_id]"
                            class="form-select form-select-sm"
                            required>
                        ${buildOptions(educationItems, '-- เลือกการศึกษา --')}
                    </select>
                </td>
                <td>
                    <input type="text"
                           name="members[${index}][relationship]"
                           class="form-control form-control-sm"
                           maxlength="100"
                           placeholder="เช่น บิดา มารดา"
                           required>
                </td>
                <td>
                    <select name="members[${index}][occupation_id]"
                            class="form-select form-select-sm"
                            required>
                        ${buildOptions(occupationItems, '-- เลือกอาชีพ --')}
                    </select>
                </td>
                <td>
                    <select name="members[${index}][income_id]"
                            class="form-select form-select-sm"
                            required>
                        ${buildOptions(incomeItems, '-- เลือกรายได้ --')}
                    </select>
                </td>
                <td>
                    <input type="text"
                           name="members[${index}][remark]"
                           class="form-control form-control-sm"
                           maxlength="255"
                           placeholder="ไม่บังคับ">
                </td>
                <td class="text-center">
                    <button type="button"
                            class="member-remove-btn remove-member"
                            aria-label="ลบสมาชิก">
                        <i class="bi bi-trash3"></i>
                    </button>
                </td>
            </tr>`;
    }

    function clearFieldError(field) {
        field.classList.remove('is-invalid');
        const cell = field.closest('td');
        cell?.querySelectorAll('.member-client-error').forEach(error => error.remove());
        cell?.querySelectorAll('.member-server-error').forEach(error => error.style.display = 'none');
    }

    function setFieldError(field, message) {
        clearFieldError(field);
        field.classList.add('is-invalid');

        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback d-block member-client-error';
        feedback.textContent = message;
        field.insertAdjacentElement('afterend', feedback);
    }

    function validateForm() {
        let valid = true;
        let firstInvalid = null;
        const rows = container.querySelectorAll('.member-item');

        if (rows.length === 0) {
            showNotice('ยังไม่มีสมาชิก', 'กรุณาเพิ่มข้อมูลสมาชิกในครอบครัวอย่างน้อย 1 คน');
            return false;
        }

        rows.forEach(row => {
            const fullname = row.querySelector('[name$="[fullname]"]');
            const age = row.querySelector('[name$="[member_age]"]');
            const education = row.querySelector('[name$="[education_id]"]');
            const relationship = row.querySelector('[name$="[relationship]"]');
            const occupation = row.querySelector('[name$="[occupation_id]"]');
            const income = row.querySelector('[name$="[income_id]"]');

            [fullname, age, education, relationship, occupation, income].forEach(field => {
                if (field) clearFieldError(field);
            });

            if (!fullname.value.trim()) {
                setFieldError(fullname, 'กรุณากรอกชื่อ - นามสกุล');
                firstInvalid ??= fullname;
                valid = false;
            }

            if (age.value === '') {
                setFieldError(age, 'กรุณากรอกอายุ');
                firstInvalid ??= age;
                valid = false;
            } else if (!Number.isInteger(Number(age.value)) || Number(age.value) < 0 || Number(age.value) > 150) {
                setFieldError(age, 'กรุณากรอกอายุเป็นจำนวนเต็มระหว่าง 0 - 150 ปี');
                firstInvalid ??= age;
                valid = false;
            }

            if (!education.value) {
                setFieldError(education, 'กรุณาเลือกระดับการศึกษา');
                firstInvalid ??= education;
                valid = false;
            }

            if (!relationship.value.trim()) {
                setFieldError(relationship, 'กรุณากรอกความสัมพันธ์กับผู้รับบริการ');
                firstInvalid ??= relationship;
                valid = false;
            }

            if (!occupation.value) {
                setFieldError(occupation, 'กรุณาเลือกอาชีพ');
                firstInvalid ??= occupation;
                valid = false;
            }

            if (!income.value) {
                setFieldError(income, 'กรุณาเลือกรายได้เฉลี่ยต่อเดือน');
                firstInvalid ??= income;
                valid = false;
            }
        });

        if (!valid && firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => firstInvalid.focus({ preventScroll: true }), 250);
            showNotice('กรอกข้อมูลไม่ครบ', 'กรุณาตรวจสอบช่องที่มีกรอบสีแดง โดยกรอกทุกช่องยกเว้นหมายเหตุ');
        }

        return valid;
    }

    addButton?.addEventListener('click', function () {
        container.insertAdjacentHTML('beforeend', memberRow(nextIndex));
        const newRow = container.querySelector(`.member-item[data-index="${nextIndex}"]`);
        nextIndex += 1;
        newRow?.querySelector('input')?.focus();
    });

    container.addEventListener('click', function (event) {
        const removeButton = event.target.closest('.remove-member');
        if (!removeButton) return;

        const rows = container.querySelectorAll('.member-item');
        if (rows.length <= 1) {
            showNotice('ไม่สามารถลบได้', 'ต้องมีข้อมูลสมาชิกในครอบครัวอย่างน้อย 1 คน');
            return;
        }

        removeButton.closest('.member-item')?.remove();
    });

    container.addEventListener('input', function (event) {
        if (event.target.matches('.form-control, .form-select')) {
            clearFieldError(event.target);
        }
    });

    container.addEventListener('change', function (event) {
        if (event.target.matches('.form-control, .form-select')) {
            clearFieldError(event.target);
        }
    });

    form?.addEventListener('submit', function (event) {
        if (!validateForm()) {
            event.preventDefault();
            event.stopPropagation();
            return;
        }

        const submitButton = form.querySelector('.member-submit-btn');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span>${submitButton.dataset.loadingText || 'กำลังบันทึก...'}</span>`;
        }
    });

    const firstServerInvalid = form?.querySelector('.is-invalid');
    if (firstServerInvalid) {
        setTimeout(() => firstServerInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' }), 150);
    }
});
</script>

@endsection