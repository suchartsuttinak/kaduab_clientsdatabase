@php
    $clientName = $client->fullname ?? $client->full_name ?? $client->name ?? '-';
    $registerNumber = $client->register_number ?? '-';
    $houseName = $client->house?->house_name ?? '-';
    $thaiDate = static function ($value): string {
        if (!$value) return '-';
        try { $d = $value instanceof \Carbon\CarbonInterface ? $value : \Carbon\Carbon::parse($value); return $d->format('d/m/') . ($d->year + 543); } catch (\Throwable $e) { return '-'; }
    };
    $domainIcon = ['physical'=>'♥','emotional'=>'☺','social'=>'●●','intellectual'=>'▣'];
    $priorityLabels = ['low'=>'ต่ำ','medium'=>'ปานกลาง','high'=>'สูง','urgent'=>'เร่งด่วน'];
    $strengthLabels = ['ability'=>'ความสามารถ/ความถนัด','interest'=>'ความสนใจ','hobby'=>'งานอดิเรก','talent'=>'ความสามารถพิเศษ','does_well'=>'สิ่งที่เด็กทำได้ดี','dream'=>'ความฝัน','career_interest'=>'อาชีพที่สนใจ','trusted_person'=>'บุคคลที่เด็กไว้วางใจ','motivation'=>'แรงจูงใจ','skills_to_promote'=>'ทักษะที่ควรส่งเสริม'];
    $needLabels = ['education'=>'การศึกษา','health'=>'สุขภาพ','mental'=>'จิตใจและอารมณ์','behavior'=>'พฤติกรรม','family'=>'ครอบครัว','social'=>'สังคม','life_skill'=>'ทักษะชีวิต','career'=>'อาชีพ','housing'=>'ที่อยู่อาศัย','rights'=>'เอกสารและสิทธิ','other'=>'อื่น ๆ'];
    $activityStatusLabels = ['planned'=>'วางแผน','in_progress'=>'กำลังดำเนินการ','completed'=>'ดำเนินการแล้ว','cancelled'=>'ยกเลิก'];
    $scoredDomains = $domainScores->filter(fn($d) => $d['score'] !== null);
    $summaryText = 'ยังไม่มีข้อมูลผลประเมินพัฒนาการสำหรับสรุปผล';
    if ($scoredDomains->isNotEmpty()) {
        $levelText = $scoredDomains->map(fn($d) => $d['name'].' ระดับ '.$d['level'].' ('.$d['level_label'].')')->implode(', ');
        $needDomains = $scoredDomains->filter(fn($d) => ($d['level'] ?? 0) <= 2)->pluck('name')->implode(', ');
        $sourceText = $latestFollowup ? 'จากการติดตามล่าสุด ครั้งที่ '.$latestFollowup->followup_no : 'จากการประเมินระดับเริ่มต้น (Baseline)';
        $summaryText = $sourceText.' พบว่า '.$levelText.'.';
        if ($needDomains !== '') {
            $summaryText .= ' ควรกำหนดหรือทบทวนเป้าหมายและกิจกรรมส่งเสริมเป็นพิเศษในด้าน '.$needDomains.' พร้อมติดตามผลอย่างต่อเนื่องและใช้หลักฐานจากการสังเกตตามสภาพจริงประกอบการทบทวนแผน';
        } else {
            $summaryText .= ' ควรรักษาความต่อเนื่องของกิจกรรมพัฒนาและติดตามแนวโน้มรายด้านตามเป้าหมายที่กำหนด';
        }
    }
    $hasFinalPage = in_array($plan->status, ['completed','cancelled'], true) || filled($plan->final_outcome) || filled($plan->close_reason);
    $hasProfilePage = !empty($plan->strength_profile) || !empty($plan->needs_profile);
    $profilePageCount = $hasProfilePage ? 1 : 0;
    // Follow-up 1 ครั้ง = 2 หน้า (สรุปผล + ตัวชี้วัด) และมีหน้าจุดแข็ง/ความต้องการเมื่อมีข้อมูลแบบโครงสร้าง
    $totalPages = 1 + $profilePageCount + $goals->count() + ($followups->count() * 2) + ($hasFinalPage ? 1 : 0);
    $followupStartPage = 1 + $profilePageCount + $goals->count();
@endphp

<div class="rp-page rp-summary-page">
    <div class="rp-title">รายงานพัฒนาและติดตามรายบุคคล</div>
    <div class="rp-subtitle">ประเมินพัฒนาการ วางแผน เป้าหมาย กิจกรรม และติดตามผลของผู้รับบริการอย่างต่อเนื่อง</div>
    <div class="rp-divider"><span></span></div>

    <div class="rp-info-box">
        <div class="rp-info-grid">
            <div class="rp-info"><div class="rp-info-label">ผู้รับบริการ</div><div class="rp-info-value">{{ $clientName }}</div></div>
            <div class="rp-info"><div class="rp-info-label">อายุ</div><div class="rp-info-value">{{ $ageText }}</div></div>
            <div class="rp-info"><div class="rp-info-label">บ้าน</div><div class="rp-info-value">{{ $houseName }}</div></div>
            <div class="rp-info"><div class="rp-info-label">เลขทะเบียน</div><div class="rp-info-value">{{ $registerNumber }}</div></div>
            <div class="rp-info"><div class="rp-info-label">แผนปัจจุบัน</div><div class="rp-info-value">ครั้งที่ {{ $plan->plan_no }}</div></div>
            <div class="rp-info"><div class="rp-info-label">วันที่เริ่มแผน</div><div class="rp-info-value">{{ $thaiDate($plan->start_date) }}</div></div>
            <div class="rp-info"><div class="rp-info-label">วันที่คาดว่าจะสิ้นสุด</div><div class="rp-info-value">{{ $thaiDate($plan->end_date) }}</div></div>
            <div class="rp-info"><div class="rp-info-label">สถานะ</div><div class="rp-status-pill">{{ $statusLabels[$plan->status] ?? $plan->status }}</div></div>
        </div>
    </div>

    <div class="rp-section">
        <div class="rp-section-title">สรุปแผนพัฒนา</div>
        <div class="rp-summary-grid">
            <div class="rp-summary-item"><div class="rp-summary-label">เป้าหมายภาพรวม</div><div class="rp-summary-value">{{ $plan->overall_goal ?: '-' }}</div></div>
            <div class="rp-summary-item"><div class="rp-summary-label">ประเด็นที่ควรพัฒนา</div><div class="rp-summary-value">{{ $plan->development_need_summary ?: '-' }}</div></div>
            <div class="rp-summary-item"><div class="rp-summary-label">จุดแข็ง</div><div class="rp-summary-value">{{ $plan->strength_summary ?: '-' }}</div></div>
            <div class="rp-summary-item"><div class="rp-summary-label">ปัจจัยคุ้มครอง</div><div class="rp-summary-value">{{ $plan->protective_factor_summary ?: '-' }}</div></div>
            <div class="rp-summary-item"><div class="rp-summary-label">ความต้องการของผู้รับบริการ</div><div class="rp-summary-value">{{ $plan->client_need_summary ?: '-' }}</div></div>
            <div class="rp-summary-item"><div class="rp-summary-label">เครือข่ายสนับสนุน</div><div class="rp-summary-value">{{ $plan->support_network_summary ?: '-' }}</div></div>
        </div>
    </div>

    <div class="rp-section">
        <div class="rp-section-title">สถานะการดำเนินงาน</div>
        <div class="rp-stat-grid">
            <div class="rp-stat"><div class="rp-stat-label">Baseline</div><div class="rp-stat-value rp-stat-text">{{ $baseline ? 'ประเมินแล้ว' : 'รอประเมิน' }}</div></div>
            <div class="rp-stat"><div class="rp-stat-label">เป้าหมายทั้งหมด</div><div class="rp-stat-value">{{ $goalStats['total'] }}</div></div>
            <div class="rp-stat"><div class="rp-stat-label">บรรลุแล้ว</div><div class="rp-stat-value">{{ $goalStats['achieved'] }}</div></div>
            <div class="rp-stat"><div class="rp-stat-label">กำลังดำเนินการ</div><div class="rp-stat-value">{{ $goalStats['in_progress'] }}</div></div>
            <div class="rp-stat"><div class="rp-stat-label">วันที่ประเมิน Baseline</div><div class="rp-stat-value rp-stat-text">{{ $baseline ? $thaiDate($baseline->assessment_date) : '-' }}</div></div>
            <div class="rp-stat"><div class="rp-stat-label">ติดตามล่าสุด</div><div class="rp-stat-value rp-stat-text">{{ $latestFollowup ? 'ครั้งที่ '.$latestFollowup->followup_no.' • '.$thaiDate($latestFollowup->followup_date) : '-' }}</div></div>
        </div>
    </div>

    <div class="rp-section">
        <div class="rp-section-title">ระดับพัฒนาการ 4 ด้าน</div>
        <div class="rp-section-note">{{ $latestFollowup ? 'อ้างอิงการติดตามล่าสุด ครั้งที่ '.$latestFollowup->followup_no.' วันที่ '.$thaiDate($latestFollowup->followup_date) : ($latestAssessment ? 'อ้างอิง Baseline วันที่ '.$thaiDate($latestAssessment->assessment_date) : '— ยังไม่มีผลประเมิน') }}</div>
        <div class="rp-domain-grid">
            @foreach($domainScores as $domain)
                <div class="rp-domain-card">
                    <div class="rp-domain-icon">{{ $domainIcon[$domain['code']] ?? '•' }}</div>
                    <div class="rp-domain-name">{{ $domain['name'] }}</div>
                    <div class="rp-domain-score" style="font-size:16px">{{ $domain['baseline_score'] !== null ? number_format($domain['baseline_score'],2) : '-' }} → {{ $domain['score'] !== null ? number_format($domain['score'],2) : '-' }}</div>
                    <div class="rp-progress"><div style="width:{{ $domain['percent'] }}%"></div></div>
                    <div class="rp-domain-level">Baseline → ล่าสุด @if($domain['delta'] !== null) • {{ $domain['delta'] > 0 ? '↑ +'.number_format($domain['delta'],2) : ($domain['delta'] < 0 ? '↓ '.number_format($domain['delta'],2) : '→ 0.00') }} @endif<br>{{ $domain['level'] ? 'ระดับ '.$domain['level'].' • '.$domain['level_label'] : $domain['level_label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="rp-conclusion">
        <div class="rp-conclusion-title">สรุปผลเบื้องต้น</div>
        <div class="rp-conclusion-text">{{ $summaryText }}</div>
    </div>

    <div class="rp-sign">
        <div>ผู้จัดทำรายงาน ............................................................................................</div>
        <div>วันที่ ...........................................................................................................</div>
    </div>
    <div class="rp-page-no">หน้า 1 / {{ $totalPages }}</div>
</div>

@if($hasProfilePage)
<div class="rp-page rp-summary-page">
    <div class="rp-title rp-title-small">จุดแข็ง ศักยภาพ และความต้องการ</div>
    <div class="rp-subtitle">แผนครั้งที่ {{ $plan->plan_no }} • {{ $clientName }} • ใช้เป็นฐานกำหนดเป้าหมายและการช่วยเหลือรายบุคคล</div>
    <div class="rp-divider"><span></span></div>

    <div class="rp-section">
        <div class="rp-section-title">จุดแข็งและศักยภาพ</div>
        <div class="rp-summary-grid">
            @forelse($strengthLabels as $key=>$label)
                @if(filled(data_get($plan->strength_profile,$key)))<div class="rp-summary-item"><div class="rp-summary-label">{{ $label }}</div><div class="rp-summary-value">{{ data_get($plan->strength_profile,$key) }}</div></div>@endif
            @empty
            @endforelse
            @if(filled($plan->strength_summary))<div class="rp-summary-item"><div class="rp-summary-label">สรุปจุดแข็งเพิ่มเติม</div><div class="rp-summary-value">{{ $plan->strength_summary }}</div></div>@endif
        </div>
    </div>

    <div class="rp-section">
        <div class="rp-section-title">ความต้องการและประเด็นที่ต้องช่วยเหลือ</div>
        <div class="rp-summary-grid">
            @foreach($needLabels as $key=>$label)
                @php($need=data_get($plan->needs_profile,$key,[]))
                @if(is_array($need) && collect($need)->filter(fn($v)=>filled($v))->isNotEmpty())
                    <div class="rp-summary-item">
                        <div class="rp-summary-label">{{ $label }}@if(filled(data_get($need,'priority'))) • ความสำคัญ {{ $priorityLabels[data_get($need,'priority')] ?? data_get($need,'priority') }}@endif</div>
                        <div class="rp-summary-value">{{ data_get($need,'detail') ?: '-' }}@if(filled(data_get($need,'client_view')))<br><strong>ความเห็นเด็ก:</strong> {{ data_get($need,'client_view') }}@endif @if(filled(data_get($need,'staff_view')))<br><strong>ความเห็นเจ้าหน้าที่:</strong> {{ data_get($need,'staff_view') }}@endif</div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <div class="rp-section">
        <div class="rp-section-title">บริบทสนับสนุนการวางแผน</div>
        <div class="rp-summary-grid">
            <div class="rp-summary-item"><div class="rp-summary-label">ความต้องการของผู้รับบริการ</div><div class="rp-summary-value">{{ $plan->client_need_summary ?: '-' }}</div></div>
            <div class="rp-summary-item"><div class="rp-summary-label">ความต้องการของผู้ดูแล/ครอบครัว</div><div class="rp-summary-value">{{ $plan->caregiver_need_summary ?: '-' }}</div></div>
            <div class="rp-summary-item"><div class="rp-summary-label">ปัจจัยเสี่ยง</div><div class="rp-summary-value">{{ $plan->risk_factor_summary ?: '-' }}</div></div>
            <div class="rp-summary-item"><div class="rp-summary-label">ปัจจัยคุ้มครอง</div><div class="rp-summary-value">{{ $plan->protective_factor_summary ?: '-' }}</div></div>
            <div class="rp-summary-item"><div class="rp-summary-label">เครือข่ายสนับสนุน</div><div class="rp-summary-value">{{ $plan->support_network_summary ?: '-' }}</div></div>
        </div>
    </div>
    <div class="rp-page-no">หน้า 2 / {{ $totalPages }}</div>
</div>
@endif

@foreach($goals as $goal)
    @php
        $gp = $goalProgress[$goal->id] ?? [];
        $goalPageNo = 1 + $profilePageCount + $loop->iteration;
    @endphp
    <div class="rp-page rp-goal-page">
        <div class="rp-title rp-title-small">เป้าหมายและแผนกิจกรรมรายบุคคล</div>
        <div class="rp-subtitle">เป้าหมายที่ {{ $loop->iteration }} / {{ $goals->count() }} • แผนครั้งที่ {{ $plan->plan_no }} • {{ $clientName }}</div>
        <div class="rp-divider"><span></span></div>

        <div class="rp-goal-block">
            <div class="rp-goal-head">
                <div><div class="rp-goal-domain">{{ $goal->domain?->name ?? '-' }} @if($goal->indicator) • {{ $goal->indicator->name }} @endif</div><div class="rp-goal-title">{{ $goal->title }}</div></div>
                <div class="rp-goal-status">{{ $goalStatusLabels[$goal->status] ?? $goal->status }}</div>
            </div>
            <table class="rp-goal-meta"><tr>
                <td><strong>Baseline → ล่าสุด → Target</strong><br>{{ $gp['baseline'] ?? $goal->baseline_level ?? '-' }} → {{ $gp['current'] ?? '-' }} → {{ $gp['target'] ?? $goal->target_level ?? '-' }}</td>
                <td><strong>ความก้าวหน้า</strong><br>{{ $gp['progress_percent'] ?? 0 }}% @if($gp['reached'] ?? false) • ถึง Target @endif</td>
                <td><strong>กำหนดสำเร็จ</strong><br>{{ $thaiDate($goal->target_date) }}</td>
                <td><strong>ผู้รับผิดชอบ</strong><br>{{ $goal->responsible_name ?: '-' }}</td>
            </tr></table>
            @if($goal->description)<div class="rp-goal-detail"><strong>ผลลัพธ์ที่คาดหวัง:</strong> {{ $goal->description }}</div>@endif
            <div class="rp-goal-detail"><strong>ตัวชี้วัดความสำเร็จ:</strong> {{ $goal->success_indicator ?: '-' }}</div>
            @if($goal->measurement_method || $goal->target_value !== null)<div class="rp-goal-detail"><strong>วิธีวัดผล:</strong> {{ $goal->measurement_method ?: '-' }} @if($goal->target_value !== null) • ค่าเป้าหมาย {{ rtrim(rtrim(number_format((float)$goal->target_value,2,'.',','),'0'),'.') }} {{ $goal->target_unit }} @endif</div>@endif
            @if($goal->status_note)<div class="rp-goal-detail"><strong>บันทึกสถานะ:</strong> {{ $goal->status_note }}</div>@endif
            @if($goal->cancel_reason)<div class="rp-goal-detail"><strong>เหตุผลยกเลิก:</strong> {{ $goal->cancel_reason }}</div>@endif
            <table class="rp-activity-table">
                <thead><tr><th style="width:13%">วันที่</th><th style="width:17%">กิจกรรม</th><th>รายละเอียด/ผล</th><th style="width:18%">ผู้รับผิดชอบ/สถานะ</th></tr></thead>
                <tbody>@forelse($goal->activities as $activity)<tr>
                    <td>{{ $thaiDate($activity->activity_date) }}</td>
                    <td>{{ $activity->activity_type ?: 'กิจกรรม' }}</td>
                    <td>{{ $activity->detail }} @if($activity->result)<br><strong>ผล:</strong> {{ $activity->result }}@endif @if($activity->problem)<br><strong>อุปสรรค:</strong> {{ $activity->problem }}@endif @if($activity->next_action)<br><strong>ทำต่อ:</strong> {{ $activity->next_action }}@endif @if($activity->cancel_reason)<br><strong>เหตุผลยกเลิก:</strong> {{ $activity->cancel_reason }}@endif</td>
                    <td>{{ $activity->responsible_name ?: '-' }}<br><span class="rp-muted">{{ $activityStatusLabels[$activity->status] ?? $activity->status }}</span></td>
                </tr>@empty<tr><td colspan="4" class="rp-empty-cell">ยังไม่มีกิจกรรมตามแผนสำหรับเป้าหมายนี้</td></tr>@endforelse</tbody>
            </table>
        </div>
        <div class="rp-sign"><div>ผู้รับผิดชอบ ............................................................................................</div><div>วันที่ ...........................................................................................................</div></div>
        <div class="rp-page-no">หน้า {{ $goalPageNo }} / {{ $totalPages }}</div>
    </div>
@endforeach

@foreach($followups as $fu)
    @php
        $fDomains = $followupSummaries->get($fu->id, collect());
        $overviewPageNo = $followupStartPage + (($loop->iteration - 1) * 2) + 1;
        $indicatorPageNo = $overviewPageNo + 1;
        $trendSymbol = ['up'=>'↑','down'=>'↓','same'=>'→','none'=>'-'];
    @endphp

    <div class="rp-page rp-followup-page rp-followup-overview-page">
        <div class="rp-title rp-title-small">รายงานผลการติดตามรายบุคคล ครั้งที่ {{ $fu->followup_no }}</div>
        <div class="rp-subtitle">สรุปผลการติดตาม • แผนครั้งที่ {{ $plan->plan_no }} • {{ $clientName }} • วันที่ {{ $thaiDate($fu->followup_date) }}</div>
        <div class="rp-divider"><span></span></div>

        <div class="rp-section">
            <div class="rp-section-title">ข้อมูลการติดตาม</div>
            <table class="rp-follow-meta">
                <tr>
                    <td><strong>วันที่ติดตาม</strong><br>{{ $thaiDate($fu->followup_date) }}</td>
                    <td><strong>ผู้ติดตาม</strong><br>{{ $fu->follower_name ?: '-' }}</td>
                    <td><strong>ผลโดยรวม</strong><br>{{ $followupResultLabels[$fu->overall_result] ?? ($fu->overall_result ?: '-') }}</td>
                    <td><strong>นัดครั้งถัดไป</strong><br>{{ $thaiDate($fu->next_followup_date) }}</td>
                </tr>
            </table>
        </div>

        <div class="rp-section">
            <div class="rp-section-title">เปรียบเทียบพัฒนาการ 4 ด้าน</div>
            <div class="rp-follow-domain-grid">
                @foreach($fDomains as $d)
                    <div class="rp-follow-domain">
                        <div class="rp-follow-domain-name">{{ $d['name'] }}</div>
                        <div class="rp-follow-score">{{ $d['previous'] !== null ? number_format($d['previous'],2) : '-' }} → {{ $d['current'] !== null ? number_format($d['current'],2) : '-' }}</div>
                        <div class="rp-follow-trend">{{ $trendSymbol[$d['trend']] ?? '-' }} @if($d['delta'] !== null){{ $d['delta'] > 0 ? '+' : '' }}{{ number_format($d['delta'],2) }}@endif • {{ $d['level'] ? 'ระดับ '.$d['level'].' '.$d['label'] : $d['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rp-section">
            <div class="rp-section-title">สรุปการติดตาม</div>
            <div class="rp-follow-summary-grid">
                <div class="rp-follow-box"><strong>สถานการณ์ปัจจุบัน</strong><br>{{ $fu->current_situation ?: '-' }}</div>
                <div class="rp-follow-box"><strong>การเปลี่ยนแปลงที่พบ</strong><br>{{ $fu->changes ?: '-' }}</div>
                <div class="rp-follow-box"><strong>สิ่งที่ดีขึ้น</strong><br>{{ $fu->positive_changes ?: '-' }}</div>
                <div class="rp-follow-box"><strong>การช่วยเหลือ/กิจกรรมที่ดำเนินการ</strong><br>{{ $fu->actions_taken ?: '-' }}</div>
                <div class="rp-follow-box"><strong>ผลที่เกิดขึ้น</strong><br>{{ $fu->result ?: '-' }}</div>
                <div class="rp-follow-box"><strong>ปัญหา/อุปสรรค</strong><br>{{ $fu->problem ?: '-' }}</div>
                <div class="rp-follow-box"><strong>ความคิดเห็นของผู้รับบริการ</strong><br>{{ $fu->client_feedback ?: '-' }}</div>
                <div class="rp-follow-box"><strong>ความคิดเห็นของผู้ดูแล/ครอบครัว</strong><br>{{ $fu->caregiver_feedback ?: '-' }}</div>
            </div>
        </div>

        <div class="rp-conclusion rp-next-box">
            <div class="rp-conclusion-title">สิ่งที่ต้องดำเนินการต่อ</div>
            <div class="rp-conclusion-text">{{ $fu->next_action ?: '-' }}</div>
            @if($fu->suggestion)
                <div class="rp-conclusion-text"><strong>ข้อเสนอแนะ:</strong> {{ $fu->suggestion }}</div>
            @endif
        </div>

        <div class="rp-page-no">หน้า {{ $overviewPageNo }} / {{ $totalPages }}</div>
    </div>

    <div class="rp-page rp-followup-page rp-followup-indicator-page">
        <div class="rp-title rp-title-small">รายละเอียดตัวชี้วัดการติดตาม ครั้งที่ {{ $fu->followup_no }}</div>
        <div class="rp-subtitle">แผนครั้งที่ {{ $plan->plan_no }} • {{ $clientName }} • วันที่ {{ $thaiDate($fu->followup_date) }}</div>
        <div class="rp-divider"><span></span></div>

        <div class="rp-indicator-intro">
            <div class="rp-indicator-intro-box">
                <strong>ผลโดยรวม</strong><br>
                {{ $followupResultLabels[$fu->overall_result] ?? ($fu->overall_result ?: '-') }}
            </div>
            <div class="rp-indicator-intro-box">
                <strong>แนวทางต่อ</strong><br>
                {{ $fu->next_action ?: '-' }}
            </div>
        </div>

        <div class="rp-section">
            <div class="rp-section-title">สรุปตัวชี้วัดก่อน–หลัง</div>
            <table class="rp-follow-table">
                <thead>
                    <tr>
                        <th>ด้าน</th>
                        <th>ตัวชี้วัด</th>
                        <th>ก่อน</th>
                        <th>ครั้งนี้</th>
                        <th>ผล</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fu->items->sortBy(fn($item) => optional($item->indicator)->sort_order ?? 999) as $item)
                        @php
                            $delta = ($item->score !== null && $item->previous_score !== null)
                                ? ((int)$item->score - (int)$item->previous_score)
                                : null;
                        @endphp
                        <tr>
                            <td>{{ $item->indicator?->domain?->name ?? '-' }}</td>
                            <td>{{ $item->indicator?->name ?? '-' }}</td>
                            <td class="rp-center">{{ $item->previous_score ?? '-' }}</td>
                            <td class="rp-center"><strong>{{ $item->score ?? '-' }}</strong></td>
                            <td class="rp-center">{{ $delta === null ? '-' : ($delta > 0 ? '↑ +'.$delta : ($delta < 0 ? '↓ '.$delta : '→ 0')) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="rp-sign">
            <div>ผู้ติดตาม/ผู้จัดทำ ....................................................................................</div>
            <div>วันที่ ...........................................................................................................</div>
        </div>

        <div class="rp-page-no">หน้า {{ $indicatorPageNo }} / {{ $totalPages }}</div>
    </div>
@endforeach


@if($hasFinalPage)
<div class="rp-page rp-final-page">
    <div class="rp-title rp-title-small">สรุปผลและการสิ้นสุดแผนพัฒนารายบุคคล</div>
    <div class="rp-subtitle">แผนครั้งที่ {{ $plan->plan_no }} • {{ $clientName }}</div>
    <div class="rp-divider"><span></span></div>
    <div class="rp-section"><div class="rp-section-title">สถานะการสิ้นสุดแผน</div><div class="rp-summary-grid">
        <div class="rp-summary-item"><div class="rp-summary-label">สถานะ</div><div class="rp-summary-value">{{ $statusLabels[$plan->status] ?? $plan->status }}</div></div>
        <div class="rp-summary-item"><div class="rp-summary-label">วันที่สิ้นสุดจริง</div><div class="rp-summary-value">{{ $thaiDate($plan->closed_at) }}</div></div>
        <div class="rp-summary-item"><div class="rp-summary-label">ผู้ทบทวน</div><div class="rp-summary-value">{{ $plan->reviewer?->name ?? '-' }}</div></div>
        <div class="rp-summary-item"><div class="rp-summary-label">ผู้ปิด/ยุติแผน</div><div class="rp-summary-value">{{ $plan->closer?->name ?? '-' }}</div></div>
    </div></div>
    <div class="rp-section"><div class="rp-section-title">เหตุผล/เกณฑ์การสิ้นสุด</div><div class="rp-summary-item"><div class="rp-summary-value">{{ $plan->close_reason ?: '-' }}</div></div></div>
    <div class="rp-section"><div class="rp-section-title">ผลลัพธ์สุดท้าย</div><div class="rp-summary-item"><div class="rp-summary-value">{{ $plan->final_outcome ?: '-' }}</div></div></div>
    <div class="rp-section"><div class="rp-section-title">ข้อเสนอแนะและแนวทางหลังสิ้นสุดแผน</div><div class="rp-summary-item"><div class="rp-summary-value">{{ $plan->final_recommendation ?: '-' }}</div></div></div>
    <div class="rp-conclusion"><div class="rp-conclusion-title">สรุปการบรรลุเป้าหมาย</div><div class="rp-conclusion-text">เป้าหมายทั้งหมด {{ $goalStats['total'] }} รายการ • บรรลุแล้ว {{ $goalStats['achieved'] }} รายการ • ยกเลิก {{ $goalStats['cancelled'] ?? 0 }} รายการ • ยังเปิดอยู่ {{ $goalStats['in_progress'] }}</div></div>
    <div class="rp-sign"><div>ผู้สรุป/ผู้ปิดแผน ...................................................................................</div><div>วันที่ ...........................................................................................................</div></div>
    <div class="rp-page-no">หน้า {{ $totalPages }} / {{ $totalPages }}</div>
</div>
@endif
