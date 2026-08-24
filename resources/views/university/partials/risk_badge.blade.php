@php
    $riskValue = $value ?? 'normal';
    $riskLabel = config('university_tracking.risk_levels.' . $riskValue, $riskValue);
@endphp
<span class="ut-badge ut-badge-{{ $riskValue }}">{{ $riskLabel }}</span>
