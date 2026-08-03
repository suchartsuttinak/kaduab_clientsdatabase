@php
    $reportQuery = http_build_query(array_filter([
        'start_date' => request('start_date', old('start_date')),
        'end_date'   => request('end_date', old('end_date')),
    ], fn ($value) => $value !== null && $value !== ''));

    $reportUrl = route('job_agencies.report', $client->id)
        . ($reportQuery !== '' ? '?' . $reportQuery : '');
@endphp

<style>
.ja-main-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    min-height: 82px;
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, .045);
}

.ja-header-left {
    display: flex;
    align-items: center;
    gap: .85rem;
    min-width: 0;
}

.ja-header-icon {
    width: 44px;
    height: 44px;
    flex: 0 0 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 13px;
    background: #eff6ff;
    color: #2563eb;
}

.ja-header-icon i {
    font-size: 1.05rem;
}

.ja-header-text {
    min-width: 0;
}

.ja-header-title {
    margin: 0;
    color: #0f172a;
    font-size: clamp(1.25rem, 1.6vw, 1.5rem);
    font-weight: 800;
    line-height: 1.35;
    letter-spacing: -.01em;
}

.ja-header-subtitle {
    margin-top: .3rem;
    color: #64748b;
    font-size: clamp(.92rem, 1vw, 1rem);
    font-weight: 500;
    line-height: 1.45;
}

.ja-header-subtitle strong {
    color: #0f172a;
    font-weight: 800;
}

.ja-header-actions,
.ja-filter-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .65rem;
    flex-wrap: wrap;
}

.ja-btn {
    min-height: 42px;
    padding: .6rem 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    border-radius: 12px;
    font-weight: 700;
    line-height: 1.2;
    white-space: nowrap;
    text-decoration: none;
}

.ja-btn-primary {
    color: #fff;
    border: 1px solid #1d4ed8;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    box-shadow: 0 7px 16px rgba(37, 99, 235, .2);
}

.ja-btn-primary:hover,
.ja-btn-primary:focus {
    color: #fff;
    border-color: #1e40af;
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
}

.ja-btn-back {
    color: #7c3aed;
    border: 1px solid #8b5cf6;
    background: #fff;
}

.ja-btn-back:hover,
.ja-btn-back:focus {
    color: #6d28d9;
    background: #faf5ff;
}

.ja-filter-card {
    padding: 1rem 1.1rem;
    margin-bottom: 1rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, .035);
}

.ja-filter-row {
    display: flex;
    align-items: flex-end;
    gap: 1rem;
    flex-wrap: wrap;
}

.ja-filter-group {
    min-width: 190px;
    flex: 1 1 190px;
}

.ja-filter-group .form-label {
    margin-bottom: .4rem;
    color: #475569;
    font-size: .88rem;
    font-weight: 700;
}

.ja-filter-group .form-control {
    min-height: 42px;
    border-radius: 11px;
}

.ja-filter-error {
    margin-top: .35rem;
    color: #dc2626;
    font-size: .82rem;
}

.ja-first-empty {
    min-height: 330px;
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

.ja-first-empty-icon {
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

.ja-first-empty-icon i {
    font-size: 1.7rem;
}

.ja-first-empty-title {
    margin: 0;
    color: #0f172a;
    font-size: 1.15rem;
    font-weight: 800;
    line-height: 1.45;
}

.ja-first-empty-description {
    max-width: 720px;
    margin: .55rem auto 1.2rem;
    color: #64748b;
    font-size: .92rem;
    line-height: 1.65;
}

.ja-first-empty-btn {
    min-height: 44px;
    padding: .65rem 1.15rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    border-radius: 12px;
    font-weight: 700;
    box-shadow: 0 8px 18px rgba(37, 99, 235, .2);
}

@media (max-width: 767.98px) {
    .ja-main-header {
        padding: .9rem;
        align-items: stretch;
    }

    .ja-header-left,
    .ja-header-actions {
        width: 100%;
    }

    .ja-header-actions > * {
        flex: 1 1 calc(50% - .35rem);
    }

    .ja-header-title {
        font-size: 1.12rem;
    }

    .ja-header-subtitle {
        font-size: .9rem;
    }

    .ja-filter-actions {
        width: 100%;
    }

    .ja-filter-actions > * {
        flex: 1 1 auto;
    }
}

@media (max-width: 575.98px) {
    .ja-header-actions,
    .ja-filter-actions {
        flex-direction: column;
    }

    .ja-header-actions > *,
    .ja-filter-actions > *,
    .ja-first-empty-btn {
        width: 100%;
        flex: 1 1 auto;
    }

    .ja-first-empty {
        min-height: 285px;
        padding: 1.75rem .9rem;
    }

    .ja-first-empty-icon {
        width: 72px;
        height: 72px;
    }
}
</style>

<header class="ja-main-header">
    <div class="ja-header-left">
        <span class="ja-header-icon" aria-hidden="true">
            <i class="bi bi-briefcase-fill"></i>
        </span>

        <div class="ja-header-text">
            <h1 class="ja-header-title">การจัดหางานให้ผู้รับบริการ</h1>
            <div class="ja-header-subtitle">
                ผู้รับบริการ:
                <strong>{{ $client->fullname ?? $client->full_name ?? $client->name ?? '-' }}</strong>
            </div>
        </div>
    </div>

    <div class="ja-header-actions">
        @if($hasAnyJobAgency)
            <a href="{{ $reportUrl }}" class="btn btn-outline-success ja-btn">
                <i class="bi bi-file-earmark-text"></i>
                <span>รายงาน</span>
            </a>

            <button type="button"
                    class="btn ja-btn ja-btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#createJobAgencyModal">
                <i class="bi bi-plus-circle"></i>
                <span>เพิ่มข้อมูล</span>
            </button>
        @endif

        <a href="{{ route('admin.index', $client->id) }}"
           class="btn ja-btn ja-btn-back"
           aria-label="กลับหน้าหลักผู้รับบริการ">
            <i class="bi bi-arrow-left-circle"></i>
            <span>กลับ</span>
        </a>
    </div>
</header>

@if($hasAnyJobAgency || $hasDateFilter)
    <div class="ja-filter-card">
        <form method="GET" action="{{ route('job_agencies.show', $client->id) }}" novalidate>
            <div class="ja-filter-row">
                <div class="ja-filter-group">
                    <label for="start_date" class="form-label">วันที่เริ่มต้น</label>
                    <input type="date"
                           id="start_date"
                           name="start_date"
                           class="form-control @error('start_date', 'filters') is-invalid @enderror"
                           value="{{ request('start_date', old('start_date')) }}"
                           max="{{ now('Asia/Bangkok')->toDateString() }}">
                    @error('start_date', 'filters')
                        <div class="ja-filter-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="ja-filter-group">
                    <label for="end_date" class="form-label">วันที่สิ้นสุด</label>
                    <input type="date"
                           id="end_date"
                           name="end_date"
                           class="form-control @error('end_date', 'filters') is-invalid @enderror"
                           value="{{ request('end_date', old('end_date')) }}"
                           max="{{ now('Asia/Bangkok')->toDateString() }}">
                    @error('end_date', 'filters')
                        <div class="ja-filter-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="ja-filter-actions">
                    <button type="submit" class="btn ja-btn ja-btn-primary">
                        <i class="bi bi-search"></i>
                        <span>ค้นหา</span>
                    </button>

                    <a href="{{ route('job_agencies.show', $client->id) }}"
                       class="btn btn-light ja-btn border">
                        <i class="bi bi-arrow-clockwise"></i>
                        <span>รีเซ็ต</span>
                    </a>

                    @if($hasAnyJobAgency)
                        <a href="{{ $reportUrl }}" class="btn btn-success ja-btn">
                            <i class="bi bi-printer"></i>
                            <span>ดูรายงาน</span>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
@endif
