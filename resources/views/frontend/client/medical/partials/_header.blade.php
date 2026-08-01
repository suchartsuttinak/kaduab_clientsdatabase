<div class="medical-hero card border-0 shadow-sm mb-3 overflow-hidden">
    <div class="card-body medical-hero__body">
        <div class="medical-hero__layout">
            <div class="medical-hero__content">
                <div class="medical-hero__icon-wrap">
                    <div class="medical-hero__icon">
                        <i class="bi bi-hospital"></i>
                    </div>
                </div>

                <div class="medical-hero__text">
                    <h3 class="medical-hero__title mb-1">บันทึกข้อมูลการรักษาพยาบาลเด็ก</h3>
                    <p class="medical-hero__desc mb-0">
                        จัดเก็บข้อมูลสุขภาพ การรักษา การวินิจฉัย และการติดตามผลอย่างเป็นระบบ
                    </p>
                </div>
            </div>

            @if($hasMedicalRecords ?? $medicals->isNotEmpty())
            <div class="medical-hero__actions">
                <button type="button"
                        class="btn btn-primary medical-btn medical-btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#add-medical-modal">
                    <i class="bi bi-plus-circle"></i>
                    <span>เพิ่มข้อมูลการรักษา</span>
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.medical-page {
    --medical-primary: #2563eb;
    --medical-primary-dark: #1d4ed8;
    --medical-success: #16a34a;
    --medical-warning: #f59e0b;
    --medical-danger: #dc2626;
    --medical-text: #0f172a;
    --medical-text-soft: #64748b;
    --medical-border: #e2e8f0;
    --medical-border-soft: #eef2f7;
    --medical-bg-soft: #f8fafc;
    --medical-bg-soft-2: #f1f5f9;
    --medical-shadow-sm: 0 8px 20px rgba(15, 23, 42, 0.05);
    --medical-radius: 18px;
    --medical-radius-md: 14px;
}

.medical-page .medical-hero,
.medical-page .medical-client-summary-card,
.medical-page .medical-table-card {
    border-radius: var(--medical-radius);
}

.medical-page .medical-hero {
    background:
        radial-gradient(circle at top left, rgba(59, 130, 246, 0.08), transparent 32%),
        linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%);
    border: 1px solid #e8f0ff;
    box-shadow: var(--medical-shadow-sm);
}

.medical-page .medical-hero__body {
    padding: 1.25rem 1.5rem;
}

.medical-page .medical-hero__layout {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    min-width: 0;
}

.medical-page .medical-hero__content {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 0;
    flex: 1 1 auto;
}

.medical-page .medical-hero__icon-wrap {
    flex: 0 0 auto;
}

.medical-page .medical-hero__icon {
    width: 64px;
    height: 64px;
    border-radius: 18px;
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: var(--medical-primary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.55rem;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.55);
}

.medical-page .medical-hero__text {
    min-width: 0;
    max-width: 760px;
}

.medical-page .medical-hero__title {
    font-size: 1.5rem;
    line-height: 1.2;
    font-weight: 800;
    color: #334e68;
    letter-spacing: -0.01em;
    margin: 0;
    word-break: keep-all;
}

.medical-page .medical-hero__desc {
    font-size: .95rem;
    line-height: 1.6;
    color: var(--medical-text-soft);
    max-width: 620px;
}

.medical-page .medical-hero__actions {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

.medical-page .medical-btn {
    min-height: 44px;
    border-radius: 12px;
    padding: .7rem 1.05rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    white-space: nowrap;
    font-weight: 700;
    font-size: .92rem;
    flex: 0 0 auto;
    box-shadow: 0 10px 22px rgba(37, 99, 235, 0.16);
}

.medical-page .medical-btn-primary {
    min-width: 178px;
}

@media (max-width: 1199.98px) {
    .medical-page .medical-hero__title {
        font-size: 1.35rem;
    }

    .medical-page .medical-hero__desc {
        max-width: 560px;
    }
}

@media (max-width: 991.98px) {
    .medical-page .medical-hero__body {
        padding: 1.15rem 1.15rem;
    }

    .medical-page .medical-hero__layout {
        flex-direction: column;
        align-items: stretch;
    }

    .medical-page .medical-hero__actions {
        justify-content: flex-start;
    }

    .medical-page .medical-hero__title {
        font-size: 1.28rem;
    }

    .medical-page .medical-btn-primary {
        min-width: 170px;
    }
}

@media (max-width: 767.98px) {
    .medical-page .medical-hero {
        border-radius: var(--medical-radius-md);
    }

    .medical-page .medical-hero__body {
        padding: 1rem;
    }

    .medical-page .medical-hero__content {
        align-items: flex-start;
        gap: .85rem;
    }

    .medical-page .medical-hero__icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        font-size: 1.35rem;
    }

    .medical-page .medical-hero__title {
        font-size: 1.12rem;
        line-height: 1.3;
    }

    .medical-page .medical-hero__desc {
        font-size: .875rem;
        line-height: 1.55;
    }
}

@media (max-width: 575.98px) {
    .medical-page .medical-hero__layout {
        gap: 1rem;
    }

    .medical-page .medical-hero__content {
        align-items: flex-start;
    }

    .medical-page .medical-hero__text {
        width: 100%;
    }

    .medical-page .medical-hero__title {
        font-size: 1.02rem;
        line-height: 1.35;
    }

    .medical-page .medical-hero__desc {
        font-size: .82rem;
    }

    .medical-page .medical-hero__actions {
        width: 100%;
    }

    .medical-page .medical-btn {
        width: 100%;
        min-width: 0;
    }
}
</style>
@endpush