@extends('admin.admin_master')
@section('admin')

<div class="container py-4">
    <div class="card border-0 shadow-sm" style="border-radius:18px;">
        <div class="card-header bg-white border-0 py-3">
            <h4 class="mb-0 fw-bold">แก้ไขข้อมูลประชาสัมพันธ์</h4>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('publicizes.update', $publicize->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">วันที่บันทึก</label>
                        <input type="date"
                               name="recorded_at"
                               class="form-control @error('recorded_at') is-invalid @enderror"
                               value="{{ old('recorded_at', optional($publicize->recorded_at)->format('Y-m-d')) }}">
                        @error('recorded_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">ประเภท</label>
                        <select name="category" class="form-select @error('category') is-invalid @enderror">
                            <option value="">-- เลือกประเภท --</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category', $publicize->category) == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">ชื่อเรื่อง</label>
                        <input type="text"
                               name="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $publicize->title) }}">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">ไฟล์ PDF</label>
                        <input type="file" name="file" accept="application/pdf" class="form-control @error('file') is-invalid @enderror">
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text mt-2">
                            ไฟล์ปัจจุบัน:
                            <a href="{{ route('publicizes.file', $publicize) }}" target="_blank">
                                {{ $publicize->file_name ?? basename($publicize->file_path) }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('publicizes.index', ['category' => $publicize->category]) }}" class="btn btn-light border">ยกเลิก</a>
                    <button type="submit" class="btn btn-primary">อัปเดตข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection