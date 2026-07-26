@extends('admin_client.admin_client')

@section('content')
    <div class="container-fluid py-4 nutrition-index-page">

        <div class="nutrition-card">

            <div class="nutrition-header">
                <div>
                    <h4 class="mb-1">ประเมินภาวะโภชนาการ</h4>
                    <div class="text-muted small">
                        {{ $client->prefix ?? '' }}{{ $client->first_name ?? '' }}
                        {{ $client->last_name ?? '' }}
                    </div>
                </div>

                <div class="nutrition-header-actions">
                    <a href="{{ route('nutrition_assessments.create', $client->id) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-circle"></i>
                        เพิ่มผลประเมิน
                    </a>


                </div>
            </div>

            <hr>

            @if (session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {

                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ',
                            text: '{{ session('success') }}',
                            timer: 3000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });

                    });
                </script>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle nutrition-table">
                    <thead>
                        <tr>
                            <th style="width:70px;">ลำดับ</th>
                            <th>วันที่ชั่งวัด</th>
                            <th>อายุ</th>
                            <th>เพศ</th>
                            <th>ส่วนสูง</th>
                            <th>น้ำหนัก</th>
                            <th>BMI</th>
                            <th>ผลประเมิน</th>
                            <th>หมายเหตุ</th>
                            <th class="text-center" style="width:140px;">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($assessments as $index => $item)
                            <tr>
                               <td>{{ $assessments->count() - $index }}</td>

                                <td>
                                    {{ $item->assessment_date ? $item->assessment_date->format('d/m/') . ($item->assessment_date->year + 543) : '-' }}
                                </td>

                                <td>
                                    {{ $item->age_year ?? '-' }} ปี
                                    {{ $item->age_month ?? '-' }} เดือน
                                </td>

                                <td>
                                    {{ $item->gender === 'male' ? 'ชาย' : ($item->gender === 'female' ? 'หญิง' : '-') }}
                                </td>

                                <td>
                                    {{ $item->height_cm ? number_format($item->height_cm, 2) : '-' }} ซม.
                                </td>

                                <td>
                                    {{ $item->weight_kg ? number_format($item->weight_kg, 2) : '-' }} กก.
                                </td>

                                <td>
                                    {{ $item->bmi ? number_format($item->bmi, 2) : '-' }}
                                </td>

                                <td>
                                    @php
                                        $status = $item->nutrition_status ?: '-';

                                        $badgeClass = match ($status) {
                                            'สมส่วน' => 'bg-success',
                                            'น้ำหนักน้อย' => 'bg-warning text-dark',
                                            'เริ่มอ้วน' => 'bg-info text-dark',
                                            'อ้วน', 'อ้วนมาก' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp

                                    <span class="badge {{ $badgeClass }}">
                                        {{ $status }}
                                    </span>
                                </td>

                                <td>
                                    {{ $item->note ?: '-' }}
                                </td>

                                <td class="text-center">

                                    <div class="action-buttons">

                                        <a href="{{ route('nutrition_assessments.show', [$client->id, $item->id]) }}"
                                            class="btn btn-action btn-view">
                                            <i class="bi bi-eye"></i>
                                            <span>ดูผล</span>
                                        </a>

                                        <a href="{{ route('nutrition_assessments.edit', [$client->id, $item->id]) }}"
                                            class="btn btn-action btn-edit">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>แก้ไข</span>
                                        </a>

                                        @if (auth()->user()->role === 'admin')
                                            <form
                                                action="{{ route('nutrition_assessments.destroy', [$client->id, $item->id]) }}"
                                                method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')

                                                <button type="button" class="btn btn-action btn-delete">
                                                    <i class="bi bi-trash"></i>
                                                    <span>ลบ</span>
                                                </button>
                                            </form>
                                        @endif

                                    </div>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    ยังไม่มีข้อมูลการประเมินภาวะโภชนาการ
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

    </div>

    <style>
        .nutrition-index-page {
            background: #f6f8fb;
        }

        .nutrition-card {
            background: #fff;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 8px 25px rgba(15, 23, 42, .06);
        }

        .nutrition-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .nutrition-header h4 {
            font-weight: 700;
            color: #1e293b;
        }

        .nutrition-header-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .nutrition-table thead th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: .9rem;
            white-space: nowrap;
        }

        .nutrition-table td {
            font-size: .9rem;
            white-space: nowrap;
        }

        .nutrition-table .badge {
            font-size: .78rem;
            padding: .45em .65em;
            border-radius: 999px;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            flex-wrap: nowrap;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 10px;
            padding: 6px 12px;
            font-size: .82rem;
            font-weight: 600;
            transition: all .2s ease;
            border-width: 1px;
        }

        .btn-action i {
            font-size: .85rem;
        }

        .btn-view {
            color: #2563eb;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
        }

        .btn-view:hover {
            background: #2563eb;
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-edit {
            color: #b45309;
            border: 1px solid #fde68a;
            background: #fffbeb;
        }

        .btn-edit:hover {
            background: #f59e0b;
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-delete {
            color: #dc2626;
            border: 1px solid #fecaca;
            background: #fef2f2;
        }

        .btn-delete:hover {
            background: #dc2626;
            color: #fff;
            transform: translateY(-1px);
        }

        @media(max-width:768px) {

            .action-buttons {
                min-width: 240px;
            }

            .btn-action {
                padding: 5px 10px;
                font-size: .78rem;
            }

        }

        @media(max-width:768px) {
            .nutrition-card {
                padding: 18px;
            }

            .nutrition-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .nutrition-header-actions {
                width: 100%;
            }

            .nutrition-header-actions .btn {
                flex: 1;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.btn-delete').forEach(button => {

                button.addEventListener('click', function() {

                    const form = this.closest('.delete-form');

                    Swal.fire({
                        title: 'ยืนยันการลบข้อมูล',
                        text: 'คุณต้องการลบผลประเมินภาวะโภชนาการนี้ใช่หรือไม่ ?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'ใช่, ลบข้อมูล',
                        cancelButtonText: 'ยกเลิก',
                        reverseButtons: true
                    }).then((result) => {

                        if (result.isConfirmed) {
                            form.submit();
                        }

                    });

                });

            });

        });
    </script>
@endsection
