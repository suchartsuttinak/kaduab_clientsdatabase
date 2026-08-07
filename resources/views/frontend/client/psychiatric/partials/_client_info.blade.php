@php
    $psychStartDate = old('start_date', request('start_date', $startDate ?? ''));
    $psychEndDate = old('end_date', request('end_date', $endDate ?? ''));

    $psychFilterErrors = $errors->getBag('filters');
    $psychStartDateError = $psychFilterErrors->first('start_date');
    $psychEndDateError = $psychFilterErrors->first('end_date');

    if (!$psychStartDateError && blank(old('_form_context'))) {
        $psychStartDateError = $errors->first('start_date');
    }

    if (!$psychEndDateError && blank(old('_form_context'))) {
        $psychEndDateError = $errors->first('end_date');
    }
@endphp

<div id="psychiatricFilterPanel"
     class="collapse psych-filter-collapse {{ ($showPsychiatricFilter ?? false) ? 'show' : '' }}"
     data-permission-keep>
    <style>
        .psychiatric-page .psych-filter-collapse {
            margin-bottom: 1rem;
        }

        .psychiatric-page .psych-filter-collapse:not(.show) {
            margin-bottom: 0;
        }

        .psychiatric-page .psych-filter-card {
            margin-bottom: 0;
            overflow: hidden;
            border: 1px solid #e2e8f0 !important;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);
        }

        .psychiatric-page .psych-filter-card .card-body {
            padding: 14px 16px;
        }

        .psychiatric-page .psych-filter-grid {
            display: grid;
            grid-template-columns: minmax(180px, 1fr) minmax(180px, 1fr) auto;
            gap: 12px;
            align-items: end;
        }

        .psychiatric-page .psych-filter-group {
            min-width: 0;
        }

        .psychiatric-page .psych-filter-label {
            display: block;
            margin-bottom: 6px;
            color: #334155;
            font-size: 0.84rem;
            font-weight: 700;
            line-height: 1.35;
        }

        .psychiatric-page .psych-filter-label i {
            margin-right: 4px;
            color: #64748b;
            font-size: 0.82rem;
        }

        .psychiatric-page .psych-filter-card .form-control {
            min-height: 42px;
            border-color: #dbe3ec;
            border-radius: 11px;
            background-color: #ffffff;
            box-shadow: none;
            color: #0f172a;
            font-size: 0.9rem;
        }

        .psychiatric-page .psych-filter-card .form-control:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 0.18rem rgba(37, 99, 235, 0.10);
        }

        .psychiatric-page .psych-filter-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .psychiatric-page .psych-filter-btn {
            min-height: 42px;
            padding: 0.58rem 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border-radius: 11px;
            font-size: 0.88rem;
            font-weight: 700;
            white-space: nowrap;
        }

        @media (max-width: 991.98px) {
            .psychiatric-page .psych-filter-grid {
                grid-template-columns: 1fr 1fr;
            }

            .psychiatric-page .psych-filter-actions {
                grid-column: 1 / -1;
                justify-content: flex-start;
            }
        }

        @media (max-width: 575.98px) {
            .psychiatric-page .psych-filter-card .card-body {
                padding: 12px;
            }

            .psychiatric-page .psych-filter-grid {
                grid-template-columns: 1fr;
            }

            .psychiatric-page .psych-filter-actions {
                grid-column: auto;
                width: 100%;
            }

            .psychiatric-page .psych-filter-btn {
                width: 100%;
                flex: 1 1 100%;
            }
        }
    </style>

    <div class="card psych-filter-card" data-permission-keep>
        <div class="card-body">
            <form method="GET"
                  action="{{ route('psychiatric.create', $client->id) }}"
                  data-permission-keep>
                <div class="psych-filter-grid">
                    <div class="psych-filter-group">
                        <label for="psych_date_from" class="psych-filter-label">
                            <i class="bi bi-calendar-event" aria-hidden="true"></i>
                            วันที่เริ่มต้น
                        </label>

                        <input type="date"
                               name="start_date"
                               id="psych_date_from"
                               class="form-control {{ $psychStartDateError ? 'is-invalid' : '' }}"
                               value="{{ $psychStartDate }}"
                               max="{{ now('Asia/Bangkok')->toDateString() }}"
                               data-permission-keep>

                        @if($psychStartDateError)
                            <div class="invalid-feedback d-block">{{ $psychStartDateError }}</div>
                        @endif
                    </div>

                    <div class="psych-filter-group">
                        <label for="psych_date_to" class="psych-filter-label">
                            <i class="bi bi-calendar-check" aria-hidden="true"></i>
                            วันที่สิ้นสุด
                        </label>

                        <input type="date"
                               name="end_date"
                               id="psych_date_to"
                               class="form-control {{ $psychEndDateError ? 'is-invalid' : '' }}"
                               value="{{ $psychEndDate }}"
                               @if(filled($psychStartDate)) min="{{ $psychStartDate }}" @endif
                               max="{{ now('Asia/Bangkok')->toDateString() }}"
                               data-permission-keep>

                        @if($psychEndDateError)
                            <div class="invalid-feedback d-block">{{ $psychEndDateError }}</div>
                        @endif
                    </div>

                    <div class="psych-filter-actions">
                        <button type="submit"
                                class="btn btn-primary psych-filter-btn"
                                data-permission-keep>
                            <i class="bi bi-search" aria-hidden="true"></i>
                            <span>ค้นหา</span>
                        </button>

                        <a href="{{ route('psychiatric.create', $client->id) }}"
                           class="btn btn-outline-secondary psych-filter-btn"
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
