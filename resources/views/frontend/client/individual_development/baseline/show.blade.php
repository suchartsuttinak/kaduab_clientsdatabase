@extends('admin_client.admin_client')

@section('content')
@php
    $clientName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
    $itemsByIndicator = $assessment->items->keyBy('indicator_id');
    $selectedSources = $assessment->information_sources ?? [];
    $thaiDate = static function ($value): string {
        if (!$value) return '-';
        try {
            $date = $value instanceof \Carbon\CarbonInterface ? $value : \Carbon\Carbon::parse($value, 'Asia/Bangkok');
            return $date->format('d/m/') . ($date->year + 543);
        } catch (\Throwable $e) { return '-'; }
    };
    $domainIcons = [
        'physical' => 'bi-heart-pulse',
        'emotional' => 'bi-emoji-smile',
        'social' => 'bi-people',
        'intellectual' => 'bi-lightbulb',
    ];
@endphp

<style>
.idp-base-show{--idp-border:#e3e9f1;--idp-text:#203049;--idp-muted:#6c7c91;padding-bottom:1.5rem}
.idp-base-show .idp-header,.idp-base-show .idp-card,.idp-base-show .idp-domain{background:#fff;border:1px solid var(--idp-border);border-radius:16px;box-shadow:0 6px 20px rgba(31,47,70,.045)}
.idp-base-show .idp-header{padding:1.05rem 1.2rem;margin-bottom:1rem}
.idp-base-show .idp-title{margin:0;color:var(--idp-text);font-size:1.15rem;font-weight:800}
.idp-base-show .idp-subtitle{margin:.28rem 0 0;color:var(--idp-muted);font-size:.86rem}
.idp-base-show .idp-meta{display:flex;flex-wrap:wrap;gap:.45rem;margin-top:.72rem}
.idp-base-show .idp-pill{display:inline-flex;align-items:center;gap:.35rem;padding:.34rem .68rem;border:1px solid #dbe5f0;border-radius:999px;background:#f8fbff;color:#41546d;font-size:.8rem}
.idp-base-show .idp-actions-top{display:flex;flex-wrap:wrap;gap:.5rem;justify-content:flex-end}
.idp-base-show .idp-btn{display:inline-flex;align-items:center;justify-content:center;gap:.4rem;min-height:39px;padding:.48rem .85rem;border-radius:10px;font-size:.82rem;font-weight:700;text-decoration:none!important;white-space:nowrap}
.idp-base-show .idp-btn-primary{border:0;background:linear-gradient(135deg,#3577bd 0%,#245f9f 100%);color:#fff;box-shadow:0 6px 14px rgba(36,95,159,.16)}
.idp-base-show .idp-btn-light{border:1px solid #d7e0e9;background:#fff;color:#4f6075}
.idp-base-show .idp-score-card{height:100%;padding:1rem;border:1px solid var(--idp-border);border-radius:14px;background:#fff}
.idp-base-show .idp-score-icon{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:10px;background:#eff6ff;color:#376fae;margin-bottom:.6rem}
.idp-base-show .idp-score-name{font-size:.88rem;font-weight:800;color:var(--idp-text)}
.idp-base-show .idp-score-value{font-size:1.45rem;font-weight:800;color:#245f9f;margin-top:.22rem}
.idp-base-show .idp-score-level{font-size:.76rem;color:var(--idp-muted);margin-top:.15rem}
.idp-base-show .idp-card{overflow:hidden;margin-bottom:1rem}
.idp-base-show .idp-head{padding:.85rem 1rem;border-bottom:1px solid var(--idp-border);background:#fbfcfe;color:var(--idp-text);font-size:.93rem;font-weight:800}
.idp-base-show .idp-body{padding:1rem}
.idp-base-show .idp-info-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.8rem 1rem}
.idp-base-show .idp-info-wide{grid-column:1/-1}
.idp-base-show .idp-label{font-size:.75rem;font-weight:700;color:var(--idp-muted);margin-bottom:.18rem}
.idp-base-show .idp-value{font-size:.86rem;line-height:1.6;color:#34465e;white-space:pre-line}
.idp-base-show .idp-domain{overflow:hidden;margin-bottom:1rem}
.idp-base-show .idp-domain-head{display:flex;align-items:center;justify-content:space-between;gap:.7rem;padding:.85rem 1rem;background:#f8fbff;border-bottom:1px solid var(--idp-border)}
.idp-base-show .idp-domain-name{display:flex;align-items:center;gap:.5rem;font-size:.94rem;font-weight:800;color:var(--idp-text)}
.idp-base-show .idp-domain-icon{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:10px;background:#edf5ff;color:#376fae}
.idp-base-show .idp-domain-summary{font-size:.79rem;color:#5c6f86;white-space:nowrap}
.idp-base-show .idp-domain-summary strong{font-size:1rem;color:#245f9f}
.idp-base-show .idp-table-wrap{width:100%;overflow-x:auto}
.idp-base-show table{width:100%;min-width:900px;border-collapse:separate;border-spacing:0}
.idp-base-show th,.idp-base-show td{padding:.7rem .72rem;border-bottom:1px solid #edf1f5;vertical-align:top;font-size:.8rem;color:#3f5066}
.idp-base-show th{background:#fbfcfe;color:#56677d;font-weight:800;white-space:nowrap}
.idp-base-show .idp-level-badge{display:inline-flex;align-items:center;justify-content:center;min-width:30px;height:30px;border-radius:9px;background:#eef5ff;color:#2e639e;font-weight:800}
.idp-base-show .idp-source-list{display:flex;flex-wrap:wrap;gap:.4rem}
.idp-base-show .idp-source{padding:.3rem .58rem;border-radius:999px;border:1px solid #dce6f0;background:#f9fbfd;color:#506177;font-size:.75rem}
@media(max-width:767.98px){.idp-base-show .idp-info-grid{grid-template-columns:1fr}.idp-base-show .idp-actions-top{justify-content:flex-start}.idp-base-show .idp-domain-head{align-items:flex-start;flex-direction:column}}

/* IDP_PHASE5_UI_STABLE_V1 */
.idp-base-show{width:100%;min-width:0}
.idp-base-show *{min-width:0}
.idp-base-show .idp-value,.idp-base-show td{overflow-wrap:anywhere;word-break:break-word}
@media(max-width:575.98px){
  .idp-base-show .idp-actions-top{display:grid!important;grid-template-columns:1fr!important;width:100%}
  .idp-base-show .idp-actions-top .btn{width:100%}
}

</style>

<div class="container-fluid px-2 px-lg-3 idp-base-show">
    <div class="idp-header">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <h4 class="idp-title"><i class="bi bi-clipboard2-check me-2 text-primary"></i>ผลประเมินระดับเริ่มต้น (Baseline)</h4>
                <p class="idp-subtitle">ผลตั้งต้นสำหรับใช้กำหนดเป้าหมายและเปรียบเทียบพัฒนาการในรอบติดตามต่อไป</p>
                <div class="idp-meta">
                    <span class="idp-pill"><i class="bi bi-person"></i>ผู้รับบริการ: <strong>{{ $clientName }}</strong></span>
                    <span class="idp-pill"><i class="bi bi-calendar3"></i>อายุ: <strong>{{ $ageText }}</strong></span>
                    <span class="idp-pill"><i class="bi bi-list-ol"></i>แผนครั้งที่: <strong>{{ $plan->plan_no }}</strong></span>
                    <span class="idp-pill"><i class="bi bi-calendar-check"></i>ประเมิน: <strong>{{ $thaiDate($assessment->assessment_date) }}</strong></span>
                </div>
            </div>
            <div class="idp-actions-top">
                <a href="{{ route('individual-development.index', $client->id) }}" class="idp-btn idp-btn-light"><i class="bi bi-arrow-left"></i>กลับหน้าสรุป</a>
                @if($canUpdateBaseline)
                    <a href="{{ route('individual-development.baseline.edit', $client->id) }}" class="idp-btn idp-btn-primary"><i class="bi bi-pencil-square"></i>แก้ไข Baseline</a>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        @foreach($domainSummaries as $summary)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="idp-score-card">
                    <span class="idp-score-icon"><i class="bi {{ $domainIcons[$summary['code']] ?? 'bi-clipboard-data' }}"></i></span>
                    <div class="idp-score-name">{{ $summary['name'] }}</div>
                    <div class="idp-score-value">{{ $summary['average'] !== null ? number_format($summary['average'], 2) : '-' }} <small class="fs-6 text-muted">/ 5</small></div>
                    <div class="idp-score-level">ระดับ {{ $summary['level'] ?? '-' }} • {{ $summary['label'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="idp-card">
        <div class="idp-head">ข้อมูลประกอบการประเมิน</div>
        <div class="idp-body">
            <div class="idp-info-grid">
                <div class="idp-info-wide">
                    <div class="idp-label">แหล่งข้อมูล/ผู้ร่วมให้ข้อมูล</div>
                    <div class="idp-source-list">
                        @forelse($selectedSources as $source)
                            <span class="idp-source">{{ $informationSourceOptions[$source] ?? $source }}</span>
                        @empty
                            <span class="text-muted small">-</span>
                        @endforelse
                    </div>
                </div>
                <div>
                    <div class="idp-label">ผู้ประเมิน</div>
                    <div class="idp-value">{{ $assessment->assessor?->name ?? '-' }}</div>
                </div>
                <div>
                    <div class="idp-label">หมายเหตุผู้ร่วมประเมิน/แหล่งข้อมูล</div>
                    <div class="idp-value">{{ $assessment->participant_note ?: '-' }}</div>
                </div>
                <div class="idp-info-wide">
                    <div class="idp-label">สรุปสถานการณ์ปัจจุบัน</div>
                    <div class="idp-value">{{ $assessment->overall_note ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="idp-card">
        <div class="idp-head">บริบทและปัจจัยสำหรับวางแผน</div>
        <div class="idp-body">
            <div class="idp-info-grid">
                @foreach([
                    'strength_summary' => 'จุดแข็ง',
                    'development_need_summary' => 'ประเด็นที่ควรพัฒนา',
                    'client_need_summary' => 'ความต้องการของผู้รับบริการ',
                    'caregiver_need_summary' => 'ความต้องการของผู้ดูแล/ครอบครัว',
                    'risk_factor_summary' => 'ปัจจัยเสี่ยง',
                    'protective_factor_summary' => 'ปัจจัยคุ้มครอง',
                    'support_network_summary' => 'เครือข่าย/ทรัพยากรสนับสนุน',
                ] as $field => $label)
                    <div class="{{ $field === 'support_network_summary' ? 'idp-info-wide' : '' }}">
                        <div class="idp-label">{{ $label }}</div>
                        <div class="idp-value">{{ $plan->{$field} ?: '-' }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @foreach($domains as $domain)
        @php $summary = $domainSummaries->firstWhere('domain_id', $domain->id); @endphp
        <div class="idp-domain">
            <div class="idp-domain-head">
                <div class="idp-domain-name"><span class="idp-domain-icon"><i class="bi {{ $domainIcons[$domain->code] ?? 'bi-clipboard-data' }}"></i></span>{{ $loop->iteration }}. {{ $domain->name }}</div>
                <div class="idp-domain-summary">ค่าเฉลี่ย <strong>{{ $summary && $summary['average'] !== null ? number_format($summary['average'], 2) : '-' }}</strong> / 5 • {{ $summary['label'] ?? '-' }}</div>
            </div>
            <div class="idp-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:50px" class="text-center">ระดับ</th>
                            <th style="width:230px">ตัวชี้วัด</th>
                            <th style="width:240px">เกณฑ์ที่เลือก</th>
                            <th>หลักฐาน/พฤติกรรมที่สังเกต</th>
                            <th>ข้อสังเกต/ประเด็นพัฒนา</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($domain->indicators as $indicator)
                            @php
                                $item = $itemsByIndicator->get($indicator->id);
                                $rubric = $indicator->rubrics->firstWhere('level', (int)($item?->score ?? 0));
                            @endphp
                            <tr>
                                <td class="text-center"><span class="idp-level-badge">{{ $item?->score ?? '-' }}</span></td>
                                <td><strong>{{ $indicator->name }}</strong><div class="small text-muted mt-1">{{ $indicator->description }}</div></td>
                                <td><strong>{{ $rubric?->title ?: ($item ? 'ระดับ '.$item->score : '-') }}</strong><div class="small text-muted mt-1">{{ $rubric?->description }}</div></td>
                                <td style="white-space:pre-line">{{ $item?->evidence ?: '-' }}</td>
                                <td style="white-space:pre-line">{{ $item?->development_note ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>

@if(session('success') || session('warning'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.Swal) return;
    Swal.fire({
        icon: @json(session('success') ? 'success' : 'warning'),
        title: @json(session('success') ? 'สำเร็จ' : 'แจ้งเตือน'),
        text: @json(session('success') ?? session('warning')),
        confirmButtonText: 'OK',
        timer: 3000,
        timerProgressBar: true
    });
});
</script>
@endif
@endsection
