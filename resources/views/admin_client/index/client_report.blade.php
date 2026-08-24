@extends('admin_client.admin_client')
@section('content')


   <style>
    @page {
        size: A4 portrait;
        margin: 12mm;
    }

    .welfare-report-page {
        --wr-ink: #172033;
        --wr-muted: #64748b;
        --wr-line: #cbd5e1;
        --wr-line-dark: #64748b;
        --wr-surface: #ffffff;
        --wr-soft: #f6f8fb;
        --wr-accent: #243b5a;
        --wr-accent-dark: #172a43;
        --wr-radius: 14px;
        --wr-font: var(--bs-body-font-family, inherit);

        min-height: 100%;
        padding: 24px 16px 40px;
        background: #eef2f6;
        color: var(--wr-ink);
        font-family: var(--wr-font);
        font-size: 14px;
        line-height: 1.55;
        box-sizing: border-box;
    }

    .welfare-report-page *,
    .welfare-report-page *::before,
    .welfare-report-page *::after {
        box-sizing: border-box;
    }

    /* ------------------------------------------------------------------
     | แถบคำสั่ง
     * ------------------------------------------------------------------ */
    .welfare-report-page .report-actions {
        width: min(210mm, 100%);
        margin: 0 auto 12px;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border: 1px solid #dbe3ec;
        border-radius: 12px;
        background: rgba(255, 255, 255, .96);
        box-shadow: 0 6px 18px rgba(15, 23, 42, .06);
    }

    .welfare-report-page .report-actions-note {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 8px;
        color: var(--wr-muted);
        font-family: var(--wr-font);
        font-size: 12px;
        line-height: 1.45;
    }

    .welfare-report-page .report-actions-note i {
        flex: 0 0 auto;
        color: var(--wr-accent);
        font-size: 15px;
    }

    .welfare-report-page .report-actions-buttons {
        display: flex;
        flex: 0 0 auto;
        align-items: center;
        gap: 8px;
    }

    .welfare-report-page .report-action-btn {
        display: inline-flex;
        min-height: 40px;
        padding: 8px 14px;
        border: 1px solid transparent;
        border-radius: 8px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        font-family: var(--wr-font);
        font-size: 13px;
        font-weight: 600;
        line-height: 1.2;
        text-decoration: none;
        cursor: pointer;
        box-shadow: none;
        transition: background-color .18s ease,
                    border-color .18s ease,
                    color .18s ease,
                    transform .18s ease;
    }

    .welfare-report-page .report-action-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .welfare-report-page .report-action-btn:focus-visible {
        outline: 3px solid rgba(36, 59, 90, .18);
        outline-offset: 2px;
    }

    .welfare-report-page .report-action-back {
        border-color: #cbd5e1;
        background: #ffffff;
        color: #334155;
    }

    .welfare-report-page .report-action-back:hover {
        border-color: #94a3b8;
        background: #f8fafc;
        color: #0f172a;
    }

    .welfare-report-page .report-action-print {
        border-color: var(--wr-accent);
        background: var(--wr-accent);
        color: #ffffff;
    }

    .welfare-report-page .report-action-print:hover {
        border-color: var(--wr-accent-dark);
        background: var(--wr-accent-dark);
        color: #ffffff;
    }

    /* ------------------------------------------------------------------
     | กระดาษรายงาน
     * ------------------------------------------------------------------ */
    .welfare-report-page .report-wrap {
        width: min(210mm, 100%);
        margin: 0 auto;
        padding: 12mm 12mm 15mm;
        border: 1px solid #d7dee8;
        border-radius: var(--wr-radius);
        background: var(--wr-surface);
        box-shadow: 0 14px 36px rgba(15, 23, 42, .09);
    }

    .welfare-report-page .report-header {
        display: grid;
        grid-template-columns: 28mm minmax(0, 1fr) 28mm;
        align-items: start;
        gap: 7mm;
        min-height: 27mm;
        margin-bottom: 4mm;
        padding: 3mm 0 3mm;
        border-bottom: 1.5px solid var(--wr-line-dark);
    }

    .welfare-report-page .report-header-spacer {
        width: 28mm;
        min-height: 1px;
    }

    .welfare-report-page .report-header-copy {
        min-width: 0;
        padding-top: 2.5mm;
        text-align: center;
    }

    .welfare-report-page .report-title {
        margin: 0;
        color: #111827;
        font-size: 22px;
        font-weight: 700;
        line-height: 1.25;
        letter-spacing: .1px;
    }

    .welfare-report-page .report-title-rule {
        width: 24mm;
        height: 1.5px;
        margin: 3mm auto 0;
        background: var(--wr-accent);
    }

    .welfare-report-page .photo-box {
        width: 23mm;
        height: 27mm;
        margin-left: auto;
        overflow: hidden;
        border: 1px solid #64748b;
        border-radius: 2px;
        background: #ffffff;
    }

    .welfare-report-page .photo-box img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* ------------------------------------------------------------------
     | ฟิลด์ข้อมูล
     * ------------------------------------------------------------------ */
    .welfare-report-page .form-row {
        display: flex;
        align-items: flex-start;
        margin-bottom: 2.7mm;
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .welfare-report-page .row-no {
        width: 9mm;
        flex: 0 0 9mm;
        padding-top: .2mm;
        color: #111827;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.35;
    }

    .welfare-report-page .row-body {
        flex: 1 1 auto;
        min-width: 0;
    }

    .welfare-report-page .line {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 1.7mm 4.5mm;
    }

    .welfare-report-page .sub-line {
        margin-top: 1.8mm;
    }

    .welfare-report-page .sub-line + .sub-line {
        margin-top: 1.3mm;
    }

    .welfare-report-page .field {
        display: inline-flex;
        min-width: 0;
        min-height: 6.5mm;
        align-items: flex-end;
        white-space: nowrap;
    }

    .welfare-report-page .label {
        flex: 0 0 auto;
        margin-right: 1.2mm;
        color: #111827;
        font-weight: 600;
    }

    .welfare-report-page .value {
        display: inline-block;
        min-height: 5.5mm;
        padding: 0 1.2mm .4mm;
        border-bottom: .35mm solid #94a3b8;
        color: #111827;
        line-height: 1.15;
        text-align: center;
        vertical-align: bottom;
    }

    .welfare-report-page .value.left {
        text-align: left;
    }

    .welfare-report-page .field.wrap {
        align-items: flex-start;
        white-space: normal;
    }

    .welfare-report-page .field.wrap .label {
        flex-shrink: 0;
        margin-top: .4mm;
        white-space: nowrap;
    }

    .welfare-report-page .field.wrap .value {
        min-width: 0;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
        line-height: 1.35;
    }

    .welfare-report-page .full-row-field {
        display: flex;
        width: 100%;
        min-width: 0;
        align-items: flex-start;
        gap: 2mm;
    }

    .welfare-report-page .full-row-field .label {
        flex: 0 0 auto;
        margin: 0;
        white-space: nowrap;
    }

    .welfare-report-page .full-row-field .value {
        flex: 1 1 auto;
        min-width: 0;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
        text-align: left;
        line-height: 1.35;
    }

    /* ความกว้างฟิลด์ ใช้หน่วยมิลลิเมตรเพื่อให้หน้าจอและงานพิมพ์ตรงกัน */
    .welfare-report-page .w-30  { min-width: 6mm; }
    .welfare-report-page .w-35  { min-width: 7mm; }
    .welfare-report-page .w-40  { min-width: 8mm; }
    .welfare-report-page .w-45  { min-width: 9mm; }
    .welfare-report-page .w-50  { min-width: 10mm; }
    .welfare-report-page .w-55  { min-width: 11mm; }
    .welfare-report-page .w-60  { min-width: 12mm; }
    .welfare-report-page .w-70  { min-width: 14mm; }
    .welfare-report-page .w-80  { min-width: 16mm; }
    .welfare-report-page .w-90  { min-width: 18mm; }
    .welfare-report-page .w-100 { min-width: 20mm; }
    .welfare-report-page .w-110 { min-width: 22mm; }
    .welfare-report-page .w-120 { min-width: 24mm; }
    .welfare-report-page .w-130 { min-width: 26mm; }
    .welfare-report-page .w-140 { min-width: 28mm; }
    .welfare-report-page .w-150 { min-width: 30mm; }
    .welfare-report-page .w-160 { min-width: 32mm; }
    .welfare-report-page .w-180 { min-width: 36mm; }
    .welfare-report-page .w-200 { min-width: 40mm; }
    .welfare-report-page .w-220 { min-width: 44mm; }
    .welfare-report-page .w-240 { min-width: 48mm; }
    .welfare-report-page .w-260 { min-width: 52mm; }
    .welfare-report-page .w-320 { min-width: 64mm; }
    .welfare-report-page .w-420 { min-width: 84mm; }
    .welfare-report-page .w-520 { min-width: 104mm; }
    .welfare-report-page .w-full { width: 100%; min-height: 6.5mm; }

    /* ------------------------------------------------------------------
     | หัวข้อหมวด
     * ------------------------------------------------------------------ */
    .welfare-report-page .report-block-title,
    .welfare-report-page .problem-title,
    .welfare-report-page .member-table-title,
    .welfare-report-page .factfinding-subtitle {
        break-after: avoid;
        page-break-after: avoid;
        color: #111827;
        font-weight: 700;
    }

    .welfare-report-page .report-block-title,
    .welfare-report-page .problem-title,
    .welfare-report-page .member-table-title {
        margin: 5.5mm 0 3mm;
        padding: 1.8mm 2.5mm;
        border-left: 1.1mm solid var(--wr-accent);
        border-bottom: 1px solid #d8dee8;
        background: var(--wr-soft);
        font-size: 16px;
        line-height: 1.2;
    }

    .welfare-report-page .factfinding-section {
        margin-top: 7mm;
        padding-top: 5mm;
        border-top: .7mm solid #64748b;
    }

    .welfare-report-page .factfinding-subtitle {
        margin: 5mm 0 2.5mm;
        padding-bottom: 1.3mm;
        border-bottom: 1px solid #94a3b8;
        font-size: 15px;
        line-height: 1.25;
    }

    /* ------------------------------------------------------------------
     | สภาพปัญหาและเอกสาร
     * ------------------------------------------------------------------ */
    .welfare-report-page .problem-list,
    .welfare-report-page .doc-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.5mm 6mm;
        padding: 0 2mm;
    }

    .welfare-report-page .problem-item,
    .welfare-report-page .doc-item {
        min-width: 0;
        font-size: 13px;
        line-height: 1.35;
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .welfare-report-page .checkbox {
        margin-right: 1.5mm;
        color: #111827;
        font-weight: 700;
    }

    .welfare-report-page .muted {
        color: var(--wr-muted);
    }

    /* ------------------------------------------------------------------
     | ตารางสมาชิกครอบครัว
     * ------------------------------------------------------------------ */
    .welfare-report-page .member-table-wrap {
        margin-top: 6mm;
    }

    .welfare-report-page .member-table {
        width: 100%;
        margin-top: 2mm;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .welfare-report-page .member-table thead {
        display: table-header-group;
    }

    .welfare-report-page .member-table tr {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .welfare-report-page .member-table th,
    .welfare-report-page .member-table td {
        padding: 1.6mm 1.8mm;
        border: .35mm solid #64748b;
        vertical-align: top;
        color: #111827;
        font-size: 12px;
        line-height: 1.28;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .welfare-report-page .member-table th {
        background: #edf1f5;
        font-weight: 700;
        text-align: center;
    }

    .welfare-report-page .center {
        text-align: center;
    }

    /* ------------------------------------------------------------------
     | ตารางข้อ 18 การติดตามผลการช่วยเหลือ
     * ------------------------------------------------------------------ */
    .welfare-report-page .followup-table-wrap {
        width: 100%;
        margin-top: 2mm;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .welfare-report-page .followup-report-table {
        width: 100%;
        min-width: 0;
        margin-top: 0;
        table-layout: fixed;
    }

    .welfare-report-page .followup-report-table col.followup-col-date {
        width: 28mm;
    }

    .welfare-report-page .followup-report-table col.followup-col-detail {
        width: auto;
    }

    .welfare-report-page .followup-report-table col.followup-col-note {
        width: 34mm;
    }

    .welfare-report-page .followup-report-table th,
    .welfare-report-page .followup-report-table td {
        vertical-align: top;
    }

    .welfare-report-page .followup-report-table td:first-child {
        white-space: nowrap;
        text-align: center;
    }

    .welfare-report-page .followup-report-table .followup-cell-text {
        white-space: pre-line;
        overflow-wrap: anywhere;
        word-break: break-word;
        line-height: 1.42;
    }

    .welfare-report-page .followup-report-table .followup-empty-row {
        padding: 4mm 2mm;
        color: var(--wr-muted);
        text-align: center;
    }

    /* ------------------------------------------------------------------
     | ข้อมูลข้อเท็จจริงและข้อความยาว
     * ------------------------------------------------------------------ */
    .welfare-report-page .factfinding-box {
        margin-top: 2.5mm;
        padding: 2.5mm 3mm;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        background: #fbfcfd;
    }

    .welfare-report-page .factfinding-box + .factfinding-box {
        margin-top: 2.5mm;
    }

    .welfare-report-page .factfinding-text {
        min-height: 13mm;
        margin-top: 1.5mm;
        padding: 2mm 2.5mm;
        border: 1px solid #d7dde5;
        border-radius: 2px;
        background: #ffffff;
        line-height: 1.42;
        white-space: pre-line;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .welfare-report-page .paired-wrap-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 4mm;
        align-items: start;
    }

    .welfare-report-page .paired-wrap-row .field.wrap {
        width: 100%;
    }

    .welfare-report-page .paired-wrap-row .field.wrap .value {
        width: auto;
        flex: 1 1 auto;
    }

    .welfare-report-page .subitem-group {
        margin-top: 1.5mm;
        padding-left: 9mm;
    }

    .welfare-report-page .subitem-row {
        display: grid;
        grid-template-columns: 12mm minmax(0, 1fr);
        gap: 2mm;
        margin-bottom: 2.5mm;
        align-items: start;
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .welfare-report-page .subitem-no {
        color: #111827;
        font-weight: 700;
        line-height: 1.4;
    }

    .welfare-report-page .subitem-body {
        min-width: 0;
    }

    /* ------------------------------------------------------------------
     | Responsive
     * ------------------------------------------------------------------ */
    @media (max-width: 900px) {
        .welfare-report-page {
            padding: 14px 8px 28px;
        }

        .welfare-report-page .report-wrap {
            padding: 22px 18px 26px;
        }

        .welfare-report-page .report-actions-note {
            display: none;
        }

        .welfare-report-page .report-actions {
            justify-content: flex-end;
        }
    }

    @media (max-width: 680px) {
        .welfare-report-page {
            font-size: 13px;
        }

        .welfare-report-page .report-actions,
        .welfare-report-page .report-actions-buttons {
            width: 100%;
        }

        .welfare-report-page .report-actions-buttons {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .welfare-report-page .report-action-btn {
            width: 100%;
        }

        .welfare-report-page .report-header {
            grid-template-columns: 1fr;
            justify-items: center;
            gap: 10px;
        }

        .welfare-report-page .report-header-spacer {
            display: none;
        }

        .welfare-report-page .report-header-copy {
            order: 1;
            padding-top: 0;
        }

        .welfare-report-page .photo-box {
            order: 2;
            margin: 0 auto;
        }

        .welfare-report-page .report-title {
            font-size: 24px;
        }

        .welfare-report-page .form-row {
            gap: 4px;
        }

        .welfare-report-page .row-no {
            width: 8mm;
            flex-basis: 8mm;
        }

        .welfare-report-page .problem-list,
        .welfare-report-page .doc-list,
        .welfare-report-page .paired-wrap-row {
            grid-template-columns: 1fr;
        }

        .welfare-report-page .subitem-group {
            padding-left: 0;
        }

        .welfare-report-page .subitem-row {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .welfare-report-page .followup-report-table {
            min-width: 620px;
        }
    }

    /* ------------------------------------------------------------------
     | งานพิมพ์ A4
     * ------------------------------------------------------------------ */
    @media print {
        html,
        body {
            width: auto !important;
            min-width: 0 !important;
            height: auto !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
            background: #ffffff !important;
        }

        /*
         * ก่อนเปิดหน้าต่างพิมพ์ JavaScript จะย้ายรายงานมาไว้ใต้ body โดยตรง
         * เพื่อไม่ให้ layout, sidebar, topbar หรือ container ของหน้า Admin
         * บีบขนาดรายงานจนตัวหนังสือเล็กกว่าหน้าจอ
         */
        body.welfare-report-printing > *:not(.welfare-report-page) {
            display: none !important;
        }

        body.welfare-report-printing > .welfare-report-page {
            display: block !important;
        }

        .welfare-report-page {
            position: static !important;
            inset: auto !important;
            width: auto !important;
            max-width: none !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
            background: #ffffff !important;
            color: var(--wr-ink) !important;
            font-family: var(--bs-body-font-family, inherit) !important;
            font-size: 14px !important;
            line-height: 1.55 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .welfare-report-page .report-actions {
            display: none !important;
        }

        /* ใช้สัดส่วนเดียวกับหน้ารายงาน แต่ตัดกรอบและเงาสำหรับกระดาษ */
        .welfare-report-page .report-wrap {
            display: block !important;
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: #ffffff !important;
            box-shadow: none !important;
        }

        .welfare-report-page .report-header {
            grid-template-columns: 28mm minmax(0, 1fr) 28mm;
            gap: 7mm;
            min-height: 27mm;
            margin: 0 0 4mm;
            padding: 2mm 0 3mm;
            border-bottom: 1.5px solid var(--wr-line-dark);
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .welfare-report-page .report-header-spacer {
            width: 28mm;
        }

        .welfare-report-page .report-header-copy {
            padding-top: 2.5mm;
        }

        .welfare-report-page .report-title {
            color: #111827 !important;
            font-size: 22px !important;
            font-weight: 700 !important;
            line-height: 1.25 !important;
        }

        .welfare-report-page .report-title-rule {
            width: 24mm;
            height: 1.5px;
            margin-top: 3mm;
            background: var(--wr-accent) !important;
        }

        .welfare-report-page .photo-box {
            width: 23mm;
            height: 27mm;
            border-color: #64748b;
        }

        /* คงขนาดและระยะห่างเดียวกับหน้ารายงาน */
        .welfare-report-page .form-row {
            margin-bottom: 2.7mm;
        }

        .welfare-report-page .row-no {
            width: 9mm;
            flex-basis: 9mm;
            font-size: 14px;
            line-height: 1.35;
        }

        .welfare-report-page .line {
            gap: 1.7mm 4.5mm;
        }

        .welfare-report-page .sub-line {
            margin-top: 1.8mm;
        }

        .welfare-report-page .sub-line + .sub-line {
            margin-top: 1.3mm;
        }

        .welfare-report-page .field {
            min-height: 6.5mm;
        }

        .welfare-report-page .value {
            min-height: 5.5mm;
            padding-bottom: .4mm;
            border-bottom: .35mm solid #94a3b8;
        }

        .welfare-report-page .report-block-title,
        .welfare-report-page .problem-title,
        .welfare-report-page .member-table-title {
            margin: 5.5mm 0 3mm;
            padding: 1.8mm 2.5mm;
            border-left: 1.1mm solid var(--wr-accent);
            border-bottom: 1px solid #d8dee8;
            background: var(--wr-soft) !important;
            color: #111827 !important;
            font-size: 16px;
            font-weight: 700;
        }

        .welfare-report-page .factfinding-section {
            margin-top: 7mm;
            padding-top: 5mm;
            border-top: .7mm solid #64748b;
        }

        .welfare-report-page .factfinding-subtitle {
            margin: 5mm 0 2.5mm;
            padding-bottom: 1.3mm;
            border-bottom: 1px solid #94a3b8;
            color: #111827 !important;
            font-size: 15px;
            font-weight: 700;
        }

        .welfare-report-page .problem-list,
        .welfare-report-page .doc-list {
            gap: 1.5mm 6mm;
        }

        .welfare-report-page .problem-item,
        .welfare-report-page .doc-item {
            font-size: 13px;
            line-height: 1.35;
        }

        .welfare-report-page .member-table-wrap {
            margin-top: 6mm;
        }

        .welfare-report-page .member-table {
            margin-top: 2mm;
        }

        .welfare-report-page .member-table thead {
            display: table-header-group;
        }

        .welfare-report-page .member-table th,
        .welfare-report-page .member-table td {
            padding: 1.8mm 2mm;
            border: .25mm solid #475569;
            color: #111827 !important;
            font-size: 12px;
            line-height: 1.35;
        }

        .welfare-report-page .member-table th {
            background: #f1f5f9 !important;
        }

        .welfare-report-page .factfinding-box {
            margin-top: 2.5mm;
            padding: 2.5mm 3mm;
            border: 1px solid #cbd5e1;
            border-radius: 2px;
            background: #f8fafc !important;
        }

        .welfare-report-page .factfinding-box + .factfinding-box {
            margin-top: 2.5mm;
        }

        .welfare-report-page .factfinding-text {
            min-height: 12mm;
            margin-top: 1.5mm;
            padding: 2mm 2.5mm;
            border: 1px solid #d7dee8;
            border-radius: 2px;
            background: #ffffff !important;
            line-height: 1.5;
        }

        .welfare-report-page .subitem-group {
            padding-left: 9mm;
        }

        .welfare-report-page .subitem-row {
            grid-template-columns: 13mm minmax(0, 1fr);
            gap: 2mm;
            margin-bottom: 2.5mm;
        }

        .welfare-report-page .followup-table-wrap {
            overflow: visible !important;
        }

        .welfare-report-page .followup-report-table {
            width: 100% !important;
            min-width: 0 !important;
            table-layout: fixed !important;
        }

        .welfare-report-page .followup-report-table col.followup-col-date {
            width: 28mm;
        }

        .welfare-report-page .followup-report-table col.followup-col-note {
            width: 34mm;
        }

        /* การแบ่งหน้า */
        .welfare-report-page .report-header,
        .welfare-report-page .report-block-title,
        .welfare-report-page .problem-title,
        .welfare-report-page .member-table-title,
        .welfare-report-page .factfinding-subtitle,
        .welfare-report-page .subitem-row,
        .welfare-report-page .form-row,
        .welfare-report-page .problem-item,
        .welfare-report-page .doc-item,
        .welfare-report-page .member-table tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .welfare-report-page .factfinding-box {
            break-inside: auto;
            page-break-inside: auto;
        }

        .welfare-report-page p,
        .welfare-report-page .factfinding-text {
            orphans: 3;
            widows: 3;
        }
    }
</style>



@php
    $factFinding = $factFinding ?? null;
    $followups = $followups ?? collect();

    $thaiMonths = [
        1 => 'มกราคม',
        2 => 'กุมภาพันธ์',
        3 => 'มีนาคม',
        4 => 'เมษายน',
        5 => 'พฤษภาคม',
        6 => 'มิถุนายน',
        7 => 'กรกฎาคม',
        8 => 'สิงหาคม',
        9 => 'กันยายน',
        10 => 'ตุลาคม',
        11 => 'พฤศจิกายน',
        12 => 'ธันวาคม',
    ];

    $birthDate   = $client->birth_date ? date('d/m/Y', strtotime($client->birth_date)) : '-';
    $arrivalDate = $client->arrival_date ? date('d/m/Y', strtotime($client->arrival_date)) : '-';

    $factDate = !empty($factFinding?->date) ? date('d/m/Y', strtotime($factFinding->date)) : '-';
    $receiveDate = !empty($factFinding?->receive_date) ? date('d/m/Y', strtotime($factFinding->receive_date)) : '-';

    $showText = function ($value, $default = '-') {
        return filled($value) ? $value : $default;
    };

    $extractPlaceName = function ($value, array $possibleKeys = [], $default = '-') {
        if (blank($value)) {
            return $default;
        }

        if (is_string($value) || is_numeric($value)) {
            return $value;
        }

        if (is_object($value)) {
            foreach ($possibleKeys as $key) {
                if (isset($value->$key) && filled($value->$key)) {
                    return $value->$key;
                }
            }

            if ($value instanceof \JsonSerializable) {
                $value = $value->jsonSerialize();
            } else {
                $value = (array) $value;
            }
        }

        if (is_array($value)) {
            foreach ($possibleKeys as $key) {
                if (array_key_exists($key, $value) && filled($value[$key])) {
                    return $value[$key];
                }
            }
        }

        return $default;
    };

    $personSubDistrict = function ($person) use ($extractPlaceName) {
        if (!$person) return '-';

        return $extractPlaceName(
            $person->sub_district ?? $person->subDistrict ?? null,
            ['subd_name', 'name', 'subdistrict_name', 'sub_district_name']
        );
    };

    $personDistrict = function ($person) use ($extractPlaceName) {
        if (!$person) return '-';

        return $extractPlaceName(
            $person->district ?? null,
            ['dist_name', 'name', 'district_name']
        );
    };

    $personProvince = function ($person) use ($extractPlaceName) {
        if (!$person) return '-';

        return $extractPlaceName(
            $person->province ?? null,
            ['prov_name', 'name', 'province_name']
        );
    };
@endphp


<div class="welfare-report-page">
    <div class="report-actions" role="toolbar" aria-label="คำสั่งรายงาน">
        <div class="report-actions-note">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            <span>ตั้งค่ากระดาษ A4 และปิด “หัวกระดาษและท้ายกระดาษ” ในหน้าต่างพิมพ์</span>
        </div>

        <div class="report-actions-buttons">
            <a href="{{ url()->previous() }}"
               class="report-action-btn report-action-back"
               aria-label="กลับหน้าหลัก">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                <span>กลับหน้าหลัก</span>
            </a>

            <button type="button"
                    class="report-action-btn report-action-print"
                    onclick="printWelfareReport()"
                    aria-label="พิมพ์รายงาน">
                <i class="bi bi-printer" aria-hidden="true"></i>
                <span>พิมพ์รายงาน</span>
            </button>
        </div>
    </div>

    <div class="report-wrap">
        <div class="report-header">
            <div class="report-header-spacer" aria-hidden="true"></div>

            <div class="report-header-copy">
                <h1 class="report-title">ทะเบียนประวัติผู้รับการสงเคราะห์</h1>
                <div class="report-title-rule" aria-hidden="true"></div>
            </div>

            <div class="photo-box">
                <img src="{{ !empty($client->image) ? route('client.image', $client->id) : asset('upload/no_image.jpg') }}"
                     alt="รูปถ่ายผู้รับการสงเคราะห์">
            </div>
        </div>

    <div class="form-row">
        <div class="row-no"></div>
        <div class="row-body">
            <div class="line">
                <div class="field">
                    <span class="label">เลขทะเบียน</span>
                    <span class="value left w-90">{{ $client->register_number ?? '-' }}</span>
                </div>

                <div class="field" style="margin-left:36px;">
                    <span class="label">ชื่อเล่น</span>
                    <span class="value left w-90">{{ $client->nick_name ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="row-no">1.</div>
        <div class="row-body">
            <div class="line">
                <div class="field">
                    <span class="label">ชื่อ-สกุล</span>
                    <span class="value left w-260">{{ $client->full_name ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">อายุ</span>
                    <span class="value w-40">{{ $client->age ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">ปี</span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="row-no">2.</div>
        <div class="row-body">
            <div class="line">
                <div class="field">
                    <span class="label">วัน เดือน ปี เกิด</span>
                    <span class="value w-100">{{ $birthDate }}</span>
                </div>

                <div class="field">
                    <span class="label">เชื้อชาติ</span>
                    <span class="value left w-80">{{ optional($client->national)->national_name ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">สัญชาติ</span>
                    <span class="value left w-80">{{ optional($client->national)->national_name ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">ศาสนา</span>
                    <span class="value left w-80">{{ optional($client->religion)->religion_name ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="row-no">3.</div>
        <div class="row-body">
            <div class="line">
                <div class="field">
                    <span class="label">บัตรประจำตัวประชาชนเลขที่</span>
                    <span class="value left w-180">{{ $client->id_card ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">วันที่รับเข้า</span>
                    <span class="value w-100">{{ $arrivalDate }}</span>
                </div>

                <div class="field">
                    <span class="label">กลุ่มเป้าหมาย</span>
                    <span class="value left w-130">{{ optional($client->target)->target_name ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="row-no">4.</div>
        <div class="row-body">
            <div class="line">
                <div class="field">
                    <span class="label">ภูมิลำเนาเดิมเลขที่</span>
                    <span class="value left w-50">{{ $client->origin_address ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">ตรอก/ซอย</span>
                    <span class="value left w-90">{{ $client->origin_soi ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">ถนน</span>
                    <span class="value left w-90">{{ $client->origin_road ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">หมู่ที่</span>
                    <span class="value w-40">{{ $client->origin_moo ?? '-' }}</span>
                </div>
            </div>

            <div class="sub-line">
                <div class="line">
                    <div class="field">
                        <span class="label">ตำบล/แขวง</span>
                        <span class="value left w-120">{{ optional($client->originSubDistrict)->subd_name ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">อำเภอ/เขต</span>
                        <span class="value left w-120">{{ optional($client->originDistrict)->dist_name ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">จังหวัด</span>
                        <span class="value left w-110">{{ optional($client->originProvince)->prov_name ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">โทร.</span>
                        <span class="value left w-100">{{ $client->origin_phone ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="row-no">5.</div>
        <div class="row-body">
            <div class="line">
                <div class="field">
                    <span class="label">ที่อยู่ปัจจุบัน เลขที่</span>
                    <span class="value left w-50">{{ $client->address ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">ตรอก/ซอย</span>
                    <span class="value left w-90">{{ $client->soi ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">ถนน</span>
                    <span class="value left w-90">{{ $client->road ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">หมู่ที่</span>
                    <span class="value w-40">{{ $client->moo ?? '-' }}</span>
                </div>
            </div>

            <div class="sub-line">
                <div class="line">
                    <div class="field">
                        <span class="label">ตำบล/แขวง</span>
                        <span class="value left w-120">{{ optional($client->sub_district)->subd_name ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">อำเภอ/เขต</span>
                        <span class="value left w-120">{{ optional($client->district)->dist_name ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">จังหวัด</span>
                        <span class="value left w-110">{{ optional($client->province)->prov_name ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">โทร.</span>
                        <span class="value left w-100">{{ $client->phone ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="row-no">6.</div>
        <div class="row-body">
            <div class="line">
                <div class="field">
                    <span class="label">ระดับการศึกษา</span>
                    <span class="value left w-120">{{ optional($client->education)->education_name ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">สถานศึกษา</span>
                    <span class="value left w-220">{{ $client->scholl ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">รายได้รายเดือน</span>
                    <span class="value left w-110">{{ optional($client->income)->income_name ?? '-' }}</span>
                </div>
            </div>

            <div class="sub-line">
                <div class="line">
                    <div class="field">
                        <span class="label">ปีการศึกษา</span>
                        <span class="value left w-70">{{ $client->study_year ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">จังหวัด</span>
                        <span class="value left w-110">{{ $client->school_province ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">อำเภอ/เขต</span>
                        <span class="value left w-110">{{ $client->school_district ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="report-block-title">ข้อมูลครอบครัว</div>

    <div class="form-row">
        <div class="row-no">7.</div>
        <div class="row-body">
            <div class="line">
                <div class="field">
                    <span class="label">บิดาชื่อ</span>
                    <span class="value left w-110">{{ optional($client->father)->fname ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">นามสกุล</span>
                    <span class="value left w-110">{{ optional($client->father)->lname ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">อายุ</span>
                    <span class="value w-40">{{ optional($client->father)->age ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">ปี</span>
                </div>

                <div class="field">
                    <span class="label">อาชีพ</span>
                    <span class="value left w-100">{{ optional($client->father)->occupation ?? '-' }}</span>
                </div>
            </div>

            <div class="sub-line">
                <div class="line">
                    <div class="field">
                        <span class="label">ที่อยู่ปัจจุบัน เลขที่</span>
                        <span class="value left w-50">{{ optional($client->father)->address_no ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">ตรอก/ซอย</span>
                        <span class="value left w-90">{{ optional($client->father)->soi ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">ถนน</span>
                        <span class="value left w-90">{{ optional($client->father)->road ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">หมู่ที่</span>
                        <span class="value w-40">{{ optional($client->father)->moo ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="sub-line">
                <div class="line">
                    <div class="field">
                        <span class="label">ตำบล/แขวง</span>
                        <span class="value left w-120">{{ $personSubDistrict($client->father) }}</span>
                    </div>

                    <div class="field">
                        <span class="label">อำเภอ/เขต</span>
                        <span class="value left w-120">{{ $personDistrict($client->father) }}</span>
                    </div>

                    <div class="field">
                        <span class="label">จังหวัด</span>
                        <span class="value left w-110">{{ $personProvince($client->father) }}</span>
                    </div>

                    <div class="field">
                        <span class="label">โทร.</span>
                        <span class="value left w-100">{{ optional($client->father)->phone ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="row-no">8.</div>
        <div class="row-body">
            <div class="line">
                <div class="field">
                    <span class="label">มารดาชื่อ</span>
                    <span class="value left w-110">{{ optional($client->mother)->fname ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">นามสกุล</span>
                    <span class="value left w-110">{{ optional($client->mother)->lname ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">อายุ</span>
                    <span class="value w-40">{{ optional($client->mother)->age ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">ปี</span>
                </div>

                <div class="field">
                    <span class="label">อาชีพ</span>
                    <span class="value left w-100">{{ optional($client->mother)->occupation ?? '-' }}</span>
                </div>
            </div>

            <div class="sub-line">
                <div class="line">
                    <div class="field">
                        <span class="label">ที่อยู่ปัจจุบัน เลขที่</span>
                        <span class="value left w-50">{{ optional($client->mother)->address_no ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">ตรอก/ซอย</span>
                        <span class="value left w-90">{{ optional($client->mother)->soi ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">ถนน</span>
                        <span class="value left w-90">{{ optional($client->mother)->road ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">หมู่ที่</span>
                        <span class="value w-40">{{ optional($client->mother)->moo ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="sub-line">
                <div class="line">
                    <div class="field">
                        <span class="label">ตำบล/แขวง</span>
                        <span class="value left w-120">{{ $personSubDistrict($client->mother) }}</span>
                    </div>

                    <div class="field">
                        <span class="label">อำเภอ/เขต</span>
                        <span class="value left w-120">{{ $personDistrict($client->mother) }}</span>
                    </div>

                    <div class="field">
                        <span class="label">จังหวัด</span>
                        <span class="value left w-110">{{ $personProvince($client->mother) }}</span>
                    </div>

                    <div class="field">
                        <span class="label">โทร.</span>
                        <span class="value left w-100">{{ optional($client->mother)->phone ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="row-no">9.</div>
        <div class="row-body">
            <div class="line">
                <div class="field">
                    <span class="label">ผู้ปกครองชื่อ</span>
                    <span class="value left w-110">{{ optional($client->spouse)->fname ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">นามสกุล</span>
                    <span class="value left w-110">{{ optional($client->spouse)->lname ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">อายุ</span>
                    <span class="value w-40">{{ optional($client->spouse)->age ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">ปี</span>
                </div>

                <div class="field">
                    <span class="label">เกี่ยวข้องเป็น</span>
                    <span class="value left w-100">{{ optional($client->spouse)->relation ?? '-' }}</span>
                </div>
            </div>

            <div class="sub-line">
                <div class="line">
                    <div class="field">
                        <span class="label">ที่อยู่ปัจจุบัน เลขที่</span>
                        <span class="value left w-50">{{ optional($client->spouse)->address_no ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">ตรอก/ซอย</span>
                        <span class="value left w-90">{{ optional($client->spouse)->soi ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">ถนน</span>
                        <span class="value left w-90">{{ optional($client->spouse)->road ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">หมู่ที่</span>
                        <span class="value w-40">{{ optional($client->spouse)->moo ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="sub-line">
                <div class="line">
                    <div class="field">
                        <span class="label">ตำบล/แขวง</span>
                        <span class="value left w-120">{{ $personSubDistrict($client->spouse) }}</span>
                    </div>

                    <div class="field">
                        <span class="label">อำเภอ/เขต</span>
                        <span class="value left w-120">{{ $personDistrict($client->spouse) }}</span>
                    </div>

                    <div class="field">
                        <span class="label">จังหวัด</span>
                        <span class="value left w-110">{{ $personProvince($client->spouse) }}</span>
                    </div>

                    <div class="field">
                        <span class="label">โทร.</span>
                        <span class="value left w-100">{{ optional($client->spouse)->phone ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="row-no">10.</div>
        <div class="row-body">
            <div class="line">
                <div class="field">
                    <span class="label">ญาติที่อยู่ที่อื่นชื่อ</span>
                    <span class="value left w-110">{{ optional($client->relative)->fname ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">นามสกุล</span>
                    <span class="value left w-110">{{ optional($client->relative)->lname ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">อายุ</span>
                    <span class="value w-40">{{ optional($client->relative)->age ?? '-' }}</span>
                </div>

                <div class="field">
                    <span class="label">ปี</span>
                </div>

                <div class="field">
                    <span class="label">เกี่ยวข้องเป็น</span>
                    <span class="value left w-100">{{ optional($client->relative)->relation ?? '-' }}</span>
                </div>
            </div>

            <div class="sub-line">
                <div class="line">
                    <div class="field">
                        <span class="label">ที่อยู่ปัจจุบัน เลขที่</span>
                        <span class="value left w-50">{{ optional($client->relative)->address_no ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">ตรอก/ซอย</span>
                        <span class="value left w-90">{{ optional($client->relative)->soi ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">ถนน</span>
                        <span class="value left w-90">{{ optional($client->relative)->road ?? '-' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">หมู่ที่</span>
                        <span class="value w-40">{{ optional($client->relative)->moo ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="sub-line">
                <div class="line">
                    <div class="field">
                        <span class="label">ตำบล/แขวง</span>
                        <span class="value left w-120">{{ $personSubDistrict($client->relative) }}</span>
                    </div>

                    <div class="field">
                        <span class="label">อำเภอ/เขต</span>
                        <span class="value left w-120">{{ $personDistrict($client->relative) }}</span>
                    </div>

                    <div class="field">
                        <span class="label">จังหวัด</span>
                        <span class="value left w-110">{{ $personProvince($client->relative) }}</span>
                    </div>

                    <div class="field">
                        <span class="label">โทร.</span>
                        <span class="value left w-100">{{ optional($client->relative)->phone ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="problem-title">สภาพปัญหา</div>
        <div class="problem-list">
            @if(!empty($clientProblems) && $clientProblems->count() > 0)

                @foreach($clientProblems as $problem)
                    <div class="problem-item">
                        <span class="checkbox">☑</span>
                        {{ $problem->problem_name ?? $problem->name ?? '-' }}
                    </div>
                @endforeach

            @else
                <div class="problem-item">- ไม่มีข้อมูลปัญหา -</div>
            @endif
        </div>

    <div class="member-table-wrap">
        <div class="member-table-title">รายละเอียดสมาชิกครอบครัว</div>

        <table class="member-table">
            <thead>
                <tr>
                    <th style="width:7%;">ลำดับที่</th>
                    <th style="width:25%;">ชื่อ-สกุล</th>
                    <th style="width:7%;">อายุ</th>
                    <th style="width:10%;">เกี่ยวข้อง</th>
                    <th style="width:20%;">อาชีพ/การศึกษา</th>
                    <th style="width:17%;">รายได้/เดือน</th>
                    <th style="width:15%;">หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($client->members as $index => $member)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $member->fullname ?? '-' }}</td>
                        <td class="center">{{ $member->member_age ?? '-' }}</td>
                        <td class="center">{{ $member->relationship ?? '-' }}</td>
                        <td>{{ optional($member->occupation)->occupation_name ?? optional($member->education)->education_name ?? '-' }}</td>
                        <td class="center">{{ optional($member->income)->income_name ?? '-' }}</td>
                        <td>{{ $member->remark ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="center">&nbsp;</td>
                        <td></td>
                        <td class="center"></td>
                        <td class="center"></td>
                        <td></td>
                        <td class="center"></td>
                        <td></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="factfinding-section">
       

        <div class="form-row">
            <div class="row-no">11.</div>
            <div class="row-body">
                <div class="line">
                    <div class="field">
                        <span class="label">วันที่นำส่ง</span>
                        <span class="value w-100">{{ $factDate }}</span>
                    </div>

                    <div class="field">
                        <span class="label">วันที่บันทึกข้อมูล</span>
                        <span class="value w-100">{{ $receiveDate }}</span>
                    </div>

                    <div class="field">
                        <span class="label">ผู้นำส่ง</span>
                        <span class="value left w-260">{{ $showText($factFinding?->fact_name) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="factfinding-subtitle">12. ข้อมูลด้านร่างกายและสุขภาพ</div>

        <div class="form-row">
            <div class="row-no"></div>
            <div class="row-body">
                <div class="line">
                    <div class="field">
                        <span class="label">รูปพรรณสัณฐาน</span>
                        <span class="value left w-150">{{ $showText($factFinding?->appearance) }}</span>
                    </div>
                    <div class="field">
                        <span class="label">สีผิว</span>
                        <span class="value left w-120">{{ $showText($factFinding?->skin) }}</span>
                    </div>
                    <div class="field">
                        <span class="label">ตำหนิ/แผลเป็น</span>
                        <span class="value left w-120">{{ $showText($factFinding?->scar) }}</span>
                    </div>
                    <div class="field">
                        <span class="label">ลักษณะความพิการ</span>
                        <span class="value left w-140">{{ $showText($factFinding?->disability) }}</span>
                    </div>
                </div>

                <div class="sub-line">
                    <div class="line">
                        <div class="field">
                            <span class="label">น้ำหนัก</span>
                            <span class="value w-55">{{ $showText($factFinding?->weight) }}</span>
                        </div>
                        <div class="field"><span class="label">กก.</span></div>

                        <div class="field">
                            <span class="label">ส่วนสูง</span>
                            <span class="value w-55">{{ $showText($factFinding?->height) }}</span>
                        </div>
                        <div class="field"><span class="label">ซม.</span></div>

                        <div class="field">
                            <span class="label">กรุ๊ปเลือด</span>
                            <span class="value left w-70">{{ $showText($factFinding?->blood_group) }}</span>
                        </div>

                        <div class="field">
                            <span class="label">สุขอนามัย</span>
                            <span class="value left w-120">{{ $showText($factFinding?->hygiene) }}</span>
                        </div>

                        <div class="field">
                            <span class="label">สุขภาพช่องปาก</span>
                            <span class="value left w-120">{{ $showText($factFinding?->oral_health) }}</span>
                        </div>
                    </div>
                </div>

                <div class="sub-line">
                    <div class="line">
                        <div class="field">
                            <span class="label">บาดแผล/การบาดเจ็บ</span>
                            <span class="value left w-260">{{ $showText($factFinding?->injury) }}</span>
                        </div>
                       <div class="field wrap">
    <span class="label">หลักฐานที่พบ</span>
    <span class="value left w-260">{{ $showText($factFinding?->evidence) }}</span>
</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="row-no">13.</div>
            <div class="row-body">
                <div class="line">
                    <div class="field">
                        <span class="label">การเจ็บป่วย</span>
                        <span class="value left w-70">{{ ($factFinding?->sick ?? 0) == 1 ? 'มี' : 'ไม่มี' }}</span>
                    </div>

                    <div class="field">
                        <span class="label">รายละเอียดการเจ็บป่วย</span>
                        <span class="value left w-320">{{ $showText($factFinding?->sick_detail) }}</span>
                    </div>
                </div>

                <div class="sub-line">
                    <div class="line">
                        <div class="field">
                            <span class="label">การรักษา</span>
                            <span class="value left w-220">{{ $showText($factFinding?->treatment) }}</span>
                        </div>

                        <div class="field">
                            <span class="label">โรงพยาบาล</span>
                            <span class="value left w-260">{{ $showText($factFinding?->hospital) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="factfinding-subtitle">14. ข้อมูลด้านครอบครัวและความสัมพันธ์</div>

        <div class="subitem-group">
            <div class="subitem-row">
                <div class="subitem-no">14.1</div>
                <div class="subitem-body">
                    <div class="full-row-field">
                        <span class="label">สถานภาพสมรส</span>
                        <span class="value left">{{ optional($factFinding?->marital)->marital_name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="subitem-row">
                <div class="subitem-no">14.2</div>
                <div class="subitem-body">
                    <div class="full-row-field">
                        <span class="label">ความสัมพันธ์ระหว่างบิดามารดา</span>
                        <span class="value left">{{ $showText($factFinding?->relation_parent) }}</span>
                    </div>
                </div>
            </div>

            <div class="subitem-row">
                <div class="subitem-no">14.3</div>
                <div class="subitem-body">
                    <div class="full-row-field">
                        <span class="label">ความสัมพันธ์ระหว่างบุคคลในครอบครัว</span>
                        <span class="value left">{{ $showText($factFinding?->relation_family) }}</span>
                    </div>
                </div>
            </div>

            <div class="subitem-row">
                <div class="subitem-no">14.4</div>
                <div class="subitem-body">
                    <div class="full-row-field">
                        <span class="label">ความสัมพันธ์กับบุตร/บุคคลในครอบครัว</span>
                        <span class="value left">{{ $showText($factFinding?->relation_child) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="factfinding-subtitle">15. สภาพแวดล้อม</div>

        <div class="factfinding-box">
            <div class="label">สภาพที่อยู่อาศัยภายนอก</div>
            <div class="factfinding-text">{{ $showText($factFinding?->ex_conditions) }}</div>
        </div>

        <div class="factfinding-box">
            <div class="label">สภาพที่อยู่อาศัยภายใน</div>
            <div class="factfinding-text">{{ $showText($factFinding?->in_conditions) }}</div>
        </div>

        <div class="factfinding-box">
            <div class="label">สภาพแวดล้อม</div>
            <div class="factfinding-text">{{ $showText($factFinding?->environment) }}</div>
        </div>

        <div class="factfinding-subtitle">16. การวิเคราะห์ปัญหาและความต้องการ</div>

        <div class="factfinding-box">
            <div class="label">สาเหตุที่เข้ารับการสงเคราะห์</div>
            <div class="factfinding-text">{{ $showText($factFinding?->cause_problem) }}</div>
        </div>

        <div class="factfinding-box">
            <div class="label">ความต้องการความช่วยเหลือ</div>
            <div class="factfinding-text">{{ $showText($factFinding?->need) }}</div>
        </div>

        <div class="factfinding-box">
            <div class="label">ประวัติความเป็นมา</div>
            <div class="factfinding-text">{{ $showText($factFinding?->case_history) }}</div>
        </div>

        <div class="factfinding-box">
            <div class="label">ข้อมูลเท็จจริงอื่น ๆ</div>
            <div class="factfinding-text">{{ $showText($factFinding?->information) }}</div>
        </div>

        <div class="factfinding-box">
            <div class="label">การวินิจฉัยปัญหา</div>
            <div class="factfinding-text">{{ $showText($factFinding?->diagnosis) }}</div>
        </div>

        <div class="factfinding-subtitle">17. เอกสารประกอบและผู้บันทึก</div>

        <div class="form-row">
            <div class="row-no"></div>
            <div class="row-body">
                <div class="line">
                    <div class="field">
                        <span class="label">ผู้บันทึก</span>
                        <span class="value left w-220">{{ $showText($factFinding?->recorder) }}</span>
                    </div>

                    <div class="field">
                        <span class="label">สถานะข้อมูล</span>
                        <span class="value left w-100">{{ ($factFinding?->active ?? 0) ? 'ใช้งาน' : 'ไม่ใช้งาน' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="problem-title" style="margin-top: 12px;">เอกสารประกอบการสอบข้อเท็จจริง</div>
        <div class="doc-list">
            @if(!empty($factFinding) && $factFinding->documents && $factFinding->documents->count() > 0)
                @foreach($factFinding->documents as $doc)
                    <div class="doc-item">
                        <span class="checkbox">☑</span>{{ $doc->document_name ?? '-' }}
                    </div>
                @endforeach
            @else
                <div class="doc-item muted">- ไม่มีข้อมูลเอกสารประกอบ -</div>
            @endif
        </div>

        <div class="factfinding-subtitle">18. การติดตามผลการช่วยเหลือ</div>

        <div class="followup-table-wrap" role="region" aria-label="ตารางการติดตามผลการช่วยเหลือ" tabindex="0">
            <table class="member-table followup-report-table">
                <colgroup>
                    <col class="followup-col-date">
                    <col class="followup-col-detail">
                    <col class="followup-col-note">
                </colgroup>
                <thead>
                    <tr>
                        <th scope="col">วันที่</th>
                        <th scope="col">การช่วยเหลือและติดตามผล</th>
                        <th scope="col">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($followups as $followup)
                        @php
                            $followupDate = !empty($followup->followup_date)
                                ? \Carbon\Carbon::parse($followup->followup_date)
                                : null;

                            $followupThaiDate = $followupDate
                                ? $followupDate->day . ' ' . $thaiMonths[$followupDate->month] . ' ' . ($followupDate->year + 543)
                                : '-';
                        @endphp
                        <tr>
                            <td>{{ $followupThaiDate }}</td>
                            <td class="followup-cell-text">{{ $showText($followup->assistance_detail) }}</td>
                            <td class="followup-cell-text">{{ $showText($followup->note) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="followup-empty-row">- ไม่มีข้อมูลการติดตามผลการช่วยเหลือ -</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>


<script>
    (function () {
        const reportPage = document.querySelector('.welfare-report-page');

        if (!reportPage) {
            return;
        }

        let originalParent = null;
        let originalNextSibling = null;
        let isPrepared = false;

        function preparePrintLayout() {
            if (isPrepared) {
                return;
            }

            originalParent = reportPage.parentNode;
            originalNextSibling = reportPage.nextSibling;

            document.body.appendChild(reportPage);
            document.body.classList.add('welfare-report-printing');
            isPrepared = true;
        }

        function restorePrintLayout() {
            if (!isPrepared || !originalParent) {
                return;
            }

            document.body.classList.remove('welfare-report-printing');

            if (originalNextSibling && originalNextSibling.parentNode === originalParent) {
                originalParent.insertBefore(reportPage, originalNextSibling);
            } else {
                originalParent.appendChild(reportPage);
            }

            isPrepared = false;
        }

        window.printWelfareReport = function () {
            preparePrintLayout();

            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(function () {
                    window.print();
                });
            });
        };

        window.addEventListener('beforeprint', preparePrintLayout);
        window.addEventListener('afterprint', restorePrintLayout);

        /* Safari/บางเบราว์เซอร์อาจไม่เรียก afterprint เมื่อยกเลิก */
        window.addEventListener('focus', function () {
            if (isPrepared) {
                window.setTimeout(restorePrintLayout, 300);
            }
        });
    })();
</script>

@endsection