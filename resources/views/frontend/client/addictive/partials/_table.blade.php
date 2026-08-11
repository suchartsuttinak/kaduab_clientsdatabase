<div class="ad-table-card" data-permission-keep>
    <div class="ad-table-head">
        <div class="ad-table-head-main">
            <span class="ad-table-icon" aria-hidden="true">
                <i class="bi bi-list-check"></i>
            </span>
            <h2 class="ad-table-title">รายการตรวจสารเสพติด</h2>
        </div>
        <span class="ad-table-meta">ทั้งหมด {{ $addictives->count() }} รายการ</span>
    </div>

    <div class="ad-table-body">
        @if($addictives->isNotEmpty())
            <div class="ad-table-wrap" data-permission-keep>
                <x-stable-table-controls target="datatable-addictive" />
                <table id="datatable-addictive" class="table align-middle w-100 ad-table" data-permission-keep data-stable-table data-page-length="10">
                    <thead>
                        <tr>
                            <th>วันที่ตรวจ</th>
                            <th class="text-center">ครั้งที่</th>
                            <th>ผลการตรวจ</th>
                            <th>การดำเนินการต่อ</th>
                            <th>บันทึกผล</th>
                            <th>ผู้ตรวจ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($addictives as $addictive)
                            <tr id="row-addictive-{{ $addictive->id }}">
                                <td data-order="{{ $addictive->date }}">
                                    <span class="ad-date">
                                        <i class="bi bi-calendar3"></i>
                                        {{ $addictive->date
                                            ? \Carbon\Carbon::parse($addictive->date)->format('d/m/') . (\Carbon\Carbon::parse($addictive->date)->year + 543)
                                            : '-' }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $addictive->count ?? '-' }}</td>
                                <td>
                                    @if((int) $addictive->exam === 0)
                                        <span class="ad-status ad-status--negative">
                                            <i class="bi bi-check-circle"></i>ไม่พบสารเสพติด
                                        </span>
                                    @else
                                        <span class="ad-status ad-status--positive">
                                            <i class="bi bi-exclamation-circle"></i>พบสารเสพติด
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if((int) $addictive->exam === 1 && (int) $addictive->refer === 1)
                                        <span class="ad-status ad-status--refer">
                                            <i class="bi bi-hospital"></i>ส่งต่อบำบัด
                                        </span>
                                    @elseif((int) $addictive->exam === 1 && (int) $addictive->refer === 2)
                                        <span class="ad-status ad-status--follow">
                                            <i class="bi bi-shield-check"></i>ติดตามดูแลต่อเนื่อง
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td><div class="ad-note" title="{{ $addictive->record ?: '-' }}">{{ $addictive->record ?: '-' }}</div></td>
                                <td>{{ $addictive->recorder ?: '-' }}</td>
                                <td class="text-center">
                                    <div class="ad-action-group">
                                        @if($canAddictiveUpdate)
                                            <button type="button"
                                                    class="btn btn-outline-warning ad-icon-btn"
                                                    onclick="openEditAddictive({{ $addictive->id }})"
                                                    title="แก้ไข"
                                                    aria-label="แก้ไขข้อมูล"
                                                    data-permission-action="update">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        @else
                                            <button type="button"
                                                    class="btn btn-outline-primary ad-icon-btn"
                                                    onclick="openAddictiveReadonly({{ $addictive->id }})"
                                                    title="ดูข้อมูล (อ่านอย่างเดียว)"
                                                    aria-label="ดูข้อมูลแบบอ่านอย่างเดียว"
                                                    data-permission-action="view"
                                                    data-permission-keep>
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        @endif

                                        @if($canAddictiveDelete)
                                            <button type="button"
                                                    class="btn btn-outline-danger ad-icon-btn"
                                                    onclick="confirmDelete('delete-form-addictive-{{ $addictive->id }}', 'คุณต้องการลบข้อมูลการตรวจสารเสพติดนี้ใช่หรือไม่')"
                                                    title="ลบ"
                                                    aria-label="ลบข้อมูล"
                                                    data-permission-action="delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif

                                        @if($canAddictivePrint)
                                            <a href="{{ route('addictive.report.all', [
                                                    'client_id' => $client->id,
                                                    'date_from' => $addictive->date,
                                                    'date_to' => $addictive->date,
                                                ]) }}"
                                               class="btn btn-outline-primary ad-icon-btn"
                                               title="รายงาน"
                                               aria-label="เปิดรายงาน"
                                               data-permission-action="print">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </a>
                                        @endif
                                    </div>

                                    @if($canAddictiveDelete)
                                        <form id="delete-form-addictive-{{ $addictive->id }}"
                                              action="{{ route('addictive.delete', $addictive->id) }}"
                                              method="POST"
                                              class="d-none"
                                              data-permission-action="delete">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <x-stable-table-footer target="datatable-addictive" :total="$addictives->count()" />
            </div>
        @else
            <div class="ad-empty">
                <span class="ad-empty-icon" aria-hidden="true">
                    <i class="bi bi-clipboard2-pulse"></i>
                </span>
                <h3 class="ad-empty-title">ยังไม่มีข้อมูลการตรวจสารเสพติด</h3>
                <p class="ad-empty-text">
                    {{ $canAddictiveCreate
                        ? 'เพิ่มข้อมูลครั้งแรกเพื่อเริ่มบันทึกและติดตามผลการตรวจ'
                        : 'ยังไม่มีรายการที่สามารถแสดงได้ในขณะนี้' }}
                </p>

                @if($canAddictiveCreate)
                    <button type="button"
                            class="btn btn-primary ad-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#createAddictiveModal"
                            data-permission-action="create">
                        <i class="bi bi-plus-circle"></i>
                        <span>เพิ่มข้อมูล</span>
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
