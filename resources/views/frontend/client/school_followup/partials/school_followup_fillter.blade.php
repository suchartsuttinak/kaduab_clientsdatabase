@php
    $hasFollowups = $hasFollowups ?? (isset($followups) && $followups->isNotEmpty());
    $showSchoolFollowupFilter = $showSchoolFollowupFilter ?? false;

    $startDateError = $errors->getBag('filters')->first('start_date')
        ?: $errors->first('start_date');
    $endDateError = $errors->getBag('filters')->first('end_date')
        ?: $errors->first('end_date');
@endphp

@if($hasFollowups)
<div id="schoolFollowupFilterPanel"
     class="collapse sf-filter-collapse {{ $showSchoolFollowupFilter ? 'show' : '' }}"
     data-permission-keep>
    <div class="sf-filter" data-permission-keep>
        <form method="GET"
              action="{{ route('school_followup_add', $client->id) }}"
              data-permission-keep>
            <div class="sf-filter-row">
                <div>
                    <label class="sf-filter-label" for="sf_start_date">
                        <i class="bi bi-calendar-event"></i>
                        ตั้งแต่วันที่
                    </label>
                    <input type="date"
                           id="sf_start_date"
                           name="start_date"
                           class="form-control sf-filter-control {{ $startDateError ? 'is-invalid' : '' }}"
                           data-permission-keep
                           value="{{ request('start_date', old('start_date')) }}"
                           max="{{ now('Asia/Bangkok')->toDateString() }}">
                    @if($startDateError)
                        <div class="sf-filter-error">{{ $startDateError }}</div>
                    @endif
                </div>

                <div>
                    <label class="sf-filter-label" for="sf_end_date">
                        <i class="bi bi-calendar-check"></i>
                        ถึงวันที่
                    </label>
                    <input type="date"
                           id="sf_end_date"
                           name="end_date"
                           class="form-control sf-filter-control {{ $endDateError ? 'is-invalid' : '' }}"
                           data-permission-keep
                           value="{{ request('end_date', old('end_date')) }}"
                           max="{{ now('Asia/Bangkok')->toDateString() }}">
                    @if($endDateError)
                        <div class="sf-filter-error">{{ $endDateError }}</div>
                    @endif
                </div>

                <div class="sf-filter-actions">
                    <button type="submit"
                            class="btn btn-primary sf-btn"
                            data-permission-keep>
                        <i class="bi bi-search"></i>
                        <span>ค้นหา</span>
                    </button>

                    <a href="{{ route('school_followup_add', $client->id) }}"
                       class="btn btn-outline-secondary sf-btn"
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