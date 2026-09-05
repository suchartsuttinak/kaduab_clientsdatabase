@extends('admin_client.admin_client')

@section('content')
@php
    $hasAssessments = $assessments->isNotEmpty();

    $clientName = trim(
        ($client->prefix ?? '') .
        ($client->first_name ?? '') . ' ' .
        ($client->last_name ?? '')
    );
@endphp

<div class="container-fluid py-4 nutrition-index-page">
    <div class="nutrition-card">

        <div class="nutrition-header">
            <div class="nutrition-header-left">
                <div class="nutrition-header-icon" aria-hidden="true">
                    <i class="bi bi-heart-pulse"></i>
                </div>

                <div>
                    <h4 class="mb-1">ประเมินภาวะโภชนาการ</h4>

                    <div class="nutrition-header-subtitle">
                        ผู้รับบริการ:
                        <strong>{{ $clientName !== '' ? $clientName : '-' }}</strong>
                    </div>
                </div>
            </div>

            <div class="nutrition-header-actions">
                @if ($hasAssessments)
                    <a href="{{ route('nutrition_assessments.create', $client->id) }}"
                       class="nutrition-btn nutrition-btn-primary">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>เพิ่มผลประเมิน</span>
                    </a>
                @endif

                <a href="{{ route('admin.index', $client->id) }}"
                   class="nutrition-btn nutrition-btn-back"
                   aria-label="กลับหน้าหลักผู้รับบริการ">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>กลับ</span>
                </a>
            </div>
        </div>

        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ',
                            text: @json(session('success')),
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    }
                });
            </script>
        @endif

        @if ($hasAssessments)
            <div class="nutrition-content">
                <div class="table-responsive nutrition-table-wrap">
                    <table class="table table-hover align-middle nutrition-table mb-0">
                        <thead>
                            <tr>
                                <th style="width:70px;">ลำดับ</th>
                                <th>วันที่ชั่งวัด</th>
                                <th>อายุ</th>
                                <th>เพศ</th>
                                <th>ส่วนสูง</th>
                                <th>น้ำหนัก</th>
                                <th>BMI</th>
                                <th>ผลประเมิน</th>
                                <th>หมายเหตุ</th>
                                <th class="text-center" style="min-width:260px;">จัดการ</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($assessments as $index => $item)
                                <tr>
                                    <td>{{ $assessments->count() - $index }}</td>

                                    <td>
                                        {{ $item->assessment_date
                                            ? $item->assessment_date->format('d/m/') . ($item->assessment_date->year + 543)
                                            : '-' }}
                                    </td>

                                    <td>
                                        {{ $item->age_year ?? '-' }} ปี
                                        {{ $item->age_month ?? '-' }} เดือน
                                    </td>

                                    <td>
                                        {{ $item->gender === 'male'
                                            ? 'ชาย'
                                            : ($item->gender === 'female' ? 'หญิง' : '-') }}
                                    </td>

                                    <td>
                                        {{ $item->height_cm !== null
                                            ? number_format($item->height_cm, 2)
                                            : '-' }} ซม.
                                    </td>

                                    <td>
                                        {{ $item->weight_kg !== null
                                            ? number_format($item->weight_kg, 2)
                                            : '-' }} กก.
                                    </td>

                                    <td>
                                        {{ $item->bmi !== null
                                            ? number_format($item->bmi, 2)
                                            : '-' }}
                                    </td>

                                    <td>
                                        @php
                                            $status = $item->nutrition_status ?: '-';

                                            $badgeClass = match ($status) {
                                                'สมส่วน' => 'status-normal',
                                                'น้ำหนักน้อย' => 'status-low',
                                                'เริ่มอ้วน' => 'status-warning',
                                                'อ้วน', 'อ้วนมาก' => 'status-danger',
                                                default => 'status-default',
                                            };
                                        @endphp

                                        <span class="nutrition-status {{ $badgeClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>

                                    <td class="nutrition-note-cell">
                                        {{ $item->note ?: '-' }}
                                    </td>

                                    <td class="text-center">
                                        <div class="action-buttons">
                                            <a href="{{ route('nutrition_assessments.show', [$client->id, $item->id]) }}"
                                               class="action-btn action-view">
                                                <i class="bi bi-eye-fill"></i>
                                                <span>ดูผล</span>
                                            </a>

                                            <a href="{{ route('nutrition_assessments.edit', [$client->id, $item->id]) }}"
                                               class="action-btn action-edit">
                                                <i class="bi bi-pencil-square"></i>
                                                <span>แก้ไข</span>
                                            </a>

                                            @if (auth()->user()?->canDeleteForm('screening_nutrition'))
                                                <form action="{{ route('nutrition_assessments.destroy', [$client->id, $item->id]) }}"
                                                      method="POST"
                                                      class="delete-form m-0">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="button"
                                                            class="action-btn action-delete btn-delete">
                                                        <i class="bi bi-trash3-fill"></i>
                                                        <span>ลบ</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="nutrition-empty-state" role="status">
                <div class="nutrition-empty-icon" aria-hidden="true">
                    <i class="bi bi-clipboard2-pulse"></i>
                </div>

                <h2 class="nutrition-empty-title">
                    ยังไม่มีข้อมูลการประเมินภาวะโภชนาการ
                </h2>

                <p class="nutrition-empty-description">
                    เริ่มต้นบันทึกผลการประเมินภาวะโภชนาการของผู้รับบริการรายนี้
                </p>

                <a href="{{ route('nutrition_assessments.create', $client->id) }}"
                   class="nutrition-btn nutrition-btn-primary nutrition-empty-button">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>เพิ่มผลประเมินภาวะโภชนาการครั้งแรก</span>
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.nutrition-index-page {
    background: #f6f8fb;
}

.nutrition-card {
    overflow: hidden;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, .055);
}

.nutrition-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    padding: 1rem 1.25rem;
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
}

.nutrition-header-left {
    display: flex;
    align-items: center;
    gap: .85rem;
    min-width: 0;
}

.nutrition-header-icon {
    width: 44px;
    height: 44px;
    border-radius: 13px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    background: #eff6ff;
    color: #2563eb;
}

.nutrition-header-icon i {
    font-size: 1.05rem;
}

.nutrition-header h4 {
    margin: 0;
    color: #0f172a;
    font-size: clamp(1.25rem, 1.6vw, 1.5rem);
    font-weight: 800;
    line-height: 1.35;
    letter-spacing: -0.01em;
}

.nutrition-header-subtitle {
    margin-top: .3rem;
    color: #64748b;
    font-size: clamp(.92rem, 1vw, 1rem);
    font-weight: 500;
    line-height: 1.45;
}

.nutrition-header-subtitle strong {
    color: #0f172a;
    font-weight: 800;
}

.nutrition-header-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .65rem;
    flex-wrap: wrap;
}

.nutrition-btn,
.action-btn {
    border: 0;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    font-weight: 700;
    line-height: 1;
    transition:
        transform .18s ease,
        box-shadow .18s ease,
        background .18s ease,
        border-color .18s ease,
        color .18s ease;
    white-space: nowrap;
}

.nutrition-btn {
    min-height: 42px;
    padding: 10px 18px;
    border-radius: 12px;
}

.nutrition-btn-primary {
    color: #fff;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    box-shadow: 0 7px 16px rgba(37, 99, 235, .22);
}

.nutrition-btn-primary:hover,
.nutrition-btn-primary:focus {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(37, 99, 235, .28);
}

.nutrition-btn-primary:active {
    transform: translateY(0);
    box-shadow: 0 4px 10px rgba(37, 99, 235, .22);
}

.nutrition-btn-back {
    color: #7c3aed;
    background: #fff;
    border: 1px solid #8b5cf6;
}

.nutrition-btn-back:hover,
.nutrition-btn-back:focus {
    color: #6d28d9;
    background: #faf5ff;
    transform: translateY(-1px);
}

.nutrition-content {
    padding: 1rem;
    background: #f8fafc;
}

.nutrition-table-wrap {
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow-x: auto;
    background: #fff;
}

.nutrition-table {
    min-width: 1120px;
}

.nutrition-table thead th {
    background: #f1f5f9;
    color: #334155;
    font-weight: 800;
    font-size: .88rem;
    white-space: nowrap;
    border-bottom: 1px solid #dbe3ec;
    padding: 13px 12px;
}

.nutrition-table td {
    font-size: .88rem;
    white-space: nowrap;
    padding: 12px;
    border-color: #edf2f7;
    color: #334155;
    vertical-align: middle;
}

.nutrition-note-cell {
    max-width: 260px;
    overflow: hidden;
    text-overflow: ellipsis;
}

.nutrition-status {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 6px 10px;
    font-size: .76rem;
    font-weight: 800;
}

.status-normal {
    background: #dcfce7;
    color: #166534;
}

.status-low {
    background: #fef3c7;
    color: #92400e;
}

.status-warning {
    background: #e0f2fe;
    color: #075985;
}

.status-danger {
    background: #fee2e2;
    color: #b91c1c;
}

.status-default {
    background: #e2e8f0;
    color: #475569;
}

.action-buttons {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 7px;
    flex-wrap: nowrap;
}

.action-btn {
    min-height: 36px;
    padding: 8px 12px;
    border-radius: 10px;
    font-size: .79rem;
    border: 1px solid transparent;
    background: #fff;
}

.action-view {
    color: #1d4ed8;
    background: #eff6ff;
    border-color: #bfdbfe;
}

.action-view:hover {
    color: #fff;
    background: #2563eb;
    border-color: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(37, 99, 235, .18);
}

.action-edit {
    color: #a16207;
    background: #fffbeb;
    border-color: #fde68a;
}

.action-edit:hover {
    color: #fff;
    background: #d97706;
    border-color: #d97706;
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(217, 119, 6, .18);
}

.action-delete {
    color: #b91c1c;
    background: #fef2f2;
    border-color: #fecaca;
}

.action-delete:hover {
    color: #fff;
    background: #dc2626;
    border-color: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(220, 38, 38, .18);
}

.nutrition-empty-state {
    min-height: 320px;
    margin: 1.85rem 1rem 1rem;
    padding: 2.5rem 1.25rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    background: #fff;
    border: 1px solid #dbe3ef;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, .04);
}

.nutrition-empty-icon {
    width: 82px;
    height: 82px;
    margin-bottom: 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #bfdbfe;
    border-radius: 50%;
    background: #eff6ff;
    color: #2563eb;
}

.nutrition-empty-icon i {
    font-size: 1.7rem;
}

.nutrition-empty-title {
    margin: 0;
    color: #0f172a;
    font-size: 1.15rem;
    font-weight: 800;
    line-height: 1.45;
}

.nutrition-empty-description {
    max-width: 720px;
    margin: .55rem auto 1.2rem;
    color: #64748b;
    font-size: .92rem;
    line-height: 1.65;
}

.nutrition-empty-button {
    min-height: 44px;
    padding: .65rem 1.15rem;
}

@media (max-width: 767.98px) {
    .nutrition-index-page {
        padding-top: .75rem !important;
        padding-bottom: 1.75rem !important;
    }

    .nutrition-header {
        padding: .9rem;
        align-items: stretch;
    }

    .nutrition-header-left,
    .nutrition-header-actions {
        width: 100%;
    }

    .nutrition-header-actions > * {
        flex: 1 1 calc(50% - .35rem);
    }

    .nutrition-content {
        padding: .75rem;
    }

    .action-buttons {
        min-width: 245px;
    }

    .nutrition-empty-state {
        min-height: 300px;
        margin: 1rem .75rem .75rem;
        padding: 2rem 1rem;
    }
}

@media (max-width: 575.98px) {
    .nutrition-header-actions {
        flex-direction: column;
    }

    .nutrition-header-actions > * {
        width: 100%;
        flex: 1 1 auto;
    }

    .nutrition-header h4 {
        font-size: 1.12rem;
        line-height: 1.35;
    }

    .nutrition-header-subtitle {
        margin-top: .25rem;
        font-size: .9rem;
    }

    .nutrition-empty-state {
        min-height: 280px;
        margin: .75rem;
        padding: 1.75rem .9rem;
    }

    .nutrition-empty-icon {
        width: 72px;
        height: 72px;
    }

    .nutrition-empty-title {
        font-size: 1rem;
    }

    .nutrition-empty-description {
        font-size: .84rem;
    }

    .nutrition-empty-button {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete').forEach(function (button) {
        button.addEventListener('click', function () {
            const form = this.closest('.delete-form');

            if (!form) {
                return;
            }

            if (!window.Swal) {
                if (window.confirm('ยืนยันการลบข้อมูลรายการนี้หรือไม่?')) {
                    button.disabled = true;
                    form.submit();
                }
                return;
            }

            Swal.fire({
                title: 'ยืนยันการลบข้อมูล',
                text: 'เมื่อลบแล้วจะไม่สามารถกู้คืนรายการนี้ได้',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'ใช่, ลบข้อมูล',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                focusCancel: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    button.disabled = true;
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection