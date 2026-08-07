<section class="co-table-card" aria-labelledby="caseOutsideTableTitle">
    <div class="co-table-head">
        <div class="co-table-title" id="caseOutsideTableTitle">
            <i class="bi bi-table"></i>
            <span>รายการติดตาม</span>
        </div>

        <div class="co-table-meta">
            จำนวน {{ number_format($caseoutsides->count()) }} รายการ
        </div>
    </div>

    <div class="co-table-wrap">
        <table id="datatable-caseoutside"
               class="table table-hover align-middle co-table">
            <thead>
                <tr>
                    <th style="width: 9%;">ครั้งที่</th>
                    <th style="width: 12%;">วันที่ติดตาม</th>
                    <th>สาเหตุที่พักภายนอก</th>
                    <th>สถานที่พัก</th>
                    <th style="width: 13%;">การดำเนินงาน</th>
                    <th>ผลการติดตาม</th>
                    <th style="width: 12%;">ผู้ติดตาม</th>
                    <th>หมายเหตุ</th>
                    <th style="width: 16%;">จัดการ</th>
                </tr>
            </thead>

            <tbody>
                @foreach($caseoutsides as $case)
                    @php
                        $caseDate = \Carbon\Carbon::parse($case->date);
                        $caseDateThai = $caseDate->format('d/m/') . ($caseDate->year + 543);
                    @endphp

                    <tr>
                        <td class="text-center">{{ $case->count ?? $loop->iteration }}</td>
                        <td class="text-center">{{ $caseDateThai }}</td>

                        <td>
                            <div class="co-cell">
                                {{ $case->outside->outside_name ?? '-' }}
                            </div>
                        </td>

                        <td>
                            <div class="co-cell">
                                {{ $case->dormitory ?: '-' }}
                            </div>
                        </td>

                        <td class="text-center">
                            {{ $case->follo_no ?: '-' }}
                        </td>

                        <td>
                            <div class="co-cell-result">
                                {{ $case->results ?: '-' }}
                            </div>
                        </td>

                        <td>
                            <div class="co-cell">
                                {{ $case->teacher ?: '-' }}
                            </div>
                        </td>

                        <td>
                            <div class="co-cell">
                                {{ $case->remerk ?: '-' }}
                            </div>
                        </td>

                        <td class="text-center">
                            <div class="co-actions">
                                <button type="button"
                                        class="btn btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editCaseOutsideModal{{ $case->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>แก้ไข</span>
                                </button>

                                <form id="delete-form-caseoutside-{{ $case->id }}"
                                      action="{{ route('case_outside.delete', $case->id) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="button"
                                            class="btn btn-danger"
                                            data-delete-case-outside="delete-form-caseoutside-{{ $case->id }}">
                                        <i class="bi bi-trash"></i>
                                        <span>ลบ</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>