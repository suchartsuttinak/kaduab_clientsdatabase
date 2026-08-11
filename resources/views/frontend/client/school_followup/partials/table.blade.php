<div class="sf-table-card" data-permission-keep>
    <div class="sf-table-head">
        <h2 class="sf-table-title"><i class="bi bi-list-check" aria-hidden="true"></i>รายการติดตามผลการเรียน</h2>
        <span class="sf-table-meta">ทั้งหมด {{ $followups->count() }} รายการ</span>
    </div>

    <div class="sf-table-body">
        @if($followups->isNotEmpty())
            <div class="sf-table-wrap">
                <x-stable-table-controls target="datatable-followup" />
                <table id="datatable-followup" class="table align-middle w-100 sf-table" data-stable-table data-page-length="10">
                    <thead>
                        <tr>
                            <th>วันที่ติดตาม</th>
                            <th>สถานศึกษา</th>
                            <th>ระดับชั้น</th>
                            <th>ครูประจำชั้น</th>
                            <th>โทรศัพท์</th>
                            <th>การดำเนินงาน</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($followups as $item)
                            @php
                                $followType = (string) ($item->follow_type ?? '');
                                $followTypeLabel = match ($followType) {
                                    'self' => 'ติดตามด้วยตนเอง',
                                    'phone' => 'โทรศัพท์',
                                    'other' => 'อื่น ๆ',
                                    default => '-',
                                };
                                $followTypeIcon = match ($followType) {
                                    'self' => 'bi-person-walking',
                                    'phone' => 'bi-telephone',
                                    'other' => 'bi-three-dots',
                                    default => 'bi-dash',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <span class="sf-date">
                                        <i class="bi bi-calendar3" aria-hidden="true"></i>
                                        {{ Carbon\Carbon::parse($item->follow_date)->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td>{{ optional($item->educationRecord)->school_name ?? 'ไม่พบข้อมูล' }}</td>
                                <td>{{ optional(optional($item->educationRecord)->education)->education_name ?? 'ไม่พบข้อมูล' }}</td>
                                <td>{{ $item->teacher_name ?? '-' }}</td>
                                <td>{{ $item->tel ?? '-' }}</td>
                                <td>
                                    <span class="sf-operation">
                                        <i class="bi {{ $followTypeIcon }}" aria-hidden="true"></i>
                                        {{ $followTypeLabel }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="sf-action-group">
                                        @if($canSchoolUpdate)
                                            <button type="button"
                                                    class="btn btn-outline-warning sf-icon-btn"
                                                    onclick="openEditFollowup({{ $item->id }})"
                                                    title="แก้ไข"
                                                    aria-label="แก้ไขข้อมูล"
                                                    data-permission-action="update">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        @else
                                            <button type="button"
                                                    class="btn btn-outline-primary sf-icon-btn"
                                                    onclick="openSchoolFollowupReadonly({{ $item->id }})"
                                                    title="ดูข้อมูล (อ่านอย่างเดียว)"
                                                    aria-label="ดูข้อมูลแบบอ่านอย่างเดียว"
                                                    data-permission-action="view"
                                                    data-permission-keep>
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        @endif

                                        @if($canSchoolDelete)
                                            <button type="button"
                                                    class="btn btn-outline-danger sf-icon-btn"
                                                    onclick="confirmDelete({{ $item->id }})"
                                                    title="ลบ"
                                                    aria-label="ลบข้อมูล"
                                                    data-permission-action="delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif

                                        @if($canSchoolPrint)
                                            <a href="{{ route('school_followup.report', $item->id) }}"
                                               class="btn btn-outline-primary sf-icon-btn"
                                               title="รายงาน"
                                               aria-label="เปิดรายงาน"
                                               data-permission-action="print">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </a>
                                        @endif
                                    </div>

                                    @if($canSchoolDelete)
                                        <form id="delete-form-{{ $item->id }}"
                                              action="{{ route('school_followup.delete', $item->id) }}"
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
                <x-stable-table-footer target="datatable-followup" :total="$followups->count()" />
            </div>
        @else
            <div class="sf-empty">
                <div class="sf-empty-icon"><i class="bi bi-journal-x"></i></div>
                <h3 class="sf-empty-title">ยังไม่มีข้อมูลติดตามผลการเรียน</h3>
                <p class="sf-empty-text">เพิ่มข้อมูลครั้งแรกเพื่อเริ่มบันทึกและติดตามผลการเรียน</p>
                @if($canSchoolCreate)
                    <button type="button"
                            class="btn btn-primary sf-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#followupModal"
                            data-permission-action="create">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มข้อมูล</span>
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
