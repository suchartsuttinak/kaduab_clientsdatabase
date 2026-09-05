@extends('admin.admin_master')
@section('admin')

@php
    $thaiMonthsShort = [
        1 => 'ม.ค.',
        2 => 'ก.พ.',
        3 => 'มี.ค.',
        4 => 'เม.ย.',
        5 => 'พ.ค.',
        6 => 'มิ.ย.',
        7 => 'ก.ค.',
        8 => 'ส.ค.',
        9 => 'ก.ย.',
        10 => 'ต.ค.',
        11 => 'พ.ย.',
        12 => 'ธ.ค.',
    ];

    $formatThaiDate = function ($value) use ($thaiMonthsShort) {
        if (empty($value)) {
            return '-';
        }

        try {
            $date = $value instanceof \Carbon\CarbonInterface
                ? $value
                : \Carbon\Carbon::parse($value);

            return $date->day
                . ' ' . $thaiMonthsShort[$date->month]
                . ' ' . ($date->year + 543);
        } catch (\Throwable $e) {
            return '-';
        }
    };
@endphp

<style>
    .refer-page{padding-top:.5rem}.refer-toolbar{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.75rem;margin-bottom:1rem}
    .refer-card{border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;box-shadow:0 4px 18px rgba(15,23,42,.04)}
    .refer-card .card-header{background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);border-bottom:1px solid #eef2f7}
    .refer-control{min-height:44px;border-radius:11px;border:1px solid #d7dee8}.refer-label{font-size:.88rem;font-weight:700;color:#334155;margin-bottom:.4rem}
    .refer-table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;border:1px solid #edf1f5;border-radius:12px}.refer-table{min-width:960px;margin-bottom:0}
    .refer-table thead th{background:#eef4ff;color:#1d4f91;font-size:13px;font-weight:700;white-space:nowrap}.refer-table td{vertical-align:middle;font-size:14px}
    .refer-avatar{height:42px;width:42px;object-fit:cover;border-radius:50%;border:2px solid #e5e7eb}.refer-actions{display:inline-flex;gap:.35rem;white-space:nowrap}
    .refer-summary{font-size:13px;color:#64748b}.refer-pagination{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.75rem;margin-top:1rem}.refer-pagination nav{margin-left:auto}
    .refer-pagination .pagination{margin:0;gap:.25rem;flex-wrap:wrap}.refer-pagination .page-link{min-width:38px;height:36px;padding:0 .7rem;border-radius:9px!important;display:inline-flex;align-items:center;justify-content:center;color:#475569;border-color:#dbe3ef}
    .refer-pagination .page-item.active .page-link{background:#4f6ef7;border-color:#4f6ef7;color:#fff}

    .refer-search-help{display:flex;align-items:center;gap:.4rem;min-height:20px;margin-top:.35rem;font-size:12px;color:#64748b}
    .refer-search-help .spinner-border{width:.8rem;height:.8rem;border-width:.12rem}
    #refer-results-card{position:relative;transition:opacity .16s ease}
    #refer-results-card.is-loading{opacity:.62;pointer-events:none}
    @media(max-width:767.98px){.refer-pagination{justify-content:center;text-align:center}.refer-pagination nav{margin:0 auto}}
</style>

<div class="content">
    <div class="container-fluid refer-page">
        <div class="refer-toolbar">
            <div>
                <h4 class="mb-1 fw-bold">รายการผู้รับบริการทั้งหมด / ประวัติการส่งต่อ</h4>
                <div class="text-muted small">ค้นหาและแบ่งหน้าจากฐานข้อมูล รองรับข้อมูลจำนวนมาก</div>
            </div>
            <a href="{{ route('client.show') }}" class="btn btn-success"><i class="mdi mdi-arrow-left"></i> กลับรายการปัจจุบัน</a>
        </div>

        <div class="card refer-card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('client.cases') }}" id="refer-search-form">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-lg-4">
                            <label for="refer-search" class="refer-label">ค้นหาผู้รับบริการ</label>
                            <input type="search" id="refer-search" name="search" value="{{ $search ?? request('search') }}" class="form-control refer-control"
                                   placeholder="{{ ($canAccessSensitiveProblems ?? false) ? 'ชื่อ นามสกุล เลขทะเบียน หรือปัญหา' : 'ชื่อ นามสกุล หรือเลขทะเบียน' }}" maxlength="100" autocomplete="off"
                                   aria-describedby="refer-search-status">
                            {{-- <div id="refer-search-status" class="refer-search-help" role="status" aria-live="polite">
                                <i class="mdi mdi-lightning-bolt-outline" aria-hidden="true"></i>
                                <span>พิมพ์แล้วระบบจะค้นหาให้อัตโนมัติ</span>
                            </div> --}}
                        </div>

                        @if($canFilterProjects ?? false)
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="project_id" class="refer-label">หน่วยงาน / โครงการ</label>
                                <select name="project_id" id="project_id" class="form-select refer-control">
                                    <option value="all" {{ ($projectId ?? 'all') === 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ (string)($projectId ?? '') === (string)$project->id ? 'selected' : '' }}>{{ $project->project_name ?? '-' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-6 col-md-3 col-lg-2">
                            <label for="per_page" class="refer-label">จำนวนต่อหน้า</label>
                            <select name="per_page" id="per_page" class="form-select refer-control">
                                @foreach([15,30,50,100] as $size)
                                    <option value="{{ $size }}" {{ (int)($perPage ?? 30) === $size ? 'selected' : '' }}>{{ $size }} รายการ</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-md-3 col-lg-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill"><i class="mdi mdi-magnify"></i> ค้นหา</button>
                            <a href="{{ route('client.cases') }}" class="btn btn-outline-secondary" title="ล้างตัวกรอง"><i class="mdi mdi-refresh"></i></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card refer-card" id="refer-results-card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="card-title mb-0">รายการผู้รับ</h5>
                <div class="refer-summary">
                    @if($clients->total() > 0)
                        แสดง {{ number_format($clients->firstItem()) }}–{{ number_format($clients->lastItem()) }} จาก {{ number_format($clients->total()) }} รายการ
                    @else
                        ไม่พบข้อมูล
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if($clients->isNotEmpty())
                    <div class="refer-table-wrap">
                        <table class="table table-hover align-middle refer-table">
                            <thead>
                                <tr>
                                    <th>ลำดับ</th><th>ภาพ</th><th>ชื่อ-นามสกุล</th><th>วันที่รับเข้า</th><th>วันเกิด</th><th>อายุ</th>
                                    @if($canAccessSensitiveProblems ?? false)
                                        <th>ปัญหา</th>
                                    @endif
                                    <th>สถานะ</th><th>การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $key => $client)
                                    <tr>
                                        <td>{{ ($clients->firstItem() ?? 1) + $key }}</td>
                                        <td><img src="{{ !empty($client->image)
                                                ? (route('client.image', $client->id)
                                                    . '?thumb=1&v=' . substr(sha1((string) $client->image), 0, 12)
                                                    . '-s' . substr(hash('sha256', (string) session()->getId()), 0, 10))
                                                : asset('upload/no_image.jpg') }}" alt="รูปผู้รับบริการ {{ $client->full_name }}" class="refer-avatar" width="42" height="42" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ asset('upload/no_image.jpg') }}';"></td>
                                        <td><div class="fw-semibold">{{ $client->full_name }}</div><div class="text-muted small">เลขทะเบียน {{ $client->register_number ?? '-' }}</div></td>
                                       <td>{{ $formatThaiDate($client->arrival_date) }}</td>
<td>{{ $formatThaiDate($client->birth_date) }}</td>
<td>{{ $client->age }}</td>
                                        @if($canAccessSensitiveProblems ?? false)
                                            <td>
                                                @if($client->problems->isNotEmpty())
                                                    <ul class="mb-0 ps-3">@foreach($client->problems as $problem)<li>{{ $problem->problem_name }}</li>@endforeach</ul>
                                                @else<span class="text-muted">ไม่มีข้อมูล</span>@endif
                                            </td>
                                        @endif
                                        <td>
                                            @if($client->release_status === 'show')<span class="badge bg-success">อยู่ในระบบ</span>
                                            @elseif($client->release_status === 'refer')<span class="badge bg-secondary">จำหน่าย/ส่งต่อ</span>
                                            @elseif($client->release_status === 'pending_refer')<span class="badge bg-warning text-dark">รออนุมัติส่งต่อ</span>
                                            @else<span class="badge bg-info text-dark">{{ $client->release_status }}</span>@endif
                                        </td>
                                        <td>
                                            <div class="refer-actions">
                                                <a title="ดูข้อมูล" href="{{ route('admin.index', $client->id) }}" class="btn btn-primary btn-sm"><span class="mdi mdi-eye-circle mdi-18px"></span></a>
                                                <a title="จำหน่าย" href="{{ route('refers.index', $client->id) }}" class="btn btn-warning btn-sm"><span class="mdi mdi-arrow-right-bold mdi-18px"></span></a>
                                                @if($client->release_status === 'refer')
                                                    <form method="POST" action="{{ route('client.changeStatus', $client->id) }}" class="d-inline-block change-status-form">@csrf
                                                        <button type="submit" class="btn btn-success btn-sm" title="คืนเข้าสู่ระบบ"><span class="mdi mdi-check-circle mdi-18px"></span></button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="refer-pagination">
                        <span class="refer-summary">หน้า {{ number_format($clients->currentPage()) }} จาก {{ number_format($clients->lastPage()) }}</span>
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
    const form = document.getElementById('refer-search-form');
    const searchInput = document.getElementById('refer-search');
    const statusBox = document.getElementById('refer-search-status');
    const resultSelector = '#refer-results-card';
    const debounceDelay = 400;

    let debounceTimer = null;
    let activeController = null;
    let isComposing = false;

    function setStatus(message, loading = false, isError = false) {
        if (!statusBox) return;

        statusBox.classList.toggle('text-danger', isError);
        statusBox.innerHTML = loading
            ? '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span>' + message + '</span>'
            : '<i class="mdi ' + (isError ? 'mdi-alert-circle-outline' : 'mdi-lightning-bolt-outline') + '" aria-hidden="true"></i><span>' + message + '</span>';
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

        const button = event.target.closest('.change-status-form button[type="submit"]');
        if (!button) return;

        const changeForm = button.closest('.change-status-form');
        if (!changeForm || changeForm.dataset.confirmed === '1') return;

        event.preventDefault();
        Swal.fire({
            title: 'ยืนยันการคืนเข้าสู่ระบบ?',
            text: 'ต้องการปรับสถานะผู้รับบริการกลับมาอยู่ในระบบใช่หรือไม่',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed) {
                changeForm.dataset.confirmed = '1';
                changeForm.submit();
            }
        });
    });

    window.addEventListener('popstate', function () {
        loadResults(new URL(window.location.href), false);
    });
});
</script>
@endpush