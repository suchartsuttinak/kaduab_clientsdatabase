@extends(config('university_tracking.client_layout', 'admin_client.admin_client'))
@section('content')
<link rel="stylesheet" href="{{ asset('backend/assets/css/university-tracking.css') }}">
@php
$schoolFollowTypeLabels = [
    'self' => 'ติดตามด้วยตนเอง',
    'phone' => 'โทรศัพท์',
    'other' => 'อื่น ๆ',
];

$schoolFollowupPermissionUser = auth()->user();
$canCreateSchoolFollowup = (bool) ($schoolFollowupPermissionUser?->canCreateForm('education_followup') ?? false);
@endphp
<div class="container-fluid ut-page"><div class="ut-wrap py-3">@include('university.partials.flash')
<div class="ut-card ut-header"><div class="ut-title-row"><div class="ut-icon"><i class="bi bi-journal-check"></i></div><div><h1 class="ut-title">ผลการเรียน ปี {{ $record->year_level }} · ภาค {{ $record->term }}/{{ $record->academic_year }}</h1><p class="ut-subtitle">@include('university.partials.client_name',['client'=>$client]) · {{ data_get($record,'educationRecord.school_name',$enrollment->university_name) }}</p></div></div><div class="ut-actions ut-no-print">@if($universityPermissions['print'])<a class="ut-btn ut-btn-light" href="{{ route('university.reports.semester',$record->id) }}"><i class="bi bi-printer"></i> รายงานภาคเรียน</a>@endif @if($universityPermissions['update'])<a class="ut-btn ut-btn-warning" href="{{ route('university.semesters.edit',$record->id) }}"><i class="bi bi-pencil-square"></i> แก้ไข</a>@endif @if($universityPermissions['delete'])<form method="POST" action="{{ route('university.semesters.destroy',$record->id) }}" class="ut-delete-form d-inline" data-message="ลบข้อมูลภาคเรียน รายวิชา และ PDF ของภาคเรียนนี้หรือไม่?">@csrf @method('DELETE')<button class="ut-btn ut-btn-danger"><i class="bi bi-trash"></i> ลบ</button></form>@endif <a class="ut-btn ut-btn-light" href="{{ route('university.enrollments.show',$enrollment->id) }}"><i class="bi bi-arrow-left"></i> กลับ</a></div></div>
<div class="ut-stat-grid"><div class="ut-card ut-stat"><div class="ut-stat-label">GPA ภาคเรียน</div><div class="ut-stat-value">{{ $record->semester_gpa ?? $record->display_gpa ?? '-' }}</div></div><div class="ut-card ut-stat"><div class="ut-stat-label">GPAX สะสม</div><div class="ut-stat-value">{{ $record->display_gpax ?? '-' }}</div></div><div class="ut-card ut-stat"><div class="ut-stat-label">หน่วยกิตลงทะเบียน</div><div class="ut-stat-value">{{ $record->display_registered_credits ?? '-' }}</div></div><div class="ut-card ut-stat"><div class="ut-stat-label">หน่วยกิตผ่าน</div><div class="ut-stat-value">{{ $record->display_earned_credits ?? '-' }}</div></div><div class="ut-card ut-stat"><div class="ut-stat-label">หน่วยกิตสะสม</div><div class="ut-stat-value">{{ $record->display_cumulative_credits ?? '-' }}</div></div><div class="ut-card ut-stat"><div class="ut-stat-label">ความเสี่ยง</div><div class="mt-2">@include('university.partials.risk_badge',['value'=>$record->risk_level])</div></div></div>

<div class="ut-grid mb-3">
 <div class="ut-col-6"><div class="ut-card ut-content h-100">
  <div class="d-flex justify-content-between gap-2 flex-wrap align-items-center mb-2">
   <h2 class="ut-section-title mb-0"><i class="bi bi-telephone"></i> การติดตามหน้าหลัก</h2>
   {{-- UNIVERSITY_SCHOOL_FOLLOWUP_ADD_BUTTON_V1 --}}
@if($canCreateSchoolFollowup)
<a class="ut-btn ut-btn-primary ut-no-print"
   href="{{ route('school_followup_add', $client->id) }}"
   title="เพิ่มข้อมูลการติดตามผลการเรียน">
    <i class="bi bi-plus-circle"></i> เพิ่มข้อมูล
</a>
@endif
  </div>
  <div class="ut-help mb-2">ผู้ติดต่อ โทรศัพท์ วิธีติดตาม ผลการติดต่อ และหมายเหตุ ให้บันทึกที่หน้า School Followup เพียงแห่งเดียว</div>
  <div class="ut-timeline">
   @forelse($schoolFollowups as $sf)
    <div class="ut-timeline-item">
      <div class="ut-timeline-title">{{ $sf->follow_date ? \Carbon\Carbon::parse($sf->follow_date)->format('d/m/Y') : '-' }} · {{ $schoolFollowTypeLabels[$sf->follow_type] ?? ($sf->follow_type ?: '-') }}</div>
      <div class="ut-timeline-text">{{ $sf->result ?: '-' }}@if($sf->teacher_name) · ผู้ติดต่อ {{ $sf->teacher_name }}@endif</div>
    </div>
   @empty
    <div class="ut-empty"><p>ยังไม่มีข้อมูล School Followup ในภาคเรียนนี้</p></div>
   @endforelse
  </div>
 </div></div>

 <div class="ut-col-6"><div class="ut-card ut-content h-100">
  <div class="d-flex justify-content-between gap-2 flex-wrap align-items-center mb-2">
   <div>
    <h2 class="ut-section-title mb-0"><i class="bi bi-person-check"></i> การติดตามเด็กมหาวิทยาลัย</h2>
    <div class="ut-help">ครั้งที่รันอัตโนมัติภายในภาคเรียนนี้ และภาคเรียนใหม่เริ่มครั้งที่ 1 ใหม่</div>
   </div>
   @if($universityPermissions['create'])<a class="ut-btn ut-btn-primary ut-no-print" href="{{ route('university.followups.create',$record->id) }}"><i class="bi bi-plus-circle"></i> เพิ่มการติดตาม</a>@endif
  </div>
  <div class="ut-timeline mt-3">
   @forelse($record->followups as $f)
    <div class="ut-timeline-item">
      <div class="d-flex justify-content-between gap-2 align-items-start">
       <div>
        <div class="ut-timeline-title">ครั้งที่ {{ $f->sequence_no }} · {{ optional($f->followup_date)->format('d/m/Y') ?: '-' }}</div>
        <div class="ut-timeline-text">
          ความเสี่ยง: {{ config('university_tracking.risk_levels.'.$f->overall_risk_level,$f->overall_risk_level) }}
          @if($f->continuation_risk_note) · {{ $f->continuation_risk_note }}@elseif($f->academic_progress) · {{ $f->academic_progress }}@endif
        </div>
       </div>
       @if($universityPermissions['update'])<a class="ut-btn ut-btn-light ut-no-print" href="{{ route('university.followups.edit',$f->id) }}"><i class="bi bi-pencil"></i></a>@endif
      </div>
    </div>
   @empty
    <div class="ut-empty"><p>ยังไม่มีการติดตามเด็กมหาวิทยาลัยในภาคเรียนนี้</p></div>
   @endforelse
  </div>
 </div></div>
</div>
<div class="ut-grid"><div class="ut-col-8"><div class="ut-card ut-content h-100"><h2 class="ut-section-title"><i class="bi bi-list-check"></i> ผลรายวิชา</h2><div class="ut-table-wrap"><table class="ut-table"><thead><tr><th>รหัส</th><th>รายวิชา</th><th>หน่วยกิต</th><th>เกรด</th><th>Grade point</th><th>ผล</th></tr></thead><tbody>@forelse($record->subjects as $s)<tr><td>{{ $s->course_code ?: '-' }}</td><td>{{ $s->course_name }}</td><td>{{ $s->credits ?? '-' }}</td><td>{{ $s->grade ?: '-' }}</td><td>{{ $s->grade_point ?? '-' }}</td><td>{{ ['pass'=>'ผ่าน','fail'=>'ไม่ผ่าน','withdrawn'=>'W','incomplete'=>'I','satisfactory'=>'S','unsatisfactory'=>'U','audit'=>'Audit','other'=>'อื่น ๆ','pending'=>'รอผล'][$s->result_status] ?? ($s->result_status ?: 'รอผล') }}</td></tr>@empty<tr><td colspan="6"><div class="ut-empty"><p>ยังไม่มีรายวิชา</p></div></td></tr>@endforelse</tbody></table></div></div></div>
<div class="ut-col-4"><div class="ut-card ut-content h-100"><h2 class="ut-section-title"><i class="bi bi-file-earmark-pdf"></i> PDF ผลการเรียนรายภาคเรียน</h2>
@if($universityPermissions['create'])<form method="POST" action="{{ route('university.documents.store',$record->id) }}" enctype="multipart/form-data" class="ut-no-print mb-3">@csrf<div class="ut-field mb-2"><label>ประเภทเอกสาร</label><select class="form-select" name="document_type"><option value="grade_report">ผลการเรียนประจำภาคเรียน</option><option value="transcript">Transcript</option><option value="registration">ใบลงทะเบียนเรียน</option><option value="other">อื่น ๆ</option></select></div><div class="ut-field mb-2"><label>ไฟล์ PDF</label><input type="file" accept="application/pdf,.pdf" class="form-control" name="pdf_file" required><div class="ut-help">สูงสุด {{ number_format(config('university_tracking.max_pdf_kb')/1024) }} MB · เก็บใน private storage</div></div><button class="ut-btn ut-btn-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i> อัปโหลด PDF</button></form>@endif
@forelse($record->documents as $doc)<div class="ut-document"><div><div class="ut-document-name"><i class="bi bi-file-earmark-pdf text-danger me-1"></i>{{ $doc->original_name }}</div><div class="ut-document-meta">{{ number_format(($doc->file_size ?? 0)/1024,1) }} KB · {{ optional($doc->uploaded_at)->format('d/m/Y H:i') }}</div></div><div class="ut-actions ut-no-print"><a target="_blank" class="ut-btn ut-btn-light" href="{{ route('university.documents.view',$doc->id) }}"><i class="bi bi-eye"></i></a>@if($universityPermissions['print'])<a class="ut-btn ut-btn-light" href="{{ route('university.documents.download',$doc->id) }}"><i class="bi bi-download"></i></a>@endif @if($universityPermissions['delete'])<form method="POST" action="{{ route('university.documents.destroy',$doc->id) }}" class="ut-delete-form" data-message="ลบไฟล์ PDF ผลการเรียนนี้หรือไม่?">@csrf @method('DELETE')<button class="ut-btn ut-btn-danger"><i class="bi bi-trash"></i></button></form>@endif</div></div>@empty<div class="ut-empty"><i class="bi bi-file-earmark-pdf"></i><h3>ยังไม่มี PDF</h3><p>แนบผลการเรียนจริงของภาคเรียนนี้เพื่อเป็นหลักฐานย้อนหลัง</p></div>@endforelse
</div></div></div>
@if($record->risk_note || $record->semester_summary)<div class="ut-card ut-content mt-3"><div class="ut-grid">@if($record->risk_note)<div class="ut-col-6"><h2 class="ut-section-title"><i class="bi bi-exclamation-triangle"></i> ข้อสังเกตความเสี่ยง</h2><div>{{ $record->risk_note }}</div></div>@endif @if($record->semester_summary)<div class="ut-col-6"><h2 class="ut-section-title"><i class="bi bi-card-text"></i> สรุปภาคเรียน</h2><div>{{ $record->semester_summary }}</div></div>@endif</div></div>@endif
</div></div>@include('university.partials.delete_confirm')@endsection
