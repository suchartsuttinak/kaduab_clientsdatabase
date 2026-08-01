@if($psychiatrics->isNotEmpty())
    <div class="psychiatric-page">
        <style>
            .psychiatric-page .psy-inline-record-card {
                border: 1px solid #e2e8f0;
                border-radius: 22px;
                overflow: hidden;
                background: #ffffff;
                box-shadow:
                    0 14px 34px rgba(15, 23, 42, 0.06),
                    0 2px 8px rgba(15, 23, 42, 0.03);
            }

            .psychiatric-page .psy-inline-record-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 1.05rem 1.2rem;
                border-bottom: 1px solid #e2e8f0;
                background:
                    radial-gradient(circle at top right, rgba(59,130,246,0.10), transparent 26%),
                    linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%);
            }

            .psychiatric-page .psy-inline-record-head-left {
                display: flex;
                align-items: center;
                gap: 0.85rem;
                min-width: 0;
            }

            .psychiatric-page .psy-inline-record-icon {
                width: 46px;
                height: 46px;
                border-radius: 14px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
                background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
                color: #1d4ed8;
                box-shadow: inset 0 1px 0 rgba(255,255,255,0.8);
            }

            .psychiatric-page .psy-inline-record-icon i {
                font-size: 1.05rem;
                line-height: 1;
            }

            .psychiatric-page .psy-inline-record-text {
                min-width: 0;
            }

            .psychiatric-page .psy-inline-record-text h6 {
                margin: 0;
                font-weight: 800;
                font-size: 1rem;
                line-height: 1.35;
                color: #0f172a;
                letter-spacing: -0.01em;
            }

            .psychiatric-page .psy-inline-record-text small {
                display: block;
                margin-top: 0.2rem;
                color: #64748b;
                font-size: 0.82rem;
                line-height: 1.45;
            }

            /* ===== datatable wrapper ===== */
            .psychiatric-page .dataTables_wrapper {
                padding-top: 0;
            }

            .psychiatric-page .dataTables_wrapper > .row:first-child {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 1rem;
                margin: 0 !important;
                padding: 0.95rem 1.15rem 0.6rem;
                background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
                border-bottom: 1px solid #eef2f7;
            }

            /* สำคัญมาก: ทับ bootstrap col-md-6 ของ DataTables */
            .psychiatric-page .dataTables_wrapper > .row:first-child > div:first-child {
                flex: 1 1 auto !important;
                width: auto !important;
                max-width: none !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .psychiatric-page .dataTables_wrapper > .row:first-child > div:last-child {
                flex: 0 0 auto !important;
                width: auto !important;
                max-width: none !important;
                margin-left: auto !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .psychiatric-page .dataTables_wrapper .dataTables_length,
            .psychiatric-page .dataTables_wrapper .dataTables_filter {
                margin: 0 !important;
                padding: 0 !important;
            }

            .psychiatric-page .dataTables_wrapper .dataTables_length {
                display: flex;
                align-items: center;
                justify-content: flex-start;
            }

            .psychiatric-page .dataTables_wrapper .dataTables_filter {
                display: flex !important;
                align-items: center;
                justify-content: flex-end !important;
                text-align: right !important;
                width: auto !important;
                margin-left: auto !important;
            }

            .psychiatric-page .dataTables_wrapper .dataTables_length label,
            .psychiatric-page .dataTables_wrapper .dataTables_filter label {
                display: flex;
                align-items: center;
                gap: 0.55rem;
                margin: 0 !important;
                font-size: 0.88rem;
                font-weight: 600;
                color: #475569;
                line-height: 1.45;
                white-space: nowrap;
            }

            .psychiatric-page .dataTables_wrapper .dataTables_filter label {
                justify-content: flex-end !important;
                width: auto !important;
                margin-left: auto !important;
            }

            .psychiatric-page .dataTables_wrapper .dataTables_length select {
                min-width: 72px;
                height: 40px;
                padding: 0.38rem 2rem 0.38rem 0.75rem;
                border: 1px solid #dbe3ee;
                border-radius: 12px;
                font-size: 0.9rem;
                color: #0f172a;
                background-color: #fff;
                box-shadow: none;
                margin: 0 !important;
            }

            .psychiatric-page .dataTables_wrapper .dataTables_filter input {
                width: 190px !important;
                min-width: 190px !important;
                height: 40px;
                padding: 0.45rem 0.85rem;
                border: 1px solid #dbe3ee;
                border-radius: 12px;
                font-size: 0.9rem;
                color: #0f172a;
                background: #fff;
                box-shadow: none;
                margin: 0 0 0 0.4rem !important;
            }

            .psychiatric-page .dataTables_wrapper .dataTables_length select:focus,
            .psychiatric-page .dataTables_wrapper .dataTables_filter input:focus {
                border-color: #93c5fd;
                box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.14);
                outline: none;
            }

            .psychiatric-page .dataTables_wrapper .row:nth-child(2) {
                margin: 0 !important;
            }

            .psychiatric-page .dataTables_wrapper .row:nth-child(3) {
                margin: 0 !important;
                padding: 0.85rem 1.15rem 1rem;
                align-items: center;
            }

            .psychiatric-page .dataTables_wrapper .dataTables_info {
                padding-top: 0 !important;
                font-size: 0.86rem;
                color: #64748b;
            }

            .psychiatric-page .dataTables_wrapper .dataTables_paginate {
                padding-top: 0 !important;
                margin-left: auto;
                text-align: right;
            }

            .psychiatric-page .dataTables_wrapper .paginate_button {
                border-radius: 10px !important;
            }

            /* ===== table ===== */
            .psychiatric-page .psy-inline-table-wrap {
                width: 100%;
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
                background: #fff;
            }

            .psychiatric-page .psy-inline-table-wrap::-webkit-scrollbar {
                height: 8px;
            }

            .psychiatric-page .psy-inline-table-wrap::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 999px;
            }

            .psychiatric-page .psy-inline-table {
                width: 100%;
                min-width: 1180px;
                margin-bottom: 0;
                border-collapse: separate;
                border-spacing: 0;
            }

            .psychiatric-page .psy-inline-table thead th {
                position: relative;
                background: #f8fafc;
                color: #0f172a;
                font-size: 0.87rem;
                font-weight: 800;
                text-align: center;
                vertical-align: middle;
                white-space: nowrap;
                padding: 0.95rem 0.8rem;
                border-bottom: 1px solid #e2e8f0;
            }

            .psychiatric-page .psy-inline-table tbody td {
                vertical-align: middle;
                font-size: 0.89rem;
                color: #1e293b;
                padding: 0.95rem 0.8rem;
                background: #fff;
                border-bottom: 1px solid #eef2f7;
            }

            .psychiatric-page .psy-inline-table tbody tr:nth-child(even) td {
                background: #fcfdff;
            }

            .psychiatric-page .psy-inline-table tbody tr:hover td {
                background: #f8fbff;
            }

            .psychiatric-page .psy-inline-table td.text-wrap {
                white-space: normal;
                word-break: break-word;
                line-height: 1.55;
            }

            .psychiatric-page .psy-inline-table td.text-center {
                white-space: nowrap;
            }

            .psychiatric-page .psy-inline-table .badge {
                padding: 0.42rem 0.65rem;
                font-size: 0.74rem;
                font-weight: 700;
                letter-spacing: 0.01em;
                box-shadow: inset 0 1px 0 rgba(255,255,255,0.25);
            }

            /* ===== actions ===== */
            .psychiatric-page .psy-inline-action-group {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.45rem;
                flex-wrap: nowrap;
                overflow: visible;
                padding-bottom: 0.1rem;
            }

            .psychiatric-page .psy-inline-action-group::-webkit-scrollbar {
                height: 6px;
            }

            .psychiatric-page .psy-inline-action-group > * {
                flex: 0 0 auto;
            }

            .psychiatric-page .psy-inline-btn-action {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.38rem;
                min-height: 36px;
                padding: 0.45rem 0.8rem;
                border-radius: 11px;
                font-weight: 700;
                white-space: nowrap;
                line-height: 1;
                border-width: 1px;
                box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
            }

            .psychiatric-page .psy-inline-btn-action i {
                font-size: 0.9rem;
                line-height: 1;
            }

            .psychiatric-page .psy-inline-btn-action:hover {
                transform: translateY(-1px);
            }

            /* ===== footer ===== */
            .psychiatric-page .psy-inline-footer-note {
                display: flex;
                align-items: center;
                gap: 0.45rem;
                padding: 0.9rem 1.15rem 1rem;
                color: #64748b;
                font-size: 0.86rem;
                background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
                border-top: 1px solid #eef2f7;
            }

            .psychiatric-page .psy-inline-footer-note::before {
                content: "";
                width: 8px;
                height: 8px;
                border-radius: 999px;
                background: #93c5fd;
                flex: 0 0 auto;
            }

            .psychiatric-page .psy-inline-empty {
                border: 1px dashed #cbd5e1;
                border-radius: 18px;
                background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
                color: #475569;
                padding: 1.1rem;
                text-align: center;
                font-size: 0.92rem;
            }

            @media (max-width: 767.98px) {
                .psychiatric-page .psy-inline-record-head {
                    padding: 0.95rem;
                }

                .psychiatric-page .psy-inline-record-icon {
                    width: 42px;
                    height: 42px;
                }

                .psychiatric-page .psy-inline-record-text h6 {
                    font-size: 0.95rem;
                }

                .psychiatric-page .psy-inline-record-text small {
                    font-size: 0.78rem;
                }

                .psychiatric-page .dataTables_wrapper > .row:first-child {
                    padding: 0.85rem 0.95rem 0.5rem;
                }

                .psychiatric-page .dataTables_wrapper .row:nth-child(3) {
                    padding: 0.8rem 0.95rem 0.95rem;
                }

                .psychiatric-page .dataTables_wrapper .dataTables_length label,
                .psychiatric-page .dataTables_wrapper .dataTables_filter label {
                    font-size: 0.84rem;
                }

                .psychiatric-page .psy-inline-table thead th,
                .psychiatric-page .psy-inline-table tbody td {
                    font-size: 0.84rem;
                }

                .psychiatric-page .psy-inline-footer-note {
                    font-size: 0.82rem;
                }
            }

            @media (max-width: 575.98px) {
                .psychiatric-page .psy-inline-record-card {
                    border-radius: 18px;
                }

                .psychiatric-page .psy-inline-record-head {
                    padding: 0.9rem;
                }

                .psychiatric-page .psy-inline-record-icon {
                    width: 38px;
                    height: 38px;
                    border-radius: 12px;
                }

                .psychiatric-page .dataTables_wrapper > .row:first-child {
                    flex-direction: column !important;
                    align-items: stretch !important;
                    padding: 0.8rem 0.85rem 0.45rem;
                }

                .psychiatric-page .dataTables_wrapper > .row:first-child > div:first-child,
                .psychiatric-page .dataTables_wrapper > .row:first-child > div:last-child {
                    width: 100% !important;
                    max-width: 100% !important;
                    flex: 1 1 100% !important;
                    margin-left: 0 !important;
                }

                .psychiatric-page .dataTables_wrapper .dataTables_length,
                .psychiatric-page .dataTables_wrapper .dataTables_filter {
                    width: 100% !important;
                    margin-left: 0 !important;
                }

                .psychiatric-page .dataTables_wrapper .dataTables_filter {
                    justify-content: stretch !important;
                    text-align: left !important;
                }

                .psychiatric-page .dataTables_wrapper .dataTables_length label,
                .psychiatric-page .dataTables_wrapper .dataTables_filter label {
                    width: 100%;
                    justify-content: space-between;
                    gap: 0.6rem;
                    flex-wrap: nowrap;
                }

                .psychiatric-page .dataTables_wrapper .dataTables_length select {
                    min-width: 68px;
                    height: 38px;
                }

                .psychiatric-page .dataTables_wrapper .dataTables_filter input {
                    min-width: 0 !important;
                    width: 100% !important;
                    height: 38px;
                    margin-left: 0 !important;
                }

                .psychiatric-page .dataTables_wrapper .row:nth-child(3) {
                    padding: 0.75rem 0.85rem 0.9rem;
                }

                .psychiatric-page .psy-inline-footer-note {
                    padding-left: 0.9rem;
                    padding-right: 0.9rem;
                }

                .psychiatric-page .psy-inline-btn-action {
                    min-height: 34px;
                    padding: 0.42rem 0.72rem;
                    font-size: 0.82rem;
                }
            }
        </style>

        <div class="card border-0 shadow-sm psychiatric-table-card psy-inline-record-card">
            <div class="psy-inline-record-head">
                <div class="psy-inline-record-head-left">
                    <div class="psy-inline-record-icon">
                        <i class="bi bi-table"></i>
                    </div>
                    <div class="psy-inline-record-text">
                        <h6>รายการข้อมูลการตรวจวินิจฉัยทางจิตเวช</h6>
                        <small>แสดงประวัติการตรวจ ผลการวินิจฉัย การรักษา และการติดตามต่อเนื่องทั้งหมด</small>
                    </div>
                </div>
            </div>

            <div class="psy-inline-table-wrap">
                <table id="datatable-psychiatric" class="table align-middle psy-inline-table">
                    <thead>
                        <tr>
                            {{-- <th style="min-width: 40px;">#</th> --}}
                            <th style="min-width: 130px;">วันที่ส่งตรวจ</th>
                            <th style="min-width: 220px;">สถานพยาบาล</th>
                            <th style="min-width: 300px;">ผลการตรวจ / การวินิจฉัย</th>
                            <th style="min-width: 130px;">นัดครั้งต่อไป</th>
                            <th style="min-width: 160px;">การรักษา</th>
                            <th style="min-width: 140px;">การขึ้นทะเบียน</th>
                            <th class="psy-col-actions" style="min-width: 185px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($psychiatrics as $index => $psychiatric)
                            <tr>
                                {{-- <td class="text-center">{{ $index + 1 }}</td> --}}

                                <td class="text-center" data-order="{{ $psychiatric->sent_date ? \Carbon\Carbon::parse($psychiatric->sent_date)->format('Y-m-d') : '' }}">
                                    {{ $psychiatric->sent_date ? \Carbon\Carbon::parse($psychiatric->sent_date)->addYears(543)->format('d/m/Y') : '-' }}
                                </td>

                                <td class="text-wrap">
                                    {{ $psychiatric->hotpital ?? '-' }}
                                </td>

                                <td class="text-wrap">
                                    <div class="fw-semibold">{{ $psychiatric->psycho->psycho_name ?? '-' }}</div>
                                    @if(filled($psychiatric->diagnose))
                                        <small class="text-muted d-block mt-1">{{ $psychiatric->diagnose }}</small>
                                    @endif
                                </td>

                                <td class="text-center" data-order="{{ $psychiatric->appoin_date ? \Carbon\Carbon::parse($psychiatric->appoin_date)->format('Y-m-d') : '' }}">
                                    {{ $psychiatric->appoin_date ? \Carbon\Carbon::parse($psychiatric->appoin_date)->addYears(543)->format('d/m/Y') : '-' }}
                                </td>

                                <td class="text-center">
                                    @if($psychiatric->drug_no === 'yes')
                                        <span class="badge rounded-pill text-bg-success">รับยา</span>
                                        @if(filled($psychiatric->drug_name))
                                            <small class="text-muted d-block mt-1">{{ $psychiatric->drug_name }}</small>
                                        @endif
                                    @else
                                        <span class="badge rounded-pill text-bg-secondary">ไม่รับยา</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if($psychiatric->disa_no === 'yes')
                                        <span class="badge rounded-pill text-bg-info">ขึ้นทะเบียน</span>
                                    @else
                                        <span class="badge rounded-pill text-bg-secondary">ไม่ขึ้นทะเบียน</span>
                                    @endif
                                </td>

                                <td class="text-center psy-col-actions">
                                    <div class="psy-inline-action-group">
                                        <button type="button"
                                                class="btn btn-warning btn-sm psy-inline-btn-action js-psychiatric-edit"
                                                data-id="{{ $psychiatric->id }}"
                                                data-edit-url="{{ url('/psychiatric/edit-json/' . $psychiatric->id) }}"
                                                data-update-url="{{ url('/psychiatric/' . $psychiatric->id) }}">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>แก้ไข</span>
                                        </button>

                                        <button type="button"
                                                class="btn btn-danger btn-sm psy-inline-btn-action js-psychiatric-delete"
                                                data-form-id="delete-form-psychiatric-{{ $psychiatric->id }}">
                                            <i class="bi bi-trash"></i>
                                            <span>ลบ</span>
                                        </button>

                                    </div>

                                    <form id="delete-form-psychiatric-{{ $psychiatric->id }}"
                                          action="{{ route('psychiatric.delete', $psychiatric->id) }}"
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

            <div class="psy-inline-footer-note">
                ตารางรองรับการเลื่อนแนวนอนอัตโนมัติบนจอเล็ก
            </div>
        </div>
    </div>
@else
    <div class="psychiatric-page">
        <style>
            .psychiatric-page .psy-inline-empty {
                border: 1px dashed #cbd5e1;
                border-radius: 18px;
                background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
                color: #475569;
                padding: 1.1rem;
                text-align: center;
                font-size: 0.92rem;
            }
        </style>

        <div class="psy-inline-empty mt-2">
            <i class="bi bi-info-circle me-1"></i>
            {{ filled($startDate) || filled($endDate) ? 'ไม่พบข้อมูลตามช่วงวันที่ที่เลือก' : 'ยังไม่มีข้อมูลการตรวจจิตเวช' }}
        </div>
    </div>
@endif