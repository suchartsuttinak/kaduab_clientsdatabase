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
    $statusLabels = ['draft'=>'ร่างแผน','active'=>'กำลังดำเนินการ','review'=>'อยู่ระหว่างทบทวน','completed'=>'ปิดแผนแล้ว','cancelled'=>'ยุติแผน'];
    $goalStatusLabels = ['not_started'=>'ยังไม่เริ่ม','in_progress'=>'กำลังดำเนินการ','partial'=>'มีความก้าวหน้า','achieved'=>'บรรลุแล้ว','cancelled'=>'ยกเลิก'];
    $domainIcons = ['physical'=>'bi-heart-pulse','emotional'=>'bi-emoji-smile','social'=>'bi-people','intellectual'=>'bi-lightbulb'];
    $isSelectedActive = $currentPlan?->status === 'active';
    $unconfirmedReached = $currentPlan ? $currentPlan->goals->filter(fn($g) => ($goalProgress[$g->id]['needs_confirmation'] ?? false))->count() : 0;
    $goalsWithoutActivity = $currentPlan ? $currentPlan->goals->filter(fn($g) => !in_array($g->status,['achieved','cancelled'],true) && $g->activities->isEmpty())->count() : 0;
    $strengthFields = ['ability'=>'ความสามารถ/ความถนัด','interest'=>'ความสนใจ','hobby'=>'งานอดิเรก','talent'=>'ความสามารถพิเศษ','does_well'=>'สิ่งที่เด็กทำได้ดี','dream'=>'ความฝัน','career_interest'=>'อาชีพที่สนใจ','trusted_person'=>'บุคคลที่เด็กไว้วางใจ','motivation'=>'แรงจูงใจ','skills_to_promote'=>'ทักษะที่ควรส่งเสริม'];
    $needCategories = ['education'=>'การศึกษา','health'=>'สุขภาพ','mental'=>'จิตใจและอารมณ์','behavior'=>'พฤติกรรม','family'=>'ครอบครัว','social'=>'สังคม','life_skill'=>'ทักษะชีวิต','career'=>'อาชีพ','housing'=>'ที่อยู่อาศัย','rights'=>'เอกสารและสิทธิ','other'=>'อื่น ๆ'];
    $priorityLabels = ['low'=>'ต่ำ','medium'=>'ปานกลาง','high'=>'สูง','urgent'=>'เร่งด่วน'];
    $hasPlanningProfile = $currentPlan ? (
        collect($currentPlan->strength_profile ?? [])->filter(fn($v)=>filled($v))->isNotEmpty()
        || collect($currentPlan->needs_profile ?? [])->filter(fn($v)=>is_array($v) && collect($v)->filter(fn($x)=>filled($x))->isNotEmpty())->isNotEmpty()
        || filled($currentPlan->strength_summary)
        || filled($currentPlan->development_need_summary)
        || filled($currentPlan->client_need_summary)
        || filled($currentPlan->caregiver_need_summary)
    ) : false;
    $hasFinalOutcome = $currentPlan ? $currentPlan->assessments->contains('assessment_type','final') : false;
    $idpUser = auth()->user();
    $canViewDevelopmentCenter = (bool) (
        $idpUser
        && (
            (method_exists($idpUser, 'isAdmin') && $idpUser->isAdmin())
            || (method_exists($idpUser, 'hasFormPermission') && $idpUser->hasFormPermission('individual_development_center', 'view'))
        )
    );
@endphp

<style>
.idp-page{--b:#e2e8f0;--text:#1e293b;--muted:#64748b;--blue:#2563a9;padding-bottom:1.2rem}.idp-page *{min-width:0}.idp-box{background:#fff;border:1px solid var(--b);border-radius:16px;box-shadow:0 5px 20px rgba(30,41,59,.045)}.idp-head{padding:1rem 1.1rem;margin-bottom:1rem}.idp-head-row,.idp-card-head,.idp-next{display:flex;align-items:flex-start;justify-content:space-between;gap:.8rem}.idp-title{margin:0;font-size:1.15rem;font-weight:800;color:var(--text)}.idp-sub{margin:.25rem 0 0;font-size:.82rem;color:var(--muted);line-height:1.55}.idp-meta,.idp-actions{display:flex;flex-wrap:wrap;gap:.45rem}.idp-meta{margin-top:.65rem}.idp-pill{display:inline-flex;align-items:center;gap:.3rem;padding:.32rem .65rem;border:1px solid #dbe4ee;border-radius:999px;background:#f8fbff;font-size:.77rem;color:#475569}.idp-btn{display:inline-flex;align-items:center;justify-content:center;gap:.35rem;min-height:38px;padding:.45rem .75rem;border-radius:10px;font-size:.78rem;font-weight:700;text-decoration:none!important;white-space:nowrap}.idp-primary{border:0;background:linear-gradient(135deg,#3b82c4,#2563a9);color:#fff!important}.idp-light{border:1px solid #d4dee9;background:#fff;color:#475569!important}.idp-outline{border:1px solid #9abce2;background:#f8fbff;color:#285f9e!important}.idp-danger{border:1px solid #fecaca;background:#fff7f7;color:#b91c1c!important}.idp-warning{border:1px solid #f3d39d;background:#fffaf0;color:#945c09!important}.idp-card{margin-bottom:1rem;overflow:hidden}.idp-card-head{align-items:center;padding:.85rem 1rem;border-bottom:1px solid var(--b);background:#fbfcfe}.idp-card-title{margin:0;font-size:.92rem;font-weight:800;color:var(--text)}.idp-body{padding:1rem}.idp-next{align-items:center;padding:.85rem 1rem;margin-bottom:1rem;border:1px solid #cfe0f2;border-radius:14px;background:#f7fbff}.idp-next strong{color:#245b91;font-size:.88rem}.idp-next p{margin:.2rem 0 0;font-size:.78rem;color:#60748a;line-height:1.5}.idp-grid2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.8rem 1rem}.idp-label{font-size:.72rem;color:var(--muted);font-weight:700;margin-bottom:.15rem}.idp-value{font-size:.82rem;color:#334155;line-height:1.55;white-space:pre-line;overflow-wrap:anywhere}.idp-status{display:inline-flex;align-items:center;padding:.25rem .55rem;border-radius:999px;font-size:.71rem;font-weight:800}.st-active{background:#ecfdf3;color:#15803d}.st-completed{background:#eff6ff;color:#1d4f91}.st-cancelled{background:#f1f5f9;color:#64748b}.st-review,.st-draft{background:#fff7ed;color:#9a5b0a}.idp-stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.6rem}.idp-stat{padding:.72rem;border:1px solid #e5ebf2;border-radius:12px;background:#fafcff}.idp-stat span{font-size:.72rem;color:var(--muted)}.idp-stat b{display:block;margin-top:.1rem;font-size:1.05rem;color:var(--text)}.idp-domains{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.8rem}.idp-domain{padding:.9rem;border:1px solid var(--b);border-radius:13px}.idp-domain-icon{display:inline-flex;width:34px;height:34px;align-items:center;justify-content:center;border-radius:10px;background:#eef6ff;color:#2f6da8}.idp-domain-name{margin-top:.5rem;font-size:.82rem;font-weight:800}.idp-domain-score{margin-top:.25rem;font-size:1.15rem;font-weight:800;color:#245f9f}.idp-delta{font-size:.72rem;font-weight:800}.up{color:#15803d}.down{color:#b91c1c}.same{color:#64748b}.idp-table{width:100%;overflow-x:auto}.idp-table table{width:100%;min-width:850px;border-collapse:collapse}.idp-table th,.idp-table td{padding:.68rem .72rem;border-bottom:1px solid #edf1f5;font-size:.77rem;vertical-align:middle;color:#3f4e63}.idp-table th{background:#f8fafc;color:#55657a;white-space:nowrap}.idp-progress{min-width:150px}.idp-bar{height:6px;background:#e8eef5;border-radius:999px;overflow:hidden;margin-top:.25rem}.idp-bar span{display:block;height:100%;background:#4b8bc6}.idp-reached{display:inline-flex;padding:.2rem .45rem;border-radius:999px;background:#ecfdf3;color:#15803d;font-size:.69rem;font-weight:800}.idp-pending{display:inline-flex;padding:.2rem .45rem;border-radius:999px;background:#fff7ed;color:#9a5b0a;font-size:.69rem;font-weight:800}.idp-timeline{position:relative;padding-left:1.2rem}.idp-timeline:before{content:"";position:absolute;left:.35rem;top:.25rem;bottom:.25rem;width:2px;background:#dbe7f3}.idp-time{position:relative;padding:0 0 .85rem .45rem}.idp-time:before{content:"";position:absolute;left:-.93rem;top:.22rem;width:11px;height:11px;border-radius:50%;background:#3d7fbc;border:2px solid #eaf4ff}.idp-time-title{font-size:.8rem;font-weight:800;color:#315271}.idp-time-text{margin-top:.2rem;font-size:.75rem;color:#607084;line-height:1.5;overflow-wrap:anywhere}.idp-final{border-left:4px solid #2d7a57;background:#f3fbf6}.idp-history-selected{background:#f4f9ff}.idp-empty{text-align:center;padding:2rem;color:var(--muted)}.idp-profile-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}.idp-profile-panel{border:1px solid #e2e8f0;border-radius:14px;padding:.95rem;background:#fff}.idp-profile-title{font-size:.86rem;font-weight:800;color:#274b70;margin-bottom:.75rem}.idp-profile-item{padding:.55rem 0;border-bottom:1px dashed #e5eaf0}.idp-profile-item:last-child{border-bottom:0}.idp-profile-item-label{font-size:.7rem;color:#64748b;font-weight:700;margin-bottom:.15rem}.idp-profile-item-value{font-size:.8rem;color:#334155;line-height:1.5;white-space:pre-line;overflow-wrap:anywhere}.idp-need{padding:.7rem;border:1px solid #e5eaf0;border-radius:12px;margin-bottom:.6rem;background:#fbfdff}.idp-need:last-child{margin-bottom:0}.idp-priority{display:inline-flex;padding:.18rem .45rem;border-radius:999px;font-size:.68rem;font-weight:800;background:#f1f5f9;color:#475569}.idp-priority-high,.idp-priority-urgent{background:#fff1f2;color:#b42318}.idp-priority-medium{background:#fff7ed;color:#9a5b0a}.idp-history-note{padding:.75rem .9rem;border:1px solid #e2e8f0;border-radius:12px;background:#f8fafc;color:#64748b;font-size:.76rem}.idp-blockers{margin:.45rem 0 0;padding-left:1.15rem;color:#7a5a18;font-size:.75rem}
@media(max-width:1199px){.idp-domains{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:991px){.idp-profile-grid{grid-template-columns:1fr}.idp-head-row,.idp-next{flex-direction:column}.idp-actions{justify-content:flex-start}}@media(max-width:575px){.idp-grid2,.idp-stats,.idp-domains{grid-template-columns:1fr}.idp-actions{flex-direction:column;width:100%}.idp-actions .idp-btn,.idp-next .idp-btn{width:100%}}
</style>

<div class="container-fluid px-2 px-lg-3 idp-page">
<div class="d-flex justify-content-end gap-2 mb-2">
    @if($canViewDevelopmentCenter && Route::has('individual-development.center'))
        <a href="{{ route('individual-development.center') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-grid me-1"></i>ศูนย์กลางเด็กทุกบ้าน</a>
    @endif
    <a href="{{ route('individual-development.timeline',$client->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-clock-history me-1"></i>Timeline</a>
</div>
    <div class="idp-box idp-head">
        <div class="idp-head-row">
            <div>
                <h4 class="idp-title"><i class="bi bi-person-up me-2 text-primary"></i>พัฒนาและติดตามรายบุคคล</h4>
                <p class="idp-sub">วงจรการทำงาน: แผน → Baseline → เป้าหมาย → กิจกรรม → ติดตาม → ยืนยันผล → ปิดแผน</p>
                <div class="idp-meta">
                    <span class="idp-pill"><i class="bi bi-person"></i>{{ $clientName }}</span>
                    <span class="idp-pill"><i class="bi bi-calendar3"></i>อายุ {{ $ageText }}</span>
                    @if($currentPlan)<span class="idp-pill"><i class="bi bi-journal-check"></i>แผนครั้งที่ {{ $currentPlan->plan_no }}</span>@endif
                </div>
            </div>
            <div class="idp-actions">
                @if($canCreateFollowup)<a class="idp-btn idp-primary" href="{{ route('individual-development.followups.create',$client->id) }}"><i class="bi bi-activity"></i>บันทึกติดตาม</a>@endif
                @if($currentPlan)<a class="idp-btn idp-outline" href="{{ route('individual-development.outcomes.index',['client'=>$client->id,'plan'=>$currentPlan->id]) }}"><i class="bi bi-graph-up-arrow"></i>ผลลัพธ์</a>@endif
                @if($currentPlan && $canPrintReport)<a class="idp-btn idp-outline" href="{{ route('individual-development.report.hub',['client'=>$client->id,'plan'=>$currentPlan->id]) }}"><i class="bi bi-file-earmark-text"></i>รายงาน</a>@endif
                @if($isSelectedActive && $baselineAssessment)<a class="idp-btn idp-light" href="{{ route('individual-development.goals.index',$client->id) }}"><i class="bi bi-list-check"></i>จัดการเป้าหมาย</a>@endif
                @if($activePlan && $currentPlan && $activePlan->id !== $currentPlan->id)<a class="idp-btn idp-light" href="{{ route('individual-development.index',$client->id) }}"><i class="bi bi-arrow-return-left"></i>กลับแผนปัจจุบัน</a>@endif
            </div>
        </div>
    </div>

    @if($currentPlan)
        <div class="idp-box idp-card">
            <div class="idp-card-head">
                <div>
                    <h5 class="idp-card-title"><i class="bi bi-stars me-2 text-primary"></i>จุดแข็ง ศักยภาพ และความต้องการ</h5>
                    <div class="small text-muted mt-1">ใช้ข้อมูลด้านบวกและเสียงของเด็กเป็นฐานในการกำหนดเป้าหมาย ไม่บันทึกปัญหาเพียงด้านเดียว</div>
                </div>
                @if($canEditProfile)
                    <button type="button" class="idp-btn idp-outline" data-bs-toggle="modal" data-bs-target="#idpProfileModal"><i class="bi bi-pencil-square"></i>แก้ไขข้อมูล</button>
                @elseif($readOnly)
                    <span class="idp-status st-completed"><i class="bi bi-eye me-1"></i>อ่านอย่างเดียว</span>
                @endif
            </div>
            <div class="idp-body">
                @if(!$isSelectedActive)
                    <div class="idp-history-note mb-3"><i class="bi bi-archive me-1"></i>กำลังดูข้อมูลจากแผนที่สิ้นสุดแล้ว ข้อมูลส่วนนี้เป็นประวัติและไม่สามารถแก้ไขได้</div>
                @endif
                <div class="idp-profile-grid">
                    <section class="idp-profile-panel">
                        <div class="idp-profile-title"><i class="bi bi-gem me-1"></i>จุดแข็งและศักยภาพ</div>
                        @php($hasStrengthProfile = collect($currentPlan->strength_profile ?? [])->filter(fn($v) => filled($v))->isNotEmpty())
                        @if($hasStrengthProfile)
                            @foreach($strengthFields as $key=>$label)
                                @if(filled(data_get($currentPlan->strength_profile,$key)))
                                    <div class="idp-profile-item"><div class="idp-profile-item-label">{{ $label }}</div><div class="idp-profile-item-value">{{ data_get($currentPlan->strength_profile,$key) }}</div></div>
                                @endif
                            @endforeach
                        @elseif(filled($currentPlan->strength_summary))
                            <div class="idp-profile-item"><div class="idp-profile-item-label">สรุปจุดแข็งจากข้อมูลเดิม</div><div class="idp-profile-item-value">{{ $currentPlan->strength_summary }}</div></div>
                        @else
                            <div class="text-muted small py-2">ยังไม่มีข้อมูลจุดแข็งและศักยภาพ</div>
                        @endif
                        @if($hasStrengthProfile && filled($currentPlan->strength_summary))
                            <div class="idp-profile-item"><div class="idp-profile-item-label">สรุปเพิ่มเติม</div><div class="idp-profile-item-value">{{ $currentPlan->strength_summary }}</div></div>
                        @endif
                    </section>

                    <section class="idp-profile-panel">
                        <div class="idp-profile-title"><i class="bi bi-compass me-1"></i>ความต้องการและประเด็นที่ต้องช่วยเหลือ</div>
                        @php($hasNeedsProfile = collect($currentPlan->needs_profile ?? [])->filter(fn($v) => is_array($v) && collect($v)->filter(fn($x)=>filled($x))->isNotEmpty())->isNotEmpty())
                        @if($hasNeedsProfile)
                            @foreach($needCategories as $key=>$label)
                                @php($need = data_get($currentPlan->needs_profile,$key,[]))
                                @if(is_array($need) && collect($need)->filter(fn($v)=>filled($v))->isNotEmpty())
                                    <div class="idp-need">
                                        <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                            <div class="fw-bold small text-dark">{{ $label }}</div>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                @if(filled(data_get($need,'priority')))<span class="idp-priority idp-priority-{{ data_get($need,'priority') }}">{{ $priorityLabels[data_get($need,'priority')] ?? data_get($need,'priority') }}</span>@endif
                                                @if($canCreateGoal && filled(data_get($need,'detail')))
                                                    <a class="btn btn-sm btn-outline-primary py-1" href="{{ route('individual-development.goals.create',['client'=>$client->id,'need'=>data_get($need,'detail'),'need_category'=>$key]) }}"><i class="bi bi-arrow-right-circle me-1"></i>นำไปสร้างเป้าหมาย</a>
                                                @endif
                                            </div>
                                        </div>
                                        @if(filled(data_get($need,'detail')))<div class="small mt-2">{{ data_get($need,'detail') }}</div>@endif
                                        @if(filled(data_get($need,'client_view')))<div class="small text-muted mt-2"><strong>ความเห็นของเด็ก:</strong> {{ data_get($need,'client_view') }}</div>@endif
                                        @if(filled(data_get($need,'staff_view')))<div class="small text-muted mt-1"><strong>ความเห็นเจ้าหน้าที่:</strong> {{ data_get($need,'staff_view') }}</div>@endif
                                    </div>
                                @endif
                            @endforeach
                        @elseif(filled($currentPlan->development_need_summary) || filled($currentPlan->client_need_summary))
                            @if(filled($currentPlan->development_need_summary))<div class="idp-profile-item"><div class="idp-profile-item-label">ประเด็นที่ควรพัฒนา</div><div class="idp-profile-item-value">{{ $currentPlan->development_need_summary }}</div></div>@endif
                            @if(filled($currentPlan->client_need_summary))<div class="idp-profile-item"><div class="idp-profile-item-label">ความต้องการของผู้รับบริการ</div><div class="idp-profile-item-value">{{ $currentPlan->client_need_summary }}</div></div>@endif
                        @else
                            <div class="text-muted small py-2">ยังไม่มีข้อมูลความต้องการและประเด็นช่วยเหลือ</div>
                        @endif
                    </section>
                </div>
                <div class="row g-2 mt-1">
                    @if(filled($currentPlan->caregiver_need_summary))<div class="col-lg-6"><div class="idp-profile-item"><div class="idp-profile-item-label">ความต้องการของผู้ดูแล/ครอบครัว</div><div class="idp-profile-item-value">{{ $currentPlan->caregiver_need_summary }}</div></div></div>@endif
                    @if(filled($currentPlan->support_network_summary))<div class="col-lg-6"><div class="idp-profile-item"><div class="idp-profile-item-label">เครือข่ายสนับสนุน</div><div class="idp-profile-item-value">{{ $currentPlan->support_network_summary }}</div></div></div>@endif
                    @if(filled($currentPlan->risk_factor_summary))<div class="col-lg-6"><div class="idp-profile-item"><div class="idp-profile-item-label">ปัจจัยเสี่ยงที่เกี่ยวข้องกับแผน</div><div class="idp-profile-item-value">{{ $currentPlan->risk_factor_summary }}</div></div></div>@endif
                    @if(filled($currentPlan->protective_factor_summary))<div class="col-lg-6"><div class="idp-profile-item"><div class="idp-profile-item-label">ปัจจัยคุ้มครอง</div><div class="idp-profile-item-value">{{ $currentPlan->protective_factor_summary }}</div></div></div>@endif
                </div>
            </div>
        </div>
    @endif

    @include('frontend.client.individual_development.partials._supplementary_workspace')

    @if($currentPlan && isset($caseSummary))
        <div class="idp-box idp-card">
            <div class="idp-card-head"><div><h5 class="idp-card-title"><i class="bi bi-magic me-2 text-primary"></i>สรุปสถานการณ์อัตโนมัติ</h5><div class="small text-muted mt-1">สรุปจากข้อมูลในแผน เป้าหมาย กิจกรรม และการติดตาม เพื่อช่วยอ่านเคสเร็วขึ้น โดยไม่แก้ข้อมูลต้นทาง</div></div></div>
            <div class="idp-body"><div class="idp-grid2">
                <div><div class="idp-label">ปัญหา/ความต้องการสำคัญ</div><div class="idp-value">{{ $caseSummary['problem'] }}</div></div>
                <div><div class="idp-label">การดำเนินงาน</div><div class="idp-value">{{ $caseSummary['actions'] }}</div></div>
                <div><div class="idp-label">ผลลัพธ์</div><div class="idp-value">{{ $caseSummary['result'] }}</div></div>
                <div><div class="idp-label">สิ่งที่ต้องทำต่อ</div><div class="idp-value">{{ $caseSummary['next'] }}</div></div>
            </div></div>
        </div>
    @endif

    @if($currentPlan && isset($nextTasks) && $nextTasks->isNotEmpty())
        <div class="idp-box idp-card">
            <div class="idp-card-head"><div><h5 class="idp-card-title"><i class="bi bi-list-task me-2 text-primary"></i>งานที่ต้องดำเนินการต่อ</h5><div class="small text-muted mt-1">รวมกำหนดเป้าหมายและนัดติดตามจากแผนปัจจุบัน</div></div></div>
            <div class="idp-body">
                <div class="row g-2">
                    @foreach($nextTasks->take(6) as $task)
                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100 {{ $task['urgency']==='overdue'?'border-danger-subtle bg-danger-subtle':($task['urgency']==='soon'?'border-warning-subtle bg-warning-subtle':'') }}">
                                <div class="d-flex justify-content-between gap-2 align-items-start"><strong class="small">{{ $task['title'] }}</strong>@if($task['urgency']==='overdue')<span class="badge bg-danger">เลยกำหนด</span>@elseif($task['urgency']==='soon')<span class="badge bg-warning text-dark">ภายใน 7 วัน</span>@endif</div>
                                <div class="small text-muted mt-1">@if($task['date'])กำหนด {{ $thaiDate($task['date']) }}@elseยังไม่ได้กำหนดวัน@endif @if($task['detail']) • {{ $task['detail'] }}@endif</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if(!$currentPlan)
        <div class="idp-box idp-empty">
            <div class="fs-2 text-primary mb-2"><i class="bi bi-clipboard2-plus"></i></div>
            <h5 class="fw-bold">ยังไม่มีแผนพัฒนารายบุคคล</h5>
            <p>เริ่มจากสร้างแผน แล้วประเมิน Baseline ก่อนกำหนดเป้าหมายและกิจกรรม</p>
            @if($canCreatePlan)<a class="idp-btn idp-primary" href="{{ route('individual-development.create',$client->id) }}"><i class="bi bi-plus-circle"></i>สร้างแผนครั้งแรก</a>@endif
        </div>
    @else
        @if(!$isSelectedActive)
            <div class="idp-next">
                <div><strong><i class="bi bi-clock-history me-1"></i>กำลังดูแผนประวัติ ครั้งที่ {{ $currentPlan->plan_no }}</strong><p>แผนที่ปิดหรือยุติแล้วเป็นข้อมูลอ่านย้อนหลัง ไม่ควรแก้ไขข้อมูลต้นทาง</p></div>
                @if($activePlan)<a class="idp-btn idp-primary" href="{{ route('individual-development.index',$client->id) }}">ไปแผนที่กำลังดำเนินการ</a>@endif
            </div>
        @elseif(!$hasPlanningProfile)
            <div class="idp-next"><div><strong>ขั้นตอนถัดไป: ทำความเข้าใจเด็กและกำหนดประเด็นช่วยเหลือ</strong><p>บันทึกจุดแข็ง ศักยภาพ ความต้องการ เสียงของเด็ก ปัจจัยเสี่ยง/คุ้มครอง และเครือข่ายสนับสนุน ก่อนกำหนด Baseline และเป้าหมาย</p></div>@if($canEditProfile)<button type="button" class="idp-btn idp-primary" data-bs-toggle="modal" data-bs-target="#idpProfileModal">บันทึกจุดแข็ง/ความต้องการ</button>@endif</div>
        @elseif(!$baselineAssessment)
            <div class="idp-next"><div><strong>ขั้นตอนถัดไป: ประเมิน Baseline</strong><p>ประเมิน 4 ด้าน 20 ตัวชี้วัด พร้อมหลักฐาน/พฤติกรรมทุกข้อ เพื่อใช้เป็นค่าตั้งต้นของแผน</p></div>@if($canCreateBaseline)<a class="idp-btn idp-primary" href="{{ route('individual-development.baseline.create',$client->id) }}">เริ่มประเมิน Baseline</a>@endif</div>
        @elseif($currentPlan->goals->isEmpty())
            <div class="idp-next"><div><strong>ขั้นตอนถัดไป: กำหนดเป้าหมายการพัฒนา</strong><p>เลือกประเด็นจาก Baseline กำหนดระดับเป้าหมาย ตัวชี้วัดความสำเร็จ และผู้รับผิดชอบ</p></div>@if($canCreateGoal)<a class="idp-btn idp-primary" href="{{ route('individual-development.goals.create',$client->id) }}">สร้างเป้าหมายแรก</a>@endif</div>
        @elseif($unconfirmedReached > 0)
            <div class="idp-next"><div><strong>มี {{ $unconfirmedReached }} เป้าหมายถึงระดับที่กำหนดแล้ว รอผู้ปฏิบัติงานยืนยัน</strong><p>ระบบเพียงแจ้งจากคะแนนล่าสุด การยืนยัน “บรรลุแล้ว” ต้องเป็นการตัดสินใจของผู้รับผิดชอบ และกิจกรรมต้องสิ้นสุดก่อน</p></div><a class="idp-btn idp-primary" href="{{ route('individual-development.goals.index',$client->id) }}">ตรวจและยืนยันผล</a></div>
        @elseif($goalsWithoutActivity > 0)
            <div class="idp-next"><div><strong>มีเป้าหมายที่ยังไม่มีกิจกรรม {{ $goalsWithoutActivity }} รายการ</strong><p>เพิ่มกิจกรรมที่สัมพันธ์กับเป้าหมายก่อนเริ่มติดตามผล เพื่อให้เห็นว่า “ทำอะไร” แล้วเกิดการเปลี่ยนแปลงอย่างไร</p></div><a class="idp-btn idp-primary" href="{{ route('individual-development.goals.index',$client->id) }}">เพิ่มกิจกรรม</a></div>
        @elseif(!$hasFinalOutcome && $currentPlan->goals->whereIn('status',['not_started','in_progress','partial'])->isEmpty())
            <div class="idp-next"><div><strong>ขั้นตอนถัดไป: ประเมินผลก่อนปิดแผน</strong><p>เมื่อเป้าหมายและกิจกรรมสิ้นสุดแล้ว ให้ประเมิน Final Outcome เพื่อเปรียบเทียบกับ Baseline ก่อนสรุปปิดแผน</p></div>@if($canCreateOutcome)<a class="idp-btn idp-primary" href="{{ route('individual-development.outcomes.create',['client'=>$client->id,'plan'=>$currentPlan->id,'type'=>'final']) }}"><i class="bi bi-graph-up-arrow"></i>ประเมิน Final Outcome</a>@endif</div>
        @elseif($canClosePlan)
            <div class="idp-next"><div><strong>แผนครบเงื่อนไขสำหรับสรุปและปิดแผนแล้ว</strong><p>ตรวจผลลัพธ์สุดท้าย ข้อเสนอแนะ และเหตุผลในการปิดแผนก่อนยืนยัน</p></div><a class="idp-btn idp-primary" href="{{ route('individual-development.close.form',$client->id) }}"><i class="bi bi-check2-circle"></i>สรุปและปิดแผน</a></div>
        @else
            <div class="idp-next"><div><strong>ดำเนินแผนและติดตามต่อ</strong><p>บันทึกผลกิจกรรมและ Follow-up ตามสถานการณ์จริง ระบบจะเปรียบเทียบกับ Baseline/ครั้งก่อนโดยไม่ตัดสินแทนผู้ปฏิบัติงาน</p>@if($closeBlockers)<ul class="idp-blockers">@foreach($closeBlockers as $b)<li>{{ $b }}</li>@endforeach</ul>@endif</div>@if($canCreateFollowup)<a class="idp-btn idp-primary" href="{{ route('individual-development.followups.create',$client->id) }}">บันทึกติดตาม</a>@endif</div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-12 col-xl-8">
                <div class="idp-box idp-card h-100">
                    <div class="idp-card-head">
                        <div><h5 class="idp-card-title">แผนครั้งที่ {{ $currentPlan->plan_no }}</h5><div class="small text-muted mt-1">{{ $thaiDate($currentPlan->start_date) }} – {{ $thaiDate($currentPlan->end_date) }}</div></div>
                        <span class="idp-status st-{{ $currentPlan->status }}">{{ $statusLabels[$currentPlan->status] ?? $currentPlan->status }}</span>
                    </div>
                    <div class="idp-body">
                        <div class="idp-grid2">
                            <div class="idp-wide" style="grid-column:1/-1"><div class="idp-label">เป้าหมายภาพรวม</div><div class="idp-value">{{ $currentPlan->overall_goal ?: '-' }}</div></div>
                            <div><div class="idp-label">จุดแข็ง</div><div class="idp-value">{{ $currentPlan->strength_summary ?: '-' }}</div></div>
                            <div><div class="idp-label">ประเด็นที่ควรพัฒนา</div><div class="idp-value">{{ $currentPlan->development_need_summary ?: '-' }}</div></div>
                            <div><div class="idp-label">ความต้องการของผู้รับบริการ</div><div class="idp-value">{{ $currentPlan->client_need_summary ?: '-' }}</div></div>
                            <div><div class="idp-label">ปัจจัยคุ้มครอง</div><div class="idp-value">{{ $currentPlan->protective_factor_summary ?: '-' }}</div></div>
                        </div>
                        @if($isSelectedActive && ($canEditPlan || $canCancelPlan || $canDeletePlan))
                            <div class="idp-actions mt-3 pt-3 border-top">
                                @if($canEditPlan)<a href="{{ route('individual-development.edit',$client->id) }}" class="idp-btn idp-light"><i class="bi bi-pencil"></i>แก้ไขข้อมูลแผน</a>@endif
                                @if($canClosePlan)<a href="{{ route('individual-development.close.form',$client->id) }}" class="idp-btn idp-outline"><i class="bi bi-check-circle"></i>ปิดแผน</a>@endif
                                @if($canCancelPlan)<button type="button" class="idp-btn idp-warning" data-bs-toggle="modal" data-bs-target="#idpCancelPlan"><i class="bi bi-slash-circle"></i>ยุติแผน</button>@endif
                                @if($canDeletePlan)<form method="POST" action="{{ route('individual-development.destroy',$client->id) }}" class="idp-delete-plan">@csrf @method('DELETE')<button class="idp-btn idp-danger" type="submit"><i class="bi bi-trash"></i>ลบแผนที่ยังไม่ใช้</button></form>@endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="idp-box idp-card h-100">
                    <div class="idp-card-head"><h5 class="idp-card-title">สถานะการดำเนินงาน</h5></div>
                    <div class="idp-body">
                        <div class="idp-stats">
                            <div class="idp-stat"><span>Baseline</span><b style="font-size:.88rem">{{ $baselineAssessment ? 'ประเมินแล้ว' : 'รอประเมิน' }}</b></div>
                            <div class="idp-stat"><span>เป้าหมายทั้งหมด</span><b>{{ $goalStats['total'] }}</b></div>
                            <div class="idp-stat"><span>บรรลุแล้ว</span><b>{{ $goalStats['achieved'] }}</b></div>
                            <div class="idp-stat"><span>กำลังดำเนินการ</span><b>{{ $goalStats['in_progress'] + $goalStats['partial'] + $goalStats['not_started'] }}</b></div>
                        </div>
                        <div class="mt-3 pt-3 border-top"><div class="idp-label">Baseline</div><div class="idp-value mb-2">{{ $baselineAssessment ? $thaiDate($baselineAssessment->assessment_date) : '-' }}</div><div class="idp-label">ติดตามล่าสุด</div><div class="idp-value">{{ $latestFollowup ? 'ครั้งที่ '.$latestFollowup->followup_no.' • '.$thaiDate($latestFollowup->followup_date) : '-' }}</div></div>
                    </div>
                </div>
            </div>
        </div>

        @if(in_array($currentPlan->status,['completed','cancelled'],true))
            <div class="idp-box idp-card idp-final">
                <div class="idp-card-head"><h5 class="idp-card-title">สรุปการสิ้นสุดแผน</h5><span class="idp-status st-{{ $currentPlan->status }}">{{ $statusLabels[$currentPlan->status] }}</span></div>
                <div class="idp-body"><div class="idp-grid2"><div><div class="idp-label">วันที่สิ้นสุดจริง</div><div class="idp-value">{{ $thaiDate($currentPlan->closed_at) }}</div></div><div><div class="idp-label">เหตุผล/เกณฑ์การสิ้นสุด</div><div class="idp-value">{{ $currentPlan->close_reason ?: '-' }}</div></div><div><div class="idp-label">ผลลัพธ์สุดท้าย</div><div class="idp-value">{{ $currentPlan->final_outcome ?: '-' }}</div></div><div><div class="idp-label">ข้อเสนอแนะหลังสิ้นสุดแผน</div><div class="idp-value">{{ $currentPlan->final_recommendation ?: '-' }}</div></div></div></div>
            </div>
        @endif

        <div class="idp-box idp-card">
            <div class="idp-card-head"><div><h5 class="idp-card-title">พัฒนาการ 4 ด้าน: Baseline → ล่าสุด</h5><div class="small text-muted mt-1">{{ $latestEvaluationLabel }}</div></div>@if($baselineAssessment && $isSelectedActive)<a href="{{ route('individual-development.baseline.show',$client->id) }}" class="idp-btn idp-light">ดู Baseline</a>@endif</div>
            <div class="idp-body"><div class="idp-domains">
                @foreach($domainScores as $domain)
                    <div class="idp-domain"><span class="idp-domain-icon"><i class="bi {{ $domainIcons[$domain['code']] ?? 'bi-clipboard-data' }}"></i></span><div class="idp-domain-name">{{ $domain['name'] }}</div><div class="idp-domain-score">{{ $domain['baseline_score'] !== null ? number_format($domain['baseline_score'],2) : '-' }} → {{ $domain['score'] !== null ? number_format($domain['score'],2) : '-' }}</div><div class="idp-delta {{ $domain['trend']==='up'?'up':($domain['trend']==='down'?'down':'same') }}">@if($domain['delta']!==null){{ $domain['delta']>0?'↑ +':($domain['delta']<0?'↓ ':'→ ') }}{{ number_format($domain['delta'],2) }}@elseยังไม่มีข้อมูลเปรียบเทียบ@endif</div><div class="idp-sub">ระดับ {{ $domain['level'] ?? '-' }} • {{ $domain['level_label'] }}</div></div>
                @endforeach
            </div></div>
        </div>

        @if($baselineAssessment)
            <div class="idp-box idp-card">
                <div class="idp-card-head"><div><h5 class="idp-card-title">เป้าหมายและความก้าวหน้า</h5><div class="small text-muted mt-1">ระบบแสดงคะแนนล่าสุดเทียบ Target แต่การยืนยันบรรลุเป็นหน้าที่ผู้ปฏิบัติงาน</div></div>@if($isSelectedActive)<a href="{{ route('individual-development.goals.index',$client->id) }}" class="idp-btn idp-light">จัดการเป้าหมาย</a>@endif</div>
                @if($currentPlan->goals->isEmpty())<div class="idp-empty">ยังไม่มีเป้าหมาย</div>@else<div class="idp-table"><table><thead><tr><th>ด้าน/ตัวชี้วัด</th><th>เป้าหมาย</th><th>Baseline → ล่าสุด → Target</th><th>ความก้าวหน้า</th><th>สถานะ</th></tr></thead><tbody>
                    @foreach($currentPlan->goals as $goal)
                        @php($gp=$goalProgress[$goal->id] ?? [])
                        <tr><td><strong>{{ $goal->domain?->name ?? '-' }}</strong><div class="small text-muted">{{ $goal->indicator?->name ?? 'เป้าหมายระดับด้าน' }}</div></td><td>{{ $goal->title }}</td><td><strong>{{ $gp['baseline'] ?? $goal->baseline_level ?? '-' }} → {{ $gp['current'] ?? '-' }} → {{ $gp['target'] ?? $goal->target_level ?? '-' }}</strong></td><td class="idp-progress"><div>{{ $gp['progress_percent'] ?? 0 }}%</div><div class="idp-bar"><span style="width:{{ $gp['progress_percent'] ?? 0 }}%"></span></div>@if($gp['needs_confirmation'] ?? false)<span class="idp-pending mt-1">ถึง Target • รอยืนยัน</span>@elseif(($gp['reached']??false) && $goal->status==='achieved')<span class="idp-reached mt-1">ยืนยันบรรลุแล้ว</span>@endif</td><td>{{ $goalStatusLabels[$goal->status] ?? $goal->status }}</td></tr>
                    @endforeach
                </tbody></table></div>@endif
            </div>

            <div class="idp-box idp-card">
                <div class="idp-card-head"><div><h5 class="idp-card-title">Timeline การติดตาม</h5><div class="small text-muted mt-1">อ่านต่อเนื่องจากครั้งล่าสุด พร้อมสิ่งที่ต้องทำต่อ</div></div>@if($canCreateFollowup)<a href="{{ route('individual-development.followups.create',$client->id) }}" class="idp-btn idp-primary">เพิ่มการติดตาม</a>@endif</div>
                <div class="idp-body">@if($currentPlan->followups->isEmpty())<div class="text-center text-muted py-3">ยังไม่มีการติดตาม</div>@else<div class="idp-timeline">@foreach($currentPlan->followups->sortByDesc('followup_no') as $fu)<div class="idp-time"><div class="d-flex justify-content-between gap-2 flex-wrap"><div><div class="idp-time-title">ครั้งที่ {{ $fu->followup_no }} • {{ $thaiDate($fu->followup_date) }}</div><div class="small text-muted">{{ ['improved'=>'ดีขึ้น','stable'=>'คงเดิม','declined'=>'ถดถอย','achieved'=>'บรรลุเป้าหมาย'][$fu->overall_result] ?? '-' }}</div></div><a class="idp-btn idp-light" href="{{ route('individual-development.followups.show',[$client->id,$fu->id]) }}">ดูรายละเอียด</a></div>@if($fu->positive_changes || $fu->result)<div class="idp-time-text">{{ $fu->positive_changes ?: $fu->result }}</div>@endif @if($fu->next_action)<div class="idp-time-text"><strong>ทำต่อ:</strong> {{ $fu->next_action }} @if($fu->next_followup_date) • นัด {{ $thaiDate($fu->next_followup_date) }}@endif</div>@endif</div>@endforeach</div>@endif</div>
            </div>
        @endif

        <div class="idp-box idp-card">
            <div class="idp-card-head"><h5 class="idp-card-title">ประวัติแผนพัฒนารายบุคคล</h5>@if($canCreatePlan)<a class="idp-btn idp-primary" href="{{ route('individual-development.create',$client->id) }}">สร้างแผนรอบใหม่</a>@endif</div>
            <div class="idp-table"><table><thead><tr><th>ครั้งที่</th><th>ช่วงแผน</th><th>เป้าหมายภาพรวม</th><th>สถานะ</th><th></th></tr></thead><tbody>@foreach($plans as $plan)<tr class="{{ $currentPlan->id===$plan->id?'idp-history-selected':'' }}"><td class="fw-bold">{{ $plan->plan_no }}</td><td>{{ $thaiDate($plan->start_date) }} – {{ $thaiDate($plan->end_date) }}</td><td>{{ \Illuminate\Support\Str::limit($plan->overall_goal ?: '-',100) }}</td><td><span class="idp-status st-{{ $plan->status }}">{{ $statusLabels[$plan->status] ?? $plan->status }}</span></td><td><a class="idp-btn idp-light" href="{{ route('individual-development.index',['client'=>$client->id,'plan'=>$plan->id]) }}">ดูแผน</a></td></tr>@endforeach</tbody></table></div>
        </div>
    @endif
</div>

@if($currentPlan && $canEditProfile)
<div class="modal fade idp-safe-modal" id="idpProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="POST" action="{{ route('individual-development.profile.update',$client->id) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <div><h5 class="modal-title fw-bold">จุดแข็ง ศักยภาพ และความต้องการ</h5><div class="small text-muted">แก้ไขเฉพาะแผนที่กำลังดำเนินการ • ข้อมูลเดิมยังคงเป็นส่วนหนึ่งของประวัติแผน</div></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-primary mb-3">จุดแข็งและศักยภาพ</h6>
                                @foreach($strengthFields as $key=>$label)
                                    <div class="mb-3"><label class="form-label fw-semibold small">{{ $label }}</label><textarea rows="2" name="strength_profile[{{ $key }}]" class="form-control">{{ old('strength_profile.'.$key, data_get($currentPlan->strength_profile,$key)) }}</textarea></div>
                                @endforeach
                                <div><label class="form-label fw-semibold small">สรุปจุดแข็งเพิ่มเติม</label><textarea rows="3" name="strength_summary" class="form-control">{{ old('strength_summary',$currentPlan->strength_summary) }}</textarea></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-primary mb-3">ความต้องการและประเด็นที่ต้องช่วยเหลือ</h6>
                                @foreach($needCategories as $key=>$label)
                                    @php($need = data_get($currentPlan->needs_profile,$key,[]))
                                    <div class="border rounded-3 p-3 mb-3">
                                        <div class="fw-bold small mb-2">{{ $label }}</div>
                                        <textarea rows="2" name="needs_profile[{{ $key }}][detail]" class="form-control mb-2" placeholder="รายละเอียดความต้องการ/ประเด็นที่ต้องช่วยเหลือ">{{ old('needs_profile.'.$key.'.detail',data_get($need,'detail')) }}</textarea>
                                        <div class="row g-2">
                                            <div class="col-md-5"><select name="needs_profile[{{ $key }}][priority]" class="form-select"><option value="">ระดับความสำคัญ</option>@foreach($priorityLabels as $value=>$labelPriority)<option value="{{ $value }}" @selected(old('needs_profile.'.$key.'.priority',data_get($need,'priority'))===$value)>{{ $labelPriority }}</option>@endforeach</select></div>
                                            <div class="col-md-7"><input type="text" name="needs_profile[{{ $key }}][client_view]" class="form-control" value="{{ old('needs_profile.'.$key.'.client_view',data_get($need,'client_view')) }}" placeholder="ความเห็นของเด็ก"></div>
                                        </div>
                                        <textarea rows="2" name="needs_profile[{{ $key }}][staff_view]" class="form-control mt-2" placeholder="ความเห็นเจ้าหน้าที่">{{ old('needs_profile.'.$key.'.staff_view',data_get($need,'staff_view')) }}</textarea>
                                    </div>
                                @endforeach
                                <div><label class="form-label fw-semibold small">สรุปประเด็นที่ควรพัฒนา</label><textarea rows="3" name="development_need_summary" class="form-control">{{ old('development_need_summary',$currentPlan->development_need_summary) }}</textarea></div>
                            </div>
                        </div>
                        <div class="col-md-6"><label class="form-label fw-semibold small">ความต้องการของผู้รับบริการ</label><textarea rows="3" name="client_need_summary" class="form-control">{{ old('client_need_summary',$currentPlan->client_need_summary) }}</textarea></div>
                        <div class="col-md-6"><label class="form-label fw-semibold small">ความต้องการของผู้ดูแล/ครอบครัว</label><textarea rows="3" name="caregiver_need_summary" class="form-control">{{ old('caregiver_need_summary',$currentPlan->caregiver_need_summary) }}</textarea></div>
                        <div class="col-md-6"><label class="form-label fw-semibold small">ปัจจัยเสี่ยงที่เกี่ยวข้องกับการวางแผน</label><textarea rows="3" name="risk_factor_summary" class="form-control">{{ old('risk_factor_summary',$currentPlan->risk_factor_summary) }}</textarea></div>
                        <div class="col-md-6"><label class="form-label fw-semibold small">ปัจจัยคุ้มครอง</label><textarea rows="3" name="protective_factor_summary" class="form-control">{{ old('protective_factor_summary',$currentPlan->protective_factor_summary) }}</textarea></div>
                        <div class="col-12"><label class="form-label fw-semibold small">เครือข่ายสนับสนุน</label><textarea rows="3" name="support_network_summary" class="form-control">{{ old('support_network_summary',$currentPlan->support_network_summary) }}</textarea></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button><button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>บันทึกข้อมูล</button></div>
            </form>
        </div>
    </div>
</div>
@endif

@if($currentPlan && $canCancelPlan)
<div class="modal fade idp-safe-modal" id="idpCancelPlan" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"><div class="modal-content"><form method="POST" action="{{ route('individual-development.cancel',$client->id) }}">@csrf<div class="modal-header"><h5 class="modal-title">ยุติแผนพัฒนารายบุคคล</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="alert alert-warning small">การยุติแผนจะเก็บประวัติเดิมทั้งหมด และยกเลิกเป้าหมาย/กิจกรรมที่ยังเปิดอยู่ ไม่ใช่การลบข้อมูล</div><div class="mb-3"><label class="form-label fw-bold">เหตุผลที่ยุติแผน <span class="text-danger">*</span></label><textarea class="form-control" name="close_reason" rows="3" required></textarea></div><div class="mb-3"><label class="form-label fw-bold">สรุปผล ณ วันที่ยุติ</label><textarea class="form-control" name="final_outcome" rows="3"></textarea></div><div><label class="form-label fw-bold">ข้อเสนอแนะ/แนวทางต่อไป</label><textarea class="form-control" name="final_recommendation" rows="3"></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">ปิด</button><button type="submit" class="btn btn-warning">ยืนยันยุติแผน</button></div></form></div></div></div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.idp-delete-plan').forEach(function(form){form.addEventListener('submit',function(e){if(!window.Swal){return;}e.preventDefault();Swal.fire({icon:'warning',title:'ลบแผนที่ยังไม่ถูกใช้งาน?',text:'ใช้เฉพาะกรณีสร้างผิดและยังไม่มี Baseline/Goal/Follow-up',showCancelButton:true,confirmButtonText:'ลบ',cancelButtonText:'ยกเลิก',confirmButtonColor:'#b91c1c'}).then(r=>{if(r.isConfirmed)form.submit();});});});
    @if(session('success') || session('warning'))
    if(window.Swal){Swal.fire({icon:@json(session('success')?'success':'warning'),title:@json(session('success')?'สำเร็จ':'แจ้งเตือน'),text:@json(session('success') ?? session('warning')),confirmButtonText:'OK',timer:3000,timerProgressBar:true});}
    @endif
    @if($errors->any() && $currentPlan && $canEditProfile)
    var profileModalEl=document.getElementById('idpProfileModal'); if(profileModalEl && window.bootstrap){bootstrap.Modal.getOrCreateInstance(profileModalEl).show();}
    @endif
});
</script>


{{-- IDP_VALIDATION_COMPLETE_V1_INCLUDE --}}
@include('frontend.client.individual_development._validation')
@endsection
