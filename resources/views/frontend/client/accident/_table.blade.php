<section class="acc-table-card" aria-labelledby="accidentTableTitle">
    <div class="acc-card-header">
        <div class="acc-card-heading">
            <div class="acc-card-heading-icon" aria-hidden="true">
                <i class="bi bi-table"></i>
            </div>

            <div>
                <h2 class="acc-card-title" id="accidentTableTitle">
                    รายการบันทึกการบาดเจ็บ
                </h2>
                <p class="acc-card-subtitle">
                    แสดงรายการล่าสุดก่อน พร้อมคำสั่งแก้ไข ลบ และพิมพ์รายงาน
                </p>
            </div>
        </div>

        <span class="acc-count-badge">
            <i class="bi bi-list-check" aria-hidden="true"></i>
            ทั้งหมด {{ number_format($accidents->count()) }} รายการ
        </span>
    </div>

    <div class="acc-card-body">
        @php
            $hasDoctorVisit = $accidents->contains(function ($row) {
                return ($row->treat_no ?? '') === 'พบแพทย์';
            });
        @endphp

        <div class="acc-table-wrap">
            <table id="datatable-accident"
                   class="table table-hover align-middle acc-table {{ $hasDoctorVisit ? 'acc-table-expanded' : 'acc-table-compact' }}">

                <thead>
                    <tr>
                        <th class="acc-col-date">วันที่เกิดเหตุ</th>
                        <th class="acc-col-location">สถานที่</th>
                        <th class="acc-col-detail">รายละเอียด</th>
                        <th class="acc-col-cause">สาเหตุ</th>
                        <th class="acc-col-treatment text-center">การพบแพทย์</th>

                        @if($hasDoctorVisit)
                            <th class="acc-col-hospital">สถานพยาบาล</th>
                            <th class="acc-col-appointment">นัดครั้งต่อไป</th>
                        @endif

                        <th class="acc-col-caretaker">ผู้ดูแล</th>
                        <th class="acc-col-actions text-center"
                            data-orderable="false"
                            data-searchable="false">
                            จัดการ
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($accidents as $row)
                        @php
                            $isDoctorVisit = ($row->treat_no ?? '') === 'พบแพทย์';
                            $incidentSort = !empty($row->incident_date)
                                ? \Carbon\Carbon::parse($row->incident_date)->format('Ymd')
                                : '00000000';
                        @endphp

                        <tr>
                            <td class="acc-col-date" data-order="{{ $incidentSort }}">
                                <span class="acc-cell-main text-nowrap">
                                    {{ \App\Helpers\ThaiDateHelper::formatThaiShort($row->incident_date) }}
                                </span>
                            </td>

                            <td class="acc-col-location">
                                <span class="acc-cell-main acc-text-block"
                                      title="{{ $row->location ?: '-' }}">
                                    {{ $row->location ?: '-' }}
                                </span>
                            </td>

                            <td class="acc-col-detail">
                                <span class="acc-text-block acc-text-clamp"
                                      title="{{ $row->detail ?: '-' }}">
                                    {{ $row->detail ?: '-' }}
                                </span>
                            </td>

                            <td class="acc-col-cause">
                                <span class="acc-text-block acc-text-clamp"
                                      title="{{ $row->cause ?: '-' }}">
                                    {{ $row->cause ?: '-' }}
                                </span>
                            </td>

                            <td class="acc-col-treatment text-center">
                                @if($isDoctorVisit)
                                    <span class="acc-status acc-status-doctor">
                                        <i class="bi bi-hospital" aria-hidden="true"></i>
                                        พบแพทย์
                                    </span>
                                @elseif(($row->treat_no ?? '') === 'ไม่พบแพทย์')
                                    <span class="acc-status acc-status-home">
                                        <i class="bi bi-house-heart" aria-hidden="true"></i>
                                        ไม่พบแพทย์
                                    </span>
                                @else
                                    <span class="acc-status acc-status-home">
                                        <i class="bi bi-question-circle" aria-hidden="true"></i>
                                        ไม่ระบุ
                                    </span>
                                @endif
                            </td>

                            @if($hasDoctorVisit)
                                <td class="acc-col-hospital">
                                    <span class="acc-text-block acc-text-clamp"
                                          title="{{ $isDoctorVisit ? ($row->hospital ?: '-') : '-' }}">
                                        {{ $isDoctorVisit ? ($row->hospital ?: '-') : '-' }}
                                    </span>
                                </td>

                                <td class="acc-col-appointment">
                                    @if($isDoctorVisit && !empty($row->appointment))
                                        <span class="acc-cell-main text-nowrap">
                                            {{ \App\Helpers\ThaiDateHelper::formatThaiShort($row->appointment) }}
                                        </span>
                                    @else
                                        <span class="acc-cell-muted">-</span>
                                    @endif
                                </td>
                            @endif

                            <td class="acc-col-caretaker">
                                <span class="acc-text-block acc-text-clamp"
                                      title="{{ $row->caretaker ?: '-' }}">
                                    {{ $row->caretaker ?: '-' }}
                                </span>
                            </td>

                            <td class="acc-col-actions text-center">
                                <div class="acc-row-actions" role="group" aria-label="จัดการรายการ">
                                    <a href="{{ route('accident.edit', $row->id) }}"
                                       class="btn btn-warning acc-icon-btn"
                                       title="แก้ไขข้อมูล"
                                       aria-label="แก้ไขข้อมูลวันที่ {{ \App\Helpers\ThaiDateHelper::formatThaiShort($row->incident_date) }}">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </a>

                                    <button type="button"
                                            class="btn btn-danger acc-icon-btn"
                                            onclick="confirmDelete({{ $row->id }})"
                                            title="ลบข้อมูล"
                                            aria-label="ลบข้อมูลวันที่ {{ \App\Helpers\ThaiDateHelper::formatThaiShort($row->incident_date) }}">
                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                    </button>

                                    <a href="{{ route('accident.report', $row->id) }}"
                                       class="btn btn-info text-white acc-icon-btn"
                                       title="พิมพ์รายงาน"
                                       aria-label="พิมพ์รายงานวันที่ {{ \App\Helpers\ThaiDateHelper::formatThaiShort($row->incident_date) }}">
                                        <i class="bi bi-printer" aria-hidden="true"></i>
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
    </div>
</section>