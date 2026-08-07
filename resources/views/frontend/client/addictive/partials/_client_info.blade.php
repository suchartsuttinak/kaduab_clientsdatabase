@php
    $addictiveDateFrom = old('date_from', request('date_from', $dateFrom ?? ''));
    $addictiveDateTo = old('date_to', request('date_to', $dateTo ?? ''));

    $addictiveFilterErrors = $errors->getBag('filters');
    $addictiveDateFromError = $addictiveFilterErrors->first('date_from');
    $addictiveDateToError = $addictiveFilterErrors->first('date_to');

    if (!$addictiveDateFromError && blank(old('_form_context'))) {
        $addictiveDateFromError = $errors->first('date_from');
    }

    if (!$addictiveDateToError && blank(old('_form_context'))) {
        $addictiveDateToError = $errors->first('date_to');
    }
@endphp

@if($canShowAddictiveFilter && $canAddictivePrint)
<div id="addictiveFilterPanel"
     class="collapse ad-filter-collapse {{ $showAddictiveFilter ? 'show' : '' }}"
     data-permission-keep>
    <style>
        .addictive-page-v2 .ad-filter-collapse {
            margin-bottom: 1rem;
        }

        .addictive-page-v2 .ad-filter-collapse:not(.show) {
            margin-bottom: 0;
        }

        .addictive-page-v2 .ad-filter-card {
            margin-bottom: 0;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);
        }

        .addictive-page-v2 .ad-filter-card-body {
            padding: 14px 16px;
        }

        .addictive-page-v2 .ad-filter-grid {
            display: grid;
            grid-template-columns: minmax(180px, 1fr) minmax(180px, 1fr) auto;
            gap: 12px;
            align-items: end;
        }

        .addictive-page-v2 .ad-filter-group {
            min-width: 0;
        }

        .addictive-page-v2 .ad-filter-pro-label {
            display: block;
            margin-bottom: 6px;
            color: #334155;
            font-size: 0.84rem;
            font-weight: 700;
            line-height: 1.35;
        }

        .addictive-page-v2 .ad-filter-pro-label i {
            margin-right: 4px;
            color: #64748b;
            font-size: 0.82rem;
        }

        .addictive-page-v2 .ad-filter-card .form-control {
            min-height: 42px;
            border-color: #dbe3ec;
            border-radius: 11px;
            background-color: #ffffff;
            color: #0f172a;
            font-size: 0.9rem;
            box-shadow: none;
        }

        .addictive-page-v2 .ad-filter-card .form-control:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 0.18rem rgba(37, 99, 235, 0.10);
        }

        .addictive-page-v2 .ad-filter-pro-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .addictive-page-v2 .ad-filter-pro-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 42px;
            padding: 0.58rem 0.9rem;
            border-radius: 11px;
            font-size: 0.88rem;
            font-weight: 700;
            white-space: nowrap;
        }

        @media (max-width: 991.98px) {
            .addictive-page-v2 .ad-filter-grid {
                grid-template-columns: 1fr 1fr;
            }

            .addictive-page-v2 .ad-filter-pro-actions {
                grid-column: 1 / -1;
                justify-content: flex-start;
            }
        }

        @media (max-width: 575.98px) {
            .addictive-page-v2 .ad-filter-card-body {
                padding: 12px;
            }

            .addictive-page-v2 .ad-filter-grid {
                grid-template-columns: 1fr;
            }

            .addictive-page-v2 .ad-filter-pro-actions {
                grid-column: auto;
                width: 100%;
            }

            .addictive-page-v2 .ad-filter-pro-btn {
                width: 100%;
                flex: 1 1 100%;
            }
        }
    </style>

    <div class="ad-filter-card" data-permission-keep>
        <div class="ad-filter-card-body">
            <form method="GET"
                  action="{{ route('addictive.report.all', $client->id) }}"
                  data-permission-keep>
                <div class="ad-filter-grid">
                    <div class="ad-filter-group">
                        <label class="ad-filter-pro-label" for="addictive_date_from">
                            <i class="bi bi-calendar-event" aria-hidden="true"></i>
                            วันที่เริ่มต้น
                        </label>

                        <input type="date"
                               id="addictive_date_from"
                               name="date_from"
                               class="form-control {{ $addictiveDateFromError ? 'is-invalid' : '' }}"
                               value="{{ $addictiveDateFrom }}"
                               max="{{ now('Asia/Bangkok')->toDateString() }}"
                               data-permission-keep>

                        @if($addictiveDateFromError)
                            <div class="invalid-feedback d-block">{{ $addictiveDateFromError }}</div>
                        @endif
                    </div>

                    <div class="ad-filter-group">
                        <label class="ad-filter-pro-label" for="addictive_date_to">
                            <i class="bi bi-calendar-check" aria-hidden="true"></i>
                            วันที่สิ้นสุด
                        </label>

                        <input type="date"
                               id="addictive_date_to"
                               name="date_to"
                               class="form-control {{ $addictiveDateToError ? 'is-invalid' : '' }}"
                               value="{{ $addictiveDateTo }}"
                               @if(filled($addictiveDateFrom)) min="{{ $addictiveDateFrom }}" @endif
                               max="{{ now('Asia/Bangkok')->toDateString() }}"
                               data-permission-keep>

                        @if($addictiveDateToError)
                            <div class="invalid-feedback d-block">{{ $addictiveDateToError }}</div>
                        @endif
                    </div>

                    <div class="ad-filter-pro-actions">
                        <button type="submit"
                                class="btn btn-primary ad-filter-pro-btn"
                                data-permission-keep>
                            <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                            <span>เปิดรายงาน</span>
                        </button>

                        <a href="{{ route('addictive.create', $client->id) }}"
                           class="btn btn-outline-secondary ad-filter-pro-btn"
                           data-permission-keep>
                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                            <span>ล้างค่า</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
