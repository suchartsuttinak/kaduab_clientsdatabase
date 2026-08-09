<style>
    .csr-page {
        max-width: 960px;
        margin: 0 auto;
        padding: 1rem 0 2rem;
        color: #1f2937;
        font-family: "Sarabun", "TH Sarabun New", Tahoma, Arial, sans-serif;
    }

    .csr-toolbar {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: .5rem;
        margin-bottom: .8rem;
    }

    .csr-sheet {
        background: #fff;
        border: 1px solid #dfe5ec;
        border-radius: 14px;
        padding: 1.25rem 1.45rem;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
    }

    .csr-head {
        text-align: center;
        margin-bottom: .85rem;
        padding-bottom: .65rem;
        border-bottom: 2px solid #334155;
    }

    .csr-head h1 {
        margin: 0;
        color: #111827;
        font-size: 1.28rem;
        font-weight: 700;
        line-height: 1.35;
    }

    .csr-head h2 {
        margin: .15rem 0 0;
        color: #334155;
        font-size: 1rem;
        font-weight: 600;
    }

    .csr-head p {
        margin: .25rem 0 0;
        color: #64748b;
        font-size: .82rem;
    }

    .csr-meta {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: .75rem;
    }

    .csr-meta td {
        width: 50%;
        padding: .2rem .38rem;
        vertical-align: top;
        font-size: .84rem;
    }

    .csr-label { font-weight: 700; color: #111827; }

    .csr-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .5rem;
        margin: .7rem 0 .85rem;
    }

    .csr-summary-item {
        padding: .55rem .65rem;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        background: #fbfdff;
    }

    .csr-summary-label { color: #64748b; font-size: .7rem; font-weight: 600; }
    .csr-summary-value { margin-top: .15rem; color: #1e293b; font-size: .82rem; font-weight: 600; line-height: 1.4; }

    .csr-section {
        margin-top: .8rem;
    }

    .csr-section-title {
        margin: 0 0 .35rem;
        padding-bottom: .25rem;
        border-bottom: 1px solid #d7dee8;
        color: #111827;
        font-size: .94rem;
        font-weight: 700;
    }

    .csr-field {
        margin-bottom: .42rem;
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .csr-field-title {
        margin-bottom: .08rem;
        color: #111827;
        font-size: .82rem;
        font-weight: 700;
    }

    .csr-field-value {
        color: #334155;
        font-size: .83rem;
        line-height: 1.5;
        white-space: pre-line;
    }

    .csr-round {
        margin-top: .85rem;
        border: 1px solid #dce3ec;
        border-radius: 10px;
        overflow: hidden;
    }

    .csr-round-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: .75rem;
        padding: .6rem .72rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        break-after: avoid;
        page-break-after: avoid;
    }

    .csr-round-title { color: #111827; font-size: .9rem; font-weight: 700; }
    .csr-round-meta { margin-top: .12rem; color: #64748b; font-size: .72rem; }
    .csr-round-status { padding: .2rem .45rem; border: 1px solid #cbd5e1; border-radius: 999px; color: #475569; background: #fff; font-size: .68rem; font-weight: 600; white-space: nowrap; }
    .csr-round-body { padding: .7rem .78rem; }

    .csr-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .5rem .9rem;
    }

    .csr-index-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: .35rem;
    }

    .csr-index-table th,
    .csr-index-table td {
        border: 1px solid #d7dee8;
        padding: .38rem .45rem;
        vertical-align: top;
        font-size: .76rem;
        line-height: 1.4;
    }

    .csr-index-table th {
        background: #f8fafc;
        color: #334155;
        font-weight: 700;
        text-align: center;
        white-space: nowrap;
    }

    .csr-next {
        margin-top: .75rem;
        padding: .65rem .75rem;
        border: 1px solid #bfdbfe;
        border-radius: 9px;
        background: #f8fbff;
    }

    .csr-signature {
        margin-top: 1.3rem;
        display: flex;
        justify-content: flex-end;
    }

    .csr-signature-box {
        width: 280px;
        text-align: center;
        font-size: .8rem;
        line-height: 1.7;
    }

    @media (max-width: 767.98px) {
        .csr-sheet { padding: .9rem; border-radius: 10px; }
        .csr-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .csr-grid-2 { grid-template-columns: 1fr; }
        .csr-meta td { display: block; width: 100%; }
    }

    @media print {
        @page { size: A4 portrait; margin: 10mm 11mm 11mm; }

        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            color: #000 !important;
            font-family: "Sarabun", "TH Sarabun New", Tahoma, Arial, sans-serif !important;
            font-size: 11.5pt !important;
            line-height: 1.35 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .app-topbar,
        .app-sidebar-menu,
        .sidebar-overlay,
        .app-footer,
        footer,
        .csr-toolbar { display: none !important; }

        .app-body,
        .content-page,
        .main-content,
        .content-shell,
        .content-scroll-x {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            height: auto !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
            transform: none !important;
        }

        .csr-page { width: 100% !important; max-width: none !important; margin: 0 !important; padding: 0 !important; }
        .csr-sheet { border: 0 !important; border-radius: 0 !important; box-shadow: none !important; padding: 0 !important; }
        .csr-head { margin-bottom: 5px; padding-bottom: 4px; }
        .csr-head h1 { font-size: 17pt; }
        .csr-head h2 { font-size: 13pt; }
        .csr-head p { font-size: 9.5pt; }
        .csr-meta { margin-bottom: 4px; }
        .csr-meta td { padding: 1px 4px; font-size: 10pt; }
        .csr-summary { gap: 4px; margin: 5px 0 6px; }
        .csr-summary-item { padding: 4px 5px; }
        .csr-summary-label { font-size: 8.5pt; }
        .csr-summary-value { font-size: 9.5pt; }
        .csr-section { margin-top: 6px; }
        .csr-section-title { margin-bottom: 2px; padding-bottom: 2px; font-size: 11pt; }
        .csr-field { margin-bottom: 3px; }
        .csr-field-title { font-size: 9.5pt; }
        .csr-field-value { font-size: 9.5pt; line-height: 1.35; }
        .csr-index-table th,
        .csr-index-table td { padding: 3px 4px; font-size: 8.8pt; }
        .csr-round { margin-top: 6px; border-radius: 0; }
        .csr-round-head { padding: 4px 5px; }
        .csr-round-title { font-size: 10.5pt; }
        .csr-round-meta { font-size: 8.5pt; }
        .csr-round-status { font-size: 8.5pt; }
        .csr-round-body { padding: 5px 6px; }
        .csr-next { margin-top: 5px; padding: 5px 6px; }
        .csr-signature { margin-top: 16px; }
        .csr-signature-box { font-size: 9.5pt; }
    }
</style>
