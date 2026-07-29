@extends('admin.admin_master')

@section('admin')

@php
    /*
    |--------------------------------------------------------------------------
    | ตัวเลือกระดับการศึกษา
    |--------------------------------------------------------------------------
    | ใช้ร่วมกับฟอร์ม “ยื่นคำขอทุนรอบใหม่”
    | ให้ตรงกับรายการตัวเลือกในหน้าเพิ่ม/แก้ไขข้อมูล
    */
    $scholarshipEducationLevels = [
        'เตรียมอนุบาล',
        'อนุบาล 1',
        'อนุบาล 2',
        'อนุบาล 3',
        'ประถมศึกษาปีที่ 1',
        'ประถมศึกษาปีที่ 2',
        'ประถมศึกษาปีที่ 3',
        'ประถมศึกษาปีที่ 4',
        'ประถมศึกษาปีที่ 5',
        'ประถมศึกษาปีที่ 6',
        'มัธยมศึกษาปีที่ 1',
        'มัธยมศึกษาปีที่ 2',
        'มัธยมศึกษาปีที่ 3',
        'มัธยมศึกษาปีที่ 4',
        'มัธยมศึกษาปีที่ 5',
        'มัธยมศึกษาปีที่ 6',
        'ประกาศนียบัตรวิชาชีพ 1 (ปวช.)',
        'ประกาศนียบัตรวิชาชีพ 2 (ปวช.)',
        'ประกาศนียบัตรวิชาชีพ 3 (ปวช.)',
        'ประกาศนียบัตรวิชาชีพชั้นสูง 1 (ปวส.)',
        'ประกาศนียบัตรวิชาชีพชั้นสูง 2 (ปวส.)',
        'อนุปริญญา',
        'ปริญญาตรีชั้นปีที่ 1',
        'ปริญญาตรีชั้นปีที่ 2',
        'ปริญญาตรีชั้นปีที่ 3',
        'ปริญญาตรีชั้นปีที่ 4',
        'สูงกว่าปริญญาตรี',
        'การศึกษานอกระบบระดับประถมศึกษา',
        'การศึกษานอกระบบระดับมัธยมศึกษาตอนต้น',
        'การศึกษานอกระบบระดับมัธยมศึกษาตอนปลาย',
        'อื่น ๆ',
    ];
@endphp

<style>
    :root{
        --sc-primary:#3157d5;
        --sc-primary-dark:#2446b8;
        --sc-primary-soft:#eef3ff;
        --sc-border:#e4e9f2;
        --sc-text:#172033;
        --sc-muted:#667085;
        --sc-success:#067647;
        --sc-success-hover:#05603a;
        --sc-success-soft:#ecfdf3;
        --sc-warning:#b54708;
        --sc-warning-soft:#fffaeb;
        --sc-danger:#b42318;
        --sc-danger-soft:#fef3f2;
        --sc-surface:#ffffff;
        --sc-page-bg:#f6f8fc;
    }

    *{
        box-sizing:border-box;
    }

    .sc-page{
        padding:20px;
        background:var(--sc-page-bg);
        min-height:100vh;
    }

    /* =========================================================
       Header
    ========================================================= */
    .sc-header{
        background:
            radial-gradient(circle at top right, rgba(49,87,213,.14), transparent 35%),
            linear-gradient(135deg,#ffffff,#f3f6ff);
        border:1px solid #dfe6f4;
        border-radius:22px;
        padding:24px;
        margin-bottom:18px;
        box-shadow:0 12px 34px rgba(16,24,40,.06);
    }

    .sc-header-row{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:18px;
        flex-wrap:wrap;
    }

    .sc-header h4{
        margin:0;
        color:#1d3b8f;
        font-size:20px;
        font-weight:800;
        letter-spacing:.1px;
    }

    .sc-header p{
        margin:8px 0 0;
        color:var(--sc-muted);
        font-size:14px;
        font-weight:500;
    }

    /* =========================================================
       Summary
    ========================================================= */
    .sc-summary-grid{
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:12px;
        margin-bottom:18px;
    }

    .sc-summary-item{
        min-width:0;
        display:flex;
        align-items:center;
        gap:12px;
        padding:16px;
        background:var(--sc-surface);
        border:1px solid var(--sc-border);
        border-radius:16px;
    }

    .sc-summary-icon{
        width:42px;
        height:42px;
        flex:0 0 42px;
        display:flex;
        align-items:center;
        justify-content:center;
        border-radius:13px;
        font-size:18px;
    }

    .sc-summary-item.total .sc-summary-icon{
        background:var(--sc-primary-soft);
        color:var(--sc-primary);
    }

    .sc-summary-item.pending .sc-summary-icon{
        background:var(--sc-warning-soft);
        color:var(--sc-warning);
    }

    .sc-summary-item.approved .sc-summary-icon{
        background:var(--sc-success-soft);
        color:var(--sc-success);
    }

    .sc-summary-item.rejected .sc-summary-icon{
        background:var(--sc-danger-soft);
        color:var(--sc-danger);
    }

    .sc-summary-label{
        color:var(--sc-muted);
        font-size:13px;
        font-weight:700;
    }

    .sc-summary-value{
        margin-top:3px;
        color:var(--sc-text);
        font-size:24px;
        line-height:1.1;
        font-weight:800;
    }

    .sc-summary-detail{
        margin-top:4px;
        color:#7a8496;
        font-size:11.5px;
        font-weight:600;
    }

    /* =========================================================
       Main card and filters
    ========================================================= */
    .sc-card{
        padding:18px;
        background:var(--sc-surface);
        border:1px solid var(--sc-border);
        border-radius:20px;
        box-shadow:0 12px 30px rgba(16,24,40,.05);
    }

    .sc-toolbar{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        flex-wrap:wrap;
        margin-bottom:16px;
        padding-bottom:14px;
        border-bottom:1px solid #edf0f5;
    }

    .sc-filter{
        min-width:0;
        flex:1;
        display:flex;
        align-items:center;
        gap:10px;
        flex-wrap:nowrap;
        overflow-x:auto;
        padding-bottom:3px;
        scrollbar-width:thin;
    }

    .sc-filter input{
        min-width:240px;
        flex:1;
    }

    .sc-filter select{
        width:205px;
        min-width:205px;
        flex:0 0 205px;
    }

    .sc-filter .btn,
    .sc-add-btn{
        white-space:nowrap;
        flex-shrink:0;
    }

    /* =========================================================
       Table
    ========================================================= */
    .sc-table-wrap{
        width:100%;
        overflow-x:auto;
        border:1px solid var(--sc-border);
        border-radius:16px;
        scrollbar-width:thin;
        -webkit-overflow-scrolling:touch;
    }

    .sc-table{
        width:100%;
        min-width:1420px;
        margin-bottom:0;
        vertical-align:middle;
    }

    .sc-table thead th{
        padding:14px 12px;
        background:#f8faff;
        color:#2d4385;
        border-bottom:1px solid #dfe6f4;
        font-size:13px;
        font-weight:800;
        white-space:nowrap;
        vertical-align:middle;
    }

    .sc-table tbody td{
        padding:13px 12px;
        color:#344054;
        border-color:#edf0f5;
        font-size:14px;
        vertical-align:middle;
    }

    .sc-table tbody tr{
        transition:background-color .18s ease;
    }

    .sc-table tbody tr:hover{
        background:#fbfcff;
    }

    .sc-name{
        color:#172033;
        font-weight:800;
    }

    .sc-subtext{
        margin-top:3px;
        color:#7a8496;
        font-size:13px;
    }

    .sc-photo{
        width:54px;
        height:54px;
        object-fit:cover;
        border:1px solid #dfe6f4;
        border-radius:14px;
    }

    .sc-avatar{
        width:54px;
        height:54px;
        display:flex;
        align-items:center;
        justify-content:center;
        background:#eef3ff;
        color:#3157d5;
        border-radius:14px;
        font-weight:800;
    }

    .sc-year-badge{
        display:inline-flex;
        align-items:center;
        padding:6px 10px;
        background:#f2f4f7;
        color:#344054;
        border:1px solid #e4e7ec;
        border-radius:999px;
        font-size:13px;
        font-weight:800;
        white-space:nowrap;
    }

    /* =========================================================
       Status
    ========================================================= */
    .sc-status-badge{
        display:inline-flex;
        align-items:center;
        gap:6px;
        margin-bottom:7px;
        padding:7px 10px;
        border-radius:999px;
        font-size:12px;
        font-weight:800;
        line-height:1.2;
        white-space:nowrap;
    }

    .sc-status-pending{
        background:var(--sc-warning-soft);
        color:var(--sc-warning);
        border:1px solid #fedf89;
    }

    .sc-status-approved{
        background:var(--sc-success-soft);
        color:var(--sc-success);
        border:1px solid #abefc6;
    }

    .sc-status-rejected{
        background:var(--sc-danger-soft);
        color:var(--sc-danger);
        border:1px solid #fecdca;
    }

    .sc-status-select{
        width:225px;
        min-width:225px;
        border-radius:10px;
        font-size:13px;
    }

    /* =========================================================
       Financial overview
    ========================================================= */
    .sc-finance-overview{
        display:grid;
        grid-template-columns:minmax(260px,330px) minmax(0,1fr);
        gap:14px;
        margin-bottom:18px;
    }

    .sc-finance-total-card{
        min-width:0;
        display:flex;
        flex-direction:column;
        justify-content:space-between;
        gap:18px;
        padding:20px;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.24), transparent 36%),
            linear-gradient(135deg,#1748c7,#3157d5);
        color:#ffffff;
        border:1px solid rgba(49,87,213,.35);
        border-radius:18px;
        box-shadow:0 14px 32px rgba(49,87,213,.16);
    }

    .sc-finance-total-top{
        display:flex;
        align-items:center;
        gap:11px;
    }

    .sc-finance-total-icon{
        width:44px;
        height:44px;
        flex:0 0 44px;
        display:flex;
        align-items:center;
        justify-content:center;
        background:rgba(255,255,255,.15);
        border:1px solid rgba(255,255,255,.22);
        border-radius:13px;
        font-size:19px;
    }

    .sc-finance-total-label{
        color:rgba(255,255,255,.82);
        font-size:13px;
        font-weight:700;
    }

    .sc-finance-total-value{
        margin-top:5px;
        font-size:30px;
        line-height:1.1;
        font-weight:800;
        overflow-wrap:anywhere;
    }

    .sc-finance-total-value small{
        font-size:13px;
        font-weight:700;
    }

    .sc-finance-total-meta{
        display:grid;
        grid-template-columns:repeat(3,minmax(0,1fr));
        gap:8px;
    }

    .sc-finance-total-meta-item{
        min-width:0;
        padding:10px;
        background:rgba(255,255,255,.11);
        border:1px solid rgba(255,255,255,.16);
        border-radius:11px;
    }

    .sc-finance-total-meta-label{
        color:rgba(255,255,255,.72);
        font-size:11px;
        font-weight:600;
    }

    .sc-finance-total-meta-value{
        margin-top:3px;
        color:#ffffff;
        font-size:16px;
        font-weight:800;
    }

    .sc-finance-years-panel{
        min-width:0;
        padding:18px;
        background:#ffffff;
        border:1px solid var(--sc-border);
        border-radius:18px;
    }

    .sc-finance-years-header{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
        margin-bottom:14px;
    }

    .sc-finance-years-title{
        margin:0;
        color:#243b7d;
        font-size:15px;
        font-weight:800;
    }

    .sc-finance-years-note{
        margin-top:4px;
        color:#7a8496;
        font-size:12px;
    }

    .sc-finance-year-list{
        display:grid;
        grid-template-columns:repeat(3,minmax(190px,1fr));
        gap:10px;
    }

    .sc-finance-year-card{
        min-width:0;
        padding:14px;
        background:#fafbfc;
        border:1px solid #e8ecf2;
        border-radius:14px;
        transition:
            transform .18s ease,
            border-color .18s ease,
            box-shadow .18s ease;
    }

    .sc-finance-year-card:hover{
        transform:translateY(-1px);
        border-color:#cbd7fa;
        box-shadow:0 7px 18px rgba(49,87,213,.08);
    }

    .sc-finance-year-card.is-active{
        background:#f3f6ff;
        border-color:#9fb2f4;
        box-shadow:0 0 0 3px rgba(49,87,213,.08);
    }

    .sc-finance-year-top{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;
        margin-bottom:9px;
    }

    .sc-finance-year-name{
        color:#3157d5;
        font-size:13px;
        font-weight:800;
    }

    .sc-finance-year-count{
        padding:4px 7px;
        background:#eef3ff;
        color:#3157d5;
        border-radius:999px;
        font-size:11px;
        font-weight:800;
        white-space:nowrap;
    }

    .sc-finance-year-amount{
        color:#172033;
        font-size:21px;
        line-height:1.2;
        font-weight:800;
        overflow-wrap:anywhere;
    }

    .sc-finance-year-amount small{
        color:#667085;
        font-size:12px;
        font-weight:700;
    }

    .sc-finance-year-meta{
        display:flex;
        align-items:center;
        gap:10px;
        flex-wrap:wrap;
        margin-top:8px;
        color:#667085;
        font-size:11.5px;
    }

    .sc-finance-empty{
        min-height:132px;
        display:flex;
        align-items:center;
        justify-content:center;
        gap:10px;
        padding:20px;
        color:#7a8496;
        background:#fafbfc;
        border:1px dashed #cfd6e2;
        border-radius:14px;
        text-align:center;
    }

    /* =========================================================
       Action column
    ========================================================= */
    .sc-action-heading{
        width:336px;
        min-width:336px;
        text-align:center;
    }

    .sc-action-cell{
        width:336px;
        min-width:336px;
    }

    .sc-action-panel{
        width:100%;
        display:flex;
        align-items:center;
        justify-content:center;
        gap:8px;
    }

    .sc-expense-action{
        min-height:40px;
        min-width:132px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:7px;
        padding:0 14px;
        background:#18a779;
        color:#ffffff;
        border:1px solid #12956c;
        border-radius:11px;
        box-shadow:0 5px 12px rgba(6,118,71,.14);
        font-size:12.5px;
        font-weight:800;
        line-height:1;
        white-space:nowrap;
        transition:
            transform .18s ease,
            box-shadow .18s ease,
            background-color .18s ease,
            border-color .18s ease;
    }

    .sc-expense-action:hover,
    .sc-expense-action:focus{
        background:#078a5e;
        color:#ffffff;
        border-color:#078a5e;
        transform:translateY(-1px);
        box-shadow:0 7px 16px rgba(6,118,71,.22);
    }

    .sc-expense-label-short{
        display:none;
    }

    .sc-icon-actions{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:6px;
        flex:0 0 auto;
    }

    .sc-action-icon{
        width:40px;
        height:40px;
        min-width:40px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:0;
        border:1px solid transparent;
        border-radius:11px;
        box-shadow:none;
        font-size:15px;
        transition:
            transform .18s ease,
            box-shadow .18s ease,
            background-color .18s ease,
            border-color .18s ease;
    }

    .sc-action-icon:hover,
    .sc-action-icon:focus{
        transform:translateY(-1px);
    }

    .sc-action-reapply{
        background:#eef3ff;
        color:#3157d5;
        border-color:#cfdaf8;
    }

    .sc-action-reapply:hover,
    .sc-action-reapply:focus{
        background:#dfe7ff;
        color:#2446b8;
        border-color:#aebff3;
        box-shadow:0 5px 12px rgba(49,87,213,.14);
    }

    .sc-action-view{
        background:#eaf7ff;
        color:#1677a8;
        border-color:#c8eaff;
    }

    .sc-action-view:hover,
    .sc-action-view:focus{
        background:#d6efff;
        color:#0e658f;
        border-color:#9edcff;
        box-shadow:0 5px 12px rgba(14,116,167,.12);
    }

    .sc-action-edit{
        background:#fff6e8;
        color:#c26a12;
        border-color:#ffe1b8;
    }

    .sc-action-edit:hover,
    .sc-action-edit:focus{
        background:#ffedd2;
        color:#a65308;
        border-color:#ffc97d;
        box-shadow:0 5px 12px rgba(194,106,18,.12);
    }

    .sc-action-delete{
        background:#fff0f1;
        color:#d54755;
        border-color:#ffd0d5;
    }

    .sc-action-delete:hover,
    .sc-action-delete:focus{
        background:#ffe0e3;
        color:#bd2636;
        border-color:#ffb1ba;
        box-shadow:0 5px 12px rgba(213,71,85,.12);
    }

    .sc-action-form{
        display:inline-flex;
        margin:0;
    }

    /* =========================================================
       Empty state and shared form styles
    ========================================================= */
    .sc-empty{
        padding:42px 20px;
        text-align:center;
        color:#667085;
        background:#fafbfc;
        border:1px dashed #cfd6e2;
        border-radius:18px;
    }

    .btn{
        border-radius:11px;
        font-weight:700;
    }

    .btn-sm{
        border-radius:9px;
    }

    .form-label{
        color:#344054;
        font-size:14px;
        font-weight:700;
    }

    .form-control,
    .form-select{
        border-color:#d0d5dd;
        border-radius:11px;
    }

    .form-control:focus,
    .form-select:focus{
        border-color:#6b83df;
        box-shadow:0 0 0 .2rem rgba(49,87,213,.12);
    }

    /* =========================================================
       Modal shared
    ========================================================= */
    .modal-content{
        overflow:hidden;
        border:none;
        border-radius:20px;
    }

    .modal-header{
        background:#f8faff;
        border-bottom:1px solid var(--sc-border);
    }

    .modal-title{
        color:#1d3b8f;
        font-weight:800;
    }

    .detail-box{
        height:100%;
        padding:12px 14px;
        background:#fafbfc;
        border:1px solid var(--sc-border);
        border-radius:13px;
    }

    .detail-label{
        margin-bottom:4px;
        color:#7a8496;
        font-size:13px;
    }

    .detail-value{
        color:#1d2939;
        font-weight:700;
        white-space:pre-line;
    }

    /* =========================================================
       Expense modal
    ========================================================= */
    .expense-section{
        padding:16px;
        background:#ffffff;
        border:1px solid var(--sc-border);
        border-radius:16px;
    }

    .expense-section-title{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        margin-bottom:14px;
    }

    .expense-section-title h6{
        margin:0;
        color:#243b7d;
        font-weight:800;
    }

    .expense-child-summary{
        display:grid;
        grid-template-columns:2fr 1fr 1fr;
        gap:10px;
        margin-bottom:14px;
    }

    .expense-items-wrap{
        overflow-x:auto;
        border:1px solid var(--sc-border);
        border-radius:13px;
        -webkit-overflow-scrolling:touch;
    }

    .expense-items-table{
        min-width:700px;
        margin:0;
    }

    .expense-items-table thead th{
        background:#f8faff;
        color:#344054;
        font-size:13px;
        white-space:nowrap;
    }

    .expense-total-box{
        display:flex;
        align-items:center;
        justify-content:flex-end;
        gap:14px;
        margin-top:12px;
        padding:13px 16px;
        background:#f0fdf4;
        color:#067647;
        border:1px solid #abefc6;
        border-radius:13px;
        font-weight:800;
    }

    .expense-total{
        min-width:150px;
        text-align:right;
        font-size:22px;
    }

    .pdf-upload-card{
        height:100%;
        padding:15px;
        background:#fafbff;
        border:1px dashed #b8c2d4;
        border-radius:14px;
    }

    .pdf-upload-card h6{
        margin-bottom:5px;
        color:#253d84;
        font-weight:800;
    }

    .pdf-upload-card p{
        margin-bottom:12px;
        color:#7a8496;
        font-size:13px;
    }

    .file-count{
        display:block;
        margin-top:7px;
        color:#475467;
        font-size:12px;
    }

    .expense-history-table{
        min-width:1000px;
        margin-bottom:0;
    }

    .expense-history-table th{
        background:#f8faff;
        color:#344054;
        font-size:13px;
        white-space:nowrap;
    }

    .attachment-link{
        display:inline-flex;
        align-items:center;
        gap:5px;
        margin:2px 5px 2px 0;
        padding:4px 7px;
        background:#ffffff;
        color:#3157d5;
        border:1px solid #dfe6f4;
        border-radius:8px;
        font-size:12px;
        text-decoration:none;
    }

    .attachment-link:hover{
        background:#eef3ff;
        color:#2444b3;
    }

    /* =========================================================
       Scholarship detail modal
    ========================================================= */
    .scholarship-detail-dialog{
        max-width:1180px;
    }

    .scholarship-detail-modal{
        overflow:hidden;
        border:1px solid #dfe6f4;
        border-radius:20px;
        box-shadow:0 24px 70px rgba(15,23,42,.18);
    }

    .scholarship-detail-header{
        padding:18px 22px;
        background:#ffffff;
        border-bottom:1px solid #e8edf5;
    }

    .scholarship-detail-header-main{
        min-width:0;
        display:flex;
        align-items:center;
        gap:12px;
    }

    .scholarship-detail-header-icon{
        width:42px;
        height:42px;
        flex:0 0 42px;
        display:flex;
        align-items:center;
        justify-content:center;
        background:#eef3ff;
        color:#3157d5;
        border-radius:13px;
        font-size:18px;
    }

    .scholarship-detail-kicker{
        margin-bottom:2px;
        color:#667085;
        font-size:12px;
        font-weight:700;
    }

    .scholarship-detail-title{
        margin:0;
        color:#1d3b8f;
        font-size:18px;
        font-weight:800;
    }

    .scholarship-detail-body{
        padding:20px;
        background:#f7f9fc;
    }

    .scholarship-detail-layout{
        display:grid;
        grid-template-columns:280px minmax(0,1fr);
        gap:18px;
        align-items:start;
    }

    .scholarship-profile-panel{
        position:sticky;
        top:0;
        padding:20px;
        background:#ffffff;
        border:1px solid #e3e8f1;
        border-radius:18px;
    }

    .scholarship-profile-photo,
    .scholarship-profile-avatar{
        width:148px;
        height:148px;
        margin:0 auto;
        border-radius:22px;
    }

    .scholarship-profile-photo{
        display:block;
        object-fit:cover;
        border:1px solid #dfe6f4;
        box-shadow:0 10px 24px rgba(15,23,42,.10);
    }

    .scholarship-profile-avatar{
        display:flex;
        align-items:center;
        justify-content:center;
        background:#eef3ff;
        color:#3157d5;
        font-size:48px;
        font-weight:800;
    }

    .scholarship-profile-name{
        margin:16px 0 4px;
        color:#172033;
        text-align:center;
        font-size:18px;
        font-weight:800;
    }

    .scholarship-profile-subtitle{
        margin-bottom:14px;
        color:#667085;
        text-align:center;
        font-size:13px;
    }

    .scholarship-profile-status{
        display:flex;
        justify-content:center;
        margin-bottom:18px;
    }

    .scholarship-profile-meta{
        padding-top:8px;
        border-top:1px solid #edf0f5;
    }

    .scholarship-profile-meta-row{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
        padding:10px 0;
        border-bottom:1px solid #f0f2f6;
    }

    .scholarship-profile-meta-row:last-child{
        border-bottom:0;
    }

    .scholarship-profile-meta-label{
        flex:0 0 auto;
        color:#7a8496;
        font-size:13px;
    }

    .scholarship-profile-meta-value{
        color:#25324b;
        text-align:right;
        font-size:13px;
        font-weight:700;
        overflow-wrap:anywhere;
    }

    .scholarship-detail-content{
        min-width:0;
        display:flex;
        flex-direction:column;
        gap:14px;
    }

    .scholarship-detail-section{
        padding:18px;
        background:#ffffff;
        border:1px solid #e3e8f1;
        border-radius:18px;
    }

    .scholarship-detail-section-header{
        display:flex;
        align-items:center;
        gap:9px;
        margin-bottom:14px;
        padding-bottom:12px;
        border-bottom:1px solid #edf0f5;
    }

    .scholarship-detail-section-icon{
        width:34px;
        height:34px;
        flex:0 0 34px;
        display:flex;
        align-items:center;
        justify-content:center;
        background:#eef3ff;
        color:#3157d5;
        border-radius:10px;
    }

    .scholarship-detail-section-title{
        margin:0;
        color:#243b7d;
        font-size:15px;
        font-weight:800;
    }

    .scholarship-info-grid{
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:12px;
    }

    .scholarship-info-item{
        min-width:0;
        padding:13px 14px;
        background:#fafbfc;
        border:1px solid #e8ecf2;
        border-radius:13px;
    }

    .scholarship-info-item.full{
        grid-column:1 / -1;
    }

    .scholarship-info-label{
        margin-bottom:5px;
        color:#7a8496;
        font-size:12px;
        font-weight:600;
    }

    .scholarship-info-value{
        color:#1d2939;
        font-size:14px;
        font-weight:700;
        line-height:1.6;
        white-space:pre-line;
        overflow-wrap:anywhere;
    }

    .scholarship-fund-summary{
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:10px;
    }

    .scholarship-fund-metric{
        min-width:0;
        padding:14px;
        background:#fafbfc;
        border:1px solid #e8ecf2;
        border-radius:14px;
    }

    .scholarship-fund-metric-label{
        margin-bottom:6px;
        color:#667085;
        font-size:12px;
        font-weight:700;
    }

    .scholarship-fund-metric-value{
        color:#172033;
        font-size:19px;
        font-weight:800;
        line-height:1.2;
        overflow-wrap:anywhere;
    }

    .scholarship-fund-metric-value.success{
        color:#067647;
    }

    .scholarship-status-note{
        display:flex;
        align-items:flex-start;
        gap:10px;
        margin-top:12px;
        padding:12px 14px;
        border-radius:13px;
        font-size:13px;
        line-height:1.6;
    }

    .scholarship-status-note.approved{
        background:#ecfdf3;
        color:#067647;
        border:1px solid #abefc6;
    }

    .scholarship-status-note.pending{
        background:#fffaeb;
        color:#b54708;
        border:1px solid #fedf89;
    }

    .scholarship-status-note.rejected{
        background:#fef3f2;
        color:#b42318;
        border:1px solid #fecdca;
    }

    .scholarship-detail-footer{
        padding:13px 20px;
        background:#ffffff;
        border-top:1px solid #e8edf5;
    }

    /* =========================================================
       Yearly totals inside child detail and expense history
    ========================================================= */
    .scholarship-year-summary{
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:10px;
        margin-top:14px;
    }

    .scholarship-year-card{
        min-width:0;
        padding:14px;
        background:#f8faff;
        border:1px solid #dfe6f4;
        border-radius:14px;
    }

    .scholarship-year-card-header{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;
        margin-bottom:11px;
    }

    .scholarship-year-card-title{
        color:#3157d5;
        font-size:13px;
        font-weight:800;
    }

    .scholarship-year-card-total{
        color:#067647;
        font-size:18px;
        font-weight:800;
        text-align:right;
    }

    .scholarship-year-card-total small{
        font-size:11px;
        font-weight:700;
    }

    .scholarship-year-card-grid{
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:8px;
    }

    .scholarship-year-card-item{
        min-width:0;
        padding:9px 10px;
        background:#ffffff;
        border:1px solid #e8ecf2;
        border-radius:10px;
    }

    .scholarship-year-card-item-label{
        color:#7a8496;
        font-size:11px;
    }

    .scholarship-year-card-item-value{
        margin-top:3px;
        color:#25324b;
        font-size:13px;
        font-weight:800;
        overflow-wrap:anywhere;
    }

    .expense-year-group-row td{
        padding:10px 12px !important;
        background:#eef3ff !important;
        color:#243b7d !important;
        border-top:1px solid #cfdaf8 !important;
        border-bottom:1px solid #cfdaf8 !important;
        font-weight:800;
    }

    .expense-year-group-content{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
    }

    .expense-year-subtotal{
        color:#067647;
        white-space:nowrap;
    }

    .expense-grand-total-row td{
        padding:13px 12px !important;
        background:#ecfdf3 !important;
        color:#067647 !important;
        border-top:2px solid #86efac !important;
        font-weight:800;
    }


    /* =========================================================
       Edit expense actions and modal
    ========================================================= */
    .expense-history-actions{
        display:flex;
        align-items:center;
        justify-content:center;
        gap:6px;
    }

    .expense-history-edit-btn{
        width:36px;
        height:36px;
        min-width:36px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:0;
        background:#fff6e8;
        color:#b85f0b;
        border:1px solid #ffd9a3;
        border-radius:10px;
        transition:
            transform .18s ease,
            background-color .18s ease,
            border-color .18s ease,
            box-shadow .18s ease;
    }

    .expense-history-edit-btn:hover,
    .expense-history-edit-btn:focus{
        background:#ffedd2;
        color:#944806;
        border-color:#ffc46b;
        transform:translateY(-1px);
        box-shadow:0 5px 12px rgba(184,95,11,.14);
    }

    .expense-edit-dialog{
        max-width:1120px;
    }

    .expense-edit-modal{
        border:1px solid #dfe6f4;
        box-shadow:0 24px 70px rgba(15,23,42,.20);
    }

    .expense-edit-summary{
        display:grid;
        grid-template-columns:2fr 1fr 1fr;
        gap:10px;
        margin-bottom:14px;
    }

    .expense-existing-files{
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:10px;
    }

    .expense-existing-file{
        min-width:0;
        display:flex;
        align-items:flex-start;
        gap:10px;
        padding:11px 12px;
        background:#fafbff;
        border:1px solid #dfe6f4;
        border-radius:12px;
    }

    .expense-existing-file .form-check-input{
        flex:0 0 auto;
        margin-top:3px;
    }

    .expense-existing-file-content{
        min-width:0;
        flex:1;
    }

    .expense-existing-file-name{
        display:block;
        color:#3157d5;
        font-size:12.5px;
        font-weight:700;
        text-decoration:none;
        overflow-wrap:anywhere;
    }

    .expense-existing-file-name:hover{
        color:#2444b3;
        text-decoration:underline;
    }

    .expense-existing-file-hint{
        display:block;
        margin-top:3px;
        color:#b42318;
        font-size:11.5px;
    }

    .expense-edit-note{
        display:flex;
        align-items:flex-start;
        gap:8px;
        padding:10px 12px;
        background:#fffaeb;
        color:#8a4b08;
        border:1px solid #fedf89;
        border-radius:11px;
        font-size:12px;
        line-height:1.55;
    }

    .scholarship-reapply-note{
        display:flex;
        align-items:flex-start;
        gap:10px;
        margin-bottom:14px;
        padding:13px 14px;
        color:#2446b8;
        background:#eef3ff;
        border:1px solid #cfdaf8;
        border-radius:13px;
        font-size:13px;
        line-height:1.6;
    }

    .scholarship-application-history{
        width:100%;
        overflow-x:auto;
        border:1px solid #e4e9f2;
        border-radius:13px;
    }

    .scholarship-application-history table{
        min-width:690px;
        margin:0;
    }

    .scholarship-application-history th{
        background:#f8faff;
        color:#344054;
        font-size:12px;
        white-space:nowrap;
    }

    /* SweetAlert must stay above Bootstrap modal */
    .swal2-container{
        z-index:20000 !important;
    }

    /* =========================================================
       Responsive: large notebook
    ========================================================= */
    @media(max-width:1399px){
        .sc-table{
            min-width:1320px;
        }

        .sc-action-heading,
        .sc-action-cell{
            width:304px;
            min-width:304px;
        }

        .sc-expense-action{
            min-width:118px;
            padding:0 11px;
            font-size:12px;
        }

        .sc-action-icon{
            width:38px;
            height:38px;
            min-width:38px;
        }
    }

    /* =========================================================
       Responsive: tablet / small notebook
    ========================================================= */
    @media(max-width:1199px){
        .sc-finance-overview{
            grid-template-columns:1fr;
        }

        .sc-finance-year-list{
            grid-template-columns:repeat(3,minmax(180px,1fr));
        }

        .sc-summary-grid{
            grid-template-columns:repeat(2,minmax(0,1fr));
        }

        .sc-table{
            min-width:1180px;
        }

        .sc-action-heading,
        .sc-action-cell{
            width:180px;
            min-width:180px;
        }

        .sc-action-panel{
            flex-direction:column;
            align-items:stretch;
            gap:7px;
        }

        .sc-expense-action{
            width:100%;
            min-width:0;
            min-height:36px;
        }

        .sc-icon-actions{
            width:100%;
        }

        .sc-action-icon{
            width:36px;
            height:36px;
            min-width:36px;
        }

        .scholarship-fund-summary{
            grid-template-columns:repeat(2,minmax(0,1fr));
        }
    }

    /* =========================================================
       Responsive: tablet portrait
    ========================================================= */
    @media(max-width:991px){
        .scholarship-detail-layout{
            grid-template-columns:1fr;
        }

        .scholarship-profile-panel{
            position:static;
            display:grid;
            grid-template-columns:130px minmax(0,1fr);
            column-gap:18px;
            align-items:center;
        }

        .scholarship-profile-photo,
        .scholarship-profile-avatar{
            width:130px;
            height:130px;
            grid-row:1 / span 4;
        }

        .scholarship-profile-name,
        .scholarship-profile-subtitle{
            text-align:left;
        }

        .scholarship-profile-name{
            margin-top:0;
        }

        .scholarship-profile-status{
            justify-content:flex-start;
            margin-bottom:8px;
        }

        .scholarship-profile-meta{
            grid-column:1 / -1;
            width:100%;
            margin-top:14px;
        }
    }

    /* =========================================================
       Responsive: mobile
    ========================================================= */
    @media(max-width:767px){
        .sc-finance-total-meta{
            grid-template-columns:1fr;
        }

        .sc-finance-year-list{
            display:flex;
            overflow-x:auto;
            padding-bottom:4px;
            -webkit-overflow-scrolling:touch;
        }

        .sc-finance-year-card{
            min-width:220px;
            flex:0 0 220px;
        }

        .scholarship-year-summary{
            grid-template-columns:1fr;
        }

        .sc-page{
            padding:12px;
        }

        .sc-header{
            padding:18px;
            border-radius:17px;
        }

        .sc-header-row{
            align-items:stretch;
        }

        .sc-add-btn{
            width:100%;
        }

        .sc-toolbar{
            align-items:stretch;
        }

        .sc-filter{
            width:100%;
            flex:0 0 100%;
        }

        .sc-filter input{
            min-width:220px;
        }

        .sc-table{
            min-width:1080px;
        }

        .sc-action-heading,
        .sc-action-cell{
            width:148px;
            min-width:148px;
        }

        .sc-expense-label-long{
            display:none;
        }

        .sc-expense-label-short{
            display:inline;
        }

        .sc-expense-action{
            min-height:35px;
            padding:0 9px;
            font-size:11.5px;
        }

        .sc-action-icon{
            width:34px;
            height:34px;
            min-width:34px;
            border-radius:9px;
            font-size:13px;
        }

        .expense-child-summary{
            grid-template-columns:1fr;
        }

        .expense-total-box{
            flex-wrap:wrap;
        }

        .expense-total{
            min-width:0;
        }

        .modal-dialog{
            margin:8px;
        }

        .modal-body{
            max-height:72vh;
            overflow-y:auto;
        }
    }


    @media(max-width:767px){
        .expense-edit-summary,
        .expense-existing-files{
            grid-template-columns:1fr;
        }

        .expense-history-edit-btn{
            width:34px;
            height:34px;
            min-width:34px;
        }
    }

    @media(max-width:576px){
        .sc-summary-grid{
            grid-template-columns:1fr;
        }

        .scholarship-detail-body{
            padding:12px;
        }

        .scholarship-profile-panel{
            display:block;
            padding:16px;
        }

        .scholarship-profile-photo,
        .scholarship-profile-avatar{
            width:120px;
            height:120px;
        }

        .scholarship-profile-name,
        .scholarship-profile-subtitle{
            text-align:center;
        }

        .scholarship-profile-name{
            margin-top:14px;
        }

        .scholarship-profile-status{
            justify-content:center;
        }

        .scholarship-info-grid,
        .scholarship-fund-summary{
            grid-template-columns:1fr;
        }

        .scholarship-info-item.full{
            grid-column:auto;
        }
    }
</style>

<div class="sc-page">

    <div class="sc-header">
        <div class="sc-header-row">
            <div>
                <h4>
                    <i class="bi bi-mortarboard-fill me-2"></i>
                    ระบบบริหารจัดการทุนการศึกษาเด็ก
                </h4>
                <p>ผู้ขอทุนหนึ่งคนยื่นได้หลายปีและหลายภาคเรียน โดยแยกผลพิจารณา ค่าใช้จ่าย และเอกสารของแต่ละรอบอย่างชัดเจน</p>
            </div>

            <button type="button"
                    class="btn btn-success sc-add-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#createChildModal">
                <i class="bi bi-plus-circle me-1"></i>
                เพิ่มผู้ขอรับทุนรายใหม่
            </button>
        </div>
    </div>

    <div class="sc-summary-grid">
        <div class="sc-summary-item total">
            <div class="sc-summary-icon"><i class="bi bi-people"></i></div>
            <div>
                <div class="sc-summary-label">คำขอทุนทั้งหมด</div>
                <div class="sc-summary-value">{{ number_format($statusSummary->total_count ?? 0) }}</div>
                <div class="sc-summary-detail">
                    ผู้ขอไม่ซ้ำ {{ number_format($distinctPersonCount ?? 0) }} คน
                </div>
            </div>
        </div>

        <div class="sc-summary-item pending">
            <div class="sc-summary-icon"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="sc-summary-label">รอการพิจารณา</div>
                <div class="sc-summary-value">{{ number_format($statusSummary->pending_count ?? 0) }}</div>
            </div>
        </div>

        <div class="sc-summary-item approved">
            <div class="sc-summary-icon"><i class="bi bi-patch-check"></i></div>
            <div>
                <div class="sc-summary-label">อนุมัติทุน</div>
                <div class="sc-summary-value">{{ number_format($statusSummary->approved_count ?? 0) }}</div>
            </div>
        </div>

        <div class="sc-summary-item rejected">
            <div class="sc-summary-icon"><i class="bi bi-x-octagon"></i></div>
            <div>
                <div class="sc-summary-label">ไม่ผ่านการอนุมัติ</div>
                <div class="sc-summary-value">{{ number_format($statusSummary->rejected_count ?? 0) }}</div>
            </div>
        </div>
    </div>

    <section class="sc-finance-overview" aria-label="สรุปยอดค่าใช้จ่ายทุนการศึกษา">

        <div class="sc-finance-total-card">
            <div>
                <div class="sc-finance-total-top">
                    <div class="sc-finance-total-icon">
                        <i class="bi bi-wallet2"></i>
                    </div>

                    <div>
                        <div class="sc-finance-total-label">
                            ยอดค่าใช้จ่ายรวมทุกปีการศึกษา
                        </div>

                        <div class="sc-finance-total-value">
                            {{ number_format($expenseGrandTotal, 2) }}
                            <small>บาท</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sc-finance-total-meta">
                <div class="sc-finance-total-meta-item">
                    <div class="sc-finance-total-meta-label">ปีการศึกษา</div>
                    <div class="sc-finance-total-meta-value">
                        {{ number_format($expenseYearSummary->count()) }}
                    </div>
                </div>

                <div class="sc-finance-total-meta-item">
                    <div class="sc-finance-total-meta-label">ครั้งที่บันทึก</div>
                    <div class="sc-finance-total-meta-value">
                        {{ number_format($expenseGrandRecordCount) }}
                    </div>
                </div>

                <div class="sc-finance-total-meta-item">
                    <div class="sc-finance-total-meta-label">เด็กที่มีค่าใช้จ่าย</div>
                    <div class="sc-finance-total-meta-value">
                        {{ number_format($expenseGrandRecipientCount) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="sc-finance-years-panel">
            <div class="sc-finance-years-header">
                <div>
                    <h5 class="sc-finance-years-title">
                        <i class="bi bi-bar-chart-line me-1"></i>
                        ยอดรวมแยกตามปีการศึกษา
                    </h5>

                    <div class="sc-finance-years-note">
                        คำนวณจากรายการค่าใช้จ่ายที่บันทึกจริงทุกภาคเรียน
                    </div>
                </div>
            </div>

            @if($expenseYearSummary->isNotEmpty())
                <div class="sc-finance-year-list">
                    @foreach($expenseYearSummary as $yearSummary)
                        <div class="sc-finance-year-card {{ (string) $academicYear === (string) $yearSummary->academic_year ? 'is-active' : '' }}">
                            <div class="sc-finance-year-top">
                                <span class="sc-finance-year-name">
                                    ปีการศึกษา {{ $yearSummary->academic_year }}
                                </span>

                                <span class="sc-finance-year-count">
                                    {{ number_format($yearSummary->expense_count) }} ครั้ง
                                </span>
                            </div>

                            <div class="sc-finance-year-amount">
                                {{ number_format($yearSummary->total_amount, 2) }}
                                <small>บาท</small>
                            </div>

                            <div class="sc-finance-year-meta">
                                <span>
                                    <i class="bi bi-people me-1"></i>
                                    {{ number_format($yearSummary->recipient_count) }} คน
                                </span>
                                <span>
                                    ภาค 1: {{ number_format($yearSummary->semester_1_total, 2) }} บาท
                                </span>
                                <span>
                                    ภาค 2: {{ number_format($yearSummary->semester_2_total, 2) }} บาท
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="sc-finance-empty">
                    <i class="bi bi-receipt"></i>
                    ยังไม่มีรายการค่าใช้จ่ายทุนการศึกษา
                </div>
            @endif
        </div>

    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
            <i class="bi bi-check-circle-fill me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(
        $errors->any()
        && !session('open_expense_modal')
        && !old('expense_child_id')
        && !session('open_edit_expense_modal')
        && !old('edit_expense_id')
        && !session('open_reapply_modal')
        && !old('reapply_child_id')
        && !session('open_create_child_modal')
        && !session('open_edit_child_modal')
    )
        <div class="alert alert-danger">
            <strong>กรุณาตรวจสอบข้อมูล</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="sc-card">

        <div class="sc-toolbar">
            @if($children->count() > 0 || request()->hasAny(['keyword', 'academic_year', 'semester', 'scholarship_status']))
                <form method="GET"
                      action="{{ route('scholarship.children.index') }}"
                      class="sc-filter"
                      id="childSearchForm">

                    <input type="text"
                           name="keyword"
                           id="keywordInput"
                           class="form-control"
                           placeholder="ค้นหาชื่อ สถานศึกษา ผู้ปกครอง หรือโทรศัพท์"
                           value="{{ request('keyword') }}"
                           autocomplete="off">

                    <select name="academic_year"
                            class="form-select"
                            onchange="this.form.submit()">
                        <option value="">ทุกปีการศึกษา</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $academicYear == $year ? 'selected' : '' }}>
                                ปีการศึกษา {{ $year }}
                            </option>
                        @endforeach
                    </select>

                    <select name="semester"
                            class="form-select"
                            onchange="this.form.submit()">
                        <option value="">ทุกภาคเรียน</option>
                        <option value="1" {{ (string) $semester === '1' ? 'selected' : '' }}>
                            ภาคเรียนที่ 1
                        </option>
                        <option value="2" {{ (string) $semester === '2' ? 'selected' : '' }}>
                            ภาคเรียนที่ 2
                        </option>
                    </select>

                    <select name="scholarship_status"
                            class="form-select"
                            onchange="this.form.submit()">
                        <option value="">ทุกสถานะ</option>
                        @foreach(\App\Models\ScholarshipChild::statusOptions() as $statusValue => $statusLabel)
                            <option value="{{ $statusValue }}" {{ $status === $statusValue ? 'selected' : '' }}>
                                {{ $statusLabel }}
                            </option>
                        @endforeach
                    </select>

                    <a href="{{ route('scholarship.children.index') }}"
                       class="btn btn-outline-secondary">
                        ล้างค่า
                    </a>

                    <a href="{{ route('scholarship.children.report', [
                            'academic_year' => $academicYear,
                            'semester' => $semester,
                            'keyword' => request('keyword'),
                            'scholarship_status' => $status,
                        ]) }}"
                       class="btn btn-outline-dark">
                        <i class="bi bi-file-earmark-text me-1"></i>
                        รายงาน
                    </a>
                </form>
            @endif
        </div>

        @if($children->count() > 0)
            <div class="sc-table-wrap">
                <table class="table table-hover align-middle sc-table">
                    <thead>
                        <tr>
                            <th width="70">ภาพ</th>
                            <th>ชื่อ - นามสกุล</th>
                            <th>อายุ</th>
                            <th>ระดับการศึกษา</th>
                            <th>สถานศึกษา</th>
                            <th>ปี / ภาคเรียน</th>
                            <th>ผู้ปกครอง</th>
                            <th>โทรศัพท์</th>
                            <th>สถานะการพิจารณา</th>
                            <th class="text-center sc-action-heading">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($children as $child)
                            @php
                                $photoUrl = null;

                                if ($child->photo) {
                                    $photoUrl = str_starts_with($child->photo, 'upload/')
                                        ? asset($child->photo)
                                        : asset('storage/' . $child->photo);
                                }
                            @endphp

                            <tr>
                                <td>
                                    @if($photoUrl)
                                        <img src="{{ $photoUrl }}"
                                             loading="lazy"
                                             decoding="async"
                                             class="sc-photo"
                                             alt="{{ $child->first_name }} {{ $child->last_name }}">
                                    @else
                                        <div class="sc-avatar">
                                            {{ mb_substr($child->first_name, 0, 1) }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="sc-name">
                                        {{ $child->first_name }} {{ $child->last_name }}
                                    </div>
                                    <div class="sc-subtext">
                                        {{ \Illuminate\Support\Str::limit($child->reason, 45) }}
                                    </div>
                                </td>

                                <td>{{ $child->age ?? '-' }}</td>
                                <td>{{ $child->education_level ?? '-' }}</td>
                                <td>{{ $child->school_name ?? '-' }}</td>

                                <td>
                                    <span class="sc-year-badge">
                                        {{ $child->academic_year }} / ภาคเรียนที่ {{ $child->semester }}
                                    </span>
                                </td>

                                <td>{{ $child->guardian_name ?? '-' }}</td>
                                <td>{{ $child->phone ?? '-' }}</td>

                                <td>
                                    <span class="sc-status-badge {{ $child->status_badge_class }}">
                                        @if($child->scholarship_status === \App\Models\ScholarshipChild::STATUS_APPROVED)
                                            <i class="bi bi-check-circle-fill"></i>
                                        @elseif($child->scholarship_status === \App\Models\ScholarshipChild::STATUS_REJECTED)
                                            <i class="bi bi-x-circle-fill"></i>
                                        @else
                                            <i class="bi bi-clock-fill"></i>
                                        @endif

                                        {{ $child->status_label }}
                                    </span>

                                    <form action="{{ route('scholarship.children.status', $child->id) }}"
                                          method="POST"
                                          class="status-update-form">
                                        @csrf
                                        @method('PATCH')

                                        <select name="scholarship_status"
                                                class="form-select form-select-sm sc-status-select status-select"
                                                data-current="{{ $child->scholarship_status }}"
                                                data-name="{{ $child->first_name }} {{ $child->last_name }}">
                                            @foreach(\App\Models\ScholarshipChild::statusOptions() as $statusValue => $statusLabel)
                                                <option value="{{ $statusValue }}"
                                                    {{ $child->scholarship_status === $statusValue ? 'selected' : '' }}>
                                                    {{ $statusLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>

                                {{-- ช่องจัดการภายใน @foreach --}}
<td class="text-center sc-action-cell">
    <div class="sc-action-panel">

        @if($child->isApproved())
            <button type="button"
                    class="btn sc-expense-action"
                    data-bs-toggle="modal"
                    data-bs-target="#expenseModal{{ $child->id }}"
                    title="บันทึกรายการค่าใช้จ่าย"
                    aria-label="บันทึกรายการค่าใช้จ่ายของ {{ $child->first_name }} {{ $child->last_name }}">

                <i class="bi bi-cash-coin"></i>

                <span class="sc-expense-label-long">
                    บันทึกค่าใช้จ่าย
                </span>

                <span class="sc-expense-label-short">
                    ค่าใช้จ่าย
                </span>
            </button>
        @endif

        <div class="sc-icon-actions">

            <button type="button"
                    class="btn sc-action-icon sc-action-reapply"
                    data-bs-toggle="modal"
                    data-bs-target="#reapplyChildModal{{ $child->id }}"
                    title="ยื่นคำขอทุนรอบใหม่"
                    aria-label="ยื่นคำขอทุนรอบใหม่ของ {{ $child->first_name }} {{ $child->last_name }}">
                <i class="bi bi-arrow-repeat"></i>
            </button>

            <button type="button"
                    class="btn sc-action-icon sc-action-view"
                    data-bs-toggle="modal"
                    data-bs-target="#showChildModal{{ $child->id }}"
                    title="ดูรายละเอียด"
                    aria-label="ดูรายละเอียดของ {{ $child->first_name }} {{ $child->last_name }}">
                <i class="bi bi-eye"></i>
            </button>

            <button type="button"
                    class="btn sc-action-icon sc-action-edit"
                    data-bs-toggle="modal"
                    data-bs-target="#editChildModal{{ $child->id }}"
                    title="แก้ไขข้อมูล"
                    aria-label="แก้ไขข้อมูลของ {{ $child->first_name }} {{ $child->last_name }}">
                <i class="bi bi-pencil-square"></i>
            </button>

            <form action="{{ route('scholarship.children.delete', $child->id) }}"
                  method="POST"
                  class="delete-child-form sc-action-form"
                  data-name="{{ $child->first_name }} {{ $child->last_name }}">

                @csrf
                @method('DELETE')

                <button type="submit"
                        class="btn sc-action-icon sc-action-delete"
                        title="ลบข้อมูล"
                        aria-label="ลบข้อมูลของ {{ $child->first_name }} {{ $child->last_name }}">
                    <i class="bi bi-trash"></i>
                </button>
            </form>

        </div>
    </div>
</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $children->links() }}
            </div>
        @else
            <div class="sc-empty">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                ไม่พบข้อมูลผู้ขอรับทุนตามเงื่อนไขที่เลือก
            </div>
        @endif
    </div>
</div>

{{-- Modal เพิ่มผู้ขอรับทุน --}}
<div class="modal fade" id="createChildModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form action="{{ route('scholarship.children.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="modal-content">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-1"></i>
                    เพิ่มผู้ขอรับทุนรายใหม่และคำขอครั้งแรก
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                @include('landing.scholarship.children.partials.form', [
                    'child' => null,
                    'yearListId' => 'academic_year_create'
                ])
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    ยกเลิก
                </button>

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i>
                    บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</div>

@foreach($children as $child)
    @php
        $modalPhotoUrl = null;

        if ($child->photo) {
            $modalPhotoUrl = str_starts_with($child->photo, 'upload/')
                ? asset($child->photo)
                : asset('storage/' . $child->photo);
        }

        $isOldExpenseChild = (string) old('expense_child_id') === (string) $child->id;

        $expenseRows = $isOldExpenseChild
            ? old('items', [['expense_type' => '', 'amount' => '']])
            : [['expense_type' => '', 'amount' => '']];

        $personApplications = $child->personApplications;

        if ($personApplications->isEmpty()) {
            $personApplications = collect([$child]);
        }

        $personApplications = $personApplications
            ->sortByDesc(function ($application) {
                return sprintf(
                    '%04d-%d-%010d',
                    (int) $application->academic_year,
                    (int) $application->semester,
                    (int) $application->id
                );
            })
            ->values();

        $allPersonExpenses = $personApplications
            ->flatMap(function ($application) {
                return $application->expenses;
            })
            ->sortByDesc(function ($expense) {
                return optional($expense->record_date)->format('Y-m-d')
                    . '-'
                    . str_pad((string) $expense->id, 10, '0', STR_PAD_LEFT);
            })
            ->values();

        $nextAcademicYear = (int) $child->academic_year;
        $nextSemester = 2;

        if ((int) $child->semester === 2) {
            $nextAcademicYear++;
            $nextSemester = 1;
        }

        $isOldReapplyChild = (string) old('reapply_child_id') === (string) $child->id;
    @endphp


    {{-- Modal ยื่นคำขอทุนรอบใหม่ โดยใช้ข้อมูลบุคคลเดิม --}}
    <div class="modal fade"
         id="reapplyChildModal{{ $child->id }}"
         tabindex="-1"
         aria-labelledby="reapplyChildModalLabel{{ $child->id }}"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form action="{{ route('scholarship.children.applications.store', $child->id) }}"
                  method="POST"
                  class="modal-content">
                @csrf

                <input type="hidden"
                       name="reapply_child_id"
                       value="{{ $child->id }}">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title"
                            id="reapplyChildModalLabel{{ $child->id }}">
                            <i class="bi bi-arrow-repeat me-1"></i>
                            ยื่นคำขอทุนรอบใหม่
                        </h5>
                        <div class="text-muted small mt-1">
                            {{ $child->first_name }} {{ $child->last_name }}
                        </div>
                    </div>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="scholarship-reapply-note">
                        <i class="bi bi-person-check-fill mt-1"></i>
                        <div>
                            ระบบจะใช้ชื่อ เพศ ที่อยู่ ผู้ปกครอง โทรศัพท์ และภาพถ่ายชุดเดิม
                            โดยสร้างคำขอใหม่ที่มีสถานะ “รอการพิจารณา”
                            ประวัติปี/ภาคเรียนเดิมจะไม่ถูกแก้ไข
                        </div>
                    </div>

                    @if($isOldReapplyChild && $errors->any())
                        <div class="alert alert-danger">
                            <strong>กรุณาตรวจสอบข้อมูล</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                ปีการศึกษา <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="academic_year"
                                   class="form-control"
                                   value="{{ $isOldReapplyChild ? old('academic_year') : $nextAcademicYear }}"
                                   inputmode="numeric"
                                   pattern="[0-9]{4}"
                                   maxlength="4"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                ภาคเรียนที่ <span class="text-danger">*</span>
                            </label>
                            <select name="semester"
                                    class="form-select"
                                    required>
                                <option value="1"
                                    {{ (string) ($isOldReapplyChild ? old('semester') : $nextSemester) === '1' ? 'selected' : '' }}>
                                    ภาคเรียนที่ 1
                                </option>
                                <option value="2"
                                    {{ (string) ($isOldReapplyChild ? old('semester') : $nextSemester) === '2' ? 'selected' : '' }}>
                                    ภาคเรียนที่ 2
                                </option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">อายุในรอบใหม่</label>
                            <input type="number"
                                   name="age"
                                   class="form-control"
                                   value="{{ $isOldReapplyChild ? old('age') : $child->age }}"
                                   min="1"
                                   max="120">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">ระดับการศึกษา</label>

                            @php
                                $reapplyEducationLevel = $isOldReapplyChild
                                    ? old('education_level')
                                    : $child->education_level;
                            @endphp

                            <select name="education_level"
                                    class="form-select {{ $isOldReapplyChild && $errors->has('education_level') ? 'is-invalid' : '' }}">

                                <option value="">เลือกระดับการศึกษา</option>

                                {{-- รักษาค่าข้อมูลเก่าที่อาจไม่อยู่ในรายการตัวเลือก --}}
                                @if(
                                    $reapplyEducationLevel &&
                                    !in_array(
                                        $reapplyEducationLevel,
                                        $scholarshipEducationLevels,
                                        true
                                    )
                                )
                                    <option value="{{ $reapplyEducationLevel }}" selected>
                                        {{ $reapplyEducationLevel }}
                                    </option>
                                @endif

                                @foreach($scholarshipEducationLevels as $level)
                                    <option value="{{ $level }}"
                                        @selected($reapplyEducationLevel === $level)>
                                        {{ $level }}
                                    </option>
                                @endforeach
                            </select>

                            @if($isOldReapplyChild)
                                @error('education_level')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            @endif
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">สถานศึกษา</label>
                            <input type="text"
                                   name="school_name"
                                   class="form-control"
                                   value="{{ $isOldReapplyChild ? old('school_name') : $child->school_name }}"
                                   maxlength="255">
                        </div>

                        <div class="col-12">
                            <label class="form-label">สาเหตุที่ขอรับทุนรอบใหม่</label>
                            <textarea name="reason"
                                      class="form-control"
                                      rows="3">{{ $isOldReapplyChild ? old('reason') : $child->reason }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">ความต้องการความช่วยเหลือ</label>
                            <textarea name="help_needed"
                                      class="form-control"
                                      rows="3">{{ $isOldReapplyChild ? old('help_needed') : $child->help_needed }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">รายละเอียดเพิ่มเติม</label>
                            <textarea name="more_detail"
                                      class="form-control"
                                      rows="3">{{ $isOldReapplyChild ? old('more_detail') : $child->more_detail }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        ยกเลิก
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="bi bi-file-earmark-plus me-1"></i>
                        สร้างคำขอรอบใหม่
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal ดูรายละเอียด --}}
@php
    /*
     * สรุปค่าใช้จ่ายของเด็กคนนี้จากข้อมูลที่ eager load มาแล้ว
     * จึงไม่เกิด Query เพิ่มภายใน View
     */
    $expenseYearGroups = $allPersonExpenses
        ->groupBy(function ($expense) {
            return (string) $expense->academic_year;
        })
        ->sortKeysDesc();

    $expenseYearSummaryForChild = $expenseYearGroups
        ->map(function ($yearExpenses, $year) {
            return [
                'academic_year' => (string) $year,
                'total_amount' => (float) $yearExpenses->sum('total_amount'),
                'semester_1_total' => (float) $yearExpenses
                    ->where('semester', 1)
                    ->sum('total_amount'),
                'semester_2_total' => (float) $yearExpenses
                    ->where('semester', 2)
                    ->sum('total_amount'),
                'record_count' => $yearExpenses->count(),
                'item_count' => $yearExpenses->sum(function ($expense) {
                    return $expense->items->count();
                }),
                'expense_document_count' => $yearExpenses->sum(function ($expense) {
                    return $expense->attachments
                        ->where('category', 'expense_document')
                        ->count();
                }),
                'grade_report_count' => $yearExpenses->sum(function ($expense) {
                    return $expense->attachments
                        ->where('category', 'grade_report')
                        ->count();
                }),
            ];
        });

    $expenseTotal = (float) $expenseYearSummaryForChild
        ->sum('total_amount');

    $expenseRecordCount = $allPersonExpenses->count();

    $allExpenseAttachments = $allPersonExpenses
        ->flatMap(function ($expense) {
            return $expense->attachments;
        });

    $expenseDocumentCount = $allExpenseAttachments
        ->where('category', 'expense_document')
        ->count();

    $gradeReportCount = $allExpenseAttachments
        ->where('category', 'grade_report')
        ->count();

    $latestExpense = $allPersonExpenses->first();

    $genderLabel = match ($child->gender) {
        'male' => 'ชาย',
        'female' => 'หญิง',
        default => '-',
    };

    $statusUpdatedText = '-';

    if ($child->scholarship_status_updated_at) {
        $statusDate = $child->scholarship_status_updated_at;

        $statusUpdatedText =
            $statusDate->format('d/m/')
            . ($statusDate->year + 543)
            . ' เวลา '
            . $statusDate->format('H:i')
            . ' น.';
    }

    $latestExpenseDateText = '-';

    if ($latestExpense && $latestExpense->record_date) {
        $latestDate = $latestExpense->record_date;

        $latestExpenseDateText =
            $latestDate->format('d/m/')
            . ($latestDate->year + 543);
    }
@endphp

<div class="modal fade"
     id="showChildModal{{ $child->id }}"
     tabindex="-1"
     aria-labelledby="showChildModalLabel{{ $child->id }}"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable scholarship-detail-dialog">

        <div class="modal-content scholarship-detail-modal">

            <div class="modal-header scholarship-detail-header">

                <div class="scholarship-detail-header-main">

                    <div class="scholarship-detail-header-icon">
                        <i class="bi bi-person-vcard"></i>
                    </div>

                    <div>
                        <div class="scholarship-detail-kicker">
                            ข้อมูลผู้ขอรับทุนการศึกษา
                        </div>

                        <h5 class="scholarship-detail-title"
                            id="showChildModalLabel{{ $child->id }}">
                            {{ $child->first_name }}
                            {{ $child->last_name }}
                        </h5>
                    </div>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="ปิด">
                </button>

            </div>

            <div class="modal-body scholarship-detail-body">

                <div class="scholarship-detail-layout">

                    {{-- ข้อมูลประจำตัว --}}
                    <aside class="scholarship-profile-panel">

                        @if($modalPhotoUrl)
                            <img src="{{ $modalPhotoUrl }}"
                                 class="scholarship-profile-photo"
                                 alt="{{ $child->first_name }} {{ $child->last_name }}">
                        @else
                            <div class="scholarship-profile-avatar">
                                {{ mb_substr($child->first_name, 0, 1) }}
                            </div>
                        @endif

                        <h6 class="scholarship-profile-name">
                            {{ $child->first_name }}
                            {{ $child->last_name }}
                        </h6>

                        <div class="scholarship-profile-subtitle">
                            {{ $child->education_level ?? 'ไม่ระบุระดับการศึกษา' }}
                        </div>

                        <div class="scholarship-profile-status">
                            <span class="sc-status-badge {{ $child->status_badge_class }} mb-0">

                                @if(
                                    $child->scholarship_status
                                    === \App\Models\ScholarshipChild::STATUS_APPROVED
                                )
                                    <i class="bi bi-check-circle-fill"></i>

                                @elseif(
                                    $child->scholarship_status
                                    === \App\Models\ScholarshipChild::STATUS_REJECTED
                                )
                                    <i class="bi bi-x-circle-fill"></i>

                                @else
                                    <i class="bi bi-clock-fill"></i>
                                @endif

                                {{ $child->status_label }}
                            </span>
                        </div>

                        <div class="scholarship-profile-meta">

                            <div class="scholarship-profile-meta-row">
                                <span class="scholarship-profile-meta-label">
                                    อายุ
                                </span>

                                <span class="scholarship-profile-meta-value">
                                    {{ $child->age ? $child->age . ' ปี' : '-' }}
                                </span>
                            </div>

                            <div class="scholarship-profile-meta-row">
                                <span class="scholarship-profile-meta-label">
                                    เพศ
                                </span>

                                <span class="scholarship-profile-meta-value">
                                    {{ $genderLabel }}
                                </span>
                            </div>

                            <div class="scholarship-profile-meta-row">
                                <span class="scholarship-profile-meta-label">
                                    ปีการศึกษา
                                </span>

                                <span class="scholarship-profile-meta-value">
                                    {{ $child->academic_year ?? '-' }}
                                </span>
                            </div>

                            <div class="scholarship-profile-meta-row">
                                <span class="scholarship-profile-meta-label">
                                    โทรศัพท์
                                </span>

                                <span class="scholarship-profile-meta-value">
                                    {{ $child->phone ?? '-' }}
                                </span>
                            </div>

                        </div>

                    </aside>

                    {{-- รายละเอียดด้านขวา --}}
                    <div class="scholarship-detail-content">

                        {{-- ข้อมูลการศึกษาและผู้ปกครอง --}}
                        <section class="scholarship-detail-section">

                            <div class="scholarship-detail-section-header">

                                <div class="scholarship-detail-section-icon">
                                    <i class="bi bi-mortarboard"></i>
                                </div>

                                <h6 class="scholarship-detail-section-title">
                                    ข้อมูลการศึกษาและผู้ปกครอง
                                </h6>

                            </div>

                            <div class="scholarship-info-grid">

                                <div class="scholarship-info-item">

                                    <div class="scholarship-info-label">
                                        ระดับการศึกษา
                                    </div>

                                    <div class="scholarship-info-value">
                                        {{ $child->education_level ?? '-' }}
                                    </div>

                                </div>

                                <div class="scholarship-info-item">

                                    <div class="scholarship-info-label">
                                        สถานศึกษา
                                    </div>

                                    <div class="scholarship-info-value">
                                        {{ $child->school_name ?? '-' }}
                                    </div>

                                </div>

                                <div class="scholarship-info-item">

                                    <div class="scholarship-info-label">
                                        ชื่อผู้ปกครอง
                                    </div>

                                    <div class="scholarship-info-value">
                                        {{ $child->guardian_name ?? '-' }}
                                    </div>

                                </div>

                                <div class="scholarship-info-item">

                                    <div class="scholarship-info-label">
                                        เบอร์โทรศัพท์
                                    </div>

                                    <div class="scholarship-info-value">
                                        {{ $child->phone ?? '-' }}
                                    </div>

                                </div>

                                <div class="scholarship-info-item full">

                                    <div class="scholarship-info-label">
                                        ที่อยู่ปัจจุบัน
                                    </div>

                                    <div class="scholarship-info-value">
                                        {{ $child->current_address ?? '-' }}
                                    </div>

                                </div>

                            </div>

                        </section>


                        {{-- ประวัติการยื่นขอทุนทุกปี/ภาคเรียนของบุคคลเดียวกัน --}}
                        <section class="scholarship-detail-section">
                            <div class="scholarship-detail-section-header">
                                <div class="scholarship-detail-section-icon">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <h6 class="scholarship-detail-section-title">
                                    ประวัติการยื่นขอทุนทุกปีและภาคเรียน
                                </h6>
                            </div>

                            <div class="scholarship-application-history">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>ปี / ภาคเรียน</th>
                                            <th>ระดับการศึกษา</th>
                                            <th>สถานศึกษา</th>
                                            <th>สถานะ</th>
                                            <th class="text-end">ค่าใช้จ่าย</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($personApplications as $application)
                                            <tr class="{{ $application->id === $child->id ? 'table-primary' : '' }}">
                                                <td class="text-nowrap">
                                                    {{ $application->academic_year }} /
                                                    ภาคเรียนที่ {{ $application->semester }}
                                                </td>
                                                <td>{{ $application->education_level ?? '-' }}</td>
                                                <td>{{ $application->school_name ?? '-' }}</td>
                                                <td>
                                                    <span class="sc-status-badge {{ $application->status_badge_class }} mb-0">
                                                        {{ $application->status_label }}
                                                    </span>
                                                </td>
                                                <td class="text-end text-nowrap fw-bold">
                                                    {{ number_format($application->expenses->sum('total_amount'), 2) }} บาท
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        {{-- ข้อมูลการขอรับทุน --}}
                        <section class="scholarship-detail-section">

                            <div class="scholarship-detail-section-header">

                                <div class="scholarship-detail-section-icon">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>

                                <h6 class="scholarship-detail-section-title">
                                    ข้อมูลการขอรับทุน
                                </h6>

                            </div>

                            <div class="scholarship-info-grid">

                                <div class="scholarship-info-item">
                                    <div class="scholarship-info-label">
                                        รอบคำขอที่กำลังดู
                                    </div>
                                    <div class="scholarship-info-value text-nowrap">
                                        {{ $child->academic_year }} / ภาคเรียนที่ {{ $child->semester }}
                                    </div>
                                </div>

                                <div class="scholarship-info-item">
                                    <div class="scholarship-info-label">
                                        จำนวนคำขอทั้งหมดของบุคคลนี้
                                    </div>
                                    <div class="scholarship-info-value">
                                        {{ number_format($personApplications->count()) }} รอบ
                                    </div>
                                </div>

                                <div class="scholarship-info-item full">

                                    <div class="scholarship-info-label">
                                        สาเหตุที่ขอรับทุน
                                    </div>

                                    <div class="scholarship-info-value">
                                        {{ $child->reason ?? '-' }}
                                    </div>

                                </div>

                                <div class="scholarship-info-item full">

                                    <div class="scholarship-info-label">
                                        ความต้องการความช่วยเหลือ
                                    </div>

                                    <div class="scholarship-info-value">
                                        {{ $child->help_needed ?? '-' }}
                                    </div>

                                </div>

                                <div class="scholarship-info-item full">

                                    <div class="scholarship-info-label">
                                        รายละเอียดเพิ่มเติม
                                    </div>

                                    <div class="scholarship-info-value">
                                        {{ $child->more_detail ?? '-' }}
                                    </div>

                                </div>

                            </div>

                        </section>

                        {{-- สถานะและข้อมูลการได้รับทุน --}}
                        <section class="scholarship-detail-section">

                            <div class="scholarship-detail-section-header">
                                <div class="scholarship-detail-section-icon">
                                    <i class="bi bi-cash-stack"></i>
                                </div>

                                <h6 class="scholarship-detail-section-title">
                                    สรุปการได้รับทุนและค่าใช้จ่าย
                                </h6>
                            </div>

                            @if($child->isApproved())

                                {{-- ยอดรวมทุกปีของเด็กคนนี้ --}}
                                <div class="scholarship-fund-summary">
                                    <div class="scholarship-fund-metric">
                                        <div class="scholarship-fund-metric-label">
                                            ยอดรวมทุกปีการศึกษา
                                        </div>

                                        <div class="scholarship-fund-metric-value success">
                                            {{ number_format($expenseTotal, 2) }}
                                            <small>บาท</small>
                                        </div>
                                    </div>

                                    <div class="scholarship-fund-metric">
                                        <div class="scholarship-fund-metric-label">
                                            จำนวนปีที่มีค่าใช้จ่าย
                                        </div>

                                        <div class="scholarship-fund-metric-value">
                                            {{ number_format($expenseYearSummaryForChild->count()) }}
                                            <small>ปี</small>
                                        </div>
                                    </div>

                                    <div class="scholarship-fund-metric">
                                        <div class="scholarship-fund-metric-label">
                                            จำนวนครั้งที่บันทึก
                                        </div>

                                        <div class="scholarship-fund-metric-value">
                                            {{ number_format($expenseRecordCount) }}
                                            <small>ครั้ง</small>
                                        </div>
                                    </div>

                                    <div class="scholarship-fund-metric">
                                        <div class="scholarship-fund-metric-label">
                                            เอกสารทั้งหมด
                                        </div>

                                        <div class="scholarship-fund-metric-value">
                                            {{ number_format($expenseDocumentCount + $gradeReportCount) }}
                                            <small>ไฟล์</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- ยอดรวมแยกตามปีการศึกษา --}}
                                @if($expenseYearSummaryForChild->isNotEmpty())
                                    <div class="scholarship-year-summary">
                                        @foreach($expenseYearSummaryForChild as $yearSummary)
                                            <div class="scholarship-year-card">
                                                <div class="scholarship-year-card-header">
                                                    <div class="scholarship-year-card-title">
                                                        ปีการศึกษา {{ $yearSummary['academic_year'] }}
                                                    </div>

                                                    <div class="scholarship-year-card-total">
                                                        {{ number_format($yearSummary['total_amount'], 2) }}
                                                        <small>บาท</small>
                                                    </div>
                                                </div>

                                                <div class="scholarship-year-card-grid">
                                                    <div class="scholarship-year-card-item">
                                                        <div class="scholarship-year-card-item-label">
                                                            ภาคเรียนที่ 1
                                                        </div>

                                                        <div class="scholarship-year-card-item-value">
                                                            {{ number_format($yearSummary['semester_1_total'], 2) }} บาท
                                                        </div>
                                                    </div>

                                                    <div class="scholarship-year-card-item">
                                                        <div class="scholarship-year-card-item-label">
                                                            ภาคเรียนที่ 2
                                                        </div>

                                                        <div class="scholarship-year-card-item-value">
                                                            {{ number_format($yearSummary['semester_2_total'], 2) }} บาท
                                                        </div>
                                                    </div>

                                                    <div class="scholarship-year-card-item">
                                                        <div class="scholarship-year-card-item-label">
                                                            รายการ / ครั้ง
                                                        </div>

                                                        <div class="scholarship-year-card-item-value">
                                                            {{ number_format($yearSummary['item_count']) }}
                                                            รายการ /
                                                            {{ number_format($yearSummary['record_count']) }}
                                                            ครั้ง
                                                        </div>
                                                    </div>

                                                    <div class="scholarship-year-card-item">
                                                        <div class="scholarship-year-card-item-label">
                                                            เอกสาร / ผลการเรียน
                                                        </div>

                                                        <div class="scholarship-year-card-item-value">
                                                            {{ number_format($yearSummary['expense_document_count']) }}
                                                            /
                                                            {{ number_format($yearSummary['grade_report_count']) }}
                                                            ไฟล์
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="sc-finance-empty mt-3">
                                        <i class="bi bi-receipt"></i>
                                        อนุมัติทุนแล้ว แต่ยังไม่มีรายการค่าใช้จ่าย
                                    </div>
                                @endif

                                @if($latestExpense)
                                    <div class="scholarship-info-grid mt-3">
                                        <div class="scholarship-info-item">
                                            <div class="scholarship-info-label">
                                                บันทึกล่าสุด
                                            </div>

                                            <div class="scholarship-info-value">
                                                {{ $latestExpenseDateText }}
                                            </div>
                                        </div>

                                        <div class="scholarship-info-item">
                                            <div class="scholarship-info-label">
                                                ปีการศึกษา / ภาคเรียนล่าสุด
                                            </div>

                                         <div class="scholarship-info-value text-nowrap">
                                            {{ $latestExpense->academic_year }} / ภาคเรียนที่ {{ $latestExpense->semester }}
                                        </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="scholarship-status-note approved">
                                    <i class="bi bi-check-circle-fill mt-1"></i>

                                    <div>
                                        เด็กได้รับการอนุมัติทุนการศึกษาแล้ว
                                        และข้อมูลจะไม่แสดงในหน้ารายงานสาธารณะ
                                        สำหรับผู้ขอรับทุนที่กำลังรอการพิจารณา
                                    </div>
                                </div>

                            @elseif(
                                $child->scholarship_status
                                === \App\Models\ScholarshipChild::STATUS_REJECTED
                            )

                                <div class="scholarship-status-note rejected">
                                    <i class="bi bi-x-circle-fill mt-1"></i>

                                    <div>
                                        รายการนี้ไม่ผ่านการอนุมัติทุนการศึกษา
                                        จึงไม่สามารถบันทึกรายการค่าใช้จ่ายได้
                                    </div>
                                </div>

                            @else

                                <div class="scholarship-status-note pending">
                                    <i class="bi bi-clock-fill mt-1"></i>

                                    <div>
                                        ปัจจุบันอยู่ระหว่างรอการพิจารณา
                                        ปุ่มบันทึกรายการค่าใช้จ่ายจะแสดง
                                        เมื่อเปลี่ยนสถานะเป็นอนุมัติทุนการศึกษาแล้ว
                                    </div>
                                </div>

                            @endif

                            <div class="scholarship-info-grid mt-3">
                                <div class="scholarship-info-item full">
                                    <div class="scholarship-info-label">
                                        ปรับสถานะล่าสุด
                                    </div>

                                    <div class="scholarship-info-value">
                                        {{ $statusUpdatedText }}
                                    </div>
                                </div>
                            </div>

                        </section>

                    </div>

                </div>

            </div>

            <div class="modal-footer scholarship-detail-footer">

                <button type="button"
                        class="btn btn-secondary px-4"
                        data-bs-dismiss="modal">
                    ปิด
                </button>

            </div>

        </div>

    </div>

</div>

    {{-- Modal แก้ไข --}}
    <div class="modal fade" id="editChildModal{{ $child->id }}" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <form action="{{ route('scholarship.children.update', $child->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="modal-content">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-1"></i>
                        แก้ไขข้อมูลผู้ขอรับทุน
                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="ปิด">
                    </button>
                </div>

                <div class="modal-body">
                    @include('landing.scholarship.children.partials.form', [
                        'child' => $child,
                        'yearListId' => 'academic_year_edit_' . $child->id
                    ])
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        ยกเลิก
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>
                        บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal บันทึกรายการค่าใช้จ่าย แสดงเฉพาะผู้ที่อนุมัติทุนแล้ว --}}
    @if($child->isApproved())
        <div class="modal fade" id="expenseModal{{ $child->id }}" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <form action="{{ route('scholarship.children.expenses.store', $child->id) }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="modal-content scholarship-expense-form"
                      data-child-id="{{ $child->id }}">
                    @csrf

                    <input type="hidden"
                           name="expense_child_id"
                           value="{{ $child->id }}">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-cash-coin me-1"></i>
                            บันทึกรายการค่าใช้จ่ายทุนการศึกษา
                        </h5>
                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="expense-child-summary">
                            <div class="detail-box">
                                <div class="detail-label">ผู้ได้รับทุน</div>
                                <div class="detail-value">
                                    {{ $child->first_name }} {{ $child->last_name }}
                                </div>
                            </div>

                            <div class="detail-box">
                                <div class="detail-label">ปีการศึกษา</div>
                                <div class="detail-value">
                                    {{ $child->academic_year }}
                                </div>
                            </div>

                            <div class="detail-box">
                                <div class="detail-label">ภาคเรียน</div>
                                <div class="detail-value">
                                    ภาคเรียนที่ {{ $child->semester }}
                                </div>
                            </div>
                        </div>

                        <div class="expense-section mb-3">
                            <div class="expense-section-title">
                                <h6>
                                    <i class="bi bi-calendar-check me-1"></i>
                                    ข้อมูลการบันทึก
                                </h6>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">
                                        วันที่บันทึก <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           name="record_date"
                                           class="form-control"
                                           max="{{ now()->format('Y-m-d') }}"
                                           value="{{ $isOldExpenseChild ? old('record_date', now()->format('Y-m-d')) : now()->format('Y-m-d') }}"
                                           required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">ปีการศึกษา</label>
                                    <input type="text"
                                           class="form-control"
                                           value="{{ $child->academic_year }}"
                                           readonly>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        ภาคเรียนที่ <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           value="ภาคเรียนที่ {{ $child->semester }}"
                                           readonly>
                                    <input type="hidden"
                                           name="semester"
                                           value="{{ $child->semester }}">
                                </div>
                            </div>
                        </div>

                        <div class="expense-section mb-3">
                            <div class="expense-section-title">
                                <h6>
                                    <i class="bi bi-list-check me-1"></i>
                                    รายการค่าใช้จ่าย
                                </h6>

                                <button type="button"
                                        class="btn btn-outline-primary btn-sm add-expense-row">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    เพิ่มรายการ
                                </button>
                            </div>

                            <div class="expense-items-wrap">
                                <table class="table align-middle expense-items-table">
                                    <thead>
                                        <tr>
                                            <th width="65" class="text-center">ลำดับ</th>
                                            <th>รายการ</th>
                                            <th width="230">จำนวนเงิน (บาท)</th>
                                            <th width="90" class="text-center">จัดการ</th>
                                        </tr>
                                    </thead>

                                    <tbody class="expense-items-body">
                                        @foreach($expenseRows as $rowIndex => $expenseRow)
                                            <tr>
                                                <td class="text-center row-number">
                                                    {{ $loop->iteration }}
                                                </td>

                                                <td>
                                                    <select name="items[{{ $rowIndex }}][expense_type]"
                                                            class="form-select"
                                                            required>
                                                        <option value="">เลือกรายการ</option>
                                                        @foreach([
                                                            'ทุนการศึกษา',
                                                            'ค่าเทอม',
                                                            'ค่าหอพัก',
                                                            'ค่าเดินทาง', 
                                                            'ค่าอุปกรณ์การเรียน',
                                                            'ค่าอาหาร',
                                                            'ค่าใช้จ่ายอื่น ๆ'
                                                        ] as $expenseType)
                                                            <option value="{{ $expenseType }}"
                                                                {{ ($expenseRow['expense_type'] ?? '') === $expenseType ? 'selected' : '' }}>
                                                                {{ $expenseType }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>

                                                <td>
                                                    <input type="number"
                                                           name="items[{{ $rowIndex }}][amount]"
                                                           class="form-control expense-amount text-end"
                                                           min="0.01"
                                                           max="99999999.99"
                                                           step="0.01"
                                                           value="{{ $expenseRow['amount'] ?? '' }}"
                                                           placeholder="0.00"
                                                           required>
                                                </td>

                                                <td class="text-center">
                                                    <button type="button"
                                                            class="btn btn-outline-danger btn-sm remove-expense-row"
                                                            title="ลบรายการ">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="expense-total-box">
                                <span>ยอดรวมทั้งสิ้น</span>
                                <span class="expense-total">0.00</span>
                                <span>บาท</span>
                            </div>
                        </div>

                        <div class="expense-section mb-3">
                            <div class="expense-section-title">
                                <h6>
                                    <i class="bi bi-paperclip me-1"></i>
                                    เอกสารประกอบ
                                </h6>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="pdf-upload-card">
                                        <h6>
                                            <i class="bi bi-receipt-cutoff me-1"></i>
                                            เอกสารรายการค่าใช้จ่าย
                                        </h6>
                                        <p>เลือกไฟล์ PDF ได้หลายไฟล์ ไฟล์ละไม่เกิน 10 MB</p>

                                        <input type="file"
                                               name="expense_documents[]"
                                               class="form-control pdf-input"
                                               accept="application/pdf,.pdf"
                                               multiple>

                                        <span class="file-count">
                                            ยังไม่ได้เลือกไฟล์
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="pdf-upload-card">
                                        <h6>
                                            <i class="bi bi-file-earmark-bar-graph me-1"></i>
                                            ผลการเรียน
                                        </h6>
                                        <p>เลือกไฟล์ PDF ได้หลายไฟล์ ไฟล์ละไม่เกิน 10 MB</p>

                                        <input type="file"
                                               name="grade_reports[]"
                                               class="form-control pdf-input"
                                               accept="application/pdf,.pdf"
                                               multiple>

                                        <span class="file-count">
                                            ยังไม่ได้เลือกไฟล์
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">หมายเหตุ</label>
                                    <textarea name="note"
                                              class="form-control"
                                              rows="3"
                                              placeholder="ระบุรายละเอียดเพิ่มเติมเกี่ยวกับการเบิกจ่าย">{{ $isOldExpenseChild ? old('note') : '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="expense-section">
                            <div class="expense-section-title">
                                <h6>
                                    <i class="bi bi-clock-history me-1"></i>
                                    ประวัติรายการค่าใช้จ่าย
                                </h6>
                            </div>

                            @if($child->expenses->count() > 0)
                                <div class="table-responsive border rounded-3">
                                    <table class="table align-middle expense-history-table">
                                        <thead>
                                            <tr>
                                                <th>วันที่</th>
                                                <th>ภาคเรียน</th>
                                                <th>รายการ</th>
                                                <th class="text-end">ยอดรวม</th>
                                                <th>เอกสารค่าใช้จ่าย</th>
                                                <th>ผลการเรียน</th>
                                                <th width="82" class="text-center">จัดการ</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach($expenseYearGroups as $historyYear => $yearExpenses)
                                                @php
                                                    $historyYearTotal = (float) $yearExpenses
                                                        ->sum('total_amount');
                                                @endphp

                                                <tr class="expense-year-group-row">
                                                    <td colspan="7">
                                                        <div class="expense-year-group-content">
                                                            <span>
                                                                <i class="bi bi-calendar3 me-1"></i>
                                                                ปีการศึกษา {{ $historyYear }}
                                                                ·
                                                                {{ number_format($yearExpenses->count()) }}
                                                                ครั้ง
                                                            </span>

                                                            <span class="expense-year-subtotal">
                                                                รวมปีนี้
                                                                {{ number_format($historyYearTotal, 2) }}
                                                                บาท
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>

                                                @foreach($yearExpenses as $expense)
                                                    <tr>
                                                        <td>
                                                            @if($expense->record_date)
                                                                {{ $expense->record_date->format('d/m/') }}{{ $expense->record_date->year + 543 }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>

                                                        <td>
                                                            ภาคเรียนที่ {{ $expense->semester }}
                                                        </td>

                                                        <td>
                                                            @foreach($expense->items as $item)
                                                                <div>
                                                                    {{ $item->expense_type }}
                                                                    <span class="text-muted">
                                                                        ({{ number_format($item->amount, 2) }} บาท)
                                                                    </span>
                                                                </div>
                                                            @endforeach
                                                        </td>

                                                        <td class="text-end fw-bold text-success">
                                                            {{ number_format($expense->total_amount, 2) }} บาท
                                                        </td>

                                                        <td>
                                                            @forelse($expense->attachments->where('category', 'expense_document') as $attachment)
                                                                <a href="{{ asset($attachment->file_path) }}"
                                                                   target="_blank"
                                                                   rel="noopener"
                                                                   class="attachment-link">
                                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                                    {{ \Illuminate\Support\Str::limit($attachment->original_name, 24) }}
                                                                </a>
                                                            @empty
                                                                <span class="text-muted">-</span>
                                                            @endforelse
                                                        </td>

                                                        <td>
                                                            @forelse($expense->attachments->where('category', 'grade_report') as $attachment)
                                                                <a href="{{ asset($attachment->file_path) }}"
                                                                   target="_blank"
                                                                   rel="noopener"
                                                                   class="attachment-link">
                                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                                    {{ \Illuminate\Support\Str::limit($attachment->original_name, 24) }}
                                                                </a>
                                                            @empty
                                                                <span class="text-muted">-</span>
                                                            @endforelse
                                                        </td>

                                                        <td class="text-center">
                                                            <div class="expense-history-actions">
                                                                <button type="button"
                                                                        class="btn expense-history-edit-btn open-edit-expense-modal"
                                                                        data-current-modal="expenseModal{{ $child->id }}"
                                                                        data-target-modal="editExpenseModal{{ $expense->id }}"
                                                                        title="แก้ไขรายการค่าใช้จ่าย"
                                                                        aria-label="แก้ไขรายการค่าใช้จ่าย ปีการศึกษา {{ $expense->academic_year }} ภาคเรียนที่ {{ $expense->semester }}">
                                                                    <i class="bi bi-pencil-square"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>

                                        <tfoot>
                                            <tr class="expense-grand-total-row">
                                                <td colspan="3">
                                                    ยอดรวมทุกปีการศึกษา
                                                </td>

                                                <td class="text-end">
                                                    {{ number_format($expenseTotal, 2) }} บาท
                                                </td>

                                                <td colspan="3">
                                                    {{ number_format($expenseRecordCount) }}
                                                    ครั้ง
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-receipt fs-2 d-block mb-2"></i>
                                    ยังไม่มีประวัติรายการค่าใช้จ่าย
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            ยกเลิก
                        </button>

                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save me-1"></i>
                            บันทึกรายการค่าใช้จ่าย
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal แก้ไขรายการค่าใช้จ่ายเดิม --}}
        @foreach($child->expenses as $expense)
            @php
                $isOldEditExpense =
                    (string) old('edit_expense_id')
                    === (string) $expense->id;

                $editExpenseRows = $isOldEditExpense
                    ? old('items', [])
                    : $expense->items
                        ->map(function ($item) {
                            return [
                                'expense_type' => $item->expense_type,
                                'amount' => $item->amount,
                            ];
                        })
                        ->values()
                        ->all();

                if (empty($editExpenseRows)) {
                    $editExpenseRows = [
                        [
                            'expense_type' => '',
                            'amount' => '',
                        ],
                    ];
                }

                $editRemovedAttachmentIds = $isOldEditExpense
                    ? collect(old('remove_attachment_ids', []))
                        ->map(function ($id) {
                            return (int) $id;
                        })
                    : collect();

                $editExpenseDocumentAttachments =
                    $expense->attachments->where(
                        'category',
                        'expense_document'
                    );

                $editGradeReportAttachments =
                    $expense->attachments->where(
                        'category',
                        'grade_report'
                    );
            @endphp

            <div class="modal fade"
                 id="editExpenseModal{{ $expense->id }}"
                 tabindex="-1"
                 aria-labelledby="editExpenseModalLabel{{ $expense->id }}"
                 aria-hidden="true">

                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable expense-edit-dialog">

                    <form action="{{ route('scholarship.children.expenses.update', [
                                'child' => $child->id,
                                'expense' => $expense->id,
                            ]) }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="modal-content scholarship-expense-form expense-edit-modal"
                          data-child-id="{{ $child->id }}"
                          data-expense-id="{{ $expense->id }}">

                        @csrf
                        @method('PUT')

                        <input type="hidden"
                               name="edit_expense_id"
                               value="{{ $expense->id }}">

                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title"
                                    id="editExpenseModalLabel{{ $expense->id }}">
                                    <i class="bi bi-pencil-square me-1"></i>
                                    แก้ไขรายการค่าใช้จ่ายทุนการศึกษา
                                </h5>

                                <div class="text-muted small mt-1">
                                    ปีการศึกษา {{ $expense->academic_year }}
                                    · ภาคเรียนที่ {{ $expense->semester }}
                                </div>
                            </div>

                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="ปิด">
                            </button>
                        </div>

                        <div class="modal-body">

                            <div class="expense-edit-summary">
                                <div class="detail-box">
                                    <div class="detail-label">
                                        ผู้ได้รับทุน
                                    </div>

                                    <div class="detail-value">
                                        {{ $child->first_name }}
                                        {{ $child->last_name }}
                                    </div>
                                </div>

                                <div class="detail-box">
                                    <div class="detail-label">
                                        ปีการศึกษา
                                    </div>

                                    <div class="detail-value">
                                        {{ $expense->academic_year }}
                                    </div>
                                </div>

                                <div class="detail-box">
                                    <div class="detail-label">
                                        ยอดเดิม
                                    </div>

                                    <div class="detail-value text-success">
                                        {{ number_format($expense->total_amount, 2) }}
                                        บาท
                                    </div>
                                </div>
                            </div>

                            <div class="expense-edit-note mb-3">
                                <i class="bi bi-info-circle-fill mt-1"></i>

                                <div>
                                    เมื่อบันทึกการแก้ไข ระบบจะคำนวณยอดรวมใหม่
                                    และปรับยอดรวมของปีการศึกษา รวมถึงยอดรวมทั้งหมด
                                    โดยอัตโนมัติ
                                </div>
                            </div>

                            <div class="expense-section mb-3">
                                <div class="expense-section-title">
                                    <h6>
                                        <i class="bi bi-calendar-check me-1"></i>
                                        ข้อมูลการบันทึก
                                    </h6>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            วันที่บันทึก
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input type="date"
                                               name="record_date"
                                               class="form-control"
                                               max="{{ now()->format('Y-m-d') }}"
                                               value="{{ $isOldEditExpense
                                                    ? old('record_date')
                                                    : optional($expense->record_date)->format('Y-m-d') }}"
                                               required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">
                                            ปีการศึกษา
                                        </label>

                                        <input type="text"
                                               class="form-control"
                                               value="{{ $expense->academic_year }}"
                                               readonly>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">
                                            ภาคเรียนที่
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input type="text"
                                               class="form-control"
                                               value="ภาคเรียนที่ {{ $child->semester }}"
                                               readonly>
                                        <input type="hidden"
                                               name="semester"
                                               value="{{ $child->semester }}">
                                    </div>
                                </div>
                            </div>

                            <div class="expense-section mb-3">
                                <div class="expense-section-title">
                                    <h6>
                                        <i class="bi bi-list-check me-1"></i>
                                        รายการค่าใช้จ่าย
                                    </h6>

                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm add-expense-row">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        เพิ่มรายการ
                                    </button>
                                </div>

                                <div class="expense-items-wrap">
                                    <table class="table align-middle expense-items-table">
                                        <thead>
                                            <tr>
                                                <th width="65"
                                                    class="text-center">
                                                    ลำดับ
                                                </th>

                                                <th>
                                                    รายการ
                                                </th>

                                                <th width="230">
                                                    จำนวนเงิน (บาท)
                                                </th>

                                                <th width="90"
                                                    class="text-center">
                                                    จัดการ
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody class="expense-items-body">
                                            @foreach($editExpenseRows as $rowIndex => $editExpenseRow)
                                                <tr>
                                                    <td class="text-center row-number">
                                                        {{ $loop->iteration }}
                                                    </td>

                                                    <td>
                                                        <select name="items[{{ $rowIndex }}][expense_type]"
                                                                class="form-select"
                                                                required>
                                                            <option value="">
                                                                เลือกรายการ
                                                            </option>

                                                            @foreach([
                                                                'ทุนการศึกษา',
                                                                'ค่าเทอม',
                                                                'ค่าหอพัก',
                                                                'ค่าเดินทาง',
                                                                'ค่าอุปกรณ์การเรียน',
                                                                'ค่าอาหาร',
                                                                'ค่าใช้จ่ายอื่น ๆ'
                                                            ] as $expenseType)
                                                                <option value="{{ $expenseType }}"
                                                                    {{ ($editExpenseRow['expense_type'] ?? '')
                                                                        === $expenseType
                                                                            ? 'selected'
                                                                            : '' }}>
                                                                    {{ $expenseType }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>

                                                    <td>
                                                        <input type="number"
                                                               name="items[{{ $rowIndex }}][amount]"
                                                               class="form-control expense-amount text-end"
                                                               min="0.01"
                                                               max="99999999.99"
                                                               step="0.01"
                                                               value="{{ $editExpenseRow['amount'] ?? '' }}"
                                                               placeholder="0.00"
                                                               required>
                                                    </td>

                                                    <td class="text-center">
                                                        <button type="button"
                                                                class="btn btn-outline-danger btn-sm remove-expense-row"
                                                                title="ลบรายการ">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="expense-total-box">
                                    <span>
                                        ยอดรวมใหม่
                                    </span>

                                    <span class="expense-total">
                                        0.00
                                    </span>

                                    <span>
                                        บาท
                                    </span>
                                </div>
                            </div>

                            <div class="expense-section mb-3">
                                <div class="expense-section-title">
                                    <h6>
                                        <i class="bi bi-folder2-open me-1"></i>
                                        เอกสารที่บันทึกไว้
                                    </h6>
                                </div>

                                @if(
                                    $editExpenseDocumentAttachments->isNotEmpty()
                                    || $editGradeReportAttachments->isNotEmpty()
                                )
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">
                                                เอกสารรายการค่าใช้จ่าย
                                            </label>

                                            @if($editExpenseDocumentAttachments->isNotEmpty())
                                                <div class="expense-existing-files">
                                                    @foreach($editExpenseDocumentAttachments as $attachment)
                                                        <label class="expense-existing-file">
                                                            <input type="checkbox"
                                                                   name="remove_attachment_ids[]"
                                                                   value="{{ $attachment->id }}"
                                                                   class="form-check-input"
                                                                   {{ $editRemovedAttachmentIds->contains(
                                                                        (int) $attachment->id
                                                                    )
                                                                        ? 'checked'
                                                                        : '' }}>

                                                            <span class="expense-existing-file-content">
                                                                <a href="{{ asset($attachment->file_path) }}"
                                                                   target="_blank"
                                                                   rel="noopener"
                                                                   class="expense-existing-file-name">
                                                                    <i class="bi bi-file-earmark-pdf me-1"></i>
                                                                    {{ $attachment->original_name }}
                                                                </a>

                                                                <span class="expense-existing-file-hint">
                                                                    เลือกช่องนี้เมื่อต้องการลบไฟล์
                                                                </span>
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-muted small">
                                                    ไม่มีไฟล์
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">
                                                ผลการเรียน
                                            </label>

                                            @if($editGradeReportAttachments->isNotEmpty())
                                                <div class="expense-existing-files">
                                                    @foreach($editGradeReportAttachments as $attachment)
                                                        <label class="expense-existing-file">
                                                            <input type="checkbox"
                                                                   name="remove_attachment_ids[]"
                                                                   value="{{ $attachment->id }}"
                                                                   class="form-check-input"
                                                                   {{ $editRemovedAttachmentIds->contains(
                                                                        (int) $attachment->id
                                                                    )
                                                                        ? 'checked'
                                                                        : '' }}>

                                                            <span class="expense-existing-file-content">
                                                                <a href="{{ asset($attachment->file_path) }}"
                                                                   target="_blank"
                                                                   rel="noopener"
                                                                   class="expense-existing-file-name">
                                                                    <i class="bi bi-file-earmark-pdf me-1"></i>
                                                                    {{ $attachment->original_name }}
                                                                </a>

                                                                <span class="expense-existing-file-hint">
                                                                    เลือกช่องนี้เมื่อต้องการลบไฟล์
                                                                </span>
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-muted small">
                                                    ไม่มีไฟล์
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="text-muted small">
                                        รายการนี้ยังไม่มีไฟล์เอกสาร
                                    </div>
                                @endif
                            </div>

                            <div class="expense-section">
                                <div class="expense-section-title">
                                    <h6>
                                        <i class="bi bi-paperclip me-1"></i>
                                        เพิ่มเอกสารใหม่
                                    </h6>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="pdf-upload-card">
                                            <h6>
                                                <i class="bi bi-receipt-cutoff me-1"></i>
                                                เอกสารรายการค่าใช้จ่าย
                                            </h6>

                                            <p>
                                                เลือกไฟล์ PDF เพิ่มได้หลายไฟล์
                                                ไฟล์ละไม่เกิน 10 MB
                                            </p>

                                            <input type="file"
                                                   name="expense_documents[]"
                                                   class="form-control pdf-input"
                                                   accept="application/pdf,.pdf"
                                                   multiple>

                                            <span class="file-count">
                                                ยังไม่ได้เลือกไฟล์
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="pdf-upload-card">
                                            <h6>
                                                <i class="bi bi-file-earmark-bar-graph me-1"></i>
                                                ผลการเรียน
                                            </h6>

                                            <p>
                                                เลือกไฟล์ PDF เพิ่มได้หลายไฟล์
                                                ไฟล์ละไม่เกิน 10 MB
                                            </p>

                                            <input type="file"
                                                   name="grade_reports[]"
                                                   class="form-control pdf-input"
                                                   accept="application/pdf,.pdf"
                                                   multiple>

                                            <span class="file-count">
                                                ยังไม่ได้เลือกไฟล์
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">
                                            หมายเหตุ
                                        </label>

                                        <textarea name="note"
                                                  class="form-control"
                                                  rows="3"
                                                  placeholder="ระบุรายละเอียดเพิ่มเติมเกี่ยวกับการเบิกจ่าย">{{ $isOldEditExpense
                                                        ? old('note')
                                                        : $expense->note }}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">
                                ยกเลิก
                            </button>

                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>
                                บันทึกการแก้ไข
                            </button>
                        </div>

                    </form>

                </div>

            </div>
        @endforeach
    @endif
@endforeach

<template id="expenseRowTemplate">
    <tr>
        <td class="text-center row-number"></td>

        <td>
            <select class="form-select expense-type-select" required>
                <option value="">เลือกรายการ</option>
                <option value="ทุนการศึกษา">ทุนการศึกษา</option>
                <option value="ค่าเทอม">ค่าเทอม</option>
                <option value="ค่าหอพัก">ค่าหอพัก</option>
                <option value="ค่าเดินทาง">ค่าเดินทาง</option>
                <option value="ค่าอุปกรณ์การเรียน">ค่าอุปกรณ์การเรียน</option>
                <option value="ค่าอาหาร">ค่าอาหาร</option>
                <option value="ค่าใช้จ่ายอื่น ๆ">ค่าใช้จ่ายอื่น ๆ</option>
            </select>
        </td>

        <td>
            <input type="number"
                   class="form-control expense-amount text-end"
                   min="0.01"
                   max="99999999.99"
                   step="0.01"
                   placeholder="0.00"
                   required>
        </td>

        <td class="text-center">
            <button type="button"
                    class="btn btn-outline-danger btn-sm remove-expense-row"
                    title="ลบรายการ">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const successAlert = document.getElementById('successAlert');

    if (successAlert) {
        setTimeout(function () {
            successAlert.style.transition = 'opacity .4s ease';
            successAlert.style.opacity = '0';

            setTimeout(function () {
                successAlert.remove();
            }, 400);
        }, 5000);
    }

    document.querySelectorAll('.delete-child-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const childName = form.getAttribute('data-name') || 'รายการนี้';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'ยืนยันการลบข้อมูล?',
                    text: 'ต้องการลบข้อมูลของ ' + childName + ' หรือไม่',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ลบข้อมูล',
                    cancelButtonText: 'ยกเลิก'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else if (confirm('ยืนยันการลบข้อมูลของ ' + childName + ' หรือไม่?')) {
                form.submit();
            }
        });
    });

                    document.querySelectorAll('.status-select').forEach(function (select) {
                        select.addEventListener('change', function () {
                            const form = select.closest('form');
                            const oldValue = select.dataset.current;
                            const childName = select.dataset.name || 'ผู้ขอรับทุน';
                            const newLabel = select.options[select.selectedIndex].text;

                        const submitStatus = function () {
                    // ป้องกันการเลือกซ้ำระหว่างกำลังส่งข้อมูล
                    // ห้ามใช้ disabled เพราะค่า select จะไม่ถูกส่งไป Controller
                    select.style.pointerEvents = 'none';
                    select.style.opacity = '0.65';

                    form.submit();
                };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'ยืนยันการเปลี่ยนสถานะ?',
                    html: 'เปลี่ยนสถานะของ <strong>' + childName + '</strong><br>เป็น “' + newLabel + '”',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3157d5',
                    cancelButtonColor: '#667085',
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        submitStatus();
                    } else {
                        select.value = oldValue;
                    }
                });
            } else if (confirm('ยืนยันการเปลี่ยนสถานะเป็น ' + newLabel + ' หรือไม่?')) {
                submitStatus();
            } else {
                select.value = oldValue;
            }
        });
    });


    document.querySelectorAll('.open-edit-expense-modal').forEach(function (button) {
        button.addEventListener('click', function () {
            const currentModalId = button.dataset.currentModal;
            const targetModalId = button.dataset.targetModal;

            const currentModalElement =
                document.getElementById(currentModalId);

            const targetModalElement =
                document.getElementById(targetModalId);

            if (
                !targetModalElement
                || typeof window.bootstrap === 'undefined'
                || !window.bootstrap.Modal
            ) {
                return;
            }

            const showTargetModal = function () {
                const targetModal =
                    window.bootstrap.Modal.getOrCreateInstance(
                        targetModalElement,
                        {
                            backdrop: 'static',
                            keyboard: false
                        }
                    );

                targetModal.show();
            };

            if (!currentModalElement) {
                showTargetModal();
                return;
            }

            const currentModal =
                window.bootstrap.Modal.getInstance(
                    currentModalElement
                );

            if (!currentModal) {
                showTargetModal();
                return;
            }

            currentModalElement.addEventListener(
                'hidden.bs.modal',
                showTargetModal,
                {
                    once: true
                }
            );

            currentModal.hide();
        });
    });

    const keywordInput = document.getElementById('keywordInput');
    const childSearchForm = document.getElementById('childSearchForm');

    if (keywordInput && childSearchForm) {
        let delayTimer;

        keywordInput.addEventListener('input', function () {
            clearTimeout(delayTimer);

            delayTimer = setTimeout(function () {
                childSearchForm.submit();
            }, 500);
        });
    }

    document.querySelectorAll('.scholarship-expense-form').forEach(function (form) {
        const itemsBody = form.querySelector('.expense-items-body');
        const addButton = form.querySelector('.add-expense-row');
        const totalElement = form.querySelector('.expense-total');
        const template = document.getElementById('expenseRowTemplate');

        if (!itemsBody || !addButton || !totalElement || !template) {
            return;
        }

        let nextIndex = itemsBody.querySelectorAll('tr').length;

        function refreshRows() {
            const rows = itemsBody.querySelectorAll('tr');

            rows.forEach(function (row, index) {
                const rowNumber = row.querySelector('.row-number');
                const select = row.querySelector('select');
                const amount = row.querySelector('.expense-amount');

                if (rowNumber) {
                    rowNumber.textContent = index + 1;
                }

                if (select) {
                    select.name = 'items[' + index + '][expense_type]';
                }

                if (amount) {
                    amount.name = 'items[' + index + '][amount]';
                }
            });

            nextIndex = rows.length;
        }

        function calculateTotal() {
            let total = 0;

            itemsBody.querySelectorAll('.expense-amount').forEach(function (input) {
                const amount = parseFloat(input.value);

                if (!Number.isNaN(amount)) {
                    total += amount;
                }
            });

            totalElement.textContent = total.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        addButton.addEventListener('click', function () {
            const fragment = template.content.cloneNode(true);
            itemsBody.appendChild(fragment);
            refreshRows();
            calculateTotal();

            const rows = itemsBody.querySelectorAll('tr');
            const lastRow = rows[rows.length - 1];
            const firstSelect = lastRow ? lastRow.querySelector('select') : null;

            if (firstSelect) {
                firstSelect.focus();
            }
        });

        itemsBody.addEventListener('click', function (event) {
            const removeButton = event.target.closest('.remove-expense-row');

            if (!removeButton) {
                return;
            }

            const rows = itemsBody.querySelectorAll('tr');
            const row = removeButton.closest('tr');

            if (rows.length === 1) {
                const select = row.querySelector('select');
                const amount = row.querySelector('.expense-amount');

                if (select) {
                    select.value = '';
                }

                if (amount) {
                    amount.value = '';
                }
            } else {
                row.remove();
            }

            refreshRows();
            calculateTotal();
        });

        itemsBody.addEventListener('input', calculateTotal);
        itemsBody.addEventListener('change', calculateTotal);

        form.querySelectorAll('.pdf-input').forEach(function (input) {
            input.addEventListener('change', function () {
                const countLabel = input.parentElement.querySelector('.file-count');
                const fileCount = input.files ? input.files.length : 0;

                if (countLabel) {
                    countLabel.textContent = fileCount > 0
                        ? 'เลือกแล้ว ' + fileCount + ' ไฟล์'
                        : 'ยังไม่ได้เลือกไฟล์';
                }
            });
        });

        refreshRows();
        calculateTotal();
    });


    @if(session('open_create_child_modal'))
        const createChildModalElement = document.getElementById('createChildModal');
        if (createChildModalElement && window.bootstrap?.Modal) {
            setTimeout(function () {
                window.bootstrap.Modal.getOrCreateInstance(
                    createChildModalElement,
                    { backdrop: 'static', keyboard: false }
                ).show();
            }, 180);
        }
    @endif

    @if(session('open_edit_child_modal'))
        const editChildId = @json(session('open_edit_child_modal'));
        const editChildModalElement = document.getElementById('editChildModal' + editChildId);
        if (editChildModalElement && window.bootstrap?.Modal) {
            setTimeout(function () {
                window.bootstrap.Modal.getOrCreateInstance(
                    editChildModalElement,
                    { backdrop: 'static', keyboard: false }
                ).show();
            }, 180);
        }
    @endif

    @if(session('open_reapply_modal') || old('reapply_child_id'))
        const reapplyChildId = @json(session('open_reapply_modal') ?? old('reapply_child_id'));
        const reapplyErrors = @json($errors->all());
        const reapplyModalElement = document.getElementById(
            'reapplyChildModal' + reapplyChildId
        );

        function openReapplyModalAfterAlert() {
            if (!reapplyModalElement || !window.bootstrap?.Modal) {
                return;
            }

            window.bootstrap.Modal.getOrCreateInstance(
                reapplyModalElement,
                { backdrop: 'static', keyboard: false }
            ).show();
        }

        if (reapplyErrors.length > 0 && typeof window.Swal !== 'undefined') {
            setTimeout(function () {
                window.Swal.fire({
                    icon: 'warning',
                    title: 'ไม่สามารถสร้างคำขอรอบใหม่ได้',
                    text: reapplyErrors.join('\n'),
                    showConfirmButton: false,
                    timer: 3200,
                    timerProgressBar: true
                }).then(function () {
                    setTimeout(openReapplyModalAfterAlert, 120);
                });
            }, 180);
        } else {
            setTimeout(openReapplyModalAfterAlert, 180);
        }
    @endif

    @if(session('open_expense_modal') || old('expense_child_id'))
        const expenseChildId =
            @json(session('open_expense_modal') ?? old('expense_child_id'));

        const expenseErrors = @json($errors->all());

        const expenseModalElement = document.getElementById(
            'expenseModal' + expenseChildId
        );

        function openExpenseModalAfterAlert() {
            if (
                !expenseModalElement
                || typeof window.bootstrap === 'undefined'
                || !window.bootstrap.Modal
            ) {
                return;
            }

            const expenseModal =
                window.bootstrap.Modal.getOrCreateInstance(
                    expenseModalElement,
                    {
                        backdrop: 'static',
                        keyboard: false
                    }
                );

            expenseModal.show();
        }

        if (expenseErrors.length > 0) {
            setTimeout(function () {
                if (typeof window.Swal !== 'undefined') {
                    window.Swal.fire({
                        icon: 'warning',
                        title: 'ไม่สามารถบันทึกรายการได้',
                        text: expenseErrors.join('\n'),
                        position: 'center',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        backdrop: true
                    }).then(function () {
                        setTimeout(openExpenseModalAfterAlert, 150);
                    });
                } else {
                    alert(expenseErrors.join('\n'));
                    openExpenseModalAfterAlert();
                }
            }, 200);
        } else {
            setTimeout(openExpenseModalAfterAlert, 200);
        }
    @endif

    @if(session('open_edit_expense_modal') || old('edit_expense_id'))
        const editExpenseId =
            @json(
                session('open_edit_expense_modal')
                ?? old('edit_expense_id')
            );

        const editExpenseErrors = @json($errors->all());

        const editExpenseModalElement = document.getElementById(
            'editExpenseModal' + editExpenseId
        );

        function openEditExpenseModalAfterAlert() {
            if (
                !editExpenseModalElement
                || typeof window.bootstrap === 'undefined'
                || !window.bootstrap.Modal
            ) {
                return;
            }

            const editExpenseModal =
                window.bootstrap.Modal.getOrCreateInstance(
                    editExpenseModalElement,
                    {
                        backdrop: 'static',
                        keyboard: false
                    }
                );

            editExpenseModal.show();
        }

        if (editExpenseErrors.length > 0) {
            setTimeout(function () {
                if (typeof window.Swal !== 'undefined') {
                    window.Swal.fire({
                        icon: 'warning',
                        title: 'ไม่สามารถแก้ไขรายการได้',
                        text: editExpenseErrors.join('\n'),
                        position: 'center',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        backdrop: true
                    }).then(function () {
                        setTimeout(
                            openEditExpenseModalAfterAlert,
                            150
                        );
                    });
                } else {
                    alert(editExpenseErrors.join('\n'));
                    openEditExpenseModalAfterAlert();
                }
            }, 200);
        } else {
            setTimeout(openEditExpenseModalAfterAlert, 200);
        }
    @endif
});
</script>

@endsection