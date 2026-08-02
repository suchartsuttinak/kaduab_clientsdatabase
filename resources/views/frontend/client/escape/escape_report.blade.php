@extends('admin_client.admin_client')

@section('content')
@php
    $thaiDate = static function ($date): string {
        if (!$date) return '-';
        try {
            $value = $date instanceof \Carbon\CarbonInterface ? $date : \Carbon\Carbon::parse($date);
            return $value->format('d/m/') . ($value->year + 543);
        } catch (\Throwable $exception) {
            return '-';
        }
    };

    $thaiDateTime = static function ($date): string {
        if (!$date) return '-';
        try {
            $value = $date instanceof \Carbon\CarbonInterface ? $date : \Carbon\Carbon::parse($date);
            return $value->format('d/m/') . ($value->year + 543) . ' ' . $value->format('H:i') . ' น.';
        } catch (\Throwable $exception) {
            return '-';
        }
    };

    $followCount = $escape->follows->count();
    $clientFullName = trim((string) ($client->fullname ?? ''));
    if ($clientFullName === '') {
        $clientFullName = trim(($client->prefix ?? '') . ' ' . ($client->first_name ?? '') . ' ' . ($client->last_name ?? '')) ?: '-';
    }
@endphp

<div class="container-fluid escape-report-page" id="escapePrintableReport">
    <div class="escape-report-shell">
        <div class="escape-report-head">
            <div class="escape-report-head__left">
                <div class="escape-report-head__eyebrow">
                    <i class="bi bi-file-earmark-text"></i>
                    รายงานข้อมูลการออกจากสถานสงเคราะห์
                </div>
                <h1 class="escape-report-head__title">รายงานรายละเอียดการออก/หลบหนี</h1>
                <p class="escape-report-head__desc">
                    แสดงข้อมูลเหตุการณ์ ประเภทการออก และประวัติการติดตามของผู้รับบริการ
                </p>
            </div>

            <div class="escape-report-head__right no-print">
                <button type="button" onclick="window.print()" class="btn escape-report-btn escape-report-btn--primary">
                    <i class="bi bi-printer"></i><span>พิมพ์รายงาน</span>
                </button>
                <a href="{{ route('escape.index', $client->id) }}" class="btn escape-report-btn escape-report-btn--light">
                    <i class="bi bi-arrow-left-circle"></i><span>กลับหน้ารายการ</span>
                </a>
            </div>
        </div>

        <div class="escape-report-card">
            <div class="escape-report-table-wrap">
                <table class="escape-report-table">
                    <colgroup>
                        <col class="escape-label-col">
                        <col>
                        <col class="escape-label-col">
                        <col>
                    </colgroup>

                    <tbody>
                        <tr class="escape-report-table__section">
                            <td colspan="4">ข้อมูลสำคัญ</td>
                        </tr>
                        <tr>
                            <th>ชื่อ-สกุล</th>
                            <td>{{ $clientFullName }}</td>
                            <th>ประเภทการออก</th>
                            <td>{{ $escape->retire->retire_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>วันที่ออก/หลบหนี</th>
                            <td>{{ $thaiDate($escape->retire_date) }}</td>
                            <th>จำนวนครั้งที่ติดตาม</th>
                            <td>{{ $followCount > 0 ? $followCount . ' ครั้ง' : 'ยังไม่มีการติดตาม' }}</td>
                        </tr>
                        <tr>
                            <th>วันที่บันทึกรายการ</th>
                            <td>{{ $thaiDateTime($escape->created_at) }}</td>
                            <th>สถานะ</th>
                            <td>{{ $followCount > 0 ? 'มีประวัติการติดตาม' : 'ยังไม่มีการติดตาม' }}</td>
                        </tr>

                        <tr class="escape-report-table__section">
                            <td colspan="4">พฤติการณ์ / สาเหตุ / เรื่องราว</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="escape-report-long-text">
                                {!! nl2br(e($escape->stories ?: '-')) !!}
                            </td>
                        </tr>

                        <tr class="escape-report-table__section">
                            <td colspan="4">ประวัติการติดตาม</td>
                        </tr>
                    </tbody>

                    @forelse ($escape->follows as $follow)
                        @php
                            // แสดงข้อมูลการลงโทษเฉพาะเมื่อมีการกำหนดวันที่ลงโทษเท่านั้น
                            $hasPunishment = filled($follow->punish_date);
                        @endphp
                        <tbody class="escape-follow-group">
                            <tr class="escape-follow-divider">
                                <td colspan="4">
                                    <div class="escape-follow-divider__wrap">
                                        <strong>การติดตามครั้งที่ {{ $follow->count ?? '-' }}</strong>
                                        <span>วันที่ติดตาม {{ $thaiDate($follow->trace_date) }}</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>ผลการติดตาม</th>
                                <td>{{ $follow->trac_no ?: '-' }}</td>
                                <th>วันที่แจ้งความ</th>
                                <td>{{ $thaiDate($follow->report_date) }}</td>
                            </tr>
                            <tr>
                                <th>วันที่ยุติการติดตาม</th>
                                <td colspan="{{ $hasPunishment ? 1 : 3 }}">{{ $thaiDate($follow->stop_date) }}</td>
                                @if ($hasPunishment)
                                    <th>วันที่ลงโทษ</th>
                                    <td>{{ $thaiDate($follow->punish_date) }}</td>
                                @endif
                            </tr>
                            <tr>
                                <th>รายละเอียดการติดตาม</th>
                                <td colspan="3" class="escape-report-long-text">{!! nl2br(e($follow->detail ?: '-')) !!}</td>
                            </tr>
                            @if ($hasPunishment)
                                <tr>
                                    <th>การลงโทษ</th>
                                    <td colspan="3" class="escape-report-long-text">{!! nl2br(e($follow->punish ?: '-')) !!}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>หมายเหตุ</th>
                                <td colspan="3" class="escape-report-long-text">{!! nl2br(e($follow->remark ?: '-')) !!}</td>
                            </tr>
                        </tbody>
                    @empty
                        <tbody>
                            <tr>
                                <td colspan="4" class="escape-report-empty-row">
                                    <div class="escape-report-empty">
                                        <i class="bi bi-inboxes"></i>
                                        <strong>ยังไม่มีข้อมูลการติดตาม</strong>
                                        <span>เมื่อบันทึกการติดตามแล้ว รายละเอียดจะแสดงในส่วนนี้</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    @endforelse
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.escape-report-page{
    padding:22px 12px 34px;
    background:linear-gradient(180deg,#f8fbff 0%,#f4f7fb 100%);
    color:#1e293b;
}
.escape-report-page .escape-report-shell{max-width:1200px;margin:0 auto;}
.escape-report-page .escape-report-head{
    display:flex;align-items:flex-start;justify-content:space-between;gap:18px;flex-wrap:wrap;
    padding-bottom:18px;margin-bottom:18px;border-bottom:1px solid #e2e8f0;
}
.escape-report-page .escape-report-head__left{flex:1 1 650px;min-width:0;}
.escape-report-page .escape-report-head__eyebrow{
    display:inline-flex;align-items:center;gap:7px;padding:6px 12px;margin-bottom:10px;
    border-radius:999px;background:#eaf2ff;color:#3156c8;font-weight:700;font-size:.88rem;
}
.escape-report-page .escape-report-head__title{margin:0 0 6px;color:#0f172a;font-size:clamp(1.45rem,2vw,1.95rem);font-weight:800;}
.escape-report-page .escape-report-head__desc{margin:0;color:#64748b;line-height:1.7;}
.escape-report-page .escape-report-head__right{display:flex;align-items:center;gap:9px;flex-wrap:wrap;}
.escape-report-page .escape-report-btn{
    display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:42px;padding:9px 15px;
    border:1px solid #d7e1ec;border-radius:12px;background:#fff;color:#334155;font-weight:700;text-decoration:none;
}
.escape-report-page .escape-report-btn:hover{background:#f8fbff;color:#0f172a;}
.escape-report-page .escape-report-card{overflow:hidden;border:1px solid #dfe7f0;border-radius:18px;background:#fff;box-shadow:0 10px 30px rgba(15,23,42,.04);}
.escape-report-page .escape-report-table-wrap{width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch;}
.escape-report-page .escape-report-table{width:100%;min-width:920px;border-collapse:collapse;table-layout:fixed;}
.escape-report-page .escape-label-col{width:180px;}
.escape-report-page .escape-report-table th,
.escape-report-page .escape-report-table td{
    padding:11px 14px;border-right:1px solid #e5ebf2;border-bottom:1px solid #e5ebf2;
    vertical-align:top;font-size:.94rem;line-height:1.65;word-break:break-word;overflow-wrap:anywhere;
}
.escape-report-page .escape-report-table tr > *:last-child{border-right:0;}
.escape-report-page .escape-report-table th{background:#f8fafc;color:#334155;font-weight:800;text-align:left;}
.escape-report-page .escape-report-table__section td{background:#eef4fb;color:#0f172a;font-weight:800;}
.escape-report-page .escape-report-long-text{white-space:normal;min-height:52px;}
.escape-report-page .escape-follow-divider td{background:#f8fbff;border-top:2px solid #cbd9e8;}
.escape-report-page .escape-follow-divider__wrap{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;}
.escape-report-page .escape-follow-divider__wrap span{color:#475569;font-size:.88rem;font-weight:700;}
.escape-report-page .escape-report-empty-row{padding:0 !important;}
.escape-report-page .escape-report-empty{display:flex;flex-direction:column;align-items:center;gap:6px;padding:30px;color:#64748b;text-align:center;}
.escape-report-page .escape-report-empty i{font-size:1.8rem;color:#94a3b8;}
.escape-report-page .escape-report-empty strong{color:#334155;}

@media (max-width:767.98px){
    .escape-report-page{padding:14px 8px 24px;}
    .escape-report-page .escape-report-head__right{width:100%;flex-wrap:nowrap;overflow-x:auto;padding-bottom:2px;}
    .escape-report-page .escape-report-btn{flex:0 0 auto;}
    .escape-report-page .escape-report-card{border-radius:14px;}
    .escape-report-page .escape-report-table{min-width:820px;}
}

@media print{
    @page{size:A4 landscape;margin:10mm;}

    html,body{margin:0 !important;padding:0 !important;background:#fff !important;}
    body *{visibility:hidden !important;}
    #escapePrintableReport,
    #escapePrintableReport *{visibility:visible !important;}
    #escapePrintableReport{
        position:absolute !important;left:0 !important;top:0 !important;width:100% !important;
        margin:0 !important;padding:0 !important;background:#fff !important;
        -webkit-print-color-adjust:exact;print-color-adjust:exact;
    }
    #escapePrintableReport .escape-report-shell{max-width:none !important;margin:0 !important;}
    #escapePrintableReport .no-print{display:none !important;}
    #escapePrintableReport .escape-report-head{margin:0 0 8px !important;padding:0 0 8px !important;border-bottom:1px solid #555 !important;}
    #escapePrintableReport .escape-report-head__eyebrow{padding:0 !important;margin:0 0 3px !important;background:transparent !important;color:#000 !important;font-size:9pt !important;}
    #escapePrintableReport .escape-report-head__title{margin:0 0 2px !important;font-size:16pt !important;color:#000 !important;}
    #escapePrintableReport .escape-report-head__desc{font-size:9pt !important;line-height:1.35 !important;color:#222 !important;}
    #escapePrintableReport .escape-report-card{overflow:visible !important;border:1px solid #555 !important;border-radius:0 !important;box-shadow:none !important;}
    #escapePrintableReport .escape-report-table-wrap{overflow:visible !important;}
    #escapePrintableReport .escape-report-table{width:100% !important;min-width:0 !important;table-layout:fixed !important;}
    #escapePrintableReport .escape-label-col{width:115px !important;}
    #escapePrintableReport .escape-report-table th,
    #escapePrintableReport .escape-report-table td{
        padding:5px 7px !important;border-color:#777 !important;color:#000 !important;
        font-size:8.6pt !important;line-height:1.3 !important;background:#fff !important;
    }
    #escapePrintableReport .escape-report-table th{background:#f2f2f2 !important;}
    #escapePrintableReport .escape-report-table__section td{background:#e7e7e7 !important;font-size:9pt !important;}
    #escapePrintableReport .escape-follow-divider td{background:#f2f2f2 !important;border-top:1.5px solid #555 !important;}
    #escapePrintableReport .escape-follow-divider__wrap span{font-size:8pt !important;color:#000 !important;}

    /* ไม่บังคับทั้งตารางให้อยู่หน้าเดียว ป้องกันข้อมูลหายหรือเกิดหน้าว่าง */
    #escapePrintableReport tr{break-inside:avoid;page-break-inside:avoid;}
    #escapePrintableReport .escape-follow-group{break-inside:avoid-page;page-break-inside:avoid;}
}
</style>
@endsection
