@extends('admin.admin_master')

@section('admin')
<div class="container-fluid py-4 user-form-page">
    <div class="user-form-card">
        <div class="user-form-header">
            <div class="user-form-header-left">
                <div class="user-form-header-icon">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div>
                    <h4 class="user-form-page-title mb-1">เพิ่มผู้ใช้งาน</h4>
                    <div class="text-muted small">กำหนดบัญชี โครงการ บ้าน บทบาท และสิทธิ์รายฟอร์มในหน้าเดียว</div>
                </div>
            </div>

            <a href="{{ route('users.index') }}" class="btn user-form-back">
                <i class="bi bi-arrow-left"></i>
                กลับหน้ารายการ
            </a>
        </div>

        <div class="user-form-body">
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('backend.users._form_fields', ['user' => null])
            </form>
        </div>
    </div>
</div>
@endsection
