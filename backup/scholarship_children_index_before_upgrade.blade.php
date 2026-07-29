@extends('admin.admin_master')

@section('admin')

<style>
    .sc-page{
        padding:20px;
        background:#f6f8fc;
        min-height:100vh;
    }

    .sc-header{
        background:linear-gradient(135deg,#eef2ff,#dbeafe);
        border:1px solid #dbeafe;
        border-radius:24px;
        padding:24px;
        margin-bottom:20px;
        box-shadow:0 14px 35px rgba(37,99,235,.12);
        position:relative;
        overflow:hidden;
    }

    .sc-header::before{
        content:"";
        position:absolute;
        right:-70px;
        top:-70px;
        width:190px;
        height:190px;
        border-radius:50%;
        background:rgba(79,70,229,.12);
    }

    .sc-header h4{
        margin:0;
        font-weight:800;
        color:#1e3a8a;
        letter-spacing:.2px;
    }

    .sc-header p{
        margin:8px 0 0;
        color:#475569;
        font-weight:500;
    }

    .sc-card{
        background:#fff;
        border-radius:22px;
        border:1px solid #e5e7eb;
        box-shadow:0 14px 32px rgba(15,23,42,.07);
        padding:18px;
    }

    .sc-toolbar{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
        margin-bottom:16px;
        padding-bottom:14px;
        border-bottom:1px solid #eef2f7;
    }

    .sc-filter{
        display:flex;
        align-items:center;
        gap:10px;
        flex-wrap:nowrap;
        flex:1;
        min-width:0;
        overflow-x:auto;
        padding-bottom:3px;
    }

    .sc-filter input{
        flex:1;
        min-width:240px;
    }

    .sc-filter select{
        width:190px;
        flex-shrink:0;
    }

    .sc-filter .btn{
        white-space:nowrap;
        flex-shrink:0;
    }

    .sc-add-btn{
        white-space:nowrap;
        flex-shrink:0;
    }

    .sc-table-wrap{
        width:100%;
        overflow-x:auto;
        border-radius:16px;
        border:1px solid #e5e7eb;
    }

    .sc-table{
        min-width:1050px;
        vertical-align:middle;
        margin-bottom:0;
    }

    .sc-table thead th{
        background:linear-gradient(135deg,#eff6ff,#eef2ff);
        color:#1e3a8a;
        font-weight:800;
        font-size:14px;
        border-bottom:1px solid #dbeafe;
        white-space:nowrap;
        padding:14px 12px;
    }

    .sc-table tbody td{
        color:#334155;
        font-size:14px;
        padding:13px 12px;
    }

    .sc-table tbody tr:hover{
        background:#f8fafc;
    }

    .sc-name{
        color:#1e293b;
        font-weight:800;
    }

    .sc-subtext{
        color:#64748b;
        font-size:13px;
        margin-top:3px;
    }

    .sc-photo{
        width:54px;
        height:54px;
        border-radius:15px;
        object-fit:cover;
        border:1px solid #dbeafe;
        box-shadow:0 6px 12px rgba(15,23,42,.08);
    }

    .sc-avatar{
        width:54px;
        height:54px;
        border-radius:15px;
        background:#eef2ff;
        color:#4f46e5;
        display:flex;
        align-items:center;
        justify-content:center;
        font-weight:800;
    }

    .sc-year-badge{
        background:#eef2ff;
        color:#3730a3;
        border:1px solid #dbeafe;
        padding:6px 10px;
        border-radius:999px;
        font-weight:800;
        font-size:13px;
    }

    .sc-empty{
        text-align:center;
        padding:42px 20px;
        color:#64748b;
        background:#f8fafc;
        border-radius:18px;
        border:1px dashed #cbd5e1;
    }

    .modal-content{
        border:none;
        border-radius:22px;
        overflow:hidden;
    }

    .modal-header{
        background:linear-gradient(135deg,#f8fafc,#eef2ff);
        border-bottom:1px solid #e5e7eb;
    }

    .modal-title{
        font-weight:800;
        color:#1e3a8a;
    }

    .form-label{
        font-weight:700;
        color:#334155;
        font-size:14px;
    }

    .form-control,
    .form-select{
        border-radius:12px;
        border-color:#d1d5db;
    }

    .form-control:focus,
    .form-select:focus{
        border-color:#6366f1;
        box-shadow:0 0 0 .2rem rgba(99,102,241,.15);
    }

    .detail-box{
        background:#f8fafc;
        border:1px solid #e5e7eb;
        border-radius:14px;
        padding:12px 14px;
        height:100%;
    }

    .detail-label{
        font-size:13px;
        color:#64748b;
        margin-bottom:4px;
    }

    .detail-value{
        font-weight:700;
        color:#1e293b;
        white-space:pre-line;
    }

    .btn{
        border-radius:12px;
        font-weight:700;
    }

    .btn-sm{
        border-radius:10px;
    }

    .sc-action-buttons{
        display:flex;
        justify-content:center;
        gap:6px;
        flex-wrap:nowrap;
    }

    @media(max-width:768px){
        .sc-toolbar{
            align-items:stretch;
        }

        .sc-filter{
            width:100%;
            flex:0 0 100%;
        }

        .sc-add-btn{
            width:100%;
        }
    }

    @media(max-width:576px){
        .sc-page{
            padding:12px;
        }

        .sc-header{
            padding:18px;
            border-radius:18px;
        }

        .modal-dialog{
            margin:8px;
        }

        .modal-body{
            max-height:72vh;
            overflow-y:auto;
        }
    }
</style>

<div class="sc-page">

    <div class="sc-header">
        <h4>
            <i class="bi bi-mortarboard-fill me-2"></i>
            ข้อมูลผู้ขอรับทุนการศึกษา
        </h4>
        <p>จัดเก็บข้อมูลเด็กที่ขอรับทุนการศึกษา แยกตามปีการศึกษา โดยไม่ผูกกับข้อมูลผู้รับบริการ</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
            <i class="bi bi-check-circle-fill me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>กรุณาตรวจสอบข้อมูล</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="sc-card">

        <div class="sc-toolbar">
              {{-- ✅ แสดงเฉพาะตอนมีข้อมูล --}}
    @if($children->count() > 0)
        <form method="GET"
              action="{{ route('scholarship.children.index') }}"
              class="sc-filter"
              id="childSearchForm">

            <input type="text"
                   name="keyword"
                   id="keywordInput"
                   class="form-control"
                   placeholder="ค้นหาชื่อ / นามสกุล"
                   value="{{ request('keyword') }}"
                   autocomplete="off">

            <select name="academic_year"
                    class="form-select"
                    onchange="this.form.submit()">
                <option value="">ทุกปีการศึกษา</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" {{ $academicYear == $year ? 'selected' : '' }}>
                        ปีการศึกษา {{ $year }}
                    </option>
                @endforeach
            </select>

            <a href="{{ route('scholarship.children.index') }}"
               class="btn btn-outline-secondary">
                ล้างค่า
            </a>

            <a href="{{ route('scholarship.children.report', [
                    'academic_year' => $academicYear,
                    'keyword' => request('keyword')
                ]) }}"
               class="btn btn-outline-dark">
                <i class="bi bi-file-earmark-text"></i> รายงาน
            </a>
        </form>
    @endif

    {{-- ✅ แสดงตลอด --}}
    <button type="button"
            class="btn btn-success sc-add-btn"
            data-bs-toggle="modal"
            data-bs-target="#createChildModal">
        <i class="bi bi-plus-circle"></i> เพิ่มผู้ขอรับทุน
    </button>
        </div>

        @if($children->count() > 0)
            <div class="sc-table-wrap">
                <table class="table table-hover align-middle sc-table">
                    <thead>
                        <tr>
                            <th width="70">ภาพ</th>
                            <th>ชื่อ - นามสกุล</th>
                            <th>อายุ</th>
                            <th>ระดับการศึกษา</th>
                            <th>สถานศึกษา</th>
                            <th>ปีการศึกษา</th>
                            <th>ผู้ปกครอง</th>
                            <th>โทรศัพท์</th>
                            <th width="220" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($children as $child)
                            <tr>
                            <td>
                                @if($child->photo)

                                    @php
                                        $photoUrl = str_starts_with($child->photo, 'upload/')
                                            ? asset($child->photo)
                                            : asset('storage/' . $child->photo);
                                    @endphp

                                    {{-- =====================================================
                                        PATCH:
                                        Lazy Load + Async Decode
                                        ช่วยให้ตารางโหลดเร็วขึ้นเมื่อมีรูปจำนวนมาก
                                    ====================================================== --}}
                                    <img src="{{ $photoUrl }}"
                                        loading="lazy"
                                        decoding="async"
                                        class="sc-photo"
                                        alt="photo">

                                @else
                                    <div class="sc-avatar">
                                        {{ mb_substr($child->first_name, 0, 1) }}
                                    </div>
                                @endif
                            </td>

                                <td>
                                    <div class="sc-name">{{ $child->first_name }} {{ $child->last_name }}</div>
                                    <div class="sc-subtext">{{ \Illuminate\Support\Str::limit($child->reason, 45) }}</div>
                                </td>

                                <td>{{ $child->age ?? '-' }}</td>
                                <td>{{ $child->education_level ?? '-' }}</td>
                                <td>{{ $child->school_name ?? '-' }}</td>

                                <td>
                                    <span class="sc-year-badge">{{ $child->academic_year }}</span>
                                </td>

                                <td>{{ $child->guardian_name ?? '-' }}</td>
                                <td>{{ $child->phone ?? '-' }}</td>

                                <td class="text-center">
                                    <div class="sc-action-buttons">
                                        <button type="button"
                                                class="btn btn-info btn-sm text-white"
                                                data-bs-toggle="modal"
                                                data-bs-target="#showChildModal{{ $child->id }}">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <button type="button"
                                                class="btn btn-warning btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editChildModal{{ $child->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('scholarship.children.delete', $child->id) }}"
                                              method="POST"
                                              class="d-inline delete-child-form"
                                              data-name="{{ $child->first_name }} {{ $child->last_name }}">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $children->links() }}
            </div>
        @else
            <div class="sc-empty">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                ยังไม่มีข้อมูลผู้ขอรับทุนการศึกษา
            </div>
        @endif

    </div>
</div>

<div class="modal fade" id="createChildModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form action="{{ route('scholarship.children.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="modal-content">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-1"></i> เพิ่มข้อมูลผู้ขอรับทุนการศึกษา
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                @include('landing.scholarship.children.partials.form', [
                    'child' => null,
                    'yearListId' => 'academic_year_create'
                ])
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save"></i> บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</div>

@foreach($children as $child)

    <div class="modal fade" id="showChildModal{{ $child->id }}" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person-vcard me-1"></i> รายละเอียดผู้ขอรับทุน
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-3 text-center">
                            @if($child->photo)
                                <img src="{{ asset('storage/'.$child->photo) }}"
                                     class="img-fluid rounded-4 border"
                                     style="max-height:260px;object-fit:cover;"
                                     alt="photo">
                            @else
                                <div class="sc-avatar mx-auto" style="width:180px;height:180px;font-size:54px;">
                                    {{ mb_substr($child->first_name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="detail-box">
                                        <div class="detail-label">ชื่อ - นามสกุล</div>
                                        <div class="detail-value">{{ $child->first_name }} {{ $child->last_name }}</div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="detail-box">
                                        <div class="detail-label">อายุ</div>
                                        <div class="detail-value">{{ $child->age ?? '-' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="detail-box">
                                        <div class="detail-label">ปีการศึกษา</div>
                                        <div class="detail-value">{{ $child->academic_year }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="detail-box">
                                        <div class="detail-label">ระดับการศึกษา</div>
                                        <div class="detail-value">{{ $child->education_level ?? '-' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="detail-box">
                                        <div class="detail-label">ชื่อสถานศึกษา</div>
                                        <div class="detail-value">{{ $child->school_name ?? '-' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="detail-box">
                                        <div class="detail-label">ชื่อผู้ปกครอง</div>
                                        <div class="detail-value">{{ $child->guardian_name ?? '-' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="detail-box">
                                        <div class="detail-label">เบอร์โทรศัพท์</div>
                                        <div class="detail-value">{{ $child->phone ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="detail-box">
                                <div class="detail-label">ที่อยู่ปัจจุบัน</div>
                                <div class="detail-value">{{ $child->current_address ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="detail-box">
                                <div class="detail-label">สาเหตุที่ขอรับทุน</div>
                                <div class="detail-value">{{ $child->reason ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="detail-box">
                                <div class="detail-label">ความต้องการความช่วยเหลือ</div>
                                <div class="detail-value">{{ $child->help_needed ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="detail-box">
                                <div class="detail-label">รายละเอียดเพิ่มเติม</div>
                                <div class="detail-value">{{ $child->more_detail ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editChildModal{{ $child->id }}" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <form action="{{ route('scholarship.children.update', $child->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="modal-content">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-1"></i> แก้ไขข้อมูลผู้ขอรับทุน
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    @include('landing.scholarship.children.partials.form', [
                        'child' => $child,
                        'yearListId' => 'academic_year_edit_' . $child->id
                    ])
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>

@endforeach

<script>
document.addEventListener('DOMContentLoaded', function () {
    const successAlert = document.getElementById('successAlert');

    if (successAlert) {
        setTimeout(function () {
            successAlert.style.transition = 'opacity .4s ease';
            successAlert.style.opacity = '0';

            setTimeout(function () {
                successAlert.remove();
            }, 400);
        }, 5000);
    }

    document.querySelectorAll('.delete-child-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const childName = form.getAttribute('data-name') || 'รายการนี้';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'ยืนยันการลบข้อมูล?',
                    text: 'ต้องการลบข้อมูลของ ' + childName + ' หรือไม่',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ลบข้อมูล',
                    cancelButtonText: 'ยกเลิก'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('ยืนยันการลบข้อมูลของ ' + childName + ' หรือไม่?')) {
                    form.submit();
                }
            }
        });
    });

    const keywordInput = document.getElementById('keywordInput');
    const childSearchForm = document.getElementById('childSearchForm');

    if (keywordInput && childSearchForm) {
        let delayTimer;

        keywordInput.addEventListener('input', function () {
            clearTimeout(delayTimer);

            delayTimer = setTimeout(function () {
                childSearchForm.submit();
            }, 400);
        });
    }
});
</script>

@endsection