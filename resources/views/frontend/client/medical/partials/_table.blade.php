@php
    $hasDoctorVisit = $medicals->contains(function ($medical) {
        return ($medical->refer ?? '') === 'พบแพทย์';
    });
@endphp

<div class="md-table-card" data-permission-keep>
    <div class="md-table-head">
        <h2 class="md-table-title">
            <i class="bi bi-list-check" aria-hidden="true"></i>
            รายการการรักษาพยาบาล
        </h2>
        <span class="md-table-meta">ทั้งหมด {{ number_format($medicals->count()) }} รายการ</span>
    </div>

    <div class="md-table-body">
        <div class="md-table-wrap">
            <table id="datatable-medical" class="table align-middle w-100 md-table">
                <thead>
                    <tr>
                        <th class="text-center md-col-seq">ลำดับ</th>
                        <th class="md-col-date">วันที่รักษา</th>
                        <th class="md-col-disease">ชื่อโรค</th>
                        <th class="md-col-detail">อาการป่วย</th>
                        <th class="md-col-detail">การรักษา</th>
                        <th class="md-col-refer">การพบแพทย์</th>
                        @if($hasDoctorVisit)
                            <th class="md-col-appt">วันที่แพทย์นัด</th>
                        @endif
                        <th class="text-center md-col-actions">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($medicals as $index => $medical)
                        @php
                            $refer = (string) ($medical->refer ?? '');
                            $isDoctorVisit = $refer === 'พบแพทย์';
                        @endphp
                        <tr>
                            <td class="text-center medical-row-number">{{ $index + 1 }}</td>
                            <td data-order="{{ $medical->medical_date ?: '' }}">
                                <span class="md-date">
                                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                                    {{ $medical->medical_date ? \Carbon\Carbon::parse($medical->medical_date)->format('d/m/Y') : '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="md-cell-text" title="{{ $medical->disease_name }}">
                                    {{ $medical->disease_name ?: '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="md-cell-text md-cell-clamp" title="{{ $medical->illness }}">
                                    {{ $medical->illness ?: '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="md-cell-text md-cell-clamp" title="{{ $medical->treatment }}">
                                    {{ $medical->treatment ?: '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="md-refer">
                                    <i class="bi {{ $isDoctorVisit ? 'bi-check-circle' : 'bi-dash-circle' }}" aria-hidden="true"></i>
                                    {{ $refer !== '' ? $refer : '-' }}
                                </span>
                            </td>
                            @if($hasDoctorVisit)
                                <td data-order="{{ $isDoctorVisit ? ($medical->appt_date ?: '') : '' }}">
                                    <span class="md-date">
                                        @if($isDoctorVisit && filled($medical->appt_date))
                                            <i class="bi bi-calendar3" aria-hidden="true"></i>
                                            {{ \Carbon\Carbon::parse($medical->appt_date)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </td>
                            @endif
                            <td class="text-center">
                                <div class="md-action-group">
                                    @if($canMedicalUpdate)
                                        <button type="button"
                                                class="btn btn-outline-warning md-icon-btn"
                                                onclick="openEditMedical({{ $medical->id }})"
                                                title="แก้ไข"
                                                aria-label="แก้ไขข้อมูล"
                                                data-permission-action="update">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    @else
                                        <button type="button"
                                                class="btn btn-outline-primary md-icon-btn"
                                                onclick="openMedicalReadonly({{ $medical->id }})"
                                                title="ดูข้อมูล (อ่านอย่างเดียว)"
                                                aria-label="ดูข้อมูลแบบอ่านอย่างเดียว"
                                                data-permission-action="view"
                                                data-permission-keep>
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    @endif

                                    @if($canMedicalDelete)
                                        <button type="button"
                                                class="btn btn-outline-danger md-icon-btn"
                                                onclick="confirmMedicalDelete({{ $medical->id }})"
                                                title="ลบ"
                                                aria-label="ลบข้อมูล"
                                                data-permission-action="delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>

                                @if($canMedicalDelete)
                                    <form id="delete-form-medical-{{ $medical->id }}"
                                          action="{{ route('medical.delete', $medical->id) }}"
                                          method="POST"
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
