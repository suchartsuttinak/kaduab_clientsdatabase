@extends('admin_client.admin_client')

@section('content')
<style>
    :root {
        --page-bg: #f4f7fb;
        --card-bg: #ffffff;
        --text-main: #0f172a;
        --text-soft: #64748b;
        --line: #e2e8f0;
        --line-soft: #edf2f7;

        --primary: #2563eb;
        --primary-soft: #eff6ff;

        --warning-btn: #e89a4a;
        --warning-btn-hover: #db8c3a;

        --danger-btn: #e67b8b;
        --danger-btn-hover: #dc6b7d;

        --info-btn: #58afe0;
        --info-btn-hover: #459fd2;

        --report-btn: #4f46e5;
        --report-btn-hover: #4338ca;

        --success: #059669;
        --success-soft: #e8f7f1;

        --wait: #f97316;
        --wait-soft: #fff3e8;

        --shadow-soft: 0 10px 30px rgba(15, 23, 42, 0.05);
        --radius-xl: 20px;
        --radius-lg: 16px;
        --radius-md: 12px;
    }

    .education-page {
        background: var(--page-bg);
        min-height: 100%;
    }

    .education-shell {
        width: 100%;
    }

    .page-panel,
    .list-panel,
    .mobile-card {
        background: var(--card-bg);
        border: 1px solid var(--line);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-soft);
    }

    .page-panel {
        padding: 1.2rem 1.25rem;
        margin-bottom: 1rem;
    }

    .page-title {
        font-size: clamp(1.15rem, 1rem + 0.8vw, 1.7rem);
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: .2rem;
    }

    .page-subtitle {
        color: var(--text-soft);
        font-size: .94rem;
        margin-bottom: 0;
    }

    .client-badge {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        padding: .72rem 1rem;
        border-radius: 999px;
        background: var(--primary-soft);
        color: var(--primary);
        font-weight: 700;
        margin-top: 1rem;
        max-width: 100%;
        word-break: break-word;
    }

    .list-panel {
        padding: 1rem;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: .25rem;
    }

    .section-subtitle {
        color: var(--text-soft);
        font-size: .9rem;
        margin-bottom: 0;
    }

    .summary-pill {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .72rem 1rem;
        border-radius: 999px;
        background: var(--success-soft);
        color: var(--success);
        font-weight: 800;
        white-space: nowrap;
    }

    .btn-modern {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        border: 0;
        border-radius: 12px;
        font-weight: 700;
        font-size: .84rem;
        line-height: 1;
        padding: .62rem .9rem;
        white-space: nowrap;
        transition: .18s ease;
        box-shadow: none !important;
        text-decoration: none;
    }

    .btn-modern:hover {
        transform: translateY(-1px);
    }

    .btn-add {
        background: var(--primary);
        color: #fff;
    }

    .btn-add:hover {
        background: #1d4ed8;
        color: #fff;
    }

    .btn-edit-soft {
        background: var(--warning-btn);
        color: #fff;
    }

    .btn-edit-soft:hover {
        background: var(--warning-btn-hover);
        color: #fff;
    }

    .btn-delete-soft {
        background: var(--danger-btn);
        color: #fff;
    }

    .btn-delete-soft:hover {
        background: var(--danger-btn-hover);
        color: #fff;
    }

    .btn-info-soft {
        background: var(--info-btn);
        color: #fff;
    }

    .btn-info-soft:hover {
        background: var(--info-btn-hover);
        color: #fff;
    }

    .btn-report-soft {
        background: var(--report-btn);
        color: #fff;
    }

    .btn-report-soft:hover {
        background: var(--report-btn-hover);
        color: #fff;
    }

    .btn-table {
        padding: .52rem .82rem;
        font-size: .81rem;
        border-radius: 12px;
        min-height: 38px;
    }

    .page-actions {
        align-items: center;
    }

    .btn-add-modern {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .72rem 1.1rem;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        border: 1px solid transparent;
        border-radius: 12px;
        font-size: .95rem;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.18);
        transition: all .2s ease;
        white-space: nowrap;
    }

    .btn-add-modern:hover {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.22);
    }

    .btn-add-modern:active {
        transform: translateY(0);
    }

    .btn-add-modern i {
        font-size: 1rem;
        line-height: 1;
    }

    .table-shell {
        border: 1px solid var(--line);
        border-radius: 18px;
        overflow: hidden;
        background: #fff;
    }

    .table-scroll {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    .table-scroll::-webkit-scrollbar {
        height: 10px;
    }

    .table-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    .education-table {
        width: 100%;
        min-width: 1360px;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        vertical-align: middle;
    }

    .education-table thead th {
        background: #f8fafc;
        color: #334155;
        font-size: .9rem;
        font-weight: 800;
        padding: 1rem .95rem;
        border-bottom: 1px solid var(--line);
        white-space: nowrap;
        text-align: center;
    }

    .education-table tbody td {
        padding: 1rem .95rem;
        font-size: .95rem;
        color: var(--text-main);
        border-bottom: 1px solid var(--line-soft);
        vertical-align: middle;
    }

    .education-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .col-date,
    .col-term,
    .col-gpa {
        white-space: nowrap;
    }

    .record-date {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0b1b3b;
        white-space: nowrap;
    }

    .record-level,
    .record-school {
        font-weight: 700;
        color: var(--text-main);
    }

    .record-school {
        min-width: 280px;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 68px;
        min-height: 38px;
        border-radius: 999px;
        padding: .35rem .8rem;
        font-size: .83rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .badge-wait {
        background: var(--wait-soft);
        color: var(--wait);
    }

    .badge-grade {
        background: var(--success-soft);
        color: var(--success);
    }

    .col-actions {
        width: 1%;
        white-space: nowrap;
    }

    .table-actions {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        flex-wrap: nowrap;
        white-space: nowrap;
    }

    .table-actions form {
        display: inline-block;
        margin: 0;
    }

    .subject-row td {
        background: #fcfdff;
        padding: 0 !important;
    }

    .subject-panel {
        padding: 1rem;
        border-top: 1px solid var(--line-soft);
    }

    .subject-table {
        margin-bottom: 0;
    }

    .subject-table th {
        background: #f8fafc;
        color: #334155;
        font-size: .86rem;
        font-weight: 800;
        white-space: nowrap;
        text-align: center;
    }

    .subject-table td {
        font-size: .91rem;
        vertical-align: middle;
    }

    .mobile-list {
        display: none;
    }

    .mobile-card {
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .mobile-card:last-child {
        margin-bottom: 0;
    }

    .mobile-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: .75rem;
        margin-bottom: .85rem;
    }

    .mobile-title {
        font-size: 1rem;
        font-weight: 800;
        margin-bottom: .2rem;
        color: var(--text-main);
    }

    .mobile-subtitle {
        font-size: .9rem;
        color: var(--text-soft);
        margin-bottom: 0;
    }

    .mobile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
        margin-bottom: .95rem;
    }

    .mobile-item {
        background: #f8fafc;
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: .75rem;
    }

    .mobile-item.full {
        grid-column: 1 / -1;
    }

    .mobile-label {
        display: block;
        color: var(--text-soft);
        font-size: .78rem;
        margin-bottom: .2rem;
    }

    .mobile-value {
        color: var(--text-main);
        font-size: .94rem;
        font-weight: 800;
        word-break: break-word;
    }

    .mobile-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .5rem;
    }

    .mobile-actions .mobile-actions-row {
        grid-column: 1 / -1;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .5rem;
    }

    .mobile-actions form .btn-modern,
    .mobile-actions .btn-modern,
    .mobile-actions form {
        width: 100%;
    }

    .mobile-actions .btn-show {
        grid-column: 1 / -1;
    }

    .empty-state {
        text-align: center;
        border: 1px dashed #f6ad55;
        background: #fffaf2;
        color: #9a3412;
        border-radius: 16px;
        padding: 2rem 1rem;
    }

    .empty-state i {
        font-size: 2rem;
        display: inline-block;
        margin-bottom: .6rem;
    }

    @media (max-width: 1199.98px) {
        .education-table {
            min-width: 1320px;
        }
    }

    @media (max-width: 991.98px) {
        .desktop-list {
            display: none;
        }

        .mobile-list {
            display: block;
        }

        .section-header {
            flex-direction: column;
            align-items: stretch;
        }

        .summary-pill {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 767.98px) {
        .page-panel,
        .list-panel,
        .mobile-card {
            border-radius: 16px;
        }

        .client-badge {
            display: flex;
            width: 100%;
            border-radius: 14px;
        }

        .page-actions {
            width: 100%;
        }

        .btn-add-modern {
            width: 100%;
        }

        .page-actions .btn-modern {
            width: 100%;
        }

        .mobile-grid {
            grid-template-columns: 1fr;
        }

        .mobile-item.full {
            grid-column: auto;
        }

        .mobile-card-head {
            flex-direction: column;
        }

        .mobile-actions,
        .mobile-actions .mobile-actions-row {
            grid-template-columns: 1fr;
        }

        .mobile-actions .btn-show {
            grid-column: auto;
        }
    }


    /* ปรับปุ่มให้เป็นมาตรฐานเดียวกับหน้าอื่นในระบบ */
    .btn-modern,
    .btn-add-modern {
        min-height: 42px;
        border-radius: 12px;
        font-weight: 750;
        border: 1px solid transparent;
        transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease, border-color .18s ease;
    }

    .btn-modern:focus-visible,
    .btn-add-modern:focus-visible {
        outline: 0;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .16) !important;
    }

    .btn-modern:disabled,
    .btn-modern.disabled,
    .btn-add-modern:disabled,
    .btn-add-modern.disabled {
        opacity: 1 !important;
        cursor: not-allowed;
        transform: none !important;
    }

    .btn-edit-soft {
        background: #16a34a;
        border-color: #15803d;
        color: #fff;
    }

    .btn-edit-soft:hover {
        background: #15803d;
        border-color: #166534;
        color: #fff;
    }

    .btn-delete-soft {
        background: #dc2626;
        border-color: #b91c1c;
        color: #fff;
    }

    .btn-delete-soft:hover {
        background: #b91c1c;
        border-color: #991b1b;
        color: #fff;
    }

    .btn-info-soft {
        background: #0284c7;
        border-color: #0369a1;
        color: #fff;
    }

    .btn-info-soft:hover {
        background: #0369a1;
        border-color: #075985;
        color: #fff;
    }

    .btn-report-soft {
        background: #4f46e5;
        border-color: #4338ca;
        color: #fff;
    }

    .btn-report-soft:hover {
        background: #4338ca;
        border-color: #3730a3;
        color: #fff;
    }

</style>

@php
    $hasGpa = static function ($record): bool {
        return $record->grade_average !== null && $record->grade_average !== '';
    };

    // GPA 0.00 เป็นผลการเรียนที่ถูกต้องและต้องนำมาคำนวณด้วย
    $recordsWithGpa = $educationRecords->filter($hasGpa);
    $overallAverageGpa = $recordsWithGpa->isNotEmpty()
        ? round((float) $recordsWithGpa->avg('grade_average'), 2)
        : null;
@endphp

<div class="container-fluid education-page py-3 py-md-4">
    <div class="education-shell">

        <div class="page-panel">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h1 class="page-title">ผลการเรียน</h1>
                    <p class="page-subtitle">แสดงข้อมูลผลการเรียนและรายวิชาที่บันทึกไว้ในระบบ</p>
                </div>

                <div class="page-actions d-flex flex-wrap gap-2">
                    <a href="{{ route('education_record_add', $client->id) }}" class="btn btn-add-modern">
                        <i data-feather="plus-circle"></i>
                        <span>เพิ่มผลการเรียนใหม่</span>
                    </a>
                </div>
            </div>

            <div class="client-badge">
                <i class="bi bi-person-circle"></i>
                <span>ชื่อ-สกุล : {{ $client->full_name ?? $client->fullname ?? '-' }}</span>
            </div>
        </div>

        <div class="list-panel">
            <div class="section-header">
                <div>
                    <div class="section-title">
                        <i class="bi bi-journal-text text-primary"></i>
                        ตารางผลการเรียน
                    </div>
                    <p class="section-subtitle">แสดงรายการผลการเรียน พร้อมจัดการข้อมูลและดูรายวิชาในแต่ละรายการ</p>
                </div>

                <div class="summary-pill">
                    <i class="bi bi-award-fill"></i>
                    <span>เกรดเฉลี่ยรวม :
                        {{ $overallAverageGpa !== null ? number_format($overallAverageGpa, 2) : '-' }}
                    </span>
                </div>
            </div>

            @if($educationRecords->isNotEmpty())

                {{-- Desktop / Notebook / Large tablet --}}
                <div class="desktop-list">
                    <div class="table-shell">
                        <div class="table-scroll">
                            <table class="table education-table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 12%;">วันที่บันทึก</th>
                                        <th style="width: 18%;">ระดับชั้น</th>
                                        <th style="width: 10%;">ภาคเรียน</th>
                                        <th style="width: 24%;">ชื่อสถานศึกษา</th>
                                        <th style="width: 10%;">เกรดเฉลี่ย</th>
                                        <th style="width: 26%;" class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($educationRecords as $record)
                                        <tr>
                                            <td class="col-date">
                                                <span class="record-date">
                                                    {{ \Carbon\Carbon::parse($record->record_date)->format('d/m') }}/{{ \Carbon\Carbon::parse($record->record_date)->year + 543 }}
                                                </span>
                                            </td>

                                            <td>
                                                <div class="record-level">{{ $record->education->education_name ?? '-' }}</div>
                                            </td>

                                           <td class="col-term">
    <div class="fw-bold">
        {{ $record->semester_label ?? ($record->semester->semester_name ?? '-') }}
    </div>
</td>
                                            <td>
                                                <div class="record-school">{{ $record->school_name ?? '-' }}</div>
                                            </td>

                                            <td class="col-gpa">
                                                @if(!$hasGpa($record))
                                                    <span class="badge-status badge-wait">รอผล</span>
                                                @else
                                                    <span class="badge-status badge-grade">
                                                        {{ number_format($record->grade_average, 2) }}
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="text-center col-actions">
                                                <div class="table-actions">
                                                    <a href="{{ route('education_record_edit', $record->id) }}"
                                                       class="btn btn-modern btn-table btn-edit-soft">
                                                        <i class="bi bi-pencil-square"></i>
                                                        แก้ไข
                                                    </a>

                                                    <form id="delete-form-{{ $record->id }}"
                                                          action="{{ route('education_record_delete', $record->id) }}"
                                                          method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                                class="btn btn-modern btn-table btn-delete-soft"
                                                                onclick="confirmDelete('delete-form-{{ $record->id }}', 'คุณแน่ใจหรือไม่ที่จะลบข้อมูลผลการเรียนนี้?')">
                                                            <i class="bi bi-trash"></i>
                                                            ลบ
                                                        </button>
                                                    </form>

                                                   <a href="{{ route('education_record.report_by_id', $record->id) }}"
                                                        class="btn btn-modern btn-table btn-report-soft">
                                                            <i class="bi bi-printer"></i>
                                                            รายงาน
                                                        </a>

                                                    <button class="btn btn-modern btn-table btn-info-soft toggle-subject-btn"
                                                            type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#record-{{ $record->id }}"
                                                            aria-expanded="false"
                                                            aria-controls="record-{{ $record->id }}">
                                                        <i class="bi bi-list-ul"></i>
                                                        <span class="btn-label">แสดงรายวิชา</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr class="subject-row">
                                            <td colspan="6">
                                                <div id="record-{{ $record->id }}" class="collapse">
                                                    <div class="subject-panel">
                                                        @if($record->subjects && $record->subjects->count())
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-hover subject-table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="width: 50%;">ชื่อวิชา</th>
                                                                            <th style="width: 25%;" class="text-center">คะแนน</th>
                                                                            <th style="width: 25%;" class="text-center">เกรด</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($record->subjects as $subject)
                                                                            <tr>
                                                                                <td>{{ $subject->subject_name ?? '-' }}</td>
                                                                                <td class="text-center">{{ $subject->pivot->score ?? '-' }}</td>
                                                                                <td class="text-center">{{ $subject->pivot->grade ?? '-' }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @else
                                                            <div class="alert alert-light border mb-0">
                                                                ยังไม่มีข้อมูลรายวิชา
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Mobile --}}
                <div class="mobile-list">
                    @foreach($educationRecords as $record)
                        <div class="mobile-card">
                            <div class="mobile-card-head">
                                <div>
                                    <div class="mobile-title">{{ $record->education->education_name ?? '-' }}</div>
                                    <p class="mobile-subtitle">
                                        วันที่บันทึก :
                                        {{ \Carbon\Carbon::parse($record->record_date)->format('d/m') }}/{{ \Carbon\Carbon::parse($record->record_date)->year + 543 }}
                                    </p>
                                </div>

                                @if(!$hasGpa($record))
                                    <span class="badge-status badge-wait">รอผล</span>
                                @else
                                    <span class="badge-status badge-grade">
                                        {{ number_format($record->grade_average, 2) }}
                                    </span>
                                @endif
                            </div>

                            <div class="mobile-grid">
                                <div class="mobile-item">
                                    <span class="mobile-label">ภาคเรียน</span>
                                    <div class="mobile-value">{{ $record->semester_label ?? data_get($record, 'semester.semester_name', '-') }}</div>
                                </div>

                                <div class="mobile-item">
                                    <span class="mobile-label">เกรดเฉลี่ย</span>
                                    <div class="mobile-value">
                                        {{ $hasGpa($record) ? number_format((float) $record->grade_average, 2) : 'รอผล' }}
                                    </div>
                                </div>

                                <div class="mobile-item full">
                                    <span class="mobile-label">ชื่อสถานศึกษา</span>
                                    <div class="mobile-value">{{ $record->school_name ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="mobile-actions">
                                <div class="mobile-actions-row">
                                    <a href="{{ route('education_record_edit', $record->id) }}"
                                       class="btn btn-modern btn-table btn-edit-soft">
                                        <i class="bi bi-pencil-square"></i>
                                        แก้ไข
                                    </a>

                                    <form id="mobile-delete-form-{{ $record->id }}"
                                          action="{{ route('education_record_delete', $record->id) }}"
                                          method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="btn btn-modern btn-table btn-delete-soft"
                                                onclick="confirmDelete('mobile-delete-form-{{ $record->id }}', 'คุณแน่ใจหรือไม่ที่จะลบข้อมูลผลการเรียนนี้?')">
                                            <i class="bi bi-trash"></i>
                                            ลบ
                                        </button>
                                    </form>
                                </div>

                                <div class="mobile-actions-row">
                                    <a href="{{ route('education_record.report_by_id', $record->id) }}"
                                       class="btn btn-modern btn-table btn-report-soft">
                                        <i class="bi bi-printer"></i>
                                        รายงาน
                                    </a>

                                    <button class="btn btn-modern btn-table btn-info-soft btn-show toggle-subject-btn"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#mobile-record-{{ $record->id }}"
                                            aria-expanded="false"
                                            aria-controls="mobile-record-{{ $record->id }}">
                                        <i class="bi bi-list-ul"></i>
                                        <span class="btn-label">แสดงรายวิชา</span>
                                    </button>
                                </div>
                            </div>

                            <div id="mobile-record-{{ $record->id }}" class="collapse mt-3">
                                <div class="subject-panel p-0 border-0">
                                    @if($record->subjects && $record->subjects->count())
                                        <div class="table-responsive">
                                            <table class="table table-bordered subject-table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>ชื่อวิชา</th>
                                                        <th class="text-center">คะแนน</th>
                                                        <th class="text-center">เกรด</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($record->subjects as $subject)
                                                        <tr>
                                                            <td>{{ $subject->subject_name ?? '-' }}</td>
                                                            <td class="text-center">{{ $subject->pivot->score ?? '-' }}</td>
                                                            <td class="text-center">{{ $subject->pivot->grade ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-light border mb-0">
                                            ยังไม่มีข้อมูลรายวิชา
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            @else
                <div class="empty-state">
                    <i class="bi bi-journal-x"></i>
                    <div class="fw-bold mb-1">ยังไม่มีข้อมูลผลการเรียน</div>
                    <div class="small mb-3">เริ่มต้นเพิ่มข้อมูลผลการเรียนเพื่อให้ระบบพร้อมใช้งาน</div>
                    <a href="{{ route('education_record_add', $client->id) }}" class="btn btn-modern btn-add">
                        <i class="bi bi-plus-circle"></i>
                        เพิ่มผลการเรียนใหม่
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const collapseElements = document.querySelectorAll('.collapse');

    collapseElements.forEach(function (collapseEl) {
        collapseEl.addEventListener('show.bs.collapse', function () {
            document.querySelectorAll(`[data-bs-target="#${collapseEl.id}"]`).forEach(button => {
                const label = button.querySelector('.btn-label');
                if (label) label.textContent = 'ซ่อนรายวิชา';
                button.setAttribute('aria-expanded', 'true');
            });
        });

        collapseEl.addEventListener('hide.bs.collapse', function () {
            document.querySelectorAll(`[data-bs-target="#${collapseEl.id}"]`).forEach(button => {
                const label = button.querySelector('.btn-label');
                if (label) label.textContent = 'แสดงรายวิชา';
                button.setAttribute('aria-expanded', 'false');
            });
        });
    });
});
</script>

@endsection