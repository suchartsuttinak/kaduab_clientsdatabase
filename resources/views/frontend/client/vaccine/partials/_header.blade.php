@php
    $vaccineClientDisplayName = filled($client->fullname ?? null)
        ? $client->fullname
        : trim(
            ($client->prefix ?? '') .
            ($client->first_name ?? '') . ' ' .
            ($client->last_name ?? '')
        );

    if ($vaccineClientDisplayName === '') {
        $vaccineClientDisplayName = $client->full_name ?? $client->name ?? '-';
    }
@endphp

<div class="vaccine-hero mb-3">
    <div class="vaccine-hero__inner">
        <div class="vaccine-hero__left">
            <div class="vaccine-hero__icon" aria-hidden="true">
                <i class="bi bi-capsule-pill"></i>
            </div>

            <div class="vaccine-hero__text">
                <h1 class="vaccine-hero__title">ประวัติการให้วัคซีน</h1>

                <div class="vaccine-hero__client">
                    ผู้รับบริการ:
                    <strong>{{ $vaccineClientDisplayName ?: '-' }}</strong>
                </div>
            </div>
        </div>

        <div class="vaccine-hero__actions">
            @unless($isVaccineFirstEmptyState ?? false)
                <button type="button"
                        class="vaccine-hero__btn vaccine-hero__btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#add-vaccine-modal">
                    <i class="bi bi-plus-circle"></i>
                    <span>เพิ่มข้อมูลวัคซีน</span>
                </button>
            @endunless

            <a href="{{ route('admin.index', $client->id) }}"
               class="vaccine-hero__btn vaccine-hero__btn-back"
               aria-label="กลับหน้าหลักผู้รับบริการ">
                <i class="bi bi-arrow-left-circle"></i>
                <span>กลับ</span>
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
.vaccine-page {
    --vaccine-primary: #2563eb;
    --vaccine-primary-dark: #1d4ed8;
    --vaccine-success: #16a34a;
    --vaccine-warning: #f59e0b;
    --vaccine-danger: #dc2626;
    --vaccine-text: #0f172a;
    --vaccine-text-soft: #64748b;
    --vaccine-border: #dbe3ef;
    --vaccine-border-soft: #eef2f7;
    --vaccine-bg-soft: #f8fafc;
    --vaccine-bg-soft-2: #f1f5f9;
    --vaccine-shadow-sm: 0 8px 20px rgba(15, 23, 42, 0.05);
    --vaccine-shadow-md: 0 14px 32px rgba(15, 23, 42, 0.08);
    --vaccine-radius: 18px;
    --vaccine-radius-md: 14px;
    --vaccine-radius-sm: 12px;
}

.vaccine-page .vaccine-hero,
.vaccine-page .vaccine-summary-card,
.vaccine-page .vaccine-table-card {
    border-radius: var(--vaccine-radius);
}

.vaccine-page .vaccine-hero {
    position: relative;
    overflow: hidden;
    min-height: 112px;
    padding: 1.15rem 1.35rem;
    border: 1px solid #dbeafe;
    border-radius: 18px;
    background: linear-gradient(135deg, #eef5ff 0%, #f8fbff 62%, #ffffff 100%);
    box-shadow: 0 10px 28px rgba(37, 99, 235, 0.075);
}

.vaccine-page .vaccine-hero::after {
    content: "";
    position: absolute;
    top: -72px;
    right: -58px;
    width: 190px;
    height: 190px;
    border: 26px solid rgba(37, 99, 235, 0.045);
    border-radius: 50%;
    pointer-events: none;
}

.vaccine-page .vaccine-hero__inner {
    position: relative;
    z-index: 1;
    min-height: 78px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.vaccine-page .vaccine-hero__left {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 0;
}

.vaccine-page .vaccine-hero__icon {
    width: 60px;
    height: 60px;
    border-radius: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    background: linear-gradient(145deg, #dbeafe, #eff6ff);
    color: var(--vaccine-primary);
    border: 1px solid #bfdbfe;
    box-shadow: 0 8px 18px rgba(37, 99, 235, 0.12);
}

.vaccine-page .vaccine-hero__icon i {
    font-size: 1.45rem;
    line-height: 1;
}

.vaccine-page .vaccine-hero__text {
    min-width: 0;
}

.vaccine-page .vaccine-hero__title {
    margin: 0;
    color: #1e3a5f;
    font-size: 1.28rem;
    font-weight: 800;
    line-height: 1.35;
}

.vaccine-page .vaccine-hero__client {
    margin-top: .3rem;
    color: #64748b;
    font-size: .88rem;
    line-height: 1.5;
}

.vaccine-page .vaccine-hero__client strong {
    color: #0f172a;
    font-weight: 800;
}

.vaccine-page .vaccine-hero__actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .7rem;
    flex-wrap: wrap;
}

.vaccine-page .vaccine-hero__btn,
.vaccine-page .vaccine-btn {
    min-height: 42px;
    padding: .55rem .95rem;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    border: 1px solid transparent;
    font-size: .86rem;
    font-weight: 700;
    line-height: 1.2;
    text-decoration: none;
    white-space: nowrap;
    transition:
        transform .18s ease,
        box-shadow .18s ease,
        background-color .18s ease,
        border-color .18s ease,
        color .18s ease;
}

.vaccine-page .vaccine-hero__btn:hover,
.vaccine-page .vaccine-btn:hover {
    transform: translateY(-1px);
}

.vaccine-page .vaccine-hero__btn-primary,
.vaccine-page .vaccine-btn-primary {
    color: #fff;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    border-color: #2563eb;
    box-shadow: 0 8px 18px rgba(37, 99, 235, .20);
}

.vaccine-page .vaccine-hero__btn-primary:hover,
.vaccine-page .vaccine-hero__btn-primary:focus,
.vaccine-page .vaccine-btn-primary:hover,
.vaccine-page .vaccine-btn-primary:focus {
    color: #fff;
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
    border-color: #1d4ed8;
    box-shadow: 0 11px 22px rgba(37, 99, 235, .26);
}

.vaccine-page .vaccine-hero__btn-back {
    color: #7c3aed;
    background: rgba(255, 255, 255, .92);
    border-color: #8b5cf6;
    box-shadow: 0 5px 12px rgba(124, 58, 237, .08);
}

.vaccine-page .vaccine-hero__btn-back:hover,
.vaccine-page .vaccine-hero__btn-back:focus {
    color: #6d28d9;
    background: #faf5ff;
    border-color: #7c3aed;
    box-shadow: 0 8px 16px rgba(124, 58, 237, .12);
}

.vaccine-page .vaccine-toolbar-scroll {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.vaccine-page .vaccine-toolbar-actions {
    display: flex;
    gap: .75rem;
    flex-wrap: nowrap;
    width: max-content;
    min-width: 100%;
    justify-content: flex-end;
}

@media (max-width: 767.98px) {
    .vaccine-page {
        padding-top: .85rem !important;
        padding-right: .65rem !important;
        padding-left: .65rem !important;
    }

    .vaccine-page .vaccine-hero {
        min-height: 166px;
        padding: 1.35rem 1rem;
        border-radius: 16px;
    }

    .vaccine-page .vaccine-hero__inner {
        min-height: 120px;
        align-content: center;
        row-gap: 1rem;
    }

    .vaccine-page .vaccine-hero__left,
    .vaccine-page .vaccine-hero__actions {
        width: 100%;
    }

    .vaccine-page .vaccine-hero__actions > * {
        flex: 1 1 calc(50% - .35rem);
    }
}

@media (max-width: 575.98px) {
    .vaccine-page .vaccine-hero {
        min-height: 176px;
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
    }

    .vaccine-page .vaccine-hero__left {
        gap: .8rem;
    }

    .vaccine-page .vaccine-hero__icon {
        width: 52px;
        height: 52px;
        border-radius: 15px;
    }

    .vaccine-page .vaccine-hero__icon i {
        font-size: 1.25rem;
    }

    .vaccine-page .vaccine-hero__title {
        font-size: 1.05rem;
    }

    .vaccine-page .vaccine-hero__client {
        font-size: .78rem;
    }

    .vaccine-page .vaccine-hero__actions {
        flex-direction: column;
    }

    .vaccine-page .vaccine-hero__actions > * {
        width: 100%;
        flex: 1 1 auto;
    }
}
</style>
@endpush