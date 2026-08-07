@php
    $showMedicalFilter = $showMedicalFilter ?? false;
    $canShowMedicalFilter = $canShowMedicalFilter
        ?? (($hasMedicalRecords ?? $medicals->isNotEmpty())
            || request()->filled('start_date')
            || request()->filled('end_date'));

    $startDateError = $errors->getBag('filters')->first('start_date')
        ?: $errors->first('start_date');
    $endDateError = $errors->getBag('filters')->first('end_date')
        ?: $errors->first('end_date');
@endphp

@if($canShowMedicalFilter)
<div id="medicalFilterPanel"
     class="collapse md-filter-collapse {{ $showMedicalFilter ? 'show' : '' }}"
     data-permission-keep>
    <div class="md-filter" data-permission-keep>
        <form method="GET"
              action="{{ route('medical.add', ['client_id' => $client->id]) }}"
              data-permission-keep>
            <div class="md-filter-row">
                <div>
                    <label class="md-filter-label" for="medical_start_date">
                        <i class="bi bi-calendar-event"></i>
                        ตั้งแต่วันที่
                    </label>
                    <input type="date"
                           id="medical_start_date"
                           name="start_date"
                           class="form-control md-filter-control {{ $startDateError ? 'is-invalid' : '' }}"
                           value="{{ request('start_date', old('start_date')) }}"
                           max="{{ now('Asia/Bangkok')->toDateString() }}"
                           data-permission-keep>
                    @if($startDateError)
                        <div class="invalid-feedback">{{ $startDateError }}</div>
                    @endif
                </div>

                <div>
                    <label class="md-filter-label" for="medical_end_date">
                        <i class="bi bi-calendar-check"></i>
                        ถึงวันที่
                    </label>
                    <input type="date"
                           id="medical_end_date"
                           name="end_date"
                           class="form-control md-filter-control {{ $endDateError ? 'is-invalid' : '' }}"
                           value="{{ request('end_date', old('end_date')) }}"
                           max="{{ now('Asia/Bangkok')->toDateString() }}"
                           data-permission-keep>
                    @if($endDateError)
                        <div class="invalid-feedback">{{ $endDateError }}</div>
                    @endif
                </div>

                <div class="md-filter-actions">
                    <button type="submit"
                            class="btn btn-primary md-btn"
                            data-permission-keep>
                        <i class="bi bi-search"></i>
                        <span>ค้นหา</span>
                    </button>

                    <a href="{{ route('medical.add', ['client_id' => $client->id]) }}"
                       class="btn btn-outline-secondary md-btn"
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