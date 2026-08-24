@extends(config('university_tracking.dashboard_layout', 'admin.admin_master'))

@section('admin')
<link rel="stylesheet" href="{{ asset('backend/assets/css/university-tracking.css') }}">
<div class="container-fluid ut-page">
<div class="ut-wrap py-3">
@include('university.partials.flash')

<div class="ut-card ut-header">
    <div class="ut-title-row">
        <div class="ut-icon"><i class="bi bi-mortarboard-fill"></i></div>
        <div>
            <h1 class="ut-title">Dashboard การศึกษาระดับอุดมศึกษา</h1>
            <p class="ut-subtitle">ติดตามผลการเรียน ความเสี่ยง การช่วยเหลือ และผลสำเร็จของผู้รับบริการที่ศึกษาต่อมหาวิทยาลัย</p>
        </div>
    </div>
    <div class="ut-actions ut-no-print">
        <a href="{{ route('university.enrollments.index', ['academic_year' => $academicYear]) }}" class="ut-btn ut-btn-primary"><i class="bi bi-people"></i> รายชื่อนักศึกษา</a>
    </div>
</div>

<form method="GET" class="ut-card ut-filter ut-no-print">
    <div class="ut-filter-row">
        <div class="ut-field">
            <label for="academic_year">ปีการศึกษา</label>
            <select class="form-select" name="academic_year" id="academic_year" onchange="this.form.submit()">
                @forelse($years as $year)
                    <option value="{{ $year }}" @selected((int)$academicYear === (int)$year)>{{ $year }}</option>
                @empty
                    <option value="{{ $academicYear }}">{{ $academicYear }}</option>
                @endforelse
            </select>
        </div>
        <div class="ut-muted small pb-2">ข้อมูลสถิติด้านล่างจะเปลี่ยนตามปีการศึกษาที่เลือก</div>
    </div>
</form>

<div class="ut-stat-grid">
    <div class="ut-card ut-stat"><div class="ut-stat-label">นักศึกษาที่มีข้อมูลในปีนี้</div><div class="ut-stat-value">{{ number_format($studentCount) }}</div><div class="ut-stat-note">นับไม่ซ้ำรายบุคคล</div></div>
    <div class="ut-card ut-stat"><div class="ut-stat-label">เด็กที่ GPA ต่ำกว่า 2.00</div><div class="ut-stat-value">{{ number_format($gpaBelowTwo) }}</div><div class="ut-stat-note">ควรพิจารณาติดตามเชิงรุก</div></div>
    <div class="ut-card ut-stat"><div class="ut-stat-label">เสี่ยง/เสี่ยงสูง</div><div class="ut-stat-value">{{ number_format($atRiskStudentCount) }}</div><div class="ut-stat-note">จากการประเมินรายภาคเรียน</div></div>
    <div class="ut-card ut-stat"><div class="ut-stat-label">การติดตามทั้งหมด</div><div class="ut-stat-value">{{ number_format($followupCount) }}</div><div class="ut-stat-note">ครั้ง ในปีการศึกษา {{ $academicYear }}</div></div>
    <div class="ut-card ut-stat"><div class="ut-stat-label">สำเร็จการศึกษา</div><div class="ut-stat-value">{{ number_format($outcomeCounts['graduated'] ?? 0) }}</div><div class="ut-stat-note">บันทึกผลสำเร็จในปีนี้</div></div>
    <div class="ut-card ut-stat"><div class="ut-stat-label">ออกกลางคัน</div><div class="ut-stat-value">{{ number_format($outcomeCounts['dropout'] ?? 0) }}</div><div class="ut-stat-note">ลาออก/ยุติการศึกษา</div></div>
    <div class="ut-card ut-stat"><div class="ut-stat-label">พ้นสภาพนักศึกษา</div><div class="ut-stat-value">{{ number_format($outcomeCounts['dismissed'] ?? 0) }}</div><div class="ut-stat-note">แยกจากการลาออก</div></div>
    @for($level=1;$level<=4;$level++)
        <div class="ut-card ut-stat"><div class="ut-stat-label">ชั้นปีที่ {{ $level }}</div><div class="ut-stat-value">{{ number_format($yearLevelCounts[$level] ?? 0) }}</div><div class="ut-stat-note">ราย</div></div>
    @endfor
</div>

<div class="ut-card ut-content mb-3">
    <h2 class="ut-section-title"><i class="bi bi-diagram-3"></i> Cohort เด็กที่เข้ามหาวิทยาลัยปีการศึกษา {{ $academicYear }}</h2>
    <div class="ut-stat-grid mb-0">
        <div class="ut-stat"><div class="ut-stat-label">เข้าศึกษาทั้งหมด</div><div class="ut-stat-value">{{ number_format($cohortTotal) }}</div></div>
        <div class="ut-stat"><div class="ut-stat-label">กำลังศึกษา</div><div class="ut-stat-value">{{ number_format($cohortStatusCounts['studying'] ?? 0) }}</div></div>
        <div class="ut-stat"><div class="ut-stat-label">สำเร็จการศึกษา</div><div class="ut-stat-value">{{ number_format($cohortStatusCounts['graduated'] ?? 0) }}</div></div>
        <div class="ut-stat"><div class="ut-stat-label">ออกกลางคัน/พ้นสภาพ</div><div class="ut-stat-value">{{ number_format(($cohortStatusCounts['dropout'] ?? 0)+($cohortStatusCounts['dismissed'] ?? 0)) }}</div></div>
        <div class="ut-stat"><div class="ut-stat-label">พัก/ย้าย/อื่น ๆ</div><div class="ut-stat-value">{{ number_format(($cohortStatusCounts['leave'] ?? 0)+($cohortStatusCounts['transferred'] ?? 0)+($cohortStatusCounts['lost_contact'] ?? 0)+($cohortStatusCounts['other'] ?? 0)) }}</div></div>
    </div>
</div>

<div class="ut-grid">
    <div class="ut-col-6">
        <div class="ut-card ut-content h-100">
            <h2 class="ut-section-title"><i class="bi bi-exclamation-diamond"></i> จำนวนเด็กตามปัญหาที่พบจากการติดตาม</h2>
            @php $issueMax = max(1, (int)($issueCounts->max('total') ?? 1)); @endphp
            <div class="ut-progress-list">
                @forelse($issueCounts as $row)
                    <div class="ut-progress-row">
                        <div class="ut-progress-label" title="{{ config('university_tracking.issue_categories.'.$row->category, $row->category) }}">{{ config('university_tracking.issue_categories.'.$row->category, $row->category) }}</div>
                        <div class="ut-progress-track"><div class="ut-progress-fill" style="width:{{ min(100, round(($row->total/$issueMax)*100)) }}%"></div></div>
                        <div class="ut-progress-count">{{ $row->total }}</div>
                    </div>
                @empty
                    <div class="ut-empty"><i class="bi bi-clipboard-check"></i><h3>ยังไม่มีข้อมูลปัญหา</h3><p>เมื่อครูบันทึกการติดตาม ระบบจะสรุปประเด็นให้อัตโนมัติ</p></div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="ut-col-6">
        <div class="ut-card ut-content h-100">
            <h2 class="ut-section-title"><i class="bi bi-sign-stop"></i> สาเหตุออกกลางคัน/พ้นสภาพ</h2>
            @php $reasonMax = max(1, (int)($dropoutReasonCounts->max('total') ?? 1)); @endphp
            <div class="ut-progress-list">
                @forelse($dropoutReasonCounts as $row)
                    <div class="ut-progress-row">
                        <div class="ut-progress-label" title="{{ config('university_tracking.outcome_reasons.'.$row->reason_code, $row->reason_code) }}">{{ config('university_tracking.outcome_reasons.'.$row->reason_code, $row->reason_code) }}</div>
                        <div class="ut-progress-track"><div class="ut-progress-fill" style="width:{{ min(100, round(($row->total/$reasonMax)*100)) }}%"></div></div>
                        <div class="ut-progress-count">{{ $row->total }}</div>
                    </div>
                @empty
                    <div class="ut-empty"><i class="bi bi-bar-chart"></i><h3>ยังไม่มีข้อมูลสาเหตุ</h3><p>ระบบจะแสดงสถิติเมื่อมีการบันทึกเด็กออกกลางคันหรือพ้นสภาพ</p></div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="ut-col-8">
        <div class="ut-card ut-content">
            <h2 class="ut-section-title"><i class="bi bi-shield-exclamation"></i> นักศึกษาที่ควรติดตาม</h2>
            <div class="ut-table-wrap">
                <table class="ut-table">
                    <thead><tr><th>ผู้รับบริการ</th><th>มหาวิทยาลัย</th><th>ชั้นปี</th><th>ภาคเรียน</th><th>GPA</th><th>ความเสี่ยง</th><th class="ut-center ut-no-print">ดู</th></tr></thead>
                    <tbody>
                    @forelse($highRiskStudents as $item)
                        <tr>
                            <td>@include('university.partials.client_name',['client'=>$item->enrollment->client])</td>
                            <td>{{ $item->enrollment->university_name }}</td>
                            <td>ปี {{ $item->year_level }}</td>
                            <td>{{ $item->semester->semester_name ?? ($item->term.'/'.$item->academic_year) }}</td>
                            <td>{{ $item->display_gpa ?? '-' }}</td>
                            <td>@include('university.partials.risk_badge',['value'=>$item->risk_level])</td>
                            <td class="ut-center ut-no-print"><a class="ut-btn ut-btn-light" href="{{ route('university.enrollments.show',$item->enrollment_id) }}"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="ut-empty"><p class="mb-0">ยังไม่มีนักศึกษาที่ถูกจัดระดับเสี่ยงในปีการศึกษานี้</p></div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="ut-col-4">
        <div class="ut-card ut-content h-100">
            <h2 class="ut-section-title"><i class="bi bi-clock-history"></i> ติดตามล่าสุด</h2>
            <div class="ut-timeline">
                @forelse($recentFollowups as $followup)
                    <div class="ut-timeline-item">
                        <div class="ut-timeline-title">@include('university.partials.client_name',['client'=>$followup->enrollment->client])</div>
                        <div class="ut-timeline-text">{{ optional($followup->followup_date)->format('d/m/Y') }} · {{ config('university_tracking.followup_methods.'.$followup->followup_method,$followup->followup_method) }}</div>
                    </div>
                @empty
                    <div class="ut-muted small">ยังไม่มีข้อมูลการติดตาม</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
</div>
</div>
@endsection
