@extends('admin_client.admin_client')

@section('content')
@php
    $clientName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
    $thaiDate = static function ($value): string {
        if (!$value) return '-';
        try {
            $date = $value instanceof \Carbon\CarbonInterface ? $value : \Carbon\Carbon::parse($value, 'Asia/Bangkok');
            return $date->format('d/m/') . ($date->year + 543);
        } catch (\Throwable $e) { return '-'; }
    };
    $statusLabels = [
        'draft' => 'ร่างแผน', 'active' => 'กำลังดำเนินการ', 'review' => 'อยู่ระหว่างทบทวน',
        'completed' => 'สำเร็จ', 'cancelled' => 'ยุติ',
    ];
    $statusClasses = [
        'draft' => 'idp-status-draft', 'active' => 'idp-status-active', 'review' => 'idp-status-review',
        'completed' => 'idp-status-completed', 'cancelled' => 'idp-status-cancelled',
    ];
    $domainIcons = [
        'physical' => 'bi-heart-pulse', 'emotional' => 'bi-emoji-smile',
        'social' => 'bi-people', 'intellectual' => 'bi-lightbulb',
    ];
@endphp

<style>
.idp-page{--idp-border:#e4eaf2;--idp-text:#1f2f46;--idp-muted:#6b7a90;padding-bottom:1rem}
.idp-page .idp-header,.idp-page .idp-card,.idp-page .idp-empty{background:#fff;border:1px solid var(--idp-border);border-radius:16px;box-shadow:0 6px 20px rgba(31,47,70,.045)}
.idp-page .idp-header{padding:1.05rem 1.2rem;margin-bottom:1rem;overflow:hidden}
.idp-page .idp-header-row{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;min-width:0}
.idp-page .idp-header-main{min-width:0;flex:1 1 auto}
.idp-page .idp-header-actions{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:.5rem;flex:0 1 auto;max-width:100%}
.idp-page .idp-title{margin:0;color:var(--idp-text);font-size:1.15rem;font-weight:800}
.idp-page .idp-subtitle{margin:.28rem 0 0;color:var(--idp-muted);font-size:.87rem;line-height:1.55}
.idp-page .idp-meta{display:flex;flex-wrap:wrap;gap:.45rem;margin-top:.72rem}
.idp-page .idp-pill{display:inline-flex;align-items:center;gap:.35rem;padding:.34rem .68rem;border:1px solid #dbe5f0;border-radius:999px;background:#f8fbff;color:#41546d;font-size:.8rem}
.idp-page .idp-btn{display:inline-flex;align-items:center;justify-content:center;gap:.4rem;min-height:39px;padding:.48rem .82rem;border-radius:10px;font-size:.82rem;font-weight:700;text-decoration:none!important;white-space:nowrap;max-width:100%}
.idp-page .idp-btn-primary{border:0;background:linear-gradient(135deg,#3577bd 0%,#245f9f 100%);color:#fff!important;box-shadow:0 6px 14px rgba(36,95,159,.17)}
.idp-page .idp-btn-light{border:1px solid #d7e0e9;background:#fff;color:#4f6075!important}
.idp-page .idp-btn-outline{border:1px solid #9cbce0;background:#f7fbff;color:#2e639e!important}
.idp-page .idp-card{overflow:hidden}
.idp-page .idp-card-head{display:flex;align-items:center;justify-content:space-between;gap:.8rem;padding:.9rem 1rem;border-bottom:1px solid var(--idp-border);background:#fbfcfe}
.idp-page .idp-card-title{margin:0;color:var(--idp-text);font-size:.94rem;font-weight:800}
.idp-page .idp-card-body{padding:1rem}
.idp-page .idp-status{display:inline-flex;align-items:center;min-height:28px;padding:.28rem .62rem;border-radius:999px;font-size:.76rem;font-weight:700;white-space:nowrap}
.idp-page .idp-status-active{background:#ecfdf3;color:#16794b}.idp-page .idp-status-review{background:#fff8e6;color:#92620a}.idp-page .idp-status-completed{background:#eef6ff;color:#245f9f}.idp-page .idp-status-draft{background:#fff8e6;color:#92620a}.idp-page .idp-status-cancelled{background:#f4f5f7;color:#68717d}
.idp-page .idp-next{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem;border:1px solid #d6e6f7;border-radius:14px;background:#f7fbff;margin-bottom:1rem}
.idp-page .idp-next-title{font-size:.91rem;font-weight:800;color:#264f7e}.idp-page .idp-next-text{margin-top:.25rem;color:#61748b;font-size:.8rem;line-height:1.5}
.idp-page .idp-plan-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.8rem 1rem}
.idp-page .idp-wide{grid-column:1/-1}.idp-page .idp-label{font-size:.75rem;font-weight:700;color:var(--idp-muted);margin-bottom:.18rem}.idp-page .idp-value{font-size:.86rem;line-height:1.6;color:#35475e;white-space:pre-line}
.idp-page .idp-stat-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.7rem}.idp-page .idp-stat{padding:.78rem;border:1px solid #e6ebf2;border-radius:12px;background:#fafcff}.idp-page .idp-stat-label{font-size:.76rem;color:var(--idp-muted)}.idp-page .idp-stat-value{margin-top:.14rem;font-size:1.12rem;font-weight:800;color:var(--idp-text)}
.idp-page .idp-domain-card{height:100%;min-height:154px;padding:1rem;border:1px solid var(--idp-border);border-radius:14px;background:#fff}.idp-page .idp-domain-icon{display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:11px;background:#eff6ff;color:#376fae;margin-bottom:.65rem}.idp-page .idp-domain-name{font-size:.9rem;font-weight:800;color:var(--idp-text)}.idp-page .idp-domain-score{margin-top:.28rem;font-size:1.45rem;font-weight:800;color:#245f9f}.idp-page .idp-domain-level{margin-top:.15rem;font-size:.76rem;color:var(--idp-muted)}.idp-page .idp-domain-note{margin-top:.32rem;font-size:.73rem;color:#8390a0}
.idp-page .idp-empty{padding:2.2rem 1.2rem;text-align:center}.idp-page .idp-empty-icon{display:inline-flex;align-items:center;justify-content:center;width:64px;height:64px;margin-bottom:.8rem;border-radius:50%;background:#eef5ff;color:#4b77ad;font-size:1.7rem}.idp-page .idp-empty-title{margin:0;color:var(--idp-text);font-size:1rem;font-weight:800}.idp-page .idp-empty-text{max-width:650px;margin:.4rem auto 1rem;color:var(--idp-muted);font-size:.86rem;line-height:1.65}
.idp-page .idp-table-wrap{width:100%;overflow-x:auto}.idp-page table{width:100%;min-width:760px;border-collapse:separate;border-spacing:0}.idp-page th,.idp-page td{padding:.7rem .75rem;border-bottom:1px solid #edf1f5;font-size:.8rem;color:#3d4c60;vertical-align:middle}.idp-page th{background:#f8fafc;color:#516177;font-weight:700;white-space:nowrap}.idp-page .idp-goal-badge{display:inline-flex;align-items:center;padding:.25rem .55rem;border-radius:999px;background:#eef6ff;color:#2f679e;font-size:.72rem;font-weight:800}.idp-page .idp-goal-target{font-weight:800;color:#245f9f}.idp-page .idp-next-goal{background:#f9fbff;border-color:#dce7f3}.idp-page .idp-timeline{position:relative;padding-left:1.35rem}.idp-page .idp-timeline:before{content:"";position:absolute;left:.42rem;top:.35rem;bottom:.35rem;width:2px;background:#d8e5f2}.idp-page .idp-time-item{position:relative;padding:0 0 .9rem .55rem}.idp-page .idp-time-item:last-child{padding-bottom:0}.idp-page .idp-time-dot{position:absolute;left:-1.04rem;top:.2rem;width:12px;height:12px;border-radius:50%;background:#3a7fc0;border:2px solid #eaf4ff}.idp-page .idp-time-head{display:flex;justify-content:space-between;gap:.7rem;align-items:center;flex-wrap:wrap}.idp-page .idp-time-title{font-size:.83rem;font-weight:800;color:#2f4b68}.idp-page .idp-time-meta{font-size:.73rem;color:#7a899a}.idp-page .idp-time-text{margin-top:.28rem;font-size:.78rem;line-height:1.5;color:#53667b;white-space:pre-line}.idp-page .idp-next-action{margin-top:.38rem;padding:.48rem .58rem;border-radius:9px;background:#f6faff;border:1px solid #d9e8f7;font-size:.76rem;color:#46617b}.idp-page .idp-followup-head-actions{display:flex;gap:.5rem;flex-wrap:wrap}
@media(max-width:991.98px){.idp-page .idp-header-row{flex-direction:column}.idp-page .idp-header-actions{justify-content:flex-start;width:100%}.idp-page .idp-next{align-items:flex-start;flex-direction:column}}
@media(max-width:575.98px){.idp-page .idp-plan-grid,.idp-page .idp-stat-grid{grid-template-columns:1fr}.idp-page .idp-header-actions{flex-direction:column}.idp-page .idp-header-actions .idp-btn,.idp-page .idp-next .idp-btn{width:100%}}

/* IDP_PHASE6_SAFE_SCOPE */
.idp-page{width:100%;min-width:0}
.idp-page *{min-width:0}
.idp-page .idp-value,.idp-page td,.idp-page .idp-time-text,.idp-page .idp-next-action{overflow-wrap:anywhere;word-break:break-word}
.idp-page .idp-card-head>div:first-child{min-width:0}
@media(max-width:767.98px){
  .idp-page .idp-card-head{align-items:flex-start;flex-wrap:wrap}
  .idp-page .idp-card-head>.idp-btn{width:100%}
  .idp-page .idp-time-head{align-items:flex-start}
  .idp-page .idp-time-head .idp-btn{width:100%}
}
</style>

<div class="container-fluid px-2 px-lg-3 idp-page">
    <div class="idp-header">
        <div class="idp-header-row">
            <div class="idp-header-main">
                <h4 class="idp-title"><i class="bi bi-person-up me-2 text-primary"></i>พัฒนาและติดตามรายบุคคล</h4>
                <p class="idp-subtitle">ประเมินพัฒนาการ วางแผน เป้าหมาย กิจกรรม และติดตามผลของผู้รับบริการอย่างต่อเนื่อง</p>
                <div class="idp-meta">
                    <span class="idp-pill"><i class="bi bi-person"></i>ผู้รับบริการ: <strong>{{ $clientName }}</strong></span>
                    <span class="idp-pill"><i class="bi bi-calendar3"></i>อายุ: <strong>{{ $ageText }}</strong></span>
                    @if(!empty($client->house?->house_name))<span class="idp-pill"><i class="bi bi-house"></i>บ้าน: <strong>{{ $client->house->house_name }}</strong></span>@endif
                </div>
            </div>
            <div class="idp-header-actions">
                @if($canCreateFollowup)
                    <a href="{{ route('individual-development.followups.create', $client->id) }}" class="idp-btn idp-btn-primary"><i class="bi bi-activity"></i>บันทึกติดตาม</a>
                @endif
                @if($currentPlan && $canPrintReport)
                    <a href="{{ route('individual-development.report.show', $client->id) }}" class="idp-btn idp-btn-outline"><i class="bi bi-printer"></i>รายงาน A4</a>
                @endif
                @if($baselineAssessment)
                    <a href="{{ route('individual-development.goals.index', $client->id) }}" class="idp-btn idp-btn-outline"><i class="bi bi-bullseye"></i>เป้าหมาย/กิจกรรม</a>
                    <a href="{{ route('individual-development.baseline.show', $client->id) }}" class="idp-btn idp-btn-outline"><i class="bi bi-clipboard2-check"></i>ดูผล Baseline</a>
                    @if($canUpdateBaseline)<a href="{{ route('individual-development.baseline.edit', $client->id) }}" class="idp-btn idp-btn-light"><i class="bi bi-pencil-square"></i>แก้ไข Baseline</a>@endif
                @endif
                @if($canCreatePlan)<a href="{{ route('individual-development.create', $client->id) }}" class="idp-btn idp-btn-primary"><i class="bi bi-plus-circle"></i>สร้างแผนพัฒนา</a>@endif
            </div>
        </div>
    </div>

    @if(!$currentPlan)
        <div class="idp-empty">
            <span class="idp-empty-icon"><i class="bi bi-person-lines-fill"></i></span>
            <h5 class="idp-empty-title">ยังไม่มีแผนพัฒนารายบุคคล</h5>
            <p class="idp-empty-text">เริ่มต้นด้วยการสร้างแผนพัฒนารายบุคคล จากนั้นระบบจะนำไปสู่การประเมินระดับเริ่มต้น (Baseline) 4 ด้าน การกำหนดเป้าหมาย และการติดตามผลในรอบถัดไป</p>
            @if($canCreatePlan)<a href="{{ route('individual-development.create', $client->id) }}" class="idp-btn idp-btn-primary"><i class="bi bi-plus-circle"></i>สร้างแผนแรก</a>@endif
        </div>
    @else
        @if(!$baselineAssessment)
            <div class="idp-next">
                <div>
                    <div class="idp-next-title"><i class="bi bi-1-circle me-1"></i>ขั้นตอนถัดไป: ประเมินระดับเริ่มต้น (Baseline) 4 ด้าน</div>
                    <div class="idp-next-text">ประเมิน 20 ตัวชี้วัดด้วย Rubric ระดับ 1–5 เพื่อใช้เป็นค่าตั้งต้นสำหรับเปรียบเทียบพัฒนาการในรอบติดตามต่อไป</div>
                </div>
                @if($canCreateBaseline)<a href="{{ route('individual-development.baseline.create', $client->id) }}" class="idp-btn idp-btn-primary"><i class="bi bi-clipboard2-pulse"></i>เริ่มประเมิน Baseline</a>@endif
            </div>
        @endif

        @if($baselineAssessment && $goalStats['total'] === 0)
            <div class="idp-next idp-next-goal">
                <div>
                    <div class="idp-next-title"><i class="bi bi-2-circle me-1"></i>ขั้นตอนถัดไป: กำหนดเป้าหมายและแผนกิจกรรมรายบุคคล</div>
                    <div class="idp-next-text">นำผล Baseline มากำหนดเป้าหมายรายด้าน ระดับเป้าหมาย ตัวชี้วัดความสำเร็จ ผู้รับผิดชอบ และกิจกรรมที่ต้องดำเนินการ</div>
                </div>
                @if($canCreateGoal)<a href="{{ route('individual-development.goals.create', $client->id) }}" class="idp-btn idp-btn-primary"><i class="bi bi-bullseye"></i>สร้างเป้าหมายแรก</a>@endif
            </div>
        @endif

        @if($baselineAssessment && $goalStats['total'] > 0 && !$latestFollowup)
            <div class="idp-next">
                <div>
                    <div class="idp-next-title"><i class="bi bi-3-circle me-1"></i>ขั้นตอนถัดไป: บันทึกการติดตามครั้งที่ 1</div>
                    <div class="idp-next-text">ระบบจะนำคะแนน Baseline ทั้ง 20 ตัวชี้วัดมาเป็นค่าครั้งก่อนโดยอัตโนมัติ คุณปรับเฉพาะสิ่งที่เปลี่ยนแปลง พร้อมบันทึกผล สิ่งที่ดำเนินการ และสิ่งที่ต้องทำต่อ</div>
                </div>
                @if($canCreateFollowup)<a href="{{ route('individual-development.followups.create', $client->id) }}" class="idp-btn idp-btn-primary"><i class="bi bi-activity"></i>เริ่มติดตามครั้งที่ 1</a>@endif
            </div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-12 col-xl-8">
                <div class="idp-card h-100">
                    <div class="idp-card-head">
                        <h5 class="idp-card-title">แผนปัจจุบัน ครั้งที่ {{ $currentPlan->plan_no }}</h5>
                        <span class="idp-status {{ $statusClasses[$currentPlan->status] ?? 'idp-status-draft' }}">{{ $statusLabels[$currentPlan->status] ?? $currentPlan->status }}</span>
                    </div>
                    <div class="idp-card-body">
                        <div class="idp-plan-grid">
                            <div><div class="idp-label">วันที่เริ่มแผน</div><div class="idp-value">{{ $thaiDate($currentPlan->start_date) }}</div></div>
                            <div><div class="idp-label">วันที่คาดว่าจะสิ้นสุด</div><div class="idp-value">{{ $thaiDate($currentPlan->end_date) }}</div></div>
                            <div class="idp-wide"><div class="idp-label">เป้าหมายภาพรวม</div><div class="idp-value">{{ $currentPlan->overall_goal ?: '-' }}</div></div>
                            <div><div class="idp-label">จุดแข็ง</div><div class="idp-value">{{ $currentPlan->strength_summary ?: 'รอประเมิน/เพิ่มเติมข้อมูล' }}</div></div>
                            <div><div class="idp-label">ประเด็นที่ควรพัฒนา</div><div class="idp-value">{{ $currentPlan->development_need_summary ?: 'รอประเมิน/เพิ่มเติมข้อมูล' }}</div></div>
                            @if($baselineAssessment)
                                <div><div class="idp-label">ความต้องการของผู้รับบริการ</div><div class="idp-value">{{ $currentPlan->client_need_summary ?: '-' }}</div></div>
                                <div><div class="idp-label">ปัจจัยคุ้มครอง</div><div class="idp-value">{{ $currentPlan->protective_factor_summary ?: '-' }}</div></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="idp-card h-100">
                    <div class="idp-card-head"><h5 class="idp-card-title">สถานะการดำเนินงาน</h5></div>
                    <div class="idp-card-body">
                        <div class="idp-stat-grid">
                            <div class="idp-stat"><div class="idp-stat-label">Baseline</div><div class="idp-stat-value" style="font-size:.92rem">{{ $baselineAssessment ? 'ประเมินแล้ว' : 'รอประเมิน' }}</div></div>
                            <div class="idp-stat"><div class="idp-stat-label">เป้าหมายทั้งหมด</div><div class="idp-stat-value">{{ $goalStats['total'] }}</div></div>
                            <div class="idp-stat"><div class="idp-stat-label">บรรลุแล้ว</div><div class="idp-stat-value">{{ $goalStats['achieved'] }}</div></div>
                            <div class="idp-stat"><div class="idp-stat-label">กำลังดำเนินการ</div><div class="idp-stat-value">{{ $goalStats['in_progress'] + $goalStats['partial'] }}</div></div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="idp-label">วันที่ประเมิน Baseline</div><div class="idp-value mb-2">{{ $baselineAssessment ? $thaiDate($baselineAssessment->assessment_date) : '-' }}</div>
                            <div class="idp-label">ติดตามล่าสุด</div><div class="idp-value">{{ $latestFollowup ? 'ครั้งที่ '.$latestFollowup->followup_no.' • '.$thaiDate($latestFollowup->followup_date) : '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="idp-card mb-3">
            <div class="idp-card-head">
                <div><h5 class="idp-card-title">ระดับพัฒนาการ 4 ด้าน</h5><div class="small text-muted mt-1">{{ $latestEvaluationLabel }}</div></div>
                @if($baselineAssessment)<a href="{{ route('individual-development.baseline.show', $client->id) }}" class="idp-btn idp-btn-light"><i class="bi bi-eye"></i>ดูรายละเอียด</a>@endif
            </div>
            <div class="idp-card-body">
                <div class="row g-3">
                    @foreach($domainScores as $domain)
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="idp-domain-card">
                                <span class="idp-domain-icon"><i class="bi {{ $domainIcons[$domain['code']] ?? 'bi-clipboard-data' }}"></i></span>
                                <div class="idp-domain-name">{{ $domain['name'] }}</div>
                                <div class="idp-domain-score">{{ $domain['score'] !== null ? number_format($domain['score'], 2) : '-' }} <small class="fs-6 text-muted">/ 5</small></div>
                                <div class="idp-domain-level">{{ $domain['level'] ? 'ระดับ '.$domain['level'].' • '.$domain['level_label'] : $domain['level_label'] }}</div>
                                <div class="idp-domain-note">{{ $domain['indicator_count'] }} ตัวชี้วัด</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($baselineAssessment)
            <div class="idp-card mb-3">
                <div class="idp-card-head">
                    <div><h5 class="idp-card-title">เป้าหมายการพัฒนาและกิจกรรม</h5><div class="small text-muted mt-1">เชื่อมผล Baseline กับเป้าหมายที่วัดผลได้และกิจกรรมรายบุคคล</div></div>
                    <a href="{{ route('individual-development.goals.index', $client->id) }}" class="idp-btn idp-btn-light"><i class="bi bi-list-check"></i>จัดการเป้าหมาย</a>
                </div>
                @if($currentPlan->goals->isEmpty())
                    <div class="idp-card-body text-center text-muted py-4">ยังไม่มีเป้าหมายการพัฒนา</div>
                @else
                    <div class="idp-table-wrap">
                        <table>
                            <thead><tr><th style="width:150px">ด้าน</th><th>เป้าหมาย</th><th style="width:130px">ระดับ</th><th style="width:130px">กำหนดสำเร็จ</th><th style="width:150px">สถานะ</th></tr></thead>
                            <tbody>
                                @foreach($currentPlan->goals->take(6) as $goal)
                                    <tr>
                                        <td><span class="idp-goal-badge">{{ $goal->domain?->name ?? '-' }}</span></td>
                                        <td>{{ \Illuminate\Support\Str::limit($goal->title, 100) }}</td>
                                        <td><span class="idp-goal-target">{{ $goal->baseline_level ?? '-' }} → {{ $goal->target_level ?? '-' }}</span></td>
                                        <td>{{ $thaiDate($goal->target_date) }}</td>
                                        <td>{{ ['not_started'=>'ยังไม่เริ่ม','in_progress'=>'กำลังดำเนินการ','partial'=>'มีความก้าวหน้า','achieved'=>'บรรลุเป้าหมาย','cancelled'=>'ยุติเป้าหมาย'][$goal->status] ?? $goal->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

        @if($baselineAssessment)
            <div class="idp-card mb-3">
                <div class="idp-card-head">
                    <div><h5 class="idp-card-title">ประวัติการติดตามรายบุคคล</h5><div class="small text-muted mt-1">อ่านต่อเนื่องได้จากครั้งล่าสุด และเห็นสิ่งที่ต้องทำต่อโดยไม่ต้องเปิดทุกฟอร์ม</div></div>
                    <div class="idp-followup-head-actions">@if($canCreateFollowup)<a href="{{ route('individual-development.followups.create',$client->id) }}" class="idp-btn idp-btn-primary"><i class="bi bi-plus-circle"></i>เพิ่มการติดตาม</a>@endif</div>
                </div>
                <div class="idp-card-body">
                    @if($currentPlan->followups->isEmpty())
                        <div class="text-center text-muted py-3">ยังไม่มีบันทึกการติดตาม</div>
                    @else
                        <div class="idp-timeline">
                            @foreach($currentPlan->followups->sortByDesc('followup_no') as $fu)
                                <div class="idp-time-item">
                                    <span class="idp-time-dot"></span>
                                    <div class="idp-time-head">
                                        <div><div class="idp-time-title">ติดตามครั้งที่ {{ $fu->followup_no }} • {{ $thaiDate($fu->followup_date) }}</div><div class="idp-time-meta">ผลโดยรวม: {{ ['improved'=>'ดีขึ้น','stable'=>'คงเดิม','declined'=>'ถดถอย','achieved'=>'บรรลุเป้าหมาย'][$fu->overall_result] ?? ($fu->overall_result ?: '-') }}</div></div>
                                        <a href="{{ route('individual-development.followups.show',[$client->id,$fu->id]) }}" class="idp-btn idp-btn-light"><i class="bi bi-eye"></i>ดูรายละเอียด</a>
                                    </div>
                                    @if($fu->positive_changes || $fu->result)<div class="idp-time-text">{{ $fu->positive_changes ?: $fu->result }}</div>@endif
                                    @if($fu->next_action)<div class="idp-next-action"><strong>สิ่งที่ต้องทำต่อ:</strong> {{ $fu->next_action }} @if($fu->next_followup_date)<span class="ms-1 text-muted">• นัด {{ $thaiDate($fu->next_followup_date) }}</span>@endif</div>@endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="idp-card">
            <div class="idp-card-head"><h5 class="idp-card-title">ประวัติแผนพัฒนารายบุคคล</h5><span class="small text-muted">ทั้งหมด {{ $plans->count() }} แผน</span></div>
            <div class="idp-table-wrap">
                <table>
                    <thead><tr><th class="text-center" style="width:80px">ครั้งที่</th><th style="width:130px">เริ่มแผน</th><th style="width:130px">สิ้นสุด</th><th>เป้าหมายภาพรวม</th><th style="width:150px">สถานะ</th></tr></thead>
                    <tbody>
                        @foreach($plans as $plan)
                            <tr>
                                <td class="text-center fw-bold">{{ $plan->plan_no }}</td>
                                <td>{{ $thaiDate($plan->start_date) }}</td>
                                <td>{{ $thaiDate($plan->end_date) }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($plan->overall_goal ?: '-', 120) }}</td>
                                <td><span class="idp-status {{ $statusClasses[$plan->status] ?? 'idp-status-draft' }}">{{ $statusLabels[$plan->status] ?? $plan->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@if(session('success') || session('warning'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.Swal) return;
    Swal.fire({icon:@json(session('success') ? 'success' : 'warning'),title:@json(session('success') ? 'สำเร็จ' : 'แจ้งเตือน'),text:@json(session('success') ?? session('warning')),confirmButtonText:'OK',timer:3000,timerProgressBar:true});
});
</script>
@endif
@endsection
