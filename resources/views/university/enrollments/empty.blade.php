@extends(config('university_tracking.client_layout', 'admin_client.admin_client'))
@section('content')
<link rel="stylesheet" href="{{ asset('backend/assets/css/university-tracking.css') }}">
<div class="container-fluid ut-page"><div class="ut-wrap py-3">
@include('university.partials.flash')
<div class="ut-card ut-header">
    <div class="ut-title-row"><div class="ut-icon"><i class="bi bi-mortarboard"></i></div><div><h1 class="ut-title">การศึกษาระดับอุดมศึกษา</h1><p class="ut-subtitle">@include('university.partials.client_name',['client'=>$client])</p></div></div>
    <div class="ut-actions"><a href="{{ route('admin.index',$client->id) }}" class="ut-btn ut-btn-light"><i class="bi bi-x-lg"></i> ปิด</a></div>
</div>
<div class="ut-card ut-empty">
    <i class="bi bi-mortarboard"></i><h3>ยังไม่มีประวัติการศึกษาระดับมหาวิทยาลัย</h3><p>เริ่มสร้างประวัติเมื่อผู้รับบริการเข้าศึกษาต่อระดับมหาวิทยาลัย</p>
    @if($universityPermissions['create'])<a href="{{ route('university.enrollments.create',$client->id) }}" class="ut-btn ut-btn-primary"><i class="bi bi-plus-lg"></i> สร้างประวัติมหาวิทยาลัย</a>@endif
</div>
</div></div>
@endsection
