@extends('admin.admin_master')
@section('admin')

<style>
    .client-page{padding-top:.5rem}
    .client-toolbar{display:flex;flex-wrap:wrap;gap:.75rem;align-items:center;justify-content:space-between;margin-bottom:1rem}
    .client-toolbar-left,.client-toolbar-right{display:flex;align-items:center;gap:.5rem}
    .client-title-box h4{margin:0;font-weight:700;color:#1f2937}
    .client-title-box p{margin:.15rem 0 0;font-size:13px;color:#6b7280}
    .client-btn{display:inline-flex;align-items:center;justify-content:center;gap:.45rem;border-radius:12px;padding:.58rem .95rem;font-weight:600;box-shadow:0 2px 8px rgba(15,23,42,.06)}
    .client-list-card{border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;box-shadow:0 4px 18px rgba(15,23,42,.04)}
    .client-list-card .card-header{background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);border-bottom:1px solid #eef2f7;padding:1rem}
    .client-list-card .card-title{margin:0;font-size:18px;font-weight:700;color:#1f2937}
    .client-list-card .card-body{padding:1rem}
    .client-filter-label{font-size:.88rem;font-weight:700;color:#334155;margin-bottom:.4rem}
    .client-filter-control{min-height:44px;border-radius:11px;border:1px solid #d7dee8;box-shadow:none}
    .client-filter-control:focus{border-color:#86a9ef;box-shadow:0 0 0 .2rem rgba(59,130,246,.10)}
    .client-list-summary{font-size:13px;color:#64748b;font-weight:500}
    .client-table-wrap{width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch;border:1px solid #edf1f5;border-radius:12px}
    .client-table{margin-bottom:0;min-width:980px;width:100%;vertical-align:middle}
    .client-table thead th{background:#eef4ff;color:#1d4f91;font-weight:700;font-size:13px;border-bottom:1px solid #dbe6f5;white-space:nowrap;vertical-align:middle}
    .client-table tbody td{font-size:14px;vertical-align:middle}
    .client-table tbody tr:hover{background:#fafcff}
    .client-link-image,.client-link-name{text-decoration:none;color:inherit;display:inline-block}
    .client-avatar{width:42px;height:42px;border-radius:50%;object-fit:cover;border:2px solid #e5e7eb;transition:transform .18s ease,box-shadow .18s ease}
    .client-link-image:hover .client-avatar{transform:scale(1.04);box-shadow:0 4px 12px rgba(0,0,0,.12)}
    .client-name{font-weight:700;color:#111827;margin-bottom:2px}
    .client-link-name:hover .client-name{color:#0d6efd}
    .client-subtext{font-size:12px;color:#6b7280}
    .problem-list{margin:0;padding-left:1rem;font-size:13px}
    .status-badge{padding:.45rem .7rem;border-radius:999px;font-size:12px;font-weight:700;white-space:nowrap}
    .action-cell{width:190px;min-width:190px;white-space:nowrap;text-align:center}
    .action-group{display:inline-flex;align-items:center;justify-content:center;gap:.35rem;white-space:nowrap}
    .action-btn{width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px;padding:0;flex:0 0 34px;border:none}
    .action-btn span{line-height:1;font-size:18px}
    .client-pagination-wrap{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.75rem;margin-top:1rem}
    .client-pagination-wrap nav{margin-left:auto}
    .client-pagination-wrap .pagination{margin:0;gap:.25rem;flex-wrap:wrap}
    .client-pagination-wrap .page-link{min-width:38px;height:36px;padding:0 .7rem;border-radius:9px!important;display:inline-flex;align-items:center;justify-content:center;color:#475569;border-color:#dbe3ef;box-shadow:none}
    .client-pagination-wrap .page-item.active .page-link{background:#4f6ef7;border-color:#4f6ef7;color:#fff}
    .client-pagination-wrap .page-item.disabled .page-link{background:#f8fafc;color:#94a3b8}

    .client-search-help{display:flex;align-items:center;gap:.4rem;min-height:20px;margin-top:.35rem;font-size:12px;color:#64748b}
    .client-search-help .spinner-border{width:.8rem;height:.8rem;border-width:.12rem}
    #client-results-card{position:relative;transition:opacity .16s ease}
    #client-results-card.is-loading{opacity:.62;pointer-events:none}
    @media(max-width:767.98px){
        .client-toolbar{align-items:stretch}.client-toolbar-left,.client-toolbar-right{width:100%;justify-content:space-between}.client-toolbar-right .client-btn{width:100%}
        .client-list-card .card-header,.client-list-card .card-body{padding:.85rem}.client-table{min-width:960px}.client-pagination-wrap{justify-content:center;text-align:center}.client-pagination-wrap nav{margin:0 auto}
    }
</style>

<div class="content">
    <div class="container-fluid client-page">
        <div class="client-toolbar">
            <div class="client-toolbar-left">
                <a href="{{ route('dashboard') }}" class="btn btn-primary client-btn">
                    <i data-feather="arrow-left-circle"></i><span>ย้อนกลับ</span>
                </a>
                <div class="client-title-box">
                    <h4>รายการผู้รับบริการ</h4>
                    <p>ค้นหาและจัดการข้อมูลจากฐานข้อมูลโดยตรง รองรับข้อมูลจำนวนมาก</p>
                </div>
            </div>

            @if(auth()->check() && auth()->user()->hasRole(['admin','executive','social_worker']))
                <div class="client-toolbar-right">
                    <a href="{{ route('client.add') }}" class="btn btn-success client-btn">
                        <i data-feather="plus-circle"></i><span>เพิ่มรายการ</span>
                    </a>
                </div>
            @endif
        </div>

        <div class="card client-list-card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('client.show') }}" id="client-search-form"> 
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-lg-4">
                            <label for="client-search" class="client-filter-label">ค้นหาผู้รับบริการ</label>  <i data-feather="zap" aria-hidden="true"></i> <span>พิมพ์แล้วระบบจะค้นหาให้อัตโนมัติ</span>
                            <input type="search" id="client-search" name="search" value="{{ $search ?? request('search') }}"
                                   class="form-control client-filter-control"
                                   placeholder="ชื่อ นามสกุล เลขทะเบียน หรือปัญหา"
                                   maxlength="100" autocomplete="off"
                                   aria-describedby="client-search-status">
                            {{-- <div id="client-search-status" class="client-search-help" role="status" aria-live="polite">
                                <i data-feather="zap" aria-hidden="true"></i>
                                <span>พิมพ์แล้วระบบจะค้นหาให้อัตโนมัติ</span>
                            </div> --}}
                        </div>

                        @if($canFilterProjects ?? false)
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="project_id" class="client-filter-label">หน่วยงาน / โครงการ</label>
                                <select name="project_id" id="project_id" class="form-select client-filter-control">
                                    <option value="all" {{ ($projectId ?? 'all') === 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ (string)($projectId ?? '') === (string)$project->id ? 'selected' : '' }}>
                                            {{ $project->project_name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-6 col-md-3 col-lg-2">
                            <label for="per_page" class="client-filter-label">จำนวนต่อหน้า</label>
                            <select name="per_page" id="per_page" class="form-select client-filter-control">
                                @foreach([15,30,50,100] as $size)
                                    <option value="{{ $size }}" {{ (int)($perPage ?? 30) === $size ? 'selected' : '' }}>{{ $size }} รายการ</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-md-3 col-lg-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary client-btn flex-fill">
                                <i data-feather="search"></i><span>ค้นหา</span>
                            </button>
                            <a href="{{ route('client.show') }}" class="btn btn-outline-secondary client-btn" title="ล้างตัวกรอง">
                                <i data-feather="refresh-ccw"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card client-list-card" id="client-results-card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="card-title">รายการผู้รับ</h5>
                <div class="client-list-summary">
                    @if($clients->total() > 0)
                        แสดง {{ number_format($clients->firstItem()) }}–{{ number_format($clients->lastItem()) }} จาก {{ number_format($clients->total()) }} รายการ
                    @else
                        ไม่พบข้อมูล
                    @endif
                </div>
            </div>

            <div class="card-body">
                @if($clients->isNotEmpty())
                    <div class="client-table-wrap">
                        <table class="table table-hover align-middle client-table">
                            <thead>
                                <tr>
                                    <th>ลำดับ</th><th>ภาพ</th><th>ชื่อ-นามสกุล</th><th>วันที่รับเข้า</th><th>วันเกิด</th><th>อายุ</th><th>ปัญหา</th><th>สถานะ</th><th class="action-cell">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $key => $client)
                                    <tr>
                                        <td>{{ ($clients->firstItem() ?? 1) + $key }}</td>
                                        <td>
                                            <a href="{{ route('admin.index', $client->id) }}" title="ดูข้อมูล" class="client-link-image">
                                                <img src="{{ !empty($client->image) ? asset('upload/client_images/' . $client->image) : asset('upload/no_image.jpg') }}"
                                                     alt="รูปผู้รับบริการ {{ $client->full_name }}" class="client-avatar" loading="lazy" decoding="async"
                                                     onerror="this.onerror=null;this.src='{{ asset('upload/no_image.jpg') }}';">
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.index', $client->id) }}" title="ดูข้อมูล" class="client-link-name">
                                                <div class="client-name">{{ $client->full_name }}</div>
                                                <div class="client-subtext">เลขทะเบียน {{ $client->register_number ?? '-' }}</div>
                                            </a>
                                        </td>
                                        <td>{{ $client->arrival_date }}</td>
                                        <td>{{ $client->birth_date }}</td>
                                        <td>{{ $client->age }}</td>
                                        <td>
                                            @if($client->problems->isNotEmpty())
                                                <ul class="problem-list">
                                                    @foreach($client->problems as $problem)<li>{{ $problem->problem_name }}</li>@endforeach
                                                </ul>
                                            @else
                                                <span class="text-muted small">ไม่มีข้อมูล</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($client->release_status === 'show')
                                                <span class="badge bg-success-subtle text-success status-badge">อยู่ในระบบ</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary status-badge">ไม่อยู่ในระบบ</span>
                                            @endif
                                        </td>
                                        <td class="action-cell">
                                            <div class="action-group">
                                                <a title="ดูข้อมูล" href="{{ route('admin.index', $client->id) }}" class="btn btn-primary btn-sm action-btn"><span class="mdi mdi-eye-circle mdi-18px"></span></a>
                                                <a title="แก้ไข" href="{{ route('client.edit', $client->id) }}" class="btn btn-success btn-sm action-btn"><span class="mdi mdi-book-edit-outline mdi-18px"></span></a>
                                                @if(in_array(auth()->user()->role, ['admin', 'social_worker']))
                                                    <button type="button" title="ลบ" class="btn btn-danger btn-sm action-btn client-delete-btn" data-url="{{ route('client.delete', $client->id) }}"><span class="mdi mdi-trash-can-outline mdi-18px"></span></button>
                                                @endif
                                                <a title="จำหน่าย" href="{{ route('refers.index', $client->id) }}" class="btn btn-secondary btn-sm action-btn"><span class="mdi mdi-file-export-outline mdi-18px"></span></a>
                                                @if(auth()->user()->role === 'admin')
                                                    <a title="ย้ายเคส" href="{{ route('client.transfer.create', $client->id) }}" class="btn btn-warning btn-sm action-btn"><span class="mdi mdi-arrow-right-bold mdi-18px"></span></a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="client-pagination-wrap">
                        <span class="client-list-summary">หน้า {{ number_format($clients->currentPage()) }} จาก {{ number_format($clients->lastPage()) }}</span>
                        {{ $clients->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="alert alert-info mb-0">ไม่พบข้อมูลผู้รับบริการตามเงื่อนไขที่ค้นหา</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.feather) { window.feather.replace(); }

    const form = document.getElementById('client-search-form');
    const searchInput = document.getElementById('client-search');
    const statusBox = document.getElementById('client-search-status');
    const resultSelector = '#client-results-card';
    const debounceDelay = 400;

    let debounceTimer = null;
    let activeController = null;
    let isComposing = false;

    function refreshIcons() {
        if (window.feather) {
            window.feather.replace();
        }
    }

    function setStatus(message, loading = false, isError = false) {
        if (!statusBox) return;

        statusBox.classList.toggle('text-danger', isError);
        statusBox.innerHTML = loading
            ? '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span>' + message + '</span>'
            : '<i data-feather="' + (isError ? 'alert-circle' : 'zap') + '" aria-hidden="true"></i><span>' + message + '</span>';

        refreshIcons();
    }

    function setLoading(loading) {
        const resultCard = document.querySelector(resultSelector);
        if (resultCard) {
            resultCard.classList.toggle('is-loading', loading);
            resultCard.setAttribute('aria-busy', loading ? 'true' : 'false');
        }
    }

    function buildSearchUrl() {
        const url = new URL(form.action, window.location.origin);
        const formData = new FormData(form);

        formData.forEach(function (value, key) {
            const normalizedValue = typeof value === 'string' ? value.trim() : value;

            if (normalizedValue === '') {
                url.searchParams.delete(key);
            } else {
                url.searchParams.set(key, normalizedValue);
            }
        });

        url.searchParams.delete('page');
        return url;
    }

    async function loadResults(url, updateHistory = true) {
        if (activeController) {
            activeController.abort();
        }

        const requestController = new AbortController();
        activeController = requestController;
        setLoading(true);
        setStatus('กำลังค้นหา...', true);

        try {
            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: requestController.signal,
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            const html = await response.text();
            const nextDocument = new DOMParser().parseFromString(html, 'text/html');
            const nextResults = nextDocument.querySelector(resultSelector);
            const currentResults = document.querySelector(resultSelector);

            if (!nextResults || !currentResults) {
                throw new Error('ไม่พบส่วนแสดงรายการ');
            }

            currentResults.replaceWith(nextResults);

            if (updateHistory) {
                window.history.replaceState({}, '', url.toString());
            }

            setStatus('แสดงผลการค้นหาล่าสุดแล้ว');
            refreshIcons();
        } catch (error) {
            if (error.name === 'AbortError') return;

            console.error('Live search failed:', error);
            setStatus('ค้นหาอัตโนมัติไม่สำเร็จ กรุณากดปุ่มค้นหา', false, true);
        } finally {
            if (activeController === requestController) {
                setLoading(false);
                activeController = null;
            }
        }
    }

    function scheduleSearch() {
        window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(function () {
            loadResults(buildSearchUrl());
        }, debounceDelay);
    }

    if (form && searchInput) {
        searchInput.addEventListener('compositionstart', function () {
            isComposing = true;
        });

        searchInput.addEventListener('compositionend', function () {
            isComposing = false;
            scheduleSearch();
        });

        searchInput.addEventListener('input', function () {
            if (!isComposing) scheduleSearch();
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            window.clearTimeout(debounceTimer);
            loadResults(buildSearchUrl());
        });

        form.querySelectorAll('select').forEach(function (select) {
            select.addEventListener('change', function () {
                window.clearTimeout(debounceTimer);
                loadResults(buildSearchUrl());
            });
        });
    }

    document.addEventListener('click', function (event) {
        const paginationLink = event.target.closest(resultSelector + ' .pagination a');
        if (paginationLink) {
            event.preventDefault();
            loadResults(new URL(paginationLink.href, window.location.origin));
            return;
        }

        const button = event.target.closest('.client-delete-btn');
        if (!button) return;

        event.preventDefault();
        const deleteUrl = button.dataset.url;
        if (!deleteUrl) return;

        Swal.fire({
            title: 'ยืนยันการลบข้อมูล?',
            text: 'ระบบจะเปลี่ยนสถานะข้อมูลตามขั้นตอนเดิม',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
            focusCancel: true,
            allowOutsideClick: false
        }).then(function (result) {
            if (result.isConfirmed) window.location.href = deleteUrl;
        });
    });

    window.addEventListener('popstate', function () {
        loadResults(new URL(window.location.href), false);
    });
});
</script>
@endpush