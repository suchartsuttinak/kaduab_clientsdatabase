@extends('admin_client.admin_client')

@section('content')
<style>
/* IDP_PRINT_LAYOUT_V3
   โครงพิมพ์ A4 แบบแบ่งหน้าจริง: 1 logical page = 1 physical sheet
   Follow-up แยกเป็น “สรุปผล” และ “ตัวชี้วัด” เพื่อไม่บีบตัวอักษรและไม่ไหลข้ามหน้า */
:root{
    --rp-blue:#0b4f91;
    --rp-blue2:#1e69ad;
    --rp-light:#eef6ff;
    --rp-border:#b8cee4;
    --rp-text:#16324f;
    --rp-muted:#667a8e;
}

*,*::before,*::after{box-sizing:border-box}

.idp-report-screen{padding:10px 0 28px}
.rp-toolbar{max-width:210mm;margin:0 auto 10px;display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap}
.rp-toolbar .btn{border-radius:10px;font-weight:700;min-height:40px}
.rp-toolbar-group{display:flex;gap:8px;flex-wrap:wrap}

.rp-page{
    box-sizing:border-box;
    width:210mm;
    min-height:297mm;
    margin:0 auto 14px;
    background:#fff;
    padding:9mm 10mm 11mm;
    color:var(--rp-text);
    font-family:"TH Sarabun New","Sarabun","Tahoma",sans-serif;
    box-shadow:0 8px 28px rgba(15,45,75,.12);
    position:relative;
}

.rp-title{text-align:center;font-size:23px;font-weight:900;color:#123e73;line-height:1.05}
.rp-title-small{font-size:21px}
.rp-subtitle{text-align:center;color:#61758a;font-size:12.5px;margin-top:3px}
.rp-divider{height:12px;text-align:center}
.rp-divider:before,.rp-divider:after{content:"";display:inline-block;width:60px;border-top:1px solid #74a6d4;vertical-align:middle}
.rp-divider span{display:inline-block;width:7px;height:7px;background:#3b86c8;transform:rotate(45deg);margin:0 6px;vertical-align:middle}

.rp-info-box,.rp-section,.rp-conclusion{border:1.25px solid #3b78b5;border-radius:8px;overflow:hidden;margin-top:6px}
.rp-info-box{padding:6px 8px}
.rp-info-grid{display:grid;grid-template-columns:repeat(4,1fr)}
.rp-info{padding:4px 7px;border-right:1px dotted #a8bfd5;border-bottom:1px dotted #c5d5e4;min-height:42px}
.rp-info:nth-child(4n){border-right:0}
.rp-info:nth-last-child(-n+4){border-bottom:0}
.rp-info-label{font-size:10px;color:#667b91}
.rp-info-value{font-size:12.5px;font-weight:800;color:#163a62;margin-top:1px}
.rp-status-pill{display:inline-block;padding:2px 9px;border-radius:999px;background:#e8f8ee;color:#168052;border:1px solid #74c99b;font-size:10.5px;font-weight:800;margin-top:1px}

.rp-section-title{background:linear-gradient(90deg,#0a4f90,#1465a8);color:#fff;font-size:12.5px;font-weight:800;padding:4px 8px}
.rp-section-note{font-size:10px;color:#697d91;padding:3px 8px 0}

.rp-summary-grid{display:grid;grid-template-columns:1fr 1fr}
.rp-summary-item{padding:5px 8px;border-right:1px solid #d2dfeb;border-bottom:1px dotted #c5d5e4;min-height:50px}
.rp-summary-item:nth-child(2n){border-right:0}
.rp-summary-item:nth-last-child(-n+2){border-bottom:0}
.rp-summary-label{font-size:10.8px;font-weight:800;color:#174c83}
.rp-summary-value{font-size:10.7px;line-height:1.34;margin-top:2px;white-space:pre-line;color:#283f57}

.rp-stat-grid{display:grid;grid-template-columns:repeat(6,1fr)}
.rp-stat{text-align:center;padding:5px 4px;border-right:1px solid #d6e2ed}
.rp-stat:last-child{border-right:0}
.rp-stat-label{font-size:9px;color:#385c80;font-weight:700}
.rp-stat-value{font-size:15px;font-weight:900;color:#0b4f91;margin-top:1px}
.rp-stat-text{font-size:10.3px;line-height:1.25}

.rp-domain-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:5px;padding:5px 6px 6px}
.rp-domain-card{border:1px solid #bfd1e2;border-radius:7px;padding:5px;text-align:center}
.rp-domain-icon{width:22px;height:22px;border-radius:50%;background:#eaf4ff;color:#1767ad;display:flex;align-items:center;justify-content:center;margin:0 auto 2px;font-size:10px;font-weight:900}
.rp-domain-name{font-size:10.6px;font-weight:800}
.rp-domain-score{font-size:15px!important;font-weight:900;color:#0b55a0}
.rp-domain-score span{font-size:9.5px;color:#536b82}
.rp-progress{height:5px;border-radius:99px;background:#d7dce2;overflow:hidden;margin:3px 2px}
.rp-progress>div{height:100%;background:#176bc0;border-radius:99px}
.rp-domain-level{font-size:9px;font-weight:700;color:#435d76;line-height:1.25}

.rp-conclusion{padding:6px 9px;background:#f5faff}
.rp-conclusion-title{font-size:11.5px;font-weight:900;color:#164f86}
.rp-conclusion-text{font-size:10.5px;line-height:1.35;margin-top:2px}
.rp-sign{width:52%;margin:10px 0 0 auto;font-size:10.5px;line-height:1.55}
.rp-page-no{text-align:center;font-size:8.8px;color:#65798e;position:absolute;bottom:4.5mm;left:0;right:0}

/* Goal */
.rp-goal-block{border:1px solid #adc7df;border-radius:8px;margin:7px 0 8px;overflow:hidden}
.rp-goal-head{display:flex;justify-content:space-between;gap:10px;padding:6px 8px;background:#f4f9ff}
.rp-goal-domain{font-size:9.8px;color:#2e679d;font-weight:800}
.rp-goal-title{font-size:12.3px;font-weight:900;margin-top:1px}
.rp-goal-status{font-size:9.8px;font-weight:800;color:#176947;background:#e8f7ef;border-radius:999px;padding:2px 7px;height:max-content}
.rp-goal-meta{width:100%;border-collapse:collapse;table-layout:fixed}
.rp-goal-meta td{border-top:1px solid #d7e2ec;border-right:1px solid #d7e2ec;padding:4px 6px;font-size:9.8px;vertical-align:top}
.rp-goal-meta td:last-child{border-right:0}
.rp-goal-detail{font-size:9.9px;line-height:1.35;padding:4px 7px;border-top:1px dotted #c6d5e3}
.rp-activity-table{width:100%;border-collapse:collapse;table-layout:fixed}
.rp-activity-table th,.rp-activity-table td{border:1px solid #c8d7e5;padding:3px 4px;font-size:9.3px;line-height:1.28;vertical-align:top}
.rp-activity-table th{background:#eef5fb;text-align:center;color:#244c71}
.rp-empty-cell{text-align:center;color:#718096}
.rp-muted{color:#708295}

/* Follow-up overview */
.rp-follow-meta{width:100%;border-collapse:collapse;table-layout:fixed}
.rp-follow-meta td{padding:5px 7px;border-right:1px solid #d7e2ec;font-size:10px;vertical-align:top}
.rp-follow-meta td:last-child{border-right:0}

.rp-follow-domain-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:5px;padding:6px}
.rp-follow-domain{border:1px solid #c1d2e2;border-radius:7px;padding:5px;text-align:center;background:#fbfdff}
.rp-follow-domain-name{font-size:10px;font-weight:800;color:#244d75}
.rp-follow-score{font-size:14px;font-weight:900;color:#0b55a0;margin-top:1px}
.rp-follow-trend{font-size:8.8px;color:#5a7086;margin-top:1px;line-height:1.2}

.rp-follow-summary-grid{display:grid;grid-template-columns:1fr 1fr}
.rp-follow-box{padding:5px 7px;border-right:1px solid #d6e2ed;border-bottom:1px dotted #c8d7e5;font-size:9.7px;line-height:1.32;white-space:pre-line;min-height:44px}
.rp-follow-box:nth-child(2n){border-right:0}
.rp-follow-box:nth-last-child(-n+2){border-bottom:0}
.rp-next-box{margin-top:6px}

/* Follow-up indicator page */
.rp-indicator-intro{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-top:6px}
.rp-indicator-intro-box{border:1px solid #c3d5e6;border-radius:7px;background:#f8fbff;padding:5px 7px;font-size:9.8px;line-height:1.3}
.rp-follow-table{width:100%;border-collapse:collapse;table-layout:fixed}
.rp-follow-table th,.rp-follow-table td{border:1px solid #cbd8e5;padding:3px 5px;font-size:9px;line-height:1.2;vertical-align:middle}
.rp-follow-table th{background:#eef5fb;color:#244c71;font-weight:800}
.rp-follow-table th:nth-child(1){width:17%}
.rp-follow-table th:nth-child(2){width:53%}
.rp-follow-table th:nth-child(3),.rp-follow-table th:nth-child(4),.rp-follow-table th:nth-child(5){width:10%}
.rp-center{text-align:center}

/* Summary page is intentionally compact so it remains one A4 sheet */
.rp-summary-page .rp-title{font-size:21px}
.rp-summary-page .rp-subtitle{font-size:11.5px}
.rp-summary-page .rp-divider{height:9px}
.rp-summary-page .rp-info-box{margin-top:4px;padding:4px 6px}
.rp-summary-page .rp-info{min-height:35px;padding:3px 5px}
.rp-summary-page .rp-info-label{font-size:9px}
.rp-summary-page .rp-info-value{font-size:11px}
.rp-summary-page .rp-section{margin-top:4px}
.rp-summary-page .rp-section-title{font-size:11.3px;padding:3px 7px}
.rp-summary-page .rp-summary-item{min-height:42px;padding:4px 6px}
.rp-summary-page .rp-summary-label{font-size:9.6px}
.rp-summary-page .rp-summary-value{font-size:9.6px;line-height:1.25}
.rp-summary-page .rp-stat{padding:3px}
.rp-summary-page .rp-stat-label{font-size:8.4px}
.rp-summary-page .rp-stat-value{font-size:13px}
.rp-summary-page .rp-stat-text{font-size:9px}
.rp-summary-page .rp-section-note{font-size:8.8px;padding-top:2px}
.rp-summary-page .rp-domain-grid{gap:4px;padding:4px 5px 5px}
.rp-summary-page .rp-domain-card{padding:4px}
.rp-summary-page .rp-domain-icon{width:18px;height:18px;font-size:8px}
.rp-summary-page .rp-domain-name{font-size:9.4px}
.rp-summary-page .rp-domain-score{font-size:13px!important}
.rp-summary-page .rp-domain-level{font-size:8px}
.rp-summary-page .rp-conclusion{margin-top:4px;padding:5px 7px}
.rp-summary-page .rp-conclusion-title{font-size:10px}
.rp-summary-page .rp-conclusion-text{font-size:9.3px;line-height:1.25}
.rp-summary-page .rp-sign{margin-top:7px;font-size:9.5px;line-height:1.4}

/* Responsive screen */
@media(max-width:900px){
    .rp-page{width:100%;min-height:auto;padding:14px}
    .rp-info-grid{grid-template-columns:repeat(2,1fr)}
    .rp-info:nth-child(2n){border-right:0}
    .rp-summary-grid{grid-template-columns:1fr}
    .rp-summary-item{border-right:0}
    .rp-stat-grid{grid-template-columns:repeat(2,1fr)}
    .rp-domain-grid,.rp-follow-domain-grid{grid-template-columns:repeat(2,1fr)}
    .rp-follow-summary-grid,.rp-indicator-intro{grid-template-columns:1fr}
    .rp-follow-box{border-right:0}
}

/* Print */
@page{size:A4 portrait;margin:0}

@media print{
    html,body{
        margin:0!important;
        padding:0!important;
        background:#fff!important;
    }

    .navbar-custom,.leftside-menu,.page-title-box,.footer,header,footer,.rp-toolbar{
        display:none!important;
    }

    .content-page,.content,.container-fluid{
        margin:0!important;
        padding:0!important;
        width:100%!important;
        max-width:none!important;
    }

    .idp-report-screen{padding:0!important}

    .rp-page{
        box-sizing:border-box!important;
        width:210mm!important;
        min-height:297mm!important;
        margin:0!important;
        padding:8mm 9mm 10mm!important;
        box-shadow:none!important;
        page-break-after:always!important;
        break-after:page!important;
        -webkit-print-color-adjust:exact!important;
        print-color-adjust:exact!important;
    }

    .rp-page:last-child{
        page-break-after:auto!important;
        break-after:auto!important;
    }

    /* บล็อกย่อยไม่ถูกตัดกลางรายการ */
    .rp-info-box,.rp-conclusion,.rp-goal-block,.rp-follow-domain,
    .rp-indicator-intro-box,tr{
        page-break-inside:avoid!important;
        break-inside:avoid!important;
    }

    thead{display:table-header-group!important}
    tfoot{display:table-footer-group!important}

    .rp-page-no{bottom:3.5mm!important}
}
</style>
<div class="idp-report-screen">
    <div class="rp-toolbar">
        <a href="{{ route('individual-development.index',['client'=>$client->id,'plan'=>$plan->id]) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>กลับหน้าหลัก</a>
        <div class="rp-toolbar-group">
            {{-- IDP_BROWSER_PRINT_FINAL_V1: ใช้ Browser Print สำหรับรายงาน A4 --}}
            <button type="button" onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer me-1"></i>พิมพ์ A4
            </button>
        </div>
    </div>
    @include('frontend.client.individual_development.report._content')
</div>
@endsection
