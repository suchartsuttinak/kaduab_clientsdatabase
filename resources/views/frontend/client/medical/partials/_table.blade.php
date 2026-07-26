<div class="card border-0 shadow-sm medical-table-card">
    <div class="card-header bg-white border-0 px-3 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h6 class="mb-0 fw-bold">
                <i class="bi bi-clipboard2-pulse me-2 text-primary"></i>รายการข้อมูลการรักษาพยาบาล
            </h6>
        </div>

        <div class="small text-muted">
            จำนวน {{ $medicals->count() }} รายการ
        </div>
    </div>

    <div class="card-body p-0">
        @if($medicals->isNotEmpty())
            @php
                $hasDoctorVisit = $medicals->contains(function ($medical) {
                    return ($medical->refer ?? '') === 'พบแพทย์';
                });
            @endphp

            <div class="table-responsive medical-table-wrapper">
                <table id="datatable-medical" class="table table-hover align-middle mb-0 medical-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:70px;">ลำดับ</th>
                            <th style="min-width:120px;">วันที่</th>
                            <th style="min-width:160px;">ชื่อโรค</th>
                            <th style="min-width:180px;">อาการป่วย</th>
                            <th style="min-width:180px;">การรักษา</th>
                            <th style="min-width:120px;">การพบแพทย์</th>

                            @if($hasDoctorVisit)
                                <th style="min-width:130px;">วันที่แพทย์นัด</th>
                            @endif

                            <th class="text-center" style="min-width:220px;">จัดการ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($medicals as $index => $medical)
                            @php
                                $isDoctorVisit = ($medical->refer ?? '') === 'พบแพทย์';
                            @endphp

                            <tr>
                                <td class="text-center fw-semibold">
                                    {{ $index + 1 }}
                                </td>

                                <td>
                                    {{ $medical->medical_date ? \Carbon\Carbon::parse($medical->medical_date)->format('d/m/Y') : '-' }}
                                </td>

                                <td>{{ $medical->disease_name ?: '-' }}</td>

                                <td>{{ $medical->illness ?: '-' }}</td>

                                <td>{{ $medical->treatment ?: '-' }}</td>

                                <td>
                                    @if($isDoctorVisit)
                                        <span class="badge rounded-pill bg-success-subtle text-success-emphasis px-3 py-2">
                                            พบแพทย์
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis px-3 py-2">
                                            ไม่พบแพทย์
                                        </span>
                                    @endif
                                </td>

                                @if($hasDoctorVisit)
                                    <td>
                                        @if($isDoctorVisit && !empty($medical->appt_date))
                                            {{ \Carbon\Carbon::parse($medical->appt_date)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endif

                                <td class="text-center">
                                    <div class="action-group">
                                        <button type="button"
                                                class="btn btn-warning btn-sm action-btn"
                                                onclick="openEditMedical({{ $medical->id }})">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>แก้ไข</span>
                                        </button>

                                        <button type="button"
                                                class="btn btn-danger btn-sm action-btn"
                                                onclick="confirmDelete('delete-form-medical-{{ $medical->id }}', 'คุณต้องการลบข้อมูลทางการแพทย์นี้ใช่หรือไม่')">
                                            <i class="bi bi-trash"></i>
                                            <span>ลบ</span>
                                        </button>

                                        {{-- <a href="{{ route('medical.report', $medical->id) }}"
                                           class="btn btn-info btn-sm action-btn text-white">
                                            <i class="bi bi-file-earmark-text"></i>
                                            <span>รายงาน</span>
                                        </a> --}}
                                    </div>

                                    <form id="delete-form-medical-{{ $medical->id }}"
                                          action="{{ route('medical.delete', $medical->id) }}"
                                          method="POST"
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-muted">
                ไม่มีข้อมูลการรักษาพยาบาล
            </div>
        @endif
    </div>
</div>