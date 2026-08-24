<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>รายงานผลการศึกษาและการติดตามนักศึกษาระดับอุดมศึกษา</title>
<style>
*{box-sizing:border-box}
html,body{margin:0;padding:0}
body{background:#f3f3f3;color:#111;font-family:"TH Sarabun New","Sarabun",Tahoma,sans-serif;font-size:13.5px;line-height:1.52}
.toolbar{max-width:210mm;margin:12px auto 7px;display:flex;justify-content:flex-end;gap:8px}
.toolbar a,.toolbar button{border:1px solid #aaa;background:#fff;color:#111;border-radius:4px;padding:6px 11px;text-decoration:none;font:inherit;cursor:pointer}
.sheet{width:210mm;min-height:297mm;margin:0 auto 24px;padding:15mm 17mm 14mm;background:#fff;box-shadow:0 2px 10px rgba(0,0,0,.07)}
.doc-head{text-align:center;margin-bottom:12px}
.doc-head .org{font-size:13.5px;font-weight:600;margin-bottom:2px}
.doc-head h1{margin:0;font-size:18px;line-height:1.35;font-weight:700}
.doc-head .period{margin-top:2px;font-size:13.5px}
.rule{border:0;border-top:1.5px solid #111;margin:10px 0 0}
.section{margin-top:14px}
.section-title{margin:0 0 6px;font-size:14.5px;font-weight:700}
.info-table,.data-table,.result-summary{width:100%;border-collapse:collapse}
.info-table td{padding:2px 3px;vertical-align:top}
.info-table .label{width:16%;font-weight:700;white-space:nowrap}
.info-table .value{width:34%;border-bottom:1px dotted #aaa}
.result-summary{margin-top:5px}
.result-summary td{padding:2px 4px;border-bottom:1px dotted #bbb}
.result-summary .label{font-weight:700;width:22%}
.data-table{margin-top:5px}
.data-table th,.data-table td{border:1px solid #888;padding:4px 5px;vertical-align:top}
.data-table th{text-align:center;font-weight:700;background:#fff}
.center{text-align:center}
.paragraph{margin:5px 0 0;white-space:pre-line}
.follow-block{padding:6px 0;border-bottom:1px solid #bbb}
.follow-block:last-child{border-bottom:0}
.follow-head{font-weight:700;margin-bottom:2px}
.follow-line{margin:1px 0}
.subheading{margin:6px 0 3px;font-weight:700}
.empty{color:#666;font-style:italic;padding:5px 0}
.signatures{margin-top:28px;display:grid;grid-template-columns:1fr 1fr;gap:34px;text-align:center}
.signature-line{margin-top:28px}
.footer{margin-top:18px;padding-top:6px;border-top:1px solid #999;font-size:10.5px;color:#555;text-align:right}
@page{size:A4 portrait;margin:12mm}
@media(max-width:850px){.sheet{width:auto;min-height:0;margin:0;padding:18px;box-shadow:none}.info-table .label{width:24%}.info-table .value{width:26%}}
@media print{body{background:#fff}.toolbar{display:none!important}.sheet{width:auto;min-height:0;margin:0;padding:0;box-shadow:none}.section-title,.data-table thead{break-after:avoid}.follow-block,.data-table tr{break-inside:avoid}}
</style>
</head>
<body>
<div class="toolbar">
    <a href="{{ route('university.semesters.show',$record->id) }}">กลับ</a>
    <button type="button" onclick="window.print()">พิมพ์รายงาน</button>
</div>

<main class="sheet">
    <header class="doc-head">
        <div class="org">ระบบติดตามการศึกษาระดับมหาวิทยาลัย</div>
        <h1>รายงานผลการศึกษาและการติดตามนักศึกษาระดับอุดมศึกษา</h1>
        <div class="period">ภาคเรียนที่ {{ $record->term }} ปีการศึกษา {{ $record->academic_year }}</div>
        <hr class="rule">
    </header>

    <section class="section">
        <h2 class="section-title">ส่วนที่ 1 ข้อมูลทั่วไปและสถานภาพการศึกษา</h2>
        <table class="info-table">
            <tr>
                <td class="label">ชื่อ–สกุล</td>
                <td class="value">{{ $client->fullname ?? trim(($client->first_name ?? '').' '.($client->last_name ?? '')) }}</td>
                <td class="label">ชั้นปี</td>
                <td class="value">{{ $record->year_level ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">สถานศึกษา</td>
                <td class="value">{{ data_get($record,'educationRecord.school_name') ?: data_get($currentEducationRecord,'school_name') ?: $enrollment->university_name }}</td>
                <td class="label">ระดับการศึกษา</td>
                <td class="value">{{ data_get($record,'educationRecord.education.education_name') ?: data_get($currentEducationRecord,'education.education_name') ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">คณะ</td>
                <td class="value">{{ $enrollment->faculty ?: '-' }}</td>
                <td class="label">สาขาวิชา</td>
                <td class="value">{{ $enrollment->major ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">ภาคเรียน</td>
                <td class="value">{{ data_get($record,'educationRecord.semester.semester_name') ?: ($record->term.'/'.$record->academic_year) }}</td>
                <td class="label">สถานะทางการศึกษา</td>
                <td class="value">{{ config('university_tracking.academic_statuses.'.$record->academic_status,$record->academic_status ?: '-') }}</td>
            </tr>
        </table>
    </section>

    <section class="section">
        <h2 class="section-title">ส่วนที่ 2 ผลการศึกษา</h2>
        <table class="result-summary">
            <tr>
                <td class="label">เกรดเฉลี่ยภาคเรียน (GPA)</td>
                <td>{{ $record->display_gpa ?? 'รอผล' }}</td>
                <td class="label">เกรดเฉลี่ยสะสม (GPAX)</td>
                <td>{{ $record->cumulative_gpa ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">หน่วยกิตลงทะเบียน</td>
                <td>{{ $record->display_registered_credits ?? '-' }}</td>
                <td class="label">หน่วยกิตที่ผ่าน / สะสม</td>
                <td>{{ $record->display_earned_credits ?? '-' }} / {{ $record->display_cumulative_credits ?? '-' }}</td>
            </tr>
        </table>
        @if($record->semester_summary)
            <p class="paragraph"><strong>สรุปผลการศึกษา:</strong> {{ $record->semester_summary }}</p>
        @endif
    </section>

    <section class="section">
        <h2 class="section-title">ส่วนที่ 3 รายวิชาและผลการเรียน</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:15%">รหัสวิชา</th>
                    <th>ชื่อรายวิชา</th>
                    <th style="width:12%">หน่วยกิต</th>
                    <th style="width:12%">เกรด</th>
                    <th style="width:18%">ผลรายวิชา</th>
                </tr>
            </thead>
            <tbody>
            @forelse($record->subjects as $subject)
                <tr>
                    <td>{{ $subject->course_code ?: '-' }}</td>
                    <td>{{ $subject->course_name }}</td>
                    <td class="center">{{ $subject->credits ?? '-' }}</td>
                    <td class="center">{{ $subject->grade ?: 'รอผล' }}</td>
                    <td>{{ ['pass'=>'ผ่าน','fail'=>'ไม่ผ่าน','withdrawn'=>'ถอนรายวิชา (W)','incomplete'=>'ผลการเรียนไม่สมบูรณ์ (I)','satisfactory'=>'ผ่าน (S)','unsatisfactory'=>'ไม่ผ่าน (U)','audit'=>'Audit','other'=>'อื่น ๆ'][$subject->result_status] ?? ($subject->result_status ?: '-') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">ยังไม่มีข้อมูลรายวิชา</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2 class="section-title">ส่วนที่ 4 ประวัติการติดตามด้านการศึกษา</h2>
        @forelse($schoolFollowups as $schoolFollowup)
            <div class="follow-block">
                <div class="follow-head">การติดตามครั้งที่ {{ $loop->iteration }} วันที่ {{ $schoolFollowup->follow_date ? \Carbon\Carbon::parse($schoolFollowup->follow_date)->format('d/m/Y') : '-' }}</div>
                @if($schoolFollowup->follow_type)<div class="follow-line"><strong>วิธีดำเนินการ:</strong> {{ $schoolFollowup->follow_type }}</div>@endif
                @if($schoolFollowup->teacher_name)<div class="follow-line"><strong>ครู/ผู้ให้ข้อมูล:</strong> {{ $schoolFollowup->teacher_name }}</div>@endif
                @if($schoolFollowup->contact_name)<div class="follow-line"><strong>ผู้ประสานงาน:</strong> {{ $schoolFollowup->contact_name }}</div>@endif
                <div class="follow-line"><strong>ผลการติดตาม:</strong> {{ $schoolFollowup->result ?: '-' }}</div>
                @if($schoolFollowup->remark)<div class="follow-line"><strong>หมายเหตุ:</strong> {{ $schoolFollowup->remark }}</div>@endif
            </div>
        @empty
            <div class="empty">ยังไม่มีข้อมูลการติดตามด้านการศึกษาที่เชื่อมกับภาคเรียนนี้</div>
        @endforelse
    </section>

    <section class="section">
        <h2 class="section-title">ส่วนที่ 5 การประเมินและติดตามในระดับมหาวิทยาลัย</h2>
        @forelse($record->followups as $followup)
            <div class="follow-block">
                <div class="follow-head">การติดตามครั้งที่ {{ $loop->iteration }} วันที่ {{ optional($followup->followup_date)->format('d/m/Y') ?: '-' }}</div>
                <div class="follow-line"><strong>ระดับความเสี่ยง:</strong> {{ config('university_tracking.risk_levels.'.$followup->overall_risk_level,$followup->overall_risk_level ?: '-') }}</div>
                @if($followup->general_condition)<div class="follow-line"><strong>สถานการณ์โดยรวม:</strong> {{ $followup->general_condition }}</div>@endif
                @if($followup->strengths)<div class="follow-line"><strong>พัฒนาการ/จุดแข็ง:</strong> {{ $followup->strengths }}</div>@endif

                @if($followup->issues->isNotEmpty())
                    <div class="subheading">ประเด็นที่พบ</div>
                    <table class="data-table">
                        <thead><tr><th style="width:24%">ประเด็น</th><th style="width:15%">ระดับ</th><th>รายละเอียด</th><th>การช่วยเหลือ</th></tr></thead>
                        <tbody>
                        @foreach($followup->issues as $issue)
                            <tr>
                                <td>{{ config('university_tracking.issue_categories.'.$issue->category,$issue->category) }}</td>
                                <td>{{ config('university_tracking.risk_levels.'.$issue->severity,$issue->severity) }}</td>
                                <td>{{ $issue->detail ?: '-' }}</td>
                                <td>{{ $issue->assistance ?: '-' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif

                @if($followup->assistance_summary)<div class="follow-line"><strong>การช่วยเหลือ/ข้อเสนอแนะ:</strong> {{ $followup->assistance_summary }}</div>@endif
                @if($followup->next_plan)<div class="follow-line"><strong>แนวทางดำเนินการต่อ:</strong> {{ $followup->next_plan }}</div>@endif
            </div>
        @empty
            <div class="empty">ยังไม่มีข้อมูลการติดตามในระดับมหาวิทยาลัย</div>
        @endforelse
    </section>

    <section class="section">
        <h2 class="section-title">ส่วนที่ 6 สรุปและข้อเสนอแนะ</h2>
        <p class="paragraph"><strong>ระดับความเสี่ยงโดยรวม:</strong> {{ config('university_tracking.risk_levels.'.$record->risk_level,$record->risk_level ?: '-') }}</p>
        @if($record->risk_note)
            <p class="paragraph"><strong>ข้อสังเกต/ประเด็นที่ควรติดตาม:</strong> {{ $record->risk_note }}</p>
        @else
            <p class="paragraph">ไม่มีข้อสังเกตเพิ่มเติม</p>
        @endif
    </section>

    <div class="signatures">
        <div><div class="signature-line">ลงชื่อ ............................................................</div><div>(ผู้บันทึก/ผู้ติดตาม)</div></div>
        <div><div class="signature-line">ลงชื่อ ............................................................</div><div>(ผู้ตรวจสอบ/ผู้รับผิดชอบ)</div></div>
    </div>

    <div class="footer">เอกสารจัดทำจากระบบติดตามการศึกษาระดับมหาวิทยาลัย</div>
</main>
</body>
</html>
