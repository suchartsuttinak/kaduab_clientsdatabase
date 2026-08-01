<div class="vaccine-hero card border-0 shadow-sm mb-3 overflow-hidden">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div class="d-flex align-items-start gap-3">
                <div class="vaccine-hero__icon">
                    <i class="bi bi-capsule-pill"></i>
                </div>
                <div>
                    <h4 class="mb-1 fw-bold">ประวัติการให้วัคซีน</h4>
                    <div class="text-muted small">
                        จัดเก็บข้อมูลการรับวัคซีนอย่างเป็นระบบ เพื่อติดตามสุขภาพและประวัติการรักษาได้ถูกต้อง
                    </div>
                </div>
            </div>
         @if(isset($vaccinations) && $vaccinations->isNotEmpty())
                <div class="vaccine-toolbar-scroll">
                    <div class="vaccine-toolbar-actions">
                        <button type="button"
                                class="btn btn-primary vaccine-btn vaccine-btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#add-vaccine-modal">
                            <i class="bi bi-plus-circle"></i>
                            <span>เพิ่มข้อมูลวัคซีน</span>
                        </button>
                    </div>
                </div>
            @endif
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
    --vaccine-border: #e2e8f0;
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
    background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%);
    border: 1px solid #e8f0ff;
    box-shadow: var(--vaccine-shadow-sm);
}

.vaccine-page .vaccine-hero__icon {
    width: 58px;
    height: 58px;
    border-radius: 16px;
    background: #dbeafe;
    color: var(--vaccine-primary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
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

.vaccine-page .vaccine-btn {
    min-height: 42px;
    border-radius: 12px;
    padding: .625rem 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    white-space: nowrap;
    font-weight: 600;
    flex: 0 0 auto;
}

.vaccine-page .vaccine-btn-primary {
    min-width: 160px;
}

@media (max-width: 991.98px) {
    .vaccine-page .vaccine-toolbar-actions {
        justify-content: flex-start;
    }
}

@media (max-width: 767.98px) {
    .vaccine-page .vaccine-hero {
        border-radius: var(--vaccine-radius-md);
    }
}
</style>
@endpush