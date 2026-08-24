@if($errors->any())
<div class="alert alert-danger mb-3">
    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> กรุณาตรวจสอบข้อมูล</div>
    <ul class="mb-0 ps-3 small">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif
