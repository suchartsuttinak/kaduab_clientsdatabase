@extends('admin.admin_master')

@section('admin')
<div class="container-fluid py-4 user-form-page">
    <div class="user-form-card">
        <div class="user-form-header">
            <div class="user-form-header-left">
                <div class="user-form-header-icon">
                    <i class="bi bi-person-gear"></i>
                </div>
                <div>
                    <h4 class="user-form-page-title mb-1">แก้ไขผู้ใช้งาน</h4>
                    <div class="text-muted small">ปรับข้อมูลขอบเขตการดูแลและสิทธิ์รายฟอร์มของ {{ $user->name }}</div>
                </div>
            </div>

            <a href="{{ route('users.index') }}" class="btn user-form-back">
                <i class="bi bi-arrow-left"></i>
                กลับหน้ารายการ
            </a>
        </div>

        <div class="user-form-body">
            <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('backend.users._form_fields', ['user' => $user])
            </form>
        </div>
    </div>
</div>
@endsection
