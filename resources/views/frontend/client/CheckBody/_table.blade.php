@php
    use App\Helpers\ThaiDateHelper;
@endphp

<section class="cb-table-card" aria-labelledby="checkBodyTableTitle">
    <div class="cb-card-header">
        <div class="cb-card-heading">
            <div class="cb-card-heading-icon" aria-hidden="true">
                <i class="bi bi-table"></i>
            </div>

            <div>
                <h2 class="cb-card-title" id="checkBodyTableTitle">
                    รายการตรวจสุขภาพเบื้องต้น
                </h2>
                <p class="cb-card-subtitle">
                    แสดงรายการล่าสุดก่อน พร้อมคำสั่งแก้ไข ลบ และพิมพ์รายงาน
                </p>
            </div>
        </div>

        <span class="cb-count-badge">
            <i class="bi bi-list-check"></i>
            ทั้งหมด {{ number_format($checkbodies->count()) }} รายการ
        </span>
    </div>

    <div class="cb-card-body">
        <div class="cb-table-wrap">
            <table id="datatable-checkbody"
                   class="table table-hover align-middle cb-table w-100">
                <thead>
                    <tr>
                        <th class="cb-col-date">วันที่ตรวจ</th>
                        <th class="cb-col-development text-center">พัฒนาการ</th>
                        <th class="cb-col-support">การส่งเสริม</th>
                        <th class="cb-col-metric">น้ำหนัก / ส่วนสูง</th>
                        <th class="cb-col-health">สุขภาพโดยรวม</th>
                        <th class="cb-col-recorder">ผู้ตรวจ</th>
                        <th class="cb-col-remark">หมายเหตุ</th>
                        <th class="cb-col-actions text-center">จัดการ</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($checkbodies as $row)
                        @php
                            $isNormalDevelopment = ($row->development ?? '') === 'สมวัย';
                            $isSpecialGroup = ($row->development_type ?? '') === 'เด็กกลุ่มพิเศษ';
                            $assessorDateSort = !empty($row->assessor_date)
                                ? \Carbon\Carbon::parse($row->assessor_date)->format('Ymd')
                                : '00000000';

                            $healthSummary = collect([
                                $row->health ?? null,
                                !empty($row->disease) ? 'โรคประจำตัว: ' . $row->disease : null,
                                !empty($row->oral) ? 'ช่องปาก: ' . $row->oral : null,
                            ])->filter()->implode(' | ');
                        @endphp

                        <tr>
                            <td class="cb-col-date" data-order="{{ $assessorDateSort }}">
                                <span class="cb-cell-main">
                                    {{ ThaiDateHelper::formatThaiShort($row->assessor_date) }}
                                </span>
                            </td>

                            <td class="cb-col-development text-center">
                                @if($isNormalDevelopment)
                                    <span class="cb-status cb-status-normal">
                                        <i class="bi bi-emoji-smile"></i>
                                        สมวัย
                                    </span>
                                @else
                                    <span class="cb-status cb-status-delayed">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        ไม่สมวัย
                                    </span>
                                @endif
                            </td>

                            <td class="cb-col-support">
                                <span class="cb-status {{ $isSpecialGroup ? 'cb-status-special' : 'cb-status-general' }}">
                                    <i class="bi {{ $isSpecialGroup ? 'bi-stars' : 'bi-person' }}"></i>
                                    {{ $row->development_type ?: 'ไม่ระบุ' }}
                                </span>

                                @if($isSpecialGroup && !empty($row->special_support_type))
                                    <span class="cb-text-block cb-text-clamp cb-cell-muted mt-1"
                                          title="{{ $row->special_support_type }}">
                                        {{ $row->special_support_type }}
                                    </span>
                                @endif
                            </td>

                            <td class="cb-col-metric">
                                <span class="cb-text-block">
                                    <strong>นน.</strong>
                                    {{ ($row->weight !== null && $row->weight !== '') ? $row->weight . ' กก.' : '-' }}
                                </span>
                                <span class="cb-text-block">
                                    <strong>สส.</strong>
                                    {{ ($row->height !== null && $row->height !== '') ? $row->height . ' ซม.' : '-' }}
                                </span>
                            </td>

                            <td class="cb-col-health">
                                @if($healthSummary !== '')
                                    <span class="cb-text-block cb-text-clamp" title="{{ $healthSummary }}">
                                        {{ $healthSummary }}
                                    </span>
                                @else
                                    <span class="cb-cell-muted">-</span>
                                @endif
                            </td>

                            <td class="cb-col-recorder">
                                <span class="cb-text-block cb-text-clamp" title="{{ $row->recorder ?: '-' }}">
                                    {{ $row->recorder ?: '-' }}
                                </span>
                            </td>

                            <td class="cb-col-remark">
                                @if(!empty($row->remark))
                                    <span class="cb-text-block cb-text-clamp" title="{{ $row->remark }}">
                                        {{ $row->remark }}
                                    </span>
                                @else
                                    <span class="cb-cell-muted">-</span>
                                @endif
                            </td>

                            <td class="cb-col-actions text-center">
                                <div class="cb-row-actions">
                                    <a href="{{ route('check_body.edit', $row->id) }}"
                                       class="btn btn-warning cb-icon-btn"
                                       title="แก้ไขข้อมูล"
                                       aria-label="แก้ไขข้อมูลวันที่ {{ ThaiDateHelper::formatThaiShort($row->assessor_date) }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <button type="button"
                                            class="btn btn-danger cb-icon-btn"
                                            onclick="confirmDelete({{ $row->id }})"
                                            title="ลบข้อมูล"
                                            aria-label="ลบข้อมูลวันที่ {{ ThaiDateHelper::formatThaiShort($row->assessor_date) }}">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <a href="{{ route('check_body.report', $row->id) }}"
                                       class="btn btn-info text-white cb-icon-btn"
                                       title="พิมพ์รายงาน"
                                       aria-label="พิมพ์รายงานวันที่ {{ ThaiDateHelper::formatThaiShort($row->assessor_date) }}">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>

                                <form id="delete-form-{{ $row->id }}"
                                      action="{{ route('check_body.delete', $row->id) }}"
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