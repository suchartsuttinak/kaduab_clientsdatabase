@extends('admin.admin_master')

@section('title', 'ศูนย์รับเคสพฤติกรรมที่ส่งต่อ')

@section('admin')
@php
    $stageLabels = [
        'actionable' => 'ต้องดำเนินการ',
        'waiting' => 'รอมอบหมาย',
        'assigned' => 'รอรับเคส',
        'accepted' => 'รอเริ่มช่วยเหลือ',
        'ongoing' => 'กำลังดำเนินการ',
        'overdue' => 'เลยนัดหมาย',
        'closed' => 'บรรลุเป้าหมาย',
        'all' => 'ทั้งหมด',
    ];
    $riskLabels = [
        'none' => 'ไม่พบความเสี่ยง',
        'low' => 'ต่ำ',
        'moderate' => 'ปานกลาง',
        'high' => 'สูง',
    ];
    $roleLabels = \App\Models\User::roleOptions();
    $thaiDate = static function ($value): string {
        if (!$value) return '-';
        try {
            $date = $value instanceof \Carbon\CarbonInterface ? $value : \Carbon\Carbon::parse($value);
            return $date->format('d/m/') . ($date->year + 543);
        } catch (\Throwable $e) {
            return '-';
        }
    };
@endphp

<div class="content referral-center-page">
    <div class="container-fluid py-3 py-lg-4">
        <div class="referral-hero">
            <div>
                <div class="referral-eyebrow"><i class="bi bi-shield-check"></i> ศูนย์ประสานงานระดับองค์กร</div>
                <h1>เคสพฤติกรรมที่ส่งต่อ</h1>
                <p>รวบรวมจากทุกบ้าน เพื่อมอบหมาย รับเคส ติดตามความเสี่ยง และปิดการช่วยเหลืออย่างมีหลักฐาน</p>
            </div>
            <div class="referral-hero-total">
                <span>รอดำเนินการ</span>
                <strong>{{ number_format($summary['actionable']) }}</strong>
                <small>เคส</small>
            </div>
        </div>

        @if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}</div>@endif

        <div class="referral-metrics">
            <a href="{{ route('observe.referrals.index', ['stage' => 'waiting']) }}" class="metric-card {{ $stage === 'waiting' ? 'active' : '' }}">
                <span class="metric-icon bg-blue"><i class="bi bi-inbox"></i></span><span><small>รอมอบหมาย</small><strong>{{ number_format($summary['waiting']) }}</strong></span>
            </a>
            <a href="{{ route('observe.referrals.index', ['stage' => 'assigned']) }}" class="metric-card {{ $stage === 'assigned' ? 'active' : '' }}">
                <span class="metric-icon bg-violet"><i class="bi bi-person-check"></i></span><span><small>รอรับเคส</small><strong>{{ number_format($summary['assigned']) }}</strong></span>
            </a>
            <a href="{{ route('observe.referrals.index', ['stage' => 'ongoing']) }}" class="metric-card {{ $stage === 'ongoing' ? 'active' : '' }}">
                <span class="metric-icon bg-green"><i class="bi bi-arrow-repeat"></i></span><span><small>กำลังดำเนินการ</small><strong>{{ number_format($summary['ongoing']) }}</strong></span>
            </a>
            <a href="{{ route('observe.referrals.index', ['stage' => 'overdue']) }}" class="metric-card danger {{ $stage === 'overdue' ? 'active' : '' }}">
                <span class="metric-icon bg-red"><i class="bi bi-clock-history"></i></span><span><small>เลยนัดหมาย</small><strong>{{ number_format($summary['overdue']) }}</strong></span>
            </a>
            <a href="{{ route('observe.referrals.index', ['stage' => 'actionable', 'risk_level' => 'high']) }}" class="metric-card danger {{ $riskLevel === 'high' ? 'active' : '' }}">
                <span class="metric-icon bg-orange"><i class="bi bi-exclamation-diamond"></i></span><span><small>ความเสี่ยงสูง</small><strong>{{ number_format($summary['high_risk']) }}</strong></span>
            </a>
        </div>

        <div class="card referral-filter-card">
            <div class="card-body">
                <form method="GET" action="{{ route('observe.referrals.index') }}" class="row g-3 align-items-end">
                    <div class="col-12 col-xl-3">
                        <label class="form-label">ค้นหา</label>
                        <div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="ชื่อ เลขทะเบียน พฤติกรรม"></div>
                    </div>
                    <div class="col-6 col-md-3 col-xl-2">
                        <label class="form-label">สถานะงาน</label>
                        <select name="stage" class="form-select">@foreach($stageLabels as $key => $label)<option value="{{ $key }}" @selected($stage === $key)>{{ $label }}</option>@endforeach</select>
                    </div>
                    <div class="col-6 col-md-3 col-xl-2">
                        <label class="form-label">ความเสี่ยง</label>
                        <select name="risk_level" class="form-select"><option value="">ทุกระดับ</option>@foreach($riskLabels as $key => $label)<option value="{{ $key }}" @selected($riskLevel === $key)>{{ $label }}</option>@endforeach</select>
                    </div>
                    <div class="col-6 col-md-3 col-xl-2">
                        <label class="form-label">บ้าน</label>
                        <select name="house_id" class="form-select"><option value="">ทุกบ้าน</option>@foreach($houses as $house)<option value="{{ $house->id }}" @selected((string) request('house_id') === (string) $house->id)>{{ $house->house_name }}</option>@endforeach</select>
                    </div>
                    <div class="col-6 col-md-3 col-xl-2">
                        <label class="form-label">ผู้รับผิดชอบ</label>
                        <select name="assigned_to" class="form-select"><option value="">ทุกคน</option><option value="unassigned" @selected(request('assigned_to') === 'unassigned')>ยังไม่มอบหมาย</option>@foreach($eligibleUsers as $person)<option value="{{ $person->id }}" @selected((string) request('assigned_to') === (string) $person->id)>{{ $person->name }}</option>@endforeach</select>
                    </div>
                    <div class="col-12 col-xl-1 d-flex gap-2">
                        <button class="btn btn-primary flex-fill" type="submit"><i class="bi bi-funnel"></i></button>
                        <a class="btn btn-light border flex-fill" href="{{ route('observe.referrals.index') }}"><i class="bi bi-arrow-counterclockwise"></i></a>
                    </div>
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                </form>
            </div>
        </div>

        <div class="card referral-list-card">
            <div class="card-header referral-list-head">
                <div><h2>รายการเคส</h2><p>แสดงตามสิทธิ์ศูนย์กลางเท่านั้น</p></div>
                <form method="GET" action="{{ route('observe.referrals.index') }}" class="per-page-form">
                    @foreach(request()->except(['per_page', 'page']) as $key => $value)@if(is_scalar($value))<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif @endforeach
                    <label>แสดง</label><select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">@foreach($perPageOptions as $option)<option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>@endforeach</select><span>รายการ/หน้า</span>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table referral-table align-middle mb-0">
                    <thead><tr><th>รับเมื่อ</th><th>ผู้รับบริการ / บ้าน</th><th>ปัญหาพฤติกรรม</th><th>ความเสี่ยง</th><th>สถานะประสานงาน</th><th>นัดหมาย</th><th class="text-end">จัดการ</th></tr></thead>
                    <tbody>
                    @forelse($referrals as $item)
                        @php
                            $latestRound = $item->latestReferralRound;
                            $latestSource = $item->latestFollowup;
                            $risk = $latestRound?->risk_level ?? $latestSource?->risk_level ?? $item->risk_level ?? 'none';
                            $assignment = $item->referralAssignment;
                            $isClosed = ($latestRound?->status ?? null) === 'goal_met';
                            $isOverdue = !$isClosed && ($latestRound?->status ?? null) === 'ongoing' && $latestRound?->next_appointment_date && $latestRound->next_appointment_date->isBefore(now('Asia/Bangkok')->startOfDay());
                            $sourceDate = ($latestSource?->status === 'referred' ? $latestSource->followup_date : null) ?? $item->date;
                            $clientName = $item->client?->full_name ?: trim(($item->client?->first_name ?? '') . ' ' . ($item->client?->last_name ?? ''));
                            if ($isClosed) { $caseStage = 'closed'; }
                            elseif (!$assignment?->assigned_to_user_id) { $caseStage = 'waiting'; }
                            elseif (!$assignment?->accepted_at) { $caseStage = 'assigned'; }
                            elseif (!$latestRound) { $caseStage = 'accepted'; }
                            else { $caseStage = 'ongoing'; }
                        @endphp
                        <tr class="{{ $isOverdue ? 'overdue-row' : '' }}">
                            <td><strong>{{ $thaiDate($sourceDate) }}</strong><small class="d-block text-muted">#{{ $item->id }}</small></td>
                            <td><div class="client-name">{{ $clientName ?: '-' }}</div><div class="client-meta"><i class="bi bi-house-door"></i> {{ $item->client?->house?->house_name ?: 'ไม่ระบุบ้าน' }} @if($item->client?->register_number)<span>• {{ $item->client->register_number }}</span>@endif</div></td>
                            <td><strong>{{ $item->misbehavior?->misbehavior_name ?: '-' }}</strong><div class="text-clamp">{{ $item->behavior ?: '-' }}</div></td>
                            <td><span class="risk-chip risk-{{ $risk }}">{{ $riskLabels[$risk] ?? '-' }}</span></td>
                            <td><span class="stage-chip stage-{{ $caseStage }}">{{ $stageLabels[$caseStage] ?? '-' }}</span><div class="assignee-text">{{ $assignment?->assignee?->name ?: 'ยังไม่มีผู้รับผิดชอบ' }}</div></td>
                            <td>@if($isOverdue)<span class="due-chip overdue"><i class="bi bi-exclamation-circle"></i> เลยนัด {{ $thaiDate($latestRound->next_appointment_date) }}</span>@elseif($latestRound?->next_appointment_date)<span class="due-chip"><i class="bi bi-calendar-event"></i> {{ $thaiDate($latestRound->next_appointment_date) }}</span>@else<span class="text-muted">-</span>@endif</td>
                            <td class="text-end"><a href="{{ route('observe.referrals.show', $item->id) }}" class="btn btn-primary btn-sm text-nowrap"><i class="bi bi-folder2-open me-1"></i>เปิดเคส</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="empty-state"><i class="bi bi-inbox"></i><strong>ไม่พบเคสตามเงื่อนไข</strong><span>ลองเปลี่ยนตัวกรอง หรือเมื่อมีการส่งต่อจากบ้าน เคสจะปรากฏที่นี่อัตโนมัติ</span></div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($referrals->hasPages())<div class="card-footer referral-pagination">{{ $referrals->onEachSide(1)->links('pagination::bootstrap-5') }}</div>@endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.referral-center-page{background:#f4f7fb;min-height:calc(100vh - 70px);color:#172033}.referral-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;padding:28px 30px;margin-bottom:18px;border-radius:22px;color:#fff;background:linear-gradient(135deg,#163b6d,#155eaa 58%,#0d9488);box-shadow:0 16px 36px rgba(23,72,129,.18)}.referral-hero h1{margin:5px 0 7px;color:#fff;font-size:1.75rem;font-weight:800}.referral-hero p{max-width:780px;margin:0;color:rgba(255,255,255,.85);line-height:1.65}.referral-eyebrow{font-size:.8rem;font-weight:700;letter-spacing:.02em}.referral-hero-total{min-width:150px;padding:14px 18px;border:1px solid rgba(255,255,255,.25);border-radius:18px;text-align:center;background:rgba(255,255,255,.13)}.referral-hero-total span,.referral-hero-total small{display:block;color:rgba(255,255,255,.8)}.referral-hero-total strong{display:block;color:#fff;font-size:2.25rem;line-height:1.15}.referral-metrics{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin-bottom:16px}.metric-card{display:flex;align-items:center;gap:12px;padding:15px;border:1px solid #e1e8f2;border-radius:16px;background:#fff;color:#24324a;box-shadow:0 5px 15px rgba(23,42,76,.04);transition:.18s}.metric-card:hover,.metric-card.active{border-color:#4f83cc;color:#153f78;transform:translateY(-1px);box-shadow:0 8px 22px rgba(23,72,129,.1)}.metric-card>span:last-child{min-width:0}.metric-card small{display:block;color:#6b7a90;font-size:.78rem;white-space:nowrap}.metric-card strong{display:block;font-size:1.4rem}.metric-icon{display:inline-flex;width:42px;height:42px;flex:0 0 42px;align-items:center;justify-content:center;border-radius:12px;font-size:1.15rem}.bg-blue{background:#e8f1ff;color:#2563eb}.bg-violet{background:#f1eafe;color:#7c3aed}.bg-green{background:#e8f8ef;color:#15803d}.bg-red{background:#feecec;color:#dc2626}.bg-orange{background:#fff1e6;color:#ea580c}.referral-filter-card,.referral-list-card{border:1px solid #e1e8f2;border-radius:17px;box-shadow:0 7px 22px rgba(23,42,76,.05)}.referral-filter-card{margin-bottom:16px}.referral-filter-card .form-label{font-size:.8rem;font-weight:700;color:#48566b}.referral-list-head{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:17px 20px;background:#fff;border-bottom:1px solid #e8edf4}.referral-list-head h2{margin:0;font-size:1.05rem;font-weight:800}.referral-list-head p{margin:3px 0 0;color:#718096;font-size:.8rem}.per-page-form{display:flex;align-items:center;gap:7px;color:#64748b;font-size:.8rem}.per-page-form .form-select{width:70px;min-height:36px}.referral-table{min-width:1160px}.referral-table thead th{padding:13px 15px;background:#f7f9fc;color:#516077;border-bottom:1px solid #e1e8f2;font-size:.78rem;font-weight:800}.referral-table tbody td{padding:15px;border-color:#edf1f6;vertical-align:middle}.referral-table tbody tr:hover{background:#fbfdff}.referral-table .overdue-row{background:#fffafa}.client-name{font-weight:800;color:#172033}.client-meta,.assignee-text,.text-clamp{margin-top:4px;color:#718096;font-size:.78rem}.text-clamp{max-width:290px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.risk-chip,.stage-chip,.due-chip{display:inline-flex;align-items:center;gap:5px;padding:5px 9px;border-radius:999px;font-size:.75rem;font-weight:800;white-space:nowrap}.risk-none{background:#f1f5f9;color:#64748b}.risk-low{background:#ecfdf3;color:#15803d}.risk-moderate{background:#fff7df;color:#b45309}.risk-high{background:#feecec;color:#c81e1e}.stage-waiting{background:#edf4ff;color:#2563eb}.stage-assigned{background:#f4edff;color:#7c3aed}.stage-accepted{background:#e9f8f5;color:#0f766e}.stage-ongoing{background:#eaf7ee;color:#15803d}.stage-closed{background:#eef2f6;color:#475569}.due-chip{background:#f5f7fa;color:#526174}.due-chip.overdue{background:#feecec;color:#c81e1e}.empty-state{display:flex;min-height:260px;align-items:center;justify-content:center;flex-direction:column;gap:8px;color:#77849a}.empty-state i{font-size:2.3rem;color:#a8b4c5}.empty-state strong{color:#3f4d63}.referral-pagination{display:flex;justify-content:center;padding:15px;background:#fff}.referral-pagination nav,.referral-pagination .pagination{margin:0}
@media(max-width:1199.98px){.referral-metrics{grid-template-columns:repeat(3,minmax(0,1fr))}}@media(max-width:767.98px){.referral-hero{align-items:flex-start;padding:22px;flex-direction:column}.referral-hero h1{font-size:1.4rem}.referral-hero-total{width:100%}.referral-metrics{grid-template-columns:1fr 1fr}.referral-list-head{align-items:flex-start;flex-direction:column}}@media(max-width:420px){.referral-metrics{grid-template-columns:1fr}}
</style>
@endpush
