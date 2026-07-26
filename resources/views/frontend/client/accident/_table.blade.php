<div class="table-card card">
    <div class="card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2">
        <div>
            <h5 class="section-title">
                <i class="bi bi-table me-2 text-primary"></i>รายการบันทึกการบาดเจ็บ
            </h5>
            <p class="section-subtitle">
                แสดงรายการล่าสุดก่อน พร้อมปุ่มแก้ไข ลบ และพิมพ์รายงาน
            </p>
        </div>
        <div>
            <span class="badge text-bg-light border">ทั้งหมด {{ $accidents->count() }} รายการ</span>
        </div>
    </div>

 <div class="card-body">
    @if($accidents->isNotEmpty())
        @php
            $hasDoctorVisit = $accidents->contains(function ($row) {
                return ($row->treat_no ?? '') === 'พบแพทย์';
            });
        @endphp

        <div class="table-responsive">
            <table id="datatable-accident" class="table table-hover align-middle w-100 mb-0">
                <thead>
                    <tr>
                        <th style="width: 90px;">วันที่เกิดเหตุ</th>
                        <th>สถานที่</th>
                        <th>รายละเอียด</th>
                        <th>สาเหตุ</th>
                        <th class="text-center">พบแพทย์</th>

                        @if($hasDoctorVisit)
                            <th>สถานพยาบาล</th>
                            <th>นัดครั้งต่อไป</th>
                        @endif

                        <th>ผู้ดูแล</th>
                        <th class="text-center" style="width: 150px;">จัดการ</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($accidents as $row)
                        @php
                            $isDoctorVisit = ($row->treat_no ?? '') === 'พบแพทย์';
                        @endphp

                        <tr>
                            <td>
                                {{ \App\Helpers\ThaiDateHelper::formatThaiShort($row->incident_date) }}
                            </td>

                            <td>
                                <div class="fw-semibold text-dark">
                                    {{ $row->location ?? '-' }}
                                </div>
                            </td>

                            <td>
                                @if(!empty($row->detail))
                                    <div style="min-width: 220px;">
                                        {{ \Illuminate\Support\Str::limit($row->detail, 80) }}
                                    </div>
                                @else
                                    -
                                @endif
                            </td>

                            <td>{{ $row->cause ?? '-' }}</td>

                            <td class="text-center">
                                @if($isDoctorVisit)
                                    <span class="badge rounded-pill badge-soft-success">
                                        พบแพทย์
                                    </span>
                                @else
                                    <span class="badge rounded-pill badge-soft-secondary">
                                        {{ $row->treat_no ?: 'ไม่พบแพทย์' }}
                                    </span>
                                @endif
                            </td>

                            @if($hasDoctorVisit)
                                <td>
                                    @if($isDoctorVisit)
                                        {{ $row->hospital ?: '-' }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @if($isDoctorVisit && !empty($row->appointment))
                                        {{ \Carbon\Carbon::parse($row->appointment)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endif

                            <td>{{ $row->caretaker ?? '-' }}</td>

                            <td class="text-center">
                                <div class="d-inline-flex flex-nowrap gap-1">
                                    <a href="{{ route('accident.edit', $row->id) }}"
                                       class="btn btn-warning btn-icon"
                                       title="แก้ไข">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <button type="button"
                                            class="btn btn-danger btn-icon"
                                            onclick="confirmDelete({{ $row->id }})"
                                            title="ลบ">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <a href="{{ route('accident.report', $row->id) }}"
                                       class="btn btn-info btn-icon text-white"
                                       title="พิมพ์รายงาน">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>

                                <form id="delete-form-{{ $row->id }}"
                                      action="{{ route('accident.delete', $row->id) }}"
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
        <div class="empty-state">
            <div class="empty-icon">
                <i class="bi bi-clipboard2-pulse"></i>
            </div>

            <h6 class="fw-bold mb-2">ยังไม่มีข้อมูลการบาดเจ็บ</h6>

            <p class="text-muted mb-3">
                เริ่มต้นโดยกดปุ่ม “เพิ่มข้อมูลการบาดเจ็บ” ด้านบนเพื่อบันทึกรายการแรก
            </p>

            <button
                type="button"
                class="btn btn-primary btn-modern"
                data-bs-toggle="collapse"
                data-bs-target="#accidentFormCollapse"
                aria-expanded="false"
                aria-controls="accidentFormCollapse"
            >
                <i class="bi bi-plus-circle me-1"></i>เพิ่มข้อมูลการบาดเจ็บ
            </button>
        </div>
    @endif
</div>
</div>