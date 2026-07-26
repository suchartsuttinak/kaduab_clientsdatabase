@extends('admin.admin_master')

@section('admin')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root{
            --ca-ink:#0f172a;
            --ca-text:#334155;
            --ca-muted:#64748b;
            --ca-line:#e2e8f0;
            --ca-line-strong:#cbd5e1;
            --ca-page:#f5f7fb;
            --ca-white:#ffffff;
            --ca-navy:#12355b;
            --ca-blue:#2563eb;
            --ca-blue-soft:#eff6ff;
            --ca-sky:#0284c7;
            --ca-teal:#0f766e;
            --ca-teal-soft:#ecfdf5;
            --ca-rose:#e11d48;
            --ca-rose-soft:#fff1f2;
            --ca-amber:#b45309;
            --ca-amber-soft:#fffbeb;
            --ca-purple:#6d28d9;
            --ca-purple-soft:#f5f3ff;
            --ca-shadow:0 18px 45px rgba(15,23,42,.08);
            --ca-shadow-soft:0 10px 26px rgba(15,23,42,.055);
        }

        .child-analytics-page{
            padding:18px 0 36px;
            background:linear-gradient(180deg,#f8fafc 0%,#eef4fb 100%);
            color:var(--ca-ink);
        }

        .report-shell{
            max-width:1320px;
            margin:0 auto;
        }

        /* =========================
           COVER / HEADER
        ========================= */
        .report-cover{
            position:relative;
            overflow:hidden;
            border-radius:28px;
            padding:28px;
            margin-bottom:18px;
            color:#ffffff;
            background:
                radial-gradient(circle at 94% 12%, rgba(255,255,255,.22), transparent 26%),
                radial-gradient(circle at 10% 100%, rgba(56,189,248,.30), transparent 26%),
                linear-gradient(135deg,#0b1220 0%,#12355b 45%,#0f766e 100%);
            box-shadow:var(--ca-shadow);
        }

        .report-cover::before{
            content:"";
            position:absolute;
            inset:auto -80px -90px auto;
            width:230px;
            height:230px;
            border-radius:50%;
            background:rgba(255,255,255,.10);
        }

        .report-cover::after{
            content:"";
            position:absolute;
            top:22px;
            right:28px;
            width:180px;
            height:1px;
            background:rgba(255,255,255,.35);
        }

        .report-cover-inner{
            position:relative;
            z-index:1;
        }

        .report-cover-top{
            display:grid;
            grid-template-columns:minmax(0,1fr) 330px;
            gap:24px;
            align-items:start;
        }

        .report-kicker{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:7px 12px;
            border-radius:999px;
            background:rgba(255,255,255,.12);
            border:1px solid rgba(255,255,255,.20);
            color:rgba(255,255,255,.95);
            font-weight:850;
            font-size:13px;
            letter-spacing:.01em;
            margin-bottom:13px;
        }

        .report-title{
            margin:0;
            color:#ffffff !important;
            font-size:30px;
            line-height:1.23;
            font-weight:950;
            letter-spacing:-.025em;
        }

        .report-subtitle{
            margin:10px 0 0;
            color:rgba(255,255,255,.84);
            font-size:14.5px;
            line-height:1.75;
            max-width:880px;
        }

        .report-meta-card{
            background:rgba(255,255,255,.12);
            border:1px solid rgba(255,255,255,.18);
            border-radius:20px;
            padding:15px 16px;
            backdrop-filter:blur(10px);
        }

        .report-meta-row{
            display:flex;
            align-items:flex-start;
            gap:10px;
            color:rgba(255,255,255,.88);
            font-size:13px;
            line-height:1.55;
            padding:7px 0;
            border-bottom:1px solid rgba(255,255,255,.14);
        }

        .report-meta-row:last-child{
            border-bottom:0;
        }

        .report-meta-row i{
            color:#bae6fd;
            margin-top:2px;
        }

        .report-meta-row strong{
            display:block;
            color:#ffffff;
            font-weight:900;
        }

        .screen-actions{
            position:relative;
            z-index:1;
            display:flex;
            gap:10px;
            justify-content:flex-end;
            flex-wrap:wrap;
            margin-top:20px;
        }

        .screen-actions .btn{
            border-radius:999px;
            font-weight:850;
            min-height:43px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            padding:.62rem 1.05rem;
            box-shadow:0 14px 26px rgba(0,0,0,.14);
        }

        .btn-print-main{
            background:#ffffff;
            color:#0f172a !important;
            border:0;
        }

        .btn-back-main{
            background:rgba(255,255,255,.10);
            color:#ffffff !important;
            border:1px solid rgba(255,255,255,.32);
        }

        .btn-back-main:hover{
            background:rgba(255,255,255,.18);
        }

        /* =========================
           FILTER
        ========================= */
        .filter-area{
            background:rgba(255,255,255,.92);
            border:1px solid rgba(226,232,240,.90);
            border-radius:24px;
            padding:18px;
            margin-bottom:18px;
            box-shadow:var(--ca-shadow-soft);
        }

        .filter-header{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:16px;
            margin-bottom:14px;
            padding-bottom:12px;
            border-bottom:1px solid var(--ca-line);
        }

        .filter-title{
            margin:0;
            color:var(--ca-ink);
            font-size:16px;
            font-weight:950;
        }

        .filter-desc{
            margin:3px 0 0;
            color:var(--ca-muted);
            font-size:13px;
        }

        .filter-grid{
            display:grid;
            grid-template-columns:repeat(12, 1fr);
            gap:12px;
        }

        .filter-field{grid-column:span 3;}
        .filter-field.small{grid-column:span 2;}
        .filter-field.wide{grid-column:span 4;}
        .filter-field.full{grid-column:1 / -1;}

        .child-analytics-page .form-label{
            color:#334155;
            font-size:13px;
            font-weight:850;
            margin-bottom:6px;
        }

        .child-analytics-page .form-control,
        .child-analytics-page .form-select{
            min-height:42px;
            border-radius:12px;
            border-color:#dbe3ec;
            box-shadow:none !important;
            font-size:14px;
            background-color:#fff;
        }

        .child-analytics-page .form-control:focus,
        .child-analytics-page .form-select:focus{
            border-color:#38bdf8;
            box-shadow:0 0 0 .18rem rgba(56,189,248,.14) !important;
        }

        .filter-actions{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            margin-top:4px;
        }

        .filter-actions .btn{
            min-height:42px;
            border-radius:12px;
            font-weight:850;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
        }

        .btn-process{
            background:linear-gradient(135deg,#1d4ed8,#0f766e);
            border:0;
            color:#fff !important;
        }

        /* =========================
           CONTEXT + NAV
        ========================= */
        .report-context{
            display:flex;
            align-items:flex-start;
            gap:11px;
            padding:14px 16px;
            border:1px solid #dbeafe;
            border-radius:18px;
            margin-bottom:16px;
            color:var(--ca-text);
            font-size:14px;
            line-height:1.65;
            background:linear-gradient(180deg,#ffffff 0%,#f8fbff 100%);
        }

        .report-context i{
            color:var(--ca-blue);
            margin-top:3px;
            font-size:17px;
        }

        .report-section-nav{
            position:sticky;
            top:72px;
            z-index:10;
            display:grid;
            grid-template-columns:repeat(7, minmax(0,1fr));
            gap:8px;
            padding:10px;
            margin-bottom:18px;
            background:rgba(255,255,255,.92);
            backdrop-filter:blur(12px);
            border:1px solid var(--ca-line);
            border-radius:18px;
            box-shadow:0 10px 28px rgba(15,23,42,.06);
        }

        .report-section-btn{
            border:1px solid transparent;
            background:#f8fafc;
            color:#475569;
            border-radius:14px;
            padding:10px 10px;
            min-height:52px;
            font-size:13px;
            font-weight:900;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:7px;
            transition:.18s ease;
            text-align:center;
        }

        .report-section-btn i{
            width:26px;
            height:26px;
            border-radius:9px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            background:#ffffff;
            color:var(--ca-blue);
            box-shadow:inset 0 0 0 1px #e2e8f0;
        }

        .report-section-btn:hover{
            background:#eef6ff;
            color:var(--ca-ink);
        }

        .report-section-btn.is-active{
            background:linear-gradient(135deg,#0f172a,#1d4ed8);
            color:#ffffff;
            box-shadow:0 12px 24px rgba(29,78,216,.20);
        }

        .report-section-btn.is-active i{
            color:#0f172a;
            background:#ffffff;
            box-shadow:none;
        }

        /* =========================
           SECTION COMMON
        ========================= */
        .report-section{
            display:none;
            background:#ffffff;
            border:1px solid rgba(226,232,240,.96);
            border-radius:26px;
            padding:24px 26px 26px;
            margin-bottom:18px;
            box-shadow:var(--ca-shadow-soft);
        }

        .report-section.is-active{display:block;}

        .section-toolbar{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:16px;
            flex-wrap:wrap;
            padding-bottom:16px;
            margin-bottom:18px;
            border-bottom:1px solid var(--ca-line);
        }

        .section-label{
            margin:0 0 5px;
            color:var(--ca-blue);
            font-size:12px;
            font-weight:950;
            letter-spacing:.10em;
            text-transform:uppercase;
        }

        .section-title{
            margin:0;
            color:var(--ca-ink);
            font-size:22px;
            line-height:1.3;
            font-weight:950;
            letter-spacing:-.02em;
        }

        .section-desc{
            margin:7px 0 0;
            color:var(--ca-muted);
            font-size:14px;
            line-height:1.65;
            max-width:860px;
        }

        .section-actions{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
        }

        .section-actions .btn{
            border-radius:999px;
            font-weight:850;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
        }

        .section-print-btn{
            background:#0f172a;
            color:#fff !important;
            border:0;
            transition:all .18s ease;
        }

        .section-print-btn:hover,
        .section-print-btn:focus,
        .section-print-btn:active,
        .section-print-btn:focus-visible{
            background:linear-gradient(135deg,#1d4ed8,#0f766e) !important;
            color:#ffffff !important;
            border:0 !important;
            box-shadow:0 12px 24px rgba(29,78,216,.22) !important;
            transform:translateY(-1px);
        }

        .section-print-btn:hover i,
        .section-print-btn:focus i,
        .section-print-btn:active i{
            color:#ffffff !important;
        }

        .btn-print-main:hover,
        .btn-print-main:focus,
        .btn-print-main:active,
        .btn-print-main:focus-visible{
            background:#e0f2fe !important;
            color:#0f172a !important;
            border:0 !important;
            box-shadow:0 14px 28px rgba(14,165,233,.22) !important;
            transform:translateY(-1px);
        }



        /* =========================
           BUTTON HOVER REFINEMENT
           ปรับสีปุ่มและ hover ให้สัมพันธ์กันทุกจุด
        ========================= */
        .screen-actions .btn,
        .filter-actions .btn,
        .report-section-btn,
        .section-print-btn,
        .open-link{
            transition:background .18s ease,
                       color .18s ease,
                       border-color .18s ease,
                       box-shadow .18s ease,
                       transform .18s ease;
        }

        .screen-actions .btn:hover,
        .filter-actions .btn:hover,
        .report-section-btn:hover,
        .section-print-btn:hover,
        .open-link:hover{
            transform:translateY(-1px);
        }

        .btn-print-main:hover,
        .btn-print-main:focus,
        .btn-print-main:active,
        .btn-print-main:focus-visible{
            background:linear-gradient(135deg,#e0f2fe 0%,#ffffff 100%) !important;
            color:#0f172a !important;
            border:0 !important;
            box-shadow:0 14px 30px rgba(14,165,233,.24) !important;
        }

        .btn-print-main:hover i,
        .btn-print-main:focus i,
        .btn-print-main:active i{
            color:#0369a1 !important;
        }

        .btn-back-main:hover,
        .btn-back-main:focus,
        .btn-back-main:active,
        .btn-back-main:focus-visible{
            background:rgba(255,255,255,.24) !important;
            color:#ffffff !important;
            border-color:rgba(255,255,255,.60) !important;
            box-shadow:0 14px 30px rgba(15,23,42,.18) !important;
        }

        .btn-process:hover,
        .btn-process:focus,
        .btn-process:active,
        .btn-process:focus-visible{
            background:linear-gradient(135deg,#1e40af 0%,#0f766e 100%) !important;
            color:#ffffff !important;
            border:0 !important;
            box-shadow:0 12px 28px rgba(29,78,216,.24) !important;
        }

        .filter-actions .btn-outline-secondary:hover,
        .filter-actions .btn-outline-secondary:focus,
        .filter-actions .btn-outline-secondary:active,
        .filter-actions .btn-outline-secondary:focus-visible{
            background:#f1f5f9 !important;
            color:#0f172a !important;
            border-color:#94a3b8 !important;
            box-shadow:0 10px 22px rgba(100,116,139,.14) !important;
        }

        .report-section-btn[data-target="section-overview"]{--btn-accent:#1d4ed8;--btn-soft:#eff6ff;--btn-border:#bfdbfe;--btn-grad:linear-gradient(135deg,#1d4ed8,#38bdf8);}
        .report-section-btn[data-target="section-absent"]{--btn-accent:#dc2626;--btn-soft:#fef2f2;--btn-border:#fecaca;--btn-grad:linear-gradient(135deg,#dc2626,#fb7185);}
        .report-section-btn[data-target="section-medical"]{--btn-accent:#047857;--btn-soft:#ecfdf5;--btn-border:#bbf7d0;--btn-grad:linear-gradient(135deg,#047857,#34d399);}
        .report-section-btn[data-target="section-school"]{--btn-accent:#0f766e;--btn-soft:#f0fdfa;--btn-border:#99f6e4;--btn-grad:linear-gradient(135deg,#0f766e,#14b8a6);}
        .report-section-btn[data-target="section-education"]{--btn-accent:#6d28d9;--btn-soft:#f5f3ff;--btn-border:#ddd6fe;--btn-grad:linear-gradient(135deg,#6d28d9,#a78bfa);}
        .report-section-btn[data-target="section-problem"]{--btn-accent:#d97706;--btn-soft:#fffbeb;--btn-border:#fde68a;--btn-grad:linear-gradient(135deg,#d97706,#fbbf24);}
        .report-section-btn[data-target="section-disease"]{--btn-accent:#334155;--btn-soft:#f1f5f9;--btn-border:#cbd5e1;--btn-grad:linear-gradient(135deg,#334155,#64748b);}

        .report-section-btn{
            border-color:transparent !important;
            background:#f8fafc !important;
            color:#475569 !important;
            box-shadow:none;
        }

        .report-section-btn i{
            background:#ffffff !important;
            color:var(--btn-accent,#1d4ed8) !important;
            box-shadow:inset 0 0 0 1px #e2e8f0 !important;
        }

        .report-section-btn:hover,
        .report-section-btn:focus,
        .report-section-btn:focus-visible{
            background:var(--btn-soft,#eff6ff) !important;
            color:var(--btn-accent,#1d4ed8) !important;
            border-color:var(--btn-border,#bfdbfe) !important;
            box-shadow:0 10px 22px rgba(15,23,42,.08) !important;
        }

        .report-section-btn:hover i,
        .report-section-btn:focus i,
        .report-section-btn:focus-visible i{
            background:#ffffff !important;
            color:var(--btn-accent,#1d4ed8) !important;
            box-shadow:inset 0 0 0 1px var(--btn-border,#bfdbfe) !important;
        }

        .report-section-btn.is-active,
        .report-section-btn.is-active:hover,
        .report-section-btn.is-active:focus,
        .report-section-btn.is-active:active,
        .report-section-btn.is-active:focus-visible{
            background:var(--btn-grad,linear-gradient(135deg,#0f172a,#1d4ed8)) !important;
            color:#ffffff !important;
            border-color:transparent !important;
            box-shadow:0 14px 28px rgba(15,23,42,.16) !important;
        }

        .report-section-btn.is-active i,
        .report-section-btn.is-active:hover i,
        .report-section-btn.is-active:focus i,
        .report-section-btn.is-active:active i{
            background:#ffffff !important;
            color:var(--btn-accent,#1d4ed8) !important;
            box-shadow:none !important;
        }

        .section-print-btn{
            background:linear-gradient(135deg,#0f172a 0%,#334155 100%) !important;
            color:#ffffff !important;
            border:0 !important;
            box-shadow:0 8px 18px rgba(15,23,42,.14);
        }

        .section-print-btn:hover,
        .section-print-btn:focus,
        .section-print-btn:active,
        .section-print-btn:focus-visible{
            background:linear-gradient(135deg,#1d4ed8 0%,#0f766e 100%) !important;
            color:#ffffff !important;
            border:0 !important;
            box-shadow:0 14px 28px rgba(29,78,216,.24) !important;
        }

        .section-print-btn i,
        .section-print-btn:hover i,
        .section-print-btn:focus i,
        .section-print-btn:active i{
            color:#ffffff !important;
        }

        .open-link:hover,
        .open-link:focus,
        .open-link:active,
        .open-link:focus-visible{
            background:#eff6ff !important;
            color:#1d4ed8 !important;
            border-color:#93c5fd !important;
            box-shadow:0 8px 18px rgba(29,78,216,.12) !important;
            text-decoration:none !important;
        }

        /* =========================
           OVERVIEW SCREEN
        ========================= */
        .overview-layout{
            display:grid;
            grid-template-columns:1.05fr 1.45fr;
            gap:18px;
            align-items:stretch;
        }

        .overview-brief{
            position:relative;
            overflow:hidden;
            border-radius:24px;
            padding:22px;
            color:#fff;
            background:
                radial-gradient(circle at 90% 10%, rgba(255,255,255,.22), transparent 28%),
                linear-gradient(135deg,#12355b 0%,#0f766e 100%);
            min-height:294px;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
        }

        .overview-brief::after{
            content:"";
            position:absolute;
            right:-55px;
            bottom:-65px;
            width:180px;
            height:180px;
            border-radius:50%;
            background:rgba(255,255,255,.10);
        }

        .brief-caption{
            position:relative;
            z-index:1;
            display:inline-flex;
            align-items:center;
            gap:8px;
            font-size:13px;
            font-weight:900;
            color:#dff9ff;
            margin-bottom:13px;
        }

        .brief-total{
            position:relative;
            z-index:1;
        }

        .brief-total-label{
            margin:0;
            color:rgba(255,255,255,.78);
            font-size:14px;
            font-weight:800;
        }

        .brief-total-value{
            margin:7px 0 0;
            font-size:56px;
            line-height:1;
            font-weight:950;
            letter-spacing:-.05em;
        }

        .brief-total-note{
            margin:10px 0 0;
            color:rgba(255,255,255,.80);
            font-size:14px;
            line-height:1.6;
        }

        .brief-chips{
            position:relative;
            z-index:1;
            display:flex;
            flex-wrap:wrap;
            gap:8px;
            margin-top:18px;
        }

        .brief-chip{
            display:inline-flex;
            align-items:center;
            gap:6px;
            border-radius:999px;
            padding:7px 10px;
            background:rgba(255,255,255,.14);
            border:1px solid rgba(255,255,255,.16);
            color:#fff;
            font-size:12px;
            font-weight:850;
        }

        .summary-grid{
            display:grid;
            grid-template-columns:repeat(2, minmax(0,1fr));
            gap:14px;
        }

        .summary-card{
            position:relative;
            overflow:hidden;
            border-radius:22px;
            padding:18px;
            background:#fff;
            border:1px solid var(--ca-line);
            min-height:140px;
            box-shadow:0 8px 22px rgba(15,23,42,.045);
        }

        .summary-card::before{
            content:"";
            position:absolute;
            inset:0 auto auto 0;
            width:5px;
            height:100%;
            background:var(--accent,#2563eb);
        }

        .summary-card::after{
            content:"";
            position:absolute;
            right:-42px;
            top:-42px;
            width:112px;
            height:112px;
            border-radius:50%;
            background:var(--soft,#eff6ff);
        }

        .summary-card.blue{--accent:#2563eb;--soft:#eff6ff;}
        .summary-card.rose{--accent:#e11d48;--soft:#fff1f2;}
        .summary-card.teal{--accent:#0f766e;--soft:#ecfdf5;}
        .summary-card.purple{--accent:#6d28d9;--soft:#f5f3ff;}

        .summary-card-content{
            position:relative;
            z-index:1;
        }

        .summary-card-top{
            display:flex;
            justify-content:space-between;
            gap:12px;
            align-items:flex-start;
        }

        .summary-card-label{
            margin:0;
            color:var(--ca-muted);
            font-size:13px;
            font-weight:850;
        }

        .summary-card-icon{
            width:40px;
            height:40px;
            border-radius:14px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            background:var(--soft,#eff6ff);
            color:var(--accent,#2563eb);
            font-size:18px;
        }

        .summary-card-value{
            margin:12px 0 0;
            color:var(--ca-ink);
            font-size:34px;
            line-height:1;
            font-weight:950;
            letter-spacing:-.04em;
        }

        .summary-card-note{
            margin:9px 0 0;
            color:var(--ca-muted);
            font-size:13px;
            line-height:1.45;
            font-weight:700;
        }

        .insight-grid{
            display:grid;
            grid-template-columns:repeat(3, 1fr);
            gap:16px;
            margin-top:20px;
        }

        .insight-block{
            border:1px solid var(--ca-line);
            border-radius:20px;
            padding:15px;
            background:linear-gradient(180deg,#ffffff 0%,#fbfdff 100%);
        }

        .insight-block h4{
            margin:0 0 12px;
            color:var(--ca-ink);
            font-size:15px;
            font-weight:950;
            display:flex;
            align-items:center;
            gap:8px;
        }

        .insight-block h4 i{
            color:var(--ca-blue);
        }

        .simple-list{
            display:flex;
            flex-direction:column;
            gap:9px;
        }

        .simple-row{
            display:grid;
            grid-template-columns:minmax(0,1fr) auto;
            gap:12px;
            align-items:center;
            padding:9px 0;
            border-bottom:1px dashed var(--ca-line);
            color:var(--ca-text);
            font-size:14px;
        }

        .simple-row:last-child{border-bottom:0;}

        .simple-row strong{
            color:var(--ca-ink);
            font-weight:900;
            min-width:0;
            overflow:hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
        }

        .simple-count{
            white-space:nowrap;
            color:var(--ca-ink);
            font-weight:950;
            border-radius:999px;
            padding:4px 9px;
            background:#f1f5f9;
        }

        .mini-summary-line{
            display:grid;
            grid-template-columns:repeat(3, minmax(0,1fr));
            gap:12px;
            margin-bottom:16px;
        }

        .mini-summary-item{
            border-radius:18px;
            background:#f8fafc;
            border:1px solid var(--ca-line);
            padding:13px 14px;
        }

        .mini-summary-item span{
            display:block;
            color:var(--ca-muted);
            font-size:12px;
            font-weight:850;
        }

        .mini-summary-item strong{
            display:block;
            color:var(--ca-ink);
            font-size:22px;
            line-height:1;
            font-weight:950;
            margin-top:7px;
        }

        /* =========================
           TABLES
        ========================= */
        .table-wrap{
            width:100%;
            overflow-x:auto;
            -webkit-overflow-scrolling:touch;
            border:1px solid var(--ca-line);
            border-radius:18px;
            background:#fff;
        }

        .analytics-table{
            width:100%;
            min-width:760px;
            border-collapse:collapse;
            margin:0;
        }

        .analytics-table th{
            background:#f8fafc;
            color:var(--ca-ink);
            font-size:13px;
            font-weight:950;
            text-align:left;
            border-bottom:1px solid var(--ca-line-strong);
            padding:12px 11px;
            white-space:nowrap;
        }

        .analytics-table td{
            color:var(--ca-text);
            font-size:14px;
            border-bottom:1px solid #eef2f7;
            padding:12px 11px;
            vertical-align:middle;
            white-space:nowrap;
        }

        .analytics-table tbody tr:last-child td{border-bottom:0;}
        .analytics-table tbody tr:hover{background:#fbfdff;}

        .analytics-table .person-name{
            color:var(--ca-ink);
            font-weight:900;
        }

        .text-center{text-align:center !important;}
        .text-end{text-align:right !important;}

        .status-number{
            display:inline-flex;
            min-width:40px;
            min-height:28px;
            padding:3px 8px;
            align-items:center;
            justify-content:center;
            border-radius:999px;
            background:var(--ca-blue-soft);
            color:var(--ca-blue);
            font-weight:950;
        }

        .status-number.danger{background:var(--ca-rose-soft);color:var(--ca-rose);}
        .status-number.success{background:var(--ca-teal-soft);color:var(--ca-teal);}
        .status-number.warning{background:var(--ca-amber-soft);color:var(--ca-amber);}
        .status-number.purple{background:var(--ca-purple-soft);color:var(--ca-purple);}

        .open-link{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:6px;
            border:1px solid var(--ca-line-strong);
            border-radius:999px;
            padding:5px 11px;
            color:#334155;
            text-decoration:none;
            font-size:13px;
            font-weight:850;
        }

        .open-link:hover{
            color:var(--ca-blue);
            border-color:#93c5fd;
            background:#eff6ff;
            text-decoration:none;
        }

        .empty-line{
            padding:24px 14px;
            border:1px dashed var(--ca-line-strong);
            border-radius:16px;
            text-align:center;
            color:var(--ca-muted);
            font-weight:850;
            background:#fafafa;
        }

        .print-signature{
            display:none;
            margin-top:30px;
            page-break-inside:avoid;
        }

        .signature-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:34px;
        }

        .signature-box{
            padding-top:32px;
            text-align:center;
            color:#111827;
            font-size:13px;
        }

        .signature-line{
            border-top:1px solid #111827;
            width:72%;
            margin:0 auto 8px;
        }


        /* =========================
           RESPONSIVE
        ========================= */
        @media(max-width:1199.98px){
            .report-cover-top{grid-template-columns:1fr;}
            .filter-field,.filter-field.wide{grid-column:span 4;}
            .filter-field.small{grid-column:span 3;}
            .report-section-nav{grid-template-columns:repeat(4, minmax(0,1fr));}
            .overview-layout{grid-template-columns:1fr;}
            .insight-grid{grid-template-columns:1fr;}
        }

        @media(max-width:991.98px){
            .report-title{font-size:24px;}
            .report-section-nav{top:0;grid-template-columns:repeat(2, minmax(0,1fr));}
            .filter-field,.filter-field.wide,.filter-field.small{grid-column:span 6;}
            .summary-grid{grid-template-columns:1fr 1fr;}
        }

        @media(max-width:767.98px){
            .child-analytics-page{padding-top:10px;}
            .report-cover,.filter-area,.report-section{border-radius:20px;padding:16px;}
            .report-title{font-size:21px;}
            .screen-actions .btn,.section-actions .btn{width:100%;}
            .filter-field,.filter-field.wide,.filter-field.small{grid-column:1 / -1;}
            .report-section-nav{grid-template-columns:1fr;}
            .summary-grid,.mini-summary-line{grid-template-columns:1fr;}
            .brief-total-value{font-size:46px;}
        }

        /* =========================
           PRINT: FORMAL GOVERNMENT STYLE
        ========================= */
        @media print{
            @page{
                size:A4 portrait;
                margin:15mm 13mm;
            }

            html,body{
                background:#ffffff !important;
                color:#000000 !important;
                -webkit-print-color-adjust:exact !important;
                print-color-adjust:exact !important;
                font-family:"Sarabun","TH Sarabun New",Tahoma,sans-serif !important;
                font-size:12px !important;
                line-height:1.45 !important;
            }

            .navbar-custom,.leftside-menu,.footer,.screen-actions,.filter-area,.report-section-nav,.section-actions,header,footer{
                display:none !important;
            }

            .content,.container-fluid,.child-analytics-page,.report-shell{
                width:100% !important;
                max-width:100% !important;
                margin:0 !important;
                padding:0 !important;
                background:#ffffff !important;
            }

            .report-cover{
                color:#000 !important;
                background:#fff !important;
                border:0 !important;
                border-radius:0 !important;
                box-shadow:none !important;
                padding:0 0 8px !important;
                margin:0 0 8px !important;
                text-align:center !important;
                border-bottom:2px solid #000 !important;
            }

            .report-cover::before,.report-cover::after{display:none !important;}
            .report-cover-top{display:block !important;}
            .report-kicker{display:none !important;}

            .report-title{
                color:#000 !important;
                font-size:18px !important;
                font-weight:700 !important;
                letter-spacing:0 !important;
                margin:0 !important;
            }

            .report-subtitle{
                display:none !important;
            }

            .report-meta-card{
                display:block !important;
                background:#fff !important;
                border:0 !important;
                padding:5px 0 0 !important;
                margin:0 !important;
                color:#000 !important;
            }

            .report-meta-row{
                display:inline !important;
                border:0 !important;
                padding:0 !important;
                margin:0 8px !important;
                color:#000 !important;
                font-size:11px !important;
                line-height:1.4 !important;
            }

            .report-meta-row i{display:none !important;}
            .report-meta-row strong{display:inline !important;color:#000 !important;font-weight:700 !important;}

            .report-context{
                border:0 !important;
                border-bottom:1px solid #9ca3af !important;
                background:#fff !important;
                padding:0 0 6px !important;
                margin:0 0 10px !important;
                border-radius:0 !important;
                font-size:11px !important;
                line-height:1.45 !important;
                color:#000 !important;
            }

            .report-context i{display:none !important;}

            .report-section{
                display:none !important;
                border:0 !important;
                border-radius:0 !important;
                box-shadow:none !important;
                padding:0 !important;
                margin:0 !important;
                background:#fff !important;
            }

            .report-section.is-active{display:block !important;}

            .section-toolbar{
                display:block !important;
                border-bottom:1.5px solid #000 !important;
                padding-bottom:6px !important;
                margin-bottom:8px !important;
            }

            .section-label{display:none !important;}

            .section-title{
                color:#000 !important;
                font-size:15px !important;
                font-weight:700 !important;
                margin:0 !important;
                text-align:left !important;
            }

            .section-desc{
                color:#000 !important;
                font-size:10.5px !important;
                margin:3px 0 0 !important;
                line-height:1.35 !important;
            }

            .overview-layout{display:block !important;}
            .overview-brief{display:none !important;}
            .summary-grid{display:none !important;}

            .print-overview-table{
                display:table !important;
            }

            .insight-grid{
                display:grid !important;
                grid-template-columns:repeat(3, 1fr) !important;
                gap:8px !important;
                margin-top:0 !important;
            }

            .insight-block{
                border:0 !important;
                border-radius:0 !important;
                background:#fff !important;
                padding:0 !important;
                box-shadow:none !important;
            }

            .insight-block h4{
                font-size:12px !important;
                margin:0 0 5px !important;
                color:#000 !important;
                font-weight:700 !important;
            }

            .insight-block h4 i{display:none !important;}

            .simple-row{
                font-size:10.5px !important;
                padding:3px 0 !important;
                border-bottom:1px dotted #cbd5e1 !important;
                color:#000 !important;
            }

            .simple-row strong{color:#000 !important;white-space:normal !important;}
            .simple-count{background:#fff !important;padding:0 !important;color:#000 !important;}

            .mini-summary-line{display:none !important;}

            .table-wrap{
                border:0 !important;
                border-radius:0 !important;
                overflow:visible !important;
                background:#fff !important;
            }

            .analytics-table{
                min-width:0 !important;
                width:100% !important;
                border-collapse:collapse !important;
                page-break-inside:auto !important;
            }

            .analytics-table thead{display:table-header-group !important;}
            .analytics-table tr{page-break-inside:avoid !important;}

            .analytics-table th{
                background:#fff !important;
                color:#000 !important;
                font-size:10.5px !important;
                font-weight:700 !important;
                border:1px solid #000 !important;
                padding:5px 5px !important;
                white-space:normal !important;
            }

            .analytics-table td{
                color:#000 !important;
                font-size:10.5px !important;
                border:1px solid #000 !important;
                padding:5px 5px !important;
                white-space:normal !important;
            }

            .status-number{
                background:#fff !important;
                border:0 !important;
                color:#000 !important;
                min-height:auto !important;
                min-width:auto !important;
                padding:0 !important;
                border-radius:0 !important;
                font-weight:700 !important;
            }

            .open-link{display:none !important;}
            .empty-line{border:1px solid #000 !important;background:#fff !important;border-radius:0 !important;color:#000 !important;font-size:11px !important;padding:10px !important;}
            .print-signature{display:block !important;}
            a[href]:after{content:"" !important;}
        }
    </style>

    @php
        $thaiStart = \Carbon\Carbon::parse($startDate)->locale('th');
        $thaiEnd = \Carbon\Carbon::parse($endDate)->locale('th');
        $periodText = $thaiStart->translatedFormat('j F') . ' ' . ($thaiStart->year + 543) . ' - ' . $thaiEnd->translatedFormat('j F') . ' ' . ($thaiEnd->year + 543);
        $printedAt = now('Asia/Bangkok')->locale('th')->translatedFormat('j F') . ' ' . (now('Asia/Bangkok')->year + 543) . ' เวลา ' . now('Asia/Bangkok')->format('H:i') . ' น.';

        $topSchools = collect($schoolSummary)->take(5);
        $topEducations = collect($educationSummary)->take(5);
        $topProblems = collect($problemSummary)->take(5);

        $releaseText = match($releaseStatus ?? 'show') {
            'show' => 'กำลังดูแล',
            'hide' => 'จำหน่าย/ซ่อน',
            'pending_refer' => 'รออนุมัติส่งต่อ',
            'all' => 'ทั้งหมด',
            default => $releaseStatus ?? '-',
        };

        $groupCount = count($schoolSummary) + count($educationSummary) + count($problemSummary);
    @endphp

    <div class="content">
        <div class="container-fluid child-analytics-page">
            <div class="report-shell">

              <div class="report-cover">
    <div class="report-cover-inner">

        <div class="report-cover-top">
            <div class="report-cover-title-area">
                <div class="report-kicker">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    ฐานข้อมูลเด็กและสวัสดิการสังคม
                </div>

                <h1 class="report-title">รายงานวิเคราะห์ข้อมูลเด็กตามเงื่อนไข</h1>

                <p class="report-subtitle">
                    แสดงผลรายงานแบบเลือกหัวข้อได้ ตามเงื่อนไขที่กำหนด
                </p>
            </div>

            <div class="screen-actions report-cover-actions">
                <button type="button" class="btn btn-print-main" onclick="window.print()">
                    <i class="bi bi-printer-fill"></i>
                    <span>พิมพ์หัวข้อที่เปิดอยู่</span>
                </button>

                <a href="{{ route('statistics.index') }}" class="btn btn-back-main">
                    <i class="bi bi-speedometer2"></i>
                    <span>กลับ Dashboard</span>
                </a>
            </div>
        </div>

    </div>
</div>

                <div class="filter-area">
                    <div class="filter-header">
                        <div>
                            <h2 class="filter-title">ตัวกรองรายงาน</h2>
                            <p class="filter-desc">เลือกช่วงเวลาและเงื่อนไขเพื่อประมวลผลรายงานเฉพาะกลุ่มที่ต้องการ</p>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('child.analytics.report.index') }}">
                        <div class="filter-grid">
                            <div class="filter-field small">
                                <label class="form-label">วันที่เริ่มต้น</label>
                                <input type="date" name="start_date" class="form-control"
                                       value="{{ request('start_date', $startDate->toDateString()) }}"
                                       max="{{ $today }}">
                            </div>

                            <div class="filter-field small">
                                <label class="form-label">วันที่สิ้นสุด</label>
                                <input type="date" name="end_date" class="form-control"
                                       value="{{ request('end_date', $endDate->toDateString()) }}"
                                       max="{{ $today }}">
                            </div>

                            <div class="filter-field small">
                                <label class="form-label">สถานะ</label>
                                <select name="release_status" class="form-select">
                                    <option value="show" {{ $releaseStatus === 'show' ? 'selected' : '' }}>กำลังดูแล</option>
                                    <option value="hide" {{ $releaseStatus === 'hide' ? 'selected' : '' }}>จำหน่าย/ซ่อน</option>
                                    <option value="pending_refer" {{ $releaseStatus === 'pending_refer' ? 'selected' : '' }}>รออนุมัติส่งต่อ</option>
                                    <option value="all" {{ $releaseStatus === 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                                </select>
                            </div>

                            <div class="filter-field small">
                                <label class="form-label">เพศ</label>
                                <select name="gender" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    <option value="male" {{ $gender === 'male' ? 'selected' : '' }}>ชาย</option>
                                    <option value="female" {{ $gender === 'female' ? 'selected' : '' }}>หญิง</option>
                                </select>
                            </div>

                            <div class="filter-field small">
                                <label class="form-label">อายุต่ำสุด</label>
                                <input type="number" min="0" max="99" name="age_min" class="form-control" value="{{ $ageMin }}">
                            </div>

                            <div class="filter-field small">
                                <label class="form-label">อายุสูงสุด</label>
                                <input type="number" min="0" max="99" name="age_max" class="form-control" value="{{ $ageMax }}">
                            </div>

                            <div class="filter-field">
                                <label class="form-label">บ้าน</label>
                                <select name="house_id" class="form-select">
                                    <option value="all">ทั้งหมด</option>
                                    @foreach ($houses as $house)
                                        <option value="{{ $house->id }}" {{ (string) $houseId === (string) $house->id ? 'selected' : '' }}>
                                            {{ $house->house_name ?? $house->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-field">
                                <label class="form-label">หน่วยงาน / โครงการ</label>
                                <select name="project_id" class="form-select">
                                    <option value="all">ทั้งหมด</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}" {{ (string) $projectId === (string) $project->id ? 'selected' : '' }}>
                                            {{ $project->project_name ?? $project->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-field">
                                <label class="form-label">กลุ่มเป้าหมาย</label>
                                <select name="target_id" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    @foreach ($targets as $target)
                                        <option value="{{ $target->id }}" {{ (string) $targetId === (string) $target->id ? 'selected' : '' }}>
                                            {{ $target->target_name ?? $target->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-field">
                                <label class="form-label">สภาพปัญหา</label>
                                <select name="problem_id" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    @foreach ($problems as $problem)
                                        <option value="{{ $problem->id }}" {{ (string) $problemId === (string) $problem->id ? 'selected' : '' }}>
                                            {{ $problem->problem_name ?? $problem->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-field">
                                <label class="form-label">ระดับชั้นเริ่มต้น</label>
                                <select name="education_start" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    @foreach ($educations as $education)
                                        <option value="{{ $education->id }}" {{ (string) $educationStart === (string) $education->id ? 'selected' : '' }}>
                                            {{ $education->education_name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-field">
                                <label class="form-label">ระดับชั้นสิ้นสุด</label>
                                <select name="education_end" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    @foreach ($educations as $education)
                                        <option value="{{ $education->id }}" {{ (string) $educationEnd === (string) $education->id ? 'selected' : '' }}>
                                            {{ $education->education_name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-field wide">
                                <label class="form-label">โรงเรียน / สถานศึกษา</label>
                                <select name="institution_id" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    @foreach ($institutions as $institution)
                                        <option value="{{ $institution->id }}" {{ (string) $institutionId === (string) $institution->id ? 'selected' : '' }}>
                                            {{ $institution->institution_name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-field full">
                                <div class="filter-actions">
                                    <button type="submit" class="btn btn-process">
                                        <i class="bi bi-search"></i>
                                        ประมวลผลรายงาน
                                    </button>

                                    <a href="{{ route('child.analytics.report.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                        ล้างตัวกรอง
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="report-context">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>
                        <strong>ช่วงรายงาน:</strong> {{ $periodText }}
                        <br>
                        จำนวนการขาดเรียนและการเจ็บป่วยนับจากวันที่เกิดรายการจริง ส่วนการจัดกลุ่มโรงเรียน ระดับชั้น และสภาพปัญหา นับจากเด็กที่ตรงตามเงื่อนไขตัวกรองปัจจุบัน
                    </div>
                </div>

                <div class="report-section-nav" aria-label="เลือกหัวข้อรายงาน">
                    <button type="button" class="report-section-btn is-active" data-target="section-overview"><i class="bi bi-speedometer2"></i> ภาพรวม</button>
                    <button type="button" class="report-section-btn" data-target="section-absent"><i class="bi bi-calendar-x"></i> ขาดเรียน</button>
                    <button type="button" class="report-section-btn" data-target="section-medical"><i class="bi bi-heart-pulse"></i> เจ็บป่วย</button>
                    <button type="button" class="report-section-btn" data-target="section-school"><i class="bi bi-building"></i> โรงเรียน</button>
                    <button type="button" class="report-section-btn" data-target="section-education"><i class="bi bi-mortarboard"></i> ระดับชั้น</button>
                    <button type="button" class="report-section-btn" data-target="section-problem"><i class="bi bi-exclamation-triangle"></i> สภาพปัญหา</button>
                    <button type="button" class="report-section-btn" data-target="section-disease"><i class="bi bi-clipboard2-pulse"></i> โรค/อาการ</button>
                </div>

                <section id="section-overview" class="report-section is-active">
                    <div class="section-toolbar">
                        <div>
                            <p class="section-label">Report Overview</p>
                            <h2 class="section-title">ภาพรวมรายงาน</h2>
                            <p class="section-desc">สรุปจำนวนเด็ก การขาดเรียน การเจ็บป่วย และกลุ่มข้อมูลสำคัญตามเงื่อนไขที่เลือก</p>
                        </div>
                        <div class="section-actions">
                            <button type="button" class="btn section-print-btn" onclick="window.print()"><i class="bi bi-printer"></i> พิมพ์ส่วนนี้</button>
                        </div>
                    </div>

                    <table class="analytics-table print-overview-table" style="display:none;margin-bottom:10px;">
                        <thead>
                            <tr>
                                <th>รายการ</th>
                                <th class="text-center">จำนวน</th>
                                <th>หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>เด็กตามเงื่อนไข</td><td class="text-center">{{ number_format($totalClients) }}</td><td>ชาย {{ number_format($maleCount) }} / หญิง {{ number_format($femaleCount) }}</td></tr>
                            <tr><td>จำนวนการขาดเรียน</td><td class="text-center">{{ number_format($absentTotalRecords) }}</td><td>เด็กที่ขาดเรียน {{ number_format($absentTotalChildren) }} คน</td></tr>
                            <tr><td>จำนวนการเจ็บป่วย</td><td class="text-center">{{ number_format($medicalTotalRecords) }}</td><td>เด็กที่มีประวัติป่วย {{ number_format($medicalTotalChildren) }} คน</td></tr>
                            <tr><td>กลุ่มข้อมูล</td><td class="text-center">{{ number_format($groupCount) }}</td><td>โรงเรียน / ระดับชั้น / สภาพปัญหา</td></tr>
                        </tbody>
                    </table>

                    <div class="overview-layout">
                        <div class="overview-brief">
                            <div>
                                <div class="brief-caption"><i class="bi bi-stars"></i> Executive Summary</div>
                                <div class="brief-total">
                                    <p class="brief-total-label">จำนวนเด็กตามเงื่อนไข</p>
                                    <div class="brief-total-value">{{ number_format($totalClients) }}</div>
                                    <p class="brief-total-note">
                                        สรุปจากผู้รับบริการที่ตรงตามตัวกรองปัจจุบัน ครอบคลุมช่วงวันที่ {{ $periodText }}
                                    </p>
                                </div>
                            </div>

                            <div class="brief-chips">
                                <span class="brief-chip"><i class="bi bi-gender-male"></i> ชาย {{ number_format($maleCount) }}</span>
                                <span class="brief-chip"><i class="bi bi-gender-female"></i> หญิง {{ number_format($femaleCount) }}</span>
                                <span class="brief-chip"><i class="bi bi-person-check"></i> {{ $releaseText }}</span>
                            </div>
                        </div>

                        <div class="summary-grid">
                            <div class="summary-card blue">
                                <div class="summary-card-content">
                                    <div class="summary-card-top">
                                        <p class="summary-card-label">เด็กตามเงื่อนไข</p>
                                        <span class="summary-card-icon"><i class="bi bi-people-fill"></i></span>
                                    </div>
                                    <div class="summary-card-value">{{ number_format($totalClients) }}</div>
                                    <p class="summary-card-note">ชาย {{ number_format($maleCount) }} / หญิง {{ number_format($femaleCount) }}</p>
                                </div>
                            </div>

                            <div class="summary-card rose">
                                <div class="summary-card-content">
                                    <div class="summary-card-top">
                                        <p class="summary-card-label">จำนวนการขาดเรียน</p>
                                        <span class="summary-card-icon"><i class="bi bi-calendar-x-fill"></i></span>
                                    </div>
                                    <div class="summary-card-value">{{ number_format($absentTotalRecords) }}</div>
                                    <p class="summary-card-note">เด็กที่ขาดเรียน {{ number_format($absentTotalChildren) }} คน</p>
                                </div>
                            </div>

                            <div class="summary-card teal">
                                <div class="summary-card-content">
                                    <div class="summary-card-top">
                                        <p class="summary-card-label">จำนวนการเจ็บป่วย</p>
                                        <span class="summary-card-icon"><i class="bi bi-heart-pulse-fill"></i></span>
                                    </div>
                                    <div class="summary-card-value">{{ number_format($medicalTotalRecords) }}</div>
                                    <p class="summary-card-note">เด็กที่มีประวัติป่วย {{ number_format($medicalTotalChildren) }} คน</p>
                                </div>
                            </div>

                            <div class="summary-card purple">
                                <div class="summary-card-content">
                                    <div class="summary-card-top">
                                        <p class="summary-card-label">กลุ่มข้อมูล</p>
                                        <span class="summary-card-icon"><i class="bi bi-diagram-3-fill"></i></span>
                                    </div>
                                    <div class="summary-card-value">{{ number_format($groupCount) }}</div>
                                    <p class="summary-card-note">โรงเรียน / ระดับชั้น / สภาพปัญหา</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="insight-grid">
                        <div class="insight-block">
                            <h4><i class="bi bi-building"></i> โรงเรียนสูงสุด 5 อันดับ</h4>
                            @if($topSchools->isNotEmpty())
                                <div class="simple-list">
                                    @foreach($topSchools as $name => $count)
                                        <div class="simple-row"><strong>{{ $name }}</strong><span class="simple-count">{{ number_format($count) }} คน</span></div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-line">ไม่พบข้อมูลโรงเรียน</div>
                            @endif
                        </div>

                        <div class="insight-block">
                            <h4><i class="bi bi-mortarboard"></i> ระดับชั้นสูงสุด 5 อันดับ</h4>
                            @if($topEducations->isNotEmpty())
                                <div class="simple-list">
                                    @foreach($topEducations as $name => $count)
                                        <div class="simple-row"><strong>{{ $name }}</strong><span class="simple-count">{{ number_format($count) }} คน</span></div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-line">ไม่พบข้อมูลระดับชั้น</div>
                            @endif
                        </div>

                        <div class="insight-block">
                            <h4><i class="bi bi-exclamation-triangle"></i> สภาพปัญหาสูงสุด 5 อันดับ</h4>
                            @if($topProblems->isNotEmpty())
                                <div class="simple-list">
                                    @foreach($topProblems as $name => $count)
                                        <div class="simple-row"><strong>{{ $name }}</strong><span class="simple-count">{{ number_format($count) }} คน</span></div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-line">ไม่พบข้อมูลสภาพปัญหา</div>
                            @endif
                        </div>
                    </div>
                </section>

                <section id="section-absent" class="report-section">
                    <div class="section-toolbar">
                        <div>
                            <p class="section-label">Attendance Report</p>
                            <h2 class="section-title">รายงานการขาดเรียนรายบุคคล</h2>
                            <p class="section-desc">แสดงชื่อ-สกุล จำนวนครั้ง และวันที่ขาดเรียนล่าสุดตามช่วงเวลาที่เลือก</p>
                        </div>
                        <div class="section-actions"><button type="button" class="btn section-print-btn" onclick="window.print()"><i class="bi bi-printer"></i> พิมพ์ส่วนนี้</button></div>
                    </div>

                    <div class="mini-summary-line">
                        <div class="mini-summary-item"><span>จำนวนการขาดเรียน</span><strong>{{ number_format($absentTotalRecords) }}</strong></div>
                        <div class="mini-summary-item"><span>เด็กที่ขาดเรียน</span><strong>{{ number_format($absentTotalChildren) }}</strong></div>
                        <div class="mini-summary-item"><span>ช่วงรายงาน</span><strong style="font-size:15px;line-height:1.3;">{{ $periodText }}</strong></div>
                    </div>

                    @if ($absentByClient->isNotEmpty())
                        <div class="table-wrap">
                            <table class="analytics-table">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:70px;">ลำดับ</th>
                                        <th>ชื่อ-สกุล</th>
                                        <th class="text-center">อายุ</th>
                                        <th class="text-center">จำนวนครั้ง</th>
                                        <th class="text-center">วันที่ล่าสุด</th>
                                        <th class="text-center">ดูข้อมูล</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($absentByClient as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="person-name">{{ $item['fullname'] }}</td>
                                            <td class="text-center">{{ $item['age'] !== null ? $item['age'] . ' ปี' : '-' }}</td>
                                            <td class="text-center"><span class="status-number danger">{{ number_format($item['total_count']) }}</span></td>
                                            <td class="text-center">{{ $item['latest_date'] ? \Carbon\Carbon::parse($item['latest_date'])->format('d/m/Y') : '-' }}</td>
                                            <td class="text-center">
                                                @if ($item['url'])
                                                    <a href="{{ $item['url'] }}" class="open-link"><i class="bi bi-box-arrow-up-right"></i> เปิด</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-line">ไม่พบข้อมูลการขาดเรียนในช่วงเวลานี้</div>
                    @endif
                </section>

                <section id="section-medical" class="report-section">
                    <div class="section-toolbar">
                        <div>
                            <p class="section-label">Medical Report</p>
                            <h2 class="section-title">รายงานการเจ็บป่วยรายบุคคล</h2>
                            <p class="section-desc">แสดงชื่อ-สกุล จำนวนครั้ง และวันที่รักษาล่าสุดตามช่วงเวลาที่เลือก</p>
                        </div>
                        <div class="section-actions"><button type="button" class="btn section-print-btn" onclick="window.print()"><i class="bi bi-printer"></i> พิมพ์ส่วนนี้</button></div>
                    </div>

                    <div class="mini-summary-line">
                        <div class="mini-summary-item"><span>จำนวนการเจ็บป่วย</span><strong>{{ number_format($medicalTotalRecords) }}</strong></div>
                        <div class="mini-summary-item"><span>เด็กที่มีประวัติป่วย</span><strong>{{ number_format($medicalTotalChildren) }}</strong></div>
                        <div class="mini-summary-item"><span>ช่วงรายงาน</span><strong style="font-size:15px;line-height:1.3;">{{ $periodText }}</strong></div>
                    </div>

                    @if ($medicalByClient->isNotEmpty())
                        <div class="table-wrap">
                            <table class="analytics-table">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:70px;">ลำดับ</th>
                                        <th>ชื่อ-สกุล</th>
                                        <th class="text-center">อายุ</th>
                                        <th class="text-center">จำนวนครั้ง</th>
                                        <th class="text-center">วันที่ล่าสุด</th>
                                        <th class="text-center">ดูข้อมูล</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($medicalByClient as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="person-name">{{ $item['fullname'] }}</td>
                                            <td class="text-center">{{ $item['age'] !== null ? $item['age'] . ' ปี' : '-' }}</td>
                                            <td class="text-center"><span class="status-number success">{{ number_format($item['total_count']) }}</span></td>
                                            <td class="text-center">{{ $item['latest_date'] ? \Carbon\Carbon::parse($item['latest_date'])->format('d/m/Y') : '-' }}</td>
                                            <td class="text-center">
                                                @if ($item['url'])
                                                    <a href="{{ $item['url'] }}" class="open-link"><i class="bi bi-box-arrow-up-right"></i> เปิด</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-line">ไม่พบข้อมูลการเจ็บป่วยในช่วงเวลานี้</div>
                    @endif
                </section>

                <section id="section-school" class="report-section">
                    <div class="section-toolbar">
                        <div>
                            <p class="section-label">School Summary</p>
                            <h2 class="section-title">กรุปรายโรงเรียน</h2>
                            <p class="section-desc">จำนวนเด็กแยกตามโรงเรียนหรือสถานศึกษาล่าสุด</p>
                        </div>
                        <div class="section-actions"><button type="button" class="btn section-print-btn" onclick="window.print()"><i class="bi bi-printer"></i> พิมพ์ส่วนนี้</button></div>
                    </div>

                    @if (!empty($schoolSummary))
                        <div class="table-wrap">
                            <table class="analytics-table">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:70px;">ลำดับ</th>
                                        <th>โรงเรียน / สถานศึกษา</th>
                                        <th class="text-end">จำนวนเด็ก</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($schoolSummary as $name => $count)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="person-name">{{ $name }}</td>
                                            <td class="text-end"><span class="status-number success">{{ number_format($count) }}</span> คน</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-line">ไม่พบข้อมูลโรงเรียน</div>
                    @endif
                </section>

                <section id="section-education" class="report-section">
                    <div class="section-toolbar">
                        <div>
                            <p class="section-label">Education Summary</p>
                            <h2 class="section-title">กรุประดับชั้นเรียน</h2>
                            <p class="section-desc">จำนวนเด็กแยกตามระดับการศึกษาล่าสุด ไม่แสดงรายชื่อรายบุคคล</p>
                        </div>
                        <div class="section-actions"><button type="button" class="btn section-print-btn" onclick="window.print()"><i class="bi bi-printer"></i> พิมพ์ส่วนนี้</button></div>
                    </div>

                    @if (!empty($educationSummary))
                        <div class="table-wrap">
                            <table class="analytics-table">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:70px;">ลำดับ</th>
                                        <th>ระดับชั้นเรียน</th>
                                        <th class="text-end">จำนวนเด็ก</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($educationSummary as $name => $count)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="person-name">{{ $name }}</td>
                                            <td class="text-end"><span class="status-number purple">{{ number_format($count) }}</span> คน</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-line">ไม่พบข้อมูลระดับชั้น</div>
                    @endif
                </section>

                <section id="section-problem" class="report-section">
                    <div class="section-toolbar">
                        <div>
                            <p class="section-label">Problem Summary</p>
                            <h2 class="section-title">กรุปสภาพปัญหา</h2>
                            <p class="section-desc">จำนวนเด็กแยกตามสภาพปัญหาที่บันทึกไว้ในระบบ</p>
                        </div>
                        <div class="section-actions"><button type="button" class="btn section-print-btn" onclick="window.print()"><i class="bi bi-printer"></i> พิมพ์ส่วนนี้</button></div>
                    </div>

                    @if (!empty($problemSummary))
                        <div class="table-wrap">
                            <table class="analytics-table">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:70px;">ลำดับ</th>
                                        <th>สภาพปัญหา</th>
                                        <th class="text-end">จำนวนเด็ก</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($problemSummary as $name => $count)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="person-name">{{ $name }}</td>
                                            <td class="text-end"><span class="status-number warning">{{ number_format($count) }}</span> คน</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-line">ไม่พบข้อมูลสภาพปัญหา</div>
                    @endif
                </section>

                <section id="section-disease" class="report-section">
                    <div class="section-toolbar">
                        <div>
                            <p class="section-label">Disease Summary</p>
                            <h2 class="section-title">กลุ่มโรค/อาการที่พบ</h2>
                            <p class="section-desc">จัดกลุ่มจากชื่อโรคในข้อมูลการรักษาพยาบาลตามช่วงวันที่รายงาน</p>
                        </div>
                        <div class="section-actions"><button type="button" class="btn section-print-btn" onclick="window.print()"><i class="bi bi-printer"></i> พิมพ์ส่วนนี้</button></div>
                    </div>

                    @if ($medicalDiseaseSummary->isNotEmpty())
                        <div class="table-wrap">
                            <table class="analytics-table">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:70px;">ลำดับ</th>
                                        <th>โรค / อาการ</th>
                                        <th class="text-end">จำนวนครั้ง</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($medicalDiseaseSummary as $disease)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="person-name">{{ $disease->name }}</td>
                                            <td class="text-end"><span class="status-number success">{{ number_format($disease->total_count) }}</span> ครั้ง</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-line">ไม่พบข้อมูลโรค/อาการในช่วงเวลานี้</div>
                    @endif
                </section>

                <div class="print-signature">
                    <div class="signature-grid">
                        <div class="signature-box">
                            <div class="signature-line"></div>
                            ผู้จัดทำรายงาน
                        </div>
                        <div class="signature-box">
                            <div class="signature-line"></div>
                            ผู้ตรวจสอบรายงาน
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.report-section-btn');
            const sections = document.querySelectorAll('.report-section');

            function activateSection(targetId, shouldScroll = true) {
                const targetSection = document.getElementById(targetId);

                if (!targetSection) {
                    return;
                }

                buttons.forEach(function (button) {
                    button.classList.toggle('is-active', button.getAttribute('data-target') === targetId);
                });

                sections.forEach(function (section) {
                    section.classList.toggle('is-active', section.id === targetId);
                });

                history.replaceState(null, '', '#' + targetId.replace('section-', ''));

                if (shouldScroll) {
                    const top = targetSection.getBoundingClientRect().top + window.pageYOffset - 104;
                    window.scrollTo({
                        top: Math.max(top, 0),
                        behavior: 'smooth'
                    });
                }
            }

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    activateSection(button.getAttribute('data-target'));
                });
            });

            const hash = window.location.hash ? window.location.hash.replace('#', '') : '';
            const initialSection = hash ? 'section-' + hash : 'section-overview';

            if (document.getElementById(initialSection)) {
                activateSection(initialSection, false);
            }
        });
    </script>
@endpush