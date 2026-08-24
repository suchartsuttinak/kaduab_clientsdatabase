@extends('admin.admin_master')
@section('admin')
@php
    $statusLabels = [
        'draft' => 'ร่างแผน',
        'active' => 'กำลังดำเนินการ',
        'review' => 'อยู่ระหว่างทบทวน',
        'completed' => 'ปิดแผนแล้ว',
        'cancelled' => 'ยุติแผน',
    ];
    $outcomeLabels = [
        'improved' => 'ดีขึ้น',
        'stable' => 'คงเดิม',
        'declined' => 'ต้องติดตาม',
        'achieved' => 'บรรลุเป้าหมาย',
    ];
    $thaiDate = static function ($value): string {
        if (!$value) return '-';
        try {
            $date = $value instanceof \Carbon\CarbonInterface ? $value : \Carbon\Carbon::parse($value, 'Asia/Bangkok');
            return $date->format('d/m/') . ($date->year + 543);
        } catch (\Throwable $e) { return '-'; }
    };
@endphp

<style>
/*
 * Individual Development Center V10
 * Scope เฉพาะหน้านี้ ไม่แก้ CSS global ของระบบ
 */
#permissionReadonlyBanner{display:none!important}
.idvc10{--idv-bg:#f7f9fc;--idv-card:#fff;--idv-border:#e4eaf2;--idv-text:#172033;--idv-muted:#66758a;--idv-primary:#3f6fe5;--idv-primary2:#5b83ed;padding:0 .15rem 1.5rem}.idvc10 *{min-width:0}.idvc10 .idv-hero{background:linear-gradient(135deg,#fff 0%,#f8fbff 100%);border:1px solid var(--idv-border);border-radius:16px;padding:1rem 1.1rem;margin-bottom:.85rem;box-shadow:0 8px 26px rgba(37,65,110,.045)}.idvc10 .idv-title{font-size:1.18rem;font-weight:800;color:var(--idv-text);margin:0;letter-spacing:-.01em}.idvc10 .idv-title-icon{display:inline-flex;width:30px;height:30px;align-items:center;justify-content:center;border-radius:9px;background:#edf3ff;color:var(--idv-primary);margin-right:.45rem}.idvc10 .idv-sub{font-size:.78rem;color:var(--idv-muted);margin:.28rem 0 0 2.55rem}.idvc10 .idv-card{background:var(--idv-card);border:1px solid var(--idv-border);border-radius:14px;box-shadow:0 4px 16px rgba(37,65,110,.03)}.idvc10 .idv-stat{display:flex;align-items:center;gap:.75rem;padding:.82rem .9rem;height:100%;color:inherit!important;text-decoration:none!important;transition:border-color .15s ease,transform .15s ease,box-shadow .15s ease}.idvc10 .idv-stat:hover{border-color:#c7d5ee;transform:translateY(-1px);box-shadow:0 7px 20px rgba(37,65,110,.065)}.idvc10 .idv-stat-icon{display:flex;align-items:center;justify-content:center;flex:0 0 34px;width:34px;height:34px;border-radius:10px;background:#f1f5ff;color:var(--idv-primary);font-size:.98rem}.idvc10 .idv-stat-label{font-size:.7rem;color:var(--idv-muted);font-weight:700;line-height:1.25}.idvc10 .idv-stat-value{font-size:1.28rem;font-weight:850;color:var(--idv-text);line-height:1.05;margin-top:.18rem}.idvc10 .idv-filter{padding:.9rem 1rem;margin:.9rem 0}.idvc10 .idv-filter-title{font-size:.8rem;font-weight:800;color:var(--idv-text);margin-bottom:.65rem}.idvc10 .form-label{font-size:.72rem;font-weight:750;color:#516176;margin-bottom:.3rem}.idvc10 .form-control,.idvc10 .form-select{border-radius:10px;border-color:#dbe3ed;min-height:39px;font-size:.78rem;background-color:#fff}.idvc10 .form-control:focus,.idvc10 .form-select:focus{border-color:#9db7ef;box-shadow:0 0 0 .16rem rgba(63,111,229,.1)}.idvc10 .idv-btn-primary{min-height:39px;border:0;border-radius:10px;background:linear-gradient(135deg,var(--idv-primary),var(--idv-primary2));color:#fff;font-size:.78rem;font-weight:750;box-shadow:0 5px 13px rgba(63,111,229,.17)}.idvc10 .idv-btn-primary:hover{color:#fff;filter:brightness(.98)}.idvc10 .idv-reset{min-height:39px;border-radius:10px}.idvc10 .idv-table-card{overflow:hidden}.idvc10 .idv-table-head{padding:.78rem 1rem;border-bottom:1px solid #edf1f6;display:flex;align-items:center;justify-content:space-between;gap:1rem}.idvc10 .idv-table-title{font-size:.82rem;font-weight:800;color:var(--idv-text)}.idvc10 .idv-table-note{font-size:.69rem;color:var(--idv-muted)}.idvc10 .idv-table{margin:0;font-size:.76rem}.idvc10 .idv-table thead th{background:#f8fafc;color:#536175;font-size:.69rem;font-weight:800;white-space:nowrap;border-bottom:1px solid #dfe6ef;padding:.68rem .72rem}.idvc10 .idv-table tbody td{padding:.72rem;border-color:#edf1f6;vertical-align:middle;color:#263449}.idvc10 .idv-table tbody tr:hover{background:#fbfdff}.idvc10 .idv-name{font-weight:850;color:#172033;font-size:.78rem}.idvc10 .idv-meta{font-size:.66rem;color:var(--idv-muted);margin-top:.12rem;line-height:1.35}.idvc10 .idv-badge{display:inline-flex;align-items:center;padding:.2rem .47rem;border-radius:999px;font-size:.64rem;font-weight:800;white-space:nowrap}.idvc10 .st-active,.idvc10 .st-good{background:#ebfaf1;color:#137a3b}.idvc10 .st-completed{background:#edf4ff;color:#2456a2}.idvc10 .st-cancelled{background:#f1f4f8;color:#66758a}.idvc10 .st-none,.idvc10 .st-soon,.idvc10 .st-warn{background:#fff6e8;color:#99600d}.idvc10 .st-overdue,.idvc10 .st-risk{background:#fff0f2;color:#b4233b}.idvc10 .idv-open{display:inline-flex;align-items:center;gap:.3rem;border:1px solid #8eb0ff;color:#315fcb;background:#fff;border-radius:9px;padding:.38rem .62rem;font-size:.69rem;font-weight:800;text-decoration:none;white-space:nowrap}.idvc10 .idv-open:hover{background:#f3f7ff;color:#244fae;border-color:#719aef}.idvc10 .idv-empty{padding:2.6rem 1rem;text-align:center;color:var(--idv-muted)}.idvc10 .idv-pagination{padding:.72rem 1rem;border-top:1px solid #edf1f6}.idvc10 .text-muted{color:#8390a2!important}
@media(min-width:1200px){.idvc10 .idv-stat-col{flex:0 0 25%;max-width:25%}}
@media(max-width:1199.98px){.idvc10 .idv-table{min-width:1040px}.idvc10 .idv-table-scroll{overflow-x:auto}}
@media(max-width:767.98px){.idvc10 .idv-sub{margin-left:0}.idvc10 .idv-hero{padding:.9rem}.idvc10 .idv-filter{padding:.8rem}.idvc10 .idv-stat{padding:.75rem}.idvc10 .idv-table-head{align-items:flex-start;flex-direction:column}}
</style>

<div class="container-fluid px-2 px-lg-3 idvc10" data-permission-readonly-ui="off">
    <section class="idv-hero">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="idv-title"><span class="idv-title-icon"><i class="bi bi-diagram-3"></i></span>ศูนย์กลางการพัฒนาเด็ก</h1>
                <div class="idv-sub">ภาพรวมเด็กทุกบ้าน • แผนพัฒนา เป้าหมาย งานที่ต้องติดตาม ผลลัพธ์ และเอกสารสำคัญในจุดเดียว</div>
            </div>
        </div>
    </section>

    <div class="row g-2">
        @foreach([
            ['key'=>'clients','label'=>'เด็กทั้งหมด','icon'=>'bi-people','status'=>''],
            ['key'=>'active_plans','label'=>'มีแผนกำลังดำเนินการ','icon'=>'bi-journal-check','status'=>'active'],
            ['key'=>'without_plan','label'=>'ยังไม่มีแผน','icon'=>'bi-journal-plus','status'=>'no_plan'],
            ['key'=>'overdue','label'=>'เป้าหมายเลยกำหนด','icon'=>'bi-exclamation-triangle','status'=>'overdue'],
            ['key'=>'due_soon','label'=>'ครบกำหนดภายใน 7 วัน','icon'=>'bi-calendar-event','status'=>'due_soon'],
            ['key'=>'stale','label'=>'ไม่ได้ติดตามเกิน 30 วัน','icon'=>'bi-hourglass-split','status'=>'stale'],
            ['key'=>'completed','label'=>'มีแผนที่ปิดแล้ว','icon'=>'bi-check2-circle','status'=>'completed'],
            ['key'=>'documents_attention','label'=>'เอกสารต้องติดตาม','icon'=>'bi-folder-exclamation','status'=>'documents_attention'],
        ] as $stat)
            <div class="col-6 col-md-4 idv-stat-col">
                <a class="idv-card idv-stat" data-permission-action="filter" data-permission-keep href="{{ request()->fullUrlWithQuery(['status'=>$stat['status'] ?: null,'page'=>null]) }}">
                    <span class="idv-stat-icon"><i class="bi {{ $stat['icon'] }}"></i></span>
                    <span><span class="idv-stat-label d-block">{{ $stat['label'] }}</span><span class="idv-stat-value d-block">{{ number_format($stats[$stat['key']] ?? 0) }}</span></span>
                </a>
            </div>
        @endforeach
    </div>

    <section class="idv-card idv-filter">
        <div class="idv-filter-title"><i class="bi bi-funnel me-1 text-primary"></i>ค้นหาและกรองข้อมูล</div>
        <form method="GET" class="row g-2 align-items-end" data-permission-action="filter" data-permission-keep>
            <div class="col-xl-4 col-md-6">
                <label class="form-label">ค้นหาเด็ก</label>
                <input class="form-control" name="q" value="{{ request('q') }}" placeholder="ชื่อ / นามสกุล / ชื่อเล่น / เลขทะเบียน">
            </div>
            <div class="col-xl-2 col-md-6">
                <label class="form-label">บ้าน</label>
                <select class="form-select" name="house_id"><option value="">ทุกบ้าน</option>@foreach($houses as $house)<option value="{{ $house->id }}" @selected((string)request('house_id')===(string)$house->id)>{{ $house->house_name ?? $house->name ?? '-' }}</option>@endforeach</select>
            </div>
            <div class="col-xl-2 col-md-6">
                <label class="form-label">โครงการ</label>
                <select class="form-select" name="project_id"><option value="">ทุกโครงการ</option>@foreach($projects as $project)<option value="{{ $project->id }}" @selected((string)request('project_id')===(string)$project->id)>{{ $project->project_name ?? $project->name ?? '-' }}</option>@endforeach</select>
            </div>
            <div class="col-xl-2 col-md-6">
                <label class="form-label">สถานะ</label>
                <select class="form-select" name="status">
                    <option value="">ทั้งหมด</option>
                    <option value="active" @selected(request('status')==='active')>มีแผนกำลังดำเนินการ</option>
                    <option value="no_plan" @selected(request('status')==='no_plan')>ยังไม่มีแผน</option>
                    <option value="overdue" @selected(request('status')==='overdue')>เป้าหมายเลยกำหนด</option>
                    <option value="due_soon" @selected(request('status')==='due_soon')>ครบกำหนดภายใน 7 วัน</option>
                    <option value="stale" @selected(request('status')==='stale')>ไม่ได้ติดตามเกิน 30 วัน</option>
                    <option value="completed" @selected(request('status')==='completed')>มีแผนที่ปิดแล้ว</option>
                    <option value="documents_attention" @selected(request('status')==='documents_attention')>เอกสารต้องติดตาม</option>
                </select>
            </div>
            <div class="col-xl-2 d-flex gap-2">
                <button class="btn idv-btn-primary flex-grow-1" data-permission-action="filter"><i class="bi bi-search me-1"></i>ค้นหา</button>
                @if(request()->hasAny(['q','house_id','project_id','status']))
                    <a href="{{ route('individual-development.center') }}" data-permission-action="filter" data-permission-keep class="btn btn-outline-secondary idv-reset" title="ล้างตัวกรอง"><i class="bi bi-arrow-counterclockwise"></i></a>
                @endif
            </div>
        </form>
    </section>

    <section class="idv-card idv-table-card">
        <div class="idv-table-head">
            <div>
                <div class="idv-table-title"><i class="bi bi-list-ul me-1 text-primary"></i>รายการผู้รับบริการ</div>
                <div class="idv-table-note">แสดง {{ $clients->count() }} รายการในหน้านี้ จากทั้งหมด {{ number_format($clients->total()) }} รายการตามเงื่อนไข</div>
            </div>
            @if(request()->hasAny(['q','house_id','project_id','status']))
                <div class="idv-table-note"><i class="bi bi-filter-circle me-1"></i>กำลังใช้ตัวกรอง</div>
            @endif
        </div>
        <div class="idv-table-scroll table-responsive">
            <table class="table align-middle idv-table">
                <thead><tr><th>ผู้รับบริการ</th><th>บ้าน / โครงการ</th><th>แผนล่าสุด</th><th>เป้าหมาย</th><th>งานถัดไป</th><th>ผลล่าสุด</th><th class="text-end">จัดการ</th></tr></thead>
                <tbody>
                @forelse($clients as $client)
                    @php
                        $plan = $client->individualDevelopmentPlans->first();
                        $isActivePlan = $plan?->status === 'active';
                        $goals = $plan?->goals ?? collect();
                        $openGoals = $isActivePlan ? $goals->whereNotIn('status',['achieved','cancelled']) : collect();
                        $overdueGoals = $openGoals->filter(fn($g) => $g->target_date && $g->target_date->lt($today))->count();
                        $dueSoonGoals = $openGoals->filter(fn($g) => $g->target_date && !$g->target_date->lt($today) && $g->target_date->lte($soon))->count();
                        $latestFollowup = $plan?->followups?->sortByDesc('followup_no')->first();
                        $latestAssessment = $plan?->assessments?->sortByDesc(fn($a)=>($a->assessment_date?->format('Ymd') ?? '00000000').str_pad((string)$a->round_no,4,'0',STR_PAD_LEFT))->first();
                        $latestAssessmentAverage = $latestAssessment?->items?->pluck('score')->filter(fn($v)=>$v!==null)->avg();
                        $nextDate = null;
                        $nextAction = null;
                        if ($isActivePlan) {
                            $nextDate = $latestFollowup?->next_followup_date;
                            $nextAction = $latestFollowup?->next_action;
                            if (!$nextDate) {
                                $nearestGoal = $openGoals->filter(fn($g)=>$g->target_date)->sortBy('target_date')->first();
                                $nextDate = $nearestGoal?->target_date;
                                $nextAction = $nextAction ?: ($nearestGoal ? 'ติดตามเป้าหมาย: '.$nearestGoal->title : null);
                            }
                        }
                    @endphp
                    <tr>
                        <td><div class="idv-name">{{ $client->fullname ?? $client->full_name ?? $client->name ?? '-' }}</div><div class="idv-meta">{{ $client->register_number ? 'เลขทะเบียน '.$client->register_number : 'ID '.$client->id }}@if($client->nick_name) • ชื่อเล่น {{ $client->nick_name }}@endif</div></td>
                        <td><div>{{ $client->house?->house_name ?? $client->house?->name ?? '-' }}</div><div class="idv-meta">{{ $client->project?->project_name ?? $client->project?->name ?? '-' }}</div></td>
                        <td>
                            @if($plan)<div class="fw-bold">ครั้งที่ {{ $plan->plan_no }}</div><span class="idv-badge st-{{ in_array($plan->status,['active','completed','cancelled'],true)?$plan->status:'none' }}">{{ $statusLabels[$plan->status] ?? $plan->status }}</span>@else<span class="idv-badge st-none">ยังไม่มีแผน</span>@endif
                        </td>
                        <td>
                            @if($plan && $isActivePlan)
                                <div>{{ $openGoals->count() }} เป้าหมายที่ยังเปิด</div>
                                @if($overdueGoals)<span class="idv-badge st-overdue mt-1">เลยกำหนด {{ $overdueGoals }}</span>@elseif($dueSoonGoals)<span class="idv-badge st-soon mt-1">ใกล้ครบกำหนด {{ $dueSoonGoals }}</span>@elseif($goals->isNotEmpty())<span class="idv-badge st-good mt-1">ตามแผน</span>@endif
                            @elseif($plan)<span class="text-muted">สิ้นสุดแผนแล้ว</span>@else<span class="text-muted">-</span>@endif
                        </td>
                        <td>
                            @if(!$isActivePlan && $plan)<span class="text-muted">ไม่มีงานค้างจากแผนที่สิ้นสุดแล้ว</span>@elseif($nextAction || $nextDate)<div>{{ \Illuminate\Support\Str::limit($nextAction ?: 'ติดตามตามกำหนด',72) }}</div><div class="idv-meta">{{ $nextDate ? $thaiDate($nextDate) : 'ยังไม่กำหนดวัน' }}</div>@else<span class="text-muted">ยังไม่มีงานถัดไป</span>@endif
                        </td>
                        <td>
                            @if($latestFollowup)<span class="idv-badge {{ $latestFollowup->overall_result==='declined'?'st-risk':($latestFollowup->overall_result==='stable'?'st-warn':'st-good') }}">{{ $outcomeLabels[$latestFollowup->overall_result] ?? 'ติดตามแล้ว' }}</span><div class="idv-meta">ติดตามครั้งที่ {{ $latestFollowup->followup_no }} • {{ $thaiDate($latestFollowup->followup_date) }}</div>
                            @elseif($latestAssessment)<span class="idv-badge st-good">ประเมินแล้ว {{ $latestAssessmentAverage!==null?number_format($latestAssessmentAverage,2).'/5':'' }}</span><div class="idv-meta">{{ $thaiDate($latestAssessment->assessment_date) }}</div>
                            @else<span class="text-muted">ยังไม่มีการประเมิน/ติดตาม</span>@endif
                        </td>
                        <td class="text-end"><a class="idv-open" data-permission-action="view" data-permission-keep href="{{ route('individual-development.index',$client->id) }}"><i class="bi bi-box-arrow-in-right"></i>เปิดข้อมูล</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7"><div class="idv-empty"><i class="bi bi-search fs-3 d-block mb-2"></i>ไม่พบข้อมูลตามเงื่อนไข</div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="idv-pagination">{{ $clients->links() }}</div>
    </section>
</div>

{{-- IDP_VALIDATION_COMPLETE_V1_INCLUDE --}}
@include('frontend.client.individual_development._validation')
@endsection
