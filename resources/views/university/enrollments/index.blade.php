@extends('admin.admin_master')
@section('admin')
<link rel="stylesheet" href="{{ asset('backend/assets/css/university-tracking.css') }}">
<div class="container-fluid ut-page"><div class="ut-wrap py-3">
@include('university.partials.flash')
<div class="ut-card ut-header">
    <div class="ut-title-row"><div class="ut-icon"><i class="bi bi-people-fill"></i></div><div><h1 class="ut-title">รายชื่อนักศึกษาระดับมหาวิทยาลัย</h1><p class="ut-subtitle">ค้นหาและเปิดประวัติการติดตามรายบุคคล</p></div></div>
    <div class="ut-actions"><a href="{{ route('university.dashboard') }}" class="ut-btn ut-btn-light"><i class="bi bi-speedometer2"></i> Dashboard</a></div>
</div>
<form method="GET" class="ut-card ut-filter">
<div class="ut-filter-row">
    <div class="ut-field ut-field-grow"><label>ค้นหา</label><input class="form-control" name="q" value="{{ request('q') }}" placeholder="รหัสนักศึกษา / มหาวิทยาลัย / คณะ / สาขา"></div>
    <div class="ut-field"><label>ปีการศึกษา</label><input class="form-control" type="number" name="academic_year" value="{{ request('academic_year') }}" placeholder="เช่น 2569"></div>
    <div class="ut-field"><label>สถานะ</label><select class="form-select" name="status"><option value="">ทั้งหมด</option>@foreach(config('university_tracking.enrollment_statuses') as $key=>$label)<option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>@endforeach</select></div>
    <button class="ut-btn ut-btn-primary"><i class="bi bi-search"></i> ค้นหา</button>
    <a href="{{ route('university.enrollments.index') }}" class="ut-btn ut-btn-light"><i class="bi bi-arrow-counterclockwise"></i> ล้าง</a>
</div>
</form>
<div class="ut-card ut-content">
<div class="ut-table-wrap"><table class="ut-table">
<thead><tr><th>ผู้รับบริการ</th><th>รหัสนักศึกษา</th><th>มหาวิทยาลัย</th><th>คณะ / สาขา</th><th>ปีที่เข้า</th><th>สถานะ</th><th>ข้อมูลล่าสุด</th><th class="ut-center">จัดการ</th></tr></thead>
<tbody>
@forelse($enrollments as $item)
@php $latest=$item->semesterRecords->first(); @endphp
<tr>
<td>@include('university.partials.client_name',['client'=>$item->client])</td><td>{{ $item->student_code ?: '-' }}</td><td>{{ $item->university_name }}</td><td>{{ $item->faculty }}<div class="ut-muted small">{{ $item->major }}</div></td><td>{{ $item->admission_academic_year }}</td><td><span class="ut-badge ut-badge-info">{{ config('university_tracking.enrollment_statuses.'.$item->current_status,$item->current_status) }}</span></td><td>@if($latest)ปี {{ $latest->year_level }} · {{ $latest->term }}/{{ $latest->academic_year }} · GPA {{ $latest->display_gpa ?? '-' }}@else<span class="ut-muted">ยังไม่มีภาคเรียน</span>@endif</td><td class="ut-center"><a href="{{ route('university.enrollments.show',$item) }}" class="ut-btn ut-btn-light"><i class="bi bi-eye"></i> ดู</a></td>
</tr>
@empty<tr><td colspan="8"><div class="ut-empty"><i class="bi bi-people"></i><h3>ไม่พบข้อมูลนักศึกษา</h3><p>เพิ่มประวัติจากหน้าโปรไฟล์ผู้รับบริการแต่ละราย</p></div></td></tr>@endforelse
</tbody></table></div>
<div class="mt-3">{{ $enrollments->links() }}</div>
</div>
</div></div>
@endsection
