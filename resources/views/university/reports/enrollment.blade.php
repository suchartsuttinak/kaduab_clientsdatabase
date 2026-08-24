<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>รายงานสรุปผลการศึกษาระดับอุดมศึกษา</title>
<style>
*{box-sizing:border-box}
html,body{margin:0;padding:0}
body{background:#f3f3f3;color:#111;font-family:"TH Sarabun New","Sarabun",Tahoma,sans-serif;font-size:13.5px;line-height:1.52}
.toolbar{max-width:210mm;margin:12px auto 7px;display:flex;justify-content:flex-end;gap:8px}
.toolbar a,.toolbar button{border:1px solid #aaa;background:#fff;color:#111;border-radius:4px;padding:6px 11px;text-decoration:none;font:inherit;cursor:pointer}
.sheet{width:210mm;min-height:297mm;margin:0 auto 24px;padding:15mm 17mm 14mm;background:#fff;box-shadow:0 2px 10px rgba(0,0,0,.07)}
.doc-head{text-align:center;margin-bottom:12px}
.doc-head .org{font-weight:600}
.doc-head h1{font-size:18px;line-height:1.35;margin:1px 0 0}
.rule{border:0;border-top:1.5px solid #111;margin:10px 0 0}
.section{margin-top:14px}
.section-title{font-size:14.5px;font-weight:700;margin:0 0 6px}
.info-table,.data-table,.summary-line{width:100%;border-collapse:collapse}
.info-table td{padding:2px 3px;vertical-align:top}
.info-table .label{width:16%;font-weight:700;white-space:nowrap}
.info-table .value{width:34%;border-bottom:1px dotted #aaa}
.semester{padding:10px 0 13px;border-bottom:1px solid #999}
.semester:last-child{border-bottom:0}
.semester-title{font-weight:700;font-size:14px;margin-bottom:5px}
.summary-line{margin:4px 0 7px}
.summary-line td{padding:2px 4px;border-bottom:1px dotted #bbb}
.summary-line .label{font-weight:700}
.data-table{margin-top:5px}
.data-table th,.data-table td{border:1px solid #888;padding:4px 5px;vertical-align:top}
.data-table th{text-align:center;background:#fff}
.center{text-align:center}
.follow-block{padding:6px 0 6px 10px;margin:5px 0;border-left:2px solid #888}
.follow-head{font-weight:700}
.paragraph{margin:5px 0;white-space:pre-line}
.empty{color:#666;font-style:italic;padding:5px 0}
.outcome{padding:7px 0;border-top:1px solid #777;border-bottom:1px solid #777;font-weight:700}
.signatures{margin-top:28px;display:grid;grid-template-columns:1fr 1fr;gap:34px;text-align:center}
.signature-line{margin-top:28px}
.footer{margin-top:18px;padding-top:6px;border-top:1px solid #999;font-size:10.5px;color:#555;text-align:right}
@page{size:A4 portrait;margin:12mm}
@media(max-width:850px){.sheet{width:auto;min-height:0;margin:0;padding:18px;box-shadow:none}.info-table .label{width:24%}.info-table .value{width:26%}}
@media print{body{background:#fff}.toolbar{display:none!important}.sheet{width:auto;min-height:0;margin:0;padding:0;box-shadow:none}.semester,.data-table tr{break-inside:avoid}.section-title,.data-table thead{break-after:avoid}}
</style>
</head>
<body>

<div class="toolbar">
    <a href="{{ route('university.enrollments.show',$enrollment->id) }}">กลับ</a>
    <button type="button" onclick="window.print()">พิมพ์รายงาน</button>
</div>

<main class="sheet">
    <header class="doc-head">
        <div class="org">ระบบติดตามการศึกษาระดับมหาวิทยาลัย</div>
        <h1>รายงานสรุปผลการศึกษาและการติดตามนักศึกษาระดับอุดมศึกษา</h1>
        <hr class="rule">
    </header>

    <section class="section">
        <h2 class="section-title">ส่วนที่ 1 ข้อมูลทั่วไปและสถานภาพปัจจุบัน</h2>
        <table class="info-table">
            <tr>
                <td class="label">ชื่อ–สกุล</td>
                <td class="value">{{ $client->fullname ?? trim(($client->first_name ?? '').' '.($client->last_name ?? '')) }}</td>
                <td class="label">สถานะการศึกษา</td>
                <td class="value">{{ config('university_tracking.enrollment_statuses.'.$enrollment->current_status,$enrollment->current_status) }}</td>
            </tr>
            <tr>
                <td class="label">สถานศึกษา</td>
                <td class="value">{{ data_get($currentEducationRecord,'school_name') ?: $enrollment->university_name }}</td>
                <td class="label">ระดับการศึกษา</td>
                <td class="value">{{ data_get($currentEducationRecord,'education.education_name') ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">ภาคเรียนปัจจุบัน</td>
                <td class="value">{{ \App\Support\UniversityCurrentEducationSource::semesterName($currentEducationRecord) ?: '-' }}</td>
                <td class="label">ปีที่เข้าเรียน</td>
                <td class="value">{{ $enrollment->admission_academic_year ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">คณะ</td>
                <td class="value">{{ $enrollment->faculty ?: '-' }}</td>
                <td class="label">สาขาวิชา</td>
                <td class="value">{{ $enrollment->major ?: '-' }}</td>
            </tr>
        </table>
    </section>

    <section class="section">
        <h2 class="section-title">ส่วนที่ 2 ประวัติผลการศึกษาและการติดตามรายภาคเรียน</h2>

        @forelse($enrollment->semesterRecords as $record)
            @php
                $schoolItems = $record->education_record_id
                    ? ($schoolFollowupsByEducationRecord->get($record->education_record_id) ?? collect())
                    : collect();
            @endphp

            <article class="semester">
                <div class="semester-title">ภาคเรียนที่ {{ $record->term }} ปีการศึกษา {{ $record->academic_year }} · ชั้นปีที่ {{ $record->year_level }}</div>

                <table class="summary-line">
                    <tr>
                        <td class="label">GPA</td><td>{{ $record->display_gpa ?? 'รอผล' }}</td>
                        <td class="label">GPAX</td><td>{{ $record->cumulative_gpa ?? '-' }}</td>
                        <td class="label">หน่วยกิตผ่าน / สะสม</td><td>{{ $record->display_earned_credits ?? '-' }} / {{ $record->display_cumulative_credits ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">ความเสี่ยง</td>
                        <td colspan="5">{{ config('university_tracking.risk_levels.'.$record->risk_level,$record->risk_level ?: '-') }}</td>
                    </tr>
                </table>

                @if($record->subjects->isNotEmpty())
                    <table class="data-table">
                        <thead><tr><th>รายวิชา</th><th style="width:14%">หน่วยกิต</th><th style="width:14%">เกรด</th></tr></thead>
                        <tbody>
                        @foreach($record->subjects as $subject)
                            <tr>
                                <td>{{ $subject->course_code ? $subject->course_code.' · ' : '' }}{{ $subject->course_name }}</td>
                                <td class="center">{{ $subject->credits ?? '-' }}</td>
                                <td class="center">{{ $subject->grade ?: 'รอผล' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif

                @if($schoolItems->isNotEmpty())
                    <div style="font-weight:700;margin-top:8px">ประวัติการติดตามด้านการศึกษา</div>
                    @foreach($schoolItems as $schoolFollowup)
                        <div class="follow-block">
                            <div class="follow-head">ครั้งที่ {{ $loop->iteration }} · {{ $schoolFollowup->follow_date ? \Carbon\Carbon::parse($schoolFollowup->follow_date)->format('d/m/Y') : '-' }}</div>
                            <div>{{ $schoolFollowup->result ?: 'ไม่มีรายละเอียดผลการติดตาม' }}</div>
                        </div>
                    @endforeach
                @endif

                @if($record->followups->isNotEmpty())
                    <div style="font-weight:700;margin-top:8px">การประเมินและติดตามในระดับมหาวิทยาลัย</div>
                    @foreach($record->followups as $followup)
                        <div class="follow-block">
                            <div class="follow-head">ครั้งที่ {{ $loop->iteration }} · {{ optional($followup->followup_date)->format('d/m/Y') ?: '-' }} · {{ config('university_tracking.risk_levels.'.$followup->overall_risk_level,$followup->overall_risk_level ?: '-') }}</div>
                            @if($followup->general_condition)<div>{{ $followup->general_condition }}</div>@endif
                            @if($followup->assistance_summary)<div><strong>การช่วยเหลือ:</strong> {{ $followup->assistance_summary }}</div>@endif
                            @if($followup->next_plan)<div><strong>แนวทางดำเนินการต่อ:</strong> {{ $followup->next_plan }}</div>@endif
                        </div>
                    @endforeach
                @endif

                @if($record->semester_summary)
                    <p class="paragraph"><strong>สรุปผลภาคเรียน:</strong> {{ $record->semester_summary }}</p>
                @endif
            </article>
        @empty
            <div class="empty">ยังไม่มีข้อมูลภาคเรียนมหาวิทยาลัย</div>
        @endforelse
    </section>

    @if($enrollment->outcome)
        <section class="section">
            <h2 class="section-title">ส่วนที่ 3 ผลสิ้นสุดการศึกษา</h2>
            <div class="outcome">
                {{ config('university_tracking.outcome_types.'.$enrollment->outcome->outcome_type,$enrollment->outcome->outcome_type) }}
                @if($enrollment->outcome->outcome_date) · วันที่ {{ optional($enrollment->outcome->outcome_date)->format('d/m/Y') }} @endif
            </div>

            @if($enrollment->outcome->reasons->isNotEmpty())
                <table class="data-table">
                    <thead><tr><th style="width:18%">ประเภท</th><th style="width:30%">เหตุผล</th><th>รายละเอียด</th></tr></thead>
                    <tbody>
                    @foreach($enrollment->outcome->reasons as $reason)
                        <tr>
                            <td>{{ $reason->is_primary ? 'เหตุผลหลัก' : 'เหตุผลร่วม' }}</td>
                            <td>{{ config('university_tracking.outcome_reasons.'.$reason->reason_code,$reason->reason_code) }}</td>
                            <td>{{ $reason->detail ?: '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

            @if($enrollment->outcome->summary)
                <p class="paragraph"><strong>สรุปผล:</strong> {{ $enrollment->outcome->summary }}</p>
            @endif
        </section>
    @endif

    <div class="signatures">
        <div><div class="signature-line">ลงชื่อ ............................................................</div><div>(ผู้บันทึก/ผู้ติดตาม)</div></div>
        <div><div class="signature-line">ลงชื่อ ............................................................</div><div>(ผู้ตรวจสอบ/ผู้รับผิดชอบ)</div></div>
    </div>

    <div class="footer">เอกสารจัดทำจากระบบติดตามการศึกษาระดับมหาวิทยาลัย</div>
</main>
</body>
</html>
