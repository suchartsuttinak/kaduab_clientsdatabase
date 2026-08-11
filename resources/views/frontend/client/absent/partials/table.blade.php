<div class="ab-table-card" data-permission-keep>
    <div class="ab-table-head">
        <h2 class="ab-table-title"><i class="bi bi-list-check" aria-hidden="true"></i>รายการบันทึกการขาดเรียน</h2>
        <span class="ab-table-meta">ทั้งหมด {{ $absents->count() }} รายการ</span>
    </div>

    <div class="ab-table-body">
        @if($absents->isNotEmpty())
            <div class="ab-table-wrap">
                <x-stable-table-controls target="datatable-absent" />
                <table id="datatable-absent" class="table modern-table align-middle w-100 ab-table" data-stable-table data-page-length="10">
                    <thead>
                        <tr>
                            <th>วันที่ขาดเรียน</th>
                            <th>วันที่บันทึก</th>
                            <th>สาเหตุ</th>
                            <th>ผู้ดูแลเด็ก</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($absents as $item)
                            <tr>
                                <td>
                                    <span class="ab-date">
                                        <i class="bi bi-calendar3" aria-hidden="true"></i>
                                        {{ \Carbon\Carbon::parse($item->absent_date)->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="ab-date">
                                        <i class="bi bi-calendar-check" aria-hidden="true"></i>
                                        {{ !empty($item->record_date) ? \Carbon\Carbon::parse($item->record_date)->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                                <td title="{{ $item->cause }}">
                                    <div class="ab-text-wrap">{{ \Illuminate\Support\Str::limit($item->cause ?? '-', 80) }}</div>
                                </td>
                                <td>{{ $item->teacher ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="ab-action-group">
                                        @if($canAbsentUpdate)
                                            <button type="button"
                                                    class="btn btn-outline-warning ab-icon-btn"
                                                    onclick="openEditAbsent({{ $item->id }})"
                                                    title="แก้ไข"
                                                    aria-label="แก้ไขข้อมูล"
                                                    data-permission-action="update">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        @else
                                            <button type="button"
                                                    class="btn btn-outline-primary ab-icon-btn"
                                                    onclick="openAbsentReadonly({{ $item->id }})"
                                                    title="ดูข้อมูล (อ่านอย่างเดียว)"
                                                    aria-label="ดูข้อมูลแบบอ่านอย่างเดียว"
                                                    data-permission-action="view"
                                                    data-permission-keep>
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        @endif

                                        @if($canAbsentDelete)
                                            <button type="button"
                                                    class="btn btn-outline-danger ab-icon-btn"
                                                    onclick="confirmDelete({{ $item->id }})"
                                                    title="ลบ"
                                                    aria-label="ลบข้อมูล"
                                                    data-permission-action="delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif

                                        @if($canAbsentPrint)
                                            <a href="{{ route('absent.report', $item->id) }}"
                                               class="btn btn-outline-primary ab-icon-btn"
                                               title="รายงาน"
                                               aria-label="เปิดรายงาน"
                                               data-permission-action="print">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </a>
                                        @endif
                                    </div>

                                    @if($canAbsentDelete)
                                        <form id="delete-form-{{ $item->id }}"
                                              action="{{ route('absent.delete', $item->id) }}"
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
                <x-stable-table-footer target="datatable-absent" :total="$absents->count()" />
            </div>
        @else
            <div class="ab-empty">
                <div class="ab-empty-icon"><i class="bi bi-calendar-x"></i></div>
                <h3 class="ab-empty-title">ยังไม่มีข้อมูลการขาดเรียน</h3>
                <p class="ab-empty-text">เพิ่มข้อมูลครั้งแรกเพื่อเริ่มบันทึกและติดตามการขาดเรียน</p>
                @if($canAbsentCreate)
                    <button type="button"
                            class="btn btn-primary ab-btn"
                            id="btn-open-absent-modal"
                            data-bs-toggle="modal"
                            data-bs-target="#absentModal"
                            data-permission-action="create">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มข้อมูล</span>
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
