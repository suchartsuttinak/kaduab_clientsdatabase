@extends('admin_client.admin_client')

@section('content')
    @php
        $clientName = $client->full_name ?? $client->fullname ?? '-';
    @endphp

    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex align-items-center">
            <i class="bi bi-file-earmark-plus text-success me-3" style="font-size: 1.5rem;"></i>
            <h5 class="mb-0">
                เพิ่มไฟล์สำหรับ <span class="text-primary">{{ $clientName }}</span>
            </h5>
        </div>
    </div>

    {{-- แสดง error validation --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        id="client-file-form"
        action="{{ route('client_files.store', $client->id) }}"
        method="POST"
        enctype="multipart/form-data"
        class="card p-3 shadow-sm"
        novalidate>
        @csrf

        <div class="mb-3">
            <label for="file_type" class="form-label">
                เลือกประเภทเอกสาร <span class="text-danger">*</span>
            </label>

            <select
                name="file_type"
                id="file_type"
                class="form-select @error('file_type') is-invalid @enderror"
                required>

                <option value="">
                    -- โปรดเลือกประเภทเอกสาร --
                </option>

                @foreach($fileTypes as $key => $label)
                    <option value="{{ $key }}" {{ old('file_type') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <div class="invalid-feedback" id="file_type_error">
                กรุณาเลือกประเภทเอกสาร
            </div>
        </div>

        <div class="mb-3">
            <label for="file" class="form-label">
                ไฟล์ PDF <span class="text-danger">*</span>
            </label>

            <input
                type="file"
                name="file"
                id="file"
                class="form-control @error('file') is-invalid @enderror"
                accept=".pdf,application/pdf"
                required>

            <small class="text-muted">
                รองรับเฉพาะไฟล์ PDF ขนาดไม่เกิน {{ $maxFileSizeMb }} MB
            </small>

            <div class="invalid-feedback" id="file_error">
                กรุณาเลือกไฟล์เอกสาร PDF
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i> บันทึก
            </button>

            <a href="{{ route('client_files.index', $client->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left-circle"></i> กลับหน้าหลัก
            </a>
        </div>
    </form>

    <script>
        document.getElementById('client-file-form').addEventListener('submit', function(e) {
            let isValid = true;

            const fileType = document.getElementById('file_type');
            const file = document.getElementById('file');

            fileType.classList.remove('is-invalid');
            file.classList.remove('is-invalid');

            if (!fileType.value) {
                fileType.classList.add('is-invalid');
                isValid = false;
            }

            if (!file.value) {
                file.classList.add('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        document.getElementById('file_type').addEventListener('change', function() {
            if (this.value) {
                this.classList.remove('is-invalid');
            }
        });

        document.getElementById('file').addEventListener('change', function() {
            if (this.value) {
                this.classList.remove('is-invalid');
            }
        });
    </script>
@endsection