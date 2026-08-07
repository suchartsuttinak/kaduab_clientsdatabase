@php
    $hasAbsents = $hasAbsents ?? (isset($absents) && $absents->isNotEmpty());
    $showAbsentFilter = $showAbsentFilter ?? false;
    $canShowAbsentFilter = $canShowAbsentFilter ?? ($hasAbsents || $showAbsentFilter);

    $startDateError = $errors->getBag('filters')->first('start_date')
        ?: $errors->first('start_date');
    $endDateError = $errors->getBag('filters')->first('end_date')
        ?: $errors->first('end_date');
@endphp

@if($canShowAbsentFilter)
<div id="absentFilterPanel"
     class="collapse ab-filter-collapse {{ $showAbsentFilter ? 'show' : '' }}"
     data-permission-keep>
    <div class="ab-filter" data-permission-keep>
        <form method="GET"
              action="{{ route('absent.add', $client->id) }}"
              data-permission-keep>
            <div class="ab-filter-row">
                <div>
                    <label class="ab-filter-label" for="ab_start_date">
                        <i class="bi bi-calendar-event"></i>
                        ตั้งแต่วันที่
                    </label>
                    <input type="date"
                           id="ab_start_date"
                           name="start_date"
                           class="form-control ab-filter-control {{ $startDateError ? 'is-invalid' : '' }}"
                           data-permission-keep
                           value="{{ request('start_date', old('start_date')) }}"
                           max="{{ now('Asia/Bangkok')->toDateString() }}">
                    @if($startDateError)
                        <div class="ab-filter-error">{{ $startDateError }}</div>
                    @endif
                </div>

                <div>
                    <label class="ab-filter-label" for="ab_end_date">
                        <i class="bi bi-calendar-check"></i>
                        ถึงวันที่
                    </label>
                    <input type="date"
                           id="ab_end_date"
                           name="end_date"
                           class="form-control ab-filter-control {{ $endDateError ? 'is-invalid' : '' }}"
                           data-permission-keep
                           value="{{ request('end_date', old('end_date')) }}"
                           max="{{ now('Asia/Bangkok')->toDateString() }}">
                    @if($endDateError)
                        <div class="ab-filter-error">{{ $endDateError }}</div>
                    @endif
                </div>

                <div class="ab-filter-actions">
                    <button type="submit"
                            class="btn btn-primary ab-btn"
                            data-permission-keep>
                        <i class="bi bi-search"></i>
                        <span>ค้นหา</span>
                    </button>

                    <a href="{{ route('absent.add', $client->id) }}"
                       class="btn btn-outline-secondary ab-btn"
                       data-permission-keep>
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>ล้างค่า</span>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endif