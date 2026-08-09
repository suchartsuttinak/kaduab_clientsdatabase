<style>
    .csl-page {
        --csl-primary: #2563eb;
        --csl-primary-soft: #eff6ff;
        --csl-text: #1e293b;
        --csl-muted: #64748b;
        --csl-border: #e2e8f0;
        --csl-soft: #f8fafc;
        --csl-success: #047857;
        --csl-danger: #dc2626;
        width: 100%;
        min-width: 0;
        padding: .9rem .2rem 1.5rem;
    }

    .csl-page *,
    .csl-page *::before,
    .csl-page *::after { box-sizing: border-box; }

    .csl-header,
    .csl-card,
    .csl-form-card,
    .csl-round-card {
        background: #fff;
        border: 1px solid var(--csl-border);
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
    }

    .csl-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        min-height: 82px;
        margin-bottom: 1rem;
        padding: 1rem 1.15rem;
    }

    .csl-header-main {
        display: flex;
        align-items: center;
        gap: .85rem;
        min-width: 0;
    }

    .csl-header-icon {
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        color: var(--csl-primary);
        background: var(--csl-primary-soft);
        font-size: 1.3rem;
    }

    .csl-title {
        margin: 0;
        color: var(--csl-text);
        font-size: 1.16rem;
        font-weight: 700;
        line-height: 1.35;
    }

    .csl-subtitle {
        display: flex;
        flex-wrap: wrap;
        gap: .2rem .35rem;
        margin-top: .28rem;
        color: var(--csl-muted);
        font-size: .84rem;
        line-height: 1.5;
    }

    .csl-subtitle strong { color: #334155; font-weight: 600; }
    .csl-dot { color: #94a3b8; }

    .csl-header-actions,
    .csl-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: .45rem;
    }

    .csl-btn-primary,
    .csl-btn-secondary,
    .csl-btn-success,
    .csl-btn-outline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .42rem;
        min-height: 40px;
        padding: .55rem .9rem;
        border-radius: 11px;
        font-size: .82rem;
        font-weight: 600;
        text-decoration: none !important;
        white-space: nowrap;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .csl-btn-primary {
        color: #fff !important;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        border: 1px solid #2563eb;
        box-shadow: 0 5px 14px rgba(37, 99, 235, .2);
    }

    .csl-btn-success {
        color: #fff !important;
        background: linear-gradient(135deg, #059669, #047857);
        border: 1px solid #059669;
        box-shadow: 0 5px 14px rgba(5, 150, 105, .18);
    }

    .csl-btn-secondary,
    .csl-btn-outline {
        color: #475569 !important;
        background: #fff;
        border: 1px solid #dbe3ec;
    }

    .csl-btn-primary:hover,
    .csl-btn-success:hover,
    .csl-btn-secondary:hover,
    .csl-btn-outline:hover { transform: translateY(-1px); }

    .csl-card { overflow: hidden; }

    .csl-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.15rem;
        border-bottom: 1px solid #edf2f7;
    }

    .csl-card-title {
        display: flex;
        align-items: center;
        gap: .45rem;
        color: var(--csl-text);
        font-size: .98rem;
        font-weight: 700;
    }

    .csl-card-title i { color: var(--csl-primary); }
    .csl-card-note { margin-top: .2rem; color: var(--csl-muted); font-size: .79rem; }
    .csl-card-body { padding: 1rem; }

    .csl-count,
    .csl-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .3rem;
        padding: .32rem .62rem;
        border-radius: 999px;
        font-size: .74rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .csl-count { color: #475569; background: #f8fafc; border: 1px solid #e2e8f0; }
    .csl-status--open { color: #1d4ed8; background: #eff6ff; }
    .csl-status--track { color: #7c3aed; background: #f5f3ff; }
    .csl-status--goal { color: #047857; background: #ecfdf5; }
    .csl-status--refer { color: #b45309; background: #fffbeb; }
    .csl-status--closed { color: #475569; background: #f1f5f9; }

    .csl-empty {
        min-height: 270px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        text-align: center;
    }

    .csl-empty-icon {
        width: 68px;
        height: 68px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: .9rem;
        border-radius: 50%;
        color: var(--csl-primary);
        background: var(--csl-primary-soft);
        font-size: 1.7rem;
    }

    .csl-empty-title { color: #334155; font-size: 1rem; font-weight: 700; }
    .csl-empty-text { max-width: 560px; margin: .35rem auto 1rem; color: var(--csl-muted); font-size: .84rem; line-height: 1.65; }

    .csl-table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .csl-table { width: 100%; min-width: 1080px; border-collapse: separate; border-spacing: 0; }
    .csl-table thead th { padding: .76rem .7rem; color: #475569; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: .78rem; font-weight: 700; white-space: nowrap; vertical-align: middle; }
    .csl-table tbody td { padding: .8rem .7rem; color: #334155; border-bottom: 1px solid #edf2f7; font-size: .81rem; vertical-align: middle; }
    .csl-table tbody tr:last-child td { border-bottom: 0; }
    .csl-table tbody tr:hover { background: #fafcff; }
    .csl-clamp-2 { display: -webkit-box; overflow: hidden; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-height: 1.45; }

    .csl-icon-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 9px;
        border: 1px solid transparent;
        background: #fff;
        text-decoration: none !important;
    }
    .csl-icon-view { color: #0f766e; background: #f0fdfa; border-color: #ccfbf1; }
    .csl-icon-edit { color: #2563eb; background: #eff6ff; border-color: #dbeafe; }
    .csl-icon-print { color: #475569; background: #f8fafc; border-color: #e2e8f0; }
    .csl-icon-delete { color: #dc2626; background: #fef2f2; border-color: #fee2e2; }

    .csl-form-card { overflow: hidden; }
    .csl-form-header { padding: 1rem 1.15rem; border-bottom: 1px solid #e2e8f0; background: #fbfdff; }
    .csl-form-body { padding: 1rem 1.15rem; }
    .csl-form-footer { display: flex; justify-content: flex-end; gap: .5rem; padding: .9rem 1.15rem; border-top: 1px solid #e2e8f0; background: #fff; }

    .csl-section { padding: .95rem; margin-bottom: .9rem; border: 1px solid #e7edf4; border-radius: 14px; background: #fff; }
    .csl-section:last-child { margin-bottom: 0; }
    .csl-section-title { display: flex; align-items: center; gap: .42rem; margin-bottom: .75rem; color: #334155; font-size: .88rem; font-weight: 700; }
    .csl-section-title i { color: var(--csl-primary); }

    .csl-page .form-label { margin-bottom: .35rem; color: #334155; font-size: .8rem; font-weight: 600; }
    .csl-page .form-control,
    .csl-page .form-select { min-height: 39px; border-color: #dbe3ec; border-radius: 10px; font-size: .83rem; }
    .csl-page textarea.form-control { min-height: 88px; resize: vertical; }
    .csl-readonly { background: #f8fafc !important; }
    .csl-required { color: #dc2626; }
    .csl-help { margin-top: .28rem; color: #64748b; font-size: .73rem; line-height: 1.45; }

    .csl-previous {
        margin-bottom: 1rem;
        padding: .9rem 1rem;
        border: 1px solid #dbeafe;
        border-radius: 14px;
        background: linear-gradient(180deg, #f8fbff, #fff);
    }
    .csl-previous-head { display: flex; justify-content: space-between; align-items: center; gap: .75rem; margin-bottom: .55rem; }
    .csl-previous-title { color: #1e3a8a; font-size: .86rem; font-weight: 700; }
    .csl-previous-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .65rem; }
    .csl-previous-item { padding: .65rem .7rem; border: 1px solid #e5edf9; border-radius: 10px; background: #fff; }
    .csl-previous-label { margin-bottom: .2rem; color: #64748b; font-size: .7rem; font-weight: 600; }
    .csl-previous-value { color: #334155; font-size: .79rem; line-height: 1.5; }

    .csl-summary-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .7rem; margin-bottom: 1rem; }
    .csl-summary-item { padding: .75rem .8rem; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; }
    .csl-summary-label { color: #64748b; font-size: .71rem; font-weight: 600; }
    .csl-summary-value { margin-top: .18rem; color: #1e293b; font-size: .86rem; font-weight: 600; line-height: 1.4; }

    .csl-round-list { display: grid; gap: .8rem; }
    .csl-round-card { overflow: hidden; }
    .csl-round-head { display: flex; justify-content: space-between; gap: 1rem; padding: .85rem 1rem; border-bottom: 1px solid #edf2f7; background: #fbfdff; }
    .csl-round-title { color: #1e293b; font-size: .91rem; font-weight: 700; }
    .csl-round-meta { margin-top: .18rem; color: #64748b; font-size: .74rem; }
    .csl-round-body { display: grid; grid-template-columns: 1.15fr 1fr 1fr; gap: .7rem; padding: .9rem 1rem; }
    .csl-round-field { min-width: 0; }
    .csl-round-label { margin-bottom: .2rem; color: #64748b; font-size: .7rem; font-weight: 600; }
    .csl-round-value { color: #334155; font-size: .8rem; line-height: 1.5; }

    .csl-modal .modal-content { border: 0; border-radius: 18px; box-shadow: 0 20px 55px rgba(15,23,42,.18); overflow: hidden; }
    .csl-modal .modal-header { padding: .95rem 1.15rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
    .csl-modal .modal-body { padding: 1rem 1.15rem; max-height: calc(100vh - 180px); overflow-y: auto; }
    .csl-modal .modal-footer { padding: .85rem 1.15rem; border-top: 1px solid #e2e8f0; }

    @media (max-width: 991.98px) {
        .csl-summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .csl-round-body { grid-template-columns: 1fr 1fr; }
        .csl-previous-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 767.98px) {
        .csl-page { padding: .7rem 0 1.2rem; }
        .csl-header { align-items: flex-start; flex-wrap: wrap; padding: .9rem; border-radius: 16px; }
        .csl-header-icon { width: 44px; height: 44px; flex-basis: 44px; }
        .csl-title { font-size: 1.04rem; }
        .csl-subtitle { font-size: .79rem; }
        .csl-header-actions { width: 100%; }
        .csl-header-actions > * { flex: 1 1 auto; }
        .csl-card, .csl-form-card, .csl-round-card { border-radius: 16px; }
        .csl-card-header { align-items: flex-start; padding: .9rem; }
        .csl-card-body { padding: .75rem; }
        .csl-summary-grid { grid-template-columns: 1fr; }
        .csl-round-body { grid-template-columns: 1fr; }
        .csl-form-body { padding: .8rem; }
        .csl-section { padding: .8rem; }
        .csl-form-footer { flex-wrap: wrap; }
        .csl-form-footer > * { flex: 1 1 auto; }
        .csl-count { display: none; }
    }
</style>
