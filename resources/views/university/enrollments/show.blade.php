@extends(config('university_tracking.client_layout', 'admin_client.admin_client'))
@section('content')
<link rel="stylesheet" href="{{ asset('backend/assets/css/university-tracking.css') }}">
<div class="container-fluid ut-page"><div class="ut-wrap py-3">
@include('university.partials.flash')
<div class="ut-card ut-header">
 <div class="ut-title-row"><div class="ut-icon"><i class="bi bi-mortarboard-fill"></i></div><div><h1 class="ut-title">ติดตามการศึกษาระดับมหาวิทยาลัย</h1><p class="ut-subtitle">@include('university.partials.client_name',['client'=>$client])</p></div></div>
 <div class="ut-actions ut-no-print">
  <a href="{{ route('university.dashboard') }}" class="ut-btn ut-btn-light"><i class="bi bi-speedometer2"></i> Dashboard</a>
  @if($universityPermissions['print'])<a href="{{ route('university.reports.enrollment',$enrollment->id) }}" class="ut-btn ut-btn-light"><i class="bi bi-printer"></i> รายงานภาพรวม</a>@endif
  @if($universityPermissions['update'])<a href="{{ route('university.enrollments.edit',$enrollment->id) }}" class="ut-btn ut-btn-warning"><i class="bi bi-pencil-square"></i> แก้ไขข้อมูลหลัก</a>@endif
  @if($universityPermissions['delete'])<form method="POST" action="{{ route('university.enrollments.destroy',$enrollment->id) }}" class="ut-delete-form d-inline" data-message="ลบประวัติมหาวิทยาลัยทั้งหมด รวมภาคเรียน การติดตาม และข้อมูลผลสิ้นสุดหรือไม่?">@csrf @method('DELETE')<button class="ut-btn ut-btn-danger"><i class="bi bi-trash"></i> ลบ</button></form>@endif
  <a href="{{ route('admin.index',$client->id) }}" class="ut-btn ut-btn-light"><i class="bi bi-x-lg"></i> ปิด</a>
 </div>
</div>
@if($currentEducationRecord)
<div class="ut-card ut-content mb-3">
    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-2">
        <h2 class="ut-section-title mb-0"><i class="bi bi-journal-check"></i> ข้อมูลการศึกษาปัจจุบัน</h2>
        <span class="ut-badge ut-badge-gray">อ้างอิง Education Record ล่าสุด</span>
    </div>
    <div class="ut-info-list">
        <div class="ut-info"><div class="ut-info-label">มหาวิทยาลัย / สถานศึกษา</div><div class="ut-info-value">{{ data_get($currentEducationRecord,'school_name','-') }}</div></div>
        <div class="ut-info"><div class="ut-info-label">ภาคเรียนปัจจุบัน</div><div class="ut-info-value">{{ $currentEducationRecord->semester_label ?? data_get($currentEducationRecord,'semester.semester_name','-') }}</div></div>
        <div class="ut-info"><div class="ut-info-label">ระดับการศึกษา</div><div class="ut-info-value">{{ data_get($currentEducationRecord,'education.education_name','-') }}</div></div>
    </div>
</div>
@endif

<div class="ut-card ut-content mb-3">
<div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-2">
 <h2 class="ut-section-title mb-0"><i class="bi bi-mortarboard"></i> ข้อมูลหลักสูตรและสถานะ</h2>
 <span class="ut-badge ut-badge-gray">ไม่กรอกมหาวิทยาลัย/ภาคเรียนซ้ำ</span>
</div>
<div class="ut-info-list">
 <div class="ut-info"><div class="ut-info-label">คณะ</div><div class="ut-info-value">{{ $enrollment->faculty ?: '-' }}</div></div>
 <div class="ut-info"><div class="ut-info-label">สาขาวิชา/วิชาเอก</div><div class="ut-info-value">{{ $enrollment->major ?: '-' }}</div></div>
 <div class="ut-info"><div class="ut-info-label">รหัสนักศึกษา</div><div class="ut-info-value">{{ $enrollment->student_code ?: '-' }}</div></div>
 <div class="ut-info"><div class="ut-info-label">ปีที่เข้าเรียน</div><div class="ut-info-value">{{ $enrollment->admission_academic_year }} @if($enrollment->admission_term) · ภาค {{ $enrollment->admission_term }} @endif</div></div>
 <div class="ut-info"><div class="ut-info-label">สถานะ</div><div class="ut-info-value">{{ config('university_tracking.enrollment_statuses.'.$enrollment->current_status,$enrollment->current_status) }}</div></div>
 <div class="ut-info"><div class="ut-info-label">ทุน/แหล่งสนับสนุน</div><div class="ut-info-value">{{ $enrollment->funding_type ?: '-' }}</div></div>
 <div class="ut-info"><div class="ut-info-label">ปีที่คาดว่าจะจบ</div><div class="ut-info-value">{{ $enrollment->expected_graduation_year ?: '-' }}</div></div>
</div></div>

<div class="ut-grid">
 <div class="ut-col-8"><div class="ut-card ut-content h-100">
  <div class="d-flex justify-content-between gap-2 flex-wrap align-items-center mb-2"><h2 class="ut-section-title mb-0"><i class="bi bi-journal-check"></i> ผลการเรียนรายภาคเรียน</h2>@if($universityPermissions['create'])<a href="{{ route('university.semesters.create',$enrollment->id) }}" class="ut-btn ut-btn-primary ut-no-print"><i class="bi bi-plus-circle"></i> เพิ่มภาคเรียน</a>@endif</div>
  <div class="ut-table-wrap"><table class="ut-table"><thead><tr><th>ปี/ภาค</th><th>ชั้นปี</th><th>GPA</th><th>GPAX</th><th>หน่วยกิต (ผ่าน / สะสม)</th><th>ความเสี่ยง</th><th>PDF</th><th class="ut-center ut-no-print">ดู</th></tr></thead><tbody>
  @forelse($enrollment->semesterRecords as $record)<tr><td>{{ $record->term }}/{{ $record->academic_year }}</td><td>ปี {{ $record->year_level }}</td><td>{{ $record->semester_gpa ?? $record->display_gpa ?? '-' }}</td><td>{{ $record->display_gpax ?? '-' }}</td><td>{{ $record->display_earned_credits ?? '-' }} / {{ $record->display_cumulative_credits ?? '-' }}</td><td>@include('university.partials.risk_badge',['value'=>$record->risk_level])</td><td>{{ $record->documents->count() }} ไฟล์</td><td class="ut-center ut-no-print"><a class="ut-btn ut-btn-light" href="{{ route('university.semesters.show',$record->id) }}"><i class="bi bi-eye"></i></a></td></tr>
  @empty<tr><td colspan="8"><div class="ut-empty"><i class="bi bi-journal-plus"></i><h3>ยังไม่มีข้อมูลรายภาคเรียน</h3><p>เพิ่มภาคเรียนเพื่อเก็บวิชา เกรด GPA/GPAX หน่วยกิต และ PDF ผลการเรียน</p></div></td></tr>@endforelse
  </tbody></table></div>
 </div></div>
 <div class="ut-col-4"><div class="ut-card ut-content h-100">
  <h2 class="ut-section-title mb-1"><i class="bi bi-person-check"></i> การติดตามรายภาคเรียน</h2>
  <div class="ut-help mb-3">เปิดภาคเรียนที่ต้องการเพื่อบันทึกการติดตาม ระบบจะรัน “ครั้งที่ 1, 2, 3...” อัตโนมัติ และเริ่มครั้งที่ 1 ใหม่เมื่อขึ้นภาคเรียนใหม่</div>
  <div class="ut-timeline">
   @forelse($enrollment->semesterRecords as $r)
    <div class="ut-timeline-item">
      <div class="d-flex justify-content-between gap-2 align-items-start">
        <div>
          <div class="ut-timeline-title">ภาคเรียน {{ $r->term }}/{{ $r->academic_year }} · ปี {{ $r->year_level }}</div>
          <div class="ut-timeline-text">ติดตามแล้ว {{ $r->followups_count ?? 0 }} ครั้ง</div>
        </div>
        <a class="ut-btn ut-btn-light" href="{{ route('university.semesters.show',$r->id) }}"><i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
   @empty
    <div class="ut-muted small">ยังไม่มีข้อมูลภาคเรียน</div>
   @endforelse
  </div>
  @php $legacyFollowups = $enrollment->followups->whereNull('semester_record_id'); @endphp
  @if($legacyFollowups->isNotEmpty())
   <div class="ut-divider"></div>
   <div class="ut-help mb-2">ข้อมูลติดตามเดิมที่ยังไม่ได้ผูกภาคเรียน (แสดงย้อนหลังเท่านั้น)</div>
   @foreach($legacyFollowups->take(5) as $f)
    <div class="ut-timeline-item"><div class="ut-timeline-title">{{ optional($f->followup_date)->format('d/m/Y') ?: '-' }}</div><div class="ut-timeline-text">{{ $f->general_condition ?: 'ข้อมูลติดตามเดิม' }}</div></div>
   @endforeach
  @endif
 </div></div>
</div>

<div class="ut-card ut-content mt-3">
 <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap"><div><h2 class="ut-section-title mb-1"><i class="bi bi-flag-fill"></i> ผลสิ้นสุด/ผลสำเร็จการศึกษา</h2><div class="ut-muted small">ใช้บันทึกสำเร็จการศึกษา ลาออก พ้นสภาพ ย้ายสถานศึกษา หรือพักเรียนระยะยาว</div></div>@if($universityPermissions[$enrollment->outcome ? 'update' : 'create'])<a href="{{ route('university.outcomes.form',$enrollment->id) }}" class="ut-btn ut-btn-{{ $enrollment->outcome ? 'warning' : 'primary' }} ut-no-print"><i class="bi bi-flag"></i> {{ $enrollment->outcome ? 'แก้ไขผลสิ้นสุด' : 'บันทึกผลสิ้นสุด' }}</a>@endif</div>
 @if($enrollment->outcome)<div class="ut-info-list mt-3"><div class="ut-info"><div class="ut-info-label">ผล</div><div class="ut-info-value">{{ config('university_tracking.outcome_types.'.$enrollment->outcome->outcome_type,$enrollment->outcome->outcome_type) }}</div></div><div class="ut-info"><div class="ut-info-label">ปีการศึกษา</div><div class="ut-info-value">{{ $enrollment->outcome->academic_year }}</div></div><div class="ut-info"><div class="ut-info-label">GPA/GPAX สุดท้าย</div><div class="ut-info-value">{{ $enrollment->outcome->final_gpa ?? '-' }}</div></div><div class="ut-info"><div class="ut-info-label">เหตุผลหลัก</div><div class="ut-info-value">@php $primary=$enrollment->outcome->reasons->firstWhere('is_primary',true); @endphp {{ $primary ? config('university_tracking.outcome_reasons.'.$primary->reason_code,$primary->reason_code) : '-' }}</div></div></div>@endif
</div>
</div></div>
@include('university.partials.delete_confirm')
@endsection
