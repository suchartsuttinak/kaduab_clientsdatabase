@extends('admin_client.admin_client')
@section('content')
    <h5 class="mb-3 text-center fw-bold mt-4" style="color:#0d47a1;">
        @if ($mode == 'edit')
            แก้ไขข้อมูลการออกจากสถานสงเคราะห์ของ {{ $client->fullname }}
        @elseif($mode == 'copy')
            แก้ไขข้อมูลการออกจากสถานสงเคราะห์ของ {{ $client->fullname }}
        @else
            เพิ่มข้อมูลการออกจากสถานสงเคราะห์ของ {{ $client->fullname }}
        @endif
    </h5>

    <form action="{{ in_array($mode, ['edit','copy']) ? 
            route('escape.update', $escape->id) : route('escape.store') }}" method="POST" class="card p-3 shadow-sm">
            @csrf
            @if (in_array($mode, ['edit','copy']))
                @method('PUT')
            @endif
        
        <input type="hidden" name="client_id" value="{{ $client->id }}">

        <div class="row g-3">
           <div class="col-md-3">
                    <label class="form-label">
                        วันที่ออก <span class="text-danger">*</span>
                    </label>

                    <input type="date"
                        name="retire_date"
                        class="form-control @error('retire_date') is-invalid @enderror"
                        value="{{ old('retire_date', isset($escape) ? $escape->retire_date?->format('Y-m-d') : now('Asia/Bangkok')->toDateString()) }}"
                        max="{{ now('Asia/Bangkok')->toDateString() }}"
                        required>

                    @error('retire_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            <div class="col-md-4">
                <label class="form-label">ประเภทการออกจากหน่วยงาน</label>
                <select name="retire_id" class="form-select" required>
                    <option value="">-- เลือก --</option>
                    @foreach ($retires as $ret)
                        <option value="{{ $ret->id }}"
                            {{ old('retire_id', $escape->retire_id ?? '') == $ret->id ? 'selected' : '' }}>
                            {{ $ret->retire_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-12">
                <label class="form-label">เรื่องราว/สาเหตุ</label>
                <textarea name="stories" class="form-control" rows="3" placeholder="บันทึกสาเหตุหรือรายละเอียดเพิ่มเติม">{{ old('stories', $escape->stories ?? '') }}</textarea>
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-between">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left-circle me-1"></i> ย้อนกลับ
            </a>
            <button type="submit" class="btn btn-success">
                @if (isset($mode) && ($mode == 'edit' || $mode == 'copy'))
                    <i class="bi bi-pencil-square me-1"></i> อัปเดทข้อมูล
                @else
                    <i class="bi bi-save me-1"></i> บันทึกข้อมูล
                @endif
            </button>
        </div>
    </form>
@endsection
