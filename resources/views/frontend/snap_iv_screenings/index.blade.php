@extends('admin_client.admin_client')

@section('content')
@php
    $hasScreenings = $screenings->count() > 0;
    $clientName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
@endphp

<style>
    .snap-index-page {
        padding: 1.25rem 0 2.5rem;
    }

    .snap-header-card,
    .snap-table-card,
    .snap-empty-card {
        background: #fff;
        border: 1px solid #dbe3ef;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.045);
    }

    .snap-header-card {
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.75rem;
    }

    .snap-header-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .snap-header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
        min-width: 0;
    }

    .snap-header-icon {
        width: 54px;
        height: 54px;
        border-radius: 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 54px;
        background: #eff6ff;
        color: #2563eb;
    }

    .snap-header-icon i {
        font-size: 1.35rem;
    }

    .snap-header-text {
        min-width: 0;
    }

    .snap-header-title {
        margin: 0;
        color: #0f172a;
        font-size: clamp(1.25rem, 1.6vw, 1.5rem);
        font-weight: 800;
        line-height: 1.3;
        letter-spacing: -0.01em;
    }

    .snap-header-subtitle {
        margin-top: .35rem;
        color: #64748b;
        font-size: clamp(.92rem, 1vw, 1rem);
        font-weight: 500;
        line-height: 1.5;
    }

    .snap-header-subtitle strong {
        color: #0f172a;
        font-weight: 800;
    }

    .snap-header-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .65rem;
        flex-wrap: wrap;
    }

    .snap-add-btn,
    .snap-back-btn,
    .snap-empty-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        min-height: 40px;
        border-radius: 11px;
        font-weight: 700;
        white-space: nowrap;
        transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease;
    }

    .snap-add-btn {
        padding: .5rem .9rem;
        box-shadow: 0 6px 15px rgba(37, 99, 235, .16);
    }

    .snap-back-btn {
        padding: .5rem .9rem;
        color: #7c3aed;
        border: 1px solid #8b5cf6;
        background: #fff;
        text-decoration: none;
    }

    .snap-back-btn:hover,
    .snap-back-btn:focus {
        color: #6d28d9;
        background: #faf5ff;
        transform: translateY(-1px);
    }

    .snap-add-btn:hover,
    .snap-empty-btn:hover {
        transform: translateY(-1px);
    }

    .snap-table-card {
        overflow: hidden;
    }

    .snap-table {
        min-width: 980px;
        margin: 0;
        vertical-align: middle;
    }

    .snap-table thead th {
        padding: .9rem .8rem;
        color: #334155;
        background: #f8fafc;
        border-bottom: 1px solid #dbe3ef;
        font-size: .86rem;
        font-weight: 800;
        white-space: nowrap;
        text-align: center;
    }

    .snap-table tbody td {
        padding: .95rem .8rem;
        color: #334155;
        border-color: #eef2f7;
        font-size: .92rem;
    }

    .snap-table tbody tr:hover {
        background: #fbfdff;
    }

    .snap-score-detail {
        margin-top: .2rem;
        color: #64748b;
        font-size: .78rem;
        line-height: 1.4;
    }

    .snap-action-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        flex-wrap: nowrap;
    }

    .snap-action-btn {
        width: 34px;
        height: 34px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    .snap-empty-card {
        min-height: 320px;
        padding: 2.5rem 1.25rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .snap-empty-icon {
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

    .snap-empty-icon i {
        font-size: 1.7rem;
    }

    .snap-empty-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.45;
    }

    .snap-empty-description {
        max-width: 720px;
        margin: .55rem auto 1.2rem;
        color: #64748b;
        font-size: .92rem;
        line-height: 1.65;
    }

    .snap-empty-btn {
        min-height: 44px;
        padding: .65rem 1.15rem;
        box-shadow: 0 9px 20px rgba(37, 99, 235, .22);
    }

    @media (max-width: 767.98px) {
        .snap-index-page {
            padding: .75rem 0 1.75rem;
        }

        .snap-header-card {
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .snap-header-left,
        .snap-header-actions {
            width: 100%;
        }

        .snap-header-actions > * {
            flex: 1 1 calc(50% - .35rem);
        }

        .snap-empty-card {
            min-height: 300px;
            padding: 2rem 1rem;
        }
    }

    @media (max-width: 575.98px) {
        .snap-header-left {
            align-items: flex-start;
            gap: .75rem;
        }

        .snap-header-icon {
            width: 48px;
            height: 48px;
            flex-basis: 48px;
            border-radius: 13px;
        }

        .snap-header-actions {
            flex-direction: column;
        }

        .snap-header-actions > * {
            width: 100%;
            flex: 1 1 auto;
        }

        .snap-header-title {
            font-size: 1.12rem;
            line-height: 1.35;
        }

        .snap-header-subtitle {
            margin-top: .25rem;
            font-size: .9rem;
        }

        .snap-empty-card {
            min-height: 280px;
            padding: 1.75rem .9rem;
        }

        .snap-empty-icon {
            width: 72px;
            height: 72px;
        }

        .snap-empty-title {
            font-size: 1rem;
        }

        .snap-empty-description {
            font-size: .84rem;
        }

        .snap-empty-btn {
            width: 100%;
        }
    }
</style>

<div class="container-fluid snap-index-page">

    <div class="snap-header-card">
        <div class="snap-header-inner">
            <div class="snap-header-left">
                <div class="snap-header-icon" aria-hidden="true">
                    <i class="bi bi-clipboard2-pulse"></i>
                </div>

                <div class="snap-header-text">
                    <h1 class="snap-header-title">
                        แบบประเมินพฤติกรรม SNAP-IV
                    </h1>

                    <div class="snap-header-subtitle">
                        ผู้รับบริการ:
                        <strong>{{ $clientName !== '' ? $clientName : '-' }}</strong>
                    </div>
                </div>
            </div>

            <div class="snap-header-actions">
                @if($hasScreenings)
                    <a href="{{ route('snap-iv.create', $client->id) }}"
                       class="btn btn-primary snap-add-btn">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มแบบประเมิน</span>
                    </a>
                @endif

                <a href="{{ route('admin.index', $client->id) }}"
                   class="snap-back-btn"
                   aria-label="กลับหน้าหลักผู้รับบริการ">
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

        <div class="snap-table-card">
            <div class="table-responsive">
                <table class="table snap-table">
                    <thead>
                        <tr>
                            <th>วันที่ประเมิน</th>
                            <th>ขาดสมาธิ</th>
                            <th>ซน/หุนหันพลันแล่น</th>
                            <th>ดื้อ/ต่อต้าน</th>
                            <th>คะแนนรวม</th>
                            <th>ผู้ประเมิน</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($screenings as $item)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $item->screening_date?->format('d/m/Y') ?: '-' }}
                                </td>

                                <td class="text-center">
                                    <div class="fw-bold">{{ $item->inattention_score }}</div>
                                    <div class="snap-score-detail">
                                        {{ $item->inattention_level ?: '-' }}
                                    </div>
                                </td>

                                <td class="text-center">
                                    <div class="fw-bold">{{ $item->hyperactivity_score }}</div>
                                    <div class="snap-score-detail">
                                        {{ $item->hyperactivity_level ?: '-' }}
                                    </div>
                                </td>

                                <td class="text-center">
                                    <div class="fw-bold">{{ $item->oppositional_score }}</div>
                                    <div class="snap-score-detail">
                                        {{ $item->oppositional_level ?: '-' }}
                                    </div>
                                </td>

                                <td class="fw-bold text-center">
                                    {{ $item->total_score }}
                                </td>

                                <td>
                                    {{ $item->observer_name ?: '-' }}
                                </td>

                                <td class="text-center">
                                    <div class="snap-action-group">
                                        <a href="{{ route('snap-iv.show', $item->id) }}"
                                           class="btn btn-outline-primary snap-action-btn"
                                           title="ดูข้อมูล"
                                           aria-label="ดูข้อมูล">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('snap-iv.edit', $item->id) }}"
                                           class="btn btn-outline-warning snap-action-btn"
                                           title="แก้ไขแบบประเมิน"
                                           aria-label="แก้ไขแบบประเมิน">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('snap-iv.destroy', $item->id) }}"
                                              method="POST"
                                              class="snap-delete-form m-0">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-outline-danger snap-action-btn"
                                                    title="ลบข้อมูล"
                                                    aria-label="ลบข้อมูล">
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

        @if(method_exists($screenings, 'links'))
            <div class="mt-3">
                {{ $screenings->links() }}
            </div>
        @endif

    @else

        <div class="snap-empty-card">
            <div class="snap-empty-icon" aria-hidden="true">
                <i class="bi bi-clipboard2-pulse"></i>
            </div>

            <h2 class="snap-empty-title">
                ยังไม่มีข้อมูลแบบประเมิน SNAP-IV
            </h2>

            <p class="snap-empty-description">
                เริ่มต้นบันทึกแบบประเมินพฤติกรรม SNAP-IV ของผู้รับบริการรายนี้
            </p>

            <a href="{{ route('snap-iv.create', $client->id) }}"
               class="btn btn-primary snap-empty-btn">
                <i class="bi bi-plus-circle"></i>
                <span>เพิ่มแบบประเมิน SNAP-IV ครั้งแรก</span>
            </a>
        </div>

    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.snap-delete-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            if (!window.Swal) {
                if (window.confirm('ยืนยันการลบข้อมูลรายการนี้หรือไม่?')) {
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
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

@endsection