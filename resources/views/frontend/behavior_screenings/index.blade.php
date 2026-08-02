@extends('admin_client.admin_client')

@section('content')

<style>
    .bs-index-page {
        padding: 24px 0 40px;
    }

    /* =========================
       ส่วนหัวหน้า
    ========================= */
    .bs-index-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 22px;
        padding: 18px 20px;
        background: #fff;
        border: 1px solid #dbe3ef;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
    }

    .bs-index-header-left {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .bs-header-icon {
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: #eef4ff;
        color: #2563eb;
        font-size: 1.25rem;
    }

    .bs-index-title-wrap {
        min-width: 0;
    }

    .bs-index-title {
        margin: 0;
        color: #0f172a;
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.35;
    }

    .bs-index-subtitle {
        margin-top: 3px;
        color: #64748b;
        font-size: .9rem;
        line-height: 1.45;
    }

    .bs-header-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .bs-back-btn,
    .bs-add-btn {
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 12px;
        padding: 9px 15px;
        font-weight: 700;
        white-space: nowrap;
        transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease;
    }

    .bs-back-btn {
        border: 1px solid #dbe3ef;
        background: #fff;
        color: #6d4aff;
    }

    .bs-back-btn:hover {
        background: #faf8ff;
        color: #5634dc;
        transform: translateY(-1px);
    }

    .bs-add-btn {
        border: 0;
        background: linear-gradient(135deg, #2f65e8 0%, #1f53dc 100%);
        color: #fff;
        box-shadow: 0 8px 18px rgba(37, 99, 235, .18);
    }

    .bs-add-btn:hover,
    .bs-add-btn:focus {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 12px 24px rgba(37, 99, 235, .24);
    }

    /* =========================
       ตารางรายการ
    ========================= */
    .bs-table-card {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
        background: #fff;
    }

    .bs-table {
        margin: 0;
        min-width: 980px;
    }

    .bs-table thead th {
        background: #f8fafc;
        color: #0f172a;
        font-weight: 800;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 12px;
        white-space: nowrap;
        vertical-align: middle;
    }

    .bs-table tbody td {
        padding: 16px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #eef2f7;
        color: #0f172a;
    }

    .bs-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .bs-summary {
        min-width: 260px;
        white-space: pre-line;
        line-height: 1.7;
        font-weight: 600;
    }

    .bs-score {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 30px;
        height: 24px;
        padding: 0 9px;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 800;
        color: #fff;
    }

    .bs-score-learning { background: #4f6df5; }
    .bs-score-ld { background: #38aee2; }
    .bs-score-adhd { background: #f59e0b; color: #111827; }
    .bs-score-autism { background: #fb7185; }

    .bs-action-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex-wrap: nowrap;
    }

    .bs-action-btn {
        width: 34px;
        height: 34px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        line-height: 1;
    }

    .bs-action-btn i {
        font-size: 14px;
        line-height: 1;
    }

    /* =========================
       สถานะยังไม่มีข้อมูล
       รูปแบบเดียวกับ /idstation
    ========================= */
    .bs-empty-card {
        min-height: 320px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dbe3ef;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
        overflow: hidden;
    }

    .bs-empty-content {
        width: 100%;
        max-width: 720px;
        padding: 42px 24px;
        text-align: center;
    }

    .bs-empty-icon {
        width: 82px;
        height: 82px;
        margin: 0 auto 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #b9d4ff;
        border-radius: 50%;
        background: #eff6ff;
        color: #2563eb;
        font-size: 2rem;
    }

    .bs-empty-title {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: 1.18rem;
        font-weight: 800;
        line-height: 1.4;
    }

    .bs-empty-description {
        margin: 0 auto 22px;
        color: #64748b;
        font-size: .95rem;
        line-height: 1.7;
    }

    .bs-first-add-btn {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 10px 20px;
        border: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #2f65e8 0%, #1f53dc 100%);
        color: #fff;
        font-weight: 800;
        box-shadow: 0 10px 22px rgba(37, 99, 235, .22);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .bs-first-add-btn:hover,
    .bs-first-add-btn:focus {
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(37, 99, 235, .28);
    }

    @media (max-width: 767.98px) {
        .bs-index-page {
            padding: 14px 0 28px;
        }

        .bs-index-header {
            padding: 15px;
            align-items: stretch;
        }

        .bs-index-header-left {
            width: 100%;
            align-items: flex-start;
        }

        .bs-header-actions {
            width: 100%;
        }

        .bs-back-btn,
        .bs-add-btn {
            flex: 1 1 100%;
            width: 100%;
        }

        .bs-empty-card {
            min-height: 290px;
        }

        .bs-empty-content {
            padding: 36px 18px;
        }

        .bs-empty-icon {
            width: 72px;
            height: 72px;
            font-size: 1.75rem;
        }

        .bs-first-add-btn {
            width: 100%;
        }
    }
</style>

@php
    $hasScreenings = $screenings->count() > 0;
@endphp

<div class="container-fluid bs-index-page">

    <div class="bs-index-header">
        <div class="bs-index-header-left">
            <div class="bs-header-icon" aria-hidden="true">
                <i class="bi bi-clipboard2-pulse"></i>
            </div>

            <div class="bs-index-title-wrap">
                <h1 class="bs-index-title">แบบสังเกตพฤติกรรม 4 โรค</h1>
                <div class="bs-index-subtitle">สำหรับคัดกรองพฤติกรรมเบื้องต้น</div>
            </div>
        </div>

        <div class="bs-header-actions">
            {{-- เมื่อมีข้อมูลแล้วจึงแสดงปุ่มเพิ่มด้านบน --}}
            @if($hasScreenings)
                <a href="{{ route('behavior-screenings.create', $client->id) }}"
                   class="btn bs-add-btn">
                    <i class="bi bi-plus-circle"></i>
                    เพิ่มแบบประเมิน
                </a>
            @endif

            <a href="{{ route('client.show') }}"
               class="btn bs-back-btn">
                <i class="bi bi-arrow-left-circle"></i>
                กลับไปหน้าเคส
            </a>
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

        <div class="card bs-table-card">
            <div class="table-responsive">
                <table class="table bs-table align-middle">
                    <thead>
                        <tr>
                            <th style="width:120px;">วันที่</th>
                            <th>ผลสรุป</th>
                            <th style="width:110px;" class="text-center">เรียนรู้ช้า</th>
                            <th style="width:80px;" class="text-center">LD</th>
                            <th style="width:90px;" class="text-center">ADHD</th>
                            <th style="width:90px;" class="text-center">Autism</th>
                            <th style="width:150px;">ผู้ประเมิน</th>
                            <th style="width:120px;" class="text-center">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($screenings as $item)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $item->screening_date?->format('d/m/Y') }}
                                </td>

                                <td>
                                    <div class="bs-summary">{{ $item->summary }}</div>
                                </td>

                                <td class="text-center">
                                    <span class="bs-score bs-score-learning">
                                        {{ $item->learning_score }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="bs-score bs-score-ld">
                                        {{ $item->ld_score }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="bs-score bs-score-adhd">
                                        {{ $item->adhd_score }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="bs-score bs-score-autism">
                                        {{ $item->autism_score }}
                                    </span>
                                </td>

                                <td>{{ $item->observer_name ?: '-' }}</td>

                                <td class="text-center">
                                    <div class="bs-action-group">
                                        <a href="{{ route('behavior-screenings.show', $item->id) }}"
                                           class="btn btn-outline-primary bs-action-btn"
                                           title="ดูข้อมูล"
                                           aria-label="ดูข้อมูล">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <form action="{{ route('behavior-screenings.destroy', $item->id) }}"
                                              method="POST"
                                              class="behavior-delete-form m-0 p-0">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-outline-danger bs-action-btn"
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

        <div class="mt-3">
            {{ $screenings->links() }}
        </div>

    @else

        <section class="bs-empty-card" aria-labelledby="bs-empty-title">
            <div class="bs-empty-content">
                <div class="bs-empty-icon" aria-hidden="true">
                    <i class="bi bi-clipboard2-pulse"></i>
                </div>

                <h2 class="bs-empty-title" id="bs-empty-title">
                    ยังไม่มีข้อมูลแบบสังเกตพฤติกรรม
                </h2>

                <p class="bs-empty-description">
                    เริ่มต้นบันทึกแบบประเมินครั้งแรก เพื่อคัดกรองพฤติกรรมเบื้องต้นและติดตามผลอย่างเป็นระบบ
                </p>

                <a href="{{ route('behavior-screenings.create', $client->id) }}"
                   class="btn bs-first-add-btn">
                    <i class="bi bi-plus-circle"></i>
                    เพิ่มแบบประเมินครั้งแรก
                </a>
            </div>
        </section>

    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.behavior-delete-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            if (typeof Swal === 'undefined') {
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
                reverseButtons: true,
                focusCancel: true
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