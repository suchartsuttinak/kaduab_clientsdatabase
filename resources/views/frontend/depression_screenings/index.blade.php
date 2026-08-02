@extends('admin_client.admin_client')

@section('content')
@php
    $hasScreenings = $screenings->isNotEmpty();

    $clientName = trim(
        ($client->prefix ?? '') .
        ($client->first_name ?? '') . ' ' .
        ($client->last_name ?? '')
    );
@endphp

<style>
    .ds-index-page {
        padding: 24px 0 40px;
    }

    .ds-header-card,
    .ds-table-card,
    .ds-empty-card {
        background: #fff;
        border: 1px solid #dbe3ef;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .05);
    }

    .ds-header-card {
        padding: 1rem 1.25rem;
        margin-bottom: 1.75rem;
    }

    .ds-index-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .ds-index-header-left {
        display: flex;
        align-items: center;
        gap: .85rem;
        min-width: 0;
    }

    .ds-header-icon {
        width: 44px;
        height: 44px;
        border-radius: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        background: #fff1f2;
        color: #dc2626;
    }

    .ds-header-icon i {
        font-size: 1.05rem;
    }

    .ds-header-text {
        min-width: 0;
    }

    .ds-index-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.05rem;
        font-weight: 800;
        line-height: 1.4;
    }

    .ds-index-subtitle {
        margin-top: .15rem;
        color: #64748b;
        font-size: .82rem;
        line-height: 1.45;
    }

    .ds-index-subtitle strong {
        color: #0f172a;
        font-weight: 800;
    }

    .ds-header-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .65rem;
        flex-wrap: wrap;
    }

    .ds-back-btn,
    .ds-add-btn,
    .ds-empty-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        min-height: 42px;
        padding: 10px 18px;
        border-radius: 12px;
        font-weight: 700;
        white-space: nowrap;
        text-decoration: none;
        transition:
            transform .18s ease,
            box-shadow .18s ease,
            background-color .18s ease,
            border-color .18s ease,
            color .18s ease;
    }

    .ds-back-btn {
        color: #7c3aed;
        border: 1px solid #8b5cf6;
        background: #fff;
    }

    .ds-back-btn:hover,
    .ds-back-btn:focus {
        color: #6d28d9;
        background: #faf5ff;
        transform: translateY(-1px);
    }

    .ds-add-btn,
    .ds-empty-btn {
        color: #fff;
        border: 0;
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        box-shadow: 0 7px 16px rgba(220, 38, 38, .22);
    }

    .ds-add-btn:hover,
    .ds-add-btn:focus,
    .ds-empty-btn:hover,
    .ds-empty-btn:focus {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(220, 38, 38, .28);
    }

    .ds-table-card {
        overflow: hidden;
    }

    .ds-table {
        margin: 0;
        min-width: 880px;
    }

    .ds-table thead th {
        background: #f8fafc;
        color: #0f172a;
        font-weight: 800;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 12px;
        white-space: nowrap;
        vertical-align: middle;
    }

    .ds-table tbody td {
        padding: 16px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #eef2f7;
        color: #0f172a;
    }

    .ds-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .ds-score {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        height: 28px;
        padding: 0 10px;
        border-radius: 999px;
        font-size: .85rem;
        font-weight: 800;
        color: #fff;
    }

    .ds-score-normal {
        background: #16a34a;
    }

    .ds-score-risk {
        background: #dc2626;
    }

    .ds-summary {
        white-space: pre-line;
        line-height: 1.7;
        font-weight: 600;
        min-width: 260px;
    }

    .ds-action-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex-wrap: nowrap;
    }

    .ds-action-btn {
        width: 34px;
        height: 34px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        line-height: 1;
    }

    .ds-empty-card {
        min-height: 320px;
        padding: 2.5rem 1.25rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .ds-empty-icon {
        width: 82px;
        height: 82px;
        margin-bottom: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #fecaca;
        border-radius: 50%;
        background: #fff1f2;
        color: #dc2626;
    }

    .ds-empty-icon i {
        font-size: 1.7rem;
    }

    .ds-empty-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.45;
    }

    .ds-empty-description {
        max-width: 720px;
        margin: .55rem auto 1.2rem;
        color: #64748b;
        font-size: .92rem;
        line-height: 1.65;
    }

    .ds-empty-btn {
        min-height: 44px;
        padding: .65rem 1.15rem;
    }

    @media (max-width: 767.98px) {
        .ds-index-page {
            padding: .75rem 0 1.75rem;
        }

        .ds-header-card {
            padding: .9rem;
            margin-bottom: 1rem;
        }

        .ds-index-header {
            align-items: stretch;
        }

        .ds-index-header-left,
        .ds-header-actions {
            width: 100%;
        }

        .ds-header-actions > * {
            flex: 1 1 calc(50% - .35rem);
        }

        .ds-empty-card {
            min-height: 300px;
            padding: 2rem 1rem;
        }
    }

    @media (max-width: 575.98px) {
        .ds-header-actions {
            flex-direction: column;
        }

        .ds-header-actions > * {
            width: 100%;
            flex: 1 1 auto;
        }

        .ds-index-title {
            font-size: .96rem;
        }

        .ds-index-subtitle {
            font-size: .76rem;
        }

        .ds-empty-card {
            min-height: 280px;
            padding: 1.75rem .9rem;
        }

        .ds-empty-icon {
            width: 72px;
            height: 72px;
        }

        .ds-empty-title {
            font-size: 1rem;
        }

        .ds-empty-description {
            font-size: .84rem;
        }

        .ds-empty-btn {
            width: 100%;
        }
    }
</style>

<div class="container-fluid ds-index-page">

    <div class="ds-header-card">
        <div class="ds-index-header">

            <div class="ds-index-header-left">
                <div class="ds-header-icon" aria-hidden="true">
                    <i class="bi bi-emoji-frown"></i>
                </div>

                <div class="ds-header-text">
                    <h1 class="ds-index-title">
                        แบบคัดกรองภาวะซึมเศร้าในวัยรุ่น
                    </h1>

                    <div class="ds-index-subtitle">
                        ผู้รับบริการ:
                        <strong>{{ $clientName !== '' ? $clientName : '-' }}</strong>
                    </div>
                </div>
            </div>

            <div class="ds-header-actions">
                @if($hasScreenings)
                    <a href="{{ route('depression-screenings.create', $client->id) }}"
                       class="ds-add-btn">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มแบบคัดกรอง</span>
                    </a>
                @endif

                <a href="{{ route('client.show') }}"
                   class="ds-back-btn"
                   aria-label="กลับไปหน้าเคส">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>กลับ</span>
                </a>
            </div>

        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="ปิด"></button>
        </div>
    @endif

    @if($hasScreenings)

        <div class="ds-table-card">
            <div class="table-responsive">
                <table class="table ds-table align-middle">
                    <thead>
                        <tr>
                            <th style="width:120px;">วันที่</th>
                            <th style="width:110px;" class="text-center">คะแนนรวม</th>
                            <th>ผลการคัดกรอง</th>
                            <th style="width:160px;">ผู้ประเมิน</th>
                            <th style="width:120px;" class="text-center">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($screenings as $item)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $item->screening_date?->format('d/m/Y') ?: '-' }}
                                </td>

                                <td class="text-center">
                                    <span class="ds-score {{ $item->total_score >= 22 ? 'ds-score-risk' : 'ds-score-normal' }}">
                                        {{ $item->total_score }}
                                    </span>
                                </td>

                                <td>
                                    <div class="ds-summary">
                                        {{ $item->summary }}
                                    </div>
                                </td>

                                <td>
                                    {{ $item->observer_name ?: '-' }}
                                </td>

                                <td class="text-center">
                                    <div class="ds-action-group">

                                        <a href="{{ route('depression-screenings.show', $item->id) }}"
                                           class="btn btn-outline-primary ds-action-btn"
                                           title="ดูข้อมูล"
                                           aria-label="ดูรายละเอียดแบบคัดกรองวันที่ {{ $item->screening_date?->format('d/m/Y') }}">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <form action="{{ route('depression-screenings.destroy', $item->id) }}"
                                              method="POST"
                                              class="depression-delete-form m-0 p-0">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-outline-danger ds-action-btn"
                                                    title="ลบข้อมูล"
                                                    aria-label="ลบแบบคัดกรองวันที่ {{ $item->screening_date?->format('d/m/Y') }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $screenings->links() }}
        </div>

    @else

        <div class="ds-empty-card" role="status">
            <div class="ds-empty-icon" aria-hidden="true">
                <i class="bi bi-emoji-frown"></i>
            </div>

            <h2 class="ds-empty-title">
                ยังไม่มีข้อมูลแบบคัดกรองภาวะซึมเศร้า
            </h2>

            <p class="ds-empty-description">
                เริ่มต้นบันทึกแบบคัดกรองภาวะซึมเศร้าในวัยรุ่นของผู้รับบริการรายนี้
            </p>

            <a href="{{ route('depression-screenings.create', $client->id) }}"
               class="ds-empty-btn">
                <i class="bi bi-plus-circle"></i>
                <span>เพิ่มแบบคัดกรองภาวะซึมเศร้าครั้งแรก</span>
            </a>
        </div>

    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.depression-delete-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const submitButton = form.querySelector('button[type="submit"]');

            if (!window.Swal) {
                if (window.confirm('ยืนยันการลบข้อมูลรายการนี้หรือไม่?')) {
                    if (submitButton) {
                        submitButton.disabled = true;
                    }

                    form.submit();
                }

                return;
            }

            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: 'เมื่อลบแล้วจะไม่สามารถกู้คืนข้อมูลได้',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่, ลบข้อมูล',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
                focusCancel: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    if (submitButton) {
                        submitButton.disabled = true;
                    }

                    form.submit();
                }
            });
        });
    });
});
</script>

@endsection