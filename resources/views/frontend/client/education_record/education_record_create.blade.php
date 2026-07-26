@extends('admin_client.admin_client')

@section('content')

<link rel="stylesheet" href="{{ asset('backend/assets/css/education-record-add.css') }}">

<div class="container-fluid edurec-create-page">
    <div class="edurec-wrap">

     {{-- ✅ แสดงข้อความแจ้งเตือน --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <style>
            div.swal2-popup.swal2-toast.edurec-toast{
                width:420px !important;
                padding:1rem 1.2rem !important;
                border-radius:18px !important;
                background:linear-gradient(135deg,#fff7d6 0%, #fff3bf 100%) !important;
                border-left:6px solid #f59e0b !important;
                box-shadow:0 12px 32px rgba(245,158,11,.25)!important;
            }

            .edurec-toast .swal2-title{
                font-size:1.05rem !important;
                font-weight:800 !important;
                color:#92400e !important;
                margin:0 !important;
            }

            .edurec-toast .swal2-html-container{
                font-size:.92rem !important;
                line-height:1.6 !important;
                margin:.45rem 0 0 0 !important;
                color:#78350f !important;
            }

            .edurec-toast .swal2-icon{
                transform:scale(.95);
                margin:.2rem .6rem .2rem .15rem !important;
            }

            .edurec-toast .swal2-timer-progress-bar{
                background:#f59e0b !important;
            }

            @media (max-width: 575.98px){
                div.swal2-popup.swal2-toast.edurec-toast{
                    width: calc(100vw - 28px) !important;
                    padding:.9rem 1rem !important;
                }
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'กรุณาบันทึกผลการเรียนก่อน',
                        html: @json(session('info')),
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'edurec-toast'
                        },
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });
                }
            });
        </script>
    @endif


<div class="edurec-card">

        <div class="edurec-card">
            <div class="edurec-header">
                <div class="edurec-header-inner">
                    <div class="edurec-title-wrap">
                        <div class="edurec-icon">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <div class="edurec-title-group">
                            <div class="edurec-badge">
                                <i class="bi bi-mortarboard-fill"></i>
                                <span>Education Record Form</span>
                            </div>
                            <h1 class="edurec-title">บันทึกผลการเรียน</h1>
                            <p class="edurec-subtitle">กรอกข้อมูลผลการเรียนให้ครบถ้วน</p>
                        </div>
                    </div>

                    <a href="{{ route('admin.index', $client->id) }}"
                       class="edurec-close-btn"
                       aria-label="ปิดฟอร์ม"
                       title="ปิดฟอร์ม">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>

            <div class="edurec-body">
                <form action="{{ route('education_record_store') }}" method="POST" novalidate id="educationRecordForm">
                    @csrf
                    <input type="hidden" name="client_id" value="{{ $client->id }}">

                    {{-- ข้อมูลพื้นฐาน --}}
                    <div class="edurec-section">
                        <h2 class="edurec-section-title">
                            <i class="bi bi-person-vcard"></i>
                            <span>ข้อมูลพื้นฐาน</span>
                        </h2>

                        <div class="edurec-info-grid">
                            <div class="edurec-col-6">
                                <div class="edurec-field">
                                    <label class="edurec-label">นักเรียน</label>
                                    <input type="text"
                                           class="form-control"
                                           value="{{ $client->full_name }}"
                                           readonly>
                                </div>
                            </div>

                            <div class="edurec-col-6">
                                <div class="edurec-field">
                                    <label for="education_id" class="edurec-label">
                                        ระดับการศึกษา <span class="text-danger">*</span>
                                    </label>
                                    <select name="education_id"
                                            id="education_id"
                                            class="form-select @error('education_id') is-invalid @enderror"
                                            required>
                                        <option value="">-- เลือกการศึกษา --</option>
                                        @foreach ($educations as $item)
                                            <option value="{{ $item->id }}" {{ old('education_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->education_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('education_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="edurec-col-4">
                                <div class="edurec-field">
                                    <label for="semester_id" class="edurec-label">
                                        ภาคเรียน <span class="text-danger">*</span>
                                    </label>
                                    <select name="semester_id"
                                            id="semester_id"
                                            class="form-select @error('semester_id') is-invalid @enderror"
                                            required>
                                        <option value="">-- เลือกภาคเรียน --</option>
                                        @foreach($semesters as $sem)
                                            <option value="{{ $sem->id }}" {{ old('semester_id') == $sem->id ? 'selected' : '' }}>
                                                {{ $sem->semester_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('semester_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="edurec-col-8">
                                <div class="edurec-field">
                                    <label for="school_name" class="edurec-label">
                                        สถานศึกษา <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="school_name"
                                           id="school_name"
                                           class="form-control @error('school_name') is-invalid @enderror"
                                           value="{{ old('school_name') }}"
                                           placeholder="เช่น โรงเรียนแมวเหมี๊ยววิทยา"
                                           required>
                                    @error('school_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="edurec-col-3">
                                <div class="edurec-field">
                                    <label for="record_date" class="edurec-label">
                                        วันที่บันทึก <span class="text-danger">*</span>
                                    </label>
                                 <input type="date"
                                    name="record_date"
                                    id="record_date"
                                    class="form-control @error('record_date') is-invalid @enderror"
                                    value="{{ old('record_date') }}"
                                    max="{{ now('Asia/Bangkok')->toDateString() }}"
                                    required>
                                    @error('record_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- รายละเอียดวิชา --}}
                    <div class="edurec-section">
                        <div class="edurec-subject-toolbar">
                            <div>
                                <h2 class="edurec-section-title mb-2">
                                    <i class="bi bi-list-check"></i>
                                    <span>รายละเอียดวิชา</span>
                                </h2>
                                <p class="edurec-subject-note">
                                    เพิ่มรายวิชาได้หลายรายการ ระบบจะช่วยคำนวณเกรดอัตโนมัติจากคะแนน
                                </p>
                            </div>

                            <button type="button" class="btn btn-edurec-add" id="add-subject">
                                <i class="bi bi-plus-circle"></i>
                                <span>เพิ่มวิชา</span>
                            </button>
                        </div>

                        <div id="subject-container" class="edurec-subject-list">
                            <div class="edurec-subject-item subject-item">
                                <div class="edurec-subject-head">
                                    <div class="edurec-subject-badge">
                                        <i class="bi bi-book-half"></i>
                                        <span>วิชาที่ 1</span>
                                    </div>
                                </div>

                                <div class="edurec-subject-grid">
                                    <div class="edurec-field">
                                        <label class="edurec-label">วิชา</label>
                                        <select name="subjects[0][subject_id]" class="form-select">
                                            <option value="">-- เลือกวิชา --</option>
                                            @foreach($subjects as $subject)
                                                <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="edurec-field">
                                        <label class="edurec-label">คะแนน</label>
                                        <input type="number"
                                               name="subjects[0][score]"
                                               class="form-control subject-score"
                                               min="0"
                                               max="100"
                                               inputmode="numeric"
                                               placeholder="0 - 100">
                                        <div class="edurec-helper">คะแนนเต็ม 100</div>
                                    </div>

                                  <div class="edurec-field">
                                        <label class="edurec-label">เกรด</label>
                                        <input type="text"
                                            name="subjects[0][grade]"
                                            class="form-control subject-grade"
                                            value=""
                                            placeholder="คำนวณอัตโนมัติ"
                                            readonly>
                                    </div>

                                    <div class="edurec-field">
                                        <label class="edurec-label d-none d-md-block">&nbsp;</label>
                                        <button type="button"
                                                class="btn btn-edurec-remove remove-subject"
                                                aria-label="ลบวิชา">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="subject-empty-state" class="edurec-empty-state">
                            <i class="bi bi-journal-x"></i>
                            <div>ยังไม่มีรายการวิชา</div>
                            <small>กด “เพิ่มวิชา” เพื่อเริ่มเพิ่มข้อมูลผลการเรียน</small>
                        </div>
                    </div>

                    {{-- สรุปผล --}}
                    <div class="edurec-section">
                        <h2 class="edurec-section-title">
                            <i class="bi bi-bar-chart-line"></i>
                            <span>สรุปผลการเรียน</span>
                        </h2>

                        <div class="edurec-summary-box">
                            <div class="edurec-summary-grid">
                                <div class="edurec-summary-meta">
                                    จำนวนวิชาที่กรอกแล้ว:
                                    <strong><span id="subject-count">1</span></strong> รายการ
                                </div>

                                <div class="edurec-gpa-wrap">
                                    <label for="grade_average" class="edurec-label">
                                        เกรดเฉลี่ย (GPA)
                                    </label>
                                    <input type="number"
                                           step="0.01"
                                           name="grade_average"
                                           id="grade_average"
                                           class="form-control @error('grade_average') is-invalid @enderror"
                                           value="{{ old('grade_average') }}"
                                           placeholder="เช่น 3.25">
                                    @error('grade_average')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ปุ่ม --}}
                    <div class="edurec-footer-actions">
                        <div class="edurec-footer-actions-left">
                            <button type="button" class="btn btn-edurec-add" id="add-subject-bottom">
                                <i class="bi bi-plus-circle"></i>
                                <span>เพิ่มวิชา</span>
                            </button>

                            <a href="{{ route('education_record_show', ['client_id' => $client->id]) }}"
                               class="btn btn-edurec-home">
                                <i class="bi bi-house-door"></i>
                                <span>กลับหน้าหลัก</span>
                            </a>
                        </div>

                        <div class="edurec-footer-actions-right">
                            <button type="submit" class="btn btn-edurec-save">
                                <i class="bi bi-save"></i>
                                <span>บันทึกผล</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('subject-container');
    const emptyState = document.getElementById('subject-empty-state');
    const addBtnTop = document.getElementById('add-subject');
    const addBtnBottom = document.getElementById('add-subject-bottom');
    const subjectCountEl = document.getElementById('subject-count');

    function calculateGrade(score) {
        const numericScore = parseFloat(score);

        if (isNaN(numericScore)) {
            return '';
        }

        if (numericScore >= 80) return '4.00';
        if (numericScore >= 75) return '3.50';
        if (numericScore >= 70) return '3.00';
        if (numericScore >= 65) return '2.50';
        if (numericScore >= 60) return '2.00';
        if (numericScore >= 55) return '1.50';
        if (numericScore >= 50) return '1.00';

        return '0.00';
    }

    function refreshSubjectLabels() {
        const items = container.querySelectorAll('.subject-item');
        items.forEach((item, index) => {
            const badgeText = item.querySelector('.edurec-subject-badge span');
            if (badgeText) {
                badgeText.textContent = `วิชาที่ ${index + 1}`;
            }
        });

        if (subjectCountEl) {
            subjectCountEl.textContent = items.length;
        }

        if (emptyState) {
            emptyState.style.display = items.length ? 'none' : 'block';
        }
    }

    function renumberFieldNames() {
        const items = container.querySelectorAll('.subject-item');
        items.forEach((item, index) => {
            const subjectSelect = item.querySelector('select[name*="[subject_id]"]');
            const scoreInput = item.querySelector('input[name*="[score]"]');
            const gradeSelect = item.querySelector('input[name*="[grade]"]');

            if (subjectSelect) subjectSelect.name = `subjects[${index}][subject_id]`;
            if (scoreInput) scoreInput.name = `subjects[${index}][score]`;
            if (gradeSelect) gradeSelect.name = `subjects[${index}][grade]`;
        });
    }

    function updateGrade(input) {
        const row = input.closest('.subject-item');
        if (!row) return;

        const gradeSelect = row.querySelector('.subject-grade');
        if (!gradeSelect) return;

        const grade = calculateGrade(input.value);
        if (grade !== '') {
            gradeSelect.value = grade;
        }
    }

    function createSubjectItem(index) {
    return `
        <div class="edurec-subject-item subject-item">
            <div class="edurec-subject-head">
                <div class="edurec-subject-badge">
                    <i class="bi bi-book-half"></i>
                    <span>วิชาที่ ${index + 1}</span>
                </div>
            </div>

            <div class="edurec-subject-grid">
                <div class="edurec-field">
                    <label class="edurec-label">วิชา</label>
                    <select name="subjects[${index}][subject_id]" class="form-select" required>
                        <option value="">-- เลือกวิชา --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="edurec-field">
                    <label class="edurec-label">คะแนน</label>
                    <input type="number"
                           name="subjects[${index}][score]"
                           class="form-control subject-score"
                           min="0"
                           max="100"
                           inputmode="numeric"
                           placeholder="0 - 100"
                           required>
                    <div class="edurec-helper">คะแนนเต็ม 100</div>
                </div>

                <div class="edurec-field">
                    <label class="edurec-label">เกรด</label>
                    <input type="text"
                           name="subjects[${index}][grade]"
                           class="form-control subject-grade"
                           value=""
                           placeholder="คำนวณอัตโนมัติ"
                           readonly>
                </div>

                <div class="edurec-field">
                    <label class="edurec-label d-none d-md-block">&nbsp;</label>
                    <button type="button"
                            class="btn btn-edurec-remove remove-subject"
                            aria-label="ลบวิชา">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}
    function addSubject() {
        const index = container.querySelectorAll('.subject-item').length;
        container.insertAdjacentHTML('beforeend', createSubjectItem(index));
        refreshSubjectLabels();
    }

    function handleAddSubjectClick() {
        addSubject();
    }

    if (addBtnTop) addBtnTop.addEventListener('click', handleAddSubjectClick);
    if (addBtnBottom) addBtnBottom.addEventListener('click', handleAddSubjectClick);

    container.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.remove-subject');
        if (!removeBtn) return;

        const items = container.querySelectorAll('.subject-item');
        if (items.length <= 1) {
            const item = removeBtn.closest('.subject-item');
            if (!item) return;

            const selects = item.querySelectorAll('select');
            const inputs = item.querySelectorAll('input');

            selects.forEach(el => el.value = '');
            inputs.forEach(el => el.value = '');
            refreshSubjectLabels();
            return;
        }

        const subjectItem = removeBtn.closest('.subject-item');
        if (subjectItem) {
            subjectItem.remove();
            renumberFieldNames();
            refreshSubjectLabels();
        }
    });

    container.addEventListener('input', function (e) {
        if (e.target.matches('input[name*="[score]"]')) {
            let value = parseFloat(e.target.value);

            if (!isNaN(value)) {
                if (value < 0) e.target.value = 0;
                if (value > 100) e.target.value = 100;
            }

            updateGrade(e.target);
        }
    });

    const semesterInput = document.getElementById('semester_id');
    if (semesterInput) {
        semesterInput.addEventListener('change', function () {
            if (!this.value) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }

    refreshSubjectLabels();
});
</script>


@endsection