@php
    $vaccineFilterErrors = $errors->getBag('filters');
    $vaccineStartDateError = $vaccineFilterErrors->first('start_date') ?: $errors->first('start_date');
    $vaccineEndDateError = $vaccineFilterErrors->first('end_date') ?: $errors->first('end_date');
@endphp

<div id="vaccineFilterPanel"
     class="collapse vaccine-filter-collapse {{ ($showVaccineFilter ?? false) ? 'show' : '' }}"
     data-permission-keep>
    <style>
        .vaccine-page .vaccine-filter-collapse{
            margin-bottom: 1rem;
        }

        .vaccine-page .vaccine-filter-collapse:not(.show){
            margin-bottom: 0;
        }

        .vaccine-filter-card{
            margin-bottom: 0;
            border: 1px solid #e5e7eb !important;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .vaccine-filter-card .card-body{
            padding: 14px 16px;
        }

        .vaccine-filter-card .vaccine-filter-grid{
            display: grid;
            grid-template-columns: minmax(180px, 1fr) minmax(180px, 1fr) auto;
            gap: 12px;
            align-items: end;
        }

        .vaccine-filter-card .vaccine-filter-group{
            min-width: 0;
        }

        .vaccine-filter-card .vaccine-filter-group label{
            display: block;
            margin-bottom: 6px;
            color: #334155;
            font-size: .84rem;
            font-weight: 700;
        }

        .vaccine-filter-card .form-control{
            min-height: 42px;
            border-color: #dbe3ec;
            border-radius: 11px;
            box-shadow: none;
            font-size: .9rem;
        }

        .vaccine-filter-card .form-control:focus{
            border-color: #93c5fd;
            box-shadow: 0 0 0 .18rem rgba(37, 99, 235, .10);
        }

        .vaccine-filter-card .vaccine-filter-actions{
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .vaccine-filter-card .vaccine-filter-btn{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 42px;
            padding: .58rem .9rem;
            border-radius: 11px;
            font-size: .88rem;
            font-weight: 700;
            white-space: nowrap;
        }

        @media (max-width: 991.98px){
            .vaccine-filter-card .vaccine-filter-grid{
                grid-template-columns: 1fr 1fr;
            }

            .vaccine-filter-card .vaccine-filter-actions{
                grid-column: 1 / -1;
                justify-content: flex-start;
            }
        }

        @media (max-width: 575.98px){
            .vaccine-filter-card .card-body{
                padding: 12px;
            }

            .vaccine-filter-card .vaccine-filter-grid{
                grid-template-columns: 1fr;
            }

            .vaccine-filter-card .vaccine-filter-actions{
                grid-column: auto;
                width: 100%;
            }

            .vaccine-filter-card .vaccine-filter-btn{
                flex: 1 1 100%;
                width: 100%;
            }
        }
    </style>

    <div class="card vaccine-filter-card" data-permission-keep>
        <div class="card-body">
            <form method="GET"
                  action="{{ route('vaccine.index', ['client_id' => $client->id]) }}"
                  data-permission-keep>
                <div class="vaccine-filter-grid">
                    <div class="vaccine-filter-group">
                        <label for="vaccine_date_from">
                            <i class="bi bi-calendar-event me-1"></i>วันที่เริ่มต้น
                        </label>
                        <input type="date"
                               name="start_date"
                               id="vaccine_date_from"
                               class="form-control {{ $vaccineStartDateError ? 'is-invalid' : '' }}"
                               value="{{ old('start_date', request('start_date')) }}"
                               max="{{ now('Asia/Bangkok')->toDateString() }}"
                               data-permission-keep>
                        @if($vaccineStartDateError)
                            <div class="invalid-feedback">{{ $vaccineStartDateError }}</div>
                        @endif
                    </div>

                    <div class="vaccine-filter-group">
                        <label for="vaccine_date_to">
                            <i class="bi bi-calendar-check me-1"></i>วันที่สิ้นสุด
                        </label>
                        <input type="date"
                               name="end_date"
                               id="vaccine_date_to"
                               class="form-control {{ $vaccineEndDateError ? 'is-invalid' : '' }}"
                               value="{{ old('end_date', request('end_date')) }}"
                               max="{{ now('Asia/Bangkok')->toDateString() }}"
                               data-permission-keep>
                        @if($vaccineEndDateError)
                            <div class="invalid-feedback">{{ $vaccineEndDateError }}</div>
                        @endif
                    </div>

                    <div class="vaccine-filter-actions">
                        <button type="submit"
                                class="btn btn-primary vaccine-filter-btn"
                                data-permission-keep>
                            <i class="bi bi-search"></i>
                            <span>ค้นหา</span>
                        </button>

                        <a href="{{ route('vaccine.index', ['client_id' => $client->id]) }}"
                           class="btn btn-outline-secondary vaccine-filter-btn"
                           data-permission-keep>
                            <i class="bi bi-arrow-counterclockwise"></i>
                            <span>ล้างค่า</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
