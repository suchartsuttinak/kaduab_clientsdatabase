@extends(config('university_tracking.client_layout', 'admin_client.admin_client'))
@section('content')
<link rel="stylesheet" href="{{ asset('backend/assets/css/university-tracking.css') }}">
<div class="container-fluid ut-page"><div class="ut-wrap py-3">
@include('university.partials.flash')
@php
    $latestSemesterName = $latestEducationRecord?->semester_label ?? data_get($latestEducationRecord,'semester.semester_name');
@endphp
<div class="ut-card ut-header">
  <div class="ut-title-row"><div class="ut-icon"><i class="bi bi-mortarboard-fill"></i></div><div><h1 class="ut-title">{{ $isEdit ? 'แก้ไขข้อมูลมหาวิทยาลัย' : 'สร้างประวัติการศึกษาระดับมหาวิทยาลัย' }}</h1><p class="ut-subtitle">@include('university.partials.client_name',['client'=>$client])</p></div></div>
  <div class="ut-actions"><a href="{{ $isEdit ? route('university.enrollments.show',$enrollment->id) : route('university.client',$client->id) }}" class="ut-btn ut-btn-light"><i class="bi bi-x-lg"></i> ปิด</a></div>
</div>
@include('university.partials.validation')

@if(!$isEdit)
<div class="ut-card ut-content mb-3">
    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-2">
        <h2 class="ut-section-title mb-0">
            <i class="bi bi-journal-check"></i> ข้อมูลการศึกษาปัจจุบัน
        </h2>
        <span class="ut-badge ut-badge-gray">ดึงจาก Education Record · ไม่กรอกซ้ำ</span>
    </div>
    <div class="ut-help mb-3">
        ภาคเรียน ระดับการศึกษา และสถานศึกษา อ้างอิงจากหน้า “บันทึกผลการเรียน” รายการล่าสุด เช่นเดียวกับหน้า School Followup
    </div>
    <div class="ut-info-list">
        <div class="ut-info">
            <div class="ut-info-label">ภาคเรียน</div>
            <div class="ut-info-value">{{ $latestSemesterName ?: '-' }}</div>
        </div>
        <div class="ut-info">
            <div class="ut-info-label">ระดับการศึกษา</div>
            <div class="ut-info-value">{{ data_get($latestEducationRecord,'education.education_name','-') }}</div>
        </div>
        <div class="ut-info">
            <div class="ut-info-label">สถานศึกษา</div>
            <div class="ut-info-value">{{ data_get($latestEducationRecord,'school_name','-') }}</div>
        </div>
    </div>
</div>
@endif

<form method="POST" action="{{ $isEdit ? route('university.enrollments.update',$enrollment->id) : route('university.enrollments.store',$client->id) }}" class="ut-card ut-content" id="utEnrollmentForm">
@csrf @if($isEdit) @method('PUT') @endif
<div class="ut-section"><h2 class="ut-section-title"><i class="bi bi-building"></i> สถานศึกษาและหลักสูตร</h2>
<div class="ut-grid">
 @if($isEdit)
 <div class="ut-field ut-col-6"><label>ชื่อมหาวิทยาลัย <span class="ut-required">*</span></label><input class="form-control" name="university_name" value="{{ old('university_name',$enrollment->university_name) }}" required></div>
 @else
 <input type="hidden" name="university_name" value="{{ old('university_name',data_get($latestEducationRecord,'school_name')) }}">
 @endif
 <div class="ut-field ut-col-3"><label>รหัสนักศึกษา</label><input class="form-control" name="student_code" value="{{ old('student_code',$enrollment->student_code) }}"></div>
 <div class="ut-field ut-col-3"><label>ประเภทหลักสูตร</label><input class="form-control" name="program_type" value="{{ old('program_type',$enrollment->program_type) }}" placeholder="เช่น ปกติ / นานาชาติ"></div>
 <div class="ut-field ut-col-4"><label>คณะ <span class="ut-required">*</span></label><input class="form-control" name="faculty" value="{{ old('faculty',$enrollment->faculty) }}" required></div>
 <div class="ut-field ut-col-4"><label>สาขาวิชา / วิชาเอก <span class="ut-required">*</span></label><input class="form-control" name="major" value="{{ old('major',$enrollment->major) }}" required></div>
 <div class="ut-field ut-col-4"><label>ชื่อปริญญา</label><input class="form-control" name="degree_name" value="{{ old('degree_name',$enrollment->degree_name) }}" placeholder="เช่น ศิลปศาสตรบัณฑิต"></div>
</div></div>
<div class="ut-divider"></div>
<div class="ut-section"><h2 class="ut-section-title"><i class="bi bi-calendar3"></i> การเข้าเรียนและแผนการศึกษา</h2>
<div class="ut-grid">
 <div class="ut-field ut-col-3"><label>ปีการศึกษาที่เข้า <span class="ut-required">*</span></label><input type="number" min="2400" max="2800" class="form-control" name="admission_academic_year" value="{{ old('admission_academic_year',$enrollment->admission_academic_year ?: ($latestSemesterName ? (int)substr($latestSemesterName,-4) : now('Asia/Bangkok')->year+543)) }}" required></div>
 <div class="ut-field ut-col-3"><label>ภาคเรียนที่เข้า</label><select class="form-select" name="admission_term"><option value="">ไม่ระบุ</option>@foreach([1,2,3] as $term)<option value="{{ $term }}" @selected((string)old('admission_term',$enrollment->admission_term)===(string)$term)>{{ $term }}</option>@endforeach</select></div>
 <div class="ut-field ut-col-3"><label>วันที่เข้าศึกษา</label><input type="date" max="{{ now('Asia/Bangkok')->toDateString() }}" class="form-control" name="admission_date" value="{{ old('admission_date',optional($enrollment->admission_date)->format('Y-m-d')) }}"></div>
 <div class="ut-field ut-col-3"><label>ระยะเวลาหลักสูตร (ปี)</label><input type="number" min="1" max="8" class="form-control" name="curriculum_years" value="{{ old('curriculum_years',$enrollment->curriculum_years ?: 4) }}"></div>
 <div class="ut-field ut-col-3"><label>ปีที่คาดว่าจะจบ</label><input type="number" min="2400" max="2800" class="form-control" name="expected_graduation_year" value="{{ old('expected_graduation_year',$enrollment->expected_graduation_year) }}"></div>
 @php
     $editableEnrollmentStatuses = ['studying', 'leave', 'lost_contact', 'other'];
     $currentEnrollmentStatus = old('current_status', $enrollment->current_status ?: 'studying');
     if ($isEdit && in_array($enrollment->current_status, ['transferred','graduated','dropout','dismissed'], true)) {
         $editableEnrollmentStatuses[] = $enrollment->current_status;
     }
     $enrollmentStatusOptions = collect(config('university_tracking.enrollment_statuses'))
         ->only(array_values(array_unique($editableEnrollmentStatuses)));
 @endphp
 <div class="ut-field ut-col-3"><label>สถานะปัจจุบัน <span class="ut-required">*</span></label><select class="form-select" name="current_status" required>@foreach($enrollmentStatusOptions as $key=>$label)<option value="{{ $key }}" @selected($currentEnrollmentStatus===$key)>{{ $label }}</option>@endforeach</select><div class="ut-help">สำเร็จการศึกษา / ออกกลางคัน / พ้นสภาพ / ย้าย ให้บันทึกผ่าน “ผลสิ้นสุดการศึกษา” เพื่อเก็บเหตุผลและสถิติให้ครบ</div></div>
</div></div>
<div class="ut-divider"></div>
<div class="ut-section"><h2 class="ut-section-title"><i class="bi bi-cash-coin"></i> ทุนและค่าใช้จ่ายด้านการศึกษา</h2>
<div class="ut-grid">
 <div class="ut-field ut-col-4"><label>แหล่งทุน/รูปแบบค่าใช้จ่าย</label><input class="form-control" name="funding_type" value="{{ old('funding_type',$enrollment->funding_type) }}" placeholder="เช่น ทุนสถานสงเคราะห์ / กยศ. / ครอบครัว"></div>
 <div class="ut-field ut-col-4"><label>ชื่อทุน</label><input class="form-control" name="scholarship_name" value="{{ old('scholarship_name',$enrollment->scholarship_name) }}"></div>
 <div class="ut-field ut-col-4"><label>จำนวนทุน (บาท)</label><input type="number" min="0" step="0.01" class="form-control" name="scholarship_amount" value="{{ old('scholarship_amount',$enrollment->scholarship_amount) }}"></div>
 <div class="ut-field ut-col-12"><label>หมายเหตุ</label><textarea class="form-control" name="note" rows="3">{{ old('note',$enrollment->note) }}</textarea></div>
</div></div>
<div class="ut-actions justify-content-end mt-3"><button class="ut-btn ut-btn-primary" type="submit" data-permission-action="{{ $isEdit ? 'update' : 'create' }}"><i class="bi bi-floppy"></i> {{ $isEdit ? 'บันทึกการแก้ไข' : 'สร้างประวัติมหาวิทยาลัย' }}</button></div>
</form></div></div>
@endsection
