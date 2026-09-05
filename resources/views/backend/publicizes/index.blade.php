@extends('admin.admin_master')
@section('admin')



<div class="container-fluid py-4 publicize-page">

    {{-- =========================
        Header
    ========================== --}}
    <div class="publicize-hero mb-4">
        <div class="publicize-hero__left">
            <div class="publicize-hero__top">
                <a href="{{ url()->previous() }}" class="btn btn-light publicize-icon-btn" title="ย้อนกลับ">
                    <i class="bi bi-arrow-left"></i>
                </a>

                <div class="publicize-hero__title-wrap">
                    <h2 class="mb-1 fw-bold publicize-hero__title">
                        <i class="bi bi-megaphone-fill me-2 text-primary"></i>
                        ระบบประชาสัมพันธ์
                    </h2>
                    <div class="text-muted publicize-hero__subtitle">
                        จัดการเอกสารประชาสัมพันธ์ แยกตามหมวดหมู่ ค้นหาได้ตามปี และเข้าถึงไฟล์ได้สะดวก
                    </div>
                </div>
            </div>
        </div>

        <div class="publicize-hero__actions">
           <a href="{{ route('client.show') }}"
                class="btn btn-outline-secondary publicize-head-btn">
                    <i class="bi bi-house-door me-1"></i>
                    <span>หน้าผู้รับบริการ</span>
                </a>

                        @if($publicizes->count())
                    <a href="{{ route('publicizes.create') }}" 
                    class="btn btn-primary publicize-head-btn">
                        <i class="bi bi-plus-circle me-1"></i>
                        <span>เพิ่มข้อมูล</span>
                    </a>
                @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm publicize-alert d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="row g-4">
        {{-- =========================
            Sidebar
        ========================== --}}
        <div class="col-xl-3 col-lg-4">
            <div class="card border-0 shadow-sm publicize-sidebar">
                <div class="card-header publicize-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-folder2-open text-primary"></i>
                        <span class="fw-bold">หมวดหมู่ประกาศ</span>
                    </div>
                </div>

                <div class="publicize-filter-box">
                    <form method="GET" action="{{ route('publicizes.index') }}" class="publicize-filter-form">
                        <input type="hidden" name="category" value="{{ $activeCategory }}">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">

                        <label class="form-label small fw-semibold mb-2">
                            <i class="bi bi-calendar3 me-1 text-primary"></i>
                            ค้นหาตามปี พ.ศ.
                        </label>

                        <div class="publicize-filter-row">
                            <select name="year_be" class="form-select form-select-sm">
                                <option value="">ทุกปี</option>
                                @foreach($yearOptions as $year)
                                    <option value="{{ $year }}" {{ (string)$yearBe === (string)$year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit" class="btn btn-primary btn-sm px-3">
                                <i class="bi bi-search me-1"></i> ค้นหา
                            </button>
                        </div>

                        @if($yearBe)
                            <a href="{{ route('publicizes.index', [
                                    'category' => $activeCategory,
                                    'per_page' => $perPage
                                ]) }}"
                               class="btn btn-light btn-sm border w-100 publicize-reset-btn">

                                <i class="bi bi-arrow-counterclockwise me-1"></i>
                                ล้างตัวกรองปี
                            </a>
                        @endif
                    </form>
                </div>

                <div class="list-group list-group-flush publicize-category-list">
                    @foreach($categories as $key => $label)
                        <a href="{{ route('publicizes.index', array_filter([
                                'category' => $key,
                                'year_be' => $yearBe,
                                'per_page' => $perPage
                           ])) }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $activeCategory === $key ? 'active' : '' }}">
                            <span class="d-flex align-items-center gap-2">
                                @if($key === 'all')
                                    <i class="bi bi-collection"></i>
                                @elseif($key === 'news')
                                    <i class="bi bi-newspaper"></i>
                                @elseif($key === 'activity')
                                    <i class="bi bi-calendar-event"></i>
                                @elseif($key === 'law')
                                    <i class="bi bi-bank"></i>
                                @elseif($key === 'book')
                                    <i class="bi bi-journal-text"></i>
                                @elseif($key === 'policy')
                                    <i class="bi bi-shield-check"></i>
                                @elseif($key === 'mou')
                                    <i class="bi bi-file-earmark-text"></i>
                                @elseif($key === 'form')
                                    <i class="bi bi-ui-checks-grid"></i>
                                @elseif($key === 'manual')
                                    <i class="bi bi-book"></i>
                                @else
                                    <i class="bi bi-folder2"></i>
                                @endif
                                <span>{{ $label }}</span>
                            </span>

                            <span class="badge {{ $activeCategory === $key ? 'bg-white text-primary' : 'bg-light text-dark' }}">
                                {{ $categoryCounts[$key] ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- =========================
            Content
        ========================== --}}
        <div class="col-xl-9 col-lg-8">
            <div class="card border-0 shadow-sm publicize-content">
                <div class="card-header publicize-card-header publicize-content-head">
                    <div>
                        <h5 class="mb-1 fw-bold d-flex align-items-center gap-2">
                            <i class="bi bi-card-list text-primary"></i>
                            <span>{{ $categories[$activeCategory] ?? 'รายการทั้งหมด' }}</span>
                        </h5>

                        <div class="text-muted small">
                            @if($yearBe)
                                กรองข้อมูลตามปี พ.ศ. {{ $yearBe }}
                            @else
                                แสดงข้อมูลทั้งหมดตามหมวดหมู่ที่เลือก
                            @endif
                        </div>
                    </div>

                    <div class="publicize-list-tools">
                        <div class="publicize-count-badge">
                            ทั้งหมด {{ number_format($publicizes->total()) }} รายการ
                        </div>

                        <form method="GET"
                              action="{{ route('publicizes.index') }}"
                              class="publicize-per-page-form">
                            <input type="hidden" name="category" value="{{ $activeCategory }}">

                            @if($yearBe)
                                <input type="hidden" name="year_be" value="{{ $yearBe }}">
                            @endif

                            <label for="per_page" class="small text-muted text-nowrap mb-0">
                                แสดง
                            </label>

                            <select name="per_page"
                                    id="per_page"
                                    class="form-select form-select-sm publicize-per-page-select"
                                    onchange="this.form.submit()">
                                @foreach($perPageOptions as $option)
                                    <option value="{{ $option }}" {{ (int) $perPage === (int) $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>

                            <span class="small text-muted text-nowrap">รายการ/หน้า</span>
                        </form>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($publicizes->count())
                        <div class="publicize-list">
                          @foreach($publicizes as $index => $item)
    <div class="publicize-item border-bottom">
        <div class="publicize-item__body">

            {{-- ลำดับ --}}
            <div class="publicize-item__no">
                {{ $publicizes->firstItem() + $index }}
            </div>

            {{-- ชื่อเอกสาร --}}
            <div class="publicize-item__main">
                <a href="{{ route('publicizes.file', $item) }}"
                   target="_blank"
                   class="publicize-file-link">
                    <span class="publicize-pdf-icon">
                        PDF
                    </span>
                    <span class="publicize-file-title">
                        {{ $item->title }}
                    </span>
                </a>
            </div>

            {{-- วันที่เผยแพร่ --}}
            <div class="publicize-item__date">
                {{ optional($item->recorded_at)->format('d/m/') }}{{ optional($item->recorded_at)->year + 543 }}
            </div>

            {{-- UNIFIED_ACCESS_SCOPE_V5: action buttons ตาม permission ไม่ผูกกับ role --}}
            @if(auth()->check() && (auth()->user()->canUpdateForm('communications_publicizes') || auth()->user()->canDeleteForm('communications_publicizes')))
                <div class="publicize-admin-actions">
                    @if(auth()->user()->canUpdateForm('communications_publicizes'))
                        <a href="{{ route('publicizes.edit', $item->id) }}"
                           class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                    @endif

                    @if(auth()->user()->canDeleteForm('communications_publicizes'))
                    <form action="{{ route('publicizes.destroy', $item->id) }}"
                          method="POST"
                          class="delete-publicize-form d-inline">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="btn btn-outline-danger btn-sm delete-publicize-btn"
                                data-title="{{ $item->title }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>
            @endif

        </div>
    </div>
@endforeach
                        </div>

                        @if($publicizes->hasPages())
                            <div class="publicize-pagination border-top px-3 py-3">
                                {{ $publicizes->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    @else
                        <div class="publicize-empty-state">
                            <div class="publicize-empty-state__icon">
                                <i class="bi bi-folder-x"></i>
                            </div>
                            <div class="fw-bold mb-2">ยังไม่มีข้อมูลในเงื่อนไขที่เลือก</div>
                            <div class="text-muted mb-3">
                                สามารถเพิ่มข้อมูลประชาสัมพันธ์ใหม่ได้ทันที
                            </div>
                            <div class="d-flex justify-content-center flex-wrap gap-2">
                                <a href="{{ route('publicizes.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i> เพิ่มข้อมูล
                                </a>
                                <a href="{{ route('publicizes.index') }}" class="btn btn-light border">
                                    <i class="bi bi-arrow-repeat me-1"></i> รีเซ็ตการแสดงผล
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

    <style>
    .publicize-page{
        --publicize-primary: #0d6efd;
        --publicize-primary-soft: rgba(13, 110, 253, 0.10);
        --publicize-border: #e9eef5;
        --publicize-text-soft: #6c757d;
        --publicize-bg-soft: #f8fafc;
        --publicize-radius: 18px;
        --publicize-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
    }

    .publicize-page .publicize-hero{
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        padding: 18px 20px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        border: 1px solid var(--publicize-border);
        border-radius: 20px;
        box-shadow: var(--publicize-shadow);
    }

    .publicize-page .publicize-hero__top{
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .publicize-page .publicize-hero__title{
        font-size: 1.45rem;
        line-height: 1.2;
    }

    .publicize-page .publicize-hero__subtitle{
        font-size: 0.95rem;
    }

    .publicize-page .publicize-hero__actions{
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .publicize-page .publicize-icon-btn{
        width: 42px;
        height: 42px;
        border-radius: 12px;
        border: 1px solid var(--publicize-border);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    }

    .publicize-page .publicize-icon-btn:hover{
        background: #f3f6fa;
    }

    .publicize-page .publicize-head-btn{
        min-height: 42px;
        padding: 0.6rem 1rem;
        border-radius: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    .publicize-page .publicize-sidebar,
    .publicize-page .publicize-content{
        border-radius: var(--publicize-radius);
        overflow: hidden;
        border: 1px solid var(--publicize-border);
        box-shadow: var(--publicize-shadow);
        background: #fff;
    }

    .publicize-page .publicize-card-header{
        padding: 1rem 1.2rem;
        background: #fff;
        border-bottom: 1px solid var(--publicize-border);
    }

    .publicize-page .publicize-filter-box{
        padding: 1rem 1rem 0.9rem;
        border-bottom: 1px solid var(--publicize-border);
        background: #fcfdff;
    }

    .publicize-page .publicize-filter-row{
        display: flex;
        gap: 8px;
    }

    .publicize-page .publicize-filter-form .form-select,
    .publicize-page .publicize-filter-form .btn{
        min-height: 40px;
    }

    .publicize-page .publicize-reset-btn{
        font-weight: 500;
    }

    .publicize-page .publicize-category-list .list-group-item{
        border: 0;
        border-radius: 0;
        padding: 14px 18px;
        font-weight: 500;
        transition: all .2s ease;
    }

    .publicize-page .publicize-category-list .list-group-item:hover{
        background: var(--publicize-bg-soft);
    }

    .publicize-page .publicize-category-list .list-group-item.active{
        background: var(--publicize-primary);
        color: #fff;
    }

    .publicize-page .publicize-content-head{
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .publicize-page .publicize-count-badge{
        background: var(--publicize-bg-soft);
        color: #334155;
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .publicize-page .publicize-list-tools{
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .publicize-page .publicize-per-page-form{
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .publicize-page .publicize-per-page-select{
        width: 72px;
        min-width: 72px;
        height: 36px;
        border-radius: 9px;
        font-weight: 600;
    }

    .publicize-page .publicize-pagination{
        background: #fff;
    }

    .publicize-page .publicize-pagination nav{
        display: flex;
        justify-content: center;
    }

    .publicize-page .publicize-pagination .pagination{
        margin-bottom: 0;
        flex-wrap: wrap;
        justify-content: center;
        gap: 3px;
    }

    .publicize-page .publicize-pagination .page-link{
        min-width: 36px;
        height: 36px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .9rem;
    }

    .publicize-page .publicize-list{
        background:#fff;
    }

    /* =========================
    TABLE-LIKE LIST
    ========================= */
    .publicize-page .publicize-item{
        border-bottom:1px solid #e5eaf0 !important;
        transition:background .15s ease;
    }

    .publicize-page .publicize-item:hover{
        background:#f8fbff;
    }

    .publicize-page .publicize-item:last-child{
        border-bottom:0 !important;
    }

    .publicize-page .publicize-item__body{
        display:grid;
        grid-template-columns: 46px minmax(0, 1fr) 150px auto;
        align-items:center;
        gap:10px;
        padding:9px 16px;
        min-height:44px;
    }

    /* ลำดับ */
    .publicize-page .publicize-item__no{
        color:#64748b;
        font-weight:600;
        font-size:.95rem;
    }

    /* ชื่อไฟล์ */
    .publicize-page .publicize-file-link{
        display:inline-flex;
        align-items:center;
        gap:8px;
        max-width:100%;
        color:#0d6efd;
        font-size:.98rem;
        font-weight:700;
        line-height:1.35;
        text-decoration:none;
    }

    .publicize-page .publicize-file-link:hover{
        color:#0b5ed7;
        text-decoration:underline;
    }

    .publicize-page .publicize-file-title{
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
    }

    /* ไอคอน PDF แบบเล็กเหมือนตาราง */
    .publicize-page .publicize-pdf-icon{
        width:22px;
        height:16px;
        flex:0 0 22px;
        border-radius:2px;
        background:#dc3545;
        color:#fff;
        font-size:7px;
        line-height:16px;
        text-align:center;
        font-weight:800;
        letter-spacing:-.2px;
        box-shadow:0 1px 2px rgba(15,23,42,.15);
    }

    /* วันที่ */
    .publicize-page .publicize-item__date{
        color:#64748b;
        font-size:.95rem;
        font-weight:600;
        white-space:nowrap;
    }

    /* ปุ่ม admin */
    .publicize-page .publicize-admin-actions{
        display:flex;
        align-items:center;
        justify-content:flex-end;
        gap:6px;
    }

    .publicize-page .publicize-admin-actions .btn{
        width:32px;
        height:32px;
        padding:0;
        border-radius:8px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
    }

    /* =========================
   EMPTY STATE
========================= */
.publicize-page .publicize-empty-state{
    min-height:320px;
    padding:48px 20px;

    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;

    text-align:center;
}

.publicize-page .publicize-empty-state__icon{
    width:74px;
    height:74px;
    margin-bottom:18px;

    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;

    background:#f1f5ff;
    color:#0d6efd;

    font-size:2rem;
}

.publicize-page .publicize-empty-state .fw-bold{
    font-size:1.08rem;
    color:#0f172a;
}

.publicize-page .publicize-empty-state .text-muted{
    max-width:420px;
    margin:auto;
    line-height:1.6;
}

.publicize-page .publicize-empty-state .d-flex{
    margin-top:12px;
}


    /* =========================
    MOBILE
    ========================= */
    @media (max-width: 767.98px){
        .publicize-page .publicize-item__body{
            grid-template-columns: 34px minmax(0, 1fr);
            gap:8px;
            padding:10px 12px;
        }

        .publicize-page .publicize-item__date{
            grid-column:2 / -1;
            font-size:.82rem;
        }

        .publicize-page .publicize-admin-actions{
            grid-column:2 / -1;
            justify-content:flex-start;
        }

        .publicize-page .publicize-file-title{
            white-space:normal;
        }
    }

    @media (max-width: 1199.98px){
        .publicize-page .publicize-hero{
            padding: 16px;
        }
    }

    @media (max-width: 991.98px){
        .publicize-page .row{
            --bs-gutter-y: 1rem;
        }

        .publicize-page .publicize-action-group{
            width: 100%;
            justify-content: flex-start;
        }
    }

    @media (max-width: 767.98px){
        .publicize-page .publicize-hero__top{
            align-items: flex-start;
        }

        .publicize-page .publicize-hero__title{
            font-size: 1.2rem;
        }

        .publicize-page .publicize-filter-row{
            flex-direction: column;
        }

        .publicize-page .publicize-filter-row .btn,
        .publicize-page .publicize-filter-row .form-select{
            width: 100%;
        }
    }

    @media (max-width: 575.98px){
        .publicize-page .publicize-hero{
            padding: 14px;
            border-radius: 16px;
        }

        .publicize-page .publicize-hero__actions{
            width: 100%;
        }

        .publicize-page .publicize-hero__actions .btn{
            flex: 1 1 100%;
            justify-content: center;
        }

        .publicize-page .publicize-content-head{
            align-items: flex-start;
        }

        .publicize-page .publicize-list-tools,
        .publicize-page .publicize-per-page-form{
            width: 100%;
        }

        .publicize-page .publicize-list-tools{
            justify-content: flex-start;
        }

        .publicize-page .publicize-item__body{
            padding: 14px;
        }

        .publicize-page .publicize-action-group{
            width: 100%;
        }

        .publicize-page .publicize-action-group .btn,
        .publicize-page .publicize-action-group form{
            width: 100%;
        }

        .publicize-page .publicize-action-group .btn{
            justify-content: center;
        }
    }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.delete-publicize-form');

        deleteForms.forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const button = form.querySelector('.delete-publicize-btn');
                const title = button?.getAttribute('data-title') || 'รายการนี้';

                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    html: `คุณต้องการลบ <b>${title}</b> ใช่หรือไม่`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่, ลบเลย',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true,
                    focusCancel: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
    </script>

    @endsection



